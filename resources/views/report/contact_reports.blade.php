@extends('layouts.app')
@section('title', __('report.contact_report'))

@section('content')
<!-- Main content -->
<section class="content">

    <div class="row">
        <div class="col-md-12">
            <div class="settlement_tabs">
                <ul class="nav nav-tabs">
                    @can('contacts_report.view')
                    <li class="active">
                        <a href="#customer_group" class="customer_group" data-toggle="tab">
                            <i class="fa fa-file-text-o"></i> <strong>@lang('report.customer_group')</strong>
                        </a>
                    </li>

                    <li class="">
                        <a href="#contact" class="contact" data-toggle="tab">
                            <i class="fa fa-file-text-o"></i> <strong>@lang('report.contact')</strong>
                        </a>
                    </li>
                    @endcan

                </ul>
                <div class="tab-content">
                    @can('contacts_report.view')
                    <div class="tab-pane active" id="customer_group">
                        @include('report.customer_group')
                    </div>

                    <div class="tab-pane" id="contact">
                        @include('report.contact')
                    </div>
                    @endcan
                </div>
            </div>
        </div>
    </div>

</section>
<!-- /.content -->

@endsection
@section('javascript')
<script src="{{ asset('js/report.js?v=' . $asset_v) }}"></script>

<script type="text/javascript">
    $(document).ready(function(){
        if($('#cg_date_range').length == 1){
            $('#cg_date_range').daterangepicker({
                ranges: ranges,
                autoUpdateInput: false,
                locale: {
                    format: moment_date_format
                }
            });
            $('#cg_date_range').on('apply.daterangepicker', function(ev, picker) {
                $(this).val(picker.startDate.format(moment_date_format) + ' ~ ' + picker.endDate.format(moment_date_format));
                cg_report_table.ajax.reload();
            });

            $('#cg_date_range').on('cancel.daterangepicker', function(ev, picker) {
                $(this).val('');
                cg_report_table.ajax.reload();
            });
        }

        cg_report_table = $('#cg_report_table').DataTable({
            processing: true,
            serverSide: true,
            "ajax": {
                "url": "/reports/customer-group",
                "data": function ( d ) {
                    d.location_id = $('#cg_location_id').val();
                    d.customer_group_id = $('#cg_customer_group_id').val();
                    d.start_date = $('#cg_date_range').data('daterangepicker').startDate.format('YYYY-MM-DD');
                    d.end_date = $('#cg_date_range').data('daterangepicker').endDate.format('YYYY-MM-DD');
                }
            },
            columns: [
                {data: 'name', name: 'CG.name'},
                {data: 'total_sell', name: 'total_sell', searchable: false}
            ],
            "fnDrawCallback": function (oSettings) {
                __currency_convert_recursively($('#cg_report_table'));
            }
        });
        //Customer Group report filter
        $('select#cg_location_id, select#cg_customer_group_id, #cg_date_range').change( function(){
            cg_report_table.ajax.reload();
        });
    })
</script>

<script>
     $('#contact_type').change(function(){
        if($(this).val() == 'customer'){
            $('.suppliers').addClass('hide');
            $('.both').addClass('hide');
            $('.customers').removeClass('hide');
        }
        if($(this).val() == 'supplier'){
            $('.suppliers').removeClass('hide');
            $('.both').addClass('hide');
            $('.customers').addClass('hide');
        }
        if($(this).val() == 'both'){
            $('.suppliers').addClass('hide');
            $('.both').removeClass('hide');
            $('.customers').addClass('hide');
        }
    })
</script>


@endsection
