@extends('layouts.app')
@section('title', __('report.management_report'))

@section('content')
<!-- Main content -->
<section class="content">

    <div class="row">
        <div class="col-md-12">
            <div class="settlement_tabs">
                <ul class="nav nav-tabs">
                    @if($report_daily)
                        @can('daily_report.view')
                        <li class=" @if($tab != 'credit_status') active @endif">
                            <a href="#daily_report" class="daily_report" data-toggle="tab">
                                <i class="fa fa-file-text-o"></i> <strong>@lang('report.daily_report')</strong>
                            </a>
                        </li>
                        @endcan
                    @endif
                    
                    @if($report_daily_summary)
                        @can('daily_summary_report.view')
                        <li class="@if($tab == 'daily_summary_report') active @endif">
                            <a href="#daily_summary_report" class="daily_summary_report" data-toggle="tab">
                                <i class="fa fa-file-text-o"></i> <strong>@lang('report.daily_summary_report')</strong>
                            </a>
                        </li>
                        @endcan
                    @endif
                    
                    @if($report_register)
                        @can('register_report.view')
                        <li class="@if($tab == 'register_report') active @endif">
                            <a href="#register_report" class="register_report" data-toggle="tab">
                                <i class="fa fa-file-text-o"></i> <strong>@lang('report.register_report')</strong>
                            </a>
                        </li>
                        @endcan
                    @endif
                    
                    @if($report_profit_loss)
                        @can('profit_loss_report.view')
                        <li class="@if($tab == 'profit_loss') active @endif">
                            <a href="#profit_loss" class="profit_loss" data-toggle="tab">
                                <i class="fa fa-file-text-o"></i> <strong>@lang('report.profit_loss')</strong>
                            </a>
                        </li>
                        @endcan
                    @endif
                    
                    @if($report_credit_status)
                        @can('credit_status.view')
                        <li class=" @if($tab == 'credit_status') active @endif">
                            <a href="#credit_status" class="credit_status" data-toggle="tab">
                                <i class="fa fa-file-text-o"></i> <strong>@lang('report.credit_status')</strong>
                            </a>
                        </li>
                        @endcan
                    @endif

                </ul>
                <div class="tab-content">
                    @if($report_daily)
                        @can('daily_report.view')
                        <div class="tab-pane  @if($tab != 'credit_status') active @endif" id="daily_report">
                            @include('report.partials.daily_report_header')
                        </div>
                        @endcan
                    @endif
                    
                    @if($report_daily_summary)
                        @can('daily_summary_report.view')
                        <div class="tab-pane @if(!$report_daily) active @endif" id="daily_summary_report">
                            @include('report.partials.daily_summary_report_header')
                        </div>
                        @endcan
                    @endif
                    
                    @if($report_register)
                        @can('register_report.view')
                        <div class="tab-pane @if(!$report_daily_summary) active @endif " id="register_report">
                            @include('report.register_report')
                        </div>
                        @endcan
                    @endif
                    
                    @if($report_profit_loss)
                        @can('profit_loss_report.view')
                        <div class="tab-pane @if(!$report_register) active @endif" id="profit_loss">
                            @include('report.profit_loss')
                        </div>
                        @endcan
                    @endif
                    
                    @if($report_credit_status)
                        @can('credit_status.view')
                        <div class="tab-pane @if($tab == 'credit_status') active @endif" id="credit_status">
                            @include('report.credit_status')
                        </div>
                        @endcan
                    @endif
                </div>
            </div>
        </div>
    </div>

</section>
<!-- /.content -->

