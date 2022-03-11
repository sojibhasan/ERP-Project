<?php

namespace Modules\Ezyboat\Http\Controllers;

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
use Modules\Ezyboat\Entities\BoatTrip;
use Modules\Ezyboat\Entities\RouteOperation;
use Yajra\DataTables\Facades\DataTables;

class BoatTripController extends Controller
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

            $boat_trips = BoatTrip::leftjoin('users', 'boat_trips.created_by', 'users.id')
                ->where('boat_trips.business_id', $business_id)
                ->select([
                    'boat_trips.*',
                    'users.username as created_by',
                ]);

            if (!empty(request()->trip_name)) {
                $boat_trips->where('trip_name', request()->trip_name);
            }
            if (!empty(request()->starting_location)) {
                $boat_trips->where('starting_location', request()->starting_location);
            }
            if (!empty(request()->final_location)) {
                $boat_trips->where('final_location', request()->final_location);
            }
            if (!empty(request()->user_id)) {
                $boat_trips->where('created_by', request()->user_id);
            }
            if (!empty(request()->start_date) && !empty(request()->end_date)) {
                $boat_trips->whereDate('date', '>=', request()->start_date);
                $boat_trips->whereDate('date', '<=', request()->end_date);
            }
            return DataTables::of($boat_trips)
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
                        if (auth()->user()->can('boat_trips.edit')) {
                            $html .= '<li><a href="#" data-href="' . action('\Modules\Ezyboat\Http\Controllers\BoatTripController@edit', [$row->id]) . '" class="btn-modal" data-container=".view_modal"><i class="glyphicon glyphicon-edit"></i> ' . __("messages.edit") . '</a></li>';
                        }
                        if (auth()->user()->can('boat_trips.delete')) {
                            $html .= '<li><a href="#" data-href="' . action('\Modules\Ezyboat\Http\Controllers\BoatTripController@destroy', [$row->id]) . '" class="delete_button"><i class="fa fa-trash"></i> ' . __("messages.delete") . '</a></li>';
                        }
                        return $html;
                    }
                )
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

        return view('ezyboat::settings.boat_trips.create')->with(compact(
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


            BoatTrip::create($data);

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
        $boat_trip = BoatTrip::find($id);

        return view('ezyboat::settings.boat_trips.edit')->with(compact(
            'boat_trip'
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

            BoatTrip::where('id', $id)->update($data);

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
            BoatTrip::where('id', $id)->delete();

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
        $route = BoatTrip::find($id);

        return $route;
    }

    public function getRouteDropdown(){
        $business_id = request()->session()->get('business.id');

        $routes = BoatTrip::where('business_id', $business_id)->pluck('route_name', 'id');
        $route_dp = $this->transactionUtil->createDropdownHtml($routes, 'Please Select');

        return $route_dp;
    }
}
