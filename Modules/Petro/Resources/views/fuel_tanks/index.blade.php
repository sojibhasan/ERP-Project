@extends('layouts.app')

@section('title', __('petro::lang.tank_management'))



@section('content')

<section class="content-header">

    <div class="row">

        <div class="col-md-12 dip_tab">

            <div class="settlement_tabs">

                <ul class="nav nav-tabs">

                    <li class="active" style="margin-left: 20px;">

                        <a style="font-size:13px;" href="#fuel_tanks" class="" data-toggle="tab">

                            <i class="fa fa-superpowers"></i> <strong>@lang('petro::lang.fuel_tanks')</strong>

                        </a>

                    </li>

                    <li class="" style="margin-left: 20px;">

                        <a style="font-size:13px;" href="#tank_transactions_details" class="" data-toggle="tab">

                            <i class="fa fa-info-circle"></i>

                            <strong>@lang('petro::lang.tank_transactions_details')</strong>

                        </a>

                    </li>

                    <li class="" style="margin-left: 20px;">

                        <a style="font-size:13px;" href="#tank_transactions_summary" class="" data-toggle="tab">

                            <i class="fa fa-exchange"></i>

                            <strong>@lang('petro::lang.tank_transactions_summary')</strong>

                        </a>

                    </li>

                </ul>

            </div>

        </div>

    </div>

    <div class="tab-content">

        <div class="tab-pane active" id="fuel_tanks">

            @if(!empty($message)) {!! $message !!} @endif

            @include('petro::fuel_tanks.fuel_tanks')

        </div>

        <div class="tab-pane" id="tank_transactions_details">

            @if(!empty($message)) {!! $message !!} @endif

            @include('petro::tanks_transaction_details.tank_transactions_details')

        </div>

        <div class="tab-pane" id="tank_transactions_summary">

            @if(!empty($message)) {!! $message !!} @endif

            @include('petro::tanks_transaction_details.tank_transactions_summary')

        </div>

    </div>



    <div class="modal fade pump_modal" role="dialog" aria-labelledby="gridSystemModalLabel">

    </div>

    <div class="modal fade fuel_tank_modal" role="dialog" aria-labelledby="gridSystemModalLabel">

    </div>

</section>

@endsection

@section('javascript')

<script type="text/javascript">

    $(document).ready( function(){

    var columns = [

            { data: 'transaction_date', name: 'transaction_date' },

            { data: 'fuel_tank_number', name: 'fuel_tank_number' },

            { data: 'product_name', name: 'products.name' },

            { data: 'location_name', name: 'business_locations.name' },

            { data: 'storage_volume', name: 'storage_volume' },

            { data: 'new_balance', name: 'new_balance' },

            { data: 'bulk_tank', name: 'bulk_tank' },

            { data: 'action', searchable: false, orderable: false },

        ];

  

    fuel_tanks_table = $('#fuel_tanks_table').DataTable({

        processing: true,

        serverSide: true,

        aaSorting: [[0, 'desc']],

        ajax: "{{action('\Modules\Petro\Http\Controllers\FuelTankController@index')}}",

        columnDefs: [ {

            "targets": 7,

            "orderable": false,

            "searchable": false

        } ],

        @include('layouts.partials.datatable_export_button')

        columns: columns,

        fnDrawCallback: function(oSettings) {

        

        },

    });



    $(document).on('click', 'a.delete_tank_button', function(e) {

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

                        fuel_tanks_table.ajax.reload();

                    },

                });

            }

        });

    });

});

</script>



