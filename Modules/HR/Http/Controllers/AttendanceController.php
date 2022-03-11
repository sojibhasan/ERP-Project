<?php

namespace Modules\HR\Http\Controllers;

use App\Utils\ModuleUtil;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Modules\HR\Entities\Attendance;
use Modules\HR\Entities\Department;
use Modules\HR\Entities\Employee;
use Modules\HR\Entities\Holiday;
use Modules\HR\Entities\LeaveApplicationType;
use Yajra\DataTables\Facades\DataTables;

class AttendanceController extends Controller
{
    /**
     * All Utils instance.
     *
     */
    protected $moduleUtil;

    /**
     * Constructor
     *
     * @param ProductUtils $product
     * @return void
     */
    public function __construct(ModuleUtil $moduleUtil)
    {
        $this->moduleUtil = $moduleUtil;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $business_id = request()->session()->get('user.business_id');

        $departments = Department::where('business_id', $business_id)->pluck('department', 'id');
        $employees  = Employee::where('business_id', $business_id)->pluck('first_name', 'id');

        $permissions['attendance'] = $this->moduleUtil->hasThePermissionInSubscription($business_id, 'attendance');
        $permissions['import_attendance'] = $this->moduleUtil->hasThePermissionInSubscription($business_id, 'import_attendance');
        return view('hr::attendance.index')->with(compact(
            'departments',
            'permissions',
            'employees'
        ));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $business_id = request()->session()->get('user.business_id');
        if ($request->input('department_id')) {
            $employee_info = Employee::where('termination', 0)->where('department_id', $request->input('department_id'))->get();
        } else {
            $employee_info = [];
        }

        $department_id = $request->input('department_id');
        $all_leave_category_info = LeaveApplicationType::all();
        $date = !empty($request->input('date')) ? $request->input('date') : date('Y-m-d');

        $atndnce = array();
        foreach ($employee_info as $key => $value) {
            $atndnce[] = Attendance::where('employee_id', $value->id)->where('date', date('Y-m-d'))->first();
        }

        $departments = Department::where('business_id', $business_id)->pluck('department', 'id');
        $employees  = Employee::where('business_id', $business_id)->pluck('first_name', 'id');
        return view('hr::attendance.partials.set_attendance')->with(compact('departments', 'employee_info', 'department_id', 'date', 'atndnce', 'all_leave_category_info', 'employees'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $business_id = request()->session()->get('user.business_id');
        $attendance_status = $request->input('attendance');
        $leave_category_id = $request->input('leave_category_id');
        $employee_id = $request->input('employee_id');
        $attendance_id = $request->input('attendance_id');
        $in_time = $request->input('in');
        $out_time = $request->input('out');
        $over_time = $request->input('over_time');
        $ot_hours = $request->input('ot_hours');
        $ot_minutes = $request->input('ot_minutes');
        $late_time = $request->input('late_time');
        $lt_hours = $request->input('lt_hours');
        $lt_minutes = $request->input('lt_minutes');
        if (!empty($attendance_id)) {
            $key = 0;
            foreach ($employee_id as $empID) {
                $data['date'] = Carbon::parse($request->input('date'))->format('Y-m-d');
                $data['business_id'] = $business_id;
                $data['attendance_status'] = 0;
                $data['employee_id'] = $empID;
                if (!empty($leave_category_id[$key])) {
                    $data['leave_category_id'] = $leave_category_id[$key];
                    $data['attendance_status'] = 3;
                } else {
                    $data['leave_category_id'] = null;
                }
                if (!empty($attendance_status)) {
                    foreach ($attendance_status as $v_status) {
                        if ($empID == $v_status) {
                            $data['attendance_status'] = 1;
                            $data['leave_category_id'] = null;
                            $data['in_time'] = date("H:i:s", strtotime($in_time[$key]));
                            $data['out_time'] = date("H:i:s", strtotime($out_time[$key]));
                            $data['over_time'] = !empty($over_time[$key]) ? 1 : 0;
                            $data['ot_hours'] = !empty($ot_hours[$key]) ? $ot_hours[$key] : 0;
                            $data['ot_minutes'] = !empty($ot_minutes[$key]) ? $ot_minutes[$key] : 0;
                            $data['late_time'] = !empty($late_time[$key]) ? 1 : 0;
                            $data['lt_hours'] = !empty($lt_hours[$key]) ? $lt_hours[$key] : 0;
                            $data['lt_minutes'] = !empty($lt_minutes[$key]) ? $lt_minutes[$key] : 0;
                        }
                    }
                }
                $id = $attendance_id[$key];
                if (!empty($id)) {
                    Attendance::where('attendance_id', $id)->update($data);
                } else {
                    Attendance::insert($data);
                }

                $key++;
            }
        } else {
            $key = 0;

            foreach ($employee_id as $empID) {
                $data = [];
                $data['date'] = Carbon::parse($request->input('date'))->format('Y-m-d');
                $data['business_id'] = $business_id;
                $data['attendance_status'] = 0;
                $data['employee_id'] = $empID;
                if (!empty($leave_category_id[$key])) {
                    $data['leave_category_id'] = $leave_category_id[$key];
                    $data['attendance_status'] = 3;
                } else {
                    $data['leave_category_id'] = null;
                }

                if (!empty($attendance_status)) {
                    foreach ($attendance_status as $v_status) {
                        if ($empID == $v_status) {
                            $data['attendance_status'] = 1;
                            $data['leave_category_id'] = null;
                            $data['in_time'] = date("H:i:s", strtotime($in_time[$key]));
                            $data['out_time'] = date("H:i:s", strtotime($out_time[$key]));
                            $data['over_time'] = !empty($over_time[$key]) ? 1 : 0;
                            $data['ot_hours'] = !empty($ot_hours[$key]) ? $ot_hours[$key] : 0;
                            $data['ot_minutes'] = !empty($ot_minutes[$key]) ? $ot_minutes[$key] : 0;
                            $data['late_time'] = !empty($late_time[$key]) ? 1 : 0;
                            $data['lt_hours'] = !empty($lt_hours[$key]) ? $lt_hours[$key] : 0;
                            $data['lt_minutes'] = !empty($lt_minutes[$key]) ? $lt_minutes[$key] : 0;
                        }
                    }
                }
                Attendance::create($data);
                $key++;
            }
        }

        $output = [
            'success' => 1,
            'msg' => __('hr.attendance_add_success')
        ];

        return redirect()->back()->with('status', $output);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }


    /**
     * Import the specified resource to storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function getImportAttendance()
    {
    }


    /**
     * Post Import the specified resource to storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function postImportAttendance(Request $request)
    {
        if ($request->hasFile('import')) {
            $file = $request->file('import');

            $parsed_array = Excel::toArray([], $file);

            //Remove header row
            $imported_data = array_splice($parsed_array[0], 1);
            $business_id = $request->session()->get('user.business_id');
            $user_id = $request->session()->get('user.id');

            $formated_data = [];

            $is_valid = true;
            $error_msg = '';

            $total_rows = count($imported_data);

            foreach ($imported_data as $worksheet) {
                $employee_number    = trim($worksheet[0]);
                $date               = trim($worksheet[1]);
                $attendance_status  = trim($worksheet[2]);
                $in_time            = date("H:i:s", strtotime(trim($worksheet[3])));
                $out_time           = date("H:i:s", strtotime(trim($worksheet[4])));
                $date               = date('Y-m-d', strtotime($date));

                $result = Employee::where('employee_id',  $employee_number)->first();

                if (empty($result))
                    continue;

                $employee_id = $result->employee_id;
                $allowed_status = array(1, 0, 3); //allowed extension
                if (in_array($attendance_status, $allowed_status)) {

                    $result = Attendance::where('employee_id', $employee_id)->where('date', $date)->first();
                    if ($result) {
                        $data_update = array(
                            'employee_id'           => $employee_id,
                            'business_id'           => $business_id,
                            'date'                  => $date,
                            'attendance_status'     => $attendance_status,
                            'in_time'               => $in_time,
                            'out_time'              => $out_time,
                        );
                        Attendance::where('attendance_id', $result->attendance_id)->update($data_update);
                    } else {
                        $data = array(
                            'employee_id'           => $employee_id,
                            'business_id'           => $business_id,
                            'date'                  => $date,
                            'attendance_status'     => $attendance_status,
                            'in_time'               => $in_time,
                            'out_time'              => $out_time,
                        );
                        Attendance::insert($data);
                    }
                }
            }


            $output = [
                'success' => 1,
                'msg' => __('hr.attendance_import_success')
            ];

            return redirect()->back()->with('status', $output);
        }
    }

    /**
     * Report the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function getAttendanceReport(Request $request)
    {
        $business_id = request()->session()->get('user.business_id');
        $all_department = Department::where('business_id', $business_id)->get();

        $sbtn = $request->input('sbtn');

        $department_id = !empty($request->input('department_id')) ? $request->input('department_id') : $all_department[0]->id;
        $date = !empty($request->input('date')) ? $request->input('date') : date('Y-m-d');

        $month = date('n', strtotime($date));
        $year = date('Y', strtotime($date));

        $num = Carbon::parse($date)->daysInMonth;

        $employee = Employee::leftjoin('job_titles', 'employees.job_title', 'job_titles.id')
            ->leftjoin('departments', 'employees.department_id', 'departments.id')
            ->where('employees.department_id', $department_id)
            ->where('employees.business_id', $business_id)
            ->where('employees.termination', 0)
            ->select('job_titles.job_title', 'employees.*', 'departments.department')->get();


        $day = date('d', strtotime($date));
        for ($i = 1; $i <= $num; $i++) {
            $dateSl[] = $i;
        }

        $holidays = DB::table('working_days')->where('flag', 0)->get();


        if ($month >= 1 && $month <= 9) {
            $yymm = $year . '-' . '0' . $month;
        } else {
            $yymm = $year . '-' . $month;
        }

        $public_holiday = Holiday::where('start_date', 'like', '%' . $yymm . '%')->get();

        //tbl a_calendar Days Holiday
        if (!empty($public_holiday)) {
            foreach ($public_holiday as $p_holiday) {
                for ($k = 1; $k <= $num; $k++) {

                    if ($k >= 1 && $k <= 9) {
                        $sdate = $yymm . '-' . '0' . $k;
                    } else {
                        $sdate = $yymm . '-' . $k;
                    }

                    if ($p_holiday->start_date == $sdate && $p_holiday->end_date == $sdate) {
                        $p_hday[] = $sdate;
                    }
                    if ($p_holiday->start_date == $sdate) {
                        for ($j = $p_holiday->start_date; $j <= $p_holiday->end_date; $j++) {
                            $p_hday[] = $j;
                        }
                    }
                }
            }
        }
        $attendance = [];
        foreach ($employee as $sl => $v_employee) {
            $key = 1;
            $x = 0;
            for ($i = 1; $i <= $num; $i++) {

                if ($i >= 1 && $i <= 9) {

                    $sdate = $yymm . '-' . '0' . $i;
                } else {
                    $sdate = $yymm . '-' . $i;
                }
                $day_name = date('l', strtotime("+$x days", strtotime($year . '-' . $month . '-' . $key)));

                if (!empty($holidays)) {
                    foreach ($holidays as $v_holiday) {
                        if ($v_holiday->days == $day_name) {
                            $flag = 'H';
                        }
                    }
                }
                if (!empty($p_hday)) {
                    foreach ($p_hday as $v_hday) {
                        if ($v_hday == $sdate) {
                            $flag = 'H';
                        }
                    }
                }
                if (!empty($flag)) {
                    $attendance[$sl][] = $this->attendance_report_by_empid($v_employee->id, $sdate, $flag);
                } else {
                    $attendance[$sl][] = $this->attendance_report_by_empid($v_employee->id, $sdate);
                }

                $key++;
                $flag = '';
            }
        }

        $date = $request->input('date');
        $dept_name = Department::where('id', $department_id)->first();
        $month = date('F-Y', strtotime($yymm));


        return view('hr::attendance.report')->with(compact('all_department', 'dateSl', 'attendance', 'employee', 'sdate'));
    }

    public function attendance_report_by_empid($employee_id = null, $sdate = null, $flag = NULL)
    {
        $result = Attendance::select('attendances.date', 'attendances.attendance_status', 'employees.first_name', 'employees.last_name')
            ->leftjoin('employees', 'attendances.employee_id', 'employees.id')
            ->where('attendances.employee_id', $employee_id)
            ->where('attendances.date', $sdate)->get();

        if ($result->count() == 0) {
            $val['attendance_status'] = $flag;
            $val['date'] = $sdate;
            $result[] = (object) $val;
        } else {
            if ($result[0]->attendance_status == 0) {
                if ($flag == 'H') {
                    $result[0]->attendance_status = 'H';
                }
            }
        }


        return $result;
    }

    public function getLateOvertime(Request $request)
    {
        $business_id = request()->session()->get('user.business_id');
        if (request()->ajax()) {

            $attendances = Attendance::where('attendances.business_id', $business_id)
                ->leftjoin('employees', 'attendances.employee_id', 'employees.id')
                ->leftjoin('departments', 'employees.department_id', 'departments.id')
                ->leftjoin('job_titles', 'employees.job_title', 'job_titles.id')
                ->leftjoin('job_categories', 'employees.category', 'job_categories.id')
                ->leftjoin('basic_salaries', 'employees.id', 'basic_salaries.employee_id')
                ->leftjoin('salaries', 'employees.id', 'salaries.employee_id')
                ->leftjoin('salary_grades', 'salaries.grade_id', 'salary_grades.id')
                ->select(
                    'attendances.*',
                    'employees.first_name',
                    'employees.last_name',
                    'job_titles.job_title',
                    'job_categories.category_name',
                    'basic_salaries.salary_amount',
                    'departments.department',
                    'salary_grades.grade_name',
                );

            if (!empty($request->start_date) && !empty($request->end_date)) {
                $attendances->whereDate('attendances.date', '>=', $request->start_date);
                $attendances->whereDate('attendances.date', '<=', $request->end_date);
            }
            if (!empty($request->employee_id)) {
                $attendances->where('attendances.employee_id', $request->employee_id);
            }
            if (!empty($request->department_id)) {
                $attendances->where('employees.department_id', $request->department_id);
            }
            if (!empty($request->mode)) {
                if ($request->mode == 'over_time') {
                    $attendances->where('attendances.ot_hours', '!=', '0');
                    $attendances->where('attendances.ot_minutes', '!=', '0');
                }
                if ($request->mode == 'late_time') {
                    $attendances->where('attendances.lt_hours', '!=', '0');
                    $attendances->where('attendances.lt_minutes', '!=', '0');
                }
            }
            return DataTables::of($attendances)
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
                        <ul class="dropdown-menu dropdown-menu-right" role="menu">';

                        // $html .= '<li><a href="' . action("\Modules\HR\Http\Controllers\AttendanceController@edit", [$row->id]) . '" class="" style="margin-right: 10px;"><i class="glyphicon glyphicon-edit"></i>' . __("messages.edit") . '</a></li>';
                        if (auth()->user()->can('attendance.approve_reject_lo')) {
                            $html .= '<li> <a href="#" data-href="' . action("\Modules\HR\Http\Controllers\AttendanceController@getApproveLateOverTime", [$row->id]) . '" class="btn-modal" data-container=".attendance_modal" style="cursor:pointer"><i class="fa fa-check"></i>' . __("hr::lang.approve_late_over_time") . '</a></li>';
                        }


                        $html .= '</ul></div>';
                        return $html;
                    }

                )
                ->editColumn('employee_name', function ($row) {
                    return $row->first_name . " " . $row->last_name;
                })
                ->editColumn('over_time', function ($row) {
                    return $row->ot_hours . " Hours " . $row->ot_minutes . "Minutes";
                })
                ->editColumn('over_time_approved', function ($row) {
                    if ($row->approved_ot_hours == 0 && $row->approved_ot_minutes == 0) {
                        return '';
                    }
                    return $row->approved_ot_hours . " Hours " . $row->approved_ot_minutes . "Minutes";
                })
                ->editColumn('late_time', function ($row) {
                    return $row->lt_hours . " Hours " . $row->lt_minutes . "Minutes";
                })
                ->editColumn('late_time_approved', function ($row) {
                    if ($row->approved_lt_hours == 0 && $row->approved_lt_minutes == 0) {
                        return '';
                    }
                    return $row->approved_lt_hours . " Hours " . $row->approved_lt_minutes . "Minutes";
                })
                ->editColumn('status', function ($row) {
                    return ucfirst($row->status);
                })

                ->removeColumn('id')
                ->rawColumns(['action'])
                ->make(true);
        }

        $departments = Department::where('business_id', $business_id)->pluck('department', 'id');
        $employees  = Employee::where('business_id', $business_id)->pluck('first_name', 'id');

        return view('hr::attendance.late_overtime')->with(compact(
            'departments',
            'employees'
        ));
    }

    public function getApproveLateOverTime($id)
    {
        $attendance = Attendance::findOrFail($id);

        return view('hr::attendance.partials.approve_modal')->with(compact(
            'attendance'
        ));
    }
    public function postApproveLateOverTime($id, Request $request)
    {
        try {
            $data = $request->except('_token');
            $data['status'] = 'approved';

            Attendance::where('id', $id)->update($data);
            $output = [
                'success' => true,
                'msg' => __('hr::lang.late_over_time_status_success')
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

    public function getEmployeeAttendance(Request $request)
    {
        $business_id = request()->session()->get('user.business_id');
        $all_department = Department::where('business_id', $business_id)->get();

        $sbtn = $request->input('sbtn');

        $employee = Employee::leftjoin('job_titles', 'employees.job_title', 'job_titles.id')
            ->leftjoin('departments', 'employees.department_id', 'departments.id')
            ->where('employees.id', Auth::user()->id)
            ->where('employees.termination', 0)
            ->select('job_titles.job_title', 'employees.*', 'departments.department')->first();

        $department_id = $employee->department_id;
        $date = !empty($request->input('date')) ? $request->input('date') : date('Y-m-d');

        $month = date('n', strtotime($date));
        $year = date('Y', strtotime($date));

        $num = Carbon::parse($date)->daysInMonth;



        $day = date('d', strtotime($date));
        for ($i = 1; $i <= $num; $i++) {
            $dateSl[] = $i;
        }

        $holidays = DB::table('working_days')->where('flag', 0)->get();


        if ($month >= 1 && $month <= 9) {
            $yymm = $year . '-' . '0' . $month;
        } else {
            $yymm = $year . '-' . $month;
        }

        $public_holiday = Holiday::where('start_date', 'like', '%' . $yymm . '%')->get();

        //tbl a_calendar Days Holiday
        if (!empty($public_holiday)) {
            foreach ($public_holiday as $p_holiday) {
                for ($k = 1; $k <= $num; $k++) {

                    if ($k >= 1 && $k <= 9) {
                        $sdate = $yymm . '-' . '0' . $k;
                    } else {
                        $sdate = $yymm . '-' . $k;
                    }

                    if ($p_holiday->start_date == $sdate && $p_holiday->end_date == $sdate) {
                        $p_hday[] = $sdate;
                    }
                    if ($p_holiday->start_date == $sdate) {
                        for ($j = $p_holiday->start_date; $j <= $p_holiday->end_date; $j++) {
                            $p_hday[] = $j;
                        }
                    }
                }
            }
        }
        $attendance = [];
        // foreach ($employee as $sl => $employee) {
        $key = 1;
        $x = 0;
        for ($i = 1; $i <= $num; $i++) {

            if ($i >= 1 && $i <= 9) {

                $sdate = $yymm . '-' . '0' . $i;
            } else {
                $sdate = $yymm . '-' . $i;
            }
            $day_name = date('l', strtotime("+$x days", strtotime($year . '-' . $month . '-' . $key)));

            if (!empty($holidays)) {
                foreach ($holidays as $v_holiday) {
                    if ($v_holiday->days == $day_name) {
                        $flag = 'H';
                    }
                }
            }
            if (!empty($p_hday)) {
                foreach ($p_hday as $v_hday) {
                    if ($v_hday == $sdate) {
                        $flag = 'H';
                    }
                }
            }
            if (!empty($flag)) {
                $attendance[0][] = $this->attendance_report_by_empid($employee->id, $sdate, $flag);
            } else {
                $attendance[0][] = $this->attendance_report_by_empid($employee->id, $sdate);
            }

            $key++;
            $flag = '';
        }
        // }

        $date = $request->input('date');
        $dept_name = Department::where('id', $department_id)->first();
        $month = date('F-Y', strtotime($yymm));


        return view('employee.attendance')->with(compact('all_department', 'dateSl', 'attendance', 'employee', 'sdate'));
    }
}
