<div class="modal-dialog" role="document" style="width: 55%;">
  <div class="modal-content">

    {!! Form::open(['url' => action('\Modules\TasksManagement\Http\Controllers\ReminderController@store'), 'method' =>
    'post', 'id' => 'note_form' ])
    !!}
    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
          aria-hidden="true">&times;</span></button>
      <h4 class="modal-title">@lang( 'tasksmanagement::lang.add_note' )</h4>
    </div>

    <div class="modal-body">
      <div class="col-md-4">
        <div class="form-group">
          {!! Form::label('name', __( 'tasksmanagement::lang.name' )) !!}
          {!! Form::text('name', null, ['class' => 'form-control', 'required', 'placeholder' =>
          __( 'tasksmanagement::lang.name')]);
          !!}
        </div>
      </div>

      <div class="col-md-4">
        <div class="form-group">
          {!! Form::label('options', __( 'tasksmanagement::lang.options' )) !!}
          {!! Form::select('options', $options, null, ['class' => 'form-control select2 options', 'id' =>
          'options', 'placeholder' => __( 'tasksmanagement::lang.please_select' )]);
          !!}
        </div>
      </div>

      <div class="col-md-4 other_pages_dropdown hide">
        <div class="form-group">
          {!! Form::label('other_pages', __( 'tasksmanagement::lang.other_pages' )) !!}
          {!! Form::select('other_pages[]', $other_pages, null, ['class' => 'form-control select2 other_pages', 'id' =>
          'other_pages', 'style' => 'width: 100%;', 'multiple']);
          !!}
        </div>
      </div>





    </div>
    <div class="clearfix"></div>
    <div class="modal-footer">
      <button type="submit" class="btn btn-primary" id="save_note_btn">@lang( 'messages.save' )</button>
      <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
    </div>

    {!! Form::close() !!}

  </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->

<script>
  $('#options').change(function(){
    let option = $(this).val();
    console.log(option);
    if(option === 'in_other_page'){
      $('.other_pages_dropdown').removeClass('hide');
    }else{
      $('.other_pages_dropdown').addClass('hide');
    }
  });

  $('.select2').select2();
</script>