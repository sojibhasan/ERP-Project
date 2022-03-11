@inject('request', 'Illuminate\Http\Request')

@if($request->segment(2) == 'pos' && ($request->segment(3) == 'create'))
@php
$pos_layout = true;
@endphp
@else
@php
$pos_layout = false;
@endphp
@endif

<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}"
    dir="{{in_array(session()->get('user.language', config('app.locale')), config('constants.langs_rtl')) ? 'rtl' : 'ltr'}}">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <!-- Tell the browser to be responsive to screen width -->
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title') - {{ Session::get('business.name') }}</title>

    @include('layouts.partials.css')
    <link href="https://fonts.googleapis.com/css?family=Raleway&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/3.7.2/animate.min.css">
    @yield('css')

</head>

<body
    class="@if($pos_layout) hold-transition lockscreen @else hold-transition skin-@if(!empty(session('business.theme_color'))){{session('business.theme_color')}}@else{{'blue'}}@endif sidebar-mini @endif">
    @include('layouts.partials.lock_screen')
    <div class="wrapper">
        <script type="text/javascript">
            if(localStorage.getItem("upos_sidebar_collapse") == 'true'){
                    var body = document.getElementsByTagName("body")[0];
                    body.className += " sidebar-collapse";
                }
        </script>
        @if(!$pos_layout)
        @include('layouts.ecom_customer.header')
        @include('layouts.ecom_customer.sidebar')
        @else
        @include('layouts.ecom_customer.header-pos')
        @endif

        <!-- Content Wrapper. Contains page content -->
        <div class="@if(!$pos_layout) content-wrapper @endif">
            <!-- Add currency related field-->
            <input type="hidden" id="__thousand" value=",">
            <input type="hidden" id="__decimal" value=".">
            <input type="hidden" id="__symbol_placement" value="before">
            <input type="hidden" id="__precision" value="2">
            <input type="hidden" id="__quantity_precision" value="2">
            <!-- End of currency related field-->

            @if (session('status'))
            <input type="hidden" id="status_span" data-status="{{ session('status.success') }}"
                data-msg="{{ session('status.msg') }}">
            @endif
            @yield('content')
            @if(config('constants.iraqi_selling_price_adjustment'))
            <input type="hidden" id="iraqi_selling_price_adjustment">
            @endif

            <!-- This will be printed -->
            <section class="invoice print_section" id="receipt_section">
            </section>

        </div>
        @include('home.todays_profit_modal')
        <!-- /.content-wrapper -->

        @if(!$pos_layout)
        @include('layouts.partials.footer')
        @else
        @include('layouts.partials.footer_pos')
        @endif

        <audio id="success-audio">
            <source src="{{ asset('/audio/success.ogg?v=' . $asset_v) }}" type="audio/ogg">
            <source src="{{ asset('/audio/success.mp3?v=' . $asset_v) }}" type="audio/mpeg">
        </audio>
        <audio id="error-audio">
            <source src="{{ asset('/audio/error.ogg?v=' . $asset_v) }}" type="audio/ogg">
            <source src="{{ asset('/audio/error.mp3?v=' . $asset_v) }}" type="audio/mpeg">
        </audio>
        <audio id="warning-audio">
            <source src="{{ asset('/audio/warning.ogg?v=' . $asset_v) }}" type="audio/ogg">
            <source src="{{ asset('/audio/warning.mp3?v=' . $asset_v) }}" type="audio/mpeg">
        </audio>

    </div>

    @include('layouts.partials.javascripts')
    <script>
        
        $('.loading_gif').hide();
        $('#btnLock').click(function(){
            $('#lock_screen_div').show();
            $.ajax({
                method: 'post',
                url: '/lock_screen',
                data: {  },
                success: function(result) {
                   
                },
            });
            jQuery('html').css('overflow', 'hidden');
        });
        pelase_enter_password = "@lang('lang_v1.pelase_enter_password')";
        $('#check_password_btn').click(function(){
            let password = $('#lock_password').val();
            if(password == ''){
                $('.hide_p').text(pelase_enter_password);
            }else{
                $(this).hide();
                $('.loading_gif').show();
                $.ajax({
                    method: 'post',
                    url: '/check_user_password',
                    data: { password : password },
                    success: function(result) {
                        console.log(result.success);
                        if(result.success == 1){
                         jQuery('html').css('overflow', 'scroll');
                         $('#lock_screen_div').addClass('animated', 'bounceOutLeft');
                         $('#lock_screen_div').hide(); 
                         $('#lock_password').val('');
                        }else if(result.success == 2){
                            window.location.replace("{{route('login')}}");
                        }   
                        else{
                            $('.hide_p').text(result.msg);
                        }
                        $('#check_password_btn').show();
                        $('.loading_gif').hide();
                    },
                });
            }
        });

        $('#lock_password').keyup(function(){
            $('.hide_p').empty().append('&nbsp;');
        });
    </script>
    <div class="modal fade view_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel"></div>
</body>

</html>