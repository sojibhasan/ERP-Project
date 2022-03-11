<?php

namespace Modules\Fleet\Http\Controllers;

use App\Utils\ModuleUtil;
use App\Utils\ProductUtil;
use App\Utils\TransactionUtil;
use App\Utils\Util;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Fleet\Entities\Driver;
use Modules\Fleet\Entities\Helper;
use Modules\Fleet\Entities\Route;

class SettingController extends Controller
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
        $business_id = request()->session()->get('business.id');

        $routes = Route::leftjoin('users', 'routes.created_by', 'users.id')->where('routes.business_id', $business_id)->select('users.username as added_by', 'routes.*')->get();
        $route_names = $routes->pluck('route_name', 'route_name');
        $orignal_locations = $routes->pluck('orignal_location', 'orignal_location');
        $destinations = $routes->pluck('destination', 'destination');
        $users = $routes->pluck('added_by', 'id');

        $drivers = Driver::leftjoin('users', 'drivers.created_by', 'users.id')->where('drivers.business_id', $business_id)->select('users.username as added_by', 'drivers.*')->get();
        $employee_nos = $drivers->pluck('employee_no', 'employee_no');
        $driver_names = $drivers->pluck('driver_name', 'driver_name');
        $nic_numbers = $drivers->pluck('nic_number', 'nic_number');
        $helpers = Helper::leftjoin('users', 'helpers.created_by', 'users.id')->where('helpers.business_id', $business_id)->select('users.username as added_by', 'helpers.*')->get();
        $helper_employee_nos = $helpers->pluck('employee_no', 'employee_no');
        $helper_names = $helpers->pluck('helper_name', 'helper_name');
        $helper_nic_numbers = $helpers->pluck('nic_number', 'nic_number');

        return view('fleet::settings.index')->with(compact(
            'routes',
            'route_names',
            'orignal_locations',
            'destinations',
            'users',
            'employee_nos',
            'driver_names',
            'nic_numbers',
            'helper_employee_nos',
            'helper_names',
            'helper_nic_numbers'
        ));
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
