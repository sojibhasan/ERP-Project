<?php

namespace Modules\TasksManagement\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Modules\TasksManagement\Entities\Priority;
use Yajra\DataTables\Facades\DataTables;

class PriorityController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        $business_id = request()->session()->get('business.id');
        if (request()->ajax()) {
            $priority = Priority::leftjoin('users', 'priorities.added_by', 'users.id')
            ->where('priorities.business_id', $business_id)
                ->select([
                    'priorities.*',
                    'users.username as added_by'
                ]);

            return DataTables::of($priority)
                ->addColumn(
                    'action',
                    '
                    <button data-href="{{action(\'\Modules\TasksManagement\Http\Controllers\PriorityController@edit\',[$id])}}" data-container=".priority_model" class="btn btn-xs btn-primary btn-modal edit_btn"><i class="glyphicon glyphicon-edit"></i> @lang("messages.edit")</button>
                    <button data-href="{{action(\'\Modules\TasksManagement\Http\Controllers\PriorityController@destroy\',[$id])}}" class="btn btn-xs btn-danger priority_delete"><i class="glyphicon glyphicon-trash"></i> @lang("messages.delete")</button>
                   
                    '
                )
                ->editColumn('date', '{{@format_date($date)}}')
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
        return view('tasksmanagement::settings.priority.create');
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
            foreach ($request->priority as $pt) {
                $data['business_id'] = $business_id;
                $data['date'] = !empty($pt['date']) ? Carbon::parse($pt['date'])->format('Y-m-d') : date('Y-m-d');
                $data['name'] = $pt['name'];
                $data['added_by'] = Auth::user()->id;
                Priority::create($data);
            }

            $output = [
                'success' => true,
                'tab' => 'priority',
                'msg' => __('tasksmanagement::lang.priority_create_success')
            ];
        } catch (\Exception $e) {
            Log::emergency('File: ' . $e->getFile() . 'Line: ' . $e->getLine() . 'Message: ' . $e->getMessage());
            $output = [
                'success' => false,
                'tab' => 'priority',
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
        $priority = Priority::findOrFail($id);
        return view('tasksmanagement::settings.priority.edit')->with(compact('priority'));
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

            Priority::where('id', $id)->update($data);
            $output = [
                'success' => true,
                'tab' => 'priority',
                'msg' => __('tasksmanagement::lang.priority_update_success')
            ];
        } catch (\Exception $e) {
            Log::emergency('File: ' . $e->getFile() . 'Line: ' . $e->getLine() . 'Message: ' . $e->getMessage());
            $output = [
                'success' => false,
                'tab' => 'priority',
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
            Priority::where('id', $id)->delete();
            $output = [
                'success' => true,
                'msg' => __('tasksmanagement::lang.priority_delete_success')
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
