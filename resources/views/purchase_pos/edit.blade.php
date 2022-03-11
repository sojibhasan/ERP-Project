@extends('layouts.app')

@section('title', 'Purchase')

@section('content')
@inject('request', 'Illuminate\Http\Request')
<style>
	.box-header {
		padding-bottom: 0px !important;
	}

	.box-body {
		padding-top: 5px !important;

	}
</style>

<section class="content no-print">
	<!-- Page level currency setting -->
	<input type="hidden" id="p_code" value="{{$currency_details->code}}">
	<input type="hidden" id="p_symbol" value="{{$currency_details->symbol}}">
	<input type="hidden" id="p_thousand" value="{{$currency_details->thousand_separator}}">
	<input type="hidden" id="p_decimal" value="{{$currency_details->decimal_separator}}">
	<input type="hidden" name="enable_code" id="enable_code"
		value="{{ !empty($search_product_settings['enable_code'])? 1: '' }}">
	<input type="hidden" name="enable_rack_number" id="enable_rack_number"
		value="{{ !empty($search_product_settings['enable_rack_number'])? 1: '' }}">
	<input type="hidden" name="enable_qty" id="enable_qty"
		value="{{ !empty($search_product_settings['enable_qty'])? 1: '' }}">
	<input type="hidden" name="enable_product_cost" id="enable_product_cost"
		value="{{ !empty($search_product_settings['enable_product_cost'])? 1: '' }}">
	<input type="hidden" name="enable_product_supplier" id="enable_product_supplier"
		value="{{ !empty($search_product_settings['enable_product_supplier'])? 1: '' }}">
	@if(session('business.enable_rp') == 1)
	<input type="hidden" id="reward_point_enabled">
	@endif
	<div class="row">
		<div class="left_div  col-md-6 col-sm-12">
			@component('components.widget', ['class' => 'box-success'])
			@slot('header')
			<div class="col-md-12">
				<div class="col-md-12">
					<p class="text-right  pull-left"><strong>@lang('sale.location'):</strong>
						{{$default_location->name}}
					</p>
				</div>
				<div class="col-md-6">
					<h4 class="entry_no" style="margin: 0; width: 150px;">@lang('lang_v1.purchase_entry_no'): <span
							class="entry_no_span">{{$purchase->purchase_entry_no}}</span></h4>
				</div>
				<div class="col-md-6">
					<h4 class="invoice_no" style="margin: 0; width: 150px;">@lang('lang_v1.purchase_invoice_no'): <span
							class="invoice_no_span">{{$purchase->invoice_no}}</span></h4>
				</div>
			</div>
			<div class="col-md-8">
				<br>
				<div class="col-md-6 text-red" style="font-size: 18px; font-weight: bold">
					@lang('lang_v1.supplier'): <span class="supplier_name"></span>
				</div>
				<div class="col-md-6 text-red" style="font-size: 18px; font-weight: bold">
					@lang('lang_v1.due_amount'): <span class="supplier_due_amount"></span>
				</div>
			</div>
			<input type="hidden" id="item_addition_method" value="{{$business_details->item_addition_method}}">

			<input type="hidden" id="service_addition_method" value="{{$business_details->service_addition_method}}">
			@endslot
			{!! Form::open(['url' => action('PurchasePosController@update', $purchase->id), 'method' => 'PUT', 'id' =>
			'edit_pos_purchase_form'
			]) !!}
			<input type="hidden" name="purchase_entry_no" value="{{$purchase->purchase_entry_no}}"
				id="purchase_entry_no">
			{!! Form::hidden('location_id', $default_location->id, ['id' => 'location_id', 'data-receipt_printer_type'
			=> !empty($default_location->receipt_printer_type) ? $default_location->receipt_printer_type : 'browser',
			'data-default_accounts' => $default_location->default_payment_accounts]); !!}
			<style>
				.select2-drop-active {
					margin-top: -25px;
				}
			</style>
			<!-- /.box-header -->
			{!! Form::hidden('transaction_date',
			@format_datetime($purchase->transaction_date),
			['class' => 'form-control', 'id'=>'datetimepicker_pos',
			'readonly', 'required']); !!}
			{!! Form::hidden('row_count',
			$purchase->purchase_lines->count(),
			['class' => 'form-control', 'id'=>'row_count',
			'readonly', 'required']); !!}
			{!! Form::hidden('invoice_no', $purchase->invoice_no, ['class' => 'form-control',
			'readonly', 'id' => 'invoice_no'] ); !!}
			{!! Form::hidden('grand_total_hidden', 0, ['class' => 'form-control',
			'readonly', 'id' => 'grand_total_hidden'] ); !!}
			<input type="hidden" id="total_subtotal_input" value=0 name="total_before_tax">
			<div class="box-body">
				<div class="row">

					@if(request()->session()->get('business.is_pharmacy') ||
					request()->session()->get('business.is_hospital'))
					<div class="col-md-6 col-sm-6">
						<div class="form-group">
							@if (request()->session()->get('business.is_pharmacy'))
							{!! Form::label('patients', __('patient.patients') . ':*') !!}
							@endif
							@if (request()->session()->get('business.is_hospital'))
							{!! Form::label('patients', __('patient.patient_cusotmer') . ':*') !!}
							@endif
							<div class="input-group">
								<span class="input-group-addon">
									<i class="fa fa-frown-o"></i>
								</span>
								{!! Form::select('patient', [],null, [ 'placeholder' => 'Select patient','class' =>
								'form-control
								select2', 'id' => 'pos_patients']); !!}


							</div>
						</div>
					</div>
					@endif

					<div class="col-md-4 col-sm-6 @if(!$currency_details->purchase_in_diff_currency) hide @endif">
						<div class="form-group">
							<div class="input-group">
								<span class="input-group-addon">
									<i class="fa fa-exchange"></i>
								</span>
								{!! Form::text('exchange_rate',
								$currency_details->p_exchange_rate,
								['class' =>
								'form-control input-sm input_number', 'placeholder' =>
								__('lang_v1.currency_exchange_rate'), 'id' => 'exchange_rate']); !!}
							</div>
						</div>
					</div>
					<div class="col-sm-4 @if(!empty($default_purchase_status)) hide @endif">
						<div class="form-group">
							{!! Form::label('status', __('purchase.purchase_status') . ':*') !!}
							@show_tooltip(__('tooltip.order_status'))
							{!! Form::select('status', $orderStatuses,
							$purchase->status, ['class' =>
							'form-control
							select2',
							'placeholder' => __('messages.please_select'), 'required']); !!}
						</div>
					</div>
					<div class="col-sm-4">
						<div class="form-group">
							{!! Form::label('store_id', __('lang_v1.store_id').':*') !!}
							{!! Form::select('store_id', $stores,
							$purchase->store_id, ['class' =>
							'form-control
							select2',
							'placeholder' => __('messages.please_select'), 'required']); !!}
						</div>
					</div>

					@if(in_array('types_of_service', $enabled_modules) && !empty($types_of_service))
					<div class="col-md-4 col-sm-6">
						<div class="form-group">
							<div class="input-group">
								<span class="input-group-addon">
									<i class="fa fa-external-link text-primary service_modal_btn"></i>
								</span>
								{!! Form::select('types_of_service_id', $types_of_service,
								!empty($temp_data->types_of_service_id)?$temp_data->types_of_service_id:null, ['class'
								=>
								'form-control', 'id' => 'types_of_service_id', 'style' => 'width: 100%;', 'placeholder'
								=> __('lang_v1.select_types_of_service')]); !!}

								{!! Form::hidden('types_of_service_price_group',
								!empty($temp_data->types_of_service_price_group)?$temp_data->types_of_service_price_group:
								null, ['id' =>
								'types_of_service_price_group']) !!}

								<span class="input-group-addon">
									@show_tooltip(__('lang_v1.types_of_service_help'))
								</span>
							</div>
							<small>
								<p class="help-block hide" id="price_group_text">@lang('lang_v1.price_group'):
									<span></span></p>
							</small>
						</div>
					</div>
					<div class="modal fade types_of_service_modal" tabindex="-1" role="dialog"
						aria-labelledby="gridSystemModalLabel"></div>
					@endif

					@if(in_array('subscription', $enabled_modules))
					<div class="col-md-4 pull-right col-sm-6">
						<div class="checkbox">
							<label>
								{!! Form::checkbox('is_recurring', 1, false, ['class' => 'input-icheck', 'id' =>
								'is_recurring']); !!} @lang('lang_v1.subscribe')?
							</label><button type="button" data-toggle="modal" data-target="#recurringInvoiceModal"
								class="btn btn-link"><i
									class="fa fa-external-link"></i></button>@show_tooltip(__('lang_v1.recurring_invoice_help'))
						</div>
					</div>
					@endif
				</div>
				<div class="row">
					<div class="@if(!empty($commission_agent)) col-sm-4 @else col-sm-6 @endif">
						<div class="form-group" style="width: 100% !important">
							<div class="input-group">
								<span class="input-group-addon">
									<i class="fa fa-user"></i>
								</span>
								<input type="hidden" id="default_supplier_id" value="{{ $purchase->contact->id }}">
								<input type="hidden" id="default_supplier_name" value="{{ $purchase->contact->name }}">
								{!! Form::select('contact_id',
								[], null, ['class' =>
								'form-control mousetrap', 'id' => 'supplier_id', 'placeholder' =>
								'Enter supplier name / phone', 'required', 'style' => 'width: 100%;']); !!}
								<span class="input-group-btn">
									<button type="button" class="btn btn-default bg-white btn-flat add_new_supplier"
										data-name="" @if(!auth()->user()->can('supplier.create')) disabled @endif><i
											class="fa fa-plus-circle text-primary fa-lg"></i></button>
								</span>
							</div>
						</div>
					</div>
					<input type="hidden" name="pay_term_number" id="pay_term_number"
						value="{{!empty($default_supplier['pay_term_number'])?$default_supplier['pay_term_number']:null}}">
					<input type="hidden" name="pay_term_type" id="pay_term_type"
						value="{{!empty($default_supplier['pay_term_type'])?$default_supplier['pay_term_type']:null}}">

					@if(!empty($commission_agent))
					<div class="col-sm-4">
						<div class="form-group">
							{!! Form::select('commission_agent',
							$commission_agent, !empty($temp_data->commission_agent)?$temp_data->commission_agent:null,
							['class' => 'form-control select2', 'placeholder' =>
							__('lang_v1.commission_agent')]); !!}
						</div>
					</div>
					@endif

					<div class="@if(!empty($commission_agent)) col-sm-4 @else col-sm-6 @endif">
						<div class="form-group">
							<div class="input-group">
								<div class="input-group-btn">
									<button type="button" class="btn btn-default bg-white btn-flat" data-toggle="modal"
										data-target="#configure_search_modal"
										title="{{__('lang_v1.configure_product_search')}}"><i
											class="fa fa-barcode"></i></button>
								</div>
								{!! Form::text('search_product', null, ['class' => 'form-control mousetrap', 'id' =>
								'search_product', 'placeholder' => __('lang_v1.search_product_placeholder'),
								'disabled' => is_null($default_location)? true : false,
								'autofocus' => is_null($default_location)? false : true,
								]); !!}
								<span class="input-group-btn">
									<button type="button"
										class="btn btn-default bg-white btn-flat pos_add_quick_product"
										data-href="{{action('ProductController@quickAdd')}}"
										data-container=".quick_add_product_modal"><i
											class="fa fa-plus-circle text-primary fa-lg"></i></button>
								</span>
							</div>
						</div>
					</div>
					<div class="clearfix"></div>

					<!-- Call restaurant module if defined -->
					@if(in_array('tables' ,$enabled_modules) || in_array('service_staff' ,$enabled_modules))
					<span id="restaurant_module_span">
						<div class="col-md-3"></div>
					</span>
					@endif
				</div>

				<div class="row">
					<div class="col-sm-12 pos_product_div">

						<table class="table table-condensed table-bordered table-striped table-responsive"
							id="purchase_entry_table">
							<thead>
								<tr>
									<th
										class="tex-center @if(!empty($pos_settings['inline_service_staff'])) col-md-3 @else col-md-4 @endif">
										@lang('sale.product') @show_tooltip(__('lang_v1.tooltip_sell_product_column'))
									</th>
									<th class="text-center col-md-3">
										@lang('sale.qty')
									</th>

									<th class="text-center col-md-2">
										@lang('lang_v1.discount_percent')
									</th>

									<th class="text-center col-md-2">
										@lang('sale.subtotal')
									</th>
									<th class="text-center"><i class="fa fa-close" aria-hidden="true"></i></th>
								</tr>
							</thead>
							<tbody>

								@include('purchase_pos.partials.edit_purchase_row')
							</tbody>
						</table>
					</div>
				</div>
				@include('purchase.partials.pos_details')

				@include('purchase.partials.payment_modal')

				{{-- @if(empty($pos_settings['disable_suspend']))
	@include('sale_pos.partials.suspend_note_modal')
	@endif

	@if(empty($pos_settings['disable_recurring_invoice']))
	@include('sale_pos.partials.recurring_invoice_modal')
	@endif --}}
			</div>
			<!--  temp cat id and brand id if there is any temp data  -->
			<input type="hidden" id="cat_id_suggestion" name="cat_id_suggestion"
				value="{{!empty($temp_data->cat_id_suggestion)?$temp_data->cat_id_suggestion:0}}">
			<input type="hidden" id="brand_id_suggestion" name="brand_id_suggestion"
				value="{{!empty($temp_data->brand_id_suggestion)?$temp_data->brand_id_suggestion:0}}">
			<input type="hidden" name="is_pos" value="1" id="is_pos">
			<input type="hidden" name="is_duplicate" value="0" id="is_duplicate">
			<input type="hidden" name="was_customer_wallet" id="was_customer_wallet" value=0>
			<input type="hidden" name="in_customer_wallet" id="in_customer_wallet" value=0>
			<input type="hidden" name="purchase_pos" id="purchase_pos" value="1">
			{!! Form::hidden('tax_amount',!empty($temp_data->tax_amount)?$temp_data->tax_amount: 0,
			['id' => 'tax_amount']); !!}
			<!-- /.box-body -->
			{!! Form::close() !!}
			@endcomponent
		</div>

		<div class="col-md-6 col-sm-12 right_div">
			@include('sale_pos.partials.right_div')
		</div>
	</div>
</section>

<!-- This will be printed -->
<section class="invoice print_section" id="receipt_section">
</section>
<div class="modal fade contact_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
	@include('contact.create', ['quick_add' => true])
</div>
<div class="modal close_register_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
</div>
<div class="modal quick_return_modal" id="quick_return_modal" role="dialog"></div>
<!-- quick product modal -->
<div class="modal fade quick_add_product_modal" tabindex="-1" role="dialog" aria-labelledby="modalTitle"></div>
<div class="modal fade patient_prescriptions_modal" role="dialog" aria-labelledby="modalTitle"></div>
@include('sale_pos.partials.configure_search_modal')

@stop

@section('javascript')
<script>
	base_url = '{{URL::to('/')}}';
</script>
<script src="{{ asset('js/purchase.js?v=' . $asset_v) }}"></script>
<script src="{{ asset('js/printer.js?v=' . $asset_v) }}"></script>
<script src="{{ asset('js/product.js?v=' . $asset_v) }}"></script>
@include('sale_pos.partials.keyboard_shortcuts')




<script>
	$( document ).ready(function() {
		setTimeout(() => {
			$(".payment_method").val($(".payment_method option:eq(1)").val());
			$(".payment_method").selectmenu().selectmenu("refresh");
			@can('is_service_staff')
			$("#res_waiter_id").val("{{auth()->user()->id}}");
			$("#res_waiter_id").trigger('change.select2'); 
			@endcan
			$('.purchase_quantity').change();
			set_default_supplier();
		}, 2000);
	});
</script>


<script>
	$('#toggle_popup').click(function(){
	$.ajax({
		url: '/toggle_popup',
		type: 'get', 
		dataType: 'json',
		success: function(result) {
			
		}
	});
});
</script>


<script>
	$('.right_div').show();
	$('.left_div').show();
	$("#hide_show_products").click(function(){
		$(".right_div").toggle();
		$('.left_div').toggleClass('col-md-7');
		$('.left_div').toggleClass('col-md-12');
  });
  $('document').ready(function(){
	// reset_pos_form();
	$('.payment_types_dropdown').val('cash');
	$('.payment_types_dropdown').trigger('change');
  });

$(document).on('change', '.payment_types_dropdown', function(e) {
    var payment_type = $(this).val();
   
	if(payment_type == 'direct_bank_deposit' || payment_type == 'bank_transfer'){
		$('.account_module').removeClass('hide');
	}else{
		$('.account_module').addClass('hide');
	}
});
</script>

<script>
	var product_row = $('input#product_row_count').val();
        var location_id = $('input#location_id').val();
        var customer_id = $('select#customer_id').val();
        var is_direct_sell = false;
        if (
            $('input[name="is_direct_sale"]').length > 0 &&
            $('input[name="is_direct_sale"]').val() == 1
        ) {
            is_direct_sell = true;
        }

        var price_group = '';
        if ($('#price_group').length > 0) {
            price_group = parseInt($('#price_group').val());
        }

        //If default price group present
        if ($('#default_price_group').length > 0 && 
            !price_group) {
            price_group = $('#default_price_group').val();
        }

        //If types of service selected give more priority
        if ($('#types_of_service_price_group').length > 0 && 
            $('#types_of_service_price_group').val()) {
            price_group = $('#types_of_service_price_group').val();
		}
</script>

<script>
	$('#request_approval').click(function(){
		let customer_id = $('#customer_id').val();

		$.ajax({
			method: 'get',
			url: '/customer-limit-approval/send-reuqest-for-approval/'+customer_id,
			data: {  },
			success: function(result) {
				if(result.success === 1){
					toastr.success(result.msg)
				}
			},
		});
	});

@if(auth()->user()->can('unfinished_form.pos'))
	setInterval(function(){ 
		$.ajax({
				method: 'POST',
				url: '{{action("TempController@saveAddPosTemp")}}',
				dataType: 'json',
				data: $('#add_pos_sell_form').serialize(),
				success: function(data) {
				},
			});
	}, 10000);


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
				window.location.href = "{{action('TempController@clearData', ['type' => 'add_pos_data'])}}";
			} 
		});
