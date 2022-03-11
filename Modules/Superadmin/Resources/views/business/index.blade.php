@extends('layouts.app')
@section('title', __('superadmin::lang.superadmin') . ' | Business')

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>@lang( 'superadmin::lang.all_business' )
        <small>@lang( 'superadmin::lang.manage_business' )</small>
    </h1>
    <!-- <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Level</a></li>
        <li class="active">Here</li>
    </ol> -->
</section>

<!-- Main content -->
<section class="content">
    <div class="row">
        <div class="col-md-12">
        @component('components.filters', ['title' => __('report.filters')])
            <div class="row">
                <div class="col-md-3">
                    <form action="{{route('filter.business')}}" method="post" id="form">
                        @csrf
                        <div class="form-group">
                            {!! Form::label('filter_business', __('lang_v1.all_business') . ':') !!}
                            <select name="filter_business" id="filter_business" class="form-control select2 filter_business">
                                <option value="all">All</option>
                                @foreach ($business as $busi)
                                    <option value="{{$busi->id}}">{{$busi->company_name}}</option>
                                @endforeach
        
                            </select>
                        </div>
                    </form>
                </div>
            </div>
        @endcomponent
        </div>
    </div>
	<div class="box">
        <div class="box-header">
            <h3 class="box-title">&nbsp;</h3>
        	<div class="box-tools">
                <a href="{{action('\Modules\Superadmin\Http\Controllers\BusinessController@create')}}" 
                    class="btn btn-block btn-primary">
                	<i class="fa fa-plus"></i> @lang( 'messages.add' )</a>
            </div>
        </div>

        <div class="box-body">
            @can('superadmin')

                @foreach ($businesses as $business)
                    @php
                        $address = $business->locations->first();
                    @endphp
                    @if($loop->index % 3 == 0)
                        <div class="row">
                    @endif

                    <div class="col-md-4">
                        
                        <div class="box box-widget widget-user-2">
                
                            <div class="widget-user-header bg-yellow">
                                <h4 style="float:right; color: brown; font-weight: 700;">{{$business->company_number}}</h4>
                              <div class="widget-user-image">
                                @if(!empty($business->logo))
                                    <img class="img-circle" src="{{ url( 'public/uploads/business_logos/' . $business->logo ) }}" alt="Business Logo">
                                @endif
                              </div>
                              <!-- /.widget-user-image -->
                              <h4 class="widget-user-username">{{ $business->name }}</h4>
                              <h5 class="widget-user-desc"><i class="fa fa-user-secret" title="Owner"></i> {{ $business->owner->first_name . ' ' . $business->owner->last_name}}</h5>
                              @if ($business->is_patient)
                              @php
                                  $patient_code = App\User::where('business_id', $business->id)->first()->username;
                              @endphp
                                <h5 class="widget-user-desc"><i class="fa fa-id-card" title="Owner"></i> {{ $patient_code}}</h5>
                              @endif
                              <h5 class="widget-user-desc"><i class="fa fa-envelope" title="Owner Email"></i> {{ $business->owner->email}}</h5>
                                <h5 class="widget-user-desc"><i class="fa fa-mobile" title="Owner Contact"></i> {{ $business->owner->contact_no }}</h5>
                                <h5 class="widget-user-desc"><i class="fa fa-phone" title="Business Contact"></i> {{ implode([$address->mobile, $address->alternate_number], ", ") }}</h5>
                                <address class="widget-user-desc">
                                  @php
                                    $address_array = [];
                                    $city_landmark = '';
                                    if(!empty($address->city)){
                                        $city_landmark = $address->city;
                                    }
                                    if(!empty($address->landmark)){
                                        $city_landmark .= ', ' . $address->landmark;
                                    }
                                    if(!empty($city_landmark)){
                                        $address_array[] = $city_landmark;
                                    }

                                    $state_country = '';
                                    if(!empty($address->state)){
                                        $state_country = $address->state;
                                    }
                                    if(!empty($address->country)){
                                        $state_country .= ' (' . $address->country . ')';
                                    }
                                    if(!empty($state_country)){
                                        $address_array[] = $state_country;
                                    }
                                    if(!empty($address->zip_code)){
                                        $address_array[] = __('business.zip_code') . ': ' .$address->zip_code;
                                    }
                                  @endphp
                                  {!! strip_tags(implode('<br>', $address_array), '<br>') !!}
                                </address>
                            <h5 class="widget-user-desc">
                                <i class="fa fa-credit-card" title="Active Package"></i> 
                                @php
                                    $package = !empty($business->subscriptions[0]) ? optional($business->subscriptions[0])->package : '';
                                @endphp

                                @if(!empty($package))
                                    {{$package->name}} 
                                @endif
                            </h5>
                                @if(!empty($business->subscriptions[0]))
                                    <h5 class="widget-user-desc">
                                        <i class="fa fa-clock-o"></i> 
                                            @lang('superadmin::lang.remaining', ['days' => \Carbon::today()->diffInDays($business->subscriptions[0]->end_date)])
                                    </h5>
                                @endif
                            </div>
                            <div class="box-footer">
                                <a href="{{action('\Modules\Superadmin\Http\Controllers\BusinessController@show', [$business->id])}}"
                                class="btn btn-info btn-xs">@lang('messages.view' )</a>

                                <button type="button" class="btn btn-primary btn-xs btn-modal" data-href="{{action('\Modules\Superadmin\Http\Controllers\SuperadminSubscriptionsController@create', ['business_id' => $business->id])}}" data-container=".view_modal">
                                    @lang('superadmin::lang.add_subscription' )
                                </button>

                                @if($business->is_active == 1)
                                    <a href="{{action('\Modules\Superadmin\Http\Controllers\BusinessController@toggleActive', [$business->id, 0])}}"
                                        class="btn btn-danger btn-xs link_confirmation">@lang('messages.deactivate')
                                    </a>
                                @else
                                    <a href="{{action('\Modules\Superadmin\Http\Controllers\BusinessController@toggleActive', [$business->id, 1])}}"
                                        class="btn btn-success btn-xs link_confirmation">@lang('messages.activate' )
                                    </a>
                                @endif

                                @if($business_id != $business->id)
                                    <a href="{{action('\Modules\Superadmin\Http\Controllers\BusinessController@destroy', [$business->id])}}"
                                        class="btn btn-danger btn-xs delete_business_confirmation">@lang('messages.delete' )
                                    </a>
                                    <a href="{{action('\Modules\Superadmin\Http\Controllers\BusinessController@manage', [$business->id])}}"
                                        class="btn btn-success btn-xs">@lang('superadmin::lang.manage' )
                                    </a>
                                @endif
                                <a href="{{action('\Modules\Superadmin\Http\Controllers\BusinessController@loginAsBusiness', [$business->id])}}"
                                    class="btn btn-primary btn-xs">@lang('superadmin::lang.login' )
                                </a>
                            </div>
                        </div>
                    </div>

                    @if($loop->index % 3 == 2)
                        </div>
                    @endif
                @endforeach

                <div class="col-md-12">
                    {{ $businesses->links() }}
                </div>
                
            @endcan
        </div>

    </div>

    <div class="modal fade brands_modal" role="dialog" 
    	aria-labelledby="gridSystemModalLabel">
    </div>

</section>
<!-- /.content -->

@endsection

@section('javascript')

<script type="text/javascript">
  $('#filter_business').select2();
    $('#filter_business').change(function(){
        $('#form').submit();
    });
    $(document).on('click', 'a.delete_business_confirmation', function(e){
        e.preventDefault();
        swal({
            title: LANG.sure,
            text: "Once deleted, you will not be able to recover this business!",
            icon: "warning",
            buttons: true,
            dangerMode: true,
        }).then((confirmed) => {
            if (confirmed) {
                window.location.href = $(this).attr('href');
            }
        });
    });

  
</script>

@endsection
