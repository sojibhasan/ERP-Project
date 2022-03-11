@extends('layouts.app')
@section('title', __('business.settings'))
@php
$business_or_entity = App\System::getProperty('business_or_entity');
@endphp
@section('content')
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>@if($business_or_entity == 'business'){{ __('business.settings') }} @endif @if($business_or_entity ==
        'entity'){{ __('lang_v1.entity_settings') }} @endif</h1>
    <br>
    @include('layouts.partials.search_settings')
</section>
<link rel="stylesheet" href="{{asset('css/editor.css')}}">
<style>
    .select2-results__option[aria-selected="true"] {
        display: none;
    }

    .equal-column {
        min-height: 95px;
    }
</style>
<!-- Main content -->
<section class="content">
    {!! Form::open(['url' => action('BusinessController@postBusinessSettings'), 'method' => 'post', 'id' =>
    'bussiness_edit_form',
    'files' => true ]) !!}
    <div class="row">
        <div class="col-xs-12">
            <!--  <pos-tab-container> -->
            <div class="col-xs-12 pos-tab-container">
                <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2 pos-tab-menu">
                    <div class="list-group">
                        @can('property.settings.unit')
                        <a href="#"
                            class="list-group-item text-center @if(empty(session('status.tab'))) active @endif">@lang('property::lang.unit')</a>
                        @endcan
                        @can('property.settings.tax')
                        <a href="#"
                            class="list-group-item text-center @if(session('status.tab') == 'taxes') active @endif">@lang('property::lang.property_taxes')</a>
                        @endcan
                        @can('property.settings.tax')
                        <a href="#"
                            class="list-group-item text-center @if(session('status.tab') == 'payment-options') active @endif">@lang('property::lang.payment_options')</a>
                        @endcan
                        @can('property.settings.tax')
                        <a href="#"
                            class="list-group-item text-center @if(session('status.tab') == 'finance-options') active @endif">@lang('property::lang.finance_options')</a>
                        @endcan
                        @can('property.settings.tax')
                        <a href="#"
                            class="list-group-item text-center @if(session('status.tab') == 'installment-cycle') active @endif">@lang('property::lang.installment_cycle')</a>
                        @endcan
                        @can('property.settings.block_close_reason')
                        <a href="#"
                            class="list-group-item text-center @if(session('status.tab') == 'block-close-reason') active @endif">@lang('property::lang.block_close_reason')</a>
                        @endcan
                        @can('property.settings.sales_officer')
                        <a href="#"
                            class="list-group-item text-center @if(session('status.tab') == 'sales-officer') active @endif">@lang('property::lang.sales_officer')</a>
                        @endcan
                    </div>
                </div>
                <div class="col-lg-10 col-md-10 col-sm-10 col-xs-10 pos-tab">
                    @can('property.settings.unit')
                    @include('property::setting.unit.index')
                    @endcan
                    @can('property.settings.tax')
                    @include('property::setting.property_taxes.index')
                    @endcan
                    @can('property.settings.tax')
                    @include('property::setting.payment_options.index')
                    @endcan
                    @can('property.settings.tax')
                    @include('property::setting.finance_options.index')
                    @endcan
                    @can('property.settings.tax')
                    @include('property::setting.installment_cycle.index')
                    @endcan
                    @can('property.settings.block_close_reason')
                    @include('property::setting.block_close_reason.index')
                    @endcan
                    @can('property.settings.sales_officer')
                    @include('property::setting.sales_officer.index')
                    @endcan

                </div>
            </div>
            <!--  </pos-tab-container> -->
        </div>
    </div>

    <div class="row">
        <div class="col-sm-12">
            <button class="btn btn-danger pull-right settingForm_button"
                type="submit">@lang('business.update_settings')</button>
        </div>
    </div>
    {!! Form::close() !!}

    <div class="modal fade unit_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
    </div>

</section>
<!-- /.content -->
@stop
@section('javascript')
<script src="{{asset('js/editor.js')}}"></script>

