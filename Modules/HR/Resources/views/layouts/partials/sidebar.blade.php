@can('hr.access')
<li class="treeview {{ in_array($request->segment(1), ['hr']) ? 'active active-sub' : '' }}"
    style="background: #00C0EF;">
    <a href="#">
        <i class="fa fa-handshake-o"></i>
        <span class="title">@lang('hr::lang.hr_module')</span>
        <span class="pull-right-container">
            <i class="fa fa-angle-left pull-right"></i>
        </span>
    </a>
    <ul class="treeview-menu">
        @if($hr_module)
        @can('hr.employee')
        @if($employee)
        <li class="{{ $request->segment(1) == 'hr' && $request->segment(2) == 'employee' ? 'active' : '' }}">
            <a href="{{action('\Modules\HR\Http\Controllers\EmployeeController@index')}}">
                <i class="fa fa-users"></i>
                <span class="title">
                    @lang('hr::lang.employee')
                </span>
            </a>
        </li>
        @endif
        @if($attendance)
        <li class="{{ $request->segment(2) == 'attendance' ? 'active active-sub' : '' }}">
            <a href="{{action('\Modules\HR\Http\Controllers\AttendanceController@index')}}">
                <i class="fa fa-thumbs-o-up"></i>
                <span class="title">
                    @lang('hr::lang.attendance')
                </span>
            </a>
        </li>
        @endif
        @if($late_and_over_time)
        <li
            class="{{ $request->segment(2) == 'attendance' && $request->segment(3) == 'get-late-and-overtime' ? 'active active-sub' : '' }}">
            <a href="{{action('\Modules\HR\Http\Controllers\AttendanceController@getLateOvertime')}}">
                <i class="fa fa-clock-o"></i>
                <span class="title">
                    @lang('hr::lang.late_and_overtime')
                </span>
            </a>
        </li>
        @endif

        @can('hr.payroll')
        @if($payroll)
        <li class="{{ $request->segment(2) == 'payroll' ? 'active active-sub' : '' }}">
            <a href="{{action('\Modules\HR\Http\Controllers\PayrollPaymentController@index')}}">
                <i class="fa fa-briefcase"></i>
                <span class="title">
                    @lang('hr::lang.payroll')
                </span>
            </a>

        </li>
        @endif
        @endcan

        @can('hr.reports')
        @if($hr_reports)
        <li class="{{ $request->segment(2) == 'report' ? 'active' : '' }}">
            <a href="{{action('\Modules\HR\Http\Controllers\ReportController@index')}}">
                <i class="fa fa-file-text"></i>
                <span class="title">
                    @lang('hr::lang.reports')
                </span>
            </a>
        </li>
        @endif
        @endcan

        @can('hr.notice_board')
        @if($notice_board)
        <li class="{{ $request->segment(2) == 'notice-board' ? 'active' : '' }}">
            <a href="{{action('\Modules\HR\Http\Controllers\NoticeBoardController@index')}}">
                <i class="fa fa-file-o"></i>
                <span class="title">
                    @lang('hr::lang.notice_board')
                </span>
            </a>
        </li>
        @endif
        @endcan
        @endif

        <!-- Hr Settings  -->
        @can('hr.settings')
        @if($hr_settings)
        <li class="{{ $request->segment(2) == 'settings' ? 'active active-sub' : '' }}">
            <a href="{{action('\Modules\HR\Http\Controllers\HrSettingsController@index')}}">
                <i class="fa fa-gears"></i>
                <span class="title">
                    @lang('hr::lang.hr_settings')
                </span>
            </a>
        </li>
        @endif
        @endcan
    </ul>
    @endif
</li>
@endcan