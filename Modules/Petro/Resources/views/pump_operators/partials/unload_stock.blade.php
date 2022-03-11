<!-- Main content -->
<section class="content">
    @if(empty($only_pumper))
    <div class="row">
        <div class="col-md-12">
            @component('components.filters', ['title' => __('report.filters')])
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('unload_stock_tank_id', __('petro::lang.tank') . ':') !!}
                    {!! Form::select('unload_stock_tank_id', $tanks, null, ['class' => 'form-control
                    select2', 'placeholder'
                    => __('petro::lang.all'), 'id' => 'unload_stock_tank_id', 'style' => 'width:100%']); !!}
                </div>
            </div>

            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('unload_stock_product_id', __('petro::lang.product') . ':') !!}
                    {!! Form::select('unload_stock_product_id', $products, null, ['class' => 'form-control
                    select2',
                    'placeholder'
                    => __('petro::lang.all'), 'id' => 'unload_stock_product_id', 'style' => 'width:100%']); !!}
                </div>
            </div>

            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('unload_stock_date_range', __('report.date_range') . ':') !!}
                    {!! Form::text('unload_stock_date_range', @format_date('first day of this month') . ' ~ ' .
                    @format_date('last day of this month') , ['placeholder' => __('lang_v1.select_a_date_range'),
                    'class' =>
                    'form-control', 'id' => 'unload_stock_date_range', 'readonly']); !!}
                </div>
            </div>
            @endcomponent
        </div>
    </div>
    @endif

    @component('components.widget', ['class' => 'box-primary', 'title' =>
    __('petro::lang.unloaded_stocks')])
    <div class="table-responsive">
        <table class="table table-bordered table-striped" id="pump_operators_unload_stock_table" style="width: 100%;">
            <thead>
                <tr>
                    <th>@lang('petro::lang.date_and_time')</th>
                    <th>@lang('petro::lang.tank')</th>
                    <th>@lang('petro::lang.product')</th>
                    <th>@lang('petro::lang.current_dip')</th>
                    <th>@lang('petro::lang.current_stock')</th>
                    <th>@lang('petro::lang.unloaded_qty')</th>
                    <th>@lang('petro::lang.total_qty')</th>
                    <th>@lang('petro::lang.added_by')</th>
                </tr>
            </thead>
        </table>
    </div>
    @endcomponent

</section>
<!-- /.content -->