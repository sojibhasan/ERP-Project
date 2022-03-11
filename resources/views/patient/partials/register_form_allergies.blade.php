
{!! Form::hidden('language', request()->lang); !!}

<fieldset>

<div class="col-md-6">
    <div class="form-group">
        {!! Form::label('allergy_name', __('patient.allergy_name') . ':' ) !!}
        <div class="input-group">
            <span class="input-group-addon">
                <i class="fa fa-deaf"></i>
            </span>
           {!! Form::text('allergy_name', null, ['class' => 'form-control', 'required', 'placeholder' =>  __('patient.allergy_name')]) !!}
        </div>
    </div>
</div>
<div class="col-md-12">
    <div class="form-group">
        {!! Form::label('description', __('patient.description') . ':*' ) !!}
        {!! Form::textarea('description', null, ['class' => 'form-control', 'rows' => '5', 'style' => 'width:
        100%']) !!}
    </div>
    <input type="hidden" name="patient_code" id="patient_code" value="{{$patient_code}}">
</div>

<div class="clearfix"></div>
</fieldset>