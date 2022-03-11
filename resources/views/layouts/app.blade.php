@inject('request', 'Illuminate\Http\Request')

@if(($request->segment(1) == 'pos' && ($request->segment(2) == 'create' || $request->segment(3) == 'edit') || ($request->segment(1) == 'purchase-pos') && ($request->segment(2) == 'create' || $request->segment(3) == 'edit')))
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
        @include('layouts.partials.header')
        @include('layouts.partials.sidebar')
        @else
        <!--@include('layouts.partials.header-pos')-->
        @endif
        <!-- Content Wrapper. Contains page content -->
        <div class="@if(!$pos_layout) content-wrapper @endif">
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
            <input type="hidden" id="__precision" value="@if(!empty($business_details->currency_precision)){{$business_details->currency_precision}}@else{{config('constants.currency_precision', 2)}}@endif">
            <input type="hidden" id="__quantity_precision" value="@if(!empty($business_details->quantity_precision)){{$business_details->quantity_precision}}@else{{config('constants.quantity_precision', 2)}}@endif">
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
        @if (!empty($register_success))
            @if($register_success['success'] == 1)
                <div class="modal" tabindex="-1" role="dialog" id="register_success_modal">
                    <div class="modal-dialog" role="document" style="width: 55%;">
                        <div class="modal-content">
                            <div class="modal-body text-center">
                                <i class="fa fa-check fa-lg" style="font-size: 50px; margin-top: 20px; border: 1px solid #4BB543; color: #4BB543; padding:15px 10px 15px 10px; border-radius: 50%;"></i>
                                <h2>{!!$register_success['title']!!}</h2>
                                <div class="clearfix"></div>
                                <div class="col-md-12">
                                    {!!$register_success['msg']!!}
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        @endif
    </div>
    @include('layouts.partials.javascripts')
    <script>
        $(document).ready(function(){
            @if (!empty($register_success))
                @if($register_success['success'] == 1)
                    $('#register_success_modal').modal('show');
                @endif
            @endif
        });
    </script>
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
    <div class="modal fade view_modal" role="dialog" aria-labelledby="gridSystemModalLabel"></div>
    <div class="stock_tranfer_notification_model">
    </div>
    <script>
        $('.payment_modal').on('hidden.bs.modal', function (e) {
        if ( $.fn.DataTable.isDataTable('#view_payment_table') ) {
            $('#view_payment_table').DataTable().destroy();
        }
        view_payment_table.destroy();
            $('#payment_filter_date_range').data('daterangepicker').remove()
        })
    </script>
    @php
        $reminders = \Modules\TasksManagement\Entities\Reminder::where('business_id',
        request()->session()->get('business.id'))->where('cancel', '0')->where('snooze', '0')->get();
        $snooze_reminder = \Modules\TasksManagement\Entities\Reminder::where('business_id',
        request()->session()->get('business.id'))->where('cancel', '0')->where('snooze', '1')->where('snoozed_at', '<=', date('Y-m-d H:i:s') )->get();
    @endphp

    @foreach ($reminders as $key => $value) {
        @if(($value->options == 'in_dashboard' || $value->options == 'when_login') && request()->path() == 'home')
            @include('layouts.partials.reminder_popup', ['value' => $value])
        @elseif($value->options == 'in_other_page')
            @if(in_array( request()->path(), $value->other_pages))
                @include('layouts.partials.reminder_popup', ['value' => $value])
            @endif
        @endif
        @if(!empty($value->crm_reminder_id))
            @include('layouts.partials.open_reminder', ['value' => $value])
            @include('layouts.partials.detail_reminder', ['value' => $value])
        @endif
    @endforeach

    @foreach ($snooze_reminder as $key => $value) {
        @if(($value->options == 'in_dashboard' || $value->options == 'when_login') && request()->path() == 'home')
            @include('layouts.partials.reminder_popup', ['value' => $value])
        @elseif($value->options == 'in_other_page')
            @if(in_array( request()->path(), $value->other_pages))
                @include('layouts.partials.reminder_popup', ['value' => $value])
            @endif
        @endif
        @if(!empty($value->crm_reminder_id))
            @include('layouts.partials.open_reminder', ['value' => $value])
            @include('layouts.partials.detail_reminder', ['value' => $value])
        @endif
    @endforeach
    <script>
        $('body').addClass('sidebar-collapse');
        $('.snooze_date').datepicker('setDate', new Date());
        $('.snooze_time').datetimepicker({
            format: 'HH:mm'
        });
    </script>
    <script>
        $(document).ready(function(){
            // $('.main-sidebar').css('width', '50px !important');
        });
    </script>
</body>

</html>