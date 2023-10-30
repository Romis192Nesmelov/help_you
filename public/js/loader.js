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
