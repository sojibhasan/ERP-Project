<div class="pos-tab-content">
    <div class="row">
        <div class="col-xs-12">
            <div class="col-xs-4">
                <div class="form-group">
                    {!! Form::label('patient_prefix', __('superadmin::lang.patient_prefix') . ':') !!}
                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="fa fa-globe"></i>
                        </span>
                        {!! Form::text('patient_prefix', $settings["patient_prefix"], ['class' =>
                        'form-control','placeholder' => __('superadmin::lang.patient_prefix')]); !!}
                    </div>
                </div>
            </div>
            <div class="col-xs-4">
                <div class="form-group">
                    {!! Form::label('patient_code_start_from', __('superadmin::lang.patient_code_start_from') . ':') !!}
                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="fa fa-globe"></i>
                        </span>
                        {!! Form::text('patient_code_start_from', $settings["patient_code_start_from"], ['class' =>
                        'form-control','placeholder' => __('superadmin::lang.patient_code_start_from')]); !!}
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xs-12">
            <div class="col-xs-4">
                <div class="form-group">
                    {!! Form::label('upload_image_width', __('superadmin::lang.upload_image_width') . ':') !!}
                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="fa fa-globe"></i>
                        </span>
                        {!! Form::text('upload_image_width', $settings["upload_image_width"], ['class' =>
                        'form-control','placeholder' => __('superadmin::lang.upload_image_width')]); !!}
                    </div>
                </div>
            </div>
            <div class="col-xs-4">
                <div class="form-group">
                    {!! Form::label('upload_image_height', __('superadmin::lang.upload_image_height') . ':') !!}
                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="fa fa-globe"></i>
                        </span>
                        {!! Form::text('upload_image_height', $settings["upload_image_height"], ['class' =>
                        'form-control','placeholder' => __('superadmin::lang.upload_image_height')]); !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>