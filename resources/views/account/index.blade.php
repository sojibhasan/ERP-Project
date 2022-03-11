@extends('layouts.app')
@section('title', __('lang_v1.payment_accounts'))

@section('content')
<link rel="stylesheet"
    href="{{ asset('plugins/bootstrap-datetimepicker/bootstrap-datetimepicker.min.css?v='.$asset_v) }}">

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>@lang('lang_v1.payment_accounts')
        <small>@lang('account.manage_your_account')</small>
    </h1>
</section>

<!-- Main content -->
<section class="content">
    @can('account.access')
    <div class="row">
        <div class="col-sm-12">
            <div class="nav-tabs-custom">
                <ul class="nav nav-tabs">
                    <li class="@if(empty(session('status.tab'))) active @endif">
                        <a href="#other_accounts" data-toggle="tab">
                            <i class="fa fa-book"></i> <strong>@lang('account.accounts')</strong>
                        </a>
                    </li>

                    <li>
                        <a href="#account_types" data-toggle="tab">
                            <i class="fa fa-list"></i> <strong>
                                @lang('lang_v1.account_types') </strong>
                        </a>
                    </li>

                    <li>
                        <a href="#account_groups" data-toggle="tab">
                            <i class="fa fa-object-group"></i> <strong>
                                @lang('lang_v1.account_groups') </strong>
                        </a>
                    </li>
                    @can('account.settings')
                    <li>
                        <a href="#account_settings" data-toggle="tab">
                            <i class="fa fa-cogs"></i> <strong>
                                @lang('lang_v1.account_settings') </strong>
                        </a>
                    </li>
                    @endcan
                    <li class="@if(session('status.tab') == 'list_deposit_transfer') active @endif">
                        <a href="#list_deposit_transfer" data-toggle="tab">
                            <i class="fa fa-list"></i> <strong>
                                @lang('lang_v1.list_deposit_transfer') </strong>
                        </a>
                    </li>
                </ul>
                <div class="tab-content">
                    <div class="tab-pane @if(empty(session('status.tab'))) active @endif" id="other_accounts">
                        <div class="row">
                          
                            <div class="col-md-12">
                                <div class="row">
                                    <button style="margin-right: 25px;" type="button" id="add_button"
                                        class="btn btn-sm btn-primary btn-modal pull-right"
                                        data-container=".account_model"
                                        data-href="{{action('AccountController@create')}}">
                                        <i class="fa fa-plus"></i> @lang( 'messages.add' )</button>
                                    <button style="margin-right: 25px;"
                                        data-href="{{action('AccountController@getDeposit', ['card'])}}"
                                        class="btn btn-sm btn-warning btn-modal  pull-right deposit_btn"
                                        data-container=".account_model"><i class="fa fa-money"></i>
                                        @lang("account.card_deposit")</button>

                                    <button style="margin-right: 25px;"
                                        data-href="{{action('AccountController@getChequeDeposit')}}"
                                        class="btn btn-sm btn-info btn-modal  pull-right deposit_btn"
                                        data-container=".account_model"><i class="fa fa-address-card-o"></i>
                                        @lang("account.cheque_deposit")</button>
                                    <button style="margin-right: 25px;"
                                        data-href="{{action('AccountController@getDeposit', ['cash'])}}"
                                        class="btn btn-sm btn-success btn-modal  pull-right deposit_btn"
                                        data-container=".account_model"><i class="fa fa-money"></i>
                                        @lang("account.cash_deposit")</button>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="row">
                            @component('components.filters', ['title' => __('report.filters')])
                                <div class="col-md-3">
                                    <div class="form-group">
                                        {!! Form::label('account_type',  __('Account Type') . ':') !!}
                                        {!! Form::select('account_type', $account_types_opts, null, ['id'=>'account_type','class' => 'form-control select2', 'style' => 'width:100%', 'placeholder' => __('lang_v1.all')]); !!}
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        {!! Form::label('account_sub_type',  __('Account Sub Type') . ':') !!}
                                        {!! Form::select('account_sub_type', $sub_acn_arr, null, ['id'=>'account_sub_type','class' => 'form-control select2', 'style' => 'width:100%', 'placeholder' => __('lang_v1.all')]); !!}
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        {!! Form::label('account_group',  __('Account Group') . ':') !!}
                                        {!! Form::select('account_group', $account_groups, null, ['id'=>'account_group','class' => 'form-control select2', 'style' => 'width:100%', 'placeholder' => __('lang_v1.all')]); !!}
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        {!! Form::label('account_name',  __('Account Name') . ':') !!}
                                        {!! Form::select('account_name', $accounts, null, ['id'=>'account_name','class' => 'form-control select2', 'style' => 'width:100%', 'placeholder' => __('lang_v1.all')]); !!}
                                    </div>
                                </div>

                            @endcomponent
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <br>
                                <table class="table table-bordered table-striped" id="other_account_table"
                                    style="width: 100%;">
                                    <thead>
                                        <tr>
                                            <th>@lang( 'lang_v1.name' )</th>
                                            <th>@lang( 'lang_v1.account_type' )</th>
                                            <th>@lang( 'lang_v1.account_sub_type' )</th>
                                            <th>@lang( 'account.account_group' )</th>
                                            <th>@lang('account.account_number')</th>
                                            <th>@lang('lang_v1.balance')</th>
                                            <th>@lang('lang_v1.added_by')</th>
                                            <th class="notexport">@lang( 'messages.action' )</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="tab-pane" id="account_types">
                        <div class="row">
                            <div class="col-md-12">
                                <button type="button" class="btn btn-primary btn-modal pull-right" @if(!$account_access)
                                    disabled @endif data-href="{{action('AccountTypeController@create')}}"
                                    data-container="#account_type_modal">
                                    <i class="fa fa-plus"></i> @lang( 'messages.add' )</button>
                            </div>
                        </div>
                        <br>
                        <div class="row">
                            <div class="col-md-12">
                                <table class="table table-striped table-bordered" id="account_types_table"
                                    style="width: 100%;">
                                    <thead>
                                        <tr>
                                            <th>@lang( 'lang_v1.name' )</th>
                                            <th>@lang( 'messages.action' )</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($account_types as $account_type)
                                        <tr class="account_type_{{$account_type->id}}">
                                            <th>{{$account_type->name}}</th>
                                            <td>

                                                {!! Form::open(['url' => action('AccountTypeController@destroy',
                                                $account_type->id), 'method' => 'delete' ]) !!}
                                                <button type="button" class="btn btn-primary btn-modal btn-xs"
                                                    data-href="{{action('AccountTypeController@edit', $account_type->id)}}"
                                                    data-container="#account_type_modal">
                                                    <i class="fa fa-edit"></i> @lang( 'messages.edit' )</button>

                                                <button type="button" class="btn btn-danger btn-xs delete_account_type">
                                                    <i class="fa fa-trash"></i> @lang( 'messages.delete' )</button>
                                                {!! Form::close() !!}
                                            </td>
                                        </tr>
                                        @foreach($account_type->sub_types as $sub_type)
                                        <tr>
                                            <td>&nbsp;&nbsp;-- {{$sub_type->name}}</td>
                                            <td>


                                                {!! Form::open(['url' => action('AccountTypeController@destroy',
                                                $sub_type->id), 'method' => 'delete' ]) !!}
                                                <button type="button" class="btn btn-primary btn-modal btn-xs"
                                                    data-href="{{action('AccountTypeController@edit', $sub_type->id)}}"
                                                    data-container="#account_type_modal">
                                                    <i class="fa fa-edit"></i> @lang( 'messages.edit' )</button>
                                                <button type="button" class="btn btn-danger btn-xs delete_account_type">
                                                    <i class="fa fa-trash"></i> @lang( 'messages.delete' )</button>
                                                {!! Form::close() !!}
                                            </td>
                                        </tr>
                                        @endforeach
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="tab-pane" id="account_groups">
                        <div class="row">
                            <div class="col-md-12">
                                <button type="button" class="btn btn-primary btn-modal pull-right" @if(!$account_access)
                                    disabled @endif id="add_acount_group_btn"
                                    data-href="{{action('AccountGroupController@create')}}"
                                    data-container="#account_groups_modal">
                                    <i class="fa fa-plus"></i> @lang( 'messages.add' )</button>
                            </div>
                        </div>
                        <br>
                        <div class="row">
                            <div class="col-md-12">
                                <table class="table table-striped table-bordered" id="account_groups_table"
                                    style="width: 100%;">
                                    <thead>
                                        <tr>
                                            <th>@lang( 'lang_v1.name' )</th>
                                            <th>@lang( 'lang_v1.account_type_name' )</th>
                                            <th>@lang( 'lang_v1.note' )</th>
                                            <th>@lang( 'messages.action' )</th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    @can('account.settings')
                    <div class="tab-pane" id="account_settings">
                        @include('account_settings.index')
                    </div>
                    @endcan
                    <div class="tab-pane @if(session('status.tab') == 'list_deposit_transfer') active @endif" id="list_deposit_transfer">
                        @include('account.list_deposit_transfer')
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endcan

    <div class="modal fade account_model"  role="dialog" aria-labelledby="gridSystemModalLabel">
    </div>

    <div class="modal fade"  role="dialog" aria-labelledby="gridSystemModalLabel" id="account_type_modal">
    </div>
    <div class="modal fade"  role="dialog" aria-labelledby="gridSystemModalLabel"
        id="account_groups_modal">
    </div>
