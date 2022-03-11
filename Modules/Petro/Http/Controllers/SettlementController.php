<?php

namespace Modules\Petro\Http\Controllers;

use App\Account;
use App\AccountTransaction;
use App\AccountType;
use App\Business;
use App\BusinessLocation;
use App\Category;
use App\Contact;
use App\ContactLedger;
use App\CustomerReference;
use App\ExpenseCategory;
use App\Product;
use App\Store;
use App\Transaction;
use App\TransactionPayment;
use App\TransactionSellLine;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Petro\Entities\Pump;
use Modules\Petro\Entities\PumpOperator;
use App\Utils\Util;
use App\Utils\ProductUtil;
use App\Utils\ModuleUtil;
use App\Utils\TransactionUtil;
use App\Utils\BusinessUtil;
use App\Variation;
use Modules\HR\Entities\WorkShift;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\Petro\Entities\CustomerPayment;
use Modules\Petro\Entities\FuelTank;
use Modules\Petro\Entities\MeterSale;
use Modules\Petro\Entities\OtherIncome;
use Modules\Petro\Entities\OtherSale;
use Modules\Petro\Entities\Settlement;
use Modules\Petro\Entities\SettlementCardPayment;
use Modules\Petro\Entities\SettlementCashPayment;
use Modules\Petro\Entities\SettlementChequePayment;
use Modules\Petro\Entities\SettlementCreditSalePayment;
use Modules\Petro\Entities\SettlementExcessPayment;
use Modules\Petro\Entities\SettlementExpensePayment;
use Modules\Petro\Entities\SettlementPayment;
use Modules\Petro\Entities\SettlementShortagePayment;
use Modules\Petro\Entities\TankPurchaseLine;
use Modules\Petro\Entities\TankSellLine;
use Modules\Petro\Entities\DailyCollection;
use Modules\Superadmin\Entities\Subscription;
use Yajra\DataTables\DataTables;

class SettlementController extends Controller
{

    /**
     * All Utils instance.
     *
     */
    protected $productUtil;
    protected $moduleUtil;
    protected $transactionUtil;
    protected $commonUtil;

    private $barcode_types;

    /**
     * Constructor
     *
     * @param ProductUtils $product
     * @return void
     */
    public function __construct(Util $commonUtil, ProductUtil $productUtil, ModuleUtil $moduleUtil, TransactionUtil $transactionUtil, BusinessUtil $businessUtil)
    {
        $this->commonUtil = $commonUtil;
        $this->productUtil = $productUtil;
        $this->moduleUtil = $moduleUtil;
        $this->transactionUtil = $transactionUtil;
        $this->businessUtil = $businessUtil;
    }


