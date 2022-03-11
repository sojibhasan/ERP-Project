@extends('layouts.app')
@section('title', __('superadmin::lang.superadmin') . ' | ' . __('superadmin::lang.packages'))
@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>@lang('superadmin::lang.packages') <small>@lang('superadmin::lang.add_package')</small></h1>
        <!-- <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Level</a></li>
            <li class="active">Here</li>
        </ol> -->
    </section>
    <!-- Main content -->
    <section class="content">
        <!-- Page level currency setting -->
        <input type="hidden" id="p_code" value="{{$currency->code}}">
        <input type="hidden" id="p_symbol" value="{{$currency->symbol}}">
        <input type="hidden" id="p_thousand" value="{{$currency->thousand_separator}}">
        <input type="hidden" id="p_decimal" value="{{$currency->decimal_separator}}">
        {!! Form::open(['url' => action('\Modules\Superadmin\Http\Controllers\PackagesController@store'), 'method' =>
        'post', 'id' => 'add_package_form']) !!}
        <div class="box box-solid">
            <div class="box-body">
                <div class="row">
                    <input type="hidden" id="currency_symbol" name="currency_symbol" value="">
                    @foreach($permissions as $module => $module_permissions)
                        @foreach($module_permissions as $permission)
                            <div class="col-sm-3">
                                <div class="checkbox">
                                    <label>
                                        {!! Form::checkbox("custom_permissions[$permission[name]]", 1, $permission['default'],
                                        ['class' => 'input-icheck']); !!}
                                        {{$permission['label']}}
                                    </label>
                                </div>
                            </div>
                        @endforeach
                    @endforeach
                    <div class="col-sm-3 ">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('account_access', 1, false, ['class' => 'input-icheck', 'id' =>
                                'account_access']); !!}
                                {{__('superadmin::lang.account_access')}}
                            </label>
                        </div>
                    </div>
                    <div class="col-sm-3 ">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('banking_module', 1, false, ['class' => 'input-icheck' , 'id' =>
                                'banking_module']); !!}
                                {{__('superadmin::lang.banking_module')}}
                            </label>
                        </div>
                    </div>
                    <div class="col-sm-3 ">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('sms_settings_access', 1, false, ['class' => 'input-icheck']); !!}
                                {{__('superadmin::lang.sms_settings_access')}}
                            </label>
                        </div>
                    </div>
                    <div class="col-sm-3 ">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('module_access', 1, false, ['class' => 'input-icheck']); !!}
                                {{__('superadmin::lang.module_access')}}
                            </label>
                        </div>
                    </div>
                    <div class="col-sm-3 ">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('hospital_system', 1, false, ['class' => 'input-icheck', 'id' =>
                                'hospital_system']); !!}
                                {{__('superadmin::lang.hospital_system')}}
                            </label>
                        </div>
                    </div>
                    <div class="col-sm-3 ">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('restaurant', 1, false, ['class' => 'input-icheck']); !!}
                                {{__('superadmin::lang.restaurant')}}
                            </label>
                        </div>
                    </div>
                    <div class="col-sm-3 ">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('booking', 1, false, ['class' => 'input-icheck']); !!}
                                {{__('superadmin::lang.booking')}}
                            </label>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('property_module', 1, false, ['class' => 'input-icheck']); !!}
                                {{__('superadmin::lang.property_module')}}
                            </label>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('crm_enable', 1, false, ['class' => 'input-icheck']); !!}
                                {{__('superadmin::lang.crm_enable')}}
                            </label>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('hr_module', 1, false, ['class' => 'input-icheck']); !!}
                                {{__('superadmin::lang.hr_module')}}
                            </label>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('enable_duplicate_invoice', 1, false, ['class' => 'input-icheck']); !!}
                                {{__('superadmin::lang.enable_duplicate_invoice')}}
                            </label>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('sms_enable', 1, false, ['class' => 'input-icheck']); !!}
                                {{__('superadmin::lang.sms_enable')}}
                            </label>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('sales_commission_agent', 1, false, ['class' => 'input-icheck']); !!}
                                {{__('superadmin::lang.sales_commission_agent')}}
                            </label>
                        </div>
                    </div>
					{{--
                    <div class="col-sm-3">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('petro_module', 1, false, ['class' => 'input-icheck']); !!}
                                {{__('lang_v1.enable_petro')}}
                            </label>
                        </div>
                    </div>
					--}}
                    <div class="col-sm-3">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('pump_operator_dashboard', 1, false, ['class' => 'input-icheck']); !!}
                                {{__('lang_v1.pump_operator_dashboard')}}
                            </label>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('mpcs_module', 1, false, ['class' => 'input-icheck']); !!}
                                {{__('superadmin::lang.mpcs_module')}}
                            </label>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('home_dashboard', 1, false, ['class' => 'input-icheck']); !!}
                                {{__('superadmin::lang.home_dashboard')}}
                            </label>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('leads_module', 1, false, ['class' => 'input-icheck']); !!}
                                {{__('superadmin::lang.leads_module')}}
                            </label>
                        </div>
                    </div>
					{{--
                    <div class="col-sm-3">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('contact_module', 1, false, ['class' => 'input-icheck']); !!}
                                {{__('superadmin::lang.contact_module')}}
                            </label>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('contact_supplier', 1, false, ['class' => 'input-icheck']); !!}
                                {{__('superadmin::lang.contact_supplier')}}
                            </label>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('contact_customer', 1, false, ['class' => 'input-icheck']); !!}
                                {{__('superadmin::lang.contact_customer')}}
                            </label>
                        </div>
                    </div>
					--}}
                    <div class="col-sm-3">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('products', 1, false, ['class' => 'input-icheck']); !!}
                                {{__('superadmin::lang.products')}}
                            </label>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('issue_customer_bill', 1, false, ['class' => 'input-icheck']); !!}
                                {{__('superadmin::lang.issue_customer_bill')}}
                            </label>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('purchase', 1, false, ['class' => 'input-icheck']); !!}
                                {{__('superadmin::lang.purchase')}}
                            </label>
                        </div>
                    </div>
					{{--
                    <div class="col-sm-3">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('sale_module', 1, false, ['class' => 'input-icheck']); !!}
                                {{__('superadmin::lang.sale_module')}}
                            </label>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('pos_sale', 1, false, ['class' => 'input-icheck']); !!}
                                {{__('superadmin::lang.pos_sale')}}
                            </label>
                        </div>
                    </div>
					--}}
                    <div class="col-sm-3">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('repair_module', 1, false, ['class' => 'input-icheck']); !!}
                                {{__('superadmin::lang.repair_module')}}
                            </label>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('stock_transfer', 1, false, ['class' => 'input-icheck']); !!}
                                {{__('superadmin::lang.stock_transfer')}}
                            </label>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('expenses', 1, false, ['class' => 'input-icheck']); !!}
                                {{__('superadmin::lang.expenses')}}
                            </label>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('tasks_management', 1, false, ['class' => 'input-icheck']); !!}
                                {{__('superadmin::lang.tasks_management')}}
                            </label>
                        </div>
                    </div>
					{{--
                    <div class="col-sm-3">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('report_module', 1, false, ['class' => 'input-icheck']); !!}
                                {{__('superadmin::lang.report_module')}}
                            </label>
                        </div>
                    </div>
					--}}
                    <div class="col-sm-3">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('catalogue_qr', 1, false, ['class' => 'input-icheck']); !!}
                                {{__('superadmin::lang.catalogue_qr')}}
                            </label>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('backup_module', 1, false, ['class' => 'input-icheck']); !!}
                                {{__('superadmin::lang.backup_module')}}
                            </label>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('notification_template_module', 1, false, ['class' => 'input-icheck']);
                                !!}
                                {{__('superadmin::lang.notification_template_module')}}
                            </label>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('member_registration', 1, false, ['class' => 'input-icheck']); !!}
                                {{__('superadmin::lang.member_registration')}}
                            </label>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('user_management_module', 1, false, ['class' => 'input-icheck']); !!}
                                {{__('superadmin::lang.user_management_module')}}
                            </label>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('settings_module', 1, false, ['class' => 'input-icheck']); !!}
                                {{__('superadmin::lang.settings_module')}}
                            </label>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('business_settings', 1, false, ['class' => 'input-icheck']); !!}
                                {{__('superadmin::lang.business_settings')}}
                            </label>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('business_location', 1, false, ['class' => 'input-icheck']); !!}
                                {{__('superadmin::lang.business_location')}}
                            </label>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('invoice_settings', 1, false, ['class' => 'input-icheck']); !!}
                                {{__('superadmin::lang.invoice_settings')}}
                            </label>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('tax_rates', 1, false, ['class' => 'input-icheck']); !!}
                                {{__('superadmin::lang.tax_rates')}}
                            </label>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('list_easy_payment', 1, false, ['class' => 'input-icheck']); !!}
                                {{__('superadmin::lang.list_easy_payment')}}
                            </label>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('fleet_module', 1, false, ['class' => 'input-icheck', 'id' =>
                                'fleet_module']); !!}
                                {{__('superadmin::lang.fleet_module')}}
                            </label>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('ezyboat_module', 1, false, ['class' => 'input-icheck', 'id' =>
                                'ezyboat_module']); !!}
                                {{__('superadmin::lang.ezyboat_module')}}
                            </label>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('customer_order_own_customer', 1, false, ['class' => 'input-icheck']);
                                !!}
                                {{__('lang_v1.customer_order_own_customer')}}
                            </label>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('customer_order_general_customer', 1, false, ['class' =>
                                'input-icheck']); !!}
                                {{__('lang_v1.customer_order_general_customer')}}
                            </label>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('is_private', 1, false, ['class' => 'input-icheck']); !!}
                                {{__('superadmin::lang.private_superadmin_only')}}
                            </label>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('is_one_time', 1, false, ['class' => 'input-icheck']); !!}
                                {{__('superadmin::lang.one_time_only_subscription')}}
                            </label>
                        </div>
                    </div>
                    {{--
                        @author Afes Oktavianus
                        @since 2021-08-23
                    --}}
                    <div class="col-md-12">
                        <hr style="border-width:2px;color:gray;background-color:gray">
                        <div class="row">
                            <div class="col-sm-3">
                                <div class="checkbox">
                                    <label class="flex-label">
                                        {!! Form::checkbox('petro_module', 1, false, ['class' => 'input-icheck', 'id', 'petro_module']); !!}
                                        {{__('lang_v1.enable_petro')}}
                                    </label>
                                </div>
                            </div>
                            <div class="col-sm-6">
                            </div>
                            <div class="col-sm-3">
                                <div class="checkbox">
                                    <label class="flex-label">
                                        {!! Form::checkbox('all_petro_module', 1, false, ['class' => 'input-icheck', 'id'=>'all_petro_module']); !!}
                                        {{__('superadmin::lang.select_all_in_enable_petro_module')}}
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-3">
                                <div class="checkbox">
                                    <label class="flex-label" class="flex-label">
                                        {!! Form::checkbox('petro_dashboard', 1, false, ['class' => 'input-icheck', 'id'=>'petro_dashboard']); !!}
                                        {{__('superadmin::lang.petro_dashboard')}}
                                    </label>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="checkbox">
                                    <label class="flex-label">
                                        {!! Form::checkbox('petro_task_management', 1, false, ['class' => 'input-icheck', 'id'=>'petro_task_management']); !!}
                                        {{__('superadmin::lang.petro_task_management')}}
                                    </label>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="checkbox">
                                    <label class="flex-label">
                                        {!! Form::checkbox('pump_management', 1, false, ['class' => 'input-icheck','id'=>'pump_management']); !!}
                                        {{__('superadmin::lang.pump_management_tab_page')}}
                                    </label>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="checkbox">
                                    <label class="flex-label">
                                        {!! Form::checkbox('pump_management_testing', 1, false, ['class' => 'input-icheck', 'id' => 'pump_management_testing']); !!}
                                        {{__('superadmin::lang.pump_management_testing_page')}}
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-3">
                                <div class="checkbox">
                                    <label class="flex-label">
                                        {!! Form::checkbox('pump_management_meter_rese_page', 1, false, ['class' => 'input-icheck', 'id' => 'meter_resetting']); !!}
                                        {{__('superadmin::lang.meter_resetting_page')}}
                                    </label>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="checkbox">
                                    <label class="flex-label">
                                        {!! Form::checkbox('meter_reading', 1, false, ['class' => 'input-icheck', 'id' => 'meter_reading']); !!}
                                        {{__('superadmin::lang.meter_reading_page')}}
                                    </label>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="checkbox">
                                    <label class="flex-label">
                                        {!! Form::checkbox('pump_dashboard_opening', 1, false, ['class' => 'input-icheck', 'id'=>'pump_dashboard_opening']); !!}
                                        {{__('superadmin::lang.pump_dashboard_opening_page')}}
                                    </label>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="checkbox">
                                    <label class="flex-label">
                                        {!! Form::checkbox('pumper_management', 1, false, ['class' => 'input-icheck', 'id'=>'pumper_management']); !!}
                                        {{__('superadmin::lang.pump_management_sub_menu_page')}}
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-3">
                                <div class="checkbox">
                                    <label class="flex-label">
                                        {!! Form::checkbox('daily_collection', 1, false, ['class' => 'input-icheck', 'id'=>'daily_collection']); !!}
                                        {{__('superadmin::lang.daily_collection_sub_menu')}}
                                    </label>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="checkbox">
                                    <label class="flex-label">
                                        {!! Form::checkbox('settlement', 1, false, ['class' => 'input-icheck','id'=>'settlement']); !!}
                                        {{__('superadmin::lang.settlement_sub_menu')}}
                                    </label>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="checkbox">
                                    <label class="flex-label">
                                        {!! Form::checkbox('list_settlement', 1, false, ['class' => 'input-icheck', 'id'=>'list_settlement']); !!}
                                        {{__('superadmin::lang.list_settlement_sub_menu')}}
                                    </label>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="checkbox">
                                    <label class="flex-label">
                                        {!! Form::checkbox('dip_management', 1, false, ['class' => 'input-icheck', 'id'=>'dip_management']); !!}
                                        {{__('superadmin::lang.dip_management_sub_menu')}}
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                    <div class="col-md-12">
                        <hr style="border-width:2px;color:gray;background-color:gray">
                        <div class="row">
                            <div class="col-sm-3">
                                <div class="checkbox">
                                    <label class="flex-label">
                                        {!! Form::checkbox('report_module', 1, false, ['class' => 'input-icheck', 'id'=>'report_module']); !!}
                                        {{__('superadmin::lang.report_module')}}
                                    </label>
                                </div>
                            </div>
                            <div class="col-sm-6">
                            </div>
                            <div class="col-sm-3">
                                <div class="checkbox">
                                    <label class="flex-label">
                                        {!! Form::checkbox('all_report_module', 1, false, ['class' => 'input-icheck', 'id'=>'all_report_module']); !!}
                                        {{__('superadmin::lang.select_all_in_enable_report_module')}}
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-3">
                                <div class="checkbox">
                                    <label class="flex-label" class="flex-label">
                                        {!! Form::checkbox('product_report', 1, false, ['class' => 'input-icheck','id'=>'product_report']); !!}
                                        {{__('superadmin::lang.product_report_sub_menu')}}
                                    </label>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="checkbox">
                                    <label class="flex-label">
                                        {!! Form::checkbox('payment_status_repor_sub_menu', 1, false, ['class' => 'input-icheck', 'id'=>'payment_status_report']); !!}
                                        {{__('superadmin::lang.payment_status_report_sub_menu')}}
                                    </label>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="checkbox">
                                    <label class="flex-label">
                                        {!! Form::checkbox('report_daily', 1, false, ['class' => 'input-icheck', 'id'=>'report_daily']); !!}
                                        {{__('superadmin::lang.report_daily_tab_page')}}
                                    </label>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="checkbox">
                                    <label class="flex-label">
                                        {!! Form::checkbox('report_daily_summary', 1, false, ['class' => 'input-icheck','id'=>'report_daily_summary']); !!}
                                        {{__('superadmin::lang.report_daily_summary_tab_page')}}
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-3">
                                <div class="checkbox">
                                    <label class="flex-label">
                                        {!! Form::checkbox('report_register', 1, false, ['class' => 'input-icheck', 'id'=>'report_register']); !!}
                                        {{__('superadmin::lang.report_register_tab_page')}}
                                    </label>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="checkbox">
                                    <label class="flex-label">
                                        {!! Form::checkbox('report_profit_loss', 1, false, ['class' => 'input-icheck','id'=>'report_profit_loss']); !!}
                                        {{__('superadmin::lang.report_profit_loss_tab_page')}}
                                    </label>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="checkbox">
                                    <label class="flex-label">
                                        {!! Form::checkbox('report_credit_status', 1, false, ['class' => 'input-icheck','id'=>'report_credit_status']); !!}
                                        {{__('superadmin::lang.report_credit_status_tab_page')}}
                                    </label>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="checkbox">
                                    <label class="flex-label">
                                        {!! Form::checkbox('activity_report', 1, false, ['class' => 'input-icheck','id'=>'activity_report']); !!}
                                        {{__('superadmin::lang.activity_report_sub_menu')}}
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-3">
                                <div class="checkbox">
                                    <label class="flex-label">
                                        {!! Form::checkbox('contact_report', 1, false, ['class' => 'input-icheck','id'=>'contact_report']); !!}
                                        {{__('superadmin::lang.contact_report_sub_menu')}}
                                    </label>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="checkbox">
                                    <label class="flex-label">
                                        {!! Form::checkbox('trending_product', 1, false, ['class' => 'input-icheck','id'=>'trending_product']); !!}
                                        {{__('superadmin::lang.trending_product_sub_menu')}}
                                    </label>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="checkbox">
                                    <label class="flex-label">
                                        {!! Form::checkbox('user_activity', 1, false, ['class' => 'input-icheck','id'=>'user_activity']); !!}
                                        {{__('superadmin::lang.user_activity_sub_menu')}}
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                    <div class="col-md-12">
                        <hr style="border-width:2px;color:gray;background-color:gray">
                        <div class="row">
                            <div class="col-sm-3">
                                <div class="checkbox">
                                    <label class="flex-label">
                                        {!! Form::checkbox('contact_module', 1, false, ['class' => 'input-icheck', 'id'=>'contact_module']); !!}
                                        {{__('superadmin::lang.contact_module')}}
                                    </label>
                                </div>
                            </div>
                            <div class="col-sm-6">
                            </div>
                            <div class="col-sm-3">
                                <div class="checkbox">
                                    <label class="flex-label">
                                        {!! Form::checkbox('all_contact_module', 1, false, ['class' => 'input-icheck', 'id'=>'all_contact_module']); !!}
                                        {{__('superadmin::lang.select_all_in_enable_contact_module')}}
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-3">
                                <div class="checkbox">
                                    <label class="flex-label" class="flex-label">
                                        {!! Form::checkbox('contact_supplier', 1, false, ['class' => 'input-icheck','id'=>'contact_supplier']); !!}
                                        {{__('superadmin::lang.supplier_tab_page')}}
                                    </label>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="checkbox">
                                    <label class="flex-label">
                                        {!! Form::checkbox('contact_customer', 1, false, ['class' => 'input-icheck','id'=>'contact_customer']); !!}
                                        {{__('superadmin::lang.customer_tab_page')}}
                                    </label>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="checkbox">
                                    <label class="flex-label">
                                        {!! Form::checkbox('contact_group_customer', 1, false, ['class' => 'input-icheck','id'=>'contact_group_customer']); !!}
                                        {{__('superadmin::lang.contact_group_customer_tab_page')}}
                                    </label>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="checkbox">
                                    <label class="flex-label">
                                        {!! Form::checkbox('contact_group_supplier', 1, false, ['class' => 'input-icheck', 'id'=>'contact_group_supplier']); !!}
                                        {{__('superadmin::lang.contact_group_supplier_tab_page')}}
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-3">
                                <div class="checkbox">
                                    <label class="flex-label">
                                        {!! Form::checkbox('import_contact', 1, false, ['class' => 'input-icheck','id'=>'import_contact']); !!}
                                        {{__('superadmin::lang.import_contact_tab_page')}}
                                    </label>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="checkbox">
                                    <label class="flex-label">
                                        {!! Form::checkbox('customer_reference', 1, false, ['class' => 'input-icheck', 'id'=>'customer_reference']); !!}
                                        {{__('superadmin::lang.customer_reference_tab_page')}}
                                    </label>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="checkbox">
                                    <label class="flex-label">
                                        {!! Form::checkbox('customer_statement', 1, false, ['class' => 'input-icheck', 'id'=>'customer_statement']); !!}
                                        {{__('superadmin::lang.customer_statement_tab_page')}}
                                    </label>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="checkbox">
                                    <label class="flex-label">
                                        {!! Form::checkbox('customer_payment', 1, false, ['class' => 'input-icheck','id'=>'customer_payment']); !!}
                                        {{__('superadmin::lang.customer_payment_tab_page')}}
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-3">
                                <div class="checkbox">
                                    <label class="flex-label">
                                        {!! Form::checkbox('outstanding_received', 1, false, ['class' => 'input-icheck', 'id'=>'outstanding_received']); !!}
                                        {{__('superadmin::lang.outstanding_received_tab_page')}}
                                    </label>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="checkbox">
                                    <label class="flex-label">
                                        {!! Form::checkbox('issue_payment_detail', 1, false, ['class' => 'input-icheck', 'id'=>'issue_payment_detail']); !!}
                                        {{__('superadmin::lang.issue_payment_detail_tab_page')}}
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                    <div class="col-md-12">
                        <hr style="border-width:2px;color:gray;background-color:gray">
                        <div class="row">
                            <div class="col-sm-3">
                                <div class="checkbox">
                                    <label class="flex-label">
                                        {!! Form::checkbox('sale_module', 1, false, ['class' => 'input-icheck','id'=>'sale_module']); !!}
                                        {{__('superadmin::lang.sale_module')}}
                                    </label>
                                </div>
                            </div>
                            <div class="col-sm-6">
                            </div>
                            <div class="col-sm-3">
                                <div class="checkbox">
                                    <label class="flex-label">
                                        {!! Form::checkbox('all_sale_module', 1, false, ['class' => 'input-icheck', 'id'=>'all_sale_module']); !!}
                                        {{__('superadmin::lang.select_all_in_enable_sale_module')}}
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-3">
                                <div class="checkbox">
                                    <label class="flex-label" class="flex-label">
                                        {!! Form::checkbox('all_sale', 1, false, ['class' => 'input-icheck', 'id'=>'all_sale']); !!}
                                        {{__('superadmin::lang.select_all_sale_sub_menu')}}
                                    </label>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="checkbox">
                                    <label class="flex-label">
                                        {!! Form::checkbox('add_sale', 1, false, ['class' => 'input-icheck','id'=>'add_sale']); !!}
                                        {{__('superadmin::lang.add_sale_sub_menu')}}
                                    </label>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="checkbox">
                                    <label class="flex-label">
                                        {!! Form::checkbox('list_pos', 1, false, ['class' => 'input-icheck','id'=>'list_pos']); !!}
                                        {{__('superadmin::lang.list_pos_sub_menu')}}
                                    </label>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="checkbox">
                                    <label class="flex-label">
                                        {!! Form::checkbox('pos_', 1, false, ['class' => 'input-icheck','id'=>'pos_']); !!}
                                        {{__('superadmin::lang.pos_sub_menu')}}
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-3">
                                <div class="checkbox">
                                    <label class="flex-label">
                                        {!! Form::checkbox('pos_button_on_top_belt', 1, false, ['class' => 'input-icheck','id'=>'pos_button_on_top_belt']); !!}
                                        {{__('superadmin::lang.pos_button_on_top_belt')}}
                                    </label>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="checkbox">
                                    <label class="flex-label">
                                        {!! Form::checkbox('list_order', 1, false, ['class' => 'input-icheck','id'=>'list_order']); !!}
                                        {{__('superadmin::lang.list_order_sub_menu')}}
                                    </label>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="checkbox">
                                    <label class="flex-label">
                                        {!! Form::checkbox('upload_order', 1, false, ['class' => 'input-icheck','id'=>'upload_order']); !!}
                                        {{__('superadmin::lang.upload_order_sub_menu')}}
                                    </label>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="checkbox">
                                    <label class="flex-label">
                                        {!! Form::checkbox('list_sell_return', 1, false, ['class' => 'input-icheck','id'=>'list_sell_return']); !!}
                                        {{__('superadmin::lang.list_sell_return_sub_menu')}}
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-3">
                                <div class="checkbox">
                                    <label class="flex-label">
                                        {!! Form::checkbox('shipment', 1, false, ['class' => 'input-icheck', 'id'=>'shipment']); !!}
                                        {{__('superadmin::lang.shipment_sub_menu')}}
                                    </label>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="checkbox">
                                    <label class="flex-label">
                                        {!! Form::checkbox('discount', 1, false, ['class' => 'input-icheck', 'id'=>'discount']); !!}
                                        {{__('superadmin::lang.discount_sub_menu')}}
                                    </label>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="checkbox">
                                    <label class="flex-label">
                                        {!! Form::checkbox('import_sales', 1, false, ['class' => 'input-icheck','id'=>'import_sales']); !!}
                                        {{__('superadmin::lang.import_sales_sub_menu')}}
                                    </label>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="checkbox">
                                    <label class="flex-label">
                                        {!! Form::checkbox('reserved_stock', 1, false, ['class' => 'input-icheck','id'=>'reserved_stock']); !!}
                                        {{__('superadmin::lang.reserved_stock_sub_menu')}}
                                    </label>
                                </div>
                            </div>
                        </div>
                        <hr style="border-width:2px;color:gray;background-color:gray">
                    </div>
                    <div class="col-md-12">
                        <div class="row">
                            <div class="col-sm-3">
                                <div class="checkbox">
                                    <label class="flex-label">
                                        {!! Form::checkbox('purchase_module', 1, false, ['class' => 'input-icheck','id'=>'purchase_module']); !!}
                                        {{__('Purchase Module')}}
                                    </label>
                                </div>
                            </div>
                            <div class="col-sm-6">
                            </div>
                            <div class="col-sm-3">
                                <div class="checkbox">
                                    <label class="flex-label">
                                        {!! Form::checkbox('all_purchase_module', 1, false, ['class' => 'input-icheck', 'id'=>'all_purchase_module']); !!}
                                        {{__('Select All In Purchase Module')}}
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-3">
                                <div class="checkbox">
                                    <label class="flex-label" class="flex-label">
                                        {!! Form::checkbox('all_purchase', 1, false, ['class' => 'input-icheck', 'id'=>'all_purchase']); !!}
                                        {{__('All Purchas')}}
                                    </label>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="checkbox">
                                    <label class="flex-label">
                                        {!! Form::checkbox('add_purchase', 1, false, ['class' => 'input-icheck','id'=>'add_purchase']); !!}
                                        {{__('Add Purchase')}}
                                    </label>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="checkbox">
                                    <label class="flex-label">
                                        {!! Form::checkbox('add_bulk_purchase', 1, false, ['class' => 'input-icheck','id'=>'add_bulk_purchase']); !!}
                                        {{__('Add Bulk Purchase')}}
                                    </label>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="checkbox">
                                    <label class="flex-label">
                                        {!! Form::checkbox('import_purchase', 1, false, ['class' => 'input-icheck','id'=>'import_purchase']); !!}
                                        {{__('Import Purchase')}}
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-3">
                                <div class="checkbox">
                                    <label class="flex-label">
                                        {!! Form::checkbox('pop_button_on_top_belt', 1, false, ['class' => 'input-icheck','id'=>'pop_button_on_top_belt']); !!}
                                        {{__('Pop Buttonn On Top Belt')}}
                                    </label>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="checkbox">
                                    <label class="flex-label">
                                        {!! Form::checkbox('purchase_return', 1, false, ['class' => 'input-icheck','id'=>'purchase_return']); !!}
                                        {{__('Purchase Return')}}
                                    </label>
                                </div>
                            </div>
                        </div>
                        <hr style="border-width:2px;color:gray;background-color:gray">
                    </div>
                    <div class="col-sm-3">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('is_active', 1, true, ['class' => 'input-icheck']); !!}
                                {{__('superadmin::lang.is_active')}}
                            </label>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('enable_custom_link', 1, false, ['class' => 'input-icheck', 'id' =>
                                'enable_custom_link']); !!}
                                {{__('superadmin::lang.enable_custom_subscription_link')}}
                            </label>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('visitors_registration_module', 1, false, ['class' => 'input-icheck',
                                'id' =>
                                'visitors_registration_module']); !!}
                                {{__('superadmin::lang.visitors_registration_module')}}
                            </label>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('day_end_enable', 1, false , [ 'class' => 'input-icheck','id' =>	'day_end_enable']); !!} {{ __( 'superadmin::lang.day_end' ) }} </label>
                            </label>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                    <div id="custom_link_div" class="hide">
                        <div class="col-sm-3">
                            <div class="form-group">
                                {!! Form::label('custom_link', __('superadmin::lang.custom_link').':') !!}
                                {!! Form::text('custom_link', null, ['class' => 'form-control']); !!}
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                {!! Form::label('custom_link_text', __('superadmin::lang.custom_link_text').':') !!}
                                {!! Form::text('custom_link_text', null, ['class' => 'form-control']); !!}
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-3 hide hospital_business_type">
                        <div class="form-group">
                            <label>
                                {!! Form::label('name', __('superadmin::lang.hospital_business_type').':') !!}
                            </label>
                            <select name="hospital_business_type[]" id="hospital_business_type" class="form-control"
                                    multiple>
                                <option value="patient">Patient</option>
                                <option value="hosp_and_dis">Hospitals & Dispensaries</option>
                                <option value="pharmacies">Pharmacies</option>
                                <option value="laboratories">Laboratories</option>
                            </select>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                    <div class="col-md-12">
                        {!! Form::label('apply_variables', __('superadmin::lang.apply_variables').':') !!}
                        <div class="row">
                            <div class="col-md-3">
                                <div class="checkbox">
                                    <label>
                                        {!! Form::checkbox('number_of_branches', 1, false, ['class' => 'input-icheck', 'id' =>
                                        'number_of_branches']); !!}
                                        {{__('superadmin::lang.number_of_branches')}}
                                    </label>
                                </div>
                                <div class="checkbox">
                                    <label>
                                        {!! Form::checkbox('number_of_users', 1, false, ['class' => 'input-icheck', 'id' =>
                                        'number_of_users']); !!}
                                        {{__('superadmin::lang.number_of_users')}}
                                    </label>
                                </div>
                                <div class="checkbox">
                                    <label>
                                        {!! Form::checkbox('number_of_customers', 1, false, ['class' => 'input-icheck', 'id' =>
                                        'number_of_customers']); !!}
                                        {{__('superadmin::lang.number_of_customers')}}
                                    </label>
                                </div>
                                <div class="checkbox">
                                    <label>
                                        {!! Form::checkbox('number_of_products', 1, false, ['class' => 'input-icheck', 'id' =>
                                        'number_of_products']); !!}
                                        {{__('superadmin::lang.number_of_products')}}
                                    </label>
                                </div>
                                <div class="checkbox">
                                    <label>
                                        {!! Form::checkbox('number_of_periods', 1, false, ['class' => 'input-icheck', 'id' =>
                                        'number_of_periods']); !!}
                                        {{__('superadmin::lang.number_of_periods')}}
                                    </label>
                                </div>
                                <div class="checkbox">
                                    <label>
                                        {!! Form::checkbox('monthly_total_sales', 1, false, ['class' => 'input-icheck', 'id' =>
                                        'monthly_total_sales']); !!}
                                        {{__('superadmin::lang.monthly_total_sales')}}
                                    </label>
                                </div>
                                <div class="checkbox">
                                    <label>
                                        {!! Form::checkbox('no_of_family_members', 1, false, ['class' => 'input-icheck', 'id' =>
                                        'no_of_family_members']); !!}
                                        {{__('superadmin::lang.no_of_family_members')}}
                                    </label>
                                </div>
                                <div class="checkbox">
                                    <label>
                                        {!! Form::checkbox('no_of_vehicles', 1, false, ['class' => 'input-icheck', 'id' =>
                                        'no_of_vehicles']); !!}
                                        {{__('superadmin::lang.no_of_vehicles')}}
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="checkbox">
                                    <label>
                                        {!! Form::checkbox('customer_interest_deduct_option', 1, false, ['class' => 'input-icheck', 'id' =>
                                        'customer_interest_deduct_option']); !!}
                                        {{ __('superadmin::lang.customer_interest_deduct_option')}}
                                    </label>
                                </div>
                            </div>
                            <div class="col-sm-3 ">
                                <div class="checkbox">
                                    <label>
                                        {!! Form::checkbox('manage_stock_enable', 1, false, ['class' => 'input-icheck', 'id' =>
                                        'manage_stock_enable']); !!}
                                        {{__('superadmin::lang.manage_stock_enable')}}
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <hr style="color:gray;background-color:gray">
                        <div class="row">
                            <div class="col-sm-3">
                                <div class="checkbox">
                                    <label class="flex-label">
                                        {!! Form::checkbox('payday', 1, false, ['class' => 'input-icheck', 'id', 'payday']); !!}
                                        PayRoll
                                    </label>
                                </div>
                            </div>
                            <div class="col-sm-6">
                            </div>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            {!! Form::label('name', __('messages.name').':') !!}
                            {!! Form::text('name', null, ['class' => 'form-control', 'required']); !!}
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            {!! Form::label('description', __('superadmin::lang.description').':') !!}
                            {!! Form::text('description', null, ['class' => 'form-control', 'required']); !!}
                        </div>
                    </div>
                    <div class="clearfix"></div>
                    <div class="col-sm-6 location_count">
                        <div class="form-group">
                            {!! Form::label('location_count', __('superadmin::lang.location_count').':') !!}
                            {!! Form::number('location_count', null, ['class' => 'form-control', 'required', 'min' => 0]);
                            !!}
                            <span class="help-block">
							@lang('superadmin::lang.infinite_help')
						</span>
                        </div>
                    </div>
                    <div class="col-sm-6 user_count">
                        <div class="form-group">
                            {!! Form::label('user_count', __('superadmin::lang.user_count').':') !!}
                            {!! Form::number('user_count', null, ['class' => 'form-control', 'required', 'min' => 0]); !!}
                            <span class="help-block">
							@lang('superadmin::lang.infinite_help')
						</span>
                        </div>
                    </div>
                    <div class="col-sm-6 customer_count">
                        <div class="form-group">
                            {!! Form::label('customer_count', __('superadmin::lang.customer_count').':') !!}
                            {!! Form::number('customer_count', $default_number_of_customers, ['class' => 'form-control',
                            'required', 'min' => 0]); !!}
                            <span class="help-block">
							@lang('superadmin::lang.infinite_help')
						</span>
                        </div>
                    </div>
                    <div class="col-sm-6 hide employee_count">
                        <div class="form-group">
                            {!! Form::label('employee_count', __('superadmin::lang.employee_count').':') !!}
                            {!! Form::number('employee_count', null, ['class' => 'form-control', 'required', 'min' => 0]);
                            !!}
                            <span class="help-block">
							@lang('superadmin::lang.infinite_help')
						</span>
                        </div>
                    </div>
                    <div class="col-sm-6 product_count">
                        <div class="form-group">
                            {!! Form::label('product_count', __('superadmin::lang.product_count').':') !!}
                            {!! Form::number('product_count', null, ['class' => 'form-control', 'required', 'min' => 0]);
                            !!}
                            <span class="help-block">
							@lang('superadmin::lang.infinite_help')
						</span>
                        </div>
                    </div>
                    <div class="col-sm-6 invoice_count">
                        <div class="form-group">
                            {!! Form::label('invoice_count', __('superadmin::lang.invoice_count').':') !!}
                            {!! Form::number('invoice_count', null, ['class' => 'form-control', 'required', 'min' => 0]);
                            !!}
                            <span class="help-block">
							@lang('superadmin::lang.infinite_help')
						</span>
                        </div>
                    </div>
                    <div class="col-sm-6 store_count">
                        <div class="form-group">
                            {!! Form::label('store_count', __('superadmin::lang.store_count').':') !!}
                            {!! Form::number('store_count', null, ['class' => 'form-control', 'required', 'min' => 0]); !!}
                            <span class="help-block">
							@lang('superadmin::lang.infinite_help')
						</span>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            {!! Form::label('interval', __('superadmin::lang.interval').':') !!}
                            {!! Form::select('interval', $intervals, null, ['class' => 'form-control select2', 'placeholder'
                            =>
                            __('messages.please_select'), 'required']); !!}
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            {!! Form::label('interval_count ', __('superadmin::lang.interval_count').':') !!}
                            {!! Form::number('interval_count', null, ['class' => 'form-control', 'required', 'min' => 1]);
                            !!}
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            {!! Form::label('trial_days ', __('superadmin::lang.trial_days').':') !!}
                            {!! Form::number('trial_days', null, ['class' => 'form-control', 'required', 'min' => 0]); !!}
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            {!! Form::label('price', __('superadmin::lang.price').':') !!}
                            @show_tooltip(__('superadmin::lang.tooltip_pkg_price'))
                            <div class="input-group">
							<span class="input-group-addon" id="basic-addon3"><b>{{$currency->code}}
                                    {{$currency->symbol}}</b></span>
                                {!! Form::text('price', null, ['class' => 'form-control input_number', 'required']); !!}
                            </div>
                            <span class="help-block">
							0 = @lang('superadmin::lang.free_package')
						</span>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="currency_id">Currency:</label>
                            <div class="input-group">
							<span class="input-group-addon">
								<i class="fa fa-money"></i>
							</span>
                                {!! Form::select('currency_id', $currencies,'', ['class' => 'form-control
                                select2_register','placeholder' => __('business.currency_placeholder'), 'required', 'id' =>
                                'currency_id']); !!}
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            {!! Form::label('sort_order ', __('superadmin::lang.sort_order').':') !!}
                            {!! Form::number('sort_order', 1, ['class' => 'form-control', 'required']); !!}
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            {!! Form::label('monthly_max_sale_limit ', __('superadmin::lang.monthly_max_sale_limit').':') !!}
                            {!! Form::number('monthly_max_sale_limit', '', ['class' => 'form-control', 'required']); !!}
                        </div>
                    </div>
                    <div class="col-sm-6 vehicle_count hide">
                        <div class="form-group">
                            {!! Form::label('vehicle_count', __('superadmin::lang.no_of_vehicle').':') !!}
                            {!! Form::number('vehicle_count', null, ['class' => 'form-control', 'required', 'min' => 0]);
                            !!}
                            <span class="help-block">
							@lang('superadmin::lang.infinite_help')
						</span>
                        </div>
                    </div>
                    <div class="col-sm-6 hide visit_count">
                        <div class="form-group">
                            {!! Form::label('visit_count ', __('superadmin::lang.visit_count').':') !!}
                            {!! Form::number('visit_count', null, ['class' => 'form-control', 'required']);
                            !!}
                        </div>
                    </div>
                    <div class="clearfix"></div>
                    {{-- <div class="col-sm-6">
                <div class="form-group">
                    {!! Form::label('package_permissions ', __('superadmin::lang.package_permissions').':') !!}
                    <select name="package_permissions[]" id="customer_permissions" class="form-control" multiple>
                        <option value="modules.access">@lang('superadmin::lang.access_modules')</option>
                        <option value="sms_settings.access">@lang('superadmin::lang.sms_settings')</option>
                        <option value="accounts.access">@lang('superadmin::lang.account_module')</option>
                    </select>
                </div>
            </div> --}}
                </div>
                <div class="row">
                    <div class="col-sm-12">
                        <button type="submit"
                                class="btn btn-primary pull-right btn-flat">@lang('messages.save')</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade branch_veriables_modal" tabindex="-1" role="dialog"
             aria-labelledby="gridSystemModalLabel">
        </div>
        <div class="modal fade user_veriables_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
        </div>
        <div class="modal fade product_veriables_modal" tabindex="-1" role="dialog"
             aria-labelledby="gridSystemModalLabel">
        </div>
        <div class="modal fade period_veriables_modal" tabindex="-1" role="dialog"
             aria-labelledby="gridSystemModalLabel">
        </div>
        <div class="modal fade customer_veriables_modal" tabindex="-1" role="dialog"
             aria-labelledby="gridSystemModalLabel">
        </div>
        <div class="modal fade total_sales_veriables_modal" tabindex="-1" role="dialog"
             aria-labelledby="gridSystemModalLabel">
        </div>
        <div class="modal fade no_of_family_members" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
        </div>
        <div class="modal fade no_of_vehicles" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
        </div>
        {!! Form::close() !!}
    </section>
