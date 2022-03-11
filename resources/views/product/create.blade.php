@extends('layouts.app')
@section('title', __('product.add_new_product'))
@section('content')
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>@lang('product.add_new_product')</h1>
    <!-- <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Level</a></li>
        <li class="active">Here</li>
    </ol> -->
</section>
<style>
    .equal-column {
        min-height: 95px;
    }
</style>
<!-- Main content -->
<section class="content">
    @php $form_class = empty($duplicate_product) ? 'create' : ''; @endphp {!! Form::open(['url' => action('ProductController@store'), 'method' => 'post', 'id' => 'product_add_form','class' => 'product_form ' . $form_class, 'files' => true
    ]) !!} @component('components.widget', ['class' => 'box-primary'])
    <div class="row">
        <div class="col-sm-4">
            <div class="form-group">
                {!! Form::label('name', __('product.product_name') . ':*') !!} {!! Form::text('name', !empty($duplicate_product->name) ? $duplicate_product->name : null, ['class' => 'form-control', 'required', 'placeholder' =>
                __('product.product_name')]); !!}
            </div>
        </div>
        @php $business_id = session()->get('user.business_id'); $business_setting = App\Business::where('id', $business_id )->first(); $sku_prefix = $business_setting->sku_prefix; $last_sku =
        DB::table('products')->select('id','sku')->where('business_id', $business_id)->orderBy('id', 'desc')->first(); if(empty($sku_prefix)){ $sku_prefix = ''; } if(empty($last_sku)){ $next_sku = $sku_prefix.'-0001'; }else{ $sku =
        $last_sku->sku; $numbers = preg_replace('/[^0-9]/', '', $sku); $letters = preg_replace('/[^a-zA-Z]/', '', $sku); $numbers++; $numbers = sprintf("%04d", $numbers); $next_sku = '0001'; if(empty($sku_prefix)){ $next_sku = $numbers;
        }else{ $next_sku = $sku_prefix .'-'.$numbers; } } @endphp
        <div class="col-sm-4 equal-column">
            <div class="form-group">
                {!! Form::label('sku', __('product.sku') . ':') !!} @if(!empty($help_explanations['sku'])) @show_tooltip($help_explanations['sku']) @endif {!! Form::text('sku', $next_sku, ['class' => 'form-control', 'placeholder' =>
                __('product.sku')]); !!}
            </div>
        </div>
        <div class="col-sm-4 equal-column">
            <div class="form-group">
                {!! Form::label('barcode_type', __('product.barcode_type') . ':*') !!} {!! Form::select('barcode_type', $barcode_types, !empty($duplicate_product->barcode_type) ? $duplicate_product->barcode_type : $barcode_default, ['class'
                => 'form-control select2', 'required']); !!}
            </div>
        </div>
        <div class="col-sm-4 equal-column">
            <div class="form-group">
                {!! Form::label('unit_id', __('product.unit') . ':*') !!}
                <div class="input-group">
                    {!! Form::select('unit_id', $units, !empty($duplicate_product->unit_id) ? $duplicate_product->unit_id : session('business.default_unit'), ['class' => 'form-control select2', 'required', 'id' => 'unit_id']); !!}
                    <span class="input-group-btn">
                        <button type="button" @if(!auth()->
                            user()->can('unit.create')) disabled @endif class="btn btn-default bg-white btn-flat btn-modal" data-href="{{action('UnitController@create', ['quick_add' => true])}}" title="@lang('unit.add_unit')"
                            data-container=".view_modal"><i class="fa fa-plus-circle text-primary fa-lg"></i>
                        </button>
                    </span>
                </div>
            </div>
        </div>
        <div class="col-sm-4 equal-column @if(!session('business.enable_sub_units')) hide @endif">
            <div class="form-group">
                {!! Form::label('sub_unit_ids', __('lang_v1.related_sub_units') . ':') !!} @if(!empty($help_explanations['related_sub_units'])) @show_tooltip($help_explanations['related_sub_units']) @endif {!! Form::select('sub_unit_ids[]',
                [], !empty($duplicate_product->sub_unit_ids) ? $duplicate_product->sub_unit_ids : null, ['class' => 'form-control select2', 'multiple', 'id' => 'sub_unit_ids']); !!}
            </div>
        </div>
        <div class="col-sm-4 equal-column @if(!session('business.enable_brand')) hide @endif">
            <div class="form-group">
                {!! Form::label('brand_id', __('product.brand') . ':') !!}
                <div class="input-group">
                    {!! Form::select('brand_id', $brands, !empty($duplicate_product->brand_id) ? $duplicate_product->brand_id : null, ['placeholder' => __('messages.please_select'), 'class' => 'form-control select2']); !!}
                    <span class="input-group-btn">
                        <button type="button" @if(!auth()->
                            user()->can('brand.create')) disabled @endif class="btn btn-default bg-white btn-flat btn-modal" data-href="{{action('BrandController@create', ['quick_add' => true])}}" title="@lang('brand.add_brand')"
                            data-container=".view_modal"><i class="fa fa-plus-circle text-primary fa-lg"></i>
                        </button>
                    </span>
                </div>
            </div>
        </div>
        <div class="col-sm-4 equal-column @if(!session('business.enable_category')) hide @endif">
            <div class="form-group">
                {!! Form::label('category_id', __('product.category') . ':') !!} {!! Form::select('category_id', $categories, !empty($duplicate_product->category_id) ? $duplicate_product->category_id : null, ['placeholder' =>
                __('messages.please_select'), 'class' => 'form-control select2']); !!}
            </div>
        </div>
        <div class="col-sm-4 equal-column @if(!(session('business.enable_category') && session('business.enable_sub_category'))) hide @endif">
            <div class="form-group">
                {!! Form::label('sub_category_id', __('product.sub_category') . ':') !!} {!! Form::select('sub_category_id', $sub_categories, !empty($duplicate_product->sub_category_id) ? $duplicate_product->sub_category_id : null,
                ['placeholder' => __('messages.please_select'), 'class' => 'form-control select2']); !!}
            </div>
        </div>
        @php $default_location = null; if(count($business_locations) == 1){ $default_location = array_key_first($business_locations->toArray()); } @endphp
        <div class="col-sm-4 equal-column">
            <div class="form-group">
                {!! Form::label('product_locations', __('business.business_locations') . ':') !!} @if(!empty($help_explanations['business_location'])) @show_tooltip($help_explanations['business_location']) @endif {!!
                Form::select('product_locations[]', $business_locations, $default_location, ['class' => 'form-control select2', 'multiple', 'id' => 'product_locations']); !!}
            </div>
        </div>
        <div class="col-sm-4 equal-column @if(!empty($duplicate_product) && $duplicate_product->enable_stock == 0) hide @endif" id="alert_quantity_div">
            <div class="form-group">
                {!! Form::label('alert_quantity', __('product.alert_quantity') . ':') !!} @if(!empty($help_explanations['alert_quantity'])) @show_tooltip($help_explanations['alert_quantity']) @endif {!! Form::number('alert_quantity',
                !empty($duplicate_product->alert_quantity) ? $duplicate_product->alert_quantity : null , ['class' => 'form-control', 'placeholder' => __('product.alert_quantity'), 'min' => '0']); !!}
            </div>
        </div>
        @if(!empty($common_settings['enable_product_warranty']))
        <div class="col-sm-4 equal-column">
            <div class="form-group">
                {!! Form::label('warranty_id', __('lang_v1.warranty') . ':') !!} {!! Form::select('warranty_id', $warranties, null, ['class' => 'form-control select2', 'placeholder' => __('messages.please_select')]); !!}
            </div>
        </div>
        @endif
        <div class="col-sm-4 equal-column">
            <div class="form-group">
                {!! Form::label('image', __('lang_v1.product_image') . ':') !!} {!! Form::file('image', ['id' => 'upload_image', 'accept' => 'image/*']); !!}
                <small>
                    <p class="help-block">@lang('purchase.max_file_size', ['size' => (config('constants.document_size_limit') / 1000000)]) @lang('lang_v1.aspect_ratio_should_be_1_1')</p>
                </small>
            </div>
        </div>
        @if($is_manged_stock_enable == 1)
        <div class="col-sm-4 equal-column">
            <div class="form-group">
                <br />
                <label>
                    {!! Form::checkbox('enable_stock', 1, !empty($duplicate_product) ? $duplicate_product->enable_stock : true, ['class' => 'input-icheck', 'id' => 'enable_stock','disabled'=>!$is_manged_stock_enable]); !!}
                    <strong>@lang('product.manage_stock')</strong>
                </label>
                @if(!empty($help_explanations['manage_stock'])) @show_tooltip($help_explanations['manage_stock']) @endif
                <p class="help-block">
                    <i>@lang('product.enable_stock_help')</i>
                </p>
            </div>
        </div>
        <div class="col-sm-4 equal-column">
            <div class="form-group">
                <br />
                <label>
					{!! Form::checkbox('is_service', 1, false, ['class' => 'input-icheck', 'id' => 'is_service','disabled'=>'disabled']); !!}
                    <strong>{{__('Is Service?')}}</strong>
                </label>
            </div>
        </div>
        @endif
        <div class="col-sm-4 equal-column @if(!empty($duplicate_product) && $duplicate_product->enable_stock == 0) hide @endif" id="raw_material_div">
            <div class="form-group">
                <br />
                {!! Form::label('stock_type', __('product.stock_type'), []) !!} @if(!empty($help_explanations['stock_account_names'])) @show_tooltip($help_explanations['stock_account_names']) @endif {!! Form::select('stock_type', $accounts,
                null, ['class' => 'form-control select2', 'id' => 'stock_type', 'required', 'placeholder' => __('product.please_select')]) !!}
            </div>
        </div>
        <div class="col-sm-4 equal-column multiple_units_checkbox hide">
            <div class="form-group">
                <br />
                <label>
                    {!! Form::checkbox('multiple_units', 1, false, ['class' => 'input-icheck', 'id' => 'multiple_units']); !!}
                    <strong>@lang('product.multiple_units')</strong>
                </label>
            </div>
        </div>
        <div class="clearfix"></div>
        <div class="col-sm-8">
            <div class="form-group">
                {!! Form::label('product_description', __('lang_v1.product_description') . ':') !!} {!! Form::textarea('product_description', !empty($duplicate_product->product_description) ? $duplicate_product->product_description : null,
                ['class' => 'form-control']); !!}
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
                    {!! Form::text('date', null, ['class' => 'form-control required input_number', 'id' => 'product_added_date']); !!}
                </div>
            </div>
        </div>
    </div>
    @endcomponent @component('components.widget', ['class' => 'box-primary'])
    <div class="row">
        @if(session('business.enable_product_expiry')) @if(session('business.expiry_type') == 'add_expiry') @php $expiry_period = 12; $hide = true; @endphp @else @php $expiry_period = null; $hide = false; @endphp @endif
        <div class="col-sm-4 @if($hide) hide @endif">
            <div class="form-group">
                <div class="multi-input">
                    {!! Form::label('expiry_period', __('product.expires_in') . ':') !!}<br />
                    {!! Form::text('expiry_period', !empty($duplicate_product->expiry_period) ? @num_format($duplicate_product->expiry_period) : $expiry_period, ['class' => 'form-control pull-left input_number', 'placeholder' =>
                    __('product.expiry_period'), 'style' => 'width:60%;']); !!} {!! Form::select('expiry_period_type', ['months'=>__('product.months'), 'days'=>__('product.days'), '' =>__('product.not_applicable') ],
                    !empty($duplicate_product->expiry_period_type) ? $duplicate_product->expiry_period_type : 'months', ['class' => 'form-control select2 pull-left', 'style' => 'width:40%;', 'id' => 'expiry_period_type']); !!}
                </div>
            </div>
        </div>
        @endif
        <div class="col-sm-4">
            <div class="form-group">
                <br />
                <label> {!! Form::checkbox('enable_sr_no', 1, !(empty($duplicate_product)) ? $duplicate_product->enable_sr_no : false, ['class' => 'input-icheck']); !!} <strong>@lang('lang_v1.enable_imei_or_sr_no')</strong> </label>
                @if(!empty($help_explanations['enable_product_description_imei'])) @show_tooltip($help_explanations['enable_product_description_imei']) @endif
            </div>
        </div>
        <div class="col-sm-4">
            <div class="form-group">
                <br />
                <label> {!! Form::checkbox('not_for_selling', 1, !(empty($duplicate_product)) ? $duplicate_product->not_for_selling : false, ['class' => 'input-icheck']); !!} <strong>@lang('lang_v1.not_for_selling')</strong> </label>
                @if(!empty($help_explanations['not_for_selling'])) @show_tooltip($help_explanations['not_for_selling']) @endif
            </div>
        </div>
        <div class="col-sm-4">
            <div class="form-group">
                <div class="checkbox">
                    <label> {!! Form::checkbox('show_avai_qty_in_qr_catalogue', 1, false, [ 'class' => 'input-icheck']); !!} <strong> {{ __( 'lang_v1.show_avai_qty_in_qr_catalogue' ) }} </strong> </label>
                </div>
            </div>
        </div>
        <div class="clearfix"></div>
        <div class="col-sm-4">
            <div class="form-group">
                <div class="checkbox">
                    <label> {!! Form::checkbox('show_in_catalogue_page', 1, false, [ 'class' => 'input-icheck']); !!} <strong> {{ __( 'lang_v1.show_in_catalogue_page' ) }} </strong> </label>
                </div>
            </div>
        </div>
        <div class="clearfix"></div>
        <!-- Rack, Row & position number -->
        @if(session('business.enable_racks') || session('business.enable_row') || session('business.enable_position'))
        <div class="col-md-12">
            <h4>@lang('lang_v1.rack_details'): @if(!empty($help_explanations['rack_row_position_details'])) @show_tooltip($help_explanations['rack_row_position_details']) @endif</h4>
        </div>
        @foreach($business_locations as $id => $location)
        <div class="col-sm-3">
            <div class="form-group">
                {!! Form::label('rack_' . $id, $location . ':') !!} @if(session('business.enable_racks')) {!! Form::text('product_racks[' . $id . '][rack]', !empty($rack_details[$id]['rack']) ? $rack_details[$id]['rack'] : null, ['class' =>
                'form-control', 'id' => 'rack_' . $id, 'placeholder' => __('lang_v1.rack')]); !!} @endif @if(session('business.enable_row')) {!! Form::text('product_racks[' . $id . '][row]', !empty($rack_details[$id]['row']) ?
                $rack_details[$id]['row'] : null, ['class' => 'form-control', 'placeholder' => __('lang_v1.row')]); !!} @endif @if(session('business.enable_position')) {!! Form::text('product_racks[' . $id . '][position]',
                !empty($rack_details[$id]['position']) ? $rack_details[$id]['position'] : null, ['class' => 'form-control', 'placeholder' => __('lang_v1.position')]); !!} @endif
            </div>
        </div>
        @endforeach @endif
        <div class="col-sm-4">
            <div class="form-group">
                {!! Form::label('weight', __('lang_v1.weight') . ':') !!} {!! Form::text('weight', !empty($duplicate_product->weight) ? $duplicate_product->weight : null, ['class' => 'form-control', 'placeholder' => __('lang_v1.weight')]);
                !!}
            </div>
        </div>
        <!--custom fields-->
        <div class="clearfix"></div>
        <div class="col-sm-3">
            <div class="form-group">
                {!! Form::label('product_custom_field1', __('lang_v1.product_custom_field1') . ':') !!} {!! Form::text('product_custom_field1', !empty($duplicate_product->product_custom_field1) ? $duplicate_product->product_custom_field1 :
                null, ['class' => 'form-control', 'placeholder' => __('lang_v1.product_custom_field1')]); !!}
            </div>
        </div>
        <div class="col-sm-3">
            <div class="form-group">
                {!! Form::label('product_custom_field2', __('lang_v1.product_custom_field2') . ':') !!} {!! Form::text('product_custom_field2', !empty($duplicate_product->product_custom_field2) ? $duplicate_product->product_custom_field2 :
                null, ['class' => 'form-control', 'placeholder' => __('lang_v1.product_custom_field2')]); !!}
            </div>
        </div>
        <div class="col-sm-3">
            <div class="form-group">
                {!! Form::label('product_custom_field3', __('lang_v1.product_custom_field3') . ':') !!} {!! Form::text('product_custom_field3', !empty($duplicate_product->product_custom_field3) ? $duplicate_product->product_custom_field3 :
                null, ['class' => 'form-control', 'placeholder' => __('lang_v1.product_custom_field3')]); !!}
            </div>
        </div>
        <div class="col-sm-3">
            <div class="form-group">
                {!! Form::label('product_custom_field4', __('lang_v1.product_custom_field4') . ':') !!} {!! Form::text('product_custom_field4', !empty($duplicate_product->product_custom_field4) ? $duplicate_product->product_custom_field4 :
                null, ['class' => 'form-control', 'placeholder' => __('lang_v1.product_custom_field4')]); !!}
            </div>
        </div>
        <!--custom fields-->
        <div class="clearfix"></div>
        @include('layouts.partials.module_form_part')
    </div>
    @endcomponent @component('components.widget', ['class' => 'box-primary'])
    <div class="row">
        <div class="col-sm-4 @if(!session('business.enable_price_tax')) hide @endif">
            <div class="form-group">
                {!! Form::label('tax', __('product.applicable_tax') . ':') !!} {!! Form::select('tax', $taxes, !empty($duplicate_product->tax) ? $duplicate_product->tax : null, ['placeholder' => __('messages.please_select'), 'class' =>
                'form-control select2'], $tax_attributes); !!}
            </div>
        </div>
        <div class="col-sm-4 @if(!session('business.enable_price_tax')) hide @endif">
            <div class="form-group">
                {!! Form::label('tax_type', __('product.selling_price_tax_type') . ':*') !!} {!! Form::select('tax_type', ['inclusive' => __('product.inclusive'), 'exclusive' => __('product.exclusive')], !empty($duplicate_product->tax_type)
                ? $duplicate_product->tax_type : 'exclusive', ['class' => 'form-control select2', 'required']); !!}
            </div>
        </div>
        <div class="clearfix"></div>
        <div class="col-sm-4">
            <div class="form-group">
                {!! Form::label('type', __('product.product_type') . ':*') !!} @if(!empty($help_explanations['product_type'])) @show_tooltip($help_explanations['product_type']) @endif {!! Form::select('type', $product_types,
                !empty($duplicate_product->type) ? $duplicate_product->type : null, ['class' => 'form-control select2', 'required', 'data-action' => !empty($duplicate_product) ? 'duplicate' : 'add', 'data-product_id' =>
                !empty($duplicate_product) ? $duplicate_product->id : '0']); !!}
            </div>
        </div>
        <div class="form-group col-sm-12" id="product_form_part">
            @include('product.partials.single_product_form_part', ['profit_percent' => $default_profit_percent])
        </div>
        <input type="hidden" id="variation_counter" value="1" />
        <input type="hidden" id="default_profit_percent" value="{{ $default_profit_percent }}" />
    </div>
    @endcomponent
    <div class="row">
        <div class="col-sm-12">
            <input type="hidden" name="submit_type" id="submit_type" />
            <div class="text-center">
                <div class="btn-group">
                    @if($selling_price_group_count)
                    <button type="submit" value="submit_n_add_selling_prices" class="btn btn-warning submit_product_form">@lang('lang_v1.save_n_add_selling_price_group_prices')</button>
                    @endif
                    <button id="opening_stock_button" @if(!empty($duplicate_product) && $duplicate_product->
                        enable_stock == 0) disabled @endif type="submit" value="submit_n_add_opening_stock" class="btn bg-purple submit_product_form">@lang('lang_v1.save_n_add_opening_stock')
                    </button>
                    <button type="submit" value="save_n_add_another" class="btn bg-maroon submit_product_form">@lang('lang_v1.save_n_add_another')</button>
                    <button type="submit" value="submit" class="btn btn-primary submit_product_form">@lang('messages.save')</button>
                </div>
            </div>
        </div>
    </div>
    {!! Form::close() !!}
</section>
<!-- /.content -->
@endsection
@section('javascript')
@php $asset_v = env('APP_VERSION'); @endphp
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
		// $('#multiple_units').on('ifChecked', function() {
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
		// });
		$("#multiple_units").on("ifUnChecked", function () {
			$(".multiple_units").hide();
			$("#multiple_units").iCheck("uncheck");
		});
	}
	fuel_category = @if(!empty($fuel_category)) {{$fuel_category->id}} @endif ;
	$("#category_id").change(function () {
		if ($(this).val() == fuel_category) {
			$("#opening_stock_button").hide();
		} else {
			$("#opening_stock_button").show();
		}
	});
	$(document).ready(function () {
		setTimeout(() => {
			$("#product_locations").trigger("change");
		}, 2000);
		$("#product_locations").change(function () {
			let location_id = $(this).val().pop();
			$.ajax({
				method: "get",
				url: "/location-has-stores-count/" + location_id,
				data: {},
				success: function (result) {
					if (result.count <= 0) {
						toastr.error("Please add store to this location");
					}
				},
			});
		});
		$("#product_added_date").datepicker("setDate", new Date());
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
</script>
@endsection