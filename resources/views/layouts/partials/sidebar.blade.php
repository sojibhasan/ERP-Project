@inject('request', 'Illuminate\Http\Request')
@php 
	$sidebar_setting = App\SiteSettings::where('id', 1)->select('ls_side_menu_bg_color', 'ls_side_menu_font_color', 'sub_module_color', 'sub_module_bg_color')->first();

	$module_array['disable_all_other_module_vr'] = 0;
	$module_array['enable_petro_module'] = 0;
	$module_array['enable_petro_dashboard'] = 0;
	$module_array['enable_petro_task_management'] = 0;
	$module_array['enable_petro_pump_dashboard'] = 0;
	$module_array['enable_petro_pumper_management'] = 0;
	$module_array['enable_petro_daily_collection'] = 0;
	$module_array['enable_petro_settlement'] = 0;
	$module_array['enable_petro_list_settlement'] = 0;
	$module_array['enable_petro_dip_management'] = 0;
	$module_array['enable_sale_cmsn_agent'] = 0;
	$module_array['enable_crm'] = 0;
	$module_array['mf_module'] = 0;
	$module_array['hr_module'] = 0;
	$module_array['employee'] = 0;
	$module_array['teminated'] = 0;
	$module_array['award'] = 0;
	$module_array['leave_request'] = 0;
	$module_array['attendance'] = 0;
	$module_array['import_attendance'] = 0;
	$module_array['late_and_over_time'] = 0;
	$module_array['payroll'] = 0;
	$module_array['salary_details'] = 0;
	$module_array['basic_salary'] = 0;
	$module_array['payroll_payments'] = 0;
	$module_array['hr_reports'] = 0;
	$module_array['notice_board'] = 0;
	$module_array['hr_settings'] = 0;
	$module_array['department'] = 0;
	$module_array['jobtitle'] = 0;
	$module_array['jobcategory'] = 0;
	$module_array['workingdays'] = 0;
	$module_array['workshift'] = 0;
	$module_array['holidays'] = 0;
	$module_array['leave_type'] = 0;
	$module_array['salary_grade'] = 0;
	$module_array['employment_status'] = 0;
	$module_array['salary_component'] = 0;
	$module_array['hr_prefix'] = 0;
	$module_array['hr_tax'] = 0;
	$module_array['religion'] = 0;
	$module_array['hr_setting_page'] = 0;
	$module_array['enable_sms'] = 0;
	$module_array['access_account'] = 0;
	$module_array['enable_booking'] = 0;
	$module_array['customer_order_own_customer'] = 0;
	$module_array['customer_settings'] = 0;
	$module_array['customer_order_general_customer'] = 0;
	$module_array['mpcs_module'] = 0;
	$module_array['fleet_module'] = 0;
	$module_array['ezyboat_module'] = 0;
	$module_array['merge_sub_category'] = 0;
	$module_array['backup_module'] = 0;
	$module_array['banking_module'] = 0;
	$module_array['products'] = 0;
	$module_array['purchase'] = 0;
	$module_array['stock_transfer'] = 0;
	$module_array['service_staff'] = 0;
	$module_array['enable_subscription'] = 0;
	$module_array['add_sale'] = 0;
	$module_array['stock_adjustment'] = 0;
	$module_array['tables'] = 0;
	$module_array['type_of_service'] = 0;
	$module_array['pos_sale'] = 0;
	$module_array['expenses'] = 0;
	$module_array['modifiers'] = 0;
	$module_array['kitchen'] = 0;
	$module_array['orders'] = 0;
	$module_array['enable_cheque_writing'] = 0;
	$module_array['issue_customer_bill'] = 0;
	$module_array['tasks_management'] = 0;
	$module_array['notes_page'] = 0;
	$module_array['tasks_page'] = 0;
	$module_array['reminder_page'] = 0;
	$module_array['member_registration'] = 0;
	$module_array['visitors_registration_module'] = 0;
	$module_array['visitors'] = 0;
	$module_array['visitors_registration'] = 0;
	$module_array['visitors_registration_setting'] = 0;
	$module_array['visitors_district'] = 0;
	$module_array['visitors_town'] = 0;
	$module_array['home_dashboard'] = 0;
	$module_array['contact_module'] = 0;
	$module_array['contact_supplier'] = 0;
	$module_array['contact_customer'] = 0;
	$module_array['contact_group_customer'] = 0;
	$module_array['import_contact'] = 0;
	$module_array['customer_reference'] = 0;
	$module_array['customer_statement'] = 0;
	$module_array['customer_payment'] = 0;
	$module_array['outstanding_received'] = 0;
	$module_array['issue_payment_detail'] = 0;
	$module_array['property_module'] = 0;
	$module_array['ran_module'] = 0;
	$module_array['report_module'] = 0;
	$module_array['product_report'] = 0;
	$module_array['payment_status_report'] = 0;
	$module_array['verification_report'] = 0;
	$module_array['activity_report'] = 0;
	$module_array['contact_report'] = 0;
	$module_array['trending_product'] = 0;
	$module_array['user_activity'] = 0;
	$module_array['verification_report'] = 0;
	$module_array['notification_template_module'] = 0;
	$module_array['settings_module'] = 0;
	$module_array['user_management_module'] = 0;
	$module_array['leads_module'] = 0;
	$module_array['leads'] = 0;
	$module_array['day_count'] = 0;
	$module_array['leads_import'] = 0;
	$module_array['leads_settings'] = 0;
	$module_array['sms_module'] = 0;
	$module_array['list_sms'] = 0;
	$module_array['status_order'] = 0;
	$module_array['list_orders'] = 0;
	$module_array['upload_orders'] = 0;
	$module_array['subcriptions'] = 0;
	$module_array['over_limit_sales'] = 0;
	$module_array['sale_module'] = 0;
	$module_array['all_sales'] = 0;
	$module_array['list_pos'] = 0;
	$module_array['list_draft'] = 0;
	$module_array['list_quotation'] = 0;
	$module_array['list_sell_return'] = 0;
	$module_array['shipment'] = 0;
	$module_array['discount'] = 0;
	$module_array['import_sale'] = 0;
	$module_array['reserved_stock'] = 0;
	$module_array['repair_module'] = 0;
	$module_array['catalogue_qr'] = 0;
	$module_array['business_settings'] = 0;
	$module_array['business_location'] = 0;
	$module_array['invoice_settings'] = 0;
	$module_array['tax_rates'] = 0;
	$module_array['list_easy_payment'] = 0;
	$module_array['payday'] = 0;

    $module_array['purchase_module'] = 0;
    $module_array['all_purchase'] = 0;
    $module_array['add_purchase'] = 0;
    $module_array['import_purchase'] = 0;
    $module_array['add_bulk_purchase'] = 0;
    $module_array['purchase_return'] = 0;
	foreach ($module_array as $key => $module_value) {
		${$key} = 0;
	}
	$business_id = request()->session()->get('user.business_id');
	$subscription = Modules\Superadmin\Entities\Subscription::active_subscription($business_id);
	if (!empty($subscription)) {
		$pacakge_details = $subscription->package_details;
		$disable_all_other_module_vr = 0;
		if (array_key_exists('disable_all_other_module_vr', $pacakge_details)) {
			$disable_all_other_module_vr = $pacakge_details['disable_all_other_module_vr'];
		}
		foreach ($module_array as $key => $module_value) {
			if ($disable_all_other_module_vr == 0) {
				if (array_key_exists($key, $pacakge_details)) {
					${$key} = $pacakge_details[$key];
				} else {
					${$key} = 0;
				}
			} else {
				${$key} = 0;
				$disable_all_other_module_vr = 1;
				$visitors_registration_module = 1;
				$visitors = 1;
				$visitors_registration = 1;
				$visitors_registration_setting = 1;
				$visitors_district = 1;
				$visitors_town = 1;
			}
		}
	}
	if ( auth()->user()->can('superadmin')) {
		foreach ($module_array as $key => $module_value) {
			${$key} = 1;
		}
		$disable_all_other_module_vr = 0;
	}
