@extends('layouts.app')

@section('title', __('lang_v1.list_store_transactions'))



@section('content')

<style>
    .select2-search--dropdown.select2-search--hide{
        display: block;
    }
    
    td.diabled{
        color: #afafaf;
    }
    
</style>

@component('components.filters', ['title' => __('report.filters')])

        <div class="col-md-3">
          <div class="form-group">
            {!! Form::label('business_location',  __('lang_v1.business_location') . ':') !!}
            {!! Form::select('business_location', $business_locations, null, ['class' => 'form-control select2', 'style' => 'width:100%', 'placeholder' => __('lang_v1.all')]); !!}
          </div>
        </div>
        <div class="col-md-3">
          <div class="form-group">
            {!! Form::label('category',  __('lang_v1.category') . ':') !!}
            {!! Form::select('category', $categoryName, null, ['class' => 'form-control select2', 'style' => 'width:100%', 'placeholder' => __('lang_v1.all')]); !!}
          </div>
        </div>
        <div class="col-md-3">
          <div class="form-group">
            {!! Form::label('sub_category',  __('lang_v1.sub_category') . ':') !!}
            {!! Form::select('sub_category', $subcategoryName, null, ['class' => 'form-control select2', 'style' => 'width:100%', 'placeholder' => __('lang_v1.all')]); !!}
          </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
              {!! Form::label('products', __('lang_v1.products') . ':')!!}
              {!! Form::select('products', $productName, null, ['class' => 'form-control select2', 'style' => 'width:100%', 'placeholder' => __('lang_v1.all')]); !!}
            </div>
        </div>

         <div class="col-md-3">
          <div class="form-group">
            {!! Form::label('from_store', __('lang_v1.from_store') . ':')!!}
            {!! Form::select('from_store', $storeName, null, ['class' => 'form-control select2', 'style' => 'width:100%', 'placeholder' => __('lang_v1.all')]); !!}
          </div>
        </div>
        <div class="col-md-3">
          <div class="form-group">
            {!! Form::label('to_store', __('lang_v1.to_store') . ':')!!}
            {!! Form::select('to_store', $storeName, null, ['class' => 'form-control select2', 'style' => 'width:100%', 'placeholder' => __('lang_v1.all')]); !!}
          </div>
        </div>
        <div class="col-md-3">
          <div class="form-group">
            <label for="type">Type:</label>

            <select name="cars" id="type" class="form-control select2" style="width:100%">

                     <option selected="selected" value>All</option>

                    <option value="sell">Sell</option>

                    <option value="purchase">Purchase</option>

                     <option value="Stock_transfer">Stock_transfer</option>

            </select>

          </div>
        </div>
        <div class="col-md-3">
          <div class="form-group">
            {!! Form::label('user', __('lang_v1.user') . ':')!!}
            {!! Form::select('user', $userName, null, ['class' => 'form-control select2', 'style' => 'width:100%', 'placeholder' => __('lang_v1.all')]); !!}
          </div>
        </div>
        <div class="col-md-3">
          <div class="form-group">

                {!! Form::label('date_range', __('lang_v1.date_range') . ':') !!}

                {!! Form::text('date_range', null, ['placeholder' => __('lang_v1.select_a_date_range'), 'class' => 'form-control', 'readonly']); !!}

          </div>

        </div>

    @endcomponent



<!-- Content Header (Page header) -->

<section class="content-header no-print">

    <h1>@lang('All Store Transactions')
    </h1>

</section>



<!-- Main content -->

<section class="content no-print">

    @component('components.widget', ['class' => 'box-primary', 'title' => __('lang_v1.list_store_transactions')])

        @slot('tool')

            <div class="box-tools">

                <a class="btn btn-block btn-primary" href="{{action('StockTransferController@create')}}">

                <i class="fa fa-plus"></i> @lang('messages.add')</a>

            </div>

        @endslot

        <div class="table-responsive">

            <table class="table table-bordered table-striped" id="list_store_transactions">
              <thead>
                <th>@lang('lang_v1.date')</th>
                <th>@lang('lang_v1.ref_no')</th>
                <th>@lang('lang_v1.business_location')</th>
                <th>@lang('lang_v1.product')</th>
                <th>@lang('lang_v1.from_store')</th>
                <th>@lang('lang_v1.to_store')</th>
                <th>@lang('lang_v1.type')</th>
                <th>@lang('Quantity Issued')</th>
                <th>@lang('Quantity Recived')</th>
                <th> Avaliable Quantity</th>
                <th>@lang('User')</th>
                <th>@lang('Action')</th>
              </thead>
            </table>
        </div>

    @endcomponent

