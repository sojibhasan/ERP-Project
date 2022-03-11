<?php

namespace App\Http\Controllers;

use App\Account;
use App\AccountGroup;
use App\AccountType;
use App\Business;
use Illuminate\Http\Request;
use App\DefaultAccount;
use App\DefaultAccountGroup;
use App\DefaultAccountType;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;

class DefaultAccountController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $business_id = session()->get('user.business_id');
        if (request()->ajax()) {
            $default_accounts = DefaultAccount::leftjoin('account_transactions as AT', function ($join) {
                $join->on('AT.account_id', '=', 'default_accounts.id');
                $join->whereNull('AT.deleted_at');
            })
                ->leftjoin(
                    'default_account_types as ats',
                    'default_accounts.account_type_id',
                    '=',
                    'ats.id'
                )
                ->leftjoin(
                    'default_account_types as pat',
                    'ats.parent_account_type_id',
                    '=',
                    'pat.id'
                )
                ->leftjoin(
                    'default_account_groups',
                    'default_accounts.asset_type',
                    '=',
                    'default_account_groups.id'
                )
                ->leftJoin('users AS u', 'default_accounts.created_by', '=', 'u.id')
                ->where('default_accounts.business_id', $business_id)
                ->select([
                    'default_accounts.name', 'default_accounts.account_number', 'default_accounts.note', 'default_accounts.id', 'default_accounts.account_type_id', 'default_account_groups.name as group_name',
                    'ats.name as account_type_name',
                    'pat.name as parent_account_type_name',
                    'is_closed'
                ]);

            $default_accounts->groupBy('default_accounts.id');

            return DataTables::of($default_accounts)
                ->addColumn(
                    'action',
                    '<button data-href="{{action(\'DefaultAccountController@edit\',[$id])}}" data-container=".default_account_model" class="btn btn-xs btn-primary btn-modal"><i class="glyphicon glyphicon-edit"></i> @lang("messages.edit")</button>
                    <button data-href="{{action(\'DefaultAccountController@destroy\',[$id])}}" class="btn btn-xs btn-danger delete_account"><i class="fa fa-trash "></i> @lang("account.delete")</button>
                              '
                )
                ->editColumn('name', function ($row) {
                    if ($row->is_closed == 1) {
                        return $row->name . ' <small class="label pull-right bg-red no-print">' . __("account.closed") . '</small><span class="print_section">(' . __("account.closed") . ')</span>';
                    } else {
                        return $row->name;
                    }
                })
                ->editColumn('balance', function ($row) {
                    return '<span class="display_currency" data-currency_symbol="true">' . $row->balance . '</span>';
                })
                ->editColumn('account_type', function ($row) {
                    $account_type = '';
                    if (!empty($row->account_type->parent_account)) {
                        $account_type .= $row->account_type->parent_account->name . ' - ';
                    }
                    if (!empty($row->account_type)) {
                        $account_type .= $row->account_type->name;
                    }
                    return $account_type;
                })
                ->editColumn('parent_account_type_name', function ($row) {
                    $parent_account_type_name = empty($row->parent_account_type_name) ? $row->account_type_name : $row->parent_account_type_name;
                    return $parent_account_type_name;
                })
                ->editColumn('account_type_name', function ($row) {
                    $account_type_name = empty($row->parent_account_type_name) ? '' : $row->account_type_name;
                    return $account_type_name;
                })
                ->removeColumn('id')
                ->removeColumn('is_closed')
                ->rawColumns(['action', 'balance', 'name'])
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
        $business_id = session()->get('user.business_id');
        $account_types = DefaultAccountType::where('business_id', $business_id)
            ->whereNull('parent_account_type_id')
            ->with(['sub_types'])
            ->get();


        $asset_type_ids = json_encode(DefaultAccountType::getAccountTypeIdOfType('Assets', $business_id));

        return view('default_account.create')
            ->with(compact('account_types', 'asset_type_ids'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (request()->ajax()) {
            try {
                $input = $request->only(['name', 'account_number', 'note', 'account_type_id', 'asset_type']);
                $business_id = $request->session()->get('user.business_id');
                $user_id = $request->session()->get('user.id');
                $input['business_id'] = $business_id;
                $input['created_by'] = $user_id;
                $asset_type_ids = DefaultAccountType::getAccountTypeIdOfType('Assets', $business_id);
                if (!in_array($input['account_type_id'],  $asset_type_ids)) {
                    $input['asset_type'] = null;
                }

                $account = DefaultAccount::create($input);

                //adding account for other businesses
                $businesses = Business::all();
                foreach ($businesses as $key => $value) {
                    $account_type = AccountType::where('business_id', $value->id)->where('default_account_type_id', $account->account_type_id)->first();
                    $data = array(
                        'business_id' => $value->id,
                        'name' => $account->name,
                        'account_number' => $account->account_number,
                        'account_type_id' => !empty($account_type) ?  $account_type->id : null,
                        'note' => $account->note,
                        'asset_type' => $account->asset_type,
                        'created_by' => $account->created_by,
                        'is_closed' => 0,
                        'default_account_id' => $account->id,
                    );
                    $account_exist = Account::where('business_id', $value->id)->where('name',  $account->name)->first();
                    if (empty($account_exist)) {
                        Account::create($data);
                    }
                }

                $output = [
                    'success' => true,
                    'msg' => __("account.account_created_success")
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
        if (request()->ajax()) {
            $business_id = request()->session()->get('user.business_id');
            $account = DefaultAccount::where('business_id', $business_id)
                ->find($id);

            $account_types = DefaultAccountType::where('business_id', $business_id)
                ->whereNull('parent_account_type_id')
                ->with(['sub_types'])
                ->get();

            $default_account_groups = DefaultAccountGroup::pluck('name', 'id');

            return view('default_account.edit')
                ->with(compact('account', 'account_types', 'default_account_groups'));
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
        if (request()->ajax()) {
            try {
                $user_id = $request->session()->get('user.id');
                $input = $request->only(['name', 'account_number', 'note', 'account_type_id', 'asset_type']);

                $business_id = request()->session()->get('user.business_id');
                $account = DefaultAccount::where('business_id', $business_id)
                    ->findOrFail($id);
                $account->name = $input['name'];
                $account->account_number = $input['account_number'];
                $account->note = $input['note'];
                $account->asset_type = $input['asset_type'];
                $account->account_type_id = $input['account_type_id'];
                $account->save();

                //update account for other businesses
                $businesses = Business::all();
                foreach ($businesses as $key => $value) {
                    $account_type = AccountType::where('business_id', $value->id)->where('default_account_type_id', $input['account_type_id'])->first();
                    $account_group = AccountGroup::where('business_id', $value->id)->where('default_account_group_id', $account->asset_type)->first();
                    $data = array(
                        'name' => $account->name,
                        'account_number' => $account->account_number,
                        'account_type_id' => !empty($account_type) ?  $account_type->id : null,
                        'note' => $account->note,
                        'asset_type' => $account_group->id,
                        'created_by' => $account->created_by,
                        'is_closed' => 0,
                        'default_account_id' => $account->id,
                    );
                    Account::where('default_account_id', $id)->where('business_id', $value->id)->update($data);
                }

                $output = [
                    'success' => true,
                    'msg' => __("account.account_updated_success")
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

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (request()->ajax()) {
            try {
                $business_id = request()->session()->get('user.business_id');

                DefaultAccount::where('id', $id)->delete();

                //delete account for other businesses
                $businesses = Business::all();
                foreach ($businesses as $key => $value) {
                    Account::where('default_account_id', $id)->where('business_id', $value->id)->delete();
                }


                $output = [
                    'success' => true,
                    'msg' => __("lang_v1.deleted_success")
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
}
