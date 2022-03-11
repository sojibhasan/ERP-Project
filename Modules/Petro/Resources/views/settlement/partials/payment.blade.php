@php
    $add_payment_settlement_no = !empty($active_settlement) ? $active_settlement->settlement_no : $settlement_no;
@endphp
<div class="row">
    <div class="col-md-12" style="margin-top: 20px;">
        <div class="col-md-4"></div>
        <div class="col-md-4">
            <div class="row">
                <div class="col-md-8" style="font-weight: bold; text-align: left;">
                    @lang('petro::lang.meter_sale_total') :
                </div>
                <div class="col-md-4" style="font-weight: bold; text-align: right;">
                    <span class="payment_meter_sale_total">{{number_format( $payment_meter_sale_total, $currency_precision )}}</span>
                </div>
            </div>
            <br>
            <div class="row">
                <div class="col-md-8" style="font-weight: bold; text-align: left;">
                    @lang('petro::lang.other_sale_total') :
                </div>
                <div class="col-md-4" style="font-weight: bold; text-align: right;">
                    <span class="payment_other_sale_total">{{number_format( $payment_other_sale_total, $currency_precision )}}</span>
                </div>
            </div>
            <br>
            <div class="row">
                <div class="col-md-8" style="font-weight: bold; text-align: left;">
                    @lang('petro::lang.other_income_total') :
                </div>
                <div class="col-md-4" style="font-weight: bold; text-align: right;">
                    <span class="payment_other_income_total">{{number_format( $payment_other_income_total, $currency_precision )}}</span>
                </div>
            </div>
            <br>
            <div class="row">
                <div class="col-md-8" style="font-weight: bold; text-align: left;">
                    @lang('petro::lang.customer_payment_total') :
                </div>
                <div class="col-md-4" style="font-weight: bold; text-align: right;">
                    <span class="payment_customer_payment_total">{{number_format( $payment_customer_payment_total, $currency_precision )}}</span>
                </div>
            </div>
            <br>
        </div>
        <div class="col-md-4"></div>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="pull-right" style="padding-right: 10px; font-size : 17px; color: brown;"><strong>@lang('purchase.payment_due'):</strong> <span
                id="payment_due">{{number_format( $payment_meter_sale_total+$payment_other_sale_total+$payment_other_income_total+$payment_customer_payment_total, $currency_precision )}}</span></div>
    </div>
    <br>
    <br>
    <div class="col-md-12">
    <button type="button" id="add_payment" data-container="add_payment" data-href="{{action('\Modules\Petro\Http\Controllers\AddPaymentController@create', [ 'settlement_no' => $add_payment_settlement_no])}}" class="btn btn-primary btn-modal pull-right">@lang('petro::lang.payment')</button>
    </div>
</div>