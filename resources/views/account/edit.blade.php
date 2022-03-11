<div class="modal-dialog" role="document">
  <div class="modal-content">

    {!! Form::open(['url' => action('AccountController@update',$account->id), 'method' => 'PUT', 'id' =>
    'edit_payment_account_form' ]) !!}

    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
          aria-hidden="true">&times;</span></button>
      <h4 class="modal-title">@lang( 'account.edit_account' )</h4>
    </div>

    <div class="modal-body">
      <div class="form-group">
        {!! Form::label('name', __( 'lang_v1.name' ) .":*") !!}
        @if (!empty($account->default_account_id))
        {!! Form::text('name', $account->name, ['class' => 'form-control', 'required','placeholder' => __(
        'lang_v1.name' ), 'readonly' ]); !!}
        @else
        {!! Form::text('name', $account->name, ['class' => 'form-control', 'required','placeholder' => __(
        'lang_v1.name' ) ]); !!}
        @endif
      </div>

      <div class="form-group @if($account_access==0) hide @endif">
        {!! Form::label('account_number', __( 'account.account_number' ) .":*") !!}
        {!! Form::text('account_number', $account->account_number, ['class' => 'form-control', 'required','placeholder'
        => __( 'account.account_number' ) ]); !!}
      </div>

      <div class="form-group opening_balance_div @if(empty($account->is_main_account)) hide @endif">
        {!! Form::label('transaction_date', __( 'account.transaction_date' ) .":*") !!}
        {!! Form::text('transaction_date', null, ['class' => 'form-control', 'placeholder' => __(
        'account.transaction_date' ) ]); !!}
      </div>
      <div class="form-group opening_balance_div  @if(empty($account->is_main_account)) hide @endif">
        {!! Form::label('opening_balance', __( 'account.opening_balance' ) .":*") !!}
        {!! Form::text('opening_balance', $balance->balance, ['class' => 'form-control', 'placeholder' => __(
        'account.opening_balance' ) ]); !!}
      </div>
      <div class="form-group opening_balance_div  @if(empty($account->is_main_account)) hide @endif">
        {!! Form::label('increase_reduce', __( 'account.increase_reduce' ) .":*") !!}
        {!! Form::select('increase_reduce', ['increase' => 'Increase', 'reduce' => 'Reduce'], null, ['class' =>
        'form-control', 'placeholder' => __(
        'lang_v1.please_select' ) ]); !!}
      </div>

      <div class="form-group">
        {!! Form::label('account_type_id', __( 'account.account_type' ) .":") !!}
        <select name="account_type_id" class="form-control select2" id="account_type_id" required>
          <option>@lang('messages.please_select')</option>
          @foreach($account_types as $account_type)
          <optgroup label="{{$account_type->name}}">
            <option value="{{$account_type->id}}" @if($account->account_type_id == $account_type->id) selected @endif
              >{{$account_type->name}}</option>
            @foreach($account_type->sub_types as $sub_type)
            <option value="{{$sub_type->id}}" @if($account->account_type_id == $sub_type->id) selected @endif
              >{{$sub_type->name}}</option>
            @endforeach
          </optgroup>
          @endforeach
        </select>
      </div>

      <div class="form-group">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('is_main_account', 1, !empty($account->is_main_account) ? $account->is_main_account : 0,
            ['class' => 'input-icheck check_main_or_sub', 'id' => 'is_main_account']); !!}
            {{__('lang_v1.main_account_dsc')}}
          </label>
        </div>
      </div>

      <div class="form-group">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('sub_type', 1, !empty($account->parent_account_id) ? $account->parent_account_id : 0,
            ['class' => 'input-icheck check_main_or_sub', 'id' => 'sub_type']); !!}
            {{__('lang_v1.sub_type')}}
          </label>
        </div>
      </div>

      <div class="form-group parent_account @if(empty($account->parent_account_id)) hide @endif">
        {!! Form::label('parent_account_id', __( 'lang_v1.parent_account' ) .":") !!}
        {!! Form::select('parent_account_id', $parent_accounts, $account->parent_account_id, ['placeholder' =>
        __('messages.please_select'), 'style' => 'width: 100%', 'class' => 'form-control select2']) !!}
      </div>

      <div class="form-group asset_type">
        {!! Form::label('asset_type', __( 'account.account_group' ) .":") !!}
        {!! Form::select('asset_type', $account_groups, $account->asset_type, ['placeholder' =>
        __('messages.please_select'), 'class' => 'form-control']) !!}
      </div>


      <div class="form-group">
        {!! Form::label('note', __( 'brand.note' )) !!}
        {!! Form::textarea('note', $account->note, ['class' => 'form-control', 'placeholder' => __( 'brand.note' ),
        'rows' => 4]); !!}
      </div>
    </div>

    <div class="modal-footer">
      <button type="submit" class="btn btn-primary">@lang( 'messages.update' )</button>
      <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
    </div>

    {!! Form::close() !!}

  </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->

<script>
  $(document).ready(function(){
    $('#account_type_id').trigger('change');
  })

  // $('#is_main_account').change(function(){
  //     if($(this).prop('checked') == true){
  //         $('.opening_balance_div').addClass('hide');
  //     }else{
  //         $('.opening_balance_div').removeClass('hide');
  //     }
  // });

  $('#sub_type').change(function(){
        if($(this).prop('checked') == true){
            $('.parent_account').removeClass('hide');
        }else{
            $('.parent_account').addClass('hide');
        }
    });

    $('#account_type_id').change(function(){
        account_type_id = $(this).val();
        saved_account_group = '{{$account->asset_type}}';

        $.ajax({
            method: 'get',
            'content-Type' : 'html',
            url: '/get-account-groups/'+account_type_id,
            data: {  },
            success: function(result) {
                $('#asset_type').empty();
                $('#asset_type').append(result);
                $('#asset_type').val(saved_account_group).selected();
            },
        });

        $.ajax({
            method: 'get',
            'content-Type' : 'html',
            url: '/accounting-module/get-parent-account-by-type/'+account_type_id,
            data: {  },
            success: function(result) {
                $('#parent_account_id').empty();
                $('#parent_account_id').append(result);
            },
        });
    });

    $('#transaction_date').datepicker( 'setDate', '{{$start_date}}');

    $('#edit_payment_account_form').validate({
        rules: {
            name: {
                required: true
            },
            account_number: {
                required: true
            },
            account_type_id: {
                required: true
            },
            transaction_date: {
                required: function(element) {
                  if($("#opening_balance").val().length > 0){
                    return true;
                  }
                  return false;
                }
            },
            increase_reduce: {
                required: function(element) {
                  if($("#opening_balance").val().length > 0){
                    return true;
                  }
                  return false;
                }
            }
        }
      });
      $('.check_main_or_sub').change(function(){
        id = $(this).attr('id');
        console.log(id);
        $('.check_main_or_sub').not(this).prop('checked', false);  
    })
</script>