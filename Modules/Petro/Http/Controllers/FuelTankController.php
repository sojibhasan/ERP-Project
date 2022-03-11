<?php



namespace Modules\Petro\Http\Controllers;



use App\AccountTransaction;

use App\Business;

use App\BusinessLocation;

use Illuminate\Http\Request;

use Illuminate\Routing\Controller;

use Modules\Petro\Entities\FuelTank;

use App\Product;

use App\ProductVariation;

use App\PurchaseLine;

use App\System;

use App\Transaction;

use App\Utils\ModuleUtil;

use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Auth;

use Yajra\DataTables\Facades\DataTables;

use App\Utils\ProductUtil;

use App\Utils\TransactionUtil;

use App\Variation;

use Carbon\Carbon;

use Illuminate\Support\Facades\Log;

use Modules\Petro\Entities\Settlement;

use Modules\Petro\Entities\TankPurchaseLine;

use Modules\Petro\Entities\TankSellLine;

use Modules\Superadmin\Entities\HelpExplanation;

use Modules\Superadmin\Entities\TankDipChart;



class FuelTankController extends Controller

{

    /**

     * All Utils instance.

     *

     */

    protected $productUtil;

    protected $transactionUtil;

    protected $moduleUtil;



    /**

     * Constructor

     *

     * @param ProductUtils $product

     * @return void

     */

    public function __construct(ProductUtil $productUtil, TransactionUtil $transactionUtil, ModuleUtil $moduleUtil)

    {

        $this->productUtil = $productUtil;

        $this->transactionUtil = $transactionUtil;

        $this->moduleUtil = $moduleUtil;

    }





    /**

     * Display a listing of the resource.

     *

     * @return \Illuminate\Http\Response

     */

    public function index()

    {

        $business_id = request()->session()->get('user.business_id');



        if (!$this->moduleUtil->hasThePermissionInSubscription($business_id, 'enable_petro_module')) {

            abort(403, 'Unauthorized Access');

        }



        if (request()->ajax()) {

            $business_id = request()->session()->get('user.business_id');

            if (request()->ajax()) {

                $query = FuelTank::leftjoin('products', 'fuel_tanks.product_id', 'products.id')

                    ->leftjoin('variations', 'products.id', '=', 'variations.product_id')

                    ->leftjoin('variation_location_details as vld', 'variations.id', '=', 'vld.variation_id')

                    ->leftjoin('business_locations', 'fuel_tanks.location_id', 'business_locations.id')

                    ->where('fuel_tanks.business_id', $business_id)

                    ->select([

                        'fuel_tanks.*',

                        "vld.qty_available as stock",

                        'products.name as product_name',

                        'business_locations.name as location_name'

                    ])->groupBy('fuel_tanks.id');

                    // dd($query);
                    

                $fuel_tanks = Datatables::of($query)

                    ->addColumn(

                        'action',

                        '@can("fuel_tank.edit")<button data-href="{{action(\'\Modules\Petro\Http\Controllers\FuelTankController@edit\', [$id])}}" data-container=".fuel_tank_modal" class="btn btn-primary btn-xs btn-modal edit_reference_button"><i class="fa fa-pencil-square-o"></i> @lang("messages.edit")</button>@endcan

                        <a href="{{action(\'\Modules\Petro\Http\Controllers\FuelTankController@destroy\', [$id])}}" class="delete_tank_button btn btn-danger btn-xs"><i class="fa fa-trash"></i> @lang("messages.delete")</a>'

                    )

                    ->editColumn('bulk_tank', '@if($bulk_tank == 1) Yes @else No @endif')

                    ->addColumn('new_balance', function ($row) use ($business_id) {
                        
                        $current_balance = $this->transactionUtil->getTankBalanceById($row->id);


                        $business_details = Business::find($business_id);

                        return $this->productUtil->num_f($current_balance, false, $business_details, true);
                        // return $row;

                    })

                    ->editColumn('transaction_date', '{{@format_date($transaction_date)}}')

                    ->removeColumn('id');





                return $fuel_tanks->rawColumns(['action', 'new_balance'])

                    ->make(true);

            }

        }



        $business_locations = BusinessLocation::where('business_id', $business_id)->pluck('name', 'id');

        $tank_numbers = FuelTank::where('business_id', $business_id)->pluck('fuel_tank_number', 'fuel_tank_number');

        $products = Product::leftjoin('categories', 'products.category_id', 'categories.id')->where('products.business_id', $business_id)->where('categories.name', 'Fuel')->pluck('products.name', 'products.id');

        $settlements = Settlement::where('business_id', $business_id)->pluck('settlement_no', 'settlement_no');

        $purhcase_nos = Transaction::where('business_id', $business_id)->where('type', 'purchase')->pluck('ref_no', 'ref_no');



        $message = $this->transactionUtil->getGeneralMessage('general_message_tank_management_checkbox');



        return view('petro::fuel_tanks.index')->with(compact(

            'business_locations',

            'message',

            'tank_numbers',

            'products',

            'settlements',

            'purhcase_nos'

        ));

    }



