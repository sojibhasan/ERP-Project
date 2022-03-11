<div class="col-md-12">
	<div class="box box-solid payment_row" data-row_id="{{ $row_index}}">
		@if($removable)
			<div class="box-header">
              <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool remove_payment_row"><i class="fa fa-times fa-2x"></i></button>
              </div>
            </div>
        @endif

        @if(!empty($payment->id))
        	{!! Form::hidden("payment[$row_index][payment_id]", $payment->id); !!}
        @endif

		<div class="box-body" >
			@include('fleet::route_operations.partials.payment_row_form_edit')
		</div>
	</div>
</div>