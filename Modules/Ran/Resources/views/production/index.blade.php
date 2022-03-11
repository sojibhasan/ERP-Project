@extends('layouts.app')
@section('title', __('ran::lang.production'))

@section('content')

<section class="content-header">
    <div class="row">
        <div class="col-md-12 dip_tab">
            <div class="settlement_tabs">
                <ul class="nav nav-tabs">
                    <li class="@if(empty(session('status.tab'))) active @endif" style="margin-left: 20px;">
                        <a style="font-size:13px;" href="#production" class="" data-toggle="tab">
                            <i class="fa fa-superpowers"></i> <strong>@lang('ran::lang.production')</strong>
                        </a>
                    </li>
                    <li class="@if(session('status.tab') =='work_order') active @endif">
                        <a style="font-size:13px;" href="#work_order" data-toggle="tab">
                            <i class="fa fa-sort"></i> <strong>@lang('ran::lang.work_order')</strong>
                        </a>
                    </li>
                    <li class="@if(session('status.tab') =='receive') active @endif">
                        <a style="font-size:13px;" href="#receive_work_order" data-toggle="tab">
                            <i class="fa fa-arrow-down"></i> <strong>@lang('ran::lang.receive_work_order')</strong>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <div class="tab-content">
        <div class="tab-pane @if(empty(session('status.tab'))) active @endif" id="production">
            @include('ran::production.production.index')
        </div>
        <div class="tab-pane @if(session('status.tab') =='work_order') active @endif" id="work_order">
            @include('ran::production.work_order.index')
        </div>
        <div class="tab-pane @if(session('status.tab') =='receive') active @endif" id="receive_work_order">
            @include('ran::production.receive_work_order.index')
        </div>
    </div>

    <div class="modal fade production_modal" role="dialog" aria-labelledby="gridSystemModalLabel">
   
    </div>
</section>

@endsection
@section('javascript')
<script type="text/javascript">
   
    $(document).ready( function(){

    var columns = [
            { data: 'created_at', name: 'created_at' },
            { data: 'reference_no', name: 'reference_no' },
            { data: 'goldsmith', name: 'goldsmith' },
            { data: 'product_qty', name: 'product_qty' },
            // { data: 'warehouse', name: 'warehouse' },
            { data: 'business_location', name: 'business_location' },
            { data: 'total_product_gold_weight', name: 'total_product_gold_weight' },
            { data: 'total_stone_other_weight', name: 'total_stone_other_weight' },
            { data: 'wastage_calculation', name: 'wastage_calculation' },
            { data: 'total_gold_wastage', name: 'total_gold_wastage' },
            { data: 'total_goldsmith_in_g', name: 'total_goldsmith_in_g' },
            { data: 'other_cost', name: 'other_cost' },
            // { data: 'action', searchable: false, orderable: false },
        ];
  
    list_production_table = $('#list_production_table').DataTable({
        processing: true,
        serverSide: true,
        aaSorting: [[0, 'desc']],
        ajax: '{{action('\Modules\Ran\Http\Controllers\ProductionController@index')}}',
        @include('layouts.partials.datatable_export_button')
        columns: columns,
        fnDrawCallback: function(oSettings) {
        
        },
    });

    $(document).on('click', 'a.delete_reference_button', function(e) {
		var page_details = $(this).closest('div.page_details')
		e.preventDefault();
        swal({
            title: LANG.sure,
            icon: 'warning',
            buttons: true,
            dangerMode: true,
        }).then(willDelete => {
            if (willDelete) {
                var href = $(this).attr('href');
                var data = $(this).serialize();
                console.log(href);
                $.ajax({
                    method: 'DELETE',
                    url: href,
                    dataType: 'json',
                    data: data,
                    success: function(result) {
                        if (result.success == true) {
                            page_details.remove();
                            toastr.success(result.msg);
                        } else {
                            toastr.error(result.msg);
                        }
                        list_production_table.ajax.reload();
                    },
                });
            }
        });
    });
  
    work_order_table = $('#work_order_table').DataTable({
        processing: true,
        serverSide: true,
        aaSorting: [[0, 'desc']],
        ajax: '{{action('\Modules\Ran\Http\Controllers\WorkOrderController@index')}}',
        @include('layouts.partials.datatable_export_button')
        columns: [
            { data: 'date_and_time', name: 'date_and_time' },
            { data: 'business_location', name: 'business_location' },
            { data: 'work_order_no', name: 'customer_order_no' },
            { data: 'customer_order_no', name: 'work_order_no' },
            { data: 'goldsmith', name: 'goldsmith' },
            { data: 'received_work_order_no', name: 'received_work_order_no' },
            { data: 'order_delivery_date', name: 'order_delivery_date' },
            { data: 'action', searchable: false, orderable: false },
        ],
        fnDrawCallback: function(oSettings) {
        
        },
    });

    $(document).on('click', 'a.work_order', function(e) {
		e.preventDefault();
        swal({
            title: LANG.sure,
            icon: 'warning',
            buttons: true,
            dangerMode: true,
        }).then(willDelete => {
            if (willDelete) {
                var href = $(this).attr('href');
                $.ajax({
                    method: 'DELETE',
                    url: href,
                    dataType: 'json',
                    success: function(result) {
                        if (result.success == true) {
                            toastr.success(result.msg);
                        } else {
                            toastr.error(result.msg);
                        }
                        work_order_table.ajax.reload();
                    },
                });
            }
        });
    });
  
    receive_work_order_table = $('#receive_work_order_table').DataTable({
        processing: true,
        serverSide: true,
        aaSorting: [[0, 'desc']],
        ajax: '{{action('\Modules\Ran\Http\Controllers\ReceiveWorkOrderController@index')}}',
        @include('layouts.partials.datatable_export_button')
        columns: [
            { data: 'date_and_time', name: 'date_and_time' },
            { data: 'business_location', name: 'business_location' },
            { data: 'receive_work_order_no', name: 'receive_work_order_no' },
            { data: 'goldsmith', name: 'goldsmith' },
            { data: 'work_order_no', name: 'work_order_no' },
            { data: 'product', name: 'products.name' },
            { data: 'item_weight', name: 'item_weight' },
            { data: 'action', searchable: false, orderable: false },
        ],
        fnDrawCallback: function(oSettings) {
        
        },
    });

    $(document).on('click', 'a.delete-receive-work-order', function(e) {
		e.preventDefault();
        swal({
            title: LANG.sure,
            icon: 'warning',
            buttons: true,
            dangerMode: true,
        }).then(willDelete => {
            if (willDelete) {
                var href = $(this).attr('href');
                $.ajax({
                    method: 'DELETE',
                    url: href,
                    dataType: 'json',
                    success: function(result) {
                        if (result.success == true) {
                            toastr.success(result.msg);
                        } else {
                            toastr.error(result.msg);
                        }
                        receive_work_order_table.ajax.reload();
                    },
                });
            }
        });
    });

});
</script>
@endsection