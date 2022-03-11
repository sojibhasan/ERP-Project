<?php

namespace Modules\HR\Http\Controllers;

use App\BusinessLocation;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Modules\HR\Entities\Department;
use Modules\HR\Entities\LeaveApplication;
use Yajra\DataTables\Facades\DataTables;

class ApplicationController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        if (request()->ajax()) {
            $business_id = request()->session()->get('user.business_id');

            $employee = LeaveApplication::leftjoin('employees', 'leave_applications.employee_id', 'employees.id')
                ->leftjoin('leave_application_types', 'leave_applications.leave_ctegory_id', 'leave_application_types.id')
                ->where('employees.business_id', $business_id)->select(
                    'leave_applications.id as a_id',
                    'leave_applications.start_date',
                    'leave_applications.end_date',
                    'leave_applications.reason',
                    'leave_applications.application_date',
                    'leave_applications.status',
                    'employees.*',
                    'leave_application_types.leave_category as type_name'
                );


            //Add condition for location,used in sales representative expense report & list of expense
            if (request()->has('location_id')) {
                $location_id = request()->get('location_id');
                if (!empty($location_id)) {
                    $employee->where('location_id', $location_id);
                }
            }


            return DataTables::of($employee)
                ->addColumn(
                    'action',
                    '<div class="btn-group">
                    <a class="btn-modal eye_modal" 
                    data-href="{{action(\'EmployeeController@viewApplication\', [$a_id])}}" 
                    data-container=".application_modal">
                    <i class="fa fa-eye"></i></a>
                    </div>'
                )
                ->removeColumn('id')
                ->editColumn('name', function ($row) {
                    return $row->first_name . ' ' . $row->last_name;
                })

                ->rawColumns(['name', 'action'])
                ->make(true);
        }

        $business_id = request()->session()->get('user.business_id');
        $department = Department::get();
        $business_locations = BusinessLocation::forDropdown($business_id, true);

        return view('hr::application.index')->with(compact('business_locations'));
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create()
    {
        return view('hr::create');
    }

    /**
     * Store a newly created resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function store(Request $request)
    {
    }

    /**
     * Show the specified resource.
     * @return Response
     */
    public function show($id)
    {
        $application = LeaveApplication::leftjoin('employees', 'leave_applications.employee_id', 'employees.id')
            ->leftjoin('leave_application_types', 'leave_applications.leave_ctegory_id', 'leave_application_types.id')
            ->where('leave_applications.id', $id)
            ->select(
                'employees.employee_id as em_id',
                'employees.first_name',
                'employees.last_name',
                'leave_applications.start_date',
                'leave_applications.end_date',
                'leave_applications.reason',
                'leave_applications.application_date',
                'leave_applications.status',
                'leave_application_types.leave_category as type_name',
                'leave_applications.id'
            )
            ->first();

        return view('hr::application.create')->with(compact('application'));
    }

    /**
     * Show the form for editing the specified resource.
     * @return Response
     */
    public function edit()
    {
        return view('hr::edit');
    }

    /**
     * Update the specified resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function update($id, Request $request)
    {
        $data = [
            'status' => $request->status
        ];

        LeaveApplication::where('id', $request->id)->update($data);

        $output = [
            'success' => 1,
            'msg' => __('hr.application_status_succuess')
        ];

        return redirect()->back()->with('status', $output);
    }

    /**
     * Remove the specified resource from storage.
     * @return Response
     */
    public function destroy()
    {
    }
}
