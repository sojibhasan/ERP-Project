<?php

namespace Modules\Superadmin\Http\Controllers;

use App\Utils\BusinessUtil;
use App\Utils\ModuleUtil;
use Carbon\Carbon;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Modules\Superadmin\Entities\IncomeMethod;
use Modules\Superadmin\Entities\ReferralGroup;
use Yajra\DataTables\Facades\DataTables;

class IncomeMethodController extends Controller
{
    /**
     * All Utils instance.
     *
     */
    protected $businessUtil;
    protected $moduleUtil;

    /**
     * Constructor
     *
     * @param ProductUtils $product
     * @return void
     */
    public function __construct(BusinessUtil $businessUtil, ModuleUtil $moduleUtil)
    {
        $this->businessUtil = $businessUtil;
        $this->moduleUtil = $moduleUtil;
    }
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index()
    {
        if (request()->ajax()) {
            $defaults = ['New signup', 'Subscription'];
            $income_methods_default = IncomeMethod::whereIn('income_method', $defaults)->get();
            $agent_group = ReferralGroup::where('group_name', 'Agent')->first();

            if ($income_methods_default->count() === 0) {
                foreach ($defaults as $default) {
                    IncomeMethod::create([
                        'date' => Carbon::now(),
                        'referral_group_id' => !empty($agent_group) ? $agent_group->id : null,
                        'income_method' => $default,
                        'status' => 'enable',
                        'income_type' => 'fixed',
                        'value' => 0,
                        'minimum_new_signups' => 1,
                        'minimum_active_subscriptions' => 1,
                        'comission_eligible_conditions' => null,
                        'created_by' => Auth::user()->id
                    ]);
                }
            }
            $income_methods = IncomeMethod::leftjoin('referral_groups', 'income_methods.referral_group_id', 'referral_groups.id')
                ->select('income_methods.*', 'referral_groups.group_name');

            $comission_eligible_conditions = [
                'minimum_signups_only' => 'Minimum Signups Only',
                'minimum_subscription_only' => 'Minimum Subscription Only',
                'both' => 'Both'
            ];
            return DataTables::of($income_methods)

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
                        $html .= '<li><a data-href="' . action('\Modules\Superadmin\Http\Controllers\IncomeMethodController@edit', [$row->id]) . '" data-container=".view_modal" class="btn-modal edit_starting_code_button"><i class="glyphicon glyphicon-edit"></i> ' . __("messages.edit") . '</a></li>';
                        $html .= '<li><a data-href="' . action('\Modules\Superadmin\Http\Controllers\IncomeMethodController@destroy', [$row->id]) . '" class="delete_button"><i class="fa fa-trash"></i> ' . __("messages.delete") . '</a></li>';
                    }

                    $html .= '</ul></div>';
                    return $html;
                })
                ->editColumn('income_type', '{{ucfirst($income_type)}}')
                ->editColumn('status', '{{ucfirst($status)}}')
                ->editColumn('date', '{{@format_date($date)}}')
                ->editColumn('value', '{{@num_format($value)}}')
                ->editColumn('minimum_new_signups', '{{@num_format($minimum_new_signups)}}')
                ->editColumn('minimum_active_subscriptions', '{{@num_format($minimum_active_subscriptions)}}')
                ->editColumn('comission_eligible_conditions', function ($row) use ($comission_eligible_conditions) {
                    if (!empty($row->comission_eligible_conditions)) {
                        return $comission_eligible_conditions[$row->comission_eligible_conditions];
                    }
                    return '';
                })

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

        return view('superadmin::income_method.create')->with(compact(
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

            foreach ($inputs['income_method'] as $key => $item) {
                $data = [
                    'date' => $this->moduleUtil->uf_date($inputs['date']),
                    'referral_group_id' => $inputs['referral_group_id'],
                    'income_method' => $item,
                    'status' => $inputs['status'][$key],
                    'income_type' => $inputs['income_type'],
                    'value' => $inputs['value'],
                    'minimum_new_signups' => $inputs['minimum_new_signups'],
                    'minimum_active_subscriptions' => $inputs['minimum_active_subscriptions'],
                    'comission_eligible_conditions' => $inputs['comission_eligible_conditions'],
                    'created_by' => Auth::user()->id
                ];

                IncomeMethod::create($data);
            }

            $output = [
                'success' => true,
                'tab' => 'income_method',
                'msg' => __('lang_v1.success')
            ];
        } catch (\Exception $e) {
            Log::emergency('File: ' . $e->getFile() . 'Line: ' . $e->getLine() . 'Message: ' . $e->getMessage());
            $output = [
                'success' => false,
                'tab' => 'income_method',
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
        $income_method = IncomeMethod::find($id);
        $referral_groups = ReferralGroup::pluck('group_name', 'id');

        return view('superadmin::income_method.edit')->with(compact(
            'referral_groups',
            'income_method'
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


            $data = [
                'date' => $this->moduleUtil->uf_date($inputs['date']),
                'referral_group_id' => $inputs['referral_group_id'],
                'income_method' => $inputs['income_method'],
                'status' => $inputs['status'],
                'income_type' => $inputs['income_type'],
                'value' => $inputs['value'],
                'minimum_new_signups' => $inputs['minimum_new_signups'],
                'minimum_active_subscriptions' => $inputs['minimum_active_subscriptions'],
                'comission_eligible_conditions' => $inputs['comission_eligible_conditions']
            ];

            IncomeMethod::where('id', $id)->update($data);


            $output = [
                'success' => true,
                'tab' => 'income_method',
                'msg' => __('lang_v1.success')
            ];
        } catch (\Exception $e) {
            Log::emergency('File: ' . $e->getFile() . 'Line: ' . $e->getLine() . 'Message: ' . $e->getMessage());
            $output = [
                'success' => false,
                'tab' => 'income_method',
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
            IncomeMethod::where('id', $id)->delete();

            $output = [
                'success' => true,
                'tab' => 'income_method',
                'msg' => __('lang_v1.success')
            ];
        } catch (\Exception $e) {
            Log::emergency('File: ' . $e->getFile() . 'Line: ' . $e->getLine() . 'Message: ' . $e->getMessage());
            $output = [
                'success' => true,
                'tab' => 'income_method',
                'msg' => __('lang_v1.success')
            ];
        }
        return $output;
    }
}
