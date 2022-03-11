<div class="pos-tab-content">
    <div class="row">
        <div class="col-xs-12">
            <div class="col-xs-4">
                <div class="form-group">
                     {!! Form::label('hospital_prefix', __('superadmin::lang.hospital_prefix') . ':') !!}
                    <div class="input-group">
                    <span class="input-group-addon">
                        <i class="fa fa-globe"></i>
                    </span>
                    {!! Form::text('hospital_prefix', $settings["hospital_prefix"], ['class' => 'form-control','placeholder' => __('superadmin::lang.hospital_prefix')]); !!}
                </div>
                </div>
            </div>
            <div class="col-xs-4">
                <div class="form-group">
                     {!! Form::label('hospital_code_start_from', __('superadmin::lang.hospital_code_start_from') . ':') !!}
                    <div class="input-group">
                    <span class="input-group-addon">
                        <i class="fa fa-globe"></i>
                    </span>
                    {!! Form::text('hospital_code_start_from', $settings["hospital_code_start_from"], ['class' => 'form-control','placeholder' => __('superadmin::lang.hospital_code_start_from')]); !!}
                </div>
                </div>
            </div>
        </div>
    </div>
</div>