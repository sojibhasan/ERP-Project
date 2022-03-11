//meter sale tab
$('#pump_operator_id').change(function () {
    if ($(this).val() === '' || $(this).val() === undefined) {
        toastr.error('Please Select the Pump operator and continue');
    } else {
        $('#below_box *').attr('disabled', false);
    }
});
$(document).ready(function () {
    if ($('#pump_operator_id').val() === '' || $('#pump_operator_id').val() === undefined) {
        $('#below_box *').attr('disabled', true);
    } else {
        $('#below_box *').attr('disabled', false);
    }
});

var tank_qty = 0;
var code = '';
var price = 0.0;
var product_name = '';
var pump_name = '';
var pump_closing_meter = 0.0;
var pump_starting_meter = 0.0;
var meter_sale_total = parseFloat($('#meter_sale_total').val());
var product_id = null;
var pump_id = null;

$('#pump_no').change(function () {
    pump_closing_meter = 0.0;
    pump_starting_meter = 0.0;

    $.ajax({
        method: 'get',
        url: '/petro/settlement/get-pump-details/' + $(this).val(),
        data: {},
        success: function (result) {
            $('#pump_starting_meter').val(result.colsing_value);
            pump_starting_meter = result.colsing_value;
            tank_qty = result.tank_remaing_qty;
            code = result.product.sku;
            price = result.product.default_sell_price;
            product_name = result.product.name;
            pump_name = result.pump_name;
            pump_id = result.pump_id;
            product_id = result.product_id;
            if (result.bulk_sale_meter == '1') {
                $('#bulk_sale_meter').val(1);
                $('.pump_starting_meter_div').addClass('hide');
                $('.pump_closing_meter_div').addClass('hide');
                $('#sold_qty').prop('disabled', false);
            } else {
                $('#bulk_sale_meter').val(0);
                $('.pump_starting_meter_div').removeClass('hide');
                $('.pump_closing_meter_div').removeClass('hide');
                $('#sold_qty').prop('disabled', true);
            }
            $('#meter_sale_unit_price').val(price);
        },
    });
});

$('#pump_closing_meter').change(function () {
    pump_closing_meter = parseFloat($(this).val());
    pump_starting_meter = parseFloat($('#pump_starting_meter').val());
    sold_qty = (pump_closing_meter - pump_starting_meter).toFixed(6);

    console.log(pump_closing_meter);
    console.log(pump_starting_meter);
    if (pump_closing_meter < pump_starting_meter) {
        toastr.error('Closing meter value should not less then starting meter value');
        $(this).val('');
    } else if (sold_qty > tank_qty) {
        toastr.error('Out of Stock');
        $(this).val('');
    } else {
        $('#sold_qty').val(sold_qty);
    }
});

$('.btn_meter_sale').click(function () {
    var testing_qty = $('#testing_qty').val();
    var meter_sale_discount = $('#meter_sale_discount').val();
    var meter_sale_discount_type = $('#meter_sale_discount_type').val();
    var sold_qty = parseFloat($('#sold_qty').val()) - parseFloat(testing_qty);
    
    sub_total = parseFloat(sold_qty) * parseFloat(price);
    var meter_sale_discount_amount = calculate_discount(meter_sale_discount_type, meter_sale_discount, sub_total);
    sub_total = sub_total - meter_sale_discount_amount;
    var meter_sale_id = null;

    let sub = parseFloat(sub_total);
    let meter_sale_total = parseFloat($('#meter_sale_total').val().replace(',', ''));
    meter_sale_total = meter_sale_total + sub;
    $.ajax({
        method: 'post',
        url: '/petro/settlement/save-meter-sale',
        data: {
            settlement_no: $('#settlement_no').val(),
            location_id: $('#location_id').val(),
            pump_operator_id: $('#pump_operator_id').val(),
            transaction_date: $('#transaction_date').val(),
            work_shift: $('#work_shift').val(),
            note: $('#note').val(),
            pump_id: pump_id,
            starting_meter: pump_starting_meter,
            closing_meter: $('#pump_closing_meter').val(),
            product_id: product_id,
            price: price,
            qty: sold_qty,
            discount: meter_sale_discount,
            discount_type: meter_sale_discount_type,
            discount_amount: meter_sale_discount_amount,
            testing_qty: testing_qty,
            sub_total: sub,
        },
        success: function (result) {
            if (!result.success) {
                toastr.error('Something went wrong');
                return false;
            }

            $('#meter_sale_total').val(meter_sale_total);

            $('#pump_no')
                .find('option[value=' + pump_id + ']')
                .remove();

            meter_sale_id = result.meter_sale_id;
            meter_sale_totals = __number_f(sub_total);
            sold_qty = (sold_qty);
            $('#meter_sale_table tbody').prepend(
                `
                <tr> 
                    <td>` +
                    code +
                    `</td>
                    <td>` +
                    product_name +
                    `</td>
                    <td>` +
                    pump_name +
                    `</td>
                    <td>` +
                    pump_starting_meter +
                    `</td>
                    <td>` +
                    pump_closing_meter +
                    `</td>
                    <td>` +
                    __number_f(price) +
                    `</td>
                    <td>` +
                    sold_qty +
                    `</td>
                    <td>` +
                    meter_sale_discount +
                    `</td>
                    <td>` +
                    testing_qty +
                    `</td>
                    <td>` +
                    meter_sale_totals +
                    `</td>
                    <td><button class="btn btn-xs btn-danger delete_meter_sale" data-href="/petro/settlement/delete-meter-sale/` +
                    meter_sale_id +
                    `"><i class="fa fa-times"></i></button>
                    </td>
                </tr>
            `
            );
            $('.meter_sale_fields').val('');
            $('.testing_qty').val(0);
            calculate_payment_tab_total();
        },
    });
});

