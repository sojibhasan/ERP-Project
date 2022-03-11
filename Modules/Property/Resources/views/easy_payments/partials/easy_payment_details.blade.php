<section class="content">
    @component('components.filters', ['title' => __('report.filters')])
    <div class="col-md-3">
        <div class="form-group">
            {!! Form::label('date_range', __('report.date_range') . ':') !!}
            {!! Form::text('date_range', null, ['placeholder' => __('lang_v1.select_a_date_range'), 'class' =>
            'form-control', 'readonly']); !!}
        </div>
    </div>
    <div class="col-md-3">

        <div class="form-group">
            {!! Form::label('location_id', __('purchase.business_location') . ':') !!}
            {!! Form::select('location_id', $business_locations, null, ['class' => 'form-control select2', 'style' =>
            'width:100%', 'placeholder' => __('lang_v1.all')]); !!}
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            {!! Form::label('customer_id', __('property::lang.customer') . ':') !!}
            {!! Form::select('customer_id', $customers, null, ['class' => 'form-control select2', 'style' =>
            'width:100%', 'placeholder' => __('lang_v1.all')]); !!}
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            {!! Form::label('project_id', __('property::lang.project') . ':') !!}
            {!! Form::select('project_id', $projects, null, ['class' => 'form-control select2', 'style' => 'width:100%',
            'placeholder' => __('lang_v1.all')]); !!}
        </div>
    </div>
    <div class="clearfix"></div>
    <div class="col-md-3">
        <div class="checkbox">
            <label>
                {!! Form::checkbox('show_only_penalty', 1, false, ['class' =>
                'input-icheck', 'id' => 'show_only_penalty']); !!}
                {{__('property::lang.show_only_penalty')}}
            </label>
        </div>
    </div>
    <div class="col-md-3">
        <div class="checkbox">
            <label>
                {!! Form::checkbox('show_only_balance_due', 1, false, ['class' =>
                'input-icheck', 'id' => 'show_only_balance_due']); !!}
                {{__('property::lang.show_only_balance_due')}}
            </label>
        </div>
    </div>

    @endcomponent

    @component('components.widget', ['class' => 'box-primary', 'title' => __('property::lang.list_easy_payments')])

    <div class="table-responsive">
        <table class="table table-bordered table-striped ajax_view" id="easy_payment_details_table">
            <thead>
                <tr>
                    <th class="notexport">@lang('messages.action')</th>
                    <th>@lang('property::lang.installment_date')</th>
                    <th>@lang('property::lang.location')</th>
                    <th>@lang('property::lang.customer')</th>
                    <th>@lang('property::lang.project_name')</th>
                    <th>@lang('property::lang.installment_amount')</th>
                    <th>@lang('property::lang.capital')</th>
                    <th>@lang('property::lang.interest')</th>
                    <th>@lang('property::lang.penalty')</th>
                    <th>@lang('property::lang.paid_amount')</th>
                    <th>@lang('property::lang.paid_on')</th>
                    <th>@lang('property::lang.balance_due')</th>
                </tr>
            </thead>
            <tfoot>
                <tr class="bg-gray font-17 text-center footer-total">
                    <td colspan="11"><strong>@lang('sale.total'):</strong></td>
                    <td class="text-left"><small>@lang('report.total_due') - <span class="display_currency"
                                id="footer_total_due" data-currency_symbol="true"></span><br></small></td>
                </tr>
            </tfoot>
        </table>
    </div>

    @endcomponent
</section>