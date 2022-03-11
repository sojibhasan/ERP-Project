<?php

namespace Modules\Visitor\Http\Controllers;

use App\Business;
use App\BusinessLocation;
use App\Districts;
use App\System;
use App\Towns;
use App\Utils\BusinessUtil;
use App\Utils\ModuleUtil;
use Carbon\Carbon;
use Modules\Visitor\Entities\Visitor;
use Modules\Visitor\Entities\VisitorSettings;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Modules\Visitor\Entities\Visit;
use Illuminate\Support\Str;

class VisitorRegistrationController extends Controller
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
     * @return Renderable
     */
    public function index()
    {
        return view('visitor::index');
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        $business_id = request()->session()->get('business.id');
        $business = Business::find($business_id);
        if (!$this->moduleUtil->hasThePermissionInSubscription($business_id, 'visitors_registration')) {
            abort(403, 'Unauthorized action.');
        }
        $visitor_count = Visitor::count() + 1;
        $visitor_setting = System::where('key', 'visitor_business_name')->first();
        $visitor_username = $visitor_setting->value . '_' . $visitor_count;

        $districts = Districts::pluck('name', 'id');
        $towns = Towns::pluck('name', 'id');
        $data['business_id'] = $business_id;
        $settings = VisitorSettings::where($data)->first();
        return view('visitor::visitor_registration.create')->with(compact(
            'business',
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
        $business = Business::where('id', $business_id)->first();
        try {
            $validator = Validator::make($request->all(), [
                'mobile_number' => 'required',
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
                $validator = Validator::make($request->all(), [
                    'username' => 'required|unique:visitors',
                    'mobile_number' => 'required|unique:visitors',
                    'password' => 'required|min:4|max:255',
                    'confirm_password' => 'required|same:password'
                ]);

                if ($validator->fails()) {
                    $output = [
                        'success' => 0,
                        'msg' => $validator->errors()->all()[0]
                    ];
                    return redirect()->back()->with('status', $output);
                }

                $data = [];
                $data['mobile_number'] = $request->mobile_number;
                $data['name'] = $request->name;
                $data['address'] = $request->address;
                $data['land_number'] = $request->land_number;
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
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function selfRegistration(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'username' => 'required|unique:visitors',
                'mobile_number' => 'required|unique:visitors',
                'password' => 'required|min:4|max:255',
                'confirm_password' => 'required|same:password'
            ]);

            if ($validator->fails()) {
                $output = [
                    'success' => 0,
                    'msg' => $validator->errors()->all()[0]
                ];

                return redirect()->back()->with('status', $output);
            }

            $data = $request->except('_token', 'confirm_password');
            $data['unique_code'] = Str::random(5);
            $data['password'] = Hash::make($data['password']);
            $visitor =  Visitor::create($data);

            //Module function to be called after after visitor is created
            if (config('app.env') != 'demo') {
                $this->moduleUtil->getModuleData('after_visitor_created', ['visitor' => $visitor]);
            }
            $welcom_msg = System::getProperty('welcome_msg_body');
            $welcom_msg = str_replace('{name}', $visitor->name, $welcom_msg);
          
            $output = [
                'success' => true,
                'welcom_msg' => $welcom_msg,
                'msg' => __('visitor::lang.visitor_regsiter_success')
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
        return view('visitor::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        return view('visitor::edit');
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
