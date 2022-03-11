@extends('layouts.guest')
@section('title', $business->name)

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header text-center">
    <h2>{{$business->name}}</h2>
    <h4 class="mb-0">{{$business_location->name}}</h4>
    <p>{!! $business_location->location_address !!}</p>
</section>

<style>
    .list_sub_cat {
        text-decoration: none;

    }

    .list_sub_cat>li {
        display: inline;
        float: left;
    }
</style>
@php
    $color_array = [
        '#FF5733',
        '#800080',
        '#2874A6',
        '#33691E',
        '#F9A825',
        '#B71C1C'
    ]; 

    $color_index = 0;
@endphp
<!-- Main content -->
<section class="content">
    <div class="container">
        <div class="row">
            <div class=" col-sm-10">
                <h3 class="mt-0">@lang('report.products')</h3>
            </div>
            <div class="col-sm-2">
                <a href="#" class="dropdown-toggle load_notifications mt-0" data-toggle="dropdown" id="go_to_shopping_cart"
                    data-loaded="false">
                    <i class="fa fa-shopping-cart fa-lg" style="font-size: 30px; color: #615d5d;">
                    </i>
                    <span class="label label-warning cart_count" style="position: absolute;left: 35px; top: -5px;">0</span>
                </a>
            </div>
        </div>
        <div class="hide">
            {!! Form::open(['url' => action('\Modules\ProductCatalogue\Http\Controllers\CartController@create'),
            'method' => 'get', 'id' => 'cart_form']) !!}
            {!! Form::hidden('product_id_array', null, ['id' => 'product_id_array']) !!}
            {!! Form::hidden('location_id', $location_id, ['id' => 'location_id']) !!}
            {!! Form::close() !!}
        </div>
        <div class="row">
            <div class="col-md-12">
                <ul class="list_sub_cat">
                    <li style="width: 52px;">
                        <div class="input-group input-group-md">
                            <div class="input-group-btn">
                                <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown"
                                    aria-expanded="false">All
                                    <span class="fa fa-caret-down"></span></button>
                                <ul class="dropdown-menu">
                                    <li><a href="#" class="drop_cat" data-id="0">All</a></li>
                                    @foreach ($cats as $cat)
                                    <li><a href="#" class="drop_cat" data-id="{{$cat->id}}">{{$cat->name}}</a></li>
                                    @endforeach

                                </ul>
                            </div>
                            <!-- /btn-group -->
                        </div>
                    </li>
                    @foreach ( $sub_cats as $sub_cat)
                    <li style="background: {{$color_array[$color_index]}};">
                        <label style="color: #fff;" class="btn btn-flat sub_cat_filter" id="sub_cat_{{$sub_cat->parent_id}}"
                            data-sub_cat_id="{{$sub_cat->id}}" data-cat_id="{{$sub_cat->parent_id}}">
                            {{ $sub_cat->name ?? 'Uncategorized'}}
                        </label>

                    </li>
                    @php
                        $color_index++;
                        if($color_index == 6){
                            $color_index = 0;
                        }
                    @endphp
                    @endforeach
                </ul>


            </div>
        </div>

        <div id="product_list">

        </div>


    </div>
</section>
<!-- /.content -->
<!-- Add currency related field-->
<input type="hidden" id="__code" value="{{$business->currency->code}}">
<input type="hidden" id="__symbol" value="{{$business->currency->symbol}}">
<input type="hidden" id="__thousand" value="{{$business->currency->thousand_separator}}">
<input type="hidden" id="__decimal" value="{{$business->currency->decimal_separator}}">
<input type="hidden" id="__symbol_placement" value="{{$business->currency->currency_symbol_placement}}">
<input type="hidden" id="__precision" value="{{config('constants.currency_precision', 2)}}">
<input type="hidden" id="__quantity_precision" value="{{config('constants.quantity_precision', 2)}}">
<div class="modal fade product_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
</div>
@stop
@section('javascript')
<script type="text/javascript">
    var cart_product_array = []; 
    var cart_count = 0;
    $(document).ready( function() {
        //Set global currency to be used in the application
        __currency_symbol = $('input#__symbol').val();
        __currency_thousand_separator = $('input#__thousand').val();
        __currency_decimal_separator = $('input#__decimal').val();
        __currency_symbol_placement = $('input#__symbol_placement').val();
        if ($('input#__precision').length > 0) {
            __currency_precision = $('input#__precision').val();
        } else {
            __currency_precision = 2;
        }

        if ($('input#__quantity_precision').length > 0) {
            __quantity_precision = $('input#__quantity_precision').val();
        } else {
            __quantity_precision = 2;
        }

        //Set page level currency to be used for some pages. (Purchase page)
        if ($('input#p_symbol').length > 0) {
            __p_currency_symbol = $('input#p_symbol').val();
            __p_currency_thousand_separator = $('input#p_thousand').val();
            __p_currency_decimal_separator = $('input#p_decimal').val();
        }

        __currency_convert_recursively($('.content'));
    });

    $(document).on('click', '.show-product-details', function(e){
        e.preventDefault();
        $.ajax({
            url: $(this).data('href'),
            dataType: 'html',
            success: function(result) {
                $('.product_modal')
                    .html(result)
                    .modal('show');
                __currency_convert_recursively($('.product_modal'));
            },
        });
    });
    $(document).on('click', '.sub_cat_filter', function(e){
        e.preventDefault();
        let sub_cat_id = $(this).data('sub_cat_id');
        getProducts(sub_cat_id);
       
    });
    $(document).on('click', '.drop_cat', function(e){
        e.preventDefault();
        let id = parseInt($(this).data('id'));
        $('.sub_cat_filter').each(function(i, obj){
            if(id != 0){
                if(parseInt($(obj).data('cat_id')) == id){
                    $(obj).removeClass('hide');
                }else{
                    $(obj).addClass('hide');
                }
            }else{
                $(obj).removeClass('hide');
            }
        })
        getProducts(0, id);
    });
    function getProducts(sub_cat_id = 0, category_id = 0){
        $.ajax({
            method: 'get',
            url: "{{action('\Modules\ProductCatalogue\Http\Controllers\ProductCatalogueController@index',  [$business_id, $location_id])}}?sub_category="+sub_cat_id+"&category_id="+category_id,
            data: {  },
            contentType: 'html',
            success: function(result) {
                $('#product_list').empty().append(result);
            },
        });
    }
    
    $(document).ready(function(){
        getProducts(0, 0);
    })

   
    $(document).on('click', '.add_to_cart', function(e){
        e.preventDefault();
        $(this).removeClass('btn-primary');
        $(this).addClass('btn-dark');
        $(this).text('Added to cart')
        $(this).attr('disabled', 'disabled');
        cart_count++;
        let product_id = parseInt($(this).data('product_id'));
        cart_product_array.push(product_id);
        $('.cart_count').text(cart_count);
        $('#product_id_array').val(cart_product_array);
       
    });

    $('#go_to_shopping_cart').click(function(){
        $('#cart_form').submit();
    })
</script>
@endsection