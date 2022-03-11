<?php

namespace Modules\Visitor\Http\Controllers;

use App\Business;
use App\System;
use App\Utils\BusinessUtil;
use App\Utils\ModuleUtil;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Visitor\Entities\VisitorSettings;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class VisitorSettingController extends Controller
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
    public function index(Request $request)
    {
        $business_id = $request->session()->get('business.id');
        if (!$this->moduleUtil->hasThePermissionInSubscription($business_id, 'visitors_registration_setting')) {
            abort(403, 'Unauthorized action.');
        }
        $user_id = Auth::user()->id;
        $data['business_id'] = $business_id;
        $settings = VisitorSettings::where($data)->first();

        $permissions['visitors_registration_setting'] = $this->moduleUtil->hasThePermissionInSubscription($business_id, 'visitors_registration_setting');
        $permissions['visitors_district'] = $this->moduleUtil->hasThePermissionInSubscription($business_id, 'visitors_district');
        $permissions['visitors_town'] = $this->moduleUtil->hasThePermissionInSubscription($business_id, 'visitors_town');

        return view('visitor::settings.index')->with(compact('settings', 'permissions'));
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create()
    {
        //not in use
    }

    /**
     * Store a newly created resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function store(Request $request)
    {

        $business_id = $request->session()->get('business.id');
        if (!$this->moduleUtil->hasThePermissionInSubscription($business_id, 'visitors_registration_setting')) {
            abort(403, 'Unauthorized action.');
        }
        $user_id = Auth::user()->id;
        try {
            $data = $request->except('_token', 'is_superadmin', 'admin_msg_visitor_qr', 'visitor_site_url', 'visitor_site_name', 'visitor_code_color', 'show_referral_code');
            $data = array_filter($data);
            $data['enable_required_name'] = (isset($data['enable_required_name'])) ? 1 : 0;
            $data['enable_required_district'] = (isset($data['enable_required_district'])) ? 1 : 0;
            $data['enable_add_district'] = (isset($data['enable_add_district'])) ? 1 : 0;
            $data['enable_required_address'] = (isset($data['enable_required_address'])) ? 1 : 0;
            $data['enable_required_town'] = (isset($data['enable_required_town'])) ? 1 : 0;
            $data['enable_add_town'] = (isset($data['enable_add_town'])) ? 1 : 0;
            if ($request->is_superadmin) {
                $businesses = Business::get();

                foreach ($businesses as $value) {
                    $data['business_id'] = $value->id;
                    $data['created_by'] = $user_id;
                    if ($setting = VisitorSettings::where('business_id', $value->id)->first()) {
                        VisitorSettings::where('id', $setting->id)->update($data);
                        $output = [
                            'success' => true,
                            'msg' => __('visitor::lang.setting_visitor_update_success')
                        ];
                    } else {
                        VisitorSettings::create($data);
                        $output = [
                            'success' => true,
                            'msg' => __('visitor::lang.setting_visitor_create_success')
                        ];
                    }
                }

                $admin_msg_visitor_qr = $request->admin_msg_visitor_qr;
                $visitor_site_url = $request->visitor_site_url;
                $visitor_site_name = $request->visitor_site_name;
                $visitor_code_color = $request->visitor_code_color;
                $show_referral_code = !empty($request->show_referral_code) ? 1 : 0;
                System::where('key', 'admin_msg_visitor_qr')->update(['value' => $admin_msg_visitor_qr]);
                System::where('key', 'visitor_site_url')->update(['value' => $visitor_site_url]);
                System::where('key', 'visitor_site_name')->update(['value' => $visitor_site_name]);
                System::where('key', 'visitor_code_color')->update(['value' => $visitor_code_color]);
                System::updateOrCreate(['key' => 'show_referral_code'], ['value' => $show_referral_code]);
            }
            $data['business_id'] = $business_id;
            $data['created_by'] = $user_id;
            if ($setting = VisitorSettings::where('business_id', $business_id)->first()) {
                VisitorSettings::where('id', $setting->id)->update($data);
                $output = [
                    'success' => true,
                    'msg' => __('visitor::lang.setting_visitor_update_success')
                ];
            } else {
                VisitorSettings::create($data);
                $output = [
                    'success' => true,
                    'msg' => __('visitor::lang.setting_visitor_create_success')
                ];
            }
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
     * @return Response
     */
    public function show()
    {
        return view('member::settings.show');
    }

    /**
     * Show the form for editing the specified resource.
     * @return Response
     */
    public function edit()
    {
        return view('member::settings.edit');
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
    public function destroy()
    {
    }
}
