
<!-- Main content -->
<section class="content">
    <div class="row">
        <div class="col-md-12">
            <!-- general form elements -->
            <div class="box box-primary">
                <div class="box-header with-border bg-primary-dark">
                    <h3 class="box-title">@lang('hr::lang.import_attendance')</h3>
                </div>
                <!-- /.box-header -->
                <!-- form start -->

                <div class="box-body">
                    <div class="row">
						<div class="col-md-5">
                            <div id="msg"></div>
                            {!! Form::open(['url' => action('\Modules\HR\Http\Controllers\AttendanceController@postImportAttendance'), 'method' => 'post', 'files' => true]) !!}
							<div class="row">
								<div class="col-md-12">
                                    <div class="row">
                                        <div class="col-md-10">
                                            <strong>@lang('hr::lang.download_sample_csv_file') </strong><br/>
                                            <p>Import employee attendance use <strong>Employee ID</strong> Search from bellow Table</p>
                                            <p>Attendance Status: 1 = Present | 0 = Absent | 3 = On leave</p>
                                            <p>Date Format: Month/Day/Year | 1/31/2017</p>
                                            <a href="{{url('files/attendance.csv')}}"><i class="fa fa-download" aria-hidden="true"></i> @lang('hr::lang.sample_csv_file') </a>
                                            <div class="form-group">
                                                <label>@lang('hr::lang.import_attendance') </label>
                                                <input type="file" name="import" class="form-control">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <button class="btn btn-primary pull-right" type="submit" value="Submit"><i class="fa fa-upload"></i> @lang('hr::lang.import_attendance') </button>
                            {!! Form::close() !!}
                        </div>
                    </div>

                    <div class="margin"></div>

                    <div class="row">
                        <div class="col-md-12">
                            <table id="employee_table" class="table table-striped table-bordered" cellspacing="0"
                                width="100%">
                                <thead>
                                    <tr>
                                        <th>@lang('hr::lang.employee_id')</th>
                                        <th>@lang('hr::lang.employee_number')</th>
                                        <th>@lang('hr::lang.employee_name')</th>
                                        <th>@lang('hr::lang.department')</th>
                                        <th>@lang('hr::lang.job_title')</th>
                                        <th>@lang('hr::lang.employment_status')</th>
                                        <th>@lang('hr::lang.shift')</th>
                                   
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>
                <!-- /.box-body -->
            </div>
            <!-- /.box -->

        </div>
    </div>
</section>
