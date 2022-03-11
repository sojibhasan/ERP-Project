@extends('layouts.app')
@section('title', __('mpcs::lang.F20_form'))

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1> @lang('mpcs::lang.F20_form')
        <small>@lang( 'mpcs::lang.F20_form', ['contacts' => __('mpcs::lang.mange_F20_form') ])</small>
    </h1>
</section>

<!-- Main content -->
<section class="content">
    <div class="row">
        <div class="col-md-12">
            @component('components.filters', ['title' => __('report.filters')])
            {!! Form::open(['action' => 'ReportController@getDailyReport', 'method' => 'get', 'id' =>
            'daily_report_filter']) !!}
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('form_date_range', __('report.date_range') . ':') !!}
                    {!! Form::text('date_range', @format_date('first day of this month') . ' ~ ' . @format_date('last
                    day of this month') , ['placeholder' => __('lang_v1.select_a_date_range'), 'class' =>
                    'form-control', 'id' => 'form_date_range', 'readonly']); !!}
                </div>
            </div>
            <div class="col-sm-2" style="margin-top: 25px">
                <button type="submit" id="#submit_btn"
                    class="btn btn-primary pull-right">@lang('report.apply_filters')</button>
            </div>
            {!! Form::close() !!}
            @endcomponent
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            @component('components.widget', ['class' => 'box-primary'])
            <div class="table-responsive">
                <table class="table table-bordered table-striped" id="daily_report_table">


                </table>
            </div>
            @endcomponent
        </div>
    </div>

</section>
<!-- /.content -->

@endsection
@section('javascript')
<script type="text/javascript">
  $('#form_date_range').daterangepicker({
        ranges: ranges,
        autoUpdateInput: false,
        locale: {
            format: moment_date_format,
            cancelLabel: LANG.clear,
            applyLabel: LANG.apply,
            customRangeLabel: LANG.custom_range,
        },
    });
    $('#form_date_range').on('apply.daterangepicker', function(ev, picker) {
        $(this).val(
            picker.startDate.format(moment_date_format) +
                ' - ' +
                picker.endDate.format(moment_date_format)
        );
    });

    $('#form_date_range').on('cancel.daterangepicker', function(ev, picker) {
        $(this).val('');
    });
</script>
@endsection