<div class="modal-dialog modal-xl" role="document">
	<div class="modal-content">
	{!! Form::open(['url' => action('OpeningStockController@save'), 'method' => 'post', 'id' => 'add_opening_stock_form' ]) !!}
	{!! Form::hidden('product_id', $product->id); !!}
		<div class="modal-header">
		    <button type="button" class="close no-print" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		      <h4 class="modal-title" id="modalTitle">@lang('lang_v1.add_opening_stock')</h4>
	    </div>
	    <div class="modal-body">
			@include('opening_stock.form-part')
		</div>
		<div class="modal-footer">
			<button type="button" class="btn btn-primary" id="add_opening_stock_btn">@lang('messages.save')</button>
		    <button type="button" class="btn btn-default no-print" data-dismiss="modal">@lang( 'messages.close' )</button>
		 </div>
	 {!! Form::close() !!}
	</div>
</div>

<script>
	@foreach($stores as $key => $value)
	@foreach($product->variations as $variation)
	$('.qty{{$value->location_id}}{{$variation->id}}').change(function(){
		var total_qty_location = 0;
		$('.qty{{$value->location_id}}{{$variation->id}}').each(function(i){
			total_qty_location += parseInt($(this).val());
		});
		$('.location-qty{{$value->location_id}}{{$variation->id}}').val(total_qty_location);
			
	});

	$('.unit_price{{$value->location_id}}{{$variation->id}}').change(function(){
		var total_unit_price_location = 0;
		// $('.unit_price{{$value->location_id}}{{$variation->id}}').each(function(i){
			total_unit_price_location = parseInt($(this).val());
		// });
		$('.location-unit_price{{$value->location_id}}{{$variation->id}}').val(total_unit_price_location);
			
	});


	@endforeach
	@endforeach
</script>