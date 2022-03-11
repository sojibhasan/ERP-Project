<!-- Content Header (Page header) -->
<section class="content-header">
    <h1> @lang('hr::lang.employee_report')
    </h1>
</section>

<!-- Main content -->
<section class="content">
    <div class="row">
        <div class="col-md-12">
            @component('components.filters', ['title' => __('report.filters')])
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('employee_department_id', __('hr::lang.department') . ':') !!}
                    {!! Form::select('employee_department_id', $departments, null, ['class' => 'form-control select2',
                    'placeholder' => __('hr::lang.please_select'),'style' => 'width:100%']); !!}
                </div>
            </div>

            @endcomponent
        </div>
    </div>

    @component('components.widget', ['class' => 'box-primary'])
    <div class="table-responsive">
        <table id="employee_table" class="table table-striped table-bordered" cellspacing="0" width="100%">
            <thead>
                <tr>
                    <th>@lang('hr::lang.employee_id')</th>
                    <th>@lang('hr::lang.employee_name')</th>
                    <th>@lang('hr::lang.department')</th>
                    <th>@lang('hr::lang.job_title')</th>
                    <th>@lang('hr::lang.joined_date')</th>
                    <th>@lang('hr::lang.date_of_permanency')</th>
                    <th>@lang('hr::lang.work_shift')</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>
    @endcomponent


</section>
<!-- /.content -->