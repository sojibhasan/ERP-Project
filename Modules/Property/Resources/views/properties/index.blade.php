@extends('layouts.app')
@section('title', __('property::lang.properties'))

@section('content')

<!-- Main content -->
<section class="content-header">
   

    <div class="row">
        <div class="col-md-12 dip_tab">
            <div class="settlement_tabs">
                <ul class="nav nav-tabs">
                    <li class=" @if(empty(session('status.tab'))) active @endif" style="margin-left: 20px;">
                        <a style="font-size:13px;" href="#list_properties" class="" data-toggle="tab">
                            <i class="fa fa-superpowers"></i> <strong>@lang('property::lang.list_all_projects')</strong>
                        </a>
                    </li>
                    <li class=" @if(!empty(session('status.tab')) && session('status.tab')=='property_details') active @endif" style="margin-left: 20px;">
                        <a style="font-size:13px;" href="#project_sold_block_details" class="" data-toggle="tab">
                            <i class="fa fa-search-plus"></i> <strong>@lang('property::lang.sold_details')</strong>
                        </a>
                    </li>
                   
                </ul>
            </div>
        </div>
    </div>
    <div class="tab-content">
        <div class="tab-pane @if(empty(session('status.tab'))) active @endif" id="list_properties">
            @include('property::properties.partials.list_properties')
        </div>
        <div class="tab-pane @if(!empty(session('status.tab')) && session('status.tab')=='property_details') active @endif" id="project_sold_block_details">
            @include('property::properties.partials.property_details')
        </div>
       
    </div>



    <div class="modal fade product_modal" tabindex="-1" role="dialog" 
        aria-labelledby="gridSystemModalLabel">
    </div>

    <div class="modal fade payment_modal" tabindex="-1" role="dialog" 
        aria-labelledby="gridSystemModalLabel">
    </div>

    <div class="modal fade edit_payment_modal" tabindex="-1" role="dialog" 
        aria-labelledby="gridSystemModalLabel">
    </div>
    <div class="modal fade block_modal" tabindex="-1" role="dialog" 
        aria-labelledby="gridSystemModalLabel">
    </div>

</section>

<section id="receipt_section" class="print_section"></section>

