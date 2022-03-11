<div class="modal-dialog" role="document">
  <div class="modal-content">

    {!! Form::open(['url' => action('\Modules\Property\Http\Controllers\PaymentOptionController@store'), 'method' =>
    'post', 'id' => $quick_add ? 'quick_add_payment_option_form' : 'payment_option_add_form' ]) !!}

    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
          aria-hidden="true">&times;</span></button>
      <h4 class="modal-title">@lang( 'property::lang.add_payment_option' )</h4>
    </div>

    <div class="modal-body">
      <div class="row">
        <div class="form-group col-sm-12">
          {!! Form::label('date', __( 'property::lang.date' ) . ':*') !!}
          {!! Form::text('date', @format_date(date('Y-m-d')), ['class' => 'form-control', 'required', 'readonly', 'placeholder' => __(
          'property::lang.date' )]); !!}
        </div>
        <div class="form-group col-sm-12">
          {!! Form::label('payment_option', __( 'property::lang.payment_option' ) . ':*') !!}
          {!! Form::text('payment_option', null, ['class' => 'form-control', 'placeholder' => __( 'property::lang.payment_option'), 'id'
          => 'payment_option']); !!}
        </div>
        <?php $payment_option = ['Refundable'=>'Refundable', 'Non-Refundable Advance'=>'Non-Refundable Advance', 'Advance Payment'=>'Advance Payment', 'Agreement Charges'=>'Agreement Charges', 'Stamp Fees'=>'Stamp Fees', 'Notary Fees'=>'Notary Fees', 'Penalty Amount'=>'Penalty Amount']; ?>
        <!--<div class="form-group col-sm-12">
          {!! Form::label('payment_option', __( 'property::lang.payment_option' ) . ':*') !!}
          {!! Form::select('payment_option', $payment_option, null, ['placeholder' => __( 'property::lang.payment_option' ),
          'required', 'id' => 'payment_option', 'class' => 'form-control']); !!}
        </div>-->
        
        <div class="form-group col-sm-12">
          {!! Form::label('credit_account_type', __( 'property::lang.credit_account_type' ) . ':*') !!}
          {!! Form::select('credit_account_type', $credit_account_type, null, ['placeholder' => __( 'property::lang.select_account_type' ),
          'required', 'class' => 'form-control']); !!}
        </div>
        <div class="form-group col-sm-12">
          {!! Form::label('credit_sub_account_type', __( 'property::lang.credit_sub_account_type' ) . ':*') !!}
          {!! Form::select('credit_sub_account_type', $credit_sub_account_type, null, ['placeholder' => __( 'property::lang.select_sub_account_type' ),
          'required', 'class' => 'form-control']); !!}
        </div>
        <div class="form-group col-sm-12">
          {!! Form::label('credit_account', __( 'property::lang.credit_account' ) . ':*') !!}
          {!! Form::select('credit_account', $accounts, null,
            ['placeholder' => __( 'property::lang.select_account' ),
             'required',
             'class' => 'form-control',
             'disabled'=>'disabled'
              ]); !!}
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
 $('#date').datepicker('setDate', new Date());
 $('#location_id option:eq(1)').attr('selected', 'selected');
</script>