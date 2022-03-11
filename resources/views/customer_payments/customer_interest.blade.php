<!-- Main content -->
<section class="content">
    <div class="row">
        <div class="col-md-12">
            @component('components.filters', ['title' => __('report.filters')])
                <div class="col-md-3">
                    <div class="form-group">
                        {!! Form::label('customer_payment_date_range', __('lang_v1.date_range').':') !!}
                        {!! Form::text('customer_payment_date_range', null, ['class' => 'form-control ', 'style' => 'width: 100%;']); !!}
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        {!! Form::label('customer_payment_customer_id', __('lang_v1.customer').':') !!}
                        {!! Form::select('customer_payment_customer_id', $customers, null, ['class' => 'form-control
                        select2', 'style' => 'width: 100%;', 'placeholder' => __('lang_v1.all')]); !!}
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        {!! Form::label('customer_payment_location_id', __('lang_v1.location').':') !!}
                        {!! Form::select('customer_payment_location_id', $business_locations, null, ['class' => 'form-control
                        select2', 'style' => 'width: 100%;', 'placeholder' => __('lang_v1.all')]); !!}
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        {!! Form::label('customer_payment_method', __('lang_v1.payment_method').':') !!}
                        {!! Form::select('customer_payment_method', $payment_types, null, ['class' => 'form-control
                        select2', 'style' => 'width: 100%;', 'placeholder' => __('lang_v1.all')]); !!}
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        {!! Form::label('paid_in_type', __('lang_v1.paid_in_type').':') !!}
                        {!! Form::select('paid_in_type', ['customer_page' => 'Customer Page', 'all_sale_page' => 'All Sale Page', 'settlement' => 'Settlement'], null, ['class' => 'form-control
                        select2', 'style' => 'width: 100%;', 'placeholder' => __('lang_v1.all')]); !!}
                    </div>
                </div>
            @endcomponent
        </div>
    </div>
    <br>
    <br>
    <div class="row">
        <div class="col-md-12">
            @component('components.widget', ['class' => 'box-primary'])
                <table class="table table-bordered table-striped" id="customer_payments_table" style="width: 100%;">
                    <thead>
                    <tr>
                        <th class="notexport">@lang('messages.action')</th>
                        <th>@lang('lang_v1.date' )</th>
                        <th>@lang('lang_v1.location' )</th>
                        <th>@lang('lang_v1.customer' )</th>
                        <th>@lang('lang_v1.interest' )</th>
                        <th>@lang('lang_v1.amount' )</th>
                        <th>@lang('lang_v1.payment_method' )</th>
                        <th>@lang('lang_v1.paid_in_type' )</th>
                       <th>@lang('contact.user')</th>
                    </tr>
                    </thead>
                    <tbody>
                    </tbody>
                    <tfoot>
                    </tfoot>
                </table>
            @endcomponent
        </div>
    </div>
</section>
<!-- /.content -->
