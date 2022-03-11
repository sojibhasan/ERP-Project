<div class="pos-tab-content">
    <div class="row">
    	<div class="col-xs-4">
            <div class="form-group">
            	{!! Form::label('MAIL_DRIVER', __('superadmin::lang.mail_driver') . ':') !!}
            	{!! Form::select('MAIL_DRIVER', $mail_drivers, $default_values['MAIL_DRIVER'], ['class' => 'form-control']); !!}
            </div>
        </div>
        <div class="col-xs-4">
            <div class="form-group">
            	{!! Form::label('MAIL_HOST', __('superadmin::lang.mail_host') . ':') !!}
            	{!! Form::text('MAIL_HOST', $default_values['MAIL_HOST'], ['class' => 'form-control','placeholder' => __('superadmin::lang.mail_host')]); !!}
            </div>
        </div>
        <div class="col-xs-4">
            <div class="form-group">
            	{!! Form::label('MAIL_PORT', __('superadmin::lang.mail_port') . ':') !!}
            	{!! Form::text('MAIL_PORT', $default_values['MAIL_PORT'], ['class' => 'form-control','placeholder' => __('superadmin::lang.mail_port')]); !!}
            </div>
        </div>
        <div class="clearfix"></div>
        <div class="col-xs-4">
            <div class="form-group">
                {!! Form::label('MAIL_USERNAME', __('superadmin::lang.mail_username') . ':') !!}
                {!! Form::text('MAIL_USERNAME', $default_values['MAIL_USERNAME'], ['class' => 'form-control','placeholder' => __('superadmin::lang.mail_username')]); !!}
            </div>
        </div>
        <div class="col-xs-4">
            <div class="form-group">
                {!! Form::label('MAIL_PASSWORD', __('superadmin::lang.mail_password') . ':') !!}
                {!! Form::text('MAIL_PASSWORD', $default_values['MAIL_PASSWORD'], ['class' => 'form-control','placeholder' => __('superadmin::lang.mail_password')]); !!}
            </div>
        </div>
        <div class="col-xs-4">
            <div class="form-group">
                {!! Form::label('MAIL_ENCRYPTION', __('superadmin::lang.mail_encryption') . ':') !!}
                {!! Form::text('MAIL_ENCRYPTION', $default_values['MAIL_ENCRYPTION'], ['class' => 'form-control','placeholder' => __('superadmin::lang.mail_encryption_place')]); !!}
            </div>
        </div>
        <div class="clearfix"></div>
        <div class="col-xs-4">
            <div class="form-group">
                {!! Form::label('MAIL_FROM_ADDRESS', __('superadmin::lang.mail_from_address') . ':') !!}
                {!! Form::email('MAIL_FROM_ADDRESS', $default_values['MAIL_FROM_ADDRESS'], ['class' => 'form-control','placeholder' => __('superadmin::lang.mail_from_address')]); !!}
            </div>
        </div>
        <div class="col-xs-4">
            <div class="form-group">
                {!! Form::label('MAIL_FROM_NAME', __('superadmin::lang.mail_from_name') . ':') !!}
                {!! Form::text('MAIL_FROM_NAME', $default_values['MAIL_FROM_NAME'], ['class' => 'form-control','placeholder' => __('superadmin::lang.mail_from_name')]); !!}
            </div>
        </div>
        <div class="clearfix"></div>
        <div class="col-md-4">
            <div class="form-group">
                <div class="checkbox">
                    <label>
                    {!! Form::checkbox('allow_email_settings_to_businesses', 1,!empty($settings["allow_email_settings_to_businesses"]), 
                    [ 'class' => 'input-icheck']); !!}
                    @lang('superadmin::lang.allow_email_settings_to_businesses') 
                    </label>
                    @show_tooltip(__('superadmin::lang.allow_email_settings_tooltip'))
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <div class="checkbox">
                    <label>
                    {!! Form::checkbox('enable_new_business_registration_notification', 1,!empty($settings["enable_new_business_registration_notification"]), 
                    [ 'class' => 'input-icheck']); !!}
                    @lang('superadmin::lang.enable_new_business_registration_notification') 
                    </label> @show_tooltip(__('superadmin::lang.new_business_notification_tooltip'))
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <div class="checkbox">
                    <label>
                    {!! Form::checkbox('enable_new_subscription_notification', 1,!empty($settings["enable_new_subscription_notification"]), 
                    [ 'class' => 'input-icheck']); !!}
                    @lang('superadmin::lang.enable_new_subscription_notification') 
                    </label> @show_tooltip(__('superadmin::lang.new_subscription_tooltip'))
                </div>
            </div>
        </div>
        <div class="clearfix"></div>
        <div class="col-xs-12">
            <hr>
            <div class="form-group">
                <div class="checkbox">
                <label>
                    {!! Form::checkbox('enable_welcome_email', 1, isset($settings["enable_welcome_email"]) ? (int)$settings["enable_welcome_email"] : false, 
                    [ 'class' => 'input-icheck']); !!} {{ __( 'superadmin::lang.enable_welcome_email' ) }}
                </label> @show_tooltip(__('superadmin::lang.new_business_welcome_notification_tooltip'))
                </div>
            </div>
        </div>
        <div class="col-xs-12">
            <h4>@lang('superadmin::lang.welcome_email_template'):</h4>
            <strong>@lang('lang_v1.available_tags'):</strong> {business_name}, {owner_name}, {username}, {company_number} <br><br>
        </div>
        <div class="col-xs-12">
            <div class="form-group">
                {!! Form::label('welcome_email_subject', __('superadmin::lang.welcome_email_subject') . ':') !!}
                {!! Form::text('welcome_email_subject', isset($settings["welcome_email_subject"]) ? $settings["welcome_email_subject"] : '', ['class' => 'form-control','placeholder' => __('superadmin::lang.welcome_email_subject')]); !!}
            </div>
        </div>
        <div class="col-xs-12">
            <div class="form-group">
                {!! Form::label('welcome_email_body', __('superadmin::lang.welcome_email_body') . ':') !!}
                {!! Form::textarea('welcome_email_body', isset($settings["welcome_email_body"]) ? $settings["welcome_email_body"] : '', ['class' => 'form-control','placeholder' => __('superadmin::lang.welcome_email_body')]); !!}
            </div>
        </div>
  
        <div class="col-xs-12">
            <hr>
            <div class="form-group">
                <div class="checkbox">
                <label>
                    {!! Form::checkbox('enable_customer_welcome_email', 1, isset($settings["enable_customer_welcome_email"]) ? (int)$settings["enable_customer_welcome_email"] : false, 
                    [ 'class' => 'input-icheck']); !!} {{ __( 'superadmin::lang.enable_customer_welcome_email' ) }}
                </label> @show_tooltip(__('superadmin::lang.new_customer_welcome_notification_tooltip'))
                </div>
            </div>
        </div>

        <div class="col-xs-12">
            <h4>@lang('superadmin::lang.customer_welcome_email_template'):</h4>
            <strong>@lang('lang_v1.available_tags'):</strong> {customer_name}, {username} <br><br>
        </div>
        <div class="col-xs-12">
            <div class="form-group">
                {!! Form::label('customer_welcome_email_subject', __('superadmin::lang.customer_welcome_email_subject') . ':') !!}
                {!! Form::text('customer_welcome_email_subject', isset($settings["customer_welcome_email_subject"]) ? $settings["customer_welcome_email_subject"] : '', ['class' => 'form-control','placeholder' => __('superadmin::lang.customer_welcome_email_subject')]); !!}
            </div>
        </div>
        <div class="col-xs-12">
            <div class="form-group">
                {!! Form::label('customer_welcome_email_body', __('superadmin::lang.customer_welcome_email_body') . ':') !!}
                {!! Form::textarea('customer_welcome_email_body', isset($settings["customer_welcome_email_body"]) ? $settings["customer_welcome_email_body"] : '', ['class' => 'form-control','placeholder' => __('superadmin::lang.customer_welcome_email_body')]); !!}
            </div>
        </div>

        <div class="col-xs-12">
            <h4>@lang('superadmin::lang.agent_welcome_email_template'):</h4>
            <strong>@lang('lang_v1.available_tags'):</strong> {agent_name}, {username}, {referral_code} <br><br>
        </div>
        <div class="col-xs-12">
            <div class="form-group">
                {!! Form::label('agent_welcome_email_subject', __('superadmin::lang.agent_welcome_email_subject') . ':') !!}
                {!! Form::text('agent_welcome_email_subject', isset($settings["agent_welcome_email_subject"]) ? $settings["agent_welcome_email_subject"] : '', ['class' => 'form-control','placeholder' => __('superadmin::lang.agent_welcome_email_subject')]); !!}
            </div>
        </div>
        <div class="col-xs-12">
            <div class="form-group">
                {!! Form::label('agent_welcome_email_body', __('superadmin::lang.agent_welcome_email_body') . ':') !!}
                {!! Form::textarea('agent_welcome_email_body', isset($settings["agent_welcome_email_body"]) ? $settings["agent_welcome_email_body"] : '', ['class' => 'form-control','placeholder' => __('superadmin::lang.agent_welcome_email_body')]); !!}
            </div>
        </div>

        <div class="col-xs-12">
            <h4>@lang('superadmin::lang.new_subscription_email_template'):</h4>
            <strong>@lang('lang_v1.available_tags'):</strong> {business_name}, {company_code} , {package_name}, {transaction_id}, {paid_via}, {status} <br><br>
        </div>
        <div class="col-xs-12">
            <div class="form-group">
                {!! Form::label('new_subscription_email_subject', __('superadmin::lang.new_subscription_email_subject') . ':') !!}  ({{__('superadmin::lang.online')}})
                {!! Form::text('new_subscription_email_subject', isset($settings["new_subscription_email_subject"]) ? $settings["new_subscription_email_subject"] : '', ['class' => 'form-control','placeholder' => __('superadmin::lang.new_subscription_email_subject')]); !!}
            </div>
        </div>
        <div class="col-xs-12">
            <div class="form-group">
                {!! Form::label('new_subscription_email_body', __('superadmin::lang.new_subscription_email_body') . ':') !!}  ({{__('superadmin::lang.online')}})
                {!! Form::textarea('new_subscription_email_body', isset($settings["new_subscription_email_body"]) ? $settings["new_subscription_email_body"] : '', ['class' => 'form-control','placeholder' => __('superadmin::lang.new_subscription_email_body')]); !!}
            </div>
        </div>
        <div class="col-xs-12">
            <div class="form-group">
                {!! Form::label('new_subscription_email_subject_offline', __('superadmin::lang.new_subscription_email_subject') . ':') !!}  ({{__('superadmin::lang.offline')}})
                {!! Form::text('new_subscription_email_subject_offline', isset($settings["new_subscription_email_subject_offline"]) ? $settings["new_subscription_email_subject_offline"] : '', ['class' => 'form-control','placeholder' => __('superadmin::lang.new_subscription_email_subject')]); !!}
            </div>
        </div>
        <div class="col-xs-12">
            <div class="form-group">
                {!! Form::label('new_subscription_email_body_offline', __('superadmin::lang.new_subscription_email_body') . ':') !!}  ({{__('superadmin::lang.offline')}})
                {!! Form::textarea('new_subscription_email_body_offline', isset($settings["new_subscription_email_body_offline"]) ? $settings["new_subscription_email_body_offline"] : '', ['class' => 'form-control','placeholder' => __('superadmin::lang.new_subscription_email_body')]); !!}
            </div>
        </div>
    </div>
</div>