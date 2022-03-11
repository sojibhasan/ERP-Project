<div class="pos-tab-content">
    <div class="row">
        <div class="col-xs-12">
            <div class="col-xs-4">
                <div class="form-group">
                     {!! Form::label('laboratory_prefix', __('superadmin::lang.laboratory_prefix') . ':') !!}
                    <div class="input-group">
                    <span class="input-group-addon">
                        <i class="fa fa-globe"></i>
                    </span>
                    {!! Form::text('laboratory_prefix', $settings["laboratory_prefix"], ['class' => 'form-control','placeholder' => __('superadmin::lang.laboratory_prefix')]); !!}
                </div>
                </div>
            </div>
            <div class="col-xs-4">
                <div class="form-group">
                     {!! Form::label('laboratory_code_start_from', __('superadmin::lang.laboratory_code_start_from') . ':') !!}
                    <div class="input-group">
                    <span class="input-group-addon">
                        <i class="fa fa-globe"></i>
                    </span>
                    {!! Form::text('laboratory_code_start_from', $settings["laboratory_code_start_from"], ['class' => 'form-control','placeholder' => __('superadmin::lang.laboratory_code_start_from')]); !!}
                </div>
                </div>
            </div>
        </div>
    </div>
</div>