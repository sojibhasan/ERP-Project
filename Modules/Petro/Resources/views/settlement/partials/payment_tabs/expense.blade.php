<br><br>
<div class="row">
    <div class="col-md-12">
        <div class="col-md-3">
            <div class="form-group">
                {!! Form::label('expense_number', __( 'petro::lang.expense_number' )) !!}
                {!! Form::text('expense_number', $expense_no, ['class' => 'form-control expense_number', 'required',
                'readonly', 'id' => 'expense_number',
                'placeholder' => __(
                'petro::lang.expense_number' ) ]); !!}
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                {!! Form::label('category', __('petro::lang.expense_category').':') !!}
                <div class="input-group">
                    {!! Form::select('expense_category', $expense_categories, null, ['class' => 'form-control
                    expense_fields select2', 'style' => 'width: 100%;', 'id' => 'expense_category',
                    'placeholder' => __('petro::lang.please_select')]); !!}
                    <span class="input-group-btn">
                        <button type="button" class="btn quick_add_expense_category
                        btn-default
                        bg-white btn-flat btn-modal"
                            data-href="{{action('ExpenseCategoryController@create', ['quick_add' => true])}}"
                            title="@lang('lang_v1.add_expense_category')" data-container=".view_modal"><i
                                class="fa fa-plus-circle text-primary fa-lg"></i></button>
                    </span>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                {!! Form::label('reference_no', __( 'petro::lang.reference_no' )) !!}
                {!! Form::text('reference_no', null, ['class' => 'form-control expense_fields reference_no', 'required',
                'placeholder' => __(
                'petro::lang.reference_no' ) ]); !!}
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                {!! Form::label('expense_account', __('petro::lang.expense_account').':') !!}
                {!! Form::select('expense_account', $expense_accounts, null, ['class' => 'form-control expense_fields
                select2', 'style' => 'width: 100%;',
                'placeholder' => __('petro::lang.please_select')]); !!}
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                {!! Form::label('expense_reason', __( 'petro::lang.reason' )) !!}
                {!! Form::text('expense_reason', null, ['class' => 'form-control expense_fields expense_reason',
                'placeholder' => __(
                'petro::lang.reason' ) ]); !!}
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                {!! Form::label('expense_amount', __( 'petro::lang.amount' )) !!}
                {!! Form::text('expense_amount', null, ['class' => 'form-control expense_fields expense_amount
                input_number', 'required',
                'placeholder' => __(
                'petro::lang.amount' ) ]); !!}
            </div>
        </div>

        <div class="col-md-1">
            <button type="button" class="btn btn-primary expense_add"
                style="margin-top: 23px;">@lang('messages.add')</button>
        </div>
    </div>
</div>
<br>
<br>
<div class="row">
    <div class="col-md-12">
        <table class="table table-bordered table-striped" id="expense_table">
            <thead>
                <tr>
                    <th>@lang('petro::lang.expense_number' )</th>
                    <th>@lang('petro::lang.expense_category' )</th>
                    <th>@lang('petro::lang.reference_no' )</th>
                    <th>@lang('petro::lang.expense_account')</th>
                    <th>@lang('petro::lang.reason')</th>
                    <th>@lang('petro::lang.amount')</th>
                    <th>@lang('petro::lang.action' )</th>
                </tr>
            </thead>
            <tbody>
                @php
                $expense_total = $settlement_expense_payments->sum('amount');
                @endphp
                @foreach ($settlement_expense_payments as $expense_payment)
                <tr>
                    <td>{{$expense_payment->expense_number}}</td>
                    <td>{{$expense_payment->category_name}}</td>
                    <td>{{$expense_payment->reference_no}}</td>
                    <td>{{$expense_payment->account_name}}</td>
                    <td>{{$expense_payment->reason}}</td>
                    <td class="expense_amount">{{number_format($expense_payment->amount, $currency_precision)}}</td>
                    <td><button type="button" class="btn btn-xs btn-danger delete_expense_payment"
                            data-href="/petro/settlement/payment/delete-expense-payment/{{$expense_payment->id}}"><i
                                class="fa fa-times"></i></button></td>
                </tr>
                @endforeach

            </tbody>

            <tfoot>
                <tr>
                    <td colspan="6" style="text-align: right; font-weight: bold;">@lang('petro::lang.total') :</td>
                    <td style="text-align: left; font-weight: bold;" class="expense_total">
                        {{number_format($expense_total, $currency_precision)}}</td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
