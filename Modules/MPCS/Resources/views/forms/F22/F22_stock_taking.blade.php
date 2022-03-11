@extends('layouts.app')
@section('title', __('mpcs::lang.F22StockTaking_form'))

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1> @lang('mpcs::lang.F22StockTaking_form')
        <small>@lang( 'mpcs::lang.F22StockTaking_form', ['contacts' => __('mpcs::lang.mange_F22StockTaking_form')
            ])</small>
    </h1>
</section>

<!-- Main content -->
<section class="content">
    <div class="row">
        <div class="col-md-12">
            <div class="settlement_tabs">
                <ul class="nav nav-tabs">
                    <li class="active">
                        <a href="#f22_form_tab" class="f22_form_tab" data-toggle="tab">
                            <i class="fa fa-file-text-o"></i> <strong>@lang('mpcs::lang.f22_form')</strong>
                        </a>
                    </li>

                    <li>
                        <a href="#f22_last_verified_stock_tab" class="f22_last_verified_stock_tab" style=""
                            data-toggle="tab">
                            <i class="fa fa-check"></i> <strong>
                                @lang('mpcs::lang.f22_last_verified_stock') </strong>
                        </a>
                    </li>

                    <li>
                        <a href="#list_f22_stock_taking_tab" class="list_f22_stock_taking_tab" style=""
                            data-toggle="tab">
                            <i class="fa fa-sign-in"></i> <strong>
                                @lang('mpcs::lang.list_f22_stock_taking') </strong>
                        </a>
                    </li>


                </ul>
                <div class="tab-content">
                    <div class="tab-pane active" id="f22_form_tab">
                        @include('mpcs::forms.F22.partials.f22_form')
                    </div>

                    <div class="tab-pane" id="f22_last_verified_stock_tab">
                        @include('mpcs::forms.F22.partials.f22_last_verified_stock')
                    </div>

                    <div class="tab-pane" id="list_f22_stock_taking_tab">
                        @include('mpcs::forms.F22.partials.list_f22_stock_taking')
                    </div>
                </div>
            </div>
        </div>
    </div>


</section>
<!-- /.content -->

@endsection
@section('javascript')
<script type="text/javascript">
    $('#f22_product_id').select2();
  $('#form_date_range').daterangepicker({
        ranges: ranges,
        autoUpdateInput: false,
        locale: {
            format: moment_date_format,
            cancelLabel: LANG.clear,
            applyLabel: LANG.apply,
            customRangeLabel: LANG.custom_range,
        },
    });
    $('#form_date_range').on('apply.daterangepicker', function(ev, picker) {
        $(this).val(
            picker.startDate.format(moment_date_format) +
                ' - ' +
                picker.endDate.format(moment_date_format)
        );
    });

    $('#form_date_range').on('cancel.daterangepicker', function(ev, picker) {
        $(this).val('');
    });

    
    $('#form_f22_date_range').daterangepicker();
    if ($('#form_f22_date_range').length == 1) {
        $('#form_f22_date_range').daterangepicker(dateRangeSettings, function(start, end) {
            $('#form_f22_date_range').val(
                start.format(moment_date_format) + ' - ' + end.format(moment_date_format)
            );
        });
        $('#form_f22_date_range').on('cancel.daterangepicker', function(ev, picker) {
            $('#product_sr_date_filter').val('');
        });
        $('#form_f22_date_range')
            .data('daterangepicker')
            .setStartDate(moment().startOf('month'));
        $('#form_f22_date_range')
            .data('daterangepicker')
            .setEndDate(moment().endOf('month'));
    }

  

