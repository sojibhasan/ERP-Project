<!-- Content Header (Page header) -->

<section class="content-header">
    <h1>{{ __('report.comparison_report')}}</h1>
</section>

<div class="col-md-12">
    @component('components.filters', ['title' => __('report.filters')])
    <div class="col-md-3">
        <div class="form-group">
            {!! Form::label('comparison_date_range_one', __('report.date_range') . ':') !!}
            {!! Form::text('date_range', @format_date('first day of this month') . ' ~ ' . @format_date('last
            day of this month') , ['placeholder' => __('lang_v1.select_a_date_range'), 'class' =>
            'form-control daily_report_change', 'id' => 'comparison_date_range_one', 'readonly']); !!}
        </div>
    </div>
    <div class="col-md-2"></div>
    <div class="col-md-3">
        <div class="form-group">
            {!! Form::label('comparison_date_range_two', __('report.date_range') . ':') !!}
            {!! Form::text('date_range', @format_date('first day of this month') . ' ~ ' . @format_date('last
            day of this month') , ['placeholder' => __('lang_v1.select_a_date_range'), 'class' =>
            'form-control daily_report_change', 'id' => 'comparison_date_range_two', 'readonly']); !!}
        </div>
    </div>
    @endcomponent
</div>

<div class="comparison_report_div"></div>