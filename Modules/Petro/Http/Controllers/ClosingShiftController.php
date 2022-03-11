<?php

namespace Modules\Petro\Http\Controllers;

use App\Business;
use App\BusinessLocation;
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
use Modules\Petro\Entities\Pump;
use Modules\Petro\Entities\PumperDayEntry;
use Modules\Petro\Entities\PumpOperator;
use Modules\Petro\Entities\PumpOperatorAssignment;
use Modules\Petro\Entities\PumpOperatorPayment;
use Yajra\DataTables\Facades\DataTables;

class ClosingShiftController extends Controller
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
        $business_id =  Auth::user()->business_id;

        $only_pumper = request()->only_pumper;
        $pump_operator_id = Auth::user()->pump_operator_id;
        if (!$this->moduleUtil->hasThePermissionInSubscription($business_id, 'enable_petro_module')) {
            abort(403, 'Unauthorized Access');
        }

        if (request()->ajax()) {
            $table_payments = PumpOperatorPayment::whereDate('date_and_time', date('Y-m-d'))
                ->select(
                    DB::raw('SUM(IF(payment_type="shortage", payment_amount, 0)) as short_amount'),
                    DB::raw('SUM(IF(payment_type="excess", payment_amount, 0)) as excess_amount'),
                )->first();
            
                   
            if ($only_pumper) {
                $table_payments->where('pump_operator_id', $pump_operator_id);
            }
            $table_payments = $table_payments->first();

             
            $business_details = Business::find($business_id);

            $query = PumperDayEntry::leftjoin('pump_operators', 'pumper_day_entries.pump_operator_id', 'pump_operators.id')
                ->whereDate('pumper_day_entries.date', date('Y-m-d'))
                ->where('pumper_day_entries.business_id', $business_id)
                ->select('pump_operators.name', 'pumper_day_entries.*');
            
            if ($only_pumper) {
                $query->where('pump_operator_id', $pump_operator_id);
            }
            // if (!empty(request()->location_id)) {
            //     $query->where('pump_operator_id', request()->location_id);
            // }
            if (!empty(request()->pump_operator_id)) {
                $query->where('pumper_day_entries.pump_operator_id', request()->pump_operator_id);
            }
            if (!empty(request()->pump_id)) {
                $query->where('pumper_day_entries.pump_id', request()->pump_id);
            }
            if (!empty(request()->payment_method)) {
                // $query->where('pump_operator_id', request()->payment_method);
            }
            if (!empty(request()->difference)) {
                // $query->where('pump_operator_id', request()->difference);
            }
            $file = fopen("imran.txt","w");
            fwrite($file,json_encode($table_payments));
            fclose($file);
            
            $fuel_tanks = DataTables::of($query)
                ->addColumn(
                    'action',
                    function ($row) {
                        $html = '<div class="btn-group">
                            <button type="button" class="btn btn-info dropdown-toggle btn-xs" 
                                data-toggle="dropdown" aria-expanded="false">' .
                            __("messages.actions") .
                            '<span class="caret"></span><span class="sr-only">Toggle Dropdown
                                </span>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-left" role="menu">';

                        // <li><a href="' . action('\Modules\Petro\Http\Controllers\PumpOperatorController@show', [$row->id]) . '"><i class="fa fa-eye" aria-hidden="true"></i>' . __("messages.view") . '</a></li>
                        // <li><a href="' . action('\Modules\Petro\Http\Controllers\PumpOperatorController@edit', [$row->id]) . '" class="edit_contact_button"><i class="fa fa-pencil-square-o"></i> ' . __("messages.edit") . '</a></li>';

                        $html .= '</ul></div>';

                        return $html;
                    }
                )
                ->addColumn(
                    'date',
                    '{{@format_date($date)}}'
                )
                ->editColumn(
                    'time',
                    '{{@format_time($time)}}'
                )
                ->editColumn(
                    'sold_ltr',
                    function ($row) use ($business_details) {

                        return  '<span class="display_currency sold_ltr" data-orig-value="' . $row->sold_ltr . '" data-currency_symbol = false>' . $this->productUtil->num_f($row->sold_ltr, false, $business_details, true) . '</span>';
                    }
                )
                ->editColumn(
                    'testing_ltr',
                    function ($row) use ($business_details) {
                        return  '<span class="display_currency testing_ltr" data-orig-value="' . $row->testing_ltr . '" data-currency_symbol = false>' . $this->productUtil->num_f($row->testing_ltr, false, $business_details, true) . '</span>';
                    }
                )
                ->editColumn(
                    'amount',
                    function ($row) use ($business_details) {
                        return  '<span class="display_currency sold_amount" data-orig-value="' . $row->amount . '" data-currency_symbol = false>' . $this->productUtil->num_f($row->amount, false, $business_details, true) . '</span>';
                    }
                )
                ->addColumn('short_amount', function ($row) use ($table_payments, $business_details) {
                    if (!empty($table_payments->excess_amount)) {
                        return  '<span class="display_currency short_amount" data-orig-value="' . $table_payments->excess_amount . '" data-currency_symbol = false>' . $this->productUtil->num_f($table_payments->excess_amount, false, $business_details, true) . '</span>';
                    }
                    if (!empty($table_payments->short_amount)) {
                        return  '<span class="display_currency short_amount text-red" data-orig-value="' . $table_payments->short_amount . '" data-currency_symbol = false>' . $this->productUtil->num_f($payments->short_amount, false, $business_details, true) . '</span>';
                    }
                })
                ->removeColumn('id');


            return $fuel_tanks->rawColumns(['action', 'sold_ltr', 'amount', 'short_amount', 'short_amount', 'testing_ltr','total_amount'])
                ->make(true);
        }
        $business_locations = BusinessLocation::forDropdown($business_id);
        $pumps = Pump::where('business_id', $business_id)->get();
        if ($only_pumper) {
            $pump_operators = PumpOperator::where('business_id', $business_id)->where('id', $pump_operator_id)->pluck('name', 'id');
        } else {
            $pump_operators = PumpOperator::where('business_id', $business_id)->pluck('name', 'id');
        }
        $payment_types = $this->transactionUtil->payment_types();

        $day_entries_query = PumperDayEntry::leftjoin('pump_operators', 'pumper_day_entries.pump_operator_id', 'pump_operators.id')
            ->leftjoin('pumps', 'pumper_day_entries.pump_id', 'pumps.id')
            ->whereDate('pumper_day_entries.date', date('Y-m-d'))->where('pumper_day_entries.business_id', $business_id)
            ->select('pump_operators.name', 'pumper_day_entries.*', 'pumps.pump_name');
        if ($only_pumper) {
            $day_entries_query->where('pump_operator_id', $pump_operator_id);
        }

        $day_entries = $day_entries_query->get();
        $today_pumps = implode(', ', $day_entries->pluck('pump_name')->toArray());

        $payments = PumpOperatorPayment::leftjoin('pump_operators', 'pump_operator_payments.pump_operator_id', 'pump_operators.id')
            ->whereDate('date_and_time', date('Y-m-d'))->select(
                DB::raw('SUM(IF(payment_type="cash", payment_amount, 0)) as cash'),
                DB::raw('SUM(IF(payment_type="card", payment_amount, 0)) as card'),
                DB::raw('SUM(IF(payment_type="cheque", payment_amount, 0)) as cheque'),
                DB::raw('SUM(IF(payment_type="credit", payment_amount, 0)) as credit'),
                DB::raw('SUM(payment_amount) as total'),
                DB::raw('SUM(IF(payment_type="shortage", payment_amount, 0)) as shortage'),
                DB::raw('SUM(IF(payment_type="excess", payment_amount, 0)) as excess'),
            );
        if ($only_pumper) {
            $payments->where('pump_operator_id', $pump_operator_id);
        }
        $payments = $payments->first();
        $pump_operator = PumpOperator::find($pump_operator_id);
        $pump_operator_name = "";
        if(!empty($pump_operator)){
            $pump_operator_name = $pump_operator->name;
        }

        
        $layout = 'app';
        if ($only_pumper) {
            $layout = 'pumper';
        }
        return view('petro::pump_operators.actions.closing_shift')->with(compact(
            'layout',
            'day_entries',
            'business_locations',
            'pumps',
            'pump_operators',
            'pump_operator',
            'today_pumps',
            'payment_types',
            'payments',
            'only_pumper',
            'pump_operator_name'
        ));
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
        //
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

    public function closeShift($pump_operator_id)
    {
        $business_id =  Auth::user()->business_id;
        PumpOperatorAssignment::where('pump_operator_id', $pump_operator_id)->where('business_id', $business_id)->whereDate('date_and_time', date('Y-m-d'))->update(['status' => 'close', 'close_date_and_time' => Carbon::now()]);

        $output = [
            'success' => 1,
            'msg' => __('lang_v1.success')
        ];
        return redirect()->to('/petro/pump-operators/dashboard')->with('status', $output);
    }
}
