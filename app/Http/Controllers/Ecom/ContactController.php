<?php

namespace App\Http\Controllers\Ecom;

use App\Http\Controllers\Controller;
use App\Account;
use App\AccountTransaction;
use App\Business;
use App\BusinessLocation;
use App\Contact;
use App\ContactLedger;
use App\Customer;
use App\PurchaseLine;
use App\ContactGroup;
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
use Excel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
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
        $type = 'customer';

        $types = ['supplier', 'customer'];

        if (empty($type) || !in_array($type, $types)) {
            return redirect()->back();
        }

        if (request()->ajax()) {
            return $this->indexCustomer();
        }

        $reward_enabled = (request()->session()->get('business.enable_rp') == 1 && in_array($type, ['customer'])) ? true : false;

        return view('ecom_customer/contact.index')
            ->with(compact('type', 'reward_enabled'));
    }

    /**
     * Returns the database object for customer
     *
     * @return \Illuminate\Http\Response
     */
    private function indexCustomer()
    {
        $query = Contact::leftjoin('transactions AS t', 'contacts.id', '=', 't.contact_id')
            ->leftjoin('contact_groups AS cg', 'contacts.customer_group_id', '=', 'cg.id')
            ->where('contacts.contact_id', auth()->user()->username)
            ->where('contacts.is_property', 0)
            ->onlyCustomers()
            ->select([
                'contacts.contact_id', 'contacts.name', 'contacts.created_at', 'contacts.active', 'total_rp', 'cg.name as customer_group', 'city', 'state', 'country', 'landmark', 'mobile', 'contacts.id', 'is_default',
                DB::raw("SUM(IF(t.type = 'sell' AND t.status = 'final', final_total, 0)) as total_invoice"),
                DB::raw("SUM(IF(t.type = 'sell' AND t.status = 'final', (SELECT SUM(IF(is_return = 1,-1*amount,amount)) FROM transaction_payments WHERE transaction_payments.transaction_id=t.id), 0)) as invoice_received"),
                DB::raw("SUM(IF(t.type = 'sell_return', final_total, 0)) as total_sell_return"),
                DB::raw("SUM(IF(t.type = 'sell_return', (SELECT SUM(amount) FROM transaction_payments WHERE transaction_payments.transaction_id=t.id), 0)) as sell_return_paid"),
                DB::raw("SUM(IF(t.type = 'opening_balance', final_total, 0)) as opening_balance"),
                DB::raw("SUM(IF(t.type = 'advance_payment', -1*final_total, 0)) as advance_payment"),
                DB::raw("SUM(IF(t.type = 'opening_balance', (SELECT SUM(IF(is_return = 1,-1*amount,amount)) FROM transaction_payments WHERE transaction_payments.transaction_id=t.id), 0)) as opening_balance_paid"),
                'email', 'tax_number', 'contacts.pay_term_number', 'contacts.pay_term_type', 'contacts.credit_limit', 'contacts.custom_field1', 'contacts.custom_field2', 'contacts.custom_field3', 'contacts.custom_field4', 'contacts.type'
            ])
            ->groupBy('contacts.contact_id');

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
                    <li>
                        <a href="{{action(\'Ecom\ContactController@show\', [$contact_id])."?view=contact_info"}}">
                            <i class="fa fa-user" aria-hidden="true"></i>
                            @lang("contact.contact_info", ["contact" => __("contact.contact") ])
                        </a>
                    </li>
                    <li>
                        <a href="{{action(\'Ecom\ContactController@show\', [$contact_id])."?view=ledger"}}">
                            <i class="fa fa-anchor" aria-hidden="true"></i>
                            @lang("lang_v1.ledger")
                        </a>
                    </li>
                   
                    <li>
                        <a href="{{action(\'ContactController@show\', [$contact_id])."?view=references"}}">
                            <i class="fa fa-link" aria-hidden="true"></i>
                             @lang("lang_v1.references")
                        </a>
                    </li>
            
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
        return $contacts->rawColumns(['action', 'opening_balance', 'credit_limit', 'pay_term', 'due', 'return_due', 'mass_delete'])
            ->make(true);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($contact_id)
    {
        $contact_details = Contact::where('contact_id', $contact_id)->first();
        $all_contact_ids = Contact::where('contact_id', $contact_id)->pluck('contacts.id')->toArray();
        $contact = Contact::whereIn('contacts.contact_id', $all_contact_ids)
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

        $references = CustomerReference::where('contact_id', $contact_details->id)->pluck('reference', 'reference');
        $reward_enabled = (request()->session()->get('business.enable_rp') == 1 && in_array($contact->type, ['customer', 'both'])) ? true : false;

        $businesses = Transaction::join('business', 'transactions.business_id', 'business.id')
            ->join('contacts', 'transactions.contact_id', 'contacts.id')
            ->where('show_for_customers', 1)->where('contacts.contact_id', $contact_id)->groupBy('transactions.contact_id')
            ->select('business.name', 'business.id')->pluck('business.name', 'business.id');

        //get contact view type : ledger, notes etc.
        $view_type = request()->get('view');
        if (is_null($view_type)) {
            $view_type = 'contact_info';
        }

        return view('ecom_customer.contact.show')
            ->with(compact('contact', 'reward_enabled', 'view_type', 'references', 'contact_id', 'businesses'));
    }

    /**
     * Shows ledger for contacts
     *
     * @param  \Illuminate\Http\Request
     * @return \Illuminate\Http\Response
     */
    public function getLedger()
    {
        $business_id = request()->business_id;
       
        $asset_account_id = Account::leftjoin('account_types', 'accounts.account_type_id', 'accounts.id')
            ->where('account_types.name', 'like', '%Assets%')
            ->where('accounts.business_id', $business_id)
            ->pluck('accounts.id')->toArray();
        $contact_id = request()->input('contact_id');

        $start_date = request()->start_date;
        $end_date =  request()->end_date;

        $contact = Contact::where('business_id', $business_id)->where('contact_id', $contact_id)->first();
        $business_details = $this->businessUtil->getDetails($contact->business_id);
        $location_details = BusinessLocation::where('business_id', $contact->business_id)->first();
        $opening_balance = Transaction::where('contact_id', $contact->id)->where('type', 'opening_balance')->where('payment_status', 'due')->sum('final_total');

        $ledger_details = $this->__getLedgerDetails($contact->id, $start_date, $end_date);

        $query = ContactLedger::leftjoin('transactions', 'contact_ledgers.transaction_id', 'transactions.id')
            ->leftjoin('business_locations', 'transactions.location_id', 'business_locations.id')
            ->leftjoin('transaction_payments', 'contact_ledgers.transaction_id', 'transaction_payments.transaction_id')
            ->where('contact_ledgers.contact_id', $contact->id)
            ->where('transactions.final_total', '>', 0)
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
                'transaction_payments.bank_name',
                'transaction_payments.cheque_date',
                'transaction_payments.cheque_number'
            )->groupBy('contact_ledgers.id');


        if (!empty($start_date)  && !empty($end_date)) {
            $query->whereDate('transactions.transaction_date', '>=', $start_date);
            $query->whereDate('transactions.transaction_date', '<=', $end_date);
        }
        $ledger_transactions = $query->get();

        if (request()->input('action') == 'pdf') {
            $for_pdf = true;
            $html = view('contact.ledger')
                ->with(compact('ledger_details', 'contact', 'for_pdf', 'ledger_transactions', 'business_details', 'location_details'))->render();
            $mpdf = $this->getMpdf();
            $mpdf->WriteHTML($html);
            $mpdf->Output();
        }
        if (request()->input('action') == 'print') {
            $for_pdf = true;
            return view('contact.ledger')
                ->with(compact('ledger_details', 'contact', 'for_pdf', 'ledger_transactions', 'business_details', 'location_details'))->render();
        }


        return view('ecom_customer.contact.ledger')
            ->with(compact('ledger_details', 'contact', 'opening_balance', 'ledger_transactions'));
    }

    public function postCustomersApi(Request $request)
    {
        try {
            $api_token = $request->header('API-TOKEN');

            $api_settings = $this->moduleUtil->getApiSettings($api_token);

            $business = Business::find($api_settings->business_id);

            $data = $request->only(['name', 'email']);

            $customer = Contact::where('business_id', $api_settings->business_id)
                ->where('email', $data['email'])
                ->whereIn('type', ['customer', 'both'])
                ->first();

            if (empty($customer)) {
                $data['type'] = 'customer';
                $data['business_id'] = $api_settings->business_id;
                $data['created_by'] = $business->owner_id;
                $data['mobile'] = 0;

                $ref_count = $this->commonUtil->setAndGetReferenceCount('contacts', $business->id);

                $data['contact_id'] = $this->commonUtil->generateReferenceNumber('contacts', $ref_count, $business->id);

                $customer = Contact::create($data);
            }
        } catch (\Exception $e) {
            Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

            return $this->respondWentWrong($e);
        }

        return $this->respond($customer);
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

        //Add Beginning balance to ledger
        // $ledger = array_merge([[
        //     'date' => $start,
        //     'ref_no' => '',
        //     'type' => __('lang_v1.beginning_balance'),
        //     'location' => '',
        //     'payment_status' => '',
        //     'total' => $beginning_balance,
        //     'payment_method' => '',
        //     'debit' => '',
        //     'credit' => '',
        //     'others' => ''
        // ]], $ledger);

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

    /**
     * Function to send ledger notification
     *
     */
    public function sendLedger(Request $request)
    {
        $notAllowed = $this->notificationUtil->notAllowedInDemo();
        if (!empty($notAllowed)) {
            return $notAllowed;
        }

        try {
            $data = $request->only(['to_email', 'subject', 'email_body', 'cc', 'bcc']);
            $emails_array = array_map('trim', explode(',', $data['to_email']));

            $contact_id = $request->input('contact_id');
            $business_id = request()->session()->get('business.id');

            $start_date = request()->input('start_date');
            $end_date =  request()->input('end_date');

            $contact = Contact::find($contact_id);

            $asset_account_id = Account::leftjoin('account_types', 'accounts.account_type_id', 'accounts.id')
                ->where('account_types.name', 'like', '%Assets%')
                ->where('accounts.business_id', $business_id)
                ->pluck('accounts.id')->toArray();

            $ledger_details = $this->__getLedgerDetails($contact_id, $start_date, $end_date);

            $business_details = $this->businessUtil->getDetails($contact->business_id);
            $location_details = BusinessLocation::where('business_id', $contact->business_id)->first();
            $opening_balance = Transaction::where('contact_id', $contact_id)->where('type', 'opening_balance')->where('payment_status', 'due')->sum('final_total');

            if ($contact->type == 'supplier') {
                $query = AccountTransaction::leftjoin('transactions', 'account_transactions.transaction_id', 'transactions.id')
                    ->leftjoin('transaction_payments', 'transactions.id', 'transaction_payments.transaction_id')
                    ->leftjoin('business_locations', 'transactions.location_id', 'business_locations.id')
                    ->where('transactions.type', 'purchase')
                    ->orWhere('transactions.type', 'opening_balance')
                    ->where('contact_id', $contact_id)
                    ->whereNotIn('account_transactions.account_id', $asset_account_id)
                    ->select(
                        'account_transactions.*',
                        'account_transactions.type as acc_transaction_type',
                        'business_locations.name as location_name',
                        'transactions.ref_no',
                        'transactions.transaction_date',
                        'transactions.payment_status',
                        'transaction_payments.method as payment_method',
                        'transactions.type as transaction_type',
                        DB::raw('(SELECT SUM(IF(AT.type="credit", -1 * AT.amount, AT.amount)) from account_transactions as AT WHERE AT.operation_date <= account_transactions.operation_date AND AT.account_id  =account_transactions.account_id AND AT.deleted_at IS NULL AND AT.id <= account_transactions.id) as balance')
                    );
            }

            if ($contact->type == 'customer') {
                $query = AccountTransaction::leftjoin('transactions', 'account_transactions.transaction_id', 'transactions.id')
                    ->leftjoin('transaction_payments', 'transactions.id', 'transaction_payments.transaction_id')
                    ->leftjoin('business_locations', 'transactions.location_id', 'business_locations.id')
                    ->leftjoin('accounts', 'account_transactions.account_id', 'accounts.id')
                    ->where('transactions.type', 'sell')
                    ->orWhere('transactions.type', 'opening_balance')
                    // ->orWhere('transactions.type', 'sell_return')
                    ->where('contact_id', $contact_id)
                    // ->whereNull('accounts.asset_type')
                    ->select(
                        'account_transactions.*',
                        'account_transactions.type as acc_transaction_type',
                        'business_locations.name as location_name',
                        'transactions.ref_no',
                        'transactions.transaction_date',
                        'transactions.payment_status',
                        'transaction_payments.method as payment_method',
                        'transactions.type as transaction_type',
                        DB::raw('(SELECT SUM(IF(AT.type="credit", -1 * AT.amount, AT.amount)) from account_transactions as AT WHERE AT.operation_date <= account_transactions.operation_date AND AT.account_id  =account_transactions.account_id AND AT.deleted_at IS NULL AND AT.id <= account_transactions.id) as balance')
                    );
            }
            if (!empty($start_date)  && !empty($end_date)) {
                $query->whereDate(
                    'transactions.transaction_date',
                    '>=',
                    $start_date
                )->whereDate('transactions.transaction_date', '<=', $end_date)->get();
            }
            $ledger_transactions = $query->get();

            $orig_data = [
                'email_body' => $data['email_body'],
                'subject' => $data['subject']
            ];

            $tag_replaced_data = $this->notificationUtil->replaceTags($business_id, $orig_data, null, $contact);
            $data['email_body'] = $tag_replaced_data['email_body'];
            $data['subject'] = $tag_replaced_data['subject'];

            //replace balance_due
            $data['email_body'] = str_replace('{balance_due}', $this->notificationUtil->num_f($ledger_details['balance_due']), $data['email_body']);

            $data['email_settings'] = request()->session()->get('business.email_settings');


            $for_pdf = true;
            $html = view('contact.ledger')
                ->with(compact('ledger_details', 'contact', 'for_pdf', 'ledger_transactions', 'business_details', 'location_details'))->render();
            $mpdf = $this->getMpdf();
            $mpdf->WriteHTML($html);

            $file = config('constants.mpdf_temp_path') . '/' . time() . '_ledger.pdf';
            $mpdf->Output($file, 'F');

            $data['attachment'] =  $file;
            $data['attachment_name'] =  'ledger.pdf';
            \Notification::route('mail', $emails_array)
                ->notify(new CustomerNotification($data));

            if (file_exists($file)) {
                unlink($file);
            }

            $output = ['success' => 1, 'msg' => __('lang_v1.notification_sent_successfully')];
        } catch (\Exception $e) {
            Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

            $output = [
                'success' => 0,
                'msg' => "File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage()
            ];
        }

        return $output;
    }

    /**
     * Function to get product stock details for a supplier
     *
     */
    public function getSupplierStockReport($supplier_id)
    {
        $pl_query_string = $this->commonUtil->get_pl_quantity_sum_string();
        $query = PurchaseLine::join('transactions as t', 't.id', '=', 'purchase_lines.transaction_id')
            ->join('products as p', 'p.id', '=', 'purchase_lines.product_id')
            ->join('variations as v', 'v.id', '=', 'purchase_lines.variation_id')
            ->join('product_variations as pv', 'v.product_variation_id', '=', 'pv.id')
            ->join('units as u', 'p.unit_id', '=', 'u.id')
            ->where('t.type', 'purchase')
            ->where('t.contact_id', $supplier_id)
            ->select(
                'p.name as product_name',
                'v.name as variation_name',
                'pv.name as product_variation_name',
                'p.type as product_type',
                'u.short_name as product_unit',
                'v.sub_sku',
                DB::raw('SUM(quantity) as purchase_quantity'),
                DB::raw('SUM(quantity_returned) as total_quantity_returned'),
                DB::raw('SUM(quantity_sold) as total_quantity_sold'),
                DB::raw("SUM( COALESCE(quantity - ($pl_query_string), 0) * purchase_price_inc_tax) as stock_price"),
                DB::raw("SUM( COALESCE(quantity - ($pl_query_string), 0)) as current_stock")
            )->groupBy('purchase_lines.variation_id');

        if (!empty(request()->location_id)) {
            $query->where('t.location_id', request()->location_id);
        }

        $product_stocks =  Datatables::of($query)
            ->editColumn('product_name', function ($row) {
                $name = $row->product_name;
                if ($row->product_type == 'variable') {
                    $name .= ' - ' . $row->product_variation_name . '-' . $row->variation_name;
                }
                return $name . ' (' . $row->sub_sku . ')';
            })
            ->editColumn('purchase_quantity', function ($row) {
                $purchase_quantity = 0;
                if ($row->purchase_quantity) {
                    $purchase_quantity =  (float) $row->purchase_quantity;
                }

                return '<span data-is_quantity="true" class="display_currency" data-currency_symbol=false  data-orig-value="' . $purchase_quantity . '" data-unit="' . $row->product_unit . '" >' . $purchase_quantity . '</span> ' . $row->product_unit;
            })
            ->editColumn('total_quantity_sold', function ($row) {
                $total_quantity_sold = 0;
                if ($row->total_quantity_sold) {
                    $total_quantity_sold =  (float) $row->total_quantity_sold;
                }

                return '<span data-is_quantity="true" class="display_currency" data-currency_symbol=false  data-orig-value="' . $total_quantity_sold . '" data-unit="' . $row->product_unit . '" >' . $total_quantity_sold . '</span> ' . $row->product_unit;
            })
            ->editColumn('stock_price', function ($row) {
                $stock_price = 0;
                if ($row->stock_price) {
                    $stock_price =  (float) $row->stock_price;
                }

                return '<span class="display_currency" data-currency_symbol=true >' . $stock_price . '</span> ';
            })
            ->editColumn('current_stock', function ($row) {
                $current_stock = 0;
                if ($row->current_stock) {
                    $current_stock =  (float) $row->current_stock;
                }

                return '<span data-is_quantity="true" class="display_currency" data-currency_symbol=false  data-orig-value="' . $current_stock . '" data-unit="' . $row->product_unit . '" >' . $current_stock . '</span> ' . $row->product_unit;
            });

        return $product_stocks->rawColumns(['current_stock', 'stock_price', 'total_quantity_sold', 'purchase_quantity'])->make(true);
    }

    public function toggleActivate($contact_id)
    {
        $contact = Contact::findOrFail($contact_id);
        $active_status = $contact->active;
        $contact->active = !$active_status;
        $contact->save();

        if ($active_status) {
            $output = ['success' => 1, 'msg' => __('lang_v1.contact_deactivate_success')];
        } else {
            $output = ['success' => 1, 'msg' => __('lang_v1.contact_activate_success')];
        }

        return redirect()->back()->with('status', $output);
    }

    public function listSecurityDeposit()
    {

        if (request()->ajax()) {
            $business_id = request()->business_id;
            $contact = Contact::where('business_id', $business_id)->where('contact_id',  request()->contact_id)->first();
            $contact_id = $contact->id;
            $security_deposit = Transaction::leftjoin('users', 'transactions.created_by', 'users.id')
                ->where('transactions.business_id', $business_id)->where('transactions.contact_id', $contact_id)
                ->where('transactions.type', 'security_deposit')
                ->select('transactions.transaction_date', 'users.username', 'transactions.final_total');

            return Datatables::of($security_deposit)
                ->editColumn('final_total', '{{@num_format($final_total)}}')
                ->rawColumns([])
                ->make(true);
        }
    }

    public function getOutstandingReceivedReport()
    {
        $business_id = request()->session()->get('user.business_id');

        $business_locations = BusinessLocation::forDropdown($business_id);
        $suppliers = Contact::suppliersDropdown($business_id, false);
        $customers = Contact::customersDropdown($business_id, false);
        $payment_types = $this->transactionUtil->payment_types();
        $customer_group = ContactGroup::forDropdown($business_id, false, true);
        $types = Contact::typeDropdown(true);
        $bill_nos = Transaction::invoiveNumberDropDown('sell');
        $payment_ref_nos = Transaction::paymentRefNumberDropDown('sell');
        $cheque_numbers = Transaction::chequeNumberDropDown('sell');

        return view('contact.outstanding_received_report')->with(compact(
            'suppliers',
            'business_locations',
            'customers',
            'customer_group',
            'types',
            'payment_types',
            'bill_nos',
            'payment_ref_nos',
            'cheque_numbers'
        ));
    }

    public function getIssuedPaymentDetails()
    {
        $business_id = request()->session()->get('user.business_id');

        $business_locations = BusinessLocation::forDropdown($business_id);
        $suppliers = Contact::suppliersDropdown($business_id, false);
        $customers = Contact::customersDropdown($business_id, false);
        $payment_types = $this->transactionUtil->payment_types();
        $customer_group = ContactGroup::forDropdown($business_id, false, true, 'supplier');
        $types = Contact::typeDropdown(true);
        $bill_nos = Transaction::invoiveNumberDropDown('purchase');
        $payment_ref_nos = Transaction::paymentRefNumberDropDown('purchase');
        $cheque_numbers = Transaction::chequeNumberDropDown('purchase');
        $business_details = Business::find($business_id);

        if (request()->ajax()) {

            $purchase = Transaction::leftJoin('contacts', 'transactions.contact_id', '=', 'contacts.id')
                ->leftJoin('transaction_payments as tp', 'transactions.id', '=', 'tp.transaction_id')
                ->where('transactions.business_id', $business_id)
                ->where('transactions.type', 'purchase')
                ->whereIn('transactions.payment_status', ['paid', 'partial'])
                ->select(
                    'transactions.id',
                    'transactions.transaction_date',
                    'transactions.invoice_no',
                    'contacts.name',
                    'transactions.payment_status',
                    'transactions.final_total',
                    'tp.paid_on',
                    'tp.method',
                    'tp.cheque_number',
                    'tp.card_number',
                    'tp.payment_ref_no',
                    DB::raw('SUM(tp.amount) as total_paid')
                );


            if (!empty(request()->customer_id)) {
                $customer_id = request()->customer_id;
                $purchase->where('contacts.id', $customer_id);
            }
            if (!empty(request()->bill_no)) {
                $purchase->where('transactions.invoice_no', request()->bill_no);
            }
            if (!empty(request()->payment_ref_no)) {
                $purchase->where('tp.payment_ref_no', request()->payment_ref_no);
            }
            if (!empty(request()->cheque_number)) {
                $purchase->where('tp.cheque_number', request()->cheque_number);
            }
            if (!empty(request()->payment_type)) {
                $purchase->where('tp.method', request()->payment_type);
            }
            if (!empty(request()->start_date) && !empty(request()->end_date)) {
                $start = request()->start_date;
                $end =  request()->end_date;
                $purchase->whereDate('transaction_date', '>=', $start)
                    ->whereDate('transaction_date', '<=', $end);
            }

            $purchase->orderBy('tp.paid_on', 'desc')->groupBy('tp.id');

            $datatable = Datatables::of($purchase)
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

                        $html .= '<li><a href="#" class="print-invoice" data-href="' . route('sell.printInvoice', [$row->id]) . '"><i class="fa fa-print" aria-hidden="true"></i> ' . __("messages.print") . '</a></li>
                            <li><a href="#" class="print-invoice" data-href="' . route('sell.printInvoice', [$row->id]) . '?package_slip=true"><i class="fa fa-file-text-o" aria-hidden="true"></i> ' . __("lang_v1.packing_slip") . '</a></li>';
                        $html .= '<li><a href="#" data-href="' . action('SellController@editShipping', [$row->id]) . '" class="btn-modal" data-container=".view_modal"><i class="fa fa-truck" aria-hidden="true"></i>' . __("lang_v1.edit_shipping") . '</a></li>';

                        $html .= '</ul></div>';

                        return $html;
                    }
                )
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

            $rawColumns = ['final_total', 'action', 'total_paid', 'total_remaining', 'payment_status', 'invoice_no', 'discount_amount', 'tax_amount', 'total_before_tax', 'shipping_status'];

            return $datatable->rawColumns($rawColumns)
                ->make(true);
        }

        return view('contact.issued_payment_details')->with(compact(
            'suppliers',
            'business_locations',
            'customers',
            'customer_group',
            'types',
            'payment_types',
            'bill_nos',
            'payment_ref_nos',
            'cheque_numbers'
        ));
    }
}
