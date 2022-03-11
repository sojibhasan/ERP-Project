@extends('layouts.ecom_customer')

@section('title', 'POS')

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
@php
$enable_line_discount = !empty($pos_settings['enable_line_discount'])? 1: 0;
@endphp
<section class="content no-print">
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
	@if(!empty($pos_settings['allow_overselling']))
	<input type="hidden" id="is_overselling_allowed">
	@endif
	@if(session('business.enable_rp') == 1)
	<input type="hidden" id="reward_point_enabled">
	@endif
	<div class="row">
		<div
			class="left_div @if(!empty($pos_settings['hide_product_suggestion']) && !empty($pos_settings['hide_recent_trans'])) col-md-10 col-md-offset-1 @else col-md-7 @endif col-sm-12">
			@component('components.widget', ['class' => 'box-success'])
			@slot('header')
			<div class="col-sm-6">
				<p class="text-right  pull-left"><strong>@lang('sale.location'):</strong> {{$default_location->name}}
				</p>
			</div>
			<input type="hidden" id="item_addition_method" value="{{$business_details->item_addition_method}}">

			<input type="hidden" id="service_addition_method" value="{{$business_details->service_addition_method}}">
			@endslot
			{!! Form::open(['url' => action('Ecom\EcomCustomerOrderController@store'), 'method' => 'post', 'id' => 'add_pos_sell_form'
			]) !!}
            {!! Form::hidden('business_id', $business_id, []) !!}
			{!! Form::hidden('location_id', $default_location->id, ['id' => 'location_id', 'data-receipt_printer_type'
			=> !empty($default_location->receipt_printer_type) ? $default_location->receipt_printer_type : 'browser',
			'data-default_accounts' => $default_location->default_payment_accounts]); !!}
			<style>
				.select2-drop-active {
					margin-top: -25px;
				}
			</style>
			<!-- /.box-header -->
			<div class="box-body">
				<div class="row">
					@if(!empty($pos_settings['enable_transaction_date']))
					<div class="col-md-4 col-sm-6">
						<div class="form-group">
							{!! Form::label('transaction_date', __('sale.sale_date') . ':*') !!}
							<div class="input-group">
								<span class="input-group-addon">
									<i class="fa fa-calendar"></i>
								</span>
								{!! Form::text('transaction_date',
								!empty($temp_data->transaction_date)?$temp_data->transaction_date:$default_datetime,
								['class' => 'form-control',
								'readonly', 'required']); !!}
							</div>
						</div>
					</div>
					@endif
					
	
		@if(config('constants.enable_sell_in_diff_currency') == true)
		<div class="col-md-4 col-sm-6">
			<div class="form-group">
				<div class="input-group">
					<span class="input-group-addon">
					   
						<i class="fa fa-exchange"></i>
					</span>
					{!! Form::text('exchange_rate',
					!empty($temp_data->exchange_rate)?$temp_data->exchange_rate:config('constants.currency_exchange_rate'),
					['class' =>
					'form-control input-sm input_number', 'placeholder' =>
					__('lang_v1.currency_exchange_rate'), 'id' => 'exchange_rate']); !!}
				</div>
			</div>
		</div>
		@endif
		@if(!empty($price_groups) && count($price_groups) > 1)
		<div class="col-md-4 col-sm-6">
			<div class="form-group">
				<div class="input-group">
					<span class="input-group-addon">
						<i class="fa fa-money"></i>
					</span>
					@php
					reset($price_groups);
					$selected_price_group = !empty($default_price_group_id) &&
					array_key_exists($default_price_group_id, $price_groups) ? $default_price_group_id :
					null;
					@endphp
					{!!
					Form::hidden('hidden_price_group',!empty($temp_data->hidden_price_group)?$temp_data->hidden_price_group:
					key($price_groups), ['id' =>
					'hidden_price_group']) !!}
					{!! Form::select('price_group',
					$price_groups,!empty($temp_data->price_group)?$temp_data->price_group:
					$selected_price_group, ['class' =>
					'form-control select2', 'id' => 'price_group', 'style' => 'width: 100%;']); !!}
					<span class="input-group-addon">
						@show_tooltip(__('lang_v1.price_group_help_text'))
					</span>
				</div>
			</div>
		</div>
		@else
		@php
		reset($price_groups);
		@endphp
		{!! Form::hidden('price_group', !empty($temp_data->price_group)?$temp_data->price_group:
		key($price_groups), ['id' => 'price_group']) !!}
		@endif
		@if(!empty($default_price_group_id))
		{!!
		Form::hidden('default_price_group',!empty($temp_data->default_price_group)?$temp_data->default_price_group:
		$default_price_group_id, ['id' => 'default_price_group'])
		!!}
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
		<div class="">
			<div class="form-group">
				<div class="input-group">
					<input type="hidden" id="default_customer_id"
						value="{{!empty($temp_data->default_customer_id)?$temp_data->default_customer_id: $walk_in_customer['id']}}">
					<input type="hidden" id="default_customer_name"
						value="{{ !empty($temp_data->default_customer_name)?$temp_data->default_customer_name:$walk_in_customer['name']}}">
					{!! Form::hidden('contact_id',
					$contact_id, ['class' =>
					'form-control ', 'id' => 'customer_ids','style' => 'width: 100%;']); !!}
					
				</div>
			</div>
		</div>
		<input type="hidden" name="pay_term_number" id="pay_term_number"
			value="{{!empty($temp_data->pay_term_number)?$temp_data->pay_term_number:$walk_in_customer['pay_term_number']}}">
		<input type="hidden" name="pay_term_type" id="pay_term_type"
			value="{{!empty($temp_data->pay_term_type)?$temp_data->pay_term_type:$walk_in_customer['pay_term_type']}}">

		@if(!empty($commission_agent))
		<div class="col-sm-4 hide">
			<div class="form-group">
				{!! Form::select('commission_agent',
				[], !empty($temp_data->commission_agent)?$temp_data->commission_agent:null,
				['class' => 'form-control select2', 'placeholder' =>
				__('lang_v1.commission_agent')]); !!}
			</div>
		</div>
		@endif

		<div class="col-md-12">
			<div class="form-group">
				<div class="input-group">
					<div class="input-group-btn">
						<button type="button" class="btn btn-default bg-white btn-flat" data-toggle="modal"
							data-target="#configure_search_modal" title="{{__('lang_v1.configure_product_search')}}"><i
								class="fa fa-barcode"></i></button>
					</div>
					{!! Form::text('search_product', null, ['class' => 'form-control mousetrap', 'id' =>
					'search_product', 'placeholder' => __('lang_v1.search_product_placeholder'),
					'disabled' => is_null($default_location)? true : false,
					'autofocus' => is_null($default_location)? false : true,
					]); !!}
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
			<input type="hidden" name="sell_price_tax" id="sell_price_tax"
				value="{{!empty($temp_data->sell_price_tax)?$temp_data->sell_price_tax:$business_details->sell_price_tax}}">

			<!-- Keeps count of product rows -->
			<input type="hidden" id="product_row_count"
				value="{{!empty($temp_data->product_row_count)?$temp_data->product_row_count:0}}">
			@php
			$hide_tax = '';
			if( session()->get('business.enable_inline_tax') == 0){
			$hide_tax = 'hide';
			}
			@endphp
			<table class="table table-condensed table-bordered table-striped table-responsive" id="pos_table">
				<thead>
					<tr>
						<th
							class="tex-center @if(!empty($pos_settings['inline_service_staff'])) col-md-3 @else col-md-4 @endif">
							@lang('sale.product') @show_tooltip(__('lang_v1.tooltip_sell_product_column'))
						</th>
						<th class="text-center col-md-3">
							@lang('sale.qty')
						</th>
						@if(!empty($pos_settings['inline_service_staff']))
						<th class="text-center col-md-2">
							@lang('restaurant.service_staff')
						</th>
						@endif
						<th class="text-center col-md-2 {{$hide_tax}}">
							@lang('sale.price_inc_tax')
						</th>
						@if($enable_line_discount)
						<th class="text-center col-md-2">
							@lang('sale.discount')
						</th>
						@endif
						<th class="text-center col-md-2">
								@lang('sale.discount')
						</th>
						<th class="text-center"><i class="fa fa-close" aria-hidden="true"></i></th>
					</tr>
				</thead>
				<tbody></tbody>
			</table>
		</div>
	</div>
    
    

    <div class="row">
        <div class="col-sm-12">
            <div class="panel panel-default">
                <div class="panel-body bg-gray disabled" style="margin-bottom: 0px !important">
                    <div class="col-sm-3 col-sm-offset-6 col-xs-6 d-inline-table" style="margin-bottom: 10px;">
                        <b>@lang('sale.item'):</b>&nbsp;
                        <span class="total_quantity">0</span>
                    </div>
                    <div class="col-sm-3 col-xs-6 d-inline-table" style="margin-bottom: 10px;">
                        <b>@lang('sale.total'):</b> &nbsp;
                        <span class="price_total">0</span>
                    </div>
                    <table class="table table-condensed" 
                        style="margin-bottom: 0px !important">
                        <tbody>
                        @php
                            $col = in_array('types_of_service', $enabled_modules) ? 'col-sm-2' : 'col-sm-3';
                        @endphp
                        <tr>
                            <td>
                                <div class="{{$col}} col-xs-6 d-inline-table">
                                    @php
                                        $is_discount_enabled = $pos_settings['disable_discount'] != 1 ? true : false;
                                        $is_rp_enabled = session('business.enable_rp') == 1 ? true : false;
                                    @endphp
                                    <span class="hide">
    
                                    <b>
                                    @if($is_discount_enabled)
                                        @lang('sale.discount')
                                        @show_tooltip(__('tooltip.sale_discount'))
                                    @endif
                                    @if($is_rp_enabled)
                                        {{session('business.rp_name')}}
                                    @endif
                                    (-):</b> 
                                    <br/>
                                    <i class="fa fa-pencil-square-o cursor-pointer" id="pos-edit-discount" title="@lang('sale.edit_discount')" aria-hidden="true" data-toggle="modal" data-target="#posEditDiscountModal"></i>
                                    <span id="total_discount">0</span>
                                    <input type="hidden" name="discount_type" id="discount_type" value="@if(empty($edit)){{'percentage'}}@else{{$transaction->discount_type}}@endif" data-default="percentage">
    
                                    <input type="hidden" name="discount_amount" id="discount_amount" value="@if(empty($edit)) {{@num_format($business_details->default_sales_discount)}} @else {{@num_format($transaction->discount_amount)}} @endif" data-default="{{$business_details->default_sales_discount}}">
    
                                    <input type="hidden" name="rp_redeemed" id="rp_redeemed" value="@if(empty($edit)){{'0'}}@else{{$transaction->rp_redeemed}}@endif">
    
                                    <input type="hidden" name="rp_redeemed_amount" id="rp_redeemed_amount" value="@if(empty($edit)){{'0'}}@else {{$transaction->rp_redeemed_amount}} @endif">
    
                                    </span>
                                </div>
    
                                <div class="{{$col}} col-xs-6 d-inline-table">
    
                                    <span class="hide">
    
                                    <b>@lang('sale.order_tax')(+): @show_tooltip(__('tooltip.sale_tax'))</b>
                                    <br/>
                                    <i class="fa fa-pencil-square-o cursor-pointer" title="@lang('sale.edit_order_tax')" aria-hidden="true" data-toggle="modal" data-target="#posEditOrderTaxModal" id="pos-edit-tax" ></i> 
                                    <span id="order_tax">
                                        @if(empty($edit))
                                            0
                                        @else
                                            {{$transaction->tax_amount}}
                                        @endif
                                    </span>
    
                                    <input type="hidden" name="tax_rate_id" 
                                        id="tax_rate_id" 
                                        value="@if(empty($edit)) {{$business_details->default_sales_tax}} @else {{$transaction->tax_id}} @endif" 
                                        data-default="{{$business_details->default_sales_tax}}">
    
                                    <input type="hidden" name="tax_calculation_amount" id="tax_calculation_amount" 
                                        value="@if(empty($edit)) {{@num_format($business_details->tax_calculation_amount)}} @else {{@num_format(optional($transaction->tax)->amount)}} @endif" data-default="{{$business_details->tax_calculation_amount}}">
    
                                    </span>
                                </div>
                                
                                <!-- shipping -->
                                <div class="{{$col}} col-xs-6 d-inline-table">
    
                                    <span class=" hide">
    
                                    <b>@lang('sale.shipping')(+): @show_tooltip(__('tooltip.shipping'))</b> 
                                    <br/>
                                    <i class="fa fa-pencil-square-o cursor-pointer"  title="@lang('sale.shipping')" aria-hidden="true" data-toggle="modal" data-target="#posShippingModal"></i>
                                    <span id="shipping_charges_amount">0</span>
                                    <input type="hidden" name="shipping_details" id="shipping_details" value="@if(empty($edit)){{""}}@else{{$transaction->shipping_details}}@endif" data-default="">
    
                                    <input type="hidden" name="shipping_address" id="shipping_address" value="@if(empty($edit)){{""}}@else{{$transaction->shipping_address}}@endif">
    
                                    <input type="hidden" name="shipping_status" id="shipping_status" value="@if(empty($edit)){{""}}@else{{$transaction->shipping_status}}@endif">
    
                                    <input type="hidden" name="delivered_to" id="delivered_to" value="@if(empty($edit)){{""}}@else{{$transaction->delivered_to}}@endif">
    
                                    <input type="hidden" name="shipping_charges" id="shipping_charges" value="@if(empty($edit)){{@num_format(0.00)}} @else{{@num_format($transaction->shipping_charges)}} @endif" data-default="0.00">
    
                                    </span>
                                </div>
                            
                                <div class="col-sm-3 col-xs-12 d-inline-table">
                                    <b>@lang('sale.total_payable'):</b>
                                    <br/>
                                    <input type="hidden" name="final_total" 
                                        id="final_total_input" value=0>
                                    <span id="total_payable" class="text-success lead text-bold">0</span>
                                   
                                        <button type="button" class="btn btn-danger btn-flat hide btn-xs pull-right" id="pos-delete">@lang('messages.delete')</button>
                               
                                </div>
                            </td>
                        </tr>
                        <input type="hidden" name="status" value="quotation">
                        <tr>
                            <td>
                                <div class="col-sm-12 col-xs-12 col-2px-padding">
                                    <div class="row">
                                        <div class="col-md-6"></div>
                                        <div class="col-md-3">
                                            <a href="{{action('Ecom\EcomCustomerOrderController@index')}}" class="btn btn-danger btn-block btn-flat">@lang('customer.cancel')</a>
                                        </div>
                                        <div class="col-md-3">
                                            <button type="submit" 
                                                class="btn btn-success btn-block btn-flat" 
                                                id="pos-quotation">@lang('customer.submit')</button>
                                        </div>
                                        </div>
                                    </div>
                                <div class="div-overlay pos-processing"></div>
                            </td>
                        </tr>
    
                        </tbody>
                    </table>
    
                    <!-- Button to perform various actions -->
                 
                </div>
            </div>
        </div>
    </div>
    
    @include('sale_pos.partials.edit_shipping_modal')




	
	</div>
	<!--  temp cat id and brand id if there is any temp data  -->
	<input type="hidden" id="cat_id_suggestion" name="cat_id_suggestion"
		value="{{!empty($temp_data->cat_id_suggestion)?$temp_data->cat_id_suggestion:0}}">
	<input type="hidden" id="brand_id_suggestion" name="brand_id_suggestion"
		value="{{!empty($temp_data->brand_id_suggestion)?$temp_data->brand_id_suggestion:0}}">
	<input type="hidden" name="is_pos" value="1" id="is_pos">
	<input type="hidden" name="is_duplicate" value="0" id="is_duplicate">
	<!-- /.box-body -->
	{!! Form::close() !!}
	@endcomponent
	</div>

	<div class="col-md-5 col-sm-12 right_div">
		@include('sale_pos.partials.right_div')
	</div>
	</div>
