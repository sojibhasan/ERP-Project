<?php

namespace Modules\TasksManagement\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Modules\TasksManagement\Entities\NoteGroup;
use Yajra\DataTables\Facades\DataTables;

class NoteGroupController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        $business_id = request()->session()->get('business.id');
        if (request()->ajax()) {
            $note_groups = NoteGroup::where('business_id', $business_id)
                ->select([
                    'note_groups.*'
                ]);

            return DataTables::of($note_groups)
                ->addColumn(
                    'action',
                    '
                    <button data-href="{{action(\'\Modules\TasksManagement\Http\Controllers\NoteGroupController@edit\',[$id])}}" data-container=".note_group_model" class="btn btn-xs btn-primary btn-modal edit_btn"><i class="glyphicon glyphicon-edit"></i> @lang("messages.edit")</button>
                    <button data-href="{{action(\'\Modules\TasksManagement\Http\Controllers\NoteGroupController@destroy\',[$id])}}" class="btn btn-xs btn-danger note_group_delete"><i class="glyphicon glyphicon-trash"></i> @lang("messages.delete")</button>
                   
                    '
                )
                ->editColumn('color', function ($row) {
                    return '<div style="height: 25px; width: 25px; background: ' . $row->color . '"></div>';
                })
                ->removeColumn('id')
                ->rawColumns(['action', 'color'])
                ->make(true);
        }
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create()
    {
        return view('tasksmanagement::settings.note_groups.create');
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
            NoteGroup::create($data);

            $output = [
                'success' => true,
                'msg' => __('tasksmanagement::lang.group_create_success')
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
     * @return Response
     */
    public function show()
    {
        return view('tasksmanagement::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @return Response
     */
    public function edit($id)
    {
        $group = NoteGroup::findOrFail($id);
        return view('tasksmanagement::settings.note_groups.edit')->with(compact('group'));
    }

    /**
     * Update the specified resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function update(Request $request, $id)
    {
        try {
            $data['name'] = $request->input('name');
            $data['color'] = $request->input('color');
            $data['prefix'] = $request->input('prefix');

            NoteGroup::where('id', $id)->update($data);
            $output = [
                'success' => true,
                'msg' => __('tasksmanagement::lang.group_update_success')
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
     * @return Response
     */
    public function destroy($id)
    {
        try {
            NoteGroup::where('id', $id)->delete();
            $output = [
                'success' => true,
                'msg' => __('tasksmanagement::lang.group_delete_success')
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
