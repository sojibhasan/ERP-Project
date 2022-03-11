@php
$settings = DB::table('site_settings')->where('id', 1)->select('*')->first();
$login_background_color = $settings->login_background_color;
@endphp
<style>
    #patient_register {
        background: {
                {
                $login_background_color
            }
        }
         !important;
    }
    label {
        color: white !important;
    }
</style>
<fieldset>
    <div id="patient_register" style="border-radius: 5px;  background: #445867 !important;">
        <style>
            label {
                color: black;
                font-weight: 800;
            }
        </style>
        <div class="modal-body">
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('first_name', __('customer.first_name') . ':*') !!}
                        <div class="input-group">
                            <span class="input-group-addon">
                                <i class="fa fa-user"></i>
                            </span>
                            {!! Form::text('first_name', null, ['class' => 'form-control','placeholder' =>
                            __('customer.first_name'), 'id' => 'c_first_name',
                            'required']); !!}
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('last_name', __('customer.last_name') . ':*') !!}
                        <div class="input-group">
                            <span class="input-group-addon">
                                <i class="fa fa-user"></i>
                            </span>
                            {!! Form::text('last_name', null, ['class' => 'form-control','placeholder' =>
                            __('customer.last_name'), 'id' => 'c_last_name',
                            'required']); !!}
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('username', __('customer.username') . ':*') !!}
                        <div class="input-group">
                            <span class="input-group-addon">
                                <i class="fa fa-user"></i>
                            </span>
                            {!! Form::text('username', null, ['class' => 'form-control','placeholder' =>
                            __('customer.username'), 'id' => 'c_username', 
                            'required']); !!}
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('email', __('business.email') . ':') !!}
                        <div class="input-group">
                            <span class="input-group-addon">
                                <i class="fa fa-envelope"></i>
                            </span>
                            {!! Form::email('email', null, ['class' => 'form-control','placeholder' =>
                            __('business.email'), 'id' => 'c_email']); !!}
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('password', __('business.password') . ':*') !!}
                        <div class="input-group">
                            <span class="input-group-addon">
                                <i class="fa fa-key"></i>
                            </span>
                            {!! Form::password('password', ['class' => 'form-control', 'id' => 'c_password', 'style' =>
                            'margin: 0px;','placeholder'
                            => __('business.password')]); !!}
                        </div>
                        <p class="help-block" style="color: white;">At least 6 character.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('confirm_password', __('business.confirm_password') . ':*') !!}
                        <div class="input-group">
                            <span class="input-group-addon">
                                <i class="fa fa-key"></i>,
                            </span>
                            {!! Form::password('confirm_password', ['class' => 'form-control', 'id' =>
                            'c_confirm_password', 'style' => 'margin: 0px;', 'placeholder' =>
                            __('business.confirm_password')]); !!}
                        </div>
                    </div>
                </div>
                <div class="col-md-12">
                    <hr>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('mobile', __('contact.mobile') . ':*') !!}
                        <div class="input-group">
                            <span class="input-group-addon">
                                <i class="fa fa-mobile"></i>
                            </span>
                            {!! Form::text('mobile', null, ['class' => 'form-control', 'required', 'placeholder' =>
                            __('contact.mobile'), 'id' => 'c_mobile']); !!}
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('alternate_number', __('contact.alternate_contact_number') . ':') !!}
                        <div class="input-group">
                            <span class="input-group-addon">
                                <i class="fa fa-phone"></i>
                            </span>
                            {!! Form::text('contact_number', null, ['class' => 'form-control', 'placeholder' =>
                            __('contact.alternate_contact_number'), 'alternate_number' => 'customer_alternate_number']); !!}
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('landline', __('contact.landline') . ':') !!}
                        <div class="input-group">
                            <span class="input-group-addon">
                                <i class="fa fa-phone"></i>
                            </span>
                            {!! Form::text('landline', null, ['class' => 'form-control', 'placeholder' =>
                            __('contact.landline')]); !!}
                        </div>
                    </div>
                </div>
                <div class="clearfix"></div>
                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('geo_location', __('customer.geo_location') . ':') !!}
                        <div class="input-group">
                            <span class="input-group-addon">
                                <i class="fa fa-map-marker"></i>
                            </span>
                            {!! Form::text('geo_location', null, ['class' => 'form-control', 'placeholder' =>
                            __('contact.geo_location')]); !!}
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('address', __('business.address') . ':') !!}
                        <div class="input-group">
                            <span class="input-group-addon">
                                <i class="fa fa-map-marker"></i>
                            </span>
                            {!! Form::text('address', null, ['class' => 'form-control', 'required', 'placeholder' =>
                            __('business.address'), 'id' => 'customer_address']); !!}
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('town', __('customer.town') . ':') !!}
                        <div class="input-group">
                            <span class="input-group-addon">
                                <i class="fa fa-map-marker"></i>
                            </span>
                            {!! Form::text('town', null, ['class' => 'form-control', 'required', 'placeholder' =>
                            __('customer.town')]); !!}
                        </div>
                    </div>
                </div>
                <div class="clearfix"></div>
                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('district', __('customer.district') . ':') !!}
                        <div class="input-group">
                            <span class="input-group-addon">
                                <i class="fa fa-map-marker"></i>
                            </span>
                            {!! Form::text('district', null, ['class' => 'form-control', 'required', 'placeholder' =>
                            __('customer.district')]); !!}
                        </div>
                    </div>
                </div>
                @if(in_array('customer', $show_referrals_in_register_page ))
                <div class="col-md-4">
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
                @if(in_array('customer', $show_give_away_gift_in_register_page ))
                <div class="col-md-4">
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
				@if(!empty($business_settings->captch_site_key))
					<div class="col-md-12">
                        <div class="form-group" style="padding:auto; margin-top:10px;margin-bottom:10px;">
                        <div class="g-recaptcha" data-sitekey="{{ $business_settings->captch_site_key }}"></div>
                    </div>
				@endif
            </div>
        </div>
        <div class="clearfix"></div>
    </div>
    <div class="modal-footer" style="padding-top: 15px; padding-bottom: 0px;">
        <div class="row">
            <div class="col-md-6" style="text-align: left;">
            </div>
            <div class="col-md-6" style="text-align: right;">
                <button class="btn btn-primary pull-right" type="submit">Submit</button>
            </div>
        </div>
    </div>
</fieldset>