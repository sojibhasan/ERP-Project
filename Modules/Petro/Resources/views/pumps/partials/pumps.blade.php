<!-- Main content -->
<section class="content">
    @component('components.widget', ['class' => 'box-primary', 'title' => __('petro::lang.all_your_list_pumps')])
    @slot('tool')
    <div class="box-tools ">
            <button type="button" class="btn  btn-primary btn-modal"
                data-href="{{action('\Modules\Petro\Http\Controllers\PumpController@create')}}"
                data-container=".fuel_tank_modal">
                <i class="fa fa-plus"></i> @lang('messages.add')</button>
            <a class="btn  btn-primary"
               href="{{action('\Modules\Petro\Http\Controllers\PumpController@importPumps')}}">
                <i class="fa fa-download "></i> @lang('petro::lang.import')</a>
        
    </div>
    @endslot
    <div class="table-responsive">
        <table class="table table-bordered table-striped" id="list_pumps_table">
            <thead>
                <tr>
                    <th>@lang('petro::lang.date')</th>
                    <th>@lang('petro::lang.transaction_date')</th>
                    <th>@lang('petro::lang.pump_no')</th>
                    <th>@lang('petro::lang.pump_name')</th>
                    <th>@lang('petro::lang.pump_starting_meter')</th>
                    <th>@lang('petro::lang.pump_current_meter')</th>
                    <th>@lang('petro::lang.product_name')</th>
                    <th>@lang('petro::lang.location')</th>
                    <th>@lang('petro::lang.fuel_tank')</th>
                    <th class="notexport">@lang('messages.action')</th>

                </tr>
            </thead>
        </table>
    </div>
    @endcomponent

    <div class="modal fade fuel_tank_modal" role="dialog" aria-labelledby="gridSystemModalLabel">
    </div>

</section>
<!-- /.content -->