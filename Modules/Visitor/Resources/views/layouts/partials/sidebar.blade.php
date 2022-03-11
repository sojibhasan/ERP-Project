@can('visitor.registration.create')
<li class="treeview {{ in_array($request->segment(1), ['visitor-module', 'visitor']) ? 'active active-sub' : '' }}">
    <a href="#"><i class="fa fa-user-circle"></i> <span>@lang('visitor::lang.visitor_module')</span>
        <span class="pull-right-container">
            <i class="fa fa-angle-left pull-right"></i>
        </span>
    </a>
    <ul class="treeview-menu">
        @if($visitors)
        <li class="{{ $request->segment(1) == 'visitor-module' && $request->segment(2) == 'visitor' ? 'active' : '' }}">
            <a href="{{action('\Modules\Visitor\Http\Controllers\VisitorController@index')}}"><i
                    class="fa fa-list"></i>@lang('visitor::lang.list_visitors')</a>
        </li>
        @endif
        @if($visitors_registration)
        <li
            class="{{ $request->segment(1) == 'visitor-module' && $request->segment(2) == 'registration' && $request->segment(3) == '' ? 'active' : '' }}">
            <a href="{{action('\Modules\Visitor\Http\Controllers\VisitorRegistrationController@create')}}"><i
                    class="fa fa-registered"></i>@lang('visitor::lang.visitor_registration')</a>
        </li>
        @endif
        @if($visitors_registration_setting)
        <li
            class="{{ $request->segment(1) == 'visitor-module' && $request->segment(2) == 'settings' ? 'active' : '' }}">
            <a href="{{action('\Modules\Visitor\Http\Controllers\VisitorSettingController@index')}}"><i
                    class="fa fa-cogs"></i>@lang('visitor::lang.visitor_registration_settings')</a>
        </li>
        @endif
        <li
            class="{{ $request->segment(1) == 'visitor-module' && $request->segment(2) == 'qr-visitor-reg' ? 'active' : '' }}">
            <a href="{{action('\Modules\Visitor\Http\Controllers\VisitorController@generateQr')}}"><i
                    class="fa fa-qrcode"></i>@lang('visitor::lang.qr_visitor_reg')</a>
        </li>
    </ul>
</li>
@endcan