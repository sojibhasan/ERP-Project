<div class="modal-dialog" role="document" style="width: 65%;">
  <div class="modal-content">
    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
          aria-hidden="true">&times;</span></button>
      <h4 class="modal-title">@lang( 'lang_v1.merged_sub_categories' )</h4>
    </div>

    <div class="modal-body">
      <div class="col-md-4">
        <div class="form-group">
          {!! Form::label('date_and_time', __( 'lang_v1.date_and_time' ) . ':*') !!}
          {!! Form::text('date_and_time', null, ['class' => 'form-control', 'id' => 'date_and_time', 'required',
          'placeholder' => __( 'lang_v1.date_and_time' )]); !!}
        </div>
      </div>
      <div class="col-md-4">
        <div class="form-group">
          {!! Form::label('merged_sub_category_name', __( 'lang_v1.merged_sub_category_name' ) . ':*') !!}
          {!! Form::text('merged_sub_category_name', null, ['class' => 'form-control', 'id' =>
          'merged_sub_category_name', 'required', 'placeholder' => __(
          'lang_v1.merged_sub_category_name' )]); !!}
        </div>
      </div>

      <div class="col-md-4">
        <div class="form-group">
          {!! Form::label('category', __( 'lang_v1.category' ) . ':*') !!}
          {!! Form::select('category', $categories, null, ['class' => 'form-control', 'id' => 'category', 'required',
          'placeholder' => __(
          'lang_v1.please_select' )]); !!}
        </div>
      </div>
      <div class="col-md-4">
        <div class="form-group">
          {!! Form::label('sub_categories', __( 'lang_v1.sub_categories' ) . ':*') !!}
          {!! Form::select('sub_categories[]', [], null , ['class' => 'form-control select2', 'id' => 'sub_categories', 'multiple', 'style' => 'width: 100%;',
          'required']); !!}
        </div>
      </div>
      <div class="col-md-4">
        <div class="form-group">
          {!! Form::label('status', __( 'lang_v1.status' ) . ':*') !!}
          {!! Form::select('status', ['1' => 'Active', '0' => 'Inactive'], null , ['class' => 'form-control', 'id' =>
          'status', 'required', 'placeholder' => __(
          'lang_v1.please_select' )]); !!}
        </div>
      </div>
    </div>

    <div class="clearfix"></div>

    <div class="modal-footer">
      <button type="button" class="btn btn-primary add_merged_sub_category">@lang( 'messages.save' )</button>
      <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
    </div>

  </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->

<script>
  $('#date_and_time').datepicker("setDate", new Date());
  $('#sub_categories').select2();
  $('#category').change(function(){
    $.ajax({
      contentType : 'html',
      method: 'get',
      url: '/merged-sub-category/get-sub-categories/'+$(this).val(),
      data: {  },
      success: function(result) {
        $('#sub_categories').empty().append(result);
      },
    });
  });
</script>