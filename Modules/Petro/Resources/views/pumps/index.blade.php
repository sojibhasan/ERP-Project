@extends('layouts.app')
@section('title', __('petro::lang.pump_management'))

@section('content')

<section class="content-header">
    <div class="row">
        <div class="col-md-12 dip_tab">
            <div class="settlement_tabs">
                <ul class="nav nav-tabs">
                    @if($enable_petro_pump_management)
                    <li class="active" style="margin-left: 20px;">
                        <a style="font-size:13px;" href="#pumps" class="" data-toggle="tab">
                            <i class="fa fa-superpowers"></i> <strong>@lang('petro::lang.pumps')</strong>
                        </a>
                    </li>
                    @endif
                    @if($enable_petro_management_testing)
                    <li class="">
                        <a style="font-size:13px;" href="#testing_details" data-toggle="tab">
                            <i class="fa fa-filter"></i> <strong>@lang('petro::lang.testing_details')</strong>
                        </a>
                    </li>
                    @endif
                    @if($meter_resetting_permission)
                        @can('meter_resetting_tab')
                        <li class="">
                            <a style="font-size:13px;" href="#meter_resettings" data-toggle="tab">
                                <i class="fa fa-sliders"></i> <strong>@lang('petro::lang.meter_resettings')</strong>
                            </a>
                        </li>
                        @endcan
                    @endif
                    @if($enable_petro_meter_reading)
                    <li class="">
                        <a style="font-size:13px;" href="#meter_readings" data-toggle="tab">
                            <i class="fa fa-thermometer"></i> <strong>@lang('petro::lang.meter_readings')</strong>
                        </a>
                    </li>
                    @endif
                    @if($enable_petro_pump_dashboard)
                    <li class="">
                        <a style="font-size:13px;" href="#dashboard_opening_meters" data-toggle="tab">
                            <i class="fa fa-dashboard"></i>
                            <strong>@lang('petro::lang.dashboard_opening_meters')</strong>
                        </a>
                    </li>
                    @endif
                </ul>
            </div>
        </div>
    </div>
    <div class="tab-content">
        <div class="tab-pane active" id="pumps">
            @if(!empty($message)) {!! $message !!} @endif
            @include('petro::pumps.partials.pumps')
        </div>
        <div class="tab-pane" id="testing_details">
            @if(!empty($message)) {!! $message !!} @endif
            @include('petro::pumps.partials.testing_details')
        </div>
        @if($meter_resetting_permission)
        @can('meter_resetting_tab')
        <div class="tab-pane" id="meter_resettings">
            @if(!empty($message)) {!! $message !!} @endif
            @include('petro::pumps.partials.meter_resettings')
        </div>
        @endcan
        @endif
        <div class="tab-pane" id="meter_readings">
            @if(!empty($message)) {!! $message !!} @endif
            @include('petro::pumps.partials.meter_readings')
        </div>
        <div class="tab-pane" id="dashboard_opening_meters">
            @if(!empty($message)) {!! $message !!} @endif
            @include('petro::pumps.partials.dashboard_opening_meters')
        </div>

    </div>

    <div class="modal fade pump_modal" role="dialog" aria-labelledby="gridSystemModalLabel">
    </div>
</section>

