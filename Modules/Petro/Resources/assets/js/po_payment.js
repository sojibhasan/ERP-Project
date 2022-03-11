function enterVal(val) {
    $("#amount").focus();
    if (val === "precision") {
        str = $("#amount").val();
        str = str + ".";
        $("#amount").val(str);
        return;
    }
    if (val === "backspace") {
        str = $("#amount").val();
        str = str.substring(0, str.length - 1);
        $("#amount").val(str);
        enableSaveButton();
        return;
    }
    let amount = $("#amount").val() + val;
    amount = amount.replace(",", "");
    $("#amount").val(amount);
    enableSaveButton();
}

$(document).on("click", ".payment_type_btn", function () {
    clicked_btn = $(this);

    siblings = $(clicked_btn).siblings();
    return_false = false;
    siblings.each(function (i, ele) {
        if ($(ele).hasClass("active") && return_false === false) {
            return_false = true;
            console.log(return_false);
        }
    });

    if (return_false) {
        return false;
    }

    siblings.each(function (i, ele) {
        $(ele).addClass("active");
        $(this).find(".payment_type_checkbox").attr("checked", false);
    });
    $("#payment_type").val($(this).find(".payment_type_checkbox").val());
    $(this).find(".payment_type_checkbox").attr("checked", true);
    $(clicked_btn).removeClass("active");
    enableSaveButton();
});

$(document).on("click", "#payment_submit", function () {
    let amount = $("#amount").val();
    let payment_type = $("#payment_type").val();
    if (amount === "" || amount === undefined || amount === null) {
        toastr.error("Please enter amount");
        return false;
    }
    if (payment_type === "" || payment_type === undefined || payment_type === null) {
        toastr.error("Please select payment type");
        return false;
    }
    $("#payment_submit").attr("disabled", true);
    amount = parseFloat(amount);
    $.ajax({
        method: "POST",
        url: "/petro/pump-operator-payments",
        data: { amount, payment_type },
        success: function (result) {
            if (result.success) {
                toastr.success(result.msg);
                if ($(".view_modal").length) {
                    $(".view_modal").modal("hide");
                    location.reload();
                }
            } else {
                toastr.error(result.msg);
                $("#payment_submit").attr("disabled", false);
            }
        },
    });
});

function reset() {
    document.getElementById("amount").value = "";
    document.getElementById("payment_type").value = "";
    $(".payment_type_btn").each(function (i, ele) {
        console.log("asdf");
        $(ele).removeClass("active");
        $(this).find(".payment_type_checkbox").attr("checked", false);
    });
}
$("#amount").focus();

$("input#amount").change(function () {
    enableSaveButton();
});

function enableSaveButton() {
    let amount = $("#amount").val();
    let payment_type = $("#payment_type").val();
    console.log(amount);
    console.log(payment_type.length);
    if (amount == "" || amount == undefined || amount == null) {
        $("#payment_submit").attr("disabled", true);
    } else if ($("#payment_type").prop("checked") == false && payment_type.length == 0) {
        $("#payment_submit").attr("disabled", true);
    } else {
        $("#payment_submit").attr("disabled", false);
    }
}
