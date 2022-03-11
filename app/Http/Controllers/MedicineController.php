<?php

namespace App\Http\Controllers;

use App\PatientMedicine;
use App\PrescriptionMedicine;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;
use App\PatientDetail;
use App\User;
use App\Business;
use App\System;
use App\Utils\BusinessUtil;
use App\Utils\ModuleUtil;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\Facades\Image;

class MedicineController extends Controller
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
            if (!empty(request()->patient_id)) {
                $business_id = request()->patient_id;
            } else {
                $business_id = Auth::user()->id;
            }
            $medicines = PatientMedicine::where('patient_medicines.business_id', $business_id)
                ->select(
                    'patient_medicines.pharmacy_name as pharmacy_name',
                    'patient_medicines.medicine_name',
                    'patient_medicines.qty',
                    'patient_medicines.amount',
                    'patient_medicines.date',
                    'patient_medicines.is_upload',
                    'patient_medicines.pharmacy_file',
                    'patient_medicines.notes',
                    'patient_medicines.description'
                )
                ->groupBy('patient_medicines.id');

            return Datatables::of($medicines)

                ->addColumn('action', function ($row) {
                    $html = '<div class="btn-group">
                <button type="button" class="btn btn-info dropdown-toggle btn-xs" 
                    data-toggle="dropdown" aria-expanded="false">' .
                        __("messages.actions") .
                        '<span class="caret"></span><span class="sr-only">Toggle Dropdown
                    </span>
                </button>
                <ul class="dropdown-menu dropdown-menu-right" role="menu">';
                    if ($row->is_upload) {
                        $html .= '<li><a href="#" data-href="' . action('PrescriptionController@imageModal', ['title' => 'Pharmacy', 'url' => url($row->pharmacy_file)]) . '" class="btn-modal" data-container=".view_modal"><i class="fa fa-eye" aria-hidden="true"></i>' . __("messages.view") . '</a></li>';
                    } else {
                        $html .= '<li><a href="#" data-href="' . action('MedicineController@show', [$row->id]) . '" class="btn-modal" data-container=".view_modal"><i class="fa fa-eye" aria-hidden="true"></i>' . __("messages.view") . '</a></li>';
                    }

                    $html .=  '</ul></div>';
                    return $html;
                })
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
        $marital_status = $marital_statuss[!empty($patient_details->marital_status) ? $patient_details->marital_status : 2];
        $gender = $genders[!empty($patient_details->gender) ? $patient_details->gender - 1 : 2];

        $dateOfBirth =  date('Y-m-d', strtotime($patient_details->date_of_birth));
        $today = date("Y-m-d");
        $diff = date_diff(date_create($dateOfBirth), date_create($today));
        $age = $diff->format('%y');

        $currencies = $this->businessUtil->allCurrencies();
        $timezone_list = $this->businessUtil->allTimeZones();
        $pharmacys = Business::where('is_pharmacy', 1)->pluck('name', 'id');

        $months = [];
        for ($i = 1; $i <= 12; $i++) {
            $months[$i] = __('business.months.' . $i);
        }

        $is_admin = true;
        return view('pharmacy.create')->with(compact(
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
            'pharmacys'
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
            $medicines_data = array(
                'business_id' => $business_id,
                'medicine_name' => $request->medicine_name,
                'description' => $request->description
            );

            PatientMedicine::create($medicines_data);
            $medicines = PatientMedicine::where('business_id', $business_id)->select('medicine_name', 'id')->get();
            $output = [
                'success' => 1,
                'msg' => __('patient.allergy_add_success'),
                'medicines' => $medicines
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
            if (!file_exists('./public/img/pharmacy/' . $patient_code)) {
                mkdir('./public/img/pharmacy/' . $patient_code, 0777, true);
            }
            if ($request->hasfile('pharmacy_file')) {

                $validate = Validator::make(
                    $request->all(),
                    [
                        'pharmacy_file' => 'mimes:jpeg,png,bmp,tiff|max:4096',
                    ]
                );

                if ($validate->fails()) {
                    $output = [
                        'success' => 0,
                        'msg' => 'Only jpeg, png, bmp,tiff are allowed.'
                    ];

                    return redirect()->back()->with('status', $output);
                }


                $file = $request->file('pharmacy_file');
                $extension = $file->getClientOriginalExtension();
                $filename = time() . '.' . $extension;
                Image::make($file->getRealPath())->resize($image_width, $image_hieght)->save('public/img/pharmacy/' . $patient_code . '/' . $filename);
                $uploadFileFicon = 'public/img/pharmacy/' . $patient_code . '/' . $filename;
                $pharmacy_file = $uploadFileFicon;
            } else {
                $pharmacy_file = '';
            }
            $date = date('Y-m-d', strtotime($request->date));
            foreach ($request->medicine as $med) {
                $medicines_data = array(
                    'business_id' => $business_id,
                    'pharmacy_name' => $request->pharmacy_name,
                    'date' => $date,
                    'medicine_name' => $med['medicine_name'],
                    'qty' => $med['qty'],
                    'amount' => $med['amount'],
                    'pharmacy_file' => $pharmacy_file,
                    'is_upload' => 1,
                );

                PatientMedicine::create($medicines_data);
            }


            $output = [
                'success' => 1,
                'msg' => __('patient.pharmacy_upload_success'),
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
}
