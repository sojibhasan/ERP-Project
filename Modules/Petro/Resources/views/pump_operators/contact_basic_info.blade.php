<strong>{{ $pump_operator->name }}</strong><br>
<strong><i class="fa fa-map-marker margin-r-5"></i> @lang('business.address')</strong>
<p class="text-muted">
    @if($pump_operator->address)
        {{ $pump_operator->address }}
    @endif
</p>
<strong><i class="fa fa-phone margin-r-5"></i> @lang('petro::lang.mobile')</strong>
<p class="text-muted">
    @if($pump_operator->mobile)
        {{ $pump_operator->mobile }}
    @endif
</p>
<strong><i class="fa fa-arrow-circle-up margin-r-5"></i> @lang('petro::lang.total_sale')</strong>
<p class="text-muted">
    @if($pump_operator->total_sale)
        {{ $pump_operator->total_sale }}
    @endif
</p>
<strong><i class="fa fa-money margin-r-5"></i> @lang('petro::lang.commission_ap')</strong>
<p class="text-muted">
    @if($pump_operator->commission_ap)
        {{ $pump_operator->commission_ap }}
    @endif
</p>
<strong><i class="fa fa-arrow-up margin-r-5"></i> @lang('petro::lang.excess_amount')</strong>
<p class="text-muted">
    @if($pump_operator->excess_amount)
        {{ $pump_operator->excess_amount }}
    @endif
</p>
<strong><i class="fa fa-arrow-down margin-r-5"></i> @lang('petro::lang.short_amount')</strong>
<p class="text-muted">
    @if($pump_operator->short_amount)
        {{ $pump_operator->short_amount }}
    @endif
</p>
