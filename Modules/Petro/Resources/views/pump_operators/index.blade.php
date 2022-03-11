@extends('layouts.app')
@section('title', __('petro::lang.pump_operators'))
<style>
.disabled {
    pointer-events:none; //This makes it not clickable
    opacity:0.6;         //This grays it out to look disabled
}
</style>
@section('content')

<section class="content-header">
    <div class="row">
        <div class="col-md-12 dip_tab">
            <div class="settlement_tabs">
                <ul class="nav nav-tabs">
                    <li class=" @if(empty(session('status.tab'))) active @endif" style="margin-left: 20px;">
                        <a style="font-size:13px;" href="#pump_operators" class="" data-toggle="tab">
                            <i class="fa fa-users"></i> <strong>@lang('petro::lang.pump_operators')</strong>
                        </a>
                    </li>
                    <li class="@if(session('status.tab') == 'pumper_excess_shortage_payments') active @endif">
                        <a style="font-size:13px;" href="#pumper_excess_shortage_payments" data-toggle="tab">
                            <i class="fa fa-minus"></i>
                            <strong>@lang('petro::lang.pumper_excess_shortage_payments')</strong>
                        </a>
                    </li>
                    <li class="@if(session('status.tab') == 'pumper_day_entries') active @endif">
                        <a style="font-size:13px;" href="#pumper_day_entries" data-toggle="tab">
                            <i class="fa fa-calculator"></i>
                            <strong>@lang('petro::lang.pumper_day_entries')</strong>
                        </a>
                    </li>
                    <li class="disabled @if(session('status.tab') == 'shift_summary') active @endif">
                        <a style="font-size:13px;" href="#shift_summary" data-toggle="tab">
                            <i class="fa fa-clock-o"></i>
                            <strong>@lang('petro::lang.shift_summary')</strong>
                        </a>
                    </li>
                    <li class="@if(session('status.tab') == 'payment_summary') active @endif">
                        <a style="font-size:13px;" href="#payment_summary" data-toggle="tab">
                            <i class="fa fa-money"></i>
                            <strong>@lang('petro::lang.payment_summary')</strong>
                        </a>
                    </li>
                    <li class="@if(session('status.tab') == 'daily_pump_status') active @endif">
                        <a style="font-size:13px;" href="#daily_pump_status" data-toggle="tab">
                            <i class="fa fa-calculator"></i>
                            <strong>@lang('petro::lang.daily_pump_status')</strong>
                        </a>
                    </li>
                    <li class="@if(session('status.tab') == 'close_shift') active @endif">
                        <a style="font-size:13px;" href="#close_shift" data-toggle="tab">
                            <i class="fa fa-ban"></i>
                            <strong>@lang('petro::lang.close_shift')</strong>
                        </a>
                    </li>
                    <li class="@if(session('status.tab') == 'current_meter') active @endif">
                        <a style="font-size:13px;" href="#current_meter" data-toggle="tab">
                            <i class="fa fa-thermometer-full"></i>
                            <strong>@lang('petro::lang.current_meter')</strong>
                        </a>
                    </li>
                    <li class="@if(session('status.tab') == 'unload_stock') active @endif">
                        <a style="font-size:13px;" href="#unload_stock" data-toggle="tab">
                            <i class="fa fa-arrow-down"></i>
                            <strong>@lang('petro::lang.unload_stock')</strong>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <div class="tab-content">
        <div class="tab-pane  @if(empty(session('status.tab'))) active @endif" id="pump_operators">
            @if(!empty($message)) {!! $message !!} @endif
            @include('petro::pump_operators.partials.pump_operators')
        </div>
        <div class="tab-pane @if(session('status.tab') == 'pumper_excess_shortage_payments') active @endif" id="pumper_excess_shortage_payments">
            @if(!empty($message)) {!! $message !!} @endif
            @include('petro::pump_operators.partials.pumper_excess_shortage_payments')
        </div>
        <div class="tab-pane @if(session('status.tab') == 'pumper_day_entries') active @endif" id="pumper_day_entries">
            @if(!empty($message)) {!! $message !!} @endif
            @include('petro::pump_operators.partials.pumper_day_entries')
        </div>
        <div class="tab-pane @if(session('status.tab') == 'shift_summary') active @endif" id="shift_summary">
            @if(!empty($message)) {!! $message !!} @endif
            @include('petro::pump_operators.partials.shift_summary')
        </div>
        <div class="tab-pane @if(session('status.tab') == 'payment_summary') active @endif" id="payment_summary">
            @if(!empty($message)) {!! $message !!} @endif
            @include('petro::pump_operators.partials.payment_summary')
        </div>
        <div class="tab-pane @if(session('status.tab') == 'daily_pump_status') active @endif" id="daily_pump_status">
            @if(!empty($message)) {!! $message !!} @endif
            @include('petro::pump_operators.partials.daily_pump_status')
        </div>
        <div class="tab-pane @if(session('status.tab') == 'closing_shift') active @endif" id="close_shift">
            @if(!empty($message)) {!! $message !!} @endif
            @include('petro::pump_operators.partials.closing_shift')
        </div>
        <div class="tab-pane @if(session('status.tab') == 'current_meter') active @endif" id="current_meter">
            @if(!empty($message)) {!! $message !!} @endif
            @include('petro::pump_operators.partials.current_meter')
        </div>
        <div class="tab-pane @if(session('status.tab') == 'unload_stock') active @endif" id="unload_stock">
            @if(!empty($message)) {!! $message !!} @endif
            @include('petro::pump_operators.partials.unload_stock')
        </div>
    </div>

    <div class="modal fade pump_modal" role="dialog" aria-labelledby="gridSystemModalLabel">
    </div>

    <div class="modal fade pump_operator_modal" role="dialog" aria-labelledby="gridSystemModalLabel">
    </div>

    <div class="modal fade payment_modal" role="dialog" aria-labelledby="gridSystemModalLabel">
    </div>

    <div class="modal fade edit_payment_modal" role="dialog" aria-labelledby="gridSystemModalLabel">
    </div>

