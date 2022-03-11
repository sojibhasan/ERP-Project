<strong>{{ $contact->name }}</strong><br>
<strong><i class="fa fa-map-marker margin-r-5"></i> @lang('business.address')</strong>
<p class="text-muted">
    @if($contact->landmark)
        {{ $contact->landmark }}
    @endif

    {{ ', ' . $contact->city }}

    @if($contact->state)
        {{ ', ' . $contact->state }}
    @endif
    <br>
    @if($contact->country)
        {{ $contact->country }}
    @endif
</p>
@if($contact->supplier_business_name)
    <strong><i class="fa fa-briefcase margin-r-5"></i> 
    @lang('business.business_name')</strong>
    <p class="text-muted">
        {{ $contact->supplier_business_name }}
    </p>
@endif