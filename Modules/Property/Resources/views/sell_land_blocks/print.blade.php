@extends('layouts.printlayout')
{{--@section('title', __('property::lang.sell_land_blocks'))--}}

@section('content')
    <div class="row">
        <div class="col-md-12">
            <span style="float: right">
                <button class="btn btn-primary" onclick="printContent('printing-page')">Print</button>
                <a href="javascript:void(0)" class="btn btn-danger back-to-sales-dashboard" style="margin-left: 15px">Back to Sales Dashboard</a>
            </span>
        </div>
    </div>
<div id="printing-page">
        <div class="col-md-12 ">
            <div class="col-md-8 col-sm-8 col-xs-8 text-center">
                <h2>{{$business->name}}</h2>
                <p>{{$location_details->landmark}}, {{$location_details->city}}, {{$location_details->state}},
                    {{$location_details->zip_code}}, {{$location_details->country}}<br>
                </p>
                <br>
                <p> @lang('property::lang.tel'): {{$location_details->mobile }} &Tab; @lang('property::lang.email'):
                    {{$location_details->email }}</p>
            </div>
            <div class="col-md-4 col-sm-4 col-xs-4 text-left">
                <h4 style="margin-top: 25px;">@lang('property::lang.no'): {{$transaction->invoice_no}}</h4>
                <h4>@lang('property::lang.date'): {{@format_date($transaction->transaction_date)}}</h4>
                <h4>@lang('property::lang.reg_no'): {{$business->reg_no}}</h4>
            </div>
        </div>
        <div class="clearfix"></div>
        <div class="row text-center">
            <h1>@lang('property::lang.invoice')</h1>
        </div>
        <div class="clearfix"></div>
        <div class="col-md-6 col-sm-6 col-xs-6">
            <p>@lang('property::lang.customer'): <strong>{{$contact->name}}</strong><br>
                {{ $contact->address }} <br>
                {{ $contact->city }} <br>
                @lang('property::lang.customer_mobile'): {{$contact->mobile}} <br>
                @lang('property::lang.customer_nic_passport'): {{ $contact->nic_number }}
            </p>
        </div>
        <div class="col-md-6 col-sm-6 col-xs-6">
            <p>
                @lang('property::lang.project_name'): {{$property->name}} <br>
                @lang('property::lang.block_no'): {{implode(',', $sold_block_nos)}} <br>
                @lang('property::lang.block_price'): {{ implode(',',$sold_total_block_value) }}<br>
                @lang('property::lang.tem_receipt_no'): {{ $block_value->block_id }}<br>
            </p>
        </div>
        <div class="clearfix"></div>
        <div class="col-md-12">
            <table class="table table-striped" id="payment_added_table">
                <thead>
                <tr>
                    <th>{{ __('property::lang.index') }}</th>
                    <th>{{ __('property::lang.on_account_of') }}</th>
                    <th>{{ __('property::lang.amount') }}</th>
                </tr>
                </thead>

                <tbody>
                @php
                    $i =1;
                @endphp
                <?php $total =0 ; ?>
                @foreach ($payments as $payment)
                    <tr>
                        <td>{{$i}}</td>
                        <td>{{ \Modules\Property\Entities\PaymentOption::find($payment['payment_option_id'])->payment_option}}</td>
                        <td>{{@num_format($payment['amount'])}}</td>
                        <?php $total = $total + $payment['amount']; ?>
                    </tr>

                    @php
                        $i++;
                    @endphp
                @endforeach
                <tr>
                    <td class="text-right" colspan="2">@lang('property::lang.total')</td>
                    <td>{{@num_format($total)}}</td>
                </tr>
                </tbody>
            </table>
            <table class="table table-striped" id="payment_added_table">
                <thead>
                <tr>
                    <th>{{ __('property::lang.payment_method') }}</th>
                    <th>{{ __('property::lang.cheque_no') }}</th>
                    <th>{{ __('property::lang.bank') }}</th>
                    <th>{{ __('property::lang.branch') }}</th>
                    <th>{{ __('property::lang.amount')}}</th>
                </tr>
                </thead>

                <tbody>
{{--                <!--@foreach ($payments as $item)-->--}}
{{--                <!--<tr>-->--}}
{{--                <!--    <td>-->--}}
{{--                <!--        @if($item->method == 'bank_transfer')-->--}}
{{--                <!--        @lang('property::lang.bank')-->--}}
{{--                <!--        @else-->--}}
{{--                <!--        {{ucfirst($item->method)}}-->--}}
{{--                <!--        @endif-->--}}
{{--                <!--    </td>-->--}}
{{--                <!--    <td>{{$item->cheque_number}}</td>-->--}}
{{--                <!--    <td>{{$payment->bank_name}}</td>-->--}}
{{--                <!--    <td>{{$payment->branch}}</td>-->--}}
{{--                <!--</tr>-->--}}
{{--                <!--@endforeach-->--}}
                <?php $total_pay =0 ; ?>

                @foreach($payment_record as $pay_val)
                    @if($pay_val['method'] == 'bank_transfer')
                        <tr>
                            <td>@lang('property::lang.bank')</td>
                            <td>{{$pay_val['cheque_number']}}</td>
                            <td>{{$pay_val['bank_name']}}</td>
                            <td></td>
                            <td>{{@num_format($pay_val['payment_method_amount'])}}</td>
                            <?php $total_pay = $total_pay + $pay_val['payment_method_amount']; ?>
                        </tr>
                    @elseif($pay_val['method'] == 'cash')
                        <tr>
                            <td>{{ucfirst($pay_val['method'])}}</td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td>{{@num_format($pay_val['payment_method_amount'])}}</td>
                            <?php $total_pay = $total_pay + $pay_val['payment_method_amount']; ?>
                        </tr>
                    @elseif($pay_val['method'] == 'cheque')
                        <tr>
                            <td>{{ucfirst($pay_val['method'])}}</td>
                            <td>{{$pay_val['cheque_number']}}</td>
                            <td>{{$pay_val['bank_name']}}</td>
                            <td></td>
                            <td>{{@num_format($pay_val['payment_method_amount'])}}</td>
                            <?php $total_pay = $total_pay + $pay_val['payment_method_amount']; ?>
                        </tr>
                    @elseif($pay_val['method'] == 'card')
                        <tr>
                            <td>{{ucfirst($pay_val['method'])}}</td>
                            <td>{{$pay_val['cheque_number']}}</td>
                            <td>{{$pay_val['bank_name']}}</td>
                            <td></td>
                            <td>{{@num_format($pay_val['payment_method_amount'])}}</td>
                            <?php $total_pay = $total_pay + $pay_val['payment_method_amount']; ?>
                        </tr>
                    @elseif($pay_val['method'] == 'direct_bank_deposit')
                        <tr>
                            <td>{{ucfirst($pay_val['method'])}}</td>
                            <td>{{$pay_val['cheque_number']}}</td>
                            <td>{{$pay_val['bank_name']}}</td>
                            <td></td>
                            <td>{{@num_format($pay_val['payment_method_amount'])}}</td>
                            <?php $total_pay = $total_pay + $pay_val['payment_method_amount']; ?>
                        </tr>
                    @endif
                @endforeach
                <tr>
                    <td class="text-right" colspan="4">@lang('property::lang.total')</td>
                    <td>{{@num_format($total_pay)}}</td>
                </tr>
                </tbody>
            </table>
        </div>
        <div class="clearfix"></div>
        <div class="row text-center">
            <div class="row">
                <div class="col-md-6 col-sm-6 col-xs-6">
                    .................... <br>
                    @lang('property::lang.customer_signature')
                </div>
                <div class="col-md-6 col-sm-6 col-xs-6">
                    .................... <br>
                    @lang('property::lang.cashier')
                </div>
            </div>

        </div>
    </div>
@endsection
@section('javascript')
    <script>
        function printContent(el){
            var restorepage = $('body').html();
            var printcontent = $('#' + el).clone();
            $('body').empty().html(printcontent);
            window.print();
            $('body').html(restorepage);
        }

        $(".back-to-sales-dashboard").click(function(){
            window.location.href = "sale-and-customer-payment/dashboard?type=customer";
        });
    </script>
@endsection
