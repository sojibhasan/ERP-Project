<div class="modal-dialog" role="document">
    <div class="modal-content">

  {!! Form::open(['url' => action('ManageUserController@updatePassword', $id), 'method' => 'post', 'id' => 'account_type_form' ]) !!}
  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    <h4 class="modal-title">@lang( 'superadmin::lang.update_password' ): {{$username}}</h4>
  </div>

  <div class="modal-body">
        <div class="form-group">
          {!! Form::label('password', __( 'superadmin::lang.password' )) !!}
            {!! Form::password('password', ['class' => 'form-control', 'required', 'placeholder' => __( 'superadmin::lang.password' )]); !!}
        </div>
  </div>

  <div class="modal-footer">
    <input  type="submit" value="@lang( 'superadmin::lang.update' )" name="update" class="btn btn-primary">
    <input  type="submit" value="@lang( 'superadmin::lang.update_password_send_sms' )" name="update_sms" class="btn btn-warning">
    <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
  </div>

  {!! Form::close() !!}

    </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->