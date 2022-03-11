<li class="treeview {{ in_array($request->segment(1), ['agents']) ? 'active active-sub' : '' }}">
    <a href="#"><i class="fa fa-user-secret"></i> <span>@lang('agent::lang.agent')</span>
        <span class="pull-right-container">
            <i class="fa fa-angle-left pull-right"></i>
        </span>
    </a>
    <ul class="treeview-menu">
        <li class="{{ $request->segment(1) == 'agent' && $request->segment(2) == 'dashboard'? 'active' : '' }}">
            <a href="{{action('\Modules\Agent\Http\Controllers\AgentController@dashboard')}}"><i
                    class="fa fa-dashboard"></i>@lang('agent::lang.dashboard')</a>
        </li>
      

    </ul>
</li>