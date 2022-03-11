<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>{{ __('lang_v1.product_purchase_report')}}</h1>
</section>

<!-- Main content -->
<section class="content">
    <div class="row">
        <div class="col-md-12">
            @component('components.filters', ['title' => __('report.filters')])
          {!! Form::open(['url' => action('ReportController@getStockReport'), 'method' => 'get', 'id' => 'product_purchase_report_form' ]) !!}
            <div class="col-md-3">
                <div class="form-group">
                {!! Form::label('search_product', __('lang_v1.search_product') . ':') !!}
                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="fa fa-search"></i>
                        </span>
                        <input type="hidden" value="" id="purchase_variation_id">
                        {!! Form::text('search_product', null, ['class' => 'form-control', 'id' => 'purchase_search_product', 'placeholder' => __('lang_v1.search_product_placeholder'), 'autofocus']); !!}
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('supplier_id', __('purchase.supplier') . ':') !!}
                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="fa fa-user"></i>
                        </span>
                        {!! Form::select('purhcase_supplier_id', $suppliers, null, ['class' => 'form-control select2', 'placeholder' => __('messages.all'), 'required', 'style' => 'width:100%', 'id' => 'purhcase_supplier_id']); !!}
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('location_id', __('purchase.business_location').':') !!}
                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="fa fa-map-marker"></i>
                        </span>
                        {!! Form::select('purhcase_location_id', $business_locations, null, ['class' => 'form-control select2', 'placeholder' => __('messages.all'), 'required', 'style' => 'width:100%', 'id' => 'purhcase_location_id']); !!}
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('category_id', __('category.category') . ':') !!}
                    {!! Form::select('category', $categories, null, ['placeholder' => __('messages.all'), 'class' =>
                    'form-control select2 category_id', 'style' => 'width:100%', 'id' => 'purchase_category_id']); !!}
                </div>
            </div>
            <div class="clearfix"></div>
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('sub_category_id', __('product.sub_category') . ':') !!}
                    {!! Form::select('sub_category', array(), null, ['placeholder' => __('messages.all'), 'class' =>
                    'form-control select2 sub_category_id', 'style' => 'width:100%', 'id' => 'purchase_sub_category_id']); !!}
                </div>
            </div>

            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('product_pr_date_filter', __('report.date_range') . ':') !!}
                    {!! Form::text('date_range', null, ['placeholder' => __('lang_v1.select_a_date_range'), 'class' => 'form-control', 'id' => 'product_pr_date_filter', 'readonly']); !!}
                </div>
            </div>
            {!! Form::close() !!}
            @endcomponent
        </div>
    </div>
    @include('report.partials.product_purchase_summary_section')
    <div class="row">
        <div class="col-md-12">
            @component('components.widget', ['class' => 'box-primary'])
                <div class="table-responsive remove-print">
                    <table class="table table-bordered table-striped" 
                    id="product_purchase_report_table"  style="width: 100%;">
                        <thead>
                            <tr>
                                <th>@lang('sale.product')</th>
                                <th>@lang('product.unit')</th>
                                <th>@lang('purchase.supplier')</th>
                                <th>@lang('purchase.add_purchase_number')</th>
                                <th>@lang('purchase.ref_no')</th>
                                <th>@lang('messages.date')</th>
                                <th>@lang('sale.qty')</th>
                                <th>@lang('lang_v1.total_unit_adjusted')</th>
                                <th>@lang('lang_v1.unit_perchase_price')</th>
                                <th>@lang('sale.subtotal')</th>
                            </tr>
                        </thead>
                        <tfoot>
                            <tr class="bg-gray font-17 footer-total text-center">
                                <td colspan="5"><strong>@lang('sale.total'):</strong></td>
                                <td id="footer_total_purchase"></td>
                                <td id="footer_total_adjusted"></td>
                                <td></td>
                                <td><span class="display_currency" id="purchase_footer_subtotal" data-currency_symbol ="true"></span></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            @endcomponent
        </div>
    </div>
</section>
<!-- /.content -->
<div class="modal fade view_register" tabindex="-1" role="dialog" 
    aria-labelledby="gridSystemModalLabel">
</div>
