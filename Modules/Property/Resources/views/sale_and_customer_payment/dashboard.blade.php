@extends('layouts.'.$layout)
@section('title', __('home.home'))

@section('content')
<style>
    .property-div a{
        white-space: initial;
    }
</style>
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>{{ __('home.welcome_message', ['name' =>Auth::user()->first_name .' '. Auth::user()->last_name]) }}
    </h1>
    <!-- Main content -->
    <section class="content no-print">
        <div class="row">
            <div class="col-md-5">
                @can('property.project_dashboard.customer_payments')
                <a href="{{action('\Modules\Property\Http\Controllers\CustomerPaymentController@create')}}"
                    type="button" style="font-size: 19px;" class="btn btn-danger btn-flat pull-right m-8  btn-sm mt-10">
                    <strong>@lang('lang_v1.customer_payment')</strong></a>
                @endcan
                <a href="{{action('Auth\PropertyUserLoginController@logout', ['main_system' => true])}}" type="button"
                    style="font-size: 19px; background: #9900cc; color: #fff;"
                    class="btn btn-flat pull-right m-8  btn-sm mt-10">
                    <strong>@lang('lang_v1.main_system')</strong></a>
            </div>
            <div class="col-md-7 col-xs-7">
                <div class="btn-group pull-right" data-toggle="buttons">
                    <label class="btn btn-info active">
                        <input type="radio" name="date-filter" data-start="{{ date('Y-m-d') }}"
                            data-end="{{ date('Y-m-d') }}" checked> {{ __('home.today') }}
                    </label>
                    <label class="btn btn-info">
                        <input type="radio" name="date-filter" data-start="{{ $date_filters['this_week']['start']}}"
                            data-end="{{ $date_filters['this_week']['end']}}"> {{ __('home.this_week') }}
                    </label>
                    <label class="btn btn-info">
                        <input type="radio" name="date-filter" data-start="{{ $date_filters['this_month']['start']}}"
                            data-end="{{ $date_filters['this_month']['end']}}"> {{ __('home.this_month') }}
                    </label>
                    <label class="btn btn-info">
                        <input type="radio" name="date-filter" data-start="{{ $date_filters['this_fy']['start']}}"
                            data-end="{{ $date_filters['this_fy']['end']}}"> {{ __('home.this_fy') }}
                    </label>
                </div>
            </div>
        </div>
        <br>
        @if(empty(Auth::user()->pump_operator_id))
        <div class="row">
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('location_id', __('lang_v1.business_locations'), []) !!}
                    {!! Form::select('location_id', $business_locations, null, ['class' => 'form-control select2', 'style' => 'width: 100%', 'placeholder' => __('lang_v1.all')]) !!}
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('project_id', __('property::lang.project'), []) !!}
                    {!! Form::select('project_id', $projects, null, ['class' => 'form-control select2', 'style' => 'width: 100%', 'placeholder' => __('lang_v1.all')]) !!}
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('block_id', __('property::lang.block_no') . ':*') !!}
                    {!! Form::select('block_id',
                    [], null, ['class' => 'form-control select2', 'id' => 'block_id', 'placeholder' =>
                    __('lang_v1.all')]); !!}
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('officer_id', __('property::lang.sale_officers') . ':*') !!}
                    {!! Form::select('officer_id',
                    $sale_officers, null, ['class' => 'form-control select2', 'id' => 'sale_officers', 'placeholder' =>
                    __('lang_v1.all')]); !!}
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('date_range', __('report.date_range') . ':') !!}
                    {!! Form::text('date_range', null, ['placeholder' => __('lang_v1.select_a_date_range'), 'class' => 'form-control', 'readonly']); !!}
                </div>
            </div>
        </div>
        @endif
        <div class="row">
            <div class="col-md-3 col-sm-6 col-xs-12">
                <div class="info-box">
                    <span class="info-box-icon bg-aqua"><i class="ion ion-cash"></i></span>

                    <div class="info-box-content">
                        <span class="info-box-text">{{ __('property::lang.total_plots_sold') }}</span>
                        <span class="info-box-number total_plots_sold"><i
                                class="fa fa-refresh fa-spin fa-fw margin-bottom"></i></span>
                    </div>
                    <!-- /.info-box-content -->
                </div>
                <!-- /.info-box -->
            </div>
            <!-- /.col -->
            <div class="col-md-3 col-sm-6 col-xs-12">
                <div class="info-box">
                    <span class="info-box-icon bg-aqua"><i class="ion ion-ios-cart-outline"></i></span>

                    <div class="info-box-content">
                        <span class="info-box-text">{{ __('property::lang.amount_of_sold_blocks') }}</span>
                        <span class="info-box-number amount_of_sold_blocks"><i
                                class="fa fa-refresh fa-spin fa-fw margin-bottom"></i></span>
                    </div>
                    <!-- /.info-box-content -->
                </div>
                <!-- /.info-box -->
            </div>
            <!-- /.col -->
            <div class="col-md-3 col-sm-6 col-xs-12">
                <div class="info-box">
                    <span class="info-box-icon bg-yellow">
                        <i class="fa fa-dollar"></i>
                        <i class="fa fa-exclamation"></i>
                    </span>

                    <div class="info-box-content">
                        <span class="info-box-text">{{ __('property::lang.total_commission') }}</span>
                        <span class="info-box-number total_commission"><i
                                class="fa fa-refresh fa-spin fa-fw margin-bottom"></i></span>
                    </div>
                    <!-- /.info-box-content -->
                </div>
                <!-- /.info-box -->
            </div>
            <!-- /.col -->
        </div>
        <br>
       
     
        @if($properties->count() > 0)
        @can('property.project_dashboard.sell_land_blocks')
        @component('components.widget', ['class' => 'box-primary'])
        
        <div class="row  text-center">
            <h2 class="title" style="text-align:left; padding-left:10px;font-size: 20px;">{{ __('property::lang.properties') }}</h2>
            @foreach ($properties as $property)
            <div class="col-md-2 text-center bg-primary property-div" style="margin: 10px 10px 10px 10px; color: #fff;">
                <a type="button" class="btn  btn-primary btn-flat"
                    style="height: auto; width:100%; background: transparent; border: 0px;"
                    href="{{action('\Modules\Property\Http\Controllers\SellLandBlockController@create', ['property_id' => $property->id])}}">
                    <h2 style="margin-top: 15px;  margin-bottom: 15px"> {{$property->name}}</h2>
                </a>
            </div>

            @endforeach
        </div>
        @endcomponent
        @endcan

        @if($sold_properties->count() > 0)
        @component('components.widget', ['class' => 'box-primary'])
        <div class="row  text-center">
            @foreach ($sold_properties as $sold_property)
            <div class="col-md-2 text-center bg-yellow"
                style="margin: 10px 10px 10px 10px; color: #fff !important; height: 120px;">
                <h2 style="margin-top: 45px;"> {{$sold_property->name}}</h2>
            </div>
            @endforeach
        </div>
        @endcomponent
        @endif
        @endif
    </section>
    <!-- /.content -->
    @stop
    @section('javascript')
    <script src="https://code.highcharts.com/highcharts.js"></script>
    <script>
        var body = document.getElementsByTagName("body")[0];
        body.className += " sidebar-collapse";


        $('#date_range').daterangepicker(
            dateRangeSettings,
            function (start, end) {
                $('#date_range').val(start.format(moment_date_format) + ' ~ ' + end.format(moment_date_format));
                // update_statistics(start, end)
            }
        );
        $('#date_range').on('cancel.daterangepicker', function(ev, picker) {
            $('#date_range').val('');
        });


        
        $(document).ready(function () {
            var start = $('input[name="date-filter"]:checked').data('start');
             var end = $('input[name="date-filter"]:checked').data('end');
            update_statistics(start, end);
            $(document).on('change', 'input[name="date-filter"]', function() {
                var start = $('input[name="date-filter"]:checked').data('start');
                var end = $('input[name="date-filter"]:checked').data('end');
                update_statistics(start, end);
            });
        })

        function update_statistics(start, end) {
            @if(empty(Auth::user()->pump_operator_id))
            start = $('input#date_range')
                        .data('daterangepicker')
                        .startDate.format('YYYY-MM-DD');
            end = $('input#date_range')
                .data('daterangepicker')
                .endDate.format('YYYY-MM-DD');
            var data = { start: start, end: end, project_id: $('#project_id').val(), location_id: $('#location_id').val(), block_id: $('#block_id').val(), officer_id: $('#officer_id').val(), };
            @else
            var data = { start: start, end: end };
            @endif
            //get purchase details
            var loader = '<i class="fa fa-refresh fa-spin fa-fw margin-bottom"></i>';
            $('.total_plots_sold').html(loader);
            $('.amount_of_sold_blocks').html(loader);
            $.ajax({
                method: 'get',
                url: '/property/sale-and-customer-payment/get-totals',
                dataType: 'json',
                data: data,
                success: function(data) {
                    $('.total_plots_sold').html(__currency_trans_from_en(data.total_plots_sold, true));
                    $('.amount_of_sold_blocks').html(__currency_trans_from_en(data.amount_of_sold_blocks, true));

                },
            });
        }

        @if(empty(Auth::user()->pump_operator_id))
        $('#project_id, #location_id, #block_id, #officer_id, #date_range').change(function () {
            start = $('input#date_range')
                        .data('daterangepicker')
                        .startDate.format('YYYY-MM-DD');
            end = $('input#date_range')
                .data('daterangepicker')
                .endDate.format('YYYY-MM-DD');
            update_statistics(start, end);
        })
        @endif



        $('#project_id').change(function(){
            project_id = $(this).val();
      
            $.ajax({
                method: 'get',
                url: '/property/property-blocks/get-block-dropdown/' + project_id,
                data: {  },
                success: function(result) {
                    if(result.success){
                        $('#block_id').empty().append(result.data);
                    }
                },
            });
        })
    </script>

    @endsection