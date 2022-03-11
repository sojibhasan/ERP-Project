<div class="col-md-12">
    @component('components.widget', ['class' => 'box'])
    <div class="col-md-3">
        <div class="form-group">
            {!! Form::label('references',  __('contact.references') . ':') !!}
            {!! Form::select('references', $references, null, ['class' => 'form-control select2', 'style' => 'width:100%', 'placeholder' => __('lang_v1.all')]); !!}
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            {!! Form::label('references_payment_status',  __('purchase.payment_status') . ':') !!}
            {!! Form::select('references_payment_status', ['paid' => __('lang_v1.paid'), 'due' => __('lang_v1.due'), 'partial' => __('lang_v1.partial'), 'overdue' => __('lang_v1.overdue')], null, ['class' => 'form-control select2', 'style' => 'width:100%', 'placeholder' => __('lang_v1.all')]); !!}
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            {!! Form::label('references_date_range', __('report.date_range') . ':') !!}
            {!! Form::text('references_date_range', null, ['placeholder' => __('lang_v1.select_a_date_range'), 'class' => 'form-control', 'readonly']); !!}
        </div>
    </div>
    @endcomponent
</div>
<div class="col-md-12">
    @component('components.widget', ['class' => 'box'])
    <div class="table-responsive">
        <table class="table table-bordered table-striped ajax_view" id="references_table">
            <thead>
                <tr>
                    <th>@lang('messages.action')</th>
                    <th>@lang('messages.date')</th>
                    <th>@lang('sale.invoice_no')</th>
                    <th>@lang('sale.customer_name')</th>
                    <th>@lang('lang_v1.contact_no')</th>
                    <th>@lang('sale.location')</th>
                    <th>@lang('contact.references')</th>
                    <th>@lang('sale.payment_status')</th>
                    <th>@lang('lang_v1.payment_method')</th>
                    <th>@lang('sale.total_amount')</th>
                    <th>@lang('sale.total_paid')</th>
                    <th>@lang('lang_v1.sell_due')</th>
                    <th>@lang('lang_v1.sell_return_due')</th>
                    <th>@lang('lang_v1.shipping_status')</th>
                    <th>@lang('lang_v1.total_items')</th>
                    <th>@lang('lang_v1.types_of_service')</th>
                    <th>@lang('lang_v1.service_custom_field_1' )</th>
                    <th>@lang('lang_v1.added_by')</th>
                    <th>@lang('sale.sell_note')</th>
                    <th>@lang('sale.staff_note')</th>
                    <th>@lang('sale.shipping_details')</th>
                    <th>@lang('restaurant.table')</th>
                    <th>@lang('restaurant.service_staff')</th>
                </tr>
            </thead>
            <tfoot>
                <tr class="bg-gray font-17 footer-total text-center">
                    <td colspan="6"><strong>@lang('sale.total'):</strong></td>
                    <td id="footer_payment_status_count"></td>
                    <td id="payment_method_count"></td>
                    <td><span class="display_currency" id="footer_sale_total" data-currency_symbol ="true"></span></td>
                    <td><span class="display_currency" id="footer_total_paid" data-currency_symbol ="true"></span></td>
                    <td><span class="display_currency" id="footer_total_remaining" data-currency_symbol ="true"></span></td>
                    <td><span class="display_currency" id="footer_total_sell_return_due" data-currency_symbol ="true"></span></td>
                    <td colspan="2"></td>
                    <td id="service_type_count"></td>
                    <td colspan="7"></td>
                </tr>
            </tfoot>
        </table>
    </div>
    @endcomponent
</div>