@endsection
@section('javascript')
<script type="text/javascript">
    var body = document.getElementsByTagName("body")[0];
    body.className += " sidebar-collapse";
    $(document).ready( function(){
    var columns = [
            { data: 'installation_date', name: 'installation_date' },
            { data: 'transaction_date', name: 'transaction_date' },
            { data: 'pump_no', name: 'pump_no' },
            { data: 'pump_name', name: 'pump_name' },
            { data: 'temp_meter_reading', name: 'temp_meter_reading' },
            { data: 'last_meter_reading', name: 'last_meter_reading' },
            { data: 'product_name', name: 'products.name' },
            { data: 'location_name', name: 'business_locations.name' },
            { data: 'fuel_tank_number', name: 'fuel_tanks.fuel_tank_number' },
            { data: 'action', searchable: false, orderable: false },
        ];
  
    list_pumps_table = $('#list_pumps_table').DataTable({
        processing: true,
        serverSide: true,
        aaSorting: [[0, 'desc']],
        ajax: '{{action('\Modules\Petro\Http\Controllers\PumpController@index')}}',
        columnDefs: [ {
            "targets": 6,
            "orderable": false,
            "searchable": false
        } ],
        @include('layouts.partials.datatable_export_button')
        columns: columns,
        fnDrawCallback: function(oSettings) {
        
        },
    });

    $(document).on('click', 'a.delete_pump_button', function(e) {
		var page_details = $(this).closest('div.page_details')
		e.preventDefault();
        swal({
            title: LANG.sure,
            icon: 'warning',
            buttons: true,
            dangerMode: true,
        }).then(willDelete => {
            if (willDelete) {
                var href = $(this).attr('href');
                var data = $(this).serialize();
                console.log(href);
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
                        list_pumps_table.ajax.reload();
                    },
                });
            }
        });
    });


    if ($('#testing_details_date_range').length == 1) {
        $('#testing_details_date_range').daterangepicker(dateRangeSettings, function(start, end) {
            $('#testing_details_date_range').val(
                start.format(moment_date_format) + ' - ' + end.format(moment_date_format)
            );
        });
        $('#testing_details_date_range').on('cancel.daterangepicker', function(ev, picker) {
            $('#testing_details_date_range').val('');
        });
        $('#testing_details_date_range')
            .data('daterangepicker')
            .setStartDate(moment().startOf('month'));
        $('#testing_details_date_range')
            .data('daterangepicker')
            .setEndDate(moment().endOf('month'));
    }
   $('#testing_details_date_range').change(function () {
       $('.testing_details_from_date').text($('input#testing_details_date_range')
                       .data('daterangepicker')
                       .startDate.format('YYYY-MM-DD'));
       $('.testing_details_to_date').text($('input#testing_details_date_range')
                       .data('daterangepicker')
                       .endDate.format('YYYY-MM-DD'));
   })


    var columns2 = [
            { data: 'action', name: 'action' },
            { data: 'transaction_date', name: 'settlements.transaction_date' },
            { data: 'location_name', name: 'business_locations.name' },
            { data: 'settlement_no', name: 'settlements.settlement_no' },
            { data: 'pump_no', name: 'pumps.pump_no' },
            { data: 'product_name', name: 'products.name' },
            { data: 'pump_operator_name', name: 'pump_operators.name'},
            { data: 'testing_qty', name: 'meter_sales.testing_qty' },
            { data: 'testing_sale_value', name: 'testing_sale_value', searchable: false }
        ];
  
    dip_testing_details_table = $('#dip_testing_details_table').DataTable({
        processing: true,
        serverSide: true,
        aaSorting: [[0, 'desc']],
        ajax: {
            url: '{{action('\Modules\Petro\Http\Controllers\PumpController@getTestingDetails')}}',
            data: function(d) {
                d.location_id = $('select#testing_details_location_id').val();
                d.pump_operator = $('select#testing_details_pump_operators').val();
                d.settlement_no = $('select#testing_details_settlement_no').val();
                d.pump = $('select#testing_details_pumps').val();
                d.product_id = $('select#testing_details_product_id').val();
                d.start_date = $('input#testing_details_date_range')
                    .data('daterangepicker')
                    .startDate.format('YYYY-MM-DD');
                d.end_date = $('input#testing_details_date_range')
                    .data('daterangepicker')
                    .endDate.format('YYYY-MM-DD');
            },
        },
       
        columns: columns2,
        @include('layouts.partials.datatable_export_button')
        fnDrawCallback: function(oSettings) {
            var testing_qty = sum_table_col($('#dip_testing_details_table'), 'testing_qty');
            $('#footer_testing_qty').text(testing_qty);
            var testing_sale_value = sum_table_col($('#dip_testing_details_table'), 'testing_sale_value');
            $('#footer_testing_sale_value').text(testing_sale_value);
            __currency_convert_recursively($('#dip_testing_details_table'));
        },
    });

