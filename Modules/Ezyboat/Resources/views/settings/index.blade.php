@extends('layouts.app')
@section('title', __('ezyboat::lang.settings'))

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
                        <a style="font-size:13px;" href="#boat_trips" class="" data-toggle="tab">
                            <i class="fa fa-superpowers"></i> <strong>@lang('ezyboat::lang.boat_trips')</strong>
                        </a>
                    </li>
                    <li class=" @if(session('status.tab') == 'crews') active @endif">
                        <a style="font-size:13px;" href="#crews" data-toggle="tab">
                            <i class="fa fa-user"></i> <strong>@lang('ezyboat::lang.crews')</strong>
                        </a>
                    </li>

                    <li class=" @if(session('status.tab') == 'income_settings') active @endif">
                        <a style="font-size:13px;" href="#income_settings" data-toggle="tab">
                            <i class="fa fa-cogs"></i> <strong>@lang('ezyboat::lang.income_settings')</strong>
                        </a>
                    </li>

                     <li class=" @if(session('status.tab') == 'route_invoice_number') active @endif">
                        <a style="font-size:13px;" href="#route_invoice_number" data-toggle="tab">
                            # <strong>@lang('ezyboat::lang.tab_2')</strong>
                        </a>
                    </li>
                     <li class=" @if(session('status.tab') == 'route_product') active @endif">
                        <a style="font-size:13px;" href="#route_product" data-toggle="tab">
                            <i class="fa fa-cubes"></i> <strong>@lang('ezyboat::lang.tab_3')</strong>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <div class="tab-content">
        <div class="tab-pane @if(empty(session('status.tab'))) active @endif" id="boat_trips">
            @include('ezyboat::settings.boat_trips.index')
        </div>
        <div class="tab-pane  @if(session('status.tab') == 'crews') active @endif" id="crews">
            @include('ezyboat::settings.crews.index')
        </div>
        <div class="tab-pane  @if(session('status.tab') == 'income_settings') active @endif" id="income_settings">
            @include('ezyboat::settings.income_settings.index')
        </div>
        <div class="tab-pane  @if(session('status.tab') == 'route_invoice_number') active @endif" id="route_invoice_number">
            @include('ezyboat::settings.route_invoice_number.index')
        </div>
        <div class="tab-pane  @if(session('status.tab') == 'route_product') active @endif" id="route_product">
            @include('ezyboat::settings.route_product.index')
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
        boat_trips_table = $('#boat_trips_table').DataTable({
            processing: true,
            serverSide: true,
            aaSorting: [[0, 'desc']],
            ajax: {
                url: '{{action('\Modules\Ezyboat\Http\Controllers\BoatTripController@index')}}',
                data: function (d) {
                    d.trip_name = $('#trip_names').val();
                    d.starting_locations = $('#starting_locations').val();
                    d.final_locations = $('#final_locations').val();
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
                { data: 'trip_name', name: 'trip_name' },
                { data: 'starting_location', name: 'starting_location' },
                { data: 'final_location', name: 'final_location' },
                { data: 'created_by', name: 'created_by' },
               
            ],
            fnDrawCallback: function(oSettings) {
            
            },
        });
    })

    $('#date_range_filter, #trip_names, #starting_locations, #final_locations, #users').change(function () {
        boat_trips_table.ajax.reload();
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
                        boat_trips_table.ajax.reload();
                        crew_table.ajax.reload();
                        income_setting_table.ajax.reload();
                        route_invoice_number_table.ajax.reload();
                        route_product_table.ajax.reload();
                    },
                });
            }
        });
    });

    //crew tab script
    if ($('#crew_date_range_filter').length == 1) {
        $('#crew_date_range_filter').daterangepicker(dateRangeSettings, function(start, end) {
            $('#crew_date_range_filter').val(
               start.format(moment_date_format) + ' - ' +  end.format(moment_date_format)
            );
        });
        $('#crew_date_range_filter').on('cancel.daterangepicker', function(ev, picker) {
            $('#product_sr_date_filter').val('');
        });
        $('#crew_date_range_filter')
            .data('daterangepicker')
            .setStartDate(moment().startOf('month'));
        $('#crew_date_range_filter')
            .data('daterangepicker')
            .setEndDate(moment().endOf('month'));
    }
    $(document).ready(function () {
        crew_table = $('#crew_table').DataTable({
            processing: true,
            serverSide: true,
            aaSorting: [[0, 'desc']],
            ajax: {
                url: '{{action('\Modules\Ezyboat\Http\Controllers\CrewController@index')}}',
                data: function (d) {
                    d.crew_name = $('#crew_name').val();
                    d.nic_number = $('#nic_number').val();
                    d.employee_no = $('#employee_no').val();
                    var start_date = $('input#crew_date_range_filter')
                        .data('daterangepicker')
                        .startDate.format('YYYY-MM-DD');
                    var end_date = $('input#crew_date_range_filter')
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
                { data: 'crew_name', name: 'crew_name' },
                { data: 'nic_number', name: 'nic_number' },
                { data: 'license_number', name: 'license_number' },
             
               
            ],
            fnDrawCallback: function(oSettings) {
            
            },
        });
    })

    $('#crew_date_range_filter, #employee_no, #crew_name, #nic_number').change(function () {
        crew_table.ajax.reload();
    })

    //income_setting tab script
    if ($('#income_setting_date_range_filter').length == 1) {
        $('#income_setting_date_range_filter').daterangepicker(dateRangeSettings, function(start, end) {
            $('#income_setting_date_range_filter').val(
               start.format(moment_date_format) + ' - ' +  end.format(moment_date_format)
            );
        });
        $('#income_setting_date_range_filter').on('cancel.daterangepicker', function(ev, picker) {
            $('#product_sr_date_filter').val('');
        });
        $('#income_setting_date_range_filter')
            .data('daterangepicker')
            .setStartDate(moment().startOf('month'));
        $('#income_setting_date_range_filter')
            .data('daterangepicker')
            .setEndDate(moment().endOf('month'));
    }
    $(document).ready(function () {
        income_setting_table = $('#income_setting_table').DataTable({
            processing: true,
            serverSide: true,
            aaSorting: [[0, 'desc']],
            ajax: {
                url: '{{action('\Modules\Ezyboat\Http\Controllers\IncomeSettingController@index')}}',
                data: function (d) {
                    d.income_name = $('#income_name').val();
                    d.nic_number = $('#income_setting_nic_number').val();
                    d.employee_no = $('#income_setting_employee_no').val();
                    var start_date = $('input#income_setting_date_range_filter')
                        .data('daterangepicker')
                        .startDate.format('YYYY-MM-DD');
                    var end_date = $('input#income_setting_date_range_filter')
                        .data('daterangepicker')
                        .endDate.format('YYYY-MM-DD');
                    d.start_date = start_date;
                    d.end_date = end_date;
                }
            },
            @include('layouts.partials.datatable_export_button')
            columns: [
                { data: 'action', searchable: false, orderable: false },
                { data: 'income_name', name: 'income_name' },
                { data: 'owner_income', name: 'owner_income' },
                { data: 'crew_income', name: 'crew_income' },
                { data: 'deduct_expense_for_income', name: 'deduct_expense_for_income' },
             
               
            ],
            fnDrawCallback: function(oSettings) {
            
            },
        });
    })

    $('#income_setting_date_range_filter, #income_setting_employee_no, #income_name, #income_setting_nic_number').change(function () {
        income_setting_table.ajax.reload();
    });

    $(document).on('click', 'a.toggle-status', function(e) {
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
                    method: 'POST',
                    url: href,
                    dataType: 'json',
                    data: data,
                    success: function(result) {
                        if (result.success == true) {
                            toastr.success(result.msg);
                        } else {
                            toastr.error(result.msg);
                        }
                        boat_trips_table.ajax.reload();
                        crew_table.ajax.reload();
                        income_setting_table.ajax.reload();
                        route_invoice_number_table.ajax.reload();
                        route_product_table.ajax.reload();
                    },
                });
            }
        });
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
                url: '{{action('\Modules\Ezyboat\Http\Controllers\RouteInvoiceNumberController@index')}}',
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
                url: '{{action('\Modules\Ezyboat\Http\Controllers\RouteProductController@index')}}',
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