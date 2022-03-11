<!-- Main content -->
<section class="content">
    @component('components.widget', ['class' => 'box-primary', 'title' => __(
        'mpcs::lang.list_f17_from')])
    
        <div class="col-md-3">
            <div class="form-group">
                {!! Form::label('type', __('mpcs::lang.date') . ':') !!}
                {!! Form::text('list_f17_date_range', null, ['class' => 'form-control list_f17_filter', 'id' => 'list_f17_date_range', 'readonly']) !!}
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                {!! Form::label('type', __('mpcs::lang.from_no') . ':') !!}
                {!! Form::select('from_no_filter', $forms_nos, null, ['class' => 'form-control list_f17_filter select2', 'style' => 'width: 100%', 'id' => 'from_no_filter']) !!}
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                {!! Form::label('category_id', __('product.category') . ':') !!}
                {!! Form::select('category_id', $categories, null, ['class' => 'form-control list_f17_filter select2', 'style' =>
                'width:100%', 'id' => 'list_f17_category_id', 'placeholder' => __('lang_v1.all')]); !!}
            </div>
        </div>
    
        <div class="col-md-3">
            <div class="form-group">
                {!! Form::label('unit_id', __('product.unit') . ':') !!}
                {!! Form::select('unit_id', $units, null, ['class' => 'form-control list_f17_filter select2', 'style' =>
                'width:100%', 'id' => 'list_f17_unit_id', 'placeholder' => __('lang_v1.all')]); !!}
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                {!! Form::label('brand_id', __('product.brand') . ':') !!}
                {!! Form::select('brand_id', $brands, null, ['class' => 'form-control list_f17_filter select2', 'style' =>
                'width:100%', 'id' => 'list_f17_brand_id', 'placeholder' => __('lang_v1.all')]); !!}
            </div>
        </div>
        <div class="col-md-3" id="location_filter">
            <div class="form-group">
                {!! Form::label('location_id', __('purchase.business_location') . ':') !!}
                {!! Form::select('list_form_f17_location_id', $business_locations, null, ['class' => 'form-control list_f17_filter select2',
                'id' => 'list_form_f17_location_id',
                'style' => 'width:100%', 'placeholder' => __('lang_v1.all')]); !!}
            </div>
        </div>
    
        <div class="col-sm-3">
            <div class="form-group">
                {!! Form::label('store_id', __('lang_v1.store_id').':') !!}
                <select name="store_id" id="list_store_id" class="form-control list_f17_filter select2" style="width: 100%;">
                    <option value="">@lang('messages.please_select')</option>
                </select>
            </div>
        </div>
    
        @endcomponent

    @component('components.widget', ['class' => 'box-primary', 'title' => __(
    'mpcs::lang.list_f17_from')])
    <div class="table-responsive">
        <table class="table table-bordered table-striped" id="list_form_f17_table" style="width:100%;">
            <thead>
                <tr>
                    <th>@lang('mpcs::lang.action')</th>
                    <th>@lang('mpcs::lang.date_and_time')</th>
                    <th>@lang('mpcs::lang.form_no')</th>
                    <th>@lang('mpcs::lang.location')</th>
                    <th>@lang('mpcs::lang.category')</th>
                    <th>@lang('mpcs::lang.sub_category')</th>
                    <th>@lang('mpcs::lang.store')</th>
                    <th>@lang('mpcs::lang.select_mode')</th>
                    <th>@lang('mpcs::lang.total_price_change_loss')</th>
                    <th>@lang('mpcs::lang.total_price_change_gain')</th>
                    <th>@lang('mpcs::lang.user')</th>
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