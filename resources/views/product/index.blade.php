@extends('layouts.app')
@section('title', __('sale.products'))

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
  <h1>@lang('sale.products')
    <small>@lang('lang_v1.manage_products')</small>
  </h1>
  <!-- <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Level</a></li>
        <li class="active">Here</li>
      </ol> -->
</section>

<!-- Main content -->
<section class="content">
  <div class="row">
    <div class="col-md-12">
      @component('components.filters', ['title' => __('report.filters')])
      <div class="col-md-3">
        <div class="form-group">
          {!! Form::label('type', __('product.product_type') . ':') !!}
          {!! Form::select('type', ['single' => __('lang_v1.single'), 'variable' => __('lang_v1.variable')],
          null, ['class' => 'form-control select2', 'style' => 'width:100%', 'id' =>
          'product_list_filter_type', 'placeholder' => __('lang_v1.all')]); !!}
        </div>
      </div>
      <div class="col-md-3">
        <div class="form-group">
          {!! Form::label('category_id', __('product.category') . ':') !!}
          {!! Form::select('category_id', $categories, null, ['class' => 'form-control select2 category_id',
          'style' =>
          'width:100%', 'id' => 'product_list_filter_category_id', 'placeholder' => __('lang_v1.all')]); !!}
        </div>
      </div>
      <div class="col-md-3">
        <div class="form-group">
          {!! Form::label('sub_category_id', __('product.sub_category') . ':') !!}
          {!! Form::select('sub_category_id', $sub_categories, null, ['class' => 'form-control select2
          sub_category_id', 'style' =>
          'width:100%', 'id' => 'product_list_filter_sub_category_id', 'placeholder' => __('lang_v1.all')]);
          !!}
        </div>
      </div>
      <div class="col-md-3">
        <div class="form-group">
          {!! Form::label('product_id', __('lang_v1.products') . ':') !!}
          {!! Form::select('product_id', $products, null, ['class' => 'form-control select2 product_id',
          'style' =>
          'width:100%', 'id' => 'product_list_filter_product_id', 'placeholder' => __('lang_v1.all')]); !!}
        </div>
      </div>
      @if(Module::has('Manufacturing'))
      @if($mf_module)
      <div class="col-md-3">
        <div class="form-group">
          {!! Form::label('only_manufactured_products', __('lang_v1.only_manufactured_products') . ':') !!}
          {!! Form::select('only_manufactured_products', $only_manufactured_products, null, ['class' =>
          'form-control select2', 'style' =>
          'width:100%', 'id' => 'product_list_filter_only_manufactured_products', 'placeholder' =>
          __('lang_v1.all')]); !!}
        </div>
      </div>
      @endif
      @endif

      <div class="col-md-3">
        <div class="form-group">
          {!! Form::label('unit_id', __('product.unit') . ':') !!}
          {!! Form::select('unit_id', $units, null, ['class' => 'form-control select2', 'style' =>
          'width:100%', 'id' => 'product_list_filter_unit_id', 'placeholder' => __('lang_v1.all')]); !!}
        </div>
      </div>
      <div class="col-md-3">
        <div class="form-group">
          {!! Form::label('tax_id', __('product.tax') . ':') !!}
          {!! Form::select('tax_id', $taxes, null, ['class' => 'form-control select2', 'style' =>
          'width:100%', 'id' => 'product_list_filter_tax_id', 'placeholder' => __('lang_v1.all')]); !!}
        </div>
      </div>
      <div class="col-md-3">
        <div class="form-group">
          {!! Form::label('brand_id', __('product.brand') . ':') !!}
          {!! Form::select('brand_id', $brands, null, ['class' => 'form-control select2', 'style' =>
          'width:100%', 'id' => 'product_list_filter_brand_id', 'placeholder' => __('lang_v1.all')]); !!}
        </div>
      </div>
      <div class="col-md-3" id="location_filter">
        <div class="form-group">
          {!! Form::label('location_id', __('purchase.business_location') . ':') !!}
          {!! Form::select('location_id', $business_locations, null, ['class' => 'form-control select2',
          'style' => 'width:100%', 'placeholder' => __('lang_v1.all')]); !!}
        </div>
      </div>

      <div class="col-sm-3">
        <div class="form-group">
          {!! Form::label('store_id', __('lang_v1.store_id').':') !!}
          <select name="store_id" id="store_id" class="form-control select2" required>
            <option value="">@lang('lang_v1.all')</option>
          </select>
        </div>
      </div>

      <div class="col-md-3">
        <div class="form-group">
          {!! Form::label('status', __('lang_v1.status').':') !!}
          {!! Form::select('active_state', ['active' => __('business.is_active'), 'inactive' =>
          __('lang_v1.inactive')], null, ['class' => 'form-control select2', 'style' => 'width:100%', 'id' =>
          'active_state', 'placeholder' => __('lang_v1.all')]); !!}
        </div>
      </div>
      <div class="col-sm-3">
        <div class="form-group">
          <br>
          <label>
            {!! Form::checkbox('not_for_selling', 1, false, ['class' => 'input-icheck', 'id' =>
            'not_for_selling']); !!} <strong>@lang('lang_v1.not_for_selling')</strong>
          </label>
        </div>
      </div>
      @endcomponent
    </div>
  </div>
  @can('product.view')
  <div class="row">
    <div class="col-md-12">
      <!-- Custom Tabs -->
      <div class="nav-tabs-custom">
        <ul class="nav nav-tabs">
          <li class="active">
            <a href="#product_list_tab" data-toggle="tab" aria-expanded="true"><i class="fa fa-cubes"
                aria-hidden="true"></i> @lang('lang_v1.all_products')</a>
          </li>

          <li>
            <a href="#product_stock_report" data-toggle="tab" aria-expanded="true"><i class="fa fa-hourglass-half"
                aria-hidden="true"></i> @lang('report.stock_report')</a>
          </li>
        </ul>

        <div class="tab-content">
          <div class="tab-pane active" id="product_list_tab">
            @can('product.create')
            <a class="btn btn-primary pull-right" href="{{action('ProductController@create')}}">
              <i class="fa fa-plus"></i> @lang('messages.add')</a>
            <br><br>
            @endcan
            @include('product.partials.product_list')
          </div>

          <div class="tab-pane" id="product_stock_report">
            @include('report.partials.report_summary_section')
            @include('report.partials.stock_report_table')
          </div>
        </div>
      </div>
    </div>
  </div>
  @endcan
  <input type="hidden" id="is_rack_enabled" value="{{$rack_enabled}}">

  <div class="modal fade product_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
  </div>

  <div class="modal fade" id="view_product_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
  </div>

  <div class="modal fade" id="opening_stock_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
  </div>

  @include('product.partials.edit_product_location_modal')

