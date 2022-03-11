<?php

namespace Modules\Superadmin\Http\Controllers;

use App\Unit;
use Carbon\Carbon;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Modules\Superadmin\Entities\TankDipChart;
use Modules\Superadmin\Entities\TankDipChartDetail;
use Yajra\DataTables\Facades\DataTables;

class TankDipChartController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index()
    {
        if (request()->ajax()) {
            $business_id = request()->session()->get('user.business_id');

            $tank_dip_chart = TankDipChart::leftjoin('tank_dip_chart_details', 'tank_dip_charts.id', 'tank_dip_chart_details.tank_dip_chart_id')
                ->where('business_id', $business_id)
                ->select(['dip_reading', 'dip_reading_value', 'tank_dip_chart_details.id', 'tank_dip_charts.id as chart_id']);

            if (!empty(request()->sheet_name)) {
                $tank_dip_chart->where('sheet_name', request()->sheet_name);
            }
            if (!empty(request()->tank_manufacturer)) {
                $tank_dip_chart->where('tank_manufacturer', request()->tank_manufacturer);
            }
            if (!empty(request()->tank_capacity)) {
                $tank_dip_chart->where('tank_capacity', request()->tank_capacity);
            }

            return DataTables::of($tank_dip_chart)
                ->addColumn('action', function ($row) {
                    if (!$row->is_default || $row->name == "Cashier#" . $row->business_id) {
                        $action = '';
                        $action .= '<a data-href="' . action('\Modules\Superadmin\Http\Controllers\TankDipChartController@edit', [$row->chart_id]) . '" data-container=".tank_dip_chart_model" class="btn btn-modal btn-xs btn-primary"><i class="glyphicon glyphicon-edit"></i> ' . __("messages.edit") . '</a>';

                        $action .= '&nbsp
                                <button type="button" data-href="' . action('\Modules\Superadmin\Http\Controllers\TankDipChartController@destroy', [$row->id]) . '" class="btn btn-xs btn-danger delete_tank_dip_chart_button"><i class="glyphicon glyphicon-trash"></i> ' . __("messages.delete") . '</button>';


                        return $action;
                    } else {
                        return '';
                    }
                })
                ->editColumn('dip_reading', '{{@num_format($dip_reading)}}')
                ->editColumn('dip_reading_value', '{{@num_format($dip_reading_value)}}')
                ->removeColumn('id')
                ->removeColumn('chart_id')
                ->rawColumns(['action'])
                ->make(true);
        }
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        $business_id = request()->session()->get('business.id');
        $units = Unit::where('business_id', $business_id)->pluck('actual_name', 'id');

        return view('superadmin::superadmin_settings.tank_dip_chart.create')->with(compact(
            'units'
        ));
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
            $input = $request->only('date', 'sheet_name', 'tank_manufacturer', 'tank_capacity', 'unit_id');
            $input['date'] = !empty($input['date']) ? Carbon::parse($input['date'])->format('Y-m-d') : date('Y-m-d');
            $input['business_id'] = $business_id;

            DB::beginTransaction();
            $tank_dip_cahrt = TankDipChart::create($input);

            $dip_readings = $request->dip_readings;

            foreach ($dip_readings as $dip_reading) {
                $data = [];
                $data['tank_dip_chart_id'] =  $tank_dip_cahrt->id;
                $data['dip_reading'] = $dip_reading['reading'];
                $data['dip_reading_value'] = $dip_reading['value'];

                TankDipChartDetail::create($data);
            }

            DB::commit();

            $output = [
                'success' => true,
                'tank_dip_chart' => true,
                'msg' => __('superadmin::lang.tank_dip_chart_add_success')
            ];
        } catch (\Exception $e) {
            Log::emergency('File: ' . $e->getFile() . 'Line: ' . $e->getLine() . 'Message: ' . $e->getMessage());
            $output = [
                'success' => false,
                'tank_dip_chart' => true,
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
        return view('superadmin::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        $business_id = request()->session()->get('business.id');
        $units = Unit::where('business_id', $business_id)->pluck('actual_name', 'id');

        $tank_dip_chart  = TankDipChart::findOrFail($id);
        $tank_dip_cahrt_details = TankDipChartDetail::where('tank_dip_chart_id', $id)->get();

        return view('superadmin::superadmin_settings.tank_dip_chart.edit')->with(compact(
            'units',
            'tank_dip_chart',
            'tank_dip_cahrt_details'
        ));
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(Request $request, $id)
    {
        $business_id = request()->session()->get('business.id');
        try {
            $input = $request->only('date', 'sheet_name', 'tank_manufacturer', 'tank_capacity', 'unit_id');
            $input['date'] = !empty($input['date']) ? Carbon::parse($input['date'])->format('Y-m-d') : date('Y-m-d');
            $input['business_id'] = $business_id;

            DB::beginTransaction();
            TankDipChart::where('id', $id)->update($input);

            $dip_readings = $request->dip_readings;
            TankDipChartDetail::where('tank_dip_chart_id', $id)->delete(); //delete older entries first
            foreach ($dip_readings as $dip_reading) {
                $data = [];
                $data['tank_dip_chart_id'] =  $id;
                $data['dip_reading'] = $dip_reading['reading'];
                $data['dip_reading_value'] = $dip_reading['value'];

                TankDipChartDetail::create($data);
            }

            DB::commit();

            $output = [
                'success' => true,
                'tank_dip_chart' => true,
                'msg' => __('superadmin::lang.tank_dip_chart_update_success')
            ];
        } catch (\Exception $e) {
            Log::emergency('File: ' . $e->getFile() . 'Line: ' . $e->getLine() . 'Message: ' . $e->getMessage());
            $output = [
                'success' => false,
                'tank_dip_chart' => true,
                'msg' => __('messages.something_went_wrong')
            ];
        }

        return redirect()->back()->with('status', $output);
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy($id)
    {
        try {
            TankDipChartDetail::find($id)->delete();

            $output = [
                'success' => true,
                'msg' => __('superadmin::lang.tank_dip_chart_delete_success')
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
     * Show the form for importing new resources.
     * @return Renderable
     */
    public function getImport()
    {
        $business_id = request()->session()->get('business.id');
        $units = Unit::where('business_id', $business_id)->pluck('actual_name', 'id');

        return view('superadmin::superadmin_settings.tank_dip_chart.import')->with(compact(
            'units'
        ));
    }

    /**
     * Store a newly imported resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function postImport(Request $request)
    {
        $business_id = request()->session()->get('business.id');
        try {
            DB::beginTransaction();
            //Set maximum php execution time
            ini_set('max_execution_time', 0);

            if ($request->hasFile('tank_dip_chart_csv')) {
                $input = $request->only('date', 'sheet_name', 'tank_manufacturer', 'tank_capacity', 'unit_id');
                $input['date'] = !empty($input['date']) ? Carbon::parse($input['date'])->format('Y-m-d') : date('Y-m-d');
                $input['business_id'] = $business_id;
                $tank_dip_cahrt = TankDipChart::create($input);



                $file = $request->file('tank_dip_chart_csv');
                $parsed_array = Excel::toArray([], $file);
                //Remove header row
                $imported_data = array_splice($parsed_array[0], 1);

                $formated_data = [];

                $is_valid = true;
                $error_msg = '';

                foreach ($imported_data as $key => $value) {
                    //Check if 2 no. of columns exists
                    if (count($value) < 2) {
                        $is_valid =  false;
                        $error_msg = "Number of columns mismatch";
                        break;
                    }

                    $row_no = $key + 1;
                    $tank_dip_chart_array = [];

                    //Check dip reading
                    if (!empty($value[0])) {
                        $tank_dip_chart_array['dip_reading'] = $value[0];
                    } else {
                        $is_valid =  false;
                        $error_msg = "dip reading is required in row no. $row_no";
                        break;
                    }
                    //Check dip reading value
                    if (!empty($value[1])) {
                        $tank_dip_chart_array['dip_reading_value'] = $value[1];
                    } else {
                        $is_valid =  false;
                        $error_msg = "dip reading value is required in row no. $row_no";
                        break;
                    }

                    $formated_data[] = $tank_dip_chart_array;
                }
                if (!$is_valid) {
                    throw new \Exception($error_msg);
                }

                if (!empty($formated_data)) {
                    foreach ($formated_data as $dip_reading_data) {


                        $data['tank_dip_chart_id'] =  $tank_dip_cahrt->id;
                        $data['dip_reading'] = $dip_reading_data['dip_reading'];
                        $data['dip_reading_value'] = $dip_reading_data['dip_reading_value'];

                        TankDipChartDetail::create($data);
                    }
                }

                $output = [
                    'success' => 1,
                    'msg' => __('product.file_imported_successfully')
                ];
            }


            DB::commit();

            $output = [
                'success' => true,
                'tank_dip_chart' => true,
                'msg' => __('superadmin::lang.tank_dip_chart_import_success')
            ];
        } catch (\Exception $e) {
            Log::emergency('File: ' . $e->getFile() . 'Line: ' . $e->getLine() . 'Message: ' . $e->getMessage());
            $output = [
                'success' => false,
                'tank_dip_chart' => true,
                'msg' => __('messages.something_went_wrong')
            ];
        }

        return redirect()->back()->with('status', $output);
    }
    public function getTankDipById($id)
    {
        $tank_dip_chart = TankDipChart::leftjoin('units', 'tank_dip_charts.unit_id', 'units.id')->where('tank_dip_charts.id', $id)->first();
        $tank_dip_chart->tank_capacity = number_format($tank_dip_chart->tank_capacity, 3, '.', '');

        return $tank_dip_chart;
    }
    public function getDipReadingValue($id)
    {
        $tank_dip_chart_detail = TankDipChartDetail::findOrFail($id);

        return ['dip_reading_value' => number_format($tank_dip_chart_detail->dip_reading_value, 3, '.', '')];
    }
}
