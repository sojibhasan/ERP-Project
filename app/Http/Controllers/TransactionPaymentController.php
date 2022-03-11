<?php



namespace App\Http\Controllers;



use App\Account;

use App\AccountGroup;

use App\Contact;

use App\Events\TransactionPaymentAdded;

use App\Events\TransactionPaymentDeleted;

use App\Events\TransactionPaymentUpdated;

use App\Transaction;

use App\TransactionPayment;

use App\Utils\BusinessUtil;

use App\Utils\ModuleUtil;

use App\Utils\TransactionUtil;

use App\PaymentMethod;

use App\AccountTransaction;

use App\AccountType;

use App\BusinessLocation;

use App\ContactLedger;

use App\System;

use App\User;

use Carbon\Carbon;

use Yajra\DataTables\DataTables;

use Illuminate\Support\Facades\DB;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Facades\Log;

use Illuminate\Support\FacadesLog;

use Modules\Property\Entities\PropertySellLine;

use Modules\Property\Entities\PaymentOption;



class TransactionPaymentController extends Controller

{

    protected $transactionUtil;

    protected $moduleUtil;

    protected $businessUtil;

    /**

     * Constructor

     *

     * @param TransactionUtil $transactionUtil

     * @return void

     */

    

     public function __construct(TransactionUtil $transactionUtil, ModuleUtil $moduleUtil, BusinessUtil $businessUtil)

    {

        $this->transactionUtil = $transactionUtil;

        $this->moduleUtil = $moduleUtil;

        $this->businessUtil = $businessUtil;

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

        //

    }

    /**

     * Store a newly created resource in storage.

     *

     * @param  \Illuminate\Http\Request  $request

     * @return \Illuminate\Http\Response

     */

    

     public function store(Request $request)

    {

        try {

            $business_id = $request->session()->get('user.business_id');

            $transaction_id = $request->input('transaction_id');

            $transaction = Transaction::where('business_id', $business_id)->findOrFail($transaction_id);

            if (!auth()->user()->can('purchase.payments') || !auth()->user()->can('sell.payments')) {

                abort(403, 'Unauthorized action.');

            }

            if ($transaction->payment_status != 'paid') {

                $inputs = $request->only([

                    'amount', 'method', 'note', 'card_number', 'card_holder_name',

                    'card_transaction_number', 'card_type', 'card_month', 'card_year', 'card_security',

                    'cheque_number', 'bank_account_number'

                ]);

                $inputs['paid_on'] = $this->transactionUtil->uf_date($request->input('paid_on'), true);

                $inputs['transaction_id'] = $transaction->id;

                $inputs['amount'] = $this->transactionUtil->num_uf($inputs['amount']);

                $inputs['created_by'] = auth()->user()->id;

                $inputs['payment_for'] = $transaction->contact_id;

                $inputs['cheque_date'] = !empty($request->cheque_date) ? $this->transactionUtil->uf_date($request->cheque_date) : null;

                if ($inputs['method'] == 'custom_pay_1') {

                    $inputs['transaction_no'] = $request->input('transaction_no_1');

                } elseif ($inputs['method'] == 'custom_pay_2') {

                    $inputs['transaction_no'] = $request->input('transaction_no_2');

                } elseif ($inputs['method'] == 'custom_pay_3') {

                    $inputs['transaction_no'] = $request->input('transaction_no_3');

                }

                if (is_numeric($inputs['method'])) {

                    $inputs['account_id'] = $inputs['method'];

                    $inputs['method'] = 'cash';

                } else {

                    $inputs['account_id'] = $this->transactionUtil->getDefaultAccountId($inputs['method'], $transaction->location_id);

                }

                if ($inputs['method'] == 'bank_transfer' && !empty($request->input('account_id'))) {

                    $inputs['account_id'] = $request->input('account_id');

                }

                if ($inputs['method'] == 'card' && !empty($request->input('account_id'))) {

                    $inputs['account_id'] = $request->input('account_id');

                }

                $prefix_type = 'purchase_payment';

                if (in_array($transaction->type, ['sell', 'sell_return'])) {

                    $prefix_type = 'sell_payment';

                } elseif ($transaction->type == 'expense') {

                    $prefix_type = 'expense_payment';

                }

                DB::beginTransaction();

                $ref_count = $this->transactionUtil->setAndGetReferenceCount($prefix_type);

                //Generate reference number

                $inputs['payment_ref_no'] = $this->transactionUtil->generateReferenceNumber($prefix_type, $ref_count);

                $inputs['reference_no'] = $request->refNo;

                $inputs['business_id'] = $request->session()->get('business.id');

                $inputs['document'] = $this->transactionUtil->uploadFile($request, 'document', 'documents');

                $inputs['is_return'] = !empty($request->is_return) ? $request->is_return : 0; //added by ahmed

                if ($transaction->type == 'sell') {

                    $inputs['paid_in_type'] = 'all_sale_page';

                }

                $tp = TransactionPayment::create($inputs);

                //update payment status

                $this->transactionUtil->updatePaymentStatus($transaction_id, $transaction->final_total);

                $account_id = $inputs['account_id'];

                $account_receivable_id = $this->transactionUtil->account_exist_return_id('Accounts Receivable');

                $account_payable_id =  $this->transactionUtil->account_exist_return_id('Accounts Payable');

                if ($transaction->type == 'sell') {

                    $account_transaction_data = [

                        'amount' => $inputs['amount'],

                        'account_id' => $account_id,

                        'type' => 'debit',

                        'operation_date' => $inputs['paid_on'],

                        'created_by' => Auth::user()->id,

                        'transaction_id' => !empty($transaction) ? $transaction->id : null,

                        'transaction_payment_id' =>  $tp->id

                    ];

                    AccountTransaction::createAccountTransaction($account_transaction_data);

                    $account_transaction_data['account_id'] = $account_receivable_id;

                    $account_transaction_data['type'] = 'credit';

                    AccountTransaction::createAccountTransaction($account_transaction_data);

                    $account_transaction_data['contact_id'] = $transaction->contact_id;

                    $account_transaction_data['sub_type'] = 'payment';

                    ContactLedger::createContactLedger($account_transaction_data);

                } else if ($transaction->type == 'purchase') {

                    $account_transaction_data = [

                        'amount' => $inputs['amount'],

                        'account_id' => $account_id,

                        'type' => 'credit',

                        'operation_date' => $inputs['paid_on'],

                        'created_by' => Auth::user()->id,

                        'transaction_id' => !empty($transaction) ? $transaction->id : null,

                        'transaction_payment_id' =>  $tp->id

                    ];

                    if ($inputs['method'] == 'bank_transfer' || $inputs['method'] == 'direct_bank_deposit') {

                        $account_transaction_data['account_id'] = $inputs['account_id'];

                    }

                    AccountTransaction::createAccountTransaction($account_transaction_data);

                    $account_transaction_data['account_id'] = $account_payable_id;

                    $account_transaction_data['type'] = 'debit';

                    AccountTransaction::createAccountTransaction($account_transaction_data);

                    $account_transaction_data['contact_id'] = $transaction->contact_id;

                    $account_transaction_data['sub_type'] = 'payment';

                    ContactLedger::createContactLedger($account_transaction_data);

                } else {

                    $inputs['transaction_type'] = $transaction->type;

                    event(new TransactionPaymentAdded($tp, $inputs));

                }

                DB::commit();

            }

            $output = [

                'success' => true,

                'msg' => __('purchase.payment_added_success')

            ];

        } catch (\Exception $e) {

            DB::rollBack();

            Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

            $output = [

                'success' => false,

                'msg' => __('messages.something_went_wrong')

            ];

        }

        return redirect()->back()->with(['status' => $output]);

    }

    /**

     * Display the specified resource.

     *

     * @param  int  $id

     * @return \Illuminate\Http\Response

     */

    

     public function show($id)

    {

        if (!auth()->user()->can('purchase.create') && !auth()->user()->can('sell.create')) {

            abort(403, 'Unauthorized action.');

        }

        if (request()->ajax()) {

            $transaction = Transaction::where('id', $id)

                ->with(['contact', 'business', 'transaction_for'])

                ->first();

            $transaction_type = $transaction->type;

            $payments_query = TransactionPayment::where('transaction_id', $id);

            $accounts_enabled = false;

            if ($this->moduleUtil->isModuleEnabled('account')) {

                $accounts_enabled = true;

                $payments_query->with(['payment_account']);

            }

            $payments = $payments_query->get();

            $receipt_no = Transaction::where('business_id', $transaction->business_id)->where('type', $transaction_type)->where('invoice_no', '!=', null)->distinct('invoice_no')->pluck('invoice_no', 'invoice_no');

            $payment_types = $this->transactionUtil->payment_types();

            $on_account_ofs = PaymentOption::where('business_id', $transaction->business_id)->pluck('payment_option', 'id');

            $users = User::where('business_id', $transaction->business_id)->pluck('username', 'id');

            return view('transaction_payment.show_payments')

                ->with(compact(

                    'transaction',

                    'payments',

                    'payment_types',

                    'receipt_no',

                    'id',

                    'accounts_enabled',

                    'users',

                    'on_account_ofs'

                ));

        }

    }

    

    public function getPaymentDatatable($id)

