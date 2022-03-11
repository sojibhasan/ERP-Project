<!-- business information here -->
@php
$font_size = $receipt_details->font_size;
$h_font_size = $receipt_details->header_font_size;
$f_font_size = $receipt_details->footer_font_size;
$b_font_size = $receipt_details->business_name_font_size;
$i_font_size = $receipt_details->invoice_heading_font_size;
$footer_top_margin = $receipt_details->footer_top_margin;
$admin_invoice_footer = $receipt_details->admin_invoice_footer;
$logo_height = $receipt_details->logo_height;
$logo_width = $receipt_details->logo_width;
$logo_margin_top = $receipt_details->logo_margin_top;
$logo_margin_bottom = $receipt_details->logo_margin_bottom;
$header_align = $receipt_details->header_align;
$contact_details = $receipt_details->contact_details;
@endphp
<div class="row" style="font-size: {{$h_font_size}}px !important;">

	<!-- Logo -->
	@if(!empty($receipt_details->logo))
	<img src="{{$receipt_details->logo}}" class="img img-responsive @if($header_align == 'center') center-block @endif"
		height="{{$logo_height}}" width="{{$logo_width}}"
		style="float: @if($header_align == 'left') left @elseif($header_align == 'right') right @endif ; margin-top: {{$logo_margin_top}}px; margin-bottom: {{$logo_margin_bottom}}px; ">
	@endif

	<!-- Header text -->
	@if(!empty($receipt_details->header_text))
	<div class="col-xs-12">
		{!! $receipt_details->header_text !!}
	</div>
	@endif

	<!-- business information here -->
	<div class="col-xs-12 text-{{$header_align}}">
		<h2 class="" style="font-size: {{$b_font_size}}px !important;">
			<!-- Shop & Location Name  -->
			@if(!empty($receipt_details->display_name))
			{{$receipt_details->display_name}}
			@endif
		</h2>

		<!-- Address -->
		<p>
			@if(!empty($receipt_details->address))
			<small class="">
				{!! $receipt_details->address !!}
			</small>
			@endif
			@if(!empty($receipt_details->contact))
			<br />{{ $receipt_details->contact }}
			@endif
			@if(!empty($receipt_details->location_custom_fields))
			<br>{{ $receipt_details->location_custom_fields }}
			@endif
		</p>
		<p>
			@if(!empty($receipt_details->sub_heading_line1))
			{{ $receipt_details->sub_heading_line1 }}
			@endif
			@if(!empty($receipt_details->sub_heading_line2))
			<br>{{ $receipt_details->sub_heading_line2 }}
			@endif
			@if(!empty($receipt_details->sub_heading_line3))
			<br>{{ $receipt_details->sub_heading_line3 }}
			@endif
			@if(!empty($receipt_details->sub_heading_line4))
			<br>{{ $receipt_details->sub_heading_line4 }}
			@endif
			@if(!empty($receipt_details->sub_heading_line5))
			<br>{{ $receipt_details->sub_heading_line5 }}
			@endif
		</p>
		<p>
			@if(!empty($receipt_details->tax_info1))
			<b>{{ $receipt_details->tax_label1 }}</b> {{ $receipt_details->tax_info1 }}
			@endif

			@if(!empty($receipt_details->tax_info2))
			<b>{{ $receipt_details->tax_label2 }}</b> {{ $receipt_details->tax_info2 }}
			@endif
		</p>

		<!-- Title of receipt -->
		@if(!empty($receipt_details->invoice_heading))
		<h3 class="" style="font-size: {{$i_font_size}}px !important;">
			{!! $receipt_details->invoice_heading !!}
		</h3>
		@endif

		<!-- Invoice  number, Date  -->
		<p style="width: 100% !important" class="word-wrap">
			<span class="pull-left text-left word-wrap">
				@if(!empty($receipt_details->invoice_no_prefix))
				<b>{!! $receipt_details->invoice_no_prefix !!}</b>
				@endif
				{{$receipt_details->invoice_no}}

				@if(!empty($receipt_details->types_of_service))
				<br />
				<span class="pull-left text-left">
					<strong>{!! $receipt_details->types_of_service_label !!}:</strong>
					{{$receipt_details->types_of_service}}
					<!-- Waiter info -->
					@if(!empty($receipt_details->types_of_service_custom_fields))
					@foreach($receipt_details->types_of_service_custom_fields as $key => $value)
					<br><strong>{{$key}}: </strong> {{$value}}
					@endforeach
					@endif
				</span>
				@endif

				<!-- Table information-->
				@if(!empty($receipt_details->table_label) || !empty($receipt_details->table))
				<br />
				<span class="pull-left text-left">
					@if(!empty($receipt_details->table_label))
					<b>{!! $receipt_details->table_label !!}</b>
					@endif
					{{$receipt_details->table}}

					<!-- Waiter info -->
				</span>
				@endif

				<!-- customer info -->
				@if(!empty($receipt_details->customer_name))
				<br />
				<b>{{ $receipt_details->customer_label }}</b> {{ $receipt_details->customer_name }} <br>
				@endif
				@if(!empty($receipt_details->customer_info))
				{!! $receipt_details->customer_info !!}
				@endif
				@if(!empty($receipt_details->client_id_label))
				<br />
				<b>{{ $receipt_details->client_id_label }}</b> {{ $receipt_details->client_id }}
				@endif
				@if(!empty($receipt_details->customer_tax_label))
				<br />
				<b>{{ $receipt_details->customer_tax_label }}</b> {{ $receipt_details->customer_tax_number }}
				@endif
				@if(!empty($receipt_details->customer_custom_fields))
				<br />{!! $receipt_details->customer_custom_fields !!}
				@endif
				@if(!empty($receipt_details->sales_person_label))
				<br />
				<b>{{ $receipt_details->sales_person_label }}</b> {{ $receipt_details->sales_person }}
				@endif
				@if(!empty($receipt_details->customer_rp_label))
				<br />
				<strong>{{ $receipt_details->customer_rp_label }}</strong> {{ $receipt_details->customer_total_rp }}
				@endif
			</span>

			<span class="pull-right text-left">
				<b>{{$receipt_details->date_label}}</b> {{$receipt_details->invoice_date}}

				@if(!empty($receipt_details->due_date_label))
				<br><b>{{$receipt_details->due_date_label}}</b> {{$receipt_details->due_date ?? ''}}
				@endif

				@if(!empty($receipt_details->serial_no_label) || !empty($receipt_details->repair_serial_no))
				<br>
				@if(!empty($receipt_details->serial_no_label))
				<b>{!! $receipt_details->serial_no_label !!}</b>
				@endif
				{{$receipt_details->repair_serial_no}}<br>
				@endif
				@if(!empty($receipt_details->repair_status_label) || !empty($receipt_details->repair_status))
				@if(!empty($receipt_details->repair_status_label))
				<b>{!! $receipt_details->repair_status_label !!}</b>
				@endif
				{{$receipt_details->repair_status}}<br>
				@endif

				@if(!empty($receipt_details->repair_warranty_label) || !empty($receipt_details->repair_warranty))
				@if(!empty($receipt_details->repair_warranty_label))
				<b>{!! $receipt_details->repair_warranty_label !!}</b>
				@endif
				{{$receipt_details->repair_warranty}}
				<br>
				@endif

				<!-- Waiter info -->
				@if(!empty($receipt_details->service_staff_label) || !empty($receipt_details->service_staff))
				<br />
				@if(!empty($receipt_details->service_staff_label))
				<b>{!! $receipt_details->service_staff_label !!}</b>
				@endif
				{{$receipt_details->service_staff}}
				@endif
			</span>
		</p>
	</div>

	@if(!empty($receipt_details->defects_label) || !empty($receipt_details->repair_defects))
	<div class="col-xs-12">
		<br>
		@if(!empty($receipt_details->defects_label))
		<b>{!! $receipt_details->defects_label !!}</b>
		@endif
		{{$receipt_details->repair_defects}}
	</div>
	@endif
	<!-- /.col -->
