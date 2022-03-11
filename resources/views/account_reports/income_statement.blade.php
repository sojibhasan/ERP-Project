@extends('layouts.app')
@section('title', __( 'account.income_statement' ))

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>@lang( 'account.income_statement')
    </h1>
</section>

<!-- Main content -->
<section class="content">
    @component('components.filters', ['title' => __('report.filters')])
    <div class="row no-print">
        <div class="col-sm-12">
            <div class="col-md-3">
                <label for="business_location">@lang('account.business_locations'):</label>
                {!! Form::select('business_location', $business_locations, $selectedID, ['class' => 'form-control select2',
                'placeholder' =>__('lang_v1.all'), 'style' => 'width: 100%', 'id' => 'business_location']) !!}
            </div>
            <div class="col-md-3">
                <label for="date_range">@lang('account.first_statement'):</label>
                <div class="input-group">
                    <span class="input-group-addon">
                        <i class="fa fa-calendar"></i>
                    </span>
                    <input type="text" id="first_statement_date_range" value="" class="form-control" readonly>
                </div>
            </div>
            <div class="col-md-3">
                <label for="date_range">@lang('account.second_statement'):</label>
                <div class="input-group">
                    <span class="input-group-addon">
                        <i class="fa fa-calendar"></i>
                    </span>
                    <input type="text" id="second_statement_date_range" value="" class="form-control" readonly>
                </div>
            </div>
            <div class="col-md-3">
                <label for="date_range">@lang('account.third_statement'):</label>
                <div class="input-group">
                    <span class="input-group-addon">
                        <i class="fa fa-calendar"></i>
                    </span>
                    <input type="text" id="third_statement_date_range" value="" class="form-control" readonly>
                </div>
            </div>
        </div>
    </div>
    @endcomponent
    <br>
    <div class="box box-solid">
        <div class="box-header">
            <h2 class="box-title">@lang( 'account.income_statement')</h2>
        </div>
        <div class="box-body" style="width: 100%;">
            <div class="" id="income_statement_sections">

            </div>
        </div>
    </div>

</section>
<!-- /.content -->
@stop
@section('javascript')

<script type="text/javascript">
    $('.select2').select2();
    if ($('#first_statement_date_range').length == 1) {
        $('#first_statement_date_range').daterangepicker(dateRangeSettings, function(start, end) {
            $('#first_statement_date_range').val(
                start.format(moment_date_format) + ' - ' + end.format(moment_date_format)
            );
        });
    
        $('#first_statement_date_range')
            .data('daterangepicker')
            .setStartDate(moment().startOf('month'));
        $('#first_statement_date_range')
            .data('daterangepicker')
            .setEndDate(moment().endOf('month'));
    }
    if ($('#second_statement_date_range').length == 1) {
        $('#second_statement_date_range').daterangepicker(dateRangeSettings, function(start, end) {
            $('#second_statement_date_range').val(
                start.format(moment_date_format) + ' - ' + end.format(moment_date_format)
            );
        });
     
        $('#second_statement_date_range')
            .data('daterangepicker')
            .setStartDate(moment().subtract(1, 'years').startOf('month'));
        $('#second_statement_date_range')
            .data('daterangepicker')
            .setEndDate(moment().subtract(1, 'years').endOf('month'));
    }
    if ($('#third_statement_date_range').length == 1) {
        $('#third_statement_date_range').daterangepicker(dateRangeSettings, function(start, end) {
            $('#third_statement_date_range').val(
                start.format(moment_date_format) + ' - ' + end.format(moment_date_format)
            );
        });
     
        $('#third_statement_date_range')
            .data('daterangepicker')
            .setStartDate(moment().subtract(2, 'years').startOf('month'));
        $('#third_statement_date_range')
            .data('daterangepicker')
            .setEndDate(moment().subtract(3, 'years').endOf('month'));
    }


    $(document).ready(function(){
        getIncomeStatement();
        
        $('#business_location, #first_statement_date_range, #second_statement_date_range, #third_statement_date_range').change(function(){
            getIncomeStatement();
        });

    })

    function getIncomeStatement(){
        var location_id = $('select#business_location').val();
        var first_statement_start_date = $('input#first_statement_date_range')
            .data('daterangepicker')
            .startDate.format('YYYY-MM-DD');
        var first_statement_end_date = $('input#first_statement_date_range')
            .data('daterangepicker')
            .endDate.format('YYYY-MM-DD');
        var second_statement_start_date = $('input#second_statement_date_range')
            .data('daterangepicker')
            .startDate.format('YYYY-MM-DD');
        var second_statement_end_date = $('input#second_statement_date_range')
            .data('daterangepicker')
            .endDate.format('YYYY-MM-DD');
        var third_statement_start_date = $('input#third_statement_date_range')
            .data('daterangepicker')
            .startDate.format('YYYY-MM-DD');
        var third_statement_end_date = $('input#third_statement_date_range')
            .data('daterangepicker')
            .endDate.format('YYYY-MM-DD');

            $.ajax({
                method: 'get',
                url: '/accounting-module/income-statement',
                data: { 
                    location_id,
                    first_statement_start_date,
                    first_statement_end_date,
                    second_statement_start_date,
                    second_statement_end_date,
                    third_statement_start_date,
                    third_statement_end_date,
                },
                 contentType : 'html',
                success: function(result) {
                    $('#income_statement_sections').empty().append(result);
                },
            });
    }

</script>

@endsection