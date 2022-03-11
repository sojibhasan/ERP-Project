<?php

namespace Modules\MPCS\Http\Controllers;

use App\BusinessLocation;
use App\Category;
use App\Employee;
use App\MergedSubCategory;
use App\Utils\ModuleUtil;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Modules\MPCS\Entities\Form9cSubCategory;
use Modules\MPCS\Entities\FormOpeningValue;
use Modules\MPCS\Entities\MpcsFormSetting;

class FormsSettingController extends Controller
{
    protected $moduleUtil;

    /**
     * Constructor
     *
     * @param ProductUtils $product
     * @return void
     */
    public function __construct(ModuleUtil $moduleUtil)
    {
        $this->moduleUtil = $moduleUtil;
    }

    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        $business_id = request()->session()->get('business.id');
        $business_locations = BusinessLocation::forDropdown($business_id);

        $settings = MpcsFormSetting::where('business_id', $business_id)->first();

        $mpcs_form_settings_permission = $this->moduleUtil->hasThePermissionInSubscription($business_id, 'mpcs_form_settings');
        $list_opening_values_permission = $this->moduleUtil->hasThePermissionInSubscription($business_id, 'list_opening_values');

        return view('mpcs::forms_setting.index')->with(compact('business_locations', 'settings', 'mpcs_form_settings_permission', 'list_opening_values_permission'));
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create()
    {
        return view('mpcs::create');
    }

