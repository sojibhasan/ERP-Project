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
<table style="width:100%;" style="font-size: {{$h_font_size}}px !important;">
	<thead>
		<tr>
			<td>
				<p class="text-right">
					<small class="text-muted-imp">
					122344
					</small>
				</p>
			</td>
		</tr>
	</thead>

	<tbody>
		<tr>
			<td class="text-{{$header_align}}" style="line-height: 15px !important; padding-bottom: 10px !important">
				@if(!empty($receipt_details->header_text))
					{!! $receipt_details->header_text !!}
				@endif

				@php
					$sub_headings = implode('<br/>', array_filter([$receipt_details->sub_heading_line1, $receipt_details->sub_heading_line2, $receipt_details->sub_heading_line3, $receipt_details->sub_heading_line4, $receipt_details->sub_heading_line5]));
				@endphp

				@if(!empty($sub_headings))
					<span>{!! $sub_headings !!}</span>
				@endif

				@if(!empty($receipt_details->invoice_heading))
					<p class="" style="font-weight: bold; font-size: {{$i_font_size}}px !important;">{!! $receipt_details->invoice_heading !!}</p>
				@endif
			</td>
		</tr>

		<tr>
			<td>

<!-- business information here -->
<div class="row invoice-info"  style="font-size: {{$h_font_size}}px !important;">

	<div class="col-md-6 invoice-col width-50">

		<div class="text-right font-23">
			@if(!empty($receipt_details->invoice_no_prefix))
				<span class="pull-left">{!! $receipt_details->invoice_no_prefix !!}</span>
			@endif

			{{$receipt_details->invoice_no}}
		</div>

		<!-- Total Due-->
		@if(!empty($receipt_details->total_due))
		<div class="bg-light-blue-active text-right font-23 padding-5">
			<span class="pull-left bg-light-blue-active">
				{!! $receipt_details->total_due_label !!}
			</span>
			
			{{$receipt_details->total_due}}
		</div>
		@endif
		@if(!empty($contact_details['due_amount']))
		<div class="bg-light-blue-active text-right font-23 padding-5">
			<span class="pull-left bg-light-blue-active">
				Balance Return
			</span>
			
			{{abs($contact_details['due_amount'])}}
		</div>
		@endif

		@if(!empty($receipt_details->all_due))
			<div class="bg-light-blue-active text-right font-23 padding-5">
				<span class="pull-left bg-light-blue-active">
					{!! $receipt_details->all_bal_label !!}
				</span>

				{{$receipt_details->all_due}}
			</div>
		@endif
		
		<!-- Total Paid-->
		@if(!empty($receipt_details->total_paid))
			<div class="text-right font-23 color-555">
				<span class="pull-left">{!! $receipt_details->total_paid_label !!}</span>
				{{$receipt_details->total_paid}}
			</div>
		@endif
		<!-- Date-->
		@if(!empty($receipt_details->date_label))
			<div class="text-right font-23 color-555">
				<span class="pull-left">
					{{$receipt_details->date_label}}
				</span>

				{{$receipt_details->invoice_date}}
			</div>
		@endif
		@if(!empty($receipt_details->due_date_label))
			<div class="text-right font-23 color-555">
				<span class="pull-left">
					{{$receipt_details->due_date_label}}
				</span>

				{{$receipt_details->due_date ?? ''}}
			</div>
		@endif

		<div class="word-wrap">
			@if(!empty($receipt_details->customer_label))
				<b>{{ $receipt_details->customer_label }}</b><br/>
			@endif

			<!-- customer info -->
			@if(!empty($receipt_details->customer_name))
				{{ $receipt_details->customer_name }}<br>
			@endif
			@if(!empty($receipt_details->customer_info))
				{!! $receipt_details->customer_info !!}
			@endif
			@if(!empty($receipt_details->client_id_label))
				<br/>
				<strong>{{ $receipt_details->client_id_label }}</strong> {{ $receipt_details->client_id }}
			@endif
			@if(!empty($receipt_details->customer_tax_label))
				<br/>
				<strong>{{ $receipt_details->customer_tax_label }}</strong> {{ $receipt_details->customer_tax_number }}
			@endif
			@if(!empty($receipt_details->customer_custom_fields))
				<br/>{!! $receipt_details->customer_custom_fields !!}
			@endif
			@if(!empty($receipt_details->sales_person_label))
				<br/>
				<strong>{{ $receipt_details->sales_person_label }}</strong> {{ $receipt_details->sales_person }}
			@endif

			@if(!empty($receipt_details->customer_rp_label))
				<br/>
				<strong>{{ $receipt_details->customer_rp_label }}</strong> {{ $receipt_details->customer_total_rp }}
			@endif

			<!-- Display type of service details -->
			@if(!empty($receipt_details->types_of_service))
				<span class="pull-left text-left">
					<strong>{!! $receipt_details->types_of_service_label !!}:</strong>
					{{$receipt_details->types_of_service}}
					<!-- Waiter info -->
					@if(!empty($receipt_details->types_of_service_custom_fields))
						<br>
						@foreach($receipt_details->types_of_service_custom_fields as $key => $value)
							<strong>{{$key}}: </strong> {{$value}}@if(!$loop->last), @endif
						@endforeach
					@endif
				</span>
			@endif
		</div>

	</div>

	<div class="col-md-6 invoice-col width-50 color-555">
		
		<!-- Logo -->
		@if(!empty($receipt_details->logo))
			<img src="{{$receipt_details->logo}}" class="img  @if($header_align == 'center') center-block @endif" height="{{$logo_height}}" width="{{$logo_width}}" style="float: @if($header_align == 'left') left @elseif($header_align == 'right') right @endif ; margin-top: {{$logo_margin_top}}px; margin-bottom: {{$logo_margin_bottom}}px; ">
			<br/>
		@endif

		<!-- Shop & Location Name  -->
		@if(!empty($receipt_details->display_name))
			<p>
				<span style="font-size: {{$b_font_size}}px !important;">{{$receipt_details->display_name}} </span>
				@if(!empty($receipt_details->address))
					<br/>{!! $receipt_details->address !!}
				@endif

				@if(!empty($receipt_details->contact))
					<br/>{{ $receipt_details->contact }}
				@endif

				@if(!empty($receipt_details->website))
					<br/>{{ $receipt_details->website }}
				@endif

				@if(!empty($receipt_details->tax_info1))
					<br/>{{ $receipt_details->tax_label1 }} {{ $receipt_details->tax_info1 }}
				@endif

				@if(!empty($receipt_details->tax_info2))
					<br/>{{ $receipt_details->tax_label2 }} {{ $receipt_details->tax_info2 }}
				@endif

				@if(!empty($receipt_details->location_custom_fields))
					<br/>{{ $receipt_details->location_custom_fields }}
				@endif
			</p>
		@endif

		<!-- Table information-->
        @if(!empty($receipt_details->table_label) || !empty($receipt_details->table))
        	<p>
				@if(!empty($receipt_details->table_label))
					{!! $receipt_details->table_label !!}
				@endif
				{{$receipt_details->table}}
			</p>
        @endif

		<!-- Waiter info -->
		@if(!empty($receipt_details->service_staff_label) || !empty($receipt_details->service_staff))
        	<p>
				@if(!empty($receipt_details->service_staff_label))
					{!! $receipt_details->service_staff_label !!}
				@endif
				{{$receipt_details->service_staff}}
			</p>
        @endif



        <div class="word-wrap">

			<p class="text-right color-555">
			@if(!empty($receipt_details->serial_no_label) || !empty($receipt_details->repair_serial_no))
				@if(!empty($receipt_details->serial_no_label))
					<span class="pull-left">
						<strong>{!! $receipt_details->serial_no_label !!}</strong>
					</span>
				@endif
				{{$receipt_details->repair_serial_no}}<br>
	        @endif
			@if(!empty($receipt_details->repair_status_label) || !empty($receipt_details->repair_status))
				@if(!empty($receipt_details->repair_status_label))
					<span class="pull-left">
						<strong>{!! $receipt_details->repair_status_label !!}</strong>
					</span>
				@endif
				{{$receipt_details->repair_status}}<br>
	        @endif
	        
	        @if(!empty($receipt_details->repair_warranty_label) || !empty($receipt_details->repair_warranty))
				@if(!empty($receipt_details->repair_warranty_label))
					<span class="pull-left">
						<strong>{!! $receipt_details->repair_warranty_label !!}</strong>
					</span>
				@endif
				{{$receipt_details->repair_warranty}}
				<br>
	        @endif
	        </p>
		</div>
	@if(!empty($receipt_details->defects_label) || !empty($receipt_details->repair_defects))
		<div class="col-xs-12">
			<p class="color-555">
				<br>
				@if(!empty($receipt_details->defects_label))
					<strong>{!! $receipt_details->defects_label !!}</strong> 
				@endif
				{{$receipt_details->repair_defects}}
			</p>
		</div>
    @endif

	</div>
