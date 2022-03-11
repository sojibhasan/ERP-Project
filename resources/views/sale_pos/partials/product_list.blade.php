@forelse($products as $product)
	<div class="col-md-3 col-xs-4 product_list no-print">
		<div class="product_box bg-gray" data-toggle="tooltip" data-placement="bottom" data-variation_id="{{$product->id}}" data-product_id="{{$product->product_id}}" title="{{$product->name}} @if($product->type == 'variable')- {{$product->variation}} @endif {{ '(' . $product->sub_sku . ')'}}">
		<div class="image-container">
			@if(count($product->media) > 0)
				<img src="{{$product->media->first()->display_url}}" alt="Product Image">
			@elseif(!empty($product->product_image))
				<img src="{{asset('/uploads/img/' . $product->product_image)}}" alt="Product Image">
			@else
				<img src="{{asset('/img/default.png')}}" alt="Product Image">
			@endif
		</div>
			<div class="text text-muted text-uppercase">
				<small>{{$product->name}} 
				@if($product->type == 'variable')
					- {{$product->variation}}
				@endif
				</small>
			</div>
			<small class="text-muted">
				({{$product->sub_sku}})
			</small>
		</div>
	</div>
@empty
	<input type="hidden" id="no_products_found">
	<div class="col-md-12">
		<h4 class="text-center">
			@lang('lang_v1.no_products_to_display')
		</h4>
	</div>
@endforelse