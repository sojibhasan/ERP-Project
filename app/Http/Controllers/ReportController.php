<?php

namespace App\Http\Controllers;

use App\Account;
use App\AccountGroup;
use App\AccountTransaction;
use App\AccountType;
use App\Brands;
use App\Business;
use App\BusinessLocation;
use App\CashRegister;
use App\Category;
use App\Contact;
use App\Currency;
use App\ContactGroup;
use App\ExpenseCategory;
use App\Product;
use App\PurchaseLine;
use App\Restaurant\ResTable;
use App\SellingPriceGroup;
use App\Transaction;
use App\TransactionPayment;
use App\TransactionSellLine;
use App\TransactionSellLinesPurchaseLines;
use App\Unit;
use App\User;
use App\Utils\CashRegisterUtil;
use App\Utils\ModuleUtil;
use App\Utils\BusinessUtil;
use App\Utils\ProductUtil;
use App\Utils\TransactionUtil;
use App\Utils\Util;
use App\Variation;
use App\VariationLocationDetails;
use Modules\HR\Entities\WorkShift;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Charts;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\Petro\Entities\CustomerPayment;
use Modules\Petro\Entities\PumpOperator;
use Modules\Petro\Entities\Settlement;
use Spatie\Activitylog\Models\Activity;
use Yajra\DataTables\Facades\DataTables;
use App\Charts\CommonChart;
use Illuminate\Support\Facades\Session;
use Modules\Petro\Entities\DipReading;
use Modules\Petro\Entities\FuelTank;
use Modules\Petro\Entities\TankSellLine;

