<div class="modal-dialog" role="document" style="width: 55%;">
    <div class="modal-content">

        {!! Form::open(['url' => action('\Modules\Ran\Http\Controllers\WorkOrderController@store'), 'method' => 'post',
        'id' =>
        'add_work_order_form' ]) !!}

        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                    aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">@lang( 'ran::lang.add_work_order' )</h4>
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
                            {!! Form::label('work_order_no', __( 'ran::lang.work_order_no' ) . ':*') !!}
                            {!! Form::text('work_order_no', $work_order_id, ['class' => 'form-control work_order_no',
                            'required', 'readonly',
                            'placeholder' => __(
                            'ran::lang.work_order_no' ) ]); !!}
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Form::label('order_delivery_date', __( 'ran::lang.order_delivery_date' ) . ':*') !!}
                            {!! Form::text('order_delivery_date', null, ['class' => 'form-control order_delivery_date',
                            'required', 'readonly',
                            'placeholder' => __(
                            'ran::lang.order_delivery_date' ) ]); !!}
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
                    <div class="clearfix"></div>
                    <hr>

                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Form::label('customer_order_no', __( 'ran::lang.customer_order_no' ) . ':*') !!}
                            {!! Form::select('customer_order_no', [], null , ['class' => 'form-control select2
                            ', 'required',
                            'placeholder' => __(
                            'ran::lang.please_select' ), 'style' => 'width: 100%;']); !!}
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Form::label('customer_id', __( 'ran::lang.customer' ) . ':*') !!}
                            {!! Form::select('customer_id', $customers, null , ['class' => 'form-control select2
                            ', 'required',
                            'placeholder' => __(
                            'ran::lang.please_select' ), 'style' => 'width: 100%;']); !!}
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
                            {!! Form::label('qty', __( 'ran::lang.qty' ) . ':*') !!}
                            {!! Form::text('qty', null, ['class' => 'form-control qty input_number', 'required',
                            'placeholder' => __(
                            'ran::lang.qty' ) ]); !!}
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Form::label('required_qty', __( 'ran::lang.required_qty' ) . ':*') !!}
                            {!! Form::text('required_qty', null, ['class' => 'form-control required_qty input_number', 'required',
                            'placeholder' => __(
                            'ran::lang.required_qty' ) ]); !!}
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Form::label('weight_showroom_product', __( 'ran::lang.weight_showroom_product' ) . ':*')
                            !!}
                            {!! Form::text('weight_showroom_product', null, ['class' => 'form-control
                            weight_showroom_product', 'required',
                            'placeholder' => __(
                            'ran::lang.weight_showroom_product' ) ]); !!}
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Form::label('required_unit_weight', __( 'ran::lang.required_unit_weight' ) . ':*') !!}
                            {!! Form::text('required_unit_weight', null, ['class' => 'form-control input_number
                            required_unit_weight', 'required',
                            'placeholder' => __(
                            'ran::lang.required_unit_weight' ) ]); !!}
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Form::label('gold_qty', __( 'ran::lang.gold_qty_for_goldsmith' ) . ':*') !!}
                            {!! Form::text('gold_qty', null, ['class' => 'form-control gold_qty input_number', 'required',

                            'placeholder' => __(
                            'ran::lang.gold_qty_for_goldsmith' ) ]); !!}
                        </div>
                    </div>
                    <input type="hidden" name="index" id="index" value="0">
                    <div class="col-md-12">
                        <button type="button"
                            class="add_item_row btn btn-primary pull-right">@lang('ran::lang.add')</button>
                    </div>

                    <div class="clearfix"></div>
                    <hr>
                    <table class="table table-bordered table-striped" id="add_item_work_order_table"
                        style="width: 100%;">
                        <thead>
                            <tr>
                                <th>@lang('ran::lang.item')</th>
                                <th>@lang('ran::lang.qty')</th>
                                <th>@lang('ran::lang.required_qty')</th>
                                <th>@lang('ran::lang.current_weight')</th>
                                <th>@lang('ran::lang.unit_weight')</th>
                                <th>@lang('ran::lang.gold_qty_for_goldsmith')</th>
                                <th class="notexport">@lang('messages.action')</th>

                            </tr>
                        </thead>
                        <tbody>
                          
                        </tbody>
                    </table>

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
    $('.order_delivery_date').datepicker();

    $(document).on('click', '.add_item_row', function(){
        let item_id = $('#item_id').val();
        let item_name = $('#item_id :selected').text();
        let qty = $('#qty').val();
        let required_qty = $('#required_qty').val();
        let weight_showroom_product = $('#weight_showroom_product').val();
        let required_unit_weight = $('#required_unit_weight').val();
        let gold_qty = $('#gold_qty').val();
        let index = parseInt($('#index').val());
        
        $('#add_item_work_order_table tbody').append(
            `<tr>
                <td><input type="hidden" value="${item_id}" name="list[${index}][item_id]">${item_name}</td>
                <td><input type="hidden" value="${qty}" name="list[${index}][qty]">${qty}</td>
                <td><input type="hidden" value="${required_qty}" name="list[${index}][required_qty]">${required_qty}</td>
                <td><input type="hidden" value="${weight_showroom_product}" name="list[${index}][weight_showroom_product]">${weight_showroom_product}</td>
                <td><input type="hidden" value="${required_unit_weight}" name="list[${index}][required_unit_weight]">${required_unit_weight}</td>
                <td><input type="hidden" value="${gold_qty}" name="list[${index}][gold_qty]">${gold_qty}</td>
                <td><button type="button" class="btn btn-xs btn-danger remove_row"><i class="fa fa-times"></i></button></td>
            </tr>

            `
        );
        $('#index').val(index+1);
    })

    $(document).on('click', '.remove_row',function(){
        $(this).parent().parent().remove();
    });
    </script>