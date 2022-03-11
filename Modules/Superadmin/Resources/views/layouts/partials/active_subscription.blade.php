@if(!empty($__subscription) && env('APP_ENV') != 'demo')
<i class="fa fa-info-circle pull-left mt-10 cursor-pointer" style="margin-top: 17px; padding-left:20px; color:white"
    aria-hidden="true" data-toggle="popover" data-html="true"
    title="@lang('superadmin::lang.active_package_description')" data-placement="right" data-trigger="hover"
    data-content="
    <table class='table table-condensed'>
     <tr class='text-center'> 
        <td colspan='2'>
            {{$__subscription->package_details['name'] }}
        </td>
     </tr>
     <tr class='text-center'>
        <td colspan='2'>
            {{ @format_date($__subscription->start_date) }} - {{@format_date($__subscription->end_date) }}
        </td>
     </tr>
     @if(!request()->session()->get('business.is_patient'))
     <tr> 
        <td colspan='2'>
            <i class='fa fa-check text-success'></i>
            @if($__subscription->package_details['location_count'] == 0)
                @lang('superadmin::lang.unlimited')
            @else
                {{$__subscription->package_details['location_count']}}
            @endif

            @lang('business.business_locations')
        </td>
     </tr>
     @endif
     <tr>
        <td colspan='2'>
            <i class='fa fa-check text-success'></i>
            @if($__subscription->package_details['user_count'] == 0)
                @lang('superadmin::lang.unlimited')
            @else
                {{$__subscription->package_details['user_count']}}
            @endif

            @if(request()->session()->get('business.is_patient'))
                @lang('superadmin::lang.members')
            @else
                @lang('superadmin::lang.users')
            @endif
        </td>
     <tr>
    @if(!request()->session()->get('business.is_patient'))
     <tr>
        <td colspan='2'>
            <i class='fa fa-check text-success'></i>
            @if($__subscription->package_details['product_count'] == 0)
                @lang('superadmin::lang.unlimited')
            @else
                {{$__subscription->package_details['product_count']}}
            @endif

            @lang('superadmin::lang.products')
        </td>
     </tr>
     @endif
     @if(!request()->session()->get('business.is_patient'))
     <tr>
        <td colspan='2'>
            <i class='fa fa-check text-success'></i>
            @if($__subscription->package_details['invoice_count'] == 0)
                @lang('superadmin::lang.unlimited')
            @else
                {{$__subscription->package_details['invoice_count']}}
            @endif

            @lang('superadmin::lang.invoices')
        </td>
     </tr>
     @endif
    @if(!empty($__subscription->package_details['access_account']))
    <tr>
        <td colspan='2'>
            <i class='fa fa-check text-success'></i> @lang('superadmin::lang.accounting_module')
        </td>
    </tr>
    @endif
    @if(!empty($__subscription->package_details['pump_operator_dashboard']))
    <tr>
        <td colspan='2'>
            <i class='fa fa-check text-success'></i> @lang('superadmin::lang.pump_operator_dashboard')
        </td>
    <tr />
    @endif
    @if(!empty($__subscription->package_details['property_module']))
    <tr>
        <td colspan='2'>
            <i class='fa fa-check text-success'></i> @lang('superadmin::lang.property_module')
        </td>
    <tr />
    @endif
     
    </table>                     
">
</i>
@endif