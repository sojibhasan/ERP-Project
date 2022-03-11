<div class="modal-dialog" role="document">
  <div class="modal-content">

    {!! Form::open(['url' => action('\Modules\Ezyboat\Http\Controllers\BoatTripController@store'), 'method' =>
    'post', 'id' => !empty($quick_add) ? 'quick_add_route' : 'route_add_form' ]) !!}

    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
          aria-hidden="true">&times;</span></button>
      <h4 class="modal-title">@lang( 'ezyboat::lang.boat_trip' )</h4>
    </div>

    <div class="modal-body">
      <div class="row">
        <input type="hidden" name="quick_add" id="quick_add" value="{{$quick_add}}">
        <div class="form-group col-sm-12">
          {!! Form::label('date', __( 'ezyboat::lang.date' ) . ':*') !!}
          {!! Form::text('date', @format_date(date('Y-m-d')), ['class' => 'form-control', 'required', 'readonly', 'placeholder' => __(
          'ezyboat::lang.date' )]); !!}
        </div>
        <div class="form-group col-sm-12">
          {!! Form::label('trip_name', __( 'ezyboat::lang.name' ) . ':*') !!}
          {!! Form::text('trip_name', null, ['class' => 'form-control', 'required', 'placeholder' => __( 'ezyboat::lang.name'), 'id'
          => 'trip_name']); !!}
        </div>
        <div class="form-group col-sm-12">
          {!! Form::label('starting_location', __( 'ezyboat::lang.starting_location' ) . ':*') !!}
          {!! Form::text('starting_location', null, ['class' => 'form-control', 'placeholder' => __( 'ezyboat::lang.starting_location'), 'id'
          => 'starting_location']); !!}
        </div>
        <div class="form-group col-sm-12">
          {!! Form::label('final_location', __( 'ezyboat::lang.final_location' ) . ':*') !!}
          {!! Form::text('final_location', null, ['class' => 'form-control', 'placeholder' => __( 'ezyboat::lang.final_location'), 'id'
          => 'final_location']); !!}
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