@endif
@endif


$('#supplier_id').change(function(){
	$supplier_name = $('#supplier_id :selected').text();

	if($supplier_name == "Walk-In supplier"){
		$('.credit_sale_btn_div').hide();
		$('.multipay_btn_div').removeClass('col-md-3').addClass('col-md-3');
	}else{
		$('.credit_sale_btn_div').show();
		$('.multipay_btn_div').removeClass('col-md-4').addClass('col-md-3');
	}

	$.ajax({
		method: 'get',
		url: "{{action('PurchaseController@getSupplierDetails')}}",
		data: { supplier_id : $(this).val() },
		success: function(result) {
			$('.supplier_name').text(result.supplier_name);
			$('.supplier_due_amount').text(result.due_amount);
			let due_amount_supplier = parseFloat(result.due_amount);
			if(parseFloat(due_amount_supplier) < 0){ //previous access amount supplier excess
				due_amount_supplier = due_amount_supplier *  -1 ;
				$('input#was_supplier_wallet').val(parseFloat(due_amount_supplier));
        		$('span.supplier_wallet').text(__currency_trans_from_en(parseFloat(due_amount_supplier), true));
				__write_number($('input#total_paying_input'), parseFloat(due_amount_supplier));
        		$('span.total_paying').text(__currency_trans_from_en(parseFloat(due_amount_supplier), true));
			}else{
				__write_number($('input#was_supplier_wallet'), 0);
        		$('span.supplier_wallet').text(__currency_trans_from_en(0, true));
			}
			calculate_balance_due();
			if(result.sol_with_approval === 1){
				$('#request_approval').removeClass('hide');
			}else{
				$('#request_approval').addClass('hide');
			}
		},
	});
});


