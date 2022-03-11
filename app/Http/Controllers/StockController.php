<?php

namespace App\Http\Controllers;

use App\BusinessLocation;
use App\Product;
use App\User;
use App\VariationStoreDetail;
use App\Variation_store_detail;
use App\Category;
use App\Contact;
use App\TransactionSellLine;
use App\PurchaseLine;
use App\Store;
use App\Transaction;
use App\TransactionSellLinesPurchaseLines;
use App\StockTransferRequest;
use App\Utils\ModuleUtil;
use App\Utils\ProductUtil;
use App\Utils\TransactionUtil;
use Datatables;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StockController extends Controller
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
    public function List_Store_Transaction()
    {   
        $business_id = request()->session()->get('user.business_id');
        if (request()->ajax()) {
            $list_store_transactions= Transaction::selectRaw(
                'transactions.*, 
                bl.id AS location_id, bl.name AS business_location, 
                p.id AS product_id, p.name AS product, purchase_product.id AS pur_product_id, p.category_id,
                p.sub_category_id, transactions.type, s.name,
                (SELECT name FROM stores WHERE transactions.To_Account = stores.id ) AS to_store, 
                (SELECT name FROM stores WHERE transactions.From_Account = stores.id ) AS from_store, 
                tsl.quantity AS qty_issue, tpl.quantity AS qty_recieve, u.username AS user, tsl.variation_id AS tsl_variation_id,
                tpl.variation_id AS tpl_variation_id')
            ->leftjoin('transaction_sell_lines AS tsl', 
                        'transactions.id', '=', 'tsl.transaction_id')
            ->leftjoin('purchase_lines AS tpl', 
                        'tpl.transaction_id', '=', 'transactions.id')
            ->leftjoin('business_locations AS bl', 
                        'transactions.location_id', '=', 'bl.id')
            ->leftjoin('products AS p', 'tsl.product_id', '=', 
                        'p.id', 'OR', 'tpl.product_id', '=', 'p.id')
            ->leftjoin('products AS purchase_product', 'tpl.product_id', '=', 
                        'purchase_product.id')
            ->leftjoin('stores AS s', 'transactions.store_id', 
                        '=', 's.id')
            ->leftjoin('users AS u', 'transactions.created_by', '=',        'u.id' )
            ->where('transactions.business_id', $business_id)
            ->with('transection_product')
            // ->whereColumn('transactions.From_Account','!=','transactions.To_Account')
            ->whereIn('transactions.type', array('sell', 'purchase', 'Stock_transfer', 'purchase_transfer', 'sell_transfer'))
            ->get();
            if (!empty(request()->start_date) && !empty(request()->end_date)) {
                    $list_store_transactions->where('list_store_transactions.created_at', '>=', request()->start_date);
                    $list_store_transactions->where('list_store_transactions.created_at', '<=', request()->end_date);
            }
            if (!empty(request()->business_location)) {
                $list_store_transactions = $list_store_transactions->where('location_id', request()->business_location);
            }
            if (!empty(request()->from_store)) {
                $list_store_transactions = $list_store_transactions->where('From_Account', request()->from_store);
            }
            if (!empty(request()->to_store)) {
                $list_store_transactions = $list_store_transactions->where('To_Account', request()->to_store);
            }
            if (!empty(request()->category)) {
                $list_store_transactions = $list_store_transactions->where('category_id', request()->category);
            }
            if (!empty(request()->sub_category)) {
                $list_store_transactions = $list_store_transactions->where('sub_category_id', request()->sub_category);
            }
            if (!empty(request()->products)) {
                $list_store_transactions = $list_store_transactions->where('product_id', request()->products);
            }
            if (!empty(request()->type)) {
                $list_store_transactions = $list_store_transactions->where('type', request()->type);
            }
            if (!empty(request()->user)) {
                $list_store_transactions = $list_store_transactions->where('user', request()->user);
            }
            return DataTables::of($list_store_transactions)
                ->addColumn('action', function ($row) {
                    $html = '<div class="btn-group">
                    <button type="button" class="btn btn-info dropdown-toggle btn-xs" 
                        data-toggle="dropdown" aria-expanded="false">' .
                        __("messages.actions") .
                        '<span class="caret"></span><span class="sr-only">Toggle Dropdown
                        </span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-left" role="menu">';
                    $html .= '<li><a href="#" data-href="#" class="btn-modal" data-container=".view_modal"><i class="fa fa-external-link" aria-hidden="true"></i> ' . __("messages.view") . '</a></li>';
                    $html .= '<li><a href="#" data-href="#" class="btn-modal" data-container=".view_modal"><i class="glyphicon glyphicon-edit"></i> ' . __("messages.edit") . '</a></li>';
                    $html .= '<li><a href="#" data-href="#" class="delete-request" ><i class="glyphicon glyphicon-trash"></i> ' . __("messages.delete") . '</a></li>';
                    if (auth()->user()->permitted_locations() == 'all' || in_array($row->request_to_location, auth()->user()->permitted_locations())) {
                        $html .= '<li><a target="_blank" href="#" class="create_transfer" ><i class="fa fa-exchange"></i> ' . __("lang_v1.create_transfer") . '</a></li>';
                    }
                    if ($row->status == 'transit') {
                        $user_id = Auth::user()->id;
                        if ($row->created_by == $user_id) {
                            $html .= '<li><a href="#" data-href="#" class="btn-modal" data-container=".view_modal"><i class="fa fa-arrow-down"></i> ' . __("lang_v1.received") . '</a></li>';
                        }
                    }
                    $html .= '</ul></div>';
                    return $html;
                })
                ->editColumn(
                    'qty_recieve',
                    '{{@format_quantity($qty_recieve)}}'
                )
                ->editColumn(
                    'qty_issue',
                    '{{@format_quantity($qty_issue)}}'
                )
                ->editColumn('type', function ($row) {
                    $html = __("store.".$row->type);
                    return $html;
                })
                ->addColumn(
                    'to_store',
                    '{{$to_store}}'
                )
                ->addColumn(
                    'from_store',
                    '{{$from_store}}'
                )
                ->addColumn(
                    'product',
                    '{{$product}}'
                )
                ->addColumn(
                    'business_location',
                    '{{$business_location}}'
                )
                ->addColumn(
                    'ref_no',
                    '{{$ref_no}}'
                )
                ->addColumn(
                    'user',
                    '{{$user}}'
                )
                ->addColumn('balance_qty', function($row){
                    if($row->transection_product){
                       $data = Variation_store_detail::where('product_id',$row->product_id)->where('store_id',$row->store_id);
                        if(isset($row->tsl_variation_id)){
                            $data = $data->where('variation_id', '=', $row->tsl_variation_id);
                        }
                       $data = $data->first()->qty_available ?? 'Undefined';
                       return  $data;
                    }else{
                        $data = Variation_store_detail::where('product_id',$row->pur_product_id)->where('store_id',$row->store_id);
                        if(isset($row->tpl_variation_id)){
                            $data = $data->where('variation_id', '=', $row->tpl_variation_id);
                        }
                        $data = $data->first()->qty_available ?? 'Undefined';
                        return  $data;
                    }
                })
                ->editColumn('transaction_date', '{{@format_datetime($transaction_date)}}')
                ->rawColumns(['action'])
                ->make(true);        
        }
        $business_locations = BusinessLocation::forDropdown($business_id);
        $categoryName = Category::forDropdown($business_id);
        $subcategoryName = Category::subCategoryforDropdown($business_id);
        $productName = Product::forDropdown($business_id);
        $storeName = Store::forDropdown($business_id);
        $userName = User::forDropdown($business_id);
        return view('stock_transfer.requests.List_Store_Transaction')->with(compact('business_locations','productName','storeName','userName','subcategoryName','categoryName'));
    }
     public function getsubcat($name=0){
         if($name=='All')
         {
             $business_id = request()->session()->get('user.business_id');
             $empData['data']=Category::where('parent_id', '!=', 0 )->where('business_id',$business_id)->get('name');
         }
         else{
             $id=Category::where('name',$name)->get('id');
        $empData['data']=Category::where('parent_id',$id[0]->id)->get('name');
         }
     return response()->json($empData);
   }
     public function getproduct($name=0){
         if($name=='All')
         {
             $business_id = request()->session()->get('user.business_id');
             $empData['data']=Product::where('business_id',$business_id)->get('name');
         }
         else{
             $id=Category::where('name',$name)->get('id');
             $empData['data']=Product::where('category_id',$id[0]->id)->get();
         }
    return response()->json($empData);
   }
    public function getproductfind($name=0){
         if($name=='All')
         {
             $business_id = request()->session()->get('user.business_id');
             $empData['data']=Product::where('business_id',$business_id)->get('name');
         }
         else{
             $id=Category::where('name',$name)->get('id');
             $empData['data']=Product::where('sub_category_id',$id[0]->id)->get();
         }
    return response()->json($empData);
   }
}
