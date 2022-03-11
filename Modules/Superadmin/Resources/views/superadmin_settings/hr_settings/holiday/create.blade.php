<div class="modal-dialog" role="document" style="width: 55%;">
  <div class="modal-content">

    {!! Form::open(['url' => action('\Modules\HR\Http\Controllers\HolidayController@store'), 'method' =>
    'post', 'id' => 'holiday_form' ])
    !!}
    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
          aria-hidden="true">&times;</span></button>
      <h4 class="modal-title">@lang( 'hr::lang.add_holiday' )</h4>
    </div>

    <div class="modal-body">
      <div class="col-md-12">
        <div class="form-group">
          {!! Form::label('event_name', __( 'hr::lang.event_name' )) !!}
          {!! Form::text('event_name', null, ['class' => 'form-control', 'required', 'placeholder' =>
          __( 'hr::lang.event_name')]);
          !!}
        </div>
      </div>

      <div class="col-md-12">
        <div class="form-group">
          {!! Form::label('description', __( 'hr::lang.description' )) !!}
          {!! Form::textarea('description', null, ['class' => 'form-control short', 'id' =>
          'description', 'placeholder' => __( 'hr::lang.description' )]);
          !!}
        </div>
      </div>

      <div class="col-md-4">
        <div class="form-group">
          {!! Form::label('start_date', __( 'hr::lang.start_date' )) !!}
          {!! Form::text('start_date', null, ['class' => 'form-control short', 'id' =>
          'start_date', 'placeholder' => __( 'hr::lang.start_date' )]);
          !!}
        </div>
      </div>

      <div class="col-md-4">
        <div class="form-group">
          {!! Form::label('end_date', __( 'hr::lang.end_date' )) !!}
          {!! Form::text('end_date', null, ['class' => 'form-control short', 'id' =>
          'end_date', 'placeholder' => __( 'hr::lang.end_date' )]);
          !!}
        </div>
      </div>
    </div>

    <div class="clearfix"></div>

    <div class="modal-footer">
      <button type="submit" class="btn btn-primary" id="save_holiday_btn">@lang( 'messages.save' )</button>
      <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
    </div>

    {!! Form::close() !!}

  </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->

<script>
  $('#start_date').datepicker("setDate" , new Date());
  $('#end_date').datepicker("setDate" , new Date());
</script>