<li class="treeview {{ in_array($request->segment(1), ['sms']) ? 'active active-sub' : '' }}">
    <a href="#"><i class="fa fa-lg fa-comments-o"></i> <span>@lang('sms::lang.sms')</span>
        <span class="pull-right-container">
            <i class="fa fa-angle-left pull-right"></i>
        </span>
    </a>
   <ul class="treeview-menu">
        {{--  @if($sms)
        @if(auth()->user()->can('sms.view') || auth()->user()->can('sms.edit') ||
        auth()->user()->can('sms.destory')|| auth()->user()->can('sms.create')) --}}
        <li class="{{ $request->segment(1) == 'sms' && $request->segment(2) == ''? 'active' : '' }}">
            <a href="{{action('\Modules\SMS\Http\Controllers\SMSController@index')}}"><i
                    class="fa fa-commenting-o"></i>@lang('sms::lang.list_sms')</a>
        </li>
        {{-- @endcan
        @endif --}}
     


    </ul>
</li>