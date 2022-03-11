@extends('layouts.app')
@section('title', __('superadmin::lang.import_export'))

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1> @lang('superadmin::lang.import_export')
        <small>@lang( 'superadmin::lang.manage_your_import_export')</small>
    </h1>
</section>

<!-- Main content -->
<section class="content">
    {!! Form::open(['url' => action('\Modules\Superadmin\Http\Controllers\ImportExportController@exportFile'), 'method'
    => 'get', 'id' => 'export_form' ]) !!}
    @component('components.widget', ['class' => 'box-primary', 'title' => __( 'superadmin::lang.export')])

    <div class="col-md-3">
        <div class="form-group">
            {!! Form::label('business_id', __('superadmin::lang.business') . ':') !!}
            {!! Form::select('business_id', $busineses, null, ['class' => 'form-control select2',
            'style' => 'width:100%', 'placeholder' => __('lang_v1.please_select'), 'required']); !!}
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            {!! Form::label('type', __('superadmin::lang.type') . ':') !!}
            {!! Form::select('type', $types, null, ['class' => 'form-control select2',
            'placeholder' => __('petro::lang.please_select'), 'style' => 'width:100%', 'required']); !!}
        </div>
    </div>
    <div class="col-md-3">
        <button type="submit" style="margin-top: 25px;" class="btn btn-success btn-sm">@lang('superadmin::lang.export')</button>
    </div>
    @endcomponent
    {!! Form::close() !!}


    {!! Form::open(['url' => action('\Modules\Superadmin\Http\Controllers\ImportExportController@importFile'), 'method'
    => 'post', 'id' => 'import_form',
    'enctype' => 'multipart/form-data' ]) !!}
    @component('components.widget', ['class' => 'box-primary', 'title' => __( 'superadmin::lang.import')])

    <div class="col-md-3">
        <div class="form-group">
            {!! Form::label('business_id', __('superadmin::lang.business') . ':') !!}
            {!! Form::select('business_id', $busineses, null, ['class' => 'form-control select2',
            'style' => 'width:100%', 'placeholder' => __('lang_v1.please_select'), 'required']); !!}
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            {!! Form::label('type', __('superadmin::lang.type') . ':*') !!}
            {!! Form::select('type', $types, null, ['class' => 'form-control select2',
            'placeholder' => __('petro::lang.please_select'), 'style' => 'width:100%', 'required']); !!}
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            {!! Form::label('file', __('superadmin::lang.file') . ':*') !!}
            {!! Form::file('file', ['files' => true, 'required']); !!}
        </div>
    </div>
    <div class="col-md-3">
        <button type="submit" style="margin-top: 25px;" class="btn btn-success btn-sm">@lang('superadmin::lang.import')</button>
    </div>
    @endcomponent
    {!! Form::close() !!}

</section>
<!-- /.content -->

@endsection
@section('javascript')
<script>
    $('form#export_form').validate();
    $('form#import_form').validate();
</script>
@endsection