</div>


<div class="row">
	<div class="col-xs-12">
		<table class="table table-responsive">
			<thead>
				<tr>
					<th>{{$receipt_details->table_product_label}}</th>
					<th>{{$receipt_details->table_qty_label}}</th>
					<th>{{$receipt_details->table_unit_price_label}}</th>
					<th>Discount</th>
					<th>{{$receipt_details->table_subtotal_label}}</th>
				</tr>
			</thead>
			<tbody>
				@forelse($receipt_details->lines as $line)
				<tr>
					<td style="word-break: break-all; font-size: {{$font_size}}px !important;">
						@if(!empty($line['image']))
						<img src="{{$line['image']}}" alt="Image" width="50" style="float: left; margin-right: 8px;">
						@endif
						{{$line['name']}} {{$line['product_variation']}} {{$line['variation']}}
						@if(!empty($line['sub_sku'])), {{$line['sub_sku']}} @endif @if(!empty($line['brand'])),
						{{$line['brand']}} @endif @if(!empty($line['cat_code'])), {{$line['cat_code']}}@endif
						@if(!empty($line['product_custom_fields'])), {{$line['product_custom_fields']}} @endif
						@if(!empty($line['sell_line_note']))({{$line['sell_line_note']}}) @endif
						@if(!empty($line['lot_number']))<br> {{$line['lot_number_label']}}: {{$line['lot_number']}}
						@endif
						@if(!empty($line['product_expiry'])), {{$line['product_expiry_label']}}:
						{{$line['product_expiry']}} @endif

						@if(!empty($line['warranty_name'])) <br><small>{{$line['warranty_name']}} </small>@endif
						@if(!empty($line['warranty_exp_date'])) <small>- {{@format_date($line['warranty_exp_date'])}}
						</small>@endif
						@if(!empty($line['warranty_description'])) <small>
							{{$line['warranty_description'] ?? ''}}</small>@endif
					</td>
					<td style="font-size: {{$font_size}}px !important;">{{$line['quantity']}} {{$line['units']}} </td>
					<td style="font-size: {{$font_size}}px !important;">{{$line['unit_price_inc_tax']}}</td>
					<td style="font-size: {{$font_size}}px !important;">{{$line['line_discount'] * $line['quantity']}} {{$line['line_discount_percentage']}}
					</td>
					<td style="font-size: {{$font_size}}px !important;">{{$line['line_total']}}</td>
				</tr>
				@if(!empty($line['modifiers']))
				@foreach($line['modifiers'] as $modifier)
				<tr>
					<td>
						{{$modifier['name']}} {{$modifier['variation']}}
						@if(!empty($modifier['sub_sku'])), {{$modifier['sub_sku']}} @endif
						@if(!empty($modifier['cat_code'])), {{$modifier['cat_code']}}@endif
						@if(!empty($modifier['sell_line_note']))({{$modifier['sell_line_note']}}) @endif
					</td>
					<td>{{$modifier['quantity']}} {{$modifier['units']}} </td>
					<td>{{$modifier['unit_price_inc_tax']}}</td>
					<td>{{$modifier['line_total']}}</td>
				</tr>
				@endforeach
				@endif
				@empty
				<tr>
					<td colspan="4">&nbsp;</td>
				</tr>
				@endforelse
			</tbody>
		</table>
	</div>