@endsection
@section('javascript')
<script src="{{ asset('js/report.js?v=' . $asset_v) }}"></script>
<script src="https://code.highcharts.com/highcharts.js"></script>
{!! $sells_chart_1->script() !!}
<script>
    var body = document.getElementsByTagName("body")[0];
    body.className += " sidebar-collapse";
    $(document).ready(function(){
    $('.credit_filter_change').change(function(){
        var start_date = $('input#credit_status_date_range')
            .data('daterangepicker')
            .startDate.format('YYYY-MM-DD');
        var end_date = $('input#credit_status_date_range')
            .data('daterangepicker')
            .endDate.format('YYYY-MM-DD');

        $('#credit_start_date').val(start_date);
        $('#credit_end_date').val(end_date);
        $('#credit_filter_form').submit();
    })
  })
    if ($('#daily_report_date_range').length == 1) {
        $('#daily_report_date_range').daterangepicker(dateRangeSettings, function(start, end) {
            $('#daily_report_date_range').val(
                start.format(moment_date_format) + ' - ' + end.format(moment_date_format)
            );
        });
        $('#daily_report_date_range').on('cancel.daterangepicker', function(ev, picker) {
            $('#product_sr_date_filter').val('');
        });
        $('#daily_report_date_range')
            .data('daterangepicker')
            .setStartDate(moment().startOf('month'));
        $('#daily_report_date_range')
            .data('daterangepicker')
            .setEndDate(moment().endOf('month'));
    }

    $('.daily_report_change').change(function(){
        getDailyReport();
    });
    $(document).ready( function() {
        @if($report_daily)
            getDailyReport();
        @endif
    });

    function getDailyReport(){
        var location_id = $('#daily_report_location_id').val();
        var work_shift = $('#daily_report_work_shift').val();
        var start_date = $('input#daily_report_date_range')
            .data('daterangepicker')
            .startDate.format('YYYY-MM-DD');
        var end_date = $('input#daily_report_date_range')
            .data('daterangepicker')
            .endDate.format('YYYY-MM-DD');
        var dr_loader = '<div class="row text-center"><i class="fa fa-refresh fa-spin fa-fw margin-bottom"></i></div>';
        $('.daily_report_content').html(dr_loader);
        $.ajax({
            method: 'get',
            url: '/reports/daily-report',
            data: { 
                location_id,
                work_shift,
                start_date,
                end_date,
             },
             contentType: 'html',
            success: function(result) {
                $('.daily_report_content').empty().append(result);
            },
        });
    }



    function printDailyReport() {
		var w = window.open('', '_self');
		var html = document.getElementById("daily_report_div").innerHTML;
		$(w.document.body).html(html);
		w.print();
        w.close();
        location.reload();
	}
</script>



<script>

    if ($('#daily_summary_report_date_range').length == 1) {
        $('#daily_summary_report_date_range').daterangepicker(dateRangeSettings, function(start, end) {
            $('#daily_summary_report_date_range').val(
               start.format(moment_date_format) + ' - ' +  end.format(moment_date_format)
            );
        });
        $('#daily_summary_report_date_range').on('cancel.daterangepicker', function(ev, picker) {
            $('#product_sr_date_filter').val('');
        });
        $('#daily_summary_report_date_range')
            .data('daterangepicker')
            .setStartDate(moment().startOf('month'));
        $('#daily_summary_report_date_range')
            .data('daterangepicker')
            .setEndDate(moment().endOf('month'));
    }

    $('.daily_summary_report_change').change(function(){
        getDailySummaryReport();
    });

    $(document).ready( function() {
        @if($report_daily_summary)
            getDailySummaryReport();
        @endif
    });

    function getDailySummaryReport(){
        var location_id = $('#daily_summary_report_location_id').val();
        var work_shift = $('#daily_summary_report_work_shift').val();
        var start_date = $('input#daily_summary_report_date_range')
            .data('daterangepicker')
            .startDate.format('YYYY-MM-DD');
        var end_date = $('input#daily_summary_report_date_range')
            .data('daterangepicker')
            .endDate.format('YYYY-MM-DD');

        var dsr_loader = '<div class="row text-center"><i class="fa fa-refresh fa-spin fa-fw margin-bottom"></i></div>';
        $('.daily_summary_report_content').html(dsr_loader);

        $.ajax({
            method: 'get',
            url: '/reports/daily-summary-report',
            data: { 
                location_id,
                work_shift,
                start_date,
                end_date,
             },
             contentType: 'html',
            success: function(result) {
                $('.daily_summary_report_content').empty().append(result);
            },
        });
    }


    function printDailySummaryDiv() {
		var w = window.open('', '_self');
		var html = document.getElementById("daily_summary_report_div").innerHTML;
		$(w.document.body).html(html);
		w.print();
        w.close();
        location.reload();
	}

</script>


