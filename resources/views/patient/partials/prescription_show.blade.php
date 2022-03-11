<div class="modal-dialog" role="document" style="width: 65%" >
    <div class="modal-content print">
        <div class="modal-header">
            <h5 class="modal-title">Prescription</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-body" id="printable">
            <div class="col-md-4">
                <div class="form-group">
                    {!! Form::label('patient_code', __('patient.patient_code') . ':' ) !!}
                    {{$patient_code}}
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    {!! Form::label('hospital_name', __('patient.hospital_name') . ':' ) !!}
                    {{$prescription->hospital}}
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    {!! Form::label('doctor_name', __('patient.doctor_name') . ':' ) !!}
                    {{$prescription->doctor}}
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    {!! Form::label('medicine', __('patient.medicine') . ':' ) !!}
                    @foreach ($medicines as $med)
                        <div class="row">
                            <div class="col-md-4">{{$med->medicine_name}}</div>
                            <div class="col-md-4">{{$med->notes}}</div>
                        </div>
                    @endforeach
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    {!! Form::label('medicine', __('patient.medicine') . ':' ) !!}
                    @foreach ($medicines as $med)
                        <div class="row">
                            <div class="col-md-4">{{$med->medicine_name}}</div>
                            <div class="col-md-4">{{$med->notes}}</div>
                        </div>
                    @endforeach
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    {!! Form::label('test', __('patient.test') . ':' ) !!}
                    @foreach ($tests as $test)
                        <div class="row">
                            <div class="col-md-4">{{$test->test_name}}</div>
                            <div class="col-md-4">{{$test->notes}}</div>
                        </div>
                    @endforeach
                </div>
            </div>


            <div class="col-md-4">
                <div class="form-group">
                    {!! Form::label('signature', __('patient.signature') . ':' ) !!}
                    {{$prescription->signature}}
                </div>
            </div>

            <div class="clearfix"></div>
            <hr>
            <div class="modal-footer">
                <div class="row">
                    {{-- <button class="btn btn-primary hidden-print print_prescription"><span class="glyphicon glyphicon-print" aria-hidden="true"></span> Print</button> --}}
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>

                </div>
              </div>
        </div>
    </div>
</div>
