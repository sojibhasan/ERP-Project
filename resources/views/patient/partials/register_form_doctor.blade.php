
{!! Form::hidden('language', request()->lang); !!}

<fieldset>
<div class="col-md-6">
    <div class="form-group">
        {!! Form::label('hospital_name', __('patient.hospital_name') . ':' ) !!}
        <div class="input-group">
            <span class="input-group-addon">
                <i class="fa fa-hospital-o"></i>
            </span>
            {!! Form::select('business_id', $hospitals, null, ['class' =>
            'form-control select2', 'placeholder' => __('patient.hospital_name'), 'style' => 'width:100%']); !!}
        </div>
    </div>
</div>
<div class="col-md-6">
    <div class="form-group">
        {!! Form::label('doctor_name', __('patient.doctor_name') . ':' ) !!}
        <div class="input-group">
            <span class="input-group-addon">
                <i class="fa fa-user-md"></i>
            </span>
           {!! Form::text('doctor_name', null, ['class' => 'form-control', 'required', 'placeholder' =>  __('patient.doctor_name')]) !!}
        </div>
    </div>
</div>
<div class="col-md-6">
    <div class="form-group">
        {!! Form::label('signature', __('patient.signature') . ':' ) !!}
        <div class="input-group">
            <span class="input-group-addon">
                <i class="fa fa-superpowers"></i>
            </span>
            {!! Form::text('signature', null, ['class' => 'form-control', 'required', 'placeholder' =>  __('patient.signature')]) !!}
        </div>
    </div>
</div>

<div class="clearfix"></div>
</fieldset>