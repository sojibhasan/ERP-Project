@extends('layouts.auth-login')
@php
    $settings = DB::table('site_settings')->where('id', 1)->select('*')->first();
    $business_settings = $settings;
    $show_message = json_decode($settings->show_messages);
    if (!empty($show_message->lp_title)) {
        if ($show_message->lp_title == 1) {
            $login_page_title = $settings->login_page_title;
        } else {
            $login_page_title = '';
        }
    }
    if (!empty($show_message->lp_description)) {
        if ($show_message->lp_description == 1) {
            $login_page_description = $settings->login_page_description;
        } else {
            $login_page_description = '';
        }
    } else {
        $login_page_description = '';
    }
    if (!empty($show_message->lp_system_message)) {
        if ($show_message->lp_system_message == 1) {
            $login_page_general_message = $settings->login_page_general_message;
        } else {
            $login_page_general_message = '';
        }
    } else {
        $login_page_general_message = '';
    }
    $bg_showing_type = $settings->background_showing_type;
    $lang_btn = App\System::getProperty('enable_lang_btn_login_page');
    $register_btn = App\System::getProperty('enable_register_btn_login_page');
    $enable_agent_register_btn_login_page = App\System::getProperty('enable_agent_register_btn_login_page');
    $visitor_register_btn = App\System::getProperty('enable_visitor_register_btn_login_page');
    $pricing_btn = App\System::getProperty('enable_pricing_btn_login_page');
    $enable_admin_login = App\System::getProperty('enable_admin_login');
    $enable_member_login = App\System::getProperty('enable_member_login');
    $enable_visitor_login = App\System::getProperty('enable_visitor_login');
    $enable_customer_login = App\System::getProperty('enable_customer_login');
    $enable_agent_login = App\System::getProperty('enable_agent_login');
    $enable_employee_login = App\System::getProperty('enable_employee_login');
    $enable_member_register_btn = App\System::getProperty('enable_member_register_btn_login_page');
    $enable_patient_register_btn = App\System::getProperty('enable_patient_register_btn_login_page');
    $enable_individual_register_btn = App\System::getProperty('enable_individual_register_btn_login_page');
    $enable_welcome_msg = App\System::getProperty('enable_welcome_msg');
    $business_or_entity = App\System::getProperty('business_or_entity');
    $enable_login_banner_image = App\System::getProperty('enable_login_banner_image');
    $login_banner_image = App\System::getProperty('login_banner_image');
    $enable_login_banner_html = App\System::getProperty('enable_login_banner_html');
    $login_banner_html = App\System::getProperty('login_banner_html');
    $array_values = [$lang_btn, $register_btn, $pricing_btn];
    if ($lang_btn == 1 || $register_btn == 1 || $pricing_btn == 1) {
        $frequency = array_count_values($array_values)[1];
    } else {
        $frequency = 0;
    }
    $margin = 0;
    if ($frequency == 3) {
        $margin = -20;
    }
    if ($frequency == 2) {
        $margin = -3;
    }
    if ($frequency == 1) {
        $margin = 11;
    }
    $user_types = [];
    if ($visitor_register_btn) {
        $user_types['visitor_register'] = __('superadmin::lang.visitor_register');
    }
    if ($enable_agent_register_btn_login_page) {
        $user_types['agent_register'] = __('superadmin::lang.agent_register');
    }
    if ($register_btn) {
        $user_types['company_register'] = __('superadmin::lang.company_register');
    }
    if ($enable_customer_login) {
        $user_types['customer_register'] = __('superadmin::lang.customer_register');
    }
    if ($enable_member_register_btn) {
        $user_types['memeber_regsiter'] = __('superadmin::lang.member_register');
    }
    if ($enable_patient_register_btn) {
        $user_types['patient_register'] = __('superadmin::lang.my_health');
    }
    if ($enable_agent_register_btn_login_page) {
        $register_btn = 1;
    }
    $business_categories = App\BusinessCategory::pluck('category_name', 'id');
@endphp
<style>
    .modal {
        overflow: auto !important;
    }
    ul.nav-tabs{
        width: 125%!important;
    }
    .sign-in-outer{
        height: 470px!important;
    }
