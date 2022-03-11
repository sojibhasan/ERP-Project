@extends('layouts.app')
@section('title', __('report.product_report'))

@section('content')
<!-- Main content -->
<section class="content">

    <div class="row">
        <div class="col-md-12">
            <div class="settlement_tabs">
                <ul class="nav nav-tabs">
                    @can('stock_report.view')
                    <li class="active">
                        <a href="#stock_report" class="stock_report" data-toggle="tab">
                            <i class="fa fa-file-text-o"></i> <strong>@lang('report.stock_report')</strong>
                        </a>
                    </li>
                    @endcan

                    @can('stock_adjustment_report.view')
                    <li class="">
                        <a href="#stock_adjustment_report" class="stock_adjustment_report" data-toggle="tab">
                            <i class="fa fa-file-text-o"></i> <strong>@lang('report.stock_adjustment_report')</strong>
                        </a>
                    </li>
                    @endcan

                    @can('item_report.view')
                    <li class="">
                        <a href="#items_report" class="items_report" data-toggle="tab">
                            <i class="fa fa-file-text-o"></i> <strong>@lang('report.items_report')</strong>
                        </a>
                    </li>
                    @endcan

                    @can('product_purchase_report.view')
                    <li class="">
                        <a href="#product_purchase_report" class="product_purchase_report" data-toggle="tab">
                            <i class="fa fa-file-text-o"></i> <strong>@lang('report.product_purchase_report')</strong>
                        </a>
                    </li>
                    @endcan

                    @can('product_sell_report.view')
                    <li class="">
                        <a href="#product_sell_report" class="product_sell_report" data-toggle="tab">
                            <i class="fa fa-file-text-o"></i> <strong>@lang('report.product_sell_report')</strong>
                        </a>
                    </li>
                    @endcan

                    @can('product_transaction_report.view')
                    <li class="">
                        <a href="#product_transaction_report" class="product_transaction_report" data-toggle="tab">
                            <i class="fa fa-file-text-o"></i>
                            <strong>@lang('report.product_transaction_report')</strong>
                        </a>
                    </li>
                    @endcan
                    <li class="">
                        <a href="#product_loss_excess_report" class="product_loss_excess_report" data-toggle="tab">
                            <i class="fa fa-file-text-o"></i>
                            <strong>@lang('report.product_loss_excess_report')</strong>
                        </a>
                    </li>



                </ul>
                <div class="tab-content">
                    @can('stock_report.view')
                    <div class="tab-pane active" id="stock_report">
                        @include('report.stock_report_tab')
                    </div>

                    <div class="tab-pane" id="stock_adjustment_report">
                        @include('report.stock_adjustment_report')
                    </div>
                    @endcan

                    @can('item_report.view')
                    <div class="tab-pane" id="items_report">
                        @include('report.items_report')
                    </div>
                    @endcan

                    @can('product_purchase_report.view')
                    <div class="tab-pane" id="product_purchase_report">
                        @include('report.product_purchase_report')
                    </div>
                    @endcan

                    @can('product_sell_report.view')
                    <div class="tab-pane" id="product_sell_report">
                        @include('report.product_sell_report')
                    </div>
                    @endcan

                    @can('product_transaction_report.view')
                    <div class="tab-pane" id="product_transaction_report">
                        @include('report.product_transaction_report')
                    </div>
                    @endcan
                    <div class="tab-pane" id="product_loss_excess_report">
                        @include('report.product_loss_excess_report')
                    </div>


                </div>
            </div>
        </div>
    </div>

</section>
<!-- /.content -->

@endsection
@section('javascript')
<script src="{{ asset('js/stock_adjustment.js?v=' . $asset_v) }}"></script>
<script src="{{ asset('js/report.js?v=' . $asset_v) }}"></script>
<script>
    $('#stock_report_filter_form #location_id').change(function() {
        let check_store_not = null;
		$.ajax({
			method: 'get',
			url: '/stock-transfer/get_transfer_store_id/'+$('#location_id').val(),
			data: { check_store_not: check_store_not},
			success: function(result) {
				
				$('#store_id').empty();
				$.each(result, function(i, location) {
					$('#store_id').append(`<option value= "`+location.id+`">`+location.name+`</option>`);
                });
                $("#store_id").change();
			},
        });
        
        stock_report_table.ajax.reload();
        stock_expiry_report_table.ajax.reload();
    });


