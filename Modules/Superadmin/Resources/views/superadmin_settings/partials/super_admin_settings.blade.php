<div
    class="pos-tab-content @if(!session('status.account_default') && !session('status.manage_user') && !session('status.tank_dip_chart')) active @endif">
    <div class="row">
        <div class="col-xs-4">
            <div class="form-group">
                {!! Form::label('invoice_business_name', __('business.business_name') . ':') !!}
                <div class="input-group">
                    <span class="input-group-addon">
                        <i class="fa fa-suitcase"></i>
                    </span>
                    {!! Form::text('invoice_business_name', $settings["invoice_business_name"], ['class' =>
                    'form-control','placeholder' => __('business.business_name'), 'required']); !!}
                </div>
            </div>
        </div>

        <div class="col-xs-4">
            <div class="form-group">
                {!! Form::label('email', __('business.email'). ':')!!}
                <div class="input-group">
                    <span class="input-group-addon">
                        <i class="fa fa-envelope"></i>
                    </span>
                    {!! Form::email('email',$settings["email"], ['class'=>'form-control', 'placeholder'=>
                    __('business.email')])!!}
                </div>
            </div>
        </div>

        <div class="col-xs-4">
            <div class="form-group">
                {!! Form::label('app_currency_id', __('business.currency') . ':') !!}
                <div class="input-group">
                    <span class="input-group-addon">
                        <i class="fa fa-money"></i>
                    </span>
                    {!! Form::select('app_currency_id', $currencies, $settings["app_currency_id"], ['class' =>
                    'form-control select2','placeholder' => __('business.currency_placeholder'), 'required']); !!}
                </div>
            </div>
        </div>

        <div class="clearfix"></div>
        <div class="col-xs-4">
            <div class="form-group">
                {!! Form::label('invoice_business_landmark', __('business.landmark') . ':') !!}
                <div class="input-group">
                    <span class="input-group-addon">
                        <i class="fa fa-map-marker"></i>
                    </span>
                    {!! Form::text('invoice_business_landmark', $settings["invoice_business_landmark"], ['class' =>
                    'form-control','placeholder' => __('business.landmark'),'required']); !!}
                </div>
            </div>
        </div>

        <div class="col-xs-4">
            <div class="form-group">
                {!! Form::label('invoice_business_zip', __('business.zip_code') . ':') !!}
                <div class="input-group">
                    <span class="input-group-addon">
                        <i class="fa fa-map-marker"></i>
                    </span>
                    {!! Form::text('invoice_business_zip',$settings["invoice_business_zip"], ['class' =>
                    'form-control','placeholder' => __('business.zip_code'), 'required']); !!}
                </div>
            </div>
        </div>

        <div class="col-xs-4">
            <div class="form-group">
                {!! Form::label('invoice_business_state', __('business.state') . ':') !!}
                <div class="input-group">
                    <span class="input-group-addon">
                        <i class="fa fa-map-marker"></i>
                    </span>
                    {!! Form::text('invoice_business_state', $settings["invoice_business_state"], ['class' =>
                    'form-control','placeholder' => __('business.state'), 'required']); !!}
                </div>
            </div>
        </div>

        <div class="col-xs-4">
            <div class="form-group">
                {!! Form::label('invoice_business_city', __('business.city') . ':') !!}
                <div class="input-group">
                    <span class="input-group-addon">
                        <i class="fa fa-map-marker"></i>
                    </span>
                    {!! Form::text('invoice_business_city',$settings["invoice_business_city"], ['class' =>
                    'form-control','placeholder' => __('business.city'),'required']); !!}
                </div>
            </div>
        </div>
        <div class="col-xs-4">
            <div class="form-group">
                {!! Form::label('invoice_business_country', __('business.country') . ':') !!}
                <div class="input-group">
                    <span class="input-group-addon">
                        <i class="fa fa-globe"></i>
                    </span>
                    {!! Form::text('invoice_business_country', $settings["invoice_business_country"], ['class' =>
                    'form-control','placeholder' => __('business.country'), 'required']); !!}
                </div>
            </div>
        </div>
        <div class="col-xs-4">
            <div class="form-group">
                {!! Form::label('package_expiry_alert_days', __('superadmin::lang.package_expiry_alert_days') . ':') !!}
                <div class="input-group">
                    <span class="input-group-addon">
                        <i class="fa fa-exclamation-triangle"></i>
                    </span>
                    {!! Form::number('package_expiry_alert_days', $settings["package_expiry_alert_days"], ['class' =>
                    'form-control','placeholder' => __('superadmin::lang.package_expiry_alert_days'), 'required']); !!}
                </div>
            </div>
        </div>
        <div class="col-xs-4">
            <div class="form-group">
                {!! Form::label('default_number_of_customers', __('superadmin::lang.default_number_of_customers') . ':')
                !!}
                <div class="input-group">
                    <span class="input-group-addon">
                        <i class="fa fa-users"></i>
                    </span>
                    {!! Form::number('default_number_of_customers', $settings["default_number_of_customers"], ['class'
                    => 'form-control','placeholder' => __('superadmin::lang.default_number_of_customers'), 'required']);
                    !!}
                </div>
            </div>
        </div>
        <div class="col-xs-4">
            <div class="form-group">
                {!! Form::label('company_number_prefix', __('superadmin::lang.company_number_prefix') . ':') !!}
                <div class="input-group">
                    <span class="input-group-addon">
                        <i class="fa fa-building-o"></i>
                    </span>
                    {!! Form::text('company_number_prefix', $settings["company_number_prefix"], ['class' =>
                    'form-control','placeholder' => __('superadmin::lang.company_number_prefix'), 'required']); !!}
                </div>
            </div>
        </div>
        <div class="col-xs-4">
            <div class="form-group">
                {!! Form::label('company_starting_number', __('superadmin::lang.company_starting_number') . ':') !!}
                <div class="input-group">
                    <span class="input-group-addon">
                        <i class="fa fa-arrow-up"></i>
                    </span>
                    {!! Form::number('company_starting_number', $settings["company_starting_number"], ['class' =>
                    'form-control','placeholder' => __('superadmin::lang.company_starting_number'), 'required']); !!}
                </div>
            </div>
        </div>
        <div class="col-xs-4">
            <div class="form-group">
                {!! Form::label('upload_image_quality', __('superadmin::lang.upload_image_quality') . ':') !!}
                <div class="input-group">
                    <span class="input-group-addon">
                        <i class="fa fa-arrow-up"></i>
                    </span>
                    {!! Form::number('upload_image_quality', $settings["upload_image_quality"], ['class' =>
                    'form-control','placeholder' => __('superadmin::lang.upload_image_quality'), 'required']); !!}
                </div>
            </div>
        </div>

        <div class="col-xs-4">
            <div class="form-group">
                {!! Form::label('business_or_entity', __('superadmin::lang.business_or_entity') . ':') !!}
                <div class="input-group">
                    <span class="input-group-addon">
                        <i class="fa fa-text-width"></i>
                    </span>
                    {!! Form::select('business_or_entity', ['buisness' => 'Business', 'entity' => 'Entity'],
                    !empty($settings["business_or_entity"]) ? $settings["business_or_entity"] : null ,['class' =>
                    'form-control','placeholder' => __('superadmin::lang.please_select'), 'required']); !!}
                </div>
            </div>
        </div>

        @php
            $show_referrals_in_register_page = json_decode($settings["show_referrals_in_register_page"], true);
        @endphp
        <div class="row">
            <div class="col-xs-4">
                <div class="form-group">
                    {!! Form::label('name', __( 'superadmin::lang.show_referrals_in_register_page' ) . ':') !!}

                    {!! Form::select('show_referrals_in_register_page[]',['customer' => __('superadmin::lang.customer'), 'my_health' =>
                    __('superadmin::lang.my_health'),
                    'visitor' => __('superadmin::lang.visitor'), 'company' => __('superadmin::lang.company'), 'member'
                    => __('superadmin::lang.member')],
                    !empty($show_referrals_in_register_page) ?
                    $show_referrals_in_register_page : null ,['class' =>
                    'form-control select2', 'multiple', 'style' => 'width: 100%;' ]); !!}

                </div>
            </div>
        </div>

        <div class="col-xs-4">
            <div class="form-group">
                {!! Form::label('helpdesk_system_url', __('superadmin::lang.helpdesk_system_url') . ':') !!}
                <div class="input-group">
                    <span class="input-group-addon">
                        <i class="fa fa-link"></i>
                    </span>
                    {!! Form::text('helpdesk_system_url', $settings["helpdesk_system_url"], ['class' =>
                    'form-control','placeholder' => __('superadmin::lang.helpdesk_system_url')]); !!}
                </div>
            </div>
        </div>

        <div class="col-xs-4">
            <div class="form-group">
                {!! Form::label('create_individual_company_package', __('superadmin::lang.create_individual_company_package') . ':') !!}
                <div class="input-group">
                    <span class="input-group-addon">
                        <i class="fa fa-link"></i>
                    </span>
                    {!! Form::select('create_individual_company_package', ['no' => 'No', 'yes' => 'Yes'],
                    !empty($settings["create_individual_company_package"]) ? $settings["create_individual_company_package"] : null ,['class' =>
                    'form-control','placeholder' => __('superadmin::lang.please_select'), 'required']); !!}
                </div>
            </div>
        </div>

        <div class="clearfix"></div>

        <div class="col-xs-4">
            <div class="form-group">
                <div class="checkbox">
                    <label>
                        {!! Form::checkbox('enable_business_based_username', 1,
                        !empty($settings["enable_business_based_username"]) ?
                        (int)$settings["enable_business_based_username"] : 0 ,
                        [ 'class' => 'input-icheck']); !!} {{ __( 'superadmin::lang.enable_business_based_username' ) }}
                    </label>
                </div>
                <p class="help-block">@lang('superadmin::lang.business_based_username_help')</p>
            </div>
        </div>

        <div class="col-xs-4">
            <div class="form-group">
                <div class="checkbox">
                    <label>
                        {!! Form::checkbox('enable_admin_login', 1, !empty($settings["enable_admin_login"]) ?
                        (int)$settings["enable_admin_login"] : 0 ,
                        [ 'class' => 'input-icheck']); !!} {{ __( 'superadmin::lang.enable_admin_login' ) }}
                    </label>
                </div>
            </div>
        </div>
        <div class="col-xs-4">
            <div class="form-group">
                <div class="checkbox">
                    <label>
                        {!! Form::checkbox('enable_member_login', 1, !empty($settings["enable_member_login"]) ?
                        (int)$settings["enable_member_login"] : 0 ,
                        [ 'class' => 'input-icheck']); !!} {{ __( 'superadmin::lang.enable_member_login' ) }}
                    </label>
                </div>
            </div>
        </div>
        <div class="col-xs-4">
            <div class="form-group">
                <div class="checkbox">
                    <label>
                        {!! Form::checkbox('enable_visitor_login', 1,
                        !empty($settings["enable_visitor_login"]) ? (int)$settings["enable_visitor_login"] :
                        0 ,
                        [ 'class' => 'input-icheck']); !!} {{ __( 'superadmin::lang.enable_visitor_login' ) }}
                    </label>
                </div>
            </div>
        </div>

        <div class="col-xs-4">
            <div class="form-group">
                <div class="checkbox">
                    <label>
                        {!! Form::checkbox('enable_lang_btn_login_page', 1,
                        !empty($settings["enable_lang_btn_login_page"]) ? (int)$settings["enable_lang_btn_login_page"] :
                        0 ,
                        [ 'class' => 'input-icheck']); !!} {{ __( 'superadmin::lang.enable_lang_btn_login_page' ) }}
                    </label>
                </div>
            </div>
        </div>
        <div class="clearfix"></div>
        <div class="col-xs-4">
            <div class="form-group">
                <div class="checkbox">
                    <label>
                        {!! Form::checkbox('enable_pricing_btn_login_page', 1,
                        !empty($settings["enable_pricing_btn_login_page"]) ?
                        (int)$settings["enable_pricing_btn_login_page"] : 0 ,
                        [ 'class' => 'input-icheck']); !!} {{ __( 'superadmin::lang.enable_pricing_btn_login_page' ) }}
                    </label>
                </div>
            </div>
        </div>
        <div class="col-xs-4">
            <div class="form-group">
                <div class="checkbox">
                    <label>
                        {!! Form::checkbox('enable_individual_register_btn_login_page', 1,
                        !empty($settings["enable_individual_register_btn_login_page"]) ?
                        (int)$settings["enable_individual_register_btn_login_page"] : 0 ,
                        [ 'class' => 'input-icheck']); !!}
                        {{ __( 'superadmin::lang.enable_individual_register_btn_login_page' ) }}
                    </label>
                </div>
            </div>
        </div>

        <div class="col-xs-12">
            <p class="help-block"><i>{!! __('superadmin::lang.version_info', ['version' => $superadmin_version]) !!}</i>
            </p>
        </div>
    </div>
</div>