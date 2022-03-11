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
{{--            <tr>--}}
{{--              <th>Description:</th>--}}
{{--              <td><span id="description"></span></td>--}}
{{--            </tr>--}}

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
          {{-- 
            /**
             * @ModifiedBy: Afes Oktavinus
             * @DateBy: 02-06-2021
             * @Task 3340
             */
          --}}
          <div class="col-sm-4">
            <div class="form-group">
              {!! Form::label('transaction_customer', __('report.customer') . ':') !!}
              <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-exchange"></i></span>
                {!! Form::select('transaction_customer', $customers, null, ['class' => 'form-control select2', 'placeholder' => __('lang_v1.all'), "id"=>"transaction_customer"]) !!}
              </div>
            </div>
          </div>

          <div class="col-sm-4">
            <div class="form-group">
              {!! Form::label('transaction_customer', __('report.supplier') . ':') !!}
              <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-exchange"></i></span>
                {!! Form::select('transaction_supplier', $suppliers, null, ['class' => 'form-control select2', 'placeholder' => __('lang_v1.all'), 'id' => "transaction_supplier" ]) !!}
              </div>
            </div>
          </div>
          {{-- Finish Change --}}
          @if($id == $card_account_id)
          <div class="col-sm-4">
            <div class="form-group">
              {!! Form::label('card_type', __('lang_v1.card_type') . ':') !!}
              <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-exchange"></i></span>
                {!! Form::select('card_type', $card_type_accounts, null, ['class' => 'form-control select2', 'placeholder' => __('lang_v1.all')]) !!}
              </div>
            </div>
          </div>
          @endif
          <div class="col-sm-4">
            <div class="form-group">
              {!! Form::label('transaction_type', __('account.transaction_type') . ':') !!}
              <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-exchange"></i></span>
                {!! Form::select('transaction_type', ['' => __('messages.all'),'debit' => __('account.debit'), 'credit' => __('account.credit')], '', ['class' => 'form-control select2']) !!}
              </div>
            </div>
          </div>
          @if($account->asset_type == $cheque_in_hand_group_id || $account->asset_type == $bank_group_id || $account->id == $cheque_return_account_id)
          <div class="col-sm-4">
            <div class="form-group">
              {!! Form::label('cheque_number', __('lang_v1.cheque_number') . ':') !!}
              <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-id-card-o"></i></span>
                {!! Form::select('cheque_number', $cheque_numbers, '', ['class' => 'form-control select2', 'id' => 'cheque_number', 'placeholder' => __('lang_v1.all')]) !!}
              </div>
            </div>
          </div>
          @endif
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
            <th>@lang( 'messages.action' )</th>
            <th>@lang( 'messages.date' )</th>
            <!--<th>@lang( 'lang_v1.ref_no' )</th>-->
            <th>@lang( 'lang_v1.description' )</th>
            @if($account->asset_type == $cheque_in_hand_group_id || $account->asset_type == $bank_group_id || $account->id == $cheque_return_account_id)
            <th>@lang('lang_v1.cheque_number')</th>
            @endif
            @if($account->id == $card_account_id)
            <th>@lang( 'lang_v1.card_type' )</th>
            @else
            <th>@lang( 'lang_v1.note' ) </th> 
            @endif
            <th>@lang( 'lang_v1.image' )</th>
            <th>@lang( 'lang_v1.added_by' )</th>
            <th>@lang('account.debit')</th>
            <th>@lang('account.credit')</th>
            <th>@lang( 'lang_v1.balance' )</th>
            <th>@lang('account.reconcile_status')</th>
          </tr>
        </thead>
        <tfoot>
          <tr class="bg-gray font-17 text-center footer-total">
            <td colspan="@if($account->asset_type == $cheque_in_hand_group_id || $account->asset_type == $bank_group_id || $account->id == $cheque_return_account_id) 6 @else 5 @endif"><strong>@lang('sale.total'):</strong></td>
            <td></td>
            <td><span id="footer_debit_total" class="display_currency" data-currency_symbol="true"></span></td>
            <td><span id="footer_credit_total" class="display_currency" data-currency_symbol="true"></span></td>
            <td></td>
            <td></td>
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
<style>
  .dataTables_empty{
        color: {{App\System::getProperty('not_enalbed_module_user_color')}};
        font-size: {{App\System::getProperty('not_enalbed_module_user_font_size')}}px;
    }
