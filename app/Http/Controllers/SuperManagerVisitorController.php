<?php

namespace App\Http\Controllers;

use App\Business;
use App\Districts;
use App\Towns;
use App\Utils\BusinessUtil;
use App\Utils\ModuleUtil;
use App\Visitors;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class SuperManagerVisitorController extends Controller
{
    protected $businessUtil;
    protected $moduleUtil;

    /**
     * Constructor
     *
     * @param ProductUtils $product
     * @return void
     */
    public function __construct(BusinessUtil $businessUtil, ModuleUtil $moduleUtil)
    {
        $this->businessUtil = $businessUtil;
        $this->moduleUtil = $moduleUtil;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (request()->ajax()) {
            $visitors = Visitors::leftjoin('districts', 'districts.id', 'visitors.district_id')
                ->leftjoin('towns', 'towns.id', 'visitors.town_id')
                ->select([
                    'visitors.*',
                    'districts.name as district',
                    'towns.name as town',
                ]);

            if (!empty(request()->town)) {
                $visitors->where('visitors.town_id', request()->town);
            }
            if (!empty(request()->district)) {
                $visitors->where('visitors.district_id', request()->district);
            }
            if (!empty(request()->business_id)) {
                $visitors->where('visitors.business_id', request()->business_id);
            }
            if (!empty(request()->start_date) && !empty(request()->end_date)) {
                $visitors->whereDate('visitors.date_and_time', '>=', request()->start_date);
                $visitors->whereDate('visitors.date_and_time', '<=', request()->end_date);
            }

            return DataTables::of($visitors)
                ->addColumn(
                    'action',

                    '<button data-href="{{action(\'SuperManagerVisitorController@edit\',[$id])}}" data-container=".visitor_model" class="btn btn-xs btn-primary btn-modal"><i class="glyphicon glyphicon-edit"></i> @lang("messages.edit")</button>

                    <button data-href="{{action(\'SuperManagerVisitorController@destroy\',[$id])}}" class="btn btn-xs btn-danger delete_visitor"><i class="fa fa-trash "></i> @lang("account.delete")</button>'

                )
                ->removeColumn('id')
                ->rawColumns(['action'])
                ->make(true);
        }

        $towns = Towns::pluck('name', 'id');
        $businesses = Business::pluck('name', 'id');
        $districts = Districts::pluck('name', 'id');
        $visitor_mobile_numbers = Visitors::pluck('mobile_number', 'id');
        $visitor_land_numbers = Visitors::pluck('land_number', 'id');

        return view('super_manager_visitors.index')->with(compact(
            'towns',
            'districts',
            'businesses'
        ));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
