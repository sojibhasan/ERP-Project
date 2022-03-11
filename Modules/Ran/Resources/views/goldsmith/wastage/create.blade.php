<div class="modal-dialog" role="document" style="width: 55%;">
    <div class="modal-content">

        {!! Form::open(['url' => action('\Modules\Ran\Http\Controllers\WastageController@store'), 'method' => 'post',
        'id' =>
        'add_wastage_form' ]) !!}

        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                    aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">@lang( 'ran::lang.add_wastage' )</h4>
        </div>

        <div class="modal-body">
            <div class="col-md-12">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Form::label('date_and_time', __( 'ran::lang.date_and_time' ) . ':*') !!}
                            {!! Form::text('date_and_time', date('Y-m-d H:i:s'), ['class' => 'form-control date_and_time',
                            'required', 'readonly',
                            'placeholder' => __(
                            'ran::lang.date_and_time' ) ]); !!}
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Form::label('wastage_form_no', __( 'ran::lang.wastage_form_no' ) . ':*') !!}
                            {!! Form::text('wastage_form_no', $wastage_form_no, ['class' => 'form-control wastage_form_no',
                            'required', 'readonly',
                            'placeholder' => __(
                            'ran::lang.wastage_form_no' ) ]); !!}
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Form::label('location_id', __( 'ran::lang.busines_locations' ) . ':*') !!}
                            {!! Form::select('location_id', $business_locations, null , ['class' => 'form-control
                            select2', 'required',
                            'placeholder' => __(
                            'ran::lang.please_select' ), 'style' => 'width: 100%;']); !!}
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Form::label('goldsmith_id', __( 'ran::lang.goldsmith' ) . ':*') !!}
                            {!! Form::select('goldsmith_id', $goldsmiths, null , ['class' => 'form-control
                            select2', 'required',
                            'placeholder' => __(
                            'ran::lang.please_select' ), 'style' => 'width: 100%;']); !!}
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Form::label('wastage', __( 'ran::lang.wastage' ) . ':*') !!}
                            {!! Form::text('wastage', null, ['class' => 'form-control wastage input_number',
                            'required', 
                            'placeholder' => __(
                                'ran::lang.wastage' ) ]); !!}
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Form::label('category_id', __( 'ran::lang.category' ) . ':*') !!}
                            {!! Form::select('category_id', $categories, null , ['class' => 'form-control
                            select2', 'required',
                            'placeholder' => __(
                            'ran::lang.please_select' ), 'style' => 'width: 100%;']); !!}
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Form::label('sub_category_id', __( 'ran::lang.sub_category' ) . ':*') !!}
                            {!! Form::select('sub_category_id', [], null , ['class' => 'form-control
                            select2', 'required',
                            'placeholder' => __(
                            'ran::lang.please_select' ), 'style' => 'width: 100%;']); !!}
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
        $('#category_id').change(function(){
            var cat = $('#category_id').val();
            $.ajax({
                method: 'POST',
                url: '/products/get_sub_categories',
                dataType: 'html',
                data: { cat_id: cat },
                success: function(result) {
                if (result) {
                    $('#sub_category_id').html(result);
                }
                },
            });
        });
       
    </script>