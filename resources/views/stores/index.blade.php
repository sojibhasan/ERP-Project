@extends('layouts.app')
@section('title', __('store.store_list'))

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>@lang('store.store_list')</h1>
    <div class="row">
        <div class="col-md-12">
            @component('components.filters', ['title' => __('report.filters')])
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('location_id', __('purchase.business_location') . ':') !!}
                    {!! Form::select('location_id', $business_locations, null, ['id' => 'location_id', 'class' =>
                    'form-control select2', 'style' => 'width:100%']); !!}
                </div>
            </div>
            @endcomponent
        </div>
    </div>
</section>

<!-- Main content -->
<section class="content">
    @component('components.widget', ['class' => 'box-primary', 'title' => __( 'store.all_Store' )])
        @can('store.create')
            @slot('tool')
                <div class="box-tools">
                    <button type="button" class="btn btn-block btn-primary btn-modal"
                    data-href="{{action('StoreController@create')}}" data-container=".store_modal">
                    <i class="fa fa-plus"></i> @lang( 'messages.add' )</button>
                </div>
            @endslot
        @endcan
        @can('store.view')
            <div class="table-responsive">
                <table class="table table-bordered table-striped" id="store_table">
                    <thead>
                        <tr>
                            <th>@lang('store.location_id')</th>
                            <th>@lang('store.location_name')</th>
                            <th>@lang('store.name')</th>
                            <th>@lang('store.address')</th>
                            <th>@lang('store.contact')</th>
                            <th>@lang('store.stock')</th>
                            <th>@lang('store.status')</th>
                            <th>@lang('store.action')</th>
                        </tr>
                    </thead>
                </table>
            </div>
        @endcan
    @endcomponent

    <div class="modal fade store_modal" tabindex="-1" role="dialog" 
    	aria-labelledby="gridSystemModalLabel">
    </div>

    <div class="modal fade edit_modal" tabindex="-1" role="dialog" 
    	aria-labelledby="gridSystemModalLabel">
    </div>

</section>
<!-- /.content -->

@endsection

@section('javascript')
<script>
    $('#location_id').change(function () {
        store_table.ajax.reload();
    });
    //employee list
    store_table = $('#store_table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '{{action("StoreController@index")}}',
            data: function (d) {
                d.location_id = $('#location_id').val();
            }
        },
        columns: [
            { data: 'location_id', name: 'business_locations.location_id' },
            { data: 'location_name', name: 'business_locations.name' },
            { data: 'name', name: 'name' },
            { data: 'address', name: 'address' },
            { data: 'contact_number', name: 'contact_number' },
            { data: 'stock', name: 'stock' },
            { data: 'status', name: 'status' },
            { data: 'action', name: 'action' },
        ],
        fnDrawCallback: function (oSettings) {
          
        },
    });

    $(document).on('click', 'a.delete_store', function(e) {
        e.preventDefault();
        swal({
            title: LANG.sure,
            text: 'This store will be deleted.',
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
                            store_table.ajax.reload();
                        } else {
                            toastr.error(result.msg);
                        }
                    },
                });
            }
        });
    });
    $('#filter_business').select2();


    $(document).on('click', 'button.edit_store_button', function() {
        $('div.edit_modal').load($(this).data('href'), function() {
            $(this).modal('show');

            $('form#unit_edit_form').submit(function(e) {
                e.preventDefault();
                $(this)
                    .find('button[type="submit"]')
                    .attr('disabled', true);
                var data = $(this).serialize();

                $.ajax({
                    method: 'POST',
                    url: $(this).attr('action'),
                    dataType: 'json',
                    data: data,
                    success: function(result) {
                        if (result.success == true) {
                            $('div.edit_modal').modal('hide');
                            toastr.success(result.msg);
                            store_table.ajax.reload();
                        } else {
                            toastr.error(result.msg);
                        }
                    },
                });
            });
        });
    });

    
</script>
@endsection