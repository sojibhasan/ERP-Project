<!-- Main content -->
<section class="content">
    {!! Form::open(['action' => '\Modules\MPCS\Http\Controllers\F22FormController@printF22Form', 'method' =>
    'post', 'id' =>
    'lf_f22_form']) !!}
    {{-- <div class="row">
        <div class="col-md-12">
            @component('components.filters', ['title' => __('report.filters')])

            <div class="col-md-3" id="location_filter">
                <div class="form-group">
                    {!! Form::label('f22_location_id', __('purchase.business_location') . ':') !!}
                    {!! Form::select('f22_location_id', $business_locations, null, ['class' => 'form-control select2',
                    'style' => 'width:100%', 'placeholder' => __('lang_v1.all')]); !!}
                </div>
            </div>
            <div class="col-md-3" id="location_filter">
                <div class="form-group">
                    {!! Form::label('f22_product_id', __('mpcs::lang.product') . ':') !!}
                    {!! Form::select('f22_product_id', $products, null, ['class' => 'form-control select2',
                    'style' => 'width:100%', 'placeholder' => __('lang_v1.all')]); !!}
                </div>
            </div>


            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('type', __('mpcs::lang.form_no') . ':') !!}
                    {!! Form::text('F22_from_no', $F22_from_no, ['class' => 'form-control', 'readonly']) !!}
                </div>
            </div>


            @endcomponent
        </div>
    </div> --}}

    <div class="row">
        <div class="col-md-12">
            @component('components.widget', ['class' => 'box-primary'])
            <div class="row">
                <div class="col-md-3">
                    {{-- {!! Form::label('manager_name', __('mpcs::lang.manager_name'), ['']) !!}
                    {!! Form::text('manager_name', null, ['class' => 'form-control']) !!} --}}
                </div>
                <div class="col-md-3 pull-right">
                    <button type="submit" name="submit_type" id="lf_f22_print" value="print"
                        class="btn btn-primary pull-right">@lang('mpcs::lang.print')</button>
                </div>
            </div>
            <div class="col-md-12">
                <div class="row">
                    <div class="col-md-4"></div>
                    <div class="col-md-5">
                        <div class="text-center">
                            <h5 style="font-weight: bold;">{{request()->session()->get('business.name')}} <br>
                                <span class="lf_f22_location_name">@lang('petro::lang.all')</span></h5>
                                <input type="hidden" name="f22_location_name" id="lf_f22_location_name" value="All">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center pull-left">
                                <h5 style="font-weight: bold;" class="text-red">@lang('mpcs::lang.f22_form')
                                    @lang('mpcs::lang.form_no') : {{$last_form_no}}</h5>
                                    <input type="hidden" name="F22_from_no" id="lf_f22_form_no" value="{{$last_form_no}}">
                        </div>
                    </div>
                </div>
                <div class="row" style="margin-top: 20px;">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped" id="form_22_last_verified_table">
                            <thead>
                                <tr>
                                    <th>@lang('mpcs::lang.index_no')</th>
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
                            <tfoot class="bg-gray">
                                <tr>
                                    <td class="text-red text-bold" colspan="7">@lang('mpcs::lang.total_this_page')</td>
                                    <td class="text-red text-bold" id="lf_footer_total_purchase_price"></td>
                                    <td>&nbsp;</td>
                                    <td class="text-red text-bold" colspan="2" id="lf_footer_total_sale_price"></td>
                                </tr>
                                <tr>
                                    <td class="text-red text-bold" colspan="7">@lang('mpcs::lang.total_previous_page')
                                    </td>
                                    <td class="text-red text-bold" id="lf_pre_total_purchase_price"></td>
                                    <td>&nbsp;</td>
                                    <td class="text-red text-bold" colspan="2" id="lf_pre_total_sale_price"></td>
                                </tr>
                                <tr>
                                    <td class="text-red text-bold" colspan="7">@lang('mpcs::lang.grand_total')</td>
                                    <td class="text-red text-bold" id="lf_grand_total_purchase_price"></td>
                                    <td>&nbsp;</td>
                                    <td class="text-red text-bold" colspan="2" id="lf_grand_total_sale_price"></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
                <input type="hidden" name="purchase_price1" id="lf_purchase_price1" value="">
                <input type="hidden" name="sales_price1" id="lf_sales_price1" value="">
                <input type="hidden" name="purchase_price2" id="lf_purchase_price2" value="">
                <input type="hidden" name="sales_price2" id="lf_sales_price2" value="">
                <input type="hidden" name="purchase_price3" id="lf_purchase_price3" value="">
                <input type="hidden" name="sales_price3" id="lf_sales_price3" value="">
            </div>

            @endcomponent
        </div>
    </div>
    {!! Form::close() !!}
</section>
<!-- /.content -->