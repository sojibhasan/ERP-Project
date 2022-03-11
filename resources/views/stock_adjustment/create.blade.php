@extends('layouts.app')
@section('title', __('stock_adjustment.add'))

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
<br>
    <h1>@lang('stock_adjustment.add')</h1>
    <!-- <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Level</a></li>
        <li class="active">Here</li>
    </ol> -->
</section>

<!-- Main content -->
<section class="content no-print">
	{!! Form::open(['url' => action('StockAdjustmentController@store'), 'method' => 'post', 'id' => 'stock_adjustment_form' ]) !!}
	<div class="box box-solid">
		<div class="box-body">
			<div class="row">
				<div class="col-sm-3">
					<div class="form-group">
						{!! Form::label('location_id', __('purchase.business_location').':*') !!}
						{!! Form::select('location_id', $business_locations, null, ['class' => 'form-control select2', 'placeholder' => __('messages.please_select'), 'required']); !!}
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
						{!! Form::label('ref_no', __('purchase.ref_no').':') !!}
						{!! Form::text('ref_no', !empty($temp_data->ref_no)?$temp_data->ref_no: $ref_no, ['class' => 'form-control']); !!}
					</div>
				</div>
				<div class="col-sm-3">
					<div class="form-group">
						{!! Form::label('transaction_date', __('messages.date') . ':*') !!}
						<div class="input-group">
							<span class="input-group-addon">
								<i class="fa fa-calendar"></i>
							</span>
							{!! Form::text('transaction_date', @format_datetime(!empty($temp_data->transaction_date)?$temp_data->transaction_date:'now'), ['class' => 'form-control', 'readonly', 'required']); !!}
						</div>
					</div>
				</div>
				<div class="col-sm-3">
					<div class="form-group">
						{!! Form::label('adjustment_type', __('stock_adjustment.adjustment_type') . ':*') !!} @show_tooltip(__('tooltip.adjustment_type'))
						{!! Form::select('adjustment_type', [ 'normal' =>  __('stock_adjustment.normal'), 'abnormal' =>  __('stock_adjustment.abnormal')], !empty($temp_data->adjustment_type)?$temp_data->adjustment_type:null, ['class' => 'form-control select2', 'placeholder' => __('messages.please_select'), 'required']); !!}
					</div>
				</div>
			</div>
		</div>
	</div> <!--box end-->
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
							{!! Form::text('search_product', null, ['class' => 'form-control', 'id' => 'search_product_for_srock_adjustment', 'placeholder' => __('stock_adjustment.search_product')]); !!}
							@else
							{!! Form::text('search_product', null, ['class' => 'form-control', 'id' => 'search_product_for_srock_adjustment', 'placeholder' => __('stock_adjustment.search_product'), 'disabled']); !!}
							@endif
						</div>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-sm-10 col-sm-offset-1">
					<input type="hidden" id="product_row_index" value="0">
					<input type="hidden" id="total_amount" name="final_total" value="{{!empty($temp_data->final_total)?$temp_data->final_total:0}}">
					<div class="table-responsive">
					<table class="table table-bordered table-striped table-condensed"
					id="stock_adjustment_product_table">
						<thead>
							<tr>
								<th class="col-sm-3 text-center">
									@lang('sale.product')
								</th>
								<th class="col-sm-1 text-center">
									@lang('sale.current_qty')
								</th>
								<th class="col-sm-2 text-center">
									@lang('sale.unit')
								</th>
								<th class="col-sm-2 text-center">
									@lang('sale.type')
								</th>
								<th class="col-sm-2 text-center">
									@lang('sale.qty')
								</th>
								<th class="col-sm-1 text-center">
									@lang('sale.unit_purchase_price')
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
							<tr class="text-center"><td colspan="3"></td><td><div class="pull-right"><b>@lang('stock_adjustment.total_amount'):</b> <span id="total_adjustment">0.00</span></div></td></tr>
						</tfoot>
					</table>
					</div>
				</div>
			</div>
		</div>
	</div> <!--box end-->
	<div class="box box-solid">
		<div class="box-body">
			<div class="row">
				<div class="col-sm-4">
					<div class="form-group">
							{!! Form::label('total_amount_recovered', __('stock_adjustment.total_amount_recovered') . ':') !!} @show_tooltip(__('tooltip.total_amount_recovered'))
							{!! Form::text('total_amount_recovered', !empty($temp_data->total_amount_recovered)?$temp_data->total_amount_recovered:0, ['class' => 'form-control input_number', 'placeholder' => __('stock_adjustment.total_amount_recovered')]); !!}
					</div>
				</div>
				<div class="col-sm-4">
					<div class="form-group">
							{!! Form::label('inventory_adjustment_account', __('lang_v1.inventory_adjustment_account') . ':') !!}
							{!! Form::select('inventory_adjustment_account', $inventory_adjustment_accounts, null, ['class' => 'form-control select2', 'placeholder' => __('messages.please_select'), 'required']); !!}
					</div>
				</div>
				<div class="col-sm-4">
					<div class="form-group">
							{!! Form::label('additional_notes', __('stock_adjustment.reason_for_stock_adjustment') . ':') !!}
							{!! Form::textarea('additional_notes', !empty($temp_data->additional_notes)?$temp_data->additional_notes:null, ['class' => 'form-control', 'placeholder' => __('stock_adjustment.reason_for_stock_adjustment'), 'rows' => 3]); !!}
					</div>
				</div>
			</div>
{{--			<select class="form-control" id="inventory_type_id" name="type_id" required>--}}
{{--				<option value="3">Increase</option>--}}
{{--				<option value="4">Decrease</option>--}}
{{--			</select>--}}
			<div class="row">
				<div class="col-sm-12">
					<button type="submit" id="stock_transfer_submit" class="btn btn-primary pull-right">@lang('messages.save')</button>
				</div>
			</div>

		</div>
	</div> <!--box end-->
	{!! Form::close() !!}
