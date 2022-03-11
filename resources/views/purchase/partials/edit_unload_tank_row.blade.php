<div class="col-md-12 check_tank_row"  id="tank_row{{$row_count}}" data-row_id="{{$row_count}}">
    <div class="col-md-12 bg-success">
        <div class="col-md-3"></div>
        <div class="col-md-6">
            <div class="row">
                <div class="col-md-4"></div>
                <div class="col-md-3" style="padding-right:0">
                    <h4>{{$product->name }}</h4>
                </div>
                <div class="col-md-3" style="padding-left:0">
                    <input type="text" name="" id="receive_qty{{$product->id}}" class="form-control"
                        style="margin-top: 3px;" readonly value="{{$purchase_lines->quantity}}">
                        <input type="hidden" id="stock_match{{$row_count}}" name="stock_match{{$row_count}}" value="0">
                        <input type="hidden" id="tank_row_conut{{$row_count}}" name="tank_row_conut{{$row_count}}" value="{{$row_count}}">
                </div>
            </div>
        </div>
        <div class="col-md-4"></div>
    </div>
    <div class="clearfix"></div>
    <table class="table table-bordered">
        <thead>
            <th>@lang('purchase.warehouse')</th>
            <th>@lang('purchase.quantity')</th>
            <th>@lang('purchase.instock_qty')</th>
        </thead>
        <tbody>
            @foreach ($fuel_tanks as $tank)
            @php
            $tank_purhcase_line = Modules\Petro\Entities\TankPurchaseLine::where('transaction_id',
            $transaction_id)->where('product_id', $product->id)->where('tank_id', $tank->id)->first();
            @endphp
            @if(!empty($tank_purhcase_line))
            <tr>
                <td>{{  $tank->fuel_tank_number }}</td>
                <td>{!! Form::number('tanks['. $tank->id .'][qty]', rtrim(rtrim($tank_purhcase_line->quantity, '0'), '.'), ['class' => 'form-control tank_qty tank_qty'.$product->id]) !!}</td>
                <td>{!! Form::number('tanks['. $tank->id .'][instock_qty]', rtrim(rtrim($tank_purhcase_line->instock_qty, '0'), '.'), ['class' => 'form-control', 'readonly']) !!}</td>
                {!! Form::hidden('tanks['. $tank->id .'][tank_purchase_line_id]', $tank_purhcase_line->id, ['class' => 'form-control']) !!}
            </tr>
            @else
            <tr>
                <td>{{  $tank->fuel_tank_number }}</td>
                <td>{!! Form::number('tanks['. $tank->id .'][qty]', null, ['class' => 'form-control tank_qty tank_qty'. $product->id]) !!}</td>
                <td>{!! Form::number('tanks['. $tank->id .'][instock_qty]', $current_balance[$tank->id], ['class' => 'form-control', 'readonly']) !!}</td>
            </tr>
            @endif
            @endforeach
        </tbody>
    </table>
</div>

<script>
    @if(!$is_view)
    $('#product_id{{$product->id}}').change(function(){
        $('#receive_qty{{$product->id}}').val($(this).val());
    });

    $('.tank_qty{{$product->id}}').change(function(){
        total_qty{{$product->id}} = 0;
        $('.tank_qty{{$product->id}}').each(function( index ) {
            if($(this).val() !== '' && $(this).val() !== undefined){
                total_qty{{$product->id}} += parseFloat($(this).val());
            }
        });
        let receive_qty{{$product->id}} = parseFloat($('#receive_qty{{$product->id}}').val());
        if(receive_qty{{$product->id}} == total_qty{{$product->id}} ){
            let this_row_count = parseInt($('#tank_row_conut{{$row_count}}').val()) + 1;
            $('#stock_match{{$row_count}}').val('1');
            $("#tank_row"+this_row_count).find('.tank_qty').removeAttr('disabled');
        }else{
            $('#stock_match{{$row_count}}').val('0');
        }
        if(unloadTankQtyMatch()){
            $('#submit_purchase_form').removeAttr("disabled");
        }

    });

    $('.tank_qty{{$product->id}}').trigger('change');
    $('.tank_qty').trigger('change');
    @else
    $('.tank_qty').prop('disabled', true);
    @endif
</script>