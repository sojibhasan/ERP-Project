@extends('layouts.app')
@section('title', __('purchase.add_purchase_bulk'))

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
	<h1>@lang('purchase.add_purchase_bulk') <i class="fa fa-keyboard-o hover-q text-muted" aria-hidden="true"
			data-container="body" data-toggle="popover" data-placement="bottom"
			data-content="@include('purchase.partials.keyboard_shortcuts_details')" data-html="true"
			data-trigger="hover" data-original-title="" title=""></i></h1>
</section>

<!-- Main content -->
<section class="content">

	<!-- Page level currency setting -->
	<input type="hidden" id="p_code" value="{{$currency_details->code}}">
	<input type="hidden" id="p_symbol" value="{{$currency_details->symbol}}">
	<input type="hidden" id="p_thousand" value="{{$currency_details->thousand_separator}}">
	<input type="hidden" id="p_decimal" value="{{$currency_details->decimal_separator}}">

	@include('layouts.partials.error')

	{!! Form::open(['url' => action('PurchaseController@savePurchaseBulk'), 'method' => 'post', 'id' =>
	'add_purchase_form',
	'files' => true ]) !!}
	@component('components.widget', ['class' => 'box-primary'])
	<div class="row">
		<div class="@if(!empty($default_purchase_status)) col-sm-4 @else col-sm-3 @endif">
			<div class="form-group">
				{!! Form::label('purchase_no', __('purchase.purchase_no').':') !!}
				{!! Form::text('invoice_no', !empty($purchase_no) ? $purchase_no : 1, ['class' => 'form-control',
				'readonly'] ); !!}
			</div>
		</div>
		<div class="@if(!empty($default_purchase_status)) col-sm-4 @else col-sm-3 @endif">
			<div class="form-group">
				{!! Form::label('supplier_id', __('purchase.supplier') . ':*') !!}
				<div class="input-group">
					<span class="input-group-addon">
						<i class="fa fa-user"></i>
					</span>
					{!! Form::select('contact_id', [], !empty($temp_data->contact_id)?$temp_data->contact_id:null,
					['class' => 'form-control', 'placeholder' =>
					__('messages.please_select'), 'required', 'id' => 'supplier_id']); !!}
					<span class="input-group-btn">
						<button type="button" class="btn btn-default bg-white btn-flat add_new_supplier" data-name=""><i
								class="fa fa-plus-circle text-primary fa-lg"></i></button>
					</span>
				</div>
			</div>
		</div>
		<div class="@if(!empty($default_purchase_status)) col-sm-4 @else col-sm-3 @endif">
			<div class="form-group">
				{!! Form::label('transaction_date', __('purchase.purchase_date') . ':*') !!}
				<div class="input-group">
					<span class="input-group-addon">
						<i class="fa fa-calendar"></i>
					</span>
					{!! Form::text('transaction_date',
					@format_datetime(!empty($temp_data->transaction_date)?$temp_data->transaction_date:'now'), ['class'
					=> 'form-control', 'readonly',
					'required']); !!}
				</div>
			</div>
		</div>
		<div class="col-sm-3 @if(!empty($default_purchase_status)) hide @endif">
			<div class="form-group">
				{!! Form::label('status', __('purchase.purchase_status') . ':*') !!}
				@show_tooltip(__('tooltip.order_status'))
				{!! Form::select('status', $orderStatuses,
				!empty($temp_data->status)?$temp_data->status:$default_purchase_status, ['class' => 'form-control
				select2',
				'placeholder' => __('messages.please_select'), 'required']); !!}
			</div>
		</div>

		<div class="clearfix"></div>

		@if(count($business_locations) == 1)
		@php
		$default_location = current(array_keys($business_locations->toArray()));
		$search_disable = false;
		@endphp
		@else
		@php $default_location = null;
		$search_disable = true;
		@endphp
		@endif
		<div class="col-sm-3">
			<div class="form-group">
				{!! Form::label('location_id', __('purchase.business_location').':*') !!}
				@show_tooltip(__('tooltip.purchase_location'))
				{!! Form::select('location_id', $business_locations,
				!empty($temp_data->location_id)?$temp_data->location_id: $default_location, ['class' => 'form-control
				select2 business_location_id', 'placeholder' => __('messages.please_select'), 'required', 'id' =>
				'location_id',
				'data-default_accounts' => '']); !!}
			</div>
		</div>
		<div class="col-sm-3">
			<div class="form-group">
				{!! Form::label('store_id', __('lang_v1.store_id').':*') !!}
				<select name="store_id" id="store_id" class="form-control select2" required>
					<option value="">@lang('messages.please_select')</option>
				</select>
			</div>
		</div>

		<!-- Currency Exchange Rate -->
		<div class="col-sm-3 @if(!$currency_details->purchase_in_diff_currency) hide @endif">
			<div class="form-group">
				{!! Form::label('exchange_rate', __('purchase.p_exchange_rate') . ':*') !!}
				@show_tooltip(__('tooltip.currency_exchange_factor'))
				<div class="input-group">
					<span class="input-group-addon">
						<i class="fa fa-info"></i>
					</span>
					{!! Form::number('exchange_rate',
					!empty($temp_data->exchange_rate)?$temp_data->exchange_rate:$currency_details->p_exchange_rate,
					['class' => 'form-control',
					'required', 'step' => 0.001]); !!}
				</div>
				<span class="help-block text-danger">
					@lang('purchase.diff_purchase_currency_help', ['currency' => $currency_details->name])
				</span>
			</div>
		</div>

		<div class="col-md-3">
			<div class="form-group">
				<div class="multi-input">
					{!! Form::label('pay_term_number', __('contact.pay_term') . ':') !!}
					@show_tooltip(__('tooltip.pay_term'))
					<br />
					{!! Form::number('pay_term_number', !empty($temp_data->pay_term_number)?$temp_data->pay_term_number:
					null, ['class' => 'form-control width-40 pull-left',
					'placeholder' => __('contact.pay_term')]); !!}

					{!! Form::select('pay_term_type',
					['months' => __('lang_v1.months'),
					'days' => __('lang_v1.days')],
					!empty($temp_data->pay_term_type)?$temp_data->pay_term_type: null,
					['class' => 'form-control width-60 pull-left','placeholder' => __('messages.please_select'), 'id' =>
					'pay_term_type']); !!}
				</div>
			</div>
		</div>

		<div class="col-sm-3">
			<div class="form-group">
				{!! Form::label('document', __('purchase.attach_document') . ':') !!}
				{!! Form::file('document', ['id' => 'upload_document']); !!}
				<p class="help-block">@lang('purchase.max_file_size', ['size' =>
					(config('constants.document_size_limit') / 1000000)])</p>
			</div>
		</div>
	</div>
	@endcomponent

	@component('components.widget', ['class' => 'box-primary'])
	<div class="row">
		<div class="col-sm-8 col-sm-offset-2">
			<div class="form-group">
				<div class="input-group">
					<span class="input-group-addon">
						<i class="fa fa-search"></i>
					</span>
					{!! Form::text('search_product', null, ['class' => 'form-control mousetrap', 'id' =>
					'search_product', 'placeholder' => __('lang_v1.search_product_placeholder'), 'disabled' =>
					$search_disable]); !!}
				</div>
			</div>
		</div>
		<div class="col-sm-2">
			<div class="form-group">
				<button tabindex="-1" type="button" class="btn btn-link btn-modal"
					data-href="{{action('ProductController@quickAdd')}}" data-container=".quick_add_product_modal"><i
						class="fa fa-plus"></i> @lang( 'product.add_new_product' ) </button>
			</div>
		</div>
	</div>
	@php
	$hide_tax = '';
	if( session()->get('business.enable_inline_tax') == 0){
	$hide_tax = 'hide';
	}
	$business_id = request()->session()->get('user.business_id');
	$enable_free_qty = App\Business::where('id', $business_id)->select('enable_free_qty')->first()->enable_free_qty;
	@endphp
	<div class="row">
		<div class="col-sm-12">
			<div class="table-responsive">
				<table class="table table-condensed table-bordered table-th-green text-center table-striped"
					id="purchase_entry_table">
					<thead>
						<tr>
							<th>#</th>
							<th>@lang( 'product.product_name' )</th>
							<th>@lang( 'product.units' )</th>
							<th>@lang( 'purchase.purchase_quantity' )</th>
							@if ($enable_free_qty)
							<th>@lang( 'purchase.free_qty' )</th>
							@endif
							<th>@lang( 'purchase.available_qty' )</th>
							<th>@lang( 'lang_v1.unit_cost_before_discount' )</th>
							<th>@lang( 'lang_v1.discount_percent' )</th>
							<th>@lang( 'purchase.unit_cost_before_tax' )</th>
							<th class="{{$hide_tax}}">@lang( 'purchase.subtotal_before_tax' )</th>
							<th class="{{$hide_tax}}">@lang( 'purchase.product_tax' )</th>
							<th class="{{$hide_tax}}">@lang( 'purchase.net_cost' )</th>
							<th>@lang( 'purchase.line_total' )</th>
							<th class="hide @if(!session('business.enable_editing_product_from_purchase')) hide @endif">
								@lang( 'lang_v1.profit_margin' )
							</th>
							<th class="hide">
								@lang( 'purchase.unit_selling_price' )
								<small>(@lang('product.inc_of_tax'))</small>
							</th>
							@if(session('business.enable_lot_number'))
							<th>
								@lang('lang_v1.lot_number')
							</th>
							@endif
							@if(session('business.enable_product_expiry'))
							<th>
								@lang('product.mfg_date') / @lang('product.exp_date')
							</th>
							@endif
							<th>@lang('purchase.invoice_no')</th>
							<th>@lang('purchase.multiple_payment')</th>
							<th>@lang('purchase.payment_amount')</th>
							<th><i class="fa fa-trash" aria-hidden="true"></i></th>
						</tr>
					</thead>
					<tbody id="purchaseBody"></tbody>
				</table>
			</div>
			<hr />

			<div class="col-md-12" style="font-size: 18px;">
				<table class="pull-right col-md-12">
					<tr class="hide">
						<th class="col-md-6 text-right">@lang( 'purchase.total_before_tax' ):</th>
						<td class="col-md-5 text-left">
							<span id="total_st_before_tax" class="display_currency"></span>
							<input type="hidden" id="st_before_tax_input" value=0>
						</td>
					</tr>
					<tr>
						<th class="col-md-6 text-right">@lang( 'purchase.total_amount' ):</th>
						<td class="col-md-5 text-left">
							<span id="total_subtotal" class="display_currency"></span>
							<!-- This is total before purchase tax-->
							<input type="hidden" id="total_subtotal_input" value=0 name="total_before_tax">
						</td>
					</tr>
					<tr class="text-red">
						<th class="col-md-6 text-right">@lang( 'purchase.total_balance_due' ):</th>
						<td class="col-md-5 text-left">
							<span id="payment_due" class="display_currency"></span>
							<!-- This is total before purchase tax-->
						</td>
					</tr>
				</table>
			</div>

			<input type="hidden" id="row_count" value="0">
		</div>
	</div>
	@endcomponent


	@component('components.widget', ['class' => 'box-primary hide'])
	<div class="row">
		<div class="col-sm-12">
			<table class="table">
				<tr>
					<td class="col-md-3">
						<div class="form-group">
							{!! Form::label('discount_type', __( 'purchase.discount_type' ) . ':') !!}
							{!! Form::select('discount_type', [ '' => __('lang_v1.none'), 'fixed' => __( 'lang_v1.fixed'
							), 'percentage' => __( 'lang_v1.percentage' )],
							!empty($temp_data->discount_type)?$temp_data->discount_type:'', ['class' => 'form-control
							select2']);
							!!}
						</div>
					</td>
					<td class="col-md-3">
						<div class="form-group">
							{!! Form::label('discount_amount', __( 'purchase.discount_amount' ) . ':') !!}
							{!! Form::text('discount_amount',
							!empty($temp_data->discount_amount)?$temp_data->discount_amount:0, ['class' => 'form-control
							input_number', 'required']);
							!!}
						</div>
					</td>
					<td class="col-md-3">
						&nbsp;
					</td>
					<td class="col-md-3">
						<b>@lang( 'purchase.discount' ):</b>(-)
						<span id="discount_calculated_amount" class="display_currency">0</span>
					</td>
				</tr>
				<tr>
					<td>
						<div class="form-group">
							{!! Form::label('tax_id', __('purchase.purchase_tax') . ':') !!}
							<select name="tax_id" id="tax_id" class="form-control select2"
								placeholder="'Please Select'">
								<option value="" data-tax_amount="0" data-tax_type="fixed" selected>
									@lang('lang_v1.none')</option>
								@foreach($taxes as $tax)
								<option value="{{ $tax->id }}" data-tax_amount="{{ $tax->amount }}"
									data-tax_type="{{ $tax->calculation_type }}">{{ $tax->name }}</option>
								@endforeach
							</select>
							{!! Form::hidden('tax_amount',!empty($temp_data->tax_amount)?$temp_data->tax_amount: 0,
							['id' => 'tax_amount']); !!}
						</div>
					</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>
						<b>@lang( 'purchase.purchase_tax' ):</b>(+)
						<span id="tax_calculated_amount" class="display_currency">0</span>
					</td>
				</tr>

				<tr>
					<td>
						<div class="form-group">
							{!! Form::label('shipping_details', __( 'purchase.shipping_details' ) . ':') !!}
							{!! Form::text('shipping_details',
							!empty($temp_data->shipping_details)?$temp_data->shipping_details:null, ['class' =>
							'form-control']); !!}
						</div>
					</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>
						<div class="form-group">
							{!! Form::label('shipping_charges','(+) ' . __( 'purchase.additional_shipping_charges' ) .
							':') !!}
							{!! Form::text('shipping_charges',
							!empty($temp_data->shipping_charges)?$temp_data->shipping_charges:0, ['class' =>
							'form-control input_number', 'required']);
							!!}
						</div>
					</td>
				</tr>

				<tr>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>
						{!! Form::hidden('final_total', 0 , ['id' => 'grand_total_hidden']); !!}
						<b>@lang('purchase.purchase_total'): </b><span id="grand_total" class="display_currency"
							data-currency_symbol='true'>0</span>
					</td>
				</tr>
				<tr>
					<td colspan="4">
						<div class="form-group">
							{!! Form::label('additional_notes',__('purchase.additional_notes')) !!}
							{!!
							Form::textarea('additional_notes',!empty($temp_data->additional_notes)?$temp_data->additional_notes:
							null, ['class' => 'form-control', 'rows' => 3]); !!}
						</div>
					</td>
				</tr>

			</table>
		</div>
	</div>
	@endcomponent

	@component('components.widget', ['class' => 'box-primary unload_div hide', 'title' => __('purchase.unload_tanks')])
	<div class="box-body unload_tank">
	</div>
	@endcomponent
	<div class="row">
		<div class="col-sm-12">
			<button type="button" id="submit_purchase_form"
				class="btn btn-primary pull-right btn-flat">@lang('messages.save')</button>
		</div>
	</div>

	{!! Form::close() !!}
</section>
<!-- quick product modal -->
<div class="modal fade quick_add_product_modal" tabindex="-1" role="dialog" aria-labelledby="modalTitle"></div>
<div class="modal fade contact_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
	@include('contact.create', ['quick_add' => true])
</div>
<!-- /.content -->
@endsection

@section('javascript')
<script src="{{ asset('js/purchase-bulk.js?v=' . $asset_v) }}"></script>
<script src="{{ asset('js/product.js?v=' . $asset_v) }}"></script>
@include('purchase.partials.keyboard_shortcuts')

@if(!empty($temp_data->purchases))
@php $i = 0; @endphp
@foreach($temp_data->purchases as $purchase)
<script>
	$(document).ready(function(){
		 var product_id = {{$purchase->product_id}};
		 var variation_id = {{$purchase->variation_id}};
		 var quantity = {{str_replace(',', '',$purchase->quantity)}};
		 base_url = '{{URL::to('/')}}';

		 if (product_id) {
			var row_count = {{$i}};
			$.ajax({
				method: 'POST',
				url: base_url+'/purchases/get_purchase_entry_row_temp',
				dataType: 'html',
				data: { product_id: product_id, row_count: row_count, variation_id: variation_id, quantity: quantity },
				success: function(result) {
					$(result)
						.find('.purchase_quantity')
						.each(function() {
							row = $(this).closest('tr');

							$('#purchase_entry_table tbody').append(
								update_purchase_entry_row_values(row)
							);
							update_row_price_for_exchange_rate(row);

							update_inline_profit_percentage(row);

							update_table_total();
							update_grand_total();
							update_table_sr_number();

							//Check if multipler is present then multiply it when a new row is added.
							if(__getUnitMultiplier(row) > 1){
								row.find('select.sub_unit').trigger('change');
							}
						});
					if ($(result).find('.purchase_quantity').length) {
						$('#row_count').val(
							$(result).find('.purchase_quantity').length + parseInt(row_count)
						);
					}
				},
			});
		}
	})
</script>
@php $i++; @endphp
@endforeach
@endif

<script>
	var body = document.getElementsByTagName("body")[0];
        body.className += " sidebar-collapse";


	function update_row_price_for_exchange_rate(row) {
    var exchange_rate = $('input#exchange_rate').val();

    if (exchange_rate == 1) {
        return true;
    }

    var purchase_unit_cost_without_discount =
        __read_number(row.find('.purchase_unit_cost_without_discount'), true) / exchange_rate;
    __write_number(
        row.find('.purchase_unit_cost_without_discount'),
        purchase_unit_cost_without_discount,
        true
    );

    var purchase_unit_cost = __read_number(row.find('.purchase_unit_cost'), true) / exchange_rate;
    __write_number(row.find('.purchase_unit_cost'), purchase_unit_cost, true);

    var row_subtotal_before_tax_hidden =
        __read_number(row.find('.row_subtotal_before_tax_hidden'), true) / exchange_rate;
    row.find('.row_subtotal_before_tax').text(
        __currency_trans_from_en(row_subtotal_before_tax_hidden, false, true)
    );
    __write_number(
        row.find('input.row_subtotal_before_tax_hidden'),
        row_subtotal_before_tax_hidden,
        true
    );

    var purchase_product_unit_tax =
        __read_number(row.find('.purchase_product_unit_tax'), true) / exchange_rate;
    __write_number(row.find('input.purchase_product_unit_tax'), purchase_product_unit_tax, true);
    row.find('.purchase_product_unit_tax_text').text(
        __currency_trans_from_en(purchase_product_unit_tax, false, true)
    );

    var purchase_unit_cost_after_tax =
        __read_number(row.find('.purchase_unit_cost_after_tax'), true) / exchange_rate;
    __write_number(
        row.find('input.purchase_unit_cost_after_tax'),
        purchase_unit_cost_after_tax,
        true
    );

    var row_subtotal_after_tax_hidden =
        __read_number(row.find('.row_subtotal_after_tax_hidden'), true) / exchange_rate;
    __write_number(
        row.find('input.row_subtotal_after_tax_hidden'),
        row_subtotal_after_tax_hidden,
        true
    );
    row.find('.row_subtotal_after_tax').text(
        __currency_trans_from_en(row_subtotal_after_tax_hidden, false, true)
    );
}

function update_inline_profit_percentage(row) {
    //Update Profit percentage
    var default_sell_price = __read_number(row.find('input.default_sell_price'), true);
    var exchange_rate = $('input#exchange_rate').val();
    default_sell_price_in_base_currency = default_sell_price / parseFloat(exchange_rate);

    var purchase_after_tax = __read_number(row.find('input.purchase_unit_cost_after_tax'), true);
    var profit_percent = __get_rate(purchase_after_tax, default_sell_price_in_base_currency);
    __write_number(row.find('input.profit_percent'), profit_percent, true);
}


function update_table_total() {
    var total_quantity = 0;
    var total_st_before_tax = 0;
    var total_subtotal = 0;

    $('#purchase_entry_table tbody')
        .find('tr')
        .each(function() {
            total_quantity += __read_number($(this).find('.purchase_quantity'), true);
            total_st_before_tax += __read_number(
                $(this).find('.row_subtotal_before_tax_hidden'),
                true
            );
            total_subtotal += __read_number($(this).find('.row_subtotal_after_tax_hidden'), true);
        });

    $('#total_quantity').text(__number_f(total_quantity, true));
    $('#total_st_before_tax').text(__currency_trans_from_en(total_st_before_tax, true, true));
    __write_number($('input#st_before_tax_input'), total_st_before_tax, true);

    $('#total_subtotal').text(__currency_trans_from_en(total_subtotal, true, true));
    __write_number($('input#total_subtotal_input'), total_subtotal, true);
}
function update_grand_total() {
    var st_before_tax = __read_number($('input#st_before_tax_input'), true);
    var total_subtotal = __read_number($('input#total_subtotal_input'), true);

    //Calculate Discount
    var discount_type = $('select#discount_type').val();
    var discount_amount = __read_number($('input#discount_amount'), true);
    var discount = __calculate_amount(discount_type, discount_amount, total_subtotal);
    $('#discount_calculated_amount').text(__currency_trans_from_en(discount, true, true));

    //Calculate Tax
    var tax_rate = parseFloat($('option:selected', $('#tax_id')).data('tax_amount'));
    var tax = __calculate_amount('percentage', tax_rate, total_subtotal - discount);
    __write_number($('input#tax_amount'), tax);
    $('#tax_calculated_amount').text(__currency_trans_from_en(tax, true, true));

    //Calculate shipping
    var shipping_charges = __read_number($('input#shipping_charges'), true);

    //Calculate Final total
    grand_total = total_subtotal - discount + tax + shipping_charges;

    __write_number($('input#grand_total_hidden'), grand_total, true);

    var payment = __read_number($('input.payment-amount'), true);

    var due = grand_total - payment;
    // __write_number($('input.payment-amount'), grand_total, true);

    $('#grand_total').text(__currency_trans_from_en(grand_total, true, true));


    $('#payment_due').text(__currency_trans_from_en(due, true, true));
	
}



function update_table_sr_number() {
    var sr_number = 1;
    $('table#purchase_entry_table tbody')
        .find('.sr_number')
        .each(function() {
            $(this).text(sr_number);
            sr_number++;
        });
	$(function(){
		$("#purchaseBody").each(function(elem,index){
		var arr = $.makeArray($("tr",this).detach());
		arr.reverse();
			$(this).append(arr);
		});
	});
}	


$('.business_location_id').change(function(){
		let check_store_not = null;
		$.ajax({
			method: 'get',
			url: '/stock-transfer/get_transfer_store_id/'+$('#location_id').val(),
			data: { check_store_not: check_store_not},
			success: function(result) {
				
				$('#store_id').empty();
				$('#store_id').append(`<option value="">Please Select</option>`);
				$.each(result, function(i, location) {
					$('#store_id').append(`<option value= "`+location.id+`">`+location.name+`</option>`);
				});
			},
		});
		$.ajax({
			method: 'get',
			url: '/purchases/get-payment-method-by-location-id/'+$(this).val(),
			data: {  },
			success: function(result) {
				$('.payment_types_dropdown').empty().append(result.html);
			},
		});
		getInvoice();


	});
	function getInvoice() {
		$.ajax({
			method: 'get',
			url: '{{action("SellController@getInvoiveNo")}}',
			data: { location_id: $('#location_id').val() },
			success: function(result) {
				$('#location_id').data('default_accounts', result.default_accounts);
				
				$('.payment_types_dropdown').val('cash');
				$('.payment_types_dropdown').trigger('change');
			},
		});
	}
	$(document).ready(function(){
		setTimeout(() => {
			getInvoice();

		}, 2000);
	});


$('#cheque_date_0').datepicker('setDate' , new Date());

$(document).on('change', '.payment_types_dropdown', function(e) {
    var default_accounts = JSON.parse($('#location_id').data('default_accounts'));
    var payment_type = $(this).val();
    if (payment_type) {
        var default_account = default_accounts && default_accounts[payment_type]['account'] ? default_accounts[payment_type]['account'] : '';
        var payment_row = $(this).closest('.payment_row');
        var row_index = payment_row.find('.payment_row_index').val();

        var account_dropdown = payment_row.find('select#account_' + row_index);
        if (account_dropdown.length && default_accounts) {
            account_dropdown.val(default_account);
            account_dropdown.change();
        }
	}
	
	if(payment_type == 'direct_bank_deposit' || payment_type == 'bank_transfer'){
		$('.account_module').removeClass('hide');
	}else{
		$('.account_module').addClass('hide');
	}
});
setTimeout(() => {
		let check_store_not = null;
		$.ajax({
			method: 'get',
			url: '/stock-transfer/get_transfer_store_id/{{array_keys($business_locations->toArray())[0]}}',
			data: { check_store_not: check_store_not},
			success: function(result) {
				
				$('#store_id').empty();
				$('#store_id').append(`<option value="">Please Select</option>`);
				$.each(result, function(i, location) {
					$('#store_id').append(`<option value= "`+location.id+`">`+location.name+`</option>`);
				});
			},
		});
}, 1000);

@if(auth()->user()->can('unfinished_form.purchase'))
	setInterval(function(){ 
		$.ajax({
                method: 'POST',
                url: '{{action("TempController@saveAddPurchaseTemp")}}',
                dataType: 'json',
                data: $('#add_purchase_form').serialize(),
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
			window.location.href = "{{action('TempController@clearData', ['type' => 'pos_create_data'])}}";
		} 
	});
@endif
@endif

@if($is_petro_enable)
	

@endif
</script>



@endsection