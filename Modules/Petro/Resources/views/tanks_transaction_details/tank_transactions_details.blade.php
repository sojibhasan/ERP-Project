<!-- Main content -->
<section class="content">
    <div class="row">
        <div class="col-md-12">
            @component('components.filters', ['title' => __('report.filters')])
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('transaction_details_date_range', __('report.date_range') . ':') !!}
                    {!! Form::text('transaction_details_date_range', null, ['placeholder' =>
                    __('lang_v1.select_a_date_range'), 'class' =>
                    'form-control', 'id' => 'transaction_details_date_range', 'readonly']); !!}
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('transaction_details_location_id', __('petro::lang.business_location') . ':') !!}
                    {!! Form::select('transaction_details_location_id', $business_locations, null, ['class' =>
                    'form-control select2 daily_report_change',
                    'placeholder' => __('petro::lang.all'), 'id' => 'transaction_details_location_id', 'style' =>
                    'width:100%']); !!}
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('transaction_details_tank_number', __('petro::lang.fuel_tank_number') . ':') !!}
                    {!! Form::select('transaction_details_tank_number', $tank_numbers, null, ['class' => 'form-control
                    select2 daily_report_change',
                    'placeholder' => __('petro::lang.all'), 'id' => 'transaction_details_tank_number', 'style' =>
                    'width:100%']); !!}
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('transaction_details_product_id', __('petro::lang.products') . ':') !!}
                    {!! Form::select('transaction_details_product_id', $products, null, ['class' => 'form-control
                    select2 daily_report_change',
                    'placeholder' => __('petro::lang.all'), 'id' => 'transaction_details_product_id', 'style' =>
                    'width:100%']); !!}
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('transaction_details_settlement_id', __('petro::lang.settlment_nos') . ':') !!}
                    {!! Form::select('transaction_details_settlement_id', $settlements, null, ['class' => 'form-control
                    select2 daily_report_change',
                    'placeholder' => __('petro::lang.all'), 'id' => 'transaction_details_settlement_id', 'style' =>
                    'width:100%']); !!}
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('transaction_details_purhcase_no', __('petro::lang.purhcase_no') . ':') !!}
                    {!! Form::select('transaction_details_purhcase_no', $purhcase_nos, null, ['class' => 'form-control
                    select2 daily_report_change',
                    'placeholder' => __('petro::lang.all'), 'id' => 'transaction_details_purhcase_no', 'style' =>
                    'width:100%']); !!}
                </div>
            </div>
            @endcomponent
        </div>
    </div>

    @component('components.widget', ['class' => 'box-primary', 'title' => __(
    'petro::lang.all_your_tank_transaction_details')])
    <div class="table-responsive">
        <table class="table table-bordered table-striped" id="tank_transaction_details_table" style="width:100%;">
            <thead>
                <tr>
                    <th>@lang('petro::lang.date_and_time')</th>
                    <th>@lang('petro::lang.transaction_date')</th>
                    <th>@lang('petro::lang.branch')</th>
                    <th>@lang('petro::lang.fuel_tank_number')</th>
                    <th>@lang('petro::lang.product')</th>
                    <th>@lang('petro::lang.settlement_purchase_invoice_no')</th>
                    <th>@lang('petro::lang.purchase_order_no')</th>
                    <th>@lang('petro::lang.starting_qty')</th>
                    <th>@lang('petro::lang.purchase_qty')</th>
                    <th>@lang('petro::lang.sold_qty')</th>
                    <th>@lang('petro::lang.balance_qty')</th>

                </tr>
            </thead>
        </table>
    </div>
    @endcomponent

</section>
<!-- /.content -->