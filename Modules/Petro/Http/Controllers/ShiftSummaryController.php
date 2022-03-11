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
use Modules\Petro\Entities\PumperDayEntry;
use Yajra\DataTables\Facades\DataTables;

class ShiftSummaryController extends Controller
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

        $business_id = request()->session()->get('user.business_id');
        if (!$this->moduleUtil->hasThePermissionInSubscription($business_id, 'enable_petro_module')) {
            abort(403, 'Unauthorized Access');
        }

        $previous_date = Carbon::now()->subDay(1)->format('Y-m-d');

        if (request()->ajax()) {
            $business_id = request()->session()->get('user.business_id');
            $business_details = Business::find($business_id);
            if (request()->ajax()) {
                $query = PumperDayEntry::leftjoin('pump_operators', 'pumper_day_entries.pump_operator_id', 'pump_operators.id')
                    ->leftjoin('pumps', 'pumper_day_entries.pump_id', 'pumps.id')
                    ->whereDate('pumper_day_entries.date', date('Y-m-d'))->where('pumper_day_entries.business_id', $business_id)
                    ->select('pump_operators.name', 'pumper_day_entries.*', 'pumps.pump_no');

                if (empty($query->count())) {
                    $query = PumperDayEntry::leftjoin('pump_operators', 'pumper_day_entries.pump_operator_id', 'pump_operators.id')
                        ->leftjoin('pumps', 'pumper_day_entries.pump_id', 'pumps.id')
                        ->whereDate('pumper_day_entries.date', $previous_date)->where('pumper_day_entries.business_id', $business_id)
                        ->select('pump_operators.name', 'pumper_day_entries.*', 'pumps.pump_no');
                }

                if (!empty(request()->location_id)) {
                    $query->where('pump_operator_id', request()->location_id);
                }
                if (!empty(request()->pump_operator_id)) {
                    $query->where('pump_operator_id', request()->pump_operator_id);
                }
                if (!empty(request()->pump_id)) {
                    $query->where('pump_id', request()->pump_id);
                }
                if (!empty(request()->payment_method)) {
                    // $query->where('pump_operator_id', request()->payment_method);
                }
                if (!empty(request()->difference)) {
                    // $query->where('pump_operator_id', request()->difference);
                }
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
                            $html .= '</ul></div>';

                            return $html;
                        }
                    )
                    ->addColumn(
                        'date',
                        '{{@format_date($date)}}'
                    )
                    ->editColumn(
                        'sold_ltr',
                        function ($row) use ($business_details) {

                            return  '<span class="display_currency sold_ltr" data-orig-value="' . $row->sold_ltr . '" data-currency_symbol = false>' . $this->productUtil->num_f($row->sold_ltr, false, $business_details, true) . '</span>';
                        }
                    )
                    ->editColumn('testing_ltr', '{{@format_quantity($testing_ltr)}}')
                    ->addColumn('credit_sale', function ($row) use ($business_details) {
                        return  '<span class="display_currency credit_sale" data-orig-value="' . $row->credit_sale . '" data-currency_symbol = false>' . $this->productUtil->num_f($row->credit_sale, false, $business_details, true) . '</span>';
                    })
                    ->addColumn('cards', function ($row) use ($business_details) {
                        return  '<span class="display_currency cards" data-orig-value="' . $row->cards . '" data-currency_symbol = false>' . $this->productUtil->num_f($row->cards, false, $business_details, true) . '</span>';
                    })
                    ->addColumn('cash', function ($row) use ($business_details) {
                        return  '<span class="display_currency cash" data-orig-value="' . $row->cash . '" data-currency_symbol = false>' . $this->productUtil->num_f($row->cash, false, $business_details, true) . '</span>';
                    })
                    ->addColumn('cheque', function ($row) use ($business_details) {
                        return  '<span class="display_currency cheque" data-orig-value="' . $row->cheque . '" data-currency_symbol = false>' . $this->productUtil->num_f($row->cheque, false, $business_details, true) . '</span>';
                    })
                    ->addColumn('total_amount', function ($row) use ($business_details) {
                        $total_amount = $row->credit_sale +  $row->cards + $row->cash + $row->cheque;
                        return  '<span class="display_currency total_amount" data-orig-value="' .  $total_amount . '" data-currency_symbol = false>' . $this->productUtil->num_f($total_amount, false, $business_details, true) . '</span>';
                    })
                    ->addColumn('difference', function ($row) use ($business_details) {
                        $total_amount = $row->credit_sale +  $row->cards + $row->cash + $row->cheque;
                        $sold_amount = $row->amount;
                        $difference = $total_amount - $sold_amount;
                        if ($difference = 0) {
                            return  '<span class="display_currency difference text-red" data-orig-value="' . $difference . '" data-currency_symbol = false>' . $this->productUtil->num_f($difference, false, $business_details, true) . '</span>';
                        }
                        return  '<span class="display_currency difference" data-orig-value="' . $difference . '" data-currency_symbol = false>' . $this->productUtil->num_f($difference, false, $business_details, true) . '</span>';
                    })
                    ->editColumn(
                        'amount',
                        function ($row) use ($business_details) {

                            return  '<span class="display_currency sold_amount" data-orig-value="' . $row->amount . '" data-currency_symbol = false>' . $this->productUtil->num_f($row->amount, false, $business_details, true) . '</span>';
                        }
                    )
                    ->removeColumn('id');


                return $fuel_tanks->rawColumns(['action', 'sold_ltr', 'amount', 'credit_sale', 'cards', 'cash', 'cheque', 'total_amount', 'difference'])
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
}
