@extends('layouts.app')

@section('title', __('petro::lang.dip_management'))



@section('content')

<!-- Content Header (Page header) -->

<section class="content-header">
    <div class="row">

        <div class="col-md-12 dip_tab">

            <div class="settlement_tabs">

                <ul class="nav nav-tabs">

                    <li class="active" style="margin-left: 20px;">

                        <a style="font-size:13px;" href="#dip_report" class="" data-toggle="tab">

                            <i class="fa fa-file-o"></i> <strong>@lang('petro::lang.dip_report')</strong>

                        </a>

                    </li>

                    <li class="">
                        <a style="font-size:13px;" href="#dip_resetting" data-toggle="tab">

                            <i class="fa fa-gear"></i> <strong>@lang('petro::lang.dip_resetting')</strong>

                        </a>

                    </li>

                </ul>

            </div>

        </div>

    </div>

    <div class="tab-content">

        <div class="tab-pane active" id="dip_report">

            @if(!empty($message)) {!! $message !!} @endif

            @include('petro::dip_management.partials.dip_report')

        </div>

        <div class="tab-pane" id="dip_resetting">

            @if(!empty($message)) {!! $message !!} @endif

            @include('petro::dip_management.partials.dip_resetting')

        </div>



    </div>



    <div class="modal fade dip_modal" role="dialog" aria-labelledby="gridSystemModalLabel">

    </div>

</section>



@endsection

@section('javascript')

