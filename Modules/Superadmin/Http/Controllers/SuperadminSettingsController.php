<?php

namespace Modules\Superadmin\Http\Controllers;

use App\Account;
use App\Business;
use App\BusinessLocation;
use App\DefaultAccount;
use App\DefaultAccountType;
use App\Scopes\HrSettingScope;
use App\System;
use App\User;
use App\Utils\BusinessUtil;
use App\Utils\ModuleUtil;
use App\Utils\Util;
use Modules\Visitor\Entities\VisitorSettings;
use Illuminate\Http\Request;

use Illuminate\Http\Response;
use Modules\HR\Entities\HrPrefix;
use Modules\HR\Entities\HrSetting;
use Modules\HR\Entities\Tax;
use Modules\HR\Entities\WorkingDay;
use Intervention\Image\Facades\Image;
use Modules\Superadmin\Entities\TankDipChart;

class SuperadminSettingsController extends BaseController
{
    /**
     * All Utils instance.
     *
     */
    protected $businessUtil;
    protected $commonUtil;
    protected $mailDrivers;
    protected $backupDisk;

    public function __construct(BusinessUtil $businessUtil, Util $commonUtil, ModuleUtil $moduleUtil)
    {
        $this->businessUtil = $businessUtil;
        $this->commonUtil = $commonUtil;
        $this->moduleUtil = $moduleUtil;

        $this->mailDrivers = [
            'smtp' => 'SMTP',
            'sendmail' => 'Sendmail',
            'mailgun' => 'Mailgun',
            'mandrill' => 'Mandrill',
            'ses' => 'SES',
            'sparkpost' => 'Sparkpost'
        ];

        $this->backupDisk = ['local' => 'Local', 'dropbox' => 'Dropbox'];
    }

