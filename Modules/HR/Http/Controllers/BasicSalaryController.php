<?php

namespace Modules\HR\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Modules\HR\Entities\BasicSalary;
use Modules\HR\Entities\Department;
use Modules\HR\Entities\Employee;
use Yajra\DataTables\Facades\DataTables;

class BasicSalaryController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        $business_id = request()->session()->get('business.id');
        if (request()->ajax()) {
            $basic_salary = BasicSalary::leftjoin('employees', 'basic_salaries.employee_id', 'employees.id')
                ->leftjoin('departments', 'basic_salaries.department_id', 'departments.id')
                ->leftjoin('work_shifts', 'employees.work_shift', 'work_shifts.id')
                ->leftjoin('job_titles', 'employees.job_title', 'job_titles.id')
                ->leftjoin('employment_statuses', 'employees.employment_status', 'employment_statuses.id')
                ->where('basic_salaries.business_id', $business_id)
                ->select([
                    'basic_salaries.*',
                    'employees.employee_id',
                    'employees.first_name',
                    'employees.last_name',
                    'employees.employee_number',
                    'departments.department',
                    'work_shifts.shift_name',
                    'employment_statuses.status_name',
                    'job_titles.job_title'
                ]);



            return DataTables::of($basic_salary)
                ->addColumn(
                    'action',
                    '
                    <button data-href="{{action(\'\Modules\HR\Http\Controllers\BasicSalaryController@edit\',[$id])}}" data-container=".basic_salary_modal" class="btn btn-xs btn-primary btn-modal edit_btn"><i class="glyphicon glyphicon-edit"></i> @lang("messages.edit")</button>
                    <button data-href="{{action(\'\Modules\HR\Http\Controllers\BasicSalaryController@destroy\',[$id])}}" class="btn btn-xs btn-danger basic_salary_delete"><i class="glyphicon glyphicon-trash"></i> @lang("messages.delete")</button>
                   
                    '
                )
                ->addColumn(
                    'employee_name',
                    '{{$first_name}} {{$last_name}}'
                )
                ->removeColumn('id')
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('hr::basic_salary.index');
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create()
    {
        $business_id = request()->session()->get('business.id');
        $departments = Department::where('business_id', $business_id)->pluck('department', 'id');

        return view('hr::basic_salary.create')->with(compact('departments'));
    }

    /**
     * Store a newly created resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        try {
            $business_id = request()->session()->get('business.id');
            $input = $request->except('_token');
            $input['business_id'] = $business_id;
            $input['salary_date'] = !empty($input['salary_date']) ? Carbon::parse($input['salary_date'])->format('Y-m-d') : date('Y-m-d');
            BasicSalary::create($input);

            $output = [
                'success' => true,
                'msg' => __('hr::lang.basic_salary_create_success')
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
        $departments = Department::where('business_id', $business_id)->pluck('department', 'id');
        $employees = Employee::where('business_id', $business_id)->pluck('first_name', 'id');

        $basic_salary = BasicSalary::findOrFail($id);

        return view('hr::basic_salary.edit')->with(compact('departments', 'basic_salary', 'employees'));
    }

    /**
     * Update the specified resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function update(Request $request, $id)
    {
        try {
            $input = $request->except('_token', '_method');
            $input['salary_date'] = !empty($input['salary_date']) ? Carbon::parse($input['salary_date'])->format('Y-m-d') : date('Y-m-d');
            BasicSalary::where('id', $id)->update($input);

            $output = [
                'success' => true,
                'msg' => __('hr::lang.basic_salary_update_success')
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

    public function getEmployeeByDepartment(Request $request)
    {
        $department_id = $request->department_id;
        $business_id = request()->session()->get('business.id');
        $employees = Employee::where('business_id', $business_id)->where('department_id', $department_id)->select('first_name', 'last_name', 'id')->get()->toArray();

        return ['employees' => $employees];
    }
}
