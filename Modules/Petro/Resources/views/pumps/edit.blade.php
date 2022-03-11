<div class="modal-dialog" role="document">
    <div class="modal-content">

        {!! Form::open(['url' => action('\Modules\Petro\Http\Controllers\PumpController@update', $pump->id), 'method' =>
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
                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Form::label('pump_no', __( 'petro::lang.pump_no' ) . ':*') !!}
                            {!! Form::text('pump_no', $pump->pump_no, ['class' => 'form-control pump_no', 'required',
                            'placeholder' => __(
                            'petro::lang.pump_no' ) ]); !!}
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Form::label('pump_name', __( 'petro::lang.pump_name' ) . ':*') !!}
                            {!! Form::text('pump_name', $pump->pump_name, ['class' => 'form-control pump_name',
                            'required',
                            'placeholder' => __(
                            'petro::lang.pump_name' ) ]); !!}
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Form::label('location_id', __( 'petro::lang.branch' ) . ':*') !!}
                            {!! Form::select('location_id', $locations, $pump->location_id , ['class' => 'form-control
                            select2
                            fuel_tank_location', 'required',
                            'placeholder' => __(
                            'petro::lang.please_select' ), 'style' => 'width: 100%;']); !!}
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Form::label('product_id', __( 'petro::lang.product' ) . ':*') !!}
                            {!! Form::select('product_id', $products, $pump->product_id , ['class' => 'form-control
                            select2
                            ', 'required',
                            'placeholder' => __(
                            'petro::lang.please_select' ), 'style' => 'width: 100%;']); !!}
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Form::label('installation_date', __( 'petro::lang.installation_date' ) . ':*') !!}
                            {!! Form::text('installation_date',  \Carbon::parse($pump->transaction_date)->format('m/d/Y'),
                            ['class' => 'form-control
                            fuel_tank_date',
                            'required', 'placeholder' => __(
                            'petro::lang.installation_date' ) ]); !!}
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Form::label('transaction_date', __( 'petro::lang.transaction_date' ) . ':*') !!}
                            {!! Form::text('transaction_date', \Carbon::parse($pump->transaction_date)->format('m/d/Y'),
                            ['class' => 'form-control fuel_tank_date',
                            'required', 'placeholder' => __(
                            'petro::lang.transaction_date' ) ]); !!}
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Form::label('bulk_sale_meter', __( 'petro::lang.bulk_sale_meter' ) . ':*') !!}
                            {!! Form::select('bulk_sale_meter', ['0' => 'No', '1' => 'Yes'], $pump->bulk_sale_meter ,
                            ['class' =>
                            'form-control select2
                            bulk_sale_meter', 'required',
                            'placeholder' => __(
                            'petro::lang.please_select' ), 'style' => 'width: 100%;']); !!}
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Form::label('meter_value', __( 'petro::lang.meter_value' ) . ':*') !!}
                            {!! Form::text('meter_value', $pump->starting_meter, ['class' => 'form-control meter_value input_number', 'readonly',
                            'required', 'placeholder' => __(
                            'petro::lang.meter_value' ) ]); !!}
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Form::label('fuel_tank_id', __( 'petro::lang.fuel_tank' ) . ':*') !!}
                            {!! Form::select('fuel_tank_id', $tanks, $pump->fuel_tank_id , ['class' => 'form-control select2
                            ', 'required',
                            'placeholder' => __(
                            'petro::lang.please_select' ), 'style' => 'width: 100%;']); !!}
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
        $('.fuel_tank_location').select2();
        $('.fuel_tank_product').select2();
        $('.fuel_tank_date').datepicker();
    </script>