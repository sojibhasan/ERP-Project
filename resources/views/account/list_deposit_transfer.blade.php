<div class="row">
    <div class="col-md-12">
        <div class="col-md-3">
            <div class="form-group">
                {!! Form::label('list_deposit_transfer_date_range', __('lang_v1.date_range').':') !!}
                {!! Form::text('list_deposit_transfer_date_range', null, ['class' => 'form-control ', 'style' => 'width: 100%;']); !!}
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                {!! Form::label('list_deposit_transfer_type', __('lang_v1.type').':') !!}
                {!! Form::select('list_deposit_transfer_type', ['deposit' => 'Deposit', 'fund_transfer' => 'Transfer'], null, ['class' => 'form-control
                select2', 'style' => 'width: 100%;', 'placeholder' => __('lang_v1.all')]); !!}
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                {!! Form::label('from_account_id', __('lang_v1.from_account').':') !!}
                {!! Form::select('from_account_id', $accounts, null, ['class' => 'form-control
                select2', 'style' => 'width: 100%;', 'placeholder' => __('lang_v1.all')]); !!}
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                {!! Form::label('to_account_id', __('lang_v1.to_account').':') !!}
                {!! Form::select('to_account_id', $accounts, null, ['class' => 'form-control
                select2', 'style' => 'width: 100%;', 'placeholder' => __('lang_v1.all')]); !!}
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                {!! Form::label('user_id', __('lang_v1.user').':') !!}
                {!! Form::select('user_id', $users, null, ['class' => 'form-control
                select2', 'style' => 'width: 100%;', 'placeholder' => __('lang_v1.all')]); !!}
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <table class="table table-striped table-bordered" id="list_deposit_transfer_table" style="width: 100%;">
            <thead>
                <tr>
                    <th>@lang( 'lang_v1.action' )</th>
                    <th>@lang( 'lang_v1.date' )</th>
                    <th>@lang( 'lang_v1.type' )</th>
                    <th>@lang( 'lang_v1.amount' )</th>
                    <th>@lang( 'lang_v1.from_account' )</th>
                    <th>@lang( 'lang_v1.to_account' )</th>
                    <th>@lang( 'lang_v1.cheque_number' )</th>
                    <th>@lang( 'lang_v1.user' )</th>
                </tr>
            </thead>
            <tbody>

            </tbody>
        </table>
    </div>
</div>