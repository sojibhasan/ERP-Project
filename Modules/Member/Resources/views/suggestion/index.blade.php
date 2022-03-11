@php
if (Auth::guard('web')->check()) {
$layout = 'app';
}else{
$layout = 'member';
}
@endphp
@extends('layouts.'.$layout)

@section('title', __('member::lang.suggestions'))

@section('content')
<!-- Main content -->
<section class="content">
    <div class="row">
        <div class="col-md-12">
            @component('components.filters', ['title' => __('report.filters')])
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('date_range_filter', __('report.date_range') . ':') !!}
                    {!! Form::text('date_range', @format_date('first day of this month') . ' ~ ' . @format_date('last
                    day of this month') , ['placeholder' => __('lang_v1.select_a_date_range'), 'class' =>
                    'form-control date_range', 'id' => 'date_range_filter', 'readonly']); !!}
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('bala_mandalaya_area', __('member::lang.bala_mandalaya_area') ) !!}
                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="fa fa-user"></i>
                        </span>
                        {!! Form::select('bala_mandalaya_area', $bala_mandalaya_areas, null,
                        ['class' => 'form-control select2','placeholder' => __('lang_v1.all'), 'style' => 'margin:0px',
                        ]); !!}
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('service_area', __('member::lang.main_areas') ) !!}
                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="fa fa-user"></i>
                        </span>
                        {!! Form::select('service_area', $service_areas, null,
                        ['class' => 'form-control select2','placeholder' => __('lang_v1.all'), 'style' => 'margin:0px',
                        ]); !!}
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('name_of_suggestions', __('member::lang.name_of_suggestions') ) !!}
                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="fa fa-user"></i>
                        </span>
                        {!! Form::select('name_of_suggestions', $name_of_suggestions, null,
                        ['class' => 'form-control select2','placeholder' => __('lang_v1.all'), 'style' => 'margin:0px',
                        'required']); !!}
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('details_of_suggestions', __('member::lang.details_of_suggestions') ) !!}
                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="fa fa-user"></i>
                        </span>
                        {!! Form::select('details_of_suggestions', $details_of_suggestions, null,
                        ['class' => 'form-control select2','placeholder' => __('lang_v1.all'), 'style' => 'margin:0px',
                        'required']); !!}
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('is_common_problem', __( 'member::lang.is_common_problem' )) !!}
                    {!! Form::select('is_common_problem', ['yes' => 'Yes', 'no' => 'No'], null, ['class' =>
                    'form-control
                    select2',
                    'required', 'placeholder' => __(
                    'member::lang.please_select' ), 'id' => 'is_common_problem']);
                    !!}
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('area_which_involved', __('member::lang.area_which_involved') ) !!}
                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="fa fa-user"></i>
                        </span>
                        {!! Form::select('area_which_involved', $area_which_involved, null,
                        ['class' => 'form-control select2','placeholder' => __('lang_v1.all'), 'style' => 'margin:0px',
                        'required']); !!}
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('state_of_urgency', __( 'member::lang.state_of_urgency' )) !!}
                    {!! Form::select('state_of_urgency', $state_of_urgencies, null, ['class' => 'form-control select2',
                    'required',
                    'placeholder' => __(
                    'member::lang.please_select' ), 'id' => 'state_of_urgency']);
                    !!}
                </div>
            </div>
            <div class="clearfix"></div>
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('solution_given', __( 'member::lang.solution_given' )) !!}
                    {!! Form::select('solution_given', $solution_givens, null, ['class' => 'form-control select2',
                    'required',
                    'placeholder' => __(
                    'member::lang.please_select' ), 'id' => 'solution_given']);
                    !!}
                </div>
            </div>


            @endcomponent
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            @component('components.widget', ['class' => 'box-primary', 'title' => __(
            'member::lang.all_suggestions')])
            @slot('tool')
            <div class="box-tools">
                <button type="button" class="btn btn-primary btn-modal pull-right" id="add_suggestion_btn"
                    data-href="{{action('\Modules\Member\Http\Controllers\SuggestionController@create')}}"
                    data-container=".suggestion_model">
                    <i class="fa fa-plus"></i> @lang( 'member::lang.add' )</button>
            </div>
            @endslot

            <div class="row">
                <div class="col-md-12">
                    <table class="table table-striped table-bordered" id="suggestion_table" style="width: 100%;">
                        <thead>
                            <tr>
                                <th>@lang( 'messages.action' )</th>
                                <th>@lang( 'member::lang.date' )</th>
                                <th>@lang( 'member::lang.balamandalaya' )</th>
                                <th>@lang( 'member::lang.main_areas' )</th>
                                <th>@lang( 'member::lang.name_of_suggestion' )</th>
                                <th>@lang( 'member::lang.is_this_a_common_problem' )</th>
                                <th>@lang( 'member::lang.area_which_involved' )</th>
                                <th>@lang( 'member::lang.state_of_urgency' )</th>
                                <th>@lang( 'member::lang.created' )</th>
                            </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>
                </div>
            </div>
            @endcomponent
        </div>
    </div>
    <div class="modal fade suggestion_model" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
    </div>
