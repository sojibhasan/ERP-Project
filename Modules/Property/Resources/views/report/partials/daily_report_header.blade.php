<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>{{ __('lang_v1.daily_report')}}</h1>
</section>
<br>
<div class="col-md-12">
    @component('components.filters', ['title' => __('report.filters')])
        <div class="col-md-3">
            <div class="form-group">
                {!! Form::label('daily_report_location_id', __('purchase.business_location') . ':') !!}
                {!! Form::select('daily_report_location_id', $business_locations, !empty($location_id) ? $location_id :
                null, ['class' => 'form-control select2 daily_report_change',
                'placeholder' => __('petro::lang.all'), 'id' => 'daily_report_location_id', 'style' => 'width:100%']); !!}
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                {!! Form::label('daily_report_work_shift', __('hr.work_shift') . ':') !!}
                {!! Form::select('daily_report_work_shift', $work_shifts, !empty($work_shift_id) ? $work_shift_id : null,
                ['class' => 'form-control select2 daily_report_change', 'placeholder'
                => __('petro::lang.all'), 'id' => 'daily_report_work_shift', 'style' => 'width:100%']); !!}
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                {!! Form::label('daily_report_date_range', __('report.date_range') . ':') !!}
                {!! Form::text('date_range', @format_date('first day of this month') . ' ~ ' . @format_date('last
                day of this month') , ['placeholder' => __('lang_v1.select_a_date_range'), 'class' =>
                'form-control daily_report_change', 'id' => 'daily_report_date_range', 'readonly']); !!}
            </div>
        </div>
    @endcomponent
</div>
<div class="daily_report_content"></div>