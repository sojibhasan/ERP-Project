@extends('layouts.app')
@section('title', __('property::lang.list_price_changes'))

@section('content')

    <!-- Main content -->
    <section class="content-header">
        <h1>@lang('property::lang.list_price_changes')</h1>
        @include('property::price_changes.partials.list_price_changes')
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
                price_changes_table.ajax.reload();
            }
        );
        $('#date_range').on('cancel.daterangepicker', function(ev, picker) {
            $('#date_range').val('');
            price_changes_table.ajax.reload();
        });

        $(document).ready(function(){
            //property table
            price_changes_table = $('#price_changes_table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '/property/list-price-changes',
                    data: function (d) {
                        if ($('#location_id').length) {
                            d.location_id = $('#location_id').val();
                        }
                        if ($('#project_id').length) {
                            d.project_id = $('#project_id').val();
                        }
                        if ($('#block_id').length) {
                            d.block_id = $('#block_id').val();
                        }
                        if ($('#officer_id').length) {
                            d.officer_id = $('#officer_id').val();
                        }
                        if ($('#sales_commission_status').length) {
                            d.sales_commission_status = $('#sales_commission_status').val();
                        }
                        if ($('#commission_entered_by').length) {
                            d.commission_entered_by = $('#commission_entered_by').val();
                        }
                        if ($('#approved_by').length) {
                            d.approved_by = $('#approved_by').val();
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
                    { data: 'transaction_date', name: 'transaction_date' },
                    { data: 'property_name', name: 'property_name' },
                    { data: 'block_number', name: 'block_number' },
                    { data: 'sale_price', name: 'sale_price' },
                    { data: 'sold_price', name: 'sold_price' },
                    { data: 'changed_amount', name: 'changed_amount' },
                    { data: 'sold_by', name: 'sold_by' },
                    { data: 'commission', name: 'commission' },
                    { data: 'commission_approval', name: 'commission_approval' },
                    { data: 'commission_status', name: 'commission_status' }
                ],
                fnDrawCallback: function (oSettings) {
                    __currency_convert_recursively($('#price_changes_table'));
                },
                createdRow: function (row, data, dataIndex) {
                    // $(row).find('td:eq(5)').attr('class', 'clickable_td');
                },
            });
            $('#date_range, #location_id, #project_id, #block_id, #officer_id, #sales_commission_status, #commission_entered_by, #approved_by').change(function(){
                price_changes_table.ajax.reload();
            })
        })

        $(document).ready(function(){
            var edit_commission_html = '';

            $(body).on('click', '.edit-commission', function(){
                edit_commission_html = $(this).parent().html();
                var commission = $(this).data('commission');
                var property_block_id = $(this).data('property-block-id');
                var html = '<input type="text" class="commission-input" value="'+commission+'"\/><button class="btn btn-sm btn-success update-commission" style="margin-top: 5px;" data-property-block-id="'+property_block_id+'">Update</button>';
                $(this).parent().html(html);
            });

            $(body).on('click', '.update-commission', function(){
                var property_block_id = $(this).data('property-block-id');
                var commission = $(this).siblings('.commission-input').val();
                var confirmation = confirm('Are you sure? The commission amount would be updated to '+commission+'.');

                if(confirmation) {
                    $.post('/property/property-blocks/'+property_block_id+'/update-commission', {sale_commission: commission}, function(result){
                        price_changes_table.ajax.reload();
                    });
                } else {
                    $(this).parent().html(edit_commission_html);
                }
            });

            $(body).on('click', '.approve-commission-btn', function() {
                var property_block_id = $(this).data('property-block-id');
                var confirmation = confirm('Are you sure?');
                if(confirmation) {
                    $.post('/property/property-blocks/'+property_block_id+'/approve-commission', {}, function(result) {
                        price_changes_table.ajax.reload();
                    });
                }
            });

            $(body).on('click', '.commission-status-toggle-btn', function() {
                var property_block_id = $(this).data('property-block-id');
                var confirmation = confirm('Are you sure?');
                if(confirmation) {
                    $.post('/property/property-blocks/'+property_block_id+'/update-commission-status', {}, function(result) {
                        price_changes_table.ajax.reload();
                    });
                }
            })
        });
    </script>

@endsection