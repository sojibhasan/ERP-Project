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
                <button class="btn btn-block btn-primary print_report pull-right" onclick="printMonthlyReport()">
                    <i class="fa fa-print"></i> @lang('messages.print')</button>
            </div>
            @endslot
            <div id="monthly_report_div">
                <style>
                    @media print {
                        td {
                            line-height: 5px !important;
                        }

                        th {
                            line-height: 5px !important;
                        }
                    }
                </style>

                @php
                $count = $sub_categories->count();
                @endphp


                <div class="table-responsive">
                    <h4 class="pull-left text-red">@lang('report.month_range'):
                        @if($print_s_date!=$print_e_date)@lang('report.from') {{$print_s_date}}
                        @lang('report.to') {{$print_e_date}} @else {{$print_s_date}} @endif ({{$year}}) </h4>
                    <table class="table table-bordered table-striped" id="daily_report_table">
                        <thead>
                        </thead>
                        <tbody>
                            @if(!empty($location_details))
                            <tr>
                                <th colspan="{{ $count+ 23 }}" class="text-center">{{$location_details->name}} <br>
                                    {{$location_details->city}}
                                </th>
                            </tr>
                            @else
                            <tr>
                                <th colspan="{{ $count+ 23 }}" class="text-center">
                                    {{request()->session()->get('business.name')}}
                                </th>
                            </tr>
                            @endif
                            <tr>
                                <th colspan="{{ $count+ 23 }}" class="text-right">Shift: {{$work_shift}}</th>
                            </tr>
                        </tbody>
                    </table>
                    <table class="table table-bordered table-striped" id="sale_table">
                        <thead>
                            <tr>
                                <th colspan="" class="text-left"><span
                                        style="background: #800080; padding: 5px 10px 5px 10px; color: #fff;">Sale</span>
                                </th>
                            </tr>
                            <tr>
                                <th>@lang('report.date')</th>
                                <th colspan="2">@lang('report.sale')</th>
                                @foreach($sub_categories as $subcat)
                                <th>{{ $subcat->name }}</th>
                                @endforeach
                                <th>@lang('report.cash_total')</th>
                                <th>@lang('report.cheque_total')</th>
                                <th>@lang('report.visa_master_total') </th>
                                <th>@lang('report.amex_total')</th>
                                <th>@lang('report.other_credit_card_total')</th>
                                <th>@lang('report.short_total')</th>
                                <th>@lang('report.excess_total') </th>
                                <th>@lang('report.credit_sales')</th>
                                <th>@lang('report.expense_in_settlement_total')</th>
                                <th>@lang('report.direct_expense_total') </th>
                                <th>@lang('report.supplier_payment_in_cash_total')</th>
                                <th>@lang('report.total_collection')</th>
                                <th>@lang('report.difference')</th>
                                <th>@lang('report.credit_cash')</th>
                                <th>@lang('report.credit_cheques')</th>
                                <th>@lang('report.credit_visa_master')</th>
                                <th>@lang('report.credit_amex_master')</th>
                                <th>@lang('report.today_total_cash')</th>
                                <th>@lang('report.previous_day_cash_balance')</th>
                                <th>@lang('report.total_cash_balance')</th>
                                <th>@lang('report.cash_deposited') </th>
                                <th>@lang('report.cash_blaance_difference')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                            $total_sales_amount = 0;
                            @endphp
                            @foreach ($period as $date)
                            @php
                            $date = $date->format('Y-m-d');
                            @endphp

                            <tr>
                                <td> {{ @format_date($date) }}</td>
                                <td colspan="2">{{@num_format($total_sales[$date])}}</td>
                                @foreach($sub_categories as $sub_cat)
                                <td> {{ @num_format( $sub_category_sales[$date][$sub_cat->id]) }}</td>

                                @endforeach
                                <td>
                                    {{  @num_format($received_payments[$date]->cash) }}
                                </td>
                                <td>
                                    {{  @num_format($received_payments[$date]->cheque) }}
                                </td>
                                <td>
                                    {{  @num_format($received_payments[$date]->visa) }}
                                </td>
                                <td>
                                    {{  @num_format($received_payments[$date]->amex) }}
                                </td>
                                <td>
                                    {{  @num_format($received_payments[$date]->other_card) }}
                                </td>
                                <td>
                                    {{  @num_format($shortage_total[$date]) }}
                                </td>
                                <td>
                                    {{  @num_format($excess_total[$date]) }}
                                </td>
                                <td>
                                    {{  @num_format($credit_sales[$date]) }}
                                </td>
                                <td>
                                    {{  @num_format($expense_in_settlement[$date]) }}
                                </td>
                                <td>
                                    {{  @num_format($direct_expens[$date]) }}
                                </td>
                                <td>
                                    {{  @num_format($purchase_by_cash[$date]) }}
                                </td>
                                <td>
                                    {{  @num_format($total_collection[$date]) }}
                                </td>
                                <td>
                                    {{  @num_format($difference[$date]) }}
                                </td>
                                <td>
                                    {{  @num_format($credit_received_payments[$date]->cash) }}
                                </td>
                                <td>
                                    {{  @num_format($credit_received_payments[$date]->cheque) }}
                                </td>
                                <td>
                                    {{  @num_format($credit_received_payments[$date]->visa) }}
                                </td>
                                <td>
                                    {{  @num_format($credit_received_payments[$date]->amex) }}
                                </td>
                                <td>
                                    {{  @num_format($today_total_cash[$date]) }}
                                </td>
                                <td>
                                    {{  @num_format($previous_day_cash_balance[$date]) }}
                                </td>
                                <td>
                                    {{  @num_format($total_cash_balance[$date]) }}
                                </td>
                                <td>
                                    {{  @num_format($cash_deposit[$date]) }}
                                </td>
                                <td>
                                    {{  @num_format($cash_balance_difference[$date]) }}
                                </td>
                            </tr>
                            @php
                            $total_sales_amount += $total_sales[$date];
                            @endphp
                            @endforeach
                            <tr>
                                <th colspan="3">Total Sale Amount</th>
                                <th>{{@num_format($total_sales_amount)}}</th>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            @endcomponent
        </div>
    </div>
</section>
<!-- /.content -->
<div class="modal fade view_register" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
</div>