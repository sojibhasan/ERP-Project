<?php

namespace Modules\HR\Http\Controllers;

use App\Business;
use App\TaxRate;
use App\Utils\BusinessUtil;
use App\Utils\ModuleUtil;
use App\Utils\RestaurantUtil;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Modules\HR\Entities\HrPrefix;
use Modules\HR\Entities\HrSetting;
use Modules\HR\Entities\Tax;
use Modules\HR\Entities\WorkingDay;

class HrSettingsController extends Controller
{
    /**
     * All Utils instance.
     *
     */
    protected $businessUtil;
    protected $restaurantUtil;
    protected $moduleUtil;
    protected $mailDrivers;

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
        $settings = HrSetting::where('business_id', $business_id)->first();
        $working_days = WorkingDay::where('business_id', $business_id)->get();
        $prefixes = HrPrefix::where('business_id', $business_id)->first();
        $taxes = Tax::where('business_id', $business_id)->get();

        $permissions['hr_module'] = $this->moduleUtil->hasThePermissionInSubscription($business_id, 'hr_module');
        $permissions['department'] = $this->moduleUtil->hasThePermissionInSubscription($business_id, 'department');
        $permissions['jobtitle'] = $this->moduleUtil->hasThePermissionInSubscription($business_id, 'jobtitle');
        $permissions['jobcategory'] = $this->moduleUtil->hasThePermissionInSubscription($business_id, 'jobcategory');
        $permissions['workingdays'] = $this->moduleUtil->hasThePermissionInSubscription($business_id, 'workingdays');
        $permissions['workshift'] = $this->moduleUtil->hasThePermissionInSubscription($business_id, 'workshift');
        $permissions['holidays'] = $this->moduleUtil->hasThePermissionInSubscription($business_id, 'holidays');
        $permissions['leave_type'] = $this->moduleUtil->hasThePermissionInSubscription($business_id, 'leave_type');
        $permissions['salary_grade'] = $this->moduleUtil->hasThePermissionInSubscription($business_id, 'salary_grade');
        $permissions['employment_status'] = $this->moduleUtil->hasThePermissionInSubscription($business_id, 'employment_status');
        $permissions['salary_component'] = $this->moduleUtil->hasThePermissionInSubscription($business_id, 'salary_component');
        $permissions['hr_prefix'] = $this->moduleUtil->hasThePermissionInSubscription($business_id, 'hr_prefix');
        $permissions['hr_tax'] = $this->moduleUtil->hasThePermissionInSubscription($business_id, 'hr_tax');
        $permissions['religion'] = $this->moduleUtil->hasThePermissionInSubscription($business_id, 'religion');
        $permissions['hr_setting_page'] = $this->moduleUtil->hasThePermissionInSubscription($business_id, 'hr_setting_page');

        return view('hr::settings.index')->with(compact(
            'settings',
            'working_days',
            'taxes',
            'permissions',
            'prefixes'
        ));
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create()
    {
        return view('hr::create');
    }

    /**
     * Store a newly created resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        $business_id = $request->session()->get('business.id');
        try {
            $input = $request->except('_token');
            HrSetting::updateOrCreate(['business_id' => $business_id], $input);

            $output = [
                'success' => true,
                'tab' => 'others',
                'msg' => __('hr::lang.success')
            ];
        } catch (\Exception $e) {
            Log::emergency('File: ' . $e->getFile() . 'Line: ' . $e->getLine() . 'Message: ' . $e->getMessage());
            $output = [
                'success' => false,
                'tab' => 'others',
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
        return view('hr::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @return Response
     */
    public function edit()
    {
        return view('hr::edit');
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