class ReportController extends Controller
{
    /**
     * All Utils instance.
     *
     */
    protected $transactionUtil;
    protected $cashRegisterUtil;
    protected $productUtil;
    protected $moduleUtil;
    protected $businessUtil;
    protected $util;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(CashRegisterUtil $cashRegisterUtil, TransactionUtil $transactionUtil, ProductUtil $productUtil, ModuleUtil $moduleUtil, Util $util, BusinessUtil $businessUtil)
    {
        $this->cashRegisterUtil = $cashRegisterUtil;
        $this->transactionUtil = $transactionUtil;
        $this->productUtil = $productUtil;
        $this->moduleUtil = $moduleUtil;
        $this->businessUtil = $businessUtil;
        $this->util = $util;
    }
    /**
     * Shows Product Related Reports
     *
     * @return \Illuminate\Http\Response
     */
    public function getProductReport(Request $request)
    {
        $business_id = $request->session()->get('user.business_id');
        $rack_enabled = (request()->session()->get('business.enable_racks') || request()->session()->get('business.enable_row') || request()->session()->get('business.enable_position'));
        $suppliers = Contact::suppliersDropdown($business_id, false);
        $customers = Contact::customersDropdown($business_id, false);
        $business_locations = BusinessLocation::forDropdown($business_id);
        $mf_module = $this->moduleUtil->hasThePermissionInSubscription($business_id, 'mf_module');
        $enable_petro_module = $this->moduleUtil->hasThePermissionInSubscription($business_id, 'enable_petro_module');
        $categories = Category::forDropdown($business_id, $enable_petro_module);
        $brands = Brands::where('business_id', $business_id)
            ->pluck('name', 'id');
        $sub_categories = Category::subCategoryforDropdown($business_id, $enable_petro_module);
        $products = Product::where('business_id', $business_id)->pluck('name', 'id');
        $only_manufactured_products = Variation::join('products as p', 'p.id', '=', 'variations.product_id')->join('mfg_recipes as mr', 'mr.variation_id', '=', 'variations.id')->where('p.business_id', $business_id)->pluck('p.name', 'p.id');
        $units = Unit::where('business_id', $business_id)
            ->pluck('short_name', 'id');
        return view('report.product_reports')->with(compact(
            'rack_enabled',
            'suppliers',
            'customers',
            'business_locations',
            'brands',
            'categories',
            'sub_categories',
            'products',
            'only_manufactured_products',
            'units',
            'mf_module'
        ));
    }
    /**
     * Shows contact related reports
     *
     * @return \Illuminate\Http\Response
     */
    public function getContactReport(Request $request)
    {
        if (!auth()->user()->can('contact_report.view')) {
            abort(403, 'Unauthorized action.');
        }
        $business_id = $request->session()->get('user.business_id');
        $customer_group = ContactGroup::forDropdown($business_id, false, true);
        $business_locations = BusinessLocation::forDropdown($business_id, true);
        $customer_group = ContactGroup::forDropdown($business_id, false, true);
        $types = Contact::typeDropdown(true);
        $customers = Contact::suppliersDropdownByType($business_id, true, true, ['customer']);
        $suppliers = Contact::suppliersDropdownByType($business_id, true, true, ['supplier']);
        $both = Contact::suppliersDropdownByType($business_id, true, true, ['customer', 'supplier']);
        return view('report.contact_reports')->with(compact(
            'customer_group',
            'business_locations',
            'customer_group',
            'types',
            'customers',
            'suppliers',
            'both'
        ));
    }
    /**
     * Shows payment status related reports
     *
     * @return \Illuminate\Http\Response
     */
    public function getPaymentStatusReport(Request $request)
    {
        $business_id = $request->session()->get('user.business_id');
        $business_locations = BusinessLocation::forDropdown($business_id);
        $suppliers = Contact::suppliersDropdown($business_id, false);
        $customers = Contact::customersDropdown($business_id, false);
        $payment_types = $this->transactionUtil->payment_types();
        $customer_group = ContactGroup::forDropdown($business_id, false, true);
        $types = Contact::typeDropdown(true);
        return view('report.payment_status_reports')->with(compact(
            'suppliers',
            'business_locations',
            'customers',
            'customer_group',
            'types',
            'payment_types'
        ));
    }
    /**
     * Shows management related reports
     *
     * @return \Illuminate\Http\Response
     */
    public function getManagementReport(Request $request)
    {
        $business_id = $request->session()->get('user.business_id');
        $business_locations = BusinessLocation::forDropdown($business_id);
        $petro_module = $this->moduleUtil->hasThePermissionInSubscription($business_id, 'enable_petro_module');
        $work_shifts = WorkShift::where('business_id', $business_id)->pluck('shift_name', 'id');
        $users = User::forDropdown($business_id, false);
        $fy = $this->businessUtil->getCurrentFinancialYear($business_id);
        $date_filters['this_fy'] = $fy;
        $date_filters['this_month']['start'] = date('Y-m-01');
        $date_filters['this_month']['end'] = date('Y-m-t');
        $date_filters['this_week']['start'] = date('Y-m-d', strtotime('monday this week'));
        $date_filters['this_week']['end'] = date('Y-m-d', strtotime('sunday this week'));
        $currency = Currency::where('id', request()->session()->get('business.currency_id'))->first();
        $start_date = !empty($request->credit_start_date) ? $request->credit_start_date : Carbon::now()->firstOfMonth();
        $end_date = !empty($request->credit_end_date) ? $request->credit_end_date : Carbon::now()->firstOfMonth();
        $no_of_period = !empty(request()->period) ? request()->period : 5;
        // dd($start_date);
        $period = Carbon::parse($start_date)->toPeriod($end_date, $no_of_period, 'days');
        $labels = [];
        $all_sell_values = [];
        $dates = [];
        foreach ($period as $key => $date) {
            $dates[] = $date;
            $labels[] = date('j M Y', strtotime($date));
        }
        $all_locations = BusinessLocation::forDropdown($business_id);
        $location_sells = [];
        $total_values = [];
        $paid_values = [];
        $due_values = [];
        foreach ($dates as $key => $date) {
            if ($key < count($dates) - 1) {
                $sells_by_location = $this->transactionUtil->getCreditSells($business_id, true,  $date->format('Y-m-d'), $dates[$key + 1]->format('Y-m-d'), request()->credit_status_business_location);
                $total_values[] = $sells_by_location->sum('total_sells');
                $paid_values[] = $sells_by_location->sum('total_paid');
                $due_values[] = $sells_by_location->sum('total_due');
            } else {
                $total_values[] = 0;
                $paid_values[] = 0;
                $due_values[] = 0;
            }
        }
        $location_sells[$key]['Issued'] = 'Issued';
        $location_sells[$key]['Received'] = 'Received';
        $location_sells[$key]['Due'] = 'Due';
        $location_sells[$key]['total_values'] = $total_values;
        $location_sells[$key]['paid_values'] = $paid_values;
        $location_sells[$key]['due_values'] = $due_values;
        $sells_chart_1 = new CommonChart;
        $sells_chart_1->labels($labels)
            ->options($this->__chartOptions(__(
                'report.credit_status',
                ['currency' => $currency->code]
            )));
        if (!empty($location_sells)) {
            foreach ($location_sells as $location_sell) {
                $sells_chart_1->dataset($location_sell['Issued'], 'column', $location_sell['total_values']);
                $sells_chart_1->dataset($location_sell['Received'], 'column', $location_sell['paid_values']);
                $sells_chart_1->dataset($location_sell['Due'], 'column', $location_sell['due_values']);
            }
        }
        $tab = !empty($request->tab) ? $request->tab : null;
        $report_daily                       = $this->moduleUtil->hasThePermissionInSubscription($business_id, 'report_daily');
        $report_daily_summary               = $this->moduleUtil->hasThePermissionInSubscription($business_id, 'report_daily_summary');
        $report_profit_loss                 = $this->moduleUtil->hasThePermissionInSubscription($business_id, 'report_profit_loss');
        $report_credit_status               = $this->moduleUtil->hasThePermissionInSubscription($business_id, 'report_credit_status');
        $report_register                    = $this->moduleUtil->hasThePermissionInSubscription($business_id, 'report_register');
        return view('report.management_reports')->with(compact(
            'petro_module',
            'business_locations',
            'work_shifts',
            'users',
            'date_filters',
            'sells_chart_1',
            'tab',
            'currency',
            'report_daily',
            'report_daily_summary',
            'report_profit_loss',
            'report_credit_status',
            'report_register'
        ));
    }
    /**
     * Verification reports related reports
     *
     * @return \Illuminate\Http\Response
     */
    public function getVerificationReport(Request $request)
    {
        $business_id = $request->session()->get('user.business_id');
        if (!$this->moduleUtil->hasThePermissionInSubscription($business_id, 'verification_report')) {
            abort(403, 'Unauthorized action.');
        }
        $business_locations = BusinessLocation::forDropdown($business_id);
        $petro_module = $this->moduleUtil->hasThePermissionInSubscription($business_id, 'enable_petro_module');
        $work_shifts = WorkShift::where('business_id', $business_id)->pluck('shift_name', 'id');
        $users = User::forDropdown($business_id, false);
        $fy = $this->businessUtil->getCurrentFinancialYear($business_id);
        $date_filters['this_fy'] = $fy;
        $date_filters['this_month']['start'] = date('Y-m-01');
        $date_filters['this_month']['end'] = date('Y-m-t');
        $date_filters['this_week']['start'] = date('Y-m-d', strtotime('monday this week'));
        $date_filters['this_week']['end'] = date('Y-m-d', strtotime('sunday this week'));
        $permission['monthly_report'] =  $this->moduleUtil->hasThePermissionInSubscription($business_id, 'monthly_report');
        $permission['comparison_report'] =  $this->moduleUtil->hasThePermissionInSubscription($business_id, 'comparison_report');
        return view('report.verification_reports')->with(compact(
            'petro_module',
            'business_locations',
            'work_shifts',
            'users',
            'permission',
            'date_filters'
        ));
    }
    /**
     * Shows activity related reports
     *
     * @return \Illuminate\Http\Response
     */
    public function getActivityReport(Request $request)
    {
        $business_id = $request->session()->get('user.business_id');
        $business_locations = BusinessLocation::forDropdown($business_id);
        $filters = $request->only(['category', 'location_id']);
        $currency = Currency::where('id', request()->session()->get('business.currency_id'))->first();
        $date_range = $request->input('date_range');
        if (!empty($date_range)) {
            $date_range_array = explode('~', $date_range);
            $filters['start_date'] = $this->transactionUtil->uf_date(trim($date_range_array[0]));
            $filters['end_date'] = $this->transactionUtil->uf_date(trim($date_range_array[1]));
        } else {
            $filters['start_date'] = Carbon::now()->startOfMonth()->format('Y-m-d');
            $filters['end_date'] = Carbon::now()->endOfMonth()->format('Y-m-d');
        }
        $expenses = $this->transactionUtil->getExpenseReport($business_id, $filters);
        $values = [];
        $labels = [];
        foreach ($expenses as $expense) {
            $values[] = $expense->total_expense;
            $labels[] = !empty($expense->category) ? $expense->category : __('report.others');
        }
        $chart = new CommonChart;
        $chart->labels($labels)
            ->options($this->__chartOptions(__(
                'report.expense_report',
                ['currency' => $currency->code]
            )));
        $chart->dataset(__('report.total_expense'), 'column', $values);
        $categories = ExpenseCategory::where('business_id', $business_id)
            ->pluck('name', 'id');
        $business_locations = BusinessLocation::forDropdown($business_id, true);
        //sale commisison report
        $sales_cmsn_agnt = $request->session()->get('business.sales_cmsn_agnt');
        $all_users = User::where('business_id', $business_id)
            ->select('id', DB::raw("CONCAT(COALESCE(surname, ''),' ',COALESCE(first_name, ''),' ',COALESCE(last_name,'')) as full_name"));
        $agent_placeholder = '';
        if ($sales_cmsn_agnt == 'cmsn_agnt') {
            $all_users->where('is_cmmsn_agnt', 1);
            $agent_placeholder = 'Sale Commission Agents';
        }
        if ($sales_cmsn_agnt == 'logged_in_user') {
            $all_users->where('id', Auth::user()->id);
            $agent_placeholder = 'Logged in user';
        }
        if ($sales_cmsn_agnt == 'user') {
            $agent_placeholder = 'All Users';
        }
        $users = $all_users->pluck('full_name', 'id');
        $customers = Contact::customersDropdown($business_id);
        $is_expense = !empty($request->is_expense) ? 1 : 0;
        return view('report.activity_reports')->with(compact(
            'business_locations',
            'users',
            'chart',
            'categories',
            'agent_placeholder',
            'is_expense',
            'customers'
        ));
    }
    /**
     * Shows profit\loss of a business
     *
     * @return \Illuminate\Http\Response
     */
    public function getProfitLoss(Request $request)
    {
        if (!auth()->user()->can('profit_loss_report.view')) {
            abort(403, 'Unauthorized action.');
        }
        $business_id = $request->session()->get('user.business_id');
        if (\Module::has('Manufacturing') && (auth()->user()->can('superadmin') || $this->moduleUtil->hasThePermissionInSubscription($business_id, 'manufacturing_module'))) {
            $show_manufacturing_data = true;
        } else {
            $show_manufacturing_data = false;
        }
        //Return the details in ajax call
        if ($request->ajax()) {
            $start_date = $request->get('start_date');
            $end_date = $request->get('end_date');
            $location_id = $request->get('location_id');
            //For Opening stock date should be 1 day before
            $day_before_start_date = \Carbon::createFromFormat('Y-m-d', $start_date)->subDay()->format('Y-m-d');
            //Get Opening stock
            $opening_stock = $this->transactionUtil->getOpeningClosingStock($business_id, $day_before_start_date, $location_id, true);
            //Get Closing stock
            $closing_stock = $this->transactionUtil->getOpeningClosingStock(
                $business_id,
                $end_date,
                $location_id
            );
            //Get Purchase details
            $purchase_details = $this->transactionUtil->getPurchaseTotals(
                $business_id,
                $start_date,
                $end_date,
                $location_id
            );
            //Get Sell details
            $sell_details = $this->transactionUtil->getSellTotals(
                $business_id,
                $start_date,
                $end_date,
                $location_id
            );
            $transaction_types = [
                'purchase_return', 'sell_return', 'expense', 'stock_adjustment', 'sell_transfer', 'purchase', 'sell'
            ];
            $show_total_payroll = false;
            if (\Module::has('Essentials') && (auth()->user()->can('superadmin') || $this->moduleUtil->hasThePermissionInSubscription($business_id, 'essentials_module'))) {
                $show_total_payroll = true;
                $transaction_types[] = 'payroll';
            }
            $transaction_totals = $this->transactionUtil->getTransactionTotals(
                $business_id,
                $transaction_types,
                $start_date,
                $end_date,
                $location_id
            );
            $sale_income_group = AccountGroup::getGroupByName('Sales Income Group');
            $cogs_group = AccountGroup::getGroupByName('COGS Account Group');
            $direct_expense_group = AccountGroup::getGroupByName('Direct Expense');
            $sale_income_balance = 0;
            $cogs_balance = 0;
            $direct_expense_balance = 0;
            $total_expense_balance = 0;
            $account_access = $this->moduleUtil->hasThePermissionInSubscription($business_id, 'access_account');
            if ($account_access) {
                $direct_expense_balance = $this->getAccountsBalanceByGroup(
                    $direct_expense_group->id,
                    'direct_expense',
                    $start_date,
                    $end_date,
                    $location_id
                );
                $total_expense_balance = $this->getTotalAccountBalanceExpense(
                    $start_date,
                    $end_date,
                    $location_id
                );
            }
            $data = [];
            if ($show_manufacturing_data) {
                $data['total_production_cost'] = $this->transactionUtil->getTotalProductionCost(
                    $business_id,
                    $start_date,
                    $end_date,
                    $location_id
                );
            } else {
                $data['total_production_cost'] = 0;
            }
            $total_transfer_shipping_charges = $transaction_totals['total_transfer_shipping_charges'];
            //Add total sell shipping charges to $total_transfer_shipping_charges
            if (!empty($sell_details['total_shipping_charges'])) {
                $total_transfer_shipping_charges += $sell_details['total_shipping_charges'];
            }
            //Add total purchase shipping charges to $total_transfer_shipping_charges
            if (!empty($purchase_details['total_shipping_charges'])) {
                $total_transfer_shipping_charges += $purchase_details['total_shipping_charges'];
            }
            //Discounts
            $total_purchase_discount = $transaction_totals['total_purchase_discount'];
            $total_sell_discount = $transaction_totals['total_sell_discount'];
            $total_reward_amount = $transaction_totals['total_reward_amount'];
            //Purchase
            $data['total_purchase'] = !empty($purchase_details['total_purchase_exc_tax']) ? $purchase_details['total_purchase_exc_tax'] : 0;
            $data['total_purchase_discount'] = !empty($total_purchase_discount) ? $total_purchase_discount : 0;
            $data['total_purchase_return'] = $transaction_totals['total_purchase_return_exc_tax'];
            //Sales
            $data['total_sell'] = !empty($sell_details['total_sell_exc_tax']) ? $sell_details['total_sell_exc_tax'] : 0;
            $data['total_profit_by_product'] = number_format($this->getProfit('product', true), 6, '.', '');
            $data['total_sell_discount'] = !empty($total_sell_discount) ? $total_sell_discount : 0;
            $data['total_sell_return'] = $transaction_totals['total_sell_return_exc_tax'];
            //Expense
            $data['total_expense'] =  $transaction_totals['total_expense'] + $transaction_totals['settlement_expense'];
            //Payroll
            $total_payroll = 0;
            if ($show_total_payroll) {
                $data['total_payroll'] =  $transaction_totals['total_payroll'];
                $total_payroll = $transaction_totals['total_payroll'];
            }
            //Stock adjustments
            $data['total_adjustment'] = $transaction_totals['total_adjustment'];
            $data['total_recovered'] = $transaction_totals['total_recovered'];
            //Shipping
            $data['total_transfer_shipping_charges'] = $total_transfer_shipping_charges;
            $data['total_reward_amount'] = !empty($total_reward_amount) ? $total_reward_amount : 0;
            $data['direct_expense_balance'] = !empty($direct_expense_balance) ? $direct_expense_balance : 0;
            $data['total_sale_cost'] = $this->transactionUtil->getSaleCost($business_id, $start_date, $end_date)->total_sale_cost;
            //Stocks
            $data['opening_stock'] = !empty($opening_stock) ? $opening_stock : 0;
            $data['closing_stock'] = $data['opening_stock']  + $data['total_purchase'] - $data['total_sale_cost'];
            $gross_profit = $data['total_profit_by_product'] - $data['direct_expense_balance'];
            $data['gross_profit'] = $gross_profit;
            // $data['net_profit'] = $gross_profit - $total_expense_balance;
            $data['net_profit'] = $gross_profit - ($transaction_totals['total_expense'] + $transaction_totals['settlement_expense']);
            return $data;
        }
        $business_locations = BusinessLocation::forDropdown($business_id, true);
        return view('report.profit_loss', compact('business_locations', 'show_manufacturing_data'));
    }
    public function getAccountsBalanceByGroup(
        $group_id,
        $type,
        $start_date,
        $end_date,
        $location_id
    ) {
        $business_id = session()->get('user.business_id');
        $accounts = Account::leftjoin('account_transactions as AT', function ($join) {
            $join->on('AT.account_id', '=', 'accounts.id');
            $join->whereNull('AT.deleted_at');
        })
            ->leftjoin('transactions', 'AT.transaction_id', 'transactions.id')
            ->where('accounts.business_id', $business_id)
            ->where('accounts.asset_type', $group_id)
            ->where('transactions.type', 'sell')
            ->where('accounts.visible', 1)->notClosed()
            ->whereDate('AT.operation_date', '>=', $start_date)
            ->whereDate('AT.operation_date', '<=', $end_date)
            ->select([
                DB::raw("SUM( IF(AT.type='credit', amount, -1*amount) ) as sale_income_balance"),
                DB::raw("SUM( IF(AT.type='credit', -1*amount, amount) ) as cogs_balance"),
                DB::raw("SUM( IF(AT.type='credit', -1*amount, amount) ) as direct_expense_balance"),
            ]);
        $accounts->where('disabled', 0);
        $accounts = $accounts->first();
        if ($type == 'sales_income') {
            return $accounts->sale_income_balance;
        }
        if ($type == 'cogs') {
            return $accounts->cogs_balance;
        }
        if ($type == 'direct_expense') {
            return $accounts->direct_expense_balance;
        }
    }
    public function getAccountsBalanceByAccountName(
        $account_name,
        $type,
        $start_date,
        $end_date,
        $location_id
    ) {
        $business_id = session()->get('user.business_id');
        $accounts = Account::leftjoin('account_transactions as AT', function ($join) {
            $join->on('AT.account_id', '=', 'accounts.id');
            $join->whereNull('AT.deleted_at');
        })->leftjoin('account_groups', 'asset_type', 'account_groups.id')
            ->notClosed()
            ->where('accounts.business_id', $business_id)
            ->where('account_groups.name',  $account_name)
            ->where('accounts.visible', 1)
            ->whereDate('AT.operation_date', '>=', $start_date)
            ->whereDate('AT.operation_date', '<=', $end_date)
            ->select([
                DB::raw("SUM( IF(AT.type='credit', amount, -1*amount) ) as sale_income_balance"),
                DB::raw("SUM( IF(AT.type='debit', amount, -1*amount) ) as cogs_balance")
            ])->groupBy('asset_type');
        $accounts->where('disabled', 0);
        $accounts = $accounts->first();
        if ($type == 'sales_income') {
            return $accounts->sale_income_balance;
        }
        if ($type == 'cogs') {
            return $accounts->cogs_balance;
        }
    }
    public function getTotalAccountBalanceExpense(
        $start_date,
        $end_date,
        $location_id
    ) {
        $business_id = session()->get('user.business_id');
        $expense_account_type_id = AccountType::getAccountTypeIdByName('Expenses', $business_id)->id;
        $cogs_accounts = Account::leftjoin('account_groups', 'accounts.asset_type', 'account_groups.id')->where('accounts.business_id', $business_id)->where('account_groups.name', 'COGS Account Group')->select('accounts.id')->get();
        $direct_expense_accounts = Account::leftjoin('account_groups', 'accounts.asset_type', 'account_groups.id')->where('accounts.business_id', $business_id)->where('account_groups.name', 'Direct Expense')->select('accounts.id')->get();
        $merged_expenses_account = $cogs_accounts->merge($direct_expense_accounts)->pluck('id')->toArray();
        $accounts = Account::leftjoin('account_transactions as AT', function ($join) {
            $join->on('AT.account_id', '=', 'accounts.id');
            $join->whereNull('AT.deleted_at');
        })
            ->where('accounts.business_id', $business_id)
            ->whereDate('AT.operation_date', '>=', $start_date)
            ->whereDate('AT.operation_date', '<=', $end_date)
            ->where('accounts.account_type_id', $expense_account_type_id)
            ->whereNotIn('accounts.id', $merged_expenses_account)
            ->notClosed()
            ->where('accounts.visible', 1)
            ->select([
                DB::raw("SUM( IF(AT.type='credit', -1*amount, amount) ) as balance"),
            ]);
        $accounts->where('disabled', 0);
        $accounts = $accounts->first();
        return $accounts->balance;
    }
    /**
     * Shows product report of a business
     *
     * @return \Illuminate\Http\Response
     */
    public function getPurchaseSell(Request $request)
    {
        if (!auth()->user()->can('purchase_and_slae_report.view')) {
            abort(403, 'Unauthorized action.');
        }
        $business_id = $request->session()->get('user.business_id');
        //Return the details in ajax call
        if ($request->ajax()) {
            $start_date = $request->get('start_date');
            $end_date = $request->get('end_date');
            $location_id = $request->get('location_id');
            $purchase_details = $this->transactionUtil->getPurchaseTotals($business_id, $start_date, $end_date, $location_id);
            $sell_details = $this->transactionUtil->getSellTotals(
                $business_id,
                $start_date,
                $end_date,
                $location_id
            );
            $transaction_types = [
                'purchase_return', 'sell_return'
            ];
            $transaction_totals = $this->transactionUtil->getTransactionTotals(
                $business_id,
                $transaction_types,
                $start_date,
                $end_date,
                $location_id
            );
            $total_purchase_return_inc_tax = $transaction_totals['total_purchase_return_inc_tax'];
            $total_sell_return_inc_tax = $transaction_totals['total_sell_return_inc_tax'];
            $difference = [
                'total' => ($sell_details['total_sell_inc_tax'] - $total_sell_return_inc_tax) - ($purchase_details['total_purchase_inc_tax'] - $total_purchase_return_inc_tax),
                'due' => $sell_details['invoice_due'] - $purchase_details['purchase_due']
            ];
            return [
                'purchase' => $purchase_details,
                'sell' => $sell_details,
                'total_purchase_return' => $total_purchase_return_inc_tax,
                'total_sell_return' => $total_sell_return_inc_tax,
                'difference' => $difference
            ];
        }
        $business_locations = BusinessLocation::forDropdown($business_id, true);
        return view('report.purchase_sell')
            ->with(compact('business_locations'));
    }
    /**
     * Shows report for Supplier
     *
     * @return \Illuminate\Http\Response
     */
    public function getCustomerSuppliers(Request $request)
    {
        if (!auth()->user()->can('contacts_report.view')) {
            abort(403, 'Unauthorized action.');
        }
        $business_id = $request->session()->get('user.business_id');
        //Return the details in ajax call
        if ($request->ajax()) {
            $contacts = Contact::where('contacts.business_id', $business_id)
                ->join('transactions AS t', 'contacts.id', '=', 't.contact_id')
                ->groupBy('contacts.id')
                ->select(
                    DB::raw("SUM(IF(t.type = 'purchase', final_total, 0)) as total_purchase"),
                    DB::raw("SUM(IF(t.type = 'purchase_return', final_total, 0)) as total_purchase_return"),
                    DB::raw("SUM(IF(t.type = 'sell' AND t.status = 'final', final_total, 0)) as total_invoice"),
                    DB::raw("SUM(IF(t.type = 'purchase', (SELECT SUM(amount) FROM transaction_payments WHERE transaction_payments.transaction_id=t.id), 0)) as purchase_paid"),
                    DB::raw("SUM(IF(t.type = 'sell' AND t.status = 'final', (SELECT SUM(IF(is_return = 1,-1*amount,amount)) FROM transaction_payments WHERE transaction_payments.transaction_id=t.id), 0)) as invoice_received"),
                    DB::raw("SUM(IF(t.type = 'sell_return', (SELECT SUM(amount) FROM transaction_payments WHERE transaction_payments.transaction_id=t.id), 0)) as sell_return_paid"),
                    DB::raw("SUM(IF(t.type = 'purchase_return', (SELECT SUM(amount) FROM transaction_payments WHERE transaction_payments.transaction_id=t.id), 0)) as purchase_return_received"),
                    DB::raw("SUM(IF(t.type = 'sell_return', final_total, 0)) as total_sell_return"),
                    DB::raw("SUM(IF(t.type = 'opening_balance', final_total, 0)) as opening_balance"),
                    DB::raw("SUM(IF(t.type = 'advance_payment', final_total, 0)) as advance_payment"),
                    DB::raw("SUM(IF(t.type = 'opening_balance', (SELECT SUM(IF(is_return = 1,-1*amount,amount)) FROM transaction_payments WHERE transaction_payments.transaction_id=t.id), 0)) as opening_balance_paid"),
                    'contacts.supplier_business_name',
                    'contacts.name',
                    'contacts.type',
                    'contacts.id'
                );
            $permitted_locations = auth()->user()->permitted_locations();
            if ($permitted_locations != 'all') {
                $contacts->whereIn('t.location_id', $permitted_locations);
            }
            if (!empty($request->input('customer_group_id'))) {
                $contacts->where('contacts.customer_group_id', $request->input('customer_group_id'));
            }
            if (!empty($request->input('contact_type'))) {
                $contacts->where('contacts.type', $request->input('contact_type'));
            }
            if (!empty($request->input('contact_id'))) {
                $contacts->where('contacts.id', $request->input('contact_id'));
            }
            if (!empty($request->input('start_date')) && !empty($request->input('end_date'))) {
                $contacts->whereDate('t.transaction_date', '>=', $request->input('start_date'));
                $contacts->whereDate('t.transaction_date', '<=', $request->input('end_date'));
            }
            return Datatables::of($contacts)
                ->editColumn('name', function ($row) {
                    $name = $row->name;
                    if (!empty($row->supplier_business_name)) {
                        $name .= ', ' . $row->supplier_business_name;
                    }
                    return '<a href="' . action('ContactController@show', [$row->id]) . '" target="_blank" class="no-print">' .
                        $name .
                        '</a><span class="print_section">' . $name . '</span>';
                })
                ->editColumn('total_purchase', function ($row) {
                    return '<span class="display_currency total_purchase" data-orig-value="' . $row->total_purchase . '" data-currency_symbol = true>' . $row->total_purchase . '</span>';
                })
                ->editColumn('total_purchase_return', function ($row) {
                    return '<span class="display_currency total_purchase_return" data-orig-value="' . $row->total_purchase_return . '" data-currency_symbol = true>' . $row->total_purchase_return . '</span>';
                })
                ->editColumn('total_sell_return', function ($row) {
                    return '<span class="display_currency total_sell_return" data-orig-value="' . $row->total_sell_return . '" data-currency_symbol = true>' . $row->total_sell_return . '</span>';
                })
                ->editColumn('total_invoice', function ($row) {
                    return '<span class="display_currency total_invoice" data-orig-value="' . $row->total_invoice . '" data-currency_symbol = true>' . $row->total_invoice . '</span>';
                })
                ->addColumn('due', function ($row) {
                    if ($row->type == 'supplier') {
                        $due = ($row->total_invoice - $row->total_sell_return) - ($row->total_purchase - $row->total_purchase_return + $row->opening_balance);
                    } else {
                        $due = ($row->total_invoice - $row->total_sell_return + $row->opening_balance) - ($row->total_purchase - $row->total_purchase_return);
                    }
                    return '<span class="display_currency total_due" data-orig-value="' . $due . '" data-currency_symbol=true data-highlight=true>' . $due . '</span>';
                })
                ->addColumn(
                    'supplier_opening_balance',
                    '@if($type=="supplier")<span class="display_currency supplier_opening_balance" data-currency_symbol=true data-orig-value="{{$opening_balance - $opening_balance_paid}}">{{$opening_balance - $opening_balance_paid}}</span>@endif'
                )
                ->addColumn(
                    'customer_opening_balance',
                    '@if($type=="customer")<span class="display_currency customer_opening_balance" data-currency_symbol=true data-orig-value="{{$opening_balance - $opening_balance_paid}}">{{$opening_balance - $opening_balance_paid}}</span>@endif'
                )
                ->removeColumn('supplier_business_name')
                ->removeColumn('invoice_received')
                ->removeColumn('purchase_paid')
                ->removeColumn('id')
                ->rawColumns(['total_purchase', 'total_invoice', 'due', 'name', 'total_purchase_return', 'total_sell_return', 'customer_opening_balance', 'supplier_opening_balance'])
                ->make(true);
        }
        $customer_group = ContactGroup::forDropdown($business_id, false, true);
        $types = Contact::typeDropdown(true);
        return view('report.contact')
            ->with(compact('customer_group', 'types'));
    }
    /**
     * Shows product stock report
     *
     * @return \Illuminate\Http\Response
     */
    public function getStockReport(Request $request)
    {
        if (!auth()->user()->can('stock_report.view')) {
            abort(403, 'Unauthorized action.');
        }
        $business_id = $request->session()->get('user.business_id');
        $selling_price_groups = SellingPriceGroup::where('business_id', $business_id)
            ->get();
        $allowed_selling_price_group = false;
        foreach ($selling_price_groups as $selling_price_group) {
            if (auth()->user()->can('selling_price_group.' . $selling_price_group->id)) {
                $allowed_selling_price_group = true;
                break;
            }
        }
        $fuel_category_id = Category::where('business_id', $business_id)->where('name', 'Fuel')->first();
        if (!empty($fuel_category_id)) {
            $fuel_category_id = $fuel_category_id->id;
        } else {
            $fuel_category_id = null;
        }
        //Return the details in ajax call
        if ($request->ajax()) {
            $query = Variation::join('products as p', 'p.id', '=', 'variations.product_id')
                ->join('units', 'p.unit_id', '=', 'units.id')
                ->leftjoin('variation_location_details as vld', 'variations.id', '=', 'vld.variation_id')
                ->leftjoin('variation_store_details as vsd', 'variations.id', '=', 'vsd.variation_id')
                ->join('product_variations as pv', 'variations.product_variation_id', '=', 'pv.id')
                ->where('p.business_id', $business_id)
                ->whereIn('p.type', ['single', 'variable']);
            $permitted_locations = auth()->user()->permitted_locations();
            $start_date = request()->get('start_date', null);
            $end_date = request()->get('end_date', null);
            if (!empty($start_date) && !empty($end_date)) {
                $location_filter = " AND date(transactions.transaction_date) <= '$end_date' ";
            } else {
                $location_filter = '';
            }
            if ($permitted_locations != 'all') {
                $query->whereIn('vld.location_id', $permitted_locations);
                $locations_imploded = implode(', ', $permitted_locations);
                $location_filter .= "AND transactions.location_id IN ($locations_imploded) ";
            }
            if (!empty($request->input('location_id'))) {
                $location_id = $request->input('location_id');
                $query->where('vld.location_id', $location_id);
                $location_filter .= "AND transactions.location_id=$location_id";
                //If filter by location then hide products not available in that location
                $query->join('product_locations as pl', 'pl.product_id', '=', 'p.id')
                    ->where(function ($q) use ($location_id) {
                        $q->where('pl.location_id', $location_id);
                    });
            }
            if (!empty($request->input('category_id'))) {
                $query->where('p.category_id', $request->input('category_id'));
            }
            if (!empty($request->input('sub_category_id'))) {
                $query->where('p.sub_category_id', $request->input('sub_category_id'));
            }
            if (!empty($request->input('brand_id'))) {
                $query->where('p.brand_id', $request->input('brand_id'));
            }
            if (!empty($request->input('unit_id'))) {
                $query->where('p.unit_id', $request->input('unit_id'));
            }
            if (!empty($request->input('store_id'))) {
                $query->where('vsd.store_id', $request->input('store_id'));
            }
            $sub_category_id = request()->get('sub_category_id', null);
            if (!empty($sub_category_id)) {
                $query->where('p.sub_category_id', $sub_category_id);
            }
            $product_id = request()->get('product_id', null);
            if (!empty($product_id)) {
                $query->where('p.id', $product_id);
            }
            $only_manufactured_product = request()->get('only_manufactured_product', null);
            if (!empty($only_manufactured_product)) {
                $query->where('p.id', $only_manufactured_product);
            }
            $tax_id = request()->get('tax_id', null);
            if (!empty($tax_id)) {
                $query->where('p.tax', $tax_id);
            }
            $type = request()->get('type', null);
            if (!empty($type)) {
                $query->where('p.type', $type);
            }
            $start_date = request()->get('start_date', null);
            $end_date = request()->get('end_date', null);
            // if (!empty($start_date) && !empty($end_date)) {
            //     $query->where('variation_location_details.created_at', '>=', $start_date);
            //     $query->where('variation_location_details.created_at', '<=', $end_date);
            // }
            $only_mfg_products = request()->get('only_mfg_products', 0);
            if (!empty($only_mfg_products)) {
                $query->join('mfg_recipes as mr', 'mr.variation_id', '=', 'variations.id');
            }
            $active_state = request()->get('active_state', null);
            if ($active_state == 'active') {
                $query->where('p.is_inactive', 0);
            }
            if ($active_state == 'inactive') {
                $query->where('p.is_inactive', 1);
            }
            $not_for_selling = request()->get('not_for_selling', null);
            if ($not_for_selling == 'true') {
                $query->where('p.not_for_selling', 1);
            }
            $price_query_part = "(purchase_lines.purchase_price + 
            COALESCE(purchase_lines.item_tax, 0))";
            //TODO::Check if result is correct after changing LEFT JOIN to INNER JOIN
            $pl_query_string = $this->productUtil->get_pl_quantity_sum_string('pl');
            $products = $query->select(
                DB::raw("(SELECT SUM(TSL.quantity - TSL.quantity_returned) FROM transactions
                        JOIN transaction_sell_lines AS TSL ON transactions.id=TSL.transaction_id
                        WHERE transactions.status='final' AND transactions.type='sell' AND (transactions.sub_type IS NULL OR transactions.sub_type='settlement') $location_filter 
                        AND TSL.variation_id=variations.id) as total_sold"),
                DB::raw("(SELECT SUM((TSL.quantity - TSL.quantity_returned) * unit_price) FROM transactions 
                        JOIN transaction_sell_lines AS TSL ON transactions.id=TSL.transaction_id
                        WHERE transactions.status='final' AND transactions.type='sell' AND (transactions.sub_type IS NULL OR transactions.sub_type='settlement') $location_filter 
                        AND TSL.variation_id=variations.id) as total_sold_value"),
                DB::raw("(SELECT SUM(TSL.quantity_returned) FROM transactions 
                        JOIN transaction_sell_lines AS TSL ON transactions.id=TSL.transaction_id
                        WHERE transactions.status='final' AND transactions.type='sell' $location_filter 
                        AND TSL.variation_id=variations.id) as total_sold_return"),
                DB::raw("(SELECT SUM(IF(transactions.type='sell_transfer', TSL.quantity, 0) ) FROM transactions 
                        JOIN transaction_sell_lines AS TSL ON transactions.id=TSL.transaction_id
                        WHERE transactions.status='final' AND transactions.type='sell_transfer' $location_filter 
                        AND (TSL.variation_id=variations.id)) as total_transfered"),
                DB::raw("(SELECT SUM(IF(transactions.type='stock_adjustment', SAL.quantity, 0) ) FROM transactions 
                        JOIN stock_adjustment_lines AS SAL ON transactions.id=SAL.transaction_id
                        WHERE transactions.status='received' AND transactions.type='stock_adjustment' $location_filter 
                        AND (SAL.variation_id=variations.id)) as total_adjusted"),
                DB::raw("(SELECT SUM( COALESCE(pl.quantity - ($pl_query_string), 0) * variations.dpp_inc_tax) FROM transactions 
                        JOIN purchase_lines AS pl ON transactions.id=pl.transaction_id
                        WHERE transactions.status='received' $location_filter 
                        AND (pl.variation_id=variations.id)) as stock_price"),
                DB::raw("(SELECT SUM( COALESCE(pl.quantity_returned , 0)) FROM transactions 
                        JOIN purchase_lines AS pl ON transactions.id=pl.transaction_id
                        WHERE transactions.status='received' $location_filter 
                        AND (pl.variation_id=variations.id)) as total_purchase_return"),
                DB::raw("(SELECT SUM( COALESCE(pl.quantity , 0)) FROM transactions 
                        JOIN purchase_lines AS pl ON transactions.id=pl.transaction_id
                        WHERE transactions.type='purchase' AND transactions.status='received' $location_filter 
                        AND (pl.variation_id=variations.id)) as total_purchase"),
                DB::raw("SUM(vld.qty_available) as stock"),
                'variations.sub_sku as sku',
                'p.name as product',
                'p.category_id',
                'p.type',
                'p.id as product_id',
                'units.short_name as unit',
                'p.enable_stock as enable_stock',
                'variations.sell_price_inc_tax as unit_price',
                'variations.dpp_inc_tax as unit_purchase_price',
                'pv.name as product_variation',
                'variations.name as variation_name'
            );
            $is_mfg_installed = $this->moduleUtil->isModuleInstalled('Manufacturing');
            if ($is_mfg_installed) {
                $pl_query_string = $this->productUtil->get_pl_quantity_sum_string('PL');
                $products->addSelect(
                    DB::raw("(SELECT COALESCE(SUM(PL.quantity - ($pl_query_string)), 0) FROM transactions 
                        JOIN purchase_lines AS PL ON transactions.id=PL.transaction_id
                        WHERE transactions.status='received' AND transactions.type='production_purchase' $location_filter 
                        AND (PL.variation_id=variations.id)) as total_mfg_stock")
                );
            }
            $products->groupBy('variations.id');
            $business_details = Business::find($business_id);
            $datatable =  Datatables::of($products)
                ->editColumn('stock', function ($row) use ($fuel_category_id) {
                    if ($row->enable_stock) {
                        if ($row->category_id == $fuel_category_id) {
                            $stock = $this->transactionUtil->getTankProductBalanceByProductId($row->product_id);
                        } else {
                            $stock = $row->stock ? $row->stock : 0;
                        }
                        return  '<span data-is_quantity="true" class="current_stock display_currency" data-orig-value="' . (float) $stock . '" data-unit="' . $row->unit . '" data-currency_symbol=false > ' . (float) $stock . '</span> <span class="unit_name">' . ' ' . $row->unit . '</span>';
                    } else {
                        return 'N/A';
                    }
                })
                ->editColumn('product', function ($row) {
                    $name = $row->product;
                    if ($row->type == 'variable') {
                        $name .= ' - ' . $row->product_variation . '-' . $row->variation_name;
                    }
                    return $name;
                })
                ->addColumn('units', function ($row) use ($business_id) {
                    return $this->productUtil->getProductUnitsDropdown($row->product_id);
                })
                ->editColumn('total_sold', function ($row) use ($business_details) {
                    $total_sold = 0;
                    if ($row->total_sold) {
                        $total_sold =  (float) $row->total_sold;
                    }
                    return '<span data-is_quantity="true" class="display_currency total_sold" data-currency_symbol=false data-orig-value="' . $total_sold . '" data-unit="' . $row->unit . '" >' . $this->productUtil->num_f($total_sold, false, $business_details, true) . '</span> <span class="unit_name">' . $row->unit . '</span>';
                })
                ->editColumn('total_transfered', function ($row) use ($business_details) {
                    $total_transfered = 0;
                    if ($row->total_transfered) {
                        $total_transfered =  (float) $row->total_transfered;
                    }
                    return '<span data-is_quantity="true" class="display_currency total_transfered" data-currency_symbol=false data-orig-value="' . $total_transfered . '" data-unit="' . $row->unit . '" >' . $this->productUtil->num_f($total_transfered, false, $business_details, true) . '</span> ' . $row->unit;
                })
                ->editColumn('total_adjusted', function ($row) use ($business_details) {
                    $total_adjusted = 0;
                    if ($row->total_adjusted) {
                        $total_adjusted =  (float) $row->total_adjusted;
                    }
                    return '<span data-is_quantity="true" class="display_currency total_adjusted" data-currency_symbol=false  data-orig-value="' . $total_adjusted . '" data-unit="' . $row->unit . '" >' . $this->productUtil->num_f($total_adjusted, false, $business_details, true) . '</span> ' . $row->unit;
                })
                ->editColumn('unit_purchase_price', function ($row) use ($business_details) {
                    $html = '<span class="display_currency purchase_price" data-currency_symbol=true data-orig-value="' . $row->unit_purchase_price . '">'
                        . $this->productUtil->num_f($row->unit_purchase_price, false, $business_details, false) . '</span>';
                    return $html;
                })
                ->editColumn('unit_price', function ($row) use ($allowed_selling_price_group, $business_details) {
                    $html = '';
                    if (auth()->user()->can('access_default_selling_price')) {
                        $html .= '<span class="display_currency selling_price" data-currency_symbol=true data-orig-value="' . $row->unit_price . '" >'
                            . $this->productUtil->num_f($row->unit_price, false, $business_details, false) . '</span>';
                    }
                    if ($allowed_selling_price_group) {
                        $html .= ' <button type="button" class="btn btn-primary btn-xs btn-modal no-print" data-container=".view_modal" data-href="' . action('ProductController@viewGroupPrice', [$row->product_id]) . '">' . __('lang_v1.view_group_prices') . '</button>';
                    }
                    return $html;
                })
                ->editColumn('stock_price', function ($row) use ($business_details) {
                    $html = '<span class="display_currency total_stock_price current_stock_price" data-currency_symbol=true data-orig-value="'
                        . $row->stock * $row->unit_purchase_price  . '">'
                        . $this->productUtil->num_f($row->stock * $row->unit_purchase_price, false, $business_details, false) . '</span>';
                    return $html;
                })
                ->editColumn('total_sold_value', function ($row) use ($business_details) {
                    $html = '<span class="display_currency total_total_sold_value total_sold_value" data-currency_symbol=true data-orig-value="'
                        . $row->total_sold_value . '">'
                        . $this->productUtil->num_f($row->total_sold_value, false, $business_details, false) . '</span>';
                    return $html;
                })
                ->editColumn('total_purchase_return', function ($row) {
                    $total_purchase_return = 0;
                    if ($row->total_purchase_return) {
                        $total_purchase_return =  (float) $row->total_purchase_return;
                    }
                    return '<span data-is_quantity="true" class="display_currency total_purchase_return" data-currency_symbol=false  data-orig-value="' . $total_purchase_return . '" data-unit="' . $row->unit . '" >' . $total_purchase_return . '</span> ' . $row->unit;
                })
                ->editColumn('total_sold_return', function ($row) {
                    $total_sold_return = 0;
                    if ($row->total_sold_return) {
                        $total_sold_return =  (float) $row->total_sold_return;
                    }
                    return '<span data-is_quantity="true" class="display_currency total_sold_return" data-currency_symbol=false  data-orig-value="' . $total_sold_return . '" data-unit="' . $row->unit . '" >' . $total_sold_return . '</span> ' . $row->unit;
                })
                ->editColumn('total_purchase', function ($row) {
                    $total_purchase = 0;
                    if ($row->total_purchase) {
                        $total_purchase =  (float) $row->total_purchase;
                    }
                    return '<span data-is_quantity="true" class="display_currency total_purchase" data-currency_symbol=false  data-orig-value="' . $total_purchase . '" data-unit="' . $row->unit . '" >' . $total_purchase . '</span> ' . $row->unit;
                })
                ->removeColumn('enable_stock')
                ->removeColumn('unit')
                ->removeColumn('id');
            $raw_columns  = [
                'unit_price', 'total_transfered', 'total_sold',
                'total_adjusted', 'stock', 'stock_price', 'total_sold_value',
                'total_purchase_return', 'total_sold_return', 'total_purchase', 'unit_purchase_price', 'units'
            ];
            if ($is_mfg_installed) {
                $datatable->editColumn('total_mfg_stock', function ($row) {
                    $total_mfg_stock = 0;
                    if ($row->total_mfg_stock) {
                        $total_mfg_stock =  (float) $row->total_mfg_stock;
                    }
                    return '<span data-is_quantity="true" class="display_currency total_mfg_stock" data-currency_symbol=false  data-orig-value="' . $total_mfg_stock . '" data-unit="' . $row->unit . '" >' . $total_mfg_stock . '</span> ' . $row->unit;
                });
                $raw_columns[] = 'total_mfg_stock';
            }
            return $datatable->rawColumns($raw_columns)->make(true);
        }
        $categories = Category::where('business_id', $business_id)
            ->where('parent_id', 0)
            ->pluck('name', 'id');
        $brands = Brands::where('business_id', $business_id)
            ->pluck('name', 'id');
        $sub_categories = Category::subCategoryforDropdown($business_id);
        $products = Product::where('business_id', $business_id)->pluck('name', 'id');
        $only_manufactured_products = Variation::join('products as p', 'p.id', '=', 'variations.product_id')->join('mfg_recipes as mr', 'mr.variation_id', '=', 'variations.id')->where('p.business_id', $business_id)->pluck('p.name', 'p.id');
        $units = Unit::where('business_id', $business_id)
            ->pluck('short_name', 'id');
        $business_locations = BusinessLocation::forDropdown($business_id, true);
        $mf_module = $this->moduleUtil->hasThePermissionInSubscription($business_id, 'mf_module');
        return view('report.stock_report')
            ->with(compact('categories', 'brands', 'units', 'business_locations', 'sub_categories', 'products', 'only_manufactured_products', 'mf_module'));
    }
    /**
     * Shows product product transaciton report
     *
     * @return \Illuminate\Http\Response
     */
    public function getWeightLossExcessReport(Request $request)
    {
        $business_id = $request->session()->get('user.business_id');
        //Return the details in ajax call
        if ($request->ajax()) {
            $query = Transaction::leftjoin('transaction_sell_lines as tsl', 'transactions.id', 'tsl.transaction_id')
                ->leftjoin('products as p', 'tsl.product_id', 'p.id')
                ->leftjoin('variations', 'p.id', 'variations.product_id')
                ->leftjoin('business_locations', 'transactions.location_id', 'business_locations.id')
                ->leftjoin('contacts', 'transactions.contact_id', 'contacts.id')
                ->leftjoin('units', 'p.unit_id', '=', 'units.id')
                ->leftjoin('variation_location_details as vld', 'variations.id', '=', 'vld.variation_id')
                ->leftjoin('variation_store_details as vsd', 'variations.id', '=', 'vsd.variation_id')
                ->leftjoin('product_variations as pv', 'variations.product_variation_id', '=', 'pv.id')
                ->where('p.business_id', $business_id);
            $permitted_locations = auth()->user()->permitted_locations();
            $location_filter = '';
            if ($permitted_locations != 'all') {
                $query->whereIn('vld.location_id', $permitted_locations);
                $locations_imploded = implode(', ', $permitted_locations);
                $location_filter .= "AND transactions.location_id IN ($locations_imploded) ";
            }
            if (!empty($request->input('location_id'))) {
                $query->where('transactions.location_id', $request->input('location_id'));
            }
            if (!empty($request->input('start_date')) && !empty($request->input('end_date'))) {
                $query->whereDate('transaction_date', '>=', $request->input('start_date'))
                    ->whereDate('transaction_date', '<=', $request->input('end_date'));
            }
            if (!empty($request->input('contact_id'))) {
                $query->where('transactions.contact_id', $request->input('contact_id'));
            }
            if (!empty($request->input('type'))) {
                if ($request->input('type') == 'loss') {
                    $query->where('tsl.weight_loss', '>', 0);
                }
                if ($request->input('type') == 'excess') {
                    $query->where('tsl.weight_excess', '>', 0);
                }
            }
            if (!empty($request->input('unit_id'))) {
                $query->where('p.unit_id', $request->input('unit_id'));
            }
            if (!empty($request->input('product_id'))) {
                $query->where('p.id', $request->input('product_id'));
            }
            $type = request()->get('type', null);
            if (!empty($type)) {
                $query->where('p.type', $type);
            }
            $products = $query->select(
                'variations.sub_sku as sku',
                'p.name as product',
                'p.type',
                'p.id as product_id',
                'units.short_name as unit',
                'p.enable_stock as enable_stock',
                'variations.sell_price_inc_tax as unit_price',
                'pv.name as product_variation',
                'variations.name as variation_name',
                'tsl.weight_loss',
                'tsl.weight_excess',
                'business_locations.name as location_name',
                'contacts.name as customer_name',
                'transactions.transaction_date',
                'transactions.ref_no',
                'transactions.final_total',
                'transactions.invoice_no'
            )->orderBy('transaction_date', 'desc')
                ->groupBy(['tsl.id']);
            $datatable =  Datatables::of($products)
                ->editColumn('product', function ($row) {
                    $name = $row->product;
                    if ($row->type == 'variable') {
                        $name .= ' - ' . $row->product_variation . '-' . $row->variation_name;
                    }
                    return $name;
                })
                ->editColumn('transaction_date', '{{@format_date($transaction_date)}}')
                ->editColumn('final_total', function ($row) {
                    $final_total = 0;
                    if ($row->final_total) {
                        $final_total =  (float) $row->final_total;
                    }
                    return '<span data-is_quantity="false" class="display_currency final_total" data-currency_symbol=false data-orig-value="' . $final_total . '" >' . $final_total . '</span> ';
                })
                ->addColumn('weight_loss_excess_qty', function ($row) {
                    $qty = 0;
                    if (!empty($row->weight_loss)) {
                        $qty = $row->weight_loss;
                    }
                    if (!empty($row->weight_excess)) {
                        $qty = $row->weight_excess;
                    }
                    return '<span data-is_quantity="true" class="display_currency qty" data-currency_symbol=false data-orig-value="' . $qty . '" >' . $qty . '</span> ';
                })
                ->addColumn('weight_loss_excess', function ($row) {
                    if (!empty($row->weight_loss)) {
                        return 'Weight Loss';
                    }
                    if (!empty($row->weight_excess)) {
                        return 'Weight Excess';
                    }
                });
            $raw_columns  = ['transaction_date', 'final_total', 'weight_loss_excess', 'weight_loss_excess_qty'];
            return $datatable->rawColumns($raw_columns)->escapeColumns([])->make(true);
        }
    }
    /**
     * Shows product product transaciton report
     *
     * @return \Illuminate\Http\Response
     */
    public function getProductTransactionReport(Request $request)
    {
        if (!auth()->user()->can('product_transaction_report.view')) {
            abort(403, 'Unauthorized action.');
        }
        $business_id = $request->session()->get('user.business_id');
        $default_start = new Carbon('first day of this month');
        $default_end = new Carbon('last day of this month');
        $start_date = !empty($request->get('start_date')) ? date('Y-m-d', strtotime($request->get('start_date'))) : $default_start->format('Y-m-d');
        $end_date = !empty($request->get('end_date')) ? date('Y-m-d', strtotime($request->get('end_date'))) :  $default_end->format('Y-m-d');
        //Return the details in ajax call
        if ($request->ajax()) {
            $query = Transaction::leftjoin('purchase_lines as pl', 'transactions.id', 'pl.transaction_id')
                ->leftjoin('purchase_lines as PRL', 'transactions.return_parent_id', 'PRL.transaction_id')
                ->leftjoin('transaction_sell_lines as tsl', function ($join) {
                    $join->on('transactions.id', 'tsl.transaction_id');
                })
                ->leftjoin('transaction_sell_lines as SRL', 'transactions.return_parent_id', 'SRL.transaction_id')
                ->leftjoin('stock_adjustment_lines', 'transactions.id', 'stock_adjustment_lines.transaction_id')
                ->leftjoin('products as p', function ($join) {
                    $join->on('pl.product_id', 'p.id')
                        ->orOn('tsl.product_id', 'p.id')
                        ->orOn('stock_adjustment_lines.product_id', 'p.id')
                        ->orOn('PRL.product_id', 'p.id')
                        ->orOn('SRL.product_id', 'p.id');
                })
                ->leftjoin('variations', 'p.id', 'variations.product_id')
                ->leftjoin('units', 'p.unit_id', '=', 'units.id')
                ->leftjoin('variation_location_details as vld', 'variations.id', '=', 'vld.variation_id')
                ->leftjoin('variation_store_details as vsd', 'variations.id', '=', 'vsd.variation_id')
                ->leftjoin('product_variations as pv', 'variations.product_variation_id', '=', 'pv.id')
                ->where('transactions.sub_type', '!=', 'credit_sale')
                ->where('p.business_id', $business_id)->withTrashed();
            // ->whereIn('p.type', ['single', 'variable']);
            $permitted_locations = auth()->user()->permitted_locations();
            $location_filter = '';
            if ($permitted_locations != 'all') {
                $query->whereIn('vld.location_id', $permitted_locations);
                $locations_imploded = implode(', ', $permitted_locations);
                $location_filter .= "AND transactions.location_id IN ($locations_imploded) ";
            }
            if (!empty($request->input('location_id'))) {
                $location_id = $request->input('location_id');
                $query->where('vld.location_id', $location_id);
                $location_filter .= "AND transactions.location_id=$location_id";
                //If filter by location then hide products not available in that location
                $query->join('product_locations as pls', 'pls.product_id', '=', 'p.id')
                    ->where(function ($q) use ($location_id) {
                        $q->where('pls.location_id', $location_id);
                    });
            }
            if (!empty($start_date) && !empty($end_date)) {
                $query->whereDate('transaction_date', '>=', $start_date)
                    ->whereDate('transaction_date', '<=', $end_date);
            }
            if (!empty($request->input('category_id'))) {
                $query->where('p.category_id', $request->input('category_id'));
            }
            if (!empty($request->input('sub_category_id'))) {
                $query->where('p.sub_category_id', $request->input('sub_category_id'));
            }
            if (!empty($request->input('brand_id'))) {
                $query->where('p.brand_id', $request->input('brand_id'));
            }
            if (!empty($request->input('unit_id'))) {
                $query->where('p.unit_id', $request->input('unit_id'));
            }
            if (!empty($request->input('store_id'))) {
                $query->where('vsd.store_id', $request->input('store_id'));
            }
            if (!empty($request->input('product_id'))) {
                $query->where('p.id', $request->input('product_id'));
            }
            $type = request()->get('type', null);
            if (!empty($type)) {
                $query->where('p.type', $type);
            }
            $pl_query_string = $this->productUtil->get_pl_quantity_sum_string('pl');
            $products = $query->select(
                'variations.sub_sku as sku',
                'p.name as product',
                'p.type',
                'p.date',
                'p.id as product_id',
                'units.short_name as unit',
                'p.enable_stock as enable_stock',
                'variations.sell_price_inc_tax as unit_price',
                'pv.name as product_variation',
                'variations.name as variation_name',
                'pl.purchase_price_inc_tax  as purchase_price',
                'pl.bonus_qty  as bonus_qty',
                // 'tsl.quantity as sq',
                DB::raw('SUM(tsl.quantity) as sq'),
                DB::raw('SUM(tsl.quantity_returned) as srq'),
                'transactions.transaction_date  as transaction_date',
                'transactions.type as tran_type',
                'transactions.ref_no',
                'transactions.invoice_no',
                'transactions.id as transaction_id',
                'pl.id as pl_id',
                'tsl.id as tsl_id',
                'tsl.weight_excess',
                'tsl.weight_loss',
                'transactions.deleted_by'
            )
                ->orderBy('transactions.id', 'asc')
                ->groupBy(['transactions.id', 'p.id']);
            $products_ids = $products->pluck('product_id')->toArray();
            Session::forget($products_ids);
            $datatable =  Datatables::of($products)
                ->editColumn('stock', function ($row) {
                    if ($row->enable_stock) {
                        $stock = $row->stock ? $row->stock : 0;
                        return  '<span data-is_quantity="true" class="current_stock display_currency" data-orig-value="' . (float) $stock . '" data-unit="' . $row->unit . '" data-currency_symbol=false > ' . (float) $stock . '</span>' . ' ' . $row->unit;
                    } else {
                        return 'N/A';
                    }
                })
                ->editColumn('product', function ($row) {
                    $name = $row->product;
                    if ($row->type == 'variable') {
                        $name .= ' - ' . $row->product_variation . '-' . $row->variation_name;
                    }
                    return $name;
                })
                ->editColumn('total_sold', function ($row) {
                    $total_sold = 0;
                    if ($row->total_sold) {
                        $total_sold =  (float) $row->total_sold;
                    }
                    return '<span data-is_quantity="true" class="display_currency total_sold" data-currency_symbol=false data-orig-value="' . $total_sold . '" data-unit="' . $row->unit . '" >' . $total_sold . '</span> ' . $row->unit;
                })
                ->editColumn('stock_price', function ($row) {
                    $html = '<span class="display_currency total_stock_price" data-currency_symbol=true data-orig-value="'
                        . $row->stock_price . '">'
                        . $row->stock_price . '</span>';
                    return $html;
                })
                ->removeColumn('enable_stock')
                ->removeColumn('unit')
                ->removeColumn('id')
                ->addColumn('description', function ($row) {
                    $html = "";
                    if ($row->tran_type == 'opening_stock') {
                        $business_id = request()->session()->get('user.business_id');
                        $business_details = Business::find($business_id);
                        $query = Transaction::leftjoin('purchase_lines', 'transactions.id', 'purchase_lines.transaction_id')
                            ->leftjoin('purchase_lines as PRL', 'transactions.return_parent_id', 'PRL.transaction_id')
                            ->leftjoin('transaction_sell_lines', 'transactions.id', 'transaction_sell_lines.transaction_id')
                            ->leftjoin('transaction_sell_lines as SRL', 'transactions.return_parent_id', 'SRL.transaction_id')
                            ->join('products', function ($join) {
                                $join
                                    ->on('purchase_lines.product_id', 'products.id')
                                    ->orOn('transaction_sell_lines.product_id', 'products.id')
                                    ->orOn('PRL.product_id', 'products.id')
                                    ->orOn('SRL.product_id', 'products.id');
                            })
                            ->where('transactions.business_id', $business_id)
                            ->where('products.id', $row->product_id)
                            ->whereDate('transactions.transaction_date', '<', $row->transaction_date)
                            ->select(
                                DB::raw('SUM(transaction_sell_lines.quantity) as sold_qty'),
                                DB::raw('SUM(SRL.quantity_returned) as sell_return'),
                                DB::raw('SUM(purchase_lines.quantity ) as purchase_qty'),
                                DB::raw('SUM(PRL.quantity_returned ) as purchase_return')
                            )->withTrashed()
                            ->orderBy('transactions.transaction_date', 'desc')
                            ->first();
                        $balance = ($this->productUtil->num_uf($query->purchase_qty)  + $this->productUtil->num_uf($query->sell_return))
                            - ($this->productUtil->num_uf($query->sold_qty)
                                + $this->productUtil->num_uf($query->purchase_return));
                        $html = "Opening Stock: ";
                        $html .= $balance;
                    } elseif ($row->tran_type == 'purchase') {
                        $html = "Purchase Number: ";
                        $html .= !empty($row->ref_no) ? $row->ref_no : $row->invoice_no;
                        if (!empty($row->deleted_by)) {
                            $delete_by = User::findOrFail($row->deleted_by);
                            $html .= '<br><b>Deleted by </b>' . $delete_by->username;
                        }
                    } elseif ($row->tran_type == 'purchase_return') {
                        $html = "Purchase Return Number: ";
                        $html .= !empty($row->ref_no) ? $row->ref_no : $row->invoice_no;
                    } elseif ($row->tran_type == 'stock_adjustment') {
                        $html = "Stock Adjustment Number: ";
                        $html .= !empty($row->ref_no) ? $row->ref_no : $row->invoice_no;
                    } elseif ($row->tran_type == 'sell') {
                        $html = 'Sale Bill Number: ' . $row->invoice_no .'<br>';
                        if(!empty($row->weight_excess) && $row->weight_excess > 0){
                            $html .= '<b>'.__('lang_v1.weight_excess').'</b><br>';
                        }
                        if(!empty($row->weight_loss) && $row->weight_loss > 0){
                            $html .= '<b>'.__('lang_v1.weight_loss').'</b><br>';
                        }
                        if (!empty($row->deleted_by)) {
                            $delete_by = User::findOrFail($row->deleted_by);
                            $html .= '<br><b>Deleted by </b>' . $delete_by->username;
                        }
                    } elseif ($row->tran_type == 'sell_return') {
                        $html = "Sales Return Number: ";
                        $html .= !empty($row->ref_no) ? $row->ref_no : $row->invoice_no;
                    }
                    return $html;
                })
                ->addColumn('purchase_qty', function ($row) use ($start_date, $end_date) {
                    $html = "";
                    $business_id = request()->session()->get('user.business_id');
                    $business_details = Business::find($business_id);
                    $query = Transaction::leftjoin('purchase_lines', 'transactions.id', 'purchase_lines.transaction_id')
                        ->leftjoin('purchase_lines as PRL', 'transactions.return_parent_id', 'PRL.transaction_id')
                        ->leftjoin('transaction_sell_lines', 'transactions.id', 'transaction_sell_lines.transaction_id')
                        ->leftjoin('transaction_sell_lines as SRL', 'transactions.return_parent_id', 'SRL.transaction_id')
                        ->leftjoin('stock_adjustment_lines', 'transactions.id', 'stock_adjustment_lines.transaction_id')
                        ->join('products', function ($join) {
                            $join->on('purchase_lines.product_id', 'products.id')
                                ->orOn('transaction_sell_lines.product_id', 'products.id')
                                ->orOn('PRL.product_id', 'products.id')
                                ->orOn('stock_adjustment_lines.product_id', 'products.id')
                                ->orOn('SRL.product_id', 'products.id');
                        })
                        ->where('transactions.business_id', $business_id)
                        ->where('products.id', $row->product_id)
                        ->where('transactions.id', '=', $row->transaction_id)
                        ->select(
                            DB::raw('SUM(transaction_sell_lines.quantity) as sold_qty'),
                            DB::raw('SUM(SRL.quantity_returned) as sell_return'),
                            DB::raw('SUM(IF(transactions.deleted_at IS NULL, purchase_lines.quantity, -1* purchase_lines.quantity) ) as purchase_qty'),
                            DB::raw('SUM(purchase_lines.quantity_returned ) as returned_qty'),
                            DB::raw('SUM(stock_adjustment_lines.quantity ) as stock_qty'),
                            DB::raw('stock_adjustment_lines.type as addjust_type')
                        )->withTrashed()
                        ->orderBy('transactions.transaction_date', 'desc')
                        ->first();
                    if ($row->tran_type == 'purchase_return') {
                        $res = $this->productUtil->num_f($query->returned_qty,  false, $business_details, true);
                        if ($res > 0.00) {
                            $html = '-' . $res;
                        } else {
                            $html = $res;
                        }
                    } else if ($row->tran_type == 'stock_adjustment') {
                        if ($query->addjust_type == 'increase') {
                            $html = $this->productUtil->num_f($query->stock_qty, false, $business_details, true);
                        } else if ($query->addjust_type == 'decrease') {
                            $res = $this->productUtil->num_f($query->stock_qty,  false, $business_details, true);
                            if ($res > 0.00) {
                                $html = '-' . $res;
                            } else {
                                $html = $res;
                            }
                        }
                    } else {
                        $purchase_qty = 0.0;
                        if (!empty($query->purchase_qty)) {
                            $purchase_qty = $query->purchase_qty;
                        }
                        //  $html = $this->productUtil->num_f($query->purchase_qty - $query->bonus_qty, false, $business_details, true);
                        $html = $this->productUtil->num_f($purchase_qty);
                    }
                    return $html;
                })
                ->addColumn('sold_qty', function ($row) {
                    $business_id = request()->session()->get('user.business_id');
                    $business_details = Business::find($business_id);
                    $query = Transaction::leftjoin('purchase_lines', 'transactions.id', 'purchase_lines.transaction_id')
                        ->leftjoin('purchase_lines as PRL', 'transactions.return_parent_id', 'PRL.transaction_id')
                        ->leftjoin('transaction_sell_lines', 'transactions.id', 'transaction_sell_lines.transaction_id')
                        ->leftjoin('transaction_sell_lines as SRL', 'transactions.return_parent_id', 'SRL.transaction_id')
                        ->join('products', function ($join) {
                            $join->on('purchase_lines.product_id', 'products.id')
                                ->orOn('transaction_sell_lines.product_id', 'products.id')
                                ->orOn('PRL.product_id', 'products.id')
                                ->orOn('SRL.product_id', 'products.id')
                                ->orOn('SRL.product_id', 'products.id');
                        })
                        ->where('transactions.business_id', $business_id)
                        ->where('products.id', $row->product_id)->where('transactions.id', '=', $row->transaction_id)
                        ->select(
                            DB::raw('SUM(IF(transactions.deleted_at IS NULL, transaction_sell_lines.quantity, -1*transaction_sell_lines.quantity) ) as sold_qty'),
                            DB::raw('SUM(SRL.quantity_returned) as sell_return')
                        )->withTrashed()
                        ->orderBy('transactions.transaction_date', 'desc')
                        ->first();
                    if ($row->tran_type == 'sell_return') {
                        $res = $this->productUtil->num_f($query->sell_return,  false, $business_details, true);
                        if ($res > 0.00) {
                            $html = '-' . $res;
                            return $html;
                        } else {
                            return $res;
                        }
                    } else {
                        $sold_qty = 0.0;
                        if (!empty($query->sold_qty)) {
                            $sold_qty = $query->sold_qty;
                        }
                        return $this->productUtil->num_f($sold_qty,  false, $business_details, true);
                    }
                })
                ->addColumn('bonus_qty', '{{@format_quantity($bonus_qty)}}')
                ->addColumn('starting_qty', function ($row) {
                    $business_id = request()->session()->get('user.business_id');
                    $st_query_pre = Transaction::leftjoin('purchase_lines', 'transactions.id', 'purchase_lines.transaction_id')
                        ->leftjoin('purchase_lines as PRL', 'transactions.return_parent_id', 'PRL.transaction_id')
                        ->leftjoin('transaction_sell_lines', 'transactions.id', 'transaction_sell_lines.transaction_id')
                        ->leftjoin('transaction_sell_lines as SRL', 'transactions.return_parent_id', 'SRL.transaction_id')
                        ->leftjoin('stock_adjustment_lines', 'transactions.id', 'stock_adjustment_lines.transaction_id')
                        ->join('products', function ($join) {
                            $join->on('purchase_lines.product_id', 'products.id')
                                ->orOn('transaction_sell_lines.product_id', 'products.id')
                                ->orOn('PRL.product_id', 'products.id')
                                ->orOn('stock_adjustment_lines.product_id', 'products.id')
                                ->orOn('SRL.product_id', 'products.id');;
                        })
                        ->where('transactions.business_id', $business_id)
                        ->where('products.id', $row->product_id)
                        ->where('transactions.id', '<', $row->transaction_id)
                        ->select(
                            DB::raw('SUM(IF(transactions.deleted_at IS NULL, transaction_sell_lines.quantity, 0) ) as sold_qty'),
                            DB::raw('SUM(SRL.quantity_returned) as sell_return'),
                            DB::raw('SUM(IF(transactions.deleted_at IS NULL, purchase_lines.quantity, 0) ) as purchase_qty'),
                            DB::raw('SUM(PRL.quantity_returned ) as purchase_return')
                        )->withTrashed()
                        ->orderBy('transactions.transaction_date', 'desc')
                        ->first();
                    $business_id = session()->get('user.business_id');
                    $business_details = Business::find($business_id);
                    // first time new value for product next time previous
                    if (Session::get($row->product_id)) {
                        $balance = str_replace(',', '', Session::get($row->product_id));
                    } else {
                        $balance = 0;
                        $balance = ($st_query_pre->purchase_qty  + $st_query_pre->sell_return) - ($st_query_pre->sold_qty
                            + $st_query_pre->purchase_return);
                        $balance = $balance;
                        Session::put($row->product_id, $balance);
                    }
                    return $this->productUtil->num_f($balance, false, $business_details, true);
                })
                ->addColumn('balance_qty', function ($row) {
                    $business_id = request()->session()->get('user.business_id');
                    $business_details = Business::find($business_id);
                    $starting_qty_query = Transaction::leftjoin('purchase_lines', 'transactions.id', 'purchase_lines.transaction_id')
                        ->leftjoin('purchase_lines as PRL', 'transactions.return_parent_id', 'PRL.transaction_id')
                        ->leftjoin('transaction_sell_lines', 'transactions.id', 'transaction_sell_lines.transaction_id')
                        ->leftjoin('transaction_sell_lines as SRL', 'transactions.return_parent_id', 'SRL.transaction_id')
                        ->leftjoin('stock_adjustment_lines', 'transactions.id', 'stock_adjustment_lines.transaction_id')
                        ->join('products', function ($join) {
                            $join->on('purchase_lines.product_id', 'products.id')
                                ->orOn('transaction_sell_lines.product_id', 'products.id')
                                ->orOn('stock_adjustment_lines.product_id', 'products.id')
                                ->orOn('PRL.product_id', 'products.id')
                                ->orOn('SRL.product_id', 'products.id')
                                ->orOn('SRL.product_id', 'products.id');
                        })
                        ->where('transactions.business_id', $business_id)
                        ->where('products.id', $row->product_id)->where('transactions.id', '<', $row->transaction_id)
                        ->select(
                            DB::raw('SUM(IF(transactions.deleted_at IS NULL, transaction_sell_lines.quantity, 0) ) as sold_qty'),
                            DB::raw('SUM(SRL.quantity_returned) as sell_return'),
                            DB::raw('SUM(IF(transactions.deleted_at IS NULL, purchase_lines.quantity, 0) ) as purchase_qty'),
                            DB::raw('SUM(purchase_lines.bonus_qty ) as bonus_qty'),
                            DB::raw('SUM(PRL.quantity_returned ) as purchase_return'),
                            DB::raw('SUM(stock_adjustment_lines.quantity ) as stock_qty'),
                            DB::raw('stock_adjustment_lines.type as addjust_type')
                        )->withTrashed()
                        ->orderBy('transactions.transaction_date', 'desc')
                        ->first();
                    $starting_qty = ($starting_qty_query->purchase_qty  + $starting_qty_query->sell_return) - ($starting_qty_query->sold_qty + $starting_qty_query->purchase_return);
                    $query = Transaction::leftjoin('purchase_lines', 'transactions.id', 'purchase_lines.transaction_id')
                        ->leftjoin('purchase_lines as PRL', 'transactions.return_parent_id', 'PRL.transaction_id')
                        ->leftjoin('transaction_sell_lines', 'transactions.id', 'transaction_sell_lines.transaction_id')
                        ->leftjoin('transaction_sell_lines as SRL', 'transactions.return_parent_id', 'SRL.transaction_id')
                        ->leftjoin('stock_adjustment_lines', 'transactions.id', 'stock_adjustment_lines.transaction_id')
                        ->join('products', function ($join) {
                            $join->on('purchase_lines.product_id', 'products.id')
                                ->orOn('transaction_sell_lines.product_id', 'products.id')
                                ->orOn('stock_adjustment_lines.product_id', 'products.id')
                                ->orOn('PRL.product_id', 'products.id')
                                ->orOn('SRL.product_id', 'products.id')
                                ->orOn('SRL.product_id', 'products.id');
                        })->withTrashed()
                        ->where('products.id', $row->product_id)->where('transactions.id', '=', $row->transaction_id)
                        ->select(
                            DB::raw('SUM(IF(transactions.deleted_at IS NULL, transaction_sell_lines.quantity, 0) ) as sold_qty'),
                            DB::raw('SUM(SRL.quantity_returned) as sell_return'),
                            DB::raw('SUM(IF(transactions.deleted_at IS NULL, purchase_lines.quantity, 0) ) as purchase_qty'),
                            DB::raw('SUM(purchase_lines.bonus_qty) as bonus_qty'),
                            DB::raw('SUM(purchase_lines.quantity_returned) as purchase_return'),
                            DB::raw('SUM(stock_adjustment_lines.quantity ) as stock_qty'),
                            DB::raw('stock_adjustment_lines.type as addjust_type')
                        )
                        ->orderBy('transactions.transaction_date', 'desc')
                        ->first();
                    if ($row->tran_type == 'stock_adjustment') {
                        if ($query->addjust_type == 'increase') {
                            $query->purchase_qty = $this->productUtil->num_uf($query->stock_qty);
                        } else if ($query->addjust_type == 'decrease') {
                            $res = $this->productUtil->num_uf($query->stock_qty);
                            $query->purchase_return = $res;
                        }
                    }
                    $balance = 0;
                    if (Session::get($row->product_id)) {
                        $oldbalance = str_replace(',', '', Session::get($row->product_id));
                        $balance = ($oldbalance + $query->purchase_qty + $query->sell_return)
                            - ($query->sold_qty + $query->purchase_return);
                        $balance = $balance;
                        Session::put($row->product_id, $balance);
                    } else {
                        $balance = ($starting_qty + $query->purchase_qty + $query->sell_return)
                            - ($query->sold_qty + $query->purchase_return);
                        $balance = $balance;
                        Session::put($row->product_id, $balance);
                    }
                    return $this->productUtil->num_f($balance, false, $business_details, true);
                })
                ->addColumn('balance_qty_value', function ($row) {
                    $business_id = request()->session()->get('user.business_id');
                    $business_details = Business::find($business_id);
                    $purchase_price = PurchaseLine::where('product_id', $row->product_id)->select('purchase_price')->orderBy('id', 'desc')->first();
                    if (!empty($purchase_price)) {
                        $purchase_price = $purchase_price->purchase_price;
                    } else {
                        $purchase_price = 0;
                    }
                    $balance_qty = 0;
                    if (Session::get($row->product_id)) {
                        $balance_qty =  str_replace(',', '', Session::get($row->product_id));
                    }
                    return $this->productUtil->num_f($balance_qty * $purchase_price, false, $business_details, true);
                })
                ->addColumn('action', function ($row) {
                    $html = '';
                    if ($row->tran_type == 'sell') {
                        $html = '<button href="#" data-href="' . action("SellController@show", [$row->transaction_id]) . '" class="btn-modal btn btn-primary btn-xs" data-container=".view_modal"><i class="fa fa-external-link" aria-hidden="true"></i> ' . __("messages.view") . '</button>';
                    }
                    if ($row->tran_type == 'purchase') {
                        $html = '<button href="#" data-href="' . action("PurchaseController@show", [$row->transaction_id]) . '" class="btn-modal btn btn-primary btn-xs" data-container=".view_modal"><i class="fa fa-external-link" aria-hidden="true"></i> ' . __("messages.view") . '</button>';
                    }
                    return $html;
                })
                ->editColumn('transaction_date', '{{@format_datetime($transaction_date)}}');
            $raw_columns  = [
                'unit_price', 'total_transfered', 'total_sold',
                'total_adjusted', 'stock', 'stock_price', 'bonus_qty', 'purchase_qty'
            ];
            return $datatable->rawColumns($raw_columns)->escapeColumns([])->make(true);
        }
        $categories = Category::where('business_id', $business_id)
            ->where('parent_id', 0)
            ->pluck('name', 'id');
        $brands = Brands::where('business_id', $business_id)
            ->pluck('name', 'id');
        $units = Unit::where('business_id', $business_id)
            ->pluck('short_name', 'id');
        $products = Product::where('business_id', $business_id)
            ->pluck('name', 'id');
        $business_locations = BusinessLocation::forDropdown($business_id, true);
        return view('report.product_transaction_report')
            ->with(compact('categories', 'products', 'brands', 'units', 'business_locations'));
    }
    /**
     * Shows product report of a business
     *
     * @return \Illuminate\Http\Response
     */
    public function getProductTransactionSummary(Request $request)
    {
        $data = $this->getPrdocutTransactionSummaryData($request);
        $opening_data = $this->getPrdocutTransactionSummaryData($request, 1);
        $opening_qty = ($opening_data['purchase_qty'] - $opening_data['sold_qty']);
        $opening_amount = ($opening_data['purchase_amount'] - $opening_data['sold_amount']);
        return [
            'sold_qty' => $data['sold_qty'],
            'purchase_qty' => $data['purchase_qty'],
            'sold_amount' => $data['sold_amount'],
            'purchase_amount' => $data['purchase_amount'],
            'opening_qty' =>  $opening_qty,
            'opening_amount' =>  $opening_amount,
            'balance_qty' => $opening_qty + ($data['purchase_qty'] - $data['sold_qty']),
            'balance_amount' => $opening_amount + ($data['purchase_amount'] - $data['sold_amount']),
        ];
    }
    public function getPrdocutTransactionSummaryData(Request $request, $is_pre = null)
    {
        if (!auth()->user()->can('purchase_n_sell_report.view')) {
            abort(403, 'Unauthorized action.');
        }
        $business_id = $request->session()->get('user.business_id');
        $default_start = new Carbon('first day of this month');
        $default_end = new Carbon('last day of this month');
        $start_date = !empty($request->get('start_date')) ? date('Y-m-d', strtotime($request->get('start_date'))) : $default_start->format('Y-m-d');
        $end_date = !empty($request->get('end_date')) ? date('Y-m-d', strtotime($request->get('end_date'))) :  $default_end->format('Y-m-d');
        //Return the details in ajax call
        if ($request->ajax()) {
            $location_id = $request->get('location_id');
            $query = Transaction::leftjoin('purchase_lines as pl', 'transactions.id', 'pl.transaction_id')
                ->leftjoin('transaction_sell_lines as tsl', 'transactions.id', 'tsl.transaction_id')
                ->join('products as p', function ($join) {
                    $join->on('pl.product_id', 'p.id')->orOn('tsl.product_id', 'p.id');
                })
                ->join('variations', 'p.id', 'variations.product_id')
                ->join('units', 'p.unit_id', '=', 'units.id')
                ->leftjoin('variation_location_details as vld', 'variations.id', '=', 'vld.variation_id')
                ->leftjoin('variation_store_details as vsd', 'variations.id', '=', 'vsd.variation_id')
                ->join('product_variations as pv', 'variations.product_variation_id', '=', 'pv.id')
                ->where('transactions.business_id', $business_id)
                ->where('transactions.sub_type', '!=', 'credit_sale')
                ->whereIn('p.type', ['single', 'variable']);
            if (!empty($request->input('location_id'))) {
                $location_id = $request->input('location_id');
                $query->where('vld.location_id', $location_id);
            }
            if (!empty($start_date) && !empty($end_date)) {
                if (empty($is_pre)) {
                    $query->whereDate('transaction_date', '>=', $start_date)
                        ->whereDate('transaction_date', '<=', $end_date);
                } else {
                    $query->whereDate('transactions.transaction_date', '<', $start_date);
                }
            }
            if (!empty($request->input('category_id'))) {
                $query->where('p.category_id', $request->input('category_id'));
            }
            if (!empty($request->input('sub_category_id'))) {
                $query->where('p.sub_category_id', $request->input('sub_category_id'));
            }
            if (!empty($request->input('brand_id'))) {
                $query->where('p.brand_id', $request->input('brand_id'));
            }
            if (!empty($request->input('unit_id'))) {
                $query->where('p.unit_id', $request->input('unit_id'));
            }
            if (!empty($request->input('store_id'))) {
                $query->where('vsd.store_id', $request->input('store_id'));
            }
            if (!empty($request->input('product_id'))) {
                $query->where('p.id', $request->input('product_id'));
            }
            $type = request()->get('type', null);
            if (!empty($type)) {
                $query->where('p.type', $type);
            }
            $data = $query->select(
                DB::raw('SUM(tsl.quantity-tsl.quantity_returned) as sold_qty'),
                DB::raw('SUM((tsl.quantity-tsl.quantity_returned)* variations.default_purchase_price) as sold_amount'),
                DB::raw('SUM(pl.quantity-pl.quantity_returned) as purchase_qty'),
                DB::raw('SUM((pl.quantity-pl.quantity_returned)*pl.purchase_price) as purchase_amount')
            )->first();
            return [
                'sold_qty' => $data->sold_qty,
                'sold_amount' => $data->sold_amount,
                'purchase_qty' => $data->purchase_qty,
                'purchase_amount' => $data->purchase_amount,
            ];
        }
    }
    /**
     * Shows product stock details
     *
     * @return \Illuminate\Http\Response
     */
    public function getStockDetails(Request $request)
    {
        //Return the details in ajax call
        if ($request->ajax()) {
            $business_id = $request->session()->get('user.business_id');
            $product_id = $request->input('product_id');
            $query = Product::leftjoin('units as u', 'products.unit_id', '=', 'u.id')
                ->join('variations as v', 'products.id', '=', 'v.product_id')
                ->join('product_variations as pv', 'pv.id', '=', 'v.product_variation_id')
                ->leftjoin('variation_location_details as vld', 'v.id', '=', 'vld.variation_id')
                ->where('products.business_id', $business_id)
                ->where('products.id', $product_id)
                ->whereNull('v.deleted_at');
            $permitted_locations = auth()->user()->permitted_locations();
            $location_filter = '';
            if ($permitted_locations != 'all') {
                $query->whereIn('vld.location_id', $permitted_locations);
                $locations_imploded = implode(', ', $permitted_locations);
                $location_filter .= "AND transactions.location_id IN ($locations_imploded) ";
            }
            if (!empty($request->input('location_id'))) {
                $location_id = $request->input('location_id');
                $query->where('vld.location_id', $location_id);
                $location_filter .= "AND transactions.location_id=$location_id";
            }
            $product_details =  $query->select(
                'products.name as product',
                'u.short_name as unit',
                'pv.name as product_variation',
                'v.name as variation',
                'v.sub_sku as sub_sku',
                'v.sell_price_inc_tax',
                DB::raw("SUM(vld.qty_available) as stock"),
                DB::raw("(SELECT SUM(IF(transactions.type='sell', TSL.quantity - TSL.quantity_returned, -1* TPL.quantity) ) FROM transactions 
                        LEFT JOIN transaction_sell_lines AS TSL ON transactions.id=TSL.transaction_id
                        LEFT JOIN purchase_lines AS TPL ON transactions.id=TPL.transaction_id
                        WHERE transactions.status='final' AND transactions.type='sell' $location_filter 
                        AND (TSL.variation_id=v.id OR TPL.variation_id=v.id)) as total_sold"),
                DB::raw("(SELECT SUM(IF(transactions.type='sell_transfer', TSL.quantity, 0) ) FROM transactions 
                        LEFT JOIN transaction_sell_lines AS TSL ON transactions.id=TSL.transaction_id
                        WHERE transactions.status='final' AND transactions.type='sell_transfer' $location_filter 
                        AND (TSL.variation_id=v.id)) as total_transfered"),
                DB::raw("(SELECT SUM(IF(transactions.type='stock_adjustment', SAL.quantity, 0) ) FROM transactions 
                        LEFT JOIN stock_adjustment_lines AS SAL ON transactions.id=SAL.transaction_id
                        WHERE transactions.status='received' AND transactions.type='stock_adjustment' $location_filter 
                        AND (SAL.variation_id=v.id)) as total_adjusted")
                // DB::raw("(SELECT SUM(quantity) FROM transaction_sell_lines LEFT JOIN transactions ON transaction_sell_lines.transaction_id=transactions.id WHERE transactions.status='final' $location_filter AND
                //     transaction_sell_lines.variation_id=v.id) as total_sold")
            )
                ->groupBy('v.id')
                ->get();
            return view('report.stock_details')
                ->with(compact('product_details'));
        }
    }
    /**
     * Shows tax report of a business
     *
     * @return \Illuminate\Http\Response
     */
    public function getTaxReport(Request $request)
    {
        if (!auth()->user()->can('tax_report.view')) {
            abort(403, 'Unauthorized action.');
        }
        $business_id = $request->session()->get('user.business_id');
        //Return the details in ajax call
        if ($request->ajax()) {
            $start_date = $request->get('start_date');
            $end_date = $request->get('end_date');
            $location_id = $request->get('location_id');
            $input_tax_details = $this->transactionUtil->getInputTax($business_id, $start_date, $end_date, $location_id);
            $input_tax = view('report.partials.tax_details')->with(['tax_details' => $input_tax_details])->render();
            $output_tax_details = $this->transactionUtil->getOutputTax($business_id, $start_date, $end_date, $location_id);
            $expense_tax_details = $this->transactionUtil->getExpenseTax($business_id, $start_date, $end_date, $location_id);
            $output_tax = view('report.partials.tax_details')->with(['tax_details' => $output_tax_details])->render();
            $expense_tax = view('report.partials.tax_details')->with(['tax_details' => $expense_tax_details])->render();
            return [
                'input_tax' => $input_tax,
                'output_tax' => $output_tax,
                'expense_tax' => $expense_tax,
                'tax_diff' => $output_tax_details['total_tax'] - $input_tax_details['total_tax'] - $expense_tax_details['total_tax']
            ];
        }
        $business_locations = BusinessLocation::forDropdown($business_id, true);
        return view('report.tax_report')
            ->with(compact('business_locations'));
    }
    /**
     * Shows trending products
     *
     * @return \Illuminate\Http\Response
     */
    public function getTrendingProducts(Request $request)
    {
        if (!auth()->user()->can('trending_products.view')) {
            abort(403, 'Unauthorized action.');
        }
        $business_id = $request->session()->get('user.business_id');
        $filters = request()->only(['category', 'sub_category', 'brand', 'unit', 'limit', 'location_id', 'product_type']);
        $date_range = request()->input('date_range');
        if (!empty($date_range)) {
            $date_range_array = explode('~', $date_range);
            $filters['start_date'] = $this->transactionUtil->uf_date(trim($date_range_array[0]));
            $filters['end_date'] = $this->transactionUtil->uf_date(trim($date_range_array[1]));
        }
        $products = $this->productUtil->getTrendingProducts($business_id, $filters);
        $values = [];
        $labels = [];
        foreach ($products as $product) {
            $values[] = (float) $product->total_unit_sold;
            $labels[] = $product->product . ' (' . $product->unit . ')';
        }
        $chart = new CommonChart;
        $chart->labels($labels)
            ->dataset(__('report.total_unit_sold'), 'column', $values);
        $categories = Category::forDropdown($business_id, 'product');
        $brands = Brands::where('business_id', $business_id)
            ->pluck('name', 'id');
        $units = Unit::where('business_id', $business_id)
            ->pluck('short_name', 'id');
        $business_locations = BusinessLocation::forDropdown($business_id, true);
        return view('report.trending_products')
            ->with(compact('chart', 'categories', 'brands', 'units', 'business_locations'));
    }
    /**
     * Shows expense report of a business
     *
     * @return \Illuminate\Http\Response
     */
    public function getExpenseReport(Request $request)
    {
        if (!auth()->user()->can('expense_report.view')) {
            abort(403, 'Unauthorized action.');
        }
        $business_id = $request->session()->get('user.business_id');
        $filters = $request->only(['category', 'location_id']);
        $date_range = $request->input('date_range');
        if (!empty($date_range)) {
            $date_range_array = explode('~', $date_range);
            $filters['start_date'] = $this->transactionUtil->uf_date(trim($date_range_array[0]));
            $filters['end_date'] = $this->transactionUtil->uf_date(trim($date_range_array[1]));
        } else {
            $filters['start_date'] = \Carbon::now()->startOfMonth()->format('Y-m-d');
            $filters['end_date'] = \Carbon::now()->endOfMonth()->format('Y-m-d');
        }
        $expenses = $this->transactionUtil->getExpenseReport($business_id, $filters);
        $values = [];
        $labels = [];
        foreach ($expenses as $expense) {
            $values[] = $expense->total_expense;
            $labels[] = !empty($expense->category) ? $expense->category : __('report.others');
        }
        $chart = Charts::create('bar', 'highcharts')
            ->title(__('report.expense_report'))
            ->dimensions(0, 400)
            ->template("material")
            ->values($values)
            ->labels($labels)
            ->elementLabel(__('report.total_expense'));
        $categories = ExpenseCategory::where('business_id', $business_id)
            ->pluck('name', 'id');
        $business_locations = BusinessLocation::forDropdown($business_id, true);
        return view('report.expense_report')
            ->with(compact('chart', 'categories', 'business_locations'));
    }
    /**
     * Shows stock adjustment report
     *
     * @return \Illuminate\Http\Response
     */
    public function getStockAdjustmentReport(Request $request)
    {
        if (!auth()->user()->can('stock_adjustment_report.view')) {
            abort(403, 'Unauthorized action.');
        }
        $business_id = $request->session()->get('user.business_id');
        //Return the details in ajax call
        if ($request->ajax()) {
            $query =  Transaction::where('business_id', $business_id)
                ->where('type', 'stock_adjustment');
            //Check for permitted locations of a user
            $permitted_locations = auth()->user()->permitted_locations();
            if ($permitted_locations != 'all') {
                $query->whereIn('location_id', $permitted_locations);
            }
            $start_date = $request->get('start_date');
            $end_date = $request->get('end_date');
            if (!empty($start_date) && !empty($end_date)) {
                $query->whereBetween(DB::raw('date(transaction_date)'), [$start_date, $end_date]);
            }
            $location_id = $request->get('location_id');
            if (!empty($location_id)) {
                $query->where('location_id', $location_id);
            }
            $stock_adjustment_details = $query->select(
                DB::raw("SUM(final_total) as total_amount"),
                DB::raw("SUM(total_amount_recovered) as total_recovered"),
                DB::raw("SUM(IF(adjustment_type = 'normal', final_total, 0)) as total_normal"),
                DB::raw("SUM(IF(adjustment_type = 'abnormal', final_total, 0)) as total_abnormal")
            )->first();
            return $stock_adjustment_details;
        }
        $business_locations = BusinessLocation::forDropdown($business_id, true);
        return view('report.stock_adjustment_report')
            ->with(compact('business_locations'));
    }
    /**
     * Shows register report of a business
     *
     * @return \Illuminate\Http\Response
     */
    public function getRegisterReport(Request $request)
    {
        if (!auth()->user()->can('register_report.view')) {
            abort(403, 'Unauthorized action.');
        }
        $business_id = $request->session()->get('user.business_id');
        //Return the details in ajax call
        if ($request->ajax()) {
            $registers = CashRegister::join(
                'users as u',
                'u.id',
                '=',
                'cash_registers.user_id'
            )
                ->leftJoin(
                    'business_locations as bl',
                    'bl.id',
                    '=',
                    'cash_registers.location_id'
                )
                ->where('cash_registers.business_id', $business_id)
                ->select(
                    'cash_registers.*',
                    DB::raw(
                        "CONCAT(COALESCE(surname, ''), ' ', COALESCE(first_name, ''), ' ', COALESCE(last_name, ''), '<br>', COALESCE(u.email, '')) as user_name"
                    ),
                    'bl.name as location_name'
                );
            if (!empty($request->input('user_id'))) {
                $registers->where('cash_registers.user_id', $request->input('user_id'));
            }
            if (!empty($request->input('status'))) {
                $registers->where('cash_registers.status', $request->input('status'));
            }
            if (!empty($request->input('start_date')) && !empty($request->input('end_date'))) {
                $registers->whereDate('cash_registers.closed_at', '>=', $request->input('start_date'));
                $registers->whereDate('cash_registers.closed_at', '<=', $request->input('end_date'));
            }
            return Datatables::of($registers)
                ->editColumn('total_card_slips', function ($row) {
                    if ($row->status == 'close') {
                        return $row->total_card_slips;
                    } else {
                        return '';
                    }
                })
                ->editColumn('total_cheques', function ($row) {
                    if ($row->status == 'close') {
                        return $row->total_cheques;
                    } else {
                        return '';
                    }
                })
                ->editColumn('closed_at', function ($row) {
                    if ($row->status == 'close') {
                        return $this->productUtil->format_date($row->closed_at, true);
                    } else {
                        return '';
                    }
                })
                ->editColumn('created_at', function ($row) {
                    return $this->productUtil->format_date($row->created_at, true);
                })
                ->editColumn('total_credit_sale', function ($row) {
                    if ($row->status == 'close') {
                        $register_details =  $this->cashRegisterUtil->getRegisterDetails($row->id);
                        $user_id = $register_details->user_id;
                        $open_time = $register_details['open_time'];
                        $close_time = \Carbon::now()->toDateTimeString();
                        $details = $this->cashRegisterUtil->getRegisterTransactionDetails($user_id, $open_time, $close_time);
                        //$total_credit_sale = $details['transaction_details']->total_sales - $register_details->total_sale;
                        $total_credit_sale = $row->closing_amount;
                        return '<span class="display_currency" data-currency_symbol="true">' . $total_credit_sale . '</span>';
                    } else {
                        return '';
                    }
                })
                ->editColumn('closing_amount', function ($row) {
                    if ($row->status == 'close') {
                        return '<span class="display_currency" data-currency_symbol="true">' .
                            $row->closing_amount . '</span>';
                    } else {
                        return '';
                    }
                })
                ->addColumn('action', '<button type="button" data-href="{{action(\'CashRegisterController@show\', [$id])}}" class="btn btn-xs btn-info btn-modal" 
                    data-container=".view_register"><i class="fa fa-external-link" aria-hidden="true"></i> @lang("messages.view")</button>')
                ->filterColumn('user_name', function ($query, $keyword) {
                    $query->whereRaw("CONCAT(COALESCE(surname, ''), ' ', COALESCE(first_name, ''), ' ', COALESCE(last_name, ''), '<br>', COALESCE(u.email, '')) like ?", ["%{$keyword}%"]);
                })
                ->rawColumns(['action', 'user_name', 'closing_amount', 'total_credit_sale'])
                ->make(true);
        }
        $users = User::forDropdown($business_id, false);
        return view('report.register_report')
            ->with(compact('users'));
    }
    /**
     * Shows sales representative report
     *
     * @return \Illuminate\Http\Response
     */
    public function getSalesRepresentativeReport(Request $request)
    {
        if (!auth()->user()->can('sales_representative.view')) {
            abort(403, 'Unauthorized action.');
        }
        $business_id = $request->session()->get('user.business_id');
        $sales_cmsn_agnt = $request->session()->get('business.sales_cmsn_agnt');
        $all_users = User::where('business_id', $business_id)
            ->select('id', DB::raw("CONCAT(COALESCE(surname, ''),' ',COALESCE(first_name, ''),' ',COALESCE(last_name,'')) as full_name"));
        $agent_placeholder = '';
        if ($sales_cmsn_agnt == 'cmsn_agnt') {
            $all_users->where('is_cmmsn_agnt', 1);
            $agent_placeholder = 'Sale Commission Agents';
        }
        if ($sales_cmsn_agnt == 'logged_in_user') {
            $all_users->where('id', Auth::user()->id);
            $agent_placeholder = 'Logged in user';
        }
        if ($sales_cmsn_agnt == 'user') {
            $agent_placeholder = 'All Users';
        }
        $users = $all_users->pluck('full_name', 'id');
        $business_locations = BusinessLocation::forDropdown($business_id, true);
        return view('report.sales_representative')
            ->with(compact('users', 'business_locations', 'agent_placeholder'));
    }
    /**
     * Shows sales representative total expense
     *
     * @return json
     */
    public function getSalesRepresentativeTotalExpense(Request $request)
    {
        if (!auth()->user()->can('sales_representative.view')) {
            abort(403, 'Unauthorized action.');
        }
        if ($request->ajax()) {
            $business_id = $request->session()->get('user.business_id');
            $filters = $request->only(['expense_for', 'location_id', 'start_date', 'end_date']);
            $total_expense = $this->transactionUtil->getExpenseReport($business_id, $filters, 'total');
            return $total_expense;
        }
    }
    /**
     * Shows sales representative total sales
     *
     * @return json
     */
    public function getSalesRepresentativeTotalSell(Request $request)
    {
        if (!auth()->user()->can('sales_representative.view')) {
            abort(403, 'Unauthorized action.');
        }
        $business_id = $request->session()->get('user.business_id');
        //Return the details in ajax call
        if ($request->ajax()) {
            $start_date = $request->get('start_date');
            $end_date = $request->get('end_date');
            $location_id = $request->get('location_id');
            $created_by = $request->get('created_by');
            $sell_details = $this->transactionUtil->getSellTotals($business_id, $start_date, $end_date, $location_id, $created_by);
            //Get Sell Return details
            $transaction_types = [
                'sell_return'
            ];
            $sell_return_details = $this->transactionUtil->getTransactionTotals(
                $business_id,
                $transaction_types,
                $start_date,
                $end_date,
                $location_id,
                $created_by
            );
            $total_sell_return = !empty($sell_return_details['total_sell_return_exc_tax']) ? $sell_return_details['total_sell_return_exc_tax'] : 0;
            $total_sell = $sell_details['total_sell_exc_tax'] - $total_sell_return;
            return [
                'total_sell_exc_tax' => $sell_details['total_sell_exc_tax'],
                'total_sell_return_exc_tax' => $total_sell_return,
                'total_sell' => $total_sell
            ];
        }
    }
    /**
     * Shows sales representative total commission
     *
     * @return json
     */
    public function getSalesRepresentativeTotalCommission(Request $request)
    {
        if (!auth()->user()->can('sales_representative.view')) {
            abort(403, 'Unauthorized action.');
        }
        $business_id = $request->session()->get('user.business_id');
        //Return the details in ajax call
        if ($request->ajax()) {
            $start_date = $request->get('start_date');
            $end_date = $request->get('end_date');
            $location_id = $request->get('location_id');
            $commission_agent = $request->get('commission_agent');
            $sell_details = $this->transactionUtil->getTotalSellCommission($business_id, $start_date, $end_date, $location_id, $commission_agent);
            //Get Commision
            $commission_percentage = User::find($commission_agent)->cmmsn_percent;
            $total_commission = $commission_percentage * $sell_details['total_sales_with_commission'] / 100;
            return [
                'total_sales_with_commission' =>
                $sell_details['total_sales_with_commission'],
                'total_commission' => $total_commission,
                'commission_percentage' => $commission_percentage
            ];
        }
    }
    /**
     * Shows product stock expiry report
     *
     * @return \Illuminate\Http\Response
     */
    public function getStockExpiryReport(Request $request)
    {
        if (!auth()->user()->can('stock_report.view')) {
            abort(403, 'Unauthorized action.');
        }
        $business_id = $request->session()->get('user.business_id');
        //TODO:: Need to display reference number and edit expiry date button
        //Return the details in ajax call
        if ($request->ajax()) {
            $query = PurchaseLine::leftjoin(
                'transactions as t',
                'purchase_lines.transaction_id',
                '=',
                't.id'
            )
                ->leftjoin(
                    'products as p',
                    'purchase_lines.product_id',
                    '=',
                    'p.id'
                )
                ->leftjoin(
                    'variations as v',
                    'purchase_lines.variation_id',
                    '=',
                    'v.id'
                )
                ->leftjoin(
                    'product_variations as pv',
                    'v.product_variation_id',
                    '=',
                    'pv.id'
                )
                ->leftjoin('business_locations as l', 't.location_id', '=', 'l.id')
                ->leftjoin('units as u', 'p.unit_id', '=', 'u.id')
                ->where('t.business_id', $business_id)
                //->whereNotNull('p.expiry_period')
                //->whereNotNull('p.expiry_period_type')
                //->whereNotNull('exp_date')
                ->where('p.enable_stock', 1);
            // ->whereRaw('purchase_lines.quantity > purchase_lines.quantity_sold + quantity_adjusted + quantity_returned');
            $permitted_locations = auth()->user()->permitted_locations();
            if ($permitted_locations != 'all') {
                $query->whereIn('t.location_id', $permitted_locations);
            }
            if (!empty($request->input('location_id'))) {
                $location_id = $request->input('location_id');
                $query->where('t.location_id', $location_id)
                    //If filter by location then hide products not available in that location
                    ->join('product_locations as pl', 'pl.product_id', '=', 'p.id')
                    ->where(function ($q) use ($location_id) {
                        $q->where('pl.location_id', $location_id);
                    });
            }
            if (!empty($request->input('category_id'))) {
                $query->where('p.category_id', $request->input('category_id'));
            }
            if (!empty($request->input('sub_category_id'))) {
                $query->where('p.sub_category_id', $request->input('sub_category_id'));
            }
            if (!empty($request->input('brand_id'))) {
                $query->where('p.brand_id', $request->input('brand_id'));
            }
            if (!empty($request->input('unit_id'))) {
                $query->where('p.unit_id', $request->input('unit_id'));
            }
            if (!empty($request->input('exp_date_filter'))) {
                $query->whereDate('exp_date', '<=', $request->input('exp_date_filter'));
            }
            $only_mfg_products = request()->get('only_mfg_products', 0);
            if (!empty($only_mfg_products)) {
                $query->where('t.type', 'production_purchase');
            }
            $report = $query->select(
                'p.name as product',
                'p.sku',
                'p.type as product_type',
                'v.name as variation',
                'pv.name as product_variation',
                'l.name as location',
                'mfg_date',
                'exp_date',
                'u.short_name as unit',
                DB::raw("SUM(COALESCE(quantity, 0) - COALESCE(quantity_sold, 0) - COALESCE(quantity_adjusted, 0) - COALESCE(quantity_returned, 0)) as stock_left"),
                't.ref_no',
                't.id as transaction_id',
                'purchase_lines.id as purchase_line_id',
                'purchase_lines.lot_number'
            )
                ->groupBy('purchase_lines.exp_date')
                ->groupBy('purchase_lines.lot_number');
            return Datatables::of($report)
                ->editColumn('name', function ($row) {
                    if ($row->product_type == 'variable') {
                        return $row->product . ' - ' .
                            $row->product_variation . ' - ' . $row->variation;
                    } else {
                        return $row->product;
                    }
                })
                ->editColumn('mfg_date', function ($row) {
                    if (!empty($row->mfg_date)) {
                        return $this->productUtil->format_date($row->mfg_date);
                    } else {
                        return '--';
                    }
                })
                ->editColumn('ref_no', function ($row) {
                    return '<button type="button" data-href="' . action('PurchaseController@show', [$row->transaction_id])
                        . '" class="btn btn-link btn-modal" data-container=".view_modal"  >' . $row->ref_no . '</button>';
                })
                ->editColumn('stock_left', function ($row) {
                    return '<span data-is_quantity="true" class="display_currency stock_left" data-currency_symbol=false data-orig-value="' . $row->stock_left . '" data-unit="' . $row->unit . '" >' . $row->stock_left . '</span> ' . $row->unit;
                })
                ->addColumn('edit', function ($row) {
                    $html =  '<button type="button" class="btn btn-primary btn-xs stock_expiry_edit_btn" data-transaction_id="' . $row->transaction_id . '" data-purchase_line_id="' . $row->purchase_line_id . '"> <i class="fa fa-edit"></i> ' . __("messages.edit") .
                        '</button>';
                    if (!empty($row->exp_date)) {
                        $carbon_exp = \Carbon::createFromFormat('Y-m-d', $row->exp_date);
                        $carbon_now = \Carbon::now();
                        if ($carbon_now->diffInDays($carbon_exp, false) < 0) {
                            $html .=  ' <button type="button" class="btn btn-warning btn-xs remove_from_stock_btn" data-href="' . action('StockAdjustmentController@removeExpiredStock', [$row->purchase_line_id]) . '"> <i class="fa fa-trash"></i> ' . __("lang_v1.remove_from_stock") .
                                '</button>';
                        }
                    }
                    return $html;
                })
                ->rawColumns(['exp_date', 'ref_no', 'edit', 'stock_left'])
                ->make(true);
        }
        $categories = Category::where('business_id', $business_id)
            ->where('parent_id', 0)
            ->pluck('name', 'id');
        $brands = Brands::where('business_id', $business_id)
            ->pluck('name', 'id');
        $units = Unit::where('business_id', $business_id)
            ->pluck('short_name', 'id');
        $business_locations = BusinessLocation::forDropdown($business_id, true);
        $view_stock_filter = [
            \Carbon::now()->subDay()->format('Y-m-d') => __('report.expired'),
            \Carbon::now()->addWeek()->format('Y-m-d') => __('report.expiring_in_1_week'),
            \Carbon::now()->addDays(15)->format('Y-m-d') => __('report.expiring_in_15_days'),
            \Carbon::now()->addMonth()->format('Y-m-d') => __('report.expiring_in_1_month'),
            \Carbon::now()->addMonths(3)->format('Y-m-d') => __('report.expiring_in_3_months'),
            \Carbon::now()->addMonths(6)->format('Y-m-d') => __('report.expiring_in_6_months'),
            \Carbon::now()->addYear()->format('Y-m-d') => __('report.expiring_in_1_year')
        ];
        return view('report.stock_expiry_report')
            ->with(compact('categories', 'brands', 'units', 'business_locations', 'view_stock_filter'));
    }
    /**
     * Shows product stock expiry report
     *
     * @return \Illuminate\Http\Response
     */
    public function getStockExpiryReportEditModal(Request $request, $purchase_line_id)
    {
        if (!auth()->user()->can('stock_report.view')) {
            abort(403, 'Unauthorized action.');
        }
        $business_id = $request->session()->get('user.business_id');
        //Return the details in ajax call
        if ($request->ajax()) {
            $purchase_line = PurchaseLine::join(
                'transactions as t',
                'purchase_lines.transaction_id',
                '=',
                't.id'
            )
                ->join(
                    'products as p',
                    'purchase_lines.product_id',
                    '=',
                    'p.id'
                )
                ->where('purchase_lines.id', $purchase_line_id)
                ->where('t.business_id', $business_id)
                ->select(['purchase_lines.*', 'p.name', 't.ref_no'])
                ->first();
            if (!empty($purchase_line)) {
                if (!empty($purchase_line->exp_date)) {
                    $purchase_line->exp_date = date('m/d/Y', strtotime($purchase_line->exp_date));
                }
            }
            return view('report.partials.stock_expiry_edit_modal')
                ->with(compact('purchase_line'));
        }
    }
    /**
     * Update product stock expiry report
     *
     * @return \Illuminate\Http\Response
     */
    public function updateStockExpiryReport(Request $request)
    {
        if (!auth()->user()->can('stock_report.view')) {
            abort(403, 'Unauthorized action.');
        }
        try {
            $business_id = $request->session()->get('user.business_id');
            //Return the details in ajax call
            if ($request->ajax()) {
                DB::beginTransaction();
                $input = $request->only(['purchase_line_id', 'exp_date']);
                $purchase_line = PurchaseLine::join(
                    'transactions as t',
                    'purchase_lines.transaction_id',
                    '=',
                    't.id'
                )
                    ->join(
                        'products as p',
                        'purchase_lines.product_id',
                        '=',
                        'p.id'
                    )
                    ->where('purchase_lines.id', $input['purchase_line_id'])
                    ->where('t.business_id', $business_id)
                    ->select(['purchase_lines.*', 'p.name', 't.ref_no'])
                    ->first();
                if (!empty($purchase_line) && !empty($input['exp_date'])) {
                    $purchase_line->exp_date = $this->productUtil->uf_date($input['exp_date']);
                    $purchase_line->save();
                }
                DB::commit();
                $output = [
                    'success' => 1,
                    'msg' => __('lang_v1.updated_succesfully')
                ];
            }
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());
            $output = [
                'success' => 0,
                'msg' => __('messages.something_went_wrong')
            ];
        }
        return $output;
    }
    /**
     * Shows product stock expiry report
     *
     * @return \Illuminate\Http\Response
     */
    public function getCustomerGroup(Request $request)
    {
        if (!auth()->user()->can('contacts_report.view')) {
            abort(403, 'Unauthorized action.');
        }
        $business_id = $request->session()->get('user.business_id');
        if ($request->ajax()) {
            $query = Transaction::leftjoin('contact_groups AS CG', 'transactions.customer_group_id', '=', 'CG.id')
                ->where('transactions.business_id', $business_id)
                ->where('transactions.type', 'sell')
                ->where('transactions.status', 'final')
                ->where('CG.type', 'customer')
                ->groupBy('transactions.customer_group_id')
                ->select(DB::raw("SUM(final_total) as total_sell"), 'CG.name');
            $group_id = $request->get('customer_group_id', null);
            if (!empty($group_id)) {
                $query->where('transactions.customer_group_id', $group_id);
            }
            $permitted_locations = auth()->user()->permitted_locations();
            if ($permitted_locations != 'all') {
                $query->whereIn('transactions.location_id', $permitted_locations);
            }
            $location_id = $request->get('location_id', null);
            if (!empty($location_id)) {
                $query->where('transactions.location_id', $location_id);
            }
            $start_date = $request->get('start_date');
            $end_date = $request->get('end_date');
            if (!empty($start_date) && !empty($end_date)) {
                $query->whereBetween(DB::raw('date(transaction_date)'), [$start_date, $end_date]);
            }
            return Datatables::of($query)
                ->editColumn('total_sell', function ($row) {
                    return '<span class="display_currency" data-currency_symbol = true>' . $row->total_sell . '</span>';
                })
                ->rawColumns(['total_sell'])
                ->make(true);
        }
        $customer_group = ContactGroup::forDropdown($business_id, false, true);
        $business_locations = BusinessLocation::forDropdown($business_id, true);
        return view('report.customer_group')
            ->with(compact('customer_group', 'business_locations'));
    }
    /**
     * Shows product purchase report
     *
     * @return \Illuminate\Http\Response
     */
    public function getproductPurchaseReport(Request $request)
    {
        if (!auth()->user()->can('product_purchase_report.view')) {
            abort(403, 'Unauthorized action.');
        }
        $business_id = $request->session()->get('user.business_id');
        $business_details = Business::find($business_id);
        if ($request->ajax()) {
            $variation_id = $request->get('variation_id', null);
            $query = PurchaseLine::join(
                'transactions as t',
                'purchase_lines.transaction_id',
                '=',
                't.id'
            )
                ->join(
                    'variations as v',
                    'purchase_lines.variation_id',
                    '=',
                    'v.id'
                )
                ->join('product_variations as pv', 'v.product_variation_id', '=', 'pv.id')
                ->join('contacts as c', 't.contact_id', '=', 'c.id')
                ->join('products as p', 'pv.product_id', '=', 'p.id')
                ->leftjoin('units as u', 'p.unit_id', '=', 'u.id')
                ->where('t.business_id', $business_id)
                ->where('t.type', 'purchase')
                ->select(
                    'p.name as product_name',
                    'p.type as product_type',
                    'p.id as product_id',
                    'pv.name as product_variation',
                    'v.name as variation_name',
                    'c.name as supplier',
                    't.id as transaction_id',
                    't.ref_no',
                    't.invoice_no',
                    't.transaction_date as transaction_date',
                    'purchase_lines.purchase_price_inc_tax as unit_purchase_price',
                    DB::raw('(purchase_lines.quantity - purchase_lines.quantity_returned) as purchase_qty'),
                    'purchase_lines.quantity_adjusted',
                    'u.short_name as unit',
                    DB::raw('((purchase_lines.quantity - purchase_lines.quantity_returned - purchase_lines.quantity_adjusted) * purchase_lines.purchase_price_inc_tax) as subtotal')
                )
                ->groupBy('purchase_lines.id');
            if (!empty($variation_id)) {
                $query->where('purchase_lines.variation_id', $variation_id);
            }
            $start_date = $request->get('start_date');
            $end_date = $request->get('end_date');
            if (!empty($start_date) && !empty($end_date)) {
                $query->whereBetween(DB::raw('date(transaction_date)'), [$start_date, $end_date]);
            }
            $permitted_locations = auth()->user()->permitted_locations();
            if ($permitted_locations != 'all') {
                $query->whereIn('t.location_id', $permitted_locations);
            }
            $location_id = $request->get('location_id', null);
            if (!empty($location_id)) {
                $query->where('t.location_id', $location_id);
            }
            $category_id = $request->get('category_id', null);
            if (!empty($category_id)) {
                $query->where('p.category_id', $category_id);
            }
            $sub_category_id = $request->get('sub_category_id', null);
            if (!empty($sub_category_id)) {
                $query->where('p.sub_category_id', $sub_category_id);
            }
            $supplier_id = $request->get('supplier_id', null);
            if (!empty($supplier_id)) {
                $query->where('t.contact_id', $supplier_id);
            }
            return Datatables::of($query)
                ->editColumn('product_name', function ($row) {
                    $product_name = $row->product_name;
                    if ($row->product_type == 'variable') {
                        $product_name .= ' - ' . $row->product_variation . ' - ' . $row->variation_name;
                    }
                    return $product_name;
                })
                ->addColumn('units', function ($row) use ($business_id) {
                    return $this->productUtil->getProductUnitsDropdown($row->product_id);
                })
                ->editColumn('ref_no', function ($row) {
                    return '<a data-href="' . action('PurchaseController@show', [$row->transaction_id])
                        . '" href="#" data-container=".view_modal" class="btn-modal">' . $row->ref_no . '</a>';
                })
                ->editColumn('purchase_qty', function ($row) use ($business_details) {
                    return '<span data-is_quantity="true" class="display_currency purchase_qty" data-currency_symbol=false data-orig-value="' . (float) $row->purchase_qty . '" data-unit="' . $row->unit . '" >' . $this->productUtil->num_f($row->purchase_qty, false, $business_details, true) . '</span> <span class="unit_name">' . $row->unit . '</span>';
                })
                ->editColumn('quantity_adjusted', function ($row) use ($business_details) {
                    return '<span data-is_quantity="true" class="display_currency quantity_adjusted" data-currency_symbol=false data-orig-value="' . (float) $row->quantity_adjusted . '" data-unit="' . $row->unit . '" >' . $this->productUtil->num_f($row->quantity_adjusted, false, $business_details, true) . '</span> <span class="unit_name">' . $row->unit . '</span>';
                })
                ->editColumn('subtotal', function ($row) use ($business_details) {
                    return '<span class="display_currency row_subtotal" data-currency_symbol=true data-orig-value="' . $row->subtotal . '">' . $this->productUtil->num_f($row->subtotal, false, $business_details, false) . '</span>';
                })
                ->editColumn('transaction_date', '{{@format_date($transaction_date)}}')
                ->editColumn('unit_purchase_price', function ($row) use ($business_details) {
                    return '<span class="display_currency purchase_price" data-currency_symbol = true data-orig-value="' . $row->unit_purchase_price . '">' . $this->productUtil->num_f($row->unit_purchase_price, false, $business_details, false) . '</span>';
                })
                ->rawColumns(['ref_no', 'unit_purchase_price', 'subtotal', 'purchase_qty', 'quantity_adjusted', 'units'])
                ->make(true);
        }
        $business_locations = BusinessLocation::forDropdown($business_id);
        $suppliers = Contact::suppliersDropdown($business_id);
        return view('report.product_purchase_report')
            ->with(compact('business_locations', 'suppliers'));
    }
    /**
     * Shows product purchase report
     *
     * @return \Illuminate\Http\Response
     */
    public function getproductPurchaseReportSummary(Request $request)
    {
        if (!auth()->user()->can('purchase_n_sell_report.view')) {
            abort(403, 'Unauthorized action.');
        }
        $business_id = $request->session()->get('user.business_id');
        if ($request->ajax()) {
            $variation_id = $request->get('variation_id', null);
            $query = PurchaseLine::join(
                'transactions as t',
                'purchase_lines.transaction_id',
                '=',
                't.id'
            )
                ->join(
                    'variations as v',
                    'purchase_lines.variation_id',
                    '=',
                    'v.id'
                )
                ->join('product_variations as pv', 'v.product_variation_id', '=', 'pv.id')
                ->join('contacts as c', 't.contact_id', '=', 'c.id')
                ->join('products as p', 'pv.product_id', '=', 'p.id')
                ->leftjoin('units as u', 'p.unit_id', '=', 'u.id')
                ->where('t.business_id', $business_id)
                ->where('t.type', 'purchase')
                ->select(
                    DB::raw('SUM(purchase_lines.quantity) as purchase_qty'),
                    DB::raw('SUM((purchase_lines.quantity - purchase_lines.quantity_returned) * purchase_lines.purchase_price_inc_tax) as purchase_qty_value'),
                    DB::raw('SUM(purchase_lines.quantity_adjusted) as adjusted_qty'),
                    DB::raw('SUM(purchase_lines.quantity_adjusted * purchase_lines.purchase_price_inc_tax) as adjusted_qty_value'),
                );
            if (!empty($variation_id)) {
                $query->where('purchase_lines.variation_id', $variation_id);
            }
            $start_date = Carbon::parse($request->get('start_date'))->format('Y-m-d');
            $end_date =  Carbon::parse($request->get('end_date'))->format('Y-m-d');
            if (!empty($start_date) && !empty($end_date)) {
                $query->whereDate('t.transaction_date', '>=', $start_date)
                    ->whereDate('t.transaction_date', '<=', $end_date);
            }
            $permitted_locations = auth()->user()->permitted_locations();
            if ($permitted_locations != 'all') {
                $query->whereIn('t.location_id', $permitted_locations);
            }
            $location_id = $request->get('location_id', null);
            if (!empty($location_id)) {
                $query->where('t.location_id', $location_id);
            }
            $category_id = $request->get('category_id', null);
            if (!empty($category_id)) {
                $query->where('p.category_id', $category_id);
            }
            $sub_category_id = $request->get('sub_category_id', null);
            if (!empty($sub_category_id)) {
                $query->where('p.sub_category_id', $sub_category_id);
            }
            $supplier_id = $request->get('supplier_id', null);
            if (!empty($supplier_id)) {
                $query->where('t.contact_id', $supplier_id);
            }
            $purchase = $query->first();
            $data = array(
                'purchase_qty' => $this->productUtil->num_f($purchase->purchase_qty, false, null, true),
                'purchase_qty_value' => $this->productUtil->num_f($purchase->purchase_qty_value, false, null, false),
                'adjusted_qty' => $this->productUtil->num_f($purchase->adjusted_qty, false, null, true),
                'adjusted_qty_value' => $this->productUtil->num_f($purchase->adjusted_qty_value, false, null, false)
            );
            return $data;
        }
    }
    /**
     * Shows product purchase report
     *
     * @return \Illuminate\Http\Response
     */
    public function getproductSellReport(Request $request)
    {
        if (!auth()->user()->can('product_sell_report.view')) {
            abort(403, 'Unauthorized action.');
        }
        $business_id = $request->session()->get('user.business_id');
        $business_details = Business::find($business_id);
        if ($request->ajax()) {
            $variation_id = $request->get('variation_id', null);
            $query = TransactionSellLine::leftjoin(
                'transactions as t',
                'transaction_sell_lines.transaction_id',
                '=',
                't.id'
            )
                ->leftjoin(
                    'variations as v',
                    'transaction_sell_lines.variation_id',
                    '=',
                    'v.id'
                )
                ->leftjoin('product_variations as pv', 'v.product_variation_id', '=', 'pv.id')
                ->leftjoin('contacts as c', 't.contact_id', '=', 'c.id')
                ->leftjoin('products as p', 'pv.product_id', '=', 'p.id')
                ->leftjoin('tax_rates', 'transaction_sell_lines.tax_id', '=', 'tax_rates.id')
                ->leftjoin('units as u', 'p.unit_id', '=', 'u.id')
                ->where('t.business_id', $business_id)
                ->where('t.type', 'sell')
                ->where('t.status', 'final')
                ->where(function ($q) {
                    $q->where('t.sub_type', '!=', 'credit_sale')->orWhereNull('t.sub_type');
                })
                ->select(
                    'p.name as product_name',
                    'p.type as product_type',
                    'pv.name as product_variation',
                    'v.name as variation_name',
                    'v.sub_sku',
                    'c.name as customer',
                    'c.contact_id',
                    'p.id as product_id',
                    't.id as transaction_id',
                    't.invoice_no',
                    't.transaction_date as transaction_date',
                    'transaction_sell_lines.unit_price_before_discount as unit_price',
                    'transaction_sell_lines.unit_price_inc_tax as unit_sale_price',
                    DB::raw('SUM(transaction_sell_lines.quantity - transaction_sell_lines.quantity_returned) as sell_qty'),
                    'transaction_sell_lines.line_discount_type as discount_type',
                    'transaction_sell_lines.line_discount_amount as discount_amount',
                    'transaction_sell_lines.item_tax',
                    'tax_rates.name as tax',
                    'u.short_name as unit',
                    DB::raw('SUM((transaction_sell_lines.quantity - transaction_sell_lines.quantity_returned) * transaction_sell_lines.unit_price_inc_tax) as subtotal')
                )
                ->groupBy(['t.id', 'transaction_sell_lines.variation_id']);   // 1005. POS Issues – doc
            if (!empty($variation_id)) {
                $query->where('transaction_sell_lines.variation_id', $variation_id);
            }
            $start_date = $request->get('start_date');
            $end_date = $request->get('end_date');
            if (!empty($start_date) && !empty($end_date)) {
                $query->whereBetween(DB::raw('date(transaction_date)'), [$start_date, $end_date]);
            }
            $permitted_locations = auth()->user()->permitted_locations();
            if ($permitted_locations != 'all') {
                $query->whereIn('t.location_id', $permitted_locations);
            }
            $location_id = $request->get('location_id', null);
            if (!empty($location_id)) {
                $query->where('t.location_id', $location_id);
            }
            $category_id = $request->get('category_id', null);
            if (!empty($category_id)) {
                $query->where('p.category_id', $category_id);
            }
            $sub_category_id = $request->get('sub_category_id', null);
            if (!empty($sub_category_id)) {
                $query->where('p.sub_category_id', $sub_category_id);
            }
            $customer_id = $request->get('customer_id', null);
            if (!empty($customer_id)) {
                $query->where('t.contact_id', $customer_id);
            }
            return Datatables::of($query)
                ->editColumn('product_name', function ($row) {
                    $product_name = $row->product_name;
                    if ($row->product_type == 'variable') {
                        $product_name .= ' - ' . $row->product_variation . ' - ' . $row->variation_name;
                    }
                    return $product_name;
                })
                ->addColumn('units', function ($row) use ($business_id) {
                    return $this->productUtil->getProductUnitsDropdown($row->product_id);
                })
                ->editColumn('invoice_no', function ($row) {
                    return '<a data-href="' . action('SellController@show', [$row->transaction_id])
                        . '" href="#" data-container=".view_modal" class="btn-modal">' . $row->invoice_no . '</a>';
                })
                ->editColumn('transaction_date', '{{@format_date($transaction_date)}}')
                ->editColumn('unit_sale_price', function ($row) use ($business_details) {
                    return '<span class="display_currency selling_price" data-currency_symbol = true data-orig-value="' . $row->unit_sale_price . '">' . $this->productUtil->num_f($row->unit_sale_price, false, $business_details, false) . '</span>';
                })
                ->editColumn('sell_qty', function ($row) use ($business_details) {
                    return '<span data-is_quantity="true" class="display_currency sell_qty" data-currency_symbol=false data-orig-value="' . (float) $row->sell_qty . '" data-unit="' . $row->unit . '" >' .  $this->productUtil->num_f($row->sell_qty, false, $business_details, false) . '</span> <span class="unit_name">' . $row->unit . '</span>';
                })
                ->editColumn('subtotal', function ($row) use ($business_details) {
                    return '<span class="display_currency row_subtotal" data-currency_symbol = true data-orig-value="' . $row->subtotal . '">' . $this->productUtil->num_f($row->subtotal, false, $business_details, false) . '</span>';
                })
                ->editColumn('unit_price', function ($row) use ($business_details) {
                    return '<span class="display_currency selling_price" data-currency_symbol = true data-orig-value="' . $row->unit_price . '">' . $this->productUtil->num_f($row->unit_price, false, $business_details, false) . '</span>';
                })
                ->editColumn('discount_amount', '
                    @if($discount_type == "percentage")
                        {{@number_format($discount_amount)}} %
                    @elseif($discount_type == "fixed")
                        {{@number_format($discount_amount)}}
                    @endif
                    ')
                ->editColumn('tax', function ($row) use ($business_details) {
                    return '<span class="display_currency" data-currency_symbol = true>' .
                        $this->productUtil->num_f($row->item_tax, false, $business_details, false)  .
                        '</span>' . '<br>' . '<span class="tax" data-orig-value="' . (float) $row->item_tax . '" data-unit="' . $row->tax . '"><small>(' . $row->tax . ')</small></span>';
                })
                ->rawColumns(['invoice_no', 'unit_sale_price', 'subtotal', 'sell_qty', 'discount_amount', 'unit_price', 'tax', 'units'])
                ->make(true);
        }
        $business_locations = BusinessLocation::forDropdown($business_id);
        $customers = Contact::customersDropdown($business_id);
        return view('report.product_sell_report')
            ->with(compact('business_locations', 'customers'));
    }
    /**
     * Shows product purchase report
     *
     * @return \Illuminate\Http\Response
     */
    public function getproductSellReportSummary(Request $request)
    {
        if (!auth()->user()->can('purchase_n_sell_report.view')) {
            abort(403, 'Unauthorized action.');
        }
        $business_id = $request->session()->get('user.business_id');
        if ($request->ajax()) {
            $variation_id = $request->get('variation_id', null);
            $query = TransactionSellLine::join(
                'transactions as t',
                'transaction_sell_lines.transaction_id',
                '=',
                't.id'
            )
                ->join(
                    'variations as v',
                    'transaction_sell_lines.variation_id',
                    '=',
                    'v.id'
                )
                ->join('product_variations as pv', 'v.product_variation_id', '=', 'pv.id')
                ->join('contacts as c', 't.contact_id', '=', 'c.id')
                ->join('products as p', 'pv.product_id', '=', 'p.id')
                ->where('t.business_id', $business_id)
                ->where('t.type', 'sell')
                ->where('t.status', 'final')
                ->select(
                    DB::raw('SUM(transaction_sell_lines.quantity) as sell_qty'),
                    DB::raw('SUM(transaction_sell_lines.quantity * transaction_sell_lines.unit_price_inc_tax) as sell_qty_value')
                );
            if (!empty($variation_id)) {
                $query->where('transaction_sell_lines.variation_id', $variation_id);
            }
            $start_date = Carbon::parse($request->get('start_date'))->format('Y-m-d');
            $end_date =  Carbon::parse($request->get('end_date'))->format('Y-m-d');
            if (!empty($start_date) && !empty($end_date)) {
                $query->whereDate('t.transaction_date', '>=', $start_date)
                    ->whereDate('t.transaction_date', '<=', $end_date);
            }
            $permitted_locations = auth()->user()->permitted_locations();
            if ($permitted_locations != 'all') {
                $query->whereIn('t.location_id', $permitted_locations);
            }
            $location_id = $request->get('location_id', null);
            if (!empty($location_id)) {
                $query->where('t.location_id', $location_id);
            }
            $category_id = $request->get('category_id', null);
            if (!empty($category_id)) {
                $query->where('p.category_id', $category_id);
            }
            $sub_category_id = $request->get('sub_category_id', null);
            if (!empty($sub_category_id)) {
                $query->where('p.sub_category_id', $sub_category_id);
            }
            $customer_id = $request->get('customer_id', null);
            if (!empty($customer_id)) {
                $query->where('t.contact_id', $customer_id);
            }
            $sell = $query->first();
            $data = array(
                'sell_qty' => $this->productUtil->num_f($sell->sell_qty, false, null, true),
                'sell_qty_value' => $this->productUtil->num_f($sell->sell_qty_value, false, null, false)
            );
            return $data;
        }
    }
    /**
     * Shows product purchase report
     *
     * @return \Illuminate\Http\Response
     */
    public function getproductSalesReportDuplicate(Request $request)
    {
        if (!auth()->user()->can('purchase_n_sell_report.view')) {
            abort(403, 'Unauthorized action.');
        }
        $business_id = $request->session()->get('user.business_id');
        $business_details = Business::find($business_id);
        if ($request->ajax()) {
            $variation_id = $request->get('variation_id', null);
            $query = TransactionSellLine::join(
                'transactions as t',
                'transaction_sell_lines.transaction_id',
                '=',
                't.id'
            )
                ->join(
                    'variations as v',
                    'transaction_sell_lines.variation_id',
                    '=',
                    'v.id'
                )
                ->join('product_variations as pv', 'v.product_variation_id', '=', 'pv.id')
                ->join('contacts as c', 't.contact_id', '=', 'c.id')
                ->join('products as p', 'pv.product_id', '=', 'p.id')
                ->leftjoin('tax_rates', 'transaction_sell_lines.tax_id', '=', 'tax_rates.id')
                ->leftjoin('units as u', 'p.unit_id', '=', 'u.id')
                ->where('t.business_id', $business_id)
                ->where('t.type', 'sell')
                ->where('t.status', 'final')
                ->where('t.is_duplicate', '1')
                ->select(
                    'p.name as product_name',
                    'p.type as product_type',
                    'pv.name as product_variation',
                    'v.name as variation_name',
                    'v.sub_sku',
                    'c.name as customer',
                    'c.contact_id',
                    't.id as transaction_id',
                    't.invoice_no',
                    't.transaction_date as transaction_date',
                    'transaction_sell_lines.unit_price_before_discount as unit_price',
                    'transaction_sell_lines.unit_price_inc_tax as unit_sale_price',
                    DB::raw('(transaction_sell_lines.quantity - transaction_sell_lines.quantity_returned) as sell_qty'),
                    'transaction_sell_lines.line_discount_type as discount_type',
                    'transaction_sell_lines.line_discount_amount as discount_amount',
                    'transaction_sell_lines.item_tax',
                    'tax_rates.name as tax',
                    'u.short_name as unit',
                    DB::raw('((transaction_sell_lines.quantity - transaction_sell_lines.quantity_returned) * transaction_sell_lines.unit_price_inc_tax) as subtotal')
                )
                ->groupBy('transaction_sell_lines.id');
            if (!empty($variation_id)) {
                $query->where('transaction_sell_lines.variation_id', $variation_id);
            }
            $start_date = $request->get('start_date');
            $end_date = $request->get('end_date');
            if (!empty($start_date) && !empty($end_date)) {
                $query->whereBetween(DB::raw('date(transaction_date)'), [$start_date, $end_date]);
            }
            $permitted_locations = auth()->user()->permitted_locations();
            if ($permitted_locations != 'all') {
                $query->whereIn('t.location_id', $permitted_locations);
            }
            $location_id = $request->get('location_id', null);
            if (!empty($location_id)) {
                $query->where('t.location_id', $location_id);
            }
            $customer_id = $request->get('customer_id', null);
            if (!empty($customer_id)) {
                $query->where('t.contact_id', $customer_id);
            }
            return Datatables::of($query)
                ->editColumn('product_name', function ($row) {
                    $product_name = $row->product_name;
                    if ($row->product_type == 'variable') {
                        $product_name .= ' - ' . $row->product_variation . ' - ' . $row->variation_name;
                    }
                    return $product_name;
                })
                ->editColumn('invoice_no', function ($row) {
                    return '<a data-href="' . action('SellController@show', [$row->transaction_id])
                        . '" href="#" data-container=".view_modal" class="btn-modal">' . $row->invoice_no . '</a>';
                })
                ->editColumn('transaction_date', '{{@format_date($transaction_date)}}')
                ->editColumn('unit_sale_price', function ($row) use ($business_details) {
                    return '<span class="display_currency" data-currency_symbol = true>' . $this->productUtil->num_f($row->unit_sale_price, false, $business_details, false) . '</span>';
                })
                ->editColumn('sell_qty', function ($row) use ($business_details) {
                    return '<span data-is_quantity="true" class="display_currency sell_qty" data-currency_symbol=false data-orig-value="' . (float) $row->sell_qty . '" data-unit="' . $row->unit . '" >' . $this->productUtil->num_f($row->sell_qty, false, $business_details, false) . '</span> ' . $row->unit;
                })
                ->editColumn('subtotal', function ($row) use ($business_details) {
                    return '<span class="display_currency row_subtotal" data-currency_symbol = true data-orig-value="' . $row->subtotal . '">' . $this->productUtil->num_f($row->subtotal, false, $business_details, false) . '</span>';
                })
                ->editColumn('unit_price', function ($row) use ($business_details) {
                    return '<span class="display_currency" data-currency_symbol = true>' . $this->productUtil->num_f($row->unit_price, false, $business_details, false) . '</span>';
                })
                ->editColumn('discount_amount', '
                    @if($discount_type == "percentage")
                        {{@number_format($discount_amount)}} %
                    @elseif($discount_type == "fixed")
                        {{@number_format($discount_amount)}}
                    @endif
                    ')
                ->editColumn('tax', function ($row) {
                    return '<span class="display_currency" data-currency_symbol = true>' .
                        $row->item_tax .
                        '</span>' . '<br>' . '<span class="tax" data-orig-value="' . (float) $row->item_tax . '" data-unit="' . $row->tax . '"><small>(' . $row->tax . ')</small></span>';
                })
                ->rawColumns(['invoice_no', 'unit_sale_price', 'subtotal', 'sell_qty', 'discount_amount', 'unit_price', 'tax'])
                ->make(true);
        }
        $business_locations = BusinessLocation::forDropdown($business_id);
        $customers = Contact::customersDropdown($business_id);
        return view('report.sales_report')
            ->with(compact('business_locations', 'customers'));
    }
    /**
     * Shows product lot report
     *
     * @return \Illuminate\Http\Response
     */
    public function getLotReport(Request $request)
    {
        if (!auth()->user()->can('stock_report.view')) {
            abort(403, 'Unauthorized action.');
        }
        $business_id = $request->session()->get('user.business_id');
        //Return the details in ajax call
        if ($request->ajax()) {
            $query = Product::where('products.business_id', $business_id)
                ->leftjoin('units', 'products.unit_id', '=', 'units.id')
                ->join('variations as v', 'products.id', '=', 'v.product_id')
                ->join('purchase_lines as pl', 'v.id', '=', 'pl.variation_id')
                ->leftjoin(
                    'transaction_sell_lines_purchase_lines as tspl',
                    'pl.id',
                    '=',
                    'tspl.purchase_line_id'
                )
                ->join('transactions as t', 'pl.transaction_id', '=', 't.id');
            $permitted_locations = auth()->user()->permitted_locations();
            $location_filter = 'WHERE ';
            if ($permitted_locations != 'all') {
                $query->whereIn('t.location_id', $permitted_locations);
                $locations_imploded = implode(', ', $permitted_locations);
                $location_filter = " LEFT JOIN transactions as t2 on pls.transaction_id=t2.id WHERE t2.location_id IN ($locations_imploded) AND ";
            }
            if (!empty($request->input('location_id'))) {
                $location_id = $request->input('location_id');
                $query->where('t.location_id', $location_id)
                    //If filter by location then hide products not available in that location
                    ->ForLocation($location_id);
                $location_filter = "LEFT JOIN transactions as t2 on pls.transaction_id=t2.id WHERE t2.location_id=$location_id AND ";
            }
            if (!empty($request->input('category_id'))) {
                $query->where('products.category_id', $request->input('category_id'));
            }
            if (!empty($request->input('sub_category_id'))) {
                $query->where('products.sub_category_id', $request->input('sub_category_id'));
            }
            if (!empty($request->input('brand_id'))) {
                $query->where('products.brand_id', $request->input('brand_id'));
            }
            if (!empty($request->input('unit_id'))) {
                $query->where('products.unit_id', $request->input('unit_id'));
            }
            $only_mfg_products = request()->get('only_mfg_products', 0);
            if (!empty($only_mfg_products)) {
                $query->where('t.type', 'production_purchase');
            }
            $products = $query->select(
                'products.name as product',
                'v.name as variation_name',
                'sub_sku',
                'pl.lot_number',
                'pl.exp_date as exp_date',
                DB::raw("( COALESCE((SELECT SUM(quantity - quantity_returned) from purchase_lines as pls $location_filter variation_id = v.id AND lot_number = pl.lot_number), 0) - 
                    SUM(COALESCE((tspl.quantity - tspl.qty_returned), 0))) as stock"),
                // DB::raw("(SELECT SUM(IF(transactions.type='sell', TSL.quantity, -1* TPL.quantity) ) FROM transactions
                //         LEFT JOIN transaction_sell_lines AS TSL ON transactions.id=TSL.transaction_id
                //         LEFT JOIN purchase_lines AS TPL ON transactions.id=TPL.transaction_id
                //         WHERE transactions.status='final' AND transactions.type IN ('sell', 'sell_return') $location_filter
                //         AND (TSL.product_id=products.id OR TPL.product_id=products.id)) as total_sold"),
                DB::raw("COALESCE(SUM(IF(tspl.sell_line_id IS NULL, 0, (tspl.quantity - tspl.qty_returned)) ), 0) as total_sold"),
                DB::raw("COALESCE(SUM(IF(tspl.stock_adjustment_line_id IS NULL, 0, tspl.quantity ) ), 0) as total_adjusted"),
                'products.type',
                'units.short_name as unit'
            )
                ->whereNotNull('pl.lot_number')
                ->groupBy('v.id')
                ->groupBy('pl.lot_number');
            return Datatables::of($products)
                ->editColumn('stock', function ($row) {
                    $stock = $row->stock ? $row->stock : 0;
                    return '<span data-is_quantity="true" class="display_currency total_stock" data-currency_symbol=false data-orig-value="' . (float) $stock . '" data-unit="' . $row->unit . '" >' . (float) $stock . '</span> ' . $row->unit;
                })
                ->editColumn('product', function ($row) {
                    if ($row->variation_name != 'DUMMY') {
                        return $row->product . ' (' . $row->variation_name . ')';
                    } else {
                        return $row->product;
                    }
                })
                ->editColumn('total_sold', function ($row) {
                    if ($row->total_sold) {
                        return '<span data-is_quantity="true" class="display_currency total_sold" data-currency_symbol=false data-orig-value="' . (float) $row->total_sold . '" data-unit="' . $row->unit . '" >' . (float) $row->total_sold . '</span> ' . $row->unit;
                    } else {
                        return '0' . ' ' . $row->unit;
                    }
                })
                ->editColumn('total_adjusted', function ($row) {
                    if ($row->total_adjusted) {
                        return '<span data-is_quantity="true" class="display_currency total_adjusted" data-currency_symbol=false data-orig-value="' . (float) $row->total_adjusted . '" data-unit="' . $row->unit . '" >' . (float) $row->total_adjusted . '</span> ' . $row->unit;
                    } else {
                        return '0' . ' ' . $row->unit;
                    }
                })
                ->editColumn('exp_date', function ($row) {
                    if (!empty($row->exp_date)) {
                        $carbon_exp = \Carbon::createFromFormat('Y-m-d', $row->exp_date);
                        $carbon_now = \Carbon::now();
                        if ($carbon_now->diffInDays($carbon_exp, false) >= 0) {
                            return $this->productUtil->format_date($row->exp_date) . '<br><small>( <span class="time-to-now">' . $row->exp_date . '</span> )</small>';
                        } else {
                            return $this->productUtil->format_date($row->exp_date) . ' &nbsp; <span class="label label-danger no-print">' . __('report.expired') . '</span><span class="print_section">' . __('report.expired') . '</span><br><small>( <span class="time-from-now">' . $row->exp_date . '</span> )</small>';
                        }
                    } else {
                        return '--';
                    }
                })
                ->removeColumn('unit')
                ->removeColumn('id')
                ->removeColumn('variation_name')
                ->rawColumns(['exp_date', 'stock', 'total_sold', 'total_adjusted'])
                ->make(true);
        }
        $categories = Category::where('business_id', $business_id)
            ->where('parent_id', 0)
            ->pluck('name', 'id');
        $brands = Brands::where('business_id', $business_id)
            ->pluck('name', 'id');
        $units = Unit::where('business_id', $business_id)
            ->pluck('short_name', 'id');
        $business_locations = BusinessLocation::forDropdown($business_id, true);
        return view('report.lot_report')
            ->with(compact('categories', 'brands', 'units', 'business_locations'));
    }
    /**
     * Shows purchase payment report
     *
     * @return \Illuminate\Http\Response
     */
    public function purchasePaymentReport(Request $request)
    {
        if (!auth()->user()->can('purchase_payment_report.view')) {
            abort(403, 'Unauthorized action.');
        }
        $business_id = $request->session()->get('user.business_id');
        if ($request->ajax()) {
            $supplier_id = $request->get('supplier_id', null);
            $contact_filter1 = !empty($supplier_id) ? "AND t.contact_id=$supplier_id" : '';
            $contact_filter2 = !empty($supplier_id) ? "AND transactions.contact_id=$supplier_id" : '';
            $location_id = $request->get('location_id', null);
            $parent_payment_query_part = empty($location_id) ? "AND transaction_payments.parent_id IS NULL" : "";
            $query = TransactionPayment::leftjoin('transactions as t', function ($join) use ($business_id) {
                $join->on('transaction_payments.transaction_id', '=', 't.id')
                    ->where('t.business_id', $business_id)
                    ->whereIn('t.type', ['purchase', 'opening_balance']);
            })
                ->leftjoin('contacts', 't.contact_id', 'contacts.id')
                ->where('transaction_payments.business_id', $business_id)
                ->where(function ($q) use ($business_id, $contact_filter1, $contact_filter2, $parent_payment_query_part) {
                    $q->whereRaw("(transaction_payments.transaction_id IS NOT NULL AND t.type IN ('purchase', 'opening_balance')  $parent_payment_query_part $contact_filter1)")
                        ->orWhereRaw("EXISTS(SELECT * FROM transaction_payments as tp JOIN transactions ON tp.transaction_id = transactions.id WHERE transactions.type IN ('purchase', 'opening_balance') AND transactions.business_id = $business_id AND tp.parent_id=transaction_payments.id $contact_filter2)");
                })
                ->where('contacts.type', 'supplier')
                ->select(
                    DB::raw("IF(transaction_payments.transaction_id IS NULL, 
                                (SELECT c.name FROM transactions as ts
                                JOIN contacts as c ON ts.contact_id=c.id 
                                WHERE ts.id=(
                                        SELECT tps.transaction_id FROM transaction_payments as tps
                                        WHERE tps.parent_id=transaction_payments.id LIMIT 1
                                    )
                                ),
                                (SELECT c.name FROM transactions as ts JOIN
                                    contacts as c ON ts.contact_id=c.id
                                    WHERE ts.id=t.id 
                                )
                            ) as supplier"),
                    'transaction_payments.amount',
                    'method',
                    'paid_on',
                    'transaction_payments.payment_ref_no',
                    'transaction_payments.document',
                    't.ref_no',
                    't.id as transaction_id',
                    'cheque_number',
                    'card_transaction_number',
                    'bank_account_number',
                    'transaction_no',
                    'transaction_payments.id as DT_RowId'
                )
                ->groupBy('transaction_payments.id');
            $start_date = $request->get('start_date');
            $end_date = $request->get('end_date');
            if (!empty($start_date) && !empty($end_date)) {
                $query->whereBetween(DB::raw('date(paid_on)'), [$start_date, $end_date]);
            }
            $permitted_locations = auth()->user()->permitted_locations();
            if ($permitted_locations != 'all') {
                $query->whereIn('t.location_id', $permitted_locations);
            }
            if (!empty($location_id)) {
                $query->where('t.location_id', $location_id);
            }
            $payment_types = $this->transactionUtil->payment_types();
            $business_details = Business::find($business_id);
            return Datatables::of($query)
                ->editColumn('ref_no', function ($row) {
                    if (!empty($row->ref_no)) {
                        return '<a data-href="' . action('PurchaseController@show', [$row->transaction_id])
                            . '" href="#" data-container=".view_modal" class="btn-modal">' . $row->ref_no . '</a>';
                    } else {
                        return '';
                    }
                })
                ->editColumn('paid_on', '{{@format_datetime($paid_on)}}')
                ->editColumn('method', function ($row) use ($payment_types) {
                    $method = !empty($payment_types[$row->method]) ? $payment_types[$row->method] : '';
                    if ($row->method == 'cheque') {
                        $method .= '<br>(' . __('lang_v1.cheque_no') . ': ' . $row->cheque_number . ')';
                    } elseif ($row->method == 'card') {
                        $method .= '<br>(' . __('lang_v1.card_transaction_no') . ': ' . $row->card_transaction_number . ')';
                    } elseif ($row->method == 'bank_transfer') {
                        $method .= '<br>(' . __('lang_v1.bank_account_no') . ': ' . $row->bank_account_number . ')';
                    } elseif ($row->method == 'custom_pay_1') {
                        $method .= '<br>(' . __('lang_v1.transaction_no') . ': ' . $row->transaction_no . ')';
                    } elseif ($row->method == 'custom_pay_2') {
                        $method .= '<br>(' . __('lang_v1.transaction_no') . ': ' . $row->transaction_no . ')';
                    } elseif ($row->method == 'custom_pay_3') {
                        $method .= '<br>(' . __('lang_v1.transaction_no') . ': ' . $row->transaction_no . ')';
                    }
                    return $method;
                })
                ->editColumn('amount', function ($row) use ($business_details) {
                    return '<span class="display_currency paid-amount" data-currency_symbol = true data-orig-value="' . $row->amount . '">' . $this->productUtil->num_f($row->amount, false, $business_details, false) . '</span>';
                })
                ->addColumn('action', '<button type="button" class="btn btn-primary btn-xs view_payment" data-href="{{ action("TransactionPaymentController@viewPayment", [$DT_RowId]) }}">@lang("messages.view")
                    </button> @if(!empty($document))<a href="{{asset("/uploads/documents/" . $document)}}" class="btn btn-success btn-xs" download=""><i class="fa fa-download"></i> @lang("purchase.download_document")</a>@endif')
                ->rawColumns(['ref_no', 'amount', 'method', 'action'])
                ->make(true);
        }
        $business_locations = BusinessLocation::forDropdown($business_id);
        $suppliers = Contact::suppliersDropdown($business_id, false);
        return view('report.purchase_payment_report')
            ->with(compact('business_locations', 'suppliers'));
    }
    /**
     * Shows sell payment report
     *
     * @return \Illuminate\Http\Response
     */
    public function sellPaymentReport(Request $request)
    {
        if (!auth()->user()->can('sell_payment_report.view')) {
            abort(403, 'Unauthorized action.');
        }
        $business_id = $request->session()->get('user.business_id');
        if ($request->ajax()) {
            $customer_id = $request->get('customer_id', null);
            $contact_filter1 = !empty($customer_id) ? "AND t.contact_id=$customer_id" : '';
            $contact_filter2 = !empty($customer_id) ? "AND transactions.contact_id=$customer_id" : '';
            $location_id = $request->get('location_id', null);
            $parent_payment_query_part = empty($location_id) ? "AND transaction_payments.parent_id IS NULL" : "";
            $query = TransactionPayment::leftjoin('transactions as t', function ($join) use ($business_id) {
                $join->on('transaction_payments.transaction_id', '=', 't.id')
                    ->where('t.business_id', $business_id)
                    ->whereIn('t.type', ['sell', 'opening_balance']);
            })
                ->leftjoin('contacts as c', 't.contact_id', '=', 'c.id')
                ->where('transaction_payments.business_id', $business_id)
                ->where(function ($q) use ($business_id, $contact_filter1, $contact_filter2, $parent_payment_query_part) {
                    $q->whereRaw("(transaction_payments.transaction_id IS NOT NULL AND t.type IN ('sell', 'opening_balance') $parent_payment_query_part $contact_filter1)")
                        ->orWhereRaw("EXISTS(SELECT * FROM transaction_payments as tp JOIN transactions ON tp.transaction_id = transactions.id WHERE transactions.type IN ('sell', 'opening_balance') AND transactions.business_id = $business_id AND tp.parent_id=transaction_payments.id $contact_filter2)");
                })
                ->select(
                    DB::raw("IF(transaction_payments.transaction_id IS NULL, 
                                (SELECT c.name FROM transactions as ts
                                JOIN contacts as c ON ts.contact_id=c.id 
                                WHERE ts.id=(
                                        SELECT tps.transaction_id FROM transaction_payments as tps
                                        WHERE tps.parent_id=transaction_payments.id LIMIT 1
                                    )
                                ),
                                (SELECT c.name FROM transactions as ts JOIN
                                    contacts as c ON ts.contact_id=c.id
                                    WHERE ts.id=t.id 
                                )
                            ) as customer"),
                    'transaction_payments.amount',
                    'transaction_payments.is_return',
                    'method',
                    'paid_on',
                    'transaction_payments.payment_ref_no',
                    'transaction_payments.document',
                    'transaction_payments.transaction_no',
                    'transaction_payments.transaction_id as tp_tid',
                    't.ref_no',
                    't.invoice_no',
                    't.id as transaction_id',
                    'cheque_number',
                    'card_transaction_number',
                    'bank_account_number',
                    'transaction_payments.id as DT_RowId'
                )
                ->groupBy('transaction_payments.id');
            $start_date = $request->get('start_date');
            $end_date = $request->get('end_date');
            if (!empty($start_date) && !empty($end_date)) {
                $query->whereBetween(DB::raw('date(paid_on)'), [$start_date, $end_date]);
            }
            $permitted_locations = auth()->user()->permitted_locations();
            if ($permitted_locations != 'all') {
                $query->whereIn('t.location_id', $permitted_locations);
            }
            if (!empty($location_id)) {
                $query->where('t.location_id', $location_id);
            }
            if (!empty($request->get('payment_types'))) {
                $query->where('transaction_payments.method', $request->get('payment_types'));
            }
            $payment_types = $this->transactionUtil->payment_types();
            $business_details = Business::find($business_id);
            return Datatables::of($query)
                ->editColumn('invoice_no', function ($row) {
                    if (!empty($row->transaction_id)) {
                        return '<a data-href="' . action('SellController@show', [$row->transaction_id])
                            . '" href="#" data-container=".view_modal" class="btn-modal">' . $row->invoice_no . '</a>';
                    } else {
                        return '';
                    }
                })
                ->editColumn('ref_no', function ($row) {
                    if (!empty($row->tp_tid)) {
                        return $row->ref_no;
                    } else {
                        $tran = TransactionPayment::leftjoin('transactions', 'transaction_payments.transaction_id', 'transactions.id')->where('parent_id', $row->DT_RowId)->first();
                        return !empty($tran) ? $tran->ref_no : null;
                    }
                })
                ->editColumn('paid_on', '{{@format_datetime($paid_on)}}')
                ->editColumn('method', function ($row) use ($payment_types) {
                    $method = !empty($payment_types[$row->method]) ? $payment_types[$row->method] : '';
                    if ($row->method == 'cheque') {
                        $method .= '<br>(' . __('lang_v1.cheque_no') . ': ' . $row->cheque_number . ')';
                    } elseif ($row->method == 'card') {
                        $method .= '<br>(' . __('lang_v1.card_transaction_no') . ': ' . $row->card_transaction_number . ')';
                    } elseif ($row->method == 'bank_transfer') {
                        $method .= '<br>(' . __('lang_v1.bank_account_no') . ': ' . $row->bank_account_number . ')';
                    } elseif ($row->method == 'custom_pay_1') {
                        $method .= '<br>(' . __('lang_v1.transaction_no') . ': ' . $row->transaction_no . ')';
                    } elseif ($row->method == 'custom_pay_2') {
                        $method .= '<br>(' . __('lang_v1.transaction_no') . ': ' . $row->transaction_no . ')';
                    } elseif ($row->method == 'custom_pay_3') {
                        $method .= '<br>(' . __('lang_v1.transaction_no') . ': ' . $row->transaction_no . ')';
                    }
                    if ($row->is_return == 1) {
                        $method .= '<br><small>(' . __('lang_v1.change_return') . ')</small>';
                    }
                    return $method;
                })
                ->editColumn('amount', function ($row) use ($business_details) {
                    $amount = $row->is_return == 1 ? -1 * $row->amount : $row->amount;
                    return '<span class="display_currency paid-amount" data-orig-value="' . $amount . '" data-currency_symbol = true>' . $this->productUtil->num_f($amount, false, $business_details, false) . '</span>';
                })
                ->editColumn('customer', function ($row) {
                    if (empty($row->customer)) {
                        return 'Walk-In Customer';
                    }
                    return $row->customer;
                })
                ->addColumn('action', '<button type="button" class="btn btn-primary btn-xs view_payment" data-href="{{ action("TransactionPaymentController@viewPayment", [$DT_RowId]) }}">@lang("messages.view")
                    </button> @if(!empty($document))<a href="{{asset("/uploads/documents/" . $document)}}" class="btn btn-success btn-xs" download=""><i class="fa fa-download"></i> @lang("purchase.download_document")</a>@endif')
                ->rawColumns(['invoice_no', 'amount', 'method', 'action'])
                ->make(true);
        }
        $business_locations = BusinessLocation::forDropdown($business_id);
        $customers = Contact::customersDropdown($business_id, false);
        $payment_types = $this->transactionUtil->payment_types();
        return view('report.sell_payment_report')
            ->with(compact('business_locations', 'customers', 'payment_types'));
    }
    /**
     * Shows tables report
     *
     * @return \Illuminate\Http\Response
     */
    public function getTableReport(Request $request)
    {
        if (!auth()->user()->can('purchase_n_sell_report.view')) {
            abort(403, 'Unauthorized action.');
        }
        $business_id = $request->session()->get('user.business_id');
        if ($request->ajax()) {
            $query = ResTable::leftjoin('transactions AS T', 'T.res_table_id', '=', 'res_tables.id')
                ->where('T.business_id', $business_id)
                ->where('T.type', 'sell')
                ->where('T.status', 'final')
                ->groupBy('res_tables.id')
                ->select(DB::raw("SUM(final_total) as total_sell"), 'res_tables.name as table');
            $location_id = $request->get('location_id', null);
            if (!empty($location_id)) {
                $query->where('T.location_id', $location_id);
            }
            $start_date = $request->get('start_date');
            $end_date = $request->get('end_date');
            if (!empty($start_date) && !empty($end_date)) {
                $query->whereBetween(DB::raw('date(transaction_date)'), [$start_date, $end_date]);
            }
            return Datatables::of($query)
                ->editColumn('total_sell', function ($row) {
                    return '<span class="display_currency" data-currency_symbol="true">' . $row->total_sell . '</span>';
                })
                ->rawColumns(['total_sell'])
                ->make(true);
        }
        $business_locations = BusinessLocation::forDropdown($business_id, true);
        return view('report.table_report')
            ->with(compact('business_locations'));
    }
    /**
     * Shows service staff report
     *
     * @return \Illuminate\Http\Response
     */
    public function getServiceStaffReport(Request $request)
    {
        if (!auth()->user()->can('sales_representative.view')) {
            abort(403, 'Unauthorized action.');
        }
        $business_id = $request->session()->get('user.business_id');
        $business_locations = BusinessLocation::forDropdown($business_id, true);
        $waiters = $this->transactionUtil->serviceStaffDropdown($business_id);
        return view('report.service_staff_report')
            ->with(compact('business_locations', 'waiters'));
    }
    /**
     * Shows product sell report grouped by date
     *
     * @return \Illuminate\Http\Response
     */
    public function getproductSellGroupedReport(Request $request)
    {
        if (!auth()->user()->can('purchase_n_sell_report.view')) {
            abort(403, 'Unauthorized action.');
        }
        $business_id = $request->session()->get('user.business_id');
        $business_details = Business::find($business_id);
        $location_id = $request->get('location_id', null);
        $vld_str = '';
        if (!empty($location_id)) {
            $vld_str = "AND vld.location_id=$location_id";
        }
        if ($request->ajax()) {
            $variation_id = $request->get('variation_id', null);
            $variation_id = $request->get('variation_id', null);
            $query = TransactionSellLine::join(
                'transactions as t',
                'transaction_sell_lines.transaction_id',
                '=',
                't.id'
            )
                ->join(
                    'variations as v',
                    'transaction_sell_lines.variation_id',
                    '=',
                    'v.id'
                )
                ->join('product_variations as pv', 'v.product_variation_id', '=', 'pv.id')
                ->join('products as p', 'pv.product_id', '=', 'p.id')
                ->leftjoin('units as u', 'p.unit_id', '=', 'u.id')
                ->where('t.business_id', $business_id)
                ->where('t.type', 'sell')
                ->where('t.status', 'final')
                ->select(
                    'p.name as product_name',
                    'p.enable_stock',
                    'p.type as product_type',
                    'pv.name as product_variation',
                    'v.name as variation_name',
                    'v.sub_sku',
                    't.id as transaction_id',
                    't.transaction_date as transaction_date',
                    DB::raw('DATE_FORMAT(t.transaction_date, "%Y-%m-%d") as formated_date'),
                    DB::raw("(SELECT SUM(vld.qty_available) FROM variation_location_details as vld WHERE vld.variation_id=v.id $vld_str) as current_stock"),
                    DB::raw('SUM(transaction_sell_lines.quantity - transaction_sell_lines.quantity_returned) as total_qty_sold'),
                    'u.short_name as unit',
                    DB::raw('SUM((transaction_sell_lines.quantity - transaction_sell_lines.quantity_returned) * transaction_sell_lines.unit_price_inc_tax) as subtotal')
                )
                ->groupBy('v.id');
            if (!empty($variation_id)) {
                $query->where('transaction_sell_lines.variation_id', $variation_id);
            }
            $start_date = $request->get('start_date');
            $end_date = $request->get('end_date');
            if (!empty($start_date) && !empty($end_date)) {
                $query->whereBetween(DB::raw('date(transaction_date)'), [$start_date, $end_date]);
            }
            $permitted_locations = auth()->user()->permitted_locations();
            if ($permitted_locations != 'all') {
                $query->whereIn('t.location_id', $permitted_locations);
            }
            if (!empty($location_id)) {
                $query->where('t.location_id', $location_id);
            }
            $customer_id = $request->get('customer_id', null);
            if (!empty($customer_id)) {
                $query->where('t.contact_id', $customer_id);
            }
            return Datatables::of($query)
                ->editColumn('product_name', function ($row) {
                    $product_name = $row->product_name;
                    if ($row->product_type == 'variable') {
                        $product_name .= ' - ' . $row->product_variation . ' - ' . $row->variation_name;
                    }
                    return $product_name;
                })
                ->editColumn('transaction_date', '{{@format_date($formated_date)}}')
                ->editColumn('total_qty_sold', function ($row) use ($business_details) {
                    return '<span data-is_quantity="true" class="display_currency sell_qty" data-currency_symbol=false data-orig-value="' . (float) $row->total_qty_sold . '" data-unit="' . $row->unit . '" >' . $this->productUtil->num_f($row->total_qty_sold, false, $business_details, true) . '</span> ' . $row->unit;
                })
                ->editColumn('current_stock', function ($row) use ($business_details) {
                    if ($row->enable_stock) {
                        return '<span data-is_quantity="true" class="display_currency current_stock" data-currency_symbol=false data-orig-value="' . (float) $row->current_stock . '" data-unit="' . $row->unit . '" >' . $this->productUtil->num_f($row->current_stock, false, $business_details, true) . '</span> ' . $row->unit;
                    } else {
                        return '';
                    }
                })
                ->editColumn('subtotal', function ($row) use ($business_details) {
                    return '<span class="display_currency row_subtotal" data-currency_symbol = true data-orig-value="' . $row->subtotal . '">' . $this->productUtil->num_f($row->subtotal, false, $business_details, false) . '</span>';
                })
                ->rawColumns(['current_stock', 'subtotal', 'total_qty_sold'])
                ->make(true);
        }
    }
    /**
     * Shows product sell report grouped by date
     *
     * @return \Illuminate\Http\Response
     */
    public function getproductSalesGroupedReportDuplicate(Request $request)
    {
        if (!auth()->user()->can('purchase_n_sell_report.view')) {
            abort(403, 'Unauthorized action.');
        }
        $business_id = $request->session()->get('user.business_id');
        $location_id = $request->get('location_id', null);
        $vld_str = '';
        if (!empty($location_id)) {
            $vld_str = "AND vld.location_id=$location_id";
        }
        if ($request->ajax()) {
            $variation_id = $request->get('variation_id', null);
            $query = TransactionSellLine::join(
                'transactions as t',
                'transaction_sell_lines.transaction_id',
                '=',
                't.id'
            )
                ->join(
                    'variations as v',
                    'transaction_sell_lines.variation_id',
                    '=',
                    'v.id'
                )
                ->join('product_variations as pv', 'v.product_variation_id', '=', 'pv.id')
                ->join('products as p', 'pv.product_id', '=', 'p.id')
                ->leftjoin('units as u', 'p.unit_id', '=', 'u.id')
                ->where('t.business_id', $business_id)
                ->where('t.type', 'sell')
                ->where('t.status', 'final')
                ->where('t.is_duplicate', '1')
                ->select(
                    'p.name as product_name',
                    'p.enable_stock',
                    'p.type as product_type',
                    'pv.name as product_variation',
                    'v.name as variation_name',
                    'v.sub_sku',
                    't.id as transaction_id',
                    't.transaction_date as transaction_date',
                    DB::raw('DATE_FORMAT(t.transaction_date, "%Y-%m-%d") as formated_date'),
                    DB::raw("(SELECT SUM(vld.qty_available) FROM variation_location_details as vld WHERE vld.variation_id=v.id $vld_str) as current_stock"),
                    DB::raw('SUM(transaction_sell_lines.quantity - transaction_sell_lines.quantity_returned) as total_qty_sold'),
                    'u.short_name as unit',
                    DB::raw('SUM((transaction_sell_lines.quantity - transaction_sell_lines.quantity_returned) * transaction_sell_lines.unit_price_inc_tax) as subtotal')
                )
                ->groupBy('v.id')
                ->groupBy('formated_date');
            if (!empty($variation_id)) {
                $query->where('transaction_sell_lines.variation_id', $variation_id);
            }
            $start_date = $request->get('start_date');
            $end_date = $request->get('end_date');
            if (!empty($start_date) && !empty($end_date)) {
                $query->whereBetween(DB::raw('date(transaction_date)'), [$start_date, $end_date]);
            }
            $permitted_locations = auth()->user()->permitted_locations();
            if ($permitted_locations != 'all') {
                $query->whereIn('t.location_id', $permitted_locations);
            }
            if (!empty($location_id)) {
                $query->where('t.location_id', $location_id);
            }
            $customer_id = $request->get('customer_id', null);
            if (!empty($customer_id)) {
                $query->where('t.contact_id', $customer_id);
            }
            return Datatables::of($query)
                ->editColumn('product_name', function ($row) {
                    $product_name = $row->product_name;
                    if ($row->product_type == 'variable') {
                        $product_name .= ' - ' . $row->product_variation . ' - ' . $row->variation_name;
                    }
                    return $product_name;
                })
                ->editColumn('transaction_date', '{{@format_date($formated_date)}}')
                ->editColumn('total_qty_sold', function ($row) {
                    return '<span data-is_quantity="true" class="display_currency sell_qty" data-currency_symbol=false data-orig-value="' . (float) $row->total_qty_sold . '" data-unit="' . $row->unit . '" >' . (float) $row->total_qty_sold . '</span> ' . $row->unit;
                })
                ->editColumn('current_stock', function ($row) {
                    if ($row->enable_stock) {
                        return '<span data-is_quantity="true" class="display_currency current_stock" data-currency_symbol=false data-orig-value="' . (float) $row->current_stock . '" data-unit="' . $row->unit . '" >' . (float) $row->current_stock . '</span> ' . $row->unit;
                    } else {
                        return '';
                    }
                })
                ->editColumn('subtotal', function ($row) {
                    return '<span class="display_currency row_subtotal" data-currency_symbol = true data-orig-value="' . $row->subtotal . '">' . $row->subtotal . '</span>';
                })
                ->rawColumns(['current_stock', 'subtotal', 'total_qty_sold'])
                ->make(true);
        }
    }
    /**
     * Shows product stock details and allows to adjust mismatch
     *
     * @return \Illuminate\Http\Response
     */
    public function productStockDetails()
    {
        if (!auth()->user()->can('report.stock_details')) {
            abort(403, 'Unauthorized action.');
        }
        $business_id = request()->session()->get('user.business_id');
        $stock_details = [];
        $location = null;
        $total_stock_calculated = 0;
        if (!empty(request()->input('location_id'))) {
            $variation_id = request()->get('variation_id', null);
            $location_id = request()->input('location_id');
            $location = BusinessLocation::where('business_id', $business_id)
                ->where('id', $location_id)
                ->first();
            $query = Variation::leftjoin('products as p', 'p.id', '=', 'variations.product_id')
                ->leftjoin('units', 'p.unit_id', '=', 'units.id')
                ->leftjoin('variation_location_details as vld', 'variations.id', '=', 'vld.variation_id')
                ->leftjoin('product_variations as pv', 'variations.product_variation_id', '=', 'pv.id')
                ->where('p.business_id', $business_id)
                ->where('vld.location_id', $location_id);
            if (!is_null($variation_id)) {
                $query->where('variations.id', $variation_id);
            }
            $stock_details = $query->select(
                DB::raw("(SELECT SUM(COALESCE(TSL.quantity, 0)) FROM transactions 
                        LEFT JOIN transaction_sell_lines AS TSL ON transactions.id=TSL.transaction_id
                        WHERE transactions.status='final' AND transactions.type='sell' AND transactions.location_id=$location_id 
                        AND TSL.variation_id=variations.id) as total_sold"),
                DB::raw("(SELECT SUM(COALESCE(TSL.quantity_returned, 0)) FROM transactions 
                        LEFT JOIN transaction_sell_lines AS TSL ON transactions.id=TSL.transaction_id
                        WHERE transactions.status='final' AND transactions.type='sell' AND transactions.location_id=$location_id 
                        AND TSL.variation_id=variations.id) as total_sell_return"),
                DB::raw("(SELECT SUM(COALESCE(TSL.quantity,0)) FROM transactions 
                        LEFT JOIN transaction_sell_lines AS TSL ON transactions.id=TSL.transaction_id
                        WHERE transactions.status='final' AND transactions.type='sell_transfer' AND transactions.location_id=$location_id 
                        AND TSL.variation_id=variations.id) as total_sell_transfered"),
                DB::raw("(SELECT SUM(COALESCE(PL.quantity,0)) FROM transactions 
                        LEFT JOIN purchase_lines AS PL ON transactions.id=PL.transaction_id
                        WHERE transactions.status='received' AND transactions.type='purchase_transfer' AND transactions.location_id=$location_id 
                        AND PL.variation_id=variations.id) as total_purchase_transfered"),
                DB::raw("(SELECT SUM(COALESCE(SAL.quantity, 0)) FROM transactions 
                        LEFT JOIN stock_adjustment_lines AS SAL ON transactions.id=SAL.transaction_id
                        WHERE transactions.status='received' AND transactions.type='stock_adjustment' AND transactions.location_id=$location_id 
                        AND SAL.variation_id=variations.id) as total_adjusted"),
                DB::raw("(SELECT SUM(COALESCE(PL.quantity, 0)) FROM transactions 
                        LEFT JOIN purchase_lines AS PL ON transactions.id=PL.transaction_id
                        WHERE transactions.status='received' AND transactions.type='purchase' AND transactions.location_id=$location_id
                        AND PL.variation_id=variations.id) as total_purchased"),
                DB::raw("(SELECT SUM(COALESCE(PL.quantity_returned, 0)) FROM transactions 
                        LEFT JOIN purchase_lines AS PL ON transactions.id=PL.transaction_id
                        WHERE transactions.status='received' AND transactions.type='purchase' AND transactions.location_id=$location_id
                        AND PL.variation_id=variations.id) as total_purchase_return"),
                DB::raw("(SELECT SUM(COALESCE(PL.quantity, 0)) FROM transactions 
                        LEFT JOIN purchase_lines AS PL ON transactions.id=PL.transaction_id
                        WHERE transactions.status='received' AND transactions.type='opening_stock' AND transactions.location_id=$location_id
                        AND PL.variation_id=variations.id) as total_opening_stock"),
                DB::raw("(SELECT SUM(COALESCE(PL.quantity, 0)) FROM transactions 
                        LEFT JOIN purchase_lines AS PL ON transactions.id=PL.transaction_id
                        WHERE transactions.status='received' AND transactions.type='production_purchase' AND transactions.location_id=$location_id
                        AND PL.variation_id=variations.id) as total_manufactured"),
                DB::raw("(SELECT SUM(COALESCE(TSL.quantity, 0)) FROM transactions 
                        LEFT JOIN transaction_sell_lines AS TSL ON transactions.id=TSL.transaction_id
                        WHERE transactions.status='final' AND transactions.type='production_sell' AND transactions.location_id=$location_id 
                        AND TSL.variation_id=variations.id) as total_ingredients_used"),
                DB::raw("SUM(vld.qty_available) as stock"),
                'variations.sub_sku as sub_sku',
                'p.name as product',
                'p.id as product_id',
                'p.type',
                'p.sku as sku',
                'units.short_name as unit',
                'p.enable_stock as enable_stock',
                'variations.sell_price_inc_tax as unit_price',
                'pv.name as product_variation',
                'variations.name as variation_name',
                'variations.id as variation_id'
            )
                ->groupBy('variations.id')
                ->get();
            foreach ($stock_details as $index => $row) {
                $total_sold = $row->total_sold ?: 0;
                $total_sell_return = $row->total_sell_return ?: 0;
                $total_sell_transfered = $row->total_sell_transfered ?: 0;
                $total_purchase_transfered = $row->total_purchase_transfered ?: 0;
                $total_adjusted = $row->total_adjusted ?: 0;
                $total_purchased = $row->total_purchased ?: 0;
                $total_purchase_return = $row->total_purchase_return ?: 0;
                $total_opening_stock = $row->total_opening_stock ?: 0;
                $total_manufactured = $row->total_manufactured ?: 0;
                $total_ingredients_used = $row->total_ingredients_used ?: 0;
                $total_stock_calculated = $total_opening_stock + $total_purchased + $total_purchase_transfered + $total_sell_return + $total_manufactured
                    - ($total_sold + $total_sell_transfered + $total_adjusted + $total_purchase_return + $total_ingredients_used);
                $stock_details[$index]->total_stock_calculated = $total_stock_calculated;
            }
        }
        $business_locations = BusinessLocation::forDropdown($business_id);
        return view('report.product_stock_details')
            ->with(compact('stock_details', 'business_locations', 'location'));
    }
    /**
     * Adjusts stock availability mismatch if found
     *
     * @return \Illuminate\Http\Response
     */
    public function adjustProductStock()
    {
        if (!auth()->user()->can('report.stock_details')) {
            abort(403, 'Unauthorized action.');
        }
        if (
            !empty(request()->input('variation_id'))
            && !empty(request()->input('location_id'))
            && !empty(request()->input('stock'))
        ) {
            $business_id = request()->session()->get('user.business_id');
            $vld = VariationLocationDetails::leftjoin(
                'business_locations as bl',
                'bl.id',
                '=',
                'variation_location_details.location_id'
            )
                ->where('variation_location_details.location_id', request()->input('location_id'))
                ->where('variation_id', request()->input('variation_id'))
                ->where('bl.business_id', $business_id)
                ->select('variation_location_details.*')
                ->first();
            if (!empty($vld)) {
                $vld->qty_available = request()->input('stock');
                $vld->save();
            }
        }
        return redirect()->back()->with(['status' => [
            'success' => 1,
            'msg' => __('lang_v1.updated_succesfully')
        ]]);
    }
    /**
     * Retrieves line orders/sales
     *
     * @return obj
     */
    public function serviceStaffLineOrders()
    {
        $business_id = request()->session()->get('user.business_id');
        $query = TransactionSellLine::leftJoin('transactions as t', 't.id', '=', 'transaction_sell_lines.transaction_id')
            ->leftJoin('variations as v', 'transaction_sell_lines.variation_id', '=', 'v.id')
            ->leftJoin('products as p', 'v.product_id', '=', 'p.id')
            ->leftJoin('units as u', 'p.unit_id', '=', 'u.id')
            ->leftJoin('product_variations as pv', 'v.product_variation_id', '=', 'pv.id')
            ->leftJoin('users as ss', 'ss.id', '=', 'transaction_sell_lines.res_service_staff_id')
            ->leftjoin(
                'business_locations AS bl',
                't.location_id',
                '=',
                'bl.id'
            )
            ->where('t.business_id', $business_id)
            ->where('t.type', 'sell')
            ->where('t.status', 'final')
            ->whereNotNull('transaction_sell_lines.res_service_staff_id');
        if (!empty(request()->service_staff_id)) {
            $query->where('transaction_sell_lines.res_service_staff_id', request()->service_staff_id);
        }
        if (request()->has('location_id')) {
            $location_id = request()->get('location_id');
            if (!empty($location_id)) {
                $query->where('t.location_id', $location_id);
            }
        }
        if (!empty(request()->start_date) && !empty(request()->end_date)) {
            $start = request()->start_date;
            $end =  request()->end_date;
            $query->whereDate('t.transaction_date', '>=', $start)
                ->whereDate('t.transaction_date', '<=', $end);
        }
        $query->select(
            'p.name as product_name',
            'p.type as product_type',
            'v.name as variation_name',
            'pv.name as product_variation_name',
            'u.short_name as unit',
            't.id as transaction_id',
            'bl.name as business_location',
            't.transaction_date',
            't.invoice_no',
            'transaction_sell_lines.quantity',
            'transaction_sell_lines.unit_price_before_discount',
            'transaction_sell_lines.line_discount_type',
            'transaction_sell_lines.line_discount_amount',
            'transaction_sell_lines.item_tax',
            'transaction_sell_lines.unit_price_inc_tax',
            DB::raw('CONCAT(COALESCE(ss.first_name, ""), COALESCE(ss.last_name, "")) as service_staff')
        );
        $datatable = Datatables::of($query)
            ->editColumn('product_name', function ($row) {
                $name = $row->product_name;
                if ($row->product_type == 'variable') {
                    $name .= ' - ' . $row->product_variation_name . ' - ' . $row->variation_name;
                }
                return $name;
            })
            ->editColumn(
                'unit_price_inc_tax',
                '<span class="display_currency unit_price_inc_tax" data-currency_symbol="true" data-orig-value="{{$unit_price_inc_tax}}">{{$unit_price_inc_tax}}</span>'
            )
            ->editColumn(
                'item_tax',
                '<span class="display_currency item_tax" data-currency_symbol="true" data-orig-value="{{$item_tax}}">{{$item_tax}}</span>'
            )
            ->editColumn(
                'quantity',
                '<span class="display_currency quantity" data-unit="{{$unit}}" data-currency_symbol="false" data-orig-value="{{$quantity}}">{{$quantity}}</span> {{$unit}}'
            )
            ->editColumn(
                'unit_price_before_discount',
                '<span class="display_currency unit_price_before_discount" data-currency_symbol="true" data-orig-value="{{$unit_price_before_discount}}">{{$unit_price_before_discount}}</span>'
            )
            ->addColumn(
                'total',
                '<span class="display_currency total" data-currency_symbol="true" data-orig-value="{{$unit_price_inc_tax * $quantity}}">{{$unit_price_inc_tax * $quantity}}</span>'
            )
            ->editColumn(
                'line_discount_amount',
                function ($row) {
                    $discount = !empty($row->line_discount_amount) ? $row->line_discount_amount : 0;
                    if (!empty($discount) && $row->line_discount_type == 'percentage') {
                        $discount = $row->unit_price_before_discount * ($discount / 100);
                    }
                    return '<span class="display_currency total-discount" data-currency_symbol="true" data-orig-value="' . $discount . '">' . $discount . '</span>';
                }
            )
            ->editColumn('transaction_date', '{{@format_date($transaction_date)}}')
            ->rawColumns(['line_discount_amount', 'unit_price_before_discount', 'item_tax', 'unit_price_inc_tax', 'item_tax', 'quantity', 'total'])
            ->make(true);
        return $datatable;
    }
    /**
     * Lists profit by product, category, brand, location, invoice and date
     *
     * @return string $by = null
     */
    public function getProfit($by = null, $only_query = false)
    {
        $business_id = request()->session()->get('user.business_id');
        $query = TransactionSellLine
            ::join('transactions as sale', 'transaction_sell_lines.transaction_id', '=', 'sale.id')
            ->leftjoin('transaction_sell_lines_purchase_lines as TSPL', 'transaction_sell_lines.id', '=', 'TSPL.sell_line_id')
            ->leftjoin(
                'purchase_lines as PL',
                'TSPL.purchase_line_id',
                '=',
                'PL.id'
            )
            ->join('products as P', 'transaction_sell_lines.product_id', '=', 'P.id')
            ->where('sale.business_id', $business_id)
            ->where('transaction_sell_lines.children_type', '!=', 'combo');
        //If type combo: find childrens, sale price parent - get PP of childrens
        $query->select(DB::raw('SUM(IF (TSPL.id IS NULL AND P.type="combo", ( 
        SELECT Sum((tspl2.quantity - tspl2.qty_returned) * (tsl.unit_price_inc_tax - pl2.purchase_price_inc_tax)) AS total
        FROM transaction_sell_lines AS tsl
            JOIN transaction_sell_lines_purchase_lines AS tspl2
        ON tsl.id=tspl2.sell_line_id 
        JOIN purchase_lines AS pl2 
        ON tspl2.purchase_line_id = pl2.id 
        WHERE tsl.parent_sell_line_id = transaction_sell_lines.id), 
        (TSPL.quantity - TSPL.qty_returned) * (transaction_sell_lines.unit_price_inc_tax - PL.purchase_price_inc_tax))) AS gross_profit'));
        if (!empty(request()->start_date) && !empty(request()->end_date)) {
            $start = request()->start_date;
            $end =  request()->end_date;
            $query->whereDate('sale.transaction_date', '>=', $start)
                ->whereDate('sale.transaction_date', '<=', $end);
        }
        if ($by == 'product') {
            $query->join('variations as V', 'transaction_sell_lines.variation_id', '=', 'V.id')
                ->leftJoin('product_variations as PV', 'PV.id', '=', 'V.product_variation_id')
                ->addSelect(DB::raw("IF(P.type='variable', CONCAT(P.name, ' - ', PV.name, ' - ', V.name, ' (', V.sub_sku, ')'), CONCAT(P.name, ' (', P.sku, ')')) as product"))
                ->groupBy('V.id');
        }
        if ($by == 'category') {
            $query->join('variations as V', 'transaction_sell_lines.variation_id', '=', 'V.id')
                ->leftJoin('categories as C', 'C.id', '=', 'P.category_id')
                ->addSelect("C.name as category")
                ->groupBy('C.id');
        }
        if ($by == 'brand') {
            $query->join('variations as V', 'transaction_sell_lines.variation_id', '=', 'V.id')
                ->leftJoin('brands as B', 'B.id', '=', 'P.brand_id')
                ->addSelect("B.name as brand")
                ->groupBy('B.id');
        }
        if ($by == 'location') {
            $query->join('business_locations as L', 'sale.location_id', '=', 'L.id')
                ->addSelect("L.name as location")
                ->groupBy('L.id');
        }
        if ($by == 'invoice') {
            $query->addSelect('sale.invoice_no', 'sale.id as transaction_id', 'final_total')
                ->groupBy('sale.invoice_no');
        }
        if ($by == 'date') {
            $query->addSelect("sale.transaction_date")
                ->groupBy(DB::raw('DATE(sale.transaction_date)'));
        }
        if ($by == 'day') {
            $results = $query->addSelect(DB::raw("DAYNAME(sale.transaction_date) as day"))
                ->groupBy(DB::raw('DAYOFWEEK(sale.transaction_date)'))
                ->get();
            $profits = [];
            foreach ($results as $result) {
                $profits[strtolower($result->day)] = $result->gross_profit;
            }
            $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
            return view('report.partials.profit_by_day')->with(compact('profits', 'days'));
        }
        if ($by == 'customer') {
            $query->join('contacts as CU', 'sale.contact_id', '=', 'CU.id')
                ->addSelect("CU.name as customer")
                ->groupBy('sale.contact_id');
        }
        if ($only_query) {
            return $query->get()->sum('gross_profit');
        }
        $datatable = Datatables::of($query)
            ->editColumn(
                'gross_profit',
                '<span class="display_currency gross-profit" data-currency_symbol="true" data-orig-value="{{$gross_profit}}">{{$gross_profit}}</span>'
            );
        if ($by == 'invoice') {
            $datatable->editColumn(
                'final_total',
                '<span class="display_currency final-total" data-currency_symbol="true" data-orig-value="{{$final_total}}">{{$final_total}}</span>'
            );
        }
        if ($by == 'category') {
            $datatable->editColumn(
                'category',
                '{{$category ?? __("lang_v1.uncategorized")}}'
            );
        }
        if ($by == 'brand') {
            $datatable->editColumn(
                'brand',
                '{{$brand ?? __("report.others")}}'
            );
        }
        if ($by == 'date') {
            $datatable->editColumn('transaction_date', '{{@format_date($transaction_date)}}');
        }
        $row_columns = ['gross_profit'];
        if ($by == 'invoice') {
            $datatable->editColumn('invoice_no', function ($row) {
                return '<a data-href="' . action('SellController@show', [$row->transaction_id])
                    . '" href="#" data-container=".view_modal" class="btn-modal">' . $row->invoice_no . '</a>';
            });
            $row_columns[] = 'invoice_no';
            $row_columns[] = 'final_total';
        }
        return $datatable->rawColumns($row_columns)
            ->make(true);
    }
    /**
     * Shows items report from sell purchase mapping table
     *
     * @return \Illuminate\Http\Response
     */
    public function itemsReport()
    {
        if (!auth()->user()->can('item_report.view')) {
            abort(403, 'Unauthorized action.');
        }
        $business_id = request()->session()->get('user.business_id');
        $business_details = Business::find($business_id);
        if (request()->ajax()) {
            $query = TransactionSellLinesPurchaseLines::leftJoin('transaction_sell_lines as SL', 'SL.id', '=', 'transaction_sell_lines_purchase_lines.sell_line_id')
                ->leftJoin('stock_adjustment_lines as SAL', 'SAL.id', '=', 'transaction_sell_lines_purchase_lines.stock_adjustment_line_id')
                ->leftJoin('transactions as sale', 'SL.transaction_id', '=', 'sale.id')
                ->leftJoin('transactions as stock_adjustment', 'SAL.transaction_id', '=', 'stock_adjustment.id')
                ->join('purchase_lines as PL', 'PL.id', '=', 'transaction_sell_lines_purchase_lines.purchase_line_id')
                ->join('transactions as purchase', 'PL.transaction_id', '=', 'purchase.id')
                ->join('business_locations as bl', 'purchase.location_id', '=', 'bl.id')
                ->join(
                    'variations as v',
                    'PL.variation_id',
                    '=',
                    'v.id'
                )
                ->join('product_variations as pv', 'v.product_variation_id', '=', 'pv.id')
                ->join('products as p', 'PL.product_id', '=', 'p.id')
                ->join('units as u', 'p.unit_id', '=', 'u.id')
                ->leftJoin('contacts as suppliers', 'purchase.contact_id', '=', 'suppliers.id')
                ->leftJoin('contacts as customers', 'sale.contact_id', '=', 'customers.id')
                ->where('purchase.business_id', $business_id)
                ->where('sale.sub_type', '!=', 'credit_sale')
                ->select(
                    'v.sub_sku as sku',
                    'p.type as product_type',
                    'p.id as product_id',
                    'p.name as product_name',
                    'v.name as variation_name',
                    'pv.name as product_variation',
                    'u.short_name as unit',
                    'purchase.transaction_date as purchase_date',
                    'purchase.ref_no as purchase_ref_no',
                    'purchase.type as purchase_type',
                    'suppliers.name as supplier',
                    'PL.purchase_price_inc_tax as purchase_price',
                    'sale.transaction_date as sell_date',
                    'stock_adjustment.transaction_date as stock_adjustment_date',
                    'sale.invoice_no as sale_invoice_no',
                    'stock_adjustment.ref_no as stock_adjustment_ref_no',
                    'customers.name as customer',
                    'transaction_sell_lines_purchase_lines.quantity as quantity',
                    'SL.unit_price as selling_price',
                    'SAL.unit_price as stock_adjustment_price',
                    'transaction_sell_lines_purchase_lines.stock_adjustment_line_id',
                    'transaction_sell_lines_purchase_lines.sell_line_id',
                    'transaction_sell_lines_purchase_lines.purchase_line_id',
                    'transaction_sell_lines_purchase_lines.qty_returned',
                    'bl.name as location'
                );
            if (!empty(request()->purchase_start) && !empty(request()->purchase_end)) {
                $start = request()->purchase_start;
                $end =  request()->purchase_end;
                $query->whereDate('purchase.transaction_date', '>=', $start)
                    ->whereDate('purchase.transaction_date', '<=', $end);
            }
            if (!empty(request()->sale_start) && !empty(request()->sale_end)) {
                $start = request()->sale_start;
                $end =  request()->sale_end;
                $query->where(function ($q) use ($start, $end) {
                    $q->where(function ($qr) use ($start, $end) {
                        $qr->whereDate('sale.transaction_date', '>=', $start)
                            ->whereDate('sale.transaction_date', '<=', $end);
                    })->orWhere(function ($qr) use ($start, $end) {
                        $qr->whereDate('stock_adjustment.transaction_date', '>=', $start)
                            ->whereDate('stock_adjustment.transaction_date', '<=', $end);
                    });
                });
            }
            $supplier_id = request()->get('supplier_id', null);
            if (!empty($supplier_id)) {
                $query->where('suppliers.id', $supplier_id);
            }
            $customer_id = request()->get('customer_id', null);
            if (!empty($customer_id)) {
                $query->where('customers.id', $customer_id);
            }
            $product_id = request()->get('product_id', null);
            if (!empty($product_id)) {
                $query->where('p.id', $product_id);
            }
            $location_id = request()->get('location_id', null);
            if (!empty($location_id)) {
                $query->where('purchase.location_id', $location_id);
            }
            $only_mfg_products = request()->get('only_mfg_products', 0);
            if (!empty($only_mfg_products)) {
                $query->where('purchase.type', 'production_purchase');
            }
            return Datatables::of($query)
                ->editColumn('product_name', function ($row) {
                    $product_name = $row->product_name;
                    if ($row->product_type == 'variable') {
                        $product_name .= ' - ' . $row->product_variation . ' - ' . $row->variation_name;
                    }
                    return $product_name;
                })
                ->addColumn('units', function ($row) use ($business_id) {
                    return $this->productUtil->getProductUnitsDropdown($row->product_id);
                })
                ->editColumn('purchase_date', '{{@format_datetime($purchase_date)}}')
                ->editColumn('purchase_ref_no', function ($row) {
                    $html = $row->purchase_type == 'purchase' ? '<a data-href="' . action('PurchaseController@show', [$row->purchase_line_id])
                        . '" href="#" data-container=".view_modal" class="btn-modal">' . $row->purchase_ref_no . '</a>' : $row->purchase_ref_no;
                    if ($row->purchase_type == 'opening_stock') {
                        $html .= '(' . __('lang_v1.opening_stock') . ')';
                    }
                    return $html;
                })
                ->editColumn('purchase_price', function ($row) use ($business_details) {
                    return '<span class="display_currency purchase_price" data-currency_symbol=true data-orig-value="' . $row->purchase_price . '">' . $this->productUtil->num_f($row->purchase_price, false, $business_details, false) . '</span>';
                })
                ->editColumn('sell_date', '@if(!empty($sell_line_id)) {{@format_datetime($sell_date)}} @else {{@format_datetime($stock_adjustment_date)}} @endif')
                ->editColumn('sale_invoice_no', function ($row) {
                    $invoice_no = !empty($row->sell_line_id) ? $row->sale_invoice_no : $row->stock_adjustment_ref_no . '<br><small>(' . __('stock_adjustment.stock_adjustment') . '</small>';
                    return $invoice_no;
                })
                ->editColumn('quantity', function ($row) {
                    $html = '<span data-is_quantity="true" class="display_currency quantity" data-currency_symbol=false data-orig-value="' . (float) $row->quantity . '" data-unit="' . $row->unit . '" >' . (float) $row->quantity . '</span> <span class="unit_name">' . $row->unit . '</span>';
                    if ($row->qty_returned > 0) {
                        $html .= '<small><i>(<span data-is_quantity="true" class="display_currency" data-currency_symbol=false>' . (float) $row->quantity . '</span> <span class="unit_name">' . $row->unit . '</span> ' . __('lang_v1.returned') . ')</i></small>';
                    }
                    return $html;
                })
                ->editColumn('selling_price', function ($row) use ($business_details) {
                    $selling_price = !empty($row->sell_line_id) ? $row->selling_price : $row->stock_adjustment_price;
                    return '<span class="display_currency row_selling_price selling_price" data-currency_symbol=true data-orig-value="' . $selling_price . '">' . $this->productUtil->num_f($selling_price, false, $business_details, false) . '</span>';
                })
                ->addColumn('subtotal', function ($row) {
                    $selling_price = !empty($row->sell_line_id) ? $row->selling_price : $row->stock_adjustment_price;
                    $subtotal = $selling_price * $row->quantity;
                    return '<span class="display_currency row_subtotal" data-currency_symbol=true data-orig-value="' . $subtotal . '">' . $subtotal . '</span>';
                })
                ->filterColumn('sale_invoice_no', function ($query, $keyword) {
                    $query->where('sale.invoice_no', 'like', ["%{$keyword}%"])
                        ->orWhere('stock_adjustment.ref_no', 'like', ["%{$keyword}%"]);
                })
                ->rawColumns(['subtotal', 'selling_price', 'quantity', 'purchase_price', 'sale_invoice_no', 'purchase_ref_no', 'units'])
                ->make(true);
        }
        $rack_enabled = (request()->session()->get('business.enable_racks') || request()->session()->get('business.enable_row') || request()->session()->get('business.enable_position'));
        $suppliers = Contact::suppliersDropdown($business_id, false);
        $customers = Contact::customersDropdown($business_id, false);
        $business_locations = BusinessLocation::forDropdown($business_id);
        $products = Product::where('business_id', $business_id)->pluck('name', 'id');
        return view('report.items_report')->with(compact('products', 'suppliers', 'customers', 'business_locations', 'rack_enabled'));
    }
    public function getOutstandingReport(Request $request)
    {
        if (!auth()->user()->can('outstanding_received_report.view')) {
            abort(403, 'Unauthorized action.');
        }
        $business_id = request()->session()->get('user.business_id');
        $business_details = Business::find($business_id);
        if (request()->ajax()) {
            $sells = Transaction::leftJoin('contacts', 'transactions.contact_id', '=', 'contacts.id')
                ->leftJoin('transaction_payments as tp', 'transactions.id', '=', 'tp.transaction_id')
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
                    'tp.id as tp_id',
                    'tp.paid_on',
                    'tp.method',
                    'tp.parent_id',
                    'tp.cheque_number',
                    'tp.card_number',
                    'tp.payment_ref_no',
                    DB::raw('SUM(tp.amount) as total_paid')
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
            if (!empty(request()->payment_type)) {
                $sells->where('tp.method', request()->payment_type);
            }
            if (!empty(request()->start_date) && !empty(request()->end_date)) {
                $start = request()->start_date;
                $end =  request()->end_date;
                $sells->whereDate('tp.paid_on', '>=', $start)
                    ->whereDate('tp.paid_on', '<=', $end);
            }
            $sells->orderBy('tp.paid_on', 'desc')->groupBy('tp.id');
            $datatable = Datatables::of($sells)
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
                ->addColumn('payment_amount', function ($row) use ($business_id, $business_details) {
                    if (!empty($row->parent_id)) {
                        $parent_payment = TransactionPayment::where('id', $row->parent_id)->first();
                        if (!empty($parent_payment)) {
                            return '<span class="display_currency final-total" data-currency_symbol="true" data-orig-value="' . $parent_payment->amount . '">' . $this->productUtil->num_f($parent_payment->amount, false, $business_details, false) . '</span>';
                        } else {
                            return '<span class="display_currency final-total" data-currency_symbol="true" data-orig-value="' . $row->total_paid . '">' . $this->productUtil->num_f($row->total_paid, false, $business_details, false) . '</span>';
                        }
                    } else if ($row->method == 'cheque') {
                        $amount = TransactionPayment::where('business_id', $business_id)->where('cheque_number', $row->cheque_number)->sum('amount');
                        return '<span class="display_currency final-total" data-currency_symbol="true" data-orig-value="' . $amount . '">' . $this->productUtil->num_f($amount, false, $business_details, false) . '</span>';
                    } else {
                        return '<span class="display_currency final-total" data-currency_symbol="true" data-orig-value="' . $row->total_paid . '">' . $this->productUtil->num_f($row->total_paid, false, $business_details, false) . '</span>';
                    }
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
                ->setRowAttr([
                    'data-href' => function ($row) {
                        if (auth()->user()->can("sell.view") || auth()->user()->can("view_own_sell_only")) {
                            return  action('SellController@show', [$row->id]);
                        } else {
                            return '';
                        }
                    }
                ]);
            $rawColumns = ['final_total', 'action', 'total_paid', 'total_remaining', 'payment_status', 'invoice_no', 'discount_amount', 'tax_amount', 'total_before_tax', 'shipping_status', 'payment_amount'];
            return $datatable->rawColumns($rawColumns)
                ->make(true);
        }
    }
    public function getAgingReport(Request $request)
    {
        if (!auth()->user()->can('aging_report.view')) {
            abort(403, 'Unauthorized action.');
        }
        $business_id = $request->session()->get('user.business_id');
        if (request()->ajax()) {
            $sells = Transaction::leftJoin('contacts', 'transactions.contact_id', '=', 'contacts.id')
                ->leftJoin('transaction_payments as tp', 'transactions.id', '=', 'tp.transaction_id')
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
                ->where('transactions.business_id', $business_id)
                ->where('transactions.type', 'sell')
                ->whereIn('transactions.payment_status', ['due', 'partial'])
                ->select(
                    'transactions.id',
                    'contacts.name',
                    'transactions.final_total',
                    'transactions.transaction_date',
                    DB::raw('(SELECT DATEDIFF(NOW(), transactions.transaction_date)) as days_over'),
                    DB::raw('SUM(IF(tp.is_return = 1,-1*tp.amount,tp.amount)) as total_paid'),
                    'bl.name as business_location',
                    DB::raw('COUNT(SR.id) as return_exists'),
                    DB::raw('(SELECT SUM(TP2.amount) FROM transaction_payments AS TP2 WHERE
                        TP2.transaction_id=SR.id ) as return_paid'),
                    DB::raw('COALESCE(SR.final_total, 0) as amount_return'),
                    'SR.id as return_transaction_id'
                );
            $permitted_locations = auth()->user()->permitted_locations();
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
            $sells->groupBy('transactions.id')->OrderBY('transactions.transaction_date', 'desc');
            $business_details = Business::find($business_id);
            $datatable = Datatables::of($sells)
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
                        }
                        $html .= '<li><a href="#" class="print-invoice" data-href="' . route('sell.printInvoice', [$row->id]) . '"><i class="fa fa-print" aria-hidden="true"></i> ' . __("messages.print") . '</a></li>';
                        $html .= '</ul></div>';
                        return $html;
                    }
                )
                ->removeColumn('id')
                ->editColumn('final_total', function ($row) use ($business_details) {
                    $final = $row->final_total - $row->total_paid;
                    $final_total = '<span class="display_currency final-total-aging" data-currency_symbol="true" data-orig-value="' . $final . '">' . $this->util->num_f($final, false, $business_details, false) . '</span>';
                    return $final_total;
                })
                ->editColumn('total_paid', function ($row) use ($business_details) {
                    if ($row->total_paid == '') {
                        $total_paid_html = '<span class="display_currency total-paid" data-currency_symbol="true" data-orig-value="0.00">0.00</span>';
                    } else {
                        $total_paid_html = '<span class="display_currency total-paid" data-currency_symbol="true" data-orig-value="' . $row->total_paid . '">' . $this->util->num_f($row->total_paid, false, $business_details, false) . '</span>';
                    }
                    return $total_paid_html;
                })
                ->editColumn('transaction_date', '{{@format_date($transaction_date)}}')
                ->addColumn('1_30_days', function ($row) use ($business_details) {
                    $final = $row->final_total - $row->total_paid;
                    $final_total = '<span class="display_currency 1_30_days-aging" data-currency_symbol="true" data-orig-value="' . $final . '">' . $this->util->num_f($final, false, $business_details, false) . '</span>';
                    if ($row->days_over >= 1 && $row->days_over <= 30) {
                        return $final_total;
                    }
                    return null;
                })
                ->addColumn('31_45_days', function ($row) use ($business_details) {
                    $final = $row->final_total - $row->total_paid;
                    $final_total = '<span class="display_currency 31_45_days-aging" data-currency_symbol="true" data-orig-value="' . $final . '">' . $this->util->num_f($final, false, $business_details, false) . '</span>';
                    if ($row->days_over >= 31 && $row->days_over <= 45) {
                        return $final_total;
                    }
                    return null;
                })
                ->addColumn('46_60_days', function ($row) use ($business_details) {
                    $final = $row->final_total - $row->total_paid;
                    $final_total = '<span class="display_currency 46_60_days-aging" data-currency_symbol="true" data-orig-value="' . $final . '">' . $this->util->num_f($final, false, $business_details, false) . '</span>';
                    if ($row->days_over >= 46 && $row->days_over <= 60) {
                        return $final_total;
                    }
                    return null;
                })
                ->addColumn('61_90_days', function ($row) use ($business_details) {
                    $final = $row->final_total - $row->total_paid;
                    $final_total = '<span class="display_currency 61_90_days-aging" data-currency_symbol="true" data-orig-value="' . $final . '">' . $this->util->num_f($final, false, $business_details, false) . '</span>';
                    if ($row->days_over >= 61 && $row->days_over <= 90) {
                        return $final_total;
                    }
                    return null;
                })
                ->addColumn('over_90_days', function ($row) use ($business_details) {
                    $final = $row->final_total - $row->total_paid;
                    $final_total = '<span class="display_currency over_90_days-aging" data-currency_symbol="true" data-orig-value="' . $final . '">' . $this->util->num_f($final, false, $business_details, false) . '</span>';
                    if ($row->days_over >= 91) {
                        return $final_total;
                    }
                    return null;
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
            $rawColumns = [
                'days_over',
                'final_total',
                'action',
                '1_30_days',
                '31_45_days',
                '46_60_days',
                '61_90_days',
                'over_90_days'
            ];
            return $datatable->rawColumns($rawColumns)
                ->make(true);
        }
    }
    public function getDailySummaryReport(Request $request)
    {
        if (!auth()->user()->can('daily_summary_report.view')) {
            abort(403, 'Unauthorized action.');
        }
        $business_id = $request->session()->get('business.id');
        $business_locations = BusinessLocation::forDropdown($business_id);
        $petro_module = $this->moduleUtil->hasThePermissionInSubscription($business_id, 'enable_petro_module');
        $work_shifts = WorkShift::where('business_id', $business_id)->pluck('shift_name', 'id');
        $default_start = new Carbon('first day of this month');
        $default_end = new Carbon('last day of this month');
        $start_date = !empty($request->start_date) ? Carbon::parse($request->start_date)->format('Y-m-d') : $default_start->format('Y-m-d');
        $end_date = !empty($request->end_date) ? Carbon::parse($request->end_date)->format('Y-m-d') : $default_end->format('Y-m-d');
        $day_diff = !empty($request->start_date) && !empty($request->end_date) ? Carbon::parse($request->start_date)->diffInDays(Carbon::parse($request->end_date)) : 0;
        $location_id = $request->location_id;
        $work_shift_id = $request->work_shift;
        $location_details = '';
        $work_shift = '';
        if (!empty($location_id)) {
            $location_details = BusinessLocation::where('id', $location_id)->first();
        }
        if (!empty($work_shift_id)) {
            $work_shift = WorkShift::where('id', $work_shift_id)->first()->shift_name;
        }
        //pump operator table
        $commsn_agnt_setting = $request->session()->get('business.sales_cmsn_agnt');
        $pump_operator_sales_query = PumpOperator::leftjoin('settlements', 'pump_operators.id', 'settlements.pump_operator_id')
            ->leftjoin('transactions', 'settlements.settlement_no', 'transactions.invoice_no')
            ->where('transactions.status', 'final')
            ->where('transactions.is_settlement', '1')
            ->where('transactions.business_id', $business_id)
            ->where('pump_operators.business_id', $business_id)
            ->whereDate('transactions.transaction_date', '>=', $start_date)
            ->whereDate('transactions.transaction_date', '<=', $end_date);
        if (!empty($work_shift_id)) {
            $pump_operator_sales_query->whereJsonContains('settlements.work_shift', $work_shift_id);
        }
        $pump_operator_sales = $pump_operator_sales_query->select(
            'pump_operators.name as pump_operator_name',
            DB::raw('GROUP_CONCAT(DISTINCT  settlements.settlement_no SEPARATOR ", ") as settlement_nos'),
            DB::raw("SUM(IF(transactions.type = 'sell' AND transactions.payment_status = 'due' , transactions.final_total, 0)) as credit_sale_total"),
            DB::raw("SUM(IF(transactions.type = 'settlement' AND transactions.sub_type = 'cash_payment', transactions.final_total, 0)) as cash_total"),
            DB::raw("SUM(IF(transactions.type = 'settlement' AND transactions.sub_type = 'cheque_payment', transactions.final_total, 0)) as cheque_total"),
            DB::raw("SUM(IF(transactions.type = 'settlement' AND transactions.sub_type = 'card_payment', transactions.final_total, 0)) as card_total"),
            DB::raw("SUM(IF(transactions.type = 'settlement' AND transactions.sub_type = 'shortage', transactions.final_total, 0)) as shortage_amount"),
            DB::raw("SUM(IF(transactions.type = 'settlement' AND transactions.sub_type = 'excess', transactions.final_total, 0)) as excess_amount"),
            DB::raw("SUM(IF(transactions.type = 'settlement' AND transactions.sub_type = 'expense', transactions.final_total, 0)) as expense_amount"),
        )->groupBy(['pump_operators.id'])->get();
        $cashiers = Transaction::leftjoin('users', 'transactions.commission_agent', 'users.id')
            ->leftjoin('transaction_payments', 'transactions.id', 'transaction_payments.transaction_id')
            ->where('transactions.status', 'final')
            ->where('transactions.type', 'sell')
            ->where('transactions.is_settlement', '0')
            ->whereDate('transaction_date', '>=', $start_date)
            ->whereDate('transaction_date', '<=', $end_date)
            ->where('transactions.business_id', $business_id)
            ->select(
                'users.username as cashier_name',
                DB::raw("SUM(IF(transaction_payments.method = 'cash', transaction_payments.amount, 0)) as cash_total"),
                DB::raw("SUM(IF(transaction_payments.method = 'cheque', transaction_payments.amount, 0)) as cheque_total"),
                DB::raw("SUM(IF(transaction_payments.method = 'card', transaction_payments.amount, 0)) as card_total"),
                DB::raw("SUM(IF(transactions.type = 'sell' AND transactions.payment_status = 'due' , transactions.final_total, 0)) as credit_sale_total"),
                DB::raw("SUM(IF(transactions.type = 'expense' , transactions.final_total, 0)) as expense_total")
            )->groupBy('transactions.commission_agent')->get();
        //finance section
        $sell = Transaction::leftjoin('transaction_payments', 'transactions.id', 'transaction_payments.transaction_id')
            ->where('transactions.status', 'final')
            ->where('transactions.type', 'sell')
            ->whereDate('transaction_date', '>=', $start_date)
            ->whereDate('transaction_date', '<=', $end_date)
            ->where('transactions.business_id', $business_id)
            ->select(
                DB::raw("SUM(IF(transaction_payments.method = 'cash', transaction_payments.amount, 0)) as cash_total"),
                DB::raw("SUM(IF(transaction_payments.method = 'cheque', transaction_payments.amount, 0)) as cheque_total"),
                DB::raw("SUM(IF(transaction_payments.method = 'card', transaction_payments.amount, 0)) as card_total"),
                DB::raw("SUM(IF(transactions.payment_status = 'due' , transactions.final_total, 0)) as credit_sale_total")
            )
            ->first();
        $received_query = Transaction::leftjoin('transaction_payments', 'transactions.id', 'transaction_payments.transaction_id')
            ->where('transactions.business_id', $business_id)
            ->whereIn('transactions.type', ['sell', 'purchase_return'])
            ->whereIn('transactions.payment_status', ['paid', 'partial', 'due'])
            ->where('is_settlement', 0)
            ->whereDate('transaction_date', '>=', $start_date)
            ->whereDate('transaction_date', '<=', $end_date);
        if (!empty($location_id)) {
            $received_query->where('transactions.location_id', $location_id);
        }
        $received_result = $received_query->select(
            DB::raw('SUM(IF(transaction_payments.method="cash", transaction_payments.amount, 0)) as cash'),
            DB::raw('SUM(IF(transaction_payments.method="card", transaction_payments.amount, 0)) as card'),
            DB::raw('SUM(IF(transaction_payments.method="cheque", transaction_payments.amount, 0)) as cheque'),
            DB::raw('SUM(IF(transactions.payment_status="due" AND transactions.type="sell", transactions.final_total, 0)) as credit_sale')
        )->first();
        $settlement_received_query = Transaction::leftjoin('transaction_payments', 'transactions.id', 'transaction_payments.transaction_id')
            ->where('transactions.business_id', $business_id)
            ->whereIn('transactions.type', ['settlement', 'sell'])
            ->where('is_settlement', 1)
            ->whereDate('transaction_date', '>=', $start_date)
            ->whereDate('transaction_date', '<=', $end_date);
        if (!empty($location_id)) {
            $settlement_received_query->where('transactions.location_id', $location_id);
        }
        $settlement_received_result = $settlement_received_query->select(
            DB::raw('SUM(IF(transactions.sub_type="cash_payment", transactions.final_total, 0)) as cash'),
            DB::raw('SUM(IF(transactions.sub_type="card_payment", transactions.final_total, 0)) as card'),
            DB::raw('SUM(IF(transactions.sub_type="cheque_payment", transactions.final_total, 0)) as cheque'),
            DB::raw('SUM(IF(transactions.sub_type="credit_sale" , transactions.final_total, 0)) as credit_sale')
        )->first();
        $received['cash'] = $received_result->cash + $settlement_received_result->cash;
        $received['card'] = $received_result->card + $settlement_received_result->card;
        $received['cheque'] = $received_result->cheque + $settlement_received_result->cheque;
        $received['credit_sale'] = $received_result->credit_sale + $settlement_received_result->credit_sale;
        $shortage_query = Transaction::leftjoin('transaction_payments', 'transactions.id', 'transaction_payments.transaction_id')
            ->where('transactions.business_id', $business_id)->where('type', 'settlement')->where('sub_type', 'shortage')
            ->whereIn('transactions.payment_status', ['paid', 'partial'])
            ->whereDate('transaction_date', '>=', $start_date)
            ->whereDate('transaction_date', '<=', $end_date);
        if (!empty($location_id)) {
            $shortage_query->where('transactions.location_id', $location_id);
        }
        $shortage_result = $shortage_query->select(
            DB::raw('SUM(IF(transaction_payments.method="cash", transaction_payments.amount, 0)) as cash'),
            DB::raw('SUM(IF(transaction_payments.method="card", transaction_payments.amount, 0)) as card'),
            DB::raw('SUM(IF(transaction_payments.method="cheque", transaction_payments.amount, 0)) as cheque'),
            DB::raw('SUM(IF(transaction_payments.method="credit_sale", transaction_payments.amount, 0)) as credit_sale')
        )->first();
        $shortage_recover['cash'] = $shortage_result->cash;
        $shortage_recover['card'] = $shortage_result->card;
        $shortage_recover['cheque'] = $shortage_result->cheque;
        $shortage_recover['credit_sale'] = $shortage_result->credit_sale;
        $shortage_total =  $shortage_recover['cash'] +  $shortage_recover['card'] +  $shortage_recover['cheque'] +  $shortage_recover['credit_sale'];
        $excess_commission_query = Transaction::leftjoin('transaction_payments', 'transactions.id', 'transaction_payments.transaction_id')
            ->where('transactions.business_id', $business_id)->where('type', 'settlement')->where('sub_type', 'excess')
            ->whereIn('transactions.payment_status', ['paid', 'partial'])
            ->whereDate('transaction_date', '>=', $start_date)
            ->whereDate('transaction_date', '<=', $end_date);
        if (!empty($location_id)) {
            $excess_commission_query->where('transactions.location_id', $location_id);
        }
        $excess_result = $excess_commission_query->select(
            DB::raw('SUM(IF(transaction_payments.method="cash", transaction_payments.amount, 0)) as cash'),
            DB::raw('SUM(IF(transaction_payments.method="card", transaction_payments.amount, 0)) as card'),
            DB::raw('SUM(IF(transaction_payments.method="cheque", transaction_payments.amount, 0)) as cheque'),
            DB::raw('SUM(IF(transaction_payments.method="credit_sale", transaction_payments.amount, 0)) as credit_sale')
        )->first();
        $excess_commission['cash'] = $excess_result->cash;
        $excess_commission['card'] = $excess_result->card;
        $excess_commission['cheque'] = $excess_result->cheque;
        $excess_commission['credit_sale'] = $excess_result->credit_sale;
        $expense_query = Transaction::where('transactions.business_id', $business_id)->where('type', 'expense')->where('payment_status', 'paid')
            ->leftjoin('transaction_payments', 'transactions.id', 'transaction_payments.transaction_id')
            ->whereDate('transaction_date', '>=', $start_date)
            ->whereDate('transaction_date', '<=', $end_date);
        if (!empty($location_id)) {
            $expense_query->where('transactions.location_id', $location_id);
        }
        $direct_expenses =  $expense_query->select(
            DB::raw("SUM(IF(transaction_payments.method = 'cash', transaction_payments.amount, 0)) as cash_total"),
            DB::raw("SUM(IF(transaction_payments.method = 'cheque', transaction_payments.amount, 0)) as cheque_total"),
            DB::raw("SUM(IF(transaction_payments.method = 'card', transaction_payments.amount, 0)) as card_total")
        )->first();
        $deposit = array(
            'cash' => 0.00,
            'cheque' => 0.00,
            'card' => 0.00,
        );
        $cash_account = $this->transactionUtil->account_exist_return_id('Cash');
        $cheque_account = $this->transactionUtil->account_exist_return_id('Cheques in Hand');
        $card_account = $this->transactionUtil->account_exist_return_id('Cards');
        $deposit['cash'] = AccountTransaction::where('account_id', $cash_account)
            ->whereDate('operation_date', '>=', $start_date)
            ->whereDate('operation_date', '<=', $end_date)
            ->whereIn('sub_type', ['deposit', 'fund_transfer'])->sum('amount');
        $deposit['cheque'] = AccountTransaction::where('account_id', $cheque_account)
            ->whereDate('operation_date', '>=', $start_date)
            ->whereDate('operation_date', '<=', $end_date)
            ->whereIn('sub_type', ['deposit', 'fund_transfer'])->sum('amount');
        $deposit['card'] = AccountTransaction::where('account_id', $card_account)
            ->whereDate('operation_date', '>=', $start_date)
            ->whereDate('operation_date', '<=', $end_date)
            ->whereIn('sub_type', ['deposit', 'fund_transfer'])->sum('amount');
        $withdrawal['cash'] = AccountTransaction::where('account_id', $cash_account)->where('type', 'debit')
            ->whereDate('operation_date', '<', $start_date)
            ->where('sub_type', 'deposit')->sum('amount');
        $withdrawal['cheque'] = AccountTransaction::where('account_id', $cheque_account)->where('type', 'debit')
            ->whereDate('operation_date', '<', $start_date)
            ->where('sub_type', 'deposit')->sum('amount');
        $withdrawal['card'] = AccountTransaction::where('account_id', $card_account)->where('type', 'debit')
            ->whereDate('operation_date', '<', $start_date)
            ->where('sub_type', 'deposit')->sum('amount');
        $purchase_by_cash = Transaction::leftjoin('transaction_payments', 'transactions.id', 'transaction_payments.transaction_id')
            ->where('transactions.business_id', $business_id)->where('type', 'purchase')->where('payment_status', 'paid')->where('transaction_payments.method', 'cash')
            ->whereDate('transaction_date', '>=', $start_date)
            ->whereDate('transaction_date', '<=', $end_date);
        if (!empty($location_id)) {
            $purchase_by_cash->where('transactions.location_id', $location_id);
        }
        $purchase_by_cash = $purchase_by_cash->sum('final_total');
        $outstandings = $this->getPreviousDayOutstanding($start_date, $end_date, $location_id, $work_shift);
        $pre_day_balance = $this->previousDayFinanceSummary($start_date, $location_id);
        $pre_day_balance['credit_sale'] = $outstandings['previous_day'];
        $balance['cash'] =  $pre_day_balance['cash'] + $sell->cash_total + $received['cash'] + $shortage_recover['cash'] + $withdrawal['cash'] -  $excess_commission['cash'] - $direct_expenses->cash_total - $deposit['cash'];
        $balance['cheque'] =  $pre_day_balance['cheque'] + $sell->cheque_total + $received['cheque'] + $shortage_recover['cheque'] + $withdrawal['cheque'] -  $excess_commission['cheque'] - $direct_expenses->cheque_total - $deposit['cheque'];
        $balance['card'] =  $pre_day_balance['card'] + $sell->card_total + $received['card'] + $shortage_recover['card'] + $withdrawal['card'] -  $excess_commission['card'] - $direct_expenses->card_total - $deposit['card'];
        $balance['credit_sale'] =  $pre_day_balance['credit_sale'] + $sell->credit_sale_total + $received['credit_sale'] + $shortage_recover['credit_sale'] -  $excess_commission['credit_sale'];
        $categories = Category::where('business_id', $business_id)->where('parent_id', '!=', '0')->select('id', 'name', 'parent_id')->get();
        $stock_qty = array();
        foreach ($categories as $cat) {
            $stock_qty[$cat->id]['opening_stock'] = $this->transactionUtil->getStockForSubCateogryByTransactionType('opening_stock', $cat->id, $start_date, $end_date, false, true);
            $stock_qty[$cat->id]['total_sold'] = $this->transactionUtil->getStockForSubCateogryByTransactionType('sell', $cat->id, $start_date, $end_date, false, true);
            $stock_qty[$cat->id]['purchase_stock'] =  $this->transactionUtil->getStockForSubCateogryByTransactionType('purchase', $cat->id, $start_date, $end_date, false, true);
            $stock_qty[$cat->id]['stock_adjusted'] = $this->transactionUtil->getStockForSubCateogryByTransactionType('stock_adjustment', $cat->id, $start_date, $end_date, false, true);
            $stock_qty[$cat->id]['preday_total_sold'] = $this->transactionUtil->getStockForSubCateogryByTransactionType('sell', $cat->id, $start_date, $end_date, true, true);
            $stock_qty[$cat->id]['preday_purchase_stock'] = $stock_qty[$cat->id]['opening_stock'] + $this->transactionUtil->getStockForSubCateogryByTransactionType('purchase', $cat->id, $start_date, $end_date, true, true);
            $stock_qty[$cat->id]['preday_stock_adjusted'] = $this->transactionUtil->getStockForSubCateogryByTransactionType('stock_adjustment', $cat->id, $start_date, $end_date, true, true);
            $stock_qty[$cat->id]['category_name'] =   Category::where('business_id', $business_id)->where('id', $cat->parent_id)->first()->name;
            $stock_qty[$cat->id]['sub_category_name'] =  $cat->name;
        }
        $stock_values = array();
        foreach ($categories as $category) {
            $stock_values[$category->id]['opening_stock'] = Account::getStockGroupAccountBalanceByTransactionTypeAndCategory('opening_stock', $category->id, $start_date, $end_date, true);
            $stock_values[$category->id]['total_sold'] = Account::getStockGroupAccountBalanceByTransactionTypeAndCategory('sell', $category->id, $start_date, $end_date);
            $stock_values[$category->id]['purchase_stock'] =  Account::getStockGroupAccountBalanceByTransactionTypeAndCategory('purchase', $category->id, $start_date, $end_date);
            $stock_values[$category->id]['stock_adjusted'] =  Account::getStockGroupAccountBalanceByTransactionTypeAndCategory('stock_adjustment', $category->id, $start_date, $end_date);
            $stock_values[$category->id]['preday_total_sold'] = Account::getStockGroupAccountBalanceByTransactionTypeAndCategory('sell', $category->id, $start_date, $end_date, true);
            $stock_values[$category->id]['preday_purchase_stock'] = $stock_values[$category->id]['opening_stock'] +  Account::getStockGroupAccountBalanceByTransactionTypeAndCategory('purchase', $category->id, $start_date, $end_date, true);
            $stock_values[$category->id]['preday_stock_adjusted'] = Account::getStockGroupAccountBalanceByTransactionTypeAndCategory('stock_adjustment', $category->id, $start_date, $end_date, true);
            $stock_values[$category->id]['category_name'] =  Category::where('business_id', $business_id)->where('id', $category->parent_id)->first()->name;
            $stock_values[$category->id]['sub_category_name'] =  $category->name;
        }
        return view('report.daily_summary_report')->with(compact(
            'business_locations',
            'petro_module',
            'day_diff',
            'start_date',
            'end_date',
            'work_shifts',
            'work_shift',
            'work_shift_id',
            'location_id',
            'location_details',
            'pump_operator_sales',
            'cashiers',
            'sell',
            'received',
            'shortage_recover',
            'excess_commission',
            'withdrawal',
            'direct_expenses',
            'deposit',
            'purchase_by_cash',
            'pre_day_balance',
            'balance',
            'stock_qty',
            'stock_values'
        ));
    }
    public function getSummaryStockQuery($category_id, $location_id)
    {
        $business_id = request()->session()->get('business.id');
        $query = Transaction::leftjoin('purchase_lines as pl', 'transactions.id', 'pl.transaction_id')
            ->leftjoin('transaction_sell_lines as tsl', 'transactions.id', 'tsl.transaction_id')
            ->leftjoin('products as p', function ($join) {
                $join->on('pl.product_id', 'p.id')->orOn('tsl.product_id', 'p.id');
            })
            ->leftjoin('product_variations', 'p.id', 'product_variations.product_id')
            ->leftjoin('variations', 'product_variations.id', 'variations.product_variation_id')
            ->leftjoin('categories as cat', 'p.category_id', '=', 'cat.id')
            ->where('transactions.business_id', $business_id)
            ->where(function ($q) {
                $q->where('transactions.sub_type', '=', 'settlement')->orWhereNull('transactions.sub_type');
            })
            ->where('p.sub_category_id', $category_id);
        if (!empty($location_id)) {
            $query->where('transactions.location_id', $location_id);
        }
        $query->groupBy('p.sub_category_id');
        return $query;
    }
    public function getSummaryStockSellValueQuery($category_id, $location_id)
    {
        $business_id = request()->session()->get('business.id');
        $query = Transaction::leftjoin('transaction_sell_lines as tsl', 'transactions.id', 'tsl.transaction_id')
            ->leftjoin('products as p', function ($join) {
                $join->on('tsl.product_id', 'p.id');
            })
            ->leftjoin('product_variations', 'p.id', 'product_variations.product_id')
            ->leftjoin('variations', 'product_variations.id', 'variations.product_variation_id')
            ->leftjoin('categories as cat', 'p.category_id', '=', 'cat.id')
            ->where('transactions.business_id', $business_id)
            ->where(function ($q) {
                $q->where('transactions.sub_type', '=', 'settlement')->orWhereNull('transactions.sub_type');
            })
            ->where('p.sub_category_id', $category_id);
        if (!empty($location_id)) {
            $query->where('transactions.location_id', $location_id);
        }
        $query->groupBy(['p.sub_category_id']);
        return $query;
    }
    public function getSummaryStockPurchaseValueQuery($category_id, $location_id)
    {
        $business_id = request()->session()->get('business.id');
        $query = Transaction::leftjoin('purchase_lines as pl', 'transactions.id', 'pl.transaction_id')
            ->leftjoin('products as p', function ($join) {
                $join->on('pl.product_id', 'p.id');
            })
            ->leftjoin('product_variations', 'p.id', 'product_variations.product_id')
            ->leftjoin('variations', 'product_variations.id', 'variations.product_variation_id')
            ->leftjoin('categories as cat', 'p.category_id', '=', 'cat.id')
            ->where(function ($q) {
                $q->where('transactions.sub_type', '=', 'settlement')->orWhereNull('transactions.sub_type');
            })
            ->where('transactions.business_id', $business_id)
            ->where('p.sub_category_id', $category_id);
        if (!empty($location_id)) {
            $query->where('transactions.location_id', $location_id);
        }
        $query->groupBy('p.sub_category_id');
        return $query;
    }
    public function previousDayFinanceSummary($start_date, $location_id)
    {
        $business_id = request()->session()->get('user.business_id');
        // outstanding received
        $received_outstanding_query = Transaction::leftjoin('transaction_payments', 'transactions.id', 'transaction_payments.transaction_id')
            ->where('transactions.business_id', $business_id)
            ->where(function ($q) {
                $q->where('is_credit_sale', 1);
            })
            ->whereIn('transactions.payment_status', ['paid', 'partial'])
            ->whereIn('transaction_payments.method', ['cash', 'cheque', 'card'])
            ->whereDate('transaction_date', '<', $start_date);
        if (!empty($location_id)) {
            $received_outstanding_query->where('transactions.location_id', $location_id);
        }
        $total_received_outstanding =  $received_outstanding_query->select(
            DB::raw('SUM(IF(transaction_payments.method="cash", transaction_payments.amount, 0)) as cash'),
            DB::raw('SUM(IF(transaction_payments.method="card", transaction_payments.amount, 0)) as card'),
            DB::raw('SUM(IF(transaction_payments.method="cheque", transaction_payments.amount, 0)) as cheque'),
            DB::raw('SUM(transaction_payments.amount) as total_amount')
        )->first();
        // recieved amount with out settlement sale // related to pos sales
        $received_query = Transaction::leftjoin('transaction_payments', 'transactions.id', 'transaction_payments.transaction_id')
            ->where('transactions.business_id', $business_id)
            ->whereIn('transactions.type', ['sell', 'purchase_return'])
            ->whereIn('transactions.payment_status', ['paid', 'partial', 'due'])
            ->where('is_settlement', 0)
            ->where('transaction_date', '<', $start_date);
        if (!empty($location_id)) {
            $received_query->where('transactions.location_id', $location_id);
        }
        $received_result = $received_query->select(
            DB::raw('SUM(IF(transaction_payments.method="cash", transaction_payments.amount, 0)) as cash'),
            DB::raw('SUM(IF(transaction_payments.method="card", transaction_payments.amount, 0)) as card'),
            DB::raw('SUM(IF(transaction_payments.method="cheque", transaction_payments.amount, 0)) as cheque'),
            DB::raw('SUM(IF(transactions.payment_status="due" AND transactions.type="sell", transactions.final_total, 0)) as credit_sale')
        )->first();
        // recieved amount settlement sale 
        $settlement_received_query = Transaction::leftjoin('transaction_payments', 'transactions.id', 'transaction_payments.transaction_id')
            ->where('transactions.business_id', $business_id)
            ->whereIn('transactions.type', ['settlement', 'sell'])
            ->where('is_settlement', 1)
            ->where('transaction_date', '<', $start_date);
        if (!empty($location_id)) {
            $settlement_received_query->where('transactions.location_id', $location_id);
        }
        $settlement_received_result = $settlement_received_query->select(
            DB::raw('SUM(IF(transactions.sub_type="cash_payment", transactions.final_total, 0)) as cash'),
            DB::raw('SUM(IF(transactions.sub_type="card_payment", transactions.final_total, 0)) as card'),
            DB::raw('SUM(IF(transactions.sub_type="cheque_payment", transactions.final_total, 0)) as cheque'),
            DB::raw('SUM(IF(transactions.sub_type="credit_sale", transactions.final_total, 0)) as credit_sale')
        )->first();
        $received['cash'] = $received_result->cash + $settlement_received_result->cash + $total_received_outstanding->cash;
        $received['card'] = $received_result->card + $settlement_received_result->card + $total_received_outstanding->card;
        $received['cheque'] = $received_result->cheque + $settlement_received_result->cheque + $total_received_outstanding->cheque;
        $received['credit_sale'] = $received_result->credit_sale + $settlement_received_result->credit_sale;
        $shortage_query = Transaction::leftjoin('transaction_payments', 'transactions.id', 'transaction_payments.transaction_id')
            ->where('transactions.business_id', $business_id)->where('type', 'settlement')->where('sub_type', 'shortage')
            ->whereDate('transactions.transaction_date', '<', $start_date);
        if (!empty($location_id)) {
            $shortage_query->where('transactions.location_id', $location_id);
        }
        $shortage_recover['cash'] = $shortage_query->where('transaction_payments.method', 'cash')->sum('final_total');
        $shortage_recover['card'] = $shortage_query->where('transaction_payments.method', 'card')->sum('final_total');
        $shortage_recover['cheque'] = $shortage_query->where('transaction_payments.method', 'cheque')->sum('final_total');
        $shortage_recover['credit_sale'] = $shortage_query->where('transactions.is_credit_sale', '1')->sum('final_total');
        $excess_commission_query = Transaction::leftjoin('transaction_payments', 'transactions.id', 'transaction_payments.transaction_id')
            ->where('transactions.business_id', $business_id)->where('type', 'settlement')->where('sub_type', 'excess')
            ->whereDate('transactions.transaction_date', '<', $start_date);
        if (!empty($location_id)) {
            $excess_commission_query->where('transactions.location_id', $location_id);
        }
        $excess_commission['cash'] = $excess_commission_query->where('transaction_payments.method', 'cash')->sum('final_total');
        $excess_commission['card'] = $excess_commission_query->where('transaction_payments.method', 'card')->sum('final_total');
        $excess_commission['cheque'] = $excess_commission_query->where('transaction_payments.method', 'cheque')->sum('final_total');
        $excess_commission['credit_sale'] = $excess_commission_query->where('transactions.is_credit_sale', '1')->sum('final_total');
        $expense_query = Transaction::where('transactions.business_id', $business_id)->where('type', 'expense')->where('payment_status', 'paid')
            ->leftjoin('transaction_payments', 'transactions.id', 'transaction_payments.transaction_id')
            ->whereDate('transactions.transaction_date', '<', $start_date);
        if (!empty($location_id)) {
            $expense_query->where('transactions.location_id', $location_id);
        }
        $direct_expenses =  $expense_query->select(
            DB::raw("SUM(IF(transaction_payments.method = 'cash', transaction_payments.amount, 0)) as cash_total"),
            DB::raw("SUM(IF(transaction_payments.method = 'cheque', transaction_payments.amount, 0)) as cheque_total"),
            DB::raw("SUM(IF(transaction_payments.method = 'card', transaction_payments.amount, 0)) as card_total")
        )->first();
        $deposit = array(
            'cash' => 0.00,
            'cheque' => 0.00,
            'card' => 0.00,
        );
        $cash_account = $this->transactionUtil->account_exist_return_id('Cash');
        $cheque_account = $this->transactionUtil->account_exist_return_id('Cheques in Hand');
        $card_account = $this->transactionUtil->account_exist_return_id('Cards (Credit Debit) Account');
        $deposit['cash'] = AccountTransaction::where('account_id', $cash_account)
            ->whereDate('operation_date', '<', $start_date)
            ->whereIn('sub_type', ['deposit', 'fund_transfer'])->sum('amount');
        $deposit['cheque'] = AccountTransaction::where('account_id', $cheque_account)
            ->whereDate('operation_date', '<', $start_date)
            ->whereIn('sub_type', ['deposit', 'fund_transfer'])->sum('amount');
        $deposit['card'] = AccountTransaction::where('account_id', $card_account)
            ->whereDate('operation_date', '<', $start_date)
            ->whereIn('sub_type', ['deposit', 'fund_transfer'])->sum('amount');
        $withdrawal['cash'] = AccountTransaction::where('account_id', $cash_account)->where('type', 'debit')
            ->whereDate('operation_date', '<', $start_date)
            ->where('sub_type', 'deposit')->sum('amount');
        $withdrawal['cheque'] = AccountTransaction::where('account_id', $cheque_account)->where('type', 'debit')
            ->whereDate('operation_date', '<', $start_date)
            ->where('sub_type', 'deposit')->sum('amount');
        $withdrawal['card'] = AccountTransaction::where('account_id', $card_account)->where('type', 'debit')
            ->whereDate('operation_date', '<', $start_date)
            ->where('sub_type', 'deposit')->sum('amount');
        $purchase_by_cash = Transaction::leftjoin('transaction_payments', 'transactions.id', 'transaction_payments.transaction_id')
            ->where('transactions.business_id', $business_id)->where('type', 'purchase')->where('payment_status', 'paid')->where('transaction_payments.method', 'cash')
            ->whereDate('transactions.transaction_date', '<', $start_date);
        if (!empty($location_id)) {
            $purchase_by_cash->where('transactions.location_id', $location_id);
        }
        $purchase_by_cash = $purchase_by_cash->sum('final_total');
        $pre_day_balance = array(
            'cash' => 0.00,
            'cheque' => 0.00,
            'card' => 0.00,
            'credit_sale' => 0.00,
        );
        $cash_ob =  AccountTransaction::where('account_id', $cash_account)->where('sub_type', 'opening_balance')->select('amount')->first();
        $cheque_ob =  AccountTransaction::where('account_id', $cheque_account)->where('sub_type', 'opening_balance')->select('amount')->first();
        $card_ob = AccountTransaction::where('account_id', $card_account)->where('sub_type', 'opening_balance')->select('amount')->first();
        $account_balance['cash'] =  !empty($cash_ob) ? $cash_ob->amount : 0;
        $account_balance['cheque'] =  !empty($cheque_ob) ? $cheque_ob->amount : 0;
        $account_balance['card'] = !empty($card_ob) ? $card_ob->amount : 0;
        $pre_day_balance['cash'] = $account_balance['cash'] + $received['cash'] + $shortage_recover['cash'] + $withdrawal['cash'] -  $excess_commission['cash'] - $direct_expenses->cash_total - $deposit['cash'];
        $pre_day_balance['cheque'] = $account_balance['cheque'] + $received['cheque'] + $shortage_recover['cheque'] + $withdrawal['cheque'] -  $excess_commission['cheque'] - $direct_expenses->cheque_total - $deposit['cheque'];
        $pre_day_balance['card'] = $account_balance['card'] + $received['card'] + $shortage_recover['card'] + $withdrawal['card'] -  $excess_commission['card'] - $direct_expenses->card_total - $deposit['card'];
        $pre_day_balance['credit_sale'] = $received['credit_sale'];
        return $pre_day_balance;
    }
    public function getDailyReport(Request $request)
    {
        if (!auth()->user()->can('daily_report.view')) {
            abort(403, 'Unauthorized action.');
        }
        $is_woocommerce = $this->moduleUtil->isModuleInstalled('Woocommerce');
        $business_id = $request->session()->get('user.business_id');
        $default_start = new Carbon('first day of this month');
        $default_end = new Carbon('last day of this month');
        $start_date = !empty($request->start_date) ? Carbon::parse($request->start_date)->format('Y-m-d') : $default_start->format('Y-m-d');
        $end_date = !empty($request->end_date) ? Carbon::parse($request->end_date)->format('Y-m-d') : $default_end->format('Y-m-d');
        \Log::info("START DATE $start_date");
        \Log::info("END   DATE $end_date");
        $day_diff = !empty($request->start_date) && !empty($request->end_date) ? Carbon::parse($request->start_date)->diffInDays(Carbon::parse($request->end_date)) : 0;
        $location_id = $request->location_id;
        $work_shift_id = $request->work_shift;
        $stock_values = array(
            'previous_day_stock' => 0.00,
            'sale_return' => 0.00,
            'purchase_stock' => 0.00,
            'sold_stock' => 0.00,
            'balance' => 0.00,
        );
        $stock_values['sale_return'] = Account::getStockGroupAccountBalanceByTransactionType('sell_return', $start_date, $end_date);
        $stock_values['purchase_stock'] = abs(Account::getStockGroupAccountBalanceByTransactionType('purchase', $start_date, $end_date));
        $stock_values['sold_stock'] = abs(Account::getStockGroupAccountBalanceByTransactionType('sell', $start_date, $end_date));
        $stock_values['previous_day_stock'] = $this->getPreviousDayStock($request, $start_date, $end_date);
        $stock_values['balance'] =  $stock_values['previous_day_stock'] + ($stock_values['sale_return'] + $stock_values['purchase_stock']) -  $stock_values['sold_stock'];
        $cash_account_group = AccountGroup::getGroupByName('Cash Account');
        $cash_account_group_id = !empty($cash_account_group) ? $cash_account_group->id : null;
        $cheque_in_hand_account_group = AccountGroup::getGroupByName("Cheques in Hand (Customer's)");
        $cheque_in_hand_account_group_id = !empty($cheque_in_hand_account_group) ? $cheque_in_hand_account_group->id : null;
        $card_account_group = AccountGroup::getGroupByName('Card');
        $card_account_group_id = !empty($card_account_group) ? $card_account_group->id : null;
        $location_details = '';
        $work_shift = '';
        if (!empty($location_id)) {
            $location_details = BusinessLocation::where('id', $location_id)->first();
        }
        if (!empty($work_shift_id)) {
            $work_shift = WorkShift::where('id', $work_shift_id)->first()->shift_name;
        }
        $petro_module = $this->moduleUtil->hasThePermissionInSubscription($business_id, 'enable_petro_module');
        $sell_details =  Transaction::leftjoin('transaction_sell_lines', 'transactions.id', 'transaction_sell_lines.transaction_id')
            ->leftjoin('products', 'transaction_sell_lines.product_id', 'products.id')
            ->leftjoin('categories', 'products.category_id', 'categories.id')
            ->leftjoin('categories as sub_cat', 'products.sub_category_id', 'sub_cat.id')
            ->where('categories.name', '!=', 'Fuel')
            ->where('transactions.business_id', $business_id)
            ->where('transactions.type', 'sell')
            // ->where('transactions.sub_type', '!=', 'credit_sale')
            ->whereDate('transactions.transaction_date', '>=', $start_date)
            ->whereDate('transactions.transaction_date', '<=', $end_date)
            ->select(
                'sub_cat.name as category_name',
                DB::raw('SUM(transaction_sell_lines.quantity - transaction_sell_lines.quantity_returned) as qty'),
                DB::raw('SUM(transaction_sell_lines.quantity  * transaction_sell_lines.unit_price_inc_tax) as total_amount'),
                DB::raw('SUM(transaction_sell_lines.line_discount_amount) as dicount_given')
            )
            ->groupBy('products.sub_category_id');
        if (!empty($location_id)) {
            $sell_details->where('transactions.location_id', $location_id);
        }
        $sales = $sell_details->get();
        // dd($sales->toArray());
        $sales_dicount_given = $sales->sum('dicount_given');
        $excess_total = 0.00;
        $shortage_total = 0.00;
        $expense_in_settlement = 0.00;
        $petro_sales_total = 0;
        $petro_dicount_given = 0;
        $petro_sales = [];
        if ($petro_module) {
            $excess_total_query = Transaction::leftjoin('settlements', function ($query) use ($business_id) {
                $query->on('transactions.invoice_no', 'settlements.settlement_no')->where('settlements.business_id', $business_id);
            })
                ->where('transactions.business_id', $business_id)->where('type', 'settlement')->where('sub_type', 'excess')
                ->whereDate('transactions.transaction_date', '>=', $start_date)
                ->whereDate('transactions.transaction_date', '<=', $end_date)
                ->select(DB::raw('ABS(transactions.final_total) as final_total'));
            if (!empty($work_shift_id)) {
                $excess_total_query->whereJsonContains('settlements.work_shift', $work_shift_id);
            }
            if (!empty($location_id)) {
                $excess_total_query->where('transactions.location_id', $location_id);
            }
            $excess_total =  abs($excess_total_query->sum('final_total'));
            $expense_in_settlement = Transaction::where('business_id', $business_id)->where('type', 'settlement')->where('sub_type', 'expense')
                ->whereDate('transaction_date', '>=', $start_date)
                ->whereDate('transaction_date', '<=', $end_date);
            if (!empty($location_id)) {
                $expense_in_settlement->where('transactions.location_id', $location_id);
            }
            $expense_in_settlement =  $expense_in_settlement->sum('final_total');
            $petro_sales_query = Transaction::leftjoin('transaction_sell_lines', 'transactions.id', 'transaction_sell_lines.transaction_id')
                ->leftjoin('settlements', function ($query) use ($business_id) {
                    $query->on('transactions.invoice_no', 'settlements.settlement_no')->where('settlements.business_id', $business_id);
                })
                ->leftjoin('products', 'transaction_sell_lines.product_id', 'products.id')
                ->leftjoin('categories', 'products.category_id', 'categories.id')
                ->leftjoin('categories as sub_cat', 'products.sub_category_id', 'sub_cat.id')
                ->where('categories.name', 'Fuel')
                ->where('transactions.business_id', $business_id)
                ->where('transactions.type', 'sell')
                // ->where('transactions.sub_type', '!=', 'credit_sale')
                ->whereDate('transactions.transaction_date', '>=', $start_date)
                ->whereDate('transactions.transaction_date', '<=', $end_date)
                ->select(
                    'sub_cat.name as sub_category_name',
                    DB::raw('SUM(transaction_sell_lines.quantity - transaction_sell_lines.quantity_returned) as qty'),
                    DB::raw('SUM(transaction_sell_lines.quantity  * transaction_sell_lines.unit_price_inc_tax) as total_amount'),
                    DB::raw('SUM(transaction_sell_lines.line_discount_amount) as dicount_given')
                )
                ->groupBy('products.sub_category_id');
            if (!empty($work_shift_id)) {
                $petro_sales_query->whereJsonContains('settlements.work_shift', $work_shift_id);
            }
            if (!empty($location_id)) {
                $petro_sales_query->where('transactions.location_id', $location_id);
            }
            $petro_sales = $petro_sales_query->get();
            $petro_sales_total = $petro_sales->sum('total_amount');
            $petro_dicount_given = $petro_sales->sum('dicount_given');
        }
        $petty_cash_account_id = $this->transactionUtil->account_exist_return_id('Petty Cash');
        $direct_cash_expenses_query = Transaction::where('transactions.business_id', $business_id)
            ->where(function ($q) {
                $q->where('type', 'expense');
            })
            ->leftjoin('transaction_payments', 'transactions.id', 'transaction_payments.transaction_id')
            ->where('transaction_payments.method', 'cash')
            ->where('transaction_payments.account_id', '!=', $petty_cash_account_id)
            ->whereIn('transactions.payment_status', ['paid', 'partial'])
            ->whereDate('transactions.transaction_date', '>=', $start_date)
            ->whereDate('transactions.transaction_date', '<=', $end_date);
        if (!empty($location_id)) {
            $direct_cash_expenses_query->where('transactions.location_id', $location_id);
        }
        $direct_cash_expenses = $direct_cash_expenses_query->sum('transaction_payments.amount');
        $purchase_by_cash = 0;
        $purchase_by_cash = Transaction::leftjoin('transaction_payments', 'transactions.id', 'transaction_payments.transaction_id')
            ->where('type', 'purchase')->whereIn('payment_status', ['partial', 'paid'])->where('transaction_payments.method', 'cash')
            ->where('transactions.business_id', $business_id)
            ->whereDate('transactions.transaction_date', '>=', $start_date)
            ->whereDate('transactions.transaction_date', '<=', $end_date);
        if (!empty($location_id)) {
            $purchase_by_cash->where('transactions.location_id', $location_id);
        }
        // \Log::info($purchase_by_cash->toSql());
        \Log::info("start");
        \Log::info($purchase_by_cash->get()->toArray());
        $purchase_by_cash = $purchase_by_cash->sum('transaction_payments.amount');
        \Log::info($purchase_by_cash);
        $supplier_ob_by_cash = Transaction::leftjoin('contacts', 'transactions.contact_id', 'contacts.id')
        ->leftjoin('transaction_payments', 'transactions.id', 'transaction_payments.transaction_id')
        ->where('contacts.type', 'supplier')
        ->where('transactions.type', 'opening_balance')->whereIn('payment_status', ['partial', 'paid'])->where('transaction_payments.method', 'cash')
        ->where('transactions.business_id', $business_id);
        // ->whereDate('transactions.transaction_date', '>=', $start_date)
        // ->whereDate('transactions.transaction_date', '<=', $end_date);
        if (!empty($location_id)) {
            $supplier_ob_by_cash->where('transactions.location_id', $location_id);
        }
        // $supplier_ob_by_cash->select('transactions.id');
        $transIds = $supplier_ob_by_cash->pluck('transactions.id');
        $obsum = TransactionPayment::whereIn('transaction_id', $transIds)
        ->where('method', 'cash')
        ->whereDate('paid_on', '>=', $start_date)
        ->whereDate('paid_on', '<=', $end_date);
        $supplier_ob_by_cash = $obsum->sum('amount');
        $total_purchase_by_cash = $purchase_by_cash + $supplier_ob_by_cash;
        // opening balance received
        $received_outstanding_query_ob = Transaction::leftjoin('transaction_payments', 'transactions.id', 'transaction_payments.transaction_id')
            ->leftjoin('contacts', 'transactions.contact_id', 'contacts.id')
            ->where('transactions.business_id', $business_id)
            ->where('contacts.type', 'customer')
            ->where('transactions.type', 'opening_balance')
            ->whereIn('transactions.payment_status', ['paid', 'partial'])
            ->whereIn('transaction_payments.method', ['cash', 'cheque', 'card'])
            ->whereDate('transaction_payments.paid_on', '>=', $start_date)
            ->whereDate('transaction_payments.paid_on', '<=', $end_date);
        if (!empty($location_id)) {
            $received_outstanding_query_ob->where('transactions.location_id', $location_id);
        }
        $total_received_outstanding_ob =  $received_outstanding_query_ob->select(
            DB::raw('SUM(IF(transaction_payments.method="cash", transaction_payments.amount, 0)) as cash'),
            DB::raw('SUM(IF(transaction_payments.method="card", transaction_payments.amount, 0)) as card'),
            DB::raw('SUM(IF(transaction_payments.method="cheque", transaction_payments.amount, 0)) as cheque'),
            DB::raw('SUM(transaction_payments.amount) as total_amount')
        )->first();
        // outstanding received
        $total_received_outstanding = $this->getTotalReceivedOutstanding($business_id, $start_date, $end_date, $location_id, false);
        // recieved amount with out settlement sale // related to pos sales
        $received_query = Transaction::leftjoin('transaction_payments', 'transactions.id', 'transaction_payments.transaction_id')
            ->where('transactions.business_id', $business_id)
            ->whereIn('transactions.type', ['sell', 'purchase_return'])
            ->whereIn('transactions.payment_status', ['due', 'partial'])
            ->where('is_settlement', 0)
            ->whereDate('transaction_payments.paid_on', '>=', $start_date)
            ->whereDate('transaction_payments.paid_on', '<=', $end_date);
        if (!empty($location_id)) {
            $received_query->where('transactions.location_id', $location_id);
        }
        $received_result = $received_query->select(
            DB::raw('SUM(IF(transactions.payment_status="due" AND transactions.type="sell", transactions.final_total, 0)) as credit_sale')
        )->first();
        // recieved amount settlement sale 
        $settlement_received_query = Transaction::where('transactions.business_id', $business_id)
            ->whereIn('transactions.type', ['settlement', 'sell'])
            ->whereIn('transactions.payment_status', ['due', 'partial'])
            ->where('is_settlement', 1)
            // ->where('is_credit_sale', 0)
            ->whereDate('transactions.transaction_date', '>=', $start_date)
            ->whereDate('transactions.transaction_date', '<=', $end_date);
        if (!empty($location_id)) {
            $settlement_received_query->where('transactions.location_id', $location_id);
        }
        $settlement_received_result = $settlement_received_query->select(
            DB::raw('SUM(IF(transactions.payment_status="due" AND transactions.type="sell", transactions.final_total, 0)) as credit_sale')
        )->first();
        $adv_sec_deposit_query = Transaction::leftjoin('transaction_payments', 'transactions.id', 'transaction_payments.transaction_id')
            ->where('transactions.business_id', $business_id)
            ->whereIn('type', ['advance_payment', 'security_deposit'])
            ->whereDate('transaction_date', '>=', $start_date)
            ->whereDate('transaction_date', '<=', $end_date);
        if (!empty($location_id)) {
            $adv_sec_deposit_query->where('transactions.location_id', $location_id);
        }
        $adv_sec_deposit_result = $adv_sec_deposit_query->select(
            DB::raw('SUM(IF(transaction_payments.method="cash", transaction_payments.amount, 0)) as cash'),
            DB::raw('SUM(IF(transaction_payments.method="card", transaction_payments.amount, 0)) as card'),
            DB::raw('SUM(IF(transaction_payments.method="cheque", transaction_payments.amount, 0)) as cheque'),
        )->first();
        $receiveable_account_id = $this->transactionUtil->account_exist_return_id('Accounts Receivable');
        $received['credit_sale'] = Account::getAccountBalanceByType($receiveable_account_id, 'debit', $start_date, $end_date, false, false);
        $received['cash'] = Account::getAccountGroupBalanceByType($cash_account_group_id, 'debit', $start_date, $end_date) - $expense_in_settlement;
        $received['cheque'] = Account::getAccountGroupBalanceByType($cheque_in_hand_account_group_id, 'debit', $start_date, $end_date);
        $received['card'] = Account::getAccountGroupBalanceByType($card_account_group_id, 'debit', $start_date, $end_date);
        $shortage_query = Transaction::leftjoin('transaction_payments', 'transactions.id', 'transaction_payments.transaction_id')
            ->leftjoin('settlements', function ($query) use ($business_id) {
                $query->on('transactions.invoice_no', 'settlements.settlement_no')->where('settlements.business_id', $business_id);
            })
            ->where('transactions.business_id', $business_id)->where('type', 'settlement')->where('sub_type', 'shortage')
            ->whereIn('transactions.payment_status', ['paid', 'partial'])
            ->whereDate('transaction_payments.paid_on', '>=', $start_date)
            ->whereDate('transaction_payments.paid_on', '<=', $end_date);
        if (!empty($work_shift_id)) {
            $shortage_query->whereJsonContains('settlements.work_shift', $work_shift_id);
        }
        if (!empty($location_id)) {
            $shortage_query->where('transactions.location_id', $location_id);
        }
        $shortage_result = $shortage_query->select(
            DB::raw('SUM(IF(transaction_payments.method="cash", transaction_payments.amount, 0)) as cash'),
            DB::raw('SUM(IF(transaction_payments.method="card", transaction_payments.amount, 0)) as card'),
            DB::raw('SUM(IF(transaction_payments.method="cheque", transaction_payments.amount, 0)) as cheque'),
            DB::raw('SUM(IF(transaction_payments.method="credit_sale", transaction_payments.amount, 0)) as credit_sale')
        )->first();
        $shortage_recover['cash'] = $shortage_result->cash;
        $shortage_recover['card'] = $shortage_result->card;
        $shortage_recover['cheque'] = $shortage_result->cheque;
        $shortage_recover['credit_sale'] = $shortage_result->credit_sale;
        $shortage_total_query = Transaction::leftjoin('settlements', function ($query) use ($business_id) {
            $query->on('transactions.invoice_no', 'settlements.settlement_no')->where('settlements.business_id', $business_id);
        })
            ->where('transactions.business_id', $business_id)->where('type', 'settlement')->where('sub_type', 'shortage')
            ->whereDate('transactions.transaction_date', '>=', $start_date)
            ->whereDate('transactions.transaction_date', '<=', $end_date);
        if (!empty($work_shift_id)) {
            $shortage_total_query->whereJsonContains('settlements.work_shift', $work_shift_id);
        }
        if (!empty($location_id)) {
            $shortage_total_query->where('transactions.location_id', $location_id);
        }
        $shortage_total_query =   $shortage_total_query->select(DB::raw('SUM(transactions.final_total) as shortage_total'))->first();
        $shortage_total = !empty($shortage_total_query)  ? $shortage_total_query->shortage_total : 0.00;
        $excess_commission_query = Transaction::leftjoin('transaction_payments', 'transactions.id', 'transaction_payments.transaction_id')
            ->leftjoin('settlements', function ($query) use ($business_id) {
                $query->on('transactions.invoice_no', 'settlements.settlement_no')->where('settlements.business_id', $business_id);
            })
            ->where('transactions.business_id', $business_id)->where('type', 'settlement')->where('sub_type', 'excess')
            ->whereIn('transactions.payment_status', ['paid', 'partial'])
            ->whereDate('transaction_payments.paid_on', '>=', $start_date)
            ->whereDate('transaction_payments.paid_on', '<=', $end_date);
        if (!empty($work_shift_id)) {
            $excess_commission_query->whereJsonContains('settlements.work_shift', $work_shift_id);
        }
        if (!empty($location_id)) {
            $excess_commission_query->where('transactions.location_id', $location_id);
        }
        $excess_result = $excess_commission_query->select(
            DB::raw('SUM(IF(transaction_payments.method="cash", transaction_payments.amount, 0)) as cash'),
            DB::raw('SUM(IF(transaction_payments.method="card", transaction_payments.amount, 0)) as card'),
            DB::raw('SUM(IF(transaction_payments.method="cheque", transaction_payments.amount, 0)) as cheque'),
            DB::raw('SUM(IF(transaction_payments.method="credit_sale", transaction_payments.amount, 0)) as credit_sale')
        )->first();
        $excess_commission['cash'] = $excess_result->cash;
        $excess_commission['card'] = $excess_result->card;
        $excess_commission['cheque'] = $excess_result->cheque;
        $excess_commission['credit_sale'] = $excess_result->credit_sale;
        $pump_operator_shortage = $this->getPreviousDayPumpOperatorShortage($start_date, $end_date, $location_id, $work_shift);
        $pump_operator_excess = $this->getPreviousDayPumpOperatorExcess($start_date, $end_date, $location_id, $work_shift);
        $deposit = array(
            'cash' => 0.00,
            'cheque' => 0.00,
            'card' => 0.00,
        );
        $cash_account = $this->transactionUtil->account_exist_return_id('Cash');
        $cheque_account = $this->transactionUtil->account_exist_return_id('Cheques in Hand');
        $card_account = $this->transactionUtil->account_exist_return_id('Cards (Credit Debit) Account');
        $card_group_id = AccountGroup::getGroupByName('Card', true);
        $deposit['cash'] = AccountTransaction::where('account_id', $cash_account)
            ->where('type', 'credit')
            ->whereDate('operation_date', '>=', $start_date)
            ->whereDate('operation_date', '<=', $end_date)
            ->whereIn('sub_type', ['deposit', 'fund_transfer'])->sum('amount');
        $deposit['cheque'] = AccountTransaction::where('account_id', $cheque_account)
            ->where('type', 'credit')
            ->whereDate('operation_date', '>=', $start_date)
            ->whereDate('operation_date', '<=', $end_date)
            ->whereIn('sub_type', ['deposit', 'fund_transfer'])->sum('amount');
        $deposit['card'] = AccountTransaction::leftjoin('accounts', 'account_transactions.account_id', 'accounts.id')->where('asset_type', $card_group_id)
            ->where('type', 'credit')
            ->whereDate('operation_date', '>=', $start_date)
            ->whereDate('operation_date', '<=', $end_date)
            ->whereIn('sub_type', ['deposit', 'fund_transfer'])->sum('amount');
        $deposit_by_customer = Transaction::where('business_id', $business_id)
            ->whereIn('type', ['advance_payment', 'deposit', 'security_deposit'])
            ->whereDate('transaction_date', '>=', $start_date)
            ->whereDate('transaction_date', '<=', $end_date)
            ->sum('final_total');
        $withdrawal_cash = AccountTransaction::leftjoin('accounts', 'account_transactions.account_id', 'accounts.id')
            ->where('accounts.asset_type', $cash_account_group_id)
            ->whereIn('account_transactions.sub_type', ['fund_transfer', 'deposit'])
            ->whereDate('operation_date', '>=', $start_date)
            ->whereDate('operation_date', '<=', $end_date)
            ->where('type', 'debit')->sum('amount');
        $dicount_given =   $petro_dicount_given + $sales_dicount_given;
        $total_sale_amount_value = $sales->sum('total_amount') + $petro_sales_total;
        $total_income = $total_sale_amount_value + abs($excess_total)  + $deposit_by_customer + $withdrawal_cash + $total_received_outstanding;
        $total_out = $shortage_total + $expense_in_settlement + $dicount_given;
        $balance_in_hand = $total_income - $total_out;
        $previous_day_balance = $this->getPrevioudDayBalance($start_date, $end_date, $location_id);
        $deposited_credit_sale = $total_received_outstanding;
        $pre_deposited_credit_sale = $this->getTotalReceivedOutstanding($business_id, $start_date, $end_date, $location_id, true);
        $previous_day_balance['credit_sale'] = $previous_day_balance['credit_sale'] - $pre_deposited_credit_sale;
        //finance section balance
        $balance['cash'] = $previous_day_balance['cash'] + $received['cash'] + $shortage_recover['cash'] - ($excess_commission['cash'] + $deposit['cash'] + $direct_cash_expenses + $total_purchase_by_cash);
        $balance['card'] = $previous_day_balance['card'] + $received['card'] + $shortage_recover['card'] - ($excess_commission['card'] + $deposit['card']);
        $balance['cheque'] = $previous_day_balance['cheque'] + $received['cheque'] + $shortage_recover['cheque'] - ($excess_commission['cheque'] + $deposit['cheque']);
        $balance['credit_sale'] = $previous_day_balance['credit_sale'] + $received['credit_sale'] + $shortage_recover['credit_sale'] - ($excess_commission['credit_sale'] + $deposited_credit_sale);
        $outstandings['previous_day'] = $previous_day_balance['credit_sale'];
        $outstandings['given'] = $received['credit_sale'];
        $outstandings['received'] = $total_received_outstanding;
        $outstandings['balance'] = $outstandings['previous_day'] + ($outstandings['given'] - $outstandings['received']);
        $business_locations = BusinessLocation::forDropdown($business_id);
        $work_shifts = WorkShift::where('business_id', $business_id)->pluck('shift_name', 'id');
        $dip_details = DipReading::leftjoin('business_locations', 'dip_readings.location_id', 'business_locations.id')
            ->leftjoin('fuel_tanks', 'dip_readings.tank_id', 'fuel_tanks.id')
            ->leftjoin('products', 'fuel_tanks.product_id', 'products.id')
            ->where('dip_readings.business_id', $business_id)
            ->whereDate('dip_readings.date_and_time', '>=', $start_date)->whereDate('dip_readings.date_and_time', '<=', $end_date)
            ->select([
                'dip_readings.*',
                'business_locations.name as location_name',
                'fuel_tanks.fuel_tank_number as tank_name',
                'products.name as product_name',
                DB::raw("(SELECT SUM(fuel_balance_dip_reading-current_qty) FROM dip_readings 
                WHERE dip_readings.tank_id=fuel_tanks.id AND dip_readings.date_and_time <  '$start_date'
                ) as opening_balance_dip_difference"),
                DB::raw('SUM(fuel_balance_dip_reading-current_qty) as balance_dip_difference')
            ])
            ->groupBy('fuel_tanks.id')
            ->get();
        $print_s_date = $this->transactionUtil->format_date($start_date);
        $print_e_date = $this->transactionUtil->format_date($end_date);
        return view('report.daily_report')->with(compact(
            'location_details',
            'dip_details',
            'work_shift',
            'day_diff',
            'petro_module',
            'business_locations',
            'work_shifts',
            'work_shift_id',
            'location_id',
            'sales',
            'excess_total',
            'shortage_total',
            'expense_in_settlement',
            'direct_cash_expenses',
            'total_income',
            'dicount_given',
            'total_out',
            'balance_in_hand',
            'total_purchase_by_cash',
            'petro_sales',
            'received',
            'total_received_outstanding',
            'shortage_recover',
            'excess_commission',
            'previous_day_balance',
            'balance',
            'deposited_credit_sale',
            'outstandings',
            'pump_operator_shortage',
            'pump_operator_excess',
            'stock_values',
            'deposit',
            'deposit_by_customer',
            'withdrawal_cash',
            'print_s_date',
            'print_e_date',
            'start_date',
            'end_date'
        ));
    }
    public function getTotalReceivedOutstanding($business_id, $start_date, $end_date, $location_id, $is_previous = false)
    {
        // outstanding received
        $received_outstanding_query_without_parent = $this->getReceivedOutstandingQuery($business_id, $start_date, $end_date, $location_id, $is_previous)->whereNull('parent_id');
        $ro_without_parent =  $received_outstanding_query_without_parent->select(
            DB::raw('SUM(transaction_payments.amount) as total_amount'),
        )->first();
        $total_received_outstanding = $ro_without_parent->total_amount;
        $received_outstanding_query_parent = $this->getReceivedOutstandingQuery($business_id, $start_date, $end_date, $location_id, $is_previous)->whereNotNull('parent_id')
            ->groupBy('parent_id')->get();
        foreach ($received_outstanding_query_parent as $roq) {
            if (!empty($roq->parent_id)) {
                $pt = TransactionPayment::find($roq->parent_id);
                if (!empty($pt)) {
                    $total_received_outstanding += $pt->amount;
                }
            } else {
                $total_received_outstanding += $roq->amount;
            }
        }
        return $total_received_outstanding;
    }
    public function getReceivedOutstandingQuery($business_id, $start_date, $end_date, $location_id, $is_previous = false)
    {
        $received_outstanding_query = Transaction::leftjoin('transaction_payments', 'transactions.id', 'transaction_payments.transaction_id')
            ->leftjoin('contacts', 'transactions.contact_id', 'contacts.id')
            ->where('transactions.business_id', $business_id)
            ->where(function ($q) {
                $q->where('transactions.type', 'opening_balance')->orWhere('transactions.is_credit_sale', 1);
            })
            ->where('contacts.type', 'customer')
            ->whereIn('transactions.payment_status', ['paid', 'partial'])
            ->whereIn('transaction_payments.method', ['cash', 'cheque', 'card']);
        if ($is_previous) {
            $received_outstanding_query->whereDate('transaction_payments.paid_on', '<', $start_date);
        } else {
            $received_outstanding_query->whereDate('transaction_payments.paid_on', '>=', $start_date)
                ->whereDate('transaction_payments.paid_on', '<=', $end_date);
        }
        if (!empty($location_id)) {
            $received_outstanding_query->where('transactions.location_id', $location_id);
        }
        return $received_outstanding_query;
    }
    //10/11/2020
    public function getMonthlyReport(Request $request, $is_previous = false)
    {
        if (!auth()->user()->can('monthly_report.view')) {
            abort(403, 'Unauthorized action.');
        }
        $is_woocommerce = $this->moduleUtil->isModuleInstalled('Woocommerce');
        $business_id = $request->session()->get('user.business_id');
        $default_start = Carbon::now()->month;
        $default_end = Carbon::now()->month;
        $default_year = Carbon::now()->year;
        $start_month = (!empty($request->start_month) ? $request->start_month : $default_start);
        $end_month = (!empty($request->end_month) ? $request->end_month : $default_end);
        $year = !empty($request->year) ? $request->year : $default_year;
        $location_id = $request->location_id;
        $work_shift_id = $request->work_shift;
        $location_details = '';
        $work_shift = '';
        $start_date = Carbon::createFromDate($year, $start_month, 1)->firstOfMonth()->format('Y-m-d');
        $end_date = Carbon::createFromDate($year, $end_month, 1)->lastOfMonth()->format('Y-m-d');
        $previou_day_of_start_date = Carbon::createFromDate($year, $start_month, 1)->firstOfMonth()->subDays(1)->format('Y-m-d');
        if ($is_previous) { //if previous day make start and end date same to make only one day period
            $start_date = $previou_day_of_start_date;
            $end_date = $previou_day_of_start_date;
        }
        $period = CarbonPeriod::create($start_date, $end_date);
        if (!empty($location_id)) {
            $location_details = BusinessLocation::where('id', $location_id)->first();
        }
        if (!empty($work_shift_id)) {
            $work_shift = WorkShift::where('id', $work_shift_id)->first()->shift_name;
        }
        $petro_module = $this->moduleUtil->hasThePermissionInSubscription($business_id, 'enable_petro_module');
        $sub_categories = Category::subCategoryOnlyFuel($business_id);
        $cash_account = $this->transactionUtil->account_exist_return_id('Cash');
        $total_sales = [];
        $sub_category_sales = [];
        $received_payments = [];
        $excess_total = [];
        $shortage_total = [];
        $credit_sales = [];
        $expense_in_settlement = [];
        $direct_expens = [];
        $purchase_by_cash = [];
        $total_collection = [];
        $difference = [];
        $credit_received_payments = [];
        $today_total_cash = [];
        $previous_day_cash_balance = [];
        $total_cash_balance = [];
        $cash_deposit = [];
        $cash_balance_difference = [];
        foreach ($period as $date) {
            $date =  $date->format('Y-m-d');
            $total_sale_query =  Transaction::leftjoin('transaction_sell_lines', 'transactions.id', 'transaction_sell_lines.transaction_id')
                ->where('transactions.business_id', $business_id)
                ->where('transactions.type', 'sell');
            if ($is_previous) {
                $total_sale_query->whereDate('transaction_date', '<=', $date);
            } else {
                $total_sale_query->whereDate('transaction_date', $date);
            }
            $total_sale_query->select(
                DB::raw('SUM(transactions.final_total) as total_amount')
            );
            if (!empty($location_id)) {
                $total_sale_query->where('transactions.location_id', $location_id);
            }
            $total_sales[$date] = $total_sale_query->first()->total_amount;
            foreach ($sub_categories as  $sub_category) {
                $sub_category_sale_query =  Transaction::leftjoin('transaction_sell_lines', 'transactions.id', 'transaction_sell_lines.transaction_id')
                    ->leftjoin('products', 'transaction_sell_lines.product_id', 'products.id')
                    ->where('products.sub_category_id', $sub_category->id)
                    ->where('transactions.business_id', $business_id)
                    ->where('transactions.type', 'sell');
                if ($is_previous) {
                    $sub_category_sale_query->whereDate('transaction_date', '<=', $date);
                } else {
                    $sub_category_sale_query->whereDate('transaction_date', $date);
                }
                $sub_category_sale_query->select(
                    DB::raw('SUM(transactions.final_total) as total_amount')
                );
                if (!empty($location_id)) {
                    $sub_category_sale_query->where('transactions.location_id', $location_id);
                }
                $sub_category_sales[$date][$sub_category->id] = $sub_category_sale_query->first()->total_amount;
            }
            $received_query = Transaction::leftjoin('transaction_payments', 'transactions.id', 'transaction_payments.transaction_id')
                ->leftjoin('accounts', 'transaction_payments.card_type', 'accounts.id')
                ->where('transactions.business_id', $business_id)
                ->where('transaction_payments.business_id', $business_id)
                ->whereIn('transactions.type', ['opening_balance', 'sell', 'purchase_return'])
                ->whereIn('transactions.payment_status', ['paid', 'partial']);
            if ($is_previous) {
                $received_query->whereDate('transaction_date', '<=', $date);
            } else {
                $received_query->whereDate('transaction_date', $date);
            }
            if (!empty($location_id)) {
                $received_query->where('transactions.location_id', $location_id);
            }
            $received_payments[$date] = $received_query->select(
                DB::raw('SUM(IF(transaction_payments.method="cash", transaction_payments.amount, 0)) as cash'),
                DB::raw('SUM(IF(transaction_payments.method="cheque", transaction_payments.amount, 0)) as cheque'),
                DB::raw('SUM(IF(transaction_payments.method="card" AND accounts.name="Amex Card", transaction_payments.amount, 0)) as amex'),
                DB::raw('SUM(IF(transaction_payments.method="card" AND accounts.name="Visa Master", transaction_payments.amount, 0)) as visa'),
                DB::raw('SUM(IF(transaction_payments.method="card" AND accounts.name!="Amex Card" AND accounts.name!="Visa Master", transaction_payments.amount, 0)) as other_card'),
            )->first();
            $excess_total_query = Transaction::where('transactions.business_id', $business_id)->where('type', 'settlement')->where('sub_type', 'excess');
            if ($is_previous) {
                $excess_total_query->whereDate('transaction_date', '<=', $date);
            } else {
                $excess_total_query->whereDate('transaction_date', $date);
            }
            if (!empty($location_id)) {
                $excess_total_query->where('transactions.location_id', $location_id);
            }
            $excess_total[$date] =  $excess_total_query->select(DB::raw('SUM(transactions.final_total) as amount'))->first()->amount;
            $shortage_total_query = Transaction::where('transactions.business_id', $business_id)->where('type', 'settlement')->where('sub_type', 'shortage');
            if ($is_previous) {
                $shortage_total_query->whereDate('transaction_date', '<=', $date);
            } else {
                $shortage_total_query->whereDate('transaction_date', $date);
            }
            if (!empty($location_id)) {
                $shortage_total_query->where('transactions.location_id', $location_id);
            }
            $shortage_total[$date] =  $shortage_total_query->select(DB::raw('SUM(transactions.final_total) as amount'))->first()->amount;
            $credit_sale_query =  Transaction::leftjoin('transaction_sell_lines', 'transactions.id', 'transaction_sell_lines.transaction_id')
                ->where('transactions.business_id', $business_id)
                ->where('transactions.type', 'sell')
                ->where('transactions.is_credit_sale', 1);
            if ($is_previous) {
                $credit_sale_query->whereDate('transaction_date', '<=', $date);
            } else {
                $credit_sale_query->whereDate('transaction_date', $date);
            }
            $credit_sale_query->select(
                DB::raw('SUM(transactions.final_total) as total_amount')
            );
            if (!empty($location_id)) {
                $credit_sale_query->where('transactions.location_id', $location_id);
            }
            $credit_sales[$date] = $credit_sale_query->first()->total_amount;
            $expense_in_settlement_query = Transaction::where('business_id', $business_id)->where('type', 'settlement')->where('sub_type', 'expense');
            if ($is_previous) {
                $expense_in_settlement_query->whereDate('transaction_date', '<=', $date);
            } else {
                $expense_in_settlement_query->whereDate('transaction_date', $date);
            }
            if (!empty($location_id)) {
                $expense_in_settlement_query->where('transactions.location_id', $location_id);
            }
            $expense_in_settlement[$date] =  $expense_in_settlement_query->sum('final_total');
            $direct_cash_expenses_query = Transaction::where('transactions.business_id', $business_id)->where('type', 'expense')
                ->leftjoin('transaction_payments', 'transactions.id', 'transaction_payments.transaction_id');
            if ($is_previous) {
                $direct_cash_expenses_query->whereDate('transaction_date', '<=', $date);
            } else {
                $direct_cash_expenses_query->whereDate('transaction_date', $date);
            }
            if (!empty($location_id)) {
                $direct_cash_expenses_query->where('transactions.location_id', $location_id);
            }
            $direct_expens[$date] = $direct_cash_expenses_query->sum('transaction_payments.amount');
            $purchase_by_cash_query = Transaction::leftjoin('transaction_payments', 'transactions.id', 'transaction_payments.transaction_id')
                ->where('type', 'purchase')->whereIn('payment_status', ['partial', 'paid'])->where('transaction_payments.method', 'cash')
                ->where('transactions.business_id', $business_id);
            if ($is_previous) {
                $purchase_by_cash_query->whereDate('transaction_date', '<=', $date);
            } else {
                $purchase_by_cash_query->whereDate('transaction_date', $date);
            }
            if (!empty($location_id)) {
                $purchase_by_cash_query->where('transactions.location_id', $location_id);
            }
            $purchase_by_cash[$date] = $purchase_by_cash_query->sum('transaction_payments.amount');
            $total_collection[$date] = $received_payments[$date]->cash
                + $received_payments[$date]->cheque
                + $received_payments[$date]->visa
                + $received_payments[$date]->amex
                + $received_payments[$date]->other_card
                + $shortage_total[$date]
                + $excess_total[$date]
                + $credit_sales[$date]
                - $expense_in_settlement[$date]
                - $direct_expens[$date]
                - $purchase_by_cash[$date];
            $difference[$date] = $total_sales[$date] - $total_collection[$date];
            $credit_received_query = Transaction::leftjoin('transaction_payments', 'transactions.id', 'transaction_payments.transaction_id')
                ->leftjoin('accounts', 'transaction_payments.card_type', 'accounts.id')
                ->where('transactions.business_id', $business_id)
                ->where('transaction_payments.business_id', $business_id)
                ->where(function ($q) {
                    $q->where('transactions.type', 'opening_balance')->orWhere('is_credit_sale', 1);
                })
                ->whereIn('transactions.payment_status', ['paid', 'partial']);
            if ($is_previous) {
                $credit_received_query->whereDate('transaction_date', '<=', $date);
            } else {
                $credit_received_query->whereDate('transaction_date', $date);
            }
            if (!empty($location_id)) {
                $credit_received_query->where('transactions.location_id', $location_id);
            }
            $credit_received_payments[$date] = $credit_received_query->select(
                DB::raw('SUM(IF(transaction_payments.method="cash", transaction_payments.amount, 0)) as cash'),
                DB::raw('SUM(IF(transaction_payments.method="cheque", transaction_payments.amount, 0)) as cheque'),
                DB::raw('SUM(IF(transaction_payments.method="card" AND accounts.name="Amex Card", transaction_payments.amount, 0)) as amex'),
                DB::raw('SUM(IF(transaction_payments.method="card" AND accounts.name="Visa Master", transaction_payments.amount, 0)) as visa'),
                DB::raw('SUM(IF(transaction_payments.method="card" AND accounts.name!="Amex Card" AND accounts.name!="Visa Master", transaction_payments.amount, 0)) as other_card'),
            )->first();
            $today_total_cash[$date] =  $received_payments[$date]->cash + $credit_received_payments[$date]->cash  - $expense_in_settlement[$date]  - $direct_expens[$date] - $purchase_by_cash[$date];
            $cash_deposit_query = AccountTransaction::where('account_id', $cash_account);
            if ($is_previous) {
                $cash_deposit_query->whereDate('operation_date', '<=', $date);
            } else {
                $cash_deposit_query->whereDate('operation_date', $date);
            }
            $cash_deposit[$date] = $cash_deposit_query->whereIn('sub_type', ['deposit', 'fund_transfer'])->sum('amount');
            $previous_day_date = Carbon::parse($date)->subDays(1)->format('Y-m-d');
            if ($is_previous) {
                $previous_day_cash_balance[$date] = 0;
            } else {
                if (array_key_exists($previous_day_date, $cash_balance_difference)) {
                    $previous_day_cash_balance[$date] = $cash_balance_difference[$previous_day_date];
                } else {
                    $previous_day_cash_balance[$date] = $this->getMonthlyReport($request, true);
                }
            }
            $total_cash_balance[$date] = $previous_day_cash_balance[$date] + $today_total_cash[$date];
            $cash_balance_difference[$date] = $total_cash_balance[$date] - $cash_deposit[$date];
            if ($is_previous) {
                return $cash_balance_difference[$date];
            }
        }
        $location_details = BusinessLocation::find($location_id);
        $business_locations = BusinessLocation::forDropdown($business_id);
        $work_shifts = WorkShift::where('business_id', $business_id)->pluck('shift_name', 'id');
        $print_s_date = date("F", mktime(0, 0, 0, $start_month, 1));
        $print_e_date = date("F", mktime(0, 0, 0, $end_month, 1));
        return view('report.monthly_report')->with(compact(
            'period',
            'total_sales',
            'sub_category_sales',
            'sub_categories',
            'received_payments',
            'excess_total',
            'shortage_total',
            'credit_sales',
            'expense_in_settlement',
            'direct_expens',
            'purchase_by_cash',
            'total_collection',
            'difference',
            'credit_received_payments',
            'today_total_cash',
            'previous_day_cash_balance',
            'total_cash_balance',
            'cash_deposit',
            'cash_balance_difference',
            'location_details',
            'work_shift',
            'print_s_date',
            'print_e_date',
        ));
    }
    public function getProductSellComparisonQuery($product_id,  $start_date, $end_date)
    {
        $business_id = request()->session()->get('user.business_id');
        $sell_stock_query = Transaction::leftjoin('transaction_sell_lines', 'transactions.id', 'transaction_sell_lines.transaction_id')
            ->leftjoin('product_variations', 'transaction_sell_lines.product_id', 'product_variations.product_id')
            ->leftjoin('variations', 'product_variations.id', 'variations.product_variation_id')
            ->whereIn('transactions.type', ['sell'])->where('transactions.status', 'final')
            ->where('transactions.sub_type', '!=', 'credit_sale')
            ->where('transaction_sell_lines.product_id', $product_id)
            ->where('transactions.business_id', $business_id);
        if (!empty($start_date) && !empty($end_date)) {
            $sell_stock_query->whereDate('transaction_date', '>=', $start_date);
            $sell_stock_query->whereDate('transaction_date', '<=', $end_date);
        }
        $sell = $sell_stock_query->select(
            DB::raw('SUM(transaction_sell_lines.quantity - transaction_sell_lines.quantity_returned) as qty'),
            DB::raw('SUM((transaction_sell_lines.quantity - transaction_sell_lines.quantity_returned) * variations.default_sell_price ) as value')
        )->groupBy('transaction_sell_lines.product_id')->first();
        return $sell;
    }
    public function getComparisonReport(Request $request)
    {
        $business_id = $request->session()->get('user.business_id');
        $start_date_one = $request->start_date_one;
        $end_date_one = $request->end_date_one;
        $start_date_two = $request->start_date_two;
        $end_date_two = $request->end_date_two;
        $products = Product::where('business_id', $business_id)->get();
        $sell_one_arr = [];
        $sell_two_arr = [];
        foreach ($products as $product) {
            $sell_one = $this->getProductSellComparisonQuery($product->id,  $start_date_one, $end_date_one);
            $sell_one_arr[$product->id]['name'] = $product->name;
            $sell_one_arr[$product->id]['qty'] = !empty($sell_one->qty) ? $sell_one->qty  : 0;
            $sell_one_arr[$product->id]['value'] = !empty($sell_one->value) ? $sell_one->value  : 0;
            $sell_two = $this->getProductSellComparisonQuery($product->id,  $start_date_two, $end_date_two);
            $sell_two_arr[$product->id]['name'] = $product->name;
            $sell_two_arr[$product->id]['qty'] = !empty($sell_two->qty) ? $sell_two->qty  : 0;
            $sell_two_arr[$product->id]['value'] = !empty($sell_two->value) ? $sell_two->value  : 0;
        }
        return view('report.comparison_report')->with(compact('sell_one_arr', 'sell_two_arr', 'products'));
    }
    public function getPreviousDayStock($request, $start_date, $end_date)
    {
        $balance = 0;
        $opening_stock = Account::getStockGroupAccountBalanceByTransactionType('opening_stock', $start_date, $end_date, true);
        $sale_return = Account::getStockGroupAccountBalanceByTransactionType('sell_return', $start_date, $end_date, true);
        $purchase_stock = abs(Account::getStockGroupAccountBalanceByTransactionType('purchase', $start_date, $end_date, true));
        $sold_stock = abs(Account::getStockGroupAccountBalanceByTransactionType('sell', $start_date, $end_date, true));
        $balance =   $opening_stock + ($sale_return + $purchase_stock) -  $sold_stock;
        return $balance;
    }
    public function getPreviousOpeningBalance($start_date, $end_date)
    {
        $business_id = request()->session()->get('user.business_id');
        $balance = 0;
        $opening_balance_equity_account_id = $this->transactionUtil->account_exist_return_id('Opening Balance Equity Account');
        if (!empty($opening_balance_equity_account_id)) {
            $account_query = Account::leftjoin(
                'account_transactions as AT',
                'AT.account_id',
                '=',
                'accounts.id'
            )->leftjoin('transactions', 'AT.transaction_id', 'transactions.id')
                ->leftjoin('purchase_lines', 'transactions.id', 'purchase_lines.transaction_id')
                ->whereNull('AT.deleted_at')
                ->where('transactions.type', 'opening_balance')
                ->where('accounts.business_id', $business_id)
                ->where('accounts.id', $opening_balance_equity_account_id);
            if (!empty($start_date) && !empty($end_date)) {
                $account_query->whereDate('operation_date', '<', $start_date);
            }
            $account_query->where('is_closed', 0);
            $account = $account_query->select(
                'accounts.*',
                DB::raw("SUM( IF(AT.type='debit',-1 * amount,  amount) ) as balance")
            )
                ->first();
            $balance += $account->balance;
        }
        return $balance;
    }
    public function getPrevioudDayBalance($start_date, $end_date, $location_id)
    {
        $business_id = request()->session()->get('user.business_id');
        $previous_day_balance = array(
            'cash' => 0.00,
            'card' => 0.00,
            'cheque' => 0.00,
            'credit_sale' => 0.00,
        );
        // opening balance received
        $received_outstanding_query_ob = Transaction::leftjoin('transaction_payments', 'transactions.id', 'transaction_payments.transaction_id')
            ->leftjoin('contacts', 'transactions.contact_id', 'contacts.id')
            ->where('transactions.business_id', $business_id)
            ->where('contacts.type', 'customer')
            ->where('transactions.type', 'opening_balance')
            ->whereIn('transactions.payment_status', ['paid', 'partial'])
            ->whereIn('transaction_payments.method', ['cash', 'cheque', 'card'])
            ->whereDate('transaction_payments.paid_on', '<', $start_date);
        if (!empty($location_id)) {
            $received_outstanding_query_ob->where('transactions.location_id', $location_id);
        }
        $total_received_outstanding_ob =  $received_outstanding_query_ob->select(
            DB::raw('SUM(IF(transaction_payments.method="cash", transaction_payments.amount, 0)) as cash'),
            DB::raw('SUM(IF(transaction_payments.method="card", transaction_payments.amount, 0)) as card'),
            DB::raw('SUM(IF(transaction_payments.method="cheque", transaction_payments.amount, 0)) as cheque'),
            DB::raw('SUM(transaction_payments.amount) as total_amount')
        )->first();
        $received_outstanding_query = Transaction::leftjoin('transaction_payments', 'transactions.id', 'transaction_payments.transaction_id')
            ->leftjoin('contacts', 'transactions.contact_id', 'contacts.id')
            ->where('transactions.business_id', $business_id)
            ->where('contacts.type', 'customer')
            ->where('is_credit_sale', 1)
            ->whereIn('transactions.payment_status', ['paid', 'partial'])
            ->whereIn('transaction_payments.method', ['cash', 'cheque', 'card'])
            ->whereDate('transaction_payments.paid_on', '<', $start_date);
        if (!empty($location_id)) {
            $received_outstanding_query->where('transactions.location_id', $location_id);
        }
        $total_received_outstanding =  $received_outstanding_query->select(
            DB::raw('SUM(IF(transaction_payments.method="cash", transaction_payments.amount, 0)) as cash'),
            DB::raw('SUM(IF(transaction_payments.method="card", transaction_payments.amount, 0)) as card'),
            DB::raw('SUM(IF(transaction_payments.method="cheque", transaction_payments.amount, 0)) as cheque'),
            DB::raw('SUM(transaction_payments.amount) as total_amount')
        )->first();
        // recieved amount with out settlement sale // related to pos sales
        $received_query = Transaction::leftjoin('transaction_payments', 'transactions.id', 'transaction_payments.transaction_id')
            ->where('transactions.business_id', $business_id)
            ->whereIn('transactions.type', ['sell', 'purchase_return'])
            ->whereIn('transactions.payment_status', ['paid', 'partial', 'due'])
            ->where('is_settlement', 0)
            ->where('transaction_payments.paid_on', '<', $start_date);
        if (!empty($location_id)) {
            $received_query->where('transactions.location_id', $location_id);
        }
        $received_result = $received_query->select(
            DB::raw('SUM(IF(transaction_payments.method="cash", transaction_payments.amount, 0)) as cash'),
            DB::raw('SUM(IF(transaction_payments.method="card", transaction_payments.amount, 0)) as card'),
            DB::raw('SUM(IF(transaction_payments.method="cheque", transaction_payments.amount, 0)) as cheque'),
            DB::raw('SUM(IF(transactions.payment_status="due" AND transactions.type="sell", transactions.final_total, 0)) as credit_sale')
        )->first();
        // recieved amount settlement sale 
        $settlement_received_query = Transaction::leftjoin('transaction_payments', 'transactions.id', 'transaction_payments.transaction_id')
            ->where('transactions.business_id', $business_id)
            ->whereIn('transactions.type', ['settlement', 'sell'])
            ->where('is_settlement', 1)
            ->where('is_credit_sale', 0)
            ->where('transaction_date', '<', $start_date);
        if (!empty($location_id)) {
            $settlement_received_query->where('transactions.location_id', $location_id);
        }
        $settlement_received_result = $settlement_received_query->select(
            DB::raw('SUM(IF(transactions.sub_type="cash_payment", transactions.final_total, 0)) as cash'),
            DB::raw('SUM(IF(transactions.sub_type="card_payment", transactions.final_total, 0)) as card'),
            DB::raw('SUM(IF(transactions.sub_type="cheque_payment", transactions.final_total, 0)) as cheque'),
            DB::raw('SUM(IF(transactions.payment_status="due" AND transactions.type="sell", transactions.final_total, 0)) as credit_sale')
        )->first();
        $cash_account_group = AccountGroup::getGroupByName('Cash Account');
        $cash_account_group_id = !empty($cash_account_group) ? $cash_account_group->id : null;
        $cheque_in_hand_account_group = AccountGroup::getGroupByName("Cheques in Hand (Customer's)");
        $cheque_in_hand_account_group_id = !empty($cheque_in_hand_account_group) ? $cheque_in_hand_account_group->id : null;
        $card_account_group = AccountGroup::getGroupByName('Card');
        $card_account_group_id = !empty($card_account_group) ? $card_account_group->id : null;
        $receiveable_account_id = $this->transactionUtil->account_exist_return_id('Accounts Receivable');
        $expense_in_settlement = Transaction::where('business_id', $business_id)->where('type', 'settlement')->where('sub_type', 'expense')
            ->whereDate('transaction_date', '<', $start_date);
        if (!empty($location_id)) {
            $expense_in_settlement->where('transactions.location_id', $location_id);
        }
        $expense_in_settlement =  $expense_in_settlement->sum('final_total');
        $cash_ob = Account::getAccountGroupOpeningBalanceByType($cash_account_group_id, 'debit', $start_date, $end_date);
        $cheque_ob = Account::getAccountGroupOpeningBalanceByType($cheque_in_hand_account_group_id, 'debit', $start_date, $end_date);
        $card_ob = Account::getAccountGroupOpeningBalanceByType($card_account_group_id, 'debit', $start_date, $end_date);
        $credit_sale_ob = Account::getAccountBalanceByType($receiveable_account_id, 'debit', $start_date, $end_date, false, true);
        $received['credit_sale'] = Account::getAccountBalanceByType($receiveable_account_id, 'debit', $start_date, $end_date, true) + $credit_sale_ob;
        $received['cash'] = Account::getAccountGroupBalanceByType($cash_account_group_id, 'debit', $start_date, $end_date, true) - $expense_in_settlement + $cash_ob;
        $received['cheque'] = Account::getAccountGroupBalanceByType($cheque_in_hand_account_group_id, 'debit', $start_date, $end_date, true) + $cheque_ob;
        $received['card'] = Account::getAccountGroupBalanceByType($card_account_group_id, 'debit', $start_date, $end_date, true) + $card_ob;
        $shortage_recover['cash'] = $this->preDaySettlementQuery($start_date, $location_id)->where('sub_type', 'shortage')->where('transaction_payments.method', 'cash')->sum('transaction_payments.amount');
        $shortage_recover['card'] = $this->preDaySettlementQuery($start_date, $location_id)->where('sub_type', 'shortage')->where('transaction_payments.method', 'card')->sum('transaction_payments.amount');
        $shortage_recover['cheque'] = $this->preDaySettlementQuery($start_date, $location_id)->where('sub_type', 'shortage')->where('transaction_payments.method', 'cheque')->sum('transaction_payments.amount');
        $excess_commission['cash'] = $this->preDaySettlementQuery($start_date, $location_id)->where('sub_type', 'excess')->where('transaction_payments.method', 'cash')->sum('transaction_payments.amount');
        $excess_commission['card'] = $this->preDaySettlementQuery($start_date, $location_id)->where('sub_type', 'excess')->where('transaction_payments.method', 'card')->sum('transaction_payments.amount');
        $excess_commission['cheque'] = $this->preDaySettlementQuery($start_date, $location_id)->where('sub_type', 'excess')->where('transaction_payments.method', 'cheque')->sum('transaction_payments.amount');
        $cash_account = $this->transactionUtil->account_exist_return_id('Cash');
        $cheque_account = $this->transactionUtil->account_exist_return_id('Cheques in Hand');
        $card_account = $this->transactionUtil->account_exist_return_id('Cards (Credit Debit) Account');
        $card_group_id = AccountGroup::getGroupByName('Card', true);
        $deposit['cash'] = AccountTransaction::where('account_id', $cash_account)->where('type', 'credit')
            ->where('operation_date', '<', $start_date)
            ->where('sub_type', 'deposit')->sum('amount');
        $deposit['cheque'] = AccountTransaction::where('account_id', $cheque_account)->where('type', 'credit')
            ->where('operation_date', '<', $start_date)
            ->where('sub_type', 'deposit')->sum('amount');
        $deposit['card'] = AccountTransaction::leftjoin('accounts', 'account_transactions.account_id', 'accounts.id')->where('asset_type', $card_group_id)
            ->where('type', 'credit')
            ->where('operation_date', '<', $start_date)
            ->whereIn('sub_type', ['deposit', 'fund_transfer'])->sum('amount');
        $petty_cash_account_id = $this->transactionUtil->account_exist_return_id('Petty Cash');
        $direct_cash_expenses_query = Transaction::where('transactions.business_id', $business_id)->where('type', 'expense')
            ->leftjoin('transaction_payments', 'transactions.id', 'transaction_payments.transaction_id')
            ->where('transaction_payments.account_id', '!=', $petty_cash_account_id)
            ->where('transaction_payments.method', 'cash')
            ->whereIn('transactions.payment_status', ['paid', 'partial'])
            ->whereDate('transactions.transaction_date', '<', $start_date);
        if (!empty($location_id)) {
            $direct_cash_expenses_query->where('transactions.location_id', $location_id);
        }
        $direct_cash_expenses = $direct_cash_expenses_query->sum('transaction_payments.amount');
        $purchase_by_cash = 0;
        $purchase_by_cash = Transaction::leftjoin('transaction_payments', 'transactions.id', 'transaction_payments.transaction_id')
            ->where('type', 'purchase')->whereIn('payment_status', ['partial', 'paid'])->where('transaction_payments.method', 'cash')
            ->where('transactions.business_id', $business_id)
            ->whereDate('transactions.transaction_date', '<', $start_date);
        if (!empty($location_id)) {
            $purchase_by_cash->where('transactions.location_id', $location_id);
        }
        $purchase_by_cash = $purchase_by_cash->sum('transaction_payments.amount');
        $supplier_ob_by_cash = 0;
        $supplier_ob_by_cash = Transaction::leftjoin('contacts', 'transactions.contact_id', 'contacts.id')
            ->leftjoin('transaction_payments', 'transactions.id', 'transaction_payments.transaction_id')
            ->where('contacts.type', 'supplier')
            ->where('transactions.type', 'opening_balance')->whereIn('payment_status', ['partial', 'paid'])->where('transaction_payments.method', 'cash')
            ->where('transactions.business_id', $business_id)
            ->whereDate('transactions.transaction_date', '<=', $start_date);
        if (!empty($location_id)) {
            $supplier_ob_by_cash->where('transactions.location_id', $location_id);
        }
        $supplier_ob_by_cash = $supplier_ob_by_cash->sum('transaction_payments.amount');
        $total_purchase_by_cash = $purchase_by_cash + $supplier_ob_by_cash;
        $previous_day_balance['cash'] =   $received['cash'] + $shortage_recover['cash'] - ($excess_commission['cash'] + $deposit['cash'] + $direct_cash_expenses + $total_purchase_by_cash);
        $previous_day_balance['card'] =  $received['card'] + $shortage_recover['card'] - ($excess_commission['card'] + $deposit['card']);
        $previous_day_balance['cheque'] =  $received['cheque'] + $shortage_recover['cheque'] - ($excess_commission['cheque'] + $deposit['cheque']);
        $previous_day_balance['credit_sale'] =  $received['credit_sale'];
        return $previous_day_balance;
    }
    public function preDaySettlementQuery($start_date, $location_id)
    {
        $business_id = request()->session()->get('user.business_id');
        $query = Transaction::leftjoin('transaction_payments', 'transactions.id', 'transaction_payments.transaction_id')
            ->where('transactions.business_id', $business_id)->where('type', 'settlement')
            ->whereIn('transactions.payment_status', ['paid', 'partial'])
            ->whereDate('transaction_date', '<', $start_date);
        if (!empty($location_id)) {
            $query->where('transactions.location_id', $location_id);
        }
        return $query;
    }
    public function getPreviousDayOutstanding($start_date, $end_date, $location_id, $work_shift_id)
    {
        $outstandings = array(
            'opening_balance' => 0.00,
            'previous_day' => 0.00,
            'given' => 0.00,
            'received' => 0.00,
            'balance' => 0.00
        );
        $outstandings['opening_balance'] =  $this->outstandingGivenQuery($location_id)->where(function ($q) {
            $q->where('transactions.type', 'opening_balance');
        })
            ->whereDate('transactions.transaction_date', '>=', $start_date)
            ->whereDate('transactions.transaction_date', '<=', $end_date)
            ->sum('final_total');
        $outstandings['previous_given'] =  $this->outstandingGivenQuery($location_id)->where(function ($q) {
            $q->where('transactions.type', 'opening_balance')->orWhere('is_credit_sale', 1);
        })->whereDate('transactions.transaction_date', '<', $start_date)->sum('final_total');
        $customer_payments_pre =  $this->customerPaymenInSettlements($location_id, $work_shift_id)->whereDate('transaction_date', '<', $start_date)->sum('amount');
        $outstandings['previous_received'] =  $this->outstandingReceivedQuery($location_id)->where(function ($q) {
            $q->where('transactions.type', 'opening_balance')->orWhere('is_credit_sale', 1);
        })->whereDate('transactions.transaction_date', '<', $start_date)
            ->whereIn('payment_status', ['paid', 'partial'])->sum('transaction_payments.amount') + $customer_payments_pre;
        $outstandings['previous_day'] = $outstandings['opening_balance'] + $outstandings['previous_given'] - $outstandings['previous_received'];
        $outstandings['given'] =  $this->outstandingGivenQuery($location_id)->where('is_credit_sale', 1)
            ->whereDate('transactions.transaction_date', '>=', $start_date)
            ->whereDate('transactions.transaction_date', '<=', $end_date)->sum('final_total');
        $customer_payments =  $this->customerPaymenInSettlements($location_id, $work_shift_id)->whereDate('transaction_date', '>=', $start_date)->whereDate('transaction_date', '<=', $end_date)->sum('amount');
        $outstandings['received'] = $this->outstandingReceivedQuery($location_id)->where(function ($q) {
            $q->where('transactions.type', 'opening_balance')->orWhere('transactions.is_credit_sale', 1);
        })
            ->whereDate('transaction_date', '>=', $start_date)->whereDate('transaction_date', '<=', $end_date)
            ->whereIn('payment_status', ['paid', 'partial'])->sum('transaction_payments.amount') +  $customer_payments;
        $outstandings['balance'] =  $outstandings['previous_day'] +  $outstandings['given'] - $outstandings['received'];
        return $outstandings;
    }
    public function outstandingGivenQuery($location_id)
    {
        $business_id = request()->session()->get('user.business_id');
        $outstanding_query = Transaction::leftjoin('contacts', 'transactions.contact_id', 'contacts.id')
            ->leftjoin('settlements', function ($query) use ($business_id) {
                $query->on('transactions.invoice_no', 'settlements.settlement_no')->where('settlements.business_id', $business_id);
            })
            ->where('contacts.type', 'customer')
            ->where('transactions.business_id', $business_id);
        if (!empty($work_shift_id)) {
            $outstanding_query->whereJsonContains('settlements.work_shift', $work_shift_id);
        }
        if (!empty($location_id)) {
            $outstanding_query->where('transactions.location_id', $location_id);
        }
        return $outstanding_query;
    }
    public function customerPaymenInSettlements($location_id, $work_shift_id)
    {
        // payments done in settlement
        /* added on 7/4/2020 discuss on skype */
        $business_id = request()->session()->get('user.business_id');
        $customer_payments_tab_query = CustomerPayment::leftjoin('settlements', 'customer_payments.settlement_no', 'settlements.id')
            ->where('settlements.business_id', $business_id);
        if (!empty($location_id)) {
            $customer_payments_tab_query->where('settlements.location_id', $location_id);
        }
        if (!empty($work_shift_id)) {
            $customer_payments_tab_query->whereJsonContains('settlements.work_shift', $work_shift_id);
        }
        return $customer_payments_tab_query;
    }
    public function outstandingReceivedQuery($location_id)
    {
        $business_id = request()->session()->get('user.business_id');
        $outstanding_query = Transaction::leftjoin('transaction_payments', 'transactions.id', 'transaction_payments.transaction_id')
            ->leftjoin('contacts', 'transactions.contact_id', 'contacts.id')
            ->where('contacts.type', 'customer')
            ->where('transactions.business_id', $business_id)
            ->where('final_total', '>', 0);
        if (!empty($location_id)) {
            $outstanding_query->where('transactions.location_id', $location_id);
        }
        return $outstanding_query;
    }
    public function getPreviousDayPumpOperatorShortage($start_date, $end_date, $location_id)
    {
        $pump_operator_shortages = array(
            'previous_day' => 0.00,
            'given' => 0.00,
            'received' => 0.00,
            'balance' => 0.00
        );
        $pump_operator_shortages['previous_given'] =  $this->pumpOperatorShortageQuery($location_id)->whereDate('transaction_date', '<', $start_date)->sum('final_total');
        $pump_operator_shortages['previous_received'] =  $this->pumpOperatorShortageRecoveredQuery($location_id)->whereDate('transaction_date', '<', $start_date)->whereIn('payment_status', ['paid', 'partial'])->sum('transaction_payments.amount');
        $pump_operator_shortages['previous_day'] = $pump_operator_shortages['previous_given'] - $pump_operator_shortages['previous_received'];
        $pump_operator_shortages['given'] =  $this->pumpOperatorShortageQuery($location_id)->whereDate('transaction_date', '>=', $start_date)
            ->whereDate('transaction_date', '<=', $end_date)->sum('final_total');
        $pump_operator_shortages['received'] = $this->pumpOperatorShortageRecoveredQuery($location_id)->whereDate('transaction_date', '>=', $start_date)
            ->whereDate('transaction_date', '<=', $end_date)->whereIn('payment_status', ['paid', 'partial'])->sum('transaction_payments.amount');
        $pump_operator_shortages['balance'] = $pump_operator_shortages['previous_day'] +  $pump_operator_shortages['given'] - $pump_operator_shortages['received'];
        return $pump_operator_shortages;
    }
    public function pumpOperatorShortageRecoveredQuery($location_id)
    {
        $business_id = request()->session()->get('user.business_id');
        $shortage_query = Transaction::leftjoin('transaction_payments', 'transactions.id', 'transaction_payments.transaction_id')
            ->leftjoin('pump_operators', 'transactions.pump_operator_id', 'pump_operators.id')
            ->where('transactions.business_id', $business_id)
            ->where('transactions.sub_type', 'shortage')
            ->where('pump_operator_id', '!=', null);
        if (!empty($location_id)) {
            $shortage_query->where('transactions.location_id', $location_id);
        }
        return $shortage_query;
    }
    public function pumpOperatorShortageQuery($location_id)
    {
        $business_id = request()->session()->get('user.business_id');
        $shortage_query = Transaction::leftjoin('pump_operators', 'transactions.pump_operator_id', 'pump_operators.id')
            ->where('transactions.business_id', $business_id)
            ->where('transactions.sub_type', 'shortage')
            ->where('pump_operator_id', '!=', null);
        if (!empty($location_id)) {
            $shortage_query->where('transactions.location_id', $location_id);
        }
        return $shortage_query;
    }
    public function getPreviousDayPumpOperatorExcess($start_date, $end_date, $location_id)
    {
        $pump_operator_excess = array(
            'previous_day' => 0.00,
            'given' => 0.00,
            'received' => 0.00,
            'balance' => 0.00
        );
        $pump_operator_excess['previous_given'] =  $this->pumpOperatorExcessQuery($location_id)->whereDate('transaction_date', '<', $start_date)->sum('final_total');
        $pump_operator_excess['previous_received'] =  $this->pumpOperatorExcessPaidQuery($location_id)->whereDate('transaction_date', '<', $start_date)->whereIn('payment_status', ['paid', 'partial'])->sum('transaction_payments.amount');
        $pump_operator_excess['previous_day'] = $pump_operator_excess['previous_given'] - $pump_operator_excess['previous_received'];
        $pump_operator_excess['given'] =  $this->pumpOperatorExcessQuery($location_id)->whereDate('transaction_date', '>=', $start_date)
            ->whereDate('transaction_date', '<=', $end_date)->sum('final_total');
        $pump_operator_excess['received'] = $this->pumpOperatorExcessPaidQuery($location_id)->where('transaction_date', '>=', $start_date)
            ->where('transaction_date', '<=', $end_date)->whereIn('payment_status', ['paid', 'partial'])->sum('transaction_payments.amount');
        $pump_operator_excess['balance'] = $pump_operator_excess['previous_day'] +  $pump_operator_excess['given'] - $pump_operator_excess['received'];
        return $pump_operator_excess;
    }
    public function pumpOperatorExcessPaidQuery($location_id)
    {
        $business_id = request()->session()->get('user.business_id');
        $excess_query = Transaction::leftjoin('transaction_payments', 'transactions.id', 'transaction_payments.transaction_id')
            ->leftjoin('pump_operators', 'transactions.pump_operator_id', 'pump_operators.id')
            ->where('transactions.business_id', $business_id)
            ->where('transactions.sub_type', 'excess')
            ->where('pump_operator_id', '!=', null);
        if (!empty($location_id)) {
            $excess_query->where('transactions.location_id', $location_id);
        }
        return $excess_query;
    }
    public function pumpOperatorExcessQuery($location_id)
    {
        $business_id = request()->session()->get('user.business_id');
        $excess_query = Transaction::leftjoin('pump_operators', 'transactions.pump_operator_id', 'pump_operators.id')
            ->where('transactions.business_id', $business_id)
            ->where('transactions.sub_type', 'excess')
            ->where('pump_operator_id', '!=', null);
        if (!empty($location_id)) {
            $excess_query->where('transactions.location_id', $location_id);
        }
        return $excess_query;
    }
    public function getDailyReportDetailsView(Request $request, $type)
    {
        $start_date = $request->start;
        $end_date = $request->end;
        $business_id = $request->session()->get('user.business_id');
        $location_id = null;
        if ($type != 7) {
            $data = $this->transactionUtil->getSellTotalsByPaymentType(
                $business_id,
                $start_date,
                $end_date,
                $location_id,
                $type
            );
        }
        if ($type == 7) {
            $data = $this->getStockTotal($business_id);
        }
        $due_data = Transaction::leftJoin('contacts', 'transactions.contact_id', '=', 'contacts.id')
            ->leftJoin('transaction_payments as tp', 'transactions.id', '=', 'tp.transaction_id')
            ->join(
                'business_locations AS bl',
                'transactions.location_id',
                '=',
                'bl.id'
            )
            ->where('transactions.business_id', $business_id)
            ->where('transactions.type', 'sell')
            ->where('transactions.payment_status', 'final')
            ->whereDate('transactions.transaction_date', '>=', $start_date)
            ->whereDate('transactions.transaction_date', '<=', $end_date)
            ->select(
                DB::raw('SUM(tp.amount) as today_settled'),
                DB::raw('SUM(tp.amount) as today_due'),
                DB::raw('SUM(IF(transactions.transaction_date < ' . date($start_date) . ', tp.amount, 0)) as opening_due')
            )->first();
        $payment_types = DB::table('payment_methods')->where('business_id', $business_id)->select('*')->get();
        $user_details = User::where('business_id', $business_id)->first();
        return view('report.partials.daily_report_view')
            ->with(compact('user_details', 'payment_types', 'type', 'start_date', 'end_date', 'data', 'due_data'));
    }
    public function getStockTotal($business_id)
    {
        $query = Variation::join('products as p', 'p.id', '=', 'variations.product_id')
            ->join('units', 'p.unit_id', '=', 'units.id')
            ->leftjoin('variation_location_details as vld', 'variations.id', '=', 'vld.variation_id')
            ->join('product_variations as pv', 'variations.product_variation_id', '=', 'pv.id')
            ->where('p.business_id', $business_id)
            ->whereIn('p.type', ['single', 'variable']);
        $permitted_locations = auth()->user()->permitted_locations();
        $location_filter = '';
        if ($permitted_locations != 'all') {
            $query->whereIn('vld.location_id', $permitted_locations);
            $locations_imploded = implode(', ', $permitted_locations);
            $location_filter .= "AND transactions.location_id IN ($locations_imploded) ";
        }
        //TODO::Check if result is correct after changing LEFT JOIN to INNER JOIN
        $pl_query_string = $this->productUtil->get_pl_quantity_sum_string('pl');
        $products = $query->select(
            // DB::raw("(SELECT SUM(quantity) FROM transaction_sell_lines LEFT JOIN transactions ON transaction_sell_lines.transaction_id=transactions.id WHERE transactions.status='final' $location_filter AND
            //     transaction_sell_lines.product_id=products.id) as total_sold"),
            DB::raw("(SELECT SUM(TSL.quantity - TSL.quantity_returned) FROM transactions 
        JOIN transaction_sell_lines AS TSL ON transactions.id=TSL.transaction_id
        WHERE transactions.status='final' AND transactions.type='sell' $location_filter 
        AND TSL.variation_id=variations.id) as total_sold"),
            DB::raw("(SELECT SUM(IF(transactions.type='sell_transfer', TSL.quantity, 0) ) FROM transactions 
        JOIN transaction_sell_lines AS TSL ON transactions.id=TSL.transaction_id
        WHERE transactions.status='final' AND transactions.type='sell_transfer' $location_filter 
        AND (TSL.variation_id=variations.id)) as total_transfered"),
            DB::raw("(SELECT SUM(IF(transactions.type='stock_adjustment', SAL.quantity, 0) ) FROM transactions 
        JOIN stock_adjustment_lines AS SAL ON transactions.id=SAL.transaction_id
        WHERE transactions.status='received' AND transactions.type='stock_adjustment' $location_filter 
        AND (SAL.variation_id=variations.id)) as total_adjusted"),
            DB::raw("(SELECT SUM( COALESCE(pl.quantity - ($pl_query_string), 0) * purchase_price_inc_tax) FROM transactions 
        JOIN purchase_lines AS pl ON transactions.id=pl.transaction_id
        WHERE transactions.status='received' $location_filter 
        AND (pl.variation_id=variations.id)) as stock_price"),
            DB::raw("SUM(vld.qty_available) as stock"),
            'variations.sub_sku as sku',
            'p.name as product',
            'p.type',
            'p.id as product_id',
            'units.short_name as unit',
            'p.enable_stock as enable_stock',
            'variations.sell_price_inc_tax as unit_price',
            'pv.name as product_variation',
            'variations.name as variation_name'
        )->groupBy('variations.id')->get();
        $total_stock_price = 0;
        foreach ($products as $item) {
            $total_stock_price += $item->stock_price;
        }
        $data = $total_stock_price;
        return $data;
    }
    public function getUserActivityReport(Request $request)
    {
        if (!auth()->user()->can('user_activity.view')) {
            abort(403, 'Unauthorized action.');
        }
        $business_id = request()->session()->get('user.business_id');
        if (request()->ajax()) {
            $with = [];
            $shipping_statuses = $this->transactionUtil->shipping_statuses();
            $business_users = User::where('business_id', $business_id)->pluck('id')->toArray();
            $activity =  Activity::whereIn('causer_id', $business_users);
            if (!empty(request()->user)) {
                $user = request()->user;
                $activity->where('causer_id', $user);
            }
            $datatable = Datatables::of($activity)
                ->editColumn('created_at', '{{@format_datetime($created_at)}}')
                ->removeColumn('id')
                ->editColumn('causer_id', function ($row) {
                    $causer_id = $row->causer_id;
                    $username = User::where('id', $causer_id)->select('username')->first()->username;
                    return $username;
                });
            $rawColumns = [];
            return $datatable->rawColumns($rawColumns)
                ->make(true);
        }
        $users = User::where('business_id', $business_id)->pluck('username', 'id');
        return view('report.user_activity')
            ->with(compact('users'));
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
    public function getCreditStatusTotalsReport()
    {
        if (request()->ajax()) {
            $start = request()->start;
            $end = request()->end;
            $location_id = request()->location_id;
            $business_id = request()->session()->get('user.business_id');
            $transaction_types = [
                'credit_sales_details'
            ];
            $transaction_totals = $this->transactionUtil->getTransactionTotals(
                $business_id,
                $transaction_types,
                $start,
                $end,
                $location_id
            );
            return $transaction_totals;
        }
    }
}
