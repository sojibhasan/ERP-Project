@foreach( $variations as $variation)
<tr>
    <td><span class="sr_number"></span></td>
    <td>
        {{ $product->name }} ({{$variation->sub_sku}})
        @if( $product->type == 'variable' )
        <br />
        (<b>{{ $variation->product_variation->name }}</b> : {{ $variation->name }})
        @endif
    </td>
    <td>
        {!! Form::hidden('purchases[' . $row_count . '][product_id]', $product->id ); !!}
        {!! Form::hidden('purchases[' . $row_count . '][variation_id]', $variation->id , ['class' => 'hidden_variation_id']); !!}
        {!! Form::hidden('purchases[' . $row_count . '][row_count]', $row_count); !!}
        {!! Form::hidden('row_count', $row_count , ['class' => 'row_count']); !!}

        @php
        $check_decimal = 'false';
        if($product->unit->allow_decimal == 0){
        $check_decimal = 'true';
        }
        $currency_precision = config('constants.currency_precision', 2);
        $quantity_precision = config('constants.quantity_precision', 2);
        @endphp
        {!! Form::text('purchases[' . $row_count . '][quantity]', number_format(!empty($temp_qty)?$temp_qty:1,
        $quantity_precision, $currency_details->decimal_separator, $currency_details->thousand_separator), ['class' =>
        'form-control input-sm purchase_quantity input_number mousetrap', 'required', 'data-rule-abs_digit' =>
        $check_decimal, 'data-msg-abs_digit' => __('lang_v1.decimal_value_not_allowed') , 'id' =>
        'product_id'.$product->id]); !!}
        <input type="hidden" class="base_unit_cost" value="{{$variation->default_purchase_price}}">
        <input type="hidden" class="base_unit_selling_price" value="{{$variation->sell_price_inc_tax}}">
        <input type="hidden" class="is_fuel_category" name="is_fuel_category" value="{{$is_fuel_category}}">
        <input type="hidden" class="product_id" name="product_id" value="{{$product->id}}">

        <input type="hidden" name="purchases[{{$row_count}}][product_unit_id]" value="{{$product->unit->id}}">

    </td>
    <td>
        @if(!empty($sub_units))
        <select name="purchases[{{$row_count}}][sub_unit_id]" class="form-control input-sm sub_unit">
            @foreach($sub_units as $key => $value)
            <option value="{{$key}}" data-multiplier="{{$value['multiplier']}}">
                {{$value['name']}}
            </option>
            @endforeach
        </select>
        @else
        {{ $product->unit->short_name }}
        @endif
    </td>
    @php
    $business_id = request()->session()->get('user.business_id');
    $enable_free_qty = App\Business::where('id', $business_id)->select('enable_free_qty')->first()->enable_free_qty;
    @endphp
    @if ($enable_free_qty)
    <td>
        <input style="width: 60px;" type="number" name="purchases[{{$row_count}}][free_qty]"
            class="free_qty form-control" value="">
    </td>
    @endif
    <td>
        <input style="width: 60px;" type="text" name="current_stock" class="current_stock form-control"
            data-orignalstock="{{$current_stock}}" value="{{$current_stock}}" readonly>
    </td>
    <td>
        {!! Form::text('purchases[' . $row_count . '][pp_without_discount]',
        rtrim(rtrim($variation->default_purchase_price, '0'), '.'), ['class' => 'form-control input-sm
        purchase_unit_cost_without_discount input_number', 'required']); !!}
    </td>
    <td>
        {!! Form::text('purchases[' . $row_count . '][discount_percent]', 0, ['class' => 'form-control input-sm
        inline_discounts input_number', 'required']); !!}
    </td>
    <td>
        {!! Form::text('purchases[' . $row_count . '][purchase_price]', rtrim(rtrim($variation->default_purchase_price,
        '0'), '.'), ['class' => 'form-control input-sm purchase_unit_cost input_number', 'required']); !!}
    </td>
    <td class="{{$hide_tax}}">
        <span class="row_subtotal_before_tax display_currency">0</span>
        <input type="hidden" class="row_subtotal_before_tax_hidden" value=0>
    </td>
    <td class="{{$hide_tax}}">
        <div class="input-group">
            <select name="purchases[{{ $row_count }}][purchase_line_tax_id]"
                class="form-control select2 input-sm purchase_line_tax_id" placeholder="'Please Select'">
                <option value="" data-tax_amount="0" @if( $hide_tax=='hide' ) selected @endif>@lang('lang_v1.none')
                </option>
                @foreach($taxes as $tax)
                <option value="{{ $tax->id }}" data-tax_amount="{{ $tax->amount }}" @if( $product->tax == $tax->id &&
                    $hide_tax != 'hide') selected @endif >{{ $tax->name }}</option>
                @endforeach
            </select>
            {!! Form::hidden('purchases[' . $row_count . '][item_tax]', 0, ['class' => 'purchase_product_unit_tax']);
            !!}
            <span class="input-group-addon purchase_product_unit_tax_text">
                0.00</span>
        </div>
    </td>
    <td class="{{$hide_tax}}">
        @php
        $dpp_inc_tax = $variation->dpp_inc_tax;
        if($hide_tax == 'hide'){
        $dpp_inc_tax = $variation->default_purchase_price;
        }

        @endphp
        {!! Form::text('purchases[' . $row_count . '][purchase_price_inc_tax]', $dpp_inc_tax, ['class' => 'form-control
        input-sm purchase_unit_cost_after_tax input_number', 'required']); !!}
    </td>
    <td>
        <input type="text" class="row_subtotal_after_tax_hidden form-control" name="purchases[{{$row_count}}][row_subtotal_after_tax_hidden]" value=0>
    </td>
    <td class="hide @if(!session('business.enable_editing_product_from_purchase')) hide @endif">
        {!! Form::text('purchases[' . $row_count . '][profit_percent]', number_format($variation->profit_percent,
        $currency_precision, $currency_details->decimal_separator, $currency_details->thousand_separator), ['class' =>
        'form-control input-sm input_number profit_percent', 'required']); !!}
    </td>
    <td class="hide">
        @if(session('business.enable_editing_product_from_purchase'))
        {!! Form::text('purchases[' . $row_count . '][default_sell_price]',
        number_format($variation->sell_price_inc_tax, $currency_precision, $currency_details->decimal_separator,
        $currency_details->thousand_separator), ['class' => 'form-control input-sm input_number default_sell_price',
        'required']); !!}
        @else
        {{ number_format($variation->sell_price_inc_tax, $currency_precision, $currency_details->decimal_separator, $currency_details->thousand_separator)}}
        @endif
    </td>
    @if(session('business.enable_lot_number'))
    <td class="hide">
        {!! Form::text('purchases[' . $row_count . '][lot_number]', null, ['class' => 'form-control input-sm']); !!}
    </td>
    @endif
    @if(session('business.enable_product_expiry'))
    <td style="text-align: left;">

        {{-- Maybe this condition for checkin expiry date need to be removed --}}
        @php
        $expiry_period_type = !empty($product->expiry_period_type) ? $product->expiry_period_type : 'month';
        @endphp
        @if(!empty($expiry_period_type))
        <input type="hidden" class="row_product_expiry" value="{{ $product->expiry_period }}">
        <input type="hidden" class="row_product_expiry_type" value="{{ $expiry_period_type }}">

        @if(session('business.expiry_type') == 'add_manufacturing')
        @php
        $hide_mfg = false;
        @endphp
        @else
        @php
        $hide_mfg = true;
        @endphp
        @endif

        <b class="@if($hide_mfg) hide @endif"><small>@lang('product.mfg_date'):</small></b>
        <div class="input-group @if($hide_mfg) hide @endif">
            <span class="input-group-addon">
                <i class="fa fa-calendar"></i>
            </span>
            {!! Form::text('purchases[' . $row_count . '][mfg_date]', null, ['class' => 'form-control input-sm
            expiry_datepicker mfg_date', 'readonly']); !!}
        </div>
        <b><small>@lang('product.exp_date'):</small></b>
        <div class="input-group">
            <span class="input-group-addon">
                <i class="fa fa-calendar"></i>
            </span>
            {!! Form::text('purchases[' . $row_count . '][exp_date]', null, ['class' => 'form-control input-sm
            expiry_datepicker exp_date', 'readonly']); !!}
        </div>
        @else
        <div class="text-center">
            @lang('product.not_applicable')
        </div>
        @endif
    </td>
    @endif
    <td>
        {!! Form::text('purchases[' . $row_count . '][ref_no]', null, ['class' => 'form-control input-sm ref_no', 'required']); !!}
    </td>
    <td>
        <button type="button" class="btn btn-info btn-xs modal_payment" data-toggle="modal"
            data-modal_id="modal_payment_{{$row_count}}"
            data-target="#modal_payment_{{$row_count}}">@lang('purchase.add_payment')</button>
    </td>
    <td>
        {!! Form::text('purchases[' . $row_count . '][payment_amount]', null, ['class' => 'form-control input-sm
        payment_amount_'.$row_count, 'readonly']); !!}
    </td>

    <td><i class="fa fa-times remove_purchase_entry_row text-danger"  data-row_count="{{ $row_count }}" title="Remove" style="cursor:pointer;"></i></td>

    <td class="payment_modal_td_{{$row_count}}" style="visibility: none">
        <div class="modal fade" tabindex="-1" role="dialog" id="modal_payment_{{$row_count}}">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title">@lang('lang_v1.payment')</h4>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-9">
                                <div class="row">
                                    <div id="payment_rows_div_{{$row_count}}">
                                        @include('purchase.partials.payment_row_bulk', ['row_count' => $row_count, 'row_index' => 0])
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <button type="button" class="btn btn-primary btn-block add-payment-row"
                                            data-row_count="{{$row_count}}">@lang('sale.add_payment_row')</button>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="box box-solid bg-orange">
                                    <div class="box-body">
                                        <div class="col-md-12">
                                            <strong>
                                                @lang('lang_v1.total_items'):
                                            </strong>
                                            <br />
                                            <span class="lead text-bold total_quantity_{{$row_count}}">1</span>
                                        </div>

                                        <div class="col-md-12">
                                            <hr>
                                            <strong>
                                                @lang('sale.total_payable'):
                                            </strong>
                                            <br />
                                            <span class="lead text-bold total_payable_span_{{$row_count}}">0</span>
                                            <input type="hidden" id="total_payable_input_{{$row_count}}">
                                        </div>

                                        <div class="col-md-12">
                                            <hr>
                                            <strong>
                                                @lang('lang_v1.total_paying'):
                                            </strong>
                                            <br />
                                            <span class="lead text-bold total_paying_{{$row_count}}">0</span>
                                            <input type="hidden" id="total_paying_input_{{$row_count}}">
                                        </div>

                                        <div class="col-md-12">
                                            <hr>
                                            <strong>
                                                @lang('lang_v1.balance'):
                                            </strong>
                                            <br />
                                            <span class="lead text-bold balance_due_{{$row_count}}">0</span>
                                            <input type="hidden" id="in_balance_due_{{$row_count}}" value=0>
                                        </div>

                                    </div>
                                    <!-- /.box-body -->
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default"
                            data-dismiss="modal">@lang('messages.close')</button>
                        <button type="button" class="btn btn-primary add_payment"
                            data-dismiss="modal">@lang('purchase.add')</button>
                    </div>
                </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
        </div><!-- /.modal -->

        <!-- Used for express checkout card transaction -->
        <div class="modal fade" tabindex="-1" role="dialog" id="card_details_modal">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title">@lang('lang_v1.card_transaction_details')</h4>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12">

                                <div class="col-md-4">
                                    <div class="form-group">
                                        {!! Form::label("card_number", __('lang_v1.card_no')) !!}
                                        {!! Form::text("", null, ['class' => 'form-control', 'placeholder' =>
                                        __('lang_v1.card_no'), 'id' => "card_number", 'autofocus']); !!}
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        {!! Form::label("card_holder_name", __('lang_v1.card_holder_name')) !!}
                                        {!! Form::text("", null, ['class' => 'form-control', 'placeholder' =>
                                        __('lang_v1.card_holder_name'), 'id' => "card_holder_name"]); !!}
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        {!! Form::label("card_transaction_number",__('lang_v1.card_transaction_no')) !!}
                                        {!! Form::text("", null, ['class' => 'form-control', 'placeholder' =>
                                        __('lang_v1.card_transaction_no'), 'id' => "card_transaction_number"]); !!}
                                    </div>
                                </div>
                                <div class="clearfix"></div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        {!! Form::label("card_type", __('lang_v1.card_type')) !!}
                                        {!! Form::select("", ['visa' => 'Visa', 'master' => 'MasterCard'],
                                        'visa',['class' =>
                                        'form-control select2', 'id' => "card_type" ]); !!}
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        {!! Form::label("card_month", __('lang_v1.month')) !!}
                                        {!! Form::text("", null, ['class' => 'form-control', 'placeholder' =>
                                        __('lang_v1.month'),
                                        'id' => "card_month" ]); !!}
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        {!! Form::label("card_year", __('lang_v1.year')) !!}
                                        {!! Form::text("", null, ['class' => 'form-control', 'placeholder' =>
                                        __('lang_v1.year'), 'id' => "card_year" ]); !!}
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        {!! Form::label("card_security",__('lang_v1.security_code')) !!}
                                        {!! Form::text("", null, ['class' => 'form-control', 'placeholder' =>
                                        __('lang_v1.security_code'), 'id' => "card_security"]); !!}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary"
                            id="pos-save-card">@lang('sale.finalize_payment')</button>
                    </div>

                </div>
            </div>
        </div>
    </td>

</tr>
<?php $row_count++ ;?>
@endforeach

<input type="hidden" id="row_count" value="{{ $row_count }}">