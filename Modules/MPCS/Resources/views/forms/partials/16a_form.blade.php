<!-- Main content -->
<section class="content">
    <div class="row">
        <div class="col-md-12">
            @component('components.filters', ['title' => __('report.filters')])

            <div class="col-md-3" id="location_filter">
                <div class="form-group">
                    {!! Form::label('16a_location_id', __('purchase.business_location') . ':') !!}
                    {!! Form::select('16a_location_id', $business_locations, null, ['class' => 'form-control select2',
                    'style' => 'width:100%', 'placeholder' => __('lang_v1.all')]); !!}
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('form_date_range', __('report.date_range') . ':') !!}
                    {!! Form::text('form_16a_date_range', @format_date('first day of this month') . ' ~ ' .
                    @format_date('last
                    day of this month') , ['placeholder' => __('lang_v1.select_a_date_range'), 'class' =>
                    'form-control', 'id' => 'form_16a_date_range', 'readonly']); !!}
                </div>
            </div>

            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('type', __('mpcs::lang.F16a_from_no') . ':') !!}
                    {!! Form::text('F16a_from_no', $F16a_from_no, ['class' => 'form-control', 'readonly']) !!}
                </div>
            </div>


            @endcomponent
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            @component('components.widget', ['class' => 'box-primary'])
            <div class="col-md-12">
                <div class="row">
                    <div class="col-md-4 text-red" style="margin-top: 14px;">
                        <b>@lang('petro::lang.date_range'): <span class="from_date"></span> @lang('petro::lang.to')
                            <span class="to_date"></span> </b>
                    </div>
                    <div class="col-md-5">
                        <div class="text-center">
                            <h5 style="font-weight: bold;">{{request()->session()->get('business.name')}} <br>
                                <span class="f16a_location_name">@lang('petro::lang.all')</span></h5>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="text-center pull-left">
                            <h5 style="font-weight: bold;" class="text-red">@lang('mpcs::lang.16A_form')
                                @lang('mpcs::lang.form_no') : {{$F16a_from_no}}</h5>
                        </div>
                    </div>
                </div>
                <div class="row" style="margin-top: 20px;">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped" id="form_16a_table">
                            <thead>
                                <tr>
                                    <th>@lang('mpcs::lang.index_no')</th>
                                    <th>@lang('mpcs::lang.product')</th>
                                    <th>@lang('mpcs::lang.location')</th>
                                    <th>@lang('mpcs::lang.received_qty')</th>
                                    <th>@lang('mpcs::lang.unit_purchase_price')</th>
                                    <th>@lang('mpcs::lang.total_purchase_price')</th>
                                    <th>@lang('mpcs::lang.unit_sale_price')</th>
                                    <th>@lang('mpcs::lang.total_sale_price')</th>
                                    <th>@lang('mpcs::lang.reference_no')</th>
                                    <th>@lang('mpcs::lang.stock_book_no')</th>

                                </tr>
                            </thead>
                            <tfoot class="bg-gray">
                                <tr>
                                    <td class="text-red text-bold" colspan="5">@lang('mpcs::lang.total_this_page')</td>
                                    <td class="text-red text-bold" id="footer_F16A_total_purchase_price"></td>
                                    <td>&nbsp;</td>
                                    <td class="text-red text-bold" colspan="3" id="footer_F16A_total_sale_price"></td>
                                </tr>
                                <tr>
                                    <td class="text-red text-bold" colspan="5">@lang('mpcs::lang.total_previous_page')
                                    </td>
                                    <td class="text-red text-bold" id="pre_F16A_total_purchase_price">0.00</td>
                                    <td>&nbsp;</td>
                                    <td class="text-red text-bold" colspan="3" id="pre_F16A_total_sale_price">0.00</td>
                                </tr>
                                <tr>
                                    <td class="text-red text-bold" colspan="5">@lang('mpcs::lang.grand_total')</td>
                                    <td class="text-red text-bold" id="grand_F16A_total_purchase_price"></td>
                                    <td>&nbsp;</td>
                                    <td class="text-red text-bold" colspan="3" id="grand_F16A_total_sale_price"></td>
                                </tr>
                                <input type="hidden" name="total_this_p" id="total_this_p" value="0">
                                <input type="hidden" name="total_this_s" id="total_this_s" value="0">
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
            @endcomponent
        </div>
    </div>

</section>
<!-- /.content -->