$('#testing_details_location_id, #testing_details_pump_operators , #testing_details_settlement_no , #testing_details_pumps , #testing_details_product_id, #testing_details_date_range').change(function(){
    dip_testing_details_table.ajax.reload();

    if($('#testing_details_location_id').val() !== ''  && $('#testing_details_location_id').val() !== undefined){
        $('.testing_details_location_name').text($('#testing_details_location_id :selected').text())
    }
});




    if ($('#meter_resettings_date_range').length == 1) {
        $('#meter_resettings_date_range').daterangepicker(dateRangeSettings, function(start, end) {
            $('#meter_resettings_date_range').val(
                start.format(moment_date_format) + ' - ' + end.format(moment_date_format)
            );
        });
        $('#meter_resettings_date_range').on('cancel.daterangepicker', function(ev, picker) {
            $('#meter_resettings_date_range').val('');
        });
        $('#meter_resettings_date_range')
            .data('daterangepicker')
            .setStartDate(moment().startOf('month'));
        $('#meter_resettings_date_range')
            .data('daterangepicker')
            .setEndDate(moment().endOf('month'));
    }
    $('input#meter_resettings_date_range').change(function () {
        $('.meter_resettings_from_date').text($('input#meter_resettings_date_range')
                        .data('daterangepicker')
                        .startDate.format('YYYY-MM-DD'));
        $('.meter_resettings_to_date').text($('input#meter_resettings_date_range')
                        .data('daterangepicker')
                        .endDate.format('YYYY-MM-DD'));
    })


    var meter_resettings_columns = [
            { data: 'action', searchable: false, orderable: false },
            { data: 'date_and_time', name: 'date_and_time' },
            { data: 'location_name', name: 'business_locations.name' },
            { data: 'pump_no', name: 'pumps.pump_no' },
            { data: 'fuel_tank_number', name: 'fuel_tanks.fuel_tank_number' },
            { data: 'last_meter', name: 'last_meter' },
            { data: 'new_reset_meter', name: 'new_reset_meter' },
            { data: 'username', name: 'users.username' },
            { data: 'reason', name: 'reason' },
        ];
  
    dip_meter_resettings_table = $('#dip_meter_resettings_table').DataTable({
        processing: true,
        serverSide: true,
        aaSorting: [[0, 'desc']],
        ajax: {
            url: '{{action('\Modules\Petro\Http\Controllers\MeterResettingController@index')}}',
            data: function(d) {
                d.location_id = $('select#meter_resettings_location_id').val();
                d.tank_id = $('select#meter_resettings_tanks').val();
                d.product_id = $('select#meter_resettings_product_id').val();
                d.start_date = $('input#meter_resettings_date_range')
                    .data('daterangepicker')
                    .startDate.format('YYYY-MM-DD');
                d.end_date = $('input#meter_resettings_date_range')
                    .data('daterangepicker')
                    .endDate.format('YYYY-MM-DD');
            },
        },
        @include('layouts.partials.datatable_export_button')
       
        columns: meter_resettings_columns,
        fnDrawCallback: function(oSettings) {
        
        },
    });

$('#meter_resettings_location_id, #meter_resettings_tanks , #meter_resettings_product_id, #meter_resettings_date_range').change(function(){
    dip_meter_resettings_table.ajax.reload();

    if($('#meter_resettings_location_id').val() !== ''  && $('#meter_resettings_location_id').val() !== undefined){
        $('.meter_resettings_location_name').text($('#meter_resettings_location_id :selected').text())
    }
});



$(document).on('click','.add_meter_resetting_btn', function(){
        $.ajax({
            method: 'post',
            url: '/petro/meter-resetting',
            data: { 
                meter_reset_ref_no :  $('#meter_reset_ref_no').val(),
                location_id :  $('#meter_reset_location_id').val(),
                pump_id :  $('#add_reset_pump_id').val(),
                new_reset_meter :  $('input[name=new_reset_meter]').val(),
                date_and_time :  $('input[name=date_and_time]').val(),
                last_meter :  $('input[name=last_meter_current_meter]').val(),
                reason :  $('input[name=meter_resettings_reason]').val()
            },
            success: function(result) {
                if(result.success == 1){
                    toastr.success(result.msg);
                    $('.pump_modal').modal('hide');
                    $('.pump_modal').empty();
                }else{
                    toastr.error(result.msg);
                }
                dip_report_table.ajax.reload();
            }
        });
    });



    if ($('#meter_readings_date_range').length == 1) {
        $('#meter_readings_date_range').daterangepicker(dateRangeSettings, function(start, end) {
            $('#meter_readings_date_range').val(
               start.format(moment_date_format) + ' - ' +  end.format(moment_date_format)
            );
        });
        $('#meter_readings_date_range').on('cancel.daterangepicker', function(ev, picker) {
            $('#product_sr_date_filter').val('');
        });
        $('#meter_readings_date_range')
            .data('daterangepicker')
            .setStartDate(moment().startOf('month'));
        $('#meter_readings_date_range')
            .data('daterangepicker')
            .setEndDate(moment().endOf('month'));
    }
  
    $('.meter_readings_from_date').text($('input#meter_readings_date_range')
                        .data('daterangepicker')
                        .startDate.format('YYYY-MM-DD'));
        $('.meter_readings_to_date').text($('input#meter_readings_date_range')
                        .data('daterangepicker')
                        .endDate.format('YYYY-MM-DD'));
    $('#meter_readings_date_range').change(function(){
        $('.meter_readings_from_date').text($('input#meter_readings_date_range')
                        .data('daterangepicker')
                        .startDate.format('YYYY-MM-DD'));
        $('.meter_readings_to_date').text($('input#meter_readings_date_range')
                        .data('daterangepicker')
                        .endDate.format('YYYY-MM-DD'));
    })


    var meter_readings_columns = [
            { data: 'action', name: 'action' },
            { data: 'transaction_date', name: 'settlements.transaction_date' },
            { data: 'location_name', name: 'business_locations.name' },
            { data: 'settlement_no', name: 'settlement_no' },
            { data: 'pump_no', name: 'pumps.pump_no' },
            { data: 'product_name', name: 'products.name' },
            { data: 'pump_operator_name', name: 'pump_operators.name' },
            { data: 'qty', name: 'qty' },
            { data: 'starting_meter', name: 'starting_meter' },
            { data: 'closing_meter', name: 'closing_meter' },
            { data: 'testing_qty', name: 'testing_qty' },
            { data: 'sub_total', name: 'sub_total' }
        ];
  
    dip_meter_readings_table = $('#dip_meter_readings_table').DataTable({
        processing: true,
        serverSide: true,
        aaSorting: [[0, 'desc']],
        ajax: {
            url: '{{action('\Modules\Petro\Http\Controllers\PumpController@getMeterReadings')}}',
            data: function(d) {
                d.location_id = $('select#meter_readings_location_id').val();
                d.pump_operator = $('select#meter_readings_pump_operators').val();
                d.settlement_no = $('select#meter_readings_settlement_no').val();
                d.pump = $('select#meter_readings_pumps').val();
                d.product_id = $('select#meter_readings_product_id').val();
                d.start_date = $('input#meter_readings_date_range')
                    .data('daterangepicker')
                    .startDate.format('YYYY-MM-DD');
                d.end_date = $('input#meter_readings_date_range')
                    .data('daterangepicker')
                    .endDate.format('YYYY-MM-DD');
            },
        },

        @include('layouts.partials.datatable_export_button')
       
        columns: meter_readings_columns,
        fnDrawCallback: function(oSettings) {
            var sold_qty = sum_table_col($('#dip_meter_readings_table'), 'qty');
            $('#footer_mr_sold_ltrs').text(sold_qty);
            var testing_qty = sum_table_col($('#dip_meter_readings_table'), 'testing_qty');
            $('#footer_mr_testing_ltrs').text(testing_qty);
            __currency_convert_recursively($('#dip_meter_readings_table'));
        },
    });

    $('#meter_readings_location_id, #meter_readings_pump_operators , #meter_readings_settlement_no , #meter_readings_pumps , #meter_readings_product_id, #meter_readings_date_range').change(function(){
        dip_meter_readings_table.ajax.reload();

        if($('#meter_readings_location_id').val() !== ''  && $('#meter_readings_location_id').val() !== undefined){
            $('.meter_readings_location_name').text($('#meter_readings_location_id :selected').text())
        }
    });

});

