<div class="col-md-12">
	<div class="box box-solid payment_row">
		@if($removable)
			<div class="box-header">
              <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool remove_payment_row"><i class="fa fa-times fa-2x"></i></button>
              </div>
            </div>
        @endif

        @if(!empty($payment_line['id']))
        	{!! Form::hidden("payment[$row_index][payment_id]", $payment_line['id']); !!}
        @endif

		<div class="box-body" >
			@include('purchase.partials.payment_row_form_bulk', ['row_index' => $row_index, 'payment_line' => $payment_lines[0]])
        </div>
        
	</div>
</div>