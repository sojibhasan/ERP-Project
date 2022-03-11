<div id="table_div">
    <div class="remove-print table-responsive">
        <table class="table table-bordered table-striped" id="stock_report_table" style="width: 100%;">
            <thead>
                <tr>
                    <th>SKU</th>
                    <th>@lang('business.product')</th>
                    <th>@lang('product.unit')</th>
                    <th>@lang('lang_v1.unit_purchase_price')</th>
                    <th>@lang('lang_v1.unit_sale_price')</th>
                    <th>@lang('report.current_stock')</th>
                    <th>@lang('lang_v1.total_stock_price')</th>
                    <th>@lang('report.total_unit_sold')</th>
                    <th>@lang('report.total_sold_value')</th>
                    <th>@lang('report.total_unit_purchase')</th>
                    <th>@lang('lang_v1.total_unit_transfered')</th>
                    <th>@lang('lang_v1.total_unit_adjusted')</th>
                    <th>@lang('report.total_unit_purchase_return')</th>
                    <th>@lang('report.total_unit_sold_return')</th>
                    @if(Module::has('Manufacturing'))
                    <th class="current_stock_mfg">@lang('manufacturing::lang.current_stock_mfg')
                        @show_tooltip(__('manufacturing::lang.mfg_stock_tooltip'))</th>
                    @endif
                </tr>
            </thead>
            <tfoot>
                <tr class="bg-gray font-17 text-center footer-total">
                    <td colspan="4"><strong>@lang('sale.total'):</strong></td>
                    <td id="footer_total_stock"></td>
                    <td><span id="footer_total_stock_price" class="display_currency" data-currency_symbol="true"></span>
                    </td>
                    <td id="footer_total_sold"></td>
                    <td><span id="footer_total_sold_value" class="display_currency" data-currency_symbol="true"></span>
                    </td>
                    <td colspan="5" id="footer_total_adjusted"></td>
                    @if(Module::has('Manufacturing'))
                    <td id="footer_total_mfg_stock"></td>
                    @endif
                </tr>
            </tfoot>
        </table>
    </div>
</div>