</section>
<!-- /.content -->

@endsection

@section('javascript')
<script src="{{ asset('js/product.js?v=' . $asset_v) }}"></script>
<script src="{{ asset('js/opening_stock.js?v=' . $asset_v) }}"></script>
<script type="text/javascript">
  var body = document.getElementsByTagName("body")[0];
    body.className += " sidebar-collapse";

  $(document).ready( function(){
              product_table = $('#product_table').DataTable({
                processing: true,
                serverSide: true,
                aaSorting: [[3, 'asc']],
                "ajax": {
                  "url": "/products",
                  "data": function ( d ) {
                    d.type = $('#product_list_filter_type').val();
                    d.category_id = $('#product_list_filter_category_id').val();
                    d.sub_category_id = $('#product_list_filter_sub_category_id').val();
                    d.product_id = $('#product_list_filter_product_id').val();
                    @if(Module::has('Manufacturing'))
                    @if($mf_module)
                    d.only_manufactured_product = $('#product_list_filter_only_manufactured_products').val();
                    @endif
                    @endif
                    d.brand_id = $('#product_list_filter_brand_id').val();
                    d.unit_id = $('#product_list_filter_unit_id').val();
                    d.tax_id = $('#product_list_filter_tax_id').val();
                    d.active_state = $('#active_state').val();
                    d.not_for_selling = $('#not_for_selling').is(':checked');
                    d.location_id = $('#location_id').val();
                  }
                },
                columnDefs: [ {
                  "targets": [0, 1, 2],
                  "orderable": false,
                  "searchable": false
                } ],
                columns: [
                { data: 'mass_delete'  },
                { data: 'image', name: 'products.image'  },
                { data: 'action', name: 'action'},
                { data: 'product', name: 'products.name'  },
                { data: 'units', name: 'units.name', searchable: false  },
                { data: 'product_locations', name: 'product_locations'  },
                @can('view_purchase_price')
                { data: 'purchase_price', name: 'max_purchase_price', searchable: false},
                @endcan
                @can('access_default_selling_price')
                { data: 'selling_price', name: 'max_price', searchable: false},
                @endcan
                { data: 'current_stock', searchable: false},
                { data: 'type', name: 'products.type'},
                { data: 'category', name: 'c1.name'},
                { data: 'brand', name: 'brands.name'},
                { data: 'tax', name: 'tax_rates.name', searchable: false},
                { data: 'sku', name: 'products.sku'},
                { data: 'product_custom_field1', name: 'products.product_custom_field1'  },
                { data: 'product_custom_field2', name: 'products.product_custom_field2'  },
                { data: 'product_custom_field3', name: 'products.product_custom_field3'  },
                { data: 'product_custom_field4', name: 'products.product_custom_field4'  }

                ],
                buttons: [

                {
                    extend: 'csv',
                    text: '<i class="fa fa-file"></i> Export to CSV',
                    className: 'btn btn-sm btn-default',
                    exportOptions: {
                      columns: function ( idx, data, node ) {
                          return $(node).is(":visible") && !$(node).hasClass('notexport') ?
                              true : false;
                      },
                    }
                },{
                    extend: 'excel',
                    text: '<i class="fa fa-file-excel-o"></i> Export to Excel',
                    className: 'btn btn-sm btn-default',
                    exportOptions: {
                      columns: function ( idx, data, node ) {
                          return $(node).is(":visible") && !$(node).hasClass('notexport') ?
                              true : false;
                      },
                    }
                },{
                    extend: 'print',
                    text: '<i class="fa fa-print"></i> Print',
                    className: 'btn btn-sm btn-default',
                    exportOptions: {
                      columns: function ( idx, data, node ) {
                          return $(node).is(":visible") && !$(node).hasClass('notexport') ?
                              true : false;
                      },
                    }
                },'colvis',
                {
                    extend: 'pdf',
                    text: '<i class="fa fa-file-pdf-o"></i> Export to PDF',
                    className: 'btn btn-sm btn-default',
                    exportOptions: {
                      columns: function ( idx, data, node ) {
                          return $(node).is(":visible") && !$(node).hasClass('notexport') ?
                              true : false;
                      },
                    }
                }],
                createdRow: function( row, data, dataIndex ) {
                  if($('input#is_rack_enabled').val() == 1){
                    var target_col = 0;
                    @can('product.delete')
                    target_col = 1;
                    @endcan
                    $( row ).find('td:eq('+target_col+') div').prepend('<i style="margin:auto;" class="fa fa-plus-circle text-success cursor-pointer no-print rack-details" title="' + LANG.details + '"></i>&nbsp;&nbsp;');
                  }
                  $( row ).find('td:eq(0)').attr('class', 'selectable_td');
                },
                fnDrawCallback: function(oSettings) {
                  __currency_convert_recursively($('#product_table'));
                },
              });
            // Array to track the ids of the details displayed rows
            var detailRows = [];

            $('#product_table tbody').on( 'click', 'tr i.rack-details', function () {
              var i = $(this);
              var tr = $(this).closest('tr');
              var row = product_table.row( tr );
              var idx = $.inArray( tr.attr('id'), detailRows );

              if ( row.child.isShown() ) {
                i.addClass( 'fa-plus-circle text-success' );
                i.removeClass( 'fa-minus-circle text-danger' );

                row.child.hide();

                    // Remove from the 'open' array
                    detailRows.splice( idx, 1 );
                  } else {
                    i.removeClass( 'fa-plus-circle text-success' );
                    i.addClass( 'fa-minus-circle text-danger' );

                    row.child( get_product_details( row.data() ) ).show();

                    // Add to the 'open' array
                    if ( idx === -1 ) {
                      detailRows.push( tr.attr('id') );
                    }
                  }
                });

            $('table#product_table tbody').on('click', 'a.delete-product', function(e){
              e.preventDefault();
              swal({
                title: LANG.sure,
                icon: "warning",
                buttons: true,
                dangerMode: true,
              }).then((willDelete) => {
                if (willDelete) {
                  var href = $(this).attr('href');
                  $.ajax({
                    method: "DELETE",
                    url: href,
                    dataType: "json",
                    success: function(result){
                      if(result.success == true){
                        toastr.success(result.msg);
                        product_table.ajax.reload();
                      } else {
                        toastr.error(result.msg);
                      }
                    }
                  });
                }
              });
            });

            $(document).on('click', '#delete-selected', function(e){
              e.preventDefault();
              var selected_rows = getSelectedRows();

              if(selected_rows.length > 0){
                $('input#selected_rows').val(selected_rows);
                swal({
                  title: LANG.sure,
                  icon: "warning",
                  buttons: true,
                  dangerMode: true,
                }).then((willDelete) => {
                  if (willDelete) {
                    $('form#mass_delete_form').submit();
                  }
                });
              } else{
                $('input#selected_rows').val('');
                swal('@lang("lang_v1.no_row_selected")');
              }    
            });

            $(document).on('click', '#deactivate-selected', function(e){
              e.preventDefault();
              var selected_rows = getSelectedRows();

              if(selected_rows.length > 0){
                $('input#selected_products').val(selected_rows);
                swal({
                  title: LANG.sure,
                  icon: "warning",
                  buttons: true,
                  dangerMode: true,
                }).then((willDelete) => {
                  if (willDelete) {
                    var form = $('form#mass_deactivate_form')

                    var data = form.serialize();
                    $.ajax({
                      method: form.attr('method'),
                      url: form.attr('action'),
                      dataType: 'json',
                      data: data,
                      success: function(result) {
                        if (result.success == true) {
                          toastr.success(result.msg);
                          product_table.ajax.reload();
                          form
                          .find('#selected_products')
                          .val('');
                        } else {
                          toastr.error(result.msg);
                        }
                      },
                    });
                  }
                });
              } else{
                $('input#selected_products').val('');
                swal('@lang("lang_v1.no_row_selected")');
              }    
            })

            $(document).on('click', '#edit-selected', function(e){
              e.preventDefault();
              var selected_rows = getSelectedRows();

              if(selected_rows.length > 0){
                $('input#selected_products_for_edit').val(selected_rows);
                $('form#bulk_edit_form').submit();
              } else{
                $('input#selected_products').val('');
                swal('@lang("lang_v1.no_row_selected")');
              }    
            })

            $('table#product_table tbody').on('click', 'a.activate-product', function(e){
              e.preventDefault();
              var href = $(this).attr('href');
              $.ajax({
                method: "get",
                url: href,
                dataType: "json",
                success: function(result){
                  if(result.success == true){
                    toastr.success(result.msg);
                    product_table.ajax.reload();
                  } else {
                    toastr.error(result.msg);
                  }
                }
              });
            });

            $(document).on('change', '#product_list_filter_only_manufactured_products, #product_list_filter_product_id, #product_list_filter_sub_category_id, #product_list_filter_type, #product_list_filter_category_id, #product_list_filter_brand_id, #product_list_filter_unit_id, #product_list_filter_tax_id, #location_id, #active_state, #store_id', 
              function() {

                if ($("#product_list_tab").hasClass('active')) {
                  product_table.ajax.reload();
                }

                if ($("#product_stock_report").hasClass('active')) {
                  stock_report_table.ajax.reload();
                }

                if($('#product_list_filter_product_id').val() !== '' && $('#product_list_filter_product_id').val() !== undefined){
                  $('.product').text($('#product_list_filter_product_id :selected').text());
                }else{
                  $('.product').text('All');
                }
                if($('#product_list_filter_category_id').val() !== '' && $('#product_list_filter_category_id').val() !== undefined){
                  $('.category').text($('#product_list_filter_category_id :selected').text());
                }else{
                  $('.category').text('All');
                }
                if($('#product_list_filter_sub_category_id').val() !== '' && $('#product_list_filter_sub_category_id').val() !== undefined){
                  $('.sub_category').text($('#product_list_filter_sub_category_id :selected').text());
                }else{
                  $('.sub_category').text('All');
                }
                summaryUpdate();
              });

            $(document).on('ifChanged', '#not_for_selling', function(){
              if ($("#product_list_tab").hasClass('active')) {
                product_table.ajax.reload();
              }

              if ($("#product_stock_report").hasClass('active')) {
                stock_report_table.ajax.reload();
              }
            });

            $('#product_location').select2({dropdownParent: $('#product_location').closest('.modal')});

            summaryUpdate();
          });

