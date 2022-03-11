<div class="modal-dialog modal-xl no-print" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close no-print" data-dismiss="modal" aria-label="Close"><span
                    aria-hidden="true">&times;</span></button>
            <h4 class="modal-title" id="modalTitle"><b>@lang('contact.statement_no'):</b>
                {{ $statement->statement_no }}
            </h4>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-md-12">

                    <style>
                        @media print {

                            .dt-buttons,
                            .dataTables_length,
                            .dataTables_filter,
                            .dataTables_info,
                            .dataTables_paginate {
                                display: none;
                            }

                            .customer_details_div {
                                display: none;
                            }
                        }
                    </style>
                    <div class="col-md-12">
                        <style>
                            .bg_color {
                                background: #357ca5;
                                font-size: 20px;
                                color: #fff;
                            }

                            .text-center {
                                text-align: center;
                            }

                            #customer_detail_table th {
                                background: #357ca5;
                                color: #fff;
                            }

                            #customer_detail_table>tbody>tr:nth-child(2n+1)>td,
                            #customer_detail_table>tbody>tr:nth-child(2n+1)>th {
                                background-color: rgba(89, 129, 255, 0.3);
                            }
                        </style>

                        @php
                        $currency_precision = !empty($business_details->currency_precision) ?
                        $business_details->currency_precision : 2;
                        @endphp
                        <div class="col-md-12 col-sm-12 @if(!empty($for_pdf)) width-100 text-center @endif">
                            <p class="text-center">
                                <strong>{{$contact->business->name}}</strong><br>{{$location_details->city}},
                                {{$location_details->state}}<br>{!!
                                $location_details->mobile !!}</p>
                            <hr>
                        </div>
                        <div class="col-md-6 col-sm-6 col-xs-6 @if(!empty($for_pdf)) width-50 f-left @endif">
                            <p class="bg_color" style="width: 40%; margin-top: 20px;">@lang('lang_v1.to'):</p>
                            <p><strong>{{$contact->name}}</strong><br> {!! $contact->contact_address !!}
                                @if(!empty($contact->email))
                                <br>@lang('business.email'): {{$contact->email}} @endif
                                <br>@lang('contact.mobile'): {{$contact->mobile}}
                                @if(!empty($contact->tax_number)) <br>@lang('contact.tax_no'): {{$contact->tax_number}}
                                @endif
                            </p>
                        </div>
                        <div
                            class="col-md-6 col-sm-6 col-xs-6 text-right align-right @if(!empty($for_pdf)) width-50 f-left @endif">
                            <p class=" bg_color"
                                style="margin-top: @if(!empty($for_pdf)) 20px @else 0px @endif; font-weight: 500;">
                                @lang('lang_v1.account_summary')</p>
                            <hr>
                            <table
                                class="table table-condensed text-left align-left no-border @if(!empty($for_pdf)) table-pdf @endif"
                                id="customer_detail_table">
                                <tr>
                                    <td>@lang('lang_v1.beginning_balance')</td>
                                    <td>{{@num_format($ledger_details['beginning_balance'])}}
                                    </td>
                                </tr>
                                @if( $contact->type == 'supplier' || $contact->type == 'both')
                                <tr>
                                    <td>@lang('report.total_purchase')</td>
                                    <td>{{@num_format($ledger_details['total_purchase'])}}
                                    </td>
                                </tr>
                                @endif
                                @if( $contact->type == 'customer' || $contact->type == 'both')
                                <tr>
                                    <td>@lang('lang_v1.total_sales')</td>
                                    <td>{{@num_format($ledger_details['total_invoice'])}}
                                    </td>
                                </tr>
                                @endif
                                <tr>
                                    <td>@lang('sale.total_paid')</td>
                                    <td>{{@num_format($ledger_details['total_paid'])}}
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>@lang('lang_v1.balance_due')</strong></td>
                                    <td>{{@num_format($ledger_details['balance_due'])}}
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <div class="row" style="margin-top: 20px;">
                        <div class="col-md-12">
                            <table class="table table-bordered table-striped" id="customer_statement_table">
                                <thead>
                                    <tr>
                                        <th>@lang('contact.date')</th>
                                        <th>@lang('contact.location')</th>
                                        <th>@lang('contact.invoice_no')</th>
                                        <th>@lang('contact.customer_reference')</th>
                                        <th>@lang('contact.voucher_order_no')</th>
                                        <th>@lang('contact.voucher_order_date')</th>
                                        <th>@lang('contact.product')</th>
                                        <th>@lang('contact.unit_price')</th>
                                        <th>@lang('contact.qty')</th>
                                        <th>@lang('contact.invoice_amount')</th>
                                        <th>@lang('contact.due_amount')</th>

                                    </tr>
                                </thead>

                                <tbody>
                                    @foreach ($statement_details as $item)
                                    <tr>
                                        <td>{{$item->date}}</td>
                                        <td>{{$item->location}}</td>
                                        <td>{{$item->invoice_no}}</td>
                                        <td>{{$item->customer_reference}}</td>
                                        <td>{{$item->order_no}}</td>
                                        <td>{{$item->order_date}}</td>
                                        <td>{{$item->product}}</td>
                                        <td>{{@num_format($item->unit_price)}}</td>
                                        <td>{{@format_quantity($item->qty)}}</td>
                                        <td>{{@num_format($item->invoice_amount)}}</td>
                                        <td>{{@num_format($item->due_amount)}}</td>
                                    </tr>
                                    @endforeach

                                </tbody>

                            </table>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>