</section>
<!-- /.content -->

@endsection

@section('javascript')
<script>
     if ($('#date_range_filter').length == 1) {
        $('#date_range_filter').daterangepicker(dateRangeSettings, function(start, end) {
            $('#date_range_filter').val(
               start.format(moment_date_format) + ' - ' +  end.format(moment_date_format)
            );
        });
        $('#date_range_filter').on('cancel.daterangepicker', function(ev, picker) {
            $('#product_sr_date_filter').val('');
        });
        $('#date_range_filter')
            .data('daterangepicker')
            .setStartDate(moment().startOf('month'));
        $('#date_range_filter')
            .data('daterangepicker')
            .setEndDate(moment().endOf('month'));
    }
    
    $(document).on('click', '#add_suggestion_btn', function(){
        $('.suggestion_model').modal({
            backdrop: 'static',
            keyboard: false
        })
    })
    $(".suggestion_model").on('hide.bs.modal', function(){
        tinymce.remove('#details');
    });

    $('#date_range_filter, #bala_mandalaya_area, #service_area, #name_of_suggestions, #details_of_suggestions, #is_common_problem, #area_which_involved, #state_of_urgency, #solution_given').change(function(){
        suggestion_table.ajax.reload();
    })

    // suggestion_table
    suggestion_table = $('#suggestion_table').DataTable({
            processing: true,
            serverSide: false,
            ajax: {
                url : "{{action('\Modules\Member\Http\Controllers\SuggestionController@index')}}",
                data: function(d){
                    d.start_date = $('#date_range_filter')
                        .data('daterangepicker')
                        .startDate.format('YYYY-MM-DD');
                    d.end_date = $('#date_range_filter')
                        .data('daterangepicker')
                        .endDate.format('YYYY-MM-DD');
                    d.balamandalaya_id = $('#bala_mandalaya_area').val();
                    d.service_area_id = $('#service_area').val();
                    d.heading = $('#name_of_suggestions').val();
                    d.details = $('#details_of_suggestions').val();
                    d.is_common_problem = $('#is_common_problem').val();
                    d.area_name = $('#area_which_involved').val();
                    d.state_of_urgency = $('#state_of_urgency').val();
                    d.solution_given = $('#solution_given').val();
                }
            },
            columnDefs:[{
                    "targets": 1,
                    "orderable": false,
                    "searchable": false
                }],
            columns: [
                {data: 'action', name: 'action'},
                {data: 'date', name: 'date'},
                {data: 'balamandalaya', name: 'balamandalaya'},
                {data: 'service_area', name: 'service_area'},
                {data: 'heading', name: 'heading'},
                {data: 'is_common_problem', name: 'is_common_problem'},
                {data: 'area_name', name: 'area_name'},
                {data: 'state_of_urgency', name: 'state_of_urgency'},
                {data: 'member_name', name: 'members.name'},
            ],
            "fnDrawCallback": function (oSettings) {
            }
        });

        $(document).on('click', 'button.note_group_delete', function(){
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
                            suggestion_table.ajax.reload();
                        },
                    });
                }
            });
        })
</script>
@endsection