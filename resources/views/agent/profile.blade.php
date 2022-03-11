@extends('layouts.agent')
@section('title', __('lang_v1.my_profile'))

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>@lang('lang_v1.my_profile')</h1>
</section>

<!-- Main content -->
<section class="content">
{!! Form::open(['url' => action('AgentController@updatePassword'), 'method' => 'post', 'id' => 'edit_password_form',
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
{!! Form::open(['url' => action('AgentController@updateProfile'), 'method' => 'post', 'id' => 'edit_user_profile_form', 'files' => true ]) !!}

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
                    {!! Form::label('name', __('business.name') . ':') !!}
                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="fa fa-info"></i>
                        </span>
                        {!! Form::text('name', $agent->name, ['class' => 'form-control','placeholder' => __('business.name') , 'readonly']); !!}
                    </div>
                </div>
                <div class="form-group col-md-6">
                    {!! Form::label('email', __('business.email') . ':') !!}
                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="fa fa-info"></i>
                        </span>
                        {!! Form::email('email',  $agent->email, ['class' => 'form-control','placeholder' => __('business.email') ]); !!}
                    </div>
                </div>
                <div class="form-group col-md-6">
                    {!! Form::label('mobile_number', __('customer.mobile') . ':') !!}
                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="fa fa-info"></i>
                        </span>
                        {!! Form::text('mobile_number',  $agent->mobile_number, ['class' => 'form-control','placeholder' => __('customer.mobile_number') ]); !!}
                    </div>
                </div>
                <div class="form-group col-md-6">
                    {!! Form::label('land_number', __('superadmin::lang.land_number') . ':') !!}
                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="fa fa-info"></i>
                        </span>
                        {!! Form::text('land_number',  $agent->land_number, ['class' => 'form-control','placeholder' => __('superadmin::lang.land_number') ]); !!}
                    </div>
                </div>
                <div class="form-group col-md-6">
                    {!! Form::label('nic_number	', __('superadmin::lang.nic_number') . ':') !!}
                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="fa fa-info"></i>
                        </span>
                        {!! Form::text('nic_number	',  $agent->nic_number	, ['class' => 'form-control','placeholder' => __('superadmin::lang.nic_number'), 'readonly' ]); !!}
                    </div>
                </div>
                <div class="form-group col-md-6">
                    {!! Form::label('address', __('customer.address') . ':') !!}
                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="fa fa-info"></i>
                        </span>
                        {!! Form::text('address',  $agent->address, ['class' => 'form-control','placeholder' => __('customer.address') ]); !!}
                    </div>
                </div>

            </div>
        </div>
    </div>
    <div class="col-md-4">
        @component('components.widget', ['title' => __('superadmin::lang.agent_photo')])
            @if(!empty($agent->agent_photo))
                <div class="col-md-12 text-center">
                    <img src="{{asset('uploads/agents/'.$agent->agent_photo)}}" width="150" height="150" class="img-circle">
                </div>
            @endif
            <div class="col-md-12">
                <div class="form-group">
                    {!! Form::label('agent_photo', __('superadmin::lang.agent_photo') . ':') !!}
                    {!! Form::file('agent_photo', ['id' => 'agent_photo', 'accept' => 'image/*']); !!}
                    <small><p class="help-block">@lang('purchase.max_file_size', ['size' => (config('constants.document_size_limit') / 1000000)])</p></small>
                </div>
            </div>
        @endcomponent
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <button type="submit" class="btn btn-primary pull-right">@lang('messages.update')</button>
    </div>
</div>
{!! Form::close() !!}
<script>
    $('.profile_div').find('.form-control').prop('disabled', true);
</script>

</section>
<!-- /.content -->
@endsection