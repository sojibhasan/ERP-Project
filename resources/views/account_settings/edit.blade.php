<div class="modal-dialog" role="document">
    <div class="modal-content">

        {!! Form::open(['url' => action('AccountSettingController@update', [$account_settings->id]), 'method' => 'PUT', 'id' =>
        'brand_edit_form' ]) !!}

        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                    aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">@lang( 'lang_v1.account_setting' )</h4>
        </div>

        <div class="modal-body">
            <div class="col-md-12">
                <div class="form-group">
                    {!! Form::label('date', __( 'lang_v1.date' ) . ':') !!}
                    {!! Form::text('date', null, ['class' => 'form-control date_edit',
                    'placeholder' => __(
                    'lang_v1.date' ) ]); !!}
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    {!! Form::label('group_id', __( 'lang_v1.account_group' ) . ':') !!}
                    {!! Form::select('group_id', $account_groups, $account_settings->group_id, ['placeholder' =>
                    __('messages.please_select'), 'requied','style' => 'width: 100%', 'class' => 'form-control
                    group_id_edit
                    select2']) !!}
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    {!! Form::label('account_id', __( 'lang_v1.account' ) . ':') !!}
                    {!! Form::select('account_id', $accounts, $account_settings->account_id, ['placeholder' =>
                    __('messages.please_select'), 'requied','style' => 'width: 100%', 'class' => 'form-control
                    account_id_edit
                    select2']) !!}
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    {!! Form::label('amount', __( 'lang_v1.amount' ) . ':') !!}
                    {!! Form::text('amount', $account_settings->amount, ['class' => 'form-control',
                    'placeholder' => __(
                    'lang_v1.amount' ) ]); !!}
                </div>
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
    $('.group_id_edit').change(function () {
        group_id = $(this).val();

        $.ajax({
            method: 'get',
            url: '/accounting-module/get-account-by-group-id/'+group_id,
            data: {  },
            contentType: 'html',
            success: function(result) {
                $('.account_id_edit').empty().append(result);
            },
        });
    })
    $('.date_edit').datepicker('setDate', "{{@format_date($account_settings->date)}}");
</script>