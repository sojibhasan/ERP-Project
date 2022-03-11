
<style>
    #financail_status_table {
        table-layout: fixed;
        width: 100%;
    }

    #financail_status_table td {
        width: 25%;
    }

    table>tbody>tr>td {
        font-size: 14px !important;
    }

    table>thead>tr>th {
        font-size: 14px !important;
    }
    .heading_td {
        width: 75%;
    }
</style>

<!-- Main content -->
<section class="content">
    <div class="row">
        <div class="col-md-12">
            @component('components.widget', ['class' => 'box-primary'])
            @slot('tool')
            <div class="box-tools">
                <button class="btn btn-block btn-primary print_report pull-right" onclick="printDailyReport()">
                    <i class="fa fa-print"></i> @lang('messages.print')</button>
                </div>
                @endslot
                <div id="daily_report_div">
                    <style>  @media print {
                        td {
                            line-height: 5px !important;
                        }
                        
                        th {
                            line-height: 5px !important;
                        }
                    }
                </style>
                <div class="table-responsive">
                    <h4 class="pull-left text-red">@lang('report.date_range'): @lang('report.from') {{$print_s_date}}
                    @lang('report.to') {{$print_e_date}}</h4>
                    <table class="table table-bordered table-striped" id="daily_report_table">
                        <thead>
                        </thead>
                        <tbody>
                            @if(!empty($location_details))
                            <tr>
                                <th colspan="5" class="text-center">{{$location_details->name}} <br>
                                    {{$location_details->city}}
                                </th>
                            </tr>
                            @else
                            <tr>
                                <th colspan="5" class="text-center">{{request()->session()->get('business.name')}}
                                </th>
                            </tr>
                            @endif
                            @if($day_diff == 0)
                            <tr>
                                <th colspan="5" class="text-right">Shift: {{$work_shift}}</th>
                            </tr>
                            @endif
                        </tbody>
                    </table>

                    <table class="table table-bordered table-striped" id="sale_table">
                        <thead>
                            <tr>
                                <th colspan="5" class="text-left"><span style="background: #800080; padding: 5px 10px 5px 10px; color: #fff;">Sale</span> </th>
                            </tr>
                            <tr>
                                <th>Product</th>
                                <th>Qty</th>
                                <th colspan="2">Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                            $total_sales_amount = 0;
                            @endphp
                            <!-- regular sales -->
                            @foreach ($sales as $sale)
                            <tr>
                                <td>{{$sale->category_name}}</td>
                                <td>{{@format_quantity($sale->qty)}}</td>
                                <td colspan="2">{{@num_format($sale->total_amount)}}</td>
                            </tr>
                            @php
                            $total_sales_amount += $sale->total_amount;
                            @endphp
                            @endforeach


                            <!-- petro sales -->
                            @if($petro_module)
                            @foreach ($petro_sales as $petro_sale)
                            <tr>
                                <td>{{$petro_sale->sub_category_name}}</td>
                                <td>{{@format_quantity($petro_sale->qty)}}</td>
                                <td colspan="2">{{@num_format($petro_sale->total_amount)}}</td>
                            </tr>
                            @php
                            $total_sales_amount += $petro_sale->total_amount;
                            @endphp
                            @endforeach
                            @endif
                            
                            <tr>
                                <th colspan="2">Total Sale Amount</th>
                                <th>{{@num_format($total_sales_amount)}}</th>
                            </tr>

                        </tbody>
                    </table>
                    <!--------------------->
                    <!------   ADD    ----->
                    <!--------------------->
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-bordered table-striped" id="daily_report_table">
                                <thead>
                                    <tr>
                                        <th colspan="5" class="text-left">Add</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="heading_td">Received Payment for Outstanding</td>
                                        <td>{{@num_format($total_received_outstanding)}}</td>
                                    </tr>
                                    <tr>
                                        <td class="heading_td">Received Customer Deposit / Advance</td>
                                        <td>{{@num_format($deposit_by_customer)}}</td>
                                    </tr>
                                    <tr>
                                        <td class="heading_td">Withdraw Cash from Banks</td>
                                        <td>{{@num_format($withdrawal_cash)}}</td>
                                    </tr>
                                    @if($petro_module)
                                    <tr>
                                        <td class="heading_td">Excess Payments Total</td>
                                        <td colspan="2">{{@num_format($excess_total)}}</td>
                                    </tr>
                                    @endif
                                    <tr>
                                        <th class="heading_td">Total Income</th>
                                        <th>{{@num_format($total_income)}}</th>
                                    </tr>
                                    <tr>
                                        <td>&nbsp; </td>
                                        <td>&nbsp; </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <!--------------------->
                        <!------   LESS    ----->
                        <!--------------------->
                        <div class="col-md-6">
                            <table class="table table-bordered table-striped" id="daily_report_table">
                                <thead>
                                    <tr>
                                        <th colspan="5" class="text-left">Less</th>
                                    </tr>
                                </thead> 
                                <tbody>
                                    @if($petro_module)
                                    <tr>
                                        <td class="heading_td">Shortage Total</td>
                                        <td colspan="2">{{@num_format($shortage_total)}}</td>
                                    </tr>
                                    <tr>
                                        <td class="heading_td">Expense In Settlement</td>
                                        <td colspan="2">{{@num_format($expense_in_settlement)}}</td>
                                    </tr>
                                    @endif
                                    <tr>
                                        <td class="heading_td">Discount Given</td>
                                        <td colspan="2">{{@num_format($dicount_given)}}</td>
                                    </tr>
                                    <tr>
                                        <th class="heading_td">Total Out</th>
                                        <th colspan="2">{{@num_format($total_out)}}</th>
                                    </tr>


                                    <tr>
                                        <th class="heading_td">Balance In Hand</th>
                                        <th colspan="2">{{@num_format($balance_in_hand)}}</th>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <table class="table table-bordered table-striped" id="financail_status_table">
                        <thead>
                            <tr>
                                <th class="text-left"><span style="background: #800080; padding: 5px 10px 5px 10px; color: #fff;">Financial Status</span></th>
                                <th>Cash</th>
                                <th>Customer Cheques</th>
                                <th>Card</th>
                                <th>Credit Sales</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Previous Day Balance</td>
                                <td>{{@num_format($previous_day_balance['cash'])}}</td>
                                <td>{{@num_format($previous_day_balance['cheque'])}}</td>
                                <td>{{@num_format($previous_day_balance['card'])}}</td>
                                <td>{{@num_format($previous_day_balance['credit_sale'])}}</td>
                            </tr>
                            <tr>
                                <td>Received</td>
                                <td>{{@num_format($received['cash'])}}</td>
                                <td>{{@num_format($received['cheque'])}}</td>
                                <td>{{@num_format($received['card'])}}</td>
                                <td>{{@num_format($received['credit_sale'])}}</td>
                            </tr>

                            <tr>
                                <td>Shortage Recovers</td>
                                <td>{{@num_format($shortage_recover['cash'])}}</td>
                                <td>{{@num_format($shortage_recover['cheque'])}}</td>
                                <td>{{@num_format($shortage_recover['card'])}}</td>
                                <td>{{@num_format($shortage_recover['credit_sale'])}}</td>
                            </tr>
                            <tr>
                                <td>Excess & Commission Paid</td>
                                <td>{{@num_format($excess_commission['cash'])}}</td>
                                <td>{{@num_format($excess_commission['cheque'])}}</td>
                                <td>{{@num_format($excess_commission['card'])}}</td>
                                <td>{{@num_format($excess_commission['credit_sale'])}}</td>
                            </tr>
                            <tr>
                                <td>Direct Cash Expenses</td>
                                <td>{{@num_format($direct_cash_expenses)}}</td>
                                <td>{{@num_format(0)}}</td>
                                <td>{{@num_format(0)}}</td>
                                <td>{{@num_format(0)}}</td>
                            </tr>
                            <tr>
                                <td>Purchase By Cash</td>
                                <td>{{@num_format($total_purchase_by_cash)}}</td>
                                <td>{{@num_format(0)}}</td>
                                <td>{{@num_format(0)}}</td>
                                <td>{{@num_format(0)}}</td>
                            </tr>
                            <tr>
                                <td>Deposited</td>
                                <td>{{@num_format($deposit['cash'])}}</td>
                                <td>{{@num_format($deposit['cheque'])}}</td>
                                <td>{{@num_format($deposit['card'])}}</td>
                                <td>{{@num_format($deposited_credit_sale)}}</td>
                            </tr>
                            <tr>
                                <td>Balance</td>
                                <td>{{@num_format($balance['cash'])}}</td>
                                <td>{{@num_format($balance['cheque'])}}</td>
                                <td>{{@num_format($balance['card'])}}</td>
                                <td>{{@num_format($balance['credit_sale'])}}</td>
                            </tr>
                        </tbody>
                    </table>
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-bordered table-striped" id="outstanding_details_table">
                                <thead>
                                    <tr>
                                        <th><span style="background: #800080; padding: 5px 10px 5px 10px; color: #fff;">Outstanding Details</span></th>
                                        <th>Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="heading_td">Previous Day Outstadning Balance</td>
                                        <td>{{@num_format($outstandings['previous_day'])}}</td>
                                    </tr>
                                    <tr>
                                        <td class="heading_td">Credit Sale(Given)</td>
                                        <td>{{@num_format($outstandings['given'])}}</td>
                                    </tr>
                                    <tr>
                                        <td class="heading_td">Credit Sale(Received)</td>
                                        <td>{{@num_format($outstandings['received'])}}</td>
                                    </tr>
                                    <tr>
                                        <td class="heading_td">Balance Outstanding</td>
                                        <td>{{@num_format($outstandings['balance'])}}</td>
                                    </tr>
                                    <tr>
                                        <td>&nbsp; </td>
                                        <td>&nbsp; </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-bordered table-striped" id="daily_report_table">
                                <thead>
                                    <tr>
                                        <th><span style="background: #800080; padding: 5px 10px 5px 10px; color: #fff;">Stock Value Status</span></th>
                                        <th colspan="2">Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>

                                        <td class="heading_td">Previous Day Stock</td>
                                        <td colspan="2">{{@num_format($stock_values['previous_day_stock'])}}</td>
                                    </tr>
                                    <tr>

                                        <td class="heading_td">Sale Returned Stock</td>
                                        <td colspan="2">{{@num_format($stock_values['sale_return'])}}</td>
                                    </tr>
                                    <tr>

                                        <td class="heading_td">Purchase Stock</td>
                                        <td colspan="2">{{@num_format(abs($stock_values['purchase_stock']))}}</td>
                                    </tr>
                                    <tr>

                                        <td class="heading_td">Sold Stock Value in Cost</td>
                                        <td colspan="2">{{@num_format(abs($stock_values['sold_stock']))}}</td>
                                    </tr>

                                    <tr>
                                        <td class="heading_td">Balance Stock</td>
                                        <td colspan="2">{{@num_format($stock_values['balance'])}}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    @if($petro_module)
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-bordered table-striped" id="outstanding_details_table">
                                <thead>
                                    <tr>
                                        <th><span style="background: #800080; padding: 5px 10px 5px 10px; color: #fff;">Pump Operator Shortage</span></th>
                                        <th>Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="heading_td">Previous Day Shortage Balance</td>
                                        <td>{{@num_format($pump_operator_shortage['previous_day'])}}</td>
                                    </tr>
                                    <tr>
                                        <td class="heading_td">Today Shortage</td>
                                        <td>{{@num_format($pump_operator_shortage['given'])}}</td>
                                    </tr>
                                    <tr>
                                        <td class="heading_td">Shortage Recovered</td>
                                        <td>{{@num_format($pump_operator_shortage['received'])}}</td>
                                    </tr>
                                    <tr>
                                        <td class="heading_td">Balance Shortage</td>
                                        <td>{{@num_format($pump_operator_shortage['balance'])}}</td>
                                    </tr>
                                    <tr>
                                        <td>&nbsp; </td>
                                        <td>&nbsp; </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-bordered table-striped" id="outstanding_details_table">
                                <thead>
                                    <tr>
                                        <th><span style="background: #800080; padding: 5px 10px 5px 10px; color: #fff;">Pump Operator Excess</span></th>
                                        <th>Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="heading_td">Previous Day Excess Balance</td>
                                        <td>{{@num_format($pump_operator_excess['previous_day'])}}</td>
                                    </tr>
                                    <tr>
                                        <td class="heading_td">Today Excess</td>
                                        <td>{{@num_format($pump_operator_excess['given'])}}</td>
                                    </tr>
                                    <tr>
                                        <td class="heading_td">Excess Paid</td>
                                        <td>{{@num_format($pump_operator_excess['received'])}}</td>
                                    </tr>
                                    <tr>
                                        <td class="heading_td">Balance Excess</td>
                                        <td>{{@num_format($pump_operator_excess['balance'])}}</td>
                                    </tr>
                                    <tr>
                                        <td>&nbsp; </td>
                                        <td>&nbsp; </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-bordered table-striped" id="dip_details">
                                <thead>
                                    <tr>
                                        <th><span style="background: #800080; padding: 5px 10px 5px 10px; color: #fff;">Dip Details</span></th>
                                        <th>Product Name</th>
                                        <th>Opening Dip Difference</th>
                                        <th>Balance Dip Difference</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($dip_details as $dip_detail)
                                    <tr>
                                        <td>{{$dip_detail->tank_name}}</td>
                                        <td>{{$dip_detail->product_name}}</td>
                                        <td>{{@num_format($dip_detail->opening_balance_dip_difference)}}</td>
                                        <td>{{@num_format($dip_detail->balance_dip_difference)}}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
            @endcomponent
        </div>
    </div>
</section>
<!-- /.content -->
<div class="modal fade view_register" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
</div>
