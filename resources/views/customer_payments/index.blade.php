@extends('layouts.app')
@section('title', __('lang_v1.customer_payments'))
@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1> @lang('lang_v1.customer_payments')
            <small>@lang( 'contact.manage_your_contact', ['contacts' => __('lang_v1.customer_payments') ])</small>
        </h1>
    </section>
    <!-- Main content -->
    <section class="content">
        <div class="settlement_tabs">
            <ul class="nav nav-tabs">
                <li class=" @if(empty(session('status.tab'))) active @endif">
                    <a href="#customer_payment_simple" data-toggle="tab">
                        <i class="fa fa-money"></i> <strong>@lang('lang_v1.customer_payment_simple')</strong>
                    </a>
                </li>
                <li class=" @if(session('status.tab') == 'bulk') active @endif">
                    <a href="#customer_payment_bulk" data-toggle="tab">
                        <i class="fa fa-money"></i> <strong>
                            @lang('lang_v1.customer_payment_bulk') </strong>
                    </a>
                </li>
                <li class=" @if(session('status.tab') == 'customer-payments') active @endif">
                    <a href="#customer_payments" data-toggle="tab">
                        <i class="fa fa-list"></i> <strong>
                            @lang('lang_v1.customer_payments_list') </strong>
                    </a>
                </li>
                <li class=" @if(session('status.tab') == 'customer-interest') active @endif">
                    <a href="#customer_interest" data-toggle="tab">
                        <i class="fa fa-list"></i> <strong>
                            @lang('lang_v1.customer_interest_list') </strong>
                    </a>
                </li>
                <li class=" @if(session('status.tab') == 'interest-settings') active @endif">
                    <a href="#interest_settings" data-toggle="tab">
                        <i class="fa fa-list"></i> <strong>
                            @lang('lang_v1.interest_settings') </strong>
                    </a>
                </li>
            </ul>
            <div class="tab-content">
                <div class="tab-pane @if(empty(session('status.tab'))) active @endif" id="customer_payment_simple">
                    @include('customer_payments.customer_payment_simple')
                </div>
                <div class="tab-pane @if(session('status.tab') == 'bulk') active @endif" id="customer_payment_bulk">
                    @include('customer_payments.customer_payment_bulk')
                </div>
                <div class="tab-pane @if(session('status.tab') == 'customer-payments') active @endif"
                     id="customer_payments">
                    @include('customer_payments.customer_payments')
                </div>
                <div class="tab-pane @if(session('status.tab') == 'customer-interest') active @endif"
                     id="customer_interest">
                    @include('customer_payments.customer_interest')
                </div>
                <div class="tab-pane @if(session('status.tab') == 'interest-settings') active @endif"
                     id="interest_settings">
                    @include('customer_payments.interest_settings')
                </div>
            </div>
        </div>
    </section>
    <!-- /.content -->
<style>
  .nav-tabs-custom>.nav-tabs>li.active a{
    color:#3c8dbc;
  }
  .nav-tabs-custom>.nav-tabs>li.active a:hover{
    color:#3c8dbc;
  }
