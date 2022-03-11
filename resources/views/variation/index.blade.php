@extends('layouts.app')
@section('title', __('product.variations'))

@section('content')
<section class="content-header">
    <div class="row">
        <div class="col-md-12 dip_tab">
            <div class="settlement_tabs">
                <ul class="nav nav-tabs">
                    <li class="@if(empty(session('status.tab'))) active @endif" style="margin-left: 20px;">
                        <a style="font-size:13px;" href="#variation" class="" data-toggle="tab">
                            <i class="fa fa-superpowers"></i> <strong>@lang('lang_v1.variation')</strong>
                        </a>
                    </li>
                    <li class="@if(session('status.tab') == 'variation_transfer') active @endif">
                        <a style="font-size:13px;" href="#variation_transfer" data-toggle="tab">
                            <i class="fa fa-exchange"></i> <strong>@lang('lang_v1.variation_transfer')</strong>
                        </a>
                    </li>
                   
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <div class="tab-content">
        <div class="tab-pane @if(empty(session('status.tab'))) active @endif" id="variation">
            @include('variation.partials.variations')
        </div>
        <div class="tab-pane @if(session('status.tab') == 'variation_transfer') active @endif" id="variation_transfer">
            @include('variation_transfer.index')
        </div>
    </div>
    <div class="modal fade variation_transfer_modal" tabindex="-1" role="dialog" 
    aria-labelledby="gridSystemModalLabel">
</div>

</section>

@endsection


@section('javascript')
    <script>
    var body = document.getElementsByTagName("body")[0];
    body.className += " sidebar-collapse";

    if ($('#form_date_range').length == 1) {
        $('#form_date_range').daterangepicker(dateRangeSettings, function(start, end) {
            $('#form_date_range').val(
                start.format(moment_date_format) + ' - ' + end.format(moment_date_format)
            );
        });
        $('#form_date_range').on('cancel.daterangepicker', function(ev, picker) {
            $('#product_sr_date_filter').val('');
        });
        $('#form_date_range')
            .data('daterangepicker')
            .setStartDate(moment().startOf('month'));
        $('#form_date_range')
            .data('daterangepicker')
            .setEndDate(moment().endOf('month'));
    }

$('#from_location').change(function(){
    let check_store_not = null;
    $.ajax({
        method: 'get',
        url: '/stock-transfer/get_transfer_store_id/'+$(this).val(),
        data: { check_store_not: check_store_not},
        success: function(result) {
            
            $('#filter_from_store').empty();
            $('#filter_from_store').append(`<option value="">Please Select</option>`);
            $.each(result, function(i, location) {
                $('#filter_from_store').append(`<option value= "`+location.id+`">`+location.name+`</option>`);
            });
        },
    });
});
$('#to_location').change(function(){
    let check_store_not = null;
    $.ajax({
        method: 'get',
        url: '/stock-transfer/get_transfer_store_id/'+$(this).val(),
        data: { check_store_not: check_store_not},
        success: function(result) {
            
            $('#filter_to_store').empty();
            $('#filter_to_store').append(`<option value="">Please Select</option>`);
            $.each(result, function(i, location) {
                $('#filter_to_store').append(`<option value= "`+location.id+`">`+location.name+`</option>`);
            });
        },
    });
});

$('#filter_category_id, #filter_sub_category_id').change(function(){
    var this_id = $(this).attr('id');
    var cat = $('#filter_category_id').val();
    var sub_cat = $('#filter_sub_category_id').val();
    $.ajax({
        method: 'POST',
        url: '/products/get_sub_categories',
        dataType: 'html',
        data: { cat_id: cat },
        success: function(result) {
        if (result) {
            console.log(this_id);
            if(this_id !== 'filter_sub_category_id'){
                $('#filter_sub_category_id').html(result);
            }
        }
        },
    });
    $.ajax({
        method: 'GET',
        url: '/variation-transfer/get-variation-by-category',
        dataType: 'html',
        data: { cat_id: cat , sub_cat_id: sub_cat },
        success: function(result) {
        if (result) {
            $('#from_variation_id').html(result);
            $('#to_variation_id').html(result);
        }
        },
    });
});

$(document).ready(function () {
    variation_transfer_table = $('#variation_transfer_table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '/variation-transfer',
            data: function (d) {
                if ($('#filter_location_from').length) {
                    d.from_location = $('#filter_location_from').val();
                }
                if ($('#filter_location_to').length) {
                    d.to_location = $('#filter_location_to').val();
                }
                if ($('#filter_from_store').length) {
                    d.from_store = $('#filter_from_store').val();
                }
                if ($('#filter_to_store').length) {
                    d.to_store = $('#filter_to_store').val();
                }
                if ($('#filter_category_id').length) {
                    d.category_id = $('#filter_category_id').val();
                }
                if ($('#filter_sub_category_id').length) {
                    d.sub_category_id = $('#filter_sub_category_id').val();
                }
                if ($('#filter_from_variation_id').length) {
                    d.from_variation_id = $('#filter_from_variation_id').val();
                }
                if ($('#filter_to_variation_id').length) {
                    d.to_variation_id = $('#filter_to_variation_id').val();
                }

                var start = '';
                var end = '';
                if ($('#form_date_range').val()) {
                    start = $('input#form_date_range')
                        .data('daterangepicker')
                        .startDate.format('YYYY-MM-DD');
                    end = $('input#form_date_range')
                        .data('daterangepicker')
                        .endDate.format('YYYY-MM-DD');
                }
                d.start_date = start;
                d.end_date = end;
            },
        },
        aaSorting: [[1, 'desc']],
        columns: [
            { data: 'action', name: 'action', orderable: false, searchable: false },
            { data: 'date', name: 'date' },
            { data: 'lf_name', name: 'lf.name' },
            { data: 'lt_name', name: 'lt.name' },
            { data: 'sf_name', name: 'sf.name' },
            { data: 'st_name', name: 'st.name' },
            { data: 'category_name', name: 'categories.name' },
            { data: 'sub_category_name', name: 'sub_category.name' },
            { data: 'fp_name', name: 'fp.name' },
            { data: 'tp_name', name: 'tp.name' },
            { data: 'qty', name: 'qty' },
            { data: 'unit_cost', name: 'unit_cost' },
            { data: 'total_cost', name: 'total_cost' },
            { data: 'added_by', name: 'users.username' },
           
        ],
        fnDrawCallback: function (oSettings) {
            __currency_convert_recursively($('#variation_transfer_table'));
        }
    });

    $('#form_date_range, #filter_location_from, #filter_location_to, #filter_from_store, #filter_to_store, #filter_category_id, #filter_sub_category_id, #filter_from_variation_id, #filter_to_variation_id').change(function () {
        variation_transfer_table.ajax.reload();
    })
});

$(document).on('click', 'a.delete-variation-transfer', function(e) {
        e.preventDefault();
        swal({
            title: LANG.sure,
            text: 'This store will be deleted.',
            icon: 'warning',
            buttons: true,
            dangerMode: true,
        }).then(willDelete => {
            if (willDelete) {
                var href = $(this).data('href');
                var data = $(this).serialize();

                $.ajax({
                    method: 'DELETE',
                    url: href,
                    dataType: 'json',
                    data: data,
                    success: function(result) {
                        if (result.success === 1) {
                            toastr.success(result.msg);
                            variation_transfer_table.ajax.reload();
                        } else {
                            toastr.error(result.msg);
                        }
                    },
                });
            }
        });
    });
    </script>
@endsection