</div>

<div class="row color-555"  style="font-size: {{$f_font_size}}px !important;">
	<div class="col-xs-12">
		<br/>
		<table class="table table-bordered table-no-top-cell-border">
			<thead>
				<tr style="background-color: #357ca5 !important; color: white !important; font-size: 15px !important" class="table-no-side-cell-border table-no-top-cell-border text-center">
					<td style="background-color: #357ca5 !important; color: white !important;width: 5% !important">#</td>
					
					@php
						$p_width = 25;
					@endphp
					@if($receipt_details->show_cat_code != 1)
						@php
							$p_width = 35;
						@endphp
					@endif
					<td style="background-color: #357ca5 !important; color: white !important; width: {{$p_width}}% !important">
						{{$receipt_details->table_product_label}}
					</td>

					@if($receipt_details->show_cat_code == 1)
						<td style="background-color: #357ca5 !important; color: white !important; width: 10% !important;">{{$receipt_details->cat_code_label}}</td>
					@endif
					
					<td style="background-color: #357ca5 !important; color: white !important;width: 10% !important;">
						{{$receipt_details->table_qty_label}}
					</td>
					<td style="background-color: #357ca5 !important; color: white !important;width: 10% !important;">
						{{$receipt_details->table_unit_price_label}}
					</td>
					<td style="background-color: #357ca5 !important; color: white !important;width: 10% !important;">
						{{$receipt_details->line_discount_label}}
					</td>
					<td style="background-color: #357ca5 !important; color: white !important;width: 10% !important;">
						{{$receipt_details->line_tax_label}}
					</td>
					<td style="background-color: #357ca5 !important; color: white !important;width: 10% !important;">
						{{$receipt_details->table_unit_price_label}} (@lang('product.inc_of_tax'))
					</td>
					<td style="background-color: #357ca5 !important; color: white !important;width: 10% !important;">
						{{$receipt_details->table_subtotal_label}}
					</td>
				</tr>
			</thead>
			<tbody>
				@foreach($receipt_details->lines as $line)
					<tr>
						<td class="text-center  font-size: {{$font_size}}px !important;">
							{{$loop->iteration}}
						</td>
						<td style="word-break: break-all; font-size: {{$font_size}}px !important;">
							@if(!empty($line['image']))
								<img src="{{$line['image']}}" alt="Image" width="50" style="float: left; margin-right: 8px;">
							@endif
                            {{$line['name']}} {{$line['product_variation']}} {{$line['variation']}} 
                            @if(!empty($line['sub_sku'])), {{$line['sub_sku']}} @endif @if(!empty($line['brand'])), {{$line['brand']}} @endif
                            @if(!empty($line['product_custom_fields'])), {{$line['product_custom_fields']}} @endif
                            @if(!empty($line['sell_line_note']))({{$line['sell_line_note']}}) @endif
                            @if(!empty($line['lot_number']))<br> {{$line['lot_number_label']}}:  {{$line['lot_number']}} @endif 
                            @if(!empty($line['product_expiry'])), {{$line['product_expiry_label']}}:  {{$line['product_expiry']}} @endif 

                            @if(!empty($line['warranty_name'])) <br><small>{{$line['warranty_name']}} </small>@endif @if(!empty($line['warranty_exp_date'])) <small>- {{@format_date($line['warranty_exp_date'])}} </small>@endif
                            @if(!empty($line['warranty_description'])) <small> {{$line['warranty_description'] ?? ''}}</small>@endif
                        </td>

						@if($receipt_details->show_cat_code == 1)
	                        <td style="font-size: {{$font_size}}px !important;">
	                        	@if(!empty($line['cat_code']))
	                        		{{$line['cat_code']}}
	                        	@endif
	                        </td>
	                    @endif

						<td class="text-right"  style="font-size: {{$font_size}}px !important;">
							{{$line['quantity']}} {{$line['units']}}
						</td>
						<td class="text-right"  style="font-size: {{$font_size}}px !important;">
							{{$line['unit_price_before_discount']}}
						</td>
						<td class="text-right"  style="font-size: {{$font_size}}px !important;">
							{{$line['line_discount']}}
						</td>
						<td class="text-right"  style="font-size: {{$font_size}}px !important;">
							{{$line['tax']}} {{$line['tax_name']}}
						</td>
						<td class="text-right"  style="font-size: {{$font_size}}px !important;">
							{{$line['unit_price_inc_tax']}}
						</td>
						<td class="text-right"  style="font-size: {{$font_size}}px !important;">
							{{$line['line_total']}}
						</td>
					</tr>
					@if(!empty($line['modifiers']))
						@foreach($line['modifiers'] as $modifier)
							<tr>
								<td class="text-center">
									&nbsp;
								</td>
								<td>
		                            {{$modifier['name']}} {{$modifier['variation']}} 
		                            @if(!empty($modifier['sub_sku'])), {{$modifier['sub_sku']}} @endif 
		                            @if(!empty($modifier['sell_line_note']))({{$modifier['sell_line_note']}}) @endif 
		                        </td>

								@if($receipt_details->show_cat_code == 1)
			                        <td>
			                        	@if(!empty($modifier['cat_code']))
			                        		{{$modifier['cat_code']}}
			                        	@endif
			                        </td>
			                    @endif

								<td class="text-right">
									{{$modifier['quantity']}} {{$modifier['units']}}
								</td>
								<td class="text-right">
									&nbsp;
								</td>
								<td class="text-center">
									&nbsp;
								</td>
								<td class="text-center">
									&nbsp;
								</td>
								<td class="text-center">
									{{$modifier['unit_price_exc_tax']}}
								</td>
								<td class="text-right">
									{{$modifier['line_total']}}
								</td>
							</tr>
						@endforeach
					@endif
				@endforeach

				@php
					$lines = count($receipt_details->lines);
				@endphp

				@for ($i = $lines; $i < 5; $i++)
    				<tr>
    					<td>&nbsp;</td>
    					<td>&nbsp;</td>
    					<td>&nbsp;</td>
    					<td>&nbsp;</td>
    					<td>&nbsp;</td>
    					<td>&nbsp;</td>
    					<td>&nbsp;</td>
    					<td>&nbsp;</td>
    					@if($receipt_details->show_cat_code == 1)
    						<td>&nbsp;</td>
    					@endif
    				</tr>
				@endfor

			</tbody>
		</table>
	</div>
