$(document).ready(function () {
    const bornDateField = $('input[name=born]'),
        emailField = $('input[name=email]'),
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
        avatarBlock = $('#avatar-block .avatar.cir'),
        avatarContainer = $('#avatar-container'),

        saveButton =$('#account-save'),
        emailRegExp = /^[a-zA-Z0-9][\-_\.\+\!\#\$\%\&\'\*\/\=\?\^\`\{\|]{0,1}([a-zA-Z0-9][\-_\.\+\!\#\$\%\&\'\*\/\=\?\^\`\{\|]{0,1})*[a-zA-Z0-9]@[a-zA-Z0-9][-\.]{0,1}([a-zA-Z][-\.]{0,1})*[a-zA-Z0-9]\.[a-zA-Z0-9]{1,}([\.\-]{0,1}[a-zA-Z]){0,}[a-zA-Z0-9]{0,}$/gi

    window.avatarImage = null;
    window.avatarSize = 100;
    window.avatarHeight = 0;

    $.mask.definitions['c'] = "[1-2]";
    bornDateField.mask("99-99-9999");

    const preValidationChangeAccount = (e) => {
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
            (born[2] === currentDate.getFullYear() - 18 && born[1] < currentDate.getMonth()) ||
            (born[2] === currentDate.getFullYear() - 18 && born[1] === currentDate.getMonth() && born[0] < currentDate.getDate())
        ) {
            bornDateField.addClass('error');
            $('.error.born').html(errorBornMessage);
            validationFlag = false;
        }

        if (emailField.val().length && !emailField.val().match(emailRegExp)) {
            emailField.addClass('error');
            $('.error.email').html(errorWrongValue);
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
    $.each(['name','family','born', 'email'], (k, field) => {
        $('input[name='+field+']').on('change', preValidationChangeAccount).keyup(preValidationChangeAccount);
    });

    const unlockGetCodeAndChangePhoneButtons = () => {
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

    const unlockChangePasswordButton = () => {
        if (oldPassword.val().length && passwordField.val().length && passwordConfirmField.val().length)
            changePasswordButton.removeAttr('disabled');
        else changePasswordButton.attr('disabled','disabled');
    };

    //Unlock change password button
    oldPassword.on('change', unlockChangePasswordButton).keyup(unlockChangePasswordButton);
    passwordField.on('change', unlockChangePasswordButton).keyup(unlockChangePasswordButton);
    passwordConfirmField.on('change', unlockChangePasswordButton).keyup(unlockChangePasswordButton);

    const preValidationChangePassword = (e) => {
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

    // Save account form
    saveButton.click((e) => {
        e.preventDefault();
        if (preValidationChangeAccount) {
            getUrl(saveButton.parents('form'), null, (data) => {
                messageModal.find('h4').html(data.message);
                messageModal.modal('show');
            });
        }
    });

    // Init slider
    $(".ui-slider-value").slider({
        value: 0,
        min: -100,
        max: 100,
        slide: function (event, ui) {
            window.avatarSize = ui.value;
            avatarContainer.css({
                'justify-content': 'start',
                'align-items': 'start'
            });

            if (!window.avatarHeight) window.avatarHeight = window.avatarImage.height();
            window.avatarImage.css({
                'width': 200 + ui.value * 2,
                'height': window.avatarHeight + (window.avatarHeight / 100 * ui.value)
            });
            window.avatarImage.css({
                'top': (200 - window.avatarImage.height()) / 2 + 150,
                'left': (200 - window.avatarImage.width()) / 2 + 150
            });
        }
    });

    // Preview and edit avatar
    let tuneAvatarModal = $('#tune-avatar-modal');
    imagePreview(avatarBlock, '/images/def_avatar.svg', (targetImage) => {
        let avatarCir = $('.avatar.cir'),
            avatarCirBig = $('.avatar.cir.big');

        avatarCir.css({
            'background-size': '100%',
            'background-position-x': 'center',
            'background-position-y': 'center',
        });

        window.avatarImage = $('<img />').attr({
            'id':'tuning-avatar',
            'src':targetImage
        }).css('width',avatarCirBig.width());

        avatarContainer.html('');
        avatarContainer.append(window.avatarImage);
        // window.avatarImage.css('top',(200 - window.avatarImage.height()) / 2 + 150);
        // window.avatarHeight = window.avatarImage.height();
        avatarImage.draggable({
            containment: "#avatar-container"
        });
        tuneAvatarModal.modal('show');
    });

    // Save tune avatar
    $('#save-tune-avatar').click(() => {
        let posX = (parseInt(window.avatarImage.css('left')) - 150) / 200 * 100 * 0.7,
            posY = (parseInt(window.avatarImage.css('top')) - 150) / 200 * 100 * 0.7,
            size = (100 + window.avatarSize);

        if (posX || posY || size) {
            $('#avatar-block .avatar.cir').css({
                'background-position-x': posX,
                'background-position-y': posY,
                'background-size': size + '%'
            });
            $('input[name=avatar_size]').val(size);
            $('input[name=avatar_position_x]').val(posX);
            $('input[name=avatar_position_y]').val(posY);
        }
        tuneAvatarModal.modal('hide');
    });
});