<script type="text/javascript">
    $(document).ready( function() {
        @if($report_profit_loss)
        $('#profit_tabs_filter').daterangepicker(dateRangeSettings, function(start, end) {
            $('#profit_tabs_filter span').html(
                start.format(moment_date_format) + ' ~ ' + end.format(moment_date_format)
            );
            $('.nav-tabs li.active').find('a[data-toggle="tab"]').trigger('shown.bs.tab');
        });
        $('#profit_tabs_filter').on('cancel.daterangepicker', function(ev, picker) {
            $('#profit_tabs_filter').html(
                '<i class="fa fa-calendar"></i> ' + LANG.filter_by_date
            );
            $('.nav-tabs li.active').find('a[data-toggle="tab"]').trigger('shown.bs.tab');
        });
        @endif
        profit_by_products_table = $('#profit_by_products_table').DataTable({
                processing: true,
                serverSide: true,
                "ajax": {
                    "url": "/reports/get-profit/product",
                    "data": function ( d ) {
                        d.start_date = $('#profit_tabs_filter')
                            .data('daterangepicker')
                            .startDate.format('YYYY-MM-DD');
                        d.end_date = $('#profit_tabs_filter')
                            .data('daterangepicker')
                            .endDate.format('YYYY-MM-DD');
                    }
                },
                columns: [
                    { data: 'product', name: 'P.name'  },
                    { data: 'gross_profit', "searchable": false},
                ],
                fnDrawCallback: function(oSettings) {
                    var total_profit = sum_table_col($('#profit_by_products_table'), 'gross-profit');
                    $('#profit_by_products_table .footer_total').text(total_profit);

                    __currency_convert_recursively($('#profit_by_products_table'));
                },
            });

        $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
            var target = $(e.target).attr('href');
            if ( target == '#profit_by_categories') {
                if(typeof profit_by_categories_datatable == 'undefined') {
                    profit_by_categories_datatable = $('#profit_by_categories_table').DataTable({
                        processing: true,
                        serverSide: true,
                        "ajax": {
                            "url": "/reports/get-profit/category",
                            "data": function ( d ) {
                                d.start_date = $('#profit_tabs_filter')
                                    .data('daterangepicker')
                                    .startDate.format('YYYY-MM-DD');
                                d.end_date = $('#profit_tabs_filter')
                                    .data('daterangepicker')
                                    .endDate.format('YYYY-MM-DD');
                            }
                        },
                        columns: [
                            { data: 'category', name: 'C.name'  },
                            { data: 'gross_profit', "searchable": false},
                        ],
                        fnDrawCallback: function(oSettings) {
                            var total_profit = sum_table_col($('#profit_by_categories_table'), 'gross-profit');
                            $('#profit_by_categories_table .footer_total').text(total_profit);

                            __currency_convert_recursively($('#profit_by_categories_table'));
                        },
                    });
                } else {
                    profit_by_categories_datatable.ajax.reload();
                }
            } else if (target == '#profit_by_brands') {
                if(typeof profit_by_brands_datatable == 'undefined') {
                    profit_by_brands_datatable = $('#profit_by_brands_table').DataTable({
                        processing: true,
                        serverSide: true,
                        "ajax": {
                            "url": "/reports/get-profit/brand",
                            "data": function ( d ) {
                                d.start_date = $('#profit_tabs_filter')
                                    .data('daterangepicker')
                                    .startDate.format('YYYY-MM-DD');
                                d.end_date = $('#profit_tabs_filter')
                                    .data('daterangepicker')
                                    .endDate.format('YYYY-MM-DD');
                            }
                        },
                        columns: [
                            { data: 'brand', name: 'B.name'  },
                            { data: 'gross_profit', "searchable": false},
                        ],
                        fnDrawCallback: function(oSettings) {
                            var total_profit = sum_table_col($('#profit_by_brands_table'), 'gross-profit');
                            $('#profit_by_brands_table .footer_total').text(total_profit);

                            __currency_convert_recursively($('#profit_by_brands_table'));
                        },
                    });
                } else {
                    profit_by_brands_datatable.ajax.reload();
                }
            } else if (target == '#profit_by_locations') {
                if(typeof profit_by_locations_datatable == 'undefined') {
                    profit_by_locations_datatable = $('#profit_by_locations_table').DataTable({
                        processing: true,
                        serverSide: true,
                        "ajax": {
                            "url": "/reports/get-profit/location",
                            "data": function ( d ) {
                                d.start_date = $('#profit_tabs_filter')
                                    .data('daterangepicker')
                                    .startDate.format('YYYY-MM-DD');
                                d.end_date = $('#profit_tabs_filter')
                                    .data('daterangepicker')
                                    .endDate.format('YYYY-MM-DD');
                            }
                        },
                        columns: [
                            { data: 'location', name: 'L.name'  },
                            { data: 'gross_profit', "searchable": false},
                        ],
                        fnDrawCallback: function(oSettings) {
                            var total_profit = sum_table_col($('#profit_by_locations_table'), 'gross-profit');
                            $('#profit_by_locations_table .footer_total').text(total_profit);

                            __currency_convert_recursively($('#profit_by_locations_table'));
                        },
                    });
                } else {
                    profit_by_locations_datatable.ajax.reload();
                }
            } else if (target == '#profit_by_invoice') {
                if(typeof profit_by_invoice_datatable == 'undefined') {
                    profit_by_invoice_datatable = $('#profit_by_invoice_table').DataTable({
                        processing: true,
                        serverSide: true,
                        "ajax": {
                            "url": "/reports/get-profit/invoice",
                            "data": function ( d ) {
                                d.start_date = $('#profit_tabs_filter')
                                    .data('daterangepicker')
                                    .startDate.format('YYYY-MM-DD');
                                d.end_date = $('#profit_tabs_filter')
                                    .data('daterangepicker')
                                    .endDate.format('YYYY-MM-DD');
                            }
                        },
                        columns: [
                            { data: 'invoice_no', name: 'sale.invoice_no'  },
                            { data: 'final_total', name: 'final_total'  },
                            { data: 'gross_profit', "searchable": false},
                        ],
                        fnDrawCallback: function(oSettings) {
                            var total_profit = sum_table_col($('#profit_by_invoice_table'), 'gross-profit');
                            $('#profit_by_invoice_table .footer_total').text(total_profit);
                            var footer_final_total = sum_table_col($('#profit_by_invoice_table'), 'final-total');
                            $('#profit_by_invoice_table .footer_final_total').text(footer_final_total);

                            __currency_convert_recursively($('#profit_by_invoice_table'));
                        },
                    });
                } else {
                    profit_by_invoice_datatable.ajax.reload();
                }
            } else if (target == '#profit_by_date') {
                if(typeof profit_by_date_datatable == 'undefined') {
                    profit_by_date_datatable = $('#profit_by_date_table').DataTable({
                        processing: true,
                        serverSide: true,
                        "ajax": {
                            "url": "/reports/get-profit/date",
                            "data": function ( d ) {
                                d.start_date = $('#profit_tabs_filter')
                                    .data('daterangepicker')
                                    .startDate.format('YYYY-MM-DD');
                                d.end_date = $('#profit_tabs_filter')
                                    .data('daterangepicker')
                                    .endDate.format('YYYY-MM-DD');
                            }
                        },
                        columns: [
                            { data: 'transaction_date', name: 'sale.transaction_date'  },
                            { data: 'gross_profit', "searchable": false},
                        ],
                        fnDrawCallback: function(oSettings) {
                            var total_profit = sum_table_col($('#profit_by_date_table'), 'gross-profit');
                            $('#profit_by_date_table .footer_total').text(total_profit);
                            __currency_convert_recursively($('#profit_by_date_table'));
                        },
                    });
                } else {
                    profit_by_date_datatable.ajax.reload();
                }
            } else if (target == '#profit_by_customer') {
                if(typeof profit_by_customers_table == 'undefined') {
                    profit_by_customers_table = $('#profit_by_customer_table').DataTable({
                        processing: true,
                        serverSide: true,
                        "ajax": {
                            "url": "/reports/get-profit/customer",
                            "data": function ( d ) {
                                d.start_date = $('#profit_tabs_filter')
                                    .data('daterangepicker')
                                    .startDate.format('YYYY-MM-DD');
                                d.end_date = $('#profit_tabs_filter')
                                    .data('daterangepicker')
                                    .endDate.format('YYYY-MM-DD');
                            }
                        },
                        columns: [
                            { data: 'customer', name: 'CU.name'  },
                            { data: 'gross_profit', "searchable": false},
                        ],
                        fnDrawCallback: function(oSettings) {
                            var total_profit = sum_table_col($('#profit_by_customer_table'), 'gross-profit');
                            $('#profit_by_customer_table .footer_total').text(total_profit);
                            __currency_convert_recursively($('#profit_by_customer_table'));
                        },
                    });
                } else {
                    profit_by_customers_table.ajax.reload();
                }
            } else if (target == '#profit_by_day') {
                var start_date = $('#profit_tabs_filter')
                                    .data('daterangepicker')
                                    .startDate.format('YYYY-MM-DD');

                var end_date = $('#profit_tabs_filter')
                                    .data('daterangepicker')
                                    .endDate.format('YYYY-MM-DD');
                var url = '/reports/get-profit/day?start_date=' + start_date + '&end_date=' + end_date;
                $.ajax({
                        url: url,
                        dataType: 'html',
                        success: function(result) {
                           $('#profit_by_day').html(result); 
                            profit_by_days_table = $('#profit_by_day_table').DataTable({
                                    "searching": false,
                                    'paging': false,
                                    'ordering': false,
                            });
                            var total_profit = sum_table_col($('#profit_by_day_table'), 'gross-profit');
                           $('#profit_by_day_table .footer_total').text(total_profit);
                            __currency_convert_recursively($('#profit_by_day_table'));
                        },
                    });
            } else if (target == '#profit_by_products') {
                profit_by_products_table.ajax.reload();
            }
        });
    });

    //credit status section
    if ($('#credit_status_date_range').length == 1) {
        $('#credit_status_date_range').daterangepicker(dateRangeSettings, function(start, end) {
            $('#credit_status_date_range').val(
                start.format('MM/DD/YYYY') + ' ~ ' + end.format('MM/DD/YYYY')
            );
        });
        $('#credit_status_date_range').on('cancel.daterangepicker', function(ev, picker) {
            $('#product_sr_date_filter').val('');
        });
        let date_range = @if(!empty(request()->date_range)) "{{request()->date_range}}".split(' - ') @else [] @endif;
        let set_strat_date = date_range.length ? date_range[0] : moment().startOf('month');
        let set_end_date = date_range.length ? date_range[1] : moment().endOf('month');
        $('#credit_status_date_range')
            .data('daterangepicker')
            .setStartDate(set_strat_date);
        $('#credit_status_date_range')
            .data('daterangepicker')
            .setEndDate(set_end_date);
    }


    $(document).ready(function() {
        @if($report_credit_status)
        var start_date = $('input#credit_status_date_range')
            .data('daterangepicker')
            .startDate.format('YYYY-MM-DD');
        var end_date = $('input#credit_status_date_range')
            .data('daterangepicker')
            .endDate.format('YYYY-MM-DD');
        update_statistics(start_date, end_date);
        $(document).on('change', 'input[name="date-period"], #credit_status_business_location, #credit_status_date_range', function() {
            var start_date = $('input#credit_status_date_range')
                .data('daterangepicker')
                .startDate.format('YYYY-MM-DD');
            var end_date = $('input#credit_status_date_range')
                .data('daterangepicker')
                .endDate.format('YYYY-MM-DD');
            update_statistics(start_date, end_date);
        });
        @endif
    });

        
    function update_statistics(start, end) {
        var locations_id = $('#credit_status_business_location').val();
        var data = { start: start, end: end, location_id : locations_id};
        //get purchase details
        var loader = '<i class="fa fa-refresh fa-spin fa-fw margin-bottom"></i>';
        var period = $('input[name="date-period"]:checked').data('period');
        $('.total_purchase').html(loader);
        $('.purchase_due').html(loader);
        $('.total_sell').html(loader);
        $('.invoice_due').html(loader);
        $.ajax({
            method: 'get',
            url: '/reports/get-credit-status-totals',
            dataType: 'json',
            data: data,
            success: function(data) {
                $('.total_credit_issued').html(__currency_trans_from_en(data.total_credit_sales, true));
                $('.total_credit_paid').html(__currency_trans_from_en(data.total_credit_sales_paid, true));
                $('.total_credit_due').html(__currency_trans_from_en(data.total_credit_sales_due, true));
            },
        });
    }

</script>

@endsection