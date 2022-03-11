<div class="modal-dialog" role="document" style="width: 55%">
    <div class="modal-content">
        {!! Form::open(['url' =>
        action('\Modules\Property\Http\Controllers\CloseCurrentSaleController@update', $property_finalize->id), 'method'
        =>
        'put', 'id' => 'property_finalize_form' ]) !!}
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                    aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">@lang( 'property::lang.close' ) - {{$property_finalize->id}}</h4>
        </div>

        <div class="modal-body">
            <input type="hidden" name="transaction_id" value="{{$property_sell->transaction_id}}">
            <input type="hidden" name="property_id" value="{{$property_sell->property_id}}">
            <input type="hidden" name="block_id" value="{{$property_sell->id}}">
            <input type="hidden" name="finalize_id" value="{{$property_finalize->id}}">
            <input type="hidden" name="property_sell_line_id" value="{{$property_sell->property_sell_line_id}}">
            <div class="col-md-12">
                <div class="col-md-3">
                    <div class="form-group">
                        {!! Form::label('customer', __( 'property::lang.customer' )) !!}
                        <br>{{ $property_sell->customer_name }}

                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        {!! Form::label('customer_code', __( 'property::lang.customer_code' )) !!}
                        <br>{{ $property_sell->contact_id }}

                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        {!! Form::label('sold_date', __( 'property::lang.sold_date' )) !!}
                        <br>{{@format_date($property_sell->transaction_date)}}

                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        {!! Form::label('block_sold_price', __( 'property::lang.block_sold_price' )) !!}
                        <br>{{@num_format($property_sell->block_sold_price)}}

                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        {!! Form::label('total_amount_paid', __( 'property::lang.total_amount_paid' )) !!}
                        <br>{{@num_format($property_sell->total_amount_paid)}}

                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        {!! Form::label('balance_amount', __( 'property::lang.balance_amount' )) !!}
                        <br>{{@num_format($property_finalize->balance_amount)}}

                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        {!! Form::label('finance_option_id', __( 'property::lang.finance_option' )) !!}
                        <br>{{$finance_options[$property_finalize->finance_option_id]}}

                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group">
                        {!! Form::label('down_payment', __( 'property::lang.down_payment' )) !!}
                        <br>{{@num_format($property_finalize->down_payment)}}

                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        {!! Form::label('easy_payment', __( 'property::lang.easy_payment' )) !!}
                        <br>{{ucfirst($property_finalize->easy_payment)}}

                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        {!! Form::label('no_of_installment', __( 'property::lang.no_of_installment' )) !!}
                        <br>{{$property_finalize->no_of_installment}}

                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        {!! Form::label('installment_amount', __( 'property::lang.installment_amount' )) !!}
                        <br>{{ @num_format($property_finalize->installment_amount)}}

                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        {!! Form::label('first_installment_date', __( 'property::lang.first_installment_date' ))
                        !!}
                        <br> {{@format_date($property_finalize->first_installment_date)}}

                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        {!! Form::label('installment_cycle_id', __( 'property::lang.installment_cycle' )) !!}
                        <br>{{$installment_cycles[$property_finalize->installment_cycle_id]}}

                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        {!! Form::label('loan_capital', __( 'property::lang.loan_capital' )) !!}
                        <br>{{@num_format($property_finalize->loan_capital)}}

                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        {!! Form::label('total_interest', __( 'property::lang.total_interest' )) !!}
                        <br>{{@num_format($property_finalize->total_interest)}}

                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        {!! Form::label('closed_by', __( 'property::lang.closed_by' )) !!}
                        <br>{{Auth::user()->username}}
                    </div>
                </div>
                <div class="clearfix"></div>
                <div class="col-md-3">
                    <div class="form-group">
                        {!! Form::label('reason_id', __( 'property::lang.reasons' )) !!}
                        {!! Form::select('reason_id[]', $reasons, $property_finalize->reason_id, ['class'=> 'form-control select2', 'multiple', 'id' => 'reason_id',
                        'style' => 'width: 100%;']) !!}
                    </div>
                </div>
                <div class="col-md-3">
                   <div class="form-group">
                    <div class="checkbox">
                        <label>
                            {!! Form::checkbox('all_payments_completed', 1, $property_finalize->all_payments_completed, ['class' => 'input-icheck',
                            'id' => 'all_payments_completed']); !!}
                            <b>@lang('property::lang.all_payments_completed')</b>
                        </label>
                    </div>
                   </div>
                </div>
            </div>

        </div>
        <div class="clearfix"></div>

        <div class="modal-footer">
            <button type="submit" class="btn btn-primary">@lang( 'messages.save' )</button>
            <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
        </div>
        {!! Form::close() !!}
    </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->

<script>
    $('.select2').select2();

    @if(count($reasons) > 0)
    $('#reason_id').attr('required', true);
    @endif
</script>