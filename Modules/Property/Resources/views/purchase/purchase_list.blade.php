
<!-- Main content -->
<section class="content no-print">
    @component('components.filters', ['title' => __('report.filters')])
        <div class="col-md-3">
            <div class="form-group">
                {!! Form::label('location_id',  __('purchase.business_location') . ':') !!}
                {!! Form::select('location_id', $business_locations, null, ['class' => 'form-control select2', 'style' => 'width:100%', 'placeholder' => __('lang_v1.all')]); !!}
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                {!! Form::label('supplier_id',  __('property::lang.property_seller') . ':') !!}
                {!! Form::select('supplier_id', $suppliers, null, ['class' => 'form-control select2', 'style' => 'width:100%', 'placeholder' => __('lang_v1.all')]); !!}
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                {!! Form::label('status',  __('property::lang.status') . ':') !!}
                {!! Form::select('status', $statuses, null, ['class' => 'form-control select2', 'style' => 'width:100%', 'placeholder' => __('lang_v1.all')]); !!}
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                {!! Form::label('payment_status',  __('purchase.payment_status') . ':') !!}
                {!! Form::select('payment_status', ['paid' => __('lang_v1.paid'), 'due' => __('lang_v1.due'), 'partial' => __('lang_v1.partial'), 'overdue' => __('lang_v1.overdue')], null, ['class' => 'form-control select2', 'style' => 'width:100%', 'placeholder' => __('lang_v1.all')]); !!}
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                {!! Form::label('date_range', __('report.date_range') . ':') !!}
                {!! Form::text('date_range', null, ['placeholder' => __('lang_v1.select_a_date_range'), 'class' => 'form-control', 'readonly']); !!}
            </div>
        </div>
    @endcomponent

    @component('components.widget', ['class' => 'box-primary', 'title' => __('property::lang.list_all_project_purchased')])
      
    <div class="table-responsive">
        <table class="table table-bordered table-striped ajax_view" id="purchase_table">
            <thead>
                <tr>
                    <th class="notexport">@lang('messages.action')</th>
                    <th>@lang('messages.date')</th>
                    <th>@lang('purchase.location')</th>
                    <th>@lang('purchase.purchase_no')</th>
                    <th>@lang('property::lang.property_seller')</th>
                    <th>@lang('property::lang.deed_no')</th>
                    <th>@lang('purchase.purchase_status')</th>
                    <th>@lang('property::lang.property_name')</th>
                    <th>@lang('property::lang.property_extent')</th>
                    <th>@lang('property::lang.unit')</th>
                    <th>@lang('purchase.amount')</th>
                    <th>@lang('property::lang.pay_terms')</th>
                    <th>@lang('purchase.payment_status')</th>
                    <th>@lang('purchase.payment_due') </th>
                    <th>@lang('purchase.payment_method')</th>
                    <th>@lang('lang_v1.added_by')</th>
                </tr>
            </thead>
            <tfoot>
                <tr class="bg-gray font-17 text-center footer-total">
                    <td colspan="9"><strong>@lang('sale.total'):</strong></td>
                    <td id="footer_status_count"></td>
                    <td><span class="display_currency" id="footer_purchase_total" data-currency_symbol ="true"></span></td>
                    <td></td>
                    <td id="footer_payment_status_count"></td>
                    <td class="text-left"><small>@lang('report.purchase_due') - <span class="display_currency" id="footer_total_due" data-currency_symbol ="true"></span><br></small></td>
                    <td></td>
                    <td></td>
                </tr>
            </tfoot>
        </table>
    </div>
      
    @endcomponent

    <div class="modal fade product_modal" tabindex="-1" role="dialog" 
    	aria-labelledby="gridSystemModalLabel">
    </div>

    <div class="modal fade payment_modal" tabindex="-1" role="dialog" 
        aria-labelledby="gridSystemModalLabel">
    </div>

    <div class="modal fade edit_payment_modal" tabindex="-1" role="dialog" 
        aria-labelledby="gridSystemModalLabel">
    </div>

</section>

<section id="receipt_section" class="print_section"></section>

