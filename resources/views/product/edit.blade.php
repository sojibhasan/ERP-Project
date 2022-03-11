@extends('layouts.app') @section('title', __('product.edit_product')) @section('content')
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>@lang('product.edit_product')</h1>
    <!-- <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Level</a></li>
        <li class="active">Here</li>
    </ol> -->
</section>
<style>
    .select2-results__option[aria-selected="true"] {
        display: none;
    }
</style>
<!-- Main content -->
<section class="content">
    {!! Form::open(['url' => action('ProductController@update' , [$product->id] ), 'method' => 'PUT', 'id' => 'product_add_form', 'class' => 'product_form', 'files' => true ]) !!}
    <input type="hidden" id="product_id" value="{{ $product->id }}" />
    @component('components.widget', ['class' => 'box-primary'])
    <div class="row">
        <div class="col-sm-4">
            <div class="form-group">
                {!! Form::label('name', __('product.product_name') . ':*') !!} {!! Form::text('name', $product->name, ['class' => 'form-control', 'required', 'placeholder' => __('product.product_name')]); !!}
            </div>
        </div>
        <div class="col-sm-4 @if(!(session('business.enable_category') && session('business.enable_sub_category'))) hide @endif">
            <div class="form-group">
                {!! Form::label('sku', __('product.sku') . ':*') !!} @show_tooltip(__('tooltip.sku')) {!! Form::text('sku', $product->sku, ['class' => 'form-control', 'placeholder' => __('product.sku'), 'required', 'readonly']); !!}
            </div>
        </div>
        <div class="col-sm-4">
            <div class="form-group">
                {!! Form::label('barcode_type', __('product.barcode_type') . ':*') !!} {!! Form::select('barcode_type', $barcode_types, $product->barcode_type, ['placeholder' => __('messages.please_select'), 'class' => 'form-control
                select2', 'required']); !!}
            </div>
        </div>
        <div class="clearfix"></div>
        <div class="col-sm-4">
            <div class="form-group">
                {!! Form::label('unit_id', __('product.unit') . ':*') !!}
                <div class="input-group">
                    {!! Form::select('unit_id', $units, $product->unit_id, ['placeholder' => __('messages.please_select'), 'class' => 'form-control select2', 'required']); !!}
                    <span class="input-group-btn">
                        <button type="button" @if(!auth()->
                            user()->can('unit.create')) disabled @endif class="btn btn-default bg-white btn-flat quick_add_unit btn-modal" data-href="{{action('UnitController@create', ['quick_add' => true])}}" title="@lang('unit.add_unit')"
                            data-container=".view_modal"><i class="fa fa-plus-circle text-primary fa-lg"></i>
                        </button>
                    </span>
                </div>
            </div>
        </div>
        <div class="col-sm-4 multiple_units_checkbox @if(empty($product->multiple_units)) hide @endif">
            <div class="form-group">
                <br />
                <label>
                    {!! Form::checkbox('multiple_units', $product->multiple_units, false, ['class' => 'input-icheck', 'id' => 'multiple_units']); !!}
                    <strong>@lang('product.multiple_units')</strong>
                </label>
            </div>
        </div>
        <div class="col-sm-4 @if(!session('business.enable_sub_units')) hide @endif">
            <div class="form-group">
                {!! Form::label('sub_unit_ids', __('lang_v1.related_sub_units') . ':') !!} @show_tooltip(__('lang_v1.sub_units_tooltip'))
                <select name="sub_unit_ids[]" class="form-control select2" multiple id="sub_unit_ids">
                    @foreach($sub_units as $sub_unit_id => $sub_unit_value)
                    <option value="{{$sub_unit_id}}" @if(is_array($product->sub_unit_ids) &&in_array($sub_unit_id, $product->sub_unit_ids)) selected @endif >{{$sub_unit_value['name']}}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="clearfix"></div>
        <div class="col-sm-4 @if(!session('business.enable_brand')) hide @endif">
            <div class="form-group">
                {!! Form::label('brand_id', __('product.brand') . ':') !!}
                <div class="input-group">
                    {!! Form::select('brand_id', $brands, $product->brand_id, ['placeholder' => __('messages.please_select'), 'class' => 'form-control select2']); !!}
                    <span class="input-group-btn">
                        <button type="button" @if(!auth()->
                            user()->can('brand.create')) disabled @endif class="btn btn-default bg-white btn-flat btn-modal" data-href="{{action('BrandController@create', ['quick_add' => true])}}" title="@lang('brand.add_brand')"
                            data-container=".view_modal"><i class="fa fa-plus-circle text-primary fa-lg"></i>
                        </button>
                    </span>
                </div>
            </div>
        </div>
        <div class="col-sm-4 @if(!session('business.enable_category')) hide @endif">
            <div class="form-group">
                {!! Form::label('category_id', __('product.category') . ':') !!} {!! Form::select('category_id', $categories, $product->category_id, ['placeholder' => __('messages.please_select'), 'class' => 'form-control select2']); !!}
            </div>
        </div>
        <div class="col-sm-4 @if(!(session('business.enable_category') && session('business.enable_sub_category'))) hide @endif">
            <div class="form-group">
                {!! Form::label('sub_category_id', __('product.sub_category') . ':') !!} {!! Form::select('sub_category_id', $sub_categories, $product->sub_category_id, ['placeholder' => __('messages.please_select'), 'class' =>
                'form-control select2']); !!}
            </div>
        </div>
        <div class="clearfix"></div>
        <div class="col-sm-4">
            <div class="form-group">
                {!! Form::label('product_locations', __('business.business_locations') . ':') !!} @show_tooltip(__('lang_v1.product_location_help')) {!! Form::select('product_locations[]', $business_locations,
                $product->product_locations->pluck('id'), ['class' => 'form-control select2', 'multiple', 'id' => 'product_locations']); !!}
            </div>
        </div>
        @if($is_manged_stock_enable == 1)
        <div class="col-sm-4">
            <div class="form-group">
                <br />
                <label> {!! Form::checkbox('enable_stock', 1, $product->enable_stock, ['class' => 'input-icheck', 'id' => 'enable_stock','disabled'=>!$is_manged_stock_enable]); !!} <strong>@lang('product.manage_stock')</strong> </label>
                @show_tooltip(__('tooltip.enable_stock'))
                <p class="help-block"><i>@lang('product.enable_stock_help')</i></p>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="form-group">
                <br />
                <label> {!! Form::checkbox('is_service', 1, $product->is_service, ['class' => 'input-icheck', 'id' => 'is_service']); !!} <strong>{{__('Is Service')}}</strong> </label>
            </div>
        </div>
        @endif
        <div class="col-sm-4 equal-column @if($product->enable_stock == 0) hide @endif" id="raw_material_div">
            <div class="form-group">
                <br />
                {!! Form::label('stock_type', __('product.stock_type'), []) !!} {!! Form::select('stock_type', $accounts, $product->stock_type, ['class' => 'form-control select2', 'id' => 'stock_type', 'required', 'placeholder' =>
                __('product.please_select')]) !!}
            </div>
        </div>
        <div class="col-sm-4" id="alert_quantity_div" @if(!$product->
            enable_stock) style="display:none" @endif>
            <div class="form-group">
                {!! Form::label('alert_quantity', __('product.alert_quantity') . ':') !!} @show_tooltip(__('tooltip.alert_quantity')) {!! Form::number('alert_quantity', $product->alert_quantity, ['class' => 'form-control', 'placeholder' =>
                __('product.alert_quantity') , 'min' => '0']); !!}
            </div>
        </div>
        <div class="col-sm-4">
            <div class="form-group">
                {!! Form::label('warranty_id', __('lang_v1.warranty') . ':') !!} {!! Form::select('warranty_id', $warranties, $product->warranty_id, ['class' => 'form-control select2', 'placeholder' => __('messages.please_select')]); !!}
            </div>
        </div>
        <div class="clearfix"></div>
        <div class="col-sm-8">
            <div class="form-group">
                {!! Form::label('product_description', __('lang_v1.product_description') . ':') !!} {!! Form::textarea('product_description', $product->product_description, ['class' => 'form-control']); !!}
            </div>
        </div>
        <div class="col-sm-4">
            {!! Form::label('', __('product.stock_account_name') . ':') !!} <br />
            @lang('product.finished_goods_account') <br />
            @lang('product.raw_material_account') <br />
            @lang('product.stock_account') <br />
            <div class="form-group">
                {!! Form::label('added_date', __('lang_v1.product_added_date') . ':*') !!}
                <div class="input-group">
                    <span class="input-group-addon">
                        <i class="fa fa-calendar"></i>
                    </span>
                    {!! Form::text('date', date('m/d/Y', strtotime($product->date)), ['class' => 'form-control required input_number', 'id' => 'product_added_date']); !!}
                </div>
            </div>
        </div>
        <div class="clearfix"></div>
        <div class="col-sm-4">
            <div class="form-group">
                {!! Form::label('image', __('lang_v1.product_image') . ':') !!} {!! Form::file('image', ['id' => 'upload_image', 'accept' => 'image/*']); !!}
                <small>
                    <p class="help-block">
                        @lang('purchase.max_file_size', ['size' => (config('constants.document_size_limit') / 1000000)]). @lang('lang_v1.aspect_ratio_should_be_1_1') @if(!empty($product->image)) <br />
                        @lang('lang_v1.previous_image_will_be_replaced') @endif
                    </p>
                </small>
            </div>
        </div>
    </div>
    @endcomponent @component('components.widget', ['class' => 'box-primary'])
    <div class="row">
        @if(session('business.enable_product_expiry')) @if(session('business.expiry_type') == 'add_expiry') @php $expiry_period = 12; $hide = true; @endphp @else @php $expiry_period = null; $hide = false; @endphp @endif
        <div class="col-sm-4 @if($hide) hide @endif">
            <div class="form-group">
                <div class="multi-input">
                    @php $disabled = false; $disabled_period = false; if( empty($product->expiry_period_type) || empty($product->enable_stock) ){ $disabled = true; } if( empty($product->enable_stock) ){ $disabled_period = true; } @endphp
                    {!! Form::label('expiry_period', __('product.expires_in') . ':') !!}<br />
                    {!! Form::text('expiry_period', @num_format($product->expiry_period), ['class' => 'form-control pull-left input_number', 'placeholder' => __('product.expiry_period'), 'style' => 'width:60%;', 'disabled' => $disabled]);
                    !!} {!! Form::select('expiry_period_type', ['months'=>__('product.months'), 'days'=>__('product.days'), '' =>__('product.not_applicable') ], $product->expiry_period_type, ['class' => 'form-control select2 pull-left',
                    'style' => 'width:40%;', 'id' => 'expiry_period_type', 'disabled' => $disabled_period]); !!}
                </div>
            </div>
        </div>
        @endif
        <div class="col-sm-4">
            <div class="checkbox">
                <label> {!! Form::checkbox('enable_sr_no', 1, $product->enable_sr_no, ['class' => 'input-icheck']); !!} <strong>@lang('lang_v1.enable_imei_or_sr_no')</strong> </label>
                @show_tooltip(__('lang_v1.tooltip_sr_no'))
            </div>
        </div>
        <div class="col-sm-4">
            <div class="form-group">
                <br />
                <label> {!! Form::checkbox('not_for_selling', 1, $product->not_for_selling, ['class' => 'input-icheck']); !!} <strong>@lang('lang_v1.not_for_selling')</strong> </label> @show_tooltip(__('lang_v1.tooltip_not_for_selling'))
            </div>
        </div>
        <div class="col-sm-4">
            <div class="form-group">
                <div class="checkbox">
                    <label> {!! Form::checkbox('show_avai_qty_in_qr_catalogue', 1, $product->show_avai_qty_in_qr_catalogue, [ 'class' => 'input-icheck']); !!} <strong> {{ __( 'lang_v1.show_avai_qty_in_qr_catalogue' ) }} </strong> </label>
                </div>
            </div>
        </div>
        <div class="clearfix"></div>
        <div class="col-sm-4">
            <div class="form-group">
                <div class="checkbox">
                    <label> {!! Form::checkbox('show_in_catalogue_page', 1, $product->show_in_catalogue_page, [ 'class' => 'input-icheck']); !!} <strong> {{ __( 'lang_v1.show_in_catalogue_page' ) }} </strong> </label>
                </div>
            </div>
        </div>
        <div class="clearfix"></div>
        <!-- Rack, Row & position number -->
        @if(session('business.enable_racks') || session('business.enable_row') || session('business.enable_position'))
        <div class="col-md-12">
            <h4>@lang('lang_v1.rack_details'): @show_tooltip(__('lang_v1.tooltip_rack_details'))</h4>
        </div>
        @foreach($business_locations as $id => $location)
        <div class="col-sm-3">
            <div class="form-group">
                {!! Form::label('rack_' . $id, $location . ':') !!} @if(!empty($rack_details[$id])) @if(session('business.enable_racks')) {!! Form::text('product_racks_update[' . $id . '][rack]', $rack_details[$id]['rack'], ['class' =>
                'form-control', 'id' => 'rack_' . $id]); !!} @endif @if(session('business.enable_row')) {!! Form::text('product_racks_update[' . $id . '][row]', $rack_details[$id]['row'], ['class' => 'form-control']); !!} @endif
                @if(session('business.enable_position')) {!! Form::text('product_racks_update[' . $id . '][position]', $rack_details[$id]['position'], ['class' => 'form-control']); !!} @endif @else {!! Form::text('product_racks[' . $id .
                '][rack]', null, ['class' => 'form-control', 'id' => 'rack_' . $id, 'placeholder' => __('lang_v1.rack')]); !!} {!! Form::text('product_racks[' . $id . '][row]', null, ['class' => 'form-control', 'placeholder' =>
                __('lang_v1.row')]); !!} {!! Form::text('product_racks[' . $id . '][position]', null, ['class' => 'form-control', 'placeholder' => __('lang_v1.position')]); !!} @endif
            </div>
        </div>
        @endforeach @endif
        <div class="col-sm-4">
            <div class="form-group">
                {!! Form::label('weight', __('lang_v1.weight') . ':') !!} {!! Form::text('weight', $product->weight, ['class' => 'form-control', 'placeholder' => __('lang_v1.weight')]); !!}
            </div>
        </div>
        <div class="clearfix"></div>
        <!--custom fields-->
        <div class="col-sm-3">
            <div class="form-group">
                {!! Form::label('product_custom_field1', __('lang_v1.product_custom_field1') . ':') !!} {!! Form::text('product_custom_field1', $product->product_custom_field1, ['class' => 'form-control', 'placeholder' =>
                __('lang_v1.product_custom_field1')]); !!}
            </div>
        </div>
        <div class="col-sm-3">
            <div class="form-group">
                {!! Form::label('product_custom_field2', __('lang_v1.product_custom_field2') . ':') !!} {!! Form::text('product_custom_field2', $product->product_custom_field2, ['class' => 'form-control', 'placeholder' =>
                __('lang_v1.product_custom_field2')]); !!}
            </div>
        </div>
        <div class="col-sm-3">
            <div class="form-group">
                {!! Form::label('product_custom_field3', __('lang_v1.product_custom_field3') . ':') !!} {!! Form::text('product_custom_field3', $product->product_custom_field3, ['class' => 'form-control', 'placeholder' =>
                __('lang_v1.product_custom_field3')]); !!}
            </div>
        </div>
        <div class="col-sm-3">
            <div class="form-group">
                {!! Form::label('product_custom_field4', __('lang_v1.product_custom_field4') . ':') !!} {!! Form::text('product_custom_field4', $product->product_custom_field4, ['class' => 'form-control', 'placeholder' =>
                __('lang_v1.product_custom_field4')]); !!}
            </div>
        </div>
        <!--custom fields-->
        @include('layouts.partials.module_form_part')
    </div>
    @endcomponent @if(auth()->user()->can('product.price_section')) @component('components.widget', ['class' => 'box-primary']) @else @component('components.widget', ['class' => 'box-primary hide']) @endif
    <div class="row">
        <div class="col-sm-4 @if(!session('business.enable_price_tax')) hide @endif">
            <div class="form-group">
                {!! Form::label('tax', __('product.applicable_tax') . ':') !!} {!! Form::select('tax', $taxes, $product->tax, ['placeholder' => __('messages.please_select'), 'class' => 'form-control select2'], $tax_attributes); !!}
            </div>
        </div>
        <div class="col-sm-4 @if(!session('business.enable_price_tax')) hide @endif">
            <div class="form-group">
                {!! Form::label('tax_type', __('product.selling_price_tax_type') . ':*') !!} {!! Form::select('tax_type',['inclusive' => __('product.inclusive'), 'exclusive' => __('product.exclusive')], $product->tax_type, ['class' =>
                'form-control select2', 'required']); !!}
            </div>
        </div>
        <div class="clearfix"></div>
        <div class="col-sm-4">
            <div class="form-group">
                {!! Form::label('type', __('product.product_type') . ':*') !!} @show_tooltip(__('tooltip.product_type')) {!! Form::select('type', $product_types, $product->type, ['class' => 'form-control select2', 'required', 'data-action'
                => 'edit', 'data-product_id' => $product->id ]); !!}
            </div>
        </div>
        <div class="form-group col-sm-12" id="product_form_part"></div>
        <input type="hidden" id="variation_counter" value="0" />
        <input type="hidden" id="default_profit_percent" value="{{ $default_profit_percent }}" />
    </div>
    @endcomponent
    <div class="row">
        <input type="hidden" name="submit_type" id="submit_type" />
        <div class="col-sm-12">
            <div class="text-center">
                <div class="btn-group">
                    @if($selling_price_group_count)
                    <button type="submit" value="submit_n_add_selling_prices" class="btn btn-warning submit_product_form">@lang('lang_v1.save_n_add_selling_price_group_prices')</button>
                    @endif @php $cat = App\Category::find($product->category_id); if(!empty($cat)){ $cat_name = $cat->name; }else{ $cat_name = ''; } @endphp @if($cat_name != "Fuel")
                    <button type="submit" @if(empty($product->
                        enable_stock)) disabled="true" @endif id="opening_stock_button" value="update_n_edit_opening_stock" class="btn bg-purple submit_product_form">@lang('lang_v1.update_n_edit_opening_stock')
                    </button>
                    @endif
                    <button type="submit" value="save_n_add_another" class="btn bg-maroon submit_product_form">@lang('lang_v1.update_n_add_another')</button>
                    <button type="submit" value="submit" class="btn btn-primary submit_product_form">@lang('messages.update')</button>
                </div>
            </div>
        </div>
    </div>
    {!! Form::close() !!}
</section>
<!-- /.content -->
@endsection @section('javascript')
<script src="{{ asset('js/product.js?v=' . $asset_v) }}"></script>
<script>
    var body = document.getElementsByTagName("body")[0];
    body.className += " sidebar-collapse";
    $(".multiple_units").hide();
    $("#unit_id").change(function () {
        $("#multiple_units").iCheck("uncheck");
        $(".multiple_units").hide();
        addUnits();
        if ($(this).val()) {
            console.log("asdf");
            $.ajax({
                method: "get",
                url: "/get_sub_units",
                data: { unit_id: $(this).val() },
                success: function (result) {
                    count = parseInt(result.count);
                    if (count > 1) {
                        $(".multiple_units_checkbox").removeClass("hide");
                    } else {
                        $(".multiple_units_checkbox").addClass("hide");
                    }
                },
            });
        } else {
            $(".multiple_units_checkbox").addClass("hide");
        }
    });
    $("#multiple_units").on("ifChanged", function () {
        addUnits();
    });
    $("#tax_type").on("change", function () {
        addUnits();
    });
    function addUnits() {
        if ($("#unit_id").val()) {
            $.ajax({
                method: "get",
                url: "/get_sub_units",
                data: { unit_id: $("#unit_id").val() },
                success: function (result) {
                    if (result.sub_units) {
                        units = result.sub_units;
                        $(".multiple_units").show();
                        $(".units_list").empty().show();
                        $(".mutiple_unit_price_input").empty();
                        $(".units_list").append(`</br></br>`);
                        units.forEach(function (unit, i) {
                            $(".units_list").append(`<b>` + unit.actual_name + `</b></br></br>`);
                            if (i !== 0) {
                                let j = parseInt(i) - 1;
                                $(".mutiple_unit_price_input").append(`</br><input class="form-control input-sm dsp input_number" name="multiple_unit[` + j + `][` + unit.id + `]" type="text">`);
                            }
                        });
                    }
                },
            });
        } else {
            $(".multiple_units_checkbox").addClass("hide");
        }
        $("#multiple_units").on("ifUnChecked", function () {
            $(".multiple_units").hide();
            $("#multiple_units").iCheck("uncheck");
        });
    }
    $("#stock_type").select2();
    $("#product_added_date").datepicker({
        format: "mm/dd/yyyy",
    });
	$("#is_service").on("ifChecked", function (event) {
		$("#enable_stock").iCheck("disable");
	});
	$("#is_service").on("ifUnchecked", function (event) {
		$("#enable_stock").iCheck("enable");
	});
	$("#enable_stock").on("ifChecked", function (event) {
		$("#is_service").iCheck("disable");
	});
	$("#enable_stock").on("ifUnchecked", function (event) {
		$("#is_service").iCheck("enable");
	});

	$(document).ready(function(){
		if($('input[name="is_service"]').is(':checked')){
			$("#enable_stock").iCheck("disable");
			$("#is_service").iCheck("enable");
		}else if($('input[name="is_service"]').is(':checked')){
			$("#is_service").iCheck("disable");
			$("#enable_stock").iCheck("enable");
		}
	});
</script>
@endsection
