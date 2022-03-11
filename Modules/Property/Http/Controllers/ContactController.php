<?php

namespace Modules\Property\Http\Controllers;

use App\Account;
use App\Media;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

use App\BusinessLocation;
use App\Contact;
use App\Customer;
use App\ContactGroup;
use App\ContactLedger;
use App\CustomerReference;
use App\System;
use App\Transaction;
use App\TransactionPayment;
use App\User;
use App\Utils\ModuleUtil;
use App\Utils\BusinessUtil;
use App\Utils\ProductUtil;
use App\Utils\TransactionUtil;
use App\Utils\Util;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Modules\Property\Entities\Property;
use Yajra\DataTables\Facades\DataTables;

class ContactController extends Controller
{
    protected $commonUtil;
    protected $transactionUtil;
    protected $moduleUtil;
    protected $businessUtil;
    protected $productUtil;

    /**
     * Constructor
     *
     * @param Util $commonUtil
     * @return void
     */
    public function __construct(
        Util $commonUtil,
        ModuleUtil $moduleUtil,
        TransactionUtil $transactionUtil,
        BusinessUtil $businessUtil,
        ProductUtil $productUtil
    ) {
        $this->commonUtil = $commonUtil;
        $this->moduleUtil = $moduleUtil;
        $this->businessUtil = $businessUtil;
        $this->transactionUtil = $transactionUtil;
        $this->productUtil = $productUtil;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        
        $type = request()->get('type');

        $types = ['supplier', 'customer'];

        if (empty($type) || !in_array($type, $types)) {
            return redirect()->back();
        }

        if (request()->ajax()) {
            if ($type == 'supplier') {
                return $this->indexSupplier();
            } elseif ($type == 'customer') {
                return $this->indexCustomer();
            } else {
                die("Not Found");
            }
        }

        $reward_enabled = (request()->session()->get('business.enable_rp') == 1 && in_array($type, ['customer'])) ? true : false;

        return view('property::contact.index')
            ->with(compact('type', 'reward_enabled'));
    }

