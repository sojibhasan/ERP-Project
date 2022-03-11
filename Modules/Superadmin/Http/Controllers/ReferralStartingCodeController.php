<?php

namespace Modules\Superadmin\Http\Controllers;

use App\Utils\ModuleUtil;
use Facade\Ignition\Tabs\Tab;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Modules\Superadmin\Entities\ReferralGroup;
use Modules\Superadmin\Entities\ReferralStartingCode;
use Yajra\DataTables\Facades\DataTables;

class ReferralStartingCodeController extends Controller
{
    /**
     * All Utils instance.
     *
     */
    protected $moduleUtil;

    /**
     * Constructor
     *
     * @return void
     */
    public function __construct(ModuleUtil $moduleUtil)
    {
        $this->moduleUtil = $moduleUtil;
    }

    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index()
    {
        if (request()->ajax()) {
            $referrals = ReferralStartingCode::leftjoin('referral_groups', 'referral_starting_codes.referral_group', 'referral_groups.id')->select('referral_starting_codes.*', 'referral_groups.group_name');

            return DataTables::of($referrals)
                ->addColumn('action', function ($row) {
                    $html = '';
                    $html = '<div class="btn-group">
                                <button type="button" class="btn btn-info dropdown-toggle btn-xs" 
                                    data-toggle="dropdown" aria-expanded="false">' .
                        __("messages.actions") .
                        '<span class="caret"></span><span class="sr-only">Toggle Dropdown
                                    </span>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-left" role="menu">';

                    if (auth()->user()->can('superadmin')) {
                        $html .= '<li><a data-href="' . action('\Modules\Superadmin\Http\Controllers\ReferralStartingCodeController@edit', [$row->id]) . '" data-container=".view_modal" class="btn-modal edit_starting_code_button"><i class="glyphicon glyphicon-edit"></i> ' . __("messages.edit") . '</a></li>';
                        $html .= '<li><a data-href="' . action('\Modules\Superadmin\Http\Controllers\ReferralStartingCodeController@destroy', [$row->id]) . '" class="delete_button"><i class="fa fa-trash"></i> ' . __("messages.delete") . '</a></li>';
                    }

                    $html .= '</ul></div>';
                    return $html;
                })
                ->editColumn('date', '{{@format_date($date)}}')

                ->removeColumn('id')
                ->rawColumns([])
                ->make(true);
        }
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        $referral_groups = ReferralGroup::pluck('group_name', 'id');

        return view('superadmin::referral.partials.create_referral_code')->with(compact(
            'referral_groups'
        ));
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request)
    {
        try {
            $inputs = $request->except('_token');
            $inputs['date'] = $this->moduleUtil->uf_date($inputs['date']);

            ReferralStartingCode::create($inputs);
            $output = [
                'success' => true,
                'tab' => 'referral_stating_code',
                'msg' => __('lang_v1.success')
            ];
        } catch (\Exception $e) {
            Log::emergency('File: ' . $e->getFile() . 'Line: ' . $e->getLine() . 'Message: ' . $e->getMessage());
            $output = [
                'success' => false,
                'tab' => 'referral_stating_code',
                'msg' => __('messages.something_went_wrong')
            ];
        }

        return redirect()->back()->with('status', $output);
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {
        return view('superadmin::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        $starting_code = ReferralStartingCode::find($id);
        $referral_groups = ReferralGroup::pluck('group_name', 'id');

        return view('superadmin::referral.partials.edit_referral_code')->with(compact(
            'referral_groups',
            'starting_code'
        ));
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(Request $request, $id)
    {
        try {
            $inputs = $request->except('_token', '_method');
            $inputs['date'] = $this->moduleUtil->uf_date($inputs['date']);

            ReferralStartingCode::where('id', $id)->update($inputs);
            $output = [
                'success' => true,
                'tab' => 'referral_stating_code',
                'msg' => __('lang_v1.success')
            ];
        } catch (\Exception $e) {
            Log::emergency('File: ' . $e->getFile() . 'Line: ' . $e->getLine() . 'Message: ' . $e->getMessage());
            $output = [
                'success' => false,
                'tab' => 'referral_stating_code',
                'msg' => __('messages.something_went_wrong')
            ];
        }

        return redirect()->back()->with('status', $output);
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy($id)
    {
        try {
            ReferralStartingCode::where('id', $id)->delete();
            $output = [
                'success' => true,
                'tab' => 'referral_stating_code',
                'msg' => __('lang_v1.success')
            ];
        } catch (\Exception $e) {
            Log::emergency('File: ' . $e->getFile() . 'Line: ' . $e->getLine() . 'Message: ' . $e->getMessage());
            $output = [
                'success' => false,
                'tab' => 'referral_stating_code',
                'msg' => __('messages.something_went_wrong')
            ];
        }

        return $output;
    }
}
