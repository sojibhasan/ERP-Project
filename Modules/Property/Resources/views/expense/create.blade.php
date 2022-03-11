@extends('layouts.app')
@section('title', __('expense.add_expense'))

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
	<h1>@lang('expense.add_expense')</h1>
</section>

<!-- Main content -->
<section class="content">
	{!! Form::open(['url' => action('\Modules\Property\Http\Controllers\ExpenseController@store'), 'method' => 'post', 'id' => 'add_expense_form', 'files'
	=> true ]) !!}
	<div class="box box-solid">
		<div class="box-body">
			<div class="row">

				@if(count($business_locations) == 1)
				@php
				$default_location = current(array_keys($business_locations->toArray()))
				@endphp
				@else
				@php $default_location = null; @endphp
				@endif
				{!! Form::hidden('property_id', $property_id, []) !!}
				<div class="col-sm-4">
					<div class="form-group">
						{!! Form::label('location_id', __('purchase.business_location').':*') !!}
						{!! Form::select('location_id', $business_locations,
						!empty($temp_data->location_id)?$temp_data->location_id: $default_location, ['class' =>
						'form-control select2', 'placeholder' => __('messages.please_select'), 'required']); !!}
					</div>
				</div>

				<div class="col-sm-4">
					<div class="form-group">
						{!! Form::label('expense_category_id', __('expense.expense_category').':') !!}
						<div class="input-group">
							{!! Form::select('expense_category_id', $expense_categories,
							!empty($temp_data->expense_category_id)?$temp_data->expense_category_id: null, ['class' =>
							'form-control select2', 'placeholder' => __('messages.please_select')]); !!}
							<span class="input-group-btn">
								<button type="button" class="btn
                                btn-default
                                bg-white btn-flat btn-modal"
									data-href="{{action('ExpenseCategoryController@create', ['quick_add' => true])}}"
									title="@lang('lang_v1.add_expense_category')"
									data-container=".expense_category_modal"><i
										class="fa fa-plus-circle text-primary fa-lg"></i></button>
							</span>
						</div>
					</div>
				</div>
				<div class="col-sm-4">
					<div class="form-group">
						{!! Form::label('ref_no', __('purchase.ref_no').':') !!}
						{!! Form::text('ref_no', !empty($temp_data->ref_no)?$temp_data->ref_no: $ref_no, ['class' =>
						'form-control']); !!}
					</div>
				</div>
				<div class="col-sm-4">
					<div class="form-group">
						{!! Form::label('transaction_date', __('messages.date') . ':*') !!}
						<div class="input-group">
							<span class="input-group-addon">
								<i class="fa fa-calendar"></i>
							</span>
							{!! Form::text('transaction_date',
							@format_datetime(!empty($temp_data->transaction_date)?$temp_data->transaction_date:'now'),
							['class' => 'form-control', 'readonly', 'required', 'id' => 'expense_transaction_date']);
							!!}
						</div>
					</div>
				</div>
				@php
					// dd($temp_data);
				@endphp
				<div class="col-sm-4">
					<div class="form-group">
						<input type="hidden" name="expense_for" value="{{$property_id}}">
						{!! Form::label('expense_for', __('expense.expense_for').':') !!}
						@show_tooltip(__('tooltip.expense_for'))
						{!! Form::text('demo' , $properties->name, ['disabled' => 'disabled' ,'class' => 'form-control', 'placeholder' => __('messages.please_select')]); !!}
					</div>
				</div>
				<div class="col-sm-4">
					<div class="form-group">
						{!! Form::label('contact_id', __('lang_v1.expense_for_contact').':') !!}
						{!! Form::select('contact_id', $contacts, null, ['class' => 'form-control select2',
						'placeholder' => __('messages.please_select')]); !!}
					</div>
				</div>
				<div class="col-sm-4">
					<div class="form-group">
						{!! Form::label('document', __('purchase.attach_document') . ':') !!}
						{!! Form::file('document', ['id' => 'upload_document']); !!}
						<p class="help-block">@lang('purchase.max_file_size', ['size' =>
							(config('constants.document_size_limit') / 1000000)])</p>
					</div>
				</div>
				<div class="col-sm-4">
					<div class="form-group">
						{!! Form::label('additional_notes', __('expense.expense_note') . ':') !!}
						{!! Form::textarea('additional_notes',
						!empty($temp_data->additional_notes)?$temp_data->additional_notes:null, ['class' =>
						'form-control', 'rows' => 3]); !!}
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
							{!! Form::select('tax_id', $taxes['tax_rates'],
							!empty($temp_data->tax_id)?$temp_data->tax_id:null, ['class' => 'form-control'],
							$taxes['attributes']); !!}

							<input type="hidden" name="tax_calculation_amount" id="tax_calculation_amount"
								value="{{!empty($temp_data->tax_calculation_amount)?$temp_data->tax_calculation_amount:0}}">
						</div>
					</div>
				</div>
				<div class="col-sm-4">
					<div class="form-group">
						{!! Form::label('final_total', __('sale.total_amount') . ':*') !!}
						{!! Form::text('final_total', !empty($temp_data->final_total)?$temp_data->final_total:null,
						['class' => 'form-control input_number', 'placeholder' => __('sale.total_amount'), 'required']);
						!!}
					</div>
				</div>
				<div class="col-sm-4">
					<div class="form-group">
						{!! Form::label('expense_account', __('sale.expense_account') . ':*') !!}
						{!! Form::select('expense_account', $expense_accounts, !empty($property_account_setting) ? $property_account_setting->expense_account_id : null, ['class' =>
						'form-control select2', 'placeholder' => __('lang_v1.please_select')]) !!}

					</div>
				</div>

			</div>
		</div>
	</div>
	@include('expense.recur_expense_form_part')
	<!--box end-->
	<div class="box box-solid">
		<div class="box-header">
			<h3 class="box-title">@lang('sale.add_payment')</h3>
		</div>
		<div class="box-body">
			<div class="row">
				<div class="col-md-12 payment_row">
					@include('expense.partials.payment_row_form', ['row_index' => 0, 'property' => true])
				</div>
			</div>
		</div>
	</div>
	<!--box end-->
	<div class="col-sm-12">
		<button type="submit" class="btn btn-primary pull-right">@lang('messages.save')</button>
	</div>
	{!! Form::close() !!}

	<div class="modal fade expense_category_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
	</div>
