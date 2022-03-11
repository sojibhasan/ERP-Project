<div class="modal-dialog" role="document">
  <div class="modal-content">

    {!! Form::open(['url' => action('SalesCommissionAgentController@update', [$user->id]), 'method' => 'PUT', 'id' =>
    'sale_commission_agent_form' ]) !!}

    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
          aria-hidden="true">&times;</span></button>
      <h4 class="modal-title">@lang( 'lang_v1.edit_sales_commission_agent' )</h4>
    </div>

    <div class="modal-body">
      <div class="row">
        <div class="col-md-2">
          <div class="form-group">
            {!! Form::label('surname', __( 'business.prefix' ) . ':') !!}
            {!! Form::text('surname', $user->surname, ['class' => 'form-control', 'placeholder' => __(
            'business.prefix_placeholder' ) ]); !!}
          </div>
        </div>
        <div class="col-md-5">
          <div class="form-group">
            {!! Form::label('first_name', __( 'business.first_name' ) . ':*') !!}
            {!! Form::text('first_name', $user->first_name, ['class' => 'form-control', 'required', 'placeholder' => __(
            'business.first_name' ) ]); !!}
          </div>
        </div>
        <div class="col-md-5">
          <div class="form-group">
            {!! Form::label('last_name', __( 'business.last_name' ) . ':') !!}
            {!! Form::text('last_name', $user->last_name, ['class' => 'form-control', 'placeholder' => __(
            'business.last_name' ) ]); !!}
          </div>
        </div>
        <div class="clearfix"></div>
        <div class="col-md-6">
          <div class="form-group">
            {!! Form::label('email', __( 'business.email' ) . ':') !!}
            {!! Form::text('email', $user->email, ['class' => 'form-control', 'placeholder' => __( 'business.email' )
            ]); !!}
          </div>
        </div>
        <div class="col-md-6">
          <div class="form-group">
            {!! Form::label('contact_no', __( 'lang_v1.contact_no' ) . ':') !!}
            {!! Form::text('contact_no', $user->contact_no, ['class' => 'form-control', 'placeholder' => __(
            'lang_v1.contact_no' ) ]); !!}
          </div>
        </div>
        <div class="col-md-4 org_cmmsn @if($user->cmmsn_application == 'per_unit') hide @endif">
          <div class="form-group">
            {!! Form::label('commission_type', __( 'lang_v1.commission_type' ) . ':') !!}
            {!! Form::select('commission_type', ['fixed' => 'Fixed', 'percentage' => 'Percentage'],
            $user->commission_type, ['class' => 'form-control', 'placeholder' => __( 'lang_v1.commission_type') ,
            'required']); !!}
          </div>
        </div>
        <div class="col-md-4 org_cmmsn @if($user->cmmsn_application == 'per_unit') hide @endif">
          <div class="form-group">
            {!! Form::label('cmmsn_percent', __( 'lang_v1.cmmsn_percent' ) . ':') !!}
            {!! Form::text('cmmsn_percent', $user->cmmsn_percent, ['class' => 'form-control input_number', 'placeholder'
            => __( 'lang_v1.cmmsn_percent' ), 'required' ]); !!}
          </div>
        </div>
        <div class="col-md-4">
          <div class="form-group">
            {!! Form::label('cmmsn_application', __( 'lang_v1.cmmsn_application' ) . ':') !!}
            {!! Form::select('cmmsn_application', ['bill' => 'Bill', 'per_unit' => 'Per Unit'],
            $user->cmmsn_application, ['class' => 'form-control', 'id' => 'cmmsn_application',  'placeholder' => __( 'lang_v1.cmmsn_application') ,
            'required']); !!}
          </div>
        </div>
        @php
        if(!empty($user->cmmsn_units) && $user->cmmsn_application == 'per_unit'){
          $cmmsn_units = json_decode($user->cmmsn_units);
        }
        $i = 0;
        @endphp
        <div class="clearfix"></div>
       
        <div class="col-md-4">
          <button class="btn btn-sm btn-primary" id="add_unit" style="display: @if($user->cmmsn_application == 'per_unit') block; @else none;  @endif">Add</button></br></br>
        </div>
        <div class="clearfix"></div>
        <div class="col-md-12 cmmsn_units" style="display: @if($user->cmmsn_application == 'per_unit') block; @else none;  @endif">
          @if(!empty($user->cmmsn_units) && $user->cmmsn_application == 'per_unit')
          @foreach ( $cmmsn_units as $key => $unit_cmmsn)
          <div class="row {{$i}}">
            <div class="col-md-3">
              {!! Form::select('cmmsn_units['.$i.'][unit]', $units,  $unit_cmmsn->unit, ['class' => 'form-control',
              'placeholder' => __( 'lang_v1.cmmsn_units')]); !!}
            </div>
            <div class="col-md-3">
              {!! Form::select('cmmsn_units['.$i.'][commission_type]', ['fixed' => 'Fixed', 'percentage' => 'Percentage'],
              $unit_cmmsn->commission_type, ['class' =>
              'form-control', 'placeholder' => __( 'lang_v1.commission_type') , 'required']); !!}
            </div>
            <div class="col-md-3">
              <input type="text" name="cmmsn_units[{{$i}}][cmmsn]" value="{{$unit_cmmsn->cmmsn}}" class="form-control"
                placeholder="@lang('lang_v1.commission')">
            </div>
            <div class="col-md-2">
              <button class="btn btn-xs btn-danger remove_unit" data-index="{{$i}}" style="margin-top: 5px">x</button>
            </div>
            </br></br>
          </div>
          @php
              $i++;
          @endphp
          @endforeach
          @endif
        </div>

      </div>
    </div>
    <input type="hidden" id="index" name="index" value="{{ !empty( $cmmsn_units )? $i - 1 : 0 }}">
    <div class="modal-footer">
      <button type="submit" class="btn btn-primary">@lang( 'messages.save' )</button>
      <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
    </div>

    {!! Form::close() !!}

  </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->


