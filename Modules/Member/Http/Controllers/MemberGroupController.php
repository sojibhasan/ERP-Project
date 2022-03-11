<?php

namespace Modules\Member\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Modules\Member\Entities\MemberGroup;
use Modules\TasksManagement\Entities\TaskGroup;
use Spatie\Permission\Models\Permission;
use Yajra\DataTables\Facades\DataTables;

class MemberGroupController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        $business_id = request()->session()->get('business.id');
        if (request()->ajax()) {
            $member_group = MemberGroup::where('business_id', $business_id)
                ->select([
                    'member_groups.*'
                ]);

            return DataTables::of($member_group)
                ->addColumn(
                    'action',
                    '
                    <button data-href="{{action(\'\Modules\Member\Http\Controllers\MemberGroupController@edit\',[$id])}}" data-container=".member_group_model" class="btn btn-xs btn-primary btn-modal edit_btn"><i class="glyphicon glyphicon-edit"></i> @lang("messages.edit")</button>
                  <!--  <button data-href="{{action(\'\Modules\Member\Http\Controllers\MemberGroupController@destroy\',[$id])}}" class="btn btn-xs btn-danger note_group_delete"><i class="glyphicon glyphicon-trash"></i> @lang("messages.delete")</button> -->
                   
                    '
                )
                ->removeColumn('id')
                ->rawColumns(['action'])
                ->make(true);
        }
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create()
    {
        return view('member::settings.member_group.create');
    }

    /**
     * Store a newly created resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        $business_id = $request->session()->get('business.id');
        try {
            $data = $request->except('_token');
            $data['business_id'] = $business_id;
            $data['date'] = !empty($data['date']) ? Carbon::parse($data['date'])->format('Y-m-d') : date('Y-m-d');
            $member_group = MemberGroup::create($data);

            //Create a new permission related to the created member_group
            Permission::create(['name' => 'member_group.' . $member_group->id]);


            $task_data['business_id'] = $business_id;
            $task_data['name'] = $data['member_group'];
            TaskGroup::create($task_data);

            $output = [
                'success' => true,
                'tab' => 'member_group',
                'msg' => __('member::lang.member_group_create_success')
            ];
        } catch (\Exception $e) {
            Log::emergency('File: ' . $e->getFile() . 'Line: ' . $e->getLine() . 'Message: ' . $e->getMessage());
            $output = [
                'success' => false,
                'tab' => 'member_group',
                'msg' => __('messages.something_went_wrong')
            ];
        }

        return redirect()->back()->with('status', $output);
    }

    /**
     * Show the specified resource.
     * @return Response
     */
    public function show()
    {
        return view('member::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @return Response
     */
    public function edit($id)
    {
        $member_group = MemberGroup::findOrFail($id);

        return view('member::settings.member_group.edit')->with(compact('member_group'));
    }

    /**
     * Update the specified resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function update(Request $request, $id)
    {
        try {
            $data = $request->except('_token', '_method');
            $data['date'] = !empty($data['date']) ? Carbon::parse($data['date'])->format('Y-m-d') : date('Y-m-d');

            MemberGroup::where('id', $id)->update($data);
            $output = [
                'success' => true,
                'tab' => 'member_group',
                'msg' => __('member::lang.member_group_update_success')
            ];
        } catch (\Exception $e) {
            Log::emergency('File: ' . $e->getFile() . 'Line: ' . $e->getLine() . 'Message: ' . $e->getMessage());
            $output = [
                'success' => false,
                'tab' => 'member_group',
                'msg' => __('messages.something_went_wrong')
            ];
        }

        return redirect()->back()->with('status', $output);
    }

    /**
     * Remove the specified resource from storage.
     * @return Response
     */
    public function destroy($id)
    {
        try {
            MemberGroup::where('id', $id)->delete();
            $output = [
                'success' => true,
                'msg' => __('member::lang.member_group_delete_success')
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
}
