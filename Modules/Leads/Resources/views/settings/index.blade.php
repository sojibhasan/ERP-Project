@extends('layouts.app')
@section('title', __('leads::lang.settings'))

@section('content')
<!-- Main content -->
<section class="content">
    <div class="row">
        <div class="col-md-12">
            <div class="settlement_tabs">
                <ul class="nav nav-tabs">
                    <li class="@if(empty(session('status.tab'))) active @endif">
                        <a href="#district" class="district" data-toggle="tab">
                            <i class="fa fa-file-text-o"></i>
                            <strong>@lang('leads::lang.district')</strong>
                        </a>
                    </li>
                    <li class="@if(session('status.tab') =='town') active @endif">
                        <a href="#town" class="town" data-toggle="tab">
                            <i class="fa fa-file-text-o"></i>
                            <strong>@lang('leads::lang.town')</strong>
                        </a>
                    </li>
                    <li class="@if(session('status.tab') =='category') active @endif">
                        <a href="#category" class="category" data-toggle="tab">
                            <i class="fa fa-file-text-o"></i>
                            <strong>@lang('leads::lang.category')</strong>
                        </a>
                    </li>

                </ul>
                <div class="tab-content">
                    <div class="tab-pane @if(empty(session('status.tab'))) active @endif" id="district">
                        @include('leads::settings.partials.district')
                    </div>
                    <div class="tab-pane @if(session('status.tab') =='town') active @endif" id="town">
                        @include('leads::settings.partials.town')
                    </div>
                    <div class="tab-pane @if(session('status.tab') =='category') active @endif" id="category">
                        @include('leads::settings.partials.category')
                    </div>

                </div>
            </div>
        </div>
    </div>
    <div class="modal fade district_model" role="dialog" aria-labelledby="gridSystemModalLabel">
    </div>
    <div class="modal fade town_model" role="dialog" aria-labelledby="gridSystemModalLabel">
    </div>
    <div class="modal fade category_model" role="dialog" aria-labelledby="gridSystemModalLabel">
    </div>
</section>
<!-- /.content -->

@endsection
@section('javascript')
<script>
    // districts_table
        districts_table = $('#districts_table').DataTable({
            processing: true,
            serverSide: false,
            ajax: "{{action('\Modules\Leads\Http\Controllers\DistrictController@index')}}",
            columnDefs:[{
                    "targets": 1,
                    "orderable": false,
                    "searchable": false
                }],
            columns: [
                {data: 'date', name: 'date'},
                {data: 'name', name: 'name'},
                {data: 'action', name: 'action'}
            ],
            "fnDrawCallback": function (oSettings) {
            }
        });

        $(document).on('click', 'button.district_delete', function(){
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
                            districts_table.ajax.reload();
                        },
                    });
                }
            });
        })
    // towns_table
        towns_table = $('#towns_table').DataTable({
            processing: true,
            serverSide: false,
            ajax: {
                url: "{{action('\Modules\Leads\Http\Controllers\TownController@index')}}",
                data: function(d){
                    d.user = $('#users_fitler_town').val();
                    d.district = $('#district_fitler_town').val();
                }
            },
            columnDefs:[{
                    "targets": 1,
                    "orderable": false,
                    "searchable": false
                }],
            columns: [
                {data: 'date', name: 'date'},
                {data: 'district', name: 'district'},
                {data: 'name', name: 'name'},
                {data: 'action', name: 'action'}
            ],
            "fnDrawCallback": function (oSettings) {
            }
        });

        $(document).on('click', 'button.town_delete', function(){
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
                            towns_table.ajax.reload();
                        },
                    });
                }
            });
        });

        $('#district_fitler_town, #users_fitler_town').change(function(){
            towns_table.ajax.reload();
        })

        if ($('#date_range_filter_category').length == 1) {
            $('#date_range_filter_category').daterangepicker(dateRangeSettings, function(start, end) {
                $('#date_range_filter_category').val(
                start.format(moment_date_format) + ' - ' +  end.format(moment_date_format)
                );
            });
            $('#date_range_filter_category').on('cancel.daterangepicker', function(ev, picker) {
                $('#product_sr_date_filter').val('');
            });
            $('#date_range_filter_category')
                .data('daterangepicker')
                .setStartDate(moment().startOf('month'));
            $('#date_range_filter_category')
                .data('daterangepicker')
                .setEndDate(moment().endOf('month'));
        }
    

    // leads_category_table
        leads_category_table = $('#leads_category_table').DataTable({
            processing: true,
            serverSide: false,
            ajax: {
                url: "{{action('\Modules\Leads\Http\Controllers\CategoryController@index')}}",
                data: function(d){
                    d.start_date = $('#date_range_filter_category')
                        .data('daterangepicker')
                        .startDate.format('YYYY-MM-DD');
                    d.end_date = $('#date_range_filter_category')
                        .data('daterangepicker')
                        .endDate.format('YYYY-MM-DD');
                    d.user = $('#users_fitler_category').val();
                    d.category = $('#district_fitler_category').val();
                }
            },
            columnDefs:[{
                    "targets": 1,
                    "orderable": false,
                    "searchable": false
                }],
            columns: [
                {data: 'date', name: 'date'},
                {data: 'name', name: 'name'},
                {data: 'user', name: 'user'},
                {data: 'action', name: 'action'}
            ],
            "fnDrawCallback": function (oSettings) {
            }
        });

        $(document).on('click', 'button.leads_category_delete', function(){
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
                            leads_category_table.ajax.reload();
                        },
                    });
                }
            });
        });

        $('#district_fitler_category, #users_fitler_category, #date_range_filter_category').change(function(){
            leads_category_table.ajax.reload();
        })

</script>
@endsection