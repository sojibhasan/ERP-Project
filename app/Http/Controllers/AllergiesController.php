<?php

namespace App\Http\Controllers;

use App\PatientAllergie;
use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Carbon;

class AllergiesController extends Controller
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
            $allergies = PatientAllergie::
                where('business_id', $business_id)
                ->select(
                    'patient_allergies.*'
                )
                ->groupBy('patient_allergies.id');

            return Datatables::of($allergies)
            
                ->addColumn('notes', function ($row) {
                    $html =  '';
                    return $html;
                })
                ->rawColumns([])
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
        return view('patient.partials.add_allergies_and_notes')->with(compact('patient_code'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if(!empty(request()->patient_code)){
            $patinet = User::where('username', request()->patient_code)->first();
            $business_id = $patinet->id;
        }else{
            $business_id = Auth::user()->id;
        }
        try {
            $allergies_data = array(
                'business_id' => $business_id,
                'date' => date('Y-m-d', strtotime($request->date)),
                'allergy_name' => $request->allergy_name,
                'description' => null
            );

            PatientAllergie::create($allergies_data);
            $allergies = PatientAllergie::where('business_id', $business_id)->select('allergy_name', 'id')->get();
            $output = [
                'success' => 1,
                'msg' => __('patient.allergy_add_success'),
                'allergies' => $allergies
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
}
