@if (empty($prescription))
@if (request()->session()->get('business.is_patient'))
{!! Form::open(['url' => action('MedicineController@upload'), 'method' => 'post', 'id' =>
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
    <h4 class="modal-title">@lang('patient.add_new_medicine')</h4>
    @else
    <h4 class="modal-title">@lang('patient.edit_medicine')</h4>
    @endif
</div>

<div class="modal-body">
    <div class="row">
        <input type="hidden" name="medicine_row_index" id="medicine_row_index" value="0">
        <input type="hidden" name="test_row_index" id="test_row_index" value="0">
        <input type="hidden" name="patient_code" id="patient_code" value="{{$patient_code}}">
        <div class="clearfix"></div>
        <div class="col-md-6 ">
            <div class="form-group">
                {!! Form::label('pharmacy', __('patient.pharmacy') . ':' ) !!}
                {!! Form::text('pharmacy_name', !empty($prescription)?$prescription->pharmacy_name:null , ['class' => 'form-control', 'id' => 'pharmacy_name', 'placeholder' => __('patient.pharmacy'), 'style' => 'width:100%;']); !!}
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                {!! Form::label('date', __('patient.date') . ':' ) !!}
                <div class="input-group">
                    <span class="input-group-addon">
                        <i class="fa fa-calendar-check-o"></i>
                    </span>
                   {!! Form::text('date', null, ['class' => 'form-control', 'required', 'placeholder' =>  __('patient.date'), 'id' => 'pharmacy_date']) !!}
                </div>
            </div>
        </div>
      
        <div class="col-md-12">
            <div class="form-group">
                {!! Form::label('pharmacy_file', __('patient.file') . ':*' ) !!}
                {!! Form::file('pharmacy_file', ['files' => true]); !!}

            </div>
        </div>
     

        @if (request()->session()->get('business.is_patient'))
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
                        <div class="col-md-3" style="padding-left: 0px;">
    
                            {!! Form::text('medicine[0][medicine_name]', null, ['class' => 'form-control', 'placeholder' =>
                            __('patient.medicine_name')]) !!}
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        <i class="fa fa-money"></i>
                                    </span>
                                   {!! Form::text('medicine[0][amount]', null, ['class' => 'form-control', 'placeholder' =>  __('patient.amount'), 'id' => 'pharmacy_amount']) !!}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        <i class="fa fa-money"></i>
                                    </span>
                                   {!! Form::text('medicine[0][qty]', null, ['class' => 'form-control', 'placeholder' =>  __('patient.qty'), 'id' => 'pharmacy_amount']) !!}
                                </div>
                            </div>
                        </div>
                       
                     
                        <div class="col-md-3">
                            <a class="delete_medicine_row"
                                style="color: brown; font-size: 18px; cursor: pointer; border: none; background: none; test-decoration:none;">x</a>
                        </div>
                        <div class="clearfix"></div>
                    </div>

                @endif
            </div>
        </div>
        <div class="row" style="padding-left: 30px;">
            <button class="btn btn-primary btn-sm" id="add_medicine_row">@lang('patient.add_medicine')</button>
        </div>
        <div class="clearfix"></div>
 
        @endif
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
