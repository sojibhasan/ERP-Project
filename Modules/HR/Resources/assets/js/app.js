$(document).ready(function () {
    is_superadmin_page = 0;
    if ($('#is_superadmin_page').val()) {
        var is_superadmin_page = $('#is_superadmin_page').val();
    }

    //departments list
    department_table = $('#department_table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '/hr/settings/department',
            data: function (d) {
                d.is_superadmin_page = is_superadmin_page;
            },
        },
        columns: [
            { data: 'department', name: 'department' },
            { data: 'description', name: 'description' },
            { data: 'action', name: 'action' },
        ],
        fnDrawCallback: function (oSettings) {},
    });

    $(document).on('click', 'a.delete_department', function (e) {
        e.preventDefault();
        swal({
            title: LANG.sure,
            text: 'This department will be deleted.',
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
                            department_table.ajax.reload();
                        } else {
                            toastr.error(result.msg);
                        }
                    },
                });
            }
        });
    });
    $('#filter_business').select2();

    //job_title list
    jobtitle_table = $('#jobtitle_table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '/hr/settings/jobtitle',
            data: function (d) {
                d.is_superadmin_page = is_superadmin_page;
            },
        },
        columns: [
            { data: 'job_title', name: 'job_title' },
            { data: 'description', name: 'description' },
            { data: 'action', name: 'action' },
        ],
        fnDrawCallback: function (oSettings) {},
    });

    $(document).on('click', 'a.delete_jobtitle', function (e) {
        e.preventDefault();
        swal({
            title: LANG.sure,
            text: 'This job title will be deleted.',
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
                            jobtitle_table.ajax.reload();
                        } else {
                            toastr.error(result.msg);
                        }
                    },
                });
            }
        });
    });

    //job_category list
    jobcategory_table = $('#jobcategory_table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '/hr/settings/jobcategory',
            data: function (d) {
                d.is_superadmin_page = is_superadmin_page;
            },
        },
        columns: [
            { data: 'category_name', name: 'category_name' },
            { data: 'action', name: 'action' },
        ],
        fnDrawCallback: function (oSettings) {},
    });

    $(document).on('click', 'a.delete_jobcategory', function (e) {
        e.preventDefault();
        swal({
            title: LANG.sure,
            text: 'This job category will be deleted.',
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
                            jobcategory_table.ajax.reload();
                        } else {
                            toastr.error(result.msg);
                        }
                    },
                });
            }
        });
    });

    //working_shift list
    workshift_table = $('#workshift_table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '/hr/settings/workshift',
            data: function (d) {d.is_superadmin_page = is_superadmin_page},
        },
        columns: [
            { data: 'shift_name', name: 'shift_name' },
            { data: 'shift_form', name: 'shift_form' },
            { data: 'shift_to', name: 'shift_to' },
            { data: 'action', name: 'action' },
        ],
        fnDrawCallback: function (oSettings) {},
    });

    $(document).on('click', 'a.delete_workshift', function (e) {
        e.preventDefault();
        swal({
            title: LANG.sure,
            text: 'This work shift will be deleted.',
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
                            workshift_table.ajax.reload();
                        } else {
                            toastr.error(result.msg);
                        }
                    },
                });
            }
        });
    });

    $('#shif_from').datetimepicker({ format: 'HH:mm a' });
    $('#shif_to').datetimepicker({ format: 'HH:mm a' });

    // holiday_table
    holiday_table = $('#holiday_table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '/hr/settings/holidays',
            data: function (d) {d.is_superadmin_page = is_superadmin_page},
        },
        columnDefs: [
            {
                targets: 4,
                orderable: false,
                searchable: false,
            },
        ],
        columns: [
            { data: 'event_name', name: 'event_name' },
            { data: 'description', name: 'description' },
            { data: 'start_date', name: 'start_date' },
            { data: 'end_date', name: 'end_date' },
            { data: 'action', name: 'action' },
        ],
        fnDrawCallback: function (oSettings) {},
    });
    $(document).on('click', 'a.delete-holiday', function () {
        swal({
            title: LANG.sure,
            icon: 'warning',
            buttons: true,
            dangerMode: true,
        }).then((willDelete) => {
            if (willDelete) {
                let href = $(this).data('href');

                $.ajax({
                    method: 'delete',
                    url: href,
                    data: {},
                    success: function (result) {
                        if (result.success == 1) {
                            toastr.success(result.msg);
                        } else {
                            toastr.error(result.msg);
                        }
                        holiday_table.ajax.reload();
                    },
                });
            }
        });
    });

    $(document).on('click', '#add_holiday_btn', function () {
        $('.holiday_model').modal({
            backdrop: 'static',
            keyboard: false,
        });
    });

    // leave_application_type_table
    leave_application_type_table = $('#leave_application_type_table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '/hr/settings/leave-application-type',
            data: function (d) {d.is_superadmin_page = is_superadmin_page},
        },
        columnDefs: [
            {
                targets: 2,
                orderable: false,
                searchable: false,
            },
        ],
        columns: [
            { data: 'leave_type', name: 'leave_type' },
            { data: 'allowed_days', name: 'allowed_days' },
            { data: 'action', name: 'action' },
        ],
        fnDrawCallback: function (oSettings) {},
    });
    $(document).on('click', 'a.delete-leave_application_type', function () {
        swal({
            title: LANG.sure,
            icon: 'warning',
            buttons: true,
            dangerMode: true,
        }).then((willDelete) => {
            if (willDelete) {
                let href = $(this).data('href');

                $.ajax({
                    method: 'delete',
                    url: href,
                    data: {},
                    success: function (result) {
                        if (result.success == 1) {
                            toastr.success(result.msg);
                        } else {
                            toastr.error(result.msg);
                        }
                        leave_application_type_table.ajax.reload();
                    },
                });
            }
        });
    });

    $(document).on('click', '#add_leave_application_type_btn', function () {
        $('.leave_application_type_model').modal({
            backdrop: 'static',
            keyboard: false,
        });
    });

    // salary_grade_table
    salary_grade_table = $('#salary_grade_table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '/hr/settings/salary-grade',
            data: function (d) {d.is_superadmin_page = is_superadmin_page},
        },
        columnDefs: [
            {
                targets: 3,
                orderable: false,
                searchable: false,
            },
        ],
        columns: [
            { data: 'grade_name', name: 'grade_name' },
            { data: 'min_salary', name: 'min_salary' },
            { data: 'max_salary', name: 'max_salary' },
            { data: 'action', name: 'action' },
        ],
        fnDrawCallback: function (oSettings) {},
    });
    $(document).on('click', 'a.delete-salary_grade', function () {
        swal({
            title: LANG.sure,
            icon: 'warning',
            buttons: true,
            dangerMode: true,
        }).then((willDelete) => {
            if (willDelete) {
                let href = $(this).data('href');

                $.ajax({
                    method: 'delete',
                    url: href,
                    data: {},
                    success: function (result) {
                        if (result.success == 1) {
                            toastr.success(result.msg);
                        } else {
                            toastr.error(result.msg);
                        }
                        salary_grade_table.ajax.reload();
                    },
                });
            }
        });
    });

    $(document).on('click', '#add_salary_grade_btn', function () {
        $('.salary_grade_model').modal({
            backdrop: 'static',
            keyboard: false,
        });
    });

    // employment_status_table
    employment_status_table = $('#employment_status_table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '/hr/settings/employment-status',
            data: function (d) {d.is_superadmin_page = is_superadmin_page},
        },
        columnDefs: [
            {
                targets: 1,
                orderable: false,
                searchable: false,
            },
        ],
        columns: [
            { data: 'status_name', name: 'status_name' },
            { data: 'action', name: 'action' },
        ],
        fnDrawCallback: function (oSettings) {},
    });
    $(document).on('click', 'a.delete-employment_status', function () {
        swal({
            title: LANG.sure,
            icon: 'warning',
            buttons: true,
            dangerMode: true,
        }).then((willDelete) => {
            if (willDelete) {
                let href = $(this).data('href');

                $.ajax({
                    method: 'delete',
                    url: href,
                    data: {},
                    success: function (result) {
                        if (result.success == 1) {
                            toastr.success(result.msg);
                        } else {
                            toastr.error(result.msg);
                        }
                        employment_status_table.ajax.reload();
                    },
                });
            }
        });
    });

    $(document).on('click', '#add_employment_status_btn', function () {
        $('.employment_status_model').modal({
            backdrop: 'static',
            keyboard: false,
        });
    });

    // salary_component_table
    salary_component_table = $('#salary_component_table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '/hr/settings/salary-component',
            data: function (d) {d.is_superadmin_page = is_superadmin_page},
        },
        columnDefs: [
            {
                targets: 2,
                orderable: false,
                searchable: false,
            },
        ],
        columns: [
            { data: 'component_name', name: 'component_name' },
            { data: 'type', name: 'type' },
            { data: 'total_payable', name: 'total_payable' },
            { data: 'cost_company', name: 'cost_company' },
            { data: 'value_type', name: 'value_type' },
            { data: 'component_amount', name: 'component_amount' },
            { data: 'statutory_fund', name: 'statutory_fund' },
            { data: 'action', name: 'action' },
        ],
        fnDrawCallback: function (oSettings) {},
    });
    $(document).on('click', 'a.delete-salary_component', function () {
        swal({
            title: LANG.sure,
            icon: 'warning',
            buttons: true,
            dangerMode: true,
        }).then((willDelete) => {
            if (willDelete) {
                let href = $(this).data('href');

                $.ajax({
                    method: 'delete',
                    url: href,
                    data: {},
                    success: function (result) {
                        if (result.success == 1) {
                            toastr.success(result.msg);
                        } else {
                            toastr.error(result.msg);
                        }
                        salary_component_table.ajax.reload();
                    },
                });
            }
        });
    });

    $(document).on('click', '#add_salary_component_btn', function () {
        $('.salary_component_model').modal({
            backdrop: 'static',
            keyboard: false,
        });
    });

    //tax tab script
    $(document).on('click', '.add_row_btn', function () {
        let index = parseInt($('#index').val());
        $('#tax_table tbody').append(`
    <tr  data-index="${index + 1}" id="${index + 1}">
        <td>${
            index + 2
        } <input class="form-control id" name="tax[${index + 1}][id]" type="hidden"></td>
        <td><input class="form-control name" name="tax[${index + 1}][name]" type="text"></td>
        <td><input class="form-control slab_amount" name="tax[${
            index + 1
        }][slab_amount]" type="number"></td>
        <td><select class="form-control type" name="tax[${
            index + 1
        }][type]"><option selected="selected" value="">Please Select</option><option value="fixed">Fixed</option><option value="percentage">Percentage</option></select></td>
        <td><input class="form-control tax_rate" name="tax[${
            index + 1
        }][tax_rate]" type="number"></td>
        <td><select class="form-control slab_wise_rates" name="tax[${
            index + 1
        }][slab_wise_rates]"><option selected="selected" value="">Please Select</option><option value="yes">Yes</option><option value="no">No</option></select></td>
        <td class="previous_slab_td"><input class="form-control previous_slab" name="tax[${
            index + 1
        }][previous_slab]" value="${index + 1}" type="hidden"></td>
        <td><button type="button" class="btn btn-xs btn-primary add_row_btn"> + </button> &nbsp; <button type="button" class="btn btn-xs btn-danger remove_row_tax">x</button></td>
    </tr>
    `);
        $('#index').val(index + 1);
    });
    $(document).on('click', '.remove_row_tax', function () {
        $('.remove_row_tax').click(function () {
            $(this).parent().parent().remove();
        });
    });
    $(document).on('click', '.slab_wise_rates', function () {
        if ($(this).val() == 'yes') {
            let tr = $(this).parent().parent();
            index = parseInt(tr.data('index'));
            let html = '';
            let j = 1;
            let select_array = [];
            for (var i = 0; i < index; i++) {
                html +=
                    '<span class="btn btn-sm btn-flat btn-primary"  style="margin-bottom:5px;">' +
                    j +
                    '</span> &nbsp';
                select_array.push(j);
                j++;
            }
            tr.find('.previous_slab_td').append(html);
            selected_sting = select_array.join(); //convert to string
            tr.find('.previous_slab').val(selected_sting);
        }
    });

    $(document).on('click', 'a.delete-tax', function () {
        let _this_tr = $(this).parent().parent();
        swal({
            title: LANG.sure,
            icon: 'warning',
            buttons: true,
            dangerMode: true,
        }).then((willDelete) => {
            if (willDelete) {
                let href = $(this).data('href');
                $.ajax({
                    method: 'delete',
                    url: href,
                    data: {},
                    success: function (result) {
                        if (result.success == 1) {
                            _this_tr.remove();
                            toastr.success(result.msg);
                        } else {
                            toastr.error(result.msg);
                        }
                    },
                });
            }
        });
    });

    // religion_table
    religion_table = $('#religion_table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '/hr/settings/religion',
            data: function (d) {d.is_superadmin_page = is_superadmin_page},
        },
        columnDefs: [
            {
                targets: 2,
                orderable: false,
                searchable: false,
            },
        ],
        columns: [
            { data: 'religion_name', name: 'religion_name' },
            { data: 'religion_status', name: 'religion_status' },
            { data: 'action', name: 'action' },
        ],
        fnDrawCallback: function (oSettings) {},
    });
    $(document).on('click', 'a.delete-religion', function () {
        swal({
            title: LANG.sure,
            icon: 'warning',
            buttons: true,
            dangerMode: true,
        }).then((willDelete) => {
            if (willDelete) {
                let href = $(this).data('href');

                $.ajax({
                    method: 'delete',
                    url: href,
                    data: {},
                    success: function (result) {
                        if (result.success == 1) {
                            toastr.success(result.msg);
                        } else {
                            toastr.error(result.msg);
                        }
                        religion_table.ajax.reload();
                    },
                });
            }
        });
    });

    $(document).on('click', '#add_religion_btn', function () {
        $('.religion_model').modal({
            backdrop: 'static',
            keyboard: false,
        });
    });
});
