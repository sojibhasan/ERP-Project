@extends('layouts.app')
@section('title', __('business.business_locations'))
@php
$business_or_entity = App\System::getProperty('business_or_entity');
@endphp
@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>@if($business_or_entity == 'business'){{ __('business.business_locations') }} @elseif($business_or_entity == 'entity'){{ __('lang_v1.entity_locations') }} @else  {{ __('business.business_locations') }} @endif
        <small>@if($business_or_entity == 'business'){{ __('business.manage_your_business_locations') }} @elseif($business_or_entity == 'entity'){{ __('lang_v1.manage_your_entity_locations') }} @else {{ __('business.manage_your_business_locations') }} @endif</small>
    </h1>
</section>

<!-- Main content -->
<section class="content">
    @if($business_or_entity == 'business')
    @component('components.widget', ['class' => 'box-primary', 'title' => __( 'business.all_your_business_locations' )])
    @elseif($business_or_entity == 'entity')
    @component('components.widget', ['class' => 'box-primary', 'title' => __( 'lang_v1.all_your_entity_locations' )])
    @else
    @component('components.widget', ['class' => 'box-primary', 'title' => __( 'business.all_your_business_locations' )])
    @endif
        @slot('tool')
            <div class="box-tools">
                <button type="button" class="btn btn-block btn-primary btn-modal" 
                    data-href="{{action('BusinessLocationController@create')}}" 
                    data-container=".location_add_modal">
                    <i class="fa fa-plus"></i> @lang( 'messages.add' )</button>
            </div>
        @endslot
        <div class="table-responsive">
            <table class="table table-bordered table-striped" id="business_location_table">
                <thead>
                    <tr>
                        <th>@lang( 'invoice.name' )</th>
                        <th>@lang( 'lang_v1.location_id' )</th>
                        <th>@lang( 'business.landmark' )</th>
                        <th>@lang( 'business.city' )</th>
                        <th>@lang( 'business.zip_code' )</th>
                        <th>@lang( 'business.state' )</th>
                        <th>@lang( 'business.country' )</th>
                        <th>@lang( 'lang_v1.price_group' )</th>
                        <th>@lang( 'invoice.invoice_scheme' )</th>
                        <th>@lang( 'invoice.invoice_layout' )</th>
                        <th>@lang( 'messages.action' )</th>
                    </tr>
                </thead>
            </table>
        </div>
    @endcomponent

    <div class="modal fade location_add_modal" tabindex="-1" role="dialog" 
    	aria-labelledby="gridSystemModalLabel">
    </div>
    <div class="modal fade location_edit_modal" tabindex="-1" role="dialog" 
        aria-labelledby="gridSystemModalLabel">
    </div>

</section>
<!-- /.content -->

@endsection
