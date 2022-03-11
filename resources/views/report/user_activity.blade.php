@extends('layouts.app')
@section('title', __('lang_v1.user_activity'))

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>{{ __('lang_v1.user_activity')}}</h1>
</section>

<!-- Main content -->
<section class="content">
    <div class="row">
        <div class="col-md-12">
            @component('components.filters', ['title' => __('report.filters')])
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('user', __('report.users') . ':') !!}
                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="fa fa-user"></i>
                        </span>
                        {!! Form::select('user', $users, null, ['id' => 'users' ,'class' => 'form-control select2', 'placeholder' => __('lang_v1.all')]); !!}
                    </div>
                </div>
            </div>
            @endcomponent
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            @component('components.widget', ['class' => 'box-primary'])
                <div class="table-responsive">
                    <table class="table table-bordered table-striped" 
                    id="user_activity_report_table">
                        <thead>
                            <tr>
                                <th>@lang('report.date_time')</th>
                                <th>@lang('report.username')</th>
                                <th>@lang('report.activity_subject')</th>
                                <th>@lang('report.subject_id')</th>
                                <th>@lang('report.activity_type')</th>
                            </tr>
                        </thead>
                        <tfoot>
                         
                        </tfoot>
                    </table>
                </div>
            @endcomponent
        </div>
    </div>
</section>
<!-- /.content -->
<div class="modal fade view_register" tabindex="-1" role="dialog" 
    aria-labelledby="gridSystemModalLabel">
</div>


@endsection

@section('javascript')

<script>
//User Activity report
user_activity_report_table = $('#user_activity_report_table').DataTable({
    processing: true,
    serverSide: true,
    ajax: {
        url: '{{action("ReportController@getUserActivityReport")}}',
        data: function (d) {
            var user = $('#users').val();
            d.user = user;
        },
    },
    columns: [
        { data: 'created_at', name: 'created_at' },
        { data: 'causer_id', name: 'causer_id' },
        { data: 'log_name', name: 'log_name' },
        { data: 'subject_id', name: 'subject_id' },
        { data: 'description', name: 'description' }
    ],

    fnDrawCallback: function (oSettings) {

    },

});
$('#users').change(function () {
    user_activity_report_table.ajax.reload();

});

</script>
@endsection