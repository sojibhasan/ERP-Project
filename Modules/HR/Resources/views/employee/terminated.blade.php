<!-- Main content -->
<section class="content">

    <div class="row">
        <div class="col-md-12">
            @component('components.filters', ['title' => __('report.filters')])
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('teminated_location_id', __('purchase.business_location') . ':') !!}
                    {!! Form::select('teminated_location_id', $business_locations, null, ['id' =>
                    'teminated_location_id', 'class' =>
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
                </div>
                <!-- /.box-header -->
                <!-- form start -->
                <div class="box-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div id="msg"></div>
                            <table id="teminated_employee_table" class="table table-striped table-bordered"
                                cellspacing="0" width="100%">
                                <thead>
                                    <tr>
                                        <th>@lang('hr::lang.employee_id')</th>
                                        <th>@lang('hr::lang.employee_number')</th>
                                        <th>@lang('hr::lang.employee_name')</th>
                                        <th>@lang('hr::lang.department')</th>
                                        <th>@lang('hr::lang.job_title')</th>
                                        <th>@lang('hr::lang.employment_status')</th>
                                        <th>@lang('hr::lang.shift')</th>
                                        <th style="width:125px;">@lang('hr::lang.actions')</th>
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