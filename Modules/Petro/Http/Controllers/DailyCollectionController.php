<?php

namespace Modules\Petro\Http\Controllers;

use App\Business;
use App\BusinessLocation;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Validator;
use Modules\Petro\Entities\PumpOperator;
use App\Utils\Util;
use App\Utils\ProductUtil;
use App\Utils\ModuleUtil;
use App\Utils\TransactionUtil;
use App\Utils\BusinessUtil;
use Illuminate\Support\Facades\Auth;
use Modules\Petro\Entities\DailyCollection;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;

class DailyCollectionController extends Controller
{

    /**
     * All Utils instance.
     *
     */
    protected $productUtil;
    protected $moduleUtil;
    protected $transactionUtil;
    protected $commonUtil;

    private $barcode_types;

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
     * @return Response
     */
    public function index()
    {
        $business_id = request()->session()->get('user.business_id');

        if (!$this->moduleUtil->hasThePermissionInSubscription($business_id, 'enable_petro_module')) {
            abort(403, 'Unauthorized Access');
        }

        if (request()->ajax()) {
            $business_id = request()->session()->get('user.business_id');
            if (request()->ajax()) {
                $query = DailyCollection::leftjoin('business_locations', 'daily_collections.location_id', 'business_locations.id')
                    ->leftjoin('pump_operators', 'daily_collections.pump_operator_id', 'pump_operators.id')
                    ->leftjoin('users', 'daily_collections.created_by', 'users.id')
                    ->leftjoin('settlements', 'daily_collections.settlement_id', 'settlements.id')
                    ->where('daily_collections.business_id', $business_id)
                    ->select([
                        'daily_collections.*',
                        'business_locations.name as location_name',
                        'pump_operators.name as pump_operator_name',
                        'settlements.settlement_no as settlement_no',
                        'users.username as user',
                    ]);
                
                if (!empty(request()->location_id)) {
                    $query->where('daily_collections.location_id', request()->location_id);
                }
                if (!empty(request()->pump_operator)) {
                    $query->where('daily_collections.pump_operator_id', request()->pump_operator);
                }
                if (!empty(request()->settlement_no)) {
                    $query->where('daily_collections.id', request()->settlement_no);
                }                    
                if (!empty(request()->start_date) && !empty(request()->end_date)) {
                    $query->whereDate('settlements.transaction_date', '>=', request()->start_date);
                    $query->whereDate('settlements.transaction_date', '<=', request()->end_date);
                }
                $query->orderBy('settlements.id', 'desc');
                $fuel_tanks = Datatables::of($query)
                    ->addColumn(
                        'action',
                        '<button class="btn btn-primary btn-xs print_btn_pump_operator" data-href="{{action(\'\Modules\Petro\Http\Controllers\DailyCollectionController@print\', [$id])}}"><i class="fa fa-print" aria-hidden="true"></i> @lang("petro::lang.print")</button>
                        @can("daily_collection.delete")<a class="btn btn-danger btn-xs delete_daily_collection" href="{{action(\'\Modules\Petro\Http\Controllers\DailyCollectionController@destroy\', [$id])}}"><i class="fa fa-trash" aria-hidden="true"></i> @lang("petro::lang.delete")</a>@endcan'
                    )
                    /**
                     * @ChangedBy Afes
                     * @Date 25-05-2021
                     * @Task 12700
                     */
                    ->addColumn('total_collection', function ($id) {
                        if ($id->settlement_id != null || $id->settlement_id != "") {
                            $dataAmount1 = DB::table('daily_collections')
                                ->where('pump_operator_id', $id->pump_operator_id)
                                ->where('id', '<', $id->id)
                                ->groupBy('pump_operator_id')
                                ->sum('current_amount');
                            $dataAmount2 = DB::table('daily_collections')
                                ->where('pump_operator_id', $id->pump_operator_id)
                                ->where('id', $id->id)
                                ->groupBy('pump_operator_id')
                                ->sum('current_amount');
                            return ($dataAmount1+$dataAmount2);
                        }else{
                            $dataAmount1 = DB::table('daily_collections')
                                ->where('pump_operator_id', $id->pump_operator_id)
                                ->where('id', '<', $id->id)
                                ->groupBy('pump_operator_id')
                                ->sum('current_amount');
                            $dataAmount2 = DB::table('daily_collections')
                                ->where('pump_operator_id', $id->pump_operator_id)
                                ->where('id', $id->id)
                                ->groupBy('pump_operator_id')
                                ->sum('current_amount');
                            $dataBal1 = DB::table('daily_collections')
                                ->where('pump_operator_id', $id->pump_operator_id)
                                ->where('id', '<', $id->id)
                                ->groupBy('pump_operator_id')
                                ->sum('balance_collection');
                            $dataBal2 = DB::table('daily_collections')
                                ->where('pump_operator_id', $id->pump_operator_id)
                                ->where('id', $id->id)
                                ->groupBy('pump_operator_id')
                                ->sum('balance_collection');
                            return ($dataAmount1+$dataAmount2)-($dataBal1+$dataBal2);
                        }
                    })
                    ->addColumn(
                        'created_at',
                        '{{@format_date($created_at)}}'
                    )


                    ->removeColumn('id');


                return $fuel_tanks->rawColumns(['action','total_collection'])
                    ->make(true);
            }
        }

        $business_locations = BusinessLocation::forDropdown($business_id);
        $pump_operators = PumpOperator::where('business_id', $business_id)->pluck('name', 'id');
        $settlement_nos = [];

        $message = $this->transactionUtil->getGeneralMessage('general_message_pump_management_checkbox');

        return view('petro::daily_collection.index')->with(compact(
            'business_locations',
            'pump_operators',
            'settlement_nos',
            'message'
        ));
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create()
    {
        $business_id = request()->session()->get('user.business_id');        
        $locations = BusinessLocation::forDropdown($business_id);
        $default_location = current(array_keys($locations->toArray()));
        
        $pump_operators = PumpOperator::where('business_id', $business_id)->pluck('name', 'id');

        $collection_form_no = (int) (DailyCollection::where('business_id', $business_id)->count()) + 1;


        return view('petro::daily_collection.create')->with(compact('locations', 'pump_operators', 'collection_form_no','default_location'));
    }

    /**
     * Store a newly created resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'collection_form_no' => 'required',
            'pump_operator_id' => 'required',
            'balance_collection' => 'required',
            'current_amount' => 'required',
            'location_id' => 'required'
        ]);

        if ($validator->fails()) {
            $output = [
                'succcess' => 0,
                'msg' => $validator->errors()->all()[0]
            ];

            return redirect()->back()->with('status', $output);
        }
        $business_id = request()->session()->get('business.id');
        try {
            $data = array(
                'business_id' => $business_id,
                'collection_form_no' => $request->collection_form_no,
                'pump_operator_id' => $request->pump_operator_id,
                'location_id' => $request->location_id,
                'balance_collection' => 0, //$request->balance_collection,
                'current_amount' => $request->current_amount,
                'created_by' =>  Auth::user()->id
            );

            DailyCollection::create($data);

            $output = [
                'success' => 1,
                'msg' => __('petro::lang.daily_collection_add_success')
            ];
        } catch (\Exception $e) {
            \Log::emergency('File: ' . $e->getFile() . 'Line: ' . $e->getLine() . 'Message: ' . $e->getMessage());
            $output = [
                'success' => 0,
                'msg' => __('messages.something_went_wrong')
            ];
        }

        return redirect()->back()->with('status', $output);
    }

    /**
     * Show the specified resource.
     * @return Response
     */
    public function show()
    {
    }

    /**
     * Show the form for editing the specified resource.
     * @return Response
     */
    public function edit($id)
    {
    }

    /**
     * Update the specified resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function update(Request $request)
    {
    }

    /**
     * Remove the specified resource from storage.
     * @return Response
     */
    public function destroy($id)
    {
        try {
            DailyCollection::where('id', $id)->delete();
            $output = [
                'success' => true,
                'msg' => __('petro::lang.daily_collection_delete_success')
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

    /**
     * Remove the specified resource from storage.
     * @return Response
     */
    public function print($pump_operator_id)
    {
        $daily_collection = DailyCollection::findOrFail($pump_operator_id);
        $pump_operator = PumpOperator::findOrFail($daily_collection->pump_operator_id);
        $business_details = Business::where('id', $pump_operator->business_id)->first();

        return view('petro::daily_collection.partials.print')->with(compact('pump_operator', 'business_details', 'daily_collection'));
    }

    /**
     * get Balance Collection for pump operator
     * @return Response
     */
    public function getBalanceCollection($pump_operator_id)
    {
        $business_id = request()->session()->get('business.id');

        $balance_collection = DailyCollection::where('business_id', $business_id)->where('pump_operator_id', $pump_operator_id)->sum('current_amount');
        $settlement_collection = DailyCollection::where('business_id', $business_id)->where('pump_operator_id', $pump_operator_id)->sum('balance_collection');

        return ['balance_collection' => $balance_collection - $settlement_collection];
    }
}
