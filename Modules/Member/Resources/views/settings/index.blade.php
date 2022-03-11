@extends('layouts.app')
@section('title', __('member::lang.settings'))

@section('content')
<!-- Main content -->
<section class="content">
    <div class="row">
        <div class="col-md-12">
            <div class="settlement_tabs">
                <ul class="nav nav-tabs">
                    <li class="@if(empty(session('status.tab'))) active @endif">
                        <a href="#gramaseva_vasama" class="gramaseva_vasama" data-toggle="tab">
                            <strong>@lang('member::lang.gramaseva_vasama')</strong>
                        </a>
                    </li>
                    <li class="@if(session('status.tab') =='balamandalaya') active @endif">
                        <a href="#balamandalaya" class="balamandalaya" data-toggle="tab">
                            <strong>@lang('member::lang.balamandalaya')</strong>
                        </a>
                    </li>
                    <li class="@if(session('status.tab') =='member_group') active @endif">
                        <a href="#member_group" class="member_group" data-toggle="tab">
                            <strong>@lang('member::lang.member_group')</strong>
                        </a>
                    </li>
                    <li class="@if(session('status.tab') =='service_areas') active @endif">
                        <a href="#service_areas" class="service_areas" data-toggle="tab">
                            <strong>@lang('member::lang.service_areas')</strong>
                        </a>
                    </li>

                </ul>
                <div class="tab-content">
                    <div class="tab-pane @if(empty(session('status.tab'))) active @endif" id="gramaseva_vasama">
                        @include('member::settings.gramaseva_vasama.index')
                    </div>
                    <div class="tab-pane @if(session('status.tab') =='balamandalaya') active @endif" id="balamandalaya">
                        @include('member::settings.balamandalaya.index')
                    </div>
                    <div class="tab-pane @if(session('status.tab') =='member_group') active @endif" id="member_group">
                        @include('member::settings.member_group.index')
                    </div>
                    <div class="tab-pane @if(session('status.tab') =='service_areas') active @endif" id="service_areas">
                        @include('member::settings.service_areas.index')
                    </div>


                </div>
            </div>
        </div>
    </div>
    <div class="modal fade gramaseva_vasama_model" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
    </div>
    <div class="modal fade balamandalaya_model" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
    </div>
    <div class="modal fade member_group_model" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
    </div>
    <div class="modal fade service_areas_model" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
    </div>
</section>
<!-- /.content -->

@endsection
@section('javascript')
<script>
    // gramaseva_vasama_table
        gramaseva_vasama_table = $('#gramaseva_vasama_table').DataTable({
            processing: true,
            serverSide: false,
            ajax: "{{action('\Modules\Member\Http\Controllers\GramasevaVasamaController@index')}}",
            columnDefs:[{
                    "targets": 1,
                    "orderable": false,
                    "searchable": false
                }],
            columns: [
                {data: 'date', name: 'date'},
                {data: 'gramaseva_vasama', name: 'gramaseva_vasama'},
                {data: 'action', name: 'action'}
            ],
            "fnDrawCallback": function (oSettings) {
            }
        });

        $(document).on('click', 'button.note_group_delete', function(){
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
                            gramaseva_vasama_table.ajax.reload();
                        },
                    });
                }
            });
        })
    // balamandalaya_table
        balamandalaya_table = $('#balamandalaya_table').DataTable({
            processing: true,
            serverSide: false,
            ajax: "{{action('\Modules\Member\Http\Controllers\BalamandalayaController@index')}}",
            columnDefs:[{
                    "targets": 1,
                    "orderable": false,
                    "searchable": false
                }],
            columns: [
                {data: 'date', name: 'date'},
                {data: 'gramaseva_vasama', name: 'gramaseva_vasama'},
                {data: 'balamandalaya', name: 'balamandalaya'},
                {data: 'action', name: 'action'}
            ],
            "fnDrawCallback": function (oSettings) {
            }
        });

        $(document).on('click', 'button.task_group_delete', function(){
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
                            balamandalaya_table.ajax.reload();
                        },
                    });
                }
            });
        })


    // member_group_table
        member_group_table = $('#member_group_table').DataTable({
            processing: true,
            serverSide: false,
            ajax: "{{action('\Modules\Member\Http\Controllers\MemberGroupController@index')}}",
            columnDefs:[{
                    "targets": 1,
                    "orderable": false,
                    "searchable": false
                }],
            columns: [
                {data: 'date', name: 'date'},
                {data: 'member_group', name: 'member_group'},
                {data: 'action', name: 'action'}
            ],
            "fnDrawCallback": function (oSettings) {
            }
        });

        $(document).on('click', 'button.member_group_delete', function(){
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
                            member_group_table.ajax.reload();
                        },
                    });
                }
            });
        })


    // service_areas_table
        service_areas_table = $('#service_areas_table').DataTable({
            processing: true,
            serverSide: false,
            ajax: "{{action('\Modules\Member\Http\Controllers\ServiceAreasController@index')}}",
            columnDefs:[{
                    "targets": 1,
                    "orderable": false,
                    "searchable": false
                }],
            columns: [
                {data: 'date', name: 'date'},
                {data: 'service_area', name: 'service_area'},
                {data: 'action', name: 'action'}
            ],
            "fnDrawCallback": function (oSettings) {
            }
        });

        $(document).on('click', 'button.service_areas_delete', function(){
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
                            service_areas_table.ajax.reload();
                        },
                    });
                }
            });
        })
</script>
@endsection