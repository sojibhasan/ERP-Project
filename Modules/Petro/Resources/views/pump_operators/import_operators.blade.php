@extends('layouts.app')
@section('title', __('petro::lang.import_operators'))

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>@lang('petro::lang.import_operators')
    </h1>
</section>

<!-- Main content -->
<section class="content">

    @if (session('notification') || !empty($notification))
    <div class="row">
        <div class="col-sm-12">
            <div class="alert alert-danger alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                @if(!empty($notification['msg']))
                {{$notification['msg']}}
                @elseif(session('notification.msg'))
                {{ session('notification.msg') }}
                @endif
            </div>
        </div>
    </div>
    @endif

    <div class="row">
        <div class="col-sm-12">
            @component('components.widget', ['class' => 'box-primary'])
            {!! Form::open(['url' => action('\Modules\Petro\Http\Controllers\PumpOperatorController@saveImport'), 'method' =>
            'post', 'enctype' => 'multipart/form-data' ]) !!}
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        {!! Form::label('location_id', __( 'petro::lang.branch' ) . ':*') !!}
                        {!! Form::select('location_id', $business_locations, null , ['class' => 'form-control select2
                        fuel_tank_location', 'required',
                        'placeholder' => __(
                        'petro::lang.please_select' ), 'style' => 'width: 100%;']); !!}
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group">
                        {!! Form::label('commission_type', __( 'petro::lang.commission_type' ) . ':*') !!}
                        {!! Form::select('commission_type', ['fixed' => 'Fixed', 'percentage' => 'Percentage'], null
                        , ['class' => 'form-control select2
                        commission_type', 'required',
                        'placeholder' => __(
                        'petro::lang.please_select' ), 'style' => 'width: 100%;']); !!}
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group">
                        {!! Form::label('name', __( 'product.file_to_import' ) . ':') !!}
                        {!! Form::file('pumps_csv', ['accept'=> '.xls', 'required' => 'required', 'style' =>
                        'margin-top: 5px;']); !!}
                    </div>
                </div>
                <div class="col-md-3" style="margin-top:15px;">
                    <button type="submit" class="btn btn-primary">@lang('messages.submit')</button>
                </div>

            </div>

            {!! Form::close() !!}
            <br><br>
            <div class="row">
                <div class="col-sm-4">
                    <a href="{{ asset('files/import_pump_operators.xls') }}" class="btn btn-success"
                        download><i class="fa fa-download"></i> @lang('lang_v1.download_template_file')</a>
                </div>
            </div>
    @endcomponent
    </div>
    </div>

</section>
<!-- /.content -->
@endsection

@section('javascript')
<script>

</script>
@endsection