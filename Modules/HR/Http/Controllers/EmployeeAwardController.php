<?php

namespace Modules\HR\Http\Controllers;

use App\BusinessLocation;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\HR\Entities\Department;
use Modules\HR\Entities\Employee;
use Modules\HR\Entities\EmployeeAward;
use Yajra\DataTables\Facades\DataTables;

class EmployeeAwardController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        if (request()->ajax()) {
            $business_id = request()->session()->get('user.business_id');

            $employee = EmployeeAward::leftjoin('employees', 'employee_awards.employee_id', 'employees.id')
                ->where('employees.business_id', $business_id)->select(
                    'employee_awards.id as a_id',
                    'employee_awards.award_name',
                    'employee_awards.employee_id',
                    'employee_awards.department_id',
                    'employee_awards.gift_item',
                    'employee_awards.award_amount',
                    'employee_awards.award_month',
                    'employees.*'
                );


            //Add condition for location,used in sales representative expense report & list of expense
            if (request()->has('location_id')) {
                $location_id = request()->get('location_id');
                if (!empty($location_id)) {
                    $employee->where('location_id', $location_id);
                }
            }
            //Add condition for start and end date filter, uses in sales representative expense report & list of expense
            // if (!empty(request()->start_date) && !empty(request()->end_date)) {
            //     $start = request()->start_date;
            //     $end =  request()->end_date;
            //     $employee->whereDate('transaction_date', '>=', $start)
            //             ->whereDate('transaction_date', '<=', $end);
            // }


            // $permitted_locations = auth()->user()->permitted_locations();
            // if ($permitted_locations != 'all') {
            //     $employee->whereIn('location_id', $permitted_locations);
            // }


            return DataTables::of($employee)
                ->addColumn(
                    'action',
                    // <a href="{{action(\'EmployeeController@edit\', [$id])}}"><i class="glyphicon glyphicon-edit"></i> @lang("messages.edit")</a>
                    '<div class="btn-group">
                    
                        <a data-href="{{action(\'EmployeeController@destroyAward\', [$a_id])}}" class="delete_award"><i class="glyphicon glyphicon-trash" style="color:brown;"></i> @lang("messages.delete")</a>
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

        return view('hr::employee_award.index')->with(compact('department', 'business_locations'));
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create()
    {
        $employee = Employee::all();

        $business_id = request()->session()->get('user.business_id');
        $department = Department::where('business_id', $business_id)->get();

        return view('hr::employee_award.create')->with(compact('department', 'employee'));
    }

    /**
     * Store a newly created resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        $data = [
            'award_name' => $request->award_name,
            'employee_id' => $request->employee_id,
            'department_id' => $request->department_id,
            'gift_item' => $request->gift_item,
            'award_amount' => $request->award_amount,
            'award_month' => $request->month,
        ];
        EmployeeAward::insert($data);

        $output = [
            'success' => 1,
            'msg' => __('hr.award_add_success')
        ];

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
    public function edit()
    {
        return view('hr::edit');
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
    public function destroy($id)
    {
        if (request()->ajax()) {
            try {
                EmployeeAward::where('id', $id)->delete();

                $output = [
                    'success' => true,
                    'msg' => __("hr.employee_delete_success")
                ];
            } catch (\Exception $e) {
                Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

                $output = [
                    'success' => false,
                    'msg' => __("messages.something_went_wrong")
                ];
            }

            return $output;
        }
    }
}
