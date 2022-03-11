<div class="col-md-12">
	<form action="{{action('\Modules\Superadmin\Http\Controllers\SubscriptionController@confirm', [$package->id])}}" method="POST">
	 	{{ csrf_field() }}
	 	<input type="hidden" name="gateway" value="{{$k}}">
	 	<input type="hidden" name="custom_price" value="@if(!empty($custom_price)){{$custom_price}}@else{{$package->price}}@endif">
	 	<input type="hidden" name="option_variables_selected" value="@if(!empty($option_variables_selected)){{$option_variables_selected}}@endif">
	 	<input type="hidden" name="module_selected" value="@if(!empty($module_selected)){{$module_selected}}@endif">

	 	<button type="submit" class="btn btn-success"> <i class="fa fa-hand-grab-o"></i> {{$v}}</button>
	</form>
	<p class="help-block">@lang('superadmin::lang.offline_pay_helptext')</p>
</div>