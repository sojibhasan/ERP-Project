<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\PatientDetail;
use App\User;
use Illuminate\Support\Facades\Auth;
use App\Utils\BusinessUtil;
use App\Utils\ModuleUtil;

class HospitalController extends Controller
{
    protected $businessUtil;
    protected $moduleUtil;
    /**
     * Constructor
     *
     * @param ProductUtils $product
     * @return void
     */
    public function __construct(BusinessUtil $businessUtil, ModuleUtil $moduleUtil)
    {
        $this->businessUtil = $businessUtil;
        $this->moduleUtil = $moduleUtil;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
       
        if(!empty(request()->patient_code)){
            //Check if subscribed or not, then check for location quota
            if (!$this->moduleUtil->isSubscribed(request()->session()->get('business.id'))) {
                return $this->moduleUtil->expiredResponse();
            }
            $blood_groups = ['AB', 'A-', 'B-', 'O-', 'AB+', 'A+', 'B+', 'O+', 'Not Known', ''];
            $marital_statuss = ['Married', 'UnMarried', ''];
            $genders = ['Male', 'Female', ''];
            $patinet = User::leftjoin('patient_details', 'users.id', 'patient_details.user_id')
                        ->where('username', request()->patient_code)
                        ->orWhere('first_name', request()->patient_code)
                        ->orWhere('patient_details.mobile', request()->patient_code)
                        ->select('users.id', 'users.username')
                        ->first();
            
            if(empty($patinet)){
                $output = [
                    'success' => 0,
                    'msg' => __("patient.no_patient_found")
                ];
                return back()->with('status', $output);
            }
            $patient_code = $patinet->username;
            $patinet_id = $patinet->id;
            $patient_business_id = $patinet->id;
            $patient_details = PatientDetail::where('user_id', $patinet_id)->first();
            if(empty($patient_details)){
                $output = [
                    'success' => 0,
                    'msg' => __("patient.no_patient_found")
                ];
                return back()->with('status', $output);
            }
            $blood_group = $blood_groups[!empty($patient_details->blood_group) ? $patient_details->blood_group-1 : 9];
            $marital_status = $marital_statuss[!empty($patient_details->marital_status) ? $patient_details->marital_status-1 : 2];
            $gender = $genders[!empty($patient_details->gender) ? $patient_details->gender-1 : 2];
    
            $dateOfBirth =  date('Y-m-d', strtotime($patient_details->date_of_birth));
            $today = date("Y-m-d");
            $diff = date_diff(date_create($dateOfBirth), date_create($today));
            $age = $diff->format('%y');

            $business_id = request()->session()->get('business.id');
            $fy = $this->businessUtil->getCurrentFinancialYear($business_id);
            $date_filters['this_fy'] = $fy;
            $date_filters['this_month']['start'] = date('Y-m-01');
            $date_filters['this_month']['end'] = date('Y-m-t');
            $date_filters['this_week']['start'] = date('Y-m-d', strtotime('monday this week'));
            $date_filters['this_week']['end'] = date('Y-m-d', strtotime('sunday this week'));

    
            return view('hospital.index')->with(compact('date_filters', 'patient_code', 'patient_business_id', 'patient_details', 'blood_group', 'age', 'marital_status', 'gender'));
        }
        return view('hospital.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
       return view('business.partials.register_form');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
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
}