<script type="text/javascript">

    if ($('#transaction_details_date_range').length == 1) {

        $('#transaction_details_date_range').daterangepicker(dateRangeSettings, function(start, end) {

            $('#transaction_details_date_range').val(

                start.format(moment_date_format) + ' - ' + end.format(moment_date_format)

            );

        });

        $('#transaction_details_date_range').on('cancel.daterangepicker', function(ev, picker) {

            $('#transaction_details_date_range').val('');

        });

        $('#transaction_details_date_range')

            .data('daterangepicker')

            .setStartDate(moment().startOf('month'));

        $('#transaction_details_date_range')

            .data('daterangepicker')

            .setEndDate(moment().endOf('month'));

    }



  



    $(document).ready( function(){

        var tank_transaction_details_columns = [

            { data: 'created_at', name: 'created_at', searchable: false, },

            { data: 'transaction_date', name: 'transaction_date' ,sortable: false},

            { data: 'location_name', name: 'business_locations.name' ,sortable: false},

            { data: 'fuel_tank_number', name: 'fuel_tanks.fuel_tank_number' ,sortable: false},

            { data: 'product_name', name: 'products.name' ,sortable: false},

            { data: 'ref_no', name: 'ref_no' ,sortable: false},

            { data: 'purchase_order_no', name: 'purchase_order_no' ,sortable: false},

            { data: 'opening_balance_qty', name: 'opening_balance_qty' ,sortable: false},

            { data: 'purchase_qty', name: 'tank_purchase_lines.quantity' ,sortable: false},

            { data: 'sold_qty', name: 'tank_sell_lines.quantity' ,sortable: false},

            { data: 'balance_qty', name: 'balance_qty', searchable: false, sortable: false ,sortable: false},

        ];

    

        tank_transaction_details_table = $('#tank_transaction_details_table').DataTable({

            processing: true,

            serverSide: true,

            pageLength: 25, 

            deferRender: true,

            order: [[0, 'desc']],

            ajax: {

                url: '/petro/tanks-transaction-details',

                data: function(d) {

                    d.start_date = $('input#transaction_details_date_range')

                        .data('daterangepicker')

                        .startDate.format('YYYY-MM-DD');

                    d.end_date = $('input#transaction_details_date_range')

                        .data('daterangepicker')

                        .endDate.format('YYYY-MM-DD');

                    d.location_id =  $('#transaction_details_location_id').val();

                    d.fuel_tank_number =  $('#transaction_details_tank_number').val();

                    d.product_id =  $('#transaction_details_product_id').val();

                    d.settlement_id =  $('#transaction_details_settlement_id').val();

                    d.purchase_no =  $('#transaction_details_purhcase_no').val();

                },

            },

            columns: tank_transaction_details_columns,

            

        });

    });



    $('#transaction_details_date_range, #transaction_details_location_id, #transaction_details_tank_number, #transaction_details_product_id, #transaction_details_settlement_id, #transaction_details_purhcase_no').change(function(){

        tank_transaction_details_table.ajax.reload();

    });

</script>





<script type="text/javascript">

    if ($('#transaction_summary_date_range').length == 1) {

        $('#transaction_summary_date_range').daterangepicker(dateRangeSettings, function(start, end) {

            $('#transaction_summary_date_range').val(

                start.format(moment_date_format) + ' - ' + end.format(moment_date_format)

            );

        });

        $('#transaction_summary_date_range').on('cancel.daterangepicker', function(ev, picker) {

            $('#transaction_summary_date_range').val('');

        });

        $('#transaction_summary_date_range')

            .data('daterangepicker')

            .setStartDate(moment().startOf('month'));

        $('#transaction_summary_date_range')

            .data('daterangepicker')

            .setEndDate(moment().endOf('month'));

    }



    $('#transaction_summary_date_range, #transaction_summary_location_id, #transaction_summary_tank_number, #transaction_summary_product_id').change(function(){

        tank_transaction_summary_table.ajax.reload();

    });





    $(document).ready( function(){

        var tank_transaction_summary_columns = [

                { data: 'transaction_date', name: 'transaction_date' },

                { data: 'fuel_tank_number', name: 'fuel_tanks.fuel_tank_number' },

                { data: 'product_name', name: 'products.name' },

                { data: 'starting_qty', name: 'starting_qty', searchable: false, sortable: false},

                { data: 'purchase_qty', name: 'purchase_qty', searchable: false, sortable: false},

                { data: 'sold_qty', name: 'sold_qty', searchable: false, sortable: false},

                { data: 'balance_qty', name: 'balance_qty', searchable: false, sortable: false},

            ];

    

        tank_transaction_summary_table = $('#tank_transaction_summary_table').DataTable({

            processing: true,

            serverSide: true,

            ajax: {

                url: '/petro/tanks-transaction-summary',

                data: function(d) {

                    d.start_date = $('input#transaction_summary_date_range')

                        .data('daterangepicker')

                        .startDate.format('YYYY-MM-DD');

                    d.end_date = $('input#transaction_summary_date_range')

                        .data('daterangepicker')

                        .endDate.format('YYYY-MM-DD');

                    d.location_id =  $('#transaction_summary_location_id').val();

                    d.fuel_tank_number =  $('#transaction_summary_tank_number').val();

                    d.product_id =  $('#transaction_summary_product_id').val();

                },

            },

            columns: tank_transaction_summary_columns,

            rowCallback: function( row, data, index ) {

                if (data['balance_qty'] == 0) {

                    $(row).hide();

                }

            },

        });



        $('.add_fuel_tank').click(function(){

            $('.fuel_tank_modal').modal({

                backdrop : 'static',

                keyboard: false

            })

        })

    });

</script>

@endsection