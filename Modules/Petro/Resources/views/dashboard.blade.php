@extends('layouts.app')
@section('title', __('petro::lang.dashboard'))

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>{{ __('home.welcome_message', ['name' => Session::get('user.first_name')]) }}
    </h1>
</section>
<!-- Main content -->
<section class="content no-print">
    @if(!empty($message)) {!! $message !!} @endif
    <div class="row">
        @foreach($fuel_tanks as $key => $tank)
        <div class="col-md-4 text-center" style="margin-bottom: 40px;">
            <div id="chart_div_{{$key}}" style=" width: auto;  margin: 0 auto !important; display: inline-block;"></div>
            <div class="clearfix"></div>
            <h4>{{$tank->fuel_tank_number}}</h4>
            @php
            $balance = 0;
            $purchase_query = \Modules\Petro\Entities\FuelTank::leftjoin('tank_purchase_lines', 'fuel_tanks.id', 'tank_purchase_lines.tank_id')
                ->where('fuel_tanks.id', $tank->id)
                ->select([
                    DB::raw('SUM(tank_purchase_lines.quantity) as pruchase_qty')
                ])->first();
            $sell_query = \Modules\Petro\Entities\TankSellLine::where('tank_id', $tank->id)
                ->select([
                    DB::raw('SUM(quantity) as sell_qty')
                ])->first();

            $balance = $purchase_query->pruchase_qty - $sell_query->sell_qty;
            @endphp
            <p>@lang('petro::lang.current_balance'): {{@num_format($balance)}}</p>

            <p>@lang('petro::lang.storage_volume'): {{@num_format($tank->storage_volume)}}</p>

        </div>
        @endforeach
    </div>
</section>
<!-- /.content -->
@endsection

@section('javascript')
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
@foreach($fuel_tanks as $key => $tank)
@php
$balance = 0;
$purchase_query = \Modules\Petro\Entities\FuelTank::leftjoin('tank_purchase_lines', 'fuel_tanks.id', 'tank_purchase_lines.tank_id')
    ->where('fuel_tanks.id', $tank->id)
    ->select([
        DB::raw('SUM(tank_purchase_lines.quantity) as pruchase_qty')
    ])->first();
$sell_query = \Modules\Petro\Entities\TankSellLine::where('tank_id', $tank->id)
    ->select([
        DB::raw('SUM(quantity) as sell_qty')
    ])->first();
$balance = $purchase_query->pruchase_qty - $sell_query->sell_qty;
@endphp
<script type="text/javascript">
    google.charts.load('current', {'packages':['gauge']});
    google.charts.setOnLoadCallback(drawChart);
        google.charts.load('current', {
        'packages': ['gauge']
        });
        google.charts.setOnLoadCallback(drawChart);
        function drawChart() {

        var data = google.visualization.arrayToDataTable([

            ['Label', 'Value'],

            ['', {{round(($balance / $tank->storage_volume) * 100) }}],

        ]);

        var options = {
            width: 400,
            height: 200,
            redFrom: 0,
            redTo: 35,
            yellowFrom: 35,
            yellowTo: 70,
            minorTicks: 5
        };
        var chart = new google.visualization.Gauge(document.getElementById('chart_div_{{$key}}'));
        chart.draw(data, options);
        }
        
</script>
@endforeach
@endsection