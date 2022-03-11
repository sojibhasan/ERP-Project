@extends('layouts.employee')
@section('title', __('hr.attendance_report'))

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>@lang('hr::lang.leave_request')</h1>
</section>

<!-- Main content -->
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
@endsection

@section('javascript')
<script>
    //leave request tab 
    if ($('#leave_request_date_range').length == 1) {
        $('#leave_request_date_range').daterangepicker(dateRangeSettings, function(start, end) {
            $('#leave_request_date_range').val(
               start.format(moment_date_format) + ' - ' +  end.format(moment_date_format)
            );
        });
        $('#leave_request_date_range').on('cancel.daterangepicker', function(ev, picker) {
            $('#product_sr_date_filter').val('');
        });
        $('#leave_request_date_range')
            .data('daterangepicker')
            .setStartDate(moment().startOf('month'));
        $('#leave_request_date_range')
            .data('daterangepicker')
            .setEndDate(moment().endOf('month'));
    }
    $('#leave_request_location_id, #leave_request_date_range, #leave_request_employee, #leave_request_leave_status, #leave_request_leave_type, #leave_request_user').change(function () {
        leave_request_table.ajax.reload();
    });
    //leave_request list
    leave_request_table = $('#leave_request_table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '{{action("\Modules\HR\Http\Controllers\LeaveRequestController@index")}}',
            data: function (d) {
                d.location_id = $('#leave_request_location_id').val();
                var start_date = $('input#leave_request_date_range')
                        .data('daterangepicker')
                        .startDate.format('YYYY-MM-DD');
                    var end_date = $('input#leave_request_date_range')
                        .data('daterangepicker')
                        .endDate.format('YYYY-MM-DD');
                d.start_date = start_date;
                d.end_date = end_date;
                d.employee_id = $('#leave_request_employee').val();
                d.status = $('#leave_request_leave_status').val();
                d.leave_type_id = $('#leave_request_leave_type').val();
                d.attended_by = $('#leave_request_user').val();
            }
        },
        columns: [
            { data: 'action', name: 'action' },
            { data: 'date', name: 'date' },
            { data: 'employee_name', name: 'employee_name' },
            { data: 'leave_type', name: 'leave_type' },
            { data: 'leave_date_from', name: 'leave_date_from' },
            { data: 'leave_date_to', name: 'leave_date_to' },
            { data: 'leave_days', name: 'leave_days' },
            { data: 'status', name: 'status' },
            { data: 'attended_by', name: 'attended_by' },
        ],
        fnDrawCallback: function (oSettings) {
          
        },
    });

    $(document).on('click', 'a.delete_leave_request', function(e) {
        e.preventDefault();
        swal({
            title: LANG.sure,
            text: 'This leave_request will be deleted.',
            icon: 'warning',
            buttons: true,
            dangerMode: true,
        }).then(willDelete => {
            if (willDelete) {
                var href = $(this).data('href');
                var data = $(this).serialize();

                $.ajax({
                    method: 'DELETE',
                    url: href,
                    dataType: 'json',
                    data: data,
                    success: function(result) {
                        if (result.success === true) {
                            toastr.success(result.msg);
                            employee_table.ajax.reload();
                        } else {
                            toastr.error(result.msg);
                        }
                    },
                });
            }
        });
    });

</script>
@endsection