<div class="modal-dialog" role="document">
  <div class="modal-content">

    {!! Form::open(['url' => action('\Modules\Property\Http\Controllers\PaymentOptionController@update',
    $payment_option->id), 'method' =>
    'post', 'id' => 'payment_option_edit_form' ]) !!}

    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
          aria-hidden="true">&times;</span></button>
      <h4 class="modal-title">@lang( 'property::lang.edit' )</h4>
    </div>

    <div class="modal-body">
      <div class="row">
        <div class="form-group col-sm-12">
          {!! Form::label('date', __( 'property::lang.date' ) . ':*') !!}
          {!! Form::text('date', null, ['class' => 'form-control', 'required',
          'readonly', 'placeholder' => __(
          'property::lang.date' )]); !!}
        </div>
        <?php $PaymentOption = ['Non-Refundable Advance', 'Advance Payment', 'Agreement Charges', 'Stamp Fees', 'Notary Fees', 'Penalty Amount' ]; ?>
        
        @if (in_array($payment_option->payment_option, $PaymentOption))
        <div class="form-group col-sm-12">
          {!! Form::label('payment_option', __( 'property::lang.payment_option' ) . ':*') !!}
          {!! Form::text('payment_option', $payment_option->payment_option, ['class' => 'form-control', 'disabled' => true, 'placeholder' =>
          __( 'property::lang.amount'), 'id'
          => 'payment_option']); !!}
        </div>
        @else
           <div class="form-group col-sm-12">
          {!! Form::label('payment_option', __( 'property::lang.payment_option' ) . ':*') !!}
          {!! Form::text('payment_option', $payment_option->payment_option, ['class' => 'form-control',  'placeholder' =>
          __( 'property::lang.amount'), 'id'
          => 'payment_option']); !!}
        </div>
        @endif
        
        
        <div class="form-group col-sm-12">
          {!! Form::label('credit_account_type', __( 'property::lang.credit_account_type' ) . ':*') !!}
          {!! Form::select('credit_account_type', $credit_account_type, $payment_option->credit_account_type, ['placeholder' => __( 'property::lang.select_account_type' ),
          'required', 'class' => 'form-control']); !!}
        </div>
        <div class="form-group col-sm-12">
          {!! Form::label('credit_sub_account_type', __( 'property::lang.credit_sub_account_type' ) . ':*') !!}
          {!! Form::select('credit_sub_account_type', $credit_sub_account_type, $payment_option->credit_sub_account_type, ['placeholder' => __( 'property::lang.select_sub_account_type' ),
          'required', 'class' => 'form-control']); !!}
        </div>
        <div class="form-group col-sm-12">
          {!! Form::label('credit_account', __( 'property::lang.credit_account' ) . ':*') !!}
          {!! Form::select('credit_account', $accounts, $payment_option->credit_account, ['placeholder' => __( 'property::lang.select_account' ),
          'required', 'class' => 'form-control']); !!}
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
  $('#credit_sub_account_type').on('change', function() {
    console.log(this.value);
    if(this.value!=null){
      $.ajax({
        type:'PUT',
        url: `{{url('/ajax/credit_sub_account_type')}}`,
        data: {
          value: this.value
        },
        success:function(html){
          console.log(html);
          $('#credit_account').removeAttr('disabled');
          $('#credit_account').html(html);

        }
      });
    }else{
      $('#credit_account').attr('disabled','disabled');
    }
  });

  $('#date').datepicker('setDate', "{{\Carbon::parse($payment_option->date)->format('m/d/y')}}");
</script>