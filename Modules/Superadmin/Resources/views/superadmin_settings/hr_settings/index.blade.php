<div class="pos-tab-content">
    {{-- <style>
        .select2-results__option[aria-selected="true"] {
            display: none;
        }

        .equal-column {
            min-height: 95px;
        }
    </style> --}}
    <!-- Main content -->


    <div class="row">
        <div class="col-md-12">
            <div class="settlement_tabs">
                <ul class="nav nav-tabs">
                    <li class="active">
                        <a href="#department" class="department" data-toggle="tab">
                            <i class="fa fa-file-text-o"></i> <strong>@lang('hr::lang.department')</strong>
                        </a>
                    </li>
                    <li class="">
                        <a href="#job_title" class="job_title" data-toggle="tab">
                            <i class="fa fa-file-text-o"></i> <strong>@lang('hr::lang.job_title')</strong>
                        </a>
                    </li>
                    <li class="">
                        <a href="#job_category" class="job_category" data-toggle="tab">
                            <i class="fa fa-file-text-o"></i> <strong>@lang('hr::lang.job_category')</strong>
                        </a>
                    </li>
                    <li class="">
                        <a href="#working_days" class="working_days" data-toggle="tab">
                            <i class="fa fa-file-text-o"></i> <strong>@lang('hr::lang.working_days')</strong>
                        </a>
                    </li>
                    <li class="">
                        <a href="#work_shift" class="work_shift" data-toggle="tab">
                            <i class="fa fa-file-text-o"></i> <strong>@lang('hr::lang.work_shift')</strong>
                        </a>
                    </li>
                    <li class="">
                        <a href="#holidays" class="holidays" data-toggle="tab">
                            <i class="fa fa-file-text-o"></i> <strong>@lang('hr::lang.holidays')</strong>
                        </a>
                    </li>
                    <li class="">
                        <a href="#leave_application_type" class="leave_application_type" data-toggle="tab">
                            <i class="fa fa-file-text-o"></i> <strong>@lang('hr::lang.leave_application_type')</strong>
                        </a>
                    </li>
                    <li class="">
                        <a href="#salary_grade" class="salary_grade" data-toggle="tab">
                            <i class="fa fa-file-text-o"></i> <strong>@lang('hr::lang.salary_grade')</strong>
                        </a>
                    </li>
                    <li class="">
                        <a href="#employment_status" class="employment_status" data-toggle="tab">
                            <i class="fa fa-file-text-o"></i> <strong>@lang('hr::lang.employment_status')</strong>
                        </a>
                    </li>
                    <li class="">
                        <a href="#prefix" class="prefix" data-toggle="tab">
                            <i class="fa fa-file-text-o"></i> <strong>@lang('hr::lang.prefix')</strong>
                        </a>
                    </li>
                    <li class="">
                        <a href="#tax" class="tax" data-toggle="tab">
                            <i class="fa fa-file-text-o"></i> <strong>@lang('hr::lang.tax')</strong>
                        </a>
                    </li>
                    <li class="">
                        <a href="#religion" class="religion" data-toggle="tab">
                            <i class="fa fa-file-text-o"></i> <strong>@lang('hr::lang.religion')</strong>
                        </a>
                    </li>

                </ul>
            </div>
        </div>
    </div>
    <div class="tab-content">
        <div class="tab-pane active" id="department">
            @include('superadmin::superadmin_settings.hr_settings.department.index')
        </div>
        <div class="tab-pane" id="job_title">
            @include('superadmin::superadmin_settings.hr_settings.job_title.index')
        </div>
        <div class="tab-pane" id="job_category">
            @include('superadmin::superadmin_settings.hr_settings.job_category.index')
        </div>
        <div class="tab-pane" id="working_days">
            @include('superadmin::superadmin_settings.hr_settings.working_day.index')
        </div>
        <div class="tab-pane" id="work_shift">
            @include('superadmin::superadmin_settings.hr_settings.work_shift.index')
        </div>
        <div class="tab-pane" id="holidays">
            @include('superadmin::superadmin_settings.hr_settings.holiday.index')
        </div>
        <div class="tab-pane" id="leave_application_type">
            @include('superadmin::superadmin_settings.hr_settings.leave_application_type.index')
        </div>
        <div class="tab-pane" id="salary_grade">
            @include('superadmin::superadmin_settings.hr_settings.salary_grade.index')
        </div>
        <div class="tab-pane" id="employment_status">
            @include('superadmin::superadmin_settings.hr_settings.employment_status.index')
        </div>
        <div class="tab-pane" id="prefix">
            @include('superadmin::superadmin_settings.hr_settings.prefix.index')
        </div>
        <div class="tab-pane" id="tax">
            @include('superadmin::superadmin_settings.hr_settings.tax.index')
        </div>
        <div class="tab-pane" id="religion">
            @include('superadmin::superadmin_settings.hr_settings.religion.index')
        </div>

    </div>


</div>