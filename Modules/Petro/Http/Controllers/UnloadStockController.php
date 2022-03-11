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
use Illuminate\Support\Facades\Log;
use Modules\Petro\Entities\FuelTank;
use Modules\Petro\Entities\PumpOperator;
use Modules\Petro\Entities\PumpOperatorAssignment;
use Modules\Petro\Entities\UnloadStock;
use Yajra\DataTables\Facades\DataTables;

class UnloadStockController extends Controller
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

            $query = UnloadStock::leftjoin('pump_operators', 'unload_stocks.pump_operator_id', 'pump_operators.id')
                ->leftjoin('fuel_tanks', 'unload_stocks.tank_id', 'fuel_tanks.id')
                ->leftjoin('users', 'unload_stocks.created_by', 'users.id')
                ->where('unload_stocks.business_id', $business_id)
                ->select('pump_operators.name', 'unload_stocks.*', 'fuel_tanks.fuel_tank_number', 'users.username');


            if (!empty(request()->pump_operator_id)) {
                $query->where('unload_stocks.pump_operator_id', request()->pump_operator_id);
                $query->whereDate('unload_stocks.date_and_time', '>=', date('Y-m-d', strtotime("-1 days")));
                $query->whereDate('unload_stocks.date_and_time', '<=', date('Y-m-d'));
            }

            if (!empty(request()->product_id)) {
                $query->where('unload_stocks.product_id', request()->product_id);
            }

            if (!empty(request()->start_date) && !empty(request()->end_date)) {
                $query->whereDate('unload_stocks.date_and_time', '>=', request()->start_date);
                $query->whereDate('unload_stocks.date_and_time', '<=', request()->end_date);
            } else {
                $query->whereDate('unload_stocks.date_and_time', '>=', date('Y-m-d'));
                $query->whereDate('unload_stocks.date_and_time', '<=', date('Y-m-d'));
            }
            if (!empty(request()->tank_id)) {
                $query->where('unload_stocks.tank_id', request()->tank_id);
            }

            $unload_stocks = DataTables::of($query)
                ->addColumn(
                    'date_and_time',
                    '{{@format_datetime($date_and_time)}}'
                )
                ->editColumn(
                    'unloaded_qty',
                    function ($row) use ($business_details) {

                        return  '<span class="display_currency unloaded_qty" data-orig-value="' . $row->unloaded_qty . '" data-currency_symbol = false>' . $this->productUtil->num_f($row->unloaded_qty, false, $business_details, true) . '</span>';
                    }
                )
                ->editColumn(
                    'current_stock',
                    function ($row) use ($business_details) {
                        return  '<span class="display_currency current_stock" data-orig-value="' . $row->current_stock . '" data-currency_symbol = false>' . $this->productUtil->num_f($row->current_stock, false, $business_details, true) . '</span>';
                    }
                )


                ->addColumn(
                    'total_qty',
                    function ($row) use ($business_details) {
                        $total_qty = ($row->unloaded_qty - $row->current_stock);
                        return  '<span class="display_currency total_sale_amount" data-orig-value="' . $total_qty . '" data-currency_symbol = false>' . $this->productUtil->num_f($total_qty, false, $business_details, true) . '</span>';
                    }
                )

                ->removeColumn('id');


            return $unload_stocks->rawColumns(['unloaded_qty', 'current_stock', 'total_qty'])
                ->make(true);
        }
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        $pump_operator_id = Auth::user()->pump_operator_id;
        $business_id = Auth::user()->business_id;

        $pumps = PumpOperatorAssignment::leftjoin('pumps', function ($join) {
            $join->on('pump_operator_assignments.pump_id', 'pumps.id');
        })->leftjoin('pump_operators', 'pump_operator_assignments.pump_operator_id', 'pump_operators.id')
            ->where('pumps.business_id', $business_id)
            ->whereDate('pump_operator_assignments.date_and_time', date('Y-m-d'))
            ->where('pump_operator_assignments.pump_operator_id', $pump_operator_id)
            ->select('pumps.*', 'pump_operator_assignments.pump_operator_id', 'pump_operator_assignments.pump_id',  'pump_operators.name as pumper_name')
            ->orderBy('pumps.id')
            ->get();

        $tanks = FuelTank::where('business_id', $business_id)->pluck('fuel_tank_number', 'id');

        return view('petro::pump_operators.unload_stock.create')->with(compact(
            'pumps',
            'tanks',
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
            $pump_operator_id = Auth::user()->pump_operator_id;
            $business_id = Auth::user()->business_id;
            $input = $request->except('_token');
            $input['pump_operator_id'] =  $pump_operator_id;
            $input['business_id'] =  $business_id;
            $input['date_and_time'] = Carbon::now();
            $input['created_by'] = Auth::user()->id;

            UnloadStock::create($input);

            $output = [
                'success' => true,
                'msg' => __('petro::lang.success')
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

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function getDetails()
    {
        $pump_operator_id = Auth::user()->pump_operator_id;
        $pump_operator = PumpOperator::find($pump_operator_id);
        $only_pumper = request()->only_pumper;
        $layout = 'pumper';

        return view('petro::pump_operators.unload_stock.details')->with(compact(
            'only_pumper',
            'pump_operator',
            'layout'
        ));
    }
}
