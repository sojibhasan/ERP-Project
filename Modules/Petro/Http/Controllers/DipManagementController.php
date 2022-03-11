<?php

namespace Modules\Petro\Http\Controllers;

use App\AccountTransaction;
use App\Business;
use App\BusinessLocation;
use App\Product;
use App\System;
use App\Transaction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Response;
use Illuminate\Routing\Controller;
use DB;
use Modules\Petro\Entities\DipReading;
use Modules\Petro\Entities\DipResetting;
use Modules\Petro\Entities\FuelTank;
use Yajra\DataTables\Facades\DataTables;
use App\Utils\Util;
use App\Utils\ProductUtil;
use App\Utils\ModuleUtil;
use App\Utils\TransactionUtil;
use App\Utils\BusinessUtil;
use Illuminate\Support\Facades\Log;
use Modules\Superadmin\Entities\TankDipChart;
use Modules\Superadmin\Entities\TankDipChartDetail;
use Modules\Petro\Entities\TankPurchaseLine;
use Modules\Petro\Entities\TankSellLine;
use Session;

class DipManagementController extends Controller
{
    /**
     * All Utils instance.
     *
     */
    protected $productUtil;
    protected $moduleUtil;
    protected $transactionUtil;
    protected $commonUtil;
    private $barcode_types;
    /**
     * Constructor
     *
     * @param ProductUtils $product
     * @return void
     */
    
    public function __construct(Util $commonUtil, ProductUtil $productUtil, ModuleUtil $moduleUtil, TransactionUtil $transactionUtil, BusinessUtil $businessUtil)
    {
        $this->commonUtil = $commonUtil;
        $this->productUtil = $productUtil;
        $this->moduleUtil = $moduleUtil;
        $this->transactionUtil = $transactionUtil;
        $this->businessUtil = $businessUtil;
    }
    /**
     * Display a listing of the resource.
     * @return Response
     */
    
    public function index(Request $request)
    {
        $business_id = $request->session()->get('user.business_id');
        $business_locations = BusinessLocation::forDropdown($business_id);
        $tanks = FuelTank::where('business_id', $business_id)->pluck('fuel_tank_number', 'id');
        $products = Product::leftjoin('categories', 'products.category_id', 'categories.id')
            ->where('categories.name', 'Fuel')
            ->where('products.business_id', $business_id)
            ->pluck('products.name', 'products.id');
        $message = $this->transactionUtil->getGeneralMessage('general_message_pump_management_checkbox');
        return view('petro::dip_management.index')->with(compact(
            'business_locations',
            'message',
            'tanks',
            'products'
        ));
    }
    /**
     * Get Dip Report
     * @return Response
     */
    
    public function getDipReport()
    {
        $business_id = request()->session()->get('user.business_id');
        if (request()->ajax()) {
            $query = DB::table('dip_readings')->
                leftjoin('business_locations', 'dip_readings.location_id', 'business_locations.id')
                ->leftjoin('fuel_tanks', 'dip_readings.tank_id', 'fuel_tanks.id')
                ->leftjoin('products', 'fuel_tanks.product_id', 'products.id')
                ->where('dip_readings.business_id', $business_id)
                ->select([
                    'dip_readings.*',
                    'business_locations.name as location_name',
                    'fuel_tanks.fuel_tank_number as tank_name',
                    'products.name as product_name', 'products.id as productID'
                ])->get();
            if (!empty(request()->location_id)) {
                $query->where('dip_readings.location_id', request()->location_id);
            }
            if (!empty(request()->tank_id)) {
                $query = $query->where('tank_id', request()->tank_id);
            }
            if (!empty(request()->product_id)) {
                $query = $query->where('productID', request()->product_id);
            }
            if (!empty(request()->start_date) && !empty(request()->end_date)) {
                $query = $query->whereBetween('date_and_time', [date("m/d/Y", strtotime(request()->start_date)) , date("m/d/Y", strtotime(request()->end_date))]);
            }
            $business_details = Business::find($business_id);
            $dip_report = Datatables::of($query)
                ->addColumn('difference', function ($row) use ($business_details) {
                    return $this->productUtil->num_f($row->fuel_balance_dip_reading - $row->current_qty, false, $business_details, true);
                })
                ->editColumn('dip_reading', function ($row) use ($business_details) {
                    return $this->productUtil->num_f($row->dip_reading, false, $business_details, false);
                })
                ->editColumn('fuel_balance_dip_reading', function ($row) use ($business_details) {
                    return $this->productUtil->num_f($row->fuel_balance_dip_reading, false, $business_details, true);
                })
                ->editColumn('difference_value', function ($row) use ($business_details) {
                    $variations = DB::table('variations')->where('product_id', $row->productID)->first();
                    return $this->productUtil->num_f(($row->fuel_balance_dip_reading - $row->current_qty) * $variations->sell_price_inc_tax, false, $business_details, true);
                })
                ->removeColumn('id');
            return $dip_report->rawColumns(['difference'])
                ->make(true);
        }
    }
    /**
     * Add new Dip
     * @return Response
     */
    