$('#f22_location_id option:eq(1)').attr('selected', true);
$(document).ready(function(){
     form_f22_list_table = $('#form_f22_list_table').DataTable({
        processing: true,
        serverSide: false,
        ajax: {
            url: '/mpcs/get-form-f22-list',
            data: function(d) {
                // d.location_id = $('#f22_location_id').val();
                // d.product_id = $('#f22_product_id').val();
            }
        },
        columns: [
            { data: 'created_at', name: 'created_at' },
            { data: 'locations_name', name: 'location' },
            { data: 'form_no', name: 'form_no' },
            { data: 'username', name: 'username' },
            { data: 'action', name: 'action' },
        
        ],
        fnDrawCallback: function(oSettings) {

        },
    });

    $('#f22_product_id, #f22_location_id').change(function(){
        form_22_table.ajax.reload();
        if($('#f22_location_id').val() !== ''  && $('#f22_location_id').val() !== undefined){
            $('.f22_location_name').text($('#f22_location_id :selected').text());
            $('#f22_location_name').val($('#f22_location_id :selected').text());
        }else{
            $('.f22_location_name').text('All');
            $('#f22_location_name').val('All');
        }
    });


    var ppage_totals = [];
    var spage_totals = [];
    var pre_gppage_totals = [];
    var pre_gspage_totals = [];
   
     //form_22_table 
     form_22_table = $('#form_22_table').DataTable({
        processing: true,
        serverSide: false,
        lengthChange: false,
        pageLength: {{!empty($settings->F22_no_of_product_per_page) ? $settings->F22_no_of_product_per_page : 25}},
        columnDefs: [ {
            "targets": 0,
            "orderable": false
        } ],
        ajax: {
            url: '/mpcs/get-form-f22',
            data: function(d) {
                d.location_id = $('#f22_location_id').val();
                d.product_id = $('#f22_product_id').val();
            }
        },
        "columnDefs": [
            { "width": "2%", "targets": 2 }
        ],
        columns: [
            { data: 'DT_Row_Index', name: 'DT_Row_Index' },
            { data: 'sku', name: 'sku' },
            { data: 'book_no', name: 'book_no' },
            { data: 'product', name: 'product' },
            { data: 'current_stock', name: 'current_stock' },
            { data: 'stock_count', name: 'stock_count' },
            { data: 'unit_purchase_price', name: 'unit_purchase_price' },
            { data: 'total_purchase_price', name: 'total_purchase_price' },
            { data: 'unit_sale_price', name: 'unit_sale_price' },
            { data: 'total_sale_price', name: 'total_sale_price' },
            { data: 'qty_difference', name: 'qty_difference' },
        ],
        fnDrawCallback: function(oSettings) {
            var total_purchase_price = sum_table_col($('#form_22_table'), 'total_purchase_price');
            $('#footer_total_purchase_price').text(total_purchase_price);
            var total_sale_price = sum_table_col($('#form_22_table'), 'total_sale_price');
            $('#footer_total_sale_price').text(total_sale_price);
         
        },
        "initComplete": function(settings, json) {
            var table_info = form_22_table.page.info(); //get table info
             for( i = 0; i < table_info.pages ; i++){
                ppage_totals[i] = 0.00;
                spage_totals[i] = 0.00;
                pre_gppage_totals[i] = 0.00;
                pre_gspage_totals[i] = 0.00;
                
            }
        },
        fnDrawCallback: function(oSettings) {
            __currency_convert_recursively($('#form_22_table'));
        }
    });

    $(document).on('keyup', '.stock_count', function(){
        let tr = $(this).parent().parent();
       let unit_purchase_price =  parseFloat(tr.find('.unit_purchase_price').data('orig-value'));
       let unit_sale_price =  parseFloat(tr.find('.unit_sale_price').data('orig-value'));
       let current_stock =  parseFloat(tr.find('.current_stock').data('orig-value'));
       let stock = parseFloat($(this).val());

       let total_purhcase_value = unit_purchase_price * stock;
       let total_sale_value = unit_sale_price * stock;
       let qty_difference = current_stock - stock;
       tr.find('.total_purchase_price').text(__number_f(total_purhcase_value, false, false, __currency_precision));
       tr.find('.total_purchase_price').data('orig-value',total_purhcase_value);
       tr.find('.total_purhcase_value').val(total_purhcase_value);
       tr.find('.total_sale_price').text(__number_f(total_sale_value, false, false, __currency_precision));
       tr.find('.total_sale_price').data('orig-value', total_sale_value);
       tr.find('.total_sale_value').val(total_sale_value);
       tr.find('.qty_difference').val(qty_difference);
       
       calculateTotals();
    });
   
 
    function calculateTotals  ( page_change =null ){
        let pgrand = 0.00;
        let sgrand = 0.00;
        let total_purchase_amount = sum_table_col($('#form_22_table'), 'total_purchase_price');
        let total_sales_amount = sum_table_col($('#form_22_table'), 'total_sale_price');
      
       let info = form_22_table.page.info(); //get table info
  
        if(page_change == 1){
            if(info.page == 0){
                pgrand = ppage_totals[info.page];
                sgrand = spage_totals[info.page];
            }else{
                pgrand = ppage_totals[info.page] + pre_gppage_totals[info.page - 1];
                sgrand = spage_totals[info.page] + pre_gspage_totals[info.page - 1];
            }
        
            pre_gppage_totals[info.page] = pgrand;
            pre_gspage_totals[info.page] = sgrand;
        }else{
            ppage_totals[info.page] = total_purchase_amount;
            spage_totals[info.page] = total_sales_amount;
            if(info.page == 0){
                pgrand = ppage_totals[info.page];
                sgrand = spage_totals[info.page];
            }else{
                pgrand = ppage_totals[info.page] + pre_gppage_totals[info.page - 1];
                sgrand = spage_totals[info.page] + pre_gspage_totals[info.page - 1];
            }
        
            pre_gppage_totals[info.page] = pgrand;
            pre_gspage_totals[info.page] = sgrand;
        }
       $('#purchase_price1').val(total_purchase_amount);
       $('#purchase_price3').val(total_purchase_amount);
       $('#sales_price1').val(total_sales_amount);
       $('#sales_price3').val(total_sales_amount);
            $('#footer_total_purchase_price').text(__number_f(ppage_totals[info.page], false, false, __currency_precision));
            $('#footer_total_sale_price').text(__number_f(spage_totals[info.page], false, false, __currency_precision));
            $('#pre_total_purchase_price').text(__number_f(pre_gppage_totals[info.page - 1], false, false, __currency_precision));
            $('#pre_total_sale_price').text(__number_f(pre_gspage_totals[info.page - 1], false, false, __currency_precision));
            $('#grand_total_purchase_price').text(__number_f(pgrand , false, false, __currency_precision));
            $('#grand_total_sale_price').text(__number_f(sgrand , false, false, __currency_precision));
    }

    $('#form_22_table').on( 'page.dt', function () {
        calculateTotals(1);
    });

     //form_22_table 
    var lf_ppage_totals = [];  
    var lf_spage_totals = [];
    var lf_pre_gppage_totals = [];
    var lf_pre_gspage_totals = [];
     form_22_last_verified_table = $('#form_22_last_verified_table').DataTable({
        processing: true,
        serverSide: false,
        pageLength: {{!empty($settings->F22_no_of_product_per_page) ? $settings->F22_no_of_product_per_page : 25}},
        ajax: {
            url: '/mpcs/get-last-verified-form-f22',
            data: function(d) {
            }
        },
        "columnDefs": [
            { "width": "2%", "targets": 2 }
        ],
        columns: [
            { data: 'DT_Row_Index', name: 'DT_Row_Index' },
            { data: 'sku', name: 'sku' },
            { data: 'book_no', name: 'book_no' },
            { data: 'product', name: 'product' },
            { data: 'current_stock', name: 'current_stock' },
            { data: 'stock_count', name: 'stock_count' },
            { data: 'unit_purchase_price', name: 'unit_purchase_price' },
            { data: 'total_purchase_price', name: 'total_purchase_price' },
            { data: 'unit_sale_price', name: 'unit_sale_price' },
            { data: 'total_sale_price', name: 'total_sale_price' },
            { data: 'qty_difference', name: 'qty_difference' },
        ],
        fnDrawCallback: function(oSettings) {
         
        },
        "initComplete": function(settings, json) {
            var table_info = form_22_last_verified_table.page.info(); //get table info
             for( i = 0; i < table_info.pages ; i++){
                lf_ppage_totals[i] = 0.00;
                lf_spage_totals[i] = 0.00;
                lf_pre_gppage_totals[i] = 0.00;
                lf_pre_gspage_totals[i] = 0.00;
                
            }
        }
    });
 
    $('#form_22_last_verified_table').on( 'page.dt', function () {
        lastFormCalculateTotals(1);
    });
    $('#form_22_last_verified_table').on( 'init.dt', function () {
        lastFormCalculateTotals();
    }).dataTable();

    function lastFormCalculateTotals  ( page_change =null ){
        let lf_pgrand = 0.00;
        let lf_sgrand = 0.00;
        let total_purchase_amount = sum_table_col($('#form_22_last_verified_table'), 'lf_total_purchase_price');
        let total_sales_amount = sum_table_col($('#form_22_last_verified_table'), 'lf_total_sale_price');
      
       let info = form_22_last_verified_table.page.info(); //get table info
  
        if(page_change == 1){
            lf_ppage_totals[info.page] = total_purchase_amount;
            lf_spage_totals[info.page] = total_sales_amount;
            if(info.page == 0){
                lf_pgrand = lf_ppage_totals[info.page];
                lf_sgrand = lf_spage_totals[info.page];
            }else{
                lf_pgrand = lf_ppage_totals[info.page] + lf_pre_gppage_totals[info.page - 1];
                lf_sgrand = lf_spage_totals[info.page] + lf_pre_gspage_totals[info.page - 1];
            }
        
            lf_pre_gppage_totals[info.page] = lf_pgrand;
            lf_pre_gspage_totals[info.page] = lf_sgrand;
            
        }
        else{
            lf_ppage_totals[info.page] = total_purchase_amount;
            lf_spage_totals[info.page] = total_sales_amount;
            if(info.page == 0){
                lf_pgrand = lf_ppage_totals[info.page];
                lf_sgrand = lf_spage_totals[info.page];
            }else{
                lf_pgrand = lf_ppage_totals[info.page] + lf_pre_gppage_totals[info.page - 1];
                lf_sgrand = lf_spage_totals[info.page] + lf_pre_gspage_totals[info.page - 1];
            }
        
            lf_pre_gppage_totals[info.page] = lf_pgrand;
            lf_pre_gspage_totals[info.page] = lf_sgrand;
        }
       
        
        $('#lf_footer_total_purchase_price').text(__number_f(lf_ppage_totals[info.page], false, false, __currency_precision));
        $('#lf_footer_total_sale_price').text(__number_f(lf_spage_totals[info.page], false, false, __currency_precision));
        $('#lf_pre_total_purchase_price').text(__number_f(lf_pre_gppage_totals[info.page - 1], false, false, __currency_precision));
        $('#lf_pre_total_sale_price').text(__number_f(lf_pre_gspage_totals[info.page - 1], false, false, __currency_precision));
        $('#lf_grand_total_purchase_price').text(__number_f(lf_pgrand , false, false, __currency_precision));
        $('#lf_grand_total_sale_price').text(__number_f(lf_sgrand , false, false, __currency_precision));
    }
  

    $('#f22_save_and_print').click(function(e){
        e.preventDefault();
        $(this).attr('disabled', 'disabled');
        $.ajax({
            method: 'post',
            url: '/mpcs/save-form-f22',
            // data: { data: $('#f22_form').serialize() },
            data: { data: form_22_table.$('input, select').serialize() + $('#f22_form').serialize() },
            success: function(result) {
                if(result.success == 0){
                    toastr.error(result.msg);

                    return false;
                }

                printPage(result);
                
            },
        });
    })
    $('#f22_print').click(function(e){
        e.preventDefault();
        $.ajax({
            method: 'post',
            url: '/mpcs/print-form-f22',
            data: { data: form_22_table.$('input, select').serialize() + $('#f22_form').serialize() },
            success: function(result) {
                if(result.success == 0){
                    toastr.error(result.msg);

                    return false;
                }
                onlyPrintPage(result);
                
            },
        });
    });
    $('#lf_f22_print').click(function(e){
        e.preventDefault();
        $.ajax({
            method: 'post',
            url: '/mpcs/print-form-f22',
            data: { data: form_22_last_verified_table.$('input, select').serialize() + $('#lf_f22_form').serialize() },
            success: function(result) {
                if(result.success == 0){
                    toastr.error(result.msg);

                    return false;
                }
                printPage(result);
                
            },
        });
    });
    $(document).on('click', '.reprint_form', function(e){
        e.preventDefault();
        href= $(this).data('href');
        console.log(href);
        
        $.ajax({
            method: 'get',
            url: href,
            data: { },
            success: function(result) {
                if(result.success == 0){
                    toastr.error(result.msg);

                    return false;
                }
                printPage(result);
                
            },
        });
    });


});

function printPage(content) {
		var w = window.open('', '_self');
		$(w.document.body).html(content);
		w.print();
		w.close();
        window.location.href = "{{URL::to('/')}}/mpcs/F22_stock_taking";
	}
function onlyPrintPage(content) {
		var w = window.open('', '_blank');
		$(w.document.body).html(`@include('layouts.partials.css')` + content);
		w.print();
		w.close();
        return false;
	}

</script>
@endsection