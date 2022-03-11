@extends('layouts.app')
@section('title', __('purchase.purchases'))

@section('content')
<section class="content">
    <div class="row">
        <div class="col-md-12">
            <div class="settlement_tabs">
                <ul class="nav nav-tabs">
                    @can('property.purchase.view')
                    <li class="active">
                        <a href="#purchases_list" data-toggle="tab">
                            <i class="fa fa-list"></i> <strong>@lang('property::lang.list_property_purchases')</strong>
                        </a>
                    </li>
                    @endcan

                    @can('property.purchase.create')
                    <li class="">
                        <a href="#add_purchase" data-toggle="tab">
                            <i class="fa fa-plus"></i> <strong>
                                @lang('property::lang.add_property') </strong>
                        </a>
                    </li>
                    @endcan
                </ul>
                <div class="tab-content">
                    @can('property.purchase.view')
                    <div class="tab-pane active" id="purchases_list">
                        @include('property::purchase.purchase_list')
                    </div>
                    @endcan

                    @can('property.purchase.create')
                    <div class="tab-pane" id="add_purchase">
                        @include('property::purchase.create')
                    </div>
                    @endcan
                </div>
            </div>
        </div>
    </div>
</section>
<section id="receipt_section" class="print_section"></section>
<!-- quick product modal -->
<div class="modal fade quick_add_product_modal" tabindex="-1" role="dialog" aria-labelledby="modalTitle"></div>
<div class="modal fade contact_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
    @include('contact.create', ['quick_add' => true])
</div>

<!-- /.content -->
@stop
@section('javascript')
<script src="{{ url('Modules/Property/Resources/assets/js/app.js') }}"></script>
<script src="{{ asset('js/payment.js?v=' . $asset_v) }}"></script>
@include('purchase.partials.keyboard_shortcuts')
<script>
    var body = document.getElementsByTagName("body")[0];
    body.className += " sidebar-collapse";
    $('#location_id option:eq(1)').attr('selected', true);
    $('.deed_date').datepicker('setDate', new Date());
    //Date range as a button
        //Date range as a button
    $('#date_range').daterangepicker(
        dateRangeSettings,
        function (start, end) {
            $('#date_range').val(start.format(moment_date_format) + ' ~ ' + end.format(moment_date_format));
           purchase_table.ajax.reload();
        }
    );
    $('#date_range').on('cancel.daterangepicker', function(ev, picker) {
        $('#date_range').val('');
        purchase_table.ajax.reload();
    });

    $(document).on('click', '.update_status', function(e){
        e.preventDefault();
        $('#update_purchase_status_form').find('#status').val($(this).data('status'));
        $('#update_purchase_status_form').find('#purchase_id').val($(this).data('purchase_id'));
        $('#update_purchase_status_modal').modal('show');
    });

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
                    purchase_table.ajax.reload();
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


<script>
  $(document).ready(function(){
        //purchase table
        purchase_table = $('#purchase_table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '/property/purchases',
                data: function (d) {
                    if ($('#location_id').length) {
                        d.location_id = $('#location_id').val();
                    }
                    if ($('#supplier_id').length) {
                        d.supplier_id = $('#supplier_id').val();
                    }
                    if ($('#payment_status').length) {
                        d.payment_status = $('#payment_status').val();
                    }
                    if ($('#status').length) {
                        d.status = $('#status').val();
                    }

                    var start = '';
                    var end = '';
                    if ($('#date_range').val()) {
                        start = $('input#date_range')
                            .data('daterangepicker')
                            .startDate.format('YYYY-MM-DD');
                        end = $('input#date_range')
                            .data('daterangepicker')
                            .endDate.format('YYYY-MM-DD');
                    }
                    d.start_date = start;
                    d.end_date = end;

                    // d = __datatable_ajax_callback(d);
                },
            },
            aaSorting: [[1, 'desc']],
            columns: [
                { data: 'action', name: 'action', orderable: false, searchable: false },
                { data: 'transaction_date', name: 'transaction_date' },
                { data: 'location_name', name: 'BS.name' },
                { data: 'invoice_no', name: 'invoice_no' },
                { data: 'supplier_name', name: 'supplier_name' },
                { data: 'deed_no', name: 'deed_no' },
                { data: 'property_status', name: 'property_status' },
                { data: 'property_name', name: 'property_name' },
                { data: 'extent', name: 'extent' },
                { data: 'actual_name', name: 'actual_name' },
                { data: 'final_total', name: 'final_total' },
                { data: 'pay_terms', name: 'pay_terms' },
                { data: 'payment_status', name: 'payment_status' },
                { data: 'payment_due', name: 'payment_due', orderable: false, searchable: false },
                { data: 'payment_method', name: 'payment_method', orderable: false, searchable: false },
                { data: 'added_by', name: 'u.first_name' },
            ],
            fnDrawCallback: function (oSettings) {
                var total_purchase = sum_table_col($('#purchase_table'), 'final_total');
                $('#footer_purchase_total').text(total_purchase);

                var total_due = sum_table_col($('#purchase_table'), 'payment_due');
                $('#footer_total_due').text(total_due);

                $('#footer_status_count').html(__sum_status_html($('#purchase_table'), 'status-label'));

                $('#footer_payment_status_count').html(
                    __sum_status_html($('#purchase_table'), 'payment-status-label')
                );

                __currency_convert_recursively($('#purchase_table'));
            },
            createdRow: function (row, data, dataIndex) {
                $(row).find('td:eq(5)').attr('class', 'clickable_td');
            },
        });
        $('#date_range, #location_id, #supplier_id, #status, #payment_status').change(function(){
            purchase_table.ajax.reload();
        })
    })


    
    $(document).on('click', 'a.delete-purchase', function (e) {
        e.preventDefault();
        swal({
            title: LANG.sure,
            icon: 'warning',
            buttons: true,
            dangerMode: true,
        }).then((willDelete) => {
            if (willDelete) {
                var href = $(this).data('href');
                $.ajax({
                    method: 'DELETE',
                    url: href,
                    dataType: 'json',
                    success: function (result) {
                        if (result.success == true) {
                            toastr.success(result.msg);
                            purchase_table.ajax.reload();
                        } else {
                            toastr.error(result.msg);
                        }
                    },
                });
            }
        });
    });

    $(document).on('change', '#final_total', function (e) {
        $('#amount_0').val($(this).val()).trigger('change')
    });
    $(document).ready( function(){
        $('#location_id_add option:eq(1)').attr('selected', true).trigger('change');
    });
</script>

@endsection