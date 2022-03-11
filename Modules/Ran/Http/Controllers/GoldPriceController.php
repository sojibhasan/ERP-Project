<?php

namespace Modules\Ran\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Modules\Ran\Entities\GoldGrade;
use Modules\Ran\Entities\GoldPrice;
use Yajra\DataTables\Facades\DataTables;

class GoldPriceController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        $business_id = request()->session()->get('business.id');
      
        $gold_grades = GoldGrade::where('business_id', $business_id)->get();
        $gold_prices = GoldPrice::where('gold_prices.business_id', $business_id)->leftjoin('users', 'gold_prices.created_by', 'users.id')->select('gold_prices.*', 'users.username')->orderBy('gold_prices.id', 'desc')->get();

        return view('ran::gold_prices.index')->with(compact(
            'gold_grades',
            'gold_prices'
        ));
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create()
    {
        $business_id = request()->session()->get('business.id');
        $last_grade = GoldGrade::where('business_id', $business_id)->where('grade_name', '24')->orderBy('id', 'desc')->first();
        
        return view('ran::gold_prices.create')->with(compact('last_grade'));
    }

    /**
     * Store a newly created resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'grade_id' => 'required',
            'price' => 'required',
        ]);

        if($validator->fails()){
            $output = [
                'success' => 0,
                'msg' => $validator->errors()->all()[0]
            ];
            return redirect()->back()->with('status', $output);
        }
        $business_id = request()->session()->get('business.id');
        try {
            $data = array(
                'business_id' => $business_id,
                'date_and_time' => !empty($request->date_and_time) ? Carbon::parse($request->date_and_time)->format('Y-m-d H:i:s') : Carbon::now(),
                'grade_id' => $request->grade_id,
                'purity' => $request->purity,
                'price' => $request->price,
                'created_by' => Auth::user()->id,
            );

            GoldPrice::create($data);
            $output = [
                'success' => true,
                'msg' => __('ran::lang.gold_price_create_success')
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
        return view('ran::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @return Response
     */
    public function edit($id)
    {
        $gold_price = GoldPrice::findOrFail($id);

        return view('ran::gold_prices.edit')->with(compact('gold_price'));
    }

    /**
     * Update the specified resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function update($id, Request $request)
    {
        try {
            $data = array(
                'grade_name' => $request->grade_name,
                'gold_purity' => $request->gold_purity
            );

            GoldPrice::where('id', $id)->update($data);
            $output = [
                'success' => true,
                'msg' => __('ran::lang.gold_price_update_success')
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
}
