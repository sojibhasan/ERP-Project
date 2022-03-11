<div class="modal-dialog" role="document">
    <div class="modal-content">

        {!! Form::open(['url' => action('\Modules\Superadmin\Http\Controllers\EditContactEntriesController@update',
        $contact_transaction->id), 'method' => 'put', 'id' => 'edit_contact_transaction_form'
        ]) !!}

        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                    aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">@lang( 'superadmin::lang.edit' )</h4>
        </div>

        <div class="modal-body">
            {!! Form::hidden('account_id', $contact_transaction->account_id, []) !!}
            {!! Form::hidden('contact_id', $contact_transaction->contact_id, []) !!}
            {!! Form::hidden('business_id', $business_id, []) !!}
            {!! Form::hidden('contact_ledger_id', $contact_transaction->id, []) !!}
            <div class="form-group">
                {!! Form::label('amount', __( 'account.amount' ) .":*") !!}
                {!! Form::text('amount', $contact_transaction->amount, ['class' => 'form-control',
                'required','placeholder' => __(
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