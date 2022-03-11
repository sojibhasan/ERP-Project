<?php

namespace Modules\Superadmin\Http\Controllers;

use App\Account;
use App\Business;
use App\CompanyPackageVariable;
use App\PackageVariable;
use \Notification;
use App\System;
use Carbon\Carbon;
use Illuminate\Routing\Controller;
use Modules\Superadmin\Entities\Package;
use Modules\Superadmin\Entities\Subscription;
use Modules\Superadmin\Notifications\NewSubscriptionNotification;

class BaseController extends Controller
{

    /**
     * Returns the list of all configured payment gateway
     * @return Response
     */
    protected function _payment_gateways()
    {
        $gateways = [];

        //Check if stripe is configured or not
        if (env('STRIPE_PUB_KEY') && env('STRIPE_SECRET_KEY')) {
            $gateways['stripe'] = 'Stripe';
        }

        //Check if paypal is configured or not
        if ((env('PAYPAL_SANDBOX_API_USERNAME') && env('PAYPAL_SANDBOX_API_PASSWORD')  && env('PAYPAL_SANDBOX_API_SECRET')) || (env('PAYPAL_LIVE_API_USERNAME') && env('PAYPAL_LIVE_API_PASSWORD')  && env('PAYPAL_LIVE_API_SECRET'))) {
            $gateways['paypal'] = 'PayPal';
        }

        //Check if Razorpay is configured or not
        if ((env('RAZORPAY_KEY_ID') && env('RAZORPAY_KEY_SECRET'))) {
            $gateways['razorpay'] = 'Razor Pay';
        }

        //Check if Payhere is configured or not
        if ((env('PAYHERE_MERCHANT_ID'))) {
            $gateways['payhere'] = 'Payhere';
        }

        //Check if Pesapal is configured or not
        if ((config('pesapal.consumer_key') && config('pesapal.consumer_secret'))) {
            $gateways['pesapal'] = 'PesaPal';
        }

        $gateways['offline'] = 'Offline';

        return $gateways;
    }

