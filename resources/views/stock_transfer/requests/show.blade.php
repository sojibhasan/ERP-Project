<div class="modal-dialog" role="document" style="width: 50%;">
	<div class="modal-content">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
					aria-hidden="true">&times;</span></button>
			<h4 class="modal-title">@lang( 'lang_v1.view_request' )</h4>
		</div>

		<div class="modal-body">
			<div class="row">
				<div class="col-md-4 col-xs-6">
					<label for="request_location">@lang('lang_v1.request_location'):</label> {{$stock_transfer_requests->rl}}
				</div>
				<div class="col-md-4 col-xs-6">
					<label for="request_to_location">@lang('lang_v1.request_to_location'):</label> {{$stock_transfer_requests->rtl}}
				</div>
				
				<div class="col-md-4">
					<div class="form-group">
						{!! Form::label('category_id',__('lang_v1.category').':') !!} {{$stock_transfer_requests->cat_name}}
					</div>
				</div>
				<div class="col-md-4">
					<div class="form-group">
						{!! Form::label('sub_category_id',__('lang_v1.sub_category').':') !!}  {{$stock_transfer_requests->sub_cat_name}}
					</div>
				</div>
				<div class="col-md-4">
					<div class="form-group">
						{!! Form::label('product_id',__('lang_v1.products').':') !!} {{$stock_transfer_requests->product_name}}
					</div>
				</div>
				<div class="col-md-4">
					<div class="form-group">
						{!! Form::label('qty', __('lang_v1.qty') . ':') !!} {{$stock_transfer_requests->qty}}
					</div>
				</div>
				<div class="col-md-4">
					<div class="form-group">
						{!! Form::label('delivery_need_on', __('lang_v1.delivery_need_on') . ':') !!}  {{\Carbon::parse($stock_transfer_requests->delivery_need_on)->format('d-m-Y')}}
					</div>
				</div>

				<div class="col-md-4">
					<div class="form-group">
						{!! Form::label('delivery_need_on', __('lang_v1.status') . ':') !!}   {{ucfirst($stock_transfer_requests->status)}}
					</div>
				</div>


			</div>
		</div>

		<div class="modal-footer">
			<button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
		</div>

		{!! Form::close() !!}

	</div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->
<script>
	
</script>