@extends('layouts.app')
@section('title', __('report.payment_status_report'))

@section('content')
<!-- Main content -->
<section class="content">

    <div class="row no-print">
        <div class="col-md-12">
            <div class="settlement_tabs no-print">
                <ul class="nav nav-tabs  no-print">
                    @can('purchase_payment_report.view')
                    <li class="active">
                        <a href="#purchase_payment_report" class="purchase_payment_report" data-toggle="tab">
                            <i class="fa fa-file-text-o"></i> <strong>@lang('report.purchase_payment_report')</strong>
                        </a>
                    </li>
                    @endcan

                    @can('sell_payment_report.view')
                    <li class="">
                        <a href="#sell_payment_report" class="sell_payment_report" data-toggle="tab">
                            <i class="fa fa-file-text-o"></i> <strong>@lang('report.sell_payment_report')</strong>
                        </a>
                    </li>
                    @endcan

                    @can('outstanding_received_report.view')
                    <li class="">
                        <a href="#outstanding_report" class="outstanding_report" data-toggle="tab">
                            <i class="fa fa-file-text-o"></i> <strong>@lang('report.outstanding_report')</strong>
                        </a>
                    </li>
                    @endcan

                    @can('aging_report.view')
                    <li class="">
                        <a href="#aging_report" class="aging_report" data-toggle="tab">
                            <i class="fa fa-file-text-o"></i> <strong>@lang('report.aging_report')</strong>
                        </a>
                    </li>
                    @endcan

                </ul>
                <div class="tab-content">
                    @can('purchase_payment_report.view')
                    <div class="tab-pane active" id="purchase_payment_report">
                        @include('report.purchase_payment_report')
                    </div>
                    @endcan

                    @can('sell_payment_report.view')
                    <div class="tab-pane" id="sell_payment_report">
                        @include('report.sell_payment_report')
                    </div>
                    @endcan

                    @can('outstanding_received_report.view')
                    <div class="tab-pane" id="outstanding_report">
                        @include('report.outstanding_report')
                    </div>
                    @endcan

                    @can('aging_report.view')
                    <div class="tab-pane" id="aging_report">
                        @include('report.aging_report')
                    </div>
                    @endcan
                </div>
            </div>
        </div>
    </div>

</section>
<!-- /.content -->

@endsection
@section('javascript')
<script src="{{ asset('js/report.js?v=' . $asset_v) }}"></script>
<script src="{{ asset('js/payment.js?v=' . $asset_v) }}"></script>

