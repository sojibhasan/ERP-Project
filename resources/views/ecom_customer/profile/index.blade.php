@extends('layouts.ecom_customer')
@section('title', __('lang_v1.my_profile'))

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>@lang('lang_v1.my_profile')</h1>
    <!-- <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Level</a></li>
        <li class="active">Here</li>
    </ol> -->
</section>

<!-- Main content -->
<section class="content">
{!! Form::open(['url' => action('CustomerController@updatePassword'), 'method' => 'post', 'id' => 'edit_password_form',
            'class' => 'form-horizontal' ]) !!}
<div class="row">
    <div class="col-sm-12">
        <div class="box box-solid"> <!--business info box start-->
            <div class="box-header">
                <div class="box-header">
                    <h3 class="box-title"> @lang('user.change_password')</h3>
                </div>
            </div>
            <div class="box-body">
                <div class="form-group">
                    {!! Form::label('current_password', __('user.current_password') . ':', ['class' => 'col-sm-3 control-label']) !!}
                    <div class="col-sm-9">
                        <div class="input-group">
                            <span class="input-group-addon">
                                <i class="fa fa-lock"></i>
                            </span>
                            {!! Form::password('current_password', ['class' => 'form-control','placeholder' => __('user.current_password'), 'required']); !!}
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    {!! Form::label('new_password', __('user.new_password') . ':', ['class' => 'col-sm-3 control-label']) !!}
                    <div class="col-sm-9">
                        <div class="input-group">
                            <span class="input-group-addon">
                                <i class="fa fa-lock"></i>
                            </span>
                            {!! Form::password('new_password', ['class' => 'form-control','placeholder' => __('user.new_password'), 'required']); !!}
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    {!! Form::label('confirm_password', __('user.confirm_new_password') . ':', ['class' => 'col-sm-3 control-label']) !!}
                    <div class="col-sm-9">
                        <div class="input-group">
                            <span class="input-group-addon">
                                <i class="fa fa-lock"></i>
                            </span>
                            {!! Form::password('confirm_password', ['class' => 'form-control','placeholder' =>  __('user.confirm_new_password'), 'required']); !!}
                        </div>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary pull-right">@lang('messages.update')</button>
            </div>
        </div>
    </div>
</div>
{!! Form::close() !!}
@if( $dues == 0)
{!! Form::open(['url' => action('CustomerController@updateProfile'), 'method' => 'post', 'id' => 'edit_user_profile_form', 'files' => true ]) !!}
@endif
<div class="row">
    <div class="col-sm-8">
        <div class="box box-solid"> <!--business info box start-->
            <div class="box-header">
                <div class="box-header">
                    <h3 class="box-title"> @lang('user.edit_profile')</h3>
                </div>
            </div>
            <div class="box-body">
                <div class="form-group col-md-6">
                    {!! Form::label('first_name', __('business.first_name') . ':') !!}
                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="fa fa-info"></i>
                        </span>
                        {!! Form::text('first_name', $customer->first_name, ['class' => 'form-control','placeholder' => __('business.first_name'), 'required']); !!}
                    </div>
                </div>
                <div class="form-group col-md-6">
                    {!! Form::label('last_name', __('business.last_name') . ':') !!}
                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="fa fa-info"></i>
                        </span>
                        {!! Form::text('last_name', $customer->last_name, ['class' => 'form-control','placeholder' => __('business.last_name')]); !!}
                    </div>
                </div>
                <div class="form-group col-md-6">
                    {!! Form::label('email', __('business.email') . ':') !!}
                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="fa fa-info"></i>
                        </span>
                        {!! Form::email('email',  $customer->email, ['class' => 'form-control','placeholder' => __('business.email') ]); !!}
                    </div>
                </div>
                <div class="form-group col-md-6">
                    {!! Form::label('mobile', __('customer.mobile') . ':') !!}
                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="fa fa-info"></i>
                        </span>
                        {!! Form::text('mobile',  $customer->mobile, ['class' => 'form-control','placeholder' => __('customer.mobile') ]); !!}
                    </div>
                </div>
                <div class="form-group col-md-6">
                    {!! Form::label('contact_number', __('customer.contact_number') . ':') !!}
                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="fa fa-info"></i>
                        </span>
                        {!! Form::text('contact_number',  $customer->contact_number, ['class' => 'form-control','placeholder' => __('customer.contact_number') ]); !!}
                    </div>
                </div>
                <div class="form-group col-md-6">
                    {!! Form::label('landline', __('customer.landline') . ':') !!}
                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="fa fa-info"></i>
                        </span>
                        {!! Form::text('landline',  $customer->landline, ['class' => 'form-control','placeholder' => __('customer.landline') ]); !!}
                    </div>
                </div>
                <div class="form-group col-md-6">
                    {!! Form::label('geo_location', __('customer.geo_location') . ':') !!}
                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="fa fa-info"></i>
                        </span>
                        {!! Form::text('geo_location',  $customer->geo_location, ['class' => 'form-control','placeholder' => __('customer.geo_location') ]); !!}
                    </div>
                </div>
                <div class="form-group col-md-6">
                    {!! Form::label('address', __('customer.address') . ':') !!}
                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="fa fa-info"></i>
                        </span>
                        {!! Form::text('address',  $customer->address, ['class' => 'form-control','placeholder' => __('customer.address') ]); !!}
                    </div>
                </div>
                <div class="form-group col-md-6">
                    {!! Form::label('town', __('customer.town') . ':') !!}
                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="fa fa-info"></i>
                        </span>
                        {!! Form::text('town',  $customer->town, ['class' => 'form-control','placeholder' => __('customer.town') ]); !!}
                    </div>
                </div>
                <div class="form-group col-md-6">
                    {!! Form::label('district', __('customer.district') . ':') !!}
                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="fa fa-info"></i>
                        </span>
                        {!! Form::text('district',  $customer->district, ['class' => 'form-control','placeholder' => __('customer.district') ]); !!}
                    </div>
                </div>

            </div>
        </div>
    </div>
    <div class="col-md-4">
        @component('components.widget', ['title' => __('lang_v1.profile_photo')])
            @if(!empty($customer->media))
                <div class="col-md-12 text-center">
                    {!! $customer->media->thumbnail([150, 150], 'img-circle') !!}
                </div>
            @endif
            <div class="col-md-12">
                <div class="form-group">
                    {!! Form::label('profile_photo', __('lang_v1.upload_image') . ':') !!}
                    {!! Form::file('profile_photo', ['id' => 'profile_photo', 'accept' => 'image/*']); !!}
                    <small><p class="help-block">@lang('purchase.max_file_size', ['size' => (config('constants.document_size_limit') / 1000000)])</p></small>
                </div>
            </div>
        @endcomponent
    </div>
</div>

@if( $dues == 0)
<div class="row">
    <div class="col-md-12">
        <button type="submit" class="btn btn-primary pull-right">@lang('messages.update')</button>
    </div>
</div>
{!! Form::close() !!}
<script>
    $('.profile_div').find('.form-control').prop('disabled', true);
</script>
@endif

</section>
<!-- /.content -->
@endsection