$('#opening_meter_pump_id').change(function () {
    var pump_id = $(this).val();
    $.ajax({
        method: 'get',
        url: '/petro/meter-resetting/get-pump-details?pump_id='+pump_id,
        data: {  },
        success: function(result) {
            $('#opening_meter_product_name').val(result.product_name);
            $('#opening_meter_current_meter').val(result.last_meter_reading);
        },
    });
})

$('#opening_meter_save').click(function () {
    
    if($('#opening_meter_pump_id').val() !== undefined && $('#opening_meter_pump_id').val() !== null && $('#opening_meter_pump_id').val() !== '' && $('#opening_meter_reset_meter').val() !== undefined && $('#opening_meter_reset_meter').val() !== null && $('#opening_meter_reset_meter').val() !== ''){
        $(this).attr('disabled', true);
        $.ajax({
            method: 'post',
            url: '/petro/opening-meter',
            data: { 
                pump_id : $('#opening_meter_pump_id').val(),
                current_meter: $('#opening_meter_current_meter').val(),
                reset_meter: $('#opening_meter_reset_meter').val(),
    
             },
            success: function(result) {
                $('#opening_meter_save').attr('disabled', false);
                if(result.success){
                    toastr.success(result.msg);
                    opening_meter_table.ajax.reload();
                }else{
                    toastr.error(result.msg);
                }
            },
        });
    }else{
        toastr.error('Please provide valid value');
    }
})
$(document).ready( function(){
    opening_meter_table = $('#opening_meter_table').DataTable({
        processing: true,
        serverSide: true,
        aaSorting: [[0, 'desc']],
        ajax: {
            url: '{{action('\Modules\Petro\Http\Controllers\OpeningMeterController@index')}}',
            data: function(d) {

            },
        },

        @include('layouts.partials.datatable_export_button')
    
        columns: [
            { data: 'date_and_time', name: 'date_and_time' },
            { data: 'location_name', name: 'business_locations.name' },
            { data: 'pump_no', name: 'pump_no' },
            { data: 'product_name', name: 'products.name' },
            { data: 'current_meter', name: 'current_meter' },
            { data: 'reset_meter', name: 'reset_meter' },
            { data: 'username', name: 'users.username' },
        ],
        fnDrawCallback: function(oSettings) {

        },
    });
});
</script>
@endsection