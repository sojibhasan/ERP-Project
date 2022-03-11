@extends('layouts.'.$layout)
@section('title', __('petro::lang.close_shift'))
@section('content')
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>@lang('petro::lang.close_shift') <br>
        <span class="text-red">{{$pump_operator_name}}</span>
    </h1>
    <a href="{{action('Auth\PumpOperatorLoginController@logout')}}" class="btn btn-flat btn-lg pull-right"
        style=" background-color: orange; color: #fff; margin-left: 5px;">@lang('petro::lang.logout')</a>
    <a href="{{action('\Modules\Petro\Http\Controllers\PumpOperatorController@dashboard')}}"
        class="btn btn-flat btn-lg pull-right"
        style="color: #fff; background-color:#810040; margin-left: 5px;">@lang('petro::lang.dashboard')
    </a>
    <a data-href="{{action('\Modules\Petro\Http\Controllers\PumpOperatorPaymentController@getPaymentSummaryModal', ['only_pumper' => true])}}"
        class="btn btn-flat btn-lg pull-right btn-modal" data-container=".view_modal"
        style="color: #fff; background-color:#71b306;">@lang('petro::lang.payment_summary')
    </a>
</section>
<div class="clearfix"></div>
@include('petro::pump_operators.partials.closing_shift')
@endsection
@section('javascript')
<script>
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
  </script>
<script type="text/javascript">
    var body = document.getElementsByTagName("body")[0];
    body.className += " sidebar-collapse";
    if ($('#date_range').length == 1) {
        $('#date_range').daterangepicker(dateRangeSettings, function(start, end) {
            $('#date_range').val(
                start.format(moment_date_format) + ' - ' + end.format(moment_date_format)
            );
        });
        $('#date_range').on('cancel.daterangepicker', function(ev, picker) {
            $('#date_range').val('');
        });
        $('#date_range')
            .data('daterangepicker')
            .setStartDate(moment().startOf('month'));
        $('#date_range')
            .data('daterangepicker')
            .setEndDate(moment().endOf('month'));
    }
$(document).ready( function(){
    pump_operators_closing_shift_table = $('#pump_operators_closing_shift_table').DataTable({
        processing: true,
        serverSide: true,
        aaSorting: [[0, 'desc']],
        ajax: {
            url: "{{action('\Modules\Petro\Http\Controllers\ClosingShiftController@index', ['only_pumper' => true])}}",
            data: function(d) {
                @if(empty(auth()->user()->pump_operator_id))
                d.start_date = $('input#date_range')
                    .data('daterangepicker')
                    .startDate.format('YYYY-MM-DD');
                d.end_date = $('input#date_range')
                    .data('daterangepicker')
                    .endDate.format('YYYY-MM-DD');
                d.location_id = $('#day_entries_location_id').val();
                d.pump_operator_id = $('#day_entries_pump_operators').val();
                d.pump_id = $('#day_entries_pumps').val();
                d.payment_method = $('#day_entries_payment_method').val();
                d.difference = $('#day_entries_difference').val();
                @endif
            },
        },
        columnDefs: [ {
            "targets": 0,
            "orderable": false,
            "searchable": false
        }],
        columns: [
            { data: 'action', searchable: false, orderable: false },
            { data: 'date', name: 'date' },
            { data: 'time', name: 'time' },
            { data: 'name', name: 'pump_operators.name' },
            { data: 'starting_meter', name: 'starting_meter' },
            { data: 'closing_meter', name: 'closing_meter' },
            { data: 'testing_ltr', name: 'testing_ltr' },
            { data: 'sold_ltr', name: 'sold_ltr' },
            { data: 'amount', name: 'amount', searchable: false  },
            { data: 'short_amount', name: 'short_amount', searchable: false  },
        ],
        fnDrawCallback: function(oSettings) {
            var testing_ltr = sum_table_col($('#pump_operators_closing_shift_table'), 'testing_ltr');
            $('#footer_cs_testing_ltr').text(testing_ltr);
            var sold_ltr = sum_table_col($('#pump_operators_closing_shift_table'), 'sold_ltr');
            $('#footer_cs_sold_ltr').text(sold_ltr);
            var sold_amount = sum_table_col($('#pump_operators_closing_shift_table'), 'sold_amount');
            $('#footer_cs_sold_amount').text(sold_amount);
            var short_amount = sum_table_col($('#pump_operators_closing_shift_table'), 'short_amount');
            $('#footer_cs_short_amount').text(short_amount);
            __currency_convert_recursively($('#pump_operators_closing_shift_table'));
        },
    });
    $('#day_entries_location_id, #day_entries_pump_operator, #day_entries_pump_operator, #day_entries_payment_method, #day_entries_date_range, #day_entries_difference').change(function(){
        pump_operators_closing_shift_table.ajax.reload();
    });
});
$(document).on('shown.bs.modal', '.view_modal', function(){
    $('#amount').focus();
});
</script>
@endsection