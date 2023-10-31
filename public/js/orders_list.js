$(document).ready(function () {
    const modalConfirm = $('#order-closing-confirm-modal'),
        orderClosedModal = $('#order-closed-modal'),
        contentBlockActive = $('#content-active'),
        ordersActiveTable = contentBlockActive.find('table.datatable-basic.default');

    // Change pagination on data-tables
    ordersActiveTable.on('draw.dt', function () {
        bindClosingOrder(modalConfirm);
    });
    bindClosingOrder(modalConfirm);

    modalConfirm.find('button.close-yes').click(function (e) {
        e.preventDefault();
        modalConfirm.modal('hide');
        let contentCounterActive = $('#top-submenu-active').next('sup'),
            contentCounterActiveVal = parseInt(contentCounterActive.html()),
            activeTable = ordersActiveTable.DataTable(),

            contentBlockArchive = $('#content-archive'),
            archiveCounter = $('#top-submenu-archive').next('sup'),
            archiveCounterVal = parseInt(archiveCounter.html()),
            archiveTable = contentBlockArchive.find('table.datatable-basic.default'),
            deleteCell = window.tableRow.find('.close-order-cell');

        $.post(
            closeOrderUrl,
            {
                '_token': window.tokenField,
                'id': orderId,
            }
        ).done(() => {
            contentCounterActiveVal--;
            archiveCounterVal++;
            contentCounterActive.html(contentCounterActiveVal);
            archiveCounter.html(archiveCounterVal);

            window.tableRow.find('.label').removeClass('in-progress').addClass('closed').html(archiveLabelText);
            deleteCell.removeClass('close-order-cell').addClass('empty');
            deleteCell.find('button').remove();
            // deleteCell.append(
            //     $('<i></i>').addClass('icon-close2').attr('del-data','delete-modal').attr('del-data',window.orderId)
            // );

            activeTable.row(window.tableRow).remove();
            activeTable.draw();
            if (!activeTable.rows().count()) {
                activeTable.destroy();
                $('.datatable-basic').remove();
                contentBlockActive.find('h4').removeClass('d-none');

                if (!archiveTable.length) {
                    contentBlockArchive.find('h4').addClass('d-none');
                    let newTable = $('<table></table>').addClass('table datatable-basic default');
                    contentBlockArchive.prepend(newTable);
                    archiveTable = dataTableAttributes(newTable, 8);
                } else {
                    archiveTable = archiveTable.DataTable();
                }
                archiveTable.row.add(window.tableRow);
                archiveTable.draw();

                orderClosedModal.modal('show');
            }
        });
    });
});

const bindClosingOrder = (modalConfirm) => {
    let buttons = $('.close-order');
    buttons.unbind();
    buttons.click(function () {
        window.tableRow = $(this).parents('tr');
        window.orderId = parseInt(window.tableRow.attr('id').replace('row-',''));
        modalConfirm.modal('show');
    });
}
