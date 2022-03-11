<div class="row">
    <div class="col-md-12">
        @component('components.widget', ['class' => 'box-primary'])
        <div class="col-md-12">
            <div class="row">
                <div class="col-md-4 text-red" style="margin-top: 14px;">
                    <b>@lang('petro::lang.date_range'): <span class="9c_from_date">{{$start_date}}</span> @lang('petro::lang.to') <span class="9c_to_date">{{$end_date}}</span> </b>
                </div>
                <div class="col-md-4">
                    <div class="text-center">
                        <h5 style="font-weight: bold;">{{request()->session()->get('business.name')}}  <br>
                            <span class="9c_location_name">@if(!empty($location)) {{$location->name}} @else All @endif</span></h5>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="text-right">
                        <h5 style="font-weight: bold;" class="text-red">@lang('mpcs::lang.9c_form') @lang('mpcs::lang.form_no') : {{$F9C_sn}}</h5>
                    </div>
                </div>
            </div>
            <div class="row" style="margin-top: 20px;">
                <div class="col-md-12">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped" id="form_9c_table" style="width: 100%;">
                            <thead>
                                <tr>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    @foreach ($sub_categories as $item)
                                    <th colspan="2" class="text-center">{{$item->name}}</th>
                                    @endforeach
                                    <th></th>
                                </tr>
                                <tr>
                                    <th>@lang('mpcs::lang.bill_no')</th>
                                    <th>@lang('mpcs::lang.order_voucher_no')</th>
                                    <th>@lang('mpcs::lang.customer')</th>
                                    <th>@lang('mpcs::lang.page')</th>
                                    @foreach ($sub_categories as $item)
                                    <th>@lang('mpcs::lang.qty')</th>
                                    <th>@lang('mpcs::lang.amount')</th>
                                    @endforeach
                                    <th>@lang('mpcs::lang.total_amount')</th>
        
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $bill_no =1;
                                @endphp
                                @foreach ($credit_sales as $credit_sale)
                                    <tr>
                                        <td>{{$bill_no}}</td>
                                        <td>{{$credit_sale->order_no}}</td>
                                        <td>{{$credit_sale->customer}}</td>
                                        <td><input class="form-control" type="text" value="" /></td>
                                        @php $amount = 0; @endphp
                                        @foreach ($sub_categories as $item)
                                            @if($item->id == $credit_sale->sub_category_id)
                                                @php $amount = $credit_sale->quantity * $credit_sale->unit_price; @endphp
                                                <td><span class="display_currency {{$item->id}}_qty" data-orig-value="{{$credit_sale->quantity}}" data-currency_symbol="false">{{@format_quantity($credit_sale->quantity)}}</span></td>
                                                <td><span class="{{$item->id}}_amount" data-orig-value="{{$amount}}" data-currency_symbol="false">{{@num_format($amount)}}</span></td>
                                            @else
                                            <td></td>
                                            <td></td>
                                            @endif
                                        @endforeach
                                        <td><span class="total_amount" data-orig-value="{{$amount}}" data-currency_symbol="false">{{@num_format($amount)}}</span></td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="bg-gray">
                                <tr>
                                    <td class="text-red text-bold" colspan="4">@lang('mpcs::lang.total_this_page')</td>
                                    @foreach ($sub_categories as $item)
                                        <td class="text-red text-bold" id="footer_f9c_qty_this_page_{{$item->id}}">0.00</td>
                                        <td class="text-red text-bold" id="footer_f9c_amount_this_page_{{$item->id}}">0.00</td>
                                    @endforeach
                                    <td class="text-red text-bold" id="footer_f9c_total_amount_this_page">0.00</td>
                                </tr>
                                <tr>
                                    <td class="text-red text-bold" colspan="4">@lang('mpcs::lang.total_previous_page')</td>
                                    @foreach ($sub_categories as $item)
                                        <td class="text-red text-bold" id="footer_f9c_qty_pre_page_{{$item->id}}">0.00</td>
                                        <td class="text-red text-bold" id="footer_f9c_amount_pre_page_{{$item->id}}">0.00</td>
                                    @endforeach
                                    <td class="text-red text-bold" id="footer_f9c_total_amount_pre_page">0.00</td>
                                </tr>
                                <tr>
                                    <td class="text-red text-bold" colspan="4">@lang('mpcs::lang.grand_total')</td>
                                    @foreach ($sub_categories as $item)
                                        <td class="text-red text-bold" id="footer_f9c_qty_grand_{{$item->id}}">0.00</td>
                                        <td class="text-red text-bold" id="footer_f9c_amount_grand_{{$item->id}}">0.00</td>
                                    @endforeach
                                    <td class="text-red text-bold" id="footer_f9c_total_amount_grand">0.00</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        @endcomponent
    </div>
</div>