    /**
     * Show the form for editing the specified resource.
     * @return Response
     */
    public function edit()
    {
        if (!auth()->user()->can('superadmin')) {
            abort(403, 'Unauthorized action.');
        }

        $settings = System::pluck('value', 'key');
        $currencies = $this->businessUtil->allCurrencies();
        $business_id = request()->session()->get('business.id');
        $superadmin_version = System::getProperty('superadmin_version');
        $is_demo = env('APP_ENV') == 'demo' ? true : false;

        $default_values = [
            'APP_NAME' => env('APP_NAME'),
            'APP_TITLE' => env('APP_TITLE'),
            'APP_LOCALE' => env('APP_LOCALE'),
            'MAIL_DRIVER' => $is_demo ? null : env('MAIL_DRIVER'),
            'MAIL_HOST' => $is_demo ? null : env('MAIL_HOST'),
            'MAIL_PORT' => $is_demo ? null : env('MAIL_PORT'),
            'MAIL_USERNAME' => $is_demo ? null : env('MAIL_USERNAME'),
            'MAIL_PASSWORD' => $is_demo ? null : env('MAIL_PASSWORD'),
            'MAIL_ENCRYPTION' => $is_demo ? null : env('MAIL_ENCRYPTION'),
            'MAIL_FROM_ADDRESS' => $is_demo ? null : env('MAIL_FROM_ADDRESS'),
            'MAIL_FROM_NAME' => $is_demo ? null : env('MAIL_FROM_NAME'),
            'STRIPE_PUB_KEY' => $is_demo ? null : env('STRIPE_PUB_KEY'),
            'STRIPE_SECRET_KEY' => $is_demo ? null : env('STRIPE_SECRET_KEY'),
            'PAYPAL_MODE' => env('PAYPAL_MODE'),
            'PAYPAL_SANDBOX_API_USERNAME' => $is_demo ? null : env('PAYPAL_SANDBOX_API_USERNAME'),
            'PAYPAL_SANDBOX_API_PASSWORD' => $is_demo ? null : env('PAYPAL_SANDBOX_API_PASSWORD'),
            'PAYPAL_SANDBOX_API_SECRET' => $is_demo ? null : env('PAYPAL_SANDBOX_API_SECRET'),
            'PAYPAL_LIVE_API_USERNAME' => $is_demo ? null : env('PAYPAL_LIVE_API_USERNAME'),
            'PAYPAL_LIVE_API_PASSWORD' => $is_demo ? null : env('PAYPAL_LIVE_API_PASSWORD'),
            'PAYPAL_LIVE_API_SECRET' => $is_demo ? null : env('PAYPAL_LIVE_API_SECRET'),
            'BACKUP_DISK' => env('BACKUP_DISK'),
            'DROPBOX_ACCESS_TOKEN' => $is_demo ? null : env('DROPBOX_ACCESS_TOKEN'),
            'RAZORPAY_KEY_ID' => $is_demo ? null : env('RAZORPAY_KEY_ID'),
            'RAZORPAY_KEY_SECRET'  => $is_demo ? null : env('RAZORPAY_KEY_SECRET'),

            'PESAPAL_CONSUMER_KEY'  => $is_demo ? null : env('PESAPAL_CONSUMER_KEY'),
            'PESAPAL_CONSUMER_SECRET'  => $is_demo ? null : env('PESAPAL_CONSUMER_SECRET'),
            'PESAPAL_LIVE'  => $is_demo ? null : env('PESAPAL_LIVE'),

            'PAYHERE_MERCHANT_ID'  => $is_demo ? null : env('PAYHERE_MERCHANT_ID'),
            'PAYHERE_MERCHANT_SECRET'  => $is_demo ? null : env('PAYHERE_MERCHANT_SECRET'),
            'PAYHERE_LIVE'  => $is_demo ? null : env('PAYHERE_LIVE'),
            'PAY_ONLINE_STARTING_NO'  => $is_demo ? null : env('PAY_ONLINE_STARTING_NO'),
            'PAY_ONLINE_STARTING_NO'  => $is_demo ? null : env('PAY_ONLINE_STARTING_NO'),
            'PAY_ONLINE_BANK_NAME'  => $is_demo ? null : env('PAY_ONLINE_BANK_NAME'),
            'PAY_ONLINE_BRANCH_NAME'  => $is_demo ? null : env('PAY_ONLINE_BRANCH_NAME'),
            'PAY_ONLINE_ACCOUNT_NO'  => $is_demo ? null : env('PAY_ONLINE_ACCOUNT_NO'),
            'PAY_ONLINE_ACCOUNT_NAME'  => $is_demo ? null : env('PAY_ONLINE_ACCOUNT_NAME'),
            'PAY_ONLINE_SWIFT_CODE'  => $is_demo ? null : env('PAY_ONLINE_SWIFT_CODE')
        ];
        $mail_drivers = $this->mailDrivers;

        $config_languages = config('constants.langs');
        $languages = [];
        foreach ($config_languages as $key => $value) {
            $languages[$key] = $value['full_name'];
        }
        $backup_disk = $this->backupDisk;

        $cron_job_command = $this->businessUtil->getCronJobCommand();

        $default_account_types = DefaultAccountType::where('business_id', $business_id)
            ->whereNull('parent_account_type_id')
            ->with(['sub_types'])
            ->get();

        $asset_type_ids = json_encode(DefaultAccountType::getAccountTypeIdOfType('Assets', $business_id));

        $default_accounts = DefaultAccount::pluck('name', 'id');
        $payment_types = $this->commonUtil->payment_types();

        $prefixes = HrPrefix::withoutGlobalScope(HrSettingScope::class)->where('business_id', $business_id)->first();
        $taxes = Tax::withoutGlobalScope(HrSettingScope::class)->where('business_id', $business_id)->get();
        $working_days = WorkingDay::withoutGlobalScope(HrSettingScope::class)->where('business_id', $business_id)->where('is_superadmin_default', 1)->get();
        $businesses = Business::where('is_active', 1)->pluck('name', 'id'); 

        if ($working_days->count() == 0) {
            $days = array(
                'Saturday',
                'Sunday',
                'Monday',
                'Tuesday',
                'Wednesday',
                'Thursday',
                'Friday',
            );
            foreach ($days as $day) {
                WorkingDay::insert(['business_id' => $business_id, 'days' => $day, 'flag' => 0, 'is_superadmin_default' => 1]);
            }
        }
        $working_days =  WorkingDay::withoutGlobalScope(HrSettingScope::class)->where('business_id', $business_id)->where('is_superadmin_default', 1)->get();

        $permissions['visitors_registration_setting'] = $this->moduleUtil->hasThePermissionInSubscription($business_id, 'visitors_registration_setting');
        $permissions['visitors_district'] = $this->moduleUtil->hasThePermissionInSubscription($business_id, 'visitors_district');
        $permissions['visitors_town'] = $this->moduleUtil->hasThePermissionInSubscription($business_id, 'visitors_town');

        $visitor_settings = VisitorSettings::where('business_id', $business_id)->first();

        $sheet_names = TankDipChart::pluck('sheet_name', 'sheet_name');
        $tank_manufacturers = TankDipChart::pluck('tank_manufacturer', 'tank_manufacturer');
        $tank_capacitys = TankDipChart::pluck('tank_capacity', 'tank_capacity');

        return view('superadmin::superadmin_settings.edit')
            ->with(compact(
                'sheet_names',
                'tank_manufacturers',
                'tank_capacitys',
                'working_days',
                'businesses',
                'prefixes',
                'taxes',
                'settings',
                'visitor_settings',
                'currencies',
                'superadmin_version',
                'mail_drivers',
                'languages',
                'default_values',
                'backup_disk',
                'cron_job_command',
                'default_account_types',
                'asset_type_ids',
                'default_accounts',
                'payment_types',
                'permissions'
            ));
    }

