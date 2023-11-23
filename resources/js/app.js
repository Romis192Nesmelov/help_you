import './bootstrap';
window.phoneRegExp = /^((\+)?(\d)(\s)?(\()?[0-9]{3}(\))?(\s)?([0-9]{3})(\-)?([0-9]{2})(\-)?([0-9]{2}))$/gi;
window.codeRegExp = /^((\d){2}(\-)(\d){2}(\-)(\d){2})$/gi
window.dataTableClasses = '.table.datatable-basic.default';

$(document).ready(function () {
    // MAIN BLOCK BEGIN
    $.mask.definitions['n'] = "[7-8]";
    $('input[name=phone]').mask("+n(999)999-99-99");
    $('input[name=code]').mask("99-99-99");
    window.messageModal = $('#message-modal');
    window.tokenField = $('input[name=_token]').val();
    window.dropDown = $('#dropdown');
    window.rightButtonBlock = $('#right-button-block .fa.fa-bell-o');

    // Setting datatable defaults
    $.extend( $.fn.dataTable.defaults, {
        autoWidth: false,
        columnDefs: [{
            targets: [4]
        }],
        order: [],
        dom: '<"datatable-header"fl><"datatable-scroll"t><"datatable-footer"ip>',
        bLengthChange: false,
        info: false,
        language: {
            search: '<span>Фильтр:</span> _INPUT_',
            lengthMenu: '<span>Показывать:</span> _MENU_',
            paginate: { 'next': '&rarr;', 'previous': '&larr;' },
            thousands:      ',',
            loadingRecords: 'Загрузка...',
            zeroRecords:    'Нет данных',
        },
        drawCallback: function () {
            $(this).find('tbody tr').slice(-3).find('.dropdown, .btn-group').addClass('dropup');
        },
        preDrawCallback: function() {
            $(this).find('tbody tr').slice(-3).find('.dropdown, .btn-group').removeClass('dropup');
        }
    });

    setTimeout(function () {
        removeLoader();
    },700);

    // Datatable
    const baseDataTable = dataTableAttributes($('.datatable-basic.default'), 8);
    const subscrDataTable = dataTableAttributes($('.datatable-basic.subscriptions'), 6);
    if (baseDataTable) clickYesDeleteOnModal(baseDataTable, true);
    if (subscrDataTable) clickYesDeleteOnModal(subscrDataTable, false);

    // Click to delete items
    window.deleteId = null;
    window.deleteRow = null;

    // Top menu tabs
    const topMenu = $('.rounded-block.tall .top-submenu');
    topMenu.find('a').click(function (e) {
        e.preventDefault();
        const parent = $(this).parents('.tab');
        if (!parent.hasClass('active')) {
            let currentActiveTab = topMenu.find('.tab.active'),
                currentActiveTabId = currentActiveTab.find('a').attr('id').replace('top-submenu-',''),
                currentContent = $('#content-'+currentActiveTabId),
                newActiveIdTabId = $(this).attr('id').replace('top-submenu-',''),
                newContent = $('#content-'+newActiveIdTabId);

            currentActiveTab.removeClass('active');
            parent.addClass('active');
            currentContent.fadeOut(() => {
                newContent.css('display','none').fadeIn();
            });
        }
    });

    $('#main-nav .navbar-toggler').click(function () {
        if (!$(this).hasClass('collapsed')) {
            $(this).find('span').css({
                'background-image':'url("data:image/svg+xml,%3csvg xmlns=\'http://www.w3.org/2000/svg\' viewBox=\'0 0 16 16\' fill=\'%23000\'%3e%3cpath d=\'M.293.293a1 1 0 011.414 0L8 6.586 14.293.293a1 1 0 111.414 1.414L9.414 8l6.293 6.293a1 1 0 01-1.414 1.414L8 9.414l-6.293 6.293a1 1 0 01-1.414-1.414L6.586 8 .293 1.707a1 1 0 010-1.414z\'/%3e%3c/svg%3e")',
                'background-size': '70%',
                'opacity':0.5
            });
        } else {
            $(this).find('span').css({
                'background-image':'url("data:image/svg+xml,%3csvg xmlns=\'http://www.w3.org/2000/svg\' viewBox=\'0 0 30 30\'%3e%3cpath stroke=\'rgba%280, 0, 0, 0.55%29\' stroke-linecap=\'round\' stroke-miterlimit=\'10\' stroke-width=\'2\' d=\'M4 7h22M4 15h22M4 23h22\'/%3e%3c/svg%3e")',
                'background-size': '100%',
                'opacity':1
            });
        }
    });

    $('.form-group.password i').click(function () {
        let cover = $(this).parents('.form-group'),
            input = cover.find('input');
        if ($(this).hasClass('icon-eye')) {
            input.attr('type','text');
            $(this).removeClass('icon-eye').addClass('icon-eye-blocked');
        } else {
            input.attr('type','password');
            $(this).removeClass('icon-eye-blocked').addClass('icon-eye');
        }
    });

    // Fancybox init
    bindFancybox();

    // Open message modal
    if (openMessageModalFlag) messageModal.modal('show');

    // Custom scroll dropdown menu
    $('.dropdown-menu').mCustomScrollbar({
        axis: 'y',
        theme: 'light-3',
        alwaysShowScrollbar: 1
    });

    // Getting news for dropdown
    if (userId) getNews();
    // MAIN BLOCK END

    // AUTH BLOCK BEGIN
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

    const unlockLoginButton = () => {
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

                // Goto prev url
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

    const unlockGetCodeAndRegisterButtons = () => {
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

    const preValidationRegister = () => {
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

    const unlockResetButton = () => {
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
    // AUTH BLOCK END

    // ACCOUNT BLOCK BEGIN
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
    // ACCOUNT BLOCK END

    // EDIT ORDER BLOCK BEGIN
    const progressBarContainer = $('#progress-bar'),
        progressBar = progressBarContainer.find('.progress-bar'),
        form = $('form'),
        nextButton = $('#next'),
        backButton = $('#back'),
        performersInput = $('input[name=performers]'),
        performersInputError = $('.error.performers'),
        addressInput = $('input[name=address]'),
        addressInputError = $('.error.address'),
        completeModal = $('#complete-modal'),
        preValidation = [
            () => {
                return true
            },
            () => {
                resetErrors(form);
                if (performersInput.val() <= 0 || performersInput.val() > 20) {
                    performersInput.addClass('error');
                    performersInputError.html(errorWrongValue);
                    return false;
                } else return true;
            },
            () => {
                resetErrors(form);
                if (addressInput.val() < 5) {
                    addressInput.addClass('error');
                    addressInputError.html(errorFieldMustBeFilledIn);
                    return false;
                } else if (addressInput.val() > 200) {
                    addressInput.addClass('error');
                    addressInputError.html(errorWrongValue);
                    return false;
                } else return true;
            },
            () => {
                return true;
            }
        ];

    if ($('#image-step3').length) {
        ymaps.ready(mapInitWithContainerForEditOrder);
    }

    $('input[name=order_type_id]').change(function () {
        let thisRadioGroup = $(this).parents('.radio-group'),
            thisSubTypesBlocks = thisRadioGroup.find('.sub-types-block'),
            anotherSubTypesBlocks = $('.radio-group').not(thisRadioGroup).find('.sub-types-block');

        anotherSubTypesBlocks.css('display','block');
        anotherSubTypesBlocks.animate({
            'height':0,
            'padding-bottom':0
        },'slow');

        if (thisSubTypesBlocks.length) {
            thisSubTypesBlocks.css({
                'display':'table',
                'opacity':0
            });

            let heightBlock = thisSubTypesBlocks.height();
            thisSubTypesBlocks.css({
                'display':'block',
                'height':0,
                'opacity':1
            });

            thisSubTypesBlocks.animate({
                'height':heightBlock
            },'slow');
        }
    });

    $('.sub-types-block input[type=checkbox]').change(function () {
        let subTypesBlock = $(this).parents('.sub-types-block');
        if (subTypesBlock.find('input[type=checkbox]:checked').length && subTypesBlock.find('input.error').length) {
            subTypesBlock.find('input.error').removeClass('error');
            subTypesBlock.find('.error.subtype').last().html('');
            subTypesBlock.css('height',subTypesBlock.height() - 20);
        }
    });

    backButton.click(() => {
        backButton.attr('disabled','disabled');
        $.get(backStepUrl, {
            'id': orderId ? orderId : '',
        }, () => {
            nextPrevStep(true, () => {
                backButton.removeAttr('disabled');
                if (step === 1) {
                    progressBarContainer.addClass('d-none');
                    backButton.addClass('d-none');
                }
                step--;
                setProgressBar(progressBar);
            });
        });
    });

    nextButton.click((e) => {
        e.preventDefault();
        resetErrors(form);
        if (preValidation[step]()) {
            if (step === 2) {
                let address = addressInput.val().indexOf('Москва') >= 0 ? addressInput.val() : 'Москва, '+addressInput.val();
                $.get(
                    'https://geocode-maps.yandex.ru/1.x/',
                    {
                        apikey: yandexApiKey,
                        geocode: address,
                        format: 'json'
                    },
                    (data) => {
                        if (parseInt(data.response.GeoObjectCollection.metaDataProperty.GeocoderResponseMetaData.found) === 1) {
                            let updatedAddress = data.response.GeoObjectCollection.featureMember[0].GeoObject.name;

                            if (window.placemark) window.myMap.geoObjects.remove(window.placemark);
                            let coordinates = data.response.GeoObjectCollection.featureMember[0].GeoObject.Point.pos.split(' ');
                            point = [parseFloat(coordinates[1]),parseFloat(coordinates[0])];
                            let newPlacemark = getPlaceMark(point,{});
                            window.myMap.geoObjects.add(newPlacemark)
                            zoomAndCenterMap();

                            backButton.attr('disabled','disabled');
                            nextButton.attr('disabled','disabled');

                            $.post(
                                nextStepUrl,
                                {
                                    '_token': window.tokenField,
                                    'address': updatedAddress,
                                    'latitude': point[0],
                                    'longitude': point[1]
                                }
                            ).done(() => {
                                setTimeout(function () {
                                    step++;
                                    setProgressBar(progressBar);
                                    nextPrevStep(false, () => {
                                        backButton.removeAttr('disabled');
                                        nextButton.removeAttr('disabled');
                                    });
                                }, 1500);
                            });
                        } else {
                            addressInput.addClass('error');
                            addressInputError.html(errorCheckAddress);
                        }
                    }
                );
            } else {
                backButton.attr('disabled','disabled');
                getUrl(form, null, () => {
                    step++;
                    if (step) {
                        backButton.removeClass('d-none');
                        progressBarContainer.removeClass('d-none');
                    }
                    setProgressBar(progressBar);
                    if (step !== 4) {
                        nextPrevStep(false, () => {
                            backButton.removeAttr('disabled');
                        });
                    } else {
                        setTimeout(function () {
                            window.location.href = orderPreviewUrl;
                        }, 3000);
                        completeModal.modal('show').on('hidden.bs.modal', () => {
                            window.location.href = orderPreviewUrl;
                        })
                    }
                });
            }
        }
    });

    performersInput.on('change', preValidation[1]).keyup(preValidation[1]);
    addressInput.on('change', preValidation[2]).keyup(preValidation[2]);
    imagePreview($('.order-photo'));

    // Delete image in edit order
    if (window.orderId) {
        $('.order-photo .icon-close2.d-block').click(function () {
            let photoPos = parseInt($(this).parents('.order-photo').find('input[type=file]').attr('name').replace('photo',''));
            $.post(
                deleteOrderImageUrl,
                {
                    '_token': window.tokenField,
                    'id': orderId,
                    'pos': photoPos
                }
            );
        });
    }
    // EDIT ORDER BLOCK END

    // ORDERS BLOCK BEGIN
    window.selectedPoints = $('#selected-points');
    window.selectedPoint = null;
    window.respondButton = $('#respond-button');
    window.pointsContainer = $('#points-container');
    window.selectedPointsOpened = false;
    window.cickedTarget = null;

    if ($('#map').length) ymaps.ready(mapInitWithContainerForOrders);

    $('#apply-button').click((e) => {
        e.preventDefault();
        removeSelectedPoints();
        window.myMap.geoObjects.removeAll();
        getPoints();
    });

    $('#selected-points i.icon-close2').click(() => {
        if (window.selectedPointsOpened) {
            removeSelectedPoints();
            // window.myMap.balloon.close();
        }
    });
    // ORDERS BLOCK END

    // ORDERS LIST BLOCK BEGIN
    const modalConfirm = $('#order-closing-confirm-modal'),
        orderClosedModal = $('#order-closed-modal'),
        ordersActiveTable = $('#content-active').find('table.datatable-basic.default');

    // Change pagination on data-tables
    ordersActiveTable.on('draw.dt', function () {
        bindClosingOrder(modalConfirm);
    });
    bindClosingOrder(modalConfirm);

    modalConfirm.find('button.close-yes').click(function (e) {
        e.preventDefault();
        modalConfirm.modal('hide');
        let deleteCell = window.tableRow.find('.close-order-cell');

        $.post(
            closeOrderUrl,
            {
                '_token': window.tokenField,
                'id': orderId,
            }
        ).done(() => {
            window.tableRow.find('.label').removeClass('in-progress').addClass('closed').html(archiveLabelText);
            deleteCell.removeClass('close-order-cell').addClass('empty');
            deleteCell.find('button').remove();

            deleteDataTableRows($('#content-active'), window.tableRow, true);
            addDataTableRow($('#content-archive'), window.tableRow, true);
            orderClosedModal.modal('show');
        });
    });
    // ORDERS LIST BLOCK END

    //CHATS BLOCK BEGIN
    const messagesBlock = $('#messages');
    if (messagesBlock.length) {
        messagesBlock.mCustomScrollbar({
            axis: 'y',
            theme: 'light-3',
            alwaysShowScrollbar: 1
        });
        messagesBlock.mCustomScrollbar('scrollTo','bottom');
        const newMessageForm = $('form#new-message'),
            inputMessage = newMessageForm.find('input[name=body]');

        newMessageForm.bind('submit', function (e) {
            e.preventDefault();
            if (inputMessage.val()) {
                $.post(newMessageForm.attr('action'),
                    {
                        '_token': window.tokenField,
                        'order_id': orderId,
                        'body': inputMessage.val()
                    }
                );
            }
        });
        $('.chat-input i').click(() => {
            newMessageForm.submit();
        });

        window.Echo.private('chat_' + orderId).listen('.chat', res => {
            let message = res.message,
                messageBlockCover = $('<div></div>').addClass('message-block'),
                messageBlock = $('<div></div>').addClass('message');

            if (message.author_id === userId) messageBlock.addClass('you');
            else {
                $.post(readMessageUrl,
                    {
                        '_token': window.tokenField,
                        'message_id': message.id,
                        'order_id': orderId
                    }
                );
            }

            messageBlock
                .append(
                    $('<div></div>').addClass('author').html(message.author)
                ).append(
                $('<div></div>').html(message.body)
            ).append(
                $('<div></div>').addClass('time').html(message.created_at)
            );

            messageBlockCover.append(messageBlock);
            $('#mCSB_2_container').append(messageBlockCover);
            messagesBlock.mCustomScrollbar('scrollTo','bottom');
            inputMessage.val('');
        });
    }
    //CHATS BLOCK END
});

const bindFancybox = () => {
    // Fancybox init
    $('.fancybox').fancybox({
        'autoScale': true,
        'touch': false,
        'transitionIn': 'elastic',
        'transitionOut': 'elastic',
        'speedIn': 500,
        'speedOut': 300,
        'autoDimensions': true,
        'centerOnScroll': true
    });
}

const addLoader = () => {
    $('body').prepend(
        $('<div></div>').attr('id','loader').append($('<div></div>'))
    ).css({
        // 'overflow':'hidden',
        'padding-right':20
    });
}

const removeLoader = () => {
    $('#loader').remove();
    $('body').css('overflow-y','auto');
}

const getUrl = (form, url, callBack) => {
    let formData = new FormData(),
        submitButton = form.find('button[type=submit]');

    form.find('input.error').removeClass('error');
    form.find('div.error').html('');
    form.find('input, select, textarea').each(function () {
        if ($(this).attr('type') === 'file') formData.append($(this).attr('name'), $(this)[0].files[0]);
        else if ($(this).attr('type') === 'radio') {
            $(this).each(function () {
                if ($(this).is(':checked')) formData.append($(this).attr('name'), $(this).val());
            });
        }
        else if ($(this).attr('type') === 'checkbox') {
            if ($(this).is(':checked')) formData.append($(this).attr('name'), $(this).val());
        }
        else {
            // console.log($(this).attr('name') + '==' + $(this).val());
            formData.append($(this).attr('name'), $(this).val());
        }
    });
    submitButton.attr('disabled','disabled');

    $.ajax({
        url: url ? url : form.attr('action'),
        data: formData,
        processData: false,
        contentType: false,
        type: form.attr('method'),
        cache: false,
        success: (data) => {
            if (callBack) callBack(data);
            submitButton.removeAttr('disabled');
        },
        error: (data) => {
            let response = jQuery.parseJSON(data.responseText),
                replaceErr = {
                    'phone':'телефон',
                    'email':'E-mail',
                    'user_name':'имя'
                };

            $.each(response.errors, function (field, error) {
                let errorMsg = error[0];
                $.each(replaceErr, function (src,replace) {
                    errorMsg = error[0].replace(src,replace);
                });
                form.find('input[name='+field+']').addClass('error');
                form.find('.error.'+field).html(errorMsg);
            });
            submitButton.removeAttr('disabled');
        }
    });
}

const getCodeAgainCounter = (getCodeButton, timer) => {
    let getRegisterCodeAgain = $('#get-code-again'),
        countDown = setInterval(() => {
        if (!timer) {
            getCodeButton.removeClass('d-none');
            clearInterval(countDown);
        }
        getRegisterCodeAgain.removeClass('d-none');
        getRegisterCodeAgain.find('span').html(timer);
        timer--;
    }, 1000);
};

const validationDate = (date) => {
    let inputDate = new Date(date[2], date[1], 0);
    return date[0] && date[1] && date[2] && date[0] <= inputDate.getDate() && date[1] <= 12;
};

const lengthValidate = (form, fields) => {
    let validationFlag = true;
    $.each(fields, (k, field) => {
        let input = form.find('input[name=' + field + ']');
        if (!input.val().length) {
            input.addClass('error');
            form.find('.error.' + field).html(errorFieldMustBeFilledIn);
            validationFlag = false;
        }
    });
    return validationFlag;
};

const resetErrors = (form) => {
    form.find('input.error').removeClass('error');
    form.find('div.error').html('');
};

const resizeDTable = (dataTable, rows) => {
    if ($(window).width() >= 991 && $(window).height() >= 800) dataTable.context[0]._iDisplayLength = rows;
    else if ($(window).width() >= 991) dataTable.context[0]._iDisplayLength = rows - 2;
    else dataTable.context[0]._iDisplayLength = rows + 2;

    dataTable.draw();
    bindDelete();
}

const bindChangePagination = (dataTable) => {
    const paginationEvent = ('draw.dt');
    dataTable.off(paginationEvent);
    dataTable.on(paginationEvent, function () {
        bindDelete();
    });
}

const bindDelete = () => {
    let deleteIcon = $('.icon-close2, .icon-bell-cross');
    deleteIcon.unbind();
    deleteIcon.click(function () {
        window.deleteId = $(this).attr('del-data');
        let deleteModal = $('#'+$(this).attr('modal-data')),
            inputId = deleteModal.find('input[name=id]'),
            possibleParentRow = $('#row-'+window.deleteId),
            altParentRow = $('.row-'+window.deleteId);

        window.deleteRow = possibleParentRow.length ? possibleParentRow : altParentRow;

        if (inputId.length) inputId.val(window.deleteId);
        deleteModal.modal('show');
    });
}

const mapInit = (container) => {
    window.myMap = new ymaps.Map(container, {
        center: [55.76, 37.64],
        zoom: 10,
        controls: []
    });
}

const getPlaceMark = (point, data) => {
    return new ymaps.Placemark(point, data, {
        preset: 'islands#darkOrangeCircleDotIcon'
    });
}

const zoomAndCenterMap = () => {
    window.myMap.setCenter(point, 17);
}

const dataTableAttributes = (dataTable, rows) => {
    if (dataTable.length) {
        dataTable = dataTable.DataTable({iDisplayLength: rows});

        // Change pagination on data-tables
        bindChangePagination(dataTable);
        bindDelete();

        $(window).resize(function () {
            resizeDTable(dataTable, rows);
        });
        return dataTable
    } else return false;
}

const clickYesDeleteOnModal = (dataTable, useCounter) => {
    // Click YES on delete modal
    $('.delete-yes').click(function () {
        let deleteModal = $(this).parents('.modal');
        deleteModal.modal('hide');
        addLoader();

        $.post(deleteModal.attr('del-function'), {
            '_token': window.tokenField,
            'id': window.deleteId,
        }, () => {
            deleteDataTableRows(window.deleteRow.parents('.content-block'), window.deleteRow, useCounter);
            removeLoader();
        });
    });
}

const deleteDataTableRows = (contentBlockTab, row, useCounter) => {
    let baseTable = row.parents('.datatable-basic.default'),
        dataTable = contentBlockTab.find('table'+window.dataTableClasses).DataTable();

    if (useCounter) changeDataCounter(contentBlockTab, -1);

    if (row.length > 1) {
        row.each(function () {
            dataTable.row($(this)).remove();
        });
    } else dataTable.row(row).remove();

    if (!dataTable.rows().count()) {
        dataTable.destroy();
        baseTable.remove();
        if (contentBlockTab) contentBlockTab.find('h4').removeClass('d-none');
    }

    dataTable.draw();
    bindChangePagination(dataTable);
    bindDelete();
}

const addDataTableRow = (contentBlockTab, row, useCounter) => {
    if (useCounter) changeDataCounter(contentBlockTab, 1);
    let dataTable = contentBlockTab.find('table'+window.dataTableClasses);

    if (!dataTable.length) {
        contentBlockTab.find('h4').addClass('d-none');
        let newTable = $('<table></table>').addClass(window.dataTableClasses.replaceAll('.',' ').trim());
        contentBlockTab.prepend(newTable);
        dataTable = dataTableAttributes(newTable, 8);
    } else {
        dataTable = dataTable.DataTable();
    }

    if (row.length > 1) {
        row.each(function () {
            dataTable.row.add($(this));
        });
    } else dataTable.row.add(row);

    dataTable.draw();
    bindChangePagination(dataTable);
    bindDelete();
}

const changeDataCounter = (contentBlockTab, increment) => {
    let contentId = contentBlockTab.attr('id').replace('content-',''),
        containerCounter = $('#top-submenu-'+contentId).next('sup'),
        counterVal = parseInt(containerCounter.html());

    counterVal += increment;
    containerCounter.html(counterVal);
}

const getNews = () => {
    window.dropDown.html('');

    $.get(getUnreadMessages).done((data) => {
        $.each(data.unread, function (id, counter) {
            let orderId = parseInt(id.replace('order',''));
            window.dropDown.append(
                $('<li></li>').attr('id','unread-order-' + orderId).append(
                    $('<div></div>')
                        .append(
                            $('<span></span>').html(unreadMessages + '<br>')
                        ).append(
                        $('<a></a>').attr('href', chatUrl+'/?order_id='+orderId).html(inChatNumber + orderId)
                    )
                ).append('<hr>')
            );
        });
    });

    $.get(getSubscriptionsUrl).done((data) => {
        if (data.subscriptions.length) {
            checkDropDownMenuNotEmpty();
            $.each(data.subscriptions, function (k,subscription) {
                $.each(subscription.unread_orders, function (k,unreadOrder) {
                    window.dropDown.append(
                        $('<li></li>').attr('id','unread-order-' + unreadOrder.order.id).append(
                            $('<div></div>')
                                .append(
                                    $('<a></a>').attr('href', ordersUrl+'/?id='+unreadOrder.order.id).html(newOrderFrom + '<br>')
                                ).append(
                                    $('<span></span>').html(unreadOrder.order.user.name + ' ' + unreadOrder.order.user.family)
                                )
                        ).append('<hr>')
                    );
                });
            });
        }
    });
}

const imagePreview = (container, defImage, callBack) => {
    container.each(function () {
        let currentContainer = $(this),
            hoverImg = currentContainer.find('img'),
            inputFile = currentContainer.find('input[type=file]'),
            addFileIcon = currentContainer.find('i.icon-file-plus2'),
            clearInputIcon = currentContainer.find('i.icon-close2');

        inputFile.change(function () {
            let input = $(this)[0].files[0];

            if (input.type.match('image.*')) {
                let reader = new FileReader();
                reader.onload = function (e) {
                    currentContainer.css('background-image', 'url(' + e.target.result + ')');
                    if (callBack) callBack(e.target.result);
                };
                reader.readAsDataURL(input);
                addFileIcon.hide();
                clearInputIcon.show();

            } else if (defImage) {
                currentContainer.css('background-image', 'url('+defImage+')');
            }
        }).on('mouseover', function () {
            hoverImg.show();
        }).on('mouseout', function () {
            hoverImg.hide();
        });

        clearInputIcon.click(function (e) {
            inputFile.val('');
            if (defImage) currentContainer.css('background-image', 'url('+defImage+')');
            else currentContainer.css('background-image', '');
            addFileIcon.removeClass('d-none');
            addFileIcon.show();
            clearInputIcon.removeClass('d-block');
            clearInputIcon.hide();
        });
    });
}

const owlSettings = (margin, nav, timeout, responsive, autoplay) => {
    let navButtonBlack1 = '<img src="/images/arrow_left.svg" />',
        navButtonBlack2 = '<img src="/images/arrow_right.svg" />';

    return {
        margin: margin,
        loop: autoplay,
        nav: nav,
        autoplay: autoplay,
        autoplayTimeout: timeout,
        dots: !nav,
        responsive: responsive,
        navText: [navButtonBlack1, navButtonBlack2]
    }
}

const nextPrevStep = (reverse, callBack) => {
    let stepFadeOut = reverse ? step + 1 : step,
        stepFadeIn = reverse ? step : step + 1,
        tags = ['head1','head2','inputs','image','description'],
        fadeOutSting = '',
        fadeInSting = '',
        callBackFlag = false;

    $.each(tags, (k, tag) => {
        let comma = (k + 1 !== tags.length ? ', ' : '');
        fadeOutSting += '#' + tag + '-step' + stepFadeOut + comma;
        fadeInSting += '#' + tag + '-step' + stepFadeIn + comma;
    });

    $(fadeOutSting).removeClass('d-block').fadeOut('slow',() => {
        $(fadeInSting).removeClass('d-none').fadeIn();
        if (callBack && !callBackFlag) {
            callBack();
            callBackFlag = true;
        }
    });
}

const setProgressBar = (progressBar) => {
    let progress = step * 25 + '%';
    progressBar.html(progress);
    progressBar.animate({
        'width': progress
    },'fast');
}

const mapInitWithContainerForEditOrder = () => {
    mapInit('image-step3');
    if (point.length) {
        window.placemark = getPlaceMark(point,{});
        window.myMap.geoObjects.add(window.placemark)
        zoomAndCenterMap();
    }
}

const mapInitWithContainerForOrders = () => {
    mapInit('map');
    getPoints();
}

const getPoints = () => {
    getUrl($('form'), (window.getPreviewFlag ? getPreviewUrl : null), (data) => {
        window.placemarks = [];
        let orders = data.orders;
        window.subscriptions = [];
        window.unreadOrders = [];
        if (data.subscriptions.length) {
            $.each(data.subscriptions, function (k,subscription) {
                window.subscriptions.push(subscription.user_id);
                if (subscription.orders.length) {
                    $.each(subscription.orders, function (k,order) {
                        window.unreadOrders.push(order.id);
                    });
                }
            });
        }
        if (orders.length) {
            $.each(orders, function (k,point) {
                let createdAt = new Date(point.created_at);
                window.placemarks.push(getPlaceMark([point.latitude, point.longitude], {
                    placemarkId: k,
                    // balloonContentHeader: point.order_type.name,
                    // balloonContentBody: point.address,
                    orderId: point.id,
                    name: point.order_type.name,
                    address: point.address,
                    orderType: point.order_type.name,
                    images: point.images,
                    orderSubTypes: point.order_type.subtypes,
                    subtypes: point.subtypes,
                    need_performers: point.need_performers,
                    performers: point.performers.length,
                    user: point.user,
                    date: createdAt.toLocaleDateString('ru-RU'),
                    description_short: point.description_short,
                    description_full: point.description_full
                }));

                if (window.getPreviewFlag) {
                    window.getPreviewFlag = false;
                    forceOpenOrder(0);
                } else if (window.openOrderId && window.openOrderId === point.id) {
                    forceOpenOrder(k);
                }
            });

            // Создаем собственный макет с информацией о выбранном геообъекте.
            // let customBalloonContentLayout = ymaps.templateLayoutFactory.createClass([
            //     '<ul class=list>',
            //     // Выводим в цикле список всех геообъектов.
            //     '{% for geoObject in properties.geoObjects %}',
            //     '<li><div class="balloon-head">{{ geoObject.properties.balloonContentHeader|raw }}</div><div class="balloon-content">{{ geoObject.properties.balloonContentBody|raw }}</div></li>',
            //     '{% endfor %}',
            //     '</ul>'
            // ].join(''));

            window.clusterer = new ymaps.Clusterer({
                preset: 'islands#darkOrangeClusterIcons',
                clusterDisableClickZoom: true,
                clusterOpenBalloonOnClick: false,
                // Устанавливаем режим открытия балуна.
                // В данном примере балун никогда не будет открываться в режиме панели.
                clusterBalloonPanelMaxMapArea: 0,
                // По умолчанию опции балуна balloonMaxWidth и balloonMaxHeight не установлены для кластеризатора,
                // так как все стандартные макеты имеют определенные размеры.
                clusterBalloonMaxHeight: 200,
                // Устанавливаем собственный макет контента балуна.
                // clusterBalloonContentLayout: customBalloonContentLayout,
            });

            // Click on cluster
            // window.clusterer.events.add('click', function (e) {
            //     $.each(e.get('target').properties._data.geoObjects, function (k, object) {
            //         console.log(object.properties.get('user'));
            //     });
            // });

            window.myMap.geoObjects.events.add('click', function (e) {
                var target = e.get('target');

                target.options.set('iconColor', '#bc202e');
                if (target.properties.get('geoObjects')) {
                    if (window.selectedPointsOpened) {
                        removeSelectedPoints(target,() => { clickedToCluster(target);});
                    } else clickedToCluster(target);
                } else {
                    if (window.selectedPointsOpened) {
                        removeSelectedPoints(target,() => { clickedToPoint(target);});
                    } else clickedToPoint(target);
                }
            });
            addPointsToMap();
        }
    });
}

const addPointsToMap = () => {
    window.clusterer.add(window.placemarks);
    window.myMap.geoObjects.add(window.clusterer);
}

const showOrder = (point) => {
    let properties = point.properties,
        orderId = properties.get('orderId'),
        orderSubTypes = properties.get('orderSubTypes'),
        currentSubTypes = properties.get('subtypes'),
        user = properties.get('user'),
        avatar = user.avatar ? user.avatar : '/images/def_avatar.svg',
        images = properties.get('images'),
        descriptionShort = properties.get('description_short'),
        descriptionFull = properties.get('description_full'),
        orderContainer = $('<div></div>').addClass('order-block mb-3').attr('id','order-'+properties.get('placemarkId'));

    // Check subscriptions
    let subscribeBellClass = window.subscriptions.includes(user.id) ? 'icon-bell-cross' : 'icon-bell-check',
        avatarProps = {'background-image':'url('+avatar+')'};

    if (user.avatar_props) {
        $.each(user.avatar_props, function (prop, value) {
            avatarProps[prop] = value;
        });
    }

    let posUnreadOrder = window.unreadOrders.indexOf(orderId);
    if (posUnreadOrder !== -1) {
        delete window.unreadOrders[posUnreadOrder];
        $.get(
            readOrderUrl,
            {'order_id': orderId}
        ).done(() => {
            $('#unread-order-'+orderId).remove();
            checkDropDownMenuEmpty();
        });
    }

    orderContainer
        .append(
            $('<h6></h6>').addClass('order-number').html(orderNumber + orderId + fromText + properties.get('date'))
        ).append(
        $('<div></div>').addClass('w-100 d-flex align-items-center justify-content-between')
            .append(
                $('<div></div>').addClass('d-flex align-items-center justify-content-center')
                    .append(
                        $('<div></div>').addClass('avatar cir').css(avatarProps)
                    ).append(
                    $('<div></div>').css('width',215)
                        .append(
                            $('<div></div>').addClass('user-name').html(user.family+' '+user.name)
                        ).append(
                        $('<div></div>').addClass('born').html(window.useAge)
                    )
                )
            )
            .append($('<i></i>').addClass('subscribe-icon ' + subscribeBellClass))
    );

    if (images.length) {
        let imagesContainer = $('<div></div>').addClass('images owl-carousel mt-3');
        $.each(images, function (k, image) {
            imagesContainer.append(
                $('<a></a>').addClass('fancybox').attr('href','/' + image.image).append(
                    $('<div></div>').addClass('image').css('background-image','url(/'+image.image+')')
                )
            );
        });
        orderContainer.append(imagesContainer);
        enablePointImagesCarousel(imagesContainer,images.length > 1);
    }

    orderContainer.append($('<h2></h2>').addClass('order-type text-dark text-left mt-3 mb-4').html(properties.get('orderType')));

    if (orderSubTypes && currentSubTypes) {
        let subTypesContainer = $('<ul></ul>').addClass('subtypes');
        for (let i=0; i < orderSubTypes.length; i++) {
            for (let ic=0; ic < currentSubTypes.length; ic++) {
                if (orderSubTypes[i].id === currentSubTypes[ic]) {
                    subTypesContainer.append($('<li></li>').html(orderSubTypes[i].name));
                    break;
                }
            }
        }
        orderContainer.append(subTypesContainer);
    }

    orderContainer.append($('<p></p>').addClass('mb-1 text-left').html('<b>' + address +'</b>: ' + properties.get('address')));

    if (descriptionShort) {
        orderContainer
            .append(
                $('<p></p>').addClass('fw-bold text-left mt-2 mb-0').html(descriptionShortText + ':')
            ).append(
            $('<p></p>').addClass('text-left order-description mb-1').html(descriptionShort)
        );
    }

    if (descriptionFull) {
        orderContainer
            .append(
                $('<p></p>').addClass('fw-bold text-left mt-0 mb-2')
                    .append($('<a></a>').addClass('description-full').html(descriptionFullText + ' »'))
            );
    }

    orderContainer
        .append(
            $('<p></p>').addClass('text-left mb-2').html(
                '<b>' + numberOfPerformersText + ':</b> ' + properties.get('performers') + outOfText + properties.get('need_performers')
            )
        );

    if (userId !== user.id) {
        orderContainer.append($('<button></button>').addClass('respond-button btn btn-primary w-100').attr('type','button').append($('<span></span>').html(respondToAnOrder)));
    }

    orderContainer.append($('<button></button>').addClass('cb-copy btn btn-primary w-100 mt-3').attr({
        'type':'button',
        'order_id':properties.get('orderId')
    }).append($('<span></span>').html(copyOrderHrefToClipboard)));

    orderContainer.append($('<hr>'));
    window.pointsContainer.append(orderContainer);
    bindFancybox();
}

const removeSelectedPoints = (target, callBack) => {
    if ( (window.cickedTarget && !target) || (window.cickedTarget && target && window.cickedTarget !== target) ) {
        window.cickedTarget.options.set('iconColor', '#e6761b');

        window.selectedPoints.animate({'margin-left': -1 * (window.selectedPoints.width() + 150)}, 'slow', function () {
            window.selectedPointsOpened = false;
            if (callBack) callBack();
        });
    }
}

const clickedToCluster = (target, objects) => {
    window.cickedTarget = target;
    purgePointsContainer();
    $.each(target.properties.get('geoObjects'), function (k, object) {
        showOrder(object);
    });
    setBindsAndOpen();
}

const clickedToPoint = (point) => {
    window.cickedTarget = point;
    purgePointsContainer();
    showOrder(point);
    setBindsAndOpen();
}

const purgePointsContainer = () => {
    window.pointsContainer.removeAttr('class').html('');
}

const setBindsAndOpen = () => {
    // Set custom scroll bar
    window.selectedPoints.mCustomScrollbar({
        axis: 'y',
        theme: 'light-3',
        alwaysShowScrollbar: 0
    });

    // Bind click respond button
    $('.respond-button').click(function (e) {
        e.preventDefault();
        let point = getPlaceMarkOnMap($(this)),
            properties = point.properties,
            orderId = properties.get('orderId'),
            orderRespondModal = $('#order-respond-modal');
        $.post(
            orderResponseUrl,
            {
                '_token': window.tokenField,
                'id': orderId,
            }
        ).done(() => {
            orderRespondModal.find('.order-number').html(orderId);
            orderRespondModal.find('.order-date').html(properties.get('date'));
            orderRespondModal.find('.order-type').html(properties.get('orderType'));
            orderRespondModal.find('.order-address').html(properties.get('address'));
            orderRespondModal.modal('show');
            window.clusterer.remove(point);
            removeSelectedPoints();
        });
    });

    // Bind subscribe button
    $('.subscribe-icon').click(function (e) {
        e.preventDefault();
        let button = $(this),
            point = getPlaceMarkOnMap(button),
            userId = point.properties.get('user').id;

        $.get(
            subscribeUrl,
            {'user_id': userId}
        ).done((data) => {
            button.fadeOut(() => {
                button.toggleClass('icon-bell-cross',data.subscription).toggleClass('icon-bell-check',!data.subscription);
                button.fadeIn();
            });
            window.subscriptions.push(userId);
        });
    });

    // Click to description full
    $('.description-full').click(function (e) {
        e.preventDefault();
        let point = getPlaceMarkOnMap($(this)),
            properties = point.properties,
            fullDescriptionModal = $('#order-full-description-modal');

        fullDescriptionModal.find('h5').html(descriptionFullOfOrderText + properties.get('orderId') + '<br>' + fromText + properties.get('date'));
        fullDescriptionModal.find('.modal-body p').html(properties.get('description_full'));
        fullDescriptionModal.modal('show');
    });

    // Open selected points
    window.selectedPoints.animate({'margin-left': 0}, 'slow', function () {
        window.selectedPointsOpened = true;
    });

    // Copy order href
    $('.cb-copy').click(function () {
        let orderId = $(this).attr('order_id'),
            href = ordersUrl + '?id=' + orderId;
        if (navigator.clipboard) {
            window.navigator.clipboard.writeText(href).then(() => {
                window.messageModal.find('h4').html(hrefIsCopied);
                window.messageModal.modal('show');
            });
        }
    });

}

const enablePointImagesCarousel = (container, autoplay) => {
    container.owlCarousel(owlSettings(
        10,
        autoplay,
        6000,
        {0: {items: 1}},
        autoplay
    ));
}

const getPlaceMarkOnMap = (obj) => {
    let placemarkId = parseInt((obj).parents('.order-block').attr('id').replace('order-',''));
    return window.placemarks[placemarkId];
}

const forceOpenOrder = (k) => {
    window.openOrderId = null;
    window.cickedTarget = window.placemarks[k];
    window.placemarks[k].options.set('iconColor', '#bc202e');
    showOrder(window.placemarks[k]);
    setBindsAndOpen();
}


const bindClosingOrder = (modalConfirm) => {
    let buttons = $('.close-order');
    buttons.unbind();
    buttons.click(function () {
        window.tableRow = $(this).parents('tr');
        window.orderId = parseInt(window.tableRow.attr('id').replace('row-',''));
        modalConfirm.modal('show');
    });
}

const checkDropDownMenuNotEmpty = () => {
    if (!window.rightButtonBlock.find('.dot').length) window.rightButtonBlock.append(
        $('<span></span>').addClass('dot')
    );
}

const checkDropDownMenuEmpty = () => {
    if (!window.dropDown.html()) {
        window.rightButtonBlock.find('.dot').remove();
    }
}
