@extends('layouts.guest')
@section('title', $business->name)
@section('css')
<style>
  label {
    font-size: 21px;
  }

  .select2-container--default .select2-selection--single,
  .select2-selection .select2-selection--single {
    height: 48px;
  }

  .select2-container .select2-selection--single .select2-selection__rendered {
    padding-right: 10px;
    padding-top: 7px;
    font-size: 20px;
  }

  .box-title {
    font-size: 26px !important;
    font-weight: bold;
  }
</style>
@endsection
@section('content')
<!-- Main content -->
<div class="container">
  <section class="content">
    <div class="row">
      <div class="col-md-12">
        <h3 class="text-center">{{ $business->name}}</h3>
        @component('components.widget', ['class' => 'box-default', 'title' => __(
        'visitor::lang.visitor_registration')])
        {!! Form::open(['url' => action('\Modules\Visitor\Http\Controllers\VisitorController@saveVisitor'),
        'method' =>
        'post', 'id' => 'visistor_form' ])
        !!}
        <input type="hidden" name="business_id" value="{{$business->id}}">
        <div class="col-md-4 hide">
          <div class="form-group">
            {!! Form::label('date_and_time', __('visitor::lang.date_and_time') . ':*') !!}
            {!! Form::text('date_and_time',Carbon::now(), ['class' => 'form-control input-lg','placeholder' =>
            __('visitor::lang.date_and_time'),
            'required', 'readonly']); !!}
          </div>
        </div>
        <div class="col-md-4 hide">
          <div class="form-group">
            {!! Form::label('visited_date', __('visitor::lang.visited_date') . ':*') !!}
            {!! Form::text('visited_date', Carbon::now()->format('m/d/Y'), ['class' => 'form-control
            input-lg','placeholder' =>
            __('visitor::lang.visited_date'), 'id' => 'visited_date'
            ]); !!}
          </div>
        </div>
        <div class="col-md-4">
          <div class="form-group">
            {!! Form::label('mobile_number', __('visitor::lang.mobile_number') . ':*') !!}
            {!! Form::text('mobile_number', null, ['class' => 'form-control input-lg','placeholder' =>
            __('visitor::lang.mobile_number'),
            'required']); !!}
          </div>
        </div>
        <div class="col-md-4 @if (!empty($settings) && $settings->enable_required_name == 0) hide @endif">
          <div class="form-group">
            @php
            $requireText='';
            $require='';
            if (!empty($settings) && $settings->enable_required_name==1)
            {
            $requireText=':*';
            $require='required';
            }
            @endphp
            {!! Form::label('name', __('business.name') .$requireText) !!}
            {!! Form::text('name', null, ['class' => 'form-control input-lg','placeholder' =>
            __('business.name'),
            $require]); !!}
          </div>
        </div>
        <div class="col-md-4 @if (!empty($settings) && $settings->enable_required_address == 0) hide @endif">
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
            {!! Form::text('address', null, ['class' => 'form-control input-lg','placeholder' =>
            __('business.address'),
            $require]); !!}
          </div>
        </div>
        <div class="col-md-4 @if (!empty($settings) && $settings->enable_required_district == 0) hide @endif">
          <div class="form-group">
            {!! Form::label('district_id', __('business.district')) !!}
            {!! Form::select('district_id', $districts, null, ['class'
            => 'form-control select2','placeholder' => __('lang_v1.please_select'), 'style' =>
            'margin:0px','id'=>'district_id',
            $require
            ]); !!}
            <div class="@if (!empty($settings) && $settings->enable_add_district == 0) hide @endif  ">
              <a id="add_button_district" class="btn-modal pull-right" data-container=".default_districts_model"
                data-href="{{action('DefaultDistrictController@create')}}" style="cursor: pointer">
                <i class="fa fa-plus"></i> @lang( 'messages.add' )</a>
            </div>
          </div>
        </div>
        <div class="col-md-4 @if (!empty($settings) && $settings->enable_required_town == 0) hide @endif">
          <div class="form-group">
            {!! Form::label('town_id', __('business.town')) !!}
            {!! Form::select('town_id', $towns, null, ['class'
            => 'form-control select2','placeholder' => __('lang_v1.please_select'), 'style' => 'margin:0px',
            'id'=>'town_id',$require
            ]); !!}
            <div class="@if (!empty($settings) && $settings->enable_add_town == 0) hide @endif">
              <a class="btn-modal pull-right" id="add_button_towns"
                data-href="{{action('DefaultTownController@create')}}" data-container=".default_towns_model"
                style="cursor: pointer">
                <i class="fa fa-plus"></i> @lang( 'messages.add' )</a>
            </div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="form-group">
            {!! Form::label('no_of_accompanied', __('visitor::lang.no_of_accompanied')) !!}
            <div class="input-group" style="width: 100%">
              {!! Form::text('no_of_accompanied', null, ['class' => 'form-control input-lg input_number','placeholder'
              =>
              __('visitor::lang.no_of_accompanied'),
              'rows' => 3]); !!}
            </div>
          </div>
        </div>
        <div class="clearfix"></div>


        <div class="visitor_no_register_field hide">
          <div class="col-md-4">
            <div class="form-group">
              {!! Form::label('username', __('business.username') . ':*') !!}
              {!! Form::text('username', null, ['class' => 'form-control input-lg','placeholder' =>
              __('business.username')]); !!}

            </div>
          </div>

          <div class="col-md-4">
            <div class="form-group">
              {!! Form::label('password', __('business.password') . ':*') !!}
              {!! Form::password('password', ['class' => 'form-control input-lg', 'id' => 'password', 'style'
              =>
              'margin: 0px;','placeholder'
              => __('business.password')]); !!}
              <p class="help-block" style="color: white;">At least 6 character.</p>
            </div>
          </div>
          <div class="col-md-4">
            <div class="form-group">
              {!! Form::label('confirm_password', __('business.confirm_password') . ':*') !!}
              {!! Form::password('confirm_password', ['class' => 'form-control input-lg', 'id' =>
              'confirm_password', 'style' => 'margin: 0px;', 'placeholder' =>
              __('business.confirm_password')]); !!}
            </div>
          </div>
        </div>
        <div class="col-md-12">
          <button type="submit" class="btn btn-primary" id="save_visitor_btn" style="float: right">@lang(
            'messages.save' )</button>
        </div>
        {!! Form::close() !!}
        @endcomponent
      </div>
    </div>
  </section>
