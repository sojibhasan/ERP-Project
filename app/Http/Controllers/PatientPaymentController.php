<?php

namespace App\Http\Controllers;

use App\PatientMedicine;
use App\PatientPayment;
use App\PatientPrescription;
use App\PatientTest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;
use Yajra\DataTables\Facades\DataTables;

class PatientPaymentController extends Controller
{
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
            $tests = PatientTest::where('patient_tests.business_id', $business_id)
                ->select(
                    'patient_tests.date',
                    'patient_tests.amount',
                    'patient_tests.bill_file as invoice',
                    'patient_tests.laboratory_name as institution_name'
                )
                ->groupBy('patient_tests.id')->get();
            $medicines = PatientMedicine::where('patient_medicines.business_id', $business_id)
                ->select(
                    'patient_medicines.date',
                    'patient_medicines.amount',
                    'patient_medicines.pharmacy_file as invoice',
                    'patient_medicines.pharmacy_name as institution_name'
                )
                ->groupBy('patient_medicines.id')->get();
            $prescriptions = PatientPrescription::where('patient_prescriptions.business_id', $business_id)
                ->select(
                    'patient_prescriptions.date',
                    'patient_prescriptions.amount',
                    'patient_prescriptions.bill_file_dummy as invoice',
                    'patient_prescriptions.hospital_name as institution_name'
                )
                ->groupBy('patient_prescriptions.id')->get();
          
            $payments = collect($tests)->merge($medicines)->merge($prescriptions);
            if(!empty(request()->filter_type)){
                if(request()->filter_type == 1){
                    $payments = $prescriptions;
                }
                if(request()->filter_type == 2){
                    $payments = $medicines;
                }
                if(request()->filter_type == 3){
                    $payments = $tests;
                }
            }
            if(!empty(request()->start_date) && !empty(request()->end_date)){
                $start_date = request()->start_date;
                $end_date = request()->end_date;
                $payments = $payments->whereBetween('date', [$start_date, $end_date]);
            }
            return Datatables::of($payments)
              
            ->addColumn('action', function ($row) {
                if (!empty(request()->patient_id)) {
                    $business_id = request()->patient_id;
                } else {
                    $business_id = Auth::user()->id;
                }
             

                $html = '<div class="btn-group">
                <button type="button" class="btn btn-info dropdown-toggle btn-xs" 
                    data-toggle="dropdown" aria-expanded="false">' .
                    __("messages.actions") .
                    '<span class="caret"></span><span class="sr-only">Toggle Dropdown
                    </span>
                </button>
                <ul class="dropdown-menu dropdown-menu-right" role="menu">';
                if (!empty($row->invoice)) {
                    $html .= '<li><a href="#" data-href="' . action('PrescriptionController@imageModal', ['title' => 'Test', 'url' => url($row->invoice)]) . '" class="btn-modal" data-container=".view_modal"><i class="fa fa-eye" aria-hidden="true"></i>' . __("messages.view") . '</a></li>';
                } 
                else {
                    $html .= '<li><a><i class="fa fa-eye" aria-hidden="true"></i>' . __("messages.view") . '</a></li>';
                }
                $html .=  '</ul></div>';
                return $html;
            })
            ->editColumn('amount', '<span data-orig-value="{{$amount}}" class="amount">{{@number_format($amount)}}</span>')
          
            ->rawColumns(['action', 'institution_name', 'amount'])
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
