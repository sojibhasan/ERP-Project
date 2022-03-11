<strong>{{ $crm->name }}</strong><br>
<strong><i class="fa fa-map-marker margin-r-5"></i> @lang('business.address')</strong>
<p class="text-muted">
    @if($crm->landmark)
        {{ $crm->landmark }}
    @endif

    {{ ', ' . $crm->city }}

    @if($crm->state)
        {{ ', ' . $crm->district }}
    @endif
    <br>
    @if($crm->country)
        {{ $crm->country }}
    @endif
</p>
@if($crm->business_name)
    <strong><i class="fa fa-briefcase margin-r-5"></i> 
    @lang('business.business_name')</strong>
    <p class="text-muted">
        {{ $crm->business_name }}
    </p>
@endif