const maxHeight = (objs, padBottom) => {
    if ($(window).width() > 650) {
        var maxHeight = 0;
        objs.each(function() {
            if (maxHeight < $(this).height()) maxHeight = $(this).height();
        });
    } else {
        maxHeight = 'auto';
    }
    if (padBottom) maxHeight += padBottom;
    objs.css('height',maxHeight);
}
