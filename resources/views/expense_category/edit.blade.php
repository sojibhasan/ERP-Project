<div class="modal-dialog" role="document">
  <div class="modal-content">

    {!! Form::open(['url' => action('ExpenseCategoryController@update', [$expense_category->id]), 'method' => 'PUT', 'id' => 'expense_category_add_form' ]) !!}

    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      <h4 class="modal-title">@lang( 'expense.edit_expense_category' )</h4>
    </div>

    <div class="modal-body">
     <div class="form-group">
        {!! Form::label('name', __( 'expense.category_name' ) . ':*') !!}
          {!! Form::text('name', $expense_category->name, ['class' => 'form-control', 'required', 'placeholder' => __( 'expense.category_name' )]); !!}
      </div>

      <div class="form-group">
        {!! Form::label('code', __( 'expense.category_code' ) . ':') !!}
          {!! Form::text('code', $expense_category->code, ['class' => 'form-control', 'placeholder' => __( 'expense.category_code' )]); !!}
      </div>
      <div class="form-group">
        {!! Form::label('expense_account', __('sale.expense_account') . ':*') !!}
        {!! Form::select('expense_account', $expense_accounts, $expense_category->expense_account, ['class' => 'form-control select2', 'placeholder' =>
        __('lang_v1.please_select'), 'style' => 'width: 100%', 'required']) !!}
      </div>
      <div class="form-group">
        <label for="is_sub_category">
          {!! Form::checkbox('is_sub_category', 1, $expense_category->is_sub_category, ['class' => 'input-icheck', 'id' => 'is_sub_category']) !!} @lang('expense.is_sub_category')
        </label>
      </div>
      <div class="form-group @if($expense_category->is_sub_category == 0) hide @endif parent_category">
        {!! Form::label('parent_id', __('expense.parent_category') . ':*') !!}
        {!! Form::select('parent_id', $expense_categories, $expense_category->parent_id, ['class' => 'form-control select2', 'placeholder'
        =>
        __('lang_v1.please_select'), 'style' => 'width: 100%']) !!}
      </div>
    <div class="modal-footer">
      <button type="submit" class="btn btn-primary">@lang( 'messages.update' )</button>
      <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
    </div>

    {!! Form::close() !!}

  </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->


<script>
  $('#name, #is_sub_category, #parent_name').change(function(){
    var is_sub_category = 0;
    if($('#is_sub_category').prop("checked") == true){
      $('#parent_id').prop('required',true);
      is_sub_category = 1;
    }
    $.ajax({
      method: 'POST',
      url: '/expense-categories/check-duplicate',
      data: { name: $(this).val(), is_sub_category: is_sub_category, 'parent_name' : $('#parent_id option:selected').text() },
      success: function(result) {
        if(result.success === '0'){
          toastr.error(result.msg);
          $('#name').val('');
        }
      },
    });
  })

  $('#is_sub_category').change(function(){
    if($(this).prop('checked')){
      $('.parent_category').removeClass('hide');
    }else{
      $('.parent_category').addClass('hide');
    }
  })
  $('.select2').select2();
</script>