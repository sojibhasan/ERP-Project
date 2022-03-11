<?php

namespace App\Http\Controllers;

use App\Business;
use App\PatientDetail;
use App\PatientTest;
use App\User;
use App\PrescriptionTest;
use App\System;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;
use App\Utils\BusinessUtil;
use App\Utils\ModuleUtil;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\Facades\Image;

class TestController extends Controller
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
        if (request()->ajax()) {
            if(!empty(request()->patient_id)){
                $business_id = request()->patient_id;
            }else{
                $business_id = Auth::user()->id;
            }
            $test = PatientTest::where('patient_tests.business_id', $business_id)
                ->select(
                    'patient_tests.laboratory_name as laboratory_name',
                    'patient_tests.id',
                    'patient_tests.test_name',
                    'patient_tests.date',
                    'patient_tests.amount',
                    'patient_tests.description',
                    'patient_tests.test_file',
                    'patient_tests.bill_file',
                    'patient_tests.is_upload'
                )
                ->groupBy('patient_tests.id');

            return Datatables::of($test)
              
            ->addColumn('action', function ($row) {
               
                $html = '<div class="btn-group">
                <button type="button" class="btn btn-info dropdown-toggle btn-xs" 
                    data-toggle="dropdown" aria-expanded="false">' .
                    __("messages.actions") .
                    '<span class="caret"></span><span class="sr-only">Toggle Dropdown
                    </span>
                </button>
                <ul class="dropdown-menu dropdown-menu-right" role="menu">';
                if($row->is_upload){
                    $html .= '<li><a href="#" data-href="' . action('PrescriptionController@imageModal', ['title' => 'Test', 'url' => url($row->test_file)]) . '" class="btn-modal" data-container=".view_modal"><i class="fa fa-eye" aria-hidden="true"></i>' . __("patient.report_view") . '</a></li>';
                }else{
                    $html .= '<li><a href="#" data-href="' . action('PrescriptionController@show', [$row->id]) . '" class="btn-modal" data-container=".view_modal"><i class="fa fa-eye" aria-hidden="true"></i>' . __("messages.view") . '</a></li>';
                }
                if($row->bill_file){
                    $html .= '<li><a href="#" data-href="' . action('PrescriptionController@imageModal', ['title' => 'Test', 'url' => url($row->bill_file)]) . '" class="btn-modal" data-container=".view_modal"><i class="fa fa-sticky-note-o" aria-hidden="true"></i>' . __("patient.bill_view") . '</a></li>';
                }


                if (request()->session()->get('business.is_patient')) {
                    $html .= '<li><a href="#" data-href="' . action('TestController@enterAmount', [$row->id]) . '" class="btn-modal" data-container=".view_modal"><i class="fa fa-money" aria-hidden="true"></i>' . __("patient.enter_amount") . '</a></li>';
                }

                $html .=  '</ul></div>';
                return $html;
            })
            ->editColumn('amount', '{{@number_format($amount)}}')
            ->rawColumns(['action', 'laboratory_name'])
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
        $marital_status = $marital_statuss[!empty($patient_details->marital_status) ? $patient_details->marital_status : 2];
        $gender = $genders[!empty($patient_details->gender) ? $patient_details->gender-1 : 2];

        $dateOfBirth =  date('Y-m-d', strtotime($patient_details->date_of_birth));
        $today = date("Y-m-d");
        $diff = date_diff(date_create($dateOfBirth), date_create($today));
        $age = $diff->format('%y');

        $currencies = $this->businessUtil->allCurrencies();
        $timezone_list = $this->businessUtil->allTimeZones();
        $laboratorys = Business::where('is_laboratory', 1)->pluck('name', 'id');

        $months = [];
        for ($i = 1; $i <= 12; $i++) {
            $months[$i] = __('business.months.' . $i);
        }

        $is_admin = true;
        return view('laboratory.create')->with(compact(
            'patient_details',
            'blood_group',
            'age',
            'marital_status',
            'gender',
            'currencies',
            'timezone_list',
            'months',
            'is_admin',
            'patient_code',
            'laboratorys'
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
        $business_id = Auth::user()->id;
        try {
            $tests_data = array(
                'business_id' => $business_id,
                'test_name' => $request->test_name,
                'description' => $request->description
            );

            PatientTest::create($tests_data);
            $tests = PatientTest::where('business_id', $business_id)->select('test_name', 'id')->get();
            $output = [
                'success' => 1,
                'msg' => __('patient.test_add_success'),
                'tests' => $tests
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
     * upload the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function upload(Request $request)
    {
        $patient_code = request()->patient_code;
        if (!empty(request()->patient_code)) {
            $patinet = User::where('username', request()->patient_code)->first();
            $business_id = $patinet->id; //this is patient user id
        } else {
            $business_id = Auth::user()->id;
        }
        try {
            
        $image_width = (int) System::getProperty('upload_image_width');
        $image_hieght = (int) System::getProperty('upload_image_height');

        //upload prfile image
        if (!file_exists('./public/img/laboratory-tests/' . $patient_code)) {
            mkdir('./public/img/laboratory-tests/' . $patient_code, 0777, true);
        }
        if (!file_exists('./public/img/laboratory-bills/' . $patient_code)) {
            mkdir('./public/img/laboratory-bills/' . $patient_code, 0777, true);
        }
        if ($request->hasfile('test_file')) {

            $validate = Validator::make(
                $request->all(),
                [
                    'test_file' => 'mimes:jpeg,png,bmp,tiff|max:4096',
                ]
            );

            if ($validate->fails()) {
                $output = [
                    'success' => 0,
                    'msg' => 'Only jpeg, png, bmp,tiff are allowed.'
                ];

                return redirect()->back()->with('status', $output);
            }


            $file = $request->file('test_file');
            $extension = $file->getClientOriginalExtension();
            $filename = time() . '.' . $extension;
            Image::make($file->getRealPath())->resize($image_width, $image_hieght)->save('public/img/laboratory-tests/' . $patient_code . '/' . $filename);
            $uploadFileFicon = 'public/img/laboratory-tests/' . $patient_code . '/' . $filename;
            $test_file = $uploadFileFicon;
        } else {
            $test_file = '';
        }
        if ($request->hasfile('bill_file')) {

            $validate = Validator::make(
                $request->all(),
                [
                    'bill_file' => 'mimes:jpeg,png,bmp,tiff|max:4096',
                ]
            );

            if ($validate->fails()) {
                $output = [
                    'success' => 0,
                    'msg' => 'Only jpeg, png, bmp,tiff are allowed.'
                ];

                return redirect()->back()->with('status', $output);
            }


            $file = $request->file('bill_file');
            $extension = $file->getClientOriginalExtension();
            $filename = time() . '.' . $extension;
            Image::make($file->getRealPath())->resize($image_width, $image_hieght)->save('public/img/laboratory-bills/' . $patient_code . '/' . $filename);
            $uploadFileFicon = 'public/img/laboratory-bills/' . $patient_code . '/' . $filename;
            $bill_file = $uploadFileFicon;
        } else {
            $bill_file = '';
        }
        $date = date('Y-m-d', strtotime($request->date));
        foreach ($request->test as $test) {
            $tests_data = array(
                'business_id' => $business_id,
                'laboratory_name' => $request->laboratory_name,
                'date' => $date,
                'test_name' => $test['test_name'],
                'amount' => $test['amount'],
                'test_file' => $test_file,
                'bill_file' => $bill_file,
                'is_upload' => 1,
            );

            PatientTest::create($tests_data);
        }


        $output = [
            'success' => 1,
            'msg' => __('patient.test_upload_success'),
        ];

        return redirect()->back()->with('status', $output);
        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

            $output = [
                'success' => 0,
                'msg' => __('messages.something_went_wrong')
            ];

            return redirect()->back()->with('status', $output);
        }
    }


    
     /**
     * show amount modal.
     *
     * @param  int  $id
     */
    public function enterAmount($id)
    {
        $amount = PatientTest::where('id', $id)->first()->amount;
        $action = action("TestController@updateAmount");
        $table = 'test_table';
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
            PatientTest::where('id', $request->id)->update(['amount' => $request->amount]);

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

}