@endsection
@section('javascript')
    <script type="text/javascript">
        $(document).ready(function () {
            $('form#add_package_form').validate();
        });
        $('#enable_custom_link').on('ifChecked', function (event) {
            $("div#custom_link_div").removeClass('hide');
        });
        $('#enable_custom_link').on('ifUnchecked', function (event) {
            $("div#custom_link_div").addClass('hide');
        });
        $('#hr_module').on('ifChecked', function (event) {
            $("div.employee_count").removeClass('hide');
        });
        $('#hr_module').on('ifUnchecked', function (event) {
            $("div.employee_count").addClass('hide');
        });
        $('#fleet_module').on('ifChecked', function (event) {
            $("div.vehicle_count").removeClass('hide');
            $("div.product_count").addClass('hide');
            $("div.store_count").addClass('hide');
        });
        $('#fleet_module').on('ifUnchecked', function (event) {
            $("div.vehicle_count").addClass('hide');
            $("div.product_count").removeClass('hide');
            $("div.store_count").removeClass('hide');
        });
        $('#hospital_system').on('ifChecked', function (event) {
            $(".hospital_business_type").removeClass('hide');
            $('.location_count, .user_count, .product_count, .invoice_count, .store_count').addClass('hide');
            $('.user_count').removeClass('hide');
            $('.user_count').children().find('label').text('Number of Family Members:');
            if ($('#hospital_business_type').val() !== null) {
                if ($('#hospital_business_type').val().includes('patient')) {
                    $('.user_count').children().find('label').text('Number of Family Members:');
                    $('.user_count').removeClass('hide');
                }
            }
        });
        $('#hospital_system').on('ifUnchecked', function (event) {
            $(".hospital_business_type").addClass('hide');
            $('.visit_count').addClass('hide');
            $('.location_count, .user_count, .product_count, .invoice_count, .store_count').removeClass('hide');
            $('.user_count').children().find('label').text('Number of active users:');
        });
        $('#hospital_business_type').change(function () {
            if ($(this).val().includes('patient')) {
                $('.location_count, .product_count, .invoice_count, .store_count').addClass('hide');
                $('.visit_count').removeClass('hide');
                $('.fazool_clearfix').removeClass('clearfix');
                $('.user_count').children().find('label').text('Number of Family Members:');
            } else {
                $('.location_count, .user_count, .product_count, .invoice_count, .store_count').removeClass('hide');
                $('.visit_count').addClass('hide');
                $('.fazool_clearfix').addClass('clearfix');
                $('.user_count').children().find('label').text('Number of active users:');
            }
        });
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $('#number_of_branches').on('ifChecked', function (event) {
            $.ajax({
                method: 'get',
                url: "{{action('\Modules\Superadmin\Http\Controllers\PackagesController@getOptionVariables')}}",
                data: {option_id: 0},
                success: function (result) {
                    $("div.branch_veriables_modal").html(result);
                },
            });
            $("div.branch_veriables_modal").modal('show');
        });
        $('#number_of_users').on('ifChecked', function (event) {
            $.ajax({
                method: 'get',
                url: "{{action('\Modules\Superadmin\Http\Controllers\PackagesController@getOptionVariables')}}",
                data: {option_id: 1},
                success: function (result) {
                    $("div.user_veriables_modal").html(result);
                },
            });
            $("div.user_veriables_modal").modal('show');
        });
        $('#number_of_products').on('ifChecked', function (event) {
            $.ajax({
                method: 'get',
                url: "{{action('\Modules\Superadmin\Http\Controllers\PackagesController@getOptionVariables')}}",
                data: {option_id: 2},
                success: function (result) {
                    $("div.product_veriables_modal").html(result);
                },
            });
            $("div.product_veriables_modal").modal('show');
        });
        $('#number_of_periods').on('ifChecked', function (event) {
            $.ajax({
                method: 'get',
                url: "{{action('\Modules\Superadmin\Http\Controllers\PackagesController@getOptionVariables')}}",
                data: {option_id: 3},
                success: function (result) {
                    $("div.period_veriables_modal").html(result);
                },
            });
            $("div.period_veriables_modal").modal('show');
        });
        $('#number_of_customers').on('ifChecked', function (event) {
            $.ajax({
                method: 'get',
                url: "{{action('\Modules\Superadmin\Http\Controllers\PackagesController@getOptionVariables')}}",
                data: {option_id: 4},
                success: function (result) {
                    $("div.customer_veriables_modal").html(result);
                },
            });
            $("div.customer_veriables_modal").modal('show');
        });
        $('#monthly_total_sales').on('ifChecked', function (event) {
            $.ajax({
                method: 'get',
                url: "{{action('\Modules\Superadmin\Http\Controllers\PackagesController@getOptionVariables')}}",
                data: {option_id: 5},
                success: function (result) {
                    $("div.total_sales_veriables_modal").html(result);
                },
            });
            $("div.total_sales_veriables_modal").modal('show');
        });
        $('#no_of_family_members').on('ifChecked', function (event) {
            $.ajax({
                method: 'get',
                url: "{{action('\Modules\Superadmin\Http\Controllers\PackagesController@getOptionVariables')}}",
                data: {option_id: 6},
                success: function (result) {
                    $("div.no_of_family_members").html(result);
                },
            });
            $("div.no_of_family_members").modal('show');
        });
        $('#no_of_vehicles').on('ifChecked', function (event) {
            $.ajax({
                method: 'get',
                url: "{{action('\Modules\Superadmin\Http\Controllers\PackagesController@getOptionVariables')}}",
                data: {option_id: 7},
                success: function (result) {
                    $("div.no_of_vehicles").html(result);
                },
            });
            $("div.no_of_vehicles").modal('show');
        });
        $('#customer_interest_deduct_option').on('ifChecked', function (event) {
            $("div.customer_interest_deduct_option").removeClass('hide');
        });
        $('#customer_interest_deduct_option').on('ifUnchecked', function (event) {
            $("div.customer_interest_deduct_option").addClass('hide');
        });
    </script>
    <script>
        $('#currency_id').change(function () {
            var currency_id = $('#currency_id').val();
            $.ajax({
                method: 'post',
                url: '{{route("site_settings.getcurrency")}}',
                dataType: 'json',
                data: {
                    currency_id: currency_id
                },
                success: function (result) {
                    $('#currency_symbol').val(result.symbol);
                },
            });
        });
        $('#account_access').on('ifChecked', function (event) {
            $('#banking_module').iCheck('disable');
        });
        $('#account_access').on('ifUnchecked', function (event) {
            $('#banking_module').iCheck('enable');
        });
        $('#banking_module').on('ifChecked', function (event) {
            $('#account_access').iCheck('disable');
        });
        $('#banking_module').on('ifUnchecked', function (event) {
            $('#account_access').iCheck('enable');
        });
    </script>
    <script>
        /**
         * @author Afes Oktavianus
         * @since 23-08-2021
         * @req 3413 - Package Permission for Petro Module
         */
         $('#all_petro_module').on('ifChecked', function (event) {
            $('#petro_dashboard').iCheck('check');
            $('#petro_task_management').iCheck('check');
            $('#pump_management').iCheck('check');
            $('#pump_management_testing').iCheck('check');
            $('#meter_resetting').iCheck('check');
            $('#meter_reading').iCheck('check');
            $('#pump_dashboard_opening').iCheck('check');
            $('#pumper_management').iCheck('check');
            $('#daily_collection').iCheck('check');
            $('#settlement').iCheck('check');
            $('#list_settlement').iCheck('check');
            $('#dip_management').iCheck('check');
        })
        $('#all_petro_module').on('ifUnchecked', function (event) {
            $('#petro_dashboard').iCheck('uncheck');
            $('#petro_task_management').iCheck('uncheck');
            $('#pump_management').iCheck('uncheck');
            $('#pump_management_testing').iCheck('uncheck');
            $('#meter_resetting').iCheck('uncheck');
            $('#meter_reading').iCheck('uncheck');
            $('#pump_dashboard_opening').iCheck('uncheck');
            $('#pumper_management').iCheck('uncheck');
            $('#daily_collection').iCheck('uncheck');
            $('#settlement').iCheck('uncheck');
            $('#list_settlement').iCheck('uncheck');
            $('#dip_management').iCheck('uncheck');
        })
        $('#all_report_module').on('ifChecked', function (event) {
            $('#product_report').iCheck('check');
            $('#payment_status_report').iCheck('check');
            $('#report_daily').iCheck('check');
            $('#report_daily_summary').iCheck('check');
            $('#report_register').iCheck('check');
            $('#report_profit_loss').iCheck('check');
            $('#report_credit_status').iCheck('check');
            $('#activity_report').iCheck('check');
            $('#contact_report').iCheck('check');
            $('#trending_product').iCheck('check');
            $('#user_activity').iCheck('check');
        })
        $('#all_report_module').on('ifUnchecked', function (event) {
            $('#product_report').iCheck('uncheck');
            $('#payment_status_report').iCheck('uncheck');
            $('#report_daily').iCheck('uncheck');
            $('#report_daily_summary').iCheck('uncheck');
            $('#report_register').iCheck('uncheck');
            $('#report_profit_loss').iCheck('uncheck');
            $('#report_credit_status').iCheck('uncheck');
            $('#activity_report').iCheck('uncheck');
            $('#contact_report').iCheck('uncheck');
            $('#trending_product').iCheck('uncheck');
            $('#user_activity').iCheck('uncheck');
        })
        $('#all_contact_module').on('ifChecked', function (event) {
            $('#contact_supplier').iCheck('check');
            $('#contact_customer').iCheck('check');
            $('#contact_group_customer').iCheck('check');
            $('#contact_group_supplier').iCheck('check');
            $('#import_contact').iCheck('check');
            $('#customer_reference').iCheck('check');
            $('#customer_statement').iCheck('check');
            $('#customer_payment').iCheck('check');
            $('#outstanding_received').iCheck('check');
            $('#issue_payment_detail').iCheck('check');
        })
        $('#all_contact_module').on('ifUnchecked', function (event) {
            $('#contact_supplier').iCheck('uncheck');
            $('#contact_customer').iCheck('uncheck');
            $('#contact_group_customer').iCheck('uncheck');
            $('#contact_group_supplier').iCheck('uncheck');
            $('#import_contact').iCheck('uncheck');
            $('#customer_reference').iCheck('uncheck');
            $('#customer_statement').iCheck('uncheck');
            $('#customer_payment').iCheck('uncheck');
            $('#outstanding_received').iCheck('uncheck');
            $('#issue_payment_detail').iCheck('uncheck');
        })
        $('#all_sale_module').on('ifChecked', function (event) {
            $('#all_sale').iCheck('check');
            $('#add_sale').iCheck('check');
            $('#list_pos').iCheck('check');
            $('#pos_').iCheck('check');
            $('#pos_button_on_top_belt').iCheck('check');
            $('#list_order').iCheck('check');
            $('#upload_order').iCheck('check');
            $('#list_sell_return').iCheck('check');
            $('#shipment').iCheck('check');
            $('#discount').iCheck('check');
            $('#import_sales').iCheck('check');
            $('#reserved_stock').iCheck('check');
        })
        $('#all_sale_module').on('ifUnchecked', function (event) {
            $('#all_sale').iCheck('uncheck');
            $('#add_sale').iCheck('uncheck');
            $('#list_pos').iCheck('uncheck');
            $('#pos_').iCheck('uncheck');
            $('#pos_button_on_top_belt').iCheck('uncheck');
            $('#list_order').iCheck('uncheck');
            $('#upload_order').iCheck('uncheck');
            $('#list_sell_return').iCheck('uncheck');
            $('#shipment').iCheck('uncheck');
            $('#discount').iCheck('uncheck');
            $('#import_sales').iCheck('uncheck');
            $('#reserved_stock').iCheck('uncheck');
        });
        $('#all_purchase_module').on('ifChecked', function (event) {
            $('#purchase_module').iCheck('check');
            $('#all_purchase').iCheck('check');
            $('#add_purchase').iCheck('check');
            $('#add_bulk_purchase').iCheck('check');
            $('#import_purchase').iCheck('check');
            $('#pop_button_on_top_belt').iCheck('check');
            $('#purchase_return').iCheck('check');
        })
        $('#all_purchase_module').on('ifUnchecked', function (event) {
            $('#purchase_module').iCheck('uncheck');
            $('#all_purchase').iCheck('uncheck');
            $('#add_purchase').iCheck('uncheck');
            $('#add_bulk_purchase').iCheck('uncheck');
            $('#import_purchase').iCheck('uncheck');
            $('#pop_button_on_top_belt').iCheck('uncheck');
            $('#purchase_return').iCheck('uncheck');
        })
    </script>
@endsection
