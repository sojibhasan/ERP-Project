@extends('layouts.'.$layout)
@section('title', __('petro::lang.unload_stock'))

@section('content')
<section class="content-header">
    <h1>@lang('petro::lang.pumper_day_entries') <br>
        <span class="text-red">{{$pump_operator->name}}</span>
    </h1>
    <a href="{{action('Auth\PumpOperatorLoginController@logout')}}" class="btn btn-flat btn-lg pull-right"
        style=" background-color: orange; color: #fff; margin-left: 5px;">@lang('petro::lang.logout')</a>
    <a href="{{action('\Modules\Petro\Http\Controllers\PumpOperatorController@dashboard')}}"
        class="btn btn-flat btn-lg pull-right"
        style="color: #fff; background-color:#810040; margin-left: 5px;">@lang('petro::lang.dashboard')
    </a>
</section>
<div class="clearfix"></div>
@include('petro::pump_operators.partials.unload_stock')

<div class="modal fade pump_operator_modal" role="dialog" aria-labelledby="gridSystemModalLabel">
</div>

@endsection


@section('javascript')
<script type="text/javascript">
   $(document).ready( function(){
    pump_operators_unload_stock_table = $('#pump_operators_unload_stock_table').DataTable({
        processing: true,
        serverSide: true,
        aaSorting: [[0, 'desc']],
        ajax: {
            url: "{{action('\Modules\Petro\Http\Controllers\UnloadStockController@index', ['only_pumper' => false])}}",
            data: function(d) {
                d.pump_operator_id = {{Auth::user()->pump_operator_id}};
            },
        },
        columnDefs: [ {
            "targets": 0,
            "orderable": false,
            "searchable": false
        }],
        columns: [
            { data: 'date_and_time', name: 'date_and_time' },
            { data: 'fuel_tank_number', name: 'fuel_tank_number' },
            { data: 'product', name: 'product' },
            { data: 'dip_reading', name: 'dip_reading' },
            { data: 'current_stock', name: 'current_stock' },
            { data: 'unloaded_qty', name: 'unloaded_qty' },
            { data: 'total_qty', name: 'total_qty' },
            { data: 'username', name: 'users.username'},
        ],
        fnDrawCallback: function(oSettings) {
            __currency_convert_recursively($('#pump_operators_unload_stock_table'));
        },
    });

    $('#unload_stock_product_id, #unload_stock_tank_id, #unload_stock_date_range').change(function(){
        pump_operators_unload_stock_table.ajax.reload();
    });
});


</script>
@endsection