<strong><i class="fa fa-mobile margin-r-5"></i> @lang('contact.mobile')</strong>
<p class="text-muted">
    {{ $crm->mobile }}
</p>
@if($crm->landline)
    <strong><i class="fa fa-phone margin-r-5"></i> @lang('contact.landline')</strong>
    <p class="text-muted">
        {{ $crm->landline }}
    </p>
@endif
@if($crm->alternate_number)
    <strong><i class="fa fa-phone margin-r-5"></i> @lang('contact.alternate_contact_number')</strong>
    <p class="text-muted">
        {{ $crm->alternate_number }}
    </p>
@endif

@if(!empty($crm->custom_field1))
    <strong>@lang('lang_v1.contact_custom_field1')</strong>
    <p class="text-muted">
        {{ $crm->custom_field1 }}
    </p>
@endif

@if(!empty($crm->custom_field2))
    <strong>@lang('lang_v1.contact_custom_field2')</strong>
    <p class="text-muted">
        {{ $crm->custom_field2 }}
    </p>
@endif

@if(!empty($crm->custom_field3))
    <strong>@lang('lang_v1.contact_custom_field3')</strong>
    <p class="text-muted">
        {{ $crm->custom_field3 }}
    </p>
@endif

@if(!empty($crm->custom_field4))
    <strong>@lang('lang_v1.contact_custom_field4')</strong>
    <p class="text-muted">
        {{ $crm->custom_field4 }}
    </p>
@endif