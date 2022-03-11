<div class="modal-dialog" role="document">
  <div class="modal-content">

    {!! Form::open(['url' => action('\Modules\Ezyboat\Http\Controllers\BoatTripController@update', $boat_trip->id), 'method' =>
    'put', 'id' => 'route_add_form' ]) !!}

    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
          aria-hidden="true">&times;</span></button>
      <h4 class="modal-title">@lang( 'ezyboat::lang.boat_trip' )</h4>
    </div>

    <div class="modal-body">
      <div class="row">
        <div class="form-group col-sm-12">
          {!! Form::label('date', __( 'ezyboat::lang.date' ) . ':*') !!}
          {!! Form::text('date', null, ['class' => 'form-control', 'required', 'readonly', 'placeholder' => __(
          'ezyboat::lang.date' )]); !!}
        </div>
        <div class="form-group col-sm-12">
          {!! Form::label('trip_name', __( 'ezyboat::lang.trip_name' ) . ':*') !!}
          {!! Form::text('trip_name', $boat_trip->trip_name, ['class' => 'form-control', 'required', 'placeholder' => __( 'ezyboat::lang.trip_name'), 'id'
          => 'trip_name']); !!}
        </div>
        <div class="form-group col-sm-12">
          {!! Form::label('starting_location', __( 'ezyboat::lang.starting_location' ) . ':*') !!}
          {!! Form::text('starting_location', $boat_trip->starting_location, ['class' => 'form-control', 'placeholder' => __( 'ezyboat::lang.starting_location'), 'id'
          => 'starting_location']); !!}
        </div>
        <div class="form-group col-sm-12">
          {!! Form::label('final_location', __( 'ezyboat::lang.final_location' ) . ':*') !!}
          {!! Form::text('final_location', $boat_trip->final_location, ['class' => 'form-control', 'placeholder' => __( 'ezyboat::lang.final_location'), 'id'
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
 $('#date').datepicker('setDate', '{{@format_date($boat_trip->date)}}');
 
</script>