</section>

<!-- This will be printed -->
<section class="invoice print_section" id="receipt_section">
</section>

<!-- /.content -->
<div class="modal fade register_details_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
</div>
<div class="modal fade close_register_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
</div>
<!-- quick product modal -->
<div class="modal fade quick_add_product_modal" tabindex="-1" role="dialog" aria-labelledby="modalTitle"></div>
<div class="modal fade patient_prescriptions_modal" role="dialog" aria-labelledby="modalTitle"></div>

@include('sale_pos.partials.configure_search_modal')

@stop

@section('javascript')
<script src="{{ asset('js/pos.js?v=' . $asset_v) }}"></script>
<script src="{{ asset('js/printer.js?v=' . $asset_v) }}"></script>
<script src="{{ asset('js/product.js?v=' . $asset_v) }}"></script>
<script src="{{ asset('js/opening_stock.js?v=' . $asset_v) }}"></script>
@include('sale_pos.partials.keyboard_shortcuts')

<!-- Call restaurant module if defined -->
@if(in_array('tables' ,$enabled_modules) || in_array('modifiers' ,$enabled_modules) || in_array('service_staff'
,$enabled_modules))
<script src="{{ asset('js/restaurant.js?v=' . $asset_v) }}"></script>
@endif




<script>
	$( document ).ready(function() {
		setTimeout(() => {
			$(".payment_method").val($(".payment_method option:eq(1)").val());
			$(".payment_method").selectmenu().selectmenu("refresh");
			@can('is_service_staff')
			$("#res_waiter_id").val("{{auth()->user()->id}}");
			$("#res_waiter_id").trigger('change.select2'); 
			@endcan
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
	reset_pos_form();
	$('.payment_types_dropdown').val('cash');
	$('.payment_types_dropdown').trigger('change');
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
		

		 @if(!empty($temp_data->products))
		 @php $i = -1; @endphp
		 @foreach($temp_data->products as $product)
		 base_url = '{{URL::to('/')}}';
		 qty = parseInt({{$product->quantity}});
		 $.ajax({
            method: 'GET',
            url: base_url+'/sells/pos/get_product_row_temp/{{$product->variation_id}}/' + location_id+ '/'+qty,
            data: {
                product_row: {{$i}},
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
						.find('input.pos_quantity');
					//increment row count
					$('input#product_row_count').val(parseInt(product_row) + 1);
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
		@php $i++; @endphp
		@endforeach
		 @endif

		 //Update values for each row
function pos_each_row(row_obj) {
    var unit_price = __read_number(row_obj.find('input.pos_unit_price'));

    var discounted_unit_price = calculate_discounted_unit_price(row_obj);
    var tax_rate = row_obj
        .find('select.tax_id')
        .find(':selected')
        .data('rate');

    var unit_price_inc_tax =
        discounted_unit_price + __calculate_amount('percentage', tax_rate, discounted_unit_price);
    __write_number(row_obj.find('input.pos_unit_price_inc_tax'), unit_price_inc_tax);

    var discount = __read_number(row_obj.find('input.row_discount_amount'));

    if (discount > 0) {
        var qty = __read_number(row_obj.find('input.pos_quantity'));
        var line_total = qty * unit_price_inc_tax;
        __write_number(row_obj.find('input.pos_line_total'), line_total);
    }

    //var unit_price_inc_tax = __read_number(row_obj.find('input.pos_unit_price_inc_tax'));

    __write_number(row_obj.find('input.item_tax'), unit_price_inc_tax - discounted_unit_price);
}
function getInvoice(){
	console.log('getInvoice');
	
}
</script>
@endsection