    /**
     * Display a listing of the resource.
     * @return Response
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
                $query = Settlement::leftjoin('business_locations', 'settlements.location_id', 'business_locations.id')
                    ->leftjoin('pump_operators', 'settlements.pump_operator_id', 'pump_operators.id')
                    ->where('settlements.business_id', $business_id)
                    ->select([
                        'pump_operators.name as pump_operator_name',
                        'business_locations.name as location_name',
                        'settlements.*',
                    ]);

                if (!empty(request()->location_id)) {
                    $query->where('settlements.location_id', request()->location_id);
                }
                if (!empty(request()->pump_operator)) {
                    $query->where('settlements.pump_operator_id', request()->pump_operator);
                }
                if (!empty(request()->settlement_no)) {
                    $query->where('settlements.id', request()->settlement_no);
                }
                if (!empty(request()->start_date) && !empty(request()->end_date)) {
                    $query->whereDate('settlements.transaction_date', '>=', request()->start_date);
                    $query->whereDate('settlements.transaction_date', '<=', request()->end_date);
                }
                $query->orderBy('settlements.id', 'desc');
                $first = null;
                $first = Settlement::where('business_id', $business_id)->where('status', 0)->orderBy('id', 'desc')->first();
                $settlements = Datatables::of($query)
                    ->addColumn(
                        'action',
                        function ($row) use ($first) {
                            $html = '';
                            if ($row->status == 1) {
                                $html .= '<a class="btn  btn-danger btn-sm" href="' . action("\Modules\Petro\Http\Controllers\SettlementController@create") . '">' . __("petro::lang.finish_settlement") . '</a>';
                            } else {
                                $html .=  '<div class="btn-group">
                                <button type="button" class="btn btn-info dropdown-toggle btn-xs" 
                                    data-toggle="dropdown" aria-expanded="false">' .
                                    __("messages.actions") .
                                    '<span class="caret"></span><span class="sr-only">Toggle Dropdown
                                    </span>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-left" role="menu">';

                                $html .= '<li><a data-href="' . action("\Modules\Petro\Http\Controllers\SettlementController@show", [$row->id]) . '" class="btn-modal" data-container=".settlement_modal"><i class="fa fa-eye" aria-hidden="true"></i> ' . __("messages.view") . '</a></li>';
                                if (auth()->user()->can("settlement.edit")) {
                                    $html .= '<li><a href="' . action("\Modules\Petro\Http\Controllers\SettlementController@edit", [$row->id]) . '" class="edit_settlement_button"><i class="fa fa-pencil-square-o"></i> ' . __("messages.edit") . '</a></li>';
                                }
                                if ((!empty($first) && $first->id == $row->id)) {
                // commented By M Usman for hiding Delete Action

                                    // $html .= '<li><a href="' . action("\Modules\Petro\Http\Controllers\SettlementController@destroy", [$row->id]) . '" class="delete_settlement_button"><i class="fa fa-trash"></i> ' . __("messages.delete") . '</a></li>';
                                }
                                $html .= '<li><a data-href="' . action("\Modules\Petro\Http\Controllers\SettlementController@print", [$row->id]) . '" class="print_settlement_button"><i class="fa fa-print"></i> ' . __("petro::lang.print") . '</a></li>';

                                $html .= '</ul></div>';
                            }
                            return $html;
                        }
                    )
                    ->editColumn('status', function ($row) {
                        if ($row->status == 0) {
                            return '<span class="label label-success">Completed</span>';
                        } else {
                            return '<span class="label label-danger">Pending</span>';
                        }
                    })
                    ->editColumn('shift', function ($row) {
                        if (!empty($row->work_shift)) {
                            $shifts = WorkShift::whereIn('id', $row->work_shift)->pluck('shift_name')->toArray();
                            return implode(',', $shifts);
                        } else {
                            return '';
                        }
                    })
                    ->editColumn('transaction_date', '{{@format_date($transaction_date)}}')
                    ->editColumn('total_amount', '{{@num_format($total_amount)}}')
                    ->setRowAttr([
                        'data-href' => function ($row) {
                            return  action('\Modules\Petro\Http\Controllers\SettlementController@show', [$row->id]);
                        }
                    ])

                    ->removeColumn('id');

                return $settlements->rawColumns(['action', 'status', 'total_amount'])
                    ->make(true);
            }
        }
        $business_locations = BusinessLocation::forDropdown($business_id);
        $pump_operators = PumpOperator::where('business_id', $business_id)->pluck('name', 'id');
        $settlement_nos = Settlement::where('business_id', $business_id)->pluck('settlement_no', 'id');

        $message = $this->transactionUtil->getGeneralMessage('general_message_pump_management_checkbox');

        return view('petro::settlement.index')->with(compact(
            'business_locations',
            'pump_operators',
            'settlement_nos',
            'message'
        ));
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create()
    {
        $business_id = request()->session()->get('business.id');
        $business_locations = BusinessLocation::forDropdown($business_id);
        $default_location = current(array_keys($business_locations->toArray()));

        $payment_types = $this->productUtil->payment_types($default_location);
        $customers = Contact::customersDropdown($business_id, false);
        $pump_operators = PumpOperator::where('business_id', $business_id)->pluck('name', 'id');

        $items = [];

        $ref_no_prefixes = request()->session()->get('business.ref_no_prefixes');
        $ref_no_starting_number = request()->session()->get('business.ref_no_starting_number');
        $prefix =   !empty($ref_no_prefixes['settlement']) ? $ref_no_prefixes['settlement'] : '';
        $starting_no =  !empty($ref_no_starting_number['settlement']) ? (int) $ref_no_starting_number['settlement'] : 1;
        $count = Settlement::where('business_id', $business_id)->count();
        $settlement_no = $prefix . ($starting_no + $count);

        $active_settlement = Settlement::where('status', 1)
            ->where('business_id', $business_id)
            ->select('settlements.*')
            ->with(['meter_sales', 'other_sales', 'other_incomes', 'customer_payments'])->first();

        $business_locations = BusinessLocation::forDropdown($business_id);
        $default_location = current(array_keys($business_locations->toArray()));
        if (!empty($active_settlement)) {
            $already_pumps = MeterSale::where('settlement_no', $active_settlement->id)->pluck('pump_id')->toArray();
            $pump_nos = Pump::where('business_id', $business_id)->whereNotIn('id', $already_pumps)->pluck('pump_name', 'id');
        } else {
            $pump_nos = Pump::where('business_id', $business_id)->pluck('pump_name', 'id');
        }

        //other_sale tab
        $stores = Store::where('business_id', $business_id)->where('location_id', $default_location)->pluck('name', 'id');
        $fuel_category_id = Category::where('business_id', $business_id)->where('name', 'Fuel')->first();
        $fuel_category_id = !empty($fuel_category_id) ? $fuel_category_id->id : null;
        $items = $this->transactionUtil->getProductDropDownArray($business_id);
        // other income tab
        $services = Product::where('business_id', $business_id)->where('enable_stock', 0)->pluck('name', 'id');


        $payment_meter_sale_total = !empty($active_settlement->meter_sales) ? $active_settlement->meter_sales->sum('sub_total') :  0.00;
        $payment_other_sale_total = !empty($active_settlement->other_sales) ? $active_settlement->other_sales->sum('sub_total') :  0.00;
        $payment_other_income_total = !empty($active_settlement->other_incomes) ? $active_settlement->other_incomes->sum('sub_total') :  0.00;
        $payment_customer_payment_total = !empty($active_settlement->customer_payments) ? $active_settlement->customer_payments->sum('sub_total') :  0.00;

        $wrok_shifts = WorkShift::where('business_id', $business_id)->pluck('shift_name', 'id');
        $bulk_tanks = FuelTank::where('business_id', $business_id)->where('bulk_tank', 1)->pluck('fuel_tank_number', 'id');


        $select_pump_operator_in_settlement = $this->moduleUtil->hasThePermissionInSubscription($business_id, 'select_pump_operator_in_settlement');

        $message = $this->transactionUtil->getGeneralMessage('general_message_pump_management_checkbox');

        return view('petro::settlement.create')->with(compact(
            'select_pump_operator_in_settlement',
            'message',
            'business_locations',
            'payment_types',
            'customers',
            'pump_operators',
            'wrok_shifts',
            'pump_nos',
            'items',
            'settlement_no',
            'default_location',
            'active_settlement',
            'stores',
            'payment_meter_sale_total',
            'payment_other_sale_total',
            'payment_other_income_total',
            'payment_customer_payment_total',
            'bulk_tanks',
            'services'
        ));
    }

    /**
     * Store a newly created resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        try {
            $settlement_no = $request->settlement_no;
            $business_id = $request->session()->get('business.id');
            $settlement = Settlement::where('settlement_no', $request->settlement_no)->where('business_id', $business_id)->first();
            $edit = Settlement::where('settlements.id', $settlement->id)->where('settlements.business_id', $business_id)->where('status', 0)->first();
            DB::beginTransaction();
            if (!empty($edit)) {
                $this->deletePreviouseTransactions($settlement->id);
            }

            $business_locations = BusinessLocation::forDropdown($business_id);
            $default_location = current(array_keys($business_locations->toArray()));

            $settlement = Settlement::where('settlements.id', $settlement->id)->where('settlements.business_id', $business_id)
                ->leftjoin('pump_operators', 'settlements.pump_operator_id', 'pump_operators.id')
                ->with([
                    'meter_sales',
                    'other_sales',
                    'other_incomes',
                    'customer_payments',
                    'cash_payments',
                    'card_payments',
                    'cheque_payments',
                    'credit_sale_payments',
                    'expense_payments',
                    'excess_payments',
                    'shortage_payments'
                ])
                ->select('settlements.*', 'pump_operators.name as pump_operator_name')
                ->first();

            $business = Business::where('id', $settlement->business_id)->first();
            $pump_operator = PumpOperator::where('id', $settlement->pump_operator_id)->first();

            $total_sales_amount = $settlement->meter_sales->sum('sub_total') + $settlement->other_sales->sum('sub_total');
            $total_sales_discount_amount = $settlement->meter_sales->sum('discount_amount') + $settlement->other_sales->sum('discount_amount');

            $subscription = Subscription::active_subscription($business_id);
            $monthly_max_sale_limit = $subscription->package->monthly_max_sale_limit;

            $startOfMonth = Carbon::now()->startOfMonth()->toDateString();
            $endOfMonth = Carbon::now()->endOfMonth()->toDateString();

            $current_monthly_sale = DB::table('transactions')
                ->select(DB::raw('sum(final_total) as total'))
                ->where('business_id', $business_id)
                ->whereIn('type', ['sell', 'property_sell'])
                ->whereBetween('transaction_date', [$startOfMonth, $endOfMonth])
                ->groupBy('business_id')
                ->first();

            $current_monthly_sale = is_null($current_monthly_sale) ? 0 : (double) $current_monthly_sale->total;
            $current_monthly_sale += $total_sales_amount;

            if($current_monthly_sale > $monthly_max_sale_limit) {
                return [
                    'success' => 0,
                    'msg' => __('lang_v1.monthly_max_sale_limit_exceeded', ['monthly_max_sale_limit' => $monthly_max_sale_limit])
                ];
            }
            $transaction = $this->createTransaction($settlement, $total_sales_amount, null, $settlement->pump_operator_id, 'sell', 'settlement', $settlement_no, null, 0, $total_sales_discount_amount);

            $sell_transaction = $transaction;
            foreach ($settlement->meter_sales as $meter_sale) {
                $fuel_tank_id = Pump::where('id', $meter_sale->pump_id)->first()->fuel_tank_id;
                $sell_line = $this->createSellTransactions($transaction, $meter_sale, $business_id, $default_location, $fuel_tank_id);
                MeterSale::where('id', $meter_sale->id)->update(['transaction_id' => $transaction->id]);
            }
            foreach ($settlement->other_sales as $other_sale) {
                $sell_line = $this->createSellTransactions($transaction, $other_sale, $business_id, $default_location);
                OtherSale::where('id', $other_sale->id)->update(['transaction_id' => $transaction->id]);
            }
            foreach ($settlement->other_incomes as $other_income) {
                $sell_line = $this->createSellTransactions($transaction, $other_income, $business_id, $default_location);
                OtherIncome::where('id', $other_income->id)->update(['transaction_id' => $transaction->id]);
            }
            /* map purchase sell lines */
            $this->createStockAccountTransactions($transaction);
            $this->mapSellPurchaseLines($business_id, $transaction, $settlement);

