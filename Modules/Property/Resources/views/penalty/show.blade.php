<div class="modal-dialog" role="document" style="width: 45%;">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                    aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">@lang( 'property::lang.penalties' )</h4>
        </div>

        <div class="modal-body">
            <div class="row">
                <div class="col-md-12">
                    <table class="table table-striped table-bordered" id="account_setting_table" style="width: 100%;">
                        <thead>
                            <tr>
                                <th>@lang('property::lang.date')</th>
                                <th>@lang('property::lang.amount')</th>
                                <th>@lang('property::lang.user')</th>
                                @if($show_delete)
                                <th>@lang('property::lang.action')</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                @foreach ($penalties as $penalty)
                                    <tr>
                                        <td>{{@format_date($penalty->date)}}</td>
                                        <td>{{@num_format($penalty->amount)}}</td>
                                        <td>{{$penalty->username}}</td>
                                        @if($show_delete)
                                        <td>
                                            <a data-href="{{action('\Modules\Property\Http\Controllers\PurchaseController@destroy', [$penalty->id])}}" class="delete-penalty btn btn-xs btn-danger"><i class="fa fa-trash"> </i>{{ __("messages.delete") }}</a>
                                        </td>
                                        @endif
                                    </tr>
                                    
                                @endforeach
                               
                            </tr>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td class="text-right"><b>@lang('property::lang.total')</b></td>
                                <td><b>{{@num_format($penalties->sum('amount'))}}</b></td>
                                <td></td>
                                @if($show_delete)
                                <td></td>
                                @endif
                            </tr>
                        </tfoot>
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