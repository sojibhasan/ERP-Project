<div class="modal-dialog" role="document" style="width: 45%;">
    <div class="modal-content">

        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                    aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">@lang( 'property::lang.account_settings' )</h4>
        </div>

        <div class="modal-body">
            <div class="row">
                <div class="col-md-4">
                    <div class="well">
                        <strong>@lang('property::lang.project'): </strong>{{$property->name}}<br>
                    </div>
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
            <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
        </div>

    </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->

<script>
    $('#date').datepicker('setDate', new Date());
  
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
</script>