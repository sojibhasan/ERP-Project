<div class="modal-dialog" role="document" style="width: 50%">
    <div class="modal-content">
  
      {!! Form::open(['url' => action('AccountController@postChequeDeposit'), 'method' => 'post', 'id' => 'deposit_form',
      'enctype' => 'multipart/form-data' ]) !!}
  
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
            aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">@lang( 'account.cheque_deposit' )</h4>
      </div>
  
      <div class="modal-body">
          <div class="col-md-4">
            <div class="form-group" style="margin-top: 28px;">
                <strong>@lang('account.selected_account')</strong>:
                {{$account->name}}
                {!! Form::hidden('account_id', $account->id) !!}
              </div>
          </div>
          <div class="col-md-4" style="margin-top: 28px;">
            <span class="text-red" > @lang('account.balance'): @if(!empty($account_balance->balance))
                {{@num_format($account_balance->balance)}} @else {{0.00}} @endif </span>
          </div>
          <div class="col-md-4">
            <div class="form-group">
                {!! Form::label('operation_date', __( 'account.transaction_date' ) .":*") !!}
                {!! Form::text('operation_date', null, ['class' => 'form-control pull-right transaction_date', 'id' => 'transaction_date', 'required','placeholder' => __(
                'account.transaction_date' ) ]); !!}
            </div>
          </div>
          <div class="clearfix"></div>
          <div class="col-sm-4">
            <div class="form-group">
              {!! Form::label('transaction_date_range_cheque_deposit', __('report.date_range') . ':') !!}
              <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                {!! Form::text('transaction_date_range_cheque_deposit', null, ['class' => 'form-control', 'readonly', 'placeholder' => __('report.date_range')]) !!}
              </div>
            </div>
          </div>
          <div class="clearfix"></div>
          <table class="table table-bordered table-striped" id="cheque_list_table">
            <thead>
             <tr>
             <th>@lang('account.select')</th>
             <th>@lang('account.cheque_no')</th>
             <th>@lang('account.cheque_date')</th>
             <th>@lang('account.bank')</th>
             <th>@lang('account.amount')</th>
            </tr>
          </thead>
          <tbody></tbody>
          </table>

          <div class="clearfix"></div>
     
        <div class="form-group">
          {!! Form::label('from_account', __( 'account.deposit_to' ) .":") !!}
          {!! Form::select('from_account', $to_accounts, null, ['class' => 'form-control select2', 'placeholder' =>
          __('messages.please_select'), 'required' ]); !!}
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
  
  <script type="text/javascript">
    $(document).ready( function(){
      $('#transaction_date').datetimepicker({
        format: moment_date_format + ' ' + moment_time_format
      });
    });

    $('#transaction_date_range_cheque_deposit').daterangepicker(
      dateRangeSettings,
      function (start, end) {
        $('#transaction_date_range_cheque_deposit').val(start.format(moment_date_format) + ' ~ ' + end.format(moment_date_format));

        get_cheques_list();
        }
    );

    $('#transaction_date_range_cheque_deposit').trigger('change');
    $('.select2').select2();
  </script>