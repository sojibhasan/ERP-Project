<?php

namespace Modules\Superadmin\Http\Controllers;

use App\Account;
use App\AccountGroup;
use App\AccountTransaction;
use App\Business;
use App\Contact;
use App\ContactLedger;
use App\Journal;
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
use Modules\Petro\Entities\PumpOperator;
use Modules\Petro\Entities\SettlementExpensePayment;
use Modules\Superadmin\Entities\EditAccountEntry;
use Yajra\DataTables\Facades\DataTables;

class EditAccountEntriesController extends Controller
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
        $business_id = request()->business_id;
        $id = request()->account_id;
        $account_access = 1;

        $card_account_id = $this->transactionUtil->account_exist_return_id('Cards (Credit Debit) Account');
        $cheque_return_account_id = $this->transactionUtil->account_exist_return_id('Cheque Return Income');
        $card_group_id = AccountGroup::getGroupByName('Card', true);
        $bank_group_id = AccountGroup::getGroupByName('Bank Account', true);
        $cheque_in_hand_group_id = AccountGroup::getGroupByName("Cheques in Hand (Customer's)", true);
        $card_type_accounts = Account::where('business_id', $business_id)->where('asset_type', $card_group_id)->where(DB::raw("REPLACE(`name`, '  ', ' ')"), '!=', 'Cards (Credit Debit) Account')->pluck('name', 'id');
        $cheque_numbers = Transaction::chequeNumberDropDown('sell');


        if (request()->ajax()) {
            $start_date = request()->input('start_date');
            $end_date = request()->input('end_date');
            Session::forget('account_balance'); // forget value if previously store in it
            $acount_balance_pre = Account::getAccountBalance($id, $start_date, $end_date, true, true);
            Session::put('account_balance', $acount_balance_pre);

            $accounts = AccountTransaction::join(
                'accounts as A',
                'account_transactions.account_id',
                '=',
                'A.id'
            )
                ->leftJoin('users AS u', 'account_transactions.created_by', '=', 'u.id')
                ->leftjoin(
                    'account_types as ats',
                    'A.account_type_id',
                    '=',
                    'ats.id'
                )
                ->leftJoin('transaction_payments AS TP', 'account_transactions.transaction_payment_id', '=', 'TP.id')
                ->where('A.business_id', $business_id)
                ->where('A.id', $id)
                // ->where('updated_by', null)
                ->with(['transaction', 'transaction.contact', 'transfer_transaction'])
                ->select([
                    'type',
                    'account_transactions.amount',
                    'account_transactions.reconcile_status',
                    'account_transactions.sub_type as at_sub_type',
                    'operation_date', 'account_transactions.note',
                    'journal_deleted',
                    'deleted_by',
                    'journal_entry',
                    'account_transactions.transaction_sell_line_id',
                    'account_transactions.income_type',
                    'account_transactions.attachment',
                    'account_transactions.cheque_number as dep_trans_cheque_number',
                    'account_transactions.transaction_payment_id as tp_id',
                    'TP.cheque_number', 'TP.bank_name', 'TP.cheque_date',
                    'TP.card_type',
                    'TP.method',
                    'TP.paid_on',
                    'TP.account_id as bank_account_id',
                    'updated_type',
                    'updated_by',
                    'account_transactions.updated_at',
                    'A.name as account_name',
                    'sub_type',
                    'transfer_transaction_id',
                    'ats.name as account_type_name',
                    'account_transactions.transaction_id',
                    'account_transactions.id',
                    DB::raw("CONCAT(COALESCE(u.surname, ''),' ',COALESCE(u.first_name, ''),' ',COALESCE(u.last_name,'')) as added_by")
                ])->withTrashed()
                ->groupBy('account_transactions.id')
                ->orderBy('account_transactions.operation_date', 'asc');
            if (!empty(request()->input('type'))) {
                $accounts->where('type', request()->input('type'));
            }
            if (!empty(request()->input('card_type'))) {
                $accounts->where('TP.card_type', request()->input('card_type'));
            }

            if (!empty(request()->input('transaction_type'))) {
                $accounts->where('account_transactions.sub_type', request()->input('transaction_type'));
            }
            if (!empty(request()->input('debit_credit'))) {
                $accounts->where('account_transactions.type', request()->input('debit_credit'));
            }

            if (!empty($start_date) && !empty($end_date)) {
                $accounts->whereBetween(DB::raw('date(operation_date)'), [$start_date, $end_date]);
            }

            $business_details = Business::find($business_id);
            $currency_precision =  !empty($business_details) && !empty($business_details->currency_precision) ? $business_details->currency_precision : config('constants.currency_precision', 2);
            return DataTables::of($accounts)
                ->addColumn('debit', function ($row) use ($business_details, $currency_precision) {
                    if ($row->type == 'debit') {
                        return  '<span class="display_currency debit_col" data-currency_symbol=false data-orig-value="' . $row->amount . '" >' . $row->amount . '</span>';
                    }
                    return '';
                })
                ->addColumn('credit', function ($row) use ($business_details, $currency_precision) {
                    if ($row->type == 'credit') {
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
                    if (strpos($row->account_type_name, "Assets") !== false || strpos($row->account_type_name, "Expenses") !== false) {
                        $balance = Session::get('account_balance');
                        if ($row->type == 'credit') {
                            $balance = $balance -  $row->amount;
                        }
                        if ($row->type == 'debit') {
                            $balance = $balance +  $row->amount;
                        }
                        Session::put('account_balance', $balance);
                        return '<span class="display_currency" data-currency_symbol="true">' . $this->productUtil->num_f($balance, false, $business_details, true) . '</span>';
                    } else if (strpos($row->account_type_name, "Income")  || strpos($row->account_type_name, "Equity") || strpos($row->account_type_name, "Liabilities")) {
                        $balance = Session::get('account_balance');
                        if ($row->type == 'credit') {
                            $balance = $balance +  $row->amount;
                        }
                        if ($row->type == 'debit') {
                            $balance = $balance - $row->amount;
                        }
                        Session::put('account_balance', $balance);
                        return '<span class="display_currency" data-currency_symbol="true">' . $this->productUtil->num_f($balance, false, $business_details, true) . '</span>';
                    }
                })
                ->editColumn('operation_date', function ($row) {
                    if (empty($row->journal_entry)) {
                        if ($row->account_name == 'Finished Goods Account' || $row->account_name == 'Raw Material Account' || $row->account_name == 'Stock Account') {
                            if(!empty($row->transaction->transaction_date)){
                                return  $this->commonUtil->format_date($row->transaction->transaction_date, false);;
                            }
                        }
                    }
                    if (!empty($row->tp_id)) {
                        $tp = TransactionPayment::find($row->tp_id);
                        if (!empty($tp)) {
                            return $this->commonUtil->format_date($tp->paid_on, false);
                        }
                    }

                    return $this->commonUtil->format_date($row->operation_date, false);
                })
                ->addColumn('description', function ($row) {
                    $details = '';
                    if (empty($row->transaction->pump_operator_id) ||  ($row->transaction->sub_type == 'credit_sale') || $row->transaction->sub_type == 'expense' || $row->transaction->sub_type == 'settlement') {
                        if ($row->journal_deleted == 0) {
                            if (in_array($row->sub_type, ['fund_transfer', 'deposit', 'payable', 'stock', 'opening_balance'])) {
                                if (in_array($row->sub_type, ['deposit']) && !empty($row->transfer_transaction)) {
                                    $details = __('account.' . $row->sub_type);
                                    if ($row->type == 'credit') {
                                        $details .= ' ( ' . __('account.to') . ': ' . $row->transfer_transaction->account->name . ')';
                                    } else {
                                        $details .= ' ( ' . __('account.from') . ': ' . $row->transfer_transaction->account->name . ')';
                                    }
                                    $details .= '<br>' . __('cheque.cheque_number') . ': ' . $row->dep_trans_cheque_number;
                                }
                                if (in_array($row->sub_type, ['fund_transfer']) && !empty($row->transfer_transaction)) {
                                    $details = __('account.' . $row->sub_type);
                                    if ($row->type == 'credit') {
                                        $details .= ' ( ' . __('account.to') . ': ' . $row->transfer_transaction->account->name . ')';
                                    } else {
                                        $details .= ' ( ' . __('account.from') . ': ' . $row->transfer_transaction->account->name . ')';
                                    }
                                    $details .= '<br>' . __('cheque.cheque_number') . ': ' . $row->dep_trans_cheque_number;
                                }
                                if (in_array($row->sub_type, ['payable', 'stock'])) {
                                    $details = '<b>' . __('purchase.supplier') . ':</b> ' . $row->transaction->contact->name . '<br><b>' .
                                        __('purchase.ref_no') . ':</b> ' . $row->transaction->ref_no;
                                }
                                if (in_array($row->sub_type, ['opening_balance'])) {
                                    $details = '<b>' . __('account.opening_amount_adjusted') . '</b>';
                                }
                            } else {
                                if (!empty($row->transaction->type)) {
                                    if ($row->transaction->type == 'purchase') {
                                        $details = '<b>' . __('lang_v1.purchase') . '</b><br> ' . '<b>' .
                                            __('purchase.supplier') . ':</b> ' . $row->transaction->contact->name . '<br><b>' .
                                            __('purchase.ref_no') . ':</b> ' . $row->transaction->ref_no . '<br><b>';
                                        if ($row->method == 'cheque') {
                                            $details .=    __('cheque.cheque_number') . ':</b> ' . $row->cheque_number . '<br><b>' .
                                                __('cheque.cheque_date') . ':</b> ' . $row->cheque_date;
                                        } else if ($row->method == 'bank_transfer') {
                                            $details .=    __('lang_v1.bank_transfer') . ':</b> <br>' . __('cheque.cheque_number') . ':</b> ' . $row->cheque_number . '<br>' .
                                                __('cheque.cheque_date') . ': ' . $row->cheque_date;
                                        } else {
                                            $details .=   ucfirst($row->method);
                                        }
                                    } else if ($row->transaction->type == 'purchase_return') {
                                        $details = '<b>' . __('lang_v1.purchase_return') . '</b><br> ' . '<b>' .
                                            __('purchase.supplier') . ':</b> ' . $row->transaction->contact->name . '<br><b>' .
                                            __('purchase.ref_no') . ':</b> ' . $row->transaction->ref_no . '<br><b>';
                                        if ($row->method == 'cheque') {
                                            $details .=    __('cheque.cheque_number') . ':</b> ' . $row->cheque_number . '<br><b>' .
                                                __('cheque.cheque_date') . ':</b> ' . $row->cheque_date;
                                        } else {
                                            $details .=   ucfirst($row->method);
                                        }
                                    } else if ($row->transaction->type == 'sell' && $row->transaction->is_settlement != 1) {
                                        if ($row->transaction->is_direct_sale) {
                                            $details = '<b>' . __('lang_v1.invoice_sale') . '</b><br> ';
                                            $details .= '<b>' . __('contact.customer') . ':</b> ' . $row->transaction->contact->name . '<br><b>';
                                        } else {
                                            $details = '<b>' . __('lang_v1.pos_sale') . '</b><br> ';
                                            $details .= '<b>' . __('contact.customer') . ':</b> ' . $row->transaction->contact->name . '<br><b>';
                                        }
                                        if ($row->transaction->is_settlement != 1) {
                                            $details .= __('sale.invoice_no') . ':</b> ' . $row->transaction->invoice_no;
                                        }
                                    } elseif ($row->transaction->type == 'opening_stock' && $row->imported == 1) {
                                        $details = 'Opening Stock <br> <b>Date:</b> ' . $row->transaction->transaction_date;
                                    } elseif ($row->transaction->type == 'expense') {
                                        $details = 'Expense <br> <b>Ref:</b> ' . $row->transaction->ref_no;
                                    } elseif ($row->transaction->type == 'opening_balance') {
                                        $contact = Contact::where('id', $row->transaction->contact_id)->first();
                                        if ($contact->type == 'supplier') {
                                            $details = '<b>' . __('purchase.supplier') . ':</b> ' . $row->transaction->contact->name . '<br><b>' . 'Opening Balance <br> </b>' . __('purchase.ref_no') . ': ' . $row->transaction->ref_no . '<br>';
                                        }
                                        if ($contact->type == 'customer') {
                                            $details = '<b>' . __('contact.customer') . ':</b> ' . $row->transaction->contact->name . '<br><b>' . 'Opening Balance <br> </b>' . __('purchase.ref_no') . ': ' . $row->transaction->ref_no . '<br>';
                                        }
                                        if ($row->method == 'cheque') {
                                            $details .=    __('cheque.cheque_number') . ':</b> ' . $row->cheque_number . '<br><b>' .
                                                __('cheque.cheque_date') . ':</b> ' . $row->cheque_date;
                                        } else if ($row->method == 'bank_transfer') {
                                            $bank_account = null;
                                            if (!empty($row->bank_account_id)) {
                                                $bank_account = Account::where('id', $row->bank_account_id)->first();
                                            }
                                            $details .=    __('lang_v1.bank_transfer') . ':</b> <br>' . __('cheque.cheque_number') . ':</b> ' . $row->cheque_number . '<br>' .
                                                __('cheque.cheque_date') . ': ' . $row->cheque_date;
                                            if (empty($bank_account)) {
                                                $details .= '<br><b>Bank:</b>' . $bank_account->name;
                                            }
                                        } else {
                                            $details .=   ucfirst($row->method);
                                        }
                                    } elseif ($row->transaction->type == 'opening_stock') {
                                        $details = 'Stock adjustment - Opening Stock <br> <b>Date:</b> ' . $row->transaction->transaction_date;
                                    } elseif ($row->transaction->type == 'stock_adjustment') {
                                        $details = 'Stock adjusted <br> <b>Date:</b> ' . $row->transaction->transaction_date;
                                    } elseif ($row->transaction->type == 'settlement' && $row->transaction->sub_type == 'expense') {
                                        $details = 'Expense <br> <b>' . 'Settlement No: ' . '</b>' . $row->transaction->invoice_no;
                                        $ref = '';
                                        if ($row->transaction->is_settlement) {
                                            $settlement_expense = SettlementExpensePayment::where('transaction_id', $row->transaction->id)->first();
                                            if (!empty($settlement_expense)) {
                                                $ref .= '<br><b>Reference No: </b>' . $settlement_expense->reference_no . '<br><b>Reason: </b>' . $settlement_expense->reason;
                                            }
                                        }
                                        $details .= $ref;
                                    } else if ($row->transaction->is_settlement == 1) {
                                        $transaction_payment = null;
                                        $this_tp = null;
                                        $details = '<b>' . 'Settlement No: ' . '</b>' . $row->transaction->invoice_no;
                                        $transaction_payment = TransactionPayment::where('id', $row->tp_id)->first();
                                        if ($row->transaction->type == 'sell' && $row->transaction->sub_type == 'credit_sale' && $row->type == 'debit') {
                                            $transaction_payment = TransactionPayment::where('transaction_id', $row->transaction_id)->first();
                                            $details .= '<br>Credit Sale <br><b> Customer: </b> ' . $row->transaction->contact->name;
                                        } elseif ($row->transaction->type == 'sell' && $row->transaction->sub_type == 'credit_sale' && $row->type == 'credit') {
                                            $details .= '<br> Credit Payment <br><b> Customer: </b> ' . $row->transaction->contact->name;
                                        } elseif (!empty($transaction_payment) && $row->transaction->is_credit_sale == 0 && $transaction_payment->method == 'cash') {
                                            $this_tp = Transaction::leftjoin('contacts', 'transactions.contact_id', 'contacts.id')->where('transactions.type', 'settlement')->where('transactions.sub_type', 'cash_payment')->where('final_total', $transaction_payment->amount)->where('invoice_no', $row->transaction->invoice_no)->first();
                                            $details .= '</br>Cash Payment';
                                            if (!empty($this_tp)) {
                                                $details .= '<br><b>Customer:</b> ' .  $this_tp->name;
                                            }
                                        } elseif (!empty($transaction_payment) && $row->transaction->is_credit_sale == 0 &&  $transaction_payment->method == 'card' && $row->type == 'debit') {
                                            $this_tp = Transaction::leftjoin('contacts', 'transactions.contact_id', 'contacts.id')->where('transactions.type', 'settlement')->where('transactions.sub_type', 'card_payment')->where('final_total', $transaction_payment->amount)->where('invoice_no', $row->transaction->invoice_no)->first();
                                            $details .= '</br>Card Sale ';
                                            if (!empty($this_tp)) {
                                                $details .= '<br><b>Customer:</b> ' .  $this_tp->name;
                                            }
                                        } elseif (!empty($transaction_payment) && $row->transaction->is_credit_sale == 0 &&  $transaction_payment->method == 'card' && $row->type == 'credit') {
                                            $this_tp = Transaction::leftjoin('contacts', 'transactions.contact_id', 'contacts.id')->where('transactions.type', 'settlement')->where('transactions.sub_type', 'card_payment')->where('final_total', $transaction_payment->amount)->where('invoice_no', $row->transaction->invoice_no)->first();
                                            $details .= '</br>Card Payment ';
                                            if (!empty($this_tp)) {
                                                $details .= '<br><b>Customer:</b> ' .  $this_tp->name;
                                            }
                                        } elseif (!empty($transaction_payment) && $row->transaction->is_credit_sale == 0 &&  $transaction_payment->method == 'cheque' && $row->type == 'debit') {
                                            $details .= '</br>Cheque Payment <br> <b>Bank:</b> ' . $transaction_payment->bank_name . '<b> Cheque No: </b>' . $transaction_payment->cheque_number . '  <b>Cheque Date: </b>' . $transaction_payment->cheque_date;
                                            $this_tp = Transaction::leftjoin('contacts', 'transactions.contact_id', 'contacts.id')->where('transactions.type', 'settlement')->where('transactions.sub_type', 'cheque_payment')->where('final_total', $transaction_payment->amount)->where('invoice_no', $row->transaction->invoice_no)->first();
                                            if (!empty($this_tp)) {
                                                $details .= '<br><b>Customer:</b> ' .  $this_tp->name;
                                            }
                                        }
                                    } elseif ($row->transaction->type == 'advance_payment') {
                                        if ($row->transaction->contact->type == 'customer') {
                                            $details = '<b>' . 'Advance Payment done by ' . '</b>' . $row->transaction->contact->name;
                                        }
                                        if ($row->transaction->contact->type == 'supplier') {
                                            $details = '<b>' . 'Advance Payment done to ' . '</b>' . $row->transaction->contact->name;
                                        }
                                    } elseif ($row->transaction->type == 'security_deposit') {
                                        if ($row->transaction->contact->type == 'customer') {
                                            $details = '<b>' . 'Security Deposit – Customer ' . '</b>' . $row->transaction->contact->name . '<br><b> Payment Ref No.</b> ' . $row->transaction->ref_no;
                                        }
                                        if ($row->transaction->contact->type == 'supplier') {
                                            $details = '<b>' . 'Security Deposit – Supplier ' . '</b>' . $row->transaction->contact->name . '<br><b> Payment Ref No.</b> ' . $row->transaction->ref_no;
                                        }
                                    } elseif ($row->transaction->type == 'refund_security_deposit') {
                                        if ($row->transaction->contact->type == 'customer') {
                                            $details = '<b>' . 'Refund Security Deposit – Customer ' . '</b>' . $row->transaction->contact->name . '<br><b> Payment Ref No.</b> ' . $row->transaction->ref_no;
                                        }
                                        if ($row->transaction->contact->type == 'supplier') {
                                            $details = '<b>' . 'Refund Security Deposit – Supplier ' . '</b>' . $row->transaction->contact->name . '<br><b> Payment Ref No.</b> ' . $row->transaction->ref_no;
                                        }
                                    } elseif ($row->transaction->type == 'refund') {
                                        $details = __("lang_v1.refund") . ':' . $row->transaction->ref_no . ' <br> <b> ' . __("lang_v1.invoice_no") . ':' . '</b>' . $row->transaction->invoice_no;
                                    } elseif ($row->transaction->type == 'cheque_return' && $row->at_sub_type == 'cheque_return_charges') {
                                        $details = __("lang_v1.cheque_return_charges") . ':' . $row->transaction->ref_no;
                                        $details .= '<br><b>' . __("lang_v1.bank_name") . ': </b> ' . $row->bank_name . ' <b> ' . __("lang_v1.cheque_no") . ': </b> ' . $row->cheque_number . ' <b> ' . __("lang_v1.cheque_date") . ': </b> ' . $row->cheque_date;
                                    } elseif ($row->transaction->type == 'cheque_return' && $row->at_sub_type != 'cheque_return_charges') {
                                        $details = __("lang_v1.cheque_return") . ':' . $row->transaction->ref_no;
                                        $details .= '<br><b>' . __("lang_v1.bank_name") . ': </b> ' . $row->bank_name . ' <br><b> ' . __("lang_v1.cheque_no") . ': </b> ' . $row->cheque_number . ' <br><b> ' . __("lang_v1.cheque_date") . ': </b> ' . $row->cheque_date;
                                    } elseif ($row->transaction->type == 'property_sell') {
                                        $details = __("lang_v1.sell");
                                        $details .= '<br>' . __('lang_v1.invoice_no') . ': <b>' . $row->transaction->invoice_no . '</b>';
                                        if (!empty($row->income_type))
                                            $details .= '<br><b>' . ucfirst($row->income_type) . '</b>';
                                    }
                                    if (!empty($row->deleted_by)) {
                                        $details .= '<br><b>' . __('lang_v1.deleted') . '<b>';
                                    }
                                }
                                if (!empty($row->journal_entry)) {
                                    $journal_id = Journal::where('id', $row->journal_entry)->first()->journal_id;
                                    $details = 'Journal Entry No. ' . $journal_id;
                                }
                            }
                        } else {
                            $journal_id = Journal::where('id', $row->journal_entry)->first()->journal_id;
                            $details = 'Journal Entry No. ' . $journal_id . ' Deleted ';
                        }
                    } else {
                        $pump_operator = PumpOperator::findOrFail($row->transaction->pump_operator_id);
                        if ($row->transaction->type == 'opening_balance') {
                            $details = '<b>' . __('petro::lang.pump_operator') . ': ' . $pump_operator->name . '</b> <br><b>' . 'Opening Balance <br> </b>' . __('purchase.ref_no') . ': ' . $row->transaction->ref_no;
                        } else if ($row->transaction->type == 'settlement' && $row->transaction->sub_type == 'shortage') {
                            $details = '<b>Settlement No: ' . $row->transaction->invoice_no . '</b> Pump Operator: ' . $pump_operator->name . ' <br><b>Shortage</b>';
                        } else if ($row->transaction->type == 'settlement' && $row->transaction->sub_type == 'excess') {
                            $details = '<b>Settlement No: ' . $row->transaction->invoice_no . '</b> Pump Operator: ' . $pump_operator->name . ' <br><b>Excess</b>';
                        } else if ($row->transaction->type == 'sell' && $row->transaction->is_settlement == '1') {
                            $contact = Contact::findOrFail(ContactLedger::where('transaction_id', $row->transaction->id)->first()->contact_id);

                            $details = '<b>Settlement No: ' . $row->transaction->invoice_no . '</b> <br><b>' . __('contact.customer') . ':</b> ' . $contact->name;
                        }
                    }

                    return $details;
                })
                ->addColumn('action', function ($row) use ($business_id, $id) {
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
                        $html .= '<li><a data-href="' . action('\Modules\Superadmin\Http\Controllers\EditAccountEntriesController@editAccountTransaction', ['transaction_id' => $row->id, 'business_id' => $business_id]) . '" data-container=".view_modal" class="btn-modal edit_at_button"><i class="glyphicon glyphicon-edit"></i> ' . __("messages.edit") . '</a></li>';
                    }
                    $html .= '<li><a data-href="' . action('\Modules\Superadmin\Http\Controllers\EditAccountEntriesController@destroy', [$row->id]) . '?business_id=' . $business_id . '&account_id=' . $id . '" class="delete_account_transaction"><i class="fa fa-trash"></i> ' . __("messages.delete") . '</a></li>';

                    $html .= '</ul></div>';
                    return $html;
                })
                ->editColumn('attachment', function ($row) {
                    $action = '';
                    if (!empty($row->attachment)) {
                        if (strpos($row->attachment, 'jpg') || strpos($row->attachment, 'jpeg') || strpos($row->attachment, 'png')) {
                            $action = '<a href="#"
                            data-href="' . action("AccountController@imageModal", ["title" => "View", "url" => url($row->attachment)]) . '"
                            class="btn-modal btn-xs btn btn-primary"
                            data-container=".view_modal">' . __("messages.view") . '</a>';
                        } else {
                            $action = '<a class="btn btn-default btn-xs" href="' . url($row->attachment) . '"><i class="fa fa-donwload"></i> ' . __('lang_v1.download') . '</a>';
                        }
                    }
                    return $action;
                })
                ->editColumn('reconcile_status', function ($row) {
                    $html = '';
                    if (auth()->user()->can('account.reconcile')) {
                        if ($row->reconcile_status == 0) {
                            $html = '<button type="button" class="btn btn-xs reconcile_status_btn" style="background: #FEA61E; color:#fff;" data-href="' . action('AccountController@reconcile', [$row->id]) . '"><i class="fa fa-times"></i> ' . __('account.reconcile') . '</button>';
                        } else {
                            $html = '<button type="button" class="btn btn-xs reconcile_status_btn" style="background: #3AEC05; color:#fff;" data-href="' . action('AccountController@reconcile', [$row->id]) . '"><i class="fa fa-check"></i> ' . __('account.reconciled') . '</button>';
                        }
                    }
                    return $html;
                })
                ->editColumn('note', function ($row) use ($card_account_id, $id) {
                    $html = $row->note;
                    if ($id == $card_account_id) {
                        $card_type = TransactionPayment::leftjoin('account_transactions', 'transaction_payments.id', 'account_transactions.transaction_payment_id')
                            ->leftjoin('accounts', 'transaction_payments.card_type', 'accounts.id')
                            ->where('transaction_payments.id', $row->tp_id)->select('accounts.name', 'accounts.id')->first();
                        if (!empty($card_type)) {
                            $html = $card_type->name;
                        }
                    }
                    return $html;
                })
                ->removeColumn('id')
                ->removeColumn('is_closed')
                ->rawColumns(['credit', 'debit', 'balance', 'sub_type', 'action', 'attachment', 'reconcile_status', 'description'])
                ->make(true);
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
        return view('superadmin::edit');
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
            $account_transaction = AccountTransaction::findOrFail($id);
            $orignal_amount = $account_transaction->amount;
            DB::beginTransaction();
            AccountTransaction::where('id', $id)->update(['amount' => $input['amount']]);

            $contact_ledger = ContactLedger::where('transaction_id', $account_transaction->transaction_id)->where('transaction_payment_id', $account_transaction->transaction_payment_id)->update(['amount' => $input['amount']]);

            $entry_data = [
                'business_id' => $input['business_id'],
                'account_id' => $input['account_id'],
                'account_transaction_id' => $input['account_transaction_id'],
                'date_and_time' => Carbon::now(),
                'orignal_amount' => $orignal_amount,
                'edited_amount' => $input['amount'],
                'action_type' => 'edited'
            ];
            EditAccountEntry::create($entry_data);
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
            $account_transaction = AccountTransaction::findOrFail($id);
            $orignal_amount = $account_transaction->amount;
            DB::beginTransaction();
            $business_id = request()->business_id;
            $account_id = request()->account_id;

            $entry_data = [
                'business_id' => $business_id,
                'account_id' => $account_id,
                'account_transaction_id' => $id,
                'date_and_time' => Carbon::now(),
                'orignal_amount' => $orignal_amount,
                'edited_amount' => $orignal_amount,
                'action_type' => 'deleted'
            ];
            EditAccountEntry::create($entry_data);

            $account_transaction->forcedelete();

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
    public function getAccountDropdownByBusiness($id)
    {
        $accounts = Account::where('business_id', $id)->pluck('name', 'id');
        return $this->transactionUtil->createDropdownHtml($accounts, 'Please Select');
    }

    public function editAccountTransaction($transaction_id, $business_id)
    {
        $account_transaction = AccountTransaction::findOrFail($transaction_id);
        $account_access = 1;

        return view('superadmin::superadmin_settings.partials.edit_account_entries_modal')->with(compact('account_transaction', 'business_id'));
    }

    public function listEditAccountTransaction()
    {
        if (request()->ajax()) {
            $transactions = EditAccountEntry::leftjoin('business', 'edit_account_entries.business_id', 'business.id')
                ->leftjoin('accounts', 'edit_account_entries.account_id', 'accounts.id')
                ->select(
                    'business.name as business_name',
                    'accounts.name as account_name',
                    'edit_account_entries.*'
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
}