    {

        $payment_types = $this->transactionUtil->payment_types(null, false, true, true, true);

        $transaction = Transaction::where('id', $id)

            ->with(['contact', 'business', 'transaction_for'])

            ->first();

        $payments_query = TransactionPayment::leftjoin('transactions', 'transaction_payments.transaction_id', 'transactions.id')

            ->where('transaction_id', $id)->select('transaction_payments.*');

        if (!empty(request()->start_date) && !empty(request()->end_date)) {

            $payments_query->whereDate('paid_on', '>=', request()->start_date);

            $payments_query->whereDate('paid_on', '<=', request()->end_date);

        }

        if (!empty(request()->method)) {

            $payments_query->where('method', request()->method);

        }

        if (!empty(request()->receipt_no)) {

            $payments_query->where('invoice_no', request()->receipt_no);

        }

        if (!empty(request()->payment_option)) {

            $payments_query->where('payment_option_id', request()->payment_option);

        }

        if (!empty(request()->user_id)) {

            $payments_query->where('created_by', request()->user_id);

        }

        if (!empty(request()->user_id)) {

            $payments_query->where('created_by', request()->user_id);

        }

        return Datatables::of($payments_query)

            ->addColumn(

                'action',

                function ($row) use ($transaction) {

                    $html = '';

                    if ((auth()->user()->can("purchase.payments") && (in_array(

                            $transaction->type,

                            ["purchase", "purchase_return"]

                        ))) || (auth()->user()->can("sell.payments") &&

                            (in_array($transaction->type, ["sell", "sell_return"]))) ||

                        auth()->user()->can("expense.access")

                    ) {

                        $html .= '<button type="button" class="btn btn-info btn-xs edit_payment"

                            data-href="' . action('TransactionPaymentController@edit', [$row->id]) . '"><i

                                class="glyphicon glyphicon-edit"></i></button>

                        &nbsp; <button type="button" class="btn btn-danger btn-xs delete_payment"

                            data-href="' . action('TransactionPaymentController@destroy', [$row->id]) . '"><i

                                class="fa fa-trash" aria-hidden="true"></i></button>

                        &nbsp;

                        <button type="button" class="btn btn-primary btn-xs view_payment"

                            data-href="' . action('TransactionPaymentController@viewPayment', [$row->id]) . '">

                            <i class="fa fa-eye" aria-hidden="true"></i>

                        </button>';

                    }

                    if (!empty($row->document_path)) {

                        $html .= '&nbsp';

                        $html .= '<a href="' . $row->document_path . '" class="btn btn-success btn-xs"

                    download="' . $row->document_name . '"><i class="fa fa-download"

                        data-toggle="tooltip" title="' . __("purchase.download_document") . '"></i></a>';

                        if (isFileImage($row->document_name)) {

                            $html .= '&nbsp';

                            $html .= '<button data-href="' . $row->document_path . '"

                    class="btn btn-info btn-xs view_uploaded_document" data-toggle="tooltip"

                    title="' . __("lang_v1.view_document") . '"><i class="fa fa-picture-o"></i></button> ';

                        }

                    }

                    return $html;

                }

            )

            ->addColumn(

                'account_name',

                function ($row) use ($transaction) {

                    $account_name = null;

                    if ($transaction->type == 'settlement' && $transaction->sub_type == 'expense') {

                        $acct = AccountTransaction::leftjoin(

                            'accounts',

                            'account_transactions.account_id',

                            'accounts.id'

                        )->leftjoin(

                            'account_types',

                            'accounts.account_type_id',

                            'account_types.id'

                        )->where(

                            'account_transactions.transaction_id',

                            $transaction->id

                        )->where(

                            'account_types.name',

                            'Expenses'

                        )->select('accounts.name')->first();

                        $account_name = !empty($acct) ? $acct->name : null;

                    }

                    if ($transaction->type == 'expense') {

                        $expense_account = Account::where('id', $transaction->expense_account)->first();

                        if (!empty($expense_account)) {

                            $account_name = $expense_account->name;

                        } else {

                            $account_name = null;

                        }

                    }

                    if ($account_name == null) {

                        $account_name = $row->payment_account->name ?? '';

                    }

                    return $account_name;

                }

            )

            ->editColumn(

                'method',

                function ($row) use ($payment_types) {

                    $html = '';

                    if ($row->method == "cheque" || $row->method == "bank_transfer") {

                        $html .= __("lang_v1.bank_name") . ': <b>' . $row->payment_account->name . '</b><br>' .

                            __("lang_v1.cheque_number") . ': <b>' . $row->cheque_number . '</b>';

                    } elseif ($row->method == "cash") {

                        if (!empty($account_id)) {

                            $cash_account = Account::find($account_id);

                            $html .= $cash_account->name;

                        } else {

                            $html .= $payment_types[$row->method];

                        }

                    } else {

                        $html .= $payment_types[$row->method];

                    }

                    return $html;

                }

            )

            ->addColumn('on_account_of', function ($row) use ($transaction) {

                $on_account_of = '';

                if ($transaction->type == 'property_sell') {

                    $on_account_of = PaymentOption::find($row->payment_option_id)->payment_option;

                }

                return  $on_account_of;

            })

            ->editColumn('amount', '{{@num_format($amount) }}')

            ->editColumn('paid_on', '{{@format_datetime($paid_on) }}')

            ->removeColumn('id')

            ->rawColumns(['action', 'method'])

            ->make(true);

    }

    /**

     * Display the specified resource.

     *

     * @param  int  $id

     * @return \Illuminate\Http\Response

     */

    

     public function pendingPayment($id)

    {

        if (request()->ajax()) {

            $transaction = Transaction::where('id', $id)

                ->with(['contact', 'business', 'transaction_for'])

                ->first();

            $payments_query = TransactionPayment::where('transaction_id', $id);

            $accounts_enabled = false;

            if ($this->moduleUtil->isModuleEnabled('account')) {

                $accounts_enabled = true;

                $payments_query->with(['payment_account']);

            }

            $payments = $payments_query->get();

            $payment_types = $this->transactionUtil->payment_types();

            return view('transaction_payment.pending_payments')

                ->with(compact('transaction', 'payments', 'payment_types', 'accounts_enabled', 'id'));

        }

    }

    /**

     * Display the specified resource.

     *

     * @param  int  $id

     * @return \Illuminate\Http\Response

     */

    

     public function pendingPaymentConfirm($transaction_id)