    public function addNewDip()
    {
        $business_id = request()->session()->get('user.business_id');
        $business_locations = BusinessLocation::forDropdown($business_id);
        $default_location = current(array_keys($business_locations->toArray()));
        $tanks = FuelTank::where('business_id', $business_id)->pluck('fuel_tank_number', 'id');
        $count = DipReading::where('business_id', $business_id)->count();
        $ref_no = $count + 1;
        $tank_dip_chart_permission = $this->moduleUtil->hasThePermissionInSubscription($business_id, 'tank_dip_chart');
        return view('petro::dip_management.add_new_dip')->with(compact(
            'business_locations',
            'default_location',
            'tanks',
            'tank_dip_chart_permission',
            'ref_no'
        ));
    }
    /**
     * Save new Dip
     * @return Response
     */
    
    public function saveNewDip(Request $request)
    {
        $business_id = request()->session()->get('user.business_id');
        try {
            $data = array(
                'business_id' => $business_id,
                'location_id' => $request->location_id,
                'ref_number' => $request->ref_number,
                'tank_id' => $request->tank_id,
                'date_and_time' => $request->date_and_time,
                'transaction_date' => $request->date_and_time,
                'dip_reading' => $request->dip_reading,
                'fuel_balance_dip_reading' => $request->fuel_balance_dip_reading,
                'current_qty' => $request->current_qty,
                'tank_manufacturer' => $request->tank_manufacturer,
                'tank_capacity' => $request->tank_capacity,
                'note' => $request->note
            );
            $tank_dip_chart_permission = $this->moduleUtil->hasThePermissionInSubscription($business_id, 'tank_dip_chart');
            if ($tank_dip_chart_permission) {
                $data['dip_reading'] =  TankDipChartDetail::findOrFail($request->dip_reading)->dip_reading;
            }
            DipReading::create($data);
            $output = [
                'success' => true,
                'msg' => __('petro::lang.success')
            ];
        } catch (\Exception $e) {
            Log::emergency('File: ' . $e->getFile() . 'Line: ' . $e->getLine() . 'Message: ' . $e->getMessage());
            $output = [
                'success' => false,
                'msg' => __('messages.something_went_wrong')
            ];
        }
        return $output;
    }
    /**
     * Get Dip Resettings
     * @return Response
     */
    
