@extends('layouts.app')
@section('title', __('contact.view_contact'))
@section('content')
<!-- Content Header (Page header) -->
<section class="content-header no-print">
    <h1>{{ __('contact.view_contact') }}</h1>
</section>
<!-- Main content -->
<section class="content no-print">
    <div class="hide print_table_part">
        <style type="text/css">
            .info_col {
                width: 25%;
                float: left;
                padding-left: 10px;
                padding-right: 10px;
            }
            .box {
                border: 0px !important;
            }
        </style>
        <div style="width: 100%;">
            <div class="info_col">
                @include('contact.contact_basic_info')
            </div>
            <div class="info_col">
                @include('contact.contact_more_info')
            </div>
            @if( $contact->type != 'customer')
            <div class="info_col">
                @include('contact.contact_tax_info')
            </div>
            @endif
            <div class="info_col">
                @include('contact.contact_payment_info')
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-4 col-xs-12">
            {!! Form::select('contact_id', $contact_dropdown, $contact->id , ['class' => 'form-control select2', 'id' =>
            'contact_id']); !!}
            <input type="hidden" id="sell_list_filter_customer_id" value="{{$contact->id}}">
            <input type="hidden" id="purchase_list_filter_supplier_id" value="{{$contact->id}}">
        </div>
        <div class="col-md-2 col-xs-12"></div>
        <div class="col-md-4 col-xs-12" style="margin-top: -14px;">
            @if($contact->type == 'customer') <span class="text-red" style="font-size: 36px;"> @lang('contact.customer'): {{$contact->name}} </span> @endif
        </div>
    </div>
    <br>
    <div class="row">
        <div class="col-md-12">
            <div class="nav-tabs-custom">
                <ul class="nav nav-tabs nav-justified">
                    <li class="
                        @if(!empty($view_type) &&  $view_type == 'contact_info')
                            active
                        @else
                            ''
                        @endif">
                        <a href="#contact_info_tab" data-toggle="tab" aria-expanded="true"><i class="fa fa-user"
                                aria-hidden="true"></i> @lang( 'contact.contact_info', ['contact' =>
                            __('contact.contact') ])</a>
                    </li>
                    <li class="
                            @if(!empty($view_type) &&  $view_type == 'ledger')
                                active
                            @else
                                ''
                            @endif">
                        <a href="#ledger_tab" data-toggle="tab" aria-expanded="true"><i class="fa fa-anchor"
                                aria-hidden="true"></i> @lang('lang_v1.ledger')</a>
                    </li>
                    @if(in_array($contact->type, ['both', 'supplier']))
                    <li class="
                            @if(!empty($view_type) &&  $view_type == 'purchase')
                                active
                            @else
                                ''
                            @endif">
                        <a href="#purchases_tab" data-toggle="tab" aria-expanded="true"><i
                                class="fa fa-arrow-circle-down" aria-hidden="true"></i> @lang(
                            'purchase.purchases')</a>
                    </li>
                    <li class="
                            @if(!empty($view_type) &&  $view_type == 'stock_report')
                                active
                            @else
                                ''
                            @endif">
                        <a href="#stock_report_tab" data-toggle="tab" aria-expanded="true"><i
                                class="fa fa-hourglass-half" aria-hidden="true"></i> @lang( 'report.stock_report')</a>
                    </li>
                    @endif
                    @if(in_array($contact->type, ['both', 'customer']))
                    <li class="
                            @if(!empty($view_type) &&  $view_type == 'sales')
                                active
                            @else
                                ''
                            @endif">
                        <a href="#sales_tab" data-toggle="tab" aria-expanded="true"><i class="fa fa-arrow-circle-up"
                                aria-hidden="true"></i> @if($contact->is_property) @lang('sale.sells') @else @lang( 'sale.sells') @endif</a>
                    </li>
                    <li class="
                            @if(!empty($view_type) &&  $view_type == 'references')
                                active
                            @else
                                ''
                            @endif">
                        <a href="#references_tab" data-toggle="tab" aria-expanded="true"><i class="fa fa-arrow-circle-up"
                                aria-hidden="true"></i> @lang( 'contact.references')</a>
                    </li>
                    @endif
                    <li class="
                            @if(!empty($view_type) &&  $view_type == 'security_deposit')
                                active
                            @else
                                ''
                            @endif">
                        <a href="#security_deposit_tab" data-toggle="tab" aria-expanded="true"><i class="fa fa-shield"
                                aria-hidden="true"></i> @lang( 'lang_v1.security_deposit')</a>
                    </li>
                    <li class="
                            @if(!empty($view_type) &&  $view_type == 'documents_and_notes')
                                active
                            @else
                                ''
                            @endif
                            ">
                        <a href="#documents_and_notes_tab" data-toggle="tab" aria-expanded="true"><i
                                class="fa fa-paperclip" aria-hidden="true"></i>
                            @lang('lang_v1.documents_and_notes')</a>
                    </li>
                </ul>
                <div class="tab-content" style="background: #fbfcfc;">
                    <div class="tab-pane
                            @if(!empty($view_type) &&  $view_type == 'contact_info')
                                active
                            @else
                                ''
                            @endif" id="contact_info_tab">
                        @include('contact.partials.contact_info_tab')
                    </div>
                    <div class="tab-pane
                                @if(!empty($view_type) &&  $view_type == 'ledger')
                                    active
                                @else
                                    ''
                                @endif" id="ledger_tab">
                        <div class="row">
                            <div class="col-md-12">
                                @include('contact.partials.ledger_tab')
                            </div>
                        </div>
                    </div>
                    @if(in_array($contact->type, ['both', 'supplier']))
                    <div class="tab-pane
                            @if(!empty($view_type) &&  $view_type == 'purchase')
                                active
                            @else
                                ''
                            @endif" id="purchases_tab">
                            <div class="row">
                                <div class="col-md-12">
                                    @component('components.widget', ['class' => 'box'])
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            {!! Form::label('purchase_list_filter_date_range', __('report.date_range') . ':') !!}
                                            {!! Form::text('purchase_list_filter_date_range', null, ['placeholder' => __('lang_v1.select_a_date_range'), 'class' => 'form-control', 'readonly']); !!}
                                        </div>
                                    </div>
                                    @endcomponent
                                </div>
                            </div>
                        <div class="row">
                            <div class="col-md-12">
                                @component('components.widget', ['class' => 'box'])
                                @include('purchase.partials.purchase_table')
                                @endcomponent
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane 
                            @if(!empty($view_type) &&  $view_type == 'stock_report')
                                active
                            @else
                                ''
                            @endif" id="stock_report_tab">
                        @include('contact.partials.stock_report_tab')
                    </div>
                    @endif
                    @if(in_array($contact->type, ['both', 'customer']))
                    <div class="tab-pane 
                            @if(!empty($view_type) &&  $view_type == 'sales')
                                active
                            @else
                                ''
                            @endif" id="sales_tab">
                        <div class="row">
                            <div class="col-md-12">
                                @if($contact->is_property)
                                @include('contact.partials.property_customer_purchase_table')
                                @else
                                @include('contact.partials.sales_tab')
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane 
                            @if(!empty($view_type) &&  $view_type == 'references')
                                active
                            @else
                                ''
                            @endif" id="references_tab">
                        <div class="row">
                            <div class="col-md-12">
                                @include('contact.partials.references_tab')
                            </div>
                        </div>
                    </div>
                    @endif
                    <div class="tab-pane 
                            @if(!empty($view_type) &&  $view_type == 'security_deposit')
                                active
                            @else
                                ''
                            @endif" id="security_deposit_tab">
                        <div class="row">
                            <div class="col-md-12">
                                @include('contact.partials.security_deposit_tab')
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane
                            @if(!empty($view_type) &&  $view_type == 'documents_and_notes')
                                active
                            @else
                                ''
                            @endif" id="documents_and_notes_tab">
                        <div class="row">
                            <div class="col-md-12">
                                @component('components.widget', ['class' => 'box'])
                                @include('contact.partials.documents_and_notes_tab')
                                @endcomponent
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- /.content -->
<div class="modal fade payment_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
</div>
<div class="modal fade edit_payment_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
</div>
<div class="modal fade pay_contact_due_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel"></div>
@stop
@section('javascript')
<script type="text/javascript">
    $('#purchase_list_filter_date_range').daterangepicker(
        dateRangeSettings,
        function (start, end) {
            $('#purchase_list_filter_date_range').val(start.format(moment_date_format) + ' ~ ' + end.format(moment_date_format));
           purchase_table.ajax.reload();
        }
    );
    $('#purchase_list_filter_date_range').on('cancel.daterangepicker', function(ev, picker) {
        $('#purchase_list_filter_date_range').val('');
        purchase_table.ajax.reload();
    });
    $(document).ready( function(){
    $('#ledger_date_range_new').daterangepicker(
        dateRangeSettings,
        function (start, end) {
            $('#ledger_date_range_new').val(start.format(moment_date_format) + ' ~ ' + end.format(moment_date_format));
        }
    );
    $('#ledger_date_range_new, #ledger_transaction_amount, #ledger_transaction_type').change( function(){
        get_contact_ledger();
    });
    get_contact_ledger();
    rp_log_table = $('#rp_log_table').DataTable({
        processing: true,
        serverSide: true,
        aaSorting: [[0, 'desc']],
        ajax: '/sells?customer_id={{ $contact->id }}&rewards_only=true',
        columns: [
            { data: 'transaction_date', name: 'transactions.transaction_date'  },
            { data: 'invoice_no', name: 'transactions.invoice_no'},
            { data: 'rp_earned', name: 'transactions.rp_earned'},
            { data: 'rp_redeemed', name: 'transactions.rp_redeemed'},
        ]
    });
    supplier_stock_report_table = $('#supplier_stock_report_table').DataTable({
        processing: true,
        serverSide: true,
        'ajax': {
            url: "{{action('ContactController@getSupplierStockReport', [$contact->id])}}",
            data: function (d) {
                d.location_id = $('#sr_location_id').val();
            }
        },
        columns: [
            { data: 'product_name', name: 'p.name'  },
            { data: 'sub_sku', name: 'v.sub_sku'  },
            { data: 'purchase_quantity', name: 'purchase_quantity', searchable: false},
            { data: 'total_quantity_sold', name: 'total_quantity_sold', searchable: false},
            { data: 'total_quantity_returned', name: 'total_quantity_returned', searchable: false},
            { data: 'current_stock', name: 'current_stock', searchable: false},
            { data: 'stock_price', name: 'stock_price', searchable: false}
        ],
        fnDrawCallback: function(oSettings) {
            __currency_convert_recursively($('#supplier_stock_report_table'));
        },
    });
    $('#sr_location_id').change( function() {
        supplier_stock_report_table.ajax.reload();
    });
    $('#contact_id').change( function() {
        if ($(this).val()) {
            window.location = "{{url('/contacts')}}/" + $(this).val();
        }
    });
});
$("input.transaction_types, input#show_payments").on('ifChanged', function (e) {
    get_contact_ledger();
});
$('#ledger_general_search').keyup(function () {
    // Declare variables
    var input, filter, table, tr, td, i, txtValue;
    input = document.getElementById("ledger_general_search");
    filter = input.value.toUpperCase();
    table = document.getElementById("ledger_table");
    tr = table.getElementsByTagName("tr");
    for (i = 0; i < tr.length; i++) {
        n_td = $(tr[i]).children().length;
        for (j = 0; j < n_td; j++) { // loop through all td in tr
            td = tr[i].getElementsByTagName("td")[j];
            if (td) {
                txtValue = td.textContent || td.innerText;
                if (txtValue.toUpperCase().indexOf(filter) > -1) {
                    tr[i].style.display = "";
                    break;
                } else {
                    tr[i].style.display = "none";
                }
            }
        }
    }
})
function get_contact_ledger() {
    var start_date = '';
    var end_date = '';
    var transaction_type = $('select#ledger_transaction_type').val();
    var transaction_amount = $('select#ledger_transaction_amount').val();
    var show_payments = $('input#show_payments').is(':checked');
    if($('#ledger_date_range_new').val()) {
        start_date = $('#ledger_date_range_new').data('daterangepicker').startDate.format('YYYY-MM-DD');
        end_date = $('#ledger_date_range_new').data('daterangepicker').endDate.format('YYYY-MM-DD');
    }
    $.ajax({
        url: '/contacts/ledger?contact_id={{$contact->id}}&start_date=' + start_date + '&transaction_type=' + transaction_type + '&show_payments=' + show_payments + '&end_date=' + end_date+'&transaction_amount='+transaction_amount,
        dataType: 'html',
        success: function(result) {
            $('#contact_ledger_div')
                .html(result);
            __currency_convert_recursively($('#contact_ledger_div'));
            $('#ledger_table').DataTable({
                searching: false,
                ordering:false,
                paging:false,
                dom: 't'
            });
        },
    });
}
$(document).on('click', '#send_ledger', function() {
    var start_date = $('#ledger_date_range_new').data('daterangepicker').startDate.format('YYYY-MM-DD');
    var end_date = $('#ledger_date_range_new').data('daterangepicker').endDate.format('YYYY-MM-DD');
    var url = "{{action('NotificationController@getTemplate', [$contact->id, 'send_ledger'])}}" + '?start_date=' + start_date + '&end_date=' + end_date;
    $.ajax({
        url: url,
        dataType: 'html',
        success: function(result) {
            $('.view_modal')
                .html(result)
                .modal('show');
        },
    });
})
$(document).on('click', '#print_ledger_pdf', function() {
    var start_date = $('#ledger_date_range_new').data('daterangepicker').startDate.format('YYYY-MM-DD');
    var end_date = $('#ledger_date_range_new').data('daterangepicker').endDate.format('YYYY-MM-DD');
    var url = $(this).data('href') + '&start_date=' + start_date + '&end_date=' + end_date;
    window.location = url;
});
</script>
<script>
$(document).ready( function(){
    sell_table = $('#sell_table').DataTable({
        processing: true,
        serverSide: true,
        aaSorting: [[1, 'desc']],
        "ajax": {
            "url": "/sales",
            "data": function ( d ) {
                if($('#sell_list_filter_date_range').val()) {
                    var start = $('#sell_list_filter_date_range').data('daterangepicker').startDate.format('YYYY-MM-DD');
                    var end = $('#sell_list_filter_date_range').data('daterangepicker').endDate.format('YYYY-MM-DD');
                    d.start_date = start;
                    d.end_date = end;
                }
                // d.is_direct_sale = 0;
                if($('#sell_list_filter_location_id').length) {
                    d.location_id = $('#sell_list_filter_location_id').val();
                }
                d.customer_id = $('#sell_list_filter_customer_id').val();
                if($('#sell_list_filter_payment_status').length) {
                    d.payment_status = $('#sell_list_filter_payment_status').val();
                }
                if($('#created_by').length) {
                    d.created_by = $('#created_by').val();
                }
                if($('#sales_cmsn_agnt').length) {
                    d.sales_cmsn_agnt = $('#sales_cmsn_agnt').val();
                }
                if($('#service_staffs').length) {
                    d.service_staffs = $('#service_staffs').val();
                }
                // d = __datatable_ajax_callback(d);
            }
        },
        @if($contact->is_property == 0)
        columnDefs: [
            { 'visible': false, 'targets': 3 },
            { 'visible': false, 'targets': 4 }
        ],
        @endif
        columns: [
            { data: 'action', name: 'action', orderable: false, "searchable": false},
            { data: 'transaction_date', name: 'transaction_date'  },
            { data: 'invoice_no', name: 'invoice_no'},
            @if($contact->is_property == 0)
            { data: 'name', name: 'contacts.name'},
            { data: 'mobile', name: 'contacts.mobile'},
            @endif
            { data: 'business_location', name: 'bl.name'},
            { data: 'payment_status', name: 'payment_status'},
            { data: 'payment_methods', orderable: false, "searchable": false},
            { data: 'final_total', name: 'final_total'},
            { data: 'total_paid', name: 'total_paid', "searchable": false},
            @if($contact->is_property == 0)
            { data: 'total_remaining', name: 'total_remaining'},
            { data: 'return_due', orderable: false, "searchable": false},
            { data: 'shipping_status', name: 'shipping_status'},
            { data: 'total_items', name: 'total_items', "searchable": false},
            { data: 'types_of_service_name', name: 'tos.name', @if(empty($is_types_service_enabled)) visible: false @endif},
            { data: 'service_custom_field_1', name: 'service_custom_field_1', @if(empty($is_types_service_enabled)) visible: false @endif},
            @endif
            { data: 'added_by', name: 'u.first_name'},
            { data: 'additional_notes', name: 'additional_notes'},
            { data: 'staff_note', name: 'staff_note'},
            @if($contact->is_property == 0)
            { data: 'shipping_details', name: 'shipping_details'},
            { data: 'table_name', name: 'tables.name', @if(empty($is_tables_enabled)) visible: false @endif },
            { data: 'waiter', name: 'ss.first_name', @if(empty($is_service_staff_enabled)) visible: false @endif }
            @endif
        ],
        "fnDrawCallback": function (oSettings) {
            $('#footer_sale_total').text(sum_table_col($('#sell_table'), 'final-total'));
            $('#footer_total_paid').text(sum_table_col($('#sell_table'), 'total-paid'));
            $('#footer_total_remaining').text(sum_table_col($('#sell_table'), 'payment_due'));
            $('#footer_total_sell_return_due').text(sum_table_col($('#sell_table'), 'sell_return_due'));
            $('#footer_payment_status_count ').html(__sum_status_html($('#sell_table'), 'payment-status-label'));
            $('#service_type_count').html(__sum_status_html($('#sell_table'), 'service-type-label'));
            $('#payment_method_count').html(__sum_status_html($('#sell_table'), 'payment-method'));
            __currency_convert_recursively($('#sell_table'));
        },
        createdRow: function( row, data, dataIndex ) {
            $( row ).find('td:eq(6)').attr('class', 'clickable_td');
        }
    });
    //Date range as a button
    $('#references_date_range').daterangepicker(
        dateRangeSettings,
        function (start, end) {
            $('#references_date_range').val(start.format(moment_date_format) + ' ~ ' + end.format(moment_date_format));
            references_table.ajax.reload();
        }
    );
    $('#references_date_range').on('cancel.daterangepicker', function(ev, picker) {
        $('#references_date_range').val('');
        references_table.ajax.reload();
    });
    $('#references_date_range, #references_payment_status, #references').change(function(){
        references_table.ajax.reload();
    });
    references_table = $('#references_table').DataTable({
        processing: true,
        serverSide: true,
        aaSorting: [[1, 'desc']],
        "ajax": {
            "url": "/sales",
            "data": function ( d ) {
                if($('#references_date_range').val()) {
                    var start = $('#references_date_range').data('daterangepicker').startDate.format('YYYY-MM-DD');
                    var end = $('#references_date_range').data('daterangepicker').endDate.format('YYYY-MM-DD');
                    d.start_date = start;
                    d.end_date = end;
                }
                // d.is_direct_sale = 0;
                if($('#references_payment_status').length) {
                    d.payment_status = $('#references_payment_status').val();
                }
                if($('#references').length) {
                    d.references = $('#references').val();
                }
                d.customer_id = $('#sell_list_filter_customer_id').val();
                // d = __datatable_ajax_callback(d);
            }
        },
        columnDefs: [
            { 'visible': false, 'targets': 3 },
            { 'visible': false, 'targets': 4 }
        ],
        columns: [
            { data: 'action', name: 'action', orderable: false, "searchable": false},
            { data: 'transaction_date', name: 'transaction_date'  },
            { data: 'invoice_no', name: 'invoice_no'},
            { data: 'name', name: 'contacts.name'},
            { data: 'mobile', name: 'contacts.mobile'},
            { data: 'business_location', name: 'bl.name'},
            { data: 'ref_no', name: 'ref_no'},
            { data: 'payment_status', name: 'payment_status'},
            { data: 'payment_methods', orderable: false, "searchable": false},
            { data: 'final_total', name: 'final_total'},
            { data: 'total_paid', name: 'total_paid', "searchable": false},
            { data: 'total_remaining', name: 'total_remaining'},
            { data: 'return_due', orderable: false, "searchable": false},
            { data: 'shipping_status', name: 'shipping_status'},
            { data: 'total_items', name: 'total_items', "searchable": false},
            { data: 'types_of_service_name', name: 'tos.name', @if(empty($is_types_service_enabled)) visible: false @endif},
            { data: 'service_custom_field_1', name: 'service_custom_field_1', @if(empty($is_types_service_enabled)) visible: false @endif},
            { data: 'added_by', name: 'u.first_name'},
            { data: 'additional_notes', name: 'additional_notes'},
            { data: 'staff_note', name: 'staff_note'},
            { data: 'shipping_details', name: 'shipping_details'},
            { data: 'table_name', name: 'tables.name', @if(empty($is_tables_enabled)) visible: false @endif },
            { data: 'waiter', name: 'ss.first_name', @if(empty($is_service_staff_enabled)) visible: false @endif }
        ],
        "fnDrawCallback": function (oSettings) {
            $('#references_table #footer_sale_total').text(sum_table_col($('#references_table'), 'final-total'));
            $('#references_table #footer_total_paid').text(sum_table_col($('#references_table'), 'total-paid'));
            $('#references_table #footer_total_remaining').text(sum_table_col($('#references_table'), 'payment_due'));
            $('#references_table #footer_total_sell_return_due').text(sum_table_col($('#references_table'), 'sell_return_due'));
            $('#references_table #footer_payment_status_count').html(__sum_status_html($('#references_table'), 'payment-status-label'));
            $('#references_table #service_type_count').html(__sum_status_html($('#references_table'), 'service-type-label'));
            $('#references_table #payment_method_count').html(__sum_status_html($('#references_table'), 'payment-method'));
            __currency_convert_recursively($('#references_table'));
        },
        createdRow: function( row, data, dataIndex ) {
            $( row ).find('td:eq(6)').attr('class', 'clickable_td');
        }
    });
    $('#references').select2();
});
    security_deposit_table = $('#security_deposit_table').DataTable({
        processing: false,
        serverSide: true,
        aaSorting: [[1, 'desc']],
        "ajax": {
            "url": "{{action('ContactController@listSecurityDeposit', $contact->id)}}",
            "data": {}
        },
        columns: [
            { data: 'transaction_date', name: 'transaction_date'  },
            { data: 'description', name: 'description'  },
            // { data: 'final_total', name: 'transactions.final_total'},
            { data: 'credit', name: 'credit'},
            { data: 'debit', name: 'debit'},
            { data: 'username', name: 'users.username'},
        ],
        "fnDrawCallback": function (oSettings) {
            __currency_convert_recursively($('#security_deposit_table'));
        }
    });
