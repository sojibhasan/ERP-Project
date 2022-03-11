<!-- app css -->
@if(!empty($for_pdf))
<link rel="stylesheet" href="{{ asset('css/app.css?v='.$asset_v) }}">
@endif
<style>
	.bg_color {
		background: #357ca5;
		font-size: 20px;
		color: #fff;
	}

	.text-center {
		text-align: center;
	}

	#ledger_table th {
		background: #357ca5;
		color: #fff;
	}

	#ledger_table>tbody>tr:nth-child(2n+1)>td,
	#ledger_table>tbody>tr:nth-child(2n+1)>th {
		background-color: rgba(89, 129, 255, 0.3);
	}
</style>

@php
$currency_precision = !empty($business_details->currency_precision) ? $business_details->currency_precision : 2;
@endphp
<div class="col-md-12 col-sm-12 @if(!empty($for_pdf)) width-100 text-center @endif">
	<p class="text-center"><strong>{{$pump_operator->business->name}}</strong><br>{{$location_details->city}},
		{{$location_details->state}}<br>{!!
		$location_details->mobile !!}</p>
	<hr>
</div>
<div class="col-md-6 col-sm-6 col-xs-6 @if(!empty($for_pdf)) width-50 f-left @endif">
	<p class="bg_color" style="width: 40%">@lang('lang_v1.to'):</p>
	<p><strong>{{$pump_operator->name}}</strong><br> {!! $pump_operator->contact_address !!}
		@if(!empty($pump_operator->email))
		<br>@lang('business.email'): {{$pump_operator->email}} @endif
		<br>@lang('contact.mobile'): {{$pump_operator->mobile}}

	</p>
</div>
<div class="col-md-6 col-sm-6 col-xs-6 text-right align-right @if(!empty($for_pdf)) width-50 f-left @endif">
	<p class=" bg_color" style="margin-top: @if(!empty($for_pdf)) 20px @else 0px @endif; font-weight: 500;">
		@lang('lang_v1.account_summary')</p>
	<hr>
	<table class="table table-condensed text-left align-left no-border @if(!empty($for_pdf)) table-pdf @endif">
		<tr>
			<td>@lang('lang_v1.beginning_balance')</td>
			<td>{{@num_format($ledger_details['beginning_balance'])}}
			</td>
		</tr>

		<tr>
			<td><strong>@lang('lang_v1.total_short')</strong></td>
			<td>{{@num_format($ledger_details['total_short'])}}</td>
		</tr>
		<tr>
			<td><strong>@lang('lang_v1.total_recovered_excess')</strong></td>
			<td>{{@num_format($ledger_details['total_recovered_excess'])}}</td>
		</tr>
		<tr>
			<td><strong>@lang('lang_v1.balance_due')</strong></td>
			<td>{{@num_format($ledger_details['balance_due'])}}</td>
		</tr>
	</table>
</div>
<div class="col-md-12 col-sm-12 @if(!empty($for_pdf)) width-100 @endif">
	<p style="text-align: center !important; float: left; width: 100%;"><strong>@lang('lang_v1.ledger_table_heading',
			['start_date' =>
			$ledger_details['start_date'], 'end_date' => $ledger_details['end_date']])</strong></p>
	<table class="table table-striped @if(!empty($for_pdf)) table-pdf td-border @endif" id="ledger_table">
		<thead>
			<tr class="row-border">
				<th>@lang('lang_v1.date')</th>
				<th>@lang('purchase.ref_no')</th>
				<th>@lang('lang_v1.type')</th>
				<th>@lang('sale.location')</th>
				<th>@lang('account.debit')</th>
				<th>@lang('account.credit')</th>
				<th>@lang('sale.total')</th>
				<th>@lang('lang_v1.payment_method')</th>
			</tr>
		</thead>
		<tbody>
			@php
			$balance = 0;
			@endphp
			@foreach($ledger_transactions as $data)
			@php

			if($data->acc_transaction_type == 'debit') {
			$balance = $balance + $data->amount;
			}
			if($data->acc_transaction_type == 'credit') {
			$balance = $balance - $data->amount;
			}

			@endphp
			<tr>
				<td class="row-border">@if(!empty($data->tp_id)){{@format_date($data->paid_on)}} @else {{@format_date($data->transaction_date)}} @endif</td>
				<td>
					@if($data->transaction_type == 'settlement' && $data->sub_type == 'shortage')
					@if(empty($data->payment_ref_no))<b>Settlment No:</b> {{$data->invoice_no}} <br> @endif @if(!empty($data->payment_ref_no)) Shortage Recovered @else Shortage @endif <br>
					@if(!empty($data->payment_ref_no))<b>Payment Ref No:</b> {{$data->payment_ref_no}} @endif
					@elseif($data->transaction_type == 'settlement' && $data->sub_type == 'excess')
					@if(empty($data->payment_ref_no))<b>Settlment No:</b> {{$data->invoice_no}} <br> @endif @if(!empty($data->payment_ref_no)) Excess Paid @else Excess @endif <br>
					@if(!empty($data->payment_ref_no))<b>Payment Ref No:</b> {{$data->payment_ref_no}} @endif
					@endif
					@if(!empty($data->deleted_by))
					<br><b>Deleted</b>
					@endif
				</td>
				<td>@if($data->transaction_type == 'purchase') @lang('lang_v1.purchase') @elseif($data->transaction_type
					== 'opening_balance') @lang('lang_v1.opening_balance') @elseif($data->transaction_type == 'sell')
					@lang('lang_v1.sell') @elseif($data->transaction_type == 'sell_return') @lang('lang_v1.sell_return')
					@endif</td>
				<td>{{$data->location_name}}</td>
				<td class="ws-nowrap">
					@if($data->acc_transaction_type == 'debit')
						{{@num_format($data->amount)}}
					
					@endif
				</td>
				<td class="ws-nowrap">
					@if($data->acc_transaction_type == 'credit')
						{{@num_format($data->amount)}}
					@endif</td>
				<td class="ws-nowrap">
					{{@num_format($balance)}}
				</td>
				<td>@if(!empty($data->payment_method)) {{$payment_types[$data->payment_method]}} @endif</td>
			</tr>
			@endforeach

		</tbody>
	</table>
</div>

<!-- This will be printed -->
<section class="invoice print_section" id="ledger_print">
</section>