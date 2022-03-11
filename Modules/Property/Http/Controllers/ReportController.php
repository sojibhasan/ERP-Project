<?php

namespace Modules\Property\Http\Controllers;

use App\Account;
use App\AccountGroup;
use App\AccountTransaction;
use App\BusinessLocation;
use App\ExpenseCategory;
use App\Transaction;
use App\TransactionPayment;
use App\User;
use App\Utils\TransactionUtil;
use Carbon\Carbon;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Modules\HR\Entities\WorkShift;
use Modules\Property\Entities\PropertyBlock;
use Yajra\DataTables\Facades\DataTables;

class ReportController extends Controller
{
    protected $transactionUtil;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(TransactionUtil $transactionUtil)
    {
        $this->transactionUtil = $transactionUtil;
    }

    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index(Request $request)
    {
        $business_id = $request->session()->get('user.business_id');

        $business_locations = BusinessLocation::forDropdown($business_id);

        $work_shifts = WorkShift::where('business_id', $business_id)->pluck('shift_name', 'id');

        $report_daily = 1;

        return view('property::report.index')->with(compact(
            'report_daily',
            'business_locations',
            'work_shifts'
        ));
    }

    public function getDailyReport(Request $request)
    {
        $business_id = $request->session()->get('user.business_id');

        $default_start = new Carbon('first day of last month');
        $default_end = new Carbon('last day of last month');

        $start_date = !empty($request->start_date) ? Carbon::parse($request->start_date)->format('Y-m-d') : $default_start->format('Y-m-d');
        $end_date = !empty($request->end_date) ? Carbon::parse($request->end_date)->format('Y-m-d') : $default_end->format('Y-m-d');

        $day_diff = !empty($request->start_date) && !empty($request->end_date) ? Carbon::parse($request->start_date)->diffInDays(Carbon::parse($request->end_date)) : 0;

        $print_s_date = $this->transactionUtil->format_date($start_date);
        $print_e_date = $this->transactionUtil->format_date($end_date);

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

        $sell_details =  Transaction::leftjoin('property_sell_lines', 'transactions.id', 'property_sell_lines.transaction_id')
            ->leftjoin('properties', 'property_sell_lines.property_id', 'properties.id')
            ->leftjoin('property_blocks', 'property_sell_lines.block_id', 'property_blocks.id')
            ->where('transactions.business_id', $business_id)
            ->where('transactions.type', 'property_sell')
            ->whereDate('transactions.transaction_date', '>=', $start_date)
            ->whereDate('transactions.transaction_date', '<=', $end_date)
            ->select(
                'properties.name as property_name',
                DB::raw('count(*) as total_blocks_sold'),
                DB::raw('sum(property_sell_lines.block_value) as total_amount')
            )
            ->groupBy('properties.id');

        if (!empty($location_id)) {
            $sell_details->where('transactions.location_id', $location_id);
        }
        $sales = $sell_details->get();

        $previous_day_balance = $this->getPreviousDayBalance($start_date, $end_date, $location_id);

        $receiveable_account_id = $this->transactionUtil->account_exist_return_id('Accounts Receivable');

        $cash_account_group = AccountGroup::getGroupByName('Cash Account');
        $cash_account_group_id = !empty($cash_account_group) ? $cash_account_group->id : null;

        $cheque_in_hand_account_group = AccountGroup::getGroupByName("Cheques in Hand (Customer's)");
        $cheque_in_hand_account_group_id = !empty($cheque_in_hand_account_group) ? $cheque_in_hand_account_group->id : null;

        $card_account_group = AccountGroup::getGroupByName('Card');
        $card_account_group_id = !empty($card_account_group) ? $card_account_group->id : null;

        $expense_in_settlement = Transaction::where('business_id', $business_id)->where('type', 'settlement')->where('sub_type', 'expense')
            ->whereDate('transaction_date', '>=', $start_date)
            ->whereDate('transaction_date', '<=', $end_date);

        if (!empty($location_id)) {
            $expense_in_settlement->where('transactions.location_id', $location_id);
        }

        $expense_in_settlement =  $expense_in_settlement->sum('final_total');

        $received['cash'] = Account::getAccountGroupBalanceByType($cash_account_group_id, 'debit', $start_date, $end_date) - $expense_in_settlement;

        $received['cheque'] = Account::getAccountGroupBalanceByType($cheque_in_hand_account_group_id, 'debit', $start_date, $end_date);

        $received['card'] = Account::getAccountGroupBalanceByType($card_account_group_id, 'debit', $start_date, $end_date);
        
        $received['due'] = Account::getAccountGroupBalanceByType($card_account_group_id, 'debit', $start_date, $end_date);

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

        $purchase_by_cash = $purchase_by_cash->sum('transaction_payments.amount');

        $supplier_ob_by_cash = Transaction::leftjoin('contacts', 'transactions.contact_id', 'contacts.id')
            ->leftjoin('transaction_payments', 'transactions.id', 'transaction_payments.transaction_id')
            ->where('contacts.type', 'supplier')
            ->where('transactions.type', 'opening_balance')->whereIn('payment_status', ['partial', 'paid'])->where('transaction_payments.method', 'cash')
            ->where('transactions.business_id', $business_id);

        if (!empty($location_id)) {
            $supplier_ob_by_cash->where('transactions.location_id', $location_id);
        }

        $transIds = $supplier_ob_by_cash->pluck('transactions.id');

        $obsum = TransactionPayment::whereIn('transaction_id', $transIds)
            ->where('method', 'cash')
            ->whereDate('paid_on', '>=', $start_date)
            ->whereDate('paid_on', '<=', $end_date);
        $supplier_ob_by_cash = $obsum->sum('amount');

        $total_purchase_by_cash = $purchase_by_cash + $supplier_ob_by_cash;

        $cash_account = $this->transactionUtil->account_exist_return_id('Cash');
        $cheque_account = $this->transactionUtil->account_exist_return_id('Cheques in Hand');
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

        $deposit['due'] = AccountTransaction::leftjoin('accounts', 'account_transactions.account_id', 'accounts.id')->where('asset_type', $card_group_id)
            ->where('type', 'credit')
            ->whereDate('operation_date', '>=', $start_date)
            ->whereDate('operation_date', '<=', $end_date)
            ->whereIn('sub_type', ['deposit', 'fund_transfer'])->sum('amount');

        $balance['cash'] = $previous_day_balance['cash'] + $received['cash'] - ($deposit['cash'] + $direct_cash_expenses + $total_purchase_by_cash);
        $balance['card'] = $previous_day_balance['card'] + $received['card']  - $deposit['card'];
        $balance['cheque'] = $previous_day_balance['cheque'] + $received['cheque'] - $deposit['cheque'];
        $balance['due'] = $previous_day_balance['due'] + $received['due'] - $deposit['due'];

        // expense categories
        $expense_categories = ExpenseCategory::leftJoin('transactions', 'transactions.expense_category_id', '=', 'expense_categories.id')
            ->leftJoin('transaction_payments', 'transaction_payments.transaction_id', '=', 'transactions.id')
            ->whereDate('transactions.transaction_date', '>=', $start_date)
            ->whereDate('transactions.transaction_date', '<=', $end_date);

        if (!empty($location_id) && !is_null($location_id)) {
            $expense_categories->where('transactions.location_id', $location_id);
        }
        $expense_categories = $expense_categories->groupBy('expense_categories.id')
            ->select(
                'expense_categories.name as category_name',
                DB::RAW('sum(amount) as amount'),
                'method as payment_method'
            )
            ->get();

        // list price changes

        $priceChanges = Transaction::with('property_sell_lines')->wherebusinessId($business_id)->wheretype('property_sell');
        // die;
        // $priceChanges = Transaction::leftJoin('property_sell_lines', 'transactions.id', 'property_sell_lines.transaction_id')
        //     ->leftJoin('properties', 'property_sell_lines.property_id', 'properties.id')
        //     ->leftJoin('property_blocks', 'property_sell_lines.block_id', 'property_blocks.id')
        //     ->leftJoin('customers', 'property_blocks.customer_id', 'customers.id')
        //     ->leftJoin('users as u', 'transactions.created_by', '=', 'u.id')
        //     ->leftJoin('users as commission_approved_by', 'property_blocks.commission_approved_by', '=', 'commission_approved_by.id')
        //     ->leftJoin('property_account_settings', 'properties.id', '=', 'property_account_settings.property_id')
        //     ->where('transactions.business_id', $business_id)
        //     ->where('transactions.type', 'property_sell')
        //     ->select(
        //         'properties.id as property_id',
        //         'properties.name as property_name',
        //         'property_blocks.block_number as block_number',
        //         'property_sell_lines.size',
        //         'property_sell_lines.unit',
        //         'customers.first_name as customer_name',
        //         'property_blocks.block_sale_price as sale_price',
        //         'property_blocks.block_sold_price as sold_price',
        //         'property_blocks.sale_commission as commission',
        //         'property_blocks.commission_approval as commission_approval',
        //         'property_blocks.commission_status as commission_status',
        //         'transactions.id',
        //         'transactions.transaction_date',
        //         DB::raw("CONCAT(COALESCE(commission_approved_by.surname, ''),' ',COALESCE(commission_approved_by.first_name, ''),' ',COALESCE(commission_approved_by.last_name,'')) as commission_approved_by"),
        //         DB::raw("CONCAT(COALESCE(u.surname, ''),' ',COALESCE(u.first_name, ''),' ',COALESCE(u.last_name,'')) as added_by")
        //     );

        $permitted_locations = auth()->user()->permitted_locations();
        if ($permitted_locations != 'all') {
            // $priceChanges->whereIn('transactions.location_id', $permitted_locations);
            $priceChanges->whereIn('location_id', $permitted_locations);
        }

        if (!is_null(request()->location_id) && !empty(request()->location_id)) {
            // $priceChanges->where('transactions.location_id', request()->location_id);
            $priceChanges->where('location_id', request()->location_id);
        }

        if (!empty(request()->start_date) && !empty(request()->end_date)) {
            $start = request()->start_date;
            $end =  request()->end_date;
            // $priceChanges->whereDate('transactions.transaction_date', '>=', $start)
            //     ->whereDate('transactions.transaction_date', '<=', $end);
            $priceChanges->where('transaction_date', '>=', $start)
                ->where('transaction_date', '<=', $end);
        }
        $price_changes = $priceChanges->get();

        return view('property::report.daily_report')->with(compact(
            'print_s_date',
            'print_e_date',
            'start_date',
            'end_date',
            'day_diff',
            'location_id',
            'work_shift_id',
            'location_details',
            'work_shift',
            'sales',
            'previous_day_balance',
            'received',
            'direct_cash_expenses',
            'purchase_by_cash',
            'total_purchase_by_cash',
            'deposit',
            'balance',
            'expense_categories',
            'price_changes'
        ));
    }

