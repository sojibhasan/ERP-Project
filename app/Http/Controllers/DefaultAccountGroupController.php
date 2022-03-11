<?php

namespace App\Http\Controllers;

use App\AccountGroup;
use App\AccountType;
use App\Business;
use App\DefaultAccountGroup;
use App\DefaultAccountType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;

class DefaultAccountGroupController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $business_id = request()->session()->get('business.id');
        if (request()->ajax()) {
            $default_account_groups = DefaultAccountGroup::leftjoin(
                'account_types as ats',
                'default_account_groups.account_type_id',
                '=',
                'ats.id'
            )
                ->select([
                    'default_account_groups.*', 'ats.name as account_type_name'
                ]);


            $default_account_groups->groupBy('default_account_groups.id');



            return DataTables::of($default_account_groups)
                ->addColumn(
                    'action',
                    '
                    <button data-href="{{action(\'DefaultAccountGroupController@edit\',[$id])}}" data-container=".default_account_model" class="btn btn-xs btn-primary btn-modal edit_btn"><i class="glyphicon glyphicon-edit"></i> @lang("messages.edit")</button>
                    <button data-href="{{action(\'DefaultAccountGroupController@destroy\',[$id])}}" class="btn btn-xs btn-danger account_group_delete"><i class="glyphicon glyphicon-trash"></i> @lang("messages.delete")</button>
                   
                    '
                )
                ->removeColumn('id')
                ->removeColumn('is_closed')
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
        $business_id = request()->session()->get('business.id');
        $account_types = DefaultAccountType::where('business_id', $business_id)
            ->whereNull('parent_account_type_id')
            ->with(['sub_types'])
            ->get();

        return view('default_account.create_account_group')->with(compact(
            'account_types'
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
        try {
            $business_id = request()->session()->get('business.id');
            $input = $request->except('_token');
            $input['business_id'] = $business_id;
            DB::beginTransaction();
            $default_account_group = DefaultAccountGroup::create($input);
            //adding account for other businesses
            $default_account_type = DefaultAccountType::find($default_account_group->account_type_id);
            $businesses = Business::all();
            foreach ($businesses as $key => $value) {
                $account_type = AccountType::where('business_id', $value->id)->where('default_account_type_id', $default_account_group->account_type_id)->first();
                if (empty($account_type)) {
                    $account_type = AccountType::where('business_id', $value->id)->where('name', $default_account_type->name)->first();
                }
                $data = array(
                    'business_id' => $value->id,
                    'name' => $default_account_group->name,
                    'account_type_id' => !empty($account_type) ? $account_type->id : null,
                    'note' => $default_account_group->note,
                    'default_account_group_id' => $default_account_group->id
                );
                $default_account_group_exist = AccountGroup::where('business_id', $value->id)->where('name',  $default_account_group->name)->first();
                if (empty($default_account_group_exist)) {
                    AccountGroup::create($data);
                }
            }
            DB::commit();
            $output = [
                'success' => true,
                'msg' => __('lang_v1.add_account_group_success')
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
        $account_group = DefaultAccountGroup::findOrFail($id);
        $account_types = DefaultAccountType::where('business_id', $business_id)
            ->whereNull('parent_account_type_id')
            ->with(['sub_types'])
            ->get();

        return view('default_account.edit_account_group')->with(compact(
            'account_types',
            'account_group'
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
            $input = $request->except('_token');
            DB::beginTransaction();
            $default_account_group = DefaultAccountGroup::findOrFail($id);

            $default_account_group->name = $input['name'];
            $default_account_group->note = $input['note'];
            $default_account_group->account_type_id = $input['account_type_id'];
            $default_account_group->save();

            //update account for other businesses
            $businesses = Business::all();
            foreach ($businesses as $key => $value) {
                $account_type = AccountType::where('business_id', $value->id)->where('default_account_type_id', $input['account_type_id'])->first();
                $data = array(
                    'business_id' => $value->id,
                    'name' => $default_account_group->name,
                    'account_type_id' => !empty($account_type) ? $account_type->id : null,
                    'note' => $default_account_group->note,
                    'default_account_group_id' => $default_account_group->id

                );

                AccountGroup::where('default_account_group_id', $id)->where('business_id', $value->id)->update($data);
            }
            DB::commit();

            $output = [
                'success' => true,
                'msg' => __('lang_v1.update_account_group_success')
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

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $account_group = DefaultAccountGroup::findOrFail($id);
            $account_group->delete();

            $output = [
                'success' => true,
                'msg' => __('lang_v1.delete_account_group_success')
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

    public function getDefaultAccountGroupByType($type_id)
    {
        $business_id = session()->get('user.business_id');

        $default_account_groups = DefaultAccountGroup::where('account_type_id', $type_id)->where('business_id', $business_id)->get();

        $html = '<option selected="selected" value="">Please Select</option>';
        foreach ($default_account_groups as $account_group) {
            $html .= '<option value="' . $account_group->id . '" >' . $account_group->name . '</option>';
        }

        return $html;
    }
}
