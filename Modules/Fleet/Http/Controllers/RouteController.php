<?php

namespace Modules\Fleet\Http\Controllers;

use App\Transaction;
use App\TransactionPayment;
use App\Utils\ModuleUtil;
use App\Utils\ProductUtil;
use App\Utils\TransactionUtil;
use App\Utils\Util;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Modules\Fleet\Entities\Route;
use Modules\Fleet\Entities\RouteOperation;
use Yajra\DataTables\Facades\DataTables;

class RouteController extends Controller
{
    protected $commonUtil;
    protected $moduleUtil;
    protected $productUtil;
    protected $transactionUtil;

    /**
     * Constructor
     *
     * @param Util $commonUtil
     * @return void
     */
    public function __construct(Util $commonUtil, ModuleUtil $moduleUtil, ProductUtil $productUtil, TransactionUtil $transactionUtil)
    {
        $this->commonUtil = $commonUtil;
        $this->moduleUtil =  $moduleUtil;
        $this->productUtil =  $productUtil;
        $this->transactionUtil =  $transactionUtil;
    }

    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index()
    {
        if (request()->ajax()) {
            $business_id = request()->session()->get('user.business_id');

            $routes = Route::leftjoin('users', 'routes.created_by', 'users.id')
                ->where('routes.business_id', $business_id)
                ->select([
                    'routes.*',
                    'users.username as created_by',
                ]);

            if (!empty(request()->route_name)) {
                $routes->where('route_name', request()->route_name);
            }
            if (!empty(request()->orignal_location)) {
                $routes->where('orignal_location', request()->orignal_location);
            }
            if (!empty(request()->destination)) {
                $routes->where('destination', request()->destination);
            }
            if (!empty(request()->user_id)) {
                $routes->where('created_by', request()->user_id);
            }
            if (!empty(request()->start_date) && !empty(request()->end_date)) {
                $routes->whereDate('date', '>=', request()->start_date);
                $routes->whereDate('date', '<=', request()->end_date);
            }
            return DataTables::of($routes)
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
                        if (auth()->user()->can('fleet.routes.edit')) {
                            $html .= '<li><a href="#" data-href="' . action('\Modules\Fleet\Http\Controllers\RouteController@edit', [$row->id]) . '" class="btn-modal" data-container=".view_modal"><i class="glyphicon glyphicon-edit"></i> ' . __("messages.edit") . '</a></li>';
                        }
                        if (auth()->user()->can('fleet.routes.delete')) {
                            $html .= '<li><a href="#" data-href="' . action('\Modules\Fleet\Http\Controllers\RouteController@destroy', [$row->id]) . '" class="delete_button"><i class="fa fa-trash"></i> ' . __("messages.delete") . '</a></li>';
                        }
                        return $html;
                    }
                )
                ->editColumn('rate', '{{@num_format($rate)}}')
                ->editColumn('route_amount', '{{@num_format($route_amount)}}')
                ->editColumn('distance', '{{@num_format($distance)}}')
                ->editColumn('helper_incentive', '{{@num_format($helper_incentive)}}')
                ->editColumn('driver_incentive', '{{@num_format($driver_incentive)}}')
                ->editColumn('date', '{{@format_date($date)}}')
                ->removeColumn('id')
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
        $quick_add = request()->quick_add;

        return view('fleet::settings.routes.create')->with(compact(
            'quick_add'
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
            $data = $request->except('_token', 'quick_add');
            $data['date'] = $this->commonUtil->uf_date($data['date']);
            $data['business_id'] = $business_id;
            $data['created_by'] = Auth::user()->id;
            $data['distance'] = $this->commonUtil->num_uf($data['distance']);
            $data['rate'] = $this->commonUtil->num_uf($data['rate']);
            $data['route_amount'] = $this->commonUtil->num_uf($data['route_amount']);
            $data['driver_incentive'] = $this->commonUtil->num_uf($data['driver_incentive']);
            $data['helper_incentive'] = $this->commonUtil->num_uf($data['helper_incentive']);


            Route::create($data);

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
        if($request->quick_add){
            return $output;
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
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        $route = Route::find($id);

        return view('fleet::settings.routes.edit')->with(compact(
            'route'
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
            $data['date'] = $this->commonUtil->uf_date($data['date']);
            $data['distance'] = $this->commonUtil->num_uf($data['distance']);
            $data['rate'] = $this->commonUtil->num_uf($data['rate']);
            $data['route_amount'] = $this->commonUtil->num_uf($data['route_amount']);
            $data['driver_incentive'] = $this->commonUtil->num_uf($data['driver_incentive']);
            $data['helper_incentive'] = $this->commonUtil->num_uf($data['helper_incentive']);

            Route::where('id', $id)->update($data);

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
            Route::where('id', $id)->delete();

            $route_operations = RouteOperation::where('route_id', $id)->get();
            foreach ($route_operations as $route_operation) {
                Transaction::where('id', $route_operation->transaction_id)->delete();
                TransactionPayment::where('transaction_id', $route_operation->transaction_id)->delete();
            }
            RouteOperation::where('helper_id', $id)->delete();

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

        return $output;
    }

    public function getDetails($id)
    {
        $route = Route::find($id);

        return $route;
    }

    public function getRouteDropdown(){
        $business_id = request()->session()->get('business.id');

        $routes = Route::where('business_id', $business_id)->pluck('route_name', 'id');
        $route_dp = $this->transactionUtil->createDropdownHtml($routes, 'Please Select');

        return $route_dp;
    }
}
