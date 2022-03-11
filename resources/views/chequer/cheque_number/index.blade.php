@extends('layouts.app')
@section('title', __('cheque.cheque_number'))

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>@lang('cheque.cheque_number')</h1>
</section>

<!-- Main content -->
<section class="content">
    @component('components.widget', ['class' => 'box-primary', 'title' => __( 'cheque.cheque_number_list')])

    @slot('tool')
    <div class="box-tools">
        <div class="box-tools">
            <button type="button" class="btn btn-block btn-primary" data-toggle="modal" data-target="#stamp_add_modal">
                <i class="fa fa-plus"></i> @lang('messages.add')
            </button>
        </div>
    </div>
    @endslot
    <div class="table-responsive">
        <table class="table table-bordered table-striped" id="cheque_number_table">
            <thead>
                <tr>
                    <th>@lang('cheque.data_tiime')</th>
                    <th>@lang('cheque.account_number')</th>
                    <th>@lang('cheque.bank')</th>
                    <th>@lang('cheque.first_cheque_number')</th>
                    <th>@lang('cheque.last_cheque_number')</th>
                    <th>@lang('cheque.no_of_cheque_leaves')</th>
                    <th>@lang('cheque.user')</th>
                </tr>
            </thead>

        </table>
    </div>

    @endcomponent

    <!-- Modal -->
    <div class="modal fade" id="stamp_add_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h3 class="modal-title" id="exampleModalLabel">@lang('cheque.add_cheque_number')</h3>
                </div>
                <div class="modal-body">
                    {!! Form::open(['url' => action('Chequer\ChequeNumberController@store'), 'method' => 'post']) !!}
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                {!! Form::label('date_time', __('cheque.date') . ':') !!}
                                <div class="input-group">
                                    {!! Form::text('date_time', date('Y/m/d'), ['class' => 'form-control', 'placeholder' =>
                                    __('cheque.date'), 'required']); !!}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                {!! Form::label('ref_no', __('cheque.ref_no') . ':') !!}
                                <div class="input-group">
                                    {!! Form::text('reference_no', null, ['class' => 'form-control', 'placeholder' =>
                                    __('cheque.ref_no'), 'required']); !!}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                {!! Form::label('account_number', __('cheque.account_number') . ':') !!}
                                <div class="input-group">
                                    {!! Form::select('account_number', $accounts, null, ['class' => 'form-control',
                                    'placeholder' =>
                                    __('cheque.account_number'), 'required', 'id' => 'account_number', 'style' =>
                                    'width: 100%']); !!}

                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                {!! Form::label('first_cheque_no', __('cheque.first_cheque_number') . ':') !!}
                                <div class="input-group">
                                    {!! Form::number('first_cheque_no', null, ['class' => 'form-control', 'placeholder'
                                    =>
                                    __('cheque.first_cheque_number'), 'required']); !!}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                {!! Form::label('last_cheque_no', __('cheque.last_cheque_number') . ':') !!}
                                <div class="input-group">
                                    {!! Form::number('last_cheque_no', null, ['class' => 'form-control', 'placeholder'
                                    =>
                                    __('cheque.last_cheque_number'), 'required']); !!}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                {!! Form::label('no_of_cheque_leaves', __('cheque.no_of_cheque_leaves') . ':') !!}
                                <div class="input-group">
                                    {!! Form::number('no_of_cheque_leaves', null, ['class' => 'form-control',
                                    'placeholder' =>
                                    __('cheque.no_of_cheque_leaves'), 'required']); !!}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <button type="button" style="margin-right: 5px;" class="pull-right btn btn-secondary"
                            data-dismiss="modal">Close</button>
                        <button type="submit" style="margin-right: 5px;"
                            class="pull-right btn btn-primary">@lang('cheque.save')</button>
                    </div>
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade edit_modal" role="dialog" aria-labelledby="gridSystemModalLabel">
    </div>
</section>
@endsection

@section('javascript')
<script>
    $('#location_id').change(function () {
        cheque_number_table.ajax.reload();
    });
    //employee list
    cheque_number_table = $('#cheque_number_table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '{{action("Chequer\ChequeNumberController@index")}}',
            data: function (d) {
                d.location_id = $('#location_id').val();
                // d.contact_type = $('#contact_type').val();
            }
        },
        columns: [
            { data: 'date_time', name: 'date_time' },
            { data: 'account_number', name: 'account_number' },
            { data: 'bank', name: 'bank' },
            { data: 'first_cheque_no', name: 'first_cheque_no' },
            { data: 'last_cheque_no', name: 'last_cheque_no' },
            { data: 'no_of_cheque_leaves', name: 'no_of_cheque_leaves' },
            { data: 'username', name: 'username' },
        ],
        fnDrawCallback: function (oSettings) {
          
        },
    });

    $(document).on('click', 'a.delete_employee', function(e) {
        e.preventDefault();
        swal({
            title: LANG.sure,
            text: 'This template will be deleted.',
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
                            cheque_number_table.ajax.reload();
                        } else {
                            toastr.error(result.msg);
                        }
                    },
                });
            }
        });
    });
    // $('#account_number').select2();
</script>
@endsection