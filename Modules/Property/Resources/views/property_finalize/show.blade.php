<div class="modal-dialog" role="document" style="width: 55%">
    <div class="modal-content">

        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                    aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">@lang( 'property::lang.view' )</h4>
        </div>

        <div class="modal-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="">
                        <div class="well">
                            <strong>@lang('property::lang.customer'): </strong>

                            {{ $property_sell->customer_name }}<br>
                        </div>
                        <div class="well">
                            <strong>@lang('property::lang.reserved_payment'): </strong> <br>
                            <strong>@lang('property::lang.invoice_no'): {{ $property_sell->invoice_no }}</strong> <br>


                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="well">
                        <strong>@lang('property::lang.project'): </strong> {{$property_sell->property_name}}<br>
                        <strong>@lang('property::lang.block_no'): </strong> {{$property_sell->block_number}}<br>
                        <strong>@lang('property::lang.block_extent'): </strong>
                        {{@num_format($property_sell->block_extent)}}<br>
                        <strong>@lang('property::lang.sold_date'): </strong>
                        {{@format_date($property_sell->transaction_date)}}<br>
                        <strong>@lang('property::lang.block_sold_price'):
                        </strong>{{@num_format($property_sell->block_sold_price)}} <br>
                    </div>
                </div>
            </div>
            <input type="hidden" name="transaction_id" value="{{$property_sell->transaction_id}}">
            <input type="hidden" name="property_id" value="{{$property_sell->property_id}}">
            <input type="hidden" name="block_id" value="{{$property_sell->id}}">
            <input type="hidden" name="property_sell_line_id" value="{{$property_sell->property_sell_line_id}}">
            <div class="col-md-12">
                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('date', __( 'property::lang.date' ) . ':*') !!}
                        <br>{{@format_date($property_finalize->date)}}

                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('balance_amount', __( 'property::lang.balance_amount' )) !!}
                        <br>{{@num_format($property_finalize->balance_amount)}}

                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('finance_option_id', __( 'property::lang.finance_option' )) !!}
                        <br>{{$finance_options[$property_finalize->finance_option_id]}}

                    </div>
                </div>

                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('down_payment', __( 'property::lang.down_payment' )) !!}
                        <br>{{@num_format($property_finalize->down_payment)}}

                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('easy_payment', __( 'property::lang.easy_payment' )) !!}
                        <br>{{ucfirst($property_finalize->easy_payment)}}

                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('no_of_installment', __( 'property::lang.no_of_installment' )) !!}
                        <br>{{$property_finalize->no_of_installment}}

                    </div>
                </div>
                <div class="installment_div @if($installments->count() == 0) hide @endif">
                    <div class="row">
                        <div class="col-md-4"></div>
                        <div class="col-md-4"><b>@lang('property::lang.installment_amount')*</b></div>
                        <div class="col-md-4"><b>@lang('property::lang.installment_date')*</b></div>
                    </div>
                    <div class="row installment_row">
                        @foreach ($installments as $installment)
                        <div class="col-md-4 text-center"><b>installment {{$loop->index+1}}</b></div>
                        <div class="col-md-4">
                            <input type="text" disabled name="installments[{{$loop->index}}][amount]"
                                class="input-number form-control" value="{{$installment->amount}}" required
                                placeholder="@lang('property::lang.amount')">
                            <input type="hidden" name="installments[{{$loop->index}}][installment_no]"
                                class="input-number form-control" value="{{$installment->installment_no}}" required">
                            <input type="hidden" name="installments[{{$loop->index}}][installment_id]"
                                class="input-number form-control" value="{{$installment->id}}" required">
                        </div>
                        <div class="col-md-4">
                            <input type="text" disabled name="installments[{{$loop->index}}][date]"
                                class="input-number form-control installment_date"
                                value="{{@format_date($installment->date)}}" required
                                placeholder="@lang('property::lang.date')">
                        </div>
                        <br><br>
                        @endforeach
                    </div>

                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('installment_amount', __( 'property::lang.installment_amount' )) !!}
                        <br>{{ @num_format($property_finalize->installment_amount)}}

                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('first_installment_date', __( 'property::lang.first_installment_date' ))
                        !!}
                        <br> {{@format_date($property_finalize->first_installment_date)}}

                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('installment_cycle_id', __( 'property::lang.installment_cycle' )) !!}
                        <br>{{$installment_cycles[$property_finalize->installment_cycle_id]}}

                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('loan_capital', __( 'property::lang.loan_capital' )) !!}
                        <br>{{@num_format($property_finalize->loan_capital)}}

                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('total_interest', __( 'property::lang.total_interest' )) !!}
                        <br>{{@num_format($property_finalize->total_interest)}}

                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        {!! Form::label('note', __( 'brand.note' )) !!}
                        <br>{{$property_finalize->note}}
                    </div>
                </div>
            </div>

        </div>
        <div class="clearfix"></div>

        <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
        </div>

    </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->

<script>

</script>