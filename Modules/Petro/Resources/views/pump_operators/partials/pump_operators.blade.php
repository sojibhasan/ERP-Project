
<!-- Main content -->
<section class="content">
    <div class="row">
        <div class="col-md-12">
            @component('components.filters', ['title' => __('report.filters')])
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('location_id', __('purchase.business_location') . ':') !!}
                    {!! Form::select('location_id', $business_locations, null, ['class' => 'form-control select2',
                    'placeholder' => __('petro::lang.all'), 'style' => 'width:100%']); !!}
                </div>
            </div>
            <div class="col-sm-3">
                <div class="form-group">
                    {!! Form::label('pump_operator', __('petro::lang.pump_operator').':') !!}
                    {!! Form::select('pump_operator', $pump_operators, null, ['class' => 'form-control select2',
                    'placeholder' => __('petro::lang.all')]); !!}
                </div>
            </div>
            <div class="col-sm-3">
                <div class="form-group">
                    {!! Form::label('settlement_no', __('petro::lang.settlement_no').':') !!}
                    {!! Form::select('settlement_no', $settlement_nos, null, ['class' => 'form-control select2',
                    'placeholder' => __('petro::lang.all')]); !!}
                </div>
            </div>

            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('date_range', __('report.date_range') . ':') !!}
                    {!! Form::text('date_range', @format_date('first day of this month') . ' ~ ' . @format_date('last
                    day of this month') , ['placeholder' => __('lang_v1.select_a_date_range'), 'class' =>
                    'form-control', 'id' => 'expense_date_range', 'readonly']); !!}
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('type', __('petro::lang.type') . ':') !!}
                    {!! Form::select('type', ['commission' => __('petro::lang.commission'),'excess' =>
                    __('petro::lang.excess'),'shortage' => __('petro::lang.shortage')], null, ['class' => 'form-control
                    select2', 'style' => 'width:100%', 'placeholder' => __('lang_v1.all')]); !!}
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('status', __('petro::lang.status') . ':') !!}
                    {!! Form::select('status', ['inactive' => __('petro::lang.inactive'),'active' =>
                    __('petro::lang.active')], null, ['class' => 'form-control
                    select2', 'style' => 'width:100%', 'placeholder' => __('lang_v1.all')]); !!}
                </div>
            </div>
            @endcomponent
        </div>
    </div>

    @component('components.widget', ['class' => 'box-primary', 'title' =>
    __('petro::lang.all_your_list_pump_operators')])
    @slot('tool')
    <div class="box-tools ">
        <button type="button" class="btn  btn-primary btn-modal"
            data-href="{{action('\Modules\Petro\Http\Controllers\PumpOperatorController@create')}}"
            data-container=".pump_operator_modal">
            <i class="fa fa-plus"></i> @lang('messages.add')</button>
        <a class="btn  btn-primary"
            href="{{action('\Modules\Petro\Http\Controllers\PumpOperatorController@importPumps')}}">
            <i class="fa fa-download "></i> @lang('petro::lang.import')</a>

    </div>
    @endslot
    <div class="table-responsive">
        <table class="table table-bordered table-striped" id="list_pump_operators_table">
            <thead>
                <tr>
                    <th class="notexport">@lang('messages.action')</th>
                    <th>@lang('petro::lang.current_status')</th>
                    <th>@lang('petro::lang.pump_operator')</th>
                    <th>@lang('petro::lang.location')</th>
                    <th>@lang('petro::lang.sold_fuel_qty')</th>
                    <th>@lang('petro::lang.sale_amount_fuel')</th>
                    <th>@lang('petro::lang.commission_rate')</th>
                    <th>@lang('petro::lang.commission_amount')</th>
                    <th>@lang('petro::lang.excess_amount')</th>
                    <th>@lang('petro::lang.short_amount')</th>

                </tr>
            </thead>

            <tfoot>
                <tr class="bg-gray font-17 footer-total text-center">
                    <td colspan="4"><strong>@lang('sale.total'):</strong></td>
                    <td><span class="display_currency" id="footer_sold_fuel_qty" data-currency_symbol="false"></span>
                    </td>
                    <td><span class="display_currency" id="footer_sale_amount_fuel" data-currency_symbol="true"></span>
                    </td>
                    <td></td>
                    <td><span class="display_currency" id="footer_commission_amount" data-currency_symbol="true"></span>
                    </td>
                    <td><span class="display_currency" id="footer_excess_amount" data-currency_symbol="true"></span>
                    </td>
                    <td><span class="display_currency" id="footer_short_amount" data-currency_symbol="true"></span></td>
                </tr>
            </tfoot>
        </table>
    </div>
    @endcomponent

</section>
<!-- /.content -->