@extends('layouts.guest')
@section('title', $business->name)

@section('content')
<!-- Content Header (Page header) -->
<section class="content-header text-center">
    <h2>{{$business->name}}</h2>
    <h4 class="mb-0">{{$business_location->name}}</h4>
    <p>{!! $business_location->location_address !!}</p>
</section>
<!--Section: Block Content-->
<section class="content">
    {!! Form::open(['url' => action('\Modules\ProductCatalogue\Http\Controllers\CartController@store'), 'method' => 'post', 'id' => 'cart_form']) !!}
    <div class="container">
        <table id="cart" class="table table-hover table-condensed">
            <thead>
                <tr>
                    <th style="width:50%">Product</th>
                    <th style="width:10%">Price</th>
                    <th style="width:8%">Quantity</th>
                    <th style="width:22%" class="text-center">Subtotal</th>
                    <th style="width:10%"></th>
                </tr>
            </thead>
            <tbody>
                @php
                $total = 0;
                @endphp
                @foreach ($products as $item)
                @php
                $variation = $item->variations->first();
                $total += $variation->default_sell_price;
                @endphp
                <tr>
                    <td data-th="Product">
                        <div class="row">
                            <div class="col-sm-2 hidden-xs"><img src="{{$item->image_url}}" alt="..."
                                    class="img-responsive" /></div>
                            <div class="col-sm-10">
                                <h4 class="nomargin">{{$item->name}}</h4>
                                <p>{{$item->product_description}}</p>
                            </div>
                        </div>
                    </td>
                    <td data-th="Price" class="price" data-price="{{$variation->default_sell_price}}">
                        {{$business->currency->symbol}} {{@num_format($variation->default_sell_price)}}</td>
                    <td data-th="Quantity">
                        <input type="number" name="product[{{$item->id}}][qty]" class="form-control text-center qty" value="1">
                    </td>

                    <td data-th="Subtotal" class="text-center">{{$business->currency->symbol}}
                        <span class="sub_total"
                            data-sub_total="{{$variation->default_sell_price}}">{{@num_format($variation->default_sell_price)}}</span>
                    </td>
                    <td class="actions" data-th="">
                        <button class="btn btn-info btn-sm"><i class="fa fa-refresh"></i></button>
                        <button class="btn btn-danger btn-sm"><i class="fa fa-trash-o"></i></button>
                    </td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="visible-xs">

                </tr>
                <tr>
                    <td>
                    </td>
                    <td colspan="2" class="hidden-xs"></td>
                    <td class="hidden-xs text-center"><strong>Total {{$business->currency->symbol}} <span
                                class="grand_total">{{@num_format($total)}}</span></strong></td>
                    <td><button type="submit" class="btn btn-success btn-block">Checkout <i class="fa fa-angle-right"></i></button>
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>
    <input type="hidden" name="total_amount" id="total_amount" value="{{$total}}">
    <input type="hidden" name="location_id" id="total_amount" value="{{$location_id}}">
    {!! Form::close() !!}
</section>
<!--Section: Block Content-->



@endsection

@section('javascript')
<script>
    $('.qty').change(function(){
        let price = parseFloat($(this).parent().parent().find('.price').data('price'));
        let qty = parseFloat($(this).val());

        let sub_total = price * qty;
        $(this).parent().parent().find('.sub_total').text(__number_f(sub_total, false, false, __currency_precision));
        $(this).parent().parent().find('.sub_total').data( 'sub_total' ,sub_total);

        calculate_cart_total();
    })

    function calculate_cart_total(){
        grand_total = 0;
        $('#cart').find('.sub_total').each((i, ele) => {
            grand_total += parseFloat($(ele).data('sub_total'));
        })

        $('#total_amount').val(grand_total);
        $('#cart').find('.grand_total').text(__number_f(grand_total, false, false, __currency_precision))
    }
</script>
@endsection