@extends('layouts.app')
@section('title', __('hr::lang.application_list'))

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>@lang('hr::lang.application_list')</h1>
</section>

<!-- Main content -->
<section class="content">
<style>
    .eye_modal{
        text-decoration: none;
        color: gray;
    }
</style>
    <div class="row">
        <div class="col-md-12">
            @component('components.filters', ['title' => __('report.filters')])
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('location_id', __('purchase.business_location') . ':') !!}
                    {!! Form::select('location_id', $business_locations, null, ['id' => 'location_id', 'class' =>
                    'form-control select2', 'style' => 'width:100%']); !!}
                </div>
            </div>
            {{-- <div class="col-md-3">
                    <div class="form-group">
                        {!! Form::label('expense_date_range', __('report.date_range') . ':') !!}
                        {!! Form::text('date_range', @format_date('first day of this month') . ' ~ ' . @format_date('last day of this month') , ['placeholder' => __('lang_v1.select_a_date_range'), 'class' => 'form-control', 'id' => 'expense_date_range', 'readonly']); !!}
                    </div>
                </div> --}}
            @endcomponent
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <!-- general form elements -->
            <div class="box box-primary">
                <div class="box-header with-border bg-primary-dark">
                    <h3 class="box-title">@lang('hr::lang.application_list')</h3>
                </div>
                <!-- /.box-header -->
                <!-- form start -->
                <div class="box-body">
                    <div class="row">


                        <div class="col-sm-12">
                            <table id="leave_appliaction_table" class="table table-bordered table-striped datatable-buttons">
                                <thead>
                                    <!-- Table head -->
                                    <tr>
                                        <th class="active">@lang('hr::lang.employee_id')</th>
                                        <th class="active">@lang('hr::lang.employee_name')</th>
                                        <th class="active">@lang('hr::lang.start_date')</th>
                                        <th class="active">@lang('hr::lang.end_date')</th>
                                        <th class="active">@lang('hr::lang.leave_type')</th>
                                        <th class="active">@lang('hr::lang.application_date')</th>
                                        <th class="active">@lang('hr::lang.status')</th>
                                        <th class="active">@lang('hr::lang.actions')</th>


                                    </tr>
                                </thead><!-- / Table head -->
                               
                            </table> <!-- / Table -->
                        </div>
                    </div>
                </div>
                <!-- /.box -->
            </div>
        </div>
    </div>
<div class="modal fade application_modal" tabindex="-1" role="dialog" 
    aria-labelledby="gridSystemModalLabel">
</div>
</section>
@endsection

@section('javascript')
<script>
    $('#location_id').change(function () {
        leave_appliaction_table.ajax.reload();
    });
    //employee list
    leave_appliaction_table = $('#leave_appliaction_table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '{{action("\Modules\HR\Http\Controllers\ApplicationController@index")}}',
            data: function (d) {
                d.location_id = $('#location_id').val();
                // d.contact_type = $('#contact_type').val();
            }
        },
        columns: [
            { data: 'employee_id', name: 'employee_id' },
            { data: 'name', name: 'name' },
            { data: 'start_date', name: 'start_date' },
            { data: 'end_date', name: 'end_date' },
            { data: 'type_name', name: 'type_name' },
            { data: 'application_date', name: 'application_date' },
            { data: 'status', name: 'status' },
            { data: 'action', name: 'action' },
        ],
        fnDrawCallback: function (oSettings) {
          
        },
    });

    $(document).on('click', 'a.delete_employee', function(e) {
        e.preventDefault();
        swal({
            title: LANG.sure,
            text: 'This application will be deleted.',
            icon: 'warning',
            buttons: true,
            dangerMode: true,
        }).then(willDelete => {
            if (willDelete) {
                var href = $(this).data('href');
                var data = $(this).serialize();

                $.ajax({
                    method: 'DELETE',
                    url: href,
                    dataType: 'json',
                    data: data,
                    success: function(result) {
                        if (result.success === true) {
                            toastr.success(result.msg);
                            leave_appliaction_table.ajax.reload();
                        } else {
                            toastr.error(result.msg);
                        }
                    },
                });
            }
        });
    });
    $('#filter_business').select2();
</script>
@endsection