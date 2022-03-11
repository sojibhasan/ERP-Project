@extends('layouts.app')
@section('title', __('fleet::lang.settings'))

<style>
    .select2 {
        width: 100% !important;
    }
</style>
@section('content')

<section class="content-header">
    <div class="row">
        <div class="col-md-12 dip_tab">
            <div class="settlement_tabs">
                <ul class="nav nav-tabs">
                    <li class="@if(empty(session('status.tab'))) active @endif" style="margin-left: 20px;">
                        <a style="font-size:13px;" href="#routes" class="" data-toggle="tab">
                            <i class="fa fa-superpowers"></i> <strong>@lang('fleet::lang.routes')</strong>
                        </a>
                    </li>
                    <li class=" @if(session('status.tab') == 'drivers') active @endif">
                        <a style="font-size:13px;" href="#drivers" data-toggle="tab">
                            <i class="fa fa-user"></i> <strong>@lang('fleet::lang.drivers')</strong>
                        </a>
                    </li>

                    <li class=" @if(session('status.tab') == 'helpers') active @endif">
                        <a style="font-size:13px;" href="#helpers" data-toggle="tab">
                            <i class="fa fa-user-secret"></i> <strong>@lang('fleet::lang.helpers')</strong>
                        </a>
                    </li>

                     <li class=" @if(session('status.tab') == 'route_invoice_number') active @endif">
                        <a style="font-size:13px;" href="#route_invoice_number" data-toggle="tab">
                            # <strong>@lang('fleet::lang.starting_invoice_number')</strong>
                        </a>
                    </li>
                     <li class=" @if(session('status.tab') == 'route_product') active @endif">
                        <a style="font-size:13px;" href="#route_product" data-toggle="tab">
                            <i class="fa fa-cubes"></i> <strong>@lang('fleet::lang.product')</strong>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <div class="tab-content">
        <div class="tab-pane @if(empty(session('status.tab'))) active @endif" id="routes">
            @include('fleet::settings.routes.index')
        </div>
        <div class="tab-pane  @if(session('status.tab') == 'drivers') active @endif" id="drivers">
            @include('fleet::settings.drivers.index')
        </div>
        <div class="tab-pane  @if(session('status.tab') == 'helpers') active @endif" id="helpers">
            @include('fleet::settings.helpers.index')
        </div>
        <div class="tab-pane  @if(session('status.tab') == 'route_invoice_number') active @endif" id="route_invoice_number">
            @include('fleet::settings.route_invoice_number.index')
        </div>
        <div class="tab-pane  @if(session('status.tab') == 'route_product') active @endif" id="route_product">
            @include('fleet::settings.route_product.index')
        </div>

    </div>
</section>

@endsection


