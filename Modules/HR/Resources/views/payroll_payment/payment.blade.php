<!-- Main content -->
<section class="content">
    <div class="row">
        <div class="col-md-12">
            @component('components.filters', ['title' => __('report.filters')])
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('filter_employee', __('hr::lang.employee') . ':') !!}
                    {!! Form::select('filter_employee', $employees, null, ['class' => 'form-control select2', 'id' =>
                    'filter_employee',
                    'style' => 'width:100%', 'placeholder' => __('hr::lang.all')]); !!}
                </div>
            </div>
            <div class="col-sm-3">
                <div class="form-group">
                    {!! Form::label('filter_month', __('hr::lang.month').':') !!}
                    {!! Form::text('filter_month', date('m-Y'), ['class' => 'form-control', 'id' => 'filter_month']);
                    !!}
                </div>
            </div>
            @endcomponent
        </div>
    </div>
    @component('components.widget', ['class' => 'box-primary', 'title' => __( 'hr::lang.all_payment' )])
    @can('hr::lang.create')
    @slot('tool')
    <div class="box-tools">
        <button type="button" class="btn btn-block btn-primary btn-modal" id="add_payment_btn"
            data-href="{{action('\Modules\HR\Http\Controllers\PayrollPaymentController@create')}}"
            data-container=".payment_modal">
            <i class="fa fa-plus"></i> @lang( 'hr::lang.make_a_payment' )</button>
    </div>
    @endslot
    @endcan
    @can('hr::lang.view')
    <div class="table-responsive">
        <table class="table table-bordered table-striped" id="payment_table" style="width: 100%;">
            <thead>
                <tr>
                    <th>@lang( 'hr::lang.month' )</th>
                    <th>@lang( 'hr::lang.employee_number' )</th>
                    <th>@lang( 'hr::lang.employee_name' )</th>
                    <th>@lang( 'hr::lang.gross_hourly_salary' )</th>
                    <th>@lang( 'hr::lang.payment_amount' )</th>
                    <th>@lang( 'hr::lang.payment_method' )</th>
                    <th>@lang( 'hr::lang.type' )</th>
                    <th>@lang( 'messages.action' )</th>
                </tr>
            </thead>
        </table>
    </div>
    @endcan
    @endcomponent

</section>
<!-- /.content -->