</style>
@section('content')
@inject('request', 'Illuminate\Http\Request')
<div class="site-wrapper">
    <div class="site-wrapper-inner clearfix">
        <div class="cover-container container">
            @if(env('ALLOW_REGISTRATION', true))
            <div class="row">
                <div class="col-md-8"></div>
                <div class="col-md-4 col-lg-3 col-xs-12 register-true" style="">
                    <div class="row">
                        @if($lang_btn)
                        <div class="col-md-6 col-xs-12">
                            <select class="form-control" id="change_lang">
                                @foreach(config('constants.langs') as $key => $val)
                                <option value="{{$key}}" @if( (empty(request()->lang) && config('app.locale')
                                    ==
                                    $key)
                                    || request()->lang == $key)
                                    selected
                                    @endif
                                    >
                                    {{$val['full_name']}}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        @endif
                        @if($register_btn)
                        <div class="col-md-6 col-xs-12">
                            @if(!($request->segment(1) == 'business' && $request->segment(2) == 'register'))
                            <a style=" margin-top:5px; color: #fff; background: @if(!empty($settings->register_now_btn_bg)) {{$settings->register_now_btn_bg}} @else #0275d8 @endif"
                                data-toggle="modal" data-target="#check_register_type_modal" href=""
                                class="btn btn-block btn-flat">{{ __('business.register') }}</a>
                            @endif
                        </div>
                        @endif
                        @if($pricing_btn)
                        <div class="col-md-6 col-xs-12">
                            @if(Route::has('pricing') && config('app.env') != 'demo' && $request->segment(1) !=
                            'pricing')
                            <a style=" margin-top:5px; color: #fff; background: @if(!empty($settings->pricing_btn_bg)) {{$settings->pricing_btn_bg}} @else #0275d8 @endif"
                                class="btn btn-block btn-flat"
                                href="{{ action('\Modules\Superadmin\Http\Controllers\PricingController@index') }}">@lang('superadmin::lang.pricing')</a>
                            @endif
                        </div>
                        @endif
                        @if($enable_individual_register_btn)
                        <div class="col-md-6 col-xs-12">
                            <a style=" margin-top:5px; color: #fff; background: @if(!empty($settings->self_register_bg)) {{$settings->self_register_bg}} @else #0275d8 @endif"
                                data-toggle="modal" data-target="#self_registration_modal" href=""
                                class="btn btn-block btn-flat">{{ __('lang_v1.self_registration') }}</a>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            @endif
            <div class="inner cover clearfix">
                <div class="row">
                    <div class="col-md-8 intro-cont center">
                        <span>
                            @if(!empty($business->background_showing_type) && !empty($business->background_showing_type)
                            && $business->background_showing_type == 'background_image_and_logo')
                            <img src="{{url('public/uploads/business_logos/' . $business->logo)}}" class="img-rounded"
                                alt="Logo" style="display: block;
                                max-width: 100%;
                                width: @if(!empty($settings->logingLogo_width)) {{$settings->logingLogo_width}}px @else 300px @endif;
                                height: @if(!empty($settings->logingLogo_height)) {{$settings->logingLogo_height}}px @else auto @endif;;
                                margin: -45px auto auto auto;">
                            @elseif(!empty($settings->uploadFileLLogo) && file_exists(public_path().
                            str_replace('public', '', $settings->uploadFileLLogo)))
                            <img src="{{url($settings->uploadFileLLogo)}}" class="img-rounded" alt="Logo" style="display: block;
                                max-width: 100%;
                                width: @if(!empty($settings->logingLogo_width)) {{$settings->logingLogo_width}}px @else 300px @endif;
                                height: @if(!empty($settings->logingLogo_height)) {{$settings->logingLogo_height}}px @else auto @endif;;
                                margin: -45px auto auto auto;">
                            @else
                            {{ config('app.name', 'ultimatePOS') }}
                            @endif
                        </span><br>
                        @if(!empty($login_page_description))
                        <p style="color: #fff;
                            font-family: arial;
                            font-size: 16px;
                            font-weight: 400;">{{$login_page_description}}</p>
                        @endif
                        <div class="clearfix"></div>
                        <div class="col-md-12 center">
                            <div style="width: 468px; height: 60px; margin: auto; ">
                                @if($enable_login_banner_image)
                                <img src="{{url($login_banner_image)}}" alt="">
                                @endif
                                @if($enable_login_banner_html)
                                {!!$login_banner_html!!}
                                @endif
                            </div>
                        </div>
                    </div>
                    <style>
                        .nav-tabs>li.active>a {
                            background: #d14d42 !important;
                        }
                        .nav>li>a:hover {
                            color: #111;
                        }
                    </style>
                    <div class="col-md-4  sign-in-outer">
                        <div class="sign-in-wrap">
                            <p style="color: white; text-align:center;">
                                @if(!empty($login_page_general_message)){{$login_page_general_message}} @endif</p>
                            <div class="form-body">
                                <ul class="nav nav-tabs final-login login-tab">
                                    <li class="active"><a data-toggle="tab"
                                            style=" font-size: 13px !important; background : @if(!empty($settings->admin_login_bg)) {{$settings->admin_login_bg}} @else #0275d8 @endif"
                                            href="#adminLogin">Login</a></li>
                                    @if($enable_agent_login)
                                    <li><a data-toggle="tab"
                                            style=" font-size: 13px !important; background : @if(!empty($settings->agent_login_bg)) {{$settings->agent_login_bg}} @else #0275d8 @endif"
                                            href="#agentLogin">Agent Login</a></li>
                                    @endif
                                    @if($enable_customer_login)
                                    <li><a data-toggle="tab"
                                            style=" font-size: 13px !important; background : @if(!empty($settings->customer_login_bg)) {{$settings->customer_login_bg}} @else #0275d8 @endif"
                                            href="#customerLogin">Customer Login</a></li>
                                    @endif
                                    @if($enable_member_login)
                                    <li><a data-toggle="tab"
                                            style=" font-size: 13px !important; background : @if(!empty($settings->member_login_bg)) {{$settings->member_login_bg}} @else #0275d8 @endif"
                                            href="#memberLogin">Member Login</a></li>
                                    @endif
                                    @if($enable_employee_login)
                                    <li><a data-toggle="tab"
                                            style=" font-size: 13px !important; background : @if(!empty($settings->employee_login_bg)) {{$settings->employee_login_bg}} @else #0275d8 @endif"
                                            href="#employeeLogin">Employee Login</a></li>
                                    @endif
                                    @if($enable_visitor_login)
                                    <li><a data-toggle="tab"
                                            style=" font-size: 13px !important; background : @if(!empty($settings->visitor_login_bg)) {{$settings->visitor_login_bg}} @else #0275d8 @endif"
                                            href="#visitorLogin">Visitor Login</a></li>
                                    @endif
                                </ul>
                                <div class="tab-content login-tab-content">
                                    <div id="adminLogin" class="tab-pane fade in active">
                                        <div class="innter-form">
                                            <form method="POST" action="{{ route('login') }}">
                                                {{ csrf_field() }}
                                                <div
                                                    class="form-group has-feedback {{ $errors->has('username') ? ' has-error' : '' }}">
                                                    @php
                                                    $username = old('username');
                                                    $password = null;
                                                    if(config('app.env') == 'demo'){
                                                    $username = 'admin';
                                                    $password = '123456';
                                                    $demo_types = array(
                                                    'all_in_one' => 'admin',
                                                    'super_market' => 'admin',
                                                    'pharmacy' => 'admin-pharmacy',
                                                    'electronics' => 'admin-electronics',
                                                    'services' => 'admin-services',
                                                    'restaurant' => 'admin-restaurant',
                                                    'superadmin' => 'superadmin',
                                                    'woocommerce' => 'woocommerce_user',
                                                    'essentials' => 'admin-essentials',
                                                    'manufacturing' => 'manufacturer-demo',
                                                    );
                                                    if( !empty($_GET['demo_type']) &&
                                                    array_key_exists($_GET['demo_type'], $demo_types) ){
                                                    $username = $demo_types[$_GET['demo_type']];
                                                    }
                                                    }
                                                    @endphp
                                                    <label style="color: white">@lang('lang_v1.username')</label>
                                                    <input  type="text" class="form-control"
                                                        name="username" value="{{ $username }}" required autofocus
                                                        placeholder="@lang('lang_v1.username')">
                                                    <span class="fa fa-user form-control-feedback"></span>
                                                    @if ($errors->has('username'))
                                                    <span class="help-block">
                                                        <strong>{{ $errors->first('username') }}</strong>
                                                    </span>
                                                    @endif
                                                </div>
                                                <div
                                                    class="form-group has-feedback {{ $errors->has('password') ? ' has-error' : '' }}">
                                                    <label style="color: white">@lang('lang_v1.password')</label>
                                                    <input  type="password" class="form-control"
                                                        name="password" value="{{ $password }}" required
                                                        placeholder="@lang('lang_v1.password')">
                                                    <span class="glyphicon glyphicon-lock form-control-feedback"></span>
                                                    @if ($errors->has('password'))
                                                    <span class="help-block">
                                                        <strong>{{ $errors->first('password') }}</strong>
                                                    </span>
                                                    @endif
                                                </div>
                                                <div class="form-group" style="margin-left: 20px;">
                                                    <div class="checkbox icheck">
                                                        <label>
                                                            <input type="checkbox" name="remember"
                                                                {{ old('remember') ? 'checked' : '' }}>
                                                            @lang('lang_v1.remember_me')
                                                        </label>
                                                    </div>
                                                </div>
												@if(!empty($business_settings->captch_site_key))
	<div class="form-group" >
													 <div class="g-recaptcha" data-sitekey="{{ $business_settings->captch_site_key }}"></div>
												 </div>
	@endif
                                                <div class="form-group">
                                                    <button type="submit"
                                                        class="btn btn-primary btn-flat btn-login">@lang('lang_v1.login')</button>
                                                    @if(config('app.env') != 'demo')
                                                    <a data-toggle="modal" data-target="#password_reset_1" href=""
                                                        class="pull-right">
                                                        @lang('lang_v1.forgot_your_password')
                                                    </a>
                                                    @endif
                                                </div>
                                            </form>
                                        </div>
                                        <div class="clearfix"></div>
                                    </div>
                                    @if($enable_customer_login)
                                    <div id="customerLogin" class="tab-pane fade in">
                                        <div class="innter-form">
                                            <form method="POST"
                                                action="{{ action('Auth\CustomerLoginController@customerLogin') }}">
                                                {{ csrf_field() }}
                                                <div
                                                    class="form-group has-feedback {{ $errors->has('username') ? ' has-error' : '' }}">
                                                    @php
                                                    $username = old('username');
                                                    $password = null;
                                                    if(config('app.env') == 'demo'){
                                                    $username = 'admin';
                                                    $password = '123456';
                                                    $demo_types = array(
                                                    'all_in_one' => 'admin',
                                                    'super_market' => 'admin',
                                                    'pharmacy' => 'admin-pharmacy',
                                                    'electronics' => 'admin-electronics',
                                                    'services' => 'admin-services',
                                                    'restaurant' => 'admin-restaurant',
                                                    'superadmin' => 'superadmin',
                                                    'woocommerce' => 'woocommerce_user',
                                                    'essentials' => 'admin-essentials',
                                                    'manufacturing' => 'manufacturer-demo',
                                                    );
                                                    if( !empty($_GET['demo_type']) &&
                                                    array_key_exists($_GET['demo_type'], $demo_types) ){
                                                    $username = $demo_types[$_GET['demo_type']];
                                                    }
                                                    }
                                                    @endphp
                                                    <label style="color: white">@lang('lang_v1.username')</label>
                                                    <input  type="text" class="form-control"
                                                        name="username" value="{{ $username }}" required autofocus
                                                        placeholder="@lang('lang_v1.username')">
                                                    <span class="fa fa-user form-control-feedback"></span>
                                                    @if ($errors->has('username'))
                                                    <span class="help-block">
                                                        <strong>{{ $errors->first('username') }}</strong>
                                                    </span>
                                                    @endif
                                                </div>
                                                <div
                                                    class="form-group has-feedback {{ $errors->has('password') ? ' has-error' : '' }}">
                                                    <label style="color: white">@lang('lang_v1.password')</label>
                                                    <input  type="password" class="form-control"
                                                        name="password" value="{{ $password }}" required
                                                        placeholder="@lang('lang_v1.password')">
                                                    <span class="glyphicon glyphicon-lock form-control-feedback"></span>
                                                    @if ($errors->has('password'))
                                                    <span class="help-block">
                                                        <strong>{{ $errors->first('password') }}</strong>
                                                    </span>
                                                    @endif
                                                </div>
                                                <div class="form-group" style="margin-left: 20px;">
                                                    <div class="checkbox icheck">
                                                        <label>
                                                            <input type="checkbox" name="remember"
                                                                {{ old('remember') ? 'checked' : '' }}>
                                                            @lang('lang_v1.remember_me')
                                                        </label>
                                                    </div>
                                                </div>
												@if(!empty($business_settings->captch_site_key))
	<div class="form-group" >
													 <div class="g-recaptcha" data-sitekey="{{ $business_settings->captch_site_key }}"></div>
												 </div>
	@endif
                                                <div class="form-group">
                                                    <button type="submit"
                                                        class="btn btn-primary btn-flat btn-login">@lang('lang_v1.login')</button>
                                                    @if(config('app.env') != 'demo')
                                                    <a data-toggle="modal" data-target="#password_reset_2" href=""
                                                        class="pull-right">
                                                        @lang('lang_v1.forgot_your_password')
                                                    </a>
                                                    @endif
                                                </div>
                                            </form>
                                        </div>
                                        <div class="clearfix"></div>
                                    </div>
                                    @endif
                                    @if($enable_member_login)
                                    <div id="memberLogin" class="tab-pane fade in">
                                        <div class="innter-form">
                                            <form method="POST"
                                                action="{{ action('Auth\MemberLoginController@memberLogin') }}">
                                                {{ csrf_field() }}
                                                <div
                                                    class="form-group has-feedback {{ $errors->has('username') ? ' has-error' : '' }}">
                                                    @php
                                                    $username = old('username');
                                                    $password = null;
                                                    @endphp
                                                    <label style="color: white">@lang('lang_v1.username')</label>
                                                    <input  type="text" class="form-control"
                                                        name="username" value="{{ $username }}" required autofocus
                                                        placeholder="@lang('lang_v1.username')">
                                                    <span class="fa fa-user form-control-feedback"></span>
                                                    @if ($errors->has('username'))
                                                    <span class="help-block">
                                                        <strong>{{ $errors->first('username') }}</strong>
                                                    </span>
                                                    @endif
                                                </div>
                                                <div
                                                    class="form-group has-feedback {{ $errors->has('password') ? ' has-error' : '' }}">
                                                    <label style="color: white">@lang('lang_v1.password')</label>
                                                    <input  type="password" class="form-control"
                                                        name="password" value="{{ $password }}" required
                                                        placeholder="@lang('lang_v1.password')">
                                                    <span class="glyphicon glyphicon-lock form-control-feedback"></span>
                                                    @if ($errors->has('password'))
                                                    <span class="help-block">
                                                        <strong>{{ $errors->first('password') }}</strong>
                                                    </span>
                                                    @endif
                                                </div>
                                                <div class="form-group" style="margin-left: 20px;">
                                                    <div class="checkbox icheck">
                                                        <label>
                                                            <input type="checkbox" name="remember"
                                                                {{ old('remember') ? 'checked' : '' }}>
                                                            @lang('lang_v1.remember_me')
                                                        </label>
                                                    </div>
                                                </div>
												@if(!empty($business_settings->captch_site_key))
	<div class="form-group" >
													 <div class="g-recaptcha" data-sitekey="{{ $business_settings->captch_site_key }}"></div>
												 </div>
	@endif
                                                <div class="form-group">
                                                    <button type="submit"
                                                        class="btn btn-primary btn-flat btn-login">@lang('lang_v1.login')</button>
                                                    @if(config('app.env') != 'demo')
                                                    <a data-toggle="modal" data-target="#password_reset_3" href=""
                                                        class="pull-right">
                                                        @lang('lang_v1.forgot_your_password')
                                                    </a>
                                                    @endif
                                                </div>
                                            </form>
                                        </div>
                                        <div class="clearfix"></div>
                                    </div>
                                    @endif
                                    @if($enable_employee_login)
                                    <div id="employeeLogin" class="tab-pane fade in">
                                        <div class="innter-form">
                                            <form method="POST"
                                                action="{{ action('Auth\EmployeeLoginController@employeeLogin') }}">
                                                {{ csrf_field() }}
                                                <div
                                                    class="form-group has-feedback {{ $errors->has('username') ? ' has-error' : '' }}">
                                                    @php
                                                    $username = old('username');
                                                    $password = null;
                                                    @endphp
                                                    <label style="color: white">@lang('lang_v1.username')</label>
                                                    <input  type="text" class="form-control"
                                                        name="username" value="{{ $username }}" required autofocus
                                                        placeholder="@lang('lang_v1.username')">
                                                    <span class="fa fa-user form-control-feedback"></span>
                                                    @if ($errors->has('username'))
                                                    <span class="help-block">
                                                        <strong>{{ $errors->first('username') }}</strong>
                                                    </span>
                                                    @endif
                                                </div>
                                                <div
                                                    class="form-group has-feedback {{ $errors->has('password') ? ' has-error' : '' }}">
                                                    <label style="color: white">@lang('lang_v1.password')</label>
                                                    <input  type="password" class="form-control"
                                                        name="password" value="{{ $password }}" required
                                                        placeholder="@lang('lang_v1.password')">
                                                    <span class="glyphicon glyphicon-lock form-control-feedback"></span>
                                                    @if ($errors->has('password'))
                                                    <span class="help-block">
                                                        <strong>{{ $errors->first('password') }}</strong>
                                                    </span>
                                                    @endif
                                                </div>
                                                <div class="form-group" style="margin-left: 20px;">
                                                    <div class="checkbox icheck">
                                                        <label>
                                                            <input type="checkbox" name="remember"
                                                                {{ old('remember') ? 'checked' : '' }}>
                                                            @lang('lang_v1.remember_me')
                                                        </label>
                                                    </div>
                                                </div>
												@if(!empty($business_settings->captch_site_key))
	<div class="form-group" >
													 <div class="g-recaptcha" data-sitekey="{{ $business_settings->captch_site_key }}"></div>
												 </div>
	@endif
                                                <div class="form-group">
                                                    <button type="submit"
                                                        class="btn btn-primary btn-flat btn-login">@lang('lang_v1.login')</button>
                                                    @if(config('app.env') != 'demo')
                                                    <a data-toggle="modal" data-target="#password_reset_4" href=""
                                                        class="pull-right">
                                                        @lang('lang_v1.forgot_your_password')
                                                    </a>
                                                    @endif
                                                </div>
                                            </form>
                                        </div>
                                        <div class="clearfix"></div>
                                    </div>
                                    @endif
                                    @if($enable_visitor_login)
                                    <div id="visitorLogin" class="tab-pane fade in">
                                        <div class="innter-form">
                                            <form method="POST"
                                                action="{{ action('Auth\VisitorLoginController@visitorLogin') }}">
                                                {{ csrf_field() }}
                                                <div
                                                    class="form-group has-feedback {{ $errors->has('username') ? ' has-error' : '' }}">
                                                    @php
                                                    $username = old('username');
                                                    $password = null;
                                                    @endphp
                                                    <label style="color: white">@lang('lang_v1.username')</label>
                                                    <input  type="text" class="form-control"
                                                        name="username" value="{{ $username }}" required autofocus
                                                        placeholder="@lang('lang_v1.username')">
                                                    <span class="fa fa-user form-control-feedback"></span>
                                                    @if ($errors->has('username'))
                                                    <span class="help-block">
                                                        <strong>{{ $errors->first('username') }}</strong>
                                                    </span>
                                                    @endif
                                                </div>
                                                <div
                                                    class="form-group has-feedback {{ $errors->has('password') ? ' has-error' : '' }}">
                                                    <label style="color: white">@lang('lang_v1.password')</label>
                                                    <input  type="password" class="form-control"
                                                        name="password" value="{{ $password }}" required
                                                        placeholder="@lang('lang_v1.password')">
                                                    <span class="glyphicon glyphicon-lock form-control-feedback"></span>
                                                    @if ($errors->has('password'))
                                                    <span class="help-block">
                                                        <strong>{{ $errors->first('password') }}</strong>
                                                    </span>
                                                    @endif
                                                </div>
                                                <div class="form-group" style="margin-left: 20px;">
                                                    <div class="checkbox icheck">
                                                        <label>
                                                            <input type="checkbox" name="remember"
                                                                {{ old('remember') ? 'checked' : '' }}>
                                                            @lang('lang_v1.remember_me')
                                                        </label>
                                                    </div>
                                                </div>
												@if(!empty($business_settings->captch_site_key))
	<div class="form-group" >
													 <div class="g-recaptcha" data-sitekey="{{ $business_settings->captch_site_key }}"></div>
												 </div>
	@endif
                                                <div class="form-group">
                                                    <button type="submit"
                                                        class="btn btn-primary btn-flat btn-login">@lang('lang_v1.login')</button>
                                                    @if(config('app.env') != 'demo')
                                                    <a data-toggle="modal" data-target="#password_reset_4" href=""
                                                        class="pull-right">
                                                        @lang('lang_v1.forgot_your_password')
                                                    </a>
                                                    @endif
                                                </div>
                                            </form>
                                        </div>
                                        <div class="clearfix"></div>
                                    </div>
                                    @endif
                                    @if($enable_agent_login)
                                    <div id="agentLogin" class="tab-pane fade in">
                                        <div class="innter-form">
                                            <form method="POST"
                                                action="{{ action('Auth\AgentLoginController@agentLogin') }}">
                                                {{ csrf_field() }}
                                                <div
                                                    class="form-group has-feedback {{ $errors->has('username') ? ' has-error' : '' }}">
                                                    @php
                                                    $username = old('username');
                                                    $password = null;
                                                    if(config('app.env') == 'demo'){
                                                    $username = 'admin';
                                                    $password = '123456';
                                                    $demo_types = array(
                                                    'all_in_one' => 'admin',
                                                    'super_market' => 'admin',
                                                    'pharmacy' => 'admin-pharmacy',
                                                    'electronics' => 'admin-electronics',
                                                    'services' => 'admin-services',
                                                    'restaurant' => 'admin-restaurant',
                                                    'superadmin' => 'superadmin',
                                                    'woocommerce' => 'woocommerce_user',
                                                    'essentials' => 'admin-essentials',
                                                    'manufacturing' => 'manufacturer-demo',
                                                    );
                                                    if( !empty($_GET['demo_type']) &&
                                                    array_key_exists($_GET['demo_type'], $demo_types) ){
                                                    $username = $demo_types[$_GET['demo_type']];
                                                    }
                                                    }
                                                    @endphp
                                                    <label style="color: white">@lang('lang_v1.username')</label>
                                                    <input  type="text" class="form-control"
                                                        name="username" value="{{ $username }}" required autofocus
                                                        placeholder="@lang('lang_v1.username')">
                                                    <span class="fa fa-user form-control-feedback"></span>
                                                    @if ($errors->has('username'))
                                                    <span class="help-block">
                                                        <strong>{{ $errors->first('username') }}</strong>
                                                    </span>
                                                    @endif
                                                </div>
                                                <div
                                                    class="form-group has-feedback {{ $errors->has('password') ? ' has-error' : '' }}">
                                                    <label style="color: white">@lang('lang_v1.password')</label>
                                                    <input  type="password" class="form-control"
                                                        name="password" value="{{ $password }}" required
                                                        placeholder="@lang('lang_v1.password')">
                                                    <span class="glyphicon glyphicon-lock form-control-feedback"></span>
                                                    @if ($errors->has('password'))
                                                    <span class="help-block">
                                                        <strong>{{ $errors->first('password') }}</strong>
                                                    </span>
                                                    @endif
                                                </div>
                                                <div class="form-group" style="margin-left: 20px;">
                                                    <div class="checkbox icheck">
                                                        <label>
                                                            <input type="checkbox" name="remember"
                                                                {{ old('remember') ? 'checked' : '' }}>
                                                            @lang('lang_v1.remember_me')
                                                        </label>
                                                    </div>
                                                </div>
												@if(!empty($business_settings->captch_site_key))
	<div class="form-group" >
													 <div class="g-recaptcha" data-sitekey="{{ $business_settings->captch_site_key }}"></div>
												 </div>
	@endif
                                                <div class="form-group">
                                                    <button type="submit"
                                                        class="btn btn-primary btn-flat btn-login">@lang('lang_v1.login')</button>
                                                    @if(config('app.env') != 'demo')
                                                    <a data-toggle="modal" data-target="#password_reset_5" href=""
                                                        class="pull-right">
                                                        @lang('lang_v1.forgot_your_password')
                                                    </a>
                                                    @endif
                                                </div>
                                            </form>
                                        </div>
                                        <div class="clearfix"></div>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="check_register_type_modal" role="dialog" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <div class="col-md-2"></div>
                <div class="col-md-8">
                    {!! Form::label('check_register_type', 'Plesae select the register type', ['style' => 'color: black
                    !important;']) !!}
                    {!! Form::select('check_register_type', $user_types, null, ['class' => 'form-control',
                    'style' => 'width: 100%;', 'id' => 'check_register_type', 'placeholder' =>
                    __('lang_v1.please_select')]) !!}
                </div>
                <div class="col-md-2"></div>
            </div>
            <hr>
            <div class="clearfix"></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="visitor_register_modal" role="dialog" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" style="width: 55%;" role="document">
        <div class="modal-content">
            {!! Form::open(['url' => route('business.postVisitorRegister'), 'method' => 'post',
            'id' => 'visitor_register_form','files' => true ]) !!}
            <div class="modal-body">
                <p class="form-header">@lang('business.register_and_get_started_in_minutes')</p>
                @include('business.partials.register_form_visitor')
				<p>
				</p>
            </div>
            <hr>
            <div class="clearfix"></div>
            <div class="modal-footer">
                <button type="submit" id="visitor_form_btn" class="btn btn-primary">Submit</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
            {!! Form::close() !!}
        </div>
    </div>
</div>
<!-- Modal -->
<div class="modal fade" id="register_modal" role="dialog">
    <div class="modal-dialog" style="width: 55%;" role="document" id="register_modal_dialog">
        <div class="modal-content">
            <div class="modal-body">
                <p class="form-header">@lang('business.register_and_get_started_in_minutes')</p>
                {!! Form::open(['url' => route('business.postRegister'), 'method' => 'post',
                'id' => 'business_register_form','files' => true ]) !!}
                @include('business.partials.register_form')
                {!! Form::hidden('package_id', $package_id); !!}
                {!! Form::close() !!}
            </div>
        </div>
    </div>
</div>
<!-- Modal -->
<div class="modal fade" id="member_register_modal" role="dialog">
    <div class="modal-dialog" style="width: 55%;" role="document" id="member_register_modal_dialog">
        <div class="modal-content">
            <div class="modal-body">
                <p class="form-header">@lang('business.member_registration')</p>
                {!! Form::open(['url' => route('business.member_register'), 'method' => 'post',
                'id' => 'member_register_form','files' => true ]) !!}
                @include('business.partials.member_register')
                {!! Form::close() !!}
            </div>
        </div>
    </div>
</div>
<!-- Modal -->
<div class="modal fade" id="patient_register_modal" role="dialog" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" style="width: 55%;" role="document">
        <div class="modal-content">
            <div class="modal-body text-left">
                <h2 class="form-header">@lang('business.my_health_register')</h2>
                {!! Form::open(['url' => route('business.postPatientRegister'), 'method' => 'post',
                'id' => 'patient_register_form','files' => true ]) !!}
                @include('business.partials.register_form_patient')
                {!! Form::hidden('package_id', $package_id, ['class' => 'package_id']); !!}
                {!! Form::close() !!}
            </div>
        </div>
    </div>
</div>
<!-- Modal -->
<div class="modal fade" id="agent_register_modal" role="dialog" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" style="width: 55%;" role="document">
        <div class="modal-content">
            <div class="modal-body text-left">
                <h2 class="form-header">@lang('superadmin::lang.agent_registration')</h2>
                {!! Form::open(['url' => route('business.postAgentRegister'), 'method' => 'post',
                'id' => 'agent_register_form','files' => true ]) !!}
                @include('business.partials.register_form_agent')
                {!! Form::hidden('package_id', $package_id, ['class' => 'package_id']); !!}
                {!! Form::close() !!}
            </div>
        </div>
    </div>
</div>
<!-- Modal -->
<div class="modal fade" id="self_registration_modal" role="dialog" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document" id="self_registration_dialog" style="width: 55%;">
        <div class="modal-content">
            <div class="modal-body">
                <p class="form-header">@lang('lang_v1.self_registration')</p>
                {{-- this route define in web.php --}}
                {!! Form::open(['url' => '/visitor/register', 'method' => 'post',
                'id' => 'self_registration_form','files' => true ]) !!}
                @include('visitor::visitor_registration.self_registration')
                {!! Form::close() !!}
            </div>
        </div>
    </div>
</div>
<!-- Modal -->
<div class="modal fade" id="customer_register_modal" role="dialog" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document" id="customer_register_modal_dialog" style="width: 50%">
        <div class="modal-content text-left">
            <div class="modal-body">
                <p class="form-header">@lang('business.register_and_get_started_in_minutes')</p>
                {!! Form::open(['url' => route('business.customer_register'), 'method' => 'post',
                'id' => 'customer_register_form','files' => true ]) !!}
                @include('business.partials.customer_register')
                {!! Form::hidden('package_id', $package_id); !!}
                {!! Form::close() !!}
            </div>
        </div>
    </div>
</div>
<!-- Modal -->
<div class="modal fade" id="password_reset_1" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document" style="width: 36%;margin-left: 32%;">
        <div class="modal-content">
            <div class="modal-body" style="padding: 0px;">
                <div class="login-form col-md-12 right-col-content" style="padding-top: 100px; padding-bottom: 100px;">
                    <form method="POST" action="{{ route('password.email') }}">
                        {{ csrf_field() }}
                        <div class="form-group has-feedback {{ $errors->has('email') ? ' has-error' : '' }}"
                            style="text-align:center;">
                            <label for="">Please enter the Email</label>
                            <input id="reset_1_email" type="email" class="form-control" name="email" value="{{ old('email') }}"
                                required autofocus placeholder="@lang('lang_v1.email_address')">
                            <span class="fa fa-envelope form-control-feedback"></span>
                            @if ($errors->has('email'))
                            <span class="help-block">
                                <strong>{{ $errors->first('email') }}</strong>
                            </span>
                            @endif
                        </div>
                        <br>
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary btn-block btn-flat">
                                @lang('lang_v1.send_password_reset_link')
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Modal -->
<div class="modal fade" id="password_reset_3" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document" style="width: 36%;margin-left: 32%;">
        <div class="modal-content">
            <div class="modal-body" style="padding: 0px;">
                <div class="login-form col-md-12 right-col-content" style="padding-top: 100px; padding-bottom: 100px;">
                    <form method="POST" action="{{ route('member_password.email') }}">
                        {{ csrf_field() }}
                        <div class="form-group has-feedback {{ $errors->has('email') ? ' has-error' : '' }}"
                            style="text-align:center;">
                            <label for="">Please enter the Email</label>
                            <input id="reset_3_email" type="email" class="form-control" name="email" value="{{ old('email') }}"
                                required autofocus placeholder="@lang('lang_v1.email_address')">
                            <span class="fa fa-envelope form-control-feedback"></span>
                            @if ($errors->has('email'))
                            <span class="help-block">
                                <strong>{{ $errors->first('email') }}</strong>
                            </span>
                            @endif
                        </div>
                        <br>
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary btn-block btn-flat">
                                @lang('lang_v1.send_password_reset_link')
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Modal -->
<div class="modal fade" id="password_reset_4" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document" style="width: 36%;margin-left: 32%;">
        <div class="modal-content">
            <div class="modal-body" style="padding: 0px;">
                <div class="login-form col-md-12 right-col-content" style="padding-top: 100px; padding-bottom: 100px;">
                    <form method="POST" action="{{ route('employee_password.email') }}">
                        {{ csrf_field() }}
                        <div class="form-group has-feedback {{ $errors->has('email') ? ' has-error' : '' }}"
                            style="text-align:center;">
                            <label for="">Please enter the Email</label>
                            <input id="reset_4_email" type="email" class="form-control" name="email" value="{{ old('email') }}"
                                required autofocus placeholder="@lang('lang_v1.email_address')">
                            <span class="fa fa-envelope form-control-feedback"></span>
                            @if ($errors->has('email'))
                            <span class="help-block">
                                <strong>{{ $errors->first('email') }}</strong>
                            </span>
                            @endif
                        </div>
                        <br>
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary btn-block btn-flat">
                                @lang('lang_v1.send_password_reset_link')
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Modal -->
<div class="modal fade" id="5" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document" style="width: 36%;margin-left: 32%;">
        <div class="modal-content">
            <div class="modal-body" style="padding: 0px;">
                <div class="login-form col-md-12 right-col-content" style="padding-top: 100px; padding-bottom: 100px;">
                    <form method="POST" action="{{ route('customer_password.email') }}">
                        {{ csrf_field() }}
                        <div class="form-group has-feedback {{ $errors->has('email') ? ' has-error' : '' }}"
                            style="text-align:center;">
                            <label for="">Please enter the Email</label>
                            <input id="reset_5_email" type="email" class="form-control" name="email" value="{{ old('email') }}"
                                required autofocus placeholder="@lang('lang_v1.email_address')">
                            <span class="fa fa-envelope form-control-feedback"></span>
                            @if ($errors->has('email'))
                            <span class="help-block">
                                <strong>{{ $errors->first('email') }}</strong>
                            </span>
                            @endif
                        </div>
                        <br>
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary btn-block btn-flat">
                                @lang('lang_v1.send_password_reset_link')
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Modal -->
<div class="modal fade" id="password_reset_5" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document" style="width: 36%;margin-left: 32%;">
        <div class="modal-content">
            <div class="modal-body" style="padding: 0px;">
                <div class="login-form col-md-12 right-col-content" style="padding-top: 100px; padding-bottom: 100px;">
                    <form method="POST" action="{{ route('customer_password.email') }}">
                        {{ csrf_field() }}
                        <div class="form-group has-feedback {{ $errors->has('email') ? ' has-error' : '' }}"
                            style="text-align:center;">
                            <label for="">Please enter the Email</label>
                            <input id="email" type="email" class="form-control" name="email" value="{{ old('email') }}"
                                required autofocus placeholder="@lang('lang_v1.email_address')">
                            <span class="fa fa-envelope form-control-feedback"></span>
                            @if ($errors->has('email'))
                            <span class="help-block">
                                <strong>{{ $errors->first('email') }}</strong>
                            </span>
                            @endif
                        </div>
                        <br>
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary btn-block btn-flat">
                                @lang('lang_v1.send_password_reset_link')
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade view_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel"></div>
<div class="modal" tabindex="-1" role="dialog" id="register_success_modal">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-body text-center">
                <i class="fa fa-check fa-lg"
                    style="font-size: 50px; margin-top: 20px; border: 1px solid #4BB543; color: #4BB543; padding:15px 10px 15px 10px; border-radius: 50%;"></i>
                <h2>{!!session('register_success.title')!!}</h2>
                <div class="clearfix"></div>
                <div class="col-md-12">
                    {!!session('register_success.msg')!!}
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<script src="https://www.google.com/recaptcha/api.js" async defer></script>
@stop
@section('javascript')
<script type="text/javascript">
    $(document).ready(function(){
        $('#currency_id').select2();
        $('#time_zone').select2();
        @if (session('register_success'))
            $('#register_success_modal').modal('show');
        @endif
        $('#change_lang').change( function(){
            window.location = "{{ route('login') }}?lang=" + $(this).val();
        });
        $(document).on('change', '#check_register_type',function(){
            register_type = $(this).val();
            if(register_type == 'visitor_register'){
                $('#visitor_register_modal').modal('show');
            }
            if(register_type == 'customer_register'){
                $('#customer_register_modal').modal('show');
            }
            if(register_type == 'memeber_regsiter'){
                $('#member_register_modal').modal('show');
            }
            if(register_type == 'patient_register'){
                $('#patient_register_modal').modal('show');
            }
            if(register_type == 'company_register'){
                $('#register_modal').modal('show');
            }
            if(register_type == 'agent_register'){
                $('#agent_register_modal').modal('show');
            }
            $('#check_register_type_modal').modal('hide');
        })
    })
    $('#show_for_customers').on('change', function(event){
            $('.business_categories_div').removeClass('hide');
        });
    $('#business_categories').select2();
    $('#customer_business_category').change(function(){
      $.ajax({
          method: 'get',
          url: "{{action('BusinessController@getBusinessByCategory')}}",
          data: { category : $(this).val() },
          dataType : "html",
          success: function(result) {
             $('.customer_business_id_div').removeClass('hide');
             $('#customer_business_id').empty().append(result);
          },
      });
    });
    $('.start-date-picker').datepicker({
        autoclose: true,
        endDate: 'today',
    });
</script>
<script>
    $('#date_of_birth').datepicker();
</script>
<script>
    $('body').on('click', '.btn-submit', function(event) {
      event.preventDefault();
      let form = $(this).closest('form');
      if(form.attr('id') == 'district_form'){
        let name = form.find('#name').val();
        let business_id = 1;
        $('.view_modal').modal('hide');
        $.ajax({
          method: 'POST',
          url: "{{ url('default-district') }}",
          data: { name, business_id },
          success: function(result) {
            if(result.success){
              toastr.success(result.msg)
            }else{
              toastr.error(result.msg)
            }
            getDistricts();
          },
        });
      }
      if(form.attr('id') == 'town_form'){
        let name = form.find('#name').val();
        let district_id = form.find('#district_id').val();
        let business_id = 1;
        $('.view_modal').modal('hide');
        $.ajax({
          method: 'POST',
          url: "{{ url('default-town') }}",
          data: { name, business_id, district_id },
          success: function(result) {
            if(result.success){
              toastr.success(result.msg)
            }else{
              toastr.error(result.msg)
            }
            $('#district_id').trigger('change');
          },
        });
      }
    });
    function getDistricts(){
      $.ajax({
        url: '{{ url('default-district/get-drop-down') }}',
        type: 'GET',
        dataType: 'html',
        data: {},
      })
      .done(function(response) {
        $('#district_id').empty();
        $('#district_id').html(response);
      });
    }
    $(document).ready(function(){
        $('.login-tab-content div').eq(0).addClass('active');
        $('.login-tab li').eq(0).addClass('active');
    })
</script>
@endsection