@extends('layouts.app')
@section('title', __('hr::lang.late_and_overtime'))

@section('content')
<!-- Main content -->
<section class="content">
    <div class="row">
        <div class="col-md-12">
            @component('components.filters', ['title' => __('report.filters')])
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('lo_date_range_filter', __('report.date_range') . ':') !!}
                    {!! Form::text('lo_date_range_filter', @format_date('first day of this month') . ' ~ ' .
                    @format_date('last day of this month') , ['placeholder' => __('lang_v1.select_a_date_range'),
                    'class' => 'form-control', 'id' => 'lo_date_range_filter', 'readonly']); !!}
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('lo_employee_filter', __('hr::lang.employee') . ':') !!}
                    {!! Form::select('lo_employee_filter', $employees, null, ['id' =>
                    'lo_employee_filter', 'class' =>
                    'form-control select2', 'style' => 'width:100%', 'placeholder' => __('lang_v1.all')]); !!}
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('lo_department_filter', __('hr::lang.department') . ':') !!}
                    {!! Form::select('lo_department_filter', $departments, null, ['id' =>
                    'lo_department_filter', 'class' =>
                    'form-control select2', 'style' => 'width:100%', 'placeholder' => __('lang_v1.all')]); !!}
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('lo_mode_filter', __('hr::lang.select_mode') . ':') !!}
                    {!! Form::select('lo_mode_filter', ['over_time' => __('hr::lang.over_time'), 'late_time' =>
                    __('hr::lang.late_time')], null, ['id' =>
                    'lo_mode_filter', 'class' =>
                    'form-control select2', 'style' => 'width:100%', 'placeholder' => __('lang_v1.all')]); !!}
                </div>
            </div>
            @endcomponent

            @component('components.widget', ['class' => 'box-primary', 'title' => __(
            'hr::lang.late_and_overtime')])
            <div class="row">
                <div class="col-md-12">
                    <div class="table-responsive">
                        <table id="employee_table" class="table table-striped table-bordered" cellspacing="0"
                            width="100%">
                            <thead>
                                <tr>
                                    <th>@lang( 'hr::lang.date' )</th>
                                    <th>@lang( 'hr::lang.employee_name' )</th>
                                    <th>@lang( 'hr::lang.department' )</th>
                                    <th>@lang( 'hr::lang.job_title' )</th>
                                    <th>@lang( 'hr::lang.job_category' )</th>
                                    <th>@lang( 'hr::lang.pay_grade' )</th>
                                    <th>@lang( 'hr::lang.current_salary' )</th>
                                    <th>@lang( 'hr::lang.over_time' )</th>
                                    <th>@lang( 'hr::lang.over_time_approved' )</th>
                                    <th>@lang( 'hr::lang.late_time' )</th>
                                    <th>@lang( 'hr::lang.late_time_approved' )</th>
                                    <th>@lang( 'hr::lang.status' )</th>
                                    <th>@lang( 'messages.action' )</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endcomponent
        </div>
    </div>

    <div class="modal fade attendance_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel"></div>
</section>


@endsection

@section('javascript')
<script>
$(document).ready(function(){
$('#lo_date_range_filter, #lo_employee_filter, #lo_department_filter, #lo_mode_filter').change(function(){
    employee_table.ajax.reload();
})
        employee_table = $('#employee_table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '{{action("\Modules\HR\Http\Controllers\AttendanceController@getLateOvertime")}}',
                data: function(d){
                    d.start_date = $('#lo_date_range_filter')
                        .data('daterangepicker')
                        .startDate.format('YYYY-MM-DD');
                    d.end_date = $('#lo_date_range_filter')
                        .data('daterangepicker')
                        .endDate.format('YYYY-MM-DD');
                    d.employee_id = $('#lo_employee_filter').val();
                    d.department_id = $('#lo_department_filter').val();
                    d.mode = $('#lo_mode_filter').val();
                }
            },
            columns: [
                { data: 'date', name: 'date' },
                { data: 'employee_name', name: 'employee_name' },
                { data: 'department', name: 'department' },
                { data: 'job_title', name: 'job_titles.job_title' },
                { data: 'category_name', name: 'job_categories.category_name' },
                { data: 'grade_name', name: 'grade_name' },
                { data: 'salary_amount', name: 'basic_salaries.salary_amount' },
                { data: 'over_time', name: 'over_time' },
                { data: 'over_time_approved', name: 'over_time_approved' },
                { data: 'late_time', name: 'late_time' },
                { data: 'late_time_approved', name: 'late_time_approved' },
                { data: 'status', name: 'status' },
                { data: 'action', name: 'action' },
            
            ],
            fnDrawCallback: function (oSettings) {
            
            },
        });
    })

if ($('#lo_date_range_filter').length == 1) {
    $('#lo_date_range_filter').daterangepicker(dateRangeSettings, function(start, end) {
        $('#lo_date_range_filter').val(
            start.format(moment_date_format) + ' - ' + end.format(moment_date_format)
        );
    });
    $('#lo_date_range_filter').on('cancel.daterangepicker', function(ev, picker) {
        $('#product_sr_date_filter').val('');
    });
    $('#lo_date_range_filter')
        .data('daterangepicker')
        .setStartDate(moment().startOf('month'));
    $('#lo_date_range_filter')
        .data('daterangepicker')
        .setEndDate(moment().endOf('month'));
}
</script>




@endsection