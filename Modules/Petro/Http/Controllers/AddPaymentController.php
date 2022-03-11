<?php

namespace Modules\Petro\Http\Controllers;

use App\Account;
use App\AccountGroup;
use App\AccountType;
use App\Business;
use App\BusinessLocation;
use App\Contact;
use App\CustomerReference;
use App\ExpenseCategory;
use App\Product;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Petro\Entities\PumpOperator;
use Modules\Petro\Entities\Settlement;
use App\Utils\Util;
use App\Utils\ProductUtil;
use App\Utils\ModuleUtil;
use App\Utils\TransactionUtil;
use App\Utils\BusinessUtil;
use Carbon\Carbon;
use Modules\Petro\Entities\DailyCollection;
use Modules\Petro\Entities\MeterSale;
use Modules\Petro\Entities\OtherIncome;
use Modules\Petro\Entities\OtherSale;
use Modules\Petro\Entities\SettlementCardPayment;
use Modules\Petro\Entities\SettlementCashPayment;
use Modules\Petro\Entities\SettlementChequePayment;
use Modules\Petro\Entities\SettlementCreditSalePayment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\Petro\Entities\CustomerPayment;
use Modules\Petro\Entities\SettlementExcessPayment;
use Modules\Petro\Entities\SettlementExpensePayment;
use Modules\Petro\Entities\SettlementShortagePayment;

