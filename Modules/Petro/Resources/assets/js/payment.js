$(document).on("click", ".cash_add", function () {
    if ($("#cash_amount").val() == "") {
        toastr.error("Please enter amount");
        return false;
    }
    var cash_customer_id = $("#cash_customer_id").val();
    var cash_amount = $("#cash_amount").val();
    var settlement_no = $("#settlement_no").val();
    var customer_name = $("#cash_customer_id :selected").text();
    $.ajax({
        method: "post",
        url: "/petro/settlement/payment/save-cash-payment",
        data: {
            customer_id: cash_customer_id,
            amount: cash_amount,
            settlement_no: settlement_no,
        },
        success: function (result) {
            if (!result.success) {
                toastr.error(result.msg);
            } else {
                settlement_cash_payment_id = result.settlement_cash_payment_id;
                add_payment(cash_amount);
                $("#cash_table tbody").prepend(
                    `
                    <tr> 
                        <td>` +
                        customer_name +
                        `</td>
                        <td class="cash_amount">` +
                        __number_f(cash_amount, false, false, __currency_precision) +
                        `</td>
                        <td><button type="button" class="btn btn-xs btn-danger delete_cash_payment" data-href="/petro/settlement/payment/delete-cash-payment/` +
                        settlement_cash_payment_id +
                        `"><i class="fa fa-times"></i></button>
                        </td>
                    </tr>
                `
                );
                $(".cash_fields").val("");
                calculateTotal("#cash_table", ".cash_amount", ".cash_total");
            }
        },
    });
});
$(document).on("click", ".delete_cash_payment", function () {
    url = $(this).data("href");
    tr = $(this).closest("tr");
    $.ajax({
        method: "delete",
        url: url,
        data: {},
        success: function (result) {
            if (result.success) {
                toastr.success(result.msg);
                tr.remove();
                let this_amount = result.amount;
                delete_payment(this_amount);
                calculateTotal("#cash_table", ".cash_amount", ".cash_total");
            } else {
                toastr.error(result.msg);
            }
        },
    });
});
//card payments
$(document).on("click", ".card_add", function () {
    if ($("#card_amount").val() == "") {
        toastr.error("Please enter amount");
        return false;
    }
    var card_customer_id = $("#card_customer_id").val();
    var customer_name = $("#card_customer_id :selected").text();
    var card_amount = $("#card_amount").val();
    var settlement_no = $("#settlement_no").val();
    var card_type = $("#card_type :selected").text();
    var card_type_id = $("#card_type").val();
    var card_number = $("#card_number").val();
    $.ajax({
        method: "post",
        url: "/petro/settlement/payment/save-card-payment",
        data: {
            customer_id: card_customer_id,
            amount: card_amount,
            card_type: card_type_id,
            card_number: card_number,
            settlement_no: settlement_no,
        },
        success: function (result) {
            if (!result.success) {
                toastr.error(result.msg);
            } else {
                settlement_card_payment_id = result.settlement_card_payment_id;
                add_payment(card_amount);
                $("#card_table tbody").prepend(
                    `
                    <tr> 
                        <td>` +
                        customer_name +
                        `</td>
                        <td>` +
                        card_type +
                        `</td>
                        <td>` +
                        card_number +
                        `</td>
                        <td class="card_amount">` +
                        __number_f(card_amount, false, false, __currency_precision) +
                        `</td>
                        <td><button type="button" class="btn btn-xs btn-danger delete_card_payment" data-href="/petro/settlement/payment/delete-card-payment/` +
                        settlement_card_payment_id +
                        `"><i class="fa fa-times"></i></button>
                        </td>
                    </tr>
                `
                );
                $(".card_fields").val("");
                calculateTotal("#card_table", ".card_amount", ".card_total");
            }
        },
    });
});
$(document).on("click", ".delete_card_payment", function () {
    url = $(this).data("href");
    tr = $(this).closest("tr");
    $.ajax({
        method: "delete",
        url: url,
        data: {},
        success: function (result) {
            if (result.success) {
                toastr.success(result.msg);
                tr.remove();
                let this_amount = result.amount;
                delete_payment(this_amount);
                calculateTotal("#card_table", ".card_amount", ".card_total");
            } else {
                toastr.error(result.msg);
            }
        },
    });
});
//cheque payments
$(document).on("click", ".cheque_add", function () {
    if ($("#cheque_amount").val() == "") {
        toastr.error("Please enter amount");
        return false;
    }
    var cheque_customer_id = $("#cheque_customer_id").val();
    var customer_name = $("#cheque_customer_id :selected").text();
    var cheque_amount = $("#cheque_amount").val();
    var settlement_no = $("#settlement_no").val();
    var cheque_date = $("#cheque_date").val();
    var bank_name = $("#bank_name").val();
    var cheque_number = $("#cheque_number").val();
    $.ajax({
        method: "post",
        url: "/petro/settlement/payment/save-cheque-payment",
        data: {
            customer_id: cheque_customer_id,
            amount: cheque_amount,
            bank_name: bank_name,
            cheque_date: cheque_date,
            cheque_number: cheque_number,
            settlement_no: settlement_no,
        },
        success: function (result) {
            if (!result.success) {
                toastr.error(result.msg);
            } else {
                settlement_cheque_payment_id = result.settlement_cheque_payment_id;
                add_payment(cheque_amount);
                $("#cheque_table tbody").prepend(
                    `
                    <tr> 
                        <td>` +
                        customer_name +
                        `</td>
                        <td>` +
                        bank_name +
                        `</td>
                        <td>` +
                        cheque_number +
                        `</td>
                        <td>` +
                        cheque_date +
                        `</td>
                        <td class="cheque_amount">` +
                        __number_f(cheque_amount, false, false, __currency_precision) +
                        `</td>
                        <td><button type="button" class="btn btn-xs btn-danger delete_cheque_payment" data-href="/petro/settlement/payment/delete-cheque-payment/` +
                        settlement_cheque_payment_id +
                        `"><i class="fa fa-times"></i></button>
                        </td>
                    </tr>
                `
                );
                $(".cheque_fields").val("");
                calculateTotal("#cheque_table", ".cheque_amount", ".cheque_total");
            }
        },
    });
});
$(document).on("click", ".delete_cheque_payment", function () {
    url = $(this).data("href");
    tr = $(this).closest("tr");
    $.ajax({
        method: "delete",
        url: url,
        data: {},
        success: function (result) {
            if (result.success) {
                toastr.success(result.msg);
                tr.remove();
                let this_amount = result.amount;
                delete_payment(this_amount);
                calculateTotal("#cheque_table", ".cheque_amount", ".cheque_total");
            } else {
                toastr.error(result.msg);
            }
        },
    });
});
//credit_sale payments
$(document).on("click", ".credit_sale_add", function () {
    if ($("#credit_sale_amount").val() == "") {
        toastr.error("Please enter amount");
        return false;
    }
    var credit_sale_customer_id = $("#credit_sale_customer_id").val();
    var customer_name = $("#credit_sale_customer_id :selected").text();
    var credit_sale_product_id = $("#credit_sale_product_id").val();
    var credit_sale_product_name = $("#credit_sale_product_id :selected").text();
    if ($("#customer_reference_one_time").val() !== "" && $("#customer_reference_one_time").val() !== null && $("#customer_reference_one_time").val() !== undefined) {
        var customer_reference = $("#customer_reference_one_time").val();
    } else {
        var customer_reference = $("#customer_reference").val();
    }
    var settlement_no = $("#settlement_no").val();
    var order_date = $("#order_date").val();
    var order_number = $("#order_number").val();
    var credit_sale_price = __read_number($("#unit_price"));
    var credit_sale_qty_hidden = $("#credit_sale_qty_hidden").val();
    var credit_sale_amount_hidden = $("#credit_sale_amount_hidden").val();
    var outstanding = $(".current_outstanding").text();
    var credit_limit = $(".credit_limit").text();
    $.ajax({
        method: "post",
        url: "/petro/settlement/payment/save-credit-sale-payment",
        data: {
            settlement_no: settlement_no,
            customer_id: credit_sale_customer_id,
            product_id: credit_sale_product_id,
            order_number: order_number,
            order_date: order_date,
            price: credit_sale_price,
            qty: credit_sale_qty_hidden,
            amount: credit_sale_amount_hidden,
            outstanding: outstanding,
            credit_limit: credit_limit,
            customer_reference: customer_reference,
        },
        success: function (result) {
            if (!result.success) {
                toastr.error(result.msg);
            } else {
                settlement_credit_sale_payment_id = result.settlement_credit_sale_payment_id;
                add_payment(credit_sale_amount_hidden);
                $("#credit_sale_table tbody").prepend(
                    `
                    <tr> 
                        <td>` +
                        customer_name +
                        `</td>
                        <td>` +
                        outstanding +
                        `</td>
                        <td>` +
                        credit_limit +
                        `</td>
                        <td>` +
                        order_number +
                        `</td>
                        <td>` +
                        order_date +
                        `</td>
                        <td>` +
                        customer_reference +
                        `</td>
                        <td>` +
                        credit_sale_product_name +
                        `</td>
                        <td>` +
                        __number_f(credit_sale_price, false, false, __currency_precision) +
                        `</td>
                        <td>` +
                        __number_f(credit_sale_qty_hidden, false, false, __currency_precision) +
                        `</td>
                        <td class="credit_sale_amount">` +
                        __number_f(credit_sale_amount_hidden, false, false, __currency_precision) +
                        `</td>
                        <td><button type="button" class="btn btn-xs btn-danger delete_credit_sale_payment" data-href="/petro/settlement/payment/delete-credit-sale-payment/` +
                        settlement_credit_sale_payment_id +
                        `"><i class="fa fa-times"></i></button>
                        </td>
                    </tr>
                `
                );
                $("#customer_reference_one_time").val("").trigger("change");
                $(".credit_sale_fields").val("");
                $("#order_number").val(order_number);
                calculateTotal("#credit_sale_table", ".credit_sale_amount", ".credit_sale_total");
            }
        },
    });
});
$(document).on("change", "#credit_sale_qty, #unit_price", function () {
    let price = __read_number($("#unit_price"));
    let qty = __read_number($("#credit_sale_qty"));
    let amount = price * qty;
    __write_number($("#credit_sale_amount"), amount);
    $("#credit_sale_qty_hidden").val(qty);
    $("#credit_sale_amount_hidden").val(amount);
});
$(document).on("change", "#credit_sale_amount", function () {
    let price = __read_number($("#unit_price"));
    let amount = __read_number($(this));
    let qty = amount / price;
    __write_number($("#credit_sale_qty"), qty);
    $("#credit_sale_qty_hidden").val(qty);
    $("#credit_sale_amount_hidden").val(amount);
});
$(document).on("change", "#credit_sale_product_id", function () {
    if ($(this).val()) {
        $.ajax({
            method: "get",
            url: "/petro/settlement/payment/get-product-price",
            data: { product_id: $(this).val() },
            success: function (result) {
                $("#unit_price").val(result.price);
                $("#credit_sale_qty").change();
                $("#credit_sale_amount").attr("disabled", false);
                $("#credit_sale_qty").attr("disabled", true);
            },
        });
    } else {
        $("#credit_sale_amount").attr("disabled", true);
        $("#credit_sale_qty").attr("disabled", true);
    }
});
$(document).on("change", "#credit_sale_customer_id", function () {
    $.ajax({
        method: "get",
        url: "/petro/settlement/payment/get-customer-details/" + $(this).val(),
        data: {},
        success: function (result) {
            $(".current_outstanding").text(result.total_outstanding);
            $(".credit_limit").text(result.credit_limit);
            $("#customer_reference").empty();
            $("#customer_reference").append(`<option selected="selected" value="">Please Select</option>`);
            result.customer_references.forEach(function (ref, i) {
                $("#customer_reference").append(`<option value="` + ref.reference + `">` + ref.reference + `</option>`);
                $("#customer_reference").val($("#customer_reference option:eq(1)").val()).trigger("change");
            });
        },
    });
});
$(document).on("click", ".delete_credit_sale_payment", function () {
    url = $(this).data("href");
    tr = $(this).closest("tr");
    $.ajax({
        method: "delete",
        url: url,
        data: {},
        success: function (result) {
            if (result.success) {
                toastr.success(result.msg);
                tr.remove();
                let this_amount = result.amount;
                delete_payment(this_amount);
                calculateTotal("#credit_sale_table", ".credit_sale_amount", ".credit_sale_total");
            } else {
                toastr.error(result.msg);
            }
        },
    });
});
//expense payments
$(document).on("click", ".expense_add", function () {
    if ($("#expense_amount").val() == "") {
        toastr.error("Please enter amount");
        return false;
    }
    var settlement_no = $("#settlement_no").val();
    var expense_number = $("#expense_number").val();
    var reference_no = $("#reference_no").val();
    var expense_account = $("#expense_account").val();
    var expense_account_name = $("#expense_account :selected").text();
    var expense_category = $("#expense_category").val();
    var expense_category_name = $("#expense_category :selected").text();
    var expense_reason = $("#expense_reason").val();
    var expense_amount = $("#expense_amount").val();
    $.ajax({
        method: "post",
        url: "/petro/settlement/payment/save-expense-payment",
        data: {
            settlement_no: settlement_no,
            expense_number: expense_number,
            category_id: expense_category,
            reference_no: reference_no,
            account_id: expense_account,
            reason: expense_reason,
            amount: expense_amount,
        },
        success: function (result) {
            if (!result.success) {
                toastr.error(result.msg);
            } else {
                settlement_expense_payment_id = result.settlement_expense_payment_id;
                add_payment(expense_amount);
                $("#expense_table tbody").prepend(
                    `
                    <tr> 
                        <td>` +
                        expense_number +
                        `</td>
                        <td>` +
                        expense_category_name +
                        `</td>
                        <td>` +
                        reference_no +
                        `</td>
                        <td>` +
                        expense_account_name +
                        `</td>
                        <td>` +
                        expense_reason +
                        `</td>
                        <td class="expense_amount">` +
                        __number_f(expense_amount, false, false, __currency_precision) +
                        `</td>
                        <td><button type="button" class="btn btn-xs btn-danger delete_expense_payment" data-href="/petro/settlement/payment/delete-expense-payment/` +
                        settlement_expense_payment_id +
                        `"><i class="fa fa-times"></i></button>
                        </td>
                    </tr>
                `
                );
                $(".expense_fields").val("");
                $("#expense_number").val(result.expense_number);
                calculateTotal("#expense_table", ".expense_amount", ".expense_total");
            }
        },
    });
});
$(document).on("click", ".delete_expense_payment", function () {
    url = $(this).data("href");
    tr = $(this).closest("tr");
    $.ajax({
        method: "delete",
        url: url,
        data: {},
        success: function (result) {
            if (result.success) {
                toastr.success(result.msg);
                tr.remove();
                let this_amount = result.amount;
                delete_payment(this_amount);
                calculateTotal("#expense_table", ".expense_amount", ".expense_total");
            } else {
                toastr.error(result.msg);
            }
        },
    });
});
//shortage payments
$(document).on("click", ".shortage_add", function () {
    if ($("#shortage_amount").val() == "") {
        toastr.error("Please enter amount");
        return false;
    }
    var settlement_no = $("#settlement_no").val();
    var shortage_amount = $("#shortage_amount").val();
    $.ajax({
        method: "post",
        url: "/petro/settlement/payment/save-shortage-payment",
        data: {
            settlement_no: settlement_no,
            amount: shortage_amount,
        },
        success: function (result) {
            if (!result.success) {
                toastr.error(result.msg);
            } else {
                settlement_shortage_payment_id = result.settlement_shortage_payment_id;
                add_payment(shortage_amount);
                $("#shortage_table tbody").prepend(
                    `
                    <tr> 
                        <td></td>
                        <td class="shortage_amount">` +
                        __number_f(shortage_amount, false, false, __currency_precision) +
                        `</td>
                        <td><button type="button" class="btn btn-xs btn-danger delete_shortage_payment" data-href="/petro/settlement/payment/delete-shortage-payment/` +
                        settlement_shortage_payment_id +
                        `"><i class="fa fa-times"></i></button>
                        </td>
                    </tr>
                `
                );
                $(".shortage_fields").val("");
                $("#shortage_number").val(result.shortage_number);
                calculateTotal("#shortage_table", ".shortage_amount", ".shortage_total");
            }
        },
    });
});
$(document).on("click", ".delete_shortage_payment", function () {
    url = $(this).data("href");
    tr = $(this).closest("tr");
    $.ajax({
        method: "delete",
        url: url,
        data: {},
        success: function (result) {
            if (result.success) {
                toastr.success(result.msg);
                tr.remove();
                let this_amount = result.amount;
                delete_payment(this_amount);
                calculateTotal("#shortage_table", ".shortage_amount", ".shortage_total");
            } else {
                toastr.error(result.msg);
            }
        },
    });
});
//excess payments
$(document).on("click", ".excess_add", function () {
    var excess_amount_input = $("#excess_amount").val();
    if (excess_amount_input == "") {
        toastr.error("Please enter amount");
        return false;
    } else {
        if (excess_amount_input > 0) {
            toastr.error("Please enter the amount with a negative symbol");
            return false;
        }
    }
    var settlement_no = $("#settlement_no").val();
    var excess_amount = $("#excess_amount").val();
    $.ajax({
        method: "post",
        url: "/petro/settlement/payment/save-excess-payment",
        data: {
            settlement_no: settlement_no,
            amount: excess_amount,
        },
        success: function (result) {
            if (!result.success) {
                toastr.error(result.msg);
            } else {
                settlement_excess_payment_id = result.settlement_excess_payment_id;
                add_payment(excess_amount);
                $("#excess_table tbody").prepend(
                    `
                    <tr> 
                        <td></td>
                        <td class="excess_amount">` +
                        __number_f(excess_amount, false, false, __currency_precision) +
                        `</td>
                        <td><button type="button" class="btn btn-xs btn-danger delete_excess_payment" data-href="/petro/settlement/payment/delete-excess-payment/` +
                        settlement_excess_payment_id +
                        `"><i class="fa fa-times"></i></button>
                        </td>
                    </tr>
                `
                );
                $(".excess_fields").val("");
                $("#excess_number").val(result.excess_number);
                calculateTotal("#excess_table", ".excess_amount", ".excess_total");
            }
        },
    });
});
$(document).on("click", ".delete_excess_payment", function () {
    url = $(this).data("href");
    tr = $(this).closest("tr");
    $.ajax({
        method: "delete",
        url: url,
        data: {},
        success: function (result) {
            if (result.success) {
                toastr.success(result.msg);
                tr.remove();
                let this_amount = result.amount;
                delete_payment(this_amount);
                calculateTotal("#excess_table", ".excess_amount", ".excess_total");
            } else {
                toastr.error(result.msg);
            }
        },
    });
});
function add_payment(add_amount) {
    add_amount = parseFloat(add_amount);
    total_balance = parseFloat($("#total_balance").val().replace(",", ""));
    total_paid = parseFloat($("#total_paid").val());
    total_balance = total_balance - add_amount;
    total_paid = total_paid + add_amount;
    $("#total_balance").val(__number_f(total_balance, false, false, __currency_precision));
    $("#total_paid").val(total_paid);
    $(".total_balance").text(__number_f(total_balance, false, false, __currency_precision));
    $(".total_paid").text(__number_f(total_paid, false, false, __currency_precision));
    if (total_balance === 0) {
        $("#settlement_save_btn").removeClass("hide");
    } else {
        $("#settlement_save_btn").addClass("hide");
    }
}
function delete_payment(delete_amount) {
    delete_amount = parseFloat(delete_amount);
    console.log(delete_amount);
    total_balance = parseFloat($("#total_balance").val().replace(",", ""));
    total_paid = parseFloat($("#total_paid").val());
    total_balance = total_balance + delete_amount;
    total_paid = total_paid - delete_amount;
    $("#total_balance").val(total_balance);
    $("#total_paid").val(total_paid);
    $(".total_balance").text(__number_f(total_balance, false, false, __currency_precision));
    $(".total_paid").text(__number_f(total_paid, false, false, __currency_precision));
    if (total_balance.toFixed(__currency_precision) === 0) {
        $("#settlement_save_btn").removeClass("hide");
    } else {
        $("#settlement_save_btn").addClass("hide");
    }
}
function calculateTotal(table_name, class_name_td, output_element) {
    let total = 0.0;
    $(table_name + " tbody")
        .find(class_name_td)
        .each(function () {
            total += parseFloat($(this).text().replace(",", ""));
        });
    $(output_element).text(__number_f(total, false, false, __currency_precision));
}
//save settlement
$(document).on("click", "#settlement_save_btn", function () {
    $(this).attr("disabled", "disabled");
    var url = $("#settlement_form").attr("action");
    var settlement_no = $("#settlement_no").val();
    $.ajax({
        method: "post",
        url: url,
        data: { settlement_no: settlement_no },
        success: function (result) {
            if (result.success === 0) {
                toastr.error(result.msg);
            } else {
                $("#settlement_print").html(result);
                var divToPrint = document.getElementById("settlement_print");
                var newWin = window.open("", "_self");
                newWin.document.open();
                newWin.document.write('<html><body onload="window.print()">' + divToPrint.innerHTML + "</body></html>");
                newWin.document.close();
            }
        },
    });
});
function myFloatNumber(i) {
    var value = Math.floor(i * 100) / 100;
    return value;
}
$(document).on("change", "#expense_category", function () {
    $.ajax({
        method: "get",
        url: "/get-expense-account-category-id/" + $(this).val(),
        data: {},
        success: function (result) {
            $("#expense_account").empty().append(`<option value="${result.expense_account_id}" selected>${result.name}</option>`);
        },
    });
});

//Check Active Tab
$(document).on("click", "#add_payment", function () {
    var myVar = setInterval(() => {
        if($('.add_payment').hasClass('in')){
            if($("#cash_tab").hasClass("active")){
                $("#cash_amount").focus();
            }
        }
        if ($("#cash_amount").is(":focus")) {
            clearInterval(myVar);
        }
    }, 1000);
});
$(document).on("click", ".tabs", function () {
    var tab_id = $(this).attr("href");
    console.log(tab_id);
    if(tab_id == "#expense_tab"  && ($("#expense_tab").hasClass("active"))){
        $('#expense_category').focus();
    }else if(tab_id == "#credit_sales_tab"  && ($("#credit_sales_tab").hasClass("active"))){
        $('#order_number').focus();
    }else{
        $(tab_id+' :input:enabled:visible:first').focus();
    }
});

