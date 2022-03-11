<?php

namespace Modules\TasksManagement\Http\Controllers;

use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Modules\TasksManagement\Entities\Priority;
use Modules\TasksManagement\Entities\Task;
use Modules\TasksManagement\Entities\TaskGroup;
use Yajra\DataTables\Facades\DataTables;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        $business_id = request()->session()->get('business.id');
        $user_id = Auth::user()->id;
        if (request()->ajax()) {
            $tasks = Task::leftjoin('task_groups', 'tasks.group_id', 'task_groups.id')
                ->leftjoin('users', 'tasks.created_by', 'users.id')
                ->where(function ($q) use ($user_id) {
                    $q->where('created_by', $user_id)->orWhereJsonContains('members', strval($user_id));
                })
                ->select([
                    'tasks.*', 'task_groups.name as task_group', 'users.username as user_created'
                ]);

            if (!empty(request()->task_group)) {
                $tasks->where('group_id', request()->task_group);
            }
            if (!empty(request()->task_id)) {
                $tasks->where('task_id', request()->task_id);
            }
            if (!empty(request()->task_heading)) {
                $tasks->where('task_heading', trim(request()->task_heading));
            }

            $start_date = request()->start_date;
            $end_date = request()->end_date;
            if (!empty($start_date) && !empty($end_date)) {
                $tasks->whereDate('date_and_time', '>=', $start_date)
                    ->whereDate('date_and_time', '<=', $end_date);
            }
            $status_array = Task::getStatusArray();

            return DataTables::of($tasks)
                ->addColumn(
                    'action',
                    function ($row) {
                        $html = '<div class="btn-group">
                    <button type="button" class="btn btn-info dropdown-toggle btn-xs" 
                        data-toggle="dropdown" aria-expanded="false">' .
                            __("messages.actions") .
                            '<span class="caret"></span><span class="sr-only">Toggle Dropdown
                        </span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-left" role="menu">';

                        // $html .= '<li><a href="#" data-href="' . action('\Modules\TasksManagement\Http\Controllers\TaskController@show', [$row->id]) . '" class="btn-modal" data-container=".task_model"><i class="fa fa-eye" aria-hidden="true"></i>' . __("messages.view") . '</a></li>';
                        $html .= '<li><a href="#" data-href="' . action('\Modules\TasksManagement\Http\Controllers\TaskController@edit', [$row->id]) . '" class="btn-modal" data-container=".task_model"><i class="glyphicon glyphicon-edit" aria-hidden="true"></i>' . __("messages.edit") . '</a></li>';
                        $html .= '<li><a href="#" data-href="' . action('\Modules\TasksManagement\Http\Controllers\TaskController@destroy', [$row->id]) . '" class="delete-task"><i class="glyphicon glyphicon-trash" aria-hidden="true"></i>' . __("messages.delete") . '</a></li>';

                        $html .=  '</ul></div>';
                        return $html;
                    }
                )
                ->editColumn('date_and_time',  '{{@format_date($date_and_time)}}')
                ->editColumn('status',  function ($row) use ($status_array) {
                    return $status_array[$row->status];
                })
                ->editColumn('members', function ($row) {
                    $share_with = User::whereIn('id', $row->members)->pluck('username')->toArray();
                    return $share_with;
                })
                ->removeColumn('id')
                ->rawColumns(['action', 'color', 'members'])
                ->make(true);
        }

        $tasks = Task::where('created_by', Auth::user()->id)->get();
        $task_groups = TaskGroup::where('business_id', $business_id)->pluck('name', 'id');
        $task_ids = Task::where('business_id', $business_id)->pluck('task_id', 'task_id');

        $task_headings = Task::where('business_id', $business_id)->pluck('task_heading', 'task_heading');


        return view('tasksmanagement::tasks.index')->with(compact('tasks', 'task_groups', 'task_ids', 'task_headings'));
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create()
    {
        $business_id = request()->session()->get('business.id');
        $task_groups = TaskGroup::where('business_id', $business_id)->pluck('name', 'id');
        $users = User::where('business_id', $business_id)->pluck('username', 'id');

        $count = Task::whereNull('group_id')->count();
        $count++;

        $task_id = $count;

        $priorities = Priority::where('business_id', $business_id)->pluck('name', 'id');
        $status_array = Task::getStatusArray();
        $reminders_array = Task::getReminderArray();

        return view('tasksmanagement::tasks.create')->with(compact('task_groups', 'users', 'task_id', 'priorities', 'status_array', 'reminders_array'));
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
            $input = $request->except('_token');
            $input['task_details'] = trim($input['task_details']);
            $input['business_id'] = $business_id;
            $input['created_by'] = Auth::user()->id;
            if (empty($request->members)) {
                $input['members'] = [];
            }
            if (empty($request->reminder)) {
                $input['reminder'] = [];
            }
            $input['start_date'] = !empty($input['start_date']) ? Carbon::parse($input['start_date'])->format('Y-m-d') : Carbon::now();
            $input['end_date'] = !empty($input['end_date']) ? Carbon::parse($input['end_date'])->format('Y-m-d') : Carbon::now();
            $input['date_and_time'] = !empty($input['date_and_time']) ? Carbon::parse($input['date_and_time'])->format('Y-m-d H:i:s') : Carbon::now();
            $task = Task::create($input);
            $priority = Priority::findOrFail($request->priority_id);
            $task->priority_name = !empty($priority) ? $priority->name : null;
            $task->save();

            $output = [
                'success' => true,
                'msg' => __('tasksmanagement::lang.task_create_success')
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
    public function show($id)
    {
        $task = Task::leftjoin('task_groups', 'tasks.group_id', 'task_groups.id')
            ->leftjoin('users', 'tasks.created_by', 'users.id')
            ->where('tasks.id', $id)
            ->select([
                'tasks.*', 'task_groups.name as task_group', 'users.username as created_by'
            ])->first();

        $share_with = implode(',',  User::whereIn('id', $task->members)->pluck('username')->toArray());

        return view('tasksmanagement::tasks.show')->with(compact('task', 'share_with'));
    }

    /**
     * Show the form for editing the specified resource.
     * @return Response
     */
    public function edit($id)
    {
        $business_id = request()->session()->get('business.id');
        $task_groups = TaskGroup::where('business_id', $business_id)->pluck('name', 'id');
        $users = User::where('business_id', $business_id)->pluck('username', 'id');
        $priorities = Priority::where('business_id', $business_id)->pluck('name', 'id');
        $status_array = Task::getStatusArray();
        $reminders_array = Task::getReminderArray();

        $task = Task::findOrFail($id);

        return view('tasksmanagement::tasks.edit')->with(compact('task_groups', 'users', 'task', 'priorities', 'status_array', 'reminders_array'));
    }

    /**
     * Update the specified resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function update(Request $request, $id)
    {

        try {
            $input = $request->except('_token', '_method');
            $input['task_details'] = trim($input['task_details']);
            // print_r(  $input['task_details']); die();
            if (empty($request->members)) {
                $input['members'] = [];
            } else {
                $input['members'] = $input['members'];
            }
            if (empty($request->reminder)) {
                $input['reminder'] = [];
            } else {
                $input['reminder'] = ($input['reminder']);
            }
            $input['start_date'] = !empty($input['start_date']) ? Carbon::parse($input['start_date'])->format('Y-m-d') : Carbon::now();
            $input['end_date'] = !empty($input['end_date']) ? Carbon::parse($input['end_date'])->format('Y-m-d') : Carbon::now();
            $input['date_and_time'] = !empty($input['date_and_time']) ? Carbon::parse($input['date_and_time'])->format('Y-m-d H:i:s') : Carbon::now();
            $priority = Priority::findOrFail($request->priority_id);
            $task = Task::findOrFail($id);
            $task->task_heading = $input['task_heading'];
            $task->task_footer = $input['task_footer'];
            $task->task_details = $input['task_details'];
            $task->group_id = $input['group_id'];
            $task->priority_id = $input['priority_id'];
            $task->members = $input['members'];
            $task->status = $input['status'];
            $task->start_date = $input['start_date'];
            $task->end_date = $input['end_date'];
            $task->estimated_hours = $input['estimated_hours'];
            $task->reminder = $input['reminder'];
            $task->color = $input['color'];
            $task->priority_name = !empty($priority) ? $priority->name : null;

            $task->save();

            $output = [
                'success' => true,
                'msg' => __('tasksmanagement::lang.task_update_success')
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
            Task::where('id', $id)->delete();
            $output = [
                'success' => true,
                'msg' => __('tasksmanagement::lang.task_delete_success')
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

    public function getTaskId(Request $request)
    {
        $task_group_id = $request->group_id;
        if (!empty($task_group_id)) {
            $group = TaskGroup::findOrFail($task_group_id);

            $count = Task::where('group_id', $task_group_id)->count();
            $count++;

            $task_id = $group->prefix . $count;
        } else {
            $count = Task::whereNull('group_id')->count();
            $count++;

            $task_id = $count;
        }

        return ['task_id' => $task_id];
    }
}
