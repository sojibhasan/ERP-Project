<!-- Main content -->
<section class="content">
    <div class="row">
        <div class="col-md-12">
            @component('components.filters', ['title' => __('report.filters')])
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('location_id', __('purchase.business_location') . ':') !!}
                    {!! Form::select('meter_resettings_location_id', $business_locations, null, ['class' => 'form-control
                    select2',
                    'placeholder' => __('petro::lang.all'), 'id' => 'meter_resettings_location_id', 'style' => 'width:100%']); !!}
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('tanks', __('petro::lang.tanks') . ':') !!}
                    {!! Form::select('meter_resettings_tanks', $tanks, null, ['class' => 'form-control select2', 'placeholder'
                    => __('petro::lang.all'), 'id' => 'meter_resettings_tanks', 'style' => 'width:100%']); !!}
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('products', __('petro::lang.products') . ':') !!}
                    {!! Form::select('meter_resettings_product_id', $products, null, ['class' => 'form-control select2',
                    'placeholder'
                    => __('petro::lang.all'), 'id' => 'meter_resettings_product_id', 'style' => 'width:100%']); !!}
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('meter_resettings_date_range', __('report.date_range') . ':') !!}
                    {!! Form::text('meter_resettings_date_range', @format_date('first day of this month') . ' ~ ' .
                    @format_date('last
                    day of this month') , ['placeholder' => __('lang_v1.select_a_date_range'), 'class' =>
                    'form-control', 'id' => 'meter_resettings_date_range', 'readonly']); !!}
                </div>
            </div>
            @endcomponent
        </div>
    </div>

    @component('components.widget', ['class' => 'box-primary', 'title' => __('petro::lang.meter_resettings')])
    @slot('tool')
    <div class="box-tools ">
            <button type="button" class="btn  btn-primary btn-modal"
                data-href="{{action('\Modules\Petro\Http\Controllers\MeterResettingController@create')}}"
                data-container=".pump_modal">
                <i class="fa fa-sliders"></i> @lang('petro::lang.add_meter_reset')</button>
    </div>
    @endslot
    <div class="col-md-12">
        <div class="row">
            <div class="col-md-5 text-red" style="margin-top: 14px;">
                <b>@lang('petro::lang.date_range'): <span class="meter_resettings_from_date"></span> @lang('petro::lang.to') <span
                        class="meter_resettings_to_date"></span> </b>
            </div>
            <div class="col-md-7">
                <div class="text-center pull-left">
                    <h5 style="font-weight: bold;">{{request()->session()->get('business.name')}} <br>
                        <span class="meter_resettings_location_name">@lang('petro::lang.all')</span></h5>
                </div>
            </div>
        </div>
        <div class="row" style="margin-top: 20px;">
            <div class="table-responsive">
                <table class="table table-bordered table-striped" id="dip_meter_resettings_table" style="width: 100%;">
                    <thead>
                        <tr>
                            <th class="notexport">@lang('petro::lang.action')</th>
                            <th>@lang('petro::lang.transaction_date')</th>
                            <th>@lang('petro::lang.location')</th>
                            <th>@lang('petro::lang.pump_no')</th>
                            <th>@lang('petro::lang.tank')</th>
                            <th>@lang('petro::lang.last_meter')</th>
                            <th>@lang('petro::lang.new_reset_meter')</th>
                            <th>@lang('petro::lang.user')</th>
                            <th>@lang('petro::lang.reason')</th>

                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
    @endcomponent
</section>
<!-- /.content -->