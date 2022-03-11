<div class="modal-dialog" role="document" style="width: 50%;">
    <div class="modal-content">

        {!! Form::open(['url' => action('StockTransferRequestController@postReceivedTransfer', $rquest_transfer->id), 'method' => 'post', 'id'
        =>
        'add_transfer_request_form' ]) !!}
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                    aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">@lang( 'lang_v1.received' )</h4>
        </div>

        <div class="modal-body">
            <div class="row">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped" id="stock_transfer_table">
                        <thead>
                            <tr>
                                <th>@lang('lang_v1.product')</th>
                                <th>@lang('lang_v1.good_condition')</th>
                                <th>@lang('lang_v1.damage')</th>
                                <th>@lang('lang_v1.short')</th>
                                <th>@lang('lang_v1.expire')</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>{{$rquest_transfer->products->name}}</td>
                                <td>{!! Form::number('good_condition', 0, ['class' => 'form-control']) !!}</td>
                                <td>{!! Form::number('damage', 0, ['class' => 'form-control']) !!}</td>
                                <td>{!! Form::number('short', 0, ['class' => 'form-control']) !!}</td>
                                <td>{!! Form::number('expire', 0, ['class' => 'form-control']) !!}</td>
                            </tr>
                        </tbody>
                    </table>

                </div>
            </div>
        </div>

        <div class="modal-footer">
            <button type="submit" class="btn btn-primary">@lang( 'messages.save' )</button>
            <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
        </div>

        {!! Form::close() !!}

    </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->
<script>

</script>