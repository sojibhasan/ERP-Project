<!DOCTYPE html>
<html>
@php
$settings = DB::table('site_settings')->where('id', 1)->select('*')->first();
$show_message = json_decode($settings->show_messages);

if(!empty($show_message->lp_title)){
if($show_message->lp_title == 1) {
$login_page_title = $settings->login_page_title;
}else{
$login_page_title = '';
}
}else{
$login_page_title = '';
}

if(!empty($show_message->lp_text)){
if($show_message->lp_text == 1) {
$login_page_footer = $settings->login_page_footer;
}else{
$login_page_footer = '';
}
}else{
$login_page_footer = '';
}


if(!empty($show_message->lp_system_expired)){
if($show_message->lp_system_expired == 1) {
$system_expired_message = $settings->system_expired_message;
}else{
$system_expired_message = '';
}
}else{
$system_expired_message = '';
}
$login_background_color = $settings->login_background_color;

if(!empty($business->background_showing_type) && !empty($business->background_showing_type)){
    $background_style = 'background-image: url('.$business->background_image.'); background-repeat: no-repeat;' ;
}else{
    if(!empty($settings->uploadFileLBackground) && ($bg_showing_type == 1 || $bg_showing_type == 3)){
        $background_style = 'background-image: url('.$settings->uploadFileLBackground.'); background-repeat: no-repeat;' ;
    }
    else{
        $background_style = 'background:'.$login_background_color.';';
    }
}
@endphp

