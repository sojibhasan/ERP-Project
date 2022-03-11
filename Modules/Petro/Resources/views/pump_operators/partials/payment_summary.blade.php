<!-- Main content -->
<section class="content">
    @if(empty($only_pumper))
    <div class="row">
        <div class="col-md-12">
            @component('components.filters', ['title' => __('report.filters')])
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('payment_summary_pump_operators', __('petro::lang.pump_operator') . ':') !!}
                    {!! Form::select('payment_summary_pump_operators', $pump_operators, null, ['class' => 'form-control
                    select2', 'placeholder'
                    => __('petro::lang.all'), 'id' => 'payment_summary_pump_operators', 'style' => 'width:100%']); !!}
                </div>
            </div>

            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('payment_summary_payment_method', __('petro::lang.payment_method') . ':') !!}
                    {!! Form::select('payment_summary_payment_method', $payment_types, null, ['class' => 'form-control
                    select2',
                    'placeholder'
                    => __('petro::lang.all'), 'id' => 'payment_summary_payment_method', 'style' => 'width:100%']); !!}
                </div>
            </div>

            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('payment_summary_date_range', __('report.date_range') . ':') !!}
                    {!! Form::text('payment_summary_date_range', @format_date('first day of this month') . ' ~ ' .
                    @format_date('last day of this month') , ['placeholder' => __('lang_v1.select_a_date_range'),
                    'class' =>
                    'form-control', 'id' => 'payment_summary_date_range', 'readonly']); !!}
                </div>
            </div>
            @endcomponent
        </div>
    </div>
    @endif

    @component('components.widget', ['class' => 'box-primary', 'title' =>
    __('petro::lang.all_your_payments')])
    <div class="table-responsive">
        <table class="table table-bordered table-striped" id="pump_operators_payment_summary_table"
            style="width: 100%;">
            <thead>
                <tr>
                    <th>@lang('petro::lang.action')</th>
                    <th>@lang('petro::lang.date')</th>
                    <th>@lang('petro::lang.time')</th>
                    <th>@lang('petro::lang.pump_operator')</th>
                    <th>@lang('petro::lang.payment_type')</th>
                    <th>@lang('petro::lang.amount')</th>
                    @if(empty($only_pumper))
                    <th>@lang('petro::lang.note')</th>
                    <th>@lang('petro::lang.edited_by')</th>
                    @endif
                </tr>
            </thead>

            <tfoot>
                <tr class="bg-gray font-17 footer-total">
                    <td colspan="@if(empty($only_pumper)) 5 @else 4 @endif" class="text-right">
                        <strong>@lang('sale.total'):</strong></td>
                    <td><span class="display_currency" id="footer_payment_summary_amount"
                            data-currency_symbol="true"></span>
                    @if(empty($only_pumper))
                    <td></td>
                    <td></td>
                    @endif
                </tr>
            </tfoot>
        </table>
    </div>
    @endcomponent

</section>
<!-- /.content -->