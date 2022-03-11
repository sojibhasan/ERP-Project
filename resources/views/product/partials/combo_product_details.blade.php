<div class="row">
	<div class="col-md-12">
		<h4>@lang('lang_v1.combo'):</h4>
	</div>
	<div class="col-md-12">
		<div class="table-responsive">
			<table class="table bg-gray">
				<tr class="bg-green">
					<th>@lang('product.product_name')</th>
					<th>@lang('sale.qty')</th>
				</tr>
				@foreach($combo_variations as $variation)
				<tr>
					<td>
						{{$variation['variation']['product']->name}} 

						@if($variation['variation']['product']->type == 'variable')
							- {{$variation['variation']->name}}
						@endif
						
						({{$variation['variation']->sub_sku}})
					</td>
					<td>
						{{$variation['quantity']}} {{$variation['unit_name']}}
					</td>
				</tr>
				@endforeach
			</table>
		</div>
	</div>
</div>