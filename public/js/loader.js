function addLoader() {
    $('body').prepend(
        $('<div></div>').attr('id','loader').append($('<div></div>'))
    ).css({
        'overflow-y':'hidden',
        'padding-right':20
    });
}

function removeLoader() {
    $('#loader').remove();
    $('body').css('overflow-y','auto');
}
