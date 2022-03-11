<!-- Main content -->
<section class="content">

    <div class="row">
        <div class="col-md-12">
            @component('components.filters', ['title' => __('report.filters')])
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('employee_location_id', __('purchase.business_location') . ':') !!}
                    {!! Form::select('employee_location_id', $business_locations, null, ['id' => 'employee_location_id', 'class' =>
                    'form-control select2', 'style' => 'width:100%']); !!}
                </div>
            </div>
            @endcomponent
        </div>
    </div>


    <div class="row">
        <div class="col-md-12">
        
            <!-- general form elements -->
            <div class="box box-primary">
                <div class="box-header with-border bg-primary-dark">
                    <h3 class="box-title">@lang('hr::lang.employee_list')</h3>
                    <div class="box-tools">
                        <button type="button" class="btn btn-primary btn-modal"
                            data-href="{{action('\Modules\HR\Http\Controllers\EmployeeController@create')}}"
                            data-container=".employee_modal">
                            <i class="fa fa-plus"></i> @lang( 'hr::lang.add_employee' )</button>
                    </div>
                </div>
                <!-- /.box-header -->
                <!-- form start -->
                <div class="box-body">
                    <div class="row">
                        <div class="col-md-12">
                            
                            <div id="msg"></div>
                            <table id="employee_table" class="table table-striped table-bordered" cellspacing="0"
                                width="100%">
                                <thead>
                                    <tr>
                                        <th style="width:125px;">@lang('hr::lang.actions')</th>
                                        <th>@lang('hr::lang.employee_id')</th>
                                        <th>@lang('hr::lang.employee_number')</th>
                                        <th>@lang('hr::lang.employee_name')</th>
                                        <th>@lang('hr::lang.department')</th>
                                        <th>@lang('hr::lang.job_title')</th>
                                        <th>@lang('hr::lang.employment_status')</th>
                                        <th>@lang('hr::lang.shift')</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <!-- /.box-body -->
            </div>
            <!-- /.box -->
        </div>
    </div>
</section>