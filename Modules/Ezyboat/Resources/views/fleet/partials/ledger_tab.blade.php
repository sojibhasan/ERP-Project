<div class="row">
    <div class="col-md-12">

        <div class="col-md-3">
            <div class="form-group">
                {!! Form::label('ledger_date_range', __('report.date_range') . ':') !!}
                {!! Form::text('ledger_date_range', null, ['placeholder' => __('lang_v1.select_a_date_range'), 'class' =>
                'form-control', 'readonly']); !!}
            </div>
        </div>

    </div>
    <div class="col-md-12">

        <div id="contact_ledger_div"></div>

    </div>
</div>

<div class="table-responsive">
    <table class="table table-bordered table-striped" id="ledger_table" style="width: 100%;">
        <thead>
            <tr>
                <th>@lang('fleet::lang.date')</th>
                <th>@lang('fleet::lang.description')</th>
                <th>@lang('fleet::lang.destination')</th>
                <th>@lang('fleet::lang.milage')</th>
                <th>@lang('fleet::lang.invoice_amount')</th>
                <th>@lang('fleet::lang.payment_received')</th>
                <th>@lang('fleet::lang.balance')</th>
                <th>@lang('fleet::lang.payment_method')</th>
            </tr>
        </thead>
    </table>
</div>