<?php

namespace Modules\Petro\Http\Controllers;

use App\BusinessLocation;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Modules\Petro\Entities\FuelTank;
use Modules\Petro\Entities\MeterResetting;
use Modules\Petro\Entities\Pump;
use Yajra\DataTables\Facades\DataTables;

class MeterResettingController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        $business_id = request()->session()->get('user.business_id');
        if (request()->ajax()) {
            $query = MeterResetting::leftjoin('pumps', 'meter_resettings.pump_id', 'pumps.id')
                ->leftjoin('business_locations', 'meter_resettings.location_id', 'business_locations.id')
                ->leftjoin('fuel_tanks', 'pumps.fuel_tank_id', 'fuel_tanks.id')
                ->leftjoin('users', 'meter_resettings.created_by', 'users.id')
                ->where('meter_resettings.business_id', $business_id)
                ->select([
                    'meter_resettings.*',
                    'business_locations.name as location_name',
                    'fuel_tanks.fuel_tank_number',
                    'pumps.pump_no',
                    'users.username',
                ]);

            if (!empty(request()->location_id)) {
                $query->where('meter_resettings.location_id', request()->location_id);
            }
            if (!empty(request()->tank_id)) {
                $query->where('fuel_tanks.id', request()->tank_id);
            }

            if (!empty(request()->product_id)) {
                $query->where('pumps.product_id', request()->product_id);
            }
            if (!empty(request()->start_date) && !empty(request()->end_date)) {
                $query->whereBetween('meter_resettings.date_and_time', [date(request()->start_date), date(request()->end_date)]);
            }

            $dip_report = Datatables::of($query)
                ->addColumn(
                    'action',
                    '<a data-href="{{action(\'\Modules\Petro\Http\Controllers\MeterResettingController@show\', [$id])}}" class="btn-modal btn btn-primary btn-xs" data-container=".pump_modal"><i class="fa fa-eye" aria-hidden="true"></i> @lang("messages.view")</a>'
                )

                ->removeColumn('id');


            return $dip_report->rawColumns(['action'])
                ->make(true);
        }
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create()
    {
        $business_id = request()->session()->get('user.business_id');
        $business_locations = BusinessLocation::where('business_id', $business_id)->pluck('name', 'id');

        $pumps = Pump::where('business_id', $business_id)->pluck('pump_no', 'id');
        $count = MeterResetting::where('business_id', $business_id)->count();

        $ref_no = $count + 1;

        return view('petro::pumps.partials.add_meter_reset')->with(compact(
            'business_locations',
            'pumps',
            'ref_no'
        ));
    }

    /**
     * Store a newly created resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        try {
            $business_id = request()->session()->get('user.business_id');

            $data = array(
                'business_id' => $business_id,
                'location_id' => $request->location_id,
                'meter_reset_ref_no' => $request->meter_reset_ref_no,
                'date_and_time' => Carbon::parse($request->date_and_time)->format('Y-m-d'),
                'pump_id' => $request->pump_id,
                'last_meter' => $request->last_meter,
                'new_reset_meter' => $request->new_reset_meter,
                'reason' => $request->reason,
                'created_by' => Auth::user()->id,
            );

            MeterResetting::create($data);

            Pump::where('id', $request->pump_id)->update(['last_meter_reading' => $request->new_reset_meter]);
            $output = [
                'success' => true,
                'msg' => __('petro::lang.success')
            ];
        } catch (\Exception $e) {
            \Log::emergency('File: ' . $e->getFile() . 'Line: ' . $e->getLine() . 'Message: ' . $e->getMessage());
            $output = [
                'success' => false,
                'msg' => __('messages.something_went_wrong')
            ];
        }

        return $output;
    }

    /**
     * Show the specified resource.
     * @return Response
     */
    public function show($id)
    {
        $business_id = request()->session()->get('user.business_id');
        $business_locations = BusinessLocation::where('business_id', $business_id)->pluck('name', 'id');

        $meter_resettings = MeterResetting::leftjoin('pumps', 'meter_resettings.pump_id', 'pumps.id')
        ->leftjoin('business_locations', 'meter_resettings.location_id', 'business_locations.id')
        ->leftjoin('fuel_tanks', 'pumps.fuel_tank_id', 'fuel_tanks.id')
        ->leftjoin('users', 'meter_resettings.created_by', 'users.id')
        ->where('meter_resettings.id', $id)
        ->select([
            'meter_resettings.*',
            'business_locations.name as location_name',
            'fuel_tanks.fuel_tank_number',
            'pumps.pump_no',
            'users.username',
        ])->first();


        return view('petro::pumps.partials.show_meter_resettings')->with(compact(
            'meter_resettings',
        ));
    }

    /**
     * Show the form for editing the specified resource.
     * @return Response
     */
    public function edit()
    {
        return view('petro::edit');
    }

    /**
     * Update the specified resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function update(Request $request)
    {
    }

    /**
     * Remove the specified resource from storage.
     * @return Response
     */
    public function destroy()
    {
    }

    public function getPumpDetails(Request $request)
    {
        $pump_id = $request->pump_id;

        $pump = Pump::leftjoin('fuel_tanks', 'pumps.fuel_tank_id', 'fuel_tanks.id')
            ->leftjoin('products', 'fuel_tanks.product_id', 'products.id')
            ->where('pumps.id', $pump_id)
            ->select('fuel_tanks.fuel_tank_number', 'products.name as product_name', 'pumps.last_meter_reading')->first();

        return $pump;
    }
}
