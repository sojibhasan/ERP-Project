@php
$settings = DB::table('site_settings')->where('id', 1)->select('*')->first();
$login_background_color = $settings->login_background_color;
@endphp
<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <!-- Tell the browser to be responsive to screen width -->
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- google adsense -->
    <script data-ad-client="ca-pub-1123727429633739" async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>

    <title>@yield('title') - {{ config('app.name', 'POS') }}</title>

    @include('layouts.partials.css')

    <!-- Jquery Steps -->
    <link rel="stylesheet" href="{{ asset('plugins/jquery.steps/jquery.steps.css?v=' . $asset_v) }}">
    <!-- iCheck -->
    <link rel="stylesheet" href="{{ asset('AdminLTE/plugins/iCheck/square/blue.css?v='.$asset_v) }}">

    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
    <style>
        body {
            background-color: {
                    {
                    $login_background_color
                }
            }

            ;
        }

        h1 {
            color: #fff;
        }
    </style>
</head>
@php
$business_categories = App\BusinessCategory::pluck('category_name', 'id');
@endphp

<body class="hold-transition">
    @if (session('status'))
    <input type="hidden" id="status_span" data-status="{{ session('status.success') }}"
        data-msg="{{ session('status.msg') }}">
    @endif

    @if(!isset($no_header))
    @include('layouts.partials.header-auth')
    @endif

    @yield('content')

    <!-- Modal -->
    <style>
        .modal-dialog {
            width: 70% !important;
        }

        @media only screen and (max-width: 600px) and (min-width:300px) {
            .modal-dialog {
                width: 90% !important;
                margin-left: 5% !important;
            }
        }
    </style>
    <div class="modal fade" id="register_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content  text-left">
                <div class="modal-body">
                    <p class="form-header">@lang('business.register_and_get_started_in_minutes')</p>
                    {!! Form::open(['url' => route('business.postRegister'), 'method' => 'post',
                    'id' => 'business_register_form','files' => true ]) !!}
                    @include('business.partials.register_form')

                    {!! Form::close() !!}
                </div>

            </div>
        </div>
    </div>

    <div class="modal fade" id="visitor_register_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content  text-left">
                {!! Form::open(['url' => route('business.postVisitorRegister'), 'method' => 'post',
                'id' => 'visitor_register_form','files' => true ]) !!}
                <div class="modal-body">
                    <p class="form-header">@lang('business.register_and_get_started_in_minutes')</p>
                    @include('business.partials.register_form_visitor')

                </div>
                <hr>
                <div class="clearfix"></div>
                <div class="modal-footer">
                    <button type="submit" id="visitor_form_btn" class="btn btn-primary">Submit</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
                {!! Form::close() !!}

            </div>
        </div>
    </div>


    <!-- Modal -->
    <div class="modal fade" id="patient_register_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document" style="width: 70%;">
            <div class="modal-content">
                <div class="modal-body text-left">
                    <h2 class="form-header">@lang('business.patient_register')</h2>
                    {!! Form::open(['url' => route('business.postPatientRegister'), 'method' => 'post',
                    'id' => 'patient_register_form','files' => true ]) !!}
                    @include('business.partials.register_form_patient')
                    {!! Form::hidden('package_id', $package_id, ['class' => 'package_id']); !!}

                    {!! Form::close() !!}
                </div>

            </div>
        </div>
    </div>
    @include('layouts.partials.javascripts')
    <!-- Scripts -->
    <script src="{{ asset('js/login.js?v=' . $asset_v) }}"></script>
    @yield('javascript')

    <script type="text/javascript">
        $(document).ready(function(){
            $('input').iCheck({
                checkboxClass: 'icheckbox_square-blue',
                radioClass: 'iradio_square-blue',
                increaseArea: '20%' // optional
            });
        });
    </script>
    @foreach ($packages as $package)
    <script>
        $('#{{$package->id}}').click(function(){
            $('.package_id').val('{{$package->id}}');
            
        });
    </script>
    @endforeach
</body>

</html>