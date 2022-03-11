@extends('layouts.'.$layout)
@section('title', __('petro::lang.closing_meter'))
<style>
    .side-label {
        font-size: 21px;
        font-weight: bold;
        padding-top: 5px;
    }

    #key_pad input {
        border: none
    }

    #key_pad button {
        height: 80px;
        width: 80px;
        font-size: 25px;
        margin: 2px 1px;
        border: none !important;
    }

    :focus {
        outline: 0 !important
    }
</style>
@section('content')
<div class="container">
    <div class="col-md-12">
        <br>
        <br>
        <br>
        {!! Form::open(['url' =>
        action('\Modules\Petro\Http\Controllers\CurrentMeterController@store', ['pump_id' => $pump->id]), 'method' =>
        'post',
        'id' =>
        'closing_meter_form' ]) !!}
        <div class="row">
            <div class="col-md-6">
                <div class="row">
                    <div class="col-md-5">
                        {!! Form::label('pump_no', __('petro::lang.pump_no') .':', ['class' => 'side-label']) !!}
                    </div>
                    <div class="col-md-7">
                        {!! Form::text('pump_no', $pump->pump_no, ['class' => 'form-control input-lg', 'readonly']) !!}
                    </div>
                </div>
                <br>
                <div class="row">
                    <div class="col-md-5">
                        {!! Form::label('sale_price', __('petro::lang.sale_price') .':', ['class' => 'side-label']) !!}
                    </div>
                    <div class="col-md-7">
                        {!! Form::text('sale_price', $pump->default_sell_price, ['class' => 'form-control input-lg',
                        'readonly']) !!}
                    </div>
                </div>
                <br>
                <div class="row">
                    <div class="col-md-5">
                        {!! Form::label('starting_meter', __('petro::lang.starting_meter') .':', ['class' =>
                        'side-label']) !!}
                    </div>
                    <div class="col-md-7">
                        {!! Form::text('starting_meter', $pump->last_meter_reading, ['class' => 'form-control input-lg',
                        'readonly', 'required']) !!}
                    </div>
                </div>
                <br>
                <div class="row">
                    <div class="col-md-5">
                        {!! Form::label('last_time_meter', __('petro::lang.last_time_entered_meter') .':', ['class' =>
                        'side-label']) !!}
                    </div>
                    <div class="col-md-7">
                        {!! Form::text('last_time_meter', !empty($last_time_meter) ? $last_time_meter->current_meter :
                        0.00, ['class' => 'form-control input-lg',
                        ]) !!}
                    </div>
                </div>
                <br>
                <div class="row">
                    <div class="col-md-5">
                        {!! Form::label('current_meter', __('petro::lang.current_meter') .':', ['class' =>
                        'side-label']) !!}
                    </div>
                    <div class="col-md-7">
                        <div class="input-group">
                            {!! Form::text('current_meter', null, ['class' => 'form-control input-lg', 'required'
                            ]) !!}
                            <div class="input-group-addon calculate_total"
                                style="background: #00a65a; color: #fff; cursor: pointer">
                                ⏎
                            </div>
                        </div>
                    </div>
                </div>
                <br>
                <div class="row">
                    <div class="col-md-5">
                        {!! Form::label('amount', __('petro::lang.total_amount') .':', ['class' => 'side-label
                        text-red']) !!}
                    </div>
                    <div class="col-md-7">
                        {!! Form::text('amount', 0.00, ['class' => 'form-control input-lg', 'readonly' , 'required'])
                        !!}
                    </div>
                    <input type="hidden" name="sold_ltr" id="sold_ltr" value="0">
                    <input type="hidden" name="amount_hidden" id="amount_hidden" value="0">
                </div>
            </div>
            <div class="col-md-3">
                <div class="row">
                    <div id="key_pad" tabindex="1">
                        <div class="row text-center" id="calc">
                            <div class="calcBG col-md-12 text-center">
                                <div class="row">
                                    <button id="7" type="button" class="btn btn-primary btn-sm"
                                        onclick="enterVal(this.id)">7</button>
                                    <button id="8" type="button" class="btn btn-primary btn-sm"
                                        onclick="enterVal(this.id)">8</button>
                                    <button id="9" type="button" class="btn btn-primary btn-sm"
                                        onclick="enterVal(this.id)">9</button>

                                </div>
                                <div class="row">
                                    <button id="4" type="button" class="btn btn-primary btn-sm"
                                        onclick="enterVal(this.id)">4</button>
                                    <button id="5" type="button" class="btn btn-primary btn-sm"
                                        onclick="enterVal(this.id)">5</button>
                                    <button id="6" type="button" class="btn btn-primary btn-sm"
                                        onclick="enterVal(this.id)">6</button>
                                </div>
                                <div class="row">
                                    <button id="1" type="button" class="btn btn-primary btn-sm"
                                        onclick="enterVal(this.id)">1</button>
                                    <button id="2" type="button" class="btn btn-primary btn-sm"
                                        onclick="enterVal(this.id)">2</button>
                                    <button id="3" type="button" class="btn btn-primary btn-sm"
                                        onclick="enterVal(this.id)">3</button>
                                </div>
                                <div class="row">
                                    <button id="backspace" type="button" class="btn btn-danger"
                                        onclick="enterVal(this.id)">⌫</button>
                                    <button id="0" type="button" class="btn btn-primary btn-sm"
                                        onclick="enterVal(this.id)">0</button>
                                    <button id="precision" type="button" class="btn btn-success"
                                        onclick="enterVal(this.id)">.</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <a href="{{action('\Modules\Petro\Http\Controllers\PumpOperatorController@dashboard')}}"
                    class="btn btn-flat btn-block btn-lg"
                    style="color: #fff; background-color:#810040;">@lang('petro::lang.dashboard')</a>
                <br><br>
                <button type="submit" class="btn btn-flat btn-block btn-lg"
                    style="color: #fff; background-color:#2874A6;">@lang('petro::lang.save')</button><br><br>

                <a href="{{action('\Modules\Petro\Http\Controllers\PumpOperatorController@dashboard')}}?tab=closing_meter"
                    class="btn btn-flat btn-block btn-lg"
                    style="color: #fff; background-color:#CC0000;">@lang('petro::lang.cancel')</a><br><br>
                <a href="{{action('Auth\PumpOperatorLoginController@logout')}}"
                    class="btn btn-flat btn-block btn-lg pull-right"
                    style=" background-color: orange; color: #fff;">@lang('petro::lang.logout')</a>
            </div>
        </div>
        {!! Form::close() !!}
    </div>
</div>

@endsection


@section('javascript')
<script type="text/javascript">
    $('#closing_meter_form').validate();

    function enterVal(val) {
        $('#closing_meter').focus();
        if(val === 'enter'){
            $('#closing_meter').next('.form-control')
            return;
        }
        if(val === 'precision'){
            str = $('#closing_meter').val();
            str = str + '.';
            $('#closing_meter').val(str);
            return;
        }
        if(val === 'backspace'){
            str = $('#closing_meter').val();
            str = str.substring(0, str.length - 1);
            $('#closing_meter').val(str);
            return;
        }
        let closing_meter = $('#closing_meter').val() + val;
        closing_meter = closing_meter.replace(',', '');
        $('#closing_meter').val(closing_meter);
    };
   
$('.calculate_total').click(function() {
    let current_meter = parseFloat($('#current_meter').val().replace(',', ''));
    let last_time_meter = parseFloat($('#last_time_meter').val().replace(',', ''));
    let starting_meter = parseFloat($('#starting_meter').val());
    if(current_meter === '' || current_meter === undefined || current_meter === NaN){
        toastr.error('Closing meter value is required');
        $('#current_meter').val('0')
        return false;
    }
    if(last_time_meter === 0 || last_time_meter === undefined){
        if(current_meter < starting_meter){
            toastr.error('Closing meter value should be greater then Starting Meter');
            return false;
        }
    }else{
        if(current_meter < last_time_meter){
            toastr.error('Closing meter value should be greater then Last Time Meter');
            return false;
        }
    }
   
    calculateTotal();
});

function calculateTotal(){
    let sale_price = parseFloat($('#sale_price').val().replace(',', ''));
    let starting_meter = parseFloat($('#starting_meter').val().replace(',', ''));
    let current_meter = parseFloat($('#current_meter').val().replace(',', ''));
    let last_time_meter = parseFloat($('#last_time_meter').val().replace(',', ''));
    let sold_ltr = 0;
    if(last_time_meter === 0 || last_time_meter === undefined){
        sold_ltr = current_meter - starting_meter;
    }else{
        sold_ltr = current_meter - last_time_meter;
    }
 

    let total = sale_price * (sold_ltr);
    __write_number($('#amount'), total);
    $('#sold_ltr').val(sold_ltr);
    $('#amount_hidden').val(total);
}

   
</script>
@endsection