    /**
     * Store a newly created resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        try {
            $business_id = request()->session()->get('business.id');
            $input = $request->except('_token');
            $input['business_id'] =  $business_id;


            $input['F9C_tdate'] = !empty($request->F_9_C_tdate) ? Carbon::parse($request->F_9_C_tdate)->format('Y-m-d') : null;
            $input['F159ABC_form_tdate'] = !empty($request->F159ABC_form_tdate) ? Carbon::parse($request->F159ABC_form_tdate)->format('Y-m-d') : null;
            $input['F16A_form_tdate'] = !empty($request->F16A_form_tdate) ? Carbon::parse($request->F16A_form_tdate)->format('Y-m-d') : null;
            $input['F21C_form_tdate'] = !empty($request->F21C_form_tdate) ? Carbon::parse($request->F21C_form_tdate)->format('Y-m-d') : null;
            $input['F14_form_tdate'] = !empty($request->F14_form_tdate) ? Carbon::parse($request->F14_form_tdate)->format('Y-m-d') : null;
            $input['F17_form_tdate'] = !empty($request->F17_form_tdate) ? Carbon::parse($request->F17_form_tdate)->format('Y-m-d') : null;
            $input['F20_form_tdate'] = !empty($request->F20_form_tdate) ? Carbon::parse($request->F20_form_tdate)->format('Y-m-d') : null;
            $input['F21_form_tdate'] = !empty($request->F21_form_tdate) ? Carbon::parse($request->F21_form_tdate)->format('Y-m-d') : null;
            $input['F22_form_tdate'] = !empty($request->F22_form_tdate) ? Carbon::parse($request->F22_form_tdate)->format('Y-m-d') : null;


            MpcsFormSetting::updateOrCreate(['business_id' => $business_id], $input);

            $output = [
                'success' => true,
                'msg' => __('mpcs::lang.settings_update_success')
            ];
        } catch (\Exception $e) {
            \Log::emergency('File: ' . $e->getFile() . 'Line: ' . $e->getLine() . 'Message: ' . $e->getMessage());
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
        return view('mpcs::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @return Response
     */
    public function edit()
    {
        return view('mpcs::edit');
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


    public function getForm9CSetting()
    {
        $business_id = request()->session()->get('business.id');
        $sub_categories = Category::where('business_id', $business_id)->where('parent_id', '!=', 0)->get();
        $settings = MpcsFormSetting::where('business_id', $business_id)->first();
        $months = MpcsFormSetting::getMonthArray();

        return view('mpcs::forms_setting.partials.form_9c_modal')->with(compact('sub_categories', 'settings', 'business_id', 'months'));
    }
    public function postForm9CSetting(Request $request)
    {
        try {
            $business_id = request()->session()->get('business.id');
            $data = array(
                'F9C_sn' => $request->F9C_setting_sn,
                'F9C_tdate' => !empty($request->F9C_setting_tdate) ? Carbon::parse($request->F9C_setting_tdate)->format('Y-m-d') : null,
                'F9C_first_day_after_stock_taking' => !empty($request->F9C_first_day_after_stock_taking) ? 1 : 0,
                'F9C_first_day_of_next_month' => !empty($request->F9C_first_day_of_next_month) ? 1 : 0,
                'F9C_first_day_of_next_month_selected' => !empty($request->F9C_first_day_of_next_month_selected) ? $request->F9C_first_day_of_next_month_selected : null
            );
            $setting = MpcsFormSetting::where('business_id', $business_id)->update($data);
            $save_sub_cat_data = [];
            if (!empty($request->sub_cat_9c)) {
                foreach ($request->sub_cat_9c as $key => $item) {
                    $sub_cat_data = [
                        'business_id' => $business_id,
                        'sub_category_id' => $key,
                        'qty' => !empty($item['qty']) ? $item['qty'] : 0.00,
                        'amount' => !empty($item['amount']) ?  $item['amount'] : 0.00
                    ];
                    Form9cSubCategory::updateOrCreate(
                        ['business_id' => $business_id, 'sub_category_id' => $key],
                        $sub_cat_data
                    );
                    $save_sub_cat_data[] =  $sub_cat_data;
                }



                $data['cat_data'] =  $save_sub_cat_data;

                $form_id = MpcsFormSetting::where('business_id', $business_id)->first();
                FormOpeningValue::create([
                    'business_id' => $business_id,
                    'form_name' => '9C',
                    'form_id' => !empty($form_id) ? $form_id->id : 0,
                    'data' => $data,
                    'edited_by' => Auth::user()->id,
                    'date' => date('Y-m-d')
                ]);
            }

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

    public function getForm16ASetting()
    {
        $business_id = request()->session()->get('business.id');
        $months_numbers = MpcsFormSetting::getMonthArray();

        $setting = MpcsFormSetting::where('business_id', $business_id)->first();

        return view('mpcs::forms_setting.partials.form_16a_modal')->with(compact('months_numbers', 'setting'));
    }
    public function postForm16ASetting(Request $request)
    {
        try {
            $business_id = request()->session()->get('business.id');
            $data = array(
                'F16A_form_tdate' => !empty($request->F16A_form_tdate) ? Carbon::parse($request->F16A_form_tdate)->format('Y-m-d') : null,
                'F16A_form_sn' => $request->F16A_form_sn,
                'F16A_total_pp' => $request->F16A_total_pp,
                'F16A_total_sp' => $request->F16A_total_sp,
                'F16A_first_day_after_stock_taking' => !empty($request->F16A_first_day_after_stock_taking) ? $request->F16A_first_day_after_stock_taking : 0,
                'F16A_first_day_of_next_month' => !empty($request->F16A_first_day_of_next_month) ? $request->F16A_first_day_of_next_month : 0,
                'F16A_first_day_of_next_month_selected' => !empty($request->F16A_first_day_of_next_month_selected) ? $request->F16A_first_day_of_next_month_selected : null,
            );
            MpcsFormSetting::where('business_id', $business_id)->update($data);
            $form_id = MpcsFormSetting::where('business_id', $business_id)->first();
            FormOpeningValue::create([
                'business_id' => $business_id,
                'form_name' => 'F16A',
                'form_id' => !empty($form_id) ? $form_id->id : 0,
                'data' => $data,
                'edited_by' => Auth::user()->id,
                'date' => date('Y-m-d')
            ]);

            $output = [
                'success' => true,
                'msg' => __('lang_v1.success')
            ];
        } catch (\Exception $e) {
            \Log::emergency('File: ' . $e->getFile() . 'Line: ' . $e->getLine() . 'Message: ' . $e->getMessage());
            $output = [
                'success' => false,
                'msg' => __('messages.something_went_wrong')
            ];
        }

        return redirect()->back()->with('status', $output);
    }

    public function getFormF22Setting()
    {
        $business_id = request()->session()->get('business.id');
        $settings = MpcsFormSetting::where('business_id',  $business_id)->select('F22_no_of_product_per_page')->first();

        return view('mpcs::forms_setting.partials.form_f22_modal')->with(compact('settings'));
    }
    public function postFormF22Setting(Request $request)
    {
        $business_id = request()->session()->get('business.id');

        $input['F22_no_of_product_per_page'] = !empty($request->F22_no_of_product_per_page) ? $request->F22_no_of_product_per_page : null;

        MpcsFormSetting::updateOrCreate(['business_id' => $business_id], $input);

        $output = [
            'success' => 1,
            'msg' => __('mpcs::lang.setting_save_success')
        ];

        return redirect()->back()->with('status', $output);
    }
    public function getForm159ABCSetting()
    {
        $business_id = request()->session()->get('business.id');
        $months = MpcsFormSetting::getMonthArray();

        $setting = MpcsFormSetting::where('business_id', $business_id)->first();

        return view('mpcs::forms_setting.partials.form_15_9_abc_modal')->with(compact('months', 'setting'));
    }
    public function saveForm159ABCSetting(Request $request)
    {
        try {
            $business_id = request()->session()->get('business.id');
            $data = array(
                'F159ABC_form_tdate' => !empty($request->F159ABC_form_tdate) ? Carbon::parse($request->F159ABC_form_tdate)->format('Y-m-d') : null,
                'F159ABC_form_sn' => $request->F159ABC_form_sn,
                'F159ABC_first_day_after_stock_taking' => !empty($request->F159ABC_first_day_after_stock_taking) ? $request->F159ABC_first_day_after_stock_taking : 0,
                'F159ABC_first_day_of_next_month' => !empty($request->F159ABC_first_day_of_next_month) ? $request->F159ABC_first_day_of_next_month : 0,
                'F159ABC_first_day_of_next_month_selected' => !empty($request->F159ABC_first_day_of_next_month_selected) ? $request->F159ABC_first_day_of_next_month_selected : null,
            );
            MpcsFormSetting::where('business_id', $business_id)->update($data);

            $form_id = MpcsFormSetting::where('business_id', $business_id)->first();
            FormOpeningValue::create([
                'business_id' => $business_id,
                'form_name' => 'F159ABC',
                'form_id' => !empty($form_id) ? $form_id->id : 0,
                'data' => $data,
                'edited_by' => Auth::user()->id,
                'date' => date('Y-m-d')
            ]);

            $output = [
                'success' => true,
                'msg' => __('lang_v1.success')
            ];
        } catch (\Exception $e) {
            \Log::emergency('File: ' . $e->getFile() . 'Line: ' . $e->getLine() . 'Message: ' . $e->getMessage());
            $output = [
                'success' => false,
                'msg' => __('messages.something_went_wrong')
            ];
        }

        return redirect()->back()->with('status', $output);
    }

    public function getForm21CSetting()
    {
        $business_id = request()->session()->get('business.id');
        $merged_sub_categories = MergedSubCategory::where('business_id', $business_id)->get();
        $settings = MpcsFormSetting::where('business_id', $business_id)->first();
        $months = MpcsFormSetting::getMonthArray();

        return view('mpcs::forms_setting.partials.form_21c_modal')->with(compact('merged_sub_categories', 'settings', 'months'));
    }
    public function postForm21CSetting(Request $request)
    {
        try {
            $business_id = request()->session()->get('business.id');
            $data = array(
                'F21C_form_sn' => $request->F21C_form_sn,
                'F21C_form_tdate' => !empty($request->F21C_form_tdate) ? Carbon::parse($request->F21C_form_tdate)->format('Y-m-d') : null,
                'F21C_first_day_after_stock_taking' => !empty($request->F21C_first_day_after_stock_taking) ? 1 : 0,
                'F21C_first_day_of_next_month' => !empty($request->F21C_first_day_of_next_month) ? 1 : 0,
                'F21C_first_day_of_next_month_selected' => $request->F21C_first_day_of_next_month
            );

            $setting = MpcsFormSetting::where('business_id', $business_id)->update($data);

            $form_id = MpcsFormSetting::where('business_id', $business_id)->first();
            FormOpeningValue::create([
                'business_id' => $business_id,
                'form_name' => 'F21C',
                'form_id' => !empty($form_id) ? $form_id->id : 0,
                'data' => $data,
                'edited_by' => Auth::user()->id,
                'date' => date('Y-m-d')
            ]);

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
}
