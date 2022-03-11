<?php

namespace Modules\Superadmin\Http\Controllers;

use App\Business;
use App\System;
use App\PackageVariable;
use App\Utils\BusinessUtil;
use App\Utils\ModuleUtil;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\Superadmin\Entities\Package;
use Modules\Superadmin\Entities\Subscription;
use Illuminate\Support\Facades\DB;

class PackagesController extends BaseController
{
    /**
     * All Utils instance.
     *
     */
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
        if (!auth()->user()->can('superadmin')) {
            abort(403, 'Unauthorized action.');
        }
        $create_individual_company_package = System::getProperty('create_individual_company_package');
        $query = Package::orderby('sort_order', 'asc');
        if ($create_individual_company_package != 'yes') {
            $query->whereNull('only_for_business');
        }
        $packages = $query->paginate(20);
        //Get all module permissions and convert them into name => label
        $permissions = $this->moduleUtil->getModuleData('superadmin_package');
        $permission_formatted = [];
        foreach ($permissions as $permission) {
            foreach ($permission as $details) {
                $permission_formatted[$details['name']] = $details['label'];
            }
        }
        return view('superadmin::packages.index')
            ->with(compact('packages', 'permission_formatted'));
    }
    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create()
    {
        if (!auth()->user()->can('superadmin')) {
            abort(403, 'Unauthorized action.');
        }
        $intervals = ['days' => __('lang_v1.days'), 'months' => __('lang_v1.months'), 'years' => __('lang_v1.years')];
        $currency = System::getCurrency();
        $currencies = $this->businessUtil->allCurrencies();
        $permissions = $this->moduleUtil->getModuleData('superadmin_package');
        $package_variables = PackageVariable::all();
        $all_variable_options = ['Number of Branches', 'Number of Users', 'Number of Products', 'Number of Periods', 'Monthly Total Sales', 'No of Family Members'];
        $all_increase_decrease = ['Increase', 'Decrease'];
        $all_variable_type =  ['Fixed', 'Percentage'];
        $default_number_of_customers = System::getProperty('default_number_of_customers');
        return view('superadmin::packages.create')
            ->with(compact('default_number_of_customers', 'intervals', 'currency', 'permissions', 'currencies', 'package_variables', 'all_variable_options', 'all_increase_decrease', 'all_variable_type'));
    }
    /**
     * Store a newly created resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        if (!auth()->user()->can('superadmin')) {
            abort(403, 'Unauthorized action.');
        }
        try {
            $input = $request->only([
                'visit_count', 'option_variables',
                'name', 'description', 'location_count', 'customer_count', 'employee_count', 'user_count', 'product_count', 'invoice_count', 'vehicle_count', 'store_count', 'interval', 'interval_count', 'trial_days', 'price', 'sort_order', 'is_active', 'custom_permissions', 'is_private', 'is_one_time', 'enable_custom_link', 'custom_link',
                'custom_link_text', 'currency_id'
            ]);
            $input['location_count'] = !empty($input['location_count']) ? $input['location_count'] : 0;
            $input['user_count'] = !empty($input['user_count']) ? $input['user_count'] : 0;
            $input['product_count'] = !empty($input['product_count']) ? $input['product_count'] : 0;
            $input['invoice_count'] = !empty($input['invoice_count']) ? $input['invoice_count'] : 0;
            $input['store_count'] = !empty($input['store_count']) ? $input['store_count'] : 0;
            $input['employee_count'] = !empty($input['employee_count']) ? $input['employee_count'] : 0;
            $input['vehicle_count'] = !empty($input['vehicle_count']) ? $input['vehicle_count'] : 0;
            $input['option_variables'] = !empty($input['option_variables']) ? json_encode($input['option_variables']) : '[]';
            if (empty($request->sales_commission_agent)) {
                $input['sales_commission_agent'] = 0;
            } else {
                $input['sales_commission_agent'] = 1;
            }
            if ($request->payday == '1') {
                $input['payday'] = 1;
            } else {
                $input['payday'] = 0;
            }
            if ($request->restaurant == '1') {
                $input['restaurant'] = 1;
            } else {
                $input['restaurant'] = 0;
            }
            if ($request->booking == '1') {
                $input['booking'] = 1;
            } else {
                $input['booking'] = 0;
            }
            if ($request->manufacturer == '1') {
                $input['manufacturer'] = 1;
            } else {
                $input['manufacturer'] = 0;
            }
            if ($request->sms_enable == '1') {
                $input['sms_enable'] = 1;
            } else {
                $input['sms_enable'] = 0;
            }
            if (empty($request->crm_enable)) {
                $input['crm_enable'] = 0;
            } else {
                $input['crm_enable'] = 1;
            }
            if (empty($request->hr_module)) {
                $input['hr_module'] = 0;
            } else {
                $input['hr_module'] = 1;
            }
            if (empty($request->hospital_system)) {
                $input['hospital_system'] = 0;
            } else {
                $input['hospital_system'] = 1;
            }
            if (empty($request->enable_duplicate_invoice)) {
                $input['enable_duplicate_invoice'] = 0;
            } else {
                $input['enable_duplicate_invoice'] = 1;
            }
            if (empty($request->petro_module)) {
                $input['petro_module'] = 0;
                $input['meter_resetting'] = 0;
                /**
                 * @author: Afes Oktavianus
                 * @since: 23-08-2021
                 * @req: 8413 - Package Permission for Petro Module
                 */
                $input['petro_dashboard'] = 0;
                $input['petro_task_management'] = 0;
                $input['pump_management'] = 0;
                $input['pump_management_testing'] = 0;
                $input['meter_reading'] = 0;
                $input['pump_dashboard_opening'] = 0;
                $input['pumper_management'] = 0;
                $input['daily_collection'] = 0;
                $input['settlement'] = 0;
                $input['list_settlement'] = 0;
                $input['dip_management'] = 0;
            } else {
                $input['petro_module'] = 1;
                $input['meter_resetting'] = 1;
                /**
                 * @author: Afes Oktavianus
                 * @since: 23-08-2021
                 * @req: 8413 - Package Permission for Petro Module
                 */
                $input['petro_dashboard'] = $request->petro_dashboard == '1' ? 1 : 0;
                $input['petro_task_management'] = $request->petro_task_management == '1' ? 1 : 0;
                $input['pump_management'] = $request->pump_management == '1' ? 1 : 0;
                $input['pump_management_testing'] = $request->pump_management_testing == '1' ? 1 : 0;
                $input['meter_reading'] = $request->meter_reading == '1' ? 1 : 0;
                $input['pump_dashboard_opening'] = $request->pump_dashboard_opening == '1' ? 1 : 0;
                $input['pumper_management'] = $request->pumper_management == '1' ? 1 : 0;
                $input['daily_collection'] = $request->daily_collection == '1' ? 1 : 0;
                $input['settlement'] = $request->settlement == '1' ? 1 : 0;
                $input['list_settlement'] = $request->list_settlement == '1' ? 1 : 0;
                $input['dip_management'] = $request->dip_management == '1' ? 1 : 0;
            }
            if (empty($request->pump_operator_dashboard)) {
                $input['pump_operator_dashboard'] = 0;
            } else {
                $input['pump_operator_dashboard'] = 1;
            }
            if (empty($request->account_access)) {
                $package_permissions['account_access'] = 0;
            } else {
                $package_permissions['account_access'] = 1;
            }
            if (empty($request->sms_settings_access)) {
                $package_permissions['sms_settings_access'] = 0;
            } else {
                $package_permissions['sms_settings_access'] = 1;
            }
            if (empty($request->module_access)) {
                $package_permissions['module_access'] = 0;
            } else {
                $package_permissions['module_access'] = 1;
            }
            if (empty($request->customer_order_own_customer)) {
                $input['customer_order_own_customer'] = 0;
            } else {
                $input['customer_order_own_customer'] = 1;
            }
            if (empty($request->customer_order_general_customer)) {
                $input['customer_order_general_customer'] = 0;
            } else {
                $input['customer_order_general_customer'] = 1;
            }
            if (empty($request->mpcs_module)) {
                $input['mpcs_module'] = 0;
            } else {
                $input['mpcs_module'] = 1;
            }
            if (empty($request->fleet_module)) {
                $input['fleet_module'] = 0;
            } else {
                $input['fleet_module'] = 1;
            }
            if (empty($request->ezyboat_module)) {
                $input['ezyboat_module'] = 0;
            } else {
                $input['ezyboat_module'] = 1;
            }
            if ($request->number_of_branches == '1') {
                $input['number_of_branches'] = 1;
            } else {
                $input['number_of_branches'] = 0;
            }
            if ($request->number_of_users == '1') {
                $input['number_of_users'] = 1;
            } else {
                $input['number_of_users'] = 0;
            }
            if ($request->number_of_products == '1') {
                $input['number_of_products'] = 1;
            } else {
                $input['number_of_products'] = 0;
            }
            if ($request->number_of_periods == '1') {
                $input['number_of_periods'] = 1;
            } else {
                $input['number_of_periods'] = 0;
            }
            if ($request->number_of_customers == '1') {
                $input['number_of_customers'] = 1;
            } else {
                $input['number_of_customers'] = 0;
            }
            if ($request->monthly_total_sales == '1') {
                $input['monthly_total_sales'] = 1;
            } else {
                $input['monthly_total_sales'] = 0;
            }
            if ($request->no_of_family_members == '1') {
                $input['no_of_family_members'] = 1;
            } else {
                $input['no_of_family_members'] = 0;
            }
            if ($request->no_of_vehicles == '1') {
                $input['no_of_vehicles'] = 1;
            } else {
                $input['no_of_vehicles'] = 0;
            }
            if (empty($request->customer_interest_deduct_option)) {
                $input['customer_interest_deduct_option'] = 0;
            } else {
                $input['customer_interest_deduct_option'] = 1;
            }
            $input['leads_module'] = $request->leads_module == '1' ? 1 : 0;
            /**
             * @author: Afes Oktavianus
             * @since: 23-08-2021
             * @req: 3413 - Package Permission for Petro Module
             */
            if (empty($request->contact_module)) {
                $input['contact_module']   = 0;
                $input['contact_supplier'] = 0;
                $input['contact_customer'] = 0;
                $input['contact_group_customer'] = 0;
                $input['contact_group_supplier'] = 0;
                $input['import_contact'] = 0;
                $input['customer_reference'] = 0;
                $input['customer_statement'] = 0;
                $input['customer_payment'] = 0;
                $input['outstanding_received'] = 0;
                $input['issue_payment_detail'] = 0;
            }else{
                $input['contact_module']   = 1;
                $input['contact_supplier'] = $request->contact_supplier == '1' ? 1 : 0;
                $input['contact_customer'] = $request->contact_customer == '1' ? 1 : 0;
                $input['contact_group_customer'] = $request->contact_group_customer == '1' ? 1 : 0;
                $input['contact_group_supplier'] = $request->contact_group_supplier == '1' ? 1 : 0;
                $input['import_contact'] = $request->import_contact == '1' ? 1 : 0;
                $input['customer_reference'] = $request->customer_reference == '1' ? 1 : 0;
                $input['customer_statement'] = $request->customer_statement == '1' ? 1 : 0;
                $input['customer_payment'] = $request->customer_payment == '1' ? 1 : 0;
                $input['outstanding_received'] = $request->outstanding_received == '1' ? 1 : 0;
                $input['issue_payment_detail'] = $request->issue_payment_detail == '1' ? 1 : 0;
            }
            $input['products'] = $request->products == '1' ? 1 : 0;
            $input['issue_customer_bill'] = $request->issue_customer_bill == '1' ? 1 : 0;
            $input['purchase'] = $request->purchase == '1' ? 1 : 0;
            $input['sale_module'] = $request->sale_module == '1' ? 1 : 0;
            $input['pos_sale'] = $request->pos_sale == '1' ? 1 : 0;
            $input['repair_module'] = $request->repair_module == '1' ? 1 : 0;
            $input['stock_transfer'] = $request->stock_transfer == '1' ? 1 : 0;
            $input['expenses'] = $request->expenses == '1' ? 1 : 0;
            $input['tasks_management'] = $request->tasks_management == '1' ? 1 : 0;
            /**
             * @author: Afes Oktavianus
             * @since: 23-08-2021
             * @req: 8413 - Package Permission for Petro Module
             */
            if(empty($request->report_module)){
                $input['report_module'] = 0;
                $input['product_report'] = 0;
                $input['payment_status_report'] = 0;
                $input['report_daily'] = 0;
                $input['report_daily_summary'] = 0;
                $input['report_profit_loss'] = 0;
                $input['report_credit_status'] = 0;
                $input['activity_report'] = 0;
                $input['contact_report'] = 0;
                $input['trending_product'] = 0;
                $input['user_activity'] = 0;
                $input['report_register']=0;
            }else{
                $input['report_module'] = 1;
                $input['product_report'] = $request->product_report == '1' ? 1 : 0;
                $input['payment_status_report'] = $request->payment_status_report == '1' ? 1 : 0;
                $input['report_daily'] = $request->report_daily == '1' ? 1 : 0;
                $input['report_daily_summary'] = $request->report_daily_summary == '1' ? 1 : 0;
                $input['report_profit_loss'] = $request->report_profit_loss == '1' ? 1 : 0;
                $input['report_credit_status'] = $request->report_credit_status == '1' ? 1 : 0;
                $input['activity_report'] = $request->activity_report == '1' ? 1 : 0;
                $input['contact_report'] = $request->contact_report == '1' ? 1 : 0;
                $input['trending_product'] = $request->trending_product == '1' ? 1 : 0;
                $input['user_activity'] = $request->user_activity == '1' ? 1 : 0;
                $input['report_register']=$request->report_register == '1' ? 1 : 0;
            }
            $input['catalogue_qr'] = $request->catalogue_qr == '1' ? 1 : 0;
            $input['backup_module'] = $request->backup_module == '1' ? 1 : 0;
            $input['notification_template_module'] = $request->notification_template_module == '1' ? 1 : 0;
            $input['member_registration'] = $request->member_registration == '1' ? 1 : 0;
            $input['user_management_module'] = $request->user_management_module == '1' ? 1 : 0;
            $input['banking_module'] = $request->banking_module == '1' ? 1 : 0;
            $input['list_easy_payment'] = $request->list_easy_payment == '1' ? 1 : 0;
            $input['settings_module'] = $request->settings_module == '1' ? 1 : 0;
            $input['business_settings'] = $request->business_settings == '1' ? 1 : 0;
            $input['business_location'] = $request->business_location == '1' ? 1 : 0;
            $input['invoice_settings'] = $request->invoice_settings == '1' ? 1 : 0;
            $input['tax_rates'] = $request->tax_rates == '1' ? 1 : 0;
            $input['home_dashboard'] = $request->home_dashboard == '1' ? 1 : 0;
            $input['day_end_enable'] = $request->day_end_enable == '1' ? 1 : 0;
            /**
             * @author: Afes Oktavianus
             * @since 24-08-2021
             * @req 8413 - Package Permission for Petro Module
             */
            if(empty($request->sale_module)){
                $input['sale_module'] = 0;
                $input['all_sales'] = 0;
                $input['add_sale'] = 0;
                $input['list_pos'] = 0;
                $input['list_draft'] = 0;
                $input['list_quotation'] = 0;
                $input['list_sell_return'] = 0;
                $input['shipment'] = 0;
                $input['discount'] = 0;
                $input['reserved_stock'] = 0;
                $input['import_sale'] = 0;
                $input['upload_orders'] = 0;
                $input['list_orders'] = 0;
                $input['pos_button_on_top_belt'] = 0;
                $input['pos_sale'] = 0;
            }else{
                $input['sale_module'] = 1;
                $input['all_sales'] = $request->all_sales == '1' ? 1 : 0;
                $input['add_sale'] = $request->add_sale == '1' ? 1 : 0;
                $input['list_pos'] = $request->list_pos == '1' ? 1 : 0;
                $input['list_draft'] = $request->list_draft == '1' ? 1 : 0;
                $input['list_quotation'] = $request->list_quotation == '1' ? 1 : 0;
                $input['list_sell_return'] = $request->list_sell_return == '1' ? 1 : 0;
                $input['shipment'] = $request->shipment == '1' ? 1 : 0;
                $input['discount'] = $request->discount == '1' ? 1 : 0;
                $input['reserved_stock'] = $request->reserved_stock == '1' ? 1 : 0;
                $input['import_sale'] = $request->import_sale == '1' ? 1 : 0;
                $input['upload_orders'] = $request->upload_order == '1' ? 1 : 0;
                $input['list_orders'] = $request->list_order == '1' ? 1 : 0;
                $input['pos_button_on_top_belt'] = $request->pos_button_on_top_belt == '1' ? 1 : 0;
                $input['pos_sale'] = $request->pos_ == '1' ? 1 : 0;
            }


            /* Purchase Module */

            if(empty($request->purchase_module)){
                $input['purchase_module'] = 0;
                $input['all_purchase'] = 0;
                $input['add_purchase'] = 0;
                $input['import_purchase'] = 0;
                $input['add_bulk_purchase'] = 0;
                $input['pop_button_on_top_belt'] = 0;
                $input['purchase_return'] = 0;
            }else{
                $input['purchase_module'] = 1;
                $input['all_purchase'] = $request->all_purchase == '1' ? 1 : 0;
                $input['add_purchase'] = $request->add_purchase == '1' ? 1 : 0;
                $input['import_purchase'] = $request->import_purchase == '1' ? 1 : 0;
                $input['add_bulk_purchase'] = $request->add_bulk_purchase == '1' ? 1 : 0;
                $input['pop_button_on_top_belt'] = $request->pop_button_on_top_belt == '1' ? 1 : 0;
                $input['purchase_return'] = $request->purchase_return == '1' ? 1 : 0;
            }
            if (empty($request->property_module)) {
                $input['property_module'] = 0;
            } else {
                $input['property_module'] = 1;
            }
            if (empty($request->visitors_registration_module)) {
                $input['visitors_registration_module'] = 0;
                $input['visitors'] = 0;
                $input['visitors_registration'] = 0;
                $input['visitors_registration_setting'] = 0;
                $input['visitors_district'] = 0;
                $input['visitors_town'] = 0;
                $input['disable_all_other_module_vr'] = 0;
            } else {
                $input['visitors_registration_module'] = 1;
                $input['visitors'] = 1;
                $input['visitors_registration'] = 1;
                $input['visitors_registration_setting'] = 1;
                $input['visitors_district'] = 1;
                $input['visitors_town'] = 1;
                $input['disable_all_other_module_vr'] = 1;
            }
            $currency = System::getCurrency();
            $input['price'] = $request->price;
            $input['is_active'] = empty($input['is_active']) ? 0 : 1;
            $input['created_by'] = $request->session()->get('user.id');
            $input['package_permissions'] = !empty($package_permissions) ? json_encode(($package_permissions)) : '';
            $input['is_private'] = empty($input['is_private']) ? 0 : 1;
            $input['is_one_time'] = empty($input['is_one_time']) ? 0 : 1;
            $input['visible'] = 1;
            $input['enable_custom_link'] = empty($input['enable_custom_link']) ? 0 : 1;
            $input['custom_link'] = empty($input['enable_custom_link']) ? '' : $input['custom_link'];
            $input['custom_link_text'] = empty($input['enable_custom_link']) ? '' : $input['custom_link_text'];
            $input['hospital_business_type'] = empty($request->hospital_business_type) ? '[]' : json_encode($request->hospital_business_type);
            $input['monthly_max_sale_limit'] = $request->monthly_max_sale_limit;
            $business_id = request()->session()->get('user.business_id');
            $business = Business::where('id', $business_id)->first();
            if (empty($request->customer_interest_deduct_option)) {
                $business_details['customer_interest_deduct_option'] = 0;
            } else {
                $business_details['customer_interest_deduct_option'] = 1;
            }
            if (!empty($request->day_end_enable)) {
                $business_details['day_end_enable'] = $request->day_end_enable;
            } else {
                $business_details['day_end_enable'] = 0;
            }
            $business->fill($business_details);
            $business->save();
            $package = new Package;
            $package->fill($input);
            $package->save();

            $manage_stock_enable = $request->manage_stock_enable;
            if($manage_stock_enable == 1){
                \App\Business::query()
                ->update(['is_manged_stock_enable'=>1]);
            }
            $output = ['success' => 1, 'msg' => __('lang_v1.success')];
        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());
            $output = [
                'success' => 0,
                'msg' => __('messages.something_went_wrong')
            ];
        }
        return redirect()
            ->action('\Modules\Superadmin\Http\Controllers\PackagesController@index')
            ->with('status', $output);
    }
    /**
     * Show the specified resource.
     * @return Response
     */
    public function show()
    {
        return view('superadmin::show');
    }
    /**
     * Show the form for editing the specified resource.
     * @return Response
     */
    public function edit($id)
    {
        $packages = Package::where('id', $id)
            ->first();
        $intervals = ['days' => __('lang_v1.days'), 'months' => __('lang_v1.months'), 'years' => __('lang_v1.years')];
        $currencies = $this->businessUtil->allCurrencies();
        $permissions = $this->moduleUtil->getModuleData('superadmin_package', true);
        $is_manage_stock_enable = \App\Business::where('id',auth()->user()->business_id)->value('is_manged_stock_enable');
        
        return view('superadmin::packages.edit')->with(compact('is_manage_stock_enable','packages', 'intervals', 'permissions', 'currencies'));
    }
    /**
     * Update the specified resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function update(Request $request, $id)
    {
        if (!auth()->user()->can('superadmin')) {
            abort(403, 'Unauthorized action.');
        }
        try {
            $packages_details = $request->only(['option_variables', 'visit_count', 'currency_id', 'name', 'id', 'description', 'location_count', 'customer_count', 'employee_count', 'user_count', 'product_count', 'invoice_count', 'vehicle_count', 'store_count', 'interval', 'interval_count', 'trial_days', 'sort_order', 'is_active', 'custom_permissions', 'is_private', 'is_one_time', 'enable_custom_link', 'custom_link', 'custom_link_text']);
            $packages_details['price'] = $request->price;
            $packages_details['is_active'] = empty($packages_details['is_active']) ? 0 : 1;
            $packages_details['custom_permissions'] = empty($packages_details['custom_permissions']) ? [] : $packages_details['custom_permissions'];
            $packages_details['package_permissions'] = !empty($request->package_permissions) ? json_encode(($request->package_permissions)) : '';
            $packages_details['is_private'] = empty($packages_details['is_private']) ? 0 : 1;
            $packages_details['is_one_time'] = empty($packages_details['is_one_time']) ? 0 : 1;
            $packages_details['visible'] = 1;
            $packages_details['enable_custom_link'] = empty($packages_details['enable_custom_link']) ? 0 : 1;
            $packages_details['custom_link'] = empty($packages_details['enable_custom_link']) ? '' : $packages_details['custom_link'];
            $packages_details['custom_link_text'] = empty($packages_details['enable_custom_link']) ? '' : $packages_details['custom_link_text'];
            $packages_details['hospital_business_type'] = empty($request->hospital_business_type) ? '[]' : json_encode($request->hospital_business_type);
            $packages_details['option_variables'] = !empty($packages_details['option_variables']) ? json_encode($packages_details['option_variables']) : '[]';
            if ($request->payday == '1') {
                $packages_details['payday'] = 1;
            } else {
                $packages_details['payday'] = 0;
            }
            if ($request->sales_commission_agent == '1') {
                $packages_details['sales_commission_agent'] = 1;
            } else {
                $packages_details['sales_commission_agent'] = 0;
            }
            if ($request->restaurant == '1') {
                $packages_details['restaurant'] = 1;
            } else {
                $packages_details['restaurant'] = 0;
            }
            if ($request->booking == '1') {
                $packages_details['booking'] = 1;
            } else {
                $packages_details['booking'] = 0;
            }
            if (array_key_exists('manufacturing_module', $packages_details['custom_permissions'])) {
                $packages_details['manufacturer'] = 1;
            } else {
                $packages_details['manufacturer'] = 0;
            }
            if ($request->sms_enable == '1') {
                $packages_details['sms_enable'] = 1;
            } else {
                $packages_details['sms_enable'] = 0;
            }
            if ($request->crm_enable == '1') {
                $packages_details['crm_enable'] = 1;
            } else {
                $packages_details['crm_enable'] = 0;
            }
            if ($request->hr_module == '1') {
                $packages_details['hr_module'] = 1;
            } else {
                $packages_details['hr_module'] = 0;
            }
            if ($request->hospital_system == '1') {
                $packages_details['hospital_system'] = 1;
            } else {
                $packages_details['hospital_system'] = 0;
            }
            if ($request->enable_duplicate_invoice == '1') {
                $packages_details['enable_duplicate_invoice'] = 1;
            } else {
                $packages_details['enable_duplicate_invoice'] = 0;
            }
            if (empty($request->petro_module)) {
                $packages_details['petro_module'] = 0;
                $packages_details['meter_resetting'] = 0;
                /**
                 * @author: Afes Oktavianus
                 * @since: 23-08-2021
                 * @req: 8413 - Package Permission for Petro Module
                 */
                $packages_details['petro_dashboard'] = 0;
                $packages_details['petro_task_management'] = 0;
                $packages_details['pump_management'] = 0;
                $packages_details['pump_management_testing'] = 0;
                $packages_details['meter_reading'] = 0;
                $packages_details['pump_dashboard_opening'] = 0;
                $packages_details['pumper_management'] = 0;
                $packages_details['daily_collection'] = 0;
                $packages_details['settlement'] = 0;
                $packages_details['list_settlement'] = 0;
                $packages_details['dip_management'] = 0;
            } else {
                $packages_details['petro_module'] = 1;
                $packages_details['meter_resetting'] = $request->meter_resetting == '1' ? 1 : 0;
                /**
                 * @author: Afes Oktavianus
                 * @since: 23-08-2021
                 * @req: 8413 - Package Permission for Petro Module
                 */
                $packages_details['petro_dashboard'] = $request->petro_dashboard == '1' ? 1 : 0;
                $packages_details['petro_task_management'] = $request->petro_task_management == '1' ? 1 : 0;
                $packages_details['pump_management'] = $request->pump_management == '1' ? 1 : 0;
                $packages_details['pump_management_testing'] = $request->pump_management_testing == '1' ? 1 : 0;
                $packages_details['meter_reading'] = $request->meter_reading == '1' ? 1 : 0;
                $packages_details['pump_dashboard_opening'] = $request->pump_dashboard_opening == '1' ? 1 : 0;
                $packages_details['pumper_management'] = $request->pumper_management == '1' ? 1 : 0;
                $packages_details['daily_collection'] = $request->daily_collection == '1' ? 1 : 0;
                $packages_details['settlement'] = $request->settlement == '1' ? 1 : 0;
                $packages_details['list_settlement'] = $request->list_settlement == '1' ? 1 : 0;
                $packages_details['dip_management'] = $request->dip_management == '1' ? 1 : 0;
            }
            if ($request->pump_operator_dashboard == '1') {
                $packages_details['pump_operator_dashboard'] = 1;
            } else {
                $packages_details['pump_operator_dashboard'] = 0;
            }
            if (empty($request->account_access)) {
                $package_permissions['account_access'] = 0;
            } else {
                $package_permissions['account_access'] = 1;
            }
            if (empty($request->sms_settings_access)) {
                $package_permissions['sms_settings_access'] = 0;
            } else {
                $package_permissions['sms_settings_access'] = 1;
            }
            if (empty($request->module_access)) {
                $package_permissions['module_access'] = 0;
            } else {
                $package_permissions['module_access'] = 1;
            }
            if (empty($request->customer_order_general_customer)) {
                $packages_details['customer_order_general_customer'] = 0;
            } else {
                $packages_details['customer_order_general_customer'] = 1;
            }
            if (empty($request->customer_order_own_customer)) {
                $packages_details['customer_order_own_customer'] = 0;
            } else {
                $packages_details['customer_order_own_customer'] = 1;
            }
            if (empty($request->mpcs_module)) {
                $packages_details['mpcs_module'] = 0;
            } else {
                $packages_details['mpcs_module'] = 1;
            }
            if (empty($request->fleet_module)) {
                $packages_details['fleet_module'] = 0;
            } else {
                $packages_details['fleet_module'] = 1;
            }
            if (empty($request->ezyboat_module)) {
                $packages_details['ezyboat_module'] = 0;
            } else {
                $packages_details['ezyboat_module'] = 1;
            }
            if ($request->number_of_branches == '1') {
                $packages_details['number_of_branches'] = 1;
            } else {
                $packages_details['number_of_branches'] = 0;
            }
            if ($request->number_of_users == '1') {
                $packages_details['number_of_users'] = 1;
            } else {
                $packages_details['number_of_users'] = 0;
            }
            if ($request->number_of_products == '1') {
                $packages_details['number_of_products'] = 1;
            } else {
                $packages_details['number_of_products'] = 0;
            }
            if ($request->number_of_periods == '1') {
                $packages_details['number_of_periods'] = 1;
            } else {
                $packages_details['number_of_periods'] = 0;
            }
            if ($request->number_of_customers == '1') {
                $packages_details['number_of_customers'] = 1;
            } else {
                $packages_details['number_of_customers'] = 0;
            }
            if ($request->monthly_total_sales == '1') {
                $packages_details['monthly_total_sales'] = 1;
            } else {
                $packages_details['monthly_total_sales'] = 0;
            }
            if ($request->no_of_family_members == '1') {
                $packages_details['no_of_family_members'] = 1;
            } else {
                $packages_details['no_of_family_members'] = 0;
            }
            if ($request->no_of_vehicles == '1') {
                $packages_details['no_of_vehicles'] = 1;
            } else {
                $packages_details['no_of_vehicles'] = 0;
            }
            if (empty($request->customer_interest_deduct_option)) {
                $packages_details['customer_interest_deduct_option'] = 0;
            } else {
                $packages_details['customer_interest_deduct_option'] = 1;
            }
            $packages_details['leads_module'] = $request->leads_module == '1' ? 1 : 0;
            /**
             * @author: Afes Oktavianus
             * @since: 23-08-2021
             * @req: 3413 - Package Permission for Petro Module
             */
            if (empty($request->contact_module)) {
                $packages_details['contact_module']   = 0;
                $packages_details['contact_supplier'] = 0;
                $packages_details['contact_customer'] = 0;
                $packages_details['contact_group_customer'] = 0;
                $packages_details['contact_group_supplier'] = 0;
                $packages_details['import_contact'] = 0;
                $packages_details['customer_reference'] = 0;
                $packages_details['customer_statement'] = 0;
                $packages_details['customer_payment'] = 0;
                $packages_details['outstanding_received'] = 0;
                $packages_details['issue_payment_detail'] = 0;
            }else{
                $packages_details['contact_module']   = 1;
                $packages_details['contact_supplier'] = $request->contact_supplier == '1' ? 1 : 0;
                $packages_details['contact_customer'] = $request->contact_customer == '1' ? 1 : 0;
                $packages_details['contact_group_customer'] = $request->contact_group_customer == '1' ? 1 : 0;
                $packages_details['contact_group_supplier'] = $request->contact_group_supplier == '1' ? 1 : 0;
                $packages_details['import_contact'] = $request->import_contact == '1' ? 1 : 0;
                $packages_details['customer_reference'] = $request->customer_reference == '1' ? 1 : 0;
                $packages_details['customer_statement'] = $request->customer_statement == '1' ? 1 : 0;
                $packages_details['customer_payment'] = $request->customer_payment == '1' ? 1 : 0;
                $packages_details['outstanding_received'] = $request->outstanding_received == '1' ? 1 : 0;
                $packages_details['issue_payment_detail'] = $request->issue_payment_detail == '1' ? 1 : 0;
            }
            /**
             * @author: Afes Oktavianus
             * @since: 23-08-2021
             * @req: 8413 - Package Permission for Petro Module
             */
            if(empty($request->report_module)){
                $packages_details['report_module'] = 0;
                $packages_details['product_report'] = 0;
                $packages_details['payment_status_report'] = 0;
                $packages_details['report_daily'] = 0;
                $packages_details['report_daily_summary'] = 0;
                $packages_details['report_profit_loss'] = 0;
                $packages_details['report_credit_status'] = 0;
                $packages_details['activity_report'] = 0;
                $packages_details['contact_report'] = 0;
                $packages_details['trending_product'] = 0;
                $packages_details['user_activity'] = 0;
                $packages_details['report_register']=0;
            }else{
                $packages_details['report_module'] = 1;
                $packages_details['product_report'] = $request->product_report == '1' ? 1 : 0;
                $packages_details['payment_status_report'] = $request->payment_status_report == '1' ? 1 : 0;
                $packages_details['report_daily'] = $request->report_daily == '1' ? 1 : 0;
                $packages_details['report_daily_summary'] = $request->report_daily_summary == '1' ? 1 : 0;
                $packages_details['report_profit_loss'] = $request->report_profit_loss == '1' ? 1 : 0;
                $packages_details['report_credit_status'] = $request->report_credit_status == '1' ? 1 : 0;
                $packages_details['activity_report'] = $request->activity_report == '1' ? 1 : 0;
                $packages_details['contact_report'] = $request->contact_report == '1' ? 1 : 0;
                $packages_details['trending_product'] = $request->trending_product == '1' ? 1 : 0;
                $packages_details['user_activity'] = $request->user_activity == '1' ? 1 : 0;
                $packages_details['report_register']=$request->report_register == '1' ? 1 : 0;
            }
            $packages_details['products'] = $request->products == '1' ? 1 : 0;
            $packages_details['issue_customer_bill'] = $request->issue_customer_bill == '1' ? 1 : 0;
            $packages_details['purchase'] = $request->purchase == '1' ? 1 : 0;
            $packages_details['sale_module'] = $request->sale_module == '1' ? 1 : 0;
            $packages_details['pos_sale'] = $request->pos_sale == '1' ? 1 : 0;
            $packages_details['repair_module'] = $request->repair_module == '1' ? 1 : 0;
            $packages_details['stock_transfer'] = $request->stock_transfer == '1' ? 1 : 0;
            $packages_details['expenses'] = $request->expenses == '1' ? 1 : 0;
            $packages_details['tasks_management'] = $request->tasks_management == '1' ? 1 : 0;
            $packages_details['catalogue_qr'] = $request->catalogue_qr == '1' ? 1 : 0;
            $packages_details['backup_module'] = $request->backup_module == '1' ? 1 : 0;
            $packages_details['notification_template_module'] = $request->notification_template_module == '1' ? 1 : 0;
            $packages_details['member_registration'] = $request->member_registration == '1' ? 1 : 0;
            $packages_details['user_management_module'] = $request->user_management_module == '1' ? 1 : 0;
            $packages_details['banking_module'] = $request->banking_module == '1' ? 1 : 0;
            $packages_details['list_easy_payment'] = $request->list_easy_payment == '1' ? 1 : 0;
            $packages_details['settings_module'] = $request->settings_module == '1' ? 1 : 0;
            $packages_details['business_settings'] = $request->business_settings == '1' ? 1 : 0;
            $packages_details['business_location'] = $request->business_location == '1' ? 1 : 0;
            $packages_details['invoice_settings'] = $request->invoice_settings == '1' ? 1 : 0;
            $packages_details['tax_rates'] = $request->tax_rates == '1' ? 1 : 0;
            $packages_details['home_dashboard'] = $request->home_dashboard == '1' ? 1 : 0;
            /**
             * @author: Afes Oktavianus
             * @since 24-08-2021
             * @req 8413 - Package Permission for Petro Module
             */
            if(empty($request->sale_module)){
                $packages_details['sale_module'] = 0;
                $packages_details['all_sales'] = 0;
                $packages_details['add_sale'] = 0;
                $packages_details['list_pos'] = 0;
                $packages_details['list_draft'] = 0;
                $packages_details['list_quotation'] = 0;
                $packages_details['list_sell_return'] = 0;
                $packages_details['shipment'] = 0;
                $packages_details['discount'] = 0;
                $packages_details['reserved_stock'] = 0;
                $packages_details['import_sale'] = 0;
                $packages_details['upload_orders'] = 0;
                $packages_details['list_orders'] = 0;
                $packages_details['pos_button_on_top_belt'] = 0;
                $packages_details['pos_sale'] = 0;
            }else{
                $packages_details['sale_module'] = 1;
                $packages_details['all_sales'] = $request->all_sales == '1' ? 1 : 0;
                $packages_details['add_sale'] = $request->add_sale == '1' ? 1 : 0;
                $packages_details['list_pos'] = $request->list_pos == '1' ? 1 : 0;
                $packages_details['list_draft'] = $request->list_draft == '1' ? 1 : 0;
                $packages_details['list_quotation'] = $request->list_quotation == '1' ? 1 : 0;
                $packages_details['list_sell_return'] = $request->list_sell_return == '1' ? 1 : 0;
                $packages_details['shipment'] = $request->shipment == '1' ? 1 : 0;
                $packages_details['discount'] = $request->discount == '1' ? 1 : 0;
                $packages_details['reserved_stock'] = $request->reserved_stock == '1' ? 1 : 0;
                $packages_details['import_sale'] = $request->import_sale == '1' ? 1 : 0;
                $packages_details['upload_orders'] = $request->upload_order == '1' ? 1 : 0;
                $packages_details['list_orders'] = $request->list_order == '1' ? 1 : 0;
                $packages_details['pos_button_on_top_belt'] = $request->pos_button_on_top_belt == '1' ? 1 : 0;
                $packages_details['pos_sale'] = $request->pos_ == '1' ? 1 : 0;
            }

            /* Purchase Module */

            if(empty($request->purchase_module)){
                $packages_details['purchase_module'] = 0;
                $packages_details['all_purchase'] = 0;
                $packages_details['add_purchase'] = 0;
                $packages_details['import_purchase'] = 0;
                $packages_details['add_bulk_purchase'] = 0;
                $packages_details['pop_button_on_top_belt'] = 0;
                $packages_details['purchase_return'] = 0;
            }else{
                $packages_details['purchase_module'] = 1;
                $packages_details['all_purchase'] = $request->all_purchase == '1' ? 1 : 0;
                $packages_details['add_purchase'] = $request->add_purchase == '1' ? 1 : 0;
                $packages_details['import_purchase'] = $request->import_purchase == '1' ? 1 : 0;
                $packages_details['add_bulk_purchase'] = $request->add_bulk_purchase == '1' ? 1 : 0;
                $packages_details['pop_button_on_top_belt'] = $request->pop_button_on_top_belt == '1' ? 1 : 0;
                $packages_details['purchase_return'] = $request->purchase_return == '1' ? 1 : 0;
            }
            if ($request->property_module == '1') {
                $packages_details['property_module'] = 1;
            } else {
                $packages_details['property_module'] = 0;
            }
            if (empty($request->visitors_registration_module)) {
                $packages_details['visitors_registration_module'] = 0;
                $packages_details['visitors'] = 0;
                $packages_details['visitors_registration'] = 0;
                $packages_details['visitors_registration_setting'] = 0;
                $packages_details['visitors_district'] = 0;
                $packages_details['visitors_town'] = 0;
                $packages_details['disable_all_other_module_vr'] = 0;
            } else {
                $packages_details['visitors_registration_module'] = 1;
                $packages_details['visitors'] = 1;
                $packages_details['visitors_registration'] = 1;
                $packages_details['visitors_registration_setting'] = 1;
                $packages_details['visitors_district'] = 1;
                $packages_details['visitors_town'] = 1;
                $packages_details['disable_all_other_module_vr'] = 1;
            }
            $packages_details['day_end_enable'] = $request->day_end_enable == '1' ? 1 : 0;
            $business_id = request()->session()->get('user.business_id');
            $business = Business::whereHas('subscriptions', function($q) use ($id) {
                $q->where('package_id', $id)
                ->whereDate('end_date', '>=', \Carbon::now());
            })->where('id', $business_id)->first();
            if (!empty($request->day_end_enable)) {
                $business_details['day_end_enable'] = $request->day_end_enable;
            } else {
                $business_details['day_end_enable'] = 0;
            }
            if (empty($request->customer_interest_deduct_option)) {
                $business_details['customer_interest_deduct_option'] = 0;
            } else {
                $business_details['customer_interest_deduct_option'] = 1;
            }
            if(!is_null($business)) {
                $business->fill($business_details);
                $business->save();
            }
            $packages_details['package_permissions'] = !empty($package_permissions) ? json_encode(($package_permissions)) : '';
            $packages_details['monthly_max_sale_limit'] = $request->monthly_max_sale_limit;
            $package = Package::where('id', $id)
                ->first();
            $package->fill($packages_details);
            $package->save();
            if (!empty($request->input('update_subscriptions'))) {
                $package_details = [
                    'location_count' => $package->location_count,
                    'user_count' => $package->user_count,
                    'customer_count' => $package->customer_count,
                    'employee_count' => $package->employee_count,
                    'product_count' => $package->product_count,
                    'invoice_count' => $package->invoice_count,
                    'vehicle_count' => $package->vehicle_count,
                    'name' => $package->name,
                    'enable_sale_cmsn_agent' => $packages_details['sales_commission_agent'],
                    'enable_restaurant' => $packages_details['restaurant'],
                    'enable_booking' => $packages_details['booking'],
                    'enable_crm' => $packages_details['crm_enable'],
                    'manufacturing_module' => $packages_details['manufacturer'],
                    'mf_module' => $packages_details['manufacturer'],
                    'enable_sms' => $packages_details['sms_enable'],
                    'products' => $packages_details['products'],
                    'issue_customer_bill' => $packages_details['issue_customer_bill'],
                    'hr_module' => $packages_details['hr_module'],
                    'leads_module' => $packages_details['leads_module'],
                    'hospital_system' => $packages_details['hospital_system'],
                    'contact_module' => $packages_details['contact_module'],
                    'contact_supplier' => $packages_details['contact_supplier'],
                    'contact_customer' => $packages_details['contact_customer'],
                    'contact_group_customer' => $packages_details['contact_group_customer'],
                    'contact_group_supplier' => $packages_details['contact_group_supplier'],
                    'import_contact'    => $packages_details['import_contact'],
                    'customer_reference'=> $packages_details['customer_reference'],
                    'customer_statement' => $packages_details['customer_statement'],
                    'customer_payment' => $packages_details['customer_payment'],
                    'outstanding_received' => $packages_details['outstanding_received'],
                    'issue_payment_detail' => $packages_details['issue_payment_detail'],
                    'enable_duplicate_invoice' => $packages_details['enable_duplicate_invoice'],
                    'mpcs_module' => $packages_details['mpcs_module'],
                    'home_dashboard' => $packages_details['home_dashboard'],
                    'enable_petro_module' => $packages_details['petro_module'],
                    'enable_petro_dashboard' => $packages_details['petro_dashboard'],
                    'enable_petro_task_management' => $packages_details['petro_task_management'],
                    'enable_petro_pump_management' => $packages_details['pump_management'],
                    'enable_petro_management_testing' => $packages_details['pump_management_testing'],
                    'enable_petro_meter_reading' => $packages_details['meter_reading'],
                    'enable_petro_pump_dashboard' => $packages_details['pump_dashboard_opening'],
                    'enable_petro_pumper_management' => $packages_details['pumper_management'],
                    'enable_petro_daily_collection' => $packages_details['daily_collection'],
                    'enable_petro_settlement'       => $packages_details['settlement'],
                    'enable_petro_list_settlement' => $packages_details['list_settlement'],
                    'enable_petro_dip_management' => $packages_details['dip_management'],
                    'meter_resetting' => $packages_details['meter_resetting'],
                    'pump_operator_dashboard' => $packages_details['pump_operator_dashboard'],
                    'property_module' => $packages_details['property_module'],
                    'customer_order_own_customer' => $packages_details['customer_order_own_customer'],
                    'customer_order_general_customer' => $packages_details['customer_order_general_customer'],
                    'access_account' => $package_permissions['account_access'],
                    'access_sms_settings' => $package_permissions['sms_settings_access'],
                    'access_module' => $package_permissions['module_access'],
                    'purchase' => $package->purchase,
                    'stock_transfer' => $package->stock_transfer,
                    'service_staff' => $package->service_staff,
                    'enable_subscription' => $package->enable_subscription,
                    'add_sale' => $package->add_sale,
                    'stock_adjustment' => $package->stock_adjustment,
                    'tables' => $package->tables,
                    'type_of_service' => $package->type_of_service,
                    'pos_sale' => $package->pos_sale,
                    'expenses' => $package->expenses,
                    'modifiers' => $package->modifiers,
                    'kitchen' => $package->kitchen,
                    'banking_module' => $package->banking_module,
                    'sale_module' => $package->sale_module,
                    'all_sales' => $package->all_sales,
                    'add_sale' => $package->add_sale,
                    'list_pos' => $package->list_pos,
                    'list_draft' => $package->list_draft,
                    'list_quotation' => $package->list_quotation,
                    'list_sell_return' => $package->list_sell_return,
                    'shipment' => $package->shipment,
                    'discount' => $package->discount,
                    'repair_module' => $package->repair_module,
                    'tasks_management' => $package->tasks_management,
                    'report_module'         => $package->report_module,
                    'product_report'        => $packages_details['product_report'],
                    'payment_status_report' => $packages_details['payment_status_report'],
                    'report_daily'          => $packages_details['report_daily'],
                    'report_daily_summary'  => $packages_details['report_daily_summary'],
                    'report_profit_loss'    => $packages_details['report_profit_loss'],
                    'report_credit_status'  => $packages_details['report_credit_status'],
                    'activity_report'       => $packages_details['activity_report'],
                    'contact_report'        => $packages_details['contact_report'],
                    'trending_product'      => $packages_details['trending_product'],
                    'user_activity'         => $packages_details['user_activity'],
                    'report_register'       => $packages_details['report_register'],
                    'catalogue_qr' => $package->catalogue_qr,
                    'backup_module' => $package->backup_module,
                    'notification_template_module' => $package->notification_template_module,
                    'member_registration' => $package->member_registration,
                    'user_management_module' => $package->user_management_module,
                    'settings_module' => $package->settings_module,
                    'business_settings' => $package->business_settings,
                    'business_location' => $package->business_location,
                    'invoice_settings' => $package->invoice_settings,
                    'tax_rates' => $package->tax_rates,
                    'list_easy_payment' => $package->list_easy_payment,
                    'fleet_module' => $package->fleet_module,
                    'ezyboat_module' => $package->ezyboat_module,
                    'enable_custom_link' => $package->enable_custom_link,
                    'visitors_registration_module' => $package->visitors_registration_module,
                    'payday' => $package->payday,
                    'purchase_module' => $packages_details['purchase_module'],
                    'all_purchase' => $packages_details['all_purchase'],
                    'add_purchase' => $packages_details['add_purchase'],
                    'import_purchase' => $packages_details['import_purchase'],
                    'add_bulk_purchase' => $packages_details['add_bulk_purchase'],
                    'pop_button_on_top_belt' => $packages_details['pop_button_on_top_belt'],
                    'purchase_return' => $packages_details['purchase_return'],
                ];
                //Update subscription package details
                $subscriptions = Subscription::where('package_id', $package->id)
                    ->whereDate('end_date', '>=', \Carbon::now())
                    ->update(['package_details' => json_encode($package_details)]);
            }
            $manage_stock_enable = $request->manage_stock_enable == 1 ? : 0;
            \App\Business::query()
                ->update(['is_manged_stock_enable'=>$manage_stock_enable]);
            $output = ['success' => 1, 'msg' => __('lang_v1.success')];
        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());
            $output = [
                'success' => 0,
                'msg' => __('messages.something_went_wrong')
            ];
        }
        return redirect()
            ->action('\Modules\Superadmin\Http\Controllers\PackagesController@index')
            ->with('status', $output);
    }
    /**
     * Remove the specified resource from storage.
     * @return Response
     */
    public function destroy($id)
    {
        if (!auth()->user()->can('superadmin')) {
            abort(403, 'Unauthorized action.');
        }
        try {
            Package::where('id', $id)
                ->delete();
            $output = ['success' => 1, 'msg' => __('lang_v1.success')];
        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());
            $output = [
                'success' => 0,
                'msg' => __('messages.something_went_wrong')
            ];
        }
        return redirect()
            ->action('\Modules\Superadmin\Http\Controllers\PackagesController@index')
            ->with('status', $output);
    }
    /**
     * get option variable of resource
     *
     * @return \Illuminate\Http\Response
     */
    public function getOptionVariables(Request $request)
    {
        $option_id = $request->option_id;
        $option_variables = PackageVariable::where('variable_options', $option_id)->get();
        $selected_variables = [];
        if (!empty($request->package_id)) {
            $selected_variables = json_decode(Package::where('id', $request->package_id)->first()->option_variables);
        }
        return view('superadmin::packages.partials.option_variables')->with(compact(
            'option_variables',
            'selected_variables'
        ));
    }
}
