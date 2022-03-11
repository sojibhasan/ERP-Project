<section class="content">
    <div class="container">
        <div class="row eq-height-row">
            <div class="col-md-12">
                @if($products->count() > 0)
                @foreach($products as $product)
                <div class="col-md-3 eq-height-col">
                    <div class="box box-solid product-box">
                        <div class="box-body" style=" box-shadow: 2px 2px 6px #888888;">
                            <a href="#" class="show-product-details"
                                data-href="{{action('\Modules\ProductCatalogue\Http\Controllers\ProductCatalogueController@show',  [$business->id, $product->id])}}?location_id={{$business_location->id}}">
                                <img src="{{$product->image_url}}" class="img-responsive catalogue">
                                <a>

                                    @php
                                    $discount = $discounts->firstWhere('brand_id', $product->brand_id);
                                    if(empty($discount)){
                                    $discount = $discounts->firstWhere('category_id', $product->category_id);
                                    }
                                    @endphp

                                    @if(!empty($discount))
                                    <span class="label label-warning discount-badge">-
                                        {{@num_format($discount->discount_amount)}}%</span>
                                    @endif

                                    @php
                                    $max_price = $product->variations->max('sell_price_inc_tax');
                                    $min_price = $product->variations->min('sell_price_inc_tax');
                                    @endphp
                                    <h2 class="catalogue-title">
                                        <a href="#" class="show-product-details"
                                            data-href="{{action('\Modules\ProductCatalogue\Http\Controllers\ProductCatalogueController@show',  [$business->id, $product->id])}}?location_id={{$business_location->id}}">
                                            {{$product->name}}
                                        </a>
                                    </h2>
                                    <table class="table no-border product-info-table">
                                        <tr>
                                            <th class="pb-0"> @lang('lang_v1.price'):</th>
                                            <td class="pb-0">
                                                <span class="display_currency"
                                                    data-currency_symbol="true">{{@num_format($max_price)}}</span>
                                                @if($max_price != $min_price) - <span class="display_currency"
                                                    data-currency_symbol="true">{{@num_format($min_price)}}</span> @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="pb-0"> @lang('product.sku'):</th>
                                            <td class="pb-0">{{$product->sku}}</td>
                                        </tr>
                                        <tr>
                                            <td colspan="2"><span class="text-red">
                                                    <a href="#" class="show-product-details" style="color:red;"
                                                        data-href="{{action('\Modules\ProductCatalogue\Http\Controllers\ProductCatalogueController@show',  [$business->id, $product->id])}}?location_id={{$business_location->id}}">
                                                        @lang('lang_v1.click_to_view_more_details')
                                                    </a>
                                                </span></td>
                                        </tr>
                                        @if($product->type == 'variable')
                                        @php
                                        $variations = $product->variations->groupBy('product_variation_id');
                                        @endphp
                                        @foreach($variations as $product_variation)
                                        <tr>
                                            <th>{{$product_variation->first()->product_variation->name}}:</th>
                                            <td>
                                                <select class="form-control input-sm">
                                                    @foreach($product_variation as $variation)
                                                    <option value="{{$variation->id}}">{{$variation->name}}
                                                        ({{$variation->sub_sku}}) -
                                                        {{@num_format($variation->sell_price_inc_tax)}}</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                        </tr>
                                        @endforeach
                                        @endif
                                        <tr>
                                            <td colspan="2"><button href="#" class="btn btn-primary btn-flat btn-block add_to_cart" data-product_id={{$product->id}}>@lang('lang_v1.add_to_cart')</button></td>
                                        </tr>
                                    </table>
                        </div>
                    </div>
                </div>
                @if($loop->iteration%4 == 0)
                <div class="clearfix"></div>
                @endif
                @endforeach
                @else
                <p class="text-center" style="text-align: center">No item found</p>
                @endif
            </div>
        </div>
    </div>
</section>