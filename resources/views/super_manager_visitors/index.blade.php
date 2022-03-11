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
                    {!! Form::label('sp_visitor_filter_date_range', __('report.date_range') . ':') !!}
                    {!! Form::text('sp_visitor_filter_date_range', null, ['placeholder' => __('lang_v1.select_a_date_range'), 'class' => 'form-control', 'readonly']); !!}
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('business_id', __('business.business') . ':*') !!}
                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="fa fa-user"></i>
                        </span>
                        {!! Form::select('business_id', $businesses, null,
                        ['class' => 'form-control select2','placeholder' => __('lang_v1.all'), 'style' => 'margin:0px',
                        'required']); !!}
                    </div>
                </div>
            </div>
            {{-- <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('location_id', __('lang_v1.business_location') . ':*') !!}
                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="fa fa-marker"></i>
                        </span>
                        {!! Form::select('location_id', [], null,
                        ['class' => 'form-control select2','placeholder' => __('lang_v1.all'), 'style' => 'margin:0px',
                        'required']); !!}
                    </div>
                </div>
            </div> --}}
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('town', __('business.town') . ':*') !!}
                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="fa fa-user"></i>
                        </span>
                        {!! Form::select('town', $towns, null,
                        ['class' => 'form-control select2','placeholder' => __('lang_v1.all'), 'style' => 'margin:0px',
                        'required']); !!}
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('district', __('business.district') . ':*') !!}
                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="fa fa-user"></i>
                        </span>
                        {!! Form::select('district', $districts, null,
                        ['class' => 'form-control select2','placeholder' => __('lang_v1.all'), 'style' => 'margin:0px',
                        'required']); !!}
                    </div>
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
                    data-href="{{action('SuperManagerVisitorController@create')}}"
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
                                    <th>@lang( 'lang_v1.business_name' )</th>
                                    <th>@lang( 'lang_v1.date_and_time' )</th>
                                    <th>@lang( 'visitor::lang.visited_date' )</th>
                                    <th>@lang( 'lang_v1.business_location' )</th>
                                    <th>@lang( 'visitor::lang.mobile_number' )</th>
                                    <th>@lang( 'visitor::lang.land_number' )</th>
                                    <th>@lang( 'visitor::lang.name' )</th>
                                    <th>@lang( 'visitor::lang.address' )</th>
                                    <th>@lang( 'visitor::lang.district' )</th>
                                    <th>@lang( 'visitor::lang.town' )</th>
                                    <th>@lang( 'lang_v1.no_of_accompanies' )</th>
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

    $('#sp_visitor_filter_date_range').daterangepicker(dateRangeSettings, function(start, end) {
        $('#sp_visitor_filter_date_range span').html(
            start.format(moment_date_format) + ' ~ ' + end.format(moment_date_format)
        );
        $('.nav-tabs li.active').find('a[data-toggle="tab"]').trigger('shown.bs.tab');
    });
    $('#sp_visitor_filter_date_range').on('cancel.daterangepicker', function(ev, picker) {
        $('#sp_visitor_filter_date_range').html(
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
                url : "{{action('SuperManagerVisitorController@index')}}",
                data: function(d){
                    d.username = $('#username').val();
                    d.town = $('#town').val();
                    d.district = $('#district').val();
                    d.business_id = $('#business_id').val();
                    d.start_date = $('#sp_visitor_filter_date_range')
                        .data('daterangepicker')
                        .startDate.format('YYYY-MM-DD');
                    d.end_date = $('#sp_visitor_filter_date_range')
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
                {data: 'business_name', name: 'business_name'},
                {data: 'date_and_time', name: 'date_and_time'},
                {data: 'visited_date', name: 'visited_date'},
                {data: 'district', name: 'district'},
                {data: 'mobile_number', name: 'mobile_number'},
                {data: 'land_number', name: 'land_number'},
                {data: 'name', name: 'name'},
                {data: 'address', name: 'address'},
                {data: 'district', name: 'district'},
                {data: 'town', name: 'town'},
                {data: 'details', name: 'details'},
            ],
            "fnDrawCallback": function (oSettings) {
            }
        });

        $('#username, #town, #district, #business_id, #sp_visitor_filter_date_range').change(function(){
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