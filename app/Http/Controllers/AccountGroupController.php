<?php

namespace App\Http\Controllers;

use App\AccountGroup;
use App\AccountType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;

class AccountGroupController extends Controller
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
            $account_groups = AccountGroup::leftjoin(
                'account_types as ats',
                'account_groups.account_type_id',
                '=',
                'ats.id'
            )
                ->where('account_groups.business_id', $business_id)
                ->select([
                    'account_groups.*', 'ats.name as account_type_name'
                ]);


            $account_groups->groupBy('account_groups.id');



            return DataTables::of($account_groups)
                ->addColumn(
                    'action',
                    '
                    <button data-href="{{action(\'AccountGroupController@edit\',[$id])}}" data-container=".account_model" class="btn btn-xs btn-primary btn-modal edit_btn"><i class="glyphicon glyphicon-edit"></i> @lang("messages.edit")</button>
                    <button data-href="{{action(\'AccountGroupController@destroy\',[$id])}}" class="btn btn-xs btn-danger account_group_delete"><i class="glyphicon glyphicon-trash"></i> @lang("messages.delete")</button>
                   
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
        $account_type_query = AccountType::where('business_id', $business_id)
            ->whereNull('parent_account_type_id')
            ->with(['sub_types']);
        $account_types = $account_type_query->get();

        return view('account_groups.create')->with(compact(
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
            AccountGroup::create($input);

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
        $account_group = AccountGroup::findOrFail($id);
        $account_type_query = AccountType::where('business_id', $business_id)
            ->whereNull('parent_account_type_id')
            ->with(['sub_types']);
        $account_types = $account_type_query->get();

        return view('account_groups.edit')->with(compact(
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
            AccountGroup::where('id', $id)->update($input);

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
            $account_group = AccountGroup::findOrFail($id);
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

    public function getAccountGroupByType($type_id){
        $business_id = session()->get('user.business_id');

        $account_groups = AccountGroup::where('account_type_id', $type_id)->where('business_id', $business_id)->get();

        $html = '<option selected="selected" value="">Please Select</option>';
        foreach ($account_groups as $account_group) {
            $html .= '<option value="' . $account_group->id . '" >' . $account_group->name . '</option>';
        }

        return $html;
    }
}
