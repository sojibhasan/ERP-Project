<!-- Main content -->
<section class="content">
    @component('components.widget', ['class' => 'box-primary', 'title' => __('petro::lang.dashboard_opening_meter')])
    <div class="col-md-3">
        <div class="form-group">
            {!! Form::label('pumps', __('petro::lang.pumps') . ':') !!}
            {!! Form::select('opening_meter_pump_id', $pumps, null, ['class' => 'form-control select2',
            'placeholder'
            => __('petro::lang.please_select'), 'id' => 'opening_meter_pump_id', 'style' => 'width:100%']); !!}
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            {!! Form::label('opening_meter_product_name', __('lang_v1.product') . ':') !!}
            {!! Form::text('opening_meter_product_name', null, ['placeholder' => __('lang_v1.product'), 'class' =>
            'form-control', 'id' => 'opening_meter_product_name', 'readonly']); !!}
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            {!! Form::label('opening_meter_current_meter', __('petro::lang.current_meter') . ':') !!}
            {!! Form::text('opening_meter_current_meter', null, ['placeholder' => __('petro::lang.current_meter'), 'class' =>
            'form-control', 'id' => 'opening_meter_current_meter', 'readonly']); !!}
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            {!! Form::label('opening_meter_reset_meter', __('petro::lang.reset_meter') . ':') !!}
            {!! Form::text('opening_meter_reset_meter', null, ['placeholder' => __('petro::lang.reset_meter'), 'class' =>
            'form-control', 'id' => 'opening_meter_reset_meter']); !!}
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            {!! Form::label('opening_meter_user', __('petro::lang.user') . ':') !!}
            {!! Form::text('opening_meter_user', auth()->user()->username, ['placeholder' => __('lang_v1.user'), 'class' =>
            'form-control', 'id' => 'opening_meter_user', 'readonly']); !!}
        </div>
    </div>
    <div class="col-md-3">
        <button type="button" style="margin-top: 24px" class="btn btn-primary" id="opening_meter_save">@lang('lang_v1.save')</button>
    </div>
    @endcomponent
    @component('components.widget', ['class' => 'box-primary', 'title' => __('petro::lang.all_your_opening_meter')])
    
    <div class="table-responsive">
        <table class="table table-bordered table-striped" id="opening_meter_table" style="width: 100%;">
            <thead>
                <tr>
                    <th>@lang('petro::lang.date')</th>
                    <th>@lang('petro::lang.location')</th>
                    <th>@lang('petro::lang.pump_no')</th>
                    <th>@lang('petro::lang.product')</th>
                    <th>@lang('petro::lang.pump_starting_meter')</th>
                    <th>@lang('petro::lang.reset_meter')</th>
                    <th>@lang('petro::lang.user')</th>

                </tr>
            </thead>
        </table>
    </div>
    @endcomponent


</section>
<!-- /.content -->