<?php

namespace App\Http\Controllers;

use App\Account;
use App\AccountTransaction;
use App\BusinessLocation;
use App\Contact;
use App\ContactLedger;
use App\Transaction;
use App\TransactionPayment;
use App\Utils\ModuleUtil;
use App\Utils\TransactionUtil;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CustomerPaymentSimpleController extends Controller
{
    protected $transactionUtil;
    protected $moduleUtil;

    /**
     * Constructor
     *
     * @param TransactionUtil $transactionUtil
     * @return void
     */
    public function __construct(TransactionUtil $transactionUtil, ModuleUtil $moduleUtil)
    {
        $this->transactionUtil = $transactionUtil;
        $this->moduleUtil = $moduleUtil;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $business_id = request()->session()->get('business.id');
        $customers = Contact::customersDropdown($business_id, false);

        return view('customer_payment_simple.create')->with(compact(
            'customers'
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
        $payments = $request->payment;
        $business_id = request()->session()->get('business.id');

        try {
            foreach ($payments as $inputs) {
                $contact_id = $inputs['contact_id'];
                unset($inputs['contact_id']);
                $inputs['amount'] = $this->transactionUtil->num_uf($inputs['amount']);
                $inputs['paid_on'] = date('Y-m-d H:i:s');
                $inputs['amount'] = $this->transactionUtil->num_uf($inputs['amount']);
                $inputs['created_by'] = auth()->user()->id;
                $inputs['payment_for'] = $contact_id;
                $inputs['business_id'] = $request->session()->get('business.id');


                $due_payment_type = 'sell';

                $prefix_type = 'purchase_payment';
                if (in_array($due_payment_type, ['sell', 'sell_return'])) {
                    $prefix_type = 'sell_payment';
                }
                $ref_count = $this->transactionUtil->setAndGetReferenceCount($prefix_type);
                //Generate reference number
                $payment_ref_no = $this->transactionUtil->generateReferenceNumber($prefix_type, $ref_count);


                //Upload documents if added
                $inputs['document'] = null;
                $inputs['cheque_date'] = $this->transactionUtil->uf_date($inputs['cheque_date']);

                $location_id = BusinessLocation::where('business_id', $business_id)->first();
                $inputs['account_id'] = $this->transactionUtil->getDefaultAccountId($inputs['method'], $location_id->id);
                DB::beginTransaction();
                $parent_payment = TransactionPayment::create($inputs);
                $inputs['transaction_type'] = $due_payment_type;

                $account_payable = Account::where('business_id', $business_id)->where('name', 'Accounts Payable')->where('is_closed', 0)->first();
                $account_payable_id = !empty($account_payable) ? $account_payable->id : 0;

                $account_transaction_data = [
                    'contact_id' => $contact_id,
                    'amount' => $parent_payment->amount,
                    'account_id' => $parent_payment->account_id,
                    'type' => 'credit',
                    'operation_date' => $parent_payment->paid_on,
                    'created_by' => Auth::user()->id,
                    'transaction_id' => null,
                    'transaction_payment_id' => $parent_payment->id,
                    'note' => null
                ];

                $contact = Contact::findOrFail($contact_id);

                if ($contact->type ==  'customer') {

                    if ($due_payment_type == 'sell_return') {
                        $sell_return_due = Transaction::where('contact_id', $contact_id)->whereIn('type', ['sell_return'])->whereIn('payment_status', ['due', 'partial'])->first();
                        $account_transaction_data['account_id'] = $request->account_id;
                        $account_transaction_data['transaction_id'] = !empty($sell_return_due) ? $sell_return_due->id : null;
                        $account_transaction_data['type'] = 'debit';
                        AccountTransaction::createAccountTransaction($account_transaction_data);
                        $account_transaction_data['sub_type'] = 'payment';
                        ContactLedger::createContactLedger($account_transaction_data);
                    } else {
                        $due_transaction_id = Transaction::where('contact_id', $contact_id)->whereIn('type', ['sell', 'opening_balance'])->whereIn('payment_status', ['due', 'partial'])->first();

                        $account_transaction_data['transaction_id'] = !empty($due_transaction_id) ? $due_transaction_id->id : null;
                        $account_transaction_data['type'] = 'debit';
                        AccountTransaction::createAccountTransaction($account_transaction_data);

                        $account_receivable = Account::where('business_id', $business_id)->where('name', 'Accounts Receivable')->where('is_closed', 0)->first();
                        $account_receivable_id = !empty($account_receivable) ? $account_receivable->id : 0;

                        $account_transaction_data['account_id'] = $account_receivable_id;
                        $account_transaction_data['type'] = 'credit';
                        $account_transaction_data['sub_type'] = 'ledger_show';
                        AccountTransaction::createAccountTransaction($account_transaction_data);
                        $account_transaction_data['contact_id'] = $contact_id;
                        $account_transaction_data['sub_type'] = 'payment';
                        ContactLedger::createContactLedger($account_transaction_data);
                    }
                }
                DB::commit();
                //Distribute above payment among unpaid transactions

                $this->transactionUtil->payAtOnce($parent_payment, $due_payment_type);
            }
            $output = [
                'success' => true,
                'msg' => __('lang_v1.success')
            ];
        } catch (\Exception $e) {
            Log::emergency('File: ' . $e->getFile() . 'Line: ' . $e->getLine() . 'Message: ' . $e->getMessage());
            $output = [
                'success' => false,
                'msg' => __('messages.something_went_wrong')
            ];
        }

        return redirect()->back()->with('status', $output);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