    /**

     * Show the form for creating a new resource.

     *

     * @return \Illuminate\Http\Response

     */

    public function create()

    {

        $business_id = request()->session()->get('business.id');

        $locations = BusinessLocation::forDropdown($business_id);

        $products = Product::leftjoin('categories', 'products.category_id', 'categories.id')

            ->where('products.business_id', $business_id)

            ->where('categories.name', 'Fuel')

            ->pluck('products.name', 'products.id');

        $help_explanations = HelpExplanation::pluck('value', 'help_key');

        $sheet_names = TankDipChart::pluck('sheet_name', 'id');

        $tank_dip_chart_permission = $this->moduleUtil->hasThePermissionInSubscription($business_id, 'tank_dip_chart');



        return view('petro::fuel_tanks.create')->with(compact('locations', 'products', 'help_explanations', 'tank_dip_chart_permission', 'sheet_names'));

    }



    /**

     * Store a newly created resource in storage.

     *

     * @param  \Illuminate\Http\Request  $request

     * @return \Illuminate\Http\Response

     */

    public function store(Request $request)

    {



        try {

            $business_id = request()->session()->get('business.id');

            $transaction_date = $request->transaction_date;

            $product_id = $request->product_id;

            $variation = Variation::where('product_id', $request->product_id)->first();

            $k = $variation->id;

            $product = Product::where('business_id', $business_id)

                ->where('id', $product_id)

                ->with(['variations', 'product_tax'])

                ->first();

            $tax_percent = !empty($product->product_tax->amount) ? $product->product_tax->amount : 0;

            $tax_id = !empty($product->product_tax->id) ? $product->product_tax->id : null;

            $purchase_price = $this->productUtil->num_uf(trim($variation->default_purchase_price));

            $item_tax = $this->productUtil->calc_percentage($purchase_price, $tax_percent);

            $purchase_price_inc_tax = $purchase_price + $item_tax;

            $qty_remaining = $this->productUtil->num_uf(trim($request->current_balance));

            $purchase_price_inc_tax = $purchase_price + $item_tax;

            //Calculate transaction total

            $purchase_total = ($purchase_price_inc_tax * $qty_remaining);

            $exp_date = null;

            $lot_number = null;

            $old_qty = 0;

            $data = array(

                'business_id' =>  $business_id,

                'product_id' =>   $request->product_id,

                'fuel_tank_number' =>   $request->fuel_tank_number,

                'location_id' =>   $request->location_id,

                'storage_volume' =>   $request->storage_volume,

                'bulk_tank' =>   $request->bulk_tank,

                'current_balance' =>   0, //this will update current qty in product stock updated below

                'user_id' =>   Auth::user()->id,

                'transaction_date' =>   date('Y-m-d', strtotime($transaction_date)),

                'tank_dip_chart_id' =>   $request->tank_dip_chart_id,

                'tank_manufacturer' =>   $request->tank_manufacturer,

                'tank_capacity' =>   $request->tank_capacity,

                'unit_name' =>   $request->unit_name

            );



            DB::beginTransaction();



            $fuel_tank = FuelTank::create($data);

            //$k is variation id

            $this->productUtil->updateProductQuantity($request->location_id, $request->product_id, $k, $request->current_balance, $old_qty, null, false, $fuel_tank->id);

            $transaction = Transaction::create(

                [

                    'type' => 'opening_stock',

                    'opening_stock_product_id' => $request->product_id,

                    'status' => 'received',

                    'business_id' => $business_id,

                    'transaction_date' => date('Y-m-d', strtotime($transaction_date)),

                    'total_before_tax' => $purchase_total,

                    'location_id' => $request->location_id,

                    'final_total' => $purchase_total,

                    'payment_status' => 'paid',

                    'created_by' => Auth::user()->id

                ]

            );



            $purchase_line = new PurchaseLine();

            $purchase_line->product_id = $product->id;

            $purchase_line->variation_id = $k;

            $purchase_line->item_tax = $item_tax;

            $purchase_line->tax_id = $tax_id;

            $purchase_line->quantity =  $request->current_balance;

            $purchase_line->pp_without_discount = $purchase_price;

            $purchase_line->purchase_price = $purchase_price;

            $purchase_line->purchase_price_inc_tax = $purchase_price_inc_tax;

            $purchase_line->exp_date = $exp_date;

            $purchase_line->lot_number = $lot_number;

            $purchase_line->transaction_id = $transaction->id;



            $purchase_line->save();



            $product_details = $this->transactionUtil->getTransactionProductDetail($transaction->id,  $transaction->type)[0];



            //create pruchase line for tank 

            TankPurchaseLine::create([

                'business_id' => $business_id,

                'transaction_id' => $transaction->id,

                'tank_id' => $fuel_tank->id,

                'product_id' => $request->product_id,

                'quantity' => $request->current_balance

            ]);





            if ($qty_remaining  > 0) {

                $acc_tran_type = 'debit';

            }

            if ($qty_remaining  < 0) {

                $acc_tran_type = 'credit';

            }

            if ($qty_remaining != 0) {

                if ($product_details->enable_stock) {

                    if (!empty($product_details->stock_type)) {

                        $account_id = $product_details->stock_type;

                        $account_transaction_data = [

                            'amount' => $transaction->final_total,

                            'account_id' => $account_id,

                            'type' => $acc_tran_type,

                            'operation_date' => $transaction->transaction_date,

                            'created_by' => $transaction->created_by,

                            'transaction_id' => $transaction->id,

                            'transaction_payment_id' => null,

                            'note' => null

                        ];



                        AccountTransaction::createAccountTransaction($account_transaction_data);

                    }

                }



                $opening_balance_equity_id = $this->transactionUtil->account_exist_return_id('Opening Balance Equity Account');

                $this->transactionUtil->createAccountTransaction($transaction, 'credit', $opening_balance_equity_id, $transaction->final_total);

            }



            DB::commit();

            $output = [

                'success' => true,

                'msg' => __('petro::lang.fuel_tank_add_success')

            ];

        } catch (\Exception $e) {

            Log::emergency('File: ' . $e->getFile() . 'Line: ' . $e->getLine() . 'Message: ' . $e->getMessage());

            $output = [

                'success' => false,

                'msg' => __('messages.something_went_wrong')

            ];

        }



        return redirect()->back()->with('status', $output);

    }