</div>

<div class="row invoice-info color-555" style="page-break-inside: avoid !important">
	<div class="col-md-6 invoice-col width-50">
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
		</table>
		<b class="pull-left">@lang('lang_v1.authorized_signatory')</b>
	</div>

	<div class="col-md-6 invoice-col width-50">
		<table class="table-no-side-cell-border table-no-top-cell-border width-100">
			<tbody>
				<tr class="color-555">
					<td style="width:50%">
						{!! $receipt_details->subtotal_label !!}
					</td>
					<td class="text-right">
						{{$receipt_details->subtotal}}
					</td>
				</tr>
				
				<!-- Shipping Charges -->
				@if(!empty($receipt_details->shipping_charges))
					<tr class="color-555">
						<td style="width:50%">
							{!! $receipt_details->shipping_charges_label !!}
						</td>
						<td class="text-right">
							{{$receipt_details->shipping_charges}}
						</td>
					</tr>
				@endif

				<!-- Discount -->
				@if( !empty($receipt_details->discount) )
					<tr class="color-555">
						<td>
							{!! $receipt_details->discount_label !!}
						</td>

						<td class="text-right">
							(-) {{$receipt_details->discount}}
						</td>
					</tr>
				@endif

				@if( !empty($receipt_details->reward_point_label) )
					<tr class="color-555">
						<td>
							{!! $receipt_details->reward_point_label !!}
						</td>

						<td class="text-right">
							(-) {{$receipt_details->reward_point_amount}}
						</td>
					</tr>
				@endif

				@if(!empty($receipt_details->group_tax_details))
					@foreach($receipt_details->group_tax_details as $key => $value)
						<tr class="color-555">
							<td>
								{!! $key !!}
							</td>
							<td class="text-right">
								(+) {{$value}}
							</td>
						</tr>
					@endforeach
				@else
					@if( !empty($receipt_details->tax) )
						<tr class="color-555">
							<td>
								{!! $receipt_details->tax_label !!}
							</td>
							<td class="text-right">
								(+) {{$receipt_details->tax}}
							</td>
						</tr>
					@endif
				@endif
				
				
				<!-- Total -->
				<tr>
					<th style="background-color: #357ca5 !important; color: white !important" class="font-23 padding-10">
						{!! $receipt_details->total_label !!}
					</th>
					<td class="text-right font-23 padding-10" style="background-color: #357ca5 !important; color: white !important">
						{{$receipt_details->total}}
					</td>
				</tr>
			</tbody>
        </table>
	</div>
</div>

<div class="row color-555">
	<div class="col-xs-6">
		{{$receipt_details->additional_notes}}
	</div>
</div>
{{-- Barcode --}}
@if($receipt_details->show_barcode)
<br>
<div class="row">
		<div class="col-xs-12">
			<img class="center-block" src="data:image/png;base64,{{DNS1D::getBarcodePNG($receipt_details->invoice_no, 'C128', 2,30,array(39, 48, 54), true)}}">
		</div>
</div>
@endif

@if(!empty($receipt_details->footer_text))
	<div class="row color-555">
		<div class="col-xs-12">
			{!! $receipt_details->footer_text !!}
		</div>
	</div>
@endif
@if(!empty($admin_invoice_footer))
	<div class="row color-555">
		<div class="col-xs-12">
			<p class="centered" style="margin-top: @if(!empty($footer_top_margin)) {{$footer_top_margin }}px;	 @else 10px; @endif">
				{!! $admin_invoice_footer !!}
			</p>
		</div>
	</div>
@endif
			</td>
		</tr>
	</tbody>
</table>