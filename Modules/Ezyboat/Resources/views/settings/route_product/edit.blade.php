<div class="modal-dialog" role="document">
  <div class="modal-content">

    {!! Form::open(['url' => action('\Modules\Ezyboat\Http\Controllers\BoatTripController@update', $route_product->id), 'method' =>
    'put', 'id' => 'route_product_add_form' ]) !!}

    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
          aria-hidden="true">&times;</span></button>
      <h4 class="modal-title">@lang( 'ezyboat::lang.product' )</h4>
    </div>

    <div class="modal-body">
      <div class="row">
        <div class="form-group col-sm-12">
          {!! Form::label('date', __( 'ezyboat::lang.date' ) . ':*') !!}
          {!! Form::text('date', @format_date(date('Y-m-d')), ['class' => 'form-control', 'required', 'readonly', 'placeholder' => __(
          'ezyboat::lang.date' )]); !!}
        </div>
        <div class="form-group col-sm-12">
          {!! Form::label('name', __( 'ezyboat::lang.name' ) . ':*') !!}
          {!! Form::text('name', $route_product->name, ['class' => 'form-control', 'placeholder' => __( 'ezyboat::lang.name'), 'id'
          => 'name']); !!}
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
 $('#date').datepicker('setDate', '{{@format_date($route_product->date)}}');
</script>