@foreach ($purchase->purchase_lines as $purchase_line)
<tr class="product_row">
    <td>
        {{ $purchase_line->product->name }} ({{$purchase_line->variations->sub_sku}})
        @if( $purchase_line->product->type == 'variable' )
        <br />
        (<b>{{ $purchase_line->variations->name }}</b> :
        {{ $purchase_line->variations->name }})
        @endif
    </td>
    
    <td class="hide">
        <div class="input-group">
            <select name="purchases[{{ $loop->index }}][purchase_line_tax_id]"
                class="form-control select2 input-sm purchase_line_tax_id" placeholder="'Please Select'">
                <option value="" data-tax_amount="0" @if( $hide_tax=='hide' ) selected @endif>@lang('lang_v1.none')
                </option>
                @foreach($taxes as $tax)
                <option value="{{ $tax->id }}" data-tax_amount="{{ $tax->amount }}" @if( $product->tax == $tax->id &&
                    $hide_tax != 'hide') selected @endif >{{ $tax->name }}</option>
                @endforeach
            </select>
            {!! Form::hidden('purchases[' . $loop->index . '][item_tax]', 0, ['class' => 'purchase_product_unit_tax']);
            !!}
            <span class="input-group-addon purchase_product_unit_tax_text">
                0.00</span>
        </div>
    </td>
    <td>
        {!! Form::hidden('purchases[' . $loop->index . '][product_id]',
        $purchase_line->product->id ); !!}
        {!! Form::hidden('purchases[' . $loop->index . '][variation_id]',
        $purchase_line->variations->id , ['class' =>
        'hidden_variation_id']); !!}
        <div class="input-group input-number">
            {{-- <span class="input-group-btn @if(!$purchase_pos) hide @endif"><button type="button" class="btn btn-default btn-flat quantity-down"><i
														class="fa fa-minus text-danger"></i></button></span> --}}
            {!! Form::text('purchases[' . $loop->index . '][quantity]',
            @num_format($purchase_line->quantity), ['class'
            =>
            'form-control purchase_quantity input_number mousetrap', 'required',
            'data-rule-abs_digit' =>
            true, 'data-msg-abs_digit' => __('lang_v1.decimal_value_not_allowed') , 'id'
            =>
            'product_id'.$purchase_line->product->id]); !!}
            {{-- <span class="input-group-btn @if(!$purchase_pos) hide @endif"><button type="button" class="btn btn-default btn-flat quantity-up"><i
														class="fa fa-plus text-success"></i></button></span> --}}
        </div>
        <input type="hidden" class="base_unit_cost" value="{{$purchase_line->variations->default_purchase_price}}">
        <input type="hidden" class="base_unit_selling_price" value="{{$purchase_line->variations->sell_price_inc_tax}}">
        <input type="hidden" class="is_fuel_category" name="is_fuel_category" value="0">
        <input type="hidden" class="product_id" name="product_id" value="{{$purchase_line->product->id}}">

        <input type="hidden" name="purchases[{{$loop->index}}][product_unit_id]"
            value="{{$purchase_line->product->unit->id}}">
    </td>
    <td class="hide">
        @if(!empty($sub_units))
        <select name="purchases[{{$loop->index}}][sub_unit_id]" class="form-control input-sm sub_unit">
            @foreach($sub_units as $key => $value)
            <option value="{{$key}}" data-multiplier="{{$value['multiplier']}}">
                {{$value['name']}}
            </option>
            @endforeach
        </select>
        @else
        {{ $purchase_line->product->unit->short_name }}
        @endif
    </td>

    <td class="hide">
        @if(!empty($purchase_line->sub_units_options))
        <br>
        <select name="purchases[{{$loop->index}}][sub_unit_id]" class="form-control input-sm sub_unit">
            @foreach($purchase_line->sub_units_options as $sub_units_key =>
            $sub_units_value)
            <option value="{{$sub_units_key}}" data-multiplier="{{$sub_units_value['multiplier']}}"
                @if($sub_units_key==$purchase_line->sub_unit_id) selected @endif>
                {{$sub_units_value['name']}}
            </option>
            @endforeach
        </select>
        @else
        {{ $purchase_line->product->unit->short_name }}
        @endif

        <input type="hidden" name="purchases[{{$loop->index}}][product_unit_id]"
            value="{{$purchase_line->product->unit->id}}">

        <input type="hidden" class="base_unit_selling_price" value="{{$purchase_line->variations->sell_price_inc_tax}}">
    </td>

    <td class="hide">
        {!! Form::text('purchases[' . $loop->index . '][pp_without_discount]',
        $purchase_line->pp_without_discount/$purchase->exchange_rate, ['class' =>
        'form-control input-sm
        purchase_unit_cost_without_discount input_number', 'required']); !!}
    </td>
    <td>
        {!! Form::text('purchases[' . $loop->index . '][discount_percent]',
        @num_format($purchase_line->discount_percent),
        ['class' =>
        'form-control input-sm inline_discounts input_number', 'required']); !!}
    </td>
    <td class="hide">
        {!! Form::text('purchases[' . $loop->index . '][purchase_price]',
        $purchase_line->purchase_price/$purchase->exchange_rate, ['class' =>
        'form-control input-sm
        purchase_unit_cost input_number', 'required']); !!}
        {!! Form::hidden('purchases[' . $loop->index . '][purchase_price]',
        $purchase_line->purchase_price/$purchase->exchange_rate, ['class' =>
        'pp_exc_tax', 'required']); !!}
    </td>
    <td class="hide">
        <span class="row_subtotal_before_tax">
            {{@num_format($purchase_line->quantity * $purchase_line->purchase_price/$purchase->exchange_rate)}}
        </span>
        <input type="hidden" class="row_subtotal_before_tax_hidden"
            value="{{($purchase_line->quantity * $purchase_line->purchase_price/$purchase->exchange_rate)}}">
    </td>
    <td class="hide">
        {!! Form::text('purchases[' . $loop->index . '][purchase_price_inc_tax]',
        $purchase_line->purchase_price_inc_tax/$purchase->exchange_rate, ['class' =>
        'form-control input-sm purchase_unit_cost_after_tax input_number', 'required']); !!}
    </td>
    <td>
        <span class="row_subtotal_after_tax">
            {{@num_format($purchase_line->purchase_price_inc_tax * $purchase_line->quantity/$purchase->exchange_rate)}}
        </span>
        <input type="hidden" class="row_subtotal_after_tax_hidden"
            value="{{@num_format($purchase_line->purchase_price_inc_tax * $purchase_line->quantity/$purchase->exchange_rate)}}">
    </td>
    <td><i class="fa fa-times remove_purchase_entry_row text-danger" data-row_count="{{ $loop->index }}" title="Remove"
            style="cursor:pointer;"></i></td>
    <input type="hidden" id="row_count" value="{{ $loop->index }}">
</tr>
@endforeach