    public function getDipResetting()
    {
        $business_id = request()->session()->get('user.business_id');
        if (request()->ajax()) {
            $query = DB::table('dip_resettings')
                ->leftjoin('business_locations', 'dip_resettings.location_id', 'business_locations.id')
                ->leftjoin('fuel_tanks', 'dip_resettings.tank_id', 'fuel_tanks.id')
                ->leftjoin('products', 'fuel_tanks.product_id', 'products.id')
                ->select([
                    'dip_resettings.*',
                    'business_locations.name as location_name',
                    'fuel_tanks.fuel_tank_number as tank_name',
                    'products.name as product_name'
                ])
                ->where('dip_resettings.business_id', $business_id)->get();
            if (!empty(request()->location_id)) {
                $query->where('dip_resettings.location_id', request()->location_id);
            }
            if (!empty(request()->tank_id)) {
                $query->where('dip_resettings.tank_id', request()->tank_id);
            }
            if (!empty(request()->product_id)) {
                $query->where('fuel_tanks.product_id', request()->product_id);
            }
            if (!empty(request()->start_date) && !empty(request()->end_date)) {
                $query->whereBetween('dip_resettings.date_and_time', [date(request()->start_date), date(request()->end_date)]);
            }
            $dip_report = Datatables::of($query)->removeColumn('id');
            return $dip_report->rawColumns(['difference'])
                ->make(true);
        }
    }
    /**
     * Add new Dip
     * @return Response
     */
    
    public function addResettingDip()
    {
        $business_id = request()->session()->get('user.business_id');
        $business_locations = BusinessLocation::where('business_id', $business_id)->pluck('name', 'id');
        $count = DipResetting::where('business_id', $business_id)->count();
        $meter_reset_form_no = $count + 1;
        $meter_reset_form_no = "DRIP-" . $meter_reset_form_no;
        $tanks = FuelTank::where('business_id', $business_id)->pluck('fuel_tank_number', 'id');
        $business = Business::where('id', $business_id)->first();
        $quantity_presicion = $business->quantity_precision;
        return view('petro::dip_management.add_resetting_dip')->with(compact(
            'business_locations',
            'tanks',
            'meter_reset_form_no',
            'quantity_presicion'
        ));
    }
    /**
     * Save new Dip
     * @return Response
     */
    
