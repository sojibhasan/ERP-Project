@extends('layouts.app')
@section('title', __('petro::lang.import_pumps'))

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>@lang('petro::lang.import_pumps')
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
            {!! Form::open(['url' => action('\Modules\Petro\Http\Controllers\PumpController@saveImport'), 'method' =>
            'post', 'enctype' => 'multipart/form-data' ]) !!}
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        {!! Form::label('location_id', __( 'petro::lang.branch' ) . ':*') !!}
                        {!! Form::select('location_id', $locations, null , ['class' => 'form-control select2
                        fuel_tank_location', 'required',
                        'placeholder' => __(
                        'petro::lang.please_select' ), 'style' => 'width: 100%;']); !!}
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group">
                        {!! Form::label('fuel_tank_id', __( 'petro::lang.fuel_tank' ) . ':*') !!}
                        {!! Form::select('fuel_tank_id', $fuel_tanks, null , ['class' => 'form-control select2
                        ', 'required',
                        'placeholder' => __(
                        'petro::lang.please_select' ), 'style' => 'width: 100%;']); !!}
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group">
                        {!! Form::label('transaction_date', __( 'petro::lang.transaction_date' ) . ':*') !!}
                        {!! Form::text('transaction_date', date('m/d/Y'), ['class' => 'form-control fuel_tank_date',
                        'required', 'placeholder' => __(
                        'petro::lang.transaction_date' ) ]); !!}
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group">
                        {!! Form::label('name', __( 'product.file_to_import' ) . ':') !!}
                        {!! Form::file('pumps_csv', ['accept'=> '.xls', 'required' => 'required', 'style' =>
                        'margin-top: 5px;']); !!}
                    </div>
                </div>
                <div class="clearfix"></div>
                <div class="col-sm-3 col-md-offset-9">
                    <button type="submit" class="btn btn-primary pull-right">@lang('messages.submit')</button>
                </div>

            </div>

            {!! Form::close() !!}
            <br><br>
            {{-- <div class="row">
                    <div class="col-sm-4">
                        <a href="{{ asset('files/import_pumps_csv_template.xls') }}" class="btn btn-success"
            download><i class="fa fa-download"></i> @lang('lang_v1.download_template_file')</a>
        </div>
    </div> --}}
    @endcomponent
    </div>
    </div>

</section>
<!-- /.content -->
@endsection

@section('javascript')
<script>
    $('.fuel_tank_date').datepicker();
</script>
@endsection