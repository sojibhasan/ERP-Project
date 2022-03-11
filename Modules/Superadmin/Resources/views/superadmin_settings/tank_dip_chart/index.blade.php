<div class="pos-tab-content @if(session('status.tank_dip_chart')) active @endif">
    <!-- Main content -->
<section class="content">
    <div class="row">
        <div class="col-md-12">
            @component('components.filters', ['title' => __('report.filters')])
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('filter_sheet_name', __('superadmin::lang.sheet_name') . ':') !!}
                    {!! Form::select('filter_sheet_name', $sheet_names, null, ['class' => 'form-control
                    select2',
                    'placeholder' => __('petro::lang.all'), 'id' => 'filter_sheet_name', 'style' => 'width:100%']); !!}
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('filter_tank_manufacturer', __('superadmin::lang.tank_manufacturer') . ':') !!}
                    {!! Form::select('filter_tank_manufacturer', $tank_manufacturers, null, ['class' => 'form-control
                    select2',
                    'placeholder' => __('petro::lang.all'), 'id' => 'filter_tank_manufacturer', 'style' => 'width:100%']); !!}
                </div>
            </div>
           
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('filter_tank_capacity', __('superadmin::lang.tank_capacity') . ':') !!}
                    {!! Form::select('filter_tank_capacity', $tank_capacitys, null, ['class' => 'form-control
                    select2',
                    'placeholder' => __('petro::lang.all'), 'id' => 'filter_tank_capacity', 'style' => 'width:100%']); !!}
                </div>
            </div>
           
            {{-- <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('tank_dip_chart_date_range', __('report.date_range') . ':') !!}
                    {!! Form::text('tank_dip_chart_date_range', @format_date('first day of this month') . ' ~ ' .
                    @format_date('last
                    day of this month') , ['placeholder' => __('lang_v1.select_a_date_range'), 'class' =>
                    'form-control', 'id' => 'tank_dip_chart_date_range', 'readonly']); !!}
                </div>
            </div> --}}
            @endcomponent
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            @component('components.widget', ['class' => 'box-primary', 'title' => __(
            'superadmin::lang.tank_dip_chart')])
            @slot('tool')
            <div class="box-tools">
                <button type="button" id="add_button_tank_dip_chart" class="btn btn-primary btn-modal pull-right" 
                data-container=".tank_dip_chart_model"
                data-href="{{action('\Modules\Superadmin\Http\Controllers\TankDipChartController@create')}}">
                <i class="fa fa-plus"></i> @lang( 'messages.add' )</button> &nbsp;
                <button type="button" id="add_button_tank_dip_chart" class="btn btn-primary btn-modal pull-right" 
                data-container=".tank_dip_chart_model" style="margin-right: 10px"
                data-href="{{action('\Modules\Superadmin\Http\Controllers\TankDipChartController@getImport')}}">
                <i class="fa fa-download"></i> @lang( 'superadmin::lang.import' )</button>
            </div>
            @endslot

            <div class="row">
                <div class="col-md-12">
                    <table class="table table-bordered table-striped" id="tank_dip_chart_table" style="width: 100%;">
                        <thead>
                            <tr>
                                <th>@lang( 'superadmin::lang.dip_reading' )</th>
                                <th>@lang( 'superadmin::lang.dip_reading_value' )</th>
                                <th>@lang( 'messages.action' )</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                            
                    </table>

                </div>
            </div>
            @endcomponent
        </div>
    </div>

</section>
<!-- /.content -->
</div>