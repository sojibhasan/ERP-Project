<?php

namespace Modules\Petro\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\Petro\Entities\Pump;
use Modules\Petro\Entities\PumperDayEntry;
use Modules\Petro\Entities\PumpOperatorAssignment;

class PumpOperatorActionsController extends Controller
{
    /**
     * Pump Receive
     * @return Renderable
     */
    public function getReceivePump()
    {
        $pump_operator_id = Auth::user()->pump_operator_id;
        $business_id = Auth::user()->business_id;

        $pumps = Pump::leftjoin('pump_operator_assignments', function ($join) {
            $join->on('pumps.id', 'pump_operator_assignments.pump_id')->where('status', '!=', 'close')->whereDate('date_and_time', date('Y-m-d'));
        })->leftjoin('pump_operators', 'pump_operator_assignments.pump_operator_id', 'pump_operators.id')
            ->where('pumps.business_id', $business_id)
            ->select('pumps.*', 'pump_operator_assignments.pump_operator_id', 'pump_operators.name as pumper_name', 'pump_operator_assignments.status')
            ->orderBy('pump_operator_assignments.date_and_time', 'desc')
            ->groupBy('pumps.id')
            ->get();


        $layout = 'pumper';

        return view('petro::pump_operators.actions.pump_receive')->with(compact(
            'pumps',
            'layout'
        ));
    }

    public function getClosingMeterModal()
    {
        $pump_operator_id = Auth::user()->pump_operator_id;
        $business_id = Auth::user()->business_id;

        // $pumps = PumpOperatorAssignment::leftjoin('pumps', function ($join) {
        //     $join->on('pump_operator_assignments.pump_id', 'pumps.id');
        // })->leftjoin('pump_operators', 'pump_operator_assignments.pump_operator_id', 'pump_operators.id')
        //     ->where('pumps.business_id', $business_id)
        //     ->whereDate('pump_operator_assignments.date_and_time', date('Y-m-d'))
        //     ->where('pump_operator_assignments.status', 'open')
        //     ->where('pump_operator_assignments.pump_operator_id', $pump_operator_id)
        //     ->select('pumps.*', 'pump_operator_assignments.pump_operator_id', 'pump_operator_assignments.pump_id',  'pump_operators.name as pumper_name', 'pump_operator_assignments.status')
        //     ->orderBy('pumps.id')
        //     ->groupBy('pumps.id')
        //     ->toSql();
        // dd($pumps->toArray());
        $date = date('Y-m-d');
        DB::enableQueryLog();


        $pumps = DB::select("SELECT pumps.*, pump_operator_assignments.pump_operator_id, pump_operator_assignments.pump_id, pump_operators.name AS pumper_name, pump_operator_assignments.status FROM pump_operator_assignments LEFT JOIN pumps ON pump_operator_assignments.pump_id = pumps.id LEFT JOIN pump_operators ON pump_operator_assignments.pump_operator_id = pump_operators.id WHERE ( pump_operator_assignments.pump_id, pump_operator_assignments.created_at ) IN( SELECT pump_id, MAX(created_at) FROM pump_operator_assignments WHERE DATE(date_and_time) = '$date' AND pump_operator_id = $pump_operator_id GROUP BY pump_id ) AND pumps.business_id = $business_id");
      // and then you can get query log
        // return $pumps;
// dd(DB::getQueryLog());
//     echo "SELECT pumps.*, pump_operator_assignments.pump_operator_id, pump_operator_assignments.pump_id, pump_operators.name AS pumper_name, pump_operator_assignments.status FROM pump_operator_assignments LEFT JOIN pumps ON pump_operator_assignments.pump_id = pumps.id LEFT JOIN pump_operators ON pump_operator_assignments.pump_operator_id = pump_operators.id WHERE ( pump_operator_assignments.pump_id, pump_operator_assignments.date_and_time ) IN( SELECT pump_id, MAX(date_and_time) FROM pump_operator_assignments WHERE DATE(date_and_time) = '$date' AND pump_operator_id = $pump_operator_id GROUP BY pump_id ) AND pumps.business_id = $business_id";      
//   dd($pumps);   
        // return $pumps;
        return view('petro::pump_operators.actions.closing_meter_modal')->with(compact(
            'pumps'
        ));
    }

    public function getClosingMeter($pump_id)
    {
        $pump_operator_id = Auth::user()->pump_operator_id;
        $business_id = Auth::user()->business_id;

        $pump = Pump::leftjoin('products', 'pumps.product_id', 'products.id')
            ->leftjoin('variations', 'products.id', 'variations.product_id')
            ->leftjoin('variation_location_details', 'variations.id', 'variation_location_details.variation_id')
            ->where('pumps.id', $pump_id)
            ->select('default_sell_price', 'pumps.*', 'variation_location_details.qty_available')->first();

        if (empty(session()->get('pump_operator_main_system'))) {
            $layout = 'pumper';
        } else {
            $layout = 'app';
        }
        // dd($pump->toArray());
        return view('petro::pump_operators.actions.closing_meter')->with(compact(
            'pump',
            'layout'
        ));
    }
    public function postClosingMeter($pump_id, Request $request)
    {
        $pump_operator_id = Auth::user()->pump_operator_id;
        $business_id = Auth::user()->business_id;

        try {
            $data = array(
                'business_id' => $business_id,
                'pump_operator_id' => $pump_operator_id,
                'date' => date('Y-m-d'),
                'pump_id' => $pump_id,
                'pump_no' => $request->pump_no,
                'starting_meter' => $request->starting_meter,
                'closing_meter' => $request->closing_meter,
                'testing_ltr' => $request->testing_ltr,
                'sold_ltr' => $request->sold_ltr,
                'amount' => $request->amount_hidden,
            );

            DB::beginTransaction();

            PumperDayEntry::create($data);
            Pump::where('id', $pump_id)->update(['pod_starting_meter' => $request->starting_meter, 'pod_last_meter' => $request->closing_meter]);
            PumpOperatorAssignment::where('pump_id', $pump_id)->update(['status' => 'close', 'close_date_and_time' => Carbon::now()]);

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

    public function store(Request $request)
    {
        try {
            $user_id = Auth::user()->id;
            $business_id = Auth::user()->business_id;
            $pump_operator_id = Auth::user()->pump_operator_id;


            $payment = new PumpPayment();

            $payment->business_id  = $business_id;
            $payment->pump_operators_id = $pump_operator_id;
            $payment->payment_type = $request->payment_type;
            $payment->payment_amount = $request->display;
            $payment->created_by = $user_id;

            $payment->save();

            $output = [
                'success' => true,
                'msg' => __('petro::lang.payment_added_successfully')
            ];
        } catch (\Exception $e) {
            Log::emergency('File: ' . $e->getFile() . 'Line: ' . $e->getLine() . 'Message: ' . $e->getMessage());
            $output = [
                'success' => false,
                'msg' => __('messages.something_went_wrong')
            ];
        }


        return redirect()->route('pump_operator_payment.index')->with('status', $output);
    }
}
