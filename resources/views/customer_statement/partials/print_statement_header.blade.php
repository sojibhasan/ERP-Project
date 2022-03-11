
<style>
    .bg_color {
        background: #357ca5;
        font-size: 20px;
        color: #fff;
    }

    .text-center {
        text-align: center;
    }

    #customer_detail_table th {
        background: #357ca5;
        color: #fff;
    }

    #customer_detail_table>tbody>tr:nth-child(2n+1)>td,
    #customer_detail_table>tbody>tr:nth-child(2n+1)>th {
        background-color: rgba(89, 129, 255, 0.3);
    }
    table#customer_detail_table > tbody > tr > td {
        padding: 0px !important;
    }
    @media print {
    }
</style>

@php
$currency_precision = !empty($business_details->currency_precision) ? $business_details->currency_precision : 2;
@endphp
<div class="col-md-12 col-sm-12 @if(!empty($for_pdf)) width-100 text-center @endif">
    <h3><strong>{{$contact->business->name}}</strong></h3><span class="text-center">{{$location_details->city}}, {{$location_details->state}}<br>@lang('lang_v1.tel'):  {!!
        $location_details->mobile !!}</span>
</div>
<div class="col-md-12 statement_no_print text-left" >
    <h4> @lang('lang_v1.invoive_no'): <span class="statement_no">{{$statement_no}}</span></h4>
</div>
<div class="row">
<div class="col-md-6 col-sm-6 col-xs-6 @if(!empty($for_pdf)) width-50 f-left @endif">
    <p class="bg_color" style="width: 40%; ">@lang('lang_v1.to'):</p>
    <p><strong>{{$contact->name}}</strong><br> {!! $contact->contact_address !!} 
        <strong> 
        @if(!empty($contact->email))
        <br>@lang('business.email'): {{$contact->email}} 
        @endif
        <br>@lang('contact.mobile'): {{$contact->mobile}}
        @if(!empty($contact->tax_number)) 
        <br>@lang('contact.tax_no'): {{$contact->tax_number}} 
        @endif
        </strong>
    </p>
</div>
<div class="col-md-6 col-sm-6 col-xs-6 text-right align-right @if(!empty($for_pdf)) width-50 f-left @endif">
    <p class=" bg_color" style=" font-weight: 500;">
        @lang('lang_v1.account_summary')</p>
    <table class="table text-left align-left no-border @if(!empty($for_pdf)) table-pdf @endif" id="customer_detail_table">
        <tbody>
            <tr>
                <td>@lang('lang_v1.beginning_balance')</td>
                <td>{{@num_format($opening_balance)}}
                </td>
            </tr>
            @if( $contact->type == 'supplier' || $contact->type == 'both')
            <tr>
                <td>@lang('report.total_purchase')</td>
                <td>{{@num_format($ledger_details['total_purchase'])}}
                </td>
            </tr>
            @endif
            @if( $contact->type == 'customer' || $contact->type == 'both')
            <tr>
                <td>@lang('lang_v1.total_sales')</td>
                <td>{{@num_format($ledger_details['total_invoice'])}}
                </td>
            </tr>
            @endif
            <tr>
                <td>@lang('sale.total_paid')</td>
                <td>{{@num_format($ledger_details['total_paid'])}}
                </td>
            </tr>
            <tr>
                <td><strong>@lang('lang_v1.balance_due')</strong></td>
                @php
                    $beginning_balance  = $opening_balance;
                    $total_invoice      = $ledger_details['total_invoice'];
                    $total_paid         = $ledger_details['total_paid'];
                    $balance_due        = $beginning_balance +  $total_invoice - $total_paid;
                @endphp
                <td>{{@num_format($balance_due)}}</td>
            </tr>
        </tbody>
    </table>
</div>
</div>