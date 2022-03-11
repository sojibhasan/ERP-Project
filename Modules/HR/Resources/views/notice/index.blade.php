@extends('layouts.app')
@section('title', __('hr::lang.notice'))

@section('content')
<!-- Main content -->
<section class="content">

    <div class="row">
        <div class="col-md-12">
            @component('components.widget', ['class' => 'box-primary', 'title' => __(
            'hr::lang.all_notice')])
            @slot('tool')
            <div class="box-tools">
                <button type="button" class="btn btn-primary btn-modal pull-right" id="add_notice_btn"
                    data-href="{{action('\Modules\HR\Http\Controllers\NoticeBoardController@create')}}"
                    data-container=".notice_model">
                    <i class="fa fa-plus"></i> @lang( 'hr::lang.add_notice' )</button>
            </div>
            @endslot
            <div class="row">
                <div class="col-md-12">
                    <table class="table table-striped table-bordered" id="notice_table" style="width: 100%;">
                        <thead>
                            <tr>
                                <th>@lang( 'hr::lang.date' )</th>
                                <th>@lang( 'hr::lang.title' )</th>
                                <th>@lang( 'hr::lang.short_description' )</th>
                                <th>@lang( 'hr::lang.notice_details' )</th>
                                <th>@lang( 'hr::lang.status' )</th>
                                <th>@lang( 'messages.action' )</th>
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
    <div class="modal fade notice_model" role="dialog" aria-labelledby="gridSystemModalLabel">
    </div>
</section>
<!-- /.content -->

@endsection
@section('javascript')
<script>
    // notice_table
        notice_table = $('#notice_table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{action('\Modules\HR\Http\Controllers\NoticeBoardController@index')}}",
            },
            columnDefs:[{
                    "targets": 5,
                    "orderable": false,
                    "searchable": false
                }],
            columns: [
                {data: 'date', name: 'date'},
                {data: 'title', name: 'titles.name'},
                {data: 'short_description', name: 'short_description'},
                {data: 'notice_details', name: 'notice_details'},
                {data: 'status', name: 'status'},
                {data: 'action', name: 'action'}
            ],
            "fnDrawCallback": function (oSettings) {
            }
        });
        $(document).on('click', 'a.delete-notice', function(){
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
                            notice_table.ajax.reload();
                        },
                    });
                }
            });
        });

        $(document).on('click', '#add_notice_btn', function(){
            $('.notice_model').modal({
                backdrop: 'static',
                keyboard: false
            })
        })

        $(".notice_model").on('hide.bs.modal', function(){
            tinymce.remove('#notice_details');
        });

</script>
@endsection