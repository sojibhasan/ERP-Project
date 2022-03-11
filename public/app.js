$(document).ready(function () {
    $('body').on('click', 'label', function (e) {
        var field_id = $(this).attr('for');
        if (field_id) {
            if ($('#' + field_id).hasClass('select2')) {
                $('#' + field_id).select2('open');
                return false;
            }
        }
    });
    fileinput_setting = {
        showUpload: false,
        showPreview: false,
        browseLabel: LANG.file_browse_label,
        removeLabel: LANG.remove,
    };
    $(document).ajaxStart(function () {
        Pace.restart();
    });
    __select2($('.select2'));
    $('body').on('click', '[data-toggle="popover"]', function () {
        if ($(this).hasClass('popover-default')) {
            return false;
        }
        $(this).popover('show');
    });
    $('body').on('click', '.details_popover', function () {
        if ($(this).hasClass('popover-default')) {
            return false;
        }
        $(this).popover('show');
    });
    $('button#btnKeyboard').hover(function () {
        $(this).tooltip('show');
    });
    $(document).ready(function () {
        $('#btnKeyboard').popover();
    });
    $('.start-date-picker').datepicker({ autoclose: true, endDate: 'today' });
    $(document).on('click', '.btn-modal', function (e) {
        e.preventDefault();
        var container = $(this).data('container');
        $.ajax({
            url: $(this).data('href'),
            dataType: 'html',
            success: function (result) {
                $(container).html(result).modal('show');
            },
        });
    });

    $(document).on('submit', 'form#brand_add_form', function (e) {
        e.preventDefault();
        $(this).find('button[type="submit"]').attr('disabled', true);
        var data = $(this).serialize();
        $.ajax({
            method: 'POST',
            url: $(this).attr('action'),
            dataType: 'json',
            data: data,
            success: function (result) {
                if (result.success == true) {
                    $('div.brands_modal').modal('hide');
                    toastr.success(result.msg);
                    brands_table.ajax.reload();
                } else {
                    toastr.error(result.msg);
                }
            },
        });
    });
    var brands_table = $('#brands_table').DataTable({
        processing: true,
        serverSide: true,
        ajax: '/brands',
        columnDefs: [{ targets: 2, orderable: false, searchable: false }],
    });
    $(document).on('click', 'button.edit_brand_button', function () {
        $('div.brands_modal').load($(this).data('href'), function () {
            $(this).modal('show');
            $('form#brand_edit_form').submit(function (e) {
                e.preventDefault();
                $(this).find('button[type="submit"]').attr('disabled', true);
                var data = $(this).serialize();
                $.ajax({
                    method: 'POST',
                    url: $(this).attr('action'),
                    dataType: 'json',
                    data: data,
                    success: function (result) {
                        if (result.success == true) {
                            $('div.brands_modal').modal('hide');
                            toastr.success(result.msg);
                            brands_table.ajax.reload();
                        } else {
                            toastr.error(result.msg);
                        }
                    },
                });
            });
        });
    });
    $(document).on('click', 'button.delete_brand_button', function () {
        swal({
            title: LANG.sure,
            text: LANG.confirm_delete_brand,
            icon: 'warning',
            buttons: true,
            dangerMode: true,
        }).then((willDelete) => {
            if (willDelete) {
                var href = $(this).data('href');
                var data = $(this).serialize();
                $.ajax({
                    method: 'DELETE',
                    url: href,
                    dataType: 'json',
                    data: data,
                    success: function (result) {
                        if (result.success == true) {
                            toastr.success(result.msg);
                            brands_table.ajax.reload();
                        } else {
                            toastr.error(result.msg);
                        }
                    },
                });
            }
        });
    });
    var tax_rates_table = $('#tax_rates_table').DataTable({
        processing: true,
        serverSide: true,
        ajax: '/tax-rates',
        columnDefs: [{ targets: 2, orderable: false, searchable: false }],
    });
    $(document).on('submit', 'form#tax_rate_add_form', function (e) {
        e.preventDefault();
        $(this).find('button[type="submit"]').attr('disabled', true);
        var data = $(this).serialize();
        $.ajax({
            method: 'POST',
            url: $(this).attr('action'),
            dataType: 'json',
            data: data,
            success: function (result) {
                if (result.success == true) {
                    $('div.tax_rate_modal').modal('hide');
                    toastr.success(result.msg);
                    tax_rates_table.ajax.reload();
                } else {
                    toastr.error(result.msg);
                }
            },
        });
    });
    $(document).on('click', 'button.edit_tax_rate_button', function () {
        $('div.tax_rate_modal').load($(this).data('href'), function () {
            $(this).modal('show');
            $('form#tax_rate_edit_form').submit(function (e) {
                e.preventDefault();
                $(this).find('button[type="submit"]').attr('disabled', true);
                var data = $(this).serialize();
                $.ajax({
                    method: 'POST',
                    url: $(this).attr('action'),
                    dataType: 'json',
                    data: data,
                    success: function (result) {
                        if (result.success == true) {
                            $('div.tax_rate_modal').modal('hide');
                            toastr.success(result.msg);
                            tax_rates_table.ajax.reload();
                            tax_groups_table.ajax.reload();
                        } else {
                            toastr.error(result.msg);
                        }
                    },
                });
            });
        });
    });
    $(document).on('click', 'button.delete_tax_rate_button', function () {
        swal({
            title: LANG.sure,
            text: LANG.confirm_delete_tax_rate,
            icon: 'warning',
            buttons: true,
            dangerMode: true,
        }).then((willDelete) => {
            if (willDelete) {
                var href = $(this).data('href');
                var data = $(this).serialize();
                $.ajax({
                    method: 'DELETE',
                    url: href,
                    dataType: 'json',
                    data: data,
                    success: function (result) {
                        if (result.success == true) {
                            toastr.success(result.msg);
                            tax_rates_table.ajax.reload();
                            tax_groups_table.ajax.reload();
                        } else {
                            toastr.error(result.msg);
                        }
                    },
                });
            }
        });
    });
    var is_property = $('#is_property').val();
    var units_table = $('#unit_table').DataTable({
        processing: true,
        serverSide: true,
        ajax: '/units?is_property=' + is_property,
        columnDefs: [{ targets: 3, orderable: false, searchable: false }],
        columns: [
            { data: 'actual_name', name: 'actual_name' },
            { data: 'short_name', name: 'short_name' },
            { data: 'allow_decimal', name: 'allow_decimal' },
            { data: 'multiple_units', name: 'multiple_units' },
            { data: 'connected_units', name: 'connected_units' },
            { data: 'action', name: 'action' },
        ],
    });
    $(document).on('submit', 'form#unit_add_form', function (e) {
        e.preventDefault();
        $(this).find('button[type="submit"]').attr('disabled', true);
        var data = $(this).serialize();
        $.ajax({
            method: 'POST',
            url: $(this).attr('action'),
            dataType: 'json',
            data: data,
            success: function (result) {
                if (result.success == true) {
                    $('div.unit_modal').modal('hide');
                    toastr.success(result.msg);
                    units_table.ajax.reload();
                } else {
                    toastr.error(result.msg);
                }
            },
        });
    });
    $(document).on('click', 'button.edit_unit_button', function (e) {
        e.preventDefault();
        $('div.unit_modal').load($(this).data('href'), function () {
            $(this).modal('show');
            $('form#unit_edit_form').submit(function (e) {
                e.preventDefault();
                $(this).find('button[type="submit"]').attr('disabled', true);
                var data = $(this).serialize();
                $.ajax({
                    method: 'POST',
                    url: $(this).attr('action'),
                    dataType: 'json',
                    data: data,
                    success: function (result) {
                        if (result.success == true) {
                            $('div.unit_modal').modal('hide');
                            toastr.success(result.msg);
                            units_table.ajax.reload();
                        } else {
                            toastr.error(result.msg);
                        }
                    },
                });
            });
        });
    });
    $(document).on('click', 'button.delete_unit_button', function () {
        swal({
            title: LANG.sure,
            text: LANG.confirm_delete_unit,
            icon: 'warning',
            buttons: true,
            dangerMode: true,
        }).then((willDelete) => {
            if (willDelete) {
                var href = $(this).data('href');
                var data = $(this).serialize();
                $.ajax({
                    method: 'DELETE',
                    url: href,
                    dataType: 'json',
                    data: data,
                    success: function (result) {
                        if (result.success == true) {
                            toastr.success(result.msg);
                            units_table.ajax.reload();
                        } else {
                            toastr.error(result.msg);
                        }
                    },
                });
            }
        });
    });

    var contact_table_type = $('#contact_type').val();
    if (contact_table_type == 'supplier') {
        var columns = [
            { data: 'mass_delete', searchable: false, orderable: false },
            { data: 'action', searchable: false, orderable: false },
            { data: 'contact_id', name: 'contact_id' },
            { data: 'supplier_business_name', name: 'supplier_business_name' },
            { data: 'name', name: 'name' },
            { data: 'mobile', name: 'mobile' },
            { data: 'supplier_group', name: 'cg.name' },
            { data: 'pay_term', name: 'pay_term', searchable: false, orderable: false },
            { data: 'due', searchable: false, orderable: false },
            { data: 'return_due', searchable: false, orderable: false },
            { data: 'opening_balance', name: 'opening_balance', searchable: false },
            { data: 'email', name: 'email' },
            { data: 'tax_number', name: 'tax_number' },
            { data: 'created_at', name: 'contacts.created_at' },
        ];
        if (!$('.contact_custom_field1').hasClass('hide')) {
            columns.push({
                data: 'custom_field1',
                name: 'custom_field1',
                searchable: false,
                orderable: false,
            });
        }
        if (!$('.contact_custom_field1').hasClass('hide')) {
            columns.push({
                data: 'custom_field2',
                name: 'custom_field2',
                searchable: false,
                orderable: false,
            });
        }
        if (!$('.contact_custom_field1').hasClass('hide')) {
            columns.push({
                data: 'custom_field3',
                name: 'custom_field3',
                searchable: false,
                orderable: false,
            });
        }
        if (!$('.contact_custom_field1').hasClass('hide')) {
            columns.push({
                data: 'custom_field4',
                name: 'custom_field4',
                searchable: false,
                orderable: false,
            });
        }
    } else if (contact_table_type == 'customer') {
        var columns = [
            { data: 'mass_delete', searchable: false, orderable: false },
            { data: 'action', searchable: false, orderable: false },
            { data: 'contact_id', name: 'contact_id' },
            { data: 'name', name: 'name' },
            { data: 'mobile', name: 'mobile' },
            { data: 'customer_group', name: 'cg.name' },
            { data: 'credit_limit', name: 'credit_limit' },
            { data: 'due', searchable: false, orderable: false },
            { data: 'return_due', searchable: false, orderable: false },
            { data: 'pay_term', name: 'pay_term', searchable: false, orderable: false },
            { data: 'opening_balance', name: 'opening_balance', searchable: false },
            { data: 'tax_number', name: 'tax_number' },
            { data: 'email', name: 'email' },
            { data: 'address', name: 'address', orderable: false },
            { data: 'created_at', name: 'contacts.created_at' },
        ];
        if (!$('.contact_custom_field1').hasClass('hide')) {
            columns.push({
                data: 'custom_field1',
                name: 'custom_field1',
                searchable: false,
                orderable: false,
            });
        }
        if (!$('.contact_custom_field1').hasClass('hide')) {
            columns.push({
                data: 'custom_field2',
                name: 'custom_field2',
                searchable: false,
                orderable: false,
            });
        }
        if (!$('.contact_custom_field1').hasClass('hide')) {
            columns.push({
                data: 'custom_field3',
                name: 'custom_field3',
                searchable: false,
                orderable: false,
            });
        }
        if (!$('.contact_custom_field1').hasClass('hide')) {
            columns.push({
                data: 'custom_field4',
                name: 'custom_field4',
                searchable: false,
                orderable: false,
            });
        }
        if ($('#rp_col').length) {
            columns.push({ data: 'total_rp', name: 'total_rp' });
        }
    }
    var contact_table = $('#contact_table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '/contacts',
            data: function (d) {
                d.type = $('#contact_type').val();
            },
        },
        aaSorting: [[1, 'desc']],
        columns: columns,
        buttons: [
            {
                extend: 'csv',
                text: '<i class="fa fa-file"></i> Export to CSV',
                className: 'btn btn-sm btn-default',
                exportOptions: {
                    columns: function (idx, data, node) {
                        return $(node).is(':visible') && !$(node).hasClass('notexport')
                            ? true
                            : false;
                    },
                },
            },
            {
                extend: 'excel',
                text: '<i class="fa fa-file-excel-o"></i> Export to Excel',
                className: 'btn btn-sm btn-default',
                exportOptions: {
                    columns: function (idx, data, node) {
                        return $(node).is(':visible') && !$(node).hasClass('notexport')
                            ? true
                            : false;
                    },
                },
            },
            {
                extend: 'colvis',
                text: '<i class="fa fa-columns"></i> Column Visibility',
                className: 'btn btn-sm btn-default',
                exportOptions: {
                    columns: function (idx, data, node) {
                        return $(node).is(':visible') && !$(node).hasClass('notexport')
                            ? true
                            : false;
                    },
                },
            },
            {
                extend: 'pdf',
                text: '<i class="fa fa-file-pdf-o"></i> Export to PDF',
                className: 'btn btn-sm btn-default',
                exportOptions: {
                    columns: function (idx, data, node) {
                        return $(node).is(':visible') && !$(node).hasClass('notexport')
                            ? true
                            : false;
                    },
                },
            },
            {
                extend: 'print',
                text: '<i class="fa fa-print"></i> Print',
                className: 'btn btn-sm btn-default',
                exportOptions: {
                    columns: function (idx, data, node) {
                        return $(node).is(':visible') && !$(node).hasClass('notexport')
                            ? true
                            : false;
                    },
                },
            },
        ],
        fnDrawCallback: function (oSettings) {
            var total_due = sum_table_col($('#contact_table'), 'contact_due');
            $('#footer_contact_due').text(total_due);
            var total_return_due = sum_table_col($('#contact_table'), 'return_due');
            $('#footer_contact_return_due').text(total_return_due);
            var total_opening_balance = sum_table_col($('#contact_table'), 'ob');
            $('#footer_contact_opening_balance').text(total_opening_balance);
            __currency_convert_recursively($('#contact_table'));
        },
    });
    var business_users_table = $('#business_users_table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '/business-users',
            data: function (d) {
                d.business_id = $('#business_id').val();
            },
        },
        columnDefs: [{ targets: [6], orderable: false, searchable: false }],
        columns: [
            { data: 'username' },
            { data: 'full_name' },
            { data: 'role' },
            { data: 'business_name' },
            { data: 'email' },
            { data: 'contact_number' },
            { data: 'action' },
        ],
    });
    $('.contact_modal').on('shown.bs.modal', function (e) {
        if ($('select#contact_type').val() == 'customer') {
            $('div.supplier_fields').addClass('hide');
            $('div.customer_fields').each(function () {
                if (!$(this).hasClass('backend_hide')) {
                    $(this).removeClass('hide');
                }
            });
        } else if ($('select#contact_type').val() == 'supplier') {
            $('div.supplier_fields').each(function () {
                if (!$(this).hasClass('backend_hide')) {
                    $(this).removeClass('hide');
                }
            });
            $('div.customer_fields').addClass('hide');
        }
        $('select#contact_type').change(function () {
            var t = $(this).val();
            if (t == 'supplier') {
                $('div.supplier_fields').each(function () {
                    if (!$(this).hasClass('backend_hide')) {
                        $(this).fadeIn();
                    }
                });
                $('div.customer_fields').fadeOut();
            } else if (t == 'both') {
                $('div.supplier_fields').each(function () {
                    if (!$(this).hasClass('backend_hide')) {
                        $(this).fadeIn();
                    }
                });
                $('div.customer_fields').each(function () {
                    if (!$(this).hasClass('backend_hide')) {
                        $(this).fadeIn();
                    }
                });
            } else if (t == 'customer') {
                $('div.customer_fields').each(function () {
                    if (!$(this).hasClass('backend_hide')) {
                        $(this).fadeIn();
                    }
                });
                $('div.supplier_fields').fadeOut();
            }
        });
        $('form#contact_add_form, form#contact_edit_form')
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
                messages: { contact_id: { remote: LANG.contact_id_already_exists } },
                submitHandler: function (form) {
                    e.preventDefault();
                    var data = $(form).serialize();
                    $(form).find('button[type="submit"]').attr('disabled', true);
                    $.ajax({
                        method: 'POST',
                        url: $(form).attr('action'),
                        dataType: 'json',
                        data: data,
                        success: function (result) {
                            if (result.success == true) {
                                $('div.contact_modal').modal('hide');
                                toastr.success(result.msg);
                                if (contact_table) {
                                    contact_table.ajax.reload();
                                }
                                if (property_contact_table) {
                                    property_contact_table.ajax.reload();
                                }
                            } else {
                                toastr.error(result.msg);
                            }
                        },
                    });
                },
            });
    });
    $(document).on('click', '.edit_contact_button', function (e) {
        e.preventDefault();
        $('div.contact_modal').load($(this).attr('href'), function () {
            $(this).modal('show');
        });
    });
    $(document).on('click', '.delete_contact_button', function (e) {
        e.preventDefault();
        swal({
            title: LANG.sure,
            text: LANG.confirm_delete_contact,
            icon: 'warning',
            buttons: true,
            dangerMode: true,
        }).then((willDelete) => {
            if (willDelete) {
                var href = $(this).attr('href');
                var data = $(this).serialize();
                $.ajax({
                    method: 'DELETE',
                    url: href,
                    dataType: 'json',
                    data: data,
                    success: function (result) {
                        if (result.success == true) {
                            toastr.success(result.msg);
                            contact_table.ajax.reload();
                        } else {
                            toastr.error(result.msg);
                        }
                    },
                });
            }
        });
    });
    category_table = $('#category_table').DataTable({
        processing: true,
        serverSide: true,
        aaSorting: [[1, 'desc']],
        ajax: {
            url: '/categories',
        },
        columns: [
            { data: 'name', name: 'name' },
            { data: 'short_code', name: 'short_code' },
            { data: 'action', name: 'action' },
        ],
    });
    $(document).on('submit', 'form#category_add_form', function (e) {
        e.preventDefault();
        $(this).find('button[type="submit"]').attr('disabled', true);
        var data = $(this).serialize();
        $.ajax({
            method: 'POST',
            url: $(this).attr('action'),
            dataType: 'json',
            data: data,
            success: function (result) {
                if (result.success === true) {
                    $('div.category_modal').modal('hide');
                    toastr.success(result.msg);
                    category_table.ajax.reload();
                } else {
                    toastr.error(result.msg);
                }
            },
        });
    });
    $(document).on('click', 'button.edit_category_button', function () {
        $('div.category_modal').load($(this).data('href'), function () {
            $(this).modal('show');
            $('form#category_edit_form').submit(function (e) {
                e.preventDefault();
                $(this).find('button[type="submit"]').attr('disabled', true);
                var data = $(this).serialize();
                $.ajax({
                    method: 'POST',
                    url: $(this).attr('action'),
                    dataType: 'json',
                    data: data,
                    success: function (result) {
                        if (result.success === true) {
                            $('div.category_modal').modal('hide');
                            toastr.success(result.msg);
                            category_table.ajax.reload();
                        } else {
                            toastr.error(result.msg);
                        }
                    },
                });
            });
        });
    });
    $(document).on('click', 'button.delete_category_button', function () {
        swal({
            title: LANG.sure,
            text: LANG.confirm_delete_category,
            icon: 'warning',
            buttons: true,
            dangerMode: true,
        }).then((willDelete) => {
            if (willDelete) {
                var href = $(this).data('href');
                var data = $(this).serialize();
                $.ajax({
                    method: 'DELETE',
                    url: href,
                    dataType: 'json',
                    data: data,
                    success: function (result) {
                        if (result.success === true) {
                            toastr.success(result.msg);
                            category_table.ajax.reload();
                        } else {
                            toastr.error(result.msg);
                        }
                    },
                });
            }
        });
    });
    var variation_table = $('#variation_table').DataTable({
        processing: true,
        serverSide: true,
        ajax: '/variation-templates',
        columnDefs: [{ targets: 2, orderable: false, searchable: false }],
    });
    $(document).on('click', '#add_variation_values', function () {
        var html =
            '<div class="form-group"><div class="col-sm-7 col-sm-offset-3"><input type="text" name="variation_values[]" class="form-control input_number" required></div><div class="col-sm-2"><button type="button" class="btn btn-danger delete_variation_value">-</button></div></div>';
        $('#variation_values').append(html);
    });
    $(document).on('click', '.delete_variation_value', function () {
        $(this).closest('.form-group').remove();
    });
    $(document).on('submit', 'form#variation_add_form', function (e) {
        e.preventDefault();
        $(this).find('button[type="submit"]').attr('disabled', true);
        var data = $(this).serialize();
        $.ajax({
            method: 'POST',
            url: $(this).attr('action'),
            dataType: 'json',
            data: data,
            success: function (result) {
                if (result.success === true) {
                    $('div.variation_modal').modal('hide');
                    toastr.success(result.msg);
                    variation_table.ajax.reload();
                } else {
                    toastr.error(result.msg);
                }
            },
        });
    });
    $(document).on('click', 'button.edit_variation_button', function () {
        $('div.variation_modal').load($(this).data('href'), function () {
            $(this).modal('show');
            $('form#variation_edit_form').submit(function (e) {
                $(this).find('button[type="submit"]').attr('disabled', true);
                e.preventDefault();
                var data = $(this).serialize();
                $.ajax({
                    method: 'POST',
                    url: $(this).attr('action'),
                    dataType: 'json',
                    data: data,
                    success: function (result) {
                        if (result.success === true) {
                            $('div.variation_modal').modal('hide');
                            toastr.success(result.msg);
                            variation_table.ajax.reload();
                        } else {
                            toastr.error(result.msg);
                        }
                    },
                });
            });
        });
    });
    $(document).on('click', 'button.delete_variation_button', function () {
        swal({
            title: LANG.sure,
            text: LANG.confirm_delete_variation,
            icon: 'warning',
            buttons: true,
            dangerMode: true,
        }).then((willDelete) => {
            if (willDelete) {
                var href = $(this).data('href');
                var data = $(this).serialize();
                $.ajax({
                    method: 'DELETE',
                    url: href,
                    dataType: 'json',
                    data: data,
                    success: function (result) {
                        if (result.success === true) {
                            toastr.success(result.msg);
                            variation_table.ajax.reload();
                        } else {
                            toastr.error(result.msg);
                        }
                    },
                });
            }
        });
    });
    var active = false;
    $(document).on('mousedown', '.drag-select', function (ev) {
        active = true;
        $('.active-cell').removeClass('active-cell');
        $(this).addClass('active-cell');
        cell_value = $(this).find('input').val();
    });
    $(document).on('mousemove', '.drag-select', function (ev) {
        if (active) {
            $(this).addClass('active-cell');
            $(this).find('input').val(cell_value);
        }
    });
    $(document).mouseup(function (ev) {
        active = false;
        if (
            !$(ev.target).hasClass('drag-select') &&
            !$(ev.target).hasClass('dpp') &&
            !$(ev.target).hasClass('dsp')
        ) {
            $('.active-cell').each(function () {
                $(this).removeClass('active-cell');
            });
        }
    });
    $(document).on('change', '.toggler', function () {
        var parent_id = $(this).attr('data-toggle_id');
        if ($(this).is(':checked')) {
            $('#' + parent_id).removeClass('hide');
        } else {
            $('#' + parent_id).addClass('hide');
        }
    });
    $('#category_id').change(function () {
        get_sub_categories();
    });
    $(document).on('change', '#unit_id', function () {
        get_sub_units();
    });
    if ($('.product_form').length && !$('.product_form').hasClass('create')) {
        show_product_type_form();
    }
    $('#type').change(function () {
        show_product_type_form();
    });
    $(document).on('click', '#add_variation', function () {
        var row_index = $('#variation_counter').val();
        var action = $(this).attr('data-action');
        $.ajax({
            method: 'POST',
            url: '/products/get_product_variation_row',
            data: { row_index: row_index, action: action },
            dataType: 'html',
            success: function (result) {
                if (result) {
                    $('#product_variation_form_part  > tbody').append(result);
                    $('#variation_counter').val(parseInt(row_index) + 1);
                    toggle_dsp_input();
                }
            },
        });
    });
    if ($('form#bussiness_edit_form').length > 0) {
        $('form#bussiness_edit_form').validate({ ignore: [] });
        $('#business_logo').fileinput(fileinput_setting);
        $('input#purchase_in_diff_currency').on('ifChecked', function (event) {
            $('div#settings_purchase_currency_div, div#settings_currency_exchange_div').removeClass(
                'hide'
            );
        });
        $('input#purchase_in_diff_currency').on('ifUnchecked', function (event) {
            $('div#settings_purchase_currency_div, div#settings_currency_exchange_div').addClass(
                'hide'
            );
        });
        $('input#enable_product_expiry').change(function () {
            if ($(this).is(':checked')) {
                $('select#expiry_type').attr('disabled', false);
                $('div#on_expiry_div').removeClass('hide');
            } else {
                $('select#expiry_type').attr('disabled', true);
                $('div#on_expiry_div').addClass('hide');
            }
        });
        $('select#on_product_expiry').change(function () {
            if ($(this).val() == 'stop_selling') {
                $('input#stop_selling_before').attr('disabled', false);
                $('input#stop_selling_before').focus().select();
            } else {
                $('input#stop_selling_before').attr('disabled', true);
            }
        });
        $('input#enable_category').on('ifChecked', function (event) {
            $('div.enable_sub_category').removeClass('hide');
        });
        $('input#enable_category').on('ifUnchecked', function (event) {
            $('div.enable_sub_category').addClass('hide');
        });
    }
    $('#upload_document').fileinput(fileinput_setting);
    $('form#edit_user_profile_form').validate();
    $('form#edit_password_form').validate({
        rules: {
            current_password: { required: true, minlength: 5 },
            new_password: { required: true, minlength: 5 },
            confirm_password: { equalTo: '#new_password' },
        },
    });
    var tax_groups_table = $('#tax_groups_table').DataTable({
        processing: true,
        serverSide: true,
        ajax: '/group-taxes',
        columnDefs: [{ targets: [2, 3], orderable: false, searchable: false }],
        columns: [
            { data: 'name', name: 'name' },
            { data: 'amount', name: 'amount' },
            { data: 'sub_taxes', name: 'sub_taxes' },
            { data: 'action', name: 'action' },
        ],
    });
    $('.tax_group_modal').on('shown.bs.modal', function () {
        $('.tax_group_modal')
            .find('.select2')
            .each(function () {
                __select2($(this));
            });
    });
    $(document).on('submit', 'form#tax_group_add_form', function (e) {
        e.preventDefault();
        $(this).find('button[type="submit"]').attr('disabled', true);
        var data = $(this).serialize();
        $.ajax({
            method: 'POST',
            url: $(this).attr('action'),
            dataType: 'json',
            data: data,
            success: function (result) {
                if (result.success == true) {
                    $('div.tax_group_modal').modal('hide');
                    toastr.success(result.msg);
                    tax_groups_table.ajax.reload();
                } else {
                    toastr.error(result.msg);
                }
            },
        });
    });
    $(document).on('submit', 'form#tax_group_edit_form', function (e) {
        e.preventDefault();
        $(this).find('button[type="submit"]').attr('disabled', true);
        var data = $(this).serialize();
        $.ajax({
            method: 'POST',
            url: $(this).attr('action'),
            dataType: 'json',
            data: data,
            success: function (result) {
                if (result.success == true) {
                    $('div.tax_group_modal').modal('hide');
                    toastr.success(result.msg);
                    tax_groups_table.ajax.reload();
                } else {
                    toastr.error(result.msg);
                }
            },
        });
    });
    $(document).on('click', 'button.delete_tax_group_button', function () {
        swal({
            title: LANG.sure,
            text: LANG.confirm_tax_group,
            icon: 'warning',
            buttons: true,
            dangerMode: true,
        }).then((willDelete) => {
            if (willDelete) {
                var href = $(this).data('href');
                var data = $(this).serialize();
                $.ajax({
                    method: 'DELETE',
                    url: href,
                    dataType: 'json',
                    data: data,
                    success: function (result) {
                        if (result.success == true) {
                            toastr.success(result.msg);
                            tax_groups_table.ajax.reload();
                        } else {
                            toastr.error(result.msg);
                        }
                    },
                });
            }
        });
    });
    $(document).on('click', '.option-div-group .option-div', function () {
        $(this)
            .closest('.option-div-group')
            .find('.option-div')
            .each(function () {
                $(this).removeClass('active');
            });
        $(this).addClass('active');
        $(this).find('input:radio').prop('checked', true).change();
    });
    $(document).on('change', 'input[type=radio][name=scheme_type]', function () {
        $('#invoice_format_settings').removeClass('hide');
        var scheme_type = $(this).val();
        if (scheme_type == 'blank') {
            $('#prefix').val('').attr('placeholder', 'XXXX').prop('disabled', false);
        } else if (scheme_type == 'year') {
            var d = new Date();
            var this_year = d.getFullYear();
            $('#prefix')
                .val(this_year + '-')
                .attr('placeholder', '')
                .prop('disabled', true);
        }
        show_invoice_preview();
    });
    $(document).on('change', '#prefix', function () {
        show_invoice_preview();
    });
    $(document).on('keyup', '#prefix', function () {
        show_invoice_preview();
    });
    $(document).on('keyup', '#start_number', function () {
        show_invoice_preview();
    });
    $(document).on('change', '#total_digits', function () {
        show_invoice_preview();
    });
    var invoice_table = $('#invoice_table').DataTable({
        processing: true,
        serverSide: true,
        bPaginate: false,
        buttons: [],
        ajax: '/invoice-schemes',
        columnDefs: [{ targets: 4, orderable: false, searchable: false }],
    });
    $(document).on('submit', 'form#invoice_scheme_add_form', function (e) {
        e.preventDefault();
        $(this).find('button[type="submit"]').attr('disabled', true);
        var data = $(this).serialize();
        $.ajax({
            method: 'POST',
            url: $(this).attr('action'),
            dataType: 'json',
            data: data,
            success: function (result) {
                if (result.success == true) {
                    $('div.invoice_modal').modal('hide');
                    $('div.invoice_edit_modal').modal('hide');
                    toastr.success(result.msg);
                    invoice_table.ajax.reload();
                } else {
                    toastr.error(result.msg);
                }
            },
        });
    });
    $(document).on('click', 'button.set_default_invoice', function () {
        var href = $(this).data('href');
        var data = $(this).serialize();
        $.ajax({
            method: 'get',
            url: href,
            dataType: 'json',
            data: data,
            success: function (result) {
                if (result.success === true) {
                    toastr.success(result.msg);
                    invoice_table.ajax.reload();
                } else {
                    toastr.error(result.msg);
                }
            },
        });
    });
    $('.invoice_edit_modal').on('shown.bs.modal', function () {
        show_invoice_preview();
    });
    $(document).on('click', 'button.delete_invoice_button', function () {
        swal({
            title: LANG.sure,
            text: LANG.delete_invoice_confirm,
            icon: 'warning',
            buttons: true,
            dangerMode: true,
        }).then((willDelete) => {
            if (willDelete) {
                var href = $(this).data('href');
                var data = $(this).serialize();
                $.ajax({
                    method: 'DELETE',
                    url: href,
                    dataType: 'json',
                    data: data,
                    success: function (result) {
                        if (result.success === true) {
                            toastr.success(result.msg);
                            invoice_table.ajax.reload();
                        } else {
                            toastr.error(result.msg);
                        }
                    },
                });
            }
        });
    });
    $('#add_barcode_settings_form').validate();
    $(document).on('change', '#is_continuous', function () {
        if ($(this).is(':checked')) {
            $('.stickers_per_sheet_div').addClass('hide');
            $('.paper_height_div').addClass('hide');
        } else {
            $('.stickers_per_sheet_div').removeClass('hide');
            $('.paper_height_div').removeClass('hide');
        }
    });
    $('input[type="checkbox"].input-icheck, input[type="radio"].input-icheck').iCheck({
        checkboxClass: 'icheckbox_square-blue',
        radioClass: 'iradio_square-blue',
    });
    $(document).on('ifChecked', '.check_all', function () {
        $(this)
            .closest('.check_group')
            .find('.input-icheck')
            .each(function () {
                $(this).iCheck('check');
            });
    });
    $(document).on('ifUnchecked', '.check_all', function () {
        $(this)
            .closest('.check_group')
            .find('.input-icheck')
            .each(function () {
                $(this).iCheck('uncheck');
            });
    });
    $('.check_all').each(function () {
        var length = 0;
        var checked_length = 0;
        $(this)
            .closest('.check_group')
            .find('.input-icheck')
            .each(function () {
                length += 1;
                if ($(this).iCheck('update')[0].checked) {
                    checked_length += 1;
                }
            });
        length = length - 1;
        if (checked_length != 0 && length == checked_length) {
            $(this).iCheck('check');
        }
    });
    business_locations = $('#business_location_table').DataTable({
        processing: true,
        serverSide: true,
        bPaginate: false,
        buttons: [],
        ajax: '/business-location',
        columnDefs: [{ targets: 10, orderable: false, searchable: false }],
    });
    $('.location_add_modal, .location_edit_modal').on('shown.bs.modal', function (e) {
        $('form#business_location_add_form')
            .submit(function (e) {
                e.preventDefault();
            })
            .validate({
                rules: {
                    location_id: {
                        remote: {
                            url: '/business-location/check-location-id',
                            type: 'post',
                            data: {
                                location_id: function () {
                                    return $('#location_id').val();
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
                messages: { location_id: { remote: LANG.location_id_already_exists } },
                submitHandler: function (form) {
                    e.preventDefault();
                    $(form).find('button[type="submit"]').attr('disabled', true);
                    var data = $(form).serialize();
                    $.ajax({
                        method: 'POST',
                        url: $(form).attr('action'),
                        dataType: 'json',
                        data: data,
                        success: function (result) {
                            if (result.success == true) {
                                $('div.location_add_modal').modal('hide');
                                $('div.location_edit_modal').modal('hide');
                                toastr.success(result.msg);
                                business_locations.ajax.reload();
                            } else {
                                toastr.error(result.msg);
                            }
                        },
                    });
                },
            });
    });
    if ($('#header_text').length) {
        CKEDITOR.replace('header_text', { customConfig: '/AdminLTE/plugins/ckeditor/config.js' });
    }
    if ($('#footer_text').length) {
        CKEDITOR.replace('footer_text', { customConfig: '/AdminLTE/plugins/ckeditor/config.js' });
    }
    var expense_cat_table = $('#expense_category_table').DataTable({
        processing: true,
        serverSide: true,
        ajax: '/expense-categories',
        columnDefs: [{ targets: 2, orderable: false, searchable: false }],
    });
    $(document).on('submit', 'form#expense_category_add_form', function (e) {
        e.preventDefault();
        var data = $(this).serialize();
        $.ajax({
            method: 'POST',
            url: $(this).attr('action'),
            dataType: 'json',
            data: data,
            success: function (result) {
                if (result.success === true) {
                    $('div.expense_category_modal').modal('hide');
                    toastr.success(result.msg);
                    expense_cat_table.ajax.reload();
                } else {
                    toastr.error(result.msg);
                }
                var expense_category_id = result.expense_category_id;
                if ($('#expense_quick_add').val()) {
                    get_expense_categories_drop_down(expense_category_id);
                    $('.view_modal').modal('hide');
                }
            },
        });
    });

    function get_expense_categories_drop_down(expense_category_id) {
        $.ajax({
            method: 'get',
            url: '/expense-categories/get-drop-down',
            contentType: 'html',
            data: {},
            success: function (result) {
                $('#expense_category').empty().append(result);
                $('#expense_category').val(expense_category_id).trigger('change');
            },
        });
    }
    $(document).on('click', 'button.delete_expense_category', function () {
        swal({
            title: LANG.sure,
            text: LANG.confirm_delete_expense_category,
            icon: 'warning',
            buttons: true,
            dangerMode: true,
        }).then((willDelete) => {
            if (willDelete) {
                var href = $(this).data('href');
                var data = $(this).serialize();
                $.ajax({
                    method: 'DELETE',
                    url: href,
                    dataType: 'json',
                    data: data,
                    success: function (result) {
                        if (result.success === true) {
                            toastr.success(result.msg);
                            expense_cat_table.ajax.reload();
                        } else {
                            toastr.error(result.msg);
                        }
                    },
                });
            }
        });
    });
    if ($('#expense_date_range').length == 1) {
        $('#expense_date_range').daterangepicker(dateRangeSettings, function (start, end) {
            $('#expense_date_range').val(
                start.format(moment_date_format) + ' ~ ' + end.format(moment_date_format)
            );
            expense_table.ajax.reload();
        });
        $('#expense_date_range').on('cancel.daterangepicker', function (ev, picker) {
            $('#product_sr_date_filter').val('');
            expense_table.ajax.reload();
        });
        $('#expense_date_range').data('daterangepicker').setStartDate(moment().startOf('month'));
        $('#expense_date_range').data('daterangepicker').setEndDate(moment().endOf('month'));
    }
    expense_table = $('#expense_table').DataTable({
        processing: true,
        serverSide: true,
        aaSorting: [[1, 'desc']],
        ajax: {
            url: '/expenses',
            data: function (d) {
                d.expense_for = $('select#expense_for').val();
                d.location_id = $('select#location_id').val();
                d.expense_category_id = $('select#expense_category_id').val();
                d.payment_status = $('select#expense_payment_status').val();
                d.start_date = $('input#expense_date_range')
                    .data('daterangepicker')
                    .startDate.format('YYYY-MM-DD');
                d.end_date = $('input#expense_date_range')
                    .data('daterangepicker')
                    .endDate.format('YYYY-MM-DD');
            },
        },
        columns: [
            { data: 'action', name: 'action', orderable: false, searchable: false },
            { data: 'transaction_date', name: 'transaction_date' },
            { data: 'ref_no', name: 'ref_no' },
            { data: 'category', name: 'ec.name' },
            { data: 'location_name', name: 'bl.name' },
            { data: 'payment_status', name: 'payment_status', orderable: false },
            { data: 'tax', name: 'tr.name' },
            { data: 'final_total', name: 'final_total' },
            { data: 'payment_due', name: 'payment_due' },
            { data: 'payment_method', name: 'payment_method' },
            { data: 'expense_for', name: 'expense_for' },
            { data: 'additional_notes', name: 'additional_notes' },
            { data: 'created_by', name: 'created_by' },
        ],
        fnDrawCallback: function (oSettings) {
            var expense_total = sum_table_col($('#expense_table'), 'final-total');
            $('#footer_expense_total').text(expense_total);
            var total_due = sum_table_col($('#expense_table'), 'payment_due');
            $('#footer_total_due').text(total_due);
            $('#footer_payment_status_count').html(
                __sum_status_html($('#expense_table'), 'payment-status')
            );
            __currency_convert_recursively($('#expense_table'));
        },
        createdRow: function (row, data, dataIndex) {
            $(row).find('td:eq(4)').attr('class', 'clickable_td');
        },
    });
    $(
        'select#location_id, select#expense_for, select#expense_category_id, select#expense_payment_status'
    ).on('change', function () {
        expense_table.ajax.reload();
    });
    $('#expense_transaction_date').datetimepicker({
        format: moment_date_format + ' ' + moment_time_format,
        ignoreReadonly: true,
    });
    $(document).on('click', 'a.delete_expense', function (e) {
        e.preventDefault();
        swal({
            title: LANG.sure,
            text: LANG.confirm_delete_expense,
            icon: 'warning',
            buttons: true,
            dangerMode: true,
        }).then((willDelete) => {
            if (willDelete) {
                var href = $(this).data('href');
                var data = $(this).serialize();
                $.ajax({
                    method: 'DELETE',
                    url: href,
                    dataType: 'json',
                    data: data,
                    success: function (result) {
                        if (result.success === true) {
                            toastr.success(result.msg);
                            expense_table.ajax.reload();
                        } else {
                            toastr.error(result.msg);
                        }
                    },
                });
            }
        });
    });
    $(document).on('change', '.payment_types_dropdown', function () {
        var payment_type = $(this).val();
        var to_show = null;
        var cheque_field = null;
        var location_id = $('#location_id').val();
        var row_id = parseInt($(this).closest('.payment_row').data('row_id'));
        $(this)
            .closest('.payment_row')
            .find('.payment_details_div')
            .each(function () {
                if ($(this).attr('data-type') == 'cheque') {
                    cheque_field = $(this);
                }
                if ($(this).attr('data-type') == payment_type) {
                    to_show = $(this);
                } else {
                    if (!$(this).hasClass('hide')) {
                        $(this).addClass('hide');
                    }
                }
            });
        $('.card_type_div').addClass('hide');
        if (to_show && to_show.hasClass('hide')) {
            to_show.removeClass('hide');
            to_show.find('input').filter(':visible:first').focus();
        }
        if (payment_type == 'bank_transfer') {
            if (cheque_field) {
                cheque_field.removeClass('hide');
                cheque_field.find('input').filter(':visible:first').focus();
            }
        }

        if (payment_type == 'bank_transfer' || payment_type == 'direct_bank_deposit') {
            $(this).closest('.payment_row').find('.account_module').removeClass('hide');
            $.ajax({
                method: 'get',
                url: '/accounting-module/get-account-group-name-dp',
                data: { group_name: 'Bank Account', location_id: location_id },
                contentType: 'html',
                success: function (result) {
                    if (row_id >= 0) {
                        $('#account_' + row_id)
                            .empty()
                            .append(result);
                        $('#account_' + row_id).attr('required', true);
                    } else {
                        $('#account_id').attr('required', true);
                        $('#account_id').empty().append(result);
                    }
                },
            });
        } else {
            $(this).closest('.payment_row').find('.account_id').attr('required', false);
            $(this).closest('.payment_row').find('.account_module').addClass('hide');
            $('#account_id').attr('required', false);
        }
        if (payment_type == 'cheque') {
            $.ajax({
                method: 'get',
                url: '/accounting-module/get-account-group-name-dp',
                data: { group_name: "Cheques in Hand (Customer's)", location_id: location_id },
                contentType: 'html',
                success: function (result) {
                    if (row_id >= 0) {
                        $('#account_' + row_id)
                            .empty()
                            .append(result);
                        check_insufficient_balance_row(row_id);
                    } else {
                        $('#account_id').empty().append(result);
                        $('#account_id')
                            .val($('#account_id option:contains("Cheques in Hand")').val())
                            .trigger('change');
                    }
                },
            });
        }
        if (payment_type == 'cash') {
            $.ajax({
                method: 'get',
                url: '/accounting-module/get-account-group-name-dp',
                data: { group_name: 'Cash Account', location_id: location_id },
                contentType: 'html',
                success: function (result) {
                    if (row_id >= 0) {
                        $('#account_' + row_id)
                            .empty()
                            .append(result);
                        check_insufficient_balance_row(row_id);
                    } else {
                        $('#account_id').empty().append(result);
                        $('#account_id')
                            .val($('#account_id option:contains("Cash")').val())
                            .trigger('change');
                    }
                },
            });
        }
        if (payment_type == 'card') {
            $('.card_type_div').removeClass('hide');
            $.ajax({
                method: 'get',
                url: '/accounting-module/get-account-group-name-dp',
                data: { group_name: 'Card', location_id: location_id },
                contentType: 'html',
                success: function (result) {
                    if (row_id >= 0) {
                        $('#account_' + row_id)
                            .empty()
                            .append(result);
                        check_insufficient_balance_row(row_id);
                    } else {
                        $('#account_id').empty().append(result);
                        $('#account_id')
                            .val(
                                $(
                                    '#account_id option:contains("Cards (Credit Debit)  Account")'
                                ).val()
                            )
                            .trigger('change');
                    }
                },
            });
        }

        edit_cheque_date = $(this)
        .closest('.payment_row')
        .find('.payment_details_div')
        .find('#payment_edit_cheque')
        .val();
        if (edit_cheque_date) {
            $(this)
            .closest('.payment_row')
            .find('.payment_details_div')
            .find('.cheque_date')
            .datepicker('setDate', edit_cheque_date);
        }else{
            $(this)
                .closest('.payment_row')
                .find('.payment_details_div')
                .find('.cheque_date')
                .datepicker('setDate', new Date());
        }
    });

    if ($('form#add_printer_form').length == 1) {
        printer_connection_type_field($('select#connection_type').val());
        $('select#connection_type').change(function () {
            var ctype = $(this).val();
            printer_connection_type_field(ctype);
        });
        $('form#add_printer_form').validate();
    }
    if ($('form#bl_receipt_setting_form').length == 1) {
        if ($('select#receipt_printer_type').val() == 'printer') {
            $('div#location_printer_div').removeClass('hide');
        } else {
            $('div#location_printer_div').addClass('hide');
        }
        $('select#receipt_printer_type').change(function () {
            var printer_type = $(this).val();
            if (printer_type == 'printer') {
                $('div#location_printer_div').removeClass('hide');
            } else {
                $('div#location_printer_div').addClass('hide');
            }
        });
        $('form#bl_receipt_setting_form').validate();
    }
    $(document).on('click', 'a.pay_purchase_due, a.pay_sale_due', function (e) {
        e.preventDefault();
        $.ajax({
            url: $(this).attr('href'),
            dataType: 'html',
            success: function (result) {
                $('.pay_contact_due_modal').html(result).modal('show');
                __currency_convert_recursively($('.pay_contact_due_modal'));
                $('#paid_on').datetimepicker({
                    format: moment_date_format + ' ' + moment_time_format,
                    ignoreReadonly: true,
                });
                $('.pay_contact_due_modal').find('form#pay_contact_due_form').validate();
            },
        });
    });
    $('#view_todays_profit').click(function () {
        $('#todays_profit_modal').modal('show');
    });
    $('#todays_profit_modal').on('shown.bs.modal', function () {
        var start = $('#modal_today').val();
        var end = start;
        var location_id = '';
        updateProfitLoss(start, end, location_id);
    });
    $(document).on('click', 'a.print-invoice', function (e) {
        e.preventDefault();
        var href = $(this).data('href');
        $.ajax({
            method: 'GET',
            url: href,
            dataType: 'json',
            success: function (result) {
                if (result.success == 1 && result.receipt.html_content != '') {
                    $('#receipt_section').html(result.receipt.html_content);
                    __currency_convert_recursively($('#receipt_section'));
                    __print_receipt('receipt_section');
                } else {
                    toastr.error(result.msg);
                }
            },
        });
    });
    var sales_commission_agent_table = $('#sales_commission_agent_table').DataTable({
        processing: true,
        serverSide: true,
        ajax: '/sales-commission-agents',
        columnDefs: [{ targets: 2, orderable: false, searchable: false }],
        columns: [
            { data: 'full_name' },
            { data: 'email' },
            { data: 'contact_no' },
            { data: 'address' },
            { data: 'commission_type' },
            { data: 'cmmsn_percent' },
            { data: 'cmmsn_application' },
            { data: 'action' },
        ],
    });
    $('div.commission_agent_modal').on('shown.bs.modal', function (e) {
        $('form#sale_commission_agent_form')
            .submit(function (e) {
                e.preventDefault();
            })
            .validate({
                submitHandler: function (form) {
                    e.preventDefault();
                    var data = $(form).serialize();
                    $.ajax({
                        method: $(form).attr('method'),
                        url: $(form).attr('action'),
                        dataType: 'json',
                        data: data,
                        success: function (result) {
                            if (result.success == true) {
                                $('div.commission_agent_modal').modal('hide');
                                toastr.success(result.msg);
                                sales_commission_agent_table.ajax.reload();
                            } else {
                                toastr.error(result.msg);
                            }
                        },
                    });
                },
            });
    });
    $(document).on('click', 'button.delete_commsn_agnt_button', function () {
        swal({ title: LANG.sure, icon: 'warning', buttons: true, dangerMode: true }).then(
            (willDelete) => {
                if (willDelete) {
                    var href = $(this).data('href');
                    var data = $(this).serialize();
                    $.ajax({
                        method: 'DELETE',
                        url: href,
                        dataType: 'json',
                        data: data,
                        success: function (result) {
                            if (result.success == true) {
                                toastr.success(result.msg);
                                sales_commission_agent_table.ajax.reload();
                            } else {
                                toastr.error(result.msg);
                            }
                        },
                    });
                }
            }
        );
    });
    $('button#full_screen').click(function (e) {
        element = document.documentElement;
        if (screenfull.enabled) {
            screenfull.toggle(element);
        }
    });
    var customer_groups_table = $('#customer_groups_table').DataTable({
        processing: true,
        serverSide: true,
        ajax: '/contact-group?type=customer',
        columnDefs: [{ targets: 2, orderable: false, searchable: false }],
    });
    $(document).on('submit', 'form#contact_group_add_form', function (e) {
        e.preventDefault();
        var data = $(this).serialize();
        $.ajax({
            method: 'POST',
            url: $(this).attr('action'),
            dataType: 'json',
            data: data,
            success: function (result) {
                if (result.success == true) {
                    $('div.contact_groups_modal ').modal('hide');
                    toastr.success(result.msg);
                    customer_groups_table.ajax.reload();
                    supplier_groups_table.ajax.reload();
                } else {
                    toastr.error(result.msg);
                }
            },
        });
    });
    $(document).on('click', 'button.edit_contact_group_button', function () {
        $('div.contact_groups_modal ').load($(this).data('href'), function () {
            $(this).modal('show');
            $('form#contact_group_edit_form').submit(function (e) {
                e.preventDefault();
                var data = $(this).serialize();
                $.ajax({
                    method: 'POST',
                    url: $(this).attr('action'),
                    dataType: 'json',
                    data: data,
                    success: function (result) {
                        if (result.success == true) {
                            $('div.contact_groups_modal ').modal('hide');
                            toastr.success(result.msg);
                            customer_groups_table.ajax.reload();
                            supplier_groups_table.ajax.reload();
                        } else {
                            toastr.error(result.msg);
                        }
                    },
                });
            });
        });
    });
    $(document).on('click', 'button.delete_contact_group_button', function () {
        swal({
            title: LANG.sure,
            text: LANG.confirm_delete_customer_group,
            icon: 'warning',
            buttons: true,
            dangerMode: true,
        }).then((willDelete) => {
            if (willDelete) {
                var href = $(this).data('href');
                var data = $(this).serialize();
                $.ajax({
                    method: 'DELETE',
                    url: href,
                    dataType: 'json',
                    data: data,
                    success: function (result) {
                        if (result.success == true) {
                            toastr.success(result.msg);
                            customer_groups_table.ajax.reload();
                            supplier_groups_table.ajax.reload();
                        } else {
                            toastr.error(result.msg);
                        }
                    },
                });
            }
        });
    });

    var supplier_groups_table = $('#supplier_groups_table').DataTable({
        processing: true,
        serverSide: true,
        ajax: '/contact-group?type=supplier',
        columnDefs: [{ targets: 2, orderable: false, searchable: false }],
    });

    $(document).on('click', '.delete-sale', function (e) {
        e.preventDefault();
        swal({ title: LANG.sure, icon: 'warning', buttons: true, dangerMode: true }).then(
            (willDelete) => {
                if (willDelete) {
                    var href = $(this).attr('href');
                    $.ajax({
                        method: 'DELETE',
                        url: href,
                        dataType: 'json',
                        success: function (result) {
                            if (result.success == true) {
                                toastr.success(result.msg);
                                if (typeof sell_table !== 'undefined') {
                                    sell_table.ajax.reload();
                                }
                                if (typeof get_recent_transactions !== 'undefined') {
                                    get_recent_transactions('final', $('div#tab_final'));
                                    get_recent_transactions('draft', $('div#tab_draft'));
                                }
                            } else {
                                toastr.error(result.msg);
                            }
                        },
                    });
                }
            }
        );
    });
    if ($('form#add_invoice_layout_form').length > 0) {
        $('select#design').change(function () {
            if ($(this).val() == 'columnize-taxes') {
                $('div#columnize-taxes').removeClass('hide');
                $('div#columnize-taxes').find('input').removeAttr('disabled', 'false');
            } else {
                $('div#columnize-taxes').addClass('hide');
                $('div#columnize-taxes').find('input').attr('disabled', 'true');
            }
        });
    }
    $(document).on('keyup', 'form#unit_add_form input#actual_name', function () {
        $('form#unit_add_form span#unit_name').text($(this).val());
    });
    $(document).on('keyup', 'form#unit_edit_form input#actual_name', function () {
        $('form#unit_edit_form span#unit_name').text($(this).val());
    });
    $('#user_dob').datepicker({ autoclose: true });
});
$('.quick_add_product_modal').on('shown.bs.modal', function () {
    $('.quick_add_product_modal')
        .find('.select2')
        .each(function () {
            var $p = $(this).parent();
            $(this).select2({ dropdownParent: $p });
        });
    $('.quick_add_product_modal')
        .find('input[type="checkbox"].input-icheck')
        .each(function () {
            $(this).iCheck({
                checkboxClass: 'icheckbox_square-blue',
                radioClass: 'iradio_square-blue',
            });
        });
});
discounts_table = $('#discounts_table').DataTable({
    processing: true,
    serverSide: true,
    ajax: base_path + '/discount',
    columnDefs: [{ targets: [0, 8], orderable: false, searchable: false }],
    aaSorting: [1, 'asc'],
    columns: [
        { data: 'row_select' },
        { data: 'name', name: 'discounts.name' },
        { data: 'starts_at', name: 'starts_at' },
        { data: 'ends_at', name: 'ends_at' },
        { data: 'priority', name: 'priority' },
        { data: 'brand', name: 'b.name' },
        { data: 'category', name: 'c.name' },
        { data: 'location', name: 'l.name' },
        { data: 'action', name: 'action' },
    ],
});
$('.discount_modal').on('shown.bs.modal', function () {
    $('.discount_modal')
        .find('.select2')
        .each(function () {
            var $p = $(this).parent();
            $(this).select2({ dropdownParent: $p });
        });
    $('.discount_modal')
        .find('input[type="checkbox"].input-icheck')
        .each(function () {
            $(this).iCheck({
                checkboxClass: 'icheckbox_square-blue',
                radioClass: 'iradio_square-blue',
            });
        });
    $('.discount_modal .discount_date').datetimepicker({
        format: moment_date_format + ' ' + moment_time_format,
        ignoreReadonly: true,
    });
    $('form#discount_form').validate();
});
$(document).on('submit', 'form#discount_form', function (e) {
    e.preventDefault();
    var data = $(this).serialize();
    $.ajax({
        method: $(this).attr('method'),
        url: $(this).attr('action'),
        dataType: 'json',
        data: data,
        success: function (result) {
            if (result.success == true) {
                $('div.discount_modal').modal('hide');
                toastr.success(result.msg);
                discounts_table.ajax.reload();
            } else {
                toastr.error(result.msg);
            }
        },
    });
});
$(document).on('click', 'button.delete_discount_button', function () {
    swal({ title: LANG.sure, icon: 'warning', buttons: true, dangerMode: true }).then(
        (willDelete) => {
            if (willDelete) {
                var href = $(this).data('href');
                var data = $(this).serialize();
                $.ajax({
                    method: 'DELETE',
                    url: href,
                    dataType: 'json',
                    data: data,
                    success: function (result) {
                        if (result.success == true) {
                            toastr.success(result.msg);
                            discounts_table.ajax.reload();
                        } else {
                            toastr.error(result.msg);
                        }
                    },
                });
            }
        }
    );
});
function printer_connection_type_field(ctype) {
    if (ctype == 'network') {
        $('div#path_div').addClass('hide');
        $('div#ip_address_div, div#port_div').removeClass('hide');
    } else if (ctype == 'windows' || ctype == 'linux') {
        $('div#path_div').removeClass('hide');
        $('div#ip_address_div, div#port_div').addClass('hide');
    }
}
function show_invoice_preview() {
    var prefix = $('#prefix').val();
    var start_number = $('#start_number').val();
    var total_digits = $('#total_digits').val();
    var preview = prefix + pad_zero(start_number, total_digits);
    $('#preview_format').text('#' + preview);
}
function pad_zero(str, max) {
    str = str.toString();
    return str.length < max ? pad_zero('0' + str, max) : str;
}
function get_sub_categories() {
    var cat = $('#category_id').val();
    $.ajax({
        method: 'POST',
        url: '/products/get_sub_categories',
        dataType: 'html',
        data: { cat_id: cat },
        success: function (result) {
            if (result) {
                $('#sub_category_id').html(result);
            }
        },
    });
}
function get_sub_units() {
    if ($('#sub_unit_ids').is(':visible')) {
        var unit_id = $('#unit_id').val();
        $.ajax({
            method: 'GET',
            url: '/products/get_sub_units',
            dataType: 'html',
            data: { unit_id: unit_id },
            success: function (result) {
                if (result) {
                    $('#sub_unit_ids').html(result);
                }
            },
        });
    }
}
function show_product_type_form() {
    if ($('#type').val() == 'combo') {
        $('#enable_stock').iCheck('uncheck');
        $('input[name="woocommerce_disable_sync"]').iCheck('check');
    }
    var action = $('#type').attr('data-action');
    var product_id = $('#type').attr('data-product_id');
    $.ajax({
        method: 'POST',
        url: '/products/product_form_part',
        dataType: 'html',
        data: { type: $('#type').val(), product_id: product_id, action: action },
        success: function (result) {
            if (result) {
                $('#product_form_part').html(result);
                toggle_dsp_input();
            }
        },
    });
}
$(document).on('click', 'table.ajax_view tbody tr', function (e) {
    if (
        !$(e.target).is('td.selectable_td input[type=checkbox]') &&
        !$(e.target).is('td.selectable_td') &&
        !$(e.target).is('td.clickable_td') &&
        !$(e.target).is('a') &&
        !$(e.target).is('button') &&
        !$(e.target).hasClass('label') &&
        !$(e.target).is('li') &&
        $(this).data('href') &&
        !$(e.target).is('i')
    ) {
        $.ajax({
            url: $(this).data('href'),
            dataType: 'html',
            success: function (result) {
                $('.view_modal').html(result).modal('show');
            },
        });
    }
});
$(document).on('click', 'td.clickable_td', function (e) {
    e.preventDefault();
    e.stopPropagation();
    if (e.target.tagName == 'SPAN') {
        return false;
    }
    var link = $(this).find('a');
    if (link.length) {
        if (!link.hasClass('no-ajax')) {
            var href = link.attr('href');
            var container = $('.payment_modal');
            $.ajax({
                url: href,
                dataType: 'html',
                success: function (result) {
                    $(container).html(result).modal('show');
                    __currency_convert_recursively(container);
                },
            });
        }
    }
});
$(document).on('click', 'button.select-all', function () {
    var this_select = $(this).closest('.form-group').find('select');
    this_select.find('option').each(function () {
        $(this).prop('selected', 'selected');
    });
    this_select.trigger('change');
});
$(document).on('click', 'button.deselect-all', function () {
    var this_select = $(this).closest('.form-group').find('select');
    this_select.find('option').each(function () {
        $(this).prop('selected', '');
    });
    this_select.trigger('change');
});
$(document).on('change', 'input.row-select', function () {
    if (this.checked) {
        $(this).closest('tr').addClass('selected');
    } else {
        $(this).closest('tr').removeClass('selected');
    }
});
$(document).on('click', '#select-all-row', function (e) {
    if (this.checked) {
        $(this)
            .closest('table')
            .find('tbody')
            .find('input.row-select')
            .each(function () {
                if (!this.checked) {
                    $(this).prop('checked', true).change();
                }
            });
    } else {
        $(this)
            .closest('table')
            .find('tbody')
            .find('input.row-select')
            .each(function () {
                if (this.checked) {
                    $(this).prop('checked', false).change();
                }
            });
    }
});
$(document).on('click', 'a.view_purchase_return_payment_modal', function (e) {
    e.preventDefault();
    e.stopPropagation();
    var href = $(this).attr('href');
    var container = $('.payment_modal');
    $.ajax({
        url: href,
        dataType: 'html',
        success: function (result) {
            $(container).html(result).modal('show');
            __currency_convert_recursively(container);
        },
    });
});
$(document).on('click', 'a.view_invoice_url', function (e) {
    e.preventDefault();
    $('div.view_modal').load($(this).attr('href'), function () {
        $(this).modal('show');
    });
    return false;
});
$(document).on('click', '.load_more_notifications', function (e) {
    e.preventDefault();
    var this_link = $(this);
    this_link.text(LANG.loading + '...');
    this_link.attr('disabled', true);
    var page = parseInt($('input#notification_page').val()) + 1;
    var href = '/load-more-notifications?page=' + page;
    $.ajax({
        url: href,
        dataType: 'html',
        success: function (result) {
            if ($('li.no-notification').length == 0) {
                $(result).insertBefore(this_link.closest('li'));
            }
            this_link.text(LANG.load_more);
            this_link.removeAttr('disabled');
            $('input#notification_page').val(page);
        },
    });
    return false;
});
$(document).on('click', 'a.load_notifications', function (e) {
    if (!$(this).data('loaded')) {
        e.preventDefault();
        $('li.load_more_li').addClass('hide');
        var this_link = $(this);
        var href = '/load-more-notifications?page=1';
        $('span.notifications_count').html(__fa_awesome());
        $.ajax({
            url: href,
            dataType: 'html',
            success: function (result) {
                $('ul#notifications_list').prepend(result);
                $('span.notifications_count').text('');
                this_link.data('loaded', true);
                $('li.load_more_li').removeClass('hide');
            },
        });
    }
});
$(document).on('click', 'a.delete_purchase_return', function (e) {
    e.preventDefault();
    swal({ title: LANG.sure, icon: 'warning', buttons: true, dangerMode: true }).then(
        (willDelete) => {
            if (willDelete) {
                var href = $(this).attr('href');
                var data = $(this).serialize();
                $.ajax({
                    method: 'DELETE',
                    url: href,
                    dataType: 'json',
                    data: data,
                    success: function (result) {
                        if (result.success == true) {
                            toastr.success(result.msg);
                            purchase_return_table.ajax.reload();
                        } else {
                            toastr.error(result.msg);
                        }
                    },
                });
            }
        }
    );
});
$(document).on('submit', 'form#types_of_service_form', function (e) {
    e.preventDefault();
    var data = $(this).serialize();
    $(this).find('button[type="submit"]').attr('disabled', true);
    $.ajax({
        method: $(this).attr('method'),
        url: $(this).attr('action'),
        dataType: 'json',
        data: data,
        success: function (result) {
            if (result.success == true) {
                $('div.type_of_service_modal').modal('hide');
                toastr.success(result.msg);
                types_of_service_table.ajax.reload();
            } else {
                toastr.error(result.msg);
            }
        },
    });
});
types_of_service_table = $('#types_of_service_table').DataTable({
    processing: true,
    serverSide: true,
    ajax: base_path + '/types-of-service',
    columnDefs: [{ targets: [3], orderable: false, searchable: false }],
    aaSorting: [1, 'asc'],
    columns: [
        { data: 'name', name: 'name' },
        { data: 'description', name: 'description' },
        { data: 'packing_charge', name: 'packing_charge' },
        { data: 'action', name: 'action' },
    ],
    fnDrawCallback: function (oSettings) {
        __currency_convert_recursively($('#types_of_service_table'));
    },
});
$(document).on('click', 'button.delete_type_of_service', function (e) {
    e.preventDefault();
    swal({ title: LANG.sure, icon: 'warning', buttons: true, dangerMode: true }).then(
        (willDelete) => {
            if (willDelete) {
                var href = $(this).data('href');
                var data = $(this).serialize();
                $.ajax({
                    method: 'DELETE',
                    url: href,
                    dataType: 'json',
                    data: data,
                    success: function (result) {
                        if (result.success == true) {
                            toastr.success(result.msg);
                            types_of_service_table.ajax.reload();
                        } else {
                            toastr.error(result.msg);
                        }
                    },
                });
            }
        }
    );
});
$(document).on('submit', 'form#edit_shipping_form', function (e) {
    e.preventDefault();
    var data = $(this).serialize();
    $(this).find('button[type="submit"]').attr('disabled', true);
    $.ajax({
        method: $(this).attr('method'),
        url: $(this).attr('action'),
        dataType: 'json',
        data: data,
        success: function (result) {
            if (result.success == true) {
                $('div.view_modal').modal('hide');
                toastr.success(result.msg);
                sell_table.ajax.reload();
            } else {
                toastr.error(result.msg);
            }
        },
    });
});
$(document).on('show.bs.modal', '.register_details_modal, .close_register_modal', function () {
    __currency_convert_recursively($(this));
});
$('#close_register').click(function () {
    $('.close_register_modal').modal({ backdrop: 'static', keyboard: false });
});
function updateProfitLoss(start = null, end = null, location_id = null) {
    if (start == null) {
        var start = $('#profit_loss_date_filter')
            .data('daterangepicker')
            .startDate.format('YYYY-MM-DD');
    }
    if (end == null) {
        var end = $('#profit_loss_date_filter')
            .data('daterangepicker')
            .endDate.format('YYYY-MM-DD');
    }
    if (location_id == null) {
        var location_id = $('#profit_loss_location_filter').val();
    }
    var data = { start_date: start, end_date: end, location_id: location_id };
    var loader = __fa_awesome();
    var pl_span = $('span#pl_span');
    pl_span
        .find(
            '.opening_stock, .total_transfer_shipping_charges, .closing_stock, .total_sell, .total_purchase, \
        .total_expense, .net_profit, .total_adjustment, .total_recovered, .total_sell_discount, \
        .total_purchase_discount, .total_purchase_return, .total_sell_return, .gross_profit, \
        .total_reward_amount, .total_payroll, .profit_without_expense, .total_sales_on_cost'
        )
        .html(loader);
    $.ajax({
        method: 'GET',
        url: '/reports/profit-loss',
        dataType: 'json',
        data: data,
        success: function (data) {
            pl_span.find('.opening_stock').html(__currency_trans_from_en(data.opening_stock, true));
            pl_span.find('.closing_stock').html(__currency_trans_from_en(data.closing_stock, true));
            pl_span.find('.total_sell').html(__currency_trans_from_en(data.total_sell, true));
            pl_span
                .find('.total_purchase')
                .html(__currency_trans_from_en(data.total_purchase, true));
            pl_span.find('.total_expense').html(__currency_trans_from_en(data.total_expense, true));
            if ($('.total_payroll').length > 0) {
                pl_span
                    .find('.total_payroll')
                    .html(__currency_trans_from_en(data.total_payroll, true));
            }
            if ($('.total_production_cost').length > 0) {
                pl_span
                    .find('.total_production_cost')
                    .html(__currency_trans_from_en(data.total_production_cost, true));
            }
            pl_span.find('.net_profit').html(__currency_trans_from_en(data.net_profit, true));
            pl_span
                .find('.profit_without_expense')
                .html(__currency_trans_from_en(data.total_profit_by_product, true));
            pl_span.find('.gross_profit').html(__currency_trans_from_en(data.gross_profit, true));
            pl_span
                .find('.total_adjustment')
                .html(__currency_trans_from_en(data.total_adjustment, true));
            pl_span
                .find('.total_sales_on_cost')
                .html(__currency_trans_from_en(parseFloat(data.total_sale_cost), true));
            pl_span
                .find('.total_recovered')
                .html(__currency_trans_from_en(data.total_recovered, true));
            pl_span
                .find('.total_purchase_return')
                .html(__currency_trans_from_en(data.total_purchase_return, true));
            pl_span
                .find('.total_transfer_shipping_charges')
                .html(__currency_trans_from_en(data.total_transfer_shipping_charges, true));
            pl_span
                .find('.total_purchase_discount')
                .html(__currency_trans_from_en(data.total_purchase_discount, true));
            pl_span
                .find('.total_sell_discount')
                .html(__currency_trans_from_en(data.total_sell_discount, true));
            pl_span
                .find('.total_reward_amount')
                .html(__currency_trans_from_en(data.total_reward_amount, true));
            pl_span
                .find('.total_sell_return')
                .html(__currency_trans_from_en(data.total_sell_return, true));
            __highlight(data.net_profit, pl_span.find('.net_profit'));
            __highlight(data.net_profit, pl_span.find('.gross_profit'));
            __highlight(data.total_profit_by_product, pl_span.find('.profit_without_expense'));
        },
    });
}
$(document).on('click', 'button.activate-deactivate-location', function () {
    swal({ title: LANG.sure, icon: 'warning', buttons: true, dangerMode: true }).then(
        (willDelete) => {
            if (willDelete) {
                $.ajax({
                    url: $(this).data('href'),
                    dataType: 'json',
                    success: function (result) {
                        if (result.success == true) {
                            toastr.success(result.msg);
                            business_locations.ajax.reload();
                        } else {
                            toastr.error(result.msg);
                        }
                    },
                });
            }
        }
    );
});
$(document).on('click', '.delete-sale-suspend', function (e) {
    e.preventDefault();
    swal({ title: LANG.sure, icon: 'warning', buttons: true, dangerMode: true }).then(
        (willDelete) => {
            if (willDelete) {
                var href = $(this).attr('href');
                $.ajax({
                    method: 'DELETE',
                    url: href,
                    dataType: 'json',
                    success: function (result) {
                        if (result.success == true) {
                            toastr.success(result.msg);
                            (parts = href.split('/')), (last_part = parts[parts.length - 1]);
                            var parent_class = $('.sale-' + last_part)
                                .parent()
                                .prop('className');
                            number = $('.' + parent_class + ' .sale').length;
                            if (number == 1) {
                                $('.sale-' + last_part)
                                    .parent()
                                    .append('<p class="text-center">No records found</p>');
                            }
                            $('.sale-' + last_part).remove();
                        } else {
                            toastr.error(result.msg);
                        }
                    },
                });
            }
        }
    );
});
$('#p_date_of_birth').datepicker({ autoclose: true, endDate: 'today' });
$('#prescription_date').datepicker({ autoclose: true, endDate: 'today' });
$('#pharmacy_date').datepicker({ autoclose: true, endDate: 'today' });
$('#laboratory_date').datepicker({ autoclose: true, endDate: 'today' });
function getDocAndNoteIndexPage() {
    var notable_type = $('#notable_type').val();
    var notable_id = $('#notable_id').val();
    $.ajax({
        method: 'GET',
        dataType: 'html',
        url: '/get-document-note-page',
        async: false,
        data: { notable_type: notable_type, notable_id: notable_id },
        success: function (result) {
            $('.document_note_body').html(result);
        },
    });
}
$(document).on('click', '.docs_and_notes_btn', function () {
    var url = $(this).data('href');
    $.ajax({
        method: 'GET',
        dataType: 'html',
        url: url,
        success: function (result) {
            $('.docus_note_modal').html(result).modal('show');
        },
    });
});
function initialize_dropzone_for_docus_n_notes() {
    var file_names = [];
    if (dropzoneForDocsAndNotes.length > 0) {
        Dropzone.forElement('div#docusUpload').destroy();
    }
    dropzoneForDocsAndNotes = $('div#docusUpload').dropzone({
        url: '/post-document-upload',
        paramName: 'file',
        uploadMultiple: true,
        autoProcessQueue: true,
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
        success: function (file, response) {
            if (response.success) {
                toastr.success(response.msg);
                file_names.push(response.file_name);
                $('input#docus_notes_media').val(file_names);
            } else {
                toastr.error(response.msg);
            }
        },
    });
}
$(document).on('submit', 'form#docus_notes_form', function (e) {
    e.preventDefault();
    var url = $('form#docus_notes_form').attr('action');
    var method = $('form#docus_notes_form').attr('method');
    var data = $('form#docus_notes_form').serialize();
    $.ajax({
        method: method,
        dataType: 'json',
        url: url,
        data: data,
        success: function (result) {
            if (result.success) {
                $('.docus_note_modal').modal('hide');
                toastr.success(result.msg);
                documents_and_notes_data_table.ajax.reload();
            } else {
                toastr.error(result.msg);
            }
        },
    });
});
$(document).on('click', '#delete_docus_note', function (e) {
    e.preventDefault();
    var url = $(this).data('href');
    swal({ title: LANG.sure, icon: 'warning', buttons: true, dangerMode: true }).then(
        (confirmed) => {
            if (confirmed) {
                $.ajax({
                    method: 'DELETE',
                    dataType: 'json',
                    url: url,
                    success: function (result) {
                        if (result.success) {
                            toastr.success(result.msg);
                            documents_and_notes_data_table.ajax.reload();
                        } else {
                            toastr.error(result.msg);
                        }
                    },
                });
            }
        }
    );
});
$(document).on('click', '.view_a_docs_note', function () {
    var url = $(this).data('href');
    $.ajax({
        method: 'GET',
        dataType: 'html',
        url: url,
        success: function (result) {
            $('.view_modal').html(result).modal('show');
        },
    });
});
function initializeDocumentAndNoteDataTable() {
    documents_and_notes_data_table = $('#documents_and_notes_table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '/note-documents',
            data: function (d) {
                d.notable_id = $('#notable_id').val();
                d.notable_type = $('#notable_type').val();
            },
        },
        columnDefs: [{ targets: [0, 2, 4], orderable: false, searchable: false }],
        aaSorting: [[3, 'asc']],
        columns: [
            { data: 'action', name: 'action' },
            { data: 'heading', name: 'heading' },
            { data: 'createdBy' },
            { data: 'created_at', name: 'created_at' },
            { data: 'updated_at', name: 'updated_at' },
        ],
    });
}

function Insufficient_balance_swal() {
    swal({
        title: 'Transaction Not Allowed. Insufficient balance Amount',
        icon: 'error',
        buttons: true,
        dangerMode: true,
    });
}


$(document).on('change', '.sub_unit', function () {
    multiplier = $(this).data('multiplier');
    table = $(this).closest('table');
    table_id = table.attr('id');
    tr = $(this).closest('tr');
    var multiplier = 1;
    var unit_name = '';
    var unit_price = 0;
    var purchase_price = 0;
    var current_stock = 0;
    var total_sold = 0;
    var current_stock_price = 0;
    var total_sold_value = 0;
    var quantity = tr.find('select.quantity').length; // in items report
    var purchase_qty = tr.find('select.purchase_qty').length; // in product purchase report
    var sell_qty = tr.find('select.sell_qty').length; // in product sell report
    var sub_unit_length = tr.find('select.sub_unit').length;
    if (sub_unit_length > 0) {
        var select = tr.find('select.sub_unit');
        multiplier = parseFloat(select.find(':selected').data('multiplier'));
        unit_name = select.find(':selected').data('unit_name');
        unit_price = parseFloat(select.find(':selected').data('unit_price'));
        purchase_price = tr.find('span.purchase_price').data('orig-value');
        current_stock = tr.find('span.current_stock').data('orig-value');
        total_sold = tr.find('span.total_sold').data('orig-value');
        total_sold_value = tr.find('span.total_sold_value').data('orig-value');
        current_stock_price = tr.find('span.current_stock_price').data('orig-value');
        quantity = tr.find('span.quantity').data('orig-value');
        purchase_qty = tr.find('span.purchase_qty').data('orig-value');
        sell_qty = tr.find('span.sell_qty').data('orig-value');
    }
    if(unit_price > 0){
      tr.find('span.selling_price').text(__currency_trans_from_en(unit_price, true));
    }else{
      selling_price = tr.find('span.selling_price').data('orig-value');
      new_selling_price = selling_price * multiplier;
      tr.find('span.selling_price').text(__currency_trans_from_en(new_selling_price, true));
    }
    if(purchase_price > 0){
      new_purchase_price = purchase_price * multiplier;
      tr.find('span.purchase_price').text(__currency_trans_from_en(new_purchase_price, true));
    }
    if(current_stock > 0){
      new_current_stock = current_stock / multiplier;
      tr.find('span.current_stock').text(__currency_trans_from_en(new_current_stock, false));
    }
    if(total_sold > 0){
      new_total_sold = total_sold / multiplier;
      tr.find('span.total_sold').text(__currency_trans_from_en(new_total_sold, false));
    }
    if(current_stock_price > 0){
      new_current_stock_price = current_stock_price / multiplier;
      tr.find('span.current_stock_price').text(__currency_trans_from_en(new_current_stock_price, true));
    }
    if(total_sold_value > 0){
      new_total_sold_value = total_sold_value / multiplier;
      tr.find('span.total_sold_value').text(__currency_trans_from_en(new_total_sold_value, true));
    }
    if(quantity > 0){
      new_quantity = quantity / multiplier;
      tr.find('span.quantity').text(__currency_trans_from_en(new_quantity, false));
    }
    if(purchase_qty > 0){
      new_purchase_qty = purchase_qty / multiplier;
      tr.find('span.purchase_qty').text(__currency_trans_from_en(new_purchase_qty, false));
    }
    if(sell_qty > 0){
      new_sell_qty = sell_qty / multiplier;
      tr.find('span.sell_qty').text(__currency_trans_from_en(new_sell_qty, false));
    }
    tr.find('span.unit_name').text(unit_name);
  
  })
