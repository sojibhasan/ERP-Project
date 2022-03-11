<?php

namespace App\Http\Controllers;

use App\Account;
use App\Business;
use App\BusinessLocation;
use App\Contact;
use App\ContactGroup;
use App\InvoiceScheme;
use App\Product;
use App\SellingPriceGroup;
use App\TaxRate;
use App\Transaction;
use App\TransactionSellLine;
use App\TypesOfService;
use App\UploadedOrder;
use App\User;
use App\Utils\BusinessUtil;
use App\Utils\ContactUtil;
use App\Utils\ModuleUtil;
use App\Utils\ProductUtil;
use App\Utils\TransactionUtil;
use App\Variation;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\Superadmin\Entities\Subscription;
use Yajra\DataTables\Facades\DataTables;
use App\Store;

class SellController extends Controller
{
    /**
     * All Utils instance.
     *
     */
    protected $contactUtil;
    protected $businessUtil;
    protected $transactionUtil;
    protected $productUtil;


    /**
     * Constructor
     *
     * @param ProductUtils $product
     * @return void
     */
    public function __construct(ContactUtil $contactUtil, BusinessUtil $businessUtil, TransactionUtil $transactionUtil, ModuleUtil $moduleUtil, ProductUtil $productUtil)
    {
        $this->contactUtil = $contactUtil;
        $this->businessUtil = $businessUtil;
        $this->transactionUtil = $transactionUtil;
        $this->moduleUtil = $moduleUtil;
        $this->productUtil = $productUtil;

        $this->dummyPaymentLine = [
            'method' => '', 'amount' => 0, 'note' => '', 'card_transaction_number' => '', 'card_number' => '', 'card_type' => '', 'card_holder_name' => '', 'card_month' => '', 'card_year' => '', 'card_security' => '', 'cheque_number' => '', 'cheque_date' => '', 'bank_account_number' => '',
            'is_return' => 0, 'transaction_no' => ''
        ];

        $this->shipping_status_colors = [
            'ordered' => 'bg-yellow',
            'packed' => 'bg-info',
            'shipped' => 'bg-navy',
            'delivered' => 'bg-green',
            'cancelled' => 'bg-red',
        ];
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        if (empty(request()->general_customer_id)) {
            if (!auth()->user()->can('sell.view') && !auth()->user()->can('sell.create') && !auth()->user()->can('direct_sell.access') && !auth()->user()->can('view_own_sell_only')) {
                abort(403, 'Unauthorized action.');
            }
        }

        $business_id = request()->session()->get('user.business_id');
        $is_woocommerce = $this->moduleUtil->isModuleInstalled('Woocommerce');
        $is_tables_enabled = $this->transactionUtil->isModuleEnabled('tables');
        $is_service_staff_enabled = $this->transactionUtil->isModuleEnabled('service_staff');

        if (request()->ajax()) {
            if (empty($business_id)) { // condition for general customer 
                $business_id = request()->business_id;
            }
            $payment_types = $this->transactionUtil->payment_types(null, false, false, false, true);
            $with = [];
            $shipping_statuses = $this->transactionUtil->shipping_statuses();
            $sells = Transaction::leftJoin('contacts', 'transactions.contact_id', '=', 'contacts.id')
                // ->leftJoin('transaction_payments as tp', 'transactions.id', '=', 'tp.transaction_id')
                ->leftJoin('transaction_sell_lines as tsl', 'transactions.id', '=', 'tsl.transaction_id')
                ->leftJoin('products', 'tsl.product_id', '=', 'products.id')
                ->leftJoin('users as u', 'transactions.created_by', '=', 'u.id')
                ->leftJoin('users as ss', 'transactions.res_waiter_id', '=', 'ss.id')
                ->leftJoin('res_tables as tables', 'transactions.res_table_id', '=', 'tables.id')
                ->join(
                    'business_locations AS bl',
                    'transactions.location_id',
                    '=',
                    'bl.id'
                )
                ->leftJoin(
                    'transactions AS SR',
                    'transactions.id',
                    '=',
                    'SR.return_parent_id'
                )
                ->leftJoin(
                    'types_of_services AS tos',
                    'transactions.types_of_service_id',
                    '=',
                    'tos.id'
                )
                ->where('transactions.business_id', $business_id)
                ->where('transactions.type', 'sell')
                ->whereIn('transactions.status', ['final', 'order'])
                ->select(
                    'transactions.id',
                    'transactions.transaction_date',
                    'transactions.is_direct_sale',
                    'transactions.invoice_no',
                    'contacts.name',
                    'contacts.mobile',
                    'transactions.price_later',
                    'transactions.payment_status',
                    'transactions.final_total',
                    'transactions.tax_amount',
                    'transactions.discount_amount',
                    'transactions.discount_type',
                    'transactions.total_before_tax',
                    'transactions.rp_redeemed',
                    'transactions.rp_redeemed_amount',
                    'transactions.rp_earned',
                    'transactions.types_of_service_id',
                    'transactions.shipping_status',
                    'transactions.pay_term_number',
                    'transactions.pay_term_type',
                    'transactions.additional_notes',
                    'transactions.staff_note',
                    'transactions.shipping_details',
                    'transactions.commission_agent',
                    'transactions.ref_no as ref_no',
                    DB::raw("CONCAT(COALESCE(u.surname, ''),' ',COALESCE(u.first_name, ''),' ',COALESCE(u.last_name,'')) as added_by"),
                    DB::raw('(SELECT SUM(IF(TP.is_return = 1,-1*TP.amount,TP.amount)) FROM transaction_payments AS TP WHERE
                        TP.transaction_id=transactions.id) as total_paid'),
                    'bl.name as business_location',
                    DB::raw('COUNT(SR.id) as return_exists'),
                    DB::raw('(SELECT SUM(TP2.amount) FROM transaction_payments AS TP2 WHERE
                        TP2.transaction_id=SR.id ) as return_paid'),
                    DB::raw('COALESCE(SR.final_total, 0) as amount_return'),
                    'SR.id as return_transaction_id',
                    'tos.name as types_of_service_name',
                    'transactions.service_custom_field_1',
                    DB::raw('COUNT( DISTINCT tsl.id) as total_items'),
                    DB::raw("CONCAT(COALESCE(ss.surname, ''),' ',COALESCE(ss.first_name, ''),' ',COALESCE(ss.last_name,'')) as waiter"),
                    'tables.name as table_name'
                )->with('sell_lines');
            $permitted_locations = auth()->user()->permitted_locations();
            if ($permitted_locations != 'all') {
                $sells->whereIn('transactions.location_id', $permitted_locations);
            }

            //Add condition for created_by,used in sales representative sales report
            if (request()->has('created_by')) {
                $created_by = request()->get('created_by');
                if (!empty($created_by)) {
                    $sells->where('transactions.created_by', $created_by);
                }
            }

            if (!auth()->user()->can('direct_sell.access') && auth()->user()->can('view_own_sell_only')) {
                $sells->where('transactions.created_by', request()->session()->get('user.id'));
            }

            if (!empty(request()->input('payment_status')) && request()->input('payment_status') != 'overdue' && request()->input('payment_status') != 'price_later') {
                $sells->where('transactions.payment_status', request()->input('payment_status'));
            } elseif (request()->input('payment_status') == 'overdue') {
                $sells->whereIn('transactions.payment_status', ['due', 'partial'])
                    ->whereNotNull('transactions.pay_term_number')
                    ->whereNotNull('transactions.pay_term_type')
                    ->whereRaw("IF(transactions.pay_term_type='days', DATE_ADD(transactions.transaction_date, INTERVAL transactions.pay_term_number DAY) < CURDATE(), DATE_ADD(transactions.transaction_date, INTERVAL transactions.pay_term_number MONTH) < CURDATE())");
            }elseif (request()->input('payment_status') == 'price_later') {
                $sells->where('transactions.price_later', 1);
            }

            //Add condition for location,used in sales representative expense report
            if (request()->has('location_id')) {
                $location_id = request()->get('location_id');
                if (!empty($location_id)) {
                    $sells->where('transactions.location_id', $location_id);
                }
            }

            if (!empty(request()->input('rewards_only')) && request()->input('rewards_only') == true) {
                $sells->where(function ($q) {
                    $q->whereNotNull('transactions.rp_earned')
                        ->orWhere('transactions.rp_redeemed', '>', 0);
                });
            }

            //general customer filter
            if (!empty(request()->general_customer_id)) {
                $contact = Contact::where('business_id', $business_id)->where('contact_id',  request()->general_customer_id)->first();
                if (!empty($contact)) {
                    $customer_id = $contact->id;
                    $sells->where('contacts.id', $customer_id);
                }
            }
            if (!empty(request()->customer_id)) {
                $customer_id = request()->customer_id;
                $sells->where('contacts.id', $customer_id);
            }
            if (!empty(request()->start_date) && !empty(request()->end_date)) {
                $start = request()->start_date;
                $end =  request()->end_date;
                $sells->whereDate('transactions.transaction_date', '>=', $start)
                    ->whereDate('transactions.transaction_date', '<=', $end);
            }

            //Check is_direct sell
            if (request()->has('is_direct_sale')) {
                $is_direct_sale = request()->is_direct_sale;
                if ($is_direct_sale == 0) {
                    $sells->where('transactions.is_direct_sale', 0);
                    $sells->whereNull('transactions.sub_type');
                }
            }

            //Add condition for commission_agent,used in sales representative sales with commission report
            if (request()->has('commission_agent')) {
                $commission_agent = request()->get('commission_agent');
                if (!empty($commission_agent)) {
                    $sells->where('transactions.commission_agent', $commission_agent);
                }
            }

            if ($is_woocommerce) {
                $sells->addSelect('transactions.woocommerce_order_id');
                if (request()->only_woocommerce_sells) {
                    $sells->whereNotNull('transactions.woocommerce_order_id');
                }
            }

            if (!empty(request()->list_for) && request()->list_for == 'service_staff_report') {
                $sells->whereNotNull('transactions.res_waiter_id');
                $sells->leftJoin('users as ss', 'ss.id', '=', 'transactions.res_waiter_id');
                $sells->addSelect(
                    DB::raw('CONCAT(COALESCE(ss.first_name, ""), COALESCE(ss.last_name, "")) as service_staff')
                );
            }

            if (!empty(request()->res_waiter_id)) {
                $sells->where('transactions.res_waiter_id', request()->res_waiter_id);
            }

            if (!empty(request()->input('sub_type'))) {
                $sells->where('transactions.sub_type', request()->input('sub_type'));
            }

            if (!empty(request()->input('created_by'))) {
                $sells->where('transactions.created_by', request()->input('created_by'));
            }

            if (!empty(request()->input('references'))) {
                $sells->where('transactions.ref_no', request()->input('references'));
            }

            if (!empty(request()->input('sales_cmsn_agnt'))) {
                $sells->where('transactions.commission_agent', request()->input('sales_cmsn_agnt'));
            }

            if (!empty(request()->input('service_staffs'))) {
                $sells->where('transactions.res_waiter_id', request()->input('service_staffs'));
            }
            $only_shipments = request()->only_shipments == 'true' ? true : false;
            if ($only_shipments && auth()->user()->can('access_shipping')) {
                $sells->whereNotNull('transactions.shipping_status');
            }

            if (!empty(request()->input('shipping_status'))) {
                $sells->where('transactions.shipping_status', request()->input('shipping_status'));
            }

            if (!empty(request()->input('invoice_no'))) {
                $sells->where('transactions.invoice_no', request()->input('invoice_no'));
            }

            $sells->groupBy('transactions.id');

            if (!empty(request()->suspended)) {
                $with = ['sell_lines'];

                if ($is_tables_enabled) {
                    $with[] = 'table';
                }

                if ($is_service_staff_enabled) {
                    $with[] = 'service_staff';
                }

                $sales = $sells->where('transactions.is_suspend', 1)
                    ->with($with)
                    ->addSelect('transactions.is_suspend', 'transactions.res_table_id', 'transactions.res_waiter_id', 'transactions.additional_notes')
                    ->get();

                return view('sale_pos.partials.suspended_sales_modal')->with(compact('sales', 'is_tables_enabled', 'is_service_staff_enabled'));
            }

            $with[] = 'payment_lines';
            if (!empty($with)) {
                $sells->with($with);
            }

            //$business_details = $this->businessUtil->getDetails($business_id);
            if ($this->businessUtil->isModuleEnabled('subscription')) {
                $sells->addSelect('transactions.is_recurring', 'transactions.recur_parent_id');
            }
            $datatable = Datatables::of($sells)
                ->addColumn(
                    'action',
                    function ($row) use ($only_shipments) {
                        $html = '<div class="btn-group">
                                    <button type="button" class="btn btn-info dropdown-toggle btn-xs" 
                                        data-toggle="dropdown" aria-expanded="false">' .
                            __("messages.actions") .
                            '<span class="caret"></span><span class="sr-only">Toggle Dropdown
                                        </span>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-left" role="menu">';

                        if (auth()->user()->can("sell.view") || auth()->user()->can("direct_sell.access") || auth()->user()->can("view_own_sell_only")) {
                            $html .= '<li><a href="#" data-href="' . action("SellController@show", [$row->id]) . '" class="btn-modal" data-container=".view_modal"><i class="fa fa-external-link" aria-hidden="true"></i> ' . __("messages.view") . '</a></li>';
                        }
                        if (!$only_shipments) {
                            
                            // Remove Edit or Delete buttons for the sales done from Petro Module/Settlement page
                            if(strpos($row->invoice_no, "ST") !== 0){
                                if ($row->is_direct_sale == 0) {
                                    if (auth()->user()->can("sell.update")) {
                                        $html .= '<li><a target="_blank" href="' . action('SellPosController@edit', [$row->id]) . '"><i class="glyphicon glyphicon-edit"></i> ' . __("messages.edit") . '</a></li>';
                                    }
                                } else {
                                    if (auth()->user()->can("direct_sell.access")) {
                                        $html .= '<li><a target="_blank" href="' . action('SellController@edit', [$row->id]) . '"><i class="glyphicon glyphicon-edit"></i> ' . __("messages.edit") . '</a></li>';
                                    }
                                }
    
                                if (auth()->user()->can("direct_sell.delete") || auth()->user()->can("sell.delete")) {
                                    $html .= '<li><a href="' . action('SellPosController@destroy', [$row->id]) . '" class="delete-sale"><i class="fa fa-trash"></i> ' . __("messages.delete") . '</a></li>';
                                }
                            }
                        }
                        if (auth()->user()->can("sell.view") || auth()->user()->can("direct_sell.access")) {
                            $html .= '<li><a href="#" class="print-invoice" data-href="' . route('sell.printInvoice', [$row->id]) . '"><i class="fa fa-print" aria-hidden="true"></i> ' . __("messages.print") . '</a></li>
                                <li><a href="#" class="print-invoice" data-href="' . route('sell.printInvoice', [$row->id]) . '?package_slip=true"><i class="fa fa-file-text-o" aria-hidden="true"></i> ' . __("lang_v1.packing_slip") . '</a></li>';
                        }
                        if (auth()->user()->can("access_shipping")) {
                            $html .= '<li><a href="#" data-href="' . action('SellController@editShipping', [$row->id]) . '" class="btn-modal" data-container=".view_modal"><i class="fa fa-truck" aria-hidden="true"></i>' . __("lang_v1.edit_shipping") . '</a></li>';
                        }
                        if (!$only_shipments) {
                            $html .= '<li class="divider"></li>';
                            if ($row->payment_status != "paid" && (auth()->user()->can("sell.create") || auth()->user()->can("direct_sell.access")) && auth()->user()->can("sell.payments")) {
                                $html .= '<li><a href="' . action('TransactionPaymentController@addPayment', [$row->id]) . '" class="add_payment_modal"><i class="fa fa-money"></i> ' . __("purchase.add_payment") . '</a></li>';
                            }
                            if ($row->payment_status == "pending") {
                                $html .= '<li><a href="' . action('TransactionPaymentController@pendingPayment', [$row->id]) . '" class="view_payment_modal"><i class="fa fa-money"></i> ' . __("lang_v1.pending_payment_confimation") . '</a></li>';
                            }

                            $html .= '<li><a href="' . action('TransactionPaymentController@show', [$row->id]) . '" class="view_payment_modal"><i class="fa fa-money"></i> ' . __("purchase.view_payments") . '</a></li>';

                            if (auth()->user()->can("sell.create")) {
                                $html .= '<li><a href="' . action('SellController@duplicateSell', [$row->id]) . '"><i class="fa fa-copy"></i> ' . __("lang_v1.duplicate_sell") . '</a></li>

                                <li><a href="' . action('SellReturnController@add', [$row->id]) . '"><i class="fa fa-undo"></i> ' . __("lang_v1.sell_return") . '</a></li>

                                <li><a href="' . action('SellPosController@showInvoiceUrl', [$row->id]) . '" class="view_invoice_url"><i class="fa fa-external-link"></i> ' . __("lang_v1.view_invoice_url") . '</a></li>';
                            }

                            if (auth()->user()->can("send_notification")) {
                                $html .= '<li><a href="#" data-href="' . action('NotificationController@getTemplate', ["transaction_id" => $row->id, "template_for" => "new_sale"]) . '" class="btn-modal" data-container=".view_modal"><i class="fa fa-envelope" aria-hidden="true"></i>' . __("lang_v1.new_sale_notification") . '</a></li>';
                            }
                        }

                        $html .= '</ul></div>';

                        return $html;
                    }
                )
                ->removeColumn('id')
                ->editColumn(
                    'final_total',
                    function ($row) {
                        return '<span class="display_currency final-total" data-currency_symbol="true" data-orig-value="' . $this->productUtil->num_f($row->final_total) . '">' . $this->productUtil->num_f($row->final_total) . '</span>';
                    }
                )
                ->editColumn(
                    'tax_amount',
                    function ($row) {
                        return '<span class="display_currency total-tax" data-currency_symbol="true" data-orig-value="' . $this->productUtil->num_f($row->tax_amount) . '">' . $this->productUtil->num_f($row->tax_amount) . '</span>';
                    }
                )
                ->editColumn(
                    'total_paid',
                    function ($row) {
                        return '<span class="display_currency total-paid" data-currency_symbol="true" data-orig-value="' . $this->productUtil->num_f($row->total_paid) . '">' . $this->productUtil->num_f($row->total_paid) . '</span>';
                    }
                )
                ->editColumn(
                    'total_before_tax',
                    function ($row) {
                        return '<span class="display_currency total_before_tax" data-currency_symbol="true" data-orig-value="' . $this->productUtil->num_f($row->total_before_tax) . '">' . $this->productUtil->num_f($row->total_before_tax) . '</span>';
                    }
                )
                ->editColumn(
                    'discount_amount',
                    function ($row) {
                        $discount = !empty($row->discount_amount) ? $row->discount_amount : 0;

                        if (!empty($discount) && $row->discount_type == 'percentage') {
                            $discount = $row->total_before_tax * ($discount / 100);
                        }

                        return '<span class="display_currency total-discount" data-currency_symbol="true" data-orig-value="' . $this->productUtil->num_f($discount) . '">' . $this->productUtil->num_f($discount) . '</span>';
                    }
                )
                ->editColumn('transaction_date', '{{@format_datetime($transaction_date)}}')
                ->editColumn(
                    'payment_status',
                    function ($row) {
                        $payment_status = Transaction::getPaymentStatus($row);
                        return (string) view('sell.partials.payment_status', ['payment_status' => $payment_status, 'id' => $row->id]);
                    }
                )
                ->editColumn(
                    'types_of_service_name',
                    '<span class="service-type-label" data-orig-value="{{$types_of_service_name}}" data-status-name="{{$types_of_service_name}}">{{$types_of_service_name}}</span>'
                )
                ->addColumn('total_remaining', function ($row) {
                    $total_remaining =  $row->final_total - $row->total_paid;
                    $total_remaining_html = '<span class="display_currency payment_due" data-currency_symbol="true" data-orig-value="' . $this->productUtil->num_f($total_remaining) . '">' . $this->productUtil->num_f($total_remaining) . '</span>';


                    return $total_remaining_html;
                })
                ->addColumn('return_due', function ($row) {
                    $return_due_html = '';
                    if (!empty($row->return_exists)) {
                        // $return_due = $row->amount_return - $row->return_paid;
                        $return_due = $row->final_total - $row->total_paid - $row->return_paid;
                        $return_due_html .= '<a href="' . action("TransactionPaymentController@show", [$row->return_transaction_id]) . '" class="view_purchase_return_payment_modal"><span class="display_currency sell_return_due" data-currency_symbol="true" data-orig-value="' . $this->productUtil->num_f($return_due) . '">' . $this->productUtil->num_f($return_due) . '</span></a>';
                    }

                    return $return_due_html;
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
                ->editColumn('shipping_status', function ($row) use ($shipping_statuses) {
                    $status_color = !empty($this->shipping_status_colors[$row->shipping_status]) ? $this->shipping_status_colors[$row->shipping_status] : 'bg-gray';
                    $status = !empty($row->shipping_status) ? '<a href="#" class="btn-modal" data-href="' . action('SellController@editShipping', [$row->id]) . '" data-container=".view_modal"><span class="label ' . $status_color . '">' . $shipping_statuses[$row->shipping_status] . '</span></a>' : '';

                    return $status;
                })
                ->addColumn('payment_methods', function ($row) use ($payment_types) {
                    $methods = array_unique($row->payment_lines->pluck('method')->toArray());
                    $count = count($methods);
                    $payment_method = '';
                    if ($count == 1 && $methods[0] != null) {
                        $payment_method = $payment_types[$methods[0]];
                    } elseif ($count > 1) {
                        $payment_method = __('lang_v1.checkout_multi_pay');
                    }

                    $html = !empty($payment_method) ? '<span class="payment-method" data-orig-value="' . $payment_method . '" data-status-name="' . $payment_method . '">' . $payment_method . '</span>' : '';

                    return $html;
                })
                ->editColumn('total_comission', function ($row) {
                    $commission = 0.00;
                    $cmmsn_application = '';
                    if (!empty($row->commission_agent)) {
                        $agent = User::where('id', $row->commission_agent)->first();
                        $commission_type = $agent->commission_type;
                        $cmmsn_application = $agent->cmmsn_application;
                        $cmmsn_percent = (float) $agent->cmmsn_percent;
                        $cmmsn_units = json_decode($agent->cmmsn_units);
                        if ($cmmsn_application == 'bill') {
                            if ($commission_type == 'fixed') {
                                $commission = $cmmsn_percent;
                            }
                            if ($commission_type == 'percentage') {
                                $commission = ((float) $row->final_total * $cmmsn_percent) / 100;
                            }
                        }
                        if ($cmmsn_application == 'per_unit') {
                            foreach ($row->sell_lines as $sell_line) {
                                $product_unit = Product::where('id', $sell_line->product_id)->first()->unit_id;
                                $product_price = Variation::where('product_id', $sell_line->product_id)->first()->default_sell_price;
                                foreach ($cmmsn_units as $cmmsn_unit) {
                                    if ($cmmsn_unit->unit == $product_unit) {
                                        if ($cmmsn_unit->commission_type == 'fixed') {
                                            $commission += ($cmmsn_unit->cmmsn * $sell_line->quantity);
                                        }
                                        if ($cmmsn_unit->commission_type == 'percentage') {
                                            $commission += (((float) $product_price * $cmmsn_unit->cmmsn) / 100) * $sell_line->quantity;
                                        }
                                    }
                                }
                            }
                        }
                    }

                    // $total_comission = '<span class="display_currency total-comission" data-currency_symbol="true" data-orig-value="' . $commission . '">' . $commission . '</span>';
                    $total_comission = '<span >' . $commission . '</span>';

                    return $total_comission;
                })

                ->setRowAttr([
                    'data-href' => function ($row) {
                        if (auth()->user()->can("sell.view") || auth()->user()->can("view_own_sell_only")) {
                            return  action('SellController@show', [$row->id]);
                        } else {
                            return '';
                        }
                    }
                ]);

            $rawColumns = ['total_comission', 'final_total', 'action', 'total_paid', 'total_remaining', 'payment_status', 'invoice_no', 'discount_amount', 'tax_amount', 'total_before_tax', 'shipping_status', 'types_of_service_name', 'payment_methods', 'return_due'];
            return $datatable->rawColumns($rawColumns)
                ->make(true);
        }
        $business_locations = BusinessLocation::forDropdown($business_id, false);
        $customers = Contact::customersDropdown($business_id, false);
        $sales_representative = User::forDropdown($business_id, false, false, true);

        //Commission agent filter
        $is_cmsn_agent_enabled = request()->session()->get('business.sales_cmsn_agnt');
        $commission_agents = [];
        if (!empty($is_cmsn_agent_enabled)) {
            $commission_agents = User::forDropdown($business_id, false, true, true);
        }

        //Service staff filter
        $service_staffs = null;
        if ($this->productUtil->isModuleEnabled('service_staff')) {
            $service_staffs = $this->productUtil->serviceStaffDropdown($business_id);
        }
        $invoice_nos = Transaction::where('business_id', $business_id)->where('type', 'sell')->groupBy('invoice_no')->pluck('invoice_no', 'invoice_no');

        if (!$this->moduleUtil->hasThePermissionInSubscription($business_id, 'all_sales')) {
            abort(403, 'Unauthorized action.');
        }

        return view('sell.index')
            ->with(compact('business_locations', 'customers', 'is_woocommerce', 'sales_representative', 'is_cmsn_agent_enabled', 'commission_agents', 'service_staffs', 'is_tables_enabled', 'is_service_staff_enabled', 'invoice_nos'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (!auth()->user()->can('direct_sell.access')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');

        if (!$this->moduleUtil->hasThePermissionInSubscription($business_id, 'add_sale')) {
            abort(403, 'Unauthorized action.');
        }

        //Check if subscribed or not, then check for users quota
        if (!$this->moduleUtil->isSubscribed($business_id)) {
            return $this->moduleUtil->expiredResponse();
        } elseif (!$this->moduleUtil->isQuotaAvailable('invoices', $business_id)) {
            return $this->moduleUtil->quotaExpiredResponse('invoices', $business_id, action('SellController@index'));
        }

        $walk_in_customer = $this->contactUtil->getWalkInCustomer($business_id);

        $business_details = $this->businessUtil->getDetails($business_id);
        $contact_id = $this->businessUtil->check_customer_code($business_id, 1);
        $shortcuts = json_decode($business_details->keyboard_shortcuts, true);
        $taxes = TaxRate::forBusinessDropdown($business_id, true, true);

        $business_locations = BusinessLocation::forDropdown($business_id, false, true);
        $bl_attributes = $business_locations['attributes'];
        $business_locations = $business_locations['locations'];

        $default_location = null;
        if (count($business_locations) == 1) {
            foreach ($business_locations as $id => $name) {
                $default_location = $id;
            }
        }

        $commsn_agnt_setting = $business_details->sales_cmsn_agnt;
        $commission_agent = [];
        if ($commsn_agnt_setting == 'user') {
            $commission_agent = User::forDropdown($business_id);
        } elseif ($commsn_agnt_setting == 'cmsn_agnt') {
            $commission_agent = User::saleCommissionAgentsDropdown($business_id);
        }

        $types = [];
        if (auth()->user()->can('supplier.create')) {
            $types['supplier'] = __('report.supplier');
        }
        if (auth()->user()->can('customer.create')) {
            $types['customer'] = __('report.customer');
        }
        if (auth()->user()->can('supplier.create') && auth()->user()->can('customer.create')) {
            $types['both'] = __('lang_v1.both_supplier_customer');
        }
        $type = 'customer'; //contact type /used in quick add contact
        $customer_groups = ContactGroup::forDropdown($business_id);

        $payment_line = $this->dummyPaymentLine;
        $payment_types = $this->transactionUtil->payment_types(null, false, false, false, true);

        //Selling Price Group Dropdown
        $price_groups = SellingPriceGroup::forDropdown($business_id);

        $default_datetime = $this->businessUtil->format_date('now', true);

        $pos_settings = empty($business_details->pos_settings) ? $this->businessUtil->defaultPosSettings() : json_decode($business_details->pos_settings, true);

        $invoice_schemes = InvoiceScheme::forDropdown($business_id);
        $default_invoice_schemes = InvoiceScheme::getDefault($business_id);
        $shipping_statuses = $this->transactionUtil->shipping_statuses();

        //Types of service
        $types_of_service = [];
        if ($this->moduleUtil->isModuleEnabled('types_of_service')) {
            $types_of_service = TypesOfService::forDropdown($business_id);
        }

        $status_order_permission =  $this->moduleUtil->hasThePermissionInSubscription($business_id, 'status_order');
        //status array 
        if ($status_order_permission && auth()->user()->can('status_order')) {
            $status_array = ['final' => __('sale.final'), 'draft' => __('sale.draft'), 'quotation' => __('lang_v1.quotation'), 'order' => __('lang_v1.order')];
        } else {
            $status_array = ['final' => __('sale.final'), 'draft' => __('sale.draft'), 'quotation' => __('lang_v1.quotation')];
        }
        //Accounts
        $accounts = [];
        if ($this->moduleUtil->isModuleEnabled('account')) {
            $accounts = Account::forDropdown($business_id, true, false);
        }

        $temp_data = DB::table('temp_data')->where('business_id', $business_id)->select('sale_create_data')->first();
        if (!empty($temp_data)) {
            $temp_data = json_decode($temp_data->sale_create_data);
        }
        if (!request()->session()->get('business.popup_load_save_data')) {
            $temp_data = [];
        }



        $bank_group_accounts = Account::leftjoin('account_groups', 'accounts.asset_type', 'account_groups.id')
            ->where('accounts.business_id', $business_id)
            ->where('account_groups.name', 'Bank Account')
            ->pluck('accounts.name', 'accounts.id');
        $stores = Store::where('business_id', $business_id)->where('location_id', $default_location)->pluck('name', 'id');
        return view('sell.create')
            ->with(compact(
                'bank_group_accounts',
                'business_details',
                'taxes',
                'walk_in_customer',
                'business_locations',
                'bl_attributes',
                'default_location',
                'commission_agent',
                'type',
                'types',
                'customer_groups',
                'payment_line',
                'payment_types',
                'price_groups',
                'default_datetime',
                'pos_settings',
                'invoice_schemes',
                'default_invoice_schemes',
                'types_of_service',
                'accounts',
                'shipping_statuses',
                'shortcuts',
                'temp_data',
                'contact_id',
                'status_array',
                'stores'
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
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {    
        if (!auth()->user()->can('sell.view') && !auth()->user()->can('direct_sell.access') && !auth()->user()->can('view_own_sell_only')) {
            abort(403, 'Unauthorized action.');
        }
        $business_id = request()->session()->get('user.business_id');
        $taxes = TaxRate::where('business_id', $business_id)
            ->pluck('name', 'id');
        $query = Transaction::where('business_id', $business_id)
            ->where('id', $id)
            ->with(['contact', 'sell_lines' => function ($q) {
                $q->whereNull('parent_sell_line_id');
            }, 'sell_lines.product', 'sell_lines.product.unit', 'sell_lines.variations', 'sell_lines.variations.product_variation', 'payment_lines', 'sell_lines.modifiers', 'sell_lines.lot_details', 'tax', 'sell_lines.sub_unit', 'table', 'service_staff', 'sell_lines.service_staff', 'types_of_service', 'sell_lines.warranties']);

        if (!auth()->user()->can('sell.view') && !auth()->user()->can('direct_sell.access') && auth()->user()->can('view_own_sell_only')) {
            $query->where('transactions.created_by', request()->session()->get('user.id'));
        }

        $sell = $query->firstOrFail();

        foreach ($sell->sell_lines as $key => $value) {
            if (!empty($value->sub_unit_id)) {
                $formated_sell_line = $this->transactionUtil->recalculateSellLineTotals($business_id, $value);
                $sell->sell_lines[$key] = $formated_sell_line;
            }
        }

        $payment_types = $this->transactionUtil->payment_types(null, false, false, false, true);

        $order_taxes = [];
        if (!empty($sell->tax)) {
            if ($sell->tax->is_tax_group) {
                $order_taxes = $this->transactionUtil->sumGroupTaxDetails($this->transactionUtil->groupTaxDetails($sell->tax, $sell->tax_amount));
            } else {
                $order_taxes[$sell->tax->name] = $sell->tax_amount;
            }
        }

        $business_details = $this->businessUtil->getDetails($business_id);
        $pos_settings = empty($business_details->pos_settings) ? $this->businessUtil->defaultPosSettings() : json_decode($business_details->pos_settings, true);
        $shipping_statuses = $this->transactionUtil->shipping_statuses();
        $shipping_status_colors = $this->shipping_status_colors;
        $common_settings = session()->get('business.common_settings');
        $is_warranty_enabled = !empty($common_settings['enable_product_warranty']) ? true : false;
        $company = Business::find($business_id);
        
        return view('sale_pos.show')
            ->with(compact(
                'taxes',
                'sell',
                'payment_types',
                'order_taxes',
                'pos_settings',
                'shipping_statuses',
                'shipping_status_colors',
                'is_warranty_enabled',
                'payment_types',
                'company'
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
        if (!auth()->user()->can('direct_sell.access')) {
            abort(403, 'Unauthorized action.');
        }

        //Check if the transaction can be edited or not.
        $edit_days = request()->session()->get('business.transaction_edit_days');
        if (!$this->transactionUtil->canBeEdited($id, $edit_days)) {
            return back()
                ->with('status', [
                    'success' => 0,
                    'msg' => __('messages.transaction_edit_not_allowed', ['days' => $edit_days])
                ]);
        }

        //Check if return exist then not allowed
        if ($this->transactionUtil->isReturnExist($id)) {
            return back()->with('status', [
                'success' => 0,
                'msg' => __('lang_v1.return_exist')
            ]);
        }

        $business_id = request()->session()->get('user.business_id');

        $business_details = $this->businessUtil->getDetails($business_id);
        $taxes = TaxRate::forBusinessDropdown($business_id, true, true);

        $transaction = Transaction::where('business_id', $business_id)
            ->with(['price_group', 'types_of_service'])
            ->where('type', 'sell')
            ->findorfail($id);
        //Selling Price Group Dropdown
        $price_groups = SellingPriceGroup::forDropdown($business_id);
        $location_id = $transaction->location_id;
        $location_printer_type = BusinessLocation::find($location_id)->receipt_printer_type;

        $sell_details = TransactionSellLine::join(
            'products AS p',
            'transaction_sell_lines.product_id',
            '=',
            'p.id'
        )
            ->join(
                'variations AS variations',
                'transaction_sell_lines.variation_id',
                '=',
                'variations.id'
            )
            ->join(
                'product_variations AS pv',
                'variations.product_variation_id',
                '=',
                'pv.id'
            )
            ->leftjoin('variation_location_details AS vld', function ($join) use ($location_id) {
                $join->on('variations.id', '=', 'vld.variation_id')
                    ->where('vld.location_id', '=', $location_id);
            })
            ->leftjoin('units', 'units.id', '=', 'p.unit_id')
            ->where('transaction_sell_lines.transaction_id', $id)
            ->select(
                DB::raw("IF(pv.is_dummy = 0, CONCAT(p.name, ' (', pv.name, ':',variations.name, ')'), p.name) AS product_name"),
                'p.id as product_id',
                'p.enable_stock',
                'p.name as product_actual_name',
                'pv.name as product_variation_name',
                'pv.is_dummy as is_dummy',
                'variations.name as variation_name',
                'variations.sub_sku',
                'p.barcode_type',
                'p.enable_sr_no',
                'variations.id as variation_id',
                'units.short_name as unit',
                'units.allow_decimal as unit_allow_decimal',
                'transaction_sell_lines.tax_id as tax_id',
                'transaction_sell_lines.item_tax as item_tax',
                'transaction_sell_lines.unit_price as default_sell_price',
                'transaction_sell_lines.unit_price_inc_tax as sell_price_inc_tax',
                'transaction_sell_lines.unit_price_before_discount as unit_price_before_discount',
                'transaction_sell_lines.id as transaction_sell_lines_id',
                'transaction_sell_lines.quantity as quantity_ordered',
                'transaction_sell_lines.sell_line_note as sell_line_note',
                'transaction_sell_lines.lot_no_line_id',
                'transaction_sell_lines.line_discount_type',
                'transaction_sell_lines.line_discount_amount',
                'transaction_sell_lines.res_service_staff_id',
                'transaction_sell_lines.weight_excess',
                'transaction_sell_lines.weight_loss',
                'transaction_sell_lines.last_purchased_price',
                'units.id as unit_id',
                'transaction_sell_lines.sub_unit_id',
                DB::raw('vld.qty_available + transaction_sell_lines.quantity AS qty_available')
            )
            ->get();
        if (!empty($sell_details)) {
            foreach ($sell_details as $key => $value) {
                if ($transaction->status != 'final') {
                    $actual_qty_avlbl = $value->qty_available - $value->quantity_ordered;
                    $sell_details[$key]->qty_available = $actual_qty_avlbl;
                    $value->qty_available = $actual_qty_avlbl;
                }

                $sell_details[$key]->formatted_qty_available = $this->transactionUtil->num_f($value->qty_available);
                $lot_numbers = [];
                if (request()->session()->get('business.enable_lot_number') == 1) {
                    $lot_number_obj = $this->transactionUtil->getLotNumbersFromVariation($value->variation_id, $business_id, $location_id);
                    foreach ($lot_number_obj as $lot_number) {
                        //If lot number is selected added ordered quantity to lot quantity available
                        if ($value->lot_no_line_id == $lot_number->purchase_line_id) {
                            $lot_number->qty_available += $value->quantity_ordered;
                        }

                        $lot_number->qty_formated = $this->transactionUtil->num_f($lot_number->qty_available);
                        $lot_numbers[] = $lot_number;
                    }
                }
                $sell_details[$key]->lot_numbers = $lot_numbers;

                if (!empty($value->sub_unit_id)) {
                    $value = $this->productUtil->changeSellLineUnit($business_id, $value);
                    $sell_details[$key] = $value;
                }

                $sell_details[$key]->formatted_qty_available = $this->transactionUtil->num_f($value->qty_available);
            }
        }

        $commsn_agnt_setting = $business_details->sales_cmsn_agnt;
        $commission_agent = [];
        if ($commsn_agnt_setting == 'user') {
            $commission_agent = User::forDropdown($business_id);
        } elseif ($commsn_agnt_setting == 'cmsn_agnt') {
            $commission_agent = User::saleCommissionAgentsDropdown($business_id);
        }

        $types = [];
        if (auth()->user()->can('supplier.create')) {
            $types['supplier'] = __('report.supplier');
        }
        if (auth()->user()->can('customer.create')) {
            $types['customer'] = __('report.customer');
        }
        if (auth()->user()->can('supplier.create') && auth()->user()->can('customer.create')) {
            $types['both'] = __('lang_v1.both_supplier_customer');
        }
        $customer_groups = ContactGroup::forDropdown($business_id);

        $transaction->transaction_date = $this->transactionUtil->format_date($transaction->transaction_date, true);

        $pos_settings = empty($business_details->pos_settings) ? $this->businessUtil->defaultPosSettings() : json_decode($business_details->pos_settings, true);

        $waiters = null;
        if ($this->productUtil->isModuleEnabled('service_staff') && !empty($pos_settings['inline_service_staff'])) {
            $waiters = $this->productUtil->serviceStaffDropdown($business_id);
        }

        $invoice_schemes = [];
        $default_invoice_schemes = null;

        if ($transaction->status == 'draft') {
            $invoice_schemes = InvoiceScheme::forDropdown($business_id);
            $default_invoice_schemes = InvoiceScheme::getDefault($business_id);
        }

        $redeem_details = [];
        if (request()->session()->get('business.enable_rp') == 1) {
            $redeem_details = $this->transactionUtil->getRewardRedeemDetails($business_id, $transaction->contact_id);

            $redeem_details['points'] += $transaction->rp_redeemed;
            $redeem_details['points'] -= $transaction->rp_earned;
        }

        $edit_discount = auth()->user()->can('edit_product_discount_from_sale_screen');
        $edit_price = auth()->user()->can('edit_product_price_from_sale_screen');

        //Accounts
        $accounts = [];
        if ($this->moduleUtil->isModuleEnabled('account')) {
            $accounts = Account::forDropdown($business_id, true, false);
        }

        $contact_id = $this->businessUtil->check_customer_code($business_id, 1);
        $type = 'customer'; //contact type /used in quick add contact
        $shipping_statuses = $this->transactionUtil->shipping_statuses();

        $user = User::where('id', Auth::user()->id)->select('toggle_popup')->first();
        if (!empty($user)) {
            $toggle_popup = $user->toggle_popup;
        } else {
            $toggle_popup = 1;
        }

        $is_sales_page = 1;

        return view('sell.edit')
            ->with(compact('is_sales_page', 'business_details', 'toggle_popup', 'contact_id', 'taxes', 'sell_details', 'transaction', 'commission_agent', 'type', 'types', 'customer_groups', 'price_groups', 'pos_settings', 'waiters', 'invoice_schemes', 'default_invoice_schemes', 'redeem_details', 'edit_discount', 'edit_price', 'accounts', 'shipping_statuses'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    // public function update(Request $request, $id)
    // {
    //     //
    // }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    // public function destroy($id)
    // {
    //     //
    // }

    /**
     * Display a listing sell drafts.
     *
     * @return \Illuminate\Http\Response
     */
    public function getDrafts()
    {
        if (!auth()->user()->can('list_drafts')) {
            abort(403, 'Unauthorized action.');
        }

        return view('sale_pos.draft');
    }

    /**
     * Display a listing sell quotations.
     *
     * @return \Illuminate\Http\Response
     */
    public function getQuotations()
    {
        $business_id = request()->session()->get('business.id');
        if (!auth()->user()->can('list_quotations')) {
            abort(403, 'Unauthorized action.');
        }

        if (!$this->moduleUtil->hasThePermissionInSubscription($business_id, 'list_quotation')) {
            abort(403, 'Unauthorized action.');
        }

        return view('sale_pos.quotations');
    }

    /**
     * Display a listing customer orders.
     *
     * @return \Illuminate\Http\Response
     */
    public function getCustomerOrders()
    {
        $business_id = request()->session()->get('business.id');
        $subscription = Subscription::active_subscription($business_id);
        if (!empty($subscription)) {
            $pacakge_details = $subscription->package_details;
            if ($pacakge_details['customer_order_own_customer'] == 0 && $pacakge_details['customer_order_general_customer'] == 0) {
                abort(403, 'Unauthorized action');
            }
        }
        if (request()->ajax()) {
            $business_id = request()->session()->get('user.business_id');
            $is_quotation = request()->only('is_quotation', 0);
            $customer_order = request()->only('customer_order', 0);

            $sells = Transaction::leftJoin('contacts', 'transactions.contact_id', '=', 'contacts.id')
                ->join(
                    'business_locations AS bl',
                    'transactions.location_id',
                    '=',
                    'bl.id'
                )
                ->where('transactions.business_id', $business_id)
                ->where('transactions.type', 'sell')
                ->where('is_quotation', $is_quotation)
                ->where('is_customer_order', 1)
                ->select(
                    'transactions.id',
                    'transactions.order_status',
                    'transactions.is_customer_order',
                    'transaction_date',
                    'invoice_no',
                    'contacts.name',
                    'bl.name as business_location',
                    'is_direct_sale'
                );
            if (!empty($customer_order)) {
                $sells->where('transactions.is_customer_order', 1);
            }

            $permitted_locations = auth()->user()->permitted_locations();
            if ($permitted_locations != 'all') {
                $sells->whereIn('transactions.location_id', $permitted_locations);
            }

            if (!empty(request()->start_date) && !empty(request()->end_date)) {
                $start = request()->start_date;
                $end =  request()->end_date;
                $sells->whereDate('transaction_date', '>=', $start)
                    ->whereDate('transaction_date', '<=', $end);
            }
            $sells->groupBy('transactions.id');

            return Datatables::of($sells)
                ->addColumn(
                    'action',
                    '<a href="#" data-href="{{action(\'SellController@show\', [$id])}}" class="btn btn-xs btn-success btn-modal" data-container=".view_modal"><i class="fa fa-external-link" aria-hidden="true"></i> @lang("messages.view")</a>
                &nbsp;
             
                <!-- <a target="_blank" href="{{action(\'SellController@edit\', [$id])}}" class="btn btn-xs btn-primary"><i class="glyphicon glyphicon-edit"></i>  @lang("lang_v1.create_sale_invoice")</a>  -->
              
                <a target="_blank" href="{{action(\'SellPosController@edit\', [$id])}}" class="btn btn-xs btn-primary"><i class="glyphicon glyphicon-edit"></i>  @lang("lang_v1.create_pos_invoice")</a>
            

                &nbsp; 
                <a href="#" class="print-invoice btn btn-xs btn-info" data-href="{{route(\'sell.printInvoice\', [$id])}}"><i class="fa fa-print" aria-hidden="true"></i> @lang("messages.print")</a>

                &nbsp; <a href="{{action(\'SellPosController@destroy\', [$id])}}" class="btn btn-xs btn-danger delete-sale"><i class="fa fa-trash"></i>  @lang("messages.delete")</a>
                '
                )
                ->removeColumn('id')
                ->editColumn('transaction_date', '{{@format_date($transaction_date)}}')
                ->editColumn(
                    'order_status',
                    '<span class="label @order_status($order_status)">{{__(\'lang_v1.\' . $order_status)}}
                        </span></span>'
                )
                ->setRowAttr([
                    'data-href' => function ($row) {
                        if (auth()->user()->can("sell.view")) {
                            return  action('SellController@show', [$row->id]);
                        } else {
                            return '';
                        }
                    }
                ])
                ->rawColumns(['action', 'invoice_no', 'transaction_date', 'order_status'])
                ->make(true);
        }

        return view('sale_pos.customer_order');
    }

    /**
     * Display a listing customer uploaded orders.
     *
     * @return \Illuminate\Http\Response
     */
    public function getCustomerUploadedOrders()
    {
        $business_id = request()->session()->get('business.id');
        $subscription = Subscription::active_subscription($business_id);
        if (!empty($subscription)) {
            $pacakge_details = $subscription->package_details;
            if ($pacakge_details['customer_order_own_customer'] == 0 && $pacakge_details['customer_order_general_customer'] == 0) {
                abort(403, 'Unauthorized action');
            }
        }
        if (request()->ajax()) {
            $is_quotation = request()->only('is_quotation', 0);
            $uploaded_orders = UploadedOrder::leftJoin('contacts', 'uploaded_orders.contact_id', 'contacts.contact_id')
                ->leftJoin('business', 'uploaded_orders.business_id', 'business.id')
                ->leftJoin('transactions', 'uploaded_orders.transaction_id', 'transactions.id')
                ->leftJoin('business_locations', 'transactions.location_id', 'business_locations.id')
                ->where('business.id', $business_id)
                ->select(
                    'uploaded_orders.*',
                    'transactions.order_status',
                    'uploaded_orders.created_at as transaction_date',
                    'invoice_no',
                    'business_locations.name as location name',
                    'contacts.name'
                );

            if (!empty(request()->start_date) && !empty(request()->end_date)) {
                $start = request()->start_date;
                $end =  request()->end_date;
                $uploaded_orders->whereDate('uploaded_orders.created_at', '>=', $start)
                    ->whereDate('uploaded_orders.created_at', '<=', $end);
            }


            return Datatables::of($uploaded_orders)
                ->addColumn(
                    'action',
                    '<a href="#" data-href="{{action(\'SellController@getImage\', [$id])}}" class="btn btn-xs btn-success btn-modal" data-container=".view_modal"><i class="fa fa-external-link" aria-hidden="true"></i> @lang("messages.view")</a>
                    '
                )
                ->removeColumn('id')
                ->editColumn('transaction_date', '{{$transaction_date}}')
                ->editColumn(
                    'order_status',
                    '<span class="label @order_status($order_status)">{{__(\'lang_v1.\' . $order_status)}}
                        </span></span>'
                )
                ->setRowAttr([
                    'data-href' => function ($row) {
                        return  action('SellController@getImage', [$row->id]);
                    }
                ])
                ->rawColumns(['action', 'invoice_no', 'transaction_date', 'order_status'])
                ->make(true);
        }
        return view('sale_pos.customer_uploaded_order');
    }


    public function getImage($id)
    {
        $order_uploaded = UploadedOrder::findOrFail($id);
        $image_url = $order_uploaded->image;

        return view('ecom_customer.order.get_image')->with(compact('image_url'));
    }

    /**
     * Send the datatable response for draft or quotations.
     *
     * @return \Illuminate\Http\Response
     */
    public function getDraftDatables()
    {
        $business_id = request()->session()->get('user.business_id');
        if (!$this->moduleUtil->hasThePermissionInSubscription($business_id, 'list_draft')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            $is_quotation = request()->only('is_quotation', 0);

            $sells = Transaction::leftJoin('contacts', 'transactions.contact_id', '=', 'contacts.id')
                ->join(
                    'business_locations AS bl',
                    'transactions.location_id',
                    '=',
                    'bl.id'
                )
                ->where('transactions.business_id', $business_id)
                ->where('transactions.type', 'sell')
                ->where('transactions.status', 'draft')
                ->where('is_quotation', $is_quotation)
                ->select(
                    'transactions.id',
                    'transaction_date',
                    'invoice_no',
                    'contacts.name',
                    'bl.name as business_location',
                    'is_direct_sale'
                );

            $permitted_locations = auth()->user()->permitted_locations();
            if ($permitted_locations != 'all') {
                $sells->whereIn('transactions.location_id', $permitted_locations);
            }

            if (!empty(request()->start_date) && !empty(request()->end_date)) {
                $start = request()->start_date;
                $end =  request()->end_date;
                $sells->whereDate('transaction_date', '>=', $start)
                    ->whereDate('transaction_date', '<=', $end);
            }
            $sells->groupBy('transactions.id');

            return Datatables::of($sells)
                ->addColumn(
                    'action',
                    '<a href="#" data-href="{{action(\'SellController@show\', [$id])}}" class="btn btn-xs btn-success btn-modal" data-container=".view_modal"><i class="fa fa-external-link" aria-hidden="true"></i> @lang("messages.view")</a>
                    &nbsp;
                    @if($is_direct_sale == 1)
                        <a target="_blank" href="{{action(\'SellController@edit\', [$id])}}" class="btn btn-xs btn-primary"><i class="glyphicon glyphicon-edit"></i>  @lang("messages.edit")</a>
                    @else
                    <a target="_blank" href="{{action(\'SellPosController@edit\', [$id])}}" class="btn btn-xs btn-primary"><i class="glyphicon glyphicon-edit"></i>  @lang("messages.edit")</a>
                    @endif

                    &nbsp; 
                    <a href="#" class="print-invoice btn btn-xs btn-info" data-href="{{route(\'sell.printInvoice\', [$id])}}"><i class="fa fa-print" aria-hidden="true"></i> @lang("messages.print")</a>

                    &nbsp; <a href="{{action(\'SellPosController@destroy\', [$id])}}" class="btn btn-xs btn-danger delete-sale"><i class="fa fa-trash"></i>  @lang("messages.delete")</a>
                    '
                )
                ->removeColumn('id')
                ->editColumn('transaction_date', '{{@format_date($transaction_date)}}')
                ->setRowAttr([
                    'data-href' => function ($row) {
                        if (auth()->user()->can("sell.view")) {
                            return  action('SellController@show', [$row->id]);
                        } else {
                            return '';
                        }
                    }
                ])
                ->rawColumns(['action', 'invoice_no', 'transaction_date'])
                ->make(true);
        }
    }


    /**
     * Creates copy of the requested sale.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function duplicateSell($id)
    {
        if (!auth()->user()->can('sell.create')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $business_id = request()->session()->get('user.business_id');
            $user_id = request()->session()->get('user.id');

            $transaction = Transaction::where('business_id', $business_id)
                ->where('type', 'sell')
                ->findorfail($id);
            $duplicate_transaction_data = [];
            foreach ($transaction->toArray() as $key => $value) {
                if (!in_array($key, ['id', 'created_at', 'updated_at'])) {
                    $duplicate_transaction_data[$key] = $value;
                }
            }
            $duplicate_transaction_data['status'] = 'draft';
            $duplicate_transaction_data['payment_status'] = null;
            $duplicate_transaction_data['transaction_date'] =  \Carbon::now();
            $duplicate_transaction_data['created_by'] = $user_id;
            $duplicate_transaction_data['invoice_token'] = null;

            DB::beginTransaction();
            $duplicate_transaction_data['invoice_no'] = $this->transactionUtil->getInvoiceNumber($business_id, 'draft', $duplicate_transaction_data['location_id']);

            //Create duplicate transaction
            $duplicate_transaction = Transaction::create($duplicate_transaction_data);

            //Create duplicate transaction sell lines
            $duplicate_sell_lines_data = [];

            foreach ($transaction->sell_lines as $sell_line) {
                $new_sell_line = [];
                foreach ($sell_line->toArray() as $key => $value) {
                    if (!in_array($key, ['id', 'transaction_id', 'created_at', 'updated_at', 'lot_no_line_id'])) {
                        $new_sell_line[$key] = $value;
                    }
                }

                $duplicate_sell_lines_data[] = $new_sell_line;
            }

            $duplicate_transaction->sell_lines()->createMany($duplicate_sell_lines_data);

            DB::commit();

            $output = [
                'success' => 0,
                'msg' => trans("lang_v1.duplicate_sell_created_successfully")
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

            $output = [
                'success' => 0,
                'msg' => trans("messages.something_went_wrong")
            ];
        }

        if (!empty($duplicate_transaction)) {
            if ($duplicate_transaction->is_direct_sale == 1) {
                return redirect()->action('SellController@edit', [$duplicate_transaction->id])->with(['status', $output]);
            } else {
                return redirect()->action('SellPosController@edit', [$duplicate_transaction->id])->with(['status', $output]);
            }
        } else {
            abort(404, 'Not Found.');
        }
    }

    /**
     * Shows modal to edit shipping details.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function editShipping($id)
    {
        if (!auth()->user()->can('access_shipping')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');

        $transaction = Transaction::where('business_id', $business_id)
            ->findorfail($id);
        $shipping_statuses = $this->transactionUtil->shipping_statuses();

        return view('sell.partials.edit_shipping')
            ->with(compact('transaction', 'shipping_statuses'));
    }

    /**
     * Update shipping.
     *
     * @param  Request $request, int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateShipping(Request $request, $id)
    {
        if (!auth()->user()->can('access_shipping')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $input = $request->only([
                'shipping_details', 'shipping_address',
                'shipping_status', 'delivered_to'
            ]);
            $business_id = $request->session()->get('user.business_id');

            $transaction = Transaction::where('business_id', $business_id)
                ->where('id', $id)
                ->update($input);

            $output = [
                'success' => 1,
                'msg' => trans("lang_v1.updated_success")
            ];
        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

            $output = [
                'success' => 0,
                'msg' => trans("messages.something_went_wrong")
            ];
        }

        return $output;
    }

    /**
     * Display list of shipments.
     *
     * @return \Illuminate\Http\Response
     */
    public function shipments()
    {
        $business_id = request()->session()->get('business.id');
        if (!auth()->user()->can('access_shipping')) {
            abort(403, 'Unauthorized action.');
        }
        if (!$this->moduleUtil->hasThePermissionInSubscription($business_id, 'shipment')) {
            abort(403, 'Unauthorized action.');
        }


        $shipping_statuses = $this->transactionUtil->shipping_statuses();

        return view('sell.shipments')->with(compact('shipping_statuses'));
    }

    /**
     * Display list of shipments.
     *
     * @return \Illuminate\Http\Response
     */
    public function getInvoiveNo()
    {
        $business_id = request()->session()->get('business.id');
        $location_id = request()->location_id;
        $business = Business::where('id', $business_id)->first();
        $pos_settings = empty($business->pos_settings) ? $this->businessUtil->defaultPosSettings() : json_decode($business->pos_settings, true);
        $scheme = $this->getInvoiceScheme($business_id, $location_id);
        if ($scheme->scheme_type == 'blank') {
            $prefix = $scheme->prefix;
        } else {
            $prefix = date('Y') . '-';
        }


        $org_invoice_no = Transaction::where('business_id', $business_id)->where('is_duplicate', 0)->where('type', '!=', 'opening_stock')->select('invoice_no')->orderBy('id', 'desc')->first();
        if (empty($org_invoice_no)) {
            $orignal_invoice_no  = '0001';
        } else {
            $o_invo = (int) str_replace($prefix, '', $org_invoice_no->invoice_no);
            $o_invo++;
            $o_invo = sprintf("%04d", $o_invo);
            $orignal_invoice_no  = $o_invo;
        }
        $orignal_invoice_no  =  $prefix . $orignal_invoice_no;

        $dup_invoice_no = Transaction::where('business_id', $business_id)->where('is_duplicate', 1)->select('invoice_no')->orderBy('id', 'desc')->first();
        if (empty($dup_invoice_no)) {
            $d_prefix = '';
            if (!empty($pos_settings['enable_prefix_duplicate_invoice'])) {
                $d_prefix = $pos_settings['duplicate_invoice_prefix'];
            }
            $duplicate_invoice_no  = $d_prefix . '0001';
        } else {
            $d_prefix = '';
            if (!empty($pos_settings['enable_prefix_duplicate_invoice'])) {
                $d_prefix = $pos_settings['duplicate_invoice_prefix'];
            }
            $d_invo =  preg_replace("/[^0-9]{1,4}/", '', $dup_invoice_no->invoice_no);;
            $d_invo++;
            $duplicate_invoice_no  =  $d_prefix . $d_invo;
        }

        $default_accounts = BusinessLocation::where('id',  $location_id)->select('default_payment_accounts')->first();
        return (['orignal_invoice_no' => $orignal_invoice_no, 'duplicate_invoice_no' => $duplicate_invoice_no, 'default_accounts' => !empty($default_accounts) ? $default_accounts->default_payment_accounts : null]);
    }

    private function getInvoiceScheme($business_id, $location_id)
    {
        $scheme_id = BusinessLocation::where('business_id', $business_id)
            ->where('id', $location_id)
            ->first()
            ->invoice_scheme_id;
        if (!empty($scheme_id) && $scheme_id != 0) {
            $scheme = InvoiceScheme::find($scheme_id);
        }

        //Check if scheme is not found then return default scheme
        if (empty($scheme)) {
            $scheme = InvoiceScheme::where('business_id', $business_id)
                ->where('is_default', 1)
                ->first();
        }

        return $scheme;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function overLimitSales()
    {
        if (!auth()->user()->can('sell.view') && !auth()->user()->can('sell.create') && !auth()->user()->can('direct_sell.access') && !auth()->user()->can('view_own_sell_only')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');
        if (!$this->moduleUtil->hasThePermissionInSubscription($business_id, 'over_limit_sales')) {
            abort(403, 'Unauthorized action.');
        }

        $is_woocommerce = $this->moduleUtil->isModuleInstalled('Woocommerce');
        $is_tables_enabled = $this->transactionUtil->isModuleEnabled('tables');
        $is_service_staff_enabled = $this->transactionUtil->isModuleEnabled('service_staff');

        if (request()->ajax()) {
            $payment_types = $this->transactionUtil->payment_types(null, false, false, false, true);
            $with = [];
            $shipping_statuses = $this->transactionUtil->shipping_statuses();
            $sells = Transaction::leftJoin('contacts', 'transactions.contact_id', '=', 'contacts.id')
                // ->leftJoin('transaction_payments as tp', 'transactions.id', '=', 'tp.transaction_id')
                ->leftJoin('transaction_sell_lines as tsl', 'transactions.id', '=', 'tsl.transaction_id')
                ->leftJoin('products', 'tsl.product_id', '=', 'products.id')
                ->leftJoin('users as u', 'transactions.requested_by', '=', 'u.id')
                ->leftJoin('users as ss', 'transactions.approved_user', '=', 'ss.id')
                ->leftJoin('res_tables as tables', 'transactions.res_table_id', '=', 'tables.id')
                ->join(
                    'business_locations AS bl',
                    'transactions.location_id',
                    '=',
                    'bl.id'
                )
                ->leftJoin(
                    'transactions AS SR',
                    'transactions.id',
                    '=',
                    'SR.return_parent_id'
                )
                ->leftJoin(
                    'types_of_services AS tos',
                    'transactions.types_of_service_id',
                    '=',
                    'tos.id'
                )
                ->where('transactions.business_id', $business_id)
                ->where('transactions.type', 'sell')
                ->where('transactions.is_over_limit_credit_sale', 1)
                ->whereIn('transactions.status', ['final', 'order'])
                ->select(
                    'transactions.id',
                    'transactions.transaction_date',
                    'transactions.is_direct_sale',
                    'transactions.invoice_no',
                    'contacts.name',
                    'contacts.mobile',
                    'transactions.payment_status',
                    'transactions.final_total',
                    'transactions.tax_amount',
                    'transactions.discount_amount',
                    'transactions.discount_type',
                    'transactions.total_before_tax',
                    'transactions.rp_redeemed',
                    'transactions.rp_redeemed_amount',
                    'transactions.rp_earned',
                    'transactions.types_of_service_id',
                    'transactions.shipping_status',
                    'transactions.pay_term_number',
                    'transactions.pay_term_type',
                    'transactions.additional_notes',
                    'transactions.staff_note',
                    'transactions.shipping_details',
                    'transactions.commission_agent',
                    'transactions.ref_no as ref_no',
                    'transactions.over_limit_amount',
                    'transactions.customer_limit',
                    DB::raw("CONCAT(COALESCE(ss.surname, ''),' ',COALESCE(ss.first_name, ''),' ',COALESCE(ss.last_name,'')) as approved_by"),
                    DB::raw("CONCAT(COALESCE(u.surname, ''),' ',COALESCE(u.first_name, ''),' ',COALESCE(u.last_name,'')) as requested_by"),
                    DB::raw('(SELECT SUM(IF(TP.is_return = 1,-1*TP.amount,TP.amount)) FROM transaction_payments AS TP WHERE
                        TP.transaction_id=transactions.id) as total_paid'),
                    'bl.name as business_location',
                    DB::raw('COUNT(SR.id) as return_exists'),
                    DB::raw('(SELECT SUM(TP2.amount) FROM transaction_payments AS TP2 WHERE
                        TP2.transaction_id=SR.id ) as return_paid'),
                    DB::raw('COALESCE(SR.final_total, 0) as amount_return'),
                    'SR.id as return_transaction_id',
                    'tos.name as types_of_service_name',
                    'transactions.service_custom_field_1',
                    DB::raw('COUNT( DISTINCT tsl.id) as total_items'),
                    'tables.name as table_name'
                )->with('sell_lines');

            $permitted_locations = auth()->user()->permitted_locations();
            if ($permitted_locations != 'all') {
                $sells->whereIn('transactions.location_id', $permitted_locations);
            }

            //Add condition for created_by,used in sales representative sales report
            if (request()->has('created_by')) {
                $created_by = request()->get('created_by');
                if (!empty($created_by)) {
                    $sells->where('transactions.created_by', $created_by);
                }
            }

            if (!auth()->user()->can('direct_sell.access') && auth()->user()->can('view_own_sell_only')) {
                $sells->where('transactions.created_by', request()->session()->get('user.id'));
            }

            if (!empty(request()->input('payment_status')) && request()->input('payment_status') != 'overdue') {
                $sells->where('transactions.payment_status', request()->input('payment_status'));
            } elseif (request()->input('payment_status') == 'overdue') {
                $sells->whereIn('transactions.payment_status', ['due', 'partial'])
                    ->whereNotNull('transactions.pay_term_number')
                    ->whereNotNull('transactions.pay_term_type')
                    ->whereRaw("IF(transactions.pay_term_type='days', DATE_ADD(transactions.transaction_date, INTERVAL transactions.pay_term_number DAY) < CURDATE(), DATE_ADD(transactions.transaction_date, INTERVAL transactions.pay_term_number MONTH) < CURDATE())");
            }

            //Add condition for location,used in sales representative expense report
            if (request()->has('location_id')) {
                $location_id = request()->get('location_id');
                if (!empty($location_id)) {
                    $sells->where('transactions.location_id', $location_id);
                }
            }

            if (!empty(request()->input('rewards_only')) && request()->input('rewards_only') == true) {
                $sells->where(function ($q) {
                    $q->whereNotNull('transactions.rp_earned')
                        ->orWhere('transactions.rp_redeemed', '>', 0);
                });
            }

            if (!empty(request()->customer_id)) {
                $customer_id = request()->customer_id;
                $sells->where('contacts.id', $customer_id);
            }
            if (!empty(request()->start_date) && !empty(request()->end_date)) {
                $start = request()->start_date;
                $end =  request()->end_date;
                $sells->whereDate('transactions.transaction_date', '>=', $start)
                    ->whereDate('transactions.transaction_date', '<=', $end);
            }

            //Check is_direct sell
            if (request()->has('is_direct_sale')) {
                $is_direct_sale = request()->is_direct_sale;
                if ($is_direct_sale == 0) {
                    $sells->where('transactions.is_direct_sale', 0);
                    $sells->whereNull('transactions.sub_type');
                }
            }

            //Add condition for commission_agent,used in sales representative sales with commission report
            if (request()->has('commission_agent')) {
                $commission_agent = request()->get('commission_agent');
                if (!empty($commission_agent)) {
                    $sells->where('transactions.commission_agent', $commission_agent);
                }
            }

            if ($is_woocommerce) {
                $sells->addSelect('transactions.woocommerce_order_id');
                if (request()->only_woocommerce_sells) {
                    $sells->whereNotNull('transactions.woocommerce_order_id');
                }
            }

            if (!empty(request()->list_for) && request()->list_for == 'service_staff_report') {
                $sells->whereNotNull('transactions.res_waiter_id');
                $sells->leftJoin('users as ss', 'ss.id', '=', 'transactions.res_waiter_id');
                $sells->addSelect(
                    DB::raw('CONCAT(COALESCE(ss.first_name, ""), COALESCE(ss.last_name, "")) as service_staff')
                );
            }

            if (!empty(request()->res_waiter_id)) {
                $sells->where('transactions.res_waiter_id', request()->res_waiter_id);
            }

            if (!empty(request()->input('sub_type'))) {
                $sells->where('transactions.sub_type', request()->input('sub_type'));
            }

            if (!empty(request()->input('created_by'))) {
                $sells->where('transactions.created_by', request()->input('created_by'));
            }

            if (!empty(request()->input('references'))) {
                $sells->where('transactions.ref_no', request()->input('references'));
            }

            if (!empty(request()->input('approved_user'))) {
                $sells->where('transactions.approved_user', request()->input('approved_user'));
            }

            if (!empty(request()->input('sales_cmsn_agnt'))) {
                $sells->where('transactions.commission_agent', request()->input('sales_cmsn_agnt'));
            }

            if (!empty(request()->input('service_staffs'))) {
                $sells->where('transactions.res_waiter_id', request()->input('service_staffs'));
            }
            $only_shipments = request()->only_shipments == 'true' ? true : false;
            if ($only_shipments && auth()->user()->can('access_shipping')) {
                $sells->whereNotNull('transactions.shipping_status');
            }

            if (!empty(request()->input('shipping_status'))) {
                $sells->where('transactions.shipping_status', request()->input('shipping_status'));
            }

            if (!empty(request()->input('invoice_no'))) {
                $sells->where('transactions.invoice_no', request()->input('invoice_no'));
            }

            $sells->groupBy('transactions.id');

            if (!empty(request()->suspended)) {
                $with = ['sell_lines'];

                if ($is_tables_enabled) {
                    $with[] = 'table';
                }

                if ($is_service_staff_enabled) {
                    $with[] = 'service_staff';
                }

                $sales = $sells->where('transactions.is_suspend', 1)
                    ->with($with)
                    ->addSelect('transactions.is_suspend', 'transactions.res_table_id', 'transactions.res_waiter_id', 'transactions.additional_notes')
                    ->get();

                return view('sale_pos.partials.suspended_sales_modal')->with(compact('sales', 'is_tables_enabled', 'is_service_staff_enabled'));
            }

            $with[] = 'payment_lines';
            if (!empty($with)) {
                $sells->with($with);
            }

            //$business_details = $this->businessUtil->getDetails($business_id);
            if ($this->businessUtil->isModuleEnabled('subscription')) {
                $sells->addSelect('transactions.is_recurring', 'transactions.recur_parent_id');
            }
            $datatable = Datatables::of($sells)
                ->addColumn(
                    'action',
                    function ($row) use ($only_shipments) {
                        $html = '<div class="btn-group">
                                    <button type="button" class="btn btn-info dropdown-toggle btn-xs" 
                                        data-toggle="dropdown" aria-expanded="false">' .
                            __("messages.actions") .
                            '<span class="caret"></span><span class="sr-only">Toggle Dropdown
                                        </span>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-left" role="menu">';


                        $html .= '<li><a href="#" data-href="' . action("SellController@show", [$row->id]) . '" class="btn-modal" data-container=".view_modal"><i class="fa fa-external-link" aria-hidden="true"></i> ' . __("messages.view") . '</a></li>';



                        $html .= '</ul></div>';

                        return $html;
                    }
                )
                ->removeColumn('id')
                ->editColumn(
                    'final_total',
                    function ($row) {
                        return '<span class="display_currency final-total" data-currency_symbol="true" data-orig-value="' . $this->productUtil->num_f($row->final_total) . '">' . $this->productUtil->num_f($row->final_total) . '</span>';
                    }
                )
                ->editColumn(
                    'tax_amount',
                    function ($row) {
                        return '<span class="display_currency total-tax" data-currency_symbol="true" data-orig-value="' . $this->productUtil->num_f($row->tax_amount) . '">' . $this->productUtil->num_f($row->tax_amount) . '</span>';
                    }
                )
                ->editColumn(
                    'total_paid',
                    function ($row) {
                        return '<span class="display_currency total-paid" data-currency_symbol="true" data-orig-value="' . $this->productUtil->num_f($row->total_paid) . '">' . $this->productUtil->num_f($row->total_paid) . '</span>';
                    }
                )
                ->editColumn(
                    'total_before_tax',
                    function ($row) {
                        return '<span class="display_currency total_before_tax" data-currency_symbol="true" data-orig-value="' . $this->productUtil->num_f($row->total_before_tax) . '">' . $this->productUtil->num_f($row->total_before_tax) . '</span>';
                    }
                )
                ->editColumn(
                    'discount_amount',
                    function ($row) {
                        $discount = !empty($row->discount_amount) ? $row->discount_amount : 0;

                        if (!empty($discount) && $row->discount_type == 'percentage') {
                            $discount = $row->total_before_tax * ($discount / 100);
                        }

                        return '<span class="display_currency total-discount" data-currency_symbol="true" data-orig-value="' . $this->productUtil->num_f($discount) . '">' . $this->productUtil->num_f($discount) . '</span>';
                    }
                )
                ->editColumn('transaction_date', '{{@format_datetime($transaction_date)}}')
                ->editColumn(
                    'payment_status',
                    function ($row) {
                        $payment_status = Transaction::getPaymentStatus($row);
                        return (string) view('sell.partials.payment_status', ['payment_status' => $payment_status, 'id' => $row->id]);
                    }
                )
                ->editColumn(
                    'types_of_service_name',
                    '<span class="service-type-label" data-orig-value="{{$types_of_service_name}}" data-status-name="{{$types_of_service_name}}">{{$types_of_service_name}}</span>'
                )
                ->addColumn('total_remaining', function ($row) {
                    $total_remaining =  $row->final_total - $row->total_paid;
                    $total_remaining_html = '<span class="display_currency payment_due" data-currency_symbol="true" data-orig-value="' . $this->productUtil->num_f($total_remaining) . '">' . $this->productUtil->num_f($total_remaining) . '</span>';


                    return $total_remaining_html;
                })
                ->addColumn('return_due', function ($row) {
                    $return_due_html = '';
                    if (!empty($row->return_exists)) {
                        // $return_due = $row->amount_return - $row->return_paid;
                        $return_due = $row->final_total - $row->total_paid - $row->return_paid;
                        $return_due_html .= '<a href="' . action("TransactionPaymentController@show", [$row->return_transaction_id]) . '" class="view_purchase_return_payment_modal"><span class="display_currency sell_return_due" data-currency_symbol="true" data-orig-value="' . $this->productUtil->num_f($return_due) . '">' . $this->productUtil->num_f($return_due) . '</span></a>';
                    }

                    return $return_due_html;
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
                ->editColumn('shipping_status', function ($row) use ($shipping_statuses) {
                    $status_color = !empty($this->shipping_status_colors[$row->shipping_status]) ? $this->shipping_status_colors[$row->shipping_status] : 'bg-gray';
                    $status = !empty($row->shipping_status) ? '<a href="#" class="btn-modal" data-href="' . action('SellController@editShipping', [$row->id]) . '" data-container=".view_modal"><span class="label ' . $status_color . '">' . $shipping_statuses[$row->shipping_status] . '</span></a>' : '';

                    return $status;
                })
                ->addColumn('payment_methods', function ($row) use ($payment_types) {
                    $methods = array_unique($row->payment_lines->pluck('method')->toArray());
                    $count = count($methods);
                    $payment_method = '';
                    if ($count == 1 && $methods[0] != null) {
                        $payment_method = $payment_types[$methods[0]];
                    } elseif ($count > 1) {
                        $payment_method = __('lang_v1.checkout_multi_pay');
                    }

                    $html = !empty($payment_method) ? '<span class="payment-method" data-orig-value="' . $payment_method . '" data-status-name="' . $payment_method . '">' . $payment_method . '</span>' : '';

                    return $html;
                })
                ->editColumn('total_comission', function ($row) {
                    $commission = 0.00;
                    $cmmsn_application = '';
                    if (!empty($row->commission_agent)) {
                        $agent = User::where('id', $row->commission_agent)->first();
                        $commission_type = $agent->commission_type;
                        $cmmsn_application = $agent->cmmsn_application;
                        $cmmsn_percent = (float) $agent->cmmsn_percent;
                        $cmmsn_units = json_decode($agent->cmmsn_units);
                        if ($cmmsn_application == 'bill') {
                            if ($commission_type == 'fixed') {
                                $commission = $cmmsn_percent;
                            }
                            if ($commission_type == 'percentage') {
                                $commission = ((float) $row->final_total * $cmmsn_percent) / 100;
                            }
                        }
                        if ($cmmsn_application == 'per_unit') {
                            foreach ($row->sell_lines as $sell_line) {
                                $product_unit = Product::where('id', $sell_line->product_id)->first()->unit_id;
                                $product_price = Variation::where('product_id', $sell_line->product_id)->first()->default_sell_price;
                                foreach ($cmmsn_units as $cmmsn_unit) {
                                    if ($cmmsn_unit->unit == $product_unit) {
                                        if ($cmmsn_unit->commission_type == 'fixed') {
                                            $commission += ($cmmsn_unit->cmmsn * $sell_line->quantity);
                                        }
                                        if ($cmmsn_unit->commission_type == 'percentage') {
                                            $commission += (((float) $product_price * $cmmsn_unit->cmmsn) / 100) * $sell_line->quantity;
                                        }
                                    }
                                }
                            }
                        }
                    }

                    // $total_comission = '<span class="display_currency total-comission" data-currency_symbol="true" data-orig-value="' . $commission . '">' . $commission . '</span>';
                    $total_comission = '<span >' . $commission . '</span>';

                    return $total_comission;
                })
                ->editColumn('over_limit_amount', '{{@num_format($over_limit_amount)}}')
                ->setRowAttr([
                    'data-href' => function ($row) {
                        if (auth()->user()->can("sell.view") || auth()->user()->can("view_own_sell_only")) {
                            return  action('SellController@show', [$row->id]);
                        } else {
                            return '';
                        }
                    }
                ]);

            $rawColumns = ['total_comission', 'final_total', 'action', 'total_paid', 'total_remaining', 'payment_status', 'invoice_no', 'discount_amount', 'tax_amount', 'total_before_tax', 'shipping_status', 'types_of_service_name', 'payment_methods', 'return_due'];


            return $datatable->rawColumns($rawColumns)
                ->make(true);
        }
        $business_locations = BusinessLocation::forDropdown($business_id, false);
        $customers = Contact::customersDropdown($business_id, false);
        $sales_representative = User::forDropdown($business_id, false, false, true);

        //Commission agent filter
        $is_cmsn_agent_enabled = request()->session()->get('business.sales_cmsn_agnt');
        $commission_agents = [];
        if (!empty($is_cmsn_agent_enabled)) {
            $commission_agents = User::forDropdown($business_id, false, true, true);
        }

        //Service staff filter
        $service_staffs = null;
        if ($this->productUtil->isModuleEnabled('service_staff')) {
            $service_staffs = $this->productUtil->serviceStaffDropdown($business_id);
        }
        $invoice_nos = Transaction::where('business_id', $business_id)->where('type', 'sell')->where('is_over_limit_credit_sale', '1')->groupBy('invoice_no')->pluck('invoice_no', 'invoice_no');
        $approved_users = Transaction::leftjoin('users', 'transactions.approved_user', 'users.id')->where('transactions.business_id', $business_id)->where('type', 'sell')->where('approved_user', '!=', null)->distinct('approved_user')->pluck('users.username', 'users.id');
        return view('sell.over_limit_sales')
            ->with(compact('approved_users', 'business_locations', 'customers', 'is_woocommerce', 'sales_representative', 'is_cmsn_agent_enabled', 'commission_agents', 'service_staffs', 'is_tables_enabled', 'is_service_staff_enabled', 'invoice_nos'));
    }
}
