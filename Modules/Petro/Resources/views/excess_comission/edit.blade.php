<div class="modal-dialog" role="document" style="width:45%">
    <div class="modal-content">
  
      {!! Form::open(['url' => action('\Modules\Petro\Http\Controllers\ExcessComissionController@update', $transaction_payment->id), 'method' => 'put', 'id' => 'pay_contact_due_form', 'files' => true ]) !!}
  
      {!! Form::hidden("pump_operator_id", $pump_operator_id); !!}
      {!! Form::hidden("due_payment_type", 'pay_excess_and_commission'); !!}
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">@lang( 'petro::lang.pay_excess_and_commission' )</h4>
      </div>
  
      <div class="modal-body">
        <div class="row">
          <div class="col-md-4">
            <div class="well">
              <strong>@lang('petro::lang.pump_operator'): </strong>{{ $pump_operator_details->name }}<br>
            </div>
          </div>
          <div class="col-md-4">
            <div class="well">
              <strong>@lang('petro::lang.location'): </strong>{{ $pump_operator_details->location_name }}<br>
            </div>
          </div>
          <div class="col-md-4">
            <div class="well">
              <strong>@lang('petro::lang.commission_amount'): </strong>{{ $pump_operator_details->commission_ap }}<br>
              <strong>@lang('petro::lang.excess_amount'): </strong>{{ $total_excess }}<br>
            </div>
          </div>
         
        </div>
        <div class="row payment_row">
        <input type="hidden" name="transaction_payment_id" value="{{$transaction_payment->id}}">
          <div class="col-md-4">
            <div class="form-group">
              {!! Form::label("payment_ref_no" , __('petro::lang.payment_ref_no') . ':*') !!}
              <div class="input-group">
                <span class="input-group-addon">
                  <i class="fa fa-money"></i>
                </span>
                {!! Form::text("payment_ref_no", !empty($transaction_payment) ? $transaction_payment->payment_ref_no : null, ['class' => 'form-control input_number', 'required', 'readonly', 'placeholder' => __('petro::lang.payment_ref_no')]); !!}
              </div>
            </div>
          </div>
          <div class="col-md-4">
            <div class="form-group">
              {!! Form::label("amount" , __('sale.amount') . ':*') !!}
              <div class="input-group">
                <span class="input-group-addon">
                  <i class="fa fa-money"></i>
                </span>
                {!! Form::text("amount", !empty($transaction_payment) ? $transaction_payment->amount : null, ['class' => 'form-control input_number', 'required', 'placeholder' => 'Amount']); !!}
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
                {!! Form::text('paid_on', date('m/d/Y'), ['class' => 'form-control', 'readonly', 'required']); !!}
              </div>
            </div>
          </div>
          <div class="col-md-4">
            <div class="form-group">
              {!! Form::label("method" , __('purchase.payment_method') . ':*') !!}
              <div class="input-group">
                <span class="input-group-addon">
                  <i class="fa fa-money"></i>
                </span>
                {!! Form::select("method", $payment_types, !empty($transaction_payment) ?
                $transaction_payment->method : null, ['class' => 'form-control select2 payment_types_dropdown', 'required', 'style' => 'width:100%;']); !!}
              </div>
            </div>
          </div>
          <div class="col-md-4 account_module hide">
            <div class="form-group">
              {!! Form::label("account_id" , __('petro::lang.accounts') . ':') !!}
              <div class="input-group">
                <span class="input-group-addon">
                  <i class="fa fa-money"></i>
                </span>
                {!! Form::select("account_id", $accounts, !empty($transaction_payment) ?
                $transaction_payment->account_id : null , ['class' => 'form-control select2', 'id' => "account_id", 'style' => 'width:100%;']); !!}
              </div>
            </div>
          </div>
          <div class="clearfix"></div>
          <div class="col-md-6">
            <div class="form-group">
              {!! Form::label("account_types" , __('petro::lang.account_types') . ':') !!}
              <div class="input-group">
                <span class="input-group-addon">
                  <i class="fa fa-money"></i>
                </span>
                {!! Form::select("account_types", $account_types,  null , ['class' => 'form-control select2', 'id' => "account_types", 'style' => 'width:100%;']); !!}
              </div>
            </div>
          </div>
          @if(!empty($accounts))
            <div class="col-md-6">
              <div class="form-group">
                {!! Form::label("double_entry_account_id" , __('petro::lang.double_entry_account') . ':') !!}
                <div class="input-group">
                  <span class="input-group-addon">
                    <i class="fa fa-money"></i>
                  </span>
                  {!! Form::select("double_entry_account_id", $accounts, !empty($transaction_payment) ?
                  $transaction_payment->double_entry_account : null , ['class' => 'form-control select2', 'id' => "double_entry_account_id", 'style' => 'width:100%;']); !!}
                </div>
              </div>
            </div>
          @endif
          <div class="clearfix"></div>
          <div class="col-md-4">
            <div class="form-group">
              {!! Form::label('document', __('purchase.attach_document') . ':') !!}
              {!! Form::file('document'); !!}
            </div>
          </div>
  
            @include('transaction_payment.advance_payment_type_details')
          <div class="col-md-12">
            <div class="form-group">
              {!! Form::label("note", __('lang_v1.payment_note') . ':') !!}
              {!! Form::textarea("note", null, ['class' => 'form-control', 'rows' => 3]); !!}
            </div>
          </div>
        </div>
      </div>
  
      <div class="modal-footer">
        <button type="submit" class="btn btn-primary">@lang( 'messages.save' )</button>
        <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
      </div>
  
      {!! Form::close() !!}
  
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->

  <script>
    $('#paid_on').datepicker();
    $('.select2').select2();
    $('#method').trigger('change');
   

    $('.pump_operator_modal ').on('shown.bs.modal', function (e) {
    //   $('#account_types').trigger('change');
      $('#method').trigger('change');
    })

    $('#account_types').change(function () {
      let account_type = $(this).val();
      $.ajax({
        method: 'get',
        url: '/accounting-module/journals/get-account-dropdown-by-type/'+account_type,
        data: {  },
        contentType: 'html',
        success: function(result) {
          $('#double_entry_account_id').html(result);
          
        },
      });
    })
  </script>