@foreach ($packages as $package)
@if($package->is_private == 1 && !auth()->user()->can('superadmin'))
@php
continue;
@endphp
@endif
@php
$is_patient_package = 0;
if($package->hospital_system && in_array('patient' , json_decode($package->hospital_business_type))){
$is_patient_package = 1;
}else{
$is_patient_package = 0;
}
@endphp
<div class="col-md-4">

	<div class="box box-success hvr-grow-shadow">
		<div class="box-header with-border text-center">
			<h2 class="box-title">{{$package->name}}</h2>
		</div>

		<!-- /.box-header -->
		<div class="box-body text-center">

			@if (!$is_patient_package)
			<i class="fa fa-check text-success"></i>
			@if($package->location_count == 0)
			@lang('superadmin::lang.unlimited')
			@else
			{{$package->location_count}}
			@endif

			@lang('business.business_locations')
			<hr />
			@endif


			<i class="fa fa-check text-success"></i>
			@if($package->user_count == 0)
			@lang('superadmin::lang.unlimited')
			@else
			{{$package->user_count}}
			@endif

			@if ($is_patient_package)
			@lang('superadmin::lang.family_members')
			@else
			@lang('superadmin::lang.users')
			@endif
			<hr />


			@if (!$is_patient_package)
			<i class="fa fa-check text-success"></i>
			@if($package->product_count == 0)
			@lang('superadmin::lang.unlimited')
			@else
			{{$package->product_count}}
			@endif

			@lang('superadmin::lang.products')
			<hr />
			@endif

			@if (!$is_patient_package)
			<i class="fa fa-check text-success"></i>
			@if($package->invoice_count == 0)
			@lang('superadmin::lang.unlimited')
			@else
			{{$package->invoice_count}}
			@endif

			@lang('superadmin::lang.invoices')
			<hr />
			@endif

			@if(!empty($package->fleet_module))
				@if($package->vehicle_count == 0)
					@lang('superadmin::lang.unlimited')
				@else
					{{$package->vehicle_count}}
				@endif
					@lang('superadmin::lang.no_of_vehicle')
				<hr />
			@endif

			@if(!empty($package->custom_permissions))
			@foreach($package->custom_permissions as $permission => $value)
			@isset($permission_formatted[$permission])
			<i class="fa fa-check text-success"></i>
			{{$permission_formatted[$permission]}}
			<hr />
			@endisset
			@endforeach
			@endif

			@if($package->trial_days != 0)
			<i class="fa fa-check text-success"></i>
			{{$package->trial_days}} @lang('superadmin::lang.trial_days')
			<hr />
			@endif

			@php
				$modules = json_decode($package->package_permissions, true);
			@endphp
			@if(!empty($modules['account_access']))
			<i class="fa fa-check text-success"></i>
			@lang('superadmin::lang.accounting_module')
			<hr />
			@endif
		
			@if(!empty($modules['pump_operator_dashboard']))
			<i class="fa fa-check text-success"></i>
			@lang('superadmin::lang.pump_operator_dashboard')
			<hr />
			@endif

			<h3 class="text-center">
				@php
				$interval_type = !empty($intervals[$package->interval]) ? $intervals[$package->interval] : __('lang_v1.'
				. $package->interval);
				$currency_symbol = App\Currency::where('id', $package->currency_id)->first();
				@endphp
				@if($package->price != 0)
				<span>
					{{!empty($currency_symbol)?$currency_symbol->symbol: ''}} {{number_format($package->price, 2)}}
				</span>

				<small>
					/ {{$package->interval_count}} {{$interval_type}}
				</small>
				@else
				@lang('superadmin::lang.free_for_duration', ['duration' => $package->interval_count . ' ' .
				$interval_type])
				@endif
			</h3>
		</div>
		<!-- /.box-body -->

		<div class="box-footer text-center">
			@if($package->enable_custom_link == 1)
			<a href="{{$package->custom_link}}" class="btn btn-block btn-success">{{$package->custom_link_text}}</a>
			@else
			@if(isset($action_type) && $action_type == 'register')
			@if ($package->number_of_branches || $package->number_of_users || $package->number_of_products ||
			$package->number_of_periods || $package->number_of_customers || $package->no_of_vehicles )
			<a href="#"
				data-href="{{action('\Modules\Superadmin\Http\Controllers\SubscriptionController@getPackageVariables', [$package->id])}}"
				class="btn-modal btn btn-block btn-success register_form_modal"
				data-container=".package_veriables_modal"
				data-is_visitor_pacakge="{{$package->visitors_registration_module}}" id="{{$package->id}}">
				@if($package->price != 0)
				@lang('superadmin::lang.register_subscribe')
				@else
				@lang('superadmin::lang.subscribe')
				@endif
			</a>
			@else
			@if($package->hospital_system && in_array('patient' , json_decode($package->hospital_business_type)))

			<a href="" data-toggle="modal" data-target="#patient_register_modal" class="btn btn-block btn-success"
				id="{{$package->id}}">
				@if($package->price != 0)
				@lang('superadmin::lang.register_subscribe')
				@else
				@lang('superadmin::lang.register_free')
				@endif
			</a>
			@else
			<a href="" data-toggle="modal" data-target="#register_modal"
				class="btn btn-block btn-success register_form_modal" id="{{$package->id}}"
				data-is_visitor_pacakge="{{$package->visitors_registration_module}}">
				@if($package->price != 0)
				@lang('superadmin::lang.register_subscribe')
				@else
				@lang('superadmin::lang.register_free')
				@endif
			</a>
			@endif
			@endif
			@else
			@if ($package->number_of_branches || $package->number_of_users || $package->number_of_products ||
			$package->number_of_periods || $package->number_of_customers || $package->no_of_vehicles || $package->only_for_business )
			<a href="#"
				data-href="{{action('\Modules\Superadmin\Http\Controllers\SubscriptionController@getPackageVariables', [$package->id])}}"
				class="btn-modal btn btn-block btn-success register_form_modal"
				data-container=".package_veriables_modal">
				@else
				<a href="{{action('\Modules\Superadmin\Http\Controllers\SubscriptionController@pay', [$package->id])}}"
					class="btn btn-block btn-success">
					@endif

					@if($package->price != 0 && !$package->only_for_business)
					@lang('superadmin::lang.register_subscribe')
					@elseif($package->only_for_business)
					@lang('superadmin::lang.pay_and_subscribe')
					@else
					@lang('superadmin::lang.subscribe')
					@endif
				</a>
			@endif
			@endif

				{{$package->description}}

		</div>
	</div>
	<!-- /.box -->
</div>
@if($loop->iteration%3 == 0)
<div class="clearfix"></div>
@endif
@endforeach