@section('javascript')
<script>
    if ($('#date_range_filter').length == 1) {
        $('#date_range_filter').daterangepicker(dateRangeSettings, function(start, end) {
            $('#date_range_filter').val(
               start.format(moment_date_format) + ' - ' +  end.format(moment_date_format)
            );
        });
        $('#date_range_filter').on('cancel.daterangepicker', function(ev, picker) {
            $('#product_sr_date_filter').val('');
        });
        $('#date_range_filter')
            .data('daterangepicker')
            .setStartDate(moment().startOf('month'));
        $('#date_range_filter')
            .data('daterangepicker')
            .setEndDate(moment().endOf('month'));
    }
    $(document).ready(function () {
        routes_table = $('#routes_table').DataTable({
            processing: true,
            serverSide: true,
            aaSorting: [[0, 'desc']],
            ajax: {
                url: '{{action('\Modules\Fleet\Http\Controllers\RouteController@index')}}',
                data: function (d) {
                    d.route_name = $('#route_names').val();
                    d.orignal_location = $('#orignal_locations').val();
                    d.destination = $('#destinations').val();
                    d.user_id = $('#users').val();
                    var start_date = $('input#date_range_filter')
                        .data('daterangepicker')
                        .startDate.format('YYYY-MM-DD');
                    var end_date = $('input#date_range_filter')
                        .data('daterangepicker')
                        .endDate.format('YYYY-MM-DD');
                    d.start_date = start_date;
                    d.end_date = end_date;
                }
            },
            @include('layouts.partials.datatable_export_button')
            columns: [
                { data: 'action', searchable: false, orderable: false },
                { data: 'date', name: 'date' },
                { data: 'route_name', name: 'route_name' },
                { data: 'orignal_location', name: 'orignal_location' },
                { data: 'destination', name: 'destination' },
                { data: 'distance', name: 'distance' },
                { data: 'rate', name: 'rate' },
                { data: 'route_amount', name: 'route_amount' },
                { data: 'driver_incentive', name: 'driver_incentive' },
                { data: 'helper_incentive', name: 'helper_incentive' },
                { data: 'created_by', name: 'created_by' },
               
            ],
            fnDrawCallback: function(oSettings) {
            
            },
        });
    })

    $('#date_range_filter, #route_names, #orignal_locations, #destinations, #users').change(function () {
        routes_table.ajax.reload();
    })

    $(document).on('click', 'a.delete_button', function(e) {
		var page_details = $(this).closest('div.page_details')
		e.preventDefault();
        swal({
            title: LANG.sure,
            icon: 'warning',
            buttons: true,
            dangerMode: true,
        }).then(willDelete => {
            if (willDelete) {
                var href = $(this).data('href');
                var data = $(this).serialize();
                $.ajax({
                    method: 'DELETE',
                    url: href,
                    dataType: 'json',
                    data: data,
                    success: function(result) {
                        if (result.success == true) {
                            toastr.success(result.msg);
                        } else {
                            toastr.error(result.msg);
                        }
                        routes_table.ajax.reload();
                        driver_table.ajax.reload();
                        helper_table.ajax.reload();
                        route_invoice_number_table.ajax.reload();
                        route_product_table.ajax.reload();
                    },
                });
            }
        });
    });

    //driver tab script
    if ($('#driver_date_range_filter').length == 1) {
        $('#driver_date_range_filter').daterangepicker(dateRangeSettings, function(start, end) {
            $('#driver_date_range_filter').val(
               start.format(moment_date_format) + ' - ' +  end.format(moment_date_format)
            );
        });
        $('#driver_date_range_filter').on('cancel.daterangepicker', function(ev, picker) {
            $('#product_sr_date_filter').val('');
        });
        $('#driver_date_range_filter')
            .data('daterangepicker')
            .setStartDate(moment().startOf('month'));
        $('#driver_date_range_filter')
            .data('daterangepicker')
            .setEndDate(moment().endOf('month'));
    }
    $(document).ready(function () {
        driver_table = $('#driver_table').DataTable({
            processing: true,
            serverSide: true,
            aaSorting: [[0, 'desc']],
            ajax: {
                url: '{{action('\Modules\Fleet\Http\Controllers\DriverController@index')}}',
                data: function (d) {
                    d.driver_name = $('#driver_name').val();
                    d.nic_number = $('#nic_number').val();
                    d.employee_no = $('#employee_no').val();
                    var start_date = $('input#driver_date_range_filter')
                        .data('daterangepicker')
                        .startDate.format('YYYY-MM-DD');
                    var end_date = $('input#driver_date_range_filter')
                        .data('daterangepicker')
                        .endDate.format('YYYY-MM-DD');
                    d.start_date = start_date;
                    d.end_date = end_date;
                }
            },
            @include('layouts.partials.datatable_export_button')
            columns: [
                { data: 'action', searchable: false, orderable: false },
                { data: 'joined_date', name: 'joined_date' },
                { data: 'employee_no', name: 'employee_no' },
                { data: 'driver_name', name: 'driver_name' },
                { data: 'nic_number', name: 'nic_number' },
                { data: 'dl_number', name: 'dl_number' },
             
               
            ],
            fnDrawCallback: function(oSettings) {
            
            },
        });
    })

    $('#driver_date_range_filter, #employee_no, #driver_name, #nic_number').change(function () {
        driver_table.ajax.reload();
    })

    //helper tab script
    if ($('#helper_date_range_filter').length == 1) {
        $('#helper_date_range_filter').daterangepicker(dateRangeSettings, function(start, end) {
            $('#helper_date_range_filter').val(
               start.format(moment_date_format) + ' - ' +  end.format(moment_date_format)
            );
        });
        $('#helper_date_range_filter').on('cancel.daterangepicker', function(ev, picker) {
            $('#product_sr_date_filter').val('');
        });
        $('#helper_date_range_filter')
            .data('daterangepicker')
            .setStartDate(moment().startOf('month'));
        $('#helper_date_range_filter')
            .data('daterangepicker')
            .setEndDate(moment().endOf('month'));
    }
    $(document).ready(function () {
        helper_table = $('#helper_table').DataTable({
            processing: true,
            serverSide: true,
            aaSorting: [[0, 'desc']],
            ajax: {
                url: '{{action('\Modules\Fleet\Http\Controllers\HelperController@index')}}',
                data: function (d) {
                    d.helper_name = $('#helper_name').val();
                    d.nic_number = $('#helper_nic_number').val();
                    d.employee_no = $('#helper_employee_no').val();
                    var start_date = $('input#helper_date_range_filter')
                        .data('daterangepicker')
                        .startDate.format('YYYY-MM-DD');
                    var end_date = $('input#helper_date_range_filter')
                        .data('daterangepicker')
                        .endDate.format('YYYY-MM-DD');
                    d.start_date = start_date;
                    d.end_date = end_date;
                }
            },
            @include('layouts.partials.datatable_export_button')
            columns: [
                { data: 'action', searchable: false, orderable: false },
                { data: 'joined_date', name: 'joined_date' },
                { data: 'employee_no', name: 'employee_no' },
                { data: 'helper_name', name: 'helper_name' },
                { data: 'nic_number', name: 'nic_number' },
             
               
            ],
            fnDrawCallback: function(oSettings) {
            
            },
        });
    })

    $('#helper_date_range_filter, #helper_employee_no, #helper_name, #helper_nic_number').change(function () {
        helper_table.ajax.reload();
    });

    //route_invoice_number_table tab script
    if ($('#route_invoice_number_date_range_filter').length == 1) {
        $('#route_invoice_number_date_range_filter').daterangepicker(dateRangeSettings, function(start, end) {
            $('#route_invoice_number_date_range_filter').val(
               start.format(moment_date_format) + ' - ' +  end.format(moment_date_format)
            );
        });
        $('#route_invoice_number_date_range_filter').on('cancel.daterangepicker', function(ev, picker) {
            $('#product_sr_date_filter').val('');
        });
        $('#route_invoice_number_date_range_filter')
            .data('daterangepicker')
            .setStartDate(moment().startOf('month'));
        $('#route_invoice_number_date_range_filter')
            .data('daterangepicker')
            .setEndDate(moment().endOf('month'));
    }
    $(document).ready(function () {
        route_invoice_number_table = $('#route_invoice_number_table').DataTable({
            processing: true,
            serverSide: true,
            aaSorting: [[0, 'desc']],
            ajax: {
                url: '{{action('\Modules\Fleet\Http\Controllers\RouteInvoiceNumberController@index')}}',
                data: function (d) {
                    var start_date = $('input#route_invoice_number_date_range_filter')
                        .data('daterangepicker')
                        .startDate.format('YYYY-MM-DD');
                    var end_date = $('input#route_invoice_number_date_range_filter')
                        .data('daterangepicker')
                        .endDate.format('YYYY-MM-DD');
                }
            },
            @include('layouts.partials.datatable_export_button')
            columns: [
                { data: 'action', searchable: false, orderable: false },
                { data: 'date', name: 'date' },
                { data: 'prefix', name: 'prefix' },
                { data: 'starting_number', name: 'starting_number' },
                { data: 'created_by', name: 'users.username' },
            ],
            fnDrawCallback: function(oSettings) {
            
            },
        });
    })
    $('#route_invoice_number_date_range_filter').change(function () {
        route_invoice_number_table.ajax.reload();
    });

    //route_product_table tab script
    if ($('#route_product_date_range_filter').length == 1) {
        $('#route_product_date_range_filter').daterangepicker(dateRangeSettings, function(start, end) {
            $('#route_product_date_range_filter').val(
               start.format(moment_date_format) + ' - ' +  end.format(moment_date_format)
            );
        });
        $('#route_product_date_range_filter').on('cancel.daterangepicker', function(ev, picker) {
            $('#product_sr_date_filter').val('');
        });
        $('#route_product_date_range_filter')
            .data('daterangepicker')
            .setStartDate(moment().startOf('month'));
        $('#route_product_date_range_filter')
            .data('daterangepicker')
            .setEndDate(moment().endOf('month'));
    }
    $(document).ready(function () {
        route_product_table = $('#route_product_table').DataTable({
            processing: true,
            serverSide: true,
            aaSorting: [[0, 'desc']],
            ajax: {
                url: '{{action('\Modules\Fleet\Http\Controllers\RouteProductController@index')}}',
                data: function (d) {
                    var start_date = $('input#route_product_date_range_filter')
                        .data('daterangepicker')
                        .startDate.format('YYYY-MM-DD');
                    var end_date = $('input#route_product_date_range_filter')
                        .data('daterangepicker')
                        .endDate.format('YYYY-MM-DD');
                }
            },
            @include('layouts.partials.datatable_export_button')
            columns: [
                { data: 'action', searchable: false, orderable: false },
                { data: 'date', name: 'date' },
                { data: 'name', name: 'name' },
                { data: 'created_by', name: 'users.username' },
            ],
            fnDrawCallback: function(oSettings) {
            
            },
        });
    })
    $('#route_product_date_range_filter').change(function () {
        route_product_table.ajax.reload();
    });


</script>
@endsection