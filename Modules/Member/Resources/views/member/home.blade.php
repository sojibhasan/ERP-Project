@extends('layouts.member')
@section('title', __('home.home'))

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>{{ __('home.welcome_message', ['name' => auth()->user()->name]) }}
    </h1>
</section>
<!-- Main content -->
<section class="content no-print">
	

</section>
<!-- /.content -->
@stop
@section('javascript')
 
@endsection

