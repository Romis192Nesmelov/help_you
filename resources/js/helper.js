window.tokenField = $('input[name=_token]').val();
window.bornMask = "99-99-9999";
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
window.singlePoint = null;

const imagePreview = (container, defImage) => {
    container.each(function () {
        let currentContainer = $(this),
            hoverImg = currentContainer.find('img.hover-image'),
            previewImage = currentContainer.find('img.image'),
            inputFile = currentContainer.find('input[type=file]'),
            addFileIcon = currentContainer.find('i.icon-file-plus2'),
            clearInputIcon = currentContainer.find('i.icon-close2');

        inputFile.change(function () {
            let input = $(this)[0].files[0];

            if (input.type.match('image.*')) {
                let reader = new FileReader();
                reader.onload = function (e) {
                    if (previewImage.length) previewImage.attr('src',e.target.result);
                    else currentContainer.css('background-image', 'url(' + e.target.result + ')');
                    currentContainer.trigger('onload_image',[e.target.result]);
                    // if (callBack) callBack(e.target.result);
                };
                reader.readAsDataURL(input);
                addFileIcon.hide();
                clearInputIcon.removeClass('d-none').removeClass('hidden');
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
            addFileIcon.removeClass('d-none').removeClass('hidden');
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

window.userRating = (ratings) => {
    if (ratings.length) {
        let ratingVal = 0;
        $.each(ratings, function (k,rating) {
            ratingVal += rating.value;
        });
        return Math.round(ratingVal/ratings.length);
    } else return 0;
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

window.initImages = () => {
    $(".file-styled").uniform({
        wrapperClass: 'bg-orange-800',
        fileButtonHtml: '<i class="icon-file-plus"></i>'
    });

    window.avatarBlock = $('.avatar.cir');
    window.defAvatar = '/images/def_avatar.svg';
    imagePreview(window.avatarBlock, window.defAvatar);
    imagePreview($('.order-photo'));
    imagePreview($('.admin-image'));
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

window.showMessage = (message) => {
    const messageModal = $('#message-modal');
    messageModal.find('h4').html(message);
    messageModal.modal('show');
}

window.bindFancybox = () => {
    setTimeout(() => {
        $('.fancybox').fancybox(window.fancyBoxSettings);
    }, 500);
}

window.getPlaceMark = (point, data) => {
    return new ymaps.Placemark(point, data, {
        preset: 'islands#darkOrangeCircleDotIcon'
    });
}

window.zoomAndCenterMap = () => {
    window.myMap.setCenter(window.singlePoint, 17);
}

window.mapInit = (container) => {
    window.myMap = new ymaps.Map(container, {
        center: [55.76, 37.64],
        zoom: 10,
        controls: []
    });
}

window.mapStepsInit = () => {
    if ($('#map-steps').length) {
        ymaps.ready(() => {
            window.mapInit('map-steps');
            if (window.singlePoint) {
                window.placemark = window.getPlaceMark(window.singlePoint,{});
                window.myMap.geoObjects.add(window.placemark);
                window.zoomAndCenterMap();
            }
        });
    }
}

window.convertTime = (date) => {
    return new Date(date).toLocaleDateString('ru-RU').replaceAll('.','/');
}

window.getDate = (date) => {
    let dateArr = date.split('/'),
        newDate = dateArr[1] + '.' + dateArr[0] + '.' + dateArr[2];
    return new Date(newDate).getTime();
}

window.cropContent = (string,max) => {
    string = string.toString();
    return string.length > max ? string.substr(0,max) + '…' : string;
}
