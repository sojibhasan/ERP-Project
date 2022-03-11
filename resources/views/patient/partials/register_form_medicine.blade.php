
{!! Form::hidden('language', request()->lang); !!}

<fieldset>

<div class="col-md-6">
    <div class="form-group">
        {!! Form::label('medicine_name', __('patient.medicine_name') . ':' ) !!}
        <div class="input-group">
            <span class="input-group-addon">
                <i class="fa fa-medkit"></i>
            </span>
           {!! Form::text('medicine_name', null, ['class' => 'form-control', 'required', 'placeholder' =>  __('patient.medicine_name')]) !!}
        </div>
    </div>
</div>
<div class="col-md-12">
    <div class="form-group">
        {!! Form::label('description', __('patient.description') . ':*' ) !!}
        {!! Form::textarea('description', null, ['class' => 'form-control', 'rows' => '5', 'style' => 'width:
        100%']) !!}
    </div>
</div>

<div class="clearfix"></div>
</fieldset>