<li
  class="treeview {{ in_array($request->segment(1), ['ran']) && $request->segment(2) != 'issue-customer-bill'? 'active active-sub' : '' }}"
  id="tour_step5">
  <a href="#"><i class="fa fa-globe fa-lg"></i> <span>@lang('ran::lang.ran')</span>
    <span class="pull-right-container">
      <i class="fa fa-angle-left pull-right"></i>
    </span>
  </a>
  <ul class="treeview-menu">
    <li class="{{ $request->segment(2) == 'gold' ? 'active' : '' }}"><a
        href="{{action('\Modules\Ran\Http\Controllers\GoldController@index')}}"><i class="fa fa-globe"></i>
        @lang('ran::lang.gold')</a>
    </li>
    <li class="{{ $request->segment(2) == 'production' ? 'active' : '' }}"><a
        href="{{action('\Modules\Ran\Http\Controllers\ProductionController@index')}}"><i class="fa fa-industry"></i>
        @lang('ran::lang.production')</a></li>

    <li class="{{ $request->segment(2) == 'goldsmith' ? 'active' : '' }}"><a
        href="{{action('\Modules\Ran\Http\Controllers\GoldSmithController@index')}}"><i class="fa fa-id-badge"></i>
        @lang('ran::lang.goldsmith')</a></li>

  </ul>
</li>