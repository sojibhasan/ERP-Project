@extends('layouts.app')
@section('title', __('cheque.stamps'))

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>@lang('cheque.stamps')</h1>
</section>

<!-- Main content -->
<section class="content">
    @component('components.widget', ['class' => 'box-primary', 'title' => __( 'cheque.stamps_list')])

    @slot('tool')
    <div class="box-tools">
        <button type="button" class="btn btn-block btn-primary" data-toggle="modal" data-target="#stamp_add_modal">
            <i class="fa fa-plus"></i> @lang('messages.add')
        </button>
    </div>
    @endslot
    <div class="table-responsive">
        <table class="table table-bordered table-striped" id="stamps_table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>@lang('cheque.stamp_image')</th>
                    <th>@lang('cheque.stamp_name')</th>
                    <th>@lang('cheque.active')</th>
                    <th>@lang('cheque.last_changed_date')</th>
                    <th>@lang('cheque.action')</th>
                </tr>
            </thead>

        </table>
    </div>

    @endcomponent
</section>

<!-- Modal -->
<div class="modal fade" id="stamp_add_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title" id="exampleModalLabel">@lang('cheque.add_stamp')</h3>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                {!! Form::open(['url' => action('Chequer\ChequerStampController@store'), 'method' => 'post', 'enctype'
                => 'multipart/form-data']) !!}
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Form::label('stamp_name', __('cheque.stamp_name') . ':') !!}
                            <div class="input-group">
                                {!! Form::text('stamp_name', null, ['class' => 'form-control', 'placeholder' =>
                                __('cheque.stamp_name'), 'required']); !!}
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Form::label('upload_stamp', __('cheque.upload_stamp') . ':') !!}
                            <div class="input-group">
                                {!! Form::file('upload_stamp', null, ['class' => 'form-control', 'placeholder' =>
                                __('cheque.upload_stamp'), 'required']); !!}
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('active', 1, false, ['class' => 'input-icheck', 'id' => 'active']);
                                !!}
                                @lang('cheque.active')
                            </label>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <button type="button" style="margin-right: 5px;" class="pull-right btn btn-secondary"
                        data-dismiss="modal">Close</button>
                    <button type="submit" style="margin-right: 5px;"
                        class="pull-right btn btn-primary">@lang('cheque.add_stamp')</button>
                </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>
</div>

<div class="modal fade edit_modal" tabindex="-1" role="dialog" 
        aria-labelledby="gridSystemModalLabel">
    </div>
@endsection

@section('javascript')
<script>
    //employee list
    stamps_table = $('#stamps_table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '{{action("Chequer\ChequerStampController@index")}}',
            data: {}
        },
        columns: [
            { data: 'id', name: 'id' },
            { data: 'stamp_image', name: 'stamp_image' },
            { data: 'stamp_name', name: 'stamp_name' },
            { data: 'active', name: 'active' },
            { data: 'updated_at', name: 'updated_at' },
            { data: 'action', name: 'action' },
        ],
        fnDrawCallback: function (oSettings) {
          
        },
    });

    $(document).on('click', 'a.delete_stamps', function(e) {
        e.preventDefault();
        swal({
            title: LANG.sure,
            text: 'This stamp will be deleted.',
            icon: 'warning',
            buttons: true,
            dangerMode: true,
        }).then(willDelete => {
            if (willDelete) {
                var href = $(this).data('href');
                var data = $(this).serialize();

                $.ajax({
                    method: 'DELETE',
                    url: href,
                    dataType: 'json',
                    data: data,
                    success: function(result) {
                        if (result.success === true) {
                            toastr.success(result.msg);
                            stamps_table.ajax.reload();
                        } else {
                            toastr.error(result.msg);
                        }
                    },
                });
            }
        });
    });
    $('#filter_business').select2();
</script>
@endsection