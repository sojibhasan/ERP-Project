<?php

namespace Modules\SMS\Http\Controllers;

use App\Member;
use App\Utils\BusinessUtil;
use App\Utils\ModuleUtil;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Modules\Member\Entities\Balamandalaya;
use Modules\Member\Entities\GramasevaVasama;
use Modules\SMS\Entities\SmsList;
use Yajra\DataTables\Facades\DataTables;

class SMSController extends Controller
{
    protected $businessUtil;
    protected $moduleUtil;

    /**
     * Constructor
     *
     * @param Util $businessUtil
     * @return void
     */
    public function __construct(BusinessUtil $businessUtil, ModuleUtil $moduleUtil)
    {
        $this->businessUtil = $businessUtil;
        $this->moduleUtil =  $moduleUtil;
    }


    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        $business_id = request()->session()->get('business.id');
        if (request()->ajax()) {
            $sms_lists = SmsList::leftjoin('users', 'sms_lists.created_by', 'users.id')
                ->where('sms_lists.business_id', $business_id)
                ->select([
                    'sms_lists.*',
                    'users.username as user',
                ]);

            if (!empty(request()->user)) {
                $sms_lists->where('sms_lists.created_by', request()->user);
            }

            if (!empty(request()->start_date) && !empty(request()->end_date)) {
                $sms_lists->whereDate('sms_lists.created_at', '>=', request()->start_date);
                $sms_lists->whereDate('sms_lists.created_at', '<=', request()->end_date);
            }

            // $sms_lists->orderBy('sms_lists.created_at', 'desc');

            return DataTables::of($sms_lists)
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
                        $html .= '<li><a href="#" data-href="' . action("\Modules\SMS\Http\Controllers\SMSController@show", [$row->id]) . '" class="btn-modal" data-container=".sms_model"><i class="fa fa-external-link" aria-hidden="true"></i> ' . __("messages.view") . '</a></li>';
                        $html .= '<li><a href="#" data-href="' . action("\Modules\SMS\Http\Controllers\SMSController@showNumbers", [$row->id]) . '" class="btn-modal" data-container=".sms_model"><i class="fa fa-external-link" aria-hidden="true"></i> ' . __("sms::lang.view_numbers") . '</a></li>';

                        return $html;
                    }
                )
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('sms::list_sms.index');
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create()
    {
        $member_groups = Member::pluck('name', 'id');
        $gramseva_vasamas = GramasevaVasama::pluck('gramaseva_vasama', 'id');
        $balamandalas = Balamandalaya::pluck('balamandalaya', 'id');

        $timezone_list = $this->businessUtil->allTimeZones();

        return view('sms::list_sms.create')->with(compact(
            'member_groups',
            'gramseva_vasamas',
            'balamandalas',
            'timezone_list'
        ));
    }

    /**
     * Store a newly created resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        $business_id = request()->session()->get('business.id');
        try {
            $input = $request->except('_token');
            $input['business_id'] = $business_id;
            $input['schedule_date_time'] = !empty($input['schedule_date_time']) ? Carbon::parse($input['schedule_date_time'])->format('Y-m-d H:i:s') : null;
            $input['numbers'] = array_map('trim', array_filter(explode("\n", $input['numbers'])));
            if (!empty($input['remove_duplicates'])) {
                $input['numbers'] = array_unique($input['numbers']);
            }
            $input['count_numbers'] = count($input['numbers']);
            $input['created_by'] = Auth::user()->id;

            SmsList::create($input);

            $output = [
                'success' => true,
                'msg' => __('sms::lang.sms_create_success')
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
        $sms = SmsList::leftjoin('users', 'sms_lists.created_by', 'users.id')
            ->leftjoin('member_groups', 'sms_lists.member_group', 'member_groups.id')
            ->leftjoin('balamandalayas', 'sms_lists.balamandala', 'balamandalayas.id')
            ->leftjoin('gramaseva_vasamas', 'sms_lists.gramseva_vasama', 'gramaseva_vasamas.id')
            ->where('sms_lists.id', $id)
            ->select([
                'sms_lists.*',
                'users.username as user',
                'member_groups.member_group as member_group',
                'balamandalayas.balamandalaya as balamandala',
                'gramaseva_vasamas.gramaseva_vasama as gramseva_vasama'
            ])->first();

        return view('sms::list_sms.show')->with(compact('sms'));
    }

    /**
     * Show the form for editing the specified resource.
     * @return Response
     */
    public function edit()
    {
        return view('sms::edit');
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
     * View the specified resource from storage.
     * @return Response
     */
    public function showNumbers($id)
    {
        $numbers = SmsList::findOrFail($id)->numbers;

        return view('sms::list_sms.view_numbers')->with(compact('numbers'));
    }
}
