@extends('layouts.app')
@section('title', __('hr::lang.reports'))

@section('content')

<section class="content-header">
    <div class="row">
        <div class="col-md-12 dip_tab">
            <div class="settlement_tabs">
                <ul class="nav nav-tabs">
                    @if($permissions['attendance_report'])
                    <li class="active" style="margin-left: 20px;">
                        <a style="font-size:13px;" href="#attendance" class="" data-toggle="tab">
                            <i class="fa fa-superpowers"></i> <strong>@lang('hr::lang.attendance')</strong>
                        </a>
                    </li>
                    @endif
                    @if($permissions['employee_report'])
                    <li class="">
                        <a style="font-size:13px;" href="#employee_list" data-toggle="tab">
                            <i class="fa fa-list"></i> <strong>@lang('hr::lang.employee_list')</strong>
                        </a>
                    </li>
                    @endif
                    @if($permissions['payroll_report'])
                    <li class="">
                        <a style="font-size:13px;" href="#payroll" data-toggle="tab">
                            <i class="fa fa-money"></i> <strong>@lang('hr::lang.payroll')</strong>
                        </a>
                    </li>
                    @endif
                </ul>
            </div>
        </div>
    </div>
    <div class="tab-content">
        @if($permissions['attendance_report'])
        <div class="tab-pane active" id="attendance">
            @include('hr::report.partials.attendance')
        </div>
        @endif
        @if($permissions['employee_report'])
        <div class="tab-pane" id="employee_list">
            @include('hr::report.partials.employee_list')
        </div>
        @endif
        @if($permissions['payroll_report'])
        <div class="tab-pane" id="payroll">
            @include('hr::report.partials.payroll')
        </div>
        @endif

    </div>

    <div class="modal fade pump_modal" role="dialog" aria-labelledby="gridSystemModalLabel">
    </div>
</section>
@endsection
@section('javascript')
<script>
    if ($('#attendance_date_range').length == 1) {
        $('#attendance_date_range').daterangepicker(dateRangeSettings, function (start, end) {
            $('#attendance_date_range').val(
                start.format(moment_date_format) + ' ~ ' + end.format(moment_date_format)
            );
            expense_table.ajax.reload();
        });
        $('#attendance_date_range').on('cancel.daterangepicker', function (ev, picker) {
            $('#product_sr_date_filter').val('');
            expense_table.ajax.reload();
        });
        $('#attendance_date_range').data('daterangepicker').setStartDate(moment().startOf('month'));
        $('#attendance_date_range').data('daterangepicker').setEndDate(moment().endOf('month'));
    }

    $('#attendance_department_id').change(function(){
        let department_id = $(this).val();
        $.ajax({
            method: 'get',
            url: '/hr/report/get-employee-by-department',
            data: { department_id },
            contentType: 'html',
            success: function(result) {
                $('#attendance_employee_id').empty().append(result);
            },
        });
    });

    $('#attendance_employee_id').change(function(){
        var employee_id = $('select#attendance_employee_id').val();
        var start_date = $('input#attendance_date_range')
            .data('daterangepicker')
            .startDate.format('YYYY-MM-DD');
        var end_date = $('input#attendance_date_range')
            .data('daterangepicker')
            .endDate.format('YYYY-MM-DD');

        $.ajax({
            method: 'get',
            url: '/hr/report/get-attendance-report',
            data:{ employee_id, start_date, end_date},
            contentType: 'html',
            success: function(result) {
                $('.attendance_report').removeClass('hide');
                $('#attendance_report_section').empty().append(result);
            },
        });
    });

// Employee Section
 $(document).ready(function(){
  employee_table = $('#employee_table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '/hr/report/get-employee-report',
            data: function (d) {
                d.department_id = $('#employee_department_id').val();
            }
        },
        columns: [
            { data: 'employee_id', name: 'employee_id' },
            { data: 'name', name: 'name' },
            { data: 'department', name: 'department' },
            { data: 'title', name: 'title' },
            { data: 'joined_date', name: 'joined_date' },
            { data: 'date_of_permanency', name: 'date_of_permanency' },
            { data: 'work_shift', name: 'work_shift' }
        ],
        fnDrawCallback: function (oSettings) {
          
        },
    });
})
    $('#employee_department_id').change(function(){
        employee_table.ajax.reload();
    })


//payroll section
    $('#payroll_department_id').change(function(){
        let department_id = $(this).val();
        $.ajax({
            method: 'get',
            url: '/hr/report/get-employee-by-department',
            data: { department_id },
            contentType: 'html',
            success: function(result) {
                $('#payroll_employee_id').empty().append(result);
            },
        });
    });

    $('#payroll_month').datepicker( {
        format: "mm-yyyy",
        viewMode: "months", 
        minViewMode: "months"
    });

    $(document).ready(function(){   
          // payment_table
          payment_table = $('#payment_table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url:  '/hr/report/get-payroll-report',
                data : function(d){
                    d.employee_id = $('#payroll_employee_id').val();
                    d.month = $('#payroll_month').val();
                }
            },
            columns: [
                {data: 'month', name: 'month'},
                {data: 'gross_salary', name: 'gross_salary'},
                {data: 'deduction', name: 'deduction'},
                {data: 'net_salary', name: 'net_salary'},
                {data: 'award', name: 'award'},
                {data: 'fine_deduction', name: 'fine_deduction'},
                {data: 'bonus', name: 'bonus'},
                {data: 'net_payment', name: 'net_payment'},
                {data: 'payment_method', name: 'payment_method'}
            ],
            "fnDrawCallback": function (oSettings) {
            }
        });
    })

    $('#payroll_employee_id, #payroll_month').change(function(){
        payment_table.ajax.reload();
    })

</script>
@endsection