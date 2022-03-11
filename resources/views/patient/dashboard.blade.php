@extends('layouts.app')
@section('title', __('home.home'))

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>{{ __('home.welcome_message', ['name' => Session::get('user.surname') . ' '. Session::get('user.first_name'). ' '. Session::get('user.last_name')]) }}
    </h1>
</section>
<!-- Main content -->
<section class="content no-print">
    @php
    $bgs = ['bg-aqua', 'bg-yellow', 'bg-primary', 'bg-success', 'bg-info', 'bg-warning'];
    @endphp
    <div class="row">
        @foreach ($all_family_patients as $patient)
        @php
        $rand = rand(0,5);
        @endphp
        <a href="{{action('PatientController@show', $patient->id)}}">
            <div class="col-md-3 col-sm-6 col-xs-12">
                <div class="info-box">
                    <span class="info-box-icon {{$bgs[$rand]}}" style="font-size: 0px; line-height:0px;"><img style="height:90px;" src="@if(empty($patient->profile_image)){{asset('img/default.png')}} @else {{url($patient->profile_image)}} @endif" alt=""></span>

                    <div class="info-box-content">
                        <span class="info-box-text" style="font-size: 20px; color: black;">{{ $patient->username }}</span>
                        <span class="info-box-text" style="font-size: 15px; color: black;">{{ $patient->first_name }}</span>
                    </div>
                    <!-- /.info-box-content -->
                </div>
                <!-- /.info-box -->
            </div>
        </a>
        @endforeach
    </div>
</section>
<!-- /.content -->
@endsection
@section('javascript')

@endsection