@extends('layouts.app')
@section('title', __('superadmin::lang.superadmin') . ' | ' . __('superadmin::lang.packages'))

@section('content')
	
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>@lang('superadmin::lang.packages') <small>@lang('superadmin::lang.all_packages')</small></h1>
    <!-- <ol class="breadcrumb">
        <a href="#"><i class="fa fa-dashboard"></i> Level</a><br/>
        <li class="active">Here<br/>
    </ol> -->
</section>

<!-- Main content -->
<section class="content">
	@include('superadmin::layouts.partials.currency')

	<div class="box">
        <div class="box-header">
            <h3 class="box-title">&nbsp;</h3>
        	<div class="box-tools">
                <a href="{{action('\Modules\Superadmin\Http\Controllers\PackagesController@create')}}" 
                    class="btn btn-block btn-primary">
                	<i class="fa fa-plus"></i> @lang( 'messages.add' )</a>
            </div>
        </div>

        <div class="box-body">
			@foreach ($packages as $package)
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

							<div class="row">
								@if($package->is_active == 1)
									<span class="badge bg-green">
										@lang('superadmin::lang.active')
									</span>
								@else
									<span class="badge bg-red">
									@lang('superadmin::lang.inactive')
									</span>
								@endif
								
								<a href="{{action('\Modules\Superadmin\Http\Controllers\PackagesController@edit', [$package->id])}}" class="btn btn-box-tool" title="edit"><i class="fa fa-edit"></i></a>
								<a href="{{action('\Modules\Superadmin\Http\Controllers\PackagesController@destroy', [$package->id])}}" class="btn btn-box-tool link_confirmation" title="delete"><i class="fa fa-trash"></i></a>
              					
							</div>
						</div>
						<!-- /.box-header -->
						<div class="box-body text-center">
							@if (!$is_patient_package)
							@if($package->location_count == 0)
								@lang('superadmin::lang.unlimited')
							@else
								{{$package->location_count}}
							@endif

							@lang('business.business_locations')
							<br/>
							@endif

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
							<br/>
						
							@if (!$is_patient_package)
							@if($package->product_count == 0)
								@lang('superadmin::lang.unlimited')
							@else
								{{$package->product_count}}
							@endif

							@lang('superadmin::lang.products')
							<br/>
							@endif

							@if (!$is_patient_package)
							@if($package->invoice_count == 0)
								@lang('superadmin::lang.unlimited')
							@else
								{{$package->invoice_count}}
							@endif

							@lang('superadmin::lang.invoices')
							<br/>
							@endif

							@if(!empty($package->fleet_module))
								@if($package->vehicle_count == 0)
									@lang('superadmin::lang.unlimited')
								@else
									{{$package->vehicle_count}}
								@endif
									@lang('superadmin::lang.no_of_vehicle')
								<br />
							@endif

							@if($package->trial_days != 0)
									{{$package->trial_days}} @lang('superadmin::lang.trial_days')
								<br/>
							@endif
							@php
								$modules = json_decode($package->package_permissions, true);
								$manage_module_enable = json_decode($package->manage_module_enable, true);
							@endphp
							@if(empty($package->only_for_business))
							@if(!empty($modules['account_access']))
							@lang('superadmin::lang.accounting_module')
							<br />
							@endif
							@else
							@if(!empty($manage_module_enable['access_account']))
							@lang('superadmin::lang.accounting_module')
							<br />
							@endif
							@endif
							@if(!empty($package->pump_operator_dashboard))
							@lang('superadmin::lang.pump_operator_dashboard')
							<br />
							@endif
							@if(!empty($package->property_module))
							@lang('superadmin::lang.property_module')
							<br />
							@endif

							@if(!empty($package->custom_permissions))
								@foreach($package->custom_permissions as $permission => $value)
									@isset($permission_formatted[$permission])
										{{$permission_formatted[$permission]}}
										<br/>
									@endisset
								@endforeach
							@endif
							@php
								$currency_symbol = App\Currency::select('symbol')->where('id', $package->currency_id)->first();
								if(empty($currency_symbol)){
									$currency_symbol = '$';
								}else{
									$currency_symbol = $currency_symbol->symbol;

								}
							@endphp
							<h3 class="text-center">
								@if($package->price != 0)
									<span>
									{{$currency_symbol}} {{number_format($package->price, 2)}}
									</span>

									<small>
										/ {{$package->interval_count}} {{__('lang_v1.' . $package->interval)}}
									</small>
								@else
									@lang('superadmin::lang.free_for_duration', ['duration' => $package->interval_count . ' ' . __('lang_v1.' . $package->interval)])
								@endif
							</h3>

						</div>
						<!-- /.box-body -->

						<div class="box-footer text-center">
							{{$package->description}}
						</div>
					</div>
					<!-- /.box -->
                </div>
                @if($loop->iteration%3 == 0)
    				<div class="clearfix"></div>
    			@endif
            @endforeach

            <div class="col-md-12">
                {{ $packages->links() }}
            </div>
        </div>

    </div>

    <div class="modal fade brands_modal" tabindex="-1" role="dialog" 
    	aria-labelledby="gridSystemModalLabel">
    </div>

</section>
<!-- /.content -->

@endsection