<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta content="width=device-width, initial-scale=1" name="viewport" />
      <!-- google adsense -->
      <script data-ad-client="ca-pub-1123727429633739" async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
    <title>{{$login_page_title}}</title>
    <link href="{{asset('css/login.css')}}" rel="stylesheet" type="text/css" />

    <link rel="shortcut icon" type="image/x-icon" href="{{url($settings->uploadFileFicon)}}" />

    <link rel="stylesheet" href="{{ asset('plugins/jquery.steps/jquery.steps.css?v=' . $asset_v) }}">
    <!-- iCheck -->
    <link rel="stylesheet" href="{{ asset('AdminLTE/plugins/iCheck/square/blue.css?v='.$asset_v) }}">

    @include('layouts.partials.css')

    <style rel="stylesheet">
        body {
            background-image: url('assets/img/Loginlogo/');
        }

        .site-wrapper::before {
            background: transparent
        }

        .site-wrapper {
            background: transparent
        }

        .login-form {
            background: rgba(255, 255, 255, 0.8);
            padding: 20px;
            border-top: 3px solid#3e4043
        }

        .innter-form {
            padding-top: 20px
        }

        .final-login li {
            width: 50%
        }

        .nav-tabs {
            border-bottom: none !important
        }

        .nav-tabs>li {
            color: #222 !important
        }

        .nav-tabs>li.active>a,
        .nav-tabs>li.active>a:hover,
        .nav-tabs>li.active>a:focus {
            color: #fff;
            background-color: #d14d42;
            border: none !important;
            border-bottom-color: transparent;
            border-radius: none !important
        }

        .nav-tabs>li>a {
            margin-right: 2px;
            line-height: 1.428571429;
            border: none !important;
            border-radius: none !important;
            text-transform: uppercase;
            font-size: 16px;
            background: rgba(255, 255, 255, .1)
        }

        .social-login {
            text-align: center;
            font-size: 12px
        }

        .social-login p {
            margin: 15px 0
        }

        .social-login ul {
            margin: 0;
            padding: 0;
            list-style-type: none
        }

        .social-login ul li {
            width: 33%;
            float: left;
            clear: fix
        }

        .social-login ul li a {
            font-size: 13px;
            color: #fff;
            text-decoration: none;
            padding: 10px 0;
            display: block
        }

        .social-login ul li:nth-child(1) a {
            background: #3b5998
        }

        .social-login ul li:nth-child(2) a {
            background: #e74c3d
        }

        .social-login ul li:nth-child(3) a {
            background: #3698d9
        }

        .sa-innate-form input[type=text],
        input[type=password],
        input[type=file],
        textarea,
        select,
        email {
            font-size: 13px;
            padding: 10px;
            border: 1px solid#ccc;
            outline: none;
            width: 100%;
            margin: 8px 0
        }

        .sa-innate-form input[type=submit] {
            border: 1px solid#e64b3b;
            background: #e64b3b;
            color: #fff;
            padding: 10px 25px;
            font-size: 14px;
            margin-top: 5px
        }

        .sa-innate-form input[type=submit]:hover {
            border: 1px solid#db3b2b;
            background: #db3b2b;
            color: #fff
        }

        .sa-innate-form button {
            border: 1px solid#e64b3b;
            background: #e64b3b;
            color: #fff;
            padding: 10px 25px;
            font-size: 14px;
            margin-top: 5px
        }

        .sa-innate-form button:hover {
            border: 1px solid#db3b2b;
            background: #db3b2b;
            color: #fff
        }

        .sa-innate-form p {
            font-size: 13px;
            padding-top: 10px
        }

        .container2 {
            position: relative;
            text-align: center;
            color: white;
        }

        #captcha_image {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
        }

        #captcha_image2 {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
        }

        .sign-in-outer {
            background-image: none;
            background-color: #3b5998;
        }


        @media only screen and (max-width: 600px) {
            .intro-cont {
                display: none;
            }

            .nav-tabs li {
                width: 100%;
                margin-left: 0% !important;
            }

            .cover-container {
                padding-top: 0px;
            }

            .inner {
                padding-top: 0px;
            }

            .inner .row {
                padding-top: 0px;
                /* margin: 0px; */
            }

            .register-true .row {
                margin-left: 0% !important;
            }

            .register-true .col-xs-offset-2 {
                margin-bottom: 10px !important;
            }

            .sign-in-outer {
                margin-top: 10px;
            }
        }

        @media (min-width: 992px) {

            /* .register-true .row{
                margin-left: 0% !important;
            } */
            .register-true .col-md-3 {
                margin: 10px !important;
            }
        }

        .swal-overlay {
            background-color: rgba(43, 165, 137, 0.45);
        }

        .form-group a {
            text-decoration: none;
            color: #ffffff;
        }

        .forget-password-link {
            color: #ffffff;
        }

        .forget-password-link:hover {
            color: #ffffff;
        }

        #business_register_form {
            padding-bottom: 30px;

        }

        html {
            overflow: scroll;
            overflow-x: hidden;
        }

        .label_register{
            color: #222 !important;
        }

        ::-webkit-scrollbar {
            width: 0px;


            ::-webkit-scrollbar-thumb {
                background: #FF0000;
            }

            .register-true {
                text-align: center;
                margin: 0px;
                padding: 0px;
            }

            .register-true a {
                padding-top: 10px;
            }

            .modal-dialog {
                width: 70% !important;
                margin-left: 15%;
            }

            @media only screen and (max-width: 600px) and (min-width:300px) {
                .modal-dialog {
                    width: 90% !important;
                    margin-left: 5% !important;
                }
            }
    </style>
</head>

<body style="{{$background_style}}">
    @if (session('status'))
    <input type="hidden" id="status_span" data-status="{{ session('status.success') }}"
        data-msg="{{ session('status.msg') }}">
    @endif
    @yield('content')
    @if(!empty($show_message->lp_text))
    @if($show_message->lp_text == 1)
    <div class="login_footer">

        <div class="copy"></div>

        <div class="copy">{{$login_page_footer}}
        </div>

    </div>
    @endif
    @endif

    @include('layouts.partials.javascripts')
    <!-- Scripts -->
    <script src="{{ asset('js/login.js?v=' . $asset_v) }}"></script>

    @yield('javascript')

</body>

</html>