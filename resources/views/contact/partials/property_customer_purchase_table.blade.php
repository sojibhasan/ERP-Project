<div class="col-md-12">
    @component('components.widget', ['class' => 'box'])
    @include('contact.partials.sell_list_filters')
    @endcomponent
</div>
<br>
<div class="col-md-12">
    @component('components.widget', ['class' => 'box'])
    <div class="table-responsive">
        <table class="table table-bordered table-striped ajax_view" id="sell_table">
            <thead>
                <tr>
                    <th class="notexport">@lang('messages.action')</th>
                    <th>@lang('messages.date')</th>
                    <th>@lang('sale.invoice_no')</th>
                    <th>@lang('sale.location')</th>
                    <th>@lang('sale.payment_status')</th>
                    <th>@lang('lang_v1.payment_method')</th>
                    <th>@lang('sale.total_amount')</th>
                    <th>@lang('sale.total_paid')</th>
                    <th>@lang('lang_v1.added_by')</th>
                    <th>@lang('sale.sell_note')</th>
                    <th>@lang('sale.staff_note')</th>
                </tr>
            </thead>
            <tfoot>
                <tr class="bg-gray font-17 footer-total text-center">
                    <td colspan="4"><strong>@lang('sale.total'):</strong></td>
                    <td id="footer_payment_status_count"></td>
                    <td id="payment_method_count"></td>
                    <td><span class="display_currency" id="footer_sale_total" data-currency_symbol="true"></span></td>
                    <td><span class="display_currency" id="footer_total_paid" data-currency_symbol="true"></span></td>
                    <td colspan="3"></td>
                </tr>
            </tfoot>
        </table>
    </div>
    @endcomponent

</div>