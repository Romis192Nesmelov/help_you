export const initAvatar = () => {
    window.avatarBlock = $('.avatar.cir');
    window.defAvatar = '/images/def_avatar.svg';
    imagePreview(window.avatarBlock, window.defAvatar);
}

export const imagePreview = (container, defImage) => {
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