$(document).on('shown.bs.modal', 'div.view_product_modal, div.view_modal', function(){
  __currency_convert_recursively($(this));
});
var data_table_initailized = false;
$('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
  if ($(e.target).attr('href') == '#product_stock_report') {
    if (!data_table_initailized) {
                    //Stock report table
                    var stock_report_cols = [
                    { data: 'sku', name: 'variations.sub_sku' },
                    { data: 'product', name: 'p.name' },
                    { data: 'units', name: 'units.name', searchable: false },
                    { data: 'unit_purchase_price', name: 'variations.dpp_inc_tax' },
                    { data: 'unit_price', name: 'variations.sell_price_inc_tax' },
                    { data: 'stock', name: 'stock', searchable: false },
                    { data: 'stock_price', name: 'stock_price', searchable: false },
                    { data: 'total_sold', name: 'total_sold', searchable: false },
                    { data: 'total_sold_value', name: 'total_sold_value', searchable: false },
                    { data: 'total_purchase', name: 'total_purchase', searchable: false },
                    { data: 'total_transfered', name: 'total_transfered', searchable: false },
                    { data: 'total_adjusted', name: 'total_adjusted', searchable: false },
                    { data: 'total_purchase_return', name: 'total_purchase_return', searchable: false },
                    { data: 'total_sold_return', name: 'total_sold_return', searchable: false }
                    ];
                    if ($('th.current_stock_mfg').length) {
                      stock_report_cols.push({ data: 'total_mfg_stock', name: 'total_mfg_stock', searchable: false });
                    }
                    stock_report_table = $('#stock_report_table').DataTable({
                      processing: true,
                      serverSide: true,
                      ajax: {
                        url: '/reports/stock-report',
                        data: function(d) {
                          d.location_id = $('#location_id').val();
                          d.category_id = $('#product_list_filter_category_id').val();
                          d.sub_category_id = $('#product_list_filter_sub_category_id').val();
                          d.product_id = $('#product_list_filter_product_id').val();
                          @if(Module::has('Manufacturing'))
                          @if($mf_module)
                          d.only_manufactured_product = $('#product_list_filter_only_manufactured_products').val();
                          @endif
                          @endif
                          d.brand_id = $('#product_list_filter_brand_id').val();
                          d.unit_id = $('#product_list_filter_unit_id').val();
                          d.type = $('#product_list_filter_type').val();
                          d.active_state = $('#active_state').val();
                          d.not_for_selling = $('#not_for_selling').is(':checked');
                          d.store_id = $('#store_id').val();
                        }
                      },
                      columns: stock_report_cols,
                      fnDrawCallback: function(oSettings) {
                        $('#footer_total_stock').html(__sum_stock($('#stock_report_table'), 'current_stock'));
                        $('#footer_total_sold').html(__sum_stock($('#stock_report_table'), 'total_sold'));
                        $('#footer_total_transfered').html(
                          __sum_stock($('#stock_report_table'), 'total_transfered')
                          );
                        $('#footer_total_adjusted').html(
                          __sum_stock($('#stock_report_table'), 'total_adjusted')
                          );
                        var total_stock_price = sum_table_col($('#stock_report_table'), 'total_stock_price');
                        $('#footer_total_stock_price').text(total_stock_price);
                        var total_sold_value = sum_table_col($('#stock_report_table'), 'total_total_sold_value');
                        $('#footer_total_sold_value').text(total_sold_value);
                        __currency_convert_recursively($('#stock_report_table'));
                      },
                    });
                    data_table_initailized = true;
                  } else {
                    stock_report_table.ajax.reload();
                  }
                } else {
                  product_table.ajax.reload();
                }
              });