$(document).on('click','#verify_password_btn', function(){
	$.ajax({
		method: 'post',
		url: '/check_user_password',
		data: { password : $('#verify_password').val() },
		success: function(result) {
			if(result.success == 1){
				$('#verify_password_modal').find('.modal-title').empty().text('Enter Invoice');
				$('#verify_password_modal').find('.modal-body').empty().append(`
				<input type="text" id="return_invoice" name="return_invoice" placeholder="@lang('lang_v1.enter_invoice')"
					style="margin-auto;" class="form-control">
				`);
				$('#verify_password_modal').find('.modal-footer').empty().append(`
				<button type="button" id="return_invoice_btn" class="btn btn-primary">Submit</button>
        		<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
				`);
			}else{
				toastr.error('Password does not match');
			}
		},
	});

});

$(document).on('click','#return_invoice_btn', function(){
	let return_invoice = $('#return_invoice').val();
	$.ajax({
		method: 'get',
		url: '/sell-return/add/'+return_invoice,
		data: {  },
		success: function(result) {
			if(result.success == 0){
				$('#verify_password_modal').modal('hide')
				toastr.error(result.msg);
				return false;
			}else{
				$('#verify_password_modal').modal('hide');
				resetVerifyPasswordModal();
				$('.quick_return_modal').empty().append(result);
				$('.quick_return_modal').modal('show');
				$('#pos_invoice_return').val($('.invoice_no_span').text());
			}
		},
	});

});

