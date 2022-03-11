<?php

namespace Modules\HR\Http\Controllers;

use App\Currency;
use App\Utils\BusinessUtil;
use App\Utils\ModuleUtil;
use App\Utils\TransactionUtil;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Modules\HR\Entities\Department;
use Modules\HR\Entities\Employee;
use Modules\HR\Entities\EmployeeAward;
use Modules\HR\Entities\Payroll;
use Modules\HR\Entities\Religion;
use Modules\HR\Entities\Salary;
use Modules\HR\Entities\SalaryComponent;
use Symfony\Component\VarDumper\Caster\RedisCaster;
use Yajra\DataTables\Facades\DataTables;

class PayrollPaymentController extends Controller
{
    /**
     * All Utils instance.
     *
     */
    protected $moduleUtil;
    protected $transactionUtil;

    private $barcode_types;

    /**
     * Constructor
     *
     * @param ProductUtils $product
     * @return void
     */
    public function __construct(ModuleUtil $moduleUtil, TransactionUtil $transactionUtil, BusinessUtil $businessUtil)
    {
        $this->moduleUtil = $moduleUtil;
        $this->transactionUtil = $transactionUtil;
        $this->businessUtil = $businessUtil;
    }

    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        $business_id = request()->session()->get('business.id');
        if (request()->ajax()) {
            $payments = Payroll::leftjoin('employees', 'payrolls.employee_id', 'employees.id')
                ->leftjoin('employment_statuses', 'employees.employment_status', 'employment_statuses.id')
                ->where('payrolls.business_id', $business_id)
                ->select([
                    'payrolls.*',
                    'employees.employee_id',
                    'employees.first_name',
                    'employees.last_name',
                    'employees.employee_number'
                ]);

            if (!empty(request()->employee_id)) {
                $payments->where('payrolls.employee_id', request()->employee_id);
            }
            if (!empty(request()->month)) {
                $payments->where('payrolls.month', request()->month);
            }


            return DataTables::of($payments)
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

                        $html .= '<li><a href="#" data-href="' . action('\Modules\HR\Http\Controllers\PayrollPaymentController@edit', [$row->id]) . '" data-container=".payment_modal" class="btn-modal payment_eidt"><i class="glyphicon glyphicon-edit"></i> ' . __("messages.edit") . '</a></li>';
                        $html .= '<li><a href="#" data-href="' . action('\Modules\HR\Http\Controllers\PayrollPaymentController@printPayment', [$row->id]) . '" class="payment_print"><i class="fa fa-print"></i> ' . __("messages.print") . '</a></li>';
                        $html .= '</ul></div>';

                        return $html;
                    }
                )
                ->addColumn(
                    'employee_name',
                    '{{$first_name}} {{$last_name}}'
                )
                ->removeColumn('id')
                ->rawColumns(['action'])
                ->make(true);
        }

        $employees = Employee::where('business_id', $business_id)->select("id", DB::raw("CONCAT(employees.first_name,' ',employees.last_name) as full_name"))->pluck('full_name', 'id');

        $permissions['salary_details'] = $this->moduleUtil->hasThePermissionInSubscription($business_id, 'salary_details');
        $permissions['basic_salary'] = $this->moduleUtil->hasThePermissionInSubscription($business_id, 'basic_salary');
        $permissions['payroll_payments'] = $this->moduleUtil->hasThePermissionInSubscription($business_id, 'payroll_payments');

        $employees = Employee::where('business_id', $business_id)->get();
        $salary_earning_list = SalaryComponent::where('type', 1)->where('statutory_fund', 0)->get();
        $salary_deduction_list = SalaryComponent::where('type', 2)->where('statutory_fund', 0)->get();
        $statutory_payments_list = SalaryComponent::where('statutory_fund', 1)->get();

        $currency_id = Session::get('business.currency_id');
        $currecy_symbol = Currency::where('id', $currency_id)->first()->symbol;

        $countries = DB::table('countries')->get();
        $religions = Religion::all();

        return view('hr::payroll_payment.index')->with(compact(
            'employees',
            'permissions',
            'employees',
            'salary_earning_list',
            'salary_deduction_list',
            'statutory_payments_list',
            'countries',
            'religions'
        ));
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create()
    {
        $business_id = request()->session()->get('business.id');
        $departments = Department::where('business_id', $business_id)->pluck('department', 'id');

        return view('hr::payroll_payment.create')->with(compact('departments'));
    }

    /**
     * Store a newly created resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function store(Request $request)
    {

        $business_id = request()->session()->get('business.id');
        $validator = Validator::make($request->all(), [
            'payment_method' => 'required',
        ]);

        if ($validator->fails()) {
            $output = [
                'success' => 0,
                'msg' => $validator->errors()->all()[0]
            ];
            return redirect()->back()->with('status', $output);
        }

        try {
            $employee_id = $request->employee_id;
            $month = $request->month;

            //check duplicate payroll
            $payroll = Payroll::where('business_id', $business_id)->where('employee_id', $employee_id)->where('month', $month)->first();
            if (!empty($payroll)) {
                $payroll_id = $payroll->id;
            } else {
                $payroll_id = null;
            }
            $salary = Salary::where('business_id', $business_id)->where('employee_id', $employee_id)->first();
            $award = EmployeeAward::where('business_id', $business_id)->where('employee_id', $employee_id)->where('award_month', $month)->get();
            $employee = Employee::findOrFail($employee_id);

            $data['business_id']    = $business_id;
            $data['employee_id']    = $employee_id;
            $data['department_id']  = $employee->department_id;
            $data['gross_salary']   = $salary->total_payable + $salary->total_deduction;
            $data['deduction']      = $salary->total_deduction;
            $data['net_salary']     = $salary->total_payable;

            $total_payable = $salary->total_payable;

            if (!empty($award)) {
                $employee_award = array();
                foreach ($award as $item) {
                    if (!empty($item->award_amount)) {
                        $employee_award[] = array(
                            'award_name' => $item->award_name,
                            'award_amount' => $item->award_amount,
                        );
                        $total_payable += $item->award_amount;
                    }
                }
                $data['award'] = json_encode($employee_award);
            }

            //fine deduction
            if (!empty($request->fine_deduction)) {
                $data['fine_deduction'] = $request->fine_deduction;
                $total_payable -= $data['fine_deduction'];
            }

            //add bonus
            if (!empty($request->bonus)) {
                $data['bonus'] = $request->bonus;
                $total_payable += $data['bonus'];
            }

            $data['net_payment'] = $total_payable;
            $data['payment_method'] = $request->payment_method;
            $data['note'] = $request->note;
            $data['month'] = $month;

            //validation check @id and @month
            if ($employee_id && $month) { //validation check
                if ($payroll_id) { //update
                    Payroll::where('id', $payroll_id)->update($data);
                    $paySlipID = $payroll_id;
                } else { //insert new data
                    $payroll_created = Payroll::create($data);
                    $paySlipID = $payroll_created->id;
                }
            }
            $output = [
                'success' => true,
                'msg' => __('hr::lang.payment_create_success')
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
        $payroll = Payroll::findOrFail($id);
        $department_id = $payroll->department_id;
        $employee_id = $payroll->employee_id;
        $month = $payroll->month;

        $department  = Department::findOrFail($department_id);
        $employee    = Employee::leftjoin('job_titles', 'employees.job_title', 'job_titles.id')
            ->leftjoin('employment_statuses', 'employees.employment_status', 'employment_statuses.id')
            ->where('employees.id', $employee_id)
            ->select('employees.*', 'job_titles.job_title', 'employment_statuses.status_name')
            ->first();
        $salaryDeductionList = SalaryComponent::where('business_id', $business_id)->where('type', 2)->where('statutory_fund', 0)->get();
        $statutoryPaymentsList = SalaryComponent::where('business_id', $business_id)->where('statutory_fund', 1)->get();

        $salary = Salary::where('business_id', $business_id)->where('employee_id', $employee_id)->first();
        $type = $payroll->type;
        $award = EmployeeAward::where('award_month', $month)->where('employee_id', $employee_id)->get();

        return view('hr::payroll_payment.edit')->with(compact(
            'business_id',
            'department_id',
            'employee_id',
            'month',
            'department',
            'employee',
            'salaryDeductionList',
            'statutoryPaymentsList',
            'salary',
            'type',
            'payroll'
        ));
    }

    /**
     * Update the specified resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function update(Request $request, $id)
    {
        $business_id = request()->session()->get('business.id');
        $validator = Validator::make($request->all(), [
            'payment_method' => 'required',
        ]);

        if ($validator->fails()) {
            $output = [
                'success' => 0,
                'msg' => $validator->errors()->all()[0]
            ];
            return redirect()->back()->with('status', $output);
        }

        try {
            $employee_id = $request->employee_id;
            $month = $request->month;
            $payroll_id = $id;
            //check duplicate payroll
            $payroll = Payroll::where('business_id', $business_id)->where('employee_id', $employee_id)->where('month', $month)->first();

            $salary = Salary::where('business_id', $business_id)->where('employee_id', $employee_id)->first();
            $award = EmployeeAward::where('business_id', $business_id)->where('employee_id', $employee_id)->where('award_month', $month)->get();
            $employee = Employee::findOrFail($employee_id);

            $data['business_id']    = $business_id;
            $data['employee_id']    = $employee_id;
            $data['department_id']  = $employee->department_id;
            $data['gross_salary']   = $salary->total_payable + $salary->total_deduction;
            $data['deduction']      = $salary->total_deduction;
            $data['net_salary']     = $salary->total_payable;

            $total_payable = $salary->total_payable;

            if (!empty($award)) {
                $employee_award = array();
                foreach ($award as $item) {
                    if (!empty($item->award_amount)) {
                        $employee_award[] = array(
                            'award_name' => $item->award_name,
                            'award_amount' => $item->award_amount,
                        );
                        $total_payable += $item->award_amount;
                    }
                }
                $data['award'] = json_encode($employee_award);
            }

            //fine deduction
            if (!empty($request->fine_deduction)) {
                $data['fine_deduction'] = $request->fine_deduction;
                $total_payable -= $data['fine_deduction'];
            }

            //add bonus
            if (!empty($request->bonus)) {
                $data['bonus'] = $request->bonus;
                $total_payable += $data['bonus'];
            }

            $data['net_payment'] = $total_payable;
            $data['payment_method'] = $request->payment_method;
            $data['note'] = $request->note;
            $data['month'] = $month;

            //validation check @id and @month
            if ($employee_id && $month) { //validation check
                Payroll::where('id', $payroll_id)->update($data);
            }
            $output = [
                'success' => true,
                'msg' => __('hr::lang.payment_update_success')
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

    public function getMakePayment(Request $request)
    {
        $business_id = request()->session()->get('business.id');
        $department_id = $request->department_id;
        $employee_id = $request->employee_id;
        $month = $request->month;
        if (!empty($department_id) && !empty($employee_id) && !empty($month)) {
            $department  = Department::findOrFail($department_id);
            $employee    = Employee::leftjoin('job_titles', 'employees.job_title', 'job_titles.id')
                ->leftjoin('employment_statuses', 'employees.employment_status', 'employment_statuses.id')
                ->where('employees.id', $employee_id)
                ->select('employees.*', 'job_titles.job_title', 'employment_statuses.status_name')
                ->first();

            $salary = Salary::where('business_id', $business_id)->where('employee_id', $employee_id)->first();
            if (empty($salary)) {
                $output = [
                    'success' => 0,
                    'msg' => __('hr::lang.employe_salary_not_set_yet')
                ];
                return $output;
            }
            $empSalary =  $salary;
            if (!empty($empSalary->component)) {
                $empSalaryDetails = json_decode($empSalary->component, true);
            }
            $salaryDeductionList = SalaryComponent::where('business_id', $business_id)->where('type', 2)->where('statutory_fund', 0)->get();
            $statutoryPaymentsList = SalaryComponent::where('business_id', $business_id)->where('statutory_fund', 1)->get();

            if ($salary->type == 'Monthly') {
                $type = 'Monthly';
                $payroll = Payroll::where('employee_id', $employee_id)->where('month', $month)->where('business_id', $business_id)->first();
            } else {
                $type = 'Hourly';
            }

            $award = EmployeeAward::where('award_month', $month)->where('employee_id', $employee_id)->get();

            return view('hr::payroll_payment.partials.make_payment')->with(compact(
                'business_id',
                'department_id',
                'employee_id',
                'month',
                'department',
                'employee',
                'salary',
                'salaryDeductionList',
                'statutoryPaymentsList',
                'type',
                'payroll'
            ));
        } else {
            $output = [
                'success' => 0,
                'msg' => __('messages.something_went_wrong')
            ];

            return $output;
        }
    }


    public function printPayment($id)
    {
        $business_id = request()->session()->get('business.id');
        if (empty($id)) {
            $output = [
                'success' => 0,
                'msg' => __('hr::lang.no_record_found')
            ];
            return redirect()->back()->with('status', $output);
        }
        $pay_slip = Payroll::findOrFail($id);

        if (empty($pay_slip)) {
            $output = [
                'success' => 0,
                'msg' => __('hr::lang.no_record_found')
            ];
            return redirect()->back()->with('status', $output);
        }
        $employee    = Employee::leftjoin('job_titles', 'employees.job_title', 'job_titles.id')
            ->leftjoin('employment_statuses', 'employees.employment_status', 'employment_statuses.id')
            ->leftjoin('departments', 'employees.department_id', 'departments.id')
            ->where('employees.id', $pay_slip->employee_id)
            ->select('employees.*', 'job_titles.job_title', 'employment_statuses.status_name', 'departments.department')
            ->first();

        $salaryDeductionList = SalaryComponent::where('business_id', $business_id)->where('type', 2)->where('statutory_fund', 0)->get();
        $statutoryPaymentsList = SalaryComponent::where('business_id', $business_id)->where('statutory_fund', 1)->get();

        return view('hr::payroll_payment.partials.print_payment')->with(compact(
            'pay_slip',
            'employee',
            'salaryDeductionList',
            'statutoryPaymentsList'
        ));
    }

    public function getEmployeeSalariesPayment()
    {
        $business_id = request()->session()->get('business.id');
        if (request()->ajax()) {
            $payments = Payroll::leftjoin('employees', 'payrolls.employee_id', 'employees.id')
                ->leftjoin('employment_statuses', 'employees.employment_status', 'employment_statuses.id')
                ->where('payrolls.employee_id', Auth::user()->id)
                ->select([
                    'payrolls.*',
                    'employees.employee_id',
                    'employees.first_name',
                    'employees.last_name',
                    'employees.employee_number'
                ]);

            if (!empty(request()->month)) {
                $payments->where('payrolls.month', request()->month);
            }


            return DataTables::of($payments)
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

                        // $html .= '<li><a href="#" data-href="' . action('\Modules\HR\Http\Controllers\PayrollPaymentController@edit', [$row->id]) . '" data-container=".payment_modal" class="btn-modal payment_eidt"><i class="glyphicon glyphicon-edit"></i> ' . __("messages.edit") . '</a></li>';
                        $html .= '<li><a href="#" data-href="' . action('\Modules\HR\Http\Controllers\PayrollPaymentController@printPayment', [$row->id]) . '" class="payment_print"><i class="fa fa-print"></i> ' . __("messages.print") . '</a></li>';
                        $html .= '</ul></div>';

                        return $html;
                    }
                )
                ->addColumn(
                    'employee_name',
                    '{{$first_name}} {{$last_name}}'
                )
                ->removeColumn('id')
                ->rawColumns(['action'])
                ->make(true);
        }




        return view('employee.salary_payments');
    }
}
