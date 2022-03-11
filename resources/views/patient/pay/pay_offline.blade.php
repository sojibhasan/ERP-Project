<div class="col-md-12">
	<form action="{{action('\Modules\Superadmin\Http\Controllers\FamilySubscriptionController@confirm', [$package->id])}}" method="POST">
	 	{{ csrf_field() }}
	 	<input type="hidden" name="gateway" value="{{$k}}">
	 	<input type="hidden" name="order_id" value="{{$order_id}}">

	 	<button type="submit" class="btn btn-success"> <i class="fa fa-hand-grab-o"></i> {{$v}}</button>
	</form>
	<p class="help-block">@lang('superadmin::lang.offline_pay_helptext')</p>
</div>