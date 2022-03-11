<!-- Main content -->
<section class="content">
    <div class="row">
        <div class="col-md-12">
            @component('components.filters', ['title' => __('report.filters')])

            <div class="col-md-3" id="location_filter">
                <div class="form-group">
                    {!! Form::label('list_customer_statement_location_id', __('purchase.business_location') . ':') !!}
                    {!! Form::select('list_customer_statement_location_id', $business_locations, null, ['class' =>
                    'form-control select2',
                    'style' => 'width:100%', 'placeholder' => __('lang_v1.all')]); !!}
                </div>
            </div>
            <div class="col-md-3">
                {!! Form::label('list_customer_statement_customer_id', __('contact.customer'). ':', []) !!}
                {!! Form::select('list_customer_statement_customer_id', $customers, null, ['class' => 'form-control select2',
                'placeholder' => __('lang_v1.all'), 'style' => 'width: 100%;']) !!}
            </div>

            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('list_customer_statement_date_range', __('report.date_range') . ':') !!}
                    {!! Form::text('list_customer_statement_date_range', @format_date('first day of this month') . ' ~ ' .
                    @format_date('last
                    day of this month') , ['placeholder' => __('lang_v1.select_a_date_range'), 'class' =>
                    'form-control', 'id' => 'list_customer_statement_date_range', 'readonly']); !!}
                </div>
            </div>


            @endcomponent
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            @component('components.widget', ['class' => 'box-primary'])
            <div class="row" style="margin-top: 20px;">
                <div class="col-md-12">
                    <table class="table table-bordered table-striped" id="customer_statement_list_table" style="width: 100%;">
                        <thead>
                            <tr>
                                <th>@lang('lang_v1.action')</th>
                                <th>@lang('contact.date_printed')</th>
                                <th>@lang('contact.location')</th>
                                <th>@lang('contact.date_from')</th>
                                <th>@lang('contact.date_to')</th>
                                <th>@lang('contact.customer')</th>
                                <th>@lang('contact.statement_no')</th>
                                <th>@lang('contact.statement_amount')</th>
                                <th>@lang('contact.added_by')</th>

                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
        @endcomponent
    </div>
</section>