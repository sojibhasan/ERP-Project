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
    <div class="wrapper">
        <script type="text/javascript">
            if(localStorage.getItem("upos_sidebar_collapse") == 'true'){
                    var body = document.getElementsByTagName("body")[0];
                    body.className += " sidebar-collapse";
                }
        </script>
        @include('layouts.employee.header')
        @include('layouts.employee.sidebar')
      

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

    <div class="modal fade view_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel"></div>
</body>

</html>