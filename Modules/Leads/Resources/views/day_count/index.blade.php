@extends('layouts.app')

@section('title', __('leads::lang.day_count'))

@section('content')
<!-- Main content -->
<section class="content">
    <div class="row">
        <div class="col-md-12">
            @component('components.filters', ['title' => __('report.filters')])
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('date_range_filter', __('report.date_range') . ':') !!}
                    {!! Form::text('date_range_filter', @format_date('first day of this month') . ' ~ ' .
                    @format_date('last
                    day of this month') , ['placeholder' => __('lang_v1.select_a_date_range'), 'class' =>
                    'form-control date_range', 'id' => 'date_range_filter', 'readonly']); !!}
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('users_fitler', __( 'leads::lang.user' )) !!}
                    {!! Form::select('users_fitler', $users, null, ['class' => 'form-control select2',
                    'required',
                    'placeholder' => __(
                    'leads::lang.please_select' ), 'id' => 'users_fitler']);
                    !!}
                </div>
            </div>
            @endcomponent
        </div>
    </div>
    @component('components.widget', ['title' => __('leads::lang.day_count')])
    <div class="row">
        <div class="col-md-12">
            <div class="table-responsive">
                <table class="table table-striped table-bordered" id="day_count_table" style="width: 100%;">
                    <thead>
                        <tr>
                            <th>@lang( 'leads::lang.user' )</th>
                            <th>@lang( 'leads::lang.date' )</th>
                            <th>@lang( 'leads::lang.day_count' )</th>
                        </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endcomponent
    <div class="modal fade leads_model" role="dialog" aria-labelledby="gridSystemModalLabel">
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
    $('#date_range_filter,  #users_fitler').change(function(){
        day_count_table.ajax.reload();
    })

    // day_count_table
    day_count_table = $('#day_count_table').DataTable({
            processing: true,
            serverSide: false,
            ajax: {
                url : "{{action('\Modules\Leads\Http\Controllers\DayCountController@index')}}",
                data: function(d){
                    d.start_date = $('#date_range_filter')
                        .data('daterangepicker')
                        .startDate.format('YYYY-MM-DD');
                    d.end_date = $('#date_range_filter')
                        .data('daterangepicker')
                        .endDate.format('YYYY-MM-DD');
                    d.created_by = $('#created_by_fitler').val();
                   
                }
            },
            columns: [
                {data: 'user', name: 'user'},
                {data: 'date', name: 'date'},
                {data: 'day_count', name: 'day_count'},
            ]
        });


</script>
@endsection