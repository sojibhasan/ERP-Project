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
				{!! Form::text("payment[$row_index][amount]", @num_format(!empty($payment->amount)?str_replace(',', ''
				,$payment->amount):$payment_line['amount']), ['class' => 'form-control payment-amount input_number',
				'required', 'id' => "amount_$row_index", 'placeholder' => __('sale.amount')]); !!}
			</div>
		</div>
	</div>
	@php
		$method = 'credit_purchase';
		if(!empty($payment->amount)){
			if($payment->amount > 0){
				if($payment->method == 'cash'){
					$method = $payment->account_id;
				}else{
					$method = $payment->method;
				}
			}
		}
	@endphp
	<div class="{{$col_class}}">
		<div class="form-group">
			{!! Form::label("method_$row_index" , __('lang_v1.payment_method') . ':*') !!}
			<div class="input-group">
				<span class="input-group-addon">
					<i class="fa fa-money"></i>
				</span>
				{!! Form::select("payment[$row_index][method]", $payment_types, $method, ['class' => 'form-control
				payment_types_dropdown select2', 'required', 'id' => "method_$row_index", 'style' => 'width:100%;',
				'placeholder' => __('messages.please_select')]); !!}
			</div>
		</div>
	</div>

	<div class="{{$col_class}} hide  account_module">
		<div class="form-group">
			{!! Form::label("account_$row_index" , __('lang_v1.bank_account') . ':') !!}
			<div class="input-group">
				<span class="input-group-addon">
					<i class="fa fa-money"></i>
				</span>
				{!! Form::select("payment[$row_index][account_id]", $bank_group_accounts, !empty($payment->account_id)? $payment->account_id : $payment_line['account_id'] ?? null , ['class' =>
				'form-control account_id
				select2', 'placeholder' => __('lang_v1.please_select'), 'id' => "account_id", 'style' =>
				'width:100%;']); !!}
			</div>
		</div>
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
</div>