function getSelectedRows() {
  var selected_rows = [];
  var i = 0;
  $('.row-select:checked').each(function () {
    selected_rows[i++] = $(this).val();
  });

  return selected_rows; 
}

$(document).on('click', '.update_product_location', function(e){
  e.preventDefault();
  var selected_rows = getSelectedRows();

  if(selected_rows.length > 0){
    $('input#selected_products').val(selected_rows);
    var type = $(this).data('type');
    var modal = $('#edit_product_location_modal');
    if(type == 'add') {
      modal.find('.remove_from_location_title').addClass('hide');
      modal.find('.add_to_location_title').removeClass('hide');
    } else if(type == 'remove') {
      modal.find('.add_to_location_title').addClass('hide');
      modal.find('.remove_from_location_title').removeClass('hide');
    }

    modal.modal('show');
    modal.find('#product_location').select2({ dropdownParent: modal });
    modal.find('#product_location').val('').change();
    modal.find('#update_type').val(type);
    modal.find('#products_to_update_location').val(selected_rows);
  } else{
    $('input#selected_products').val('');
    swal('@lang("lang_v1.no_row_selected")');
  }    
});

$(document).on('submit', 'form#edit_product_location_form', function(e) {
  e.preventDefault();
  $(this)
  .find('button[type="submit"]')
  .attr('disabled', true);
  var data = $(this).serialize();

  $.ajax({
    method: $(this).attr('method'),
    url: $(this).attr('action'),
    dataType: 'json',
    data: data,
    success: function(result) {
      if (result.success == true) {
        $('div#edit_product_location_modal').modal('hide');
        toastr.success(result.msg);
        product_table.ajax.reload();
        $('form#edit_product_location_form')
        .find('button[type="submit"]')
        .attr('disabled', false);
      } else {
        toastr.error(result.msg);
      }
    },
  });
});


