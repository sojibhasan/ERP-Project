<?php

namespace Modules\Property\Http\Controllers;

use App\BusinessLocation;
use App\Currency;
use App\Transaction;
use App\Utils\BusinessUtil;
use App\Utils\ModuleUtil;
use App\Utils\TransactionUtil;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Modules\Property\Entities\Property;
use Modules\Property\Entities\SalesOfficer;

class SaleAndCustomerPaymentController extends Controller
{
    /**
     * All Utils instance.
     *
     */
    protected $businessUtil;
    protected $transactionUtil;
    protected $moduleUtil;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(
        BusinessUtil $businessUtil,
        TransactionUtil $transactionUtil,
        ModuleUtil $moduleUtil
    ) {
        $this->businessUtil = $businessUtil;
        $this->transactionUtil = $transactionUtil;
        $this->moduleUtil = $moduleUtil;
    }

    /**
     * Display dashboard
     * @return Renderable
     */
    public function dashboard()
    {
        $business_id = request()->session()->get('user.business_id');


        $fy = $this->businessUtil->getCurrentFinancialYear($business_id);
        $date_filters['this_fy'] = $fy;
        $date_filters['this_month']['start'] = date('Y-m-01');
        $date_filters['this_month']['end'] = date('Y-m-t');
        $date_filters['this_week']['start'] = date('Y-m-d', strtotime('monday this week'));
        $date_filters['this_week']['end'] = date('Y-m-d', strtotime('sunday this week'));

        $currency = Currency::where('id', request()->session()->get('business.currency_id'))->first();

        $properties = Property::join('property_blocks', 'property_blocks.property_id', 'properties.id')->where('status', 'open')->where('properties.business_id', $business_id)->notSold()->select('properties.*')->groupBy('properties.id')->get();
        $sold_properties = Property::join('property_blocks', 'property_blocks.property_id', 'properties.id')->where('status', '!=', 'open')->where('properties.business_id', $business_id)->onlySold()->select('properties.*')->groupBy('properties.id')->get();

        $business_locations = BusinessLocation::where('business_id', $business_id)->pluck('name', 'id');
        $projects = Property::where('business_id', $business_id)->pluck('name', 'id');
        $sale_officers = SalesOfficer::leftjoin('users', 'sales_officers.officer_id', 'users.id')->where('sales_officers.business_id', $business_id)->pluck('username', 'sales_officers.id');

        $layout = 'property';
        if (Session::get('access-main-system' . Auth::user()->id)) {
            $layout = 'app';
        }
        if (empty(Auth::user()->pump_operator_id)) {
            $layout = 'app';
        }

        return view('property::sale_and_customer_payment.dashboard')->with(compact(
            'layout',
            'date_filters',
            'currency',
            'properties',
            'business_locations',
            'sale_officers',
            'projects',
            'sold_properties'
        ));
    }

    /**
     * set the session value to show main system pages
     * @return Renderable
     */
    public function accessMainSystem()
    {
        $user_id = Auth::user()->id;
        Session::put('access-main-system' . $user_id, true);

        return redirect()->to('/property/sale-and-customer-payment/dashboard');
    }


    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        return view('property::create');
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
        return view('property::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        return view('property::edit');
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

    public function getTotals()
    {
        $start_date = request()->start;
        $end_date = request()->end;
        $location_id = request()->location_id;
        $porject_id = request()->porject_id;
        $block_id = request()->block_id;
        $officer_id = request()->officer_id;

        $block_count_query = Transaction::leftjoin('property_sell_lines', 'transactions.id', 'property_sell_lines.transaction_id')
            ->leftjoin('property_blocks', 'property_sell_lines.block_id', 'property_blocks.id')
            ->leftjoin('properties', 'property_sell_lines.property_id', 'properties.id')
            ->whereDate('transactions.transaction_date', '>=', $start_date)
            ->whereDate('transactions.transaction_date', '<=', $end_date)
            ->where('type', 'property_sell')
            ->where('property_blocks.is_sold', 1)
            ->where('property_blocks.sold_by', Auth::user()->id)
            ->groupBy('property_blocks.id');
        if (!empty($location_id)) {
            $block_count_query->where('transactions.location_id', $location_id);
        }
        if (!empty($block_id)) {
            $block_count_query->where('property_sell_lines.block_id', $block_id);
        }
        if (!empty($porject_id)) {
            $block_count_query->where('property_sell_lines.property_id', $porject_id);
        }
        if (!empty($officer_id)) {
            // $block_count_query->where('property_sell_lines.officer_id', $officer_id);
        }
        $block_counts = $block_count_query->count();

        $amount_of_sold_blocks_query = Transaction::leftjoin('property_sell_lines', 'transactions.id', 'property_sell_lines.transaction_id')
            ->where('type', 'property_sell')
            ->whereDate('transactions.transaction_date', '>=', $start_date)
            ->whereDate('transactions.transaction_date', '<=', $end_date)
            ->where('transactions.created_by', Auth::user()->id)
            ->select(
                DB::raw('SUM(transactions.final_total) as total')
            );
        if (!empty($location_id)) {
            $amount_of_sold_blocks_query->where('transactions.location_id', $location_id);
        }
        if (!empty($block_id)) {
            $amount_of_sold_blocks_query->where('property_sell_lines.block_id', $block_id);
        }
        if (!empty($porject_id)) {
            $amount_of_sold_blocks_query->where('property_sell_lines.property_id', $porject_id);
        }
        if (!empty($officer_id)) {
            // $amount_of_sold_blocks_query->where('property_sell_lines.officer_id', $officer_id);
        }
        $amount_of_sold_blocks = $amount_of_sold_blocks_query->first();
        $data = [
            'total_plots_sold' => $block_counts,
            'amount_of_sold_blocks' => $amount_of_sold_blocks->total
        ];

        return $data;
    }
}
