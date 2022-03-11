<!-- Main content -->
<section class="content">
    @component('components.widget', ['class' => 'box-primary', 'title' => __( 'hr::lang.all_basic_salary' )])
        @can('hr::lang.create')
            @slot('tool')
                <div class="box-tools">
                    <button type="button" class="btn btn-block btn-primary btn-modal" 
                        data-href="{{action('\Modules\HR\Http\Controllers\BasicSalaryController@create')}}" 
                        data-container=".basic_salary_modal">
                        <i class="fa fa-plus"></i> @lang( 'messages.add' )</button>
                </div>
            @endslot
        @endcan
        @can('hr::lang.view')
            <div class="table-responsive">
                <table class="table table-bordered table-striped" id="basic_salary_table">
                    <thead>
                        <tr>
                            <th>@lang( 'hr::lang.date' )</th>
                            <th>@lang( 'hr::lang.employee_id' )</th>
                            <th>@lang( 'hr::lang.employee_name' )</th>
                            <th>@lang( 'hr::lang.employee_number' )</th>
                            <th>@lang( 'hr::lang.department' )</th>
                            <th>@lang( 'hr::lang.job_title' )</th>
                            <th>@lang( 'hr::lang.employee_status' )</th>
                            <th>@lang( 'hr::lang.shift' )</th>
                            <th>@lang( 'hr::lang.basic_salary' )</th>
                            <th>@lang( 'messages.action' )</th>
                        </tr>
                    </thead>
                </table>
            </div>
        @endcan
    @endcomponent

</section>
<!-- /.content -->
