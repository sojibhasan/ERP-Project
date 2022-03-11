@php
if(!isset($payment)){
$payment = json_encode([]);
}

@endphp

<div class="row">
	<input type="hidden" class="payment_row_index" value="{{ $row_index}}">
	<input type="hidden" class="payment_id" name="payment[{{$row_index}}][payment_id]" value="{{ $payment->id}}">
	@php
	$col_class = 'col-md-6';
	@endphp
	<div class="{{$col_class}}">
		<div class="form-group">
			{!! Form::label("amount_$row_index" ,__('sale.amount') . ':*') !!}
			<div class="input-group">
				<span class="input-group-addon">
					<i class="fa fa-money"></i>
				</span>
				{!! Form::text("payment[$row_index][amount]", $payment->amount, ['class' => 'form-control payment-amount input_number',
				'required', 'id' => "amount_$row_index", 'placeholder' => __('sale.amount')]); !!}
			</div>
		</div>
	</div>
	@php
		$method = '';
		if(!empty($payment->method)){
			if($payment->method == 'cash'){
				$method = $payment->account_id;
			}else{
				$method = $payment->method;
			}
		}else{
			$method = $cash_account_id;
		}
	@endphp
	<div class="{{$col_class}}">
		<div class="form-group">
			{!! Form::label("method_$row_index" , __('lang_v1.payment_method') . ':*') !!}
			<div class="input-group">
				<span class="input-group-addon">
					<i class="fa fa-money"></i>
				</span>
				{!! Form::select("payment[$row_index][method]", $payment_types, $method, ['class' => 'form-control method
				payment_types_dropdown select2', 'required', 'id' => "method_$row_index", 'style' => 'width:100%;',
				'placeholder' => __('messages.please_select')]); !!}
			</div>
		</div>
	</div>
	{{$payment->account_id}}

<div class="{{$col_class}} hide  account_module">
	<div class="form-group">
		{!! Form::label("account_$row_index" , __('lang_v1.bank_account') . ':') !!}
		<div class="input-group">
			<span class="input-group-addon">
				<i class="fa fa-money"></i>
			</span>
			{!! Form::select("payment[$row_index][account_id]", $bank_group_accounts, $payment->account_id , ['class' => 'form-control account_id
			select2', 'placeholder' => __('lang_v1.please_select'), 'id' => "account_$row_index", 'style' => 'width:100%;']); !!}
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