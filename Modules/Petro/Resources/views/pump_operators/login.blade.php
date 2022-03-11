@extends('layouts.auth-login')
@inject('request', 'Illuminate\Http\Request')

@php
$bg_showing_type = $settings->background_showing_type;
@endphp

@section('content')

<style>
    #key_pad input {
        border: none
    }

    #key_pad button {
        height: 80px;
        width: 80px;
        font-size: 35px;
        margin: 2px 1px;
        border: none !important;
    }

    :focus {
        outline: 0 !important
    }
</style>

<div class="container">
    <div class="row text-center">
        <h1>@lang('lang_v1.welcome')</h1>
    </div>
    <div class="clearfix"></div>
    <div class="row">
        <div class="col-md-6 col-lg-6 ">
            <div class="row text-center" style="text-align:center;">
                @if(!empty($settings->uploadFileLLogo) && file_exists(public_path(). str_replace('public', '',
                $settings->uploadFileLLogo)))
                <img src="{{url($settings->uploadFileLLogo)}}" class="img-rounded" alt="Logo" style="text-center;
                                max-width: 100%;
                                width: 360px;
                                height: 180px;
                                ">

                @endif
                <h1 style="font-size: 42.5px; color: #838383; font-family: monospace">
                    {{!empty($business) ? $business->name : 'SYZYGY' }}</h1>
                <h3 class="text-red" style="font-size: 28px; font-weight: bold; font-family: inherit; margin-top: 0px;">
                    @lang('lang_v1.pump_operator_dashboard')</h3>
                <h3 style="font-size: 25px; color: #838383; font-family: arial; margin: 15px 10px;">
                    @lang('lang_v1.locked')</h3>
            </div>

            {!! Form::open(['url' => action('Auth\PumpOperatorLoginController@login'),
            'method' =>
            'post',
            'id' =>
            'login_form' ]) !!}
            <div class="row text-center">
                <div class="input-group" style="width: 70%; float: left;">
                    <span class="input-group-addon">
                        <i class="fa fa-lock"></i>
                    </span>
                    {!! Form::password('passcode', ['class' => 'form-control', 'id' => 'passcode',
                    'placeholder' => 'passcode', 'style' => 'margin: 0']); !!}
                </div>
                <img src="{{asset('img/loading.gif')}}" alt="loading" class="loading_gif" style="display:none;">
                <button type="submit" class="btn btn-success" id="check_password_btn" style="border-radius: 0px; text-transform: none;">@lang('petro::lang.click_to_enter')</button>
            </div>
            {!! Form::close() !!}

        </div>
        <div class="col-md-6 col-lg-6 text-center">
            <div class="row">
                <h1 style="font-weight: bold; margin-bottom: 0px; margin-top: -25px;">@lang('lang_v1.today'):
                    {{Carbon::now()->format('m-d-Y')}}
                </h1>
                <h1 style="font-weight: bold; margin-top: 0px">@lang('lang_v1.time'): {{Carbon::now()->format('H:i')}}
                </h1>
            </div>
            <div class="row">
                <div id="key_pad" tabindex="1">
                    <div class="row text-center" id="calc">
                        <div class="calcBG col-md-12 text-center">
                            <div class="row">
                                <button id="7" type="button" class="btn btn-sm" onclick="enterVal(this.id)">7</button>
                                <button id="8" type="button" class="btn btn-sm" onclick="enterVal(this.id)">8</button>
                                <button id="9" type="button" class="btn btn-sm" onclick="enterVal(this.id)">9</button>

                            </div>
                            <div class="row">
                                <button id="4" type="button" class="btn btn-sm" onclick="enterVal(this.id)">4</button>
                                <button id="5" type="button" class="btn btn-sm" onclick="enterVal(this.id)">5</button>
                                <button id="6" type="button" class="btn btn-sm" onclick="enterVal(this.id)">6</button>
                            </div>
                            <div class="row">
                                <button id="1" type="button" class="btn btn-sm" onclick="enterVal(this.id)">1</button>
                                <button id="2" type="button" class="btn btn-sm" onclick="enterVal(this.id)">2</button>
                                <button id="3" type="button" class="btn btn-sm" onclick="enterVal(this.id)">3</button>
                            </div>
                            <div class="row">
                                <button id="backspace" type="button" class="btn btn-danger"
                                    onclick="enterVal(this.id)">⌫</button>
                                <button id="0" type="button" class="btn" onclick="enterVal(this.id)">0</button>
                                <button id="enter" type="button" class="btn btn-success"
                                    onclick="enterVal(this.id)">↵</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

@stop
@section('javascript')
<script>
    function enterVal(val) {
        if(val === 'enter'){
            $('#login_form').submit();
            return;
        }
        if(val === 'backspace'){
            str = $('#passcode').val();
            str = str.substring(0, str.length - 1);
            $('#passcode').val(str);
            return;
        }
        let passcode = $('#passcode').val() + val;
        $('#passcode').val(passcode);
    };

</script>

@endsection