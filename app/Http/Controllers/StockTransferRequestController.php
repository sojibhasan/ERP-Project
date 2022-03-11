<?php

namespace App\Http\Controllers;

use App\BusinessLocation;
use App\Category;
use App\Events\StockTransferRequestComplete;
use App\Product;
use App\StockTransferRequest;
use App\Transaction;
use App\Utils\ModuleUtil;
use App\Utils\ProductUtil;
use App\Utils\TransactionUtil;
use App\Variation;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use PhpParser\Node\Expr\Variable;
use Yajra\DataTables\Facades\DataTables;

class StockTransferRequestController extends Controller
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
        if (request()->ajax()) {
            $business_id = request()->session()->get('user.business_id');
            $edit_days = request()->session()->get('business.transaction_edit_days');

            $stock_transfer_requests = StockTransferRequest::leftjoin(
                'business_locations AS rl',
                'stock_transfer_requests.request_location',
                '=',
                'rl.id'
            )
                ->leftjoin(
                    'business_locations AS rtl',
                    'stock_transfer_requests.request_to_location',
                    '=',
                    'rtl.id'
                )
                ->leftjoin(
                    'products',
                    'stock_transfer_requests.product_id',
                    '=',
                    'products.id'
                )
                ->leftjoin(
                    'users',
                    'stock_transfer_requests.created_by',
                    '=',
                    'users.id'
                )
                ->where('stock_transfer_requests.business_id', $business_id)
                ->select(
                    'stock_transfer_requests.*',
                    'rl.name as rl',
                    'rtl.name as rtl',
                    'products.name as product_name',
                    'users.username as username',
                );

            if (!empty(request()->start_date) && !empty(request()->end_date)) {
                $stock_transfer_requests->whereDate('stock_transfer_requests.created_at', '>=', request()->start_date);
                $stock_transfer_requests->whereDate('stock_transfer_requests.created_at', '<=', request()->end_date);
            }
            if (!empty(request()->request_location)) {
                $stock_transfer_requests->where('stock_transfer_requests.request_location', request()->request_location);
            }
            if (!empty(request()->request_to_location)) {
                $stock_transfer_requests->where('stock_transfer_requests.request_to_location', request()->request_to_location);
            }
            if (!empty(request()->category_id)) {
                $stock_transfer_requests->where('stock_transfer_requests.category_id', request()->category_id);
            }
            if (!empty(request()->sub_category_id)) {
                $stock_transfer_requests->where('stock_transfer_requests.sub_category_id', request()->sub_category_id);
            }
            if (!empty(request()->product_id)) {
                $stock_transfer_requests->where('stock_transfer_requests.product_id', request()->product_id);
            }
            if (!empty(request()->status)) {
                $stock_transfer_requests->where('stock_transfer_requests.status', request()->status);
            }

            return DataTables::of($stock_transfer_requests)
                ->addColumn('action', function ($row) {
                    $html = '<div class="btn-group">
                    <button type="button" class="btn btn-info dropdown-toggle btn-xs" 
                        data-toggle="dropdown" aria-expanded="false">' .
                        __("messages.actions") .
                        '<span class="caret"></span><span class="sr-only">Toggle Dropdown
                        </span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-left" role="menu">';

                    $html .= '<li><a href="#" data-href="' . action("StockTransferRequestController@show", [$row->id]) . '" class="btn-modal" data-container=".view_modal"><i class="fa fa-external-link" aria-hidden="true"></i> ' . __("messages.view") . '</a></li>';
                    $html .= '<li><a href="#" data-href="' . action('StockTransferRequestController@edit', [$row->id]) . '" class="btn-modal" data-container=".view_modal"><i class="glyphicon glyphicon-edit"></i> ' . __("messages.edit") . '</a></li>';
                    $html .= '<li><a href="#" data-href="' . action('StockTransferRequestController@destroy', [$row->id]) . '" class="delete-request" ><i class="glyphicon glyphicon-trash"></i> ' . __("messages.delete") . '</a></li>';
                    if (auth()->user()->permitted_locations() == 'all' || in_array($row->request_to_location, auth()->user()->permitted_locations())) {
                        $html .= '<li><a target="_blank" href="' . action('StockTransferRequestController@createTransfer', [$row->id]) . '" class="create_transfer" ><i class="fa fa-exchange"></i> ' . __("lang_v1.create_transfer") . '</a></li>';
                    }
                    if ($row->status == 'transit') {
                        $user_id = Auth::user()->id;
                        if ($row->created_by == $user_id) {
                            $html .= '<li><a href="#" data-href="' . action('StockTransferRequestController@getReceivedTrasnfer', [$row->id]) . '" class="btn-modal" data-container=".view_modal"><i class="fa fa-arrow-down"></i> ' . __("lang_v1.received") . '</a></li>';
                        }
                    }

                    $html .= '</ul></div>';
                    return $html;
                })
                ->removeColumn('id')
                ->editColumn(
                    'qty',
                    '{{@format_quantity($qty)}}'
                )
                ->editColumn(
                    'status',
                    '{{ucfirst($status)}}'
                )
                ->addColumn(
                    'received_status',
                    function($row){
                        if($row->status == 'received'){
                            if($row->qty == $row->good_condition){
                                return '<label class="label label-success">'.__("lang_v1.received").'</label>';
                            }else{
                                return '<label class="label label-warning">'.__("lang_v1.received").'</label>';
                            }
                        }else{
                            return '';
                        }
                    }
                )

                ->editColumn('date', '{{@format_datetime($created_at)}}')
                ->rawColumns(['action', 'received_status'])
                ->make(true);
        }



        $business_id = request()->session()->get('business.id');
        $business_locations = BusinessLocation::where('business_id', $business_id)->pluck('name', 'id');
        $categories = Category::where('business_id', $business_id)->where('parent_id', 0)->pluck('name', 'id');
        $products = Product::where('business_id', $business_id)->pluck('name', 'id');


        return view('stock_transfer.requests.index')->with(compact(
            'business_locations',
            'categories',
            'products',
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
        $business_locations = BusinessLocation::where('business_id', $business_id)->pluck('name', 'id');
        $categories = Category::where('business_id', $business_id)->where('parent_id', 0)->pluck('name', 'id');

        $products = Product::where('business_id', $business_id)->pluck('name', 'id');


        return view('stock_transfer.requests.create')->with(compact(
            'business_locations',
            'categories',
            'products'
        ));
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
            $input = $request->except('_token');
            $input['business_id'] = $request->session()->get('business.id');
            $input['delivery_need_on'] = !empty($request->delivery_need_on) ? Carbon::parse($request->delivery_need_on)->format('Y-m-d') : date('Y-m-d');
            $input['created_by'] = Auth::user()->id;

            StockTransferRequest::create($input);

            $output = [
                'success' => true,
                'msg' => __('lang_v1.request_create_success')
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
        $stock_transfer_requests = StockTransferRequest::leftjoin(
            'business_locations AS rl',
            'stock_transfer_requests.request_location',
            '=',
            'rl.id'
        )
            ->leftjoin(
                'business_locations AS rtl',
                'stock_transfer_requests.request_to_location',
                '=',
                'rtl.id'
            )
            ->leftjoin(
                'products',
                'stock_transfer_requests.product_id',
                '=',
                'products.id'
            )
            ->leftjoin(
                'categories as cat',
                'stock_transfer_requests.category_id',
                '=',
                'cat.id'
            )
            ->leftjoin(
                'categories as sub_cat',
                'stock_transfer_requests.sub_category_id',
                '=',
                'sub_cat.id'
            )
            ->leftjoin(
                'users',
                'stock_transfer_requests.created_by',
                '=',
                'users.id'
            )
            ->where('stock_transfer_requests.id', $id)
            ->select(
                'stock_transfer_requests.*',
                'rl.name as rl',
                'rtl.name as rtl',
                'products.name as product_name',
                'cat.name as cat_name',
                'sub_cat.name as sub_cat_name',
                'users.username as created_by',
            )->first();

        return view('stock_transfer.requests.show')->with(compact(
            'stock_transfer_requests'
        ));
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
        $business_locations = BusinessLocation::where('business_id', $business_id)->pluck('name', 'id');
        $categories = Category::where('business_id', $business_id)->where('parent_id', 0)->pluck('name', 'id');
        $sub_categories = Category::where('business_id', $business_id)->where('parent_id', '!=', 0)->pluck('name', 'id');
        $products = Product::where('business_id', $business_id)->pluck('name', 'id');

        $transfer_request = StockTransferRequest::findOrFail($id);

        return view('stock_transfer.requests.edit')->with(compact(
            'business_locations',
            'categories',
            'sub_categories',
            'products',
            'transfer_request'
        ));
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
            $input = $request->except('_token', '_method');
            $input['business_id'] = $request->session()->get('business.id');
            $input['delivery_need_on'] = !empty($request->delivery_need_on) ? Carbon::parse($request->delivery_need_on)->format('Y-m-d') : date('Y-m-d');
            $input['created_by'] = Auth::user()->id;

            StockTransferRequest::where('id', $id)->update($input);

            $request_transfer = StockTransferRequest::findOrFail($id);
            if(!empty($request_transfer->transaction_id)){
                $purchase_transaction = Transaction::where('id', $id)->where('type', 'purchase_transfer')->update(['status' => $input['status']]);
            }

            $output = [
                'success' => true,
                'msg' => __('lang_v1.request_update_success')
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
        try {
            StockTransferRequest::where('id', $id)->delete();

            $output = [
                'success' => true,
                'msg' => __('lang_v1.request_delete_success')
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
     * create transfer.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function createTransfer($id)
    {
        $business_id = request()->session()->get('user.business_id');
        $request_transfer = StockTransferRequest::findOrFail($id);
        $business_locations = BusinessLocation::where('business_id', $business_id)->pluck('name', 'id');

        $variation_id = Variation::where('product_id', $request_transfer->product_id)->first();

        return view('stock_transfer.requests.create_transfer')
            ->with(compact('business_locations', 'request_transfer', 'variation_id'));
    }

    /**
     * create transfer.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function saveTransfer(Request $request)
    {
        if (!auth()->user()->can('purchase.create')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $business_id = $request->session()->get('user.business_id');
            DB::table('temp_data')->where('business_id', $business_id)->update(['stock_transfer_data' => '']);
            //Check if subscribed or not
            if (!$this->moduleUtil->isSubscribed($business_id)) {
                return $this->moduleUtil->expiredResponse(action('StockTransferController@index'));
            }

            DB::beginTransaction();

            $input_data = $request->only(['location_id', 'ref_no', 'transaction_date', 'additional_notes', 'shipping_charges', 'final_total']);
            $from_store = $request->input('from_store');
            $to_store = $request->input('to_store');

            $user_id = $request->session()->get('user.id');

            $input_data['final_total'] = $this->productUtil->num_uf($input_data['final_total']);
            $input_data['total_before_tax'] = $input_data['final_total'];

            $input_data['type'] = 'sell_transfer';
            $input_data['business_id'] = $business_id;
            $input_data['created_by'] = $user_id;
            $input_data['transaction_date'] = $this->productUtil->uf_date($input_data['transaction_date'], true);
            $input_data['shipping_charges'] = $this->productUtil->num_uf($input_data['shipping_charges']);
            $input_data['status'] = 'final';
            $input_data['payment_status'] = 'paid';

            //Update reference count
            $ref_count = $this->productUtil->setAndGetReferenceCount('stock_transfer');
            //Generate reference number
            if (empty($input_data['ref_no'])) {
                $input_data['ref_no'] = $this->productUtil->generateReferenceNumber('stock_transfer', $ref_count);
            }

            $products = $request->input('products');
            $sell_lines = [];
            $purchase_lines = [];

            if (!empty($products)) {
                foreach ($products as $product) {
                    $sell_line_arr = [
                        'product_id' => $product['product_id'],
                        'variation_id' => $product['variation_id'],
                        'quantity' => $this->productUtil->num_uf($product['quantity']),
                        'item_tax' => 0,
                        'tax_id' => null
                    ];

                    $purchase_line_arr = $sell_line_arr;
                    $sell_line_arr['unit_price'] = $this->productUtil->num_uf($product['unit_price']);
                    $sell_line_arr['unit_price_inc_tax'] = $sell_line_arr['unit_price'];

                    $purchase_line_arr['purchase_price'] = $sell_line_arr['unit_price'];
                    $purchase_line_arr['purchase_price_inc_tax'] = $sell_line_arr['unit_price'];

                    if (!empty($product['lot_no_line_id'])) {
                        //Add lot_no_line_id to sell line
                        $sell_line_arr['lot_no_line_id'] = $product['lot_no_line_id'];

                        //Copy lot number and expiry date to purchase line
                        $lot_details = PurchaseLine::find($product['lot_no_line_id']);
                        $purchase_line_arr['lot_number'] = $lot_details->lot_number;
                        $purchase_line_arr['mfg_date'] = $lot_details->mfg_date;
                        $purchase_line_arr['exp_date'] = $lot_details->exp_date;
                    }

                    $sell_lines[] = $sell_line_arr;
                    $purchase_lines[] = $purchase_line_arr;
                }
            }
            //set the store id 
            $input_data['store_id'] = $request->input('from_store');
            //Create Sell Transfer transaction
            $sell_transfer = Transaction::create($input_data);

            //Create Purchase Transfer at transfer location
            $input_data['type'] = 'purchase_transfer';
            $input_data['status'] = $request->status;
            $input_data['location_id'] = $request->input('transfer_location_id');
            $input_data['transfer_parent_id'] = $sell_transfer->id;

            //transfer to store id
            $input_data['store_id'] = $request->input('to_store');
            $purchase_transfer = Transaction::create($input_data);

            //Sell Product from first location
            if (!empty($sell_lines)) {
                $this->transactionUtil->createOrUpdateSellLines($sell_transfer, $sell_lines, $input_data['location_id']);
            }

            //Purchase product in second location
            if (!empty($purchase_lines)) {
                $purchase_transfer->purchase_lines()->createMany($purchase_lines);
            }

            //Decrease product stock from sell location
            //And increase product stock at purchase location
            foreach ($products as $product) {
                if ($product['enable_stock']) {

                    $this->productUtil->decreaseProductQuantity(
                        $product['product_id'],
                        $product['variation_id'],
                        $sell_transfer->location_id,
                        $this->productUtil->num_uf($product['quantity'])
                    );
                    $this->productUtil->decreaseProductQuantityStore(
                        $product['variation_id'],
                        $product['product_id'],
                        $sell_transfer->location_id,
                        $this->productUtil->num_uf($product['quantity']),
                        $from_store
                    );

                    // $this->productUtil->updateProductQuantity(
                    //     $purchase_transfer->location_id,
                    //     $product['product_id'],
                    //     $product['variation_id'],
                    //     $product['quantity']
                    // );
                    // $this->productUtil->updateProductQuantityStore(
                    //     $purchase_transfer->location_id,
                    //     $product['product_id'],
                    //     $product['variation_id'],
                    //     $product['quantity'],
                    //     $to_store
                    // );
                }
            }

            //Adjust stock over selling if found
            $this->productUtil->adjustStockOverSelling($purchase_transfer);

            //Map sell lines with purchase lines
            $business = [
                'id' => $business_id,
                'accounting_method' => $request->session()->get('business.accounting_method'),
                'location_id' => $sell_transfer->location_id
            ];
            $this->transactionUtil->mapPurchaseSell($business, $sell_transfer->sell_lines, 'purchase');

            StockTransferRequest::where('id', $request->request_id)->update(['status' => $request->status, 'transaction_id' => $purchase_transfer->id ]);
            $transfer_request = StockTransferRequest::findOrFail($request->request_id);
            $product = Product::findOrFail($transfer_request->product_id);
            event(new StockTransferRequestComplete($transfer_request->created_by, $transfer_request,  $product, $transfer_request->request_location));

            $output = [
                'success' => 1,
                'msg' => __('lang_v1.stock_transfer_added_successfully')
            ];

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

            $output = [
                'success' => 0,
                'msg' => $e->getMessage()
            ];
        }

        return redirect('stock-transfers-request')->with('status', $output);
    }

    public function getNotificationPopup($id)
    {
        $transfer_request = StockTransferRequest::findOrFail($id);
        $product = Product::findOrFail($transfer_request->product_id);

        return view('layouts.partials.stock_transfer_notification')->with(compact(
            'product',
            'transfer_request'
        ));
    }
    public function stopNotification($id)
    {
        StockTransferRequest::where('id', $id)->update(['notification' => 'stop']);

        return redirect()->back();
    }
    public function getReceivedTrasnfer($id)
    {
        $rquest_transfer = StockTransferRequest::where('id', $id)->with(['products'])->first();


        return view('stock_transfer.requests.received_transfer')->with(compact('rquest_transfer'));
    }
    public function postReceivedTransfer($id, Request $request)
    {
        $input = $request->except('_token');
        $input['status'] = 'received';
        StockTransferRequest::where('id', $id)->update($input);
        $transfer_request = StockTransferRequest::findOrFail($id);
        $variation = Variation::where('product_id', $transfer_request->product_id)->first();
        $transaction = Transaction::findOrFail($transfer_request->transaction_id);
        $transaction->status = 'received';
        $transaction->save();
        
        $this->productUtil->updateProductQuantity(
            $transfer_request->request_location,
            $transfer_request->product_id,
            $variation->id,
            $input['good_condition']
        );
        $this->productUtil->updateProductQuantityStore(
            $transfer_request->request_location,
            $transfer_request->product_id,
            $variation->id,
            $input['good_condition'],
            $transaction->store_id
        );

        return redirect()->back()->with('status', ['success' => 1, 'msg' => __('lang_v1.qty_update_success')]);
    }
}
