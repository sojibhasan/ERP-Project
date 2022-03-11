<div class="modal-dialog" role="document">
    <div class="modal-content">
    @if(!empty($business_category))
      {!! Form::open(['url' => action('BusinessCategoryController@update', $business_category->id), 'method' => 'put', 'id' => 'business_category_form' ]) !!}
    @else
      {!! Form::open(['url' => action('BusinessCategoryController@store'), 'method' => 'post', 'id' => 'business_category_form' ]) !!}
    @endif
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">@lang( 'superadmin::lang.add_business_category' )</h4>
      </div>
  
      <div class="modal-body">
              <div class="form-group">
                  {!! Form::label('category_name', __( 'superadmin::lang.category_name' ) .":*") !!}
                  {!! Form::text('category_name',!empty($business_category) ? $business_category->category_name : null, ['class' => 'form-control', 'required','placeholder' => __( 'superadmin::lang.category_name' ) ]); !!}
              </div>
      </div>
  
      <div class="modal-footer">
        <button type="submit" class="btn btn-primary">@lang( 'messages.save' )</button>
        <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
      </div>
  
      {!! Form::close() !!}
  
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
  