<li class="treeview {{ in_array($request->segment(1), ['ezyboat']) ? 'active active-sub' : '' }}" id="tour_step5">
    <a href="#"><i class="fa fa-ship"></i> <span>@lang('ezyboat::lang.ezyboat')</span>
        <span class="pull-right-container">
            <i class="fa fa-angle-left pull-right"></i>
        </span>
    </a>
    <ul class="treeview-menu">
        <li class="{{ $request->segment(2) == 'list' ? 'active' : '' }}"><a
                href="{{action('\Modules\Ezyboat\Http\Controllers\EzyboatController@index')}}"><i class="fa fa-list"></i>
                @lang('ezyboat::lang.list_boats')</a>
        </li>
        <li class="{{ $request->segment(2) == 'boat-operation' ? 'active' : '' }}"><a
                href="{{action('\Modules\Ezyboat\Http\Controllers\BoatOperationController@index')}}"><i class="fa fa-taxi"></i>
                @lang('ezyboat::lang.list_boat_trips')</a>
        </li>
        <li class="{{ $request->segment(2) == 'settings' ? 'active' : '' }}"><a
                href="{{action('\Modules\Ezyboat\Http\Controllers\SettingController@index')}}"><i class="fa fa-cogs"></i>
                @lang('ezyboat::lang.fleet_settings')</a>
        </li>

    </ul>
</li>