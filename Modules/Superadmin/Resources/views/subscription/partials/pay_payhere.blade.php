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

    if(!empty($custom_price)){
    $price = $custom_price;
    }else{
    $price = $package->price;
    }
    @endphp
    <script>
        // Called when user completed the payment. It can be a successful payment or failure
    payhere.onCompleted = function onCompleted(orderId) {
        console.log(orderId);
        $.ajax({
            method: 'get',
            url: "{{action('\Modules\Superadmin\Http\Controllers\SubscriptionController@checkStatus', ['package_id' => $package->id, 'gateway' => 'payhere'])}}",
            data: {  },
            dataType: 'json',
            success: function(result) {
                console.log(result);
                if(result.success === 1){
                    $('#subscription_success_modal').find('.title').text(result.title);
                    $('#subscription_success_modal').find('.body_msg').html(result.msg);
                    $('#subscription_success_modal').modal('show');
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
        "return_url": "{{action('\Modules\Superadmin\Http\Controllers\SubscriptionController@confirm', [$package->id]) . '?gateway=payhere'}}",
        "cancel_url": "{{action('\Modules\Superadmin\Http\Controllers\SubscriptionController@pay', [$package->id])}}",
        "notify_url": "{{route('subscription-payhere-confirm')}}",
        "order_id": "{{$order_id}}",
        "items": "{{$package->name}}",
        "amount": "{{$price}}",
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
            method: 'POST',
            url: "{{route('subscription-payhere-initaildata')}}",
            data: { business_id : {{$business_id}}, order_id: '{{$order_id}}', package_id: {{$package->id}}, transaction_id: {{$package->id}}, user_id: {{auth()->user()->id}}, price: {{$price}} },
            success: function(result) {
                
            },
        });
        $.ajax({
            method: 'get',
            url: "{{action('\Modules\Superadmin\Http\Controllers\SubscriptionController@confirm', [$package->id])}}?gateway=payhere&custom_price={{$price}}",
            data: {  },
            success: function(result) {
               
            },
        });
      
        payhere.startPayment(payment);
    };
    </script>
</div>

<div class="modal" tabindex="-1" role="dialog" id="subscription_success_modal">
    <div class="modal-dialog" role="document" style="width: 40% !important;">
        <div class="modal-content">
            <div class="modal-body text-center">
                <i class="fa fa-check fa-lg"
                    style="font-size: 50px; margin-top: 20px; border: 1px solid #4BB543; color: #4BB543; padding:15px 10px 15px 10px; border-radius: 50%;"></i>
                <h2 class="title"></h2>
                <div class="clearfix"></div>
                <div class="col-md-12 body_msg" style="color: black;">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>