    /**
     * Update the specified resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function update(Request $request)
    {
        if (!auth()->user()->can('superadmin')) {
            abort(403, 'Unauthorized action.');
        }

        try {

            //Disable .ENV settings in demo
            if (config('app.env') == 'demo') {
                $output = [
                    'success' => 0,
                    'msg' => 'Feature disabled in demo!!'
                ];
                return back()->with('status', $output);
            }

            $system_settings = $request->only([
                'customer_supplier_security_deposit_current_liability_font_size',
                'customer_supplier_security_deposit_current_liability_color',
                'customer_supplier_security_deposit_current_liability_message',
                'not_enalbed_module_user_font_size',
                'not_enalbed_module_user_color',
                'not_enalbed_module_user_message',
                'visitor_welcome_email_subject',
                'visitor_welcome_email_body',
                'customer_welcome_email_subject',
                'customer_welcome_email_body',
                'agent_welcome_email_subject',
                'agent_welcome_email_body',
                'new_subscription_email_subject',
                'new_subscription_email_subject_offline',
                'new_subscription_email_body_offline',
                'new_subscription_email_body',
                'company_starting_number',
                'upload_image_quality',
                'helpdesk_system_url',
                'create_individual_company_package',
                'business_or_entity',
                'company_number_prefix',
                'sms_on_password_change',
                'footer_top_margin',
                'admin_invoice_footer',
                'default_number_of_customers',
                'upload_image_width',
                'upload_image_height',
                'laboratory_prefix',
                'laboratory_code_start_from',
                'pharmacy_prefix',
                'pharmacy_code_start_from',
                'hospital_prefix',
                'hospital_code_start_from',
                'patient_prefix',
                'patient_code_start_from',
                'app_currency_id',
                'invoice_business_name',
                'email',
                'invoice_business_landmark',
                'invoice_business_zip',
                'invoice_business_state',
                'invoice_business_city',
                'invoice_business_country',
                'package_expiry_alert_days',
                'superadmin_register_tc',
                'welcome_email_subject',
                'welcome_email_body',
                'patient_register_success_title',
                'patient_register_success_msg',
                'company_register_success_title',
                'company_register_success_msg',
                'subscription_message_online_success_title',
                'subscription_message_online_success_msg',
                'subscription_message_offline_success_title',
                'subscription_message_offline_success_msg',
                'visitor_register_success_title',
                'visitor_register_success_msg',
                'customer_register_success_title',
                'customer_register_success_msg',
                'member_register_success_title',
                'member_register_success_msg',
                'agent_register_success_title',
                'agent_register_success_msg',
                'login_banner_html'
            ]);

            $system_settings['show_give_away_gift_in_register_page'] = !empty($request->show_give_away_gift_in_register_page) ?  json_encode($request->show_give_away_gift_in_register_page) : '[]';
            $system_settings['show_referrals_in_register_page'] = !empty($request->show_referrals_in_register_page) ? json_encode($request->show_referrals_in_register_page) : '[]';
            $system_settings['PAY_ONLINE_CURRENCY_TYPE'] = !empty($request->PAY_ONLINE_CURRENCY_TYPE) ? json_encode($request->PAY_ONLINE_CURRENCY_TYPE) : '[]';

            //Checkboxes
            $checkboxes = [
                'enable_visitor_register_btn_login_page',
                'enable_individual_register_btn_login_page',
                'enable_visitor_welcome_email',
                'enable_customer_welcome_email',
                'enable_admin_login',
                'enable_member_login',
                'enable_visitor_login',
                'enable_customer_login',
                'enable_agent_login',
                'enable_employee_login',
                'enable_pricing_btn_login_page',
                'enable_member_register_btn_login_page',
                'enable_patient_register_btn_login_page',
                'enable_register_btn_login_page',
                'enable_agent_register_btn_login_page',
                'enable_lang_btn_login_page',
                'enable_business_based_username',
                'superadmin_enable_register_tc',
                'allow_email_settings_to_businesses',
                'enable_new_business_registration_notification',
                'enable_new_subscription_notification',
                'enable_welcome_email',
                'customer_secrity_deposit_current_liability_checkbox',
                'supplier_secrity_deposit_current_liability_checkbox',
                'general_message_pump_operator_dashbaord_checkbox',
                'general_message_petro_dashboard_checkbox',
                'general_message_tank_management_checkbox',
                'general_message_pump_management_checkbox',
                'general_message_pumper_management_checkbox',
                'general_message_daily_collection_checkbox',
                'general_message_settlement_checkbox',
                'general_message_list_settlement_checkbox',
                'general_message_dip_management_checkbox',
                'enable_login_banner_image',
                'enable_login_banner_html'
            ];
            $input = $request->input();
            foreach ($checkboxes as $checkbox) {
                $system_settings[$checkbox] = !empty($input[$checkbox]) ? 1 : 0;
            }
            if ($request->enable_customer_login) {
                User::where('is_customer', 1)->update(['status' => 'active']);
            } else {
                User::where('is_customer', 1)->update(['status' => 'inactive']);
            }
            if (!file_exists('./public/img/banners')) {
                mkdir('./public/img/banners', 0777, true);
            }

            //upload banner image
            if ($request->hasfile('login_banner_image')) {
                $file = $request->file('login_banner_image');
                $extension = $file->getClientOriginalExtension();
                $filename = time() . '.' . $extension;
                Image::make($file->getRealPath())->resize(468, 60)->save('public/img/banners/' . $filename);
                $uploadFileFicon = 'public/img/banners/' . $filename;
                $system_settings['login_banner_image'] = $uploadFileFicon;
            } else {
                $system_settings['login_banner_image'] = null;
            }

            $system_settings['default_payment_accounts'] = !empty($input['default_payment_accounts']) ? json_encode($input['default_payment_accounts']) : null;
            foreach ($system_settings as $key => $setting) {
                System::updateOrCreate(
                    ['key' => $key],
                    ['value' => $setting]
                );
            }

            //change defuat account mapping to all business
            $businesses = Business::select('id')->get();
            foreach ($businesses as $business) {
                $busines_map_account = array();
                foreach ($input['default_payment_accounts'] as $key => $map) {
                    $account_id = Account::where('business_id', $business->id)->where('default_account_id', $map['account'])->first();
                    $busines_map_account[$key]['is_enabled'] = !empty($map['is_enabled']) ? $map['is_enabled'] : 0;
                    $busines_map_account[$key]['account'] = !empty($account_id) ? $account_id->id : 0;
                }
                BusinessLocation::where('business_id', $business->id)
                    ->update(['default_payment_accounts' => json_encode($busines_map_account)]);
            }

            $env_settings =  $request->only([
                'APP_NAME', 'APP_TITLE',
                'APP_LOCALE', 'MAIL_DRIVER', 'MAIL_HOST', 'MAIL_PORT',
                'MAIL_USERNAME', 'MAIL_PASSWORD', 'MAIL_ENCRYPTION',
                'MAIL_FROM_ADDRESS', 'MAIL_FROM_NAME', 'STRIPE_PUB_KEY',
                'STRIPE_SECRET_KEY', 'PAYPAL_MODE',
                'PAYPAL_SANDBOX_API_USERNAME',
                'PAYPAL_SANDBOX_API_PASSWORD',
                'PAYPAL_SANDBOX_API_SECRET', 'PAYPAL_LIVE_API_USERNAME',
                'PAYPAL_LIVE_API_PASSWORD', 'PAYPAL_LIVE_API_SECRET',
                'BACKUP_DISK', 'DROPBOX_ACCESS_TOKEN',
                'RAZORPAY_KEY_ID', 'RAZORPAY_KEY_SECRET',
                'PESAPAL_CONSUMER_KEY', 'PESAPAL_CONSUMER_SECRET', 'PESAPAL_LIVE',
                'PAYHERE_MERCHANT_ID', 'PAYHERE_MERCHANT_SECRET', 'PAYHERE_LIVE',
                'PAY_ONLINE_STARTING_NO', 'PAY_ONLINE_BANK_NAME',
                'PAY_ONLINE_BRANCH_NAME', 'PAY_ONLINE_ACCOUNT_NO', 'PAY_ONLINE_ACCOUNT_NAME', 'PAY_ONLINE_SWIFT_CODE'
            ]);
         

            $found_envs = [];
            $env_path = base_path('.env');
            $env_lines = file($env_path);
            foreach ($env_settings as $index => $value) {
                foreach ($env_lines as $key => $line) {
                    //Check if present then replace it.
                    if (strpos($line, $index) !== false) {
                        $env_lines[$key] = $index . '="' . $value . '"' . PHP_EOL;

                        $found_envs[] = $index;
                    }
                }
            }

            //Add the missing env settings
            $missing_envs = array_diff(array_keys($env_settings), $found_envs);
            if (!empty($missing_envs)) {
                $missing_envs = array_values($missing_envs);
                foreach ($missing_envs as $k => $key) {
                    if ($k == 0) {
                        $env_lines[] = PHP_EOL . $key . '="' . $env_settings[$key] . '"' . PHP_EOL;
                    } else {
                        $env_lines[] = $key . '="' . $env_settings[$key] . '"' . PHP_EOL;
                    }
                }
            }

            $env_content = implode('', $env_lines);

            if (is_writable($env_path) && file_put_contents($env_path, $env_content)) {
                $output = [
                    'success' => 1,
                    'msg' => __('lang_v1.success')
                ];
            } else {
                $output = ['success' => 0, 'msg' => 'Some setting could not be saved, make sure .env file has 644 permission & owned by www-data user'];
            }
        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

            $output = [
                'success' => 0,
                'msg' => __('messages.something_went_wrong')
            ];
        }

        return redirect()
            ->action('\Modules\Superadmin\Http\Controllers\SuperadminSettingsController@edit')
            ->with('status', $output);
    }
}
