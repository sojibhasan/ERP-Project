<?php

namespace Modules\Petro\Http\Controllers;

use App\Business;
use App\Utils\BusinessUtil;
use App\Utils\ModuleUtil;
use App\Utils\ProductUtil;
use App\Utils\TransactionUtil;
use App\Utils\Util;
use Carbon\Carbon;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\Petro\Entities\CurrentMeter;
use Modules\Petro\Entities\Pump;
use Modules\Petro\Entities\PumpOperatorAssignment;
use Yajra\DataTables\Facades\DataTables;

class CurrentMeterController extends Controller
{

    /**
     * All Utils instance.
     *
     */
    protected $productUtil;
    protected $moduleUtil;
    protected $transactionUtil;
    protected $commonUtil;

    /**
     * Constructor
     *
     * @param ProductUtils $product
     * @return void
     */
    public function __construct(Util $commonUtil, ProductUtil $productUtil, ModuleUtil $moduleUtil, TransactionUtil $transactionUtil, BusinessUtil $businessUtil)
    {
        $this->commonUtil = $commonUtil;
        $this->productUtil = $productUtil;
        $this->moduleUtil = $moduleUtil;
        $this->transactionUtil = $transactionUtil;
        $this->businessUtil = $businessUtil;
    }

    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index()
    {

        if (request()->ajax()) {
            $business_id = request()->session()->get('user.business_id');
            $business_details = Business::find($business_id);

            $query = CurrentMeter::leftjoin('pump_operators', 'current_meters.pump_operator_id', 'pump_operators.id')
                ->where('current_meters.business_id', $business_id)
                ->select('pump_operators.name', 'current_meters.*');


            if (!empty(request()->pump_operator_id)) {
                $query->where('pump_operator_id', request()->pump_operator_id);
            }

            if (!empty(request()->start_date) && !empty(request()->end_date)) {
                $query->whereDate('current_meters.date_and_time', '>=', request()->start_date);
                $query->whereDate('current_meters.date_and_time', '<=', request()->end_date);
            } else {
                $query->whereDate('current_meters.date_and_time', '>=', date('Y-m-d'));
                $query->whereDate('current_meters.date_and_time', '<=', date('Y-m-d'));
            }
            if (!empty(request()->pump_id)) {
                $query->where('current_meters.pump_id', request()->pump_id);
            }

            $current_meters = DataTables::of($query)
                ->addColumn(
                    'date_and_time',
                    '{{@format_datetime($date_and_time)}}'
                )
                ->editColumn(
                    'sold_ltr',
                    function ($row) use ($business_details) {

                        return  '<span class="display_currency sold_ltr" data-orig-value="' . $row->sold_ltr . '" data-currency_symbol = false>' . $this->productUtil->num_f($row->sold_ltr, false, $business_details, true) . '</span>';
                    }
                )
                ->editColumn(
                    'current_meter',
                    function ($row) use ($business_details) {
                        return  '<span class="display_currency current_meter" data-orig-value="' . $row->current_meter . '" data-currency_symbol = false>' . $this->productUtil->num_f($row->current_meter, false, $business_details, true) . '</span>';
                    }
                )
                ->editColumn(
                    'last_time_meter',
                    function ($row) use ($business_details) {
                        return  '<span class="display_currency last_time_meter" data-orig-value="' . $row->last_time_meter . '" data-currency_symbol = false>' . $this->productUtil->num_f($row->last_time_meter, false, $business_details, true) . '</span>';
                    }
                )
                ->editColumn(
                    'amount',
                    function ($row) use ($business_details) {
                        if ($row->last_time_meter != 0) {
                            $amount = ($row->current_meter - $row->last_time_meter) * $row->amount;
                        } else {
                            $amount = ($row->current_meter - $row->starting_meter) * $row->amount;
                        }
                        return  '<span class="display_currency sold_amount" data-orig-value="' . $amount . '" data-currency_symbol = false>' . $this->productUtil->num_f($amount, false, $business_details, true) . '</span>';
                    }
                )

                ->editColumn(
                    'total_sale_amount',
                    function ($row) use ($business_details) {
                        $amount = ($row->current_meter - $row->starting_meter) * $row->amount;
                        return  '<span class="display_currency total_sale_amount" data-orig-value="' . $amount . '" data-currency_symbol = false>' . $this->productUtil->num_f($amount, false, $business_details, true) . '</span>';
                    }
                )

                ->removeColumn('id');


            return $current_meters->rawColumns(['sold_ltr', 'amount', 'current_meter', 'last_time_meter', 'total_sale_amount'])
                ->make(true);
        }
    }

