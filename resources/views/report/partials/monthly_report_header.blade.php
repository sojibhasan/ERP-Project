<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>{{ __('lang_v1.monthly_report')}}</h1>
</section>
<br>
<div class="col-md-12">
    @component('components.filters', ['title' => __('report.filters')])
    <div class="col-md-3">
        <div class="form-group">
            {!! Form::label('monthly_report_location_id', __('purchase.business_location') . ':') !!}
            {!! Form::select('monthly_report_location_id', $business_locations, !empty($location_id) ? $location_id :
            null, ['class' => 'form-control select2 monthly_report_change',
            'placeholder' => __('petro::lang.all'), 'id' => 'monthly_report_location_id', 'style' => 'width:100%']); !!}
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            {!! Form::label('monthly_report_work_shift', __('hr.work_shift') . ':') !!}
            {!! Form::select('monthly_report_work_shift', $work_shifts, !empty($work_shift_id) ? $work_shift_id : null,
            ['class' => 'form-control select2 monthly_report_change', 'placeholder'
            => __('petro::lang.all'), 'id' => 'monthly_report_work_shift', 'style' => 'width:100%']); !!}
        </div>
    </div>
    <div class="col-md-2">
        <div class="form-group">
            {!! Form::label('monthly_report_start_month_range', __('report.start_month_range') . ':') !!}
            {!! Form::selectMonth('monthly_report_start_month', !empty($from_month) ? $from_month : date('m'),
            ['class' => 'form-control select2 monthly_report_change', 'placeholder'
            => __('report.month_placeholder'), 'id' => 'monthly_report_start_month', 'style' => 'width:100%']); !!}
        </div>
    </div>
    <div class="col-md-2">
        <div class="form-group">
            {!! Form::label('monthly_report_end_month_range', __('report.end_month_range') . ':') !!}
            {!! Form::selectMonth('monthly_report_end_month', !empty($end_month) ? $end_month : date('m'),
            ['class' => 'form-control select2 monthly_report_change', 'placeholder'
            => __('report.month_placeholder'), 'id' => 'monthly_report_end_month', 'style' => 'width:100%']); !!}
        </div>
    </div>
    <div class="col-md-2">
        <div class="form-group">
            {!! Form::label('monthly_report_year_range', __('report.years_range') . ':') !!}
            {!! Form::selectYear('monthly_report_year',date('Y'),2000,!empty($year) ? $year : date('Y'),
            ['class' => 'form-control select2 monthly_report_change', 'placeholder'
            => __('report.years_range'), 'id' => 'monthly_report_year', 'style' => 'width:100%']); !!}
        </div>
    </div>
    @endcomponent
</div>
<div class="monthly_report_content"></div>