</div>

<div class="modal fade default_districts_model" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
</div>
<div class="modal fade default_towns_model" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel"></div>
@endsection
@section('javascript')
<script>
  $('body').on('click', '.btn-submit', function(event) {
      event.preventDefault();
      let form = $(this).closest('form');
      if(form.attr('id') == 'district_form'){
        let name = form.find('#name').val();
        let business_id = {{$business->id}};
        $('.default_districts_model').modal('hide');
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
        $('.default_towns_model').modal('hide');
        $.ajax({
          method: 'POST',
          url: "{{ url('default-town') }}",
          data: { name, business_id, district_id },
          success: function(result) {
            if(result.success){
              $('#district_id').val('').trigger('change');
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
            if ($('#district_id').find("option[value='" + result.details.district_id + "']").length) {
                $('#district_id').val(result.details.district_id).trigger('change');
            }
            setTimeout(() => {  if ($('#town_id').find("option[value='" + result.details.town_id + "']").length) {
                $('#town_id').val(result.details.town_id).trigger('change');
            } }, 3000);
            // $('.visitor_no_register_field').addClass('hide');
            $('#no_of_accompanied').val(result.details.no_of_accompanied);
          }else{
            // $('.visitor_no_register_field').removeClass('hide');
          }
        },
      });
    })


    $('#visited_date').datepicker({
          format: 'mm/dd/yyyy'
      });
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