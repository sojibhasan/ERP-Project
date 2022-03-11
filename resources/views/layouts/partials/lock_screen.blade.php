@php
$settings = DB::table('site_settings')->where('id', 1)->select('*')->first();
$locked_screen = DB::table('users')->where('id', auth()->user()->id)->select('lock_screen')->first();
@endphp
<div id="lock_screen_div" class="animated fadeInDown" style="@if(empty($locked_screen->lock_screen)) display:none; @endif ">
<div class="col-md-12 lock-content">
    <div class="row">
        <div class="lock_logo">
            @if(!empty($settings->uploadFileLLogo))
            <img src="{{url($settings->uploadFileLLogo)}}" class="img-rounded">
            @else
            {{ config('app.name', 'ultimatePOS') }}
            @endif
        </div>
    </div>
    <div class="row">
        <div class="col-md-4 col-md-offset-4 text-center">
            <h1>{{auth()->user()->username}}</h1>
            <h3>{{auth()->user()->email}}</h3>
            <p class="locked_p">Locked</p>
        </div>
    </div>
    <div class="row">
        <div class="col-md-4 col-md-offset-4 text-center">
            <div class="row">
                <p class="hide_p">&nbsp;</p>
                <div class="input-group" style="width: 90%; float: left;">
                    <span class="input-group-addon">
                        <i class="fa fa-lock"></i>
                    </span>
                    {!! Form::password('lock_password', ['class' => 'form-control', 'id' => 'lock_password',
                    'placeholder' => 'Password']); !!}
                </div>
                <img src="{{asset('img/loading.gif')}}" alt="loading" class="loading_gif" style="display:none;">
                <button class="btn btn-danger" id="check_password_btn" style="border-radius: 0px"><i
                        class="fa fa-arrow-right"></i></button>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-4 col-md-offset-4 text-center">
            <a href="{{route('logout')}}" class="not_super_admin">Not <b>@lang('lang_v1.super_admin')</b></a>
        </div>
    </div>
</div>

</div>