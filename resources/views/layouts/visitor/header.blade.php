@inject('request', 'Illuminate\Http\Request')
@php
    $top_belt_bg = DB::table('site_settings')->where('id', 1)->select('topBelt_background_color')->first()->topBelt_background_color;
@endphp
<!-- Main Header -->
  <header class="main-header no-print">
    <a href="{{route('visitor-home')}}" class="logo" style="background: {{ $top_belt_bg}};">
      
      <span class="logo-lg">{{ Auth::User()->name }}</span>
    </a>

    <!-- Header Navbar -->
    <nav class="navbar navbar-static-top" role="navigation" style="background: {{ $top_belt_bg}};">
      <!-- Sidebar toggle button-->
      <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
           <span class="sr-only">Toggle navigation</span>
        <span class="sr-only">Toggle navigation</span>
      </a>
      @if(App\System::getProperty('show_referral_code'))
      <h4 class="pull-left" style="color: #fff; font-weight: bold;">@lang('lang_v1.referral_code'): {{ Auth::User()->unique_code }}</h4>
      @endif

      <!-- Navbar Right Menu -->
      <div class="navbar-custom-menu">

        @if(Module::has('Essentials'))
          @includeIf('essentials::layouts.partials.header_part')
        @endif

        <button id="btnCalculator" title="@lang('lang_v1.calculator')" type="button" class="btn btn-success btn-flat pull-left m-8 hidden-xs btn-sm mt-10 popover-default" tabindex="-1" data-toggle="click" data-trigger="click" data-content='@include("layouts.partials.calculator")' data-html="true" data-placement="bottom">
            <strong><i class="fa fa-calculator fa-lg" aria-hidden="true"></i></strong>
        </button>
        
  

        <div class="m-8 pull-left mt-15 hidden-xs" style="color: #fff;"><strong>{{ @format_date('now') }}</strong></div>

        <ul class="nav navbar-nav">
          @include('layouts.partials.header-notifications')
          <!-- User Account Menu -->
          <li class="dropdown user user-menu">
            <!-- Menu Toggle Button -->
            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
              <!-- The user image in the navbar-->
          
              <!-- hidden-xs hides the username on small devices so only the image appears. -->
              <span>{{ Auth::User()->name }}</span>
            </a>
            <ul class="dropdown-menu">
              <!-- The user image in the menu -->
              <li class="user-header">
           
                <p>
                  {{ Auth::User()->name }}
                </p>
              </li>
              <!-- Menu Body -->
              <!-- Menu Footer-->
              <li class="user-footer">
                <div class="pull-left">
                  <a href="{{action('UserController@getProfile')}}" class="btn btn-default btn-flat">@lang('lang_v1.profile')</a>
                </div>
                <div class="pull-right">
                  <a href="{{action('Auth\LoginController@logout')}}" class="btn btn-default btn-flat">@lang('lang_v1.sign_out')</a>
                </div>
              </li>
            </ul>
          </li>
          <!-- Control Sidebar Toggle Button -->
        </ul>
      </div>
    </nav>
  </header>