$(document).ready(function(){
    summaryUpdate();
    if($('#ir_location_id > option').length <= 2 ){
        console.log($("#ir_location_id option:last").val());
        $("#ir_location_id").val($("#ir_location_id option:last").val()).trigger('change');
    }
  
    if($('#sell_location_id > option').length <= 2 ){
        console.log($("#sell_location_id option:last").val());
        $("#sell_location_id").val($("#sell_location_id option:last").val()).trigger('change');
    }
})
   
    function summaryUpdate(){
        var product_id = $('#product_list_filter_product_id').val();
        var category_id = $('#stock_report_filter_form #category_id').val();
        var sub_category_id = $('#product_list_filter_sub_category_id').val();
        var location_id = $('#stock_report_filter_form #location_id').val();
        var brand_id = $('#stock_report_filter_form #brand').val();
        var unit_id = $('#stock_report_filter_form #unit').val();
        var store_id = $('#stock_report_filter_form #store_id').val();
        let start = null;
        let end = null;
         if($('#stock_report_date_range').length){
            var stock_start = $('input#stock_report_date_range')
                .data('daterangepicker')
                .startDate.format('YYYY-MM-DD');
            var stock_end = $('input#stock_report_date_range')
                .data('daterangepicker')
                .endDate.format('YYYY-MM-DD');

        }
        var data =  { product_id: product_id, category_id: category_id, sub_category_id: sub_category_id, location_id: location_id, brand_id: brand_id, unit_id: unit_id, store_id: store_id, start_date: stock_start, end_date: stock_end };

        var loader = __fa_awesome();
         $('#stock_report').find('.opening_qty').html(loader);
         $('#stock_report').find('.opening_amount').html(loader);
         $('#stock_report').find('.purchase_qty').html(loader);
         $('#stock_report').find('.purchase_amount').html(loader);
         $('#stock_report').find('.sold_qty').html(loader);
         $('#stock_report').find('.sold_amount').html(loader);
         $('#stock_report').find('.balance_qty').html(loader);
         $('#stock_report').find('.balance_amount').html(loader);

        $.ajax({
            method: 'GET',
            url: '/reports/get-product-transaction-summary',
            dataType: 'json',
            data: data,
            success: function(data) {
                 $('#stock_report').find('.sold_qty').html(__number_f(data.sold_qty));
                 $('#stock_report').find('.purchase_qty').html(__number_f(data.purchase_qty));
                 $('#stock_report').find('.opening_qty').html(__number_f(data.opening_qty));
                 $('#stock_report').find('.balance_qty').html(__number_f(data.balance_qty));
                 $('#stock_report').find('.sold_amount').html(__currency_trans_from_en(data.sold_amount));
                 $('#stock_report').find('.purchase_amount').html(__currency_trans_from_en(data.purchase_amount));
                 $('#stock_report').find('.opening_amount').html(__currency_trans_from_en(data.opening_amount));
                 $('#stock_report').find('.balance_amount').html(__currency_trans_from_en(data.balance_amount));
            },
        });
    }


    $('.category_id, .sub_category_id').change(function(){
        var cat = $('#category_id').val();
        var sub_cat = $('#sub_category_id').val();
        $.ajax({
            method: 'POST',
            url: '/products/get_sub_categories',
            dataType: 'html',
            data: { cat_id: cat },
            success: function(result) {
                if (result) {
                    $('#sub_category_id').html(result);
                }
            },
        });
        $.ajax({
            method: 'POST',
            url: '/products/get_product_category_wise',
            dataType: 'html',
            data: { cat_id: cat , sub_cat_id: sub_cat },
            success: function(result) {
                if (result) {
                    $('#product_list_filter_product_id').html(result);
                }
            },
        });
    });
</script>

