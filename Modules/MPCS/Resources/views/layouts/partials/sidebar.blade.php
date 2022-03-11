<li
class="treeview {{ in_array($request->segment(1), ['mpcs']) ? 'active active-sub' : '' }}"
id="tour_step5">
<a href="#" id="tour_step5_menu"><i class="fa fa-calculator"></i> <span>@lang('mpcs::lang.mpcs')</span>
  <span class="pull-right-container">
    <i class="fa fa-angle-left pull-right"></i>
  </span>
</a>
<ul class="treeview-menu">
  @if(auth()->user()->can('f9c_form') || auth()->user()->can('f15a9abc_form') || auth()->user()->can('f16a_form') || auth()->user()->can('f21c_form'))
  <li class="{{ $request->segment(1) == 'mpcs' && $request->segment(2) == 'form-set-1' ? 'active' : '' }}"><a
      href="{{action('\Modules\MPCS\Http\Controllers\MPCSController@FromSet1')}}"><i class="fa fa-file-text-o"></i>@lang('mpcs::lang.form_set_1')</a>
  </li>
  @endif
  @if(auth()->user()->can('f17_form'))
  <li class="{{ $request->segment(1) == 'mpcs' && $request->segment(2) == 'F17' ? 'active' : '' }}"><a
      href="{{action('\Modules\MPCS\Http\Controllers\F17FormController@index')}}"><i class="fa fa-file-text-o"></i>@lang('mpcs::lang.F17_form')</a>
  </li>
  @endif
  @if(auth()->user()->can('f14b_form') || auth()->user()->can('f20_form'))
  <li class="{{ $request->segment(1) == 'mpcs' && $request->segment(2) == 'F14B_F20_Forms' ? 'active' : '' }}"><a
      href="{{action('\Modules\MPCS\Http\Controllers\F20F14bFormController@index')}}"><i class="fa fa-file-text-o"></i>@lang('mpcs::lang.F20andF14b_form')</a>
  </li>
  @endif
  @if(auth()->user()->can('f21_form'))
  <li class="{{ $request->segment(1) == 'mpcs' && $request->segment(2) == 'F21' ? 'active' : '' }}"><a
      href="{{action('\Modules\MPCS\Http\Controllers\MPCSController@F21')}}"><i class="fa fa-file-text-o"></i>@lang('mpcs::lang.F21_form')</a>
  </li>
  @endif
  @if(auth()->user()->can('f22_stock_taking_form'))
  <li class="{{ $request->segment(1) == 'mpcs' && $request->segment(2) == 'F22_stock_taking' ? 'active' : '' }}"><a
      href="{{action('\Modules\MPCS\Http\Controllers\F22FormController@F22StockTaking')}}"><i class="fa fa-file-text-o"></i>@lang('mpcs::lang.F22StockTaking_form')</a>
  </li>
  @endif
  <li class="{{ $request->segment(1) == 'mpcs' && $request->segment(2) == 'forms-setting' ? 'active' : '' }}"><a
      href="{{action('\Modules\MPCS\Http\Controllers\FormsSettingController@index')}}"><i class="fa fa-cogs"></i>@lang('mpcs::lang.mpcs_forms_setting')</a>
  </li>
  
</ul>
</li>