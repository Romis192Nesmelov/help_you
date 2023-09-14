$(() => {
    let messageModal = $('#message-modal'),
        loginModal = $('#login-modal'),
        loginPasswordField = loginModal.find('input[name=password]'),
        loginPasswordFieldError = loginModal.find('.error.password'),
        registerModal = $('#register-modal'),
        getRegisterCode = $('#get-register-code'),
        getRegisterCodeAgain = $('#get-code-again'),
        registerPasswordField = registerModal.find('input[name=password]'),
        registerConfirmPasswordField = registerModal.find('input[name=password_confirmation]'),
        registerAgree = registerModal.find('input[name=i_agree]'),
        registerPasswordFieldError = registerModal.find('.error.password'),
        registerAgreeError = registerModal.find('.error.i_agree'),
        registerConfirmPasswordFieldError = registerModal.find('.error.password_confirmation'),
        resetPasswordModal = $('#restore-password-modal');

    //Login form
    $('#enter-button').click((e) => {
        e.preventDefault();

        loginPasswordField.removeClass('error');
        loginPasswordFieldError.html('');

        if (loginModal.find('input[name=phone]').val().length) {
            if (!loginPasswordField.val().length) {
                loginPasswordField.addClass('error');
                loginPasswordFieldError.html(passwordMustBeEntered);
            } else {
                getUrl(loginModal.find('form'), null, (data) => {
                    loginModal.modal('hide');
                    loginModal.remove();
                    registerModal.remove();
                    resetPasswordModal.remove();
                    $('#login-button').remove();
                    $('#account-button').removeClass('d-none');
                    $('#login-href').remove();
                    $('#account-href').removeClass('d-none');
                    $('.fa.fa-bell-o').removeClass('d-none');
                    $('#right-button-block').removeClass('justify-content-end').addClass('justify-content-between');
                });
            }
        }
    });

    let preValidationRegister = () => {
        if (
            registerModal.find('input[name=phone]').val().length !== 0 &&
            registerPasswordField.val().length !== 0 &&
            registerConfirmPasswordField.val().length !== 0
        ) {
            registerPasswordField.removeClass('error');
            registerConfirmPasswordField.removeClass('error');
            registerAgree.removeClass('error');

            registerPasswordFieldError.html('');
            registerConfirmPasswordFieldError.html('');
            registerAgreeError.html('');

            let validationFlag = true;
            if (!registerAgree.is(':checked')) {
                registerAgree.addClass('error');
                registerAgreeError.html(youMustConsent);
                validationFlag = false;
            }

            if (registerPasswordField.val() !== registerConfirmPasswordField.val()) {
                registerPasswordField.addClass('error');
                registerConfirmPasswordField.addClass('error');
                registerPasswordFieldError.html(passwordsError);
                registerConfirmPasswordFieldError.html(passwordsError);
                validationFlag = false;
            }
            return validationFlag;
        }
        return false;
    }

    //Register form generate code
    getRegisterCode.click((e) => {
        e.preventDefault();
        if (preValidationRegister()) {
            getRegisterCode.addClass('d-none');
            $('#register-button').removeClass('d-none').removeAttr('disabled');
            registerModal.find('.form-group.d-none').removeClass('d-none');
            registerModal.find('input.d-none').removeClass('d-none');
            let timer = 45;
            let countDown = setInterval(() => {
                if (!timer) {
                    getRegisterCode.removeClass('d-none');
                    clearInterval(countDown);
                }
                getRegisterCodeAgain.removeClass('d-none');
                getRegisterCodeAgain.find('span').html(timer);
                timer--;
            }, 1000);
            getUrl(registerModal.find('form'), generateCodeUrl, (data) => {
                messageModal.find('h4').html(data.message);
                messageModal.modal('show');
            });
        }
    });

    //Register form final register
    $('#register-button').click((e) => {
        e.preventDefault();
        if (preValidationRegister() && registerModal.find('input[name=code]').val().length) {
            getUrl(registerModal.find('form'), null, (data) => {
                messageModal.find('h4').html(data.message);
                registerModal.modal('hide');
                messageModal.modal('show');
            });
        }
    });

    //Reset password form
    resetPasswordModal.find('button').click(() => {
        if (resetPasswordModal.find('input[name=phone]').val().length !== 0) {
            getUrl(resetPasswordModal.find('form'), null, (data) => {
                resetPasswordModal.modal('hide');
                messageModal.find('h4').html(data.message);
                messageModal.modal('show');
            });
        }
    });
});

let getUrl = (form, url, callBack) => {
    let formData = new FormData,
        allObjInForm = form.find('input, select, textarea, button');

    allObjInForm.attr('disabled','disabled');
    form.find('input.error').removeClass('error');
    form.find('div.error').html('');

    form.find('input').each(function () {
        formData.append($(this).attr('name'), $(this).val());
    });
    
    $.ajax({
        url: url ? url : form.attr('action'),
        data: formData,
        processData: false,
        contentType: false,
        type: form.attr('method'),
        success: (data) => {
            if (callBack) callBack(data);
            allObjInForm.removeAttr('disabled');
        },
        error: (data) => {
            let response = jQuery.parseJSON(data.responseText);
            $.each(response.errors, (field, errorMsg) => {
                form.find('input[name='+field+']').addClass('error');
                form.find('.error.'+field).html(errorMsg[0]);
            });
            allObjInForm.removeAttr('disabled');
        }
    });
}
