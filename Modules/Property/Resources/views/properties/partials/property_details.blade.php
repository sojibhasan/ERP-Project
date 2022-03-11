<section class="content">
    @component('components.filters', ['title' => __('report.filters')])
    <div class="col-md-3">
        <div class="form-group">
            {!! Form::label('property_details_location_id',  __('purchase.business_location') . ':') !!}
            {!! Form::select('property_details_location_id', $business_locations, null, ['class' => 'form-control select2', 'style' => 'width:100%', 'placeholder' => __('lang_v1.all')]); !!}
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            {!! Form::label('property_details_customer_id',  __('property::lang.customers') . ':') !!}
            {!! Form::select('property_details_customer_id', $customers, null, ['class' => 'form-control select2', 'style' => 'width:100%', 'placeholder' => __('lang_v1.all')]); !!}
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            {!! Form::label('property_details_property_id',  __('property::lang.project_name') . ':') !!}
            {!! Form::select('property_details_property_id', $properties, null, ['class' => 'form-control select2', 'style' => 'width:100%', 'placeholder' => __('lang_v1.all')]); !!}
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            {!! Form::label('property_details_finance_option_id',  __('property::lang.block_number') . ':') !!}
            {!! Form::select('property_details_finance_option_id', $finance_options, null, ['class' => 'form-control select2', 'style' => 'width:100%', 'placeholder' => __('lang_v1.all')]); !!}
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            {!! Form::label('property_details_easy_payment',  __('property::lang.finance_option') . ':') !!}
            {!! Form::select('property_details_easy_payment', ['0' => 'No', '1' => 'Yes'], null, ['class' => 'form-control select2', 'style' => 'width:100%', 'placeholder' => __('lang_v1.all')]); !!}
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            {!! Form::label('property_details_installment_cycle_id',  __('property::lang.installment_cycle') . ':') !!}
            {!! Form::select('property_details_installment_cycle_id', $installment_cycles, null, ['class' => 'form-control select2', 'style' => 'width:100%', 'placeholder' => __('lang_v1.all')]); !!}
        </div>
    </div>

    <div class="col-md-3">
        <div class="form-group">
            {!! Form::label('property_details_date_range', __('report.date_range') . ':') !!}
            {!! Form::text('property_details_date_range', null, ['placeholder' => __('lang_v1.select_a_date_range'), 'class' => 'form-control', 'readonly']); !!}
        </div>
    </div>
    @endcomponent
    
    @component('components.widget', ['class' => 'box-primary', 'title' => "List All Sold Details"])
    
    <div class="table-responsive">
    <table class="table table-bordered table-striped ajax_view" id="property_details_table">
        <thead>
            <tr>
                <th class="notexport">@lang('messages.action')</th>
                <th>@lang('property::lang.sold_date')</th>
                <th>@lang('property::lang.project_name')</th>
                <th>@lang('property::lang.block_number')</th>
                <th>@lang('property::lang.block_extent')</th>
                <th>@lang('property::lang.units')</th>
{{--                <th>@lang('property::lang.block_sale_price')</th>--}}
                <th>Block Sale(Price)</th>
                <th>Block Sold(Price)</th>
                <th>@lang('property::lang.customer')</th>
                <th>@lang('property::lang.finance_option')</th>
                <th>@lang('property::lang.reservation_amount')</th>
                <th>@lang('property::lang.down_payment')</th>
                <th>@lang('property::lang.easy_payment')</th>
                <th>@lang('property::lang.no_of_installments')</th>
                <th>@lang('property::lang.installment_amount')</th>
                <th>@lang('property::lang.first_installment_date')</th>
                <th>@lang('property::lang.installment_cycle')</th>
                <th>@lang('property::lang.total_amount')</th>
                <th>@lang('property::lang.capital')</th>
                <th>@lang('property::lang.interest')</th>
            </tr>
        </thead>
        <tfoot>
            {{-- <tr class="bg-gray font-17 text-center footer-total">
                <td colspan="9"><strong>@lang('sale.total'):</strong></td>
                <td id="footer_status_count"></td>
                <td><span class="display_currency" id="footer_purchase_total" data-currency_symbol ="true"></span></td>
                <td></td>
                <td id="footer_payment_status_count"></td>
                <td class="text-left"><small>@lang('report.purchase_due') - <span class="display_currency" id="footer_total_due" data-currency_symbol ="true"></span><br></small></td>
                <td></td>
                <td></td>
            </tr> --}}
        </tfoot>
    </table>
    </div>
    
    @endcomponent
    </section>