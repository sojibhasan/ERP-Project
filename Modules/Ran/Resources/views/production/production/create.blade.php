<div class="modal-dialog" role="document" style="width: 55%;">
    <div class="modal-content">

        {!! Form::open(['url' => action('\Modules\Ran\Http\Controllers\ProductionController@store'), 'method' => 'post',
        'id' =>
        'add_productions_form' ]) !!}

        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                    aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">@lang( 'ran::lang.add_production' )</h4>
        </div>

        <div class="modal-body">
            <div class="col-md-12">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Form::label('reference_no', __( 'ran::lang.reference_no' ) . ':*') !!}
                            {!! Form::text('reference_no', $reference_code, ['class' => 'form-control reference_no',
                            'required', 'readonly',
                            'placeholder' => __(
                            'ran::lang.reference_no' ) ]); !!}
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Form::label('location_id', __( 'ran::lang.busines_locations' ) . ':*') !!}
                            {!! Form::select('location_id', $business_locations, null , ['class' => 'form-control
                            select2
                            fuel_tank_location', 'required',
                            'placeholder' => __(
                            'ran::lang.please_select' ), 'style' => 'width: 100%;']); !!}
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Form::label('gold_smith_id', __( 'ran::lang.gold_smith' ) . ':*') !!}
                            {!! Form::select('gold_smith_id', $gold_smiths, null , ['class' => 'form-control select2
                            fuel_tank_location', 'required',
                            'placeholder' => __(
                            'ran::lang.please_select' ), 'style' => 'width: 100%;']); !!}
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Form::label('category_type', __( 'ran::lang.category_type' ) . ':*') !!}
                            {!! Form::select('category_type', $category_types, null , ['class' => 'form-control select2
                            fuel_tank_location', 'required',
                            'placeholder' => __(
                            'ran::lang.please_select' ), 'style' => 'width: 100%;']); !!}
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Form::label('product_qty', __( 'ran::lang.product_qty' ) . ':*') !!}
                            {!! Form::text('product_qty', null, ['class' => 'form-control product_qty', 'required',
                            'placeholder' => __(
                            'ran::lang.product_qty' ) ]); !!}
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Form::label('receiving_store', __( 'ran::lang.receiving_store' ) . ':*') !!}
                            {!! Form::select('receiving_store', $receiving_stores, null , ['class' => 'form-control
                            select2
                            fuel_tank_location', 'required',
                            'placeholder' => __(
                            'ran::lang.please_select' ), 'style' => 'width: 100%;']); !!}
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Form::label('gold_grade_id', __( 'ran::lang.gold_grade' ) . ':*') !!}
                            {!! Form::select('gold_grade_id', $gold_grades, null , ['class' => 'form-control select2
                            fuel_tank_location', 'required',
                            'placeholder' => __(
                            'ran::lang.please_select' ), 'style' => 'width: 100%;']); !!}
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Form::label('wastage_per_8_g', __( 'ran::lang.wastage_per_8_gin' ) . ':*') !!}
                            {!! Form::text('wastage_per_8_g', null, ['class' => 'form-control wastage_per_8_g',
                            'required',
                            'placeholder' => __(
                            'ran::lang.wastage_per_8_g' ) ]); !!}
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Form::label('total_product_gold_weight', __( 'ran::lang.total_product_gold_weight' ) .
                            ':*') !!}
                            {!! Form::text('total_product_gold_weight', null, ['class' => 'form-control
                            total_product_gold_weight', 'required',
                            'placeholder' => __(
                            'ran::lang.total_product_gold_weight' ) ]); !!}
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Form::label('total_stone_other_weight', __( 'ran::lang.total_stone_other_weight' ) .
                            ':*') !!}
                            {!! Form::text('total_stone_other_weight', null, ['class' => 'form-control
                            total_stone_other_weight', 'required',
                            'placeholder' => __(
                            'ran::lang.total_stone_other_weight' ) ]); !!}
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Form::label('wastage_calculation', __( 'ran::lang.wastage_calculation' ) . ':*') !!}
                            {!! Form::text('wastage_calculation', null, ['class' => 'form-control wastage_calculation',
                            'required',
                            'placeholder' => __(
                            'ran::lang.wastage_calculation' ) ]); !!}
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Form::label('total_gold_wastage', __( 'ran::lang.total_gold_wastage' ) . ':*') !!}
                            {!! Form::text('total_gold_wastage', null, ['class' => 'form-control total_gold_wastage',
                            'required',
                            'placeholder' => __(
                            'ran::lang.total_gold_wastage' ) ]); !!}
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Form::label('total_goldsmith_in_g', __( 'ran::lang.total_goldsmith_in_g' ) . ':*') !!}
                            {!! Form::text('total_goldsmith_in_g', null, ['class' => 'form-control
                            total_goldsmith_in_g', 'required',
                            'placeholder' => __(
                            'ran::lang.total_goldsmith_in_g' ) ]); !!}
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Form::label('labour_cost', __( 'ran::lang.labour_cost' ) . ':*') !!}
                            {!! Form::text('labour_cost', null, ['class' => 'form-control labour_cost', 'required',
                            'placeholder' => __(
                            'ran::lang.labour_cost' ) ]); !!}
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Form::label('labour_cost_total', __( 'ran::lang.labour_cost_total' ) . ':*') !!}
                            {!! Form::text('labour_cost_total', null, ['class' => 'form-control labour_cost_total',
                            'required',
                            'placeholder' => __(
                            'ran::lang.labour_cost_total' ) ]); !!}
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Form::label('design_cost', __( 'ran::lang.design_cost' ) . ':*') !!}
                            {!! Form::text('design_cost', null, ['class' => 'form-control design_cost', 'required',
                            'placeholder' => __(
                            'ran::lang.design_cost' ) ]); !!}
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Form::label('stone_cost', __( 'ran::lang.stone_cost' ) . ':*') !!}
                            {!! Form::text('stone_cost', null, ['class' => 'form-control stone_cost', 'required',
                            'placeholder' => __(
                            'ran::lang.stone_cost' ) ]); !!}
                        </div>
                    </div>

                    <div class="col-md-12">
                        <button class="btn btn-primary btn-sm add_other_cost_btn pull-left"
                            type="button">@lang('ran::lang.add_other_cost')</button>
                        <input type="hidden" name="index" value="0" id="index">
                    </div>

                    <div class="col-md-12">
                        <div class="other_cost_div hide">
                            <div class="row">
                                <div class="col-md-5">
                                    <div class="form-group">
                                        {!! Form::label('cost_name', __( 'ran::lang.cost_name' ) . ':*') !!}

                                    </div>
                                </div>
                                <div class="col-md-5">
                                    <div class="form-group">
                                        {!! Form::label('cost', __( 'ran::lang.cost' ) . ':*') !!}

                                    </div>
                                </div>
                                <div class="col-md-2">

                                </div>
                            </div>
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
        $('.add_other_cost_btn').click(function(){
            $('.other_cost_div').removeClass('hide');
            let index = parseInt($('#index').val());
            $('.other_cost_div').append(`
            <div class="row">
                <div class="col-md-5">
                    <div class="form-group">
                        <input class="form-control
                        cost_name" required="" placeholder="Cost Name" name="other_cost[${index}][cost_name]" type="text">
                    </div>
                </div>
                <div class="col-md-5">
                    <div class="form-group">
                        <input class="form-control cost" required="" placeholder="Cost" name="other_cost[${index}][cost]" type="text">
                    </div>
                </div>
                <div class="col-md-2">
                    <button class="remove_row btn btn-xs btn-danger pull-right" type="button" style="margin-top: 6px;"><i class="fa fa-times"></i></button>
                </div>
            </div>
            `);

            $('#index').val(index+1);
        });

        $(document).on('click', '.remove_row', function(){
            $(this).parent().parent().remove();
        });


        function cal_wastage(){
            var wastage= $("input[name=wastage_per_8_g]").val();
            var other= $("input[name=total_stone_other_weight]").val();
            var total= $("input[name=total_product_gold_weight]").val();

            if(wastage>0  && other>0  && total>0){
                var result2 = (parseFloat(total)-parseFloat(other));
                $("input[name=total_gold_wastage]").val(Math.round(result2*1000)/1000);

                var result = ((parseFloat(total)-parseFloat(other))/8)*wastage;
                result3  = parseFloat(result2)+parseFloat(Math.round(result*1000)/1000);
                $("input[name=total_goldsmith_in_g]").val(Math.round(result3*1000)/1000);
                var wastage= $("input[name=wastage_calculation]").val(Math.round(result*1000)/1000);
            }
        }

        $(document).on('keyup', '#wastage_per_8_g, #total_product_gold_weight, #total_stone_other_weight', function(){
            cal_wastage();
        })

        $(document).on('change', '#gold_grade_id', function(){
           $.ajax({
               method: 'get',
               url: "{{action('\Modules\Ran\Http\Controllers\GoldPriceController@getGoldPriceByGrade')}}"+$(this).val(),
               data: {  },
               success: function(result) {
                   
               },
           });
        })
    </script>