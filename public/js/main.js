// window.stop();
$(window).on('load', function () {
    $('input[name=phone]').mask("+7(999)999-99-99");
    $('.styled').uniform();

    setTimeout(function () {
        // windowResize();
        removeLoader();
    },1000);

    $(window).resize(function() {
        mainHeight();
    });
    mainHeight();

    // $(window).scroll(function() {
    //     fixingMainMenu();
    // });

    // Fancybox init
    bindFancybox();
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

function mainHeight() {
    let windowHeight = $(window).height(),
        body = $('body'),
        mainContainer = $('#main-container'),
        mainContainerHeight = windowHeight - parseInt(mainContainer.css('padding-bottom')),
        topLine = $('#top-line'),
        h100 = $('.h100'),
        h50 = $('.h50'),
        gap = parseInt($('.col').css('padding-left')) * 2,
        topLineHeight = topLine.height() + parseInt(topLine.css('padding-top')) + parseInt(topLine.css('padding-bottom')) + parseInt(topLine.css('margin-bottom'));

    $(h50[0]).css('margin-bottom',gap);

    if ($(window).width() >= 768) {
        if ($(window).height() <= 700) {
            body.css('overflow-y','auto');
            mainContainer.css('height',mainContainerHeight * ($(window).height() >= 400 ? 2 : 5));
        } else {
            body.css('overflow-y','hidden');
            mainContainer.css('height',mainContainerHeight);
        }

        let workZoneHeight = mainContainer.height() - topLine.height() - parseInt(topLine.css('margin-bottom'));
        h100.css('height',workZoneHeight);
        h50.css('height',workZoneHeight/2 - gap / 2);
    } else {
        body.css('overflow-y','auto');
        mainContainer.css('height','auto');
        h100.css({'height':windowHeight/2, 'margin-bottom':gap});
        h50.css('height',windowHeight/2);
    }
}
