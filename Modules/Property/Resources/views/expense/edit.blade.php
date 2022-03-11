@extends('layouts.app')
@section('title', __('expense.edit_expense'))

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>@lang('expense.edit_expense')</h1>
</section>

<!-- Main content -->
<section class="content">
  {!! Form::open(['url' => action('\Modules\Property\Http\Controllers\ExpenseController@update', [$expense->id]), 'method' => 'PUT', 'id' => 'add_expense_form', 'files' => true ]) !!}
  <div class="box box-solid">
    <div class="box-body">
      <div class="row">
        <div class="col-sm-4">
          <div class="form-group">
            {!! Form::label('location_id', __('purchase.business_location').':*') !!}
            {!! Form::select('location_id', $business_locations, $expense->location_id, ['class' => 'form-control select2', 'placeholder' => __('messages.please_select'), 'required']); !!}
          </div>
        </div>
        <div class="col-sm-4">
          <div class="form-group">
            {!! Form::label('expense_category_id', __('expense.expense_category').':') !!}
            {!! Form::select('expense_category_id', $expense_categories, $expense->expense_category_id, ['class' => 'form-control select2', 'placeholder' => __('messages.please_select')]); !!}
          </div>
        </div>
        <div class="col-sm-4">
          <div class="form-group">
            {!! Form::label('ref_no', __('purchase.ref_no').':*') !!}
            {!! Form::text('ref_no', $expense->ref_no, ['class' => 'form-control', 'required']); !!}
          </div>
        </div>
        <div class="col-sm-4">
          <div class="form-group">
            {!! Form::label('transaction_date', __('messages.date') . ':*') !!}
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fa fa-calendar"></i>
              </span>
              {!! Form::text('transaction_date', @format_datetime($expense->transaction_date), ['class' => 'form-control', 'readonly', 'required', 'id' => 'expense_transaction_date']); !!}
            </div>
          </div>
        </div>
        <div class="col-sm-4">
          <div class="form-group">
            {!! Form::label('expense_for', __('expense.expense_for').':') !!} @show_tooltip(__('tooltip.expense_for'))
            {!! Form::select('expense_for', $properties, $expense->expense_for, ['class' => 'form-control select2', 'placeholder' => __('messages.please_select')]); !!}
          </div>
        </div>
        <div class="col-sm-4">
					<div class="form-group">
						{!! Form::label('contact_id', __('lang_v1.expense_for_contact').':') !!} 
						{!! Form::select('contact_id', $contacts, $expense->contact_id, ['class' => 'form-control select2', 'placeholder' => __('messages.please_select')]); !!}
					</div>
				</div>
        <div class="col-sm-4">
            <div class="form-group">
                {!! Form::label('document', __('purchase.attach_document') . ':') !!}
                {!! Form::file('document', ['id' => 'upload_document']); !!}
                <p class="help-block">@lang('purchase.max_file_size', ['size' => (config('constants.document_size_limit') / 1000000)])</p>
            </div>
        </div>
        <div class="col-sm-4">
          <div class="form-group">
            {!! Form::label('additional_notes', __('expense.expense_note') . ':') !!}
                {!! Form::textarea('additional_notes', $expense->additional_notes, ['class' => 'form-control', 'rows' => 3]); !!}
          </div>
        </div>
        <div class="clearfix"></div>
          <div class="col-md-4">
            <div class="form-group">
                {!! Form::label('tax_id', __('product.applicable_tax') . ':' ) !!}
                <div class="input-group">
                    <span class="input-group-addon">
                        <i class="fa fa-info"></i>
                    </span>
                    {!! Form::select('tax_id', $taxes['tax_rates'], $expense->tax_id, ['class' => 'form-control'], $taxes['attributes']); !!}

            <input type="hidden" name="tax_calculation_amount" id="tax_calculation_amount" 
            value="0">
                </div>
            </div>
        </div>
        <div class="col-sm-4">
          <div class="form-group">
            {!! Form::label('final_total', __('sale.total_amount') . ':*') !!}
            {!! Form::text('final_total', rtrim(rtrim($expense->final_total, '0'), '.'), ['class' => 'form-control input_number', 'placeholder' => __('sale.total_amount'), 'required']) !!}
          </div>
        </div>
				<div class="col-sm-4">
					<div class="form-group">
						{!! Form::label('expense_account', __('sale.expense_account') . ':*') !!}
						{!! Form::select('expense_account', $expense_accounts, $expense->expense_account, ['class' => 'form-control select2', 'placeholder' => __('lang_v1.please_select')]) !!}
						
					</div>
				</div>
      </div>
    </div>
  </div> <!--box end-->
     <!--box end-->
     @include('expense.recur_expense_form_part')
     <div class="box box-solid">
      <div class="box-header">
              <h3 class="box-title">@lang('lang_v1.payment')</h3>
          </div>
      <div class="box-body">
        <div class="row">
          <div class="col-md-12 payment_row">
            @if(!empty($expense->payment_lines) && $expense->payment_lines->count() > 0)
            @include('expense.partials.payment_row_form', ['row_index' => 0, 'payment' => $expense->payment_lines[0]])
            @else
            @include('expense.partials.payment_row_form', ['row_index' => 0, 'payment' => $expense->payment_lines])
            @endif
          </div>
        </div>
        <div class="col-sm-12">
          <button type="submit" class="btn btn-primary pull-right">@lang('messages.update')</button>
        </div>
      </div>
    </div>
    <!--box end-->

{!! Form::close() !!}
</section>
@endsection

@section('javascript')
   <script>
      $(document).ready(function(){
        $('#amount_0').trigger('change');
        $('.payment_types_dropdown').trigger('change');
      });
      $('#method_0').prop('disabled', false);

      $('#final_total, #amount_0').change(function(){
        total = parseFloat($('#final_total').val());
        paid = parseFloat($('#amount_0').val());
        due = total - paid;
        if(due > 0){
          $('.controller_account_div').removeClass('hide')
        }else{
          $('.controller_account_div').addClass('hide')
        }
        $('#payment_due').text(__currency_trans_from_en(due, false, false));

        var account_balance = parseFloat($('#account_id option:selected').data('account_balance'));
        if($('#account_id option:selected').data('check_insufficient_balance')){
          if(paid > account_balance){
            Insufficient_balance_swal();
          }
        }
      });

      $('#expense_category_id').change(function(){
			$.ajax({
				method: 'get',
				url: '/get-expense-account-category-id/'+ $(this).val(),
				data: {  },
				success: function(result) {
					$('#expense_account').empty().append(
						`<option value="${result.expense_account_id}" selected>${result.name}</option>`
					);
				
				},
			});
    })
    
    $('#method_0').change(function(){
      if($(this).val() == 'bank_transfer' || $(this).val() == 'direct_bank_deposit'){
        $('.account_list').removeClass('hide');
      }else{
        $('.account_list').addClass('hide');
      }
    })


   </script>
@endsection