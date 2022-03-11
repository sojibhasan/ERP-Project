<!-- Main content -->
<section class="content">
    <div class="row">
        <div class="col-md-12">
            <div class="col-xs-12">
                <h4>@lang('superadmin::lang.welcome_email_template'):</h4>
                <strong>@lang('lang_v1.available_tags'):</strong> {visitor_name}, {username}<br><br>
            </div>
            <div class="col-xs-12">
                <div class="form-group">
                    {!! Form::label('visitor_welcome_email_subject', __('superadmin::lang.welcome_email_subject') . ':') !!}
                    {!! Form::text('visitor_welcome_email_subject', isset($settings["visitor_welcome_email_subject"]) ? $settings["visitor_welcome_email_subject"] : '', ['class' => 'form-control','placeholder' => __('superadmin::lang.welcome_email_subject')]); !!}
                </div>
            </div>
            <div class="col-xs-12">
                <div class="form-group">
                    {!! Form::label('visitor_welcome_email_body', __('superadmin::lang.welcome_email_body') . ':') !!}
                    {!! Form::textarea('visitor_welcome_email_body', isset($settings["visitor_welcome_email_body"]) ? $settings["visitor_welcome_email_body"] : '', ['class' => 'form-control','placeholder' => __('superadmin::lang.welcome_email_body')]); !!}
                </div>
            </div>
      
            <div class="col-xs-12">
                <hr>
                <div class="form-group">
                    <div class="checkbox">
                    <label>
                        {!! Form::checkbox('enable_visitor_welcome_email', 1, isset($settings["enable_visitor_welcome_email"]) ? (int)$settings["enable_visitor_welcome_email"] : false, 
                        [ 'class' => 'input-icheck']); !!} {{ __( 'superadmin::lang.enable_visitor_welcome_email' ) }}
                    </label> @show_tooltip(__('superadmin::lang.new_customer_welcome_notification_tooltip'))
                    </div>
                </div>
            </div>
        </div>
    </div>

</section>
<!-- /.content -->