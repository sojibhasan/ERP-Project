<div class="modal-dialog" role="document" style="width: 50%;">
	<div class="modal-content">

		{!! Form::open(['url' => action('StockTransferRequestController@update', $transfer_request->id), 'method' => 'put', 'id' =>
		'edit_transfer_request_form' ]) !!}
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
					aria-hidden="true">&times;</span></button>
			<h4 class="modal-title">@lang( 'lang_v1.edit_request' )</h4>
		</div>

		<div class="modal-body">
			<div class="row">
				<div class="col-md-4 col-xs-6">
					<label for="request_location">@lang('lang_v1.request_location'):</label>
					{!! Form::select('request_location', $business_locations, $transfer_request->request_location, ['class' => 'form-control
					select2',
					'placeholder' =>__('lang_v1.please_select'), 'style' => 'width: 100%', 'id' => 'add_request_location']) !!}
				</div>
				<div class="col-md-4 col-xs-6">
					<label for="request_to_location">@lang('lang_v1.request_to_location'):</label>
					{!! Form::select('request_to_location', $business_locations, $transfer_request->request_to_location, ['class' => 'form-control
					select2',
					'placeholder' =>__('lang_v1.please_select'), 'style' => 'width: 100%', 'id' => 'add_request_to_location']) !!}
				</div>
				
				<div class="col-md-4">
					<div class="form-group">
						{!! Form::label('category_id',__('lang_v1.category').':') !!}
						{!! Form::select('category_id', $categories, $transfer_request->category_id, ['placeholder' =>
						__('lang_v1.please_select'), 'class' => 'form-control select2', 'style' => 'width:100%', 'id' =>
						'add_category_id']); !!}
					</div>
				</div>
				<div class="col-md-4">
					<div class="form-group">
						{!! Form::label('sub_category_id',__('lang_v1.sub_category').':') !!}
						{!! Form::select('sub_category_id', $sub_categories, $transfer_request->sub_category_id, ['placeholder' =>
						__('lang_v1.please_select'), 'class' => 'form-control select2', 'style' => 'width:100%', 'id' =>
						'add_sub_category_id']); !!}
					</div>
				</div>
				<div class="col-md-4">
					<div class="form-group">
						{!! Form::label('product_id',__('lang_v1.products').':') !!}
						{!! Form::select('product_id', $products, $transfer_request->product_id, ['placeholder' =>
						__('lang_v1.please_select'), 'class' => 'form-control select2', 'style' => 'width:100%', 'id' =>
						'add_product_id']); !!}
					</div>
				</div>
				<div class="col-md-4">
					<div class="form-group">
						{!! Form::label('qty', __('lang_v1.qty') . ':') !!}
						{!! Form::text('qty', $transfer_request->qty , ['placeholder' => __('lang_v1.qty'), 'class' =>
						'form-control', 'id' => 'add_qty']); !!}
					</div>
				</div>
				<div class="col-md-4">
					<div class="form-group">
						{!! Form::label('delivery_need_on', __('lang_v1.delivery_need_on') . ':') !!}
						{!! Form::text('delivery_need_on', null , ['placeholder' => __('lang_v1.delivery_need_on'), 'class' =>
						'form-control', 'id' => 'add_delivery_need_on', 'readonly']); !!}
					</div>
				</div>
                <div class="col-md-3">
                    <div class="form-group">
                        {!! Form::label('status',__('lang_v1.status').':') !!}
                        {!! Form::select('status', ['requested' => __('lang_v1.status'), 'issued' => __('lang_v1.issued'),
                        'transit' => __('lang_v1.transit'), 'received' => __('lang_v1.received')],  $transfer_request->status, ['placeholder' =>
                        __('report.all'), 'class' => 'form-control select2', 'style' => 'width:100%', 'id' =>
                        'status']); !!}
                    </div>
                </div>

			</div>
		</div>

		<div class="modal-footer">
			<button type="submit" class="btn btn-primary">@lang( 'messages.save' )</button>
			<button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
		</div>

		{!! Form::close() !!}

	</div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->
<script>
	$('#add_category_id').change(function(){
        var cat = $('#add_category_id').val();
        var sub_cat = $('#add_sub_category_id').val();
        $.ajax({
            method: 'POST',
            url: '/products/get_sub_categories',
            dataType: 'html',
            data: { cat_id: cat },
            success: function(result) {
                if (result) {
                    $('#add_sub_category_id').html(result);
                }
            },
        });
    });
	$('#add_sub_category_id').change(function(){
        var cat = $('#add_category_id').val();
        var sub_cat = $('#add_sub_category_id').val();
        $.ajax({
            method: 'POST',
            url: '/products/get_product_category_wise',
            dataType: 'html',
            data: { cat_id: cat , sub_cat_id: sub_cat },
            success: function(result) {
                if (result) {
                    $('#add_product_id').html(result);
                }
            },
        });
	});
	$('.select2').select2();
  $('#add_delivery_need_on').datepicker('setDate', "{{\Carbon::parse($transfer_request->delivery_need_on)->format('d-m-Y')}}");
</script>