@extends('layouts.app')

@section('title', __('lang_v1.customer_settings'))

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
            
            @endcomponent
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            @component('components.widget', ['class' => 'box-primary', 'title' => __(
            'lang_v1.all_customer_settings')])

            <div class="row">
                <div class="col-md-12">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered" id="customer_settings_table" style="width: 100%;">
                            <thead>
                                <tr>
                                    <th>@lang( 'lang_v1.action' )</th>
                                    <th>@lang( 'lang_v1.customer' )</th>
                                    <th>@lang( 'lang_v1.credit_limit' )</th>
                                </tr>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endcomponent
        </div>
    </div>
    <div class="modal fade customer_settings_model" role="dialog" aria-labelledby="gridSystemModalLabel">
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
    
    $(document).on('click', '#add_customer_settings_btn', function(){
        $('.customer_settings_model').modal({
            backdrop: 'static',
            keyboard: false
        })
    })

    $('#date_range_filter').change(function(){
        customer_settings_table.ajax.reload();
    })

    // // customer_settings_table
    customer_settings_table = $('#customer_settings_table').DataTable({
            processing: true,
            serverSide: false,
            ajax: {
                url : "{{action('CustomerSettingsController@index')}}",
                data: function(d){
                    // d.start_date = $('#date_range_filter')
                    //     .data('daterangepicker')
                    //     .startDate.format('YYYY-MM-DD');
                    // d.end_date = $('#date_range_filter')
                    //     .data('daterangepicker')
                    //     .endDate.format('YYYY-MM-DD');
                    // d.sector = $('#sector_fitler').val();
                    // d.category_id = $('#category_id_fitler').val();
                    // d.main_organization = $('#main_organization_fitler').val();
                    // d.business = $('#business_fitler').val();
                    // d.town = $('#town_fitler').val();
                    // d.district = $('#district_fitler').val();
                    // d.mobile_no = $('#mobile_no_fitler').val();
                    // d.created_by = $('#users_fitler').val();
                }
            },
            columnDefs:[{
                    "targets": 1,
                    "orderable": false,
                    "searchable": false
                }],
            columns: [
                {data: 'action', name: 'action'},
                {data: 'customer_name', name: 'contacts.name'},
                {data: 'credit_limit', name: 'credit_limit'},
            ]
        });

        $(document).on('click', 'a.delete-customer_settings', function(){
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
                            customer_settings_table.ajax.reload();
                        },
                    });
                }
            });
        });
</script>
@endsection