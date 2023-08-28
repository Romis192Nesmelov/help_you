// window.stop();
$(window).on('load', function () {
    $('input[name=phone]').mask("+7(999)999-99-99");

    setTimeout(function () {
        windowResize();
        $('body').removeAttr('style');
        removeLoader();
    },1000);

    $(window).resize(function() {
        windowResize();
    });

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

function windowResize() {
    mapHeight();
    // maxHeight($('.article-announcement'), 50);
    // maxHeight($('.action-list .action'), 30);
    // maxHeight($('#actions-brand-block table'), 0);
}

function mapHeight() {
    let windowHeight = $(window).height(),
        map = $('#map'),
        filters = $('#filters-block'),
        topLine = $('#top-line'),
        topLineHeight = topLine.height() + parseInt(topLine.css('padding-top')) + parseInt(topLine.css('padding-bottom')) + parseInt(topLine.css('margin-bottom'));

    if ($(window).width() >= 768) {
        map.css({'height':windowHeight - topLineHeight - 60,'margin-bottom':0});
    } else {
        if ($(window).height() <= 600) {
            map.css({'height':400,'margin-bottom':20});
        } else {
            let filtersHeight = filters.height() + parseInt(filters.css('padding-top')) + parseInt(filters.css('padding-bottom'));
            map.css({'height':windowHeight - (filtersHeight + topLineHeight + parseInt(map.css('margin-top'))) - 30,'margin-bottom':0});
        }
    }
}
