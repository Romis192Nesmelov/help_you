// window.stop();
$(() => {
    $('input[name=phone]').mask("+9(999)999-99-99");
    $('input[name=code]').mask("99-99-99");
    $('input[name=born]').mask("1999-99-99");

    setTimeout(function () {
        // windowResize();
        removeLoader();
    },1000);

    $('#main-nav .navbar-toggler').click(function () {
        let blackBg = $('<div></div>').attr('id','black-background');
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

    // $(window).resize(function() {
    //     mainHeight();
    // });

    // $(window).scroll(function() {
    //     fixingMainMenu();
    // });

    // Fancybox init
    bindFancybox();

    // Preview avatar
    let avatar = $('#avatar-block .avatar'),
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

    // Open message modal
    if (openMessageModalFlag) $('#message-modal').modal('show');
});

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
