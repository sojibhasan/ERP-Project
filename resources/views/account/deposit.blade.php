<div class="modal-dialog" role="document">
  <div class="modal-content">

    {!! Form::open(['url' => action('AccountController@postDeposit'), 'method' => 'post', 'id' => 'deposit_form',
    'enctype' => 'multipart/form-data' ]) !!}

    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
          aria-hidden="true">&times;</span></button>
      <h4 class="modal-title">@lang( 'account.deposit' )</h4>
    </div>

    <div class="modal-body">
      <div class="form-group">
        <strong>@lang('account.selected_account')</strong>:
        <span class="selected_account">
          @if(empty($sub_card_accounts))
          {{$account->name}}
          @endif
        </span>
        <span class="text-red pull-right account_balance"> @lang('account.balance'): @if(!empty($account_balance->balance))
          {{@num_format($account_balance->balance)}} @else {{0.00}} @endif </span>
        {!! Form::hidden('check_insufficient', $check_insufficient, ['id' => 'check_insufficient']) !!}
        @if(empty($sub_card_accounts))
        {!! Form::hidden('account_balance', !empty($account_balance->balance) ? $account_balance->balance : 0, ['id' => 'account_balance']) !!}
        @else
        {!! Form::hidden('account_balance', 0, ['id' => 'account_balance']) !!}
        @endif
      </div>

      @if(!empty($sub_card_accounts))
      <div class="form-group">
        {!! Form::label('account_id', __( 'account.card_accounts' ) .":") !!}
        {!! Form::select('account_id', $sub_card_accounts, null, ['class' => 'form-control select2 account_id_deposit', 'placeholder' =>
        __('messages.please_select') ]); !!}
      </div>
      @else
      {!! Form::hidden('account_id', $account->id) !!}

      @endif


      <div class="form-group">
        {!! Form::label('amount', __( 'sale.amount' ) .":*") !!}
        {!! Form::text('amount', 0, ['class' => 'form-control input_number', 'required','placeholder' => __(
        'sale.amount' ) ]); !!}
      </div>

      <div class="form-group">
        {!! Form::label('account_group_id', __( 'account.account_group' ) .":*") !!}
        {!! Form::select('account_group_id', $account_groups, null, ['class' => 'form-control select2', 'placeholder' => __('lang_v1.please_select') ]); !!}
      </div>

      <div class="form-group">
        {!! Form::label('from_account', __( 'account.deposit_to' ) .":") !!}
        {!! Form::select('from_account', $from_accounts, null, ['class' => 'form-control select2', 'placeholder' =>
        __('messages.please_select') ]); !!}
      </div>

      
      <div class="form-group">
        {!! Form::label('cheque_number', __( 'lang_v1.cheque_number' ) .":*") !!}
        {!! Form::text('cheque_number', null, ['class' => 'form-control input_number', 'required','placeholder' => __(
        'lang_v1.cheque_number' ) ]); !!}
      </div>

      <div class="form-group">
        {!! Form::label('operation_date', __( 'messages.date' ) .":*") !!}
        <div class="input-group date" id='od_datetimepicker'>
          {!! Form::text('operation_date', 0, ['class' => 'form-control', 'required','placeholder' => __(
          'messages.date' ) ]); !!}
          <span class="input-group-addon">
            <span class="glyphicon glyphicon-calendar"></span>
          </span>
        </div>
      </div>

      <div class="form-group">
        {!! Form::label('note', __( 'brand.note' )) !!}
        {!! Form::textarea('note', null, ['class' => 'form-control', 'placeholder' => __( 'brand.note' ), 'rows' => 4]);
        !!}
      </div>

      <div class="form-group">
        {!! Form::label('attachment', __( 'lang_v1.add_image_document' )) !!}
        {!! Form::file('attachment', ['files' => true]); !!}
      </div>
    </div>

    <div class="modal-footer">
      <button type="submit" class="btn btn-primary submit_btn">@lang( 'messages.submit' )</button>
      <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
    </div>

    {!! Form::close() !!}

  </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->
<style>
  .swal-title {
    color: red;
  }
</style>
<script type="text/javascript">
  $(document).ready( function(){
    $('#od_datetimepicker').datetimepicker({
      format: moment_date_format + ' ' + moment_time_format
    });
  });

  @if($group_name != 'Bank Account')
    $('#amount').change(function(){
      let amount = parseFloat($(this).val());
      account_balance = parseFloat($('#account_balance').val());
      if(amount > account_balance){
        swal({
            title: '@lang('account.insufficient_balance_msg')',
            icon: "error",
            buttons: true,
            dangerMode: true,
        })
        $('.submit_btn').prop('disabled', true);
      }else{
        $('.submit_btn').prop('disabled', false);
      }
    })
  @endif


  @if(!empty($sub_card_accounts))
  $('.account_id_deposit').change(function(){
    account_id = $(this).val();
    $.ajax({
      method: 'get',
      url: '/accounting-module/get-account-balance/'+account_id,
      data: {  },
      success: function(result) {
        $('.account_balance').text('Balance:' +__number_f(result.balance, false));
        $('.selected_account').text(result.name);
        $('#account_balance').val(result.balance);
      },
    });
  })
  @endif
  $('#account_group_id').change(function () {
    $.ajax({
      method: 'get',
      url: '/accounting-module/get-account-by-group-id/' + $(this).val(),
      data: {  },
      contentType: 'html',
      success: function(result) {
        $('#from_account').empty().append(result);
      },
    });
  })
</script>