<script>
    //Date range as a button
    if ($('#outstanding_report_date_filter').length == 1) {
        $('#outstanding_report_date_filter').daterangepicker(dateRangeSettings, function(start, end) {
            $('#outstanding_report_date_filter span').val(
                start.format(moment_date_format) + ' ~ ' + end.format(moment_date_format)
            );
            outstanding_report_table.ajax.reload();
        });
        $('#outstanding_report_date_filter').on('cancel.daterangepicker', function(ev, picker) {
            $('#outstanding_report_date_filter').val('');
            outstanding_report_table.ajax.reload();
        });
        $('#outstanding_report_date_filter').data('daterangepicker').setStartDate(moment().startOf('month'));
        $('#outstanding_report_date_filter').data('daterangepicker').setEndDate(moment().endOf('month'));
    }
    $(document).ready(function(){
        outstanding_report_table = $('#outstanding_report_table').DataTable({
            processing: true,
            serverSide: true,
            aaSorting: [[0, 'desc']],
            "ajax": {
                "url": "{{action('ReportController@getOutstandingReport')}}",
                "data": function ( d ) {
                    if($('#outstanding_report_date_filter').val()) {
                        var start = $('#outstanding_report_date_filter').data('daterangepicker').startDate.format('YYYY-MM-DD');
                        var end = $('#outstanding_report_date_filter').data('daterangepicker').endDate.format('YYYY-MM-DD');
                        d.start_date = start;
                        d.end_date = end;
                    }
                    d.customer_id = $('#outstanding_customer_id').val();
                }
            },
            columnDefs: [ {
                "targets": [6],
                "orderable": false,
                "searchable": false
            } ],
            columns: [
                { data: 'paid_on', name: 'tp.paid_on'  },
                { data: 'name', name: 'contacts.name'},
                { data: 'invoice_no', name: 'invoice_no'},
                { data: 'transaction_date', name: 'transaction_date'  },
                { data: 'final_total', name: 'final_total'},
                { data: 'total_paid', name: 'total_paid', "searchable": false},
                { data: 'cheque_number', name: 'cheque_number'},
                { data: 'action', name: 'action'}
            ],
            buttons: [
                {
                    extend: 'csv',
                    text: '<i class="fa fa-file"></i> Export to CSV',
                    className: 'btn btn-default btn-sm',
                    title: 'Outstanding Received Report',
                    exportOptions: {
                        columns: function ( idx, data, node ) {
                            return $(node).is(":visible") && !$(node).hasClass('notexport') ?
                                true : false;
                        } 
                    },
                },
                {
                    extend: 'excel',
                    text: '<i class="fa fa-file-excel-o"></i> Export to Excel',
                    className: 'btn btn-default btn-sm',
                    title: 'Outstanding Received Report',
                    exportOptions: {
                        columns: function ( idx, data, node ) {
                            return $(node).is(":visible") && !$(node).hasClass('notexport') ?
                                true : false;
                        } 
                    },
                },
                {
                    extend: 'colvis',
                    text: '<i class="fa fa-columns"></i> Column Visibility',
                    className: 'btn btn-default btn-sm',
                    title: 'Outstanding Received Report',
                    exportOptions: {
                        columns: function ( idx, data, node ) {
                            return $(node).is(":visible") && !$(node).hasClass('notexport') ?
                                true : false;
                        } 
                    },
                },
                {
                    extend: 'pdf',
                    text: '<i class="fa fa-file-pdf-o"></i> Export to PDF',
                    className: 'btn btn-default btn-sm',
                    title: 'Outstanding Received Report',
                    exportOptions: {
                        columns: function ( idx, data, node ) {
                            return $(node).is(":visible") && !$(node).hasClass('notexport') ?
                                true : false;
                        } 
                    },
                },
                {
                    extend: 'print',
                    text: '<i class="fa fa-print"></i> Print',
                    className: 'btn btn-default btn-sm',
                    title: 'Outstanding Received Report',
                    exportOptions: {
                        columns: function ( idx, data, node ) {
                            return $(node).is(":visible") && !$(node).hasClass('notexport') ?
                                true : false;
                        } 
                    },
                    customize: function (win) {
                        $(win.document.body).find('h1').css('text-align', 'center');
                        $(win.document.body).find('h1').css('font-size', '25px');
                    },
                },
            ],
            "fnDrawCallback": function (oSettings) {

                $('#footer_sale_total').text(sum_table_col($('#outstanding_report_table'), 'final-total'));
                
                $('#footer_total_paid').text(sum_table_col($('#outstanding_report_table'), 'total-paid'));

                $('#footer_total_remaining').text(sum_table_col($('#outstanding_report_table'), 'payment_due'));

                $('#footer_total_sell_return_due').text(sum_table_col($('#outstanding_report_table'), 'sell_return_due'));

                $('#footer_payment_status_count').html(__sum_status_html($('#outstanding_report_table'), 'payment-status-label'));
                __currency_convert_recursively($('#outstanding_report_table'));
            },
     
        });
    });

    $(document).on('change', '#outstanding_report_date_filter, #outstanding_customer_id',  function() {
        outstanding_report_table.ajax.reload();
    });

</script>


<script>
    //Date range as a button
    if ($('#aging_report_date_filter').length == 1) {
        $('#aging_report_date_filter').daterangepicker(dateRangeSettings, function(start, end) {
            $('#aging_report_date_filter span').val(
                start.format(moment_date_format) + ' ~ ' + end.format(moment_date_format)
            );
            aging_report_table.ajax.reload();
        });
        $('#aging_report_date_filter').on('cancel.daterangepicker', function(ev, picker) {
            $('#aging_report_date_filter').val('');
            aging_report_table.ajax.reload();
        });
        $('#aging_report_date_filter').data('daterangepicker').setStartDate(moment().startOf('month'));
        $('#aging_report_date_filter').data('daterangepicker').setEndDate(moment().endOf('month'));
    }
