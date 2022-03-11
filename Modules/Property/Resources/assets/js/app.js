$(document).ready(function () {
    if ($('input#iraqi_selling_price_adjustment').length > 0) {
        iraqi_selling_price_adjustment = true;
    } else {
        iraqi_selling_price_adjustment = false;
    }

    //Date picker
    $('#transaction_date').datetimepicker({
        format: moment_date_format + ' ' + moment_time_format,
        ignoreReadonly: true,
    });

    //get suppliers
    $('#contact_id')
        .select2({
            ajax: {
                url: '/purchases/get_suppliers',
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        q: params.term, // search term
                        page: params.page,
                    };
                },
                processResults: function (data) {
                    return {
                        results: data,
                    };
                },
            },
            minimumInputLength: 1,
            escapeMarkup: function (m) {
                return m;
            },
            templateResult: function (data) {
                if (!data.id) {
                    return data.text;
                }
                var html = data.text + ' - ' + data.business_name + ' (' + data.contact_id + ')';
                return html;
            },
            language: {
                noResults: function () {
                    var name = $('#contact_id').data('select2').dropdown.$search.val();
                    return (
                        '<button type="button" data-name="' +
                        name +
                        '" class="btn btn-link add_new_supplier"><i class="fa fa-plus-circle fa-lg" aria-hidden="true"></i>&nbsp; ' +
                        __translate('add_name_as_new_supplier', { name: name }) +
                        '</button>'
                    );
                },
            },
        })
        .on('select2:select', function (e) {
            var data = e.params.data;
            $('#pay_term_number').val(data.pay_term_number);
            $('#pay_term_type').val(data.pay_term_type);
        });

    //Quick add supplier
    $(document).on('click', '.add_new_supplier', function () {
        $('#contact_id').select2('close');
        var name = $(this).data('name');
        $('.contact_modal').find('input#name').val(name);
        $('.contact_modal')
            .find('select#contact_type')
            .val('supplier')
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

    $('button#add-payment-row').click(function () {
        var row_index = parseInt($('.payment_row_index').last().val()) + 1;console.log(row_index);
        var location_id = $('#location_id').val();

        $.ajax({
            method: 'POST',
            url: '/purchases/get_payment_row',
            data: { row_index: row_index, location_id: location_id },
            dataType: 'html',
            success: function (result) {
                if (result) {
                    var total_payable = __read_number($('input#final_total'));
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

    $('#final_total').change(function () {
        update_payment_due();
    });

    $(document).on('change', 'input.payment-amount', function () {
        var payment = 0;

        $('#payment_rows_div')
            .find('.payment-amount')
            .each(function () {
                if (parseFloat($(this).val())) {
                    payment += __read_number($(this));
                }
            });
        var grand_total = __read_number($('input#final_total'), true);
        var bal = grand_total - payment;
        $('#payment_due').text(__currency_trans_from_en(bal, true, true));

        if (
            $('.payment_types_dropdown').val() === 'cash' ||
            $('.payment_types_dropdown').val() === 'cheque'
        ) {
            if (parseFloat($(this).val()) > grand_total) {
                toastr.error('Amount Should not be more then total amount');
                $(this).val('');
            }
        }

        var row_id = parseInt($(this).closest('.payment_row').data('row_id'));
        if (row_id >= 0) {
            check_insufficient_balance_row(row_id);
        }
    });

    function update_payment_due() {
        var payment = 0;

        $('#payment_rows_div')
            .find('.payment-amount')
            .each(function () {
                if (parseFloat($(this).val())) {
                    payment += __read_number($(this));
                }
            });
        var grand_total = __read_number($('input#final_total'), true);
        var bal = grand_total - payment;
        $('#payment_due').text(__currency_trans_from_en(bal, true, true));
    }

    $(document).on('click', 'button#submit_purchase_form', function (e) {
        e.preventDefault();
        //check if internet available or not

        $('form#add_purchase_form').validate({
            rules: {
            },
            messages: {
            },
        });

        if ($('form#add_purchase_form').valid()) {
            $('form#add_purchase_form').submit();
        }
    });
});