    public function getModal()
    {
        $pump_operator_id = Auth::user()->pump_operator_id;
        $business_id = Auth::user()->business_id;

        // $pumps = PumpOperatorAssignment::leftjoin('pumps', function ($join) {
        //     $join->on('pump_operator_assignments.pump_id', 'pumps.id');
        // })->leftjoin('pump_operators', 'pump_operator_assignments.pump_operator_id', 'pump_operators.id')
        //     ->where('pumps.business_id', $business_id)
        //     ->whereDate('pump_operator_assignments.date_and_time', date('Y-m-d'))
        //     ->where('pump_operator_assignments.pump_operator_id', $pump_operator_id)
        //     ->select('pumps.*', 'pump_operator_assignments.pump_operator_id', 'pump_operator_assignments.pump_id',  'pump_operators.name as pumper_name')
        //     ->orderBy('pumps.id')
        //     ->get();
            
        $date = date('Y-m-d');    
        // $pumps = DB::select("SELECT pumps.*, pump_operator_assignments.pump_operator_id, pump_operator_assignments.pump_id, pump_operators.name AS pumper_name, pump_operator_assignments.status FROM pump_operator_assignments LEFT JOIN pumps ON pump_operator_assignments.pump_id = pumps.id LEFT JOIN pump_operators ON pump_operator_assignments.pump_operator_id = pump_operators.id WHERE ( pump_operator_assignments.pump_id, pump_operator_assignments.date_and_timedate_and_tECT pump_id, MAX(date_and_time) FROM pump_operator_assignments WHERE DATE(date_and_time) = '$date' AND pump_operator_id = $pump_operator_id GROUP BY pump_id ) AND pumps.business_id = $business_id");
 $pumps = DB::select("SELECT pumps.*, pump_operator_assignments.pump_operator_id, pump_operator_assignments.pump_id, pump_operators.name AS pumper_name, pump_operator_assignments.status FROM pump_operator_assignments LEFT JOIN pumps ON pump_operator_assignments.pump_id = pumps.id LEFT JOIN pump_operators ON pump_operator_assignments.pump_operator_id = pump_operators.id WHERE ( pump_operator_assignments.pump_id, pump_operator_assignments.created_at ) IN( SELECT pump_id, MAX(created_at) FROM pump_operator_assignments WHERE DATE(date_and_time) = '$date' AND pump_operator_id = $pump_operator_id GROUP BY pump_id ) AND pumps.business_id = $business_id");

        
        return view('petro::pump_operators.current_meter.get_modal')->with(compact(
            'pumps'
        ));
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        $pump_id = request()->pump_id;
        $pump_operator_id = Auth::user()->pump_operator_id;
        $business_id = Auth::user()->business_id;

        $pump = Pump::leftjoin('products', 'pumps.product_id', 'products.id')
            ->leftjoin('variations', 'products.id', 'variations.product_id')
            ->leftjoin('variation_location_details', 'variations.id', 'variation_location_details.variation_id')
            ->where('pumps.id', $pump_id)
            ->select('default_sell_price', 'pumps.*', 'variation_location_details.qty_available')->first();

        $last_time_meter = CurrentMeter::where('pump_id', $pump_id)->whereDate('date', date('Y-m-d'))->first();

        if (empty(session()->get('pump_operator_main_system'))) {
            $layout = 'pumper';
        } else {
            $layout = 'app';
        }
        return view('petro::pump_operators.current_meter.create')->with(compact(
            'pump',
            'layout',
            'last_time_meter'
        ));
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request)
    {
        $pump_operator_id = Auth::user()->pump_operator_id;
        $business_id = Auth::user()->business_id;
        $pump_id = request()->pump_id;
        try {
            $data = array(
                'business_id' => $business_id,
                'pump_operator_id' => $pump_operator_id,
                'date_and_time' => Carbon::now(),
                'pump_id' => $pump_id,
                'pump_no' => $request->pump_no,
                'starting_meter' => $request->starting_meter,
                'current_meter' => $request->current_meter,
                'last_time_meter' => $request->last_time_meter,
                'sold_ltr' => $request->sold_ltr,
                'sale_price' => $request->sale_price,
                'amount' => $request->amount_hidden,
            );

            DB::beginTransaction();

            CurrentMeter::create($data);
            // Pump::where('id', $pump_id)->update(['last_meter_reading' => $request->closing_meter]);
            // PumpOperatorAssignment::where('pump_id', $pump_id)->update(['status' => 'close', 'close_date_and_time' => Carbon::now()]);

            DB::commit();

            $output = [
                'success' => 1,
                'msg' => __('petro::lang.success')
            ];

            return redirect()->to('/petro/pump-operators/dashboard?tab=closing_meter')->with('status', $output);
        } catch (\Exception $e) {
            Log::emergency('File: ' . $e->getFile() . 'Line: ' . $e->getLine() . 'Message: ' . $e->getMessage());
            $output = [
                'success' => 0,
                'msg' => __('messages.something_went_wrong')
            ];

            return redirect()->back()->with('status', $output);
        }
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
