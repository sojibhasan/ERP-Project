<?php

namespace App\Http\Controllers;
use App\Business;
use App\BusinessLocation;
use App\Currency;
use App\Transaction;
use App\TransactionPayment;
use App\Utils\BusinessUtil;
use App\Utils\ModuleUtil;
use App\Utils\TransactionUtil;
use App\VariationLocationDetails;
use App\Charts\CommonChart;
use Carbon\Carbon;
use Datatables;
use DB;
use Hyn\Tenancy\Models\Website;
use Hyn\Tenancy\Contracts\Repositories\WebsiteRepository;
use Hyn\Tenancy\Models\Hostname;
use Hyn\Tenancy\Contracts\Repositories\HostnameRepository;
use Modules\Superadmin\Entities\HelpExplanation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\Superadmin\Entities\Subscription;

class HomeController extends Controller
{
    /**
     * All Utils instance.
     *
     */
    protected $businessUtil;
    protected $transactionUtil;
    protected $moduleUtil;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(
        BusinessUtil $businessUtil,
        TransactionUtil $transactionUtil,
        ModuleUtil $moduleUtil
    ) {
        $this->businessUtil = $businessUtil;
        $this->transactionUtil = $transactionUtil;
        $this->moduleUtil = $moduleUtil;
    }
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $business_id = request()->session()->get('user.business_id');
        $subscription = Subscription::active_subscription($business_id);
        if (session()->get('business.is_patient')) {
            return redirect('patient');
        }
        if (session()->get('business.is_hospital') || session()->get('business.is_laboratory')) {
            return redirect('hospital');
        }
        $home_dashboard =  $this->moduleUtil->hasThePermissionInSubscription($business_id, 'home_dashboard');
        $enable_petro_module = $this->moduleUtil->hasThePermissionInSubscription($business_id, 'enable_petro_module');
        /**
         * @author:Afes Oktavianus
         * @since: 25-08-2021
         * @Req :3413
         */
        $enable_petro_dashboard = $this->moduleUtil->hasThePermissionInSubscription($business_id, 'enable_petro_module');
        $enable_petro_task_management 		= $this->moduleUtil->hasThePermissionInSubscription($business_id, 'enable_petro_task_management');
        $enable_petro_pump_management 		= $this->moduleUtil->hasThePermissionInSubscription($business_id, 'enable_petro_pump_management');
        $enable_petro_management_testing 	= $this->moduleUtil->hasThePermissionInSubscription($business_id, 'enable_petro_management_testing'); 
        $enable_petro_meter_reading 		= $this->moduleUtil->hasThePermissionInSubscription($business_id, 'enable_petro_meter_reading');
        $enable_petro_meter_resetting 		= $this->moduleUtil->hasThePermissionInSubscription($business_id, 'enable_petro_meter_resetting');
        $enable_petro_pump_dashboard 		= $this->moduleUtil->hasThePermissionInSubscription($business_id, 'enable_petro_pump_dashboard');
        $enable_petro_pumper_management 	= $this->moduleUtil->hasThePermissionInSubscription($business_id, 'enable_petro_pumper_management');
        $enable_petro_daily_collection 		= $this->moduleUtil->hasThePermissionInSubscription($business_id, 'enable_petro_daily_collection');
        $enable_petro_settlement 		    = $this->moduleUtil->hasThePermissionInSubscription($business_id, 'enable_petro_settlement');
        $enable_petro_list_settlement 		= $this->moduleUtil->hasThePermissionInSubscription($business_id, 'enable_petro_list_settlement');
        $enable_petro_dip_management 		= $this->moduleUtil->hasThePermissionInSubscription($business_id, 'enable_petro_dip_management');
        $report_module                      = $this->moduleUtil->hasThePermissionInSubscription($business_id, 'report_module');
        $product_report                     = $this->moduleUtil->hasThePermissionInSubscription($business_id, 'product_report');
        $payment_status_report              = $this->moduleUtil->hasThePermissionInSubscription($business_id, 'payment_status_report');
        $report_daily                       = $this->moduleUtil->hasThePermissionInSubscription($business_id, 'report_daily');
        $report_daily_summary               = $this->moduleUtil->hasThePermissionInSubscription($business_id, 'report_daily_summary');
        $report_profit_loss                 = $this->moduleUtil->hasThePermissionInSubscription($business_id, 'report_profit_loss');
        $report_credit_status               = $this->moduleUtil->hasThePermissionInSubscription($business_id, 'report_credit_status');
        $activity_report                    = $this->moduleUtil->hasThePermissionInSubscription($business_id, 'activity_report');
        $contact_report                     = $this->moduleUtil->hasThePermissionInSubscription($business_id, 'contact_report');
        $trending_product                   = $this->moduleUtil->hasThePermissionInSubscription($business_id, 'trending_product');
        $user_activity                      = $this->moduleUtil->hasThePermissionInSubscription($business_id, 'user_activity');
        $report_register                    = $this->moduleUtil->hasThePermissionInSubscription($business_id, 'report_register');
        $contact_module                     = $this->moduleUtil->hasThePermissionInSubscription($business_id, 'contact_module');
        $contact_supplier                   = $this->moduleUtil->hasThePermissionInSubscription($business_id, 'contact_supplier');
        $contact_customer                   = $this->moduleUtil->hasThePermissionInSubscription($business_id, 'contact_customer');
        $contact_group_customer             = $this->moduleUtil->hasThePermissionInSubscription($business_id, 'contact_group_customer');
        $contact_group_supplier             = $this->moduleUtil->hasThePermissionInSubscription($business_id, 'contact_group_supplier');
        $import_contact                     = $this->moduleUtil->hasThePermissionInSubscription($business_id, 'import_contact');
        $customer_reference                 = $this->moduleUtil->hasThePermissionInSubscription($business_id, 'customer_reference');
        $customer_statement                 = $this->moduleUtil->hasThePermissionInSubscription($business_id, 'customer_statement');
        $customer_payment                   = $this->moduleUtil->hasThePermissionInSubscription($business_id, 'customer_payment');
        $outstanding_received               = $this->moduleUtil->hasThePermissionInSubscription($business_id, 'outstanding_received');
        $issue_payment_detail               = $this->moduleUtil->hasThePermissionInSubscription($business_id, 'issue_payment_detail');
        $pos_sale                           = $this->moduleUtil->hasThePermissionInSubscription($business_id, 'pos_sale');
        $pump_operator_dashboard = $this->moduleUtil->hasThePermissionInSubscription($business_id, 'pump_operator_dashboard');
        $property_module = $this->moduleUtil->hasThePermissionInSubscription($business_id, 'property_module');
        $disable_all_other_module_vr =  $this->moduleUtil->hasThePermissionInSubscription($business_id, 'disable_all_other_module_vr');
        if ($disable_all_other_module_vr && !auth()->user()->can('superadmin')) {
            return redirect()->to('visitor-module/visitor');
        }
        if (!auth()->user()->can('dashboard.data')) { 
            return view('home.index')->with(compact('home_dashboard', 'property_module', 'enable_petro_module', 'enable_petro_dashboard',
            'enable_petro_task_management',
            'enable_petro_pump_management',
            'enable_petro_management_testing',
            'enable_petro_meter_reading',
            'enable_petro_meter_resetting',
            'enable_petro_pump_dashboard',
            'enable_petro_pumper_management',
            'enable_petro_daily_collection',
            'enable_petro_settlement',
            'enable_petro_list_settlement',
            'enable_petro_dip_management',
            'report_module',
            'product_report',
            'payment_status_report',
            'report_daily',
            'report_daily_summary',
            'report_profit_loss',
            'report_credit_status',
            'activity_report',
            'contact_report',
            'trending_product',
            'user_activity',
            'report_register',
            'contact_module',
            'contact_supplier',
            'contact_customer',
            'contact_group_customer',
            'contact_group_supplier',
            'import_contact',
            'customer_reference',
            'customer_statement',
            'customer_payment',
            'outstanding_received',
            'issue_payment_detail',
            'pos_sale',
            'subscription'
        ));
        }
        $fy = $this->businessUtil->getCurrentFinancialYear($business_id);
        $date_filters['this_fy'] = $fy;
        $date_filters['this_month']['start'] = date('Y-m-01');
        $date_filters['this_month']['end'] = date('Y-m-t');
        $date_filters['this_week']['start'] = date('Y-m-d', strtotime('monday this week'));
        $date_filters['this_week']['end'] = date('Y-m-d', strtotime('sunday this week'));
        $currency = Currency::where('id', request()->session()->get('business.currency_id'))->first();
        //Chart for sells last 30 days
        $sells_last_30_days = $this->transactionUtil->getSellsLast30Days($business_id);
        $labels = [];
        $all_sell_values = [];
        $dates = [];
        for ($i = 29; $i >= 0; $i--) {
            $date = \Carbon::now()->subDays($i)->format('Y-m-d');
            $dates[] = $date;
            $labels[] = date('j M Y', strtotime($date));
            if (!empty($sells_last_30_days[$date])) {
                $all_sell_values[] = $sells_last_30_days[$date];
            } else {
                $all_sell_values[] = 0;
            }
        }
        //Get sell for indivisual locations
        $all_locations = BusinessLocation::forDropdown($business_id);
        $location_sells = [];
        $sells_by_location = $this->transactionUtil->getSellsLast30Days($business_id, true);
        foreach ($all_locations as $loc_id => $loc_name) {
            $values = [];
            foreach ($dates as $date) {
                $sell = $sells_by_location->first(function ($item) use ($loc_id, $date) {
                    return $item->date == $date &&
                        $item->location_id == $loc_id;
                });
                if (!empty($sell)) {
                    $values[] = $sell->total_sells;
                } else {
                    $values[] = 0;
                }
            }
            $location_sells[$loc_id]['loc_label'] = $loc_name;
            $location_sells[$loc_id]['values'] = $values;
        }
        $sells_chart_1 = new CommonChart;
        $sells_chart_1->labels($labels)
            ->options($this->__chartOptions(__(
                'home.total_sells',
                ['currency' => $currency->code]
            )));
        if (!empty($location_sells)) {
            foreach ($location_sells as $location_sell) {
                $sells_chart_1->dataset($location_sell['loc_label'], 'line', $location_sell['values']);
            }
        }
        if (count($all_locations) > 1) {
            $sells_chart_1->dataset(__('report.all_locations'), 'line', $all_sell_values);
        }
        //Chart for sells this financial year
        $sells_this_fy = $this->transactionUtil->getSellsCurrentFy($business_id, $fy['start'], $fy['end']);
        $labels = [];
        $values = [];
        $months = [];
        $date = strtotime($fy['start']);
        $last   = date('m-Y', strtotime($fy['end']));
        $fy_months = [];
        do {
            $month_year = date('m-Y', $date);
            $fy_months[] = $month_year;
            $month_number = date('m', $date);
            $labels[] = Carbon::createFromFormat('m-Y', $month_year)
                ->format('M-Y');
            $date = strtotime('+1 month', $date);
            if (!empty($sells_this_fy[$month_year])) {
                $values[] = $sells_this_fy[$month_year];
            } else {
                $values[] = 0;
            }
        } while ($month_year != $last);
        $fy_sells_by_location = $this->transactionUtil->getSellsCurrentFy($business_id, $fy['start'], $fy['end'], true);
        $fy_sells_by_location_data = [];
        foreach ($all_locations as $loc_id => $loc_name) {
            $values_data = [];
            foreach ($fy_months as $month) {
                $sell = $fy_sells_by_location->first(function ($item) use ($loc_id, $month) {
                    return $item->yearmonth == $month &&
                        $item->location_id == $loc_id;
                });
                if (!empty($sell)) {
                    $values_data[] = $sell->total_sells;
                } else {
                    $values_data[] = 0;
                }
            }
            $fy_sells_by_location_data[$loc_id]['loc_label'] = $loc_name;
            $fy_sells_by_location_data[$loc_id]['values'] = $values_data;
        }
        $sells_chart_2 = new CommonChart;
        $sells_chart_2->labels($labels)
            ->options($this->__chartOptions(__(
                'home.total_sells',
                ['currency' => $currency->code]
            )));
        if (!empty($fy_sells_by_location_data)) {
            foreach ($fy_sells_by_location_data as $location_sell) {
                $sells_chart_2->dataset($location_sell['loc_label'], 'line', $location_sell['values']);
            }
        }
        if (count($all_locations) > 1) {
            $sells_chart_2->dataset(__('report.all_locations'), 'line', $values);
        }
        //Get Dashboard widgets from module
        $module_widgets = $this->moduleUtil->getModuleData('dashboard_widget');
        $widgets = [];
        foreach ($module_widgets as $widget_array) {
            if (!empty($widget_array['position'])) {
                $widgets[$widget_array['position']][] = $widget_array['widget'];
            }
        }
        $pending_customer_payments = Transaction::leftjoin('contacts', 'transactions.contact_id', 'contacts.id')->select('name')->where('transactions.business_id', $business_id)->where('payment_status', 'pending')->groupBy('name')->pluck('name')->toArray();
        $customer_name_payment = implode(',', $pending_customer_payments);
        $help_explanations = HelpExplanation::pluck('value', 'help_key');
        $register_success = session('register_success');
        return view('home.index', compact(
            'help_explanations',
            'home_dashboard',
            'date_filters',
            'sells_chart_1',
            'sells_chart_2',
            'widgets',
            'customer_name_payment',
            'all_locations',
            'pump_operator_dashboard',
            'property_module',
            'enable_petro_module',
            'enable_petro_dashboard',
            'enable_petro_task_management',
            'enable_petro_pump_management',
            'enable_petro_management_testing',
            'enable_petro_meter_reading',
            'enable_petro_meter_resetting',
            'enable_petro_pump_dashboard',
            'enable_petro_pumper_management',
            'enable_petro_daily_collection',
            'enable_petro_settlement',
            'enable_petro_list_settlement',
            'enable_petro_dip_management',
            'register_success',
            'report_module',
            'product_report',
            'payment_status_report',
            'report_daily',
            'report_daily_summary',
            'report_profit_loss',
            'report_credit_status',
            'activity_report',
            'contact_report',
            'trending_product',
            'user_activity',
            'report_register',
            'contact_module',
            'contact_supplier',
            'contact_customer',
            'contact_group_customer',
            'contact_group_supplier',
            'import_contact',
            'customer_reference',
            'customer_statement',
            'customer_payment',
            'outstanding_received',
            'issue_payment_detail',
            'pos_sale',
            'subscription'
        ));
    }
    /**
     * Retrieves purchase and sell details for a given time period.
     *
     * @return \Illuminate\Http\Response
     */
    public function getTotals()
    {
        if (request()->ajax()) {
            $start = request()->start;
            $end = request()->end;
            $business_id = request()->session()->get('user.business_id');
            $purchase_details = $this->transactionUtil->getPurchaseTotals($business_id, $start, $end);
            $sell_details = $this->transactionUtil->getSellTotals($business_id, $start, $end);
            $transaction_types = [
                'purchase_return', 'stock_adjustment', 'sell_return'
            ];
            $transaction_totals = $this->transactionUtil->getTransactionTotals(
                $business_id,
                $transaction_types,
                $start,
                $end
            );
            $total_purchase_inc_tax = !empty($purchase_details['total_purchase_inc_tax']) ? $purchase_details['total_purchase_inc_tax'] : 0;
            $total_purchase_return_inc_tax = $transaction_totals['total_purchase_return_inc_tax'];
            $total_adjustment = $transaction_totals['total_adjustment'];
            $total_purchase = $total_purchase_inc_tax - $total_purchase_return_inc_tax - $total_adjustment;
            $output = $purchase_details;
            $output['total_purchase'] = $total_purchase;
            $output['total_purchase_due'] = !empty($allpurchase_details['total_purchase_due']) ? $allpurchase_details['total_purchase_due'] : 0;
            $total_sell_inc_tax = !empty($sell_details['total_sell_inc_tax']) ? $sell_details['total_sell_inc_tax'] : 0;
            $total_sell_return_inc_tax = !empty($transaction_totals['total_sell_return_inc_tax']) ? $transaction_totals['total_sell_return_inc_tax'] : 0;
            $output['total_sell'] = $total_sell_inc_tax - $total_sell_return_inc_tax;
            $output['total_sell_due'] = !empty($allsell_details['total_sell_due']) ? $allsell_details['total_sell_due'] : 0;
            $output['invoice_due'] = $sell_details['invoice_due'];
            return $output;
        }
    }
    /**
     * Retrieves sell products whose available quntity is less than alert quntity.
     *
     * @return \Illuminate\Http\Response
     */
    public function getProductStockAlert()
    {
        if (request()->ajax()) {
            $business_id = request()->session()->get('user.business_id');
            $query = VariationLocationDetails::join(
                'product_variations as pv',
                'variation_location_details.product_variation_id',
                '=',
                'pv.id'
            )
                ->join(
                    'variations as v',
                    'variation_location_details.variation_id',
                    '=',
                    'v.id'
                )
                ->join(
                    'products as p',
                    'variation_location_details.product_id',
                    '=',
                    'p.id'
                )
                ->leftjoin(
                    'business_locations as l',
                    'variation_location_details.location_id',
                    '=',
                    'l.id'
                )
                ->leftjoin('units as u', 'p.unit_id', '=', 'u.id')
                ->where('p.business_id', $business_id)
                ->where('p.enable_stock', 1)
                ->where('p.is_inactive', 0)
                ->whereRaw('variation_location_details.qty_available <= p.alert_quantity');
            //Check for permitted locations of a user
            $permitted_locations = auth()->user()->permitted_locations();
            if ($permitted_locations != 'all') {
                $query->whereIn('variation_location_details.location_id', $permitted_locations);
            }
            $products = $query->select(
                'p.name as product',
                'p.type',
                'pv.name as product_variation',
                'v.name as variation',
                'l.name as location',
                'variation_location_details.qty_available as stock',
                'u.short_name as unit'
            )
                ->groupBy('variation_location_details.id')
                ->orderBy('stock', 'asc');
            return Datatables::of($products)
                ->editColumn('product', function ($row) {
                    if ($row->type == 'single') {
                        return $row->product;
                    } else {
                        return $row->product . ' - ' . $row->product_variation . ' - ' . $row->variation;
                    }
                })
                ->editColumn('stock', function ($row) {
                    $stock = $row->stock ? $row->stock : 0;
                    return '<span data-is_quantity="true" class="display_currency" data-currency_symbol=false>' . (float) $stock . '</span> ' . $row->unit;
                })
                ->removeColumn('unit')
                ->removeColumn('type')
                ->removeColumn('product_variation')
                ->removeColumn('variation')
                ->rawColumns([2])
                ->make(false);
        }
    }
    /**
     * Retrieves payment dues for the purchases.
     *
     * @return \Illuminate\Http\Response
     */
    public function getPurchasePaymentDues()
    {
        if (request()->ajax()) {
            $business_id = request()->session()->get('user.business_id');
            $today = \Carbon::now()->format("Y-m-d H:i:s");
            $query = Transaction::join(
                'contacts as c',
                'transactions.contact_id',
                '=',
                'c.id'
            )
                ->leftJoin(
                    'transaction_payments as tp',
                    'transactions.id',
                    '=',
                    'tp.transaction_id'
                )
                ->where('transactions.business_id', $business_id)
                ->where('transactions.type', 'purchase')
                ->where('transactions.payment_status', '!=', 'paid')
                ->whereRaw("DATEDIFF( DATE_ADD( transaction_date, INTERVAL IF(c.pay_term_type = 'days', c.pay_term_number, 30 * c.pay_term_number) DAY), '$today') <= 7");
            //Check for permitted locations of a user
            $permitted_locations = auth()->user()->permitted_locations();
            if ($permitted_locations != 'all') {
                $query->whereIn('transactions.location_id', $permitted_locations);
            }
            $dues =  $query->select(
                'transactions.id as id',
                'c.name as supplier',
                'ref_no',
                'final_total',
                DB::raw('SUM(tp.amount) as total_paid')
            )
                ->groupBy('transactions.id');
            return Datatables::of($dues)
                ->addColumn('due', function ($row) {
                    $total_paid = !empty($row->total_paid) ? $row->total_paid : 0;
                    $due = $row->final_total - $total_paid;
                    return '<span class="display_currency" data-currency_symbol="true">' .
                        $due . '</span>';
                })
                ->editColumn('ref_no', function ($row) {
                    if (auth()->user()->can('purchase.view')) {
                        return  '<a href="#" data-href="' . action('PurchaseController@show', [$row->id]) . '"
                                    class="btn-modal" data-container=".view_modal">' . $row->ref_no . '</a>';
                    }
                    return $row->ref_no;
                })
                ->removeColumn('id')
                ->removeColumn('final_total')
                ->removeColumn('total_paid')
                ->rawColumns([1, 2])
                ->make(false);
        }
    }
    /**
     * Retrieves payment dues for the purchases.
     *
     * @return \Illuminate\Http\Response
     */
    public function getSalesPaymentDues()
    {
        if (request()->ajax()) {
            $business_id = request()->session()->get('user.business_id');
            $today = \Carbon::now()->format("Y-m-d H:i:s");
            $query = Transaction::join(
                'contacts as c',
                'transactions.contact_id',
                '=',
                'c.id'
            )
                ->leftJoin(
                    'transaction_payments as tp',
                    'transactions.id',
                    '=',
                    'tp.transaction_id'
                )
                ->where('transactions.business_id', $business_id)
                ->where('transactions.type', 'sell')
                ->where('transactions.payment_status', '!=', 'paid')
                ->whereNotNull('transactions.pay_term_number')
                ->whereNotNull('transactions.pay_term_type')
                ->whereRaw("DATEDIFF( DATE_ADD( transaction_date, INTERVAL IF(transactions.pay_term_type = 'days', transactions.pay_term_number, 30 * transactions.pay_term_number) DAY), '$today') <= 7");
            //Check for permitted locations of a user
            $permitted_locations = auth()->user()->permitted_locations();
            if ($permitted_locations != 'all') {
                $query->whereIn('transactions.location_id', $permitted_locations);
            }
            $dues =  $query->select(
                'transactions.id as id',
                'c.name as customer',
                'transactions.invoice_no',
                'final_total',
                DB::raw('SUM(tp.amount) as total_paid')
            )
                ->groupBy('transactions.id');
            return Datatables::of($dues)
                ->addColumn('due', function ($row) {
                    $total_paid = !empty($row->total_paid) ? $row->total_paid : 0;
                    $due = $row->final_total - $total_paid;
                    return '<span class="display_currency" data-currency_symbol="true">' .
                        $due . '</span>';
                })
                ->editColumn('invoice_no', function ($row) {
                    if (auth()->user()->can('sell.view')) {
                        return  '<a href="#" data-href="' . action('SellController@show', [$row->id]) . '"
                                    class="btn-modal" data-container=".view_modal">' . $row->invoice_no . '</a>';
                    }
                    return $row->invoice_no;
                })
                ->removeColumn('id')
                ->removeColumn('final_total')
                ->removeColumn('total_paid')
                ->rawColumns([1, 2])
                ->make(false);
        }
    }
    public function loadMoreNotifications()
    {
        $notifications = auth()->user()->notifications()->orderBy('created_at', 'DESC')->paginate(10);
        if (request()->input('page') == 1) {
            auth()->user()->unreadNotifications->markAsRead();
        }
        $notifications_data = [];
        foreach ($notifications as $notification) {
            $data = $notification->data;
            if (in_array($notification->type, [\App\Notifications\RecurringInvoiceNotification::class])) {
                $msg = '';
                $icon_class = '';
                $link = '';
                if (
                    $notification->type ==
                    \App\Notifications\RecurringInvoiceNotification::class
                ) {
                    $msg = !empty($data['invoice_status']) && $data['invoice_status'] == 'draft' ?
                        __(
                            'lang_v1.recurring_invoice_error_message',
                            ['product_name' => $data['out_of_stock_product'], 'subscription_no' => !empty($data['subscription_no']) ? $data['subscription_no'] : '']
                        ) :
                        __(
                            'lang_v1.recurring_invoice_message',
                            ['invoice_no' => !empty($data['invoice_no']) ? $data['invoice_no'] : '', 'subscription_no' => !empty($data['subscription_no']) ? $data['subscription_no'] : '']
                        );
                    $icon_class = !empty($data['invoice_status']) && $data['invoice_status'] == 'draft' ? "fa fa-exclamation-triangle text-warning" : "fa fa-recycle text-green";
                    $link = action('SellPosController@listSubscriptions');
                }
                $notifications_data[] = [
                    'msg' => $msg,
                    'icon_class' => $icon_class,
                    'link' => $link,
                    'read_at' => $notification->read_at,
                    'created_at' => $notification->created_at->diffForHumans()
                ];
            } else {
                $module_notification_data = $this->moduleUtil->getModuleData('parse_notification', $notification);
                if (!empty($module_notification_data)) {
                    foreach ($module_notification_data as $module_data) {
                        if (!empty($module_data)) {
                            $notifications_data[] = $module_data;
                        }
                    }
                }
            }
        }
        return view('layouts.partials.notification_list', compact('notifications_data'));
    }
    private function __chartOptions($title)
    {
        return [
            'yAxis' => [
                'title' => [
                    'text' => $title
                ]
            ],
            'legend' => [
                'align' => 'right',
                'verticalAlign' => 'top',
                'floating' => true,
                'layout' => 'vertical'
            ],
        ];
    }
    public function loginPayroll(Request $request)
    {
        $connection = DB::connection('mysql2');
        $users= $connection->select("SELECT id,business_id FROM users WHERE email='".\Auth::user()->email."'");
        if(!empty($users)){
            $user_id = $users[0]->id;
        }else{
            $user_id = $connection->table('users')->insertGetId([
                'email' => \Auth::user()->email, 
                'first_name' => \Auth::user()->first_name,
                'last_name' => \Auth::user()->last_name,
                'password' => \Auth::user()->password,
                'status_id' => 1,
                'is_in_employee' => 1,
                'business_id' => \Auth::user()->business_id,
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s"),
            ]);
            if($user_id){
                if(\Auth::user()->getRoleNameAttribute() == "Admin"){
                    $userRole = $connection->table('role_user')->insert([
                        'user_id' => $user_id, 
                        'role_id' => 1,
                    ]);
                }else{
                    $userRole = $connection->table('role_user')->insert([
                        'user_id' => $user_id, 
                        'role_id' => 4,
                    ]);
                }
            }
        }
        return response()->json(['login_url' => env('PAYROLL_LOGIN')."/".base64_encode($user_id)]);
    }
}
