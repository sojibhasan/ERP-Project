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
                    {!! Form::select('location_id', $business_locations, null, ['class' => 'form-control select2',
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
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('stock_report_date_range', __('lang_v1.sell_date') . ':') !!}
                    {!! Form::text('stock_report_date_range', null, ['placeholder' => __('lang_v1.select_a_date_range'), 'class' => 'form-control', 'readonly']); !!}
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