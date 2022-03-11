<div class="modal-dialog" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                    aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">@lang( 'contact.edit_customer_statement_setting' )</h4>
        </div>
        {!! Form::open(['url' => action('CustomerStatementSettingController@update', $setting->id), 'method' => 'put', 'id' =>
        'customer_statement_setting_add_form' ]) !!}
        <div class="modal-body">
            <div class="row">
                <div class="col-md-4">
                    {!! Form::label('separate_customer_statement_no',
                    __('contact.enable_separate_customer_statement_no'). ':', []) !!}
                    {!! Form::select('edit_enable_separate_customer_statement_no', ['0' => 'No', '1' => 'Yes'],
                    $setting->enable_separate_customer_statement_no, ['class' => 'form-control', 'placeholder' =>
                    __('lang_v1.please_select'), 'id' => 'edit_enable_separate_customer_statement_no']) !!}
                </div>
                <div class="col-md-4">
                    {!! Form::label('customer_id', __('contact.customer'). ':', []) !!}
                    {!! Form::select('edit_customer_id', $customers, $setting->customer_id, ['class' => 'form-control
                    select2', 'placeholder' => __('lang_v1.please_select'), 'id' => 'edit_customer_id', 'style' => 'width: 100%;']) !!}
                </div>
                <div class="col-md-4">
                    {!! Form::label('starting_no', __('contact.starting_no'). ':', []) !!}
                    {!! Form::text('edit_starting_no', $setting->starting_no, ['class' => 'form-control', 'id' => 'edit_starting_no', 'placeholder'
                    => __('contact.starting_no')]) !!}
                </div>
            </div>

        </div><!-- /.modal-content -->
        <div class="modal-footer">
            <button type="button" id="edit_statement_settings" class="btn btn-primary">@lang( 'messages.save' )</button>
            <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
        </div>
    </div><!-- /.modal-dialog -->
    {!! Form::close() !!}
    <script>
        $('#customer_id').select2();
    </script>