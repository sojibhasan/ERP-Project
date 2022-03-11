<?php

namespace Modules\HR\Http\Controllers;

use App\Currency;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Modules\HR\Entities\BasicSalary;
use Modules\HR\Entities\Component;
use Modules\HR\Entities\Employee;
use Modules\HR\Entities\Religion;
use Modules\HR\Entities\Salary;
use Modules\HR\Entities\SalaryComponent;

class SalaryController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        return view('hr::index');
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create()
    {
       
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
            $type =  $request->type;
            $data['business_id'] = $business_id;
            $data['type'] = $type;

            if ($type == 'Hourly') {
                $data['employee_id'] = $request->employee_id_hourly;
                $data['hourly_salary'] = (float) $request->hourly_salary;
              
                Salary::create($data);
            } else {
                $data['employee_id'] = $request->employee_id_monthly;
                $records = SalaryComponent::where('business_id', $business_id)->get();

                $data['business_id']           =  $business_id;
                $data['grade_id']           =  $request->grade_id;
                $data['comment']            =  $request->comment;
                $data['salary_year']        =  $request->year;
                $data['salary_month']        =  $request->month;
                $earning_id                 =  $request->earn;
                $deduction_id               =  $request->deduction;
                $statutory_id               =  $request->statutory;
                -$total_cost_company = 0;
                $total_payable = 0;
                $total_deduction = 0;
                $total_statutory = 0;
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

                // print_r($basic_salary); die();
                for ($j = 0; $j < sizeof($statutory_id); $j++) {
                    if ($statutory_id[$j] == 0 || $statutory_id[$j] == '')
                        continue;

                    $dbData['component_id'][] = $statutory_id[$j];
                    $dbData['salary'][] = $statutory_id[$j];
                    $statutory_value = (int)$statutory_id[$j];
                    foreach ($records as $record) {
                        if ($record->id == $statutory_value) {
                            if ($record->value_type == 1) {
                                $total_statutory += $statutory_value;
                                if ($record->total_payable == 1) //total payable
                                {
                                    $total_payable -= $statutory_value;
                                }
                                if ($record->cost_company == 1) //cost to company
                                {
                                    $total_cost_company += $statutory_value;
                                }
                            }
                            if ($record->value_type == 2) //percentage
                            {
                                $total_statutory += ($basic_salary * $statutory_value) / 100;
                                $deduction = ($basic_salary * $statutory_value) / 100;
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

                $data['total_payable']      = $total_payable;
                $data['total_cost_company'] = $total_cost_company;
                $data['total_deduction']    = $total_deduction;
                $data['total_statutory']    = $total_statutory;
                $salaryDetails = array();
                for ($j = 0; $j < sizeof($dbData['component_id']); $j++) {
                    $salaryDetails[$dbData['component_id'][$j]] = $dbData['salary'][$j];
                    $componentID[] = $dbData['component_id'][$j];
                }

                //save component
                $salaryComponent = SalaryComponent::where('business_id', $business_id)->get();

                foreach ($salaryComponent as $key => $item) {
                    if ($item->id == $componentID[$key]) {
                        $component['component_id'] = $item->id;
                        $component['employee_id'] = $data['employee_id'];

                        Component::firstOrCreate($component);
                    } else {
                        Component::where('component_id', $item->id)->where('employee_id', $data['employee_id'])->delete();
                    }
                }

                $data['component'] = json_encode($salaryDetails);

                if (!empty($salary_id)) {
                    //update data
                    Salary::where('id', $salary_id)->update($data);
                    // $this->db->where('id', $salary_id);
                    // $this->db->update('salary', $data);
                } else {
                    //insert data
                    Salary::create($data);
                    // $this->db->insert('salary', $data);
                }
            }
            $output = [
                'success' => true,
                'msg' => __('hr::lang.salary_detail_create_success')
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
    public function destroy()
    {
    }


    public function getSalaryRangeByEmployeeId($id)
    {
        $business_id = request()->session()->get('business.id');
        $basicSalary  = BasicSalary::where('business_id', $business_id)->where('employee_id', $id)->first();
        if (!empty($basicSalary)) {
            return ['basic_salary' => $basicSalary->salary_amount];
        } else {
            return 0;
        }
    }
}