</div>

<div class="row" style="font-size: {{$f_font_size}}px !important;">
	<br />
	<div class="col-md-12">
		<hr />
	</div>
	<br />


	<div class="col-xs-6">

		<table class="table table-condensed">

			@if(!empty($receipt_details->payments))
			@foreach($receipt_details->payments as $payment)
			<tr>
				<td>{{$payment['method']}}</td>
				<td>{{$payment['amount']}}</td>
				<!--<td>{{$payment['date']}}</td>-->
			</tr>
			@endforeach
			@endif

			<!-- Total Paid-->
			@if(!empty($receipt_details->total_paid))
			<tr>
				<th>
					{!! $receipt_details->total_paid_label !!}
				</th>
				<td>
					{{$receipt_details->total_paid}}
				</td>
			</tr>
			@endif

			<!-- Total Due-->
			@if(!empty($receipt_details->total_due))
			<tr>
				<th>
					{!! $receipt_details->total_due_label !!}
				</th>
				<td>
					{{$receipt_details->total_due}}
				</td>
			</tr>
			@endif
			<!--@if(!empty($contact_details['due_amount']))
			<tr>
				<th>
					Balance Return
				</th>
				<td>
					{{abs($contact_details['due_amount'])}}
				</td>
			</tr>
			@endif-->
			
			@if($receipt_details->paid_greater === true)
				<tr>
					<th>
						Balance Return
					</th>
					<td>
						{{$receipt_details->due_amount}}
					</td>
				</tr>
			@endif
			@if(!empty($receipt_details->all_due))
			<tr>
				<th>
					{!! $receipt_details->all_bal_label !!}
				</th>
				<td>
					{{$receipt_details->all_due}}
				</td>
			</tr>
			@endif
		</table>

		{{$receipt_details->additional_notes}}
	</div>

	<div class="col-xs-6">
		<div class="table-responsive">
			<table class="table">
				<tbody>
					<tr>
						<th style="width:70%">
							{!! $receipt_details->subtotal_label !!}
						</th>
						<td>
							{{$receipt_details->subtotal}}
						</td>
					</tr>

					<!-- Shipping Charges -->
					@if(!empty($receipt_details->shipping_charges))
					<tr>
						<th style="width:70%">
							{!! $receipt_details->shipping_charges_label !!}
						</th>
						<td>
							{{$receipt_details->shipping_charges}}
						</td>
					</tr>
					@endif

					<!-- Discount -->
					@if( !empty($receipt_details->discount) )
					<tr>
						<th id="discount">
							{!! $receipt_details->discount_label !!}
						</th>

						<td >
							(-) {{$receipt_details->discount}}
						</td>
					</tr>
					@endif

					@if( !empty($receipt_details->reward_point_label) )
					<tr>
						<th>
							{!! $receipt_details->reward_point_label !!}
						</th>

						<td>
							(-) {{$receipt_details->reward_point_amount}}
						</td>
					</tr>
					@endif

					<!-- Tax -->
					@if( !empty($receipt_details->tax) )
					<tr>
						<th>
							{!! $receipt_details->tax_label !!}
						</th>
						<td>
							(+) {{$receipt_details->tax}}
						</td>
					</tr>
					@endif

					<!-- Total -->
					<tr>
						<th>
							{!! $receipt_details->total_label !!}
						</th>
						<td>
							{{$receipt_details->total}}
						</td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>
</div>

@if($receipt_details->show_barcode)
<div class="row">
	<div class="col-xs-12">
		{{-- Barcode --}}
		<img class="center-block"
			src="data:image/png;base64,{{DNS1D::getBarcodePNG($receipt_details->invoice_no, 'C128', 2,30,array(39, 48, 54), true)}}">
	</div>
</div>
@endif

@if(!empty($receipt_details->footer_text))
<div class="row">
	<div class="col-xs-12">
		{!! $receipt_details->footer_text !!}
	</div>
</div>
@endif

@if(!empty($admin_invoice_footer))
<div class="row">
	<div class="col-xs-12">
		<p class="centered"
			style="margin-top: @if(!empty($footer_top_margin)) {{$footer_top_margin }}px; @else 10px; @endif">
			{!! $admin_invoice_footer !!}
		</p>
	</div>
</div>
@endif