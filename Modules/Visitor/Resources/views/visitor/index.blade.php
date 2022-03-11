@extends('layouts.app')
@section('title', __('visitor::lang.visitors'))

@section('content')
<!-- Main content -->

<section class="content">
    <div class="row">
        <div class="col-md-12">
            @component('components.filters', ['title' => __('report.filters')])
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('visitor_list_date_range', __('report.date_range') . ':') !!}
                    {!! Form::text('visitor_list_date_range', @format_date('first day of this month') . ' ~ ' .
                    @format_date('last
                    day of this month') , ['placeholder' => __('lang_v1.select_a_date_range'), 'class' =>
                    'form-control visitor_list_date_range', 'id' => 'visitor_list_date_range', 'readonly']); !!}
                </div>
            </div>

            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('mobile_number', __('visitor::lang.mobile_number') . ':') !!}
                    {!! Form::select('mobile_number', $mobile_numbers, null,
                    ['class' => 'form-control select2','placeholder' => __('lang_v1.all'), 'style' => 'margin:0px',
                    ]); !!}
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('town', __('business.town') . ':') !!}

                    {!! Form::select('town', $towns, null,
                    ['class' => 'form-control select2','placeholder' => __('lang_v1.all'), 'style' => 'margin:0px',
                    ]); !!}
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('district', __('business.district') . ':') !!}

                    {!! Form::select('district', $districts, null,
                    ['class' => 'form-control select2','placeholder' => __('lang_v1.all'), 'style' => 'margin:0px',
                    ]); !!}
                </div>
            </div>
            @endcomponent
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            @component('components.widget', ['class' => 'box-primary', 'title' => __(
            'visitor::lang.all_visitor')])
            @slot('tool')
            <div class="box-tools">
                <button type="button" class="btn btn-primary btn-modal pull-right" id="add_visitor_btn"
                    data-href="{{action('\Modules\Visitor\Http\Controllers\VisitorController@create')}}"
                    data-container=".visitor_model">
                    <i class="fa fa-plus"></i> @lang( 'visitor::lang.add' )</button>
            </div>
            @endslot

            <div class="row">
                <div class="col-md-12">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered" id="visitors_table" style="width: 100%;">
                            <thead>
                                <tr>
                                    <th>@lang( 'messages.action' )</th>
                                    <th>@lang( 'visitor::lang.name' )</th>
                                    <th>@lang( 'visitor::lang.visitor_code' )</th>
                                    <th>@lang( 'visitor::lang.gender' )</th>
                                    <th>@lang( 'visitor::lang.address' )</th>
                                    <th>@lang( 'visitor::lang.town' )</th>
                                    <th>@lang( 'visitor::lang.district' )</th>
                                    <th>@lang( 'visitor::lang.mobile_number' )</th>
                                    <th>@lang( 'visitor::lang.land_number' )</th>
                                    <th>@lang( 'visitor::lang.visited_date' )</th>
                                    <th>@lang( 'visitor::lang.logged_in_time' )</th>
                                    <th>@lang( 'visitor::lang.logged_out_time' )</th>
                                    <th>@lang( 'visitor::lang.details' )</th>
                                </tr>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endcomponent
        </div>
    </div>
    <div class="modal fade visitor_model" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
    </div>
</section>
<!-- /.content -->


@endsection
@section('javascript')
<script>
    $('#visitor_list_date_range').daterangepicker(dateRangeSettings, function(start, end) {
            $('#visitor_list_date_range span').html(
                start.format(moment_date_format) + ' ~ ' + end.format(moment_date_format)
            );
            $('.nav-tabs li.active').find('a[data-toggle="tab"]').trigger('shown.bs.tab');
        });
        $('#visitor_list_date_range').on('cancel.daterangepicker', function(ev, picker) {
            $('#visitor_list_date_range').html(
                '<i class="fa fa-calendar"></i> ' + LANG.filter_by_date
            );
            $('.nav-tabs li.active').find('a[data-toggle="tab"]').trigger('shown.bs.tab');
        });
    $('.select2').select2();
    // visitors_table
        visitor_table = $('#visitors_table').DataTable({
            processing: true,
            serverSide: false,
            ajax: {
                url : "{{action('\Modules\Visitor\Http\Controllers\VisitorController@index')}}",
                data: function(d){
                    d.username = $('#username').val();
                    d.town = $('#town').val();
                    d.district = $('#district').val();
                    d.mobile_number = $('#mobile_number').val();
                    d.start_date = $('#visitor_list_date_range')
                            .data('daterangepicker')
                            .startDate.format('YYYY-MM-DD');
                        d.end_date = $('#visitor_list_date_range')
                            .data('daterangepicker')
                            .endDate.format('YYYY-MM-DD');
                }
            },
            columnDefs:[{
                    "targets": 1,
                    "orderable": false,
                    "searchable": false
                }],
            columns: [
                {data: 'action', name: 'action'},
                {data: 'name', name: 'name'},
                {data: 'business_name', name: 'business_name'},
                {data: 'gender', name: 'gender'},
                {data: 'address', name: 'address'},
                {data: 'town', name: 'town'},
                {data: 'district', name: 'district'},
                {data: 'mobile_number', name: 'mobile_number'},
                {data: 'land_number', name: 'land_number'},
                {data: 'visited_date', name: 'visited_date'},
                {data: 'logged_in_time', name: 'logged_in_time'},
                {data: 'logged_out_time', name: 'logged_out_time'},
                {data: 'details', name: 'details'},
            ],
            "fnDrawCallback": function (oSettings) {
            }
        });

        $('#username, #town, #district, #visitor_list_date_range, #mobile_number').change(function(){
            visitor_table.ajax.reload();
        })

        $(document).on('click', 'button.delete_visitor', function(){
            swal({
                title: LANG.sure,
                icon: "warning",
                buttons: true,
                dangerMode: true,
            }).then((willDelete)=>{
                if(willDelete){
                    let href = $(this).data('href');

                    $.ajax({
                        method: 'delete',
                        url: href,
                        data: {  },
                        success: function(result) {
                            if(result.success == 1){
                                toastr.success(result.msg);
                            }else{
                                toastr.error(result.msg);
                            }
                            visitor_table.ajax.reload();
                        },
                    });
                }
            });
        });
</script>

@endsection