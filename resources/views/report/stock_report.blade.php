@extends('layouts.app')
@section('title', __('report.stock_report'))

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>{{ __('report.stock_report')}}</h1>
</section>

<!-- Main content -->
<section class="content">
    <div class="row">
        <div class="col-md-12">
            @component('components.filters', ['title' => __('report.filters')])
            {!! Form::open(['url' => action('ReportController@getStockReport'), 'method' => 'get', 'id' =>
            'stock_report_filter_form' ]) !!}
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('location_id', __('purchase.business_location') . ':') !!}
                    {!! Form::select('location_id', $business_locations, null, ['class' => 'form-control select2', 'placeholder' => __('lang_v1.all'),
                    'style' => 'width:100%']); !!}
                </div>
            </div>

            <div class="col-sm-3">
                <div class="form-group">
                    {!! Form::label('store_id', __('lang_v1.store_id').':*') !!}
                    <select name="store_id" id="store_id" class="form-control select2" required>
                        <option value="">@lang('lang_v1.all')</option>
                    </select>
                </div>
            </div>

            @if(Module::has('Manufacturing'))
            @if($mf_module)
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('only_manufactured_products', __('lang_v1.only_manufactured_products') . ':') !!}
                    {!! Form::select('only_manufactured_products', $only_manufactured_products, null, ['class' =>
                    'form-control select2', 'style' =>
                    'width:100%', 'id' => 'product_list_filter_only_manufactured_products', 'placeholder' =>
                    __('lang_v1.all')]); !!}
                </div>
            </div>
            @endif
            @endif

            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('category_id', __('category.category') . ':') !!}
                    {!! Form::select('category', $categories, null, ['placeholder' => __('messages.all'), 'class' =>
                    'form-control select2 category_id', 'style' => 'width:100%', 'id' => 'category_id']); !!}
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('sub_category_id', __('product.sub_category') . ':') !!}
                    {!! Form::select('sub_category', array(), null, ['placeholder' => __('messages.all'), 'class' =>
                    'form-control select2 sub_category_id', 'style' => 'width:100%', 'id' => 'sub_category_id']); !!}
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('product_id', __('lang_v1.products') . ':') !!}
                    {!! Form::select('product_id', $products, null, ['class' => 'form-control select2 product_id',
                    'style' =>
                    'width:100%', 'id' => 'product_list_filter_product_id', 'placeholder' => __('lang_v1.all')]); !!}
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('brand', __('product.brand') . ':') !!}
                    {!! Form::select('brand', $brands, null, ['placeholder' => __('messages.all'), 'class' =>
                    'form-control select2', 'style' => 'width:100%']); !!}
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('unit',__('product.unit') . ':') !!}
                    {!! Form::select('unit', $units, null, ['placeholder' => __('messages.all'), 'class' =>
                    'form-control select2', 'style' => 'width:100%']); !!}
                </div>
            </div>
            @if(Module::has('Manufacturing'))
            <div class="col-md-3">
                <div class="form-group">
                    <br>
                    <div class="checkbox">
                        <label>
                            {!! Form::checkbox('only_mfg', 1, false,
                            [ 'class' => 'input-icheck', 'id' => 'only_mfg_products']); !!}
                            {{ __('manufacturing::lang.only_mfg_products') }}
                        </label>
                    </div>
                </div>
            </div>
            @endif
            {!! Form::close() !!}
            @endcomponent
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            @include('report.partials.report_summary_section')

            @component('components.widget', ['class' => 'box-primary'])
            @include('report.partials.stock_report_table')
            @endcomponent
        </div>
    </div>
</section>
<!-- /.content -->

@endsection

@section('javascript')
<script src="{{ asset('js/report.js?v=' . $asset_v) }}"></script>

<script>
    $('#stock_report_filter_form #location_id'
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
        
        stock_report_table.ajax.reload();
        stock_expiry_report_table.ajax.reload();
    });

$(document).ready(function(){
    summaryUpdate();
})
   
    function summaryUpdate(){
        var product_id = $('#product_list_filter_product_id').val();
        var category_id = $('#category_id').val();
        var sub_category_id = $('#product_list_filter_sub_category_id').val();
        var location_id = $('#purchase_sell_location_filter').val();

        var data =  { product_id: product_id, category_id: category_id, sub_category_id: sub_category_id, location_id: location_id };

        var loader = __fa_awesome();
        $('.opening_qty').html(loader);
        $('.opening_amount').html(loader);
        $('.purchase_qty').html(loader);
        $('.purchase_amount').html(loader);
        $('.sold_qty').html(loader);
        $('.sold_amount').html(loader);
        $('.balance_qty').html(loader);
        $('.balance_amount').html(loader);

        $.ajax({
            method: 'GET',
            url: '/reports/get-product-transaction-summary',
            dataType: 'json',
            data: data,
            success: function(data) {
                $('.sold_qty').html(__number_f(data.sold_qty));
                $('.purchase_qty').html(__number_f(data.purchase_qty));
                $('.opening_qty').html(__number_f(data.opening_qty));
                $('.balance_qty').html(__number_f(data.balance_qty));
                $('.sold_amount').html(__currency_trans_from_en(data.sold_amount));
                $('.purchase_amount').html(__currency_trans_from_en(data.purchase_amount));
                $('.opening_amount').html(__currency_trans_from_en(data.opening_amount));
                $('.balance_amount').html(__currency_trans_from_en(data.balance_amount));
            },
        });
    }

    function printDiv() {
        $('.remove-print').removeClass('table-responsive');
        var w = window.open('', '_self');
        var html = '<div style="width: 100%; text-align:center"><h3>{{request()->session()->get("business.name")}}</h3></div>' +document.getElementById("summary_div").innerHTML  + document.getElementById("table_div").innerHTML;
        $(w.document.body).html(html);
        w.print();
        w.close();
        window.location.href = "{{URL::to('/')}}/products";
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
@endsection