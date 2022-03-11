@if (empty($prescription))
@if (request()->session()->get('business.is_patient'))
{!! Form::open(['url' => action('TestController@upload'), 'method' => 'post', 'id' =>
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
    <h4 class="modal-title">@lang('patient.add_new_test')</h4>
    @else
    <h4 class="modal-title">@lang('patient.edit_test')</h4>
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
                {!! Form::label('laboratory', __('patient.laboratory') . ':' ) !!}
                {!! Form::text('laboratory_name', null , ['class' => 'form-control', 'id' => 'laboratory_name', 'placeholder' => __('patient.laboratory'), 'style' => 'width:100%;']); !!}
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
      
        <div class="col-md-6">
            <div class="form-group">
                {!! Form::label('test_file', __('patient.report_file') ) !!}
                {!! Form::file('test_file', ['files' => true]); !!}

            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                {!! Form::label('bill_file', __('patient.bill_file')  ) !!}
                {!! Form::file('bill_file', ['files' => true]); !!}

            </div>
        </div>
     

        @if (request()->session()->get('business.is_patient'))
        <div class="clearfix"></div>

        <div class="col-md-12" style="padding-left: 30px;" id="medicine_row">
            <div class="row">
                {!! Form::label('test', __('patient.test') . ':*' ) !!}
            </div>
            <div class="row" id="test_rows">
               
                <div class="test_row">
                    <div class="col-md-4" style="padding-left: 0px;">
                        {!! Form::text('test[0][test_name]', null, ['class' => 'form-control', 'placeholder' =>
                        __('patient.test_name')]) !!}
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fa fa-money"></i>
                                </span>
                               {!! Form::text('test[0][amount]', null, ['class' => 'form-control', 'placeholder' =>  __('patient.amount'), 'id' => 'pharmacy_amount']) !!}
                            </div>
                        </div>
                    </div>
                 
                    <div class="col-md-3">
                        <a class="delete_test_row"
                            style="color: brown; font-size: 18px; cursor: pointer; border: none; background: none; test-decoration:none;">x</a>
                    </div>
                    <div class="clearfix"></div>
                </div>
               
            </div>
        </div> 
        <div class="row" style="padding-left: 30px;">
            <button class="btn btn-primary btn-sm" id="add_test_row">@lang('patient.add_test')</button>
        </div>
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