    /**
     * Returns the database object for supplier
     *
     * @return \Illuminate\Http\Response
     */
    private function indexSupplier()
    {
        if (!auth()->user()->can('supplier.view')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');

        $contact = Contact::leftjoin('transactions AS t', 'contacts.id', '=', 't.contact_id')
            ->leftjoin('contact_groups AS cg', 'contacts.supplier_group_id', '=', 'cg.id')
            ->where('contacts.business_id', $business_id)
            ->where('contacts.is_property', 1)
            ->onlySuppliers()
            ->select([
                'contacts.contact_id', 'supplier_business_name', 'contacts.active', 'contacts.name', 'cg.name as supplier_group', 'contacts.created_at', 'mobile',
                'contacts.type', 'contacts.id',
                DB::raw("SUM(IF(t.type = 'purchase', final_total, 0)) as total_purchase"),
                DB::raw("SUM(IF(t.type = 'purchase', (SELECT SUM(amount) FROM transaction_payments WHERE transaction_payments.transaction_id=t.id), 0)) as purchase_paid"),
                DB::raw("SUM(IF(t.type = 'purchase_return', final_total, 0)) as total_purchase_return"),
                DB::raw("SUM(IF(t.type = 'purchase_return', (SELECT SUM(amount) FROM transaction_payments WHERE transaction_payments.transaction_id=t.id), 0)) as purchase_return_paid"),
                DB::raw("SUM(IF(t.type = 'opening_balance', final_total, 0)) as opening_balance"),
                DB::raw("SUM(IF(t.type = 'opening_balance', (SELECT SUM(IF(is_return = 1,-1*amount,amount)) FROM transaction_payments WHERE transaction_payments.transaction_id=t.id), 0)) as opening_balance_paid"),
                'email', 'tax_number', 'contacts.pay_term_number', 'contacts.pay_term_type', 'contacts.custom_field1', 'contacts.custom_field2', 'contacts.custom_field3', 'contacts.custom_field4'
            ])
            ->groupBy('contacts.id');

        return Datatables::of($contact)
            ->addColumn(
                'due',
                '<span class="display_currency contact_due" data-orig-value="{{$total_purchase - $purchase_paid}}" data-currency_symbol=true data-highlight=false>{{$total_purchase - $purchase_paid }}</span>'
            )
            ->addColumn(
                'return_due',
                '<span class="display_currency return_due" data-orig-value="{{$total_purchase_return - $purchase_return_paid}}" data-currency_symbol=true data-highlight=false>{{$total_purchase_return - $purchase_return_paid }}</span>'
            )
            ->addColumn(
                'action',
                '<div class="btn-group">
                    <button type="button" class="btn btn-info dropdown-toggle btn-xs" 
                        data-toggle="dropdown" aria-expanded="false">' .
                    __("messages.actions") .
                    '<span class="caret"></span><span class="sr-only">Toggle Dropdown
                        </span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-left" role="menu">
                @if(($total_purchase + $opening_balance - $purchase_paid - $opening_balance_paid)  > 0)
                    <li><a href="{{action(\'TransactionPaymentController@getPayContactDue\', [$id])}}?type=purchase" class="pay_purchase_due"><i class="fa fa-credit-card" aria-hidden="true"></i>@lang("contact.pay_due_amount")</a></li>
                @endif
                @if(($total_purchase_return - $purchase_return_paid)  > 0)
                    <li><a href="{{action(\'TransactionPaymentController@getPayContactDue\', [$id])}}?type=purchase_return" class="pay_purchase_due"><i class="fa fa-credit-card" aria-hidden="true"></i>@lang("lang_v1.receive_purchase_return_due")</a></li>
                @endif
                @if(($total_purchase + $opening_balance - $purchase_paid - $opening_balance_paid)  <= 0)
                    <li><a href="{{action(\'TransactionPaymentController@getAdvancePayment\', [$id])}}?type=advance_payment" class="pay_purchase_due"><i class="fa fa-money" aria-hidden="true"></i>@lang("lang_v1.advance_payment")</a></li>
                @endif
                <li><a href="{{action(\'TransactionPaymentController@getSecurityDeposit\', [$id])}}?type=security_deposit" class="pay_purchase_due"><i class="fa fa-shield" aria-hidden="true"></i>@lang("lang_v1.security_deposit")</a></li>
                @can("supplier.view")
                    <li><a href="{{action(\'\Modules\Property\Http\Controllers\ContactController@show\', [$id])}}"><i class="fa fa-eye" aria-hidden="true"></i> @lang("messages.view")</a></li>
                @endcan
                @can("supplier.update")
                    <li><a href="{{action(\'\Modules\Property\Http\Controllers\ContactController@edit\', [$id])}}" class="edit_contact_button"><i class="fa fa-pencil-square-o "></i> @lang("messages.edit")</a></li>
                @endcan
                @can("supplier.delete")
                    <li><a href="{{action(\'\Modules\Property\Http\Controllers\ContactController@destroy\', [$id])}}" class="delete_contact_button"><i class="fa fa-trash"></i> @lang("messages.delete")</a></li>
                @endcan
                @can("supplier.view")
                    <li class="divider"></li>
                    <li>
                        <a href="{{action(\'\Modules\Property\Http\Controllers\ContactController@show\', [$id])."?view=contact_info"}}">
                            <i class="fa fa-user" aria-hidden="true"></i>
                            @lang("contact.contact_info", ["contact" => __("contact.contact") ])
                        </a>
                    </li>
                    <li>
                        <a href="{{action(\'\Modules\Property\Http\Controllers\ContactController@show\', [$id])."?view=ledger"}}">
                            <i class="fa fa-anchor" aria-hidden="true"></i>
                            @lang("lang_v1.ledger")
                        </a>
                    </li>
                    @if(in_array($type, ["both", "supplier"]))
                        <li>
                            <a href="{{action(\'\Modules\Property\Http\Controllers\ContactController@show\', [$id])."?view=purchase"}}">
                                <i class="fa fa-arrow-circle-down" aria-hidden="true"></i>
                                @lang("purchase.purchases")
                            </a>
                        </li>
                        <li>
                            <a href="{{action(\'\Modules\Property\Http\Controllers\ContactController@show\', [$id])."?view=stock_report"}}">
                                <i class="fa fa-hourglass-half" aria-hidden="true"></i>
                                @lang("report.stock_report")
                            </a>
                        </li>
                    @endif
                    @if(in_array($type, ["both", "customer"]))
                        <li>
                            <a href="{{action(\'\Modules\Property\Http\Controllers\ContactController@show\', [$id])."?view=sales"}}">
                                <i class="fa fa-arrow-circle-up" aria-hidden="true"></i>
                                @lang("sale.sells")
                            </a>
                        </li>
                    @endif
                    <li>
                        <a href="{{action(\'\Modules\Property\Http\Controllers\ContactController@show\', [$id])."?view=documents_and_notes"}}">
                            <i class="fa fa-paperclip" aria-hidden="true"></i>
                             @lang("lang_v1.documents_and_notes")
                        </a>
                    </li>
                    <li>
                        <a href="{{action(\'ContactController@toggleActivate\', [$id])}}">
                        @if($active)
                            <i class="fa fa-times" aria-hidden="true"></i>
                            @lang("lang_v1.deactivate")
                        @else
                            <i class="fa fa-check" aria-hidden="true"></i>
                            @lang("lang_v1.activate")
                        @endif
                        </a>
                    </li>
                @endcan
                </ul></div>'
            )
            ->editColumn('opening_balance', function ($row) {
                $paid_opening_balance = !empty($row->opening_balance_paid) ? $row->opening_balance_paid : 0;
                $opening_balance = !empty($row->opening_balance) ? $row->opening_balance : 0;
                $balance_value = $opening_balance - ($paid_opening_balance);
                $html = '<span class="display_currency ob" data-currency_symbol="true" data-orig-value="' . $balance_value . '">' . $balance_value . '</span>';

                return $html;
            })
            ->addColumn('mass_delete', function ($row) {
                return  '<input type="checkbox" class="row-select" value="' . $row->id . '">';
            })
            ->editColumn('pay_term', '
                @if(!empty($pay_term_type) && !empty($pay_term_number))
                    {{$pay_term_number}}
                    @lang("lang_v1.".$pay_term_type)
                @endif
            ')
            ->editColumn('created_at', '{{@format_date($created_at)}}')
            ->removeColumn('opening_balance_paid')
            ->removeColumn('type')
            ->removeColumn('id')
            ->removeColumn('total_purchase')
            ->removeColumn('purchase_paid')
            ->removeColumn('total_purchase_return')
            ->removeColumn('purchase_return_paid')
            ->rawColumns(['action', 'opening_balance', 'pay_term', 'due', 'return_due', 'mass_delete'])
            ->make(true);
    }

    /**
     * Returns the database object for customer
     *
     * @return \Illuminate\Http\Response
     */
    private function indexCustomer()
    {
        if (!auth()->user()->can('property.customer.view')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');

        $query = Contact::leftjoin('transactions AS t', 'contacts.id', '=', 't.contact_id')
            ->leftjoin('contact_groups AS cg', 'contacts.customer_group_id', '=', 'cg.id')
            ->where('contacts.business_id', $business_id)
            ->where('contacts.is_property', 1)
            ->onlyCustomers()
            ->select([
                'contacts.contact_id', 'contacts.name', 'contacts.created_at', 'contacts.active', 'total_rp', 'cg.name as customer_group', 'city', 'state', 'country', 'contacts.nic_number', 'landmark', 'mobile', 'contacts.id', 'is_default',
                DB::raw("SUM(IF(t.type = 'sell' AND t.status = 'final', final_total, 0)) as total_invoice"),
                DB::raw("SUM(IF(t.type = 'sell' AND t.status = 'final', (SELECT SUM(IF(is_return = 1,-1*amount,amount)) FROM transaction_payments WHERE transaction_payments.transaction_id=t.id), 0)) as invoice_received"),
                DB::raw("SUM(IF(t.type = 'sell_return', final_total, 0)) as total_sell_return"),
                DB::raw("SUM(IF(t.type = 'sell_return', (SELECT SUM(amount) FROM transaction_payments WHERE transaction_payments.transaction_id=t.id), 0)) as sell_return_paid"),
                DB::raw("SUM(IF(t.type = 'opening_balance', final_total, 0)) as opening_balance"),
                DB::raw("SUM(IF(t.type = 'advance_payment', -1*final_total, 0)) as advance_payment"),
                DB::raw("SUM(IF(t.type = 'opening_balance', (SELECT SUM(IF(is_return = 1,-1*amount,amount)) FROM transaction_payments WHERE transaction_payments.transaction_id=t.id), 0)) as opening_balance_paid"),
                'email', 'tax_number', 'contacts.pay_term_number', 'contacts.pay_term_type', 'contacts.credit_limit', 'contacts.custom_field1', 'contacts.custom_field2', 'contacts.custom_field3', 'contacts.custom_field4', 'contacts.type', 'contacts.image', 'contacts.signature'
            ])
            ->groupBy('contacts.id');

        $contacts = Datatables::of($query)
            ->addColumn('address', '{{implode(array_filter([$landmark, $city, $state, $country]), ", ")}}')
            ->addColumn(
                'due',
                '<span class="display_currency contact_due" data-orig-value="{{$total_invoice - $invoice_received + $advance_payment}}" data-currency_symbol=true data-highlight=true>{{($total_invoice - $invoice_received + $advance_payment)}}</span>'
            )
            ->addColumn(
                'return_due',
                '<span class="display_currency return_due" data-orig-value="{{$total_sell_return - $sell_return_paid}}" data-currency_symbol=true data-highlight=false>{{$total_sell_return - $sell_return_paid }}</span>'
            )
            ->addColumn(
                'action',
                '<div class="btn-group">
                    <button type="button" class="btn btn-info dropdown-toggle btn-xs" 
                        data-toggle="dropdown" aria-expanded="false">' .
                    __("messages.actions") .
                    '<span class="caret"></span><span class="sr-only">Toggle Dropdown
                        </span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-left" role="menu">
                @if(($total_invoice + $opening_balance - $invoice_received - $opening_balance_paid)  > 0)
                    <li><a href="{{action(\'TransactionPaymentController@getPayContactDue\', [$id])}}?type=sell" class="pay_sale_due"><i class="fa fa-credit-card" aria-hidden="true"></i>@lang("contact.pay_due_amount")</a></li>
                @endif
                @if(($total_sell_return - $sell_return_paid)  > 0)
                    <li><a href="{{action(\'TransactionPaymentController@getPayContactDue\', [$id])}}?type=sell_return" class="pay_purchase_due"><i class="fa fa-credit-card" aria-hidden="true"></i>@lang("lang_v1.pay_sell_return_due")</a></li>
                @endif
                @if(($total_invoice + $opening_balance - $invoice_received - $opening_balance_paid)  <= 0)
                <li><a href="{{action(\'TransactionPaymentController@getAdvancePayment\', [$id])}}?type=advance_payment" class="pay_purchase_due"><i class="fa fa-money" aria-hidden="true"></i>@lang("lang_v1.advance_payment")</a></li>
                @endif
                <li><a href="{{action(\'TransactionPaymentController@getSecurityDeposit\', [$id])}}?type=security_deposit" class="pay_purchase_due"><i class="fa fa-shield" aria-hidden="true"></i>@lang("lang_v1.security_deposit")</a></li>
                <li><a href="{{action(\'TransactionPaymentController@getRefundPayment\', [$id])}}?type=refund_payment" class="pay_purchase_due"><i class="fa fa-recycle" aria-hidden="true"></i>@lang("lang_v1.refund_cheque_return")</a></li>
              
                @can("property.customer.view")
                    <li><a href="{{action(\'\Modules\Property\Http\Controllers\ContactController@show\', [$id])}}"><i class="fa fa-eye" aria-hidden="true"></i> @lang("messages.view")</a></li>
                @endcan
                @can("property.customer.edit")
                    <li><a href="{{action(\'\Modules\Property\Http\Controllers\ContactController@edit\', [$id])}}" class="edit_contact_button"><i class="fa fa-pencil-square-o"></i> @lang("messages.edit")</a></li>
                @endcan
                @if(!$is_default)
                @can("property.customer.delete")
                    <li><a href="{{action(\'\Modules\Property\Http\Controllers\ContactController@destroy\', [$id])}}" class="delete_contact_button"><i class="fa fa-trash"></i> @lang("messages.delete")</a></li>
                @endcan
                @endif
                @can("property.customer.view")
                    <li class="divider"></li>
                    <li>
                        <a href="{{action(\'\Modules\Property\Http\Controllers\ContactController@show\', [$id])."?view=contact_info"}}">
                            <i class="fa fa-user" aria-hidden="true"></i>
                            @lang("contact.contact_info", ["contact" => __("contact.contact") ])
                        </a>
                    </li>
                    <li>
                        <a href="{{action(\'\Modules\Property\Http\Controllers\ContactController@show\', [$id])."?view=ledger"}}">
                            <i class="fa fa-anchor" aria-hidden="true"></i>
                            @lang("lang_v1.ledger")
                        </a>
                    </li>
                    @if(in_array($type, ["both", "supplier"]))
                        <li>
                            <a href="{{action(\'\Modules\Property\Http\Controllers\ContactController@show\', [$id])."?view=purchase"}}">
                                <i class="fa fa-arrow-circle-down" aria-hidden="true"></i>
                                @lang("purchase.purchases")
                            </a>
                        </li>
                        <li>
                            <a href="{{action(\'\Modules\Property\Http\Controllers\ContactController@show\', [$id])."?view=stock_report"}}">
                                <i class="fa fa-hourglass-half" aria-hidden="true"></i>
                                @lang("report.stock_report")
                            </a>
                        </li>
                    @endif
                    @if(in_array($type, ["both", "customer"]))
                        <li>
                            <a href="{{action(\'\Modules\Property\Http\Controllers\ContactController@show\', [$id])."?view=sales"}}">
                                <i class="fa fa-arrow-circle-up" aria-hidden="true"></i>
                                @lang("property::lang.properties_purchased")
                            </a>
                        </li>
                    @endif
                    <li>
                        <a href="{{action(\'\Modules\Property\Http\Controllers\ContactController@show\', [$id])."?view=documents_and_notes"}}">
                            <i class="fa fa-paperclip" aria-hidden="true"></i>
                             @lang("lang_v1.documents_and_notes")
                        </a>
                    </li>
                    <li>
                        <a href="{{action(\'ContactController@toggleActivate\', [$id])}}">
                        @if($active)
                            <i class="fa fa-times" aria-hidden="true"></i>
                            @lang("lang_v1.deactivate")
                        @else
                            <i class="fa fa-check" aria-hidden="true"></i>
                            @lang("lang_v1.activate")
                        @endif
                        </a>
                    </li>
                @endcan
                </ul></div>'
            )
            ->editColumn('opening_balance', function ($row) {
                $paid_opening_balance = !empty($row->opening_balance_paid) ? $row->opening_balance_paid : 0;
                $opening_balance = !empty($row->opening_balance) ? $row->opening_balance : 0;
                $balance_value = $opening_balance - ($paid_opening_balance);
                $html = '<span class="display_currency ob" data-currency_symbol="true" data-orig-value="' . $balance_value . '">' . $balance_value . '</span>';

                return $html;
            })
            ->editColumn('credit_limit', function ($row) {
                $html = __('lang_v1.no_limit');
                if (!is_null($row->credit_limit)) {
                    $html = '<span class="display_currency" data-currency_symbol="true" data-orig-value="' . $row->credit_limit . '">' . $row->credit_limit . '</span>';
                }

                return $html;
            })
            ->addColumn('mass_delete', function ($row) {
                return  '<input type="checkbox" class="row-select" value="' . $row->id . '">';
            })
            ->addColumn('image', function ($row) {
                if(isset($row->image) && $row->image!=null ){
                    $image = asset('uploads/media/'.$row->image);
                     return  '<img class="popup" src="'.$image.'" height="50" width="50" >';
                 }else{
                    return '';
                 }
               
            })
            ->addColumn('signature', function ($row) {
                if(isset($row->signature) && $row->signature!=null ){
                    $signature = asset('uploads/media/'.$row->signature);
                     return  '<img class="popup" src="'.$signature.'" height="50" width="50" >';
                 }else{
                    return '';
                 }
               
            })
            ->editColumn('pay_term', '
                @if(!empty($pay_term_type) && !empty($pay_term_number))
                    {{$pay_term_number}}
                    @lang("lang_v1.".$pay_term_type)
                @endif
            ')
            ->editColumn('total_rp', '{{$total_rp ?? 0}}')
            ->editColumn('created_at', '{{@format_date($created_at)}}')
            ->removeColumn('total_invoice')
            ->removeColumn('opening_balance_paid')
            ->removeColumn('invoice_received')
            ->removeColumn('state')
            ->removeColumn('country')
            ->removeColumn('city')
            ->removeColumn('type')
            ->removeColumn('id')
            ->removeColumn('is_default')
            ->removeColumn('total_sell_return')
            ->removeColumn('sell_return_paid')
            ->filterColumn('address', function ($query, $keyword) {
                $query->whereRaw("CONCAT(COALESCE(landmark, ''), ', ', COALESCE(city, ''), ', ', COALESCE(state, ''), ', ', COALESCE(country, '') ) like ?", ["%{$keyword}%"]);
            });
        $reward_enabled = (request()->session()->get('business.enable_rp') == 1) ? true : false;
        if (!$reward_enabled) {
            $contacts->removeColumn('total_rp');
        }
        return $contacts->rawColumns(['action', 'opening_balance', 'credit_limit', 'pay_term', 'due', 'return_due', 'mass_delete', 'image', 'signature'])
            ->make(true);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (!auth()->user()->can('supplier.create') && !auth()->user()->can('property.customer.create')) {
            abort(403, 'Unauthorized action.');
        }
        $type = request()->type;
        $business_id = request()->session()->get('user.business_id');

        //Check if subscribed or not
        if (!$this->moduleUtil->isSubscribed($business_id)) {
            return $this->moduleUtil->expiredResponse();
        }

        $types = [];
        if (auth()->user()->can('supplier.create')) {
            $types['supplier'] = __('report.supplier');
        }
        if (auth()->user()->can('property.customer.create')) {
            $types['customer'] = __('report.customer');
        }
        if (auth()->user()->can('supplier.create') && auth()->user()->can('property.customer.create')) {
            $types['both'] = __('lang_v1.both_supplier_customer');
        }

        $customer_groups = ContactGroup::forDropdown($business_id);
        $supplier_groups = ContactGroup::forDropdown($business_id, true, false, 'supplier');

        $contact_id = $this->businessUtil->check_customer_code($business_id);


        return view('property::contact.create')
            ->with(compact('types', 'customer_groups', 'supplier_groups', 'contact_id', 'type'));
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (!auth()->user()->can('supplier.create') && !auth()->user()->can('property.customer.create')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $business_id = $request->session()->get('user.business_id');

            if (!$this->moduleUtil->isSubscribed($business_id)) {
                return $this->moduleUtil->expiredResponse();
            }
            if ($request->type == 'customer') {
                if (!$this->moduleUtil->isQuotaAvailable('customers', $business_id)) {
                    return $this->moduleUtil->quotaExpiredResponse('customers', $business_id, action('ContactController@index'));
                }
                $validator = Validator::make($request->all(), [
                    'password' => 'required|min:4|max:255',
                    'confirm_password' => 'required|same:password',

                ]);

                if ($validator->fails()) {
                    $output = [
                        'success' => false,
                        'msg' => 'Password does not match'
                    ];
                    return $output;
                }

                $customer_data = array(
                    'business_id' => $business_id,
                    'first_name' => $request->name,
                    'last_name' => '',
                    'email' => $request->email,
                    'username' => $request->contact_id,
                    'password' => Hash::make($request->password),
                    'mobile' => $request->mobile,
                    'nic_number' => $request->nic_number,
                    'contact_number' => $request->alternate_number,
                    'landline' => $request->landline,
                    'geo_location' => '',
                    'address' => $request->city,
                    'town' => $request->state,
                    'district' => $request->country,
                    'is_company_customer' => 1,
                );

                Customer::create($customer_data);
            }

            $input = $request->only([
                'type', 'supplier_business_name',
                'name', 'nic_number', 'tax_number', 'pay_term_number', 'pay_term_type', 'mobile', 'landline', 'alternate_number', 'city', 'state', 'country', 'landmark', 'customer_group_id', 'supplier_group_id', 'contact_id', 'custom_field1', 'custom_field2', 'custom_field3', 'custom_field4', 'email','image','signature'
            ]);
            $input['business_id'] = $business_id;
            $input['is_property'] = 1;
            $input['created_by'] = $request->session()->get('user.id');

            $input['credit_limit'] = $request->input('credit_limit') != '' ? $this->commonUtil->num_uf($request->input('credit_limit')) : null;
            if ($request->transaction_date && $request->type == 'supplier') {
                $input['created_at'] = date('Y-m-d H:i:s', strtotime($request->transaction_date));
            }
            //Check Contact id
            $count = 0;
            if (!empty($input['contact_id'])) {
                $count = Contact::where('business_id', $input['business_id'])
                    ->where('contact_id', $input['contact_id'])
                    ->count();
            }

            if ($count == 0) {
                //Update reference count
                $ref_count = $this->commonUtil->setAndGetReferenceCount('contacts');

                if (empty($input['contact_id'])) {
                    //Generate reference number
                    $input['contact_id'] = $this->commonUtil->generateReferenceNumber('contacts', $ref_count);
                }


                $contact = Contact::create($input);

                //Add opening balance
                if (!empty($request->input('opening_balance'))) {
                    $this->transactionUtil->createOpeningBalanceTransaction($business_id, $contact->id, $request->input('opening_balance'), $request->transaction_date);
                }

                $output = [
                    'success' => true,
                    'data' => $contact,
                    'msg' => __("contact.added_success")
                ];
            } else {
                throw new \Exception("Error Processing Request", 1);
            }
        } catch (\Exception $e) {
            Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

            $output = [
                'success' => false,
                'msg' => __("messages.something_went_wrong")
            ];
        }

        return $output;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if (!auth()->user()->can('supplier.view') && !auth()->user()->can('property.customer.view')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');
        $contact = Contact::where('contacts.id', $id)
            ->where('contacts.business_id', $business_id)
            ->join('transactions AS t', 'contacts.id', '=', 't.contact_id')
            ->with(['business'])
            ->select(
                DB::raw("SUM(IF(t.type = 'purchase', final_total, 0)) as total_purchase"),
                DB::raw("SUM(IF(t.type = 'sell' AND t.status = 'final', final_total, 0)) as total_invoice"),
                DB::raw("SUM(IF(t.type = 'purchase', (SELECT SUM(amount) FROM transaction_payments WHERE transaction_payments.transaction_id=t.id), 0)) as purchase_paid"),
                DB::raw("SUM(IF(t.type = 'sell' AND t.status = 'final', (SELECT SUM(IF(is_return = 1,-1*amount,amount)) FROM transaction_payments WHERE transaction_payments.transaction_id=t.id), 0)) as invoice_received"),
                DB::raw("SUM(IF(t.type = 'opening_balance', final_total, 0)) as opening_balance"),
                DB::raw("SUM(IF(t.type = 'opening_balance', (SELECT SUM(amount) FROM transaction_payments WHERE transaction_payments.transaction_id=t.id), 0)) as opening_balance_paid"),
                'contacts.*'
            )->first();

        $reward_enabled = (request()->session()->get('business.enable_rp') == 1 && in_array($contact->type, ['customer', 'both'])) ? true : false;

        $contact_dropdown = Contact::contactDropdown($business_id, false, false);

        $references = CustomerReference::where('business_id', $business_id)->where('contact_id', $id)->pluck('reference', 'reference');

        $business_locations = BusinessLocation::forDropdown($business_id, true);

        //get contact view type : ledger, notes etc.
        $view_type = request()->get('view');
        if (is_null($view_type)) {
            $view_type = 'contact_info';
        }

        $transaction_amounts = ContactLedger::where('contact_id', $id)->distinct('amount')->pluck('amount', 'amount');

        $blocks = Property::getLandAndBlockDropdown($business_id, true, true);
        $properties = Property::where('business_id', $business_id)->pluck('name', 'id');

        return view('property::contact.show')
            ->with(compact(
                'contact',
                'reward_enabled',
                'contact_dropdown',
                'business_locations',
                'view_type',
                'references',
                'transaction_amounts',
                'blocks',
                'properties'
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
        if (!auth()->user()->can('supplier.update') && !auth()->user()->can('property.customer.edit')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            $business_id = request()->session()->get('user.business_id');
            $contact = Contact::where('business_id', $business_id)->find($id);

            if (!$this->moduleUtil->isSubscribed($business_id)) {
                return $this->moduleUtil->expiredResponse();
            }



            $types = [];
            if (auth()->user()->can('supplier.create')) {
                $types['supplier'] = __('report.supplier');
            }
            if (auth()->user()->can('property.customer.create')) {
                $types['customer'] = __('report.customer');
            }
            if (auth()->user()->can('supplier.create') && auth()->user()->can('property.customer.create')) {
                $types['both'] = __('lang_v1.both_supplier_customer');
            }

            $customer_groups = ContactGroup::forDropdown($business_id);
            $supplier_groups = ContactGroup::forDropdown($business_id, true, false, 'supplier');

            $ob_transaction =  Transaction::where('contact_id', $id)
                ->where('type', 'opening_balance')
                ->first();
            $opening_balance = !empty($ob_transaction->final_total) ? $ob_transaction->final_total : 0;

            //Deduct paid amount from opening balance.
            if (!empty($opening_balance)) {
                $opening_balance_paid = $this->transactionUtil->getTotalAmountPaid($ob_transaction->id);
                if (!empty($opening_balance_paid)) {
                    $opening_balance = $opening_balance - $opening_balance_paid;
                }

                $opening_balance = $this->commonUtil->num_f($ob_transaction->final_total);
            }

            return view('property::contact.edit')
                ->with(compact('contact', 'types', 'customer_groups', 'supplier_groups', 'opening_balance', 'ob_transaction'));
        }
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
        if (!auth()->user()->can('supplier.update') && !auth()->user()->can('property.customer.edit')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            try {
                $input = $request->only(['contact_id', 'type', 'supplier_business_name', 'name', 'tax_number', 'pay_term_number', 'pay_term_type', 'mobile', 'landline', 'alternate_number', 'city', 'state', 'country', 'landmark', 'customer_group_id', 'supplier_group_id', 'custom_field1', 'custom_field2', 'custom_field3', 'custom_field4', 'email','image','signature']);

                $input['credit_limit'] = $request->input('credit_limit') != '' ? $this->commonUtil->num_uf($request->input('credit_limit')) : null;

                $business_id = $request->session()->get('user.business_id');
                $input['is_property'] = 1;
                if (!$this->moduleUtil->isSubscribed($business_id)) {
                    return $this->moduleUtil->expiredResponse();
                }

                $contact_user = User::where('username', $input['contact_id'])->first();
                if (request()->type == 'customer') {
                    if (!empty(request()->password)) {
                        $validator = Validator::make(request()->all(), [
                            'password' => 'required|min:4|max:255',
                            'confirm_password' => 'required|same:password',
                            'image' => 'required|mimes:jpeg,png,jpg,gif,svg|max:2048',
                            'signature' => 'required|mimes:jpeg,png,jpg,gif,svg|max:2048',
                        ]);

                        if ($validator->fails()) {
                            $output = [
                                'success' => false,
                                'msg' => 'something went wrong try again'
                            ];
                            return $output;
                        }
                    }
                    if (empty($contact_user)) {
                        if (!$this->moduleUtil->isQuotaAvailable('customers', $business_id)) {
                            return $this->moduleUtil->quotaExpiredResponse('customers', $business_id, action('ContactController@index'));
                        }


                        $customer_details = request()->only(['email', 'password']);
                        $customer_details['language'] = env('APP_LOCALE');
                        $customer_details['surname'] = '';
                        $customer_details['first_name'] = request()->name;
                        $customer_details['last_name'] = '';
                        $customer_details['username'] = request()->contact_id;
                        $customer_details['is_customer'] = 1;
                        $customer_details['business_id'] = $business_id;



                        $user = User::create_user($customer_details);
                        $user->business_id = $business_id;
                        $user->is_customer = 1;

                        $enable_customer_login = System::getProperty('enable_customer_login');
                        if (!$enable_customer_login) {
                            $user->status = 'inactive';
                        }
                        $user->save();
                    } else {
                        $contact_user->first_name = request()->name;
                        if (!empty(request()->password)) {
                            $contact_user->password = Hash::make(request()->password);
                        }
                        $contact_user->save();
                    }
                }

                $count = 0;

                //Check Contact id
                if (!empty($input['contact_id'])) {
                    $count = Contact::where('business_id', $business_id)
                        ->where('contact_id', $input['contact_id'])
                        ->where('id', '!=', $id)
                        ->count();
                }

                if ($count == 0) {
                    $contact = Contact::where('business_id', $business_id)->findOrFail($id);
                    foreach ($input as $key => $value) {
                        $contact->$key = $value;
                    }
                    if($request->hasFile('image')){
                        $imageName = Media::uploadFile($request->file('image'));
                        $contact->image=$imageName;
                    }if($request->hasFile('signature')) {
                        $signatureName = Media::uploadFile($request->file('signature'));
                        $contact->signature = $signatureName;
                    }
                    $contact->save();

                    //Get opening balance if exists
                    $ob_transaction =  Transaction::where('contact_id', $id)
                        ->where('type', 'opening_balance')
                        ->first();

                    if (!empty($ob_transaction)) {
                        $amount = $this->commonUtil->num_uf($request->input('opening_balance'));
                        $opening_balance_paid = $this->transactionUtil->getTotalAmountPaid($ob_transaction->id);
                        if (!empty($opening_balance_paid)) {
                            $amount += $opening_balance_paid;
                        }

                        $ob_transaction->final_total = $amount;
                        $ob_transaction->transaction_date = Carbon::parse($request->transaction_date)->format('Y-m-d');
                        $ob_transaction->save();
                        //Update opening balance payment status
                        $this->transactionUtil->updatePaymentStatus($ob_transaction->id, $ob_transaction->final_total);
                    } else {
                        //Add opening balance
                        if (!empty($request->input('opening_balance'))) {
                            $this->transactionUtil->createOpeningBalanceTransaction($business_id, $contact->id, $request->input('opening_balance'));
                        }
                    }

                    $output = [
                        'success' => true,
                        'msg' => __("contact.updated_success")
                    ];
                } else {
                    throw new \Exception("Error Processing Request", 1);
                }
            } catch (\Exception $e) {
                Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

                $output = [
                    'success' => false,
                    'msg' => __("messages.something_went_wrong")
                ];
            }

            return $output;
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (!auth()->user()->can('supplier.delete') && !auth()->user()->can('property.customer.delete')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            try {
                $business_id = request()->user()->business_id;

                //Check if any transaction related to this contact exists
                $count = Transaction::where('business_id', $business_id)
                    ->where('contact_id', $id)
                    ->count();
                if ($count == 0) {
                    $contact = Contact::where('business_id', $business_id)->findOrFail($id);
                    if (!$contact->is_default) {
                        $contact->delete();
                    }
                    $output = [
                        'success' => true,
                        'msg' => __("contact.deleted_success")
                    ];
                } else {
                    $output = [
                        'success' => false,
                        'msg' => __("lang_v1.you_cannot_delete_this_contact")
                    ];
                }
            } catch (\Exception $e) {
                Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

                $output = [
                    'success' => false,
                    'msg' => __("messages.something_went_wrong")
                ];
            }

            return $output;
        }
    }

    /**
     * Mass deletes contact.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function massDestroy(Request $request)
    {
        if (!auth()->user()->can('product.delete')) {
            abort(403, 'Unauthorized action.');
        }
        try {
            $purchase_exist = false;

            if (!empty($request->input('selected_rows'))) {
                $business_id = $request->session()->get('user.business_id');

                $selected_rows = explode(',', $request->input('selected_rows'));

                $contacts = Contact::where('business_id', $business_id)
                    ->whereIn('id', $selected_rows)
                    ->get();

                DB::beginTransaction();
                $not_deleted_contact = []; // not deleted contact names
                foreach ($contacts  as $contact) {
                    $transactions = Transaction::where('contact_id', $contact->id)->whereIn('type', ['sell', 'purchase'])->where('deleted_at', null)->first();

                    if (!empty($transactions)) {
                        array_push($not_deleted_contact, $contact->name);
                    } else {
                        $contact->delete();
                    }
                }

                DB::commit();
            }

            if (empty($not_deleted_contact)) {
                $output = [
                    'success' => 1,
                    'msg' => __('lang_v1.deleted_success')
                ];
            } else {
                $not_deleted_contact_name =  implode(',', $not_deleted_contact);
                $output = [
                    'success' => 0,
                    'msg' => __('lang_v1.contacts') . ' ' . $not_deleted_contact_name . ' ' . __('lang_v1.contact_could_not_be_deleted')
                ];
            }
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

            $output = [
                'success' => 0,
                'msg' => __("messages.something_went_wrong")
            ];
        }

        return redirect()->back()->with(['status' => $output]);
    }

    /**
     * Shows ledger for contacts
     *
     * @param  \Illuminate\Http\Request
     * @return \Illuminate\Http\Response
     */
    public function getLedger()
    {
        if (!auth()->user()->can('supplier.view') && !auth()->user()->can('customer.view')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');
        $asset_account_id = Account::leftjoin('account_types', 'accounts.account_type_id', 'accounts.id')
            ->where('account_types.name', 'like', '%Assets%')
            ->where('accounts.business_id', $business_id)
            ->pluck('accounts.id')->toArray();
        $contact_id = request()->input('contact_id');

        $start_date = request()->start_date;
        $end_date =  request()->end_date;
        $transaction_type =  request()->transaction_type;
        $transaction_amount =  request()->transaction_amount;
        $contact = Contact::find($contact_id);
        $business_details = $this->businessUtil->getDetails($contact->business_id);
        $location_details = BusinessLocation::where('business_id', $contact->business_id)->first();
        $opening_balance = Transaction::where('contact_id', $contact_id)->where('type', 'opening_balance')->where('payment_status', 'due')->sum('final_total');

        $ledger_details = $this->__getLedgerDetails($contact_id, $start_date, $end_date);
        if ($contact->type == 'supplier') {
            $query = ContactLedger::leftjoin('transactions', 'contact_ledgers.transaction_id', 'transactions.id')
                ->leftjoin('transaction_payments', 'contact_ledgers.transaction_payment_id', 'transaction_payments.id')
                ->leftjoin('business_locations', 'transactions.location_id', 'business_locations.id')
                ->leftjoin('account_transactions', 'transactions.id', 'account_transactions.transaction_id')
                ->leftjoin('accounts', 'account_transactions.account_id', 'accounts.id')
                ->where('transactions.contact_id', $contact_id)
                ->where('transactions.business_id', $business_id)
                ->select(
                    'contact_ledgers.*',
                    'contact_ledgers.type as acc_transaction_type',
                    'business_locations.name as location_name',
                    'transactions.ref_no',
                    'transactions.invoice_no',
                    'transactions.transaction_date',
                    'transactions.payment_status',
                    'transactions.pay_term_number',
                    'transactions.pay_term_type',
                    'transaction_payments.method as payment_method',
                    'transaction_payments.bank_name',
                    'transaction_payments.cheque_date',
                    'transaction_payments.cheque_number',
                    'transactions.type as transaction_type',
                    'accounts.account_number',
                    'accounts.name as account_name'
                )->groupBy('contact_ledgers.id');
        }

        if ($contact->type == 'customer') {
            $opening_balance_new = DB::select("select `cl`.`amount` as opening_balance
            from `contact_ledgers` cl left join `transactions` t on `cl`.`transaction_id` = `t`.`id`
            left join `business_locations` bl on `t`.`location_id` = `bl`.`id`
             where `cl`.`contact_id` = " . $contact_id . "
             and `cl`.`type` = 'debit'
             and `t`.`business_id` = " . $business_id . "
            and `t`.`type` = 'opening_balance'
             and date(`cl`.`operation_date`) >= '" . $start_date . "'
             and date(`cl`.`operation_date`) <= '" . $end_date . "'
            order by `cl`.`operation_date`");
            if (count($opening_balance_new) == 0) {
                $opening_balance_new = DB::select(" select ( select
                sum(`bc_cl`.`amount`) as total_paid
                from `contact_ledgers` bc_cl left join `transactions` bc_t on `bc_cl`.`transaction_id` = `bc_t`.`id`
               left join `business_locations` bc_bl on `bc_t`.`location_id` = `bc_bl`.`id`
               where `bc_cl`.`contact_id` =  " . $contact_id . "
               and `bc_cl`.`type` = 'credit'
               and `bc_t`.`business_id` = " . $business_id . "
               and date(`bc_cl`.`operation_date`)  <= '" . $start_date . "'
               group by `bc_cl`.`id` and `bc_cl`.`contact_id` order by bc_cl.operation_date) as before_purchase,
               (select sum(`cl`.`amount`)
               from `contact_ledgers` cl left join `transactions` t on `cl`.`transaction_id` = `t`.`id`
               left join `business_locations` bl on `t`.`location_id` = `bl`.`id`
                where `cl`.`contact_id` = " . $contact_id . "
                and `cl`.`type` = 'debit'
                and `t`.`business_id` = " . $business_id . "
                and date(`cl`.`operation_date`) < '" . $start_date . "'
                group by `cl`.`id` and `cl`.`contact_id` order by cl.operation_date)  as before_sell,
               (select(IFNULL(before_sell,0) - IFNULL(before_purchase,0))) as opening_balance");
            }
            $total_paid = DB::select("select
            sum(`bc_cl`.`amount`) as total_paid
            from `contact_ledgers` bc_cl left join `transactions` bc_t on `bc_cl`.`transaction_id` = `bc_t`.`id`
           left join `business_locations` bc_bl on `bc_t`.`location_id` = `bc_bl`.`id`
           where `bc_cl`.`contact_id` =  " . $contact_id . "
           and `bc_cl`.`type` = 'credit'
           and `bc_t`.`business_id` = " . $business_id . "
           and date(`bc_cl`.`operation_date`)  >= '" . $start_date . "'
           and date(`bc_cl`.`operation_date`)  <= '" . $end_date . "'
           group by `bc_cl`.`id` and `bc_cl`.`contact_id` ");
            $total_sell = DB::select("select
            sum(`bc_cl`.`amount`) as total_sell
            from `contact_ledgers` bc_cl left join `transactions` bc_t on `bc_cl`.`transaction_id` = `bc_t`.`id`
           left join `business_locations` bc_bl on `bc_t`.`location_id` = `bc_bl`.`id`
           where `bc_cl`.`contact_id` =  " . $contact_id . "
           and `bc_cl`.`type` = 'debit'
           and `bc_t`.`type` != 'opening_balance'
           and `bc_t`.`business_id` = " . $business_id . "
           and date(`bc_cl`.`operation_date`)  >= '" . $start_date . "'
           and date(`bc_cl`.`operation_date`)  <= '" . $end_date . "'
           group by `bc_cl`.`id` and `bc_cl`.`contact_id` ");
            $ledger_details['total_invoice'] = count($total_sell) > 0 ? $total_sell[0]->total_sell : 0;
            $ledger_details['total_paid'] = count($total_paid) > 0 ? $total_paid[0]->total_paid : 0;
            $ledger_details['beginning_balance'] = count($opening_balance_new) > 0 ? $opening_balance_new[0]->opening_balance : 0;
            $ledger_details['balance_due'] = $ledger_details['beginning_balance'] + $ledger_details['total_invoice'] - $ledger_details['total_paid'];

            $query = ContactLedger::leftjoin('transactions', 'contact_ledgers.transaction_id', 'transactions.id')
                ->leftjoin('business_locations', 'transactions.location_id', 'business_locations.id')
                ->leftjoin('transaction_payments', 'contact_ledgers.transaction_payment_id', 'transaction_payments.id')
                ->leftjoin('property_sell_lines', 'transactions.id', 'property_sell_lines.transaction_id')
                ->leftjoin('properties', 'property_sell_lines.property_id', 'properties.id')
                ->leftjoin('property_blocks', 'property_sell_lines.block_id', 'property_blocks.id')
                ->leftjoin('installments', 'contact_ledgers.installment_id', 'installments.id')
                ->leftjoin('payment_options', 'contact_ledgers.payment_option_id', 'payment_options.id')
                ->where('transactions.contact_id', $contact_id)
                ->where('transactions.business_id', $business_id)
                ->select(
                    'contact_ledgers.*',
                    'contact_ledgers.type as acc_transaction_type',
                    'contact_ledgers.transaction_sell_line_id',
                    'contact_ledgers.income_type',
                    'business_locations.name as location_name',
                    'transactions.sub_type as t_sub_type',
                    'transactions.final_total',
                    'transactions.ref_no',
                    'transactions.invoice_no',
                    'transactions.is_direct_sale',
                    'transactions.is_credit_sale',
                    'transactions.is_settlement',
                    'transactions.transaction_date',
                    'transactions.payment_status',
                    'transactions.pay_term_number',
                    'transactions.pay_term_type',
                    'transactions.type as transaction_type',
                    'transaction_payments.method as payment_method',
                    'transaction_payments.paid_on',
                    'transaction_payments.bank_name',
                    'transaction_payments.cheque_date',
                    'transaction_payments.cheque_number',
                    'properties.name as property_name',
                    'property_blocks.block_number',
                    'installments.installment_no',
                    'installments.date as installment_date',
                    'payment_options.payment_option',
                    DB::raw("(select
                    sum(`bc_cl`.`amount`)
                    from `contact_ledgers` bc_cl left join `transactions` bc_t on `bc_cl`.`transaction_id` = `bc_t`.`id`
                    left join `business_locations` bc_bl on `bc_t`.`location_id` = `bc_bl`.`id`
                    where `bc_cl`.`contact_id` =  `contact_ledgers`.`contact_id`
                    and `bc_cl`.`type` = 'credit'
                    and `bc_t`.`business_id` = `transactions`.`business_id`
                    and `bc_cl`.`id`  <= `contact_ledgers`.`id`
                    group by `bc_cl`.`id` and `bc_cl`.`contact_id`) as balance_credit"),
                    DB::raw("(select
                    sum(`cl`.`amount`)
                    from `contact_ledgers` cl left join `transactions` t on `cl`.`transaction_id` = `t`.`id`
                    left join `business_locations` bl on `t`.`location_id` = `bl`.`id`
                    where `cl`.`contact_id` =  `contact_ledgers`.`contact_id`
                    and `cl`.`type` = 'debit'
                    and `t`.`business_id` = `transactions`.`business_id`
                    and `cl`.`id`  <= `contact_ledgers`.`id`
                    group by `cl`.`id` and `cl`.`contact_id`) as balance_debit"),
                    DB::raw("(select(IFNULL(balance_debit,0) - IFNULL(balance_credit,0)) ) as balance")
                )
                ->groupBy('contact_ledgers.id');
        }

        if (!empty($start_date)  && !empty($end_date)) {
            $query->whereDate('contact_ledgers.operation_date', '>=', $start_date);
            $query->whereDate('contact_ledgers.operation_date', '<=', $end_date);
        }
        $block_id = request()->block_id;
        if (!empty($block_id)) {
            $query->where('property_sell_lines.block_id', $block_id);
        }
        $project_id = request()->project_id;
        if (!empty($project_id)) {
            $query->where('property_sell_lines.property_id', $project_id);
        }
        $query->orderby('contact_ledgers.operation_date');
  
        $ledger_transactions = $query->get();

        if (request()->input('action') == 'pdf') {
            $for_pdf = true;
            $html = view('property::contact.ledger')
                ->with(compact('ledger_details', 'contact', 'for_pdf', 'ledger_transactions', 'business_details', 'location_details'))->render();
            $mpdf = $this->getMpdf();
            $mpdf->WriteHTML($html);
            $mpdf->Output();
        }
        if (request()->input('action') == 'print') {
            $for_pdf = true;
            return view('property::contact.ledger')
                ->with(compact('ledger_details', 'contact', 'for_pdf', 'ledger_transactions', 'business_details', 'location_details'))->render();
        }

        return view('property::contact.ledger')
            ->with(compact('ledger_details', 'contact', 'opening_balance', 'ledger_transactions', 'business_details', 'location_details'));
    }


    /**
     * Function to get ledger details
     *
     */
    private function __getLedgerDetails($contact_id, $start, $end)
    {
        $contact = Contact::where('id', $contact_id)->first();
        //Get transaction totals between dates
        $transactions = $this->__transactionQuery($contact_id, $start, $end)
            ->with(['location'])->get();

        $transaction_types = Transaction::transactionTypes();

        //Get sum of totals before start date
        $previous_transaction_sums = $this->__transactionQuery($contact_id, $start)
            ->select(
                DB::raw("SUM(IF(type = 'purchase', final_total, 0)) as total_purchase"),
                DB::raw("SUM(IF(type = 'sell' AND status = 'final', final_total, 0)) as total_invoice"),
                DB::raw("SUM(IF(type = 'sell_return', final_total, 0)) as total_sell_return"),
                DB::raw("SUM(IF(type = 'purchase_return', final_total, 0)) as total_purchase_return"),
                DB::raw("SUM(IF(type = 'opening_balance', final_total, 0)) as opening_balance")
            )->first();

        $ledger = [];

        foreach ($transactions as $transaction) {
            $ledger[] = [
                'date' => $transaction->transaction_date,
                'ref_no' => in_array($transaction->type, ['sell', 'sell_return']) ? $transaction->invoice_no : $transaction->ref_no,
                'type' => $transaction_types[$transaction->type],
                'location' => $transaction->location->name,
                'payment_status' =>  __('lang_v1.' . $transaction->payment_status),
                'total' => $transaction->final_total,
                'payment_method' => '',
                'debit' => '',
                'credit' => '',
                'others' => $transaction->additional_notes
            ];
        }

        $invoice_sum = $transactions->where('type', 'sell')->sum('final_total');
        $purchase_sum = $transactions->where('type', 'purchase')->sum('final_total');
        $sell_return_sum = $transactions->where('type', 'sell_return')->sum('final_total');
        $purchase_return_sum = $transactions->where('type', 'purchase_return')->sum('final_total');
        $opening_balance_sum = $transactions->where('type', 'opening_balance')->sum('final_total');

        //Get payment totals between dates
        $payments = $this->__paymentQuery($contact_id, $start, $end)
            ->select('transaction_payments.*', 'bl.name as location_name', 't.type as transaction_type', 't.ref_no', 't.invoice_no')->get();
        $paymentTypes = $this->transactionUtil->payment_types();

        //Get payment totals before start date
        $prev_payments_sum = $this->__paymentQuery($contact_id, $start)
            ->select(DB::raw("SUM(transaction_payments.amount) as total_paid"))
            ->first();

        foreach ($payments as $payment) {
            $ref_no = in_array($payment->transaction_type, ['sell', 'sell_return']) ?  $payment->invoice_no :  $payment->ref_no;
            $ledger[] = [
                'date' => $payment->paid_on,
                'ref_no' => $payment->payment_ref_no,
                'type' => $transaction_types['payment'],
                'location' => $payment->location_name,
                'payment_status' => '',
                'total' => '',
                'payment_method' => !empty($paymentTypes[$payment->method]) ? $paymentTypes[$payment->method] : '',
                'debit' => in_array($payment->transaction_type, ['purchase', 'sell_return']) ? $payment->amount : '',
                'credit' => in_array($payment->transaction_type, ['sell', 'purchase_return', 'opening_balance']) ? $payment->amount : '',
                'others' => $payment->note . '<small>' . __('account.payment_for') . ': ' . $ref_no . '</small>'
            ];

            if ($contact->type == "supplier") {
            }
        }

        $total_ob_paid = $payments->where('transaction_type', 'opening_balance')->sum('amount');
        $total_invoice_paid = $payments->where('transaction_type', 'sell')->sum('amount');
        $total_purchase_paid = $payments->where('transaction_type', 'purchase')->sum('amount');

        $start_date = $this->commonUtil->format_date($start);
        $end_date = $this->commonUtil->format_date($end);

        $total_invoice = $invoice_sum - $sell_return_sum;
        $total_purchase = $purchase_sum - $purchase_return_sum;

        $total_prev_invoice = $previous_transaction_sums->total_purchase + $previous_transaction_sums->total_invoice -  $previous_transaction_sums->total_sell_return -  $previous_transaction_sums->total_purchase_return;
        $total_prev_paid = $prev_payments_sum->total_paid;

        $beginning_balance = ($previous_transaction_sums->opening_balance + $total_prev_invoice + $opening_balance_sum) - $prev_payments_sum->amount;

        $total_paid = $total_invoice_paid + $total_purchase_paid + $total_ob_paid;
        $curr_due =  ($beginning_balance + $total_invoice + $total_purchase) - $total_paid;

        //Sort by date
        if (!empty($ledger)) {
            usort($ledger, function ($a, $b) {
                $t1 = strtotime($a['date']);
                $t2 = strtotime($b['date']);
                return $t2 - $t1;
            });
        }

        $output = [
            'ledger' => $ledger,
            'start_date' => $start_date,
            'end_date' => $end_date,
            'total_invoice' => $total_invoice,
            'total_purchase' => $total_purchase,
            'beginning_balance' => $beginning_balance,
            'total_paid' => $total_paid,
            'balance_due' => $curr_due
        ];

        return $output;
    }

    /**
     * Query to get transaction totals for a customer
     *
     */
    private function __transactionQuery($contact_id, $start, $end = null)
    {
        $business_id = request()->session()->get('user.business_id');
        $transaction_type_keys = array_keys(Transaction::transactionTypes());

        $query = Transaction::where('transactions.contact_id', $contact_id)
            ->where('transactions.business_id', $business_id)
            ->where('status', '!=', 'draft')
            ->whereIn('type', $transaction_type_keys);

        if (!empty($start)  && !empty($end)) {
            $query->whereDate(
                'transactions.transaction_date',
                '>=',
                $start
            )
                ->whereDate('transactions.transaction_date', '<=', $end)->get();
        }

        if (!empty($start)  && empty($end)) {
            $query->whereDate('transactions.transaction_date', '<', $start);
        }

        return $query;
    }

    /**
     * Query to get payment details for a customer
     *
     */
    private function __paymentQuery($contact_id, $start, $end = null)
    {
        $business_id = request()->session()->get('user.business_id');

        $query = TransactionPayment::join(
            'transactions as t',
            'transaction_payments.transaction_id',
            '=',
            't.id'
        )
            ->leftJoin('business_locations as bl', 't.location_id', '=', 'bl.id')
            ->where('t.contact_id', $contact_id)
            ->where('t.business_id', $business_id)
            ->where('t.status', '!=', 'draft');

        if (!empty($start)  && !empty($end)) {
            $query->whereDate('t.transaction_date', '>=', $start)
                ->whereDate('t.transaction_date', '<=', $end);
        }

        if (!empty($start)  && empty($end)) {
            $query->whereDate('t.transaction_date', '<', $start);
        }

        return $query;
    }
}
