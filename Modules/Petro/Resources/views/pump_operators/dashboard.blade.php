@extends('layouts.'.$layout)
@section('title', __('home.home'))
<style>
  .button-row p {
    font-size: 25px;
    white-space: initial;
  }

  .big-buttons {
    height: 200px;
    width: 250px;
    margin-left: 20px;
    margin-bottom: 20px !important;
    color: #fff;
    font-size: 35px !important;
  }

  .big-buttons:hover,
  .btn:hover {
    color: #fff !important;
  }

  .small-buttons {
    height: 121px !important;
    width: 30% !important;
    margin-left: 20px !important;
    margin-bottom: 20px !important;
    color: #fff !important;
    font-size: 35px !important;
  }
</style>

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
  <h1 style="float: left">{{ __('home.welcome_message', ['name' => \Auth::user()->first_name]) }}
  </h1>
  <h4 style="float: left; margin-top: 5px;">@lang('petro::lang.today'): {{@format_date(Carbon::now()->format('Y-m-d'))}}
  </h4>
  <h4 style="float: left; margin-top: 5px;"> @lang('petro::lang.time'): {{Carbon::now()->format('H:i:s')}}</h4>
</section>
<!-- Main content -->
@if(auth()->user()->can('pump_operator.dashboard'))
<!-- Main content -->
<section class="content no-print">
  <div class="clearfix"></div>
  <div class="row">
    <div class="col-md-12 text-center">
      <h2 style="font-weight: bold; color: brown; margin-top: 0px;">@lang('petro::lang.pump_operator_dashboard')</h2>
      {!!$general_message !!}
    </div>
  </div>
  <div class="col-md-12">
    <div class="col-md-8"></div>
    <div class="col-md-4">
    @can('pump_operator.main_system')
      @if(empty(session()->get('pump_operator_main_system')))
      <a href="{{action('Auth\PumpOperatorLoginController@logout', ['main_system' => true])}}"
        class="btn btn-flat btn-lg pull-right"
        style=" background-color: brown; color: #fff; margin-left: 5px">@lang('petro::lang.main_system')</a>
      @endif
    @endcan
      <a href="{{action('Auth\PumpOperatorLoginController@logout')}}" class="btn btn-flat btn-lg pull-right"
        style=" background-color: orange; color: #fff; margin-left: 5px;">@lang('petro::lang.logout')</a>
    </div>
  </div>
  <div class="clearfix"></div>
  <br>


  @component('components.widget', ['class' => 'box-primary'])
  <div class="container">
    <div class="row">
      <div class="col-md-12 button-row text-center">
        <button class="btn big-buttons" value="pumps" style="background: #FF5733">@lang('petro::lang.pumps')</button>
        <button class="btn big-buttons" value="meters" style="background: #800080">@lang('petro::lang.meters')</button>
        <button class="btn big-buttons" value="unloading"
          style="background: #2874A6">@lang('petro::lang.unloading')</button>
      </div>
    </div>
    <div class="clearfix"></div>
    <br>
    <div class="row">
      <div class="col-md-12 button-row text-center">
        <div class="pump_buttons  list-button">
          <a id="receive_pump_btn" type="button" class="btn   btn-flat small-buttons"
            style="height: auto; width:100%; background: #FF5733; border: 0px;"
            href="{{action('\Modules\Petro\Http\Controllers\PumpOperatorActionsController@getReceivePump')}}">
            <p style="margin-top: 36px; margin-bottom: 36px"> @lang('petro::lang.receive_pump')</p>
          </a>

          <a id="payments_btn" class=" btn small-buttons"
            style="height: auto; width:100%; background: #800080; border: 0px; color: #fff; "
            href="{{action('\Modules\Petro\Http\Controllers\PumpOperatorPaymentController@create')}}">
            <p style="margin-top: 42px;margin-bottom: 42px;">@lang('petro::lang.payments')</p>
          </a>

          <a id="" class="btn small-buttons"
            style="height: auto; width:100%; background: #2874A6; border: 0px; color: #fff; "
            href="{{action('\Modules\Petro\Http\Controllers\PumperDayEntryController@index', ['only_pumper' => true])}}">
            <p style="margin-top: 42px;margin-bottom: 42px;">@lang('petro::lang.day_entries')</p>
          </a>

          <a type="button" class="btn btn-flat btn-modal small-buttons" data-container=".pump_operator_modal"
            style="height: auto; width:100%; background: #33691E; border: 0px;"
            data-href="{{action('\Modules\Petro\Http\Controllers\PumpOperatorActionsController@getClosingMeterModal')}}">
            <p style="margin-top: 36px; margin-bottom: 36px"> @lang('petro::lang.closing_meter')</p>
          </a>

          @if($can_close_shift)
          <a id="" class="btn small-buttons"
            style="height: auto; width:100%; background: #F9A825; border: 0px; color: #fff; "
            href="{{action('\Modules\Petro\Http\Controllers\ClosingShiftController@index', ['only_pumper' => true])}}">
            <p style="margin-top: 42px;margin-bottom: 42px;">@lang('petro::lang.close_shift')</p>
          </a>
          @else
          <a id="" class="btn small-buttons"
            style="height: auto; width:100%; background: #F9A825; border: 0px; color: #fff; ">
            <p style="margin-top: 42px;margin-bottom: 42px;">@lang('petro::lang.close_shift')</p>
          </a>
          @endif

          <a id="" class="btn small-buttons"
            style="height: auto; width:100%; background: rgb(105, 107, 105); border: 0px; color: #fff; "
            href="{{action('\Modules\Petro\Http\Controllers\PumpOperatorPaymentController@index', ['only_pumper' => true])}}">
            <p style="margin-top: 42px;margin-bottom: 42px;">@lang('petro::lang.payment_summary')</p>
          </a>
        </div>
        <div class="meter_buttons list-button">
          <a id="" class="btn small-buttons btn btn-flat btn-modal" data-container=".pump_operator_modal"
            style="height: auto; width:100%; background: rgb(235, 52, 58) !important; border: 0px;"
            data-href="{{action('\Modules\Petro\Http\Controllers\CurrentMeterController@getModal', ['only_pumper' => true])}}">
            <p style="margin-top: 36px; margin-bottom: 36px">@lang('petro::lang.enter_current_meter')</p>
          </a>
        </div>

        <div class="unload_buttons list-button">
          <a id="" class="btn btn-flat btn-modal small-buttons" data-container=".pump_operator_modal"
            style="height: auto; width:100%; background: rgb(109, 87, 219); border: 0px;"
            data-href="{{action('\Modules\Petro\Http\Controllers\UnloadStockController@create', ['only_pumper' => true])}}">
            <p style="margin-top: 36px; margin-bottom: 36px">@lang('petro::lang.unload_stock')</p>
          </a>

          <a id="" class="btn btn-flat small-buttons"
            style="height: auto; width:100%; background: rgb(240, 77, 5); border: 0px;"
            href="{{action('\Modules\Petro\Http\Controllers\UnloadStockController@getDetails', ['only_pumper' => true])}}">
            <p style="margin-top: 36px; margin-bottom: 36px">@lang('petro::lang.unload_stock_details')</p>
          </a>
        </div>
      </div>
    </div>
  </div>
  @endcomponent
