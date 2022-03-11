@extends('layouts.app')
@section('title', __('ran::lang.goldsmith'))

@section('content')

<section class="content-header">
    <div class="row">
        <div class="col-md-12 dip_tab">
            <div class="settlement_tabs">
                <ul class="nav nav-tabs">
                    <li class="@if(empty(session('status.tab'))) active @endif" style="margin-left: 20px;">
                        <a style="font-size:13px;" href="#goldsmith" class="" data-toggle="tab">
                            <i class="fa fa-superpowers"></i> <strong>@lang('ran::lang.goldsmith')</strong>
                        </a>
                    </li>
                    <li class="@if(session('status.tab') =='wastage') active @endif">
                        <a style="font-size:13px;" href="#goldsmith_wastage" data-toggle="tab">
                            <i class="fa fa-asterisk"></i> <strong>@lang('ran::lang.goldsmith_wastage')</strong>
                        </a>
                    </li>
                    <li class="@if(session('status.tab') =='details') active @endif">
                        <a style="font-size:13px;" href="#goldsmith_wastage_details" data-toggle="tab">
                            <i class="fa fa-file-text"></i>
                            <strong>@lang('ran::lang.goldsmith_wastage_details')</strong>
                        </a>
                    </li>

                </ul>
            </div>
        </div>
    </div>
    <div class="tab-content">
        <div class="tab-pane @if(empty(session('status.tab'))) active @endif" id="goldsmith">
            @include('ran::goldsmith.goldsmith.index')
        </div>
        <div class="tab-pane @if(session('status.tab') =='wastage') active @endif" id="goldsmith_wastage">
            @include('ran::goldsmith.wastage.index')
        </div>
        <div class="tab-pane @if(session('status.tab') =='details') active @endif" id="goldsmith_wastage_details">
            @include('ran::goldsmith.wastage.details')
        </div>
    </div>

    <div class="modal fade goldsmith_model" role="dialog" aria-labelledby="gridSystemModalLabel">
    </div>
</section>

@endsection
@section('javascript')
<script type="text/javascript">
    $(document).ready( function(){
    
        if ($('#details_date_range').length == 1) {
            $('#details_date_range').daterangepicker(dateRangeSettings, function(start, end) {
                $('#details_date_range').val(
                    start.format(moment_date_format) + ' - ' + end.format(moment_date_format)
                );
            });
            $('#details_date_range').on('cancel.daterangepicker', function(ev, picker) {
                $('#product_sr_date_filter').val('');
            });
            $('#details_date_range')
                .data('daterangepicker')
                .setStartDate(moment().startOf('month'));
            $('#details_date_range')
                .data('daterangepicker')
                .setEndDate(moment().endOf('month'));
        }

        $('#filter_category_id').change(function(){
            var cat = $('#filter_category_id').val();
            $.ajax({
                method: 'POST',
                url: '/products/get_sub_categories',
                dataType: 'html',
                data: { cat_id: cat },
                success: function(result) {
                if (result) {
                    $('#filter_sub_category_id').html(result);
                }
                },
            });
        });

        var columns = [
                { data: 'name', name: 'name' },
                { data: 'email', name: 'email' },
                { data: 'mobile', name: 'mobile' },
                { data: 'landline', name: 'landline' },
                { data: 'employee_number', name: 'employee_number' },
                { data: 'opening_gold_qty', name: 'opening_gold_qty' },
                { data: 'action', searchable: false, orderable: false },
            ];
    
        goldsmith_table = $('#goldsmith_table').DataTable({
            processing: true,
            serverSide: true,
            aaSorting: [[0, 'desc']],
            ajax: '{{action('\Modules\Ran\Http\Controllers\GoldSmithController@index')}}',
            columnDefs: [ {
                "targets": 6,
                "orderable": false,
                "searchable": false
            } ],
            @include('layouts.partials.datatable_export_button')
            columns: columns,
            fnDrawCallback: function(oSettings) {
            
            },
        });

        $(document).on('click', 'a.delete-goldsmith', function(e) {
            e.preventDefault();
            swal({
                title: LANG.sure,
                icon: 'warning',
                buttons: true,
                dangerMode: true,
            }).then(willDelete => {
                if (willDelete) {
                    var href = $(this).data('href');
                    var data = $(this).serialize();
                    console.log(href);
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
                            goldsmith_table.ajax.reload();
                        },
                    });
                }
            });
        });
        //wastage_table
        wastage_table = $('#wastage_table').DataTable({
            processing: true,
            serverSide: true,
            aaSorting: [[0, 'desc']],
            ajax: '{{action('\Modules\Ran\Http\Controllers\WastageController@index')}}',
            columnDefs: [ {
                "targets": 7,
                "orderable": false,
                "searchable": false
            } ],
            @include('layouts.partials.datatable_export_button')
            columns:  [
                { data: 'date_and_time', name: 'date_and_time' },
                { data: 'wastage_form_no', name: 'wastage_form_no' },
                { data: 'location', name: 'business_locations.name' },
                { data: 'goldsmith', name: 'goldsmiths.name' },
                { data: 'sub_category', name: 'categories.name' },
                { data: 'wastage', name: 'wastage' },
                { data: 'user', name: 'users.username' },
                { data: 'action', searchable: false, orderable: false },
            ],
            fnDrawCallback: function(oSettings) {
            
            },
        });
        //wastage_details_table
        wastage_details_table = $('#wastage_details_table').DataTable({
            processing: true,
            serverSide: true,
            aaSorting: [[0, 'desc']],
            ajax: {
                url: '{{action('\Modules\Ran\Http\Controllers\WastageController@getDetails')}}',
                data: function(d) {
                    d.category_id = $('select#filter_category_id').val();
                    d.sub_category_id = $('select#filter_sub_category_id').val();
                    d.goldsmith_id = $('select#filter_goldsmith_id').val();
                    d.start_date = $('input#details_date_range')
                        .data('daterangepicker')
                        .startDate.format('YYYY-MM-DD');
                    d.end_date = $('input#details_date_range')
                        .data('daterangepicker')
                        .endDate.format('YYYY-MM-DD');
                },
            },
            columnDefs: [ {
                "targets": 6,
                "orderable": false,
                "searchable": false
            } ],
            @include('layouts.partials.datatable_export_button')
            columns:  [
                { data: 'date_and_time', name: 'date_and_time' },
                { data: 'wastage_form_no', name: 'wastage_form_no' },
                { data: 'goldsmith', name: 'goldsmiths.name' },
                { data: 'category', name: 'cat.name' },
                { data: 'sub_category', name: 'sub_cat.name' },
                { data: 'wastage', name: 'wastage' },
                { data: 'action', searchable: false, orderable: false },
            ],
            fnDrawCallback: function(oSettings) {
            
            },
        });

        $(document).on('click', 'a.delete-wastage', function(e) {
            e.preventDefault();
            swal({
                title: LANG.sure,
                icon: 'warning',
                buttons: true,
                dangerMode: true,
            }).then(willDelete => {
                if (willDelete) {
                    var href = $(this).data('href');
                    var data = $(this).serialize();
                    console.log(href);
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
                            wastage_table.ajax.reload();
                            wastage_details_table.ajax.reload();
                        },
                    });
                }
            });
        });

        $('#details_date_range, #filter_sub_category_id, #filter_goldsmith_id, #filter_category_id').change(function(){
            wastage_details_table.ajax.reload();
        })
     
});
</script>
@endsection