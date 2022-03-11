<?php

namespace Modules\Fleet\Http\Controllers;

use App\Transaction;
use App\Utils\BusinessUtil;
use App\Utils\ModuleUtil;
use App\Utils\ProductUtil;
use App\Utils\TransactionUtil;
use App\Utils\Util;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Yajra\DataTables\Facades\DataTables;

class IncomeController extends Controller
{
    protected $commonUtil;
    protected $moduleUtil;
    protected $productUtil;
    protected $transactionUtil;
    protected $businessUtil;

    /**
     * Constructor
     *
     * @param Util $commonUtil
     * @return void
     */
    public function __construct(Util $commonUtil, ModuleUtil $moduleUtil, ProductUtil $productUtil, TransactionUtil $transactionUtil, BusinessUtil $businessUtil)
    {
        $this->commonUtil = $commonUtil;
        $this->moduleUtil =  $moduleUtil;
        $this->productUtil =  $productUtil;
        $this->transactionUtil =  $transactionUtil;
        $this->businessUtil =  $businessUtil;
    }

    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index()
    {
        $business_id = request()->session()->get('business.id');
        if (!$this->moduleUtil->hasThePermissionInSubscription($business_id, 'fleet_module')) {
            abort(403, 'Unauthorized action.');
        }
        if (request()->ajax()) {
            $route_operations = Transaction::leftjoin('route_operations', 'transactions.id', 'route_operations.transaction_id')
                ->leftjoin('business_locations', 'route_operations.location_id', 'business_locations.id')
                ->leftjoin('transaction_payments', 'transactions.id', 'transaction_payments.transaction_id')
                ->leftjoin('drivers', 'route_operations.driver_id', 'drivers.id')
                ->leftjoin('helpers', 'route_operations.helper_id', 'helpers.id')
                ->leftjoin('products', 'route_operations.product_id', 'products.id')
                ->leftjoin('fleets', 'route_operations.fleet_id', 'fleets.id')
                ->leftjoin('contacts', 'route_operations.contact_id', 'contacts.id')
                ->leftjoin('routes', 'route_operations.route_id', 'routes.id')
                ->leftjoin('users', 'route_operations.created_by', 'users.id')
                ->where('transactions.type', 'route_operation')
                ->where('route_operations.business_id', $business_id)
                ->select([
                    'route_operations.*',
                    'drivers.driver_name',
                    'helpers.helper_name',
                    'routes.route_name',
                    'fleets.vehicle_number',
                    'contacts.name as contact_name',
                    'products.name as product_name',
                    'transactions.payment_status',
                    'transactions.id as t_id',
                    'transactions.final_total',
                    'transaction_payments.method',
                    'business_locations.name as location_name',
                    DB::raw("SUM(IF(transactions.type = 'route_operation', transaction_payments.amount, 0)) as total_received")
                ]);

            if (!empty(request()->start_date) && !empty(request()->end_date)) {
                $route_operations->whereDate('transactions.transaction_date', '>=', request()->start_date);
                $route_operations->whereDate('transactions.transaction_date', '<=', request()->end_date);
            }
            if (!empty(request()->route_id)) {
                $route_operations->where('route_operations.route_id', request()->route_id);
            }
            if (!empty(request()->fleet_id)) {
                $route_operations->where('route_operations.fleet_id', request()->fleet_id);
            }
            $route_operations->groupBy('route_operations.id');
            $route_operations->orderBy('route_operations.id', 'asc');
            Session::put('balance', 0);
            return DataTables::of($route_operations)

                ->editColumn('date_of_operation', '{{@format_date($date_of_operation)}}')
                ->addColumn('debit', '@if(!empty($final_total)){{@num_format($final_total)}}@endif')
                ->addColumn('credit', '@if(!empty($total_received)){{@num_format($total_received)}}@endif')
                ->addColumn('balance', function ($row) {
                
                    $balance = Session::get('balance');
                    $balance = $balance +  $row->final_total - $row->total_received;
                    Session::put('balance', $balance);

                    $html = '<span class="display_currency" data-currency_symbol="false" data-orig-value="' . $balance . '">' . $balance . '</span>';

                    return $html;
                })

                ->removeColumn('id')
                ->rawColumns(['action', 'balance'])
                ->make(true);
        }
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        return view('fleet::create');
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
        return view('fleet::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        return view('fleet::edit');
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
