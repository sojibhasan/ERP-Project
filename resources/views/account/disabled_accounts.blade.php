@extends('layouts.app')
@section('title', __('lang_v1.payment_accounts'))

@section('content')
<link rel="stylesheet"
    href="{{ asset('plugins/bootstrap-datetimepicker/bootstrap-datetimepicker.min.css?v='.$asset_v) }}">

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>@lang('account.disabled_accounts')
        <small>@lang('account.manage_your_disabled_account')</small>
    </h1>
</section>

<!-- Main content -->
<section class="content">
    @component('components.widget', ['class' => 'box-primary', 'title' => __( 'account.all_your_disabled_accounts' )])
    @can('account.access')
    <div class="row">
        <div class="col-sm-12">
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('location_id', __('purchase.business_location') . ':') !!}
                    {!! Form::select('location_id', $business_locations, null, ['class' => 'form-control select2',
                    'placeholder' => __('petro::lang.all'), 'style' => 'width:100%']); !!}
                </div>
            </div>
        </div>
        <div class="col-sm-12">
            <div class="row">
                <div class="col-sm-12">
                    <br>
                    <table class="table table-bordered table-striped" id="other_account_table">
                        <thead>
                            <tr>
                                <th>@lang( 'lang_v1.name' )</th>
                                <th>@lang( 'lang_v1.account_type' )</th>
                                <th>@lang( 'lang_v1.account_sub_type' )</th>
                                <th>@lang( 'account.account_group' )</th>
                                <th>@lang('account.account_number')</th>
                                <th>@lang('lang_v1.balance')</th>
                                <th>@lang('lang_v1.added_by')</th>
                                <th>@lang( 'messages.action' )</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>

        </div>
    </div>
    @endcan
    @endcomponent

    <div class="modal fade account_model" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
    </div>

    <div class="modal fade" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel" id="account_type_modal">
    </div>
    <div class="modal fade" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel"
        id="account_groups_modal">
    </div>
</section>
<!-- /.content -->

@endsection
@if(!$account_access)
<style>
  .dataTables_empty{
        color: {{App\System::getProperty('not_enalbed_module_user_color')}};
        font-size: {{App\System::getProperty('not_enalbed_module_user_font_size')}}px;
    }
