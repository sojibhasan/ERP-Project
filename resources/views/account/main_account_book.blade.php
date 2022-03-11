@extends('layouts.app')
@section('title', __('account.account_book'))

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
  <h1>@lang('account.account_book')
  </h1>
</section>

<!-- Main content -->
<section class="content">
  <div class="row">
    <div class="col-sm-4 col-xs-6">
      <div class="box box-solid">
        <div class="box-body">
          <table class="table">
            <tr>
              <th>@lang('account.account_name'): </th>
              <td>{{$account->name}}</td>
            </tr>
            <tr>
              <th>@lang('lang_v1.account_type'):</th>
              <td>@if(!empty($account->account_type->parent_account)) {{$account->account_type->parent_account->name}} - @endif {{$account->account_type->name ?? ''}}</td>
            </tr>
            <tr>
              <th>@lang('account.account_number'):</th>
              <td>{{$account->account_number}}</td>
            </tr>
            <tr>
              <th>@lang('lang_v1.balance'):</th>
              <td><span id="account_balance"></span></td>
            </tr>
          </table>
        </div>
      </div>
    </div>
    <div class="col-sm-8 col-xs-12">
      <div class="box box-solid">
        <div class="box-header">
          <h3 class="box-title"> <i class="fa fa-filter" aria-hidden="true"></i> @lang('report.filters'):</h3>
        </div>
        <div class="box-body">
          <div class="col-sm-4">
            <div class="form-group">
              {!! Form::label('transaction_date_range', __('report.date_range') . ':') !!}
              <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                {!! Form::text('transaction_date_range', null, ['class' => 'form-control', 'readonly', 'placeholder' => __('report.date_range')]) !!}
              </div>
            </div>
          </div>
          @if($id == $card_account_id)
          <div class="col-sm-4">
            <div class="form-group">
              {!! Form::label('card_type', __('lang_v1.card_type') . ':') !!}
              <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-exchange"></i></span>
                {!! Form::select('card_type', $card_type_accounts, null, ['class' => 'form-control', 'placeholder' => __('lang_v1.all')]) !!}
              </div>
            </div>
          </div>
          @endif
          <div class="col-sm-4">
            <div class="form-group">
              {!! Form::label('transaction_type', __('account.transaction_type') . ':') !!}
              <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-exchange"></i></span>
                {!! Form::select('transaction_type', ['' => __('messages.all'),'debit' => __('account.debit'), 'credit' => __('account.credit')], '', ['class' => 'form-control']) !!}
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
 
  <div class="row">
    <div class="col-sm-12">
     <div class="box">
      <div class="box-body">
        @can('account.access')
        <div class="table-responsive">
         <table class="table table-bordered table-striped" id="account_book">
          <thead>
           <tr>
            <th>@lang( 'lang_v1.sub_account_number' )</th>
            <th>@lang( 'account.sub_account_name' )</th>
            <th>@lang( 'lang_v1.balance' )</th>
          </tr>
        </thead>
        <tfoot>
          <tr class="bg-gray font-17 text-center footer-total">
              <td colspan="2"><strong>@lang('sale.total'):</strong></td>
              <td><span id="footer_total_balance" class="display_currency" data-currency_symbol="true"></span></td>
          </tr>
      </tfoot>
      </table>
    </div>
    @endcan
  </div>
</div>
</div>
</div>


<div class="modal fade at_modal" tabindex="-1" role="dialog"
aria-labelledby="gridSystemModalLabel">
<div class="modal fade account_model" tabindex="-1" role="dialog"
aria-labelledby="gridSystemModalLabel">
</div>

</section>
<!-- /.content -->

@endsection

@section('javascript')
<script>
  $(document).ready(function(){
    update_account_balance();

    dateRangeSettings.startDate = moment().subtract(6, 'days');
    dateRangeSettings.endDate = moment();
    $('#transaction_date_range').daterangepicker(
      dateRangeSettings,
      function (start, end) {
        $('#transaction_date_range').val(start.format(moment_date_format) + ' ~ ' + end.format(moment_date_format));

        account_book.ajax.reload();
      }
      );

    $(document).on('click', 'button.reconcile_status_btn', function(e){
      let href = $(this).data('href');

      $.ajax({
        method: 'get',
        url: href,
        data: {  },
        success: function(result) {
          if(result.success){
            toastr.success('Success');
            account_book.ajax.reload();
          }
        },
      });
    });


        // Account Book
        account_book = $('#account_book').DataTable({
          processing: true,
          serverSide: false,
          ajax: {
            url: '{{action("AccountController@getMainAccountBook",[$account->id])}}',
            data: function(d) {
              var start = '';
              var end = '';
              if($('#transaction_date_range').val()){
                start = $('input#transaction_date_range').data('daterangepicker').startDate.format('YYYY-MM-DD');
                end = $('input#transaction_date_range').data('daterangepicker').endDate.format('YYYY-MM-DD');
              }
              var transaction_type = $('select#transaction_type').val();
              d.start_date = start;
              d.end_date = end;
              d.type = transaction_type;

            }
          },
          "ordering": true,
          "searching": true,
          columns: [
            {data: 'account_number', name: 'account_number'},
            {data: 'name', name: 'name'},
            {data: 'balance', name: 'balance'},
          ],
          @include('layouts.partials.datatable_export_button')
          "fnDrawCallback": function (oSettings) {
            var total = sum_table_col($('#account_book'), 'balance');
            $('#footer_total_balance').text(total);
            __currency_convert_recursively($('#account_book'));
          }
        });

        $('#transaction_type').change( function(){
          account_book.ajax.reload();
        });
        $('#transaction_date_range').on('cancel.daterangepicker', function(ev, picker) {
          $('#transaction_date_range').val('');
          account_book.ajax.reload();
        });

      });

      $('#card_type').change(function(){
        account_book.ajax.reload();
      })
  $(document).on('click', 'a.delete_account_transaction', function(e){
    e.preventDefault();
    swal({
      title: LANG.sure,
      icon: "warning",
      buttons: true,
      dangerMode: true,
    }).then((willDelete) => {
      if (willDelete) {
        var href = $(this).data('href');
        $.ajax({
          url: href,
          method: 'DELETE',
          dataType: "json",
          success: function(result){
            if(result.success === true){
              toastr.success(result.msg);
              account_book.ajax.reload();
              update_account_balance();
            } else {
              toastr.error(result.msg);
            }
          }
        });
      }
    });
  });

  function update_account_balance(argument) {
    $('span#account_balance').html('<i class="fa fa-refresh fa-spin"></i>');
    $.ajax({
      url: '{{action("AccountController@getAccountBalanceMain", [$account->id])}}',
      dataType: "json",
      success: function(data){
          console.log(data);
        $('span#account_balance').text(__currency_trans_from_en(data.balance, true));
      }
    });
  }
</script>
@endsection