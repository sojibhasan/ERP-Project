@extends('layouts.app')
@section('title', __('lang_v1.add_opening_stock'))

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>@lang('lang_v1.add_opening_stock')</h1>
</section>

<!-- Main content -->
<section class="content">
	{!! Form::open(['url' => action('OpeningStockController@save'), 'method' => 'post', 'id' => 'add_opening_stock_form' ]) !!}
	{!! Form::hidden('product_id', $product->id); !!}
	@include('opening_stock.form-part')
	<div class="row">
		<div class="col-sm-12">
			<button type="submit" class="btn btn-primary pull-right">@lang('messages.save')</button>
		</div>
	</div>

	{!! Form::close() !!}
</section>
@stop
@section('javascript')
	<script src="{{ asset('js/opening_stock.js?v=' . $asset_v) }}"></script>


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

	$("table").on('click','.btnDeleteRow',function(){
    	$(this).closest('tr').remove();
    });
	</script>
@endsection
