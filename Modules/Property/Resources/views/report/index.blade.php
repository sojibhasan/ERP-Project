@extends('layouts.app')
@section('title', __('property::lang.reports'))

@section('content')
    <!-- Main content -->
    <section class="content">

        <div class="row">
            <div class="col-md-12">
                <div class="settlement_tabs">
                    <ul class="nav nav-tabs">
                        @if($report_daily)
                            <li class="active">
                                <a href="#daily_report" class="daily_report" data-toggle="tab">
                                    <i class="fa fa-file-text-o"></i> <strong>@lang('report.daily_report')</strong>
                                </a>
                            </li>
                        @endif
                    </ul>
                    <div class="tab-content">
                        @if($report_daily)
                            <div class="tab-pane active" id="daily_report">
                                @include('property::report.partials.daily_report_header')
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

    </section>
    <!-- /.content -->

@endsection
@section('javascript')
    <script>
        var body = document.getElementsByTagName("body")[0];
        body.className += " sidebar-collapse";
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
                .setStartDate(moment());
            $('#daily_report_date_range')
                .data('daterangepicker')
                .setEndDate(moment());
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
                url: '/property/reports/daily-report',
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

@endsection
