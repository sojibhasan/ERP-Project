@php
$layout = 'app';
@endphp
@extends('layouts.'.$layout)

@section('title', __('visitor::lang.visitor_registration'))

@section('content')
<!-- Main content -->
<section class="content">
  <div class="row">
    <div class="col-md-12">
      @component('components.widget', ['class' => 'box-primary', 'title' => __(
      'visitor::lang.visitor_registration')])

      {!! Form::open(['url' => action('\Modules\Visitor\Http\Controllers\VisitorRegistrationController@store'), 'method' =>
      'post', 'id' => 'visistor_form' ])
      !!}
      <div class="col-md-4">
        <div class="form-group">
          {!! Form::label('mobile_number', __('visitor::lang.mobile_number') . ':*') !!}
          <div class="input-group">
            <span class="input-group-addon">
              <i class="fa fa-mobile"></i>
            </span>
            {!! Form::text('mobile_number', null, ['class' => 'form-control','placeholder' =>
            __('visitor::lang.mobile_number'),
            'required']); !!}
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="form-group">
          {!! Form::label('land_number', __('business.land_number') . ':*') !!}
          <div class="input-group">
            <span class="input-group-addon">
              <i class="fa fa-phone"></i>
            </span>
            {!! Form::text('land_number', null, ['class' => 'form-control','placeholder' =>
            __('business.land_number')
            ]); !!}
          </div>
        </div>
      </div>

      <div class="col-md-4">
        <div class="form-group">
          @php
          $requireText='';
          $require='';
          if ($settings && $settings->enable_required_name==1)
          {
          $requireText=':*';
          $require='required';
          }
          @endphp
          {!! Form::label('name', __('business.name') .$requireText) !!}
          <div class="input-group">
            <span class="input-group-addon">
              <i class="fa fa-user"></i>
            </span>
            {!! Form::text('name', null, ['class' => 'form-control','placeholder' =>
            __('business.name'),
            $require]); !!}
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="form-group">
          @php
          $requireText='';
          $require='';
          if ($settings && $settings->enable_required_address==1)
          {
          $requireText=':*';
          $require='required';
          }
          @endphp
          {!! Form::label('address', __('business.address') . $requireText) !!}
          <div class="input-group">
            <span class="input-group-addon">
              <i class="fa fa-user"></i>
            </span>
            {!! Form::text('address', null, ['class' => 'form-control','placeholder' =>
            __('business.address'),
            $require]); !!}
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="form-group">
          @php
          $requireText='';
          $require='';
          if ($settings && $settings->enable_required_district==1)
          {
          $requireText=':*';
          $require='required';
          }

          @endphp
          {!! Form::label('district_id', __('business.district') . $requireText) !!}
          {!! Form::select('district_id', $districts, null, ['class'
          => 'form-control select2','placeholder' => __('lang_v1.please_select'), 'style' => 'width:
          100%;','id'=>'district_id' ]); !!}
          @if($settings && $settings->enable_add_district==1)
          <div class="">
            <a id="add_button_district" class="btn-modal pull-right" data-container=".view_modal"
              data-href="{{action('DefaultDistrictController@create')}}" style="cursor: pointer">
              <i class="fa fa-plus"></i> @lang( 'messages.add' )</a>
          </div>
          @endif
        </div>
      </div>

      <div class="col-md-4">
        <div class="form-group">
          @php
          $requireText='';
          $require='';
          if ($settings && $settings->enable_required_town==1)
          {
          $requireText=':*';
          $require='required';
          }

          @endphp
          {!! Form::label('town_id', __('business.town') . $requireText) !!}
          {!! Form::select('town_id', $towns, null, ['class'
          => 'form-control select2','placeholder' => __('lang_v1.please_select'), 'style' => 'width: 100%;',
          'id'=>'town_id' ]); !!}
          @if($settings && $settings->enable_add_town==1)
          <div class="">
            <a class="btn-modal pull-right" id="add_button_towns" data-href="{{action('DefaultTownController@create')}}"
              data-container=".view_modal" style="cursor: pointer">
              <i class="fa fa-plus"></i> @lang( 'messages.add' )</a>
          </div>
          @endif
        </div>
      </div>
      <div class="visitor_no_register_field hide">
        <div class="col-md-4">
          <div class="form-group">
            {!! Form::label('username', __('business.username') . ':*') !!}
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fa fa-user-o"></i>
              </span>
              {!! Form::text('username', null, ['class' => 'form-control','placeholder' =>
              __('business.username'),
              'required']); !!}
            </div>
          </div>
        </div>

        <div class="col-md-4">
          <div class="form-group">
            {!! Form::label('password', __('business.password') . ':*') !!}
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fa fa-key"></i>
              </span>

              {!! Form::password('password', ['class' => 'form-control', 'id' => 'password', 'style'
              =>
              'margin: 0px;','placeholder'
              => __('business.password')]); !!}
            </div>
            <p class="help-block" style="color: white;">At least 6 character.</p>
          </div>
        </div>
        <div class="col-md-4">
          <div class="form-group">
            {!! Form::label('confirm_password', __('business.confirm_password') . ':*') !!}
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fa fa-key"></i>,
              </span>
              {!! Form::password('confirm_password', ['class' => 'form-control', 'id' =>
              'confirm_password', 'style' => 'margin: 0px;', 'placeholder' =>
              __('business.confirm_password')]); !!}
            </div>
          </div>
        </div>
      </div>
      <div class="col-md-12">
        <div class="form-group">
          {!! Form::label('details', __('visitor::lang.details')) !!}
          <div class="input-group" style="width: 100%">
            {!! Form::textarea('details', null, ['class' => 'form-control','placeholder' =>
            __('visitor::lang.details'),
            'rows' => 3]); !!}
          </div>
        </div>
      </div>
      <div class="col-md-12">
        <button type="submit" class="btn btn-primary" id="save_visitor_btn" style="float: right">@lang( 'messages.save'
          )</button>
      </div>
      {!! Form::close() !!}
      @endcomponent
    </div>
  </div>


  @endsection
  @section('javascript')
  <script>
    $('body').on('click', '.btn-submit', function(event) {
      event.preventDefault();
      let form = $(this).closest('form');
      if(form.attr('id') == 'district_form'){
        let name = form.find('#name').val();
        let business_id = {{$business->id}};
        $('.view_modal').modal('hide');
        $.ajax({
          method: 'POST',
          url: "{{ url('default-district') }}",
          data: { name, business_id },
          success: function(result) {
            if(result.success){
              toastr.success(result.msg)
            }else{
              toastr.error(result.msg)
            }
            getDistricts();
          },
        });
      }
      if(form.attr('id') == 'town_form'){
        let name = form.find('#name').val();
        let district_id = form.find('#district_id').val();
        let business_id = {{$business->id}};
        $('.view_modal').modal('hide');
        $.ajax({
          method: 'POST',
          url: "{{ url('default-town') }}",
          data: { name, business_id, district_id },
          success: function(result) {
            if(result.success){
              toastr.success(result.msg)
            }else{
              toastr.error(result.msg)
            }
            $('#districts_towns').trigger('change');
          },
        });
      }
    });
    $('#mobile_number').change(function(){
      let mobile_number = $(this).val();
      $.ajax({
        method: 'get',
        url: "{{url('/visitor/get-detail-if-registered')}}",
        data: { mobile_number },
        success: function(result) {
          if(result.success == 1){
            $('#name').val(result.details.name);
            $('#address').val(result.details.address);
            $('#land_number').val(result.details.land_number);
            if ($('#district_id').find("option[value='" + result.details.district_id + "']").length) {
                $('#district_id').val(result.details.district_id).trigger('change');
            }
            setTimeout(() => {  if ($('#town_id').find("option[value='" + result.details.town_id + "']").length) {
                $('#town_id').val(result.details.town_id).trigger('change');
            } }, 2000);
            
            $('#no_of_accompanied').val(result.details.no_of_accompanied);
            $('.visitor_no_register_field').addClass('hide');
          }else{
            $('.visitor_no_register_field').removeClass('hide');
          }
        },
      });
    })
    $('body').on('change', '#district_id', function(event) {
      event.preventDefault();
      $.ajax({
        url: '{{ url('default-district/getTowns') }}',
        type: 'GET',
        dataType: 'html',
        data: {district: $(this).val()},
      })
      .done(function(response) {
        $('#town_id').empty();
        $('#town_id').html(response);
      });
      
    });
    function getDistricts(){
      $.ajax({
        url: '{{ url('default-district/get-drop-down') }}',
        type: 'GET',
        dataType: 'html',
        data: {},
      })
      .done(function(response) {
        $('#district_id').empty();
        $('#district_id').html(response);
      });
    }
  </script>
  @endsection