@extends('layouts.app')
@section('title', 'Tenant')

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>@lang( 'superadmin::lang.tenant' )
        <small>@lang( 'superadmin::lang.manage_your_tenant' )</small>
    </h1>
    <!-- <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Level</a></li>
        <li class="active">Here</li>
    </ol> -->
</section>

<!-- Main content -->
<section class="content">
    @component('components.widget', ['class' => 'box-primary', 'title' => __( 'superadmin::lang.all_your_tenant' )])
    @can('superadmin::lang.create')
    @slot('tool')
    <div class="box-tools">
        <button type="button" class="btn btn-block btn-primary btn-modal"
            data-href="{{action('\Modules\Superadmin\Http\Controllers\TenantManagementController@create')}}"
            data-container=".tenant_modal">
            <i class="fa fa-plus"></i> @lang( 'messages.add' )</button>
    </div>
    @endslot
    @endcan
    @can('superadmin::lang.view')
    <div class="table-responsive">
        <table class="table table-bordered table-striped" id="tenant_table">
            <thead>
                <tr>
                    <th>@lang( 'superadmin::lang.tenant' )</th>
                    <th>@lang( 'superadmin::lang.created_at' )</th>
                    <th class="notexport">@lang( 'messages.action' )</th>
                </tr>
            </thead>
        </table>
    </div>
    @endcan
    @endcomponent

    <div class="modal fade tenant_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
    </div>

</section>
<!-- /.content -->

@endsection

@section('javascript')
<script>
    $(document).ready(function(){
        $(document).on('click', 'button.delete_tenant_button', function(){
            swal({
                title: LANG.sure,
                icon: "warning",
                buttons: true,
                dangerMode: true,
            }).then((willDelete)=>{
                if(willDelete){
                     var url = $(this).data('href');
                     $.ajax({
                         method: "delete",
                         url: url,
                         dataType: "json",
                         success: function(result){
                             if(result.success == true){
                                toastr.success(result.msg);
                                
                                tenant_table.ajax.reload();
                             }else{
                                toastr.error(result.msg);
                            }

                        }
                    });
                }
            });
        });

          // tenant_table
        tenant_table = $('#tenant_table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{action('\Modules\Superadmin\Http\Controllers\TenantManagementController@index')}}",
                data: function(d){
                  
                }
            },
            columnDefs:[{
                    "targets": 2,
                    "orderable": false,
                    "searchable": false,
                    "width" : "30%",
                }],
            columns: [
                {data: 'id', name: 'id'},
                {data: 'created_at', name: 'created_at'},
                {data: 'action', name: 'action'},
               
            ]
        });
    });


</script>
@endsection