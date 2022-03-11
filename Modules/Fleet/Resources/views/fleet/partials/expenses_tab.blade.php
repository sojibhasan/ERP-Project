<div class="col-md-12">

    <div class="col-md-3">
        <div class="form-group">
            {!! Form::label('expense_date_range', __('report.date_range') . ':') !!}
            {!! Form::text('expense_date_range', null, ['placeholder' => __('lang_v1.select_a_date_range'), 'class' =>
            'form-control', 'readonly']); !!}
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            {!! Form::label('expense_category_id', __('fleet::lang.category') . ':') !!}
            {!! Form::select('expense_category_id', $expense_categories, null, ['class' => 'form-control select2',
            'style' => 'width:100%',
            'placeholder' => __('lang_v1.all')]); !!}
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            {!! Form::label('expense_payment_status', __('purchase.payment_status') . ':') !!}
            {!! Form::select('expense_payment_status', ['paid' => __('lang_v1.paid'), 'due' =>
            __('lang_v1.due'), 'partial' => __('lang_v1.partial')], null, ['class' => 'form-control select2',
            'style' => 'width:100%', 'placeholder' => __('lang_v1.all')]); !!}
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            {!! Form::label('expense_payment_method', __('purchase.payment_method') . ':') !!}
            {!! Form::select('expense_payment_method', $payment_types, null, ['class' => 'form-control select2', 'style'
            => 'width:100%', 'placeholder' => __('lang_v1.all')]); !!}
        </div>
    </div>

</div>
<div class="clearfix"></div>
<br>
<br>
<div class="col-md-12">
    <div class="table-responsive">
        <table class="table table-bordered table-striped" id="fleet_expense_table" style="width: 100%">
            <thead>
                <tr>
                    <th>@lang('fleet::lang.date')</th>
                    <th>@lang('fleet::lang.expense_number')</th>
                    <th>@lang('fleet::lang.expense_category')</th>
                    <th>@lang('fleet::lang.amount')</th>
                    <th>@lang('fleet::lang.payment_status')</th>
                    <th>@lang('fleet::lang.payment_method')</th>
                </tr>
            </thead>
        </table>
    </div>
   
</div>


{{-- 
<th>@lang('fleet::lang.date')</th>
<th>@lang('fleet::lang.expense_number')</th>
<th>@lang('fleet::lang.expense_category')</th>
<th>@lang('fleet::lang.amount')</th>
<th>@lang('fleet::lang.payment_status')</th>
<th>@lang('fleet::lang.payment_method')</th> --}}