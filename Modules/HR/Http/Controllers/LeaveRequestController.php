<?php

namespace Modules\HR\Http\Controllers;

use App\BusinessLocation;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Modules\HR\Entities\Employee;
use Modules\HR\Entities\LeaveApplicationType;
use Modules\HR\Entities\LeaveRequest;
use Yajra\DataTables\Facades\DataTables;

class LeaveRequestController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        if (request()->ajax()) {
            $business_id = request()->session()->get('user.business_id');
            $leave_request = LeaveRequest::leftjoin('employees', 'leave_requests.employee_id', 'employees.id')
                ->leftjoin('leave_application_types', 'leave_requests.leave_type_id', 'leave_application_types.id')
                ->leftjoin('users', 'leave_requests.attended_by', 'users.id')
                ->where('leave_requests.business_id', $business_id)
                ->select('leave_requests.*', 'users.username as attended_by', 'leave_application_types.leave_type');


            if (!empty(request()->location_id)) {
                $leave_request->where('employees.location_id', request()->location_id);
            }
            if (!empty(request()->employee_id)) {
                $leave_request->where('leave_requests.employee_id', request()->employee_id);
            }
            if (!empty(request()->status)) {
                $leave_request->where('leave_requests.status', request()->status);
            }
            if (!empty(request()->leave_type_id)) {
                $leave_request->where('leave_requests.leave_type_id', request()->leave_type_id);
            }
            if (!empty(request()->attended_by)) {
                $leave_request->where('leave_requests.attended_by', request()->attended_by);
            }
            if (!empty(request()->start_date) && !empty(request()->end_date)) {
                $leave_request->whereDate('leave_requests.date', '>=', request()->start_date);
                $leave_request->whereDate('leave_requests.date', '<=', request()->end_date);
            }

            return DataTables::of($leave_request)
                ->addColumn(
                    'action',
                    function ($row) {
                        $html = '';
                        $html .= '<div class="btn-group">
                        <button type="button" class="btn btn-info dropdown-toggle btn-xs" 
                        data-toggle="dropdown" aria-expanded="false">' .
                            __("messages.actions") .
                            '<span class="caret"></span><span class="sr-only">Toggle Dropdown
                        </span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-left" role="menu">';
                        if (auth()->user()->can('leave_request.edit')) {
                            $html .= '<li><a data-href="' . action("\Modules\HR\Http\Controllers\LeaveRequestController@edit", [$row->id]) . '" class="btn-modal" data-container=".leave_request_modal" style="margin-right: 10px;"><i class="glyphicon glyphicon-edit"></i>' . __("messages.edit") . '</a></li>   ';
                        }
                        if (auth()->user()->can('leave_request.delete')) {
                            $html .= '<li> <a data-href="' . action("\Modules\HR\Http\Controllers\LeaveRequestController@destroy", [$row->id]) . '" class="delete_employee" style="cursor:pointer"><i class="glyphicon glyphicon-trash"></i>' . __("messages.delete") . '</a></li>';
                        }
                        $html .= '</ul></div>';
                        return $html;
                    }
                )
                ->removeColumn('id')
                ->editColumn('employee_name', function ($row) {
                    return $row->first_name . ' ' . $row->last_name;
                })
                ->editColumn('status', '{{ucfirst($status)}}')
                ->addColumn('leave_days', function ($row) {
                    return Carbon::parse($row->leave_date_from)->diffInDays($row->leave_date_to);
                })

