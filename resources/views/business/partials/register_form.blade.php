@if(empty($is_admin))
<h3>@lang('business.business')</h3>
@endif
@php
$business_or_entity = App\System::getProperty('business_or_entity');
@endphp
{!! Form::hidden('language', request()->lang); !!}
<style>
    label {
        color: #111 !important;
    }
    .select2-results__option[aria-selected="true"] {
        display: none;
    }
    .equal-column {
        min-height: 95px;
    }
</style>
<fieldset>
    <legend> @if($business_or_entity == 'business') @lang('business.business_details'): @endif @if($business_or_entity
        == 'entity') @lang('lang_v1.entity_details'): @endif</legend>
    <input type="hidden" name="option_variables_selected" class="rm_option_variables_selected">
    <input type="hidden" name="module_selected" class="rm_module_selected">
    <input type="hidden" name="custom_price" class="rm_custom_price">
    {!! Form::hidden('package_id', null, ['class' => 'package_id']); !!}
    <div class="col-md-12">
        <div class="form-group">
            @if($business_or_entity == 'business')
            {!! Form::label('name', __('business.business_name') . ':', ['class' => 'label_register'] ) !!}
            @elseif($business_or_entity == 'entity')
            {!! Form::label('name', __('lang_v1.entity_name') . ':' , ['class' => 'label_register']) !!}
            @else
            {!! Form::label('name', __('business.business_name') . ':' , ['class' => 'label_register']) !!}
            @endif
            <div class="input-group">
                <span class="input-group-addon">
                    <i class="fa fa-suitcase"></i>
                </span>
                @if($business_or_entity == 'business')
                {!! Form::text('name', null, ['class' => 'form-control','placeholder' =>
                __('business.business_name'),'id' => 'b_name', 'required']); !!}
                @elseif($business_or_entity == 'entity')
                {!! Form::text('name', null, ['class' => 'form-control','placeholder' =>
                __('lang_v1.entity_name'),'id' => 'b_name', 'required']); !!}
                @else
                {!! Form::text('name', null, ['class' => 'form-control','placeholder' =>
                __('business.business_name'),'id' => 'b_name', 'required']); !!}
                @endif
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            {!! Form::label('start_date', __('business.start_date') . ':', ['class' => 'label_register']) !!}
            <div class="input-group">
                <span class="input-group-addon">
                    <i class="fa fa-calendar"></i>
                </span>
                {!! Form::text('start_date', null, ['class' => 'form-control start-date-picker','placeholder' =>
                __('business.start_date'), 'readonly']); !!}
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            {!! Form::label('currency_id', __('business.currency') . ':', ['class' => 'label_register']) !!}
            <div class="input-group">
                <span class="input-group-addon">
                    <i class="fa fa-money"></i>
                </span>
                {!! Form::select('currency_id', $currencies, '', ['class' => 'form-control','placeholder' => __('business.currency_placeholder'),'style' => 'width: 100%;', 'required']); !!}
            </div>
        </div>
    </div>
    <div class="clearfix"></div>
    <div class="col-md-6">
        <div class="form-group">
            {!! Form::label('business_logo', __('business.upload_logo') . ':', ['class' => 'label_register']) !!}
            {!! Form::file('business_logo', ['accept' => 'image/*']); !!}
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            {!! Form::label('website', __('lang_v1.website') . ':', ['class' => 'label_register']) !!}
            <div class="input-group">
                <span class="input-group-addon">
                    <i class="fa fa-globe"></i>
                </span>
                {!! Form::text('website', null, ['class' => 'form-control','placeholder' => __('lang_v1.website')]); !!}
            </div>
        </div>
    </div>
    <div class="clearfix"></div>
    <div class="col-md-6">
        <div class="form-group">
            {!! Form::label('mobile', __('lang_v1.business_telephone') . ':', ['class' => 'label_register']) !!}
            <div class="input-group">
                <span class="input-group-addon">
                    <i class="fa fa-phone"></i>
                </span>
                {!! Form::text('mobile', null, ['class' => 'form-control','placeholder' =>
                __('lang_v1.business_telephone'), 'id' => 'b_mobile']); !!}
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            {!! Form::label('alternate_number', __('business.alternate_number') . ':', ['class' => 'label_register']) !!}
            <div class="input-group">
                <span class="input-group-addon">
                    <i class="fa fa-phone"></i>
                </span>
                {!! Form::text('alternate_number', null, ['class' => 'form-control','placeholder' =>
                __('business.alternate_number'), 'id' => 'business_alternate_number']); !!}
            </div>
        </div>
    </div>
    <div class="clearfix"></div>
    <div class="col-md-6">
        <div class="form-group">
            {!! Form::label('country', __('business.country') . ':', ['class' => 'label_register']) !!}
            <div class="input-group">
                <span class="input-group-addon">
                    <i class="fa fa-globe"></i>
                </span>
                {!! Form::text('country', null, ['class' => 'form-control','placeholder' => __('business.country'), 'id' => 'business_country',
                'required']); !!}
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            {!! Form::label('state',__('business.state') . ':', ['class' => 'label_register']) !!}
            <div class="input-group">
                <span class="input-group-addon">
                    <i class="fa fa-map-marker"></i>
                </span>
                {!! Form::text('state', null, ['class' => 'form-control','placeholder' => __('business.state'), 'id' => 'b_state',
                'required']); !!}
            </div>
        </div>
    </div>
    <div class="clearfix"></div>
    <div class="col-md-6">
        <div class="form-group">
            {!! Form::label('city',__('business.city'). ':', ['class' => 'label_register']) !!}
            <div class="input-group">
                <span class="input-group-addon">
                    <i class="fa fa-map-marker"></i>
                </span>
                {!! Form::text('city', null, ['class' => 'form-control','placeholder' => __('business.city'), 'id' => 'business_city',
                'required']); !!}
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            {!! Form::label('zip_code', __('business.zip_code') . ':', ['class' => 'label_register']) !!}
            <div class="input-group">
                <span class="input-group-addon">
                    <i class="fa fa-map-marker"></i>
                </span>
                {!! Form::text('zip_code', null, ['class' => 'form-control','placeholder' =>
                __('business.zip_code_placeholder'), 'id' => 'b_zip_code', 'required']); !!}
            </div>
        </div>
    </div>
    <div class="clearfix"></div>
    <div class="col-md-6">
        <div class="form-group">
            {!! Form::label('landmark', __('business.landmark') . ':', ['class' => 'label_register']) !!}
            <div class="input-group">
                <span class="input-group-addon">
                    <i class="fa fa-map-marker"></i>
                </span>
                {!! Form::text('landmark', null, ['class' => 'form-control','placeholder' => __('business.landmark'),
                'required', 'id' => 'b_landmark']); !!}
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            {!! Form::label('time_zone', __('business.time_zone') . ':', ['class' => 'label_register']) !!}
            <div class="input-group">
                <span class="input-group-addon">
                    <i class="fa fa-clock-o"></i>
                </span>
                {!! Form::select('time_zone', $timezone_list, config('app.timezone'), ['class' => 'form-control','placeholder' => __('business.time_zone'), 'style' => 'width: 100%; margin:0px;',
                'required']); !!}
            </div>
        </div>
    </div>
    @if(in_array('company', $show_referrals_in_register_page ))
    <div class="col-md-6">
        <div class="form-group">
            {!! Form::label('referral_code', __('superadmin::lang.referral_code') . ':', ['class' => 'label_register']) !!} <small>@lang('lang_v1.please_enter_referral_code_if_any')</small>
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
    <div class="col-md-6">
        <div class="form-group" style="margin-top: 32px;">
            @if(request()->segment(1) == 'superadmin')
            <label style="color: black !important;" class="label_register">
                {!! Form::checkbox('show_for_customers', 1, false, ['class' => '', 'id' =>
                'show_for_customers']); !!} @lang('business.show_for_customers')</label>
            @else
            <label  class="label_register">
                {!! Form::checkbox('show_for_customers', 1, false, ['class' => '', 'id' =>
                'show_for_customers']); !!} @lang('business.show_for_customers')</label>
            @endif
        </div>
    </div>
    <div class="col-md-6 business_categories_div hide">
        <div class="form-group">
            {!! Form::label('business_categories', __('business.business_categories') . ':', ['class' => 'label_register']) !!}
            <div class="input-group">
                <span class="input-group-addon">
                    <i class="fa fa-bars"></i>
                </span>
                {!! Form::select('business_categories[]', $business_categories, null, ['class' => 'form-control select2
                business_categories', 'id' => 'business_categories', 'multiple', 'style' => 'width: 100%;
                margin:0px;']);
                !!}
            </div>
        </div>
    </div>
</fieldset>
<!-- tax details -->
@if(empty($is_admin))
@if($business_or_entity == 'business')
<h3>@lang('business.business_settings')</h3>
@elseif($business_or_entity == 'entity')
<h3>@lang('lang_v1.entity_settings')</h3>
@else
<h3>@lang('business.business_settings')</h3>
@endif
<fieldset>
    @if($business_or_entity == 'business')
    <legend>@lang('business.business_settings'):</legend>
    @endif
    @if($business_or_entity == 'entity')
    <legend>@lang('lang_v1.entity_settings'):</legend>
    @endif
    <div class="col-md-6">
        <div class="form-group">
            {!! Form::label('tax_label_1', __('business.tax_1_name') . ':', ['class' => 'label_register']) !!}
            <div class="input-group">
                <span class="input-group-addon">
                    <i class="fa fa-info"></i>
                </span>
                {!! Form::text('tax_label_1', null, ['class' => 'form-control','placeholder' =>
                __('business.tax_1_placeholder')]); !!}
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            {!! Form::label('tax_number_1', __('business.tax_1_no') . ':', ['class' => 'label_register']) !!}
            <div class="input-group">
                <span class="input-group-addon">
                    <i class="fa fa-info"></i>
                </span>
                {!! Form::text('tax_number_1', null, ['class' => 'form-control']); !!}
            </div>
        </div>
    </div>
    <div class="clearfix"></div>
    <div class="col-md-6">
        <div class="form-group">
            {!! Form::label('tax_label_2',__('business.tax_2_name') . ':', ['class' => 'label_register']) !!}
            <div class="input-group">
                <span class="input-group-addon">
                    <i class="fa fa-info"></i>
                </span>
                {!! Form::text('tax_label_2', null, ['class' => 'form-control','placeholder' =>
                __('business.tax_1_placeholder')]); !!}
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            {!! Form::label('tax_number_2',__('business.tax_2_no') . ':', ['class' => 'label_register']) !!}
            <div class="input-group">
                <span class="input-group-addon">
                    <i class="fa fa-info"></i>
                </span>
                {!! Form::text('tax_number_2', null, ['class' => 'form-control',]); !!}
            </div>
        </div>
    </div>
    <div class="clearfix"></div>
    <div class="col-md-6">
        <div class="form-group">
            {!! Form::label('fy_start_month', __('business.fy_start_month') . ':', ['class' => 'label_register']) !!}
            @show_tooltip(__('tooltip.fy_start_month'))
            <div class="input-group">
                <span class="input-group-addon">
                    <i class="fa fa-calendar"></i>
                </span>
                {!! Form::select('fy_start_month', $months, null, ['class' => 'form-control select2',
                'required', 'style' => 'width:100%; margin: 0px;']); !!}
            </div>
        </div>
    </div>
    <div class="col-sm-6">
        <div class="form-group">
            {!! Form::label('accounting_method', __('business.accounting_method') . ':', ['class' => 'label_register']) !!}
            <div class="input-group">
                <span class="input-group-addon">
                    <i class="fa fa-calculator"></i>
                </span>
                {!! Form::select('accounting_method', $accounting_methods, null, ['class' => 'form-control
                select2', 'required', 'style' => 'width:100%; margin:0px;']); !!}
            </div>
        </div>
    </div>
    @if(in_array('company', $show_give_away_gift_in_register_page ))
    <div class="col-md-6">
        <div class="form-group">
            {!! Form::label('give_away_gifts', __('superadmin::lang.give_away_gifts') . ':', ['class' => 'label_register']) !!}
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
</fieldset>
@endif
<!-- Owner Information -->
@if(empty($is_admin))
<h3>@lang('business.owner')</h3>
@endif
<fieldset>
    <legend>@lang('business.owner_info')</legend>
    <div class="col-md-4">
        <div class="form-group">
            {!! Form::label('surname', __('business.prefix') . ':', ['class' => 'label_register']) !!}
            <div class="input-group">
                <span class="input-group-addon">
                    <i class="fa fa-info"></i>
                </span>
                {!! Form::text('surname', null, ['class' => 'form-control','placeholder' =>
                __('business.prefix_placeholder'), 'id' => 'b_surname']); !!}
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group">
            {!! Form::label('first_name', __('business.first_name') . ':', ['class' => 'label_register']) !!}
            <div class="input-group">
                <span class="input-group-addon">
                    <i class="fa fa-info"></i>
                </span>
                {!! Form::text('first_name', null, ['class' => 'form-control','placeholder' =>
                __('business.first_name'), 'required', 'id' => 'b_first_name']); !!}
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group">
            {!! Form::label('last_name', __('business.last_name') . ':', ['class' => 'label_register']) !!}
            <div class="input-group">
                <span class="input-group-addon">
                    <i class="fa fa-info"></i>
                </span>
                {!! Form::text('last_name', null, ['class' => 'form-control','placeholder' =>
                __('business.last_name'), 'id' => 'b_last_name']); !!}
            </div>
        </div>
    </div>
    <div class="clearfix"></div>
    <div class="col-md-6">
        <div class="form-group">
            {!! Form::label('username', __('business.username') . ':', ['class' => 'label_register']) !!}
            <div class="input-group">
                <span class="input-group-addon">
                    <i class="fa fa-user"></i>
                </span>
                {!! Form::text('username', null, ['class' => 'form-control','placeholder' => __('business.username'), 'id' => 'b_username',
                'required']); !!}
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            {!! Form::label('email', __('business.email') . ':', ['class' => 'label_register']) !!}
            <div class="input-group">
                <span class="input-group-addon">
                    <i class="fa fa-envelope"></i>
                </span>
                {!! Form::text('email', null, ['class' => 'form-control','placeholder' => __('business.email'), 'id' => 'b_email']); !!}
            </div>
        </div>
    </div>
    <div class="clearfix"></div>
    <div class="col-md-6">
        <div class="form-group">
            {!! Form::label('password', __('business.password') . ':', ['class' => 'label_register']) !!}
            <div class="input-group">
                <span class="input-group-addon">
                    <i class="fa fa-lock"></i>
                </span>
                {!! Form::password('password', ['class' => 'form-control','placeholder' => __('business.password'), 'id' => 'b_password',
                'style' => 'margin:0px;', 'required']); !!}
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            {!! Form::label('confirm_password', __('business.confirm_password') . ':', ['class' => 'label_register']) !!}
            <div class="input-group">
                <span class="input-group-addon">
                    <i class="fa fa-lock"></i>
                </span>
                {!! Form::password('confirm_password', ['class' => 'form-control','placeholder' =>
                __('business.confirm_password'), 'style' => 'margin:0px;', 'required', 'id' => 'b_confirm_password']); !!}
            </div>
        </div>
    </div>
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
    <div class="clearfix"></div>
	@if(!empty($business_settings->captch_site_key))
	<div class="col-md-12">
    <div class="form-group" style="padding:auto; margin-top:10px;margin-bottom:10px;">
    <div class="g-recaptcha" data-sitekey="{{ $business_settings->captch_site_key }}"></div>
    </div>
	@endif
</fieldset>