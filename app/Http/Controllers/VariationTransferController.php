<?php

namespace App\Http\Controllers;

use App\BusinessLocation;
use App\Category;
use App\Product;
use App\PurchaseLine;
use App\Store;
use App\Transaction;
use App\TransactionSellLinesPurchaseLines;
use App\Utils\ModuleUtil;
use App\Utils\ProductUtil;
use App\Utils\TransactionUtil;
use App\Variation;
use App\VariationTransfer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;

class VariationTransferController extends Controller
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

            $variation_transfers = VariationTransfer::leftjoin('variations as vf', 'vf.id', 'variation_transfers.from_variation_id')
                ->leftjoin('variations as vt', 'vt.id', 'variation_transfers.to_variation_id')
                ->leftjoin('products as fp', 'fp.id', 'vf.product_id')
                ->leftjoin('products as tp', 'tp.id', 'vt.product_id')
                ->leftjoin('business_locations as lf', 'lf.id', 'variation_transfers.from_location')
                ->leftjoin('business_locations as lt', 'lt.id', 'variation_transfers.to_location')
                ->leftjoin('stores as sf', 'sf.id', 'variation_transfers.from_store')
                ->leftjoin('stores as st', 'st.id', 'variation_transfers.to_store')
                ->leftjoin('categories', 'categories.id', 'variation_transfers.category_id')
                ->leftjoin('categories as sub_category', 'sub_category.id', 'variation_transfers.sub_category_id')
                ->leftjoin('users', 'variation_transfers.created_by', 'users.id')
                ->where('variation_transfers.business_id', $business_id)
                ->select([
                    'variation_transfers.*',
                    'vf.name as vf_name',
                    'vt.name as vt_name',
                    'vf.product_id as vf_product_id',
                    'vt.product_id as vt_product_id',
                    'lf.name as lf_name',
                    'lt.name as lt_name',
                    'sf.name as sf_name',
                    'st.name as st_name',
                    'fp.name as fp_name',
                    'tp.name as tp_name',
                    'users.username as added_by',
                    'categories.name as category_name',
                    'sub_category.name as sub_category_name',

                ]);
            if (!empty(request()->from_location)) {
                $variation_transfers->where('variation_transfers.from_location', request()->from_location);
            }
            if (!empty(request()->to_location)) {
                $variation_transfers->where('variation_transfers.to_location', request()->to_location);
            }

            if (!empty(request()->from_store)) {
                $variation_transfers->where('variation_transfers.from_store', request()->from_store);
            }

            if (!empty(request()->to_store)) {
                $variation_transfers->where('variation_transfers.to_store', request()->to_store);
            }

            if (!empty(request()->category_id)) {
                $variation_transfers->where('variation_transfers.category_id', request()->category_id);
            }

            if (!empty(request()->sub_category_id)) {
                $variation_transfers->where('variation_transfers.sub_category_id', request()->sub_category_id);
            }

            if (!empty(request()->from_variation_id)) {
                $variation_transfers->where('variation_transfers.from_variation_id', request()->from_variation_id);
            }

            if (!empty(request()->to_variation_id)) {
                $variation_transfers->where('variation_transfers.to_variation_id', request()->to_variation_id);
            }

            if (!empty(request()->start_date) && !empty(request()->end_date)) {
                $variation_transfers->where('variation_transfers.date', '>=', request()->start_date);
                $variation_transfers->where('variation_transfers.date', '<=', request()->end_date);
            }

            return DataTables::of($variation_transfers)
                ->addColumn(
                    'action',
                    function ($row) {
                        $html = '<div class="btn-group">
                            <button type="button" class="btn btn-info dropdown-toggle btn-xs" 
                                data-toggle="dropdown" aria-expanded="false">' .
                            __("messages.actions") .
                            '<span class="caret"></span><span class="sr-only">Toggle Dropdown
                                </span>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-left" role="menu">';
                        $html .= '<li><a href="#" data-href="' . action('VariationTransferController@edit', [$row->id]) . '" class="btn-modal" data-container=".variation_transfer_modal"><i class="glyphicon glyphicon-edit"></i> ' . __("messages.edit") . '</a></li>';
                        $html .= '<li><a href="#" data-href="' . action('VariationTransferController@show', [$row->id]) . '" class="btn-modal" data-container=".variation_transfer_modal"><i class="fa fa-eye"></i> ' . __("messages.view") . '</a></li>';

                        $html .= '<li><a href="#" data-href="' . action('VariationTransferController@destroy', [$row->id]) . '" class="delete-variation-transfer"><i class="fa fa-trash"></i> ' . __("messages.delete") . '</a></li>';


                        return $html;
                    }
                )
                ->editColumn('unit_cost', '{{@num_format($unit_cost)}}')
                ->editColumn('total_cost', '{{@num_format($total_cost)}}')
                ->editColumn('date', '{{@format_date($qty)}}')
                ->editColumn('qty', '{{@format_quantity($qty)}}')
                ->editColumn('fp_name', function ($row) {
                    $name = $row->fp_name;
                    if (!empty($row->vf_name) && $row->vf_name != 'DUMMY') {
                        $name .= '(' . $row->vf_name . ')';
                    }

                    return $name;
                })
                ->editColumn('tp_name', function ($row) {
                    $name = $row->tp_name;
                    if (!empty($row->vt_name) && $row->vt_name != 'DUMMY') {
                        $name .= '(' . $row->vt_name . ')';
                    }

                    return $name;
                })
                ->removeColumn('id')
                ->rawColumns(['action'])
                ->make(true);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

        $business_id   = request()->session()->get('business.id');
        $variations = Variation::getVariationDropdown($business_id);
        $business_locations = BusinessLocation::forDropdown($business_id);
        $categories = Category::forDropdown($business_id);
        $sub_categories = Category::subCategoryforDropdown($business_id);

        return view('variation_transfer.create')->with(compact(
            'variations',
            'business_locations',
            'categories',
            'sub_categories',
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
        $business_id   = request()->session()->get('business.id');
        $user_id = $request->session()->get('user.id');
        try {
            $input = $request->except('_token');
            $input['business_id'] = $business_id;
            $input['date'] = $this->transactionUtil->uf_date($input['date']);
            $input['qty'] = $this->transactionUtil->num_uf($input['qty']);
            $input['unit_cost'] = $this->transactionUtil->num_uf($input['unit_cost']);
            $input['total_cost'] = $this->transactionUtil->num_uf($input['total_cost']);
            $input['created_by'] = $user_id;

            DB::beginTransaction();
            $variation_transfer = VariationTransfer::create($input);



            $input_data = $request->only(['location_id', 'ref_no', 'additional_notes']);
            $from_store = $request->input('from_store');
            $to_store = $request->input('to_store');


            $input_data['final_total'] = $this->productUtil->num_uf($input['total_cost']);
            $input_data['total_before_tax'] = $input_data['final_total'];

            $input_data['type'] = 'sell_transfer';
            $input_data['business_id'] = $business_id;
            $input_data['created_by'] = $user_id;
            $input_data['transaction_date'] = $input['date'];
            $input_data['shipping_charges'] =  0;
            $input_data['status'] = 'final';
            $input_data['payment_status'] = 'paid';

            //Update reference count
            $ref_count = $this->productUtil->setAndGetReferenceCount('stock_transfer');
            //Generate reference number
            if (empty($input_data['ref_no'])) {
                $input_data['ref_no'] = $this->productUtil->generateReferenceNumber('stock_transfer', $ref_count);
            }


            $sell_lines = [];
            $purchase_lines = [];

            $from_variation_id = $request->from_variation_id;
            $to_variation_id = $request->to_variation_id;

            $from_product_id = Variation::where('id', $from_variation_id)->first()->product_id;
            $to_product_id = Variation::where('id', $to_variation_id)->first()->product_id;
            $input['product_id'] = $from_product_id;

            $sell_line_arr = [
                'product_id' => $from_product_id,
                'variation_id' => $from_variation_id,
                'quantity' => $this->productUtil->num_uf($input['qty']),
                'item_tax' => 0,
                'tax_id' => null
            ];

            $purchase_line_arr = $sell_line_arr;
            $sell_line_arr['unit_price'] = $this->productUtil->num_uf($input['unit_cost']);
            $sell_line_arr['unit_price_inc_tax'] = $sell_line_arr['unit_price'];

            $purchase_line_arr['purchase_price'] = $sell_line_arr['unit_price'];
            $purchase_line_arr['purchase_price_inc_tax'] = $sell_line_arr['unit_price'];
            $purchase_line_arr['product_id'] = $to_product_id;
            $purchase_line_arr['variation_id'] = $to_variation_id;

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

            //set the store id 
            $input_data['store_id'] = $request->input('from_store');
            $input_data['location_id'] = $request->input('from_location');
            //Create Sell Transfer transaction
            $sell_transfer = Transaction::create($input_data);

            //Create Purchase Transfer at transfer location
            $input_data['type'] = 'purchase_transfer';
            $input_data['status'] = 'received';
            $input_data['location_id'] = $request->input('to_location');
            $input_data['transfer_parent_id'] = $sell_transfer->id;

            //transfer to store id
            $input_data['store_id'] = $request->input('to_store');
            $purchase_transfer = Transaction::create($input_data);

            //Sell Product from first location
            if (!empty($sell_lines)) {
                $this->transactionUtil->createOrUpdateSellLines($sell_transfer, $sell_lines, $input['from_location']);
            }

            //Purchase product in second location
            if (!empty($purchase_lines)) {
                $purchase_transfer->purchase_lines()->createMany($purchase_lines);
            }

            $variation_transfer->sell_transfer_id = $sell_transfer->id;
            $variation_transfer->purchase_transfer_id = $purchase_transfer->id;
            $variation_transfer->save();

            $this->productUtil->decreaseProductQuantity(
                $sell_line_arr['product_id'],
                $sell_line_arr['variation_id'],
                $request->input('from_location'),
                $this->productUtil->num_uf($input['qty'])
            );
            $this->productUtil->decreaseProductQuantityStore(
                $sell_line_arr['variation_id'],
                $sell_line_arr['product_id'],
                $request->input('from_location'),
                $this->productUtil->num_uf($input['qty']),
                $from_store
            );

            $this->productUtil->updateProductQuantity(
                $request->input('to_location'),
                $purchase_line_arr['product_id'],
                $purchase_line_arr['variation_id'],
                $purchase_line_arr['quantity']
            );
            $this->productUtil->updateProductQuantityStore(
                $request->input('to_location'),
                $purchase_line_arr['product_id'],
                $purchase_line_arr['variation_id'],
                $purchase_line_arr['quantity'],
                $to_store
            );


            //Adjust stock over selling if found
            $this->productUtil->adjustStockOverSelling($purchase_transfer);

            //Map sell lines with purchase lines
            $business = [
                'id' => $business_id,
                'accounting_method' => $request->session()->get('business.accounting_method'),
                'location_id' => $sell_transfer->location_id
            ];
            $this->transactionUtil->mapPurchaseSell($business, $sell_transfer->sell_lines, 'purchase');

            DB::commit();

            $output = [
                'success' => true,
                'tab' => 'variation_transfer',
                'msg' => __('lang_v1.success')
            ];
        } catch (\Exception $e) {
            Log::emergency('File: ' . $e->getFile() . 'Line: ' . $e->getLine() . 'Message: ' . $e->getMessage());
            $output = [
                'success' => false,
                'tab' => 'variation_transfer',
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
        $business_id   = request()->session()->get('business.id');
        $variations = Variation::getVariationDropdown($business_id);
        $business_locations = BusinessLocation::forDropdown($business_id);
        $categories = Category::forDropdown($business_id);
        $sub_categories = Category::subCategoryforDropdown($business_id);
        $stores = Store::where('business_id', $business_id)->pluck('name', 'id');
        $variation_transfer = VariationTransfer::find($id);

        return view('variation_transfer.show')->with(compact(
            'variations',
            'business_locations',
            'categories',
            'sub_categories',
            'stores',
            'variation_transfer',
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
        $business_id   = request()->session()->get('business.id');
        $variations = Variation::getVariationDropdown($business_id);
        $business_locations = BusinessLocation::forDropdown($business_id);
        $categories = Category::forDropdown($business_id);
        $sub_categories = Category::subCategoryforDropdown($business_id);
        $stores = Store::where('business_id', $business_id)->pluck('name', 'id');
        $variation_transfer = VariationTransfer::find($id);

        return view('variation_transfer.edit')->with(compact(
            'variations',
            'business_locations',
            'categories',
            'sub_categories',
            'stores',
            'variation_transfer',
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
        //
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
            if (request()->ajax()) {
                $variation_transfer = VariationTransfer::where('id', $id)->first();

                $edit_days = request()->session()->get('business.transaction_edit_days');
                if (!$this->transactionUtil->canBeEdited($id, $edit_days)) {
                    return [
                        'success' => 0,
                        'msg' => __('messages.transaction_edit_not_allowed', ['days' => $edit_days])
                    ];
                }

                //Get sell transfer transaction
                $sell_transfer = Transaction::where('id', $variation_transfer->sell_transfer_id)
                    ->where('type', 'sell_transfer')
                    ->with(['sell_lines'])
                    ->first();

                //Get purchase transfer transaction
                $purchase_transfer = Transaction::where('transfer_parent_id', $sell_transfer->id)
                    ->where('type', 'purchase_transfer')
                    ->with(['purchase_lines'])
                    ->first();

                //Check if any transfer stock is deleted and delete purchase lines
                $purchase_lines = $purchase_transfer->purchase_lines;
                foreach ($purchase_lines as $purchase_line) {
                    if ($purchase_line->quantity_sold > 0) {
                        return [
                            'success' => 0,
                            'msg' => __('lang_v1.stock_transfer_cannot_be_deleted')
                        ];
                    }
                }

                DB::beginTransaction();
                //Get purchase lines from transaction_sell_lines_purchase_lines and decrease quantity_sold
                $sell_lines = $sell_transfer->sell_lines;
                $deleted_sell_purchase_ids = [];

                foreach ($sell_lines as $sell_line) {
                    $purchase_sell_line = TransactionSellLinesPurchaseLines::where('sell_line_id', $sell_line->id)->first();

                    if (!empty($purchase_sell_line)) {
                        //Decrease quntity sold from purchase line
                        PurchaseLine::where('id', $purchase_sell_line->purchase_line_id)
                            ->decrement('quantity_sold', $sell_line->quantity);

                        $deleted_sell_purchase_ids[] = $purchase_sell_line->id;
                    }
                }

                //Update quantity available in both location
                if (!empty($sell_lines)) {
                    foreach ($sell_lines as $key => $value) {

                        //Increase in sell line variation
                        $this->productUtil->updateProductQuantity(
                            $sell_transfer->location_id,
                            $value->product_id,
                            $value->variation_id,
                            $value->quantity
                        );
                        $this->productUtil->updateProductQuantityStore(
                            $sell_transfer->location_id,
                            $value->product_id,
                            $value->variation_id,
                            $value->quantity,
                            $variation_transfer->from_store
                        );
                    }
                }

                //decrease purchse line variation
                foreach ($purchase_lines as $value) {
                    $this->productUtil->decreaseProductQuantity(
                        $value->product_id,
                        $value->variation_id,
                        $purchase_transfer->location_id,
                        $value->quantity
                    );
                    $this->productUtil->decreaseProductQuantityStore(
                        $value->product_id,
                        $value->variation_id,
                        $purchase_transfer->location_id,
                        $value->quantity,
                        $variation_transfer->to_store
                    );
                }

                //Delete sale line purchase line
                if (!empty($deleted_sell_purchase_ids)) {
                    TransactionSellLinesPurchaseLines::whereIn('id', $deleted_sell_purchase_ids)
                        ->delete();
                }

                //Delete both transactions
                $sell_transfer->delete();
                $purchase_transfer->delete();
                $variation_transfer->delete();

                $output = [
                    'success' => 1,
                    'msg' => __('lang_v1.stock_transfer_delete_success')
                ];
                DB::commit();
            }
        } catch (\Exception $e) {
            DB::rollBack();
            Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

            $output = [
                'success' => 0,
                'msg' => __('messages.something_went_wrong')
            ];
        }
        return $output;
    }

    public function getVariationByCategory()
    {
        $category_id = request()->cat_id;
        $sub_category_id = request()->sub_cat_id;
        $business_id   = request()->session()->get('business.id');

        $variations = Variation::getVariationDropdown($business_id, $category_id, $sub_category_id);

        $html = $this->transactionUtil->createDropdownHtml($variations, 'Please Select');

        return $html;
    }

    public function getVariationOfProduct($variation_id)
    {
        $business_id   = request()->session()->get('business.id');

        $variations = Variation::getVariationDropdown($business_id, null, null, $variation_id);

        $html = $this->transactionUtil->createDropdownHtml($variations, 'Please Select');

        return $html;
    }
}
