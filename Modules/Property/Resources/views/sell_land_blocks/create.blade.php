@extends('layouts.property')
@section('title', __('property::lang.sell_land_blocks'))

@section('content')

<style>
    #key_pad input {
        border: none
    }

    #key_pad button {
        height: 56px;
        width: 56px;
        font-size: 25px;
        margin: 2px 1px;
        border: none !important;
    }

    :focus {
        outline: 0 !important
    }

    .border-top {
        border-top: 2px solid brown;
    }
</style>
<!-- Content Header (Page header) -->
<section class="content-header">
    <h2 style="margin-top:0px;margin-bottom:0px; ">{{ __('property::lang.sell_land_blocks') }}
    </h2>
</section>
{!! Form::open(['url' => action('\Modules\Property\Http\Controllers\SellLandBlockController@store'), 'method' =>
'post', 'id' => 'sell_land_form',
'files' => true ]) !!}
<!-- Main content -->
<section class="content no-print">
    @component('components.widget', ['class' => 'box-primary'])
    <div class="hide">
        {!! Form::select('contact_id',
        ['customer' => 'Customer', 'supplier' => 'Supplier'], 'customer', ['class' => 'form-control select2 hide',
        'id' => 'contact_type', 'placeholder' =>
        'Type', 'required']); !!}
    </div>
    <div class="row">
        <div class="col-sm-2">
            <div class="form-group">
                {!! Form::label('contact_id', __('property::lang.customer') . ':*') !!}
                <div class="input-group">
                    {!! Form::select('contact_id',
                    $customers, null, ['class' => 'form-control select2', 'id' => 'contact_id', 'placeholder' =>
                    'Enter
                    Customer name', 'required']); !!}
                    <span class="input-group-btn">
                        <button type="button" class="btn btn-default bg-white btn-flat add_new_customer" data-name=""><i
                                class="fa fa-plus-circle text-primary fa-lg"></i></button>
                    </span>
                </div>
            </div>
        </div>
        <div class="col-sm-2">
            <div class="form-group">
                {!! Form::label('property_id', __('property::lang.land') . ':*') !!}
                {!! Form::select('property_id', $properties, $property->id, ['class' => 'form-control select2', 'id'
                => 'property_id', 'placeholder' =>
                __('property::lang.please_select')]); !!}

            </div>
        </div>
        <div class="col-sm-2">
            <div class="form-group">
                {!! Form::label('block_id', __('property::lang.block_no') . ':*') !!}
                {!! Form::select('block_id',
                [], null, ['class' => 'form-control select2', 'id' => 'block_id', 'placeholder' =>
                __('lang_v1.please_select')]); !!}
            </div>
        </div>
        <div class="col-sm-1">
            <div class="form-group">
                {!! Form::label('unit', __('property::lang.unit') . ':*') !!}
                {!! Form::text('unit', null, ['class' => 'form-control', 'id' => 'unit', 'placeholder' =>
                __('property::lang.unit'), 'readonly']); !!}

            </div>
        </div>
        <div class="col-sm-2">
            <div class="form-group">
                {!! Form::label('block_value', __('property::lang.block_value') . ':*') !!}
                {!! Form::text('block_value', null, ['class' => 'form-control', 'id' => 'block_value', 'placeholder'
                =>
                __('property::lang.block_value'), 'readonly']); !!}
            </div>
            @if(auth()->user()->can('dashboard.change'))
            <br>
            <div class="form-group">
                <a class="btn btn-flat btn-block btn-primary" id="change_remove_readonly">Change</a>
            </div>
            @endif
        </div>

        <div class="col-sm-2">
            <div class="form-group">
                {!! Form::label('block_extent', __('property::lang.block_extent') . ':*') !!}
                {!! Form::text('block_extent', null, ['class' => 'form-control', 'id' => 'block_extent',
                'placeholder'
                =>
                __('property::lang.block_extent'), 'readonly']); !!}

            </div>
        </div>
        <div class="col-md-1 col-xs-12">
            <i class="btn btn-primary add_block_purchase"
                style="font-size: 20px; padding: 0px 10px 0px 10px; margin-top: 25px;">+</i>
        </div>
        <div class="clearfix"></div>
        <hr>

        <div class="col-md-12">
            <div class="col-md-7">
                <div class="col-sm-4">
                    <div class="form-group">
                        {!! Form::label('date', __('property::lang.date')) !!}
                        {!! Form::text('date', null, ['class' => 'form-control', 'id' => 'date', 'placeholder' =>
                        __('property::lang.date'), 'required', 'readonly']); !!}

                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="form-group">
                        {!! Form::label('land_name', __('property::lang.land_name')) !!}
                        {!! Form::text('land_name', null, ['class' => 'form-control', 'id' => 'land_name',
                        'placeholder' =>
                        __('property::lang.land_name'), 'required', 'readonly']); !!}

                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="form-group">
                        {!! Form::label('total_block_value', __('property::lang.total_block_value')) !!}
                        {!! Form::text('total_block_value', null, ['class' => 'form-control', 'id' =>
                        'total_block_value', 'placeholder' =>
                        __('property::lang.total_block_value'), 'required', 'readonly']); !!}

                    </div>
                </div>
                <input type="hidden" name="block_row_id" id="block_row_id" value="0">
                <input type="hidden" name="final_total" id="final_total" value="0">
                <div class="clearfix"></div>
                <hr>
                <div class="col-md-12">
                    <h3 class="text-brown">@lang('property::lang.block_purchase_details'):</h3>
                    <table class="table table-striped" id="block_table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>{{ __('property::lang.block_no') }}</th>
                                <th>{{ __('property::lang.size') }}</th>
                                <th>{{ __('property::lang.unit') }}</th>
                                <th>{{ __('property::lang.block_value') }}</th>
                                <th></th>
                            </tr>
                        </thead>

                        <tbody>
                            {{-- @foreach ($ablocks as $block)
                                
                            <tr>
                                <td>{{$block->block_number}}</td>
                                <td>{{@format_quantity($block->block_extent)}}</td>
                                <td>{{$block->actual_name}}</td>
                                <td>{{@num_format($block->block_sold_price)}}</td>
                                
                            </tr>
                            @endforeach --}}
                        </tbody>
                    </table>
                </div>
                <div class="block_data"></div>
                <div class="clearfix"></div>
                <hr>
                <div class="col-sm-4">
                    <div class="form-group">
                        {!! Form::label('finance_option_id', __('property::lang.finance_option')) !!}
                        {!! Form::select('finance_option_id', $finance_options, null, ['class' => 'form-control
                        select2', 'id' => 'finance_option_id', 'placeholder' =>
                        __('property::lang.finance_option'), ]); !!}

                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="row">
                    <div id="key_pad" tabindex="1">
                        <div class="row text-center" id="calc">
                            <div class="calcBG col-md-12 text-center">
                                <div class="row">
                                    <button id="7" type="button" class="btn btn-primary btn-sm"
                                        onclick="enterVal(this.id)">7</button>
                                    <button id="8" type="button" class="btn btn-primary btn-sm"
                                        onclick="enterVal(this.id)">8</button>
                                    <button id="9" type="button" class="btn btn-primary btn-sm"
                                        onclick="enterVal(this.id)">9</button>

                                </div>
                                <div class="row">
                                    <button id="4" type="button" class="btn btn-primary btn-sm"
                                        onclick="enterVal(this.id)">4</button>
                                    <button id="5" type="button" class="btn btn-primary btn-sm"
                                        onclick="enterVal(this.id)">5</button>
                                    <button id="6" type="button" class="btn btn-primary btn-sm"
                                        onclick="enterVal(this.id)">6</button>
                                </div>
                                <div class="row">
                                    <button id="1" type="button" class="btn btn-primary btn-sm"
                                        onclick="enterVal(this.id)">1</button>
                                    <button id="2" type="button" class="btn btn-primary btn-sm"
                                        onclick="enterVal(this.id)">2</button>
                                    <button id="3" type="button" class="btn btn-primary btn-sm"
                                        onclick="enterVal(this.id)">3</button>
                                </div>
                                <div class="row">
                                    <button id="backspace" type="button" class="btn btn-danger"
                                        onclick="enterVal(this.id)">⌫</button>
                                    <button id="0" type="button" class="btn btn-primary btn-sm"
                                        onclick="enterVal(this.id)">0</button>
                                    <button id="enter" type="button" class="btn btn-success"
                                        onclick="enterVal(this.id)">↵</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
            <div class="col-md-2">
                <a href="{{action('\Modules\Property\Http\Controllers\SaleAndCustomerPaymentController@dashboard')}}"
                    class="btn btn-flat btn-block btn-info ">@lang('property::lang.dashboard')</a>
                <button type="submit" class="btn btn-flat btn-block btn-primary ">@lang('property::lang.save')</button>
                <button type="button"
                    class="btn btn-flat btn-block btn-danger cancel_btn ">@lang('property::lang.cancel')</button>
                <a href="{{action('Auth\PropertyUserLoginController@logout')}}"
                    class="btn btn-flat btn-block btn-warning ">@lang('property::lang.log_off')</a>
                <a href="{{action('HomeController@index')}}"
                    class="btn btn-flat btn-block bg-purple ">@lang('property::lang.main_system')</a>
            </div>
        </div>
    </div>

    <div class="clearfix"></div>
    <div class="border-top">
        <div class="col-md-12 payment_row">
            <h3 class="text-brown">@lang('property::lang.payment_options')</h3>
            <div class="clearfix"></div>
            <div class="col-md-3 col-xs-12">
                <div class="form-group">
                    {!! Form::label('payment_option', __('property::lang.payment_option')) !!}
                    {!! Form::select('payment_option', $payment_options, null, ['class' => 'form-control
                    select2', 'id' => 'payment_option', 'placeholder' =>
                    __('property::lang.please_select')]); !!}

                </div>
            </div>
            <div class="col-md-3 col-xs-12">
                <div class="form-group">
                    {!! Form::label('amount', __('property::lang.amount')) !!}
                    {!! Form::text('amount', null, ['class' => 'form-control', 'id' => 'amount', 'placeholder' =>
                    __('property::lang.amount')]); !!}

                </div>
            </div>
            <input type="hidden" name="row_id" id="row_id" value="0">
            <input type="hidden" name="total_amount" id="total_amount" value="0">
            <div class="col-md-3 col-xs-12">
                <i class="btn btn-primary add_payment_row"
                    style="font-size: 20px; padding: 0px 10px 0px 10px; margin-top: 25px;">+</i>
            </div>
            <div class="clearfix"></div>

            <div class="col-md-6">
                <table class="table table-striped payment_table" id="payment_table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>{{ __('property::lang.on_account_of') }}</th>
                            <th>{{ __('property::lang.amount') }}</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody></tbody>

                </table>
            </div>

            <div class="payment_table_data"></div>
            <div class="clearfix"></div>
            <hr>
            <div class="col-md-12">
                <div class="col-md-6">
                    <div class="text-red text-right " style="margin-top: 25px; font-size: 23px; font-weight: bold">
                        @lang('property::lang.total_amount'): <span class="total_amount">0.00</span>
                    </div>
                </div>
                <div class="col-md-3 col-xs-12">
                    <div class="form-group">
                        {!! Form::label('payment_method', __('property::lang.payment_method')) !!}
                        {!! Form::select('payment_method', $payment_types, null, ['class' => 'form-control
                        payment_types_dropdown
                        select2', 'id' => 'payment_method', 'placeholder' =>
                        __('property::lang.please_select')]); !!}

                    </div>
                </div>
                <div class="col-md-2 col-xs-12">
                    <div class="form-group">
                        {!! Form::label('payment_method_amount', __('property::lang.amount')) !!}
                        {!! Form::text('payment_method_amount', null, ['class' => 'form-control', 'id' => 'payment_method_amount', 'placeholder' =>
                        __('property::lang.amount')]); !!}

                    </div>
                </div>
                <div class="col-md-1 col-xs-12">
                    <i class="btn btn-primary add_payment_section_row"
                       style="font-size: 20px; padding: 0px 10px 0px 10px; margin-top: 25px;">+</i>
                </div>
                <div class="clearfix"></div>
                <div class="col-md-3 hide  account_module">
                    <div class="form-group">
                        {!! Form::label("account_id" , __('lang_v1.bank_account') . ':') !!}
                        <div class="input-group">
                        <span class="input-group-addon">
                            <i class="fa fa-money"></i>
                        </span>
                            {!! Form::select("account_id", $bank_group_accounts, null , ['class' =>
                            'form-control
                            select2', 'placeholder' => __('lang_v1.please_select'), 'id' => "account_id", 'style' =>
                            'width:100%;']); !!}
                        </div>
                    </div>
                </div>
                @include('property::sell_land_blocks.partials.payment_type_details', ['payment_line' => $payment,
                'row_index' => 0 ])
            </div>

            <input type="hidden" name="m_row_id" id="m_row_id" value="0">
            <input type="hidden" name="total_payment_method_amount" id="total_payment_method_amount" value="0">
            <h3 class="text-brown">@lang('property::lang.payment_section')</h3>
            <div class="clearfix"></div>
            <div class="col-md-10">
                <table class="table table-striped payment_section" id="payment_section">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>{{ __('property::lang.payment_method') }}</th>
                        <th>{{ __('property::lang.cheque_no') }}</th>
                        <th>{{ __('property::lang.cheque_date') }}</th>
                        <th>{{ __('property::lang.bank') }}</th>
                        <th>{{ __('property::lang.card_no') }}</th>
                        <th>{{ __('property::lang.amount') }}</th>
                    </tr>
                    </thead>
                    <tbody></tbody>

                </table>
            </div>
            <div class="payment_data"></div>
            <div class="clearfix"></div>
            <hr>
            <div class="col-md-12">
                <div class="col-md-6"></div>
                <div class="col-md-3">
                    <div class="text-red text-right " style="font-size: 23px; font-weight: bold">
                        @lang('property::lang.total_amount_paid'): <span class="total_payment_method_amount">0.00</span>
                    </div>
                </div>
                <div class="col-md-3 text-right" style="float: right; font-size: 23px; font-weight: bold">
                    <button type="submit" class="btn btn-primary">@lang('property::lang.pay_now')</button>
                </div>
            </div>
        </div>
    </div>

    @endcomponent
    {!! Form::close() !!}


    <div class="modal fade contact_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
        @include('contact.create', ['quick_add' => true])
    </div>
    <div id="invoice_print"></div>
</section>
<!-- /.content -->
@stop
@section('javascript')
<script src="https://code.highcharts.com/highcharts.js"></script>
<script>
    //ptr remove
    $(document).on('click', '#change_remove_readonly', function() {
        if($('#block_value').val()){
            $('#block_value').removeAttr('readonly');
        }
    });

    $("#payment_table").on("click", ".p-remove", function(event) {
        rowindex = $(this).closest('tr').index();
        $('[name="account_of['+$(this).attr("index")+'][amount]"]').remove();
        $('[name="account_of['+$(this).attr("index")+'][payment_option_id]"]').remove();

        $(this).closest("tr").remove();
        var preoff=0;
        $("#payment_table .onAccountof").each(function()
        {
            preoff = parseInt($(this).text().replace(",", ""))+parseInt(preoff);
        });
        $('#total_amount').val(preoff);
        $('.total_amount').text(__currency_trans_from_en(preoff, false))

        let total_block_value = __number_uf($('#total_block_value').val());
        let remove_block_value = __number_f($('.block_value').val());
    });

    //Method tr remove

    $("#payment_section").on("click", ".m-remove", function(event) {
        rowindex = $(this).closest('tr').index();

        $('[name="payment['+$(this).attr("index_id")+'][payment_method_amount]"]').remove();
        $('[name="payment['+$(this).attr("index_id")+'][method]"]').remove();
        $('[name="payment['+$(this).attr("index_id")+'][account_id]"]').remove();
        $('[name="payment['+$(this).attr("index_id")+'][card_number]"]').remove();
        $('[name="payment['+$(this).attr("index_id")+'][card_transaction_number]"]').remove();
        $('[name="payment['+$(this).attr("index_id")+'][card_holder_name]"]').remove();
        $('[name="payment['+$(this).attr("index_id")+'][card_type]"]').remove();
        $('[name="payment['+$(this).attr("index_id")+'][card_year]"]').remove();
        $('[name="payment['+$(this).attr("index_id")+'][card_month]"]').remove();
        $('[name="payment['+$(this).attr("index_id")+'][card_security]"]').remove();
        $('[name="payment['+$(this).attr("index_id")+'][cheque_number]"]').remove();
        $('[name="payment['+$(this).attr("index_id")+'][cheque_date]"]').remove();
        $('[name="payment['+$(this).attr("index_id")+'][bank_name]"]').remove();

        $(this).closest("tr").remove();
        let total_block_value = __number_uf($('#total_block_value').val());
        let remove_block_value = __number_f($('.block_value').val());
        var pre=0;
        $("#payment_section .payment_section_amount").each(function()
        {
            pre = parseInt($(this).text().replace(",", ""))+parseInt(pre);
        });
        $('#total_payment_method_amount').val(pre);
        $('.total_payment_method_amount').text(__currency_trans_from_en(pre, false))
    });
    $('#date').datepicker('setDate', new Date());
        $('#block_id').change(function(){
            let block_no = $(this).val();
            $.ajax({
                method: 'get',
                url: '/property/property-blocks/get-block-details/' +block_no,
                data: {  },
                success: function(result) {

                    if(result.success === 1){
                        $('#unit').val(result.data.unit_name);
                        __write_number($('#block_value'), result.data.block_sale_price);
                        __write_number($('#block_extent'), result.data.block_extent);
                    }
                },
            });
        })


        $(document).on('click', '.add_payment_section_row', function(){
            let amount = $('#payment_method_amount').val();
            if(amount === '' || amount === undefined || parseFloat(amount) === 0){
                toastr.error('Amount is required');
                return false;
            }
            let payment_method = $('#payment_method').val();
            let payment_method_text = $('#payment_method option:selected').text();
            let account_id = $('#account_id').val();
            let card_holder_name = $('#card_holder_name_0').val();
            let card_transaction_number = $('#card_transaction_number_0').val();
            let card_number = $('#card_number_0').val();
            let card_type = $('#card_type_0').val();
            let card_month = $('#card_month_0').val();
            let card_year = $('#card_year_0').val();
            let card_security = $('#card_security_0').val();
            let cheque_number = $('#cheque_number_0').val();
            let cheque_date = $('#cheque_date_0').val();
            let bank_name = $('#bank_name_0').val();
            let m_row_id = parseInt($('#m_row_id').val());

            if(payment_method != 'bank_transfer' && payment_method != 'cheque'){
                cheque_date = '';
            }
            $('#payment_section tbody').append(
                `<tr>
                    <td>${m_row_id+1}</td>
                    <td>${payment_method_text}</td>
                    <td>${cheque_number}</td>
                    <td>${cheque_date}</td>
                    <td>${bank_name}</td>
                    <td>${card_number}</td>
                    <td class="payment_section_amount">${__number_f(amount)}</td>
                     <td class="text-center"><button type="button" class="btn btn-danger btn-xs m-remove" index_id="${m_row_id}"><i class="glyphicon glyphicon-remove"></i></button></td>
                </tr>`
            )
            $('.payment_data').append(
                `<input type="hidden" name="payment[${m_row_id}][payment_method_amount]" value="${amount}">
                <input type="hidden" name="payment[${m_row_id}][method]" value="${payment_method}">
                <input type="hidden" name="payment[${m_row_id}][account_id]" value="${account_id}">
                <input type="hidden" name="payment[${m_row_id}][card_number]" value="">
                <input type="hidden" name="payment[${m_row_id}][card_transaction_number]" value="">
                <input type="hidden" name="payment[${m_row_id}][card_holder_name]" value="${card_holder_name}">
                <input type="hidden" name="payment[${m_row_id}][card_type]" value="${card_type}">
                <input type="hidden" name="payment[${m_row_id}][card_year]" value="${card_year}">
                <input type="hidden" name="payment[${m_row_id}][card_month]" value="${card_month}">
                <input type="hidden" name="payment[${m_row_id}][card_security]" value="${card_security}">
                <input type="hidden" name="payment[${m_row_id}][cheque_number]" value="${cheque_number}">
                <input type="hidden" name="payment[${m_row_id}][cheque_date]" value="${cheque_date}">
                <input type="hidden" name="payment[${m_row_id}][bank_name]" value="${bank_name}">
        `
            )

            $('#m_row_id').val(m_row_id + 1);
            $('#payment_method_amount').val(0);
            $('#payment_method').val('cash').trigger('change');
            $('.reset').val('');

            let total_amount = parseFloat($('#total_payment_method_amount').val());
            amount = parseFloat(amount);
            total_amount = total_amount + amount;
            $('#total_payment_method_amount').val(total_amount);
            $('.total_payment_method_amount').text(__currency_trans_from_en(total_amount, false))

        });

    $(document).on('click', '.add_payment_row', function(){
        let payment_option = $('#payment_option').val();
        let payment_option_text = $('#payment_option option:selected').text();
        let amount = $('#amount').val();
        if(amount === '' || amount === undefined || parseFloat(amount) === 0){
            toastr.error;
            return false;
        }

        let row_id = parseInt($('#row_id').val());


        $('#payment_table tbody').append(
            `<tr>
                    <td>${row_id+1}</td>
                    <td>${payment_option_text}</td>
                    <td class="onAccountof">${__number_f(amount)}</td>
                     <td class="text-center"><button type="button" class="btn btn-danger btn-xs p-remove" index="${row_id}" ><i class="glyphicon glyphicon-remove"></i></button></td>

                </tr>`
        )
        $('.payment_table_data').append(
            `
                <input type="hidden" name="account_of[${row_id}][payment_option_id]" value="${payment_option}">
                <input type="hidden" name="account_of[${row_id}][amount]" value="${amount}">
        `
        )

        $('#row_id').val(row_id + 1);
        $('#payment_option').val('').trigger('change');
        $('#amount').val(0);
        $('.reset').val('');

        let total_amount = parseFloat($('#total_amount').val());
        amount = parseFloat(amount);
        total_amount = total_amount + amount;
        $('#total_amount').val(total_amount);
        $('.total_amount').text(__currency_trans_from_en(total_amount, false))

    });

    $('.cancel_btn').click(function(){
        $('#block_id').val('').trigger('change');
        $('#contact_id').val('').trigger('change');
        $('#property_id').val('').trigger('change');
        $('#land_name').val('');
        $('#total_block_value').val('');
        $('#unit').val('');
        $('#block_value').val('');
        $('#block_extent').val('');
        $('#land_name').val('');
        $('.payment_data').empty();
        $('#payment_table tbody').empty();
        $('.block_data').empty();
        $('#block_table tbody').empty();
        $('#total_amount').val(0);
        $('.total_amount').text(__currency_trans_from_en(0, false))
    })
    $('.add_block_purchase').click(function(){
        let property_id = $('#property_id').val();
        let property_name = $('#property_id option:selected').text();
        let block_number = $('#block_id option:selected').text();
        let date = $('#date').val();
        let block_id = $('#block_id').val();
        let unit = $('#unit').val();
        let block_value = __number_uf($('#block_value').val());
        let size = $('#block_extent').val();
        let total_block_value = __number_uf($('#total_block_value').val());
        let block_row_id = parseInt($('#block_row_id').val());

        //tr remove
        $("#block_table").on("click", ".remove", function(event) {
            rowindex = $(this).closest('tr').index();
            $(this).closest("tr").remove();
            let total_block_value = __number_uf($('#total_block_value').val());
            let remove_block_value = __number_f($('.block_value').val());
            let curent_block_value = Number(total_block_value) - Number(remove_block_value);
        });



        $('#block_table tbody').append(
            `<tr>
                <td>${block_row_id+1}</td>
                <td>${block_number}</td>
                <td>${size}</td>
                <td>${unit}</td>
                <td>${__number_f(block_value)}</td>
                <td class="text-center"><button type="button" class="btn btn-danger btn-xs remove"><i class="glyphicon glyphicon-remove"></i></button></td>
            </tr>`
        )
        $('.block_data').append(
            `
            <input type="hidden" name="sell_line[${block_row_id}][date]" value="${date}">
            <input type="hidden" name="sell_line[${block_row_id}][property_id]" value="${property_id}">
            <input type="hidden" name="sell_line[${block_row_id}][block_id]" value="${block_id}">
            <input type="hidden" name="sell_line[${block_row_id}][block_number]" value="${block_number}">
            <input type="hidden" name="sell_line[${block_row_id}][unit]" value="${unit}">
            <input type="hidden" name="sell_line[${block_row_id}][size]" value="${size}">
            <input type="hidden" class="block_value" name="sell_line[${block_row_id}][block_value]" value="${block_value}">
            `
        )
        $('#block_id :eq(0)').prop('selected', true);
        $('#block_row_id').val(block_row_id+1);
        let final_total = parseFloat($('#final_total').val());
        final_total = final_total + block_value;
        $('#total_block_value').val(__number_f(final_total));
        $('#final_total').val(final_total);
    })

    $(document).ready(function(){
        $('#property_id').trigger('change');
    })
    $('#property_id').change(function(){
        property_id = $(this).val();
        property_name = $('#property_id :selected').text();
        $('#land_name').val(property_name);


        $.ajax({
            method: 'get',
            url: '/property/property-blocks/get-block-dropdown/' + property_id,
            data: {  },
            success: function(result) {
                if(result.success){
                    $('#block_id').empty().append(result.data);
                }
            },
        });
    })


    $(document).on('click', '.add_new_customer', function() {
        $('.contact_modal')
            .find('select#contact_type')
            .val('customer')
            .closest('div.contact_type_div')
            .addClass('hide');
        $('.contact_modal').modal('show');
    });

        
    $('form#quick_add_contact')
        .submit(function (e) {
            e.preventDefault();
        })
        .validate({
            rules: {
                contact_id: {
                    remote: {
                        url: '/contacts/check-contact-id',
                        type: 'post',
                        data: {
                            contact_id: function () {
                                return $('#contact_id').val();
                            },
                            hidden_id: function () {
                                if ($('#hidden_id').length) {
                                    return $('#hidden_id').val();
                                } else {
                                    return '';
                                }
                            },
                        },
                    },
                },
            },
            messages: {
                contact_id: {
                    remote: LANG.contact_id_already_exists,
                },
            },
            submitHandler: function (form) {
                $(form).find('button[type="submit"]').attr('disabled', true);
                var data = $(form).serialize();
                $.ajax({
                    method: 'POST',
                    url: $(form).attr('action'),
                    dataType: 'json',
                    data: data,
                    success: function (result) {
                        if (result.success == true) {
                            $('select#contact_id').append(
                                $('<option>', { value: result.data.id, text: result.data.name })
                            );
                            $('select#contact_id').val(result.data.id).trigger('change');
                            $('div.contact_modal').modal('hide');
                            toastr.success(result.msg);
                        } else {
                            toastr.error(result.msg);
                        }
                    },
                });
            },
        });
    $('.contact_modal').on('hidden.bs.modal', function () {
        $('form#quick_add_contact').find('button[type="submit"]').removeAttr('disabled');
        $('form#quick_add_contact')[0].reset();
    });

    // $('form#sell_land_form').on('submit', function (e) {
    //     e.preventDefault();
    //     $(this).validate();
    //     if($(this).valid()){
    //         data = $(this).serialize();
    //         console.log(data);
    //         $.ajax({
    //             method: 'POST',
    //             url: $('form#sell_land_form').attr('action'),
    //             data: data,
    //             success: function (result) {
    //                 // alert();
    //                     // console.log(result);
    //                     $('#invoice_print').html(result);

    //                     var divToPrint = document.getElementById('invoice_print');

    //                     var newWin = window.open('', '_parent');

    //                     newWin.document.open();

    //                     newWin.document.write(
    //                         '<html><body onload="window.print()">' + divToPrint.innerHTML + '</body></html>'
    //                     );

    //                     newWin.document.close();
                    
    //             },
    //         });
    //     }
    // })

</script>
@endsection