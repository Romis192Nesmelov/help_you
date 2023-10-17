$(document).ready(function () {
    const progressBarContainer = $('#progress-bar'),
        progressBar = progressBarContainer.find('.progress-bar'),
        form = $('form'),
        nextButton = $('#next'),
        backButton = $('#back'),
        performersInput = $('input[name=performers]'),
        performersInputError = $('.error.performers'),
        addressInput = $('input[name=address]'),
        addressInputError = $('.error.address'),
        completeModal = $('#complete-modal'),
        preValidation = [
            () => {
                let subTypesBlock = $('input[name=order_type]:checked').parents('.radio-group').find('.sub-types-block');
                if (subTypesBlock.length && !subTypesBlock.find('input[type=checkbox]:checked').length) {
                    subTypesBlock.find('input[type=checkbox]').addClass('error');
                    subTypesBlock.find('.error.subtype').last().html(errorSelectOneOfItems);
                    subTypesBlock.css('height',subTypesBlock.height() + 27);
                    return false;
                } else return true
            },
            () => {
                resetErrors(form);
                if (performersInput.val() <= 0 || performersInput.val() > 20) {
                    performersInput.addClass('error');
                    performersInputError.html(errorWrongValue);
                    return false;
                } else return true;
            },
            () => {
                resetErrors(form);
                if (addressInput.val() < 5) {
                    addressInput.addClass('error');
                    addressInputError.html(errorFieldMustBeFilledIn);
                    return false;
                } else if (addressInput.val() > 200) {
                    addressInput.addClass('error');
                    addressInputError.html(errorWrongValue);
                    return false;
                } else return true;
            },
            () => {
                return true;
            }
        ];

    ymaps.ready(mapInit);

    $('input[name=order_type]').change(function () {
        let thisRadioGroup = $(this).parents('.radio-group'),
            thisSubTypesBlocks = thisRadioGroup.find('.sub-types-block'),
            anotherSubTypesBlocks = $('.radio-group').not(thisRadioGroup).find('.sub-types-block');

        anotherSubTypesBlocks.css('display','block');
        anotherSubTypesBlocks.animate({
            'height':0,
            'padding-bottom':0
        },'slow');

        if (thisSubTypesBlocks.length) {
            thisSubTypesBlocks.css({
                'display':'table',
                'opacity':0
            });
            let heightBlock = thisSubTypesBlocks.height();
            thisSubTypesBlocks.css({
                'display':'block',
                'height':0,
                'opacity':1
            });

            let addHeight = thisSubTypesBlocks.find('.error.subtype').last().html().length ? 32 : 5
            thisSubTypesBlocks.animate({
                'height':heightBlock + addHeight
            },'slow');
        }
    });

    $('.sub-types-block input[type=checkbox]').change(function () {
        let subTypesBlock = $(this).parents('.sub-types-block');
        if (subTypesBlock.find('input[type=checkbox]:checked').length && subTypesBlock.find('input.error').length) {
            subTypesBlock.find('input.error').removeClass('error');
            subTypesBlock.find('.error.subtype').last().html('');
            subTypesBlock.css('height',subTypesBlock.height() - 20);
        }
    });

    backButton.click(() => {
        backButton.attr('disabled','disabled');
        $.get(backStepUrl, () => {
            nextPrevStep(true, () => {
                backButton.removeAttr('disabled');
                if (step === 1) {
                    progressBarContainer.addClass('d-none');
                    backButton.addClass('d-none');
                }
                step--;
                setProgressBar(progressBar);
            });
        });
    });

    nextButton.click((e) => {
        e.preventDefault();
        resetErrors(form);
        if (preValidation[step]()) {
            backButton.attr('disabled','disabled');
            getUrl(form, null, (data) => {
                step++;
                if (step) {
                    backButton.removeClass('d-none');
                    progressBarContainer.removeClass('d-none');
                }
                setProgressBar(progressBar);
                if (step !== 4) {
                    nextPrevStep(false, () => {
                        backButton.removeAttr('disabled');
                    });
                } else completeModal.modal('show');
            });
        }
    });

    performersInput.on('change', preValidation[1]).keyup(preValidation[1]);
    addressInput.on('change', preValidation[2]).keyup(preValidation[2]);
});

let nextPrevStep = (reverse, callBack) => {
    let stepFadeOut = reverse ? step + 1 : step,
        stepFadeIn = reverse ? step : step + 1,
        tags = ['head1','head2','inputs','image','description'],
        fadeOutSting = '',
        fadeInSting = '',
        callBackFlag = false;

    $.each(tags, (k, tag) => {
        let comma = (k + 1 !== tags.length ? ', ' : '');
        fadeOutSting += '#' + tag + '-step' + stepFadeOut + comma;
        fadeInSting += '#' + tag + '-step' + stepFadeIn + comma;
    });

    $(fadeOutSting).removeClass('d-block').fadeOut('slow',() => {
        $(fadeInSting).removeClass('d-none').fadeIn();
        if (callBack && !callBackFlag) {
            callBack();
            callBackFlag = true;
        }
    });
}

let setProgressBar = (progressBar) => {
    let progress = step * 25 + '%';
    progressBar.html(progress);
    progressBar.animate({
        'width': progress
    },'fast');
}

let mapInit = () => {
    let myMap = new ymaps.Map('image-step3', {
        center: [55.76, 37.64],
        zoom: 10
    }, {
        searchControlProvider: 'yandex#search'
    }),
    objectManager = new ymaps.ObjectManager({
        // Чтобы метки начали кластеризоваться, выставляем опцию.
        clusterize: false,
        // ObjectManager принимает те же опции, что и кластеризатор.
        gridSize: 32,
        clusterDisableClickZoom: true
    });
}