</style>
@endif
@section('javascript')
<script src="{{ asset('plugins/bootstrap-datetimepicker/bootstrap-datetimepicker.min.js?v=' . $asset_v) }}"></script>
<script>
    $(document).ready(function(){

        $(document).on('click', 'button.close_account', function(){
            swal({
                title: LANG.sure,
                icon: "warning",
                buttons: true,
                dangerMode: true,
            }).then((willDelete)=>{
                if(willDelete){
                     var url = $(this).data('url');

                     $.ajax({
                         method: "get",
                         url: url,
                         dataType: "json",
                         success: function(result){
                             if(result.success == true){
                                toastr.success(result.msg);
                               
                                other_account_table.ajax.reload();
                             }else{
                                toastr.error(result.msg);
                            }

                        }
                    });
                }
            });
        });

        
        $(document).on('click', 'button.disable_status_account', function(){
            swal({
                title: LANG.sure,
                icon: "warning",
                buttons: true,
                dangerMode: true,
            }).then((willDelete)=>{
                if(willDelete){
                     var url = $(this).data('url');

                     $.ajax({
                         method: "get",
                         url: url,
                         dataType: "json",
                         success: function(result){
                             if(result.success == true){
                                toastr.success(result.msg);
                                
                                other_account_table.ajax.reload();
                             }else{
                                toastr.error(result.msg);
                            }

                        }
                    });
                }
            });
        });

        $(document).on('submit', 'form#edit_payment_account_form', function(e){
            e.preventDefault();
            var data = $(this).serialize();
            $.ajax({
                method: "POST",
                url: $(this).attr("action"),
                dataType: "json",
                data: data,
                success:function(result){
                    if(result.success == true){
                        $('div.account_model').modal('hide');
                        toastr.success(result.msg);
                        other_account_table.ajax.reload();
                    }else{
                        toastr.error(result.msg);
                    }
                }
            });
        });

        $(document).on('submit', 'form#payment_account_form', function(e){
            e.preventDefault();
            var data = $(this).serialize();
            $.ajax({
                method: "post",
                url: $(this).attr("action"),
                dataType: "json",
                data: data,
                success:function(result){
                    if(result.success == true){
                        $('div.account_model').modal('hide');
                        toastr.success(result.msg);
                        
                        other_account_table.ajax.reload();
                    }else{
                        toastr.error(result.msg);
                    }
                }
            });
        });

        // other_account_table
        other_account_table = $('#other_account_table').DataTable({
            language: {
                "emptyTable": "@if(!$account_access) {{App\System::getProperty('not_enalbed_module_user_message')}} @else @lang('account.no_data_available_in_table') @endif"
            },
            processing: true,
            serverSide: false,
            ajax: {
                url: '/accounting-module/disabled-account?account_type=other',
                data: function(d){
                    d.location_id = $('#location_id').val();
                }
            },
            columnDefs:[{
                    "targets": 7,
                    "orderable": false,
                    "searchable": false,
                    "width" : "30%",
                }],
            columns: [
                {data: 'name', name: 'accounts.name'},
                {data: 'parent_account_type_name', name: 'pat.name'},
                {data: 'account_type_name', name: 'ats.name'},
                {data: 'account_group', name: 'account_group'},
                {data: 'account_number', name: 'accounts.account_number'},
                {data: 'balance', name: 'balance', searchable: false},
                {data: 'added_by', name: 'u.first_name'},
                {data: 'action', name: 'action'},
               
            ],
            "fnDrawCallback": function (oSettings) {
                __currency_convert_recursively($('#other_account_table'));
            },
            "rowCallback": function( row, data, index ) {  
                if (parseInt($(row).data('visible')) === 0) {
                    $(row).addClass('hide');
                }
            }
        });

        $('#location_id').change(function(){
            other_account_table.ajax.reload();
        })

        // account_groups_table
        account_groups_table = $('#account_groups_table').DataTable({
            processing: true,
            serverSide: false,
            ajax: '/account-groups',
            columnDefs:[{
                    "targets": 3,
                    "orderable": false,
                    "searchable": false,
                    "width" : "30%",
                }],
            columns: [
                {data: 'name', name: 'account_groups.name'},
                {data: 'account_type_name', name: 'ats.name'},
                {data: 'note', name: 'note'},
                {data: 'action', name: 'action'}
            ],
            "fnDrawCallback": function (oSettings) {
            }
        });


        $(document).on('click', '#save_account_group_btn', function(e){
            e.preventDefault();
            let name = $('#account_group_name_group').val();
            let account_type_id = $('#account_type_id_group').val();
            let note = $('#note_group').val();

            $.ajax({
                method: 'post',
                url: '/account-groups',
                data: { 
                    name,
                    account_type_id,
                    note,
                },
                success: function(result) {
                    if(result.success == 1){
                        toastr.success(result.msg);
                    }else{
                        toastr.error(result.msg);
                    }
                    $('#account_groups_modal').modal('hide');
                    account_groups_table.ajax.reload();
                },
            });

        });
        $(document).on('click', '#update_account_group_btn', function(e){
            e.preventDefault();
            let name = $('#account_group_name_group').val();
            let account_type_id = $('#account_type_id_group').val();
            let note = $('#note_group').val();
            let url = $('#account_group_form').attr('action');
            $.ajax({
                method: 'put',
                url: url,
                data: { 
                    name,
                    account_type_id,
                    note,
                },
                success: function(result) {
                    if(result.success == 1){
                        toastr.success(result.msg);
                    }else{
                        toastr.error(result.msg);
                    }
                    $('.account_model').modal('hide');
                    account_groups_table.ajax.reload();
                },
            });

        });

        $(document).on('click', 'button.account_group_delete', function(){
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
                            account_groups_table.ajax.reload();
                        },
                    });
                }
            });
        })

    });
    $('.account_model').on('shown.bs.modal', function(e) {
        $('.account_model .select2').select2({ dropdownParent: $(this) });
    });
   
    $('#add_button').click(function(){
        $('.account_model').modal({
            backdrop: 'static',
            keyboard: false
        })
    });
    $('#add_acount_group_btn').click(function(){
        $('#account_groups_modal').modal({
            backdrop: 'static',
            keyboard: false
        })
    });
    $(document).on('click', '.edit_btn, .deposit_btn, .transfer_btn', function(){
        $('.account_model').modal({
            backdrop: 'static',
            keyboard: false
        })
    });
    $(document).on('click', 'button.delete_account_type', function(){
        swal({
            title: LANG.sure,
            icon: "warning",
            buttons: true,
            dangerMode: true,
        }).then((willDelete)=>{
            if(willDelete){
                $(this).closest('form').submit();
            }
        });
    })

    $(document).on('change', '#account_number', function(){
        $.ajax({
            method: 'get',
            url: '/accounting-module/check_account_number',
            data: { account_number: $(this).val() },
            success: function(result) {
                if(!result.success){
                    $(this).val('');
                    toastr.error(result.msg);
                }
            },
        });
    })

</script>
@endsection