function calculate_discount(discount_type, discount_value , amount){
    if(discount_type == 'fixed'){
        return discount_value;
    }
    if(discount_type == 'percentage'){
        return (amount * discount_value) / 100;
    }
    return 0;
}

$(document).on('click', '.delete_meter_sale', function () {
    url = $(this).data('href');
    tr = $(this).closest('tr');
    $.ajax({
        method: 'delete',
        url: url,
        data: {},
        success: function (result) {
            if (result.success) {
                toastr.success(result.msg);
                tr.remove();
                let meter_sale_total =
                    parseFloat($('#meter_sale_total').val()) - parseFloat(result.amount);
                meter_sale_total_text = __number_f(
                    meter_sale_total,
                    false,
                    false,
                    __currency_precision
                );
                $('.meter_sale_total').text(meter_sale_total_text);
                $('#meter_sale_total').val(meter_sale_total);
                $('#pump_no').append(
                    `<option value="` + result.pump_id + `">` + result.pump_name + `</option>`
                );
                calculate_payment_tab_total();
            } else {
                toastr.error(result.msg);
            }
        },
    });
});

//other sale tab
other_sale_code = null;
other_sale_product_name = null;
other_sale_price = 0.0;
other_sale_qty = 0.0;
other_sale_discount = 0.0;
other_sale_total = parseFloat($('#other_sale_total').val());
$('#item').change(function () {
    let item_id = $(this).val();
    $.ajax({
        method: 'get',
        url: '/petro/settlement/get_balance_stock/' + item_id,
        data: {},
        success: function (result) {
            $('#balance_stock').val(result.balance_stock);
            $('#other_sale_price').val(result.price);
            other_sale_code = result.code;
            other_sale_product_name = result.product_name;
            other_sale_price = result.price;
        },
    });
});

$('.btn_other_sale').click(function () {
    var other_sale_discount = $('#other_sale_discount').val();
    var other_sale_discount_type = $('#other_sale_discount_type').val();
    var other_sale_qty = $('#other_sale_qty').val();
    var balance_stock = $('#balance_stock').val();
    sub_total = parseFloat(other_sale_qty) * parseFloat(other_sale_price);
    var other_sale_discount_amount = calculate_discount(other_sale_discount_type, other_sale_discount, sub_total);
    sub_total = sub_total - other_sale_discount_amount;
    var other_sale_id = null;

    let sub = parseFloat(sub_total);
    let other_sale_total = parseFloat($('#other_sale_total').val().replace(',', ''));
    other_sale_total = other_sale_total + sub;

    $.ajax({
        method: 'post',
        url: '/petro/settlement/save-other-sale',
        data: {
            settlement_no: $('#settlement_no').val(),
            location_id: $('#location_id').val(),
            pump_operator_id: $('#pump_operator_id').val(),
            transaction_date: $('#transaction_date').val(),
            work_shift: $('#work_shift').val(),
            note: $('#note').val(),
            product_id: $('#item').val(), //item is product in whole page
            store_id: $('#store_id').val(),
            price: other_sale_price,
            qty: other_sale_qty,
            balance_stock: balance_stock,
            discount: other_sale_discount,
            discount_type: other_sale_discount_type,
            discount_amount: other_sale_discount_amount,
            sub_total: sub,
        },
        success: function (result) {
            if (!result.success) {
                toastr.error('Something went wrong');
                return false;
            }
            $('#other_sale_total').val(other_sale_total);
            
            other_sale_id = result.other_sale_id;
            sub_total = __number_f(sub_total);
            $('#other_sale_table tbody').prepend(
                `
                <tr> 
                    <td>` +
                    other_sale_code +
                    `</td>
                    <td>` +
                    other_sale_product_name +
                    `</td>
                    <td>` +
                    balance_stock +
                    `</td>
                    <td>` +
                    __number_f(other_sale_price) +
                    `</td>
                    <td>` +
                    other_sale_qty +
                    `</td>
                    <td>` +
                    other_sale_discount +
                    `</td>
                    <td>` +
                    sub_total +
                    `</td>
                    <td><button class="btn btn-xs btn-danger delete_other_sale" data-href="/petro/settlement/delete-other-sale/` +
                    other_sale_id +
                    `"><i class="fa fa-times"></i></button>
                    </td>
                </tr>
            `
            );
            $('.other_sale_fields').val('').trigger('change');
            calculate_payment_tab_total();
        },
    });
});

