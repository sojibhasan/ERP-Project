
<!-- Main content -->
<section class="content">
    <div class="row">
        <div class="col-md-12">
            @component('components.filters', ['title' => __('report.filters')])
            {!! Form::open(['url' => action('\Modules\HR\Http\Controllers\AttendanceController@create'), 'method' =>
            'get', 'id' => 'get_attendance_from']) !!}
            <div class="col-sm-3">
                <div class="form-group">
                    <label>@lang('hr::lang.date') <span class="required">*</span></label>

                    <div class="input-group">
                        <input type="text" name="date" id="date" class="form-control" value="">
                        <div class="input-group-addon">
                            <i class="fa fa-calendar"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-3">
                <div class="form-group">
                    <label>@lang('hr::lang.department')</label>
                    {!! Form::select('department_id', $departments, null, ['class' => 'form-control select2', 'placeholder' => __('lang_v1.please_select'), 'id' => 'department_id']) !!}
                </div>
            </div>
            <div class="form-group">
                <div class="col-sm-3">
                    <button type="submit" id="sbtn" name="sbtn" value="1" style="margin-top: 24px;"
                        class="btn bg-primary btn-md">@lang('hr::lang.go') </button>
                </div>
            </div>
            {!! Form::close() !!}
            @endcomponent
        </div>
    </div>

    <div id="set_attendance_div"></div>

</section>