</section>

<div class="modal fade pump_operator_modal" role="dialog" aria-labelledby="gridSystemModalLabel">
</div>
@endif


@endsection
@section('javascript')
<script>
  //dashboard
$('.list-button').hide();
$(document).ready(function() {
  $('.big-buttons').click(function () {
    $('.list-button').hide();
    if($(this).val() === 'pumps'){
      $('.pump_buttons').slideDown();
    }
    if($(this).val() === 'meters'){
      $('.meter_buttons').slideDown();
    }
    if($(this).val() === 'unloading'){
      $('.unload_buttons').slideDown();
    }
  })
  $('body').addClass('sidebar-collapse')
    var start = $('input[name="date-filter"]:checked').data('start');
    var end = $('input[name="date-filter"]:checked').data('end');
    update_statistics(start, end);
    $(document).on('change', 'input[name="date-filter"]', function() {
        var start = $('input[name="date-filter"]:checked').data('start');
        var end = $('input[name="date-filter"]:checked').data('end');
        update_statistics(start, end);
    });
});



function update_statistics(start, end) {
    var data = { start: start, end: end, pump_operator_id : {{auth()->user()->pump_operator_id}} };
    //get purchase details
    var loader = '<i class="fa fa-refresh fa-spin fa-fw margin-bottom"></i>';
    $('.total_liter_sold').html(loader);
    $('.total_income_earned').html(loader);
    $('.total_short').html(loader);
    $('.total_leave').html(loader);
    $.ajax({
        method: 'get',
        url: "{{action('\Modules\Petro\Http\Controllers\PumpOperatorController@getDashboardData')}}",
        dataType: 'json',
        data: data,
        success: function(data) {
            //purchase details
            $('.total_liter_sold').html(__currency_trans_from_en(data.total_liter_sold, true));
            $('.total_income_earned').html(__currency_trans_from_en(data.total_income_earned, true));

            //sell details
            $('.total_short').html(__currency_trans_from_en(data.total_short, true));
            $('.total_excess').html(__currency_trans_from_en(data.total_excess, true));
        },
    });
}
</script>
<script>
  $(document).ready(function(){
    @if(request()->tab == 'closing_meter')
    $('#closing_meter_btn').trigger('click');
    @endif
  })
</script>
@endsection