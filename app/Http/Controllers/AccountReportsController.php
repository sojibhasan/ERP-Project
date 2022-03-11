<?php

namespace App\Http\Controllers;

use App\Account;
use App\AccountGroup;
use App\AccountTransaction;
use App\AccountType;
use App\BusinessLocation;
use App\TransactionPayment;
use App\Utils\ModuleUtil;
use App\Utils\TransactionUtil;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Bus;
use Modules\Superadmin\Entities\ModulePermissionLocation;
use Yajra\DataTables\Facades\DataTables;

class AccountReportsController extends Controller
{
    /**
     * All Utils instance.
     *
     */
    protected $transactionUtil;
    protected $moduleUtil;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(TransactionUtil $transactionUtil, ModuleUtil $moduleUtil)
    {
        $this->transactionUtil = $transactionUtil;
        $this->moduleUtil = $moduleUtil;
    }
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function balanceSheet()
    {
        if (!auth()->user()->can('account.access')) {
            abort(403, 'Unauthorized action.');
        }
        $business_id = session()->get('user.business_id');
        $account_access = $this->moduleUtil->hasThePermissionInSubscription($business_id, 'access_account');
        $location_id = request()->location_id;
        $end_date = !empty(request()->input('end_date')) ? $this->transactionUtil->uf_date(request()->input('end_date')) : Carbon::now()->format('Y-m-d');
        $start_date = Carbon::now()->year . '-' . request()->session()->get('business.fy_start_month') . '-1';
        if (request()->ajax()) {

            $assets_accounts = $this->accountQueryBalanceSheet($location_id, $business_id, $start_date, $end_date)->where('ats.name', 'Current Assets')->where('parent_account_id', null)->select([
                'accounts.name', 'ats.name as type_name',
                DB::raw("SUM( IF(AT.type='credit', -1*amount, amount) ) as balance")
            ])->groupBy('accounts.id')->get();
            $assets_accounts_mains = Account::leftjoin('account_types as ats', 'accounts.account_type_id', '=', 'ats.id')->where('accounts.business_id', $business_id)->where('ats.name', 'Current Assets')->where('is_main_account', 1)->select('accounts.name', 'accounts.id')->get();
            $assets_accounts_main_balances = [];
            foreach ($assets_accounts_mains as $assets_accounts_main) {
                $assets_accounts_main_balances[$assets_accounts_main->name] =  Account::getSubAccountBalanceByMainAccountId($assets_accounts_main->id, $start_date, $end_date);
            }


            $fixed_assets_accounts = $this->accountQueryBalanceSheet($location_id, $business_id, $start_date, $end_date)->where('ats.name', 'Fixed Assets')->where('parent_account_id', null)->select([
                'accounts.name', 'ats.name as type_name',
                DB::raw("SUM( IF(AT.type='credit', -1*amount, amount) ) as balance")
            ])->groupBy('accounts.id')->get();
            $fixed_assets_accounts_mains = Account::leftjoin('account_types as ats', 'accounts.account_type_id', '=', 'ats.id')->where('accounts.business_id', $business_id)->where('ats.name', 'Fixed Assets')->where('is_main_account', 1)->select('accounts.name', 'accounts.id')->get();
            $fixed_assets_accounts_main_balances = [];
            foreach ($fixed_assets_accounts_mains as $fixed_assets_accounts_main) {
                $fixed_assets_accounts_main_balances[$fixed_assets_accounts_main->name] =  Account::getSubAccountBalanceByMainAccountId($fixed_assets_accounts_main->id, $start_date, $end_date);
            }

            $liabilities_accounts = $this->accountQueryBalanceSheet($location_id, $business_id, $start_date, $end_date)->where('ats.name', 'Current Liabilities')->select([
                'accounts.name', 'ats.name as type_name',
                DB::raw("SUM( IF(AT.type='credit', amount, -1*amount) ) as balance")
            ])->get();
            $liabilities_accounts_mains = Account::leftjoin('account_types as ats', 'accounts.account_type_id', '=', 'ats.id')->where('accounts.business_id', $business_id)->where('ats.name', 'Current Liabilities')->where('is_main_account', 1)->select('accounts.name', 'accounts.id')->get();
            $liabilities_accounts_main_balances = [];
            foreach ($liabilities_accounts_mains as $liabilities_accounts_main) {
                $liabilities_accounts_main_balances[$liabilities_accounts_main->name] =  Account::getSubAccountBalanceByMainAccountId($liabilities_accounts_main->id, $start_date, $end_date);
            }

            $lt_liabilities_accounts = $this->accountQueryBalanceSheet($location_id, $business_id, $start_date, $end_date)->where('ats.name', 'Long term Liabilities')->where('parent_account_id', null)->select([
                'accounts.name', 'ats.name as type_name',
                DB::raw("SUM( IF(AT.type='credit', amount, -1*amount) ) as balance")
            ])->groupBy('accounts.id')->get();

            $lt_liabilities_accounts_mains = Account::leftjoin('account_types as ats', 'accounts.account_type_id', '=', 'ats.id')->where('accounts.business_id', $business_id)->where('ats.name', 'Long term Liabilities')->where('is_main_account', 1)->select('accounts.name', 'accounts.id')->get();
            $lt_liabilities_accounts_main_balances = [];
            foreach ($lt_liabilities_accounts_mains as $lt_liabilities_accounts_main) {
                $lt_liabilities_accounts_main_balances[$lt_liabilities_accounts_main->name] =  Account::getSubAccountBalanceByMainAccountId($lt_liabilities_accounts_main->id, $start_date, $end_date);
            }

            $equity_accounts = $this->accountQueryBalanceSheet($location_id, $business_id, $start_date, $end_date)->where('ats.name', 'Equity')->where('parent_account_id', null)->select([
                'accounts.name', 'ats.name as type_name',
                DB::raw("SUM( IF(AT.type='credit', amount, -1*amount) ) as balance")
            ])->groupBy('accounts.id')->get();

            return view('account_reports.partials.balance_sheet_details')->with(compact(
                'account_access',
                'assets_accounts',
                'liabilities_accounts',
                'fixed_assets_accounts',
                'lt_liabilities_accounts',
                'equity_accounts',
                'assets_accounts_main_balances',
                'fixed_assets_accounts_main_balances',
                'liabilities_accounts_main_balances',
                'lt_liabilities_accounts_main_balances'
            ));
        }
        $business_locations = BusinessLocation::where('business_id', $business_id)->pluck('name', 'id');

        return view('account_reports.balance_sheet')->with(compact('business_locations', 'account_access'));
    }

