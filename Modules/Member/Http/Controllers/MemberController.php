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
use Modules\Member\Entities\GramasevaVasama;
use Modules\Member\Entities\MemberGroup;
use Yajra\DataTables\Facades\DataTables;

class MemberController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        $business_id = request()->session()->get('business.id');
        if (request()->ajax()) {
            $member = Member::leftjoin('gramaseva_vasamas', 'members.gramasevaka_area', 'gramaseva_vasamas.id')
                ->leftjoin('balamandalayas', 'members.bala_mandalaya_area', 'balamandalayas.id')
                ->leftjoin('member_groups', 'members.member_group', 'member_groups.id')
                ->select([
                    'members.*',
                    'gramaseva_vasamas.gramaseva_vasama as gramasevaka_area',
                    'balamandalayas.balamandalaya as bala_mandalaya_area',
                    'member_groups.member_group'
                ]);

            if (!empty(request()->username)) {
                $member->where('username', request()->username);
            }
            if (!empty(request()->town)) {
                $member->where('town', request()->town);
            }
            if (!empty(request()->district)) {
                $member->where('district', request()->district);
            }
            if (!empty(request()->gramasevaka_area)) {
                $member->where('gramasevaka_area', request()->gramasevaka_area);
            }
            if (!empty(request()->bala_mandalaya_area)) {
                $member->where('bala_mandalaya_area', request()->bala_mandalaya_area);
            }
            if (!empty(request()->member_group)) {
                $member->where('members.member_group', request()->member_group);
            }

            return DataTables::of($member)
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
                        $html .= '<li><a href="#" data-href="' . action("\Modules\Member\Http\Controllers\MemberController@edit", [$row->id]) . '" class="btn-modal" data-container=".member_model"><i class="glyphicon glyphicon-edit"></i> ' . __("messages.edit") . '</a></li>';
                        $html .= '<li><a href="' . action('\Modules\Member\Http\Controllers\MemberController@show', [$row->id]) . "?view=member_info" . '"><i class="fa fa-eye"></i> ' . __("messages.view") . '</a></li>';
                        $html .= '<li><a href="' . action('\Modules\Member\Http\Controllers\MemberController@show', [$row->id]) . "?view=documents_and_notes" . '"><i class="fa fa-paperclip"></i> ' . __("lang_v1.documents_and_notes") . '</a></li>';

                        return $html;
                    }
                )
                ->editColumn('date_of_birth', '{{@format_date($date_of_birth)}}')
                ->removeColumn('id')
                ->rawColumns(['action'])
                ->make(true);
        }

        $gramasevaka_areas = GramasevaVasama::pluck('gramaseva_vasama', 'id');
        $bala_mandalaya_areas = Balamandalaya::pluck('balamandalaya', 'id');
        $member_groups = MemberGroup::pluck('member_group', 'id');
        $towns = Member::distinct('town')->pluck('town', 'town');
        $districts = Member::distinct('district')->pluck('district', 'district');
        $usernames = Member::distinct('username')->pluck('username', 'username');

        return view('member::member.index')->with(compact(
            'gramasevaka_areas',
            'bala_mandalaya_areas',
            'member_groups',
            'towns',
            'districts',
            'usernames'
        ));
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create()
    {
        $gramasevaka_areas = GramasevaVasama::pluck('gramaseva_vasama', 'id');
        $bala_mandalaya_areas = Balamandalaya::pluck('balamandalaya', 'id');
        $member_groups = MemberGroup::pluck('member_group', 'id');
        $member_count = Member::count() + 1;
        $member_username = 'MEM' . $member_count;

        return view('member::member.create')->with(compact(
            'gramasevaka_areas',
            'bala_mandalaya_areas',
            'member_groups',
            'member_count',
            'member_username'
        ));
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
            $data['give_away_gifts'] = $request->give_away_gifts;
            $data['date_of_birth'] = !empty($data['date_of_birth']) ? Carbon::parse($data['date_of_birth'])->format('Y-m-d') : date('Y-m-d');
            Member::create($data);

            $output = [
                'success' => true,
                'msg' => __('member::lang.member_create_success')
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
        $member = Member::findOrFail($id);

        $member_dropdown = Member::pluck('name', 'id');

        //get contact view type : ledger, notes etc.
        $view_type = request()->get('view');
        if (is_null($view_type)) {
            $view_type = 'member_info';
        }

        return view('member::member.show')->with(compact(
            'member',
            'member_dropdown',
            'view_type'
        ));
    }

    /**
     * Show the form for editing the specified resource.
     * @return Response
     */
    public function edit($id)
    {
        $member = Member::findOrFail($id);

        $gramasevaka_areas = GramasevaVasama::pluck('gramaseva_vasama', 'id');
        $bala_mandalaya_areas = Balamandalaya::pluck('balamandalaya', 'id');
        $member_groups = MemberGroup::pluck('member_group', 'id');

        return view('member::member.edit')->with(compact('member', 'gramasevaka_areas', 'bala_mandalaya_areas', 'member_groups'));
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
            $data['date_of_birth'] = !empty($data['date_of_birth']) ? Carbon::parse($data['date_of_birth'])->format('Y-m-d') : null;

            Member::where('id', $id)->update($data);
            $output = [
                'success' => true,
                'msg' => __('member::lang.member_update_success')
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
            Member::where('id', $id)->delete();
            $output = [
                'success' => true,
                'msg' => __('member::lang.member_delete_success')
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

    public function home()
    {
        return view('member::member.home');
    }

    public function getProfile()
    {
        $member = Member::findOrFail(Auth::user()->id);
        $gramasevaka_areas = GramasevaVasama::pluck('gramaseva_vasama', 'id');
        $bala_mandalaya_areas = Balamandalaya::pluck('balamandalaya', 'id');
        $member_groups = MemberGroup::pluck('member_group', 'id');

        return view('member::member.profile')->with(compact(
            'member', 'gramasevaka_areas', 'bala_mandalaya_areas', 'member_groups'
        ));
    }

    public function updateProfile(Request $request, $id)
    {
        try {
            $data = $request->except('_token', '_method');
            $data['date_of_birth'] = !empty($data['date_of_birth']) ? Carbon::parse($data['date_of_birth'])->format('Y-m-d') : null;

            Member::where('id', $id)->update($data);
            $output = [
                'success' => true,
                'msg' => __('member::lang.profile_update_success')
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
