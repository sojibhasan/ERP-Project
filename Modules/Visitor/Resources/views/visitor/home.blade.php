@extends('layouts.visitor')
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
                    {!! Form::label('business_id', __('visitor::lang.business') . ':') !!}
                    {!! Form::select('business_id', $businesses, null,
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
            'visitor::lang.all_visits')])

            <div class="row">
                <div class="col-md-12">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered" id="visitors_table" style="width: 100%;">
                            <thead>
                                <tr>
                                    <th>@lang( 'visitor::lang.visited_date' )</th>
                                    <th>@lang( 'visitor::lang.business_name' )</th>
                                    <th>@lang( 'visitor::lang.no_of_accompanied' )</th>
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
                start.format("MM/DD/YYYY") + ' ~ ' + end.format("MM/DD/YYYY")
            );
            $('.nav-tabs li.active').find('a[data-toggle="tab"]').trigger('shown.bs.tab');
        });
    $('#visitor_list_date_range').on('cancel.daterangepicker', function(ev, picker) {
        $('#visitor_list_date_range').html(
            '<i class="fa fa-calendar"></i> ' + LANG.filter_by_date
        );
        $('.nav-tabs li.active').find('a[data-toggle="tab"]').trigger('shown.bs.tab');
    });
    $(document).ready(function() {
    // visitors_table
        visitor_table = $('#visitors_table').DataTable({
            processing: true,
            serverSide: false,
            ajax: {
                url : "{{action('\Modules\Visitor\Http\Controllers\VisitController@home')}}",
                data: function(d){
                    d.business_id = $('#business_id').val();
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
                {data: 'visited_date', name: 'visited_date'},
                {data: 'business_name', name: 'business_name'},
                {data: 'no_of_accompanied', name: 'no_of_accompanied'},
            ],
            "fnDrawCallback": function (oSettings) {
            }
        });

        $('#visitor_list_date_range, #business_id').change(function(){
            visitor_table.ajax.reload();
        })
    })
    $('.select2').select2();

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