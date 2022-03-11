<?php

namespace Modules\Ran\Http\Controllers;

use App\BusinessLocation;
use App\Store;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Modules\Ran\Entities\GoldGrade;
use Modules\Ran\Entities\GoldProduction;
use Modules\Ran\Entities\GoldSmith;
use Yajra\DataTables\Facades\DataTables;

class ProductionController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index()
    {
        $business_id = request()->session()->get('business.id');
        if (request()->ajax()) {
            $gold_productions = GoldProduction::where('gold_productions.business_id', $business_id)
                ->leftjoin('business_locations', 'gold_productions.location_id', 'business_locations.id')
                ->leftjoin('gold_smiths', 'gold_productions.gold_smith_id', 'gold_smiths.id')
                ->select([
                    'gold_productions.*',
                    'business_locations.name as business_location',
                    'gold_smiths.name as goldsmith'
                ]);
            return DataTables::of($gold_productions)
                ->editColumn('other_cost', function($row){
                     $other_costs = ($row->other_cost);
                    $cost = 0;
                    if(!empty($other_costs)){
                        foreach($other_costs as $other_cost){
                            $cost += $other_cost['cost'];
                        }
                    }
                    return $cost;
                })
                ->removeColumn('id')
                ->rawColumns(['other_cost'])
                ->make(true);
        }

        return view('ran::production.index');
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        $business_id = request()->session()->get('business.id');
        $business_locations = BusinessLocation::where('business_id', $business_id)->pluck('name', 'id');
        $reference_code = uniqid();
        $gold_smiths =  GoldSmith::where('business_id', $business_id)->pluck('name', 'id');
        $category_types = ['service' => __('ran::lang.service'), 'non_inventory' => __('ran::lang.non_inventory')];
        $receiving_stores = Store::where('business_id', $business_id)->pluck('name', 'id');
        $gold_grades = GoldGrade::where('business_id', $business_id)->pluck('grade_name', 'id');

        return view('ran::production.production.create')->with(compact(
            'business_locations',
            'reference_code',
            'gold_smiths',
            'category_types',
            'receiving_stores',
            'gold_grades'
        ));
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request)
    {
        try {
            $business_id = request()->session()->get('business.id');
            $input = $request->except('_token', 'index');
            $input['business_id'] = $business_id;

            GoldProduction::create($input);


            $output = [
                'success' => true,
                'msg' => __('ran::lang.production_add_success')
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
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {
        return view('ran::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        return view('ran::edit');
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy($id)
    {
        //
    }
}
