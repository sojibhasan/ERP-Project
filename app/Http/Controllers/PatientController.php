<?php

namespace App\Http\Controllers;

use App\Business;
use App\Contact;
use Illuminate\Http\Request;
use App\PatientDetail;
use App\Utils\ModuleUtil;
use Illuminate\Support\Facades\Auth;
use App\User;
use App\Utils\BusinessUtil;
use Illuminate\Support\Facades\DB;

class PatientController extends Controller
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

        $user = User::where('id', auth()->user()->id)->first();
        $is_admin = $user->hasRole('Admin#' . request()->session()->get('business.id')) ? true : false;
        $business_id = request()->session()->get('business.id');
        if ($is_admin) {
            $all_family_patients = User::where('business_id', $business_id)->leftjoin('patient_details', 'users.id', 'patient_details.user_id')->select('patient_details.*', 'users.*')->groupBy('users.id')->get();
        } else {
            return  redirect()->action('PatientController@show', auth()->user()->id);
        }

        return view('patient.dashboard')->with(compact('all_family_patients'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //Check if subscribed or not, then check for location quota
        if (!$this->moduleUtil->isSubscribed(request()->session()->get('business.id'))) {
            return $this->moduleUtil->expiredResponse();
        }

        $blood_groups = ['AB', 'A-', 'B-', 'O-', 'AB+', 'A+', 'B+', 'O+', 'Not Known', ''];
        $marital_statuss = ['Married', 'UnMarried', ''];
        $genders = ['Male', 'Female', ''];
        $patient_details = PatientDetail::where('user_id', $id)->first();

        $blood_group = $blood_groups[!empty($patient_details->blood_group) ? $patient_details->blood_group - 1 : 9];
        $marital_status = $marital_statuss[!empty($patient_details->marital_status) ? $patient_details->marital_status-1 : 2];
        $gender = $genders[!empty($patient_details->gender) ? $patient_details->gender-1 : 2];

        $dateOfBirth =  date('Y-m-d', strtotime($patient_details->date_of_birth));
        $today = date("Y-m-d");
        $diff = date_diff(date_create($dateOfBirth), date_create($today));
        $age = $diff->format('%y');

        $patient_code = User::where('id', $id)->select('username')->first()->username;

        $business_id = request()->session()->get('business.id');
        $fy = $this->businessUtil->getCurrentFinancialYear($business_id);
        $date_filters['this_fy'] = $fy;
        $date_filters['this_month']['start'] = date('Y-m-01');
        $date_filters['this_month']['end'] = date('Y-m-t');
        $date_filters['this_week']['start'] = date('Y-m-d', strtotime('monday this week'));
        $date_filters['this_week']['end'] = date('Y-m-d', strtotime('sunday this week'));

        return view('patient.index')->with(compact('date_filters', 'patient_code', 'id', 'patient_details', 'blood_group', 'age', 'marital_status', 'gender'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $blood_groups = ['AB', 'A-', 'B-', 'O-', 'AB+', 'A+', 'B+', 'O+', 'Not Known', ''];
        $marital_statuss = ['Married', 'UnMarried', ''];
        $genders = ['Male', 'Female', ''];
        $patient_details = PatientDetail::where('user_id', $id)->first();

        $blood_group = $blood_groups[!empty($patient_details->blood_group) ? $patient_details->blood_group - 1 : 9];
        $marital_status = $marital_statuss[!empty($patient_details->marital_status) ? $patient_details->marital_status-1 : 2];
        $gender = $genders[!empty($patient_details->gender) ? $patient_details->gender-1 : 2];

        $dateOfBirth =  date('Y-m-d', strtotime($patient_details->date_of_birth));
        $today = date("Y-m-d");
        $diff = date_diff(date_create($dateOfBirth), date_create($today));
        $age = $diff->format('%y');

        return view('patient.edit')->with(compact('patient_details', 'blood_group', 'age', 'marital_status', 'gender', 'id'));
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

        try {

            $update_data = array(
                'address' => $request->address,
                'city' => $request->city,
                'state' => $request->state,
                'country' => $request->country,
                'mobile' => $request->mobile,
                'gender' => $request->gender,
                'marital_status' => $request->marital_status,
                'blood_group' => $request->blood_group,
                'height' => $request->height,
                'weight' => $request->weight,
                'guardian_name' => $request->guardian_name,
                'known_allergies' => $request->known_allergies,
                'notes' => $request->notes
            );
            if (!file_exists('./public/img/patient_photos')) {
                mkdir('./public/img/patient_photos', 0777, true);
            }
            if ($request->hasfile('fileToImage')) {
                $file = $request->file('fileToImage');
                $extension = $file->getClientOriginalExtension();
                $filename = time() . '.' . $extension;
                $file->move('public/img/patient_photos', $filename);
                $uploadFileFicon = 'public/img/patient_photos/' . $filename;
                $update_data['profile_image'] =  $uploadFileFicon;
            }

            $patient_details = PatientDetail::where('user_id', $id)->update($update_data);
            $output = [
                'success' => 1,
                'msg' => __("patient.details_update_success")
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

            $output = [
                'success' => 0,
                'msg' => __("messages.something_went_wrong")
            ];
        }


        return back()->with('status', $output);
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
     * Retrieves list of customers, if filter is passed then filter it accordingly.
     *
     * @param  string  $q
     * @return JSON
     */
    public function getPatient()
    {
        if (request()->ajax()) {
            $term = request()->input('q', '');
            $patient = Business::where('is_patient', 1)->leftjoin('users', 'business.id', 'users.business_id')
                    ->leftjoin('patient_details', 'users.id', 'patient_details.user_id');
      

            if (!empty($term)) {
                $patient->where(function ($query) use ($term) {
                    $query->Where('users.username', 'like', '%' . $term .'%')
                            ->orWhere('patient_details.mobile', 'like', '%' . $term .'%')
                            ->orWhere('patient_details.name', 'like', '%' . $term .'%');
                });
            }

            $patient->select(
                'users.id',
                'username',
                'patient_details.mobile',
                'patient_details.name'
            );

          
            $patient = $patient->get();
            return json_encode($patient);
        }
    }

}
