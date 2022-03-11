<div class="modal-body">
    <div class="row">
        <div class="box box-primary collapsed-box">
            <div class="box-header with-border" data-widget="collapse" style="cursor: pointer;">
                <h3 class="box-title">@lang('superadmin::lang.basic_details')</h3>
            </div>
            <div class="box-body">
                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('date', __('superadmin::lang.date') . ':*', ['class' => 'label_register']) !!}
                        <div class="input-group">
                            <span class="input-group-addon">
                                <i class="fa fa-calendar"></i>
                            </span>
                            {!! Form::text('date', date('d-m-Y'), ['class' => 'form-control','placeholder' =>
                            __('superadmin::lang.date'), 'id' => 'a_date', 'readonly',
                            'required']); !!}
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('name', __('superadmin::lang.name') . ':*', ['class' => 'label_register']) !!}
                        <div class="input-group">
                            <span class="input-group-addon">
                                <i class="fa fa-user"></i>
                            </span>
                            {!! Form::text('name', null, ['class' => 'form-control','placeholder' =>
                            __('superadmin::lang.name'), 'id' => 'a_name',
                            'required']); !!}
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('address', __('superadmin::lang.address') . ':*', ['class' => 'label_register'])
                        !!}
                        <div class="input-group">
                            <span class="input-group-addon">
                                <i class="fa fa-map-marker"></i>
                            </span>
                            {!! Form::text('address', null, ['class' => 'form-control','placeholder' =>
                            __('superadmin::lang.address'), 'id' => 'a_address',
                            'required']); !!}
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('mobile_number', __('superadmin::lang.mobile_number') . ':*', ['class' =>
                        'label_register']) !!}
                        <div class="input-group">
                            <span class="input-group-addon">
                                <i class="fa fa-mobile"></i>
                            </span>
                            {!! Form::text('mobile_number', null, ['class' => 'form-control','placeholder' =>
                            __('superadmin::lang.mobile_number'), 'id' => 'a_mobile_number',
                            'required']); !!}
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('land_number', __('superadmin::lang.land_number') . ':', ['class' =>
                        'label_register']) !!}
                        <div class="input-group">
                            <span class="input-group-addon">
                                <i class="fa fa-phone"></i>
                            </span>
                            {!! Form::text('land_number', null, ['class' => 'form-control','placeholder' =>
                            __('superadmin::lang.land_number'), 'id' => 'a_land_number',
                            ]); !!}
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('email', __('superadmin::lang.email') . ':*', ['class' => 'label_register']) !!}
                        <div class="input-group">
                            <span class="input-group-addon">
                                <i class="fa fa-envelope"></i>
                            </span>
                            {!! Form::text('email', null, ['class' => 'form-control','placeholder' =>
                            __('superadmin::lang.email'), 'id' => 'a_email',
                            'required']); !!}
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('nic_number', __('superadmin::lang.nic_number') . ':*', ['class' =>
                        'label_register']) !!}
                        <div class="input-group">
                            <span class="input-group-addon">
                                <i class="fa fa-id-card-o"></i>
                            </span>
                            {!! Form::text('nic_number', null, ['class' => 'form-control','placeholder' =>
                            __('superadmin::lang.nic_number'), 'id' => 'a_nic_number',
                            'required']); !!}
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('referral_code', __('superadmin::lang.referral_code') . ':', ['class' =>
                        'label_register']) !!}
                        <div class="input-group">
                            <span class="input-group-addon">
                                <i class="fa fa-link"></i>
                            </span>
                            {!! Form::text('referral_code', $agent_referral_code, ['class' =>
                            'form-control','placeholder' =>
                            __('superadmin::lang.referral_code'), 'id' => 'a_referral_code', 'readonly',
                            'required']); !!}
                        </div>
                    </div>
                </div>
                <div class="clearfix"></div>
                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('nic_copy', __('superadmin::lang.upload_nic_image') . ':*', ['class' =>
                        'label_register']) !!}
                        {!! Form::file('nic_copy', ['class' => '', 'id' => 'a_nic_copy', 'style' => 'margin: 0px;']);
                        !!}
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('agent_photo', __('superadmin::lang.upload_your_photo') . ':', ['class' =>
                        'label_register']) !!}
                        {!! Form::file('agent_photo', ['class' => '', 'id' => 'a_agent_photo', 'style' => 'margin:
                        0px;']); !!}
                    </div>
                </div>
            </div>
            <div class="box-footer">
            </div>
        </div>
        <div class="box  box-primary collapsed-box">
            <div class="box-header with-border" data-widget="collapse"  style="cursor: pointer;">
                <h3 class="box-title">@lang('superadmin::lang.bank_details')</h3>
            </div>
            <div class="box-body">
                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('bank_name', __('superadmin::lang.bank_name') . ':*', ['class' =>
                        'label_register']) !!}
                        <div class="input-group">
                            <span class="input-group-addon">
                                <i class="fa fa-building"></i>
                            </span>
                            {!! Form::text('bank_name', null, ['class' => 'form-control','placeholder' =>
                            __('superadmin::lang.bank_name'), 'id' => 'a_bank_name',
                            'required']); !!}
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('account_number', __('superadmin::lang.account_number') . ':*', ['class' =>
                        'label_register']) !!}
                        <div class="input-group">
                            <span class="input-group-addon">
                                <i class="fa fa-credit-card"></i>
                            </span>
                            {!! Form::text('account_number', null, ['class' => 'form-control','placeholder' =>
                            __('superadmin::lang.account_number'), 'id' => 'a_account_number',
                            'required']); !!}
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('branch', __('superadmin::lang.branch') . ':*', ['class' => 'label_register'])
                        !!}
                        <div class="input-group">
                            <span class="input-group-addon">
                                <i class="fa fa-home"></i>
                            </span>
                            {!! Form::text('branch', null, ['class' => 'form-control','placeholder' =>
                            __('superadmin::lang.branch'), 'id' => 'a_branch', 'style' => 'margin: 0px !important;',
                            'required']); !!}
                        </div>
                    </div>
                </div>
            </div>
            <div class="box-footer">
            </div>
        </div>
        <div class="box  box-primary collapsed-box">
            <div class="box-header with-border" data-widget="collapse"  style="cursor: pointer;">
                <h3 class="box-title">@lang('superadmin::lang.login_details')</h3>
            </div>
            <div class="box-body">
                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('username', __('superadmin::lang.username') . ':*', ['class' => 'label_register']) !!}
                        <div class="input-group">
                            <span class="input-group-addon">
                                <i class="fa fa-user"></i>
                            </span>
                            {!! Form::text('username', $agent_referral_code, ['class' => 'form-control','placeholder' =>
                            __('superadmin::lang.username'), 'id' => 'a_username', 'readonly',
                            'required']); !!}
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('passwords', __('superadmin::lang.password') . ':*', ['class' =>
                        'label_register']) !!}
                        <div class="input-group">
                            <span class="input-group-addon">
                                <i class="fa fa-key"></i>
                            </span>
                            <input name="password" type="password" value="" class="form-control" id="a_password" style="margin: 0px;">
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('confirm_passwords', __('superadmin::lang.confirm_password') . ':*', ['class' =>
                        'label_register']) !!}
                        <div class="input-group">
                            <span class="input-group-addon">
                                <i class="fa fa-key"></i>
                            </span>
                            <input name="password" type="password" value="" class="form-control" id="a_confirm_password" style="margin: 0px;">
                        </div>
                    </div>
                </div>
            </div>
            <div class="box-footer">
            </div>
        </div>
		@if(!empty($business_settings->captch_site_key))
        <div class="col-md-12">
        <div class="form-group" style="padding:auto; margin-top:10px;margin-bottom:10px;">
        <div class="g-recaptcha" data-sitekey="{{ $business_settings->captch_site_key }}"></div>
        </div>
	@endif
    </div>
</div>
<div class="modal-footer" style="padding-top: 15px; padding-bottom: 0px;">
    <div class="row">
        <div class="col-md-6" style="text-align: left;">
        </div>
        <div class="col-md-6" style="text-align: right;">
            <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
            <button class="btn btn-primary pull-right" type="submit">@lang( 'messages.save' )</button>
        </div>
    </div>
</div>