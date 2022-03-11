@extends('layouts.app')
@section('title', __('property::lang.easy_payment'))

@section('content')

<!-- Main content -->
<section class="content-header">
   

    <div class="row">
        <div class="col-md-12 dip_tab">
            <div class="settlement_tabs">
                <ul class="nav nav-tabs">
                    <li class=" @if(empty(session('status.tab'))) active @endif" style="margin-left: 20px;">
                        <a style="font-size:13px;" href="#easy_payment_details" class="" data-toggle="tab">
                            <i class="fa fa-superpowers"></i> <strong>@lang('property::lang.easy_payment_details')</strong>
                        </a>
                    </li>
                    <li class=" @if(!empty(session('status.tab')) && session('status.tab')=='aging_report') active @endif" style="margin-left: 20px;">
                        <a style="font-size:13px;" href="#aging_report" class="" data-toggle="tab">
                            <i class="fa fa-search-plus"></i> <strong>@lang('property::lang.aging_report')</strong>
                        </a>
                    </li>
                   
                </ul>
            </div>
        </div>
    </div>
    <div class="tab-content">
        <div class="tab-pane @if(empty(session('status.tab'))) active @endif" id="easy_payment_details">
            @include('property::easy_payments.partials.easy_payment_details')
        </div>
        <div class="tab-pane @if(!empty(session('status.tab')) && session('status.tab')=='aging_report') active @endif" id="aging_report">
            @include('property::easy_payments.partials.aging_report')
        </div>
       
    </div>

</section>

<section id="receipt_section" class="print_section"></section>

<!-- /.content -->
@stop
@section('javascript')
<script>
    // var body = document.getElementsByTagName("body")[0];
    // body.className += " sidebar-collapse";

    //Date range as a button
    $('#date_range').daterangepicker(
        dateRangeSettings,
        function (start, end) {
            $('#date_range').val(start.format(moment_date_format) + ' ~ ' + end.format(moment_date_format));
            easy_payment_details_table.ajax.reload();
        }
    );
    $('#date_range').on('cancel.daterangepicker', function(ev, picker) {
        $('#date_range').val('');
        easy_payment_details_table.ajax.reload();
    });

    $(document).ready(function(){
        //property table
        easy_payment_details_table = $('#easy_payment_details_table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '/property/easy-payments',
                data: function (d) {
                    if ($('#location_id').length) {
                        d.location_id = $('#location_id').val();
                    }
                    if ($('#customer_id').length) {
                        d.customer_id = $('#customer_id').val();
                    }
                    if ($('#project_id').length) {
                        d.project_id = $('#project_id').val();
                    }
                        d.show_only_penalty = $('#show_only_penalty').is(':checked');
                        d.show_only_balance_due = $('#show_only_balance_due').is(':checked');
                   

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
                },
            },
            aaSorting: [[1, 'desc']],
            @include('layouts.partials.datatable_export_button')
            columns: [
                { data: 'action', name: 'action', orderable: false, searchable: false },
                { data: 'first_installment_date', name: 'first_installment_date' },
                { data: 'location_name', name: 'BS.name' },
                { data: 'customer_name', name: 'contacts.name' },
                { data: 'property_name', name: 'properties.name' },
                { data: 'installment_amount', name: 'installment_amount' },
                { data: 'loan_capital', name: 'loan_capital' },
                { data: 'total_interest', name: 'total_interest' },
                { data: 'penalty', name: 'penalty' },
                { data: 'paid_amount', name: 'paid_amount' },
                { data: 'paid_on', name: 'paid_on' },
                { data: 'balance_due', name: 'balance_due' },
            ],
            fnDrawCallback: function (oSettings) {
                $('#footer_total_due').html(
                    __sum_status_html($('#easy_payment_details_table'), 'balance_due')
                );

                __currency_convert_recursively($('#easy_payment_details_table'));
            },
            rowCallback: function( row, data, index ) {
                if ($('#show_only_balance_due').is(':checked')) {
                    let due = parseFloat(data.due);
                    if( due > 0){
                        $(row).hide();
                    }
                }
            },
            createdRow: function (row, data, dataIndex) {
            },
        });
        $('#date_range, #location_id, #customer_id, #project_id, #show_only_penalty, #show_only_balance_due').change(function(){
            easy_payment_details_table.ajax.reload();
        })
        $('#show_only_penalty').on('ifChecked', function(event){
            easy_payment_details_table.ajax.reload();
        })
        $('#show_only_balance_due').on('ifChecked', function(event){
            easy_payment_details_table.ajax.reload();
        })
        $('#show_only_penalty').on('ifUnchecked', function(event){
            easy_payment_details_table.ajax.reload();
        })
        $('#show_only_balance_due').on('ifUnchecked', function(event){
            easy_payment_details_table.ajax.reload();
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
                    easy_payment_details_table.ajax.reload();
                    $('#update_purchase_status_form')
                        .find('button[type="submit"]')
                        .attr('disabled', false);
                } else {
                    toastr.error(result.msg);
                }
            },
        });
    });

    $(document).on('click', 'a.delete-penalty', function (e) {
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


     //Date range as a button
     if ($('#aging_report_date_filter').length == 1) {
        $('#aging_report_date_filter').daterangepicker(dateRangeSettings, function(start, end) {
            $('#aging_report_date_filter span').val(
                start.format(moment_date_format) + ' ~ ' + end.format(moment_date_format)
            );
            aging_report_table.ajax.reload();
        });
        $('#aging_report_date_filter').on('cancel.daterangepicker', function(ev, picker) {
            $('#aging_report_date_filter').val('');
            aging_report_table.ajax.reload();
        });
    }
    $(document).ready(function(){
        aging_report_table = $('#aging_report_table').DataTable({
            processing: true,
            serverSide: true,
            "ajax": {
                "url": "/property/easy-payments/aging-report",
                "data": function ( d ) {
                    if($('#aging_report_date_filter').val()) {
                        var start = $('#aging_report_date_filter').data('daterangepicker').startDate.format('YYYY-MM-DD');
                        var end = $('#aging_report_date_filter').data('daterangepicker').endDate.format('YYYY-MM-DD');
                        d.start_date = start;
                        d.end_date = end;
                    }
                    d.customer_id = $('#aging_customer_id').val();
                    d.no_of_days_over = $('#no_of_days_over').val();
                }
            },
            @include('layouts.partials.datatable_export_button')
            columns: [
                { data: 'due_date', name: 'due_date'  },
                { data: 'name', name: 'contacts.name'},
                { data: 'due_amount', name: 'due_amount'},
                { data: '1_30_days', name: '1_30_days'},
                { data: '31_45_days', name: '31_45_days'},
                { data: '46_60_days', name: '46_60_days'},
                { data: '61_90_days', name: '61_90_days'},
                { data: 'over_90_days', name: 'over_90_days'},
                { data: 'action', name: 'action', orderable: false, searchable: false }
            ],
            "fnDrawCallback": function (oSettings) {
                $('#footer_total_amount_aging').text(sum_table_col($('#aging_report_table'), 'final-total-aging'));
                __currency_convert_recursively($('#aging_report_table'))
            },
            rowCallback: function( row, data, index ) {
            var no_of_days_over = $('#no_of_days_over').val();
            
            if(no_of_days_over != ''){
                if (parseInt(data['days_over']) <= parseInt(no_of_days_over)) {
                    $(row).hide();
                }

            }
            },
        
            
        });
    })

    $(document).on('change', '#aging_report_date_filter, #aging_customer_id, #no_of_days_over',  function() {
        aging_report_table.ajax.reload();
    });
</script>
	
@endsection