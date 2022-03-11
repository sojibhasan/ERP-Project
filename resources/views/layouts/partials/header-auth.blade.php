@inject('request', 'Illuminate\Http\Request')

<div class="container-fluid">
	@php

	$lang_btn = App\System::getProperty('enable_lang_btn_login_page');
	$register_btn = App\System::getProperty('enable_register_btn_login_page');
	$pricing_btn = App\System::getProperty('enable_pricing_btn_login_page');
	@endphp
	<!-- Language changer -->
	<div class="row">
		<div class="col-md-offset-1 col-md-4">
			<div class="pull-left mt-10">
				@if($lang_btn)
				<select class="form-control input-sm" id="change_lang">
					@foreach(config('constants.langs') as $key => $val)
					<option value="{{$key}}" @if( (empty(request()->lang) && config('app.locale') == $key)
						|| request()->lang == $key)
						selected
						@endif
						>
						{{$val['full_name']}}
					</option>
					@endforeach
				</select>
				@endif
			</div>
		</div>
		<div class="col-md-6">
			<div class="pull-right text-white" style="margin-top:10px;">
				@if(!($request->segment(1) == 'business' && $request->segment(2) == 'register'))
		
				<!-- Register Url -->
				@if(env('ALLOW_REGISTRATION', true))
				@if($register_btn)
				<a data-toggle="modal" data-target="#register_modal" href=""
					class="btn bg-maroon btn-flat">{{ __('business.register_now') }}</a>
				@endif
				<!-- pricing url -->
				@if(Route::has('pricing') && config('app.env') != 'demo' && $request->segment(1) != 'pricing')
				<a
					ref="{{ action('\Modules\Superadmin\Http\Controllers\PricingController@index') }}">@lang('superadmin::lang.pricing')</a>
				@endif
				@endif
				@endif

				@if(!($request->segment(1) == 'business' && $request->segment(2) == 'register') && $request->segment(1)
				!= 'login')
				<a class="btn btn-success"
					href="{{ action('Auth\LoginController@login') }}@if(!empty(request()->lang)){{'?lang=' . request()->lang}} @endif">{{ __('business.sign_in') }}</a>
				@endif
			</div>
		</div>
	</div>
</div>