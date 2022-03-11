$(document).ready(function () {
    $('[data-toggle="tooltip"]').tooltip();
    if ($('#business_register_form').length) {
        var form = $('#business_register_form').show();
        form.steps({
            headerTag: 'h3',
            bodyTag: 'fieldset',
            transitionEffect: 'slideLeft',
            labels: { finish: LANG.register, next: LANG.next, previous: LANG.previous },
            onStepChanging: function (event, currentIndex, newIndex) {
                if (currentIndex > newIndex) {
                    return true;
                }
                if (currentIndex < newIndex) {
                    form.find('.body:eq(' + newIndex + ') label.error').remove();
                    form.find('.body:eq(' + newIndex + ') .error').removeClass('error');
                }
                form.validate().settings.ignore = ':disabled,:hidden';
                return form.valid();
            },
            onFinishing: function (event, currentIndex) {
                form.validate().settings.ignore = ':disabled';
                return form.valid();
            },
            onFinished: function (event, currentIndex) {
                form.submit();
            },
        });
    }
    $('.start-date-picker').datepicker({ autoclose: true, endDate: 'today' });
    $('#p_date_of_birth').datepicker({ autoclose: true, endDate: 'today' });
    $('form#business_register_form').validate({
        errorPlacement: function (error, element) {
            if (element.parent('.input-group').length) {
                error.insertAfter(element.parent());
            } else if (element.parent().hasClass('checkbox')) {
                error.insertAfter(element.closest('.checkbox'));
            } else {
                error.insertAfter(element);
            }
        },
        rules: {
            name: 'required',
            email: {
                email: true,
                remote: {
                    url: '/business/register/check-email',
                    type: 'post',
                    data: {
                        email: function () {
                            return $('#b_email').val();
                        },
                    },
                },
            },
            password: { required: true, minlength: 5 },
            confirm_password: { equalTo: '#b_password' },
            username: {
                required: true,
                minlength: 4,
                remote: {
                    url: '/business/register/check-username',
                    type: 'post',
                    data: {
                        username: function () {
                            return $('#b_username').val();
                        },
                    },
                },
            },
            website: { url: true },
        },
        messages: {
            name: LANG.specify_business_name,
            password: { minlength: LANG.password_min_length },
            confirm_password: { equalTo: LANG.password_mismatch },
            username: { remote: LANG.invalid_username },
            email: { remote: LANG.email_taken },
        },
    });
    $('form#patient_register_form').validate({
        errorPlacement: function (error, element) {
            if (element.parent('.input-group').length) {
                error.insertAfter(element.parent());
            } else if (element.parent().hasClass('checkbox')) {
                error.insertAfter(element.closest('.checkbox'));
            } else {
                error.insertAfter(element);
            }
        },
        rules: {
            name: 'required',
            p_email: {
                email: true,
                remote: {
                    url: '/business/register/check-email',
                    type: 'post',
                    data: {
                        email: function () {
                            return $('#p_email').val();
                        },
                    },
                },
            },
            p_password: { required: true, minlength: 5 },
            p_confirm_password: { equalTo: '#p_password' },
            address: 'required',
            city: 'required',
            state: 'required',
            mobile: 'required',
            gender: 'required',
            marital_status: 'required',
            blood_group: 'required',
            guardian_name: 'required',
            time_zone: 'required',
        },
        messages: {
            name: LANG.specify_business_name,
            password: { minlength: LANG.password_min_length },
            confirm_password: { equalTo: LANG.password_mismatch },
            username: { remote: LANG.invalid_username },
            email: { remote: LANG.email_taken },
        },
    });
    $('form#agent_register_form').validate({
        errorPlacement: function (error, element) {
            if (element.parent('.input-group').length) {
                error.insertAfter(element.parent());
            } else if (element.parent().hasClass('checkbox')) {
                error.insertAfter(element.closest('.checkbox'));
            } else {
                error.insertAfter(element);
            }
        },
        rules: {
            name: 'required',
            a_email: {
                email: true,
                remote: {
                    url: '/business/register/check-email-agent',
                    type: 'post',
                    data: {
                        email: function () {
                            return $('#a_email').val();
                        },
                    },
                },
            },
            username: {
                required: true,
                minlength: 4,
                remote: {
                    url: '/business/register/check-username-agent',
                    type: 'post',
                    data: {
                        username: function () {
                            return $('#username').val();
                        },
                    },
                },
            },
            password: { required: true, minlength: 6 },
            confirm_password: { equalTo: '#a_password' },
            address: 'required',
            mobile_number: 'required',
            nic_number: 'required',
            referral_code: 'required',
            bank_name: 'required',
            account_number: 'required',
            branch: 'required',
            username: 'required',
        },
        messages: {
            password: { minlength: LANG.password_min_length },
            confirm_password: { equalTo: LANG.password_mismatch },
            username: { remote: LANG.invalid_username },
            email: { remote: LANG.email_taken },
        },
    });
    $('#business_logo').fileinput({
        showUpload: false,
        showPreview: false,
        browseLabel: LANG.file_browse_label,
        removeLabel: LANG.remove,
    });

    var img_fileinput_setting = {
        showUpload: false,
        showPreview: true,
        browseLabel: LANG.file_browse_label,
        removeLabel: LANG.remove,
        previewSettings: {
            image: { width: '100%', height: '100%', 'max-width': '100%', 'max-height': '100%' },
        },
    };
    $('#a_nic_copy').fileinput(img_fileinput_setting);
    $('#a_agent_photo').fileinput(img_fileinput_setting);
    $('#fileToImage').fileinput(img_fileinput_setting);
    $('#fileToPayment').fileinput(img_fileinput_setting);

    $(document).on('click', '.box-header', function (e) {
        $(this).children().removeClass('fa-plus').removeClass('fa-minus');
    });
});