    /**

     * Display the specified resource.

     *

     * @param  int  $id

     * @return \Illuminate\Http\Response

     */

    public function show($id)

    {

        //

    }



    /**

     * Show the form for editing the specified resource.

     *

     * @param  int  $id

     * @return \Illuminate\Http\Response

     */

    public function edit($id)

    {

        $business_id = request()->session()->get('business.id');

        $locations = BusinessLocation::forDropdown($business_id);

        $products = Product::leftjoin('categories', 'products.category_id', 'categories.id')

            ->where('products.business_id', $business_id)

            ->where('categories.name', 'Fuel')

            ->pluck('products.name', 'products.id');

        $fuel_tank = FuelTank::findOrFail($id);

        $sheet_names = TankDipChart::pluck('sheet_name', 'id');

        $tank_dip_chart_permission = $this->moduleUtil->hasThePermissionInSubscription($business_id, 'tank_dip_chart');



        return view('petro::fuel_tanks.edit')->with(compact('locations', 'products', 'fuel_tank', 'tank_dip_chart_permission', 'sheet_names'));

    }



    /**

     * Update the specified resource in storage.

     *

     * @param  \Illuminate\Http\Request  $request

     * @param  int  $id

     * @return \Illuminate\Http\Response

     */

    public function update(Request $request, $id)

