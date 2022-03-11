<div class="modal-dialog" role="document" style="width: 55%;">
    <div class="modal-content">

        {!! Form::open(['url' => action('VariationTransferController@update', $variation_transfer->id), 'method' => 'post', 'id' =>
        'variation_transfer_add_form', 'class' => 'form-horizontal' ]) !!}
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                    aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">@lang('lang_v1.variation_transfer')</h4>
        </div>

        <div class="modal-body">
            <div class="col-md-12">
                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('date',__('lang_v1.date') . ':*', ['class' => '']) !!}
                        {!! Form::text('date', null, ['class' => 'form-control date', 'required', 'placeholder' =>
                        __('lang_v1.date')]); !!}
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('from_location', __( 'lang_v1.from_location' ) . ':*') !!}
                        {!! Form::select('from_location', $business_locations, $variation_transfer->from_location,
                        ['placeholder'
                        => __( 'messages.please_select' ), 'required', 'class' => 'form-control select2', 'style' => 'width: 100%;']); !!}
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('from_store', __( 'lang_v1.from_store' ) . ':*') !!}
                        {!! Form::select('from_store', $stores, $variation_transfer->from_store,
                        ['placeholder'
                        => __( 'messages.please_select' ), 'required', 'class' => 'form-control select2', 'style' => 'width: 100%;']); !!}
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('to_location', __( 'lang_v1.to_location' ) . ':*') !!}
                        {!! Form::select('to_location', $business_locations, $variation_transfer->to_location,
                        ['placeholder'
                        => __( 'messages.please_select' ), 'required', 'class' => 'form-control select2', 'style' => 'width: 100%;']); !!}
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('to_store', __( 'lang_v1.to_store' ) . ':*') !!}
                        {!! Form::select('to_store', $stores, $variation_transfer->to_store,
                        ['placeholder'
                        => __( 'messages.please_select' ), 'required', 'class' => 'form-control select2', 'style' => 'width: 100%;']); !!}
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('category_id', __( 'lang_v1.category' ) . ':*') !!}
                        {!! Form::select('category_id', $categories, $variation_transfer->category_id,
                        ['placeholder'
                        => __( 'messages.please_select' ), 'required', 'class' => 'form-control select2', 'style' => 'width: 100%;']); !!}
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('sub_category_id', __( 'lang_v1.sub_category' ) . ':*') !!}
                        {!! Form::select('sub_category_id', $sub_categories, $variation_transfer->sub_category_id,
                        ['placeholder'
                        => __( 'messages.please_select' ), 'required', 'class' => 'form-control select2', 'style' => 'width: 100%;']); !!}
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('from_variation_id', __( 'lang_v1.product_from' ) . ':*') !!}
                        {!! Form::select('from_variation_id', $variations, $variation_transfer->from_variation_id,
                        ['placeholder'
                        => __( 'messages.please_select' ), 'required', 'class' => 'form-control select2', 'style' => 'width: 100%;']); !!}
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('to_variation_id', __( 'lang_v1.product_to' ) . ':*') !!}
                        {!! Form::select('to_variation_id', $variations, $variation_transfer->to_variation_id,
                        ['placeholder'
                        => __( 'messages.please_select' ), 'required', 'class' => 'form-control select2', 'style' => 'width: 100%;']); !!}
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('qty', __( 'lang_v1.qty' ) . ':*') !!}
                        {!! Form::text('qty', @format_quantity($variation_transfer->qty), ['class' => 'form-control', 'placeholder' => __( 'lang_v1.qty'), 'id'
                        => 'qty']); !!}
                      </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('unit_cost', __( 'lang_v1.unit_cost' ) . ':*') !!}
                        {!! Form::text('unit_cost', @num_format($variation_transfer->unit_cost), ['class' => 'form-control', 'placeholder' => __( 'lang_v1.unit_cost'), 'id'
                        => 'unit_cost']); !!}
                      </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('total_cost', __( 'lang_v1.total_cost' ) . ':*') !!}
                        {!! Form::text('total_cost', @num_format($variation_transfer->total_cost), ['class' => 'form-control', 'placeholder' => __( 'lang_v1.total_cost'), 'id'
                        => 'total_cost']); !!}
                      </div>
                </div>
            </div>
        </div>
        <div class="clearfix"></div>

        <div class="modal-footer">
            <button type="submit" class="btn btn-primary">@lang('messages.save')</button>
            <button type="button" class="btn btn-default" data-dismiss="modal">@lang('messages.close')</button>
        </div>

        {!! Form::close() !!}

    </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->


<script>
    $('.date').datepicker('setDate', new Date());
    $('.select2').select2();
    $('#from_location').change(function(){
		let check_store_not = null;
		$.ajax({
			method: 'get',
			url: '/stock-transfer/get_transfer_store_id/'+$(this).val(),
			data: { check_store_not: check_store_not},
			success: function(result) {
				
				$('#from_store').empty();
				$('#from_store').append(`<option value="">Please Select</option>`);
				$.each(result, function(i, location) {
					$('#from_store').append(`<option value= "`+location.id+`">`+location.name+`</option>`);
				});
			},
		});
	});
    $('#to_location').change(function(){
		let check_store_not = null;
		$.ajax({
			method: 'get',
			url: '/stock-transfer/get_transfer_store_id/'+$(this).val(),
			data: { check_store_not: check_store_not},
			success: function(result) {
				
				$('#to_store').empty();
				$('#to_store').append(`<option value="">Please Select</option>`);
				$.each(result, function(i, location) {
					$('#to_store').append(`<option value= "`+location.id+`">`+location.name+`</option>`);
				});
			},
		});
	});

    $('#category_id, #sub_category_id').change(function(){
        var this_id = $(this).attr('id');
        var cat = $('#category_id').val();
        var sub_cat = $('#sub_category_id').val();
        $.ajax({
            method: 'POST',
            url: '/products/get_sub_categories',
            dataType: 'html',
            data: { cat_id: cat },
            success: function(result) {
            if (result) {
                console.log(this_id);
                if(this_id !== 'sub_category_id'){
                    $('#sub_category_id').html(result);
                }
            }
            },
        });
        $.ajax({
            method: 'GET',
            url: '/variation-transfer/get-variation-by-category',
            dataType: 'html',
            data: { cat_id: cat , sub_cat_id: sub_cat },
            success: function(result) {
            if (result) {
                $('#from_variation_id').html(result);
                $('#to_variation_id').html(result);
            }
            },
        });
    });

    $('#qty, #unit_cost').change(function () {
        var qty = __read_number($('#qty'));
        var unit_cost = __read_number($('#unit_cost'));

        if(qty > 0 && unit_cost > 0 ){
            total_cost = qty * unit_cost;
            __write_number($('#total_cost'), total_cost);
        }
    })
</script>