$(document).on('click', '.delete_other_sale', function () {
    url = $(this).data('href');
    tr = $(this).closest('tr');
    $.ajax({
        method: 'delete',
        url: url,
        data: {},
        success: function (result) {
            if (result.success) {
                toastr.success(result.msg);
                tr.remove();
                let other_sale_total =
                    parseFloat($('#other_sale_total').val().replace(',', '')) -
                    parseFloat(result.amount);
                other_sale_total_text = __number_f(
                    other_sale_total,
                    false,
                    false,
                    __currency_precision
                );
                $('.other_sale_total').text(other_sale_total_text);
                $('#other_sale_total').val(other_sale_total);
                calculate_payment_tab_total();
            } else {
                toastr.error(result.msg);
            }
        },
    });
});

//other income tab
var other_income_total = parseFloat($('#other_income_total').val().replace(',', ''));
var sub_total = 0.0;
var other_income_code = null;
var other_income_product_name = null;
var other_income_price = 0.0;

$('.btn_other_income').click(function () {
    var other_income_product_id = $('#other_income_product_id').val();
    var other_income_qty = $('#other_income_qty').val();
    var other_income_reason = $('#other_income_reason').val();
    var other_income_id = null;
    other_income_price = parseFloat($('#other_income_price').val());

    var other_income_amount = parseFloat(other_income_qty) * other_income_price;

    let other_income_total = parseFloat($('#other_income_total').val().replace(',', ''));
    other_income_total = other_income_total + other_income_amount;
    $('#other_income_total').val(other_income_total);
    $.ajax({
        method: 'post',
        url: '/petro/settlement/save-other-income',
        data: {
            settlement_no: $('#settlement_no').val(),
            location_id: $('#location_id').val(),
            pump_operator_id: $('#pump_operator_id').val(),
            transaction_date: $('#transaction_date').val(),
            work_shift: $('#work_shift').val(),
            note: $('#note').val(),
            product_id: other_income_product_id,
            qty: other_income_qty,
            price: other_income_price,
            other_income_reason: other_income_reason,
            sub_total: other_income_amount,
        },
        success: function (result) {
            if (!result.success) {
                toastr.error('Something went wrong');
                return false;
            }

            other_income_id = result.other_income_id;
            other_income_sub_total = __number_f(other_income_amount);
            $('#other_income_table tbody').prepend(
                `
                <tr> 
                    <td>` +
                    other_income_product_name +
                    `</td>
                    <td>` +
                    __number_f(other_income_qty) +
                    `</td>
                    <td>` +
                    other_income_reason +
                    `</td>
                    <td>` +
                    other_income_sub_total +
                    `</td>
                    <td><button class="btn btn-xs btn-danger delete_other_income" data-href="/petro/settlement/delete-other-income/` +
                    other_income_id +
                    `"><i class="fa fa-times"></i></button>
                    </td>
                </tr>
            `
            );
            $('.other_income_fields').val('').trigger('change');
            calculate_payment_tab_total();
        },
    });
});
$('#other_income_product_id').change(function () {
    let item_id = $(this).val();
    $.ajax({
        method: 'get',
        url: '/petro/settlement/get_balance_stock/' + item_id,
        data: {},
        success: function (result) {
            other_income_code = result.code;
            other_income_product_name = result.product_name;
            other_income_price = result.price;
            $('#other_income_price').val(__number_f(other_income_price));
        },
    });
});

$(document).on('click', '.delete_other_income', function () {
    url = $(this).data('href');
    tr = $(this).closest('tr');
    $.ajax({
        method: 'delete',
        url: url,
        data: {},
        success: function (result) {
            if (result.success) {
                toastr.success(result.msg);
                tr.remove();
                let other_income_total =
                    parseFloat($('#other_income_total').val()) - parseFloat(result.sub_total);
                other_income_total_text = __number_f(
                    other_income_total,
                    false,
                    false,
                    __currency_precision
                );
                $('.other_income_total').text(other_income_total_text);
                $('#other_income_total').val(other_income_total);
                calculate_payment_tab_total();
            } else {
                toastr.error(result.msg);
            }
        },
    });
});
//customer_payment tab
var customer_payment_total = parseFloat($('#customer_payment_total').val().replace(',', ''));
var sub_total = 0.0;