</script>
<script src="{{ asset('js/payment.js?v=' . $asset_v) }}"></script>
@if(in_array($contact->type, ['both', 'supplier']))
<script src="{{ asset('js/purchase.js?v=' . $asset_v) }}"></script>
@endif
<script type="text/javascript">
    getDocAndNoteIndexPage();
    setTimeout(() => {
        initializeDocumentAndNoteDataTable();
    }, 200);
    function getDocAndNoteIndexPage() {
    var notable_type = $('#notable_type').val();
    var notable_id = $('#notable_id').val();
    $.ajax({
        method: "GET",
        dataType: "html",
        url: "{{action('DocumentAndNoteController@getDocAndNoteIndexPage')}}",
        async: false,
        data: {'notable_type' : notable_type, 'notable_id' : notable_id},
        success: function(result){
            $('.document_note_body').html(result);
        }
    });
}
function initializeDocumentAndNoteDataTable() {
    documents_and_notes_data_table = $('#documents_and_notes_table').DataTable({
            processing: true,
            serverSide: true,
            ajax:{
                url: '/note-documents',
                data: function(d) {
                    d.notable_id = $('#notable_id').val();
                    d.notable_type = $('#notable_type').val();
                }
            },
            columnDefs: [
                {
                    targets: [0, 2, 4],
                    orderable: false,
                    searchable: false,
                },
            ],
            aaSorting: [[3, 'asc']],
            columns: [
                { data: 'action', name: 'action' },
                { data: 'heading', name: 'heading' },
                { data: 'createdBy'},
                { data: 'created_at', name: 'created_at' },
                { data: 'updated_at', name: 'updated_at' },
            ]
        });
}
</script>
<script>
    if ($('#ledger_date_range_new').length == 1) {
        $('#ledger_date_range_new').daterangepicker(dateRangeSettings, function(start, end) {
            $('#ledger_date_range_new span').html(
                start.format(moment_date_format) + ' ~ ' + end.format(moment_date_format)
            );
        });
        $('#ledger_date_range_new').on('cancel.daterangepicker', function(ev, picker) {
            $('#ledger_date_range_new').html(
                '<i class="fa fa-calendar"></i> ' + LANG.filter_by_date
            );
        });
    }
    //Date range as a button
    $('#sell_list_filter_date_range').daterangepicker(
        dateRangeSettings,
        function (start, end) {
            $('#sell_list_filter_date_range').val(start.format(moment_date_format) + ' ~ ' + end.format(moment_date_format));
            sell_table.ajax.reload();
        }
    );
    $('#sell_list_filter_date_range').on('cancel.daterangepicker', function(ev, picker) {
        $('#sell_list_filter_date_range').val('');
        sell_table.ajax.reload();
    });
    $('#sell_list_filter_date_range, #sell_list_filter_payment_status').change(function(){
        sell_table.ajax.reload();
    });
    $(document).on('click', '#print_btn', function() {
        var start_date = $('#ledger_date_range_new').data('daterangepicker').startDate.format('YYYY-MM-DD');
        var end_date = $('#ledger_date_range_new').data('daterangepicker').endDate.format('YYYY-MM-DD');
        var url = $(this).data('href') + '&start_date=' + start_date + '&end_date=' + end_date;
        $.ajax({
            method: 'get',
			url: url,
			data: {  },
			success: function(result) {
                $('#ledger_print').html(result);
                var divToPrint=document.getElementById('ledger_print');
                var newWin=window.open('','Print-Ledger');
                newWin.document.open();
                newWin.document.write('<html><body onload="window.print()">'+divToPrint.innerHTML+'</body></html>');
                newWin.document.close();
			},
		});
    });
</script>
@endsection