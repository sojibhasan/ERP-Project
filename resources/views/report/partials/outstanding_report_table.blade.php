<div class="row">
    <div class="col-md-12">
        @component('components.widget', ['class' => 'box-primary'])
        <div class="table-responsive">
            <table class="table table-bordered table-striped" id="outstanding_report_table" style="width: 100%">
                <thead>
                        <tr>
                            <th>@lang('report.payment_received_date')</th>
                            <th>@lang('report.customer')</th>
                            <th>@lang('report.ref_bill_no')</th>
                            <th>@lang('report.sale_invoice_pos_date')</th>
                            <th>@lang('report.bill_amount')</th>
                            <th>@lang('report.received_amount')</th>
                            <th>@lang('report.payment_method_cheque_no')</th>
                            <th  class="notexport">@lang('report.action')</th> 
                        </tr>
                </thead>
                <tfoot>
                    <tr class="bg-gray font-17 footer-total text-center">
                        <td colspan="3"><strong>@lang('sale.total'):</strong></td>
                        <td id="footer_payment_status_count"></td>
                        <td><span class="display_currency" id="footer_sale_total" data-currency_symbol ="true"></span></td>
                        <td><span class="display_currency" id="footer_total_paid" data-currency_symbol ="true"></span></td>
                        <td colspan="2"></td>
                    </tr>
                </tfoot>
            </table>
        </div>
        @endcomponent
    </div>
</div>