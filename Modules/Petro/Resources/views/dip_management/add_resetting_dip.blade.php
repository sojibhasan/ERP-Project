<div class="modal-dialog" role="document" style="width: 70%;">

    <div class="modal-content">



        {!! Form::open(['url' => action('\Modules\Petro\Http\Controllers\DipManagementController@saveResettingDip'),

        'method' =>

        'post',

        'id' =>

        'dip_resetting_form' ]) !!}



        <div class="modal-header">

            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span

                    aria-hidden="true">&times;</span></button>

            <h4 class="modal-title">@lang( 'petro::lang.add_resetting_dip' ) </h4>

        </div>



        <div class="modal-body">
            <div class="col-md-12">

                <div class="row">

                    <div class="col-md-4">

                        <div class="form-group">

                            {!! Form::label('meter_reset_form_no', __( 'petro::lang.dip_resetting_no' ) . ':*') !!}

                            {!! Form::text('meter_reset_form_no', $meter_reset_form_no, ['class' => 'form-control meter_reset_form_no',

                            'required', 'readonly',

                            'placeholder' => __(

                            'petro::lang.meter_reset_form_no' ) ]); !!}

                        </div>

                    </div>



                    <div class="col-md-4">

                        <div class="form-group">

                            {!! Form::label('date_and_time', __( 'petro::lang.date' ) . ':*') !!}

                            {!! Form::text('date_and_time', null, ['class' => 'form-control date_and_time', 'required', 'readonly',

                            'placeholder' => __(

                            'petro::lang.date_and_time' ) ]); !!}

                        </div>

                    </div>



                    <div class="col-md-4">

                        <div class="form-group">

                            {!! Form::label('location_id', __( 'petro::lang.location' ) . ':*') !!}

                            {!! Form::select('location_id', $business_locations, null , ['class' => 'form-control

                            select2

                            fuel_tank_location', 'required', 'id' => 'location_id',

                            'placeholder' => __(

                            'petro::lang.please_select' ), 'style' => 'width: 100%;']); !!}

                        </div>

                    </div>



                    <div class="col-md-4">

                        <div class="form-group">

                            {!! Form::label('tank_id', __('petro::lang.tanks') . ':') !!}

                            {!! Form::select('tank_id', $tanks, null, ['class' => 'form-control select2', 'placeholder'

                            => __('petro::lang.please_select'), 'id' => 'add_reset_tank_id', 'style' => 'width:100%']); !!}

                        </div>

                    </div>





                    <div class="col-md-4">

                        <div class="form-group">

                            {!! Form::label('product_name', __( 'petro::lang.product_name' ) . ':*') !!}

                            {!! Form::text('product_name', null, ['class' => 'form-control product_name', 'required',

                            'readonly',

                            'placeholder' => __(

                            'petro::lang.product_name' ) ]); !!}

                        </div>

                    </div>





                    <div class="col-md-4">

                        <div class="form-group">

                            {!! Form::label('current_qty', __( 'petro::lang.current_qty' ) . ':*') !!}

                            {!! Form::text('current_qty', null, ['class' => 'form-control current_qty input_number',

                            'required', 'readonly',

                            'placeholder' => __(

                            'petro::lang.current_qty' ) ]); !!}

                        </div>

                    </div>

                    <div class="col-md-4">

                        <div class="form-group">

                            {!! Form::label('current_dip_difference', __( 'petro::lang.current_dip_difference' ) . ':*')

                            !!}

                            {!! Form::text('current_dip_difference', null, ['class' => 'form-control input_number

                            current_dip_difference', 'required', "id"=>"current_dip_difference",

                            'placeholder' => __('petro::lang.current_dip_difference'), "disabled"=>"disabled" ]); !!}

                        </div>

                    </div>

                    <div class="col-md-4">

                        <div class="form-group">

                            {!! Form::label('reset_new_dip', __( 'petro::lang.reset_new_dip' ) . ':*') !!}

                            {!! Form::text('reset_new_dip', null, ['class' => 'form-control reset_new_dip input_number', 'required',

                            'placeholder' => __(

                            'petro::lang.reset_new_dip' ) ]); !!}

                        </div>

                    </div>

                    {{-- 

                        * @ModifiedBy Afes Oktavianus

                        * @DateBy 06-06-2021

                        * @Task 3341

                    --}}

                    <div class="col-md-4">

                        <div class="form-group">

                            {!! Form::label('qty_to_adjust', __( 'petro::lang.qty_to_adjust' ) . ':*') !!}

                            {!! Form::text('qty_to_adjust', null, ['class' => 'form-control qty_to_adjust input_number', 'required',

                            'placeholder' => __(

                            'petro::lang.qty_to_adjust' ), 'disabled'=> "disabled" ]); !!}

                            {!! Form::hidden("quantity_presicion", $quantity_presicion, ["id"=>"quantity_presicion"]) !!}

                            

                        </div>

                    </div>

                    <div class="col-md-4">

                        {!! Form::label('type', __( 'petro::lang.type' ) . ':*') !!}

                        <select name="adjustment_type" id="adjustment_type" class="form-control select2" required disabled="disabled">

                            <option value="">@lang('lang_v1.please_select')</option>

                            <option value="increase">@lang('sale.increase')</option>

                            <option value="decrease">@lang('sale.decrease')</option>

                        </select>

                    </div>                    

                    <div class="col-md-4">

                        <div class="form-group">

                                {!! Form::label('inventory_adjustment_account', __('lang_v1.inventory_adjustment_account') . ':') !!}

                                {!! Form::select('inventory_adjustment_account', [], null, ['class' => 'form-control select2', 'placeholder' => __('messages.please_select'), 'required', 'style' => 'width: 100%;']); !!}

                        </div>

                    </div>

                    <div class="col-md-4">

                        <div class="form-group">

                            {!! Form::label('reason', __( 'petro::lang.reason' ) . ':') !!}

                            {!! Form::textarea('reason', null, ['class' => 'form-control reason',  'rows' =>

                            4,
                            'required',
                            'placeholder' => __(

                            'petro::lang.reason' ) ]); !!}

                        </div>

                    </div>



                </div>

            </div>

            <div class="clearfix"></div>

            <div class="modal-footer">

                <button type="submit" class="btn btn-primary add_dip_resetting_btn">@lang( 'messages.save' )</button>

                <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>

            </div>



            {!! Form::close() !!}

        </div><!-- /.modal-content -->

    </div><!-- /.modal-dialog -->



    <script>

        $('#date_and_time').datepicker("setDate", new Date());

        // $('#date_and_time').datetimepicker({

        //         format: moment_date_format + ' ' + moment_time_format,
        //         ignoreReadonly: true,

        //     });

        $('#transaction_date').datepicker("setDate", new Date());

        $('#add_reset_tank_id').select2();

        $('#location_id').select2();

        $('#inventory_adjustment_account').select2();

        $('#location_id option:eq(1)').attr('selected', true).trigger('change');



        $(document).on('change', '#addjustment_type',function () {

            if($(this).val() === 'increase'){

                type = 'increase';

            }

            if($(this).val() === 'decrease'){

                type = 'decrease';

            }



            $.ajax({

                method: 'get',

                url: "/stock-adjustments/inventory-adjustment-account",

                data: { type },

                contentType: 'html',

                success: function(result) {

                    $('#inventory_adjustment_account').empty().append(result);

                },

            });

        })

    </script>