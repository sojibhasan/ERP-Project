@extends('layouts.app')

@section('title', __('ezyboat::lang.boat_trip'))

@section('content')
<!-- Main content -->
<section class="content">
    {!! Form::open(['url' => action('\Modules\Ezyboat\Http\Controllers\BoatOperationController@store'), 'method' =>
    'post', 'id' => 'boat_trip_form', 'enctype' => 'multipart/form-data' ])
    !!}
    <div class="row">
        <div class="col-md-12">
            @component('components.widget', ['class' => 'box-primary', 'title' => __(
            'ezyboat::lang.boat_trip')])
            <div class="col-md-4">
                <div class="form-group">
                    {!! Form::label('date_of_operation', __( 'ezyboat::lang.date_of_operation' )) !!}
                    {!! Form::text('date_of_operation', date('m/d/Y'), ['class' => 'form-control', 'required',
                    'placeholder' => __(
                    'ezyboat::lang.date_of_operation' ), 'readonly',
                    'id' => 'date_of_operation']);
                    !!}
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    {!! Form::label('location_id', __( 'ezyboat::lang.location' )) !!}
                    {!! Form::select('location_id', $business_locations, null, ['class' => 'form-control select2',
                    'required',
                    'placeholder' => __(
                    'ezyboat::lang.please_select' ), 'id' => 'location_id']);
                    !!}
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    {!! Form::label('contact_id', __( 'ezyboat::lang.customer' )) !!}
                    {!! Form::select('contact_id', $customers, null, ['class' => 'form-control select2',
                    'required',
                    'placeholder' => __(
                    'ezyboat::lang.please_select' ), 'id' => 'customer']);
                    !!}
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    {!! Form::label('route_id', __( 'ezyboat::lang.route' )) !!}
                    <div class="input-group">
                        {!! Form::select('route_id', $routes, null, ['class' => 'form-control select2',
                        'required',
                        'placeholder' => __(
                        'ezyboat::lang.please_select' ), 'id' => 'route_id']);
                        !!}
                        <span class="input-group-btn">
                            <button type="button" class="btn
                            btn-default
                            bg-white btn-flat btn-modal"
                                data-href="{{action('\Modules\Ezyboat\Http\Controllers\RouteController@create', ['quick_add' => true])}}"
                                title="@lang('fleet.add_route')" data-container=".view_modal"><i
                                    class="fa fa-plus-circle text-primary fa-lg"></i></button>
                        </span>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    {!! Form::label('fleet_id', __( 'ezyboat::lang.vehicle_no' )) !!}
                    {!! Form::select('fleet_id', $fleets, null, ['class' => 'form-control select2',
                    'required',
                    'placeholder' => __(
                    'ezyboat::lang.please_select' ), 'id' => 'fleet_id']);
                    !!}
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    {!! Form::label('invoice_no', __( 'ezyboat::lang.invoice_no' )) !!}
                    {!! Form::text('invoice_no', $invoice_number, ['class' => 'form-control', 'placeholder' => __(
                    'ezyboat::lang.invoice_no' ), 'readonly',
                    'id' => 'invoice_no']);
                    !!}
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    {!! Form::label('order_number', __( 'ezyboat::lang.order_number' )) !!}
                    {!! Form::text('order_number', null, ['class' => 'form-control', 'placeholder' => __(
                    'ezyboat::lang.order_number' ),
                    'id' => 'order_number']);
                    !!}
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    {!! Form::label('order_date', __( 'ezyboat::lang.order_date' )) !!}
                    {!! Form::text('order_date', null, ['class' => 'form-control', 'placeholder' => __(
                    'ezyboat::lang.order_date' ), 'readonly',
                    'id' => 'order_date']);
                    !!}
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    {!! Form::label('product_id', __( 'ezyboat::lang.product' )) !!}
                    {!! Form::select('product_id', $products , null, ['class' => 'form-control select2',
                    'placeholder' => __(
                    'ezyboat::lang.please_select' ), 'id' => 'product_id']);
                    !!}
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    {!! Form::label('qty', __( 'ezyboat::lang.qty' )) !!}
                    {!! Form::text('qty', null, ['class' => 'form-control', 'placeholder' => __(
                    'ezyboat::lang.qty' ),
                    'id' => 'qty']);
                    !!}
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    {!! Form::label('driver_id', __( 'ezyboat::lang.driver' )) !!}
                    {!! Form::select('driver_id', $drivers, null, ['class' => 'form-control select2',
                    'required',
                    'placeholder' => __(
                    'ezyboat::lang.please_select' ), 'id' => 'driver_id']);
                    !!}
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    {!! Form::label('helper_id', __( 'ezyboat::lang.helper' )) !!}
                    {!! Form::select('helper_id', $helpers, null, ['class' => 'form-control select2',
                    'required',
                    'placeholder' => __(
                    'ezyboat::lang.please_select' ), 'id' => 'helper_id']);
                    !!}
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    {!! Form::label('distance', __( 'ezyboat::lang.distance_km' )) !!}
                    {!! Form::text('distance', null, ['class' => 'form-control', 'placeholder' => __(
                    'ezyboat::lang.distance' ),
                    'id' => 'distance']);
                    !!}
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    {!! Form::label('amount', __( 'ezyboat::lang.amount' )) !!}
                    {!! Form::text('amount', null, ['class' => 'form-control', 'placeholder' => __(
                    'ezyboat::lang.amount' ),
                    'id' => 'amount']);
                    !!}
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    {!! Form::label('driver_incentive', __( 'ezyboat::lang.driver_incentive' )) !!}
                    {!! Form::text('driver_incentive', null, ['class' => 'form-control', 'placeholder' => __(
                    'ezyboat::lang.driver_incentive' ),
                    'id' => 'driver_incentive']);
                    !!}
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    {!! Form::label('helper_incentive', __( 'ezyboat::lang.helper_incentive' )) !!}
                    {!! Form::text('helper_incentive', null, ['class' => 'form-control', 'placeholder' => __(
                    'ezyboat::lang.helper_incentive' ),
                    'id' => 'helper_incentive']);
                    !!}
                </div>
            </div>
            <input type="hidden" name="grand_total_hidden" id="grand_total_hidden">
            <input type="hidden" name="final_total" id="final_total">

            @endcomponent

            @component('components.widget', ['class' => 'box-primary', 'title' => __('purchase.add_payment')])
            <div class="box-body payment_row" data-row_id="0">
                <div id="payment_rows_div">
                    @include('sale_pos.partials.payment_row_form', ['row_index' => 0])
                    <hr>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <button type="button" class="btn btn-primary btn-block"
                            id="add-payment-row">@lang('sale.add_payment_row')</button>
                    </div>
                </div>

                <div class="row">
                    <div class="col-sm-12">
                        <div class="pull-right"><strong>@lang('purchase.payment_due'):</strong> <span
                                id="payment_due">0.00</span>
                        </div>

                    </div>
                </div>
                <br>
                <div class="row">
                    <div class="col-sm-12">
                        <button type="button" id="boat_trip_form"
                            class="btn btn-primary pull-right btn-flat">@lang('messages.save')</button>
                    </div>
                </div>
            </div>
            @endcomponent
        </div>
    </div>
    <div class="modal fade fleet_model" role="dialog" aria-labelledby="gridSystemModalLabel">
    </div>
    {!! Form::close() !!}
