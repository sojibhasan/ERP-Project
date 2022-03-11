@if(!session('business.enable_price_tax')) 
  @php
    $default = 0;
    $class = 'hide';
  @endphp
@else
  @php
    $default = null;
    $class = '';
  @endphp
@endif

<div class="col-sm-12"><br>
    <div class="table-responsive">
    <table class="table table-bordered add-product-price-table table-condensed {{$class}}">
        <tr>
          <th>@lang('product.default_purchase_price')</th>
          <th>@lang('product.profit_percent') @show_tooltip(__('tooltip.profit_percent'))</th>
          <th class="multiple_units">@lang('product.units')</th>
          <th>@lang('product.default_selling_price')</th>
          <th>@lang('lang_v1.product_image')</th>
        </tr>
        @foreach($product_deatails->variations as $variation )
            @if($loop->first)
            @php
                $unit_id = App\Product::where('id', $variation->product_id)->first()->unit_id;
                $units = App\Unit::where('id',  $unit_id)->orWhere('base_unit_id',  $unit_id)->select('actual_name', 'id')->get();
                $default_multiple_unit_price = (array) json_decode($variation->default_multiple_unit_price);
            @endphp
                <tr>
                    <td>
                        <input type="hidden" name="single_variation_id" value="{{$variation->id}}">

                        <div class="col-sm-6">
                          {!! Form::label('single_dpp', trans('product.exc_of_tax') . ':*') !!}

                          {!! Form::text('single_dpp', ( ((float)$variation->default_purchase_price) != "" ) ? (float)$variation->default_purchase_price : 0.00 , ['class' => 'form-control input-sm dpp input_number', 'placeholder' => __('product.exc_of_tax'), 'required']); !!}
                        </div>

                        <div class="col-sm-6">
                          {!! Form::label('single_dpp_inc_tax', trans('product.inc_of_tax') . ':*') !!}
                        
                          {!! Form::text('single_dpp_inc_tax', ( ((float)$variation->dpp_inc_tax) != "" ) ? (float)$variation->dpp_inc_tax : 0.00 , ['class' => 'form-control input-sm dpp_inc_tax input_number', 'placeholder' => __('product.inc_of_tax'), 'required']); !!}
                        </div>
                    </td>

                    <td>
                        <br/>
                        {!! Form::text('profit_percent', ( ((float)$variation->profit_percent) != "" ) ? (float)$variation->profit_percent : 0.00 , ['class' => 'form-control input-sm input_number', 'id' => 'profit_percent', 'required']); !!}
                    </td>
                    <td class="multiple_units units_list">
                    </br></br>
                     @foreach ($units as $unit)
                      <b>{{$unit->actual_name}}</b></br></br>
                     @endforeach
                    </td>
                    <td>
                        <label><span class="dsp_label"></span></label>
                        {!! Form::text('single_dsp', ( ((float)$variation->default_sell_price) != "" ) ? (float)$variation->default_sell_price : 0.00 , ['class' => 'form-control input-sm dsp input_number', 'style' => 'margin-top:7px;', 'placeholder' => __('product.exc_of_tax'), 'id' => 'single_dsp', 'required']); !!}

                        {!! Form::text('single_dsp_inc_tax', ( ((float)$variation->sell_price_inc_tax) != "" ) ? (float)$variation->sell_price_inc_tax : 0.00 , ['class' => 'form-control input-sm hide input_number', 'style' => 'margin-top:7px;', 'placeholder' => __('product.inc_of_tax'), 'id' => 'single_dsp_inc_tax', 'required']); !!}
                        <span class="mutiple_unit_price_input">
                          @php
                              $i = 0;
                          @endphp
                        @foreach ($units as $key => $unit)
                          @if($unit->id != $unit_id)
                          </br><input class="form-control input-sm dsp input_number" value="{{array_key_exists($unit->id, $default_multiple_unit_price) ? $default_multiple_unit_price[$unit->id] : 0}}" name="multiple_unit[{{$i}}][{{$unit->id}}]" type="text">
                          @php
                            $i++;
                          @endphp
                          @endif
                         @endforeach
                         
                        </span>
                      </td>
                    <td>
                        @php 
                            $action = !empty($action) ? $action : '';
                        @endphp
                        @if($action !== 'duplicate')
                            @foreach($variation->media as $media)
                                <div class="img-thumbnail">
                                    <span class="badge bg-red delete-media" data-href="{{ action('ProductController@deleteMedia', ['media_id' => $media->id])}}"><i class="fa fa-close"></i></span>
                                    {!! $media->thumbnail() !!}
                                </div>
                            @endforeach
                        @endif
                        <div class="form-group">
                            {!! Form::label('variation_images', __('lang_v1.product_image') . ':') !!}
                            {!! Form::file('variation_images[]', ['class' => 'variation_images', 'accept' => 'image/*', 'multiple']); !!}
                            <small><p class="help-block">@lang('purchase.max_file_size', ['size' => (config('constants.document_size_limit') / 1000000)]) <br> @lang('lang_v1.aspect_ratio_should_be_1_1')</p></small>
                        </div>
                    </td>
                </tr>
            @endif
        @endforeach
    </table>
    </div>
</div>