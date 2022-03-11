<br>
<div class="row">
    <div class="col-md-12">
        <div class="col-md-2">
            <div class="form-group">
                {!! Form::label('pump_no', __('petro::lang.pump_no').':') !!}
                {!! Form::select('pump_no', $pump_nos, null, ['class' => 'form-control meter_sale_fields check_pumper
                select2',
                'placeholder' => __('petro::lang.please_select')]); !!}
            </div>
        </div>
        <div class="col-md-2 pump_starting_meter_div">
            <div class="form-group">
                {!! Form::label('pump_starting_meter', __( 'petro::lang.pump_starting_meter' ) ) !!}
                {!! Form::text('pump_starting_meter', null, ['class' => 'form-control meter_sale_fields check_pumper
                input_number
                pump_starting_meter', 'required', 'readonly',
                'placeholder' => __(
                'petro::lang.pump_starting_meter' ) ]); !!}
            </div>
        </div>
        <div class="col-md-2 pump_closing_meter_div">
            <div class="form-group">
                {!! Form::label('pump_closing_meter', __( 'petro::lang.pump_closing_meter' ) ) !!}
                {!! Form::text('pump_closing_meter', null, ['class' => 'form-control meter_sale_fields check_pumper
                input_number
                pump_closing_meter', 'required',
                'placeholder' => __(
                'petro::lang.pump_closing_meter' ) ]); !!}
            </div>
        </div>
        <div class="col-md-2">
            <div class="form-group">
                {!! Form::label('sold_qty', __( 'petro::lang.sold_qty' ) ) !!}
                {!! Form::text('sold_qty', null, ['class' => 'form-control meter_sale_fields check_pumper sold_qty
                input_number',
                'required', 'disabled',
                'placeholder' => __(
                'petro::lang.sold_qty' ) ]); !!}
            </div>
        </div>
        <div class="col-md-2">
            <div class="form-group">
                {!! Form::label('unit_price', __( 'petro::lang.unit_price' ) ) !!}
                {!! Form::text('meter_sale_unit_price', null, ['id' => 'meter_sale_unit_price', 'class' => 'form-control
                meter_sale_fields check_pumper unit_price input_number',
                'readonly',
                'placeholder' => __(
                'petro::lang.unit_price' ) ]); !!}
            </div>
        </div>
        <div class="col-md-2">
            <div class="form-group">
                {!! Form::label('testing_qty', __( 'petro::lang.testing_qty' ) ) !!}
                {!! Form::text('testing_qty', 0.00, ['class' => 'form-control check_pumper input_number
                testing_qty', 'required',
                'placeholder' => __(
                'petro::lang.testing_qty' ) ]); !!}
            </div>
        </div>
        <div class="col-md-2">
            <div class="form-group">
                {!! Form::label('meter_sale_discount_type', __( 'petro::lang.discount_type' ) ) !!}
                {!! Form::select('meter_sale_discount_type', ['fixed' => 'Fixed', 'percentage' => 'Percentage'], null, ['class' => 'form-control meter_sale_fields check_pumper
                input_number
                meter_sale_discount_type', 'required',
                'placeholder' => __(
                'petro::lang.please_select' ) ]); !!}
            </div>
        </div>
        <div class="col-md-2">
            <div class="form-group">
                {!! Form::label('meter_sale_discount', __( 'petro::lang.discount' ) ) !!}
                {!! Form::text('meter_sale_discount', 0.00, ['class' => 'form-control meter_sale_fields check_pumper
                input_number
                meter_sale_discount', 'required',
                'placeholder' => __(
                'petro::lang.discount' ) ]); !!}
            </div>
        </div>
        {!! Form::hidden('bulk_sale_meter', 0, ['id' => 'bulk_sale_meter']) !!}
        <div class="col-md-1 pull-right">
            <button type="button" class="btn btn-primary btn_meter_sale"
                style="margin-top: 23px;">@lang('messages.add')</button>
        </div>
    </div>
</div>
<br>
<br>
<div class="row">
    <div class="col-md-12">
        <table class="table table-bordered table-striped" id="meter_sale_table">
            <thead>
                <tr>
                    <th>@lang('petro::lang.code' )</th>
                    <th>@lang('petro::lang.products' )</th>
                    <th>@lang('petro::lang.pump' )</th>
                    <th>@lang('petro::lang.starting_meter')</th>
                    <th>@lang('petro::lang.closing_meter')</th>
                    <th>@lang('petro::lang.price')</th>
                    <th>@lang('petro::lang.qty' )</th>
                    <th>@lang('petro::lang.discount' )</th>
                    <th>@lang('petro::lang.testing_qty' )</th>
                    <th>@lang('petro::lang.sub_total' )</th>
                    <th>@lang('petro::lang.action' )</th>
                </tr>
            </thead>
            <tbody>
                @php
                $final_total = 0.00;
                @endphp
                @if (!empty($active_settlement))
                @foreach ($active_settlement->meter_sales as $item)
                @php
                $product = App\Product::where('id', $item->product_id)->first();
                $pump = Modules\Petro\Entities\Pump::where('id', $item->pump_id)->first();
                $final_total = $final_total + $item->sub_total;
                @endphp
                <tr>
                    <td>{{$product->sku}}</td>
                    <td>{{$product->name}}</td>
                    <td>{{$pump->pump_no}}</td>
                    <td>{{number_format($item->starting_meter, $currency_precision)}}</td>
                    <td>{{number_format($item->closing_meter, $currency_precision)}}</td>
                    <td>{{number_format($item->price, $currency_precision)}}</td>
                    <td>{{number_format($item->qty, $currency_precision)}}</td>
                    <td>{{number_format($item->discount, $currency_precision)}}</td>
                    <td>{{number_format($item->testing_qty, $currency_precision)}}</td>
                    <td>{{number_format($item->sub_total, $currency_precision)}}</td>

                    {{-- @if(!isset($edit)) --}}
                    <td><button class="btn btn-xs btn-danger delete_meter_sale"
                            data-href="/petro/settlement/delete-meter-sale/{{$item->id}}"><i class="fa fa-times"></i>
                    </td>
                    {{-- @endif --}}
                </tr>
                @endforeach
                @endif

            </tbody>

            <tfoot>
                <tr>
                    <td colspan="9" style="text-align: right; font-weight: bold;">@lang('petro::lang.meter_sale_total')
                        :</td>
                    <td style="text-align: left; font-weight: bold;" class="meter_sale_total">
                        {{number_format( $final_total, $currency_precision)}}</td>
                </tr>
                <input type="hidden" value="{{$final_total}}" name="meter_sale_total" id="meter_sale_total">
            </tfoot>
        </table>
    </div>
</div>