</section>

<section id="receipt_section_stock" class="print_section"></section>



<!-- /.content -->

@stop

@section('javascript')

<script type='text/javascript'>

  $(document).ready(function(){
      
    //   setTimeout(function(){ setQuantityShades() }, 10000);

      
      

    $('#category, #sub_category').change(function(){
        var $this = $(this);
        var cat = $('#category').val();
        var sub_cat = $('#sub_category').val();
        $.ajax({
            method: 'POST',
            url: '/products/get_sub_categories',
            dataType: 'html',
            data: { cat_id: cat },
            success: function(result) {
                if (result) {
                    if($this.attr('id') == 'category'){
                        $('#sub_category').html(result);
                    }
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
                    $('#products').html(result);
                }
            },
        });
    });
    if ($('#date_range').length == 1) {
        $('#date_range').daterangepicker(dateRangeSettings, function (start, end) {
            $('#date_range').val(
                start.format(moment_date_format) + ' ~ ' + end.format(moment_date_format)
            );
            expense_table.ajax.reload();
        });
        $('#date_range').on('cancel.daterangepicker', function (ev, picker) {
            $('#product_sr_date_filter').val('');
            expense_table.ajax.reload();
        });
        $('#date_range').data('daterangepicker').setStartDate(moment().startOf('month'));
        $('#date_range').data('daterangepicker').setEndDate(moment().endOf('month'));
    }

    list_store_transactions = $('#list_store_transactions').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '/List_Store_Transaction',
            data: function (d) {
                d.business_location = $('#business_location').val();
                d.category = $('#category').val();
                d.sub_category = $('#sub_category').val();
                d.products = $('#products').val();
                d.from_store = $('#from_store').val();
                d.to_store = $('#to_store').val();
                d.type = $('#type').val();
                d.user = $('#user').val();
                d.start_date = $('#date_range')
                    .data('daterangepicker')
                    .startDate.format('YYYY-MM-DD');
                d.end_date = $('#date_range')
                    .data('daterangepicker')
                    .endDate.format('YYYY-MM-DD');
            }
        },
        columnDefs: [
            {
                targets: 7,
                orderable: false,
                searchable: false,
            },
        ],
        columns: [
            { data: 'transaction_date', name: 'transaction_date' },
            { data: 'ref_no', name: 'ref_no' },
            { data: 'business_location', name: 'business_location' },
            { data: 'product', name: 'product' },
            { data: 'from_store', name: 'from_store' },
            { data: 'to_store', name: 'to_store' },
            { data: 'type', name: 'type' },
            { data: 'qty_issue', name: 'qty_issue' },
            { data: 'qty_recieve', name: 'qty_recieve' },
            { data: 'balance_qty', name: 'balance_qty' },
            { data: 'user', name: 'user' },
            { data: 'action', name: 'action' },
        ],
        fnDrawCallback: function(oSettings) {
            __currency_convert_recursively($('#list_store_transactions'));
        },
    }).on( 'draw.dt', function () {
        setQuantityShades()
    });

    $('#business_location, #category, #sub_category, #products, #from_store, #to_store, #type, #user, #date_range').change(function(){
        list_store_transactions.ajax.reload();
    })

  });
  
    function setQuantityShades(){
      
        $('#list_store_transactions tbody tr').each(function(){
            $tr = $(this);
            
            $from_store = $tr.find('td').eq(4);
            $to_store = $tr.find('td').eq(5);
            $quantity_issued = $tr.find('td').eq(7);
            $quantity_recieved = $tr.find('td').eq(8);
            
            if($quantity_issued.html() == '0.00'){
                $from_store.addClass('diabled');
            }
            
            if($quantity_recieved.html() == '0.00'){
                $to_store.addClass('diabled');
            }
            
        });
      
    }

</script>

@endsection