<div class="modal-dialog" role="document" style="width: 50%;">
    <div class="modal-content">

        {!! Form::open(['url' => action('\Modules\Ran\Http\Controllers\GoldSmithController@update', $goldsmith->id),
        'method' => 'put',
        'id' =>
        'add_goldsmith_form' ]) !!}

        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                    aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">@lang( 'ran::lang.edit_goldsmith' )</h4>
        </div>

        <div class="modal-body">
            <div class="col-md-12">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Form::label('name', __( 'ran::lang.name' ) . ':*') !!}
                            {!! Form::text('name', $goldsmith->name, ['class' => 'form-control name',
                            'required',
                            'placeholder' => __(
                            'ran::lang.name' ) ]); !!}
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Form::label('email', __( 'ran::lang.email' ) . ':*') !!}
                            {!! Form::text('email', $goldsmith->email, ['class' => 'form-control email',
                            'required',
                            'placeholder' => __(
                            'ran::lang.email' ) ]); !!}
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Form::label('mobile', __( 'ran::lang.mobile' ) . ':*') !!}
                            {!! Form::text('mobile', $goldsmith->mobile, ['class' => 'form-control mobile input_number',
                            'required',
                            'placeholder' => __(
                            'ran::lang.mobile' ) ]); !!}
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Form::label('address', __( 'ran::lang.address' ) . ':*') !!}
                            {!! Form::text('address', $goldsmith->address, ['class' => 'form-control address',
                            'required',
                            'placeholder' => __(
                            'ran::lang.address' ) ]); !!}
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Form::label('landline', __( 'ran::lang.landline' ) . ':*') !!}
                            {!! Form::text('landline', $goldsmith->landline, ['class' => 'form-control landline input_number',
                            'required',
                            'placeholder' => __(
                            'ran::lang.landline' ) ]); !!}
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Form::label('dob', __( 'ran::lang.dob' ) . ':*') !!}
                            {!! Form::text('dob', null, ['class' => 'form-control dob',
                            'required', 'readonly',
                            'placeholder' => __(
                            'ran::lang.dob' ) ]); !!}
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Form::label('employee_number', __( 'ran::lang.employee_number' ) . ':*') !!}
                            {!! Form::text('employee_number', $goldsmith->employee_number, ['class' => 'form-control
                            employee_number',
                            'required',
                            'placeholder' => __(
                            'ran::lang.employee_number' ) ]); !!}
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Form::label('opening_gold_qty', __( 'ran::lang.opening_gold_qty' ) . ':*') !!}
                            {!! Form::text('opening_gold_qty', $goldsmith->opening_gold_qty, ['class' => 'form-control input_number
                            opening_gold_qty',
                            'required',
                            'placeholder' => __(
                            'ran::lang.opening_gold_qty' ) ]); !!}
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
        $('#dob').datepicker('setDate', "{{!empty($goldsmith->dob) ? \Carbon::parse($goldsmith->dob)->format('m-d-Y'): null}}" );
       
    </script>