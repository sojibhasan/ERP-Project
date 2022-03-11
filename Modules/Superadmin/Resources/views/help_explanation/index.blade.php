@extends('layouts.app')
@section('title', __('superadmin::lang.superadmin') . ' | ' . __('superadmin::lang.help_explanation'))

@section('content')
<section class="content-header">
    {!! Form::open(['url' => action('\Modules\Superadmin\Http\Controllers\HelpExplanationController@store'), 'method' => 'post', 'id' => 'bussiness_edit_form',
           'files' => true ]) !!}
    <div class="row">
        <div class="col-md-12 dip_tab">
            <div class="settlement_tabs">
                <ul class="nav nav-tabs">
                    <li class="active" style="margin-left: 20px;">
                        <a style="font-size:13px;" href="#business_settings" class="" data-toggle="tab">
                            <i class="fa fa-superpowers"></i> <strong>@lang('superadmin::lang.business_settings')</strong>
                        </a>
                    </li>
                    <li class="">
                        <a style="font-size:13px;" href="#home_page" data-toggle="tab">
                            <i class="fa fa-home"></i> <strong>@lang('superadmin::lang.home_page')</strong>
                        </a>
                    </li>
                    <li class="">
                        <a style="font-size:13px;" href="#customer_related" data-toggle="tab">
                            <i class="fa fa-user"></i> <strong>@lang('superadmin::lang.customer_related')</strong>
                        </a>
                    </li>
                    <li class="">
                        <a style="font-size:13px;" href="#product" data-toggle="tab">
                            <i class="fa fa-cubes"></i> <strong>@lang('superadmin::lang.product')</strong>
                        </a>
                    </li>
                    <li class="">
                        <a style="font-size:13px;" href="#unit" data-toggle="tab">
                            <i class="fa fa-balance-scale"></i> <strong>@lang('superadmin::lang.unit')</strong>
                        </a>
                    </li>
                    <li class="">
                        <a style="font-size:13px;" href="#categories" data-toggle="tab">
                            <i class="fa fa-tags"></i> <strong>@lang('superadmin::lang.categories')</strong>
                        </a>
                    </li>
                    <li class="">
                        <a style="font-size:13px;" href="#petro" data-toggle="tab">
                            <i class="fa fa-tags"></i> <strong>@lang('superadmin::lang.petro')</strong>
                        </a>
                    </li>
                 
                </ul>
            </div>
        </div>
    </div>
    <div class="tab-content">
        <div class="tab-pane active" id="business_settings">
            @include('superadmin::help_explanation.partials.business_settings')
        </div>
        <div class="tab-pane" id="home_page">
            @include('superadmin::help_explanation.partials.home_page')
        </div>
        <div class="tab-pane" id="customer_related">
            @include('superadmin::help_explanation.partials.customer_related')
        </div>
        <div class="tab-pane" id="product">
            @include('superadmin::help_explanation.partials.product')
        </div>
        <div class="tab-pane" id="unit">
            @include('superadmin::help_explanation.partials.unit')
        </div>
        <div class="tab-pane" id="categories">
            @include('superadmin::help_explanation.partials.categories')
        </div>
        <div class="tab-pane" id="petro">
            @include('superadmin::help_explanation.partials.petro')
        </div>
    </div>

    <div class="modal fade pump_modal" role="dialog" aria-labelledby="gridSystemModalLabel">
    </div>
    <div class="row">
        <div class="col-md-12">
            <button type="submit" class="btn btn-danger pull-right">@lang('messages.submit')</button>
        </div>
    </div>
    {!! Form::close() !!}
</section>
@stop
@section('javascript')
<script type="text/javascript">
	
</script>
@endsection