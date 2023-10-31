$(document).ready(function () {
    const modalConfirm = $('#order-closing-confirm-modal'),
        orderClosedModal = $('#order-closed-modal'),
        ordersActiveTable = $('#content-active').find('table.datatable-basic.default');

    // Change pagination on data-tables
    ordersActiveTable.on('draw.dt', function () {
        bindClosingOrder(modalConfirm);
    });
    bindClosingOrder(modalConfirm);

    modalConfirm.find('button.close-yes').click(function (e) {
        e.preventDefault();
        modalConfirm.modal('hide');
        let deleteCell = window.tableRow.find('.close-order-cell');

        $.post(
            closeOrderUrl,
            {
                '_token': window.tokenField,
                'id': orderId,
            }
        ).done(() => {
            window.tableRow.find('.label').removeClass('in-progress').addClass('closed').html(archiveLabelText);
            deleteCell.removeClass('close-order-cell').addClass('empty');
            deleteCell.find('button').remove();

            deleteDataTableRows($('#content-active'), window.tableRow, true);
            addDataTableRow($('#content-archive'), window.tableRow, true);
            orderClosedModal.modal('show');
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
