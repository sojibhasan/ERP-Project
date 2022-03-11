@extends('layouts.app')
@section('title', __('lang_v1.stock_transfer_requests'))

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header no-print">
    <h1>@lang('lang_v1.stock_transfer_requests')
    </h1>
</section>

<!-- Main content -->
<section class="content no-print">
    <div class="row">
        <div class="col-md-12">
            @component('components.filters', ['title' => __('report.filters')])
            <div class="col-md-3 col-xs-6">
                <label for="request_location">@lang('lang_v1.request_location'):</label>
                {!! Form::select('request_location', $business_locations, null, ['class' => 'form-control
                select2',
                'placeholder' =>__('lang_v1.all'), 'style' => 'width: 100%', 'id' => 'request_location']) !!}
            </div>
            <div class="col-md-3 col-xs-6">
                <label for="request_to_location">@lang('lang_v1.request_to_location'):</label>
                {!! Form::select('request_to_location', $business_locations, null, ['class' => 'form-control
                select2',
                'placeholder' =>__('lang_v1.all'), 'style' => 'width: 100%', 'id' => 'request_to_location']) !!}
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('date_range', __('report.date_range') . ':') !!}
                    {!! Form::text('date_range', @format_date('first day of this month') . ' ~ ' . @format_date('last
                    day of this month') , ['placeholder' => __('lang_v1.select_a_date_range'), 'class' =>
                    'form-control', 'id' => 'date_range', 'readonly']); !!}
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('category_id',__('lang_v1.category').':') !!}
                    {!! Form::select('category_id', $categories, null, ['placeholder' =>
                    __('report.all'), 'class' => 'form-control select2', 'style' => 'width:100%', 'id' =>
                    'category_id']); !!}
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('sub_category_id',__('lang_v1.sub_category').':') !!}
                    {!! Form::select('sub_category_id', $categories, null, ['placeholder' =>
                    __('report.all'), 'class' => 'form-control select2', 'style' => 'width:100%', 'id' =>
                    'sub_category_id']); !!}
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('product_id',__('lang_v1.products').':') !!}
                    {!! Form::select('product_id', $products, null, ['placeholder' =>
                    __('report.all'), 'class' => 'form-control select2', 'style' => 'width:100%', 'id' =>
                    'product_id']); !!}
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('status',__('lang_v1.status').':') !!}
                    {!! Form::select('status', ['requested' => __('lang_v1.status'), 'issued' => __('lang_v1.issued'),
                    'transit' => __('lang_v1.transit'), 'received' => __('lang_v1.received')], null, ['placeholder' =>
                    __('report.all'), 'class' => 'form-control select2', 'style' => 'width:100%', 'id' =>
                    'status']); !!}
                </div>
            </div>

            @endcomponent
        </div>
    </div>

    @component('components.widget', ['class' => 'box-primary', 'title' => __('lang_v1.all_stock_transfer_requests')])
    @slot('tool')
    <div class="box-tools">
        <button type="button" class="btn btn-block btn-primary btn-modal"
            data-href="{{action('StockTransferRequestController@create')}}" data-container=".stock_transfer_modal">
            <i class="fa fa-plus"></i> @lang( 'lang_v1.add_request' )</button>
    </div>
    @endslot
    <div class="table-responsive">
        <table class="table table-bordered table-striped" id="stock_transfer_table">
            <thead>
                <tr>
                    <th>@lang('messages.action')</th>
                    <th>@lang('messages.date')</th>
                    <th>@lang('lang_v1.request_location')</th>
                    <th>@lang('lang_v1.request_to_location')</th>
                    <th>@lang('lang_v1.product')</th>
                    <th>@lang('lang_v1.qty')</th>
                    <th>@lang('lang_v1.status')</th>
                    <th>@lang('lang_v1.received_status')</th>
                    <th>@lang('lang_v1.user')</th>
                </tr>
            </thead>
        </table>
    </div>
    @endcomponent
</section>

<div class="modal fade stock_transfer_modal" role="dialog" aria-labelledby="gridSystemModalLabel">
</div>

<section id="receipt_section" class="print_section"></section>

<!-- /.content -->
@stop
@section('javascript')
<script>
    $('#category_id, #sub_category_id').change(function(){
        var cat = $('#category_id').val();
        var sub_cat = $('#sub_category_id').val();
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
        $.ajax({
            method: 'POST',
            url: '/products/get_product_category_wise',
            dataType: 'html',
            data: { cat_id: cat , sub_cat_id: sub_cat },
            success: function(result) {
                if (result) {
                    $('#product_id').html(result);
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


     //employee list
     stock_transfer_table = $('#stock_transfer_table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '{{action("StockTransferRequestController@index")}}',
            data: function (d) {
                
                d.request_location = $('#request_location').val();
                d.request_to_location = $('#request_to_location').val();
                d.category_id = $('#category_id').val();
                d.sub_category_id = $('#sub_category_id').val();
                d.product_id = $('#product_id').val();
                d.status = $('#status').val();
                d.start_date = $('#date_range')
                    .data('daterangepicker')
                    .startDate.format('YYYY-MM-DD');
                d.end_date = $('#date_range')
                    .data('daterangepicker')
                    .endDate.format('YYYY-MM-DD');
            }
        },
        columns: [
            { data: 'action', name: 'action' },
            { data: 'created_at', name: 'created_at' },
            { data: 'rl', name: 'rl.name' },
            { data: 'rtl', name: 'rtl.name' },
            { data: 'product_name', name: 'products.name' },
            { data: 'qty', name: 'qty' },
            { data: 'status', name: 'status' },
            { data: 'received_status', name: 'received_status' },
            { data: 'username', name: 'users.username' },
        ],
        fnDrawCallback: function (oSettings) {
          
        },
    });

    $('#request_location, #request_to_location, #category_id, #sub_category_id, #status, #date_range').change(function(){
        stock_transfer_table.ajax.reload();
    })

    $(document).on('click', 'a.delete-request', function(){
            swal({
                title: LANG.sure,
                icon: "warning",
                buttons: true,
                dangerMode: true,
            }).then((willDelete)=>{
                if(willDelete){
                    let href = $(this).data('href');

                    $.ajax({
                        method: 'delete',
                        url: href,
                        data: {  },
                        success: function(result) {
                            if(result.success == 1){
                                toastr.success(result.msg);
                            }else{
                                toastr.error(result.msg);
                            }
                            stock_transfer_table.ajax.reload();
                        },
                    });
                }
            });
        })
</script>
@endsection