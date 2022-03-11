<?php

namespace Modules\Petro\Http\Controllers;

use App\BusinessLocation;
use App\Product;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use Modules\Petro\Entities\FuelTank;
use Modules\Petro\Entities\Pump;
use Yajra\DataTables\Facades\DataTables;
use App\Utils\ModuleUtil;
use App\Utils\ProductUtil;
use App\Utils\TransactionUtil;
use App\Utils\Util;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Modules\Petro\Entities\MeterSale;
use Modules\Petro\Entities\PumpOperator;
use Modules\Petro\Entities\Settlement;
use Modules\Superadmin\Entities\HelpExplanation;
use Modules\Superadmin\Entities\ModulePermissionLocation;

class PumpController extends Controller
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
    public function __construct(ProductUtil $productUtil, ModuleUtil $moduleUtil, TransactionUtil $transactionUtil, Util $commonUtil)
    {
        $this->productUtil = $productUtil;
        $this->moduleUtil = $moduleUtil;
        $this->transactionUtil = $transactionUtil;
        $this->commonUtil = $commonUtil;

        //barcode types
        $this->barcode_types = $this->productUtil->barcode_types();
    }

    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        $business_id = request()->session()->get('user.business_id');

        if (!$this->moduleUtil->hasThePermissionInSubscription($business_id, 'enable_petro_module')) {
            abort(403, 'Unauthorized Access');
        }

        $business_locations = BusinessLocation::forDropdown($business_id);
        $pumps = Pump::where('business_id', $business_id)->pluck('pump_name', 'id');
        $tanks = FuelTank::where('business_id', $business_id)->pluck('fuel_tank_number', 'id');
        $pump_operators = PumpOperator::where('business_id', $business_id)->pluck('name', 'id');
        $settlement_nos = Settlement::where('business_id', $business_id)->pluck('settlement_no', 'id');
        $products = Product::leftjoin('categories', 'products.category_id', 'categories.id')
            ->where('categories.name', 'Fuel')
            ->where('products.business_id', $business_id)
            ->pluck('products.name', 'products.id');

        if (request()->ajax()) {
            $business_id = request()->session()->get('user.business_id');
            if (request()->ajax()) {
                $query = Pump::leftjoin('products', 'pumps.product_id', 'products.id')
                    ->leftjoin('business_locations', 'pumps.location_id', 'business_locations.id')
                    ->leftjoin('fuel_tanks', 'pumps.fuel_tank_id', 'fuel_tanks.id')
                    ->where('pumps.business_id', $business_id)
                    ->select([
                        'pumps.*',
                        'fuel_tanks.fuel_tank_number',
                        'products.name as product_name',
                        'business_locations.name as location_name',
                    ]);

                $fuel_tanks = Datatables::of($query)
                    ->addColumn(
                        'action',
                        '<button data-href="{{action(\'\Modules\Petro\Http\Controllers\PumpController@edit\', [$id])}}" data-container=".fuel_tank_modal" class="btn btn-primary btn-xs btn-modal edit_reference_button"><i class="fa fa-pencil-square-o"></i> @lang("messages.edit")</button>
                        <a href="{{action(\'\Modules\Petro\Http\Controllers\PumpController@destroy\', [$id])}}" class="delete_pump_button btn btn-danger btn-xs"><i class="fa fa-trash"></i> @lang("messages.delete")</a>'
                    )
                    ->editColumn('created_at', function ($row) {
                        return  date('Y-m-d H:i:s', strtotime($row->created_at));;
                    })
                    ->removeColumn('id')
                    ->editColumn('transaction_date', '{{@format_date($transaction_date)}}')
                    ->editColumn('installation_date', '{{@format_date($installation_date)}}');

                return $fuel_tanks->rawColumns(['action'])
                    ->make(true);
            }
        }

        $meter_resetting_permission         = $this->moduleUtil->hasThePermissionInSubscription($business_id, 'meter_resetting');
        $enable_petro_management_testing 	= $this->moduleUtil->hasThePermissionInSubscription($business_id, 'enable_petro_management_testing'); 
        $enable_petro_meter_reading 		= $this->moduleUtil->hasThePermissionInSubscription($business_id, 'enable_petro_meter_reading');
        $enable_petro_pump_dashboard 		= $this->moduleUtil->hasThePermissionInSubscription($business_id, 'enable_petro_pump_dashboard');
        $enable_petro_pump_management 		= $this->moduleUtil->hasThePermissionInSubscription($business_id, 'enable_petro_pump_management');
        
        $message = $this->transactionUtil->getGeneralMessage('general_message_pump_management_checkbox');

        return view('petro::pumps.index')->with(compact(
            'business_locations',
            'products',
            'message',
            'pumps',
            'tanks',
            'pump_operators',
            'meter_resetting_permission',
            'enable_petro_pump_management',
            'enable_petro_management_testing',
            'enable_petro_meter_reading',
            'enable_petro_pump_dashboard',
            'settlement_nos'
        ));
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create()
    {
        $business_id = request()->session()->get('business.id');
        $locations = BusinessLocation::forDropdown($business_id);
        $products = Product::leftjoin('categories', 'products.category_id', 'categories.id')
            ->where('products.business_id', $business_id)
            ->where('categories.name', 'Fuel')
            ->pluck('products.name', 'products.id');
        $tanks = FuelTank::where('business_id', $business_id)->pluck('fuel_tank_number', 'id');
        $help_explanations = HelpExplanation::pluck('value', 'help_key');

        return view('petro::pumps.create')->with(compact('locations', 'products', 'tanks', 'help_explanations'));
    }

    /**
     * Store a newly created resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'pump_no' => 'required',
            'pump_name' => 'required',
            'location_id' => 'required',
            'product_id' => 'required',
            'installation_date' => 'required',
            'transaction_date' => 'required',
            'bulk_sale_meter' => 'required',
            'meter_value' => 'required',
            'fuel_tank_id' => 'required',
        ]);

        if ($validator->fails()) {
            $output = [
                'success' => 0,
                'msg' => $validator->errors()->all()[0]
            ];

            return redirect()->back()->with('status', $output);
        }

        $business_id = $request->session()->get('business.id');
        if (!$this->commonUtil->getPumpsQuotaByLocation($business_id, $request->location_id)) {
            $output = [
                'success' => 0,
                'msg' => __('petro::lang.number_of_pumps_limit_reach_in_location')
            ];
            return redirect()->back()->with('status', $output);
        }

        try {

            $data = array(
                'business_id' => $business_id,
                'pump_no' => $request->pump_no,
                'pump_name' => $request->pump_name,
                'location_id' => $request->location_id,
                'product_id' => $request->product_id,
                'installation_date' => date('Y-m-d', strtotime($request->installation_date)),
                'transaction_date' => date('Y-m-d', strtotime($request->transaction_date)),
                'bulk_sale_meter' => $request->bulk_sale_meter,
                'starting_meter' => $request->meter_value,
                'last_meter_reading' => $request->meter_value,
                'temp_meter_reading' => $request->meter_value,
                'fuel_tank_id' => $request->fuel_tank_id,
            );

            Pump::create($data);
            $output = [
                'success' => 1,
                'msg' => __('petro::lang.pump_add_success')
            ];
        } catch (\Exception $e) {
            \Log::emergency('File: ' . $e->getFile() . 'Line: ' . $e->getLine() . 'Message: ' . $e->getMessage());
            $output = [
                'success' => 0,
                'msg' => __('messages.something_went_wrong')
            ];
        }

        return redirect()->back()->with('status', $output);
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
    public function edit($id)
    {
        $business_id = request()->session()->get('business.id');
        $locations = BusinessLocation::forDropdown($business_id);
        $products = Product::leftjoin('categories', 'products.category_id', 'categories.id')
            ->where('products.business_id', $business_id)
            ->where('categories.name', 'Fuel')
            ->pluck('products.name', 'products.id');
        $tanks = FuelTank::where('business_id', $business_id)->pluck('fuel_tank_number', 'id');
        $pump = Pump::findOrFail($id);

        return view('petro::pumps.edit')->with(compact('locations', 'products', 'tanks', 'pump'));
    }

    /**
     * Update the specified resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function update($id, Request $request)
    {
        try {
            $business_id = $request->session()->get('business.id');

            $data = array(
                'business_id' => $business_id,
                'pump_no' => $request->pump_no,
                'pump_name' => $request->pump_name,
                'location_id' => $request->location_id,
                'product_id' => $request->product_id,
                'installation_date' => date('Y-m-d', strtotime($request->installation_date)),
                'transaction_date' => date('Y-m-d', strtotime($request->transaction_date)),
                'bulk_sale_meter' => $request->bulk_sale_meter,
                'fuel_tank_id' => $request->fuel_tank_id,
            );

            Pump::where('id', $id)->update($data);
            $output = [
                'success' => 1,
                'msg' => __('petro::lang.pump_update_success')
            ];
        } catch (\Exception $e) {
            \Log::emergency('File: ' . $e->getFile() . 'Line: ' . $e->getLine() . 'Message: ' . $e->getMessage());
            $output = [
                'success' => 0,
                'msg' => __('messages.something_went_wrong')
            ];
        }

        return redirect()->back()->with('status', $output);
    }

    /**
     * Remove the specified resource from storage.
     * @return Response
     */
    public function destroy($id)
    {
        try {
            Pump::where('id', $id)->delete();
            $output = [
                'success' => true,
                'msg' => __('petro::lang.pump_delete_success')
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
     * Import pumps
     * @return view
     */
    public function importPumps()
    {
        $business_id = request()->session()->get('business.id');
        $locations = BusinessLocation::forDropdown($business_id);
        $fuel_tanks = FuelTank::where('business_id', $business_id)->pluck('fuel_tank_number', 'id');

        return view('petro::pumps.import_pumps')->with(compact('locations', 'fuel_tanks'));
    }

    /**
     * Import pumps
     * @return Response
     */
    public function saveImport(Request $request)
    {
        $notAllowed = $this->productUtil->notAllowedInDemo();
        if (!empty($notAllowed)) {
            return $notAllowed;
        }
        $business_id = request()->session()->get('business.id');
        $user_id = $request->session()->get('user.id');
        $fuel_tank_id =  $request->fuel_tank_id;
        $location_id =   $request->location_id;
        $transaction_date =   $request->transaction_date;
        $product_id = FuelTank::where('id', $fuel_tank_id)->first()->product_id;
        try {
            //Set maximum php execution time
            ini_set('max_execution_time', 0);
            ini_set('memory_limit', -1);

            if ($request->hasFile('pumps_csv')) {
                $file = $request->file('pumps_csv');

                $parsed_array = Excel::toArray([], $file);

                //Remove header row
                $imported_data = array_splice($parsed_array[0], 1);

                $formated_data = [];

                $is_valid = true;
                $error_msg = '';

                $total_rows = count($imported_data);

                //Check if subscribed or not, then check for products quota
                // if (!$this->moduleUtil->isSubscribed($business_id)) {
                //     return $this->moduleUtil->expiredResponse();
                // } elseif (!$this->moduleUtil->isQuotaAvailable('products', $business_id, $total_rows)) {
                //     return $this->moduleUtil->quotaExpiredResponse('products', $business_id, action('ImportProductsController@index'));
                // }

                $row_no = 0;
                DB::beginTransaction();
                foreach ($imported_data as $key => $value) {

                    $product_array = [];
                    $product_array['business_id'] = $business_id;
                    $product_array['location_id'] = $location_id;
                    $product_array['fuel_tank_id'] = $fuel_tank_id;
                    $product_array['product_id'] =  $product_id;
                    $product_array['transaction_date'] = Carbon::parse($transaction_date)->format('Y-m-d');

                    //Check if any column is missing
                    //Check if any column is missing
                    if (count($value) < 4) {
                        $is_valid =  false;
                        $error_msg = "Some of the columns are missing. Please, use latest CSV file template.";
                        break;
                    }

                    $pump_no = strtolower(trim($value[0]));
                    if ($pump_no) {
                        $product_array['pump_no'] = $pump_no;
                    } else {
                        $is_valid = false;
                        $error_msg = "Invalid value for pump number in row no. $row_no";
                        break;
                    }

                    $pump_name = strtolower(trim($value[1]));
                    if ($pump_name) {
                        $product_array['pump_name'] = $pump_name;
                    } else {
                        $is_valid = false;
                        $error_msg = "Invalid value for pump name in row no. $row_no";
                        break;
                    }

                    $installation_date = strtolower(trim($value[2]));
                    if ($installation_date) {
                        $product_array['installation_date'] = Carbon::parse($installation_date)->format('Y-m-d');
                    } else {
                        $is_valid = false;
                        $error_msg = "Invalid value for installation in row no. $row_no";
                        break;
                    }


                    $meter_value = $this->productUtil->num_uf(strtolower(trim($value[3])));
                    if ($meter_value) {
                        $product_array['starting_meter'] = $meter_value;
                        $product_array['last_meter_reading'] = $meter_value;
                        $product_array['temp_meter_reading'] = $meter_value;
                    } else {
                        $is_valid = false;
                        $error_msg = "Invalid value for meter reading in row no. $row_no";
                        break;
                    }


                    if (!$is_valid) {
                        throw new \Exception($error_msg);
                    }

                    Pump::create($product_array);

                    $row_no++;
                }
                DB::commit();
            }

            $output = [
                'success' => 1,
                'msg' => __('petro::lang.pump_import_success')
            ];
        } catch (\Exception $e) {
            \Log::emergency('File: ' . $e->getFile() . 'Line: ' . $e->getLine() . 'Message: ' . $e->getMessage());
            $output = [
                'success' => 0,
                'msg' => $e->getMessage()
            ];

            return redirect()->back()->with('notification', $output);
        }

        return redirect('/petro/pumps')->with('status', $output);
    }


    public function getTestingDetails()
    {
        $business_id = request()->session()->get('user.business_id');
        if (request()->ajax()) {
            $query = MeterSale::leftjoin('pumps', 'meter_sales.pump_id', 'pumps.id')
                ->leftjoin('products', 'pumps.product_id', 'products.id')
                ->leftjoin('settlements', 'meter_sales.settlement_no', 'settlements.id')
                ->leftjoin('business_locations', 'settlements.location_id', 'business_locations.id')
                ->leftjoin('pump_operators', 'settlements.pump_operator_id', 'pump_operators.id')
                ->where('settlements.business_id', $business_id)
                ->where('meter_sales.testing_qty', '>', 0)
                ->select([
                    'meter_sales.*',
                    'business_locations.name as location_name',
                    'settlements.settlement_no as settlement_no',
                    'pumps.pump_no',
                    'products.name as product_name',
                    'pump_operators.name as pump_operator_name',
                    'settlements.transaction_date',
                    'settlements.id as settlement_id',
                ]);
            if (!empty(request()->location_id)) {
                $query->where('settlements.location_id', request()->location_id);
            }
            if (!empty(request()->pump_operator)) {
                $query->where('settlements.pump_operator_id', request()->pump_operator);
            }
            if (!empty(request()->settlement_no)) {
                $query->where('settlements.id', request()->settlement_no);
            }
            if (!empty(request()->pump)) {
                $query->where('meter_sales.pump_id', request()->pump);
            }

            if (!empty(request()->product_id)) {
                $query->where('pumps.product_id', request()->product_id);
            }
            if (!empty(request()->start_date) && !empty(request()->end_date)) {
                $query->whereDate('settlements.transaction_date', '>=', request()->start_date);
                $query->whereDate('settlements.transaction_date', '<=', request()->end_date);
            }

            $testing_details = Datatables::of($query)
                ->addColumn(
                    'action',
                    '<a data-href="{{action(\'\Modules\Petro\Http\Controllers\SettlementController@show\', [$settlement_id])}}" class="btn-modal btn btn-primary btn-xs" data-container=".pump_modal"><i class="fa fa-eye" aria-hidden="true"></i> @lang("messages.view")</a>'
                )
                ->editColumn('created_at', function ($row) {
                    return  date('Y-m-d H:i:s', strtotime($row->created_at));;
                })
                ->editColumn('testing_qty', '<span class="display_currency testing_qty" data-currency_symbol="true" data-orig-value="{{$testing_qty}}">{{$testing_qty}}</span>')
                ->addColumn('testing_sale_value', '<span class="display_currency testing_sale_value" data-currency_symbol="true" data-orig-value="{{$price*$testing_qty}}">{{$price*$testing_qty}}</span>')
                ->removeColumn('id');


            return $testing_details->rawColumns(['action', 'testing_sale_value', 'testing_qty'])
                ->make(true);
        }
    }
    public function getMeterReadings()
    {
        $business_id = request()->session()->get('user.business_id');
        if (request()->ajax()) {
            $query = MeterSale::leftjoin('pumps', 'meter_sales.pump_id', 'pumps.id')
                ->leftjoin('products', 'pumps.product_id', 'products.id')
                ->leftjoin('settlements', 'meter_sales.settlement_no', 'settlements.id')
                ->leftjoin('business_locations', 'settlements.location_id', 'business_locations.id')
                ->leftjoin('pump_operators', 'settlements.pump_operator_id', 'pump_operators.id')
                ->where('settlements.business_id', $business_id)
                ->select([
                    'meter_sales.*',
                    'business_locations.name as location_name',
                    'settlements.settlement_no as settlement_no',
                    'pumps.pump_no',
                    'products.name as product_name',
                    'pump_operators.name as pump_operator_name',
                    'settlements.transaction_date',
                    'settlements.id as settlement_id',
                ]);

            if (!empty(request()->location_id)) {
                $query->where('settlements.location_id', request()->location_id);
            }
            if (!empty(request()->pump_operator)) {
                $query->where('settlements.pump_operator_id', request()->pump_operator);
            }
            if (!empty(request()->settlement_no)) {
                $query->where('settlements.id', request()->settlement_no);
            }
            if (!empty(request()->pump)) {
                $query->where('meter_sales.pump_id', request()->pump);
            }

            if (!empty(request()->product_id)) {
                $query->where('pumps.product_id', request()->product_id);
            }
            if (!empty(request()->start_date) && !empty(request()->end_date)) {
                $query->whereBetween('settlements.transaction_date', [date(request()->start_date), date(request()->end_date)]);
            }

            $testing_details = Datatables::of($query)
                ->addColumn(
                    'action',
                    '<a data-href="{{action(\'\Modules\Petro\Http\Controllers\SettlementController@show\', [$settlement_id])}}" class="btn-modal btn btn-primary btn-xs" data-container=".pump_modal"><i class="fa fa-eye" aria-hidden="true"></i> @lang("messages.view")</a>'
                )
                ->editColumn('sub_total', '<span class="display_currency sub_total" data-currency_symbol="false" data-orig-value="{{$sub_total}}">{{$sub_total}}</span>')
                ->editColumn('qty', '<span class="display_currency qty" data-currency_symbol="false" data-orig-value="{{$qty}}">{{$qty}}</span>')
                ->editColumn('testing_qty', '<span class="display_currency testing_qty" data-currency_symbol="false" data-orig-value="{{$testing_qty}}">{{$testing_qty}}</span>')
                ->removeColumn('id');


            return $testing_details->rawColumns(['action', 'qty', 'testing_qty', 'sub_total'])
                ->make(true);
        }
    }
}
