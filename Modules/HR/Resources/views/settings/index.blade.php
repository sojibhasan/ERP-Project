@extends('layouts.app')
@section('title', __('business.settings'))

@section('content')
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>@lang('business.settings')</h1>
    <br>
    @include('layouts.partials.search_settings')
</section>
<link rel="stylesheet" href="{{asset('css/editor.css')}}">
<style>
    .select2-results__option[aria-selected="true"] {
        display: none;
    }

    .equal-column {
        min-height: 95px;
    }
</style>
<!-- Main content -->
<section class="content">
    <div class="row">
        <div class="col-xs-12">
            <!--  <pos-tab-container> -->
            <div class="col-xs-12 pos-tab-container">
                <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2 pos-tab-menu">
                    <div class="list-group">
                        @if($permissions['hr_module'])
                        @if($permissions['department'])
                        <a href="#" class="list-group-item text-center @if(empty(session('status.tab'))) active @endif">@lang('hr::lang.department')</a>
                        @endif
                        @if($permissions['jobtitle'])
                        <a href="#" class="list-group-item text-center @if(session('status.tab') == 'job_title') active @endif">@lang('hr::lang.job_title')</a>
                        @endif
                        @if($permissions['jobcategory'])
                        <a href="#" class="list-group-item text-center @if(session('status.tab') == 'job_category') active @endif">@lang('hr::lang.job_category')</a>
                        @endif
                        @if($permissions['workingdays'])
                        <a href="#" class="list-group-item text-center @if(session('status.tab') == 'working_days') active @endif">@lang('hr::lang.working_days')</a>
                        @endif
                        @endif
                        @if($permissions['workshift'])
                        <a href="#" class="list-group-item text-center @if(session('status.tab') == 'working_shift') active @endif">@lang('hr::lang.work_shift')</a>
                        @endif
                        @if($permissions['hr_module'])
                        @if($permissions['holidays'])
                        <a href="#" class="list-group-item text-center @if(session('status.tab') == 'holidays') active @endif">@lang('hr::lang.holidays')</a>
                        @endif
                        @if($permissions['leave_type'])
                        <a href="#" class="list-group-item text-center @if(session('status.tab') == 'leave_application_type') active @endif">@lang('hr::lang.leave_application_type')</a>
                        @endif
                        @if($permissions['salary_grade'])
                        <a href="#" class="list-group-item text-center @if(session('status.tab') == 'salary_grade') active @endif">@lang('hr::lang.salary_grade')</a>
                        @endif
                        @if($permissions['employment_status'])
                        <a href="#" class="list-group-item text-center @if(session('status.tab') == 'employment_status') active @endif">@lang('hr::lang.employment_status')</a>
                        @endif
                        @if($permissions['salary_component'])
                        <a href="#" class="list-group-item text-center @if(session('status.tab') == 'salary_component') active @endif">@lang('hr::lang.salary_component')</a>
                        @endif
                        @if($permissions['hr_prefix'])
                        <a href="#" class="list-group-item text-center @if(session('status.tab') == 'prefix') active @endif">@lang('hr::lang.prefix')</a>
                        @endif
                        @if($permissions['hr_tax'])
                        <a href="#" class="list-group-item text-center @if(session('status.tab') == 'tax') active @endif">@lang('hr::lang.tax')</a>
                        @endif
                        @if($permissions['religion'])
                        <a href="#" class="list-group-item text-center @if(session('status.tab') == 'religion') active @endif">@lang('hr::lang.religion')</a>
                        @endif
                        @if($permissions['hr_setting_page'])
                        <a href="#" class="list-group-item text-center @if(session('status.tab') == 'others') active @endif">@lang('hr::lang.others')</a>
                        @endif
                        @endif
                    </div>
                </div>
                <div class="col-lg-10 col-md-10 col-sm-10 col-xs-10 pos-tab">
                    
                    @if($permissions['hr_module'])
                    @if($permissions['department'])
                    @include('hr::settings.department.index')
                    @endif
                    @if($permissions['jobtitle'])
                    @include('hr::settings.job_title.index')
                    @endif
                    @if($permissions['jobcategory'])
                    @include('hr::settings.job_category.index')
                    @endif
                    @if($permissions['workingdays'])
                    @include('hr::settings.working_day.index')
                    @endif
                    @endif
                    @if($permissions['workshift'])
                    @include('hr::settings.work_shift.index')
                    @endif
                    @if($permissions['hr_module'])
                    @if($permissions['holidays'])
                    @include('hr::settings.holiday.index')
                    @endif
                    @if($permissions['leave_type'])
                    @include('hr::settings.leave_application_type.index')
                    @endif
                    @if($permissions['salary_grade'])
                    @include('hr::settings.salary_grade.index')
                    @endif
                    @if($permissions['employment_status'])
                    @include('hr::settings.employment_status.index')
                    @endif
                    @if($permissions['salary_component'])
                    @include('hr::settings.salary_component.index')
                    @endif
                    @if($permissions['hr_prefix'])
                    @include('hr::settings.prefix.index')
                    @endif
                    @if($permissions['hr_tax'])
                    @include('hr::settings.tax.index')
                    @endif
                    @if($permissions['religion'])
                    @include('hr::settings.religion.index')
                    @endif
                    @if($permissions['hr_setting_page'])
                    @include('hr::settings.others.index')
                    @endif
                    @endif

                </div>
            </div>
        </div>
    </div>
