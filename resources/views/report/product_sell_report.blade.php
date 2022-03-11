<!-- Content Header (Page header) -->
<section class="content-header no-print">
    <h1>{{ __('lang_v1.product_sell_report')}}</h1>
</section>

<!-- Main content -->
<section class="content no-print">
    <div class="row">
        <div class="col-md-12">
            @component('components.filters', ['title' => __('report.filters')])
              {!! Form::open(['url' => action('ReportController@getStockReport'), 'method' => 'get', 'id' => 'product_sell_report_form' ]) !!}
                <div class="col-md-3">
                    <div class="form-group">
                    {!! Form::label('search_product', __('lang_v1.search_product') . ':') !!}
                        <div class="input-group">
                            <span class="input-group-addon">
                                <i class="fa fa-search"></i>
                            </span>
                            <input type="hidden" value="" id="sell_variation_id">
                            {!! Form::text('search_product', null, ['class' => 'form-control', 'id' => 'sell_search_product', 'placeholder' => __('lang_v1.search_product_placeholder'), 'autofocus']); !!}
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        {!! Form::label('customer_id', __('contact.customer') . ':') !!}
                        <div class="input-group">
                            <span class="input-group-addon">
                                <i class="fa fa-user"></i>
                            </span>
                            {!! Form::select('sell_customer_id', $customers, null, ['class' => 'form-control select2', 'placeholder' => __('messages.please_select'), 'required', 'style' => 'width:100%', 'id' => 'sell_customer_id']); !!}
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
                            {!! Form::select('sell_location_id', $business_locations, null, ['class' => 'form-control select2', 'placeholder' => __('lang_v1.all'), 'required', 'style' => 'width:100%', 'id' => 'sell_location_id']); !!}
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group">
                        {!! Form::label('category_id', __('category.category') . ':') !!}
                        {!! Form::select('category', $categories, null, ['placeholder' => __('messages.all'), 'class' =>
                        'form-control select2 category_id', 'style' => 'width:100%', 'id' => 'sell_category_id']); !!}
                    </div>
                </div>
                <div class="clearfix"></div>
                <div class="col-md-3">
                    <div class="form-group">
                        {!! Form::label('sub_category_id', __('product.sub_category') . ':') !!}
                        {!! Form::select('sub_category', array(), null, ['placeholder' => __('messages.all'), 'class' =>
                        'form-control select2 sub_category_id', 'style' => 'width:100%', 'id' => 'sell_sub_category_id']); !!}
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group">

                        {!! Form::label('product_sr_date_filter', __('report.date_range') . ':') !!}
                        {!! Form::text('date_range', null, ['placeholder' => __('lang_v1.select_a_date_range'), 'class' => 'form-control', 'id' => 'product_sr_date_filter', 'readonly']); !!}
                    </div>
                </div>
                {!! Form::close() !!}
            @endcomponent
        </div>
    </div>
    @include('report.partials.product_sell_summary_section')
    <div class="row">
        <div class="col-md-12">
            <div class="nav-tabs-custom">
                <ul class="nav nav-tabs">
                    <li class="active">
                        <a href="#psr_detailed_tab" data-toggle="tab" aria-expanded="true"><i class="fa fa-list" aria-hidden="true"></i> @lang('lang_v1.detailed')</a>
                    </li>

                    <li>
                        <a href="#psr_grouped_tab" data-toggle="tab" aria-expanded="true"><i class="fa fa-bars" aria-hidden="true"></i> @lang('lang_v1.grouped')</a>
                    </li>
                </ul>
                <div class="tab-content">
                    <div class="tab-pane active" id="psr_detailed_tab">
                        <div class="table-responsive remove-print">
                            <table class="table table-bordered table-striped" 
                            id="product_sell_report_table"  style="width: 100%;">
                                <thead>
                                    <tr>
                                        <th>@lang('sale.product')</th>
                                        <th>@lang('product.unit')</th>
                                        <th>@lang('product.sku')</th>
                                        <th>@lang('sale.customer_name')</th>
                                        <th>@lang('lang_v1.contact_id')</th>
                                        <th>@lang('sale.invoice_no')</th>
                                        <th>@lang('messages.date')</th>
                                        <th>@lang('sale.qty')</th>
                                        <th>@lang('sale.unit_price')</th>
                                        <th>@lang('sale.discount')</th>
                                        <th>@lang('sale.tax')</th>
                                        <th>@lang('sale.price_inc_tax')</th>
                                        <th>@lang('sale.total')</th>
                                    </tr>
                                </thead>
                                <tfoot>
                                    <tr class="bg-gray font-17 footer-total text-center">
                                        <td colspan="6"><strong>@lang('sale.total'):</strong></td>
                                        <td id="footer_total_sold_sell_report"></td>
                                        <td></td>
                                        <td></td>
                                        <td id="footer_tax"></td>
                                        <td></td>
                                        <td><span class="display_currency" id="sell_footer_subtotal" data-currency_symbol ="true"></span></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>

                    <div class="tab-pane" id="psr_grouped_tab">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped" 
                            id="product_sell_grouped_report_table" style="width: 100%;">
                                <thead>
                                    <tr>
                                        <th>@lang('sale.product')</th>
                                        <th>@lang('product.sku')</th>
                                        <th>@lang('messages.date')</th>
                                        <th>@lang('report.current_stock')</th>
                                        <th>@lang('report.total_unit_sold')</th>
                                        <th>@lang('sale.total')</th>
                                    </tr>
                                </thead>
                                <tfoot>
                                    <tr class="bg-gray font-17 footer-total text-center">
                                        <td colspan="4"><strong>@lang('sale.total'):</strong></td>
                                        <td id="footer_total_grouped_sold"></td>
                                        <td><span class="display_currency" id="footer_grouped_subtotal" data-currency_symbol ="true"></span></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- /.content -->
<div class="modal fade view_register" tabindex="-1" role="dialog" 
    aria-labelledby="gridSystemModalLabel">
</div>
