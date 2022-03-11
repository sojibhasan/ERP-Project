<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\SiteSettings;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Currency;
use App\System;
use Illuminate\Support\Facades\Log;
use Intervention\Image\Facades\Image;

class SiteSettingController extends Controller
{
    public function viewPage()
    {
        if (!auth()->user()->can('superadmin')) {
            abort(403, 'Unauthorized action.');
        }

        $settings = SiteSettings::where('id', 1)->first();

        if (!empty($settings)) {
            return view('system_admin.index', compact('settings'));
        } else {
            return view('system_admin.index');
        }
    }

    public function updateSettings(Request $request)
    {
        if (!auth()->user()->can('sys_admin_settings.update')) {
            abort(403, 'Unauthorized action.');
        }
        try {
            $upload_image_quality = (int) System::getProperty('upload_image_quality');

            if (!file_exists('./public/img/setting')) {
                mkdir('./public/img/setting', 0777, true);
            }
            if ($request->hasfile('uploadFileFicon')) {
                $file = $request->file('uploadFileFicon');
                $extension = $file->getClientOriginalExtension();
                $filename = time() . '.' . $extension;
                $img = Image::make($file->getRealPath())->save('public/img/setting/' . $filename, $upload_image_quality);
                $uploadFileFicon = 'public/img/setting/' . $filename;
            } else {
                $uploadFileFicon = '';
            }

            if ($request->hasfile('uploadFileLBackground')) {
                $file = $request->file('uploadFileLBackground');
                $extension = $file->getClientOriginalExtension();
                $filename = time() . '.' . $extension;
                $img = Image::make($file->getRealPath())->save('public/img/setting/' . $filename, $upload_image_quality);
                $uploadFileLBackground = 'public/img/setting/' . $filename;
            } else {
                $uploadFileLBackground = '';
            }
            if ($request->hasfile('uploadFileLLogo')) {
                $file = $request->file('uploadFileLLogo');
                $extension = $file->getClientOriginalExtension();
                $filename = time() . '.' . $extension;
                $img = Image::make($file->getRealPath())->save('public/img/setting/' . $filename, $upload_image_quality);
                $uploadFileLLogo = 'public/img/setting/' . $filename;
            } else {
                $uploadFileLLogo = '';
            }
            if ($request->tc_sale_and_pos == '') {
                $tc_sale_and_pos = '0';
            } else {
                $tc_sale_and_pos = '1';
            }

            $request_data = array(
                'logingLogo_width' => $request->logingLogo_width,
                'logingLogo_height' => $request->logingLogo_height,
                'uploadFileFicon' => $uploadFileFicon,
                'uploadFileLBackground' => $uploadFileLBackground,
                'uploadFileLLogo' => $uploadFileLLogo,
                'login_background_color' => $request->login_background_color,
                'login_box_color' => $request->login_box_color,
                'topBelt_background_color' => $request->topBelt_background_color,
                'background_showing_type' => $request->background_showing_type,
                'sales_agents_registration' => !empty($request->sales_agents_registration) ? $request->sales_agents_registration : '0',
                'main_module_color' => $request->main_module_color,
                'sub_module_color' => $request->sub_module_color,
                'sub_module_bg_color' => $request->sub_module_bg_color,
                'ls_side_menu_bg_color' => $request->ls_side_menu_bg_color,
                'ls_side_menu_font_color' => $request->ls_side_menu_font_color,
                'show_messages' => json_encode($request->show_messages),
                'login_page_title' => $request->login_page_title,
                'login_page_footer' => $request->login_page_footer,
                'login_page_description' => $request->login_page_description,
                'login_page_general_message' => $request->login_page_general_message,
                'system_expired_message' => $request->system_expired_message,
                'invoice_footer' => $request->invoice_footer,
                'tc_sale_and_pos' => $tc_sale_and_pos,
                'register_now_btn_bg' => $request->register_now_btn_bg,
                'customer_register_btn_bg' => $request->customer_register_btn_bg,
                'member_register_btn_bg' => $request->member_register_btn_bg,
                'pricing_btn_bg' => $request->pricing_btn_bg,
                'member_register_bg' => $request->member_register_bg,
                'self_register_bg' => $request->self_register_bg,
                'admin_login_bg' => $request->admin_login_bg,
                'customer_login_bg' => $request->customer_login_bg,
                'member_login_bg' => $request->member_login_bg,
                'employee_login_bg' => $request->employee_login_bg,
                'visitor_login_bg' => $request->visitor_login_bg,
                'captch_site_key' => $request->captch_site_key,
            );

            $filter_data = array_filter($request_data, function ($value) {
                return $value != '';
            });
            $settings = SiteSettings::updateOrCreate(['id' => 1], $filter_data);

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

    public function help()
    {
        if (!auth()->user()->can('sys_admin_settings.view')) {
            abort(403, 'Unauthorized action.');
        }

        return view('system_admin.help');
    }

    public function helpUpdate(Request $request)
    {
        if (!auth()->user()->can('sys_admin_settings.view')) {
            abort(403, 'Unauthorized action.');
        }

        DB::table('site_settings')->where('id', 1)->update(['tour_toggle' => $request->tour_toggle]);

        return redirect()->back();
    }

    public function getCurrencyCode(Request $request)
    {
        $c_id = $request->currency_id;
        $currency = Currency::find($c_id);

        return $currency;
    }
}
