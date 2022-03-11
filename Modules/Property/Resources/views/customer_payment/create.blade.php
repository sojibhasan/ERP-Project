@extends('layouts.'.$layout)
@section('content')
<style>
  #key_pad input {
    border: none
  }

  #key_pad button {
    height: 70px;
    width: 70px;
    font-size: 30px;
    margin: 2px 1px;
    border: none !important;
    /* padding: 50px 50px; */
    color: #fff;
  }

  :focus {
    outline: 0 !important
  }

  #key_pad .screen {
    width: 100px;
    height: 36px;
    border-radius: 3px;
    padding: 10px;
    margin: 2px 24px;
    font-size: 15px;
    font-weight: 700;
    background: #8e9eab
  }

  #key_pad .small {
    color: #fff;
    font-weight: 700
  }
</style>
<!-- Content Header (Page header) -->
<section class="content-header">
  <h2 style="margin-top:0px;margin-bottom:0px; ">{{ __('property::lang.customer_poayment_dashboard') }}
  </h2>
</section>
<section class="content no-print">
  <form name="calculator">
    <div class="container">
      <div class="clearfix"></div>
      <br>
      <div class="row">
        <div class="col-md-12 col-lg-12 ">
          <div class="row">
            <div class="col-sm-3">
              <div class="form-group">
                {!! Form::label('customer_id', __('property::lang.customer').':*') !!}
                {!! Form::select('customer_id', $customers, null, ['class' => 'form-control reset
                select2 customer_id', 'placeholder' => __('messages.please_select'), 'required', 'id' =>
                'customer_id']); !!}
              </div>
            </div>
            <div class="col-sm-3">
              <div class="form-group">
                {!! Form::label('property_id', __('property::lang.property').':*') !!}
                {!! Form::select('property_id', $land_and_blocks, null, ['class' => 'form-control reset
                select2 property_id', 'placeholder' => __('messages.please_select'), 'required', 'id' =>
                'property_id']); !!}
              </div>
            </div>
            <div class="col-sm-3">
              <div class="form-group">
                {!! Form::label('installment', __('property::lang.original_amount').':*') !!}
                {!! Form::text('original_amount', null, ['class' => 'form-control reset
                original_amount', 'required', 'id' =>
                'original_amount']); !!}
              </div>
            </div>
            <div class="col-sm-3">
              <div class="form-group">
                {!! Form::label('installment', __('property::lang.installment').':*') !!}
                {!! Form::text('installment', null, ['class' => 'form-control reset
                installment', 'required', 'id' =>
                'installment']); !!}
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="clearfix"></div>
      <br>
      <br>
      <br>
      <div class="row">
        <div class="col-md-12 col-lg-12">
          <div class="col-md-4 col-lg-4 ">
            <div class="row">
              <div class="col-md-6">
                {!! Form::label('overdue_amount', __('property::lang.overdue_amount').':*') !!}
              </div>
              <div class="col-md-6">
                {!! Form::text('overdue_amount', null, ['class' => 'form-control reset
                overdue_amount', 'placeholder' => __('property::lang.overdue_amount'), 'required', 'id' =>
                'overdue_amount']); !!}
              </div>
            </div>
            <br>
            <div class="row">
              <div class="col-md-6">
                {!! Form::label('penalty_amount', __('property::lang.penalty_amount').':*') !!}
              </div>
              <div class="col-md-6">
                {!! Form::text('penalty_amount', null, ['class' => 'form-control reset
                penalty_amount', 'placeholder' => __('property::lang.penalty_amount'), 'required', 'id' =>
                'penalty_amount']); !!}
              </div>
            </div>
            <br>
            <div class="row">
              <div class="col-md-6">
                {!! Form::label('total_overdue', __('property::lang.total_overdue').':*') !!}
              </div>
              <div class="col-md-6">
                {!! Form::text('total_overdue', null, ['class' => 'form-control reset
                total_overdue', 'placeholder' => __('property::lang.total_overdue'), 'required', 'id' =>
                'total_overdue']); !!}
              </div>
            </div>
            <br>
            <div class="row">
              <div class="col-md-6">
                {!! Form::label('total_due', __('property::lang.total_due').':*') !!}
              </div>
              <div class="col-md-6">
                {!! Form::text('total_due', null, ['class' => 'form-control reset
                total_due', 'placeholder' => __('property::lang.total_due'), 'required', 'id' =>
                'total_due']); !!}
              </div>
            </div>
            <br>
            <div class="row">
              <div class="col-md-6">
                {!! Form::label('pay_amount', __('property::lang.pay_amount').':*') !!}
              </div>
              <div class="col-md-6">
                {!! Form::text('pay_amount', null, ['class' => 'form-control reset
                pay_amount', 'placeholder' => __('property::lang.pay_amount'), 'required', 'id' =>
                'pay_amount']); !!}
              </div>
            </div>

          </div>
          <div id="key_pad" class="row col-md-6 text-center">
            <div class="row">
              <button id="7" type="button" class="btn btn-primary btn-sm" onclick="enterVal(this.id)">7</button>
              <button id="8" type="button" class="btn btn-primary btn-sm" onclick="enterVal(this.id)">8</button>
              <button id="9" type="button" class="btn btn-primary btn-sm" onclick="enterVal(this.id)">9</button>

            </div>
            <div class="row">
              <button id="4" type="button" class="btn btn-primary btn-sm" onclick="enterVal(this.id)">4</button>
              <button id="5" type="button" class="btn btn-primary btn-sm" onclick="enterVal(this.id)">5</button>
              <button id="6" type="button" class="btn btn-primary btn-sm" onclick="enterVal(this.id)">6</button>
            </div>
            <div class="row">
              <button id="1" type="button" class="btn btn-primary btn-sm" onclick="enterVal(this.id)">1</button>
              <button id="2" type="button" class="btn btn-primary btn-sm" onclick="enterVal(this.id)">2</button>
              <button id="3" type="button" class="btn btn-primary btn-sm" onclick="enterVal(this.id)">3</button>
            </div>
            <div class="row">
              <button id="backspace" type="button" class="btn btn-danger" onclick="enterVal(this.id)">⌫</button>
              <button id="0" type="button" class="btn btn-primary btn-sm" onclick="enterVal(this.id)">0</button>
              <button id="precision" type="button" class="btn btn-success" onclick="enterVal(this.id)">.</button>
            </div>
          </div>


          <div class="col-md-2 col-lg-2">
            <div class="row">
              <a href="{{action('\Modules\Property\Http\Controllers\SaleAndCustomerPaymentController@dashboard')}}"><input
                  value="Dashboard" class="btn btn-flat btn-lg btn-block" style="color: #fff;background-color:#810040;"
                  type="button" />
              </a>
              <br /><br />
              <button value="save" id="submit" name="submit" class="btn btn-flat btn-lg btn-block"
                style="color: #fff; background-color:#2874A6;" type="button">@lang('lang_v1.save') </button>
              <br /><br />
              <span onclick="reset()">
                <button type="button" class="btn btn-flat btn-lg btn-block"
                  style="color: #fff; background-color:#CC0000;" type="button"> <i class="fa fa-refresh"
                    aria-hidden="true"></i> @lang('petro::lang.cancel') </button>
              </span>
              <br>
              <br>
              <a href="{{action('Auth\PumpOperatorLoginController@logout')}}"
                class="btn btn-flat btn-block btn-lg pull-right"
                style=" background-color: orange; color: #fff;">@lang('petro::lang.logout')</a>
            </div>

          </div>
        </div>
      </div>
      <div class="row text-center">
        <a href="{{action('Auth\PropertyUserLoginController@logout', ['main_system' => true])}}" type="button"
          style="font-size: 19px; background: #9900cc; color: #fff;" class="btn btn-flat m-8  btn-sm mt-10">
          <strong>@lang('lang_v1.main_system')</strong></a>
      </div>
      
      
      <div class="clearfix"></div>
    <div class="border-top">
        <div class="col-md-12 payment_row">
            <h3 class="text-brown">@lang('property::lang.payment_options')</h3>
            <div class="clearfix"></div>
            <div class="col-md-3 col-xs-12">
                <div class="form-group">
                    {!! Form::label('payment_option', __('property::lang.payment_option')) !!}
                    {!! Form::select('payment_option', $payment_options, null, ['class' => 'form-control
                    select2', 'id' => 'payment_option', 'placeholder' =>
                    __('property::lang.please_select')]); !!}

                </div>
            </div>
            <div class="col-md-3 col-xs-12">
                <div class="form-group">
                    {!! Form::label('amount', __('property::lang.amount')) !!}
                    {!! Form::text('amount', null, ['class' => 'form-control', 'id' => 'amount', 'placeholder' =>
                    __('property::lang.amount')]); !!}

                </div>
            </div>
            <input type="hidden" name="row_id" id="row_id" value="0">
            <input type="hidden" name="total_amount" id="total_amount" value="0">
            <div class="col-md-3 col-xs-12">
                <i class="btn btn-primary add_payment_row"
                    style="font-size: 20px; padding: 0px 10px 0px 10px; margin-top: 25px;">+</i>
            </div>
            <div class="clearfix"></div>

            <div class="col-md-6">
                <table class="table table-striped payment_table" id="payment_table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>{{ __('property::lang.on_account_of') }}</th>
                            <th>{{ __('property::lang.amount') }}</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody></tbody>

                </table>
            </div>

            <div class="payment_table_data"></div>
            <div class="clearfix"></div>
            <hr>
       <div class="col-md-12">
                <div class="col-md-6">
                    <div class="text-red text-right " style="margin-top: 25px; font-size: 23px; font-weight: bold">
                        @lang('property::lang.total_amount'): <span class="total_amount">0.00</span>
                    </div>
                </div>
                <div class="col-md-3 col-xs-12">
                    <div class="form-group">
                        {!! Form::label('payment_method', __('property::lang.payment_method')) !!}
                        {!! Form::select('payment_method', $payment_types, null, ['class' => 'form-control
                        payment_types_dropdown
                        select2', 'id' => 'payment_method', 'placeholder' =>
                        __('property::lang.please_select')]); !!}

                    </div>
                </div>
                <div class="col-md-2 col-xs-12">
                    <div class="form-group">
                        {!! Form::label('payment_method_amount', __('property::lang.amount')) !!}
                        {!! Form::text('payment_method_amount', null, ['class' => 'form-control', 'id' => 'payment_method_amount', 'placeholder' =>
                        __('property::lang.amount')]); !!}

                    </div>
                </div>
                <div class="col-md-1 col-xs-12">
                    <i class="btn btn-primary add_payment_section_row"
                       style="font-size: 20px; padding: 0px 10px 0px 10px; margin-top: 25px;">+</i>
                </div>
                <div class="clearfix"></div>
                <div class="col-md-3 hide  account_module">
                    <div class="form-group">
                        {!! Form::label("account_id" , __('lang_v1.bank_account') . ':') !!}
                        <div class="input-group">
                        <span class="input-group-addon">
                            <i class="fa fa-money"></i>
                        </span>
                            {!! Form::select("account_id", $bank_group_accounts, null , ['class' =>
                            'form-control
                            select2', 'placeholder' => __('lang_v1.please_select'), 'id' => "account_id", 'style' =>
                            'width:100%;']); !!}
                        </div>
                    </div>
                </div>
                @include('property::sell_land_blocks.partials.payment_type_details', ['payment_line' => $payment,
                'row_index' => 0 ])
            </div>

            <input type="hidden" name="m_row_id" id="m_row_id" value="0">
            <input type="hidden" name="total_payment_method_amount" id="total_payment_method_amount" value="0">
            <h3 class="text-brown">@lang('property::lang.payment_section')</h3>
            <div class="clearfix"></div>
            <div class="col-md-10">
                <table class="table table-striped payment_section" id="payment_section">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>{{ __('property::lang.payment_method') }}</th>
                        <th>{{ __('property::lang.cheque_no') }}</th>
                        <th>{{ __('property::lang.cheque_date') }}</th>
                        <th>{{ __('property::lang.bank') }}</th>
                        <th>{{ __('property::lang.card_no') }}</th>
                        <th>{{ __('property::lang.amount') }}</th>
                    </tr>
                    </thead>
                    <tbody></tbody>

                </table>
            </div>
            <div class="payment_data"></div>
            <div class="clearfix"></div>
            <hr>
            <div class="col-md-12">
                <div class="col-md-6"></div>
                <div class="col-md-3">
                    <div class="text-red text-right " style="font-size: 23px; font-weight: bold">
                        @lang('property::lang.total_amount_paid'): <span class="total_payment_method_amount">0.00</span>
                    </div>
                </div>
                <div class="col-md-3 text-right" style="float: right; font-size: 23px; font-weight: bold">
                    <button type="submit" class="btn btn-primary">@lang('property::lang.pay_now')</button>
                </div>
            </div>
      </div>
      </div>

    </div>
    <div class="modal" id="payment_modal" tabindex="-1" role="dialog">
      <div class="modal-dialog" role="document" style="width: 70%">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">@lang('property::lang.payment')</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <div class="row">
              <div class="col-md-2">
                {!! Form::label('cash', __('property::lang.cash').':*') !!}
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  {!! Form::label('cash', __('property::lang.amount').':*') !!}
                  {!! Form::text('payment[cash][amount]', null, ['class' => 'form-control reset
                  cash', 'placeholder' => __('property::lang.amount'), 'required', 'id' =>
                  'cash']); !!}
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-2">
                {!! Form::label('credit_card', __('property::lang.credit_card').':*') !!}
              </div>
              <div class="col-md-2">
                <div class="form-group">
                  {!! Form::label('cheuqe', __('property::lang.amount').':*') !!}
                  {!! Form::text('payment[credit_card][amount]', null, ['class' => 'form-control reset
                  cheuqe', 'placeholder' => __('property::lang.amount'), 'required', 'id' =>
                  'cheuqe']); !!}
                </div>
              </div>
              <div class="col-md-2">
                <div class="form-group">
                  {!! Form::label('card_no', __('property::lang.card_no').':*') !!}
                  {!! Form::text('payment[credit_card][card_no]', null, ['class' => 'form-control reset
                  card_no', 'placeholder' => __('property::lang.card_no'), 'required', 'id' =>
                  'card_no']); !!}
                </div>
              </div>
              <div class="col-md-2">
                <div class="form-group">
                  {!! Form::label('expiry_date', __('property::lang.expiry_date').':*') !!}
                  {!! Form::text('payment[credit_card][expiry_date]', null, ['class' => 'form-control reset
                  expiry_date', 'placeholder' => __('property::lang.expiry_date'), 'required', 'id' =>
                  'expiry_date']); !!}
                </div>
              </div>

            </div>
            <div class="row">
              <div class="col-md-2">
                {!! Form::label('cheque', __('property::lang.cheque').':*') !!}
              </div>
              <div class="col-md-2">
                <div class="form-group">
                  {!! Form::label('cheuqe', __('property::lang.amount').':*') !!}
                  {!! Form::text('payment[cheuqe][amount]', null, ['class' => 'form-control reset
                  cheuqe', 'placeholder' => __('property::lang.amount'), 'required', 'id' =>
                  'cheuqe']); !!}
                </div>
              </div>
              <div class="col-md-2">
                <div class="form-group">
                  {!! Form::label('cheque_no', __('property::lang.cheque_no').':*') !!}
                  {!! Form::text('payment[cheuqe][cheque_no]', null, ['class' => 'form-control reset
                  cheque_no', 'placeholder' => __('property::lang.cheque_no'), 'required', 'id' =>
                  'cheque_no']); !!}
                </div>
              </div>
              <div class="col-md-2">
                <div class="form-group">
                  {!! Form::label('bank', __('property::lang.bank').':*') !!}
                  {!! Form::text('payment[cheuqe][bank]', null, ['class' => 'form-control reset
                  bank', 'placeholder' => __('property::lang.bank'), 'required', 'id' =>
                  'bank']); !!}
                </div>
              </div>
              <div class="col-md-2">
                <div class="form-group">
                  {!! Form::label('branch', __('property::lang.branch').':*') !!}
                  {!! Form::text('payment[cheuqe][branch]', null, ['class' => 'form-control reset
                  branch', 'placeholder' => __('property::lang.branch'), 'required', 'id' =>
                  'branch']); !!}
                </div>
              </div>
              <div class="col-md-2">
                <div class="form-group">
                  {!! Form::label('cheque_date', __('property::lang.cheque_date').':*') !!}
                  {!! Form::text('payment[cheuqe][cheque_date]', null, ['class' => 'form-control reset
                  cheque_date', 'placeholder' => __('property::lang.cheque_date'), 'required', 'id' =>
                  'cheque_date']); !!}
                </div>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-primary">@lang('messages.save')</button>
            <button type="button" class="btn btn-secondary" data-dismiss="modal">@lang('messages.close')</button>
          </div>
        </div>
      </div>
    </div>
  </form>
