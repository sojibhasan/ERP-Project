
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1> @lang('petro::lang.daily_voucher')
        <small>@lang( 'petro::lang.daily_voucher', ['contacts' => __('petro::lang.mange_daily_voucher') ])</small>
    </h1>
</section>

<!-- Main content -->
<section class="content">
    <div class="row">
        <div class="col-md-12">
            @component('components.filters', ['title' => __('report.filters')])
                <div class="col-md-3">
                    <div class="form-group">
                        {!! Form::label('daily_voucher_location_id',  __('purchase.business_location') . ':') !!}
                        {!! Form::select('daily_voucher_location_id', $business_locations, null, ['class' => 'form-control select2', 'placeholder' => __('petro::lang.all'), 'style' => 'width:100%']); !!}
                    </div>
                </div>
               
                <div class="col-md-3">
                    <div class="form-group">
                        {!! Form::label('daily_voucher_date_range', __('report.date_range') . ':') !!}
                        {!! Form::text('daily_voucher_date_range', @format_date('first day of this month') . ' ~ ' . @format_date('last day of this month') , ['placeholder' => __('lang_v1.select_a_date_range'), 'class' => 'form-control', 'id' => 'daily_voucher_date_range', 'readonly']); !!}
                    </div>
                </div>
       
            @endcomponent
        </div>
    </div>

    @component('components.widget', ['class' => 'box-primary', 'title' => __('petro::lang.all_your_daily_voucher')])
    @slot('tool')
    <div class="box-tools ">
            <button type="button" class="btn  btn-primary btn-modal"
                data-href="{{action('\Modules\Petro\Http\Controllers\DailyVoucherController@create')}}"
                data-container=".pump_modal">
                <i class="fa fa-plus"></i> @lang('messages.add')</button>
        
    </div>
    @endslot
    <div class="table-responsive">
        <table class="table table-bordered table-striped" id="daily_voucher_table" style="width: 100%;">
            <thead>
                <tr>
                    <th>@lang('petro::lang.date_and_time')</th>
                    <th>@lang('petro::lang.location')</th>
                    <th>@lang('petro::lang.date')</th>
                    <th>@lang('petro::lang.daily_voucher_order_no')</th>
                    <th>@lang('petro::lang.pump_operator')</th>
                    <th>@lang('petro::lang.customer')</th>
                    <th>@lang('petro::lang.created_by')</th>
                    <th>@lang('petro::lang.settlement_no')</th>
                    <th>@lang('messages.action')</th>

                </tr>
            </thead>
        </table>
    </div>
    @endcomponent

</section>
<!-- /.content -->
