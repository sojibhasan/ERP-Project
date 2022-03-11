@extends('layouts.app')
@section('title', __('mpcs::lang.F20andF14b_form'))

@section('content')
<!-- Main content -->
<section class="content">

    <div class="row">
        <div class="col-md-12">
            <div class="settlement_tabs">
                <ul class="nav nav-tabs">
                    @if(auth()->user()->can('f14b_form'))
                    <li class="active">
                        <a href="#14b_form" class="14b_form" data-toggle="tab">
                            <i class="fa fa-file-text-o"></i> <strong>@lang('mpcs::lang.f14b_form')</strong>
                        </a>
                    </li>
                    @endif
                    @if(auth()->user()->can('f20_form'))
                    <li class="">
                        <a href="#20_form" class="20_form" data-toggle="tab">
                            <i class="fa fa-file-text-o"></i> <strong>@lang('mpcs::lang.f20_form')</strong>
                        </a>
                    </li>
                    @endif
                </ul>
                <div class="tab-content">
                    @if(auth()->user()->can('f14b_form'))
                    <div class="tab-pane active" id="14b_form">
                        <!-- Main content -->
                        <section class="content">
                            @component('components.widget', ['class' => 'box-primary', 'title' => __(
                            'mpcs::lang.f14b_form')])
                            <div class="col-md-3" id="location_filter">
                                <div class="form-group">
                                    {!! Form::label('f14b_location_id', __('purchase.business_location') . ':') !!}
                                    {!! Form::select('f14b_location_id', $business_locations, null, ['class' => 'form-control
                                    select2',
                                    'style' => 'width:100%', 'placeholder' => __('lang_v1.all')]); !!}
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    {!! Form::label('type', __('mpcs::lang.date') . ':') !!}
                                    {!! Form::text('f14b_date', null, ['class' => 'form-control', 'id' => 'f14b_date',
                                    'readonly'])
                                    !!}
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    {!! Form::label('type', __('mpcs::lang.F14b_from_no') . ':') !!}
                                    {!! Form::text('F14b_from_no', $F14_from_no, ['class' => 'form-control',
                                    'readonly']) !!}
                                </div>
                            </div>
                            @endcomponent

                            @component('components.widget', ['class' => 'box-primary', 'title' => __(
                            'mpcs::lang.f14b_form')])
                            <div id="form14B_content"></div>
                            @endcomponent
                        </section>
                        <!-- /.content -->
                        {{-- @include('mpcs::forms.F20andF14b.partials.14b_form') --}}
                    </div>
                    @endif
                    @if(auth()->user()->can('f20_form'))
                    <div class="tab-pane" id="20_form">
                        @include('mpcs::forms.F20andF14b.partials.20_form')
                    </div>
                    @endif

                </div>
            </div>
        </div>
    </div>

</section>
<!-- /.content -->

@endsection
@section('javascript')
<script>
    // form 14b
     $('#f14b_date').daterangepicker();
     if ($('#f14b_date').length == 1) {
         $('#f14b_date').daterangepicker(dateRangeSettings, function(start, end) {
             $('#f14b_date').val(
                 start.format(moment_date_format) + ' - ' + end.format(moment_date_format)
             );
         });
         $('#f14b_date').on('cancel.daterangepicker', function(ev, picker) {
             $('#product_sr_date_filter').val('');
         });
         $('#f14b_date')
             .data('daterangepicker')
             .setStartDate(moment().startOf('month'));
         $('#f14b_date')
             .data('daterangepicker')
             .setEndDate(moment().endOf('month'));
     }
     $(document).ready(function(){
        getForm14b();
        $('#f14b_date, #f14b_location_id').change(function(){
            getForm14b();
        })
     })
     function getForm14b(){
            var start_date = $('input#f14b_date')
                    .data('daterangepicker')
                .startDate.format('YYYY-MM-DD');
            var end_date = $('input#f14b_date')
                .data('daterangepicker')
                .endDate.format('YYYY-MM-DD');
            start_date = start_date;
            end_date = end_date;
            location_id = $('#f14b_location_id').val();

         $.ajax({
             method: 'get',
             url: '/mpcs/get-form-14b',
             data: {
                start_date,
                end_date,
                location_id
            },
            contentType: 'html',
            success: function(result) {
                $('#form14B_content').empty().append(result)
            },
         });
     }
     

     //form 20 
    $('#form_20_date_range').daterangepicker();
    if ($('#form_20_date_range').length == 1) {
        $('#form_20_date_range').daterangepicker(dateRangeSettings, function(start, end) {
            $('#form_20_date_range').val(
                start.format(moment_date_format) + ' - ' + end.format(moment_date_format)
            );
        });
        $('#form_20_date_range').on('cancel.daterangepicker', function(ev, picker) {
            $('#product_sr_date_filter').val('');
        });
        $('#form_20_date_range')
            .data('daterangepicker')
            .setStartDate(moment().startOf('month'));
        $('#form_20_date_range')
            .data('daterangepicker')
            .setEndDate(moment().endOf('month'));
    }

    let date = $('#form_20_date_range').val().split(' - ');

    $('.from_date').text(date[0]);
    $('.to_date').text(date[1]);
    
    $('#f14b_location_id option:eq(1)').attr('selected', true);
    $('#20_location_id option:eq(1)').attr('selected', true);
    $(document).ready(function(){


    //form_20_table 
    form_20_table = $('#form_20_table').DataTable({
    processing: true,
    serverSide: true,
    ajax: {
        url: '/mpcs/get-form-20',
        data: function(d) {
            var start_date = $('input#form_20_date_range')
                .data('daterangepicker')
                .startDate.format('YYYY-MM-DD');
            var end_date = $('input#form_20_date_range')
                .data('daterangepicker')
                .endDate.format('YYYY-MM-DD');
            d.start_date = start_date;
            d.end_date = end_date;
            d.location_id = $('#20_location_id').val();
        }
    },
    columns: [
        { data: 'DT_Row_Index', name: 'DT_Row_Index' , orderable: false, searchable: false},
        { data: 'sku', name: 'products.sku' },
        { data: 'product', name: 'products.name' },
        { data: 'sold_qty', name: 'transaction_sell_lines.quantity' },
        { data: 'unit_price', name: 'transaction_sell_lines.unit_price' },
        { data: 'total_amount', name: 'total_amount' },
    ],
    fnDrawCallback: function(oSettings) {
        var cash_sale = sum_table_col($('#form_20_table'), 'cash_sale');
        $('#cash_sale').text(__number_f(cash_sale , false, false, __currency_precision));
        var credit_sale =  sum_table_col($('#form_20_table'), 'credit_sale');
        $('#credit_sale').text(__number_f(credit_sale , false, false, __currency_precision));
        var grand_total = cash_sale + credit_sale;
        $('#grand_total').text(__number_f(grand_total , false, false, __currency_precision));
    },
    });

    $('#form_20_date_range, #20_location_id').change(function(){
    form_20_table.ajax.reload();
    setTimeout(() => {
        // get_previous_value_20();
    }, 1500);

    if($('#20_location_id').val() !== ''  && $('#20_location_id').val() !== undefined){
        $('.f20_location_name').text($('#20_location_id :selected').text())
    }else{
        $('.f20_location_name').text('All')
    }
    });

    setTimeout(() => {
    // get_previous_value_20();
    }, 1500);

});

</script>
@endsection