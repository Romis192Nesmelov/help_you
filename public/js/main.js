// window.stop();
window.phoneRegExp = /^((\+)?(\d)(\s)?(\()?[0-9]{3}(\))?(\s)?([0-9]{3})(\-)?([0-9]{2})(\-)?([0-9]{2}))$/gi;
window.codeRegExp = /^((\d){2}(\-)(\d){2}(\-)(\d){2})$/gi

$(document).ready(function () {
    $.mask.definitions['n'] = "[7-8]";
    $('input[name=phone]').mask("+n(999)999-99-99");
    $('input[name=code]').mask("99-99-99");
    window.messageModal = $('#message-modal');
    window.tokenField = $('input[name=_token]').val();

    // Setting datatable defaults
    $.extend( $.fn.dataTable.defaults, {
        autoWidth: false,
        columnDefs: [{
            targets: [4]
        }],
        order: [],
        dom: '<"datatable-header"fl><"datatable-scroll"t><"datatable-footer"ip>',
        bLengthChange: false,
        info: false,
        language: {
            search: '<span>Фильтр:</span> _INPUT_',
            lengthMenu: '<span>Показывать:</span> _MENU_',
            paginate: { 'next': '&rarr;', 'previous': '&larr;' },
            thousands:      ',',
            loadingRecords: 'Загрузка...',
            zeroRecords:    'Нет данных',
        },
        drawCallback: function () {
            $(this).find('tbody tr').slice(-3).find('.dropdown, .btn-group').addClass('dropup');
        },
        preDrawCallback: function() {
            $(this).find('tbody tr').slice(-3).find('.dropdown, .btn-group').removeClass('dropup');
        }
    });

    setTimeout(function () {
        removeLoader();
    },700);

    // Datatable
    const baseDataTable = dataTableAttributes($('.datatable-basic.default'), 8);
    const subscrDataTable = dataTableAttributes($('.datatable-basic.subscriptions'), 6);
    if (baseDataTable) clickYesDeleteOnModal(baseDataTable, true);
    if (subscrDataTable) clickYesDeleteOnModal(subscrDataTable, false);

    // Click to delete items
    window.deleteId = null;
    window.deleteRow = null;

    // Top menu tabs
    const topMenu = $('.rounded-block.tall .top-submenu');
    topMenu.find('a').click(function (e) {
        e.preventDefault();
        const parent = $(this).parents('.tab');
        if (!parent.hasClass('active')) {
            let currentActiveTab = topMenu.find('.tab.active'),
                currentActiveTabId = currentActiveTab.find('a').attr('id').replace('top-submenu-',''),
                currentContent = $('#content-'+currentActiveTabId),
                newActiveIdTabId = $(this).attr('id').replace('top-submenu-',''),
                newContent = $('#content-'+newActiveIdTabId);

            currentActiveTab.removeClass('active');
            parent.addClass('active');
            currentContent.fadeOut(() => {
                newContent.css('display','none').fadeIn();
            });
        }
    });

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

    // Fancybox init
    bindFancybox();

    // Open message modal
    if (openMessageModalFlag) messageModal.modal('show');

    // Custom scroll dropdown menu
    $('.dropdown-menu').mCustomScrollbar({
        axis: 'y',
        theme: 'light-3',
        alwaysShowScrollbar: 1
    });
});

