@extends('layouts.app')
@section('title', __('tasksmanagement::lang.tasks'))

@section('content')
<!-- Main content -->
<section class="content">
    <div class="row">
        <div class="col-md-12">
            @component('components.filters', ['title' => __('report.filters')])
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('task_groups', __('tasksmanagement::lang.task_groups') . ':') !!}
                    {!! Form::select('task_groups', $task_groups,
                    null, ['class' => 'form-control select2', 'style' => 'width:100%', 'id' =>
                    'task_groups_filter', 'placeholder' => __('lang_v1.all')]); !!}
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('task_ids', __('tasksmanagement::lang.task_ids') . ':') !!}
                    {!! Form::select('task_ids', $task_ids,
                    null, ['class' => 'form-control select2', 'style' => 'width:100%', 'id' =>
                    'task_ids_filter', 'placeholder' => __('lang_v1.all')]); !!}
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('task_headings', __('tasksmanagement::lang.task_headings') . ':') !!}
                    {!! Form::select('task_headings', $task_headings,
                    null, ['class' => 'form-control select2', 'style' => 'width:100%', 'id' =>
                    'task_headings_filter', 'placeholder' => __('lang_v1.all')]); !!}
                </div>
            </div>

            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('date_range_filter', __('report.date_range') . ':') !!}
                    {!! Form::text('date_range', @format_date('first day of this month') . ' ~ ' . @format_date('last
                    day of this month') , ['placeholder' => __('lang_v1.select_a_date_range'), 'class' =>
                    'form-control daily_report_change', 'id' => 'date_range_filter', 'readonly']); !!}
                </div>
            </div>
            @endcomponent
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            @component('components.widget', ['class' => 'box-primary', 'title' => __(
            'tasksmanagement::lang.all_tasks')])
            @slot('tool')
            <div class="box-tools">
                <button type="button" class="btn btn-primary btn-modal pull-right" id="add_task_task_btn"
                    data-href="{{action('\Modules\TasksManagement\Http\Controllers\TaskController@create')}}"
                    data-container=".task_model">
                    <i class="fa fa-plus"></i> @lang( 'tasksmanagement::lang.add_task' )</button>
            </div>
            @endslot

            <div class="row">
                <div class="col-md-12">
                    <table class="table table-striped table-bordered" id="tasks_table" style="width: 100%;">
                        <thead>
                            <tr>
                                <th>@lang( 'messages.action' )</th>
                                <th>@lang( 'tasksmanagement::lang.date_and_time' )</th>
                                <th>@lang( 'tasksmanagement::lang.task_id' )</th>
                                <th>@lang( 'tasksmanagement::lang.task' )</th>
                                <th>@lang( 'tasksmanagement::lang.user_created' )</th>
                                <th>@lang( 'tasksmanagement::lang.assigned_members' )</th>
                                <th>@lang( 'tasksmanagement::lang.start_date' )</th>
                                <th>@lang( 'tasksmanagement::lang.end_date' )</th>
                                <th>@lang( 'tasksmanagement::lang.estimated_time' )</th>
                                <th>@lang( 'tasksmanagement::lang.priority' )</th>
                                <th>@lang( 'tasksmanagement::lang.task_status' )</th>
                            </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>
                </div>
            </div>
            @endcomponent
        </div>
    </div>
    <div class="modal fade task_model" role="dialog" aria-labelledby="gridSystemModalLabel">
    </div>
</section>
<!-- /.content -->

@endsection
@section('javascript')
<script>
    if ($('#date_range_filter').length == 1) {
        $('#date_range_filter').daterangepicker(dateRangeSettings, function(start, end) {
            $('#date_range_filter').val(
               start.format(moment_date_format) + ' - ' +  end.format(moment_date_format)
            );
        });
        $('#date_range_filter').on('cancel.daterangepicker', function(ev, picker) {
            $('#product_sr_date_filter').val('');
        });
        $('#date_range_filter')
            .data('daterangepicker')
            .setStartDate(moment().startOf('month'));
        $('#date_range_filter')
            .data('daterangepicker')
            .setEndDate(moment().endOf('month'));
    }

    $('#date_range_filter, #task_groups_filter, #task_ids_filter, #task_headings_filter').change(function(){
        tasks_table.ajax.reload();
    })
    // tasks_table
        tasks_table = $('#tasks_table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{action('\Modules\TasksManagement\Http\Controllers\TaskController@index')}}",
                data: function ( d ) {
                    d.start_date = $('#date_range_filter')
                        .data('daterangepicker')
                        .startDate.format('YYYY-MM-DD');
                    d.end_date = $('#date_range_filter')
                        .data('daterangepicker')
                        .endDate.format('YYYY-MM-DD');
                    d.task_group = $('#task_groups_filter').val();
                    d.task_id = $('#task_ids_filter').val();
                    d.task_heading = $('#task_headings_filter').val();
                }
            },
            columnDefs:[{
                    "targets": 1,
                    "orderable": false,
                    "searchable": false
                }],
            columns: [
                {data: 'action', name: 'action'},
                {data: 'date_and_time', name: 'date_and_time'},
                {data: 'task_id', name: 'task_id'},
                {data: 'task_heading', name: 'task_heading'},
                {data: 'user_created', name: 'users.username'},
                {data: 'members', name: 'members'},
                {data: 'start_date', name: 'start_date'},
                {data: 'end_date', name: 'end_date'},
                {data: 'estimated_hours', name: 'estimated_hours'},
                {data: 'priority_name', name: 'priority_name'},
                {data: 'status', name: 'status'}
            ],
            "fnDrawCallback": function (oSettings) {
            }
        });
        $(document).on('click', 'a.delete-task', function(){
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
                            tasks_table.ajax.reload();
                        },
                    });
                }
            });
        });

        $(document).on('click', '#add_task_task_btn', function(){
            // $('.task_model').modal({
            //     backdrop: 'static',
            //     keyboard: false
            // })
        })

        $(".task_model").on('hide.bs.modal', function(){
            tinymce.remove('#task_details');
        });

</script>
@endsection