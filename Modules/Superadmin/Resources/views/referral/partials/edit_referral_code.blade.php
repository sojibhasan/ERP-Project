<div class="modal-dialog" role="document">
    <div class="modal-content">

        {!! Form::open(['url' => action('\Modules\Superadmin\Http\Controllers\ReferralStartingCodeController@update', $starting_code->id), 'method' => 'put',
        'id' =>
        'add_pumps_form' ]) !!}

        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                    aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">@lang( 'superadmin::lang.edit_starting_code' )</h4>
        </div>

        <div class="modal-body">
            <div class="col-md-12">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Form::label('date', __( 'superadmin::lang.date' ) . ':*') !!}
                            {!! Form::text('date', null, ['class' => 'form-control date', 'required',
                            'placeholder' => __(
                            'superadmin::lang.date' ) ]); !!}
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Form::label('referral_group', __( 'superadmin::lang.referral_group' ) . ':*') !!}
                            {!! Form::select('referral_group', $referral_groups, $starting_code->referral_group, ['class' => 'form-control referral_group select2', 'required',
                            'placeholder' => __(
                            'superadmin::lang.please_select' ), 'style' => 'width: 100%;' ]); !!}
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Form::label('prefix', __( 'superadmin::lang.prefix' ) . ':*') !!}
                            {!! Form::text('prefix', $starting_code->prefix, ['class' => 'form-control prefix', 'required',
                            'placeholder' => __(
                            'superadmin::lang.prefix' ) ]); !!}
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Form::label('starting_code', __( 'superadmin::lang.starting_code' ) . ':*') !!}
                            {!! Form::text('starting_code', $starting_code->starting_code, ['class' => 'form-control starting_code', 'required',
                            'placeholder' => __(
                            'superadmin::lang.starting_code' ) ]); !!}
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
        $('.date').datepicker('setDate', '{{@format_date($starting_code->date)}}');
        $('.select2').select2();
    </script>