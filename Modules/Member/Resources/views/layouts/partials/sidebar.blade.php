<li class="treeview {{ in_array($request->segment(1), ['member-module', 'member']) ? 'active active-sub' : '' }}">
    <a href="#"><i class="fa fa-user-circle"></i> <span>@lang('member::lang.member_module')</span>
        <span class="pull-right-container">
            <i class="fa fa-angle-left pull-right"></i>
        </span>
    </a>
    <ul class="treeview-menu">
        <li class="{{ $request->segment(1) == 'member-module' && $request->segment(2) == 'members' ? 'active' : '' }}">
            <a href="{{action('\Modules\Member\Http\Controllers\MemberController@index')}}"><i
                    class="fa fa-list"></i>@lang('member::lang.list_member')</a>
        </li>
        <li
            class="{{ $request->segment(1) == 'member' && $request->segment(2) == 'suggestions' && $request->segment(3) == '' ? 'active' : '' }}">
            <a href="{{action('\Modules\Member\Http\Controllers\SuggestionController@index')}}"><i
                    class="fa fa-thumbs-o-up"></i>@lang('member::lang.list_suggestions')</a>
        </li>
        <li
            class="{{ $request->segment(1) == 'member-module' && $request->segment(2) == 'member-settings' ? 'active' : '' }}">
            <a href="{{action('\Modules\Member\Http\Controllers\MemberSettingController@index')}}"><i
                    class="fa fa-cogs"></i>@lang('member::lang.member_settings')</a>
        </li>

    </ul>
</li>