<script>
  $('#cmmsn_application').change(function(){
    if($(this).val() == 'per_unit'){
      $('.cmmsn_units').show();
      $('.org_cmmsn').addClass('hide');
      $('#add_unit').show();
    }else{
      $('.cmmsn_units').hide();
      $('.org_cmmsn').removeClass('hide');
      $('#add_unit').hide();
    }
  });

  $('#add_unit').click(function(e){
    e.preventDefault();
    index = parseInt($('#index').val()) +1 ;
    $('#index').val(index);
    $('.cmmsn_units').append(`<div class="row `+index+`">
            <div class="col-md-3">
               <select class="form-control" id="cmmsn_units" name="cmmsn_units[`+index+`][unit]"><option selected="selected" value="">Select unit</option><option value="1">Pieces</option><option value="35">Nos</option></select>
            </div>
            <div class="col-md-3">
              <select class="form-control" required="" name="cmmsn_units[`+index+`][commission_type]" aria-required="true"><option selected="selected" value="">Sales Commission Type</option><option value="fixed">Fixed</option><option value="percentage">Percentage</option></select>
            </div>
            <div class="col-md-3">
              <input type="text" name="cmmsn_units[`+index+`][cmmsn]" value="" class="form-control" placeholder="Commission">
            </div>
            <div class="col-md-2">
              <button class="btn btn-xs btn-danger remove_unit" data-index="`+index+`"  style="margin-top: 5px">x</button>
            </div></br></br>
          </div>`);
  });

  $('body').on('click', '.remove_unit', function(e) {
  // $('.remove_unit').click(function(e){
    e.preventDefault();
    var row_index = parseInt($(this).data('index'));
    $('.'+row_index).remove();
    console.log(row_index);
  });
</script>