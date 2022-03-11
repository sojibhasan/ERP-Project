<div class="modal-dialog" role="document" style="width: 55%;">
    <div class="modal-content">

        {!! Form::open(['url' => action('\Modules\Ran\Http\Controllers\ReceiveWorkOrderController@store'), 'method' => 'post',
        'id' =>
        'add_work_order_form' ]) !!}

        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                    aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">@lang( 'ran::lang.add_receive_work_order' )</h4>
        </div>

        <div class="modal-body">
            <div class="col-md-12">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Form::label('location_id', __( 'ran::lang.business_location' ) . ':*') !!}
                            {!! Form::select('location_id', $business_locations, null , ['class' => 'form-control
                            select2
                            fuel_tank_location', 'required',
                            'placeholder' => __(
                            'ran::lang.please_select' ), 'style' => 'width: 100%;']); !!}
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Form::label('goldsmith_id', __( 'ran::lang.goldsmith' ) . ':*') !!}
                            {!! Form::select('goldsmith_id', $goldsmiths, null , ['class' => 'form-control select2
                            fuel_tank_location', 'required',
                            'placeholder' => __(
                            'ran::lang.please_select' ), 'style' => 'width: 100%;']); !!}
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Form::label('date_and_time', __( 'ran::lang.date_and_time' ) . ':*') !!}
                            {!! Form::text('date_and_time', date('Y-m-d H:i:s'), ['class' => 'form-control
                            date_and_time',
                            'required', 'readonly',
                            'placeholder' => __(
                            'ran::lang.date_and_time' ) ]); !!}
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Form::label('receive_work_order_no', __( 'ran::lang.receive_work_order_no' ) . ':*') !!}
                            {!! Form::text('receive_work_order_no', $work_order_id, ['class' => 'form-control receive_work_order_no',
                            'required', 'readonly',
                            'placeholder' => __(
                            'ran::lang.receive_work_order_no' ) ]); !!}
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Form::label('receiving_store_id', __( 'ran::lang.receiving_store' ) . ':*') !!}
                            {!! Form::select('receiving_store_id', $stores, null, ['class' => 'form-control receiving_store',
                            'required',
                            'placeholder' => __(
                            'ran::lang.please_select' ) ]); !!}
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Form::label('work_order_id', __( 'ran::lang.work_order' ) . ':*') !!}
                            {!! Form::select('work_order_id', $work_orders, null, ['class' => 'form-control work_order',
                            'required',
                            'placeholder' => __(
                            'ran::lang.please_select' ) ]); !!}
                        </div>
                    </div>
                  
                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Form::label('item_id', __( 'ran::lang.item' ) . ':*') !!}
                            {!! Form::select('item_id', $products, null , ['class' => 'form-control select2
                            ', 'required',
                            'placeholder' => __(
                            'ran::lang.please_select' ), 'style' => 'width: 100%;']); !!}
                        </div>
                    </div>


                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Form::label('gold_grade', __( 'ran::lang.gold_grade' ) . ':*') !!}
                            {!! Form::text('gold_grade', null, ['class' => 'form-control gold_grade', 'required', 'readonly',
                            'placeholder' => __(
                            'ran::lang.gold_grade' ) ]); !!}
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Form::label('item_weight', __( 'ran::lang.item_weight' ) . ':*') !!}
                            {!! Form::text('item_weight', null, ['class' => 'form-control item_weight', 'required', 'readonly',
                            'placeholder' => __(
                            'ran::lang.item_weight' ) ]); !!}
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Form::label('required_item_weight', __( 'ran::lang.required_item_weight' ) . ':*') !!}
                            {!! Form::text('required_item_weight', null, ['class' => 'form-control required_item_weight', 'required',
                            'placeholder' => __(
                            'ran::lang.required_item_weight' ) ]); !!}
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Form::label('required_qty', __( 'ran::lang.required_qty' ) . ':*') !!}
                            {!! Form::text('required_qty', null, ['class' => 'form-control required_qty', 'required', 'readonly',
                            'placeholder' => __(
                            'ran::lang.required_qty' ) ]); !!}
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Form::label('received_qty', __( 'ran::lang.received_qty' ) . ':*') !!}
                            {!! Form::text('received_qty', null, ['class' => 'form-control received_qty', 'required',
                            'placeholder' => __(
                            'ran::lang.received_qty' ) ]); !!}
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Form::label('category_id', __( 'ran::lang.category' ) . ':*') !!}
                            {!! Form::select('category_id', $categories, null , ['class' => 'form-control select2
                            ', 'required',
                            'placeholder' => __(
                            'ran::lang.please_select' ), 'style' => 'width: 100%;']); !!}
                        </div>
                    </div>


                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Form::label('received_weight_for_all_items', __( 'ran::lang.received_weight_for_all_items' ) . ':*') !!}
                            {!! Form::text('received_weight_for_all_items', null, ['class' => 'form-control received_weight_for_all_items', 'required',
                            'placeholder' => __(
                            'ran::lang.received_weight_for_all_items' ) ]); !!}
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Form::label('wastage_per_8g', __( 'ran::lang.wastage_per_8g' ) . ':*') !!}
                            {!! Form::text('wastage_per_8g', null, ['class' => 'form-control wastage_per_8g', 'required',
                            'placeholder' => __(
                            'ran::lang.wastage_per_8g' ) ]); !!}
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Form::label('total_wastage', __( 'ran::lang.total_wastage' ) . ':*') !!}
                            {!! Form::text('total_wastage', null, ['class' => 'form-control total_wastage', 'required',
                            'placeholder' => __(
                            'ran::lang.total_wastage' ) ]); !!}
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Form::label('total_stone_weight', __( 'ran::lang.total_stone_weight' ) . ':*') !!}
                            {!! Form::text('total_stone_weight', null, ['class' => 'form-control total_stone_weight', 'required',
                            'placeholder' => __(
                            'ran::lang.total_stone_weight' ) ]); !!}
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Form::label('labour_cost', __( 'ran::lang.labour_cost_total' ) . ':*') !!}
                            {!! Form::text('labour_cost', null, ['class' => 'form-control labour_cost', 'required',
                            'placeholder' => __(
                            'ran::lang.labour_cost' ) ]); !!}
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Form::label('item_details', __( 'ran::lang.item_details' ) . ':') !!}
                            {!! Form::textarea('item_details', null, ['class' => 'form-control item_details',
                            'required', 'rows'=>' 3',
                            'placeholder' => __(
                            'ran::lang.item_details' ) ]); !!}
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Form::label('note', __( 'ran::lang.note' ) . ':') !!}
                            {!! Form::textarea('note', null, ['class' => 'form-control note',
                            'required', 'rows'=>' 3',
                            'placeholder' => __(
                            'ran::lang.note' ) ]); !!}
                        </div>
                    </div>
                </div>
            </div>
            <div class="clearfix"></div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary add_fuel_tank_btn">@lang( 'messages.save' )</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
            </div>

            {!! Form::close() !!}
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->

<script>
    $(document).on('change', '#work_order_id', function(){
        $.ajax({
            method: 'get',
            url: "{{action('\Modules\Ran\Http\Controllers\WorkOrderController@getWorkOrderItems')}}",
            data: { work_order_id : $(this).val() },
            contentType: 'html',
            success: function(result) {
                $('#item_id').empty().append(result);
            },
        });
    })
    $(document).on('change', '#item_id', function(){
        $.ajax({
            method: 'get',
            url: "{{action('\Modules\Ran\Http\Controllers\WorkOrderController@getWorkOrderItemDetails')}}",
            data: { work_order_id : $('#work_order_id').val(), item_id: $(this).val() },
            success: function(result) {
                $('#required_qty').val(result.required_qty);
                $('#required_item_weight').val(result.required_unit_weight);
            },
        });
    })
</script>