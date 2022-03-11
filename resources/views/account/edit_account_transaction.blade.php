<div class="modal-dialog" role="document">
    <div class="modal-content">

        {!! Form::open(['url' => action('AccountController@updateAccountTransaction', $account_transaction->id), 'method' => 'post', 'id' => 'edit_account_transaction_form'
        ]) !!}

        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                    aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">@lang( 'account.add_account' )</h4>
        </div>

        <div class="modal-body">
            

            <div class="form-group asset_type">
                {!! Form::label('account_id', __( 'account.account' ) .":") !!}
                {!! Form::select('account_id', $accounts, $account_transaction->account_id, ['placeholder' =>
                __('messages.please_select'), 'class' => 'form-control']) !!}
            </div>

            <div class="form-group">
                {!! Form::label('amount', __( 'account.amount' ) .":*") !!}
                {!! Form::text('amount', @num_format($account_transaction->amount), ['class' => 'form-control', 'required','placeholder' => __(
                'account.amount' ) ]); !!}
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
   
</script>