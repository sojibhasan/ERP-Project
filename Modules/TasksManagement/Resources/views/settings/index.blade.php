@extends('layouts.app')
@section('title', __('tasksmanagement::lang.settings'))

@section('content')
<!-- Main content -->
<section class="content">
    <div class="row">
        <div class="col-md-12">
            <div class="settlement_tabs">
                <ul class="nav nav-tabs">
                    <li class="@if(empty(session('status.tab'))) active @endif">
                        <a href="#notes_settings" class="notes_settings" data-toggle="tab">
                            <i class="fa fa-file-text-o"></i>
                            <strong>@lang('tasksmanagement::lang.notes_settings')</strong>
                        </a>
                    </li>
                    <li class="@if(session('status.tab') =='task') active @endif">
                        <a href="#tasks_settings" class="tasks_settings" data-toggle="tab">
                            <i class="fa fa-file-text-o"></i>
                            <strong>@lang('tasksmanagement::lang.tasks_settings')</strong>
                        </a>
                    </li>
                    <li class="@if(session('status.tab') =='priority') active @endif">
                        <a href="#priorities_settings" class="priorities_settings" data-toggle="tab">
                            <i class="fa fa-file-text-o"></i>
                            <strong>@lang('tasksmanagement::lang.priority')</strong>
                        </a>
                    </li>

                </ul>
                <div class="tab-content">
                    <div class="tab-pane @if(empty(session('status.tab'))) active @endif" id="notes_settings">
                        @include('tasksmanagement::settings.partials.notes_settings')
                    </div>
                    <div class="tab-pane @if(session('status.tab') =='task') active @endif" id="tasks_settings">
                        @include('tasksmanagement::settings.partials.tasks_settings')
                    </div>
                    <div class="tab-pane @if(session('status.tab') =='priority') active @endif" id="priorities_settings">
                        @include('tasksmanagement::settings.partials.priorities_settings')
                    </div>


                </div>
            </div>
        </div>
    </div>
    <div class="modal fade note_group_model" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
    </div>
    <div class="modal fade task_group_model" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
    </div>
    <div class="modal fade priority_model" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
    </div>
</section>
<!-- /.content -->

@endsection
@section('javascript')
<script>
    // note_groups_table
        note_groups_table = $('#note_groups_table').DataTable({
            processing: true,
            serverSide: false,
            ajax: "{{action('\Modules\TasksManagement\Http\Controllers\NoteGroupController@store')}}",
            columnDefs:[{
                    "targets": 1,
                    "orderable": false,
                    "searchable": false
                }],
            columns: [
                {data: 'name', name: 'name'},
                {data: 'color', name: 'color'},
                {data: 'prefix', name: 'prefix'},
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
                            note_groups_table.ajax.reload();
                        },
                    });
                }
            });
        })
    // task_groups_table
        task_groups_table = $('#task_groups_table').DataTable({
            processing: true,
            serverSide: false,
            ajax: "{{action('\Modules\TasksManagement\Http\Controllers\TaskGroupController@store')}}",
            columnDefs:[{
                    "targets": 1,
                    "orderable": false,
                    "searchable": false
                }],
            columns: [
                {data: 'name', name: 'name'},
                {data: 'color', name: 'color'},
                {data: 'prefix', name: 'prefix'},
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
                            task_groups_table.ajax.reload();
                        },
                    });
                }
            });
        })


    // priority_table
        priority_table = $('#priority_table').DataTable({
            processing: true,
            serverSide: false,
            ajax: "{{action('\Modules\TasksManagement\Http\Controllers\PriorityController@store')}}",
            columnDefs:[{
                    "targets": 1,
                    "orderable": false,
                    "searchable": false
                }],
            columns: [
                {data: 'date', name: 'date'},
                {data: 'name', name: 'name'},
                {data: 'added_by', name: 'users.username'},
                {data: 'action', name: 'action'}
            ],
            "fnDrawCallback": function (oSettings) {
            }
        });

        $(document).on('click', 'button.priority_delete', function(){
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
                            priority_table.ajax.reload();
                        },
                    });
                }
            });
        })
</script>
@endsection