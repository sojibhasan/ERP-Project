<strong><i class="fa fa-mobile margin-r-5"></i> @lang('contact.mobile')</strong>
<p class="text-muted">
    {{ $contact->mobile }}
</p>
@php
if(!$contact->tax_number)
$contact->tax_number = 123456789;
@endphp
@if($contact->tax_number)
    <strong> @lang('contact.tax_no')</strong>
    <p class="text-muted">
        {{ $contact->tax_number }}
    </p>
@endif
@if($contact->landline)
    <strong><i class="fa fa-phone margin-r-5"></i> @lang('contact.landline')</strong>
    <p class="text-muted">
        {{ $contact->landline }}
    </p>
@endif
@if($contact->alternate_number)
    <strong><i class="fa fa-phone margin-r-5"></i> @lang('contact.alternate_contact_number')</strong>
    <p class="text-muted">
        {{ $contact->alternate_number }}
    </p>
@endif

@if(!empty($contact->custom_field1))
    <strong>@lang('lang_v1.contact_custom_field1')</strong>
    <p class="text-muted">
        {{ $contact->custom_field1 }}
    </p>
@endif

@if(!empty($contact->custom_field2))
    <strong>@lang('lang_v1.contact_custom_field2')</strong>
    <p class="text-muted">
        {{ $contact->custom_field2 }}
    </p>
@endif

@if(!empty($contact->custom_field3))
    <strong>@lang('lang_v1.contact_custom_field3')</strong>
    <p class="text-muted">
        {{ $contact->custom_field3 }}
    </p>
@endif

@if(!empty($contact->custom_field4))
    <strong>@lang('lang_v1.contact_custom_field4')</strong>
    <p class="text-muted">
        {{ $contact->custom_field4 }}
    </p>
@endif