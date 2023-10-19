// window.stop();
window.phoneRegExp = /^((\+)?(\d)(\s)?(\()?[0-9]{3}(\))?(\s)?([0-9]{3})(\-)?([0-9]{2})(\-)?([0-9]{2}))$/gi;
window.codeRegExp = /^((\d){2}(\-)(\d){2}(\-)(\d){2})$/gi
$(window).on('load', function () {
    $.mask.definitions['n'] = "[7-8]";
    $('input[name=phone]').mask("+n(999)999-99-99");
    $('input[name=code]').mask("99-99-99");
    window.messageModal = $('#message-modal');

    // Setting datatable defaults
    $.extend( $.fn.dataTable.defaults, {
        autoWidth: false,
        columnDefs: [{
            targets: [3]
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

    // Basic datatable
    let dataTable = $('.datatable-basic');
    if (dataTable.length) {
        dataTable = dataTable.DataTable({iDisplayLength: 8});

        // Change pagination on data-tables
        dataTable.on('draw.dt', function () {
            bindDelete();
            bindFancybox();
        });
        bindDelete();

        $(window).resize(function() {
            resizeDTable(dataTable);
        });

        // Click to delete items
        window.deleteId = null;
        window.deleteRow = null;

        // Click YES on delete modal
        $('.delete-yes').click(function () {
            let deleteModal = $(this).parents('.modal');
            deleteModal.modal('hide');
            addLoader();

            $.post(deleteModal.attr('del-function'), {
                '_token': $('input[name=_token]').val(),
                'id': window.deleteId,
            }, (data) => {
                if (data.success) {
                    let contentBlock = window.deleteRow.parents('.content-block'),
                        contentId = contentBlock.attr('id').replace('content-',''),
                        contentContainerCounter = $('#top-submenu-'+contentId).next('sup'),
                        contentCounter = parseInt(contentContainerCounter.html());

                    contentCounter--;
                    contentContainerCounter.html(contentCounter);
                    dataTable.row(window.deleteRow).remove();

                    if (!contentCounter) {
                        contentBlock.find('.no-data-block').removeClass('d-none');
                        window.deleteRow.parents('.dataTables_wrapper').remove();
                    } else dataTable.draw();
                    removeLoader();
                }
            });
        });
    }

    // Top menu
    let topMenu = $('.rounded-block.tall .top-submenu');
    topMenu.find('a').click(function () {
        let parent = $(this).parents('.tab');
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
});

let getUrl = (form, url, callBack) => {
    let formData = new FormData(),
        submitButton = form.find('button[type=submit]');

    form.find('input.error').removeClass('error');
    form.find('div.error').html('');
    form.find('input').each(function () {
        if ($(this).attr('type') === 'file') formData.append($(this).attr('name'), $(this)[0].files[0]);
        else if ($(this).attr('type') === 'radio') {
            $(this).each(function () {
                if ($(this).is(':checked')) formData.append($(this).attr('name'), $(this).val());
            });
        }
        else if ($(this).attr('type') === 'checkbox') {
            if ($(this).is(':checked')) formData.append($(this).attr('name'), $(this).val());
        }
        else formData.append($(this).attr('name'), $(this).val());
    });
    submitButton.attr('disabled','disabled');

    $.ajax({
        url: url ? url : form.attr('action'),
        data: formData,
        processData: false,
        contentType: false,
        type: form.attr('method'),
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

let validationDate = (date) => {
    let inputDate = new Date(date[2], date[1], 0);
    return date[0] && date[1] && date[2] && date[0] <= inputDate.getDate() && date[1] <= 12;
};

let lengthValidate = (form, fields) => {
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

let resetErrors = (form) => {
    form.find('input.error').removeClass('error');
    form.find('div.error').html('');
};

let resizeDTable = (dataTable) => {
    if ($(window).width() >= 991 && $(window).height() >= 800) dataTable.context[0]._iDisplayLength = 8;
    else if ($(window).width() >= 991) dataTable.context[0]._iDisplayLength = 6;
    else dataTable.context[0]._iDisplayLength = 10;
    dataTable.draw();
}

function bindDelete() {
    let deleteIcon = $('.icon-close2');
    deleteIcon.unbind();
    deleteIcon.click(function () {
        let deleteModal = $('#' + $(this).attr('modal-data')),
            inputId = deleteModal.find('input[name=id]');

        window.deleteId = $(this).attr('del-data');
        window.deleteRow = $(this).parents('tr');
        if (inputId.length) inputId.val(window.deleteId);
        deleteModal.modal('show');
    });
}
