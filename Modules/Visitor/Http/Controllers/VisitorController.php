<?php

namespace Modules\Visitor\Http\Controllers;

use App\Business;
use App\BusinessLocation;
use Modules\Visitor\Entities\Visitor;
use Modules\Visitor\Entities\VisitorSettings;
use App\Districts;
use App\Towns;
use App\System;
use App\Utils\BusinessUtil;
use App\Utils\ModuleUtil;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Modules\Visitor\Entities\Visit;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Str;

class VisitorController extends Controller
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
     * @return Response
     */
    public function index()
    {
        $business_id = request()->session()->get('business.id');
        if (!$this->moduleUtil->hasThePermissionInSubscription($business_id, 'visitors')) {
            abort(403, 'Unauthorized action.');
        }
        if (request()->ajax()) {
            $visitors = Visit::leftjoin('visitors', 'visits.visitor_id', 'visitors.id')
                ->leftjoin('business', 'visits.business_id', 'business.id')
                ->leftjoin('districts', 'districts.id', 'visitors.district_id')
                ->leftjoin('towns', 'towns.id', 'visitors.town_id')
                ->where('visits.business_id', $business_id)
                ->select([
                    'visits.*',
                    'visitors.*',
                    'business.name as business_name',
                    'districts.name as district',
                    'towns.name as town',
                ]);
            if (!empty(request()->town)) {
                $visitors->where('visitors.town_id', request()->town);
            }
            if (!empty(request()->district)) {
                $visitors->where('visitors.district_id', request()->district);
            }
            if (!empty(request()->mobile_number)) {
                $visitors->where('visitors.id', request()->mobile_number);
            }
            if (!empty(request()->start_date) && !empty(request()->end_date)) {
                $visitors->whereDate('visits.visited_date', '>=', request()->start_date);
                $visitors->whereDate('visits.visited_date', '<=', request()->end_date);
            }
            return DataTables::of($visitors)
                ->addColumn(
                    'action',
                    '<button data-href="{{action(\'\Modules\Visitor\Http\Controllers\VisitorController@edit\',[$id])}}" data-container=".visitor_model" class="btn btn-xs btn-primary btn-modal"><i class="glyphicon glyphicon-edit"></i> @lang("messages.edit")</button>
                    <button data-href="{{action(\'\Modules\Visitor\Http\Controllers\VisitorController@destroy\',[$id])}}" class="btn btn-xs btn-danger delete_visitor"><i class="fa fa-trash "></i> @lang("account.delete")</button>'
                )
                ->editColumn('gender', '{{ucfirst($gender)}}')
                ->editColumn('logged_in_time', '@if(!empty($logged_in_time)){{@format_datetime($logged_in_time)}}@endif')
                ->editColumn('logged_out_time', '@if(!empty($logged_out_time)){{@format_datetime($logged_out_time)}}@endif')
                ->removeColumn('id')
                ->rawColumns(['action'])
                ->make(true);
        }
        $towns = Towns::pluck('name', 'id');
        $districts = Districts::pluck('name', 'id');
        $mobile_numbers = Visit::leftjoin('visitors', 'visits.visitor_id', 'visitors.id')->where('visits.business_id', $business_id)->pluck('visitors.mobile_number', 'visitors.id');
        return view('visitor::visitor.index')->with(compact(
            'towns',
            'mobile_numbers',
            'districts',
        ));
    }
    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create(Request $request)
    {
        $business_id = $request->session()->get('business.id');
        if (!$this->moduleUtil->hasThePermissionInSubscription($business_id, 'visitors')) {
            abort(403, 'Unauthorized action.');
        }
        $visitor_count = Visitor::count() + 1;
        $visitor_setting = System::where('key', 'visitor_business_name')->first();
        $visitor_username = $visitor_setting->value . $visitor_count;
        $districts = Districts::pluck('name', 'id');
        $towns = Towns::pluck('name', 'id');
        $data['business_id'] = $business_id;
        $settings = VisitorSettings::where($data)->first();
        return view('visitor::visitor.create')->with(compact(
            'districts',
            'towns',
            'visitor_count',
            'visitor_username',
            'settings'
        ));
    }
    /**
     * Store a newly created resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        $business_id = $request->session()->get('business.id');
        $business = Business::findOrFail($business_id);
        try {
            $validator = Validator::make($request->all(), [
                'mobile_number' => 'required|numeric|unique:visitors'
            ]);

            if ($validator->fails()) {
                $output = [
                    'success' => 0,
                    'msg' => $validator->errors()->all()[0]
                ];
                return redirect()->back()->with('status', $output);
            }
            DB::beginTransaction();
            $visitor = Visitor::where('mobile_number', $request->mobile_number)->first();
            if (empty($visitor)) {

                $data = [];
                $data['mobile_number'] = $request->mobile_number;
                $data['gender'] = $request->gender;
                $data['name'] = $request->name;
                $data['address'] = $request->address;
                $data['district_id'] = $request->district_id;
                $data['town_id'] = $request->town_id;
                $data['username'] = $request->username;
                $data['details'] = $request->details ?? null;
                $data['password'] = Hash::make($request->password);


                $visitor = Visitor::create($data);
            }

            $visit_data = [];
            $visit_data['visitor_id'] = $visitor->id;
            $visit_data['business_id'] = $business_id;
            $visit_data['no_of_accompanied'] = !empty($request->no_of_accompanied) ? $request->no_of_accompanied : 0;
            $visit_data['visited_date'] = !empty($data['visited_date']) ? date('Y-m-d', strtotime($data['visited_date'])) : date('Y-m-d');
            $visit_data['date_and_time'] = Carbon::now()->format('Y-m-d H:i:s');

            $visit = Visit::create($visit_data);

            $company_number = $business->company_number;
            $visitor_count = Visit::where('business_id', $business_id)->count() + 1;
            $unique_code = $company_number . '-' . $visitor_count;

            $visit->unique_code = $unique_code;
            $visit->save();

            DB::commit();
            $output = [
                'success' => true,
                'msg' => __('visitor::lang.details_added_success'),
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
     * Show the form for creating a new resource.
     * @return Response
     */
    public function createVisitor($business_id, $location_id)
    {
        $visitor_count = Visitor::count() + 1;
        $visitor_setting = System::where('key', 'visitor_business_name')->first();
        $visitor_username = $visitor_setting->value . '_' . $visitor_count;

        $districts = Districts::pluck('name', 'id');
        $towns = Towns::pluck('name', 'id');
        $data['business_id'] = $business_id;
        $settings = VisitorSettings::where($data)->first();
        $business = Business::findOrFail($business_id);

        return view('visitor::visitor_qr.create')->with(compact(
            'districts',
            'towns',
            'visitor_count',
            'visitor_username',
            'settings',
            'business'
        ));
    }


    /**
     * Store a newly created resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function saveVisitor(Request $request)
    {
        $business_id = $request->business_id;
        $business = Business::where('id', $business_id)->first();
        try {
            $validator = Validator::make($request->all(), [
                'mobile_number' => 'required|numeric',
                'business_id' => 'required'
            ]);

            if ($validator->fails()) {
                $output = [
                    'success' => 0,
                    'msg' => $validator->errors()->all()[0]
                ];
                return redirect()->back()->with('status', $output);
            }
            DB::beginTransaction();
            $visitor = Visitor::where('mobile_number', $request->mobile_number)->first();
            if (empty($visitor)) {

                $data = [];
                $data['mobile_number'] = $request->mobile_number;
                $data['name'] = $request->name;
                $data['address'] = $request->address;
                $data['district_id'] = $request->district_id;
                $data['town_id'] = $request->town_id;
                $data['username'] = $request->username;
                $data['details'] = $request->details ?? null;
                $data['password'] = Hash::make($request->password);
                $data['unique_code'] = Str::random(5);

                $visitor = Visitor::create($data);
            }

            //update log out time for previous visit if not updated
            Visit::where('visitor_id', $visitor->id)->whereNull('logged_out_time')->update(['logged_out_time' => Carbon::now()->format('Y-m-d H:i:s')]);

            $visit_data = [];
            $visit_data['visitor_id'] = $visitor->id;
            $visit_data['business_id'] = $business_id;
            $visit_data['no_of_accompanied'] = !empty($request->no_of_accompanied) ? $request->no_of_accompanied : 0;
            $visit_data['visited_date'] = !empty($data['visited_date']) ? date('Y-m-d', strtotime($data['visited_date'])) : date('Y-m-d');
            $visit_data['date_and_time'] = Carbon::now()->format('Y-m-d H:i:s');
            $visit_data['logged_in_time'] = Carbon::now()->format('Y-m-d H:i:s');

            $visit = Visit::create($visit_data);


            $site_url = System::where('key', 'visitor_site_url')->first()->value;
            $site_name = System::where('key', 'visitor_site_name')->first()->value;
            $visitor_code_color = System::where('key', 'visitor_code_color')->first()->value;

            $company_number = $business->company_number;
            $visitor_count = Visit::where('business_id', $business_id)->count() + 1;
            $unique_code = $company_number . '-' . $visitor_count;

            $visit->unique_code = $unique_code;
            $visit->save();

            DB::commit();
            $output = [
                'success' => true,
                'msg' => __('visitor::lang.details_added_success'),
            ];
        } catch (\Exception $e) {
            Log::emergency('File: ' . $e->getFile() . 'Line: ' . $e->getLine() . 'Message: ' . $e->getMessage());
            $output = [
                'success' => false,
                'msg' => __('messages.something_went_wrong')
            ];

            return redirect()->back()->with('status', $output);
        }
        return view('visitor::visitor_qr.show_reponse')->with(compact(
            'business',
            'visitor',
            'site_url',
            'site_name',
            'visitor_code_color',
            'unique_code'
        ));
    }
    /**
     * Show the specified resource.
     * @return Response
     */
    public function show($id)
    {
        //
    }
    /**
     * Show the form for editing the specified resource.
     * @return Response
     */
    public function edit($id)
    {
        $visitor = Visitor::findOrFail($id);
        $districts = Districts::pluck('name', 'id');
        $towns = Towns::where('district_id', $visitor->district_id)->pluck('name', 'id');
        return view('visitor::visitor.edit')->with(compact('visitor', 'districts', 'towns'));
    }
    /**
     * Update the specified resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function update(Request $request, $id)
    {
        $business_id = $request->session()->get('business.id');
        try {
            if (isset($request->district_name)) {
                $district = Districts::where('name', $request->district_name)->first();
                if (!$district) {
                    $input['business_id'] = $business_id;
                    $input['created_by'] = Auth::user()->id;
                    $input['name'] = $request->district_name;
                    $district = Districts::create($input);
                }
            }
            if (isset($request->town_name)) {
                $towns = Towns::where('name', $request->town_name)->first();
                if (!$towns) {
                    $input['business_id'] = $business_id;
                    $input['created_by'] = Auth::user()->id;
                    $input['name'] = $request->town_name;
                    $input['district_id'] = (isset($district)) ? $district->id : $request->districts;
                    $towns = Towns::create($input);
                }
            }
            $data = $request->except('_token', 'districts', 'town', '_method');
            if (isset($data['town_name'])) {
                unset($data['town_name']);
            }
            if (isset($data['district_name'])) {
                unset($data['district_name']);
            }
            $data = array_filter($data);
            $data['district_id'] = (isset($district)) ? $district->id : $request->districts;
            $data['town_id'] = (isset($towns)) ? $towns->id : $request->town;
            $data['business_id'] = $business_id;
            $data['date_and_time'] = !empty($data['date_and_time']) ? date('Y-m-d H:i:s', strtotime($data['date_and_time'])) : date('Y-m-d h:i:sa');
            $data['visited_date'] = !empty($data['visited_date']) ? date('Y-m-d', strtotime($data['visited_date'])) : date('Y-m-d');
            Visitor::where('id', $id)->update($data);
            $output = [
                'success' => true,
                'msg' => __('visitor::lang.visitor_update_success')
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
     * @return Response
     */
    public function destroy($id)
    {
        try {
            Visitor::where('id', $id)->delete();
            Visit::where('visitor_id', $id)->delete();
            $output = [
                'success' => true,
                'msg' => __('visitor::lang.visitor_delete_success')
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


    public function generateQr()
    {
        $business_id = request()->session()->get('user.business_id');

        $business_id = request()->session()->get('user.business_id');
        $visitor_qr_data = Business::where('id', $business_id)->first()->visitor_qr_data;
        $business_locations = BusinessLocation::forDropdown($business_id);

        return view('visitor::visitor_qr.generate_qr')
            ->with(compact('business_locations', 'visitor_qr_data'));
    }


    public function getDetailIfRegistered(Request $request)
    {
        $mobile_number = $request->mobile_number;

        $visitor = Visitor::where('mobile_number', $mobile_number)->first();

        if (!empty($visitor)) {
            $visitor_details = [
                'name' => $visitor->name,
                'address' => $visitor->address,
                'district_id' => $visitor->district_id,
                'town_id' => $visitor->town_id,
                'land_number' => $visitor->land_number,
                'no_of_accompanied' => 0,
            ];

            return ['success' => 1, 'details' => $visitor_details];
        }
        return ['success' => 0, 'details' => null];
    }

    public function saveQr(Request $request)
    {
        $visitor_qr_data = $request->visitor_qr_data;
        $business_id = request()->session()->get('user.business_id');
        Business::where('id', $business_id)->update(['visitor_qr_data' => $visitor_qr_data]);

        return true;
    }
}
