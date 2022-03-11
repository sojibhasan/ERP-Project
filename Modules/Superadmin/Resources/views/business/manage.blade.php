@extends('layouts.app')
@section('title', __('superadmin::lang.superadmin') . ' | Business')

@section('content')

    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>@lang( 'superadmin::lang.individual_company_permissions' )
        </h1>
    </section>
    <!-- Main content -->
    <section class="content">
        <div class="box">
            <div class="box-body">
                @can('superadmin')
                    <h3> @lang('superadmin::lang.business_name'): {{$business->name}}</h3>
                    {!! Form::open(['url' => action('\Modules\Superadmin\Http\Controllers\BusinessController@saveManage',
                    $business->id), 'method' => 'post', 'id' => 'custom_permission_form', 'enctype' => 'multipart/form-data'])
                    !!}
                    <div class="col-md-12">
                        <div class="row">
                            <div class="col-md-2">
                                <div class="from-group">
                                    {!! Form::label('annual_fee_package', __('superadmin::lang.annual_fee_package'), ['class' =>
                                    'search_label']) !!}
                                    {!! Form::text('annual_fee_package', !empty($package_manage) ?
                                    number_format($package_manage->price, 2, '.', '') : null, ['class' => 'form-control', 'id'
                                    =>
                                    'annual_fee_package', 'placeholder' => __('superadmin::lang.annual_fee_package')]) !!}
                                </div>
                            </div>
                            <input type="hidden" name="package_manage_id"
                                   value="{{!empty($package_manage) ? $package_manage->id : null}}">
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label for="currency_id" class="search_label">Currency:</label>
                                    <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fa fa-money"></i>
                                </span>
                                        {!! Form::select('currency_id', $currencies, !empty($package_manage) ?
                                        $package_manage->currency_id : null, ['class' => 'form-control
                                        select2','placeholder' => __('business.currency_placeholder'), 'required', 'id' =>
                                        'currency_id_manage']); !!}
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label for="search_settings">@lang('superadmin::lang.search_permissions'):</label>
                                    @include('superadmin::layouts.partials.search_settings')
                                </div>
                            </div>
                            <div class="col-md-2">
                                <label for="search_settings">Individual Package:</label>
                                <select name="individual_package" id="individual_package" class="form-control ">
                                    <option value="">Please Select</option>
                                    <option value="yes">Yes</option>
                                    <option value="No">No</option>
                                </select>
                            </div>

                            <div class="col-sm-3">
                                <div class="checkbox">
                                    <label>
                                        {!! Form::checkbox('day_end_enable', 1, $business->day_end_enable , [ 'class' => 'input-icheck','id' =>	'day_end_enable']); !!} {{ __( 'superadmin::lang.day_end' ) }} </label>
                                    </label>
                                </div>
                            </div>
                        </div>
                        </br>
                        <div class="row">
                            <div class="form-group">
                                <div class="checkbox">
                                    {!! Form::checkbox('select_all', 1, false, ['class' => 'input-icheck select_all',])
                                    !!}{{__('superadmin::lang.select_all')}}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="col-sm-3 product_count">
                                <div class="form-group">
                                    {!! Form::label('product_count', __('superadmin::lang.product_count').':') !!}
                                    {!! Form::number('product_count', !empty($package_manage) ? $package_manage->product_count :
                                    $previous_package_data['product_count'], ['class' => 'form-control', 'required', 'min' =>
                                    0]); !!}

                                    <span class="help-block">
                                @lang('superadmin::lang.infinite_help')
                            </span>
                                </div>
                            </div>
                            <div class="col-sm-3 product_count">
                                <div class="form-group">
                                    {!! Form::label('location_count', __('superadmin::lang.location_count').':') !!}
                                    {!! Form::number('location_count', !empty($package_manage) ? $package_manage->location_count
                                    : $previous_package_data['location_count'], ['class' => 'form-control', 'required', 'min' =>
                                    0]); !!}

                                    <span class="help-block">
                                @lang('superadmin::lang.infinite_help')
                            </span>
                                </div>
                            </div>
                            <div class="col-sm-3 product_count">
                                <div class="form-group">
                                    {!! Form::label('vehicle_count', __('superadmin::lang.no_of_vehicles').':') !!}
                                    {!! Form::number('vehicle_count', !empty($package_manage) ? $package_manage->vehicle_count
                                    : $previous_package_data['vehicle_count'], ['class' => 'form-control', 'required', 'min' =>
                                    0]); !!}

                                    <span class="help-block">
                                @lang('superadmin::lang.infinite_help')
                            </span>
                                </div>
                            </div>
                            <div class="clearfix"></div>
                            <div class="row">
                                <div class="col-md-6">

                                </div>
                                <div class="col-md-3">

                                </div>
                                <div class="col-md-3  text-center">
                                    <h5><b>@lang('superadmin::lang.option_value')</b></h5>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <label class="search_label">@lang('superadmin::lang.manufacturing_module')</label>
                                </div>
                                <div class="col-md-3">
                                    {!! Form::checkbox('mf_module', 1, !empty($manage_module_enable['mf_module']) ? true : false,
                                    ['class' => 'input-icheck ch_select mf_module']) !!}
                                </div>
                                <div class="col-md-3">
                                    {!! Form::text('mf_module_value', !empty($module_enable_price->mf_module) ?
                                    $module_enable_price->mf_module : null, ['class' => 'form-control']) !!}
                                </div>
                            </div>
                            <div class="row mf_module_locations check_group">
                                <div class="col-md-3">
                                    <p style="padding-top: 9px;"><b>@lang('superadmin::lang.select_locations'): </b></p>
                                </div>
                                <div class="col-md-8">
                                    <div class="checkbox">
                                        <label>
                                            <input type="checkbox" class="check_all input-icheck"> {{ __( 'role.select_all' ) }}
                                        </label>
                                    </div>
                                </div>
                                <br>
                                @foreach ($business_locations as $location)
                                    <div class="col-md-3">
                                        <div class="checkbox">
                                            <label>
                                                {!! Form::checkbox('module_permission_location[mf_module]['.$location->id.']', 1,
                                                !empty($module_permission_locations_value['mf_module']->locations) ?
                                                array_key_exists($location->id,
                                                $module_permission_locations_value['mf_module']->locations) : false, ['class' =>
                                                'input-icheck']); !!}
                                                {{$location->name}}
                                            </label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <br>
                            <hr>
                            <div class="row">
                                <div class="col-md-6">
                                    <label class="search_label">@lang('superadmin::lang.access_account')</label>
                                </div>
                                <div class="col-md-3">
                                    {!! Form::checkbox('access_account', 1, !empty($manage_module_enable['access_account']) ? true
                                    :
                                    false, ['class' => 'input-icheck ch_select accounting_module'])
                                    !!}
                                </div>
                                <div class="col-md-3">
                                    {!! Form::text('access_account_value', !empty($module_enable_price->access_account) ?
                                    $module_enable_price->access_account : null, ['class' => 'form-control']) !!}
                                </div>
                            </div>
                            <br>
                            <div class="row">
                                <div class="col-md-6">
                                    {!! Form::checkbox('zero_previous_accounting_values', 1, false, ['class' => 'input-icheck
                                    accounting_module'])
                                    !!} <label
                                            class="search_label">@lang('superadmin::lang.zero_previous_accounting_values')</label>
                                </div>
                            </div>
                            <br>
                            <div class="row accounting_module_locations check_group">
                                <div class="col-md-3">
                                    <p style="padding-top: 9px;"><b>@lang('superadmin::lang.select_locations'): </b></p>
                                </div>
                                <div class="col-md-8">
                                    <div class="checkbox">
                                        <label>
                                            <input type="checkbox" class="check_all input-icheck"> {{ __( 'role.select_all' ) }}
                                        </label>
                                    </div>
                                </div>
                                <br>
                                @foreach ($business_locations as $location)
                                    <div class="col-md-3">
                                        <div class="checkbox">
                                            <label>
                                                {!!
                                                Form::checkbox('module_permission_location[accounting_module]['.$location->id.']',
                                                1,
                                                !empty($module_permission_locations_value['accounting_module']->locations) ?
                                                array_key_exists($location->id,
                                                $module_permission_locations_value['accounting_module']->locations) : false,
                                                ['class' => 'input-icheck']); !!}
                                                {{$location->name}}
                                            </label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <div class="col-md-12" style="margin-bottom: 20px">
                                <!--Accordion wrapper-->
                                <div class="accordion md-accordion" id="accordionEx1" role="tablist"
                                     aria-multiselectable="true">

                                    <div class="card">
                                        <div class="card-header" role="tab" id="headingTwo1">
                                            <a class="collapsed" data-toggle="collapse" data-parent="#accordionEx1"
                                               href="#collapseTwo1" aria-expanded="false" aria-controls="collapseTwo1">
                                                <h5 class="mb-0 text-black">
                                                    <label
                                                            class="search_label">@lang('superadmin::lang.manage_accounts')</label>
                                                    <i class="fa fa-angle-down rotate-icon pull-right"></i>
                                                </h5>
                                            </a>
                                        </div>
                                        <div id="collapseTwo1" class="collapse" role="tabpanel" aria-labelledby="headingTwo1"
                                             data-parent="#accordionEx1">
                                            <div class="card-body" style="margin-bottom: 10px;">
                                                <hr>
                                                @foreach ($accounts as $account)
                                                    <div class="col-md-6">
                                                        <div class="checkbox">
                                                            <label>
                                                                {!!
                                                                Form::checkbox('accounts_enabled['.$account->id.']', 1,
                                                                $account->visible,['class' => 'input-icheck']); !!}
                                                                {{$account->name}}
                                                            </label>
                                                        </div>
                                                    </div>
                                                @endforeach
                                                <hr>
                                            </div>
                                        </div>

                                    </div>

                                </div>
                                <hr>
                                <!-- Accordion wrapper -->
                            </div>
                            <hr>
                            <br>
                            <div class="row">
                                <div class="col-md-6">
                                    <label class="search_label">@lang('superadmin::lang.access_sms_settings')</label>
                                </div>
                                <div class="col-md-3">
                                    {!! Form::checkbox('access_sms_settings', 1,
                                    !empty($manage_module_enable['access_sms_settings'])
                                    ? true : false, ['class' => 'input-icheck
                                    ch_select', 'id' => 'sms_settings_checkbox']) !!}
                                </div>
                                <div class="col-md-3">
                                    {!! Form::text('access_sms_settings_value',
                                    !empty($module_enable_price->access_sms_settings) ?
                                    $module_enable_price->access_sms_settings : null, ['class' => 'form-control']) !!}
                                </div>
                            </div>
                            <br>
                            <hr>
                            <div class="row">
                                <div class="col-md-6">
                                    <label class="search_label"> @lang('superadmin::lang.access_module')</label>
                                </div>
                                <div class="col-md-3">
                                    {!! Form::checkbox('access_module', 1, !empty($manage_module_enable['access_module']) ? true :
                                    false, ['class' => 'input-icheck ch_select'])
                                    !!}
                                </div>
                                <div class="col-md-3">
                                    {!! Form::text('access_module_value', !empty($module_enable_price->access_module) ?
                                    $module_enable_price->access_module : null, ['class' => 'form-control']) !!}
                                </div>
                            </div>
                            <br>
                            <hr>
                            <div class="row">
                                <div class="col-md-6">
                                    <label class="search_label"> @lang('superadmin::lang.hospital_system')</label>
                                </div>
                                <div class="col-md-3">
                                    {!! Form::checkbox('hospital_system', 1, !empty($manage_module_enable['hospital_system']) ?
                                    true :
                                    false, ['class' => 'input-icheck ch_select'])
                                    !!}
                                </div>
                                <div class="col-md-3">
                                    {!! Form::text('hospital_system_value', !empty($module_enable_price->hospital_system) ?
                                    $module_enable_price->hospital_system : null, ['class' => 'form-control']) !!}
                                </div>
                            </div>
                            <br>
                            <hr>
                            <div class="row">
                                <div class="col-md-6">
                                    <label class="search_label">@lang('superadmin::lang.enable_restaurant')</label>
                                </div>
                                <div class="col-md-3">
                                    {!! Form::checkbox('enable_restaurant', 1, !empty($manage_module_enable['enable_restaurant'])
                                    ?
                                    true : false, ['class' => 'input-icheck
                                    ch_select restaurant_module']) !!}
                                </div>
                                <div class="col-md-3">
                                    {!! Form::text('enable_restaurant_value', !empty($module_enable_price->enable_restaurant) ?
                                    $module_enable_price->enable_restaurant : null, ['class' => 'form-control']) !!}
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-3">
                                    {!! Form::checkbox('orders', 1,
                                    !empty($manage_module_enable['orders']) ? true : false, ['class' =>
                                    'input-icheck
                                    ch_select']) !!}<label class="search_label">@lang('superadmin::lang.orders')</label>
                                </div>
                            </div>
                            <div class="row restaurant_module_locations check_group">
                                <div class="col-md-3">
                                    <p style="padding-top: 9px;"><b>@lang('superadmin::lang.select_locations'): </b></p>
                                </div>
                                <div class="col-md-8">
                                    <div class="checkbox">
                                        <label>
                                            <input type="checkbox" class="check_all input-icheck"> {{ __( 'role.select_all' ) }}
                                        </label>
                                    </div>
                                </div>
                                <br>
                                @foreach ($business_locations as $location)
                                    <div class="col-md-3">
                                        <div class="checkbox">
                                            <label>
                                                {!!
                                                Form::checkbox('module_permission_location[restaurant_module]['.$location->id.']',
                                                1,
                                                !empty($module_permission_locations_value['restaurant_module']->locations) ?
                                                array_key_exists($location->id,
                                                $module_permission_locations_value['restaurant_module']->locations) : false,
                                                ['class' => 'input-icheck']); !!}
                                                {{$location->name}}
                                            </label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <hr>
                            <br>
                            <div class="row">
                                <div class="col-md-6">
                                    <label class="search_label">@lang('superadmin::lang.enable_booking')</label>
                                </div>
                                <div class="col-md-3">
                                    {!! Form::checkbox('enable_booking', 1, !empty($manage_module_enable['enable_booking']) ? true
                                    :
                                    false, ['class' => 'input-icheck ch_select'])
                                    !!}
                                </div>
                                <div class="col-md-3">
                                    {!! Form::text('enable_booking_value', !empty($module_enable_price->enable_booking) ?
                                    $module_enable_price->enable_booking : null, ['class' => 'form-control']) !!}
                                </div>
                            </div>
                            <br>
                            <hr>
                            <div class="row">
                                <div class="col-md-6">
                                    <label class="search_label">@lang('superadmin::lang.enable_crm')</label>
                                </div>
                                <div class="col-md-3">
                                    {!! Form::checkbox('enable_crm', 1, !empty($manage_module_enable['enable_crm']) ? true :
                                    false,
                                    ['class' => 'input-icheck ch_select']) !!}
                                </div>
                                <div class="col-md-3">
                                    {!! Form::text('enable_crm_value', !empty($module_enable_price->enable_crm) ?
                                    $module_enable_price->enable_crm : null, ['class' => 'form-control']) !!}
                                </div>
                            </div>
                            <br>
                            <hr>
                            <div class="check_group">
                                <div class="row">
                                    <div class="col-md-3">
                                        <label class="search_label">@lang('superadmin::lang.hr_module')</label>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="checkbox">
                                            <label>
                                                <input type="checkbox" class="check_all input-icheck">
                                                {{ __( 'role.select_all' ) }}
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        {!! Form::checkbox('hr_module', 1, !empty($manage_module_enable['hr_module']) ? true :
                                        false,
                                        ['class' => 'input-icheck ch_select hr_module']) !!}
                                    </div>
                                    <div class="col-md-3">
                                        {!! Form::text('hr_module_value', !empty($module_enable_price->hr_module) ?
                                        $module_enable_price->hr_module : null, ['class' => 'form-control']) !!}
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="check_group">
                                        <div class="col-md-3">
                                            <label>@lang('superadmin::lang.employee'):</label>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="checkbox">
                                                <label>
                                                    <input type="checkbox" class="check_all input-icheck">
                                                    {{ __( 'role.select_all' ) }}
                                                </label>
                                            </div>
                                        </div>
                                        <div class="clearfix"></div>
                                        <div class="col-md-4">
                                            {!! Form::checkbox('employee', 1,
                                            !empty($manage_module_enable['employee']) ? true : false, ['class' =>
                                            'input-icheck
                                            ch_select']) !!}<label
                                                    class="search_label">@lang('superadmin::lang.employee')</label>
                                        </div>
                                        <div class="col-md-4">
                                            {!! Form::checkbox('teminated', 1,
                                            !empty($manage_module_enable['teminated']) ? true : false, ['class' =>
                                            'input-icheck
                                            ch_select']) !!}<label
                                                    class="search_label">@lang('superadmin::lang.teminated')</label>
                                        </div>
                                        <div class="col-md-4">
                                            {!! Form::checkbox('award', 1,
                                            !empty($manage_module_enable['award']) ? true : false, ['class' =>
                                            'input-icheck
                                            ch_select']) !!}<label class="search_label">@lang('superadmin::lang.award')</label>
                                        </div>
                                        <div class="col-md-4">
                                            {!! Form::checkbox('leave_request', 1,
                                            !empty($manage_module_enable['leave_request']) ? true : false, ['class' =>
                                            'input-icheck
                                            ch_select']) !!}<label
                                                    class="search_label">@lang('superadmin::lang.leave_request')</label>
                                        </div>
                                    </div>
                                    <br>
                                    <br>
                                    <div class="clearfix"></div>
                                    <div class="check_group">
                                        <div class="col-md-3">
                                            <label style="margin-top: 8px;">@lang('superadmin::lang.attendance'):</label>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="checkbox">
                                                <label>
                                                    <input type="checkbox" class="check_all input-icheck">
                                                    {{ __( 'role.select_all' ) }}
                                                </label>
                                            </div>
                                        </div>
                                        <div class="clearfix"></div>
                                        <div class="col-md-4">
                                            {!! Form::checkbox('attendance', 1,
                                            !empty($manage_module_enable['attendance']) ? true : false, ['class' =>
                                            'input-icheck
                                            ch_select']) !!}<label
                                                    class="search_label">@lang('superadmin::lang.attendance')</label>
                                        </div>
                                        <div class="col-md-4">
                                            {!! Form::checkbox('import_attendance', 1,
                                            !empty($manage_module_enable['import_attendance']) ? true : false, ['class' =>
                                            'input-icheck
                                            ch_select']) !!}<label
                                                    class="search_label">@lang('superadmin::lang.import_attendance')</label>
                                        </div>
                                        <div class="col-md-4">
                                            {!! Form::checkbox('late_and_over_time', 1,
                                            !empty($manage_module_enable['late_and_over_time']) ? true : false, ['class' =>
                                            'input-icheck
                                            ch_select']) !!}<label
                                                    class="search_label">@lang('superadmin::lang.late_and_over_time')</label>
                                        </div>
                                    </div>
                                    <div class="check_group">
                                        <div class="col-md-3">
                                            <label style="margin-top: 8px;">@lang('superadmin::lang.payroll'):</label>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="checkbox">
                                                <label>
                                                    <input type="checkbox" class="check_all input-icheck">
                                                    {{ __( 'role.select_all' ) }}
                                                </label>
                                            </div>
                                        </div>
                                        <div class="clearfix"></div>
                                        <div class="col-md-4">
                                            {!! Form::checkbox('payroll', 1,
                                            !empty($manage_module_enable['payroll']) ? true : false, ['class' =>
                                            'input-icheck
                                            ch_select']) !!}<label
                                                    class="search_label">@lang('superadmin::lang.payroll')</label>
                                        </div>
                                        <div class="col-md-4">
                                            {!! Form::checkbox('salary_details', 1,
                                            !empty($manage_module_enable['salary_details']) ? true : false, ['class' =>
                                            'input-icheck
                                            ch_select']) !!}<label
                                                    class="search_label">@lang('superadmin::lang.salary_details')</label>
                                        </div>
                                        <div class="col-md-4">
                                            {!! Form::checkbox('basic_salary', 1,
                                            !empty($manage_module_enable['basic_salary']) ? true : false, ['class' =>
                                            'input-icheck
                                            ch_select']) !!}<label
                                                    class="search_label">@lang('superadmin::lang.basic_salary')</label>
                                        </div>
                                        <div class="col-md-4">
                                            {!! Form::checkbox('payroll_payments', 1,
                                            !empty($manage_module_enable['payroll_payments']) ? true : false, ['class' =>
                                            'input-icheck
                                            ch_select']) !!}<label
                                                    class="search_label">@lang('superadmin::lang.payroll_payments')</label>
                                        </div>
                                    </div>
                                    <div class="clearfix"></div>
                                    <div class="check_group">
                                        <div class="col-md-3">
                                            <label style="margin-top: 8px;">@lang('superadmin::lang.reports'):</label>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="checkbox">
                                                <label>
                                                    <input type="checkbox" class="check_all input-icheck">
                                                    {{ __( 'role.select_all' ) }}
                                                </label>
                                            </div>
                                        </div>
                                        <div class="clearfix"></div>
                                        <div class="col-md-4">
                                            {!! Form::checkbox('hr_reports', 1,
                                            !empty($manage_module_enable['hr_reports']) ? true : false, ['class' =>
                                            'input-icheck
                                            ch_select']) !!}<label
                                                    class="search_label">@lang('superadmin::lang.hr_reports')</label>
                                        </div>
                                        <div class="col-md-4">
                                            {!! Form::checkbox('attendance_report', 1,
                                            !empty($manage_module_enable['attendance_report']) ? true : false, ['class' =>
                                            'input-icheck
                                            ch_select']) !!}<label
                                                    class="search_label">@lang('superadmin::lang.attendance_report')</label>
                                        </div>
                                        <div class="col-md-4">
                                            {!! Form::checkbox('employee_report', 1,
                                            !empty($manage_module_enable['employee_report']) ? true : false, ['class' =>
                                            'input-icheck
                                            ch_select']) !!}<label
                                                    class="search_label">@lang('superadmin::lang.employee_report')</label>
                                        </div>
                                        <div class="col-md-4">
                                            {!! Form::checkbox('payroll_report', 1,
                                            !empty($manage_module_enable['payroll_report']) ? true : false, ['class' =>
                                            'input-icheck
                                            ch_select']) !!}<label
                                                    class="search_label">@lang('superadmin::lang.payroll_report')</label>
                                        </div>
                                    </div>

                                    <div class="clearfix"></div>
                                    <br>
                                    <div class="col-md-4">
                                        {!! Form::checkbox('notice_board', 1,
                                        !empty($manage_module_enable['notice_board']) ? true : false, ['class' =>
                                        'input-icheck
                                        ch_select']) !!}<label
                                                class="search_label">@lang('superadmin::lang.notice_board')</label>
                                    </div>
                                    <div class="clearfix"></div>
                                    <br>
                                    <div class="check_group">
                                        <div class="col-md-3">
                                            <label style="margin-top: 8px;">@lang('superadmin::lang.settings'):</label>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="checkbox">
                                                <label>
                                                    <input type="checkbox" class="check_all input-icheck">
                                                    {{ __( 'role.select_all' ) }}
                                                </label>
                                            </div>
                                        </div>
                                        <div class="clearfix"></div>
                                        <div class="col-md-4">
                                            {!! Form::checkbox('hr_settings', 1,
                                            !empty($manage_module_enable['hr_settings']) ? true : false, ['class' =>
                                            'input-icheck
                                            ch_select']) !!}<label
                                                    class="search_label">@lang('superadmin::lang.hr_settings')</label>
                                        </div>
                                        <div class="col-md-4">
                                            {!! Form::checkbox('department', 1,
                                            !empty($manage_module_enable['department']) ? true : false, ['class' =>
                                            'input-icheck
                                            ch_select']) !!}<label
                                                    class="search_label">@lang('superadmin::lang.department')</label>
                                        </div>
                                        <div class="col-md-4">
                                            {!! Form::checkbox('jobtitle', 1,
                                            !empty($manage_module_enable['jobtitle']) ? true : false, ['class' =>
                                            'input-icheck
                                            ch_select']) !!}<label
                                                    class="search_label">@lang('superadmin::lang.jobtitle')</label>
                                        </div>
                                        <div class="col-md-4">
                                            {!! Form::checkbox('jobcategory', 1,
                                            !empty($manage_module_enable['jobcategory']) ? true : false, ['class' =>
                                            'input-icheck
                                            ch_select']) !!}<label
                                                    class="search_label">@lang('superadmin::lang.jobcategory')</label>
                                        </div>
                                        <div class="col-md-4">
                                            {!! Form::checkbox('workingdays', 1,
                                            !empty($manage_module_enable['workingdays']) ? true : false, ['class' =>
                                            'input-icheck
                                            ch_select']) !!}<label
                                                    class="search_label">@lang('superadmin::lang.workingdays')</label>
                                        </div>
                                        <div class="col-md-4">
                                            {!! Form::checkbox('workshift', 1,
                                            !empty($manage_module_enable['workshift']) ? true : false, ['class' =>
                                            'input-icheck
                                            ch_select']) !!}<label
                                                    class="search_label">@lang('superadmin::lang.workshift')</label>
                                        </div>
                                        <div class="col-md-4">
                                            {!! Form::checkbox('holidays', 1,
                                            !empty($manage_module_enable['holidays']) ? true : false, ['class' =>
                                            'input-icheck
                                            ch_select']) !!}<label
                                                    class="search_label">@lang('superadmin::lang.holidays')</label>
                                        </div>
                                        <div class="col-md-4">
                                            {!! Form::checkbox('leave_type', 1,
                                            !empty($manage_module_enable['leave_type']) ? true : false, ['class' =>
                                            'input-icheck
                                            ch_select']) !!}<label
                                                    class="search_label">@lang('superadmin::lang.leave_type')</label>
                                        </div>
                                        <div class="col-md-4">
                                            {!! Form::checkbox('salary_grade', 1,
                                            !empty($manage_module_enable['salary_grade']) ? true : false, ['class' =>
                                            'input-icheck
                                            ch_select']) !!}<label
                                                    class="search_label">@lang('superadmin::lang.salary_grade')</label>
                                        </div>
                                        <div class="col-md-4">
                                            {!! Form::checkbox('employment_status', 1,
                                            !empty($manage_module_enable['employment_status']) ? true : false, ['class' =>
                                            'input-icheck
                                            ch_select']) !!}<label
                                                    class="search_label">@lang('superadmin::lang.employment_status')</label>
                                        </div>
                                        <div class="col-md-4">
                                            {!! Form::checkbox('salary_component', 1,
                                            !empty($manage_module_enable['salary_component']) ? true : false, ['class' =>
                                            'input-icheck
                                            ch_select']) !!}<label
                                                    class="search_label">@lang('superadmin::lang.salary_component')</label>
                                        </div>
                                        <div class="col-md-4">
                                            {!! Form::checkbox('hr_prefix', 1,
                                            !empty($manage_module_enable['hr_prefix']) ? true : false, ['class' =>
                                            'input-icheck
                                            ch_select']) !!}<label
                                                    class="search_label">@lang('superadmin::lang.hr_prefix')</label>
                                        </div>
                                        <div class="col-md-4">
                                            {!! Form::checkbox('hr_tax', 1,
                                            !empty($manage_module_enable['hr_tax']) ? true : false, ['class' =>
                                            'input-icheck
                                            ch_select']) !!}<label class="search_label">@lang('superadmin::lang.hr_tax')</label>
                                        </div>
                                        <div class="col-md-4">
                                            {!! Form::checkbox('religion', 1,
                                            !empty($manage_module_enable['religion']) ? true : false, ['class' =>
                                            'input-icheck
                                            ch_select']) !!}<label
                                                    class="search_label">@lang('superadmin::lang.religion')</label>
                                        </div>
                                        <div class="col-md-4">
                                            {!! Form::checkbox('hr_setting_page', 1,
                                            !empty($manage_module_enable['hr_setting_page']) ? true : false, ['class' =>
                                            'input-icheck
                                            ch_select']) !!}<label
                                                    class="search_label">@lang('superadmin::lang.hr_setting_page')</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row hr_module_locations check_group">
                                <div class="col-md-3">
                                    <p style="padding-top: 9px;"><b>@lang('superadmin::lang.select_locations'): </b></p>
                                </div>
                                <div class="col-md-8">
                                    <div class="checkbox">
                                        <label>
                                            <input type="checkbox" class="check_all input-icheck"> {{ __( 'role.select_all' ) }}
                                        </label>
                                    </div>
                                </div>
                                <br>
                                @foreach ($business_locations as $location)
                                    <div class="col-md-3">
                                        <div class="checkbox">
                                            <label>
                                                {!! Form::checkbox('module_permission_location[hr_module]['.$location->id.']', 1,
                                                !empty($module_permission_locations_value['hr_module']->locations) ?
                                                array_key_exists($location->id,
                                                $module_permission_locations_value['hr_module']->locations) : false, ['class' =>
                                                'input-icheck']); !!}
                                                {{$location->name}}
                                            </label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <hr>
                            <div class="check_group">
                                <div class="row">
                                    <div class="col-md-3">
                                        <label
                                                class="search_label">@lang('superadmin::lang.visitors_registration_module')</label>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="checkbox">
                                            <label>
                                                <input type="checkbox" class="check_all input-icheck">
                                                {{ __( 'role.select_all' ) }}
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        {!! Form::checkbox('visitors_registration_module', 1,
                                        !empty($manage_module_enable['visitors_registration_module']) ? true :
                                        false,
                                        ['class' => 'input-icheck ch_select visitors_registration_module']) !!}
                                    </div>
                                    <div class="col-md-3">
                                        {!! Form::text('visitors_registration_module_value',
                                        !empty($module_enable_price->visitors_registration_module) ?
                                        $module_enable_price->visitors_registration_module : null, ['class' => 'form-control'])
                                        !!}
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4">
                                        {!! Form::checkbox('visitors', 1,
                                        !empty($manage_module_enable['visitors']) ? true : false, ['class' =>
                                        'input-icheck
                                        ch_select']) !!}<label class="search_label">@lang('superadmin::lang.visitors')</label>
                                    </div>
                                    <div class="col-md-4">
                                        {!! Form::checkbox('visitors_registration', 1,
                                        !empty($manage_module_enable['visitors_registration']) ? true : false, ['class' =>
                                        'input-icheck
                                        ch_select']) !!}<label
                                                class="search_label">@lang('superadmin::lang.visitors_registration')</label>
                                    </div>
                                    <div class="col-md-4">
                                        {!! Form::checkbox('visitors_registration_setting', 1,
                                        !empty($manage_module_enable['visitors_registration_setting']) ? true : false, ['class' =>
                                        'input-icheck
                                        ch_select']) !!}<label
                                                class="search_label">@lang('superadmin::lang.visitors_registration_setting')</label>
                                    </div>
                                    <div class="col-md-4">
                                        {!! Form::checkbox('visitors_district', 1,
                                        !empty($manage_module_enable['visitors_district']) ? true : false, ['class' =>
                                        'input-icheck
                                        ch_select']) !!}<label
                                                class="search_label">@lang('superadmin::lang.visitors_district')</label>
                                    </div>
                                    <div class="col-md-4">
                                        {!! Form::checkbox('visitors_town', 1,
                                        !empty($manage_module_enable['visitors_town']) ? true : false, ['class' =>
                                        'input-icheck
                                        ch_select']) !!}<label
                                                class="search_label">@lang('superadmin::lang.visitors_town')</label>
                                    </div>
                                    <div class="clearfix"></div>
                                    <div class="col-md-4">
                                        {!! Form::checkbox('disable_all_other_module_vr', 1,
                                        !empty($manage_module_enable['disable_all_other_module_vr']) ? true : false, ['class' =>
                                        'input-icheck
                                        ch_select']) !!}<label
                                                class="search_label">@lang('superadmin::lang.disable_all_other_module_vr')</label>
                                    </div>

                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-md-6">
                                    <label class="search_label">@lang('superadmin::lang.enable_duplicate_invoice')</label>
                                </div>
                                <div class="col-md-3">
                                    {!! Form::checkbox('enable_duplicate_invoice', 1,
                                    !empty($manage_module_enable['enable_duplicate_invoice']) ? true : false, ['class' =>
                                    'input-icheck
                                    ch_select']) !!}
                                </div>
                                <div class="col-md-3">
                                    {!! Form::text('enable_duplicate_invoice_value',
                                    !empty($module_enable_price->enable_duplicate_invoice) ?
                                    $module_enable_price->enable_duplicate_invoice : null, ['class' => 'form-control']) !!}
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-md-6">
                                    <label class="search_label">@lang('superadmin::lang.catalogue_qr')</label>
                                </div>
                                <div class="col-md-3">
                                    {!! Form::checkbox('catalogue_qr', 1,
                                    !empty($manage_module_enable['catalogue_qr']) ? true : false, ['class' =>
                                    'input-icheck
                                    ch_select']) !!}
                                </div>
                                <div class="col-md-3">
                                    {!! Form::text('catalogue_qr_value',
                                    !empty($module_enable_price->catalogue_qr) ?
                                    $module_enable_price->catalogue_qr : null, ['class' => 'form-control']) !!}
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-md-6">
                                    <label class="search_label">@lang('superadmin::lang.enable_sms')</label>
                                </div>
                                <div class="col-md-3">
                                    {!! Form::checkbox('enable_sms', 1, !empty($manage_module_enable['enable_sms']) ? true :
                                    false,
                                    ['class' => 'input-icheck ch_select']) !!}
                                </div>
                                <div class="col-md-3">
                                    {!! Form::text('enable_sms_value', !empty($module_enable_price->enable_sms) ?
                                    $module_enable_price->enable_sms : null, ['class' => 'form-control']) !!}
                                </div>
                            </div>
                            <br>
                            <hr>
                            <div class="row">
                                <div class="col-md-6">
                                    <label class="search_label">@lang('superadmin::lang.enable_sale_cmsn_agent')</label>
                                </div>
                                <div class="col-md-3">
                                    {!! Form::checkbox('enable_sale_cmsn_agent', 1,
                                    !empty($manage_module_enable['enable_sale_cmsn_agent']) ? true : false, ['class' =>
                                    'input-icheck
                                    ch_select']) !!}
                                </div>
                                <div class="col-md-3">
                                    {!! Form::text('enable_sale_cmsn_agent_value',
                                    !empty($module_enable_price->enable_sale_cmsn_agent) ?
                                    $module_enable_price->enable_sale_cmsn_agent : null, ['class' => 'form-control']) !!}
                                </div>
                            </div>
                            <br>
                            <hr>
                            <div class="row">
                                <div class="col-md-6">
                                    <label class="search_label">@lang('superadmin::lang.monthly_total_sales_volumn')</label>
                                </div>
                                <div class="col-md-3">
                                    {!! Form::checkbox('monthly_total_sales_volumn', 1,
                                    !empty($manage_module_enable['monthly_total_sales_volumn']) ? true : false, ['class' =>
                                    'input-icheck
                                    ch_select']) !!}
                                </div>
                                <div class="col-md-3">
                                    {!! Form::text('monthly_total_sales_volumn_value',
                                    !empty($module_enable_price->monthly_total_sales_volumn) ?
                                    $module_enable_price->monthly_total_sales_volumn : null, ['class' => 'form-control']) !!}
                                </div>
                            </div>
                            <br>
                            <hr>
                            <div class="row">
                                <div class="col-md-6">
                                    <label class="search_label">@lang('superadmin::lang.customer_order_own_customer')</label>
                                </div>
                                <div class="col-md-3">
                                    {!! Form::checkbox('customer_order_own_customer', 1,
                                    !empty($manage_module_enable['customer_order_own_customer']) ? true : false, ['class' =>
                                    'input-icheck
                                    ch_select']) !!}
                                </div>
                                <div class="col-md-3">
                                    {!! Form::text('customer_order_own_customer_value',
                                    !empty($module_enable_price->customer_order_own_customer) ?
                                    $module_enable_price->customer_order_own_customer : null, ['class' => 'form-control']) !!}
                                </div>
                            </div>
                            <br>
                            <hr>
                            <div class="row">
                                <div class="col-md-6">
                                    <label class="search_label">@lang('superadmin::lang.customer_settings')</label>
                                </div>
                                <div class="col-md-3">
                                    {!! Form::checkbox('customer_settings', 1,
                                    !empty($manage_module_enable['customer_settings']) ? true : false, ['class' =>
                                    'input-icheck
                                    ch_select']) !!}
                                </div>
                                <div class="col-md-3">
                                    {!! Form::text('customer_settings_value',
                                    !empty($module_enable_price->customer_settings) ?
                                    $module_enable_price->customer_settings : null, ['class' => 'form-control']) !!}
                                </div>
                            </div>
                            <br>
                            <hr>
                            <div class="row">
                                <div class="col-md-6">
                                    <label
                                            class="search_label">@lang('superadmin::lang.customer_order_general_customer')</label>
                                </div>
                                <div class="col-md-3">
                                    {!! Form::checkbox('customer_order_general_customer', 1,
                                    !empty($manage_module_enable['customer_order_general_customer']) ? true : false, ['class' =>
                                    'input-icheck
                                    ch_select']) !!}
                                </div>
                                <div class="col-md-3">
                                    {!! Form::text('customer_order_general_customer_value',
                                    !empty($module_enable_price->customer_order_general_customer) ?
                                    $module_enable_price->customer_order_general_customer : null, ['class' => 'form-control'])
                                    !!}
                                </div>
                            </div>
                            <br>
                            <hr>
                            <div class="row">
                                <div class="col-md-6">
                                    <label class="search_label">@lang('superadmin::lang.customer_to_directly_in_panel')</label>
                                </div>
                                <div class="col-md-3">
                                    {!! Form::checkbox('customer_to_directly_in_panel', 1,
                                    !empty($manage_module_enable['customer_to_directly_in_panel']) ? true : false, ['class' =>
                                    'input-icheck
                                    ch_select']) !!}
                                </div>
                                <div class="col-md-3">
                                    {!! Form::text('customer_to_directly_in_panel_value',
                                    !empty($module_enable_price->customer_to_directly_in_panel) ?
                                    $module_enable_price->customer_to_directly_in_panel : null, ['class' => 'form-control'])
                                    !!}
                                </div>
                            </div>
                            <br>
                            <hr>
                            <div class="row">
                                <div class="col-md-6">
                                    <label class="search_label">@lang('superadmin::lang.enable_petro_module')</label>
                                </div>
                                <div class="col-md-3">
                                    {!! Form::checkbox('enable_petro_module', 1,
                                    !empty($manage_module_enable['enable_petro_module']) ? true : false, ['class' =>
                                    'input-icheck
                                    ch_select']) !!}
                                </div>
                                <div class="col-md-3">
                                    {!! Form::text('enable_petro_module_value',
                                    !empty($module_enable_price->enable_petro_module) ?
                                    $module_enable_price->enable_petro_module : null, ['class' => 'form-control']) !!}
                                </div>
                            </div>
                            <br>
                            <div class="row">
                                <div class="col-md-4">
                                    {!! Form::checkbox('meter_resetting', 1,
                                    !empty($manage_module_enable['meter_resetting']) ? true : false, ['class' =>
                                    'input-icheck
                                    ch_select']) !!}<label
                                            class="search_label">@lang('superadmin::lang.meter_resetting')</label>
                                </div>
                                <div class="col-md-4">
                                    {!! Form::checkbox('pay_excess_commission', 1,
                                    !empty($manage_module_enable['pay_excess_commission']) ? true : false, ['class' =>
                                    'input-icheck
                                    ch_select']) !!}<label
                                            class="search_label">@lang('superadmin::lang.pay_excess_commission')</label>
                                </div>
                                <div class="col-md-4">
                                    {!! Form::checkbox('recover_shortage', 1,
                                    !empty($manage_module_enable['recover_shortage']) ? true : false, ['class' =>
                                    'input-icheck
                                    ch_select']) !!}<label
                                            class="search_label">@lang('superadmin::lang.recover_shortage')</label>
                                </div>
                                <div class="clearfix"></div><br>
                                <div class="col-md-4">
                                    {!! Form::checkbox('pump_operator_ledger', 1,
                                    !empty($manage_module_enable['pump_operator_ledger']) ? true : false, ['class' =>
                                    'input-icheck
                                    ch_select']) !!}<label
                                            class="search_label">@lang('superadmin::lang.pump_operator_ledger')</label>
                                </div>
                                <div class="col-md-4">
                                    {!! Form::checkbox('commission_type', 1,
                                    !empty($manage_module_enable['commission_type']) ? true : false, ['class' =>
                                    'input-icheck
                                    ch_select']) !!}<label
                                            class="search_label">@lang('superadmin::lang.commission_type')</label>
                                </div>
                                <div class="col-md-4">
                                    {!! Form::checkbox('select_pump_operator_in_settlement', 1,
                                    !empty($manage_module_enable['select_pump_operator_in_settlement']) ? true : false, ['class' =>
                                    'input-icheck
                                    ch_select']) !!}<label
                                            class="search_label">@lang('superadmin::lang.select_pump_operator_in_settlement')</label>
                                </div>
                            </div>
                            <br>
                            <div class="col-md-12">
                                {!! Form::label('number_of_pumps', __('superadmin::lang.number_of_pumps'). ':', ['class' =>
                                'search_label']) !!}
                                <div class="clearfix"></div>
                                @foreach ($business_locations as $location)
                                    <div class="col-md-3">
                                        {!! Form::label('location_pumps', $location->name) !!}
                                        {!! Form::text('module_permission_location[number_of_pumps]['.$location->id.']',
                                        !empty($module_permission_locations_value['number_of_pumps']->locations[$location->id]) ?
                                        $module_permission_locations_value['number_of_pumps']->locations[$location->id] : null,
                                        ['class' => 'form-control']) !!}
                                    </div>
                                @endforeach
                            </div>
                            <div class="clearfix"></div>
                            <br>
                            <hr>
                            <div class="row">
                                <div class="col-md-6">
                                    <label class="search_label">@lang('superadmin::lang.tank_dip_chart')</label>
                                </div>
                                <div class="col-md-3">
                                    {!! Form::checkbox('tank_dip_chart', 1,
                                    !empty($manage_module_enable['tank_dip_chart']) ? true : false, ['class' =>
                                    'input-icheck
                                    ch_select']) !!}
                                </div>
                                <div class="col-md-3">
                                    {!! Form::text('tank_dip_chart_value',
                                    !empty($module_enable_price->tank_dip_chart) ?
                                    $module_enable_price->tank_dip_chart : null, ['class' => 'form-control']) !!}
                                </div>
                            </div>

                            <hr>
                            <div class="row">
                                <div class="col-md-6">
                                    <label class="search_label">@lang('superadmin::lang.tasks_management')</label>
                                </div>
                                <div class="col-md-3">
                                    {!! Form::checkbox('tasks_management', 1,
                                    !empty($manage_module_enable['tasks_management']) ? true : false, ['class' =>
                                    'input-icheck
                                    ch_select']) !!}
                                </div>
                                <div class="col-md-3">
                                    {!! Form::text('tasks_management_value',
                                    !empty($module_enable_price->tasks_management) ?
                                    $module_enable_price->tasks_management : null, ['class' => 'form-control']) !!}
                                </div>
                            </div>
                            <br>
                            <div class="row">
                                <div class="col-md-4">
                                    {!! Form::checkbox('notes_page', 1,
                                    !empty($manage_module_enable['notes_page']) ? true : false, ['class' =>
                                    'input-icheck
                                    ch_select']) !!}<label class="search_label">@lang('superadmin::lang.notes_page')</label>
                                </div>
                                <div class="col-md-4">
                                    {!! Form::checkbox('tasks_page', 1,
                                    !empty($manage_module_enable['tasks_page']) ? true : false, ['class' =>
                                    'input-icheck
                                    ch_select']) !!}<label class="search_label">@lang('superadmin::lang.tasks_page')</label>
                                </div>
                                <div class="col-md-4">
                                    {!! Form::checkbox('reminder_page', 1,
                                    !empty($manage_module_enable['reminder_page']) ? true : false, ['class' =>
                                    'input-icheck
                                    ch_select']) !!}<label class="search_label">@lang('superadmin::lang.reminder_page')</label>
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-md-6">
                                    <label class="search_label">@lang('superadmin::lang.repair_module')</label>
                                </div>
                                <div class="col-md-3">
                                    {!! Form::checkbox('repair_module', 1,
                                    !empty($manage_module_enable['repair_module']) ? true : false, ['class' =>
                                    'input-icheck
                                    ch_select']) !!}
                                </div>
                                <div class="col-md-3">
                                    {!! Form::text('repair_module_value',
                                    !empty($module_enable_price->repair_module) ?
                                    $module_enable_price->repair_module : null, ['class' => 'form-control']) !!}
                                </div>
                            </div>
                            <br>
                            <div class="row">
                                <div class="col-md-4">
                                    {!! Form::checkbox('job_sheets', 1,
                                    !empty($manage_module_enable['job_sheets']) ? true : false, ['class' =>
                                    'input-icheck
                                    ch_select']) !!}<label class="search_label">@lang('superadmin::lang.job_sheets')</label>
                                </div>
                                <div class="col-md-4">
                                    {!! Form::checkbox('add_job_sheet', 1,
                                    !empty($manage_module_enable['add_job_sheet']) ? true : false, ['class' =>
                                    'input-icheck
                                    ch_select']) !!}<label class="search_label">@lang('superadmin::lang.add_job_sheet')</label>
                                </div>
                                <div class="col-md-4">
                                    {!! Form::checkbox('list_invoice', 1,
                                    !empty($manage_module_enable['list_invoice']) ? true : false, ['class' =>
                                    'input-icheck
                                    ch_select']) !!}<label class="search_label">@lang('superadmin::lang.list_invoice')</label>
                                </div>
                                <div class="col-md-4">
                                    {!! Form::checkbox('add_invoice', 1,
                                    !empty($manage_module_enable['add_invoice']) ? true : false, ['class' =>
                                    'input-icheck
                                    ch_select']) !!}<label class="search_label">@lang('superadmin::lang.add_invoice')</label>
                                </div>
                                <div class="col-md-4">
                                    {!! Form::checkbox('brands', 1,
                                    !empty($manage_module_enable['brands']) ? true : false, ['class' =>
                                    'input-icheck
                                    ch_select']) !!}<label class="search_label">@lang('superadmin::lang.brands')</label>
                                </div>
                                <div class="col-md-4">
                                    {!! Form::checkbox('repair_settings', 1,
                                    !empty($manage_module_enable['repair_settings']) ? true : false, ['class' =>
                                    'input-icheck
                                    ch_select']) !!}<label
                                            class="search_label">@lang('superadmin::lang.repair_settings')</label>
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-md-6">
                                    <label class="search_label">@lang('superadmin::lang.member_registration')</label>
                                </div>
                                <div class="col-md-3">
                                    {!! Form::checkbox('member_registration', 1,
                                    !empty($manage_module_enable['member_registration']) ? true : false, ['class' =>
                                    'input-icheck
                                    ch_select']) !!}
                                </div>
                                <div class="col-md-3">
                                    {!! Form::text('member_registration_value',
                                    !empty($module_enable_price->member_registration) ?
                                    $module_enable_price->member_registration : null, ['class' => 'form-control']) !!}
                                </div>
                            </div>
                            <hr>
                            <br>
                            <div class="row">
                                <div class="col-md-6">
                                    <label class="search_label">@lang('superadmin::lang.fleet_module')</label>
                                </div>
                                <div class="col-md-3">
                                    {!! Form::checkbox('fleet_module', 1,
                                    !empty($manage_module_enable['fleet_module']) ? true : false, ['class' =>
                                    'input-icheck
                                    ch_select']) !!}
                                </div>
                                <div class="col-md-3">
                                    {!! Form::text('fleet_module_value',
                                    !empty($module_enable_price->fleet_module) ?
                                    $module_enable_price->fleet_module : null, ['class' => 'form-control']) !!}
                                </div>
                            </div>
                            <hr>
                            <br>
                            <div class="row">
                                <div class="col-md-6">
                                    <label class="search_label">@lang('superadmin::lang.mpcs_module')</label>
                                </div>
                                <div class="col-md-3">
                                    {!! Form::checkbox('mpcs_module', 1,
                                    !empty($manage_module_enable['mpcs_module']) ? true : false, ['class' =>
                                    'input-icheck
                                    ch_select']) !!}
                                </div>
                                <div class="col-md-3">
                                    {!! Form::text('mpcs_module_value',
                                    !empty($module_enable_price->mpcs_module) ?
                                    $module_enable_price->mpcs_module : null, ['class' => 'form-control']) !!}
                                </div>
                            </div>
                            <br>
                            <div class="row">
                                <div class="col-md-4">
                                    {!! Form::checkbox('mpcs_form_settings', 1,
                                    !empty($manage_module_enable['mpcs_form_settings']) ? true : false, ['class' =>
                                    'input-icheck
                                    ']) !!}<label class="search_label">@lang('mpcs::lang.mpcs_form_settings')</label>
                                </div>
                                <div class="col-md-4">
                                    {!! Form::checkbox('list_opening_values', 1,
                                    !empty($manage_module_enable['list_opening_values']) ? true : false, ['class' =>
                                    'input-icheck
                                    ']) !!}<label class="search_label">@lang('mpcs::lang.list_opening_values')</label>
                                </div>
                            </div>
                            <br>
                            <hr>
                            <div class="row">
                                <div class="col-md-6">
                                    <label class="search_label">@lang('superadmin::lang.merge_sub_category')</label>
                                </div>
                                <div class="col-md-3">
                                    {!! Form::checkbox('merge_sub_category', 1,
                                    !empty($manage_module_enable['merge_sub_category']) ? true : false, ['class' =>
                                    'input-icheck
                                    ch_select']) !!}
                                </div>
                                <div class="col-md-3">
                                    {!! Form::text('merge_sub_category_value',
                                    !empty($module_enable_price->merge_sub_category) ?
                                    $module_enable_price->merge_sub_category : null, ['class' => 'form-control']) !!}
                                </div>
                            </div>
                            <br>
                            <hr>
                            <div class="row">
                                <div class="col-md-6">
                                    <label class="search_label">@lang('superadmin::lang.backup_module')</label>
                                </div>
                                <div class="col-md-3">
                                    {!! Form::checkbox('backup_module', 1,
                                    !empty($manage_module_enable['backup_module']) ? true : false, ['class' =>
                                    'input-icheck
                                    ch_select']) !!}
                                </div>
                                <div class="col-md-3">
                                    {!! Form::text('backup_module_value',
                                    !empty($module_enable_price->backup_module) ?
                                    $module_enable_price->backup_module : null, ['class' => 'form-control']) !!}
                                </div>
                            </div>
                            <br>
                            <hr>
                            <div class="row">
                                <div class="col-md-6">
                                    <label class="search_label">@lang('contact.enable_separate_customer_statement_no')</label>
                                </div>
                                <div class="col-md-3">
                                    {!! Form::checkbox('enable_separate_customer_statement_no', 1,
                                    !empty($manage_module_enable['enable_separate_customer_statement_no']) ? true : false,
                                    ['class' =>
                                    'input-icheck
                                    ch_select']) !!}
                                </div>
                                <div class="col-md-3">
                                    {!! Form::text('enable_separate_customer_statement_no_value',
                                    !empty($module_enable_price->enable_separate_customer_statement_no) ?
                                    $module_enable_price->enable_separate_customer_statement_no : null, ['class' =>
                                    'form-control']) !!}
                                </div>
                            </div>
                            <br>
                            <hr>
                            <div class="row">
                                <div class="col-md-6">
                                    <label class="search_label">@lang('contact.edit_customer_statement')</label>
                                </div>
                                <div class="col-md-3">
                                    {!! Form::checkbox('edit_customer_statement', 1,
                                    !empty($manage_module_enable['edit_customer_statement']) ? true : false, ['class' =>
                                    'input-icheck
                                    ch_select']) !!}
                                </div>
                                <div class="col-md-3">
                                    {!! Form::text('edit_customer_statement_value',
                                    !empty($module_enable_price->edit_customer_statement) ?
                                    $module_enable_price->edit_customer_statement : null, ['class' => 'form-control']) !!}
                                </div>
                            </div>
                            <br>
                            <hr>
                            <div class="row">
                                <div class="col-md-6">
                                    <label class="search_label">@lang('lang_v1.enable_cheque_writing')</label>
                                </div>
                                <div class="col-md-3">
                                    {!! Form::checkbox('enable_cheque_writing', 1,
                                    !empty($manage_module_enable['enable_cheque_writing']) ? true : false, ['class' =>
                                    'input-icheck
                                    ch_select']) !!}
                                </div>
                                <div class="col-md-3">
                                    {!! Form::text('enable_cheque_writing_value',
                                    !empty($module_enable_price->enable_cheque_writing) ?
                                    $module_enable_price->enable_cheque_writing : null, ['class' => 'form-control']) !!}
                                </div>
                            </div>
                            <br>
                            <hr>
                            <div class="row">
                                <div class="col-md-6">
                                    <label class="search_label">@lang('superadmin::lang.issue_customer_bill')</label>
                                </div>
                                <div class="col-md-3">
                                    {!! Form::checkbox('issue_customer_bill', 1,
                                    !empty($manage_module_enable['issue_customer_bill']) ? true : false, ['class' =>
                                    'input-icheck
                                    ch_select']) !!}
                                </div>
                                <div class="col-md-3">
                                    {!! Form::text('issue_customer_bill_value',
                                    !empty($module_enable_price->issue_customer_bill) ?
                                    $module_enable_price->issue_customer_bill : null, ['class' => 'form-control']) !!}
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-md-6">
                                    <label class="search_label">@lang('superadmin::lang.home_dashboard')</label>
                                </div>
                                <div class="col-md-3">
                                    {!! Form::checkbox('home_dashboard', 1,
                                    !empty($manage_module_enable['home_dashboard']) ? true : false, ['class' =>
                                    'input-icheck
                                    ch_select']) !!}
                                </div>
                                <div class="col-md-3">
                                    {!! Form::text('home_dashboard_value',
                                    !empty($module_enable_price->home_dashboard) ?
                                    $module_enable_price->home_dashboard : null, ['class' => 'form-control']) !!}
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-md-6">
                                    <label class="search_label">@lang('superadmin::lang.contact_module')</label>
                                </div>
                                <div class="col-md-3">
                                    {!! Form::checkbox('contact_module', 1,
                                    !empty($manage_module_enable['contact_module']) ? true : false, ['class' =>
                                    'input-icheck
                                    ch_select']) !!}
                                </div>
                                <div class="col-md-3">
                                    {!! Form::text('contact_module_value',
                                    !empty($module_enable_price->contact_module) ?
                                    $module_enable_price->contact_module : null, ['class' => 'form-control']) !!}
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <label class="search_label">@lang('superadmin::lang.contact_supplier')</label>
                                </div>
                                <div class="col-md-3">
                                    {!! Form::checkbox('contact_supplier', 1,
                                    !empty($manage_module_enable['contact_supplier']) ? true : false, ['class' =>
                                    'input-icheck
                                    ch_select']) !!}
                                </div>
                                <div class="col-md-3">
                                    {!! Form::text('contact_supplier_value',
                                    !empty($module_enable_price->contact_supplier) ?
                                    $module_enable_price->contact_supplier : null, ['class' => 'form-control']) !!}
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <label class="search_label">@lang('superadmin::lang.contact_customer')</label>
                                </div>
                                <div class="col-md-3">
                                    {!! Form::checkbox('contact_customer', 1,
                                    !empty($manage_module_enable['contact_customer']) ? true : false, ['class' =>
                                    'input-icheck
                                    ch_select']) !!}
                                </div>
                                <div class="col-md-3">
                                    {!! Form::text('contact_customer_value',
                                    !empty($module_enable_price->contact_customer) ?
                                    $module_enable_price->contact_customer : null, ['class' => 'form-control']) !!}
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-md-6">
                                    <label class="search_label">@lang('superadmin::lang.property_module')</label>
                                </div>
                                <div class="col-md-3">
                                    {!! Form::checkbox('property_module', 1,
                                    !empty($manage_module_enable['property_module']) ? true : false, ['class' =>
                                    'input-icheck
                                    ch_select']) !!}
                                </div>
                                <div class="col-md-3">
                                    {!! Form::text('property_module_value',
                                    !empty($module_enable_price->property_module) ?
                                    $module_enable_price->property_module : null, ['class' => 'form-control']) !!}
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-md-6">
                                    <label class="search_label">@lang('superadmin::lang.ran_module')</label>
                                </div>
                                <div class="col-md-3">
                                    {!! Form::checkbox('ran_module', 1,
                                    !empty($manage_module_enable['ran_module']) ? true : false, ['class' =>
                                    'input-icheck
                                    ch_select']) !!}
                                </div>
                                <div class="col-md-3">
                                    {!! Form::text('ran_module_value',
                                    !empty($module_enable_price->ran_module) ?
                                    $module_enable_price->ran_module : null, ['class' => 'form-control']) !!}
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-md-6">
                                    <label class="search_label">@lang('superadmin::lang.report_module')</label>
                                </div>
                                <div class="col-md-3">
                                    {!! Form::checkbox('report_module', 1,
                                    !empty($manage_module_enable['report_module']) ? true : false, ['class' =>
                                    'input-icheck
                                    ch_select']) !!}
                                </div>
                                <div class="col-md-3">
                                    {!! Form::text('report_module_value',
                                    !empty($module_enable_price->report_module) ?
                                    $module_enable_price->report_module : null, ['class' => 'form-control']) !!}
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-md-6">
                                    <label class="search_label">@lang('superadmin::lang.verification_report')</label>
                                </div>
                                <div class="col-md-3">
                                    {!! Form::checkbox('verification_report', 1,
                                    !empty($manage_module_enable['verification_report']) ? true : false, ['class' =>
                                    'input-icheck
                                    ch_select']) !!}
                                </div>
                                <div class="col-md-3">
                                    {!! Form::text('verification_report_value',
                                    !empty($module_enable_price->verification_report) ?
                                    $module_enable_price->verification_report : null, ['class' => 'form-control']) !!}
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    {!! Form::checkbox('monthly_report', 1,
                                    !empty($manage_module_enable['monthly_report']) ? true : false, ['class' =>
                                    'input-icheck
                                    ch_select']) !!}<label class="search_label">@lang('superadmin::lang.monthly_report')</label>
                                </div>
                                <div class="col-md-4">
                                    {!! Form::checkbox('comparison_report', 1,
                                    !empty($manage_module_enable['comparison_report']) ? true : false, ['class' =>
                                    'input-icheck
                                    ch_select']) !!}<label
                                            class="search_label">@lang('superadmin::lang.comparison_report')</label>
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-md-6">
                                    <label class="search_label">@lang('superadmin::lang.notification_template_module')</label>
                                </div>
                                <div class="col-md-3">
                                    {!! Form::checkbox('notification_template_module', 1,
                                    !empty($manage_module_enable['notification_template_module']) ? true : false, ['class' =>
                                    'input-icheck
                                    ch_select']) !!}
                                </div>
                                <div class="col-md-3">
                                    {!! Form::text('notification_template_module_value',
                                    !empty($module_enable_price->notification_template_module) ?
                                    $module_enable_price->notification_template_module : null, ['class' => 'form-control']) !!}
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-md-6">
                                    <label class="search_label">@lang('superadmin::lang.list_easy_payment')</label>
                                </div>
                                <div class="col-md-3">
                                    {!! Form::checkbox('list_easy_payment', 1,
                                    !empty($manage_module_enable['list_easy_payment']) ? true : false, ['class' =>
                                    'input-icheck
                                    ch_select']) !!}
                                </div>
                                <div class="col-md-3">
                                    {!! Form::text('list_easy_payment_value',
                                    !empty($module_enable_price->list_easy_payment) ?
                                    $module_enable_price->list_easy_payment : null, ['class' => 'form-control']) !!}
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-md-6">
                                    <label class="search_label">@lang('superadmin::lang.settings_module')</label>
                                </div>
                                <div class="col-md-3">
                                    {!! Form::checkbox('settings_module', 1,
                                    !empty($manage_module_enable['settings_module']) ? true : false, ['class' =>
                                    'input-icheck
                                    ch_select']) !!}
                                </div>
                                <div class="col-md-3">
                                    {!! Form::text('settings_module_value',
                                    !empty($module_enable_price->settings_module) ?
                                    $module_enable_price->settings_module : null, ['class' => 'form-control']) !!}
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    {!! Form::checkbox('business_settings', 1,
                                    !empty($manage_module_enable['business_settings']) ? true : false, ['class' =>
                                    'input-icheck
                                    ch_select']) !!}<label
                                            class="search_label">@lang('superadmin::lang.business_settings')</label>
                                </div>
                                <div class="col-md-4">
                                    {!! Form::checkbox('business_location', 1,
                                    !empty($manage_module_enable['business_location']) ? true : false, ['class' =>
                                    'input-icheck
                                    ch_select']) !!}<label
                                            class="search_label">@lang('superadmin::lang.business_location')</label>
                                </div>
                                <div class="col-md-4">
                                    {!! Form::checkbox('invoice_settings', 1,
                                    !empty($manage_module_enable['invoice_settings']) ? true : false, ['class' =>
                                    'input-icheck
                                    ch_select']) !!}<label
                                            class="search_label">@lang('superadmin::lang.invoice_settings')</label>
                                </div>
                                <div class="col-md-4">
                                    {!! Form::checkbox('tax_rates', 1,
                                    !empty($manage_module_enable['tax_rates']) ? true : false, ['class' =>
                                    'input-icheck
                                    ch_select']) !!}<label class="search_label">@lang('superadmin::lang.tax_rates')</label>
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-md-6">
                                    <label class="search_label">@lang('superadmin::lang.user_management_module')</label>
                                </div>
                                <div class="col-md-3">
                                    {!! Form::checkbox('user_management_module', 1,
                                    !empty($manage_module_enable['user_management_module']) ? true : false, ['class' =>
                                    'input-icheck
                                    ch_select']) !!}
                                </div>
                                <div class="col-md-3">
                                    {!! Form::text('user_management_module_value',
                                    !empty($module_enable_price->user_management_module) ?
                                    $module_enable_price->user_management_module : null, ['class' => 'form-control']) !!}
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-md-6">
                                    <label class="search_label">@lang('superadmin::lang.banking_module')</label>
                                </div>
                                <div class="col-md-3">
                                    {!! Form::checkbox('banking_module', 1,
                                    !empty($manage_module_enable['banking_module']) ? true : false, ['class' =>
                                    'input-icheck
                                    ch_select']) !!}
                                </div>
                                <div class="col-md-3">
                                    {!! Form::text('banking_module_value',
                                    !empty($module_enable_price->banking_module) ?
                                    $module_enable_price->banking_module : null, ['class' => 'form-control']) !!}
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-md-6">
                                    <label class="search_label">@lang('superadmin::lang.products')</label>
                                </div>
                                <div class="col-md-3">
                                    {!! Form::checkbox('products', 1,
                                    !empty($manage_module_enable['products']) ? true : false, ['class' =>
                                    'input-icheck
                                    ch_select']) !!}
                                </div>
                                <div class="col-md-3">
                                    {!! Form::text('products_value',
                                    !empty($module_enable_price->products) ?
                                    $module_enable_price->products : null, ['class' => 'form-control']) !!}
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-md-6">
                                    <label class="search_label">@lang('superadmin::lang.purchase')</label>
                                </div>
                                <div class="col-md-3">
                                    {!! Form::checkbox('purchase', 1,
                                    !empty($manage_module_enable['purchase']) ? true : false, ['class' =>
                                    'input-icheck
                                    ch_select']) !!}
                                </div>
                                <div class="col-md-3">
                                    {!! Form::text('purchase_value',
                                    !empty($module_enable_price->purchase) ?
                                    $module_enable_price->purchase : null, ['class' => 'form-control']) !!}
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-md-6">
                                    <label class="search_label">@lang('superadmin::lang.stock_transfer')</label>
                                </div>
                                <div class="col-md-3">
                                    {!! Form::checkbox('stock_transfer', 1,
                                    !empty($manage_module_enable['stock_transfer']) ? true : false, ['class' =>
                                    'input-icheck
                                    ch_select']) !!}
                                </div>
                                <div class="col-md-3">
                                    {!! Form::text('stock_transfer_value',
                                    !empty($module_enable_price->stock_transfer) ?
                                    $module_enable_price->stock_transfer : null, ['class' => 'form-control']) !!}
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-md-6">
                                    <label class="search_label">@lang('superadmin::lang.service_staff')</label>
                                </div>
                                <div class="col-md-3">
                                    {!! Form::checkbox('service_staff', 1,
                                    !empty($manage_module_enable['service_staff']) ? true : false, ['class' =>
                                    'input-icheck
                                    ch_select']) !!}
                                </div>
                                <div class="col-md-3">
                                    {!! Form::text('service_staff_value',
                                    !empty($module_enable_price->service_staff) ?
                                    $module_enable_price->service_staff : null, ['class' => 'form-control']) !!}
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-md-6">
                                    <label class="search_label">@lang('superadmin::lang.enable_subscription')</label>
                                </div>
                                <div class="col-md-3">
                                    {!! Form::checkbox('enable_subscription', 1,
                                    !empty($manage_module_enable['enable_subscription']) ? true : false, ['class' =>
                                    'input-icheck
                                    ch_select']) !!}
                                </div>
                                <div class="col-md-3">
                                    {!! Form::text('enable_subscription_value',
                                    !empty($module_enable_price->enable_subscription) ?
                                    $module_enable_price->enable_subscription : null, ['class' => 'form-control']) !!}
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-md-6">
                                    <label class="search_label">@lang('superadmin::lang.sale_module')</label>
                                </div>
                                <div class="col-md-3">
                                    {!! Form::checkbox('sale_module', 1,
                                    !empty($manage_module_enable['sale_module']) ? true : false, ['class' =>
                                    'input-icheck
                                    ch_select']) !!}
                                </div>
                                <div class="col-md-3">
                                    {!! Form::text('sale_module_value',
                                    !empty($module_enable_price->sale_module) ?
                                    $module_enable_price->sale_module : null, ['class' => 'form-control']) !!}
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    {!! Form::checkbox('all_sales', 1,
                                    !empty($manage_module_enable['all_sales']) ? true : false, ['class' =>
                                    'input-icheck
                                    ch_select']) !!}<label class="search_label">@lang('superadmin::lang.all_sales')</label>
                                </div>
                                <div class="col-md-4">
                                    {!! Form::checkbox('add_sale', 1,
                                    !empty($manage_module_enable['add_sale']) ? true : false, ['class' =>
                                    'input-icheck
                                    ch_select']) !!}<label class="search_label">@lang('superadmin::lang.add_sale')</label>
                                </div>
                                <div class="col-md-4">
                                    {!! Form::checkbox('pos_sale', 1,
                                    !empty($manage_module_enable['pos_sale']) ? true : false, ['class' =>
                                    'input-icheck
                                    ch_select']) !!}<label class="search_label">@lang('superadmin::lang.pos')</label>
                                </div>
                                <div class="col-md-4">
                                    {!! Form::checkbox('list_pos', 1,
                                    !empty($manage_module_enable['list_pos']) ? true : false, ['class' =>
                                    'input-icheck
                                    ch_select']) !!}<label class="search_label">@lang('superadmin::lang.list_pos')</label>
                                </div>
                                <div class="col-md-4">
                                    {!! Form::checkbox('list_draft', 1,
                                    !empty($manage_module_enable['list_draft']) ? true : false, ['class' =>
                                    'input-icheck
                                    ch_select']) !!}<label class="search_label">@lang('superadmin::lang.list_draft')</label>
                                </div>
                                <div class="col-md-4">
                                    {!! Form::checkbox('list_quotation', 1,
                                    !empty($manage_module_enable['list_quotation']) ? true : false, ['class' =>
                                    'input-icheck
                                    ch_select']) !!}<label class="search_label">@lang('superadmin::lang.list_quotation')</label>
                                </div>
                                <div class="col-md-4">
                                    {!! Form::checkbox('list_sell_return', 1,
                                    !empty($manage_module_enable['list_sell_return']) ? true : false, ['class' =>
                                    'input-icheck
                                    ch_select']) !!}<label
                                            class="search_label">@lang('superadmin::lang.list_sell_return')</label>
                                </div>
                                <div class="col-md-4">
                                    {!! Form::checkbox('shipment', 1,
                                    !empty($manage_module_enable['shipment']) ? true : false, ['class' =>
                                    'input-icheck
                                    ch_select']) !!}<label class="search_label">@lang('superadmin::lang.shipment')</label>
                                </div>
                                <div class="col-md-4">
                                    {!! Form::checkbox('discount', 1,
                                    !empty($manage_module_enable['discount']) ? true : false, ['class' =>
                                    'input-icheck
                                    ch_select']) !!}<label class="search_label">@lang('superadmin::lang.discount')</label>
                                </div>
                                <div class="col-md-4">
                                    {!! Form::checkbox('import_sale', 1,
                                    !empty($manage_module_enable['import_sale']) ? true : false, ['class' =>
                                    'input-icheck
                                    ch_select']) !!}<label class="search_label">@lang('superadmin::lang.import_sale')</label>
                                </div>
                                <div class="col-md-4">
                                    {!! Form::checkbox('reserved_stock', 1,
                                    !empty($manage_module_enable['reserved_stock']) ? true : false, ['class' =>
                                    'input-icheck
                                    ch_select']) !!}<label class="search_label">@lang('superadmin::lang.reserved_stock')</label>
                                </div>
                                <div class="col-md-4">
                                    {!! Form::checkbox('list_orders', 1,
                                    !empty($manage_module_enable['list_orders']) ? true : false, ['class' =>
                                    'input-icheck
                                    ch_select']) !!}<label class="search_label">@lang('superadmin::lang.list_orders')</label>
                                </div>
                                <div class="col-md-4">
                                    {!! Form::checkbox('upload_orders', 1,
                                    !empty($manage_module_enable['upload_orders']) ? true : false, ['class' =>
                                    'input-icheck
                                    ch_select']) !!}<label class="search_label">@lang('superadmin::lang.upload_orders')</label>
                                </div>
                                <div class="col-md-4">
                                    {!! Form::checkbox('subcriptions', 1,
                                    !empty($manage_module_enable['subcriptions']) ? true : false, ['class' =>
                                    'input-icheck
                                    ch_select']) !!}<label class="search_label">@lang('superadmin::lang.subcriptions')</label>
                                </div>
                                <div class="col-md-4">
                                    {!! Form::checkbox('over_limit_sales', 1,
                                    !empty($manage_module_enable['over_limit_sales']) ? true : false, ['class' =>
                                    'input-icheck
                                    ch_select']) !!}<label
                                            class="search_label">@lang('superadmin::lang.over_limit_sales')</label>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-3">
                                    {!! Form::checkbox('status_order', 1,
                                    !empty($manage_module_enable['status_order']) ? true : false, ['class' =>
                                    'input-icheck
                                    ch_select']) !!}<label class="search_label">@lang('superadmin::lang.status_order')</label>
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-md-6">
                                    <label class="search_label">@lang('superadmin::lang.stock_adjustment')</label>
                                </div>
                                <div class="col-md-3">
                                    {!! Form::checkbox('stock_adjustment', 1,
                                    !empty($manage_module_enable['stock_adjustment']) ? true : false, ['class' =>
                                    'input-icheck
                                    ch_select']) !!}
                                </div>
                                <div class="col-md-3">
                                    {!! Form::text('stock_adjustment_value',
                                    !empty($module_enable_price->stock_adjustment) ?
                                    $module_enable_price->stock_adjustment : null, ['class' => 'form-control']) !!}
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-md-6">
                                    <label class="search_label">@lang('superadmin::lang.tables')</label>
                                </div>
                                <div class="col-md-3">
                                    {!! Form::checkbox('tables', 1,
                                    !empty($manage_module_enable['tables']) ? true : false, ['class' =>
                                    'input-icheck
                                    ch_select']) !!}
                                </div>
                                <div class="col-md-3">
                                    {!! Form::text('tables_value',
                                    !empty($module_enable_price->tables) ?
                                    $module_enable_price->tables : null, ['class' => 'form-control']) !!}
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-md-6">
                                    <label class="search_label">@lang('superadmin::lang.type_of_service')</label>
                                </div>
                                <div class="col-md-3">
                                    {!! Form::checkbox('type_of_service', 1,
                                    !empty($manage_module_enable['type_of_service']) ? true : false, ['class' =>
                                    'input-icheck
                                    ch_select']) !!}
                                </div>
                                <div class="col-md-3">
                                    {!! Form::text('type_of_service_value',
                                    !empty($module_enable_price->type_of_service) ?
                                    $module_enable_price->type_of_service : null, ['class' => 'form-control']) !!}
                                </div>
                            </div>
                            <hr>
                            {{-- <div class="row">
                                <div class="col-md-6">
                                    <label class="search_label">@lang('superadmin::lang.pos_sale')</label>
                                </div>
                                <div class="col-md-3">
                                    {!! Form::checkbox('pos_sale', 1,
                                    !empty($manage_module_enable['pos_sale']) ? true : false, ['class' =>
                                    'input-icheck
                                    ch_select']) !!}
                                </div>
                                <div class="col-md-3">
                                    {!! Form::text('pos_sale_value',
                                    !empty($module_enable_price->pos_sale) ?
                                    $module_enable_price->pos_sale : null, ['class' => 'form-control']) !!}
                                </div>
                            </div>
                            <hr> --}}
                            <div class="row">
                                <div class="col-md-6">
                                    <label class="search_label">@lang('superadmin::lang.expenses')</label>
                                </div>
                                <div class="col-md-3">
                                    {!! Form::checkbox('expenses', 1,
                                    !empty($manage_module_enable['expenses']) ? true : false, ['class' =>
                                    'input-icheck
                                    ch_select']) !!}
                                </div>
                                <div class="col-md-3">
                                    {!! Form::text('expenses_value',
                                    !empty($module_enable_price->expenses) ?
                                    $module_enable_price->expenses : null, ['class' => 'form-control']) !!}
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-md-6">
                                    <label class="search_label">@lang('superadmin::lang.modifiers')</label>
                                </div>
                                <div class="col-md-3">
                                    {!! Form::checkbox('modifiers', 1,
                                    !empty($manage_module_enable['modifiers']) ? true : false, ['class' =>
                                    'input-icheck
                                    ch_select']) !!}
                                </div>
                                <div class="col-md-3">
                                    {!! Form::text('modifiers_value',
                                    !empty($module_enable_price->modifiers) ?
                                    $module_enable_price->modifiers : null, ['class' => 'form-control']) !!}
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-md-6">
                                    <label class="search_label">@lang('superadmin::lang.kitchen')</label>
                                </div>
                                <div class="col-md-3">
                                    {!! Form::checkbox('kitchen', 1,
                                    !empty($manage_module_enable['kitchen']) ? true : false, ['class' =>
                                    'input-icheck
                                    ch_select']) !!}
                                </div>
                                <div class="col-md-3">
                                    {!! Form::text('kitchen_value',
                                    !empty($module_enable_price->kitchen) ?
                                    $module_enable_price->kitchen : null, ['class' => 'form-control']) !!}
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-md-6">
                                    <label class="search_label">@lang('superadmin::lang.leads_module')</label>
                                </div>
                                <div class="col-md-3">
                                    {!! Form::checkbox('leads_module', 1,
                                    !empty($manage_module_enable['leads_module']) ? true : false, ['class' =>
                                    'input-icheck
                                    ch_select']) !!}
                                </div>
                                <div class="col-md-3">
                                    {!! Form::text('leads_module_value',
                                    !empty($module_enable_price->leads_module) ?
                                    $module_enable_price->leads_module : null, ['class' => 'form-control']) !!}
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-3">
                                    {!! Form::checkbox('leads', 1,
                                    !empty($manage_module_enable['leads']) ? true : false, ['class' =>
                                    'input-icheck
                                    ch_select']) !!}<label class="search_label">@lang('superadmin::lang.leads')</label>
                                </div>
                                <div class="col-md-3">
                                    {!! Form::checkbox('day_count', 1,
                                    !empty($manage_module_enable['day_count']) ? true : false, ['class' =>
                                    'input-icheck
                                    ch_select']) !!}<label class="search_label">@lang('superadmin::lang.day_count')</label>
                                </div>
                                <div class="col-md-3">
                                    {!! Form::checkbox('leads_import', 1,
                                    !empty($manage_module_enable['leads_import']) ? true : false, ['class' =>
                                    'input-icheck
                                    ch_select']) !!}<label class="search_label">@lang('superadmin::lang.leads_import')</label>
                                </div>
                                <div class="col-md-3">
                                    {!! Form::checkbox('leads_settings', 1,
                                    !empty($manage_module_enable['leads_settings']) ? true : false, ['class' =>
                                    'input-icheck
                                    ch_select']) !!}<label class="search_label">@lang('superadmin::lang.settings')</label>
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-md-6">
                                    <label class="search_label">@lang('superadmin::lang.cache_clear')</label>
                                </div>
                                <div class="col-md-3">
                                    {!! Form::checkbox('cache_clear', 1,
                                    !empty($manage_module_enable['cache_clear']) ? true : false, ['class' =>
                                    'input-icheck
                                    ch_select']) !!}
                                </div>
                                <div class="col-md-3">
                                    {!! Form::text('cache_clear_value',
                                    !empty($module_enable_price->cache_clear) ?
                                    $module_enable_price->cache_clear : null, ['class' => 'form-control']) !!}
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-md-6">
                                    <label class="search_label">@lang('superadmin::lang.pump_operator_dashboard')</label>
                                </div>
                                <div class="col-md-3">
                                    {!! Form::checkbox('pump_operator_dashboard', 1,
                                    !empty($manage_module_enable['pump_operator_dashboard']) ? true : false, ['class' =>
                                    'input-icheck
                                    ch_select']) !!}
                                </div>
                                <div class="col-md-3">
                                    {!! Form::text('pump_operator_dashboard_value',
                                    !empty($module_enable_price->pump_operator_dashboard) ?
                                    $module_enable_price->pump_operator_dashboard : null, ['class' => 'form-control']) !!}
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-md-6">
                                    <label class="search_label">@lang('superadmin::lang.sms')</label>
                                </div>
                                <div class="col-md-3">
                                    {!! Form::checkbox('sms_module', 1,
                                    !empty($manage_module_enable['sms_module']) ? true : false, ['class' =>
                                    'input-icheck
                                    ch_select']) !!}
                                </div>
                                <div class="col-md-3">
                                    {!! Form::text('sms_module_value',
                                    !empty($module_enable_price->sms_module) ?
                                    $module_enable_price->sms_module : null, ['class' => 'form-control']) !!}
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-3">
                                    {!! Form::checkbox('list_sms', 1,
                                    !empty($manage_module_enable['list_sms']) ? true : false, ['class' =>
                                    'input-icheck
                                    ch_select']) !!}<label class="search_label">@lang('superadmin::lang.list_sms')</label>
                                </div>
                            </div>
                            <hr>



                            <div class="row">
                                <div class="col-md-6">
                                    <label class="search_label">@lang('superadmin::lang.customer_interest_deduct_option')</label>
                                </div>
                                <div class="col-md-3">
                                    {!! Form::checkbox('customer_interest_deduct_option', 1,
                                    !empty($manage_module_enable['customer_interest_deduct_option']) ? true : false, ['class' =>
                                    'input-icheck
                                    ch_select']) !!}
                                </div>
                                <div class="col-md-3">
                                    {!! Form::text('customer_interest_deduct_option_value',
                                    !empty($module_enable_price->customer_interest_deduct_option_value) ?
                                    $module_enable_price->customer_interest_deduct_option_value : null, ['class' => 'form-control']) !!}
                                </div>
                            </div>



                            <hr>
                            <div class="row">
                                <div class="col-md-6">
                                    <label class="search_label">@lang('superadmin::lang.upload_images')</label>
                                </div>
                                <div class="col-md-3">
                                    {!! Form::checkbox('upload_images', 1,
                                    !empty($manage_module_enable['upload_images']) ? true : false, ['class' =>
                                    'input-icheck
                                    ch_select']) !!}
                                </div>
                                <div class="col-md-3">
                                    {!! Form::text('upload_images_value',
                                    !empty($module_enable_price->upload_images) ?
                                    $module_enable_price->upload_images : null, ['class' => 'form-control']) !!}
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>@lang('superadmin::lang.login_page_showing_type')</label>
                                        {!! Form::select('background_showing_type', ['only_background_image'
                                        =>__('superadmin::lang.only_background_image'), 'background_image_and_logo' =>
                                        __('superadmin::lang.background_image_and_logo')],
                                        $business_details->background_showing_type , ['class' => 'form-control',
                                        'placeholder' => __('superadmin::lang.please_select')]) !!}
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        {!! Form::label('background_image', __( 'superadmin::lang.background_image' ) . ':') !!}
                                        {!! Form::file('background_image', ['accept' => 'image/*']); !!}
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        {!! Form::label('logo', __( 'superadmin::lang.logo' ) . ':') !!}
                                        {!! Form::file('logo', ['accept' => 'image/*']); !!}
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <div class="col-sm-4">
                                <label class="search_label">
                                    {!! Form::label('sale_import_date', __('lang_v1.sale_import_date'), []) !!}
                                    {!! Form::text('sale_import_date',
                                    !empty($sale_import_date) ?
                                    $sale_import_date : null, ['class' => 'form-control']) !!}
                                </label>
                            </div>
                            <div class="col-sm-4">
                                <label class="search_label">
                                    {!! Form::label('purchase_import_date', __('lang_v1.purchase_import_date'), []) !!}
                                    {!! Form::text('purchase_import_date',
                                    !empty($purchase_import_date) ?
                                    $purchase_import_date : null, ['class' => 'form-control']) !!}
                                </label>
                            </div>
                        </div>
                    </div>

                    <hr>

                    <div class="col-sm-12">
                        <label class="search_label">@lang('lang_v1.payment_options'):</label>
                    @foreach ($business_locations as $business_location)
                        <!--Accordion wrapper-->
                            <div class="accordion md-accordion" id="accordionEx{{$business_location->id}}" role="tablist"
                                 aria-multiselectable="true">

                                <div class="card">
                                    <div class="card-header" role="tab" id="headingTwo{{$business_location->id}}">
                                        <a class="collapsed" data-toggle="collapse"
                                           data-parent="#accordionEx{{$business_location->id}}"
                                           href="#collapseTwo{{$business_location->id}}" aria-expanded="false"
                                           aria-controls="collapseTwo{{$business_location->id}}">
                                            <h5 class="mb-0 text-black">
                                                <label class="search_label">{{$business_location->name}}</label>
                                                <i class="fa fa-angle-down rotate-icon pull-right"></i>
                                            </h5>
                                        </a>
                                    </div>
                                    <div id="collapseTwo{{$business_location->id}}" class="collapse" role="tabpanel"
                                         aria-labelledby="headingTwo{{$business_location->id}}"
                                         data-parent="#accordionEx{{$business_location->id}}">
                                        <div class="card-body" style="margin-bottom: 10px;">
                                            <hr>
                                            @php
                                                $default_payment_accounts = json_decode($business_location->default_payment_accounts);
                                            @endphp
                                            <table class="table table-condensed table-striped">
                                                <thead>
                                                <tr>
                                                    <th class="text-center">@lang('lang_v1.payment_method')</th>
                                                    <th class="text-center">@lang('lang_v1.enable')</th>
                                                    <th class="text-center @if(empty($accounts)) hide @endif">
                                                        @lang('lang_v1.default_accounts')
                                                        @show_tooltip(__('lang_v1.default_account_help'))</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                @foreach($payment_types as $key => $value)
                                                    <tr>
                                                        <td class="text-center">{{$value}}</td>
                                                        <td class="text-center">{!!
                                                Form::checkbox('default_payment_accounts['.$business_location->id.']['.$key.'][is_enabled]',
                                                1,
                                                !empty($default_payment_accounts->$key->is_enabled) ?
                                                $default_payment_accounts->$key->is_enabled : 0); !!}</td>
                                                        <td class="text-center @if(empty($accounts)) hide @endif">
                                                            {!! Form::select('default_payment_accounts['.$business_location->id.']['
                                                            . $key . '][account]', $only_assets_accounts,
                                                            !empty($default_payment_accounts->$key->account) ?
                                                            $default_payment_accounts->$key->account :
                                                            null, ['class' => 'form-control input-sm', 'id' => 'account_'.$key]);
                                                            !!}
                                                        </td>
                                                    </tr>
                                                @endforeach
                                                </tbody>
                                            </table>
                                            <hr>
                                        </div>
                                    </div>

                                </div>

                            </div>
                        @endforeach
                    </div>
                @endcan
            </div>
        </div>

        <div class="box">
            <div class="box-body">
                @can('superadmin')
                    <h3> @lang('superadmin::lang.with_variables')</h3>
                    <div class="col-md-6">
                        <div class="row">
                            <div class="col-md-5">

                            </div>
                            <div class="col-md-3">
                                {!! Form::label('current_value', __('superadmin::lang.current_values'), ['class' =>
                                'search_label']) !!}
                            </div>
                            <div class="col-md-4">

                            </div>
                        </div>
                        </br>
                        <div class="row">
                            <div class="col-md-5">
                                <label class="search_label">@lang('superadmin::lang.number_of_branches')</label>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    {!! Form::number('current_values[number_of_branches]',
                                    !empty($current_values['number_of_branches']) ? $current_values['number_of_branches'] :
                                    null, ['class' => 'form-control']) !!}
                                </div>
                            </div>
                            <div class="col-md-4">
                                <button class="btn btn-primary btn-xs btn-modal" data-container=".option_modal"
                                        data-href="{{action('\Modules\Superadmin\Http\Controllers\CompanyPackageVariableController@getOptionVariables', [ 'id' => '0', 'business_id' => $business->id])}}">@lang('superadmin::lang.enter_variables')</button>
                            </div>
                        </div>
                        </br>
                        <div class="row">
                            <div class="col-md-5">
                                <label class="search_label">@lang('superadmin::lang.number_of_users')</label>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    {!! Form::number('current_values[number_of_users]',
                                    !empty($current_values['number_of_users']) ? $current_values['number_of_users'] : null,
                                    ['class' => 'form-control']) !!}
                                </div>
                            </div>
                            <div class="col-md-4">
                                <button class="btn btn-primary btn-xs btn-modal" data-container=".option_modal"
                                        data-href="{{action('\Modules\Superadmin\Http\Controllers\CompanyPackageVariableController@getOptionVariables', [ 'id' => '1', 'business_id' => $business->id])}}">@lang('superadmin::lang.enter_variables')</button>
                            </div>
                        </div>
                        </br>
                        <div class="row">
                            <div class="col-md-5">
                                <label class="search_label">@lang('superadmin::lang.number_of_customers')</label>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    {!! Form::number('current_values[number_of_customers]',
                                    !empty($current_values['number_of_customers']) ? $current_values['number_of_customers'] :
                                    null, ['class' => 'form-control']) !!}
                                </div>
                            </div>
                            <div class="col-md-4">
                                <button class="btn btn-primary btn-xs btn-modal" data-container=".option_modal"
                                        data-href="{{action('\Modules\Superadmin\Http\Controllers\CompanyPackageVariableController@getOptionVariables', [ 'id' => '4', 'business_id' => $business->id])}}">@lang('superadmin::lang.enter_variables')</button>
                            </div>
                        </div>
                        </br>
                        <div class="row">
                            <div class="col-md-5">
                                <label class="search_label">@lang('superadmin::lang.number_of_products')</label>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    {!! Form::number('current_values[number_of_products]',
                                    !empty($current_values['number_of_products']) ? $current_values['number_of_products'] :
                                    null, ['class' => 'form-control']) !!}
                                </div>
                            </div>
                            <div class="col-md-4">
                                <button class="btn btn-primary btn-xs btn-modal" data-container=".option_modal"
                                        data-href="{{action('\Modules\Superadmin\Http\Controllers\CompanyPackageVariableController@getOptionVariables', [ 'id' => '2', 'business_id' => $business->id])}}">@lang('superadmin::lang.enter_variables')</button>
                            </div>
                        </div>
                        </br>
                        <div class="row">
                            <div class="col-md-5">
                                <label class="search_label">@lang('superadmin::lang.number_of_periods')</label>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    {!! Form::number('current_values[number_of_periods]',
                                    !empty($current_values['number_of_periods']) ? $current_values['number_of_periods'] : null,
                                    ['class' => 'form-control']) !!}
                                </div>
                            </div>
                            <div class="col-md-4">
                                <button class="btn btn-primary btn-xs btn-modal" data-container=".option_modal"
                                        data-href="{{action('\Modules\Superadmin\Http\Controllers\CompanyPackageVariableController@getOptionVariables', [ 'id' => '3', 'business_id' => $business->id])}}">@lang('superadmin::lang.enter_variables')</button>
                            </div>
                        </div>
                        </br>
                        <div class="row">
                            <div class="col-md-5">
                                <label class="search_label">@lang('superadmin::lang.number_of_stores')</label>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    {!! Form::number('current_values[number_of_stores]',
                                    !empty($current_values['number_of_stores']) ? $current_values['number_of_stores'] : null,
                                    ['class' => 'form-control']) !!}
                                </div>
                            </div>
                            <div class="col-md-4">
                                <button class="btn btn-primary btn-xs btn-modal" data-container=".option_modal"
                                        data-href="{{action('\Modules\Superadmin\Http\Controllers\CompanyPackageVariableController@getOptionVariables', [ 'id' => '5', 'business_id' => $business->id])}}">@lang('superadmin::lang.enter_variables')</button>
                            </div>
                        </div>
                    </div>
                @endcan
            </div>
        </div>
        <input type="hidden" name="opt_vars" id="opt_vars" value="">
        <div class="clearfix"></div>

        <div class="box sms_setting_div">
            <div class="box-body">
                <label class="search_label">@lang('lang_v1.sms_settings') </label>
                <div class="row">
                    <div class="col-md-10">
                        @include('business.partials.settings_sms')
                    </div>
                </div>
            </div>
        </div>
        <div class="clearfix"></div>
        <button type="submit" class="btn btn-danger pull-right"
                id="custom_permission_btn">@lang('superadmin::lang.save')</button>
        <div class="clearfix"></div>
        {!! Form::close() !!}
        <div class="modal fade option_modal" role="dialog" aria-labelledby="gridSystemModalLabel">
        </div>

    </section>
    <!-- /.content -->

@endsection

@section('javascript')
    <script>
        $('#currency_id_manage').select2();
    </script>
    <script>
        $('.select_all').on('ifChecked', function(){
            if($('#annual_fee_package').val() == ''){
                toastr.error('Please enter annual fee package value');
                return;
            }
            $('.ch_select').iCheck('check');
        });
        $('.select_all').on('ifUnchecked', function(){
            $('.ch_select').iCheck('uncheck');
        });

        $('.ch_select').on('ifChecked', function(){
            if($('#annual_fee_package').val() == ''){
                toastr.error('Please enter annual fee package value');
            }
        });

        let  opt_val_array = [];

        $('#custom_permission_btn').click(function(e){
            e.preventDefault();
            if(Array.isArray(opt_val_array) && opt_val_array.length){
                $('#opt_vars').val(JSON.stringify(opt_val_array));
            }
            $('#custom_permission_form').submit();
        });
        @if(empty($manage_module_enable['access_sms_settings']))
        $('.sms_setting_div').addClass('hide');
        @endif

        $('#sms_settings_checkbox').on('ifChecked', function(event){
            $('div.sms_setting_div').removeClass('hide');
        });
        $('#sms_settings_checkbox').on('ifUnchecked', function(event){
            $('div.sms_setting_div').addClass('hide');
        });

        $('#customer_interest_deduct_option').on('ifChecked', function(event){
            $('div.customer_interest_deduct_option').removeClass('hide');
        });
        $('#customer_interest_deduct_option').on('ifUnchecked', function(event){
            $('div.customer_interest_deduct_option').addClass('hide');
        });

        @foreach ($module_permission_locations as $module)
        $('.{{$module}}').on('ifChecked', function(event){
            $('.{{$module}}_locations').removeClass('hide');
        });
        $('.{{$module}}').on('ifUnChecked', function(event){
            $('.{{$module}}_locations').addClass('hide');
        });
        @endforeach
        $(document).ready(function(){
            let sale_date = @if(!empty($sale_import_date)) '{{$sale_import_date}}' @else new Date() @endif;
            $('#sale_import_date').datepicker("setDate", sale_date);

            let purchase_date = @if(!empty($purchase_import_date)) {{$purchase_import_date}} @else new Date() @endif;
            $('#purchase_import_date').datepicker("setDate", purchase_date);

        })
    </script>

@endsection