</section>


<!-- Modal -->
<div id="department_modal" class="modal" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">@lang('hr::lang.add_department')</h4>
            </div>
            {!! Form::open(['url' => action('\Modules\HR\Http\Controllers\DepartmentController@store'), 'method' =>
            'post']) !!}
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="department">@lang('hr::lang.department'):</label>
                            <input type="text" class="form-control" name="department">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="description">@lang('hr::lang.description'):</label>
                            <textarea type="text" class="form-control" name="description"> </textarea>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary">Save</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
            {!! Form::close() !!}
        </div>

    </div>
</div>

<div class="modal fade department_edit_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
</div>


<!-- Modal -->
<div id="job_title_modal" class="modal fade" role="dialog">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">@lang('hr::lang.add_job_title')</h4>
            </div>
            {!! Form::open(['url' => action('\Modules\HR\Http\Controllers\JobtitleController@store'), 'method' =>
            'post']) !!}
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="job_title">@lang('hr::lang.job_title'):</label>
                            <input type="text" class="form-control" name="job_title">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="description">@lang('hr::lang.description'):</label>
                            <textarea type="text" class="form-control" name="description"> </textarea>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary">Save</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
            {!! Form::close() !!}
        </div>

    </div>
</div>

<div class="modal fade jobtitle_edit_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
</div>


<!-- Modal -->
<div id="job_category_modal" class="modal fade" role="dialog">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">@lang('hr::lang.add_job_category')</h4>
            </div>
            {!! Form::open(['url' => action('\Modules\HR\Http\Controllers\JobCategoryController@store'), 'method' =>
            'post']) !!}
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="job_title">@lang('hr::lang.job_category'):</label>
                            <input type="text" class="form-control" name="category_name">
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary">Save</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
            {!! Form::close() !!}
        </div>

    </div>
</div>

<div class="modal fade jobcategory_edit_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
</div>

<!-- Modal -->
<div id="working_shift_modal" class="modal fade" role="dialog">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">@lang('hr::lang.add_workshift')</h4>
            </div>
            {!! Form::open(['url' => action('\Modules\HR\Http\Controllers\WorkShiftController@store'), 'method' => 'post']) !!}
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="shift_name">@lang('hr::lang.shift_name'):</label>
                            <input type="text" class="form-control" name="shift_name">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="shift_form">@lang('hr::lang.shift_form'):</label>
                            <input type="text" id="shif_from" class="form-control" name="shift_form">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="shift_to">@lang('hr::lang.shift_to'):</label>
                            <input type="text" id="shif_to" class="form-control" name="shift_to">
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary">Save</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
            {!! Form::close() !!}
        </div>

    </div>
</div>

<div class="modal fade workshift_edit_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
</div>
<div class="modal fade holiday_model" role="dialog" aria-labelledby="gridSystemModalLabel">
</div>
<div class="modal fade leave_application_type_model" role="dialog" aria-labelledby="gridSystemModalLabel">
</div>
<div class="modal fade salary_grade_model" role="dialog" aria-labelledby="gridSystemModalLabel">
</div>
<div class="modal fade employment_status_model" role="dialog" aria-labelledby="gridSystemModalLabel">
</div>
<div class="modal fade salary_component_model" role="dialog" aria-labelledby="gridSystemModalLabel">
</div>
<div class="modal fade tax_model" role="dialog" aria-labelledby="gridSystemModalLabel">
</div>
<div class="modal fade religion_model" role="dialog" aria-labelledby="gridSystemModalLabel">
</div>

@stop
@section('javascript')
<script src="{{url('Modules/HR/Resources/assets/js/app.js')}}"></script>

@endsection