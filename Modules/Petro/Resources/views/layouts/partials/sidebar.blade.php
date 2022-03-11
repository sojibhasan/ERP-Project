<li

class="treeview {{ in_array($request->segment(1), ['petro']) && $request->segment(2) != 'issue-customer-bill'? 'active active-sub' : '' }}"

id="tour_step5">

<a href="#" id="tour_step5_menu"><i class="fa fa-tint fa-lg"></i> <span>@lang('petro::lang.petro')</span>

  <span class="pull-right-container">

    <i class="fa fa-angle-left pull-right"></i>

  </span>

</a>
<ul class="treeview-menu">
@if($enable_petro_dashboard)
  <li class="{{ $request->segment(1) == 'petro' && $request->segment(2) == 'dashboard' ? 'active' : '' }}"><a

      href="{{action('\Modules\Petro\Http\Controllers\PetroController@index')}}"><i class="fa fa-tachometer"></i> @lang('petro::lang.dashboard')</a>

  </li>
@endif
@if($enable_petro_task_management)
  <li class="{{ $request->segment(1) == 'petro' && $request->segment(2) == 'tank-management' ? 'active' : '' }}"><a

      href="{{action('\Modules\Petro\Http\Controllers\FuelTankController@index')}}"><i class="fa fa-ship"></i> @lang('petro::lang.tank_management')</a>

  </li>
@endif

  <li class="{{ $request->segment(1) == 'petro' && $request->segment(2) == 'pump-management' ? 'active' : '' }}"><a

      href="{{action('\Modules\Petro\Http\Controllers\PumpController@index')}}"><i class="fa fa-superpowers"></i> @lang('petro::lang.pump_management')</a>

  </li>
@if($enable_petro_pumper_management)
  <li class="{{ $request->segment(1) == 'petro' && $request->segment(2) == 'pump-operators' && $request->segment(3) == '' ? 'active' : '' }}"><a

      href="{{action('\Modules\Petro\Http\Controllers\PumpOperatorController@index')}}"><i class="fa fa-user"></i> @lang('petro::lang.pumper_management')</a>

  </li>
@endif
@if($enable_petro_daily_collection)
  <li class="{{ $request->segment(1) == 'petro' && $request->segment(2) == 'daily-collection' ? 'active' : '' }}"><a

      href="{{action('\Modules\Petro\Http\Controllers\DailyCollectionController@index')}}"><i class="fa fa-ravelry"></i> @lang('petro::lang.daily_collection')</a>

  </li>
@endif
@if($enable_petro_settlement)
  <li class="{{ $request->segment(1) == 'petro' && $request->segment(2) == 'settlement' && $request->segment(3) == 'create' ? 'active' : '' }}"><a

      href="{{action('\Modules\Petro\Http\Controllers\SettlementController@create')}}"><i class="fa fa-eercast"></i> @lang('petro::lang.settlement')</a>

  </li>
@endif
@if($enable_petro_list_settlement)
  <li class="{{ $request->segment(1) == 'petro' && $request->segment(2) == 'settlement' && $request->segment(3) == '' ? 'active' : '' }}"><a

      href="{{action('\Modules\Petro\Http\Controllers\SettlementController@index')}}"><i class="fa fa-list"></i> @lang('petro::lang.list_settlement')</a>

  </li>
@endif
@if($enable_petro_dip_management)
  <li class="{{ $request->segment(1) == 'petro' && $request->segment(2) == 'dip-management' && $request->segment(3) == '' ? 'active' : '' }}"><a

      href="{{action('\Modules\Petro\Http\Controllers\DipManagementController@index')}}"><i class="fa fa-thermometer"></i> @lang('petro::lang.dip_management')</a>

  </li>
@endif
  

</ul>

</li>



@if($issue_customer_bill)

@can('issue_customer_bill.access')

<li

  class="treeview {{in_array($request->segment(2), ['issue-customer-bill']) ? 'active active-sub' : '' }}">

  <a href="#" id="tour_step6_menu"><i class="fa fa-file-text"></i>

    <span>@lang('petro::lang.bill_to_customer')</span>

    <span class="pull-right-container">

      <i class="fa fa-angle-left pull-right"></i>

    </span>

  </a>

  <ul class="treeview-menu">

    <li class="{{ $request->segment(2) == 'issue-customer-bill'? 'active' : '' }}"><a href="{{action('\Modules\Petro\Http\Controllers\IssueCustomerBillController@index')}}"><i class="fa fa-list"></i>@lang('petro::lang.issue_bills_customer')</a></li>

  </ul>

</li>

@endcan

@endif