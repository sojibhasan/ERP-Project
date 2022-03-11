@extends('layouts.app')
@section('title', __('superadmin::lang.referrals'))

@section('content')

<section class="content-header">
    <div class="row">
        <div class="col-md-12 dip_tab">
            <div class="settlement_tabs">
                <ul class="nav nav-tabs">
                    <li class="@if(empty(session('status.tab'))) active @endif" style="margin-left: 20px;">
                        <a style="font-size:13px;" href="#referral" class="" data-toggle="tab">
                            <i class="fa fa-superpowers"></i> <strong>@lang('superadmin::lang.referral')</strong>
                        </a>
                    </li>
                    <li class="@if(session('status.tab') == 'referral_stating_code') active @endif">
                        <a style="font-size:13px;" href="#starting_referral_code" data-toggle="tab">
                            <i class="fa fa-filter"></i>
                            <strong>@lang('superadmin::lang.starting_referral_code')</strong>
                        </a>
                    </li>
                    <li class="@if(session('status.tab') == 'referral_group') active @endif">
                        <a style="font-size:13px;" href="#referral_group" data-toggle="tab">
                            <i class="fa fa-connectdevelop"></i>
                            <strong>@lang('superadmin::lang.referral_group')</strong>
                        </a>
                    </li>
                    <li class="@if(session('status.tab') == 'income_method') active @endif">
                        <a style="font-size:13px;" href="#income_method" data-toggle="tab">
                            <i class="fa fa-money"></i>
                            <strong>@lang('superadmin::lang.income_method')</strong>
                        </a>
                    </li>

                </ul>
            </div>
        </div>
    </div>
    <div class="tab-content">
        <div class="tab-pane @if(empty(session('status.tab'))) active @endif" id="referral">
            @include('superadmin::referral.partials.referral')
        </div>
        <div class="tab-pane @if(session('status.tab') == 'referral_stating_code') active @endif"
            id="starting_referral_code">
            @include('superadmin::referral.partials.starting_referral_code')
        </div>
        <div class="tab-pane @if(session('status.tab') == 'referral_group') active @endif"
            id="referral_group">
            @include('superadmin::referral.partials.referral_group')
        </div>
        <div class="tab-pane @if(session('status.tab') == 'income_method') active @endif"
            id="income_method">
            @include('superadmin::income_method.index')
        </div>

    </div>

    <div class="modal fade pump_modal" role="dialog" aria-labelledby="gridSystemModalLabel">
    </div>
</section>


@endsection
@section('javascript')
<script>
    $('#date_range').daterangepicker(
        dateRangeSettings,
        function (start, end) {
            $('#date_range').val(start.format(moment_date_format) + ' ~ ' + end.format(moment_date_format));
            referral_table.ajax.reload();
        }
    );
    $('#date_range').on('cancel.daterangepicker', function(ev, picker) {
        $('#date_range').val('');
        referral_table.ajax.reload();
    });


    var columns = [
            { data: 'created_at', name: 'referrals.created_at' },
            { data: 'referral_code', name: 'referral_code' },
            { data: 'name_of_registeration', name: 'business.name' },
            { data: 'company_number', name: 'business.company_number' },
            { data: 'package_name', name: 'packages.name' },
        ];
  
    
    var referral_table = $('#referral_table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '{{action("\Modules\Superadmin\Http\Controllers\ReferralController@index")}}',
            data: function (d) {
                if($('#date_range').val()) {
                    var start = $('#date_range').data('daterangepicker').startDate.format('YYYY-MM-DD');
                    var end = $('#date_range').data('daterangepicker').endDate.format('YYYY-MM-DD');
                    d.start_date = start;
                    d.end_date = end;
                    d.referral_code = $('#referral_code').val();
                    d.package_id = $('#package_id').val();
                }
            }
        },
        columns: columns,
        fnDrawCallback: function(oSettings) {
        
        },
    });

    $('#referral_code, #date_range, #package_id').change(function(){
        referral_table.ajax.reload();
    })



    var referral_starting_code_table = $('#referral_starting_code_table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '{{action("\Modules\Superadmin\Http\Controllers\ReferralStartingCodeController@index")}}',
            data: function (d) {
            }
        },
        columns: [
            { data: 'date', name: 'date' },
            { data: 'group_name', name: 'group_name' },
            { data: 'prefix', name: 'prefix' },
            { data: 'starting_code', name: 'starting_code' },
            { data: 'action', name: 'action' }
        ],
        fnDrawCallback: function(oSettings) {
        
        },
    });

    var referral_group_table = $('#referral_group_table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '{{action("\Modules\Superadmin\Http\Controllers\ReferralGroupController@index")}}',
            data: function (d) {
            }
        },
        columns: [
            { data: 'date', name: 'date' },
            { data: 'group_name', name: 'group_name' },
            { data: 'added_by', name: 'added_by' }
        ],
        fnDrawCallback: function(oSettings) {
        
        },
    });


    $(document).on('click', 'a.delete_button', function(e){
        e.preventDefault();
        swal({
        title: LANG.sure,
        icon: "warning",
        buttons: true,
        dangerMode: true,
        }).then((willDelete) => {
        if (willDelete) {
            var href = $(this).data('href');
            $.ajax({
            url: href,
            method: 'DELETE',
            dataType: "json",
            success: function(result){
                if(result.success === true){
                toastr.success(result.msg);
                referral_starting_code_table.ajax.reload();
                } else {
                toastr.error(result.msg);
                }
            }
            });
        }
        });
        
    });

    var income_method_table = $('#income_method_table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '{{action("\Modules\Superadmin\Http\Controllers\IncomeMethodController@index")}}',
            data: function (d) {
            }
        },
        columns: [
            { data: 'action', name: 'action' },
            { data: 'date', name: 'date' },
            { data: 'group_name', name: 'group_name' },
            { data: 'income_method', name: 'income_method' },
            { data: 'status', name: 'status' },
            { data: 'income_type', name: 'income_type' },
            { data: 'value', name: 'value' },
            { data: 'minimum_new_signups', name: 'minimum_new_signups' },
            { data: 'minimum_active_subscriptions', name: 'minimum_active_subscriptions' },
            { data: 'comission_eligible_conditions', name: 'comission_eligible_conditions' },
        ],
        fnDrawCallback: function(oSettings) {
        
        },
    });

    $(document).on('click', 'a.delete_button', function (e) {
            e.preventDefault();
            swal({
                title: LANG.sure,
                text: 'This department will be deleted.',
                icon: 'warning',
                buttons: true,
                dangerMode: true,
            }).then((willDelete) => {
                if (willDelete) {
                    var href = $(this).data('href');
                    var data = $(this).serialize();

                    $.ajax({
                        method: 'DELETE',
                        url: href,
                        dataType: 'json',
                        data: data,
                        success: function (result) {
                            if (result.success === true) {
                                toastr.success(result.msg);
                                income_method_table.ajax.reload();
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