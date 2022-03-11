<div class="row">
    <div class="col-md-12">

        {{-- <div class="col-md-3">
            <div class="form-group">
                {!! Form::label('ledger_date_range', __('report.date_range') . ':') !!}
                {!! Form::text('ledger_date_range', null, ['placeholder' => __('lang_v1.select_a_date_range'), 'class' => 'form-control', 'readonly', 'id' => 'ledger_date_range_new']); !!}
            </div>
        </div>
       
        <div class="col-md-3">
            <div class="form-group">
                {!! Form::label('ledger_transaction_type', __('lang_v1.transaction_type') . ':') !!}
                {!! Form::select('ledger_transaction_type', ['debit' => 'Debit', 'credit' => 'Credit'], null, ['placeholder' => __('lang_v1.please_select'), 'class' => 'form-control select2', 'readonly', 'id' => 'ledger_transaction_type']); !!}
            </div>
        </div>
       
      
        <div class="col-md-3">
            <div class="form-group">
                {!! Form::label('ledger_general_search', __('lang_v1.search') . ':') !!}
                {!! Form::text('ledger_general_search', null, ['placeholder' => __('lang_v1.search'), 'class' =>
                'form-control', 'id' => 'ledger_general_search']); !!}
            </div>
        </div> --}}


    </div>
    <div class="col-md-12">

        <div id="contact_ledger_div"></div>

    </div>
</div>

<div class="table-responsive">
    <table class="table table-bordered table-striped" id="rp_log_table">
        <thead>
            <tr>
                <th>@lang('fleet::lang.date')</th>
                <th>@lang('fleet::lang.description')</th>
                <th>@lang('fleet::lang.income')</th>
                <th>@lang('fleet::lang.payment')</th>
                <th>@lang('fleet::lang.balance')</th>
                <th>@lang('fleet::lang.paid_amount')</th>
            </tr>
        </thead>
    </table>
</div>