@extends('layouts.app')

@section('title', __('report.activity_report'))

@section('content')
<!-- Main content -->
<section class="content">

    <div class="row">
        <div class="col-md-12">
            <div class="settlement_tabs">
                <ul class="nav nav-tabs no-print">
                    @can('sales_report.view')
                    <li class="@if(empty($is_expense)) active @endif">
                        <a href="#sales_report" class="sales_report" data-toggle="tab">
                            <i class="fa fa-file-text-o"></i> <strong>@lang('report.sales_report')</strong>
                        </a>
                    </li>
                    @endcan
                    
                    @can('purchase_and_slae_report.view')
                    <li class="">
                        <a href="#purchase_sell" class="purchase_sell" data-toggle="tab">
                            <i class="fa fa-file-text-o"></i> <strong>@lang('report.purchase_sell')</strong>
                        </a>
                    </li>
                    @endcan
                    
                    @can('expense_report.view')
                    <li class="@if(!empty($is_expense)) active @endif">
                        <a href="#expense_report" class="expense_report" data-toggle="tab">
                            <i class="fa fa-file-text-o"></i> <strong>@lang('report.expense_report')</strong>
                        </a>
                    </li>
                    @endcan

                    @can('sales_representative.view')
                    <li class="">
                        <a href="#sales_representative" class="sales_representative" data-toggle="tab">
                            <i class="fa fa-file-text-o"></i> <strong>@lang('report.sales_representative')</strong>
                        </a>
                    </li>
                    @endcan

                    @can('tax_report.view')
                    <li class="">
                        <a href="#tax_report" class="tax_report" data-toggle="tab">
                            <i class="fa fa-file-text-o"></i> <strong>@lang('report.tax_report')</strong>
                        </a>
                    </li>
                    @endcan
                </ul>
                <div class="tab-content">
                    @can('sales_report.view')
                    <div class="tab-pane @if(empty($is_expense)) active @endif" id="sales_report">
                        @include('report.sales_report')
                    </div>
                    @endcan

                    @can('purchase_and_slae_report.view')
                    <div class="tab-pane" id="purchase_sell">
                        @include('report.purchase_sell')
                    </div>
                    @endcan

                    @can('expense_report.view')
                    <div class="tab-pane @if(!empty($is_expense)) active @endif" id="expense_report">
                        @include('report.expense_report')
                    </div>
                    @endcan

                    @can('sales_representative.view')
                    <div class="tab-pane" id="sales_representative">
                        @include('report.sales_representative')
                    </div>
                    @endcan

                    @can('tax_report.view')
                    <div class="tab-pane" id="tax_report">
                        @include('report.tax_report')
                    </div>
                    @endcan

                </div>
            </div>
        </div>
    </div>

</section>
<!-- /.content -->

@endsection
@section('javascript')
<script src="{{ asset('js/report.js?v=' . $asset_v) }}"></script>
<script src="{{ asset('js/payment.js?v=' . $asset_v) }}"></script>
<script src="https://code.highcharts.com/highcharts.js"></script>
{!! $chart->script() !!}


@endsection