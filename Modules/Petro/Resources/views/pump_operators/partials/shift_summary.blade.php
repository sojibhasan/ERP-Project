
<!-- Main content -->
<section class="content">
    <div class="row">
        <div class="col-md-12">
            @component('components.widget')
            <div class="col-md-5 col-md-offset-2">
                <div class="row">
                    <div class="col-md-6 text-red">
                        <h3>@lang('petro::lang.no_of_pumps_today'):</h3>
                    </div>
                    <div class="col-md-6">
                        <h3>{{@num_format($day_entries->count())}}</h3>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 text-red">
                        <h3>@lang('petro::lang.total_sale_today'):</h3>
                    </div>
                    <div class="col-md-6">
                        <h3>{{@num_format($day_entries->sum('amount'))}}</h3>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 text-red">
                        <h3>@lang('petro::lang.total_payments'):</h3>
                    </div>
                    <div class="col-md-6">
                        <h3>333</h3>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 text-red">
                        <h3>@lang('petro::lang.balance_to_settle'):</h3>
                    </div>
                    <div class="col-md-6">
                        <h3>333</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-5">
                <div class="row">
                    <div class="col-md-6 text-red">
                        <h3>@lang('petro::lang.cash'):</h3>
                    </div>
                    <div class="col-md-6">
                        <h3>333</h3>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 text-red">
                        <h3>@lang('petro::lang.credit_sales'):</h3>
                    </div>
                    <div class="col-md-6">
                        <h3>333</h3>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 text-red">
                        <h3>@lang('petro::lang.credit_cards'):</h3>
                    </div>
                    <div class="col-md-6">
                        <h3>333</h3>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 text-red">
                        <h3>@lang('petro::lang.cheque_sales'):</h3>
                    </div>
                    <div class="col-md-6">
                        <h3>333</h3>
                    </div>
                </div>
            </div>
          
            @endcomponent
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            @component('components.filters', ['title' => __('report.filters')])
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('shift_summary_location_id', __('purchase.business_location') . ':') !!}
                    {!! Form::select('shift_summary_location_id', $business_locations, null, ['class' => 'form-control
                    select2',
                    'placeholder' => __('petro::lang.all'), 'id' => 'shift_summary_location_id', 'style' => 'width:100%']); !!}
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('shift_summary_pump_operators', __('petro::lang.pump_operator') . ':') !!}
                    {!! Form::select('shift_summary_pump_operators', $pump_operators, null, ['class' => 'form-control select2', 'placeholder'
                    => __('petro::lang.all'), 'id' => 'shift_summary_pump_operators', 'style' => 'width:100%']); !!}
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('pumps', __('petro::lang.pumps') . ':') !!}
                    {!! Form::select('shift_summary_pumps', $pumps->pluck('pump_name', 'id'), null, ['class' => 'form-control select2', 'placeholder'
                    => __('petro::lang.all'), 'id' => 'shift_summary_pumps', 'style' => 'width:100%']); !!}
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('shift_summary_payment_method', __('petro::lang.payment_method') . ':') !!}
                    {!! Form::select('shift_summary_payment_method', $payment_types, null, ['class' => 'form-control select2',
                    'placeholder'
                    => __('petro::lang.all'), 'id' => 'shift_summary_payment_method', 'style' => 'width:100%']); !!}
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('shift_summary_difference', __('petro::lang.difference') . ':') !!}
                    {!! Form::select('shift_summary_difference', ['positive' => 'Positive', 'negative' => 'Negative'], null, ['class' => 'form-control select2',
                    'placeholder'
                    => __('petro::lang.all'), 'id' => 'shift_summary_difference', 'style' => 'width:100%']); !!}
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('shift_summary_date_range', __('report.date_range') . ':') !!}
                    {!! Form::text('shift_summary_date_range', @format_date('first day of this month') . ' ~ ' .
                    @format_date('last
                    day of this month') , ['placeholder' => __('lang_v1.select_a_date_range'), 'class' =>
                    'form-control', 'id' => 'shift_summary_date_range', 'readonly']); !!}
                </div>
            </div>
            @endcomponent
        </div>
    </div>

    @component('components.widget', ['class' => 'box-primary', 'title' =>
    __('petro::lang.all_your_daily_collection')])
    <div class="table-responsive">
        <table class="table table-bordered table-striped" id="pump_operators_shift_summary_table" style="width: 100%;">
            <thead>
                <tr>
                    <th class="notexport">@lang('messages.action')</th>
                    <th>@lang('petro::lang.date')</th>
                    <th>@lang('petro::lang.pump_operator')</th>
                    <th>@lang('petro::lang.pump_no')</th>
                    <th>@lang('petro::lang.starting_meter')</th>
                    <th>@lang('petro::lang.closing_meter')</th>
                    <th>@lang('petro::lang.test_qty')</th>
                    <th>@lang('petro::lang.sold_ltr')</th>
                    <th>@lang('petro::lang.sold_amount')</th>
                    <th>@lang('petro::lang.credit_sale')</th>
                    <th>@lang('petro::lang.cards')</th>
                    <th>@lang('petro::lang.cash')</th>
                    <th>@lang('petro::lang.cheque')</th>
                    <th>@lang('petro::lang.total_amount')</th>
                    <th>@lang('petro::lang.difference')</th>

                </tr>
            </thead>

            <tfoot>
                <tr class="bg-gray font-17 footer-total">
                    <td colspan="4" class="text-right"><strong>@lang('sale.total'):</strong></td>
                    <td><span class="display_currency" id="footer_shift_summary_sold_ltr" data-currency_symbol="false"></span>
                    <td><span class="display_currency" id="footer_shift_summary_sold_amount" data-currency_symbol="true"></span>
                    <td><span class="display_currency" id="footer_shift_summary_credit_sale" data-currency_symbol="true"></span>
                    <td><span class="display_currency" id="footer_shift_summary_cards" data-currency_symbol="true"></span>
                    <td><span class="display_currency" id="footer_shift_summary_cash" data-currency_symbol="true"></span>
                    <td><span class="display_currency" id="footer_shift_summary_cheque" data-currency_symbol="true"></span>
                    <td><span class="display_currency" id="footer_shift_summary_total_amount" data-currency_symbol="true"></span>
                    <td><span class="display_currency" id="footer_shift_summary_difference" data-currency_symbol="true"></span>
                   
                </tr>
            </tfoot>
        </table>
    </div>
    @endcomponent

</section>
<!-- /.content -->