    public function accountQueryBalanceSheet($location_id = null, $business_id, $start_date, $end_date)
    {
        $query = Account::leftjoin(
            'account_transactions as AT',
            'AT.account_id',
            '=',
            'accounts.id'
        )->leftjoin(
            'transactions',
            'AT.transaction_id',
            '=',
            'transactions.id'
        )
            ->leftjoin(
                'account_types as ats',
                'accounts.account_type_id',
                '=',
                'ats.id'
            )
            // ->NotClosed()
            ->whereNull('AT.deleted_at')
            ->where('accounts.business_id', $business_id)
            ->whereDate('AT.operation_date', '>=', $start_date)
            ->whereDate('AT.operation_date', '<=', $end_date);
        if (!empty($location_id)) {
            $query->where('transactions.location_id', $location_id);
        } else {
            $allowed_locations = ModulePermissionLocation::getModulePermissionLocations($business_id, 'accounting_module');
            if (!empty($allowed_locations)) {
                if (!empty($allowed_locations->locations)) {
                    $location_ids = array_keys($allowed_locations->locations);
                    $query->whereIn('transactions.location_id',  $location_ids);
                }
            }
        }
        return $query;
    }
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function trialBalance()
    {
        if (!auth()->user()->can('account.access')) {
            abort(403, 'Unauthorized action.');
        }
        $business_id = session()->get('user.business_id');
        $account_access = $this->moduleUtil->hasThePermissionInSubscription($business_id, 'access_account');
        if (request()->ajax()) {
            $end_date = request()->input('end_date');
            $start_date = request()->input('start_date');
            $location_id = request()->input('location_id');
          
            $accounts = AccountTransaction::join(
                'accounts as A',
                'account_transactions.account_id',
                '=',
                'A.id'
            )->leftjoin(
                'transactions',
                'account_transactions.transaction_id',
                '=',
                'transactions.id'
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
                ->select([
                    'A.name', 'A.account_number', 'ats.name as account_type',
                    DB::raw("SUM( IF(account_transactions.type='debit', account_transactions.amount, -1*account_transactions.amount) ) as ass_exp_balance"),
                    DB::raw("SUM( IF(account_transactions.type='credit', account_transactions.amount, -1*account_transactions.amount) ) as li_in_eq_balance")
                    // DB::raw("SUM( IF(account_transactions.type='credit', account_transactions.amount, -1*account_transactions.amount) ) as ass_exp_balance"),
                    // DB::raw("SUM( IF(account_transactions.type='debit', account_transactions.amount, -1*account_transactions.amount) ) as li_in_eq_balance")

                ])->withTrashed()
                ->groupBy('A.id');
            if (!empty(request()->input('location_id'))) {
                $accounts->where('transactions.location_id', request()->input('location_id'));
            }
            $start_date = request()->input('start_date');
            $end_date = request()->input('end_date');

            if (!empty($start_date) && !empty($end_date)) {
                $accounts->whereDate('account_transactions.operation_date', '>=', $start_date)
                    ->whereDate('account_transactions.operation_date', '<=', $end_date);
            }

            return DataTables::of($accounts)

                ->addColumn('debit', function ($row) {
                    $debit = '';
                    if ($row->account_type == 'Assets' || $row->account_type == 'Fixed Assets' || $row->account_type == 'Assets' || $row->account_type == 'Current Assets' || $row->account_type == 'Expenses') {
                        $debit = '<span class="display_currency debit" data-currency_symbol="true" data-orig-value="' . $row->ass_exp_balance . '">' . $row->ass_exp_balance . '</span>';
                    }
                    return $debit;
                })
                ->addColumn('credit', function ($row) {
                    $credit = '';
                    if ($row->account_type == 'Liabilities' || $row->account_type == 'Current Liabilities' || $row->account_type == 'Income' || $row->account_type == 'Equity' || $row->account_type == 'Profit & Loss' || $row->account_type == 'Owners Contribution') {
                        $credit = '<span class="display_currency credit" data-currency_symbol="true" data-orig-value="' . $row->li_in_eq_balance . '">' . $row->li_in_eq_balance . '</span>';
                    }
                    return $credit;
                })

                ->rawColumns(['debit', 'credit'])
                ->make(true);
        }




        $business_locations = BusinessLocation::where('business_id', $business_id)->pluck('name', 'id');

        return view('account_reports.trial_balance')->with(compact('business_locations', 'account_access'));
    }

    /**
     * Retrives account balances.
     * @return Obj
     */
    private function getAccountBalance($location_id = null, $business_id,  $start_date, $end_date, $account_type = 'others')
    {
        $query = Account::leftjoin(
            'account_transactions as AT',
            'AT.account_id',
            '=',
            'accounts.id'
        )
            ->leftjoin(
                'transactions',
                'AT.transaction_id',
                '=',
                'transactions.id'
            )
            ->leftjoin('account_types', 'accounts.account_type_id', 'account_types.id')
            ->whereNull('AT.deleted_at')
            ->where('accounts.is_main_account', 0)
            ->where('accounts.business_id', $business_id)
            ->whereDate('AT.operation_date', '>=', $start_date)
            ->whereDate('AT.operation_date', '<=', $end_date);
        if (!empty($location_id)) {
            $query->where('transactions.location_id', $location_id);
        } else {
            $allowed_locations = ModulePermissionLocation::getModulePermissionLocations($business_id, 'accounting_module');
            if (!empty($allowed_locations)) {
                if (!empty($allowed_locations->locations)) {
                    $location_ids = array_keys($allowed_locations->locations);
                    $query->whereIn('transactions.location_id',  $location_ids);
                }
            }
        }
        $account_details = $query->select([
            'accounts.name', 'accounts.account_number', 'account_types.name as type_name',
            DB::raw("SUM( IF(AT.type='credit', amount, -1*amount) ) as credit_balance"),
            DB::raw("SUM( IF(AT.type='debit', amount, -1*amount) ) as debit_balance")
        ])
            ->groupBy('accounts.id')
            ->get()->toArray();

        return $account_details;
    }

    /**
     * Displays payment account report.
     * @return Response
     */
    public function paymentAccountReport()
    {
        if (!auth()->user()->can('account.access')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = session()->get('user.business_id');

        if (request()->ajax()) {
            $query = TransactionPayment::leftjoin(
                'transactions as T',
                'transaction_payments.transaction_id',
                '=',
                'T.id'
            )
                ->leftjoin('accounts as A', 'transaction_payments.account_id', '=', 'A.id')
                ->where('transaction_payments.business_id', $business_id)
                ->whereNull('transaction_payments.parent_id')
                ->select([
                    'paid_on',
                    'payment_ref_no',
                    'T.ref_no',
                    'T.invoice_no',
                    'T.type',
                    'T.id as transaction_id',
                    'A.name as account_name',
                    'A.account_number',
                    'transaction_payments.id as payment_id',
                    'transaction_payments.account_id',
                    'transaction_payments.amount as paid_amount',
                    'T.final_total as amount'
                ]);

            $start_date = !empty(request()->input('start_date')) ? request()->input('start_date') : '';
            $end_date = !empty(request()->input('end_date')) ? request()->input('end_date') : '';

            if (!empty($start_date) && !empty($end_date)) {
                $query->whereBetween(DB::raw('date(paid_on)'), [$start_date, $end_date]);
            }

            $account_id = !empty(request()->input('account_id')) ? request()->input('account_id') : '';
            if (!empty($account_id)) {
                $query->where('account_id', $account_id);
            }

            return DataTables::of($query)
                ->editColumn('paid_on', function ($row) {
                    return $this->transactionUtil->format_date($row->paid_on, true);
                })
                ->addColumn('action', function ($row) {
                    if (auth()->user()->can('account.link_account')) {
                        $action = '<button type="button" class="btn btn-info 
                        btn-xs btn-modal"
                        data-container=".view_modal" 
                        data-href="' . action('AccountReportsController@getLinkAccount', [$row->payment_id]) . '">' . __('account.link_account') . '</button>';
                    } else {
                        $action = '';
                    }
                    return $action;
                })
                ->addColumn('account', function ($row) {
                    $account = '';
                    if (!empty($row->account_id)) {
                        $account = $row->account_name . ' - ' . $row->account_number;
                    }
                    return $account;
                })
                ->addColumn('transaction_number', function ($row) {
                    $html = $row->ref_no;
                    if ($row->type == 'sell') {
                        $html = '<button type="button" class="btn btn-link btn-modal"
                                    data-href="' . action('SellController@show', [$row->transaction_id]) . '" data-container=".view_modal">' . $row->invoice_no . '</button>';
                    } elseif ($row->type == 'purchase') {
                        $html = '<button type="button" class="btn btn-link btn-modal"
                                    data-href="' . action('PurchaseController@show', [$row->transaction_id]) . '" data-container=".view_modal">' . $row->ref_no . '</button>';
                    }
                    return $html;
                })
                ->editColumn('type', function ($row) {
                    $type = $row->type;
                    if ($row->type == 'sell') {
                        $type = __('sale.sale');
                    } elseif ($row->type == 'purchase') {
                        $type = __('lang_v1.purchase');
                    } elseif ($row->type == 'expense') {
                        $type = __('lang_v1.expense');
                    }
                    return $type;
                })
                ->editColumn('amount', function ($row) {
                    return '<span class="display_currency" data-currency_symbol="false">' . $row->amount . '</span>';
                })
                ->editColumn('paid_amount', function ($row) {
                    return '<span class="display_currency" data-currency_symbol="false">' . $row->paid_amount . '</span>';
                })
                ->filterColumn('account', function ($query, $keyword) {
                    $query->where('A.name', 'like', ["%{$keyword}%"])
                        ->orWhere('account_number', 'like', ["%{$keyword}%"]);
                })
                ->filterColumn('transaction_number', function ($query, $keyword) {
                    $query->where('T.invoice_no', 'like', ["%{$keyword}%"])
                        ->orWhere('T.ref_no', 'like', ["%{$keyword}%"]);
                })
                ->rawColumns(['action', 'transaction_number', 'amount', 'paid_amount'])
                ->make(true);
        }

        $accounts = Account::forDropdown($business_id, false);
        $accounts->prepend(__('messages.all'), '');

        return view('account_reports.payment_account_report')
            ->with(compact('accounts'));
    }

    /**
     * Shows form to link account with a payment.
     * @return Response
     */
    public function getLinkAccount($id)
    {
        if (!auth()->user()->can('account.access')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = session()->get('user.business_id');
        if (request()->ajax()) {
            $payment = TransactionPayment::where('business_id', $business_id)->findOrFail($id);
            $accounts = Account::forDropdown($business_id, false);

            return view('account_reports.link_account_modal')
                ->with(compact('accounts', 'payment'));
        }
    }

    /**
     * Links account with a payment.
     * @param  Request $request
     * @return Response
     */
    public function postLinkAccount(Request $request)
    {
        if (!auth()->user()->can('account.access')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $business_id = session()->get('user.business_id');
            if (request()->ajax()) {
                $payment_id = $request->input('transaction_payment_id');
                $account_id = $request->input('account_id');

                $payment = TransactionPayment::with(['transaction'])->where('business_id', $business_id)->findOrFail($payment_id);
                $payment->account_id = $account_id;
                $payment->save();

                $payment_type = !empty($payment->transaction->type) ? $payment->transaction->type : null;
                if (empty($payment_type)) {
                    $child_payment = TransactionPayment::where('parent_id', $payment->id)->first();
                    $payment_type = !empty($child_payment->transaction->type) ? $child_payment->transaction->type : null;
                }

                AccountTransaction::updateAccountTransaction($payment, $payment_type);
            }
            $output = [
                'success' => true,
                'msg' => __("account.account_linked_success")
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


    /**
     * Income Statement
     * @param  Request $request
     * @return Response
     */
    public function incomeStatement(Request $request)
    {
        if (!auth()->user()->can('account.access')) {
            abort(403, 'Unauthorized action.');
        }
        $business_id = session()->get('user.business_id');
        $account_access = $this->moduleUtil->hasThePermissionInSubscription($business_id, 'access_account');


        if (request()->ajax()) {

            $first_statement_start_date = $request->first_statement_start_date;
            $first_statement_end_date = $request->first_statement_end_date;
            $second_statement_start_date = $request->second_statement_start_date;
            $second_statement_end_date = $request->second_statement_end_date;
            $third_statement_start_date = $request->third_statement_start_date;
            $third_statement_end_date = $request->third_statement_end_date;
            $location_id = $request->location_id;
            $dates['first'] = $first_statement_start_date;
            $dates['second'] = $second_statement_start_date;
            $dates['third'] = $third_statement_start_date;

            /* income section */
            $income_details = [];
            $get_income_type_id = AccountType::getAccountTypeIdOfType('Income', $business_id);
            
            $income_accounts = Account::where('account_type_id', $get_income_type_id)->get();
            foreach ($income_accounts as $i_account) {
                $income_details[$i_account->name]['first'] =  $this->accountBalanceQuery($location_id, $first_statement_start_date, $first_statement_end_date, $i_account->id);
                $income_details[$i_account->name]['second'] =  $this->accountBalanceQuery($location_id, $second_statement_start_date, $second_statement_end_date, $i_account->id);
                $income_details[$i_account->name]['third'] =  $this->accountBalanceQuery($location_id, $third_statement_start_date, $third_statement_end_date, $i_account->id);
            }
            
            

            /* cost of sale section */
            $cost_details = [];
            $cog_group_id = AccountGroup::getGroupByName('COGS Account Group');
            $cost_accounts = Account::where('asset_type', $cog_group_id->id)->get();
            foreach ($cost_accounts as $c_account) {
                $cost_details[$c_account->name]['first'] =  $this->accountBalanceQuery($location_id, $first_statement_start_date, $first_statement_end_date, $c_account->id);
                $cost_details[$c_account->name]['second'] =  $this->accountBalanceQuery($location_id, $second_statement_start_date, $second_statement_end_date, $c_account->id);
                $cost_details[$c_account->name]['third'] =  $this->accountBalanceQuery($location_id, $third_statement_start_date, $third_statement_end_date, $c_account->id);
            }

            /* expense of sale section */
            $expense_details = [];
            $cog_group_id = AccountGroup::getGroupByName('COGS Account Group');
            $get_expense_type_id = AccountType::getAccountTypeIdOfType('Expenses', $business_id);
            $expense_accounts = Account::where('account_type_id', $get_expense_type_id)->where('asset_type', '!=', $cog_group_id->id)->get();
            foreach ($expense_accounts as $e_account) {
                $expense_details[$e_account->name]['first'] =  $this->accountBalanceQuery($location_id, $first_statement_start_date, $first_statement_end_date, $e_account->id);
                $expense_details[$e_account->name]['second'] =  $this->accountBalanceQuery($location_id, $second_statement_start_date, $second_statement_end_date, $e_account->id);
                $expense_details[$e_account->name]['third'] =  $this->accountBalanceQuery($location_id, $third_statement_start_date, $third_statement_end_date, $e_account->id);
            }


            return view('account_reports.partials.income_statement_details')->with(compact(
                'account_access',
                'income_details',
                'cost_details',
                'expense_details',
                'dates'
            ));
        }
        
        
        $business_locations = BusinessLocation::where('business_id', $business_id)->pluck('name', 'id');
        
        $selectedbusiness = BusinessLocation::where('business_id', $business_id)->first();
        
        if (!empty($selectedbusiness)) {
            $selectedID = $selectedbusiness->id;
        }else{
            $selectedID = ''; 
        }
        return view('account_reports.income_statement')->with(compact('selectedID',
            'business_locations'
        ));
    }

    public function accountBalanceQuery($location_id = null, $start_date, $end_date, $account_id)
    {
        $business_id = session()->get('user.business_id');

        $account_type_id = Account::where('id', $account_id)->first()->account_type_id;
        $account_type_name = AccountType::where('id', $account_type_id)->first();

        $query = Account::leftjoin('account_transactions as AT', 'AT.account_id', '=', 'accounts.id')
            ->leftjoin(
                'transactions',
                'AT.transaction_id',
                '=',
                'transactions.id'
            )
            ->where('accounts.id', $account_id)
            ->where('accounts.business_id', $business_id)
            ->whereNull('AT.deleted_at')
            ->whereDate('AT.operation_date', '>=', $start_date)
            ->whereDate('AT.operation_date', '<=', $end_date);
        if (!empty($location_id)) {
            //$query->where('transactions.location_id', $location_id);
        }

        if (strpos($account_type_name, "Assets") !== false || strpos($account_type_name, "Expenses") !== false) {
            $query->select([
                DB::raw("SUM( IF(AT.type='credit', -1 * amount, amount) ) as balance")
            ]);
        } else {
            $query->select([
                DB::raw("SUM( IF(AT.type='debit',-1 * amount,  amount) ) as balance")
            ]);
        }

        $account_details =  $query->first();
        if (!empty($account_details)) {
            return $account_details->balance;
        }
        return 0;
    }
}
