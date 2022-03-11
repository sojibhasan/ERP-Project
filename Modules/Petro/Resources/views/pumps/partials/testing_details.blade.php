<!-- Main content -->
<section class="content">
    <div class="row">
        <div class="col-md-12">
            @component('components.filters', ['title' => __('report.filters')])
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('location_id', __('purchase.business_location') . ':') !!}
                    {!! Form::select('testing_details_location_id', $business_locations, null, ['class' => 'form-control
                    select2',
                    'placeholder' => __('petro::lang.all'), 'id' => 'testing_details_location_id', 'style' =>
                    'width:100%']); !!}
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('pump_operators', __('petro::lang.pump_operator') . ':') !!}
                    {!! Form::select('testing_details_pump_operators', $pump_operators, null, ['class' => 'form-control
                    select2', 'placeholder'
                    => __('petro::lang.all'), 'id' => 'testing_details_pump_operators', 'style' => 'width:100%']); !!}
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('settlement_no', __('petro::lang.settlement_no') . ':') !!}
                    {!! Form::select('testing_details_settlement_no', $settlement_nos, null, ['class' => 'form-control
                    select2', 'placeholder'
                    => __('petro::lang.all'), 'id' => 'testing_details_settlement_no', 'style' => 'width:100%']); !!}
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('pumps', __('petro::lang.pumps') . ':') !!}
                    {!! Form::select('testing_details_pumps', $pumps, null, ['class' => 'form-control select2',
                    'placeholder'
                    => __('petro::lang.all'), 'id' => 'testing_details_pumps', 'style' => 'width:100%']); !!}
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('products', __('petro::lang.products') . ':') !!}
                    {!! Form::select('testing_details_product_id', $products, null, ['class' => 'form-control select2',
                    'placeholder'
                    => __('petro::lang.all'), 'id' => 'testing_details_product_id', 'style' => 'width:100%']); !!}
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('testing_details_date_range', __('report.date_range') . ':') !!}
                    {!! Form::text('testing_details_date_range', @format_date('first day of this month') . ' ~ ' .
                    @format_date('last
                    day of this month') , ['placeholder' => __('lang_v1.select_a_date_range'), 'class' =>
                    'form-control', 'id' => 'testing_details_date_range', 'readonly']); !!}
                </div>
            </div>
            @endcomponent
        </div>
    </div>

    @component('components.widget', ['class' => 'box-primary', 'title' => __('petro::lang.testing_details')])
    <div class="col-md-12">
        <div class="row">
            <div class="col-md-5 text-red" style="margin-top: 14px;">
                <b>@lang('petro::lang.date_range'): <span class="testing_details_from_date"></span>
                    @lang('petro::lang.to') <span class="testing_details_to_date"></span> </b>
            </div>
            <div class="col-md-7">
                <div class="text-center pull-left">
                    <h5 style="font-weight: bold;">{{request()->session()->get('business.name')}} <br>
                        <span class="testing_details_location_name">@lang('petro::lang.all')</span></h5>
                </div>
            </div>
        </div>
        <div class="row" style="margin-top: 20px;">
            <div class="table-responsive">
                <table class="table table-bordered table-striped" id="dip_testing_details_table" style="width: 100%;">
                    <thead>
                        <tr>
                            <th class="notexport">@lang('petro::lang.action')</th>
                            <th>@lang('petro::lang.transaction_date')</th>
                            <th>@lang('petro::lang.location')</th>
                            <th>@lang('petro::lang.settlement_no')</th>
                            <th>@lang('petro::lang.pump_no')</th>
                            <th>@lang('petro::lang.product')</th>
                            <th>@lang('petro::lang.pump_operator')</th>
                            <th>@lang('petro::lang.testing_liters')</th>
                            <th>@lang('petro::lang.testing_sale_value')</th>

                        </tr>
                    </thead>
                    <tfoot class="bg-gray">
                        <tr>
                            <td class="text-bold text-right" colspan="7">@lang('petro::lang.total')</td>
                            <td class=""><span class="display_currency" id="footer_testing_qty"
                                    data-currency_symbol="true"></span></td>
                            <td class=""><span class="display_currency" id="footer_testing_sale_value"
                                    data-currency_symbol="true"></span></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
    @endcomponent
</section>
<!-- /.content -->