<!-- Main content -->
<section class="content">
    @component('components.widget', ['class' => 'box-primary', 'title' => __(
    'contact.settings_customer_statements')])
        <div class="row">
            <div class="col-md-4">
                {!! Form::label('separate_customer_statement_no', __('contact.enable_separate_customer_statement_no'). ':', []) !!} @if(!empty($help_explanations['enable_separate_customer_statement_no'])) @show_tooltip($help_explanations['enable_separate_customer_statement_no']) @endif
                {!! Form::select('enable_separate_customer_statement_no', ['0' => 'No', '1' => 'Yes'], null, ['class' => 'form-control', 'placeholder' => __('lang_v1.please_select'), 'id' => 'enable_separate_customer_statement_no']) !!}
            </div>
            <div class="col-md-3 customer_separate_field hide">
                {!! Form::label('customer_id', __('contact.customer'). ':', []) !!}
                {!! Form::select('customer_id', $customers, null, ['class' => 'form-control select2', 'placeholder' => __('lang_v1.please_select'), 'style' => 'width: 100%;']) !!}
            </div>
            <div class="col-md-3 customer_separate_field hide">
                {!! Form::label('starting_no', __('contact.starting_no'). ':', []) !!}
                {!! Form::text('starting_no', null, ['class' => 'form-control', 'placeholder' => __('contact.starting_no')]) !!}
            </div>
            <div class="col-md-2 customer_separate_field hide">
               <button type="button" class="btn btn-primary" id="settings_statement_btn" style="margin-top: 23px;">@lang('messages.add')</button>
            </div>
        </div>
    @endcomponent

    @component('components.widget', ['class' => 'box-primary', 'title' => __(
    'contact.all_customer_statement_numbers')])
        <div class="table-responsive">
            <table class="table table-bordered table-striped" id="statement_settings_table" style="width: 100%;">
                <thead>
                    <tr>
                        <th>@lang( 'contact.customer' )</th>
                        <th>@lang( 'contact.starting_no' )</th>
                        <th>@lang( 'messages.action' )</th>
                    </tr>
                </thead>
            </table>
        </div>
        @endcomponent
</section>
<!-- /.content -->