@extends('layouts.app')
@section('title', __( 'superadmin::lang.pay_online' ))

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>@lang( 'superadmin::lang.pay_online' )
    </h1>
</section>

<!-- Main content -->
<section class="content">
    {!! Form::open(['url' => action('\Modules\Superadmin\Http\Controllers\PayOnlineController@store'), 'method' =>
    'post', 'id' => 'pay_online_form' ]) !!}
    @component('components.widget', ['class' => 'box-primary', 'title' => __( 'superadmin::lang.pay_online' )])
    <div class="col-xs-4">
        <div class="form-group">
            {!! Form::label('date', __('superadmin::lang.date') . ':') !!}
            {!! Form::text('date', @format_date(date('Y-m-d')), ['class' =>
            'form-control','placeholder' => __('superadmin::lang.date'), 'readonly']); !!}
        </div>
    </div>
    <div class="col-xs-4">
        <div class="form-group">
            {!! Form::label('pay_online_no', __('superadmin::lang.pay_online_no') . ':') !!}
            {!! Form::text('pay_online_no', $pay_online_no, ['class' =>
            'form-control','placeholder' => __('superadmin::lang.pay_online_no'), 'required', 'readonly']); !!}
        </div>
    </div>
    <div class="col-xs-4">
        <div class="form-group">
            {!! Form::label('reference_no', __('superadmin::lang.reference_no') . ':') !!}
            {!! Form::text('reference_no', $reference_no, ['class' =>
            'form-control','placeholder' => __('superadmin::lang.reference_no'), 'required', 'readonly']); !!}
        </div>
    </div>
    <div class="clearfix"></div>
    <div class="col-xs-4">
        <div class="form-group">
            {!! Form::label('first_name', __('superadmin::lang.first_name') . ':') !!}
            {!! Form::text('first_name', auth()->user()->first_name, ['class' =>
            'form-control','placeholder' => __('superadmin::lang.first_name') , 'required']); !!}
        </div>
    </div>
    <div class="col-xs-4">
        <div class="form-group">
            {!! Form::label('last_name', __('superadmin::lang.last_name') . ':') !!}
            {!! Form::text('last_name', auth()->user()->last_name, ['class' =>
            'form-control','placeholder' => __('superadmin::lang.last_name') , 'required']); !!}
        </div>
    </div>
    <div class="col-xs-4">
        <div class="form-group">
            {!! Form::label('note', __('superadmin::lang.note') . ':') !!}
            {!! Form::text('note',null, ['class' =>
            'form-control','placeholder' => __('superadmin::lang.note')]); !!}
        </div>
    </div>
    <div class="clearfix"></div>
    <div class="col-xs-4">
        <div class="form-group">
            {!! Form::label('amount', __('superadmin::lang.amount') . ':') !!}
            {!! Form::number('amount',null, ['class' =>
            'form-control','placeholder' => __('superadmin::lang.amount') , 'required', 'min' => 0]); !!}
        </div>
    </div>

    <div class="col-xs-4">
        <div class="form-group">
            {!! Form::label('currency', __('superadmin::lang.currency') . ':') !!}
            {!! Form::select('currency', $currencies, null, ['class' => 'form-control select2','placeholder' =>
            __('superadmin::lang.please_select'), 'required']);
            !!}
        </div>
    </div>
    <input type="hidden" name="order_id" id="order_id" value="{{$order_id}}">
    @endcomponent

    <div class="row">
        <div class="col-md-12">
            <script type="text/javascript" src="https://www.payhere.lk/lib/payhere.js"></script>
            <button class="btn btn-success pull-right" type="button" id="payhere-payment"
                style="margin-left: 5px;">@lang('superadmin::lang.pay_online_now')</button> &nbsp;
            <button class="btn btn-success pull-right" type="submit"
                id="offline-payment">@lang('superadmin::lang.pay_offline_now')</button>

        </div>
    </div>
    {!! Form::close() !!}
</section>
<!-- /.content -->

@endsection
@php
$business_id = request()->session()->get('business.id');
$location_data = App\BusinessLocation::where('business_id', $business_id)->first();
$mobile = !empty($location_data->mobile)?$location_data->mobile: '';
$country = !empty($location_data->country)?$location_data->country: '';
$city = !empty($location_data->city)?$location_data->city: '';
$name = !empty($location_data->name)?$location_data->name: '';
$zip_code = !empty($location_data->zip_code)?$location_data->zip_code: '';

@endphp
@section('javascript')
<script>
    // Called when user completed the payment. It can be a successful payment or failure
payhere.onCompleted = function onCompleted(orderId) {
   
    window.location.replace("{{action('\Modules\Superadmin\Http\Controllers\PayOnlineController@create')}}");
        
  
    //Note: validate the payment and show success or failure page to the customer
};

// Called when user closes the payment without completing
payhere.onDismissed = function onDismissed() {
    //Note: Prompt user to pay again or show an error page
    console.log("Payment dismissed");
};

// Called when error happens when initializing payment such as invalid parameters
payhere.onError = function onError(error) {
    // Note: show an error page
    console.log(error);
    swal({
        title: 'Error',
        text: 'Something went wrong',
        icon: 'error',
        buttons: true,
        dangerMode: true,
    }).then(confirm => {
        window.location.reload();
    });
};

// Show the payhere.js popup, when "PayHere Pay" is clicked
document.getElementById('payhere-payment').onclick = function (e) {
        
    $('form#pay_online_form').validate();

    if ($('form#pay_online_form').valid()) {
        // Put the payment variables here
        var payment = {
            "sandbox":@if(empty(env('PAYHERE_LIVE'))) true @else false @endif,
            "merchant_id": "{{env('PAYHERE_MERCHANT_ID')}}",       // Replace your Merchant ID
            "return_url": "",
            "cancel_url": "",
            "notify_url": "{{action('\Modules\Superadmin\Http\Controllers\PayOnlineController@notifyPayhere')}}",
            "order_id": "{{$order_id}}",
            "items": "Security Deposit",
            "amount": parseFloat($('#amount').val()),
            "currency": $('#currency').val(),
            "first_name": $('#first_name').val(),
            "last_name": $('#last_name').val(),
            "email": "{{request()->session()->get('user.email')}}",
            "phone": "{{$mobile}}",
            "address": "{{$name}}, {{$city}}, {{$country}}, {{$zip_code}}",
            "city": "{{$city}}",
            "country": "{{$country}}",
        };  

        
        $.ajax({
            method: 'post',
            url: "{{action('\Modules\Superadmin\Http\Controllers\PayOnlineController@initiatePayhere')}}",
            data: { gateway: 'payhere', pay_online_no: $('#pay_online_no').val(), order_id: "{{$order_id}}", amount: parseFloat($('#amount').val()), first_name: $('#first_name').val(), last_name: $('#last_name').val(), note: $('#note').val(), reference_no: $('#reference_no').val(), type: 'security_deposit', currency: $('#currency').val()  },
            success: function(result) {
            if(result.success == 1){
                payhere.startPayment(payment);
            }
            },
        });
    }

};

$('button#offline-payment').click(function (e) {
    e.preventDefault();
    $('form#pay_online_form').validate();

    if ($('form#pay_online_form').valid()) {
        $('form#pay_online_form').submit();
    }
})
</script>
@endsection