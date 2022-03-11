<div class="modal-dialog" role="document" style="width: 35%;">
  <div class="modal-content">

    {!! Form::open(['url' => action('ContactGroupController@update', [$contact_group->id]), 'method' => 'PUT', 'id' => 'contact_group_edit_form' ]) !!}

    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      <h4 class="modal-title">@lang( 'lang_v1.edit_contact_group' )</h4>
    </div>
    <input type="hidden" name="type" value="{{$type}}">
    <div class="modal-body">
      <div class="form-group">
        {!! Form::label('name', __( 'lang_v1.contact_group_name' ) . ':*') !!}
        {!! Form::text('name', $contact_group->name, ['class' => 'form-control', 'required', 'placeholder' => __( 'lang_v1.contact_group_name' )]); !!}
      </div>

      <div class="form-group">
        {!! Form::label('amount', __( 'lang_v1.calculation_percentage' ) . ':') !!}
        @show_tooltip(__('lang_v1.tooltip_calculation_percentage'))
        {!! Form::text('amount', @num_format($contact_group->amount), ['class' => 'form-control input_number','placeholder' => __( 'lang_v1.calculation_percentage')]); !!}
      </div>
      
      <div class="form-group">
         @if($type == "supplier")
          {!! Form::label('name', __( 'lang_v1.interest_expense_account' ) . ':') !!}
         @else
          {!! Form::label('name', __( 'lang_v1.interest_income_account' ) . ':') !!}
         @endif
        <select name="account_type_id" class="form-control select2" id="changeAccountSelect">
            <option value="">@lang( 'lang_v1.account_type' )</option>
            @foreach($allAccountsType as $accountType)
                <option value="{{ $accountType->id }}" {{ $selectedAccountType->id == $accountType->id ? 'selected' : '' }}>{{ $accountType->name }}</option>
            @endforeach
        </select>
      </div>
      <div class="form-group">
         {!! Form::label('name', __( 'lang_v1.interest_income_account' ) . ':') !!}
        <select name="interest_account_id" class="form-control select2" id="AccountName">
            <option value="">@lang( 'lang_v1.account' )</option>
            @foreach($allAccounts as $account)
                <option value="{{ $account->id }}" {{ $selectedAccount->id == $account->id ? 'selected' : '' }}>{{ $account->name }}</option>
            @endforeach
        </select>
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
  $("#changeAccountSelect").change(function(){
    var typeId = $(this).val();
    $.ajax({
      type: "POST",
      url: "{{url('fetch/AccountName')}}",
      data:{type_id:typeId,_token: '{{csrf_token()}}'},
      dataType: "json",
      success: function(data){
        $("#AccountName").html('<option value="">Account</option>');
        $.each(data.accounts, function (key, value) {
          $("#AccountName").append('<option value="' + value.id + '">' + value.name + '</option>');
        });
      }
    });
  })
</script>
<script>
  if($('#name').val() == 'Own Company'){
    $('#name').attr('readonly', true);
  }
</script>