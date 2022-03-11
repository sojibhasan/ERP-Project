<strong>{{ $member->name }}</strong><br>
<strong><i class="fa fa-map-marker margin-r-5"></i> @lang('business.address')</strong>
<p class="text-muted">
    @if($member->address)
        {{ $member->address }}
    @endif

    {{ ', ' . $member->town }}

    @if($member->district)
        {{ ', ' . $member->district }}
    @endif
    <br>
   
</p>
