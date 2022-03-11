<?php

namespace Modules\Property\Http\Controllers;

use App\Account;
use App\AccountTransaction;
use App\AccountType;
use App\BusinessLocation;
use App\Contact;
use App\ExpenseCategory;
use App\TaxRate;
use App\Transaction;
use App\TransactionPayment;
use App\User;
use App\Utils\ModuleUtil;
use App\Utils\TransactionUtil;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\Property\Entities\Property;
use Modules\Property\Entities\PropertyAccountSetting;
use Yajra\DataTables\Facades\DataTables;

class ExpenseController extends Controller
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

        $this->dummyPaymentLine = [
            'method' => '', 'amount' => 0, 'note' => '', 'card_transaction_number' => '', 'card_number' => '', 'card_type' => '', 'card_holder_name' => '', 'card_month' => '', 'card_year' => '', 'card_security' => '', 'cheque_number' => '', 'cheque_date' => '', 'bank_account_number' => '',
            'is_return' => 0, 'transaction_no' => ''
        ];
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (!auth()->user()->can('expense.access')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            $business_id = request()->session()->get('user.business_id');

            $expenses = Transaction::leftJoin('expense_categories AS ec', 'transactions.expense_category_id', '=', 'ec.id')
                ->join(
                    'business_locations AS bl',
                    'transactions.location_id',
                    '=',
                    'bl.id'
                )
                ->leftJoin('tax_rates as tr', 'transactions.tax_id', '=', 'tr.id')
                ->leftJoin('users AS U', 'transactions.expense_for', '=', 'U.id')
                ->leftJoin('users AS m', 'transactions.created_by', '=', 'm.id')
                ->leftjoin('transaction_payments AS TP', function ($join) {
                    $join->on('transactions.id', 'TP.transaction_id')->where('TP.amount', '!=', 0);
                })
                ->where('transactions.business_id', $business_id)
                ->where(function ($query) {
                    $query->where('transactions.type', 'expense')->orWhere('sub_type', 'expense');
                })
                ->select(
                    'transactions.id',
                    'transactions.document',
                    'transaction_date',
                    'ref_no',
                    'ec.name as category',
                    'payment_status',
                    'additional_notes',
                    'final_total',
                    'is_settlement',
                    'bl.name as location_name',
                    'TP.method',
                    'TP.cheque_date',
                    'TP.cheque_number',
                    'TP.account_id',
                    DB::raw("CONCAT(COALESCE(U.surname, ''),' ',COALESCE(U.first_name, ''),' ',COALESCE(U.last_name,'')) as expense_for"),
                    DB::raw("CONCAT(tr.name ,' (', tr.amount ,' )') as tax"),
                    DB::raw('SUM(TP.amount) as amount_paid'),
                    DB::raw("CONCAT(COALESCE(m.surname, ''),' ',COALESCE(m.first_name, ''),' ',COALESCE(m.last_name,'')) as created_by")
                )
                ->groupBy('transactions.id');

            //Add condition for expense for,used in sales representative expense report & list of expense
            if (request()->has('expense_for')) {
                $expense_for = request()->get('expense_for');
                if (!empty($expense_for)) {
                    $expenses->where('transactions.expense_for', $expense_for);
                }
            }

            //Add condition for location,used in sales representative expense report & list of expense
            if (request()->has('location_id')) {
                $location_id = request()->get('location_id');
                if (!empty($location_id)) {
                    $expenses->where('transactions.location_id', $location_id);
                }
            }

            //Add condition for expense category, used in list of expense,
            if (request()->has('expense_category_id')) {
                $expense_category_id = request()->get('expense_category_id');
                if (!empty($expense_category_id)) {
                    $expenses->where('transactions.expense_category_id', $expense_category_id);
                }
            }

            //Add condition for start and end date filter, uses in sales representative expense report & list of expense
            if (!empty(request()->start_date) && !empty(request()->end_date)) {
                $start = request()->start_date;
                $end =  request()->end_date;
                $expenses->whereDate('transaction_date', '>=', $start)
                    ->whereDate('transaction_date', '<=', $end);
            }

            //Add condition for expense category, used in list of expense,
            if (request()->has('expense_category_id')) {
                $expense_category_id = request()->get('expense_category_id');
                if (!empty($expense_category_id)) {
                    $expenses->where('transactions.expense_category_id', $expense_category_id);
                }
            }

            $permitted_locations = auth()->user()->permitted_locations();
            if ($permitted_locations != 'all') {
                $expenses->whereIn('transactions.location_id', $permitted_locations);
            }

            //Add condition for payment status for the list of expense
            if (request()->has('payment_status')) {
                $payment_status = request()->get('payment_status');
                if (!empty($payment_status)) {
                    $expenses->where('transactions.payment_status', $payment_status);
                }
            }

            return DataTables::of($expenses)
                ->addColumn(
                    'action',
                    '<div class="btn-group">
                        <button type="button" class="btn btn-info dropdown-toggle btn-xs" 
                            data-toggle="dropdown" aria-expanded="false"> @lang("messages.actions")<span class="caret"></span><span class="sr-only">Toggle Dropdown
                                </span>
                        </button>
                    <ul class="dropdown-menu dropdown-menu-left" role="menu">
                    @can("expense.update")
                    <li><a href="{{action(\'ExpenseController@edit\', [$id])}}"><i class="glyphicon glyphicon-edit"></i> @lang("messages.edit")</a></li>
                    @endcan
                    @if($document)
                        <li><a href="{{ url(\'uploads/documents/\' . $document)}}" 
                        download=""><i class="fa fa-download" aria-hidden="true"></i> @lang("purchase.download_document")</a></li>
                        @if(isFileImage($document))
                            <li><a href="#" data-href="{{ url(\'uploads/documents/\' . $document)}}" class="view_uploaded_document"><i class="fa fa-picture-o" aria-hidden="true"></i>@lang("lang_v1.view_document")</a></li>
                        @endif
                    @endif
                    @can("expense.delete")
                        <li><a data-href="{{action(\'ExpenseController@destroy\', [$id])}}" class="delete_expense"><i class="glyphicon glyphicon-trash"></i> @lang("messages.delete")</a></li>
                    @endcan
                    <li class="divider"></li> 
                    @if($payment_status != "paid")
                        @can("expense.create_payment")
                            <li><a href="{{action("TransactionPaymentController@addPayment", [$id])}}" class="add_payment_modal"><i class="fa fa-money" aria-hidden="true"></i> @lang("purchase.add_payment")</a></li>
                        @endcan
                    @endif
                    <li><a href="{{action("TransactionPaymentController@show", [$id])}}" class="view_payment_modal"><i class="fa fa-money" aria-hidden="true" ></i> @lang("purchase.view_payments")</a></li>
                    </ul></div>'
                )
                ->removeColumn('id')
                ->editColumn(
                    'final_total',
                    '<span class="display_currency final-total" data-currency_symbol="true" data-orig-value="{{$final_total}}">{{$final_total}}</span>'
                )
                ->editColumn('transaction_date', '{{@format_datetime($transaction_date)}}')
                ->editColumn('ref_no', function ($row) {
                    $ref = $row->ref_no;
                    return $ref;
                })
                ->editColumn(
                    'payment_status',
                    '<a href="{{ action("TransactionPaymentController@show", [$id])}}" class="view_payment_modal payment-status no-print" data-orig-value="{{$payment_status}}" data-status-name="{{__(\'lang_v1.\' . $payment_status)}}"><span class="label @payment_status($payment_status)">{{__(\'lang_v1.\' . $payment_status)}}
                        </span></a><span class="print_section">{{__(\'lang_v1.\' . $payment_status)}}</span>'
                )
                ->addColumn('payment_due', function ($row) {
                    $due = $row->final_total - $row->amount_paid;
                    return '<span class="display_currency payment_due" data-currency_symbol="true" data-orig-value="' . $due . '">' . $due . '</span>';
                })
                ->addColumn('payment_method', function ($row) {
                    $html = '';
                    if ($row->payment_status == 'due') {
                        return 'Credit Expense';
                    }
                    if ($row->method == 'bank_transfer') {
                        $bank_acccount = Account::find($row->account_id);
                        if (!empty($bank_acccount)) {
                            $html .= '<b>Bank Name:</b> ' . $bank_acccount->name . '</br>';
                            $html .= '<b>Account Number:</b> ' . $bank_acccount->account_number . '</br>';
                        }
                    } else if ($row->method == 'cash') {
                        $bank_acccount = Account::find($row->account_id);
                        if (!empty($bank_acccount)) {
                            $html .= $bank_acccount->name . '</br>';
                        } else {
                            $html .= ucfirst($row->method);
                        }
                    } else {
                        $html .= ucfirst($row->method);
                    }

                    return $html;
                })
                ->rawColumns(['final_total', 'action', 'payment_status', 'payment_due', 'ref_no', 'payment_method'])
                ->make(true);
        }

        $business_id = request()->session()->get('user.business_id');

        $categories = ExpenseCategory::where('business_id', $business_id)
            ->pluck('name', 'id');

        $users = User::forDropdown($business_id, false, true, true);

        $business_locations = BusinessLocation::forDropdown($business_id, true);

        return view('property::expense.index')
            ->with(compact('categories', 'business_locations', 'users'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (!auth()->user()->can('expense.create')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');

        //Check if subscribed or not
        if (!$this->moduleUtil->isSubscribed($business_id)) {
            return $this->moduleUtil->expiredResponse(action('ExpenseController@index'));
        }

        $property_id = request()->property_id;

        $account_module = $this->moduleUtil->hasThePermissionInSubscription($business_id, 'access_account');
        $payment_line = $this->dummyPaymentLine;
        $first_location = BusinessLocation::where('business_id', $business_id)->first();
        $payment_types = $this->transactionUtil->payment_types($first_location->id, true, false, true);

        unset($payment_types['credit_sale']);
        $accounts = [];
        $expense_account_type_id = AccountType::where('business_id', $business_id)->where('name', 'Expenses')->first();
        $current_account_type_id = AccountType::where('business_id', $business_id)->where('name', 'Current Assets')->first();
        $current_liability_account_type = AccountType::where('business_id', $business_id)->where('name', 'Current Liabilities')->first();
        $current_liability_account_type_id = !empty($current_liability_account_type) ? $current_liability_account_type->id : 0;
        $expense_accounts = [];
        $expense_account_id = null;

        if ($account_module) {
            if (!empty($expense_account_type_id)) {
                $expense_accounts = Account::where('business_id', $business_id)->where('account_type_id', $expense_account_type_id->id)->pluck('name', 'id');
            }
        } else {
            $expense_account_id = Account::where('name', 'Expenses')->where('business_id', $business_id)->first()->id;
            $expense_accounts = Account::where('business_id', $business_id)->where('name', 'Expenses')->pluck('name', 'id');
        }
        $current_liabilities_accounts =  Account::where('business_id', $business_id)->where('account_type_id', $current_liability_account_type_id)->pluck('name', 'id');

        $business_locations = BusinessLocation::forDropdown($business_id);
        $contacts = Contact::contactDropdown($business_id, false, false);

        $properties = Property::where('business_id', $business_id)->where('id', $property_id)->first();
        $property_account_setting = PropertyAccountSetting::where('property_id', $property_id)->first();
        $expense_categories = ExpenseCategory::where('business_id', $business_id)
            ->pluck('name', 'id');
        $users = User::forDropdown($business_id, true, true);

        $taxes = TaxRate::forBusinessDropdown($business_id, true, true);

        $ref_count = $this->transactionUtil->onlyGetReferenceCount('expense', null, false);
        //Generate reference number
        $ref_no = $this->transactionUtil->generateReferenceNumber('expense', $ref_count);


        $temp_data = [];

        $cash_account_id = Account::getAccountByAccountName('Cash')->id;

        return view('property::expense.create')
            ->with(compact(
                'cash_account_id',
                'ref_no',
                'properties',
                'property_account_setting',
                'property_id',
                'account_module',
                'accounts',
                'expense_accounts',
                'payment_types',
                'payment_line',
                'expense_categories',
                'business_locations',
                'users',
                'taxes',
                'temp_data',
                'contacts',
                'current_liabilities_accounts',
                'expense_account_id'
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
        if (!auth()->user()->can('expense.create')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $business_id = $request->session()->get('user.business_id');

            DB::table('temp_data')->where('business_id', $business_id)->update(['add_expense_data' => '']);
            //Check if subscribed or not
            if (!$this->moduleUtil->isSubscribed($business_id)) {
                return $this->moduleUtil->expiredResponse(action('ExpenseController@index'));
            }

            //Validate document size
            $request->validate([
                'document' => 'file|max:' . (config('constants.document_size_limit') / 1000)
            ]);

            $transaction_data = $request->only(['ref_no', 'transaction_date', 'location_id', 'final_total', 'expense_for', 'property_id', 'additional_notes', 'expense_category_id', 'tax_id', 'contact_id']);

            $user_id = $request->session()->get('user.id');
            $transaction_data['business_id'] = $business_id;
            $transaction_data['created_by'] = $user_id;
            $transaction_data['type'] = 'expense';
            $transaction_data['status'] = 'final';
            $transaction_data['payment_status'] = 'due';
            $transaction_data['expense_account'] = $request->expense_account;
            $transaction_data['controller_account'] = $request->controller_account;
            $transaction_data['transaction_date'] = $this->transactionUtil->uf_date($transaction_data['transaction_date'], true);
            $transaction_data['final_total'] = $this->transactionUtil->num_uf(
                $transaction_data['final_total']
            );

            $transaction_data['total_before_tax'] = $transaction_data['final_total'];
            if (!empty($transaction_data['tax_id'])) {
                $tax_details = TaxRate::find($transaction_data['tax_id']);
                $transaction_data['total_before_tax'] = $this->transactionUtil->calc_percentage_base($transaction_data['final_total'], $tax_details->amount);
                $transaction_data['tax_amount'] = $transaction_data['final_total'] - $transaction_data['total_before_tax'];
            }

            //Update reference count
            $ref_count = $this->transactionUtil->setAndGetReferenceCount('expense');
            //Generate reference number
            if (empty($transaction_data['ref_no'])) {
                $transaction_data['ref_no'] = $this->transactionUtil->generateReferenceNumber('expense', $ref_count);
            }

            //upload document
            $document_name = $this->transactionUtil->uploadFile($request, 'document', 'documents');
            if (!empty($document_name)) {
                $transaction_data['document'] = $document_name;
            }

            if ($request->has('is_recurring')) {
                $transaction_data['is_recurring'] = 1;
                $transaction_data['recur_interval'] = !empty($request->input('recur_interval')) ? $request->input('recur_interval') : 1;
                $transaction_data['recur_interval_type'] = $request->input('recur_interval_type');
                $transaction_data['recur_repetitions'] = $request->input('recur_repetitions');
                $transaction_data['subscription_repeat_on'] = $request->input('recur_interval_type') == 'months' && !empty($request->input('subscription_repeat_on')) ? $request->input('subscription_repeat_on') : null;
            }

            DB::beginTransaction();
            $transaction = Transaction::create($transaction_data);

            $transaction_id =  $transaction->id;
            $tp = null;
            if ($transaction->payment_status != 'paid') {
                $inputs = $request->payment[0];

                $inputs['paid_on'] = $transaction->transaction_date;
                $inputs['transaction_id'] = $transaction->id;

                $inputs['amount'] = $this->transactionUtil->num_uf($inputs['amount']);
                $amount = $inputs['amount'];
                if ($amount > 0 && $inputs['method'] != 'credit_expense') {
                    $inputs['created_by'] = auth()->user()->id;
                    $inputs['payment_for'] = $transaction->contact_id;

                    if (is_numeric($inputs['method'])) {
                        $inputs['account_id'] = $inputs['method'];
                        $inputs['method'] = 'cash';
                    }

                    if (!empty($inputs['account_id'])) {
                        $inputs['account_id'] = $inputs['account_id'];
                    }

                    if ($transaction->type == 'expense') {
                        $prefix_type = 'expense_payment';
                    }
                    $transaction->controller_account = !empty($inputs['controller_account']) ? $inputs['controller_account'] : null;
                    $transaction->save();

                    $ref_count = $this->transactionUtil->setAndGetReferenceCount($prefix_type);
                    //Generate reference number
                    $inputs['payment_ref_no'] = $this->transactionUtil->generateReferenceNumber($prefix_type, $ref_count);

                    $inputs['business_id'] = $request->session()->get('business.id');
                    $inputs['document'] = $this->transactionUtil->uploadFile($request, 'document', 'documents');

                    $inputs['is_return'] =  0; //added by ahmed
                    unset($inputs['transaction_no_1']);
                    unset($inputs['transaction_no_2']);
                    unset($inputs['transaction_no_3']);
                    unset($inputs['controller_account']);
                    $tp = TransactionPayment::create($inputs);

                    //update payment status
                    $this->transactionUtil->updatePaymentStatus($transaction_id, $transaction->final_total);
                }
            }
            $this->addAccountTransaction($transaction, $request, $business_id, $tp);
            DB::commit();
            $output = [
                'success' => 1,
                'msg' => __('expense.expense_add_success')
            ];
        } catch (\Exception $e) {
            Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

            $output = [
                'success' => 0,
                'msg' => __('messages.something_went_wrong')
            ];
        }

        return redirect('property/properties')->with('status', $output);
    }

    /**
     * Add Account Transactions
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function addAccountTransaction($transaction, $request, $business_id,  $tp)
    {
        if (!empty($request->expense_account)) {
            $ob_transaction_data = [
                'amount' => $request->final_total,
                'account_id' => $request->expense_account,
                'type' => 'debit',
                'sub_type' => 'expense',
                'operation_date' => $transaction->transaction_date,
                'created_by' => Auth::user()->id,
                'transaction_id' => $transaction->id,
                'transaction_payment_id' => !empty($tp) ? $tp->id : null
            ];

            AccountTransaction::createAccountTransaction($ob_transaction_data);

            $payment = $request->payment[0];

            $account_payable_id = !empty($payment['controller_account']) ? $payment['controller_account'] : Account::where('business_id', $business_id)->where('name', 'Accounts Payable')->first()->id;
            $ap_transaction_data = [
                'operation_date' => $transaction->transaction_date,
                'created_by' => Auth::user()->id,
                'transaction_id' => $transaction->id,
                'transaction_payment_id' => !empty($tp) ? $tp->id : null
            ];
            if ($payment['method'] == 'credit_expense') {
                $payment['amount'] = 0;
            }

            //if no amount paid
            if ($payment['amount'] == 0) {
                $ap_transaction_data['amount'] = $request->final_total;
                $ap_transaction_data['account_id'] = $account_payable_id;
                $ap_transaction_data['type'] = 'credit';

                AccountTransaction::createAccountTransaction($ap_transaction_data);
            }

            //if partial amount paid
            else if ($payment['amount'] < $request->final_total) {
                $ap_transaction_data['amount'] = $payment['amount'];  //paid amount
                if ($payment['method'] == 'bank_transfer' || $payment['method'] == 'direct_bank_deposit') {
                    $ap_transaction_data['account_id'] = $payment['account_id'];
                } else {
                    //if method is numberic, then it hold the cash groups account id
                    if (is_numeric($payment['method'])) {
                        $ap_transaction_data['account_id'] = $payment['method'];
                    } else {
                        $ap_transaction_data['account_id'] =  $this->transactionUtil->getDefaultAccountId($payment['method'], $request->location_id);
                    }
                }
                $ap_transaction_data['type'] = 'credit';

                AccountTransaction::createAccountTransaction($ap_transaction_data);

                $ap_transaction_data['amount'] = $request->final_total - $payment['amount']; //unpaid amount
                $ap_transaction_data['account_id'] = $account_payable_id;
                $ap_transaction_data['type'] = 'credit';

                AccountTransaction::createAccountTransaction($ap_transaction_data);
            }
            // if full amount paid
            if ($payment['amount'] == $request->final_total) {
                $ap_transaction_data['amount'] = $payment['amount'];  // full paid amount
                if ($payment['method'] == 'bank_transfer' || $payment['method'] == 'direct_bank_deposit') {
                    $ap_transaction_data['account_id'] = $payment['account_id'];
                } else {
                    if (is_numeric($payment['method'])) {
                        $ap_transaction_data['account_id'] = $payment['method'];
                    } else {
                        $ap_transaction_data['account_id'] =  $this->transactionUtil->getDefaultAccountId($payment['method'], $request->location_id);
                    }
                }
                $ap_transaction_data['type'] = 'credit';

                AccountTransaction::createAccountTransaction($ap_transaction_data);
            }
        }
    }

    /**
     * Add Account Transactions
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function reverseAccountTransaction($transaction, $request, $business_id)
    {
        AccountTransaction::where('transaction_id', $transaction->id)->forcedelete();
    }


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        $business_id = $request->session()->get('user.business_id');
        $data = DB::table('temp_data')->where('business_id', $business_id)->first();
        return view('property::expense.show' , compact('data'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (!auth()->user()->can('expense.update')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');

        //Check if subscribed or not
        if (!$this->moduleUtil->isSubscribed($business_id)) {
            return $this->moduleUtil->expiredResponse(action('ExpenseController@index'));
        }

        $business_locations = BusinessLocation::forDropdown($business_id);

        $expense_categories = ExpenseCategory::where('business_id', $business_id)
            ->pluck('name', 'id');
        $expense = Transaction::where('business_id', $business_id)
            ->where('id', $id)->with(['purchase_lines'])
            ->first();
        $users = User::forDropdown($business_id, true, true);
        $first_location = BusinessLocation::where('business_id', $business_id)->first();
        $payment_types = $this->transactionUtil->payment_types($first_location->id, true, false, true);
        unset($payment_types['credit_sale']);
        $taxes = TaxRate::forBusinessDropdown($business_id, true, true);
        $account_module = $this->moduleUtil->hasThePermissionInSubscription($business_id, 'access_account');
        $payment_line = $this->dummyPaymentLine;
        $accounts = [];
        $expense_account_type_id = AccountType::where('business_id', $business_id)->where('name', 'Expenses')->first();
        $current_account_type_id = AccountType::where('business_id', $business_id)->where('name', 'Current Assets')->first();
        $current_liability_account_type = AccountType::where('business_id', $business_id)->where('name', 'Current Liabilities')->first();
        $current_liability_account_type_id = !empty($current_liability_account_type) ? $current_liability_account_type->id : 0;

        $contacts = Contact::contactDropdown($business_id, false, false);
        $expense_accounts = [];

        if ($account_module) {
            if (!empty($expense_account_type_id)) {
                $expense_accounts = Account::where('business_id', $business_id)->where('account_type_id', $expense_account_type_id->id)->pluck('name', 'id');
            }
        } else {
            $expense_accounts = Account::where('business_id', $business_id)->where('name', 'Expenses')->pluck('name', 'id');
        }

        $property_id = $expense->property_id; // expense for hold property id

        $properties = Property::where('business_id', $business_id)->where('id', $property_id)->pluck('name', 'id');
        $property_account_setting = PropertyAccountSetting::where('property_id', $property_id)->first();

        $current_liabilities_accounts =  Account::where('business_id', $business_id)->where('account_type_id', $current_liability_account_type_id)->pluck('name', 'id');
        $cash_account_id = Account::getAccountByAccountName('Cash')->id;;
        return view('property::expense.edit')
            ->with(compact(
                'cash_account_id',
                'properties',
                'property_account_setting',
                'expense',
                'expense_categories',
                'business_locations',
                'users',
                'taxes',
                'payment_types',
                'account_module',
                'payment_line',
                'accounts',
                'current_liabilities_accounts',
                'expense_accounts',
                'contacts'
            ));
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
        if (!auth()->user()->can('expense.update')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            //Validate document size
            $request->validate([
                'document' => 'file|max:' . (config('constants.document_size_limit') / 1000)
            ]);

            $transaction_data = $request->only(['ref_no', 'transaction_date', 'location_id', 'final_total', 'expense_for', 'property_id', 'additional_notes', 'expense_category_id', 'tax_id', 'contact_id', 'expense_account']);

            $business_id = $request->session()->get('user.business_id');

            //Check if subscribed or not
            if (!$this->moduleUtil->isSubscribed($business_id)) {
                return $this->moduleUtil->expiredResponse(action('ExpenseController@index'));
            }

            $transaction_data['transaction_date'] = $this->transactionUtil->uf_date($transaction_data['transaction_date'], true);
            $transaction_data['final_total'] = $this->transactionUtil->num_uf(
                $transaction_data['final_total']
            );

            //upload document
            $document_name = $this->transactionUtil->uploadFile($request, 'document', 'documents');
            if (!empty($document_name)) {
                $transaction_data['document'] = $document_name;
            }

            $transaction_data['total_before_tax'] = $transaction_data['final_total'];
            if (!empty($transaction_data['tax_id'])) {
                $tax_details = TaxRate::find($transaction_data['tax_id']);
                $transaction_data['total_before_tax'] = $this->transactionUtil->calc_percentage_base($transaction_data['final_total'], $tax_details->amount);
                $transaction_data['tax_amount'] = $transaction_data['final_total'] - $transaction_data['total_before_tax'];
            }
            DB::beginTransaction();

            $transaction = Transaction::findOrFail($id);

            $transaction_data['is_recurring'] = $request->has('is_recurring') ? 1 : $transaction->is_recurring;
            $transaction_data['recur_interval'] = $request->has('is_recurring') && !empty($request->input('recur_interval')) ? $request->input('recur_interval') : $transaction->recur_interval;
            $transaction_data['recur_interval_type'] = !empty($request->input('recur_interval_type')) ? $request->input('recur_interval_type') : $transaction->recur_interval_type;
            $transaction_data['recur_repetitions'] = !empty($request->input('recur_repetitions')) ? $request->input('recur_repetitions') : $transaction->recur_repetitions;
            $transaction_data['subscription_repeat_on'] = !empty($request->input('subscription_repeat_on')) ? $request->input('subscription_repeat_on') : $transaction->subscription_repeat_on;

            $transaction->update($transaction_data);

            $transaction_id =  $transaction->id;

            $inputs = $request->payment[0];

            $inputs['paid_on'] = $transaction->transaction_date;
            $inputs['transaction_id'] = $transaction->id;

            $inputs['amount'] = $this->transactionUtil->num_uf($inputs['amount']);
            $inputs['created_by'] = auth()->user()->id;
            $inputs['payment_for'] = $transaction->contact_id;

            //if method is numberic, then it hold the cash groups account id
            if (is_numeric($inputs['method'])) {
                $inputs['method'] = 'cash';
            }


            if (!empty($inputs['account_id'])) {
                $inputs['account_id'] = $inputs['account_id'];
            }

            if ($transaction->type == 'expense') {
                $prefix_type = 'expense_payment';
            }

            $transaction->controller_account = !empty($inputs['controller_account']) ? $inputs['controller_account'] : null;
            $transaction->save();

            $ref_count = $this->transactionUtil->setAndGetReferenceCount($prefix_type);
            //Generate reference number
            $inputs['payment_ref_no'] = $this->transactionUtil->generateReferenceNumber($prefix_type, $ref_count);

            $inputs['business_id'] = $request->session()->get('business.id');
            $inputs['document'] = $this->transactionUtil->uploadFile($request, 'document', 'documents');

            $inputs['is_return'] =  0; //added by ahmed
            unset($inputs['transaction_no_1']);
            unset($inputs['transaction_no_2']);
            unset($inputs['transaction_no_3']);
            unset($inputs['controller_account']);
            $tp = null;
            if ($inputs['method'] != 'credit_expense') {
                $tp = TransactionPayment::updateOrCreate(['transaction_id' => $transaction_id], $inputs);
            }

            //update payment status
            $this->transactionUtil->updatePaymentStatus($transaction_id, $transaction->final_total);
            $payment = $request->payment[0];
            if (is_numeric($payment['method'])) {
                $account_id = $payment['method'];
            } else {
                $account_id = $this->transactionUtil->getDefaultAccountId($payment['method'], $request->location_id);
            }

            //get account trasaction for changed account 
            $transaction_exist = AccountTransaction::where('transaction_id', $id)->where('account_id',  $request->expense_account)->first();
            $payment_transaction_exist = AccountTransaction::where('transaction_id', $id)->where('account_id',  $account_id)->first();

            if (empty($transaction_exist) || empty($payment_transaction_exist)) { // if account is changed
                $this->reverseAccountTransaction($transaction, $request, $business_id); // reverse previous account transaction
                $this->addAccountTransaction($transaction, $request, $business_id, $tp); // add new transactions
            }


            DB::commit();
            $output = [
                'success' => 1,
                'msg' => __('expense.expense_update_success')
            ];
        } catch (\Exception $e) {
            Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

            $output = [
                'success' => 0,
                'msg' => __('messages.something_went_wrong')
            ];
        }

        return redirect('property/properties')->with('status', $output);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (!auth()->user()->can('expense.delete')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            try {
                $business_id = request()->session()->get('user.business_id');

                $expense = Transaction::where('business_id', $business_id)
                    ->where('type', 'expense')
                    ->where('id', $id)
                    ->first();
                $expense->delete();

                //Delete account transactions
                AccountTransaction::where('transaction_id', $expense->id)->delete();
                ContactLedger::where('transaction_id', $expense->id)->delete();

                $output = [
                    'success' => true,
                    'msg' => __("expense.expense_delete_success")
                ];
            } catch (\Exception $e) {
                \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

                $output = [
                    'success' => false,
                    'msg' => __("messages.something_went_wrong")
                ];
            }

            return $output;
        }
    }

    public function getPaymentMethodByLocationDropDown($location_id)
    {
        $payment_methods = $this->transactionUtil->payment_types($location_id, true, true);
        unset($payment_methods['credit_sale']);

        return $this->transactionUtil->createDropdownHtml($payment_methods, 'Please Select');
    }
}
