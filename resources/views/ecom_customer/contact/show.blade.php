@extends('layouts.ecom_customer')
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
        <div class="col-md-2">
            <div class="form-group">
                {!! Form::label('business_id', __('customer.business') . ':') !!}
                {!! Form::select('business_id', $businesses, null,['placeholder' => __('lang_v1.please_select'), 'class'
                => 'form-control select', 'id' => 'ledger_business_id']); !!}
            </div>
        </div>
        <div class="col-md-4 col-xs-12">
            <input type="hidden" id="sell_list_filter_customer_id" value="{{$contact->id}}">
            <input type="hidden" id="purchase_list_filter_supplier_id" value="{{$contact->id}}">
        </div>
        <div class="col-md-2 col-xs-12"></div>
        <div class="col-md-4 col-xs-12" style="margin-top: -14px;">
            @if($contact->type == 'customer') <span class="text-red" style="font-size: 36px;">
                @lang('contact.customer'): {{$contact->name}} </span> @endif
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

                    <li class="
                            @if(!empty($view_type) &&  $view_type == 'references')
                                active
                            @else
                                ''
                            @endif">
                        <a href="#references_tab" data-toggle="tab" aria-expanded="true"><i
                                class="fa fa-arrow-circle-up" aria-hidden="true"></i> @lang( 'contact.references')</a>
                    </li>
                    <li class="
                            @if(!empty($view_type) &&  $view_type == 'security_deposit')
                                active
                            @else
                                ''
                            @endif">
                        <a href="#security_deposit_tab" data-toggle="tab" aria-expanded="true"><i class="fa fa-shield"
                                aria-hidden="true"></i> @lang( 'lang_v1.security_deposit')</a>
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
                                @include('ecom_customer.contact.partials.ledger_tab')
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
    $('select[id=ledger_business_id] option:eq(1)').attr('selected', 'selected');
    $(document).ready( function(){

        if ($('#ledger_date_range_new').length == 1) {
        $('#ledger_date_range_new').daterangepicker(dateRangeSettings, function(start, end) {
            $('#ledger_date_range_new span').html(
                start.format('YYYY-MM-DD') + ' ~ ' + end.format('YYYY-MM-DD')
            );
     
        });
        $('#ledger_date_range_new').on('cancel.daterangepicker', function(ev, picker) {
            $('#ledger_date_range_new').html(
                '<i class="fa fa-calendar"></i> ' + LANG.filter_by_date
            );
        });
      
    }
    $('#ledger_date_range_new, #ledger_business_id').change( function(){
        get_contact_ledger();
    });
    get_contact_ledger();

    $('#contact_id').change( function() {
        if ($(this).val()) {
            window.location = "{{url('/customer/details')}}/" + $(this).val();
        }
    });
});

function get_contact_ledger() {

    var start_date = '';
    var end_date = '';
    var transaction_types = $('input.transaction_types:checked').map(function(i, e) {return e.value}).toArray();
    var show_payments = $('input#show_payments').is(':checked');
    var business_id = $('#ledger_business_id').val();

    if($('#ledger_date_range_new').val()) {
        start_date = $('#ledger_date_range_new').data('daterangepicker').startDate.format('YYYY-MM-DD');
        end_date = $('#ledger_date_range_new').data('daterangepicker').endDate.format('YYYY-MM-DD');
    }
    $.ajax({
        url: '/customer/details/ledger?contact_id={{$contact_id}}&start_date=' + start_date + '&transaction_types=' + transaction_types + '&show_payments=' + show_payments + '&end_date=' + end_date+'&business_id=' +business_id,
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

    var url = "{{action('NotificationController@getTemplate', [$contact_id, 'send_ledger'])}}" + '?start_date=' + start_date + '&end_date=' + end_date;

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
    $('#references_date_range').daterangepicker(
        dateRangeSettings,
        function (start, end) {
            $('#references_date_range').val(start.format('YYYY-MM-DD') + ' ~ ' + end.format('YYYY-MM-DD'));
            references_table.ajax.reload();
        }
    );
    //Date range as a button
    $('#references_date_range').on('cancel.daterangepicker', function(ev, picker) {
        $('#references_date_range').val('');
        references_table.ajax.reload();
    });

    $('#references_date_range, #references_payment_status, #references, #ledger_business_id').change(function(){
        references_table.ajax.reload();
    });
    $(document).ready( function(){
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
                        d.business_id = $('#ledger_business_id').val();
                    }
                    // d.is_direct_sale = 0;

                    if($('#references_payment_status').length) {
                        d.payment_status = $('#references_payment_status').val();
                    }
                    if($('#references').length) {
                        d.references = $('#references').val();
                    }
                    d.general_customer_id = "{{auth()->user()->username}}";
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

        security_deposit_table = $('#security_deposit_table').DataTable({
            processing: true,
            serverSide: true,
            aaSorting: [[1, 'desc']],
            "ajax": {
                "url": "{{action('Ecom\ContactController@listSecurityDeposit')}}",
                "data": function ( d ) {
                    d.contact_id = "{{auth()->user()->username}}";
                    d.business_id = $('#ledger_business_id').val();
                }
            },
            columns: [
                { data: 'transaction_date', name: 'transaction_date'  },
                { data: 'final_total', name: 'final_total'},
                { data: 'username', name: 'users.username'},
            ],
            "fnDrawCallback": function (oSettings) {
            
            }
        });
    });
   
</script>
<script src="{{ asset('js/payment.js?v=' . $asset_v) }}"></script>
@if(in_array($contact->type, ['both', 'supplier']))
<script src="{{ asset('js/purchase.js?v=' . $asset_v) }}"></script>
@endif


<script>
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