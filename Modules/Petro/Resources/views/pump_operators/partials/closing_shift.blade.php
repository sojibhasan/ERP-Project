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
                        <h3>{{@num_format($day_entries->sum('amount') - $payments->total)}}
                            @if(($day_entries->sum('amount') - $payments->total) < 0 ) <span>
                                @lang('petro::lang.excess')</span> @endif </h3>
                    </div>
                </div>
                @if(!empty(auth()->user()->pump_operator_id))
                <div class="row">
                    <div class="col-md-4">
                        
                         
                        </a>
                    </div>
                    <div class="col-md-4">
                        <a id="" class="btn btn-flat btn-primary"
                            style="font-family: 'Source Sans Pro',sans-serif;color: #fff; "
                            href="{{action('\Modules\Petro\Http\Controllers\PumpOperatorPaymentController@balanceToOperator', $pump_operator->id)}}">
                            @lang('petro::lang.balance_to_operator')
                        </a>
                    </div>
                </div>
                @endif
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
                <div class="row">
                    <div class="col-md-6 text-red">
                        <h3>@lang('petro::lang.balance_to_operator'):</h3>
                    </div>
                    <div class="col-md-6">
                        <h3>{{@num_format(abs($payments->excess) + abs($payments->shortage))}}</h3>
                    </div>
                </div>
                @if(!empty(auth()->user()->pump_operator_id))
                <div class="row">
                    <div class="col-md-8">
                        <a id="close_shift_btn" class=" @if(($day_entries->sum('amount') - $payments->total) != 0 ) hide @endif btn btn-flat pull-right" style="background: #3f48cc; color: #fff;"
                            style="font-family: 'Source Sans Pro',sans-serif;color: #fff; "
                            href="{{action('\Modules\Petro\Http\Controllers\ClosingShiftController@closeShift', $pump_operator->id)}}">
                            @lang('petro::lang.close_shift')
                        </a>
                    </div>
                </div>
                @endif
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
                    {!! Form::label('close_shift_location_id', __('purchase.business_location') . ':') !!}
                    {!! Form::select('close_shift_location_id', $business_locations, null, ['class' => 'form-control
                    select2',
                    'placeholder' => __('petro::lang.all'), 'id' => 'close_shift_location_id', 'style' =>
                    'width:100%']); !!}
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('close_shift_pump_operators', __('petro::lang.pump_operator') . ':') !!}
                    {!! Form::select('close_shift_pump_operators', $pump_operators, null, ['class' => 'form-control
                    select2', 'placeholder'
                    => __('petro::lang.all'), 'id' => 'close_shift_pump_operators', 'style' => 'width:100%']); !!}
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('pumps', __('petro::lang.pumps') . ':') !!}
                    {!! Form::select('close_shift_pumps', $pumps->pluck('pump_name', 'id'), null, ['class' =>
                    'form-control select2', 'placeholder'
                    => __('petro::lang.all'), 'id' => 'close_shift_pumps', 'style' => 'width:100%']); !!}
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('close_shift_payment_method', __('petro::lang.payment_method') . ':') !!}
                    {!! Form::select('close_shift_payment_method', $payment_types, null, ['class' => 'form-control
                    select2',
                    'placeholder'
                    => __('petro::lang.all'), 'id' => 'close_shift_payment_method', 'style' => 'width:100%']); !!}
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('close_shift_date_range', __('report.date_range') . ':') !!}
                    {!! Form::text('close_shift_date_range', @format_date('first day of this month') . ' ~ ' .
                    @format_date('last
                    day of this month') , ['placeholder' => __('lang_v1.select_a_date_range'), 'class' =>
                    'form-control', 'id' => 'close_shift_date_range', 'readonly']); !!}
                </div>
            </div>
            @endcomponent
        </div>
    </div>
    @endif

    @component('components.widget', ['class' => 'box-primary', 'title' =>
    __('petro::lang.all_your_daily_collection')])
    <div class="table-responsive">
        <table class="table table-bordered table-striped" id="pump_operators_closing_shift_table" style="width: 100%;">
            <thead>
                <tr>
                    <th class="notexport">@lang('messages.action')</th>
                    <th>@lang('petro::lang.date')</th>
                    <th>@lang('petro::lang.time')</th>
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
                    <td colspan="6" class="text-right"><strong>@lang('sale.total'):</strong></td>
                    <td><span class="display_currency" id="footer_cs_testing_ltr" data-currency_symbol="false"></span></td>
                    <td><span class="display_currency" id="footer_cs_sold_ltr" data-currency_symbol="false"></span></td>
                    <td><span class="display_currency" id="footer_cs_sold_amount" data-currency_symbol="true"></span></td>
                    <td><span class="display_currency" id="footer_cs_short_amount" data-currency_symbol="true"></span></td>

                </tr>
            </tfoot>
        </table>
    </div>
    @endcomponent

</section>
<!-- /.content -->