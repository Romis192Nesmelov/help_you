import './bootstrap';
import {createApp} from "vue/dist/vue.esm-bundler";
import mitt from 'mitt';
import MessageComponent from "./components/MessageComponent.vue";
import TopLineComponent from "./components/TopLineComponent.vue";
import LeftMenuComponent from "./components/LeftMenuComponent.vue";
import AccountComponent from "./components/AccountComponent.vue";
import MyOrdersListComponent from "./components/MyOrdersListComponent.vue";
import MyHelpListComponent from "./components/MyHelpListComponent.vue";
import MySubscriptionsComponent from "./components/MySubscriptionsComponent.vue";
import MyChatsComponent from "./components/MyChatsComponent.vue";

const app = createApp({
    components: {
        MessageComponent,
        TopLineComponent,
        LeftMenuComponent,
        AccountComponent,
        MyOrdersListComponent,
        MyHelpListComponent,
        MySubscriptionsComponent,
        MyChatsComponent
    }
});

window.toCamelize = str => str.replace(/-|_./g, x=>x[1].toUpperCase());
window.getUserAge = (born) => {
    let now = new Date(),
        bornArr = born.split('-');
    let age = now.getFullYear() - parseInt(bornArr[2]);
    if (now.getMonth() + 1 <= parseInt(bornArr[1]) && now.getDay() < parseInt(bornArr[1])) {
        age--;
    }
    let lastDigit = age.toString().substr(-1,1),
        word;

    if (lastDigit === 0) word = 'лет';
    else if (lastDigit === 1) word = 'год';
    else if (lastDigit > 1 && lastDigit < 5) word = 'года';
    else word = 'лет';

    return age + ' ' + word;
};
window.userRating = (ratings) => {
    if (ratings.length) {
        let ratingVal = 0;
        $.each(ratings, function (k,rating) {
            ratingVal += rating.value;
        });
        return Math.round(ratingVal/ratings.length);
    } else return 0;
};
window.showMessage = (message) => {
    const messageModal = $('#message-modal');
    messageModal.find('h4').html(message);
    messageModal.modal('show');
};

window.addLoader = () => {
    $('body').prepend(
        $('<div></div>').attr('id', 'loader').append($('<div></div>'))
    ).css({
        'overflow-y': 'hidden',
        'padding-right': 20
    });
}

window.removeLoader = () => {
    $('#loader').remove();
    $('body').css('overflow-y', 'auto');
};

window.emitter = mitt();
app.config.globalProperties.emitter = window.emitter;
app.mount('#app');

window.tokenField = $('input[name=_token]').val();
window.avatarBlock = $('#avatar-block .avatar.cir');
window.inputLoginPhone = '';
window.inputLoginPassword = '';
window.inputRegisterPhone = '';
window.inputRegisterPassword = '';
window.inputRegisterConfirmPassword = '';
window.inputRegisterCode = '';
window.inputIAgreeRegister = false;
window.inputRestorePasswordPhone = '';
window.inputChangePhone = '';
window.inputChangePhoneCode = '';
window.inputChangePasswordOldPassword = '';
window.inputChangePasswordPassword = '';
window.inputChangePasswordConfirmPassword = '';

