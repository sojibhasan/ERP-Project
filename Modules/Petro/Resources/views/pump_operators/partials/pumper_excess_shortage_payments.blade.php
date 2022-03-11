<!-- Main content -->
<section class="content">
    <div class="row">
        <div class="col-md-12">
            @component('components.filters', ['title' => __('report.filters')])
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('pesp_location_id', __('purchase.business_location') . ':') !!}
                    {!! Form::select('pesp_location_id', $business_locations, null, ['class' => 'form-control select2',
                    'placeholder' => __('petro::lang.all'), 'style' => 'width:100%']); !!}
                </div>
            </div>
            <div class="col-sm-3">
                <div class="form-group">
                    {!! Form::label('pesp_pump_operator', __('petro::lang.pump_operator').':') !!}
                    {!! Form::select('pesp_pump_operator', $pump_operators, null, ['class' => 'form-control select2' ,
                    'style' => 'width:100%',
                    'placeholder' => __('petro::lang.all')]); !!}
                </div>
            </div>

            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('pesp_date_range', __('report.date_range') . ':') !!}
                    {!! Form::text('pesp_date_range', @format_date('first day of this month') . ' ~ ' .
                    @format_date('last
                    day of this month') , ['placeholder' => __('lang_v1.select_a_date_range'), 'class' =>
                    'form-control', 'id' => 'pesp_date_range', 'readonly']); !!}
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('pesp_type', __('petro::lang.type') . ':') !!}
                    {!! Form::select('pesp_type', ['commission' => __('petro::lang.commission'),'excess' =>
                    __('petro::lang.excess'),'shortage' => __('petro::lang.shortage')], null, ['class' => 'form-control
                    select2', 'style' => 'width:100%', 'placeholder' => __('lang_v1.all')]); !!}
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('pesp_payment_type', __('petro::lang.payment_type') . ':') !!}
                    {!! Form::select('pesp_payment_type', $payment_types, null, ['class' => 'form-control
                    select2', 'style' => 'width:100%', 'placeholder' => __('lang_v1.all')]); !!}
                </div>
            </div>
            @endcomponent
        </div>
    </div>

    @component('components.widget', ['class' => 'box-primary', 'title' =>
    __('petro::lang.pumper_excess_shortage_payments')])
    <div class="table-responsive">
        <table class="table table-bordered table-striped" id="pumper_excess_shortage_payments_table" style="width: 100%;">
            <thead>
                <tr>
                    <th>@lang('petro::lang.action')</th>
                    <th>@lang('petro::lang.transaction_date')</th>
                    <th>@lang('petro::lang.location')</th>
                    <th>@lang('petro::lang.pump_operator')</th>
                    <th>@lang('petro::lang.current_shortage')</th>
                    <th>@lang('petro::lang.current_excess')</th>
                    <th>@lang('petro::lang.shortage_recovered')</th>
                    <th>@lang('petro::lang.excess_paid')</th>

                </tr>
            </thead>

            <tfoot>
            </tfoot>
        </table>
    </div>
    @endcomponent

</section>
<!-- /.content -->