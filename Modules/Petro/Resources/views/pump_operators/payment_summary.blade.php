@extends('layouts.'.$layout)
@section('title', __('petro::lang.payment_summary'))

@section('content')
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>@lang('petro::lang.payment_summary')
    </h1>
    <a href="{{action('Auth\PumpOperatorLoginController@logout')}}" class="btn btn-flat btn-lg pull-right"
    style=" background-color: orange; color: #fff; margin-left: 5px;">@lang('petro::lang.logout')</a>
    <a href="{{action('\Modules\Petro\Http\Controllers\PumpOperatorController@dashboard')}}"
        class="btn btn-flat btn-lg pull-right"
        style="color: #fff; background-color:#810040;">@lang('petro::lang.dashboard')
    </a>

</section>
<div class="clearfix"></div>
@include('petro::pump_operators.partials.payment_summary')


@endsection
@section('javascript')
<script type="text/javascript">
    var body = document.getElementsByTagName("body")[0];
    body.className += " sidebar-collapse";
    
if ($('#payment_summary_date_range').length == 1) {
    $('#payment_summary_date_range').daterangepicker(dateRangeSettings, function(start, end) {
        $('#payment_summary_date_range').val(
            start.format(moment_date_format) + ' - ' + end.format(moment_date_format)
        );
    });
    $('#payment_summary_date_range').on('cancel.daterangepicker', function(ev, picker) {
        $('#payment_summary_date_range').val('');
    });
    $('#payment_summary_date_range')
        .data('daterangepicker')
        .setStartDate(moment().startOf('month'));
    $('#payment_summary_date_range')
        .data('daterangepicker')
        .setEndDate(moment().endOf('month'));
}
$(document).ready( function(){
    pump_operators_payment_summary_table = $('#pump_operators_payment_summary_table').DataTable({
        processing: true,
        serverSide: true,
        aaSorting: [[0, 'desc']],
        ajax: {
            url: "{{action('\Modules\Petro\Http\Controllers\PumpOperatorPaymentController@index', ['only_pumper' => true])}}",
            data: function(d) {
            },
        },
        columnDefs: [ {
            "targets": 0,
            "orderable": false,
            "searchable": false
        }],
        columns: [
            { data: 'action', name: 'action' },
            { data: 'date', name: 'date' },
            { data: 'time', name: 'time' },
            { data: 'pump_operator_name', name: 'pump_operators.name' },
            { data: 'payment_type', name: 'payment_type' },
            { data: 'amount', name: 'amount' }
        ],
        fnDrawCallback: function(oSettings) {
            var footer_payment_summary_amount = sum_table_col($('#pump_operators_payment_summary_table'), 'amount');
            $('#footer_payment_summary_amount').text(footer_payment_summary_amount);
          
            __currency_convert_recursively($('#pump_operators_payment_summary_table'));
        },
    });
});

</script>
@endsection