class AddPaymentController extends Controller
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
        return view('petro::index');
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create(Request $request)
    {
        $business_id = request()->session()->get('business.id');

        $settlement_no = $request->settlement_no;
        $pump_operator_id = $request->operator_id;
        $settlement = Settlement::where('settlement_no', $settlement_no)->where('business_id', $business_id)->first();
        $pump_operator = PumpOperator::where('id', $pump_operator_id)->first();

        $business_locations = BusinessLocation::forDropdown($business_id);
        $default_location = current(array_keys($business_locations->toArray()));
        $payment_types = $this->productUtil->payment_types($default_location);

        $expense_no = $this->getExpenseNumber($settlement->id);
        $expense_categories = ExpenseCategory::where('business_id', $business_id)
            ->pluck('name', 'id');
        $expense_account_type_id = AccountType::where('business_id', $business_id)->where('name', 'Expenses')->first();
        $expense_accounts = [];
        if ($this->moduleUtil->hasThePermissionInSubscription($business_id, 'access_account')) {
            if (!empty($expense_account_type_id)) {
                $expense_accounts = Account::where('business_id', $business_id)->where('account_type_id', $expense_account_type_id->id)->pluck('name', 'id');
            }
        }
        $customers = Contact::customersDropdown($business_id, false, true, 'customer');
        $products = Product::where('business_id', $business_id)->pluck('name', 'id');

        $card_types = [];
        $card_group = AccountGroup::where('business_id', $business_id)->where('name', 'Card')->first();
        if (!empty($card_group)) {
            $card_types = Account::where('business_id', $business_id)->where('asset_type', $card_group->id)->where(DB::raw("REPLACE(`name`, '  ', ' ')"), '!=', 'Cards (Credit Debit) Account')->pluck('name', 'id');
        }
      
        $customer_payments_tab = CustomerPayment::leftjoin('contacts', 'customer_payments.customer_id', 'contacts.id')
            ->where('customer_payments.settlement_no', $settlement->id)
            ->select('customer_payments.*', 'contacts.name as customer_name')
            ->get();
        $settlement_cash_payments = SettlementCashPayment::leftjoin('contacts', 'settlement_cash_payments.customer_id', 'contacts.id')
            ->where('settlement_cash_payments.settlement_no', $settlement->id)
            ->select('settlement_cash_payments.*', 'contacts.name as customer_name')
            ->get();
        $settlement_card_payments = SettlementCardPayment::leftjoin('contacts', 'settlement_card_payments.customer_id', 'contacts.id')
            ->leftjoin('accounts', 'settlement_card_payments.card_type', 'accounts.id')
            ->where('settlement_card_payments.settlement_no', $settlement->id)
            ->select('settlement_card_payments.*', 'contacts.name as customer_name', 'accounts.name as card_type')
            ->get();
        $settlement_cheque_payments = SettlementChequePayment::leftjoin('contacts', 'settlement_cheque_payments.customer_id', 'contacts.id')
            ->where('settlement_cheque_payments.settlement_no', $settlement->id)
            // ->where('settlement_cheque_payments.business_id', $business_id)
            ->select('settlement_cheque_payments.*', 'contacts.name as customer_name')
            ->get();
        $settlement_credit_sale_payments = SettlementCreditSalePayment::leftjoin('contacts', 'settlement_credit_sale_payments.customer_id', 'contacts.id')
            ->leftjoin('products', 'settlement_credit_sale_payments.product_id', 'products.id')
            ->where('settlement_credit_sale_payments.settlement_no', $settlement->id)
            ->select('settlement_credit_sale_payments.*', 'contacts.name as customer_name', 'products.name as product_name')
            ->get();
        $settlement_expense_payments = SettlementExpensePayment::leftjoin('accounts', 'settlement_expense_payments.account_id', 'accounts.id')
            ->leftjoin('expense_categories', 'settlement_expense_payments.category_id', 'expense_categories.id')
            ->where('settlement_expense_payments.settlement_no', $settlement->id)
            ->select('settlement_expense_payments.*', 'accounts.name as account_name', 'expense_categories.name as category_name')
            ->get();
        $settlement_shortage_payments = SettlementShortagePayment::where('settlement_shortage_payments.settlement_no', $settlement->id)
            ->select('settlement_shortage_payments.*')
            ->get();
        $settlement_excess_payments = SettlementExcessPayment::where('settlement_excess_payments.settlement_no', $settlement->id)
            ->select('settlement_excess_payments.*')
            ->get();
        /**
         * @ChangedBy Afes
         * @Date 25-05-2021
         * @Date 02-06-2021
         * @Task 12700
         * @Task 127004
         */        
        $total_daily_collection = floatval(DailyCollection::where('pump_operator_id', $pump_operator_id)->where('business_id', $business_id)->whereNull('settlement_id')->sum('current_amount'));  

        /**
         * @ModifiedBy Afes Oktavianus
         * @Date 02-06-2021
         * @Date 03-06-2021
         * @Task 127004
         */
        $total_excess = $this->transactionUtil->getPumpOperatorExcessOrShortage($pump_operator_id, 'excess');
        
        $total_shortage = $this->transactionUtil->getPumpOperatorExcessOrShortage($pump_operator_id, 'shortage');

        $total_commission = $this->calculateCommission($pump_operator->id, $settlement->id);

        $total_meter_sale = MeterSale::where('settlement_no', $settlement->id)->sum('sub_total');
        $total_other_sale = OtherSale::where('settlement_no', $settlement->id)->sum('sub_total');
        $total_other_income = OtherIncome::where('settlement_no', $settlement->id)->sum('sub_total');
        $total_customer_payment = CustomerPayment::where('settlement_no', $settlement->id)->sum('sub_total');

        $total_amount = $total_meter_sale + $total_other_sale + $total_other_income + $total_customer_payment;

        $total_settlement_cash_payment = SettlementCashPayment::where('settlement_no', $settlement->id)->sum('amount');
        $total_settlement_card_payment = SettlementCardPayment::where('settlement_no', $settlement->id)->sum('amount');
        $total_settlement_cheque_payment = SettlementChequePayment::where('settlement_no', $settlement->id)->sum('amount');
        $total_settlement_credit_sale_payment = SettlementCreditSalePayment::where('settlement_no', $settlement->id)->sum('amount');
        $total_settlement_expense_payment = SettlementExpensePayment::where('settlement_no', $settlement->id)->sum('amount');
        $total_settlement_shortage_payment = SettlementShortagePayment::where('settlement_no', $settlement->id)->sum('amount');
        $total_settlement_excess_payment = SettlementExcessPayment::where('settlement_no', $settlement->id)->sum('amount');
        $total_paid = $total_daily_collection + $total_settlement_cash_payment + $total_settlement_card_payment + $total_settlement_cheque_payment + $total_settlement_credit_sale_payment + $total_settlement_expense_payment + $total_settlement_shortage_payment + $total_settlement_excess_payment;

        $business_details = Business::find($business_id);
        $currency_precision = $business_details->currency_precision;
        $total_balance = number_format($total_amount - $total_paid, $currency_precision, '.', '');


        return view('petro::settlement.partials.add_payment')->with(compact(
            'settlement',
            'pump_operator',
            'customer_payments_tab',
            'settlement_cash_payments',
            'settlement_card_payments',
            'settlement_cheque_payments',
            'settlement_credit_sale_payments',
            'settlement_expense_payments',
            'settlement_shortage_payments',
            'settlement_excess_payments',
            'payment_types',
            'expense_accounts',
            'expense_categories',
            'expense_no',
            'customers',
            'products',
            'card_types',
            'total_daily_collection',
            'total_commission',
            'total_amount',
            'total_paid',
            'total_balance',
            'total_excess',
            'total_shortage'
        ));
    }

    public function getExpenseNumber($settlement_id)
    {
        $settlement_int = preg_match_all('!\d+!', $settlement_id, $matches);
        $ref_no_prefixes = request()->session()->get('business.ref_no_prefixes');
        $expense_prefix =   !empty($ref_no_prefixes['expense']) ? $ref_no_prefixes['expense'] : '';
        $expense_count = SettlementExpensePayment::where('settlement_no', $settlement_id)->count();
        $expense_no = $expense_prefix . '-' . $settlement_int . '-' . ($expense_count + 1);

        return $expense_no;
    }

    /**
     * Store a newly created resource in storage.
     * @param  pump_operator_id
     * @param  settlement_no
     * @return Response
     */
    public function calculateCommission($pump_operator_id, $settlement_id)
    {
        $all_sales = OtherSale::where('settlement_no', $settlement_id)->get();

        $pump_operator = PumpOperator::where('id', $pump_operator_id)->first();
        $pump_operator_commission_type = $pump_operator->commission_type;
        $pump_operator_commission_value = $pump_operator->commission_ap;

        if ($pump_operator_commission_type == 'fixed') {
            $total_sales_counts = $all_sales->count();

            return $total_sales_counts * $pump_operator_commission_value;
        } elseif ($pump_operator_commission_type == 'percentage') {
            $total_sales_commission = 0;


            return $total_sales_commission;
        } else {
            return 0.00;
        }
    }
    /**
     * Store a newly created resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function store(Request $request)
    {
    }

    /**
     * Show the specified resource.
     * @return Response
     */
    public function show()
    {
        return view('petro::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @return Response
     */
    public function edit()
    {
        return view('petro::edit');
    }

    /**
     * Update the specified resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function update(Request $request)
    {
    }

    /**
     * Remove the specified resource from storage.
     * @return Response
     */
    public function destroy()
    {
    }


    /**
     * add cash payment data to db
     * @return Response
     */
    public function saveCashPayment(Request $request)
    {
        try {
            $business_id = $request->session()->get('business.id');
            $settlement = Settlement::where('settlement_no', $request->settlement_no)->where('business_id', $business_id)->first();
            $data = array(
                'business_id' => $business_id,
                'settlement_no' => $settlement->id,
                'amount' => $request->amount,
                'customer_id' => $request->customer_id
            );

            $settlement_cash_payment = SettlementCashPayment::create($data);

            $output = [
                'success' => true,
                'settlement_cash_payment_id' => $settlement_cash_payment->id,
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
     * delete cash payment data to db
     * @return Response
     */
    public function deleteCashPayment($id)
    {
        try {
            $payment = SettlementCashPayment::where('id', $id)->first();
            $amount = $payment->amount;
            $payment->delete();
            $output = [
                'success' => true,
                'amount' => $amount,
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
     * add card payment data to db
     * @return Response
     */
    public function saveCardPayment(Request $request)
    {
        try {
            $business_id = $request->session()->get('business.id');
            $settlement = Settlement::where('settlement_no', $request->settlement_no)->where('business_id', $business_id)->first();
            $data = array(
                'business_id' => $business_id,
                'settlement_no' => $settlement->id,
                'amount' => $request->amount,
                'card_type' => $request->card_type,
                'card_number' => $request->card_number,
                'customer_id' => $request->customer_id
            );

            $settlement_card_payment = SettlementCardPayment::create($data);

            $output = [
                'success' => true,
                'settlement_card_payment_id' => $settlement_card_payment->id,
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
     * delete card payment data to db
     * @return Response
     */
    public function deleteCardPayment($id)
    {
        try {
            $payment = SettlementCardPayment::where('id', $id)->first();
            $amount = $payment->amount;
            $payment->delete();
            $output = [
                'success' => true,
                'amount' => $amount,
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
     * add cheque payment data to db
     * @return Response
     */
    public function saveChequePayment(Request $request)
    {
        try {
            $business_id = $request->session()->get('business.id');
            $settlement = Settlement::where('settlement_no', $request->settlement_no)->where('business_id', $business_id)->first();
            $data = array(
                'business_id' => $business_id,
                'settlement_no' => $settlement->id,
                'amount' => $request->amount,
                'bank_name' => $request->bank_name,
                'cheque_number' => $request->cheque_number,
                'cheque_date' => Carbon::parse($request->cheque_date)->format('Y-m-d'),
                'customer_id' => $request->customer_id
            );

            $settlement_cheque_payment = SettlementChequePayment::create($data);

            $output = [
                'success' => true,
                'settlement_cheque_payment_id' => $settlement_cheque_payment->id,
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
     * delete cheque payment data to db
     * @return Response
     */
    public function deleteChequePayment($id)
    {
        try {
            $payment = SettlementChequePayment::where('id', $id)->first();
            $amount = $payment->amount;
            $payment->delete();
            $output = [
                'success' => true,
                'amount' => $amount,
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
     * add credit_sale payment data to db
     * @return Response
     */
    public function saveCreditSalePayment(Request $request)
    {
        try {
            $business_id = $request->session()->get('business.id');
            $settlement = Settlement::where('settlement_no', $request->settlement_no)->where('business_id', $business_id)->first();
            $price = $this->productUtil->num_uf($request->price);
            $qty = $this->productUtil->num_uf($request->qty);
            $amount = $this->productUtil->num_uf($request->amount);
            $data = array(
                'business_id' => $business_id,
                'settlement_no' => $settlement->id,
                'customer_id' => $request->customer_id,
                'product_id' => $request->product_id,
                'order_number' => $request->order_number,
                'order_date' => Carbon::parse($request->order_date)->format('Y-m-d'),
                'price' => $price,
                'qty' => $qty,
                'amount' => $amount,
                'outstanding' => $this->productUtil->num_uf($request->outstanding),
                'credit_limit' => $request->credit_limit,
                'customer_reference' => $request->customer_reference
            );

            $settlement_credit_sale_payment = SettlementCreditSalePayment::create($data);

            $output = [
                'success' => true,
                'settlement_credit_sale_payment_id' => $settlement_credit_sale_payment->id,
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
     * delete credit_sale payment data to db
     * @return Response
     */
    public function deleteCreditSalePayment($id)
    {
        try {
            $payment = SettlementCreditSalePayment::where('id', $id)->first();
            $amount = $payment->amount;
            $payment->delete();
            $output = [
                'success' => true,
                'amount' => $amount,
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
     * get price of product
     * @return Response
     */
    public function getProductPrice(Request $request)
    {
        $product_id =  $request->product_id;
        $product = Product::leftjoin('variations', 'products.id', 'variations.product_id')
            ->where('products.id', $product_id)
            ->select('default_sell_price')
            ->first();
        if (!empty($product)) {
            $price = $product->default_sell_price;
        } else {
            $price = 0.00;
        }
        return ['price' => $price];
    }
    /**
     * get price of product
     * @return Response
     */
    public function getCustomerDetails($customer_id)
    {
        $business_id = request()->session()->get('business.id');
        $query = Contact::leftjoin('transactions AS t', 'contacts.id', '=', 't.contact_id')
            ->leftjoin('contact_groups AS cg', 'contacts.customer_group_id', '=', 'cg.id')
            ->where('contacts.business_id', $business_id)
            ->where('contacts.id', $customer_id)
            ->onlyCustomers()
            ->select([
                'contacts.contact_id', 'contacts.name', 'contacts.created_at', 'total_rp', 'cg.name as customer_group', 'city', 'state', 'country', 'landmark', 'mobile', 'contacts.id', 'is_default',
                DB::raw("SUM(IF(t.type = 'sell' AND t.status = 'final', final_total, 0)) as total_invoice"),
                DB::raw("SUM(IF(t.type = 'sell' AND t.status = 'final', (SELECT SUM(IF(is_return = 1,-1*amount,amount)) FROM transaction_payments WHERE transaction_payments.transaction_id=t.id), 0)) as invoice_received"),
                DB::raw("SUM(IF(t.type = 'sell_return', final_total, 0)) as total_sell_return"),
                DB::raw("SUM(IF(t.type = 'sell_return', (SELECT SUM(amount) FROM transaction_payments WHERE transaction_payments.transaction_id=t.id), 0)) as sell_return_paid"),
                DB::raw("SUM(IF(t.type = 'opening_balance', final_total, 0)) as opening_balance"),
                DB::raw("SUM(IF(t.type = 'advance_payment', -1*final_total, 0)) as advance_payment"),
                DB::raw("SUM(IF(t.type = 'opening_balance', (SELECT SUM(IF(is_return = 1,-1*amount,amount)) FROM transaction_payments WHERE transaction_payments.transaction_id=t.id), 0)) as opening_balance_paid"),
                'email', 'tax_number', 'contacts.pay_term_number', 'contacts.pay_term_type', 'contacts.credit_limit', 'contacts.custom_field1', 'contacts.custom_field2', 'contacts.custom_field3', 'contacts.custom_field4', 'contacts.type'
            ])
            ->groupBy('contacts.id')->first();
        $due = $query->total_invoice - $query->invoice_received + $query->advance_payment;
        $return_due = $query->total_sell_return - $query->sell_return_paid;
        $opening_balance = $query->opening_balance - $query->opening_balance_paid;

        $total_outstanding =  $due -  $return_due + $opening_balance ;
        if (empty($total_outstanding)) {
            $total_outstanding = 0.00;
        }
        if (empty($query->credit_limit)) {
            $credit_limit = 'No Limit';
        } else {
            $credit_limit = $query->credit_limit;
        }
        $business_details = Business::find($business_id);
        $customer_references = CustomerReference::where('contact_id', $customer_id)->where('business_id', $business_id)->select('reference')->get();


        return ['total_outstanding' =>  strval($this->productUtil->num_f($total_outstanding, false, $business_details, true)), 'credit_limit' => strval($credit_limit), 'customer_references' => $customer_references];
    }

    /**
     * add expense payment data to db
     * @return Response
     */
    public function saveExpensePayment(Request $request)
    {
        try {
            $business_id = $request->session()->get('business.id');
            $settlement = Settlement::where('settlement_no', $request->settlement_no)->where('business_id', $business_id)->first();
            $data = array(
                'business_id' => $business_id,
                'settlement_no' => $settlement->id,
                'expense_number' => $request->expense_number,
                'category_id' => $request->category_id,
                'reference_no' => $request->reference_no,
                'account_id' => $request->account_id,
                'reason' => $request->reason,
                'amount' => $request->amount,
            );

            //Update reference count
            $ref_count = $this->transactionUtil->setAndGetReferenceCount('expense');
            //Generate reference number
            if (empty($request->reference_no)) {
                $data['reference_no'] = $this->transactionUtil->generateReferenceNumber('expense', $ref_count);
            }

            $settlement_expense_payment = SettlementExpensePayment::create($data);

            $expense_number = $this->getExpenseNumber($request->settlement_no);

            $output = [
                'success' => true,
                'expense_number' => $expense_number,
                'reference_no' => $settlement_expense_payment->reference_no,
                'settlement_expense_payment_id' => $settlement_expense_payment->id,
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
     * delete expense payment data to db
     * @return Response
     */
    public function deleteExpensePayment($id)
    {
        try {
            $payment = SettlementExpensePayment::where('id', $id)->first();
            $amount = $payment->amount;
            $payment->delete();
            $output = [
                'success' => true,
                'amount' => $amount,
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
     * add shortage payment data to db
     * @return Response
     */
    public function saveShortagePayment(Request $request)
    {
        try {
            $business_id = $request->session()->get('business.id');
            $settlement = Settlement::where('settlement_no', $request->settlement_no)->where('business_id', $business_id)->first();
            $pump_operator = PumpOperator::findOrFail($settlement->pump_operator_id);
            $data = array(
                'business_id' => $business_id,
                'settlement_no' => $settlement->id,
                'amount' => $request->amount,
                'current_shortage' => $pump_operator->short_amount,
            );

            $settlement_shortage_payment = SettlementShortagePayment::create($data);

            $output = [
                'success' => true,
                'settlement_shortage_payment_id' => $settlement_shortage_payment->id,
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
     * delete shortage payment data to db
     * @return Response
     */
    public function deleteShortagePayment($id)
    {
        try {
            $payment = SettlementShortagePayment::where('id', $id)->first();
            $amount = $payment->amount;
            $payment->delete();
            $output = [
                'success' => true,
                'amount' => $amount,
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
     * add excess payment data to db
     * @return Response
     */
    public function saveExcessPayment(Request $request)
    {
        try {
            $business_id = $request->session()->get('business.id');
            $settlement = Settlement::where('settlement_no', $request->settlement_no)->where('business_id', $business_id)->first();
            $pump_operator = PumpOperator::findOrFail($settlement->pump_operator_id);
            $data = array(
                'business_id' => $business_id,
                'settlement_no' => $settlement->id,
                'amount' => $request->amount,
                'current_excess' => $pump_operator->excess_amount,
            );
            if($request->amount > 0){
                $output = [
                    'success' => false,
                    'msg' => __('Please enter the amount with a negative symbol')
                ];
                return $output;
            }

            $settlement_excess_payment = SettlementExcessPayment::create($data);

            $output = [
                'success' => true,
                'settlement_excess_payment_id' => $settlement_excess_payment->id,
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
     * delete excess payment data to db
     * @return Response
     */
    public function deleteExcessPayment($id)
    {
        try {
            $payment = SettlementExcessPayment::where('id', $id)->first();
            $amount = $payment->amount;
            $payment->delete();
            $output = [
                'success' => true,
                'amount' => $amount,
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
     * preview payment details
     * @return Response
     */
    public function preview($id)
    {
        $business_id = request()->session()->get('business.id');

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

        return view('petro::settlement.partials.payment_preview')->with(compact('settlement', 'business', 'pump_operator', 'customer_payments_tab'));
    }

        /**
     * preview payment details
     * @return Response
     */
    public function productPreview($id)
    {
        
        $business_id = request()->session()->get('business.id');

        $settlement = Settlement::where('settlements.id', $id)
            
            ->leftjoin('settlement_credit_sale_payments', 'settlements.id', 'settlement_credit_sale_payments.settlement_no')
            ->leftjoin('products', 'products.id', 'settlement_credit_sale_payments.product_id')
            ->select('settlements.*', 'products.*', 'settlement_credit_sale_payments.*')
            ->get();

        return view('petro::settlement.partials.product_preview')->with(compact('settlement'));
    }
}
