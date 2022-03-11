<!-- Content Header (Page header) -->
<section class="content-header">
    <h1> @lang('hr::lang.attendance_report')
    </h1>
</section>

<!-- Main content -->
<section class="content">
    <div class="row">
        <div class="col-md-12">
            @component('components.filters', ['title' => __('report.filters')])
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('attendance_date_range', __('hr::lang.date_range') . ':') !!}
                    {!! Form::text('attendance_date_range', null , ['placeholder' => __('lang_v1.select_a_date_range'),
                    'class' => 'form-control', 'id' => 'attendance_date_range', 'readonly']); !!}
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('attendance_department_id', __('hr::lang.department') . ':') !!}
                    {!! Form::select('attendance_department_id', $departments, null, ['class' => 'form-control select2',
                    'placeholder' => __('hr::lang.please_select'),'style' => 'width:100%']); !!}
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('attendance_employee_id', __('hr::lang.employee') . ':') !!}
                    {!! Form::select('attendance_employee_id', [], null, ['class' => 'form-control select2',
                    'placeholder' => __('hr::lang.please_select'),'style' => 'width:100%']); !!}
                </div>
            </div>

            @endcomponent
        </div>
    </div>

    @component('components.widget', ['class' => 'box-primary attendance_report hide'])
        <div class="row">
        <div class="col-md-12">
            <div id="attendance_report_section">

            </div>
        </div>
        </div>
    @endcomponent

    <div class="modal fade pump_operator_modal" role="dialog" aria-labelledby="gridSystemModalLabel">
    </div>

    <div id="daily_collection_print"></div>

</section>
<!-- /.content -->