            foreach ($settlement->cash_payments as $cash_payment) {
                //this transaction will use in report to show amounts
                $this->createTransaction($settlement, $cash_payment->amount, $cash_payment->customer_id, null, 'settlement', 'cash_payment', $settlement_no);
            }
            $cash_transaction_payment = null;
            if ($settlement->cash_payments->sum('amount') > 0) {
                $cash_transaction_payment = $this->createTansactionPayment($transaction, 'cash', $settlement->cash_payments->sum('amount'));
            }

            foreach ($settlement->card_payments as $card_payment) {
                //this transaction will use in report to show amounts
                $this->createTransaction($settlement, $card_payment->amount, $card_payment->customer_id, null, 'settlement', 'card_payment', $settlement_no);
                $transaction_payment = $this->createTansactionPayment($transaction, 'card', $card_payment->amount, $card_payment->card_number, $card_payment->card_type);
                SettlementCardPayment::where('id', $card_payment->id)->update(['customer_payment_id' => $transaction_payment->id]);
                if (!empty($card_payment->card_type)) {
                    $account_id = $card_payment->card_type;
                } else {
                    $account_id = $this->transactionUtil->account_exist_return_id('Cards (Credit Debit) Account');
                }
                $type = 'debit';
                $this->createAccountTransaction($transaction, $type, $account_id, $transaction_payment->id, 'ledger_show', $card_payment->customer_id, $card_payment->amount);
            }
            foreach ($settlement->cheque_payments as $cheque_payment) {
                //this transaction will use in report to show amounts
                $this->createTransaction($settlement, $cheque_payment->amount, $cheque_payment->customer_id, null, 'settlement', 'cheque_payment', $settlement_no);
                $transaction_payment = $this->createTansactionPayment($transaction, 'cheque', $cheque_payment->amount, null, null, $cheque_payment->cheque_number, $cheque_payment->bank_name, $cheque_payment->cheque_date);
                SettlementChequePayment::where('id', $cheque_payment->id)->update(['customer_payment_id' => $transaction_payment->id]);
                $account_id = $this->transactionUtil->account_exist_return_id('Cheques in Hand');
                $type = 'debit';
                $this->createAccountTransaction($transaction, $type, $account_id, $transaction_payment->id, 'ledger_show', $cheque_payment->customer_id, $cheque_payment->amount);
            }
            foreach ($settlement->credit_sale_payments as $credit_sale_payment) {
                $transaction = $this->createCreditSellTransactions($settlement, 'credit_sale', $credit_sale_payment, $business_id, $default_location, null, $credit_sale_payment->id);
                SettlementCreditSalePayment::where('id', $credit_sale_payment->id)->update(['transaction_id' => $transaction->id]);
                $credit_sale_payment->transaction_id = $transaction->id;
                $credit_sale_payment->save();
                $account_id = $this->transactionUtil->account_exist_return_id('Accounts Receivable');
                $type = 'debit';
                $this->createAccountTransaction($transaction, $type, $account_id, null, 'ledger_show', null, 0, true);
            }
            $total_shortage = $pump_operator->short_amount; //get previous amount
            foreach ($settlement->shortage_payments as $shortage_payment) {
                $transaction = $this->createTransaction($settlement, $shortage_payment->amount, null, $settlement->pump_operator_id, 'settlement', 'shortage', $settlement_no);
                SettlementShortagePayment::where('id', $shortage_payment->id)->update(['transaction_id' => $transaction->id]);
                $account_id = $this->transactionUtil->account_exist_return_id('Accounts Receivable');
                $type = 'debit';
                $this->createAccountTransaction($transaction, $type, $account_id, null, 'ledger_show');
                $total_shortage += $shortage_payment->amount;
            }
            $total_excess = $pump_operator->excess_amount; //get previous amount
            foreach ($settlement->excess_payments as $excess_payment) {
                $transaction = $this->createTransaction($settlement, $excess_payment->amount, null, $settlement->pump_operator_id, 'settlement', 'excess', $settlement_no);
                SettlementExcessPayment::where('id', $excess_payment->id)->update(['transaction_id' => $transaction->id]);
                $account_id = $this->transactionUtil->account_exist_return_id('Accounts Receivable');
                $type = 'credit';
                $this->createAccountTransaction($transaction, $type, $account_id, null, 'ledger_show');
                $total_excess += $excess_payment->amount;
            }
            $pump_operator->short_amount =  $total_shortage;
            $pump_operator->excess_amount =  $total_excess;
            $pump_operator->settlement_no =  $settlement->settlement_no;

            $pump_operator->save();

            foreach ($settlement->expense_payments as $expense_payment) {
                $transaction = $this->createTransaction($settlement, $expense_payment->amount, null, $settlement->pump_operator_id, 'settlement', 'expense', $settlement_no);
                $transaction->expense_category_id =  $expense_payment->category_id;
                $transaction->ref_no =  "Settlement No: " . $settlement->settlement_no;
                $transaction->expense_account = $expense_payment->account_id;
                $transaction->save();
                SettlementExpensePayment::where('id', $expense_payment->id)->update(['transaction_id' => $transaction->id]);
                $transaction_payment = $this->createTansactionPayment($transaction, 'cash');
                $account_id = $expense_payment->account_id;
                $type = 'debit';
                $this->createAccountTransaction($transaction, $type, $account_id, $transaction_payment->id);
                $account_id = $this->transactionUtil->account_exist_return_id('Cash');
                $type = 'credit';
                $this->createAccountTransaction($transaction, $type, $account_id, $transaction_payment->id);
            }

            //Cash payment + expense payment  //doc 3075 - POS Settlement Expense amount in cash account – 5 Nov 2020
            $account_id = $this->transactionUtil->account_exist_return_id('Cash');
            $expense_transaction_data = [
                'amount' => $settlement->expense_payments->sum('amount') + $settlement->cash_payments->sum('amount'),
                'account_id' => $account_id,
                'contact_id' => $sell_transaction->contact_id,
                'type' => 'debit',
                'sub_type' => null,
                'operation_date' => $sell_transaction->transaction_date,
                'created_by' => $sell_transaction->created_by,
                'transaction_id' => $sell_transaction->id,
                'transaction_payment_id' => !empty($cash_transaction_payment) ? $cash_transaction_payment->id : null,
                'note' => null
            ];
            AccountTransaction::createAccountTransaction($expense_transaction_data);


            //this for only to show in print page customer payments which entered in customer payments tab
            $customer_payments_tab = CustomerPayment::leftjoin('contacts', 'customer_payments.customer_id', 'contacts.id')
                ->where('customer_payments.settlement_no', $settlement->id)
                ->where('customer_payments.business_id', $business_id)
                ->select('customer_payments.*', 'contacts.name as customer_name')
                ->get();

            $settlement_total = $settlement->meter_sales->sum('sub_total') + $settlement->other_sales->sum('sub_total') + $settlement->other_incomes->sum('sub_total') + $settlement->customer_payments->sum('sub_total');
            $settlement->total_amount = $settlement_total;
            $settlement->status = 0; // set status to non active
            $settlement->finish_date = date('Y-m-d');
            $settlement->save();

