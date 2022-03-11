
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
        <li class="{{ $request->segment(1) == 'customer-home' ? 'active' : '' }}">
          <a href="{{action('CustomerController@index')}}">
            <i class="fa fa-dashboard"></i> <span>
              @lang('home.home')</span>
          </a>
        </li>



        <li
        class="treeview {{  in_array( $request->segment(1), ['customer-sales', 'customer', 'customer-order', 'customer-order-list']) && $request->segment(2) != 'home' ? 'active active-sub' : '' }}"
        id="">
        <a href="#" id=""><i class="fa fa-arrow-circle-up"></i> <span>@lang('sale.sale')</span>
          <span class="pull-right-container">
            <i class="fa fa-angle-left pull-right"></i>
          </span>
        </a>
        <ul class="treeview-menu">
          <li class="{{ $request->segment(1) == 'customer' && $request->segment(2) == 'order' && $request->segment(3) == '' ? 'active' : '' }}"><a
              href="{{action('Ecom\EcomCustomerOrderController@index')}}"><i class="fa fa-plus"></i>@lang('customer.add_order')</a>
          </li>
          <li class="{{ $request->segment(1) == 'customer' && $request->segment(2) == 'order' && $request->segment(3) == 'lists' ? 'active' : '' }}"><a
              href="{{action('Ecom\EcomCustomerOrderController@getOrders')}}"><i class="fa fa-list-ol"></i>@lang('lang_v1.list_order')</a>
          </li>
          <li class="{{$request->segment(1) == 'customer' && $request->segment(2) == 'order' && $request->segment(3) == 'uploaded' ? 'active' : '' }}"><a
              href="{{action('Ecom\EcomCustomerOrderController@getUploadedOrders')}}"><i class="fa fa-upload"></i>@lang('customer.uploaded_order')</a>
          </li>
          <li class="{{$request->segment(1) == 'customer' && $request->segment(2) == 'order' && $request->segment(3) == 'uploaded' ? 'active' : '' }}"><a
              href="{{action('Ecom\EcomCustomerOrderController@getUploadedOrders')}}"><i class="fa fa-upload"></i>@lang('customer.uploaded_order')</a>
          </li>
          <li class="{{$request->segment(1) == 'customer' && $request->segment(2) == 'details'? 'active' : '' }}"><a
              href="{{action('Ecom\ContactController@index')}}"><i class="fa fa-address-book"></i>@lang('customer.contacts')</a>
          </li>

        </ul>
      </li>
      </ul>
    </section>
</aside>
       