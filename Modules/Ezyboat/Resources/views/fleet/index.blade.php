@extends('layouts.app')

@section('title', __('ezyboat::lang.fleet'))

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
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('location_id', __( 'ezyboat::lang.location' )) !!}
                    {!! Form::select('location_id', $business_locations, null, ['class' => 'form-control select2',
                    'required',
                    'placeholder' => __(
                    'ezyboat::lang.please_select' ), 'id' => 'location_id']);
                    !!}
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('vehicle_number', __( 'ezyboat::lang.vehicle_number' )) !!}
                    {!! Form::select('vehicle_number', $vehicle_numbers, null, ['class' => 'form-control select2',
                    'required',
                    'placeholder' => __(
                    'ezyboat::lang.please_select' ), 'id' => 'vehicle_number']);
                    !!}
                </div>
            </div>
          
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('vehicle_type', __( 'ezyboat::lang.vehicle_type' )) !!}
                    {!! Form::select('vehicle_type', $vehicle_types, null, ['class' => 'form-control select2',
                    'required',
                    'placeholder' => __(
                    'ezyboat::lang.please_select' ), 'id' => 'vehicle_type']);
                    !!}
                </div>
            </div>
          
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('vehicle_brand', __( 'ezyboat::lang.vehicle_brand' )) !!}
                    {!! Form::select('vehicle_brand', $vehicle_brands, null, ['class' => 'form-control select2',
                    'required',
                    'placeholder' => __(
                    'ezyboat::lang.please_select' ), 'id' => 'vehicle_brand']);
                    !!}
                </div>
            </div>
          
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('vehicle_model', __( 'ezyboat::lang.vehicle_model' )) !!}
                    {!! Form::select('vehicle_model', $vehicle_models, null, ['class' => 'form-control select2',
                    'required',
                    'placeholder' => __(
                    'ezyboat::lang.please_select' ), 'id' => 'vehicle_model']);
                    !!}
                </div>
            </div>
          
            @endcomponent
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            @component('components.widget', ['class' => 'box-primary', 'title' => __(
            'ezyboat::lang.all_your_boats')])
            @slot('tool')
            <div class="box-tools">
                <button type="button" class="btn btn-primary btn-modal pull-right" id="add_fleet_btn"
                    data-href="{{action('\Modules\Ezyboat\Http\Controllers\EzyboatController@create')}}"
                    data-container=".fleet_model">
                    <i class="fa fa-plus"></i> @lang( 'ezyboat::lang.add' )</button>
            </div>
            @endslot

            <div class="row">
                <div class="col-md-12">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered" id="fleet_table" style="width: 100%;">
                            <thead>
                                <tr>
                                    <th>@lang( 'messages.action' )</th>
                                    <th>@lang( 'ezyboat::lang.date' )</th>
                                    <th>@lang( 'ezyboat::lang.location' )</th>
                                    <th>@lang( 'ezyboat::lang.code_vehicle' )</th>
                                    <th>@lang( 'ezyboat::lang.vehicle_number' )</th>
                                    <th>@lang( 'ezyboat::lang.vehicle_type' )</th>
                                    <th>@lang( 'ezyboat::lang.income' )</th>
                                    <th>@lang( 'ezyboat::lang.payment_received' )</th>
                                    <th>@lang( 'ezyboat::lang.payment_due' )</th>
                                    <th>@lang( 'ezyboat::lang.opening_amount' )</th>
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
    <div class="modal fade fleet_model" role="dialog" aria-labelledby="gridSystemModalLabel">
    </div>
</section>
<!-- /.content -->

@endsection

@section('javascript')
<script>
    $('#location_id option:eq(1)').attr('selected', true);

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
    
    $(document).on('click', '#add_fleet_btn', function(){
        $('.fleet_model').modal({
            backdrop: 'static',
            keyboard: false
        })
    })


    // fleet_table
    $(document).ready(function(){
        fleet_table = $('#fleet_table').DataTable({
                processing: true,
                serverSide: false,
                ajax: {
                    url : "{{action('\Modules\Ezyboat\Http\Controllers\EzyboatController@index')}}",
                    data: function(d){
                        d.location_id = $('#location_id').val();
                        d.vehicle_model = $('#vehicle_model').val();
                        d.vehicle_brand = $('#vehicle_brand').val();
                        d.vehicle_type = $('#vehicle_type').val();
                        d.vehicle_number = $('#vehicle_number').val();
                        d.start_date = $('#date_range_filter')
                            .data('daterangepicker')
                            .startDate.format('YYYY-MM-DD');
                        d.end_date = $('#date_range_filter')
                            .data('daterangepicker')
                            .endDate.format('YYYY-MM-DD');
                    }
                },
                columnDefs:[{
                        "targets": 1,
                        "orderable": false,
                        "searchable": false
                    }],
                columns: [
                    {data: 'action', name: 'action'},
                    {data: 'date', name: 'date'},
                    {data: 'location_name', name: 'location_name'},
                    {data: 'code_for_vehicle', name: 'code_for_vehicle'},
                    {data: 'vehicle_number', name: 'vehicle_number'},
                    {data: 'vehicle_type', name: 'vehicle_type'},
                    {data: 'income', name: 'income'},
                    {data: 'payment_received', name: 'payment_received'},
                    {data: 'payment_due', name: 'payment_due'},
                    {data: 'opening_balance', name: 'opening_balance'},
                  
                ],
                fnDrawCallback: function(oSettings) {
                    __currency_convert_recursively($('#fleet_table'));
                }
            });
        });

        $('#date_range_filter, #location_id, #vehicle_model, #vehicle_brand, #vehicle_type, #vehicle_number').change(function () {
            fleet_table.ajax.reload();
        })
        $(document).on('click', 'a.delete-fleet', function(){
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
                            fleet_table.ajax.reload();
                        },
                    });
                }
            });
        })
</script>
@endsection