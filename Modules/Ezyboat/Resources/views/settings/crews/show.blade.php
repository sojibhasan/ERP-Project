@extends('layouts.app')
@section('title', __('ezyboat::lang.view_crew'))

@section('content')
<style>
    .select2{
        width: 100% !important;
    }
</style>
<!-- Content Header (Page header) -->
<section class="content-header no-print">
    <h1>{{ __('ezyboat::lang.view_crew') }}</h1>
</section>

<!-- Main content -->
<section class="content no-print">
    <div class="row">
        <div class="col-md-4 col-xs-12">
            {!! Form::select('fleet_id', $crew_dropdown, $crew->id , ['class' => 'form-control select2', 'id' =>
            'fleet_id']); !!}
        </div>
        <div class="col-md-2 col-xs-12"></div>
    </div>
    <br>
    <div class="row">
        <div class="col-md-12">
            <div class="nav-tabs-custom">
                <ul class="nav nav-tabs nav-justified">
                    <li class="
                            @if(!empty($view_type) &&  $view_type == 'ledger')
                                active
                            @else
                                ''
                            @endif">
                        <a href="#ledger_tab" data-toggle="tab" aria-expanded="true"><i class="fa fa-anchor"
                                aria-hidden="true"></i> @lang('lang_v1.ledger')</a>
                    </li>
                </ul>

                <div class="tab-content" style="background: #fbfcfc;">
                    <div class="tab-pane
                                @if(!empty($view_type) &&  $view_type == 'ledger')
                                    active
                                @else
                                    ''
                                @endif" id="ledger_tab">
                        <div class="row">
                            <div class="col-md-12">
                                @include('ezyboat::settings.crews.partials.ledger_tab')
                            </div>
                        </div>
                    </div>
                   
                </div>
            </div>
        </div>
    </div>
</section>

@endsection

@section('javascript')
<script>
     $('#fleet_id').change( function() {
        if ($(this).val()) {
            window.location = "{{url('/fleet-management/crews')}}/" + $(this).val()+"?tab={{$view_type}}";
        }
    });
</script>
    
@endsection