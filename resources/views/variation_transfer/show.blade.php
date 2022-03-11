<div class="modal-dialog" role="document" style="width: 55%;">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                    aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">@lang('lang_v1.variation_transfer')</h4>
        </div>

        <div class="modal-body">
            <div class="col-md-12">
                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('date',__('lang_v1.date') . ':', ['class' => '']) !!} {{@format_date($variation_transfer->date)}}
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('from_location', __( 'lang_v1.from_location' ) . ':') !!} {{$business_locations[$variation_transfer->from_location]}}
                      
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('from_store', __( 'lang_v1.from_store' ) . ':') !!} {{$stores[$variation_transfer->from_store]}}
                    </div>
                </div>
                <div class="clearfix"></div>
                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('to_location', __( 'lang_v1.to_location' ) . ':') !!} {{$business_locations[$variation_transfer->to_location]}}
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('to_store', __( 'lang_v1.to_store' ) . ':') !!} {{$stores[$variation_transfer->to_store]}}
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('category_id', __( 'lang_v1.category' ) . ':') !!} {{$categories[$variation_transfer->category_id]}}
                    </div>
                </div>
                <div class="clearfix"></div>
                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('sub_category_id', __( 'lang_v1.sub_category' ) . ':') !!} {{$sub_categories[$variation_transfer->sub_category_id]}}
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('from_variation_id', __( 'lang_v1.product_from' ) . ':') !!} {{$variations[$variation_transfer->from_variation_id]}}
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('to_variation_id', __( 'lang_v1.product_to' ) . ':') !!} {{$variations[$variation_transfer->to_variation_id]}}
                    </div>
                </div>
                <div class="clearfix"></div>
                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('qty', __( 'lang_v1.qty' ) . ':') !!} {{@format_quantity($variation_transfer->qty)}}
                      </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('unit_cost', __( 'lang_v1.unit_cost' ) . ':') !!} {{@num_format($variation_transfer->unit_cost)}}
                      </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('total_cost', __( 'lang_v1.total_cost' ) . ':') !!} {{@num_format($variation_transfer->total_cost)}}
                      </div>
                </div>
            </div>
        </div>
        <div class="clearfix"></div>

        <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">@lang('messages.close')</button>
        </div>


    </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->


<script>
    
</script>