    /**
     * Enter details for subscriptions
     * @return object
     */
    protected function _add_subscription($business_id, $package, $gateway, $payment_transaction_id, $user_id, $price,  $is_superadmin = false, $option_variables_selected = null, $module_selected = null)
    {
        $trial_used = Business::where('id', $business_id)->first()->trial_used;
        $module_selected = json_decode($module_selected);
        $option_variables_selected = json_decode($option_variables_selected);
        $subcription = Subscription::active_subscription($business_id);
        $already_pacakge = null;
        if (!empty($subcription)) {
            $already_pacakge = Package::where('id', $subcription->package_id)->first();
        }
        $is_company_pacakge = 0;
        if (!empty($package->only_for_business)) {
            $is_company_pacakge = 1;
        }
        $subscription = [
            'business_id' => $business_id,
            'package_id' => $package->id,
            'paid_via' => $gateway,
            'payment_transaction_id' => $payment_transaction_id
        ];

        if (in_array($gateway, ['offline', 'pesapal', 'payhere']) && !$is_superadmin) {
            //If offline then dates will be decided when approved by superadmin
            $subscription['start_date'] = null;
            $subscription['end_date'] = null;
            $subscription['trial_end_date'] = null;
            $subscription['status'] = 'waiting';
        } else {
            $dates = $this->_get_package_dates($business_id, $package);

            $subscription['start_date'] = $dates['start'];
            $subscription['end_date'] = $dates['end'];
            $subscription['trial_end_date'] = $dates['trial'];
            $subscription['status'] = 'approved';
        }
        if ($package->trial_days > 0 && $trial_used == 0) {
            $dates = $this->_get_package_dates($business_id, $package);
            $subscription['start_date'] = $dates['start'];
            $subscription['end_date'] = $dates['trial'];
            $subscription['trial_end_date'] = $dates['trial'];
            $subscription['status'] = 'approved';
            Business::where('id', $business_id)->update(['trial_used' => 1]);
        }
        $subscription['package_price'] = $price;
        if (!empty($option_variables_selected)) {
            $subscription['package_details'] = [
                'location_count' => $this->getoption_value(0, $option_variables_selected, $is_company_pacakge, $already_pacakge),
                'user_count' => $this->getoption_value(1, $option_variables_selected, $is_company_pacakge, $already_pacakge),
                'product_count' => $this->getoption_value(2, $option_variables_selected, $is_company_pacakge, $already_pacakge),
                'invoice_count' => $package->invoice_count,
                'customer_count' => $package->customer_count,
                'period_count' => $this->getoption_value(3, $option_variables_selected, $is_company_pacakge, $already_pacakge),
                'customer_count' => $this->getoption_value(4, $option_variables_selected, $is_company_pacakge, $already_pacakge),
                'vehicle_count' => $this->getoption_value(7, $option_variables_selected, $is_company_pacakge, $already_pacakge),
                'monthly_total_sales_limit' => $this->getoption_value(5, $option_variables_selected, $is_company_pacakge, $already_pacakge),
                'name' => $package->name,
                'mf_module' =>  $this->check_module_selected('mf_module', $module_selected, $package, $is_company_pacakge, $already_pacakge),
                'enable_sale_cmsn_agent' => $this->check_module_selected('enable_sale_cmsn_agent', $module_selected, $package, $is_company_pacakge, $already_pacakge),
                'enable_restaurant' =>  $this->check_module_selected('enable_restaurant', $module_selected, $package, $is_company_pacakge, $already_pacakge),
                'enable_booking' => $this->check_module_selected('enable_booking', $module_selected, $package, $is_company_pacakge, $already_pacakge),
                'enable_crm' => $this->check_module_selected('enable_crm', $module_selected, $package, $is_company_pacakge, $already_pacakge),
                'enable_sms' => $this->check_module_selected('enable_sms', $module_selected, $package, $is_company_pacakge, $already_pacakge),
                'hr_module' => $this->check_module_selected('hr_module', $module_selected, $package, $is_company_pacakge, $already_pacakge),
                'employee' => $this->check_module_selected('employee', $module_selected, $package, $is_company_pacakge, $already_pacakge),
                'teminated' => $this->check_module_selected('teminated', $module_selected, $package, $is_company_pacakge, $already_pacakge),
                'award' => $this->check_module_selected('award', $module_selected, $package, $is_company_pacakge, $already_pacakge),
                'leave_request' => $this->check_module_selected('leave_request', $module_selected, $package, $is_company_pacakge, $already_pacakge),
                'attendance' => $this->check_module_selected('attendance', $module_selected, $package, $is_company_pacakge, $already_pacakge),
                'import_attendance' => $this->check_module_selected('import_attendance', $module_selected, $package, $is_company_pacakge, $already_pacakge),
                'late_and_over_time' => $this->check_module_selected('late_and_over_time', $module_selected, $package, $is_company_pacakge, $already_pacakge),
                'payroll' => $this->check_module_selected('payroll', $module_selected, $package, $is_company_pacakge, $already_pacakge),
                'salary_details' => $this->check_module_selected('salary_details', $module_selected, $package, $is_company_pacakge, $already_pacakge),
                'basic_salary' => $this->check_module_selected('basic_salary', $module_selected, $package, $is_company_pacakge, $already_pacakge),
                'payroll_payments' => $this->check_module_selected('payroll_payments', $module_selected, $package, $is_company_pacakge, $already_pacakge),
                'hr_reports' => $this->check_module_selected('hr_reports', $module_selected, $package, $is_company_pacakge, $already_pacakge),
                'attendance_report' => $this->check_module_selected('attendance_report', $module_selected, $package, $is_company_pacakge, $already_pacakge),
                'employee_report' => $this->check_module_selected('employee_report', $module_selected, $package, $is_company_pacakge, $already_pacakge),
                'payroll_report' => $this->check_module_selected('payroll_report', $module_selected, $package, $is_company_pacakge, $already_pacakge),
                'notice_board' => $this->check_module_selected('notice_board', $module_selected, $package, $is_company_pacakge, $already_pacakge),
                'hr_settings' => $this->check_module_selected('hr_settings', $module_selected, $package, $is_company_pacakge, $already_pacakge),
                'department' => $this->check_module_selected('department', $module_selected, $package, $is_company_pacakge, $already_pacakge),
                'jobtitle' => $this->check_module_selected('jobtitle', $module_selected, $package, $is_company_pacakge, $already_pacakge),
                'jobcategory' => $this->check_module_selected('jobcategory', $module_selected, $package, $is_company_pacakge, $already_pacakge),
                'workingdays' => $this->check_module_selected('workingdays', $module_selected, $package, $is_company_pacakge, $already_pacakge),
                'workshift' => $this->check_module_selected('workshift', $module_selected, $package, $is_company_pacakge, $already_pacakge),
                'holidays' => $this->check_module_selected('holidays', $module_selected, $package, $is_company_pacakge, $already_pacakge),
                'leave_type' => $this->check_module_selected('leave_type', $module_selected, $package, $is_company_pacakge, $already_pacakge),
                'salary_grade' => $this->check_module_selected('salary_grade', $module_selected, $package, $is_company_pacakge, $already_pacakge),
                'employment_status' => $this->check_module_selected('employment_status', $module_selected, $package, $is_company_pacakge, $already_pacakge),
                'salary_component' => $this->check_module_selected('salary_component', $module_selected, $package, $is_company_pacakge, $already_pacakge),
                'hr_prefix' => $this->check_module_selected('hr_prefix', $module_selected, $package, $is_company_pacakge, $already_pacakge),
                'hr_tax' => $this->check_module_selected('hr_tax', $module_selected, $package, $is_company_pacakge, $already_pacakge),
                'religion' => $this->check_module_selected('religion', $module_selected, $package, $is_company_pacakge, $already_pacakge),
                'hr_setting_page' => $this->check_module_selected('hr_setting_page', $module_selected, $package, $is_company_pacakge, $already_pacakge),
                'mpcs_module' => $this->check_module_selected('mpcs_module', $module_selected, $package, $is_company_pacakge, $already_pacakge),
                'fleet_module' => $this->check_module_selected('fleet_module', $module_selected, $package, $is_company_pacakge, $already_pacakge),
                'ezyboat_module' => $this->check_module_selected('ezyboat_module', $module_selected, $package, $is_company_pacakge, $already_pacakge),
                'mpcs_form_settings' => $this->check_module_selected('mpcs_form_settings', $module_selected, $package, $is_company_pacakge, $already_pacakge),
                'list_opening_values' => $this->check_module_selected('list_opening_values', $module_selected, $package, $is_company_pacakge, $already_pacakge),
                'merge_sub_category' => $this->check_module_selected('merge_sub_category', $module_selected, $package, $is_company_pacakge, $already_pacakge),
                'backup_module' => $this->check_module_selected('backup_module', $module_selected, $package, $is_company_pacakge, $already_pacakge),
                'enable_separate_customer_statement_no' => $this->check_module_selected('enable_separate_customer_statement_no', $module_selected, $package, $is_company_pacakge, $already_pacakge),
                'edit_customer_statement' => $this->check_module_selected('edit_customer_statement', $module_selected, $package, $is_company_pacakge, $already_pacakge),
                'enable_cheque_writing' => $this->check_module_selected('enable_cheque_writing', $module_selected, $package, $is_company_pacakge, $already_pacakge),
                'issue_customer_bill' => $this->check_module_selected('issue_customer_bill', $module_selected, $package, $is_company_pacakge, $already_pacakge),
                'enable_petro_module' => $package->petro_module,
                'hospital_system' => $package->hospital_system,
                'customer_order_own_customer' => $this->check_module_selected('customer_order_own_customer', $module_selected, $package, $is_company_pacakge, $already_pacakge),
                'customer_settings' => $this->check_module_selected('customer_settings', $module_selected, $package, $is_company_pacakge, $already_pacakge),
                'customer_order_general_customer' => $this->check_module_selected('customer_order_general_customer', $module_selected, $package, $is_company_pacakge, $already_pacakge),
                'customer_to_directly_in_panel' => $this->check_module_selected('customer_to_directly_in_panel', $module_selected, $package, $is_company_pacakge, $already_pacakge),
                'meter_resetting' => $this->check_module_selected('meter_resetting', $module_selected, $package, $is_company_pacakge, $already_pacakge),
                'tasks_management' => $this->check_module_selected('tasks_management', $module_selected, $package, $is_company_pacakge, $already_pacakge),
                'notes_page' => $this->check_module_selected('notes_page', $module_selected, $package, $is_company_pacakge, $already_pacakge),
                'tasks_page' => $this->check_module_selected('tasks_page', $module_selected, $package, $is_company_pacakge, $already_pacakge),
                'reminder_page' => $this->check_module_selected('reminder_page', $module_selected, $package, $is_company_pacakge, $already_pacakge),
                'member_registration' => $this->check_module_selected('member_registration', $module_selected, $package, $is_company_pacakge, $already_pacakge),
                'visitors_registration_module' => $this->check_module_selected('visitors_registration_module', $module_selected, $package, $is_company_pacakge, $already_pacakge),
                'visitors' => $this->check_module_selected('visitors', $module_selected, $package, $is_company_pacakge, $already_pacakge),
                'visitors_registration' => $this->check_module_selected('visitors_registration', $module_selected, $package, $is_company_pacakge, $already_pacakge),
                'visitors_registration_setting' => $this->check_module_selected('visitors_registration_setting', $module_selected, $package, $is_company_pacakge, $already_pacakge),
                'visitors_district' => $this->check_module_selected('visitors_district', $module_selected, $package, $is_company_pacakge, $already_pacakge),
                'visitors_town' => $this->check_module_selected('visitors_town', $module_selected, $package, $is_company_pacakge, $already_pacakge),
                'disable_all_other_module_vr' => $this->check_module_selected('disable_all_other_module_vr', $module_selected, $package, $is_company_pacakge, $already_pacakge),
                'catalogue_qr' => $this->check_module_selected('catalogue_qr', $module_selected, $package, $is_company_pacakge, $already_pacakge),
                'pay_excess_commission' => $this->check_module_selected('pay_excess_commission', $module_selected, $package, $is_company_pacakge, $already_pacakge),
                'recover_shortage' => $this->check_module_selected('recover_shortage', $module_selected, $package, $is_company_pacakge, $already_pacakge),
                'pump_operator_ledger' => $this->check_module_selected('pump_operator_ledger', $module_selected, $package, $is_company_pacakge, $already_pacakge),
                'commission_type' => $this->check_module_selected('commission_type', $module_selected, $package, $is_company_pacakge, $already_pacakge),
                'access_account' => json_decode($package->package_permissions)->account_access,
                'access_sms_settings' => json_decode($package->package_permissions)->sms_settings_access,
                'access_module' => json_decode($package->package_permissions)->module_access,
                'home_dashboard' => $this->check_module_selected('home_dashboard', $module_selected, $package, $is_company_pacakge, $already_pacakge),
                'contact_module' => $this->check_module_selected('contact_module', $module_selected, $package, $is_company_pacakge, $already_pacakge),
                'contact_customer' => $this->check_module_selected('contact_customer', $module_selected, $package, $is_company_pacakge, $already_pacakge),
                'contact_supplier' => $this->check_module_selected('contact_supplier', $module_selected, $package, $is_company_pacakge, $already_pacakge),
                'property_module' => $package->property_module,
                'tank_dip_chart' => $this->check_module_selected('tank_dip_chart', $module_selected, $package, $is_company_pacakge, $already_pacakge),
                'ran_module' => $this->check_module_selected('ran_module', $module_selected, $package, $is_company_pacakge, $already_pacakge),
                'report_module' => $this->check_module_selected('report_module', $module_selected, $package, $is_company_pacakge, $already_pacakge),
                'verification_report' => $this->check_module_selected('verification_report', $module_selected, $package, $is_company_pacakge, $already_pacakge),
                'monthly_report' => $this->check_module_selected('monthly_report', $module_selected, $package, $is_company_pacakge, $already_pacakge),
                'comparison_report' => $this->check_module_selected('comparison_report', $module_selected, $package, $is_company_pacakge, $already_pacakge),
                'notification_template_module' => $this->check_module_selected('notification_template_module', $module_selected, $package, $is_company_pacakge, $already_pacakge),
                'list_easy_payment' => $this->check_module_selected('list_easy_payment', $module_selected, $package, $is_company_pacakge, $already_pacakge),
                'settings_module' => $this->check_module_selected('settings_module', $module_selected, $package, $is_company_pacakge, $already_pacakge),
                'business_settings' => $this->check_module_selected('business_settings', $module_selected, $package, $is_company_pacakge, $already_pacakge),
                'business_location' => $this->check_module_selected('business_location', $module_selected, $package, $is_company_pacakge, $already_pacakge),
                'invoice_settings' => $this->check_module_selected('invoice_settings', $module_selected, $package, $is_company_pacakge, $already_pacakge),
                'tax_rates' => $this->check_module_selected('tax_rates', $module_selected, $package, $is_company_pacakge, $already_pacakge),
                'user_management_module' => $this->check_module_selected('user_management_module', $module_selected, $package, $is_company_pacakge, $already_pacakge),
                'banking_module' => $this->check_module_selected('banking_module', $module_selected, $package, $is_company_pacakge, $already_pacakge),
                'orders' => $this->check_module_selected('orders', $module_selected, $package, $is_company_pacakge, $already_pacakge),
                'products' => $this->check_module_selected('products', $module_selected, $package, $is_company_pacakge, $already_pacakge),
                'purchase' => $this->check_module_selected('purchase', $module_selected, $package, $is_company_pacakge, $already_pacakge),
                'stock_transfer' => $this->check_module_selected('stock_transfer', $module_selected, $package, $is_company_pacakge, $already_pacakge),
                'service_staff' => $this->check_module_selected('service_staff', $module_selected, $package, $is_company_pacakge, $already_pacakge),
                'enable_subscription' => $this->check_module_selected('enable_subscription', $module_selected, $package, $is_company_pacakge, $already_pacakge),
                'sale_module' => $this->check_module_selected('sale_module', $module_selected, $package, $is_company_pacakge, $already_pacakge),
                'all_sales' => $this->check_module_selected('all_sales', $module_selected, $package, $is_company_pacakge, $already_pacakge),
                'add_sale' => $this->check_module_selected('add_sale', $module_selected, $package, $is_company_pacakge, $already_pacakge),
                'list_pos' => $this->check_module_selected('list_pos', $module_selected, $package, $is_company_pacakge, $already_pacakge),
                'pos_sale' => $this->check_module_selected('pos_sale', $module_selected, $package, $is_company_pacakge, $already_pacakge),
                'list_draft' => $this->check_module_selected('list_draft', $module_selected, $package, $is_company_pacakge, $already_pacakge),
                'list_quotation' => $this->check_module_selected('list_quotation', $module_selected, $package, $is_company_pacakge, $already_pacakge),
                'list_sell_return' => $this->check_module_selected('list_sell_return', $module_selected, $package, $is_company_pacakge, $already_pacakge),
                'shipment' => $this->check_module_selected('shipment', $module_selected, $package, $is_company_pacakge, $already_pacakge),
                'discount' => $this->check_module_selected('discount', $module_selected, $package, $is_company_pacakge, $already_pacakge),
                'import_sale' => $this->check_module_selected('import_sale', $module_selected, $package, $is_company_pacakge, $already_pacakge),
                'reserved_stock' => $this->check_module_selected('reserved_stock', $module_selected, $package, $is_company_pacakge, $already_pacakge),
                'stock_adjustment' => $this->check_module_selected('stock_adjustment', $module_selected, $package, $is_company_pacakge, $already_pacakge),
                'tables' => $this->check_module_selected('tables', $module_selected, $package, $is_company_pacakge, $already_pacakge),
                'type_of_service' => $this->check_module_selected('type_of_service', $module_selected, $package, $is_company_pacakge, $already_pacakge),
                'expenses' => $this->check_module_selected('expenses', $module_selected, $package, $is_company_pacakge, $already_pacakge),
                'modifiers' => $this->check_module_selected('modifiers', $module_selected, $package, $is_company_pacakge, $already_pacakge),
                'kitchen' => $this->check_module_selected('kitchen', $module_selected, $package, $is_company_pacakge, $already_pacakge),
                'upload_images' => $this->check_module_selected('upload_images', $module_selected, $package, $is_company_pacakge, $already_pacakge),
                'leads_module' => $this->check_module_selected('leads_module', $module_selected, $package, $is_company_pacakge, $already_pacakge),
                'leads' => $this->check_module_selected('leads', $module_selected, $package, $is_company_pacakge, $already_pacakge),
                'day_count' => $this->check_module_selected('day_count', $module_selected, $package, $is_company_pacakge, $already_pacakge),
                'leads_import' => $this->check_module_selected('leads_import', $module_selected, $package, $is_company_pacakge, $already_pacakge),
                'leads_settings' => $this->check_module_selected('leads_settings', $module_selected, $package, $is_company_pacakge, $already_pacakge),
                'sms_module' => $this->check_module_selected('sms_module', $module_selected, $package, $is_company_pacakge, $already_pacakge),
                'cache_clear' => $this->check_module_selected('cache_clear', $module_selected, $package, $is_company_pacakge, $already_pacakge),
                'pump_operator_dashboard' => $this->check_module_selected('pump_operator_dashboard', $module_selected, $package, $is_company_pacakge, $already_pacakge),
                'list_sms' => $this->check_module_selected('list_sms', $module_selected, $package, $is_company_pacakge, $already_pacakge),
                'status_order' => $this->check_module_selected('status_order', $module_selected, $package, $is_company_pacakge, $already_pacakge),
                'list_orders' => $this->check_module_selected('list_orders', $module_selected, $package, $is_company_pacakge, $already_pacakge),
                'upload_orders' => $this->check_module_selected('upload_orders', $module_selected, $package, $is_company_pacakge, $already_pacakge),
                'subcriptions' => $this->check_module_selected('subcriptions', $module_selected, $package, $is_company_pacakge, $already_pacakge),
                'over_limit_sales' => $this->check_module_selected('over_limit_sales', $module_selected, $package, $is_company_pacakge, $already_pacakge),
                'repair_module' => $this->check_module_selected('repair_module', $module_selected, $package, $is_company_pacakge, $already_pacakge),
                'job_sheets' => $this->check_module_selected('job_sheets', $module_selected, $package, $is_company_pacakge, $already_pacakge),
                'add_job_sheet' => $this->check_module_selected('add_job_sheet', $module_selected, $package, $is_company_pacakge, $already_pacakge),
                'list_invoice' => $this->check_module_selected('list_invoice', $module_selected, $package, $is_company_pacakge, $already_pacakge),
                'add_invoice' => $this->check_module_selected('add_invoice', $module_selected, $package, $is_company_pacakge, $already_pacakge),
                'brands' => $this->check_module_selected('brands', $module_selected, $package, $is_company_pacakge, $already_pacakge),
                'repair_settings' => $this->check_module_selected('repair_settings', $module_selected, $package, $is_company_pacakge, $already_pacakge),
                'payday' => $this->check_module_selected('payday', $module_selected, $package, $is_company_pacakge, $already_pacakge)

            ];
        } else {
            $subscription['package_details'] = [
                'location_count' => $package->location_count,
                'user_count' => $package->user_count,
                'customer_count' => $package->customer_count,
                'monthly_total_sales_limit' => 0,
                'product_count' => $package->product_count,
                'invoice_count' => $package->invoice_count,
                'vehicle_count' => $package->vehicle_count,
                'name' => $package->name,
                'enable_sale_cmsn_agent' => $package->sales_commission_agent,
                'manufacturing_module' => $package->manufacturer,
                'mf_module' => $package->manufacturer,
                'enable_restaurant' => $package->restaurant,
                'enable_booking' => $package->booking,
                'enable_crm' => $package->crm_enable,
                'enable_sms' => $package->sms_enable,

                'hr_module' => $package->hr_module,
                'employee' => $package->employee,
                'teminated' => $package->teminated,
                'award' => $package->award,
                'leave_request' => $package->leave_request,
                'attendance' => $package->attendance,
                'import_attendance' => $package->import_attendance,
                'late_and_over_time' => $package->late_and_over_time,
                'payroll' => $package->payroll,
                'salary_details' => $package->salary_details,
                'basic_salary' => $package->basic_salary,
                'payroll_payments' => $package->payroll_payments,
                'hr_reports' => $package->hr_reports,
                'attendance_report' => $package->attendance_report,
                'employee_report' => $package->employee_report,
                'payroll_report' => $package->payroll_report,
                'notice_board' => $package->notice_board,
                'hr_settings' => $package->hr_settings,
                'department' => $package->department,
                'jobtitle' => $package->jobtitle,
                'jobcategory' => $package->jobcategory,
                'workingdays' => $package->workingdays,
                'workshift' => $package->workshift,
                'holidays' => $package->holidays,
                'leave_type' => $package->leave_type,
                'salary_grade' => $package->salary_grade,
                'employment_status' => $package->employment_status,
                'salary_component' => $package->salary_component,
                'hr_prefix' => $package->hr_prefix,
                'hr_tax' => $package->hr_tax,
                'religion' => $package->religion,
                'hr_setting_page' => $package->hr_setting_page,

                'enable_petro_module' => $package->petro_module,
                'mpcs_module' => $package->mpcs_module,
                'fleet_module' => $package->fleet_module,
                'ezyboat_module' => $package->ezyboat_module,
                'mpcs_form_settings' => $package->mpcs_form_settings,
                'list_opening_values' => $package->list_opening_values,
                'merge_sub_category' => $package->merge_sub_category,
                'backup_module' => $package->backup_module,
                'enable_separate_customer_statement_no' => $package->enable_separate_customer_statement_no,
                'edit_customer_statement' => $package->edit_customer_statement,
                'enable_cheque_writing' => $package->enable_cheque_writing,
                'issue_customer_bill' => $package->issue_customer_bill,
                'hospital_system' => $package->hospital_system,
                'customer_order_own_customer' => $package->customer_order_own_customer,
                'customer_settings' => $package->customer_settings,
                'customer_order_general_customer' => $package->customer_order_general_customer,
                'customer_to_directly_in_panel' => $package->customer_to_directly_in_panel,
                'meter_resetting' => $package->meter_resetting,
                'tasks_management' => $package->tasks_management,
                'notes_page' => $package->notes_page,
                'tasks_page' => $package->tasks_page,
                'reminder_page' => $package->reminder_page,
                'member_registration' => $package->member_registration,
                'visitors_registration_module' => $package->visitors_registration_module,
                'visitors' => $package->visitors,
                'visitors_registration' => $package->visitors_registration,
                'visitors_registration_setting' => $package->visitors_registration_setting,
                'visitors_district' => $package->visitors_district,
                'visitors_town' => $package->visitors_town,
                'disable_all_other_module_vr' => $package->disable_all_other_module_vr,
                'catalogue_qr' => $package->catalogue_qr,
                'pay_excess_commission' => $package->pay_excess_commission,
                'recover_shortage' => $package->recover_shortage,
                'pump_operator_ledger' => $package->pump_operator_ledger,
                'commission_type' => $package->commission_type,
                'enable_duplicate_invoice' => $package->enable_duplicate_invoice,
                'access_account' => json_decode($package->package_permissions)->account_access,
                'access_sms_settings' => json_decode($package->package_permissions)->sms_settings_access,
                'access_module' => json_decode($package->package_permissions)->module_access,
                'home_dashboard' => $package->home_dashboard,
                'contact_module' => $package->contact_module,
                'contact_customer' => $package->contact_customer,
                'contact_supplier' => $package->contact_supplier,
                'property_module' => $package->property_module,
                'tank_dip_chart' => $package->tank_dip_chart,
                'ran_module' => $package->ran_module,
                'report_module' => $package->report_module,
                'verification_report' => $package->verification_report,
                'monthly_report' => $package->monthly_report,
                'comparison_report' => $package->comparison_report,
                'notification_template_module' => $package->notification_template_module,
                'list_easy_payment' => $package->list_easy_payment,
                'settings_module' => $package->settings_module,
                'business_settings' => $package->business_settings,
                'business_location' => $package->business_location,
                'invoice_settings' => $package->invoice_settings,
                'tax_rates' => $package->tax_rates,
                'user_management_module' => $package->user_management_module,
                'banking_module' => $package->banking_module,
                'orders' => $package->orders,
                'products' => $package->products,
                'purchase' => $package->purchase,
                'stock_transfer' => $package->stock_transfer,
                'service_staff' => $package->service_staff,
                'enable_subscription' => $package->enable_subscription,
                'sale_module' => $package->sale_module,
                'all_sales' => $package->add_sale,
                'add_sale' => $package->add_sale,
                'list_pos' => $package->list_pos,
                'pos_sale' => $package->pos_sale,
                'list_draft' => $package->list_draft,
                'list_quotation' => $package->list_quotation,
                'list_sell_return' => $package->list_sell_return,
                'shipment' => $package->shipment,
                'discount' => $package->discount,
                'import_sale' => $package->import_sale,
                'reserved_stock' => $package->reserved_stock,
                'stock_adjustment' => $package->stock_adjustment,
                'tables' => $package->tables,
                'type_of_service' => $package->type_of_service,
                'expenses' => $package->expenses,
                'modifiers' => $package->modifiers,
                'kitchen' => $package->kitchen,
                'upload_images' => $package->upload_images,
                'leads_module' => $package->leads_module,
                'leads' => $package->leads,
                'day_count' => $package->day_count,
                'leads_import' => $package->leads_import,
                'leads_settings' => $package->leads_settings,
                'sms_module' => $package->sms_module,
                'cache_clear' => $package->cache_clear,
                'pump_operator_dashboard' => $package->pump_operator_dashboard,
                'list_sms' => $package->list_sms,
                'status_order' => $package->status_order,
                'list_orders' => $package->list_orders,
                'upload_orders' => $package->upload_orders,
                'subcriptions' => $package->subcriptions,
                'over_limit_sales' => $package->over_limit_sales,
                'repair_module' => $package->repair_module,
                'job_sheets' => $package->job_sheets,
                'add_job_sheet' => $package->add_job_sheet,
                'list_invoice' => $package->list_invoice,
                'add_invoice' => $package->add_invoice,
                'brands' => $package->brands,
                'repair_settings' => $package->repair_settings,
                'payday' => $package->payday
            ];
        }

        //Custom permissions.
        if (!empty($package->custom_permissions)) {
            foreach ($package->custom_permissions as $name => $value) {
                $subscription['package_details'][$name] = $value;
            }
        }

        $subscription['created_id'] = $user_id;

        $subscription = Subscription::create($subscription);

        //visible all account if account module is enable
        if (!empty($subscription['package_details']['access_account'])) {
            Account::where('business_id', $business_id)->update(['visible' => 1]);
        }

        if (!$is_superadmin && $gateway != 'payhere' && $trial_used != 0) {
            $email = System::getProperty('email');
            $is_notif_enabled = System::getProperty('enable_new_subscription_notification');

            if (!empty($email) && $is_notif_enabled == 1 && $subscription->status == 'approved') {
                Notification::route('mail', $email)
                    ->notify(new NewSubscriptionNotification($subscription));
            }
        }

        return $subscription;
    }

