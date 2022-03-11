<div class="modal-dialog" role="document" style="width: 55%;">
  <div class="modal-content">

    {!! Form::open(['url' => action('\Modules\HR\Http\Controllers\EmploymentStatusController@update', $employment_status->id), 'method' =>
    'put', 'id' => 'employment_status_form' ])
    !!}
    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
          aria-hidden="true">&times;</span></button>
      <h4 class="modal-title">@lang( 'hr::lang.edit_employment_status' )</h4>
    </div>

    <div class="modal-body">
      <div class="col-md-12">
        <div class="form-group">
          {!! Form::label('status_name', __( 'hr::lang.status_name' )) !!}
          {!! Form::text('status_name', $employment_status->status_name, ['class' => 'form-control', 'required', 'placeholder' =>
          __( 'hr::lang.status_name')]);
          !!}
        </div>
      </div>
     
    <div class="clearfix"></div>

    <div class="modal-footer">
      <button type="submit" class="btn btn-primary" id="save_employment_status_btn">@lang( 'messages.save' )</button>
      <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
    </div>

    {!! Form::close() !!}

  </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->

<script>
 
</script>