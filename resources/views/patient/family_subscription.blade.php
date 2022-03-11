@extends('layouts.app')
@section('title', __('patient.family_subscription'))

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>@lang('patient.family_subscription')</h1>
</section>

<!-- Main content -->
<section class="content">
    <div class="row">
        <div class="col-md-12">
            {!! Form::open(['method' => 'post', 'url' =>
            action('\Modules\Superadmin\Http\Controllers\FamilySubscriptionController@store')]) !!}
            @component('components.widget', ['class' => 'box-primary', 'title' => __( 'patient.family_subscription'
            )])
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('date', __('patient.date') . ':' ) !!}
                    {!! Form::text('date', null , ['class' => 'form-control
                    ', 'id' => 'date', 'readonly', 'placeholder' => __('patient.date')]); !!}
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('current_package_name', __('patient.current_package_name') . ':' ) !!}
                    {!! Form::text('current_package_name', $package->name , ['class' => 'form-control
                    ', 'id' => 'current_package_name', 'readonly', 'placeholder' =>
                    __('patient.current_package_name')]);
                    !!}
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('current_package_period', __('patient.current_package_period') . ':' ) !!}
                    {!! Form::text('current_package_period', $package->interval_count .' '. ucfirst($package->interval), ['class' => 'form-control
                    ', 'id' => 'current_package_period', 'readonly', 'placeholder' =>
                    __('patient.current_package_period')]); !!}
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('current_package_price', __('patient.current_package_price') . ':' ) !!}
                    {!! Form::text('current_package_price', $package->price , ['class' => 'form-control
                    ', 'id' => 'current_package_price', 'readonly', 'placeholder' =>
                    __('patient.current_package_price')]);
                    !!}
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('no_of_family_members', __('patient.no_of_family_members') . ':' ) !!}
                    {!! Form::number('no_of_family_members', 0 , ['class' => 'form-control
                    ', 'id' => 'no_of_family_members','placeholder' => __('patient.no_of_family_members')]);
                    !!}
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('value', __('patient.value') . ':' ) !!}
                    {!! Form::text('value', 0 , ['class' => 'form-control
                    ', 'id' => 'value', 'readonly', 'placeholder' => __('patient.value')]);
                    !!}
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('type', __('patient.type') . ':' ) !!}
                    {!! Form::text('type', null , ['class' => 'form-control
                    ', 'id' => 'type', 'readonly', 'placeholder' => __('patient.type')]);
                    !!}
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('increase_decrease', __('patient.increase_decrease') . ':' ) !!}
                    {!! Form::text('increase_decrease', null , ['class' => 'form-control
                    ', 'id' => 'increase_decrease', 'readonly', 'placeholder' => __('patient.increase_decrease')]);
                    !!}
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('amount_to_pay', __('patient.amount_to_pay') . ':' ) !!}
                    {!! Form::text('amount_to_pay', 0 , ['class' => 'form-control
                    ', 'id' => 'amount_to_pay', 'readonly', 'placeholder' => __('patient.amount_to_pay')]);
                    !!}
                </div>
            </div>
            <input type="hidden" name="amount_to_pay_hidden" id="amount_to_pay_hidden" value="0">
            <input type="hidden" name="option_variable_id" id="option_variable_id " value="0">
            <input type="hidden" name="package_id" id="package_id" value="{{$package->id}}">
            <input type="hidden" name="order_id" id="order_id" value="{{$order_id}}">

            @endcomponent
            <div class="row">
                <div class="col-md-12">
                    <button type="submit" disabled id="pay-submit"
                        class="pull-right btn btn-success">@lang('patient.pay')</button>
                </div>
            </div>
            {!! Form::close() !!}
        </div>
    </div>
    <div class="clearfix"></div>
    <br>
    <div class="row">
        <div class="col-md-12">
            @component('components.widget', ['class' => 'box-primary', 'title' => __( 'patient.all_your_subscriptions' )])
            <div class="table-responsive">
                <table class="table table-bordered table-striped" id="family_subscription_table" style="width: 100%;">
                    <thead>
                        <tr>
                            <th>@lang( 'superadmin::lang.patient_code' )</th>
                            <th>@lang( 'superadmin::lang.package_name' )</th>
                            <th>@lang( 'superadmin::lang.status' )</th>
                            <th>@lang( 'superadmin::lang.no_of_family_members' )</th>
                            <th>@lang( 'superadmin::lang.price' )</th>
                            <th>@lang( 'superadmin::lang.paid_via' )</th>
                            <th>@lang( 'superadmin::lang.payment_transaction_id' )</th>
                        </tr>
                    </thead>
                </table>
            </div>
            @endcomponent
        </div>
    </div>
</section>
<!-- /.content -->

@endsection

@section('javascript')
<script>
    $('#date').datepicker("setDate", new Date());

    $('#no_of_family_members').change(function(){
        var no_of_family_members = parseInt($(this).val());
        $.ajax({
            method: 'get',
            url: '/superadmin/family-subscription/get-option-variable',
            data: { option_value: $(this).val(), package_id: {{$package->id}}, option_id: 6 },
            success: function(result) {
                console.log(result.price_value);
                if(result){
                  
                    let package_amount = parseFloat($('#current_package_price').val());
                    let price_value = parseFloat(result.price_value);
                    $('#value').val(price_value);
                    amount_to_pay = 0;
                    if(result.variable_type == '1'){ // type percentaee
                        amount_to_pay = (package_amount * no_of_family_members) - (package_amount * no_of_family_members * price_value); 
                        $('#type').val('Percentage');
                    }
                    if(result.variable_type == '0'){ // type fixed
                        amount_to_pay = (package_amount * no_of_family_members) - (no_of_family_members * price_value); 
                        $('#type').val('Fixed');
                    }
                    if(result.increase_decrease == 0){
                        $('#increase_decrease').val('Increase');
                    }
                    if(result.increase_decrease == 1){
                        $('#increase_decrease').val('Decrease');
                    }
                    amount_to_pay = amount_to_pay / {{$package_period}} * {{$balance_period}};
                    $('#amount_to_pay').val(amount_to_pay);
                    $('#amount_to_pay_hidden').val(amount_to_pay);

                    if(amount_to_pay > 0){
                        $('#pay-submit').prop('disabled', false);
                    }
                    $('#option_variable_id').val(result.id);
                     
                }
            },
        });
    })

    $(document).ready(function(){
        // family_subscription_table
        var family_subscription_table = $('#family_subscription_table').DataTable({
            processing: true,
            serverSide: true,
            ajax: '/superadmin/family-subscription/patient',
            columnDefs:[{
                    "targets": 5,
                    "orderable": false,
                    "searchable": false
                }],
            "fnDrawCallback": function (oSettings) {
                __currency_convert_recursively($('#family_subscription_table'), true);
            }
        });

    })
</script>
@endsection