<div class="modal-dialog" role="document" style="width: 70%;">
    <div class="modal-content">

        {!! Form::open(['url' => action('\Modules\Petro\Http\Controllers\DipManagementController@saveResettingDip'),
        'method' =>
        'post',
        'id' =>
        'dip_resetting_form' ]) !!}

        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                    aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">@lang( 'petro::lang.add_meter_reset' )</h4>
        </div>

        <div class="modal-body">
            <div class="col-md-12">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            {!! Form::label('meter_reset_ref_no', __( 'petro::lang.meter_reset_ref_no' ) . ':*') !!}
                            {!! Form::text('meter_reset_ref_no', $ref_no, ['class' => 'form-control meter_reset_ref_no',
                            'required', 'readonly',
                            'placeholder' => __(
                            'petro::lang.meter_reset_ref_no' ) ]); !!}
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            {!! Form::label('date_and_time', __( 'petro::lang.date_and_time' ) . ':*') !!}
                            {!! Form::text('date_and_time', null, ['class' => 'form-control date_and_time', 'required', 'readonly',
                            'placeholder' => __(
                            'petro::lang.date_and_time' ) ]); !!}
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            {!! Form::label('location_id', __( 'petro::lang.location' ) . ':*') !!}
                            {!! Form::select('meter_reset_location_id', $business_locations, null , ['class' => 'form-control
                            select2
                            fuel_tank_location', 'required', 'id' => 'meter_reset_location_id',
                            'placeholder' => __(
                            'petro::lang.please_select' ), 'style' => 'width: 100%;']); !!}
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            {!! Form::label('pumps', __('petro::lang.pumps') . ':') !!}
                            {!! Form::select('meter_reset_pumps', $pumps, null, ['class' => 'form-control select2', 'placeholder'
                            => __('petro::lang.please_select'), 'id' => 'add_reset_pump_id', 'style' => 'width:100%']); !!}
                        </div>
                    </div>


                    <div class="col-md-4">
                        <div class="form-group">
                            {!! Form::label('meter_resettings_product_name', __( 'petro::lang.product_name' ) . ':') !!}
                            {!! Form::text('meter_resettings_product_name', null, ['class' => 'form-control meter_resettings_product_name', 'required',
                            'readonly',
                            'placeholder' => __(
                            'petro::lang.product_name' ) ]); !!}
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            {!! Form::label('meter_resettings_tank_name', __( 'petro::lang.tank_name' ) . ':') !!}
                            {!! Form::text('meter_resettings_tank_name', null, ['class' => 'form-control meter_resettings_tank_name', 'required',
                            'readonly',
                            'placeholder' => __(
                            'petro::lang.tank_name' ) ]); !!}
                        </div>
                    </div>


                    <div class="col-md-4">
                        <div class="form-group">
                            {!! Form::label('last_meter_current_meter', __( 'petro::lang.last_meter_current_meter' ) . ':') !!}
                            {!! Form::text('last_meter_current_meter', null, ['class' => 'form-control last_meter_current_meter input_number',
                            'required', 'readonly',
                            'placeholder' => __(
                            'petro::lang.last_meter_current_meter' ) ]); !!}
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            {!! Form::label('reset_new_meter', __( 'petro::lang.reset_new_meter' ) . ':*') !!}
                            {!! Form::text('new_reset_meter', null, ['class' => 'form-control reset_new_meter input_number', 'required',
                            'placeholder' => __(
                            'petro::lang.reset_new_meter' ) ]); !!}
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            {!! Form::label('reason', __( 'petro::lang.reason' ) . ':') !!}
                            {!! Form::textarea('meter_resettings_reason', null, ['class' => 'form-control reason',  'rows' =>
                            4,
                            'placeholder' => __(
                            'petro::lang.reason' ) ]); !!}
                        </div>
                    </div>

                </div>
            </div>
            <div class="clearfix"></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary add_meter_resetting_btn">@lang( 'messages.save' )</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
            </div>

            {!! Form::close() !!}
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->

    <script>
        $('#date_and_time').datepicker("setDate", new Date());
        $('#transaction_date').datepicker("setDate", new Date());
        $('#add_reset_tank_id').select2();
        $('#location_id').select2();

        
        $('#add_reset_pump_id').change(function(){
            $.ajax({
                method: 'get',
                url: '{{action('\Modules\Petro\Http\Controllers\MeterResettingController@getPumpDetails')}}',
                data: { pump_id : $(this).val() },
                success: function(result) {
                    $('#meter_resettings_product_name').val(result.product_name);
                    $('#meter_resettings_tank_name').val(result.fuel_tank_number);
                    $('#last_meter_current_meter').val(result.last_meter_reading);
                },
            });
        });
    </script>