</style>
@section('javascript')
<script>
  $(document).ready(function(){
    var body = document.getElementsByTagName("body")[0];
    body.className += " sidebar-collapse";

    update_account_balance();
    // update_description();

    dateRangeSettings.startDate = moment().startOf('month');
    dateRangeSettings.endDate = moment().endOf('month');
    
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
          language: {
              "emptyTable": "@if(!$account_access) {{App\System::getProperty('not_enalbed_module_user_message')}} @else @lang('account.no_data_available_in_table') @endif"
          },
          processing: true,
          serverSide: false,
          pageLength: 25,
          aaSorting: [0,'asc'],
          ajax: {
            url: '{{action("AccountController@show",[$account->id])}}',
            data: function(d) {
              var start = '';
              var end = '';
              if($('#transaction_date_range').val()){
                start = $('input#transaction_date_range').data('daterangepicker').startDate.format('YYYY-MM-DD');
                end = $('input#transaction_date_range').data('daterangepicker').endDate.format('YYYY-MM-DD');                
              }
              var transaction_type = $('select#transaction_type').val();
              var customer = $('select#transaction_customer').val();
              var supplier = $('select#transaction_supplier').val();
              d.start_date = start;
              d.end_date = end;
              d.type = transaction_type;
              /** 
             * @ModifiedBy : Afes Oktavianus
             * @DateBy : 03-06-2021
             * @Task : 3340
             */
              d.customer = customer;
              d.supplier = supplier;

              if($('#card_type').length){
                d.card_type = $('#card_type').val();
              }
              if($('#cheque_number').length){
                d.cheque_number = $('#cheque_number').val();
              }
            }
          },
          aaSorting: [[0, 'asc']],
          "ordering": true,
          "searching": true,
          columns: [
            {data: 'action', name: 'action'},
            {data: 'operation_date', name: 'operation_date'},
            // {data: 'ref_no', name: 'ref_no'},
            {data: 'description', name: 'description'},
            @if($account->asset_type == $cheque_in_hand_group_id || $account->asset_type == $bank_group_id || $account->id == $cheque_return_account_id)
            {data: 'cheque_number', name: 'cheque_number'},
            @endif
            {data: 'note', name: 'note'},
            {data: 'attachment', name: 'attachment'},
            {data: 'added_by', name: 'u.first_name'},
            {data: 'debit', name: 'amount'},
            {data: 'credit', name: 'amount'},
            {data: 'balance', name: 'balance', searchable: false},
            {data: 'reconcile_status', name: 'reconcile_status', searchable: false, sortable: false},
          ],
          @include('layouts.partials.datatable_export_button')
          "fnDrawCallback": function (oSettings) {
            var debit_total = sum_table_col($('#account_book'), 'debit_col');
            $('#footer_debit_total').text(debit_total);
            var credit_total = sum_table_col($('#account_book'), 'credit_col');
            $('#footer_credit_total').text(credit_total);
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

      $('#card_type, #cheque_number, #transaction_customer, #transaction_supplier').change(function(){
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
              // update_description();

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
      url: '{{action("AccountController@getAccountBalance", [$account->id])}}',
      dataType: "json",
      success: function(data){
        $('span#account_balance').text(__currency_trans_from_en(data.balance, true));
      }
    });
  }

  {{--function update_description(){--}}
  {{--  $('span#description').html('<i class="fa fa-refresh fa-spin"></i>');--}}
  {{--  $.ajax({--}}
  {{--    url: '{{url('accounting-module/get-description/'.$account->id) }}',--}}
  {{--    dataType: "json",--}}
  {{--    success: function(data){--}}
  {{--      console.log(data);--}}
  {{--      $('span#description').text(data, true);--}}
  {{--    }--}}
  {{--  });--}}
  {{--}--}}
</script>
@endsection