@endphp
<style>
    .skin-blue .main-sidebar {
    	background-color: @if( !empty($sidebar_setting->ls_side_menu_bg_color)) {{$sidebar_setting->ls_side_menu_bg_color}}
    	@endif;
    }
    .skin-blue .sidebar a {
    	color: @if( !empty($sidebar_setting->ls_side_menu_font_color)) {{$sidebar_setting->ls_side_menu_font_color}}
    	@endif;
    }
    .skin-blue .treeview-menu>li>a {
    	color: @if( !empty($sidebar_setting->sub_module_color)) {{$sidebar_setting->sub_module_color}}
    	@endif;
    }
    .skin-blue .sidebar-menu>li>.treeview-menu {
    	background: @if( !empty($sidebar_setting->sub_module_bg_color)) {{$sidebar_setting->sub_module_bg_color}}
    	@endif;
    }
</style>
<!-- Left side column. contains the logo and sidebar -->
<aside class="main-sidebar">
    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">
        @php $user = App\User::where('id', auth()->user()->id)->first(); $is_admin = $user->hasRole('Admin#' . request()->session()->get('business.id')) ? true : false; @endphp
        <!-- Sidebar Menu -->
        @if(session()->get('business.is_patient'))
        <ul class="sidebar-menu">
            @if(session()->get('business.is_patient'))
            <li class="{{ $request->segment(1) == 'patient' ? 'active' : '' }}">
                <a href="{{action('PatientController@index')}}"> <i class="fa fa-dashboard"></i> <span> @lang('home.home')</span> </a>
            </li>
            @endif @if(session()->get('business.is_hospital'))
            <li class="{{ $request->segment(1) == 'patient' ? 'active' : '' }}">
                <a href="{{action('HospitalController@index')}}"> <i class="fa fa-dashboard"></i> <span> @lang('home.home')</span> </a>
            </li>
            @endif
            <li class="{{ $request->segment(1) == 'reports' ? 'active' : '' }}">
                <a href="{{action('ReportController@getUserActivityReport')}}"><i class="fa fa-eercast"></i> @lang('report.user_activity')</a>
            </li>
            @if ($is_admin) @if(Module::has('Superadmin')) @includeIf('superadmin::layouts.partials.subscription') @endif @if(request()->session()->get('business.is_patient'))
            <li class="treeview @if( in_array($request->segment(1), ['family-members', 'superadmin', 'pay-online'])) {{'active active-sub'}} @endif">
                <a href="#" id="tour_step2_menu">
                    <i class="fa fa-cog"></i> <span>@lang('business.settings')</span>
                    <span class="pull-right-container">
                        <i class="fa fa-angle-left pull-right"></i>
                    </span>
                </a>
                <ul class="treeview-menu">
                    <li class="{{ $request->segment(1) == 'family-member' ? 'active' : '' }}">
                        <a href="{{action('FamilyController@index')}}"><i class="fa fa-users"></i> @lang('patient.family_member')</a>
                    </li>
                    <li class="{{ $request->segment(2) == 'family-subscription' ? 'active' : '' }}">
                        <a href="{{action('\Modules\Superadmin\Http\Controllers\FamilySubscriptionController@index')}}"><i class="fa fa-users"></i> @lang('patient.family_subscription')</a>
                    </li>
                    <li class="{{ $request->segment(1) == 'pay-online' && $request->segment(2) == 'create' ? 'active active-sub' : '' }}">
                        <a href="{{action('\Modules\Superadmin\Http\Controllers\PayOnlineController@create')}}">
                            <i class="fa fa-money"></i>
                            <span class="title">
                                @lang('superadmin::lang.pay_online')
                            </span>
                        </a>
                    </li>
                </ul>
            </li>
            @endif @endif
        </ul>
        @else
        <!-- The main POS System Menu -->
        <ul class="sidebar-menu">
            <!-- Call superadmin module if defined -->
            @if(Module::has('Superadmin')) @includeIf('superadmin::layouts.partials.sidebar') @endif
            <!-- Call ecommerce module if defined -->
            @if(Module::has('Ecommerce')) @includeIf('ecommerce::layouts.partials.sidebar') @endif
            <!-- <li class="header">HEADER</li> -->
            @if($home_dashboard) @if(auth()->user()->can('dashboard.data') && !auth()->user()->is_pump_operator && !auth()->user()->is_property_user)
            <li class="{{ $request->segment(1) == 'home' ? 'active' : '' }}">
                <a href="{{action('HomeController@index')}}"> <i class="fa fa-dashboard"></i> <span> @lang('home.home')</span> </a>
            </li>
            @endif @endif @if(auth()->user()->is_pump_operator) @if(auth()->user()->can('pump_operator.dashboard'))
            <li class="{{ $request->segment(1) == 'petro' && $request->segment(2) == 'pump-operators' && $request->segment(3) == 'dashboard' ? 'active' : '' }}">
                <a href="{{action('\Modules\Petro\Http\Controllers\PumpOperatorController@dashboard')}}"><i class="fa fa-tachometer"></i> <span>@lang('petro::lang.dashboard')</span></a>
            </li>
            @endif
            <li class="{{ $request->segment(1) == 'petro' && $request->segment(2) == 'pump-operators' && $request->segment(3) == 'pumper-day-entries' ? 'active' : '' }}">
                <a href="{{action('\Modules\Petro\Http\Controllers\PumperDayEntryController@index')}}"><i class="fa fa-calculator"></i> <span>@lang('petro::lang.pumper_day_entries')</span></a>
            </li>
            @endif 
			@if(auth()->user()->is_customer == 0) 
				@if(auth()->user()->can('crm.view')) 
					@if($enable_crm == 1)
						<li class="treeview {{ in_array($request->segment(1), ['crm']) ? 'active active-sub' : '' }}">
							<a href="#">
								<i class="fa fa-connectdevelop"></i>
								<span class="title">@lang('lang_v1.crm')</span>
								<span class="pull-right-container">
									<i class="fa fa-angle-left pull-right"></i>
								</span>
							</a>
							<ul class="treeview-menu">
								@can('crm.view')
								<li class="{{ $request->segment(1) == 'crm' && $request->input('type') == 'customer' ? 'active' : '' }}">
									<a href="{{action('CRMController@index')}}"><i class="fa fa-star"></i> @lang('lang_v1.crm')</a>
								</li>
								<li class="{{ $request->segment(1) == 'crmgroups' ? 'active' : '' }}">
									<a href="{{action('CrmGroupController@index')}}"><i class="fa fa-object-group"></i> @lang('lang_v1.crm_group')</a>
								</li>
								@endcan
								<li class="{{ $request->segment(1) == 'crm-activity' ? 'active' : '' }}">
									<a href="{{action('CRMActivityController@index')}}"><i class="fa fa-object-group"></i> @lang('lang_v1.crm_activity')</a>
								</li>
							</ul>
						</li>
					@endif 
				@endif
			@endif 
			@if($leads_module) 
				@includeIf('leads::layouts.partials.sidebar') 
			@endif 
			@if(Auth::guard('agent')->check()) 
				@includeIf('agent::layouts.partials.sidebar') 
			@endif 
			@if($contact_module)
				@if(auth()->user()->can('supplier.view') || auth()->user()->can('customer.view') )
					<li class="treeview {{ in_array($request->segment(1), ['contacts', 'customer-group', 'contact-group', 'customer-reference', 'customer-statement', 'outstanding-received-report']) ? 'active active-sub' : '' }}" id="tour_step4">
						<a href="#" id="tour_step4_menu">
							<i class="fa fa-address-book"></i> <span>@lang('contact.contacts')</span>
							<span class="pull-right-container">
								<i class="fa fa-angle-left pull-right"></i>
							</span>
						</a>
						<ul class="treeview-menu">
							@if($contact_supplier) @can('supplier.view')
							<li class="{{ $request->input('type') == 'supplier' ? 'active' : '' }}">
								<a href="{{action('ContactController@index', ['type' => 'supplier'])}}"><i class="fa fa-star"></i> @lang('report.supplier')</a>
							</li>
							@endcan @endif @can('customer.view') @if($contact_customer) {{-- @if(!$property_module)--}}
							<li class="{{ $request->input('type') == 'customer' ? 'active' : '' }}">
								<a href="{{action('ContactController@index', ['type' => 'customer'])}}"><i class="fa fa-star"></i> @lang('report.customer')</a>
							</li>
							{{-- @endif--}} @endif @if($contact_group_customer)
							<li class="{{ $request->segment(1) == 'contact-group' ? 'active' : '' }}">
								<a href="{{action('ContactGroupController@index')}}"><i class="fa fa-users"></i> @lang('lang_v1.contact_groups')</a>
							</li>
							@endif @endcan @if($import_contact) @if(!$property_module && $contact_customer) @if(auth()->user()->can('supplier.create') || auth()->user()->can('customer.create') )
							<li class="{{ $request->segment(1) == 'contacts' && $request->segment(2) == 'import' ? 'active' : '' }}">
								<a href="{{action('ContactController@getImportContacts')}}"><i class="fa fa-download"></i> @lang('lang_v1.import_contacts')</a>
							</li>
							@endcan @endif @endif @if($customer_reference) @if($enable_petro_module && $contact_customer && !$property_module)
							<li class="{{ $request->segment(1) == 'customer-reference' ? 'active' : '' }}">
								<a href="{{action('CustomerReferenceController@index')}}"><i class="fa fa-link"></i> @lang('lang_v1.customer_reference')</a>
							</li>
							@endif @endif @if($contact_customer) @if($customer_statement)
							<li class="{{ $request->segment(1) == 'customer-statement' ? 'active' : '' }}">
								<a href="{{action('CustomerStatementController@index')}}"><i class="fa fa-paperclip"></i> @lang('contact.customer_statements')</a>
							</li>
							@endif @if($customer_payment)
							<li class="{{ $request->segment(1) == 'customer-payment-simple' ? 'active' : '' }}">
								<a href="{{action('CustomerPaymentController@index')}}"><i class="fa fa-money"></i> @lang('lang_v1.customer_payments')</a>
							</li>
							@endif @if($outstanding_received)
							<li class="{{ $request->segment(1) == 'outstanding-received-report' ? 'active' : '' }}">
								<a href="{{action('ContactController@getOutstandingReceivedReport')}}"><i class="fa fa-arrow-right"></i> @lang('lang_v1.outstanding_received')</a>
							</li>
							@endif @endif @if($contact_supplier) @if($issue_payment_detail)
							<li class="{{ $request->segment(1) == 'issued-payment-details' ? 'active' : '' }}">
								<a href="{{action('ContactController@getIssuedPaymentDetails')}}"><i class="fa fa-arrow-left"></i> @lang('lang_v1.issued_payment_details')</a>
							</li>
							@endif @endif
						</ul>
					</li>
				@endif
			@endif 
			@if($property_module) 
				@includeIf('property::layouts.partials.sidebar')
			@endif
			@if($products) 
				@if(auth()->user()->can('product.view') || auth()->user()->can('product.create') || auth()->user()->can('brand.view') || auth()->user()->can('unit.view') || auth()->user()->can('category.view') || auth()->user()->can('brand.create') || auth()->user()->can('unit.create') || auth()->user()->can('category.create') )
					<li class="treeview {{ in_array($request->segment(1), ['variation-templates', 'products', 'labels', 'import-products', 'import-opening-stock', 'selling-price-group', 'brands', 'units', 'categories', 'warranties']) ? 'active active-sub' : '' }}"
						id="tour_step5">
						<a href="#" id="tour_step5_menu">
							<i class="fa fa-cubes"></i> <span>@lang('sale.products')</span>
							<span class="pull-right-container">
								<i class="fa fa-angle-left pull-right"></i>
							</span>
						</a>
						<ul class="treeview-menu">
							@can('product.view')
							<li class="{{ $request->segment(1) == 'products' && $request->segment(2) == '' ? 'active' : '' }}">
								<a href="{{action('ProductController@index')}}"><i class="fa fa-list"></i>@lang('lang_v1.list_products')</a>
							</li>
							@endcan @can('product.create')
							<li class="{{ $request->segment(1) == 'products' && $request->segment(2) == 'create' ? 'active' : '' }}">
								<a href="{{action('ProductController@create')}}"><i class="fa fa-plus-circle"></i>@lang('product.add_product')</a>
							</li>
							@endcan @can('product.view')
							<li class="{{ $request->segment(1) == 'labels' && $request->segment(2) == 'show' ? 'active' : '' }}">
								<a href="{{action('LabelsController@show')}}"><i class="fa fa-barcode"></i>@lang('barcode.print_labels')</a>
							</li>
							@endcan @can('product.create')
							<li class="{{ $request->segment(1) == 'variation-templates' ? 'active' : '' }}">
								<a href="{{action('VariationTemplateController@index')}}"><i class="fa fa-circle-o"></i><span>@lang('product.variations')</span></a>
							</li>
							@endcan @can('product.create')
							<li class="{{ $request->segment(1) == 'import-products' ? 'active' : '' }}">
								<a href="{{action('ImportProductsController@index')}}"><i class="fa fa-download"></i><span>@lang('product.import_products')</span></a>
							</li>
							@endcan @if(session()->get('business.is_pharmacy'))
							<li class="{{ $request->segment(1) == 'sample-medical-product-list' ? 'active' : '' }}">
								<a href="{{action('SampleMedicalProductController@index')}}"><i class="fa fa-download"></i><span>@lang('lang_v1.sample_medical_product_list')</span></a>
							</li>
							@endif @can('product.opening_stock')
							<li class="{{ $request->segment(1) == 'import-opening-stock' ? 'active' : '' }}">
								<a href="{{action('ImportOpeningStockController@index')}}"><i class="fa fa-download"></i><span>@lang('lang_v1.import_opening_stock')</span></a>
							</li>
							@endcan @can('product.create')
							<li class="{{ $request->segment(1) == 'selling-price-group' ? 'active' : '' }}">
								<a href="{{action('SellingPriceGroupController@index')}}"><i class="fa fa-circle-o"></i><span>@lang('lang_v1.selling_price_group')</span></a>
							</li>
							@endcan @if(auth()->user()->can('unit.view') || auth()->user()->can('unit.create'))
							<li class="{{ $request->segment(1) == 'units' ? 'active' : '' }}">
								<a href="{{action('UnitController@index')}}"><i class="fa fa-balance-scale"></i> <span>@lang('unit.units')</span></a>
							</li>
							@endif @if(auth()->user()->can('category.view') || auth()->user()->can('category.create'))
							<li class="{{ $request->segment(1) == 'categories' ? 'active' : '' }}">
								<a href="{{action('CategoryController@index')}}"><i class="fa fa-tags"></i> <span>@lang('category.categories') </span></a>
							</li>
							@endif @if(auth()->user()->can('brand.view') || auth()->user()->can('brand.create'))
							<li class="{{ $request->segment(1) == 'brands' ? 'active' : '' }}">
								<a href="{{action('BrandController@index')}}"><i class="fa fa-diamond"></i> <span>@lang('brand.brands')</span></a>
							</li>
							@endif
							<li class="{{ $request->segment(1) == 'warranties' ? 'active active-sub' : '' }}">
								<a href="{{action('WarrantyController@index')}}">
									<i class="fa fa-shield"></i>
									<span class="title">@lang('lang_v1.warranties')</span>
								</a>
							</li>
							@if($enable_petro_module) @if($merge_sub_category)
							<li class="{{ $request->segment(1) == 'merged-sub-categories' ? 'active active-sub' : '' }}">
								<a href="{{action('MergedSubCategoryController@index')}}">
									<i class="fa fa-compress"></i>
									<span class="title">@lang('lang_v1.merged_sub_categories')</span>
								</a>
							</li>
							@endif @endif
						</ul>
					</li>
				@endif
			@endif
            <!-- Start Petro Module -->
            @if($enable_petro_module) @if(auth()->user()->can('petro.access')) @includeIf('petro::layouts.partials.sidebar') @endif @endif
            <!-- End Petro Module -->
            <!-- Start MPCS Module -->
            @if($mpcs_module) @if(auth()->user()->can('mpcs.access')) @includeIf('mpcs::layouts.partials.sidebar') @endif @endif
            <!-- End MPCS Module -->
            <!-- Start Fleet Module -->
            @if($fleet_module) @if(auth()->user()->can('fleet.access')) @includeIf('fleet::layouts.partials.sidebar') @endif @endif
            <!-- End Fleet Module -->
            <!-- Start Ezyboat Module -->
            @if($ezyboat_module) {{-- @if(auth()->user()->can('ezyboat.access')) --}} @includeIf('ezyboat::layouts.partials.sidebar') {{-- @endif --}} @endif
            <!-- End Ezyboat Module -->
            <!-- Start Gold Module -->
            @if($ran_module) @if(auth()->user()->can('ran.access')) @includeIf('ran::layouts.partials.sidebar') @endif @endif
            <!-- End Gold Module -->
            @if(Module::has('Manufacturing')) @if($mf_module) @if(auth()->user()->is_customer == 0) @if(auth()->user()->can('manufacturing.access_recipe') || auth()->user()->can('manufacturing.access_production') )
            @include('manufacturing::layouts.partials.sidebar') @endif @endif @endif @endif 
            
            @if($purchase && $purchase_module) 
                @if(in_array('purchase', $enabled_modules)) 
                    @if(auth()->user()->can('purchase.view') || auth()->user()->can('purchase.create') || auth()->user()->can('purchase.update') )
                        <li class="treeview {{in_array($request->segment(1), ['purchases', 'purchase-return', 'import-purchases']) ? 'active active-sub' : '' }}" id="tour_step6">
                            <a href="#" id="tour_step6_menu">
                                <i class="fa fa-arrow-circle-down"></i>
                                <span>@lang('purchase.purchases')</span>
                                <span class="pull-right-container">
                                    <i class="fa fa-angle-left pull-right"></i>
                                </span>
                            </a>
                            <ul class="treeview-menu">
                                @if($all_purchase)
                                <li class="{{ $request->segment(1) == 'purchases' && $request->segment(2) == null ? 'active' : '' }}">
                                    <a href="{{action('PurchaseController@index')}}"><i class="fa fa-list"></i>@lang('purchase.list_purchase')</a>
                                </li>
                                @endif 
                                @if($add_bulk_purchase)
                                <li class="{{ $request->segment(1) == 'purchases' && $request->segment(2) == 'add-purchase-bulk' ? 'active' : '' }}">
                                    <a href="{{action('PurchaseController@addPurchaseBulk')}}"><i class="fa fa-stack-exchange"></i> @lang('purchase.add_purchase_bulk')</a>
                                </li>
                                @endif
                                @if($add_purchase)
                                <li class="{{ $request->segment(1) == 'purchases' && $request->segment(2) == 'create' ? 'active' : '' }}">
                                    <a href="{{action('PurchaseController@create')}}"><i class="fa fa-plus-circle"></i> @lang('purchase.add_purchase')</a>
                                </li>
                                @endif 
                                @if($purchase_return)
                                <li class="{{ $request->segment(1) == 'purchase-return' ? 'active' : '' }}">
                                    <a href="{{action('PurchaseReturnController@index')}}"><i class="fa fa-undo"></i> @lang('lang_v1.list_purchase_return')</a>
                                </li>
                                @endif
                                @if($import_purchase)
                                <li class="{{ $request->segment(1) == 'import-purchases'? 'active' : '' }}">
                                    <a href="{{action('ImportPurchasesController@index')}}"><i class="fa fa-recycle"></i>@lang('lang_v1.import_purchases')</a>
                                </li>
                                @endif
                            </ul>
                        </li>
                    @endif
                @endif
            @endif 
            @if($sale_module) @if(in_array('add_sale', $enabled_modules)) @if(auth()->user()->can('sell.view') || auth()->user()->can('sell.create') || auth()->user()->can('direct_sell.access') ||
            auth()->user()->can('view_own_sell_only'))
            <li class="treeview {{  in_array( $request->segment(1), ['sales', 'pos', 'sell-return', 'ecommerce', 'discount', 'shipments', 'import-sales', 'reserved-stocks']) ? 'active active-sub' : '' }}" id="tour_step7">
                <a href="#" id="tour_step7_menu">
                    <i class="fa fa-arrow-circle-up"></i> <span>@lang('sale.sale')</span>
                    <span class="pull-right-container">
                        <i class="fa fa-angle-left pull-right"></i>
                    </span>
                </a>
                <ul class="treeview-menu">
                    @if($all_sales) @if(auth()->user()->can('direct_sell.access') || auth()->user()->can('view_own_sell_only'))
                    <li class="{{ $request->segment(1) == 'sales' && $request->segment(2) == null ? 'active' : '' }}">
                        <a href="{{action('SellController@index')}}"><i class="fa fa-list"></i>@lang('lang_v1.all_sales')</a>
                    </li>
                    @endif @endif
                    <!-- Call superadmin module if defined -->
                    @if(Module::has('Ecommerce')) @includeIf('ecommerce::layouts.partials.sell_sidebar') @endif @if($add_sale) @can('direct_sell.access')
                    <li class="{{ $request->segment(1) == 'sales' && $request->segment(2) == 'create' ? 'active' : '' }}">
                        <a href="{{action('SellController@create')}}"><i class="fa fa-plus-circle"></i>@lang('sale.add_sale')</a>
                    </li>
                    @endcan @endif @if($list_pos) @can('sell.view')
                    <li class="{{ $request->segment(1) == 'pos' && $request->segment(2) == null ? 'active' : '' }}">
                        <a href="{{action('SellPosController@index')}}"><i class="fa fa-list"></i>@lang('sale.list_pos')</a>
                </li>
                    @endcan @endif @if(in_array('pos_sale', $enabled_modules)) @can('sell.create')
                    <li class="{{ $request->segment(1) == 'pos' && $request->segment(2) == 'create' ? 'active' : '' }}">
                        <a href="{{action('SellPosController@create')}}"><i class="fa fa-plus-circle"></i>@lang('sale.pos_sale')</a>
                    </li>
                    @endcan @endif @if($list_draft) @can('list_drafts')
                    <li class="{{ $request->segment(1) == 'sales' && $request->segment(2) == 'drafts' ? 'active' : '' }}">
                        <a href="{{action('SellController@getDrafts')}}"><i class="fa fa-pencil-square" aria-hidden="true"></i>@lang('lang_v1.list_drafts')</a>
                    </li>
                    @endcan @endif @if($list_quotation) @can('list_quotations')
                    <li class="{{ $request->segment(1) == 'sales' && $request->segment(2) == 'quotations' ? 'active' : '' }}">
                        <a href="{{action('SellController@getQuotations')}}"><i class="fa fa-pencil-square" aria-hidden="true"></i>@lang('lang_v1.list_quotations')</a>
                    </li>
                    @endcan @endif @if($customer_order_own_customer == 1 || $customer_order_general_customer == 1) @if($list_orders)
                    <li class="{{ $request->segment(1) == 'sales' && $request->segment(2) == 'customer-orders' ? 'active' : '' }}">
                        <a href="{{action('SellController@getCustomerOrders')}}"><i class="fa fa-pencil-square" aria-hidden="true"></i>@lang('lang_v1.list_orders')</a>
                    </li>
                    @endif @if($upload_orders)
                    <li class="{{ $request->segment(1) == 'sales' && $request->segment(2) == 'customer-orders' ? 'active' : '' }}">
                        <a href="{{action('SellController@getCustomerUploadedOrders')}}"><i class="fa fa-upload" aria-hidden="true"></i>@lang('customer.uploaded_orders')</a>
                    </li>
                    @endif @endif @if($list_sell_return) @can('sell.view')
                    <li class="{{ $request->segment(1) == 'sell-return' && $request->segment(2) == null ? 'active' : '' }}">
                        <a href="{{action('SellReturnController@index')}}"><i class="fa fa-undo"></i>@lang('lang_v1.list_sell_return')</a>
                    </li>
                    @endcan @endif @if($shipment) @can('access_shipping')
                    <li class="{{ $request->segment(1) == 'shipments' ? 'active' : '' }}">
                        <a href="{{action('SellController@shipments')}}"><i class="fa fa-truck"></i>@lang('lang_v1.shipments')</a>
                    </li>
                    @endcan @endif @if($discount) @can('discount.access')
                    <li class="{{ $request->segment(1) == 'discount' ? 'active' : '' }}">
                        <a href="{{action('DiscountController@index')}}"><i class="fa fa-percent"></i>@lang('lang_v1.discounts')</a>
                    </li>
                    @endcan @endif @if($subcriptions) @if(in_array('subscription', $enabled_modules) && auth()->user()->can('direct_sell.access'))
                    <li class="{{ $request->segment(1) == 'subscriptions'? 'active' : '' }}">
                        <a href="{{action('SellPosController@listSubscriptions')}}"><i class="fa fa-recycle"></i>@lang('lang_v1.subscriptions')</a>
                    </li>
                    @endif @endif @if($import_sale)
                    <li class="{{ $request->segment(1) == 'import-sales'? 'active' : '' }}">
                        <a href="{{action('ImportSalesController@index')}}"><i class="fa fa-recycle"></i>@lang('lang_v1.import_sales')</a>
                    </li>
                    @endif @if($reserved_stock)
                    <li class="{{ $request->segment(1) == 'reserved-stocks'? 'active' : '' }}">
                        <a href="{{action('ReservedStocksController@index')}}"><i class="fa fa-recycle"></i>@lang('lang_v1.reserved_stocks')</a>
                    </li>
                    @endif @if($customer_settings) @if($over_limit_sales)
                    <li class="{{ $request->segment(1) == 'sales' && $request->segment(2) == 'over-limit-sales' ? 'active' : '' }}">
                        <a href="{{action('SellController@overLimitSales')}}"><i class="fa fa-plus-circle"></i>@lang('sale.over_limit_sales')</a>
                    </li>
                    @endif @endif
                </ul>
            </li>
            @endif @endif @endif @if(Module::has('Repair')) @if($repair_module) @if(auth()->user()->can('repair.access'))
            <li class="{{ $request->segment(1) == 'repair' ? 'active' : '' }}">
                <a href="{{action('\Modules\Repair\Http\Controllers\DashboardController@index')}}"><i class="fa fa-wrench"></i><span>@lang('lang_v1.repair')</span></a>
            </li>
            @endif @endif @endif @if($stock_transfer) @if(in_array('stock_transfer', $enabled_modules)) @if(auth()->user()->can('purchase.view') || auth()->user()->can('purchase.create') )
            <li class="treeview {{ $request->segment(1) == 'stock-transfers' || $request->segment(1) == 'stock-transfers-request'  ? 'active active-sub' : '' }}">
                <a href="#">
                    <i class="fa fa-truck" aria-hidden="true"></i> <span>@lang('lang_v1.stock_transfers')</span>
                    <span class="pull-right-container">
                        <i class="fa fa-angle-left pull-right"></i>
                    </span>
                </a>
                <ul class="treeview-menu">
                    @can('purchase.view')
                    <li class="{{ $request->segment(1) == 'stock-transfers' && $request->segment(2) == null ? 'active' : '' }}">
                        <a href="{{action('StockTransferController@index')}}"><i class="fa fa-list"></i>@lang('lang_v1.list_stock_transfers')</a>
                    </li>
                    @endcan @can('purchase.create')
                    <li class="{{ $request->segment(1) == 'stock-transfers' && $request->segment(2) == 'create' ? 'active' : '' }}">
                        <a href="{{action('StockTransferController@create')}}"><i class="fa fa-plus-circle"></i>@lang('lang_v1.add_stock_transfer')</a>
                    </li>
                    @endcan {{-- @can('purchase.create') --}}
                    <li class="{{ $request->segment(1) == 'stock-transfers-request' && $request->segment(2) == null ? 'active' : '' }}">
                        <a href="{{action('StockTransferRequestController@index')}}"><i class="fa fa-question-circle"></i>@lang('lang_v1.stock_transfer_request')</a>
                    </li>
                    {{-- @endcan --}}
                    <li>
                        <a href="{{ url('List_Store_Transaction') }}"><i class="fa fa-list"></i>@lang('lang_v1.list_store_transactions')</a>
                    </li>
                    <li></li>
                </ul>
            </li>
            @endif @endif @endif {{-- @if($stock_adjustment)--}} {{-- @if(in_array('stock_adjustment', $enabled_modules))--}} {{-- @if(auth()->user()->can('purchase.view') || auth()->user()->can('purchase.create') )--}}
            <li class="treeview {{ $request->segment(1) == 'stock-adjustments' ? 'active active-sub' : '' }}">
                <a href="#">
                    <i class="fa fa-database" aria-hidden="true"></i>
                    <span>@lang('stock_adjustment.stock_adjustment')</span>
                    <span class="pull-right-container">
                        <i class="fa fa-angle-left pull-right"></i>
                    </span>
                </a>
                <ul class="treeview-menu">
                    @can('purchase.view')
                    <li class="{{ $request->segment(1) == 'stock-adjustments' && $request->segment(2) == null ? 'active' : '' }}">
                        <a href="{{action('StockAdjustmentController@index')}}"><i class="fa fa-list"></i>@lang('stock_adjustment.list')</a>
                    </li>
                    @endcan @can('purchase.create')
                    <li class="{{ $request->segment(1) == 'stock-adjustments' && $request->segment(2) == 'create' ? 'active' : '' }}">
                        <a href="{{action('StockAdjustmentController@create')}}"><i class="fa fa-plus-circle"></i>@lang('stock_adjustment.add')</a>
                    </li>
                    @endcan
                </ul>
            </li>
            {{-- @endif--}} {{-- @endif--}} {{-- @endif--}} @if($expenses) @if(in_array('expenses', $enabled_modules)) @if(auth()->user()->can('expense.access'))
            <li class="treeview {{  in_array( $request->segment(1), ['expense-categories', 'expenses']) ? 'active active-sub' : '' }}">
                <a href="#">
                    <i class="fa fa-minus-circle"></i> <span>@lang('expense.expenses')</span>
                    <span class="pull-right-container">
                        <i class="fa fa-angle-left pull-right"></i>
                    </span>
                </a>
                <ul class="treeview-menu">
                    <li class="{{ $request->segment(1) == 'expenses' && empty($request->segment(2)) ? 'active' : '' }}">
                        <a href="{{action('ExpenseController@index')}}"><i class="fa fa-list"></i>@lang('lang_v1.list_expenses')</a>
                    </li>
                    <li class="{{ $request->segment(1) == 'expenses' && $request->segment(2) == 'create' ? 'active' : '' }}">
                        <a href="{{action('ExpenseController@create')}}"><i class="fa fa-plus-circle"></i>@lang('messages.add') @lang('expense.expenses')</a>
                    </li>
                    <li class="{{ $request->segment(1) == 'expense-categories' ? 'active' : '' }}">
                        <a href="{{action('ExpenseCategoryController@index')}}"><i class="fa fa-circle-o"></i>@lang('expense.expense_categories')</a>
                    </li>
                </ul>
            </li>
            @endif @endif @endif
            <!-- Start hr Module -->
            @if($hr_module) @includeIf('hr::layouts.partials.sidebar') @endif
            <!-- End hr Module -->
            <!-- Start PayRoll Module -->
            @if($payday) @if(auth()->user()->can('payday') && !auth()->user()->is_pump_operator && !auth()->user()->is_property_user)
            <li>
                <a href="javascript:void(0);" id="login_payroll"> <i class="fa fa-briefcase"></i> <span> PayRoll</span> </a>
            </li>
            @endif @endif
            <!-- End PayRoll Module -->
            <!-- Start Task Management Module -->
            @if($tasks_management)
                @can('tasks_management.access')
                    @includeIf('tasksmanagement::layouts.partials.sidebar')
                @endcan 
            @endif
            <!-- End Task Management Module -->
            @if($banking_module == 1 || $access_account == 1)
                @if(in_array('account', $enabled_modules) || in_array('banking_module', $enabled_modules)) 
                    @can('account.access')
                        <li class="treeview {{ $request->segment(1) == 'accounting-module' ? 'active active-sub' : '' }}">
                <a href="#">
                    <i class="fa fa-money" aria-hidden="true"></i> <span>@if($access_account) @lang('account.accounting_module') @else @lang('account.banking_module') @endif</span>
                    <span class="pull-right-container">
                        <i class="fa fa-angle-left pull-right"></i>
                    </span>
                </a>
                <ul class="treeview-menu">
                    <li class="{{ $request->segment(1) == 'accounting-module' && $request->segment(2) == 'account' ? 'active' : '' }}">
                        <a href="{{action('AccountController@index')}}"><i class="fa fa-list"></i>@lang('account.list_accounts')</a>
                    </li>
                    <li class="{{ $request->segment(1) == 'accounting-module' && $request->segment(2) == 'disabled-account' ? 'active' : '' }}">
                        <a href="{{action('AccountController@disabledAccount')}}"><i class="fa fa-times"></i>@lang('account.disabled_account')</a>
                    </li>
                    <li class="{{ $request->segment(1) == 'accounting-module' && $request->segment(2) == 'journals' ? 'active' : '' }}">
                        <a href="{{action('JournalController@index')}}"><i class="fa fa-book"></i>@lang('account.list_journals')</a>
                    </li>
                    <li class="{{ $request->segment(1) == 'accounting-module' && $request->segment(2) == 'get-profit-loss-report' ? 'active' : '' }}">
                        <a href="{{action('AccountController@getProfitLossReport')}}"><i class="fa fa-file-text"></i>@lang('lang_v1.profit_loss_report')</a>
                    </li>
                    <li class="{{ $request->segment(1) == 'accounting-module' && $request->segment(2) == 'income-statement' ? 'active' : '' }}">
                        <a href="{{action('AccountReportsController@incomeStatement')}}"><i class="fa fa-book"></i>@lang('account.income_statement')</a>
                    </li>
                    <li class="{{ $request->segment(1) == 'accounting-module' && $request->segment(2) == 'balance-sheet' ? 'active' : '' }}">
                        <a href="{{action('AccountReportsController@balanceSheet')}}"><i class="fa fa-book"></i>@lang('account.balance_sheet')</a>
                    </li>
                    <li class="{{ $request->segment(1) == 'accounting-module' && $request->segment(2) == 'trial-balance' ? 'active' : '' }}">
                        <a href="{{action('AccountReportsController@trialBalance')}}"><i class="fa fa-balance-scale"></i>@lang('account.trial_balance')</a>
                    </li>
                    <li class="{{ $request->segment(1) == 'accounting-module' && $request->segment(2) == 'cash-flow' ? 'active' : '' }}">
                        <a href="{{action('AccountController@cashFlow')}}"><i class="fa fa-exchange"></i>@lang('lang_v1.cash_flow')</a>
                    </li>
                    <li class="{{ $request->segment(1) == 'accounting-module' && $request->segment(2) == 'payment-account-report' ? 'active' : '' }}">
                        <a href="{{action('AccountReportsController@paymentAccountReport')}}"><i class="fa fa-file-text-o"></i>@lang('account.payment_account_report')</a>
                    </li>
                    <li class="{{ $request->segment(1) == 'accounting-module' && $request->segment(2) == 'import' ? 'active' : '' }}">
                        <a href="{{action('AccountController@getImportAccounts')}}"><i class="fa fa-download"></i>@lang('lang_v1.import_accounts')</a>
                    </li>
                </ul>
            </li>
                    @endcan
                @endif
            @endif
            @if($report_module) @if(auth()->user()->can('report.access'))
            <li class="treeview {{  in_array( $request->segment(1), ['reports']) ? 'active active-sub' : '' }}" id="tour_step8">
                <a href="#" id="tour_step8_menu">
                    <i class="fa fa-bar-chart-o"></i> <span>@lang('report.reports')</span>
                    <span class="pull-right-container">
                        <i class="fa fa-angle-left pull-right"></i>
                    </span>
                </a>
                <ul class="treeview-menu">
                    @if($product_report) @if(auth()->user()->can('stock_report.view') || auth()->user()->can('stock_adjustment_report.view') || auth()->user()->can('item_report.view') || auth()->user()->can('product_purchase_report.view')
                    || auth()->user()->can('product_sell_report.view') || auth()->user()->can('product_transaction_report.view') )
                    <li class="{{ $request->segment(2) == 'product' ? 'active' : '' }}">
                        <a href="{{action('ReportController@getProductReport')}}"><i class="fa fa-hourglass-half"></i>@lang('report.product_report')</a>
                    </li>
                    @endif @endif @if($payment_status_report) @if(auth()->user()->can('purchase_payment_report.view') || auth()->user()->can('sell_payment_report.view') || auth()->user()->can('outstanding_received_report.view') ||
                    auth()->user()->can('aging_report.view') )
                    <li class="{{ $request->segment(2) == 'payment-status' ? 'active' : '' }}">
                        <a href="{{action('ReportController@getPaymentStatusReport')}}"><i class="fa fa-money"></i>@lang('report.payment_status_report')</a>
                    </li>
                    @endif @endif @if(auth()->user()->can('daily_report.view') || auth()->user()->can('daily_summary_report.view') || auth()->user()->can('register_report.view') || auth()->user()->can('profit_loss_report.view') )
                    <li class="{{ $request->segment(2) == 'management' ? 'active' : '' }}">
                        <a href="{{action('ReportController@getManagementReport')}}"><i class="fa fa-briefcase"></i>@lang('report.management_report')</a>
                    </li>
                    @endif @if($verification_report)
                    <li class="{{ $request->segment(2) == 'verification' ? 'active' : '' }}">
                        <a href="{{action('ReportController@getVerificationReport')}}"><i class="fa fa-check-circle"></i>@lang('report.verification_reports')</a>
                    </li>
                    @endif @if($activity_report) @if(auth()->user()->can('sales_report.view') || auth()->user()->can('purchase_and_slae_report.view') || auth()->user()->can('expense_report.view') ||
                    auth()->user()->can('sales_representative.view') || auth()->user()->can('tax_report.view') )
                    <li class="{{ $request->segment(2) == 'activity' ? 'active' : '' }}">
                        <a href="{{action('ReportController@getActivityReport')}}"><i class="fa fa-user-secret"></i>@lang('report.activity_report')</a>
                    </li>
                    @endif @endif @if($contact_report) @can('contact_report.view')
                    <li class="{{ $request->segment(2) == 'contact' ? 'active' : '' }}">
                        <a href="{{action('ReportController@getContactReport')}}"><i class="fa fa-address-book"></i>@lang('report.contact_report')</a>
                    </li>
                    @endcan @endif @can('stock_report.view') @if(session('business.enable_product_expiry') == 1)
                    <li class="{{ $request->segment(2) == 'stock-expiry' ? 'active' : '' }}">
                        <a href="{{action('ReportController@getStockExpiryReport')}}"><i class="fa fa-calendar-times-o"></i>@lang('report.stock_expiry_report')</a>
                    </li>
                    @endif @endcan @can('stock_report.view') @if(session('business.enable_lot_number') == 1)
                    <li class="{{ $request->segment(2) == 'lot-report' ? 'active' : '' }}">
                        <a href="{{action('ReportController@getLotReport')}}"><i class="fa fa-hourglass-half" aria-hidden="true"></i>@lang('lang_v1.lot_report')</a>
                    </li>
                    @endif @endcan @if($trending_product) @can('trending_products.view')
                    <li class="{{ $request->segment(2) == 'trending-products' ? 'active' : '' }}">
                        <a href="{{action('ReportController@getTrendingProducts')}}"><i class="fa fa-line-chart" aria-hidden="true"></i>@lang('report.trending_products')</a>
                    </li>
                    @endcan @endif @if($user_activity) @can('user_activity.view')
                    <li class="{{ $request->segment(2) == 'user_activity' ? 'active' : '' }}">
                        <a href="{{action('ReportController@getUserActivityReport')}}"><i class="fa fa-eercast" aria-hidden="true"></i>@lang('report.user_activity')</a>
                    </li>
                    @endcan @endif @if($tables) @if(in_array('tables', $enabled_modules)) @can('purchase_n_sell_report.view')
                    <li class="{{ $request->segment(2) == 'table-report' ? 'active' : '' }}">
                        <a href="{{action('ReportController@getTableReport')}}"><i class="fa fa-table"></i>@lang('restaurant.table_report')</a>
                    </li>
                    @endcan @endif @endif @if($service_staff) @if(in_array('service_staff', $enabled_modules)) @can('sales_representative.view')
                    <li class="{{ $request->segment(2) == 'service-staff-report' ? 'active' : '' }}">
                        <a href="{{action('ReportController@getServiceStaffReport')}}"><i class="fa fa-user-secret"></i>@lang('restaurant.service_staff_report')</a>
                    </li>
                    @endcan @endif @endif
                </ul>
            </li>
            @endif @endif @if($catalogue_qr) @if(auth()->user()->can('catalogue.access'))
            <li class="treeview {{  in_array( $request->segment(1), ['backup']) ? 'active active-sub' : '' }}">
                <a href="{{action('\Modules\ProductCatalogue\Http\Controllers\ProductCatalogueController@generateQr')}}">
                    <i class="fa fa-qrcode"></i>
                    <span>@lang('lang_v1.catalogue_qr')</span>
                </a>
            </li>
            @endif @endif @if($backup_module) @can('backup')
            <li class="treeview {{  in_array( $request->segment(1), ['backup']) ? 'active active-sub' : '' }}">
                <a href="{{action('BackUpController@index')}}">
                    <i class="fa fa-dropbox"></i>
                    <span>@lang('lang_v1.backup')</span>
                </a>
            </li>
            @endcan @endif
            <!-- Call restaurant module if defined -->
            @if($enable_booking)
            <!-- check if module in subscription -->
            @if(in_array('booking', $enabled_modules)) @if(auth()->user()->can('crud_all_bookings') || auth()->user()->can('crud_own_bookings') )
            <li class="treeview {{ $request->segment(1) == 'bookings'? 'active active-sub' : '' }}">
                <a href="{{action('Restaurant\BookingController@index')}}"><i class="fa fa-calendar-check-o"></i> <span>@lang('restaurant.bookings')</span></a>
            </li>
            @endif @endif @endif @if($kitchen) @if(in_array('kitchen', $enabled_modules))
            <li class="treeview {{ $request->segment(1) == 'modules' && $request->segment(2) == 'kitchen' ? 'active active-sub' : '' }}">
                <a href="{{action('Restaurant\KitchenController@index')}}"><i class="fa fa-fire"></i> <span>@lang('restaurant.kitchen')</span></a>
            </li>
            @endif @endif @if($orders) @if(in_array('service_staff', $enabled_modules))
            <li class="treeview {{ $request->segment(1) == 'modules' && $request->segment(2) == 'orders' ? 'active active-sub' : '' }}">
                <a href="{{action('Restaurant\OrderController@index')}}"><i class="fa fa-list-alt"></i> <span>@lang('restaurant.orders')</span></a>
            </li>
            @endif @endif @if($notification_template_module) @can('send_notifications')
            <li class="treeview {{  $request->segment(1) == 'notification-templates' ? 'active active-sub' : '' }}">
                <a href="">
                    <i class="fa fa-envelope"></i> <span>@lang('lang_v1.notification_templates')</span>
                    <span class="pull-right-container">
                        <i class="fa fa-angle-left pull-right"></i>
                    </span>
                </a>
                <ul class="treeview-menu">
                    @if($enable_sms) @can('sms.view')
                    <li class="{{ $request->segment(2) == 'sms-template' ? 'active' : '' }}">
                        <a href="{{ url('notification-templates/sms-template')}}"><i class="fa fa-commenting-o"></i> @lang('lang_v1.sms_template')</a>
                    </li>
                    @endcan @endif
                    <li class="{{ $request->segment(2) == 'email-template' ? 'active' : '' }}">
                        <a href="{{ url('notification-templates/email-template')}}"><i class="fa fa-envelope-o"></i> @lang('lang_v1.email_template')</a>
                    </li>
                </ul>
            </li>
            @endif @endrole @php $business_or_entity = App\System::getProperty('business_or_entity'); @endphp @if(!$disable_all_other_module_vr) @if(!auth()->user()->is_pump_operator)
            <li
                class="treeview @if( in_array($request->segment(1), ['pay-online', 'stores', 'business', 'tax-rates', 'barcodes', 'invoice-schemes', 'business-location', 'invoice-layouts', 'printers', 'subscription', 'types-of-service']) || in_array($request->segment(2), ['tables', 'modifiers']) ) {{'active active-sub'}} @endif"
            >
                <a href="#" id="tour_step2_menu">
                    <i class="fa fa-cog"></i> <span>@lang('business.settings')</span>
                    <span class="pull-right-container">
                        <i class="fa fa-angle-left pull-right"></i>
                    </span>
                </a>
                <ul class="treeview-menu" id="tour_step3">
                    @if($settings_module) @can('business_settings.access') @if($business_settings)
                    <li class="{{ $request->segment(1) == 'business' ? 'active' : '' }}">
                        <a href="{{action('BusinessController@getBusinessSettings')}}" id="tour_step2">
                            <i class="fa fa-cogs"></i> @if($business_or_entity == 'business'){{ __('business.business_settings') }} @elseif($business_or_entity == 'entity'){{ __('lang_v1.entity_settings') }} @else {{
                            __('business.business_settings') }} @endif
                        </a>
                    </li>
                    @endif @if($business_location)
                    <li class="{{ $request->segment(1) == 'business-location' ? 'active' : '' }}">
                        <a href="{{action('BusinessLocationController@index')}}">
                            <i class="fa fa-map-marker"></i> @if($business_or_entity == 'business'){{ __('business.business_locations') }} @elseif($business_or_entity == 'entity'){{ __('lang_v1.entity_locations') }} @else {{
                            __('business.business_locations') }} @endif
                        </a>
                    </li>
                    @endif @if(!$property_module)
                    <li class="{{ $request->segment(1) == 'stores' ? 'active' : '' }}">
                        <a href="{{action('StoreController@index')}}"><i class="fa fa-stack-exchange"></i> @lang('business.stores_settings')</a>
                    </li>
                    @endif @endcan @can('invoice_settings.access') @if($invoice_settings)
                    <li class="@if( in_array($request->segment(1), ['invoice-schemes', 'invoice-layouts']) ) {{'active'}} @endif">
                        <a href="{{action('InvoiceSchemeController@index')}}"><i class="fa fa-file"></i> <span>@lang('invoice.invoice_settings')</span></a>
                    </li>
                    @endif @endcan @if(!$property_module) @can('barcode_settings.access')
                    <li class="{{ $request->segment(1) == 'barcodes' ? 'active' : '' }}">
                        <a href="{{action('BarcodeController@index')}}"><i class="fa fa-barcode"></i> <span>@lang('barcode.barcode_settings')</span></a>
                    </li>
                    @endcan
                    <li class="{{ $request->segment(1) == 'printers' ? 'active' : '' }}">
                        <a href="{{action('PrinterController@index')}}"><i class="fa fa-share-alt"></i> <span>@lang('printer.receipt_printers')</span></a>
                    </li>
                    @endif @if(auth()->user()->can('tax_rate.view') || auth()->user()->can('tax_rate.create')) @if($tax_rates)
                    <li class="{{ $request->segment(1) == 'tax-rates' ? 'active' : '' }}">
                        <a href="{{action('TaxRateController@index')}}"><i class="fa fa-bolt"></i> <span>@lang('tax_rate.tax_rates')</span></a>
                    </li>
                    @endif @endif @if(!$property_module) @if($customer_settings) @if(auth()->user()->can('customer_settings.access'))
                    <li class="{{ $request->segment(1) == 'customer-settings' ? 'active' : '' }}">
                        <a href="{{action('CustomerSettingsController@index')}}"><i class="fa fa-bolt"></i> <span>@lang('lang_v1.customer_settings')</span></a>
                    </li>
                    @endif @endif @if(in_array('tables', $enabled_modules)) @can('business_settings.access')
                    <li class="{{ $request->segment(1) == 'modules' && $request->segment(2) == 'tables' ? 'active' : '' }}">
                        <a href="{{action('Restaurant\TableController@index')}}"><i class="fa fa-table"></i> @lang('restaurant.tables')</a>
                    </li>
                    @endcan @endif @if($expenses) @if(in_array('modifiers', $enabled_modules)) @if(auth()->user()->can('product.view') || auth()->user()->can('product.create') )
                    <li class="{{ $request->segment(1) == 'modules' && $request->segment(2) == 'modifiers' ? 'active' : '' }}">
                        <a href="{{action('Restaurant\ModifierSetsController@index')}}"><i class="fa fa-delicious"></i> @lang('restaurant.modifiers')</a>
                    </li>
                    @endif @endif @endif @endif @if(in_array('type_of_service', $enabled_modules) && !$property_module)
                    <li class="{{  $request->segment(1) == 'types-of-service' ? 'active active-sub' : '' }}">
                        <a href="{{action('TypesOfServiceController@index')}}">
                            <i class="fa fa-user-circle-o"></i>
                            <span>@lang('lang_v1.types_of_service')</span>
                        </a>
                    </li>
                    @endif @endif @if(Module::has('Superadmin')) @includeIf('superadmin::layouts.partials.subscription') @endif
                    <li class="{{ $request->segment(1) == 'pay-online' && $request->segment(2) == 'create' ? 'active active-sub' : '' }}">
                        <a href="{{action('\Modules\Superadmin\Http\Controllers\PayOnlineController@create')}}">
                            <i class="fa fa-money"></i>
                            <span class="title">
                                @lang('superadmin::lang.pay_online')
                            </span>
                        </a>
                    </li>
                </ul>
            </li>
            @endif @endif @if($sms_module) @can('sms.access') @includeIf('sms::layouts.partials.sidebar') @endcan @endif @if($member_registration) @can('member.access') @includeIf('member::layouts.partials.sidebar') @endcan @endif
            @if(auth()->user()->hasRole('Super Manager#1'))
            <li class="treeview {{ in_array($request->segment(1), ['super-manager']) ? 'active active-sub' : '' }}">
                <a href="#">
                    <i class="fa fa-user-secret"></i>
                    <span class="title">@lang('lang_v1.super_manager')</span>
                    <span class="pull-right-container">
                        <i class="fa fa-angle-left pull-right"></i>
                    </span>
                </a>
                <ul class="treeview-menu">
                    <li class="{{ $request->segment(2) == 'visitors' ? 'active active-sub' : '' }}">
                        <a href="{{action('SuperManagerVisitorController@index')}}">
                            <i class="fa fa-users"></i>
                            <span class="title">
                                @lang('lang_v1.all_visitor_details')
                            </span>
                        </a>
                    </li>
                </ul>
            </li>
            @endif @if($visitors_registration_module) @includeIf('visitor::layouts.partials.sidebar') @endif @if($user_management_module) @if(auth()->user()->can('user.view') || auth()->user()->can('user.create') ||
            auth()->user()->can('roles.view'))
            <li class="treeview {{ in_array($request->segment(1), ['roles', 'users', 'sales-commission-agents']) ? 'active active-sub' : '' }}">
                <a href="#">
                    <i class="fa fa-users"></i>
                    <span class="title">@lang('user.user_management')</span>
                    <span class="pull-right-container">
                        <i class="fa fa-angle-left pull-right"></i>
                    </span>
                </a>
                <ul class="treeview-menu">
                    @can( 'user.view' )
                    <li class="{{ $request->segment(1) == 'users' ? 'active active-sub' : '' }}">
                        <a href="{{action('ManageUserController@index')}}">
                            <i class="fa fa-user"></i>
                            <span class="title">
                                @lang('user.users')
                            </span>
                        </a>
                    </li>
                    @endcan @can('roles.view')
                    <li class="{{ $request->segment(1) == 'roles' ? 'active active-sub' : '' }}">
                        <a href="{{action('RoleController@index')}}">
                            <i class="fa fa-briefcase"></i>
                            <span class="title">
                                @lang('user.roles')
                            </span>
                        </a>
                    </li>
                    @endcan @if($enable_sale_cmsn_agent == 1) @can('user.create')
                    <li class="{{ $request->segment(1) == 'sales-commission-agents' ? 'active active-sub' : '' }}">
                        <a href="{{action('SalesCommissionAgentController@index')}}">
                            <i class="fa fa-handshake-o"></i>
                            <span class="title">
                                @lang('lang_v1.sales_commission_agents')
                            </span>
                        </a>
                    </li>
                    @endcan @endif
                </ul>
            </li>
            @endif @endif
            <!-- call Project module if defined -->
            @if(Module::has('Project')) @includeIf('project::layouts.partials.sidebar') @endif
            <!-- call Essentials module if defined -->
            @if(Module::has('Essentials')) @includeIf('essentials::layouts.partials.sidebar_hrm') @includeIf('essentials::layouts.partials.sidebar') @endif @if(Module::has('Woocommerce')) @includeIf('woocommerce::layouts.partials.sidebar')
            @endif
            <!-- only customer accessable pages -->
            @if(auth()->user()->is_customer == 1)
            <li class="treeview {{  in_array( $request->segment(1), ['customer-sales', 'customer-sell-return', 'customer-order', 'customer-order-list']) ? 'active active-sub' : '' }}" id="">
                <a href="#" id="">
                    <i class="fa fa-arrow-circle-up"></i> <span>@lang('sale.sale')</span>
                    <span class="pull-right-container">
                        <i class="fa fa-angle-left pull-right"></i>
                    </span>
                </a>
                <ul class="treeview-menu">
                    <li class="{{ $request->segment(1) == 'customer-sales' ? 'active' : '' }}">
                        <a href="{{action('CustomerSellController@index')}}"><i class="fa fa-list"></i>@lang('lang_v1.all_sales')</a>
                    </li>
                    <li class="{{ $request->segment(1) == 'customer-sell-return'  ? 'active' : '' }}">
                        <a href="{{action('CustomerSellReturnController@index')}}"><i class="fa fa-undo"></i>@lang('lang_v1.list_sell_return')</a>
                    </li>
                    <li class="{{ $request->segment(1) == 'customer-order' ? 'active' : '' }}">
                        <a href="{{action('CustomerOrderController@create')}}"><i class="fa fa-bullseye"></i>@lang('lang_v1.order')</a>
                    </li>
                    <li class="{{ $request->segment(1) == 'customer-order-list' ? 'active' : '' }}">
                        <a href="{{action('CustomerOrderController@getOrders')}}"><i class="fa fa-list-ol"></i>@lang('lang_v1.list_order')</a>
                    </li>
                </ul>
            </li>
            @endif
            <!-- end only customer accessable pages -->
            @if($enable_cheque_writing == 1) @if(auth()->user()->can('enable_cheque_writing'))
            <!--  Cheque Writing Module pages -->
            <li class="treeview {{  in_array( $request->segment(1), ['cheque-templates', 'cheque-write', 'stamps', 'cheque-numbers']) ? 'active active-sub' : '' }}" id="" style="background: brown;">
                <a href="#" id="">
                    <i class="fa fa-book"></i> <span>@lang('cheque.cheque_writing_module')</span>
                    <span class="pull-right-container">
                        <i class="fa fa-angle-left pull-right"></i>
                    </span>
                </a>
                <ul class="treeview-menu">
                    <li class="{{ $request->segment(1) == 'cheque-templates'  && $request->segment(2) == '' ? 'active' : '' }}">
                        <a href="{{action('Chequer\ChequeTemplateController@index')}}"><i class="fa fa-book"></i>@lang('cheque.templates')</a>
                    </li>
                    <li class="{{ $request->segment(1) == 'cheque-templates' && $request->segment(2) == 'create' ? 'active' : '' }}">
                        <a href="{{action('Chequer\ChequeTemplateController@create')}}"><i class="fa fa-plus"></i>@lang('cheque.add_new_templates')</a>
                    </li>
                    <li class="{{ $request->segment(1) == 'cheque-write' && $request->segment(2) == 'create' ? 'active' : '' }}">
                        <a href="{{action('Chequer\ChequeWriteController@create')}}"><i class="fa fa-pencil-square-o"></i>@lang('cheque.write_cheque')</a>
                    </li>
                    <li class="{{ $request->segment(1) == 'stamps' && $request->segment(2) == '' ? 'active' : '' }}">
                        <a href="{{action('Chequer\ChequerStampController@index')}}"><i class="fa fa-gavel"></i>@lang('cheque.manage_stamps')</a>
                    </li>
                    <li class="{{ $request->segment(1) == 'cheque-numbers' && $request->segment(2) == '' ? 'active' : '' }}">
                        <a href="{{action('Chequer\ChequeNumberController@index')}}"><i class="fa fa-list-ol"></i>@lang('cheque.cheque_number_list')</a>
                    </li>
                </ul>
            </li>
            @endif @endif
        </ul>
        @endif
        <!-- /.sidebar-menu -->
    </section>
    <!-- /.sidebar -->
</aside>

