@if(!empty($notifications_data))
@foreach($notifications_data as $notification_data)
<li class="@if(empty($notification_data['read_at'])) bg-aqua-lite @endif">
  <a href="{{$notification_data['link'] ?? '#'}}">
    <i class="{{$notification_data['icon_class'] ?? ''}}"></i> {!! $notification_data['msg'] ?? '' !!} <br>
    <small>{{$notification_data['created_at']}}</small>
  </a>
</li>
@endforeach
@else
@endif

@php
$stock_trasfer_notifications = App\StockTransferRequest::where('business_id',
request()->session()->get('business.id'))->where('created_by', auth()->user()->id)->where('notification', 'ok')->get();
@endphp
@if(!empty($stock_trasfer_notifications))
@foreach($stock_trasfer_notifications as $stock_trasfer_notification_data)
@php
    $product = App\Product::findOrFail($stock_trasfer_notification_data->product_id)
@endphp
<li class="">
  <a class="stock-transfer-request-link"
    href="{{action('StockTransferRequestController@getNotificationPopup', $stock_trasfer_notification_data->id)}}">
    <i class=""></i> Request for <br> Product: {{$product->name}} <br>
    <i>Status: {{ucfirst($stock_trasfer_notification_data->status)}} </i>
    <small>{{$stock_trasfer_notification_data->updated_at}}</small>
  </a>
</li>
@endforeach
@endif