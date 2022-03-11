<div class="pos-tab-content">
    <div class="row">
        <div class="col-xs-6>
            <div class=" form-group">
            {!! Form::label('sms_on_password_change', __('superadmin::lang.sms_on_password_change') . ':') !!}
            {!! Form::textarea('sms_on_password_change', $settings['sms_on_password_change'], ['class' =>
            'form-control','placeholder' => __('superadmin::lang.sms_on_password_change'), 'cols' => 4, 'rows' => 6, 'style' => 'width: 90%;']); !!}
        </div>
    </div>
</div>