</section>
<!-- /.content -->

@endsection

@section('javascript')
<script src="{{ asset('plugins/bootstrap-datetimepicker/bootstrap-datetimepicker.min.js?v=' . $asset_v) }}"></script>
<script>
    $(document).ready(function(){
        var body = document.getElementsByTagName("body")[0];
        body.className += " sidebar-collapse";
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
            processing: true,
            serverSide: false,
            ajax: {
                url: '/accounting-module/account?account_type=other',
                data: function(d){
                    d.location_id = $('#location_id').val();
                    d.account_type_s = $('#account_type').val();
                    d.account_sub_type = $('#account_sub_type').val();
                    d.account_group = $('#account_group').val();
                    d.account_name = $('#account_name').val();

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
            @include('layouts.partials.datatable_export_button')
            "fnDrawCallback": function (oSettings) {
                __currency_convert_recursively($('#other_account_table'));
            },
            "rowCallback": function( row, data, index ) {
                
            }
        });

        // filter Data

        var filter = JSON.parse(`<?php echo json_encode($filterdata) ?>`);

        $('#location_id').change(function(){
            other_account_table.ajax.reload();
        })

        $('#account_type').change(function(){
            console.log(filter,$('#account_type').val());
            let change_val ='subType_'+ $('#account_type').val();
            // console.log(filter.subType_10);
            console.log('acctype',filter[change_val]);


            // $('#account_type').empty().trigger("change");
            $('#account_sub_type').select2('destroy').empty().select2(filter[change_val]).change();
            loadNamesOtion();

            // $("#account_sub_type").val("").change();
            other_account_table.ajax.reload();
        })

        $('#account_sub_type').change(function(){
            // console.log(filter,$('#account_sub_type').val());
            let change_val ='groupType_'+ $('#account_sub_type').val();
            console.log('ch',change_val);
            if(change_val == 'groupType_All'){
                change_val = 'groupType_';
            }
            if(change_val=="groupType_"){
                data = [{'id':'','text':'All'}];
                $('#account_sub_type option').each(function(){
                    if($(this).attr('value') != ''){
                        let newChangeVal ='groupType_'+ $(this).val();
                        if(filter[newChangeVal] && filter[newChangeVal]['data']){

                            for(var i in filter[newChangeVal]['data']) {
                                data.push(filter[newChangeVal]['data'][i]);
                            }

                        }
                    }
                    $('#account_group').select2('destroy').empty().select2({'data':data}).change();

                })

            }else{
               $('#account_group').select2('destroy').empty().select2(filter[change_val]).change();
            }
            loadNamesOtion();
            other_account_table.ajax.reload();

        
        })

        $('#account_group').change(function(){
            loadNamesOtion();
            other_account_table.ajax.reload();
        })

        $('#account_name').change(function(){
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

        function loadNamesOtion(){
            postData ={"account_type_s" : $('#account_type').val(),
                "account_sub_type": $('#account_sub_type').val(),
                 "account_group" : $('#account_group').val(),
                 "_token": "{{ csrf_token() }}"
                 }

            //      postData = "account_type_s =" + $('#account_type').val();
            // postData +=    "&account_sub_type =" + $('#account_sub_type').val();
            // postData +=    "&account_group_type =" + $('#account_group').val(),
            // postData +=    "&_token = {{ csrf_token() }}";
            $.ajax({
                method: "post",
                url: '/accounting-module/check_account_names',
                dataType: "json",
                data: postData,
                success:function(result){
                    console.log(result);
                    if(result.data){
                        $('#account_name').select2('destroy').empty().select2(result).change();
                    }
                }
            });
        }
        
    });

    $('.account_model').on('show.bs.modal', function(e) {
        get_cheques_list();
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


    function get_cheques_list(){
        if($('#transaction_date_range_cheque_deposit').val()){
            start_date = $('input#transaction_date_range_cheque_deposit').data('daterangepicker').startDate.format('YYYY-MM-DD');
            end_date = $('input#transaction_date_range_cheque_deposit').data('daterangepicker').endDate.format('YYYY-MM-DD');
            $.ajax({
                method: 'get',
                url: '{{action("AccountController@getChequeList")}}',
                data: { start_date, end_date },
                contentType: 'html',
                success: function(result) {
                    $('.account_model').find('#cheque_list_table tbody').empty().append(result);
                },
            });
        }
       
    }

    //account settings tab script
    $(document).ready(function () {
        account_setting_table = $('#account_setting_table').DataTable({
            processing: true,
            serverSide: false,
            ajax: {
                url: "{{action('AccountSettingController@index')}}",
                data: function(d){
                }
            },
            columns: [
                {data: 'date', name: 'date'},
                {data: 'account_group', name: 'account_groups.name'},
                {data: 'name', name: 'accounts.name'},
                {data: 'amount', name: 'amount'},
                {data: 'created_by', name: 'users.username'},
                {data: 'action', name: 'action'},
               
            ],
            @include('layouts.partials.datatable_export_button')
            "fnDrawCallback": function (oSettings) {
                __currency_convert_recursively($('#account_setting_table'));
            },
            "rowCallback": function( row, data, index ) {
                
            }
        });

        
    })
    $('#group_id').change(function () {
        group_id = $(this).val();

        $.ajax({
            method: 'get',
            url: '/accounting-module/get-account-by-group-id/'+group_id,
            data: {  },
            contentType: 'html',
            success: function(result) {
                $('#account_id').empty().append(result);
            },
        });
    })
    $('#date').datepicker('setDate', new Date());


    // list deposit and transfer account
    if($('#list_deposit_transfer_date_range').length) {
        $('#list_deposit_transfer_date_range').daterangepicker(
            dateRangeSettings,
            function (start, end) {
                $('#list_deposit_transfer_date_range').val(start.format(moment_date_format) + ' ~ ' + end.format(moment_date_format));
                list_deposit_transfer_table.ajax.reload();
            }
        );
        $('#list_deposit_transfer_date_range').on('cancel.daterangepicker', function(ev, picker) {
            $('#list_deposit_transfer_date_range').val('');
            list_deposit_transfer_table.ajax.reload();
        });
    }
    $(document).ready(function () {
        list_deposit_transfer_table = $('#list_deposit_transfer_table').DataTable({
            processing: true,
            serverSide: false,
            ajax: {
                url: '/accounting-module/list-deposit-transfer',
                data: function(d){
                    if($('#list_deposit_transfer_date_range').val()) {
                        var start = $('#list_deposit_transfer_date_range').data('daterangepicker').startDate.format('YYYY-MM-DD');
                        var end = $('#list_deposit_transfer_date_range').data('daterangepicker').endDate.format('YYYY-MM-DD');
                        d.start_date = start;
                        d.end_date = end;
                    }
                    d.sub_type = $('#list_deposit_transfer_type').val();
                    d.from_account_id = $('#from_account_id').val();
                    d.to_account_id = $('#to_account_id').val();
                    d.user_id = $('#user_id').val();
                }
            },
            columnDefs:[{
                    "targets": 7,
                    "orderable": false,
                    "searchable": false,
                    "width" : "30%",
                }],
            columns: [
                {data: 'action', name: 'action'},
                {data: 'operation_date', name: 'operation_date'},
                {data: 'sub_type', name: 'sub_type'},
                {data: 'amount', name: 'amount'},
                {data: 'from_account', name: 'from_account'},
                {data: 'to_account', name: 'to_account'},
                {data: 'cheque_number', name: 'cheque_number'},
                {data: 'username', name: 'users.username'},
            
               
            ],
            @include('layouts.partials.datatable_export_button')
            "fnDrawCallback": function (oSettings) {
                __currency_convert_recursively($('#list_deposit_transfer_table'));
            },
            "rowCallback": function( row, data, index ) {
                
            }
        });

        $('#list_deposit_transfer_date_range, #list_deposit_transfer_type, #from_account_id, #to_account_id, #user_id').change(function(){
            list_deposit_transfer_table.ajax.reload();
        })
    })
</script>
@endsection