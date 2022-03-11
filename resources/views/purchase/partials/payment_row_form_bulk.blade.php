@php
if(!isset($payment)){
$payment = json_encode([]);
}

@endphp

<div class="row">
    <input type="hidden" class="payment_row_index" value="{{ $row_index}}">
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
                {!! Form::text("payment[$row_count][$row_index][amount]", @num_format(!empty($payment->amount) ?
                $payment->amount : 0), ['class' => 'form-control payment-amount input_number', 'data-row_count' => $row_count,
                'required', 'id' => "amount_$row_index", 'placeholder' => __('sale.amount')]); !!}
            </div>
        </div>
    </div>
    <div class="{{$col_class}}">
        <div class="form-group">
            {!! Form::label("method_$row_index" , __('lang_v1.payment_method') . ':*') !!}
            <div class="input-group">
                <span class="input-group-addon">
                    <i class="fa fa-money"></i>
                </span>
                {!! Form::select("payment[$row_count][$row_index][method]", $payment_types, !empty($payment->method) ?
                $payment->method: 'cash', ['class' => 'form-control
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
                {!! Form::select("payment[$row_count][$row_index][account_id]", $bank_group_accounts,
                !empty($payment->account_id) ? $payment->account_id : null , ['class' => 'form-control
                select2', 'placeholder' => __('lang_v1.please_select'), 'id' => "account_$row_index", 'style' =>
                'width:100%;']); !!}
            </div>
        </div>
    </div>

    <div class="clearfix"></div>
    @include('purchase.partials.payment_type_details_bulk', ['row_count' => $row_count,'payment' => $payment])
    <div class="col-md-12">
        <div class="form-group">
            {!! Form::label("note_$row_index", __('sale.payment_note') . ':') !!}
            {!! Form::textarea("payment[$row_count][$row_index][note]", !empty($payment->note)?$payment->note:$payment_line['note'],
            ['class' => 'form-control', 'rows' => 3, 'id' => "note_$row_index"]); !!}
        </div>
    </div>
</div>