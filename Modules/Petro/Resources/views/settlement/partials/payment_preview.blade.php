<div class="modal-dialog" role="document" style="width: 65%;">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                    aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">{{$settlement->settlement_no}}</h4>
        </div>

        <div class="modal-body">
            @php
            $business_id = session()->get('user.business_id');
            $business_details = App\Business::find($business_id);
            $currency_precision = !empty($business_details->currency_precision) ? $business_details->currency_precision
            : 2;
            @endphp
            <div class="row">
                <div class="col-xs-12 text-center" style="font-weight: bold; maring-bottom: -10px; font-size: 18px;">
                    @lang('petro::lang.payment_details')
                </div>
                <div class="">
                    <div class="col-md-12">
                        <table class="table table-bordered table-striped">
                            <tbody>
                                <tr>
                                    <th colspan="6" class="text-red">@lang('petro::lang.cash' )</th>
                                </tr>
                                <tr>
                                    <th colspan="5">@lang('petro::lang.customer')</th>
                                    <th>@lang('petro::lang.amount')</th>
                                </tr>
                                @foreach ($settlement->cash_payments as $cash)
                                <tr>
                                    <td colspan="5">
                                        @php
                                        $cash_customer = \App\Contact::findOrFail($cash->customer_id);
                                        @endphp
                                        {{!empty($cash_customer) ? $cash_customer->name : ''}}
                                    </td>
                                    <td>{{number_format($cash->amount, $currency_precision)}}</td>
                                </tr>
                                @endforeach
                                <tr>
                                    <th colspan="5" class="text-right">
                                        @lang('petro::lang.total')
                                    </th>
                                    <td>{{number_format($settlement->cash_payments->sum('amount'), $currency_precision)}}
                                    </td>
                                </tr>
                                <tr>
                                    <th colspan="6" class="text-red">@lang('petro::lang.cards' )</th>
                                </tr>
                                <tr>
                                    <th colspan="3">@lang('petro::lang.customer')</th>
                                    <th colspan="2">@lang('petro::lang.card_number')</th>
                                    <th>@lang('petro::lang.amount')</th>
                                </tr>
                                @foreach ($settlement->card_payments as $card) 
                                <tr>
                                    <td colspan="3">
                                        @php
                                        $card_customer = \App\Contact::findOrFail($card->customer_id);
                                        @endphp
                                        {{!empty($card_customer) ? $card_customer->name : ''}}
                                    </td>
                                    <td colspan="2">
                                        {{$card->card_number}}
                                    </td>
                                    <td>{{number_format($card->amount, $currency_precision)}}</td>
                                </tr>
                                @endforeach
                                <tr>
                                    <th colspan="5" class="text-right">
                                        @lang('petro::lang.total')
                                    </th>
                                    <td>{{number_format($settlement->card_payments->sum('amount'), $currency_precision)}}
                                    </td>
                                </tr>
                                <tr>
                                    <th colspan="6" class="text-red">@lang('petro::lang.cheques' )</th>
                                </tr>
                                <tr>
                                    <th colspan="2">@lang('petro::lang.customer')</th>
                                    <th>@lang('petro::lang.bank_name')</th>
                                    <th>@lang('petro::lang.cheque_number')</th>
                                    <th>@lang('petro::lang.cheque_date')</th>
                                    <th>@lang('petro::lang.amount')</th>
                                </tr>
                                @foreach ($settlement->cheque_payments as $cheque)
                                <tr>
                                    <td colspan="2">
                                        @php
                                        $cheque_customer = \App\Contact::findOrFail($cheque->customer_id);
                                        @endphp
                                        {{!empty($cheque_customer) ? $cheque_customer->name : ''}}
                                    </td>
                                    <td>
                                        {{$cheque->bank_name}}
                                    </td>
                                    <td>
                                        {{$cheque->cheque_number}}
                                    </td>
                                    <td>
                                        {{$cheque->cheque_date}}
                                    </td>
                                    <td>{{number_format($cheque->amount, $currency_precision)}}</td>
                                </tr>
                                @endforeach
                                <tr>
                                    <th colspan="5" class="text-right">
                                        @lang('petro::lang.total')
                                    </th>
                                    <td>{{number_format($settlement->cheque_payments->sum('amount'), $currency_precision)}}
                                    </td>
                                </tr>
                                <tr>
                                    <th colspan="6" class="text-red">@lang('petro::lang.credit_sales' )</th>
                                </tr>
                                <tr>
                                    <th>@lang('petro::lang.customer')</th>
                                    <th>@lang('petro::lang.order_number')</th>
                                    <th>@lang('petro::lang.order_date')</th>
                                    <th>@lang('petro::lang.product')</th>
                                    <th>@lang('petro::lang.qty')</th>
                                    <th>@lang('petro::lang.amount')</th>
                                </tr>
                                @foreach ($settlement->credit_sale_payments as $credit_sale)
                                <tr>
                                    <td>
                                        @php
                                        $credit_sale_customer = \App\Contact::findOrFail($credit_sale->customer_id);
                                        $credit_sale_product = \App\Product::findOrFail($credit_sale->product_id);
                                        @endphp
                                        {{!empty($credit_sale_customer) ? $credit_sale_customer->name : ''}}
                                    </td>
                                    <td>
                                        {{$credit_sale->order_number}}
                                    </td>
                                    <td>
                                        {{$credit_sale->order_date}}
                                    </td>
                                    <td>
                                        {{!empty($credit_sale_product) ? $credit_sale_product->name : ''}}
                                    </td>
                                    <td>
                                        {{$credit_sale->qty}}
                                    </td>
                                    <td>{{number_format($credit_sale->amount, $currency_precision)}}</td>
                                </tr>
                                @endforeach
                                <tr>
                                    <th colspan="2">
                                        <button data-href="{{action('\Modules\Petro\Http\Controllers\AddPaymentController@productPreview', [$settlement->id])}}" type="button" class="btn-modal btn btn-primary pull-left credit_sale_product_detail" data-container=".preview_settlement" id="product_preview_btn">Credit Sales Product details</button>
                                    </th>
                                    <th colspan="3" class="text-right">
                                        @lang('petro::lang.total')
                                    </th>
                                    <td>{{number_format($settlement->credit_sale_payments()->sum('amount'), $currency_precision)}}
                                    </td>
                                </tr>
                                <tr>
                                    <th colspan="6" class="text-red">@lang('petro::lang.expense' )</th>
                                </tr>
                                <tr>
                                    <th>@lang('petro::lang.expense_number' )</th>
                                    <th colspan="2">@lang('petro::lang.reference_no' )</th>
                                    <th colspan="2">@lang('petro::lang.reason')</th>
                                    <th>@lang('petro::lang.amount')</th>
                                </tr>
                                @foreach ($settlement->expense_payments as $expense)
                                <tr>
                                    <td>
                                        {{$expense->expense_number}}
                                    </td>
                                    <td colspan="2">
                                        {{$expense->reference_no}}
                                    </td>
                                    <td colspan="2">
                                        {{$expense->reference_no}}
                                    </td>
                                    <td>{{number_format($expense->amount, $currency_precision)}}</td>
                                </tr>
                                @endforeach
                                <tr>
                                    <th colspan="5" class="text-right">
                                        @lang('petro::lang.total')
                                    </th>
                                    <td>{{number_format($settlement->expense_payments->sum('amount'), $currency_precision)}}
                                    </td>
                                </tr>
                                <tr>
                                    <th colspan="6" class="text-red">@lang('petro::lang.shortage' )</th>
                                </tr>
                                <tr>
                                    <th colspan="5"></th>
                                    <th>@lang('petro::lang.amount' )</th>
                                </tr>
                                @foreach ($settlement->shortage_payments as $shortage)
                                <tr>
                                    <td colspan="5"></td>
                                    <td>{{number_format($shortage->amount, $currency_precision)}}</td>
                                </tr>
                                @endforeach

                                <tr>
                                    <th colspan="6" class="text-red">@lang('petro::lang.excess' )</th>
                                </tr>
                                <tr>
                                    <th colspan="5"></th>
                                    <th>@lang('petro::lang.amount' )</th>
                                </tr>
                                @foreach ($settlement->excess_payments as $excess)
                                <tr>
                                    <td colspan="5"></td>
                                    <td>{{number_format($excess->amount, $currency_precision)}}</td>
                                </tr>
                                @endforeach
                                
                                <tr>
                                    <th colspan="5" class="text-right">
                                        @lang('petro::lang.total')
                                    </th>
                                    <td>{{number_format($settlement->cash_payments->sum('amount') + $settlement->card_payments->sum('amount') + $settlement->cheque_payments->sum('amount') + $settlement->credit_sale_payments->sum('amount') + $settlement->expense_payments->sum('amount') + $settlement->shortage_payments->sum('amount') + $settlement->excess_payments->sum('amount'), $currency_precision)}}
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