</section>
<!-- /.content -->

@endsection

@section('javascript')
<script>
    $('#location_id option:eq(1)').attr('selected', true);
    $('#date_of_operation').datepicker('setDate', new Date());
    $('#order_date').datepicker();
    $('#route_id').change(function () {
        let id = $(this).val();

        $.ajax({
            method: 'get',
            url: '/fleet-management/routes/get-details/'+id,
            data: {  },
            success: function(result) {
                __write_number($('#distance'), result.distance);
                __write_number($('#amount'), result.route_amount);
                $('#grand_total_hidden').val(result.route_amount);
                $('#final_total').val(result.route_amount);
                $('#payment_due').text(__currency_trans_from_en(result.route_amount, false, false));
                $('#amount_0').val(result.route_amount);
                __write_number($('#driver_incentive'), result.driver_incentive);
                __write_number($('#helper_incentive'), result.helper_incentive);
                $('vehicle_no')
            },
        });
    });

    $('button#add-payment-row').click(function () {
        var row_index = parseInt($('.payment_row_index').val()) + 1;
        var location_id = $('#location_id').val();

        $.ajax({
            method: 'POST',
            url: '/purchases/get_payment_row',
            data: { row_index: row_index, location_id: location_id },
            dataType: 'html',
            success: function (result) {
                if (result) {
                    var total_payable = __read_number($('input#grand_total_hidden'));
                    var total_paying = 0;
                    $('#payment_rows_div')
                        .find('.payment-amount')
                        .each(function () {
                            if (parseFloat($(this).val())) {
                                total_paying += __read_number($(this));
                            }
                        });
                    var b_due = total_payable - total_paying;
                    var appended = $('#payment_rows_div').append(result);
                    $(appended).find('input.payment-amount').focus();
                    $(appended).find('input.payment-amount').last().val(b_due).change().select();
                    __select2($(appended).find('.select2'));
                    $('#amount_' + row_index).trigger('change');
                    $('#cheque_date_' + row_index).datepicker('setDate', new Date());
                    $('.payment_row_index').val(parseInt(row_index));
                    let cash_account_id = $('#cash_account_id').val();
                    $(appended).find('select.payment_types_dropdown ').last().val(cash_account_id).change().select();
                }
            },
        });
    });

    $(document).on('click', '.remove_payment_row', function () {
        swal({
            title: LANG.sure,
            icon: 'warning',
            buttons: true,
            dangerMode: true,
        }).then((willDelete) => {
            if (willDelete) {
                $(this).closest('.payment_row').remove();
                calculate_balance_due();
            }
        });
    });

    $(document).on('change', '.payment-amount', function() {
        calculate_balance_due();
    });

    
    function calculate_balance_due() {
        var total_payable = __read_number($('#final_total'));
        var total_paying = 0;
        $('#payment_rows_div')
            .find('.payment-amount')
            .each(function() {
                if (parseFloat($(this).val())) {
                    total_paying += __read_number($(this));
                }
            });
        var bal_due = total_payable - total_paying;
    

        $('#payment_due').text(__currency_trans_from_en(bal_due, false, false));
    }

    $('#boat_trip_form').click(function () {
        $('#boat_trip_form').validate();
        if($('#boat_trip_form').valid()){
            $('#boat_trip_form').submit();
        }
    })

    
 $(document).on('submit', 'form#quick_add_route', function (e) {
    e.preventDefault();
    var data = $(this).serialize();
    $.ajax({
      method: 'post',
      url: '{{action('\Modules\Ezyboat\Http\Controllers\RouteController@store')}}',
      dataType: 'json',
      data: data,
      success: function(result) {
        if(result.success){
            toastr.success(result.msg);
            $('.view_modal').modal('hide');
            $.ajax({
                method: 'get',
                url: '{{action('\Modules\Ezyboat\Http\Controllers\RouteController@getRouteDropdown')}}',
                data: {  },
                contactType: 'html',
                success: function(data_html) {
                    $('#route_id').empty().append(data_html);
                },
            });
        }else{
            toastr.error(result.msg);
        }
      },
    });
 })
</script>
@endsection