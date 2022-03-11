<?php

namespace Modules\Superadmin\Http\Controllers;

use App\Agent;
use App\Utils\BusinessUtil;
use App\Utils\ModuleUtil;
use App\Utils\TransactionUtil;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Modules\Superadmin\Entities\IncomeMethod;
use Modules\Superadmin\Entities\Package;
use Yajra\DataTables\Facades\DataTables;

class AgentController extends Controller
{
    /**
     * All Utils instance.
     *
     */
    protected $businessUtil;
    protected $transactionUtil;
    protected $moduleUtil;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(
        BusinessUtil $businessUtil,
        TransactionUtil $transactionUtil,
        ModuleUtil $moduleUtil
    ) {
        $this->businessUtil = $businessUtil;
        $this->transactionUtil = $transactionUtil;
        $this->moduleUtil = $moduleUtil;
    }

    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index()
    {
        if (!auth()->user()->can('superadmin')) {
            abort(403, 'Unauthorized action.');
        }
        if (request()->ajax()) {
            $business_id = request()->session()->get('user.business_id');

            $agents = Agent::select('*');

            if (!empty(request()->start_date) && !empty(request()->end_date)) {
                $agents->whereDate('date', '>=', request()->start_date);
                $agents->whereDate('date', '<=', request()->end_date);
            }
            return DataTables::of($agents)
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
                        <ul class="dropdown-menu dropdown-menu-right" role="menu">
                        <li><a href="' . action('\Modules\Superadmin\Http\Controllers\AgentController@edit', [$row->id]) . '" class="edit_entity"><i class="glyphicon glyphicon-edit"></i> Edit</a></li>
                        
                        <li><a data-href="' . action('\Modules\Superadmin\Http\Controllers\AgentController@destroy', [$row->id]) . '" class="delete_agent"><i class="glyphicon glyphicon-trash" style="color:brown; cursor: pointer;"></i> Delete</a></li>
                        ';

                        $html .=  '</ul></div>';
                        return $html;
                    }
                )
                ->editColumn('date', '{{@format_date($date)}}')
                ->addColumn('referral_group', '')
                ->addColumn('total_orders', '')
                ->addColumn('active_subscription', '')
                ->addColumn('income', '')
                ->addColumn('paid', '')
                ->addColumn('due', '')
                ->removeColumn('id')
                ->rawColumns(['action'])
                ->make(true);
        }

        $business_id = request()->session()->get('user.business_id');
        $fy = $this->businessUtil->getCurrentFinancialYear($business_id);
        $date_filters['this_fy'] = $fy;
        $date_filters['this_month']['start'] = date('Y-m-01');
        $date_filters['this_month']['end'] = date('Y-m-t');
        $date_filters['this_week']['start'] = date('Y-m-d', strtotime('monday this week'));
        $date_filters['this_week']['end'] = date('Y-m-d', strtotime('sunday this week'));

        $agents = Agent::pluck('username', 'id');
        $packages = Package::pluck('name', 'id');
        $income_methods = IncomeMethod::pluck('income_method', 'id');


        return view('superadmin::agents.index')->with(compact(
            'date_filters',
            'agents',
            'packages',
            'income_methods'
        ));
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        return view('superadmin::create');
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request)
    {
        //
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
        $agent = Agent::find($id);

        return view('superadmin::agents.edit')->with(compact(
            'agent'
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
            $input = $request->except('_token', '_method');
            Agent::where('id', $id)->update($input);
            $output = [
                'success' => true,
                'msg' => __('lang_v1.success')
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
     * @param int $id
     * @return Renderable
     */
    public function destroy($id)
    {
        try {
            Agent::where('id', $id)->delete();

            $output = [
                'success' => true,
                'msg' => __('lang_v1.success')
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
