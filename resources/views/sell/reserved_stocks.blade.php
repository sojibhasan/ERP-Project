@extends('layouts.app')
@section('title', __( 'lang_v1.all_reserved_stocks'))

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header no-print">
    <h1>@lang( 'lang_v1.reserved_stocks')
    </h1>
</section>

<!-- Main content -->
<section class="content no-print">
    @component('components.filters', ['title' => __('report.filters')])
    <div class="col-md-3">
        <div class="form-group">
            {!! Form::label('sell_list_filter_location_id', __('purchase.business_location') . ':') !!}

            {!! Form::select('sell_list_filter_location_id', $business_locations, null, ['class' => 'form-control
            select2',
            'style' => 'width:100%', 'placeholder' => __('lang_v1.all') ]); !!}
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            {!! Form::label('sell_list_filter_customer_id', __('contact.customer') . ':') !!}
            {!! Form::select('sell_list_filter_customer_id', $customers, null, ['class' => 'form-control select2',
            'style'
            => 'width:100%', 'placeholder' => __('lang_v1.all')]); !!}
        </div>
    </div>

    <div class="col-md-3">
        <div class="form-group">
            {!! Form::label('sell_list_filter_date_range', __('report.date_range') . ':') !!}
            {!! Form::text('sell_list_filter_date_range', null, ['placeholder' => __('lang_v1.select_a_date_range'),
            'class'
            => 'form-control', 'readonly']); !!}
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            {!! Form::label('created_by', __('report.user') . ':') !!}
            {!! Form::select('created_by', $sales_representative, null, ['class' => 'form-control select2', 'style' =>
            'width:100%']); !!}
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            {!! Form::label('sell_list_filter_invoice_no', __('lang_v1.invoice_no') . ':') !!}
            {!! Form::select('sell_list_filter_invoice_no', $invoice_nos, null, ['class' => 'form-control
            select2', 'style' => 'width:100%', 'placeholder' => __('lang_v1.all')]); !!}
        </div>
    </div>

    @if($is_woocommerce)
    <div class="col-md-4">
        <div class="form-group">
            <div class="checkbox">
                <label>
                    {!! Form::checkbox('only_woocommerce_sells', 1, false,
                    [ 'class' => 'input-icheck', 'id' => 'synced_from_woocommerce']); !!}
                    {{ __('lang_v1.synced_from_woocommerce') }}
                </label>
            </div>
        </div>
    </div>
    @endif
    @endcomponent
    @component('components.widget', ['class' => 'box-primary', 'title' => __( 'lang_v1.all_reserved_stocks'), 'date'=>''])
    @can('sell.create')
    @slot('tool')
    <div class="box-tools">
        <a class="btn btn-block btn-primary" href="{{action('SellController@create')}}">
            <i class="fa fa-plus"></i> @lang('messages.add')</a>
    </div>
    @endslot
    @endcan
    @if(auth()->user()->can('direct_sell.access') || auth()->user()->can('view_own_sell_only'))
    <div class="table-responsive">
        <table class="table table-bordered table-striped table-responsive ajax_view" id="sell_table">
            <thead>
                <tr>
                    <th>@lang('messages.action')</th>
                    <th>@lang('messages.date')</th>
                    <th>@lang('sale.invoice_no')</th>
                    <th>@lang('sale.customer_name')</th>
                    <th>@lang('sale.location')</th>
                    <th>@lang('lang_v1.status')</th>
                </tr>
            </thead>
            <tfoot>

            </tfoot>
        </table>
    </div>
    @endif
    @endcomponent
</section>
<!-- /.content -->
<div class="modal fade payment_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
</div>

<div class="modal fade edit_payment_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
</div>

<!-- This will be printed -->
<!-- <section class="invoice print_section" id="receipt_section">
</section> -->

@stop

@section('javascript')
<script type="text/javascript">
    $(document).ready( function(){
    //Date range as a button
    $('#sell_list_filter_date_range').daterangepicker(
        dateRangeSettings,
        function (start, end) {
            $('#sell_list_filter_date_range').val(start.format(moment_date_format) + ' ~ ' + end.format(moment_date_format));
            $("#report_date_range").text("Date Range: "+ $("#sell_list_filter_date_range").val());
            sell_table.ajax.reload();
        }
    );
    $('#sell_list_filter_date_range').on('cancel.daterangepicker', function(ev, picker) {
        $('#sell_list_filter_date_range').val('');
        sell_table.ajax.reload();
        $("#report_date_range").text("Date Range: - ");
    });
    var buttons = [
        {
            extend: 'csv',
            text: '<i class="fa fa-file-text-o" aria-hidden="true"></i> ' + LANG.export_to_csv,
            className: 'btn-sm',
            exportOptions: { columns: ':visible:not(:eq(0))' },
            footer: true,
        },
        {
            extend: 'excel',
            text: '<i class="fa fa-file-excel-o" aria-hidden="true"></i> ' + LANG.export_to_excel,
            className: 'btn-sm',
            exportOptions: { columns: ':visible:not(:eq(0))'  },
            footer: true,
        },
        {
            extend: 'print',
            text: '<i class="fa fa-print" aria-hidden="true"></i> ' + LANG.print,
            className: 'btn-sm',
            exportOptions: { columns: ':visible:not(:eq(0))', stripHtml: true },
            footer: true,
            customize: function (win) {
                if ($('.print_table_part').length > 0) {
                    $($('.print_table_part').html()).insertBefore(
                        $(win.document.body).find('table')
                    );
                }
                __currency_convert_recursively($(win.document.body).find('table'));
            },
        },
        {
            extend: 'colvis',
            text: '<i class="fa fa-columns" aria-hidden="true"></i> ' + LANG.col_vis,
            className: 'btn-sm',
        },{
            extend: 'pdf',
            text: '<i class="fa fa-file-pdf-o" aria-hidden="true"></i> ' + LANG.export_to_pdf,
            className: 'btn-sm',
            exportOptions: { columns: ':visible:not(:eq(0))' },
            footer: true,
        }
    ];
    sell_table = $('#sell_table').DataTable({
        buttons,
        processing: true,
        serverSide: true,
        aaSorting: [[1, 'desc']],
        "ajax": {
            "url": "/reserved-stocks",
            "data": function ( d ) {
                if($('#sell_list_filter_date_range').val()) {
                    var start = $('#sell_list_filter_date_range').data('daterangepicker').startDate.format('YYYY-MM-DD');
                    var end = $('#sell_list_filter_date_range').data('daterangepicker').endDate.format('YYYY-MM-DD');
                    d.start_date = start;
                    d.end_date = end;
                }
                d.is_direct_sale = 1;

                d.location_id = $('#sell_list_filter_location_id').val();
                d.customer_id = $('#sell_list_filter_customer_id').val();
                d.payment_status = $('#sell_list_filter_payment_status').val();
                d.invoice_no = $('#sell_list_filter_invoice_no').val();
                d.created_by = $('#created_by').val();
                d.sales_cmsn_agnt = $('#sales_cmsn_agnt').val();
                d.service_staffs = $('#service_staffs').val();
                
                @if($is_woocommerce)
                    if($('#synced_from_woocommerce').is(':checked')) {
                        d.only_woocommerce_sells = 1;
                    }
                @endif
            }
        },
        columns: [
            { data: 'action', name: 'action', orderable: false, "searchable": false},
            { data: 'transaction_date', name: 'transaction_date'  },
            { data: 'invoice_no', name: 'invoice_no'},
            { data: 'name', name: 'contacts.name'},
            { data: 'business_location', name: 'bl.name'},
            { data: 'status', name: 'status'},
        ],
        "fnDrawCallback": function (oSettings) {

       

        },
        createdRow: function( row, data, dataIndex ) {
            $( row ).find('td:eq(6)').attr('class', 'clickable_td');
        }
    });

    $(document).on('change', '#sell_list_filter_location_id, #sell_list_filter_customer_id, #sell_list_filter_payment_status, #sell_list_filter_invoice_no, #created_by, #sales_cmsn_agnt, #service_staffs',  function() {
        sell_table.ajax.reload();
    });
    @if($is_woocommerce)
        $('#synced_from_woocommerce').on('ifChanged', function(event){
            sell_table.ajax.reload();
        });
    @endif
});
</script>
<script src="{{ asset('js/payment.js?v=' . $asset_v) }}"></script>
@endsection