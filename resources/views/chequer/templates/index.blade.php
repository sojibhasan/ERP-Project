@extends('layouts.app')
@section('title', __('cheque.templates'))

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>@lang('cheque.templates')</h1>
</section>

<!-- Main content -->
<section class="content">
    @component('components.widget', ['class' => 'box-primary', 'title' => __( 'cheque.templates_list')])

    @slot('tool')
    <div class="box-tools">
        <a type="button" class="btn btn-block btn-primary"
            href="{{action('Chequer\ChequeTemplateController@create')}}">
            <i class="fa fa-plus"></i> @lang('messages.add')</a>
    </div>
    @endslot
    <div class="table-responsive">
        <table class="table table-bordered table-striped" id="templates_table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>@lang('cheque.template_name')</th>
                    <th>@lang('cheque.template_size')</th>
                    <th>@lang('cheque.last_changed_by')</th>
                    <th>@lang('cheque.last_changed_date')</th>
                    <th>@lang('cheque.action')</th>
                </tr>
            </thead>

        </table>
    </div>

    @endcomponent
</section>
@endsection

@section('javascript')
<script>
    $('#location_id').change(function () {
        templates_table.ajax.reload();
    });
    //employee list
    templates_table = $('#templates_table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '{{action("Chequer\ChequeTemplateController@index")}}',
            data: function (d) {
                d.location_id = $('#location_id').val();
                // d.contact_type = $('#contact_type').val();
            }
        },
        columns: [
            { data: 'id', name: 'id' },
            { data: 'template_name', name: 'template_name' },
            { data: 'template_size', name: 'template_size' },
            { data: 'username', name: 'username' },
            { data: 'created_date', name: 'created_date' },
            { data: 'action', name: 'action' },
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
                            templates_table.ajax.reload();
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