$('#location_id').change(function() {
  let check_store_not = null;
  $.ajax({
   method: 'get',
   url: '/stock-transfer/get_transfer_store_id/'+$('#location_id').val(),
   data: { check_store_not: check_store_not},
   success: function(result) {

    $('#store_id').empty();
    $.each(result, function(i, location) {
     $('#store_id').append(`<option value= "`+location.id+`">`+location.name+`</option>`);
   });
    $("#store_id").change();
  },
});

  stock_report_table.ajax.reload();
});


function summaryUpdate(){
  var product_id = $('#product_list_filter_product_id').val();
  var category_id = $('#product_list_filter_category_id').val();
  var sub_category_id = $('#product_list_filter_sub_category_id').val();
  var location_id = $('#purchase_sell_location_filter').val();

  var data = { product_id: product_id, category_id: category_id, sub_category_id: sub_category_id, location_id: location_id };

  var loader = __fa_awesome();
  $('.opening_qty').html(loader);
  $('.opening_amount').html(loader);
  $('.purchase_qty').html(loader);
  $('.purchase_amount').html(loader);
  $('.sold_qty').html(loader);
  $('.sold_amount').html(loader);
  $('.balance_qty').html(loader);
  $('.balance_amount').html(loader);

  $.ajax({
    method: 'GET',
    url: '/reports/get-product-transaction-summary',
    dataType: 'json',
    data: data,
    success: function(data) {
      $('.sold_qty').html(__number_f(data.sold_qty));
      $('.purchase_qty').html(__number_f(data.purchase_qty));
      $('.opening_qty').html(__number_f(data.opening_qty));
      $('.balance_qty').html(__number_f(data.balance_qty));
      $('.sold_amount').html(__currency_trans_from_en(data.sold_amount));
      $('.purchase_amount').html(__currency_trans_from_en(data.purchase_amount));
      $('.opening_amount').html(__currency_trans_from_en(data.opening_amount));
      $('.balance_amount').html(__currency_trans_from_en(data.balance_amount));
    },
  });
}

function printDiv() {
  $('.remove-print').removeClass('table-responsive');
  var w = window.open('', '_self');
  var html = '<div style="width: 100%; text-align:center"><h3>{{request()->session()->get("business.name")}}</h3></div>' +document.getElementById("summary_div").innerHTML  + document.getElementById("table_div").innerHTML;
  $(w.document.body).html(html);
  w.print();
  w.close();
  window.location.href = "{{URL::to('/')}}/products";
}


$('.category_id, .sub_category_id').change(function(){
  var cat = $('#product_list_filter_category_id').val();
  var sub_cat = $('#product_list_filter_sub_category_id').val();
  $.ajax({
    method: 'POST',
    url: '/products/get_sub_categories',
    dataType: 'html',
    data: { cat_id: cat },
    success: function(result) {
      if (result) {
        $('#product_list_filter_sub_category_id').html(result);
      }
    },
  });
  $.ajax({
    method: 'POST',
    url: '/products/get_product_category_wise',
    dataType: 'html',
    data: { cat_id: cat , sub_cat_id: sub_cat },
    success: function(result) {
      if (result) {
        $('#product_list_filter_product_id').html(result);
      }
    },
  });
});
</script>
@endsection