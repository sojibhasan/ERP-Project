<style>
    div[id="l_category"] {
        display: none;

    }

    input[class="child_absent"]:checked~div[id="l_category"] {
        display: block;
    }

    .child_absent {
        float: left;
    }


    div[id="check_in"] {
        display: none;
    }

    input[class="child_present"]:checked~div[id="check_in"] {
        display: block;
    }

    .child_present {
        float: left;
    }
</style>
<div class="row">
    <div class="col-sm-12">
        <div class="wrap-fpanel">
            <div class="box box-primary">
                <div class="box-header with-border bg-primary-dark">
                    <h3 class="box-title">@lang('hr::lang.set_attendance') </h3>
                </div>
                <div class="panel-body">
                    {!! Form::open(['url' => action('\Modules\HR\Http\Controllers\AttendanceController@store'),
                    'method' => 'post' ]) !!}

                    <table class="table table-hover table-bordered">
                        <thead>
                            <tr>
                                <th class="active">@lang('hr::lang.employee_number') </th>
                                <th class="active">@lang('hr::lang.employee') </th>
                                <th class="active">@lang('hr::lang.job_title') </th>
                                <th class="active">
                                    <label class="css-input css-checkbox css-checkbox-success">
                                        <input type="checkbox" class="checkbox-inline select_one input-icheck"
                                            id="parent_present"><span> @lang('hr::lang.attendance')</span> 
                                    </label>
                                </th>
                                <th class="active">
                                    <label class="css-input css-checkbox css-checkbox-danger">
                                        <input type="checkbox" class="checkbox-inline select_one input-icheck"
                                            id="parent_absent"><span> @lang('hr::lang.leave_category')</span>
                                        
                                    </label>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @if (!empty($employee_info))
                            @foreach ($employee_info as $v_employee)
                            <tr>

                                <td> {{$v_employee->employee_number}} </td>

                                <td>
                                    <input type="hidden" name="date" value="{{$date}}">
                                    @foreach ($atndnce as $atndnce_status)
                                    @if (!empty($atndnce_status))
                                    @if ($v_employee->id == $atndnce_status->employee_id)

                                    <input type="hidden" name="attendance_id[]"
                                        value="@if ($atndnce_status) {{ $atndnce_status->attendance_id }} @endif">

                                    @endif
                                    @endif
                                    @endforeach

                                    <input type="hidden" name="employee_id[]" value="{{$v_employee->id }}">
                                    {{$v_employee->first_name . ' ' . $v_employee->last_name }}
                                </td>
                                @php
                                $job_title = \Modules\HR\Entities\Department::where('id', $v_employee->title)->first();
                                @endphp

                                <td>{{!empty($job_title) ? $job_title->job_title : $job_title}} </td>

                                <td style="width: 35%;">
                                    <input name="attendance[]" @foreach ($atndnce as $atndnce_status)
                                        @if($atndnce_status) 
                                        @if ($v_employee->id == $atndnce_status->employee_id)
                                    {{$atndnce_status->attendance_status == 1 ? 'checked ' : ''}}
                                    @endif
                                    @endif
                                    @endforeach
                                    id="{{$v_employee->id}}"
                                    value="{{$v_employee->id}}" type="checkbox" style="margin-top: 7px;"
                                    class="child_present input-icheck">

                                    <div id="check_in" class="col-sm-11 check_in">
                                        @foreach ($atndnce as $atndnce_status)
                                        @if (!empty($atndnce_status))
                                        @if ($v_employee->id == $atndnce_status->employee_id)
                                        @php
                                        $inTime = date("h:i A", strtotime($atndnce_status->in_time));
                                        $out_time = date("h:i A", strtotime($atndnce_status->out_time));
                                        @endphp
                                        @endif
                                        @endif
                                        @endforeach
                                        <div class="form-group row">
                                            <label class="col-md-1 control-label " style="margin-top: 6px;">In: </label>
                                            <div class="col-md-4">
                                                <div class="input-group bootstrap-timepicker timepicker">
                                                    <input type="text" id="time_only1" class="form-control timepicker "
                                                        name="in[]"
                                                        value="@if(!empty($inTime)) {{$inTime}} @else {{ '10:00 AM' }} @endif">
                                                </div>
                                            </div>
                                            <label for="inputValue" class="col-md-1 control-label" style="margin-top: 6px;">Out: </label>
                                            <div class="col-md-4">
                                                <div class="input-group bootstrap-timepicker timepicker">
                                                    <input type="text" id="time_only2" class="form-control timepicker "
                                                        name="out[]"
                                                        value="@if(!empty($out_time))  {{$out_time}} @else {{ '05:00 PM' }} @endif">
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="checkbox">
                                            <label>
                                              {!! Form::checkbox('over_time[]', 1, false, ['class' => 'input-icheck over_time']);
                                              !!}
                                              @lang('hr::lang.over_time')
                                            </label>
                                        </div>
                                        <br>
                                        <div id="" class="col-sm-11">
                                            <div class="form-group row">
                                                <label class="col-md-1 control-label " style="margin: 8px 20px 0px 0px ;">@lang('hr::lang.hours'):  </label>
                                                <div class="col-md-4">
                                                    <div class="input-group">
                                                        <input type="text" class="form-control "
                                                            name="ot_hours[]"
                                                            value="">
                                                    </div>
                                                </div>
                                                <label for="inputValue" class="col-md-1 control-label" style="margin: 8px 20px 0px 0px ;">@lang('hr::lang.minutes'):  </label>
                                                <div class="col-md-4">
                                                    <div class="input-group">
                                                        <input type="text" class="form-control "
                                                            name="ot_minutes[]"
                                                            value="">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="checkbox">
                                            <label>
                                              {!! Form::checkbox('late_time[]', 1, false, ['class' => 'input-icheck']);
                                              !!}
                                              @lang('hr::lang.late_time')
                                            </label>
                                        </div>
                                        <br>
                                        <div id="" class="col-sm-11">
                                            <div class="form-group row">
                                                <label class="col-md-1 control-label " style="margin: 8px 20px 0px 0px ;">@lang('hr::lang.hours'):  </label>
                                                <div class="col-md-4">
                                                    <div class="input-group">
                                                        <input type="text" class="form-control "
                                                            name="lt_hours[]"
                                                            value="">
                                                    </div>
                                                </div>
                                                <label for="inputValue" class="col-md-1 control-label" style="margin: 8px 20px 0px 0px ;">@lang('hr::lang.minutes'):  </label>
                                                <div class="col-md-4">
                                                    <div class="input-group">
                                                        <input type="text" class="form-control "
                                                            name="lt_minutes[]"
                                                            value="">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                 

                                </td>
                                <td style="width: 35%">
                                    <input id="{{$v_employee->id }}" type="checkbox" 
                                    @foreach ($atndnce as $atndnce_status) 
                                    @if ($atndnce_status) 
                                    @if ($v_employee->id == $atndnce_status->employee_id) 
                                    {{$atndnce_status->leave_category_id ? 'checked' : ''}}
                                    @endif
                                    @endif
                                    @endforeach value="{{$v_employee->id}}"
                                    class="child_absent input-icheck">

                                    <div id="l_category" class="col-sm-9 l_category">
                                        <select name="leave_category_id[]" class="form-control">
                                            <option value="">@lang('hr::lang.select_leave_category') ...
                                            </option>
                                            @foreach ($all_leave_category_info as $v_L_category)
                                            <option value="{{$v_L_category->id}}" 
                                                @foreach ($atndnce as $atndnce_status)
                                                @if ($atndnce_status) 
                                                @if ($v_employee->id == $atndnce_status->employee_id)
                                                {{ $v_L_category->id == $atndnce_status->leave_category_id ? 'selected' : ''}}
                                                @endif
                                                @endif
                                                @endforeach
                                                >
                                                {{$v_L_category->leave_category }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                </td>
                            </tr>
                            @endforeach
                            @else
                            <tr>
                                <td colspan="5" style="text-align:center">No record found!</td>
                            </tr>
                            @endif
                        </tbody>
                    </table>
                    <button type="submit" id="sbtn" class="btn bg-primary btn-md pull-right">
                        @lang('hr::lang.update') </button>
                </div>
                <input type="hidden" name="department_id" value="{{$department_id }}">

                {!! Form::close() !!}
            </div>
        </div>
    </div>
</div>