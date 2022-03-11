<div class="modal-dialog" role="document">
  <div class="modal-content">

    {!! Form::open(['url' => action('\Modules\Ezyboat\Http\Controllers\RouteInvoiceNumberController@store'), 'method' =>
    'post', 'id' => 'route_invoice_number_add_form' ]) !!}

    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
          aria-hidden="true">&times;</span></button>
      <h4 class="modal-title">@lang( 'ezyboat::lang.starting_invoice_number' )</h4>
    </div>

    <div class="modal-body">
      <div class="row">
        <div class="form-group col-sm-12">
          {!! Form::label('date', __( 'ezyboat::lang.date' ) . ':*') !!}
          {!! Form::text('date', @format_date(date('Y-m-d')), ['class' => 'form-control', 'required', 'readonly', 'placeholder' => __(
          'ezyboat::lang.date' )]); !!}
        </div>
        <div class="form-group col-sm-12">
          {!! Form::label('prefix', __( 'ezyboat::lang.prefix' ) . ':*') !!}
          {!! Form::text('prefix', null, ['class' => 'form-control', 'placeholder' => __( 'ezyboat::lang.prefix'), 'id'
          => 'prefix']); !!}
        </div>
        <div class="form-group col-sm-12">
          {!! Form::label('starting_number', __( 'ezyboat::lang.starting_number' ) . ':*') !!}
          {!! Form::text('starting_number', null, ['class' => 'form-control', 'placeholder' => __( 'ezyboat::lang.starting_number'), 'id'
          => 'starting_number']); !!}
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
</script>