</section>
@stop
@section('javascript')
	<script src="{{ asset('js/stock_adjustment.js?v=' . $asset_v) }}"></script>

	<script>
		// get inventoryTypeAccount
		$("#inventory_type_id").on('change',function(){
			var inventoryId = $(this).val();
			console.log(inventoryId);
			$("#inventory_adjustment_account").html('');
			$.ajax({
				type: "POST",
					url: "{{url('stock-adjustments/get_inventory_account')}}",
				data:{inventoryId:inventoryId},
				success: function(data){
					console.log(data);
					$.each(data.accounts, function (key, value) {
						$("#inventory_adjustment_account").append('<option value="' + value
								.id + '">' + value.name + '</option>');
					});
				}
			});
		})
			$(document).ready(function () {
				$('#location_id option:eq(1)').attr('selected', true).trigger('change');
			})

		$('#stock_transfer_submit').click(function(e){
			e.preventDefault();

			 //check if internet available or not
			 var offline = Offline.state;

			if(offline == 'down'){
				toastr.error('No internet connection available');
				return offline;
			}else{
				$('form#stock_adjustment_form').submit();
			}
		});
	</script>

	<script>

		$.ajaxSetup({
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			}
		});

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


$('#location_id').change(function(){
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

	@if(auth()->user()->can('unfinished_form.stock_adjustment'))

	setInterval(function(){
		$.ajax({
				method: 'POST',
				url: '{{action("TempController@saveStockAdjustmentTemp")}}',
				dataType: 'json',
				data: $('#stock_adjustment_form').serialize(),
				success: function(data) {
				console.log(data);
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
					$('#from_store option[:first]').attr("selected", "selected");
				},
			});
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
				window.location.href = "{{action('TempController@clearData', ['type' => 'stock_adjustment_data'])}}";
			}
		});
	@endif
	@endif
	</script>
@endsection
