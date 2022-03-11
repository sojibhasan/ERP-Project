<li class="treeview {{ in_array($request->segment(1), ['property']) ? 'active active-sub' : '' }}" id="tour_step5">
  <a href="#"><i class="fa fa-building"></i> <span>@lang('property::lang.property')</span>
    <span class="pull-right-container">
      <i class="fa fa-angle-left pull-right"></i>
    </span>
  </a>
  <ul class="treeview-menu">
    <li class="{{ $request->segment(2) == 'list-price-changes' ? 'active' : '' }}"><a
              href="{{action('\Modules\Property\Http\Controllers\PriceChangesController@index')}}"><i
                class="fa fa-building"></i>
        @lang('property::lang.list_price_changes')</a></li>
    <li class="{{ $request->segment(2) == 'sale-and-customer-payment' && $request->segment(3) == 'dashboard' ? 'active' : '' }}"><a
      href="{{action('\Modules\Property\Http\Controllers\SaleAndCustomerPaymentController@dashboard', ['type' => 'customer'])}}"><i
        class="fa fa-dashboard"></i>
      @lang('property::lang.sales_dashboard')</a></li>
    @can('property.customer.view')
    <li class="{{ $request->segment(2) == 'contacts' && $request->input('type') == 'customer' ? 'active' : '' }}"><a
        href="{{action('\Modules\Property\Http\Controllers\ContactController@index', ['type' => 'customer'])}}"><i
          class="fa fa-star"></i>
        @lang('property::lang.property_customer')</a></li>
    @endcan
    @can('property.list.view')
    <li class="{{ $request->segment(2) == 'properties' ? 'active' : '' }}">
      <a href="{{action('\Modules\Property\Http\Controllers\PropertyController@index')}}"><i class="fa fa-building"></i>
        @lang('property::lang.list_properties')</a>
    </li>
    @endcan
    @can('property.purchase.view')
    <li class="{{ $request->segment(2) == 'purchases' ? 'active' : '' }}">
      <a href="{{action('\Modules\Property\Http\Controllers\PurchaseController@index')}}"><i
          class="fa fa-arrow-circle-down"></i>
        @lang('property::lang.property_purchase')</a>
    </li>
    @endcan
    @can('property.purchase.view')
      <li class="{{ $request->segment(2) == 'reports' ? 'active' : '' }}">
        <a href="{{action('\Modules\Property\Http\Controllers\ReportController@index')}}"><i
                  class="fa fa-file"></i>
          @lang('property::lang.reports')</a>
      </li>
    @endcan
    @can('property.settings.access')
    <li class="{{ $request->segment(2) == 'settings' ? 'active' : '' }}">
      <a href="{{action('\Modules\Property\Http\Controllers\SettingController@index')}}"><i class="fa fa-cogs"></i>
        @lang('property::lang.settings')</a>
    </li>
    @endcan
  </ul>
</li>
@if($list_easy_payment)
@if(auth()->user()->can('list_easy_payments.access'))
<li class="treeview {{  in_array( $request->segment(2), ['easy-payments']) ? 'active active-sub' : '' }}">
  <a href="{{action('\Modules\Property\Http\Controllers\EasyPaymentController@index')}}"><i class="fa fa-money"></i>
    <span>@lang('property::lang.list_easy_payments')</span>
  </a>
</li>
@endif
@endif