</section>

@endsection
@section('javascript')
<script src="{{ asset('js/payment.js?v=' . $asset_v) }}"></script>
<script type="text/javascript">
    var body = document.getElementsByTagName("body")[0];
    body.className += " sidebar-collapse";
    $(document).ready( function(){
    var columns = [
            { data: 'action', searchable: false, orderable: false },
            { data: 'current_status', name: 'current_status' },
            { data: 'name', name: 'name' },
            { data: 'location_name', name: 'business_locations.name' },
            { data: 'sold_fuel_qty', name: 'sold_fuel_qty' },
            { data: 'sale_amount_fuel', name: 'sale_amount_fuel' },
            { data: 'commission_rate', name: 'commission_ap' },
            { data: 'commission_amount', searchable: false  },
            { data: 'excess_amount', name: 'excess_amount' },
            { data: 'short_amount', name: 'short_amount' },
        ];
  
    list_pump_operators_table = $('#list_pump_operators_table').DataTable({
        processing: true,
        serverSide: true,
        aaSorting: [[0, 'desc']],
        ajax: {
            url: '{{action('\Modules\Petro\Http\Controllers\PumpOperatorController@index')}}',
            data: function(d) {
                d.location_id = $('select#location_id').val();
                d.pump_operator = $('select#pump_operator').val();
                d.settlement_no = $('select#settlement_no').val();
                d.type = $('select#type').val();
                d.status = $('select#status').val();
                d.start_date = $('input#expense_date_range')
                    .data('daterangepicker')
                    .startDate.format('YYYY-MM-DD');
                d.end_date = $('input#expense_date_range')
                    .data('daterangepicker')
                    .endDate.format('YYYY-MM-DD');
            },
        },
        columnDefs: [ {
            "targets": 0,
            "orderable": false,
            "searchable": false
        },{
            "targets": 3,
            "width": "1%"
        },{
            "targets": 8,
            "width": "1%"
        },{
            "targets": 9,
            "width": "1%"
        },{
            "targets": 7,
            "width": "1%"
        }
        ],
        columns: columns,
        fnDrawCallback: function(oSettings) {
            var sold_fuel_qty = sum_table_col($('#list_pump_operators_table'), 'sold_fuel_qty');
            $('#footer_sold_fuel_qty').text(sold_fuel_qty);
            var sale_amount_fuel = sum_table_col($('#list_pump_operators_table'), 'sale_amount_fuel');
            $('#footer_sale_amount_fuel').text(sale_amount_fuel);
            var commission_amount = sum_table_col($('#list_pump_operators_table'), 'commission_amount');
            $('#footer_commission_amount').text(commission_amount);
            var excess_amount = sum_table_col($('#list_pump_operators_table'), 'excess_amount');
            $('#footer_excess_amount').text(excess_amount);
            var short_amount = sum_table_col($('#list_pump_operators_table'), 'short_amount');
            $('#footer_short_amount').text(short_amount);

            __currency_convert_recursively($('#list_pump_operators_table'));
        },
    });

    $('#location_id, #pump_operator, #pump_operator, #settlement_no, #type, #date_range, #status, #expense_date_range').change(function(){
        list_pump_operators_table.ajax.reload();
    });


    $(document).on('click', 'a.delete_reference_button', function(e) {
		var page_details = $(this).closest('div.page_details')
		e.preventDefault();
        swal({
            title: LANG.sure,
            icon: 'warning',
            buttons: true,
            dangerMode: true,
        }).then(willDelete => {
            if (willDelete) {
                var href = $(this).attr('href');
                var data = $(this).serialize();
                console.log(href);
                $.ajax({
                    method: 'DELETE',
                    url: href,
                    dataType: 'json',
                    data: data,
                    success: function(result) {
                        if (result.success == true) {
                            page_details.remove();
                            toastr.success(result.msg);
                        } else {
                            toastr.error(result.msg);
                        }
                        list_pump_operators_table.ajax.reload();
                    },
                });
            }
        });
    });


    
    $(document).on('click', 'a.toggle_active_button', function(e) {
		e.preventDefault();
        swal({
            title: LANG.sure,
            icon: 'warning',
            buttons: true,
            dangerMode: true,
        }).then(willDelete => {
            if (willDelete) {
                var href = $(this).attr('href');
                $.ajax({
                    method: 'GET',
                    url: href,
                    dataType: 'json',
                    data: {},
                    success: function(result) {
                        if (result.success == true) {
                            toastr.success(result.msg);
                        } else {
                            toastr.error(result.msg);
                        }
                        list_pump_operators_table.ajax.reload();
                    },
                });
            }
        });
    });
});

