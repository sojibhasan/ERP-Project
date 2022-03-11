<?php

namespace Modules\Petro\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\Petro\Entities\OpeningMeter;
use Modules\Petro\Entities\Pump;
use Yajra\DataTables\Facades\DataTables;

class OpeningMeterController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index()
    {
        if (request()->ajax()) {
            $business_id = request()->session()->get('user.business_id');
            if (request()->ajax()) {
                $query = OpeningMeter::leftjoin('pumps', 'opening_meters.pump_id', 'pumps.id')
                    ->leftjoin('products', 'pumps.product_id', 'products.id')
                    ->leftjoin('business_locations', 'pumps.location_id', 'business_locations.id')
                    ->leftjoin('fuel_tanks', 'pumps.fuel_tank_id', 'fuel_tanks.id')
                    ->leftjoin('users', 'opening_meters.created_by', 'users.id')
                    ->where('pumps.business_id', $business_id)
                    ->select([
                        'opening_meters.*',
                        'pumps.pump_no',
                        'users.username',
                        'fuel_tanks.fuel_tank_number',
                        'products.name as product_name',
                        'business_locations.name as location_name',
                    ]);

                $opening_meters = DataTables::of($query)
                    ->editColumn('date_and_time', '{{@format_datetime($date_and_time)   }}')
                    ->removeColumn('id');

                return $opening_meters->rawColumns([])
                    ->make(true);
            }
        }
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        return view('petro::create');
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request)
    {
        $business_id = request()->session()->get('business.id');
        try {
            $data = $request->except('_token');
            $data['business_id'] = $business_id;
            $data['date_and_time'] = Carbon::now();
            $data['created_by'] = Auth::user()->id;

            DB::beginTransaction();
            OpeningMeter::create($data);

            Pump::where('id', $data['pump_id'])->update(['pod_starting_meter' => $data['reset_meter'], 'pod_last_meter' => $data['reset_meter']]);
            DB::commit();
            $output = [
                'success' => true,
                'msg' => __('lang_v1.success')
            ];
        } catch (\Exception $e) {
            Log::emergency('File: ' . $e->getFile() . 'Line: ' . $e->getLine() . 'Message: ' . $e->getMessage());
            $output = [
                'success' => false,
                'msg' => __('messages.something_went_wrong')
            ];
        }

        return $output;
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {
        return view('petro::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        return view('petro::edit');
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
