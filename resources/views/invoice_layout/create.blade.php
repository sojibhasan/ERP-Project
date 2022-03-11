@extends('layouts.app')
@section('title',  __('invoice.add_invoice_layout'))

@section('content')
<style type="text/css">



</style>
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>@lang('invoice.add_invoice_layout')</h1>
</section>

<!-- Main content -->
<section class="content">
{!! Form::open(['url' => action('InvoiceLayoutController@store'), 'method' => 'post', 'id' => 'add_invoice_layout_form', 'files' => true]) !!}
  <div class="box box-solid">
    <div class="box-body">
      <div class="row">

        <div class="col-sm-6">
          <div class="col-sm-12">
            <div class="form-group">
              {!! Form::label('name', __('invoice.layout_name') . ':*') !!}
                {!! Form::text('name', null, ['class' => 'form-control', 'required',
                'placeholder' => __('invoice.layout_name')]); !!}
            </div>
          </div>
          <div class="col-sm-6">
            <div class="form-group">
              {!! Form::label('font_size', __('invoice.business_name_font_size') . ':') !!}
                {!! Form::text('business_name_font_size', null, ['class' => 'form-control', 'required',
                'placeholder' => __('invoice.business_name_font_size')]); !!}
            </div>
          </div>
          <div class="col-sm-6">
            <div class="form-group">
              {!! Form::label('font_size', __('invoice.invoice_heading_font_size') . ':') !!}
                {!! Form::text('invoice_heading_font_size', null, ['class' => 'form-control', 'required',
                'placeholder' => __('invoice.invoice_heading_font_size')]); !!}
            </div>
          </div>
          <div class="col-sm-6">
            <div class="form-group">
              {!! Form::label('font_size', __('invoice.header_font_size') . ':') !!}
                {!! Form::text('header_font_size', null, ['class' => 'form-control', 'required',
                'placeholder' => __('invoice.header_font_size')]); !!}
            </div>
          </div>
          <div class="col-sm-6">
            <div class="form-group">
              {!! Form::label('font_size', __('invoice.layout_font_size') . ':') !!}
                {!! Form::text('font_size', null, ['class' => 'form-control', 'required',
                'placeholder' => __('invoice.layout_font_size')]); !!}
            </div>
          </div>
          <div class="col-sm-6">
            <div class="form-group">
              {!! Form::label('font_size', __('invoice.footer_font_size') . ':') !!}
                {!! Form::text('footer_font_size', null, ['class' => 'form-control', 'required',
                'placeholder' => __('invoice.footer_font_size')]); !!}
            </div>
          </div>
          <div class="col-sm-6">
            <div class="form-group">
              {!! Form::label('header_align', __('lang_v1.header_align') . ':') !!}
              {!! Form::select('header_align', ['left' => 'Left', 'right' => 'Right', 'center' => 'Center'], null, ['class' => 'form-control',
                'placeholder' => __('lang_v1.please_select'), 'id' => 'header_align' ]); !!}
            </div>
          </div>
        </div>
        <div class="col-sm-6">
          <div class="form-group">
            {!! Form::label('design', __('lang_v1.design') . ':*') !!}
              {!! Form::select('design', $designs, 'classic', ['class' => 'form-control']); !!}
              <span class="help-block">Used for browser based printing</span>
          </div>

          <div class="form-group hide" id="columnize-taxes">
            <div class="col-md-3">
              <input type="text" class="form-control" 
              name="table_tax_headings[]" required="required" 
              placeholder="tax 1 name"
              disabled>
              @show_tooltip(__('lang_v1.tooltip_columnize_taxes_heading'))
            </div>
            <div class="col-md-3">
              <input type="text" class="form-control" 
              name="table_tax_headings[]" placeholder="tax 2 name"
              disabled>
            </div>
            <div class="col-md-3">
              <input type="text" class="form-control" 
              name="table_tax_headings[]" placeholder="tax 3 name"
              disabled>
            </div>
            <div class="col-md-3">
              <input type="text" class="form-control" 
              name="table_tax_headings[]" placeholder="tax 4 name"
              disabled>
            </div>
           
          </div>

        </div>

        <!-- Logo -->
        <div class="col-sm-6">
          <div class="form-group">
            {!! Form::label('logo', __('invoice.invoice_logo') . ':') !!}
            {!! Form::file('logo', ['id' => 'logo_image', 'onchange' => 'readURL(this)']); !!}
            <span class="help-block">@lang('lang_v1.invoice_logo_help', ['max_size' => '1 MB'])</span>
          </div>
        </div>
        <div class="col-sm-6">
          <div class="form-group">
            <div class="checkbox">
              <label>
                {!! Form::checkbox('show_logo', 1, false, ['class' => 'input-icheck']); !!} @lang('invoice.show_logo')</label>
              </div>
          </div>
          <div class="clearfix"></div>
          <div class="col-sm-3">
            <div class="form-group">
              {!! Form::label('logo_height', __('lang_v1.logo_height', ['_number_' => 1]) . ':' ) !!}
              {!! Form::text('logo_height', null, ['class' => 'form-control', 'id' => 'logo_height',
                'placeholder' => __('lang_v1.logo_height', ['_number_' => 1]) ]); !!}
            </div>
          </div>
          <div class="col-sm-3">
            <div class="form-group">
              {!! Form::label('logo_width', __('lang_v1.logo_width', ['_number_' => 1]) . ':' ) !!}
              {!! Form::text('logo_width',null, ['class' => 'form-control', 'id' => 'logo_width',
                'placeholder' => __('lang_v1.logo_width', ['_number_' => 1]) ]); !!}
            </div>
          </div>
          <div class="col-sm-3">
            <div class="form-group">
              {!! Form::label('logo_margin_top', __('lang_v1.logo_margin_top', ['_number_' => 1]) . ':' ) !!}
              {!! Form::text('logo_margin_top',null, ['class' => 'form-control',  'id' => 'logo_margin_top',
                'placeholder' => __('lang_v1.logo_margin_top', ['_number_' => 1]) ]); !!}
            </div>
          </div>
          <div class="col-sm-3">
            <div class="form-group">
              {!! Form::label('logo_margin_bottom', __('lang_v1.logo_margin_bottom', ['_number_' => 1]) . ':' ) !!}
              {!! Form::text('logo_margin_bottom', null, ['class' => 'form-control', 'id' => 'logo_margin_bottom',
                'placeholder' => __('lang_v1.logo_margin_bottom', ['_number_' => 1]) ]); !!}
            </div>
          </div>
         
          <div class="clearfix"></div>
        </div>

        <button type="button" style="margin-right: 20px;" class="btn btn-info pull-right" id="preview_button">@lang('invoice.preview')</button>
        <div class="col-sm-12">
          <div class="form-group">
            {!! Form::label('header_text', __('invoice.header_text') . ':' ) !!}
            {!! Form::textarea('header_text','', ['class' => 'form-control',
              'placeholder' => __('invoice.header_text'), 'rows' => 3]); !!}
          </div>
        </div>
        <div class="clearfix"></div>
        <div class="col-sm-3">
          <div class="form-group">
            {!! Form::label('sub_heading_line1', __('lang_v1.sub_heading_line', ['_number_' => 1]) . ':' ) !!}
            {!! Form::text('sub_heading_line1', null, ['class' => 'form-control',
              'placeholder' => __('lang_v1.sub_heading_line', ['_number_' => 1]) ]); !!}
          </div>
        </div>
        <div class="col-sm-3">
          <div class="form-group">
            {!! Form::label('sub_heading_line2', __('lang_v1.sub_heading_line', ['_number_' => 2]) . ':' ) !!}
            {!! Form::text('sub_heading_line2', null, ['class' => 'form-control',
              'placeholder' => __('lang_v1.sub_heading_line', ['_number_' => 2]) ]); !!}
          </div>
        </div>
        <div class="col-sm-3">
          <div class="form-group">
            {!! Form::label('sub_heading_line3', __('lang_v1.sub_heading_line', ['_number_' => 3]) . ':' ) !!}
            {!! Form::text('sub_heading_line3', null, ['class' => 'form-control',
              'placeholder' => __('lang_v1.sub_heading_line', ['_number_' => 3]) ]); !!}
          </div>
        </div>
        <div class="col-sm-3">
          <div class="form-group">
            {!! Form::label('sub_heading_line4', __('lang_v1.sub_heading_line', ['_number_' => 4]) . ':' ) !!}
            {!! Form::text('sub_heading_line4', null, ['class' => 'form-control',
              'placeholder' => __('lang_v1.sub_heading_line', ['_number_' => 4]) ]); !!}
          </div>
        </div>
        <div class="clearfix"></div>
        <div class="col-sm-3">
          <div class="form-group">
            {!! Form::label('sub_heading_line5', __('lang_v1.sub_heading_line', ['_number_' => 5]) . ':' ) !!}
            {!! Form::text('sub_heading_line5', null, ['class' => 'form-control',
              'placeholder' => __('lang_v1.sub_heading_line', ['_number_' => 5]) ]); !!}
          </div>
        </div>

      </div>
    </div>
  </div>
  <div class="box box-solid">
  <div class="box-body">
    <div class="row">
        <div class="col-sm-3">
          <div class="form-group">
            {!! Form::label('invoice_heading', __('invoice.invoice_heading') . ':' ) !!}
            {!! Form::text('invoice_heading', 'Invoice', ['class' => 'form-control',
              'placeholder' => __('invoice.invoice_heading') ]); !!}
          </div>
        </div>
        <div class="col-sm-3">
          <div class="form-group">
            {!! Form::label('invoice_heading_not_paid', __('invoice.invoice_heading_not_paid') . ':' ) !!}
            {!! Form::text('invoice_heading_not_paid', null, ['class' => 'form-control',
              'placeholder' => __('invoice.invoice_heading_not_paid') ]); !!}
          </div>
        </div>
        <div class="col-sm-3">
          <div class="form-group">
            {!! Form::label('invoice_heading_paid', __('invoice.invoice_heading_paid') . ':' ) !!}
            {!! Form::text('invoice_heading_paid', null, ['class' => 'form-control',
              'placeholder' => __('invoice.invoice_heading_paid') ]); !!}
          </div>
        </div>
        <div class="col-sm-3">
          <div class="form-group">
            {!! Form::label('quotation_heading', __('lang_v1.quotation_heading') . ':' ) !!}
            @show_tooltip(__('lang_v1.tooltip_quotation_heading'))
            {!! Form::text('quotation_heading', 'Quotation', ['class' => 'form-control',
              'placeholder' => __('lang_v1.quotation_heading') ]); !!}
          </div>
        </div>
        <div class="clearfix"></div>
        <div class="col-sm-3">
          <div class="form-group">
            {!! Form::label('invoice_no_prefix', __('invoice.invoice_no_prefix') . ':' ) !!}
            {!! Form::text('invoice_no_prefix', 'Invoice No.', ['class' => 'form-control',
              'placeholder' => __('invoice.invoice_no_prefix') ]); !!}
          </div>
        </div>
        <div class="col-sm-3">
          <div class="form-group">
            {!! Form::label('quotation_no_prefix', __('lang_v1.quotation_no_prefix') . ':' ) !!}
            {!! Form::text('quotation_no_prefix', 'Quotation No.', ['class' => 'form-control',
              'placeholder' => __('lang_v1.quotation_no_prefix') ]); !!}
          </div>
        </div>
        <div class="col-sm-3">
          <div class="form-group">
            {!! Form::label('date_label', __('lang_v1.date_label') . ':' ) !!}
            {!! Form::text('date_label', 'Date', ['class' => 'form-control',
              'placeholder' => __('lang_v1.date_label') ]); !!}
          </div>
        </div>

        <div class="col-sm-3">
          <div class="form-group">
            {!! Form::label('due_date_label', __('lang_v1.due_date_label') . ':' ) !!}
            {!! Form::text('common_settings[due_date_label]', 'Due Date', ['class' => 'form-control',
              'placeholder' => __('lang_v1.due_date_label'), 'id' => 'due_date_label' ]); !!}
          </div>
        </div>
        <div class="col-sm-3">
          <div class="form-group">
            <div class="checkbox">
              <label>
                {!! Form::checkbox('common_settings[show_due_date]', 1, false, ['class' => 'input-icheck']); !!} @lang('lang_v1.show_due_date')</label>
              </div>
          </div>
        </div>

        <div class="col-sm-3">
          <div class="form-group">
            {!! Form::label('date_time_format', __('lang_v1.date_time_format') . ':' ) !!}
            {!! Form::text('date_time_format', null, ['class' => 'form-control',
              'placeholder' => __('lang_v1.date_time_format') ]); !!} 
              <p class="help-block">{!! __('lang_v1.date_time_format_help') !!}</p>
          </div>
        </div>
        
        <div class="col-sm-3">
          <div class="form-group">
            {!! Form::label('sales_person_label', __('lang_v1.sales_person_label') . ':' ) !!}
            {!! Form::text('sales_person_label', null, ['class' => 'form-control',
            'placeholder' => __('lang_v1.sales_person_label') ]); !!}
          </div>
        </div>
        <div class="clearfix"></div>
        
        <div class="col-sm-3">
          <div class="form-group">
            <div class="checkbox">
              <label>
                {!! Form::checkbox('show_business_name', 1, false, ['class' => 'input-icheck']); !!} @lang('invoice.show_business_name')</label>
              </div>
          </div>
        </div>
        <div class="col-sm-3">
          <div class="form-group">
            <div class="checkbox">
              <label>
                {!! Form::checkbox('show_location_name', 1, true, ['class' => 'input-icheck']); !!} @lang('invoice.show_location_name')</label>
              </div>
          </div>
        </div>
        
        <div class="col-sm-3">
          <div class="form-group">
            <div class="checkbox">
              <label>
                {!! Form::checkbox('show_sales_person', 1, false, ['class' => 'input-icheck']); !!} @lang('lang_v1.show_sales_person')</label>
              </div>
          </div>
        </div>
        <div class="clearfix"></div>
        <div class="col-sm-12">
          <h4>@lang('lang_v1.fields_for_customer_details'):</h4>
        </div>
        <div class="clearfix"></div>
        <div class="col-sm-3">
          <div class="form-group">
            <div class="checkbox">
              <label>
                {!! Form::checkbox('show_customer', 1, true, ['class' => 'input-icheck']); !!} @lang('invoice.show_customer')</label>
              </div>
          </div>
        </div>
        <div class="col-sm-3">
          <div class="form-group">
            {!! Form::label('customer_label', __('invoice.customer_label') . ':' ) !!}
            {!! Form::text('customer_label', 'Customer', ['class' => 'form-control',
              'placeholder' => __('invoice.customer_label') ]); !!}
          </div>
        </div>
        <div class="col-sm-3">
          <div class="form-group">
            <div class="checkbox">
              <label>
                {!! Form::checkbox('show_client_id', 1, false, ['class' => 'input-icheck']); !!} @lang('lang_v1.show_client_id')</label>
              </div>
          </div>
        </div>
        <div class="col-sm-3">
          <div class="form-group">
            {!! Form::label('client_id_label', __('lang_v1.client_id_label') . ':' ) !!}
            {!! Form::text('client_id_label', null, ['class' => 'form-control',
              'placeholder' => __('lang_v1.client_id_label') ]); !!}
          </div>
        </div>
        
        <div class="col-sm-3">
          <div class="form-group">
            {!! Form::label('client_tax_label', __('lang_v1.client_tax_label') . ':' ) !!}
            {!! Form::text('client_tax_label', null, ['class' => 'form-control',
            'placeholder' => __('lang_v1.client_tax_label') ]); !!}
          </div>
        </div>

        <div class="col-sm-3">
          <div class="form-group">
            <div class="checkbox">
              <label>
                {!! Form::checkbox('show_reward_point', 1, false, ['class' => 'input-icheck']); !!} @lang('lang_v1.show_reward_point')</label>
              </div>
          </div>
        </div>

        <div class="clearfix"></div>
        <div class="col-sm-3">
        <div class="form-group">
          <div class="checkbox">
            <label>
              {!! Form::checkbox('contact_custom_fields[]', 'custom_field1', false, ['class' => 'input-icheck']); !!} @lang('lang_v1.contact_custom_field1')</label>
          </div>
        </div>
      </div>

      <div class="col-sm-3">
        <div class="form-group">
          <div class="checkbox">
            <label>
              {!! Form::checkbox('contact_custom_fields[]', 'custom_field2', false, ['class' => 'input-icheck']); !!} @lang('lang_v1.contact_custom_field2')</label>
          </div>
        </div>
      </div>

      <div class="col-sm-3">
        <div class="form-group">
          <div class="checkbox">
            <label>
              {!! Form::checkbox('contact_custom_fields[]', 'custom_field3', false, ['class' => 'input-icheck']); !!} @lang('lang_v1.contact_custom_field3')</label>
          </div>
        </div>
      </div>
      <div class="col-sm-3">
        <div class="form-group">
          <div class="checkbox">
            <label>
              {!! Form::checkbox('contact_custom_fields[]', 'custom_field4', false, ['class' => 'input-icheck']); !!} @lang('lang_v1.contact_custom_field4')</label>
          </div>
        </div>
      </div>

        <div class="clearfix"></div>
        <div class="col-sm-12">
          <h4>@lang('invoice.fields_to_be_shown_in_address'):</h4>
        </div>
        <div class="clearfix"></div>
        <div class="col-sm-3">
          <div class="form-group">
            <div class="checkbox">
              <label>
                {!! Form::checkbox('show_landmark', 1, true, ['class' => 'input-icheck']); !!} @lang('business.landmark')</label>
              </div>
          </div>
        </div>
        <div class="col-sm-3">
          <div class="form-group">
            <div class="checkbox">
              <label>
                {!! Form::checkbox('show_city', 1, true, ['class' => 'input-icheck']); !!} @lang('business.city')</label>
              </div>
          </div>
        </div>
        <div class="col-sm-3">
          <div class="form-group">
            <div class="checkbox">
              <label>
                {!! Form::checkbox('show_state', 1, true, ['class' => 'input-icheck']); !!} @lang('business.state')</label>
              </div>
          </div>
        </div>
        <div class="col-sm-3">
          <div class="form-group">
            <div class="checkbox">
              <label>
                {!! Form::checkbox('show_country', 1, true, ['class' => 'input-icheck']); !!} @lang('business.country')</label>
              </div>
          </div>
        </div>
        <div class="clearfix"></div>
        <div class="col-sm-3">
          <div class="form-group">
            <div class="checkbox">
              <label>
                {!! Form::checkbox('show_zip_code', 1, true, ['class' => 'input-icheck']); !!} @lang('business.zip_code')</label>
              </div>
          </div>
        </div>
        <div class="col-sm-3">
        <div class="form-group">
          <div class="checkbox">
            <label>
              {!! Form::checkbox('location_custom_fields[]', 'custom_field1', false, ['class' => 'input-icheck']); !!} @lang('lang_v1.location_custom_field1')</label>
          </div>
        </div>
      </div>

      <div class="col-sm-3">
        <div class="form-group">
          <div class="checkbox">
            <label>
              {!! Form::checkbox('location_custom_fields[]', 'custom_field2', false, ['class' => 'input-icheck']); !!} @lang('lang_v1.location_custom_field2')</label>
          </div>
        </div>
      </div>

      <div class="col-sm-3">
        <div class="form-group">
          <div class="checkbox">
            <label>
              {!! Form::checkbox('location_custom_fields[]', 'custom_field3', false, ['class' => 'input-icheck']); !!} @lang('lang_v1.location_custom_field3')</label>
          </div>
        </div>
      </div>

      <div class="col-sm-3">
        <div class="form-group">
          <div class="checkbox">
            <label>
              {!! Form::checkbox('location_custom_fields[]', 'custom_field4', false, ['class' => 'input-icheck']); !!} @lang('lang_v1.location_custom_field4')</label>
          </div>
        </div>
      </div>

        <div class="clearfix"></div>
         <!-- Shop Communication details -->
        <div class="col-sm-12">
          <h4>@lang('invoice.fields_to_shown_for_communication'):</h4>
        </div>

        <div class="col-sm-3">
          <div class="form-group">
            <div class="checkbox">
              <label>
                {!! Form::checkbox('show_mobile_number', 1, true, ['class' => 'input-icheck']); !!} @lang('invoice.show_mobile_number')</label>
              </div>
          </div>
        </div>
        <div class="col-sm-3">
          <div class="form-group">
            <div class="checkbox">
              <label>
                {!! Form::checkbox('show_alternate_number', 1, false, ['class' => 'input-icheck']); !!} @lang('invoice.show_alternate_number')</label>
              </div>
          </div>
        </div>
        <div class="col-sm-3">
          <div class="form-group">
            <div class="checkbox">
              <label>
                {!! Form::checkbox('show_email', 1, false, ['class' => 'input-icheck']); !!} @lang('invoice.show_email')</label>
              </div>
          </div>
        </div>
        <div class="col-sm-12">
          <h4>@lang('invoice.fields_to_shown_for_tax'):</h4>
        </div>
        <div class="col-sm-3">
          <div class="form-group">
            <div class="checkbox">
              <label>
                {!! Form::checkbox('show_tax_1', 1, true, ['class' => 'input-icheck']); !!} @lang('invoice.show_tax_1')</label>
              </div>
          </div>
        </div>
        <div class="col-sm-3">
          <div class="form-group">
            <div class="checkbox">
              <label>
                {!! Form::checkbox('show_tax_2', 1, false, ['class' => 'input-icheck']); !!} @lang('invoice.show_tax_2')</label>
              </div>
          </div>
        </div>
        
    </div>
    </div>
  </div>
  <div class="box box-solid">
    <div class="box-body">
      <div class="row">
        <div class="col-sm-3">
          <div class="form-group">
            {!! Form::label('table_product_label', __('lang_v1.product_label') . ':' ) !!}
            {!! Form::text('table_product_label', 'Product', ['class' => 'form-control',
              'placeholder' => __('lang_v1.product_label') ]); !!}
          </div>
        </div>
        <div class="col-sm-3">
          <div class="form-group">
            {!! Form::label('table_qty_label', __('lang_v1.qty_label') . ':' ) !!}
            {!! Form::text('table_qty_label', 'Quantity', ['class' => 'form-control',
              'placeholder' => __('lang_v1.qty_label') ]); !!}
          </div>
        </div>
        <div class="col-sm-3">
          <div class="form-group">
            {!! Form::label('table_unit_price_label', __('lang_v1.unit_price_label') . ':' ) !!}
            {!! Form::text('table_unit_price_label', 'Unit Price', ['class' => 'form-control',
              'placeholder' => __('lang_v1.unit_price_label') ]); !!}
          </div>
        </div>
        <div class="col-sm-3">
          <div class="form-group">
            {!! Form::label('table_subtotal_label', __('lang_v1.subtotal_label') . ':' ) !!}
            {!! Form::text('table_subtotal_label', 'Subtotal', ['class' => 'form-control',
              'placeholder' => __('lang_v1.subtotal_label') ]); !!}
          </div>
        </div>
        <div class="col-sm-3">
          <div class="form-group">
            {!! Form::label('cat_code_label', __('lang_v1.cat_code_label') . ':' ) !!}
            {!! Form::text('cat_code_label', 'HSN', ['class' => 'form-control',
              'placeholder' => 'HSN or Category Code' ]); !!}
          </div>
        </div>
        
        <div class="col-sm-12">
          <h4>@lang('lang_v1.product_details_to_be_shown'):</h4>
        </div>
        <div class="col-sm-3">
          <div class="form-group">
            <div class="checkbox">
              <label>
                {!! Form::checkbox('show_brand', 1, false, ['class' => 'input-icheck']); !!} @lang('lang_v1.show_brand')</label>
              </div>
          </div>
        </div>
        <div class="col-sm-3">
          <div class="form-group">
            <div class="checkbox">
              <label>
                {!! Form::checkbox('show_sku', 1, true, ['class' => 'input-icheck']); !!} @lang('lang_v1.show_sku')</label>
              </div>
          </div>
        </div>
        <div class="col-sm-3">
          <div class="form-group">
            <div class="checkbox">
              <label>
                {!! Form::checkbox('show_cat_code', 1, false, ['class' => 'input-icheck']); !!} @lang('lang_v1.show_cat_code')</label>
              </div>
          </div>
        </div>
        
        

        <div class="col-sm-3">
          <div class="form-group">
            <div class="checkbox">
              <label>
                {!! Form::checkbox('show_sale_description', 1, false, ['class' => 'input-icheck']); !!} @lang('lang_v1.show_sale_description')</label>
            </div>
            <p class="help-block">@lang('lang_v1.product_imei_or_sn')</p>
          </div>
        </div>
        <div class="clearfix"></div>
        <div class="col-sm-3">
        <div class="form-group">
          <div class="checkbox">
            <label>
              {!! Form::checkbox('product_custom_fields[]', 'product_custom_field1', false, ['class' => 'input-icheck']); !!} @lang('lang_v1.product_custom_field1')</label>
          </div>
        </div>
      </div>

      <div class="col-sm-3">
        <div class="form-group">
          <div class="checkbox">
            <label>
              {!! Form::checkbox('product_custom_fields[]', 'product_custom_field2', false, ['class' => 'input-icheck']); !!} @lang('lang_v1.product_custom_field2')</label>
          </div>
        </div>
      </div>

      <div class="col-sm-3">
        <div class="form-group">
          <div class="checkbox">
            <label>
              {!! Form::checkbox('product_custom_fields[]', 'product_custom_field3', false, ['class' => 'input-icheck']); !!} @lang('lang_v1.product_custom_field3')</label>
          </div>
        </div>
      </div>

      <div class="col-sm-3">
        <div class="form-group">
          <div class="checkbox">
            <label>
              {!! Form::checkbox('product_custom_fields[]', 'product_custom_field4', false, ['class' => 'input-icheck']); !!} @lang('lang_v1.product_custom_field4')</label>
          </div>
        </div>
      </div>
      <div class="clearfix"></div>
        @if(request()->session()->get('business.enable_product_expiry') == 1)
          <div class="col-sm-3">
            <div class="form-group">
              <div class="checkbox">
                <label>
                  {!! Form::checkbox('show_expiry', 1, false, ['class' => 'input-icheck']); !!} @lang('lang_v1.show_product_expiry')</label>
                </div>
            </div>
          </div>
        @endif
        @if(request()->session()->get('business.enable_lot_number') == 1)
          <div class="col-sm-3">
            <div class="form-group">
              <div class="checkbox">
                <label>
                  {!! Form::checkbox('show_lot', 1, false, ['class' => 'input-icheck']); !!} @lang('lang_v1.show_lot_number')</label>
                </div>
            </div>
          </div>
        @endif

        <div class="col-sm-3">
          <div class="form-group">
            <div class="checkbox">
              <label>
                {!! Form::checkbox('show_image', 1, false, ['class' => 'input-icheck']); !!} @lang('lang_v1.show_product_image')</label>
              </div>
          </div>
        </div>

        <div class="clearfix"></div>
        <div class="col-sm-3">
          <div class="form-group">
            <div class="checkbox">
              <label>
                {!! Form::checkbox('common_settings[show_warranty_name]', 1, false, ['class' => 'input-icheck']); !!} @lang('lang_v1.show_warranty_name')</label>
              </div>
          </div>
        </div>
        <div class="col-sm-3">
          <div class="form-group">
            <div class="checkbox">
              <label>
                {!! Form::checkbox('common_settings[show_warranty_exp_date]', 1, false, ['class' => 'input-icheck']); !!} @lang('lang_v1.show_warranty_exp_date')</label>
              </div>
          </div>
        </div>
        <div class="col-sm-3">
          <div class="form-group">
            <div class="checkbox">
              <label>
                {!! Form::checkbox('common_settings[show_warranty_description]', 1, false, ['class' => 'input-icheck']); !!} @lang('lang_v1.show_warranty_description')</label>
              </div>
          </div>
        </div>

      </div>
    </div>
  </div>
  <div class="box box-solid">
    <div class="box-body">
      <div class="row">
        <div class="col-sm-3">
          <div class="form-group">
            {!! Form::label('sub_total_label', __('invoice.sub_total_label') . ':' ) !!}
            {!! Form::text('sub_total_label', 'Subtotal', ['class' => 'form-control',
              'placeholder' => __('invoice.sub_total_label') ]); !!}
          </div>
        </div>
        <div class="col-sm-3">
          <div class="form-group">
            {!! Form::label('discount_label', __('invoice.discount_label') . ':' ) !!}
            {!! Form::text('discount_label', 'Discount', ['class' => 'form-control',
              'placeholder' => __('invoice.discount_label') ]); !!}
          </div>
        </div>
        <div class="col-sm-3">
          <div class="form-group">
            {!! Form::label('tax_label', __('invoice.tax_label') . ':' ) !!}
            {!! Form::text('tax_label', 'Tax', ['class' => 'form-control',
              'placeholder' => __('invoice.tax_label') ]); !!}
          </div>
        </div>
        <div class="col-sm-3">
          <div class="form-group">
            {!! Form::label('total_label', __('invoice.total_label') . ':' ) !!}
            {!! Form::text('total_label', 'Total', ['class' => 'form-control',
              'placeholder' => __('invoice.total_label') ]); !!}
          </div>
        </div>
        <div class="col-sm-3">
          <div class="form-group">
            {!! Form::label('total_due_label', __('invoice.total_due_label') . ' (' . __('lang_v1.current_sale') . '):' ) !!}
            {!! Form::text('total_due_label', 'Total Due', ['class' => 'form-control',
              'placeholder' => __('invoice.total_due_label') ]); !!}
          </div>
        </div>
        <div class="col-sm-3">
          <div class="form-group">
            {!! Form::label('paid_label', __('invoice.paid_label') . ':' ) !!}
            {!! Form::text('paid_label', 'Total Paid', ['class' => 'form-control',
              'placeholder' => __('invoice.paid_label') ]); !!}
          </div>
        </div>

        <div class="col-sm-3">
          <div class="form-group">
            <div class="checkbox">
              <label>
                {!! Form::checkbox('show_payments', 1, true, ['class' => 'input-icheck']); !!} @lang('invoice.show_payments')</label>
              </div>
          </div>
        </div>
        <!-- Barcode -->
        <div class="col-sm-3">
          <div class="form-group">
            <div class="checkbox">
              <label>
                {!! Form::checkbox('show_barcode', 1, false, ['class' => 'input-icheck']); !!} @lang('invoice.show_barcode')</label>
              </div>
          </div>
        </div>
        <div class="clearfix"></div>
        <div class="col-sm-3">
          <div class="form-group">
            {!! Form::label('prev_bal_label', __('invoice.total_due_label') . ' (' . __('lang_v1.all_sales') . '):' ) !!}
            {!! Form::text('prev_bal_label', 'All Balance Due', ['class' => 'form-control',
              'placeholder' => __('invoice.total_due_label') ]); !!}
          </div>
        </div>
        <div class="col-sm-5">
          <div class="form-group">
            <div class="checkbox">
              <label>
                {!! Form::checkbox('show_previous_bal', 1, false, ['class' => 'input-icheck']); !!} @lang('lang_v1.show_previous_bal_due')</label>
                @show_tooltip(__('lang_v1.previous_bal_due_help'))
              </div>
          </div>
        </div>
        <div class="col-sm-3">
          <div class="form-group">
            {!! Form::label('change_return_label', __('lang_v1.change_return_label') . ':' ) !!} @show_tooltip(__('lang_v1.change_return_help'))
            {!! Form::text('change_return_label', 'Change Return', ['class' => 'form-control',
              'placeholder' => __('lang_v1.change_return_label') ]); !!}
          </div>
        </div>

      </div>
    </div>
  </div>
	<div class="box box-solid">
    <div class="box-body">
      <div class="row">
        <div class="col-sm-6 hide">
          <div class="form-group">
            {!! Form::label('highlight_color', __('invoice.highlight_color') . ':' ) !!}
            {!! Form::text('highlight_color', '#000000', ['class' => 'form-control',
              'placeholder' => __('invoice.highlight_color') ]); !!}
          </div>
        </div>
        
        <div class="clearfix"></div>
        <div class="col-md-12 hide">
          <hr/>
        </div>

        <div class="col-sm-12">
          <div class="form-group">
            {!! Form::label('footer_text', __('invoice.footer_text') . ':' ) !!}
              {!! Form::textarea('footer_text', null, ['class' => 'form-control',
              'placeholder' => __('invoice.footer_text'), 'rows' => 3]); !!}
          </div>
        </div>
        <div class="col-sm-6">
          <div class="form-group">
            <br>
            <div class="checkbox">
              <label>
                {!! Form::checkbox('is_default', 1, false, ['class' => 'input-icheck']); !!} @lang('barcode.set_as_default')</label>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  @if(!empty($enabled_modules) && in_array('types_of_service', $enabled_modules) )
    @include('types_of_service.invoice_layout_settings')
  @endif
  
  <!-- Call restaurant module if defined -->
  @include('restaurant.partials.invoice_layout')

  @if(Module::has('Repair'))
    @include('repair::layouts.partials.invoice_layout_settings')
  @endif
  <div class="box box-solid">
    <div class="box-header with-border">
      <h3 class="box-title">@lang('lang_v1.layout_credit_note')</h3>
    </div>

    <div class="box-body">
      <div class="row">
        
        <div class="col-sm-3">
          <div class="form-group">
            {!! Form::label('cn_heading', __('lang_v1.cn_heading') . ':' ) !!}
            {!! Form::text('cn_heading', 'Credit Note', ['class' => 'form-control',
              'placeholder' => __('lang_v1.cn_heading') ]); !!}
          </div>
        </div>

        <div class="col-sm-3">
          <div class="form-group">
            {!! Form::label('cn_no_label', __('lang_v1.cn_no_label') . ':' ) !!}
            {!! Form::text('cn_no_label', 'Ref. No.', ['class' => 'form-control',
              'placeholder' => __('lang_v1.cn_no_label') ]); !!}
          </div>
        </div>

        <div class="col-sm-3">
          <div class="form-group">
            {!! Form::label('cn_amount_label', __('lang_v1.cn_amount_label') . ':' ) !!}
            {!! Form::text('cn_amount_label', 'Credit Amount', ['class' => 'form-control', 'placeholder' => __('lang_v1.cn_amount_label') ]); !!}
          </div>
        </div>
        <div class="col-sm-3">
          <button type="button" style="margin-right: 20px; margin-top:25px;" class="btn btn-info pull-right" id="preview_button2">@lang('invoice.preview')</button>
        </div>

      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-sm-12">
      <button type="submit" class="btn btn-primary pull-right">@lang('messages.save')</button>
    </div>
  </div>

  {!! Form::close() !!}


  <!-- Modal -->
  <div id="myModal" class="modal fade" role="dialog">
    <div class="modal-dialog" style="width:50%;">

      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">@lang('lang_v1.preview')</h4>
        </div>
        <div class="modal-body">
          <!-- business information here -->

          <div class="row">

            <!-- Logo -->
            <img src="" id="preview_logo_img" class="img img-responsive">
            <!-- Header text -->
            <div class="col-xs-12 header_text">
              <p>This is header text.</p>
            </div>

            <!-- business information here -->
            <div class="col-xs-12 text-center header-top">
              <h2 class="buiness_name">
                <!-- Shop & Location Name  -->
                <span class="busines_name">SYZYGY</span> <span class="busines_location"> SYZYGY</span>
              </h2>

              <!-- Address -->
              <p>
                <small class="address">
                  <span class="landmark">1</span><span class="city">Malabe,</span><span class="state">Western,</span><span class="zip">10115,</span><span class="country">Sri Lanka</span>
                </small>

              </p>
              <p>
                    <span class="sub_heading_1"> Heading Line 1: </span>
                    <span class="sub_heading_2">Sub Heading Line 2:</span>
                    <span class="sub_heading_3">Sub Heading Line 3:</span>
                    <span class="sub_heading_4">Sub Heading Line 4:</span>
                    <span class="sub_heading_5">Sub Heading Line 5:</span>
              </p>
              <p>

              </p>

              <!-- Title of receipt -->
              <h3 class="">
                <span class="invoice_heading">Invoice heading:</span> <span class="h_sufix_paid">Heading Suffix for paid:</span><span class="h_sufix_not_paid">Heading Suffix for not paid:</span>
              </h3>

              <!-- Invoice  number, Date  -->
              <p style="width: 100% !important" class="word-wrap">
                <span class="pull-left text-left word-wrap">
                  <b><span class="invoice_no_label">Invoice no. label:</span></b>
                  0035

                  <!-- Table information-->

                  <!-- customer info -->
                  <span class="customer_info">
                    <br />
                    <b><span class="customer_label">Customer</span></b> Walk-In Customer <br>
                    <br><br>
                  </span>
                    <br />
                    <span class="customer_id">
                    <b><span class="client_id_label">Client ID Label:</span></b> CO0001
                    </span>
                    <br />
                    <b><span class="client_tax_number">Client tax number label:</span></b>
                    <br />
                  
                    <span class="saleperson_show"><b><span class="sale_person_lable">Sales Person Label:</span></b> syzygy</span>
                </span>

                <span class="pull-right text-left">
                  <b><span class="date_label">Date Label:</span></b> Fri 05, 2019



                  <!-- Waiter info -->
                </span>
              </p>
            </div>

            <!-- /.col -->
          </div>


          <div class="row">
            <div class="col-xs-12">
              <br /><br />
              <table class="table table-responsive">
                <thead>
                  <tr>
                    <th class="product_label">Product Label:</th>
                    <th class="quantity_label">Quantity Label:</th>
                    <th class="unit_price_label">Unit Price Label:</th>
                    <th class="discount_label">Discount</th>
                    <th class="sub_total_label">Subtotal Label:</th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <td style="word-break: break-all;">
                      <img class="prduct_image" src="{{asset('/img/default.png')}}" alt="Image" width="50"
                        style="float: left; margin-right: 8px;">
                      FUEL HOSE <span class="category_code"> 4.5MM HO 4.5MM </span>
                      , <span class="sku">0053,</span><span class="brand">No Brand </span>


                    </td>
                    <td>1.00 Inch </td>
                    <td>10.00</td>
                    <td>0</td>
                    <td>10.00</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>

          <div class="row">
            <br />
            <div class="col-md-12">
              <hr />
            </div>
            <br />


            <div class="col-xs-6">

              <table class="table table-condensed">


                <!-- Total Paid-->
                <tr>
                  <th class="amount_paid_label">
                    Amount Paid Label:
                  </th>
                  <td>
                    10.00
                  </td>
                </tr>

                <!-- Total Due-->

                <tr>
                  <th class="total_due_label">
                    Total Due Label (All sales):
                  </th>
                  <td>
                    43.00
                  </td>
                </tr>
              </table>


            </div>

            <div class="col-xs-6">
              <div class="table-responsive">
                <table class="table">
                  <tbody>
                    <tr>
                      <th style="width:70%" class="subtotal_label">
                        Subtotal label::
                      </th>
                      <td>
                        10.00
                      </td>
                    </tr>

                    <!-- Shipping Charges -->

                    <!-- Discount -->


                    <!-- Tax -->

                    <!-- Total -->
                    <tr>
                      <th class="total_label">
                        Total label::
                      </th>
                      <td>
                        10.00
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>

          <div class="row barcode_image">
            <div class="col-xs-12">

              <img class="center-block"
                src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAHIAAAAeAQMAAADKJdgfAAAABlBMVEX///8nMDZ4PCBxAAAAAXRSTlMAQObYZgAAAAlwSFlzAAAOxAAADsQBlSsOGwAAAEZJREFUGJVj+Mx/+Dw/Dw+/wQeeD/bGBxhG+ah8ogC/nM0fZL7k5rQDqHy2H8h86e02Z5D5srvZeNDUo/HTUNQD7asgzmUApRJ5U5oM+GwAAAAASUVORK5CYII=">
            </div>
          </div>

          <div class="row">
            <div class="col-xs-12">
              <p class="footer_text">This is footer text.</p>
            </div>
          </div>

        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        </div>
      </div>

    </div>
  </div>
