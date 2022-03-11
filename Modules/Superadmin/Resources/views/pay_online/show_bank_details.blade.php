@extends('layouts.app')
@section('title', __('superadmin::lang.pay_offline'))

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1> @lang('superadmin::lang.pay_offline')
    </h1>
</section>

<!-- Main content -->
<section class="content">


    @component('components.widget', ['class' => 'box-primary', 'title' => __( 'superadmin::lang.bank_details',
    ['contacts' =>
    __('superadmin::lang.') ])])

    <table class="table table-condensed" style="width: 50%">
        <tbody>
            <tr>
                <td>
                    <strong>@lang('superadmin::lang.bank_name')</strong>
                </td>
                <td>
                    {{env('PAY_ONLINE_BANK_NAME')}}
                </td>
            </tr>
            <tr>
                <td>
                    <strong>@lang('superadmin::lang.branch_name')</strong>
                </td>
                <td>
                    {{env('PAY_ONLINE_BRANCH_NAME')}}
                </td>
            </tr>
            <tr>
                <td>
                    <strong>@lang('superadmin::lang.account_no')</strong>
                </td>
                <td>
                    {{env('PAY_ONLINE_ACCOUNT_NO')}}
                </td>
            </tr>
            <tr>
                <td>
                    <strong>@lang('superadmin::lang.account_name')</strong>
                </td>
                <td>
                    {{env('PAY_ONLINE_ACCOUNT_NAME')}}
                </td>
            </tr>
            <tr>
                <td>
                    <strong>@lang('superadmin::lang.swift_code')</strong>
                </td>
                <td>
                    {{env('PAY_ONLINE_SWIFT_CODE')}}
                </td>
            </tr>
            <tr>
                <td>
                    <strong>@lang('superadmin::lang.amount')</strong>
                </td>
                <td>
                    {{@num_format($amount)}}
                </td>
            </tr>
        </tbody>
    </table>
    @endcomponent

</section>
<!-- /.content -->

@endsection
@section('javascript')

@endsection