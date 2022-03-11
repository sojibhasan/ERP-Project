<!-- Main content -->
<section class="content">

    <div class="row">
        <div class="col-md-12">
            @component('components.filters', ['title' => __('report.filters')])
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('leave_request_location_id', __('purchase.business_location') . ':') !!}
                    {!! Form::select('leave_request_location_id', $business_locations, null, ['id' =>
                    'leave_request_location_id', 'class' =>
                    'form-control select2', 'style' => 'width:100%']); !!}
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('leave_request_employee', __('hr::lang.employee') . ':') !!}
                    {!! Form::select('leave_request_employee', $employees, null, ['id' => 'leave_request_employee',
                    'class' =>
                    'form-control select2', 'style' => 'width:100%', 'placeholder' => __('lang_v1.all')]); !!}
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('leave_request_leave_status', __('hr::lang.leave_status') . ':') !!}
                    {!! Form::select('leave_request_leave_status', ['pending' => __('hr::lang.pending'), 'approved' =>
                    __('hr::lang.approved'), 'rejected' => __('hr::lang.rejected')], null, ['id' =>
                    'leave_request_leave_status', 'class' =>
                    'form-control select2', 'style' => 'width:100%', 'placeholder' => __('lang_v1.all')]); !!}
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('leave_request_date_range', __('hr::lang.date_range') . ':') !!}
                    {!! Form::text('leave_request_date_range', null, ['id' =>
                    'leave_request_date_range', 'class' =>
                    'form-control', 'readonly', 'style' => 'width:100%']); !!}
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('leave_request_leave_type', __('hr::lang.leave_types') . ':') !!}
                    {!! Form::select('leave_request_leave_type', $leave_types, null, ['id' =>
                    'leave_request_leave_type', 'class' =>
                    'form-control select2', 'style' => 'width:100%', 'placeholder' => __('lang_v1.all')]); !!}
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('leave_request_user', __('hr::lang.approved_by') . ':') !!}
                    {!! Form::select('leave_request_user', $users, null, ['id' =>
                    'leave_request_user', 'class' =>
                    'form-control select2', 'style' => 'width:100%', 'placeholder' => __('lang_v1.all')]); !!}
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
                    <h3 class="box-title">@lang('hr::lang.leave_request_list')</h3>
                    <div class="box-tools">
                        <button type="button" class="btn btn-primary btn-modal"
                            data-href="{{action('\Modules\HR\Http\Controllers\LeaveRequestController@create')}}"
                            data-container=".leave_request_modal">
                            <i class="fa fa-plus"></i> @lang( 'hr::lang.add_leave_request' )</button>
                    </div>
                </div>
                <!-- /.box-header -->
                <!-- form start -->
                <div class="box-body">
                    <div class="row">
                        <div class="col-md-12">

                            <div id="msg"></div>
                            <table id="leave_request_table" class="table table-striped table-bordered" cellspacing="0"
                                width="100%">
                                <thead>
                                    <tr>
                                        <th style="width:125px;">@lang('hr::lang.actions')</th>
                                        <th>@lang('hr::lang.date')</th>
                                        <th>@lang('hr::lang.employee')</th>
                                        <th>@lang('hr::lang.leave_type')</th>
                                        <th>@lang('hr::lang.leave_from')</th>
                                        <th>@lang('hr::lang.leave_to')</th>
                                        <th>@lang('hr::lang.leave_days')</th>
                                        <th>@lang('hr::lang.leave_status')</th>
                                        <th>@lang('hr::lang.attended_by')</th>
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