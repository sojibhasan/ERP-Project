<div class="modal-dialog" role="document" style="width: 55%;">
  <div class="modal-content">

    {!! Form::open(['url' => action('\Modules\HR\Http\Controllers\NoticeBoardController@update', $notice->id), 'method' =>
    'put', 'id' => 'notice_form' ])
    !!}
    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
          aria-hidden="true">&times;</span></button>
      <h4 class="modal-title">@lang( 'hr::lang.add_notice' )</h4>
    </div>

    <div class="modal-body">
      <div class="col-md-12">
        <div class="form-group">
          {!! Form::label('title', __( 'hr::lang.title' )) !!}
          {!! Form::text('title', $notice->title, ['class' => 'form-control', 'required', 'placeholder' =>
          __( 'hr::lang.title')]);
          !!}
        </div>
      </div>

      <div class="col-md-12">
        <div class="form-group">
          {!! Form::label('short_description', __( 'hr::lang.short_description' )) !!}
          {!! Form::text('short_description', $notice->short_description, ['class' => 'form-control short', 'id' =>
          'short_description', 'placeholder' => __( 'hr::lang.short_description' )]);
          !!}
        </div>
      </div>

      <div class="clearfix"></div>
      <div class="col-xs-12">
        <div class="form-group">
          {!! Form::label('notice_details', __('hr::lang.notice_details') . ':') !!}
          {!! Form::textarea('notice_details', $notice->notice_details, ['class' => 'form-control','placeholder' =>
          __('hr::lang.notice_details')]); !!}
        </div>
      </div>

      <div class="col-md-12">
        <div class="row">
          <div class="col-md-1">
            {!! Form::label('status', __( 'hr::lang.status' ). ':', ['style' => 'margin-top: 7px;']) !!}
          </div>
          <div class="col-md-3">
            {!! Form::select('status', ['published'=> 'Published', 'unpublished' => 'Unpublished'], $notice->status, ['class' => 'form-control', 'placeholder' => __('lang_v1.please_select')]) !!}
          </div>
        </div>
      </div>



    </div>

    <div class="modal-footer">
      <button type="submit" class="btn btn-primary" id="save_notice_btn">@lang( 'messages.save' )</button>
      <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
    </div>

    {!! Form::close() !!}

  </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->

<script>
  if ($('#notice_details').length) {
      tinymce.init({
          selector: 'textarea#notice_details',
      });
  }
</script>