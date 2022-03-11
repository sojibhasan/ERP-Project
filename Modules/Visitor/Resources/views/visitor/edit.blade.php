<div class="modal-dialog" role="document" style="width: 65%;">
  <div class="modal-content">

    {!! Form::open(['url' => action('\Modules\Visitor\Http\Controllers\VisitorController@update',
    $visitor->id), 'method' => 'PUT', 'id' => 'visistor_form' ])
    !!}
    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
          aria-hidden="true">&times;</span></button>
      <h4 class="modal-title">@lang( 'visitor::lang.edit_visitor' ) ({{$visitor->name}})</h4>
    </div>

    <div class="modal-body">
            <div class="col-md-4">
        <div class="form-group">
          {!! Form::label('business_name', __('visitor::lang.visitor_code') . ':*') !!}
          <div class="input-group">
            <span class="input-group-addon">
              <i class="fa fa-user"></i>
            </span>
            {!! Form::text('business_name', $visitor->business_name, ['class' => 'form-control','placeholder' =>
            __('visitor::lang.visitor_code'),
            'required', 'readonly']); !!}
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="form-group">
          {!! Form::label('date_and_time', __('visitor::lang.date_and_time') . ':*') !!}
          <div class="input-group">
            <span class="input-group-addon">
              <i class="fa fa-user"></i>
            </span>
            {!! Form::text('date_and_time',date('Y-m-d H:m:s',strtotime($visitor->date_and_time)), ['class' => 'form-control','placeholder' =>
            __('visitor::lang.date_and_time'),
            'required', 'readonly']); !!}
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="form-group">
          {!! Form::label('visited_date', __('visitor::lang.visited_date') . ':*') !!}
          <div class="input-group">
            <span class="input-group-addon">
              <i class="fa fa-user"></i>
            </span>
            {!! Form::text('visited_date',date('m/d/Y',strtotime($visitor->visited_date)), ['class' => 'form-control','placeholder' =>
            __('visitor::lang.visited_date'), 'id' => 'visited_date'
            ]); !!}
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="form-group">
          {!! Form::label('mobile_number', __('visitor::lang.mobile_number') . ':*') !!}
          <div class="input-group">
            <span class="input-group-addon">
              <i class="fa fa-user"></i>
            </span>
            {!! Form::text('mobile_number',$visitor->mobile_number, ['class' => 'form-control','placeholder' =>
            __('visitor::lang.mobile_number'),
            'required']); !!}
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="form-group">
          {!! Form::label('land_number', __('business.land_number')) !!}
          <div class="input-group">
            <span class="input-group-addon">
              <i class="fa fa-user"></i>
            </span>
            {!! Form::text('land_number', $visitor->land_number, ['class' => 'form-control','placeholder' =>
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

          {!! Form::label('name', __('business.name') . $requireText) !!}
          <div class="input-group">
            <span class="input-group-addon">
              <i class="fa fa-user"></i>
            </span>
            {!! Form::text('name', $visitor->name, ['class' => 'form-control','placeholder' =>
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
            {!! Form::text('address', $visitor->address, ['class' => 'form-control','placeholder' =>
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
          $faicon='fa-flag';
          if ($settings && $settings->enable_add_district==1)
          {
          $faicon='fa-plus';
          }
          @endphp
          {!! Form::label('district', __('business.district') . ':*') !!}
          <div class="input-group">
             <span class="input-group-addon add-district" @if($settings && $settings->enable_add_district==1) style="background-color: #00a65a;border-color: #008d4c;color: #ffff;" @endif>
              <i class="fa {{ $faicon }}"></i>
            </span>
            {!! Form::select('districts', $districts, $visitor->district_id, ['class'
            => 'form-control','placeholder' => __('lang_v1.please_select'), 'style' => 'margin:0px','id'=>'districts_towns'
            ]); !!}
            @if($settings && $settings->enable_add_district==1)
            {!! Form::text('district_name', null, ['class' => 'form-control','placeholder' =>'Enter District name...',
            'id'=>'districts_name', 'style'=>'margin: 0px; display: none;',$require]); !!}
            @endif
          </div>
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
          $faicon='fa-home';
          if ($settings && $settings->enable_add_town==1)
          {
          $faicon='fa-plus';
          }
          @endphp
          {!! Form::label('town', __('business.town') . $requireText) !!}
          <div class="input-group">
            <span class="input-group-addon add-town" @if($settings && $settings->enable_add_town==1) style="background-color: #00a65a;border-color: #008d4c;color: #ffff;" @endif>
              <i class="fa {{ $faicon }}"></i>
            </span>
             {!! Form::select('town', $towns, $visitor->town_id, ['class'
            => 'form-control','placeholder' => __('lang_v1.please_select'), 'style' => 'margin:0px', 'id'=>'towns',$require
            ]); !!}
            @if($settings && $settings->enable_add_town==1)
             {!! Form::text('town_name', null, ['class' => 'form-control','placeholder' =>'Enter Town name...',
            'id'=>'town_name','style'=>'margin: 0px; display: none;']); !!}
            @endif

          </div>
        </div>
      </div>
      <div class="col-md-12">
        <div class="form-group">
          {!! Form::label('details', __('visitor::lang.details') . ':*') !!}
          <div class="input-group" style="width: 100%">
            {!! Form::textarea('details', $visitor->details, ['class' => 'form-control','placeholder' =>
            __('visitor::lang.details'),
            'required' ,'rows' => 3]); !!}
          </div>
        </div>
      </div>


    </div>

    <div class="clearfix"></div>
    <div class="modal-footer">
      <button type="submit" class="btn btn-primary" id="save_member_btn">@lang( 'member::lang.update'
        )</button>
      <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
    </div>

    {!! Form::close() !!}

  </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->

<script>
  @if($settings && $settings->enable_add_district==1)
  $('body').on('click', '.add-district', function(event) {
      $('body').find('#districts_towns').toggle();
    
      $('body').find('#districts_name').toggle();
    
      $('body').find('#districts_name').val('');
    
      if($(this).find('i').hasClass('fa-plus'))
    
      {
    
        $(this).find('i').removeClass('fa-plus');
    
        $(this).find('i').addClass('fa-minus');
    
      }else{
    
        $(this).find('i').addClass('fa-plus');
    
        $(this).find('i').removeClass('fa-minus');
    
      } 
      $('body').find('.add-town').click();
    });
  @endif
  @if($settings && $settings->enable_add_town==1)  
    $('body').on('click', '.add-town', function(event) {
    
      $('body').find('#towns').toggle();
    
      $('body').find('#town_name').toggle();
    
      $('body').find('#town_name').val('');
    
      if($(this).find('i').hasClass('fa-plus'))
    
      {
    
        $(this).find('i').removeClass('fa-plus');
    
        $(this).find('i').addClass('fa-minus');
    
      }else{
    
        $(this).find('i').addClass('fa-plus');
    
        $(this).find('i').removeClass('fa-minus');
    
      } 
    });
  @endif  
  
  $('body').on('change', '#districts_towns', function(event) {
  
    event.preventDefault();
  
    $.ajax({
  
      url: '{{ url('default-district/getTowns') }}',
  
      type: 'POST',
  
      dataType: 'html',
  
      data: {district: $(this).val()},
  
    })
  
    .done(function(response) {
  
      $('#towns').empty();
  
      $('#towns').html(response);
  
    });
    
  });
  
  $('#visited_date').datepicker({
        format: 'mm/dd/yyyy'
    });
</script>