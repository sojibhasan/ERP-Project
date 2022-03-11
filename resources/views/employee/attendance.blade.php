@extends('layouts.employee')
@section('title', __('hr.attendance_report'))

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>@lang('hr::lang.attendance_report')</h1>
</section>

<!-- Main content -->
<section class="content">

    <div class="row">
        <div class="col-md-12">
            @component('components.filters', ['title' => __('report.filters')])
            {!! Form::open(['url' => action('\Modules\HR\Http\Controllers\AttendanceController@getEmployeeAttendance'),
            'method' => 'get']) !!}
            <div class="col-sm-3">
                <div class="form-group">
                    <label>@lang('hr::lang.date') <span class="required">*</span></label>

                    <div class="input-group">
                        <input type="text" name="date" id="filter_date" class="form-control" value="" data-format="mm-yyyy"
                            autocomplete="true">
                        <div class="input-group-addon">
                            <i class="fa fa-calendar"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <div class="col-sm-3">
                    <button type="submit" id="sbtn" name="sbtn" value="1" style="margin-top: 24px;"
                        class="btn bg-olive btn-md btn-flat">@lang('hr::lang.go') </button>
                </div>
            </div>
            {!! Form::close() !!}
            @endcomponent
        </div>
    </div>

    <div class="row">
        <div class="col-sm-12">
            <div class="wrap-fpanel">
                <div class="box box-primary">
                    <div class="box-header with-border bg-primary-dark">
                        <h3 class="box-title">@lang('hr::lang.attendance_report') </h3>
                    </div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-12 col-sm-12 col-xs-12">


                                <table class="table table-bordered cart-buttons" width="100%">
                                    <thead style="display: none">
                                        <tr>
                                            <th class="active">Name</th>

                                            @foreach ($dateSl as $edate) 
                                            <th class="active"> {{$edate}} </th>
                                            @endforeach

                                        </tr>

                                    </thead>

                                    <tbody style="display: none">

                                        @foreach ($attendance as $key => $v_employee)
                                        <tr>

                                            <td>{{Auth::user()->first_name}} {{Auth::user()->last_name}} 
                                            </td>
                                            @foreach ($v_employee as $v_result)
                                            @foreach ($v_result as $emp_attendance)
                                            <td>
                                                @if ($emp_attendance->attendance_status == '1')
                                                <small class="label bg-olive">P</small>

                                                @elseif ($emp_attendance->attendance_status == '0')
                                                <small class="label bg-red">A</small>

                                                @elseif($emp_attendance->attendance_status == '3')
                                                <small class="label bg-yellow">L</small>

                                                @elseif ($emp_attendance->attendance_status == 'H')
                                                <small class="label btn-default">H</small>
                                                @endif
                                            </td>
                                            @endforeach
                                            @endforeach
                                        </tr>
                                        @endforeach

                                    </tbody>
                                </table>

                                <div class="table-responsive">
                                    <table class="table table-bordered" width="100%">
                                        <thead>
                                            <tr>
                                                <th class="active">@lang('hr::lang.name') </th>

                                                @foreach ($dateSl as $edate)
                                                <th class="active">{{$edate}}</th>
                                                @endforeach

                                            </tr>

                                        </thead>

                                        <tbody>

                                            @foreach ($attendance as $key => $v_employee)
                                            <tr>

                                                <td>{{Auth::user()->first_name}} {{Auth::user()->last_name}} 
                                                </td>
                                                @foreach ($v_employee as $v_result)
                                                @foreach ($v_result as $emp_attendance)
                                                <td>
                                                    @if ($emp_attendance->attendance_status == '1')
                                                    <small class="label bg-olive">P</small>
    
                                                    @elseif ($emp_attendance->attendance_status == '0')
                                                    <small class="label bg-red">A</small>
    
                                                    @elseif($emp_attendance->attendance_status == '3')
                                                    <small class="label bg-yellow">L</small>
    
                                                    @elseif ($emp_attendance->attendance_status == 'H')
                                                    <small class="label btn-default">H</small>
                                                    @endif
                                                </td>
                                                @endforeach


                                                @endforeach
                                            </tr>
                                            @endforeach

                                        </tbody>
                                    </table>
                                </div>

                            </div>
                        </div>



                    </div>
                </div>
            </div>
        </div>
    </div>

</section>
@endsection

@section('javascript')
<script>
    $(function() {
        $('#filter_date').datepicker({
            changeMonth: true,
            changeYear: true,
            showButtonPanel: true,
            dateFormat: "MM",
        });
    });

var handleCartButtons = function() {
            "use strict";
            0 !== $(".cart-buttons").length && $(".cart-buttons").DataTable({
                "iDisplayLength": "All",
                "bSort" : false,
                paging: false,

                dom: "Bfrtip",
                buttons: [{
                    extend: "copy",
                    className: "btn-sm"
                }, {
                    extend: "csv",
                    className: "btn-sm"
                }, {
                    extend: "excel",
                    className: "btn-sm"
                }, {
                    extend: 'pdf',
                    orientation: 'landscape',
                    className: "btn-sm"
                }, {
                    extend: "print",
                    className: "btn-sm"
                }],
                responsive: !0
            })
        },
        cartButtons = function() {
            "use strict";
            return {
                init: function() {
                    handleCartButtons()
                }
            }
        }();
</script>
@endsection