<!-- /.content -->
@stop
@section('javascript')
<script src="{{ asset('js/payment.js?v=' . $asset_v) }}"></script>
<script>

    var body = document.getElementsByTagName("body")[0];
    body.className += " sidebar-collapse";
    
    //Date range as a button
    $('#date_range').daterangepicker(
        dateRangeSettings,
        function (start, end) {
            $('#date_range').val(start.format(moment_date_format) + ' ~ ' + end.format(moment_date_format));
           property_table.ajax.reload();
        }
    );
    $('#date_range').on('cancel.daterangepicker', function(ev, picker) {
        $('#date_range').val('');
        property_table.ajax.reload();
    });
    //Date range as a button
    $('#property_details_date_range').daterangepicker(
        dateRangeSettings,
        function (start, end) {
            $('#property_details_date_range').val(start.format(moment_date_format) + ' ~ ' + end.format(moment_date_format));
           property_details_table.ajax.reload();
        }
    );
    $('#property_details_date_range').on('cancel.daterangepicker', function(ev, picker) {
        $('#property_details_date_range').val('');
        property_details_table.ajax.reload();
    });

    $(document).on('click', '.update_status', function(e){
        e.preventDefault();
        $('#update_purchase_status_form').find('#status').val($(this).data('status'));
        $('#update_purchase_status_form').find('#purchase_id').val($(this).data('purchase_id'));
        $('#update_purchase_status_modal').modal('show');
    });

    $(document).ready(function(){
        $(".select2").select2();
        //property table
        property_table = $('#property_table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '/property/properties',
                data: function (d) {
                    if ($('#location_id').length) {
                        d.location_id = $('#location_id').val();
                    }
                    if ($('#supplier_id').length) {
                        d.supplier_id = $('#supplier_id').val();
                    }
                    if ($('#property_id').length) {
                        d.property_id = $('#property_id').val();
                    }
                    if ($('#status').length) {
                        d.status = $('#status').val();
                    }

                    var start = '';
                    var end = '';
                    if ($('#date_range').val()) {
                        start = $('input#date_range').data('daterangepicker').startDate.format('YYYY-MM-DD');
                        end = $('input#date_range').data('daterangepicker').endDate.format('YYYY-MM-DD');
                    }
                    d.start_date = start;
                    d.end_date = end;
                },
            },
            aaSorting: [[1, 'desc']],
            columns: [
                { data: 'action', name: 'action', orderable: false, searchable: false },
                { data: 'transaction_date', name: 'transaction_date' },
                { data: 'location_name', name: 'BS.name' },
                { data: 'invoice_no', name: 'invoice_no' },
                { data: 'property_status', name: 'property_status' },
                { data: 'property_name', name: 'properties.name' },
                { data: 'extent', name: 'extent' },
                { data: 'actual_name', name: 'actual_name' },
                { data: 'no_of_blocks', name: 'no_of_blocks' },
                { data: 'added_by', name: 'added_by' },
            ],
            fnDrawCallback: function (oSettings) {
                var total_purchase = sum_table_col($('#property_table'), 'final_total');
                $('#footer_purchase_total').text(total_purchase);

                var total_due = sum_table_col($('#property_table'), 'payment_due');
                $('#footer_total_due').text(total_due);

                $('#footer_status_count').html(__sum_status_html($('#property_table'), 'status-label'));

                $('#footer_payment_status_count').html(
                    __sum_status_html($('#property_table'), 'payment-status-label')
                );

                __currency_convert_recursively($('#property_table'));
            },
            createdRow: function (row, data, dataIndex) {
                // $(row).find('td:eq(5)').attr('class', 'clickable_td');
            },
        });
        $('#date_range, #location_id, #supplier_id, #status, #property_id').change(function(){
            property_table.ajax.reload();
        })
 
        //property_details_table table
        property_details_table = $('#property_details_table').DataTable({
            processing: true,
            serverSide: true, 
            ajax: { 
                url: '/property/property-finalize',
                data: function (d) {
                    if ($('#location_id').length) {
                        d.location_id = $('#location_id').val();
                    }
                    if ($('#supplier_id').length) {
                        d.supplier_id = $('#supplier_id').val();
                    }
                    if ($('#property_id').length) {
                        d.property_id = $('#property_id').val();
                    }
                    if ($('#status').length) {
                        d.status = $('#status').val();
                    }

                    var start = '';
                    var end = '';
                    if ($('#property_details_date_range').val()) {
                        start = $('input#property_details_date_range')
                            .data('daterangepicker')
                            .startDate.format('YYYY-MM-DD');
                        end = $('input#property_details_date_range')
                            .data('daterangepicker')
                            .endDate.format('YYYY-MM-DD');
                    }
                    d.start_date = start;
                    d.end_date = end;
                },
            },
            aaSorting: [[1, 'desc']],
            columns: [
                { data: 'action', name: 'action', orderable: false, searchable: false },
                { data: 'transaction_date', name: 'transaction_date' },
                { data: 'property_name', name: 'properties.name' },
                { data: 'block_number', name: 'block_number' },
                { data: 'block_extent', name: 'block_extent' },
                { data: 'unit_name', name: 'units.actual_name' },
                { data: 'block_sale_price', name: 'block_sale_price' },
                { data: 'block_sold_price', name: 'block_sold_price' },
                { data: 'customer_name', name: 'contacts.name' },
                { data: 'finance_option', name: 'finance_options.finance_option' },
                { data: 'reservation_amount', name: 'reservation_amount' },
                { data: 'down_payment', name: 'down_payment' },
                { data: 'easy_payment', name: 'easy_payment' },
                { data: 'no_of_installment', name: 'no_of_installment' },
                { data: 'installment_amount', name: 'installment_amount', orderable: false, searchable: false },
                { data: 'first_installment_date', name: 'first_installment_date' },
                { data: 'installment_cycle', name: 'installment_cycles.name' },
                { data: 'final_total', name: 'final_total', orderable: false, searchable: false },
                { data: 'loan_capital', name: 'loan_capital', orderable: false, searchable: false },
                { data: 'total_interest', name: 'total_interest', orderable: false, searchable: false },
            ],
            fnDrawCallback: function (oSettings) {
                __currency_convert_recursively($('#property_details_table'));
            },
            createdRow: function (row, data, dataIndex) {
                // $(row).find('td:eq(5)').attr('class', 'clickable_td');
            },
        });
        $('#property_details_date_range, #property_details_location_id, #property_details_customer_id, #property_details_property_id, #property_details_block_id, #property_details_finance_option_id, #property_details_easy_payment, #property_details_installment_cycle_id').change(function(){
            property_details_table.ajax.reload();
        })
    })

    $(document).on('submit', '#update_purchase_status_form', function(e){
        e.preventDefault();
        $(this)
            .find('button[type="submit"]')
            .attr('disabled', true);
        var data = $(this).serialize();

        $.ajax({
            method: 'POST',
            url: $(this).attr('action'),
            dataType: 'json',
            data: data,
            success: function(result) {
                if (result.success == true) {
                    $('#update_purchase_status_modal').modal('hide');
                    toastr.success(result.msg);
                    property_table.ajax.reload();
                    $('#update_purchase_status_form')
                        .find('button[type="submit"]')
                        .attr('disabled', false);
                } else {
                    toastr.error(result.msg);
                }
            },
        });
    });
</script>
    
@endsection 