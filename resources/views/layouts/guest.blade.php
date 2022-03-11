<!DOCTYPE html>
<html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <!-- Tell the browser to be responsive to screen width -->
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title')</title> 
    @include('layouts.partials.css')

    @yield('css')
   
</head>

<body>
    @if (session('status'))
    <input type="hidden" id="status_span" data-status="{{ session('status.success') }}"
        data-msg="{{ session('status.msg') }}">
    @endif
    @yield('content')

    @include('layouts.partials.javascripts')
</body>

</html>