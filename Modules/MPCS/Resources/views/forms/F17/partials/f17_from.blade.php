<!-- Main content -->
<section class="content">
    @component('components.widget', ['class' => 'box-primary', 'title' => __(
    'mpcs::lang.f17_from')])

    <div class="col-md-3">
        <div class="form-group">
            {!! Form::label('type', __('mpcs::lang.date') . ':') !!}
            {!! Form::text('f17_date', null, ['class' => 'form-control', 'id' => 'f17_date', 'readonly']) !!}
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            {!! Form::label('type', __('mpcs::lang.F17_from_no') . ':') !!}
            {!! Form::text('F17_from_no', $F17_from_no, ['class' => 'form-control f17_filter', 'id' => 'F17_from_no', 'readonly']) !!}
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            {!! Form::label('category_id', __('product.category') . ':') !!}
            {!! Form::select('category_id', $categories, null, ['class' => 'form-control f17_filter select2', 'style' =>
            'width:100%', 'id' => 'product_list_filter_category_id', 'placeholder' => __('lang_v1.all')]); !!}
        </div>
    </div>

    <div class="col-md-3">
        <div class="form-group">
            {!! Form::label('unit_id', __('product.unit') . ':') !!}
            {!! Form::select('unit_id', $units, null, ['class' => 'form-control f17_filter select2', 'style' =>
            'width:100%', 'id' => 'product_list_filter_unit_id', 'placeholder' => __('lang_v1.all')]); !!}
        </div>
    </div>
  
    <div class="col-md-3">
        <div class="form-group">
            {!! Form::label('brand_id', __('product.brand') . ':') !!}
            {!! Form::select('brand_id', $brands, null, ['class' => 'form-control f17_filter select2', 'style' =>
            'width:100%', 'id' => 'product_list_filter_brand_id', 'placeholder' => __('lang_v1.all')]); !!}
        </div>
    </div>
    <div class="col-md-3" id="location_filter">
        <div class="form-group">
            {!! Form::label('location_id', __('purchase.business_location') . ':') !!}
            {!! Form::select('location_id', $business_locations, null, ['class' => 'form-control f17_filter select2',
            'id' => 'location_id',
            'style' => 'width:100%', 'placeholder' => __('lang_v1.all')]); !!}
        </div>
    </div>

    <div class="col-sm-3">
        <div class="form-group">
            {!! Form::label('store_id', __('lang_v1.store_id').':') !!}
            <select name="store_id" id="store_id" class="form-control select2" required>
                <option value="">@lang('messages.please_select')</option>
            </select>
        </div>
    </div>

    @endcomponent
    @component('components.widget', ['class' => 'box-primary', 'title' => __(
    'mpcs::lang.f17_from')])
    @slot('tool')
    <div class="col-md-3 pull-right">
        <button type="submit" name="submit_type" id="f17_save" value="save" class="btn btn-primary pull-right"
            style="margin-left: 20px">@lang('mpcs::lang.save')</button>
    </div>
    @endslot
    <div class="table-responsive">
        <table class="table table-bordered table-striped" id="form_17_table" style="width:100%;">
            <thead>
                <tr>
                    <th>@lang('mpcs::lang.index')</th>
                    <th>@lang('mpcs::lang.product_code')</th>
                    <th>@lang('mpcs::lang.product')</th>
                    <th>@lang('mpcs::lang.current_stock')</th>
                    <th>@lang('mpcs::lang.unit_price')</th>
                    <th>@lang('mpcs::lang.select_mode')</th>
                    <th>@lang('mpcs::lang.new_price')</th>
                    <th>@lang('mpcs::lang.unit_price_difference')</th>
                    <th>@lang('mpcs::lang.price_changed_loss')</th>
                    <th>@lang('mpcs::lang.price_changed_gain')</th>
                    <th>@lang('mpcs::lang.signature')</th>
                    <th>@lang('mpcs::lang.page_no')</th>

                </tr>
            </thead>
        </table>
    </div>
    @endcomponent

    <div class="modal fade fuel_tank_modal" role="dialog" aria-labelledby="gridSystemModalLabel">
    </div>

</section>
<!-- /.content -->