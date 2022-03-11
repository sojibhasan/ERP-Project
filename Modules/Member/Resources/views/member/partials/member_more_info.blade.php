<strong><i class="fa fa-mobile margin-r-5"></i> @lang('member.mobile')</strong>
<p class="text-muted">
    {{ $member->mobile_number_1 }}
</p>
@if($member->land_number)
    <strong><i class="fa fa-phone margin-r-5"></i> @lang('contact.landline')</strong>
    <p class="text-muted">
        {{ $member->land_number }}
    </p>
@endif
@if($member->mobile_number_2)
    <strong><i class="fa fa-phone margin-r-5"></i> @lang('member.alternate_number')</strong>
    <p class="text-muted">
        {{ $member->mobile_number_2 }}
    </p>
@endif