</section>
@endsection

@section('javascript')
<script>
  $('.cheque_date').datepicker('setDate', new Date())
  $('.pay_amount').click(function(){
    $('#payment_modal').modal('show');

  });
  $('#submit').click(function(){
    let amount = $('#amount').val();
    let payment_type = $('#payment_type').val();
    if(amount === '' || amount === undefined ){
      toastr.error('Please enter amount');
      return false
    }
    if(payment_type === '' || payment_type === undefined){
      toastr.error('Please select payment type');
      return false
    }
    amount = parseFloat(amount);
    console.log(amount);
    $.ajax({
      method: 'POST',
      url: "{{action('\Modules\Petro\Http\Controllers\PumpOperatorPaymentController@store')}}",
      data: { amount, payment_type },
      success: function(result) {
        if(result.success){
          toastr.success(result.msg);
          reset();
        }else{
          toastr.error(result.msg)
        }
      },
    });
  })

  function reset() {
    console.log('reset');
    $('.reset').val('');
  }
</script>
<script>
  function enterVal(val) {
        $('#amount').focus();
        if(val === 'precision'){
            str = $('#amount').val();
            str = str + '.';
            $('#amount').val(str);
            return;
        }
        if(val === 'backspace'){
            str = $('#amount').val();
            str = str.substring(0, str.length - 1);
            $('#amount').val(str);
            return;
        }
        let amount = $('#amount').val() + val;
        amount = amount.replace(',', '');
        $('#amount').val(amount);
    };
   
   $('#customer_id').change(function () {
      let customer_id = $(this).val();
      $.ajax({
        method: 'GET',
        url: '/property/customer-payment/get-property-dropdown-by-customer-id/'+customer_id,
        contentType: 'html',
        data: {  },
        success: function(result) {
            $('#property_id').empty().append(result);
        },
      });
   })
   
   
    $(document).on('click', '.add_payment_row', function(){
        let payment_option = $('#payment_option').val();
        let payment_option_text = $('#payment_option option:selected').text();
        let amount = $('#amount').val();
        if(amount === '' || amount === undefined || parseFloat(amount) === 0){
            toastr.error('Amount is required');
            return false;
        }

        let row_id = parseInt($('#row_id').val());


        $('#payment_table tbody').append(
            `<tr>
                    <td>${row_id+1}</td>
                    <td>${payment_option_text}</td>
                    <td class="onAccountof">${__number_f(amount)}</td>
                     <td class="text-center"><button type="button" class="btn btn-danger btn-xs p-remove" index="${row_id}" ><i class="glyphicon glyphicon-remove"></i></button></td>

                </tr>`
        )
        $('.payment_table_data').append(
            `
                <input type="hidden" name="account_of[${row_id}][payment_option_id]" value="${payment_option}">
                <input type="hidden" name="account_of[${row_id}][amount]" value="${amount}">
        `
        )

        $('#row_id').val(row_id + 1);
        $('#payment_option').val('').trigger('change');
        $('#amount').val(0);
        $('.reset').val('');

        let total_amount = parseFloat($('#total_amount').val());
        amount = parseFloat(amount);
        total_amount = total_amount + amount;
        $('#total_amount').val(total_amount);
        $('.total_amount').text(__currency_trans_from_en(total_amount, false))

    });
    
    
    $("#payment_table").on("click", ".p-remove", function(event) {
        rowindex = $(this).closest('tr').index();
        $('[name="account_of['+$(this).attr("index")+'][amount]"]').remove();
        $('[name="account_of['+$(this).attr("index")+'][payment_option_id]"]').remove();

        $(this).closest("tr").remove();
        var preoff=0;
        $("#payment_table .onAccountof").each(function()
        {
            preoff = parseInt($(this).text().replace(",", ""))+parseInt(preoff);
        });
        $('#total_amount').val(preoff);
        $('.total_amount').text(__currency_trans_from_en(preoff, false))

        let total_block_value = __number_uf($('#total_block_value').val());
        let remove_block_value = __number_f($('.block_value').val());
    });
    
    
    $(document).on('click', '.add_payment_section_row', function(){
            let amount = $('#payment_method_amount').val();
            if(amount === '' || amount === undefined || parseFloat(amount) === 0){
                toastr.error('Amount is required');
                return false;
            }
            let payment_method = $('#payment_method').val();
            let payment_method_text = $('#payment_method option:selected').text();
            let account_id = $('#account_id').val();
            let card_holder_name = $('#card_holder_name_0').val();
            let card_transaction_number = $('#card_transaction_number_0').val();
            let card_number = $('#card_number_0').val();
            let card_type = $('#card_type_0').val();
            let card_month = $('#card_month_0').val();
            let card_year = $('#card_year_0').val();
            let card_security = $('#card_security_0').val();
            let cheque_number = $('#cheque_number_0').val();
            let cheque_date = $('#cheque_date_0').val();
            let bank_name = $('#bank_name_0').val();
            let m_row_id = parseInt($('#m_row_id').val());

            if(payment_method != 'bank_transfer' && payment_method != 'cheque'){
                cheque_date = '';
            }
            $('#payment_section tbody').append(
                `<tr>
                    <td>${m_row_id+1}</td>
                    <td>${payment_method_text}</td>
                    <td>${cheque_number}</td>
                    <td>${cheque_date}</td>
                    <td>${bank_name}</td>
                    <td>${card_number}</td>
                    <td class="payment_section_amount">${__number_f(amount)}</td>
                     <td class="text-center"><button type="button" class="btn btn-danger btn-xs m-remove" index_id="${m_row_id}"><i class="glyphicon glyphicon-remove"></i></button></td>
                </tr>`
            )
            $('.payment_data').append(
                `<input type="hidden" name="payment[${m_row_id}][payment_method_amount]" value="${amount}">
                <input type="hidden" name="payment[${m_row_id}][method]" value="${payment_method}">
                <input type="hidden" name="payment[${m_row_id}][account_id]" value="${account_id}">
                <input type="hidden" name="payment[${m_row_id}][card_number]" value="">
                <input type="hidden" name="payment[${m_row_id}][card_transaction_number]" value="">
                <input type="hidden" name="payment[${m_row_id}][card_holder_name]" value="${card_holder_name}">
                <input type="hidden" name="payment[${m_row_id}][card_type]" value="${card_type}">
                <input type="hidden" name="payment[${m_row_id}][card_year]" value="${card_year}">
                <input type="hidden" name="payment[${m_row_id}][card_month]" value="${card_month}">
                <input type="hidden" name="payment[${m_row_id}][card_security]" value="${card_security}">
                <input type="hidden" name="payment[${m_row_id}][cheque_number]" value="${cheque_number}">
                <input type="hidden" name="payment[${m_row_id}][cheque_date]" value="${cheque_date}">
                <input type="hidden" name="payment[${m_row_id}][bank_name]" value="${bank_name}">
        `
            )

            $('#m_row_id').val(m_row_id + 1);
            $('#payment_method_amount').val(0);
            $('#payment_method').val('cash').trigger('change');
            $('.reset').val('');

            let total_amount = parseFloat($('#total_payment_method_amount').val());
            amount = parseFloat(amount);
            total_amount = total_amount + amount;
            $('#total_payment_method_amount').val(total_amount);
            $('.total_payment_method_amount').text(__currency_trans_from_en(total_amount, false))

        });
        
        
        
    $("#payment_section").on("click", ".m-remove", function(event) {
        rowindex = $(this).closest('tr').index();

        $('[name="payment['+$(this).attr("index_id")+'][payment_method_amount]"]').remove();
        $('[name="payment['+$(this).attr("index_id")+'][method]"]').remove();
        $('[name="payment['+$(this).attr("index_id")+'][account_id]"]').remove();
        $('[name="payment['+$(this).attr("index_id")+'][card_number]"]').remove();
        $('[name="payment['+$(this).attr("index_id")+'][card_transaction_number]"]').remove();
        $('[name="payment['+$(this).attr("index_id")+'][card_holder_name]"]').remove();
        $('[name="payment['+$(this).attr("index_id")+'][card_type]"]').remove();
        $('[name="payment['+$(this).attr("index_id")+'][card_year]"]').remove();
        $('[name="payment['+$(this).attr("index_id")+'][card_month]"]').remove();
        $('[name="payment['+$(this).attr("index_id")+'][card_security]"]').remove();
        $('[name="payment['+$(this).attr("index_id")+'][cheque_number]"]').remove();
        $('[name="payment['+$(this).attr("index_id")+'][cheque_date]"]').remove();
        $('[name="payment['+$(this).attr("index_id")+'][bank_name]"]').remove();

        $(this).closest("tr").remove();
        let total_block_value = __number_uf($('#total_block_value').val());
        let remove_block_value = __number_f($('.block_value').val());
        var pre=0;
        $("#payment_section .payment_section_amount").each(function()
        {
            pre = parseInt($(this).text().replace(",", ""))+parseInt(pre);
        });
        $('#total_payment_method_amount').val(pre);
        $('.total_payment_method_amount').text(__currency_trans_from_en(pre, false))
    });
   
</script>
@endsection