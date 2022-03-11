<div class="modal-dialog" role="document">
    <div class="modal-content">
  
      {!! Form::open(['url' => action('CustomerLimitApprovalController@updateApprovalLimit',  $customer->id), 'method' => 'post', 'id' => 'limit_form' ]) !!}
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
            aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">@lang( 'lang_v1.customer_name' ) : {{$customer->name}}</h4>
      </div>
  
      <div class="modal-body">
        <div class="form-group">
          {!! Form::label('over_limit_percentage', __( 'lang_v1.over_limit_percentage' )) !!}
          {!! Form::text('over_limit_percentage', $customer->over_limit_percentage, ['class' => 'form-control', 'required', 'placeholder' => __(
          'lang_v1.over_limit_percentage'), 'id' => 'over_limit_percentage' ]); !!}
        </div>
        <input type="hidden" value="{{$requested_user}}" id="requested_user" name="requested_user">
  
      </div>
  
      <div class="modal-footer">
        <button type="button" id="limit_form_btn" class="btn btn-primary">@lang( 'messages.save' )</button>
        <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
      </div>
  
      {!! Form::close() !!}
  
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
  
  <script>
   
  </script>