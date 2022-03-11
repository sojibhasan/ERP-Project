@extends('layouts.app')
@section('title', __('superadmin::lang.superadmin') . ' | ' . __('superadmin::lang.packages'))
@section('content')
	<!-- Content Header (Page header) -->
	<section class="content-header">
		<h1>@lang('superadmin::lang.packages') <small>@lang('superadmin::lang.edit_package')</small></h1>
		<!-- <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Level</a></li>
            <li class="active">Here</li>
        </ol> -->
	</section>
	<!-- Main content -->
	<section class="content">
		{!! Form::open(['route' => ['packages.update', $packages->id], 'method' => 'put', 'id' => 'edit_package_form']) !!}
		<div class="box box-solid">
			<div class="box-body">
				<div class="row">
					<input type="hidden" id="currency_symbol" name="currency_symbol" value="{{$packages->currency_symbol}}">
					@foreach($permissions as $module => $module_permissions)
						@foreach($module_permissions as $permission)
							@php
								$value = isset($packages->custom_permissions[$permission['name']]) ?
                                $packages->custom_permissions[$permission['name']] : false;
							@endphp
							<div class="col-sm-3">
								<div class="checkbox">
									<label>
										{!! Form::checkbox("custom_permissions[$permission[name]]", 1, $value, ['class' =>
                                        'input-icheck']); !!}
										{{$permission['label']}}
									</label>
								</div>
							</div>
						@endforeach
					@endforeach
					@php
						if(!empty($packages->package_permissions)){
                        $package_permissions = json_decode($packages->package_permissions);
                        }
					@endphp
					<div class="col-sm-3 ">
						<div class="checkbox">
							<label>
								{!! Form::checkbox('account_access', 1, $package_permissions->account_access, ['class' =>
                                'input-icheck', 'id' => 'account_access']); !!}
								{{__('superadmin::lang.account_access')}}
							</label>
						</div>
					</div>
					<div class="col-sm-3 ">
						<div class="checkbox">
							<label>
								{!! Form::checkbox('banking_module', 1, $packages->banking_module, ['class' =>
                                'input-icheck', 'id' => 'banking_module']); !!}
								{{__('superadmin::lang.banking_module')}}
							</label>
						</div>
					</div>
					<div class="col-sm-3 ">
						<div class="checkbox">
							<label>
								{!! Form::checkbox('sms_settings_access', 1, $package_permissions->sms_settings_access,
                                ['class' => 'input-icheck']); !!}
								{{__('superadmin::lang.sms_settings_access')}}
							</label>
						</div>
					</div>
					<div class="col-sm-3 ">
						<div class="checkbox">
							<label>
								{!! Form::checkbox('module_access', 1, $package_permissions->module_access, ['class' =>
                                'input-icheck']); !!}
								{{__('superadmin::lang.module_access')}}
							</label>
						</div>
					</div>
					<div class="col-sm-3 ">
						<div class="checkbox">
							<label>
								{!! Form::checkbox('hospital_system', 1, $packages->hospital_system, ['class' =>
                                'input-icheck' , 'id' => 'hospital_system']); !!}
								{{__('superadmin::lang.hospital_system')}}
							</label>
						</div>
					</div>
					<div class="col-sm-3 ">
						<div class="checkbox">
							<label>
								{!! Form::checkbox('restaurant', 1, $packages->restaurant, ['class' => 'input-icheck']); !!}
								{{__('superadmin::lang.restaurant')}}
							</label>
						</div>
					</div>
					<div class="col-sm-3 ">
						<div class="checkbox">
							<label>
								{!! Form::checkbox('booking', 1, $packages->booking, ['class' => 'input-icheck']); !!}
								{{__('superadmin::lang.booking')}}
							</label>
						</div>
					</div>
					<div class="col-sm-3 ">
						<div class="checkbox">
							<label>
								{!! Form::checkbox('hr_module', 1, $packages->hr_module, ['class' => 'input-icheck', 'id' =>
                                'hr_module']); !!}
								{{__('superadmin::lang.hr_module')}}
							</label>
						</div>
					</div>
					<div class="col-sm-3 ">
						<div class="checkbox">
							<label>
								{!! Form::checkbox('enable_duplicate_invoice', 1, $packages->enable_duplicate_invoice,
                                ['class' => 'input-icheck']); !!}
								{{__('superadmin::lang.enable_duplicate_invoice')}}
							</label>
						</div>
					</div>
					<div class="col-sm-3 ">
						<div class="checkbox">
							<label>
								{!! Form::checkbox('sms_enable', 1, $packages->sms_enable, ['class' => 'input-icheck']); !!}
								{{__('superadmin::lang.sms_enable')}}
							</label>
						</div>
					</div>
					<div class="col-sm-3">
						<div class="checkbox">
							<label>
								{!! Form::checkbox('sales_commission_agent', 1, $packages->sales_commission_agent, ['class'
                                =>
                                'input-icheck']); !!}
								{{__('superadmin::lang.sales_commission_agent')}}
							</label>
						</div>
					</div>
					<div class="col-sm-3">
						<div class="checkbox">
							<label>
								{!! Form::checkbox('property_module', 1, $packages->property_module, ['class' =>
                                'input-icheck']); !!}
								{{__('superadmin::lang.property_module')}}
							</label>
						</div>
					</div>
					<div class="col-sm-3">
						<div class="checkbox">
							<label>
								{!! Form::checkbox('crm_enable', 1, $packages->crm_enable, ['class' => 'input-icheck']); !!}
								{{__('superadmin::lang.crm_enable')}}
							</label>
						</div>
					</div>
                    {{--
					<div class="col-sm-3">
						<div class="checkbox">
							<label>
								{!! Form::checkbox('petro_module', 1, $packages->petro_module, ['class' => 'input-icheck']);
                                !!}
								{{__('lang_v1.enable_petro')}}
							</label>
						</div>
					</div>
                    --}}
					<div class="col-sm-3">
						<div class="checkbox">
							<label>
								{!! Form::checkbox('pump_operator_dashboard', 1, $packages->pump_operator_dashboard,
                                ['class' => 'input-icheck']); !!}
								{{__('lang_v1.pump_operator_dashboard')}}
							</label>
						</div>
					</div>
					<div class="col-sm-3">
						<div class="checkbox">
							<label>
								{!! Form::checkbox('mpcs_module', 1, $packages->mpcs_module, ['class' => 'input-icheck']);
                                !!}
								{{__('superadmin::lang.mpcs_module')}}
							</label>
						</div>
					</div>
					<div class="col-sm-3">
						<div class="checkbox">
							<label>
								{!! Form::checkbox('home_dashboard', 1, $packages->home_dashboard, ['class' =>
                                'input-icheck']); !!}
								{{__('superadmin::lang.home_dashboard')}}
							</label>
						</div>
					</div>
					<div class="col-sm-3">
						<div class="checkbox">
							<label>
								{!! Form::checkbox('leads_module', 1, $packages->leads_module, ['class' => 'input-icheck']);
                                !!}
								{{__('superadmin::lang.leads_module')}}
							</label>
						</div>
					</div>
					<div class="col-sm-3">
						<div class="checkbox">
							<label>
								{!! Form::checkbox('products', 1, $packages->products, ['class' => 'input-icheck']); !!}
								{{__('superadmin::lang.products')}}
							</label>
						</div>
					</div>
					<div class="col-sm-3">
						<div class="checkbox">
							<label>
								{!! Form::checkbox('issue_customer_bill', 1, $packages->issue_customer_bill, ['class' =>
                                'input-icheck']); !!}
								{{__('superadmin::lang.issue_customer_bill')}}
							</label>
						</div>
					</div>
					<div class="col-sm-3">
						<div class="checkbox">
							<label>
								{!! Form::checkbox('purchase', 1, $packages->purchase, ['class' => 'input-icheck']); !!}
								{{__('superadmin::lang.purchase')}}
							</label>
						</div>
					</div>
                    {{--
					<div class="col-sm-3">
						<div class="checkbox">
							<label>
								{!! Form::checkbox('sale_module', 1, $packages->sale_module, ['class' => 'input-icheck']);
                                !!}
								{{__('superadmin::lang.sale_module')}}
							</label>
						</div>
					</div>
					<div class="col-sm-3">
						<div class="checkbox">
							<label>
								{!! Form::checkbox('pos_sale', 1, $packages->pos_sale, ['class' => 'input-icheck']);
                                !!}
								{{__('superadmin::lang.pos_sale')}}
							</label>
						</div>
					</div>
                    --}}
					<div class="col-sm-3">
						<div class="checkbox">
							<label>
								{!! Form::checkbox('repair_module', 1, $packages->repair_module, ['class' =>
                                'input-icheck']); !!}
								{{__('superadmin::lang.repair_module')}}
							</label>
						</div>
					</div>
					<div class="col-sm-3">
						<div class="checkbox">
							<label>
								{!! Form::checkbox('stock_transfer', 1, $packages->stock_transfer, ['class' =>
                                'input-icheck']); !!}
								{{__('superadmin::lang.stock_transfer')}}
							</label>
						</div>
					</div>
					<div class="col-sm-3">
						<div class="checkbox">
							<label>
								{!! Form::checkbox('expenses', 1, $packages->expenses, ['class' => 'input-icheck']); !!}
								{{__('superadmin::lang.expenses')}}
							</label>
						</div>
					</div>
					<div class="col-sm-3">
						<div class="checkbox">
							<label>
								{!! Form::checkbox('tasks_management', 1, $packages->tasks_management, ['class' =>
                                'input-icheck']); !!}
								{{__('superadmin::lang.tasks_management')}}
							</label>
						</div>
					</div>
					<div class="col-sm-3">
						<div class="checkbox">
							<label>
								{!! Form::checkbox('catalogue_qr', 1, $packages->catalogue_qr, ['class' => 'input-icheck']);
                                !!}
								{{__('superadmin::lang.catalogue_qr')}}
							</label>
						</div>
					</div>
					<div class="col-sm-3">
						<div class="checkbox">
							<label>
								{!! Form::checkbox('backup_module', 1, $packages->backup_module, ['class' =>
                                'input-icheck']); !!}
								{{__('superadmin::lang.backup_module')}}
							</label>
						</div>
					</div>
					<div class="col-sm-3">
						<div class="checkbox">
							<label>
								{!! Form::checkbox('notification_template_module', 1,
                                $packages->notification_template_module, ['class' => 'input-icheck']); !!}
								{{__('superadmin::lang.notification_template_module')}}
							</label>
						</div>
					</div>
					<div class="col-sm-3">
						<div class="checkbox">
							<label>
								{!! Form::checkbox('member_registration', 1, $packages->member_registration, ['class' =>
                                'input-icheck']); !!}
								{{__('superadmin::lang.member_registration')}}
							</label>
						</div>
					</div>
					<div class="col-sm-3">
						<div class="checkbox">
							<label>
								{!! Form::checkbox('user_management_module', 1, $packages->user_management_module, ['class'
                                => 'input-icheck']); !!}
								{{__('superadmin::lang.user_management_module')}}
							</label>
						</div>
					</div>
					<div class="col-sm-3">
						<div class="checkbox">
							<label>
								{!! Form::checkbox('settings_module', 1, $packages->settings_module, ['class' =>
                                'input-icheck']); !!}
								{{__('superadmin::lang.settings_module')}}
							</label>
						</div>
					</div>
					<div class="col-sm-3">
						<div class="checkbox">
							<label>
								{!! Form::checkbox('business_settings', 1, $packages->business_settings, ['class' =>
                                'input-icheck']); !!}
								{{__('superadmin::lang.business_settings')}}
							</label>
						</div>
					</div>
					<div class="col-sm-3">
						<div class="checkbox">
							<label>
								{!! Form::checkbox('business_location', 1, $packages->business_location, ['class' =>
                                'input-icheck']); !!}
								{{__('superadmin::lang.business_location')}}
							</label>
						</div>
					</div>
					<div class="col-sm-3">
						<div class="checkbox">
							<label>
								{!! Form::checkbox('invoice_settings', 1, $packages->invoice_settings, ['class' =>
                                'input-icheck']); !!}
								{{__('superadmin::lang.invoice_settings')}}
							</label>
						</div>
					</div>
					<div class="col-sm-3">
						<div class="checkbox">
							<label>
								{!! Form::checkbox('tax_rates', 1, $packages->tax_rates, ['class' => 'input-icheck']); !!}
								{{__('superadmin::lang.tax_rates')}}
							</label>
						</div>
					</div>
					<div class="col-sm-3">
						<div class="checkbox">
							<label>
								{!! Form::checkbox('list_easy_payment', 1, $packages->list_easy_payment, ['class' =>
                                'input-icheck']); !!}
								{{__('superadmin::lang.list_easy_payment')}}
							</label>
						</div>
					</div>
					<div class="col-sm-3">
						<div class="checkbox">
							<label>
								{!! Form::checkbox('fleet_module', 1, $packages->fleet_module, ['class' => 'input-icheck',
                                'id' => 'fleet_module']);
                                !!}
								{{__('superadmin::lang.fleet_module')}}
							</label>
						</div>
					</div>
					<div class="col-sm-3">
						<div class="checkbox">
							<label>
								{!! Form::checkbox('ezyboat_module', 1, $packages->ezyboat_module, ['class' =>
                                'input-icheck', 'id' => 'ezyboat_module']);
                                !!}
								{{__('superadmin::lang.ezyboat_module')}}
							</label>
						</div>
					</div>
					<div class="col-sm-3">
						<div class="checkbox">
							<label>
								{!! Form::checkbox('customer_order_own_customer', 1, $packages->customer_order_own_customer,
                                ['class' => 'input-icheck']); !!}
								{{__('lang_v1.customer_order_own_customer')}}
							</label>
						</div>
					</div>
					<div class="col-sm-3">
						<div class="checkbox">
							<label>
								{!! Form::checkbox('customer_order_general_customer', 1,
                                $packages->customer_order_general_customer, ['class' => 'input-icheck']); !!}
								{{__('lang_v1.customer_order_general_customer')}}
							</label>
						</div>
					</div>
					<div class="col-sm-3">
						<div class="checkbox">
							<label>
								{!! Form::checkbox('is_private', 1, $packages->is_private, ['class' => 'input-icheck']); !!}
								{{__('superadmin::lang.private_superadmin_only')}}
							</label>
						</div>
					</div>
					<div class="col-sm-3">
						<div class="checkbox">
							<label>
								{!! Form::checkbox('is_one_time', 1, $packages->is_one_time, ['class' => 'input-icheck']);
                                !!}
								{{__('superadmin::lang.one_time_only_subscription')}}
							</label>
						</div>
					</div>
					<div class="col-sm-3">
						<div class="checkbox">
							<label>
								{!! Form::checkbox('enable_custom_link', 1, $packages->enable_custom_link, ['class' =>
                                'input-icheck', 'id' => 'enable_custom_link']); !!}
								{{__('superadmin::lang.enable_custom_subscription_link')}}
							</label>
						</div>
					</div>
					<div class="col-sm-3">
						<div class="checkbox">
							<label>
								{!! Form::checkbox('visitors_registration_module', 1,
                                $packages->visitors_registration_module, ['class' =>
                                'input-icheck', 'id' => 'visitors_registration_module']); !!}
								{{__('superadmin::lang.visitors_registration_module')}}
							</label>
						</div>
					</div>
                    {{--
                        @author Afes Oktavianus
                        @since 2021-08-23
                        @req 3413-Package Permission for Petro Module
                    --}}
                    <div class="clearfix"></div>
                    <div class="col-md-12">
                        <hr style="border-width:2px;color:gray;background-color:gray">
                        <div class="row">
                            <div class="col-sm-3">
                                <div class="checkbox">
                                    <label class="flex-label">
                                        {!! Form::checkbox('petro_module', 1, $packages->petro_module, ['class' => 'input-icheck', 'id'=>'petro_module']); !!}
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
                                        {!! Form::checkbox('petro_dashboard', 1, $packages->petro_dashboard, ['class' => 'input-icheck', 'id'=>'petro_dashboard']); !!}
                                        {{__('superadmin::lang.petro_dashboard')}}
                                    </label>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="checkbox">
                                    <label class="flex-label">
                                        {!! Form::checkbox('petro_task_management', 1, $packages->petro_task_management, ['class' => 'input-icheck', 'id'=>'petro_task_management']); !!}
                                        {{__('superadmin::lang.petro_task_management')}}
                                    </label>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="checkbox">
                                    <label class="flex-label">
                                        {!! Form::checkbox('pump_management', 1, $packages->pump_management, ['class' => 'input-icheck', 'id'=>'pump_management']); !!}
                                        {{__('superadmin::lang.pump_management_tab_page')}}
                                    </label>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="checkbox">
                                    <label class="flex-label">
                                        {!! Form::checkbox('pump_management_testing', 1, $packages->pump_management_testing, ['class' => 'input-icheck', 'id'=>'pump_management_testing']); !!}
                                        {{__('superadmin::lang.pump_management_testing_page')}}
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-3">
                                <div class="checkbox">
                                    <label class="flex-label">
                                        {!! Form::checkbox('meter_resetting', 1, $packages->meter_resetting, ['class' => 'input-icheck', 'id'=>'meter_resetting']); !!}
                                        {{__('superadmin::lang.meter_resetting_page')}}
                                    </label>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="checkbox">
                                    <label class="flex-label">
                                        {!! Form::checkbox('meter_reading', 1, $packages->meter_reading, ['class' => 'input-icheck','id'=>'meter_reading']); !!}
                                        {{__('superadmin::lang.meter_reading_page')}}
                                    </label>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="checkbox">
                                    <label class="flex-label">
                                        {!! Form::checkbox('pump_dashboard_opening', 1, $packages->pump_dashboard_opening, ['class' => 'input-icheck','id'=>'pump_dashboard_opening']); !!}
                                        {{__('superadmin::lang.pump_dashboard_opening_page')}}
                                    </label>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="checkbox">
                                    <label class="flex-label">
                                        {!! Form::checkbox('pumper_management', 1, $packages->pumper_management, ['class' => 'input-icheck', 'id'=>'pumper_management']); !!}
                                        {{__('superadmin::lang.pump_management_sub_menu_page')}}
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-3">
                                <div class="checkbox">
                                    <label class="flex-label">
                                        {!! Form::checkbox('daily_collection', 1, $packages->daily_collection, ['class' => 'input-icheck', 'id'=>'daily_collection']); !!}
                                        {{__('superadmin::lang.daily_collection_sub_menu')}}
                                    </label>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="checkbox">
                                    <label class="flex-label">
                                        {!! Form::checkbox('settlement', 1, $packages->settlement, ['class' => 'input-icheck','id'=>'settlement']); !!}
                                        {{__('superadmin::lang.settlement_sub_menu')}}
                                    </label>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="checkbox">
                                    <label class="flex-label">
                                        {!! Form::checkbox('list_settlement', 1, $packages->list_settlement, ['class' => 'input-icheck','id'=>'list_settlement']); !!}
                                        {{__('superadmin::lang.list_settlement_sub_menu')}}
                                    </label>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="checkbox">
                                    <label class="flex-label">
                                        {!! Form::checkbox('dip_management', 1, $packages->dip_management, ['class' => 'input-icheck','id'=>'dip_management']); !!}
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
                                        {!! Form::checkbox('report_module', 1, $packages->report_module, ['class' => 'input-icheck','id'=>'report_module']); !!}
                                        {{__('superadmin::lang.report_module')}}
                                    </label>
                                </div>
                            </div>
                            <div class="col-sm-6">
                            </div>
                            <div class="col-sm-3">
                                <div class="checkbox">
                                    <label class="flex-label">
                                        {!! Form::checkbox('all_report_module', 1, false, ['class' => 'input-icheck','id'=>'all_report_module']); !!}
                                        {{__('superadmin::lang.select_all_in_enable_report_module')}}
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-3">
                                <div class="checkbox">
                                    <label class="flex-label" class="flex-label">
                                        {!! Form::checkbox('product_report', 1, $packages->product_report, ['class' => 'input-icheck','id'=>'product_report']); !!}
                                        {{__('superadmin::lang.product_report_sub_menu')}}
                                    </label>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="checkbox">
                                    <label class="flex-label">
                                        {!! Form::checkbox('payment_status_report', 1, $packages->payment_status_report, ['class' => 'input-icheck','id'=>'payment_status_report']); !!}
                                        {{__('superadmin::lang.payment_status_report_sub_menu')}}
                                    </label>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="checkbox">
                                    <label class="flex-label">
                                        {!! Form::checkbox('report_daily', 1, $packages->report_daily, ['class' => 'input-icheck','id'=>'report_daily']); !!}
                                        {{__('superadmin::lang.report_daily_tab_page')}}
                                    </label>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="checkbox">
                                    <label class="flex-label">
                                        {!! Form::checkbox('report_daily_summary', 1, $packages->report_daily_summary, ['class' => 'input-icheck','id'=>'report_daily_summary']); !!}
                                        {{__('superadmin::lang.report_daily_summary_tab_page')}}
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-3">
                                <div class="checkbox">
                                    <label class="flex-label">
                                        {!! Form::checkbox('report_register', 1, $packages->report_register, ['class' => 'input-icheck','id'=>'report_register']); !!}
                                        {{__('superadmin::lang.report_register_tab_page')}}
                                    </label>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="checkbox">
                                    <label class="flex-label">
                                        {!! Form::checkbox('report_profit_loss', 1, $packages->report_profit_loss, ['class' => 'input-icheck','id'=>'report_profit_loss']); !!}
                                        {{__('superadmin::lang.report_profit_loss_tab_page')}}
                                    </label>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="checkbox">
                                    <label class="flex-label">
                                        {!! Form::checkbox('report_credit_status', 1, $packages->report_credit_status, ['class' => 'input-icheck','id'=>'report_credit_status']); !!}
                                        {{__('superadmin::lang.report_credit_status_tab_page')}}
                                    </label>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="checkbox">
                                    <label class="flex-label">
                                        {!! Form::checkbox('activity_report', 1, $packages->activity_report, ['class' => 'input-icheck','id'=>'activity_report']); !!}
                                        {{__('superadmin::lang.activity_report_sub_menu')}}
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-3">
                                <div class="checkbox">
                                    <label class="flex-label">
                                        {!! Form::checkbox('contact_report', 1, $packages->contact_report, ['class' => 'input-icheck','id'=>'contact_report']); !!}
                                        {{__('superadmin::lang.contact_report_sub_menu')}}
                                    </label>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="checkbox">
                                    <label class="flex-label">
                                        {!! Form::checkbox('trending_product', 1, $packages->trending_product, ['class' => 'input-icheck','id'=>'trending_product']); !!}
                                        {{__('superadmin::lang.trending_product_sub_menu')}}
                                    </label>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="checkbox">
                                    <label class="flex-label">
                                        {!! Form::checkbox('user_activity', 1, $packages->user_activity, ['class' => 'input-icheck','id'=>'user_activity']); !!}
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
                                        {!! Form::checkbox('contact_module', 1, $packages->contact_module, ['class' => 'input-icheck', 'id'=>'contact_module']); !!}
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
                                        {!! Form::checkbox('contact_supplier', 1, $packages->contact_supplier, ['class' => 'input-icheck', 'id'=>'contact_supplier']); !!}
                                        {{__('superadmin::lang.supplier_tab_page')}}
                                    </label>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="checkbox">
                                    <label class="flex-label">
                                        {!! Form::checkbox('contact_customer', 1, $packages->contact_customer, ['class' => 'input-icheck', 'id'=>'contact_customer']); !!}
                                        {{__('superadmin::lang.customer_tab_page')}}
                                    </label>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="checkbox">
                                    <label class="flex-label">
                                        {!! Form::checkbox('contact_group_customer', 1, $packages->contact_group_customer, ['class' => 'input-icheck', 'id'=>'contact_group_customer']); !!}
                                        {{__('superadmin::lang.contact_group_customer_tab_page')}}
                                    </label>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="checkbox">
                                    <label class="flex-label">
                                        {!! Form::checkbox('contact_group_supplier', 1, $packages->contact_group_supplier, ['class' => 'input-icheck', 'id'=>'contact_group_supplier']); !!}
                                        {{__('superadmin::lang.contact_group_supplier_tab_page')}}
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-3">
                                <div class="checkbox">
                                    <label class="flex-label">
                                        {!! Form::checkbox('import_contact', 1, $packages->import_contact, ['class' => 'input-icheck','id'=>'import_contact']); !!}
                                        {{__('superadmin::lang.import_contact_tab_page')}}
                                    </label>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="checkbox">
                                    <label class="flex-label">
                                        {!! Form::checkbox('customer_reference', 1, $packages->customer_reference, ['class' => 'input-icheck', 'id'=>'customer_reference']); !!}
                                        {{__('superadmin::lang.customer_reference_tab_page')}}
                                    </label>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="checkbox">
                                    <label class="flex-label">
                                        {!! Form::checkbox('customer_statement', 1, $packages->customer_statement, ['class' => 'input-icheck', 'id'=>'customer_statement']); !!}
                                        {{__('superadmin::lang.customer_statement_tab_page')}}
                                    </label>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="checkbox">
                                    <label class="flex-label">
                                        {!! Form::checkbox('customer_payment', 1, $packages->customer_payment, ['class' => 'input-icheck', 'id'=> 'customer_payment']); !!}
                                        {{__('superadmin::lang.customer_payment_tab_page')}}
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-3">
                                <div class="checkbox">
                                    <label class="flex-label">
                                        {!! Form::checkbox('outstanding_received', 1, $packages->outstanding_received, ['class' => 'input-icheck', 'id'=>'outstanding_received']); !!}
                                        {{__('superadmin::lang.outstanding_received_tab_page')}}
                                    </label>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="checkbox">
                                    <label class="flex-label">
                                        {!! Form::checkbox('issue_payment_detail', 1, $packages->issue_payment_detail, ['class' => 'input-icheck', 'id'=>'issue_payment_detail']); !!}
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
                                        {!! Form::checkbox('sale_module', 1, $packages->sale_module, ['class' => 'input-icheck','id'=>'sale_module']); !!}
                                        {{__('superadmin::lang.sale_module')}}
                                    </label>
                                </div>
                            </div>
                            <div class="col-sm-6">
                            </div>
                            <div class="col-sm-3">
                                <div class="checkbox">
                                    <label class="flex-label">
                                        {!! Form::checkbox('all_sale_module', 1, false, ['class' => 'input-icheck','id'=>'all_sale_module']); !!}
                                        {{__('superadmin::lang.select_all_in_enable_sale_module')}}
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-3">
                                <div class="checkbox">
                                    <label class="flex-label" class="flex-label">
                                        {!! Form::checkbox('all_sales', 1, $packages->all_sales, ['class' => 'input-icheck','id'=>'all_sales']); !!}
                                        {{__('superadmin::lang.select_all_sale_sub_menu')}}
                                    </label>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="checkbox">
                                    <label class="flex-label">
                                        {!! Form::checkbox('add_sale', 1, $packages->add_sale, ['class' => 'input-icheck',"id"=>'add_sale']); !!}
                                        {{__('superadmin::lang.add_sale_sub_menu')}}
                                    </label>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="checkbox">
                                    <label class="flex-label">
                                        {!! Form::checkbox('list_pos', 1, $packages->list_pos, ['class' => 'input-icheck','id'=>'list_pos']); !!}
                                        {{__('superadmin::lang.list_pos_sub_menu')}}
                                    </label>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="checkbox">
                                    <label class="flex-label">
                                        {!! Form::checkbox('pos_', 1, $packages->pos_sale, ['class' => 'input-icheck','id'=>'pos_']); !!}
                                        {{__('superadmin::lang.pos_sub_menu')}}
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-3">
                                <div class="checkbox">
                                    <label class="flex-label">
                                        {!! Form::checkbox('pos_button_on_top_belt', 1, $packages->pos_button_on_top_belt, ['class' => 'input-icheck', 'id'=>'pos_button_on_top_belt']); !!}
                                        {{__('superadmin::lang.pos_button_on_top_belt')}}
                                    </label>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="checkbox">
                                    <label class="flex-label">
                                        {!! Form::checkbox('list_order', 1, $packages->list_orders, ['class' => 'input-icheck', 'id'=>'list_order']); !!}
                                        {{__('superadmin::lang.list_order_sub_menu')}}
                                    </label>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="checkbox">
                                    <label class="flex-label">
                                        {!! Form::checkbox('upload_order', 1, $packages->upload_orders, ['class' => 'input-icheck', 'id'=>'upload_order']); !!}
                                        {{__('superadmin::lang.upload_order_sub_menu')}}
                                    </label>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="checkbox">
                                    <label class="flex-label">
                                        {!! Form::checkbox('list_sell_return', 1, $packages->list_sell_return, ['class' => 'input-icheck','id'=>'list_sell_return']); !!}
                                        {{__('superadmin::lang.list_sell_return_sub_menu')}}
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-3">
                                <div class="checkbox">
                                    <label class="flex-label">
                                        {!! Form::checkbox('shipment', 1, $packages->shipment, ['class' => 'input-icheck','id'=>'shipment']); !!}
                                        {{__('superadmin::lang.shipment_sub_menu')}}
                                    </label>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="checkbox">
                                    <label class="flex-label">
                                        {!! Form::checkbox('discount', 1, $packages->discount, ['class' => 'input-icheck','id'=>'discount']); !!}
                                        {{__('superadmin::lang.discount_sub_menu')}}
                                    </label>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="checkbox">
                                    <label class="flex-label">
                                        {!! Form::checkbox('import_sale', 1, $packages->import_sale, ['class' => 'input-icheck','id'=>'import_sale']); !!}
                                        {{__('superadmin::lang.import_sales_sub_menu')}}
                                    </label>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="checkbox">
                                    <label class="flex-label">
                                        {!! Form::checkbox('reserved_stock', 1, $packages->reserved_stock, ['class' => 'input-icheck','id'=>'reserved_stock']); !!}
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
                                        {!! Form::checkbox('purchase_module', 1, $packages->purchase_module, ['class' => 'input-icheck','id'=>'purchase_module']); !!}
                                        {{__('Purchase Module')}}
                                    </label>
                                </div>
                            </div>
                            <div class="col-sm-6">
                            </div>
                            <div class="col-sm-3">
                                <div class="checkbox">
                                    <label class="flex-label">
                                        {!! Form::checkbox('all_purchase_module', 1, false, ['class' => 'input-icheck','id'=>'all_purchase_module']); !!}
                                        {{__('Select All In Purchase Module')}}
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-3">
                                <div class="checkbox">
                                    <label class="flex-label">
                                        {!! Form::checkbox('all_purchase', 1, $packages->all_purchase, ['class' => 'input-icheck', 'id'=>'all_purchase']); !!}
                                        {{__('All Purchase')}}
                                    </label>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="checkbox">
                                    <label class="flex-label">
                                        {!! Form::checkbox('add_purchase', 1, $packages->add_purchase, ['class' => 'input-icheck','id'=>'add_purchase']); !!}
                                        {{__('Add Purchase')}}
                                    </label>
                                </div>
                            </div>
							<div class="col-sm-3">
                                <div class="checkbox">
                                    <label class="flex-label">
                                        {!! Form::checkbox('add_bulk_purchase', 1, $packages->add_bulk_purchase, ['class' => 'input-icheck','id'=>'add_bulk_purchase']); !!}
                                        {{__('Add Bulk Purchase')}}
                                    </label>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="checkbox">
                                    <label class="flex-label">
                                        {!! Form::checkbox('import_purchase', 1, $packages->import_purchase, ['class' => 'input-icheck','id'=>'import_purchase']); !!}
                                        {{__('Import Purchase')}}
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-3">
                                <div class="checkbox">
                                    <label class="flex-label">
                                        {!! Form::checkbox('pop_button_on_top_belt', 1, $packages->pop_button_on_top_belt, ['class' => 'input-icheck','id'=>'pop_button_on_top_belt']); !!}
                                        {{__('Pop Button On The Belt')}}
                                    </label>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="checkbox">
                                    <label class="flex-label">
                                        {!! Form::checkbox('purchase_return', 1, $packages->purchase_return, ['class' => 'input-icheck','id'=>'purchase_return']); !!}
                                        {{__('Purchase Return')}}
                                    </label>
                                </div>
                            </div>
                        </div>
                        <hr style="border-width:2px;color:gray;background-color:gray">
                    </div>
                    <div class="clearfix"></div>
					<div class="col-sm-3 ">
						<div class="checkbox">
							<label>
								{!! Form::checkbox('is_active', 1, $packages->is_active, ['class' => 'input-icheck']); !!}
								{{__('superadmin::lang.is_active')}}
							</label>
						</div>
					</div>
					<div class="col-sm-3">
						<div class="checkbox">
							<label>
								{!! Form::checkbox('update_subscriptions', 1, false, ['class' => 'input-icheck']); !!}
								{{__('superadmin::lang.update_existing_subscriptions')}}
							</label>
							@show_tooltip(__('superadmin::lang.update_existing_subscriptions_tooltip'))
						</div>
					</div>
					<div class="col-sm-3">
						<div class="checkbox">
							<label>
								{!! Form::checkbox('day_end_enable', 1, $packages->day_end_enable , [ 'class' => 'input-icheck','id' =>	'day_end_enable']); !!} {{ __( 'superadmin::lang.day_end' ) }} </label>
							</label>
						</div>
					</div>
					<div class="clearfix"></div>
					<div id="custom_link_div" @if(empty($packages->enable_custom_link)) class="hide" @endif>
						<div class="col-sm-3">
							<div class="form-group">
								{!! Form::label('custom_link', __('superadmin::lang.custom_link').':') !!}
								{!! Form::text('custom_link', $packages->custom_link, ['class' => 'form-control']); !!}
							</div>
						</div>
						<div class="col-sm-3">
							<div class="form-group">
								{!! Form::label('custom_link_text', __('superadmin::lang.custom_link_text').':') !!}
								{!! Form::text('custom_link_text', $packages->custom_link_text, ['class' =>
                                'form-control']); !!}
							</div>
						</div>
					</div>
					@php
						$hbt = json_decode($packages->hospital_business_type);
                        if(empty($hbt)){
                        $hbt = [];
                        }
					@endphp
					<div class="col-sm-3 @if(!$packages->hospital_system) hide @endif hospital_business_type">
						<div class="form-group">
							<label>
								{!! Form::label('name', __('superadmin::lang.hospital_business_type').':') !!}
							</label>
							<select name="hospital_business_type[]" id="hospital_business_type" class="form-control"
									multiple>
								<option @if(in_array('patient', $hbt)) selected @endif value="patient">Patient</option>
								<option @if(in_array( 'hosp_and_dis' , $hbt)) selected @endif value="hosp_and_dis">Hospitals
									&
									Dispensaries</option>
								<option @if(in_array( 'pharmacies' , $hbt)) selected @endif value="pharmacies">Pharmacies
								</option>
								<option @if(in_array( 'laboratories' , $hbt)) selected @endif value="laboratories">
									Laboratories
								</option>
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
										{!! Form::checkbox('number_of_branches', 1, $packages->number_of_branches, ['class' =>
                                        'input-icheck', 'id' => 'number_of_branches']); !!}
										{{__('superadmin::lang.number_of_branches')}}
									</label>
								</div>
								<div class="checkbox">
									<label>
										{!! Form::checkbox('number_of_users', 1, $packages->number_of_users, ['class' =>
                                        'input-icheck', 'id' => 'number_of_users']); !!}
										{{__('superadmin::lang.number_of_users')}}
									</label>
								</div>
								<div class="checkbox">
									<label>
										{!! Form::checkbox('number_of_products', 1, $packages->number_of_products, ['class' =>
                                        'input-icheck', 'id' => 'number_of_products']); !!}
										{{__('superadmin::lang.number_of_products')}}
									</label>
								</div>
								<div class="checkbox">
									<label>
										{!! Form::checkbox('number_of_customers', 1, $packages->number_of_customers, ['class' =>
                                        'input-icheck', 'id' => 'number_of_customers']); !!}
										{{__('superadmin::lang.number_of_customers')}}
									</label>
								</div>
								<div class="checkbox">
									<label>
										{!! Form::checkbox('number_of_periods', 1, $packages->number_of_periods, ['class' =>
                                        'input-icheck', 'id' => 'number_of_periods']); !!}
										{{__('superadmin::lang.number_of_periods')}}
									</label>
								</div>
								<div class="checkbox">
									<label>
										{!! Form::checkbox('monthly_total_sales', 1, $packages->monthly_total_sales, ['class' =>
                                        'input-icheck', 'id' => 'monthly_total_sales']); !!}
										{{__('superadmin::lang.monthly_total_sales')}}
									</label>
								</div>
								<div class="checkbox">
									<label>
										{!! Form::checkbox('no_of_family_members', 1, $packages->no_of_family_members, ['class' =>
                                        'input-icheck', 'id' => 'no_of_family_members']); !!}
										{{__('superadmin::lang.no_of_family_members')}}
									</label>
								</div>
								<div class="checkbox">
									<label>
										{!! Form::checkbox('no_of_vehicles', 1, $packages->no_of_vehicles, ['class' =>
                                        'input-icheck', 'id' => 'no_of_vehicles']); !!}
										{{__('superadmin::lang.no_of_vehicles')}}
									</label>
								</div>
							</div>
							<div class="col-md-3">
								<div class="checkbox">
									<label>
										{!! Form::checkbox('customer_interest_deduct_option', 1, $packages->customer_interest_deduct_option, ['class' => 'input-icheck', 'id' =>
                                        'customer_interest_deduct_option']); !!}
										{{ __('superadmin::lang.customer_interest_deduct_option')}}
									</label>
								</div>
							</div>
							<div class="col-sm-3 ">
                                <div class="checkbox">
                                    <label>
                                        {!! Form::checkbox('manage_stock_enable', 1, $is_manage_stock_enable, ['class' => 'input-icheck', 'id' =>
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
										{!! Form::checkbox('payday', 1, $packages->payday, ['class' =>
                                        'input-icheck', 'id' => 'payday']); !!}
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
							{!! Form::text('name',$packages->name, ['class' => 'form-control', 'required']); !!}
						</div>
					</div>
					<div class="col-sm-6">
						<div class="form-group">
							{!! Form::label('description', __('superadmin::lang.description').':') !!}
							{!! Form::text('description', $packages->description, ['class' => 'form-control', 'required']);
                            !!}
						</div>
					</div>
					<div class="col-sm-6  @if(in_array('patient', $hbt)) hide @endif location_count">
						<div class="form-group">
							{!! Form::label('location_count', __('superadmin::lang.location_count').':') !!}
							{!! Form::number('location_count', $packages->location_count, ['class' => 'form-control',
                            'required',
                            'min' => 0]); !!}
							<span class="help-block">
							@lang('superadmin::lang.infinite_help')
						</span>
						</div>
					</div>
					<div class="col-sm-6 user_count">
						<div class="form-group">
							@if(in_array('patient', $hbt))
								{!! Form::label('user_count', __('superadmin::lang.family_members_count').':') !!}
							@else
								{!! Form::label('user_count', __('superadmin::lang.user_count').':') !!}
							@endif
							{!! Form::number('user_count', $packages->user_count, ['class' => 'form-control', 'required',
                            'min' =>
                            0]); !!}
							<span class="help-block">
							@lang('superadmin::lang.infinite_help')
						</span>
						</div>
					</div>
					<div class="col-sm-6 @if(in_array('patient', $hbt)) hide @endif product_count">
						<div class="form-group">
							{!! Form::label('customer_count', __('superadmin::lang.customer_count').':') !!}
							{!! Form::number('customer_count', $packages->customer_count, ['class' => 'form-control',
                            'required',
                            'min' => 0]); !!}
							<span class="help-block">
							@lang('superadmin::lang.infinite_help')
						</span>
						</div>
					</div>
					<div class="col-sm-6 @if(!$packages->hr_module) hide @endif employee_count">
						<div class="form-group">
							{!! Form::label('employee_count', __('superadmin::lang.employee_count').':') !!}
							{!! Form::number('employee_count', $packages->employee_count, ['class' => 'form-control',
                            'required',
                            'min' => 0]); !!}
							<span class="help-block">
							@lang('superadmin::lang.infinite_help')
						</span>
						</div>
					</div>
					<div
							class="col-sm-6 @if(in_array('patient', $hbt)) hide @endif @if($packages->fleet_module) hide @endif product_count">
						<div class="form-group">
							{!! Form::label('product_count', __('superadmin::lang.product_count').':') !!}
							{!! Form::number('product_count', $packages->product_count, ['class' => 'form-control',
                            'required',
                            'min' => 0]); !!}
							<span class="help-block">
							@lang('superadmin::lang.infinite_help')
						</span>
						</div>
					</div>
					<div class="col-sm-6 @if(in_array('patient', $hbt)) hide @endif invoice_count">
						<div class="form-group">
							{!! Form::label('invoice_count', __('superadmin::lang.invoice_count').':') !!}
							{!! Form::number('invoice_count', $packages->invoice_count, ['class' => 'form-control',
                            'required',
                            'min' => 0]); !!}
							<span class="help-block">
							@lang('superadmin::lang.infinite_help')
						</span>
						</div>
					</div>
					<div
							class="col-sm-6 @if(in_array('patient', $hbt)) hide @endif @if($packages->fleet_module) hide @endif store_count">
						<div class="form-group">
							{!! Form::label('store_count', __('superadmin::lang.store_count').':') !!}
							{!! Form::number('store_count', $packages->store_count, ['class' => 'form-control', 'required',
                            'min' =>
                            0]); !!}
							<span class="help-block">
							@lang('superadmin::lang.infinite_help')
						</span>
						</div>
					</div>
					<div class="col-sm-6">
						<div class="form-group">
							{!! Form::label('interval', __('superadmin::lang.interval').':') !!}
							{!! Form::select('interval', $intervals, $packages->interval, ['class' => 'form-control
                            select2',
                            'placeholder' => __('messages.please_select'), 'required']); !!}
							<span class="help-block">
							&nbsp;
						</span>
						</div>
					</div>
					<div class="col-sm-6">
						<div class="form-group">
							{!! Form::label('interval_count ', __('superadmin::lang.interval_count').':') !!}
							{!! Form::number('interval_count', $packages->interval_count, ['class' => 'form-control',
                            'required']);
                            !!}
						</div>
					</div>
					<div class="col-sm-6">
						<div class="form-group">
							{!! Form::label('trial_days ', __('superadmin::lang.trial_days').':') !!}
							{!! Form::number('trial_days', $packages->trial_days, ['class' => 'form-control', 'required',
                            'min' =>
                            0]); !!}
						</div>
					</div>
					<div class="col-sm-6">
						<div class="form-group">
							{!! Form::label('price', __('superadmin::lang.price').':') !!}
							{!! Form::text('price', $packages->price, ['class' => 'form-control input_number', 'required']);
                            !!}
						</div>
					</div>
					@php
						$selected_currency = App\Currency::select('symbol')->where('id', $packages->currency_id)->first();
                        if(empty($selected_currency)){
                        $selected_currency = '$';
                        }else{
                        $selected_currency = $selected_currency ->symbol;
                        }
					@endphp
					<div class="col-sm-6">
						<div class="form-group">
							<label for="currency_id">Currency:</label>
							<div class="input-group">
							<span class="input-group-addon">
								<i class="fa fa-money"></i>
							</span>
								{{-- <select class="form-control select2" required id="currency_id" name="currency_id"><option value="">Currency</option><option value="3">Afghanistan - Afghanis(AF) </option><option value="1">Albania - Leke(ALL) </option><option value="135">Algerie - Algerian dinar(DZD) </option><option value="2">America - Dollars(USD) </option><option value="139">Angola - Kwanza(AOA) </option><option value="4">Argentina - Pesos(ARS) </option><option value="5">Aruba - Guilders(AWG) </option><option value="6">Australia - Dollars(AUD) </option><option value="7">Azerbaijan - New Manats(AZ) </option><option value="8">Bahamas - Dollars(BSD) </option><option value="134">Bangladesh - Taka(BDT) </option><option value="9">Barbados - Dollars(BBD) </option><option value="10">Belarus - Rubles(BYR) </option><option value="11">Belgium - Euro(EUR) </option><option value="12">Beliz - Dollars(BZD) </option><option value="13">Bermuda - Dollars(BMD) </option><option value="14">Bolivia - Bolivianos(BOB) </option><option value="15">Bosnia and Herzegovina - Convertible Marka(BAM) </option><option value="16">Botswana - Pula&#039;s(BWP) </option><option value="18">Brazil - Reais(BRL) </option><option value="19">Britain [United Kingdom] - Pounds(GBP) </option><option value="20">Brunei Darussalam - Dollars(BND) </option><option value="17">Bulgaria - Leva(BG) </option><option value="21">Cambodia - Riels(KHR) </option><option value="22">Canada - Dollars(CAD) </option><option value="23">Cayman Islands - Dollars(KYD) </option><option value="24">Chile - Pesos(CLP) </option><option value="25">China - Yuan Renminbi(CNY) </option><option value="26">Colombia - Pesos(COP) </option><option value="27">Costa Rica - Col��n(CRC) </option><option value="28">Croatia - Kuna(HRK) </option><option value="29">Cuba - Pesos(CUP) </option><option value="30">Cyprus - Euro(EUR) </option><option value="31">Czech Republic - Koruny(CZK) </option><option value="32">Denmark - Kroner(DKK) </option><option value="33">Dominican Republic - Pesos(DOP ) </option><option value="34">East Caribbean - Dollars(XCD) </option><option value="35">Egypt - Pounds(EGP) </option><option value="36">El Salvador - Colones(SVC) </option><option value="37">England [United Kingdom] - Pounds(GBP) </option><option value="38">Euro - Euro(EUR) </option><option value="39">Falkland Islands - Pounds(FKP) </option><option value="40">Fiji - Dollars(FJD) </option><option value="41">France - Euro(EUR) </option><option value="42">Ghana - Cedis(GHC) </option><option value="43">Gibraltar - Pounds(GIP) </option><option value="44">Greece - Euro(EUR) </option><option value="45">Guatemala - Quetzales(GTQ) </option><option value="46">Guernsey - Pounds(GGP) </option><option value="47">Guyana - Dollars(GYD) </option><option value="48">Holland [Netherlands] - Euro(EUR) </option><option value="49">Honduras - Lempiras(HNL) </option><option value="50">Hong Kong - Dollars(HKD) </option><option value="51">Hungary - Forint(HUF) </option><option value="52">Iceland - Kronur(ISK) </option><option value="53">India - Rupees(INR) </option><option value="54">Indonesia - Rupiahs(IDR) </option><option value="55">Iran - Rials(IRR) </option><option value="132">Iraq - Iraqi dinar(IQD) </option><option value="56">Ireland - Euro(EUR) </option><option value="57">Isle of Man - Pounds(IMP) </option><option value="58">Israel - New Shekels(ILS) </option><option value="59">Italy - Euro(EUR) </option><option value="60">Jamaica - Dollars(JMD) </option><option value="61">Japan - Yen(JPY) </option><option value="62">Jersey - Pounds(JEP) </option><option value="63">Kazakhstan - Tenge(KZT) </option><option value="133">Kenya - Kenyan shilling(KES) </option><option value="64">Korea [North] - Won(KPW) </option><option value="65">Korea [South] - Won(KRW) </option><option value="66">Kyrgyzstan - Soms(KGS) </option><option value="67">Laos - Kips(LAK) </option><option value="68">Latvia - Lati(LVL) </option><option value="69">Lebanon - Pounds(LBP) </option><option value="70">Liberia - Dollars(LRD) </option><option value="71">Liechtenstein - Switzerland Francs(CHF) </option><option value="72">Lithuania - Litai(LTL) </option><option value="73">Luxembourg - Euro(EUR) </option><option value="74">Macedonia - Denars(MKD) </option><option value="75">Malaysia - Ringgits(MYR) </option><option value="76">Malta - Euro(EUR) </option><option value="77">Mauritius - Rupees(MUR) </option><option value="78">Mexico - Pesos(MXN) </option><option value="79">Mongolia - Tugriks(MNT) </option><option value="80">Mozambique - Meticais(MZ) </option><option value="81">Namibia - Dollars(NAD) </option><option value="82">Nepal - Rupees(NPR) </option><option value="84">Netherlands - Euro(EUR) </option><option value="83">Netherlands Antilles - Guilders(ANG) </option><option value="85">New Zealand - Dollars(NZD) </option><option value="86">Nicaragua - Cordobas(NIO) </option><option value="87">Nigeria - Nairas(NG) </option><option value="88">North Korea - Won(KPW) </option><option value="89">Norway - Krone(NOK) </option><option value="90">Oman - Rials(OMR) </option><option value="91">Pakistan - Rupees(PKR) </option><option value="92">Panama - Balboa(PAB) </option><option value="93">Paraguay - Guarani(PYG) </option><option value="94">Peru - Nuevos Soles(PE) </option><option value="95">Philippines - Pesos(PHP) </option><option value="96">Poland - Zlotych(PL) </option><option value="97">Qatar - Rials(QAR) </option><option value="98">Romania - New Lei(RO) </option><option value="99">Russia - Rubles(RUB) </option><option value="100">Saint Helena - Pounds(SHP) </option><option value="101">Saudi Arabia - Riyals(SAR) </option><option value="102">Serbia - Dinars(RSD) </option><option value="103">Seychelles - Rupees(SCR) </option><option value="104">Singapore - Dollars(SGD) </option><option value="105">Slovenia - Euro(EUR) </option><option value="106">Solomon Islands - Dollars(SBD) </option><option value="107">Somalia - Shillings(SOS) </option><option value="108">South Africa - Rand(ZAR) </option><option value="109">South Korea - Won(KRW) </option><option value="110">Spain - Euro(EUR) </option><option value="111" selected="selected">Sri Lanka - Rupees(LKR) </option><option value="114">Suriname - Dollars(SRD) </option><option value="112">Sweden - Kronor(SEK) </option><option value="113">Switzerland - Francs(CHF) </option><option value="115">Syria - Pounds(SYP) </option><option value="116">Taiwan - New Dollars(TWD) </option><option value="138">Tanzania - Tanzanian shilling(TZS) </option><option value="117">Thailand - Baht(THB) </option><option value="118">Trinidad and Tobago - Dollars(TTD) </option><option value="119">Turkey - Lira(TRY) </option><option value="120">Turkey - Liras(TRL) </option><option value="121">Tuvalu - Dollars(TVD) </option><option value="137">Uganda - Uganda shillings(UGX) </option><option value="122">Ukraine - Hryvnia(UAH) </option><option value="136">United Arab Emirates - United Arab Emirates dirham(AED) </option><option value="123">United Kingdom - Pounds(GBP) </option><option value="124">United States of America - Dollars(USD) </option><option value="125">Uruguay - Pesos(UYU) </option><option value="126">Uzbekistan - Sums(UZS) </option><option value="127">Vatican City - Euro(EUR) </option><option value="128">Venezuela - Bolivares Fuertes(VEF) </option><option value="129">Vietnam - Dong(VND) </option><option value="130">Yemen - Rials(YER) </option><option value="131">Zimbabwe - Zimbabwe Dollars(ZWD) </option></select> --}}
								{!! Form::select('currency_id', $currencies, $packages->currency_id, ['class' =>
                                'form-control
                                select2_register','placeholder' => __('business.currency_placeholder'), 'required', 'id' =>
                                'currency_id']); !!}
							</div>
						</div>
					</div>
					<div class="clearfix"></div>
					<div class="col-sm-6">
						<div class="form-group">
							{!! Form::label('sort_order ', __('superadmin::lang.sort_order').':') !!}
							{!! Form::number('sort_order', $packages->sort_order, ['class' => 'form-control', 'required']);
                            !!}
						</div>
					</div>
					<div class="col-sm-6">
						<div class="form-group">
							{!! Form::label('monthly_max_sale_limit ', __('superadmin::lang.monthly_max_sale_limit').':') !!}
							{!! Form::number('monthly_max_sale_limit', $packages->monthly_max_sale_limit, ['class' => 'form-control', 'required']) !!}
						</div>
					</div>
					<div class="col-sm-6 vehicle_count @if(!$packages->fleet_module) hide @endif">
						<div class="form-group">
							{!! Form::label('vehicle_count', __('superadmin::lang.no_of_vehicle').':') !!}
							{!! Form::number('vehicle_count', $packages->vehicle_count, ['class' => 'form-control',
                            'required', 'min' => 0]);
                            !!}
							<span class="help-block">
							@lang('superadmin::lang.infinite_help')
						</span>
						</div>
					</div>
					<div class="col-sm-6 @if(!in_array('patient', $hbt)) hide @endif visit_count">
						<div class="form-group">
							{!! Form::label('visit_count ', __('superadmin::lang.visit_count').':') !!}
							{!! Form::number('visit_count', $packages->visit_count, ['class' => 'form-control',
                            'required']);
                            !!}
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-sm-12">
						<button type="submit" class="btn btn-primary pull-right btn-flat">@lang('messages.save')</button>
					</div>
				</div>
			</div>
		</div>
		<div class="modal fade branch_veriables_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
		</div>
		<div class="modal fade user_veriables_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
		</div>
		<div class="modal fade product_veriables_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
		</div>
		<div class="modal fade period_veriables_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
		</div>
		<div class="modal fade number_of_customers" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
		</div>
		<div class="modal fade monthly_total_sales" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
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
		$(document).ready(function(){
			$('form#edit_package_form').validate();
		});
		$('#enable_custom_link').on('ifChecked', function(event){
			$("div#custom_link_div").removeClass('hide');
		});
		$('#enable_custom_link').on('ifUnchecked', function(event){
			$("div#custom_link_div").addClass('hide');
		});
		$('#hr_module').on('ifChecked', function(event){
			$("div.employee_count").removeClass('hide');
		});
		$('#hr_module').on('ifUnchecked', function(event){
			$("div.employee_count").addClass('hide');
		});
		$('#fleet_module').on('ifChecked', function(event){
			$("div.vehicle_count").removeClass('hide');
			$("div.product_count").addClass('hide');
			$("div.store_count").addClass('hide');
		});
		$('#fleet_module').on('ifUnchecked', function(event){
			$("div.vehicle_count").addClass('hide');
			$("div.product_count").removeClass('hide');
			$("div.store_count").removeClass('hide');
		});
		$('#hospital_system').on('ifChecked', function(event){
			$(".hospital_business_type").removeClass('hide');
			$('.location_count, .user_count, .product_count, .invoice_count, .store_count').addClass('hide');
			if($('#hospital_business_type').val() !== null){
				if($('#hospital_business_type').val().includes('patient')){
					$('.user_count').children().find('label').text('Number of Family Members:');
					$('.user_count').removeClass('hide');
				}
			}
		});
		$('#hospital_system').on('ifUnchecked', function(event){
			$(".hospital_business_type").addClass('hide');
			$('.visit_count').addClass('hide');
			$('.location_count, .user_count, .product_count, .invoice_count, .store_count').removeClass('hide');
			$('.user_count').children().find('label').text('Number of active users:');
		});
		$('#hospital_business_type').change(function(){
			if($(this).val().includes('patient')){
				$('.location_count, .product_count, .invoice_count, .store_count').addClass('hide');
				$('.visit_count').removeClass('hide');
				$('.fazool_clearfix').removeClass('clearfix');
				$('.user_count').children().find('label').text('Number of Family Members:');
			}else{
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
		$('#number_of_branches').on('ifChecked', function(event){
			$.ajax({
				method: 'get',
				url: "{{action('\Modules\Superadmin\Http\Controllers\PackagesController@getOptionVariables')}}",
				data: { option_id: 0, package_id: {{$packages->id}} },
				success: function(result) {
					$("div.branch_veriables_modal").html(result);
				},
			});
			$("div.branch_veriables_modal").modal('show');
		});
		$('#number_of_users').on('ifChecked', function(event){
			$.ajax({
				method: 'get',
				url: "{{action('\Modules\Superadmin\Http\Controllers\PackagesController@getOptionVariables')}}",
				data: { option_id: 1, package_id: {{$packages->id}} },
				success: function(result) {
					$("div.user_veriables_modal").html(result);
				},
			});
			$("div.user_veriables_modal").modal('show');
		});
		$('#number_of_products').on('ifChecked', function(event){
			$.ajax({
				method: 'get',
				url: "{{action('\Modules\Superadmin\Http\Controllers\PackagesController@getOptionVariables')}}",
				data: { option_id: 2, package_id: {{$packages->id}} },
				success: function(result) {
					$("div.product_veriables_modal").html(result);
				},
			});
			$("div.product_veriables_modal").modal('show');
		});
		$('#number_of_periods').on('ifChecked', function(event){
			$.ajax({
				method: 'get',
				url: "{{action('\Modules\Superadmin\Http\Controllers\PackagesController@getOptionVariables')}}",
				data: { option_id: 3, package_id: {{$packages->id}} },
				success: function(result) {
					$("div.period_veriables_modal").html(result);
				},
			});
			$("div.period_veriables_modal").modal('show');
		});
		$('#number_of_customers').on('ifChecked', function(event){
			$.ajax({
				method: 'get',
				url: "{{action('\Modules\Superadmin\Http\Controllers\PackagesController@getOptionVariables')}}",
				data: { option_id: 4, package_id: {{$packages->id}} },
				success: function(result) {
					$("div.number_of_customers").html(result);
				},
			});
			$("div.number_of_customers").modal('show');
		});
		$('#monthly_total_sales').on('ifChecked', function(event){
			$.ajax({
				method: 'get',
				url: "{{action('\Modules\Superadmin\Http\Controllers\PackagesController@getOptionVariables')}}",
				data: { option_id: 5, package_id: {{$packages->id}} },
				success: function(result) {
					$("div.monthly_total_sales").html(result);
				},
			});
			$("div.monthly_total_sales").modal('show');
		});
		$('#no_of_family_members').on('ifChecked', function(event){
			$.ajax({
				method: 'get',
				url: "{{action('\Modules\Superadmin\Http\Controllers\PackagesController@getOptionVariables')}}",
				data: { option_id: 6, package_id: {{$packages->id}} },
				success: function(result) {
					$("div.no_of_family_members").html(result);
				},
			});
			$("div.no_of_family_members").modal('show');
		});
		$('#no_of_vehicles').on('ifChecked', function(event){
			$.ajax({
				method: 'get',
				url: "{{action('\Modules\Superadmin\Http\Controllers\PackagesController@getOptionVariables')}}",
				data: { option_id: 7, package_id: {{$packages->id}} },
				success: function(result) {
					$("div.no_of_vehicles").html(result);
				},
			});
			$("div.no_of_vehicles").modal('show');
		});
		//on load page get save values
		$('document').ready(function(){
			@if($packages->number_of_branches)
			get_save_pacakge_variables('branch_veriables_modal', 0);
			@endif
			@if($packages->number_of_users)
			get_save_pacakge_variables('user_veriables_modal', 1);
			@endif
			@if($packages->number_of_products)
			get_save_pacakge_variables('product_veriables_modal', 2);
			@endif
			@if($packages->number_of_periods)
			get_save_pacakge_variables('period_veriables_modal', 3);
			@endif
			@if($packages->number_of_customers)
			get_save_pacakge_variables('number_of_customers', 4);
			@endif
			@if($packages->monthly_total_sales)
			get_save_pacakge_variables('monthly_total_sales', 5);
			@endif
			@if($packages->no_of_family_members)
			get_save_pacakge_variables('no_of_family_members', 6);
			@endif
			@if($packages->no_of_vehicles)
			get_save_pacakge_variables('no_of_vehicles', 7);
			@endif
		});
		function get_save_pacakge_variables(modal_name, option_id) {
			$.ajax({
				method: 'get',
				url: "{{action('\Modules\Superadmin\Http\Controllers\PackagesController@getOptionVariables')}}",
				data: { option_id: option_id, package_id: {{$packages->id}} },
				success: function(result) {
					$("div."+modal_name).html(result);
				},
			});
		}
	</script>
	<script>
		$('document').ready(function(){
			$('#currency_id').change(function () {
				var currency_id = $('#currency_id').val();
				$.ajax({
					method: 'post',
					url: '{{route("site_settings.getcurrency")}}',
					dataType: 'json',
					data: {
						currency_id : currency_id
					},
					success: function(result) {
						$('#currency_symbol').val(result.symbol);
					},
				});
			});
		});
		$('#account_access').on('ifChecked', function(event){
			$('#banking_module').iCheck('disable');
		});
		$('#account_access').on('ifUnchecked', function(event){
			$('#banking_module').iCheck('enable');
		});
		$('#banking_module').on('ifChecked', function(event){
			$('#account_access').iCheck('disable');
		});
		$('#banking_module').on('ifUnchecked', function(event){
			$('#account_access').iCheck('enable');
		});
		@if($package_permissions->account_access)
		$('#banking_module').iCheck('disable');
		@endif
		@if($packages->banking_module)
		$('#account_access').iCheck('disable');
		@endif
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
            $('#all_sales').iCheck('check');
            $('#add_sale').iCheck('check');
            $('#list_pos').iCheck('check');
            $('#pos_').iCheck('check');
            $('#pos_button_on_top_belt').iCheck('check');
            $('#list_order').iCheck('check');
            $('#upload_order').iCheck('check');
            $('#list_sell_return').iCheck('check');
            $('#shipment').iCheck('check');
            $('#discount').iCheck('check');
            $('#import_sale').iCheck('check');
            $('#reserved_stock').iCheck('check');
        })
        $('#all_sale_module').on('ifUnchecked', function (event) {
            $('#all_sales').iCheck('uncheck');
            $('#add_sale').iCheck('uncheck');
            $('#list_pos').iCheck('uncheck');
            $('#pos_').iCheck('uncheck');
            $('#pos_button_on_top_belt').iCheck('uncheck');
            $('#list_order').iCheck('uncheck');
            $('#upload_order').iCheck('uncheck');
            $('#list_sell_return').iCheck('uncheck');
            $('#shipment').iCheck('uncheck');
            $('#discount').iCheck('uncheck');
            $('#import_sale').iCheck('uncheck');
            $('#reserved_stock').iCheck('uncheck');
        })
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
