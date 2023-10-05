$(() => {
    const bornDateField = $('input[name=born]'),
        changePhoneModal = $('#change-phone-modal'),
        phoneField = changePhoneModal.find('input[name=phone]'),
        codeField = changePhoneModal.find('input[name=code]'),
        getCodeButton = $('#get-code'),
        changePhoneButton = $('#change-phone-button'),

        changePasswordModal = $('#change-password-modal'),
        oldPassword = changePasswordModal.find('input[name=old_password]'),
        passwordField = changePasswordModal.find('input[name=password]'),
        errorPassword = changePasswordModal.find('.error.password'),
        passwordConfirmField = changePasswordModal.find('input[name=password_confirmation]'),
        errorConfirmPassword = changePasswordModal.find('.error.password_confirmation'),
        changePasswordButton = $('#change-password-button'),

        saveButton =$('#account-save'),
        emailRegExp = /^[a-zA-Z0-9][\-_\.\+\!\#\$\%\&\'\*\/\=\?\^\`\{\|]{0,1}([a-zA-Z0-9][\-_\.\+\!\#\$\%\&\'\*\/\=\?\^\`\{\|]{0,1})*[a-zA-Z0-9]@[a-zA-Z0-9][-\.]{0,1}([a-zA-Z][-\.]{0,1})*[a-zA-Z0-9]\.[a-zA-Z0-9]{1,}([\.\-]{0,1}[a-zA-Z]){0,}[a-zA-Z0-9]{0,}$/gi

    $.mask.definitions['c'] = "[1-2]";
    bornDateField.mask("99-99-9999");

    let preValidationChangeAccount = (e) => {
        let form = $(e.target).parents('form'),
            born = bornDateField.val().split('-'),
            currentDate = new Date();

        resetErrors(form);

        let validationFlag = lengthValidate(form, ['name','family','born']);

        if (
            !validationDate(born) ||
            born[2] <= currentDate.getFullYear() - 100 ||
            born[2] > currentDate.getFullYear() ||
            (born[2] >= currentDate.getFullYear() - 18 && born[1] >= currentDate.getMonth() && born[0] < currentDate.getDate()) ||
            born[2] > currentDate.getFullYear() - 18 ||
            (born[2] == currentDate.getFullYear() - 18 && born[1] < currentDate.getMonth()) ||
            (born[2] == currentDate.getFullYear() - 18 && born[1] == currentDate.getMonth() && born[0] < currentDate.getDate())
        ) {
            bornDateField.addClass('error');
            $('.error.born').html(errorBornMessage);
            validationFlag = false;
        }

        if (validationFlag) {
            saveButton.removeAttr('disabled');
            return true;
        } else {
            saveButton.attr('disabled','disabled');
            return false;
        }
    }

    //Unlock save button
    $.each(['name','family','born'], (k, field) => {
        $('input[name='+field+']').on('change', preValidationChangeAccount).keyup(preValidationChangeAccount);
    });

    let unlockGetCodeAndChangePhoneButtons = () => {
        if (phoneField.val().match(phoneRegExp) && phoneField.val().substr(2) !== currentPhone) {
            getCodeButton.removeAttr('disabled');
            if (codeField.val().match(codeRegExp)) changePhoneButton.removeAttr('disabled');
            else changePhoneButton.attr('disabled','disabled');
        } else {
            getCodeButton.attr('disabled','disabled');
            changePhoneButton.attr('disabled','disabled');
        }
    };

    //Unlock get code button
    phoneField.on('change', unlockGetCodeAndChangePhoneButtons);

    //Unlock change phone button
    codeField.on('change', unlockGetCodeAndChangePhoneButtons).keyup(unlockGetCodeAndChangePhoneButtons);

    //Change phone form generate code
    getCodeButton.click((e) => {
        e.preventDefault();
        getCodeButton.addClass('d-none');
        changePhoneButton.removeClass('d-none');
        changePhoneModal.find('.form-group.d-none').removeClass('d-none');

        getCodeAgainCounter(getCodeButton, 45);

        getUrl(changePhoneModal.find('form'), getCodeUrl, (data) => {
            messageModal.find('h4').html(data.message);
            messageModal.modal('show');
        });
    });

    //Change phone form change phone
    changePhoneButton.click((e) => {
        e.preventDefault();
        getUrl(changePhoneModal.find('form'), null, (data) => {
            $('#phone-number').html(data.number);
            messageModal.find('h4').html(data.message);
            changePhoneModal.modal('hide');
            messageModal.modal('show');
        });
    });

    let unlockChangePasswordButton = () => {
        if (oldPassword.val().length && passwordField.val().length && passwordConfirmField.val().length)
            changePasswordButton.removeAttr('disabled');
        else changePasswordButton.attr('disabled','disabled');
    };

    //Unlock change password button
    oldPassword.on('change', unlockChangePasswordButton).keyup(unlockChangePasswordButton);
    passwordField.on('change', unlockChangePasswordButton).keyup(unlockChangePasswordButton);
    passwordConfirmField.on('change', unlockChangePasswordButton).keyup(unlockChangePasswordButton);

    let preValidationChangePassword = (e) => {
        let form = $(e.target).parents('form');

        resetErrors(form);

        if (passwordField.val().length < 6) {
            passwordField.addClass('error');
            errorPassword.html(passwordCannotBeLess);
            return false;
        } else if (passwordField.val() !== passwordConfirmField.val()) {
            passwordField.addClass('error');
            errorPassword.html(passwordsMismatch);
            passwordConfirmField.addClass('error');
            errorConfirmPassword.html(passwordsMismatch);
            return false;
        } else return true;
    };

    //Change password form
    changePasswordButton.click((e) => {
        e.preventDefault();
        if (preValidationChangePassword()) {
            getUrl(changePasswordModal.find('form'), null, (data) => {
                messageModal.find('h4').html(data.message);
                changePasswordModal.modal('hide');
                messageModal.modal('show');
            });
        }
    });

    //Save account form
    saveButton.click((e) => {
        e.preventDefault();
        if (preValidationChangeAccount) {
            getUrl(saveButton.parents('form'), null, (data) => {
                messageModal.find('h4').html(data.message);
                messageModal.modal('show');
            });
        }
    });

    // Preview avatar
    let avatar = $('#avatar-block .avatar.cir'),
        hoverImg = avatar.find('img');

    avatar.find('input[type=file]').change(function () {
        let input = $(this)[0].files[0];
        if (input.type.match('image.*')) {
            let reader = new FileReader();
            reader.onload = function (e) {
                avatar.css('background-image', 'url(' + e.target.result + ')');
            };
            reader.readAsDataURL(input);
        } else {
            avatar.css('background-image', 'url(/images/def_avatar.svg)');
        }
    }).on('mouseover', function () {
        hoverImg.show();
    }).on('mouseout', function () {
        hoverImg.hide();
    });
});
