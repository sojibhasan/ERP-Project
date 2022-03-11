@extends('layouts.app')
@section('title', __('ran::lang.gold_prices'))

@section('content')

<section class="content-header">
    <div class="row">
        <div class="col-md-12 dip_tab">
            <div class="settlement_tabs">
                <ul class="nav nav-tabs">
                    <li class="active" style="margin-left: 20px;">
                        <a style="font-size:13px;" href="#gold_grade" class="" data-toggle="tab">
                            <i class="fa fa-superpowers"></i> <strong>@lang('ran::lang.gold_grade')</strong>
                        </a>
                    </li>
                    <li class="">
                        <a style="font-size:13px;" href="#gold_prices" data-toggle="tab">
                            <i class="fa fa-filter"></i> <strong>@lang('ran::lang.gold_prices')</strong>
                        </a>
                    </li>

                </ul>
            </div>
        </div>
    </div>
    <div class="tab-content">
        <div class="tab-pane active" id="gold_grade">
            @include('ran::gold_grade.index')
        </div>
        <div class="tab-pane" id="gold_prices">
            @include('ran::gold_prices.index')
        </div>

    </div>


    <div class="modal fade gold_price_model" role="dialog" aria-labelledby="gridSystemModalLabel">
    </div>
    <div class="modal fade gold_grade_model" role="dialog" aria-labelledby="gridSystemModalLabel">
    </div>
</section>
@endsection
@section('javascript')
<script>
    // $('#date_range').daterangepicker();
    //     if ($('#date_range').length == 1) {
    //         $('#date_range').daterangepicker(dateRangeSettings, function(start, end) {
    //             $('#date_range').val(
    //                 start.format(moment_date_format) + ' ~ ' + end.format(moment_date_format)
    //             );
    //         });
    //         $('#date_range').on('cancel.daterangepicker', function(ev, picker) {
    //             $('#product_sr_date_filter').val('');
    //         });
    //         $('#date_range')
    //             .data('daterangepicker')
    //             .setStartDate(moment().startOf('month'));
    //         $('#date_range')
    //             .data('daterangepicker')
    //             .setEndDate(moment().endOf('month'));
    //     }
    // $('#date_range').change(function(){
    //     gold_price_table.ajax.reload();
    // })

   
</script>

<script>
    $('#date_range_gold_grade').daterangepicker();
        if ($('#date_range_gold_grade').length == 1) {
            $('#date_range_gold_grade').daterangepicker(dateRangeSettings, function(start, end) {
                $('#date_range_gold_grade').val(
                    start.format(moment_date_format) + ' ~ ' + end.format(moment_date_format)
                );
            });
            $('#date_range_gold_grade').on('cancel.daterangepicker', function(ev, picker) {
                $('#product_sr_date_filter').val('');
            });
            $('#date_range_gold_grade')
                .data('daterangepicker')
                .setStartDate(moment().startOf('month'));
            $('#date_range_gold_grade')
                .data('daterangepicker')
                .setEndDate(moment().endOf('month'));
    }   

    // gold_grade_table
        gold_grade_table = $('#gold_grade_table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{action('\Modules\Ran\Http\Controllers\GoldGradeController@index')}}",
                data: function(d) {
                    var start_date = $('input#date_range_gold_grade')
                        .data('daterangepicker')
                        .startDate.format('YYYY-MM-DD');
                    var end_date = $('input#date_range_gold_grade')
                        .data('daterangepicker')
                        .endDate.format('YYYY-MM-DD');
                    d.start_date = start_date;
                    d.end_date = end_date;
                }
            },
            columnDefs:[{
                    "targets": 4,
                    "orderable": false,
                    "searchable": false
                }],
            columns: [
                {data: 'date_and_time', name: 'date_and_time'},
                {data: 'grade_name', name: 'description'},
                {data: 'last_gold_purity', name: 'last_gold_purity'},
                {data: 'gold_purity', name: 'gold_purity'},
                {data: 'username', name: 'users.username'},
                {data: 'action', name: 'action'}
            ],
            "fnDrawCallback": function (oSettings) {
            }
        });
        $(document).on('click', 'a.delete-gold_grade', function(){
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
                            gold_grade_table.ajax.reload();
                        },
                    });
                }
            });
        });

        $('#date_range_gold_grade').change(function(){
            gold_grade_table.ajax.reload();
        })

        $(document).on('click', '#add_gold_grade_btn', function(){
            $('.gold_grade_model').modal({
                backdrop: 'static',
                keyboard: false
            })
        })
        $(document).on('click', '.gold_grade_eidt', function(){
            $('.gold_grade_model').modal({
                backdrop: 'static',
                keyboard: false
            })
        })
</script>
@endsection