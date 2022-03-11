<div class="modal-dialog" role="document">
    <div class="modal-content">
  
      {!! Form::open(['url' => action('DefaultAccountController@store'), 'method' => 'post', 'id' => 'payment_account_form' ]) !!}
  
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">@lang( 'account.add_account' )</h4>
      </div>
  
      <div class="modal-body">
              <div class="form-group">
                  {!! Form::label('name', __( 'lang_v1.name' ) .":*") !!}
                  {!! Form::text('name', null, ['class' => 'form-control', 'required','placeholder' => __( 'lang_v1.name' ) ]); !!}
              </div>
  
              <div class="form-group">
                  {!! Form::label('account_number', __( 'account.account_number' ) .":*") !!}
                  {!! Form::text('account_number', null, ['class' => 'form-control', 'required','placeholder' => __( 'account.account_number' ) ]); !!}
              </div>
  
              <div class="form-group">
                  {!! Form::label('account_type_id', __( 'account.account_type' ) .":") !!}
                  <select name="account_type_id" class="form-control select2" id="account_type_id">\
                      <option>@lang('messages.please_select')</option>
                      @foreach($account_types as $account_type)
                          <optgroup label="{{$account_type->name}}">
                              <option value="{{$account_type->id}}">{{$account_type->name}}</option>
                              @foreach($account_type->sub_types as $sub_type)
                                  <option value="{{$sub_type->id}}">{{$sub_type->name}}</option>
                              @endforeach
                          </optgroup>
                      @endforeach
                  </select>
              </div>
  
              <div class="form-group asset_type">
                  {!! Form::label('asset_type', __( 'account.account_group' ) .":") !!}
                  {!! Form::select('asset_type', [], null, ['placeholder' => __('messages.please_select'), 'class' => 'form-control']) !!}
              </div>
  

  
          
              <div class="form-group">
                  {!! Form::label('note', __( 'brand.note' )) !!}
                  {!! Form::textarea('note', null, ['class' => 'form-control', 'placeholder' => __( 'brand.note' ), 'rows' => 4]); !!}
              </div>
      </div>
  
      <div class="modal-footer">
        <button type="submit" class="btn btn-primary">@lang( 'messages.save' )</button>
        <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
      </div>
  
      {!! Form::close() !!}
  
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
  
  <script >
      var asset_type_array = {{$asset_type_ids}};
      $('#account_type_id').change(function(){
          var this_val = parseInt($(this).val());
            $.ajax({
                method: 'get',
                url: '/default-account-group/get-account-groups-by-type/'+this_val,
                data: {  },
                contentType : 'html',
                success: function(result) {
                    $('#asset_type').empty().append(result);
                },
            });
      });
     
  </script>