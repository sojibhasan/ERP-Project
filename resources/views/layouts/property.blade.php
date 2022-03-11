@inject('request', 'Illuminate\Http\Request')

@if($request->segment(1) == 'pos' && ($request->segment(2) == 'create' || $request->segment(3) == 'edit'))
@php
$pos_layout = true;
@endphp
@else
@php
$pos_layout = false;
@endphp
@endif
@php
$settings = DB::table('site_settings')->where('id', 1)->select('*')->first();
@endphp

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

    <!-- google adsense -->
    <script data-ad-client="ca-pub-1123727429633739" async
        src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>

    <title>@yield('title') - {{ Session::get('business.name') }}</title>
    <link rel="shortcut icon" type="image/x-icon" href="{{url($settings->uploadFileFicon)}}" />
    @include('layouts.partials.css')
    <link href="https://fonts.googleapis.com/css?family=Raleway&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/3.7.2/animate.min.css">
    @yield('css')

    <style>
        .sidebar-mini.sidebar-collapse .content-wrapper,
        .sidebar-mini.sidebar-collapse .main-footer,
        .sidebar-mini.sidebar-collapse .right-side {
            margin-left: 0px !important;
        }
    </style>
  

</head>

<body
    class="@if($pos_layout) hold-transition @else hold-transition skin-@if(!empty(session('business.theme_color'))){{session('business.theme_color')}}@else{{'blue'}}@endif sidebar-mini @endif">
  
    <div class="wrapper">

        <!-- Content Wrapper. Contains page content -->
        <div class="@if(!$pos_layout) content-wrapper @endif" style="margin-left: 0px;">
            @php
            $business_id = session()->get('user.business_id');
            $business_details = App\Business::find($business_id);
            @endphp
            <!-- Add currency related field-->
            <input type="hidden" id="__code" value="{{session('currency')['code']}}">
            <input type="hidden" id="__symbol" value="{{session('currency')['symbol']}}">
            <input type="hidden" id="__thousand" value="{{session('currency')['thousand_separator']}}">
            <input type="hidden" id="__decimal" value="{{session('currency')['decimal_separator']}}">
            <input type="hidden" id="__symbol_placement" value="{{session('business.currency_symbol_placement')}}">
            <input type="hidden" id="__precision" value="{{$business_details->currency_precision}}">
            <input type="hidden" id="__quantity_precision" value="{{$business_details->quantity_precision}}">
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
    <div class="stock_tranfer_notification_model">
    </div>
  
</body>

</html>