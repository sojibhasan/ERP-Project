@extends('layouts.app')
@section('title', __('manufacturing::lang.manufacturing'))

@section('content')
<section class="content-header">
    <div class="row">
        <div class="col-md-12 dip_tab">
            <div class="settlement_tabs">
                <ul class="nav nav-tabs">
                    <li class="active" style="margin-left: 20px;">
                        <a style="font-size:13px;" href="#recipe" class="" data-toggle="tab">
                            <i class="fa fa-superpowers"></i> <strong>@lang('manufacturing::lang.recipe')</strong>
                        </a>
                    </li>
                    @can('manufacturing.access_production')
                    <li class="">
                        <a style="font-size:13px;" href="#production" data-toggle="tab">
                            <i class="fa fa-filter"></i> <strong>@lang('manufacturing::lang.production')</strong>
                        </a>
                    </li>

                   <li class="">
                        <a style="font-size:13px;" href="#add_production" data-toggle="tab">
                            <i class="fa fa-sliders"></i> <strong>@lang('manufacturing::lang.add_production')</strong>
                        </a>
                    </li>
                     <li class="">
                        <a style="font-size:13px;" href="#settings" data-toggle="tab">
                            <i class="fa fa-thermometer"></i> <strong>@lang('manufacturing::lang.settings')</strong>
                        </a>
                    </li>
                    <li class="">
                        <a style="font-size:13px;" href="#manufacturing_report" data-toggle="tab">
                            <i class="fa fa-thermometer"></i>
                            <strong>@lang('manufacturing::lang.manufacturing_report')</strong>
                        </a>
                    </li>
                    @endcan
                </ul>
            </div>
        </div>
    </div>
    <div class="tab-content">
        <div class="tab-pane active" id="recipe">
            @include('manufacturing::recipe.index')
        </div>
        @can('manufacturing.access_production') 
         <div class="tab-pane" id="production">
            @include('manufacturing::production.index')
        </div>

        <div class="tab-pane" id="add_production">
            @include('manufacturing::production.create')
        </div>
       <div class="tab-pane" id="settings">
            @include('manufacturing::settings.index')
        </div>
        <div class="tab-pane" id="manufacturing_report">
            @include('manufacturing::production.report')
        </div> 
        @endcan

    </div>

    <div class="modal fade pump_modal" role="dialog" aria-labelledby="gridSystemModalLabel">
    </div>
    <!-- /.content -->
<div class="modal fade" id="recipe_modal" tabindex="-1" role="dialog" 
aria-labelledby="gridSystemModalLabel">
</div>
<!-- /.content -->
<div class="modal fade" id="recipe_modal" tabindex="-1" role="dialog" 
    aria-labelledby="gridSystemModalLabel">
</div>
</section>

@endsection

@section('javascript')
@include('manufacturing::layouts.partials.common_script')
<script type="text/javascript">
    $(document).ready( function () {
        $(".file-input").fileinput(fileinput_setting);
    });
</script>

<script type="text/javascript">
    $(document).ready( function() {
        if ($('#mfg_report_date_filter').length == 1) {
            $('#mfg_report_date_filter').daterangepicker(dateRangeSettings, function(start, end) {
                $('#mfg_report_date_filter span').html(
                    start.format(moment_date_format) + ' ~ ' + end.format(moment_date_format)
                );
                updateMfgReport();
            });
            $('#mfg_report_date_filter').on('cancel.daterangepicker', function(ev, picker) {
                $('#mfg_report_date_filter').html(
                    '<i class="fa fa-calendar"></i> ' + LANG.filter_by_date
                );
            });
        }
        updateMfgReport();
        $('#mfg_report_location_filter').change(function() {
            updateMfgReport();
        });

        function updateMfgReport() {
            var start = $('#mfg_report_date_filter')
                .data('daterangepicker')
                .startDate.format('YYYY-MM-DD');
            var end = $('#mfg_report_date_filter')
                .data('daterangepicker')
                .endDate.format('YYYY-MM-DD');
            var location_id = $('#mfg_report_location_filter').val();

            var data = { start_date: start, end_date: end, location_id: location_id };

            var loader = __fa_awesome();
            $(
                '.total_production, .total_sold, .total_production_cost'
            ).html(loader);

            $.ajax({
                method: 'GET',
                url: '/manufacturing/report',
                dataType: 'json',
                data: data,
                success: function(data) {
                    $('.total_production').html(__currency_trans_from_en(data.total_production, true));
                    $('.total_sold').html(__currency_trans_from_en(data.total_sold, true));
                    $('.total_production_cost').html(__currency_trans_from_en(data.total_production_cost, true));
                },
            });
        }
    });
</script>

@endsection