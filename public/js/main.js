// window.stop();
window.phoneRegExp = /^((\+)?(\d)(\s)?(\()?[0-9]{3}(\))?(\s)?([0-9]{3})(\-)?([0-9]{2})(\-)?([0-9]{2}))$/gi;
window.codeRegExp = /^((\d){2}(\-)(\d){2}(\-)(\d){2})$/gi
$(() => {
    $.mask.definitions['n'] = "[7-8]";
    $('input[name=phone]').mask("+n(999)999-99-99");
    $('input[name=code]').mask("99-99-99");
    window.messageModal = $('#message-modal');

    setTimeout(function () {
        // windowResize();
        removeLoader();
    },1000);

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

    // $(window).resize(function() {
    //     mainHeight();
    // });

    // $(window).scroll(function() {
    //     fixingMainMenu();
    // });

    // Fancybox init
    bindFancybox();

    // Open message modal
    if (openMessageModalFlag) messageModal.modal('show');
});

let getUrl = (form, url, callBack) => {
    let formData = new FormData();

    form.find('input.error').removeClass('error');
    form.find('div.error').html('');
    addLoader();
    form.find('input').each(function () {
        if ($(this).attr('type') === 'file') formData.append($(this).attr('name'), $(this)[0].files[0]);
        else formData.append($(this).attr('name'), $(this).val());
    });

    $.ajax({
        url: url ? url : form.attr('action'),
        data: formData,
        processData: false,
        contentType: false,
        type: form.attr('method'),
        success: (data) => {
            if (callBack) callBack(data);
            removeLoader();
        },
        error: (data) => {
            let response = jQuery.parseJSON(data.responseText);
            $.each(response.errors, (field, errorMsg) => {
                form.find('input[name='+field+']').addClass('error');
                form.find('.error.'+field).html(errorMsg[0]);
            });
            removeLoader();
        }
    });
}

let getCodeAgainCounter = (getCodeButton, timer) => {
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

// function fixingMainMenu() {
//     let mainMenuFix = $('#main-nav');
//     if ($(window).scrollTop() > 250) {
//         mainMenuFix.css({
//             'position':'fixed',
//             'top':0
//         });
//     } else {
//         mainMenuFix.css({
//             'position':'relative',
//             'top':'auto'
//         });
//     }
// }

// function windowResize() {
//     maxHeight($('.article-announcement'), 50);
//     maxHeight($('.action-list .action'), 30);
//     maxHeight($('#actions-brand-block table'), 0);
// }

// function mainHeight() {
//     let windowHeight = $(window).height(),
//         body = $('body'),
//         mainContainer = $('#main-container'),
//         mainContainerHeight = windowHeight - parseInt(mainContainer.css('padding-bottom')),
//         topLine = $('#top-line'),
//         h100 = $('.h100'),
//         h50 = $('.h50'),
//         gap = parseInt($('.col').css('padding-left')) * 2,
//         topLineHeight = topLine.height() + parseInt(topLine.css('padding-top')) + parseInt(topLine.css('padding-bottom')) + parseInt(topLine.css('margin-bottom'));
//
//     $(h50[0]).css('margin-bottom',gap);
// }
