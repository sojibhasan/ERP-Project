@extends('layouts.guest')
@section('title', $business->name)
@section('content')
<!-- Main content -->
<div class="container">
    <section class="content">
        <div class="row" style="margin-top: auto; margin-bottom: auto;">
            <div class="col-md-6 col-md-offset-3">
                <div class="row text-center">
                    <h2>{{$business->name}}</h2>
                </div>

                <div class="jumbotron">
                    <h2>@lang('visitor::lang.thank_you')</h2>
                    <hr>
                    <p style="font-size: 13px; color: gray"> {{$visitor->date_and_time}}</p>
                    <p>@lang('visitor::lang.tankyou_for_visiting') {{$business->name}}</p>
                    <p>@lang('visitor::lang.your_number_is') <span style="font-weight: bold; color: {{$visitor_code_color}}">{{$unique_code}}</span>
                    </p>
                    <br>
                    <br>
                    @lang('visitor::lang.system_develop_by') <a href="{{$site_url}}">{{ $site_name}}</a><br>
                    <a href="{{$site_url}}">{{ $site_url}}</a>
                </div>

            </div>
        </div>
    </section>
</div>
@endsection
@section('javascript')

@endsection