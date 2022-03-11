<?php

namespace Modules\TasksManagement\Http\Controllers;

use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Modules\TasksManagement\Entities\Note;
use Modules\TasksManagement\Entities\NoteGroup;
use Yajra\DataTables\Facades\DataTables;

class NoteController extends Controller
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
            $notes = Note::leftjoin('note_groups', 'notes.group_id', 'note_groups.id')
                ->where(function ($q) use ($user_id) {
                    $q->where('created_by', $user_id)->orWhereJsonContains('shared_with_users', strval($user_id));
                })
                ->select([
                    'notes.*', 'note_groups.name as note_group'
                ]);

            if (!empty(request()->note_group)) {
                $notes->where('group_id', request()->note_group);
            }
            if (!empty(request()->note_id)) {
                $notes->where('note_id', request()->note_id);
            }
            if (!empty(request()->note_heading)) {
                $notes->where('note_heading', trim(request()->note_heading));
            }

            $start_date = request()->start_date;
            $end_date = request()->end_date;
            if (!empty($start_date) && !empty($end_date)) {
                $notes->whereDate('date_and_time', '>=', $start_date)
                    ->whereDate('date_and_time', '<=', $end_date);
            }

            return DataTables::of($notes)
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

                        $html .= '<li><a href="#" data-href="' . action('\Modules\TasksManagement\Http\Controllers\NoteController@show', [$row->id]) . '" class="btn-modal" data-container=".note_model"><i class="fa fa-eye" aria-hidden="true"></i>' . __("messages.view") . '</a></li>';
                        $html .= '<li><a href="#" data-href="' . action('\Modules\TasksManagement\Http\Controllers\NoteController@edit', [$row->id]) . '" class="btn-modal" data-container=".note_model"><i class="glyphicon glyphicon-edit" aria-hidden="true"></i>' . __("messages.edit") . '</a></li>';
                        $html .= '<li><a href="#" data-href="' . action('\Modules\TasksManagement\Http\Controllers\NoteController@destroy', [$row->id]) . '" class="delete-note"><i class="glyphicon glyphicon-trash" aria-hidden="true"></i>' . __("messages.delete") . '</a></li>';

                        $html .=  '</ul></div>';
                        return $html;
                    }
                )
                ->editColumn('color', function ($row) {
                    return '<div style="height: 25px; width: 25px; background: ' . $row->color . '"></div>';
                })
                ->editColumn('shared_with_users', function ($row) {
                    $share_with = User::whereIn('id', $row->shared_with_users)->pluck('username')->toArray();
                    return $share_with;
                })
                ->editColumn('show_on_top_section', '{{ucfirst($show_on_top_section)}}')
                ->removeColumn('id')
                ->rawColumns(['action', 'color', 'shared_with_users'])
                ->make(true);
        }

        $notes = Note::where('created_by', Auth::user()->id)->where('show_on_top_section', 'yes')->get();

        $note_groups = NoteGroup::where('business_id', $business_id)->pluck('name', 'id');
        $note_ids = Note::where('business_id', $business_id)->pluck('note_id', 'note_id');
        $note_headings = Note::where('business_id', $business_id)->pluck('note_heading', 'note_heading');

        return view('tasksmanagement::notes.index')->with(compact('notes', 'note_groups', 'note_ids', 'note_headings'));
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create()
    {
        $business_id = request()->session()->get('business.id');
        $note_groups = NoteGroup::where('business_id', $business_id)->pluck('name', 'id');
        $users = User::where('business_id', $business_id)->pluck('username', 'id');

        $count = Note::whereNull('group_id')->count();
        $count++;

        $note_id = $count;

        return view('tasksmanagement::notes.create')->with(compact('note_groups', 'users', 'note_id'));
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
            $input['note_details'] = trim($input['note_details']);
            $input['business_id'] = $business_id;
            $input['created_by'] = Auth::user()->id;
            if (empty($request->shared_with_users)) {
                $input['shared_with_users'] = [];
            }
            $input['date_and_time'] = !empty($input['date_and_time']) ? Carbon::parse($input['date_and_time'])->format('Y-m-d H:i:s') : Carbon::now();
            Note::create($input);
            $output = [
                'success' => true,
                'msg' => __('tasksmanagement::lang.note_create_success')
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
        $note = Note::leftjoin('note_groups', 'notes.group_id', 'note_groups.id')
            ->leftjoin('users', 'notes.created_by', 'users.id')
            ->where('notes.id', $id)
            ->select([
                'notes.*', 'note_groups.name as note_group', 'users.username as created_by'
            ])->first();

        $share_with = implode(',',  User::whereIn('id', $note->shared_with_users)->pluck('username')->toArray());

        return view('tasksmanagement::notes.show')->with(compact('note', 'share_with'));
    }

    /**
     * Show the form for editing the specified resource.
     * @return Response
     */
    public function edit($id)
    {
        $business_id = request()->session()->get('business.id');
        $note_groups = NoteGroup::where('business_id', $business_id)->pluck('name', 'id');
        $users = User::where('business_id', $business_id)->pluck('username', 'id');

        $note = Note::findOrFail($id);

        return view('tasksmanagement::notes.edit')->with(compact('note_groups', 'users', 'note'));
    }

    /**
     * Update the specified resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function update(Request $request, $id)
    {
        $business_id = $request->session()->get('business.id');

        try {
            $input = $request->except('_token', '_method');

            $input['note_details'] = trim($input['note_details']);
            if (empty($request->shared_with_users)) {
                $input['shared_with_users'] = [];
            } else {
                $input['shared_with_users'] = ($input['shared_with_users']);
            }
            $input['date_and_time'] = !empty($input['date_and_time']) ? Carbon::parse($input['date_and_time'])->format('Y-m-d H:i:s') : Carbon::now();
            $note  = Note::findOrFail($id);

            $note->date_and_time = $input['date_and_time'];
            $note->group_id = $input['group_id'];
            $note->note_id = $input['note_id'];
            $note->note_heading = $input['note_heading'];
            $note->note_details = $input['note_details'];
            $note->note_footer = $input['note_footer'];
            $note->show_on_top_section = $input['show_on_top_section'];
            $note->color = $input['color'];
            $note->shared_with_users = $input['shared_with_users'];
            $note->save();

            $output = [
                'success' => true,
                'msg' => __('tasksmanagement::lang.note_update_success')
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
            Note::where('id', $id)->delete();
            $output = [
                'success' => true,
                'msg' => __('tasksmanagement::lang.note_delete_success')
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

    public function getNoteId(Request $request)
    {
        $note_group_id = $request->group_id;
        if (!empty($note_group_id)) {
            $group = NoteGroup::findOrFail($note_group_id);

            $count = Note::where('group_id', $note_group_id)->count();
            $count++;

            $note_id = $group->prefix . $count;
        } else {
            $count = Note::whereNull('group_id')->count();
            $count++;

            $note_id = $count;
        }

        return ['note_id' => $note_id];
    }
}
