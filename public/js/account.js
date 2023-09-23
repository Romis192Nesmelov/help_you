$(() => {
    let bornDateInput = $('input[name=born]'),
        errorBorn = $('.error.born');

    bornDateInput.mask("9999-99-99", {
        completed: function() {
            let born = $(this).val().split('-'),
                currentDate = new Date(),
                inputDate = new Date(born[0], born[1], 0);

            if (
                born[1] > 12 ||
                born[0] >= currentDate.getFullYear() ||
                born[2] > inputDate.getDate() ||
                born[0] > currentDate.getFullYear() - 18 ||
                (born[0] == currentDate.getFullYear() - 18 && born[1] < currentDate.getMonth()) ||
                (born[0] == currentDate.getFullYear() - 18 && born[1] == currentDate.getMonth() && born[2] < currentDate.getDate())
            ) {
                $(this).addClass('error');
                errorBorn.html(errorBornMessage);
            } else {
                $(this).removeClass('error');
                errorBorn.html('');
            }
        }
    });

    // Click save
    $('#account-save').click((e) => {
        let errorFlag = false;
        $.each(['name','family','born','email','phone'], (k, field) => {
            if (!$('input[name='+field+']').val()) {
                errorFlag = true;
                return false;
            }
        });
        if (bornDateInput.hasClass('error')) errorFlag = true;
        if (errorFlag) e.preventDefault();
    });

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