function resetVerifyPasswordModal(){
	$('#verify_password_modal').find('.modal-title').empty().text('Enter Password');
	$('#verify_password_modal').find('.modal-body').empty().append(`
		<input type="password" id="verify_password" name="verify_password" placeholder="@lang('lang_v1.enter_password')"
		style="margin-auto;" class="form-control">
		`);
		$('#verify_password_modal').find('.modal-footer').empty().append(`
		<button type="button" id="verify_password_btn" class="btn btn-primary">@lang('lang_v1.verify')</button>
		<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
	`);
}

</script>





<script type="text/javascript">
	$(document).ready( function(){
		$('form#sell_return_form').validate();
		update_sell_return_total();
	});

	$(document).on('click', '#sell_return_submit',function(e){
		e.preventDefault();
		var data = $('form#sell_return_form').serialize();

	  	$.ajax({
			method: 'POST',
			url: "{{action('SellReturnController@savePosReturn')}}",
			dataType: 'json',
			data: data,
			success: function(result) {
				var location_id = $('input#location_id').val();
				if (result.success == true) {
					$('.quick_return_modal').modal('hide');
					jQuery.each(result.returns, function(id, obj){
						id = Object.keys(obj);
						qty = Object.values(obj);
						add_pos_product_row(qty * -1, id, location_id);
						$('input#product_row_count').val(parseInt($('input#product_row_count').val()) + 1);  
					})
				} else {
					toastr.error(result.msg);
				}
				
			},
		});
	});
	
	$(document).on('change', 'input.return_qty, #discount_amount, #discount_type', function(){
		update_sell_return_total()
	});

	function update_sell_return_total(){
		var net_return = 0;
		$('table#sell_return_table tbody tr').each( function(){
			var quantity = __read_number($(this).find('input.return_qty'));
			var unit_price = __read_number($(this).find('input.unit_price'));
			var subtotal = quantity * unit_price;
			$(this).find('.return_subtotal').text(__currency_trans_from_en(subtotal, true));
			net_return += subtotal;
		});
		var discount = 0;
		if($('#discount_type').val() == 'fixed'){
			discount = __read_number($("#discount_amount"));
		} else if($('#discount_type').val() == 'percentage'){
			var discount_percent = __read_number($("#discount_amount"));
			discount = __calculate_amount('percentage', discount_percent, net_return);
		}
		discounted_net_return = net_return - discount;

		var tax_percent = $('input#tax_percent').val();
		var total_tax = __calculate_amount('percentage', tax_percent, discounted_net_return);
		var net_return_inc_tax = total_tax + discounted_net_return;

		$('input#tax_amount').val(total_tax);
		$('span#total_return_discount').text(__currency_trans_from_en(discount, true));
		$('span#total_return_tax').text(__currency_trans_from_en(total_tax, true));
		$('span#net_return').text(__currency_trans_from_en(net_return_inc_tax, true));
	}

	
	function add_pos_product_row(qty, variation_id, location_id){
		$.ajax({
			method: 'GET',
			url: '/sells/pos/get_product_row_temp/'+variation_id+'/' + location_id+ '/'+qty,
			data: {
				product_row: $('input#product_row_count').val(),
				customer_id: customer_id,
				is_direct_sell: is_direct_sell,
				price_group: price_group,
				purchase_line_id: null
			},
			dataType: 'json',
			success: function(result) {
				if (result.success) {
					$('table#pos_table tbody')
						.append(result.html_content)
						.find('input.purchase_quantity');
					//increment row count
					
					var this_row = $('table#pos_table tbody')
						.find('tr')
						.last();
					pos_each_row(this_row);

					//For initial discount if present
					var line_total = __read_number(this_row.find('input.pos_line_total'));
					this_row.find('span.pos_line_total_text').text(line_total);

					pos_total_row();

					//Check if multipler is present then multiply it when a new row is added.
					if(__getUnitMultiplier(this_row) > 1){
						this_row.find('select.sub_unit').trigger('change');
					}

					if (result.enable_sr_no == '1') {
						var new_row = $('table#pos_table tbody')
							.find('tr')
							.last();
						new_row.find('.add-pos-row-description').trigger('click');
					}

					round_row_to_iraqi_dinnar(this_row);
					__currency_convert_recursively(this_row);

					$('input#search_product')
						.focus()
						.select();

					//Used in restaurant module
					if (result.html_modifier) {
						$('table#pos_table tbody')
							.find('tr')
							.last()
							.find('td:first')
							.append(result.html_modifier);
					}

					//scroll bottom of items list
					$(".pos_product_div").animate({ scrollTop: $('.pos_product_div').prop("scrollHeight")}, 1000);
				} else {
					toastr.error(result.msg);
					$('input#search_product')
						.focus()
						.select();
				}
				}
				

		});
	}
</script>
@endsection