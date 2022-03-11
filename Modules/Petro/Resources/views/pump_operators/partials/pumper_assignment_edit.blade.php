<div class="modal-dialog" role="document">
    <div class="modal-content">

        {!! Form::open(['url' => action('\Modules\Petro\Http\Controllers\PumpOperatorAssignmentController@update', $pump_assignment->id), 'method' =>
        'put',
        'id' =>
        'add_pumps_form' ]) !!}

        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                    aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">@lang( 'petro::lang.add_pump' )</h4>
        </div>

        <div class="modal-body">
            <div class="col-md-12">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            {!! Form::label('starting_meter', __( 'petro::lang.starting_meter' ) . ':*') !!}
                            {!! Form::text('starting_meter', $pump_assignment->starting_meter, ['class' => 'form-control
                            starting_meter', 'required', 'readonly',
                            'placeholder' => __(
                            'petro::lang.starting_meter' ) ]); !!}
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            {!! Form::label('closing_meter', __( 'petro::lang.reconfirm_meter' ) . ':*') !!}
                            {!! Form::text('closing_meter', $pump_assignment->closing_meter, ['class' => 'form-control
                            closing_meter',
                            'placeholder' => __(
                            'petro::lang.closing_meter' ) ]); !!}
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            {!! Form::label('status', __( 'petro::lang.status' ) . ':*') !!} <br>
                            <input type="checkbox" @if($pump_assignment->status == 'open' ) checked @endif name="status" id="toggle-two" data-toggle="toggle">
                        </div>
                    </div>
                  
                </div>
            </div>
            <div class="clearfix"></div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary add_fuel_tank_btn">@lang( 'messages.save' )</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
            </div>

            {!! Form::close() !!}
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->

    <script>
       $('#toggle-two').bootstrapToggle({
            on: 'Open',
            off: 'Close',
            width: 100,
            onstyle: 'success',
            offstyle: 'danger'
        });
    </script>