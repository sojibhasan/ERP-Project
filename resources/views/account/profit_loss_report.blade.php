@extends('layouts.app')

@section('title', __('lang_v1.profit_loss_report'))



@section('content')

@include('report.profit_loss')

@endsection



@section('javascript')

<script src="{{ asset('js/report.js?v=' . $asset_v) }}"></script>

<script>

    $(document).ready( function() {

            if ($('#profit_tabs_filter').length == 1) {

            $('#profit_tabs_filter').daterangepicker(dateRangeSettings, function(start, end) {

                $('#profit_tabs_filter span').html(

                    start.format(moment_date_format) + ' ~ ' + end.format(moment_date_format)

                );

                $('.nav-tabs li.active').find('a[data-toggle="tab"]').trigger('shown.bs.tab');

            });

            $('#profit_tabs_filter').on('cancel.daterangepicker', function(ev, picker) {

                $('#profit_tabs_filter').html(

                    '<i class="fa fa-calendar"></i> ' + LANG.filter_by_date

                );

                $('.nav-tabs li.active').find('a[data-toggle="tab"]').trigger('shown.bs.tab');

            });

            $('#profit_tabs_filter').data('daterangepicker').setStartDate(moment().startOf('month'));

            $('#profit_tabs_filter').data('daterangepicker').setEndDate(moment().endOf('month'));

        }

        profit_by_products_table = $('#profit_by_products_table').DataTable({

                processing: true,

                serverSide: true,

                "ajax": {

                    "url": "/reports/get-profit/product",

                    "data": function ( d ) {

                        d.start_date = $('#profit_tabs_filter')

                            .data('daterangepicker')

                            .startDate.format('YYYY-MM-DD');

                        d.end_date = $('#profit_tabs_filter')

                            .data('daterangepicker')

                            .endDate.format('YYYY-MM-DD');

                    }

                },

                columns: [

                    { data: 'product', name: 'P.name'  },

                    { data: 'gross_profit', "searchable": false},

                ],

                fnDrawCallback: function(oSettings) {

                    var total_profit = sum_table_col($('#profit_by_products_table'), 'gross-profit');

                    $('#profit_by_products_table .footer_total').text(total_profit);



                    __currency_convert_recursively($('#profit_by_products_table'));

                },

            });



        $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {

            var target = $(e.target).attr('href');

            if ( target == '#profit_by_categories') {

                if(typeof profit_by_categories_datatable == 'undefined') {

                    profit_by_categories_datatable = $('#profit_by_categories_table').DataTable({

                        processing: true,

                        serverSide: true,

                        "ajax": {

                            "url": "/reports/get-profit/category",

                            "data": function ( d ) {

                                d.start_date = $('#profit_tabs_filter')

                                    .data('daterangepicker')

                                    .startDate.format('YYYY-MM-DD');

                                d.end_date = $('#profit_tabs_filter')

                                    .data('daterangepicker')

                                    .endDate.format('YYYY-MM-DD');

                            }

                        },

                        columns: [

                            { data: 'category', name: 'C.name'  },

                            { data: 'gross_profit', "searchable": false},

                        ],

                        fnDrawCallback: function(oSettings) {

                            var total_profit = sum_table_col($('#profit_by_categories_table'), 'gross-profit');

                            $('#profit_by_categories_table .footer_total').text(total_profit);



                            __currency_convert_recursively($('#profit_by_categories_table'));

                        },

                    });

                } else {

                    profit_by_categories_datatable.ajax.reload();

                }

            } else if (target == '#profit_by_brands') {

                if(typeof profit_by_brands_datatable == 'undefined') {

                    profit_by_brands_datatable = $('#profit_by_brands_table').DataTable({

                        processing: true,

                        serverSide: true,

                        "ajax": {

                            "url": "/reports/get-profit/brand",

                            "data": function ( d ) {

                                d.start_date = $('#profit_tabs_filter')

                                    .data('daterangepicker')

                                    .startDate.format('YYYY-MM-DD');

                                d.end_date = $('#profit_tabs_filter')

                                    .data('daterangepicker')

                                    .endDate.format('YYYY-MM-DD');

                            }

                        },

                        columns: [

                            { data: 'brand', name: 'B.name'  },

                            { data: 'gross_profit', "searchable": false},

                        ],

                        fnDrawCallback: function(oSettings) {

                            var total_profit = sum_table_col($('#profit_by_brands_table'), 'gross-profit');

                            $('#profit_by_brands_table .footer_total').text(total_profit);



                            __currency_convert_recursively($('#profit_by_brands_table'));

                        },

                    });

                } else {

                    profit_by_brands_datatable.ajax.reload();

                }

            } else if (target == '#profit_by_locations') {

                if(typeof profit_by_locations_datatable == 'undefined') {

                    profit_by_locations_datatable = $('#profit_by_locations_table').DataTable({

                        processing: true,

                        serverSide: true,

                        "ajax": {

                            "url": "/reports/get-profit/location",

                            "data": function ( d ) {

                                d.start_date = $('#profit_tabs_filter')

                                    .data('daterangepicker')

                                    .startDate.format('YYYY-MM-DD');

                                d.end_date = $('#profit_tabs_filter')

                                    .data('daterangepicker')

                                    .endDate.format('YYYY-MM-DD');

                            }

                        },

                        columns: [

                            { data: 'location', name: 'L.name'  },

                            { data: 'gross_profit', "searchable": false},

                        ],

                        fnDrawCallback: function(oSettings) {

                            var total_profit = sum_table_col($('#profit_by_locations_table'), 'gross-profit');

                            $('#profit_by_locations_table .footer_total').text(total_profit);



                            __currency_convert_recursively($('#profit_by_locations_table'));

                        },

                    });

                } else {

                    profit_by_locations_datatable.ajax.reload();

                }

            } else if (target == '#profit_by_invoice') {

                if(typeof profit_by_invoice_datatable == 'undefined') {

                    profit_by_invoice_datatable = $('#profit_by_invoice_table').DataTable({

                        processing: true,

                        serverSide: true,

                        "ajax": {

                            "url": "/reports/get-profit/invoice",

                            "data": function ( d ) {

                                d.start_date = $('#profit_tabs_filter')

                                    .data('daterangepicker')

                                    .startDate.format('YYYY-MM-DD');

                                d.end_date = $('#profit_tabs_filter')

                                    .data('daterangepicker')

                                    .endDate.format('YYYY-MM-DD');

                            }

                        },

                        columns: [

                            { data: 'invoice_no', name: 'sale.invoice_no'  },

                            { data: 'final_total', name: 'final_total'  },

                            { data: 'gross_profit', "searchable": false},

                        ],

                        fnDrawCallback: function(oSettings) {

                            var total_profit = sum_table_col($('#profit_by_invoice_table'), 'gross-profit');

                            $('#profit_by_invoice_table .footer_total').text(total_profit);

                            var footer_final_total = sum_table_col($('#profit_by_invoice_table'), 'final-total');

                            $('#profit_by_invoice_table .footer_final_total').text(footer_final_total);



                            __currency_convert_recursively($('#profit_by_invoice_table'));

                        },

                    });

                } else {

                    profit_by_invoice_datatable.ajax.reload();

                }

            } else if (target == '#profit_by_date') {

                if(typeof profit_by_date_datatable == 'undefined') {

                    profit_by_date_datatable = $('#profit_by_date_table').DataTable({

                        processing: true,

                        serverSide: true,

                        "ajax": {

                            "url": "/reports/get-profit/date",

                            "data": function ( d ) {

                                d.start_date = $('#profit_tabs_filter')

                                    .data('daterangepicker')

                                    .startDate.format('YYYY-MM-DD');

                                d.end_date = $('#profit_tabs_filter')

                                    .data('daterangepicker')

                                    .endDate.format('YYYY-MM-DD');

                            }

                        },

                        columns: [

                            { data: 'transaction_date', name: 'sale.transaction_date'  },

                            { data: 'gross_profit', "searchable": false},

                        ],

                        fnDrawCallback: function(oSettings) {

                            var total_profit = sum_table_col($('#profit_by_date_table'), 'gross-profit');

                            $('#profit_by_date_table .footer_total').text(total_profit);

                            __currency_convert_recursively($('#profit_by_date_table'));

                        },

                    });

                } else {

                    profit_by_date_datatable.ajax.reload();

                }

            } else if (target == '#profit_by_customer') {

                if(typeof profit_by_customers_table == 'undefined') {

                    profit_by_customers_table = $('#profit_by_customer_table').DataTable({

                        processing: true,

                        serverSide: true,

                        "ajax": {

                            "url": "/reports/get-profit/customer",

                            "data": function ( d ) {

                                d.start_date = $('#profit_tabs_filter')

                                    .data('daterangepicker')

                                    .startDate.format('YYYY-MM-DD');

                                d.end_date = $('#profit_tabs_filter')

                                    .data('daterangepicker')

                                    .endDate.format('YYYY-MM-DD');

                            }

                        },

                        columns: [

                            { data: 'customer', name: 'CU.name'  },

                            { data: 'gross_profit', "searchable": false},

                        ],

                        fnDrawCallback: function(oSettings) {

                            var total_profit = sum_table_col($('#profit_by_customer_table'), 'gross-profit');

                            $('#profit_by_customer_table .footer_total').text(total_profit);

                            __currency_convert_recursively($('#profit_by_customer_table'));

                        },

                    });

                } else {

                    profit_by_customers_table.ajax.reload();

                }

            } else if (target == '#profit_by_day') {

                var start_date = $('#profit_tabs_filter')

                                    .data('daterangepicker')

                                    .startDate.format('YYYY-MM-DD');



                var end_date = $('#profit_tabs_filter')

                                    .data('daterangepicker')

                                    .endDate.format('YYYY-MM-DD');

                var url = '/reports/get-profit/day?start_date=' + start_date + '&end_date=' + end_date;

                $.ajax({

                        url: url,

                        dataType: 'html',

                        success: function(result) {

                           $('#profit_by_day').html(result); 

                            profit_by_days_table = $('#profit_by_day_table').DataTable({

                                    "searching": false,

                                    'paging': false,

                                    

                            });

                            var total_profit = sum_table_col($('#profit_by_day_table'), 'gross-profit');

                           $('#profit_by_day_table .footer_total').text(total_profit);

                            __currency_convert_recursively($('#profit_by_day_table'));

                        },

                    });

            } else if (target == '#profit_by_products') {

                profit_by_products_table.ajax.reload();

            }

        });

    });





</script>

@endsection