    public function getPreviousDayBalance($start_date, $end_date, $location_id)
    {
        $business_id = request()->session()->get('user.business_id');

        $previous_day_balance = array(
            'cash' => 0.00,
            'card' => 0.00,
            'cheque' => 0.00,
            'due' => 0.00
        );

        // $due = Transaction::where('payment_status')
        // opening balance received
        $received_outstanding_query_ob = Transaction::leftjoin('transaction_payments', 'transactions.id', 'transaction_payments.transaction_id')
            ->leftjoin('contacts', 'transactions.contact_id', 'contacts.id')
            ->where('transactions.business_id', $business_id)
            ->where('contacts.type', 'customer')
            ->where('transactions.type', 'opening_balance')
            ->whereIn('transactions.payment_status', ['paid', 'partial' , 'due'])
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
            ->whereIn('transactions.payment_status', ['paid', 'partial' , 'due'])
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
            ->where('transactions.type', 'property_sell')
            ->whereIn('transactions.payment_status', ['paid', 'partial', 'due'])
            ->where('is_settlement', 0)
            ->where('transaction_payments.paid_on', '<', $start_date);
        if (!empty($location_id)) {
            $received_query->where('transactions.location_id', $location_id);
        }

        $received_result = $received_query->select(
            DB::raw('SUM(IF(transaction_payments.method="cash", transaction_payments.amount, 0)) as cash'),
            DB::raw('SUM(IF(transaction_payments.method="card", transaction_payments.amount, 0)) as card'),
            DB::raw('SUM(IF(transaction_payments.method="cheque", transaction_payments.amount, 0)) as cheque')
        )->first();

        // recieved amount settlement sale
        $settlement_received_query = Transaction::leftjoin('transaction_payments', 'transactions.id', 'transaction_payments.transaction_id')
            ->where('transactions.business_id', $business_id)
            ->where('transactions.type', 'property_sell')
            ->where('is_settlement', 1)
            ->where('is_credit_sale', 0)
            ->where('transaction_date', '<', $start_date);
        if (!empty($location_id)) {
            $settlement_received_query->where('transactions.location_id', $location_id);
        }

        $settlement_received_result = $settlement_received_query->select(
            DB::raw('SUM(IF(transactions.sub_type="cash_payment", transactions.final_total, 0)) as cash'),
            DB::raw('SUM(IF(transactions.sub_type="card_payment", transactions.final_total, 0)) as card'),
            DB::raw('SUM(IF(transactions.sub_type="cheque_payment", transactions.final_total, 0)) as cheque')
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
       
        $due_ob = Account::getAccountGroupOpeningBalanceByType($card_account_group_id, 'debit', $start_date, $end_date);

        $credit_sale_ob = Account::getAccountBalanceByType($receiveable_account_id, 'debit', $start_date, $end_date, false, true);


        $received['cash'] = Account::getAccountGroupBalanceByType($cash_account_group_id, 'debit', $start_date, $end_date, true) - $expense_in_settlement + $cash_ob;

        $received['cheque'] = Account::getAccountGroupBalanceByType($cheque_in_hand_account_group_id, 'debit', $start_date, $end_date, true) + $cheque_ob;

        $received['card'] = Account::getAccountGroupBalanceByType($card_account_group_id, 'debit', $start_date, $end_date, true) + $card_ob;
        $received['due'] = Account::getAccountGroupBalanceByType($card_account_group_id, 'debit', $start_date, $end_date, true) + $card_ob;


        $shortage_recover['cash'] = $this->preDaySettlementQuery($start_date, $location_id)->where('sub_type', 'shortage')->where('transaction_payments.method', 'cash')->sum('transaction_payments.amount');

        $shortage_recover['card'] = $this->preDaySettlementQuery($start_date, $location_id)->where('sub_type', 'shortage')->where('transaction_payments.method', 'card')->sum('transaction_payments.amount');

        $shortage_recover['cheque'] = $this->preDaySettlementQuery($start_date, $location_id)->where('sub_type', 'shortage')->where('transaction_payments.method', 'cheque')->sum('transaction_payments.amount');
        $shortage_recover['due'] = $this->preDaySettlementQuery($start_date, $location_id)->where('sub_type', 'shortage')->where('transaction_payments.method', 'cheque')->sum('transaction_payments.amount');




        $excess_commission['cash'] = $this->preDaySettlementQuery($start_date, $location_id)->where('sub_type', 'excess')->where('transaction_payments.method', 'cash')->sum('transaction_payments.amount');

        $excess_commission['card'] = $this->preDaySettlementQuery($start_date, $location_id)->where('sub_type', 'excess')->where('transaction_payments.method', 'card')->sum('transaction_payments.amount');

        $excess_commission['cheque'] = $this->preDaySettlementQuery($start_date, $location_id)->where('sub_type', 'excess')->where('transaction_payments.method', 'cheque')->sum('transaction_payments.amount');
        $excess_commission['due'] = $this->preDaySettlementQuery($start_date, $location_id)->where('sub_type', 'excess')->where('transaction_payments.method', 'cheque')->sum('transaction_payments.amount');


        $cash_account = $this->transactionUtil->account_exist_return_id('Cash');
        $cheque_account = $this->transactionUtil->account_exist_return_id('Cheques in Hand');
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

        $deposit['due'] = AccountTransaction::leftjoin('accounts', 'account_transactions.account_id', 'accounts.id')->where('asset_type', $card_group_id)
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

        $purchase_by_cash = Transaction::leftjoin('transaction_payments', 'transactions.id', 'transaction_payments.transaction_id')
            ->where('type', 'purchase')->whereIn('payment_status', ['partial', 'paid'])->where('transaction_payments.method', 'cash')
            ->where('transactions.business_id', $business_id)
            ->whereDate('transactions.transaction_date', '<', $start_date);

        if (!empty($location_id)) {
            $purchase_by_cash->where('transactions.location_id', $location_id);
        }

        $purchase_by_cash = $purchase_by_cash->sum('transaction_payments.amount');

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
        $previous_day_balance['due'] =  $received['due'] + $shortage_recover['due'] - ($excess_commission['due'] + $deposit['due']);


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
}
