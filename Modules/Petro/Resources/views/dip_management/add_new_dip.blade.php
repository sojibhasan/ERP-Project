<div class="modal-dialog" role="document" style="width: 70%;">
    <div class="modal-content">

        {!! Form::open(['url' => action('\Modules\Petro\Http\Controllers\DipManagementController@saveNewDip'), 'method' =>
        'post',
        'id' =>
        'add_new_dip_form' ]) !!}

        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                    aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">@lang( 'petro::lang.add_new_dip' )</h4>
        </div>

        <div class="modal-body">
            <div class="col-md-12">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            {!! Form::label('ref_number', __( 'petro::lang.ref_number' ) . ':*') !!}
                            {!! Form::text('ref_number', $ref_no, ['class' => 'form-control ref_number', 'required', 'readonly',
                            'placeholder' => __(
                            'petro::lang.ref_number' ) ]); !!}
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
                            {!! Form::select('location_id', $business_locations, !empty($default_location) ? $default_location : null , ['class' => 'form-control select2
                            fuel_tank_location', 'required',
                            'placeholder' => __(
                            'petro::lang.please_select' ), 'style' => 'width: 100%;']); !!}
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            {!! Form::label('tank_id', __('petro::lang.tanks') . ':') !!}
                            {!! Form::select('tank_id', $tanks, null, ['class' => 'form-control select2', 'placeholder'
                            => __('petro::lang.please_select'), 'id' => 'add_dip_tank_id', 'style' => 'width:100%']); !!}
                        </div>
                    </div>

                    @if($tank_dip_chart_permission)
                    <div class="col-md-4">
                        <div class="form-group">
                            {!! Form::label('tank_manufacturer', __( 'petro::lang.tank_manufacturer' ) . ':*') !!}
                            {!! Form::text('tank_manufacturer', null, ['class' => 'form-control', 'readonly',
                            'required', 'placeholder' => __(
                            'petro::lang.tank_manufacturer' ) ]); !!}
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            {!! Form::label('tank_capacity', __( 'petro::lang.tank_capacity' ) . ':*') !!}
                            {!! Form::text('tank_capacity', null, ['class' => 'form-control input_number', 'readonly',
                            'required', 'placeholder' => __(
                            'petro::lang.tank_capacity' ) ]); !!}
                        </div>
                    </div>
                   
                    <div class="col-md-4">
                        <div class="form-group">
                            {!! Form::label('dip_reading', __( 'petro::lang.dip_reading' ) . ':*') !!}
                            {!! Form::select('dip_reading', [], null, ['class' => 'form-control dip_reading select2', 'required',
                            'placeholder' => __(
                            'petro::lang.please_select' ) ]); !!}
                        </div>
                    </div>
                    @else
                    <div class="col-md-4">
                        <div class="form-group">
                            {!! Form::label('dip_reading', __( 'petro::lang.dip_reading' ) . ':*') !!}
                            {!! Form::text('dip_reading', null, ['class' => 'form-control dip_reading', 'required',
                            'placeholder' => __(
                            'petro::lang.please_select' ) ]); !!}
                        </div>
                    </div>
                    @endif

                    <div class="col-md-4">
                        <div class="form-group">
                            {!! Form::label('fuel_balance_dip_reading', __( 'petro::lang.tank_fuel_balance_dip_reading' ) . ':*') !!}
                            {!! Form::text('fuel_balance_dip_reading', null, ['class' => 'form-control tank_fuel_balance_dip_reading', 'required', 'id' => 'fuel_balance_dip_reading', 'readonly',
                            'placeholder' => __(
                            'petro::lang.tank_fuel_balance_dip_reading' ) ]); !!}
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            {!! Form::label('current_qty', __( 'petro::lang.current_qty' ). ':') !!}
                            {!! Form::text('current_qty', null, ['class' => 'form-control current_qty', 'id' =>'current_qty', 'readonly',
                            'placeholder' => __(
                            'petro::lang.current_qty' ) ]); !!}
                        </div>
                    </div>

                    <div class="clearfix"></div>
                    <div class="col-md-12">
                        <div class="form-group">
                            {!! Form::label('note', __( 'petro::lang.note' ) . ':*') !!}
                            {!! Form::textarea('note', null, ['class' => 'form-control note', 'required', 'rows' => 4,
                            'placeholder' => __(
                            'petro::lang.note' ) ]); !!}
                        </div>
                    </div>

                </div>
            </div>
            <div class="clearfix"></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary add_new_dip_reading_btn">@lang( 'messages.save' )</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
            </div>

            {!! Form::close() !!}
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->

    <script>
      
        $('#date_and_time').datepicker("setDate", new Date());
        $('#transaction_date').datepicker("setDate", new Date());
        $('#tank_id').select2();
        $('#location_id').select2();
        $('#location_id option:eq(1)').attr('selected', true).trigger('change');
        
        @if(!$tank_dip_chart_permission)
            $('#fuel_balance_dip_reading').attr("readonly", false); 
        @endif
        $('#add_dip_tank_id').change(function(){
            let tank_id= $(this).val();

            $.ajax({
                method: 'get',
                url: "/petro/get-tank-balance-by-id/"+tank_id,
                data: {  },
                success: function(result) {
                    $('#tank_manufacturer').val(result.details.tank_manufacturer);
                    $('#tank_capacity').val(result.details.tank_capacity);
                    let html = '<option selected="selected" value="">Please Select</option>';
                    let dip_readings = result.dip_readings;
                    for (const [key, value] of Object.entries(dip_readings)) {
                        html += '<option value="'+value+'">'+key+'</option>';
                    }
                    $('#dip_reading').empty().append(html);
                },
            });
        })
        @if($tank_dip_chart_permission)
        $('#dip_reading').change(function(){
            let tank_dip_reading= $(this).val();

            $.ajax({
                method: 'get',
                url: "/superadmin/tank-dip-chart-details/get-reading-value/"+tank_dip_reading,
                data: {  },
                success: function(result) {
                    $('#fuel_balance_dip_reading').val(result.dip_reading_value);
                },
            });
        })
        @endif
    </script>