<!-- product transaction report js -->
<script>
    $(document).ready(function(){
    $('#product_transaction_report_filter_form #location_id'
    ).change(function() {
        let check_store_not = null;
		$.ajax({
			method: 'get',
			url: '/stock-transfer/get_transfer_store_id/'+$('#location_id').val(),
			data: { check_store_not: check_store_not},
			success: function(result) {
				
				$('#store_id').empty();
				$.each(result, function(i, location) {
					$('#store_id').append(`<option value= "`+location.id+`">`+location.name+`</option>`);
                });
                $("#store_id").change();
			},
        });
        
        product_transaction_report_table.ajax.reload();
    });

    if ($('#product_transaction_date_range').length == 1) {
        $('#product_transaction_date_range').daterangepicker(dateRangeSettings, function(start, end) {
            $('#product_transaction_date_range').val(
                start.format(moment_date_format) + ' ~ ' + end.format(moment_date_format)
            );
           
        });
        $('#product_transaction_date_range').on('cancel.daterangepicker', function(ev, picker) {
            $('#product_transaction_date_range').val('');
        });
        $('#product_transaction_date_range')
            .data('daterangepicker')
            .setStartDate(moment().startOf('month'));
        $('#product_transaction_date_range')
            .data('daterangepicker')
            .setEndDate(moment().endOf('month'));
    }
   
    var product_transaction_report_cols = [
            { data: 'action', name: 'action' },
            { data: 'transaction_date', name: 'transaction_date' },
            { data: 'sku', name: 'variations.sub_sku' },
            { data: 'product', name: 'p.name' },
            { data: 'description', name: 'ref_no' },
            { data: 'starting_qty', name: 'starting_qty' },
            { data: 'purchase_qty', name: 'purchase_qty' },
            { data: 'bonus_qty', name: 'bonus_qty' },
            { data: 'sold_qty', name: 'sold_qty' },
            { data: 'balance_qty', name: 'balance_qty' },
            { data: 'balance_qty_value', name: 'balance_qty_value' },
            { data: 'date', name: 'date' }
            

        ];

      //product transaction report table
      product_transaction_report_table = $('#product_transaction_report_table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '/reports/product-transaction-report',
            data: function(d) {
                d.location_id = $('#product_transaction_location_id').val();
                d.category_id = $('#product_transaction_category_id').val();
                d.sub_category_id = $('#product_transaction_sub_category_id').val();
                d.brand_id = $('#product_transaction_brand').val();
                d.unit_id = $('#product_transaction_unit').val();
                d.store_id = $('#product_transaction_store_id').val();
                d.product_id = $('#product_transaction_product').val();

                var start = $('input#product_transaction_date_range')
                    .data('daterangepicker')
                    .startDate.format('YYYY-MM-DD');
                var end = $('input#product_transaction_date_range')
                    .data('daterangepicker')
                    .endDate.format('YYYY-MM-DD');
           
                (d.start_date = start),
                (d.end_date = end);
            },
        },
        @include('layouts.partials.datatable_export_button')
        columns: product_transaction_report_cols,
        fnDrawCallback: function(oSettings) {
            $(".view-detail").on("click", viewDetail);
        },
    });
    
    function viewDetail() {
        var transType = $(this).attr("data-trans-type");
        $(".view-detail").each(function(){
            if($(this).attr("data-trans-type") != transType) {
                $(this).parent().parent().hide();
            }
        });
    }
    
    let reset_date = $('#product_transaction_date_range').val().split(' - ');
    $('.period_from').text(reset_date[0]);
    $('.period_to').text(reset_date[1]);

    $('#product_transaction_product, #product_transaction_date_range, #product_transaction_report_filter_form #location_id, #product_transaction_report_filter_form #product_transaction_category_id, #product_transaction_report_filter_form #product_transaction_sub_category_id, #product_transaction_report_filter_form #product_transaction_brand, #product_transaction_report_filter_form #product_transaction_unit,#product_transaction_report_filter_form #view_stock_filter,#product_transaction_report_filter_form #store_id '
    ).change(function() {
        product_transaction_report_table.ajax.reload();

        let reset_date = $('#product_transaction_date_range').val().split(' - ');
        $('.period_from').text(reset_date[0]);
        $('.period_to').text(reset_date[1]);

        if($('#product_transaction_product').val() !== '' && $('#product_transaction_product').val() !== undefined){
            $('.product').text($('#product_transaction_product :selected').text());
        }else{
            $('.product').text('All');
        }
        if($('#product_transaction_category_id').val() !== '' && $('#product_transaction_category_id').val() !== undefined){
            $('.category').text($('#product_transaction_category_id :selected').text());
        }else{
            $('.category').text('All');
        }
        if($('#product_transaction_sub_category_id').val() !== '' && $('#product_transaction_sub_category_id').val() !== undefined){
            $('.sub_category').text($('#product_transaction_sub_category_id :selected').text());
        }else{
            $('.sub_category').text('All');
        }
        summaryUpdateProductTransaction();
  

    });

    $('#product_transaction_category_id, #product_transaction_sub_category_id').change(function(){
        var cat = $('#product_transaction_category_id').val();
        var sub_cat = $('#product_transaction_sub_category_id').val();
        $.ajax({
            method: 'POST',
            url: '/products/get_sub_categories',
            dataType: 'html',
            data: { cat_id: cat },
            success: function(result) {
                if (result) {
                    $('#product_transaction_sub_category_id').html(result);
                }
            },
        });
        $.ajax({
            method: 'POST',
            url: '/products/get_product_category_wise',
            dataType: 'html',
            data: { cat_id: cat , sub_cat_id: sub_cat },
            success: function(result) {
                if (result) {
                    $('#product_transaction_product').html(result);
                }
            },
        });
    });

    summaryUpdateProductTransaction();
    function summaryUpdateProductTransaction(){
        var start = $('#product_transaction_date_range')
        .data('daterangepicker')
        .startDate.format('YYYY-MM-DD');
        var end = $('#product_transaction_date_range')
            .data('daterangepicker')
            .endDate.format('YYYY-MM-DD');
        var product_id = $('#product_transaction_product').val();
        var category_id = $('#product_transaction_category_id').val();
        var sub_category_id = $('#product_transaction_sub_category_id').val();
        var brand_id = $('#product_transaction_brand').val();
        var unit_id = $('#product_transaction_unit').val();
        var store_id = $('#product_transaction_store_id').val();
        var location_id = $('#product_transaction_location_id').val();

        var data = { start_date: start, end_date: end, location_id: location_id, category_id: category_id, sub_category_id: sub_category_id, brand_id: brand_id, unit_id: unit_id, store_id: store_id };

        var loader = __fa_awesome();
        $('#product_transaction_report').find('.opening_qty').html(loader);
        $('#product_transaction_report').find('.opening_amount').html(loader);
        $('#product_transaction_report').find('.purchase_qty').html(loader);
        $('#product_transaction_report').find('.purchase_amount').html(loader);
        $('#product_transaction_report').find('.sold_qty').html(loader);
        $('#product_transaction_report').find('.sold_amount').html(loader);
        $('#product_transaction_report').find('.balance_qty').html(loader);
        $('#product_transaction_report').find('.balance_amount').html(loader);

        $.ajax({
            method: 'GET',
            url: '/reports/get-product-transaction-summary',
            dataType: 'json',
            data: data,
            success: function(data) {
                $('#product_transaction_report').find('.sold_qty').html(__number_f(data.sold_qty));
                $('#product_transaction_report').find('.purchase_qty').html(__number_f(data.purchase_qty));
                $('#product_transaction_report').find('.opening_qty').html(__number_f(data.opening_qty));
                $('#product_transaction_report').find('.balance_qty').html(__number_f(data.balance_qty));
                $('#product_transaction_report').find('.sold_amount').html(__currency_trans_from_en(data.sold_amount));
                $('#product_transaction_report').find('.purchase_amount').html(__currency_trans_from_en(data.purchase_amount));
                $('#product_transaction_report').find('.opening_amount').html(__currency_trans_from_en(data.opening_amount));
                $('#product_transaction_report').find('.balance_amount').html(__currency_trans_from_en(data.balance_amount));
            },
        });
    }

    $('#product').select2();

});
    function printSummary() {
        var w = window.open('', '_self');
        var html = '<div style="width: 100%; text-align:center"><h3>{{request()->session()->get("business.name")}}</h3></div>' +document.getElementById("summary_div").innerHTML;
        $(w.document.body).html(html);
        w.print();
        w.close();
        window.location.href = "{{URL::to('/')}}/reports/product";
    }
    function printDiv() {
        $('.remove-print').removeClass('table-responsive');
        var w = window.open('', '_self');
        var html = '<div style="width: 100%; text-align:center"><h3>{{request()->session()->get("business.name")}}</h3></div>' +document.getElementById("summary_div").innerHTML  + document.getElementById("table_div").innerHTML;
        $(w.document.body).html(html);
        w.print();
        w.close();
        window.location.href = "{{URL::to('/')}}/reports/product";
    }

    //purchase report script

    $(document).ready(function(){
        let purchase_date = $('#product_pr_date_filter').val().split(' - ');
        $('.purchase_period_from').text(purchase_date[0]);
        $('.purchase_period_to').text(purchase_date[1]);
    
        $('#purchase_category_id, #purchase_sub_category_id, #purhcase_supplier_id, #purhcase_location_id, #product_pr_date_filter').change(function(){
            //set value in summary section
            get_purchase_report_summary();
    
            let purchase_date = $('#product_pr_date_filter').val().split(' - ');
            $('.purchase_period_from').text(purchase_date[0]);
            $('.purchase_period_to').text(purchase_date[1]);
            if($('#purchase_category_id').val() !== '' && $('#purchase_category_id').val() !== undefined){
                $('.purchase_category').text($('#purchase_category_id :selected').text());
            }else{
                $('.purchase_category').text('All');
            }
            if($('#purchase_sub_category_id').val() !== '' && $('#purchase_sub_category_id').val() !== undefined){
                $('.purchase_sub_category').text($('#purchase_sub_category_id :selected').text());
            }else{
                $('.purchase_sub_category').text('All');
            }
            if($('#purhcase_supplier_id').val() !== '' && $('#purhcase_supplier_id').val() !== undefined){
                $('.purchase_sub_category').text($('#purhcase_supplier_id :selected').text());
            }else{
                $('.purchase_sub_category').text('All');
            }
            if($('#purhcase_location_id').val() !== '' && $('#purhcase_location_id').val() !== undefined){
                $('.purchase_sub_category').text($('#purhcase_location_id :selected').text());
            }else{
                $('.purchase_sub_category').text('All');
            }
        });
        get_purchase_report_summary();
   });
    function get_purchase_report_summary(){
        var loader = __fa_awesome();
        $('#product_purchase_report').find('.purchase_total_qty_purcahse').html(loader);
        $('#product_purchase_report').find('.purchase_total_qty_purcahse_value').html(loader);
        $('#product_purchase_report').find('.purchase_total_qty_adjusted').html(loader);
        $('#product_purchase_report').find('.purchase_total_qty_adjusted_value').html(loader);
        let purchase_date = $('#product_pr_date_filter').val().split(' - ');
       
        let start_date =  purchase_date[0];;
        let end_date = purchase_date[1];
        let variation_id = $('#variation_id').val();
        let supplier_id = $('select#purhcase_supplier_id').val();
        let location_id = $('select#purhcase_location_id').val();
        let category_id = $('select#purchase_category_id').val();
        let sub_category_id = $('select#purchase_sub_category_id').val();

        $.ajax({
            method: 'get',
            url: '/reports/product-purchase-report-summary',
            data: {
                start_date ,
                end_date ,
                variation_id ,
                supplier_id ,
                location_id ,
                category_id ,
                sub_category_id ,
            },
            success: function(result) {
                $('.purchase_total_qty_purcahse').html(result.purchase_qty);
                $('.purchase_total_qty_purcahse_value').html(result.purchase_qty_value);
                $('.purchase_total_qty_adjusted').html(result.adjusted_qty);
                $('.purchase_total_qty_adjusted_value').html(result.adjusted_qty_value);
            },
        });
    }

    $('#purchase_category_id').change(function(){
        var cat = $('#purchase_category_id').val();
        var sub_cat = $('#purchase_sub_category_id').val();
        $.ajax({
            method: 'POST',
            url: '/products/get_sub_categories',
            dataType: 'html',
            data: { cat_id: cat },
            success: function(result) {
                if (result) {
                    $('#purchase_sub_category_id').html(result);
                }
            },
        });
    });

    function printPSummary() {
        var w = window.open('', '_self');
        var html = '<div style="width: 100%; text-align:center"><h3>{{request()->session()->get("business.name")}}</h3></div>' +document.getElementById("purchase_summary_div").innerHTML;
        $(w.document.body).html(html);
        w.print();
        w.close();
        window.location.href = "{{URL::to('/')}}/reports/product";
    }
    function printPDiv() {
        $('.remove-print').removeClass('table-responsive');
        var w = window.open('', '_self');
        var html = '<div style="width: 100%; text-align:center"><h3>{{request()->session()->get("business.name")}}</h3></div>' +document.getElementById("purchase_summary_div").innerHTML  + document.getElementById("table_div").innerHTML;
        $(w.document.body).html(html);
        w.print();
        w.close();
        window.location.href = "{{URL::to('/')}}/reports/product";
    }


    //sell report script

    $(document).ready(function(){
        let sell_date = $('#product_sr_date_filter').val().split(' - ');
        $('.sell_period_from').text(sell_date[0]);
        $('.sell_period_to').text(sell_date[1]);
    
        $('#sell_category_id, #sell_sub_category_id, #sell_customer_id, #sell_location_id, #product_sr_date_filter').change(function(){
            //set value in summary section
            get_sell_report_summary();
    console.log('asdf');
            let sell_date = $('#product_sr_date_filter').val().split(' - ');
            $('.sell_period_from').text(sell_date[0]);
            $('.sell_period_to').text(sell_date[1]);
            if($('#sell_category_id').val() !== '' && $('#sell_category_id').val() !== undefined){
                $('.sell_category').text($('#sell_category_id :selected').text());
            }else{
                $('.sell_category').text('All');
            }
            if($('#sell_sub_category_id').val() !== '' && $('#sell_sub_category_id').val() !== undefined){
                $('.sell_sub_category').text($('#sell_sub_category_id :selected').text());
            }else{
                $('.sell_sub_category').text('All');
            }
            if($('#sell_customer_id').val() !== '' && $('#sell_customer_id').val() !== undefined){
                $('.sell_customer').text($('#sell_customer_id :selected').text());
            }else{
                $('.sell_customer').text('All');
            }
            if($('#sell_location_id').val() !== '' && $('#sell_location_id').val() !== undefined){
                $('.sell_location').text($('#sell_location_id :selected').text());
            }else{
                $('.sell_location').text('All');
            }
        });
        get_sell_report_summary();
   });
    function get_sell_report_summary(){
        var loader = __fa_awesome();
        $('#product_sell_report').find('.sell_total_qty_purcahse').html(loader);
        $('#product_sell_report').find('.sell_total_qty_purcahse_value').html(loader);
        $('#product_sell_report').find('.sell_total_qty_adjusted').html(loader);
        $('#product_sell_report').find('.sell_total_qty_adjusted_value').html(loader);
        let sell_date = $('#product_sr_date_filter').val().split(' - ');
       
        let start_date =  sell_date[0];;
        let end_date = sell_date[1];
        let variation_id = $('#variation_id').val();
        let customer_id = $('select#sell_customer_id').val();
        let location_id = $('select#sell_location_id').val();
        let category_id = $('select#sell_category_id').val();
        let sub_category_id = $('select#sell_sub_category_id').val();

        $.ajax({
            method: 'get',
            url: '/reports/product-sell-report-summary',
            data: {
                start_date ,
                end_date ,
                variation_id ,
                customer_id ,
                location_id ,
                category_id ,
                sub_category_id ,
            },
            success: function(result) {
                $('.sell_total_qty_sell').html(result.sell_qty);
                $('.sell_total_qty_sell_value').html(result.sell_qty_value);
            },
        });
    }

    $('#sell_category_id').change(function(){
        var cat = $('#sell_category_id').val();
        var sub_cat = $('#sell_sub_category_id').val();
        $.ajax({
            method: 'POST',
            url: '/products/get_sub_categories',
            dataType: 'html',
            data: { cat_id: cat },
            success: function(result) {
                if (result) {
                    $('#sell_sub_category_id').html(result);
                }
            },
        });
    })

    function printPSummary() {
        var w = window.open('', '_self');
        var html = '<div style="width: 100%; text-align:center"><h3>{{request()->session()->get("business.name")}}</h3></div>' +document.getElementById("sell_summary_div").innerHTML;
        $(w.document.body).html(html);
        w.print();
        w.close();
        window.location.href = "{{URL::to('/')}}/reports/product";
    }
    function printDiv() {
        $('.remove-print').removeClass('table-responsive');
        var w = window.open('', '_self');
        var html = '<div style="width: 100%; text-align:center"><h3>{{request()->session()->get("business.name")}}</h3></div>' +document.getElementById("sell_summary_div").innerHTML  + document.getElementById("table_div").innerHTML;
        $(w.document.body).html(html);
        w.print();
        w.close();
        window.location.href = "{{URL::to('/')}}/reports/product";
    }



    //prodcut loss excess report script
    if ($('#product_loss_excess_date_range').length == 1) {
        $('#product_loss_excess_date_range').daterangepicker(dateRangeSettings, function(start, end) {
            $('#product_loss_excess_date_range').val(
                start.format(moment_date_format) + ' ~ ' + end.format(moment_date_format)
            );
           
        });
        $('#product_loss_excess_date_range').on('cancel.daterangepicker', function(ev, picker) {
            $('#product_loss_excess_date_range').val('');
        });
        $('#product_loss_excess_date_range')
            .data('daterangepicker')
            .setStartDate(moment().startOf('month'));
        $('#product_loss_excess_date_range')
            .data('daterangepicker')
            .setEndDate(moment().endOf('month'));
    }

    $(document).ready(function () {
        var product_loss_excess_report_cols = [
                { data: 'transaction_date', name: 'transaction_date' },
                { data: 'location_name', name: 'business_locations.name' },
                { data: 'product', name: 'p.name' },
                { data: 'unit', name: 'units.short_name' },
                { data: 'weight_loss_excess_qty', name: 'weight_loss_excess_qty' },
                { data: 'weight_loss_excess', name: 'weight_loss_excess' },
                { data: 'customer_name', name: 'contacts.name' },
                { data: 'invoice_no', name: 'invoice_no' },
                { data: 'final_total', name: 'final_total' }
            ];
        //product loss excess report table
        product_loss_excess_report_table = $('#product_loss_excess_report_table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '/reports/product-weight-loss-excess-report',
                data: function(d) {
                    d.location_id = $('#product_loss_excess_location_id').val();
                    d.contact_id = $('#product_loss_excess_customer').val();
                    d.type = $('#product_loss_excess_type').val();
                    d.unit_id = $('#product_loss_excess_unit').val();
                    d.product_id = $('#product_loss_excess_product').val();

                    var start = $('input#product_loss_excess_date_range')
                        .data('daterangepicker')
                        .startDate.format('YYYY-MM-DD');
                    var end = $('input#product_loss_excess_date_range')
                        .data('daterangepicker')
                        .endDate.format('YYYY-MM-DD');
            
                    d.start_date = start;
                    d.end_date = end;
                },
            },
            @include('layouts.partials.datatable_export_button')
            columns: product_loss_excess_report_cols,
            fnDrawCallback: function(oSettings) {
                __currency_convert_recursively($('#product_loss_excess_report_table'));
            },
        });
    })
    $('#product_loss_excess_product, #product_loss_excess_date_range, #product_loss_excess_report_filter_form #location_id, #product_loss_excess_report_filter_form #product_loss_excess_type, #product_loss_excess_report_filter_form #product_loss_excess_customer, #product_loss_excess_report_filter_form #product_loss_excess_brand, #product_loss_excess_report_filter_form #product_loss_excess_unit,#product_loss_excess_report_filter_form #view_stock_filter,#product_loss_excess_report_filter_form #store_id '
    ).change(function() {
        product_loss_excess_report_table.ajax.reload();
    });

    $('#product_loss_excess_category_id, #product_loss_excess_sub_category_id').change(function(){
        var cat = $('#product_loss_excess_category_id').val();
        var sub_cat = $('#product_loss_excess_sub_category_id').val();
        $.ajax({
            method: 'POST',
            url: '/products/get_sub_categories',
            dataType: 'html',
            data: { cat_id: cat },
            success: function(result) {
                if (result) {
                    $('#product_loss_excess_sub_category_id').html(result);
                }
            },
        });
        $.ajax({
            method: 'POST',
            url: '/products/get_product_category_wise',
            dataType: 'html',
            data: { cat_id: cat , sub_cat_id: sub_cat },
            success: function(result) {
                if (result) {
                    $('#product_loss_excess_product').html(result);
                }
            },
        });
    });

</script>
@endsection