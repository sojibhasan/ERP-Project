<?php

namespace Modules\Ezyboat\Http\Controllers;

use App\Utils\ModuleUtil;
use App\Utils\ProductUtil;
use App\Utils\TransactionUtil;
use App\Utils\Util;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Ezyboat\Entities\IncomeSetting;
use Modules\Ezyboat\Entities\BoatTrip;
use Modules\Ezyboat\Entities\Crew;

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

        $boat_trips = BoatTrip::leftjoin('users', 'boat_trips.created_by', 'users.id')->where('boat_trips.business_id', $business_id)->select('users.username as added_by', 'boat_trips.*')->get();
        $trip_names = $boat_trips->pluck('trip_name', 'trip_name');
        $starting_locations = $boat_trips->pluck('starting_location', 'starting_location');
        $final_locations = $boat_trips->pluck('final_location', 'final_location');
        $users = $boat_trips->pluck('added_by', 'id');

        $crews = Crew::leftjoin('users', 'crews.created_by', 'users.id')->where('crews.business_id', $business_id)->select('users.username as added_by', 'crews.*')->get();
        $employee_nos = $crews->pluck('employee_no', 'employee_no');
        $crew_names = $crews->pluck('crew_name', 'crew_name');
        $nic_numbers = $crews->pluck('nic_number', 'nic_number');
        $income_settings = IncomeSetting::leftjoin('users', 'income_settings.created_by', 'users.id')->where('income_settings.business_id', $business_id)->select('users.username as added_by', 'income_settings.*')->get();
        $income_setting_employee_nos = $income_settings->pluck('employee_no', 'employee_no');
        $income_names = $income_settings->pluck('income_name', 'income_name');
        $income_setting_nic_numbers = $income_settings->pluck('nic_number', 'nic_number');

        return view('ezyboat::settings.index')->with(compact(
            'boat_trips',
            'trip_names',
            'starting_locations',
            'final_locations',
            'users',
            'employee_nos',
            'crew_names',
            'nic_numbers',
            'income_setting_employee_nos',
            'income_names',
            'income_setting_nic_numbers'
        ));
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        return view('ezyboat::create');
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
        return view('ezyboat::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        return view('ezyboat::edit');
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
