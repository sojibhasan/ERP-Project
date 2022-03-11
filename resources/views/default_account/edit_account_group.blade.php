<div class="modal-dialog" role="document">
    <div class="modal-content">

  {!! Form::open(['url' => action('DefaultAccountGroupController@update', $account_group->id), 'method' => 'put', 'id' => 'account_group_form' ]) !!}
  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    <h4 class="modal-title">@lang( 'lang_v1.edit_account_group' )</h4>
  </div>

  <div class="modal-body">
    <div class="form-group">
      {!! Form::label('name', __( 'lang_v1.name' ) . ':*') !!}
      {!! Form::text('name', $account_group->name, ['class' => 'form-control', 'required', 'placeholder' => __( 'lang_v1.name' ), 'id' => 'account_group_name_group']);
      !!}
    </div>

    <div class="form-group">
      {!! Form::label('account_type_id', __( 'account.account_type' ) .":") !!}
      <select name="account_type_id" class="form-control select2" id="account_type_id_group">
        <option>@lang('messages.please_select')</option>
        @foreach($account_types as $account_type)
        <optgroup label="{{$account_type->name}}">
          <option @if($account_group->account_type_id == $account_type->id) selected @endif value="{{$account_type->id}}">{{$account_type->name}}</option>
          @foreach($account_type->sub_types as $sub_type)
          <option @if($account_group->account_type_id == $sub_type->id) selected @endif value="{{$sub_type->id}}">{{$sub_type->name}}</option>
          @endforeach
        </optgroup>
        @endforeach
      </select>
    </div>

    <div class="form-group">
      {!! Form::label('note', __( 'lang_v1.note' )) !!}
      {!! Form::textarea('note', $account_group->note, ['class' => 'form-control', 'placeholder' => __( 'lang_v1.note'
      ), 'rows' => 3, 'id' => 'note_group' ]); !!}
    </div>

  </div>

  <div class="modal-footer">
    <button type="submit" class="btn btn-primary"  id="update_account_group_btn">@lang( 'messages.save' )</button>
    <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
  </div>

  {!! Form::close() !!}

    </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->