    protected function check_module_selected($key, $module_selected, $package, $is_company_pacakge, $already_pacakge)
    {
        if ($is_company_pacakge == 1) {
            if (in_array($key, $module_selected)) {
                return 1;
            }
            if (!empty($already_pacakge)) {
                if ($key == 'mf_module') {
                    return $already_pacakge->manufacturer;
                }
                if ($key == 'enable_sale_cmsn_agent') {
                    return $already_pacakge->sales_commission_agent;
                }
                if ($key == 'enable_restaurant') {
                    return $already_pacakge->restaurant;
                }
                if ($key == 'enable_booking') {
                    return $already_pacakge->booking;
                }
                if ($key == 'enable_crm') {
                    return $already_pacakge->crm_enable;
                }
                if ($key == 'enable_sms') {
                    return $already_pacakge->sms_enable;
                }
                if ($key == 'hr_module') {
                    return $already_pacakge->hr_module;
                }
            }
        }
        if ($key == 'mf_module') {
            return $package->manufacturer;
        }
        if ($key == 'enable_sale_cmsn_agent') {
            return $package->sales_commission_agent;
        }
        if ($key == 'enable_restaurant') {
            return $package->restaurant;
        }
        if ($key == 'enable_booking') {
            return $package->booking;
        }
        if ($key == 'enable_crm') {
            return $package->crm_enable;
        }
        if ($key == 'enable_sms') {
            return $package->sms_enable;
        }
        if ($key == 'hr_module') {
            return $package->hr_module;
        }

        return $package->$key;
    }

