@php
if(!isset($payment)){
$payment = json_encode([]);
}
@endphp

<div class="row">
	<input type="hidden" class="payment_row_index" value="{{ $row_index}}">
	@php
	$col_class = 'col-md-6';
	if(!empty($accounts)){
	$col_class = 'col-md-4';
	}
	@endphp
	<div class="{{$col_class}}">
		<div class="form-group">
			{!! Form::label("amount_$row_index" ,__('sale.amount') . ':*') !!}
			<div class="input-group">
				<span class="input-group-addon">
					<i class="fa fa-money"></i>
				</span>
				{!! Form::text("payment[$row_index][amount]", !empty($payment->amount)?rtrim(rtrim($payment->amount,
				'0'), '.'): rtrim(rtrim($payment_line['amount'], '0'), '.'), ['class' => 'form-control payment-amount
				input_number', 'required', 'id' => "amount_$row_index", 'placeholder' => __('sale.amount')]); !!}
			</div>
		</div>
	</div>
	@php
	$method = '';
		if(!empty($payment->method)){
			if($payment->method == 'cash'){
				$method = App\AccountTransaction::where('transaction_payment_id', $payment->id)->orderBy('id', 'desc')->first()->account_id;
			}else{
				$method = $payment->method;
			}
		}else{
			$method = 'credit_expense';
		}

		//if expense for property
		if(!empty($property)){
			$method = App\Account::where('business_id', request()->session()->get('business.id'))->where('name', 'Cash')->first()->id;
		}
	@endphp
	<div class="{{$col_class}}">
		<div class="form-group">
			{!! Form::label("method_$row_index" , __('lang_v1.payment_method') . ':*') !!}
			<div class="input-group">
				<span class="input-group-addon">
					<i class="fa fa-money"></i>
				</span>
				{!! Form::select("payment[$row_index][method]", $payment_types, $method, ['class' => 'form-control payment_types_dropdown select2', 'required', 'id'
				=> "method_$row_index", 'disabled' => true, 'style' => 'width:100%;', 'placeholder' =>
				__('messages.please_select')]); !!}
			</div>
		</div>
	</div>
	<div class="{{$col_class}} hide account_list">
		<div class="form-group">
			{!! Form::label("account_$row_index" , __('lang_v1.payment_account') . ':') !!}
			<div class="input-group">
				<span class="input-group-addon">
					<i class="fa fa-money"></i>
				</span>
				{!! Form::select("payment[$row_index][account_id]", $accounts,
				!empty($payment->account_id)?$payment->account_id:(!empty($payment_line['account_id']) ?
				$payment_line['account_id'] : '') , ['class' => 'form-control select2', 'disabled' => false, 'id' =>
				"account_id", 'style' => 'width:100%;']); !!}
			</div>
		</div>
	</div>
	<div class="{{$col_class}} hide controller_account_div">
		<div class="form-group">
			{!! Form::label("controller_account$row_index" , __('lang_v1.controller_account') . ':') !!}
			<div class="input-group">
				<span class="input-group-addon">
					<i class="fa fa-money"></i>
				</span>
				{!! Form::select("payment[$row_index][controller_account]", $current_liabilities_accounts,
				null , ['class' => 'form-control select2', 'disabled' => $account_module ? false : true, 'id' =>
				"controller_account", 'placeholder' => __('lang_v1.please_select'), 'style' => 'width:100%;']); !!}
			</div>
		</div>
		@if (!$account_module)
		<p class="text-red controller_account_msg">@lang('lang_v1.controller_account_msg')</p>
		@endif
	</div>
	<div class="clearfix"></div>
	@include('sale_pos.partials.payment_type_details', ['payment' => $payment])
	<div class="col-md-12">
		<div class="form-group">
			{!! Form::label("note_$row_index", __('sale.payment_note') . ':') !!}
			{!! Form::textarea("payment[$row_index][note]", !empty($payment->note)?$payment->note:$payment_line['note'],
			['class' => 'form-control', 'rows' => 3, 'id' => "note_$row_index"]); !!}
		</div>
	</div>
	<div class="row">
		<div class="col-sm-12">
			<div class="pull-right" style="padding-right: 10px;"><strong>@lang('purchase.payment_due'):</strong> <span
					id="payment_due">{{!empty($temp_data->final_total)?$temp_data->final_total:0.00}}</span></div>
		</div>
	</div>
</div>