$(document).ready(function () {
    // console.log(window.toCamelize('kebab_case'));

    // MAIN BLOCK BEGIN
    $('.form-group.has-label i.icon-eye').click(function () {
        let cover = $(this).parents('.form-group'),
            input = cover.find('input');
        if ($(this).hasClass('icon-eye')) {
            input.attr('type', 'text');
            $(this).removeClass('icon-eye').addClass('icon-eye-blocked');
        } else {
            input.attr('type', 'password');
            $(this).removeClass('icon-eye-blocked').addClass('icon-eye');
        }
    });

    $('.dropdown-menu').mCustomScrollbar({
        axis: 'y',
        theme: 'light-3',
        alwaysShowScrollbar: 1
    });

    $('#messages').mCustomScrollbar({
        axis: 'y',
        theme: 'light-3',
        alwaysShowScrollbar: 1,
        scrollTo: 'bottom'
    });

    $.mask.definitions['n'] = "[7-8]";
    $.mask.definitions['c'] = "[1-2]";

    setTimeout(function () {
        removeLoader();
    }, 500);

    // Fancybox init
    bindFancybox();

    $('#main-nav .navbar-toggler').click(function () {
        if (!$(this).hasClass('collapsed')) {
            $(this).find('span').css({
                'background-image': 'url("data:image/svg+xml,%3csvg xmlns=\'http://www.w3.org/2000/svg\' viewBox=\'0 0 16 16\' fill=\'%23000\'%3e%3cpath d=\'M.293.293a1 1 0 011.414 0L8 6.586 14.293.293a1 1 0 111.414 1.414L9.414 8l6.293 6.293a1 1 0 01-1.414 1.414L8 9.414l-6.293 6.293a1 1 0 01-1.414-1.414L6.586 8 .293 1.707a1 1 0 010-1.414z\'/%3e%3c/svg%3e")',
                'background-size': '70%',
                'opacity': 0.5
            });
        } else {
            $(this).find('span').css({
                'background-image': 'url("data:image/svg+xml,%3csvg xmlns=\'http://www.w3.org/2000/svg\' viewBox=\'0 0 30 30\'%3e%3cpath stroke=\'rgba%280, 0, 0, 0.55%29\' stroke-linecap=\'round\' stroke-miterlimit=\'10\' stroke-width=\'2\' d=\'M4 7h22M4 15h22M4 23h22\'/%3e%3c/svg%3e")',
                'background-size': '100%',
                'opacity': 1
            });
        }
    });
    // MAIN BLOCK END

    // AUTH BLOCK BEGIN
    const loginModal = $('#login-modal'),
        loginModalLogin = loginModal.find('input[name=phone]'),
        loginModalPassword = loginModal.find('input[name=password]'),
        phoneMask = "+n(999)999-99-99",
        codeMask = "99-99-99",
        bornMask = "99-99-9999",
        phoneRegExp = /^((\+)?(\d)(\s)?(\()?[0-9]{3}(\))?(\s)?([0-9]{3})(\-)?([0-9]{2})(\-)?([0-9]{2}))$/gi,
        bornRegExp = /^([0-3][0-9])-([0-1][0-9])-((19([0-9][0-9]))|(20[0-9][0-9]))$/gi,
        emailRegExp = /^[a-zA-Z0-9][\-_\.\+\!\#\$\%\&\'\*\/\=\?\^\`\{\|]{0,1}([a-zA-Z0-9][\-_\.\+\!\#\$\%\&\'\*\/\=\?\^\`\{\|]{0,1})*[a-zA-Z0-9]@[a-zA-Z0-9][-\.]{0,1}([a-zA-Z][-\.]{0,1})*[a-zA-Z0-9]\.[a-zA-Z0-9]{1,}([\.\-]{0,1}[a-zA-Z]){0,}[a-zA-Z0-9]{0,}$/gi,
        codeRegExp = /^((\d){2}(\-)(\d){2}(\-)(\d){2})$/gi;

    loginModalLogin.mask(phoneMask).keyup(function () {
        if ($(this).val().match(phoneRegExp)) {
            window.inputLoginPhone = $(this).val();
            enableLoginModalButtons();
        } else {
            window.inputLoginPhone = null;
            enableLoginModalButtons();
        }
    });

    loginModalPassword.keyup(function () {
        if ($(this).val().length) {
            window.inputLoginPassword = $(this).val();
            enableLoginModalButtons();
        } else {
            window.inputLoginPassword = null;
            enableLoginModalButtons();
        }
    });

    const registerModal = $('#register-modal'),
        loginRegisterModal = registerModal.find('input[name=phone]'),
        passwordRegisterModal = registerModal.find('input[name=password]'),
        confirmPasswordRegisterModal = registerModal.find('input[name=password_confirmation]'),
        codeRegisterModal = registerModal.find('input[name=code]'),
        iAgreeRegisterModal = registerModal.find('input[name=i_agree]');

    loginRegisterModal.mask(phoneMask).keyup(function () {
        if ($(this).val().match(phoneRegExp)) {
            window.inputRegisterPhone = $(this).val();
            enableRegisterModalButtons();
        } else {
            window.inputRegisterPhone = null;
            enableRegisterModalButtons();
        }
    });

    passwordRegisterModal.keyup(function () {
        if ($(this).val().length) {
            window.inputRegisterPassword = $(this).val();
            enableRegisterModalButtons();
        } else {
            window.inputRegisterPassword = null;
            enableRegisterModalButtons();
        }
    });

    confirmPasswordRegisterModal.keyup(function () {
        if ($(this).val().length) {
            window.inputRegisterConfirmPassword = $(this).val();
            enableRegisterModalButtons();
        } else {
            window.inputRegisterConfirmPassword = null;
            enableRegisterModalButtons();
        }
    });

    codeRegisterModal.mask(codeMask).keyup(function () {
        if ($(this).val().match(codeRegExp)) {
            window.inputRegisterCode = $(this).val();
            enableRegisterModalButtons();
        } else {
            window.inputRegisterCode = null;
            enableRegisterModalButtons();
        }
    });

    iAgreeRegisterModal.change(function () {
        window.inputIAgreeRegister = $(this).is(':checked');
        enableRegisterModalButtons();
    });

    const submitRestorePasswordButton = $('#reset-button');
    $('#restore-password-modal input[name=phone]').mask(phoneMask).keyup(function () {
        if ($(this).val().match(phoneRegExp)) {
            window.inputRestorePasswordPhone = $(this).val();
            submitRestorePasswordButton.removeAttr('disabled');
        } else {
            window.inputRestorePasswordPhone = null;
            submitRestorePasswordButton.attr('disabled', 'disabled');
        }
    });
    // AUTH BLOCK END

    // ACCOUNT BLOCK BEGIN
    imagePreview(window.avatarBlock, '/images/def_avatar.svg');

    const changePhoneModal = $('#change-phone-modal'),
        phoneChangePhoneModal = changePhoneModal.find('input[name=phone]'),
        codeChangePhoneModal = changePhoneModal.find('input[name=code]');

    phoneChangePhoneModal.mask(phoneMask).keyup(function () {
        if ($(this).val().match(phoneRegExp)) {
            window.inputChangePhone = $(this).val();
            enableChangePhoneModalButton();
        } else {
            window.inputChangePhone = null;
            enableChangePhoneModalButton();
        }
    });

    codeChangePhoneModal.mask(codeMask).keyup(function () {
        if ($(this).val().match(codeRegExp)) {
            window.inputChangePhoneCode = $(this).val();
            enableChangePhoneModalButton();
        } else {
            window.inputChangePhoneCode = null;
            enableChangePhoneModalButton();
        }
    });

    const changePasswordModal = $('#change-password-modal'),
        oldPasswordChangePasswordModal = changePasswordModal.find('input[name=old_password]'),
        passwordChangePasswordModal = changePasswordModal.find('input[name=password]'),
        confirmPasswordChangePasswordModal = changePasswordModal.find('input[name=password_confirmation]');

    oldPasswordChangePasswordModal.keyup(function () {
        if ($(this).val().length) {
            window.inputChangePasswordOldPassword = $(this).val();
            enableChangePasswordModalButton();
        } else {
            window.inputChangePasswordOldPassword = null;
            enableChangePasswordModalButton();
        }
    });

    passwordChangePasswordModal.keyup(function () {
        if ($(this).val().length) {
            window.inputChangePasswordPassword = $(this).val();
            enableChangePasswordModalButton();
        } else {
            window.inputChangePasswordPassword = null;
            enableChangePasswordModalButton();
        }
    });

    confirmPasswordChangePasswordModal.keyup(function () {
        if ($(this).val().length) {
            window.inputChangePasswordConfirmPassword = $(this).val();
            enableChangePasswordModalButton();
        } else {
            window.inputChangePasswordConfirmPassword = null;
            enableChangePasswordModalButton();
        }
    });

    // backButton.click(() => {
    //     backButton.attr('disabled','disabled');
    //     $.get(backStepUrl, {
    //         'id': window.orderId ? window.orderId : '',
    //     }, () => {
    //         nextPrevStep(true, () => {
    //             backButton.removeAttr('disabled');
    //             if (step === 1) {
    //                 progressBarContainer.addClass('d-none');
    //                 backButton.addClass('d-none');
    //             }
    //             step--;
    //             setProgressBar(progressBar);
    //         });
    //     });
    // });
    //
    // nextButton.click((e) => {
    //     e.preventDefault();
    //     resetErrors(form);
    //     if (preValidation[step]()) {
    //         if (step === 2) {
    //             let address = addressInput.val().indexOf('Москва') >= 0 ? addressInput.val() : 'Москва, '+addressInput.val();
    //             $.get(
    //                 'https://geocode-maps.yandex.ru/1.x/',
    //                 {
    //                     apikey: yandexApiKey,
    //                     geocode: address,
    //                     format: 'json'
    //                 },
    //                 (data) => {
    //                     if (parseInt(data.response.GeoObjectCollection.metaDataProperty.GeocoderResponseMetaData.found) === 1) {
    //                         let updatedAddress = data.response.GeoObjectCollection.featureMember[0].GeoObject.name;
    //
    //                         if (window.placemark) window.myMap.geoObjects.remove(window.placemark);
    //                         let coordinates = data.response.GeoObjectCollection.featureMember[0].GeoObject.Point.pos.split(' '),
    //                             point = [parseFloat(coordinates[1]),parseFloat(coordinates[0])],
    //                             newPlacemark = getPlaceMark(point,{});
    //
    //                         window.myMap.geoObjects.add(newPlacemark)
    //                         zoomAndCenterMap();
    //
    //                         backButton.attr('disabled','disabled');
    //                         nextButton.attr('disabled','disabled');
    //
    //                         let hiddenIdInput = $('input[name=id]'),
    //                             fields = {
    //                             '_token': window.tokenField,
    //                             'address': updatedAddress,
    //                             'latitude': point[0],
    //                             'longitude': point[1]
    //                         };
    //
    //                         if (hiddenIdInput.length) {
    //                             fields['id'] = hiddenIdInput.val();
    //                         }
    //
    //                         $.post(nextStepUrl, fields,
    //                         () => {
    //                             setTimeout(function () {
    //                                 step++;
    //                                 setProgressBar(progressBar);
    //                                 nextPrevStep(false, () => {
    //                                     backButton.removeAttr('disabled');
    //                                     nextButton.removeAttr('disabled');
    //                                 });
    //                             }, 1500);
    //                         });
    //                     } else {
    //                         addressInput.addClass('error');
    //                         addressInputError.html(errorCheckAddress);
    //                     }
    //                 }
    //             );
    //         } else {
    //             backButton.attr('disabled','disabled');
    //             getUrl(form, null, () => {
    //                 step++;
    //                 if (step) {
    //                     backButton.removeClass('d-none');
    //                     progressBarContainer.removeClass('d-none');
    //                 }
    //                 setProgressBar(progressBar);
    //                 if (step !== 4) {
    //                     nextPrevStep(false, () => {
    //                         backButton.removeAttr('disabled');
    //                     });
    //                 } else {
    //                     setTimeout(function () {
    //                         window.location.href = orderPreviewUrl;
    //                     }, 3000);
    //                     completeModal.modal('show').on('hidden.bs.modal', () => {
    //                         window.location.href = orderPreviewUrl;
    //                     })
    //                 }
    //             });
    //         }

    const accountBlock = $('#account-block'),
        userNameInput = accountBlock.find('input[name=name]'),
        userFamilyInput = accountBlock.find('input[name=family]'),
        userBornInput = accountBlock.find('input[name=born]'),
        useEmailInput = accountBlock.find('input[name=email]');

    window.userName = userNameInput.val();
    window.userFamily = userFamilyInput.val();
    window.userBorn = userBornInput.val();
    window.userEmail = useEmailInput.val();

    userNameInput.keyup(function () {
        if ($(this).val().length) {
            window.userName = $(this).val();
            enableAccountButton();
        } else {
            window.userName = null;
            enableAccountButton();
        }
    });

    userFamilyInput.keyup(function () {
        if ($(this).val().length) {
            window.userFamily = $(this).val();
            enableAccountButton();
        } else {
            window.userFamily = null;
            enableAccountButton();
        }
    });

    userBornInput.mask(bornMask).keyup(function () {
        if ($(this).val().match(bornRegExp)) {
            window.userBorn = $(this).val();
            enableAccountButton();
        } else {
            window.userBorn = null;
            enableAccountButton();
        }
    });

    useEmailInput.keyup(function () {
        if ($(this).val().match(emailRegExp)) {
            window.userEmail = $(this).val();
            enableAccountButton();
        } else {
            window.userEmail = null;
            enableAccountButton();
        }
    });
});

    // // EDIT ORDER BLOCK BEGIN
    // const progressBarContainer = $('#progress-bar'),
    //     progressBar = progressBarContainer.find('.progress-bar'),
    //     form = $('form'),
    //     nextButton = $('#next'),
    //     backButton = $('#back'),
    //     performersInput = $('input[name=performers]'),
    //     performersInputError = $('.error.performers'),
    //     addressInput = $('input[name=address]'),
    //     addressInputError = $('.error.address'),
    //     completeModal = $('#complete-modal'),
    //     preValidation = [
    //         () => {
    //             return true
    //         },
    //         () => {
    //             resetErrors(form);
    //             if (performersInput.val() <= 0 || performersInput.val() > 20) {
    //                 performersInput.addClass('error');
    //                 performersInputError.html(errorWrongValueText);
    //                 return false;
    //             } else return true;
    //         },
    //         () => {
    //             resetErrors(form);
    //             if (addressInput.val() < 5) {
    //                 addressInput.addClass('error');
    //                 addressInputError.html(errorFieldMustBeFilledInText);
    //                 return false;
    //             } else if (addressInput.val() > 200) {
    //                 addressInput.addClass('error');
    //                 addressInputError.html(errorWrongValueText);
    //                 return false;
    //             } else return true;
    //         },
    //         () => {
    //             return true;
    //         }
    //     ];
    //
    // if ($('#image-step3').length) {
    //     ymaps.ready(mapInitWithContainerForEditOrder);
    // }
    //
    // $('input[name=order_type_id]').change(function () {
    //     let thisRadioGroup = $(this).parents('.radio-group'),
    //         thisSubTypesBlocks = thisRadioGroup.find('.sub-types-block'),
    //         anotherSubTypesBlocks = $('.radio-group').not(thisRadioGroup).find('.sub-types-block');
    //
    //     anotherSubTypesBlocks.css('display','block');
    //     anotherSubTypesBlocks.animate({
    //         'height':0,
    //         'padding-bottom':0
    //     },'slow');
    //
    //     if (thisSubTypesBlocks.length) {
    //         thisSubTypesBlocks.css({
    //             'display':'table',
    //             'opacity':0
    //         });
    //
    //         let heightBlock = thisSubTypesBlocks.height();
    //         thisSubTypesBlocks.css({
    //             'display':'block',
    //             'height':0,
    //             'opacity':1
    //         });
    //
    //         thisSubTypesBlocks.animate({
    //             'height':heightBlock
    //         },'slow');
    //     }
    // });
    //
    // $('.sub-types-block input[type=checkbox]').change(function () {
    //     let subTypesBlock = $(this).parents('.sub-types-block');
    //     if (subTypesBlock.find('input[type=checkbox]:checked').length && subTypesBlock.find('input.error').length) {
    //         subTypesBlock.find('input.error').removeClass('error');
    //         subTypesBlock.find('.error.subtype').last().html('');
    //         subTypesBlock.css('height',subTypesBlock.height() - 20);
    //     }
    // });
    //
    // backButton.click(() => {
    //     backButton.attr('disabled','disabled');
    //     $.get(backStepUrl, {
    //         'id': window.orderId ? window.orderId : '',
    //     }, () => {
    //         nextPrevStep(true, () => {
    //             backButton.removeAttr('disabled');
    //             if (step === 1) {
    //                 progressBarContainer.addClass('d-none');
    //                 backButton.addClass('d-none');
    //             }
    //             step--;
    //             setProgressBar(progressBar);
    //         });
    //     });
    // });
    //
    // nextButton.click((e) => {
    //     e.preventDefault();
    //     resetErrors(form);
    //     if (preValidation[step]()) {
    //         if (step === 2) {
    //             let address = addressInput.val().indexOf('Москва') >= 0 ? addressInput.val() : 'Москва, '+addressInput.val();
    //             $.get(
    //                 'https://geocode-maps.yandex.ru/1.x/',
    //                 {
    //                     apikey: yandexApiKey,
    //                     geocode: address,
    //                     format: 'json'
    //                 },
    //                 (data) => {
    //                     if (parseInt(data.response.GeoObjectCollection.metaDataProperty.GeocoderResponseMetaData.found) === 1) {
    //                         let updatedAddress = data.response.GeoObjectCollection.featureMember[0].GeoObject.name;
    //
    //                         if (window.placemark) window.myMap.geoObjects.remove(window.placemark);
    //                         let coordinates = data.response.GeoObjectCollection.featureMember[0].GeoObject.Point.pos.split(' ');
    //                         point = [parseFloat(coordinates[1]),parseFloat(coordinates[0])];
    //                         let newPlacemark = getPlaceMark(point,{});
    //                         window.myMap.geoObjects.add(newPlacemark)
    //                         zoomAndCenterMap();
    //
    //                         backButton.attr('disabled','disabled');
    //                         nextButton.attr('disabled','disabled');
    //
    //                         let hiddenIdInput = $('input[name=id]'),
    //                             fields = {
    //                             '_token': window.tokenField,
    //                             'address': updatedAddress,
    //                             'latitude': point[0],
    //                             'longitude': point[1]
    //                         };
    //
    //                         if (hiddenIdInput.length) {
    //                             fields['id'] = hiddenIdInput.val();
    //                         }
    //
    //                         $.post(nextStepUrl, fields,
    //                         () => {
    //                             setTimeout(function () {
    //                                 step++;
    //                                 setProgressBar(progressBar);
    //                                 nextPrevStep(false, () => {
    //                                     backButton.removeAttr('disabled');
    //                                     nextButton.removeAttr('disabled');
    //                                 });
    //                             }, 1500);
    //                         });
    //                     } else {
    //                         addressInput.addClass('error');
    //                         addressInputError.html(errorCheckAddress);
    //                     }
    //                 }
    //             );
    //         } else {
    //             backButton.attr('disabled','disabled');
    //             getUrl(form, null, () => {
    //                 step++;
    //                 if (step) {
    //                     backButton.removeClass('d-none');
    //                     progressBarContainer.removeClass('d-none');
    //                 }
    //                 setProgressBar(progressBar);
    //                 if (step !== 4) {
    //                     nextPrevStep(false, () => {
    //                         backButton.removeAttr('disabled');
    //                     });
    //                 } else {
    //                     setTimeout(function () {
    //                         window.location.href = orderPreviewUrl;
    //                     }, 3000);
    //                     completeModal.modal('show').on('hidden.bs.modal', () => {
    //                         window.location.href = orderPreviewUrl;
    //                     })
    //                 }
    //             });
    //         }
    //     }
    // });
    //
    // performersInput.on('change', preValidation[1]).keyup(preValidation[1]);
    // addressInput.on('change', preValidation[2]).keyup(preValidation[2]);
    // imagePreview($('.order-photo'));
    //
    // // Delete image in edit order
    // if (window.orderId) {
    //     $('.order-photo .icon-close2.d-block').click(function () {
    //         let photoPos = parseInt($(this).parents('.order-photo').find('input[type=file]').attr('name').replace('photo',''));
    //         $.post(deleteOrderImageUrl, {
    //             '_token': window.tokenField,
    //             'id': window.orderId,
    //             'pos': photoPos
    //         });
    //     });
    // }
    // // EDIT ORDER BLOCK END
    //
    // // ORDERS BLOCK BEGIN
    // window.selectedPoints = $('#selected-points');
    // window.selectedPoint = null;
    // window.respondButton = $('#respond-button');
    // window.pointsContainer = $('#points-container');
    // window.selectedPointsOpened = false;
    // window.cickedTarget = null;
    //
    // if ($('#map').length) {
    //     ymaps.ready(mapInitWithContainerForOrders);
    //     window.Echo.channel('order_event').listen('.order', res => {
    //         if (res.notice === 'remove_order') {
    //             for (let i=0;i<window.placemarks.length;i++) {
    //                 if (window.placemarks[i].properties.get('orderId') === res.order.id) {
    //                     window.clusterer.remove(window.placemarks[i]);
    //                     break;
    //                 }
    //             }
    //         } else {
    //             let markId = window.placemarks.length,
    //                 createdAt = new Date(res.order.created_at);
    //
    //             window.placemarks.push(getPlaceMark([res.order.latitude, res.order.longitude], {
    //                 placemarkId: markId,
    //                 orderId: res.order.id,
    //                 name: res.order_type.name,
    //                 address: res.order.address,
    //                 orderType: res.order_type.name,
    //                 images: res.images,
    //                 subtype: res.sub_type.name,
    //                 need_performers: res.order.need_performers,
    //                 performers: res.performers.length,
    //                 user: res.user,
    //                 date: createdAt.toLocaleDateString('ru-RU'),
    //                 description_short: res.order.description_short,
    //                 description_full: res.order.description_full
    //             }));
    //             window.clusterer.add(window.placemarks);
    //         }
    //     });
    // }
    //
    // $('#apply-button').click((e) => {
    //     e.preventDefault();
    //     removeSelectedPoints();
    //     window.myMap.geoObjects.removeAll();
    //     getPoints();
    // });
    //
    // $('#selected-points i.icon-close2').click(() => {
    //     if (window.selectedPointsOpened) {
    //         removeSelectedPoints();
    //         // window.myMap.balloon.close();
    //     }
    // });
    // // ORDERS BLOCK END
    //
    // //CHATS BLOCK BEGIN
    // const messagesBlock = $('#messages'),
    //     orderDataModal = $('#order-data-modal');
    //
    // enablePointImagesCarousel(orderDataModal.find('.images'),orderDataModal.find('.image').length > 1);
    //
    // if (messagesBlock.length) {
    //     messagesBlock.mCustomScrollbar({
    //         axis: 'y',
    //         theme: 'light-3',
    //         alwaysShowScrollbar: 1
    //     });
    //     messagesBlock.mCustomScrollbar('scrollTo','bottom');
    //     const inputMessage = $('textarea[name=body]'),
    //         inputMessageFile = $('input[name=image]'),
    //         mainContainer = $('#mCSB_2_container');
    //
    //     inputMessageFile.change(function () {
    //         let attachedImageContainer = getAttachedImageContainerNotLoaded(),
    //             attachedFile = $(this)[0].files[0],
    //             reader = new FileReader();
    //
    //         // Preview attaching image
    //         if (inputMessageFile.val() && (attachedFile.type === 'image/jpeg' || attachedFile.type === 'image/png')) {
    //             reader.onload = function (e) {
    //                 if (attachedImageContainer.length) {
    //                     attachedImageContainer.find('img').attr('src',e.target.result);
    //                     $('.error.image').html('');
    //                 } else {
    //                     attachingImageContainer(
    //                         mainContainer,
    //                         messagesBlock,
    //                         inputMessageFile,
    //                         true,
    //                         true,
    //                         e.target.result
    //                     );
    //                 }
    //             };
    //             reader.readAsDataURL(attachedFile);
    //         } else {
    //             inputMessageFile.val('');
    //             removeLastMessageRowWithPreviewImage();
    //         }
    //     });
    //
    //     // Send new message
    //     inputMessage.keydown(function(e) {
    //         if (e.keyCode === 13) {
    //             e.preventDefault();
    //             newMessageChat(inputMessage, inputMessageFile, messagesBlock);
    //         }
    //     });
    //
    //     messagesBlock.keydown(function(e) {
    //         if (e.keyCode === 13) {
    //             e.preventDefault();
    //             newMessageChat(inputMessage, inputMessageFile, messagesBlock);
    //         }
    //     });
    //
    //     $('.chat-input i').click(() => {
    //         newMessageChat(inputMessage, inputMessageFile, messagesBlock);
    //     });
    //
    //     // Receiving new message
    //     window.Echo.private('chat_' + window.orderId).listen('.chat', res => {
    //         $.post(readMessageUrl, {
    //             '_token': window.tokenField,
    //             'order_id': window.orderId,
    //         }, () => {
    //             window.dropDown.find('li.unread-messages').remove();
    //             if (!window.dropDown.find('li').length) window.rightButtonBlock.find('.dot').remove();
    //         });
    //
    //         let messageData = res.message,
    //             lastDate = $('.date-block').last().find('.date').html(),
    //             messageBody = $('<div></div>').addClass('message-block')
    //                 .append(getAvatarBlock(messageData.user, 0.2))
    //                 .append(
    //                     $('<div></div>').addClass('message')
    //                         .append(
    //                             $('<div></div>').addClass('author').html(getUserName(messageData.user))
    //                                 .append($('<span></span>').html(messageData.time))
    //                             ).append(
    //                                 $('<div></div>').html(messageData.body)
    //                             )
    //                 );
    //
    //         // Set global date
    //         if (messageData.date !== lastDate) {
    //             let dateBlock = $('<div></div>').addClass('date-block').append(
    //                 $('<span></span>').addClass('date').html(messageData.date)
    //             )
    //             if (lastDate) mainContainer.append(dateBlock);
    //             else mainContainer.prepend(dateBlock);
    //         }
    //
    //         // Removing last message row if that contains not-attached image
    //         if (userId === messageData.user.id) {
    //             inputMessageFile.val('');
    //             removeLastMessageRowWithPreviewImage();
    //         }
    //
    //         if (messageData.image) {
    //             let attachedImageContainer = attachingImageContainer(
    //                 mainContainer,
    //                 messagesBlock,
    //                 inputMessageFile,
    //                 userId === messageData.user.id,
    //                 false,
    //                 messageData.image
    //             );
    //             attachedImageContainer.append(messageBody);
    //         } else {
    //             let messageRow = getNewMessageRow(userId === messageData.user.id);
    //             messageRow.append(messageBody);
    //             mainContainer.append(messageRow);
    //         }
    //         messagesBlock.mCustomScrollbar('scrollTo','bottom');
    //     });
    // }
    //CHATS BLOCK END

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

const enableLoginModalButtons = () => {
    const submitLoginButton = $('#enter');
    if (window.inputLoginPhone && window.inputLoginPassword) submitLoginButton.removeAttr('disabled');
    else submitLoginButton.attr('disabled','disabled');
}

const enableRegisterModalButtons = () => {
    const submitRegisterButton = $('#register'),
        getCodeRegisterCodeButton = $('#get-register-code');

    if (window.inputRegisterPhone && window.inputRegisterPassword && window.inputRegisterConfirmPassword) {
        getCodeRegisterCodeButton.removeAttr('disabled');
        if (window.inputRegisterCode && window.inputIAgreeRegister) submitRegisterButton.removeAttr('disabled');
        else submitRegisterButton.attr('disabled','disabled');
    } else {
        getCodeRegisterCodeButton.attr('disabled','disabled');
        submitRegisterButton.attr('disabled','disabled');
    }
}

const enableChangePhoneModalButton = () => {
    const submitChangePhoneButton = $('#change-phone-button'),
        getCodeChangePhoneButton = $('#get-register-code'),
        getCodeAgainBlock = $('#get-code-again');

    if (window.inputChangePhone) {
        if (!getCodeAgainBlock.length) getCodeChangePhoneButton.removeAttr('disabled');
        else getCodeChangePhoneButton.attr('disabled','disabled');

        if (window.inputChangePhoneCode) submitChangePhoneButton.removeAttr('disabled');
        else submitChangePhoneButton.attr('disabled','disabled');
    } else {
        submitChangePhoneButton.attr('disabled','disabled');
        getCodeChangePhoneButton.attr('disabled','disabled');
    }
}

const enableChangePasswordModalButton = () => {
    const submitChangePasswordButton = $('#change-password-button');

    if (window.inputChangePasswordOldPassword && window.inputChangePasswordPassword && window.inputChangePasswordConfirmPassword) {
        submitChangePasswordButton.removeAttr('disabled');
    } else {
        submitChangePasswordButton.attr('disabled', 'disabled');
    }
}

const enableAccountButton = () => {
    const submitChangeAccountButton = $('#account-save');

    if (window.userName && window.userFamily && window.userBorn && window.userEmail) {
        submitChangeAccountButton.removeAttr('disabled');
    } else {
        submitChangeAccountButton.attr('disabled','disabled');
    }
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

const imagePreview = (container, defImage) => {
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
                    currentContainer.trigger('onload_image',[e.target.result]);
                    // if (callBack) callBack(e.target.result);
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

// const getNewMessageRow = (selfFlag) => {
//     let messageRow = $('<div></div>').addClass('message-row');
//     if (selfFlag) messageRow.addClass('my-self');
//     return messageRow;
// }
//
// const getTableRow = (orderId) => {
//     return $('#row-' + orderId);
// }
//
// const getPerformersCountContainer = (orderId) => {
//     return $('#order-performers-' + window.orderId).next('span');
// }
//
// const removeLastMessageRowWithPreviewImage = () => {
//     let attachedImageContainer = getAttachedImageContainerNotLoaded();
//     if (attachedImageContainer.length) attachedImageContainer.parents('.message-row').remove();
// }
//
// const getAttachedImageContainerNotLoaded = () => {
//     return $('.attached-image.not-loaded').last();
// }
//
// const attachingImageContainer = (mainContainer, messagesBlock, inputMessageFile, selfFlag, notLoadedFlag, imgSrc) => {
//     let messageRow = getNewMessageRow(selfFlag),
//         attachedImageContainer = $('<div></div>').addClass('attached-image');
//
//     attachedImageContainer
//         .append(
//             $('<a></a>').addClass('fancybox').attr('href',imgSrc)
//                 .append(
//                     $('<img>').attr('src',imgSrc).css('opacity',(notLoadedFlag ? 0.6 : 1))
//                 )
//         );
//
//     if (notLoadedFlag) {
//         attachedImageContainer.addClass('not-loaded');
//         attachedImageContainer.append($('<div></div>').addClass('error image'));
//         attachedImageContainer.append(
//             $('<i></i>').addClass('icon-close2').click(function () {
//                 // $(this).parents('.attached-image.not-loaded').remove();
//                 messageRow.remove();
//                 inputMessageFile.val('');
//             })
//         );
//     }
//
//     messageRow.append(attachedImageContainer);
//     mainContainer.append(messageRow);
//
//     setTimeout(() => {
//         bindFancybox();
//         messagesBlock.mCustomScrollbar('scrollTo','bottom');
//     }, 200);
//
//     return attachedImageContainer;
// }
//
// const newMessageChat = (inputMessage, inputMessageFile, messagesBlock) => {
//     if (inputMessage.val() || inputMessageFile.val()) {
//         let formData = new FormData();
//
//         $('.error').html('');
//         formData.append('_token', window.tokenField);
//         formData.append('order_id', parseInt(window.orderId));
//         formData.append('body', inputMessage.val());
//
//         if (inputMessageFile.val()) formData.append('image', inputMessageFile[0].files[0]);
//
//         processingAjax(
//             newMessageUrl,
//             formData,
//             'post',
//             (data) => {
//                 inputMessage.val('');
//             },
//             (data) => {
//                 messagesBlock.mCustomScrollbar('scrollTo','bottom');
//             }
//         );
//     }
// }
//
// const getUrl = (form, url, callBack) => {
//     let formData = new FormData(),
//         submitButton = form.find('button[type=submit]');
//
//     form.find('input.error').removeClass('error');
//     form.find('div.error').html('');
//     form.find('input, select, textarea').each(function () {
//         if ($(this).attr('type') === 'file') formData.append($(this).attr('name'), $(this)[0].files[0]);
//         else if ($(this).attr('type') === 'radio') {
//             $(this).each(function () {
//                 if ($(this).is(':checked')) formData.append($(this).attr('name'), $(this).val());
//             });
//         }
//         else if ($(this).attr('type') === 'checkbox') {
//             if ($(this).is(':checked')) formData.append($(this).attr('name'), $(this).val());
//         }
//         else {
//             formData.append($(this).attr('name'), $(this).val());
//         }
//     });
//     submitButton.attr('disabled','disabled');
//
//     processingAjax(
//         url ? url : form.attr('action'),
//         formData,
//         form.attr('method'),
//         (data) => {
//             if (callBack) callBack(data);
//             submitButton.removeAttr('disabled');
//         },
//         (data) => {
//             submitButton.removeAttr('disabled');
//         }
//     );
// }
//
// const processingAjax = (url, formData, method, successCallback, failCallback) => {
//     $.ajax({
//         url: url,
//         data: formData,
//         processData: false,
//         contentType: false,
//         type: method,
//         cache: false,
//         success: (data) => {
//             if (successCallback) successCallback(data);
//         },
//         error: (data) => {
//             let replaceErr = {
//                 'body':'сообщения',
//                 'phone':'телефон',
//                 'email':'E-mail',
//                 'user_name':'имя'
//             };
//
//             $.each(data.responseJSON.errors, function (field, error) {
//                 var errorMsg = error[0];
//                 $.each(replaceErr, function (src,replace) {
//                     errorMsg = errorMsg.replace(src,replace);
//                 });
//                 $('input[name='+field+']').addClass('error');
//                 $('.error.'+field).html(errorMsg);
//             });
//             if (failCallback) failCallback(data);
//         }
//     });
// }
//
// const getCodeAgainCounter = (getCodeButton, timer) => {
//     let getRegisterCodeAgain = $('#get-code-again'),
//         countDown = setInterval(() => {
//         if (!timer) {
//             getCodeButton.removeClass('d-none');
//             clearInterval(countDown);
//         }
//         getRegisterCodeAgain.removeClass('d-none');
//         getRegisterCodeAgain.find('span').html(timer);
//         timer--;
//     }, 1000);
// };
//
// const validationDate = (date) => {
//     let inputDate = new Date(date[2], date[1], 0);
//     return date[0] && date[1] && date[2] && date[0] <= inputDate.getDate() && date[1] <= 12;
// };
//
// const lengthValidate = (form, fields) => {
//     let validationFlag = true;
//     $.each(fields, (k, field) => {
//         let input = form.find('input[name=' + field + ']');
//         if (!input.val().length) {
//             input.addClass('error');
//             form.find('.error.' + field).html(errorFieldMustBeFilledIn);
//             validationFlag = false;
//         }
//     });
//     return validationFlag;
// };
//
// const resetErrors = (form) => {
//     form.find('input.error').removeClass('error');
//     form.find('div.error').html('');
// };
//
// const bindChangePagination = (dataTable) => {
//     const paginationEvent = ('draw.dt');
//     dataTable.off(paginationEvent);
//     dataTable.on(paginationEvent, function () {
//         // bindOrderOperation(window.modalClosingConfirm,'close-order');
//         // bindOrderOperation(window.modalResumedConfirm,'resume-order');
//         // bindDelete();
//     });
// }
//
// const bindOrderPerformersList = () => {
//     const iconPerformersList =  $('i.performers-list'),
//         removePerformerModal = $('#remove-performer-modal'),
//         removePerformerModalYesButton = removePerformerModal.find('button.delete-yes'),
//         orderPerformersModal = $('#order-performers-modal'),
//         tablePerformers = orderPerformersModal.find('table.table.table-striped'),
//         headNoPerformers = orderPerformersModal.find('h4');
//
//     iconPerformersList.unbind();
//     iconPerformersList.click(function () {
//         let orderId = getId($(this), 'order-performers-', true);
//         $.post(getOrderPerformersUrl, {
//             '_token': window.tokenField,
//             'id': orderId,
//         }, (data) => {
//             if (data.performers.length) {
//                 headNoPerformers.addClass('d-none');
//                 tablePerformers.removeClass('d-none');
//
//                 $.each(data.performers, function (k, performer) {
//                     let tableRowLast = tablePerformers.find('tr').last(),
//                         tableRow = !k ? tableRowLast : tableRowLast.clone(),
//                         removePerformerIcon = tableRow.find('.order-cell-delete i');
//
//                     if (k) tablePerformers.append(tableRow);
//                     if (performer.avatar) tableRow.find('.avatar.cir').css(getAvatarProps(performer.avatar,performer.avatar_props,0.35))
//                     tableRow.find('.user-name').html(performer.full_name);
//                     tableRow.find('.user-age').html(performer.age);
//                     // removePerformerIcon.attr('id',performer.id);
//
//                     removePerformerIcon.click(function () {
//                         window.removingPerformerId = performer.id;
//                         window.orderId = orderId;
//                         orderPerformersModal.modal('hide');
//                         removePerformerModal.modal('show');
//                     });
//
//                     let ratingLine = tableRow.find('.rating-line');
//                     if (performer.rating) {
//                         for (let i=1;i<=ratingLine.find('i').length;i++) {
//                             let ratingStar = ratingLine.find('#rating-star-' + i);
//                             if (i <= performer.rating && ratingStar.hasClass('icon-star-empty3')) {
//                                 ratingStar.removeClass('icon-star-empty3').addClass('icon-star-full2');
//                             } else if (i > performer.rating && ratingStar.hasClass('icon-star-full2')) ratingStar.removeClass('icon-star-full2').addClass('icon-star-empty3');
//                         }
//                     } else ratingLine.find('i.icon-star-full2').removeClass('icon-star-full2').addClass('icon-star-empty3');
//
//                 });
//             } else {
//                 headNoPerformers.removeClass('d-none');
//                 tablePerformers.addClass('d-none');
//             }
//         });
//         orderPerformersModal.modal('show');
//     });
//
//     removePerformerModalYesButton.unbind();
//     removePerformerModalYesButton.click(function () {
//         $.post(removeOrderPerformerUrl, {
//             '_token': window.tokenField,
//             'order_id': window.orderId,
//             'user_id': window.removingPerformerId
//         }, (data) => {
//             if (data.performers_count) {
//                 getPerformersCountContainer(window.orderId).html(data.performers_count);
//             } else {
//                 movingOrderToOpen(getTableRow(window.orderId));
//             }
//             messageModal.find('h4').html(data.message);
//             removePerformerModal.modal('hide');
//             messageModal.modal('show');
//         });
//     });
// }
//
// const bindDelete = () => {
//     let deleteIcon = $('.icon-close2, .icon-bell-cross');
//     deleteIcon.unbind();
//     deleteIcon.click(function () {
//         window.deleteId = $(this).attr('del-data');
//         let deleteModal = $('#'+$(this).attr('modal-data')),
//             inputId = deleteModal.find('input[name=id]'),
//             possibleParentRow = $('#row-' + window.deleteId),
//             altParentRow = $('.row-' + window.deleteId);
//
//         window.deleteRow = possibleParentRow.length ? possibleParentRow : altParentRow;
//
//         if (inputId.length) inputId.val(window.deleteId);
//         deleteModal.modal('show');
//     });
// }
//
// const mapInit = (container) => {
//     window.myMap = new ymaps.Map(container, {
//         center: [55.76, 37.64],
//         zoom: 10,
//         controls: []
//     });
// }
//
// const getPlaceMark = (point, data) => {
//     return new ymaps.Placemark(point, data, {
//         preset: 'islands#darkOrangeCircleDotIcon'
//     });
// }
//
// const zoomAndCenterMap = () => {
//     window.myMap.setCenter(point, 17);
// }
//
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

// const nextPrevStep = (reverse, callBack) => {
//     let stepFadeOut = reverse ? step + 1 : step,
//         stepFadeIn = reverse ? step : step + 1,
//         tags = ['head1','head2','inputs','image','description'],
//         fadeOutSting = '',
//         fadeInSting = '',
//         callBackFlag = false;
//
//     $.each(tags, (k, tag) => {
//         let comma = (k + 1 !== tags.length ? ', ' : '');
//         fadeOutSting += '#' + tag + '-step' + stepFadeOut + comma;
//         fadeInSting += '#' + tag + '-step' + stepFadeIn + comma;
//     });
//
//     $(fadeOutSting).removeClass('d-block').fadeOut('slow',() => {
//         $(fadeInSting).removeClass('d-none').fadeIn();
//         if (callBack && !callBackFlag) {
//             callBack();
//             callBackFlag = true;
//         }
//     });
// }

// const setProgressBar = (progressBar) => {
//     let progress = step * 25 + '%';
//     progressBar.html(progress);
//     progressBar.animate({
//         'width': progress
//     },'fast');
// }

// const mapInitWithContainerForEditOrder = () => {
//     mapInit('image-step3');
//     if (point.length) {
//         window.placemark = getPlaceMark(point,{});
//         window.myMap.geoObjects.add(window.placemark);
//         zoomAndCenterMap();
//     }
// }

// const mapInitWithContainerForOrders = () => {
//     mapInit('map');
//     getPoints();
// }

// const getPoints = () => {
//     getUrl($('form'), (window.getPreviewFlag ? getPreviewUrl : null), (data) => {
//         window.placemarks = [];
//         let orders = data.orders;
//         window.subscriptions = [];
//         window.unreadOrders = [];
//         if (data.subscriptions.length) {
//             $.each(data.subscriptions, function (k,subscription) {
//                 window.subscriptions.push(subscription.user_id);
//                 if (subscription.orders.length) {
//                     $.each(subscription.orders, function (k,order) {
//                         window.unreadOrders.push(order.id);
//                     });
//                 }
//             });
//         }
//         if (data) {
//             $.each(orders, function (k,point) {
//                 let createdAt = new Date(point.created_at),
//                     meIsPerformer = false;
//                 if (point.performers.length) {
//                     for (let p=0;p<point.performers.length;p++) {
//                         if (point.performers[p].id === userId) {
//                             meIsPerformer = true;
//                             break;
//                         }
//                     }
//                 }
//
//                 if (!meIsPerformer) {
//                     window.placemarks.push(getPlaceMark([point.latitude, point.longitude], {
//                         placemarkId: k,
//                         // balloonContentHeader: point.order_type.name,
//                         // balloonContentBody: point.address,
//                         orderId: point.id,
//                         name: point.name,
//                         address: point.address,
//                         orderType: point.order_type.name,
//                         images: point.images,
//                         subtype: point.sub_type ? point.sub_type.name : null,
//                         need_performers: point.need_performers,
//                         performers: point.performers.length,
//                         user: point.user,
//                         date: createdAt.toLocaleDateString('ru-RU'),
//                         description_short: point.description_short,
//                         description_full: point.description_full
//                     }));
//
//                     if (window.getPreviewFlag) {
//                         window.getPreviewFlag = false;
//                         forceOpenOrder(0);
//                     } else if (window.openOrderId && window.openOrderId === point.id) {
//                         forceOpenOrder(k);
//                     }
//                 }
//             });
//
//             // Создаем собственный макет с информацией о выбранном геообъекте.
//             // let customBalloonContentLayout = ymaps.templateLayoutFactory.createClass([
//             //     '<ul class=list>',
//             //     // Выводим в цикле список всех геообъектов.
//             //     '{% for geoObject in properties.geoObjects %}',
//             //     '<li><div class="balloon-head">{{ geoObject.properties.balloonContentHeader|raw }}</div><div class="balloon-content">{{ geoObject.properties.balloonContentBody|raw }}</div></li>',
//             //     '{% endfor %}',
//             //     '</ul>'
//             // ].join(''));
//
//             window.clusterer = new ymaps.Clusterer({
//                 preset: 'islands#darkOrangeClusterIcons',
//                 clusterDisableClickZoom: true,
//                 clusterOpenBalloonOnClick: false,
//                 // Устанавливаем режим открытия балуна.
//                 // В данном примере балун никогда не будет открываться в режиме панели.
//                 clusterBalloonPanelMaxMapArea: 0,
//                 // По умолчанию опции балуна balloonMaxWidth и balloonMaxHeight не установлены для кластеризатора,
//                 // так как все стандартные макеты имеют определенные размеры.
//                 clusterBalloonMaxHeight: 200,
//                 // Устанавливаем собственный макет контента балуна.
//                 // clusterBalloonContentLayout: customBalloonContentLayout,
//             });
//
//             // Click on cluster
//             // window.clusterer.events.add('click', function (e) {
//             //     $.each(e.get('target').properties._data.geoObjects, function (k, object) {
//             //         console.log(object.properties.get('user'));
//             //     });
//             // });
//
//             window.myMap.geoObjects.events.add('click', function (e) {
//                 var target = e.get('target');
//
//                 target.options.set('iconColor', '#bc202e');
//                 if (target.properties.get('geoObjects')) {
//                     if (window.selectedPointsOpened) {
//                         removeSelectedPoints(target,() => { clickedToCluster(target);});
//                     } else clickedToCluster(target);
//                 } else {
//                     if (window.selectedPointsOpened) {
//                         removeSelectedPoints(target,() => { clickedToPoint(target);});
//                     } else clickedToPoint(target);
//                 }
//             });
//             addPointsToMap();
//         }
//     });
// }

// const addPointsToMap = () => {
//     window.clusterer.add(window.placemarks);
//     window.myMap.geoObjects.add(window.clusterer);
// }

// const showOrder = (point) => {
//     let properties = point.properties,
//         orderId = properties.get('orderId'),
//         currentSubType = properties.get('subtype'),
//         user = properties.get('user'),
//         orderName = properties.get('name'),
//         images = properties.get('images'),
//         descriptionShort = properties.get('description_short'),
//         descriptionFull = properties.get('description_full'),
//         orderContainer = $('<div></div>').addClass('order-block mb-3').attr('id','order-'+properties.get('placemarkId'));
//
//     // Check subscriptions
//     let subscribeBellClass = window.subscriptions.includes(user.id) ? 'icon-bell-cross' : 'icon-bell-check',
//         posUnreadOrder = window.unreadOrders.indexOf(orderId);
//
//     if (posUnreadOrder !== -1) {
//         delete window.unreadOrders[posUnreadOrder];
//         $.get(
//             readOrderUrl,
//             {'order_id': orderId}
//         ).done(() => {
//             $('#unread-order-' + orderId).remove();
//             checkDropDownMenuEmpty();
//         });
//     }
//
//     orderContainer
//         .append(
//             $('<h6></h6>').addClass('order-number').html(orderNumberText + orderId + fromText + properties.get('date'))
//         ).append(
//             $('<div></div>').addClass('w-100 d-flex align-items-center justify-content-between')
//                 .append(
//                     $('<div></div>').addClass('d-flex align-items-center justify-content-center')
//                         .append(
//                             getAvatarBlock(user, 0.35)
//                         ).append(
//                             $('<div></div>').css('width',215)
//                                 .append(
//                                     $('<div></div>').addClass('ms-3 fs-lg-6 fs-sm-7 user-name').html(getUserName(user))
//                                 ).append(
//                                     $('<div></div>').addClass('fs-lg-6 fs-sm-7 ms-3 user-age').html('')
//                                 )
//                             )
//                     ).append($('<i></i>').addClass('subscribe-icon ' + subscribeBellClass))
//         );
//     getUserAge(user.id);
//
//     if (images.length) {
//         let imagesContainer = $('<div></div>').addClass('images owl-carousel mt-3');
//         $.each(images, function (k, image) {
//             imagesContainer.append(
//                 $('<a></a>').addClass('fancybox').attr('href','/' + image.image).append(
//                     $('<div></div>').addClass('image').css('background-image','url(/'+image.image+')')
//                 )
//             );
//         });
//         orderContainer.append(imagesContainer);
//         enablePointImagesCarousel(imagesContainer,images.length > 1);
//     }
//
//     orderContainer.append($('<h2></h2>').addClass('order-name text-dark text-left mt-3 mb-4').html(orderName));
//     orderContainer.append($('<h2></h2>').addClass('order-type text-dark text-left mt-3 h5').html(properties.get('orderType')));
//
//     if (currentSubType) {
//         let subTypesContainer = $('<ul></ul>').addClass('subtypes').append($('<li></li>').html(currentSubType));
//         orderContainer.append(subTypesContainer);
//     }
//
//     orderContainer.append($('<p></p>').addClass('mb-1 text-left').html('<b>' + addressText +'</b>: ' + properties.get('address')));
//
//     if (descriptionShort) {
//         orderContainer
//             .append(
//                 $('<p></p>').addClass('fw-bold text-left mt-2 mb-0').html(descriptionShortText + ':')
//             ).append(
//             $('<p></p>').addClass('text-left order-description mb-1').html(descriptionShort)
//         );
//     }
//
//     if (descriptionFull) {
//         orderContainer
//             .append(
//                 $('<p></p>').addClass('fw-bold text-left mt-0 mb-2')
//                     .append($('<a></a>').addClass('description-full').html(descriptionFullText + ' »'))
//             );
//     }
//
//     orderContainer
//         .append(
//             $('<p></p>').addClass('text-left mb-2').html(
//                 '<b>' + numberOfPerformersText + ':</b> ' + properties.get('performers') + outOfText + properties.get('need_performers')
//             )
//         );
//
//     if (userId !== user.id) {
//         orderContainer.append($('<button></button>').addClass('respond-button btn btn-primary w-100').attr('type','button').append($('<span></span>').html(respondToAnOrderText)));
//     }
//
//     orderContainer.append($('<button></button>').addClass('cb-copy btn btn-primary w-100 mt-3').attr({
//         'type':'button',
//         'order_id':properties.get('orderId')
//     }).append($('<span></span>').html(copyOrderHrefToClipboardText)));
//
//     orderContainer.append($('<hr>'));
//     window.pointsContainer.append(orderContainer);
//     bindFancybox();
// }

// const removeSelectedPoints = (target, callBack) => {
//     if ( (window.cickedTarget && !target) || (window.cickedTarget && target && window.cickedTarget !== target) ) {
//         window.cickedTarget.options.set('iconColor', '#e6761b');

        // Change pagination on data-tables
        // bindChangePagination(dataTable);
        // bindDelete();
//     }
// }

// const clickYesDeleteOnModal = (dataTable, useCounter) => {
//     // Click YES on delete modal
//     $('.delete-yes').click(function () {
//         let deleteModal = $(this).parents('.modal');
//         deleteModal.modal('hide');
//         addLoader();
//
//         $.post(deleteModal.attr('del-function'), {
//             '_token': window.tokenField,
//             'id': window.deleteId,
//         }, () => {
//             deleteDataTableRows(window.deleteRow.parents('.content-block'), window.deleteRow, useCounter);
//             removeLoader();
//         });
//     });
// }
//
// const movingOrderToApproving = (tableRow) => {
//     changeTableRowLabel(tableRow, 'closed', 'in-approve', window.orderStatuses[3]);
//     addEditOrderIcon(tableRow);
//     addDeleteIcon(tableRow);
//
//     deleteDataTableRows($('#content-archive'), tableRow, true);
//     addDataTableRow($('#content-approving'), tableRow, true);
// }
//
// const movingOrderToOpen = (tableRow) => {
//     if (tableRow.parents('.content-block').attr('id') !== 'content-active') {
//         changeTableRowLabel(tableRow, 'in-approve', 'open', window.orderStatuses[2]);
//         deleteDataTableRows($('#content-approving'), tableRow, true);
//         addDataTableRow($('#content-active'), tableRow, true);
//     } else {
//         changeTableRowLabel(tableRow, 'in-progress', 'open', window.orderStatuses[2]);
//         addEditOrderIcon(tableRow);
//         addDeleteIcon(tableRow);
//     }
// }
//
// const movingOrderToInProgress = (tableRow, performers) => {
//     changeTableRowLabel(tableRow, 'in-approve', 'in-progress', window.orderStatuses[1]);
//     addPerformersIcon(tableRow, performers);
//     addCloseOrderButton(tableRow);
// }
//
// const movingOrderToArchive = (tableRow) => {
//     changeTableRowLabel(tableRow, 'in-progress', 'closed', window.orderStatuses[0]);
//     changeTableRowButton(tableRow, 'close-order', 'resume-order', resumeOrderText);
//
//     getOrderCellEdit(tableRow).addClass('empty').html('');
//
//     deleteDataTableRows($('#content-active'), tableRow, true);
//     addDataTableRow($('#content-archive'), tableRow, true);
//     bindOrderOperation(window.modalResumedConfirm,'resume-order');
// }
//
// const getOrderCellEdit = (tableRow) => {
//     return tableRow.find('.order-cell-edit');
// }
//
// const addEditOrderIcon = (tableRow) => {
//     tableRow.find('.order-cell-edit').html('').removeClass('empty').addClass('icon').append(
//         $('<a></a>').attr({
//             'title': editOrderText,
//             'href': editOrderUrl + '?id=' + getId(tableRow, 'row-', false)
//         }).append(
//             $('<i></i>').addClass('icon-pencil5')
//         )
//     );
// }
//
// const addPerformersIcon = (tableRow, performers) => {
//     getOrderCellEdit(tableRow).html('').append(
//         $('<nobr></nobr>').append(
//             $('<i></i>').attr({
//                 'id': 'order-performers-' + getId(tableRow, 'row-', false),
//                 'title': participantsText,
//             }).addClass('performers-list icon-users4 me-1')
//         ).append(
//             $('<span></span>').html(performers)
//         )
//     );
//     bindOrderPerformersList();
// }
//
// const addCloseOrderButton = (tableRow) => {
//     tableRow.find('.order-cell-delete').html('').removeClass('order-cell-delete').addClass('order-cell-button').append(
//         $('<button></button>').attr('type','button').addClass('btn btn-secondary close-order micro').append(
//             $('<span></span>').html(closeOrderText)
//         )
//     );
//     bindOrderOperation(window.modalClosingConfirm,'close-order');
// }
//
// const addDeleteIcon = (tableRow) => {
//     tableRow.find('.order-cell-button').html('').removeClass('order-cell-button').addClass('order-cell-delete').append(
//         $('<i></i>').attr({
//             'title': deleteOrderText,
//             'modal-data': 'delete-modal',
//             'del-data': getId(tableRow, 'row-', false)
//         }).addClass('icon-close2')
//     );
//     bindDelete();
// }
//
// const deleteDataTableRows = (contentBlockTab, row, useCounter) => {
//     let baseTable = row.parents('.datatable-basic.default'),
//         dataTable = contentBlockTab.find('table'+window.dataTableClasses).DataTable();
//
//     if (useCounter) changeDataCounter(contentBlockTab, -1);
//
//     if (row.length > 1) {
//         row.each(function () {
//             dataTable.row($(this)).remove();
//         });
//     } else dataTable.row(row).remove();
//
//     if (!dataTable.rows().count()) {
//         dataTable.destroy();
//         baseTable.remove();
//         if (contentBlockTab) contentBlockTab.find('h4').removeClass('d-none');
//     }
//
//     dataTable.draw();
//     bindChangePagination(dataTable);
//     bindDelete();
// }
//
// const changeTableRowLabel = (tableRow, removingClass, addingClass, labelText) => {
//     tableRow.find('.label').removeClass(removingClass).addClass(addingClass).html(labelText);
// }
//
// const changeTableRowButton = (tableRow, removingClass, addingClass, buttonText) => {
//     let button = tableRow.find('button.' + removingClass);
//     button.removeClass(removingClass).addClass(addingClass);
//     button.find('span').html(buttonText);
// }
//
// const addDataTableRow = (contentBlockTab, row, useCounter) => {
//     if (useCounter) changeDataCounter(contentBlockTab, 1);
//     let dataTable = contentBlockTab.find('table'+window.dataTableClasses);
//
//     if (!dataTable.length) {
//         contentBlockTab.find('h4').addClass('d-none');
//         let newTable = $('<table></table>').addClass(window.dataTableClasses.replaceAll('.',' ').trim());
//         contentBlockTab.prepend(newTable);
//         dataTable = dataTableAttributes(newTable, 8);
//     } else {
//         dataTable = dataTable.DataTable();
//     }
//
//     if (row.length > 1) {
//         row.each(function () {
//             dataTable.row.add($(this));
//         });
//     } else dataTable.row.add(row);
//
//     dataTable.draw();
//     bindChangePagination(dataTable);
//     bindDelete();
// }
//
// const changeDataCounter = (contentBlockTab, increment) => {
//     let contentId = getId(contentBlockTab, 'content-', false),
//         containerCounter = $('#top-submenu-'+contentId).next('sup'),
//         counterVal = parseInt(containerCounter.html());
//
//     counterVal += increment;
//     containerCounter.html(counterVal);
// }
//
// const appendDotInLeftMenu = (leftMenuId) => {
//     let leftMenu = $('#' + leftMenuId);
//     if (!leftMenu.find('.dot').length) {
//         leftMenu.append(
//             $('<div></div>').addClass('dot')
//         );
//     }
// }
//
//
// const owlSettings = (margin, nav, timeout, responsive, autoplay) => {
//     let navButtonBlack1 = '<img src="/images/arrow_left.svg" />',
//         navButtonBlack2 = '<img src="/images/arrow_right.svg" />';
//
//     return {
//         margin: margin,
//         loop: autoplay,
//         nav: nav,
//         autoplay: autoplay,
//         autoplayTimeout: timeout,
//         dots: !nav,
//         responsive: responsive,
//         navText: [navButtonBlack1, navButtonBlack2]
//     }
// }
//
// const nextPrevStep = (reverse, callBack) => {
//     let stepFadeOut = reverse ? step + 1 : step,
//         stepFadeIn = reverse ? step : step + 1,
//         tags = ['head1','head2','inputs','image','description'],
//         fadeOutSting = '',
//         fadeInSting = '',
//         callBackFlag = false;
//
//     $.each(tags, (k, tag) => {
//         let comma = (k + 1 !== tags.length ? ', ' : '');
//         fadeOutSting += '#' + tag + '-step' + stepFadeOut + comma;
//         fadeInSting += '#' + tag + '-step' + stepFadeIn + comma;
//     });
//
//     $(fadeOutSting).removeClass('d-block').fadeOut('slow',() => {
//         $(fadeInSting).removeClass('d-none').fadeIn();
//         if (callBack && !callBackFlag) {
//             callBack();
//             callBackFlag = true;
//         }
//     });
// }
//
// const setProgressBar = (progressBar) => {
//     let progress = step * 25 + '%';
//     progressBar.html(progress);
//     progressBar.animate({
//         'width': progress
//     },'fast');
// }
//
// const mapInitWithContainerForEditOrder = () => {
//     mapInit('image-step3');
//     if (point.length) {
//         window.placemark = getPlaceMark(point,{});
//         window.myMap.geoObjects.add(window.placemark);
//         zoomAndCenterMap();
//     }
// }
//
// const mapInitWithContainerForOrders = () => {
//     mapInit('map');
//     getPoints();
// }
//
// const getPoints = () => {
//     getUrl($('form'), (window.getPreviewFlag ? getPreviewUrl : null), (data) => {
//         window.placemarks = [];
//         let orders = data.orders;
//         window.subscriptions = [];
//         window.unreadOrders = [];
//         if (data.subscriptions.length) {
//             $.each(data.subscriptions, function (k,subscription) {
//                 window.subscriptions.push(subscription.user_id);
//                 if (subscription.orders.length) {
//                     $.each(subscription.orders, function (k,order) {
//                         window.unreadOrders.push(order.id);
//                     });
//                 }
//             });
//         }
//         if (data) {
//             $.each(orders, function (k,point) {
//                 let createdAt = new Date(point.created_at),
//                     meIsPerformer = false;
//                 if (point.performers.length) {
//                     for (let p=0;p<point.performers.length;p++) {
//                         if (point.performers[p].id === userId) {
//                             meIsPerformer = true;
//                             break;
//                         }
//                     }
//                 }
//
//                 if (!meIsPerformer) {
//                     window.placemarks.push(getPlaceMark([point.latitude, point.longitude], {
//                         placemarkId: k,
//                         // balloonContentHeader: point.order_type.name,
//                         // balloonContentBody: point.address,
//                         orderId: point.id,
//                         name: point.name,
//                         address: point.address,
//                         orderType: point.order_type.name,
//                         images: point.images,
//                         subtype: point.sub_type ? point.sub_type.name : null,
//                         need_performers: point.need_performers,
//                         performers: point.performers.length,
//                         user: point.user,
//                         date: createdAt.toLocaleDateString('ru-RU'),
//                         description_short: point.description_short,
//                         description_full: point.description_full
//                     }));
//
//                     if (window.getPreviewFlag) {
//                         window.getPreviewFlag = false;
//                         forceOpenOrder(0);
//                     } else if (window.openOrderId && window.openOrderId === point.id) {
//                         forceOpenOrder(k);
//                     }
//                 }
//             });
//
//             // Создаем собственный макет с информацией о выбранном геообъекте.
//             // let customBalloonContentLayout = ymaps.templateLayoutFactory.createClass([
//             //     '<ul class=list>',
//             //     // Выводим в цикле список всех геообъектов.
//             //     '{% for geoObject in properties.geoObjects %}',
//             //     '<li><div class="balloon-head">{{ geoObject.properties.balloonContentHeader|raw }}</div><div class="balloon-content">{{ geoObject.properties.balloonContentBody|raw }}</div></li>',
//             //     '{% endfor %}',
//             //     '</ul>'
//             // ].join(''));
//
//             window.clusterer = new ymaps.Clusterer({
//                 preset: 'islands#darkOrangeClusterIcons',
//                 clusterDisableClickZoom: true,
//                 clusterOpenBalloonOnClick: false,
//                 // Устанавливаем режим открытия балуна.
//                 // В данном примере балун никогда не будет открываться в режиме панели.
//                 clusterBalloonPanelMaxMapArea: 0,
//                 // По умолчанию опции балуна balloonMaxWidth и balloonMaxHeight не установлены для кластеризатора,
//                 // так как все стандартные макеты имеют определенные размеры.
//                 clusterBalloonMaxHeight: 200,
//                 // Устанавливаем собственный макет контента балуна.
//                 // clusterBalloonContentLayout: customBalloonContentLayout,
//             });
//
//             // Click on cluster
//             // window.clusterer.events.add('click', function (e) {
//             //     $.each(e.get('target').properties._data.geoObjects, function (k, object) {
//             //         console.log(object.properties.get('user'));
//             //     });
//             // });
//
//             window.myMap.geoObjects.events.add('click', function (e) {
//                 var target = e.get('target');
//
//                 target.options.set('iconColor', '#bc202e');
//                 if (target.properties.get('geoObjects')) {
//                     if (window.selectedPointsOpened) {
//                         removeSelectedPoints(target,() => { clickedToCluster(target);});
//                     } else clickedToCluster(target);
//                 } else {
//                     if (window.selectedPointsOpened) {
//                         removeSelectedPoints(target,() => { clickedToPoint(target);});
//                     } else clickedToPoint(target);
//                 }
//             });
//             addPointsToMap();
//         }
//     });
// }
//
// const addPointsToMap = () => {
//     window.clusterer.add(window.placemarks);
//     window.myMap.geoObjects.add(window.clusterer);
// }
//
// const showOrder = (point) => {
//     let properties = point.properties,
//         orderId = properties.get('orderId'),
//         currentSubType = properties.get('subtype'),
//         user = properties.get('user'),
//         orderName = properties.get('name'),
//         images = properties.get('images'),
//         descriptionShort = properties.get('description_short'),
//         descriptionFull = properties.get('description_full'),
//         orderContainer = $('<div></div>').addClass('order-block mb-3').attr('id','order-'+properties.get('placemarkId'));
//
//     // Check subscriptions
//     let subscribeBellClass = window.subscriptions.includes(user.id) ? 'icon-bell-cross' : 'icon-bell-check',
//         posUnreadOrder = window.unreadOrders.indexOf(orderId);
//
//     if (posUnreadOrder !== -1) {
//         delete window.unreadOrders[posUnreadOrder];
//         $.get(
//             readOrderUrl,
//             {'order_id': orderId}
//         ).done(() => {
//             $('#unread-order-' + orderId).remove();
//             checkDropDownMenuEmpty();
//         });
//     }
//
//     orderContainer
//         .append(
//             $('<h6></h6>').addClass('order-number').html(orderNumberText + orderId + fromText + properties.get('date'))
//         ).append(
//             $('<div></div>').addClass('w-100 d-flex align-items-center justify-content-between')
//                 .append(
//                     $('<div></div>').addClass('d-flex align-items-center justify-content-center')
//                         .append(
//                             getAvatarBlock(user, 0.35)
//                         ).append(
//                             $('<div></div>').css('width',215)
//                                 .append(
//                                     $('<div></div>').addClass('ms-3 fs-lg-6 fs-sm-7 user-name').html(user.family+' '+user.name)
//                                 ).append(
//                                     $('<div></div>').addClass('fs-lg-6 fs-sm-7 ms-3 user-age').html(window.useAge)
//                                 )
//                             )
//                     ).append($('<i></i>').addClass('subscribe-icon ' + subscribeBellClass))
//         );
//
//     if (images.length) {
//         let imagesContainer = $('<div></div>').addClass('images owl-carousel mt-3');
//         $.each(images, function (k, image) {
//             imagesContainer.append(
//                 $('<a></a>').addClass('fancybox').attr('href','/' + image.image).append(
//                     $('<div></div>').addClass('image').css('background-image','url(/'+image.image+')')
//                 )
//             );
//         });
//         orderContainer.append(imagesContainer);
//         enablePointImagesCarousel(imagesContainer,images.length > 1);
//     }
//
//     orderContainer.append($('<h2></h2>').addClass('order-name text-dark text-left mt-3 mb-4').html(orderName));
//     orderContainer.append($('<h2></h2>').addClass('order-type text-dark text-left mt-3 h5').html(properties.get('orderType')));
//
//     if (currentSubType) {
//         let subTypesContainer = $('<ul></ul>').addClass('subtypes').append($('<li></li>').html(currentSubType));
//         orderContainer.append(subTypesContainer);
//     }
//
//     orderContainer.append($('<p></p>').addClass('mb-1 text-left').html('<b>' + addressText +'</b>: ' + properties.get('address')));
//
//     if (descriptionShort) {
//         orderContainer
//             .append(
//                 $('<p></p>').addClass('fw-bold text-left mt-2 mb-0').html(descriptionShortText + ':')
//             ).append(
//             $('<p></p>').addClass('text-left order-description mb-1').html(descriptionShort)
//         );
//     }
//
//     if (descriptionFull) {
//         orderContainer
//             .append(
//                 $('<p></p>').addClass('fw-bold text-left mt-0 mb-2')
//                     .append($('<a></a>').addClass('description-full').html(descriptionFullText + ' »'))
//             );
//     }
//
//     orderContainer
//         .append(
//             $('<p></p>').addClass('text-left mb-2').html(
//                 '<b>' + numberOfPerformersText + ':</b> ' + properties.get('performers') + outOfText + properties.get('need_performers')
//             )
//         );
//
//     if (userId !== user.id) {
//         orderContainer.append($('<button></button>').addClass('respond-button btn btn-primary w-100').attr('type','button').append($('<span></span>').html(respondToAnOrderText)));
//     }
//
//     orderContainer.append($('<button></button>').addClass('cb-copy btn btn-primary w-100 mt-3').attr({
//         'type':'button',
//         'order_id':properties.get('orderId')
//     }).append($('<span></span>').html(copyOrderHrefToClipboardText)));
//
//     orderContainer.append($('<hr>'));
//     window.pointsContainer.append(orderContainer);
//     bindFancybox();
// }
//
// const removeSelectedPoints = (target, callBack) => {
//     if ( (window.cickedTarget && !target) || (window.cickedTarget && target && window.cickedTarget !== target) ) {
//         window.cickedTarget.options.set('iconColor', '#e6761b');
//
//         window.selectedPoints.animate({'margin-left': -1 * (window.selectedPoints.width() + 150)}, 'slow', function () {
//             window.selectedPointsOpened = false;
//             if (callBack) callBack();
//         });
//     }
// }
//
// const clickedToCluster = (target, objects) => {
//     window.cickedTarget = target;
//     purgePointsContainer();
//     $.each(target.properties.get('geoObjects'), function (k, object) {
//         showOrder(object);
//     });
//     setBindsAndOpen();
// }
//
// const clickedToPoint = (point) => {
//     window.cickedTarget = point;
//     purgePointsContainer();
//     showOrder(point);
//     setBindsAndOpen();
// }
//
// const purgePointsContainer = () => {
//     window.pointsContainer.removeAttr('class').html('');
// }
//
// const setBindsAndOpen = () => {
//     // Set custom scroll bar
//     window.selectedPoints.mCustomScrollbar({
//         axis: 'y',
//         theme: 'light-3',
//         alwaysShowScrollbar: 0
//     });
//
//     // Bind click respond button
//     $('.respond-button').click(function (e) {
//         e.preventDefault();
//         let point = getPlaceMarkOnMap($(this)),
//             properties = point.properties,
//             orderId = properties.get('orderId'),
//             orderRespondModal = $('#order-respond-modal');
//
//         $.post(orderResponseUrl, {
//             '_token': window.tokenField,
//             'id': orderId,
//         }, () => {
//             orderRespondModal.find('.order-number').html(orderId);
//             orderRespondModal.find('.order-date').html(properties.get('date'));
//             orderRespondModal.find('.order-type').html(properties.get('orderType'));
//             orderRespondModal.find('.order-address').html(properties.get('address'));
//             orderRespondModal.modal('show');
//             // window.clusterer.remove(point);
//             removeSelectedPoints();
//         });
//     });
//
//     // Bind subscribe button
//     $('.subscribe-icon').click(function (e) {
//         e.preventDefault();
//         let button = $(this),
//             point = getPlaceMarkOnMap(button),
//             userId = point.properties.get('user').id;
//
//         $.get(
//             subscribeUrl,
//             {'user_id': userId}
//         ).done((data) => {
//             button.fadeOut(() => {
//                 button.toggleClass('icon-bell-cross',data.subscription).toggleClass('icon-bell-check',!data.subscription);
//                 button.fadeIn();
//             });
//             window.subscriptions.push(userId);
//         });
//     });
//
//     // Click to description full
//     $('.description-full').click(function (e) {
//         e.preventDefault();
//         let point = getPlaceMarkOnMap($(this)),
//             properties = point.properties,
//             fullDescriptionModal = $('#order-full-description-modal');
//
//         fullDescriptionModal.find('h5').html(descriptionFullOfOrderText + properties.get('orderId') + '<br>' + fromText + properties.get('date'));
//         fullDescriptionModal.find('.modal-body p').html(properties.get('description_full'));
//         fullDescriptionModal.modal('show');
//     });
//
//     // Open selected points
//     window.selectedPoints.animate({'margin-left': 0}, 'slow', function () {
//         window.selectedPointsOpened = true;
//     });
//
//     // Copy order href
//     $('.cb-copy').click(function () {
//         let orderId = $(this).attr('order_id'),
//             href = ordersUrl + '?id=' + orderId;
//         if (navigator.clipboard) {
//             window.navigator.clipboard.writeText(href).then(() => {
//                 window.messageModal.find('h4').html(hrefIsCopiedText);
//                 window.messageModal.modal('show');
//             });
//         }
//     });
// }
//
//
// const getPlaceMarkOnMap = (obj) => {
//     let placemarkId = getId((obj).parents('.order-block'), 'order-', true);
//     return window.placemarks[placemarkId];
// }
//
// const forceOpenOrder = (k) => {
//     window.openOrderId = null;
//     window.cickedTarget = window.placemarks[k];
//     window.placemarks[k].options.set('iconColor', '#bc202e');
//     showOrder(window.placemarks[k]);
//     setBindsAndOpen();
// }
//
// const bindOrderOperation = (modalConfirm, buttonClass) => {
//     let buttons = $('.' + buttonClass);
//     buttons.unbind();
//     buttons.click(function () {
//         window.tableRow = $(this).parents('tr');
//         window.orderId = getId(window.tableRow, 'row-', true);
//         modalConfirm.modal('show');
//     });
// }

//
// const checkDropDownMenuEmpty = () => {
//     if (!window.dropDown.html()) {
//         window.rightButtonBlock.find('.dot').remove();
//     }
//
//
// }
//
// const getId = (obj, replace, returnInt) => {
//     let id = obj.attr('id').replace(replace, '');
//     return returnInt ? parseInt(id) : id;
// }
//
//
// const getUserAge = (userId) => {
//     $.get(
//         getUserAgeUrl,
//         {'id': userId},
//         (data) => {
//             $('.user-age').html(data.age);
//         }
//     );
// }

const bellRing = (degrees) => {
    window.rightButtonBlock.css({'-webkit-transform' : 'rotate('+ degrees +'deg)',
        '-moz-transform' : 'rotate('+ degrees +'deg)',
        '-ms-transform' : 'rotate('+ degrees +'deg)',
        'transform' : 'rotate('+ degrees +'deg)'});
}

const getAvatarBlock = (user, coof) => {
    let avatar = user.avatar ? user.avatar : '/images/def_avatar.svg';
    return $('<div></div>').addClass('avatar cir').css(getAvatarProps(avatar, user.avatar_props, coof));
}

const getAvatarProps = (avatar, props, coof) => {
    let avatarProps = {'background-image':'url('+avatar+')'};
    if (props) {
        $.each(props, function (prop, value) {
            avatarProps[prop] = (prop === 'background-size' ? value : value * coof);
        });
    }
    return avatarProps;
}

const getUserName = (user) => {
    return user.name + ' ' + user.family;
}
