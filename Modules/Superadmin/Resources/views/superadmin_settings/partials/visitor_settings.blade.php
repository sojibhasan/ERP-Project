<div class="pos-tab-content">
    <div class="row">
        <div class="col-md-12">
            <div class="settlement_tabs">
                <ul class="nav nav-tabs">
                    <li class="@if(empty(session('status.tab'))) active @endif">
                        <a href="#visitor_settings" class="visitor_settings" data-toggle="tab">
                            <strong>@lang('visitor::lang.settings')</strong>
                        </a>
                    </li>
                    <li class="@if(session('status.tab') =='town') active @endif">
                        <a href="#district_tab" class="district_tab" data-toggle="tab">
                            <strong>@lang('visitor::lang.district')</strong>
                        </a>
                    </li>
                    <li class="@if(session('status.tab') =='town_tab') active @endif">
                        <a href="#town_tab" class="town_tab" data-toggle="tab">
                            <strong>@lang('visitor::lang.town')</strong>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <div class="tab-content">
        <div class="tab-pane @if(empty(session('status.tab'))) active @endif" id="visitor_settings">
            @include('superadmin::superadmin_settings.visitor_settings.system.index')
        </div>
        <div class="tab-pane @if(session('status.tab') =='district_tab') active @endif" id="district_tab">
            @include('superadmin::superadmin_settings.visitor_settings.district.index')
        </div>
        <div class="tab-pane @if(session('status.tab') =='town_tab') active @endif" id="town_tab">
            @include('superadmin::superadmin_settings.visitor_settings.town.index')
        </div>
    </div>
</div>