</section>
@endsection

@section('javascript')
<script>

	jQuery.validator.addMethod("greaterThanZero", function(value, element) {
        return (parseFloat(value) > 0);
    });
    $.validator.messages.greaterThanZero = 'Zero Values not accepted. Please correct';
    jQuery.validator.addClassRules("payment-amount", {
		required:true,  
        greaterThanZero: true
    });
  
    $('form#add_expense_form').validate({
        rules: {
        
        },
        messages: {
           
        },
	});

	


	$(document).ready(function(){
		$('#location_id').trigger('change');
		$('.payment_types_dropdown').trigger('change');
	})
	$('#location_id').change(function(){
		$.ajax({
			method: 'get',
			url: '/expenses/get-payment-method-by-location-id/'+$(this).val(),
			data: {  },
			success: function(result) {
				$('#method_0').empty().append(result)
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
	$(".expense_category_modal").on('hide.bs.modal', function(){
		$.ajax({
			method: 'get',
			url: '/expense-categories/get-drop-down',
			data: {  },
			contentType: 'html',
			success: function(result) {
				$('#expense_category_id').empty().append(result)
			},
		});
	});


	@if(auth()->user()->can('unfinished_form.expense'))
		setInterval(function(){ 
			$.ajax({
					method: 'POST',
					url: '{{action("TempController@saveAddExpenseTemp")}}',
					dataType: 'json',
					data: $('#add_expense_form').serialize(),
					success: function(data) {
					},
				});
		}, 10000);
		
		@if(!empty($temp_data))
		swal({
			title: "Do you want to load unsaved data?",
			icon: "info",
			buttons: {
				confirm: {
					text: "Yes",
					value: false,
					visible: true,
					className: "",
					closeModal: true
				},
				cancel: {
					text: "No",
					value: true,
					visible: true,
					className: "",
					closeModal: true,
				}
				
			},
			dangerMode: false,
		}).then((sure) => {
			if(sure){
				window.location.href = "{{action('TempController@clearData', ['type' => 'add_expense_data'])}}";
			} 
		});
		@endif
		@endif

		@if ($account_module)
		$('#expense_account').select2();
		@endif

		
		$('#method_0').prop('disabled', false);

		$('#final_total').change(function(){
			$('#amount_0').val($('#final_total').val());
			total = parseFloat($('#final_total').val());
			paid = parseFloat($('#amount_0').val());
			due = total - paid;
			if(due > 0){
				$('.controller_account_div').removeClass('hide')
			}else{
				$('.controller_account_div').addClass('hide')
			}
			$('#payment_due').text(__currency_trans_from_en(due, false, false));
		});
		$('#amount_0').change(function(){
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
</script>
@endsection