@extends('layouts.app')

@section('title', __('sms::lang.sms'))

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
            {{--     <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('sector_fitler', __( 'sms::lang.sector' )) !!}
                    {!! Form::select('sector_fitler', ['private' => __('sms::lang.private'), 'government' =>
                    __('sms::lang.government')], null, ['class' => 'form-control select2',
                    'required',
                    'placeholder' => __(
                    'sms::lang.please_select' ), 'id' => 'sector_fitler']);
                    !!}
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('category_id_fitler', __( 'sms::lang.category' )) !!}
                    {!! Form::select('category_id_fitler', $categories, null, ['class' => 'form-control select2',
                    'required',
                    'placeholder' => __(
                    'sms::lang.please_select' ), 'id' => 'category_id_fitler']);
                    !!}
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('main_organization_fitler', __( 'sms::lang.main_organization' )) !!}
                    {!! Form::select('main_organization_fitler', $main_organizations, null, ['class' => 'form-control
                    select2',
                    'required',
                    'placeholder' => __(
                    'sms::lang.please_select' ), 'id' => 'main_organization_fitler']);
                    !!}
                </div>
            </div>
           <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('business_fitler', __( 'sms::lang.business' )) !!}
                    {!! Form::select('business_fitler', $businesses, null, ['class' => 'form-control select2',
                    'required',
                    'placeholder' => __(
                    'sms::lang.please_select' ), 'id' => 'business_fitler']);
                    !!}
                </div>
            </div> --}}


            {{-- <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('users_fitler', __( 'sms::lang.user' )) !!}
                    {!! Form::select('users_fitler', $users, null, ['class' => 'form-control select2',
                    'required',
                    'placeholder' => __(
                    'sms::lang.please_select' ), 'id' => 'users_fitler']);
                    !!}
                </div>
            </div> --}}
            @endcomponent
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            @component('components.widget', ['class' => 'box-primary', 'title' => __(
            'sms::lang.all_sms')])
            @slot('tool')
            @can('sms.create')
            <div class="box-tools">
                <button type="button" class="btn btn-primary btn-modal pull-right" id="add_sms_btn"
                    data-href="{{action('\Modules\SMS\Http\Controllers\SMSController@create')}}"
                    data-container=".sms_model">
                    <i class="fa fa-plus"></i> @lang( 'sms::lang.add' )</button>
            </div>
            @endcan
            @endslot

            <div class="row">
                <div class="col-md-12">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered" id="sms_table" style="width: 100%;">
                            <thead>
                                <tr>
                                    <th>@lang( 'sms::lang.action' )</th>
                                    <th>@lang( 'sms::lang.date_and_time' )</th>
                                    <th>@lang( 'sms::lang.message_id' )</th>
                                    <th>@lang( 'sms::lang.message_count' )</th>
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
    <div class="modal fade sms_model" role="dialog" aria-labelledby="gridSystemModalLabel">
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
    
    $(document).on('click', '#add_sms_btn', function(){
        $('.sms_model').modal({
            backdrop: 'static',
            keyboard: false
        })
    })

    $('#date_range_filter').change(function(){
        sms_table.ajax.reload();
    })

    // // sms_table
    sms_table = $('#sms_table').DataTable({
            processing: true,
            serverSide: false,
            ajax: {
                url : "{{action('\Modules\SMS\Http\Controllers\SMSController@index')}}",
                data: function(d){
                    d.start_date = $('#date_range_filter')
                        .data('daterangepicker')
                        .startDate.format('YYYY-MM-DD');
                    d.end_date = $('#date_range_filter')
                        .data('daterangepicker')
                        .endDate.format('YYYY-MM-DD');
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
                {data: 'sent_on', name: 'sent_on'},
                {data: 'id', name: 'id'},
                {data: 'count_message', name: 'count_message'},
            ]
        });

        $(document).on('click', 'a.delete-sms', function(){
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
                            sms_table.ajax.reload();
                        },
                    });
                }
            });
        });
</script>
@endsection