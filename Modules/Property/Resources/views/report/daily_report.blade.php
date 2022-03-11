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
                    <div class="table-responsive">
                        <h4 class="pull-left text-red">@lang('report.date_range'): @lang('report.from') {{ $print_s_date }}
                            @lang('report.to') {{ $print_e_date }}</h4>
                        <table class="table table-bordered table-striped" id="daily_report_table">
                            <thead>
                            </thead>
                            <tbody>
                                @if (!empty($location_details))
                                    <tr>
                                        <th colspan="5" class="text-center">{{ $location_details->name }} <br>
                                            {{ $location_details->city }}
                                        </th>
                                    </tr>
                                @else
                                    <tr>
                                        <th colspan="5" class="text-center">
                                            {{ request()->session()->get('business.name') }}
                                        </th>
                                    </tr>
                                @endif
                                @if ($day_diff == 0)
                                    <tr>
                                        <th colspan="5" class="text-right">Shift: {{ $work_shift }}</th>
                                    </tr>
                                @endif
                            </tbody>
                        </table>

                        <table class="table table-bordered table-striped" id="sale_table">
                            <thead>
                                <tr>
                                    <th colspan="5" class="text-left"><span
                                            style="background: #800080; padding: 5px 10px 5px 10px; color: #fff;">Sale</span>
                                    </th>
                                </tr>
                                <tr>
                                    <th>Project Name</th>
                                    <th>Total Blocks Sold</th>
                                    <th>Total Amount</th>
                                </tr>
                            </thead>
                            @if (count($sales) > 0)
                                <tbody>
                                    @php
                                        $total_sales_amount = 0;
                                    @endphp
                                    <!-- regular sales -->
                                    @foreach ($sales as $sale)
                                        <tr>
                                            <td>{{ $sale->property_name }}</td>
                                            <td>
                                                {{ @format_quantity($sale->total_blocks_sold) }}
                                                <button type="button" class="btn btn-xs btn-default btn-modal"
                                                    data-toggle="modal" data-target="#blocks_detail_modal"><i
                                                        class="fa fa-eye"></i>
                                                    @lang("property::lang.view_details")</button>
                                            </td>
                                            <td colspan="2">{{ @num_format($sale->total_amount) }}</td>
                                        </tr>
                                        @php
                                            $total_sales_amount += $sale->total_amount;
                                        @endphp
                                    @endforeach
                                    <tr>
                                        <th colspan="2">Total Sale Amount</th>
                                        <th>{{ @num_format($total_sales_amount) }}</th>
                                    </tr>
                                </tbody>
                            @endif
                        </table>
                    </div>
                </div>
                <table class="table table-bordered table-striped" id="financail_status_table">
                    <thead>
                        <tr>
                            <th class="text-left"><span
                                    style="background: #800080; padding: 5px 10px 5px 10px; color: #fff;">Financial
                                    Status</span></th>
                            <th>Cash</th>
                            <th>Customer Cheques</th>
                            <th>Card</th>
                            <th>Balance Payment Due</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Previous Day Balance</td>
                            <td>{{ @num_format($previous_day_balance['cash']) }}</td>
                            <td>{{ @num_format($previous_day_balance['cheque']) }}</td>
                            <td>{{ @num_format($previous_day_balance['card']) }}</td>
                            <td>{{ @num_format($balance['due']) }}</td>
                        </tr>
                        <tr>
                            <td>Received</td>
                            <td>{{ @num_format($received['cash']) }}</td>
                            <td>{{ @num_format($received['cheque']) }}</td>
                            <td>{{ @num_format($received['card']) }}</td>
                            <td>{{ @num_format($balance['due']) }}</td>

                        </tr>
                        <tr>
                            <td>Direct Cash Expenses</td>
                            <td>{{ @num_format($direct_cash_expenses) }}</td>
                            <td>{{ @num_format(0) }}</td>
                            <td>{{ @num_format(0) }}</td>
                            <td>{{ @num_format($balance['due']) }}</td>

                        </tr>
                        <tr>
                            <td>Purchase By Cash</td>
                            <td>{{ @num_format($total_purchase_by_cash) }}</td>
                            <td>{{ @num_format(0) }}</td>
                            <td>{{ @num_format(0) }}</td>
                            <td>{{ @num_format($balance['due']) }}</td>

                        </tr>
                        <tr>
                            <td>Deposited</td>
                            <td>{{ @num_format($deposit['cash']) }}</td>
                            <td>{{ @num_format($deposit['cheque']) }}</td>
                            <td>{{ @num_format($deposit['card']) }}</td>
                            <td>{{ @num_format($balance['due']) }}</td>

                        </tr>
                        <tr>
                            <td>Balance</td>
                            <td>{{ @num_format($balance['cash']) }}</td>
                            <td>{{ @num_format($balance['cheque']) }}</td>
                            <td>{{ @num_format($balance['card']) }}</td>
                            <td>{{ @num_format($balance['due']) }}</td>

                        </tr>
                    </tbody>
                </table>
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-bordered table-striped" id="financail_status_table">
                            <thead>
                                <tr>
                                    <th class="text-left" colspan="4"><span
                                            style="background: #800080; padding: 5px 10px 5px 10px; color: #fff;">Expenses</span>
                                    </th>
                                </tr>
                                <tr>
                                    <th>Expense Categories</th>
                                    <th>Amount</th>
                                    <th>Payment Method</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($expense_categories as $category)
                                    <tr>
                                        <td>{{ $category->category_name }}</td>
                                        <td>{{ @num_format($category->amount) }}</td>
                                        <td>{{ $category->payment_method }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped" id="price_changes_table">
                                <thead>
                                    <tr>
                                        <th colspan="6"><span
                                                style="background: #800080; padding: 5px 10px 5px 10px; color: #fff;">List
                                                Price Changes</span></th>
                                    </tr>
                                    <tr>
                                        <th>@lang('property::lang.sold_date')</th>
                                        <th>@lang('property::lang.property_name')</th>
                                        <th>@lang('property::lang.block_number')</th>
                                        <th>@lang('property::lang.sale_price')</th>
                                        <th>@lang('property::lang.sold_price')</th>
                                        <th>@lang('property::lang.sales_officer')</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($price_changes as $price_change)
                                        @foreach ($price_change->property_sell_lines as $item)
                                            <tr>
                                                <td>{{ @format_date($price_change->transaction_date) }}</td>
                                                <td>{{ $item->property->name }}</td>
                                                <td>{{ $item->block_number }}</td>
                                                <td>{{ @number_format($item->block->block_sale_price) }}</td>
                                                <td>{{ @number_format($item->block->block_sold_price) }}</td>
                                                <td>
                                                    @if ($price_change->sales_person != null)
                                                        {{ $price_change->sales_person->surname }}
                                                        {{ $price_change->sales_person->first_name }}
                                                        {{ $price_change->sales_person->last_name }}
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    @endforeach

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endcomponent
        </div>
    </div>
</section>

<div class="modal fade" id="blocks_detail_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">@lang('property::lang.property_blocks_details')</h4>
            </div>

            <div class="modal-body">
                <div class="table-responsive" id="block_details_table">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>@lang('property::lang.sold_date')</th>
                                <th>@lang('property::lang.property_name')</th>
                                <th>@lang('property::lang.block_number')</th>
                                <th>@lang('property::lang.size')</th>
                                <th>@lang('property::lang.unit')</th>
                                <th>@lang('property::lang.customer')</th>
                                <th>@lang('property::lang.sale_price')</th>
                                <th>@lang('property::lang.sold_price')</th>
                                <th>@lang('property::lang.sales_officer')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                // dd($price_changes);
                            @endphp
                            @foreach ($price_changes as $price_change)
                                @foreach ($price_change->property_sell_lines as $item)
                                    <tr>
                                        <td>{{ @format_date($price_change->transaction_date) }}</td>
                                        <td>{{ $item->property->name }}</td>
                                        <td>{{ $item->block_number }}</td>
                                        <td>{{ @number_format($item->size) }}</td>
                                        <td>{{ $item->unit }}</td>
                                        <td>{{ $item->block->customer != null ? $item->block->customer->first_name : ''}}</td>
                                        <td>{{ @number_format($item->block->block_sale_price) }}</td>
                                        <td>{{ @number_format($item->block->block_sold_price) }}</td>
                                        <td>
                                            @if ($price_change->sales_person != null)
                                                {{ $price_change->sales_person->surname }}
                                                {{ $price_change->sales_person->first_name }}
                                                {{ $price_change->sales_person->last_name }}
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach

                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="modal-footer">
                <button class="btn btn-primary" onclick="printBlockDetailsTable()"><i class="fa fa-print"></i>
                    Print</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->

    <script>
        function printBlockDetailsTable() {
            var w = window.open('', '_self');
            var html = document.getElementById("block_details_table").innerHTML;
            $(w.document.body).html(html);
            w.print();
            w.close();
            location.reload();
        }
    </script>
