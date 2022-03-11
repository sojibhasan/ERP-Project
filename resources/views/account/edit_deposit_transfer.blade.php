<div class="modal-dialog" role="document">
    <div class="modal-content">

        {!! Form::open(['url' => action('AccountController@updateDepositTransfer', $account_transaction->id), 'method' => 'post', 'id' => 'edit_account_transaction_form'
        ]) !!}

        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                    aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">@lang( 'lang_v1.edit' )</h4>
        </div>

        <div class="modal-body">
            
            <div class="form-group">
                {!! Form::label('operation_date', __( 'lang_v1.account' ) .":") !!}
                {!! Form::text('operation_date', null, ['placeholder' =>
                __('lang_v1.date'), 'class' => 'form-control operation_date']) !!}
            </div>
            <div class="form-group">
                {!! Form::label('from_account', __( 'lang_v1.from_account' ) .":") !!}
                {!! Form::select('from_account', $accounts, $account_transaction->from_account, ['placeholder' =>
                __('messages.please_select'), 'class' => 'form-control select2']) !!}
            </div>

            <div class="form-group">
                {!! Form::label('to_account', __( 'lang_v1.to_account' ) .":") !!}
                {!! Form::select('to_account', $accounts, $account_transaction->to_account, ['placeholder' =>
                __('messages.please_select'), 'class' => 'form-control select2']) !!}
            </div>

            <div class="form-group">
                {!! Form::label('amount', __( 'account.amount' ) .":*") !!}
                {!! Form::text('amount', @num_format($account_transaction->amount), ['class' => 'form-control', 'required','placeholder' => __(
                'account.amount' ) ]); !!}
            </div>
            <div class="form-group">
                {!! Form::label('cheque_number', __( 'lang_v1.cheque_number' ) .":") !!}
                {!! Form::text('cheque_number', $account_transaction->cheque_number, ['class' => 'form-control', 'placeholder' => __(
                'lang_v1.cheque_number' ) ]); !!}
            </div>

        </div>

        <div class="modal-footer">
            <button type="submit" class="btn btn-primary">@lang( 'messages.update' )</button>
            <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
        </div>

        {!! Form::close() !!}

    </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->

<script>
   $('.operation_date').datepicker('setDate', '{{@format_date($account_transaction->operation_date)}}')
   $('.select2').select2();
</script>