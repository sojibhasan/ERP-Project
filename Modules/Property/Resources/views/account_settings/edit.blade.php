<div class="modal-dialog" role="document" style="width: 55%;">
  <div class="modal-content">

    {!! Form::open(['url' => action('\Modules\Property\Http\Controllers\PropertyAccountSettingController@update',
    $account_settings->id),
    'method' =>
    'put', 'id' => 'account_settings_add_form' ]) !!}

    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
          aria-hidden="true">&times;</span></button>
      <h4 class="modal-title">@lang( 'property::lang.edit_account_settings' )</h4>
    </div>

    <div class="modal-body">
      <div class="row">
        <div class="form-group col-sm-12">
          {!! Form::label('date', __( 'property::lang.date' ) . ':*') !!}
          {!! Form::text('date', @format_date(date('Y-m-d')), ['class' => 'form-control', 'required', 'readonly',
          'placeholder' => __(
          'property::lang.date' )]); !!}
        </div>
        <div class="form-group col-sm-12">
          {!! Form::label('income_account_id', __( 'property::lang.select_income_account' ) . ':*') !!}
          {!! Form::select('income_account_id', $income_not_pen_accounts, $account_settings->income_account_id,
          ['placeholder' => __(
          'messages.please_select' ),
          'required', 'class' => 'form-control select2', 'style' => 'width: 100%;']); !!}
        </div>
        <div class="form-group col-sm-12">
          {!! Form::label('expense_account_id', __( 'property::lang.select_expense_account' ) . ':*') !!}
          {!! Form::select('expense_account_id', $expense_accounts, $account_settings->expense_account_id,
          ['placeholder' => __( 'messages.please_select'
          ),
          'required', 'class' => 'form-control select2', 'style' => 'width: 100%;']); !!}
        </div>
        <div class="form-group col-sm-12">
          {!! Form::label('interest_income_account_id', __( 'property::lang.select_interest_income_account' ) . ':*')
          !!}
          {!! Form::select('interest_income_account_id', $income_accounts,
          $account_settings->interest_income_account_id, ['placeholder' => __(
          'messages.please_select' ),
          'required', 'class' => 'form-control select2', 'style' => 'width: 100%;']); !!}
        </div>
        <div class="form-group col-sm-12">
          {!! Form::label('penalty_income_account_id', __( 'property::lang.select_penalty_income_account' ) . ':*') !!}
          {!! Form::select('penalty_income_account_id', $income_accounts, $account_settings->penalty_income_account_id,
          ['placeholder' => __(
          'messages.please_select' ),
          'required', 'class' => 'form-control select2', 'style' => 'width: 100%;']); !!}
        </div>
        <div class="form-group col-sm-12">
          {!! Form::label('account_receivable_account_id', __( 'property::lang.select_account_receivable_account' ) .
          ':*') !!}
          {!! Form::select('account_receivable_account_id', $account_receivable_accounts,
          $account_settings->account_receivable_account_id, ['placeholder' => __(
          'messages.please_select' ),
          'required', 'class' => 'form-control select2', 'style' => 'width: 100%;']); !!}
        </div>
        <div class="form-group col-sm-12">
          {!! Form::label('capital_income_account_id', __( 'property::lang.select_capital_income_account' ) . ':*') !!}
          {!! Form::select('capital_income_account_id', $income_accounts, $account_settings->capital_income_account_id,
          ['placeholder' => __(
          'messages.please_select' ),
          'class' => 'form-control select2', 'style' => 'width: 100%;']); !!}
        </div>


      </div>
      <div class="row">
        <div class="col-md-12">
          <table class="table table-striped table-bordered" id="account_setting_table" style="width: 100%;">
            <thead>
              <tr>
                <th>@lang('property::lang.income_account')</th>
                <th>@lang('property::lang.expense_account')</th>
                <th>@lang('property::lang.interest_income_account')</th>
                <th>@lang('property::lang.penalty_income_account')</th>
                <th>@lang('property::lang.account_receivable')</th>
                <th>@lang('property::lang.capital_income_account')</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td class="income_account_text">
                  @if(!empty($account_settings->income_account_id))
                  {{App\Account::find($account_settings->income_account_id)->name}}
                  @endif
                </td>
                <td class="expense_account_text">
                  @if(!empty($account_settings->expense_account_id))
                  {{App\Account::find($account_settings->expense_account_id)->name}}
                  @endif
                </td>
                <td class="interest_income_account_text">
                  @if(!empty($account_settings->interest_income_account_id))
                  {{App\Account::find($account_settings->interest_income_account_id)->name}}
                  @endif
                </td>
                <td class="penalty_income_account_text">
                  @if(!empty($account_settings->penalty_income_account_id))
                  {{App\Account::find($account_settings->penalty_income_account_id)->name}}
                  @endif
                </td>
                <td class="account_receivable_account_text">
                  @if(!empty($account_settings->account_receivable_account_id))
                  {{App\Account::find($account_settings->account_receivable_account_id)->name}}
                  @endif
                </td>
                <td class="capital_income_account_text">
                  @if(!empty($account_settings->capital_income_account_id))
                  {{App\Account::find($account_settings->capital_income_account_id)->name}}
                  @endif
                </td>
              </tr>
            </tbody>
          </table>
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
   $('.select2').select2();
  $('#date').datepicker('setDate', "{{\Carbon::parse($account_settings->date)->format(session('business.date_format'))}}");

 $('#income_account_id').change(function () {
    $('.income_account_text').text($('#income_account_id option:selected').text());
 })
 $('#expense_account_id').change(function () {
    $('.expense_account_text').text($('#expense_account_id option:selected').text());
 })
 $('#interest_income_account_id').change(function () {
    $('.interest_income_account_text').text($('#interest_income_account_id option:selected').text());
 })
 $('#penalty_income_account_id').change(function () {
    $('.penalty_income_account_text').text($('#penalty_income_account_id option:selected').text());
 })
 $('#account_receivable_account_id').change(function () {
    $('.account_receivable_account_text').text($('#account_receivable_account_id option:selected').text());
 })
 $('#capital_income_account_id').change(function () {
    $('.capital_income_account_text').text($('#capital_income_account_id option:selected').text());
 })
</script>