    {

        try {

            $business_id = request()->session()->get('business.id');

            $data = array(

                'business_id' =>  $business_id,

                'product_id' =>   $request->product_id,

                'fuel_tank_number' =>   $request->fuel_tank_number,

                'location_id' =>   $request->location_id,

                'storage_volume' =>   $request->storage_volume,

                'tank_dip_chart_id' =>   $request->tank_dip_chart_id,  //sheet name

                'tank_manufacturer' =>   $request->tank_manufacturer,

                'tank_capacity' =>   $request->tank_capacity,

                'unit_name' =>   $request->unit_name,

                'bulk_tank' =>   $request->bulk_tank,

                'user_id' =>   Auth::user()->id,

                'transaction_date' =>   date('Y-m-d', strtotime($request->transaction_date))

            );



            DB::beginTransaction();

            FuelTank::where('id', $id)->update($data);



            $opening_stock = Transaction::leftjoin('purchase_lines', 'transactions.id', 'purchase_lines.transaction_id')

                ->where('transactions.business_id', $business_id)

                ->where('transactions.type', 'opening_stock')

                ->where('purchase_lines.product_id', $request->product_id)

                ->select('transactions.*')

                ->first();

            if (!empty($opening_stock)) {

                $opening_balance_equity_id = $this->transactionUtil->account_exist_return_id('Opening Balance Equity Account');

                $os_account_transaction = AccountTransaction::where('account_transactions.transaction_id', $opening_stock->id)->where('account_id', $opening_balance_equity_id)->first();



                if (empty($os_account_transaction)) {

                    $this->transactionUtil->createAccountTransaction($opening_stock, 'credit', $opening_balance_equity_id, $opening_stock->final_total);

                }

            }



            DB::commit();

            $output = [

                'success' => true,

                'msg' => __('petro::lang.fuel_tank_update_success')

            ];

        } catch (\Exception $e) {

            Log::emergency('File: ' . $e->getFile() . 'Line: ' . $e->getLine() . 'Message: ' . $e->getMessage());

            $output = [

                'success' => false,

                'msg' => __('messages.something_went_wrong')

            ];

        }



        return redirect()->back()->with('status', $output);

    }



    /**

     * Remove the specified resource from storage.

     *

     * @param  int  $id

     * @return \Illuminate\Http\Response

     */

    public function destroy($id)

    {

        $business_id = request()->session()->get('business.id');



        try {

            $tank_purchases = TankPurchaseLine::leftjoin('transactions', 'tank_purchase_lines.transaction_id', 'transactions.id')->where('transactions.type', '!=', 'opening_stock')->where('tank_purchase_lines.business_id', $business_id)->where('tank_id', $id)->count();

            $tank_lines = TankSellLine::where('business_id', $business_id)->where('tank_id', $id)->count();



            if ($tank_purchases == 0 && $tank_lines == 0) {

                $fuel_tank = FuelTank::where('id', $id)->first();

                $product_id = $fuel_tank->product_id;

                $variation = Variation::where('product_id', $product_id)->first();

                $product = Product::findOrFail($product_id);

                $location_id = $fuel_tank->location_id;

                $old_quantity = 0;

                $new_quantity = $fuel_tank->current_balance;



                $this->productUtil->decreaseProductQuantity($product_id, $variation->id, $location_id, $new_quantity, $old_quantity);



                $account_id = $product->stock_type;



                $account_transaction_data = [

                    'amount' => $variation->default_purchase_price * $new_quantity,

                    'account_id' => $product->stock_type,

                    'type' => 'credit',

                    'operation_date' => date('Y-m-d H:i:s'),

                    'created_by' => Auth::user()->id,



                ];



                AccountTransaction::createAccountTransaction($account_transaction_data);



                $fuel_tank->delete();

                $output = [

                    'success' => true,

                    'msg' => __('petro::lang.tank_delete_success')

                ];

            } else {

                $output = [

                    'success' => false,

                    'msg' => __('petro::lang.transactions_exist_for_tank')

                ];

            }

        } catch (\Exception $e) {

            Log::emergency('File: ' . $e->getFile() . 'Line: ' . $e->getLine() . 'Message: ' . $e->getMessage());

            $output = [

                'success' => false,

                'msg' => __('messages.something_went_wrong')

            ];

        }



        return $output;

    }

}

