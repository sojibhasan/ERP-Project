<?php

namespace Modules\Leads\Http\Controllers;

use App\Category;
use App\User;
use App\Utils\ModuleUtil;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Modules\Leads\Entities\Lead;
use Modules\Leads\Entities\LeadsCategory;
use Yajra\DataTables\Facades\DataTables;

class LeadsController extends Controller
{
    protected $moduleUtil;

    /**
     * Constructor
     *
     * @param Util $commonUtil
     * @return void
     */
    public function __construct(ModuleUtil $moduleUtil)
    {
        $this->moduleUtil =  $moduleUtil;
    }

    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index(Request $request)
    {
        $business_id = request()->session()->get('business.id');
        if (!$this->moduleUtil->hasThePermissionInSubscription($business_id, 'leads')) {
            abort(403, 'Unauthorized action.');
        }
        if (request()->ajax()) {
            $leads = Lead::leftjoin('leads_categories', 'leads.category_id', 'leads_categories.id')
                ->leftjoin('users', 'leads.created_by', 'users.id')
                ->where('leads.business_id', $business_id)
                ->select([
                    'leads.*',
                    'leads_categories.name as category',
                    'users.username as user'
                ]);
            if (!empty(request()->start_date) && !empty(request()->end_date)) {
                $leads->whereDate('leads.date', '>=', request()->start_date);
                $leads->whereDate('leads.date', '<=', request()->end_date);
            }
            if (!empty(request()->sector)) {
                $leads->where('sector', request()->sector);
            }
            if (!empty(request()->category_id)) {
                $leads->where('category_id', request()->category_id);
            }
            if (!empty(request()->main_organization)) {
                $leads->where('main_organization', request()->main_organization);
            }
            if (!empty(request()->business)) {
                $leads->where('business', request()->business);
            }
            if (!empty(request()->town)) {
                $leads->where('town', request()->town);
            }
            if (!empty(request()->district)) {
                $leads->where('district', request()->district);
            }
            if (!empty(request()->mobile_no)) {
                $leads->where('mobile_no_1', request()->mobile_no)->orWhere('mobile_no_2', request()->mobile_no)->orWhere('mobile_no_3', request()->mobile_no);
            }
            if (!empty(request()->created_by)) {
                $leads->where('leads.created_by', request()->created_by);
            }

            return DataTables::of($leads)
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
                        if (auth()->user()->can('leads.view')) {
                            $html .= '<li><a href="#" data-href="' . action('\Modules\Leads\Http\Controllers\LeadsController@show', [$row->id]) . '" class="btn-modal" data-container=".leads_model"><i class="fa fa-eye"></i> ' . __("messages.view") . '</a></li>';
                        }
                        if (auth()->user()->can('leads.edit')) {
                            $html .= '<li><a href="#" data-href="' . action('\Modules\Leads\Http\Controllers\LeadsController@edit', [$row->id]) . '" class="btn-modal" data-container=".leads_model"><i class="glyphicon glyphicon-edit"></i> ' . __("messages.edit") . '</a></li>';
                        }
                        if (auth()->user()->can('leads.delete')) {
                            $html .= '<li><a href="#" data-href="' . action('\Modules\Leads\Http\Controllers\LeadsController@destroy', [$row->id]) . '" class="delete-leads"><i class="fa fa-trash"></i> ' . __("messages.delete") . '</a></li>';
                        }
                        if (auth()->user()->can('leads.edit')) {
                            $html .= '<li><a href="#" data-href="' . action('\Modules\Leads\Http\Controllers\LeadsController@toggleStatus', [$row->id]) . '" class="change-status"><i class="fa fa-recycle"></i> ' . __("leads::lang.valid_invlid_status") . '</a></li>';
                        }

                        return $html;
                    }
                )
                ->addColumn('mass_delete', function ($row) {
                    return  '<input type="checkbox" class="row-select" value="' . $row->id . '">';
                })
                ->editColumn('date', '{{@format_date($date)}}')
                ->editColumn('status', '{{ucfirst($status)}}')
                ->editColumn('sector', '{{ucfirst($sector)}}')
                ->removeColumn('id')
                ->rawColumns(['action', 'mass_delete'])
                ->make(true);
        }
        $categories = LeadsCategory::where('business_id', $business_id)->pluck('name', 'id');
        $main_organizations = Lead::where('business_id', $business_id)->distinct('main_organization')->pluck('main_organization', 'main_organization');
        $businesses = Lead::where('business_id', $business_id)->distinct('business')->pluck('business', 'business');
        $towns = Lead::where('business_id', $business_id)->distinct('town')->pluck('town', 'town');
        $districts = Lead::where('business_id', $business_id)->distinct('district')->pluck('district', 'district');
        $mobile_nos = Lead::where('business_id', $business_id)->select('mobile_no_1 as mobile_no', 'mobile_no_2 as mobile_no', 'mobile_no_3 as mobile_no')->pluck('mobile_no', 'mobile_no');
        $users = User::where('users.business_id', $business_id)->pluck('username', 'users.id');

        return view('leads::leads.index')->with(compact(
            'categories',
            'main_organizations',
            'businesses',
            'towns',
            'districts',
            'mobile_nos',
            'users'
        ));
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create()
    {
        $business_id = request()->session()->get('business.id');

        $categories = LeadsCategory::where('business_id', $business_id)->pluck('name', 'id');
        return view('leads::leads.create')->with(compact(
            'categories'
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
        if (!$this->moduleUtil->hasThePermissionInSubscription($business_id, 'leads')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $data = $request->except('_token');
            $data['business_id'] = $business_id;
            $data['status'] = 'valid';
            $data['created_by'] = Auth::user()->id;
            $data['date'] = !empty($data['date']) ? Carbon::parse($data['date'])->format('Y-m-d') : date('Y-m-d');
            Lead::create($data);

            $output = [
                'success' => true,
                'msg' => __('leads::lang.leads_create_success')
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
        $leads = Lead::leftjoin('leads_categories', 'leads.category_id', 'leads_categories.name')
            ->leftjoin('users', 'leads.created_by', 'users.id')
            ->where('leads.id', $id)
            ->select([
                'leads.*',
                'leads_categories.name as category',
                'users.username as user'
            ])->first();


        return view('leads::leads.show')->with(compact('leads'));
    }

    /**
     * Show the form for editing the specified resource.
     * @return Response
     */
    public function edit($id)
    {
        $business_id = request()->session()->get('business.id');
        $leads = Lead::findOrFail($id);
        $categories = LeadsCategory::where('business_id', $business_id)->pluck('name', 'id');

        return view('leads::leads.edit')->with(compact('leads', 'categories'));
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
            $data['date'] = !empty($data['date']) ? Carbon::parse($data['date'])->format('Y-m-d') : date('Y-m-d');

            Lead::where('id', $id)->update($data);
            $output = [
                'success' => true,
                'msg' => __('leads::lang.leads_update_success')
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
            Lead::where('id', $id)->delete();
            $output = [
                'success' => true,
                'msg' => __('leads::lang.leads_delete_success')
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

    public function massValid(Request $request)
    {
        try {
            $selected_rows = explode(',', $request->input('selected_rows'));

            foreach ($selected_rows as $row) {
                Lead::where('id', $row)->update(['status' => 'valid']);
            }

            $output = [
                'success' => true,
                'msg' => __('leads::lang.success')
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
    public function massInvalid(Request $request)
    {
        try {
            $selected_rows = explode(',', $request->input('selected_rows'));

            foreach ($selected_rows as $row) {
                Lead::where('id', $row)->update(['status' => 'invalid']);
            }

            $output = [
                'success' => true,
                'msg' => __('leads::lang.success')
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
    public function toggleStatus($id)
    {
        try {
            $lead = Lead::findOrFail($id);
            if ($lead->status == 'valid') {
                $lead->status = 'invalid';
            } else {
                $lead->status = 'valid';
            }
            $lead->save();

            $output = [
                'success' => true,
                'msg' => __('leads::lang.success')
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
