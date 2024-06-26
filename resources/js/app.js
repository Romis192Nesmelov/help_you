import './bootstrap';
import {createApp} from "vue/dist/vue.esm-bundler";
import mitt from 'mitt';
import FeedbackComponent from "./components/FeedbackComponent.vue";
import MessageComponent from "./components/MessageComponent.vue";
import TopLineComponent from "./components/TopLineComponent.vue";
import LeftMenuComponent from "./components/LeftMenuComponent.vue";
import AccountComponent from "./components/AccountComponent.vue";
import MyOrdersListComponent from "./components/MyOrdersListComponent.vue";
import MyHelpListComponent from "./components/MyHelpListComponent.vue";
import MySubscriptionsComponent from "./components/MySubscriptionsComponent.vue";
import IncentivesComponent from "./components/IncentivesComponent.vue";
import MyChatsComponent from "./components/MyChatsComponent.vue";
import EditOrderComponent from "./components/EditOrderComponent.vue";
import OrdersComponent from "./components/OrdersComponent.vue";
import ChatComponent from "./components/ChatComponent.vue";

const app = createApp({
    components: {
        FeedbackComponent,
        MessageComponent,
        TopLineComponent,
        LeftMenuComponent,
        AccountComponent,
        MyOrdersListComponent,
        MyHelpListComponent,
        IncentivesComponent,
        MySubscriptionsComponent,
        MyChatsComponent,
        EditOrderComponent,
        OrdersComponent,
        ChatComponent
    }
});

window.tokenField = $('input[name=_token]').val();
window.phoneMask = "+n(999)999-99-99";
window.codeMask = "99-99-99";
window.bornMask = "99-99-9999";
window.phoneRegExp = /^((\+)?(\d)(\s)?(\()?[0-9]{3}(\))?(\s)?([0-9]{3})(\-)?([0-9]{2})(\-)?([0-9]{2}))$/gi;
window.bornRegExp = /^([0-3][0-9])-([0-1][0-9])-((19([0-9][0-9]))|(20[0-9][0-9]))$/gi;
window.emailRegExp = /^[a-zA-Z0-9][\-_\.\+\!\#\$\%\&\'\*\/\=\?\^\`\{\|]{0,1}([a-zA-Z0-9][\-_\.\+\!\#\$\%\&\'\*\/\=\?\^\`\{\|]{0,1})*[a-zA-Z0-9]@[a-zA-Z0-9][-\.]{0,1}([a-zA-Z][-\.]{0,1})*[a-zA-Z0-9]\.[a-zA-Z0-9]{1,}([\.\-]{0,1}[a-zA-Z]){0,}[a-zA-Z0-9]{0,}$/gi;
window.codeRegExp = /^((\d){2}(\-)(\d){2}(\-)(\d){2})$/gi;
window.singlePoint = null;

window.toCamelize = str => str.replace(/-|_./g, x=>x[1].toUpperCase());

window.userRating = (ratings) => {
    if (ratings.length) {
        let ratingVal = 0;
        $.each(ratings, function (k,rating) {
            ratingVal += rating.value;
        });
        return Math.round(ratingVal/ratings.length);
    } else return 0;
}

window.showMessage = (message) => {
    const messageModal = $('#message-modal');
    messageModal.find('h4').html(message);
    messageModal.modal('show');
}

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
}

window.bellRinging = (bellIcon) => {
    let counter = 0,
        degrees = 15,
        bellRinging = setInterval(() => {
            degrees *= -1;
            bellRing(degrees);
            counter++;
            if (counter > 5) {
                clearInterval(bellRinging);
                bellRing(0);
            }
        }, 200);

    const bellRing = (degrees) => {
        bellIcon.css({'-webkit-transform' : 'rotate('+ degrees +'deg)',
            '-moz-transform' : 'rotate('+ degrees +'deg)',
            '-ms-transform' : 'rotate('+ degrees +'deg)',
            'transform' : 'rotate('+ degrees +'deg)'});
    }
}

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
}

window.getPlaceMark = (point, data) => {
    return new ymaps.Placemark(point, data, {
        preset: 'islands#darkOrangeCircleDotIcon'
    });
}

window.zoomAndCenterMap = () => {
    window.myMap.setCenter(window.singlePoint, 17);
}

window.fancyBoxSettings = {
    'autoScale': true,
    'touch': false,
    'transitionIn': 'elastic',
    'transitionOut': 'elastic',
    'speedIn': 500,
    'speedOut': 300,
    'autoDimensions': true,
    'centerOnScroll': true
}

window.bindFancybox = () => {
    setTimeout(() => {
        $('.fancybox').fancybox(window.fancyBoxSettings);
    }, 200);
}

window.enablePointImagesCarousel = () => {
    $('.images.owl-carousel').trigger('destroy.owl.carousel');
    setTimeout(() => {
        let navButtonBlack1 = '<img src="/images/arrow_left.svg" />',
            navButtonBlack2 = '<img src="/images/arrow_right.svg" />';

        $('.images.owl-carousel').owlCarousel({
            margin: 10,
            loop: true,
            nav: false,
            autoplay: true,
            autoplayTimeout: 6000,
            dots: true,
            responsive: {0: {items: 1}},
            navText: [navButtonBlack1, navButtonBlack2]
        });
    }, 200);
}

window.scrollBottomMessages = () => {
    $('#messages').mCustomScrollbar('scrollTo','bottom');
}

window.emitter = mitt();
app.config.globalProperties.emitter = window.emitter;
app.mount('#app');

$(document).ready(function () {
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

    setTimeout(function () {
        removeLoader();
    }, 500);

    // Fancybox init
    window.bindFancybox();

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

    // ACCOUNT BLOCK BEGIN
    window.avatarBlock = $('#avatar-block .avatar.cir');
    window.defAvatar = '/images/def_avatar.svg';
    imagePreview(window.avatarBlock, window.defAvatar);
    // ACCOUNT BLOCK END

    // EDIT ORDER BLOCK BEGIN
    imagePreview($('.order-photo'));

    if ($('#map-steps').length) {
        ymaps.ready(() => {
            mapInit('map-steps');
            if (window.singlePoint) {
                window.placemark = window.getPlaceMark(window.singlePoint,{});
                window.myMap.geoObjects.add(window.placemark);
                window.zoomAndCenterMap();
            }
        });
    }
    // EDIT ORDER BLOCK END

    // ORDERS BLOCK BEGIN
    if ($('#map').length) {
        window.selectedPointsDie = $('#selected-points');
        window.selectedPointsDie.mCustomScrollbar({
            axis: 'y',
            theme: 'light-3',
            alwaysShowScrollbar: 0
        });
        ymaps.ready(() => {
            mapInit('map');
            window.emitter.emit('map-is-ready');
        });
    }
    // ORDERS BLOCK END
    $('#messages').mCustomScrollbar({
        axis: 'y',
        theme: 'light-3',
        alwaysShowScrollbar: 1,
        scrollTo: 'bottom'
    });
});

const mapInit = (container) => {
    window.myMap = new ymaps.Map(container, {
        center: [55.76, 37.64],
        zoom: 10,
        controls: []
    });
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
                clearInputIcon.removeClass('d-none');
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

            if (currentContainer.hasClass('order-photo')) {
                let id = parseInt(currentContainer.attr('id').replace('photo',''));
                currentContainer.find('.icon-file-plus2').show();
                window.emitter.emit('remove-order-photo',id);
            }
        });
    });
}
