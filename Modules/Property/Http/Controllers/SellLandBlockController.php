<?php

namespace Modules\Property\Http\Controllers;

use App\Account;
use App\AccountTransaction;
use App\Business;
use App\BusinessLocation;
use App\Contact;
use App\ContactGroup;
use App\ContactLedger;
use App\Transaction;
use App\TransactionPayment;
use App\Unit;
use App\Utils\BusinessUtil;
use App\Utils\ModuleUtil;
use App\Utils\ProductUtil;
use App\Utils\TransactionUtil;
use App\Utils\Util;
use Carbon\Carbon;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\Property\Entities\FinanceOption;
use Modules\Property\Entities\PaymentOption;
use Modules\Property\Entities\Property;
use Modules\Property\Entities\PropertyAccountSetting;
use Modules\Property\Entities\PropertyBlock;
use Modules\Property\Entities\PropertySellLine;
use Modules\Superadmin\Entities\Subscription;

class SellLandBlockController extends Controller
{
    protected $moduleUtil;
    protected $commonUtil;
    protected $businessUtil;
    protected $transactionUtil;
    protected $productUtil;
    /**
     * Constructor
     *
     *
     * @return void
     */
    public function __construct(ModuleUtil $moduleUtil, Util $commonUtil, BusinessUtil $businessUtil, TransactionUtil $transactionUtil, ProductUtil $productUtil)
    {
        $this->moduleUtil = $moduleUtil;
        $this->commonUtil = $commonUtil;
        $this->businessUtil = $businessUtil;
        $this->transactionUtil = $transactionUtil;
        $this->productUtil = $productUtil;
        $this->dummyPaymentLine = [
            'method' => 'cash', 'amount' => 0, 'note' => '', 'card_transaction_number' => '', 'card_number' => '', 'card_type' => '', 'card_holder_name' => '', 'card_month' => '', 'card_year' => '', 'card_security' => '', 'cheque_number' => '', 'cheque_date' => '', 'bank_account_number' => '',
            'is_return' => 0, 'transaction_no' => '', 'bank_name' => ''
        ];
    }
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index()
    {  
        return view('property::index');
    }
    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        $business_id = request()->session()->get('user.business_id');
        $property_id = request()->property_id;
        $property = Property::find($property_id);
        $blocks = PropertyBlock::where('property_id', $property_id)->pluck('block_number', 'id');
        $ablocks = PropertyBlock::where('property_id', $property_id)->get();
        $finance_options = FinanceOption::where('business_id', $business_id)->pluck('finance_option', 'id');
        $payment_options = PaymentOption::where('business_id', $business_id)->pluck('payment_option', 'id');
        $payment_types = $this->commonUtil->payment_types();
        $properties = Property::where('business_id', $business_id)->pluck('name', 'id');
        $types = Contact::getContactTypes();
        $is_property_customer = true;
        $contact_id = $this->businessUtil->check_customer_code($business_id, 1);
        $type = 'customer';
        $customer_groups = ContactGroup::forDropdown($business_id);
        $customers = Contact::where('business_id', $business_id)->where('is_property', 1)->pluck('name', 'id')->toArray();
        $payment = $this->dummyPaymentLine;
        $bank_group_accounts = Account::leftjoin('account_groups', 'accounts.asset_type', 'account_groups.id')
            ->where('accounts.business_id', $business_id)
            ->where('account_groups.name', 'Bank Account')
            ->pluck('accounts.name', 'accounts.id');
        return view('property::sell_land_blocks.create')->with(compact(
            'is_property_customer',
            'payment_options',
            'finance_options',
            'payment_types',
            'bank_group_accounts',
            'properties',
            'payment',
            'property',
            'contact_id',
            'customers',
            'types',
            'type',
            'customer_groups',
            'blocks',
            'ablocks'
        ));
    }
    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request)
    {
        // echo "<pre>";
        // print_r($request);
        // dd($request->all(),$payment_record = $request->input('payment'));
        // die();
//         try { 
            $business_id = request()->session()->get('user.business_id');
            DB::table('temp_data')->where('business_id', $business_id)->update(['pos_create_data' => '']);
            $property_id = $request->property_id;
            $transaction_data = $request->only(['date', 'contact_id', 'final_total', 'discount', 'finance_option_id']);
            $busines_location = BusinessLocation::where('business_id', $business_id)->first();
            $transaction_data['location_id'] = !empty($busines_location) ? $busines_location->id : null;
            $transaction_data['discount'] = "2325000";
            $exchange_rate = 1;
            $request->validate([
                'contact_id' => 'required',
                'final_total' => 'required',
            ]);
            $user_id = Auth::user()->id;
            $currency_details = $this->transactionUtil->purchaseCurrencyDetails($business_id);
            //unformat input values
            $transaction_data['total_before_tax'] = $this->productUtil->num_uf($transaction_data['final_total'], $currency_details) * $exchange_rate;
            $transaction_data['tax_amount'] = 0;
            $transaction_data['shipping_charges'] = 0;
            $transaction_data['final_total'] = $this->productUtil->num_uf($transaction_data['final_total'], $currency_details) * $exchange_rate;
            $transaction_data['discount'] = $this->productUtil->num_uf($transaction_data['discount'], $currency_details);
            $transaction_data['business_id'] = $business_id;
            $transaction_data['created_by'] = $user_id;
            $transaction_data['type'] = 'property_sell';
            $transaction_data['payment_status'] = 'due';
            $transaction_data['status'] = 'final';
            $transaction_data['store_id'] = null;
            $transaction_data['transaction_date'] = $this->productUtil->uf_date($transaction_data['date'], false);
            unset($transaction_data['date']);
            unset($transaction_data['discount']);
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
        $current_monthly_sale += $transaction_data['final_total'];
        if($current_monthly_sale > $monthly_max_sale_limit) {
            $output = [
                'success' => 0,
                'msg' => __('lang_v1.monthly_max_sale_limit_exceeded', ['monthly_max_sale_limit' => $monthly_max_sale_limit])
            ];
            return redirect()
                ->action('\Modules\Property\Http\Controllers\SaleAndCustomerPaymentController@dashboard')
                ->with('status', $output);
        }
            DB::beginTransaction();
            //Update reference count
            $ref_count = $this->productUtil->setAndGetReferenceCount('sell');
            //Generate reference number
            if (empty($transaction_data['ref_no'])) {
                $transaction_data['ref_no'] = $this->productUtil->generateReferenceNumber('sell', $ref_count);
            }
            $transaction_data['invoice_no'] = $this->transactionUtil->getInvoiceNumber($business_id, 'final', $transaction_data['location_id']);
            $transaction = Transaction::create($transaction_data);
            $sell_lines = $request->sell_line;
            $sold_block_ids = [];
            $sold_block_nos = [];
            $sold_total_block_value = [];
            if(!empty($sell_lines) && $sell_lines != null && $sell_lines !=''){
                foreach ($sell_lines as $sell_line) {
                    $sell_line_array = [
                        'transaction_id' => $transaction->id,
                        'property_id' => $sell_line['property_id'],
                        'block_id' => $sell_line['block_id'],
                        'block_number' => $sell_line['block_number'],
                        'unit' => $sell_line['unit'],
                        'size' => $sell_line['size'],
                        'block_value' => $sell_line['block_value']
                    ];
                    $sold_block_nos[] = $sell_line['block_number'];
                    $sold_block_ids[] = $sell_line['block_id'];
                    $sold_total_block_value[] = $sell_line['block_value'];
                    PropertySellLine::create($sell_line_array);
                    //PropertyBlock::where('id', $sell_line['block_id'])->update(['block_sold_price' =>  $transaction_data['discount']]);
                }
            }
            if (!empty($sold_block_ids)) {
                PropertyBlock::whereIn('id', $sold_block_ids)->update(['customer_id' => $transaction->contact_id, 'is_sold' => 1, 'sold_by' => $user_id]);
            }
            $payments = $request->input('account_of');
            $payment_record = $request->input('payment');
            $on_account_of['total_amount'] = $request->total_amount;
            // $on_account_of_record = [];
            // foreach($payments as $valPayment){
            //     if($request->payment_method =='bank_transfer'){
            //         $temp['on_account_of'] = $request->payment_option;
            //     }
            // }
             $this->createSellAccountTransactions($transaction, $payments, $payment_record, $business_id);
            // //Add Purchase payments
            //$this->transactionUtil->createOrUpdatePaymentLines($transaction, $payments);
            // //update payment status
            //$this->transactionUtil->updatePaymentStatus($transaction->id, $transaction->final_total);
            //$this->transactionUtil->updatePropertyStatus($property_id);
            DB::commit();
            $business = Business::find($business_id);
            $location_details = BusinessLocation::where('business_id', $business_id)->first();
            $contact = Contact::find($transaction->contact_id);
            $property = Property::find($property_id);
            $block_value = PropertySellLine::where('transaction_id', $transaction->id)->first();
        //    $payments = TransactionPayment::leftjoin('payment_options', 'transaction_payments.payment_option_id', 'payment_options.id')
            //    ->where('transaction_id', $transaction->id)->select('payment_options.payment_option as on_account_of', 'transaction_payments.*')->get();
//
            return view('property::sell_land_blocks.print')->with(compact(
                'business',
                'location_details',
                'transaction',
                'sold_block_nos',
                'contact',
                'property',
                'payments',
                'payment_record',
                'block_value',
                'sold_total_block_value'
            ));
        // } catch (\Exception $e) {
        //     DB::rollBack();
        //     Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());
        //     $output = [
        //         'success' => 0,
        //         'msg' => __('messages.something_went_wrong')
        //     ];
        // }
        // return $output;
    }
    // public function store(Request $request)
    // {
    //     try {
    //         $business_id = request()->session()->get('user.business_id');
    //         DB::table('temp_data')->where('business_id', $business_id)->update(['pos_create_data' => '']);
    //         $property_id = $request->property_id;
    //         $transaction_data = $request->only(['date', 'contact_id', 'final_total', 'discount', 'finance_option_id']);
    //         $busines_location = BusinessLocation::where('business_id', $business_id)->first();
    //         $transaction_data['location_id'] = !empty($busines_location) ? $busines_location->id : null;
    //         $transaction_data['discount'] = "2325000";
    //         $exchange_rate = 1;
    //         $request->validate([
    //             'contact_id' => 'required',
    //             'final_total' => 'required',
    //         ]);
    //         $user_id = Auth::user()->id;
    //         $currency_details = $this->transactionUtil->purchaseCurrencyDetails($business_id);
    //         //unformat input values
    //         $transaction_data['total_before_tax'] = $this->productUtil->num_uf($transaction_data['final_total'], $currency_details) * $exchange_rate;
    //         $transaction_data['tax_amount'] = 0;
    //         $transaction_data['shipping_charges'] = 0;
    //         $transaction_data['final_total'] = $this->productUtil->num_uf($transaction_data['final_total'], $currency_details) * $exchange_rate;
    //         $transaction_data['discount'] = $this->productUtil->num_uf($transaction_data['discount'], $currency_details);
    //         $transaction_data['business_id'] = $business_id;
    //         $transaction_data['created_by'] = $user_id;
    //         $transaction_data['type'] = 'property_sell';
    //         $transaction_data['payment_status'] = 'due';
    //         $transaction_data['status'] = 'final';
    //         $transaction_data['store_id'] = null;
    //         $transaction_data['transaction_date'] = $this->productUtil->uf_date($transaction_data['date'], false);
    //         unset($transaction_data['date']);
    //         unset($transaction_data['discount']);
    //         DB::beginTransaction();
    //         //Update reference count
    //         $ref_count = $this->productUtil->setAndGetReferenceCount('sell');
    //         //Generate reference number
    //         if (empty($transaction_data['ref_no'])) {
    //             $transaction_data['ref_no'] = $this->productUtil->generateReferenceNumber('sell', $ref_count);
    //         }
    //         $transaction_data['invoice_no'] = $this->transactionUtil->getInvoiceNumber($business_id, 'final', $transaction_data['location_id']);
    //         $transaction = Transaction::create($transaction_data);
    //         $sell_lines = $request->sell_line;
    //         $sold_block_ids = [];
    //         $sold_block_nos = [];
    //         foreach ($sell_lines as $sell_line) {
    //             $sell_line_array = [
    //                 'transaction_id' => $transaction->id,
    //                 'property_id' => $sell_line['property_id'],
    //                 'block_id' => $sell_line['block_id'],
    //                 'block_number' => $sell_line['block_number'],
    //                 'unit' => $sell_line['unit'],
    //                 'size' => $sell_line['size'],
    //                 'block_value' => $sell_line['block_value']
    //             ];
    //             $sold_block_nos[] = $sell_line['block_number'];
    //             $sold_block_ids[] = $sell_line['block_id'];
    //             PropertySellLine::create($sell_line_array);
    //             //PropertyBlock::where('id', $sell_line['block_id'])->update(['block_sold_price' =>  $transaction_data['discount']]);
    //         }
    //         // - - - -
    //         if (!empty($sold_block_ids)) {
    //             PropertyBlock::whereIn('id', $sold_block_ids)->update(['customer_id' => $transaction->contact_id, 'is_sold' => 1, 'sold_by' => $user_id]);
    //         }
    //         $payments = $request->input('payment');
    //         // $this->createSellAccountTransactions($transaction);
    //         // //Add Purchase payments
    //         // $this->transactionUtil->createOrUpdatePaymentLines($transaction, $payments);
    //         // //update payment status
    //         // $this->transactionUtil->updatePaymentStatus($transaction->id, $transaction->final_total);
    //         // $this->transactionUtil->updatePropertyStatus($property_id);
    //         DB::commit();
    //         $business = Business::find($business_id);
    //         $location_details = BusinessLocation::where('business_id', $business_id)->first();
    //         $contact = Contact::find($transaction->contact_id);
    //         $property = Property::find($property_id);
    //         $payments = TransactionPayment::leftjoin('payment_options', 'transaction_payments.payment_option_id', 'payment_options.id')
    //             ->where('transaction_id', $transaction->id)->select('payment_options.payment_option as on_account_of', 'transaction_payments.*')->get();
    //         return (String) view('property::sell_land_blocks.print')->with(compact(
    //             'business',
    //             'location_details',
    //             'transaction',
    //             'sold_block_nos',
    //             'contact',
    //             'property',
    //             'payments'
    //         ));
    //     } catch (\Exception $e) {
    //         DB::rollBack();
    //         Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());
    //         $output = [
    //             'success' => 0,
    //             'msg' => __('messages.something_went_wrong')
    //         ];
    //     }
    //     return $output;
    // }
    public function createSellAccountTransactions($transaction, $payments, $payment_record, $business_id)
    {
        $transaction_sell_line = PropertySellLine::where('transaction_id', $transaction->id)->first();
        $property_accounts = PropertyAccountSetting::where('property_id', $transaction_sell_line->property_id)->first();
        $account_transaction_data['contact_id'] = $transaction->contact_id;
        // $account_transaction_data['account_id'] = 0;
        $account_transaction_data['account_id'] = $property_accounts->income_account_id;
        $account_transaction_data['amount'] = $transaction->final_total;
        $account_transaction_data['type'] = 'debit';
        $account_transaction_data['created_by'] = Auth::user()->id;
        $account_transaction_data['transaction_id'] = $transaction->id;
        $account_transaction_data['transaction_sell_line_id'] = $transaction_sell_line->id;
        ContactLedger::createContactLedger($account_transaction_data);
        if(!is_null($payments)) {
            foreach($payments as $payment) {
                $account_transaction_data['type'] = 'debit';
                $account_transaction_data['payment_option_id'] = $payment['payment_option_id'];
                $account_transaction_data['amount'] = $payment['amount'];
                ContactLedger::createContactLedger($account_transaction_data);
                if (!empty($property_accounts->income_account_id)) {
                    $account_transaction_data['type'] = 'credit';
                    $account_transaction_data['account_id'] = $property_accounts->income_account_id;
                    
                    AccountTransaction::createAccountTransaction($account_transaction_data);
                }
                if (!empty($property_accounts->account_receivable_account_id)) {
                    $account_transaction_data['type'] = 'debit';
                    $account_transaction_data['account_id'] = $property_accounts->account_receivable_account_id;
                    AccountTransaction::createAccountTransaction($account_transaction_data);
                }
            }
        }
        if(!is_null($payment_record)) {
            foreach($payment_record as $payment) {
                if(isset($payment['payment_method_amount'])) {
                    $account_transaction_data['amount'] = $payment['payment_method_amount'];
                    if ($payment['method'] === 'card') {
                        $account = Account::where('business_id', $business_id)->where('name', 'Cards (Credit Debit)  Account')->first();
                    } else if($payment['method'] === 'cheque') {
                        $account = Account::where('business_id', $business_id)->where('name', 'Cheques in Hand')->first();
                    } else if($payment['method'] === 'cash') {
                        $account = Account::where('business_id', $business_id)->where('name', 'Cash')->first();
                    }
                    // dd($account);
                    if (!is_null($account)) {
                        $account_transaction_data['type'] = 'debit';
                        $account_transaction_data['account_id'] = $account->id;
                        $account_transaction_data['payment_method'] = $payment['method'];
                        if($payment['method'] === 'cheque'){
                            $account_transaction_data['cheque_number'] = $payment['cheque_number'];
                            $account_transaction_data['bank_name'] = $payment['bank_name'];
                            $account_transaction_data['cheque_date'] = $payment['cheque_date'];
                        }
                        AccountTransaction::createAccountTransaction($account_transaction_data);
                        if (!empty($property_accounts->account_receivable_account_id)) {
                            $account_transaction_data['type'] = 'credit';
                            $account_transaction_data['account_id'] = $property_accounts->account_receivable_account_id;
                            $account_transaction_data['payment_method'] = $payment['method'];
                            if($payment['method'] === 'cheque'){
                                $account_transaction_data['cheque_number'] = $payment['cheque_number'];
                                $account_transaction_data['bank_name'] = $payment['bank_name'];
                                $account_transaction_data['cheque_date'] = $payment['cheque_date'];

                            }
                            AccountTransaction::createAccountTransaction($account_transaction_data);
                        }
                    }
                }
            }
        }
    }
    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {
        return view('property::show');
    }
    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        return view('property::edit');
    }
    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(Request $request, $id)
    {
        //
    }
    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy($id)
    {
        //
    }
}
