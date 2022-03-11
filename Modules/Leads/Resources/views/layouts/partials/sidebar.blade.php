<li class="treeview {{ in_array($request->segment(1), ['leads']) ? 'active active-sub' : '' }}">
    <a href="#"><i class="fa fa-lg fa-lightbulb-o"></i> <span>@lang('leads::lang.leads')</span>
        <span class="pull-right-container">
            <i class="fa fa-angle-left pull-right"></i>
        </span>
    </a>
    <ul class="treeview-menu">
        @if($leads)
        @if(auth()->user()->can('leads.view') || auth()->user()->can('leads.edit') ||
        auth()->user()->can('leads.destory')|| auth()->user()->can('leads.create'))
        <li class="{{ $request->segment(1) == 'leads' && $request->segment(2) == 'leads'? 'active' : '' }}">
            <a href="{{action('\Modules\Leads\Http\Controllers\LeadsController@index')}}"><i
                    class="fa fa-lightbulb-o"></i>@lang('leads::lang.leads')</a>
        </li>
        @endcan
        @endif
        @if($leads_import)
        @can('leads.import')
        <li class="{{ $request->segment(1) == 'leads' && $request->segment(2) == 'import'? 'active' : '' }}">
            <a href="{{action('\Modules\Leads\Http\Controllers\ImportLeadsController@index')}}"><i
                    class="fa fa-download"></i>@lang('leads::lang.import_data')</a>
        </li>
        @endcan
        @endif
        @if($day_count)
        @can('day_count')
        <li class="{{ $request->segment(1) == 'leads' && $request->segment(2) == 'day-count'? 'active' : '' }}">
            <a href="{{action('\Modules\Leads\Http\Controllers\DayCountController@index')}}"><i
                    class="fa fa-plus"></i>@lang('leads::lang.day_count')</a>
        </li>
        @endcan
        @endif

        @if($leads_settings)
        @can('leads.settings')
        <li class="{{ $request->segment(1) == 'leads' && $request->segment(2) == 'settings'? 'active' : '' }}">
            <a href="{{action('\Modules\Leads\Http\Controllers\SettingController@index')}}"><i
                    class="fa fa-cogs"></i>@lang('leads::lang.settings')</a>
        </li>
        @endcan
        @endif

    </ul>
</li>