<script>
    $(document).ready(function(){
        property_taxes_table = $('#property_taxes_table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{action('\Modules\Property\Http\Controllers\PropertyTaxesController@index')}}",
            },
            columns: [
                { data: 'location_name', name: 'business_locations.name' },
                { data: 'tax_name', name: 'tax_name' },
                { data: 'tax_type', name: 'tax_type' },
                { data: 'value', name: 'value' },
                { data: 'action', name: 'action', searchable: false, sortable: false },
            ],
        });
        payment_option_table = $('#payment_option_table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{action('\Modules\Property\Http\Controllers\PaymentOptionController@index')}}",
            },
            columns: [
                { data: 'date', name: 'date' },
                { data: 'location_name', name: 'business_locations.name' },
                { data: 'payment_option', name: 'payment_option' },
                { data: 'credit_account', name: 'accounts.name' },
                { data: 'created_by', name: 'users.username' },
                { data: 'action', name: 'action' },
            ],
        });
        finance_option_table = $('#finance_option_table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{action('\Modules\Property\Http\Controllers\FinanceOptionController@index')}}",
            },
            columns: [
                { data: 'date', name: 'date' },
                { data: 'location_name', name: 'business_locations.name' },
                { data: 'finance_option', name: 'finance_option' },
                { data: 'custom_payments', name: 'custom_payments' },
                { data: 'created_by', name: 'users.username' },
                { data: 'action', name: 'action' },
            ],
        });
        installment_cycle_table = $('#installment_cycle_table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{action('\Modules\Property\Http\Controllers\InstallmentCycleController@index')}}",
            },
            columns: [
                { data: 'date', name: 'date' },
                { data: 'name', name: 'name' },
                { data: 'created_by', name: 'users.username' },
                { data: 'action', name: 'action' },
            ],
        });
        block_close_reason_table = $('#block_close_reason_table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{action('\Modules\Property\Http\Controllers\BlockCloseReasonController@index')}}",
            },
            columns: [
                { data: 'date', name: 'date' },
                { data: 'reason', name: 'reason' },
                { data: 'created_by', name: 'users.username' },
                { data: 'action', name: 'action' },
            ],
        });
        sales_officer_table = $('#sales_officer_table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{action('\Modules\Property\Http\Controllers\SalesOfficerController@index')}}",
            },
            columns: [
                { data: 'date', name: 'date' },
                { data: 'name', name: 'name' },
                { data: 'username', name: 'username' },
                { data: 'created_by', name: 'users.username' },
                { data: 'action', name: 'action' },
            ],
        });
    })

    $(document).on('click', '.delete_property_tax_button', function (e) {
        e.preventDefault();
        swal({
            title: LANG.sure,
            text: 'This item will be deleted',
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
                        if (result.success == true) {
                            toastr.success(result.msg);
                        } else {
                            toastr.error(result.msg);
                        }
                        property_taxes_table.ajax.reload();
                    },
                });
            }
        });
    });
  

    $(document).on('submit', 'form#payment_option_add_form, form#quick_add_payment_option_form', function(e){
        e.preventDefault();
        var data = $(this).serialize();

        $.ajax({
            method: "POST",
            url: $(this).attr("action"),
            dataType: "json",
            data: data,
            success: function(result){
                if(result.success == true){
                    $('div.view_modal').modal('hide');
                    toastr.success(result.msg);
                    payment_option_table.ajax.reload();
                } else {
                    toastr.error(result.msg);
                }
            }
        });
    });

    $(document).on('submit', 'form#installment_cycle_add_form', function(e){
        e.preventDefault();
        var data = $(this).serialize();

        $.ajax({
            method: "POST",
            url: $(this).attr("action"),
            dataType: "json",
            data: data,
            success: function(result){
                if(result.success == true){
                    $('div.view_modal').modal('hide');
                    toastr.success(result.msg);
                    installment_cycle_table.ajax.reload();
                } else {
                    toastr.error(result.msg);
                }
            }
        });
    });

    $(document).on('submit', 'form#block_close_reason_add_form', function(e){
        e.preventDefault();
        var data = $(this).serialize();

        $.ajax({
            method: "POST",
            url: $(this).attr("action"),
            dataType: "json",
            data: data,
            success: function(result){
                if(result.success == true){
                    $('div.view_modal').modal('hide');
                    toastr.success(result.msg);
                    block_close_reason_table.ajax.reload();
                } else {
                    toastr.error(result.msg);
                }
            }
        });
    });

    $(document).on('submit', 'form#sales_officer_add_form', function(e){
        e.preventDefault();
        var data = $(this).serialize();

        $.ajax({
            method: "POST",
            url: $(this).attr("action"),
            dataType: "json",
            data: data,
            success: function(result){
                if(result.success == true){
                    $('div.view_modal').modal('hide');
                    toastr.success(result.msg);
                    sales_officer_table.ajax.reload();
                } else {
                    toastr.error(result.msg);
                }
            }
        });
    });

    $(document).on('click', '.delete_finance_option_button, .delete_payment_option_button, .delete_installment_cycle_button, .delete_block_close_reason_button, .delete_sales_officer_button', function (e) {
        e.preventDefault();
        swal({
            title: LANG.sure,
            text: 'This item will be deleted',
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
                        if (result.success == true) {
                            toastr.success(result.msg);
                        } else {
                            toastr.error(result.msg);
                        }
                        finance_option_table.ajax.reload();
                        payment_option_table.ajax.reload();
                        installment_cycle_table.ajax.reload();
                        block_close_reason_table.ajax.reload();
                        sales_officer_table.ajax.reload();
                    },
                });
            }
        });
    });

    $(document).on('submit', 'form#finance_option_add_form, form#quick_add_finance_option_form', function(e){
        e.preventDefault();
        var data = $(this).serialize();
        $.ajax({
            method: "POST",
            url: $(this).attr("action"),
            dataType: "json",
            data: data,
            success: function(result){
                if(result.success == true){
                    $('div.view_modal').modal('hide');
                    toastr.success(result.msg);
                    finance_option_table.ajax.reload();
                } else {
                    toastr.error(result.msg);
                }
            }
        });
    });
    $(document).on('submit', 'form#finance_option_edit_form, form#payment_option_edit_form, form#installment_cycle_edit_form, form#block_close_reason_edit_form, form#sales_officer_edit_form', function(e){
        e.preventDefault();
        var data = $(this).serialize();
        $.ajax({
            method: "PUT",
            url: $(this).attr("action"),
            dataType: "json",
            data: data,
            success: function(result){
                if(result.success == true){
                    $('div.view_modal').modal('hide');
                    toastr.success(result.msg);
                    finance_option_table.ajax.reload();
                    payment_option_table.ajax.reload();
                    installment_cycle_table.ajax.reload();
                    block_close_reason_table.ajax.reload();
                    sales_officer_table.ajax.reload();
                } else {
                    toastr.error(result.msg);
                }
            }
        });
    });
</script>
@endsection