$(document).on('click', '.edit_contact_button', function(e) {
    e.preventDefault();
    $('div.pump_operator_modal').load($(this).attr('href'), function() {
        $(this).modal('show');
    });
});

//pumper excess and shortage payments
$(document).ready( function(){

    if ($('#pesp_date_range').length == 1) {
        $('#pesp_date_range').daterangepicker(dateRangeSettings, function(start, end) {
            $('#pesp_date_range').val(
                start.format(moment_date_format) + ' - ' + end.format(moment_date_format)
            );
        });
        $('#pesp_date_range').on('cancel.daterangepicker', function(ev, picker) {
            $('#pesp_date_range').val('');
        });
        $('#pesp_date_range')
            .data('daterangepicker')
            .setStartDate(moment().startOf('month'));
        $('#pesp_date_range')
            .data('daterangepicker')
            .setEndDate(moment().endOf('month'));
    }
  
    pumper_excess_shortage_payments_table = $('#pumper_excess_shortage_payments_table').DataTable({
        processing: true,
        serverSide: true,
        aaSorting: [[0, 'desc']],
        ajax: {
            url: '{{action('\Modules\Petro\Http\Controllers\PumpOperatorController@getPumperExcessShortagePayments')}}',
            data: function(d) {
                d.location_id = $('select#pesp_location_id').val();
                d.pump_operator = $('select#pesp_pump_operator').val();
                d.type = $('select#pesp_type').val();
                d.payment_type = $('select#pesp_payment_type').val();
                d.start_date = $('input#pesp_date_range')
                    .data('daterangepicker')
                    .startDate.format('YYYY-MM-DD');
                d.end_date = $('input#pesp_date_range')
                    .data('daterangepicker')
                    .endDate.format('YYYY-MM-DD');
            },
        },
        columns: [
            { data: 'action', name: 'action' },
            { data: 'paid_on', name: 'transaction_payments.paid_on' },
            { data: 'location_name', name: 'business_locations.name' },
            { data: 'name', name: 'name' },
            { data: 'short_amount', name: 'short_amount' },
            { data: 'excess_amount', name: 'excess_amount' },
            { data: 'shortage_recover', name: 'transaction_payments.amount' },
            { data: 'excess_paid', name: 'transaction_payments.amount' },
        ],
        fnDrawCallback: function(oSettings) {

            __currency_convert_recursively($('#pumper_excess_shortage_payments_table'));
        },
    });
    $('#pesp_location_id, #pesp_pump_operator, #pesp_pump_operator, #pesp_type, #pesp_date_range, #pesp_payment_type').change(function(){
        pumper_excess_shortage_payments_table.ajax.reload();
    });
});

