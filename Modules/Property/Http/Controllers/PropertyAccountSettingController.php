<?php

namespace Modules\Property\Http\Controllers;

use App\Account;
use Carbon\Carbon;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Modules\Property\Entities\Property;
use Modules\Property\Entities\PropertyAccountSetting;

class PropertyAccountSettingController extends Controller
{
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
        $property_id = request()->property_id;
        $business_id = request()->session()->get('user.business_id');

        $income_not_pen_accounts = Account::leftjoin('account_types', 'accounts.account_type_id', 'account_types.id')
            ->leftjoin('account_groups', 'accounts.asset_type', 'account_groups.id')
            ->where('accounts.business_id', $business_id)
            ->where('account_types.name', 'Income')
            ->whereNotIn('account_groups.name', ['Interest Income', 'Penalty Income'])
            ->pluck('accounts.name', 'accounts.id');

        $expense_accounts = Account::getAccountByAccountTypeName('Expenses');
        $income_accounts = Account::getAccountByAccountTypeName('Income');
        $receiveable_account = Account::where('name', 'Accounts Receivable')->where('business_id', $business_id)->first();
        if (!empty($receiveable_account->is_main_account)) {//if main account then return sub accounts
            $account_receivable_accounts = Account::where('business_id', $business_id)->where('parent_account_id', $receiveable_account->id)->pluck('name', 'id');
           
        }else{
            $account_receivable_accounts = Account::where('name', 'Accounts Receivable')->where('business_id', $business_id)->pluck('name', 'id');
        }

        return view('property::account_settings.create')->with(compact(
            'property_id',
            'income_not_pen_accounts',
            'expense_accounts',
            'account_receivable_accounts',
            'income_accounts'
        ));
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request)
    {
        $business_id = request()->session()->get('user.business_id');
        try {
            $input = $request->except('_token');
            $input['business_id'] = $business_id;
            $input['date'] = !empty($input['date']) ? Carbon::createFromFormat(session('business.date_format'), $input['date'])->toDateString() : date('Y-m-d');

            PropertyAccountSetting::create($input);

            $output = [
                'success' => true,
                'msg' => __('property::lang.acount_setting_added_success')
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
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {
        $account_settings = PropertyAccountSetting::find($id);
        $property = Property::find($account_settings->property_id);

        return view('property::account_settings.show')->with(compact(
            'account_settings',
            'property'
        ));
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        $account_settings = PropertyAccountSetting::find($id);
        $property = Property::find($account_settings->property_id);
        $business_id = request()->session()->get('user.business_id');

        $income_not_pen_accounts = Account::leftjoin('account_types', 'accounts.account_type_id', 'account_types.id')
            ->leftjoin('account_groups', 'accounts.asset_type', 'account_groups.id')
            ->where('accounts.business_id', $business_id)
            ->where('account_types.name', 'Income')
            ->whereNotIn('account_groups.name', ['Interest Income', 'Penalty Income'])
            ->pluck('accounts.name', 'accounts.id');

        $expense_accounts = Account::getAccountByAccountTypeName('Expenses');
        $income_accounts = Account::getAccountByAccountTypeName('Income');
        $receiveable_account = Account::where('name', 'Accounts Receivable')->where('business_id', $business_id)->first();
        if (!empty($receiveable_account->is_main_account)) {//if main account then return sub accounts
            $account_receivable_accounts = Account::where('business_id', $business_id)->where('parent_account_id', $receiveable_account->id)->pluck('name', 'id');
           
        }else{
            $account_receivable_accounts = Account::where('name', 'Accounts Receivable')->where('business_id', $business_id)->pluck('name', 'id');
        }

        return view('property::account_settings.edit')->with(compact(
            'account_settings',
            'property',
            'income_not_pen_accounts',
            'expense_accounts',
            'account_receivable_accounts',
            'income_accounts'
        ));
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(Request $request, $id)
    {
        $business_id = request()->session()->get('user.business_id');
        try {
            $input = $request->except('_token', '_method');
            $input['business_id'] = $business_id;
            $input['date'] = !empty($input['date']) ? Carbon::createFromFormat(session('business.date_format'), $input['date'])->toDateString() : date('Y-m-d');

            PropertyAccountSetting::where('id', $id)->update($input);

            $output = [
                'success' => true,
                'msg' => __('property::lang.acount_setting_updated_success')
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
        //
    }
}
