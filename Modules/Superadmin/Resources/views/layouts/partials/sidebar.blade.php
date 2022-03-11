@can('superadmin')
<li
	class="bg-red treeview {{ in_array($request->segment(1), ['superadmin', 'sample-medical-product-import', 'site-settings', 'pay-online']) ? 'active active-sub' : '' }}">
	<a href="#">
		<i class="fa fa-bank"></i>
		<span class="title">@lang('superadmin::lang.superadmin')</span>
		<span class="pull-right-container">
			<i class="fa fa-angle-left pull-right"></i>
		</span>
	</a>

	<ul class="treeview-menu">
		<li
			class="{{ empty($request->segment(2)) && $request->segment(1) != 'site-settings' ? 'active active-sub' : '' }}">
			<a href="{{action('\Modules\Superadmin\Http\Controllers\SuperadminController@index')}}">
				<i class="fa fa-bank"></i>
				<span class="title">
					@lang('superadmin::lang.superadmin')
				</span>
			</a>
		</li>

		<li class="{{ $request->segment(2) == 'business' ? 'active active-sub' : '' }}">
			<a href="{{action('\Modules\Superadmin\Http\Controllers\BusinessController@index')}}">
				<i class="fa fa-bank"></i>
				<span class="title">
					@lang('superadmin::lang.all_business')
				</span>
			</a>
		</li>
		<!-- superadmin subscription -->
		<li class="{{ $request->segment(2) == 'superadmin-subscription' ? 'active active-sub' : '' }}">
			<a href="{{action('\Modules\Superadmin\Http\Controllers\SuperadminSubscriptionsController@index')}}"><i
					class="fa fa-refresh"></i>
				<span class="title">@lang('superadmin::lang.subscription')</span>
			</a>
		</li>

		<li class="{{ $request->segment(2) == 'packages' ? 'active active-sub' : '' }}">
			<a href="{{action('\Modules\Superadmin\Http\Controllers\PackagesController@index')}}">
				<i class="fa fa-credit-card"></i>
				<span class="title">
					@lang('superadmin::lang.subscription_packages')
				</span>
			</a>
		</li>

		<li class="{{ $request->segment(2) == 'tenant-management' ? 'active active-sub' : '' }}">
			<a href="{{action('\Modules\Superadmin\Http\Controllers\TenantManagementController@index')}}">
				<i class="fa fa-sitemap"></i>
				<span class="title">
					@lang('superadmin::lang.tenant_management')
				</span>
			</a>
		</li>

		<li class="{{ $request->segment(2) == 'agent' ? 'active active-sub' : '' }}">
			<a href="{{action('\Modules\Superadmin\Http\Controllers\AgentController@index')}}">
				<i class="fa fa-user"></i>
				<span class="title">
					@lang('superadmin::lang.list_agents')
				</span>
			</a>
		</li>

		<li class="{{ $request->segment(2) == 'referrals' ? 'active active-sub' : '' }}">
			<a href="{{action('\Modules\Superadmin\Http\Controllers\ReferralController@index')}}">
				<i class="fa fa-link"></i>
				<span class="title">
					@lang('superadmin::lang.referrals')
				</span>
			</a>
		</li>

		<li class="{{ $request->segment(2) == 'settings' ? 'active active-sub' : '' }}">
			<a href="{{action('\Modules\Superadmin\Http\Controllers\SuperadminSettingsController@edit')}}">
				<i class="fa fa-cogs"></i>
				<span class="title">
					@lang('superadmin::lang.super_admin_settings')
				</span>
			</a>
		</li>

		<li class="{{ $request->segment(2) == 'imports-exports' ? 'active active-sub' : '' }}">
			<a href="{{action('\Modules\Superadmin\Http\Controllers\ImportExportController@index')}}">
				<i class="fa fa-arrows-alt"></i>
				<span class="title">
					@lang('superadmin::lang.import_export')
				</span>
			</a>
		</li>

		<li class="{{ $request->segment(1) == 'pay-online' ? 'active active-sub' : '' }}">
			<a href="{{action('\Modules\Superadmin\Http\Controllers\PayOnlineController@index')}}">
				<i class="fa fa-list"></i>
				<span class="title">
					@lang('superadmin::lang.pay_online_list')
				</span>
			</a>
		</li>
		<li class="{{ $request->segment(2) == 'help-explanation' ? 'active active-sub' : '' }}">
			<a href="{{action('\Modules\Superadmin\Http\Controllers\HelpExplanationController@index')}}">
				<i class="fa fa-info-circle"></i>
				<span class="title">
					@lang('superadmin::lang.help_explanation')
				</span>
			</a>
		</li>

		<li class="{{ $request->segment(2) == 'communicator' ? 'active active-sub' : '' }}">
			<a href="{{action('\Modules\Superadmin\Http\Controllers\CommunicatorController@index')}}">
				<i class="fa fa-envelope"></i>
				<span class="title">
					@lang('superadmin::lang.communicator')
				</span>
			</a>
		</li>

		<li class="{{ $request->segment(1) == 'site-settings'? 'active' : '' }}">
			<a href="{{route('site_settings.view')}}">
				<i class="fa fa-gears"></i> @lang('site_settings.settings')</a>
		</li>
		<li class="{{ $request->segment(1) == 'system_administration'? 'active' : '' }}">
			<a href="{{route('site_settings.help_view')}}">
				<i class="fa fa-question-circle"></i> @lang('site_settings.help')</a>
		</li>

		<li class="{{ $request->segment(1) == 'sample-medical-product-import' ? 'active' : '' }}">
			<a href="{{action('ImportMedicalProductController@index')}}">
				<i class="fa fa-download"></i><span>@lang('lang_v1.sample_medical_product_import')</span></a>
		</li>
</ul>
</li>
@endcan