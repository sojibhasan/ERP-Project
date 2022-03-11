@extends('layouts.app')
@section('title', __('tasksmanagement::lang.notes'))

@section('content')
<!-- Main content -->
<section class="content">
    <div class="row">
        <div class="col-md-12">
            @component('components.filters', ['title' => __('report.filters')])
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('note_groups', __('tasksmanagement::lang.note_groups') . ':') !!}
                    {!! Form::select('note_groups', $note_groups,
                    null, ['class' => 'form-control select2', 'style' => 'width:100%', 'id' =>
                    'note_groups_filter', 'placeholder' => __('lang_v1.all')]); !!}
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('note_ids', __('tasksmanagement::lang.note_ids') . ':') !!}
                    {!! Form::select('note_ids', $note_ids,
                    null, ['class' => 'form-control select2', 'style' => 'width:100%', 'id' =>
                    'note_ids_filter', 'placeholder' => __('lang_v1.all')]); !!}
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('note_headings', __('tasksmanagement::lang.note_headings') . ':') !!}
                    {!! Form::select('note_headings', $note_headings,
                    null, ['class' => 'form-control select2', 'style' => 'width:100%', 'id' =>
                    'note_headings_filter', 'placeholder' => __('lang_v1.all')]); !!}
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
            'tasksmanagement::lang.all_notes')])
            @slot('tool')
            <div class="box-tools">
                <button type="button" class="btn btn-primary btn-modal pull-right" id="add_note_note_btn"
                    data-href="{{action('\Modules\TasksManagement\Http\Controllers\NoteController@create')}}"
                    data-container=".note_model">
                    <i class="fa fa-plus"></i> @lang( 'tasksmanagement::lang.add_note' )</button>
            </div>
            @endslot

            <div class="row">
                <div class="col-md-12">
                    @foreach ($notes as $note)
                    <a href="#" style="color: black; text-decoration: none;"
                        data-href="{{action('\Modules\TasksManagement\Http\Controllers\NoteController@show', [$note->id])}}"
                        class="btn-modal" data-container=".note_model">
                        <div class="col-md-2 text-center">
                            <div style="width: 200px; height: auto; background: {{$note->color}};">
                                <h2 style="padding: 30px 15px;">{{str_limit($note->note_heading, 10)}}</h2>
                            </div>
                        </div>
                    </a>
                    @endforeach
                </div>
            </div>
            <br>
            <br>
            <br>
            <div class="clearfix"></div>
            <div class="row">
                <div class="col-md-12">
                    <table class="table table-striped table-bordered" id="notes_table" style="width: 100%;">
                        <thead>
                            <tr>
                                <th>@lang( 'messages.action' )</th>
                                <th>@lang( 'tasksmanagement::lang.color' )</th>
                                <th>@lang( 'tasksmanagement::lang.date_and_time' )</th>
                                <th>@lang( 'tasksmanagement::lang.note_group' )</th>
                                <th>@lang( 'tasksmanagement::lang.note_id' )</th>
                                <th>@lang( 'tasksmanagement::lang.note_heading' )</th>
                                <th>@lang( 'tasksmanagement::lang.shared_with' )</th>
                                <th>@lang( 'tasksmanagement::lang.show_on_the_top' )</th>
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
    <div class="modal fade note_model" role="dialog" aria-labelledby="gridSystemModalLabel">
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

    $('#date_range_filter, #note_groups_filter, #note_ids_filter, #note_headings_filter').change(function(){
        notes_table.ajax.reload();
    })
    // notes_table
        notes_table = $('#notes_table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{action('\Modules\TasksManagement\Http\Controllers\NoteController@index')}}",
                data: function ( d ) {
                    d.start_date = $('#date_range_filter')
                        .data('daterangepicker')
                        .startDate.format('YYYY-MM-DD');
                    d.end_date = $('#date_range_filter')
                        .data('daterangepicker')
                        .endDate.format('YYYY-MM-DD');
                    d.note_group = $('#note_groups_filter').val();
                    d.note_id = $('#note_ids_filter').val();
                    d.note_heading = $('#note_headings_filter').val();
                }
            },
            columnDefs:[{
                    "targets": 1,
                    "orderable": false,
                    "searchable": false
                }],
            columns: [
                {data: 'action', name: 'action'},
                {data: 'color', name: 'color'},
                {data: 'date_and_time', name: 'date_and_time'},
                {data: 'note_group', name: 'note_groups.name'},
                {data: 'note_id', name: 'note_id'},
                {data: 'note_heading', name: 'note_heading'},
                {data: 'shared_with_users', name: 'shared_with_users'},
                {data: 'show_on_top_section', name: 'show_on_top_section'}
            ],
            "fnDrawCallback": function (oSettings) {
            }
        });
        $(document).on('click', 'a.delete-note', function(){
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
                            notes_table.ajax.reload();
                        },
                    });
                }
            });
        });

        $(document).on('click', '#add_note_note_btn', function(){
            $('.note_model').modal({
                backdrop: 'static',
                keyboard: false
            })
        })

        $(".note_model").on('hide.bs.modal', function(){
            tinymce.remove('#note_details');
        });

</script>
@endsection