            // //Get Daily Collection from businiss_id and pomp_operator id and settlement_id is null
            $daily_collections = DailyCollection::leftjoin('business_locations', 'daily_collections.location_id', 'business_locations.id')
            ->leftjoin('pump_operators', 'daily_collections.pump_operator_id', 'pump_operators.id')
            ->leftjoin('users', 'daily_collections.created_by', 'users.id')
            ->leftjoin('settlements', 'daily_collections.settlement_id', 'settlements.id')
            ->where('daily_collections.business_id', $business_id)
            ->where('daily_collections.pump_operator_id', $settlement->pump_operator_id)
            ->whereNull('settlement_id')
            ->select([
                'daily_collections.*',
                'business_locations.name as location_name',
                'pump_operators.name as pump_operator_name',
                'settlements.id as settlements_id',
                'users.username as user',
            ])->orderBy('daily_collections.id')->get();

            $outstanding_payment = $settlement_total;
            foreach ($daily_collections as $daily_collections) {
                # code...
                if ($outstanding_payment >= 0) {
                    $outstanding_payment = floatval($outstanding_payment) - floatval($daily_collections->current_amount);
                    DB::update('update daily_collections set settlement_id = ?, settlement_date = ?, balance_collection = ? where business_id = ? and pump_operator_id = ? and id = ? and settlement_id is null',
                     [$settlement->id, $settlement->finish_date, floatval($daily_collections->current_amount), $business_id, $settlement->pump_operator_id, $daily_collections->id]);
                    // echo var_dump($outstanding_payment ."/nr");
                }
            }

            DB::commit();

