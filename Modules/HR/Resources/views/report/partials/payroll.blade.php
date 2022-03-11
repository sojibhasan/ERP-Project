<!-- Content Header (Page header) -->
<section class="content-header">
    <h1> @lang('hr::lang.payroll_report')
    </h1>
</section>

<!-- Main content -->
<section class="content">
    <div class="row">
        <div class="col-md-12">
            @component('components.filters', ['title' => __('report.filters')])
            <div class="col-sm-3">
                <div class="form-group">
                    {!! Form::label('payroll_month', __('hr::lang.month').':') !!}
                    {!! Form::text('payroll_month', date('m-Y'), ['class' => 'form-control', 'id' => 'payroll_month']); !!}
                </div>
            </div>

            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('payroll_department_id', __('hr::lang.department') . ':') !!}
                    {!! Form::select('payroll_department_id', $departments, null, ['class' => 'form-control select2',
                    'placeholder' => __('hr::lang.please_select'),'style' => 'width:100%']); !!}
                </div>
            </div>

            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('payroll_employee_id', __('hr::lang.employee') . ':') !!}
                    {!! Form::select('payroll_employee_id', [], null, ['class' => 'form-control select2',
                    'placeholder' => __('hr::lang.please_select'),'style' => 'width:100%']); !!}
                </div>
            </div>

            @endcomponent
        </div>
    </div>

    @component('components.widget', ['class' => 'box-primary'])
    <div class="table-responsive">
        <table class="table table-bordered table-striped" id="payment_table" style="width: 100%;">
            <thead>
                <tr>
                    <th>@lang( 'hr::lang.salary_month' )</th>
                    <th>@lang( 'hr::lang.gross_salary' )</th>
                    <th>@lang( 'hr::lang.deduction' )</th>
                    <th>@lang( 'hr::lang.net_salary' )</th>
                    <th>@lang( 'hr::lang.award' )</th>
                    <th>@lang( 'hr::lang.fine_deduction' )</th>
                    <th>@lang( 'hr::lang.bonus' )</th>
                    <th>@lang( 'hr::lang.payment_amount' )</th>
                    <th>@lang( 'hr::lang.payment_method' )</th>
                </tr>
            </thead>
        </table>
    </div>
    @endcomponent


</section>
<!-- /.content -->