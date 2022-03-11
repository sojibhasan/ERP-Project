@extends('layouts.ecom_customer')

@section('title', __('lang_v1.create_order'))

@section('content')
<!-- Content Header (Page header) -->
<section class="content-header">
	<h1>@lang('lang_v1.create_order')</h1>
</section>
<input type="hidden" id="__code" value="{{$currency->code}}">
<input type="hidden" id="__symbol" value="{{$currency->symbol}}">
<!-- Main content -->
<section class="content no-print">
	@if(!empty($pos_settings['allow_overselling']))
	<input type="hidden" id="is_overselling_allowed">
	@endif
	@if(session('business.enable_rp') == 1)
	<input type="hidden" id="reward_point_enabled">
	@endif
	@if(is_null($default_location))
	<div class="row">
		<div class="col-sm-3">
			<div class="form-group">
				<div class="input-group">
					<span class="input-group-addon">
						<i class="fa fa-map-marker"></i>
					</span>
					{!! Form::select('select_location_id', $business_locations, null, ['class' => 'form-control
					input-sm',
					'placeholder' => __('lang_v1.select_location'),
					'id' => 'select_location_id',
					'required', 'autofocus'], $bl_attributes); !!}
					<span class="input-group-addon">
						@show_tooltip(__('tooltip.sale_location'))
					</span>
				</div>
			</div>
		</div>
	</div>
	@endif
	<input type="hidden" id="item_addition_method" value="{{$business_details->item_addition_method}}">
	<input type="hidden" id="service_addition_method" value="{{$business_details->service_addition_method}}">
	{!! Form::open(['url' => action('Ecom\EcomCustomerOrderController@store'), 'method' => 'post', 'id' => 'add_sell_form' ]) !!}
	<div class="row">
		<div class="col-md-12 col-sm-12">
			{!! Form::hidden('location_id', $default_location, ['id' => 'location_id', 'data-receipt_printer_type' =>
			isset($bl_attributes[$default_location]['data-receipt_printer_type']) ?
			$bl_attributes[$default_location]['data-receipt_printer_type'] : 'browser']); !!}
			@component('components.widget', ['class' => 'box-primary hide'])

			@if(!empty($price_groups))
			@if(count($price_groups) > 1)
			<div class="col-sm-4">
				<div class="form-group">
					<div class="input-group">
						<span class="input-group-addon">
							<i class="fa fa-money"></i>
						</span>
						@php
						reset($price_groups);
						@endphp
						{!! Form::hidden('hidden_price_group', key($price_groups), ['id' => 'hidden_price_group']) !!}
						{!! Form::select('price_group', $price_groups,
						!empty($temp_data->price_group)?$temp_data->price_group:null, ['class' => 'form-control
						select2', 'id' =>
						'price_group']); !!}
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
			{!! Form::hidden('price_group', key($price_groups), ['id' => 'price_group']) !!}
			@endif
			@endif

			{!! Form::hidden('default_price_group',
			!empty($temp_data->default_price_group)?$temp_data->default_price_group:null, ['id' =>
			'default_price_group']) !!}

			@if(in_array('types_of_service', $enabled_modules) && !empty($types_of_service))
			<div class="col-md-4 col-sm-6">
				<div class="form-group">
					<div class="input-group">
						<span class="input-group-addon">
							<i class="fa fa-external-link text-primary service_modal_btn"></i>
						</span>
						{!! Form::select('types_of_service_id', $types_of_service, null, ['class' => 'form-control',
						'id' => 'types_of_service_id', 'style' => 'width: 100%;', 'placeholder' =>
						__('lang_v1.select_types_of_service')]); !!}

						{!! Form::hidden('types_of_service_price_group', null, ['id' => 'types_of_service_price_group'])
						!!}

						<span class="input-group-addon">
							@show_tooltip(__('lang_v1.types_of_service_help'))
						</span>
					</div>
					<small>
						<p class="help-block hide" id="price_group_text">@lang('lang_v1.price_group'): <span></span></p>
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
			<div class="clearfix"></div>
			<div class="col-sm-4 hide">
				<div class="form-group">
					{!! Form::label('contact_id', __('contact.customer') . ':*') !!}
					<div class="input-group">
						<span class="input-group-addon">
							<i class="fa fa-user"></i>
                        </span>
						<input type="hidden" id="default_customer_id" value="">
						<input type="hidden" id="customer_id" value="{{Auth::user()->username}}">
						<input type="hidden" id="default_customer_name" value="">
						{!! Form::select('contact_id',
						[], null , ['class' => 'form-control
						mousetrap', 'id' => 'customer_id', 'placeholder' => 'Enter
						Customer name / phone', 'required', 'readonly']); !!}
					</div>
				</div>
			</div>

			<div class="col-md-3 hide">
				<div class="form-group">
					<div class="multi-input">
						{!! Form::label('pay_term_number', __('contact.pay_term') . ':') !!}
						@show_tooltip(__('tooltip.pay_term'))
						<br />
						{!! Form::number('pay_term_number',
						$walk_in_customer['pay_term_number'], ['class' =>
						'form-control width-40 pull-left', 'placeholder' => __('contact.pay_term')]); !!}

						{!! Form::select('pay_term_type',
						['months' => __('lang_v1.months'),
						'days' => __('lang_v1.days')],
						$walk_in_customer['pay_term_type'],
						['class' => 'form-control width-60 pull-left','placeholder' => __('messages.please_select')]);
						!!}
					</div>
				</div>
			</div>

			<div class="col-sm-4">
				<div class="form-group">
					{!! Form::label('transaction_date', __('sale.sale_date') . ':*') !!}
					<div class="input-group">
						<span class="input-group-addon">
							<i class="fa fa-calendar"></i>
						</span>
						{!! Form::hidden('transaction_date',
						$default_datetime, ['class'
						=> 'form-control', 'readonly',
						'required']); !!}
					</div>
				</div>
			</div>
			<div class="col-sm-4 hide">
				<div class="form-group">
					{!! Form::label('status', __('sale.status') . ':*') !!}
					{!! Form::select('status', ['final' => __('sale.final'), 'draft' => __('sale.draft'), 'quotation' =>
					__('lang_v1.quotation')],'quotation', ['class' =>
					'form-control select2', 'placeholder' =>
					__('messages.please_select'), 'required']); !!}
				</div>
			</div>
			<div class="col-sm-3 hide">
				<div class="form-group">
					{!! Form::label('invoice_scheme_id', __('invoice.invoice_scheme') . ':') !!}
					{!! Form::select('invoice_scheme_id', $invoice_schemes,
					$default_invoice_schemes->id,
					['class' =>
					'form-control select2', 'placeholder' => __('messages.please_select')]); !!}
				</div>
			</div>
			<div class="clearfix"></div>
			<!-- Call restaurant module if defined -->
			@if(in_array('tables' ,$enabled_modules) || in_array('service_staff' ,$enabled_modules))
			<span id="restaurant_module_span">
				<div class="col-md-3"></div>
			</span>
			@endif
			@endcomponent

			@component('components.widget', ['class' => 'box-primary'])
			<div class="col-sm-10 col-sm-offset-1">
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
							<button type="button" class="btn btn-default bg-white btn-flat pos_add_quick_product"
								data-href="{{action('ProductController@quickAdd')}}"
								data-container=".quick_add_product_modal"><i
									class="fa fa-plus-circle text-primary fa-lg"></i></button>
						</span>
					</div>
				</div>
			</div>

			<div class="row col-sm-12 pos_product_div" style="min-height: 0">

				<input type="hidden" name="sell_price_tax" id="sell_price_tax"
					value="{{!empty($temp_data->sell_price_tax)?$temp_data->sell_price_tax :$business_details->sell_price_tax}}">

				<!-- Keeps count of product rows -->
				<input type="hidden" id="product_row_count" value="0">
				@php
				$hide_tax = '';
				if( session()->get('business.enable_inline_tax') == 0){
				$hide_tax = 'hide';
				}
				@endphp
				<div class="table-responsive">
					<table class="table table-condensed table-bordered table-striped table-responsive" id="pos_table">
						<thead>
							<tr>
								<th class="text-center">
									@lang('sale.product')
								</th>
								<th class="text-center">
									@lang('sale.qty')
								</th>
								@if(!empty($pos_settings['inline_service_staff']))
								<th class="text-center">
									@lang('restaurant.service_staff')
								</th>
								@endif
								<th class="text-center {{$hide_tax}}">
									@lang('sale.price_inc_tax')
								</th>
								<th class="text-center">
									@lang('sale.subtotal')
								</th>
								<th class="text-center"><i class="fa fa-close" aria-hidden="true"></i></th>
							</tr>
						</thead>
						<tbody>
							{{-- @if(!empty($temp_data->products))
							@foreach ($temp_data->products as $key => $product)
								@include('sale_pos.product_row', ['row_count' => $key, 'temp_product' => $product])
							@endforeach
							@endif --}}
						</tbody>
					</table>
				</div>
				<div class="table-responsive">
					<table class="table table-condensed table-bordered table-striped">
						<tr>
							<td>
								<div class="pull-right">
									<b>@lang('sale.item'):</b>
									<span class="total_quantity">0</span>
									&nbsp;&nbsp;&nbsp;&nbsp;
									<b>@lang('sale.total'): </b>
									<span class="price_total">0</span>
								</div>
							</td>
						</tr>
					</table>
				</div>
			</div>
			@endcomponent

			@component('components.widget', ['class' => 'box-primary'])
			<div class="col-md-4 hide">
				<div class="form-group">
					{!! Form::label('discount_type', __('sale.discount_type') . ':*' ) !!}
					<div class="input-group">
						<span class="input-group-addon">
							<i class="fa fa-info"></i>
						</span>
						{!! Form::select('discount_type', ['fixed' => __('lang_v1.fixed'), 'percentage' =>
						__('lang_v1.percentage')], 'fixed' , ['class' => 'form-control','placeholder' =>
						__('messages.please_select'), 'required', 'data-default' => 'percentage']); !!}
					</div>
				</div>
			</div>
			<div class="col-md-4 hide hide">
				<div class="form-group">
					{!! Form::label('discount_amount', __('sale.discount_amount') . ':*' ) !!}
					<div class="input-group">
						<span class="input-group-addon">
							<i class="fa fa-info"></i>
						</span>
						{!! Form::text('discount_amount',
						null,
						['class' => 'form-control input_number', 'data-default' =>
						$business_details->default_sales_discount]); !!}
					</div>
				</div>
			</div>
			<div class="col-md-4 hide"><br>
				<b>@lang( 'sale.discount_amount' ):</b>(-)
				<span class="display_currency" id="total_discount">0</span>
			</div>
			<div class="clearfix"></div>
			<div class="col-md-12 well well-sm bg-light-gray hide ">
				<input type="hidden" name="rp_redeemed" id="rp_redeemed"
					value="{{!empty($temp_data->rp_redeemed)?$temp_data->rp_redeemed :0}}">
				<input type="hidden" name="rp_redeemed_amount" id="rp_redeemed_amount"
					value="{{!empty($temp_data->rp_redeemed_amount)?$temp_data->rp_redeemed_amount :0}}">
				<div class="col-md-12">
					<h4>{{session('business.rp_name')}}</h4>
				</div>
				<div class="col-md-4">
					<div class="form-group">
						{!! Form::label('rp_redeemed_modal', __('lang_v1.redeemed') . ':' ) !!}
						<div class="input-group">
							<span class="input-group-addon">
								<i class="fa fa-gift"></i>
							</span>
							{!! Form::number('rp_redeemed_modal', 0, ['class' => 'form-control direct_sell_rp_input',
							'data-amount_per_unit_point' => session('business.redeem_amount_per_unit_rp'), 'min' => 0,
							'data-max_points' => 0, 'data-min_order_total' =>
							session('business.min_order_total_for_redeem') ]); !!}
							<input type="hidden" id="rp_name" value="{{session('business.rp_name')}}">
						</div>
					</div>
				</div>
				<div class="col-md-4">
					<p><strong>@lang('lang_v1.available'):</strong> <span id="available_rp">0</span></p>
				</div>
				<div class="col-md-4">
					<p><strong>@lang('lang_v1.redeemed_amount'):</strong> (-)<span id="rp_redeemed_amount_text">0</span>
					</p>
				</div>
			</div>
			<div class="clearfix"></div>
			<div class="col-md-4 hide">
				<div class="form-group">
					{!! Form::label('tax_rate_id', __('sale.order_tax') . ':*' ) !!}
					<div class="input-group">
						<span class="input-group-addon">
							<i class="fa fa-info"></i>
						</span>
						{!! Form::select('tax_rate_id', $taxes['tax_rates'],
						$business_details->default_sales_tax,
						['placeholder' => __('messages.please_select'), 'class' => 'form-control', 'data-default'=>
						$business_details->default_sales_tax], $taxes['attributes']); !!}

						<input type="hidden" name="tax_calculation_amount" id="tax_calculation_amount"
							value=""
							data-default="{{$business_details->tax_calculation_amount}}">
					</div>
				</div>
			</div>
			<div class="col-md-4 col-md-offset-4 hide">
				<b>@lang( 'sale.order_tax' ):</b>(+)
				<span class="display_currency" id="order_tax">0</span>
			</div>
			<div class="clearfix"></div>
			<div class="col-md-4">
				<div class="form-group">
					{!! Form::label('shipping_details', __('sale.shipping_details')) !!}
					<div class="input-group">
						<span class="input-group-addon">
							<i class="fa fa-info"></i>
						</span>
						{!!
						Form::textarea('shipping_details',null, ['class' => 'form-control','placeholder' =>
						__('sale.shipping_details') ,'rows' => '1', 'cols'=>'30']); !!}
					</div>
				</div>
			</div>
			<div class="col-md-4">
				<div class="form-group">
					{!! Form::label('shipping_address', __('lang_v1.shipping_address')) !!}
					<div class="input-group">
						<span class="input-group-addon">
							<i class="fa fa-map-marker"></i>
						</span>
						{!!
						Form::textarea('shipping_address',null, ['class' => 'form-control','placeholder' =>
						__('lang_v1.shipping_address') ,'rows' => '1', 'cols'=>'30']); !!}
					</div>
				</div>
			</div>
			<div class="col-md-4 hide">
				<div class="form-group">
					{!!Form::label('shipping_charges', __('sale.shipping_charges'))!!}
					<div class="input-group">
						<span class="input-group-addon">
							<i class="fa fa-info"></i>
						</span>
						{!!Form::text('shipping_charges',0.00,['class'=>'form-control
						input_number','placeholder'=> __('sale.shipping_charges')]);!!}
					</div>
				</div>
			</div>
			<div class="col-md-4  hide">
				<div class="form-group">
					{!! Form::label('shipping_status', __('lang_v1.shipping_status')) !!}
					{!! Form::select('shipping_status',$shipping_statuses,
					'ordered', ['class' =>
					'form-control','placeholder' => __('messages.please_select')]); !!}
				</div>
			</div>
			<div class="col-md-4">
				<div class="form-group">
					{!! Form::label('delivered_to', __('lang_v1.delivered_to') . ':' ) !!}
					{!! Form::text('delivered_to', null,
					['class' => 'form-control','placeholder' =>
					__('lang_v1.delivered_to')]); !!}
				</div>
			</div>
			<div class="clearfix"></div>
			<div class="col-md-4 col-md-offset-8">
				<div><b>@lang('sale.total_payable'): </b>
					<input type="hidden" name="final_total" id="final_total_input"
						value="{{0}}">
					<span id="total_payable">{{0}}</span>
				</div>
			</div>
			<div class="col-md-12 hide">
				<div class="form-group">
					{!! Form::label('sell_note',__('sale.sell_note')) !!}
					{!! Form::textarea('sale_note',null, ['class'
					=> 'form-control', 'rows' => 3]); !!}
				</div>
			</div>
			<input type="hidden" name="customer_business_id" id="customer_business_id"
				value="{{$business_id}}">
			<input type="hidden" name="is_direct_sale"
				value="{{1}}">
			@endcomponent

		</div>
	</div>
	@can('sell.payments')
	@component('components.widget', ['class' => 'box-primary', 'id' => "payment_rows_div", 'title' =>
	__('purchase.add_payment')])
	<div class="payment_row">
		@if (!empty($temp_data->payment))
		@include('sale_pos.partials.payment_row_form', ['row_index' => 0, 'payment' => $temp_data->payment[0]])
		@else
		@include('sale_pos.partials.payment_row_form', ['row_index' => 0])
		@endif
		<hr>
		<div class="row">
			<div class="col-sm-12">
				<div class="pull-right"><strong>@lang('lang_v1.balance'):</strong> <span class="balance_due">0.00</span>
				</div>
			</div>
		</div>
	</div>
	@endcomponent
	@endcan

	<div class="row">
		<div class="col-sm-12">
			<button type="button" id="submit-sell"
				class="btn btn-primary pull-right btn-flat">@lang('messages.submit')</button>
		</div>
	</div>

	@if(empty($pos_settings['disable_recurring_invoice']))
	@include('sale_pos.partials.recurring_invoice_modal')
	@endif

	{!! Form::close() !!}
</section>

<div class="modal fade contact_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
	{{-- @include('contact.create', ['quick_add' => true]) --}}
</div>
<!-- /.content -->
<div class="modal fade register_details_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
</div>
<div class="modal fade close_register_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
</div>

<!-- quick product modal -->
<div class="modal fade quick_add_product_modal" tabindex="-1" role="dialog" aria-labelledby="modalTitle"></div>

@include('sale_pos.partials.configure_search_modal')

@stop

@section('javascript')
<script src="{{ asset('js/pos.js?v=' . $asset_v) }}"></script>
<script src="{{ asset('js/product.js?v=' . $asset_v) }}"></script>
<script src="{{ asset('js/opening_stock.js?v=' . $asset_v) }}"></script>
<script>
    function getInvoice(){
        console.log('getInvoice');
        
    }
</script>

<!-- Call restaurant module if defined -->
@if(in_array('tables' ,$enabled_modules) || in_array('modifiers' ,$enabled_modules) || in_array('service_staff'
,$enabled_modules))
<script src="{{ asset('js/restaurant.js?v=' . $asset_v) }}"></script>
@endif

@endsection