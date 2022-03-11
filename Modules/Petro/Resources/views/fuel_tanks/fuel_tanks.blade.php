<!-- Main content -->
<section class="content">
    @component('components.widget', ['class' => 'box-primary', 'title' => __('petro::lang.all_your_fuel_tanks')])
    @slot('tool')
    <div class="box-tools">
        <button type="button" class="btn btn-block btn-primary btn-modal add_fuel_tank"
            data-href="{{action('\Modules\Petro\Http\Controllers\FuelTankController@create')}}"
            data-container=".fuel_tank_modal">
            <i class="fa fa-plus"></i> @lang('messages.add')</button>
    </div>
    @endslot
    <div class="table-responsive">
        <table class="table table-bordered table-striped" id="fuel_tanks_table">
            <thead>
                <tr>
                    <th>@lang('petro::lang.date')</th>
                    <th>@lang('petro::lang.fuel_tank_number')</th>
                    <th>@lang('petro::lang.product_name')</th>
                    <th>@lang('petro::lang.location_name')</th>
                    <th>@lang('petro::lang.storage_volume')</th>
                    <th>@lang('petro::lang.current_balance')</th>
                    <th>@lang('petro::lang.bulk_tank')</th>
                    <th class="notexport">@lang('messages.action')</th>

                </tr>
            </thead>
        </table>
    </div>
    @endcomponent

</section>
<!-- /.content -->