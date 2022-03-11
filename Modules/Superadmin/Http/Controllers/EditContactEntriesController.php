<?php

namespace Modules\Superadmin\Http\Controllers;

use App\Account;
use App\Business;
use App\Contact;
use App\ContactLedger;
use App\Transaction;
use App\TransactionPayment;
use App\Utils\BusinessUtil;
use App\Utils\ModuleUtil;
use App\Utils\ProductUtil;
use App\Utils\TransactionUtil;
use App\Utils\Util;
use Carbon\Carbon;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Modules\Superadmin\Entities\EditContactEntry;
use Yajra\DataTables\Facades\DataTables;

class EditContactEntriesController extends Controller
{

    /**
     * All Utils instance.
     *
     */
    protected $businessUtil;
    protected $moduleUtil;
    protected $transactionUtil;
    protected $productUtil;
    protected $commonUtil;

    /**
     * Constructor
     *
     * @param ProductUtils $product
     * @return void
     */
    public function __construct(BusinessUtil $businessUtil, ModuleUtil $moduleUtil, TransactionUtil $transactionUtil, ProductUtil $productUtil, Util $commonUtil)
    {
        $this->transactionUtil = $transactionUtil;
        $this->businessUtil = $businessUtil;
        $this->productUtil = $productUtil;
        $this->moduleUtil = $moduleUtil;
        $this->commonUtil = $commonUtil;
    }
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index()
    {
        if (request()->ajax()) {
            $transactions = EditContactEntry::leftjoin('business', 'edit_contact_entries.business_id', 'business.id')
                ->leftjoin('contacts', 'edit_contact_entries.contact_id', 'contacts.id')
                ->select(
                    'business.name as business_name',
                    'contacts.name as contact_name',
                    'edit_contact_entries.*'
                );


            $business_details = Business::find(1);
            $currency_precision =  !empty($business_details) && !empty($business_details->currency_precision) ? $business_details->currency_precision : config('constants.currency_precision', 2);
            return DataTables::of($transactions)
                ->editColumn('orignal_amount', function ($row) use ($business_details, $currency_precision) {
                    return  '<span class="display_currency orignal_amount" data-currency_symbol=false data-orig-value="' . $row->orignal_amount . '" >' . $row->orignal_amount . '</span>';
                })
                ->editColumn('edited_amount', function ($row) use ($business_details, $currency_precision) {
                    return  '<span class="display_currency edited_amount" data-currency_symbol=false data-orig-value="' . $row->edited_amount . '" >' . $row->edited_amount . '</span>';
                })
                ->editColumn('date_and_time', '{{@format_datetime($date_and_time)}}')
                ->editColumn('action_type', '{{ucfirst($action_type)}}')

                ->rawColumns([
                    'orignal_amount',
                    'edited_amount',
                    'date_and_time',
                ])->make(true);
        }
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        return view('superadmin::create');
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {
        return view('superadmin::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        $contact_transaction = ContactLedger::findOrFail($id);
        $transaction = Transaction::find($contact_transaction->transaction_id);
        $business_id = $transaction->business_id;
        $contact_access = 1;

        return view('superadmin::superadmin_settings.partials.edit_contact_entries_modal')->with(compact('contact_transaction', 'business_id'));
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(Request $request, $id)
    {
        try {
            $input = request()->except('_token', '_method');
            $contact_ledger = ContactLedger::findOrFail($id);
            $orignal_amount = $contact_ledger->amount;
            DB::beginTransaction();
            ContactLedger::where('id', $id)->update(['amount' => $input['amount']]);

            if (!empty($contact_ledger->transaction_payment_id)) {
                $contact_ledger = ContactLedger::where('transaction_id', $contact_ledger->transaction_id)->where('transaction_payment_id', $contact_ledger->transaction_payment_id)->update(['amount' => $input['amount']]);
            }

            $entry_data = [
                'business_id' => $input['business_id'],
                'account_id' => $input['account_id'],
                'contact_id' => $input['contact_id'],
                'contact_ledger_id' => $input['contact_ledger_id'],
                'date_and_time' => Carbon::now(),
                'orignal_amount' => $orignal_amount,
                'edited_amount' => $input['amount'],
                'action_type' => 'edited'
            ];
            EditContactEntry::create($entry_data);
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
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy($id)
    {
        try {
            $contact_ledger = ContactLedger::findOrFail($id);
            $orignal_amount = $contact_ledger->amount;
            DB::beginTransaction();
            $business_id = request()->business_id;
            $contact_id = request()->contact_id;

            $entry_data = [
                'business_id' => $business_id,
                'account_id' => null,
                'contact_id' => $contact_id,
                'contact_ledger_id' => $id,
                'date_and_time' => Carbon::now(),
                'orignal_amount' => $orignal_amount,
                'edited_amount' => $orignal_amount,
                'action_type' => 'deleted'
            ];
            EditContactEntry::create($entry_data);

            $contact_ledger->forcedelete();

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

        return $output;
    }

    public function getContactDropdownByBusiness($id, $type)
    {
        $contacts = Contact::where('business_id', $id)->where('type', $type)->pluck('name', 'id');

        return $this->transactionUtil->createDropdownHtml($contacts, 'Please Select');
    }


    public function getLedger()
    {

        $contact_id = request()->contact_id;
        $contact = Contact::find($contact_id);
        $business_id = request()->business_id;
        $business_details = Business::find($business_id);

        $start_date = request()->start_date;
        $end_date =  request()->end_date;
        $transaction_type =  request()->transaction_type;
        $transaction_amount =  request()->transaction_amount;

        if (!empty($contact)) {


            if ($contact->type == 'supplier') {
                $query = ContactLedger::leftjoin('transactions', 'contact_ledgers.transaction_id', 'transactions.id')
                    ->leftjoin('transaction_payments', 'contact_ledgers.transaction_payment_id', 'transaction_payments.id')
                    ->leftjoin('business_locations', 'transactions.location_id', 'business_locations.id')
                    ->leftjoin('account_transactions', 'transactions.id', 'account_transactions.transaction_id')
                    ->leftjoin('accounts', 'account_transactions.account_id', 'accounts.id')
                    ->where('transactions.contact_id', $contact_id)
                    ->where('transactions.business_id', $business_id)
                    ->select(
                        'contact_ledgers.*',
                        'contact_ledgers.type as acc_transaction_type',
                        'business_locations.name as location_name',
                        'transactions.ref_no',
                        'transactions.invoice_no',
                        'transactions.transaction_date',
                        'transactions.payment_status',
                        'transactions.pay_term_number',
                        'transactions.pay_term_type',
                        'transaction_payments.method as payment_method',
                        'transaction_payments.bank_name',
                        'transaction_payments.cheque_date',
                        'transaction_payments.cheque_number',
                        'transactions.type as transaction_type',
                        'accounts.account_number',
                        'accounts.name as account_name'
                    )->groupBy('contact_ledgers.id')->orderBy('contact_ledgers.id', 'asc');;
            }

            if ($contact->type == 'customer') {
                $query = ContactLedger::leftjoin('transactions', 'contact_ledgers.transaction_id', 'transactions.id')
                    ->leftjoin('business_locations', 'transactions.location_id', 'business_locations.id')
                    ->leftjoin('transaction_payments', 'contact_ledgers.transaction_payment_id', 'transaction_payments.id')
                    // ->where('transactions.contact_id', $contact_id)
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
                        'transaction_payments.id as tp_id',
                        'transaction_payments.method as payment_method',
                        'transaction_payments.transaction_id as tp_transaction_id',
                        'transaction_payments.paid_on',
                        'transaction_payments.bank_name',
                        'transaction_payments.cheque_date',
                        'transaction_payments.cheque_number',
                        DB::raw("(select
                    sum(`bc_cl`.`amount`)
                    from `contact_ledgers` bc_cl left join `transactions` bc_t on `bc_cl`.`transaction_id` = `bc_t`.`id`
                   left join `business_locations` bc_bl on `bc_t`.`location_id` = `bc_bl`.`id`
                   where `bc_cl`.`contact_id` =  `contact_ledgers`.`contact_id`
                   and `bc_cl`.`type` = 'credit'
                   and `bc_t`.`business_id` = `transactions`.`business_id`
                   and `bc_cl`.`id`  <= `contact_ledgers`.`id`
                   group by `bc_cl`.`id` and `bc_cl`.`contact_id`) as balance_credit"),
                        DB::raw("(select
                    sum(`cl`.`amount`)
                   from `contact_ledgers` cl left join `transactions` t on `cl`.`transaction_id` = `t`.`id`
                   left join `business_locations` bl on `t`.`location_id` = `bl`.`id`
                   where `cl`.`contact_id` =  `contact_ledgers`.`contact_id`
                   and `cl`.`type` = 'debit'
                   and `t`.`business_id` = `transactions`.`business_id`
                   and `cl`.`id`  <= `contact_ledgers`.`id`
                   group by `cl`.`id` and `cl`.`contact_id`) as balance_debit"),
                        DB::raw("(select(IFNULL(balance_debit,0) - IFNULL(balance_credit,0)) ) as balance"),
                    )->groupBy('contact_ledgers.id')->orderBy('contact_ledgers.id', 'asc');
            }
            if (!empty($start_date)  && !empty($end_date)) {
                $query->whereDate('contact_ledgers.operation_date', '>=', $start_date);
                $query->whereDate('contact_ledgers.operation_date', '<=', $end_date);
            }
            if (!empty($transaction_type)) { // debit / credit type filter
                $query->where('contact_ledgers.type', $transaction_type);
            }
            if (!empty($transaction_amount)) {
                $query->where('contact_ledgers.amount', $transaction_amount);
            }
            if (!empty($contact_id)) {
                $query->where('transactions.contact_id', $contact_id);
            }
            $query->orderby('contact_ledgers.operation_date');
        } else {
            $query = [];
        }
        Session::put('balance', 0);

        return DataTables::of($query)
            ->editColumn('operation_date', '{{@format_date($operation_date)}}')
            ->addColumn('debit', function ($row) {
                if ($row->acc_transaction_type == 'debit') {
                    return  '<span class="display_currency debit_col" data-currency_symbol=false data-orig-value="' . $row->amount . '" >' . $row->amount . '</span>';
                }
                return '';
            })
            ->addColumn('credit', function ($row) {
                if ($row->acc_transaction_type == 'credit') {
                    return  '<span class="display_currency credit_col" data-currency_symbol=false data-orig-value="' . $row->amount . '" >' . $row->amount . '</span>';
                }
                return '';
            })
            ->editColumn('cheque_number', function ($row) use ($business_details) {
                if (!empty($row->dep_trans_cheque_number)) {
                    return $row->dep_trans_cheque_number;
                }
                if ($row->sub_type == 'deposit') {
                    $tp = TransactionPayment::find($row->tp_id);
                    if (!empty($tp)) {
                        return $tp->cheque_number;
                    } else {
                        return '';
                    }
                }
                return $row->cheque_number;
            })
            ->editColumn('balance', function ($row) use ($business_details) {

                $balance = Session::get('balance');
                if ($row->acc_transaction_type == 'credit') {
                    $balance = $balance -  $row->amount;
                }
                if ($row->acc_transaction_type == 'debit') {
                    $balance = $balance +  $row->amount;
                }
                Session::put('balance', $balance);
                return '<span class="" data-currency_symbol="false">' . $this->productUtil->num_f($balance, false, $business_details, true) . '</span>';
            })
            ->editColumn('type', function ($row) use ($business_details) {
                $html = '';
                if ($row->transaction_type == 'purchase') {
                    $html = __('lang_v1.purchase');
                } else if ($row->transaction_type == 'opening_balance') {
                    $html = __('lang_v1.opening_balance');
                } else if ($row->transaction_type == 'sell') {
                    $html = __('lang_v1.sell');
                } else if ($row->transaction_type == 'sell_return') {
                    $html = __('lang_v1.sell_return');
                }
                return $html;
            })
            ->editColumn(
                'payment_status',
                function ($row) {
                    $payment_status = Transaction::getPaymentStatus($row);
                    return (string) view('sell.partials.payment_status', ['payment_status' => $payment_status, 'id' => $row->id, 'for_purchase' => true]);
                }
            )
            ->editColumn('payment_method', function ($row) {
                $html = '';
                if ($row->payment_status == 'due' && $row->transaction_type == 'purchase') {
                    return 'Credit Purchase';
                }
                if ($row->payment_status == 'due' && $row->transaction_type == 'sell') {
                    return 'Credit Sell';
                }
                if ($row->payment_method == 'bank_transfer') {
                    $bank_acccount = Account::find($row->account_id);
                    if (!empty($bank_acccount)) {
                        $html .= '<b>Bank Name:</b> ' . $bank_acccount->name . '</br>';
                        $html .= '<b>Account Number:</b> ' . $bank_acccount->account_number . '</br>';
                    }
                } else {
                    $html .= ucfirst($row->payment_method);
                }

                return $html;
            })
            ->addColumn('action', function ($row) use ($business_id, $contact_id) {
                $html = '';
                $html = '<div class="btn-group">
                            <button type="button" class="btn btn-info dropdown-toggle btn-xs" 
                                data-toggle="dropdown" aria-expanded="false">' .
                    __("messages.actions") .
                    '<span class="caret"></span><span class="sr-only">Toggle Dropdown
                                </span>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-left" role="menu">';

                if (auth()->user()->can('superadmin')) {
                    $html .= '<li><a data-href="' . action('\Modules\Superadmin\Http\Controllers\EditContactEntriesController@edit', [$row->id]) . '" data-container=".view_modal" class="btn-modal edit_at_button"><i class="glyphicon glyphicon-edit"></i> ' . __("messages.edit") . '</a></li>';
                }
                $html .= '<li><a data-href="' . action('\Modules\Superadmin\Http\Controllers\EditContactEntriesController@destroy', [$row->id]) . '?business_id=' . $business_id . '&contact_id=' . $contact_id . '" class="delete_account_transaction"><i class="fa fa-trash"></i> ' . __("messages.delete") . '</a></li>';

                $html .= '</ul></div>';
                return $html;
            })

            ->removeColumn('id')
            ->removeColumn('is_closed')
            ->rawColumns(['credit', 'debit', 'balance', 'sub_type', 'action', 'payment_status', 'payment_method'])
            ->make(true);
    }
}
