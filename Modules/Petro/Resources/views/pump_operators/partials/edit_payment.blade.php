<div class="modal-dialog" role="document">
    <div class="modal-content">

        {!! Form::open(['url' => action('\Modules\Petro\Http\Controllers\PumpOperatorPaymentController@update', $payment->id), 'method' => 'put', 'id' =>
        'customer_reference_add_form' ]) !!}

        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                    aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">@lang( 'petro::lang.edit_payment' )</h4>
        </div>

        <div class="modal-body">
            <div class="col-md-12">
                <div class="form-group">
                    {!! Form::label('payment_amount', __( 'petro::lang.amount' ) . ':*') !!}
                    {!! Form::text('payment_amount', $payment->payment_amount, ['class' => 'form-control amount', 'required',
                    'placeholder' => __(
                    'petro::lang.amount' ) ]); !!}
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    {!! Form::label('payment_type', __( 'petro::lang.branch' ) . ':*') !!}
                    {!! Form::select('payment_type', $payment_types, $payment->payment_type , ['class' => 'form-control select2
                    payment_type', 'required',
                    'placeholder' => __(
                    'petro::lang.please_select' ), 'style' => 'width: 100%;']); !!}
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    {!! Form::label('note', __( 'petro::lang.note' ) . ':*') !!}
                    {!! Form::textarea('note', $payment->note, ['class' => 'form-control note', 'rows' => 3,
                    'placeholder' => __(
                    'petro::lang.note' ) ]); !!}
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
       $('.select2').select2();
    </script>