const getUrl = (form, url, callBack) => {
    let formData = new FormData(),
        submitButton = form.find('button[type=submit]');

    form.find('input.error').removeClass('error');
    form.find('div.error').html('');
    form.find('input, select, textarea').each(function () {
        if ($(this).attr('type') === 'file') formData.append($(this).attr('name'), $(this)[0].files[0]);
        else if ($(this).attr('type') === 'radio') {
            $(this).each(function () {
                if ($(this).is(':checked')) formData.append($(this).attr('name'), $(this).val());
            });
        }
        else if ($(this).attr('type') === 'checkbox') {
            if ($(this).is(':checked')) formData.append($(this).attr('name'), $(this).val());
        }
        else {
            // console.log($(this).attr('name') + '==' + $(this).val());
            formData.append($(this).attr('name'), $(this).val());
        }
    });
    submitButton.attr('disabled','disabled');

    $.ajax({
        url: url ? url : form.attr('action'),
        data: formData,
        processData: false,
        contentType: false,
        type: form.attr('method'),
        cache: false,
        success: (data) => {
            if (callBack) callBack(data);
            submitButton.removeAttr('disabled');
        },
        error: (data) => {
            let response = jQuery.parseJSON(data.responseText),
                replaceErr = {
                    'phone':'телефон',
                    'email':'E-mail',
                    'user_name':'имя'
                };

            $.each(response.errors, function (field, error) {
                let errorMsg = error[0];
                $.each(replaceErr, function (src,replace) {
                    errorMsg = error[0].replace(src,replace);
                });
                form.find('input[name='+field+']').addClass('error');
                form.find('.error.'+field).html(errorMsg);
            });
            submitButton.removeAttr('disabled');
        }
    });
}

