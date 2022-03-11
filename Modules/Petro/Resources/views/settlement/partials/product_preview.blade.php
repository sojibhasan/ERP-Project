<div class="modal-dialog" role="document" style="width: 65%;">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                    aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">{{$settlement[0]->settlement_no}}</h4>
        </div>

        <div class="modal-body">
           
            <div class="row">
                <div class="col-xs-12 text-center" style="font-weight: bold; maring-bottom: -10px; font-size: 18px;">
                    @lang('petro::lang.credit_sale_product_detail')
                </div>
                <div class="">
                    <div class="col-md-12">
                        <table class="table table-bordered table-striped">
                            <tbody>
                                <tr>
                                    <th>@lang('petro::lang.product_name')</th>
                                    <th>@lang('petro::lang.qty')</th>
                                    <th>@lang('petro::lang.amount')</th>
                                </tr>
                                @foreach ($settlement as $credit_sale_product)
                                <tr>
                                    
                                    <td>
                                        {{$credit_sale_product->name}}
                                    </td>
                                    <td>
                                        {{$credit_sale_product->qty}}
                                    </td>
                                    <td>
                                        {{$credit_sale_product->amount}}
                                    </td>
                                </tr>
                                @endforeach
                                

                                <tr>
                                    <th class="text-right">
                                        @lang('petro::lang.total')
                                    </th>
                                    <td>
                                        {{number_format($settlement->sum('qty'), 0)}} 
                                    </td>
                                    <td>
                                        {{number_format($settlement->sum('amount'), 2)}} 
                                    </td>
                                </tr>

                            </tbody>

                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="clearfix"></div>
        <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
        </div>

    </div>
</div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->