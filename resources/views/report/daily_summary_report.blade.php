<!-- Main content -->
<section class="content">
    <div class="row">
        <div class="col-md-12">
            @component('components.widget', ['class' => 'box-primary'])
            @slot('tool')
            <div class="box-tools">
                <button class="btn btn-block btn-primary print_report" onclick="printDailySummaryDiv()">
                    <i class="fa fa-print"></i> @lang('messages.print')</button>
            </div>
            @endslot
            <div id="daily_summary_report_div">
                <div class="row text-center">
                    <div class="col-md-12 text-center">
                        @if(!empty($location_details))
                        <h5 style="font-weight: bold;">{{$location_details->name}} <br>
                            {{$location_details->city}}</h5>
                        @else
                        <tr>
                            <th colspan="5" class="text-center">
                                <h3>{{request()->session()->get('business.name')}} </h3>
                            </th>
                        </tr>
                        @endif
                    </div>
                    @if($day_diff == 0)
                    <div class="col-md-12 text-right">
                        <h5 style="font-weight: bold;"> Shift: {{$work_shift}}</h5>
                    </div>
                    @endif
                </div>
                <div class="col-md-12">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped" id="daily_summary_report_table">
                            <thead>
                                @if($pump_operator_sales->count() > 0)
                                <th>@lang('report.pump_operator')</th>
                                <th>@lang('report.settlement_no')</th>
                                @else
                                <th>@lang('report.cashiers')</th>
                                @endif
                                <th>@lang('report.cash')</th>
                                <th>@lang('report.cheque')</th>
                                <th>@lang('report.card')</th>
                                <th>@lang('report.credit_sale')</th>
                                <th>@lang('report.short')</th>
                                <th>@lang('report.excess')</th>
                                <th>@lang('report.expense')</th>
                                <th>@lang('report.total_sale')</th>
                            </thead>
                            <tbody>
                                @php
                                $total_row['cash'] = 0;
                                $total_row['cheque'] = 0;
                                $total_row['card'] = 0;
                                $total_row['credit_sale'] = 0;
                                $total_row['shortage'] = 0;
                                $total_row['excess'] = 0;
                                $total_row['expense'] = 0;
                                @endphp
                                @php
                                $total_row['cash'] = $pump_operator_sales->sum('cash_total') +
                                $cashiers->sum('cash_total');
                                $total_row['cheque'] = $pump_operator_sales->sum('cheque_total') +
                                $cashiers->sum('cheque_total');
                                $total_row['card'] = $pump_operator_sales->sum('card_total') +
                                $cashiers->sum('card_total');
                                $total_row['credit_sale'] = $pump_operator_sales->sum('credit_sale_total') +
                                $cashiers->sum('credit_sale_total');
                                $total_row['shortage'] = $pump_operator_sales->sum('shortage_amount');
                                $total_row['excess'] = $pump_operator_sales->sum('excess_amount') ;
                                $total_row['expense'] = $pump_operator_sales->sum('expense_amount') +
                                $cashiers->sum('expense_amount');
                                @endphp
                                @foreach ($pump_operator_sales as $pump_operator_sale)
                                <tr>
                                    <td>{{ $pump_operator_sale->pump_operator_name}}</td>
                                    @if($day_diff <= 5) <td>{{ $pump_operator_sale->settlement_nos}}</td>
                                        @else
                                        <td></td>
                                        @endif
                                        <td>{{@num_format($pump_operator_sale->cash_total)}}</td>
                                        <td>{{@num_format($pump_operator_sale->cheque_total)}}</td>
                                        <td>{{@num_format($pump_operator_sale->card_total)}}</td>
                                        <td>{{@num_format($pump_operator_sale->credit_sale_total)}}</td>
                                        <td>{{@num_format($pump_operator_sale->shortage_amount)}}</td>
                                        <td>{{@num_format($pump_operator_sale->excess_amount)}}</td>
                                        <td>{{@num_format($pump_operator_sale->expense_amount)}}</td>
                                        <td>{{@num_format($pump_operator_sale->expense_amount + $pump_operator_sale->excess_amount + $pump_operator_sale->shortage_amount + $pump_operator_sale->credit_sale_total + $pump_operator_sale->card_total + $pump_operator_sale->cheque_total + $pump_operator_sale->cash_total)}}
                                        </td>
                                </tr>
                                @endforeach

                                @foreach ($cashiers as $cashier)
                                <tr>
                                    <td>{{ $cashier->cashier_name}}</td>
                                    <td>{{@num_format($cashier->cash_total)}}</td>
                                    <td>{{@num_format($cashier->cheque_total)}}</td>
                                    <td>{{@num_format($cashier->card_total)}}</td>
                                    <td>{{@num_format($cashier->credit_sale_total)}}</td>
                                    <td>{{@num_format(0.00)}}</td>
                                    <td>{{@num_format(0.00)}}</td>
                                    <td>{{@num_format($cashier->expense_total)}}</td>
                                    <td>{{@num_format($cashier->cash_total + $cashier->cheque_total + $cashier->card_total + $cashier->credit_sale_total + $cashier->expense_total)}}
                                    </td>
                                </tr>
                                @endforeach

                                <tr class="text-red">
                                    <td colspan="2"><b>@lang('lang_v1.total')</b></td>
                                    <td>{{@num_format($total_row['cash'])}}</td>
                                    <td>{{@num_format($total_row['cheque'])}}</td>
                                    <td>{{@num_format($total_row['card'])}}</td>
                                    <td>{{@num_format($total_row['credit_sale'])}}</td>
                                    <td>{{@num_format($total_row['shortage'])}}</td>
                                    <td>{{@num_format($total_row['excess'])}}</td>
                                    <td>{{@num_format($total_row['expense'])}}</td>
                                    <td>{{@num_format($total_row['cash'] + $total_row['cheque'] + $total_row['card'] + $total_row['credit_sale'] + $total_row['shortage'] + $total_row['excess'] + $total_row['expense'])}}
                                    </td>
                                </tr </tbody> </table> </div> </div> <div class="row">
                                <div class="col-md-6">
                                    <div class="col-md-12">
                                        <h4 style="font-weight:bold;">@lang('report.stock_summary_qty')</h4>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="table-responsive">
                                            <table class="table table-bordered table-striped" id="stock_table">
                                                <thead>
                                                    <th>@lang('report.category')</th>
                                                    <th>@lang('report.sub_category')</th>
                                                    <th>@lang('report.previous_day_qty')</th>
                                                    <th>@lang('report.pruchase_qty')</th>
                                                    <th>@lang('report.stock_adjustment')</th>
                                                    <th>@lang('report.sold_qty')</th>
                                                    <th>@lang('report.balance_qty')</th>
                                                </thead>
                                                <tbody>
                                                    @php
                                                    $total_preday = 0;
                                                    $total_purchase = 0;
                                                    $total_stock_adjusted = 0;
                                                    $total_sold = 0;
                                                    $total_balance = 0;
                                                    @endphp
                                                    @foreach ($stock_qty as $stock)
                                                    @php
                                                    $preday = $stock['preday_purchase_stock'] -
                                                    $stock['preday_total_sold'] + $stock['preday_stock_adjusted'];

                                                    $total_preday += $preday;
                                                    $total_purchase += $stock['purchase_stock'];
                                                    $total_stock_adjusted += $stock['stock_adjusted'];
                                                    $total_sold += $stock['total_sold'];
                                                    $total_balance += $preday + $stock['purchase_stock'] -
                                                    $stock['total_sold'] + $stock['stock_adjusted'];
                                                    @endphp
                                                    <tr>
                                                        <td>{{$stock['category_name']}}</td>
                                                        <td>{{$stock['sub_category_name']}}</td>
                                                        <td>{{@num_format($preday)}}</td>
                                                        <td>{{@num_format($stock['purchase_stock'])}}</td>
                                                        <td>{{@num_format($stock['stock_adjusted'])}}</td>
                                                        <td>{{@num_format($stock['total_sold'])}}</td>
                                                        <td>{{@num_format($preday + $stock['purchase_stock'] - $stock['total_sold'] + $stock['stock_adjusted'])}}
                                                        </td>
                                                    </tr>
                                                    @endforeach
                                                    <tr class="text-red">
                                                        <th>@lang('report.total')</th>
                                                        <td> </td>
                                                        <td>{{@num_format($total_preday)}}</td>
                                                        <td>{{@num_format($total_purchase)}}</td>
                                                        <td>{{@num_format($total_stock_adjusted)}}</td>
                                                        <td>{{@num_format($total_sold)}}</td>
                                                        <td>{{@num_format($total_balance)}}</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>


                                <div class="col-md-6">
                                    <div class="col-md-12">
                                        <h4 style="font-weight:bold;">@lang('report.stock_summary_value')</h4>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="table-responsive">
                                            <table class="table table-bordered table-striped" id="stock_value_table">
                                                <thead>
                                                    <th>@lang('report.category')</th>
                                                    <th>@lang('report.sub_category')</th>
                                                    <th>@lang('report.previous_day_value')</th>
                                                    <th>@lang('report.pruchase_value')</th>
                                                    <th>@lang('report.stock_adjustment_value')</th>
                                                    <th>{{__('Sold Value')}}</th> 
                                                    <th>@lang('report.balance_value')</th>
                                                </thead>
                                                <tbody>
                                                    @php
                                                    $total_preday_value = 0;
                                                    $total_purchase_value = 0;
                                                    $total_stock_adjusted_value = 0;
                                                    $total_sold_value = 0;
                                                    $total_balance_value = 0;
                                                    @endphp
                                                    @foreach ($stock_values as $stock_value)
                                                    @php
                                                    $preday = $stock_value['preday_purchase_stock'] -
                                                    $stock_value['preday_total_sold'] + $stock_value['preday_stock_adjusted'];

                                                    $total_preday_value += $preday;
                                                    $total_purchase_value += $stock_value['purchase_stock'];
                                                    $total_stock_adjusted_value += $stock_value['stock_adjusted'];
                                                    $total_sold_value += $stock_value['total_sold'];
                                                    $total_balance_value += $preday + $stock_value['purchase_stock'] -
                                                    $stock_value['total_sold'] + $stock_value['stock_adjusted'] ;
                                                    @endphp
                                                    <tr>
                                                        <td>{{$stock_value['category_name']}}</td>
                                                        <td>{{$stock_value['sub_category_name']}}</td>
                                                        <td>{{@num_format($preday)}}</td>
                                                        <td>{{@num_format($stock_value['purchase_stock'])}}</td>
                                                        <td>{{@num_format($stock_value['stock_adjusted'])}}</td>
                                                        <td>{{@num_format($stock_value['total_sold'])}}</td> 
                                                        <td>{{@num_format($preday + $stock_value['purchase_stock'] - $stock_value['total_sold'])}}
                                                        </td>
                                                    </tr>
                                                    @endforeach
                                                    <tr class="text-red">
                                                        <th>@lang('report.total')</th>
                                                        <td> </td>
                                                        <td>{{@num_format($total_preday_value)}}</td>
                                                        <td>{{@num_format($total_purchase_value)}}</td>
                                                        <td>{{@num_format($total_stock_adjusted_value)}}</td>
                                                        <!-- <td>{{@num_format($total_sold_value)}}</td> -->
                                                        <td>{{@num_format($total_balance_value)}}</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                    </div>
                </div>
                @endcomponent
            </div>
        </div>
</section>
<!-- /.content -->
<div class="modal fade view_register" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
</div>