</script>


<script type="text/javascript">
    $(document).ready( function(){
    list_daily_collection_table = $('#list_daily_collection_table').DataTable({
        processing: true,
        serverSide: true,
        aaSorting: [[0, 'desc']],
        ajax: {
            url: '{{action('\Modules\Petro\Http\Controllers\PumperDayEntryController@getDailyCollection')}}',
            data: function(d) {
               
            },
        },
        columnDefs: [ {
            "targets": 0,
            "orderable": false,
            "searchable": false
        }
        ],
        columns: [
            { data: 'action', searchable: false, orderable: false },
            { data: 'date_and_time', name: 'date_and_time' },
            { data: 'name', name: 'name' },
            { data: 'pump_no', name: 'pump_no' },
            { data: 'starting_meter', name: 'starting_meter' },
            { data: 'closing_meter', name: 'closing_meter' },
            { data: 'sold_ltr', name: 'sold_ltr' },
            { data: 'sold_amount', name: 'sold_amount' },
        ],
        fnDrawCallback: function(oSettings) {
            __currency_convert_recursively($('#list_daily_collection_table'));
        },
    });
});



$(document).on('click', 'a.delete_daily_collection', function(e) {
    var page_details = $(this).closest('div.page_details')
    e.preventDefault();
    swal({
        title: LANG.sure,
        icon: 'warning',
        buttons: true,
        dangerMode: true,
    }).then(willDelete => {
        if (willDelete) {
            var href = $(this).attr('href');
            var data = $(this).serialize();
            $.ajax({
                method: 'DELETE',
                url: href,
                dataType: 'json',
                data: data,
                success: function(result) {
                    if (result.success == true) {
                        toastr.success(result.msg);
                    } else {
                        toastr.error(result.msg);
                    }
                    list_daily_collection_table.ajax.reload();
                },
            });
        }
    });
});


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
    pump_operators_day_entries_table = $('#pump_operators_day_entries_table').DataTable({
        processing: true,
        serverSide: true,
        aaSorting: [[0, 'desc']],
        ajax: {
            url: '{{action('\Modules\Petro\Http\Controllers\PumperDayEntryController@index')}}',
            data: function(d) {
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
            @if(empty(auth()->user()->pump_operator_id))
            { data: 'settlement_no', name: 'settlement_no' },
            @endif
            { data: 'name', name: 'pump_operators.name' },
            { data: 'starting_meter', name: 'starting_meter' },
            { data: 'closing_meter', name: 'closing_meter' },
            { data: 'testing_ltr', name: 'testing_ltr' },
            { data: 'sold_ltr', name: 'sold_ltr' },
            { data: 'amount', name: 'amount', searchable: false  },
            { data: 'short_amount', name: 'short_amount', searchable: false  },
        ],
        fnDrawCallback: function(oSettings) {
            var sold_ltr = sum_table_col($('#pump_operators_day_entries_table'), 'sold_ltr');
            $('#footer_sold_ltr').text(sold_ltr);
            var sold_amount = sum_table_col($('#pump_operators_day_entries_table'), 'sold_amount');
            $('#footer_sold_amount').text(sold_amount);
            var credit_sale = sum_table_col($('#pump_operators_day_entries_table'), 'credit_sale');
            $('#footer_credit_sale').text(credit_sale);
            var cards = sum_table_col($('#pump_operators_day_entries_table'), 'cards');
            $('#footer_cards').text(cards);
            var cash = sum_table_col($('#pump_operators_day_entries_table'), 'cash');
            $('#footer_cash').text(cash);
            var cheque = sum_table_col($('#pump_operators_day_entries_table'), 'cheque');
            $('#footer_cheque').text(cheque);
            var total_amount = sum_table_col($('#pump_operators_day_entries_table'), 'total_amount');
            $('#footer_total_amount').text(total_amount);
          

            __currency_convert_recursively($('#pump_operators_day_entries_table'));
        },
    });

    $('#day_entries_location_id, #day_entries_pump_operator, #day_entries_pump_operator, #day_entries_payment_method, #day_entries_date_range, #day_entries_difference').change(function(){
        pump_operators_day_entries_table.ajax.reload();
    });
});

