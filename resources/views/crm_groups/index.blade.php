@extends('layouts.app')
@section('title', __( 'lang_v1.crm_groups' ))

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>@lang( 'lang_v1.crm_groups' )</h1>
    <!-- <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Level</a></li>
        <li class="active">Here</li>
    </ol> -->
</section>

<!-- Main content -->
<section class="content">
    @component('components.widget', ['class' => 'box-primary', 'title' => __( 'lang_v1.all_your_crm_groups' )])
        @can('customer.create')
            @slot('tool')
                <div class="box-tools">
                    <button type="button" class="btn btn-block btn-primary btn-modal" 
                        data-href="{{action('CrmGroupController@create')}}" 
                        data-container=".crm_groups_modal">
                        <i class="fa fa-plus"></i> @lang( 'messages.add' )</button>
                </div>
            @endslot
        @endcan
        @can('customer.view')
            <div class="table-responsive">
                <table class="table table-bordered table-striped" id="crm_groups_table">
                    <thead>
                        <tr>
                            <th>@lang( 'lang_v1.crm_group_name' )</th>
                            <th>@lang( 'messages.action' )</th>
                        </tr>
                    </thead>
                </table>
                </div>
        @endcan
    @endcomponent

    <div class="modal fade crm_groups_modal" tabindex="-1" role="dialog" 
    	aria-labelledby="gridSystemModalLabel">
    </div>
    <div class="modal fade edit_crm_groups_modal" tabindex="-1" role="dialog" 
    	aria-labelledby="gridSystemModalLabel">
    </div>

</section>
<!-- /.content -->

@endsection


@section('javascript')
    <script>
    
    //CRM Group table
  
    crm_groups_table = $('#crm_groups_table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '{{action("CrmGroupController@index")}}',
            data: function (d) {
              
            }
        },
        columns: [
            { data: 'name', name: 'name' },
            { data: 'action', name: 'action' },
        ],
        fnDrawCallback: function (oSettings) {
          
        },
    });

    $(document).on('submit', 'form#crm_group_add_form', function(e) {
        e.preventDefault();
        var data = $(this).serialize();

        $.ajax({
            method: 'POST',
            url: '{{action("CrmGroupController@store")}}',
            dataType: 'json',
            data: data,
            success: function(result) {
                if (result.success == true) {
                    $('div.crm_groups_modal').modal('hide');
                    toastr.success(result.msg);
                    crm_groups_table.ajax.reload();
                } else {
                    toastr.error(result.msg);
                }
            },
        });
    });

    $(document).on('click', 'button.crm_group_edit_button', function() {
        $('div.edit_crm_groups_modal').load($(this).data('href'), function() {
            $(this).modal('show');

            $('form#customer_group_edit_form').submit(function(e) {
                e.preventDefault();
                var data = $(this).serialize();

                $.ajax({
                    method: 'POST',
                    url: $(this).attr('action'),
                    dataType: 'json',
                    data: data,
                    success: function(result) {
                        if (result.success == true) {
                            $('div.edit_crm_groups_modal').modal('hide');
                            toastr.success(result.msg);
                            crm_groups_table.ajax.reload();
                        } else {
                            toastr.error(result.msg);
                        }
                    },
                });
            });
        });
    });


    $(document).on('click', 'button.delete_crm_group_button', function() {
        swal({
            title: LANG.sure,
            text: LANG.confirm_delete_customer_group,
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
                        if (result.success == true) {
                            toastr.success(result.msg);
                            crm_groups_table.ajax.reload();
                        } else {
                            toastr.error(result.msg);
                        }
                    },
                });
            }
        });
    });


    </script>
@endsection