$('.btn_customer_payment').click(function () {
    var customer_payment_amount = parseFloat($('#customer_payment_amount').val());
    var customer_name = $('#customer_payment_customer_id :selected').text();
    var payment_method = $('#customer_payment_payment_method').val();
    var bank_name = $('#customer_payment_bank_name').val();
    var cheque_date = $('#customer_payment_cheque_date').val();
    var cheque_number = $('#customer_payment_cheque_number').val();
    var customer_payment_id = null;

    let customer_payment_total = parseFloat($('#customer_payment_total').val().replace(',', ''));
    customer_payment_total = customer_payment_total + customer_payment_amount;
    $('#customer_payment_total').val(customer_payment_total);

    $.ajax({
        method: 'post',
        url: '/petro/settlement/save-customer-payment',
        data: {
            settlement_no: $('#settlement_no').val(),
            location_id: $('#location_id').val(),
            pump_operator_id: $('#pump_operator_id').val(),
            transaction_date: $('#transaction_date').val(),
            work_shift: $('#work_shift').val(),
            note: $('#note').val(),

            customer_id: $('#customer_payment_customer_id').val(),
            payment_method: $('#customer_payment_payment_method').val(),
            bank_name: bank_name,
            cheque_date: cheque_date,
            cheque_number: cheque_number,
            amount: customer_payment_amount,
            sub_total: customer_payment_amount,
        },
        success: function (result) {
            if (!result.success) {
                toastr.error('Something went wrong');
                return false;
            }

            customer_payment_id = result.customer_payment_id;
            customer_payment_amount = __number_f(customer_payment_amount);
            $('#customer_payment_table tbody').prepend(
                `
                <tr> 
                    <td>` +
                    customer_name +
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
                    customer_payment_amount +
                    `</td>
                    <td><button class="btn btn-xs btn-danger delete_customer_payment" data-href="/petro/settlement/delete-customer-payment/` +
                    customer_payment_id +
                    `"><i class="fa fa-times"></i></button>
                    </td>
                </tr>
            `
            );
            $('.customer_payment_fields').val('').trigger('change');
            calculate_payment_tab_total();
        },
    });
});

$('#customer_payment_payment_method').change(function () {
    if ($(this).val() == 'cheque') {
        $('.cheque_divs').removeClass('hide');
    } else {
        $('.cheque_divs').addClass('hide');
    }
});

$(document).on('click', '.delete_customer_payment', function () {
    url = $(this).data('href');
    tr = $(this).closest('tr');
    $.ajax({
        method: 'delete',
        url: url,
        data: {},
        success: function (result) {
            if (result.success) {
                toastr.success(result.msg);
                tr.remove();
                let customer_payment_total =
                    parseFloat($('#customer_payment_total').val()) - parseFloat(result.amount);
                customer_payment_total_text = __number_f(
                    customer_payment_total,
                    false,
                    false,
                    __currency_precision
                );
                $('.customer_payment_total').text(customer_payment_total_text);
                $('#customer_payment_total').val(customer_payment_total);
                calculate_payment_tab_total();
            } else {
                toastr.error(result.msg);
            }
        },
    });
});

function calculate_payment_tab_total() {
    let meter_sale_totals = parseFloat($('#meter_sale_total').val());
    let other_sale_totals = parseFloat($('#other_sale_total').val());
    let other_income_totals = parseFloat($('#other_income_total').val());
    let customer_payment_totals = parseFloat($('#customer_payment_total').val());

    let all_totals =
        meter_sale_totals + other_sale_totals + other_income_totals + customer_payment_totals;

    $('.payment_meter_sale_total').text(
        __number_f(meter_sale_totals, false, false, __currency_precision)
    );
    $('.payment_other_sale_total').text(
        __number_f(other_sale_totals, false, false, __currency_precision)
    );
    $('.payment_other_income_total').text(
        __number_f(other_income_totals, false, false, __currency_precision)
    );
    $('.payment_customer_payment_total').text(
        __number_f(customer_payment_totals, false, false, __currency_precision)
    );
    $('.meter_sale_total').text(__number_f(meter_sale_totals, false, false, __currency_precision));
    $('.other_sale_total').text(__number_f(other_sale_totals, false, false, __currency_precision));
    $('.other_income_total').text(
        __number_f(other_income_totals, false, false, __currency_precision)
    );
    $('.customer_payment_total').text(
        __number_f(customer_payment_totals, false, false, __currency_precision)
    );

    $('#payment_due').text(__number_f(all_totals, false, false, __currency_precision));
}
