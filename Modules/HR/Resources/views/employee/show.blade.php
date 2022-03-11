@extends('layouts.app')
@section('title', __('hr.employee_details'))

@section('content')
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>@lang('hr::lang.employee_details')</h1>
    <br>
    {{-- @include('layouts.partials.search_settings') --}}
</section>
<link rel="stylesheet" href="{{asset('css/editor.css')}}">
<!-- Main content -->
<section class="content">
    {{-- {!! Form::open(['url' => action('\Modules\HR\Http\Controllers\EmployeeController@update', [$employee->id]), 'method'
    => 'put', 'id' => 'employee_edit_form',
    'files' => true ]) !!} --}}
    <div class="row">
        <div class="col-md-12">
            <div class="nav-tabs-custom">
                <ul class="nav nav-tabs nav-justified">
                    <li class="
                        @if(!empty($view_type) &&  $view_type == 'view')
                            active
                        @else
                            ''
                        @endif">
                        <a href="#view_tab" data-toggle="tab" aria-expanded="true"><i class="fa fa-user"
                                aria-hidden="true"></i> @lang( 'hr::lang.view')</a>
                    </li>
                    <li class="
                            @if(!empty($view_type) &&  $view_type == 'loans')
                                active
                            @else
                                ''
                            @endif">
                        <a href="#loans_tab" data-toggle="tab" aria-expanded="true"><i class="fa fa-anchor"
                                aria-hidden="true"></i> @lang('hr::lang.loans')</a>
                    </li>

                    <li class="
                            @if(!empty($view_type) &&  $view_type == 'advances')
                                active
                            @else
                                ''
                            @endif">
                        <a href="#advances_tab" data-toggle="tab" aria-expanded="true"><i
                                class="fa fa-arrow-circle-down" aria-hidden="true"></i> @lang(
                            'hr::lang.advances')</a>
                    </li>
                    <li class="
                            @if(!empty($view_type) &&  $view_type == 'salaries')
                                active
                            @else
                                ''
                            @endif">
                        <a href="#salaries_tab" data-toggle="tab" aria-expanded="true"><i
                                class="fa fa-arrow-circle-down" aria-hidden="true"></i> @lang(
                            'hr::lang.salaries')</a>
                    </li>

                </ul>

                <div class="tab-content" style="background: #fbfcfc;">
                    <div class="tab-pane
                            @if(!empty($view_type) &&  $view_type == 'view')
                                active
                            @else
                                ''
                            @endif" id="view_tab">
                        @include('hr::employee.partials.view_tab')
                    </div>
                    <div class="tab-pane
                                @if(!empty($view_type) &&  $view_type == 'loans')
                                    active
                                @else
                                    ''
                                @endif" id="loans_tab">
                        <div class="row">
                            <div class="col-md-12">
                                @include('hr::employee.partials.loans_tab')
                            </div>
                        </div>
                    </div>

                    <div class="tab-pane
                            @if(!empty($view_type) &&  $view_type == 'advances')
                                active
                            @else
                                ''
                            @endif" id="advances_tab">
                        <div class="row">
                            <div class="col-md-12">
                                {{-- @component('components.widget', ['class' => 'box']) --}}
                                @include('hr::employee.partials.advances_tab')
                                {{-- @endcomponent --}}
                            </div>
                        </div>
                    </div>

                    <div class="tab-pane
                            @if(!empty($view_type) &&  $view_type == 'salaries')
                                active
                            @else
                                ''
                            @endif" id="salaries_tab">
                        <div class="row">
                            <div class="col-md-12">
                                {{-- @component('components.widget', ['class' => 'box']) --}}
                                @include('hr::employee.partials.salaries_tab')
                                {{-- @endcomponent --}}
                            </div>
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </div>

    <div class="row">
        <div class="col-sm-12">
            <button class="btn btn-danger pull-right settingForm_button"
                type="submit">@lang('hr::lang.update_employee')</button>
        </div>
    </div>
    {{-- {!! Form::close() !!} --}}
</section>
<!-- /.content -->
@stop
@section('javascript')



@endsection