if ($('#shift_summary_date_range').length == 1) {
    $('#shift_summary_date_range').daterangepicker(dateRangeSettings, function(start, end) {
        $('#shift_summary_date_range').val(
            start.format(moment_date_format) + ' - ' + end.format(moment_date_format)
        );
    });
    $('#shift_summary_date_range').on('cancel.daterangepicker', function(ev, picker) {
        $('#shift_summary_date_range').val('');
    });
    $('#shift_summary_date_range')
        .data('daterangepicker')
        .setStartDate(moment().startOf('month'));
    $('#shift_summary_date_range')
        .data('daterangepicker')
        .setEndDate(moment().endOf('month'));
}
$(document).ready( function(){
    pump_operators_shift_summary_table = $('#pump_operators_shift_summary_table').DataTable({
        processing: true,
        serverSide: true,
        aaSorting: [[0, 'desc']],
        ajax: {
            url: '{{action('\Modules\Petro\Http\Controllers\ShiftSummaryController@index')}}',
            data: function(d) {
                d.start_date = $('input#shift_summary_date_range')
                    .data('daterangepicker')
                    .startDate.format('YYYY-MM-DD');
                d.end_date = $('input#shift_summary_date_range')
                    .data('daterangepicker')
                    .endDate.format('YYYY-MM-DD');
                d.location_id = $('#shift_summary_location_id').val();
                d.pump_operator_id = $('#shift_summary_pump_operators').val();
                d.pump_id = $('#shift_summary_pumps').val();
                d.payment_method = $('#shift_summary_payment_method').val();
                d.difference = $('#shift_summary_difference').val();
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
            { data: 'name', name: 'pump_operators.name' },
            { data: 'pump_no', name: 'pump_no' },
            { data: 'starting_meter', name: 'starting_meter' },
            { data: 'closing_meter', name: 'closing_meter' },
            { data: 'testing_ltr', name: 'testing_ltr' },
            { data: 'sold_ltr', name: 'sold_ltr' },
            { data: 'amount', name: 'amount', searchable: false  },
            { data: 'credit_sale', name: 'credit_sale', searchable: false  },
            { data: 'cards', name: 'cards', searchable: false  },
            { data: 'cash', name: 'cash', searchable: false  },
            { data: 'cheque', name: 'cheque', searchable: false  },
            { data: 'total_amount', name: 'total_amount', searchable: false  },
            { data: 'difference', name: 'difference', searchable: false  },
        ],
        fnDrawCallback: function(oSettings) {
            var sold_ltr = sum_table_col($('#pump_operators_shift_summary_table'), 'sold_ltr');
            $('#footer_shift_summary_sold_ltr').text(sold_ltr);
            var sold_amount = sum_table_col($('#pump_operators_shift_summary_table'), 'sold_amount');
          

            __currency_convert_recursively($('#pump_operators_shift_summary_table'));
        },
    });

    $('#shift_summary_location_id, #shift_summary_pump_operator, #shift_summary_pump_operator, #shift_summary_payment_method, #shift_summary_date_range, #shift_summary_difference, #shift_summary_date_range').change(function(){
        pump_operators_shift_summary_table.ajax.reload();
    });
});

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
            url: '{{action('\Modules\Petro\Http\Controllers\PumpOperatorPaymentController@index')}}',
            data: function(d) {
                d.start_date = $('input#payment_summary_date_range')
                    .data('daterangepicker')
                    .startDate.format('YYYY-MM-DD');
                d.end_date = $('input#payment_summary_date_range')
                    .data('daterangepicker')
                    .endDate.format('YYYY-MM-DD');
                d.pump_operator_id = $('#payment_summary_pump_operators').val();
                d.payment_method = $('#payment_summary_payment_method').val();
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
            { data: 'amount', name: 'amount' },
            { data: 'note', name: 'note' },
            { data: 'edited_by', name: 'edited_by' }
        ],
        fnDrawCallback: function(oSettings) {
            var footer_payment_summary_amount = sum_table_col($('#pump_operators_payment_summary_table'), 'amount');
            $('#footer_payment_summary_amount').text(footer_payment_summary_amount);
          
            __currency_convert_recursively($('#pump_operators_payment_summary_table'));
        },
    });

    $('#payment_summary_pump_operators, #payment_summary_payment_method, #payment_summary_date_range').change(function(){
        pump_operators_payment_summary_table.ajax.reload();
    });
});



