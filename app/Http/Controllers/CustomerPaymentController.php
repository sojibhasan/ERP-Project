<?php

namespace App\Http\Controllers;

use App\Account;
use App\Business;
use App\BusinessLocation;
use App\Contact;
use App\AccountType;
use App\AccountGroup;
use App\ContactGroup;
use App\Transaction;
use App\TransactionPayment;
use App\User;
use App\Utils\ModuleUtil;
use App\Utils\ProductUtil;
use App\Utils\TransactionUtil;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\Superadmin\Entities\Package;
use Yajra\DataTables\Facades\DataTables;

class CustomerPaymentController extends Controller
{
    protected $transactionUtil;
    protected $moduleUtil;
    protected $productUtil;
    /**
     * Constructor
     *
     * @param TransactionUtil $transactionUtil
     * @return void
     */
    public function __construct(TransactionUtil $transactionUtil, ModuleUtil $moduleUtil, ProductUtil $productUtil)
    {
        $this->transactionUtil = $transactionUtil;
        $this->productUtil = $productUtil;
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
        $business_details = Business::find($business_id);
        $paid_in_types = ['customer_page' => 'Customer Page',
            'all_sale_page' => 'All Sale Page',
            'settlement' => 'Settlement',
            'customer_bulk' => 'Customer Bulk',
            'customer_simple' => 'Customer Simple'];
        $latest_ref_number = 0;
        $latest_ref_number_PP = 0;
        $latest_ref_number_CPB = 0;
        $latest_ref_number_CPS = 0;
        try {
                $latest_ref_number = DB::table('transaction_payments')->orderBy('created_at', 'DESC')->first()->payment_ref_no;
                $latest_ref_number_PP = DB::table('transaction_payments')->where('paid_in_type', 'customer_page')->orderBy('created_at', 'DESC')->first()->payment_ref_no;
                $latest_ref_number_CPB = DB::table('transaction_payments')->where('paid_in_type', 'customer_bulk')->orderBy('created_at', 'DESC')->first()->payment_ref_no;
                $latest_ref_number_CPS = DB::table('transaction_payments')->where('paid_in_type', 'customer_simple')->orderBy('created_at', 'DESC')->first()->payment_ref_no;
            } catch (\Exception $exception) {
            }
            $latest_ref_number = (int)explode('/', $latest_ref_number);
            $latest_ref_number_PP = (int)explode('PP2021/', $latest_ref_number_PP);
            $latest_ref_number_CPB = (int)explode('CPB-', $latest_ref_number_CPB);
            $latest_ref_number_CPS = (int)explode('CPS-', $latest_ref_number_CPS,);
            $latest_ref_number += 1;
            $latest_ref_number_PP += 1;
            $latest_ref_number_CPB += 1;
            $latest_ref_number_CPS += 1;
        if (request()->ajax()) {
            $sells = Transaction::leftJoin('contacts', 'transactions.contact_id', '=', 'contacts.id')
                ->leftJoin('transaction_payments as tp', 'transactions.id', '=', 'tp.transaction_id')
                ->leftJoin('users', 'tp.created_by', '=', 'users.id')
                ->leftJoin('business_locations', 'transactions.location_id', '=', 'business_locations.id')
                ->leftJoin(
                    'account_transactions as act',
                    'transactions.id',
                    '=',
                    'act.transaction_id'
                )
                ->where('transactions.business_id', $business_id)
                ->where('contacts.type', 'customer')
                ->whereIn('transactions.payment_status', ['paid', 'partial'])
                ->where(function ($q) {
                    $q->where('transactions.type', 'opening_balance')->orWhere('transactions.is_credit_sale', 1);
                })
                ->select(
                    'transactions.id',
                    'transactions.transaction_date',
                    'transactions.invoice_no',
                    'contacts.name',
                    'transactions.payment_status',
                    'transactions.final_total',
                    'business_locations.name as location_name',
                    'tp.id as tp_id',
                    'tp.paid_on',
                    'tp.method',
                    'act.id as act_id',
                    'act.interest',
                    'tp.parent_id',
                    'tp.cheque_number',
                    'tp.card_number',
                    'tp.payment_ref_no',
                    'tp.paid_in_type',
                    'tp.created_by',
                     'users.username',
                     'tp.amount as total_paid'
                    //DB::raw('SUM(tp.amount) as total_paid')
                );
            if (!empty(request()->customer_id)) {
                $customer_id = request()->customer_id;
                $sells->where('contacts.id', $customer_id);
            }
            if (!empty(request()->bill_no)) {
                $sells->where('transactions.invoice_no', request()->bill_no);
            }
            if (!empty(request()->payment_ref_no)) {
                $sells->where('tp.payment_ref_no', request()->payment_ref_no);
            }
            if (!empty(request()->cheque_number)) {
                $sells->where('tp.cheque_number', request()->cheque_number);
            }
            if (!empty(request()->payment_method)) {
                $sells->where('tp.method', request()->payment_method);
            }
            if (!empty(request()->paid_in_type)) {
                $sells->where('tp.paid_in_type', request()->paid_in_type);
            }
            if (!empty(request()->start_date) && !empty(request()->end_date)) {
                $start = request()->start_date;
                $end = request()->end_date;
                $sells->whereDate('tp.paid_on', '>=', $start)
                    ->whereDate('tp.paid_on', '<=', $end);
            }
            $sells->orderBy('tp.paid_on', 'desc')->groupBy('tp.id');
            $datatable = DataTables::of($sells)
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
                                    <ul class="dropdown-menu dropdown-menu-right" role="menu">';
                        if (auth()->user()->can("sell.view") || auth()->user()->can("direct_sell.access") || auth()->user()->can("view_own_sell_only")) {
                            $html .= '<li><a href="#" data-href="' . action("SellController@show", [$row->id]) . '" class="btn-modal" data-container=".view_modal"><i class="fa fa-external-link" aria-hidden="true"></i> ' . __("messages.view") . '</a></li>';
                            $html .= '<li><a href="#" data-href="' . action("TransactionPaymentController@edit", [$row->tp_id]) . '" class="btn-modal" data-container=".view_modal"><i class="glyphicon glyphicon-edit" aria-hidden="true"></i> ' . __("messages.edit") . '</a></li>';
                        }
                        $html .= '</ul></div>';
                        return $html;
                    }
                )
                ->addColumn('payment_amount', function ($row) use ($business_details) {
                    if (!empty($row->parent_id)) {
                        $parent_payment = TransactionPayment::where('id', $row->parent_id)->first();
                        if (!empty($parent_payment)) {
                            return '<span class="display_currency final-total" data-currency_symbol="true" data-orig-value="' . $parent_payment->amount . '">' . $this->productUtil->num_f($parent_payment->amount, false, $business_details, false) . '</span>';
                        } else {
                            return '<span class="display_currency final-total" data-currency_symbol="true" data-orig-value="' . $row->total_paid . '">' . $this->productUtil->num_f($row->total_paid, false, $business_details, false) . '</span>';
                        }
                    } else {
                        return '<span class="display_currency final-total" data-currency_symbol="true" data-orig-value="' . $row->total_paid . '">' . $this->productUtil->num_f($row->total_paid, false, $business_details, false) . '</span>';
                    }
                })->addColumn('name', function ($row) {
                    return $row->name;
                })
                ->addColumn('interest', function ($row) {
                    return $row->interest == Null ? '0.00' : $row->interest;
                })
                ->removeColumn('id')
                ->editColumn('final_total', function ($row) use ($business_details) {
                    return '<span class="display_currency final-total" data-currency_symbol="true" data-orig-value="' . $row->final_total . '">' . $this->productUtil->num_f($row->final_total, false, $business_details, false) . '</span>';
                })
                ->editColumn('total_paid', function ($row) use ($business_details) {
                    if ($row->total_paid == '') {
                        $total_paid_html = '<span class="display_currency total-paid" data-currency_symbol="true" data-orig-value="0.00">' . $this->productUtil->num_f(0, false, $business_details, false) . '</span>';
                    } else {
                        $total_paid_html = '<span class="display_currency total-paid" data-currency_symbol="true" data-orig-value="' . $row->total_paid . '">' . $this->productUtil->num_f($row->total_paid, false, $business_details, false) . '</span>';
                    }
                    return $total_paid_html;
                })
                ->editColumn('transaction_date', '{{@format_date($transaction_date)}}')
                ->editColumn('paid_on', '{{@format_date($paid_on)}}')
                ->editColumn('method', function ($row) {
                    if ($row->method == 'bank_transfer') {
                        return 'Bank';
                    }
                    if ($row->method == 'card') {
                        if (!empty($row->card_number)) {
                            $htm = '<span class="" >Card <small>' . $row->card_number . '</small></span>';
                            return $htm;
                        }
                    }
                    if ($row->method == 'cheque') {
                        $html = '<span class="" >Cheque <small>' . $row->bank_name . '</small><small> ' . $row->cheque_number . '</small> <small>' . $row->cheque_date . '</small></span>';
                        return $html;
                    }
                    return ucfirst($row->method);
                })
                ->editColumn('cheque_number', function ($row) {
                    if ($row->method == 'bank_transfer' || $row->method == 'cheque') {
                        return $row->cheque_number;
                    }
                    if ($row->method == 'card') {
                        return $row->card_number;
                    }
                    return '';
                })
                ->editColumn('invoice_no', function ($row) {
                    $invoice_no = $row->invoice_no;
                    if (!empty($row->woocommerce_order_id)) {
                        $invoice_no .= ' <i class="fa fa-wordpress text-primary no-print" title="' . __('lang_v1.synced_from_woocommerce') . '"></i>';
                    }
                    if (!empty($row->return_exists)) {
                        $invoice_no .= ' &nbsp;<small class="label bg-red label-round no-print" title="' . __('lang_v1.some_qty_returned_from_sell') . '"><i class="fa fa-undo"></i></small>';
                    }
                    if (!empty($row->is_recurring)) {
                        $invoice_no .= ' &nbsp;<small class="label bg-red label-round no-print" title="' . __('lang_v1.subscribed_invoice') . '"><i class="fa fa-recycle"></i></small>';
                    }
                    if (!empty($row->recur_parent_id)) {
                        $invoice_no .= ' &nbsp;<small class="label bg-info label-round no-print" title="' . __('lang_v1.subscription_invoice') . '"><i class="fa fa-recycle"></i></small>';
                    }
                    return $invoice_no;
                })
                ->addColumn('paid_in_type', function ($row) use ($paid_in_types) {
                    if (!empty($row->paid_in_type) && !empty($paid_in_types[$row->paid_in_type])) {
                        return $paid_in_types[$row->paid_in_type];
                    }
                    return '';
                })
                ->setRowAttr([
                    'data-href' => function ($row) {
                        if (auth()->user()->can("sell.view") || auth()->user()->can("view_own_sell_only")) {
                            return action('SellController@show', [$row->id]);
                        } else {
                            return '';
                        }
                    }
                ]);
            $rawColumns = ['name', 'method', 'final_total', 'action', 'total_paid', 'total_remaining', 'payment_status', 'invoice_no', 'discount_amount', 'tax_amount', 'total_before_tax', 'shipping_status', 'payment_amount'];
            return $datatable->rawColumns($rawColumns)
                ->make(true);
        }
        $business_id = request()->session()->get('business.id');
        $customers = Contact::customersDropdown($business_id, false);
        $business_locations = BusinessLocation::forDropdown($business_id);
        $payment_types = $this->transactionUtil->payment_types();
        $package_manage = Package::where('only_for_business', $business_id)->first();
        $customer_interest_deduct_option = $business_details->customer_interest_deduct_option;
        $customer_groups = ContactGroup::where('contact_groups.business_id', $business_id)
            ->where('contact_groups.type', 'customer')
            ->pluck('name', 'id');
        $income_accounts = Account::leftJoin('account_types', 'accounts.account_type_id', 'account_types.id')
            ->where('account_types.name', 'Income')
            ->select(DB::raw('accounts.name as name, accounts.id as id'))
            ->pluck('name', 'id');
            
            $account_type_query = AccountType::where('business_id', $business_id)
            ->whereNull('parent_account_type_id');
        $account_types_opts = $account_type_query->pluck('name', 'id');
        $account_type_query->with(['sub_types']);
        if (0 == 0) {
            $account_type_query->where(function ($q) {
                $q->where('name', 'Assets')->orWhere('name', 'Liabilities');
            });
        }
              $account_types = $account_type_query->get();
        // dd($account_types->toArray());
        $filterdata =[];
        $sub_acn_arr = [];
        $filterdata['subType_']['data'][] =array('id'=>"",'text'=>"All",true);
        foreach($account_types->toArray() as $acunts){
            $filterdata['subType_'.$acunts['id']]['data'][] =array('id'=>"",'text'=>"All",true);
            foreach($acunts['sub_types'] as $sub_Acn){
                $filterdata['subType_']['data'][] =array('id'=>$sub_Acn['id'],'text'=>$sub_Acn['name']);
                $filterdata['subType_'.$acunts['id']]['data'][] =array('id'=>$sub_Acn['id'],'text'=>$sub_Acn['name']);
                $sub_acn_arr[$sub_Acn['id']] = $sub_Acn['name'];
            }
        }
        $account_groups_raw = AccountGroup::where('business_id', $business_id)->whereIn('name', ['Cash Account', "Cheques in Hand (Customer's)", 'Card', 'Bank Account'])->get()->toArray();
        $account_groups = [];
        $filterdata['groupType_']['data'][] = array('id'=>"",'text'=>"All",true);
        foreach($account_groups_raw as $datarow){
            $filterdata['groupType_'.$datarow['account_type_id']]['data'][] = array('id'=>$datarow['id'],'text'=>$datarow['name']);
            $account_groups[$datarow['id']] = $datarow['name'];
        }
        return view('customer_payments.index')->with(compact(
            'customers',
            'filterdata',
            'business_locations',
            'payment_types',
            'account_types_opts',
            'account_groups',
            'customer_interest_deduct_option',
            'latest_ref_number',
            'latest_ref_number_PP',
            'latest_ref_number_CPB',
            'latest_ref_number_CPS',
            'customer_groups',
            'income_accounts'
        )); 
    }
    public function CustomerInterest()
    {
        $business_id = request()->session()->get('user.business_id');
        $business_details = Business::find($business_id);
        $paid_in_types = ['customer_page' => 'Customer Page',
            'all_sale_page' => 'All Sale Page',
            'settlement' => 'Settlement',
            'customer_bulk' => 'Customer Bulk',
            'customer_simple' => 'Customer Simple'];
        $latest_ref_number = 0;
        $latest_ref_number_PP = 0;
        $latest_ref_number_CPB = 0;
        $latest_ref_number_CPS = 0;
        try {
                $latest_ref_number = DB::table('transaction_payments')->orderBy('created_at', 'DESC')->first()->payment_ref_no;
                $latest_ref_number_PP = DB::table('transaction_payments')->where('paid_in_type', 'customer_page')->orderBy('created_at', 'DESC')->first()->payment_ref_no;
                $latest_ref_number_CPB = DB::table('transaction_payments')->where('paid_in_type', 'customer_bulk')->orderBy('created_at', 'DESC')->first()->payment_ref_no;
                $latest_ref_number_CPS = DB::table('transaction_payments')->where('paid_in_type', 'customer_simple')->orderBy('created_at', 'DESC')->first()->payment_ref_no;
            } catch (\Exception $exception) {
            }
            $latest_ref_number = (int)explode('/', $latest_ref_number);
            $latest_ref_number_PP = (int)explode('PP2021/', $latest_ref_number_PP);
            $latest_ref_number_CPB = (int)explode('CPB-', $latest_ref_number_CPB);
            $latest_ref_number_CPS = (int)explode('CPS-', $latest_ref_number_CPS,);
            $latest_ref_number += 1;
            $latest_ref_number_PP += 1;
            $latest_ref_number_CPB += 1;
            $latest_ref_number_CPS += 1;
        if (request()->ajax()) {
            $sells = Transaction::leftJoin('contacts', 'transactions.contact_id', '=', 'contacts.id')
                ->leftJoin('transaction_payments as tp', 'transactions.id', '=', 'tp.transaction_id')
                ->leftJoin('users', 'tp.created_by', '=', 'users.id')
                ->leftJoin('business_locations', 'transactions.location_id', '=', 'business_locations.id')
                ->leftJoin(
                    'account_transactions as act',
                    'transactions.id',
                    '=',
                    'act.transaction_id'
                )
                ->where('transactions.business_id', $business_id)
                ->where('contacts.type', 'customer')
                ->where('act.interest','!=', null)
                ->whereIn('transactions.payment_status', ['paid', 'partial'])
                ->where(function ($q) { $q->where('transactions.type', 'opening_balance')->orWhere('transactions.is_credit_sale', 1);})
                ->select(
                    'transactions.id',
                    'transactions.transaction_date',
                    'transactions.invoice_no',
                    'contacts.name',
                    'transactions.payment_status',
                    'transactions.final_total',
                    'business_locations.name as location_name',
                    'tp.id as tp_id',
                    'tp.paid_on',
                    'tp.method',
                    'act.id as act_id',
                    'act.interest',
                    'tp.parent_id',
                    'tp.cheque_number',
                    'tp.card_number',
                    'tp.payment_ref_no',
                    'tp.paid_in_type',
                    'tp.created_by',
                     'users.username',
                     'tp.amount as total_paid'
                    //DB::raw('SUM(tp.amount) as total_paid')
                );
            if (!empty(request()->customer_id)) {
                $customer_id = request()->customer_id;
                $sells->where('contacts.id', $customer_id);
            }
            if (!empty(request()->bill_no)) {
                $sells->where('transactions.invoice_no', request()->bill_no);
            }
            if (!empty(request()->payment_ref_no)) {
                $sells->where('tp.payment_ref_no', request()->payment_ref_no);
            }
            if (!empty(request()->cheque_number)) {
                $sells->where('tp.cheque_number', request()->cheque_number);
            }
            if (!empty(request()->payment_method)) {
                $sells->where('tp.method', request()->payment_method);
            }
            if (!empty(request()->paid_in_type)) {
                $sells->where('tp.paid_in_type', request()->paid_in_type);
            }
            if (!empty(request()->start_date) && !empty(request()->end_date)) {
                $start = request()->start_date;
                $end = request()->end_date;
                $sells->whereDate('tp.paid_on', '>=', $start)
                    ->whereDate('tp.paid_on', '<=', $end);
            }
            $sells->orderBy('tp.paid_on', 'desc')->groupBy('tp.id');
            $datatable = DataTables::of($sells)
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
                                    <ul class="dropdown-menu dropdown-menu-right" role="menu">';
                        if (auth()->user()->can("sell.view") || auth()->user()->can("direct_sell.access") || auth()->user()->can("view_own_sell_only")) {
                            $html .= '<li><a href="#" data-href="' . action("SellController@show", [$row->id]) . '" class="btn-modal" data-container=".view_modal"><i class="fa fa-external-link" aria-hidden="true"></i> ' . __("messages.view") . '</a></li>';
                            $html .= '<li><a href="#" data-href="' . action("TransactionPaymentController@edit", [$row->tp_id]) . '" class="btn-modal" data-container=".view_modal"><i class="glyphicon glyphicon-edit" aria-hidden="true"></i> ' . __("messages.edit") . '</a></li>';
                        }
                        $html .= '</ul></div>';
                        return $html;
                    }
                )
                ->addColumn('payment_amount', function ($row) use ($business_details) {
                    if (!empty($row->parent_id)) {
                        $parent_payment = TransactionPayment::where('id', $row->parent_id)->first();
                        if (!empty($parent_payment)) {
                            return '<span class="display_currency final-total" data-currency_symbol="true" data-orig-value="' . $parent_payment->amount . '">' . $this->productUtil->num_f($parent_payment->amount, false, $business_details, false) . '</span>';
                        } else {
                            return '<span class="display_currency final-total" data-currency_symbol="true" data-orig-value="' . $row->total_paid . '">' . $this->productUtil->num_f($row->total_paid, false, $business_details, false) . '</span>';
                        }
                    } else {
                        return '<span class="display_currency final-total" data-currency_symbol="true" data-orig-value="' . $row->total_paid . '">' . $this->productUtil->num_f($row->total_paid, false, $business_details, false) . '</span>';
                    }
                })->addColumn('name', function ($row) {
                    return $row->name;
                })
                ->addColumn('interest', function ($row) {
                    return $row->interest == Null ? '0.00' : $row->interest;
                })
                ->removeColumn('id')
                ->editColumn('final_total', function ($row) use ($business_details) {
                    return '<span class="display_currency final-total" data-currency_symbol="true" data-orig-value="' . $row->final_total . '">' . $this->productUtil->num_f($row->final_total, false, $business_details, false) . '</span>';
                })
                ->editColumn('total_paid', function ($row) use ($business_details) {
                    if ($row->total_paid == '') {
                        $total_paid_html = '<span class="display_currency total-paid" data-currency_symbol="true" data-orig-value="0.00">' . $this->productUtil->num_f(0, false, $business_details, false) . '</span>';
                    } else {
                        $total_paid_html = '<span class="display_currency total-paid" data-currency_symbol="true" data-orig-value="' . $row->total_paid . '">' . $this->productUtil->num_f($row->total_paid, false, $business_details, false) . '</span>';
                    }
                    return $total_paid_html;
                })
                ->editColumn('transaction_date', '{{@format_date($transaction_date)}}')
                ->editColumn('paid_on', '{{@format_date($paid_on)}}')
                ->editColumn('method', function ($row) {
                    if ($row->method == 'bank_transfer') {
                        return 'Bank';
                    }
                    if ($row->method == 'card') {
                        if (!empty($row->card_number)) {
                            $htm = '<span class="" >Card <small>' . $row->card_number . '</small></span>';
                            return $htm;
                        }
                    }
                    if ($row->method == 'cheque') {
                        $html = '<span class="" >Cheque <small>' . $row->bank_name . '</small><small> ' . $row->cheque_number . '</small> <small>' . $row->cheque_date . '</small></span>';
                        return $html;
                    }
                    return ucfirst($row->method);
                })
                ->editColumn('cheque_number', function ($row) {
                    if ($row->method == 'bank_transfer' || $row->method == 'cheque') {
                        return $row->cheque_number;
                    }
                    if ($row->method == 'card') {
                        return $row->card_number;
                    }
                    return '';
                })
                ->editColumn('invoice_no', function ($row) {
                    $invoice_no = $row->invoice_no;
                    if (!empty($row->woocommerce_order_id)) {
                        $invoice_no .= ' <i class="fa fa-wordpress text-primary no-print" title="' . __('lang_v1.synced_from_woocommerce') . '"></i>';
                    }
                    if (!empty($row->return_exists)) {
                        $invoice_no .= ' &nbsp;<small class="label bg-red label-round no-print" title="' . __('lang_v1.some_qty_returned_from_sell') . '"><i class="fa fa-undo"></i></small>';
                    }
                    if (!empty($row->is_recurring)) {
                        $invoice_no .= ' &nbsp;<small class="label bg-red label-round no-print" title="' . __('lang_v1.subscribed_invoice') . '"><i class="fa fa-recycle"></i></small>';
                    }
                    if (!empty($row->recur_parent_id)) {
                        $invoice_no .= ' &nbsp;<small class="label bg-info label-round no-print" title="' . __('lang_v1.subscription_invoice') . '"><i class="fa fa-recycle"></i></small>';
                    }
                    return $invoice_no;
                })
                ->addColumn('paid_in_type', function ($row) use ($paid_in_types) {
                    if (!empty($row->paid_in_type) && !empty($paid_in_types[$row->paid_in_type])) {
                        return $paid_in_types[$row->paid_in_type];
                    }
                    return '';
                })
                ->setRowAttr([
                    'data-href' => function ($row) {
                        if (auth()->user()->can("sell.view") || auth()->user()->can("view_own_sell_only")) {
                            return action('SellController@show', [$row->id]);
                        } else {
                            return '';
                        }
                    }
                ]);
            $rawColumns = ['name', 'method', 'final_total', 'action', 'total_paid', 'total_remaining', 'payment_status', 'invoice_no', 'discount_amount', 'tax_amount', 'total_before_tax', 'shipping_status', 'payment_amount'];
            return $datatable->rawColumns($rawColumns)
                ->make(true);
        }
        $business_id = request()->session()->get('business.id');
        $customers = Contact::customersDropdown($business_id, false);
        $business_locations = BusinessLocation::forDropdown($business_id);
        $payment_types = $this->transactionUtil->payment_types();
        $package_manage = Package::where('only_for_business', $business_id)->first();
        $customer_interest_deduct_option = $business_details->customer_interest_deduct_option;
        return view('customer_payments.index')->with(compact(
            'customers',
            'business_locations',
            'payment_types',
            'customer_interest_deduct_option',
            'latest_ref_number',
            'latest_ref_number_PP',
            'latest_ref_number_CPB',
            'latest_ref_number_CPS'
        ));
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }
    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }
    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }
    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
