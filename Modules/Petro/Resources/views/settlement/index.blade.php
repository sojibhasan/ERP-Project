@extends('layouts.app')
@section('title', __('petro::lang.list_settlement'))

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1> @lang('petro::lang.list_settlement')
        <small>@lang( 'petro::lang.mange_list_settlement')</small>
    </h1>
</section>

<!-- Main content -->
<section class="content">
    @if(!empty($message)) {!! $message !!} @endif
    <div class="row">
        <div class="col-md-12">
            @component('components.filters', ['title' => __('report.filters')])
                <div class="col-md-3">
                    <div class="form-group">
                        {!! Form::label('location_id',  __('purchase.business_location') . ':') !!}
                        {!! Form::select('location_id', $business_locations, null, ['class' => 'form-control select2', 'placeholder' => __('petro::lang.all'), 'style' => 'width:100%']); !!}
                    </div>
                </div>
                <div class="col-sm-3">
                    <div class="form-group">
                        {!! Form::label('pump_operator', __('petro::lang.pump_operator').':') !!}
                        {!! Form::select('pump_operator', $pump_operators, null, ['class' => 'form-control select2', 'placeholder' => __('petro::lang.all')]); !!}
                    </div>
                </div>
                <div class="col-sm-3">
                    <div class="form-group">
                        {!! Form::label('settlement_no', __('petro::lang.settlement_number').':') !!}
                        {!! Form::select('settlement_no', $settlement_nos, null, ['class' => 'form-control select2', 'placeholder' => __('petro::lang.all')]); !!}
                    </div>
                </div>
            
                <div class="col-md-3">
                    <div class="form-group">
                        {!! Form::label('date_range', __('report.date_range') . ':') !!}
                        {!! Form::text('date_range', @format_date('first day of this month') . ' ~ ' . @format_date('last day of this month') , ['placeholder' => __('lang_v1.select_a_date_range'), 'class' => 'form-control', 'id' => 'expense_date_range', 'readonly']); !!}
                    </div>
                </div>
            @endcomponent
        </div>
    </div>

    @component('components.widget', ['class' => 'box-primary', 'title' => __('petro::lang.all_your_list_settlement')])
    @slot('tool')
    <div class="box-tools ">
            <a class="btn  btn-primary" href="{{action('\Modules\Petro\Http\Controllers\SettlementController@create')}}">
                <i class="fa fa-plus"></i> @lang('messages.add')</a>
    </div>
    @endslot
    <div class="table-responsive">
        <table class="table table-bordered table-striped" id="list_settlement">
            <thead>
                <tr>
                    <th class="notexport">@lang('messages.action')</th>
                    <th>@lang('petro::lang.status')</th>
                    <th>@lang('petro::lang.settlement_date')</th>
                    <th>@lang('petro::lang.settlement_no')</th>
                    <th>@lang('petro::lang.pump_operator_name')</th>
                    <th>@lang('petro::lang.location')</th>
                    <th>@lang('petro::lang.shift')</th>
                    <th>@lang('petro::lang.note')</th>
                    <th>@lang('petro::lang.total_amnt')</th>
                </tr>
            </thead>
        </table>
    </div>
    @endcomponent

    <div class="modal fade settlement_modal" role="dialog" aria-labelledby="gridSystemModalLabel">
    </div>
    <div id="settlement_print" class="container"></div>
</section>
<!-- /.content -->

@endsection
@section('javascript')
<script type="text/javascript">
    $(document).ready( function(){
    var columns = [
            { data: 'action', searchable: false, orderable: false },
            { data: 'status', name: 'status' },
            { data: 'transaction_date', name: 'transaction_date' },
            { data: 'settlement_no', name: 'settlement_no' },
            { data: 'pump_operator_name', name: 'pump_operators.name' },
            { data: 'location_name', name: 'business_locations.name' },
            { data: 'shift', name: 'shift', searchable: false},
            { data: 'note', name: 'note' },
            { data: 'total_amount', name: 'total_amount' }
        ];
  
    list_settlement = $('#list_settlement').DataTable({
        processing: true,
        serverSide: true,
        aaSorting: [[0, 'desc']],
        ajax: {
            url: '{{action('\Modules\Petro\Http\Controllers\SettlementController@index')}}',
            data: function(d) {
                d.location_id = $('select#location_id').val();
                d.pump_operator = $('select#pump_operator').val();
                d.settlement_no = $('select#settlement_no').val();
                d.start_date = $('input#expense_date_range')
                    .data('daterangepicker')
                    .startDate.format('YYYY-MM-DD');
                d.end_date = $('input#expense_date_range')
                    .data('daterangepicker')
                    .endDate.format('YYYY-MM-DD');
            },
        },
        columnDefs: [ {
            "targets": 0,
            "orderable": false,
            "searchable": false
        } ],
        columns: columns,
        fnDrawCallback: function(oSettings) {
        
        },
    });

    $('#location_id, #pump_operator, #pump_operator, #settlement_no, #type, #expense_date_range').change(function(){
        list_settlement.ajax.reload();
    });


    $(document).on('click', 'a.delete_settlement_button', function(e) {
		e.preventDefault();
        swal({
            title: LANG.sure,
            icon: 'warning',
            buttons: true,
            dangerMode: true,
        }).then(willDelete => {
            if (willDelete) {
                var href = $(this).attr('href');
                var data = $(this).serialize();
                $.ajax({
                    method: 'DELETE',
                    url: href,
                    dataType: 'json',
                    data: data,
                    success: function(result) {
                        if (result.success == true) {
                            toastr.success(result.msg);
                        } else {
                            toastr.error(result.msg);
                        }
                        list_settlement.ajax.reload();
                    },
                });
            }
        });
    });

    $(document).on('click', 'a.delete_reference_button', function(e) {
		var page_details = $(this).closest('div.page_details')
		e.preventDefault();
        swal({
            title: LANG.sure,
            icon: 'warning',
            buttons: true,
            dangerMode: true,
        }).then(willDelete => {
            if (willDelete) {
                var href = $(this).attr('href');
                var data = $(this).serialize();
                console.log(href);
                $.ajax({
                    method: 'DELETE',
                    url: href,
                    dataType: 'json',
                    data: data,
                    success: function(result) {
                        if (result.success == true) {
                            page_details.remove();
                            toastr.success(result.msg);
                        } else {
                            toastr.error(result.msg);
                        }
                        list_settlement.ajax.reload();
                    },
                });
            }
        });
    });
});

$(document).on('click', '.edit_contact_button', function(e) {
    e.preventDefault();
    $('div.pump_operator_modal').load($(this).attr('href'), function() {
        $(this).modal('show');
    });
});

$('#location_id').select2();


//save settlement
$(document).on('click', '.print_settlement_button', function () {
    var url = $(this).data('href');
    $.ajax({
        method: 'get',
        url: url,
        data: {},
        success: function(result) {
            $('#settlement_print').html(result);

            var divToPrint=document.getElementById('settlement_print');

            var newWin=window.open('','Print-Ledger');
        
            newWin.document.open();
        
            newWin.document.write('<html><body onload="window.print()">'+divToPrint.innerHTML+'</body></html>');
        
            newWin.document.close();
            
        },
    });
});

$('#settlement_print').css('visibility', 'hidden');
</script>
@endsection