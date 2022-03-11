<div class="modal-dialog" role="document" style="width: 55%">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                    aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">@lang( 'property::lang.closed' )</h4>
        </div>

        <div class="modal-body">
            <div class="col-md-12">
                @if ($closed_current_sales->count() == 0)
                <p class="text-center">@lang('property::lang.this_block_is_not_closed_yet')</p>
                @endif
               @foreach ($closed_current_sales as $close_sale)
               <div class="box  box-primary collapsed-box">
                <div class="box-header with-border" data-widget="collapse"  style="cursor: pointer;">
                    <h3 class="box-title">@lang('property::lang.closed') - {{$loop->index+1}}</h3>
                </div>
                <div class="box-body">
                    <div class="col-md-12">
                        <div class="col-md-4">
                            <div class="form-group">
                                {!! Form::label('date', __( 'property::lang.date' ) ) !!}
                                <br>{{@format_date($close_sale->property_finalize->date)}}
        
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                {!! Form::label('sold_date', __( 'property::lang.sold_date' ) ) !!}
                                <br>{{@format_date($close_sale->transaction_date)}}
        
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                {!! Form::label('customer', __( 'property::lang.customer' ) ) !!}
                                <br>{{@format_date($close_sale->customer_name)}}
        
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                {!! Form::label('balance_amount', __( 'property::lang.balance_amount' )) !!}
                                <br>{{@num_format($close_sale->property_finalize->balance_amount)}}
        
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                {!! Form::label('finance_option_id', __( 'property::lang.finance_option' )) !!}
                                <br>{{$finance_options[$close_sale->property_finalize->finance_option_id]}}
        
                            </div>
                        </div>
        
                        <div class="col-md-4">
                            <div class="form-group">
                                {!! Form::label('down_payment', __( 'property::lang.down_payment' )) !!}
                                <br>{{@num_format($close_sale->property_finalize->down_payment)}}
        
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                {!! Form::label('easy_payment', __( 'property::lang.easy_payment' )) !!}
                                <br>{{ucfirst($close_sale->property_finalize->easy_payment)}}
        
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                {!! Form::label('no_of_installment', __( 'property::lang.no_of_installment' )) !!}
                                <br>{{$close_sale->property_finalize->no_of_installment}}
        
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                {!! Form::label('installment_amount', __( 'property::lang.installment_amount' )) !!}
                                <br>{{ @num_format($close_sale->property_finalize->installment_amount)}}
        
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                {!! Form::label('first_installment_date', __( 'property::lang.first_installment_date' ))
                                !!}
                                <br> {{@format_date($close_sale->property_finalize->first_installment_date)}}
        
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                {!! Form::label('installment_cycle_id', __( 'property::lang.installment_cycle' )) !!}
                                <br>{{$installment_cycles[$close_sale->property_finalize->installment_cycle_id]}}
        
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                {!! Form::label('loan_capital', __( 'property::lang.loan_capital' )) !!}
                                <br>{{@num_format($close_sale->property_finalize->loan_capital)}}
        
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                {!! Form::label('total_interest', __( 'property::lang.total_interest' )) !!}
                                <br>{{@num_format($close_sale->property_finalize->total_interest)}}
        
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                {!! Form::label('all_payments_completed', __( 'property::lang.all_payments_completed' )) !!}
                                <br>@if($close_sale->all_payments_completed) Yes @else No @endif
        
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                {!! Form::label('closed_by', __( 'property::lang.closed_by' )) !!}
                                <br>{{$close_sale->username}}
        
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                {!! Form::label('note', __( 'brand.note' )) !!}
                                <br>{{$close_sale->property_finalize->note}}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="box-footer">
                </div>
            </div>
               @endforeach
            </div>

        </div>
        <div class="clearfix"></div>

        <div class="modal-footer">
            <button type="submit" class="btn btn-primary">@lang( 'messages.save' )</button>
            <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
        </div>
    </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->
