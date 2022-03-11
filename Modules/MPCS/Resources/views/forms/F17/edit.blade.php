@extends('layouts.app')
@section('title', __('mpcs::lang.F17_form'))

@section('content')
<!-- Main content -->
<section class="content">
    @component('components.widget', ['class' => 'box-primary', 'title' => __(
    'mpcs::lang.f17_from')])
    @slot('tool')
    <div class="col-md-3 pull-right">
        <button type="submit" name="submit_type" id="f17_save" value="save" class="btn btn-primary pull-right"
            style="margin-left: 20px">@lang('mpcs::lang.save')</button>
    </div>
    @endslot
    <div class="table-responsive">
        <table class="table table-bordered table-striped" id="form_17_table" style="width:100%;">
            <thead>
                <tr>
                    <th>@lang('mpcs::lang.index')</th>
                    <th>@lang('mpcs::lang.product_code')</th>
                    <th>@lang('mpcs::lang.product')</th>
                    <th>@lang('mpcs::lang.current_stock')</th>
                    <th>@lang('mpcs::lang.unit_price')</th>
                    <th>@lang('mpcs::lang.select_mode')</th>
                    <th>@lang('mpcs::lang.new_price')</th>
                    <th>@lang('mpcs::lang.unit_price_difference')</th>
                    <th>@lang('mpcs::lang.price_changed_loss')</th>
                    <th>@lang('mpcs::lang.price_changed_gain')</th>
                    <th>@lang('mpcs::lang.signature')</th>
                    <th>@lang('mpcs::lang.page_no')</th>

                </tr>
            </thead>
        </table>
    </div>
    @endcomponent

    <div class="modal fade fuel_tank_modal" role="dialog" aria-labelledby="gridSystemModalLabel">
    </div>

</section>
<!-- /.content -->

@endsection
@section('javascript')
<script>
    $(document).ready(function(){
    //form_17_table 
    form_17_table = $('#form_17_table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '/mpcs/F17/{{$id}}/edit',
            data: function(d) {
                var start_date = $('input#f17_date').val()
                d.start_date = start_date;
                d.category_id = $('#product_list_filter_category_id').val();
                d.unit_id = $('#product_list_filter_unit_id').val();
                d.brand_id = $('#product_list_filter_brand_id').val();
                d.location_id = $('#location_id').val();
                d.store_id = $('#store_id').val();
            }
        },
        columns: [
            { data: 'DT_Row_Index', name: 'DT_Row_Index' , orderable: false, searchable: false},
            { data: 'sku', name: 'products.sku' },
            { data: 'product', name: 'products.name' },
            { data: 'current_stock', name: 'vld.qty_available' },
            { data: 'unit_price', name: 'variations.default_sell_price' },
            { data: 'select_mode', name: 'select_mode' },
            { data: 'new_price', name: 'new_price' },
            { data: 'unit_price_difference', name: 'unit_price_difference' },
            { data: 'price_changed_loss', name: 'price_changed_loss' },
            { data: 'price_changed_gain', name: 'price_changed_gain' },
            { data: 'signature', name: 'signature' },
            { data: 'page_no', name: 'page_no' },
        ],
        columnDefs: [
            { width: 20, targets: 6 }
        ],
    });

    $(document).on('keyup', '.new_price_value', function(){
        let tr = $(this).parent().parent();
        let unit_price =  parseFloat(tr.find('.unit_price').data('orig-value'));
        let select_mode =  tr.find('.select_mode').val();
        let current_stock =  parseFloat(tr.find('.current_stock').data('orig-value'));

        price_gain = 0;
        price_loss = 0;
        difference = 0;

        difference =parseFloat($(this).val()) - unit_price;

        if(select_mode == 'increase'){
            price_gain =  current_stock * difference;
        }
        if(select_mode == 'decrease'){
            price_loss =  current_stock * difference;
        }
       
      
        tr.find('.price_changed_loss').text(__number_f(price_loss, false, false, __currency_precision));
        tr.find('.price_changed_gain').text(__number_f(price_gain, false, false, __currency_precision));
        tr.find('.unit_price_difference').text(__number_f(difference, false, false, __currency_precision));
        tr.find('.price_changed_gain_value').val(price_gain);
        tr.find('.price_changed_loss_value').val(price_loss);
        tr.find('.unit_price_difference_value').val(difference);

    });

    $(document).on('change', '.select_mode', function(){
        let tr = $(this).parent().parent();
        tr.find('.new_price_value').trigger('keyup');
    });

    $('.f17_filter').change(function(){
        form_17_table.ajax.reload();
    });

    $('#f17_save').click(function(e){
        e.preventDefault();
        $.ajax({
            method: 'put',
            url: "{{action('\Modules\MPCS\Http\Controllers\F17FormController@update', [$id])}}",
            data: { 
                data: form_17_table.$('input, select').serialize(), 
            },
            success: function(result) {
                console.log(result);
                
                if(result.success == 0){
                    toastr.error(result.msg);
                    return false;
                }else{
                    window.location.href = '{{URL::to('/')}}/mpcs/F17';
                }
                
            },
        });
    });

});
</script>
@endsection