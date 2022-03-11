<?php

namespace App\Http\Controllers;

use App\PatientPrescription;
use App\Business;
use App\User;
use App\PatientAllergie;
use Illuminate\Http\Request;
use App\Utils\BusinessUtil;
use App\Utils\ModuleUtil;
use Illuminate\Support\Facades\Auth;
use App\PatientDetail;
use App\PatientDoctor;
use App\PatientMedicine;
use App\PatientTest;
use App\PrescriptionMedicine;
use App\PrescriptionTest;
use App\System;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;
use Intervention\Image\Facades\Image;

class PrescriptionController extends Controller
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
     * business id is user id 
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        if (request()->ajax()) {
            if (!empty(request()->patient_id)) {
                $business_id = request()->patient_id;
            } else {
                $business_id = Auth::user()->id;
            }
            //Check if subscribed or not, then check for location quota
            if (!$this->moduleUtil->isSubscribed(request()->session()->get('business.id'))) {
                return $this->moduleUtil->expiredResponse();
            }
            $prescription = PatientPrescription::leftJoin('prescription_tests', 'patient_prescriptions.id', '=', 'prescription_tests.prescription_id')
                ->leftJoin('patient_tests', 'prescription_tests.test_id', '=', 'patient_tests.id')
                ->leftJoin('prescription_medicines', 'patient_prescriptions.id', '=', 'prescription_medicines.prescription_id')
                ->leftJoin(
                    'patient_medicines',
                    'prescription_medicines.medicine_id',
                    'patient_medicines.id'
                )
                ->leftJoin(
                    'patient_allergies',
                    'patient_prescriptions.allergies_id',
                    'patient_allergies.id'
                )
                ->leftJoin(
                    'patient_doctors',
                    'patient_prescriptions.doctor_id',
                    'patient_doctors.id'
                )
            
                ->where('patient_prescriptions.business_id', $business_id)
                ->select(
                    'patient_prescriptions.*',
                    'patient_tests.test_name as test',
                    'patient_medicines.medicine_name as medicine',
                    'patient_allergies.allergy_name as allergy',
                    'patient_doctors.doctor_name as doctor',
                    'patient_prescriptions.hospital_name as hospital',
                    'patient_prescriptions.created_at as date_created'
                )
                ->groupBy('patient_prescriptions.id');

            return Datatables::of($prescription)
                ->addColumn('action', function ($row) {
                    if (!empty(request()->patient_id)) {
                        $business_id = request()->patient_id;
                    } else {
                        // $business_id = request()->session()->get('user.business_id');
                        $business_id = Auth::user()->id;
                    }
                    $patinet_code = User::where('id', $business_id)->first()->username;

                    $now = Carbon::now();
                    $created_at = Carbon::parse($row->date_created);
                    $diffHuman = $created_at->diffInHours($now);


                    $html = '<div class="btn-group">
                    <button type="button" class="btn btn-info dropdown-toggle btn-xs" 
                        data-toggle="dropdown" aria-expanded="false">' .
                        __("messages.actions") .
                        '<span class="caret"></span><span class="sr-only">Toggle Dropdown
                        </span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-right" role="menu">';
                    if ($row->is_upload) {
                        $html .= '<li><a href="#" data-href="' . action('PrescriptionController@imageModal', ['title' => 'Prescription', 'url' => url($row->prescription_file)]) . '" class="btn-modal" data-container=".view_modal"><i class="fa fa-eye" aria-hidden="true"></i>' . __("messages.view") . '</a></li>';
                    } else {
                        $html .= '<li><a href="#" data-href="' . action('PrescriptionController@show', [$row->id]) . '" class="btn-modal" data-container=".view_modal"><i class="fa fa-eye" aria-hidden="true"></i>' . __("messages.view") . '</a></li>';
                    }

                    if (session()->get('business.is_pharmacy') || session()->get('business.is_hospital')) {
                        if ($diffHuman > 8) {
                            $html .= '<li><a><i class="fa fa-pencil-square-o" aria-hidden="true"></i>' . __("messages.edit") . '</a></li>';
                        } 
                        else {
                            $html .= '<li><a href="' . action('PrescriptionController@edit', [$row->id, 'patient_code' => $patinet_code]) . '"><i class="fa fa-pencil-square-o" aria-hidden="true"></i>' . __("messages.edit") . '</a></li>';
                        }
                    }
                    if (request()->session()->get('business.is_patient')) {
                        $html .= '<li><a href="#" data-href="' . action('PrescriptionController@enterAmount', [$row->id]) . '" class="btn-modal" data-container=".view_modal"><i class="fa fa-money" aria-hidden="true"></i>' . __("patient.enter_amount") . '</a></li>';
                    }

                    $html .=  '</ul></div>';
                    return $html;
                })
                ->editColumn('amount', '{{@number_format($amount)}}')
                ->rawColumns(['action'])
                ->make(true);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

        if (!empty(request()->patient_code)) {
            $patient_code = request()->patient_code;
            $patinet = User::where('username', request()->patient_code)->first();
            $patient_id = $patinet->id;
            $business_id = $patinet->id;
        } else {
            $business_id = Auth::user()->id;
            $patient_id =  Auth::user()->id;
            $patient_code =  Auth::user()->username;
        }

        //Check if subscribed or not, 
        if (!$this->moduleUtil->isSubscribed(request()->session()->get('business.id'))) {
            return $this->moduleUtil->expiredResponse();
        }
        $blood_groups = ['AB', 'A-', 'B-', 'O-', 'AB+', 'A+', 'B+', 'O+', 'Not Known', ''];
        $marital_statuss = ['Married', 'UnMarried', ''];
        $genders = ['Male', 'Female', ''];
        $patient_details = PatientDetail::where('user_id', $patient_id)->first();
        $blood_group = $blood_groups[!empty($patient_details->blood_group) ? $patient_details->blood_group - 1 : 9];
        $marital_status = $marital_statuss[!empty($patient_details->marital_status) ? $patient_details->marital_status-1 : 2];
        $gender = $genders[!empty($patient_details->gender) ? $patient_details->gender-1 : 2];

        $dateOfBirth =  date('Y-m-d', strtotime($patient_details->date_of_birth));
        $today = date("Y-m-d");
        $diff = date_diff(date_create($dateOfBirth), date_create($today));
        $age = $diff->format('%y');

        $currencies = $this->businessUtil->allCurrencies();
        $timezone_list = $this->businessUtil->allTimeZones();

        $accounting_methods = $this->businessUtil->allAccountingMethods();

        $hospitals = Business::where('is_hospital', 1)->pluck('name', 'id');
        $doctors = PatientDoctor::pluck('doctor_name', 'id');

        $allergies = PatientAllergie::where('business_id', $business_id)->pluck('allergy_name', 'id');
        $medicines = PatientMedicine::where('business_id', $business_id)->pluck('medicine_name', 'id');
        $tests = PatientTest::where('business_id', $business_id)->pluck('test_name', 'id');

        $months = [];
        for ($i = 1; $i <= 12; $i++) {
            $months[$i] = __('business.months.' . $i);
        }

        $is_admin = true;
        return view('patient.partials.add_prescription')->with(compact(
            'patient_details',
            'blood_group',
            'age',
            'marital_status',
            'gender',
            'currencies',
            'timezone_list',
            'accounting_methods',
            'months',
            'is_admin',
            'hospitals',
            'allergies',
            'patient_code',
            'doctors'
        ));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        try {
            if (!empty(request()->patient_code)) {
                $patient_code = request()->patient_code;
                $patinet = User::where('username', request()->patient_code)->first();
                $patient_id = $patinet->id;
                $business_id = $patinet->id;
            } else {
                $business_id = Auth::user()->id;
                $patient_id =  Auth::user()->id;
                $patient_code =  Auth::user()->username;
            }
            $today = Carbon::now()->format("Y-m-d");
            $prescription_data = array(
                'business_id' => $business_id,
                'hospital_name' => $request->hospital_name,
                'doctor_id' => $request->doctor_id,
                'symptoms' => $request->symptoms,
                'diagnosis' => $request->diagnosis,
                'allergies_id' => $request->allergies_id,
                'date' => !empty($request->date) ? date('Y-m-d', strtotime($request->date)) : $today
            );

            //Check if subscribed or not, then check for location quota
            if (!$this->moduleUtil->isSubscribed(request()->session()->get('business.id'))) {
                return $this->moduleUtil->expiredResponse();
            }
            $prescription = PatientPrescription::create($prescription_data);

            foreach ($request->medicine as $med) {
                $medicine = PatientMedicine::create(['business_id' => $business_id, 'medicine_name' => $med['medicine_name']]);
                $medicine_data = array(
                    'prescription_id' => $prescription->id,
                    'medicine_id' => $medicine->id,
                    'notes' => $med['notes'],
                );
                PrescriptionMedicine::create($medicine_data);
            }

            foreach ($request->test as $te) {
                $test = PatientTest::create(['business_id' => $business_id, 'test_name' => $te['test_name']]);
                $test_data = array(
                    'prescription_id' => $prescription->id,
                    'test_id' => $test->id,
                    'notes' => $te['notes'],
                );

                PrescriptionTest::create($test_data);
            }

            $output = [
                'success' => 1,
                'msg' => __('patient.prescription_add_success')
            ];
        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

            $output = [
                'success' => 0,
                'msg' => __('messages.something_went_wrong')
            ];
        }
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
        $prescription = PatientPrescription::leftJoin('prescription_tests', 'patient_prescriptions.id', '=', 'prescription_tests.prescription_id')
            ->leftJoin('patient_tests', 'prescription_tests.test_id', '=', 'patient_tests.id')
            ->leftJoin('prescription_medicines', 'patient_prescriptions.id', '=', 'prescription_medicines.prescription_id')
            ->leftJoin(
                'patient_medicines',
                'prescription_medicines.medicine_id',
                'patient_medicines.id'
            )
            ->leftJoin(
                'patient_allergies',
                'patient_prescriptions.allergies_id',
                'patient_allergies.id'
            )
            ->leftJoin(
                'patient_doctors',
                'patient_prescriptions.doctor_id',
                'patient_doctors.id'
            )
            ->leftJoin(
                'business',
                'patient_prescriptions.hospital_id',
                'business.id'
            )
            ->where('patient_prescriptions.id', $id)
            ->select(
                'patient_prescriptions.*',
                'patient_tests.test_name as test',
                'patient_medicines.medicine_name as medicine',
                'patient_allergies.allergy_name as allergy',
                'patient_doctors.doctor_name as doctor',
                'patient_doctors.signatures as signature',
                'business.name as hospital',
                'patient_prescriptions.created_at as date_created'
            )
            ->first();
        $patient = User::where('id', $prescription->business_id)->first();
        $patient_code = $patient->username;
        $medicines = PrescriptionMedicine::leftJoin('patient_prescriptions', 'prescription_medicines.prescription_id', '=', 'patient_prescriptions.id')
            ->leftJoin('patient_medicines', 'prescription_medicines.medicine_id', '=', 'patient_medicines.id')
            ->where('prescription_medicines.prescription_id', $prescription->id)
            ->select(
                'prescription_medicines.*',
                'patient_medicines.medicine_name'
            )->get();

        $tests = PrescriptionTest::leftJoin('patient_prescriptions', 'prescription_tests.prescription_id', '=', 'patient_prescriptions.id')
            ->leftJoin('patient_tests', 'prescription_tests.test_id', '=', 'patient_tests.id')
            ->where('prescription_tests.prescription_id', $prescription->id)
            ->select(
                'prescription_tests.*',
                'patient_tests.test_name'
            )->get();

        return view('patient.partials.prescription_show')->with(compact('prescription', 'patient_code', 'medicines', 'tests'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (!empty(request()->patient_code)) {
            $patient_code = request()->patient_code;
            $patinet = User::where('username', request()->patient_code)->first();
            $patient_id = $patinet->id;
            $business_id = $patinet->id;
        } else {
            $business_id = Auth::user()->id;
            $patient_id =  Auth::user()->id;
            $patient_code =  Auth::user()->username;
        }

        //Check if subscribed or not, then check for location quota
        if (!$this->moduleUtil->isSubscribed(request()->session()->get('business.id'))) {
            return $this->moduleUtil->expiredResponse();
        }
        $blood_groups = ['AB', 'A-', 'B-', 'O-', 'AB+', 'A+', 'B+', 'O+', ''];
        $marital_statuss = ['Married', 'UnMarried', ''];
        $genders = ['Male', 'Female', ''];
        $patient_details = PatientDetail::where('user_id', $patient_id)->first();
        $blood_group = $blood_groups[!empty($patient_details->blood_group) ? $patient_details->blood_group - 1 : 8];
        $marital_status = $marital_statuss[!empty($patient_details->marital_status) ? $patient_details->marital_status-1 : 2];
        $gender = $genders[!empty($patient_details->gender) ? $patient_details->gender-1 : 2];

        $dateOfBirth =  date('Y-m-d', strtotime($patient_details->date_of_birth));
        $today = date("Y-m-d");
        $diff = date_diff(date_create($dateOfBirth), date_create($today));
        $age = $diff->format('%y');

        $currencies = $this->businessUtil->allCurrencies();
        $timezone_list = $this->businessUtil->allTimeZones();

        $accounting_methods = $this->businessUtil->allAccountingMethods();

        $hospitals = Business::where('is_hospital', 1)->pluck('name', 'id');

        $allergies = PatientAllergie::where('business_id', $business_id)->pluck('allergy_name', 'id');

        $months = [];
        for ($i = 1; $i <= 12; $i++) {
            $months[$i] = __('business.months.' . $i);
        }

        $prescription = PatientPrescription::where('id', $id)->first();
        $medicines = PrescriptionMedicine::leftJoin('patient_prescriptions', 'prescription_medicines.prescription_id', '=', 'patient_prescriptions.id')
            ->leftJoin('patient_medicines', 'prescription_medicines.medicine_id', '=', 'patient_medicines.id')
            ->where('prescription_medicines.prescription_id', $prescription->id)
            ->select(
                'prescription_medicines.*',
                'patient_medicines.medicine_name'
            )->get();

        $tests = PrescriptionTest::leftJoin('patient_prescriptions', 'prescription_tests.prescription_id', '=', 'patient_prescriptions.id')
            ->leftJoin('patient_tests', 'prescription_tests.test_id', '=', 'patient_tests.id')
            ->where('prescription_tests.prescription_id', $prescription->id)
            ->select(
                'prescription_tests.*',
                'patient_tests.test_name'
            )->get();
        $doctors = PatientDoctor::pluck('doctor_name', 'id');
        $is_admin = true;
        return view('patient.partials.add_prescription')->with(compact(
            'prescription',
            'patient_details',
            'blood_group',
            'age',
            'marital_status',
            'gender',
            'currencies',
            'timezone_list',
            'accounting_methods',
            'months',
            'is_admin',
            'hospitals',
            'allergies',
            'medicines',
            'tests',
            'patient_code',
            'doctors'
        ));
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
            if (!empty(request()->patient_code)) {
                $patient_code = request()->patient_code;
                $patinet = User::where('username', request()->patient_code)->first();
                $patient_id = $patinet->id;
                $business_id = $patinet->id;
            } else {
                $business_id = Auth::user()->id;
                $patient_id =  Auth::user()->id;
                $patient_code =  Auth::user()->username;
            }
            //Check if subscribed or not, then check for location quota
            if (!$this->moduleUtil->isSubscribed(request()->session()->get('business.id'))) {
                return $this->moduleUtil->expiredResponse();
            }
            $today = Carbon::now()->format("Y-m-d");
            $prescription_data = array(
                'hospital_name' => $request->hospital_name,
                'doctor_id' => $request->doctor_id,
                'symptoms' => $request->symptoms,
                'diagnosis' => $request->diagnosis,
                'allergies_id' => $request->allergies_id,
                'date' => !empty($request->date) ? date('Y-m-d', strtotime($request->date)) : $today
            );

            $prescription = PatientPrescription::where('id', $id)->update($prescription_data);

            foreach ($request->medicine as $med) {
                $medicine = PatientMedicine::updateOrCreate(['business_id' => $business_id, 'medicine_name' => $med['medicine_name']], ['business_id' => $business_id, 'medicine_name' => $med['medicine_name']]);
                $medicine_data = array(
                    'prescription_id' => $id,
                    'medicine_id' => $medicine->id,
                    'notes' => $med['notes'],
                );
                PrescriptionMedicine::updateOrCreate(['id' => $med['medicine_id']], $medicine_data);
            }

            foreach ($request->test as $te) {
                $test = PatientTest::updateOrCreate(['business_id' => $business_id, 'test_name' => $te['test_name']], ['business_id' => $business_id, 'test_name' => $te['test_name']]);
                $test_data = array(
                    'prescription_id' => $id,
                    'test_id' => $test->id,
                    'notes' => $te['notes'],
                );

                PrescriptionTest::updateOrCreate(['id' => $te['test_id']], $test_data);
            }

            $output = [
                'success' => 1,
                'msg' => __('patient.prescription_update_success')
            ];
        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

            $output = [
                'success' => 0,
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
        //
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function upload(Request $request)
    {
        try {
            if (!empty(request()->patient_code)) {
                $patient_code = request()->patient_code;
                $patinet = User::where('username', request()->patient_code)->first();
                $patient_id = $patinet->id;
                $business_id = $patinet->id;
            } else {
                $business_id = Auth::user()->id;
            }

            $today = Carbon::now()->format("Y-m-d");
            $prescription_data = array(
                'business_id' => $business_id,
                'hospital_name' => !empty($request->hospital_name) ? $request->hospital_name : null,
                'doctor_id' => !empty($request->doctor_id) ? $request->doctor_id : 0,
                'date' => !empty($request->date) ? date('Y-m-d', strtotime($request->date)) : $today,
                'symptoms' => '',
                'diagnosis' => '',
                'amount' => !empty($request->amount) ? $request->amount : null,
                'allergies_id' => 0,
                'is_upload' => 1
            );

            //Check if subscribed or not, then check for location quota
            if (!$this->moduleUtil->isSubscribed(request()->session()->get('business.id'))) {
                return $this->moduleUtil->expiredResponse();
            }

            //upload prfile image
            if (!file_exists('./public/img/prescription/' . $patient_code)) {
                mkdir('./public/img/prescription/' . $patient_code, 0777, true);
            }
            if ($request->hasfile('prescription_file')) {

                $validate = Validator::make(
                    $request->all(),
                    [
                        'prescription_file' => 'mimes:jpeg,png,bmp|max:4096',
                    ]
                );

                if ($validate->fails()) {
                    $output = [
                        'success' => 0,
                        'msg' => 'Only jpeg, png, bmp are allowed.'
                    ];

                    return redirect()->back()->with('status', $output);
                }

                $image_width = (int) System::getProperty('upload_image_width');
                $image_hieght = (int) System::getProperty('upload_image_height');

                $file = $request->file('prescription_file');
                $extension = $file->getClientOriginalExtension();
                $filename = time() . '.' . $extension;
                Image::make($file->getRealPath())->resize($image_width, $image_hieght)->save('public/img/prescription/' . $patient_code . '/' . $filename);
                $uploadFileFicon = 'public/img/prescription/' . $patient_code . '/' . $filename;
                $prescription_data['prescription_file'] = $uploadFileFicon;
            } else {
                $prescription_data['prescription_file'] = '';
            }
            $prescription = PatientPrescription::create($prescription_data);



            $output = [
                'success' => 1,
                'msg' => __('patient.prescription_add_success')
            ];
        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

            $output = [
                'success' => 0,
                'msg' => __('messages.something_went_wrong')
            ];
        }
        return redirect()->back()->with('status', $output);
    }

    /**
     * show image modal.
     *
     * @return \Illuminate\Http\Response
     */
    public function imageModal(Request $request)
    {
        $url = $request->url;
        $title = $request->title;

        return view('patient.partials.image_modal')->with(compact('title', 'url'));
    }

    /**
     * show amount modal.
     *
     * @param  int  $id
     */
    public function enterAmount($id)
    {
        $amount = PatientPrescription::where('id', $id)->first()->amount;
        $action = action("PrescriptionController@updateAmount");
        $table = 'prescription_table';
        return view('patient.partials.enter_amount_modal')->with(compact('id', 'amount', 'action', 'table'));
    }

    /**
     * update amount for resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function updateAmount(Request $request)
    {
        try {
            PatientPrescription::where('id', $request->id)->update(['amount' => $request->amount]);

            $output = [
                'success' => 1,
                'msg' => __('patient.amount_update_success'),
            ];

            return $output;
        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

            $output = [
                'success' => 0,
                'msg' => __('messages.something_went_wrong')
            ];

            return $output;
        }
    }

    /**
     * update amount for resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getPrescriptions($patient_id)
    {
        $prescriptions = PatientPrescription::where('business_id', $patient_id)->get();

        return view('patient.partials.prescriptions_modal')->with(compact('prescriptions'));
    }
}