    public function saveResettingDip(Request $request)
    {
        $business_id = request()->session()->get('user.business_id');
        // try {
        $data = array(
            'business_id' => $business_id,
            'location_id' => $request->location_id,
            'meter_reset_form_no' => $request->meter_reset_form_no,
            'tank_id' => $request->tank_id,
            'date_and_time' => $request->date_and_time,
            'current_qty' => $request->current_qty,
            'current_dip_difference' => $request->current_dip_difference,
            'reset_new_dip' => $request->reset_new_dip,
            'reason' => $request->reason
        );
        DB::beginTransaction();
        $dip_resetting = DipResetting::create($data);
        $for_current_diff = DB::table('dip_readings')->where('tank_id', $request->tank_id)->latest()->first();
         $dip_report = DB::table('dip_readings')
              ->where('id', $for_current_diff->id)
              ->update(['reset_new_dip' =>  $request->reset_new_dip]);
        $user_id = $request->session()->get('user.id');
        $input_data['type'] = 'stock_adjustment';
        $input_data['business_id'] = $business_id;
        $input_data['created_by'] = $user_id;
        $input_data['additional_notes'] = $request->reason;
        $input_data['adjustment_type'] = 'normal';
        $input_data['location_id'] = $request->location_id;
        $input_data['transaction_date'] = date('d-m-Y', strtotime($request->date_and_time));
        $input_data['total_amount_recovered'] = 0.00;
        //     //Update reference count
        $ref_count = $this->productUtil->setAndGetReferenceCount('stock_adjustment');
        //Generate reference number
        if (empty($input_data['ref_no'])) {
            $input_data['ref_no'] = $this->productUtil->generateReferenceNumber('stock_adjustment', $ref_count);
        }
        $product = FuelTank::leftjoin('products', 'fuel_tanks.product_id', 'products.id')
            ->leftjoin('variations', 'products.id', 'variations.product_id')
            ->where('fuel_tanks.id', $request->tank_id)
            ->select('products.*', 'variations.id as variation_id', 'variations.default_purchase_price')->first();
        $quantity = abs($request->reset_new_dip - $request->current_qty);
        $adjustment_line = [
            'product_id' => $product->id,
            'variation_id' => $product->variation_id,
            'quantity' => $this->productUtil->num_uf($quantity),
            'unit_price' => $this->productUtil->num_uf($product->default_purchase_price),
            'type' => $request->adjustment_type,
            'tank_id' => $request->tank_id,
            'inventory_adjustment_account' =>  $request->inventory_adjustment_account
        ];
        $product_data[] = $adjustment_line;
        //Decrease available quantity
        $this->productUtil->decreaseProductQuantity(
            $product->id,
            $product->variation_id,
            $request->location_id,
            $this->productUtil->num_uf($quantity),
            0,
            $request->adjustment_type
        );
        $input_data['final_total'] = $quantity * $product->default_purchase_price;
        /**
         * @ModifiedBy Afes Oktavianus
         * @DateBy 07-06-2021
         * @Task 3341
         */
        // $input_data['invoice_no']  = $request->meter_reset_form_no . $request->adjustment_type;
        // @modifyedBy Iqbal Hossain
        // @date 24 july 2021
        $input_data['invoice_no']  = $request->meter_reset_form_no;
        $stock_adjustment = Transaction::create($input_data);
        /**
         * @ModifiedBy Afes Oktavianus
         * @DateBy 07-06-2021
         * @Task 3341
         */
        if ($request->adjustment_type == "increase") {
            $input_tank_purchase['transaction_id'] = $stock_adjustment->id;
            $input_tank_purchase['business_id'] = $business_id;
            $input_tank_purchase['tank_id'] = $request->tank_id;
            $input_tank_purchase['product_id'] = $product->id;
            $input_tank_purchase['quantity'] = $quantity;
            $tank_purchase_lines = TankPurchaseLine::create($input_tank_purchase);
        } else {
            $input_tank_purchase['transaction_id'] = $stock_adjustment->id;
            $input_tank_purchase['business_id'] = $business_id;
            $input_tank_purchase['tank_id'] = $request->tank_id;
            $input_tank_purchase['product_id'] = $product->id;
            $input_tank_purchase['quantity'] = $quantity;
            $tank_purchase_lines = TankSellLine::create($input_tank_purchase);
        }
        $dip_resetting->adjustment_transaction_id = $stock_adjustment->id;
        $dip_resetting->save();
        $stock_adjustment->stock_adjustment_lines()->createMany($product_data);
        // //Map Stock adjustment & Purchase.
        $business = [
            'id' => $business_id,
            'accounting_method' => $request->session()->get('business.accounting_method'),
            'location_id' => $request->location_id
        ];
        $this->transactionUtil->mapPurchaseSell($business, $stock_adjustment->stock_adjustment_lines, 'stock_adjustment');
        if ($request->adjustment_type  == 'increase') {
            $acc_tran_type = 'debit';
        }
        if ($request->adjustment_type  ==  'decrease') {
            $acc_tran_type = 'credit';
        }
        $this_product = Product::where('id', $product->id)->first();
        if (!empty($this_product->stock_type)) {
            $account_transaction_data = [
                'amount' => $stock_adjustment->final_total,
                'account_id' => $this_product->stock_type,
                'type' => $acc_tran_type,
                'operation_date' => $stock_adjustment->transaction_date,
                'created_by' => $stock_adjustment->created_by,
                'transaction_id' => $stock_adjustment->id,
                'transaction_payment_id' => null,
                'note' => null
            ];
            AccountTransaction::createAccountTransaction($account_transaction_data);
        }
        if (!empty($request->inventory_adjustment_account)) {
            if ($product['addjustment_type']  == 'increase') {
                $acc_tran_type = 'credit';
            }
            if ($product['addjustment_type']  ==  'decrease') {
                $acc_tran_type = 'debit';
            }
            $account_transaction_data = [
                'amount' => $stock_adjustment->final_total,
                'account_id' => $request->inventory_adjustment_account,
                'type' => $acc_tran_type,
                'operation_date' => $stock_adjustment->transaction_date,
                'created_by' => $stock_adjustment->created_by,
                'transaction_id' => $stock_adjustment->id,
                'transaction_payment_id' => null,
                'note' => null
            ];
            AccountTransaction::createAccountTransaction($account_transaction_data);
        }
        DB::commit();
        $output = [
            'success' => 1,
            'msg' => __('petro::lang.success')
        ];
        return $output;
    }
    /**
     * Store a newly created resource in storage.
     * @param  Request $request
     * @return Response
     */
    
