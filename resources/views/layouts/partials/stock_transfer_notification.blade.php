<div class="modal" tabindex="-1" role="dialog" id="stock_transfer_notification_modal_{{$transfer_request->id}}" style="margin-top: 10%; border-radius: 10px;">
    <div class="modal-dialog" role="document">
        <div class="modal-content" style="border-radius: 15px;">
            <div class="modal-body text-center">
                <i class="fa fa-exchange fa-lg" style="font-size: 50px; margin-top: 20px; border: 1px solid #333; padding:15px 10px 15px 10px; border-radius: 50%;"></i>
                <h3>{{$product->name}}</h3> <br>
                <label for="">@lang('lang_v1.status')</label> : {{ ucfirst($transfer_request->status) }}
            </div>
            <div class="modal-footer">
                <div class="col-md-12">
                    <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'lang_v1.ok' )</button>
                            <a href="{{action('StockTransferRequestController@stopNotification', $transfer_request->id)}}" class="btn btn-danger pull-right" style="margin-right: 10px;" >@lang('lang_v1.stop')</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    $('#stock_transfer_notification_modal_{{$transfer_request->id}}').modal('show');
</script>