<script type="text/javascript">

    $(document).ready( function(){

    if ($('#report_date_range').length == 1) {

        $('#report_date_range').daterangepicker(dateRangeSettings, function(start, end) {

            $('#report_date_range').val(

                start.format(moment_date_format) + ' - ' + end.format(moment_date_format)

            );

        });

        $('#report_date_range').on('cancel.daterangepicker', function(ev, picker) {

            $('#report_date_range').val('');

        });

        $('#report_date_range')

            .data('daterangepicker')

            .setStartDate(moment().startOf('month'));

        $('#report_date_range')

            .data('daterangepicker')

            .setEndDate(moment().endOf('month'));

    }

    let date = $('#report_date_range').val().split(' - ');

    

    $('.report_from_date').text(date[0]);

    $('.report_to_date').text(date[1]);



    $('#location_id').select2();

    $('#tank_id').select2();

    $('#prodcut_id').select2();



    var columns = [

            { data: 'ref_number', name: 'ref_number' },

            { data: 'date_and_time', name: 'date_and_time' },

            { data: 'location_name', name: 'business_locations.name' },

            { data: 'tank_name', name: 'fuel_tanks.fuel_tank_number' },

            { data: 'product_name', name: 'products.name' },

            { data: 'dip_reading', name: 'dip_reading' },

            { data: 'fuel_balance_dip_reading', name: 'fuel_balance_dip_reading' },

            { data: 'current_qty', name: 'current_qty' },

            { data: 'difference', name: 'difference' },
            
            { data: 'difference_value', name: 'difference_value' },

        ];
        
        dip_report_table = $('#dip_report_table').DataTable({

        processing: true,

        serverSide: true,

        aaSorting: [[0, 'desc']],

        ajax: {

            url: '{{action('\Modules\Petro\Http\Controllers\DipManagementController@getDipReport')}}',

            data: function(d) {

                d.location_id = $('select#report_location_id').val();
               
                d.tank_id= $('select#report_tank_id').val();
                    
                d.product_id = $('select#report_product_id').val();
                    
                d.start_date = $('input#report_date_range').data('daterangepicker').startDate.format('YYYY-MM-DD');
                    
                d.end_date = $('input#report_date_range')
                        .data('daterangepicker')
                        .endDate.format('YYYY-MM-DD');

            },
            

        },
        
        fnDrawCallback: function(oSettings) { 
            
            var api = this.api(), data;
            var intVal = function(i) {
                return typeof i === 'string' ? i.replace(/[\$,]/g, '') * 1 : typeof i === 'number' ? i : 0;
            };
            
                var total_difference = api
                .column(8)
                .data()
                .reduce( function(a,b) {
                    return intVal(a) + intVal(b);
                }, 0);
                
                
                 var total_difference_value = api
                .column(9)
                .data()
                .reduce( function(a,b) {
                    return intVal(a) + intVal(b);
                }, 0);
                
                
                $('#dip_report_table .footer_total .difference_total').text(total_difference.toLocaleString('en-US', {minimumFractionDigits: 2}));
                
                $('#dip_report_table .footer_total .difference_value_total').text(total_difference_value.toLocaleString('en-US', {minimumFractionDigits: 2}));
                
            },
        

        columnDefs: [ {

            "targets": 0,

            "orderable": false,

            "searchable": false

        } ],

        columns: columns,
        
    

    });

    //     let dip_report_table = $('#dip_report_table').DataTable({
    //         aaSorting: [[0, 'desc']],
    //         ajax: {
    //             url: "{{action('\Modules\Petro\Http\Controllers\DipManagementController@getDipReport')}}",
    //             type: "GET",
    //             data: function(d) {
    //                 d.location_id = $('select#report_location_id').val();
    //                 d.tank_id= $('select#report_tank_id').val();
    //                 d.product_id = $('select#report_product_id').val();
    //                 d.start_date = $('input#report_date_range')
    //                     .data('daterangepicker')
    //                     .startDate.format('YYYY-MM-DD');
    //                 d.end_date = $('input#report_date_range')
    //                     .data('daterangepicker')
    //                     .endDate.format('YYYY-MM-DD');
    //             },
    //             dataSrc: "",

    //         },
    //         deferRender: true,
    //         createdRow: function (row, data, index) {
    //         },
    //         fnDrawCallback: function(oSettings) { 
        
    //             var total_difference = '{{ Session::get('difference_total')}}';
    //             $('#dip_report_table .footer_total .difference_total').text(total_difference);
    //             var total_difference_value = '{{ Session::get('difference_value_total')}}';
    //             $('#dip_report_table .footer_total .difference_value_total').text(total_difference_value);

    //             __currency_convert_recursively($('#dip_report_table')); 
    //         },        
    // });

    //     dip_report_table = $('#dip_report_table').DataTable({
    //     processing: true,
    //     serverSide: true,
    //     aaSorting: [[0, 'desc']],
    //     ajax: {
    //         url: "{{action('\Modules\Petro\Http\Controllers\DipManagementController@getDipReport')}}",
    //         data: function(d) {
    //             d.location_id = $('select#report_location_id').val();
    //             d.tank_id= $('select#report_tank_id').val();
    //             d.product_id = $('select#report_product_id').val();
    //             d.start_date = $('input#report_date_range')
    //                 .data('daterangepicker')
    //                 .startDate.format('YYYY-MM-DD');
    //             d.end_date = $('input#report_date_range')
    //                 .data('daterangepicker')
    //                 .endDate.format('YYYY-MM-DD');
    //         },
    //     },
       
    //     columns: columns,
    //     fnDrawCallback: function(oSettings) { 
    //         var api = this.api(), data;
    //         // converting to interger to find total
    //         var intVal = function ( i ) {
    //             return typeof i === 'string' ?
    //                 i.replace(/[\$,]/g, '')*1 :
    //                 typeof i === 'number' ?
    //                     i : 0;
    //         };
    
    //         // computing column Total of the complete result 
    //         var total_difference = api
    //                     .column( 8 )
    //                     .data()
    //                     .reduce( function (a, b) {
    //                         return intVal(a) + intVal(b);
    //                     }, 0 );
                        
    //         $('#dip_report_table .footer_total .difference_total').text(total_difference);
    //         var total_difference_value = api
    //                     .column( 9 )
    //                     .data()
    //                     .reduce( function (a, b) {
    //                         return intVal(a) + intVal(b);
    //                     }, 0 );            
    //         $('#dip_report_table .footer_total .difference_value_total').text(total_difference_value);

    //         __currency_convert_recursively($('#dip_report_table'));                              
    //     },        
    // });



$('#report_location_id, #report_tank_id , #report_product_id, #report_date_range').change(function(){

    dip_report_table.ajax.reload();



    if($('#report_location_id').val() !== ''  && $('#report_location_id').val() !== undefined){

        $('.report_location_name').text($('#report_location_id :selected').text())

    }

    let date = $('#report_date_range').val().split(' - ');

    

    $('.report_from_date').text(date[0]);

    $('.report_to_date').text(date[1]);

});



    $(document).on('change','#add_dip_tank_id', function(){

        $.ajax({

            method: 'get',

            url: '/petro/get-tank-balance-by-id/'+ $(this).val(),

            data: { },

            success: function(result) {
                console.log('colled');
                if(result.current_diff == '0.000'){
                    $('#current_qty').val(result.current_stock);
                }else {
                    $('#current_qty').val(result.current_stock);
                }
                
            

            },

        });

    });



    $(document).on('change','#add_reset_tank_id', function(){
console.log('ok working 1');

        $.ajax({

            method: 'get',

            url: '/petro/get-tank-balance-by-id/'+ $(this).val(),

            data: { },

            dataType: 'json',

            success: function(result) {
                

                $('#product_name').val(result.product.name);

                
                    
                  
            if(result.current_diff == '0.000') {
                $('#current_qty').val(result.current_stock);
            }else {
                $('#current_qty').val(result.current_stock);
            }

            
            if(result.current_diff == '0.000' || result.reset_new_dip == '0.00'){
                $('#current_dip_difference').val(result.current_diff_for_reseting);
            }else {
                $('#current_dip_difference').val(result.reset_new_dip);
            }
                console.log('ok working 3');


            },

        });

    });
    
    
    

    /**  

     * @ModifiedBy Afes Oktavianus

     * @DateBy 06-06-2021

     * @Task 3341

     */

    $(document).on('change','#reset_new_dip', function(){

        let quantity_presicion = $('#quantity_presicion').val()

        let current_qty = $('#current_qty').val();

        let new_dip_qty = $(this).val();

        let qty_to_adjust = new_dip_qty - current_qty;

        var type = '';
        if (parseInt(qty_to_adjust) < 0) {

            $('#adjustment_type').val('decrease');
            type = 'decrease';

        }else {

            $('#adjustment_type').val('increase');
            type = 'increase';

        }
        $.ajax({

                method: 'get',

                url: "/stock-adjustments/inventory-adjustment-account",

                data: { type },

                contentType: 'html',

                success: function(result) {

                    $('#inventory_adjustment_account').empty().append(result);

                },

            });


        $('#qty_to_adjust').val(qty_to_adjust.toFixed(quantity_presicion));

    })



    $(document).on('click','.add_new_dip_reading_btn', function(){

        $(this).attr('disabled', 'disabled');

        $.ajax({

            method: 'post',

            url: '/petro/save-new-dip-reading',

            data: { 

                location_id :  $('#location_id').val(),

                tank_id :  $('#add_dip_tank_id').val(),

                ref_number :  $('input[name=ref_number]').val(),

                date_and_time :  $('input[name=date_and_time]').val(),

                dip_reading :  $('#dip_reading').val(),

                fuel_balance_dip_reading :  $('input[name=fuel_balance_dip_reading]').val(),

                current_qty :  $('input[name=current_qty]').val(),

                tank_manufacturer :  $('input[name=tank_manufacturer]').val(),

                tank_capacity :  $('input[name=tank_capacity]').val(),

                note :  $('#note').val(),

            },

            success: function(result) {

                if(result.success == 1){

                    toastr.success(result.msg);

                    $('.dip_modal').modal('hide');

                    $('.dip_modal').empty();

                }else{

                    toastr.error(result.msg);

                }

                dip_report_table.ajax.reload();

            }

        });

    });



    if ($('#resetting_date_range').length == 1) {

        $('#resetting_date_range').daterangepicker(dateRangeSettings, function(start, end) {

            $('#resetting_date_range').val(

                start.format(moment_date_format) + ' - ' + end.format(moment_date_format)

            );

        });

        $('#resetting_date_range').on('cancel.daterangepicker', function(ev, picker) {

            $('#product_sr_date_filter').val('');

        });

        $('#resetting_date_range')

            .data('daterangepicker')

            .setStartDate(moment().startOf('month'));

        $('#resetting_date_range')

            .data('daterangepicker')

            .setEndDate(moment().endOf('month'));

    }

    let reset_date = $('#resetting_date_range').val().split(' - ');

    $('.resetting_from_date').text(reset_date[0]);

    $('.resetting_to_date').text(reset_date[1]);



    $(document).on('submit','#dip_resetting_form', function(e){
        e.preventDefault()
        $.ajax({

            method: 'post',

            url: '/petro/save-resetting-dip',

            data: { 

                location_id :  $('#location_id').val(),

                tank_id :  $('#add_reset_tank_id').val(),

                inventory_adjustment_account :  $('#inventory_adjustment_account').val(),

                meter_reset_form_no :  $('input[name=meter_reset_form_no]').val(),

                date_and_time :  $('input[name=date_and_time]').val(),

                current_qty :  $('input[name=current_qty]').val(),

                current_dip_difference :  $('input[name=current_dip_difference]').val(),

                reset_new_dip :  $('input[name=reset_new_dip]').val(),

                adjustment_type :  $('#adjustment_type').val(),

                reason :  $('#reason').val(),

            },

            success: function(result) {

                if(result.success == 1){

                    toastr.success(result.msg);

                    $('.dip_modal').modal('hide');

                    $('.dip_modal').empty();

                }else{

                    toastr.error(result.msg);

                }

                dip_resetting_table.ajax.reload();

            }

        });
        return false;

    });



    $(document).on('change', '#adjustment_type',function () {

		if($(this).val() === 'increase'){

			type = 'increase';

		}

		if($(this).val() === 'decrease'){

			type = 'decrease';

		}



		$.ajax({

			method: 'get',

			url: "/stock-adjustments/inventory-adjustment-account",

			data: { type },

			contentType: 'html',

			success: function(result) {

				$('#inventory_adjustment_account').empty().append(result);

			},

		});

	})



    var resetting_columns = [

            { data: 'meter_reset_form_no', name: 'meter_reset_form_no' },

            { data: 'date_and_time', name: 'date_and_time' },

            { data: 'location_name', name: 'location_name' },

            { data: 'tank_name', name: 'tank_name' },

            { data: 'product_name', name: 'product_name' },

            { data: 'current_qty', name: 'current_qty' },

            { data: 'current_dip_difference', name: 'current_dip_difference' },

            { data: 'reset_new_dip', name: 'reset_new_dip' },

            { data: 'reason', name: 'reason' }

        ];

  

    dip_resetting_table = $('#dip_resetting_table').DataTable({

        processing: true,

        serverSide: true,

        aaSorting: [[0, 'desc']],

        ajax: {

            url: '{{action('\Modules\Petro\Http\Controllers\DipManagementController@getDipResetting')}}',

            data: function(d) {

                d.location_id = $('select#resetting_location_id').val();

                d.tank_id= $('select#resetting_tank_id').val();

                d.product_id = $('select#resetting_product_id').val();

                d.start_date = $('input#resetting_date_range')

                    .data('daterangepicker')

                    .startDate.format('YYYY-MM-DD');

                d.end_date = $('input#resetting_date_range')

                    .data('daterangepicker')

                    .endDate.format('YYYY-MM-DD');

            },

        },

        columnDefs: [ {

            "targets": 0,

            "orderable": false,

            "searchable": false

        } ],

        columns: resetting_columns,

        fnDrawCallback: function(oSettings) {

        

        },

    });



$('#resetting_location_id, #resetting_tank_id , #resetting_product_id, #resetting_date_range').change(function(){

    dip_resetting_table.ajax.reload();



    if($('#resetting_location_id').val() !== ''  && $('#resetting_location_id').val() !== undefined){

        $('.resetting_location_name').text($('#resetting_location_id :selected').text())

    }

    let reset_date = $('#resetting_date_range').val().split(' - ');

    $('.resetting_from_date').text(reset_date[0]);

    $('.resetting_to_date').text(reset_date[1]);

});





});

</script>

@endsection