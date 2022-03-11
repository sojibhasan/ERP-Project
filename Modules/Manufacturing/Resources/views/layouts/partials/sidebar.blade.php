{{-- @if($__is_mfg_enabled) --}}
	<li class="treeview bg-manufacturing {{ in_array($request->segment(1), ['manufacturing']) ? 'active active-sub' : '' }}">
	    <a href="#">
	        <i class="fa fa-industry"></i>
	        <span class="title">@lang('manufacturing::lang.manufacturing')</span>
	        <span class="pull-right-container">
	            <i class="fa fa-angle-left pull-right"></i>
	        </span>
	    </a>

	    <ul class="treeview-menu">
		    	<li class="{{ $request->segment(1) == 'manufacturing'? 'active active-sub' : '' }}">
					<a href="{{action('\Modules\Manufacturing\Http\Controllers\ManufacturingController@index')}}">
						<i class="fa fa-industry"></i>
						<span class="title">
							@lang('manufacturing::lang.manufacturing')
						</span>
				  	</a>
				</li>
        </ul>
	</li>
