<?php

namespace App\Http\Controllers;

use App\Account;
use App\AccountGroup;
use App\AccountSetting;
use App\AccountTransaction;
use App\AccountType;
use App\Utils\ModuleUtil;
use App\Utils\ProductUtil;
use App\Utils\TransactionUtil;
use App\Utils\Util;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AccountSettingController extends Controller
{
    protected $commonUtil;
    protected $moduleUtil;
    protected $productUtil;
    protected $transactionUtil;

    /**
     * Constructor
     *
     * @param Util $commonUtil
     * @return void
     */
    public function __construct(Util $commonUtil, ModuleUtil $moduleUtil, ProductUtil $productUtil, TransactionUtil $transactionUtil)
    {
        $this->commonUtil = $commonUtil;
        $this->moduleUtil =  $moduleUtil;
        $this->productUtil =  $productUtil;
        $this->transactionUtil =  $transactionUtil;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (request()->ajax()) {
            $business_id = request()->session()->get('user.business_id');

            $brands = AccountSetting::leftjoin('account_groups', 'account_settings.group_id', 'account_groups.id')
                ->leftjoin('accounts', 'account_settings.account_id', 'accounts.id')
                ->leftjoin('users', 'account_settings.created_by', 'users.id')
                ->where('account_settings.business_id', $business_id)
                ->select('account_settings.date', 'account_groups.name as account_group', 'accounts.name', 'account_settings.amount', 'users.username as created_by', 'account_settings.id');

            return datatables()::of($brands)
                ->addColumn(
                    'action',
                    '@can("account.settings.edit")
                    <button data-href="{{action(\'AccountSettingController@edit\', [$id])}}" class="btn btn-xs btn-primary btn-modal" data-container=".view_modal"><i class="glyphicon glyphicon-edit"></i> @lang("messages.edit")</button>
                        &nbsp;
                    @endcan'
                )
                ->editColumn('amount', '{{@num_format($amount)}}')
                ->editColumn('date', '{{@format_date($date)}}')
                ->removeColumn('id')
                ->rawColumns(['action'])
                ->make(true);
        }
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
            DB::beginTransaction();

            $account_id = $request->account_id;
            $amount = $request->amount;
            $date = $this->transactionUtil->uf_date($request->date);
            if (!empty($account_id) && !empty($amount)) {
                if (!empty($account_id)) {
                    $this->addAccountOpeningBalance($amount, $account_id, $date, null, true);
                }
            }

            DB::commit();
            $output = [
                'success' => 1,
                'msg' => __('lang_v1.success')
            ];
        } catch (\Exception $e) {
            Log::emergency('File: ' . $e->getFile() . 'Line: ' . $e->getLine() . 'Message: ' . $e->getMessage());
            $output = [
                'success' => 0,
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
        $business_id = request()->session()->get('business.id');
        $account_settings = AccountSetting::find($id);

        $account_groups = AccountGroup::where('business_id', $business_id)->whereIn('name', ['Cash Account', "Cheques in Hand (Customer's)", 'Card', 'Bank Account'])->pluck('name', 'id');

        $accounts = Account::where('business_id', $business_id)->where('asset_type', $account_settings->group_id)->pluck('name', 'id');

        return view('account_settings.edit')->with(compact(
            'account_settings',
            'account_groups',
            'accounts'
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
        try {
            DB::beginTransaction();
            $account_settings = AccountSetting::find($id);
            $account_settings->date = !empty($request->date) ? $this->transactionUtil->uf_date($request->date) : date('Y-m-d');
            $account_settings->amount = $request->amount;
            $account_settings->account_id = $request->account_id;
            $account_settings->created_by = Auth::user()->id;
            $account_settings->save();



            $account_id = $request->account_id;
            $amount = $request->amount;
            $date = $this->transactionUtil->uf_date($request->date);
            if (!empty($account_id) && !empty($amount)) {
                if (!empty($account_id)) {
                    $this->updateAccountOpeningBalance($account_settings, $amount, $account_id, $date, null, true);
                }
            }

            DB::commit();
            $output = [
                'success' => 1,
                'msg' => __('lang_v1.success')
            ];
        } catch (\Exception $e) {
            Log::emergency('File: ' . $e->getFile() . 'Line: ' . $e->getLine() . 'Message: ' . $e->getMessage());
            $output = [
                'success' => 0,
                'msg' => __('messages.something_went_wrong')
            ];
        }
        return redirect()->back()->with('status', $output);
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

    public function addAccountOpeningBalance($amount, $account_id, $date = null, $note = null, $is_setting = false)
    {
        $business_id = request()->session()->get('business.id');

        $type = 'debit';
        if ($amount > 0) {
            $type = 'debit';
        } else {
            $type = 'credit';
        }
        $amount = ($amount);

        $ob_transaction_data = [
            'amount' => abs($this->commonUtil->num_uf($amount)),
            'account_id' => $account_id,
            'type' => $type,
            'sub_type' => 'opening_balance',
            'operation_date' => !empty($date) ? $date : Carbon::now(),
            'note' => !empty($note) ? $note : null,
            'created_by' => Auth::user()->id
        ];

        $at_asset_transaction = AccountTransaction::createAccountTransaction($ob_transaction_data);

        $opening_balance_equity_id = $this->transactionUtil->account_exist_return_id('Opening Balance Equity Account');
      
        if ($amount > 0) {
            $type = 'credit';
        } else {
            $type = 'debit';
        }
        $ob_transaction_data['account_id'] = $opening_balance_equity_id;
        $ob_transaction_data['type'] = $type;
        $ob_transaction_data['amount'] = abs($this->commonUtil->num_uf($amount));
        $at_obe_transaction = AccountTransaction::createAccountTransaction($ob_transaction_data);

        if ($is_setting) {
            $setting_date = [
                'business_id' => $business_id,
                'date' => $date,
                'account_id' => $account_id,
                'amount' => $amount,
                'group_id' => request()->group_id,
                'at_asset_id' => $at_asset_transaction->id,
                'at_obe_id' => $at_obe_transaction->id,
                'created_by' => Auth::user()->id
            ];

            AccountSetting::create($setting_date);
        }

        return true;
    }
    public function updateAccountOpeningBalance($account_settings, $amount, $account_id, $date = null, $note = null, $is_setting = false)
    {
        $business_id = request()->session()->get('business.id');

        $type = 'debit';
        if ($amount > 0) {
            $type = 'debit';
        } else {
            $type = 'credit';
        }
        $amount = ($amount);

        $ob_transaction_data = [
            'amount' => abs($this->commonUtil->num_uf($amount)),
            'account_id' => $account_id,
            'type' => $type,
            'sub_type' => 'opening_balance',
            'operation_date' => !empty($date) ? $date : Carbon::now(),
            'note' => !empty($note) ? $note : null,
            'created_by' => Auth::user()->id
        ];

        AccountTransaction::where('id', $account_settings->at_asset_id)->update($ob_transaction_data);

        $opening_balance_equity_id = $this->transactionUtil->account_exist_return_id('Opening Balance Equity Account');
        if ($amount > 0) {
            $type = 'credit';
        } else {
            $type = 'debit';
        }
        $ob_transaction_data['account_id'] = $opening_balance_equity_id;
        $ob_transaction_data['amount'] = abs($this->commonUtil->num_uf($amount));
        $ob_transaction_data['type'] = $type;
        AccountTransaction::where('id', $account_settings->at_obe_id)->update($ob_transaction_data);

        return true;
    }
}
