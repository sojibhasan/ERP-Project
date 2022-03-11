
<div class="modal-dialog" role="document">
  <div class="modal-content">

@php
// dd($selectedAccount);
// dd($payment_line->account_id); 

@endphp
    {!! Form::open(['url' => action('TransactionPaymentController@update', [$payment_line->id]), 'method' => 'put', 'id' => 'transaction_payment_add_form', 'files' => true ]) !!}

    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      <h4 class="modal-title">@lang( 'purchase.edit_payment' )</h4>
    </div>

    <div class="modal-body">
      <div class="row">
        @if(!empty($transaction->contact))
        <div class="col-md-4">
          <div class="well">
            <strong>@lang('purchase.supplier'): </strong>{{ $transaction->contact->name }}<br>
            <strong>@lang('business.business'): </strong>{{ $transaction->contact->supplier_business_name }}
          </div>
        </div>
        @endif
        @if($transaction->type != 'opening_balance')
        <div class="col-md-4">
          <div class="well">
            <strong>@lang('purchase.ref_no'): </strong>{{ $transaction->ref_no }}<br>
            @if(!empty($transaction->location))
              <strong>@lang('purchase.location'): </strong>{{ $transaction->location->name }}
            @endif
          </div>
        </div>
        <div class="col-md-4">
          <div class="well">
            <strong>@lang('sale.total_amount'): </strong><span class="display_currency" data-currency_symbol="true">{{ $transaction->final_total }}</span><br>
            <strong>@lang('purchase.payment_note'): </strong>
            @if(!empty($transaction->additional_notes))
            {{ $transaction->additional_notes }}
            @else
              --
            @endif
          </div>
        </div>
        @endif
      </div>
      <div class="row payment_row">
        <div class="col-md-4">
          <div class="form-group">
            {!! Form::label("amount" , __('sale.amount') . ':*') !!}
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fa fa-money"></i>
              </span>
              {!! Form::text("amount", @num_format($payment_line->amount), ['class' => 'form-control input_number', 'required', 'placeholder' => 'Amount']); !!}
            </div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="form-group">
            {!! Form::label("paid_on" , __('lang_v1.paid_on') . ':*') !!}
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fa fa-calendar"></i>
              </span>
              {!! Form::text('paid_on', @format_datetime($payment_line->paid_on), ['class' => 'form-control', 'readonly', 'required']); !!}
            </div>
          </div>
        </div>
        @php
         if($payment_line->method == 'cash'){
            $method = $payment_line->account_id;
         }else{
            $method = $payment_line->method;
         }
        @endphp
        <div class="col-md-4">
          <div class="form-group">
            {!! Form::label("method" , __('purchase.payment_method') . ':*') !!}
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fa fa-money"></i>
              </span>
              {!! Form::select("method", $payment_types, $method, ['class' => 'form-control select2 payment_types_dropdown', 'required', 'style' => 'width:100%;']); !!}
            </div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="form-group">
            {!! Form::label('document', __('purchase.attach_document') . ':') !!}
            {!! Form::file('document'); !!}
            <p class="help-block">@lang('lang_v1.previous_file_will_be_replaced')</p>
          </div>
        </div>
        @if(!empty($accounts))
        @if($transaction->type == 'expense')
        <div class="col-sm-6">
					<div class="form-group">
						{!! Form::label('expense_account', __('sale.expense_account') . ':*') !!}
						{!! Form::select('expense_account', $expense_accounts, !empty($transaction->expense_account) ? $transaction->expense_account : null, ['class' => 'form-control select2', 'placeholder' => __('lang_v1.please_select')]) !!}
						
					</div>
				</div>
        @else
          <div class="col-md-6">
            <div class="form-group">
              {!! Form::label("account_id" , __('lang_v1.payment_account') . ':') !!}
              <div class="input-group">
                <span class="input-group-addon">
                  <i class="fa fa-money"></i>
                </span>
                {!! Form::select("account_id", $accounts, !empty($payment_line->account_id) ? $payment_line->account_id : '' , ['class' => 'form-control select2', 'id' => "account_id", 'selected' => "!empty($selectedAccount) && $payment_line->account_id == $selectedAccount) && ->id ? 'selected' : '' ", 'style' => 'width:100%;']); !!}
              </div>
            </div>
          </div>
          @endif
        @endif
        
        <div class="clearfix"></div>
          @include('transaction_payment.payment_type_details')
        <div class="col-md-12">
          <div class="form-group">
            {!! Form::label("note", __('lang_v1.payment_note') . ':') !!}
            {!! Form::textarea("note", $payment_line->note, ['class' => 'form-control', 'rows' => 3]); !!}
          </div>
        </div>
      </div>
    </div>

    <div class="modal-footer">
      <button type="submit" class="btn btn-primary">@lang( 'messages.update' )</button>
      <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
    </div>

    {!! Form::close() !!}

  </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->

<script>
  @if(!empty($payment_line->cheque_date) && !empty($payment_line->method) && $payment_line->method == 'bank_transfer')
  $('.cheque_date').datepicker('setDate', '{{@format_date($payment_line->cheque_date)}}');
  @else
  $('.cheque_date').datepicker('setDate', new Date());
  @endif
  $('#paid_on').datepicker('setDate', '{{@format_date($payment_line->paid_on)}}');

</script>
