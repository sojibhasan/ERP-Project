<div class="modal-dialog" role="document">

  <div class="modal-content">



    {!! Form::open(['url' => action('TransactionPaymentController@postSecurityDeposit', $contact_id), 'method' =>

    'post', 'id' => 'add_security_deposit_form', 'files' => true ]) !!}



    {!! Form::hidden("contact_id", $contact_details->contact_id, ['id' => 'contact_id']); !!}

    <div class="modal-header">

      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span

          aria-hidden="true">&times;</span></button>

      <h4 class="modal-title">@lang( 'lang_v1.security_deposit' )</h4>

    </div>

    <input type="hidden" name="type" value="security_deposit">

    <div class="modal-body">

      <div class="row">

        <div class="col-md-6">

          <div class="well">

            <strong>@lang('lang_v1.customer'): </strong>{{ $contact_details->name }}<br>

          </div>

        </div>

        <div class="col-md-6">

          @if(!empty($security_deposit_already))

          <button type="button" class="btn btn-flat btn-danger pull-right" id="refund_btn">@lang('lang_v1.refund')</button>

          @endif 

        </div>

      </div>

      <input type="hidden" name="refund_transaction_id" id="refund_transaction_id" value="@if(!empty($security_deposit_already)){{$security_deposit_already->id}}@endif">

      <div class="row payment_row">

        <div class="col-md-4">

          <div class="form-group">

            {!! Form::label("location_id" , __('purchase.business_location') . ':*') !!}

            <div class="input-group">

              <span class="input-group-addon">

                <i class="fa fa-location-arrow"></i>

              </span>

              {!! Form::select("location_id", $business_locations, $business_location_id, ['class' => 'form-control

              select2 location_id', 'required', 'style' => 'width:100%;', 'placeholder' =>

              __('lang_v1.please_select')]); !!}

            </div>

          </div>

        </div>

        <div class="col-md-4">

          <div class="form-group">

            {!! Form::label("payment_ref_no" , __('lang_v1.ref_no') . ':*') !!}

            <div class="input-group">

              <span class="input-group-addon">

                <i class="fa fa-link"></i>

              </span>

              {!! Form::text("payment_ref_no", $payment_ref_no, ['class' => 'form-control

              payment_ref_no', 'readonly', 'style' => 'width:100%;', 'placeholder' =>

              __('lang_v1.ref_no')]); !!}

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

              {!! Form::text("amount", null, ['class' => 'form-control input_number', 'required', 'placeholder' =>

              'Amount']); !!}

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

              {!! Form::text("paid_on", @format_datetime($contact_details->transaction_date), ['class' => 'form-control', 'readonly', 'required']); !!}

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

              payment_types_dropdown', 'required', 'style' => 'width:100%;']); !!}

            </div>

          </div>

        </div>

        <div class="col-md-6 account_id_div">

          <div class="form-group">

            {!! Form::label("account_id" , __('lang_v1.payment_account') . ':') !!}

            <div class="input-group">

              <span class="input-group-addon">

                <i class="fa fa-money"></i>

              </span>

              {!! Form::select("account_id", $accounts, $customer_deposit_account_id , ['class' => 'form-control

              select2', 'placeholder' => __('lang_v1.please_select'), 'id' => "account_id", 'style' => 'width:100%;']);

              !!}

            </div>

          </div>

        </div>

        <div class="col-md-6 account_id_div">

          <div class="form-group">

            {!! Form::label("current_liability_account" , __('lang_v1.current_liability_account') . ':') !!}

            <div class="input-group">

              <span class="input-group-addon">

                <i class="fa fa-money"></i>

              </span>

              {!! Form::select("current_liability_account", $current_libility_accounts, $current_libility_account_id ,

              ['class' => 'form-control

              select2', 'placeholder' => __('lang_v1.please_select'), 'id' => "current_liability_account", 'style' =>

              'width:100%;', $disabled]);

              !!}

            </div>

          </div>

          {!!$message!!}

        </div>

        <div class="clearfix"></div>

        <div class="col-md-4">

          <div class="form-group">

            {!! Form::label('document', __('purchase.attach_document') . ':') !!}

            {!! Form::file('document'); !!}

          </div>

        </div>



        <div class="clearfix"></div>



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

  $('#add_security_deposit_form').validate();

    $('.payment_types_dropdown').trigger('change');



    $(document).on('change', '.location_id', function(){

        let location_id = $(this).val();

        $.ajax({

          method: 'get',

          url: "/payments/get-payment-method-by-location-id/"+location_id,

          data: {  },

          contentType: 'html',

          success: function(result) {

            if(result){

              $('#method').empty().append(result);

              $('#method option:eq(0)').prop('selected', 'selected');

              $('.payment_types_dropdown').trigger('change');

            }

          },

        });

      })

    $(document).on('click', '#refund_btn', function(){

      $('#add_security_deposit_form').validate();

      if($('#add_security_deposit_form').valid()){

        let refund_transaction_id = $('#refund_transaction_id').val();

        let contact_id = {{$contact_id}};

        

        $.ajax({

          method: 'post',

          url: "/payments/refund-security-deposit/"+contact_id,

          data: { 

            refund_transaction_id : refund_transaction_id, 

            amount: $('#amount').val(), 

            payment_ref_no: 'R' +$('#payment_ref_no').val(),

            account_id: $('#account_id').val(), 

            current_liability_account: $('#current_liability_account').val(), 

            paid_on: $('#paid_on').val(), 

            method: $('#method').val(), 

            

          },

          success: function(result) {

            if(result.success === 1){

              toastr.success(result.msg)

              $('.pay_contact_due_modal').modal('hide')

            }else{

              toastr.error(result.msg)

            }

          },

        });



      }

      })

</script>