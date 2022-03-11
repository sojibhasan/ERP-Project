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
            @component('components.widget', ['class' => 'box-primary'])
            <div class="row">
                <div class="col-md-3">
                    {{-- {!! Form::label('manager_name', __('mpcs::lang.manager_name'), ['']) !!}
                    {!! Form::text('manager_name', null, ['class' => 'form-control']) !!} --}}
                </div>
                <div class="col-md-3 pull-right">
                    <button type="submit" name="submit_type" id="f22_save_and_print" value="save_and_print"
                        class="btn btn-primary pull-right"
                        style="margin-left: 20px">@lang('mpcs::lang.update_and_print')</button>
                </div>
            </div>
            <div class="col-md-12">
                {{-- <div class="row">
                    <div class="col-md-4"></div>
                    <div class="col-md-5">
                        <div class="text-center">
                            <h5 style="font-weight: bold;">{{request()->session()->get('business.name')}} <br>
                                <span class="f22_location_name">@lang('petro::lang.all')</span></h5>
                                <input type="hidden" name="f22_location_name" id="f22_location_name" value="All">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="text-center pull-left">
                            <h5 style="font-weight: bold;" class="text-red">@lang('mpcs::lang.f22_form')
                                @lang('mpcs::lang.form_no') : {{$F22_from_no}}</h5>
                        </div>
                    </div>
                </div> --}}
                {!! Form::close() !!}
                <div class="row" style="margin-top: 20px;">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped" id="form_22_table">
                            <thead>
                                <tr>
                                    <th>@lang('mpcs::lang.code')</th>
                                    <th>@lang('mpcs::lang.book_no')</th>
                                    <th>@lang('mpcs::lang.product')</th>
                                    <th>@lang('mpcs::lang.current_stock')</th>
                                    <th>@lang('mpcs::lang.stock_count')</th>
                                    <th>@lang('mpcs::lang.unit_purchase_price')</th>
                                    <th>@lang('mpcs::lang.total_purchase_price')</th>
                                    <th>@lang('mpcs::lang.unit_sale_price')</th>
                                    <th>@lang('mpcs::lang.total_sale_price')</th>
                                    <th>@lang('mpcs::lang.qty_difference')</th>

                                </tr>
                            </thead>
                            <tfoot>
                                <tr class="bg-gray">
                                    <td class="text-red text-bold" colspan="6">@lang('mpcs::lang.total_this_page')</td>
                                    <td class="text-red text-bold" id="footer_total_purchase_price"></td>
                                    <td>&nbsp;</td>
                                    <td class="text-red text-bold" colspan="2" id="footer_total_sale_price"></td>
                                </tr>
                                <tr class="bg-gray">
                                    <td class="text-red text-bold" colspan="6">@lang('mpcs::lang.total_previous_page')
                                    </td>
                                    <td class="text-red text-bold" id="pre_total_purchase_price"></td>
                                    <td>&nbsp;</td>
                                    <td class="text-red text-bold" colspan="2" id="pre_total_sale_price"></td>
                                </tr>
                                <tr class="bg-gray">
                                    <td class="text-red text-bold" colspan="6">@lang('mpcs::lang.grand_total')</td>
                                    <td class="text-red text-bold" id="grand_total_purchase_price"></td>
                                    <td>&nbsp;</td>
                                    <td class="text-red text-bold" colspan="2" id="grand_total_sale_price"></td>
                                </tr>
                                <tr>
                                    <td colspan="11"> @lang('mpcs::lang.confirm_f22')</td>
                                </tr>
                                <tr>
                                    <td colspan="6"><h5 style="font-weight: bold; margin-bottom: 0px; ">
                                        @lang('mpcs::lang.checked_by'): ____________</h5></td>
                                        <td colspan="4"><h5 style="font-weight: bold; margin-bottom: 0px; ">
                                            @lang('mpcs::lang.received_by'): ____________</h5> <br></td>
                                </tr>
                                <tr>
                                    <td colspan="6"> <h5 style="font-weight: bold; margin-bottom: 0px; ">
                                        @lang('mpcs::lang.signature_of_manager'): ____________</h5></td>
                                        <td colspan="4">
                                            <h5 style="font-weight: bold; margin-bottom: 0px; ">
                                                @lang('mpcs::lang.handed_over_by'): ____________</h5>
                                        </td>
                                </tr>
                                <tr>
                                    <td colspan="11"> <h5 style="font-weight: bold; margin-top: 10px; ">@lang('mpcs::lang.user'):
                                        {{auth()->user()->username }}</h5></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
                <input type="hidden" name="purchase_price1" id="purchase_price1" value="">
                <input type="hidden" name="sales_price1" id="sales_price1" value="">
                <input type="hidden" name="purchase_price2" id="purchase_price2" value="">
                <input type="hidden" name="sales_price2" id="sales_price2" value="">
                <input type="hidden" name="purchase_price3" id="purchase_price3" value="">
                <input type="hidden" name="sales_price3" id="sales_price3" value="">
            </div>

            @endcomponent
            
        </div>
    </div>


</section>
<!-- /.content -->

@endsection
@section('javascript')
<script type="text/javascript">
  

$(document).ready(function(){
   
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
        columnDefs: [ {
            "targets": 0,
            "orderable": false
        } ],
        ajax: {
            url: '/mpcs/edit-form-f22/{{$id}}',
            data: function(d) {
                d.location_id = $('#f22_location_id').val();
                d.product_id = $('#f22_product_id').val();
            }
        },
        "columnDefs": [
            { "width": "2%", "targets": 2 }
        ],
        columns: [
            { data: 'sku', name: 'sku' },
            { data: 'book_no', name: 'book_no' },
            { data: 'product', name: 'product' },
            { data: 'current_stock', name: 'current_stock' },
            { data: 'stock_count', name: 'stock_count' },
            { data: 'unit_purchase_price', name: 'unit_purchase_price' },
            { data: 'total_purchase_price', name: 'total_purchase_price' },
            { data: 'unit_sale_price', name: 'unit_sale_price' },
            { data: 'total_sale_price', name: 'total_sale_price' },
            { data: 'qty_difference', name: 'qty_difference' }
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
    $('#form_22_table').on( 'init.dt', function () {
        $('.stock_count').each(function(){
            if(parseFloat($(this).val()) > 0) {
                $(this).trigger('keyup');
            }
        });
    });

   

    $('#f22_save_and_print').click(function(e){
        e.preventDefault();
        $.ajax({
            method: 'put',
            url: '/mpcs/update-form-f22/{{$id}}',
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

});

function printPage(content) {
    var w = window.open('', '_self');
    $(w.document.body).html(content);
    w.print();
    w.close();
    window.location.href = "{{URL::to('/')}}/mpcs/F22_stock_taking";
}
</script>
@endsection