</style>
@endsection
@section('javascript')
    <script>
        var body = document.getElementsByTagName("body")[0];
        body.className += " sidebar-collapse";
    </script>
    <script>
    
            $('#customer_payment_bulk_payment_method').change(function () {
        group_id = $(this).val();

        $.ajax({
            method: 'get',
            url: '/accounting-module/get-account-by-group-id/'+group_id,
            data: {  },
            contentType: 'html',
            success: function(result) {
                $('#customer_payment_bulk_accounting_module').empty().append(result);
            },
        });
    })
         
    
        //customer_payment simple tab script
        $('.cheque_date').datepicker('setDate', new Date());
        $('.transaction_date').datepicker('setDate', new Date());
        var customer_payment_simple_total = parseFloat($('#customer_payment_simple_total').val().replace(',', ''));
        var sub_total = 0.0;
        $('.btn_customer_payment').click(function () {
            var amount = parseFloat($('#amount').val());
            var customer_name = $('#customer_payment_simple_customer_id :selected').text();
            var customer_id = $('#customer_payment_simple_customer_id').val();
            var payment_method = $('#customer_payment_simple_payment_method').val();
            var bank_name = $('#customer_payment_simple_bank_name').val();
            var payment_ref_no = 'CPS-' + $('#customer_payment_simple_payment_ref_no').val();
            if (payment_method == 'cheque') {
                var cheque_date = $('#customer_payment_simple_cheque_date').val();
            } else {
                var cheque_date = '';
            }
            var cheque_number = $('#customer_payment_simple_cheque_number').val();
            var customer_payment_simple_id = null;
            let row_index = parseInt($('#row_index').val());
            let customer_payment_simple_total = parseFloat($('#customer_payment_simple_total').val().replace(',', ''));
            customer_payment_simple_total = customer_payment_simple_total + amount;
            $('#customer_payment_simple_total').val(customer_payment_simple_total);
            $('.row_data').append(`
        <input type="hidden" name="payment[${row_index}][payment_ref_no]" value="${payment_ref_no}">
        <input type="hidden" name="payment[${row_index}][contact_id]" value="${customer_id}">
        <input type="hidden" name="payment[${row_index}][method]" value="${payment_method}">
        <input type="hidden" name="payment[${row_index}][bank_name]" value="${bank_name}">
        <input type="hidden" name="payment[${row_index}][cheque_date]" value="${cheque_date}">
        <input type="hidden" name="payment[${row_index}][cheque_number]" value="${cheque_number}">
        <input type="hidden" name="payment[${row_index}][amount]" value="${amount}">
    `);
            amount = __number_f(amount);
            $('#customer_payment_simple_table tbody').prepend(
                `
        <tr>
            <td>` +
                customer_name +
                `</td>
            <td>` +
                payment_ref_no +
                `</td>
            <td>` +
                payment_method +
                `</td>
            <td>` +
                bank_name +
                `</td>
            <td>` +
                cheque_date +
                `</td>
            <td>` +
                cheque_number +
                `</td>
            <td>` +
                amount +
                `</td>
            <td><button class="btn btn-xs btn-danger delete_customer_payment_simple"><i class="fa fa-times"></i></button>
            </td>
        </tr>
    `
            );
            $('.customer_payment_simple_fields').val('').trigger('change');
            $('#row_index').val(row_index + 1);
        });
        $('#customer_payment_simple_payment_method').change(function () {
           
            let row = $(this).closest('.payment_data_row')
             if ($(this).val() == 'cheque') {
                row.find('.cheque_divs').removeClass('hide');
                row.find('.card_divs').addClass('hide');
            } else if ($(this).val() == 'card') {
                row.find('.card_divs').removeClass('hide');
                row.find('.cheque_divs').addClass('hide');
            } else {
                row.find('.card_divs').addClass('hide');
                row.find('.cheque_divs').addClass('hide');
            }
        });
        $('#customer_payment_bulk_payment_method').change(function () {
             //alert($(this).find('option:selected').text());
            let row = $(this).closest('.payment_data_row')
            
            if ($(this).find('option:selected').text() == 'Cash') {
                row.find('.cash_divs').removeClass('hide');
                row.find('.card_divs').addClass('hide');
                row.find('.cheque_divs').addClass('hide');
            }else if ($(this).find('option:selected').text() == "Cheque") {
                 row.find('.cash_divs').addClass('hide');
                row.find('.cheque_divs').removeClass('hide');
                row.find('.card_divs').addClass('hide');
            } else if ($(this).find('option:selected').text() == 'Card') {
                 row.find('.cash_divs').addClass('hide');
                row.find('.card_divs').removeClass('hide');
                row.find('.cheque_divs').addClass('hide');
            } else {
                row.find('.card_divs').addClass('hide');
                row.find('.cheque_divs').addClass('hide');
                 row.find('.cash_divs').addClass('hide');
            }
        });
        $(document).on('click', '.delete_customer_payment_simple', function () {
            tr = $(this).closest('tr');
            tr.remove();
        });
    </script>
    {{--  Start By Yusuf   --}}
    <script !src="">
        $('#customer_interest_deduct_option').change(function () {
            if ($(this).val() === 'yes') {
                $('.interest_selection_checkbox').removeClass('hide');
            } else {
                $('.interest_selection_checkbox').addClass('hide');
            }
        });
        function calculate_interest_column_total() {
            let orderTotal = 0;
            $('.interest_column_total').each(function () {
                if (!isNaN(parseFloat($(this).val()))) {
                    orderTotal += parseFloat($(this).val());
                }
            });
            return orderTotal;
        }
        function calculate_amount_column_total() {
            let orderTotal = 0;
            $('.amount_column_total').each(function () {
                if (!isNaN(parseFloat($(this).val()))) {
                    orderTotal += parseFloat($(this).val());
                }
            });
            return orderTotal;
        }
    //     code commented
    //     $(document).on('change', '.interest_selection_checkbox, .amount_selection_box', function () {
    //         let excess_amount = $('#payable_amount').val();
    //         if (excess_amount <= 0) {
    //             toastr.error('Please Enter Payable Amount!');
    //             $(this).prop('checked', false);
    //             return;
    //         }
    //         let interest_column_ttl = calculate_interest_column_total();
    //         let amount_column_ttl = calculate_amount_column_total();
    //         //Excess Amount =  Payable amount – (Interest amount [Column Total Amount] + Amount Pay now Column Total [Except Excess Amount Row])
    //         let payable_amount = $('#payable_amount').val();
    //         let excess_ttl = payable_amount - (interest_column_ttl + amount_column_ttl)
    //         $('#excess_amount').val(excess_ttl);
    //     });
    // </script>
    // {{--  End By Yusuf   --}}
    <script>
        //customer payment bulk script
        $(document).ready(function () {
            $('#customer_payment_bulk_customer_id').trigger('change');
        })
        $('#customer_payment_bulk_customer_id').change(function () {
            $.ajax({
                method: 'get',
                url: '/petro/settlement/payment/get-customer-details/' + $(this).val(),
                data: {},
                success: function (result) {
                    __write_number($('#customer_payment_bulk_balance'), result.total_outstanding);
                },
            });
            $.ajax({
                method: 'get',
                url: '/customer-payment-bulk/get-payment-table?customer_id=' + $(this).val(),
                data: {},
                contentType: 'html',
                success: function (result) {
                    $('#payment_bulk_invoice_table tbody').empty().append(result);
                    $('#payable_amount').val('');
                },
            });
        });
        
        $(document).on('click', '.paying_checkbox', function () {
            let transactionId = $(this).data('id');
            let outstandingAmount = parseFloat($('#outstanding_amount_'+transactionId).val());
            let interestAmount = parseFloat($('#interest_amount_'+transactionId).val());
            let totalAmount = parseFloat($('#total_amount_'+transactionId).val());
            if(!isNaN(totalAmount)) {
                if(!isNaN(interestAmount)) {
                    totalAmount -= interestAmount;
                }
            } else {
                totalAmount = outstandingAmount;
                if(!isNaN(interestAmount)) {
                    totalAmount += interestAmount;
                }
            }
            if($(this).is(":checked")) {
                $('#total_amount_'+transactionId).val(totalAmount.toFixed(0));
            } else {
                $('#total_amount_'+transactionId).val('');
            }
            let access_amount = parseFloat($('#payable_amount').val());
            $('.amount_column_total').each(function(i, input) {
                let inputVal = $(this).val();
                if(!isNaN(inputVal)) {
                    access_amount -= inputVal;
                }
            });

           
            access_amount = parseFloat(balance) - access_amount;
            // alert(access_amount);
            calculate_excess_amount();
            // $('#excess_amount').val(access_amount);
        });
        // code commented
        // $(document).on('click', '.paying_checkbox', function () {
        //     let excess_amount = parseFloat($('#excess_amount').val());
        //     if (excess_amount == 0) {
        //         toastr.error('Please enter Payable Amount!');
        //         $(this).prop('checked', false);
        //         return;
        //     }
        //     let id = $(this).data('id');
        //     let outstanding = parseFloat($('.outstanding_' + id).val());
        //     console.log(outstanding);
        //     if (excess_amount < outstanding) {
        //         outstanding = excess_amount;
        //     }
        //     $('.amount_' + id).val(outstanding);
        //     calculate_excess_amount();
        // });
         var  Gtotal_paying = 0;   
        function calculate_excess_amount() {
            let total_paying = 0;
            $('.paying_checkbox').each(function () {
                if ($(this).prop('checked') === true) {
                    let id = $(this).data('id');
                    let this_amount = parseFloat($('.amount_' + id).val());
                    total_paying += this_amount;
                }
            })

            let payable_amount = parseFloat($('#customer_payment_bulk_balance').val());
            
            // let payable_amount = parseFloat($('#payable_amount').val());
            excess_amount = payable_amount - total_paying;
            Gtotal_paying = excess_amount;
            alert(payable_amount);
            $('#excess_amount').val(excess_amount);
        }

        $('#payable_amount').change(function () {
            let amount = parseFloat($(this).val())
            calculate_excess_amount();
            if(Gtotal_paying > 0){
                $('#excess_amount').val(Gtotal_paying);    
            }else{

            $('#excess_amount').val(amount);
            }
        });
        $(document).on('click', '.pay_all', function () {
            if ($(this).prop('checked') === true) {
                $('.paying_checkbox').each(function () {
                    let excess_amount = parseFloat($('#excess_amount').val());
                    if (excess_amount == 0) {
                        return;
                    }
                    $(this).prop('checked', true);
                    let id = $(this).data('id');
                    let outstanding = parseFloat($('.outstanding_' + id).val());
                    if (excess_amount < outstanding) {
                        outstanding = excess_amount;
                    }
                    excess_amount -= outstanding;
                    $('#excess_amount').val(excess_amount.toFixed(2));
                    $('.amount_' + id).val(outstanding.toFixed(2));
                })
            }
        });
        //Date range as a button
        $('#customer_payment_date_range').daterangepicker(
            dateRangeSettings,
            function (start, end) {
                $('#customer_payment_date_range').val(start.format(moment_date_format) + ' ~ ' + end.format(moment_date_format));
                customer_payments.ajax.reload();
            }
        );
        $('#customer_payment_date_range').on('cancel.daterangepicker', function (ev, picker) {
            $('#customer_payment_date_range').val('');
            customer_payments.ajax.reload();
        });
        $(document).ready(function () {
            customer_payments = $('#interest_settings_table').DataTable({
                processing: true,
                serverSide: true,
                aaSorting: [[1, 'desc']],
                "ajax": {
                    "url": "/interest-settings",
                    "data": function (d) {
                    }
                },
                columns: [
                    {data: 'date', name: 'interest_settings.date', searchable: false},
                    {data: 'contact_group', name: 'contact_groups.name'},
                    {data: 'account', name: 'accounts.name'},
                    {data: 'username', name: 'users.username'}
                ]
            });
            customer_payments = $('#customer_payments_table').DataTable({
                processing: true,
                serverSide: true,
                aaSorting: [[1, 'desc']],
                "ajax": {
                    "url": "/customer-payments",
                    "data": function (d) {
                        if ($('#customer_payment_date_range').val()) {
                            var start = $('#customer_payment_date_range').data('daterangepicker').startDate.format('YYYY-MM-DD');
                            var end = $('#customer_payment_date_range').data('daterangepicker').endDate.format('YYYY-MM-DD');
                            d.start_date = start;
                            d.end_date = end;
                        }
                        d.location_id = $('#customer_payment_location_id').val();
                        d.customer_id = $('#customer_payment_customer_id').val();
                        d.payment_method = $('#customer_payment_method').val();
                        d.paid_in_type = $('#paid_in_type option:selected').val();
                    }
                },
                columns: [
                    {data: 'action', name: 'action', searchable: false},
                    {data: 'paid_on', name: 'tp.paid_on'},
                    {data: 'location_name', name: 'business_locations.name'},
                    {data: 'name', name: 'contacts.name'},
                    {data: 'interest', name: 'act.interest'},
                    {data: 'total_paid', name: 'total_paid', searchable: false},
                    {data: 'method', name: 'tp.method'},
                    {data: 'paid_in_type', name: 'tp.paid_in_type'},
                    {data: 'username', name: 'users.username'},
                ],
                "fnDrawCallback": function (oSettings) {
                    __currency_convert_recursively($('#customer_payments_table'));
                },
            });
        });
        $('#customer_interest_date_range').daterangepicker(
            dateRangeSettings,
            function (start, end) {
                $('#customer_interest_date_range').val(start.format(moment_date_format) + ' ~ ' + end.format(moment_date_format));
                customer_payments.ajax.reload();
            }
        );
        $('#customer_interest_date_range').on('cancel.daterangepicker', function (ev, picker) {
            $('#customer_interest_date_range').val('');
            customer_payments.ajax.reload();
        });
        $(document).ready(function () {
            customer_payments = $('#customer_interest_table').DataTable({
                processing: true,
                serverSide: true,
                aaSorting: [[1, 'desc']],
                "ajax": {
                    "url": "/customer-interest",
                    "data": function (d) {
                        if ($('#customer_interest_date_range').val()) {
                            var start = $('#customer_interest_date_range').data('daterangepicker').startDate.format('YYYY-MM-DD');
                            var end = $('#customer_interest_date_range').data('daterangepicker').endDate.format('YYYY-MM-DD');
                            d.start_date = start;
                            d.end_date = end;
                        }
                        d.location_id = $('#customer_payment_location_id').val();
                        d.customer_id = $('#customer_payment_customer_id').val();
                        d.payment_method = $('#customer_payment_method').val();
                        d.paid_in_type = $('#paid_in_type option:selected').val();
                    }
                },
                columns: [
                    {data: 'action', name: 'action', searchable: false},
                    {data: 'paid_on', name: 'tp.paid_on'},
                    {data: 'location_name', name: 'business_locations.name'},
                    {data: 'name', name: 'contacts.name'},
                    {data: 'interest', name: 'act.interest'},
                    {data: 'total_paid', name: 'total_paid', searchable: false},
                    {data: 'method', name: 'tp.method'},
                    {data: 'paid_in_type', name: 'tp.paid_in_type'},
                    {data: 'username', name: 'users.username'},
                ],
                "fnDrawCallback": function (oSettings) {
                    __currency_convert_recursively($('#customer_payments_table'));
                },
            });
        });
        $('#customer_payment_date_range, #customer_payment_location_id, #customer_payment_customer_id, #customer_payment_method, #paid_in_type, #customer_interest_date_range').change(function () {
            customer_payments.ajax.reload();
        });
    </script>
@endsection