    {

        $business_id = request()->session()->get('user.business_id');

        $transaction = Transaction::where('business_id', $business_id)->findOrFail($transaction_id);

        $location = BusinessLocation::find($transaction->location_id);

        $default_payment_accounts = !empty($location->default_payment_accounts) ? json_decode($location->default_payment_accounts, true) : [];

        try {

            DB::beginTransaction();

            //update payment status

            $this->transactionUtil->updatePaymentStatus($transaction_id, $transaction->final_total);

            $tp = TransactionPayment::where('transaction_id', $transaction_id)->first();

            $inputs['transaction_type'] = $transaction->type;

            $inputs['amount'] = $transaction->final_total;

            $inputs['method'] = 'bank_transfer';

            $inputs['account_id'] = $default_payment_accounts['bank_transfer']['account'];

            event(new TransactionPaymentAdded($tp, $inputs));

            DB::commit();

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

     * Show the form for editing the specified resource.

     *

     * @param  int  $id

     * @return \Illuminate\Http\Response

     */

    

     public function edit($id)

    {

        if (!auth()->user()->can('purchase.create') && !auth()->user()->can('sell.create')) {

            abort(403, 'Unauthorized action.');

        }

        if (request()->ajax()) {

            $business_id = request()->session()->get('user.business_id');

            $payment_line = TransactionPayment::findOrFail($id);

            //if has the parent transaction

            if (!empty($payment_line->parent_id)) {

                $parent_payment = TransactionPayment::where('id', $payment_line->parent_id)->first();

                if (!empty($parent_payment)) {

                    $payment_line->amount = $parent_payment->amount;

                }

            }

            $transaction = Transaction::where('id', $payment_line->transaction_id)

                ->where('business_id', $business_id)

                ->with(['contact', 'location'])

                ->first();

            if ($transaction->type == 'expense') {

                $payment_types = $this->transactionUtil->payment_types($transaction->location, true, false, true);

            } else if ($transaction->type == 'purchase' || $transaction->type == 'property_purchase') {

                $payment_types = $this->transactionUtil->payment_types($transaction->location, true, true);

            } else if ($transaction->type == 'sell' || $transaction->type == 'property_sell') {

                $payment_types = $this->transactionUtil->payment_types($transaction->location, true, false, false, true);

            } else {

                $payment_types = $this->transactionUtil->payment_types($transaction->location);

            }

            //Accounts

            $accounts = $this->moduleUtil->accountsDropdown($business_id, true);

            $expense_accounts = [];

            $expense_account_type_id = AccountType::where('business_id', $business_id)->where('name', 'Expenses')->first();

            if ($this->moduleUtil->isModuleEnabled('account') && $transaction->type == 'expense') {

                if (!empty($expense_account_type_id)) {

                    $expense_accounts = Account::where('business_id', $business_id)->where('account_type_id', $expense_account_type_id->id)->pluck('name', 'id');

                }

            }

            $selectedAccount = Account::find($payment_line->account_id);

            return view('transaction_payment.edit_payment_row')

                ->with(compact('transaction', 'payment_types', 'payment_line', 'accounts', 'expense_accounts','selectedAccount'));

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

        if (!auth()->user()->can('purchase.payments') && !auth()->user()->can('sell.payments')) {

            abort(403, 'Unauthorized action.');

        }

        try {

            $payment = TransactionPayment::findOrFail($id);

            $transaction = Transaction::findOrFail($payment->transaction_id);

            $inputs = $request->only([

                'amount', 'method', 'note', 'card_number', 'card_holder_name',

                'card_transaction_number', 'card_type', 'card_month', 'card_year', 'card_security',

                'cheque_number', 'bank_account_number'

            ]);

            $inputs['paid_on'] = $this->transactionUtil->uf_date($request->input('paid_on'));

            $inputs['amount'] = $this->transactionUtil->num_uf($inputs['amount']);

            $amount_difference = $payment->amount - $inputs['amount'];

            if ($inputs['method'] == 'custom_pay_1') {

                $inputs['transaction_no'] = $request->input('transaction_no_1');

            } elseif ($inputs['method'] == 'custom_pay_2') {

                $inputs['transaction_no'] = $request->input('transaction_no_2');

            } elseif ($inputs['method'] == 'custom_pay_3') {

                $inputs['transaction_no'] = $request->input('transaction_no_3');

            }

            if (is_numeric($inputs['method'])) {

                $inputs['account_id'] = $inputs['method'];

                $inputs['method'] = 'cash';

            } else {

                $inputs['account_id'] = $this->transactionUtil->getDefaultAccountId($inputs['method'], $transaction->location_id);

            }

            if ($inputs['method'] == 'bank_transfer' && !empty($request->input('account_id'))) {

                $inputs['account_id'] = $request->input('account_id');

            }

            DB::beginTransaction();

            //Update parent payment if exists

            if (!empty($payment->parent_id)) {

                $parent_payment = TransactionPayment::find($payment->parent_id);

                $parent_payment->update($inputs);

                $this->transactionUtil->updatePaymentAtOnce($parent_payment, $transaction->type);

            }

            $business_id = $request->session()->get('user.business_id');

            $transaction = Transaction::where('business_id', $business_id)

                ->find($payment->transaction_id);

            $document_name = $this->transactionUtil->uploadFile($request, 'document', 'documents');

            if (!empty($document_name)) {

                $inputs['document'] = $document_name;

            }

            if (empty($payment->parent_id)) {

                $payment->update($inputs);

            }

            //update payment status

            $this->transactionUtil->updatePaymentStatus($payment->transaction_id);

            if ($transaction->type == 'expense') {

                $transaction->expense_account = $request->expense_account;

                $transaction->save();

                AccountTransaction::where('transaction_id', $transaction->id)->delete();

                $this->addExpenseAccountTransaction($transaction,  $inputs, $business_id);

            } else {

                //

                if ($transaction->type == 'sell') {

                    $account_receivable_id = $this->transactionUtil->account_exist_return_id('Accounts Receivable');

                    if (!empty($payment->parent_id)) {

                        AccountTransaction::where('account_id', $account_receivable_id)->where('transaction_payment_id', $payment->parent_id)->update(['amount' => $inputs['amount'],  'operation_date' => $inputs['paid_on']]);

                    }

                }

                if ($transaction->type == 'property_sell') {

                    $account_settings = $this->transactionUtil->getPropertyAccountSettingsByTransaction($transaction->id);

                    if (!empty($account_settings)) {

                        $account_receivable_account_id = $account_settings->account_receivable_account_id;

                        AccountTransaction::where('account_id', $account_receivable_account_id)->where('transaction_payment_id', $payment->id)->update(['amount' => $inputs['amount'],  'operation_date' => $inputs['paid_on']]);

                    }

                }

                //event

                event(new TransactionPaymentUpdated($payment, $transaction->type));

            }

            DB::commit();

            $output = [

                'success' => true,

                'msg' => __('purchase.payment_updated_success')

            ];

        } catch (\Exception $e) {

            DB::rollBack();

            Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

            $output = [

                'success' => false,

                'msg' => __('messages.something_went_wrong')

            ];

        }

        return redirect()->back()->with(['status' => $output]);

    }

    /**

     * Add Account Transactions

     *

     * @param  int  $id

     * @return \Illuminate\Http\Response

     */

    

     public function addExpenseAccountTransaction($transaction,  $payment, $business_id)

    {

        if (!empty($transaction->expense_account)) {

            $ob_transaction_data = [

                'amount' => $transaction->final_total,

                'account_id' => $transaction->expense_account,

                'type' => 'debit',

                'sub_type' => 'expense',

                'operation_date' => \Carbon::now(),

                'created_by' => Auth::user()->id,

                'transaction_id' => $transaction->id

            ];

            AccountTransaction::createAccountTransaction($ob_transaction_data);

            $account_payable_id = Account::where('business_id', $business_id)->where('name', 'Accounts Payable')->first()->id;

            $ap_transaction_data = [

                'operation_date' => \Carbon::now(),

                'created_by' => Auth::user()->id,

                'transaction_id' => $transaction->id

            ];

            //if no amount paid

            if ($payment['amount'] == 0) {

                $ap_transaction_data['amount'] = $transaction->final_total;

                $ap_transaction_data['account_id'] = $account_payable_id;

                $ap_transaction_data['type'] = 'credit';

                AccountTransaction::createAccountTransaction($ap_transaction_data);

            }

            //if partial amount paid

            else if ($payment['amount'] < $transaction->final_total) {

                $ap_transaction_data['amount'] = $payment['amount'];  //paid amount

                $ap_transaction_data['account_id'] =  $this->transactionUtil->getDefaultAccountId($payment['method'], $transaction->location_id);

                $ap_transaction_data['type'] = 'credit';

                AccountTransaction::createAccountTransaction($ap_transaction_data);

                $ap_transaction_data['amount'] = $transaction->final_total - $payment['amount']; //unpaid amount

                $ap_transaction_data['account_id'] = $account_payable_id;

                $ap_transaction_data['type'] = 'credit';

                AccountTransaction::createAccountTransaction($ap_transaction_data);

            }

            // if full amount paid

            if ($payment['amount'] == $transaction->final_total) {

                $ap_transaction_data['amount'] = $payment['amount'];  // full paid amount

                $ap_transaction_data['account_id'] =  $this->transactionUtil->getDefaultAccountId($payment['method'], $transaction->location_id);

                $ap_transaction_data['type'] = 'credit';

                AccountTransaction::createAccountTransaction($ap_transaction_data);

            }

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

        if (!auth()->user()->can('purchase.payments') && !auth()->user()->can('sell.payments')) {

            abort(403, 'Unauthorized action.');

        }

        if (request()->ajax()) {

            try {

                $payment = TransactionPayment::findOrFail($id);

                DB::beginTransaction();

                //Update parent payment if exists

                if (!empty($payment->parent_id)) {

                    $parent_payment = TransactionPayment::find($payment->parent_id);

                    $parent_payment->amount -= $payment->amount;

                    if ($parent_payment->amount <= 0) {

                        $parent_payment->delete();

                    } else {

                        $parent_payment->save();

                    }

                }

                $transaction_id = $payment->transaction_id;

                $transaction = Transaction::findOrFail($transaction_id);

                $this->transactionUtil->deleteAccountAndLedgerTransactionReverse($transaction, $id);

                $payment->delete();

                //update payment status

                $this->transactionUtil->updatePaymentStatus($payment->transaction_id);

                if ($transaction->sub_type == 'excess' || $transaction->sub_type == 'shortage') {

                    $transaction->payment_status = 'due';

                    $transaction->save();

                }

                if ($transaction->type != 'purchase' && $transaction->type != 'sell' && $transaction->sub_type != 'excess' && $transaction->sub_type != 'shortage') {

                    event(new TransactionPaymentDeleted($payment->id, $payment->account_id));

                }

                DB::commit();

                $output = [

                    'success' => true,

                    'msg' => __('purchase.payment_deleted_success')

                ];

            } catch (\Exception $e) {

                Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

                $output = [

                    'success' => false,

                    'msg' => __('messages.something_went_wrong')

                ];

            }

            return $output;

        }

    }

    /**

     * Adds new payment to the given transaction.

     *

     * @param  int  $transaction_id

     * @return \Illuminate\Http\Response

     */

    

     public function addPayment($transaction_id)

    {

        if (!auth()->user()->can('purchase.payments') && !auth()->user()->can('sell.payments')) {

            abort(403, 'Unauthorized action.');

        }

        if (request()->ajax()) {

            $business_id = request()->session()->get('user.business_id');

            $transaction = Transaction::where('id', $transaction_id)

                ->where('business_id', $business_id)

                ->with(['contact', 'location'])

                ->first();

            if ($transaction->payment_status != 'paid') {

                if ($transaction->type == 'expense') {

                    $payment_types = $this->transactionUtil->payment_types($transaction->location, true, false, true);

                } else if ($transaction->type == 'purchase' || $transaction->type == 'property_purchase') {

                    $payment_types = $this->transactionUtil->payment_types($transaction->location, true, true);

                } else if ($transaction->type == 'sell' || $transaction->type == 'property_sell') {

                    $payment_types = $this->transactionUtil->payment_types($transaction->location, true, false, false, true);

                } else {

                    $payment_types = $this->transactionUtil->payment_types($transaction->location);

                }

                $paid_amount = $this->transactionUtil->getTotalPaid($transaction_id);

                $amount = $transaction->final_total - $paid_amount;

                if ($amount < 0) {

                    $amount = 0;

                }

                $amount_formated = $this->transactionUtil->num_f($amount);

                $payment_line = new TransactionPayment();

                $payment_line->amount = $amount;

                $payment_line->method = 'cash';

                $payment_line->paid_on = \Carbon::now()->toDateTimeString();

                $current_account_type_id = AccountType::where('business_id', $business_id)->where('name', 'Current Assets')->first();

                $account_module = $this->moduleUtil->isModuleEnabled('account');

                if ($transaction->type == 'expense') {

                    $accounts = $this->moduleUtil->accountsDropdown($business_id, true);

                    if ($this->moduleUtil->isModuleEnabled('account')) {

                        $accounts = Account::where('business_id', $business_id)->where('account_type_id', $current_account_type_id->id)->notClosed()->pluck('name', 'id');

                    }

                } else {

                    //Accounts

                    $accounts = $this->moduleUtil->accountsDropdown($business_id, true);

                }

                $trans_count = Transaction::count();

                // $refNo = $transaction->ref_no ?$transaction->ref_no : "SP-".$trans_count;

                $refNo = "SP-".$trans_count;

                $view = view('transaction_payment.payment_row')

                    ->with(compact('transaction', 'payment_types', 'account_module', 'payment_line', 'amount_formated', 'accounts','refNo'))->render();

                $output = [

                    'status' => 'due',

                    'view' => $view

                ];

            } else {

                $output = [

                    'status' => 'paid',

                    'view' => '',

                    'msg' => __('purchase.amount_already_paid')

                ];

            }

            return json_encode($output);

        }

    }

    /**

     * Shows contact's advance payment modal

     *

     * @param  int  $contact_id

     * @return \Illuminate\Http\Response

     */

    

     public function getAdvancePayment($contact_id)

    {

        if (!auth()->user()->can('purchase.create')) {

            abort(403, 'Unauthorized action.');

        }

        if (request()->ajax()) {

            $business_id = request()->session()->get('user.business_id');

            $business_locations = BusinessLocation::where('business_id', $business_id)->pluck('name', 'id');

            $business_location_id = BusinessLocation::where('business_id', $business_id)->first()->id;

            $contact_details = Contact::where('id', $contact_id)->first();

            $payment_types = $this->transactionUtil->payment_types($business_location_id);

            unset($payment_types['credit_sale']);  // removing credit sale method from array

            $clati = 0;

            $current_liability_account_type_id = AccountType::where('name', 'Current Liabilities')->where('business_id', $business_id)->first();

            if (!empty($current_liability_account_type_id)) {

                $clati = $current_liability_account_type_id->id;

            }

            //Accounts

            $advance_to_supplier_account_id = $this->transactionUtil->account_exist_return_id('Advances to Suppliers');

            $customer_deposit_account_id = $this->transactionUtil->account_exist_return_id('Customer Deposits');

            if ($contact_details->type == 'customer') {

                $accounts = Account::where('business_id', $business_id)->where('id', $customer_deposit_account_id)->where('is_closed', 0)->pluck('name', 'id');

            } else {

                $accounts = Account::where('business_id', $business_id)->where('id', $advance_to_supplier_account_id)->where('is_closed', 0)->pluck('name', 'id');

            }

            $prefix_type = 'advance_payment';

            $ref_count = $this->transactionUtil->onlyGetReferenceCount($prefix_type, $business_id, false);

            //Generate reference number

            $payment_ref_no = $this->transactionUtil->generateReferenceNumber($prefix_type, $ref_count);

            return view('transaction_payment.customer_advance_payment')

                ->with(compact('business_locations', 'business_location_id', 'contact_details', 'payment_types', 'accounts', 'payment_ref_no', 'contact_id', 'customer_deposit_account_id'));

        }

    }

    /**

     * Adds Advance Payments for Contact

     *

     * @param  \Illuminate\Http\Request  $request

     * @return \Illuminate\Http\Response

     */

    

     public function postAdvancePayment(Request  $request)

    {

        if (!auth()->user()->can('purchase.create') && !auth()->user()->can('sell.create')) {

            abort(403, 'Unauthorized action.');

        }

        try {

            $business_id = request()->session()->get('user.business_id');

            $contact_id = $request->input('contact_id');

            $contact_id = Contact::where('contact_id', $contact_id)->where('business_id', $business_id)->first()->id;

            $inputs = $request->only([

                'amount', 'method', 'note', 'card_number', 'card_holder_name',

                'card_transaction_number', 'card_type', 'card_month', 'card_year', 'card_security',

                'cheque_number', 'bank_account_number'

            ]);

            $inputs['paid_on'] = !empty($inputs['paid_on']) ? Carbon::parse($inputs['paid_on'])->format('Y-m-d') : date('Y-m-d');

            $inputs['amount'] = $this->transactionUtil->num_uf($inputs['amount']);

            $inputs['created_by'] = auth()->user()->id;

            $inputs['payment_for'] = $contact_id;

            $inputs['business_id'] = $request->session()->get('business.id');

            if ($inputs['method'] == 'custom_pay_1') {

                $inputs['transaction_no'] = $request->input('transaction_no_1');

            } elseif ($inputs['method'] == 'custom_pay_2') {

                $inputs['transaction_no'] = $request->input('transaction_no_2');

            } elseif ($inputs['method'] == 'custom_pay_3') {

                $inputs['transaction_no'] = $request->input('transaction_no_3');

            }

            $payment_type = $request->type;

            $prefix_type = $request->type;

            $ref_count = $this->transactionUtil->setAndGetReferenceCount($prefix_type);

            //Generate reference number

            $payment_ref_no = $this->transactionUtil->generateReferenceNumber($prefix_type, $ref_count);

            $inputs['payment_ref_no'] = $payment_ref_no;

            $business_location = BusinessLocation::where('business_id', $business_id)

                ->first();

            $inputs['account_id'] = $request->account_id;

            //Upload documents if added

            $inputs['document'] = $this->transactionUtil->uploadFile($request, 'document', 'documents');

            DB::beginTransaction();

            //Add opening balance

            $transaction = $this->transactionUtil->createAdvancePaymentTransaction($business_id, $contact_id, $inputs['amount'], $inputs['account_id'], $payment_type, $inputs['paid_on']);

            $inputs['transaction_id'] = $transaction->id;

            $parent_payment = TransactionPayment::create($inputs);

            ContactLedger::where('transaction_id', $transaction->id)->update(['transaction_payment_id' => $parent_payment->id]);

            AccountTransaction::where('transaction_id', $transaction->id)->update(['transaction_payment_id' => $parent_payment->id]);

            DB::commit();

            $output = [

                'success' => true,

                'msg' => __('purchase.payment_added_success')

            ];

        } catch (\Exception $e) {

            DB::rollBack();

            Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

            $output = [

                'success' => false,

                'msg' => __('messages.something_went_wrong')

            ];

        }

        return redirect()->back()->with(['status' => $output]);

    }

    /**

     * Shows contact's refund/cheque return payment modal

     *

     * @param  int  $contact_id

     * @return \Illuminate\Http\Response

     */

    

     public function getRefundPayment($contact_id)

    {

        if (!auth()->user()->can('purchase.create')) {

            abort(403, 'Unauthorized action.');

        }

        if (request()->ajax()) {

            $business_id = request()->session()->get('user.business_id');

            $contact_details = Contact::where('id', $contact_id)->first();

            $payment_types = ['cash' => __('lang_v1.cash'), 'cheque' => __('lang_v1.cheque'), 'bank_transfer' => __('lang_v1.bank')];;

            $clati = 0;

            $current_liability_account_type_id = AccountType::where('name', 'Current Liabilities')->where('business_id', $business_id)->first();

            if (!empty($current_liability_account_type_id)) {

                $clati = $current_liability_account_type_id->id;

            }

            $bank_account_group_id = AccountGroup::getGroupByName('Bank Account');

            $bank_accounts = Account::where('business_id', $business_id)->where('asset_type', $bank_account_group_id->id)->get();

            $invoices = Transaction::where('contact_id', $contact_id)->where('type', 'sell')->pluck('invoice_no', 'invoice_no');

            //Accounts

            $advance_to_supplier_account_id = $this->transactionUtil->account_exist_return_id('Advances to Suppliers');

            $customer_deposit_account_id = $this->transactionUtil->account_exist_return_id('Customer Deposits');

            if ($contact_details->type == 'customer') {

                $accounts = Account::where('business_id', $business_id)->where('id', $customer_deposit_account_id)->where('is_closed', 0)->pluck('name', 'id');

            } else {

                $accounts = Account::where('business_id', $business_id)->where('id', $advance_to_supplier_account_id)->where('is_closed', 0)->pluck('name', 'id');

            }

            $cheque_array = [];

            $cheque_banks = AccountTransaction::leftjoin('accounts', 'account_transactions.account_id', 'accounts.id')

                ->leftjoin('transaction_payments', 'account_transactions.transaction_payment_id', 'transaction_payments.parent_id')

                ->leftjoin('transactions', 'transaction_payments.transaction_id', 'transactions.id')

                ->where('account_transactions.sub_type', 'deposit')

                ->where('transaction_payments.method', 'cheque')

                ->where('account_transactions.type', 'debit')

                ->where('transactions.contact_id', $contact_id)

                ->pluck('accounts.name', 'accounts.id');

            return view('transaction_payment.customer_refund_payment')

                ->with(compact(

                    'invoices',

                    'bank_accounts',

                    'contact_details',

                    'payment_types',

                    'accounts',

                    'contact_id',

                    'customer_deposit_account_id',

                    'cheque_array',

                    'cheque_banks'

                ));

        }

    }

    /**

     * Adds Advance Payments for Contact

     *

     * @param  \Illuminate\Http\Request  $request

     * @return \Illuminate\Http\Response

     */

    

     public function postRefundPayment(Request  $request)

    {

        if (!auth()->user()->can('purchase.create') && !auth()->user()->can('sell.create')) {

            abort(403, 'Unauthorized action.');

        }

        try {

            $business_id = request()->session()->get('user.business_id');

            $contact_id = $request->input('contact_id');

            $contact_id = Contact::where('contact_id', $contact_id)->where('business_id', $business_id)->first()->id;

            $inputs = $request->only([

                'amount', 'method', 'note', 'card_number', 'card_holder_name',

                'card_transaction_number', 'card_type', 'card_month', 'card_year', 'card_security', 'cheque_date',

                'cheque_number', 'bank_account_number', 'transfer_date', 'bank_name', 'sale_invoice_bill_number', 'cheque_return_charges'

            ]);

            $inputs['paid_on'] = !empty($inputs['paid_on']) ? Carbon::parse($inputs['paid_on'])->format('Y-m-d') : date('Y-m-d');

            $inputs['amount'] = $this->transactionUtil->num_uf($inputs['amount']);

            $inputs['created_by'] = auth()->user()->id;

            $inputs['payment_for'] = $contact_id;

            $inputs['business_id'] = $request->session()->get('business.id');

            $method =  $request->input('method');

            if ($request->type == 'cheque_return') {

                $method = 'bank_transfer';

                $inputs['method'] =  $method;

                $inputs['cheque_number'] =  $this->getPaymentDetailsById($request->cheque_number_return)->cheque_number;

                $inputs['bank_name'] =  $request->bank_name_text;

            }

            if ($inputs['method'] == 'custom_pay_1') {

                $inputs['transaction_no'] = $request->input('transaction_no_1');

            } elseif ($inputs['method'] == 'custom_pay_2') {

                $inputs['transaction_no'] = $request->input('transaction_no_2');

            } elseif ($inputs['method'] == 'custom_pay_3') {

                $inputs['transaction_no'] = $request->input('transaction_no_3');

            }

            $payment_type = $request->type;

            $prefix_type = $request->type;

            $ref_count = $this->transactionUtil->setAndGetReferenceCount($prefix_type);

            //Generate reference number

            $payment_ref_no = $this->transactionUtil->generateReferenceNumber($prefix_type, $ref_count);

            $inputs['payment_ref_no'] = $payment_ref_no;

            $business_location = BusinessLocation::where('business_id', $business_id)

                ->first();

            $cheque_bank = $request->cheque_bank;

            $inputs['account_id'] = $cheque_bank;

            //Upload documents if added

            $inputs['document'] = $this->transactionUtil->uploadFile($request, 'document', 'documents');

            DB::beginTransaction();

            //Add opening balance

            $transaction = $this->transactionUtil->createRefundPaymentTransaction($business_id, $contact_id, $inputs['amount'], $inputs['account_id'], $payment_type, $inputs['paid_on'], $cheque_bank);

            $inputs['transaction_id'] = $transaction->id;

            unset($inputs['sale_invoice_bill_number']);

            unset($inputs['cheque_return_charges']);

            $inputs['transfer_date'] = !empty($inputs['transfer_date']) ? Carbon::parse($inputs['transfer_date'])->format('Y-m-d') : null;

            $inputs['cheque_date'] = !empty($inputs['cheque_date']) ? Carbon::parse($inputs['cheque_date'])->format('Y-m-d') : null;

            $inputs['is_return'] = 1;

            $parent_payment = TransactionPayment::create($inputs);

            ContactLedger::where('transaction_id', $transaction->id)->update(['transaction_payment_id' => $parent_payment->id]);

            AccountTransaction::where('transaction_id', $transaction->id)->update(['transaction_payment_id' => $parent_payment->id]);

            DB::commit();

            $output = [

                'success' => true,

                'msg' => __('purchase.payment_added_success')

            ];

        } catch (\Exception $e) {

            DB::rollBack();

            Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

            $output = [

                'success' => false,

                'msg' => __('messages.something_went_wrong')

            ];

        }

        return redirect()->back()->with(['status' => $output]);

    }

    /**

     * Shows contact's advance payment modal

     *

     * @param  int  $contact_id

     * @return \Illuminate\Http\Response

     */

    

     public function getSecurityDeposit($contact_id)

    {

        if (!auth()->user()->can('purchase.create')) {

            abort(403, 'Unauthorized action.');

        }

        if (request()->ajax()) {

            $business_id = request()->session()->get('user.business_id');

            $business_locations = BusinessLocation::where('business_id', $business_id)->pluck('name', 'id');

            $business_location_id = BusinessLocation::where('business_id', $business_id)->first()->id;

            $contact_details = Contact::leftjoin('transactions AS t', 'contacts.id', '=', 't.contact_id')

                ->where('contacts.id', $contact_id)->first();

            $payment_types = $this->transactionUtil->payment_types($business_location_id);

            unset($payment_types['credit_sale']);  // removing credit sale method from array

            $clati = 0;

            $current_liability_account_type_id = AccountType::where('name', 'Current Liabilities')->where('business_id', $business_id)->first();

            if (!empty($current_liability_account_type_id)) {

                $clati = $current_liability_account_type_id->id;

            }

            //Accounts

            $current_libility_account_id = $this->transactionUtil->account_exist_return_id('Customer Deposits');

            $current_libility_accounts = Account::where('business_id', $business_id)->where('parent_account_id',  $current_libility_account_id)->where('is_closed', 0)->pluck('name', 'id');

            if (count($current_libility_accounts) == 0) {

                $current_libility_accounts = Account::where('id', $current_libility_account_id)->where('is_closed', 0)->pluck('name', 'id');

            }

            $account_access = $this->moduleUtil->hasThePermissionInSubscription($business_id, 'access_account');

            $disabled = '';

            $message = '';

            if (!$account_access) {

                $font_size = System::getProperty('customer_supplier_security_deposit_current_liability_font_size');

                $color = System::getProperty('customer_supplier_security_deposit_current_liability_color');

                $msg = System::getProperty('customer_supplier_security_deposit_current_liability_message');

                if ($contact_details->type == 'supplier' && System::getProperty('supplier_secrity_deposit_current_liability_checkbox') == 1) {

                    $message = '<p style="font-size: ' . $font_size . ';color: ' . $color . ' ">' . $msg . '</p>';

                }

                if ($contact_details->type == 'customer' && System::getProperty('customer_secrity_deposit_current_liability_checkbox') == 1) {

                    $message = '<p style="font-size: ' . $font_size . ';color: ' . $color . ' ">' . $msg . '</p>';

                }

                $disabled = 'disabled';

                $current_libility_account_id = 0;

            }

            $prefix_type = 'security_deposit';

            $ref_count = $this->transactionUtil->onlyGetReferenceCount($prefix_type, $business_id, false);

            //Generate reference number

            $payment_ref_no = $this->transactionUtil->generateReferenceNumber($prefix_type, $ref_count);

            $customer_deposit_account_id = $this->transactionUtil->account_exist_return_id('Cash');

            $accounts = Account::where('business_id', $business_id)->where('id', $customer_deposit_account_id)->where('is_closed', 0)->pluck('name', 'id');

            $security_deposit_already = Transaction::where('contact_id', $contact_id)->where('type', 'security_deposit')->first();

            return view('transaction_payment.customer_security_payment')

                ->with(compact(

                    'business_locations',

                    'business_location_id',

                    'security_deposit_already',

                    'contact_details',

                    'payment_types',

                    'accounts',

                    'payment_ref_no',

                    'contact_id',

                    'customer_deposit_account_id',

                    'current_libility_accounts',

                    'current_libility_account_id',

                    'disabled',

                    'message'

                ));

        }

    }

    /**

     * Adds Advance Payments for Contact

     *

     * @param  \Illuminate\Http\Request  $request

     * @return \Illuminate\Http\Response

     */

    

     public function postSecurityDeposit(Request $request)

    {

        if (!auth()->user()->can('purchase.create') && !auth()->user()->can('sell.create')) {

            abort(403, 'Unauthorized action.');

        }

        try {

            $business_id = request()->session()->get('user.business_id');

            $contact_id = $request->input('contact_id');

            $contact_id = Contact::where('contact_id', $contact_id)->where('business_id', $business_id)->first()->id;

            $inputs = $request->only([

                'amount', 'method', 'note', 'card_number', 'card_holder_name',

                'card_transaction_number', 'card_type', 'card_month', 'card_year', 'card_security',

                'cheque_number', 'bank_account_number'

            ]);

            $inputs['paid_on'] = !empty($inputs['paid_on']) ? Carbon::parse($inputs['paid_on'])->format('Y-m-d') : date('Y-m-d');

            $inputs['amount'] = $this->transactionUtil->num_uf($inputs['amount']);

            $inputs['created_by'] = auth()->user()->id;

            $inputs['payment_for'] = $contact_id;

            $inputs['business_id'] = $request->session()->get('business.id');

            if ($inputs['method'] == 'custom_pay_1') {

                $inputs['transaction_no'] = $request->input('transaction_no_1');

            } elseif ($inputs['method'] == 'custom_pay_2') {

                $inputs['transaction_no'] = $request->input('transaction_no_2');

            } elseif ($inputs['method'] == 'custom_pay_3') {

                $inputs['transaction_no'] = $request->input('transaction_no_3');

            }

            $payment_type = $request->type;

            $inputs['payment_ref_no'] = $request->payment_ref_no;

            $business_location = BusinessLocation::where('business_id', $business_id)

                ->first();

            $inputs['account_id'] = $request->account_id;

            //Upload documents if added

            $inputs['document'] = $this->transactionUtil->uploadFile($request, 'document', 'documents');

            DB::beginTransaction();

            $current_liability_account = $request->current_liability_account;

            //Add opening balance

            $transaction = $this->transactionUtil->createAdvancePaymentTransaction($business_id, $contact_id, $inputs['amount'], $inputs['account_id'], $payment_type, $inputs['paid_on'], $current_liability_account);

            $inputs['transaction_id'] = $transaction->id;

            $parent_payment = TransactionPayment::create($inputs);

            DB::commit();

            $output = [

                'success' => true,

                'msg' => __('lang_v1.security_deposit_added_success')

            ];

        } catch (\Exception $e) {

            DB::rollBack();

            Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

            $output = [

                'success' => false,

                'msg' => __('messages.something_went_wrong')

            ];

        }

        return redirect()->back()->with(['status' => $output]);

    }

    /**

     * Refund security deposit

     *

     * @param  \Illuminate\Http\Request  $request

     * @return \Illuminate\Http\Response

     */

    

     public function getRefundSecurityDeposit($contact_id, Request  $request)

    {

        try {

            $business_id = request()->session()->get('user.business_id');

            $refund_transaction_id = $request->refund_transaction_id;

            $refund_transaction = Transaction::find($refund_transaction_id);

            if (empty($refund_transaction)) {

                $output = [

                    'success' => 0,

                    'msg' => __('messages.something_went_wrong')

                ];

                return $output;

            }

            $amount = $request->amount;

            $account_id = $request->account_id;

            $current_liability_account = $request->current_liability_account;

            $business_location = BusinessLocation::where('business_id', $business_id)

                ->first();

            $inputs['paid_on'] = !empty($inputs['paid_on']) ? Carbon::parse($inputs['paid_on'])->format('Y-m-d') : date('Y-m-d');

            $inputs['amount'] = $this->transactionUtil->num_uf($amount);

            $payment_type = 'refund_security_deposit';

            $ob_data = [

                'business_id' => $business_id,

                'location_id' => $business_location->id,

                'type' => $payment_type,

                'status' => 'final',

                'payment_status' => 'paid',

                'contact_id' => $contact_id,

                'transaction_date' => $inputs['paid_on'],

                'total_before_tax' => $inputs['amount'],

                'final_total' => $inputs['amount'],

                'return_parent_id' => $refund_transaction->id,

                'created_by' => request()->session()->get('user.id')

            ];

            //Generate reference number

            $ref_count = $this->transactionUtil->onlyGetReferenceCount($payment_type, $business_id, false);

            //Generate reference number

            $payment_ref_no = $this->transactionUtil->generateReferenceNumber($payment_type, $ref_count);

            $ob_data['ref_no'] = $payment_ref_no;

            DB::beginTransaction();

            //Create opening balance transaction

            $transaction = Transaction::create($ob_data);

            $inputs['amount'] = $this->transactionUtil->num_uf($inputs['amount']);

            $inputs['created_by'] = auth()->user()->id;

            $inputs['payment_for'] = $contact_id;

            $inputs['business_id'] = $request->session()->get('business.id');

            $inputs['account_id'] = $account_id;

            $inputs['transaction_id'] = $transaction->id;

            $inputs['method'] = $transaction->id;

            $payment = TransactionPayment::create($inputs);

            //create account transaction

            $account_transaction_data = [

                'amount' => abs($transaction->final_total),

                'account_id' => $account_id,

                'contact_id' => $contact_id,

                'operation_date' => $inputs['paid_on'],

                'created_by' => $transaction->created_by,

                'transaction_id' => $transaction->id,

                'transaction_payment_id' => $payment->id,

                'note' => null

            ];

            $account_transaction_data['type'] = 'credit';

            AccountTransaction::createAccountTransaction($account_transaction_data);

            $account_transaction_data['account_id'] = $current_liability_account;

            $account_transaction_data['type'] = 'debit';

            AccountTransaction::createAccountTransaction($account_transaction_data);

            DB::commit();

            $output = [

                'success' => 1,

                'msg' => __('lang_v1.success')

            ];

        } catch (\Exception $e) {

            DB::rollBack();

            Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

            $output = [

                'success' => false,

                'msg' => __('messages.something_went_wrong')

            ];

        }

        return $output;

    }

    /**

     * Shows contact's payment due modal

     *

     * @param  int  $contact_id

     * @return \Illuminate\Http\Response

     */

    

     public function getPayContactDue($contact_id)

    {

        if (!auth()->user()->can('purchase.create')) {

            abort(403, 'Unauthorized action.');

        }

        if (request()->ajax()) {

            $business_id = request()->session()->get('user.business_id');

            $due_payment_type = request()->input('type');

            $query = Contact::where('contacts.id', $contact_id)

                ->join('transactions AS t', 'contacts.id', '=', 't.contact_id');

            if ($due_payment_type == 'purchase') {

                $query->select(

                    DB::raw("SUM(IF(t.type = 'purchase', final_total, 0)) as total_purchase"),

                    DB::raw("SUM(IF(t.type = 'purchase', (SELECT SUM(amount) FROM transaction_payments WHERE transaction_payments.transaction_id=t.id), 0)) as total_paid"),

                    'contacts.name',

                    'contacts.supplier_business_name',

                    'contacts.id as contact_id',

                    't.transaction_date'

                );

            } elseif ($due_payment_type == 'purchase_return') {

                $query->select(

                    DB::raw("SUM(IF(t.type = 'purchase_return', final_total, 0)) as total_purchase_return"),

                    DB::raw("SUM(IF(t.type = 'purchase_return', (SELECT SUM(amount) FROM transaction_payments WHERE transaction_payments.transaction_id=t.id), 0)) as total_return_paid"),

                    'contacts.name',

                    'contacts.supplier_business_name',

                    'contacts.id as contact_id'

                );

            } elseif ($due_payment_type == 'sell') {

                $query->select(

                    DB::raw("SUM(IF(t.type = 'sell' AND t.status = 'final', final_total, 0)) as total_invoice"),

                    DB::raw("SUM(IF(t.type = 'sell' AND t.status = 'final', (SELECT SUM(IF(is_return = 1,-1*amount,amount)) FROM transaction_payments WHERE transaction_payments.transaction_id=t.id), 0)) as total_paid"),

                    DB::raw("SUM(IF(t.type = 'cheque_return' AND t.status = 'final', final_total, 0)) as total_cheque_return"),

                    DB::raw("SUM(IF(t.type = 'cheque_return' AND t.status = 'final', (SELECT SUM(IF(is_return = 1,-1*amount,amount)) FROM transaction_payments WHERE transaction_payments.transaction_id=t.id  AND is_return=0), 0)) as total_paid_cheque_return"),

                    'contacts.name',

                    'contacts.supplier_business_name',

                    'contacts.id as contact_id'

                );

            } elseif ($due_payment_type == 'sell_return') {

                $query->select(

                    DB::raw("SUM(IF(t.type = 'sell_return', final_total, 0)) as total_sell_return"),

                    DB::raw("SUM(IF(t.type = 'sell_return', (SELECT SUM(amount) FROM transaction_payments WHERE transaction_payments.transaction_id=t.id), 0)) as total_return_paid"),

                    'contacts.name',

                    'contacts.supplier_business_name',

                    'contacts.id as contact_id'

                );

            }

            //Query for opening balance details

            $query->addSelect(

                DB::raw("SUM(IF(t.type = 'opening_balance', final_total, 0)) as opening_balance"),

                DB::raw("SUM(IF(t.type = 'opening_balance', (SELECT SUM(amount) FROM transaction_payments WHERE transaction_payments.transaction_id=t.id), 0)) as opening_balance_paid")

            );

            $contact_details = $query->first();

            $payment_line = new TransactionPayment();
 
            if ($due_payment_type == 'purchase') {

                $contact_details->total_purchase = empty($contact_details->total_purchase) ? 0 : $contact_details->total_purchase;

                $payment_line->amount = $contact_details->total_purchase -

                    $contact_details->total_paid;

                $prefix_type = 'purchase_payment';

            } elseif ($due_payment_type == 'purchase_return') {

                $payment_line->amount = $contact_details->total_purchase_return -

                    $contact_details->total_return_paid;

                $prefix_type = 'purchase_payment';

            } elseif ($due_payment_type == 'sell') {

                $contact_details->total_invoice = empty($contact_details->total_invoice) ? 0 : $contact_details->total_invoice;

                $contact_details->total_cheque_return = empty($contact_details->total_cheque_return) ? 0 : $contact_details->total_cheque_return;

                $cheque_return_amount = $contact_details->total_cheque_return - $contact_details->total_paid_cheque_return;

                $payment_line->amount = $contact_details->total_invoice - $contact_details->total_paid + $cheque_return_amount;

                $prefix_type = 'sell_payment';

            } elseif ($due_payment_type == 'sell_return') {

                $payment_line->amount = $contact_details->total_sell_return -

                    $contact_details->total_return_paid;

                $prefix_type = 'sell_payment';

            }

            //If opening balance due exists add to payment amount

            $contact_details->opening_balance = !empty($contact_details->opening_balance) ? $contact_details->opening_balance : 0;

            $contact_details->opening_balance_paid = !empty($contact_details->opening_balance_paid) ? $contact_details->opening_balance_paid : 0;

            $ob_due = $contact_details->opening_balance - $contact_details->opening_balance_paid;

            if ($ob_due > 0) {

                $payment_line->amount += $ob_due;

            }

            $business_locations = BusinessLocation::where('business_id', $business_id)->pluck('name', 'id');

            $business_location_id = BusinessLocation::where('business_id', $business_id)->first()->id;

            $amount_formated = $this->transactionUtil->num_f($payment_line->amount);

            $contact_details->total_paid = empty($contact_details->total_paid) ? 0 : $contact_details->total_paid;

            $payment_line->method = 'cash';

            $payment_line->paid_on = \Carbon::now()->toDateTimeString();

            $payment_types = $this->transactionUtil->payment_types($business_location_id);

            unset($payment_types['credit_sale']);  // removing credit sale method from array

            //Accounts

            $accounts = $this->moduleUtil->accountsDropdown($business_id, true);

            $ref_count = $this->transactionUtil->onlyGetReferenceCount($prefix_type, $business_id, false);

            //Generate reference number

            $payment_ref_no = $this->transactionUtil->generateReferenceNumber($prefix_type, $ref_count);

            $payment_line->amount = $this->get_due_bal($contact_id);
            // print_r($payment_line); die;
            // if ($payment_line->amount > 0) {

                return view('transaction_payment.pay_supplier_due_modal')

                    ->with(compact('business_locations', 'business_location_id', 'contact_details', 'payment_types', 'payment_line', 'due_payment_type', 'ob_due', 'amount_formated', 'accounts', 'payment_ref_no'));

            // }

        }

    }

    

    public function get_due_bal($contact_id){

        $start_date = date('Y-m-d');

        $end_date   = date('Y-m-d');

        $business_id = request()->session()->get('user.business_id');

        $asset_account_id = Account::leftjoin('account_types', 'accounts.account_type_id', 'accounts.id')

            ->where('account_types.name', 'like', '%Assets%')

            ->where('accounts.business_id', $business_id)

            ->pluck('accounts.id')->toArray();

        $contact = Contact::find($contact_id);

        $business_details = $this->businessUtil->getDetails($contact->business_id);

        $location_details = BusinessLocation::where('business_id', $contact->business_id)->first();

        $opening_balance = Transaction::where('contact_id', $contact_id)->where('type', 'opening_balance')->where('payment_status', 'due')->sum('final_total');

        if ($contact->type == 'customer') {

            $opening_amount = ''; // ONLY SHOW OPENING BALANCE WHEN NO SALES AND PAYMENT

            $opening_balance_new = DB::select("select `cl`.`amount` as opening_balance

                    from `contact_ledgers` cl left join `transactions` t on `cl`.`transaction_id` = `t`.`id`

                    left join `business_locations` bl on `t`.`location_id` = `bl`.`id`

                    where `cl`.`contact_id` = " . $contact_id . "

                    and `cl`.`type` = 'debit'

                    and `t`.`business_id` = " . $business_id . "

                    and `t`.`type` = 'opening_balance'

                    and date(`cl`.`operation_date`) >= '" . $start_date . "'

                    and date(`cl`.`operation_date`) <= '" . $end_date . "'

                    order by `cl`.`operation_date` limit 2");

            if (count($opening_balance_new) <= 1) {

                $opening_amount =  DB::select(" select (select(0 - IFNULL(amount,0))) as opening_balance 

                    from `contact_ledgers` where contact_id = '$contact_id' order by created_at ASC limit 1");

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

            } else {

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

            }

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

            $ledger_details['opening'] = $opening_amount;

            //$GLOBALS['n'] = $array($ledger_details['balance_due'], $contact_id);

           

        }
         $query = ContactLedger::leftjoin('transactions', 'contact_ledgers.transaction_id', 'transactions.id')

                ->leftjoin('business_locations', 'transactions.location_id', 'business_locations.id')

                ->leftjoin('transaction_payments', 'contact_ledgers.transaction_payment_id', 'transaction_payments.id')

                ->where('contact_ledgers.contact_id', $contact_id)

                ->where('transactions.business_id', $business_id)

                ->select(

                    'contact_ledgers.*',

                    'contact_ledgers.type as acc_transaction_type',

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

                    'transaction_payments.transaction_id as tp_transaction_id',

                    'transaction_payments.paid_on',

                    'transaction_payments.bank_name',

                    'transaction_payments.cheque_date',

                    'transaction_payments.cheque_number',

                    )->groupBy('contact_ledgers.id')->orderBy('contact_ledgers.id', 'asc');

        if (!empty($start_date)  && !empty($end_date)) {
           
            $query->whereDate('contact_ledgers.operation_date', '>=', $start_date);

            $query->whereDate('contact_ledgers.operation_date', '<=', $end_date);

        }

        $query->orderby('contact_ledgers.operation_date');

        // $query->skip(0)->take(5);

        // $ledger_transactions = $query->get();

        $ledger_transactions = $query->get();

        // if ($contact->type == 'customer') {

        //     $total_paid = 0;

        //     foreach($ledger_transactions->toArray() as $val) {

        //        if($val['acc_transaction_type'] == 'credit') {

        //             if(!empty($val['transaction_payment_id'])){

        //                 $transaction_payment = TransactionPayment::where('id', $val['transaction_payment_id'])->withTrashed()->first();

        //             }

        //             $amount = 0;

        //             if(!empty($transaction_payment)){

        //                 if(empty($transaction_payment->transaction_id)){ // if empty then it will be parent payment

        //                     $amount = $transaction_payment->amount;  // show parent transaction payment amount

        //                 }else{

        //                     $amount = $val['amount']; // get the amount from contact ledger if not a payment

        //                 }

        //             }else{

        //                 $amount = $val['amount'];

        //             }

        //             $total_paid = $total_paid + $amount;

        //        }

        //     }

        //     $dateTimestamp1 = date('Y-m-d',strtotime($contact->created_at));

        //     // $dateTimestamp2 = strtotime($date2);

        //     $ledger_details['total_paid'] = $total_paid;

        //     $ledger_details['bf_balance'] = $ledger_details['beginning_balance'] = count($opening_balance_new) > 0 ? $opening_balance_new[0]->opening_balance : 0;

        //     if(!empty($start_date) && $dateTimestamp1 >= $start_date){

        //         $ledger_details['bf_balance'] = 0;

        //     }

        //     // $ledger_details['beginning_balance'] = count($bg_bl) > 0 ? $bg_bl[0]->opening_balance : 0;

        //     $ledger_details['balance_due'] = $ledger_details['beginning_balance'] + $ledger_details['total_invoice'] - $ledger_details['total_paid'];

        //     // dd($ledger_details);

        //     return  $ledger_details['balance_due'];

        // }

        if ($contact->type == 'customer') {

            $total_paid = $skipped_cr = 0;

            $dateTimestamp1 = date('Y-m-d',strtotime($contact->created_at));

            foreach($ledger_transactions->toArray() as $val) {

                if($val['acc_transaction_type'] == 'credit') {

                    if(!empty($val['transaction_payment_id'])){

                        $transaction_payment = TransactionPayment::where('id', $val['transaction_payment_id'])->withTrashed()->first();

                    }

                    $amount = 0;

                    if(!empty($transaction_payment)){

                        if(empty($transaction_payment->transaction_id)){ // if empty then it will be parent payment

                            $amount = $transaction_payment->amount;  // show parent transaction payment amount

                        }else{

                            $amount = $val['amount']; // get the amount from contact ledger if not a payment

                        }

                    }else{

                        $amount = $val['amount'];

                    }

                    if($val['transaction_type'] == 'opening_balance' ){

                        $dateTimestamp1 = date('Y-m-d',strtotime($val['transaction_date'])) ;

                        $skipped_cr += $amount;

                        continue;

                    }

                    $total_paid = $total_paid + $amount;

                }

            }

            // dd($dateTimestamp1);

            // $dateTimestamp2 = strtotime($date2);

            $ledger_details['total_paid'] = $total_paid;

            $ledger_details['bf_balance'] = $ledger_details['beginning_balance'] = count($opening_balance_new) > 0 ? $opening_balance_new[0]->opening_balance : 0;

            // $ledger_details['beginning_balance'] = count($bg_bl) > 0 ? $bg_bl[0]->opening_balance : 0;

            $ledger_details['balance'] = $ledger_details['balance_due'] = $ledger_details['beginning_balance'] + $ledger_details['total_invoice'] - ($ledger_details['total_paid']);

            // dd($ledger_details);

            //    dd($ledger_details);

            if(!empty($start_date) && $dateTimestamp1 >= $start_date){

                $ledger_details['bf_balance'] = 0;

                $ledger_details['balance'] = 0;

            }

            if(!empty($start_date) && $dateTimestamp1 > $start_date){

                echo "Inside Con<br>";

                $ledger_details['beginning_balance'] =0;

                $ledger_details['balance_due'] -=  $skipped_cr ;

            }else{

                // $ledger_details['balance'] -=  $skipped_cr ;

            }

            return  $ledger_details['balance_due'];

        }

        return 0;

    }

    /**

     * Adds Payments for Contact due

     *

     * @param  \Illuminate\Http\Request  $request

     * @return \Illuminate\Http\Response

     */

    

     public function postPayContactDue(Request  $request)

    {

        if (!auth()->user()->can('purchase.create') && !auth()->user()->can('sell.create')) {

            abort(403, 'Unauthorized action.');

        }

        $business_id = $request->session()->get('business.id');

        try {

            $contact_id = $request->input('contact_id');

            $inputs = $request->only([

                'amount', 'method', 'note', 'card_number', 'card_holder_name',

                'card_transaction_number', 'card_type', 'card_month', 'card_year', 'card_security',

                'cheque_number', 'bank_account_number'

            ]);

            $inputs['paid_on'] = $this->transactionUtil->uf_date($request->input('paid_on'), true);

            $inputs['amount'] = $this->transactionUtil->num_uf($inputs['amount']);

            $inputs['created_by'] = auth()->user()->id;

            $inputs['payment_for'] = $contact_id;

            $inputs['business_id'] = $request->session()->get('business.id');

            $inputs['cheque_date'] = !empty($request->cheque_date) ? Carbon::parse($request->cheque_date)->format('Y-m-d') : null;

            if ($inputs['method'] == 'custom_pay_1') {

                $inputs['transaction_no'] = $request->input('transaction_no_1');

            } elseif ($inputs['method'] == 'custom_pay_2') {

                $inputs['transaction_no'] = $request->input('transaction_no_2');

            } elseif ($inputs['method'] == 'custom_pay_3') {

                $inputs['transaction_no'] = $request->input('transaction_no_3');

            }

            $due_payment_type = $request->input('due_payment_type');

            $prefix_type = 'purchase_payment';

            if (in_array($due_payment_type, ['sell', 'sell_return'])) {

                $prefix_type = 'sell_payment';

            }

            $ref_count = $this->transactionUtil->setAndGetReferenceCount($prefix_type);

            //Generate reference number

            $payment_ref_no = $this->transactionUtil->generateReferenceNumber($prefix_type, $ref_count);

            $inputs['payment_ref_no'] = $payment_ref_no;

            if (!empty($request->input('account_id'))) {

                $inputs['account_id'] = $request->input('account_id');

            }

            //Upload documents if added

            $inputs['document'] = $this->transactionUtil->uploadFile($request, 'document', 'documents');

            $inputs['paid_in_type'] = 'customer_page';

            DB::beginTransaction();

            $parent_payment = TransactionPayment::create($inputs);

            $inputs['transaction_type'] = $due_payment_type;

            $account_payable = Account::where('business_id', $business_id)->where('name', 'Accounts Payable')->where('is_closed', 0)->first();

            $account_payable_id = !empty($account_payable) ? $account_payable->id : 0;

            // $amount_consumed = $this->transactionUtil->getTotalAmountConsumable($parent_payment, $due_payment_type);

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

            $location_id = BusinessLocation::where('business_id', $business_id)->first();

            $account_transaction_data['account_id'] = $request->account_id;

            $contact = Contact::findOrFail($contact_id);

            if ($contact->type ==  'supplier') {

                if ($due_payment_type == 'purchase_return') {

                    $purchase_return_due = Transaction::where('contact_id', $contact_id)->whereIn('type', ['purchase_return'])->whereIn('payment_status', ['due', 'partial'])->first();

                    $account_transaction_data['account_id'] = $request->account_id;

                    $account_transaction_data['transaction_id'] = !empty($purchase_return_due) ? $purchase_return_due->id : null;

                    $account_transaction_data['type'] = 'debit';

                    $account_transaction_data['sub_type'] = 'payment';

                    AccountTransaction::createAccountTransaction($account_transaction_data);

                    $account_transaction_data['account_id'] =  $account_payable_id;

                    $account_transaction_data['type'] = 'credit';

                    AccountTransaction::createAccountTransaction($account_transaction_data);

                    $account_transaction_data['type'] = 'credit';

                    ContactLedger::createContactLedger($account_transaction_data);

                } else {

                    $due_transaction_id = Transaction::where('contact_id', $contact_id)->whereIn('type', ['purchase', 'opening_balance'])->whereIn('payment_status', ['due', 'partial'])->first();

                    if ($inputs['method'] == 'bank_transfer' || $inputs['method'] == 'direct_bank_deposit') {

                        $account_transaction_data['account_id'] = $inputs['account_id'];

                    }

                    $account_transaction_data['transaction_id'] = !empty($due_transaction_id) ? $due_transaction_id->id : null;

                    $account_transaction_data['type'] = 'credit';

                    AccountTransaction::createAccountTransaction($account_transaction_data);

                    $account_transaction_data['account_id'] = $account_payable_id;

                    $account_transaction_data['type'] = 'debit';

                    AccountTransaction::createAccountTransaction($account_transaction_data);

                    $account_transaction_data['sub_type'] = 'payment';

                    ContactLedger::createContactLedger($account_transaction_data);

                }

            }

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

                    $due_transaction_id = Transaction::where('contact_id', $contact_id)->whereIn('type', ['sell', 'opening_balance', 'cheque_return'])->whereIn('payment_status', ['due', 'partial'])->first();

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

            //Distribute above payment among unpaid transactions

            $this->transactionUtil->payAtOnce($parent_payment, $due_payment_type);

            DB::commit();

            $output = [

                'success' => true,

                'msg' => __('purchase.payment_added_success')

            ];

        } catch (\Exception $e) {

            DB::rollBack();

            Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

            $output = [

                'success' => false,

                'msg' => __('messages.something_went_wrong')

            ];

        }

        return redirect()->back()->with(['status' => $output]);

    }

    /**

     * view details of single..,

     * payment.

     * @param  \Illuminate\Http\Request  $request

     * @return \Illuminate\Http\Response

     */

    

     public function viewPayment($payment_id)

    {

        if (!auth()->user()->can('purchase.payments') && !auth()->user()->can('sell.payments')) {

            abort(403, 'Unauthorized action.');

        }

        if (request()->ajax()) {

            $business_id = request()->session()->get('business.id');

            $single_payment_line = TransactionPayment::findOrFail($payment_id);

            $transaction = null;

            if (!empty($single_payment_line->transaction_id)) {

                $transaction = Transaction::where('id', $single_payment_line->transaction_id)

                    ->with(['contact', 'location', 'transaction_for'])

                    ->first();

            } else {

                $child_payment = TransactionPayment::where('business_id', $business_id)

                    ->where('parent_id', $payment_id)

                    ->with(['transaction', 'transaction.contact', 'transaction.location', 'transaction.transaction_for'])

                    ->first();

                $transaction = $child_payment->transaction;

            }

            $payment_types = $this->transactionUtil->payment_types();

            return view('transaction_payment.single_payment_view')

                ->with(compact('single_payment_line', 'transaction', 'payment_types'));

        }

    }

    /**

     * Retrieves all the child payments of a parent payments

     * payment.

     * @param  \Illuminate\Http\Request  $request

     * @return \Illuminate\Http\Response

     */

    

     public function showChildPayments($payment_id)

    {

        if (!auth()->user()->can('purchase.payments') && !auth()->user()->can('sell.payments')) {

            abort(403, 'Unauthorized action.');

        }

        if (request()->ajax()) {

            $business_id = request()->session()->get('business.id');

            $child_payments = TransactionPayment::where('business_id', $business_id)

                ->where('parent_id', $payment_id)

                ->with(['transaction', 'transaction.contact'])

                ->get();

            $payment_types = $this->transactionUtil->payment_types();

            return view('transaction_payment.show_child_payments')

                ->with(compact('child_payments', 'payment_types'));

        }

    }

    /**

     * Retrieves list of all opening balance payments.

     *

     * @param  int  $contact_id

     * @return \Illuminate\Http\Response

     */

    

     public function getOpeningBalancePayments($contact_id)

    {

        if (!auth()->user()->can('purchase.payments') && !auth()->user()->can('sell.payments')) {

            abort(403, 'Unauthorized action.');

        }

        $business_id = request()->session()->get('business.id');

        if (request()->ajax()) {

            $query = TransactionPayment::leftjoin('transactions as t', 'transaction_payments.transaction_id', '=', 't.id')

                ->where('t.business_id', $business_id)

                ->where('t.type', 'opening_balance')

                ->where('t.contact_id', $contact_id)

                ->where('transaction_payments.business_id', $business_id)

                ->select(

                    'transaction_payments.amount',

                    'method',

                    'paid_on',

                    'transaction_payments.payment_ref_no',

                    'transaction_payments.document',

                    'transaction_payments.id',

                    'cheque_number',

                    'card_transaction_number',

                    'bank_account_number'

                )

                ->groupBy('transaction_payments.id');

            $permitted_locations = auth()->user()->permitted_locations();

            if ($permitted_locations != 'all') {

                $query->whereIn('t.location_id', $permitted_locations);

            }

            return Datatables::of($query)

                ->editColumn('paid_on', '{{@format_datetime($paid_on)}}')

                ->editColumn('method', function ($row) {

                    $method = __('lang_v1.' . $row->method);

                    if ($row->method == 'cheque') {

                        $method .= '<br>(' . __('lang_v1.cheque_no') . ': ' . $row->cheque_number . ')';

                    } elseif ($row->method == 'card') {

                        $method .= '<br>(' . __('lang_v1.card_transaction_no') . ': ' . $row->card_transaction_number . ')';

                    } elseif ($row->method == 'bank_transfer') {

                        $method .= '<br>(' . __('lang_v1.bank_account_no') . ': ' . $row->bank_account_number . ')';

                    } elseif ($row->method == 'custom_pay_1') {

                        $method = __('lang_v1.custom_payment_1') . '<br>(' . __('lang_v1.transaction_no') . ': ' . $row->transaction_no . ')';

                    } elseif ($row->method == 'custom_pay_2') {

                        $method = __('lang_v1.custom_payment_2') . '<br>(' . __('lang_v1.transaction_no') . ': ' . $row->transaction_no . ')';

                    } elseif ($row->method == 'custom_pay_3') {

                        $method = __('lang_v1.custom_payment_3') . '<br>(' . __('lang_v1.transaction_no') . ': ' . $row->transaction_no . ')';

                    }

                    return $method;

                })

                ->editColumn('amount', function ($row) {

                    return '<span class="display_currency paid-amount" data-orig-value="' . $row->amount . '" data-currency_symbol = true>' . $row->amount . '</span>';

                })

                ->addColumn('action', '<button type="button" class="btn btn-primary btn-xs view_payment" data-href="{{ action("TransactionPaymentController@viewPayment", [$id]) }}"><i class="fa fa-external-link"></i> @lang("messages.view")

                    </button> <button type="button" class="btn btn-info btn-xs edit_payment" 

                    data-href="{{action("TransactionPaymentController@edit", [$id]) }}"><i class="glyphicon glyphicon-edit"></i> @lang("messages.edit")</button>

                    &nbsp; <button type="button" class="btn btn-danger btn-xs delete_payment" 

                    data-href="{{ action("TransactionPaymentController@destroy", [$id]) }}"

                    ><i class="fa fa-trash" aria-hidden="true"></i> @lang("messages.delete")</button> @if(!empty($document))<a href="{{asset("/uploads/documents/" . $document)}}" class="btn btn-success btn-xs" download=""><i class="fa fa-download"></i> @lang("purchase.download_document")</a>@endif')

                ->rawColumns(['amount', 'method', 'action'])

                ->make(true);

        }

    }

    

    public function getAccountDropDown(Request $request)

    {

        $method_name = $request->method_name;

        $business_id = $request->session()->get('business.id');

        $html = '<option value="">None</option>';

        $accounts = Account::where('business_id', $business_id)->notClosed()->get();

        if ($method_name == 'direct_bank_deposit') {

            $accounts = Account::where('business_id', $business_id)->where('asset_type', '4')->notClosed()->get();

        }

        foreach ($accounts as $account) {

            $html .= '<option value="' . $account->id . '">' . $account->name . '</option>';

        }

        return $html;

    }

    

    public function getPaymentMethodByLocationDropDown($location_id)

    {

        $payment_methods = $this->transactionUtil->payment_types($location_id, true, true);

        unset($payment_methods['credit_sale']);

        $html = '';

        foreach ($payment_methods as $key => $value) {

            $html .= '<option value="' . $key . '">' . $value . '</option>';

        }

        return $html;

    }

    

    public function getPaymentDetailsById($payment_id)

    {

        $payment = TransactionPayment::find($payment_id);

        $business_id = request()->session()->get('business.id');

        if ($payment->method == 'cheque') {

            $amount = TransactionPayment::where('business_id', $business_id)->whereNotNull('transaction_id')->where('cheque_number', $payment->cheque_number)->sum('amount');

        }

        $payment->amount = $amount;

        return $payment;

    }

    

    public function getChequeDropdownByBankId($bank_id, $contact_id)

    {

        $business_id = request()->session()->get('business.id');

        $cheque_banks = AccountTransaction::leftjoin('accounts', 'account_transactions.account_id', 'accounts.id')

            ->leftjoin('transaction_payments', function ($join) use ($business_id) {

                $join->on('account_transactions.transaction_payment_id', 'transaction_payments.id');

            })

            ->leftjoin('transactions', 'transaction_payments.transaction_id', 'transactions.id')

            ->where('account_transactions.sub_type', 'deposit')

            ->where('account_transactions.type', 'debit')

            ->whereNotNull('transfer_transaction_id')

            ->where('transactions.contact_id', $contact_id)

            ->where('accounts.id', $bank_id)

            ->groupBy('account_transactions.id')

            ->select('transaction_payments.cheque_number', 'transaction_payments.parent_id', 'transaction_payments.id')->get();

        $array = [];

        foreach ($cheque_banks as $cheque_bank) {

            if (!empty($cheque_bank->parent_id)) {

                $array[$cheque_bank->parent_id] = $cheque_bank->cheque_number;

            } else {

                $array[$cheque_bank->id] = $cheque_bank->cheque_number;

            }

        }

        $cheque_banks2 = AccountTransaction::leftjoin('accounts', 'account_transactions.account_id', 'accounts.id')

            ->leftjoin('transaction_payments', function ($join) use ($business_id) {

                $join->on('account_transactions.transaction_payment_id', 'transaction_payments.parent_id');

            })

            ->leftjoin('transactions', 'transaction_payments.transaction_id', 'transactions.id')

            ->where('account_transactions.sub_type', 'deposit')

            ->where('account_transactions.type', 'debit')

            ->whereNotNull('transfer_transaction_id')

            ->where('transactions.contact_id', $contact_id)

            ->where('accounts.id', $bank_id)

            ->groupBy('account_transactions.id')

            ->select('transaction_payments.cheque_number', 'transaction_payments.parent_id', 'transaction_payments.id')->get();

        foreach ($cheque_banks2 as $cheque_bank2) {

            if (!empty($cheque_bank2->parent_id)) {

                $array[$cheque_bank2->parent_id] = $cheque_bank2->cheque_number;

            } else {

                $array[$cheque_bank2->id] = $cheque_bank2->cheque_number;

            }

        }

        $html = $this->transactionUtil->createDropdownHtml($array, 'Please Select');

        return $html;

    }

}

