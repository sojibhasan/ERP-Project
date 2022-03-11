<?php

namespace Modules\HR\Http\Controllers;

use App\Utils\ModuleUtil;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Modules\HR\Entities\Attendance;
use Modules\HR\Entities\Department;
use Modules\HR\Entities\Employee;
use Modules\HR\Entities\Holiday;
use Modules\HR\Entities\JobTitle;
use Modules\HR\Entities\Payroll;
use Modules\HR\Entities\WorkingDay;
use Modules\HR\Entities\WorkShift;
use Yajra\DataTables\Facades\DataTables;

class ReportController extends Controller
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
     * @return Response
     */
    public function index()
    {
        $business_id = request()->session()->get('business.id');
        $departments = Department::where('business_id', $business_id)->pluck('department', 'id');

        $permissions['attendance_report'] = $this->moduleUtil->hasThePermissionInSubscription($business_id, 'attendance_report');
        $permissions['employee_report'] = $this->moduleUtil->hasThePermissionInSubscription($business_id, 'employee_report');
        $permissions['payroll_report'] = $this->moduleUtil->hasThePermissionInSubscription($business_id, 'payroll_report');

        return view('hr::report.index')->with(compact(
            'departments',
            'permissions'
        ));
    }

    public function getAttendanceReport(Request $request)
    {
        $business_id = request()->session()->get('business.id');
        $employee_id = $request->employee_id;

        $start_date    = Carbon::parse($request->start_date)->format('Y-m-d');
        $end_date    = Carbon::parse($request->end_date)->format('Y-m-d');

        $period   = CarbonPeriod::create($start_date, '1 Month', $end_date);

        foreach ($period as $dt) {

            $date = $dt->format('Y-m');

            $month = date('n', strtotime($date));
            $year = date('Y', strtotime($date));

            $num = Carbon::parse($date)->daysInMonth;
            $employee = Employee::findOrFail($employee_id);
            for ($i = 1; $i <= $num; $i++) {
                $dateSl[$dt->format("Y-m")][] = $i;
            }

            if ($month >= 1 && $month <= 9) {
                $yymm = $year . '-' . '0' . $month;
            } else {
                $yymm = $year . '-' . $month;
            }

            $public_holiday = Holiday::where('business_id', $business_id)->where('start_date', 'like', '%' . $yymm . '%')->get();
            $holidays = WorkingDay::where('business_id', $business_id)->where('flag', 0)->get();

            $flag = '';
            if (count($public_holiday) > 0) {
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

            $key = 1;
            $x = 0;
            for ($i = 1; $i <= $num; $i++) {

                if ($i >= 1 && $i <= 9) {
                    $sdate = $yymm . '-' . '0' . $i;
                } else {
                    $sdate = $yymm . '-' . $i;
                }
                $day_name = date('l', strtotime("+$x days", strtotime($year . '-' . $month . '-' . $key)));
                if (count($holidays) > 0) {
                    foreach ($holidays as $v_holiday) {

                        if ($v_holiday->days == $day_name) {
                            $flag = 'H';
                        } else {
                            $flag = '';
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
                $employee_attendance = Attendance::where('employee_id', $employee_id)->whereDate('date', $sdate)->select('attendance_status', 'date')->first();

                if (!empty($employee_attendance)) {
                    if ($employee_attendance->attendance_status == 0) {
                        if ($flag == 'H') {
                            $employee_attendance->attendance_status = 'H';
                        }
                    }
                    $result = $employee_attendance;
                } else {
                    $val['attendance_status'] = $flag;
                    $val['date'] = $sdate;
                    $result = (object) $val;
                }

                $attendance[$dt->format("Y-m")][] = $result;
                $key++;
                $flag = '';
            }
        }

        $from = $start_date;
        $to = $end_date;

        return view('hr::report.partials.attendance_report_section')->with(compact(
            'period',
            'from',
            'to',
            'dateSl',
            'attendance',
            'employee'
        ));
    }

    public function getEmployeeReport(Request $request)
    {

        if (request()->ajax()) {
            $business_id = request()->session()->get('user.business_id');

            $employee = Employee::where('business_id', $business_id);

            if (!empty($request->department_id)) {
                $employee->where('department_id', $request->department_id);
            }


            return DataTables::of($employee)
                ->removeColumn('id')
                ->editColumn('name', function ($row) {
                    return $row->first_name . ' ' . $row->last_name;
                })
                ->addColumn('name', function ($row) {
                    return $row->first_name . ' ' . $row->last_name;
                })
                ->editColumn('department', function ($row) {
                    if (!empty($row->department_id)) {
                        $dep_name = Department::where('id', $row->department_id)->first()->department;
                    } else {
                        $dep_name = '';
                    }
                    return $dep_name;
                })

                ->editColumn('title', function ($row) {
                    if (!empty($row->title)) {
                        $job_title = JobTitle::where('id', $row->title)->first()->job_title;
                    } else {
                        $job_title = '';
                    }
                    return $job_title;
                })
                ->editColumn('work_shift', function ($row) {
                    if (!empty($row->work_shift)) {
                        $shift_name = WorkShift::where('id', $row->work_shift)->first()->shift_name;
                    } else {
                        $shift_name = '';
                    }
                    return $shift_name;
                })

                ->rawColumns(['name'])
                ->make(true);
        }
    }

    public function getPayrollReport(Request $request)
    {
        $business_id = $request->session()->get('business.id');
        if ($request->ajax()) {
            $payments = Payroll::where('payrolls.business_id', $business_id)
                ->select([
                    'payrolls.*'
                ]);
            if (!empty($request->employee_id)) {
                $payments->where('payrolls.employee_id', $request->employee_id);
            }
            if (!empty($request->month)) {
                $payments->where('payrolls.month', $request->month);
            }

            return DataTables::of($payments)
                ->removeColumn('id')
                ->rawColumns([])
                ->make(true);
        }
    }

    public function getEmployeeByDerpartment(Request $request)
    {
        $department_id = $request->department_id;

        $html = '<option>Please Select</option>';
        if (!empty($department_id)) {
            $employees = Employee::where('department_id', $department_id)->select("id", DB::raw("CONCAT(employees.first_name,' ',employees.last_name) as full_name", 'id'))->get();
            foreach ($employees  as $employee) {
                $html .= '<option value="' . $employee->id . '">' . $employee->full_name . '</option>';
            }
        }
        return $html;
    }
}
