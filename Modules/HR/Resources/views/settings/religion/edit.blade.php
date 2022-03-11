<div class="modal-dialog" role="document" style="width: 55%;">
  <div class="modal-content">

    {!! Form::open(['url' => action('\Modules\HR\Http\Controllers\ReligionController@update', $religion->id), 'method' =>
    'put', 'id' => 'religion_form' ])
    !!}
    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
          aria-hidden="true">&times;</span></button>
      <h4 class="modal-title">@lang( 'hr::lang.edit_religion' )</h4>
    </div>

    <div class="modal-body">
      <div class="col-md-12">
        <div class="form-group">
          {!! Form::label('religion_name', __( 'hr::lang.religion_name' )) !!}
          {!! Form::text('religion_name', $religion->religion_name, ['class' => 'form-control', 'required', 'placeholder' =>
          __( 'hr::lang.religion_name')]);
          !!}
        </div>
      </div>
      <div class="col-md-4">
        <div class="form-group">
          {!! Form::label('religion_status', __( 'hr::lang.religion_status' )) !!}
          {!! Form::select('religion_status', ['1' => 'Yes', '0' => 'No'], $religion->religion_status, ['class' => 'form-control', 'required', 'placeholder' =>
          __( 'hr::lang.religion_status')]);
          !!}
        </div>
      </div>

    <div class="clearfix"></div>

    <div class="modal-footer">
      <button type="submit" class="btn btn-primary" id="save_religion_btn">@lang( 'messages.save' )</button>
      <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
    </div>

    {!! Form::close() !!}

  </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->

<script>
 
</script>