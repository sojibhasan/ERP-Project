<!-- Main content -->
<section class="content">
    <div class="row">
        {!! Form::open(['url' => '/interest-settings', 'method' => 'post', 'id' => 'interest_settings_form' ]) !!}
        <div class="col-md-3">
            <div class="form-group">
                {!! Form::label('date', __( 'lang_v1.date' ) ) !!}
                {!! Form::text('date', null, ['class' => 'form-control transaction_date', 'required', 'id' => 'transaction_date', 'readonly',
                'style' => 'width: 100%;', 'placeholder' => __('petro::lang.transaction_date' ) ]); !!}
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                {!! Form::label('contact_group_id', __('lang_v1.customer_group').':') !!}
                {!! Form::select('contact_group_id', $customer_groups, null, ['class' => 'form-control select2', 'style' => 'width: 100%;']); !!}
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                {!! Form::label('account_id', __('lang_v1.income_account').':') !!}
                {!! Form::select('account_id', $income_accounts, null, ['class' => 'form-control select2', 'style' => 'width: 100%;']); !!}
            </div>
        </div>
        <div class="col-md-1">
            <button type="submit" class="btn btn-primary btn_interest_settings"
                    style="margin-top: 23px;">@lang('messages.save')</button>
        </div>
        {!! Form::close() !!}
    </div>
    <br>
    <br>
    <div class="row">
        <div class="col-md-12">
            @component('components.widget', ['class' => 'box-primary'])
                <table class="table table-bordered table-striped" id="interest_settings_table" style="width: 100%;">
                    <thead>
                        <tr>
                            <th>@lang('lang_v1.date' )</th>
                            <th>@lang('lang_v1.customer_group' )</th>
                            <th>@lang('lang_v1.linked_account' )</th>
                            <th>@lang('contact.user')</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            @endcomponent
        </div>
    </div>
</section>
<!-- /.content -->
