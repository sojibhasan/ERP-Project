<div class="modal-dialog" role="document">
  <div class="modal-content">

    {!! Form::open(['url' => action('TransactionPaymentController@postRefundPayment', $contact_id), 'method' => 'post',
    'id' => 'pay_contact_due_form', 'files' => true ]) !!}

    {!! Form::hidden("contact_id", $contact_details->contact_id); !!}
    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
          aria-hidden="true">&times;</span></button>
      <h4 class="modal-title">@lang( 'lang_v1.refund_cheque_return' )</h4>
    </div>

    <div class="modal-body">
      <div class="row">
        <div class="col-md-6">
          <div class="well">
            @if($contact_details->type == 'customer')
            <strong>@lang('lang_v1.customer'):
              @else
              <strong>@lang('lang_v1.supplier'):
                @endif
              </strong>{{ $contact_details->name }}<br>
          </div>
        </div>
        <input type="hidden" name="type" value="advance_payment">
      </div>
      <div class="row payment_row">
        <div class="col-md-4">
          <div class="form-group">
            {!! Form::label("amount" , __('lang_v1.type') . ':*') !!}
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fa fa-money"></i>
              </span>
              {!! Form::select("type", ['refund' => __('lang_v1.refund'), 'cheque_return' =>
              __('lang_v1.cheque_return')], null , ['class' => 'form-control input_number', 'required', 'placeholder' =>
              __('lang_v1.please_select'), 'id' => 'type']); !!}
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
              {!! Form::text("amount", null, ['class' => 'form-control input_number', 'data-rule-min-value' => 0,
              'data-msg-min-value' => __('lang_v1.negative_value_not_allowed'), 'required', 'placeholder' => 'Amount']);
              !!}
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
              {!! Form::text( "paid_on", null, ['class' => 'form-control paid_on_date', 'placeholder' => __('lang_v1.paid_on') , 'id' => 'paid_on_date']); !!}
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
              {!! Form::select("method", $payment_types, null, ['class' => 'form-control select2
              payment_types_dropdown_refund', 'required', 'style' => 'width:100%;', 'id' => 'method']); !!}
            </div>
          </div>
        </div>
        <div class="col-md-4 cheque_return_charges_div hide">
          <div class="form-group">
            {!! Form::label("cheque_bank",__('lang_v1.bank')) !!}
            {!! Form::select("cheque_bank", $cheque_banks, null, ['class' => 'form-control select2
            cheque_bank', 'required', 'placeholder' => __('lang_v1.please_select')]); !!}
          </div>
        </div>
        <div class="col-md-4 cheque_return_charges_div hide">
          <div class="form-group">
            {!! Form::label("cheque_number_return",__('lang_v1.cheque_number')) !!}
            {!! Form::select("cheque_number_return", $cheque_array, null, ['class' => 'form-control select2
            cheque_number_return', 'required', 'placeholder' => __('lang_v1.please_select')]); !!}
          </div>
        </div>
        <div class="col-md-6 sale_invoice_bill_number_div hide">
          <div class="form-group">
            {!! Form::label("sale_invoice_bill_number",__('lang_v1.sale_invoice_bill_number')) !!}
            {!! Form::select("sale_invoice_bill_number", $invoices, null, ['class' => 'form-control select2
            sale_invoice_bill_number', 'required', 'placeholder' => __('lang_v1.please_select')]); !!}
          </div>
        </div>
        <div class="clearfix"></div>
        <div class="col-md-4">
          <div class="form-group">
            {!! Form::label('document', __('purchase.attach_document') . ':') !!}
            {!! Form::file('document'); !!}
          </div>
        </div>

        <div class="col-md-6 account_id_div hide @if(empty($accounts)) hide  @endif">
          <div class="form-group">
            {!! Form::label("account_id" , __('lang_v1.payment_account') . ':') !!}
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fa fa-money"></i>
              </span>
              {!! Form::select("account_id", $accounts, $customer_deposit_account_id , ['class' => 'form-control
              select2', 'placeholder' => __('lang_v1.please_select'), 'id' => "account_id", 'readonly','style' =>
              'width:100%;']); !!}
            </div>
          </div>
        </div>

        <div class="clearfix"></div>

        @include('transaction_payment.refund_payment_type_details')
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
  $('#pay_contact_due_form').validate();

      $('#payment_type').change(function(){
        if($(this).val() == 'advance_payment'){
          $('.account_id_div').addClass('hide');
        }else{
          $('.account_id_div').removeClass('hide');
        }
      })

    $('.paid_on_date').daterangepicker('setDate', new Date());
    $('.transfer_date').datepicker('setDate', new Date());
    $('.cheque_date').datepicker('setDate', new Date());

    $('#type').change(function(){
        if($(this).val() === 'refund'){
            $('.cheque_div').addClass('hide');
            $('.bank_name_div').addClass('hide');
            $('.cheque_return_charges_div').addClass('hide');
            $('.sale_invoice_bill_number_div').removeClass('hide');
            $('.bank_name_div').addClass('hide');
            $('.bank_name_text_div').addClass('hide');
            $('#amount').attr('readonly', false);
            $('.payment_types_dropdown_refund').attr('disabled', false);
        }else if($(this).val() === 'cheque_return'){
            $('.cheque_div').removeClass('hide');
            $('.bank_name_div').removeClass('hide');
            $('.cheque_return_charges_div').removeClass('hide');
            $('.sale_invoice_bill_number_div').addClass('hide');
            $('.bank_name_div').addClass('hide');
            $('.bank_name_text_div').removeClass('hide');
            $('#amount').attr('readonly', true);
            $('.payment_types_dropdown_refund').attr('disabled', true);
            $('.payment_types_dropdown_refund').val('bank_transfer').trigger('change');
        }else{
            $('.cheque_div').addClass('hide');
            $('.bank_name_div').addClass('hide');
            $('.cheque_return_charges_div').addClass('hide');
            $('.sale_invoice_bill_number_div').addClass('hide');
            $('.bank_name_div').addClass('hide');
            $('.bank_name_text_div').addClass('hide');
            $('#amount').attr('readonly', false);
            $('.payment_types_dropdown_refund').attr('disabled', false);
        }
    })

    $(document).on('change', '#cheque_number_return', function () {
      let payment_id = $(this).val();

        $.ajax({
          method: 'get',
          url: '/payments/get-payment-details-by-id/'+payment_id,
          data: {  },
          success: function(result) {
            __write_number($('#amount'), result.amount);
            $('#bank_name_text').val(result.bank_name);

        },
      });
    });

    $(document).on('change', '#cheque_bank', function () {
      let bank_id = $('#cheque_bank').val();

      $.ajax({
        method: 'get',
        url: '/payments/get-cheque-dropdown-by-bank-id/'+bank_id+'/{{ $contact_details->id }}',
        data: {  },
        contentType: 'html',
        success: function(result) {
            
          //$('#cheque_number_return').empty().append(result);
          $('#cheque_number_return').html(result);

        },
      });
    });
    $(document).on('change', '.payment_types_dropdown_refund', function () {
        var payment_type = $(this).val();
        var to_show = null;
        var cheque_field = null;
        $(this)
            .closest('.payment_row')
            .find('.payment_details_div_refund')
            .each(function () {
                if ($(this).attr('data-type') == 'cheque') {
                    cheque_field = $(this);
                    $('.bank_name_div').removeClass('hide');
                }
                if ($(this).attr('data-type') == payment_type) {
                    to_show = $(this);
                } else {
                    if (!$(this).hasClass('hide')) {
                        $(this).addClass('hide');
                    }
                }
            });
        if (to_show && to_show.hasClass('hide')) {
            to_show.removeClass('hide');
            to_show.find('input').filter(':visible:first').focus();
        }

        $('.bank_name_div').removeClass('hide');
        if (payment_type == 'bank_transfer' || payment_type == 'cheque' || payment_type == 'cash') {
          if($('#type').val() == 'cheque_return'){
            $('.bank_name_div').addClass('hide');
          }
        }

        if (payment_type == 'bank_transfer') {
            $.ajax({
                method: 'get',
                url: '/accounting-module/get-account-group-name-dp',
                data: { group_name: 'Bank Account' },
                contentType: 'html',
                success: function (result) {
                    $('#account_id').empty().append(result);
                    $('#cheque_bank').empty().append(result);
                },
            });
        }
        if (payment_type == 'cheque') {
            $.ajax({
                method: 'get',
                url: '/accounting-module/get-account-dp?type=' + payment_type,
                data: {},
                contentType: 'html',
                success: function (result) {
                    $('#account_id').empty().append(result);
                    $('#account_id')
                        .val($('#account_id option:contains("Cheques in Hand")').val())
                        .trigger('change');
                },
            });
        }
        if (payment_type == 'cash') {
          $('.bank_name_text_div').addClass('hide');
            $.ajax({
                method: 'get',
                url: '/accounting-module/get-account-dp?type=' + payment_type,
                data: {},
                contentType: 'html',
                success: function (result) {
                    $('#account_id').empty().append(result);
                    $('#account_id')
                        .val($('#account_id option:contains("Cash")').val())
                        .trigger('change');
                },
            });
        }
    
    });
</script>