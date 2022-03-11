@extends('layouts.employee')
@section('title', __('home.home'))

@section('css')
    {!! Charts::styles(['highcharts']) !!}
@endsection

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>{{ __('home.welcome_message', ['name' => Auth::user()->first_name. ' '. Auth::user()->last_name]) }}
    </h1>
</section>
<!-- Main content -->
<section class="content no-print">

 
</section>
<!-- /.content -->


@endsection

