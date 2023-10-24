$(window).on('load', function () {
    const loginModal = $('#login-modal'),
        loginPhoneField = loginModal.find('input[name=phone]'),
        loginPasswordField = loginModal.find('input[name=password]'),
        loginPasswordFieldError = loginModal.find('.error.password'),
        loginButton = $('#enter-button'),

        registerModal = $('#register-modal'),
        registerPhoneField = registerModal.find('input[name=phone]'),
        getRegisterCodeButton = $('#get-register-code'),
        registerPasswordField = registerModal.find('input[name=password]'),
        registerConfirmPasswordField = registerModal.find('input[name=password_confirmation]'),
        registerCodeField = registerModal.find('input[name=code]'),
        registerAgree = registerModal.find('input[name=i_agree]'),
        registerPasswordFieldError = registerModal.find('.error.password'),
        registerAgreeError = registerModal.find('.error.i_agree'),
        registerConfirmPasswordFieldError = registerModal.find('.error.password_confirmation'),
        registerButton = $('#register-button'),

        resetPasswordModal = $('#restore-password-modal'),
        resetPhoneField = resetPasswordModal.find('input[name=phone]'),
        resetButton = $('#reset-button');

    // Click to another links on home page
    // $('.link-cover').click((e) => {
    //     e.preventDefault();
    //     localStorage.setItem('want_url',$(e.target).parents('a').attr('href'));
    //     loginModal.modal('show');
    // });

    let unlockLoginButton = () => {
        if (loginPhoneField.val().match(phoneRegExp)) {
            if (loginPasswordField.val().length) loginButton.removeAttr('disabled');
            else loginButton.attr('disabled','disabled');
            registerPhoneField.val(loginPhoneField.val());
        } else loginButton.attr('disabled','disabled');
    };

    //Unlock login button
    loginPhoneField.on('change',unlockLoginButton);
    loginPasswordField.on('change',unlockLoginButton).keyup(unlockLoginButton);

    //Login form
    loginButton.click((e) => {
        e.preventDefault();

        loginPasswordField.removeClass('error');
        loginPasswordFieldError.html('');

        getUrl(loginModal.find('form'), null, (data) => {
            loginModal.modal('hide');
            if (data.account) {
                loginModal.remove();
                registerModal.remove();
                resetPasswordModal.remove();
                $('#login-button').remove();
                $('#account-button').removeClass('d-none');
                $('#login-href').remove();
                $('#account-href').removeClass('d-none');
                $('#navbar-dropdown-messages').removeClass('d-none');
                $('#right-button-block').removeClass('justify-content-end').addClass('justify-content-between');
                // let wnantUrl = localStorage.getItem('want_url');
                // if (wnantUrl) window.location.href = wnantUrl;
                $.get(
                    getPrevUrl,
                    (data) => {
                        if (data.url) window.location.href = data.url;
                    }
                );
            } else {
                window.location.href = accountUrl;
            }
        });
    });

    let unlockGetCodeAndRegisterButtons = () => {
        if (
            registerPhoneField.val().match(phoneRegExp) &&
            registerPasswordField.val().length &&
            registerConfirmPasswordField.val().length
        ) {
            getRegisterCodeButton.removeAttr('disabled');
            if (registerCodeField.val().match(codeRegExp)) registerButton.removeAttr('disabled');
            else registerButton.attr('disabled','disabled');
        } else {
            getRegisterCodeButton.attr('disabled','disabled');
            registerButton.attr('disabled','disabled');
        }
    };

    //Unlock get code button
    registerPhoneField.on('change',unlockGetCodeAndRegisterButtons);
    registerPasswordField.on('change',unlockGetCodeAndRegisterButtons).keyup(unlockGetCodeAndRegisterButtons);
    registerConfirmPasswordField.on('change',unlockGetCodeAndRegisterButtons).keyup(unlockGetCodeAndRegisterButtons);
    registerCodeField.on('change',unlockGetCodeAndRegisterButtons).keyup(unlockGetCodeAndRegisterButtons);

    //Unlock register button
    registerCodeField.on('change', unlockGetCodeAndRegisterButtons).keyup(unlockGetCodeAndRegisterButtons);

    let preValidationRegister = () => {
        registerPasswordField.removeClass('error');
        registerConfirmPasswordField.removeClass('error');
        registerAgree.removeClass('error');

        registerPasswordFieldError.html('');
        registerConfirmPasswordFieldError.html('');
        registerAgreeError.html('');

        if (!registerAgree.is(':checked')) {
            registerAgree.addClass('error');
            registerAgreeError.html(youMustConsent);
            return false;
        }

        if (registerPasswordField.val() !== registerConfirmPasswordField.val()) {
            registerPasswordField.addClass('error');
            registerConfirmPasswordField.addClass('error');
            registerPasswordFieldError.html(passwordsMismatch);
            registerConfirmPasswordFieldError.html(passwordsMismatch);
            return false;
        } else if (registerPasswordField.val().length < 6) {
            registerPasswordField.addClass('error');
            registerConfirmPasswordField.addClass('error');
            registerPasswordFieldError.html(passwordCannotBeLess);
            registerConfirmPasswordFieldError.html(passwordCannotBeLess);
            return false;
        }
        return true;
    }

    //Register form generate code
    getRegisterCodeButton.click((e) => {
        e.preventDefault();
        if (preValidationRegister()) {
            getCodeAgainCounter(getRegisterCodeButton, 45);
            getUrl(registerModal.find('form'), generateCodeUrl, (data) => {
                getRegisterCodeButton.addClass('d-none');
                registerButton.removeClass('d-none');
                registerModal.find('.form-group.d-none').removeClass('d-none');
                messageModal.find('h4').html(data.message);
                messageModal.modal('show');
            });
        }
    });

    //Register form final register
    registerButton.click((e) => {
        e.preventDefault();
        if (preValidationRegister()) {
            getUrl(registerModal.find('form'), null, (data) => {
                messageModal.find('h4').html(data.message);
                registerModal.modal('hide');
                messageModal.modal('show');
            });
        }
    });

    let unlockResetButton = () => {
        if (resetPhoneField.val().match(phoneRegExp)) resetButton.removeAttr('disabled');
        else resetButton.attr('disabled','disabled');
    };

    //Unlock reset button
    resetPhoneField.on('change',unlockResetButton);

    //Reset password form
    resetButton.click((e) => {
        e.preventDefault();
        getUrl(resetPasswordModal.find('form'), null, (data) => {
            resetPasswordModal.modal('hide');
            messageModal.find('h4').html(data.message);
            messageModal.modal('show');
        });
    });

    let currentUrl = new URL(window.location.href);
    if (currentUrl.searchParams.get('login')) loginModal.modal('show');
});