    public function store(Request $request)
    {
    }
    /**
     * Show the specified resource.
     * @return Response
     */
    
    public function show()
    {
        return view('petro::show');
    }
    /**
     * Show the form for editing the specified resource.
     * @return Response
     */
    
    public function edit()
    {
        return view('petro::edit');
    }
    /**
     * Update the specified resource in storage.
     * @param  Request $request
     * @return Response
     */
    
    public function update(Request $request)
    {
    }
    /**
     * Remove the specified resource from storage.
     * @return Response
     */
    
    public function destroy()
    {
    }
    /**
     * Get tank product details
     * @return Response
     */
    
    public function getTankProduct($tank_id)
    {
        $product = FuelTank::leftjoin('products', 'fuel_tanks.product_id', 'products.id')
            ->join('variations as v', 'v.product_id', '=', 'products.id')
            ->leftJoin('variation_location_details as vld', 'vld.variation_id', '=', 'v.id')
            ->where('fuel_tanks.id', $tank_id)
            ->select('products.*', DB::raw('SUM(vld.qty_available) as current_stock'))->first();
        return ['product' => $product];
    }
    
    public function getTankBalanceById($tank_id)
    {
        $for_current_diff = DB::table('dip_readings')->where('tank_id', $tank_id)->latest()->first();
        $dip_reseting_new_reset_qty = DB::table('dip_resettings')->where('tank_id', $tank_id)->latest()->first();
        if(!empty($dip_reseting_new_reset_qty->current_dip_difference)){
            $reset_new_dip = $dip_reseting_new_reset_qty->current_dip_difference;
        }else {
            $reset_new_dip = "0.00";
        }
        if(!empty($for_current_diff->fuel_balance_dip_reading)) {
            $add_new_diff_cqty = $for_current_diff->fuel_balance_dip_reading;
        }else{
            $add_new_diff_cqty = "0.00";
        }
        if(!empty($for_current_diff->fuel_balance_dip_reading)) {
        $current_diff_for_reseting = $for_current_diff->fuel_balance_dip_reading - $for_current_diff->current_qty;
        }else {
            $current_diff_for_reseting = "0.00";
        }
        $current_balance = $this->transactionUtil->getTankBalanceById($tank_id);
         if(!empty($for_current_diff->current_qty)) {
            $current_diff = $for_current_diff->current_qty;
        }else {
            $current_diff =  "0.00";
        }
        $tank = FuelTank::find($tank_id);
        $product = Product::where('id', $tank->product_id)->first();
        $details['tank_manufacturer'] = $tank->tank_manufacturer;
        $details['tank_capacity'] = number_format($tank->tank_capacity, 3, '.', '');
        $tank_dip_chart = TankDipChart::leftjoin('tank_dip_chart_details', 'tank_dip_charts.id', 'tank_dip_chart_details.tank_dip_chart_id')->where('tank_manufacturer', $details['tank_manufacturer'])->where('tank_capacity', $details['tank_capacity'])->pluck('tank_dip_chart_details.id', 'dip_reading');
        return ['current_stock' => number_format($current_balance, 3, '.', ''), 'details' => $details,'current_diff_for_reseting' => $current_diff_for_reseting, 'add_new_diff_cqty' => $add_new_diff_cqty,'reset_new_dip' =>$reset_new_dip , 'dip_readings' =>  $tank_dip_chart, 'product' => $product, 'current_diff' => number_format($current_diff, 3, '.', '')];
    }
}