            return view('petro::settlement.print')->with(compact('settlement', 'business', 'pump_operator', 'customer_payments_tab'));
        } catch (\Exception $e) {
            Log::emergency('File: ' . $e->getFile() . 'Line: ' . $e->getLine() . 'Message: ' . $e->getMessage());
            $output = [
                'success' => 0,
                'msg' => $e->getMessage()
            ];
        }

        return $output;
    }



    public function createTransaction($settlement, $amount, $customer_id = null, $pump_operator_id = null, $type = 'sell', $sub_type, $settlement_no, $ref_no = null, $is_credit_sale = 0, $total_sales_discount_amount = 0.00)
    {

        $business_id = request()->session()->get('business.id');
        $business_location = BusinessLocation::where('business_id', $business_id)
            ->first();
        $total_sales_discount_amount = !empty($total_sales_discount_amount) ?? 0;
        $final_amount = $amount;
        $ob_data = [
            'business_id' => $business_id,
            'location_id' => $business_location->id,
            'type' => $type,
            'sub_type' => $sub_type,
            'status' => 'final',
            'payment_status' => 'paid',
            'contact_id' => $customer_id,
            'pump_operator_id' => $pump_operator_id,
            'transaction_date' => Carbon::parse($settlement->transaction_date)->format('Y-m-d'),
            'total_before_tax' => $final_amount,
            'final_total' => $final_amount,
            'discount_amount' => $total_sales_discount_amount,
            'created_by' => request()->session()->get('user.id'),
            'is_settlement' => 1
        ];
        if ($sub_type == 'excess' || $sub_type == 'shortage') {
            $ob_data['payment_status'] = 'due';
        }

        $ob_data['invoice_no'] = $settlement_no;
        $ob_data['ref_no'] = !empty($ref_no) ? $ref_no : null;
        if ($is_credit_sale == 1) {
            $ob_data['type'] = 'sell';
            $ob_data['sub_type'] = 'credit_sale';
        }
        $transaction = Transaction::create($ob_data);
        return $transaction;
    }

    public function createSellTransactions($transaction, $sale, $business_id, $default_location, $fuel_tank_id = null)
    {
        $uf_quantity = $this->productUtil->num_uf($sale->qty);
        $product = Variation::leftjoin('products', 'variations.product_id', 'products.id')
            ->leftjoin('variation_location_details', 'variations.id', 'variation_location_details.variation_id')
            ->leftjoin('categories', 'products.category_id', 'categories.id')
            ->where('products.id', $sale->product_id)
            ->select('variations.id as variation_id', 'variation_location_details.location_id', 'products.id as product_id', 'categories.name as category_name', 'products.enable_stock')->first();

        $this->transactionUtil->createOrUpdateSellLinesSettlement($transaction, $product->product_id, $product->variation_id, $product->location_id, $sale);
        $location_product = !empty($product->location_id) ? $product->location_id : $default_location;
        // if enable stock
        if ($product->enable_stock) {
            //decrease sold qty from product stock
            $this->productUtil->decreaseProductQuantity(
                $sale->product_id,
                $product->variation_id,
                $location_product,
                $uf_quantity
            );
        }

        //update qty to fuel tank current stock
        if (!empty($fuel_tank_id)) {
            FuelTank::where('id', $fuel_tank_id)->decrement('current_balance', $sale->qty);
            TankSellLine::create([
                'business_id' => $business_id,
                'transaction_id' => $transaction->id,
                'tank_id' => $fuel_tank_id,
                'product_id' => $sale->product_id,
                'quantity' => $sale->qty
            ]);
        }

        return true;
    }

    public function createCreditSellTransactions($settlement, $sub_type, $sale, $business_id, $default_location, $fuel_tank_id = null, $credit_sale_id = null)
    {
        $opening_balance = Transaction::where('contact_id', $sale->customer_id)->where('type', 'opening_balance')->where('payment_status', 'due')->sum('final_total');

        $uf_quantity = $this->productUtil->num_uf($sale->qty);


        $product = Variation::leftjoin('products', 'variations.product_id', 'products.id')
            ->leftjoin('variation_location_details', 'variations.id', 'variation_location_details.variation_id')
            ->leftjoin('categories', 'products.category_id', 'categories.id')
            ->where('products.id', $sale->product_id)
            ->select('variations.id as variation_id', 'variation_location_details.location_id', 'products.id as product_id', 'categories.name as category_name')->first();

        $final_amount = $sale->qty * $sale->price;

        $discount = $this->getDiscount(!empty($sale->discount) ? $sale->discount : 0);
        $discount_type =  $discount['discount_type'];
        $discount_amount =  $discount['discount_amount'];
        $ob_data = [
            'business_id' => $business_id,
            'location_id' => $settlement->location_id,
            'type' => 'sell',
            'status' => 'final',
            'payment_status' => 'paid',
            'contact_id' => $sale->customer_id,
            'pump_operator_id' => $settlement->pump_operator_id,
            'transaction_date' => Carbon::parse($settlement->transaction_date)->format('Y-m-d'),
            'total_before_tax' => $final_amount,
            'final_total' => $final_amount,
            'discount_type' => $discount_type,
            'discount_amount' => $discount_amount,
            'credit_sale_id' => $credit_sale_id,
            'is_credit_sale' => 0,
            'is_settlement' => 1,
            'created_by' => request()->session()->get('user.id')
        ];
        //Generate reference number
        $ob_data['invoice_no'] = $settlement->settlement_no;
        if (!empty($sale->customer_reference)) {
            $ob_data['ref_no'] = $sale->customer_reference;
            $ob_data['customer_ref'] = $sale->customer_reference;
        }
        if (!empty($sale->order_number)) {
            $ob_data['order_no'] = $sale->order_number;
        }
        if (!empty($sale->order_date)) {
            $ob_data['order_date'] = $sale->order_date;
        }
        if ($sub_type == 'credit_sale') {
            $ob_data['is_credit_sale'] = 1;
            $ob_data['sub_type'] = 'credit_sale';
            $ob_data['payment_status'] = 'due';
        }
        if ($sub_type == 'credit_sale' && $opening_balance < 0) {
            $ob_data['payment_status'] = 'paid';
        }
        if ($sub_type == 'meter_sale') {
            $ob_data['sub_type'] = 'meter_sale';
        }
        if ($sub_type == 'other_sale') {
            $ob_data['sub_type'] = 'other_sale';
        }

        //Create transaction
        $transaction = Transaction::create($ob_data);
        $this->transactionUtil->createOrUpdateSellLinesSettlement($transaction, $product->product_id, $product->variation_id, $product->location_id, $sale);
        $location_product = !empty($product->location_id) ? $product->location_id : $default_location;

        return $transaction;
    }

    public function mapSellPurchaseLines($business_id, $transaction, $settlement)
    {
        //Allocate the quantity from purchase and add mapping of
        //purchase & sell lines in transaction_sell_lines_purchase_lines table
        $business_details = $this->businessUtil->getDetails($business_id);
        $pos_settings = empty($business_details->pos_settings) ? $this->businessUtil->defaultPosSettings() : json_decode($business_details->pos_settings, true);

        $business = [
            'id' => $business_id,
            'accounting_method' => request()->session()->get('business.accounting_method'),
            'location_id' => $settlement->location_id,
            'pos_settings' => $pos_settings
        ];
        $this->transactionUtil->mapPurchaseSell($business, $transaction->sell_lines, 'purchase');
    }

    public function createTansactionPayment($transaction, $method, $amount = 0, $card_number = null, $card_type = null, $cheque_number = null, $bank_name = null, $cheque_date = null)
    {
        $business_id = request()->session()->get('business.id');
        $transaction_payment_data = [
            'transaction_id' => $transaction->id,
            'business_id' => $business_id,
            'amount' => abs($transaction->final_total),
            'method' => $method,
            'paid_on' => $transaction->transaction_date,
            'created_by' => $transaction->created_by,
            'card_number' => $card_number,
            'card_type' => $card_type,
            'cheque_number' => $cheque_number,
            'bank_name' => $bank_name,
            'cheque_date' => !empty($cheque_date) ? Carbon::parse($cheque_date)->format('Y-m-d') : null
        ];

        if (!empty($amount)) {
            $transaction_payment_data['amount'] = $amount;
        }

        $transaction_payment_data['paid_in_type'] = 'settlement';

        $transaction_payment = TransactionPayment::create($transaction_payment_data);

        return $transaction_payment;
    }

    public function createAccountTransaction($transaction, $type, $account_id, $transaction_payment_id = null, $sub_type = null, $contact_id = null, $amount = 0, $is_credit_sale = false)
    {
        $account_transaction_data = [
            'amount' => abs($transaction->final_total),
            'account_id' => $account_id,
            'contact_id' => $transaction->contact_id,
            'type' => $type,
            'sub_type' => $sub_type,
            'operation_date' => $transaction->transaction_date,
            'created_by' => $transaction->created_by,
            'transaction_id' => $transaction->id,
            'transaction_payment_id' => $transaction_payment_id,
            'note' => null
        ];
        if (!empty($contact_id)) {
            $account_transaction_data['contact_id'] = $contact_id;
        }
        if (!empty($amount)) {
            $account_transaction_data['amount'] = $amount;
        }

        AccountTransaction::createAccountTransaction($account_transaction_data);
        // create ledger transactions
        if ($sub_type == 'ledger_show') {
            ContactLedger::createContactLedger($account_transaction_data);
            if (!$is_credit_sale) {
                if ($type == 'debit') {
                    $ledger_type = 'credit';
                }
                if ($type == 'credit') {
                    $ledger_type = 'debit';
                }
                $account_transaction_data['type'] = $ledger_type;
                ContactLedger::createContactLedger($account_transaction_data);
            }
        }
    }

    public function createStockAccountTransactions($transaction)
    {
        $account_transaction_data = [
            'amount' => abs($transaction->final_total),
            'operation_date' => $transaction->transaction_date,
            'created_by' => $transaction->created_by,
            'transaction_id' => $transaction->id,
            'note' => null
        ];

        $this->transactionUtil->manageStockAccount($transaction, $account_transaction_data, 'credit', $transaction->final_total);
        $this->transactionUtil->createCostofGoodsSoldTransaction($transaction, 'ledger_show', 'debit');
        $this->transactionUtil->createSaleIncomeTransaction($transaction, 'ledger_show', 'credit');
    }

    public function deletePreviouseTransactions($settlement_id, $is_destory = false)
    {
        $business_id = request()->session()->get('business.id');
        $settlement = Settlement::find($settlement_id);
        $all_trasactions = Transaction::where('invoice_no', $settlement->settlement_no)->where('is_settlement', 1)->where('business_id', $business_id)->with(['sell_lines'])->withTrashed()->get();

        foreach ($all_trasactions as $transaction) {
            if (!empty($transaction)) {
                $deleted_sell_lines = $transaction->sell_lines;
                $deleted_sell_lines_ids = $deleted_sell_lines->pluck('id')->toArray();
                if ($transaction->sub_type == 'credit_sale') {
                    $this->transactionUtil->deleteSellLinesSettlement(
                        $deleted_sell_lines_ids,
                        $transaction->location_id,
                        false
                    );
                } else {
                    $this->transactionUtil->deleteSellLinesSettlement(
                        $deleted_sell_lines_ids,
                        $transaction->location_id
                    );
                }

                $transaction->status = 'draft';
                $business = [
                    'id' => $business_id,
                    'accounting_method' => request()->session()->get('business.accounting_method'),
                    'location_id' => $transaction->location_id
                ];
                if ($transaction->sub_type != 'credit_sale') {
                    $this->transactionUtil->adjustMappingPurchaseSell('final', $transaction, $business, $deleted_sell_lines_ids);
                }

                //Delete Cash register transactions
                $transaction->cash_register_payments()->delete();
            }

            $tank_sell_lines =  TankSellLine::where('transaction_id', $transaction->id)->get();
            foreach ($tank_sell_lines as $tank_sell_line) {
                FuelTank::where('id', $tank_sell_line->tank_id)->increment('current_balance', $tank_sell_line->quantity);
            }
            TankSellLine::where('transaction_id', $transaction->id)->forceDelete();
            AccountTransaction::where('transaction_id', $transaction->id)->forceDelete();
            ContactLedger::where('transaction_id', $transaction->id)->forceDelete();
            TransactionPayment::where('transaction_id', $transaction->id)->forceDelete();
            Transaction::where('id', $transaction->id)->forceDelete();
        }

        $settlement->total_amount = 0;
        $settlement->save();


        if ($is_destory) {
            $meter_sales = MeterSale::where('settlement_no', $settlement->id)->get();
            foreach ($meter_sales as $meter_sale) {
                Pump::where('id', $meter_sale->pump_id)->update(['last_meter_reading' => $meter_sale->starting_meter]);
                $meter_sale->delete();
            }
            OtherSale::where('settlement_no', $settlement->id)->delete();
            OtherIncome::where('settlement_no', $settlement->id)->delete();
            CustomerPayment::where('settlement_no', $settlement->id)->delete();
            SettlementCardPayment::where('settlement_no', $settlement->id)->delete();
            SettlementCashPayment::where('settlement_no', $settlement->id)->delete();
            SettlementChequePayment::where('settlement_no', $settlement->id)->delete();
            SettlementExpensePayment::where('settlement_no', $settlement->id)->delete();
            SettlementExcessPayment::where('settlement_no', $settlement->id)->delete();
            SettlementShortagePayment::where('settlement_no', $settlement->id)->delete();
            SettlementCreditSalePayment::where('settlement_no', $settlement->id)->delete();
        }
    }


    public function getDiscount($discount)
    {
        $pos = strpos($discount, '%');
        $discount_amount = str_replace('%', '', $discount);
        if ($pos === false) {
            $discount_type = 'fixed';
        } else {
            $discount_type = 'percentage';
        }

        return ['discount_amount' => $discount_amount, 'discount_type' => $discount_type];
    }
    /**
     * Show the specified resource.
     * @return Response
     */
    public function show($id)
    {
        $business_id = request()->session()->get('business.id');
        $business_locations = BusinessLocation::forDropdown($business_id);
        $default_location = current(array_keys($business_locations->toArray()));

        $settlement = Settlement::where('settlements.id', $id)->where('settlements.business_id', $business_id)
            ->leftjoin('pump_operators', 'settlements.pump_operator_id', 'pump_operators.id')
            ->with([
                'meter_sales',
                'other_sales',
                'other_incomes',
                'customer_payments',
                'cash_payments',
                'card_payments',
                'cheque_payments',
                'credit_sale_payments',
                'expense_payments',
                'excess_payments',
                'shortage_payments'
            ])
            ->select('settlements.*', 'pump_operators.name as pump_operator_name')
            ->first();

        $business = Business::where('id', $settlement->business_id)->first();
        $pump_operator = PumpOperator::where('id', $settlement->pump_operator_id)->first();

        //this for only to show in print page customer payments which entered in customer payments tab
        $customer_payments_tab = CustomerPayment::leftjoin('contacts', 'customer_payments.customer_id', 'contacts.id')
            ->where('customer_payments.settlement_no', $id)
            ->where('customer_payments.business_id', $business_id)
            ->select('customer_payments.*', 'contacts.name as customer_name')
            ->get();

        return view('petro::settlement.show')->with(compact('settlement', 'business', 'pump_operator', 'customer_payments_tab'));
    }

    /**
     * Show the form for editing the specified resource.
     * @return Response
     */
    public function edit($id)
    {
        $business_id = request()->session()->get('business.id');
        $business_locations = BusinessLocation::forDropdown($business_id);
        $default_location = current(array_keys($business_locations->toArray()));
        $payment_types = $this->productUtil->payment_types($default_location);
        $customers = Contact::customersDropdown($business_id, false, true, 'customer');
        $pump_operators = PumpOperator::where('business_id', $business_id)->pluck('name', 'id');

        $pump_nos = Pump::where('business_id', $business_id)->pluck('pump_name', 'id');

        $items = [];


        $active_settlement = Settlement::where('id', $id)
            ->select('settlements.*')
            ->with(['meter_sales', 'other_sales', 'other_incomes', 'customer_payments'])->first();
        $settlement_no = $active_settlement->settlement_no;

        $business_locations = BusinessLocation::forDropdown($business_id);
        $default_location = current(array_keys($business_locations->toArray()));
        if (!empty($active_settlement)) {
            $already_pumps = MeterSale::where('settlement_no', $active_settlement->id)->pluck('pump_id')->toArray();
            $pump_nos = Pump::where('business_id', $business_id)->whereNotIn('id', $already_pumps)->pluck('pump_name', 'id');
        } else {
            $pump_nos = Pump::where('business_id', $business_id)->pluck('pump_name', 'id');
        }

        //other_sale tab
        $stores = Store::where('business_id', $business_id)->pluck('name', 'id');
        $fuel_category_id = Category::where('business_id', $business_id)->where('name', 'Fuel')->first();
        $fuel_category_id = !empty($fuel_category_id) ? $fuel_category_id->id : null;
        // $items = Product::where('category_id', '!=', $fuel_category_id)->where('business_id', $business_id)->pluck('name', 'id');

        $items = $this->transactionUtil->getProductDropDownArray($business_id, $fuel_category_id);


        $payment_meter_sale_total = !empty($active_settlement->meter_sales) ? $active_settlement->meter_sales->sum('sub_total') :  0.00;
        $payment_other_sale_total = !empty($active_settlement->other_sales) ? $active_settlement->other_sales->sum('sub_total') :  0.00;
        $payment_other_income_total = !empty($active_settlement->other_incomes) ? $active_settlement->other_incomes->sum('sub_total') :  0.00;
        $payment_customer_payment_total = !empty($active_settlement->customer_payments) ? $active_settlement->customer_payments->sum('sub_total') :  0.00;

        $wrok_shifts = WorkShift::where('business_id', $business_id)->pluck('shift_name', 'id');
        $bulk_tanks = FuelTank::where('business_id', $business_id)->where('bulk_tank', 1)->pluck('fuel_tank_number', 'id');

        $services = Product::where('business_id', $business_id)->where('enable_stock', 0)->pluck('name', 'id');

        return view('petro::settlement.edit')->with(compact(
            'business_locations',
            'payment_types',
            'services',
            'customers',
            'pump_operators',
            'wrok_shifts',
            'pump_nos',
            'items',
            'settlement_no',
            'default_location',
            'active_settlement',
            'stores',
            'payment_meter_sale_total',
            'payment_other_sale_total',
            'payment_other_income_total',
            'payment_customer_payment_total',
            'bulk_tanks'
        ));
    }

    /**
     * Update the specified resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function update(Request $request, $id)
    {
        try {
            $input = $request->except('_token', '_method');
            $input['work_shift'] = json_encode($request->work_shift);
            $input['transaction_date'] = Carbon::parse($request->transaction_date)->format('Y-m-d');
            Settlement::where('id', $id)->update($input);
            $output = [
                'success' => true,
                'msg' => __('petro::lang.success')
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
     * Remove the specified resource from storage.
     * @return Response
     */
    public function destroy($id)
    {
        try {
            DB::beginTransaction();
            $settlement = Settlement::findOrFail($id);
            $this->deletePreviouseTransactions($settlement->id, true);
            $settlement->delete();
            DB::commit();

            $output = [
                'success' => true,
                'msg' => __('petro::lang.settlement_delete_success')
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
     * get details for pump id
     * @return Response
     */
    public function getPumpDetails($pump_id)
    {
        $pump = Pump::where('id', $pump_id)->first();
        $last_meter_reading = $pump->last_meter_reading;
        $last_meter_sale = MeterSale::where('pump_id', $pump_id)->orderBy('id', 'desc')->first();
        if (!empty($last_meter_sale)) {
            $last_meter_reading = $last_meter_sale->closing_meter;
        }
        $fuel_tank = FuelTank::where('id', $pump->fuel_tank_id)->first();

        $product = Variation::leftjoin('products', 'variations.product_id', 'products.id')
            ->leftjoin('variation_location_details', 'variations.id', 'variation_location_details.variation_id')
            ->where('products.id', $fuel_tank->product_id)
            ->select('sku', 'default_sell_price', 'products.name', 'products.id', 'variation_location_details.qty_available')->first();

        $current_balance = $this->transactionUtil->getTankBalanceById($pump->fuel_tank_id);

        return [
            'colsing_value' => number_format($last_meter_reading, 5, '.', ''),
            'tank_remaing_qty' => $current_balance,
            'product' => $product,
            'pump_name' => $pump->pump_name,
            'product_id' => $product->id,
            'pump_id' => $pump->id,
            'bulk_sale_meter' => $pump->bulk_sale_meter
        ];
    }

    /**
     * get balance stock of product
     * @param product_id
     * @return Response
     */
    public function getBalanceStock($id)
    {
        try {
            $product = Product::leftjoin('variations', 'products.id', 'variations.product_id')
                ->leftjoin('variation_location_details', 'variations.id', 'variation_location_details.variation_id')
                ->where('products.id', $id)->select('qty_available', 'sell_price_inc_tax', 'products.name', 'sku')->first();

            $output = [
                'success' => true,
                'balance_stock' => $product->qty_available,
                'price' => $product->sell_price_inc_tax,
                'product_name' => $product->name,
                'code' => $product->sku,
                'msg' => __('petro::lang.success')
            ];
        } catch (\Exception $e) {
            \Log::emergency('File: ' . $e->getFile() . 'Line: ' . $e->getLine() . 'Message: ' . $e->getMessage());
            $output = [
                'success' => false,
                'msg' => __('messages.something_went_wrong')
            ];
        }

        return $output;
    }
    /**
     * save meter sale to db
     * @return Response
     */
    public function saveMeterSale(Request $request)
    {
        try {
            $business_id = $request->session()->get('business.id');
            $business_locations = BusinessLocation::forDropdown($business_id);
            $default_location = current(array_keys($business_locations->toArray()));

            DB::beginTransaction();

            $settlement_exist = $this->createSettlementIfNotExist($request);

            $pump = Pump::where('id', $request->pump_id)->first();
            $tank_id = $pump->fuel_tank_id;
            $data = array(
                'business_id' => $business_id,
                'settlement_no' => $settlement_exist->id,
                'product_id' => $request->product_id,
                'pump_id' => $request->pump_id,
                'starting_meter' => $request->starting_meter,
                'closing_meter' => $pump->bulk_sale_meter == 0 ? $request->closing_meter : '',
                'price' => $request->price,
                'qty' => $request->qty,
                'discount' => $request->discount,
                'discount_type' => $request->discount_type,
                'discount_amount' => $request->discount_amount,
                'testing_qty' => $request->testing_qty,
                'sub_total' => $request->sub_total
            );

            $meter_sale = MeterSale::create($data);

            Pump::where('id', $request->pump_id)->update(['starting_meter' => $request->starting_meter, 'last_meter_reading' => $request->closing_meter]);

            DB::commit();
            $output = [
                'success' => true,
                'msg' => 'success',
                'meter_sale_id' => $meter_sale->id
            ];
        } catch (\Exception $e) {
            \Log::emergency('File: ' . $e->getFile() . 'Line: ' . $e->getLine() . 'Message: ' . $e->getMessage());
            $output = [
                'success' => false,
                'msg' => __('messages.something_went_wrong')
            ];
        }

        return $output;
    }

    public function deleteMeterSale($id)
    {
        try {
            $meter_sale = MeterSale::where('id', $id)->first();
            $amount = $meter_sale->sub_total;
            $starting_meter = $meter_sale->starting_meter;
            $closing_meter = $meter_sale->closing_meter;
            $pump = Pump::where('id', $meter_sale->pump_id)->first();
            $tank_id = $pump->fuel_tank_id;
            FuelTank::where('id', $tank_id)->increment('current_balance', $meter_sale->qty);
            $meter_sale->delete();
            $pump->last_meter_reading = $starting_meter; //reset back to previous starting meter

            $previous_meter_sale = MeterSale::where('pump_id', $pump->id)->orderBy('id', 'desc')->first();
            if (!empty($previous_meter_sale)) {
                $pump->starting_meter = $previous_meter_sale->starting_meter;
            }
            $pump->save();

            $pump_name = $pump->pump_name;
            $pump_id = $pump->id;

            $output = [
                'success' => true,
                'amount' => $amount,
                'pump_name' => $pump_name,
                'pump_id' => $pump_id,
                'msg' => __('petro::lang.success')
            ];
        } catch (\Exception $e) {
            \Log::emergency('File: ' . $e->getFile() . 'Line: ' . $e->getLine() . 'Message: ' . $e->getMessage());
            $output = [
                'success' => false,
                'msg' => __('messages.something_went_wrong')
            ];
        }

        return $output;
    }

    /**
     * save other sale data in db
     * @param product_id
     * @return Response
     */
    public function saveOtherSale(Request $request)
    {
        try {
            $business_id = $request->session()->get('business.id');

            $settlement_exist = $this->createSettlementIfNotExist($request);
            $data = array(
                'business_id' => $business_id,
                'settlement_no' => $settlement_exist->id,
                'store_id' => $request->store_id,
                'product_id' => $request->product_id,
                'price' => $request->price,
                'qty' => $request->qty,
                'balance_stock' => $request->balance_stock,
                'discount' => $request->discount,
                'discount_type' => $request->discount_type,
                'discount_amount' => $request->discount_amount,
                'sub_total' => $request->sub_total
            );
            $other_sale = OtherSale::create($data);

            $output = [
                'success' => true,
                'other_sale_id' => $other_sale->id,
                'msg' => __('petro::lang.success')
            ];
        } catch (\Exception $e) {
            \Log::emergency('File: ' . $e->getFile() . 'Line: ' . $e->getLine() . 'Message: ' . $e->getMessage());
            $output = [
                'success' => false,
                'msg' => __('messages.something_went_wrong')
            ];
        }

        return $output;
    }

    public function deleteOtherSale($id)
    {
        try {
            $other_sale = OtherSale::where('id', $id)->first();
            $amount = $other_sale->sub_total;
            $other_sale->delete();

            $output = [
                'success' => true,
                'amount' => $amount,
                'msg' => __('petro::lang.success')
            ];
        } catch (\Exception $e) {
            \Log::emergency('File: ' . $e->getFile() . 'Line: ' . $e->getLine() . 'Message: ' . $e->getMessage());
            $output = [
                'success' => false,
                'msg' => __('messages.something_went_wrong')
            ];
        }

        return $output;
    }

    /**
     * save other income data in db
     * @param product_id
     * @return Response
     */
    public function saveOtherIncome(Request $request)
    {
        try {
            $business_id = $request->session()->get('business.id');

            $settlement_exist = $this->createSettlementIfNotExist($request);
            $data = array(
                'business_id' => $business_id,
                'settlement_no' => $settlement_exist->id,
                'product_id' => $request->product_id,
                'qty' => $request->qty,
                'price' => $request->price,
                'reason' => $request->other_income_reason,
                'sub_total' => $request->sub_total
            );
            $other_income = OtherIncome::create($data);

            $output = [
                'success' => true,
                'other_income_id' => $other_income->id,
                'msg' => __('petro::lang.success')
            ];
        } catch (\Exception $e) {
            \Log::emergency('File: ' . $e->getFile() . 'Line: ' . $e->getLine() . 'Message: ' . $e->getMessage());
            $output = [
                'success' => false,
                'msg' => __('messages.something_went_wrong')
            ];
        }

        return $output;
    }

    public function deleteOtherIncome($id)
    {
        try {
            $other_income = OtherIncome::where('id', $id)->first();
            $sub_total = $other_income->sub_total;
            $other_income->delete();

            $output = [
                'success' => true,
                'sub_total' => $sub_total,
                'msg' => __('petro::lang.success')
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
     * save customer payment data in db
     * @param product_id
     * @return Response
     */
    public function saveCustomerPayment(Request $request)
    {
        try {
            $business_id = $request->session()->get('business.id');

            $settlement_exist = $this->createSettlementIfNotExist($request);
            $data = array(
                'business_id' => $business_id,
                'settlement_no' => $settlement_exist->id,
                'customer_id' => $request->customer_id,
                'payment_method' => $request->payment_method,
                'cheque_date' => !empty($request->cheque_date) ? Carbon::parse($request->cheque_date)->format('Y-m-d') : null,
                'cheque_number' => $request->cheque_number,
                'bank_name' => $request->bank_name,
                'amount' => $request->amount,
                'sub_total' => $request->sub_total
            );
            DB::beginTransaction();
            $customer_payment = CustomerPayment::create($data);

            if ($request->payment_method == 'cash') {
                $cash_data = array(
                    'business_id' => $business_id,
                    'settlement_no' => $settlement_exist->id,
                    'amount' => $request->amount,
                    'customer_id' => $request->customer_id,
                    'customer_payment_id' => $customer_payment->id
                );

                $settlement_cash_payment = SettlementCashPayment::create($cash_data);
            }
            if ($request->payment_method == 'card') {
                $card_data = array(
                    'business_id' => $business_id,
                    'settlement_no' => $settlement_exist->id,
                    'amount' => $request->amount,
                    'card_type' => $request->card_type,
                    'card_number' => $request->card_number,
                    'customer_id' => $request->customer_id,
                    'customer_payment_id' => $customer_payment->id
                );

                $settlement_card_payment = SettlementCardPayment::create($card_data);
            }
            if ($request->payment_method == 'cheque') {
                $cheque_data = array(
                    'business_id' => $business_id,
                    'settlement_no' => $settlement_exist->id,
                    'amount' => $request->amount,
                    'bank_name' => $request->bank_name,
                    'cheque_number' => $request->cheque_number,
                    'cheque_date' => !empty($request->cheque_date) ? Carbon::parse($request->cheque_date)->format('Y-m-d') : null,
                    'customer_id' => $request->customer_id,
                    'customer_payment_id' => $customer_payment->id
                );

                $settlement_cheque_payment = SettlementChequePayment::create($cheque_data);
            }
            DB::commit();
            $output = [
                'success' => true,
                'customer_payment_id' => $customer_payment->id,
                'msg' => __('petro::lang.success')
            ];
        } catch (\Exception $e) {
            \Log::emergency('File: ' . $e->getFile() . 'Line: ' . $e->getLine() . 'Message: ' . $e->getMessage());
            $output = [
                'success' => false,
                'msg' => __('messages.something_went_wrong')
            ];
        }

        return $output;
    }

    public function deleteCustomerPayment($id)
    {
        try {
            $customer_payment = CustomerPayment::where('id', $id)->first();
            $amount = $customer_payment->amount;
            $customer_payment->delete();
            SettlementCashPayment::where('customer_payment_id', $id)->delete();
            SettlementCardPayment::where('customer_payment_id', $id)->delete();
            SettlementChequePayment::where('customer_payment_id', $id)->delete();

            $output = [
                'success' => true,
                'amount' =>  $amount,
                'msg' => __('petro::lang.success')
            ];
        } catch (\Exception $e) {
            \Log::emergency('File: ' . $e->getFile() . 'Line: ' . $e->getLine() . 'Message: ' . $e->getMessage());
            $output = [
                'success' => false,
                'msg' => __('messages.something_went_wrong')
            ];
        }

        return $output;
    }

    public function createSettlementIfNotExist(Request $request)
    {
        $business_id = $request->session()->get('business.id');
        $settlement_data = array(
            'settlement_no' => $request->settlement_no,
            'business_id' => $business_id,
            'transaction_date' => Carbon::parse($request->transaction_date)->format('Y-m-d'),
            'location_id' => $request->location_id,
            'pump_operator_id' => $request->pump_operator_id,
            'work_shift' => !empty($request->work_shift) ? $request->work_shift : [],
            'note' => $request->note,
            'status' => 1
        );
        $settlement_exist = Settlement::where('settlement_no', $request->settlement_no)->where('business_id', $business_id)->first();
        if (empty($settlement_exist)) {
            $settlement_exist = Settlement::create($settlement_data);
        }

        return $settlement_exist;
    }

    /**
     * print resources
     * @param settlement_id
     * @return Response
     */
    public function print($id)
    {
        $business_id = request()->session()->get('business.id');
        $business_locations = BusinessLocation::forDropdown($business_id);
        $default_location = current(array_keys($business_locations->toArray()));

        $settlement = Settlement::where('settlements.id', $id)->where('settlements.business_id', $business_id)
            ->leftjoin('pump_operators', 'settlements.pump_operator_id', 'pump_operators.id')
            ->with([
                'meter_sales',
                'other_sales',
                'other_incomes',
                'customer_payments',
                'cash_payments',
                'card_payments',
                'cheque_payments',
                'credit_sale_payments',
                'expense_payments',
                'excess_payments',
                'shortage_payments'
            ])
            ->select('settlements.*', 'pump_operators.name as pump_operator_name')
            ->first();

        $business = Business::where('id', $settlement->business_id)->first();
        $pump_operator = PumpOperator::where('id', $settlement->pump_operator_id)->first();

        //this for only to show in print page customer payments which entered in customer payments tab
        $customer_payments_tab = CustomerPayment::leftjoin('contacts', 'customer_payments.customer_id', 'contacts.id')
            ->where('customer_payments.settlement_no', $settlement->settlement_no)
            ->where('customer_payments.business_id', $business_id)
            ->select('customer_payments.*', 'contacts.name as customer_name')
            ->get();

        return view('petro::settlement.print')->with(compact('settlement', 'business', 'pump_operator', 'customer_payments_tab'));
    }

    // Added by Muneeb Ahmad for Store Dropdown
    public function getStoresById(Request $request)
    {
        $business_id = $request->session()->get('user.business_id');
        $account_type = null;
        $stores = '<option value="">Please Select</option>';

        $stores = Store::where('business_id', $business_id);
        if($request->location_id){
            $stores = $stores->where('location_id', $request->location_id);
        }
        $stores = $stores->pluck('name', 'id');
        return $this->transactionUtil->createDropdownHtml($stores, 'Please Select');
    }

    public function getProductsByStoreId(Request $request)
    {
        $business_id = $request->session()->get('user.business_id');
        $location_id = $request->location_id;
        $store_id = $request->store_id;
        if($store_id){
            return $this->transactionUtil->getProductsByStoreId($business_id, $location_id, $store_id);
        }else{
            $products = [];
            return $this->transactionUtil->createDropdownHtml($products, 'No Item Found');
        }
    }

    public function getBalanceStockById(Request $request, $id)
    {
        try {
            $product = Product::join('variations', 'products.id', 'variations.product_id')
                        ->join('variation_location_details', 'variations.id', 'variation_location_details.variation_id')
                        ->join('variation_store_details', 'variations.id', 'variation_store_details.variation_id')
                        ->where('variation_store_details.product_id', $id)
                        ->where('variation_location_details.location_id', $request->location_id)
                        ->where('variation_store_details.store_id', $request->store_id)
                        ->select('variation_store_details.qty_available', 'sell_price_inc_tax', 'products.name', 'sku')->first();

            $output = [
                'success' => true,
                'balance_stock' => $product->qty_available,
                'price' => $product->sell_price_inc_tax,
                'product_name' => $product->name,
                'code' => $product->sku,
                'msg' => __('petro::lang.success')
            ];
        } catch (\Exception $e) {
            \Log::emergency('File: ' . $e->getFile() . 'Line: ' . $e->getLine() . 'Message: ' . $e->getMessage());
            $output = [
                'success' => false,
                'msg' => __('messages.something_went_wrong')
            ];
        }

        return $output;
    }
}
