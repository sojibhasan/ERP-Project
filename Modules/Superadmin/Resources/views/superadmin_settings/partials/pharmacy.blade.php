<div class="pos-tab-content">
    <div class="row">
        <div class="col-xs-12">
            <div class="col-xs-4">
                <div class="form-group">
                     {!! Form::label('pharmacy_prefix', __('superadmin::lang.pharmacy_prefix') . ':') !!}
                    <div class="input-group">
                    <span class="input-group-addon">
                        <i class="fa fa-globe"></i>
                    </span>
                    {!! Form::text('pharmacy_prefix', $settings["pharmacy_prefix"], ['class' => 'form-control','placeholder' => __('superadmin::lang.pharmacy_prefix')]); !!}
                </div>
                </div>
            </div>
            <div class="col-xs-4">
                <div class="form-group">
                     {!! Form::label('pharmacy_code_start_from', __('superadmin::lang.pharmacy_code_start_from') . ':') !!}
                    <div class="input-group">
                    <span class="input-group-addon">
                        <i class="fa fa-globe"></i>
                    </span>
                    {!! Form::text('pharmacy_code_start_from', $settings["pharmacy_code_start_from"], ['class' => 'form-control','placeholder' => __('superadmin::lang.pharmacy_code_start_from')]); !!}
                </div>
                </div>
            </div>
        </div>
    </div>
</div>