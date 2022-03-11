@extends('layouts.app')
@section('title', __('lang_v1.add_stock_transfer'))

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
	<h1>@lang('lang_v1.add_stock_transfer')</h1>
</section>

<!-- Main content -->
<section class="content no-print">
	{!! Form::open(['url' => action('StockTransferController@store'), 'method' => 'post', 'id' => 'stock_transfer_form'
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
							@format_datetime(!empty($temp_data->transaction_date)?$temp_data->transaction_date:'now'),
							['class' => 'form-control', 'readonly', 'required']); !!}
						</div>
					</div>
				</div>
				{{--   
				* @ModifiedBy Afes Oktavianus
				* @DateBy 27-05-2021
				* @DateBy 03-06-2021
				* @task 1523 
				* @task 127005
				--}}
				<div class="col-sm-3">
					<div class="form-group">
						{!! Form::label('ref_no', __('purchase.ref_no').':') !!}
						{!! Form::text('ref_no', !empty($temp_data->ref_no)?$temp_data->ref_no:(!empty($stock_transfer_form_no) ? $stock_transfer_form_no : null), ['class' =>
						'form-control', 'readonly'=>'readonly']); !!}
					</div>
				</div>
				<div class="col-sm-3">
					<div class="form-group">
						{!! Form::label('location_id', __('lang_v1.location_from').':*') !!}
						{!! Form::select('location_id', $business_locations,
						!empty($temp_data->location_id)?$temp_data->location_id:(!empty($default_location) ? $default_location :null), ['class' => 'form-control
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
						<select name="transfer_location_id" id="transfer_location_id" class="form-control select2">
							<option value="">@lang('messages.please_select')</option>
						</select>
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

			</div>
		</div>
	</div>
	<!--box end-->
	<div class="box box-solid">
		<div class="box-header">
			<h3 class="box-title">{{ __('stock_adjustment.search_products') }}</h3>
		</div>
		<div class="box-body">
			<div class="row">
				<div class="col-sm-8 col-sm-offset-2">
					<div class="form-group">
						<div class="input-group">
							<span class="input-group-addon">
								<i class="fa fa-search"></i>
							</span>
							@if (!empty($temp_data))
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
			</div>
			<div class="row">
				<div class="col-sm-10 col-sm-offset-1">
					<input type="hidden" id="product_row_index" value="0">
					<input type="hidden" id="total_amount" name="final_total"
						value="{{!empty($temp_data->final_total)?$temp_data->final_total:0}}">
					<div class="table-responsive">
						<table class="table table-bordered table-striped table-condensed"
							id="stock_adjustment_product_table">
							<thead>
								<tr>
									<th class="col-sm-4 text-center">
										@lang('sale.product')
									</th>
									<th class="col-sm-2 text-center">
										@lang('sale.balance_qty')
									</th>
									<th class="col-sm-2 text-center">
										@lang('unit.units')
									</th>
									<th class="col-sm-2 text-center">
										@lang('sale.transfer_qty')
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
									<td colspan="5"></td>
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
						!empty($temp_data->shipping_charges)?$temp_data->shipping_charges:0, ['class' => 'form-control
						input_number', 'placeholder' => __('lang_v1.shipping_charges')]); !!}
					</div>
				</div>
				<div class="col-sm-4">
					<div class="form-group">
						{!! Form::label('additional_notes',__('purchase.additional_notes')) !!}
						{!! Form::textarea('additional_notes',
						!empty($temp_data->additional_notes)?$temp_data->additional_notes:null, ['class' =>
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
	/**
	 * @ModifiedBy Afes Oktavianus
	 * @DateBy 01-06-2021
	 * @Task 127003
	 */
	$(document).ready(function() {
		//Default Location
		$.ajax({
			method: 'get',
			url: '/stock-transfer/get_transfer_store_id/'+$('#location_id').val(),
			data: { },
			success: function(result) {
				$('#from_store').empty();
				$.each(result, function(i, location) {
					$('#from_store').append(`<option value= "`+location.id+`">`+location.name+`</option>`);                
				});
				$('#search_product_for_srock_adjustment').removeAttr('disabled');
				$('table#stock_adjustment_product_table tbody').html('');
				$('#product_row_index').val(0);
			},
		});		

		$.ajax({
			method: 'get',
			url: '/stock-transfer/get_transfer_location/'+$('#location_id').val(),
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
			}
		})
	});

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

@if(auth()->user()->can('unfinished_form.stock_transfer'))
		setInterval(function(){ 
			$.ajax({
					method: 'POST',
					url: '{{action("TempController@saveStockTransferTemp")}}',
					dataType: 'json',
					data: $('#stock_transfer_form').serialize(),
					success: function(data) {
					// console.log(data);
					},
				});
		}, 10000);
		@if(!empty($temp_data->location_id))
			
			let base_url = '{{URL::to('/')}}';
				$.ajax({
					method: 'get',
					url: base_url+'/stock-transfer/get_transfer_store_id_temp/{{$temp_data->location_id}}',
					data: { },
					success: function(result) {
						console.log(result);
						
						$('#from_store').empty();
						$.each(result, function(i, location) {
							$('#from_store').append(`<option  value= "`+location.id+`">`+location.name+`</option>`);
						});
						$('#from_store option[value="{{!empty($temp_data->from_store)?$temp_data->from_store:1}}"]').attr("selected", "selected");
					},
				});
		@endif
	
		@if(!empty($temp_data->transfer_location_id))
		setTimeout(() => {
			$.ajax({
				method: 'get',
				url: '/stock-transfer/get_transfer_location_temp/{{$temp_data->location_id}}',
				data: { },
				success: function(result) {
					
					$('#transfer_location_id').empty();
					$.each(result, function(i, location) {
						$('#transfer_location_id').append(`<option value= "`+location.id+`">`+location.name+`</option>`);
					});
					$('#transfer_location_id option[value="{{$temp_data->transfer_location_id}}"]').attr("selected", "selected");
	
					@if(!empty($temp_data->to_store))
						let check_store_not = $('#from_store').val();
						$.ajax({
							method: 'get',
							url: '/stock-transfer/get_transfer_store_id_temp/{{$temp_data->transfer_location_id}}',
							data: { check_store_not: check_store_not},
							success: function(result) {
								$('#to_store').empty();
								$.each(result, function(i, location) {
									$('#to_store').append(`<option value= "`+location.id+`">`+location.name+`</option>`);
								});
								$('#to_store option[value="{{$temp_data->to_store}}"]').attr("selected", "selected");
							},
						});
					@endif
				},
			});
		}, 1000);
		@endif
	
		@if(!empty($temp_data->products))
		@foreach($temp_data->products as $key => $product)
			base_url = '{{URL::to('/')}}';
			variation_id = {{$product->variation_id}};
			temp_qty = {{$product->quantity}};
			row_index =  {{$key}};
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
		@endforeach
		@endif


	@if(!empty($temp_data))
		swal({
			title: "Do you want to load unsaved data?",
			icon: "info",
			buttons: {
				confirm: {
					text: "Yes",
					value: false,
					visible: true,
					className: "",
					closeModal: true
				},
				cancel: {
					text: "No",
					value: true,
					visible: true,
					className: "",
					closeModal: true,
				}
				
			},
			dangerMode: false,
		}).then((sure) => {
			if(sure){
				window.location.href = "{{action('TempController@clearData', ['type' => 'stock_transfer_data'])}}";
			} 
		});
	@endif
@endif

    $(document).ready(function(){
        
        $("#from_store").change(function(){
            selectRandomToStore();
        })
        
        $("#to_store").change(function(){
            selectRandomFromStore();
        })
        
    })
    
    $(window).load(function(){
        setTimeout(function(){
            selectRandomToStore();
        }, 5000);
    })

    function selectRandomToStore(){
        
        let from_text = $("#select2-from_store-container").html();
        let to_text = $("#select2-to_store-container").html();
        let indexes = [];
        
        if(from_text == to_text){
            
            $("#to_store option").each(function(){
                if($(this).val() != $('#from_store').val()){
                    indexes.push($(this).val());
                }
            });
            
            $("#to_store").val(indexes[Math.floor(Math.random() * indexes.length)]);
            $("#to_store").trigger('change');
            
        }
        
    }
    
    function selectRandomFromStore(){
        
        let from_text = $("#select2-from_store-container").html();
        let to_text = $("#select2-to_store-container").html();
        let indexes = [];
        
        if(from_text == to_text){
            
            $("#from_store option").each(function(){
                if($(this).val() != $('#to_store').val()){
                    indexes.push($(this).val());
                }
            });
            
            $("#from_store").val(indexes[Math.floor(Math.random() * indexes.length)]);
            $("#from_store").trigger('change');
            
        }
        
    }

</script>
@endsection