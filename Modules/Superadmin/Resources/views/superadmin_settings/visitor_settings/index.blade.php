<div class="pos-tab-content">
    <div class="row">
        <div class="col-md-12">
            <div class="settlement_tabs">
                <ul class="nav nav-tabs">
                    @if($permissions['visitors_registration_setting'])
                    <li class="@if(empty(session('status.tab'))) active @endif">
                        <a href="#visitor_settings" class="visitor_settings" data-toggle="tab">
                            <strong>@lang('visitor::lang.settings')</strong>
                        </a>
                    </li>
                    @endif
                    @if($permissions['visitors_district'])
                    <li class="@if(session('status.tab') =='town') active @endif">
                        <a href="#district_tab" class="district_tab" data-toggle="tab">
                            <strong>@lang('visitor::lang.district')</strong>
                        </a>
                    </li>
                    @endif
                    @if($permissions['visitors_town'])
                    <li class="@if(session('status.tab') =='town_tab') active @endif">
                        <a href="#town_tab" class="town_tab" data-toggle="tab">
                            <strong>@lang('visitor::lang.town')</strong>
                        </a>
                    </li>
                    @endif
                    <li class="@if(session('status.tab') =='welcome_email') active @endif">
                        <a href="#welcome_email" class="welcome_email" data-toggle="tab">
                            <strong>@lang('superadmin::lang.welcome_email')</strong>
                        </a>
                    </li>


                </ul>
                <div class="tab-content">
                    @if($permissions['visitors_registration_setting'])
                    <div class="tab-pane @if(empty(session('status.tab'))) active @endif" id="visitor_settings">
                        @include('superadmin::superadmin_settings.visitor_settings.system.index')
                    </div>
                    @endif
                    @if($permissions['visitors_district'])
                    <div class="tab-pane @if(session('status.tab') =='district_tab') active @endif" id="district_tab">
                        @include('superadmin::superadmin_settings.visitor_settings.district.index')
                    </div>
                    @endif
                    @if($permissions['visitors_town'])
                    <div class="tab-pane @if(session('status.tab') =='town_tab') active @endif" id="town_tab">
                        @include('superadmin::superadmin_settings.visitor_settings.town.index')
                    </div>
                    @endif
                    <div class="tab-pane @if(session('status.tab') =='welcome_email') active @endif" id="welcome_email">
                        @include('superadmin::superadmin_settings.visitor_settings.welcome_email')
                    </div>


                </div>
            </div>
        </div>
    </div>
</div>