if ($('#close_shift_date_range').length == 1) {
    $('#close_shift_date_range').daterangepicker(dateRangeSettings, function(start, end) {
        $('#close_shift_date_range').val(
            start.format(moment_date_format) + ' - ' + end.format(moment_date_format)
        );
    });
    $('#close_shift_date_range').on('cancel.daterangepicker', function(ev, picker) {
        $('#close_shift_date_range').val('');
    });
    $('#close_shift_date_range')
        .data('daterangepicker')
        .setStartDate(moment().startOf('month'));
    $('#close_shift_date_range')
        .data('daterangepicker')
        .setEndDate(moment().endOf('month'));
}
$(document).ready( function(){
    pump_operators_closing_shift_table = $('#pump_operators_closing_shift_table').DataTable({
        processing: true,
        serverSide: true,
        aaSorting: [[0, 'desc']],
        ajax: {
            url: "{{action('\Modules\Petro\Http\Controllers\ClosingShiftController@index', ['only_pumper' => false])}}",
            data: function(d) {
                @if(empty(auth()->user()->pump_operator_id))
                d.start_date = $('input#close_shift_date_range')
                    .data('daterangepicker')
                    .startDate.format('YYYY-MM-DD');
                d.end_date = $('input#close_shift_date_range')
                    .data('daterangepicker')
                    .endDate.format('YYYY-MM-DD');
                d.location_id = $('#close_shift_location_id').val();
                d.pump_operator_id = $('#close_shift_pump_operators').val();
                d.pump_id = $('#close_shift_pumps').val();
                d.payment_method = $('#close_shift_payment_method').val();
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

    $('#close_shift_location_id, #close_shift_pump_operators, #close_shift_pumps, #close_shift_payment_method, #close_shift_date_range').change(function(){
        pump_operators_closing_shift_table.ajax.reload();
    });
});

// current meter tab script
if ($('#current_meter_date_range').length == 1) {
    $('#current_meter_date_range').daterangepicker(dateRangeSettings, function(start, end) {
        $('#current_meter_date_range').val(
            start.format(moment_date_format) + ' - ' + end.format(moment_date_format)
        );
    });
    $('#current_meter_date_range').on('cancel.daterangepicker', function(ev, picker) {
        $('#current_meter_date_range').val('');
    });
    $('#current_meter_date_range')
        .data('daterangepicker')
        .setStartDate(moment().startOf('month'));
    $('#current_meter_date_range')
        .data('daterangepicker')
        .setEndDate(moment().endOf('month'));
}
$(document).ready( function(){
    pump_operators_current_meter_table = $('#pump_operators_current_meter_table').DataTable({
        processing: true,
        serverSide: true,
        aaSorting: [[0, 'desc']],
        ajax: {
            url: "{{action('\Modules\Petro\Http\Controllers\CurrentMeterController@index', ['only_pumper' => false])}}",
            data: function(d) {
                d.start_date = $('input#current_meter_date_range')
                    .data('daterangepicker')
                    .startDate.format('YYYY-MM-DD');
                d.end_date = $('input#current_meter_date_range')
                    .data('daterangepicker')
                    .endDate.format('YYYY-MM-DD');
                d.pump_operator_id = $('#current_meter_pump_operators').val();
                d.pump_id = $('#current_meter_pump_no').val();
            },
        },
        columnDefs: [ {
            "targets": 0,
            "orderable": false,
            "searchable": false
        }],
        columns: [
            { data: 'date_and_time', name: 'date_and_time' },
            { data: 'pump_no', name: 'pump_no' },
            { data: 'name', name: 'pump_operators.name' },
            { data: 'starting_meter', name: 'starting_meter' },
            { data: 'last_time_meter', name: 'last_time_meter' },
            { data: 'current_meter', name: 'current_meter' },
            { data: 'amount', name: 'amount' },
            { data: 'total_sale_amount', name: 'total_sale_amount'},
        ],
        fnDrawCallback: function(oSettings) {
            __currency_convert_recursively($('#pump_operators_current_meter_table'));
        },
    });

    $('#current_meter_pump_operators, #current_meter_pump_no, #current_meter_date_range').change(function(){
        pump_operators_current_meter_table.ajax.reload();
    });
});


// current meter tab script
if ($('#unload_stock_date_range').length == 1) {
    $('#unload_stock_date_range').daterangepicker(dateRangeSettings, function(start, end) {
        $('#unload_stock_date_range').val(
            start.format(moment_date_format) + ' - ' + end.format(moment_date_format)
        );
    });
    $('#unload_stock_date_range').on('cancel.daterangepicker', function(ev, picker) {
        $('#unload_stock_date_range').val('');
    });
    $('#unload_stock_date_range')
        .data('daterangepicker')
        .setStartDate(moment().startOf('month'));
    $('#unload_stock_date_range')
        .data('daterangepicker')
        .setEndDate(moment().endOf('month'));
}
$(document).ready( function(){
    pump_operators_unload_stock_table = $('#pump_operators_unload_stock_table').DataTable({
        processing: true,
        serverSide: true,
        aaSorting: [[0, 'desc']],
        ajax: {
            url: "{{action('\Modules\Petro\Http\Controllers\UnloadStockController@index', ['only_pumper' => true])}}",
            data: function(d) {
                d.start_date = $('input#unload_stock_date_range')
                    .data('daterangepicker')
                    .startDate.format('YYYY-MM-DD');
                d.end_date = $('input#unload_stock_date_range')
                    .data('daterangepicker')
                    .endDate.format('YYYY-MM-DD');
                d.tank_id = $('#unload_stock_tank_id').val();
                d.product_id = $('#unload_stock_product_id').val();
            },
        },
        columnDefs: [ {
            "targets": 0,
            "orderable": false,
            "searchable": false
        }],
        columns: [
            { data: 'date_and_time', name: 'date_and_time' },
            { data: 'fuel_tank_number', name: 'fuel_tank_number' },
            { data: 'product', name: 'product' },
            { data: 'dip_reading', name: 'dip_reading' },
            { data: 'current_stock', name: 'current_stock' },
            { data: 'unloaded_qty', name: 'unloaded_qty' },
            { data: 'total_qty', name: 'total_qty' },
            { data: 'username', name: 'users.username'},
        ],
        fnDrawCallback: function(oSettings) {
            __currency_convert_recursively($('#pump_operators_unload_stock_table'));
        },
    });

    $('#unload_stock_product_id, #unload_stock_tank_id, #unload_stock_date_range').change(function(){
        pump_operators_unload_stock_table.ajax.reload();
    });
});



</script>
@endsection