$(document).ready(function(){
    aging_report_table = $('#aging_report_table').DataTable({
        processing: true,
        serverSide: true,
        "ajax": {
            "url": "{{action('ReportController@getAgingReport')}}",
            "data": function ( d ) {
                if($('#aging_report_date_filter').val()) {
                    var start = $('#aging_report_date_filter').data('daterangepicker').startDate.format('YYYY-MM-DD');
                    var end = $('#aging_report_date_filter').data('daterangepicker').endDate.format('YYYY-MM-DD');
                    d.start_date = start;
                    d.end_date = end;
                }
                d.customer_id = $('#aging_customer_id').val();
                d.no_of_days_over = $('#no_of_days_over').val();
            }
        },
        buttons: [
                {
                    extend: 'csv',
                    text: '<i class="fa fa-file"></i> Export to CSV',
                    className: 'btn btn-default btn-sm',
                    title: 'Aging Report',
                    exportOptions: {
                        columns: function ( idx, data, node ) {
                            return $(node).is(":visible") && !$(node).hasClass('notexport') ?
                                true : false;
                        } 
                    },
                },
                {
                    extend: 'excel',
                    text: '<i class="fa fa-file-excel-o"></i> Export to Excel',
                    className: 'btn btn-default btn-sm',
                    title: 'Aging Report',
                    exportOptions: {
                        columns: function ( idx, data, node ) {
                            return $(node).is(":visible") && !$(node).hasClass('notexport') ?
                                true : false;
                        } 
                    },
                },
                {
                    extend: 'colvis',
                    text: '<i class="fa fa-columns"></i> Column Visibility',
                    className: 'btn btn-default btn-sm',
                    title: 'Aging Report',
                    exportOptions: {
                        columns: function ( idx, data, node ) {
                            return $(node).is(":visible") && !$(node).hasClass('notexport') ?
                                true : false;
                        } 
                    },
                },
                {
                    extend: 'pdf',
                    text: '<i class="fa fa-file-pdf-o"></i> Export to PDF',
                    className: 'btn btn-default btn-sm',
                    title: 'Aging Report',
                    exportOptions: {
                        columns: function ( idx, data, node ) {
                            return $(node).is(":visible") && !$(node).hasClass('notexport') ?
                                true : false;
                        } 
                    },
                },
                {
                    extend: 'print',
                    text: '<i class="fa fa-print"></i> Print',
                    className: 'btn btn-default btn-sm',
                    title: 'Aging Report',
                    exportOptions: {
                        columns: function ( idx, data, node ) {
                            return $(node).is(":visible") && !$(node).hasClass('notexport') ?
                                true : false;
                        } 
                    },
                    customize: function (win) {
                        $(win.document.body).find('h1').css('text-align', 'center');
                        $(win.document.body).find('h1').css('font-size', '20px');
                    },
                },
            ],
        columns: [
            { data: 'transaction_date', name: 'transaction_date'  },
            { data: 'name', name: 'contacts.name'},
            { data: 'final_total', name: 'final_total'},
            { data: '1_30_days', name: '1_30_days'},
            { data: '31_45_days', name: '31_45_days'},
            { data: '46_60_days', name: '46_60_days'},
            { data: '61_90_days', name: '61_90_days'},
            { data: 'over_90_days', name: 'over_90_days'},
            { data: 'action', name: 'action', orderable: false, searchable: false }
        ],
        "fnDrawCallback": function (oSettings) {
            $('#footer_total_amount_aging').text(sum_table_col($('#aging_report_table'), 'final-total-aging'));
            __currency_convert_recursively($('#aging_report_table'))
        },
        rowCallback: function( row, data, index ) {
        var no_of_days_over = $('#no_of_days_over').val();
        
        if(no_of_days_over != ''){
            if (parseInt(data['days_over']) <= parseInt(no_of_days_over)) {
                $(row).hide();
            }

        }
        },
       
        
    });
})

    $(document).on('change', '#aging_report_date_filter, #aging_customer_id, #no_of_days_over',  function() {
        aging_report_table.ajax.reload();
    });
</script>

@endsection