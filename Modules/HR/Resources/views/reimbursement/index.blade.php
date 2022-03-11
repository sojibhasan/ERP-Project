@extends('layouts.app')
@section('title', __('hr.reimbursement'))

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>@lang('hr::lang.reimbursement')</h1>
</section>

<!-- Main content -->
<section class="content">

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
            @endcomponent
        </div>
    </div>

    <div class="row">
        <div class="col-sm-12">

            <div class="row">
                <div class="col-sm-12" data-offset="0">

                    <div class="wrap-fpanel">
                        <div class="box box-primary" data-collapsed="0">
                            <div class="box-header with-border bg-primary-dark">
                                <h3 class="box-title">@lang('hr::lang.reimbursement')</h3>
                            </div>
                            <div class="panel-body">
                                {{-- <button type="button" class="btn btn-success pull-right" data-toggle="modal" data-target="#myModal">@lang('hr::lang.add_reiembursment')</button> --}}
                                <button class="btn-modal eye_modal btn btn-success pull-right" 
                                data-href="{{action('\Modules\HR\Http\Controllers\ReimbursementController@create')}}" 
                                data-container=".reimbursement_add_modal">
                                <i class="fa fa-plus" style="color:#fff;"></i> @lang('hr::lang.add_reiembursment') </button>
                            <br>
                            <br>
                                <table class="table table-bordered" id="table_reimbursement">
                                    <thead>
                                        <th>@lang('hr::lang.date')</th>
                                        <th>@lang('hr::lang.employee')</th>
                                        <th>@lang('hr::lang.department')</th>
                                        <th>@lang('hr::lang.amount')</th>
                                        <th>@lang('hr::lang.description')</th>
                                        <th>@lang('hr::lang.approved_by_manager')</th>
                                        {{-- <th>@lang('hr::lang.manager_comment')</th> --}}
                                        <th>@lang('hr::lang.approved_by_admin')</th>
                                        {{-- <th>@lang('hr::lang.admin_comment')</th> --}}
                                        <th>@lang('hr::lang.action')</th>
                                    </thead>

                                </table>



                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
</section>
<div class="modal fade reimbursement_view_modal" tabindex="-1" role="dialog" 
    aria-labelledby="gridSystemModalLabel">
</div>
<div class="modal fade reimbursement_edit_modal" tabindex="-1" role="dialog" 
    aria-labelledby="gridSystemModalLabel">
</div>

<div class="modal fade reimbursement_add_modal" tabindex="-1" role="dialog" 
    aria-labelledby="gridSystemModalLabel">
</div>


@endsection

@section('javascript')
<script>
    $('#location_id').change(function () {
        table_reimbursement.ajax.reload();
    });
    //employee list
    table_reimbursement = $('#table_reimbursement').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '{{action("\Modules\HR\Http\Controllers\ReimbursementController@index")}}',
            data: function (d) {
                d.location_id = $('#location_id').val();
            }
        },
        columns: [
            { data: 'date', name: 'date' },
            { data: 'name', name: 'name' },
            { data: 'department', name: 'department' },
            { data: 'amount', name: 'amount' },
            { data: 'desc', name: 'desc' },
            { data: 'approved_manager', name: 'approved_manager' },
            { data: 'approved_admin', name: 'approved_admin' },
            { data: 'action', name: 'action' },
        ],
        fnDrawCallback: function (oSettings) {
          
        },
    });

    $(document).on('click', 'a.delete_reimbursement', function(e) {
        e.preventDefault();
        swal({
            title: LANG.sure,
            text: 'This employee will be deleted.',
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
                            table_reimbursement.ajax.reload();
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