    protected function getoption_value($option, $option_variables_selected, $is_company_pacakge, $already_pacakge)
    {
        if ($is_company_pacakge == 1) {
            foreach ($option_variables_selected as $opt_var) {
                $value = PackageVariable::where('id', $opt_var)->where('is_company_variable', 1)->first();
                if ($value->variable_options == $option) {
                    return $value->option_value;
                }
            }
        } else {
            foreach ($option_variables_selected as $opt_var) {
                $value = PackageVariable::where('id', $opt_var)->where('is_company_variable', 0)->first();
                if ($value->variable_options == $option) {
                    return $value->option_value;
                }
            }
        }

        if (!empty($already_pacakge)) {
            if ($option == 0) {
                return $already_pacakge->location_count;
            }
            if ($option == 1) {
                return $already_pacakge->user_count;
            }
            if ($option == 2) {
                return $already_pacakge->product_count;
            }
            if ($option == 3) {
                return $already_pacakge->period_count;
            }
            if ($option == 4) {
                return $already_pacakge->customer_count;
            }
        }

        return 1;
    }
    /**
     * The function returns the start/end/trial end date for a package.
     *
     * @param int $business_id
     * @param object $package
     *
     * @return array
     */
    protected function _get_package_dates($business_id, $package)
    {
        $output = ['start' => '', 'end' => '', 'trial' => ''];

        //calculate start date
        $start_date = Subscription::end_date($business_id);
        $start_date = $start_date->subDays(1);
        $output['start'] = $start_date->toDateString();

        //Calculate end date
        if ($package->interval == 'days') {
            $output['end'] = $start_date->addDays($package->interval_count)->toDateString();
        } elseif ($package->interval == 'months') {
            $output['end'] = $start_date->addMonths($package->interval_count)->toDateString();
        } elseif ($package->interval == 'years') {
            $output['end'] = $start_date->addYears($package->interval_count)->toDateString();
        }

        //if company already used the trial period then no need to add trial again
        $trial_used = Business::where('id', $business_id)->first()->trial_used;
        if ($trial_used == 0) {
            $st_date = Carbon::parse($output['start']);
            $output['trial'] =  $st_date->addDays($package->trial_days)->toDateString();
        } else {
            $output['trial'] = NULL;
        }

        return $output;
    }
}
