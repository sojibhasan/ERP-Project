<div class="modal-dialog" role="document" style="width: 55%">
    <div class="modal-content">

        {!! Form::open(['url' => action('\Modules\Property\Http\Controllers\PropertyFinalizeController@update',
        $property_finalize->id), 'method'
        =>
        'put', 'id' => 'property_finalize_form' ]) !!}

        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                    aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">@lang( 'property::lang.edit_finalize' )</h4>
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
                        {!! Form::text('date', @format_date($property_finalize->date), ['placeholder' => __(
                        'property::lang.date' ),
                        'required', 'class' => 'form-control']); !!}

                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('balance_amount', __( 'property::lang.balance_amount' ) . ':*') !!}
                        {!! Form::text('balance_amount', $property_finalize->balance_amount , ['placeholder' => __(
                        'property::lang.balance_amount' ),
                        'required', 'class' => 'form-control']); !!}

                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('finance_option_id', __( 'property::lang.finance_option' ) . ':*') !!}
                        {!! Form::select('finance_option_id', $finance_options, $property_finalize->finance_option_id ,
                        ['placeholder' => __( 'lang_v1.please_select' ),
                        'required', 'class' => 'form-control select2']); !!}

                    </div>
                </div>

                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('down_payment', __( 'property::lang.down_payment' ) . ':*') !!}
                        {!! Form::text('down_payment', $property_finalize->down_payment , ['placeholder' => __(
                        'property::lang.down_payment' ),
                        'required', 'class' => 'form-control']); !!}

                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('easy_payment', __( 'property::lang.easy_payment' ) . ':*') !!}
                        {!! Form::select('easy_payment', ['no' => 'No', 'yes' => 'Yes'],$property_finalize->easy_payment
                        , ['placeholder' => __( 'lang_v1.please_select' ),
                        'required', 'class' => 'form-control select2']); !!}

                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('no_of_installment', __( 'property::lang.no_of_installment' ) . ':*') !!}
                        {!! Form::text('no_of_installment', $property_finalize->no_of_installment , ['placeholder' =>
                        __(
                        'property::lang.no_of_installment' ),
                        'required', 'class' => 'form-control']); !!}

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
                            <input type="text" name="installments[{{$loop->index}}][amount]" class="input-number form-control" value="{{$installment->amount}}" required
                                placeholder="@lang('property::lang.amount')">
                            <input type="hidden" name="installments[{{$loop->index}}][installment_no]" class="input-number form-control" value="{{$installment->installment_no}}" required">
                            <input type="hidden" name="installments[{{$loop->index}}][installment_id]" class="input-number form-control" value="{{$installment->id}}" required">
                        </div>
                        <div class="col-md-4">
                            <input type="text" name="installments[{{$loop->index}}][date]" class="input-number form-control installment_date" value="{{@format_date($installment->date)}}" required
                                placeholder="@lang('property::lang.date')">
                        </div>
                        <br><br>
                        @endforeach
                    </div>

                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('installment_amount', __( 'property::lang.installment_amount' ) . ':*') !!}
                        {!! Form::text('installment_amount', $property_finalize->installment_amount , ['placeholder' =>
                        __(
                        'property::lang.installment_amount' ),
                        'required', 'class' => 'form-control']); !!}

                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('first_installment_date', __( 'property::lang.first_installment_date' ) . ':*')
                        !!}
                        {!! Form::text('first_installment_date',
                        @format_date($property_finalize->first_installment_date) , ['placeholder' => __(
                        'property::lang.first_installment_date' ),
                        'required', 'class' => 'form-control']); !!}

                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('installment_cycle_id', __( 'property::lang.installment_cycle' ) . ':*') !!}
                        {!! Form::select('installment_cycle_id', $installment_cycles,
                        $property_finalize->installment_cycle_id , ['placeholder' => __(
                        'property::lang.please_select' ),
                        'required', 'class' => 'form-control select2']); !!}

                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('loan_capital', __( 'property::lang.loan_capital' ) . ':*') !!}
                        {!! Form::text('loan_capital', $property_finalize->loan_capital , ['placeholder' => __(
                        'property::lang.loan_capital' ),
                        'required', 'class' => 'form-control']); !!}

                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('total_interest', __( 'property::lang.total_interest' ) . ':*') !!}
                        {!! Form::text('total_interest', $property_finalize->total_interest , ['placeholder' => __(
                        'property::lang.total_interest' ),
                        'required', 'class' => 'form-control']); !!}

                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        {!! Form::label('attachment', __( 'lang_v1.add_image_document' )) !!}
                        {!! Form::file('attachment', ['files' => true]); !!}
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        {!! Form::label('note', __( 'brand.note' )) !!}
                        {!! Form::textarea('note', $property_finalize->note , ['class' => 'form-control', 'placeholder'
                        => __( 'brand.note'
                        ), 'rows' => 4]);
                        !!}
                    </div>
                </div>
                <input type="hidden" name="custom_payments" id="custom_payments" value="{{$finance_option->custom_payments}}">
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
    // $('#location_id option:eq(1)').attr('selected', 'selected');
    $('#date').datepicker('setDate', new Date())
    $('#first_installment_date').datepicker('setDate', new Date())
    $('#finance_option_id').trigger('change');
    $('#finance_option_id').change(function () {
        finance_option_id = $(this).val();
        $.ajax({
            method: 'get',
            url: '/property/finance-options/get-details/'+finance_option_id,
            data: {  },
            success: function(result) {
                if(result.finance_option.custom_payments === 'yes'){
                    $('input#custom_payments').val(result.finance_option.custom_payments);
                }else{
                    $('input#custom_payments').val(result.finance_option.custom_payments);
                }
            },
        });
    });

    $('#no_of_installment').change(function () {
        let no_of_installment = parseInt($(this).val()); 
        if($('#easy_payment').val() === 'yes' && $('#custom_payments').val() === 'yes' && no_of_installment > 0){
            $('.installment_div').removeClass('hide');
            $('.installment_row').empty();

            for (let index = 0; index < no_of_installment; index++) {
               $('.installment_row').append(
                   `<div class="col-md-4 text-center"><b>installment ${index+1}</b></div>
                    <div class="col-md-4">
                        <input type="text" name="installments[${index}][amount]" class="input-number form-control" required
                            placeholder="@lang('property::lang.amount')">
                        <input type="hidden" name="installments[${index}][installment_no]" class="input-number form-control" required value="${index+1}">
                    </div>
                    <div class="col-md-4">
                        <input type="text" name="installments[${index}][date]" class="input-number form-control installment_date" required
                            placeholder="@lang('property::lang.date')">
                    </div>
                    <br><br>`
               );
                
            }
        }else{
            $('.installment_div').addClass('hide');
        }
        $('.installment_date').datepicker('setDate', new Date());
    })
</script>