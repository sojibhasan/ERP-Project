<div class="modal-dialog" role="document" style="width: 55%;">
  <div class="modal-content">

    {!! Form::open(['url' => action('\Modules\HR\Http\Controllers\LeaveApplicationTypeController@update', $leave_application_type->id), 'method' =>
    'put', 'id' => 'leave_application_type_form' ])
    !!}
    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
          aria-hidden="true">&times;</span></button>
      <h4 class="modal-title">@lang( 'hr::lang.edit_leave_application_type' )</h4>
    </div>

    <div class="modal-body">
      <div class="col-md-12">
        <div class="form-group">
          {!! Form::label('leave_type', __( 'hr::lang.leave_type' )) !!}
          {!! Form::text('leave_type', $leave_application_type->leave_type, ['class' => 'form-control', 'required', 'placeholder' =>
          __( 'hr::lang.leave_type')]);
          !!}
        </div>
      </div>

      <div class="col-md-12">
        <div class="form-group">
          {!! Form::label('allowed_days', __( 'hr::lang.allowed_days' )) !!}
          {!! Form::number('allowed_days', $leave_application_type->allowed_days, ['class' => 'form-control short', 'id' =>
          'allowed_days', 'placeholder' => __( 'hr::lang.allowed_days' )]);
          !!}
        </div>
      </div>

    </div>

    <div class="clearfix"></div>

    <div class="modal-footer">
      <button type="submit" class="btn btn-primary" id="save_leave_application_type_btn">@lang( 'messages.save' )</button>
      <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
    </div>

    {!! Form::close() !!}

  </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->

<script>
 
</script>