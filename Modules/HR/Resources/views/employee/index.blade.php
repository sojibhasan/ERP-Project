@extends('layouts.app')
@section('title', __('hr.employee_list'))

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <div class="row">
        <div class="col-md-12 dip_tab">
            <div class="settlement_tabs">
                <ul class="nav nav-tabs">
                    @if($permissions['employee'])
                    <li class="@if(empty(session('status.tab'))) active @endif" style="margin-left: 20px;">
                        <a style="font-size:13px;" href="#employee" class="" data-toggle="tab">
                            <i class="fa fa-superpowers"></i> <strong>@lang('hr::lang.employee')</strong>
                        </a>
                    </li>
                    @endif
                    @if($permissions['teminated'])
                    <li class="" style="margin-left: 20px;">
                        <a style="font-size:13px;" href="#terminated" class="" data-toggle="tab">
                            <i class="fa fa-user-times"></i> <strong>@lang('hr::lang.terminated')</strong>
                        </a>
                    </li>
                    @endif
                    @if($permissions['award'])
                    <li class="" style="margin-left: 20px;">
                        <a style="font-size:13px;" href="#employee_award" class="" data-toggle="tab">
                            <i class="fa fa-trophy"></i> <strong>@lang('hr::lang.awards')</strong>
                        </a>
                    </li>
                    @endif
                    @if($permissions['leave_request'])
                    <li class="@if(!empty(session('status.tab')) && session('status.tab')=='leave_request') active @endif" style="margin-left: 20px;">
                        <a style="font-size:13px;" href="#leave_request" class="" data-toggle="tab">
                            <i class="fa fa-file-text"></i> <strong>@lang('hr::lang.leave_request')</strong>
                        </a>
                    </li>
                    @endif
                </ul>
            </div>
        </div>
    </div>
    <div class="tab-content">
        @if($permissions['employee'])
        <div class="tab-pane @if(empty(session('status.tab'))) active @endif" id="employee">
            @include('hr::employee.employee')
        </div>
        @endif
        @if($permissions['teminated'])
        <div class="tab-pane " id="terminated">
            @include('hr::employee.terminated')
        </div>
        @endif
        @if($permissions['award'])
        <div class="tab-pane " id="employee_award">
            @include('hr::employee_award.index')
        </div>
        @endif
        @if($permissions['leave_request'])
        <div class="tab-pane @if(!empty(session('status.tab')) && session('status.tab')=='leave_request') active @endif " id="leave_request">
            @include('hr::leave_request.index')
        </div>
        @endif


    </div>
    <div class="modal fade employee_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel"></div>
    <div class="modal fade award_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel"></div>
    <div class="modal fade leave_request_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel"></div>
</section>

@endsection

@section('javascript')
<script>
    $('#employee_location_id').change(function () {
        employee_table.ajax.reload();
    });
    //employee list
    employee_table = $('#employee_table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '{{action("\Modules\HR\Http\Controllers\EmployeeController@index")}}',
            data: function (d) {
                d.location_id = $('#employee_location_id').val();
            }
        },
        columns: [
            { data: 'action', name: 'action' },
            { data: 'employee_id', name: 'employee_id' },
            { data: 'employee_number', name: 'employee_number' },
            { data: 'name', name: 'name' },
            { data: 'department', name: 'department' },
            { data: 'title', name: 'title' },
            { data: 'employment_status', name: 'employment_status' },
            { data: 'work_shift', name: 'work_shift' },
        ],
        fnDrawCallback: function (oSettings) {
          
        },
    });

    $(document).on('click', 'a.delete_employee', function(e) {
        e.preventDefault();
        swal({
            title: LANG.sure,
            text: 'This employee will be deleted.',
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
    $(document).on('click', 'a.toggle_active_employee', function(e) {
        e.preventDefault();
        swal({
            title: LANG.sure,
            text: '',
            icon: 'warning',
            buttons: true,
            dangerMode: true,
        }).then(willDelete => {
            if (willDelete) {
                var href = $(this).data('href');
                var data = $(this).serialize();

                $.ajax({
                    method: 'post',
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
    $('#filter_business').select2();
</script>


<script>
    $('#teminated_location_id').change(function () {
        teminated_employee_table.ajax.reload();
    });

    //employee list
    teminated_employee_table = $('#teminated_employee_table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '{{action("\Modules\HR\Http\Controllers\EmployeeController@teminatedEmployee")}}',
            data: function (d) {
                d.location_id = $('#teminated_location_id').val();
            }
        },
        columns: [
            { data: 'employee_id', name: 'employee_id' },
            { data: 'employee_number', name: 'employee_number' },
            { data: 'name', name: 'name' },
            { data: 'department', name: 'department' },
            { data: 'title', name: 'title' },
            { data: 'employment_status', name: 'employment_status' },
            { data: 'work_shift', name: 'work_shift' },
            { data: 'action', name: 'action' },
        ],
        fnDrawCallback: function (oSettings) {
          
        },
    });

    $(document).on('click', 'a.delete_employee', function(e) {
        e.preventDefault();
        swal({
            title: LANG.sure,
            text: 'This employee will be deleted.',
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
                            teminated_employee_table.ajax.reload();
                        } else {
                            toastr.error(result.msg);
                        }
                    },
                });
            }
        });
    });
    $('#filter_business').select2();
</script>

<script>
    $('#award_location_id').change(function () {
        award_list_table.ajax.reload();
    });
    //employee list
    award_list_table = $('#award_list_table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '{{action("\Modules\HR\Http\Controllers\EmployeeAwardController@index")}}',
            data: function (d) {
                d.location_id = $('#award_location_id').val();
            }
        },
        columns: [
            { data: 'employee_number', name: 'employee_number' },
            { data: 'name', name: 'name' },
            { data: 'award_name', name: 'award_name' },
            { data: 'gift_item', name: 'gift_item' },
            { data: 'award_amount', name: 'award_amount' },
            { data: 'award_month', name: 'award_month' },
            { data: 'action', name: 'action' },
        ],
        fnDrawCallback: function (oSettings) {
          
        },
    });

    $(document).on('click', 'a.delete_award', function(e) {
        e.preventDefault();
        swal({
            title: LANG.sure,
            text: 'This award will be deleted.',
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
                            award_list_table.ajax.reload();
                        } else {
                            toastr.error(result.msg);
                        }
                    },
                });
            }
        });
    });
    $('#filter_business').select2();
</script>
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