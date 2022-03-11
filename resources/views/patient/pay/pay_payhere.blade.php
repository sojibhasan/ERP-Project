<div class="col-md-12">
    <script type="text/javascript" src="https://www.payhere.lk/lib/payhere.js"></script>
    <button class="btn btn-success" type="submit" id="payhere-payment">PayHere Pay</button>
    @php
    $business_id = request()->session()->get('business.id');
    $location_data = App\BusinessLocation::where('business_id', $business_id)->first();
    $mobile = !empty($location_data->mobile)?$location_data->mobile: '';
    $country = !empty($location_data->country)?$location_data->country: '';
    $city = !empty($location_data->city)?$location_data->city: '';
    $name = !empty($location_data->name)?$location_data->name: '';
    $zip_code = !empty($location_data->zip_code)?$location_data->zip_code: '';

    @endphp
    <script>
        // Called when user completed the payment. It can be a successful payment or failure
    payhere.onCompleted = function onCompleted(orderId) {
        $.ajax({
            method: 'get',
            url: "",
            data: {  },
            success: function(result) {
                console.log(result);
                if(result.status === 1){
                    swal({
                        title: 'Success',
                        text: 'Payment Successful. Package is activated',
                        icon: 'success',
                        buttons: true,
                        dangerMode: false,
                    }).then(confirm => {
                        window.location.replace("{{url('home')}}");
                    });
                }
            },
        });
      
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

    // Put the payment variables here
    var payment = {
        "sandbox":@if(empty(env('PAYHERE_LIVE'))) true @else false @endif,
        "merchant_id": "{{env('PAYHERE_MERCHANT_ID')}}",       // Replace your Merchant ID
        "return_url": "{{action('\Modules\Superadmin\Http\Controllers\FamilySubscriptionController@confirm') . '?gateway=payhere'}}",
        "cancel_url": "{{action('\Modules\Superadmin\Http\Controllers\FamilySubscriptionController@pay')}}",
        "notify_url": "{{action('\Modules\Superadmin\Http\Controllers\FamilySubscriptionController@notifyPayhere')}}",
        "order_id": "{{$order_id}}",
        "items": "@lang('patient.no_of_family_members'): {{$no_of_family_members}}",
        "amount": "{{$amount_to_pay}}",
        "currency": "{{$currency_code}}",
        "first_name": "{{request()->session()->get('user.first_name')}}",
        "last_name": "{{request()->session()->get('user.last_name')}}",
        "email": "{{request()->session()->get('user.email')}}",
        "phone": "{{$mobile}}",
        "address": "{{$name}}, {{$city}}, {{$country}}, {{$zip_code}}",
        "city": "{{$city}}",
        "country": "{{$country}}",
    };

    // Show the payhere.js popup, when "PayHere Pay" is clicked
    document.getElementById('payhere-payment').onclick = function (e) {
        $.ajax({
            method: 'post',
            url: "{{action('\Modules\Superadmin\Http\Controllers\FamilySubscriptionController@confirm')}}",
            data: { gateway: 'payhere', order_id: "{{$order_id}}" },
            success: function(result) {
               console.log(result);
            },
        });
      
        payhere.startPayment(payment);
    };
    </script>
</div>