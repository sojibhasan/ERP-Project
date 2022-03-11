@extends('layouts.'.$layout)
@section('title', __('petro::lang.daily_pump_status'))

@section('content')
<section class="content-header">
    <div class="col-md-12">
        <h1 class="pull-left">@lang('petro::lang.daily_pump_status')</h1>
        <h2 class="text-red pull-right">@lang('petro::lang.date') : {{@format_date(date('Y-m-d'))}}</h2>

    </div>
</section>
@include('petro::pump_operators.partials.daily_pump_status')

<div class="modal fade pump_operator_modal" role="dialog" aria-labelledby="gridSystemModalLabel">
</div>

@endsection


@section('javascript')
<script type="text/javascript">
    $(document).ready( function(){
    list_daily_collection_table = $('#list_daily_collection_table').DataTable({
        processing: true,
        serverSide: true,
        aaSorting: [[0, 'desc']],
        ajax: {
            url: '{{action('\Modules\Petro\Http\Controllers\PumperDayEntryController@getDailyCollection')}}',
            data: function(d) {
               
            },
        },
        columnDefs: [ {
            "targets": 0,
            "orderable": false,
            "searchable": false
        }
        ],
        columns: [
            { data: 'action', searchable: false, orderable: false },
            { data: 'date_and_time', name: 'date_and_time' },
            { data: 'name', name: 'name' },
            { data: 'pump_no', name: 'pump_no' },
            { data: 'starting_meter', name: 'starting_meter' },
            { data: 'closing_meter', name: 'closing_meter' },
            { data: 'sold_ltr', name: 'sold_ltr' },
            { data: 'sold_amount', name: 'sold_amount' },
        ],
        fnDrawCallback: function(oSettings) {
            __currency_convert_recursively($('#list_daily_collection_table'));
        },
    });
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
                        list_daily_collection_table.ajax.reload();
                    },
                });
            }
        });
    });



</script>
@endsection