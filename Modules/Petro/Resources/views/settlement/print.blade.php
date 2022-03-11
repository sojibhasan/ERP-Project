<!-- app css -->


<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta http-equiv="X-UA-Compatible" content="ie=edge">
<link rel="stylesheet" href="{{ asset('css/app.css?v='.$asset_v) }}">
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
<title>@lang('petro::lang.print_settlement')</title>

@php
$business_id = session()->get('user.business_id');
$business_details = App\Business::find($business_id);
$currency_precision = !empty($business_details->currency_precision) ? $business_details->currency_precision : 2;
@endphp


<style>
	.settlement_print_div table. {
		border: 1px solid #222;
		margin-top: 10px;
		margin-bottom: 0px;
	}

	.settlement_print_div table.table-bordered>thead>tr>th {
		border: 1px solid #222;
		;
	}

	.settlement_print_div table.table-bordered>tbody>tr>td {
		border: 1px solid #222;
		font-size: 13px;
	}

	.settlement_print_div {
		max-width: 70%;
	}

	@media print {

		.no-print,
		.no-print * {
			display: none !important;
		}
	}
</style>
<div class="container settlement_print_div" style="width: 65%; margin-auto">
	<div class="col-xs-12 text-center">
		<p style="font-size: 22px;" class="text-center"><strong>{{request()->session()->get('business.name')}}</strong>
		</p>
		<p style="font-size: 16px;"><strong>@lang('petro::lang.pump_operator_sale_report')</strong></p>

		<a style=" border-radius: 0px !important; float: right; margin-bottom: 10px;" class="btn btn-success btn-sm btn-flat pull-right  no-print"
			href="{{action('\Modules\Petro\Http\Controllers\SettlementController@create')}}">@lang('petro::lang.back_to_settlement')</a>

	</div>
	<div class="clearfix"></div>
	<div class="col-xs-12 col-xs-12" style="border: 1px solid #222;">
		<div class="col-md-8" style="width: 50%; float: left;">
			@lang('petro::lang.address') : {{$pump_operator->address}} <br>
			@lang('petro::lang.settlement_no') : {{$settlement->settlement_no}} <br>
			@lang('petro::lang.settlement_date') : {{$settlement->transaction_date}}
		</div>
		<div class="col-md-4" style="width: 50%; float: right;">
			@lang('petro::lang.pump_operator_name') : {{$pump_operator->name}}<br>
			@lang('petro::lang.print_date_and_time') : {{\Carbon::now()}}<br>
			@if(!empty($settlement->work_shift))
			@foreach ($settlement->work_shift as $work_shift)
			@php
			$work_shift_timing = \Modules\HR\Entities\WorkShift::where('id', $work_shift)->first();
			@endphp
			@lang('petro::lang.shift_time_from') : {{$work_shift_timing->shift_form}} @lang('petro::lang.to') :
			{{$work_shift_timing->shift_to}} <br>
			@endforeach
			@endif

		</div>
	</div>


	<div class="clearfix"></div>
	<br>
	<div class="col-xs-12 text-center" style="font-weight: bold; maring-bottom: 0px; font-size: 18px;">
		@lang('petro::lang.meter_sale')
	</div>
	<div class="row">
		<div class="col-md-12">
			<table class="table table-striped" style="width: 100%;">
				<thead>
					<tr class="row-border">
						<th>@lang('petro::lang.code' )</th>
						<th>@lang('petro::lang.products' )</th>
						<th>@lang('petro::lang.pump' )</th>
						<th>@lang('petro::lang.starting_meter')</th>
						<th>@lang('petro::lang.closing_meter')</th>
						<th>@lang('petro::lang.unit_price')</th>
						<th>@lang('petro::lang.sold_qty' )</th>
						<th>@lang('petro::lang.total' )</th>
					</tr>
				</thead>
				<tbody>
					@php
					$final_total = 0.00;
					@endphp
					@if (!empty($settlement))
					@foreach ($settlement->meter_sales as $item)
					@php
					$product = App\Product::where('id', $item->product_id)->first();
					$pump = Modules\Petro\Entities\Pump::where('id', $item->pump_id)->first();
					$final_total = $final_total + $item->sub_total;
					@endphp
					<tr>
						<td>{{$product->sku}}</td>
						<td>{{$product->name}}</td>
						<td>{{$pump->pump_no}}</td>
						<td>{{$item->starting_meter}}</td>
						<td>{{$item->closing_meter}}</td>
						<td>{{@num_format($item->price)}}</td>
						<td>{{@num_format($item->qty)}}</td>
						<td class="text-right">{{@num_format($item->sub_total)}}</td>
					</tr>
					@endforeach
					@endif
					<tr>
						<td colspan="7" style="text-align: right;"><b>@lang('petro::lang.sub_total')</b></td>
						<td class="text-right">{{@num_format($final_total)}}</td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>


	<div class="clearfix"></div>
	<br>
	<div class="col-xs-12 text-center" style="font-weight: bold; maring-bottom: 0px;  font-size: 18px;">
		@lang('petro::lang.other_sale')
	</div>
	<div class="row">
		<div class="col-md-12">
			<table class="table table-striped">
				<thead>
					<tr>
						<th>@lang('petro::lang.code' )</th>
						<th>@lang('petro::lang.products' )</th>
						<th>@lang('petro::lang.unit_price')</th>
						<th>@lang('petro::lang.sold_qty' )</th>
						<th>@lang('petro::lang.sub_total' )</th>
					</tr>
				</thead>
				<tbody>
					@php
					$other_sale_final_total = 0.00;
					@endphp
					@if (!empty($settlement))
					@foreach ($settlement->other_sales as $ot_item)
					@php
					$product = App\Product::where('id', $ot_item->product_id)->first();
					$other_sale_final_total = $other_sale_final_total + $ot_item->sub_total;
					@endphp
					<tr>
						<td>{{$product->sku}}</td>
						<td>{{$product->name}}</td>
						<td>{{@num_format($ot_item->price)}}</td>
						<td>{{@num_format($ot_item->qty)}}</td>
						<td class="text-right">{{@num_format($ot_item->sub_total)}}</td>
					</tr>
					@endforeach
					@endif
					<tr>
						<td colspan="4" style="text-align: right;"><b>@lang('petro::lang.sub_total')</b></td>
						<td class="text-right">{{@num_format($other_sale_final_total)}}</td>
					</tr>

				</tbody>
			</table>
		</div>
	</div>

	<div class="clearfix"></div>
	<br>
	<div class="col-xs-12 text-center" style="font-weight: bold; maring-bottom: 0px; font-size: 18px;">
		@lang('petro::lang.other_income')
	</div>
	<div class="row">
		<div class="col-md-12">
			<table class="table table-striped">
				<thead>
					<tr>
						<th>@lang('petro::lang.service' )</th>
						<th>@lang('petro::lang.qty' )</th>
						<th>@lang('petro::lang.reason' )</th>
						<th>@lang('petro::lang.sub_total' )</th>
					</tr>
				</thead>
				<tbody>
					@php
					$other_income_final_total = 0.00;
					@endphp
					@if (!empty($settlement))
					@foreach ($settlement->other_incomes as $other_income_item)
					@php
					$product = App\Product::where('id', $other_income_item->product_id)->first();
					$other_income_final_total = $other_income_final_total + $other_income_item->sub_total;
					@endphp
					<tr>
						<td>{{$product->name}}</td>
						<td>{{@num_format($other_income_item->qty)}}</td>
						<td>{{$other_income_item->reason}}</td>
						<td class="text-right">{{@num_format($other_income_item->sub_total)}}</td>
					</tr>
					@endforeach
					@endif
					<tr>
						<td colspan="3" style="text-align: right;"><b>@lang('petro::lang.sub_total')</b></td>
						<td class="text-right">{{@num_format($other_income_final_total)}}</td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>


	<div class="clearfix"></div>
	<br>
	<div class="col-xs-12 text-center" style="font-weight: bold; maring-bottom: 0px; font-size: 18px;">
		@lang('petro::lang.customer_payment')
	</div>
	<div class="row">
		<div class="col-md-12">
			<table class="table table-striped">
				<thead>
					<tr>
						<th>@lang('petro::lang.customer' )</th>
						<th>@lang('petro::lang.payment_method' )</th>
						<th>@lang('petro::lang.amount' )</th>
					</tr>
				</thead>
				<tbody>
					@php
					$customer_payment_final_total = 0.00;
					@endphp
					@if (!empty($customer_payments_tab))
					@foreach ($customer_payments_tab as $customer_payment_item)
					@php
					$customer_name = App\Contact::where('id', $customer_payment_item->customer_id)->first();
					$customer_payment_final_total = $customer_payment_final_total +
					$customer_payment_item->sub_total;
					@endphp
					<tr>
						<td>{{!empty($customer_name) ? $customer_name->name : ''}}</td>
						<td>{{ucfirst($customer_payment_item->payment_method)}}</td>
						<td class="text-right">{{@num_format($customer_payment_item->sub_total)}}</td>
					</tr>
					@endforeach
					@endif
					<tr>
						<td colspan="2" style="text-align: right;"><b>@lang('petro::lang.sub_total')</b></td>
						<td class="text-right">{{@num_format($customer_payment_final_total)}}</td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>


	<div class="clearfix"></div>
	<br>
	<div class="col-xs-12 text-center" style="font-weight: bold; maring-bottom: 0px; font-size: 18px;">
		@lang('petro::lang.credit_sales')
	</div>
	<div class="row">
		<div class="col-md-12">
			<table class="table table-striped">
				<thead>
					<tr>
						<th>@lang('petro::lang.cusotmer_name' )</th>
						<th>@lang('petro::lang.current_outstanding_before_sale' )</th>
						<th>@lang('petro::lang.voucher_no' )</th>
						<th>@lang('petro::lang.product_name' )</th>
						<th>@lang('petro::lang.qty' )</th>
						<th>@lang('petro::lang.unit_rate' )</th>
						<th>@lang('petro::lang.total' )</th>
					</tr>
				</thead>
				<tbody>
					@php
					$credit_sale_total = $settlement->credit_sale_payments->sum('amount');
					@endphp
					@if(!empty($settlement->credit_sale_payments ))
					@foreach ($settlement->credit_sale_payments as $credit_sale_payment)
					@php
					$customer_name = App\Contact::where('id', $credit_sale_payment->customer_id)->first();
					$product = App\Product::where('id', $credit_sale_payment->product_id)->first();
					@endphp
					<tr>
						<td>{{!empty($customer_name) ? $customer_name->name : ''}}</td>
						<td>{{@num_format($credit_sale_payment->outstanding)}}</td>
						<td>{{$credit_sale_payment->order_number}}</td>
						<td>{{!empty($product)? $product->name : '' }}</td>
						<td>{{@num_format($credit_sale_payment->qty)}}</td>
						<td>{{@num_format($credit_sale_payment->price)}}</td>
						<td class="credit_sale_amount text-right">
							{{@num_format($credit_sale_payment->price * $credit_sale_payment->qty)}}</td>
					</tr>
					@endforeach
					@endif
					<tr>
						<td colspan="5" style="text-align: right;"><b>@lang('petro::lang.sub_total')</b></td>
						<td class="text-right">{{@num_format($credit_sale_total)}}</td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>

	<div class="clearfix"></div>
	<br>
	<div class="col-xs-12 text-center" style="font-weight: bold; maring-bottom: 0px; font-size: 18px;">
		@lang('petro::lang.expenses')
	</div>
	<div class="row">
		<div class="col-md-12">
			<table class="table table-striped">
				<thead>
					<tr>
						<th>@lang('petro::lang.expense_category' )</th>
						<th>@lang('petro::lang.reference_no' )</th>
						<th>@lang('petro::lang.reason')</th>
						<th>@lang('petro::lang.amount')</th>
					</tr>
				</thead>
				<tbody>
					@php
					$expense_total = $settlement->expense_payments->sum('amount');
					@endphp
					@if(!empty($settlement->expense_payments))
					@foreach ($settlement->expense_payments as $expense_payment)
					@php
					$expense_category = App\ExpenseCategory::where('id', $expense_payment->category_id)->first();
					@endphp
					<tr>
						<td>{{!empty($expense_category) ? $expense_category->name : ''}}</td>
						<td>{{$expense_payment->reference_no}}</td>
						<td>{{$expense_payment->reason}}</td>
						<td class="text-right">{{@num_format($expense_payment->amount)}}</td>
					</tr>
					@endforeach
					@endif
					<tr>
						<td colspan="3" style="text-align: right;"><b>@lang('petro::lang.sub_total')</b></td>
						<td class="text-right">{{@num_format($expense_total)}}</td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>


	<div class="clearfix"></div>
	<br>
	<div class="col-xs-12 text-center" style="font-weight: bold; maring-bottom: 0px; font-size: 18px;">
		@lang('petro::lang.payment_details')
	</div>
	<div class="row">
		<div class="col-md-12">
			<table class="table table-striped">
				<thead>
					<tr>
						<th>@lang('petro::lang.cash' )</th>
						<th>@lang('petro::lang.cards' )</th>
						<th>@lang('petro::lang.cheques')</th>
						<th>@lang('petro::lang.credit_sales')</th>
						<th>@lang('petro::lang.expenses')</th>
						<th>@lang('petro::lang.short')</th>
						<th>@lang('petro::lang.excess')</th>
						<th>@lang('petro::lang.total')</th>
					</tr>
				</thead>
				<tbody>

					<tr>
						{{-- 
							* @ChangedBy Afes
							* @Date 25-05-2021
							* @Task 12700 
						--}}
						<td>{{@num_format($settlement->cash_payments->sum('amount'))}}</td>
						<td>{{@num_format($settlement->card_payments->sum('amount'))}}</td>
						<td>{{@num_format($settlement->cheque_payments->sum('amount'))}}</td>
						<td>{{@num_format($settlement->credit_sale_payments->sum('amount'))}}
						</td>
						<td>{{@num_format($settlement->expense_payments->sum('amount'))}}
						</td>
						<td>{{@num_format($settlement->shortage_payments->sum('amount'))}}
						</td>
						<td>{{@num_format($settlement->excess_payments->sum('amount'))}}</td>
						<td class="text-right">
							{{@num_format(
								$settlement->cash_payments->sum('amount') + $settlement->card_payments->sum('amount') + $settlement->cheque_payments->sum('amount') + $settlement->credit_sale_payments->sum('amount') + $settlement->expense_payments->sum('amount') + $settlement->shortage_payments->sum('amount') + $settlement->excess_payments->sum('amount'))}}
						</td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>
	<br>
	<div class="col-xs-12 text-center no-print"
		style="font-weight: bold; margin-bottom: 20px !important; margin-top: 10px !important; ">
		<a style=" border-radius: 0px !important;" class="btn btn-success btn-sm btn-flat"
			href="{{action('\Modules\Petro\Http\Controllers\SettlementController@create')}}">@lang('petro::lang.back_to_settlement')</a>
	</div>
</div>