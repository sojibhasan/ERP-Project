<div class="row">
    <div class="col-md-12">
        @component('components.widget', ['class' => 'box-primary', 'title' => __('report.summary')])
        @slot('tool')
        <div class="box-tools">
            <div class="btn-group">
                <button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown"
                    aria-expanded="false"><i class="fa fa-print"></i> @lang('messages.print')
                    <span class="caret"></span><span class="sr-only">Toggle Dropdown
                    </span>
                </button>
                <ul class="dropdown-menu dropdown-menu-right" role="menu">
                    <li><a href="#" onclick="printSSummary()">@lang('report.summary')</a></li>
                    <li><a href="#" onclick="printSDiv()">@lang('report.summary_and_details')</a></li>
                </ul>
            </div>
        </div>
        @endslot
        <div id="sell_summary_div">
            <style>
                @media print {
                    .dataTables_length {
                        display: none;
                    }

                    .dt-buttons {
                        display: none;
                    }

                    .dataTables_filter,
                    .dataTables_info,
                    .dataTables_paginate {
                        display: none;
                    }

                    table.dataTable thead .sorting:after {
                        display: none;
                    }

                    table {
                        page-break-inside: auto
                    }

                    tr {
                        page-break-inside: avoid;
                        page-break-after: auto
                    }

                    thead {
                        display: table-header-group
                    }

                    tfoot {
                        display: table-footer-group
                    }

                    .col-print-1 {
                        width: 8%;
                        float: left;
                    }

                    .col-print-2 {
                        width: 16%;
                        float: left;
                    }

                    .col-print-3 {
                        width: 25%;
                        float: left;
                    }

                    .col-print-4 {
                        width: 33%;
                        float: left;
                    }

                    .col-print-5 {
                        width: 42%;
                        float: left;
                    }

                    .col-print-6 {
                        width: 50%;
                        float: left;
                    }

                    .col-print-7 {
                        width: 58%;
                        float: left;
                    }

                    .col-print-8 {
                        width: 66%;
                        float: left;
                    }

                    .col-print-9 {
                        width: 75%;
                        float: left;
                    }

                    .col-print-10 {
                        width: 83%;
                        float: left;
                    }

                    .col-print-11 {
                        width: 92%;
                        float: left;
                    }

                    .col-print-12 {
                        width: 100%;
                        float: left;
                    }

                }
            </style>
            <div class="row">
                <div class="col-md-6 col-print-6">
                    <div class="row">
                        <div class="col-md-6 col-print-6">
                            <h4>@lang('report.period_form'): <span class="sell_period_from"></span></h4>
                        </div>
                        <div class="col-md-6 col-print-6">
                            <h4>@lang('report.period_to'): <span class="sell_period_to"></span></h4>
                        </div>
                    </div>
                </div>
                <div class="@if(request()->segment(2) != 'product-transaction-report') col-md-12 col-print-12 @else col-md-6 col-print-6 @endif">
                    <div class="row">
                        <div class="col-md-3 col-print-3 text-center">
                            <h4 style="line-height: 5px;">@lang('report.category')</h4><br>
                            <span class="sell_category">@lang('messages.all')</span>
                        </div>
                        <div class="col-md-3 col-print-3 text-center">
                            <h4 style="line-height: 5px;">@lang('report.sub_category')</h4><br>
                            <span class="sell_sub_category">@lang('messages.all')</span>
                        </div>
                        <div class="col-md-3 col-print-3 text-center">
                            <h4 style="line-height: 5px;">@lang('report.customer')</h4><br>
                            <span class="sell_customer">@lang('messages.all')</span>
                        </div>
                        <div class="col-md-3 col-print-3 text-center">
                            <h4 style="line-height: 5px;">@lang('report.business')</h4><br>
                            <span class="sell_location">@lang('messages.all')</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="clearfix"></div>
            <hr>
            <div class="row">
                <div class="col-md-4 col-print-3">
                    @lang('report.total_qty_sell'): <span class="sell_total_qty_sell"></span><br>
                    @lang('report.total_qty_sell_value'): <span class="sell_total_qty_sell_value"></span>
                </div>
            </div>
            <div class="clearfix"></div>
        </div>
        @endcomponent
    </div>
</div>