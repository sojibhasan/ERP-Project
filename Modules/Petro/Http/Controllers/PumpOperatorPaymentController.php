<?php

namespace Modules\Petro\Http\Controllers;

use App\Business;
use App\Utils\BusinessUtil;
use App\Utils\ModuleUtil;
use App\Utils\ProductUtil;
use App\Utils\TransactionUtil;
use App\Utils\Util;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\Petro\Entities\Pump;
use Modules\Petro\Entities\PumperDayEntry;
use Modules\Petro\Entities\PumpOperator;
use Modules\Petro\Entities\PumpOperatorAssignment;
use Modules\Petro\Entities\PumpOperatorPayment;
use Yajra\DataTables\Facades\DataTables;

class PumpOperatorPaymentController extends Controller
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
    public function __construct(Util $commonUtil, ProductUtil $productUtil, ModuleUtil $moduleUtil, TransactionUtil $transactionUtil, BusinessUtil   $businessUtil)
    {
        $this->commonUtil = $commonUtil;
        $this->productUtil = $productUtil;
        $this->moduleUtil = $moduleUtil;
        $this->transactionUtil = $transactionUtil;
        $this->businessUtil = $businessUtil;

        //barcode types
        $this->barcode_types = $this->productUtil->barcode_types();
    }

    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index()
    {
        $business_id =  Auth::user()->business_id;
        $pump_operator_id = Auth::user()->pump_operator_id;
        $business_details = Business::find($business_id);

        if (!$this->moduleUtil->hasThePermissionInSubscription($business_id, 'enable_petro_module')) {
            abort(403, 'Unauthorized Access');
        }

        $only_pumper = request()->only_pumper;
        $date = date('Y-m-d');

        if (request()->ajax()) {
            $business_id =  Auth::user()->business_id;
            $query = PumpOperatorPayment::leftjoin('pump_operators', 'pump_operator_payments.pump_operator_id', 'pump_operators.id')
                ->leftjoin('users as edited_user', 'pump_operator_payments.edited_by', 'edited_user.id')
                ->where('pump_operators.business_id', $business_id)
                ->whereDate('date_and_time', $date)
                ->select('pump_operator_payments.*', 'pump_operators.name as pump_operator_name', 'edited_user.username as edited_by');

            if ($only_pumper) {
                $query->where('pump_operator_payments.pump_operator_id', $pump_operator_id);
            }
            if (!empty(request()->payment_method)) {
                $query->where('payment_type', request()->payment_method);
            }
            if (!empty(request()->pump_operator_id)) {
                $query->where('pump_operator_id', request()->pump_operator_id);
            }
            if (!empty(request()->start_date) && !empty(request()->end_date)) {
                $query->whereDate('date_and_time', '>=', request()->start_date);
                $query->whereDate('date_and_time', '<=', request()->end_date);
            }

            $fuel_tanks = DataTables::of($query)
                ->addColumn(
                    'action',
                    function ($row) use($pump_operator_id, $business_id, $only_pumper) {
                        $html = '<div class="btn-group">
                        <button type="button" class="btn btn-info dropdown-toggle btn-xs" 
                            data-toggle="dropdown" aria-expanded="false">' .
                            __("messages.actions") .
                            '<span class="caret"></span><span class="sr-only">Toggle Dropdown
                            </span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-left" role="menu">';
                        $is_shift_close = null;
                        if($only_pumper){
                            $is_shift_close =  PumpOperatorAssignment::where('pump_operator_id', $pump_operator_id)->where('business_id', $business_id)->whereDate('date_and_time', date('Y-m-d'))->where('status', 'open')->count();
                            if($is_shift_close){
                                $html .= '<li><a href="#" data-href="' . action('\Modules\Petro\Http\Controllers\PumpOperatorPaymentController@edit', [$row->id]) . '" class="btn-modal" data-container=".view_modal"><i class="glyphicon glyphicon-edit"></i> ' . __("messages.edit") . '</a></li>';
                            }
                        }else{
                            $html .= '<li><a href="#" data-href="' . action('\Modules\Petro\Http\Controllers\PumpOperatorPaymentController@edit', [$row->id]) . '" class="btn-modal" data-container=".view_modal"><i class="glyphicon glyphicon-edit"></i> ' . __("messages.edit") . '</a></li>';
                        }


                        return $html;
                    }
                )
                ->addColumn('date', '{{@format_date($date_and_time)}}')
                ->addColumn('time', '{{@format_time($date_and_time)}}')
                ->removeColumn('id')
                ->editColumn('payment_type', '{{ucfirst($payment_type)}}')
                ->editColumn(
                    'amount',
                    function ($row) use ($business_details) {
                        return  '<span class="display_currency amount" data-orig-value="' . $row->payment_amount . '" data-currency_symbol = false>' . $this->productUtil->num_f($row->payment_amount, false, $business_details, true) . '</span>';
                    }
                );

            return $fuel_tanks->rawColumns(['amount', 'action'])
                ->make(true);
        }

        $pump_operators = PumpOperator::where('business_id', $business_id)->pluck('name', 'id');
        $payment_types = $this->transactionUtil->payment_types();
        $layout = 'app';
        if ($only_pumper) {
            $layout = 'pumper';
        }

        return view('petro::pump_operators.payment_summary')->with(compact(
            'pump_operators',
            'only_pumper',
            'payment_types',
            'layout'
        ));
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        $pump_operator_id = Auth::user()->pump_operator_id;
        $business_id = Auth::user()->business_id;

        $pumps = Pump::leftjoin('pump_operator_assignments', function ($join) {
            $join->on('pumps.id', 'pump_operator_assignments.pump_id')->whereDate('date_and_time', date('Y-m-d'));
        })->leftjoin('pump_operators', 'pump_operator_assignments.pump_operator_id', 'pump_operators.id')
            ->where('pumps.business_id', $business_id)
            ->where('pump_operator_assignments.pump_operator_id', $pump_operator_id)
            ->select('pumps.*', 'pump_operator_assignments.pump_operator_id', 'pump_operator_assignments.pump_id', 'pump_operators.name as pumper_name')
            ->orderBy('pumps.id')
            ->get();

        $layout = 'pumper';


        return view('petro::pump_operators.actions.payments')->with(compact(
            'pumps',
            'layout'
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
        $created_by = Auth::user()->id;


        try {
            $payment_amount = $request->amount;
            $payment_type = $request->payment_type;

            if($payment_amount == "" || $payment_type == ""){
                $output = [
                    'success' => false,
                    'msg' => "Please payment type and amount are mendatory fields!"
                ];
                return $output;
            }

            $data = [
                'business_id' => $business_id,
                'pump_operator_id' => $pump_operator_id,
                'payment_type' => $payment_type,
                'payment_amount' => $payment_amount,
                'created_by' => $created_by,
            ];
            
            PumpOperatorPayment::create($data);

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

        return  $output;
    }

    /**
     * Show the specified resource in modal.
     * @param int $id
     * @return Renderable
     */
    public function getPaymentSummaryModal()
    {
        $only_pumper = request()->only_pumper;

        return view('petro::pump_operators.partials.payment_summary_modal')->with(compact('only_pumper'));
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
        $payment = PumpOperatorPayment::find($id);

        $payment_types = PumpOperatorPayment::getPaymentTypesArray();

        return view('petro::pump_operators.partials.edit_payment')->with(compact(
            'payment',
            'payment_types'
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
        try {
            $data = $request->except('_token', '_method');
            $data['edited_by'] = Auth::user()->id;

            PumpOperatorPayment::where('id', $id)->update($data);
            $output = [
                'success' => true,
                'tab' => 'payment_summary',
                'msg' => __('lang_v1.success')
            ];
        } catch (\Exception $e) {
            Log::emergency('File: ' . $e->getFile() . 'Line: ' . $e->getLine() . 'Message: ' . $e->getMessage());
            $output = [
                'success' => false,
                'tab' => 'payment_summary',
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
        //
    }
    /**
     * return modal view
     * @param int $id
     * @return Renderable
     */
    public function getPaymentModal()
    {
        $pump_operator_id = Auth::user()->pump_operator_id;
        $business_id = Auth::user()->business_id;

        $pumps = Pump::leftjoin('pump_operator_assignments', function ($join) {
            $join->on('pumps.id', 'pump_operator_assignments.pump_id')->whereDate('date_and_time', date('Y-m-d'));
        })->leftjoin('pump_operators', 'pump_operator_assignments.pump_operator_id', 'pump_operators.id')
            ->where('pumps.business_id', $business_id)
            ->where('pump_operator_assignments.pump_operator_id', $pump_operator_id)
            ->select('pumps.*', 'pump_operator_assignments.pump_operator_id', 'pump_operator_assignments.pump_id', 'pump_operators.name as pumper_name')
            ->orderBy('pumps.id')
            ->get();
        $pop_up = true;

        return view('petro::pump_operators.partials.payment_modal')->with(compact(
            'pumps',
            'pop_up'
        ));
    }

    public function balanceToOperator($pump_operator_id)
    {

        $business_id = Auth::user()->business_id;
        $payments = PumpOperatorPayment::whereDate('date_and_time', date('Y-m-d'))
            ->where('pump_operator_id', $pump_operator_id)
            ->select(
                DB::raw('SUM(IF(payment_type="cash", payment_amount, 0)) as cash'),
                DB::raw('SUM(IF(payment_type="card", payment_amount, 0)) as card'),
                DB::raw('SUM(IF(payment_type="cheque", payment_amount, 0)) as cheque'),
                DB::raw('SUM(IF(payment_type="credit", payment_amount, 0)) as credit'),
                DB::raw('SUM(payment_amount) as total')
            )->first();

        $day_entries = PumperDayEntry::leftjoin('pump_operators', 'pumper_day_entries.pump_operator_id', 'pump_operators.id')
            ->leftjoin('pumps', 'pumper_day_entries.pump_id', 'pumps.id')
            ->whereDate('pumper_day_entries.date', date('Y-m-d'))
            ->where('pumper_day_entries.business_id', $business_id)
            ->where('pumper_day_entries.pump_operator_id', $pump_operator_id)
            ->select('pump_operators.name', 'pumper_day_entries.*', 'pumps.pump_name')
            ->get();
        if ($day_entries->sum('amount') - $payments->total > 0) {
            $payment_type = 'shortage';
            $payment_amount = $day_entries->sum('amount') - $payments->total;
        }
        if ($day_entries->sum('amount') - $payments->total < 0) {
            $payment_type = 'excess';
            $payment_amount = $day_entries->sum('amount') - $payments->total;
        }
        if(!isset($payment_type) || $payment_type == ""){
            $output = [
                'success' => 0,
                'msg' => __('messages.something_went_wrong')
            ];
            return redirect()->back()->with('status', $output);
        }
        $data = [
            'business_id' => $business_id,
            'pump_operator_id' => $pump_operator_id,
            'payment_type' => $payment_type,
            'payment_amount' => $payment_amount,
            'created_by' => Auth::user()->id
        ];
        if ($day_entries->sum('amount') - $payments->total != 0) {
            PumpOperatorPayment::create($data);
        }

        $output = [
            'success' => 1,
            'msg' => __('lang_v1.success')
        ];
        return redirect()->back()->with('status', $output);
    }
}
