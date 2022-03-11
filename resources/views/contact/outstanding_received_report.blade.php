@extends('layouts.app')
@section('title', __('lang_v1.outstanding_received_report'))

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1> @lang('lang_v1.outstanding_received_report')</h1>
</section>

<!-- Main content -->
<section class="content">

    <div class="row">
        <div class="col-md-12">
            @component('components.filters', ['title' => __('report.filters')])
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('ir_customer_id', __('contact.customer') . ':') !!}
                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="fa fa-user"></i>
                        </span>
                        {!! Form::select('ir_customer_id', $customers, null, ['class' => 'form-control select2',
                        'placeholder' => __('lang_v1.all'), 'id' => 'outstanding_customer_id', 'style' => 'width:
                        100%;']); !!}
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('payment_ref_no', __('lang_v1.payment_ref_no') . ':') !!}
                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="fa fa-user"></i>
                        </span>
                        {!! Form::select('payment_ref_no', $payment_ref_nos, null, ['class' => 'form-control select2',
                        'placeholder' => __('lang_v1.all'), 'id' => 'payment_ref_no', 'style' => 'width:
                        100%;']); !!}
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('cheque_number', __('lang_v1.cheque_number') . ':') !!}
                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="fa fa-user"></i>
                        </span>
                        {!! Form::select('cheque_number', $cheque_numbers, null, ['class' => 'form-control select2',
                        'placeholder' => __('lang_v1.all'), 'id' => 'cheque_number', 'style' => 'width:
                        100%;']); !!}
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('payment_type', __('lang_v1.payment_method') . ':') !!}
                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="fa fa-user"></i>
                        </span>
                        {!! Form::select('payment_type', $payment_types, null, ['class' => 'form-control select2',
                        'placeholder' => __('lang_v1.all'), 'id' => 'payment_type', 'style' => 'width:
                        100%;']); !!}
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('outstanding_report_date_filter', __('report.date_range') . ':') !!}
                    {!! Form::text('date_range', null, ['placeholder' => __('lang_v1.select_a_date_range'), 'class' =>
                    'form-control', 'id' => 'outstanding_report_date_filter', 'readonly']); !!}
                </div>
            </div>
            @endcomponent
        </div>
    </div>


    <div class="table-responsive">
        <div class="row">
            <div class="col-md-12">
                @component('components.widget', ['class' => 'box-primary'])
                <div class="table-responsive">
                    <table class="table table-bordered table-striped" id="outstanding_report_table" style="width: 100%">
                        <thead>
                            <tr>
                                <th>@lang('report.payment_received_date')</th>
                                <th>@lang('report.customer')</th>
                                <th>@lang('lang_v1.payment_ref_no')</th>
                              <!--  <th>@lang('report.sale_invoice_pos_date')</th>   
                                <th>@lang('report.bill_amount')</th>
                                <th>@lang('report.received_amount')</th>  -->
                                <th>@lang('lang_v1.payment_method')</th>
                                <th>@lang('lang_v1.payment_amount')</th>
                                <th>@lang('lang_v1.cheque_card_no')</th>
                                <th>Bank</th>
                                <th>User</th>
                            </tr>
                        </thead>
                        <tfoot>
                            <tr class="bg-gray font-17 footer-total text-center">
                                <td><strong>@lang('sale.total'):</strong></td>
                                <td id="footer_payment_status_count"></td>
                                <td><span class="display_currency" id="footer_sale_total"
                                        data-currency_symbol="true"></span></td>
                                <td><span class="display_currency" id="footer_total_paid"
                                        data-currency_symbol="true"></span></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                
                            </tr>
                        </tfoot>
                    </table>
                </div>
                @endcomponent
            </div>
        </div>
    </div>

</section>
<!-- /.content -->

@endsection

@section('javascript')
<script>
    var body = document.getElementsByTagName("body")[0];
        body.className += " sidebar-collapse";
</script>
<script>
    if ($('#outstanding_report_date_filter').length == 1) {
        $('#outstanding_report_date_filter').daterangepicker(dateRangeSettings, function(start, end) {
            $('#outstanding_report_date_filter span').val(
                start.format(moment_date_format) + ' ~ ' + end.format(moment_date_format)
            );
            outstanding_report_table.ajax.reload();
        });
        $('#outstanding_report_date_filter').on('cancel.daterangepicker', function(ev, picker) {
            $('#outstanding_report_date_filter').val('');
            outstanding_report_table.ajax.reload();
        });
    }

    $(document).ready(function(){
        outstanding_report_table = $('#outstanding_report_table').DataTable({
            processing: true,
            serverSide: true,
            aaSorting: [[0, 'desc']],
            "ajax": {
                "url": "{{action('ReportController@getOutstandingReport')}}",
                "data": function ( d ) {
                    if($('#outstanding_report_date_filter').val()) {
                        var start = $('#outstanding_report_date_filter').data('daterangepicker').startDate.format('YYYY-MM-DD');
                        var end = $('#outstanding_report_date_filter').data('daterangepicker').endDate.format('YYYY-MM-DD');
                        d.start_date = start;
                        d.end_date = end;
                        d.bill_no = $('#bill_no').val();
                        d.payment_ref_no = $('#payment_ref_no').val();
                        d.cheque_number = $('#cheque_number').val();
                        d.payment_type = $('#payment_type').val();
                    }
                    d.customer_id = $('#outstanding_customer_id').val();
                }
            },
            columns: [
                { data: 'paid_on', name: 'tp.paid_on'  },
                { data: 'name', name: 'contacts.name'},
              //  { data: 'invoice_no', name: 'transactions.invoice_no'},
                { data: 'payment_ref_no', name: 'tp.payment_ref_no'},
              // { data: 'transaction_date', name: 'transaction_date'  },
                { data: 'final_total', name: 'final_total'},
               /* { data: 'total_paid', name: 'tp.amount'},
                { data: 'method', name: 'tp.method'}, */
                { data: 'payment_amount', name: 'payment_amount'},
                { data: 'cheque_number', name: 'tp.cheque_number'},
                { data: 'payment_amount', name: 'payment_amount'},
                { data: 'cheque_number', name: 'cheque_number'}
            ],
            buttons: [
                {
                    extend: 'csv',
                    text: '<i class="fa fa-file"></i> Export to CSV',
                    className: 'btn btn-default btn-sm',
                    title: 'Outstanding Received Report',
                    exportOptions: {
                        columns: function ( idx, data, node ) {
                            return $(node).is(":visible") && !$(node).hasClass('notexport') ?
                                true : false;
                        } 
                    },
                },
                {
                    extend: 'excel',
                    text: '<i class="fa fa-file-excel-o"></i> Export to Excel',
                    className: 'btn btn-default btn-sm',
                    title: 'Outstanding Received Report',
                    exportOptions: {
                        columns: function ( idx, data, node ) {
                            return $(node).is(":visible") && !$(node).hasClass('notexport') ?
                                true : false;
                        } 
                    },
                },
                {
                    extend: 'colvis',
                    text: '<i class="fa fa-columns"></i> Column Visibility',
                    className: 'btn btn-default btn-sm',
                    title: 'Outstanding Received Report',
                    exportOptions: {
                        columns: function ( idx, data, node ) {
                            return $(node).is(":visible") && !$(node).hasClass('notexport') ?
                                true : false;
                        } 
                    },
                },
                {
                    extend: 'pdf',
                    text: '<i class="fa fa-file-pdf-o"></i> Export to PDF',
                    className: 'btn btn-default btn-sm',
                    title: 'Outstanding Received Report',
                    exportOptions: {
                        columns: function ( idx, data, node ) {
                            return $(node).is(":visible") && !$(node).hasClass('notexport') ?
                                true : false;
                        } 
                    },
                },
                {
                    extend: 'print',
                    text: '<i class="fa fa-print"></i> Print',
                    className: 'btn btn-default btn-sm',
                    title: 'Outstanding Received Report',
                    exportOptions: {
                        columns: function ( idx, data, node ) {
                            return $(node).is(":visible") && !$(node).hasClass('notexport') ?
                                true : false;
                        } 
                    },
                    customize: function (win) {
                        $(win.document.body).find('h1').css('text-align', 'center');
                        $(win.document.body).find('h1').css('font-size', '25px');
                    },
                },
            ],
            "fnDrawCallback": function (oSettings) {

                $('#footer_sale_total').text(sum_table_col($('#outstanding_report_table'), 'final-total'));
                
                $('#footer_total_paid').text(sum_table_col($('#outstanding_report_table'), 'total-paid'));

                $('#footer_total_paid1').text(sum_table_col($('#outstanding_report_table'), 'total-paid'));

                $('#footer_total_remaining').text(sum_table_col($('#outstanding_report_table'), 'payment_due'));

                $('#footer_total_sell_return_due').text(sum_table_col($('#outstanding_report_table'), 'sell_return_due'));

                $('#footer_payment_status_count').html(__sum_status_html($('#outstanding_report_table'), 'payment-status-label'));
                __currency_convert_recursively($('#outstanding_report_table'));
            },
     
        });
    });

    $(document).on('change', '#outstanding_report_date_filter, #outstanding_customer_id, #payment_type, #cheque_number, #payment_ref_no, #bill_no',  function() {
        outstanding_report_table.ajax.reload();
    });
</script>
@endsection