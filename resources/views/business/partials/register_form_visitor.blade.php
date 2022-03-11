@if(empty($is_admin))
<h3>@lang('business.business')</h3>
@endif
{!! Form::hidden('language', request()->lang); !!}
@php
$settings = DB::table('site_settings')->where('id', 1)->select('*')->first();
$login_background_color = $settings->login_background_color;
@endphp
<style>
    .wizard>.content {
        background: {
                {
                $login_background_color
            }
        }
         !important;
    }
    label {
        text-align: left;
        color: white !important;
    }
    .select2-results__option[aria-selected="true"] {
        display: none;
    }
    .equal-column {
        min-height: 95px;
    }
</style>
<div class="col-md-12" style="background: {{$login_background_color}}">
    <input type="hidden" name="option_variables_selected" class="rm_option_variables_selected">
    <input type="hidden" name="module_selected" class="rm_module_selected">
    <input type="hidden" name="custom_price" class="rm_custom_price">
    {!! Form::hidden('package_id', null, ['class' => 'package_id']); !!}
    <div class="col-md-12">
        <div class="form-group">
            {!! Form::label('name', __('business.business_name') . ':' ) !!}
            <div class="input-group">
                <span class="input-group-addon">
                    <i class="fa fa-suitcase"></i>
                </span>
                {!! Form::text('name', null, ['class' => 'form-control','placeholder' => __('business.business_name'),
                'required', 'id' => 'v_name']); !!}
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            {!! Form::label('mobile', __('lang_v1.business_telephone') . ':') !!}
            <div class="input-group">
                <span class="input-group-addon">
                    <i class="fa fa-phone"></i>
                </span>
                {!! Form::text('mobile', null, ['class' => 'form-control','placeholder' =>
                __('lang_v1.business_telephone')]); !!}
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            {!! Form::label('alternate_number', __('business.alternate_number') . ':') !!}
            <div class="input-group">
                <span class="input-group-addon">
                    <i class="fa fa-phone"></i>
                </span>
                {!! Form::text('alternate_number', null, ['class' => 'form-control','placeholder' =>
                __('business.alternate_number')]); !!}
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            {!! Form::label('country', __('business.country') . ':') !!}
            <div class="input-group">
                <span class="input-group-addon">
                    <i class="fa fa-globe"></i>
                </span>
                {!! Form::text('country', null, ['class' => 'form-control','placeholder' => __('business.country'),
                'required']); !!}
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            {!! Form::label('state',__('business.state') . ':') !!}
            <div class="input-group">
                <span class="input-group-addon">
                    <i class="fa fa-map-marker"></i>
                </span>
                {!! Form::text('state', null, ['class' => 'form-control','placeholder' => __('business.state'),
                'required']); !!}
            </div>
        </div>
    </div>
    <div class="clearfix"></div>
    <div class="col-md-6">
        <div class="form-group">
            {!! Form::label('city',__('business.city'). ':') !!}
            <div class="input-group">
                <span class="input-group-addon">
                    <i class="fa fa-map-marker"></i>
                </span>
                {!! Form::text('city', null, ['class' => 'form-control','placeholder' => __('business.city'),
                'required']); !!}
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            {!! Form::label('zip_code', __('business.zip_code') . ':') !!}
            <div class="input-group">
                <span class="input-group-addon">
                    <i class="fa fa-map-marker"></i>
                </span>
                {!! Form::text('zip_code', null, ['class' => 'form-control','placeholder' =>
                __('business.zip_code_placeholder'), 'required']); !!}
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            {!! Form::label('landmark', __('business.landmark') . ':') !!}
            <div class="input-group">
                <span class="input-group-addon">
                    <i class="fa fa-map-marker"></i>
                </span>
                {!! Form::text('landmark', null, ['class' => 'form-control','placeholder' => __('business.landmark'),
                'required']); !!}
            </div>
        </div>
    </div>
    <div class="clearfix"></div>
    <div class="col-md-4">
        <div class="form-group">
            {!! Form::label('surname', __('business.prefix') . ':') !!}
            <div class="input-group">
                <span class="input-group-addon">
                    <i class="fa fa-info"></i>
                </span>
                {!! Form::text('surname', null, ['class' => 'form-control','placeholder' =>
                __('business.prefix_placeholder')]); !!}
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group">
            {!! Form::label('first_name', __('business.first_name') . ':') !!}
            <div class="input-group">
                <span class="input-group-addon">
                    <i class="fa fa-info"></i>
                </span>
                {!! Form::text('first_name', null, ['class' => 'form-control','placeholder' =>
                __('business.first_name'), 'required']); !!}
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group">
            {!! Form::label('last_name', __('business.last_name') . ':') !!}
            <div class="input-group">
                <span class="input-group-addon">
                    <i class="fa fa-info"></i>
                </span>
                {!! Form::text('last_name', null, ['class' => 'form-control','placeholder' =>
                __('business.last_name')]); !!}
            </div>
        </div>
    </div>
    <div class="clearfix"></div>
    <div class="col-md-6">
        <div class="form-group">
            {!! Form::label('username', __('business.username') . ':') !!}
            <div class="input-group">
                <span class="input-group-addon">
                    <i class="fa fa-user"></i>
                </span>
                {!! Form::text('username', null, ['class' => 'form-control','placeholder' => __('business.username'),
                'id' => 'visitor_username',
                'required']); !!}
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            {!! Form::label('email', __('business.email') . ':') !!}
            <div class="input-group">
                <span class="input-group-addon">
                    <i class="fa fa-envelope"></i>
                </span>
                {!! Form::text('email', null, ['class' => 'form-control', 'id' => 'visitor_email', 'placeholder' =>
                __('business.email')]); !!}
            </div>
        </div>
    </div>
    <div class="clearfix"></div>
    <div class="col-md-6">
        <div class="form-group">
            {!! Form::label('password', __('business.password') . ':') !!}
            <div class="input-group">
                <span class="input-group-addon">
                    <i class="fa fa-lock"></i>
                </span>
                {!! Form::password('password', ['class' => 'form-control','placeholder' => __('business.password'), 'id'
                => 'visitor_password',
                'style' => 'margin:0px;', 'required']); !!}
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            {!! Form::label('confirm_password', __('business.confirm_password') . ':') !!}
            <div class="input-group">
                <span class="input-group-addon">
                    <i class="fa fa-lock"></i>
                </span>
                {!! Form::password('confirm_password', ['class' => 'form-control','placeholder' =>
                __('business.confirm_password'), 'style' => 'margin:0px;', 'required', 'id' =>
                'visitor_confirm_password']); !!}
            </div>
        </div>
    </div>
    @if(in_array('visitor', $show_referrals_in_register_page ))
    <div class="col-md-6">
        <div class="form-group">
            {!! Form::label('referral_code', __('superadmin::lang.referral_code') . ':') !!} <small>@lang('lang_v1.please_enter_referral_code_if_any')</small>
            <div class="input-group">
                <span class="input-group-addon">
                    <i class="fa fa-link"></i>
                </span>
                {!! Form::text('referral_code', 0, ['class' => 'form-control','placeholder' =>
                __('superadmin::lang.referral_code'), 'style' => 'width: 100%;',
                ]); !!}
            </div>
        </div>
    </div>
    @endif
    @if(in_array('visitor', $show_give_away_gift_in_register_page ))
    <div class="col-md-6">
        <div class="form-group">
            {!! Form::label('give_away_gifts', __('superadmin::lang.give_away_gifts') . ':') !!}
            @foreach ($give_away_gifts as $key => $give_away_gift)
            <div class="checkbox">
                <label>
                    {!! Form::checkbox('give_away_gifts[]', $key, false, ['class' => '']);
                    !!} {{$give_away_gift}}
                </label>
            </div>
            @endforeach
        </div>
    </div>
    @endif
    <div class="clearfix"></div>
    <div class="col-md-6">
        @if(!empty($system_settings['superadmin_enable_register_tc']))
        <div class="checkbox">
            {!! Form::checkbox('accept_tc', 0, false, ['required']); !!}
            <label>
                <a class="terms_condition" data-toggle="modal" data-target="#tc_modal">
                    @lang('lang_v1.accept_terms_and_conditions')
                </a>
            </label>
        </div>
        @include('business.partials.terms_conditions')
        @endif
    </div>
	@if(!empty($business_settings->captch_site_key))
        <div class="col-md-12">
        <div class="form-group" style="padding:auto; margin-top:10px;margin-bottom:10px;">
        <div class="g-recaptcha" data-sitekey="{{ $business_settings->captch_site_key }}"></div>
        </div>
	@endif
</div>