</section>
<!-- /.content -->


@endsection

@section('javascript')
<script>
  $('#preview_button').click(function(){

    show_preview();
    
  });
  $('#preview_button2').click(function(){

    show_preview();
    
  });
  
    $('.sub_heading_1').hide();
    $('.sub_heading_2').hide();
    $('.sub_heading_3').hide();
    $('.sub_heading_4').hide();
    $('.sub_heading_5').hide();
  function show_preview() {
    $('.header_text p').text(CKEDITOR.instances.header_text.getData().replace('<p>', '').replace('</p>', ''));

    $('.sub_heading_1').text($('#sub_heading_line1').val());
    $('.sub_heading_2').html('<br>'+$('#sub_heading_line2').val());
    $('.sub_heading_3').html('<br>'+$('#sub_heading_line3').val());
    $('.sub_heading_4').html('<br>'+$('#sub_heading_line4').val());
    $('.sub_heading_5').html('<br>'+$('#sub_heading_line5').val());
    
    
    $('.invoice_heading').text($('#invoice_heading').val());
    $('.h_sufix_not_paid').text($('#invoice_heading_not_paid').val());
    $('.h_sufix_paid').text($('#invoice_heading_paid').val());
    $('.invoice_no_label').text($('#invoice_no_prefix').val());
    $('.customer_label').text($('#customer_label').val());
    $('.client_id_label').text($('#client_id_label').val());
    $('.client_tax_number').text($('#client_tax_label').val());

    $('.sale_person_lable').text($('#sales_person_label').val());
    $('.date_label').text($('#date_label').val());

    $('.product_label').text($('#table_product_label').val());
    $('.quantity_label').text($('#table_qty_label').val());
    $('.unit_price_label').text($('#table_unit_price_label').val());
    $('.discount_label').text($('#discount_label').val());
    $('.sub_total_label').text($('#table_subtotal_label').val());

    $('.amount_paid_label').text($('#paid_label').val());
    $('.total_due_label').text($('#total_due_label').val());
    $('.subtotal_label').text($('#sub_total_label').val());
    $('.total_label').text($('#total_label').val());
    // ('.total_due_label').text($('#total_due_label').val());

    if($('#sub_heading_line1').val() != ''){
      $('.sub_heading_1').show();
    }
    if($('#sub_heading_line2').val() != ''){
      $('.sub_heading_2').show();
    }
    if($('#sub_heading_line3').val() != ''){
      $('.sub_heading_3').show();
    }
    if($('#sub_heading_line4').val() != ''){
      $('.sub_heading_4').show();
    }
    if($('#sub_heading_line5').val() != ''){
      $('.sub_heading_5').show();
    }

    if($('#invoice_heading').val() == ''){
      $('.invoice_heading').hide();
    }
    if($('#invoice_heading_not_paid').val() == ''){
      $('.h_sufix_not_paid').hide();
    }
    if($('#invoice_heading_paid').val() == ''){
      $('.h_sufix_paid').hide();
    }
    if($('#invoice_no_prefix').val() == ''){
      $('.invoice_no_label').hide();
    }
    if($('#customer_label').val() == ''){
      $('.customer_label').hide();
    }
    if($('#client_id_label').val() == ''){
      $('.client_id_label').hide();
    }
    if($('#client_tax_label').val() == ''){
      $('.client_tax_number').hide();
    }
    if($('#sales_person_label').val() == ''){
      $('.sale_person_lable').hide();
    }
    if($('#date_label').val() == ''){
      $('.date_label').hide();
    }
    if($('#table_product_label').val() == ''){
      $('.product_label').hide();
    }
    if($('#table_qty_label').val() == ''){
      $('.quantity_label').hide();
    }
    if($('#table_unit_price_label').val() == ''){
      $('.unit_price_label').hide();
    }
    if($('#discount_label').val() == ''){
      $('.discount_label').hide();
    }
    if($('#table_subtotal_label').val() == ''){
      $('.sub_total_label').hide();
    }
    if($('#paid_label').val() == ''){
      $('.amount_paid_label').hide();
    }
    if($('#total_due_label').val() == ''){
      $('.total_due_label').hide();
    }
    if($('#sub_total_label').val() == ''){
      $('.subtotal_label').hide();
    }
    if($('#total_label').val() == ''){
      $('.total_label').hide();
    }
   

    if($('input[name="show_business_name"]'). prop("checked") != true){
        $('.busines_name').hide();
    }
    if($('input[name="show_location_name"]'). prop("checked") != true){
        $('.busines_location').hide();
    }
    if($('input[name="show_customer"]'). prop("checked") != true){
        $('.customer_info').hide();
    }
    if($('input[name="show_sales_person"]'). prop("checked") != true){
        $('.saleperson_show').hide();
    }
    if($('input[name="show_client_id"]'). prop("checked") != true){
        $('.customer_id').hide();
    }

    
    if($('input[name="show_landmark"]'). prop("checked") != true){
        $('.landmark').hide();
    }

    if($('input[name="show_city"]'). prop("checked") != true){
        $('.city').hide();
    }

    if($('input[name="show_state"]'). prop("checked") != true){
        $('.state').hide();
    }

    if($('input[name="show_zip_code"]'). prop("checked") != true){
        $('.zip').hide();
    }

    if($('input[name="show_country"]'). prop("checked") != true){
        $('.country').hide();
    }


    if($('input[name="show_sku"]'). prop("checked") != true){
        $('.sku').hide();
    }
    if($('input[name="show_brand"]'). prop("checked") != true){
        $('.brand').hide();
    }
    if($('input[name="show_cat_code"]'). prop("checked") != true){
        $('.category_code').hide();
    }
   
    if($('input[name="show_image"]'). prop("checked") != true){
        $('.prduct_image').hide();
    }
   

    $('.footer_text').text(CKEDITOR.instances.footer_text.getData().replace('<p>', '').replace('</p>', ''));

    var align =  $('#header_align').val();
    $('.header-top').removeClass('text-center text-left text-right').addClass( 'text-'+ align);
    if(align == 'center'){
      $('#preview_logo_img').css('float', '');
      $('#preview_logo_img').addClass('center-block');
    }else{
      $('#preview_logo_img').removeClass('center-block');
      $('#preview_logo_img').css('float', align);
    }
      $('#preview_logo_img').css('height', $('#logo_height').val()+'px');
      $('#preview_logo_img').css('width', $('#logo_width').val()+'px');
      $('#preview_logo_img').css('margin-top', $('#logo_margin_top').val()+'px');
      $('#preview_logo_img').css('margin-bottom', $('#logo_margin_bottom').val()+'px');


    $('#myModal').modal('show');
  }

  
  function readURL(input) {
  console.log(input.files );
  
  if (input.files && input.files[0]) {
            var reader = new FileReader();

            reader.onload = function (e) {
              console.log( e.target.result);
                $('#preview_logo_img')
                    .attr('src', e.target.result)
                    .width(150)
                    .height(200);
            };

            reader.readAsDataURL(input.files[0]);
        }
};
</script>
@endsection