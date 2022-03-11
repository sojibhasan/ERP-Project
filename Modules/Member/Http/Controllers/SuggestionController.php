<?php

namespace Modules\Member\Http\Controllers;

use App\Member;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Modules\Member\Entities\Balamandalaya;
use Modules\Member\Entities\ServiceArea;
use Modules\Member\Entities\Suggestion;
use Yajra\DataTables\Facades\DataTables;

class SuggestionController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        $business_id = request()->session()->get('business.id');
        $bala_mandalaya_areas = Balamandalaya::pluck('balamandalaya', 'id');
        $service_areas = ServiceArea::pluck('service_area', 'id');
        $name_of_suggestions = Suggestion::pluck('heading', 'id');
        $details_of_suggestions = Suggestion::pluck('details', 'id');
        $area_which_involved = Suggestion::pluck('area_name', 'id');
        $state_of_urgencies  = Suggestion::getStateOfUrgenciesArray();
        $solution_givens = Suggestion::getSolutionGivenArray();

        if (request()->ajax()) {
            $suggestions = Suggestion::leftjoin('balamandalayas', 'suggestions.balamandalaya_id', 'balamandalayas.id')
                ->leftjoin('service_areas', 'suggestions.service_area_id', 'service_areas.id')
                ->leftjoin('members', 'suggestions.member_id', 'members.id')
                ->select([
                    'suggestions.*',
                    'service_areas.service_area',
                    'balamandalayas.balamandalaya',
                    'members.name as member_name'
                ]);

            if (!empty(request()->balamandalaya_id)) {
                $suggestions->where('balamandalaya_id', request()->balamandalaya_id);
            }
            if (!empty(request()->service_area_id)) {
                $suggestions->where('service_area_id', request()->service_area_id);
            }
            if (!empty(request()->heading)) {
                $suggestions->where('heading', request()->heading);
            }
            if (!empty(request()->details)) {
                $suggestions->where('details', request()->details);
            }
            if (!empty(request()->is_common_problem)) {
                $suggestions->where('is_common_problem', request()->is_common_problem);
            }
            if (!empty(request()->area_name)) {
                $suggestions->where('area_name', request()->area_name);
            }
            if (!empty(request()->state_of_urgency)) {
                $suggestions->where('state_of_urgency', request()->state_of_urgency);
            }
            if (!empty(request()->solution_given)) {
                $suggestions->where('solution_given', request()->solution_given);
            }
            if (!empty(request()->start_date) && !empty(request()->end_date)) {
                $suggestions->whereDate('suggestions.date', '>=', request()->start_date);
                $suggestions->whereDate('suggestions.date', '<=', request()->end_date);
            }
            if (Suggestion::checkMemberorNot()) {
                $suggestions->where('member_id', Auth::user()->id);
            }

            return DataTables::of($suggestions)
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
                        $html .= '<li><a href="#" data-href="' . action('\Modules\Member\Http\Controllers\SuggestionController@show', [$row->id]) . '" class="btn-modal" data-container=".suggestion_model"><i class="fa fa-eye"></i> ' . __("messages.view") . '</a></li>';
                        if (auth()->user()->can('update_status_of_issue')) {
                            $html .= '<li><a href="#" data-href="' . action("\Modules\Member\Http\Controllers\SuggestionController@getUpdateStatus", [$row->id]) . '" class="btn-modal" data-container=".suggestion_model"><i class="glyphicon glyphicon-edit"></i> ' . __("member::lang.change_status") . '</a></li>';
                        }

                        return $html;
                    }
                )
                ->editColumn('date', '{{@format_date($date)}}')
                ->editColumn('state_of_urgency', '{{ucfirst($state_of_urgency)}}')
                ->removeColumn('id')
                ->rawColumns(['action'])
                ->make(true);
        }


        return view('member::suggestion.index')->with(compact(
            'bala_mandalaya_areas',
            'service_areas',
            'name_of_suggestions',
            'details_of_suggestions',
            'area_which_involved',
            'state_of_urgencies',
            'solution_givens'
        ));
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create()
    {

        $balamandalayas = Balamandalaya::pluck('balamandalaya', 'id');
        $service_areas = ServiceArea::pluck('service_area', 'id');
        $state_of_urgencies = Suggestion::getStateOfUrgenciesArray();
        $solution_givens = Suggestion::getSolutionGivenArray();
        $members = Member::pluck('name', 'id');

        return view('member::suggestion.create')->with(compact(
            'balamandalayas',
            'state_of_urgencies',
            'solution_givens',
            'members',
            'service_areas'
        ));
    }

    /**
     * Store a newly created resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        try {
            $input = $request->except('_token');
            $input['date'] = !empty($input['date']) ? Carbon::parse($input['date'])->format('Y-m-d') : date('Y-m-d');

            if (Suggestion::checkMemberorNot()) {
                $input['member_id'] = Auth::user()->id;
            }

            //upload suggestion file
            if (!file_exists('./public/uploads/suggestion/' . $input['member_id'])) {
                mkdir('./public/uploads/suggestion/' . $input['member_id'], 0777, true);
            }

            if ($request->hasfile('upload_document')) {

                $file = $request->file('upload_document');
                $extension = $file->getClientOriginalExtension();
                $filename = time() . '.' . $extension;
                $file->move('public/uploads/suggestion/' . $input['member_id'], $filename);
                $uploadFileFicon = 'public/uploads/suggestion/' . $input['member_id'] . '/' . $filename;
                $input['upload_document'] = $uploadFileFicon;
            } else {
                $input['upload_document'] = '';
            }

            Suggestion::create($input);

            $output = [
                'success' => true,
                'msg' => __('member::lang.suggestion_create_success')
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
        $suggestion = Suggestion::leftjoin('balamandalayas', 'suggestions.balamandalaya_id', 'balamandalayas.id')
            ->leftjoin('service_areas', 'suggestions.service_area_id', 'service_areas.id')
            ->leftjoin('members', 'suggestions.member_id', 'members.id')
            ->where('suggestions.id', $id)->select(
                'suggestions.*',
                'balamandalayas.balamandalaya',
                'service_areas.service_area'
            )->first();

        return view('member::suggestion.show')->with(compact(
            'suggestion'
        ));
    }

    /**
     * Show the form for editing the specified resource.
     * @return Response
     */
    public function edit()
    {
        return view('member::edit');
    }

    /**
     * Update the specified resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function update(Request $request)
    {
    }

    /**
     * Remove the specified resource from storage.
     * @return Response
     */
    public function destroy()
    {
    }
    /**
     * change status specified resource from storage.
     * @return Response
     */
    public function getUpdateStatus($id)
    {
        $status_array = Suggestion::getStatusArray();
        $suggestion = Suggestion::findOrFail($id);
        $members = Member::pluck('name', 'id');

        return view('member::suggestion.update_status')->with(compact(
            'status_array',
            'suggestion',
            'members'
        ));
    }
    /**
     * change status specified resource from storage.
     * @return Response
     */
    public function postUpdateStatus($id)
    {
        try {
            Suggestion::where('id', $id)->update([
                'status' => request()->status,
                'assigned_to_member_id' => request()->assigned_to_member_id
            ]);
            $output = [
                'success' => true,
                'msg' => __('member::lang.status_update_success')
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
}
