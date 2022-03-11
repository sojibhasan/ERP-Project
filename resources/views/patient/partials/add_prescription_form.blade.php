@if (empty($prescription))
@if (request()->session()->get('business.is_patient'))
{!! Form::open(['url' => action('PrescriptionController@upload'), 'method' => 'post', 'id' =>
'prescription_form', 'enctype' => 'multipart/form-data']) !!}
@else
{!! Form::open(['url' => action('PrescriptionController@store'), 'method' => 'post','id' => 'prescription_form']) !!}
@endif
@else
{!! Form::open(['url' => action('PrescriptionController@update', [$prescription->id]), 'method' => 'PUT','id' =>
'prescription_form']) !!}
@endif

<div class="modal-header">
    @if (empty($prescription))
    <h4 class="modal-title">@lang('patient.add_new_prescription')</h4>
    @else
    <h4 class="modal-title">@lang('patient.editprescription')</h4>
    @endif
</div>

<div class="modal-body">
    <div class="row">
        <input type="hidden" name="medicine_row_index" id="medicine_row_index" value="0">
        <input type="hidden" name="test_row_index" id="test_row_index" value="0">
        <input type="hidden" name="patient_code" id="patient_code" value="{{$patient_code}}">
        <div class="col-md-6 ">
            <div class="form-group">
                {!! Form::label('hospital', __('patient.hospital') . ':' ) !!}
                {!! Form::text('hospital_name', !empty($prescription)?$prescription->hospital_name:null , ['class' => 'form-control', 'id' => 'hospital_name', 'placeholder' => __('patient.hospital'), 'style' => 'width:100%;']); !!}
            </div>
        </div>

        <div class="col-md-6 ">
            <div class="form-group">
                {!! Form::label('doctor', __('patient.doctor') . ':' ) !!}
                <div class="input-group">
                    {!! Form::select('doctor_id',
                    !empty($prescription)?$doctors:$doctors, !empty($prescription)?$prescription->doctor_id:null , ['class' => 'form-control
                    mousetrap', 'id' => 'doctor_id', 'placeholder' => __('patient.doctor')]); !!}
                    <span class="input-group-btn">
                        <button type="button" class="btn btn-default bg-white btn-flat btn-modal" id='add_new_doctor'>
                            <i class="fa fa-plus-circle text-primary fa-lg"></i></button>
                    </span>
                </div>
            </div>
        </div>
        <div class="clearfix"></div>
        <div class="col-md-6">
            <div class="form-group">
                {!! Form::label('date', __('patient.date') . ':' ) !!}
                <div class="input-group">
                    <span class="input-group-addon">
                        <i class="fa fa-calendar-check-o"></i>
                    </span>
                   {!! Form::text('date', null, ['class' => 'form-control', 'required', 'placeholder' =>  __('patient.date'), 'id' => 'prescription_date']) !!}
                </div>
            </div>
        </div>
        @if (request()->session()->get('business.is_patient'))
        <div class="col-md-6">
            <div class="form-group">
                {!! Form::label('amount', __('patient.amount') . ':') !!}
                {!! Form::text('amount', null, ['class' => 'form-control',  'placeholder' =>  __('patient.amount'), 'id' => 'prescription_amount']) !!}

            </div>
        </div>
        <div class="col-md-12">
            <div class="form-group">
                {!! Form::label('prescription_file', __('patient.file') . ':*' ) !!}
                {!! Form::file('prescription_file', ['required' => 'required', 'files' => true]); !!}

            </div>
        </div>
        @endif

        @if (!request()->session()->get('business.is_patient'))
        <div class="col-md-12">
            <div class="form-group">
                {!! Form::label('symptoms', __('patient.symptoms') . ':*' ) !!}
                {!! Form::textarea('symptoms', !empty($prescription)?$prescription->symptoms: null, ['class' =>
                'form-control', 'rows' => '5', 'style' => 'width:
                100%']) !!}

            </div>
        </div>
        <div class="col-md-12">
            <div class="form-group">
                {!! Form::label('diagnosis', __('patient.diagnosis') . ':*' ) !!}
                {!! Form::textarea('diagnosis', !empty($prescription)?$prescription->diagnosis:null, ['class' =>
                'form-control', 'rows' => '5', 'style' => 'width:
                100%']) !!}
            </div>
        </div>

        <div class="col-md-6 ">
            <div class="form-group">
                {!! Form::label('allergies', __('patient.allergies') . ':*' ) !!}
                <div class="input-group">
                    {!! Form::select('allergies_id',
                    $allergies, !empty($prescription)?$prescription->allergies_id:null , ['class' => 'form-control
                    mousetrap', 'id' => 'allergies_id', 'placeholder' => __('patient.allergies'), 'required']); !!}
                    <span class="input-group-btn">
                        <button type="button" class="btn btn-default bg-white btn-flat btn-modal"
                            id='add_new_allergies'>
                            <i class="fa fa-plus-circle text-primary fa-lg"></i></button>
                    </span>
                </div>
            </div>
        </div>
     
        <div class="clearfix"></div>
        <div class="col-md-12" style="padding-left: 30px;">
            <div class="row">
                {!! Form::label('medicine', __('patient.medicine') . ':*' ) !!}
            </div>
            <div class="row" id="medicine_rows">
                @if (!empty($medicines))
                @foreach ($medicines as $key => $item)
                <div class="medicine_row">
                    <div class="col-md-5" style="padding-left: 0px;">

                        {!! Form::text('medicine['.$key.'][medicine_name]',  $item->medicine_name, ['class' => 'form-control', 'placeholder' =>
                        __('patient.medicine_name')]) !!}
                    </div>
                    <div class="col-md-5">
                        <div class="form-group">
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fa fa-sticky-note-o"></i>
                                </span>
                                {!! Form::text('medicine['.$key.'][notes]',  $item->notes, ['class' => 'form-control',
                                'placeholder'
                                =>
                                __('patient.medicine_notes')]) !!}
                            </div>
                        </div>
                    </div>
                    {!! Form::hidden('medicine['.$key.'][medicine_id]', $item->id) !!}
                    <div class="col-md-2">
                        <a class="delete_medicine_row"
                            style="color: brown; font-size: 18px; cursor: pointer; border: none; background: none; test-decoration:none;">x</a>
                    </div>
                </div>
                    
                @endforeach
                @else
                    <div class="medicine_row">
                        <div class="col-md-5" style="padding-left: 0px;">
    
                            {!! Form::text('medicine[0][medicine_name]', null, ['class' => 'form-control', 'placeholder' =>
                            __('patient.medicine_name')]) !!}
                        </div>
                        <div class="col-md-5">
                            <div class="form-group">
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        <i class="fa fa-sticky-note-o"></i>
                                    </span>
                                    {!! Form::text('medicine[0][notes]', null, ['class' => 'form-control',
                                    'placeholder'
                                    =>
                                    __('patient.medicine_notes')]) !!}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <a class="delete_medicine_row"
                                style="color: brown; font-size: 18px; cursor: pointer; border: none; background: none; test-decoration:none;">x</a>
                        </div>
                    </div>

                @endif
            </div>
        </div>
        <div class="row" style="padding-left: 30px;">
            <button class="btn btn-primary btn-sm" id="add_medicine_row">@lang('patient.add_medicine')</button>
        </div>
        <div class="clearfix"></div>
        <div class="col-md-12" style="padding-left: 30px;" id="medicine_row">
            <div class="row">
                {!! Form::label('test', __('patient.test') . ':*' ) !!}
            </div>
            <div class="row" id="test_rows">
                @if (!empty($tests))
                @foreach ($tests as $ke => $test)
                <div class="test_row">
                    <div class="col-md-5" style="padding-left: 0px;">
                        {!! Form::text('test['.$ke.'][test_name]', $test->test_name, ['class' => 'form-control', 'placeholder' =>
                        __('patient.test_name')]) !!}
                    </div>
                    <div class="col-md-5">
                        <div class="form-group">
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fa fa-sticky-note-o"></i>
                                </span>
                                {!! Form::text('test['.$ke.'][notes]', $test->notes, ['class' => 'form-control',
                                'placeholder'
                                =>
                                __('patient.test_notes')]) !!}
                            </div>
                        </div>
                    </div>
                    {!! Form::hidden('test['.$ke.'][test_id]', $test->id) !!}
                    <div class="col-md-2">
                        <a class="delete_test_row"
                            style="color: brown; font-size: 18px; cursor: pointer; border: none; background: none; test-decoration:none;">x</a>
                    </div>
                </div>
                    
                @endforeach
                @else
                <div class="test_row">
                    <div class="col-md-5" style="padding-left: 0px;">
                        {!! Form::text('test[0][test_name]', null, ['class' => 'form-control', 'placeholder' =>
                        __('patient.test_name')]) !!}
                    </div>
                    <div class="col-md-5">
                        <div class="form-group">
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fa fa-sticky-note-o"></i>
                                </span>
                                {!! Form::text('test[0][notes]', null, ['class' => 'form-control',
                                'placeholder'
                                =>
                                __('patient.test_notes')]) !!}
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <a class="delete_test_row"
                            style="color: brown; font-size: 18px; cursor: pointer; border: none; background: none; test-decoration:none;">x</a>
                    </div>
                </div>
                @endif
            </div>
        </div>
        <div class="row" style="padding-left: 30px;">
            <button class="btn btn-primary btn-sm" id="add_test_row">@lang('patient.add_test')</button>
        </div>
        @endif
        <br>
        <br>
        @php
            $this_patient_id = App\User::where('username', $patient_code)->select('id')->first();
        @endphp
        <div class="row" style="margin: 20px;">
            <a href="{{action('PatientController@show', $this_patient_id)}}" class="btn btn-danger pull-left active" role="button" aria-pressed="true">@lang('patient.back')</a>
            <button class="btn btn-primary pull-right" id="prescription_submit"
                type="submit">@lang('messages.submit')</button>
        </div>
      
    </div>
</div>


{!! Form::close() !!}
