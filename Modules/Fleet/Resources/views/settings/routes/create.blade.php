<div class="modal-dialog" role="document">
  <div class="modal-content">

    {!! Form::open(['url' => action('\Modules\Fleet\Http\Controllers\RouteController@store'), 'method' =>
    'post', 'id' => !empty($quick_add) ? 'quick_add_route' : 'route_add_form' ]) !!}

    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
          aria-hidden="true">&times;</span></button>
      <h4 class="modal-title">@lang( 'fleet::lang.route' )</h4>
    </div>

    <div class="modal-body">
      <div class="row">
        <input type="hidden" name="quick_add" id="quick_add" value="{{$quick_add}}">
        <div class="form-group col-sm-12">
          {!! Form::label('date', __( 'fleet::lang.date' ) . ':*') !!}
          {!! Form::text('date', @format_date(date('Y-m-d')), ['class' => 'form-control', 'required', 'readonly', 'placeholder' => __(
          'fleet::lang.date' )]); !!}
        </div>
        <div class="form-group col-sm-12">
          {!! Form::label('route_name', __( 'fleet::lang.route_name' ) . ':*') !!}
          {!! Form::text('route_name', null, ['class' => 'form-control', 'required', 'placeholder' => __( 'fleet::lang.route_name'), 'id'
          => 'route_name']); !!}
        </div>
        <div class="form-group col-sm-12">
          {!! Form::label('orignal_location', __( 'fleet::lang.orignal_location' ) . ':*') !!}
          {!! Form::text('orignal_location', null, ['class' => 'form-control', 'placeholder' => __( 'fleet::lang.orignal_location'), 'id'
          => 'orignal_location']); !!}
        </div>
        <div class="form-group col-sm-12">
          {!! Form::label('destination', __( 'fleet::lang.destination' ) . ':*') !!}
          {!! Form::text('destination', null, ['class' => 'form-control', 'placeholder' => __( 'fleet::lang.destination'), 'id'
          => 'destination']); !!}
        </div>
        <div class="form-group col-sm-12">
          {!! Form::label('distance', __( 'fleet::lang.distance_km' ) . ':*') !!}
          {!! Form::text('distance', @num_format(0.00), ['class' => 'form-control', 'placeholder' => __( 'fleet::lang.distance'), 'id'
          => 'distance']); !!}
        </div>
        <div class="form-group col-sm-12">
          {!! Form::label('rate', __( 'fleet::lang.rate_km' ) . ':*') !!}
          {!! Form::text('rate', @num_format(0.00), ['class' => 'form-control', 'placeholder' => __( 'fleet::lang.rate'), 'id'
          => 'rate']); !!}
        </div>
        <div class="form-group col-sm-12">
          {!! Form::label('route_amount', __( 'fleet::lang.route_amount' ) . ':*') !!}
          {!! Form::text('route_amount', @num_format(0.00), ['class' => 'form-control', 'placeholder' => __( 'fleet::lang.route_amount'), 'id'
          => 'route_amount']); !!}
        </div>       
        <div class="form-group col-sm-12">
          {!! Form::label('driver_incentive', __( 'fleet::lang.driver_incentive' ) . ':*') !!}
          {!! Form::text('driver_incentive', null, ['class' => 'form-control', 'placeholder' => __( 'fleet::lang.driver_incentive'), 'id'
          => 'driver_incentive']); !!}
        </div>       
        <div class="form-group col-sm-12">
          {!! Form::label('helper_incentive', __( 'fleet::lang.helper_incentive' ) . ':*') !!}
          {!! Form::text('helper_incentive', null, ['class' => 'form-control', 'placeholder' => __( 'fleet::lang.helper_incentive'), 'id'
          => 'helper_incentive']); !!}
        </div>       
      </div>

    </div>

    <div class="modal-footer">
      <button type="submit" class="btn btn-primary">@lang( 'messages.save' )</button>
      <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
    </div>

    {!! Form::close() !!}

  </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->

<script>
 $('#date').datepicker('setDate', new Date());
 $('#distance, #rate').change(function () {
    let distance = parseFloat($('#distance').val());
    let rate = parseFloat($('#rate').val());

    let route_amount = distance * rate;
    __write_number($('#route_amount'), route_amount);

 })

</script>