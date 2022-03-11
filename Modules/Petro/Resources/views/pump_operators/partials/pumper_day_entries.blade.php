
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
                        <h3>{{$today_pumps}}</h3>
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
                        <h3>{{@num_format($payments->total)}}</h3>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 text-red">
                        <h3>@lang('petro::lang.balance_to_settle'):</h3>
                    </div>
                    <div class="col-md-6">
                        <h3>{{@num_format($day_entries->sum('amount') - $payments->total)}} @if(($day_entries->sum('amount') - $payments->total) < 0  ) <span>@lang('petro::lang.excess')</span> @endif </h3> 
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                    <a id="" class="btn btn-flat btn-warning btn-modal"
                        style="font-family: 'Source Sans Pro',sans-serif;color: #fff; " data-container=".view_modal"
                        data-href="{{action('\Modules\Petro\Http\Controllers\PumpOperatorPaymentController@getPaymentModal', ['only_pumper' => true])}}">
                        @lang('petro::lang.settle_button')
                      </a>
                    </div>
                </div>
            </div>
            <div class="col-md-5">
                <div class="row">
                    <div class="col-md-6 text-red">
                        <h3>@lang('petro::lang.cash'):</h3>
                    </div>
                    <div class="col-md-6">
                        <h3>{{@num_format($payments->cash)}}</h3>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 text-red">
                        <h3>@lang('petro::lang.credit_sales'):</h3>
                    </div>
                    <div class="col-md-6">
                        <h3>{{@num_format($payments->credit)}}</h3>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 text-red">
                        <h3>@lang('petro::lang.credit_cards'):</h3>
                    </div>
                    <div class="col-md-6">
                        <h3>{{@num_format($payments->card)}}</h3>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 text-red">
                        <h3>@lang('petro::lang.cheque_sales'):</h3>
                    </div>
                    <div class="col-md-6">
                        <h3>{{@num_format($payments->cheque)}}</h3>
                    </div>
                </div>
            </div>
          
            @endcomponent
        </div>
    </div>

    @if(empty(auth()->user()->pump_operator_id))
    <div class="row">
        <div class="col-md-12">
            @component('components.filters', ['title' => __('report.filters')])
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('day_entries_location_id', __('purchase.business_location') . ':') !!}
                    {!! Form::select('day_entries_location_id', $business_locations, null, ['class' => 'form-control
                    select2',
                    'placeholder' => __('petro::lang.all'), 'id' => 'day_entries_location_id', 'style' => 'width:100%']); !!}
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('day_entries_pump_operators', __('petro::lang.pump_operator') . ':') !!}
                    {!! Form::select('day_entries_pump_operators', $pump_operators, null, ['class' => 'form-control select2', 'placeholder'
                    => __('petro::lang.all'), 'id' => 'day_entries_pump_operators', 'style' => 'width:100%']); !!}
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('pumps', __('petro::lang.pumps') . ':') !!}
                    {!! Form::select('day_entries_pumps', $pumps->pluck('pump_name', 'id'), null, ['class' => 'form-control select2', 'placeholder'
                    => __('petro::lang.all'), 'id' => 'day_entries_pumps', 'style' => 'width:100%']); !!}
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('day_entries_payment_method', __('petro::lang.payment_method') . ':') !!}
                    {!! Form::select('day_entries_payment_method', $payment_types, null, ['class' => 'form-control select2',
                    'placeholder'
                    => __('petro::lang.all'), 'id' => 'day_entries_payment_method', 'style' => 'width:100%']); !!}
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('day_entries_difference', __('petro::lang.difference') . ':') !!}
                    {!! Form::select('day_entries_difference', ['positive' => 'Positive', 'negative' => 'Negative'], null, ['class' => 'form-control select2',
                    'placeholder'
                    => __('petro::lang.all'), 'id' => 'day_entries_difference', 'style' => 'width:100%']); !!}
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('date_range', __('report.date_range') . ':') !!}
                    {!! Form::text('date_range', @format_date('first day of this month') . ' ~ ' .
                    @format_date('last
                    day of this month') , ['placeholder' => __('lang_v1.select_a_date_range'), 'class' =>
                    'form-control', 'id' => 'date_range', 'readonly']); !!}
                </div>
            </div>
            @endcomponent
        </div>
    </div>
    @endif

    @component('components.widget', ['class' => 'box-primary', 'title' =>
    __('petro::lang.all_your_daily_collection')])
    <div class="table-responsive">
        <table class="table table-bordered table-striped" id="pump_operators_day_entries_table" style="width: 100%;">
            <thead>
                <tr>
                    <th class="notexport">@lang('messages.action')</th>
                    <th>@lang('petro::lang.date')</th>
                    @if(empty(auth()->user()->pump_operator_id))
                    <th>@lang('petro::lang.settlement_no')</th>
                    @endif
                    <th>@lang('petro::lang.pump_operator')</th>
                    <th>@lang('petro::lang.starting_meter')</th>
                    <th>@lang('petro::lang.closing_meter')</th>
                    <th>@lang('petro::lang.test_qty')</th>
                    <th>@lang('petro::lang.sold_ltr')</th>
                    <th>@lang('petro::lang.amount')</th>
                    <th>@lang('petro::lang.short_amount')</th>

                </tr>
            </thead>

            <tfoot>
                <tr class="bg-gray font-17 footer-total">
                    <td colspan="@if(!empty(auth()->user()->pump_operator_id)) 6 @else 7 @endif" class="text-right"><strong>@lang('sale.total'):</strong></td>
                    <td><span class="display_currency" id="footer_sold_ltr" data-currency_symbol="false"></span></td>
                    <td><span class="display_currency" id="footer_sold_amount" data-currency_symbol="true"></span></td>
                    <td></td>
                </tr>
            </tfoot>
        </table>
    </div>
    @endcomponent

</section>
<!-- /.content -->