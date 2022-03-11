@extends('layouts.app')
@section('title', __('petro::lang.daily_collection'))

@section('content')
@php
$business_id = session()->get('user.business_id');
$business_details = App\Business::find($business_id);
@endphp

<section class="content-header">
    <div class="row">
        <div class="col-md-12 dip_tab">
            <div class="settlement_tabs">
                <ul class="nav nav-tabs">
                    <li class="active" style="margin-left: 20px;">
                        <a style="font-size:13px;" href="#daily_collection" class="" data-toggle="tab">
                            <i class="fa fa-superpowers"></i> <strong>@lang('petro::lang.daily_collection')</strong>
                        </a>
                    </li>
                    <li class="">
                        <a style="font-size:13px;" href="#daily_voucher" data-toggle="tab">
                            <i class="fa fa-thermometer"></i> <strong>@lang('petro::lang.daily_voucher')</strong>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <div class="tab-content">
        <div class="tab-pane active" id="daily_collection">
            @if(!empty($message)) {!! $message !!} @endif
            @include('petro::daily_collection.partials.daily_collection')
        </div>
        <div class="tab-pane" id="daily_voucher">
            @if(!empty($message)) {!! $message !!} @endif
            @include('petro::daily_collection.partials.daily_voucher')
        </div>

    </div>

    <div class="modal fade pump_modal" role="dialog" aria-labelledby="gridSystemModalLabel">
    </div>
</section>
@endsection
@section('javascript')
<script type="text/javascript">
    $(document).ready( function(){
        /*
        * @ChangedBy Afes
        * @Date 26-05-2021
        * @Task 1526 
        */      
    var columns = [
            { data: 'created_at', name: 'created_at' },
            { data: 'location_name', name: 'business_locations.name' },
            { data: 'pump_operator_name', name: 'pump_operators.name' },
            { data: 'collection_form_no', name: 'collection_form_no' },
            { data: 'current_amount', name: 'current_amount' },
            { data: 'total_collection', name: 'total_collection' },
            { data: 'user', name: 'users.username' },
            { data: 'settlement_no', name: 'settlements.settlement_no' },
            { data: 'settlement_date', name: 'settlement_date' },
            { data: 'action', searchable: false, orderable: false },
        ];
  
    daily_collection_table = $('#daily_collection_table').DataTable({
        processing: true,
        serverSide: true,
        aaSorting: [[0, 'desc']],
        ajax: {
            url: '{{action('\Modules\Petro\Http\Controllers\DailyCollectionController@index')}}',
            data: function(d) {
                d.location_id = $('select#location_id').val();
                /*
                * @ChangedBy Afes
                * @Date 26-05-2021
                * @Task 1526 
                */
                d.pump_operator = $('select#pump_operator').val();
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

    /*
    * @ChangedBy Afes
    * @Date 26-05-2021
    * @Task 1526 
    */
    $('#location_id, #pump_operator, #expense_date_range').change(function(){
        daily_collection_table.ajax.reload();
    });


    $(document).on('click', 'a.delete_daily_collection', function(e) {
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
                        daily_collection_table.ajax.reload();
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

$(document).on('click', '.print_btn_pump_operator', function() {
        var url = $(this).data('href');
        $.ajax({
            method: 'get',
			url: url,
            'Content-Type': 'html',
			data: {  },
			success: function(result) {
                console.log(result);
                $('#daily_collection_print').html(result);

                var divToPrint=document.getElementById('daily_collection_print');

                var newWin=window.open('','Print-Daily-Collection');
            
                newWin.document.open();
            
                newWin.document.write('<html><body onload="window.print()">'+divToPrint.innerHTML+'</body></html>');
            
                newWin.document.close();

			},
		});
    });

$(document).ready( function(){
    if ($('#daily_voucher_date_range').length == 1) {
        $('#daily_voucher_date_range').daterangepicker(dateRangeSettings, function (start, end) {
            $('#daily_voucher_date_range').val(
                start.format(moment_date_format) + ' ~ ' + end.format(moment_date_format)
            );
            expense_table.ajax.reload();
        });
        $('#daily_voucher_date_range').on('cancel.daterangepicker', function (ev, picker) {
            $('#product_sr_date_filter').val('');
            expense_table.ajax.reload();
        });
        $('#daily_voucher_date_range').data('daterangepicker').setStartDate(moment().startOf('month'));
        $('#daily_voucher_date_range').data('daterangepicker').setEndDate(moment().endOf('month'));
    }

     // daily_voucher_table
     daily_voucher_table = $('#daily_voucher_table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{action('\Modules\Petro\Http\Controllers\DailyVoucherController@index')}}",
                data : function(d){
                    d.location_id = $('#daily_voucher_location_id').val();
                    d.start_date = $('input#daily_voucher_date_range')
                        .data('daterangepicker')
                        .startDate.format('YYYY-MM-DD');
                    d.end_date = $('input#daily_voucher_date_range')
                        .data('daterangepicker')
                        .endDate.format('YYYY-MM-DD');
                }
            },
            columnDefs:[{
                    "targets": 8,
                    "orderable": false,
                    "searchable": false
                }],
            columns: [
                {data: 'created_at', name: 'created_at'},
                {data: 'location_name', name: 'business_locations.name'},
                {data: 'transaction_date', name: 'transaction_date'},
                {data: 'voucher_order_number', name: 'voucher_order_number'},
                {data: 'operator_name', name: 'pump_operators.name'},
                {data: 'customer_name', name: 'contacts.name'},
                {data: 'username', name: 'users.username'},
                {data: 'settlement_no', name: 'settlement_no'},
                {data: 'action', name: 'action'},
            ],
            "fnDrawCallback": function (oSettings) {
            }
        });
        $('#daily_voucher_location_id, #daily_voucher_date_range').change(function(){
            daily_voucher_table.ajax.reload();
        });
});
        $(document).on('click', 'a.delete-issue_bill_customer', function(){
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
                            daily_voucher_table.ajax.reload();
                        },
                    });
                }
            });
        });

        $(document).on('click', 'a.print_bill', function(){
            let href = $(this).data('href');

            $.ajax({
                method: 'get',
                url: href,
                data: {  },
                contentType: 'html',
                success: function(result) {
                    html = result;
                    var w = window.open('', '_self');
                    $(w.document.body).html(html);
                    w.print();
                    w.close();
                    location.reload();
                },
            });
        });


</script>
@endsection