                ->rawColumns(['name', 'action', 'leave_days'])
                ->make(true);
        }
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create()
    {
        $business_id = request()->session()->get('business.id');
        $employee_names = Employee::where('business_id', $business_id)->pluck('first_name', 'id');
        $employee_numbers = Employee::where('business_id', $business_id)->pluck('employee_number', 'id');
        $leave_types = LeaveApplicationType::where('business_id', $business_id)->pluck('leave_type', 'id');

        return view('hr::leave_request.create')->with(compact(
            'leave_types',
            'employee_names',
            'employee_numbers'
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
            $tab = $request->tab;
            $input = $request->except('_token', 'employee_name', 'employee_number', 'tab');

            $input['date'] = !empty($request->date) ? Carbon::parse($request->date)->format('Y-m-d') : date('Y-m-d');
            $input['leave_date_from'] = !empty($request->leave_date_from) ? Carbon::parse($request->leave_date_from)->format('Y-m-d') : date('Y-m-d');
            $input['leave_date_to'] = !empty($request->leave_date_to) ? Carbon::parse($request->leave_date_to)->format('Y-m-d') : date('Y-m-d');
            $input['business_id'] = $business_id;
            $input['status'] = 'pending';

            LeaveRequest::create($input);

            $output = [
                'success' => true,
                'tab' => 'leave_request',
                'msg' => __('hr::lang.leave_request_create_success')
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
    public function show()
    {
        return view('hr::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @return Response
     */
    public function edit($id)
    {
        $business_id = request()->session()->get('business.id');
        $employee_names = Employee::where('business_id', $business_id)->pluck('first_name', 'id');
        $employee_numbers = Employee::where('business_id', $business_id)->pluck('employee_number', 'id');
        $leave_types = LeaveApplicationType::where('business_id', $business_id)->pluck('leave_type', 'id');

        $leave_request = LeaveRequest::findOrFail($id);
        return view('hr::leave_request.edit')->with(compact(
            'leave_types',
            'leave_request',
            'employee_names',
            'employee_numbers'
        ));
    }

    /**
     * Update the specified resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function update($id, Request $request)
    {
        $business_id = $request->session()->get('business.id');
        try {
            $tab = $request->tab;
            $input = $request->except('_token', 'employee_name', 'employee_number', 'tab', '_method');

            $input['date'] = !empty($request->date) ? Carbon::parse($request->date)->format('Y-m-d') : date('Y-m-d');
            $input['leave_date_from'] = !empty($request->leave_date_from) ? Carbon::parse($request->leave_date_from)->format('Y-m-d') : date('Y-m-d');
            $input['leave_date_to'] = !empty($request->leave_date_to) ? Carbon::parse($request->leave_date_to)->format('Y-m-d') : date('Y-m-d');
            if ($input['status'] == 'approved' || $input['status'] == 'rejected') {
                $input['attended_by'] = Auth::user()->id;
            }
            LeaveRequest::where('id', $id)->update($input);

            $output = [
                'success' => true,
                'tab' => 'leave_request',
                'msg' => __('hr::lang.leave_request_update_success')
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
    public function destroy()
    {
    }

    /**
     * Get the specified resource from storage for sepcifice employee
     * @return Response
     */
    public function getEmployeeLeaveRequest()
    {
        $business_id = $request->session()->get('business.id');
        if (request()->ajax()) {
            $leave_request = LeaveRequest::leftjoin('employees', 'leave_requests.employee_id', 'employees.id')
                ->leftjoin('leave_application_types', 'leave_requests.leave_type_id', 'leave_application_types.id')
                ->leftjoin('users', 'leave_requests.attended_by', 'users.id')
                ->where('leave_requests.employee_id', Auth::user()->id)
                ->select('leave_requests.*', 'users.username as attended_by', 'leave_application_types.leave_type');


            if (request()->has('location_id')) {
                $location_id = request()->get('location_id');
                if (!empty($location_id)) {
                    $leave_request->where('location_id', $location_id);
                }
            }
            if (!empty(request()->location_id)) {
                $leave_request->where('employees.location_id', request()->location_id);
            }
            if (!empty(request()->status)) {
                $leave_request->where('leave_requests.status', request()->status);
            }
            if (!empty(request()->leave_type_id)) {
                $leave_request->where('leave_requests.leave_type_id', request()->leave_type_id);
            }
            if (!empty(request()->attended_by)) {
                $leave_request->where('leave_requests.attended_by', request()->attended_by);
            }
            if (!empty(request()->start_date) && !empty(request()->end_date)) {
                $leave_request->whereDate('leave_requests.date', '>=', request()->start_date);
                $leave_request->whereDate('leave_requests.date', '<=', request()->end_date);
            }


            return DataTables::of($leave_request)
                ->addColumn(
                    'action',
                    function ($row) {
                        $html = '';
                        $html .= '<div class="btn-group">
                        <button type="button" class="btn btn-info dropdown-toggle btn-xs" 
                        data-toggle="dropdown" aria-expanded="false">' .
                            __("messages.actions") .
                            '<span class="caret"></span><span class="sr-only">Toggle Dropdown
                        </span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-left" role="menu">';
                        if (auth()->user()->can('leave_request.edit')) {
                            $html .= '<li><a data-href="' . action("\Modules\HR\Http\Controllers\LeaveRequestController@edit", [$row->id]) . '" class="btn-modal" data-container=".leave_request_modal" style="margin-right: 10px;"><i class="glyphicon glyphicon-edit"></i>' . __("messages.edit") . '</a></li>   ';
                        }
                        if (auth()->user()->can('leave_request.delete')) {
                            $html .= '<li> <a data-href="' . action("\Modules\HR\Http\Controllers\LeaveRequestController@destroy", [$row->id]) . '" class="delete_employee" style="cursor:pointer"><i class="glyphicon glyphicon-trash"></i>' . __("messages.delete") . '</a></li>';
                        }
                        $html .= '</ul></div>';
                        return $html;
                    }
                )
                ->removeColumn('id')
                ->editColumn('employee_name', function ($row) {
                    return $row->first_name . ' ' . $row->last_name;
                })
                ->editColumn('status', '{{ucfirst($status)}}')
                ->addColumn('leave_days', function ($row) {
                    return Carbon::parse($row->leave_date_from)->diffInDays($row->leave_date_to);
                })

                ->rawColumns(['name', 'action', 'leave_days'])
                ->make(true);
        }
        $business_locations = BusinessLocation::forDropdown($business_id, true);
        $employees = Employee::where('business_id', $business_id)->pluck('first_name', 'id');
        $leave_types = LeaveApplicationType::where('business_id', $business_id)->pluck('leave_type', 'id');
        $users = User::where('business_id', $business_id)->pluck('username', 'id');

        return view('emplyee.leave_request')->with(compact(
            'countries',
            'locations',
            'business_locations',
            'employees',
            'leave_types',
            'users'
        ));
    }
}
