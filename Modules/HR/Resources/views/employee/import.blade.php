@extends('layouts.app')
@section('title', __('hr.import_employee'))

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>@lang('hr::lang.import_employee')</h1>
</section>

<!-- Main content -->
<section class="content">
    <div class="row">
        <!-- /.col -->
        <div class="col-md-12">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">
                        @lang('hr::lang.import_employee')
                    </h3>
                    <!-- /.box-tools -->
                </div>
                <!-- /.box-header -->

                {!! Form::open(['url' => action('\Modules\HR\Http\Controllers\EmployeeController@postImportEmployee'), 'method' => 'post', 'id' =>
                'employee_import', 'files' => true ]) !!}

                <div class="box-body">


                    <div class="row">
                        <div class="col-md-6">
                            <div id="msg"></div>
                            <div class="row">
                                <div class="col-md-12">

                                    <div class="row">

                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>@lang('import_employee') </label>
                                                <input type="file" name="import" class="form-control">
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>


                            <button class="btn btn-primary" type="submit" value="Submit"><i class="fa fa-upload"></i>
                                @lang('hr::lang.import_employee') </button>
                        </div>

                        <div class="col-md-6">
                            <strong>@lang('hr::lang.download_sample_csv_file')</strong><br />
                            <a href="{{url('files/employee.csv')}}"><i class="fa fa-download" aria-hidden="true"></i> @lang('sample_csv_file')</a>
                        </div>
                    </div>


                </div>
                <!-- /.box-body -->

                <div class="box-footer">

                </div>
                {!! Form::close() !!}

            </div>
            <!-- /.box -->
        </div>
        <!-- /.col -->
    </div>
    <!-- /.row -->
</section>
@endsection

@section('javascript')

@endsection