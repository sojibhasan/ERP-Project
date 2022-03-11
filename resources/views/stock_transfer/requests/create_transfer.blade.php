@extends('layouts.app')
@section('title', __('lang_v1.add_stock_transfer'))

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
	<h1>@lang('lang_v1.add_stock_transfer')</h1>
</section>

<!-- Main content -->
<section class="content no-print">
	{!! Form::open(['url' => action('StockTransferRequestController@saveTransfer'), 'method' => 'post', 'id' => 'stock_transfer_form'
	]) !!}
	<div class="box box-solid">
		<div class="box-body">
			<div class="row">
				<div class="col-sm-3">
					<div class="form-group">
						{!! Form::label('transaction_date', __('messages.date') . ':*') !!}
						<div class="input-group">
							<span class="input-group-addon">
								<i class="fa fa-calendar"></i>
							</span>
							{!! Form::text('transaction_date',
							@format_datetime('now'),
							['class' => 'form-control', 'readonly', 'required']); !!}
						</div>
					</div>
				</div>
				<div class="col-sm-3">
					<div class="form-group">
						{!! Form::label('ref_no', __('purchase.ref_no').':') !!}
						{!! Form::text('ref_no', null, ['class' =>
						'form-control']); !!}
					</div>
				</div>
				<div class="col-sm-3">
					<div class="form-group">
						{!! Form::label('location_id', __('lang_v1.location_from').':*') !!}
						{!! Form::select('location_id', $business_locations,
						!empty($request_transfer->request_to_location)?$request_transfer->request_to_location:null, ['class' => 'form-control
						select2', 'placeholder' => __('messages.please_select'), 'required', 'id' => 'location_id']);
						!!}
					</div>
				</div>
				<div class="col-sm-3">
					<div class="form-group">
						{!! Form::label('from_store', __('lang_v1.from_store').':*') !!}
						<select name="from_store" id="from_store" class="form-control select2">
							<option value="">@lang('messages.please_select')</option>
						</select>
					</div>
				</div>
				<div class="col-sm-3">
					<div class="form-group">
                        {!! Form::label('transfer_location_id', __('lang_v1.location_to').':*') !!}
                        {!! Form::select('transfer_location_id', $business_locations,
						!empty($request_transfer->request_location)?$request_transfer->request_location:null, ['class' => 'form-control
                        select2', 'placeholder' => __('messages.please_select'), 'required', 'id' => 'transfer_location_id']);
                        !!}
						{{-- // <select name="transfer_location_id" id="transfer_location_id" class="form-control select2">
						// 	<option value="">@lang('messages.please_select')</option>
						// </select> --}}
					</div>
				</div>
				<div class="col-sm-3">
					<div class="form-group">
						{!! Form::label('to_store', __('lang_v1.to_store').':*') !!}
						<select name="to_store" id="to_store" class="form-control select2">
							<option value="">@lang('messages.please_select')</option>
						</select>
					</div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        {!! Form::label('status',__('lang_v1.status').':') !!}
                        {!! Form::select('status', ['issued' => __('lang_v1.issued'), 'transit' => __('lang_v1.transit')], null, ['placeholder' =>
                        __('lang_v1.please_select'), 'class' => 'form-control select2', 'required','style' => 'width:100%', 'id' =>
                        'status']); !!}
                    </div>
                </div>
                <input type="hidden" name="request_id" value="{{$request_transfer->id}}">
			</div>
		</div>
	</div>
	<!--box end-->
	<div class="box box-solid">
		<div class="box-header">
			<h3 class="box-title">{{ __('stock_adjustment.search_products') }}</h3>
		</div>
		<div class="box-body">
			{{-- <div class="row">
				<div class="col-sm-8 col-sm-offset-2">
					<div class="form-group">
						<div class="input-group">
							<span class="input-group-addon">
								<i class="fa fa-search"></i>
							</span>
							@if (!empty($request_transfer))
							{!! Form::text('search_product', null, ['class' => 'form-control', 'id' =>
							'search_product_for_srock_adjustment', 'placeholder' =>
							__('stock_adjustment.search_product')]); !!}
							@else
							{!! Form::text('search_product', null, ['class' => 'form-control', 'id' =>
							'search_product_for_srock_adjustment', 'placeholder' =>
							__('stock_adjustment.search_product'), 'disabled']); !!}
							@endif
						</div>
					</div>
				</div>
			</div> --}}
			<div class="row">
				<div class="col-sm-10 col-sm-offset-1">
					<input type="hidden" id="product_row_index" value="0">
					<input type="hidden" id="total_amount" name="final_total"
						value="0.00">
					<div class="table-responsive">
						<table class="table table-bordered table-striped table-condensed"
							id="stock_adjustment_product_table">
							<thead>
								<tr>
									<th class="col-sm-4 text-center">
										@lang('sale.product')
									</th>
									<th class="col-sm-2 text-center">
										@lang('sale.qty')
									</th>
									<th class="col-sm-2 text-center">
										@lang('sale.unit_price')
									</th>
									<th class="col-sm-2 text-center">
										@lang('sale.subtotal')
									</th>
									<th class="col-sm-2 text-center"><i class="fa fa-trash" aria-hidden="true"></i></th>
								</tr>
							</thead>
							<tbody>
							</tbody>
							<tfoot>
								<tr class="text-center">
									<td colspan="3"></td>
									<td>
										<div class="pull-right"><b>@lang('stock_adjustment.total_amount'):</b> <span
												id="total_adjustment">0.00</span></div>
									</td>
								</tr>
							</tfoot>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>
	<!--box end-->
	<div class="box box-solid">
		<div class="box-body">
			<div class="row">
				<div class="col-sm-4">
					<div class="form-group">
						{!! Form::label('shipping_charges', __('lang_v1.shipping_charges') . ':') !!}
						{!! Form::text('shipping_charges',
						0, ['class' => 'form-control
						input_number', 'placeholder' => __('lang_v1.shipping_charges')]); !!}
					</div>
				</div>
				<div class="col-sm-4">
					<div class="form-group">
						{!! Form::label('additional_notes',__('purchase.additional_notes')) !!}
						{!! Form::textarea('additional_notes',
						null, ['class' =>
						'form-control', 'rows' => 3]); !!}
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-sm-12">
					<button type="submit" id="save_stock_transfer"
						class="btn btn-primary pull-right">@lang('messages.save')</button>
				</div>
			</div>

        </div>
	</div>
	<!--box end-->
	{!! Form::close() !!}
</section>
@stop
@section('javascript')
<script src="{{ asset('js/stock_transfer.js?v=' . $asset_v) }}"></script>
<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	$('#location_id').change(function(){
		location_id = $('#location_id').val();
		$.ajax({
			method: 'get',
			url: '/stock-transfer/get_transfer_location/'+location_id,
			data: { },
			success: function(result) {
				
				$('#transfer_location_id').empty();
				$.each(result, function(i, location) {
					$('#transfer_location_id').append(`<option value= "`+location.id+`">`+location.name+`</option>`);
				});
				$.ajax({
					method: 'get',
					url: '/stock-transfer/get_transfer_store_id/'+$('#transfer_location_id').val(),
					data: { },
					success: function(result) {
						
						$('#to_store').empty();
						$.each(result, function(i, location) {
							$('#to_store').append(`<option value= "`+location.id+`">`+location.name+`</option>`);
						});
					},
				});
			},
		});

		$.ajax({
			method: 'get',
			url: '/stock-transfer/get_transfer_store_id/'+$('#location_id').val(),
			data: { },
			success: function(result) {
				$('#from_store').empty();
				$.each(result, function(i, location) {
					$('#from_store').append(`<option value= "`+location.id+`">`+location.name+`</option>`);
				});
			},
		});
			
	});

	$('#transfer_location_id').change(function(){
		let check_store_not = null;
		if($(this).val() == $('#location_id').val()){
            check_store_not = $('#from_store').val();
		}
		$.ajax({
			method: 'get',
			url: '/stock-transfer/get_transfer_store_id/'+$('#transfer_location_id').val(),
			data: { check_store_not: check_store_not},
			success: function(result) {
				
				$('#to_store').empty();
				$.each(result, function(i, location) {
					$('#to_store').append(`<option value= "`+location.id+`">`+location.name+`</option>`);
				});
			},
		});

	});

	function update_table_total() {
		var table_total = 0;
		$('table#stock_adjustment_product_table tbody tr').each(function() {
			var this_total = parseFloat(__read_number($(this).find('input.product_line_total')));
			if (this_total) {
				table_total += this_total;
			}
		});
		$('input#total_amount').val(table_total);
		$('span#total_adjustment').text(__number_f(table_total));
	}

		@if(!empty($request_transfer->request_to_location))
			
			let base_url = '{{URL::to('/')}}';
				$.ajax({
					method: 'get',
					url: base_url+'/stock-transfer/get_transfer_store_id_temp/{{$request_transfer->request_to_location}}',
					data: { },
					success: function(result) {
						console.log(result);
						
						$('#from_store').empty();
						$.each(result, function(i, location) {
							$('#from_store').append(`<option  value= "`+location.id+`">`+location.name+`</option>`);
						});
						$('#from_store option[value="{{!empty($request_transfer->from_store)?$request_transfer->from_store:1}}"]').attr("selected", "selected");
					},
				});
		@endif
   
		$(document).ready(function(){
            $('#transfer_location_id').trigger('change');
            base_url = '{{URL::to('/')}}';
			variation_id = {{$variation_id->id}};
			temp_qty = {{$request_transfer->qty}};
			row_index =  0;
			var location_id = $('select#location_id').val();
			$.ajax({
				method: 'POST',
				url: base_url+'/stock-adjustments/get_product_row_temp',
				data: { row_index: row_index, variation_id: variation_id, location_id: location_id, temp_qty: temp_qty },
				dataType: 'html',
				success: function(result) {
					$('table#stock_adjustment_product_table tbody').append(result);
					update_table_total();
					$('#product_row_index').val(row_index + 1);
				},
            });
            console.log('asdf');
        })
        
</script>
@endsection