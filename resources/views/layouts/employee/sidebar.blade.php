
@inject('request', 'Illuminate\Http\Request')

@php $sidebar_setting = App\SiteSettings::where('id', 1)->select('ls_side_menu_bg_color', 'ls_side_menu_font_color',
'sub_module_color', 'sub_module_bg_color')->first();  @endphp

<style>
  .skin-blue .main-sidebar {
    background-color: @if( !empty($sidebar_setting->ls_side_menu_bg_color)) {{$sidebar_setting->ls_side_menu_bg_color}}

    @endif;
  }

  .skin-blue .sidebar a {
    color: @if( !empty($sidebar_setting->ls_side_menu_font_color)) {{$sidebar_setting->ls_side_menu_font_color}}

    @endif;
  }

  .skin-blue .treeview-menu>li>a {
    color: @if( !empty($sidebar_setting->sub_module_color)) {{$sidebar_setting->sub_module_color}}

    @endif;
  }

  .skin-blue .sidebar-menu>li>.treeview-menu {
    background: @if( !empty($sidebar_setting->sub_module_bg_color)) {{$sidebar_setting->sub_module_bg_color}}

    @endif;
  }
</style>

<!-- Left side column. contains the logo and sidebar -->
<aside class="main-sidebar">

    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">
      <!-- Sidebar Menu -->
      <ul class="sidebar-menu">
        <li class="{{ $request->segment(1) == 'employee' && $request->segment(2) == 'home' ? 'active' : '' }}">
          <a href="{{action('\Modules\HR\Http\Controllers\EmployeeController@home')}}">
            <i class="fa fa-dashboard"></i> <span>
              @lang('home.home')</span>
          </a>
        </li>
        <li class="{{ $request->segment(1) == 'employee' && $request->segment(2) == 'attendance' ? 'active' : '' }}"><a
            href="{{action('\Modules\HR\Http\Controllers\AttendanceController@getEmployeeAttendance')}}"><i class="fa fa-thumbs-o-up"></i>@lang('hr::lang.attendance')</a>
        </li>
        <li class="{{ $request->segment(1) == 'employee' && $request->segment(2) == 'salaries' ? 'active' : '' }}"><a
            href="{{action('\Modules\HR\Http\Controllers\PayrollPaymentController@getEmployeeSalariesPayment')}}"><i class="fa fa-money"></i>@lang('hr::lang.salaries')</a>
        </li>
        <li class="{{ $request->segment(1) == 'employee' && $request->segment(2) == 'leave-request' ? 'active' : '' }}"><a
            href="{{action('\Modules\HR\Http\Controllers\LeaveRequestController@getEmployeeLeaveRequest')}}"><i class="fa fa-file-text"></i>@lang('hr::lang.leave_request')</a>
        </li>

        </ul>
      </li> 
      </ul>
    </section>
</aside>
       