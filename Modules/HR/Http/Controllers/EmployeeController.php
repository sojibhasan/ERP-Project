<?php

namespace Modules\HR\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\HR\Entities\Employee;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;
use App\BusinessLocation;
use App\Currency;
use App\User;
use App\Utils\ModuleUtil;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use Modules\HR\Entities\Component;
use Modules\HR\Entities\Department;
use Modules\HR\Entities\EmploymentStatus;
use Modules\HR\Entities\HrPrefix;
use Modules\HR\Entities\JobTitle;
use Modules\HR\Entities\LeaveApplicationType;
use Modules\HR\Entities\Religion;
use Modules\HR\Entities\Salary;
use Modules\HR\Entities\SalaryComponent;
use Modules\HR\Entities\SalaryGrade;
use Modules\HR\Entities\WorkShift;

class EmployeeController extends Controller
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

    public function home()
    {
        print_r('asdf');
        die();
        return view('employee.home');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (request()->ajax()) {
            $business_id = request()->session()->get('user.business_id');
            $employee = Employee::where('business_id', $business_id);
            if (request()->has('location_id')) {
                $location_id = request()->get('location_id');
                if (!empty($location_id)) {
                    $employee->where('location_id', $location_id);
                }
            }

            $permitted_locations = auth()->user()->permitted_locations();
            if ($permitted_locations != 'all') {
                $employee->whereIn('location_id', $permitted_locations);
            }


            return Datatables::of($employee)
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
                        if ($row->active == 1) {
                            if (auth()->user()->can('employee.edit')) {
                                $html .= '<li><a href="' . action("\Modules\HR\Http\Controllers\EmployeeController@edit", [$row->id]) . '" class="" style="margin-right: 10px;"><i class="glyphicon glyphicon-edit"></i>' . __("messages.edit") . '</a></li>   ';
                            }

                            $html .= '<li> <a data-href="' . action("\Modules\HR\Http\Controllers\EmployeeController@destroy", [$row->id]) . '" class="delete_employee" style="cursor:pointer"><i class="glyphicon glyphicon-trash"></i>' . __("messages.delete") . '</a></li>
                                <li><a href="' . action("\Modules\HR\Http\Controllers\EmployeeController@show", [$row->id]) . '?view_type=view" class="" style="margin-right: 10px;"><i class="fa fa-eye"></i>' . __("messages.view") . '</a></li>   
                                <li><a href="' . action("\Modules\HR\Http\Controllers\EmployeeController@show", [$row->id]) . '?view_type=loans" class="" style="margin-right: 10px;"><i class="fa fa-arrow-down"></i>' . __("hr::lang.loans") . '</a></li>   
                                <li><a href="' . action("\Modules\HR\Http\Controllers\EmployeeController@show", [$row->id]) . '?view_type=advanes" class="" style="margin-right: 10px;"><i class="fa fa-arrow-up"></i>' . __("hr::lang.advances") . '</a></li>   
                                <li><a href="' . action("\Modules\HR\Http\Controllers\EmployeeController@show", [$row->id]) . '?view_type=salaries" class="" style="margin-right: 10px;"><i class="fa fa-money"></i>' . __("hr::lang.salaries") . '</a></li>   
                                ';
                        }
                        $html .= '<li> <a data-href="' . action("\Modules\HR\Http\Controllers\EmployeeController@toggleActive", [$row->id]) . '" class="toggle_active_employee" style="cursor:pointer"><i class=""></i>' . __("hr::lang.active_inactive") . '</a></li>';

                        $html .= '</ul></div>';
                        return $html;
                    }
                )
                ->removeColumn('id')
                ->editColumn('name', function ($row) {
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

                ->editColumn('employment_status', function ($row) {
                    if (!empty($row->employment_status)) {
                        $status_name = EmploymentStatus::where('id', $row->employment_status)->first()->status_name;
                    } else {
                        $status_name = '';
                    }
                    return $status_name;
                })
                ->editColumn('work_shift', function ($row) {
                    if (!empty($row->work_shift)) {
                        $shift_name = WorkShift::where('id', $row->work_shift)->first()->shift_name;
                    } else {
                        $shift_name = '';
                    }
                    return $shift_name;
                })

                ->rawColumns(['name', 'action'])
                ->make(true);
        }

        $business_id = request()->session()->get('user.business_id');

        $business_locations = BusinessLocation::forDropdown($business_id, true);
        $employees = Employee::where('business_id', $business_id)->pluck('first_name', 'id');
        $countries = DB::table('countries')->get();
        $locations = BusinessLocation::where('business_id', $business_id)->get();
        $leave_types = LeaveApplicationType::where('business_id', $business_id)->pluck('leave_type', 'id');
        $users = User::where('business_id', $business_id)->pluck('username', 'id');

        $permissions['employee'] = $this->moduleUtil->hasThePermissionInSubscription($business_id, 'employee');
        $permissions['teminated'] = $this->moduleUtil->hasThePermissionInSubscription($business_id, 'teminated');
        $permissions['award'] = $this->moduleUtil->hasThePermissionInSubscription($business_id, 'award');
        $permissions['leave_request'] = $this->moduleUtil->hasThePermissionInSubscription($business_id, 'leave_request');


        return view('hr::employee.index')
            ->with(compact('countries', 'locations', 'business_locations', 'employees', 'leave_types', 'users', 'permissions'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $business_id = request()->session()->get('user.business_id');
        $countries = DB::table('countries')->get();
        $locations = BusinessLocation::where('business_id', $business_id)->get();

        $prefix_settings = HrPrefix::where('business_id', $business_id)->first();
        $employee_number = '';
        $employee_count = Employee::where('business_id', $business_id)->count();
        $departments = Department::where('business_id', $business_id)->pluck('department', 'id');
        $religions = Religion::where('business_id', $business_id)->where('religion_status', 1)->orderBy('religion_name')->pluck('religion_name', 'id');
        if (!empty($prefix_settings)) {
            $employee_count = $employee_count + $prefix_settings->employee_starting_number;
            if (!empty($prefix_settings->employee_prefix)) {
                $employee_number =  $prefix_settings->employee_prefix . $employee_count;
            } else {
                $employee_number =  $employee_count;
            }
        }
        $employee_number = $employee_number . '-' . $business_id;

        return view('hr::employee.create')
            ->with(compact('countries', 'locations', 'employee_number', 'departments', 'religions'));
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

        if (!$this->moduleUtil->isQuotaAvailable('employees', $business_id)) {
            return $this->moduleUtil->quotaExpiredResponse('employees', $business_id, action('Modules\HR\Http\Controllers\Employeecontroller@index'));
        }

        $validator = Validator::make($request->all(), [
            'employee_number' => 'required',
            'password' => 'required|min:6',
        ]);

        if ($validator->fails()) {
            $output = [
                'success' => 0,
                'msg' => $validator->errors()->all()[0]
            ];
            return redirect()->back()->with('status', $output);
        }

        $prefix = 10000;
        $data = array(
            'business_id'    => $business_id,
            'employee_number' => $request->input('employee_number'),
            'username' => $request->input('employee_number'),
            'location_id'    => $request->input('business_location'),
            'first_name'     => $request->input('first_name'),
            'last_name'      => $request->input('last_name'),
            'marital_status' => $request->input('marital_status'),
            'date_of_birth'  => $request->input('date_of_birth'),
            'country'        => $request->input('country'),
            'blood_group'    => $request->input('blood_group'),
            'id_number'      => $request->input('id_number'),
            'religious'      => $request->input('religious'),
            'gender'         => $request->input('gender'),
            'password'         => Hash::make($request->input('password')),
            'department_id'         => $request->input('department_id')
        );

        $id = Employee::insertGetId($data);

        $employee_id = $prefix + $id;

        if (!file_exists('./public/employee/' . $employee_id)) {
            mkdir('./public/employee/' . $employee_id, 0777, true);
        }

        if ($request->hasfile('employee_photo')) {
            $file = $request->file('employee_photo');
            $extension = $file->getClientOriginalExtension();
            $filename = time() . '.' . $extension;
            $file->move('./public/employee/' . $employee_id, $filename);
            $employee_photo = './public/employee/' . $employee_id . '/' . $filename;
        } else {
            $employee_photo = '';
        }

        $data = array(
            'employee_id'   => $employee_id,
            'photo'         => $employee_photo,
        );

        Employee::where('id', $id)->update($data);

        $output = [
            'success' => 1,
            'msg' => __('hr::lang.employee_add_success')
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
        $view_type = request()->view_type;
        $employee = Employee::findOrFail($id);

        return view('hr::employee.show')->with(compact(
            'view_type',
            'employee'
        ));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $employee = Employee::findOrFail($id);
        $empSalary = Salary::where('employee_id', $id)->first();
        $gradeList = SalaryGrade::get();
        $salaryEarningList = SalaryComponent::where('type', 1)->where('statutory_fund', 0)->get();
        $salaryDeductionList = SalaryComponent::where('type', 2)->where('statutory_fund', 0)->get();
        $statutoryPaymentsList = SalaryComponent::where('statutory_fund', 1)->get();

        $currency_id = Session::get('business.currency_id');
        $currecy_symbol = Currency::where('id', $currency_id)->first()->symbol;
        $religions = Religion::get();
        $countries = DB::table('countries')->get();
        return view('hr::employee.edit')->with(compact('employee', 'countries', 'religions', 'empSalary', 'gradeList', 'salaryEarningList', 'salaryDeductionList', 'statutoryPaymentsList', 'currecy_symbol'));
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
        $business_id = request()->session()->get('user.business_id');

        try {
            $contact_details = array(
                'address_1'         => $request->input('address_1'),
                'address_2'         => $request->input('address_2'),
                'city'              => $request->input('city'),
                'state'             => $request->input('state'),
                'postal'            => $request->input('postal'),
                'country'           => $request->input('country'),
                'home_telephone'    => $request->input('home_telephone'),
                'mobile'            => $request->input('mobile'),
                'work_telephone'    => $request->input('work_telephone'),
                'work_email'        => $request->input('work_email'),
                'other_email'       => $request->input('other_email'),
            );

            $deposit = array(
                'account_name'      => $request->input('account_name'),
                'account_number'    => $request->input('account_number'),
                'bank_name'         => $request->input('bank_name'),
                'note'              => $request->input('note'),
            );
            $data = array(
                'business_id'       => $business_id,
                'location_id'       => $request->input('business_location'),
                'first_name'        => $request->input('first_name'),
                'last_name'         => $request->input('last_name'),
                'marital_status'    => $request->input('marital_status'),
                'date_of_birth'     => $request->input('date_of_birth'),
                'country'           => $request->input('country'),
                'blood_group'       => $request->input('blood_group'),
                'id_number'         => $request->input('id_number'),
                'religious'         => $request->input('religious'),
                'gender'            => $request->input('gender'),
                'joined_date'          => date('Y-m-d', strtotime($request->input('joined_date'))),
                'date_of_permanency'   => date('Y-m-d', strtotime($request->input('date_of_permanency'))),
                'probation_end_date'   => date('Y-m-d', strtotime($request->input('probation_end_date'))),
                'contact_details'   => \json_encode($contact_details),
                'deposit'           => \json_encode($deposit),

            );
            if (!empty($request->input('password'))) {
                $data['password'] = Hash::make($request->input('password'));
            }

            if (!file_exists('./public/employee/' . $id)) {
                mkdir('./public/employee/' . $id, 0777, true);
            }

            if ($request->hasfile('employee_photo')) {
                $file = $request->file('employee_photo');
                $extension = $file->getClientOriginalExtension();
                $filename = time() . '.' . $extension;
                $file->move('./public/employee/' . $id, $filename);
                $employee_photo = './public/employee/' . $id . '/' . $filename;
                $data['photo'] = $employee_photo;
            }


            Employee::where('id', $id)->update($data);

            //salary 
            $type = $request->input('type');
            $salary_id = $request->input('salary_id');

            if (empty($salary_id)) {
                $output = [
                    'success' => true,
                    'msg' => __("hr::lang.no_record_found")
                ];
            }

            $data_salaries['employee_id'] = $id;
            if ($type == 'Hourly') {

                $hourly_data = array(
                    'hourly_salary' => (float) $request->input('hourly_salary')
                );

                if (!empty($salary_id)) {
                    //update data
                    Salary::where('id', $salary_id)->update($hourly_data);
                } else {
                    //insert data
                    Salary::insert($hourly_data);
                }
            } else {
                $records = SalaryComponent::get();

                $data_salaries['grade_id']           = $request->grade_id;
                $data_salaries['comment']            = $request->comment;
                $earning_id                 = !empty($request->earn) ? $request->earn : [];
                $deduction_id               = !empty($request->deduction) ? $request->earn : [];
                // print_r(sizeof($earning_id)); die();
                $total_cost_company = 0;
                $total_payable = 0;
                $total_deduction = 0;
                $basic_salary = 0;
                for ($i = 0; $i < sizeof($earning_id); $i++) {
                    if ($earning_id[$i] == 0)
                        continue;

                    $dbData['component_id'][] = $earning_id[$i];
                    $dbData['salary'][] = $earning_id[$i];

                    //check payment type
                    foreach ($records as $record) {
                        if ($record->id == $earning_id[$i]) {
                            if ($record->value_type == 1) //Amount
                            {
                                if ($record->total_payable == 1) //total payable
                                {
                                    $total_payable += $earning_id[$i];
                                }
                                if ($record->cost_company == 1) //cost to company
                                {
                                    $total_cost_company += $earning_id[$i];
                                }
                            }
                            if ($record->value_type == 2) //percentage
                            {
                                if ($record->total_payable == 1) //total payable
                                {
                                    $total_payable += ($basic_salary * $earning_id[$i]) / 100;
                                }
                                if ($record->cost_company == 1) //cost to company
                                {
                                    $total_cost_company += ($basic_salary * $earning_id[$i]) / 100;
                                }
                            }
                        }
                    }
                }

                for ($j = 0; $j < sizeof($deduction_id); $j++) {
                    if ($deduction_id[$j] == 0)
                        continue;

                    $dbData['component_id'][] = $deduction_id[$j];
                    $dbData['salary'][] = $deduction_id[$j];

                    foreach ($records as $record) {
                        if ($record->id == $deduction_id[$j]) {
                            if ($record->value_type == 1) //Amount
                            {
                                $total_deduction += $deduction_id[$j];
                                if ($record->total_payable == 1) //total payable
                                {
                                    $total_payable -= $deduction_id[$j];
                                }
                                if ($record->cost_company == 1) //cost to company
                                {
                                    $total_cost_company += $deduction_id[$j];
                                }
                            }
                            if ($record->value_type == 2) //percentage
                            {
                                $total_deduction += ($basic_salary * $deduction_id[$j]) / 100;
                                $deduction = ($basic_salary * $deduction_id[$j]) / 100;
                                if ($record->total_payable == 1) //total payable
                                {
                                    $total_payable -= $deduction;
                                }
                                if ($record->cost_company == 1) //cost to company
                                {
                                    $total_cost_company += $deduction;
                                }
                            }
                        }
                    }
                }

                $data_salaries['total_payable']      = $total_payable;
                $data_salaries['total_cost_company'] = $total_cost_company;
                $data_salaries['total_deduction']    = $total_deduction;
                $salaryDetails = array();
                if (!empty($dbData['component_id'])) {
                    for ($j = 0; $j < sizeof($dbData['component_id']); $j++) {
                        $salaryDetails[$dbData['component_id'][$j]] = $dbData['salary'][$j];
                        $componentID[] = $dbData['component_id'][$j];
                    }
                }

                //save component
                $salaryComponent = SalaryComponent::select('id')->get();
                $m = 0;
                foreach ($salaryComponent as $key => $item) {
                    if (count($componentID) >= $m + 1) {
                        $comp = $componentID[$key];
                    } else {
                        $comp = 0;
                    }
                    if ($item->id == $comp) {
                        $component['component_id'] = $item->id;
                        $component['employee_id'] = $data_salaries['employee_id'];

                        $result = Component::where('employee_id', $data_salaries['employee_id'])->where('component_id', $item->id)->first();

                        if (empty($result)) {
                            Component::insert($component);
                        }
                    } else {
                        Component::where('employee_id', $data_salaries['employee_id'])->where('component_id', $item->id)->delete();
                    }
                    $m++;
                }

                $data_salaries['component'] = json_encode($salaryDetails);

                if (!empty($salary_id)) {
                    //update data
                    Salary::where('id', $salary_id)->update($data_salaries);
                } else {
                    //insert data
                    Salary::insert($data_salaries);
                }
            }
            $output = [
                'success' => true,
                'msg' => __('hr::lang.employee_update_success')
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
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {

        if (request()->ajax()) {
            try {
                $business_id = request()->session()->get('user.business_id');

                $employee = Employee::where('business_id', $business_id)
                    ->where('id', $id)
                    ->first();
                $employee->delete();

                $output = [
                    'success' => true,
                    'msg' => __("hr::lang.employee_delete_success")
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

    /**
     * Display a listing of the teminated employee.
     *
     * @return \Illuminate\Http\Response
     */
    public function teminatedEmployee()
    {
        if (request()->ajax()) {
            $business_id = request()->session()->get('user.business_id');

            $employee = Employee::where('business_id', $business_id)->where('termination', '0');

            if (request()->has('location_id')) {
                $location_id = request()->get('location_id');
                if (!empty($location_id)) {
                    $employee->where('location_id', $location_id);
                }
            }

            $permitted_locations = auth()->user()->permitted_locations();
            if ($permitted_locations != 'all') {
                $employee->whereIn('location_id', $permitted_locations);
            }


            return Datatables::of($employee)
                ->addColumn(
                    'action',
                    '<div class="btn-group">
                        <a href="{{action(\'\Modules\HR\Http\Controllers\EmployeeController@edit\', [$id])}}"><i class="glyphicon glyphicon-edit"></i> @lang("messages.edit")</a>
                    
                        <a data-href="{{action(\'\Modules\HR\Http\Controllers\EmployeeController@destroy\', [$id])}}" class="delete_employee"><i class="glyphicon glyphicon-trash" style="color:brown;"></i> @lang("messages.delete")</a>
                    </div>'
                )
                ->removeColumn('id')
                ->editColumn('name', function ($row) {
                    return $row->first_name . ' ' . $row->last_name;
                })
                ->editColumn('department', function ($row) {
                    if (!empty($row->department)) {
                        $dep_name = Department::where('id', $row->department)->first()->department;
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

                ->editColumn('employment_status', function ($row) {
                    if (!empty($row->employment_status)) {
                        $status_name = EmploymentStatus::where('id', $row->employment_status)->first()->status_name;
                    } else {
                        $status_name = '';
                    }
                    return $status_name;
                })

                ->editColumn('employment_status', function ($row) {
                    if (!empty($row->employment_status)) {
                        $status_name = EmploymentStatus::where('id', $row->employment_status)->first()->status_name;
                    } else {
                        $status_name = '';
                    }
                    return $status_name;
                })
                ->editColumn('work_shift', function ($row) {
                    if (!empty($row->work_shift)) {
                        $shift_name = WorkShift::where('id', $row->work_shift)->first()->shift_name;
                    } else {
                        $shift_name = '';
                    }
                    return $shift_name;
                })

                ->rawColumns(['name', 'action'])
                ->make(true);
        }

        $business_id = request()->session()->get('user.business_id');

        $business_locations = BusinessLocation::forDropdown($business_id, true);
        $countries = DB::table('countries')->get();
        $locations = BusinessLocation::where('business_id', $business_id)->get();
        return view('hr::employee.teminated')
            ->with(compact('countries', 'locations', 'business_locations'));
    }


    public function getImportEmployee()
    {
        return view('hr::employee.import');
    }

    public function postImportEmployee(Request $request)
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

            if (!$this->moduleUtil->isQuotaAvailable('employees', $business_id, $total_rows)) {
                return $this->moduleUtil->quotaExpiredResponse('employees', $business_id, action('Modules\HR\Http\Controllers\Employeecontroller@index'));
            }

            $location_id = BusinessLocation::where('business_id', $business_id)->first()->id;

            DB::beginTransaction();
            foreach ($imported_data as $key => $value) {
                $employee_id = trim($value[0]);
                $first_name = trim($value[1]);
                $last_name = trim($value[2]);
                $marital_status = trim($value[3]);
                $date_of_birth = trim($value[4]);
                $id_number = trim($value[5]);
                $gender = trim($value[6]);


                $data = array(
                    'business_id'  => $business_id,
                    'location_id'  => $location_id,
                    'employee_id'  => $employee_id,
                    'first_name'  => $first_name,
                    'last_name'  => $last_name,
                    'marital_status'  => $marital_status,
                    'date_of_birth'  => $date_of_birth,
                    'id_number'  => $id_number,
                    'gender'  => $gender
                );

                $id = Employee::insertGetId($data);
            }
            DB::commit();
            $output = [
                'success' => true,
                'msg' => __("messages.import_successful")
            ];
            return redirect()->back()->with(compact('output'));
        } else {
            $output = [
                'success' => false,
                'msg' => __("messages.something_went_wrong")
            ];
            return redirect()->back()->with(compact('output'));
        }


        $tmp = explode(".", $_FILES['import']['name']); // For getting Extension of selected file
        $extension = end($tmp);
        $allowed_extension = array("xls", "xlsx", "csv"); //allowed extension
        if (in_array($extension, $allowed_extension)) //check selected file extension is present in allowed extension array
        {
            $this->load->library('Data_importer');
            $file = $_FILES["import"]["tmp_name"]; // getting temporary source of excel file
            $this->data_importer->employee_excel_import($file);
        } else {
            $output = [
                'success' => false,
                'msg' => __("messages.something_went_wrong")
            ];
            return redirect()->back()->with(compact('output'));
        }


        $this->mViewData['form'] = $this->form_builder->create_form();

        $this->mTitle .= lang('import_data');
        $this->render('import/import_employee');

        return view('hr::employee.import');
    }

    public function toggleActive($id)
    {
        try {
            $employee = Employee::findOrFail($id);
            $employee->active = !$employee->active;
            $employee->save();
            $output = [
                'success' => true,
                'msg' => __('hr::lang.success')
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