const getCodeAgainCounter = (getCodeButton, timer) => {
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

const validationDate = (date) => {
    let inputDate = new Date(date[2], date[1], 0);
    return date[0] && date[1] && date[2] && date[0] <= inputDate.getDate() && date[1] <= 12;
};

const lengthValidate = (form, fields) => {
    let validationFlag = true;
    $.each(fields, (k, field) => {
        let input = form.find('input[name=' + field + ']');
        if (!input.val().length) {
            input.addClass('error');
            form.find('.error.' + field).html(errorFieldMustBeFilledIn);
            validationFlag = false;
        }
    });
    return validationFlag;
};

const resetErrors = (form) => {
    form.find('input.error').removeClass('error');
    form.find('div.error').html('');
};

const resizeDTable = (dataTable, rows) => {
    if ($(window).width() >= 991 && $(window).height() >= 800) dataTable.context[0]._iDisplayLength = rows;
    else if ($(window).width() >= 991) dataTable.context[0]._iDisplayLength = rows - 2;
    else dataTable.context[0]._iDisplayLength = rows + 2;
    dataTable.draw();
    bindDelete();
}

const bindDelete = () => {
    let deleteIcon = $('.icon-close2, .icon-bell-cross');
    deleteIcon.unbind();
    deleteIcon.click(function () {
        window.deleteId = $(this).attr('del-data');
        let deleteModal = $('#'+$(this).attr('modal-data')),
            inputId = deleteModal.find('input[name=id]'),
            possibleParentRow = $('#row-'+window.deleteId),
            altParentRow = $('.row-'+window.deleteId);

        window.deleteRow = possibleParentRow.length ? possibleParentRow : altParentRow;

        if (inputId.length) inputId.val(window.deleteId);
        deleteModal.modal('show');
    });
}

const mapInit = (container) => {
    window.myMap = new ymaps.Map(container, {
        center: [55.76, 37.64],
        zoom: 10,
        controls: []
    });
}

const getPlaceMark = (point, data) => {
    return new ymaps.Placemark(point, data, {
        preset: 'islands#darkOrangeCircleDotIcon'
    });
}

const zoomAndCenterMap = () => {
    window.myMap.setCenter(point, 17);
}

const dataTableAttributes = (dataTable, rows) => {
    if (dataTable.length) {
        dataTable = dataTable.DataTable({iDisplayLength: rows});

        // Change pagination on data-tables
        dataTable.on('draw.dt', function () {
            bindDelete();
        });
        bindDelete();

        $(window).resize(function () {
            resizeDTable(dataTable, rows);
        });
        return dataTable
    } else return false;
}

const clickYesDeleteOnModal = (dataTable, useCounter) => {
    // Click YES on delete modal
    $('.delete-yes').click(function () {
        let deleteModal = $(this).parents('.modal');
        deleteModal.modal('hide');
        addLoader();

        $.post(deleteModal.attr('del-function'), {
            '_token': window.tokenField,
            'id': window.deleteId,
        }, () => {
            deleteDataTableRows(dataTable, window.deleteRow, useCounter);
            removeLoader();
        });
    });
}

const deleteDataTableRows = (dataTable, row, useCounter) => {
    let baseTable = row.parents('.datatable-basic.default'),
        contentBlockTab = row.parents('.content-block');

    if (useCounter) changeDataCounter(contentBlockTab, -1);

    if (row.length > 1) {
        row.each(function () {
            dataTable.row($(this)).remove();
        });
    } else dataTable.row(row).remove();
    dataTable.draw();

    if (!dataTable.rows().count()) {
        dataTable.destroy();
        baseTable.remove();
        if (contentBlockTab) contentBlockTab.find('h4').removeClass('d-none');
    }
    bindDelete();
}

const addDataTableRow = (contentBlockTab, row, useCounter) => {
    if (useCounter) changeDataCounter(contentBlockTab, 1);
    let dataTable = contentBlockTab.find('table.datatable-basic.default');

    if (!dataTable.length) {
        contentBlockTab.find('h4').addClass('d-none');
        let newTable = $('<table></table>').addClass('table datatable-basic default');
        contentBlockTab.prepend(newTable);
        dataTable = dataTableAttributes(newTable, 8);
    } else {
        dataTable = dataTable.DataTable();
    }

    if (row.length > 1) {
        row.each(function () {
            dataTable.row.add($(this));
        });
    } else dataTable.row.add(row);
    dataTable.draw();
}

const changeDataCounter = (contentBlockTab, increment) => {
    let contentId = contentBlockTab.attr('id').replace('content-',''),
        containerCounter = $('#top-submenu-'+contentId).next('sup'),
        counterVal = parseInt(containerCounter.html());

    counterVal += increment;
    containerCounter.html(counterVal);
}

const getSubscriptionsNews = (subscriptionsUrl, ordersUrl, newOrderFrom) => {
    $.get(subscriptionsUrl).done((data) => {
        if (data.subscriptions.length) {
            $('#right-button-block .fa.fa-bell-o').append(
                $('<span></span>').addClass('dot')
            );
            $.each(data.subscriptions, function (k,subscription) {
                $.each(subscription.orders, function (k,order) {
                    $('#dropdown').append(
                        $('<li></li>').attr('id','unread-order-' + order.id).append(
                            $('<div></div>')
                                .append(
                                    $('<a></a>').attr('href', ordersUrl+'/?id='+order.id).html(newOrderFrom + '<br>')
                                ).append(
                                $('<span></span>').html(order.user.name + ' ' + order.user.family)
                            )
                        ).append('<hr>')
                    );
                });
            });
        }
    });
}

const imagePreview = (container, defImage) => {
    container.each(function () {
        let currentContainer = $(this),
            hoverImg = currentContainer.find('img');

        currentContainer.find('input[type=file]').change(function () {
            let input = $(this)[0].files[0];
            if (input.type.match('image.*')) {
                let reader = new FileReader();
                reader.onload = function (e) {
                    currentContainer.css('background-image', 'url(' + e.target.result + ')');
                };
                reader.readAsDataURL(input);
                currentContainer.find('i').hide();
            } else if (defImage) {
                currentContainer.css('background-image', 'url('+defImage+')');
            }
        }).on('mouseover', function () {
            hoverImg.show();
        }).on('mouseout', function () {
            hoverImg.hide();
        });
    });
}

const owlSettings = (margin, nav, timeout, responsive, autoplay) => {
    let navButtonBlack1 = '<img src="/images/arrow_left.svg" />',
        navButtonBlack2 = '<img src="/images/arrow_right.svg" />';

    return {
        margin: margin,
        loop: true,
        nav: nav,
        autoplay: autoplay,
        autoplayTimeout: timeout,
        dots: !nav,
        responsive: responsive,
        navText: [navButtonBlack1, navButtonBlack2]
    }
}
