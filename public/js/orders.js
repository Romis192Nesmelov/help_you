$(document).ready(function () {
    ymaps.ready(mapInitWithContainer);
    window.orderModal = $('#order-modal');
    window.selectedPoint = null;
    window.tokenField = $('input[name=_token]').val(),
    window.showDefButton = $('#show-default');
    window.respondButton = $('#respond-button');
    window.subscribeButton = $('#subscribe-button');

    $('#apply-button').click((e) => {
        e.preventDefault();
        window.myMap.geoObjects.removeAll();
        getPoints();
    });

    window.showDefButton.click((e) => {
        e.preventDefault();
        // console.log($(this).attr('id'));
        $('form').attr('action',getOrdersUrl);
        window.myMap.geoObjects.removeAll();
        getPoints();
        window.showDefButton.remove();
    });

    window.respondButton.click((e) => {
        e.preventDefault();
        window.orderModal.modal('hide');
        $('#order-respond-modal').modal('show');
        let properties = window.selectedPoint.properties;
        $.post(
            orderResponseUrl,
            {
                '_token': window.tokenField,
                'order_id': properties.get('orderId'),
            }
        ).done(() => {
            window.clusterer.remove(window.selectedPoint);
        });
    });

    window.subscribeButton.click((e) => {
        e.preventDefault();
        let userId = window.selectedPoint.properties.get('user').id;
        $.get(
            subscribeUrl,
            {'user_id': userId}
        ).done((data) => {
            revertSubscribeButton(data.revert);
            window.subscriptions.push(userId);
        });
    });
});

let mapInitWithContainer = () => {
    mapInit('map');
    getPoints();
}

let getPoints = () => {
    getUrl($('form'), null, (data) => {
        window.placemarks = [];
        let orders = data.orders;
        window.subscriptions = [];
        window.unreadOrders = [];
        if (data.subscriptions.length) {
            $.each(data.subscriptions, function (k,subscription) {
                window.subscriptions.push(subscription.user_id);
                if (subscription.orders.length) {
                    $.each(subscription.orders, function (k,order) {
                        window.unreadOrders.push(order.id);
                    });
                }
            });
        }
        if (orders.length) {
            $.each(orders, function (k,point) {
                if (point.need_performers > point.performers.length) {
                    let createdAt = new Date(point.created_at);
                    window.placemarks.push(getPlaceMark([point.latitude, point.longitude], {
                        placemarkId: k,
                        balloonContentHeader: '<a data-id="' + k + '" class="list-item">' + point.order_type.name + '</a>',
                        balloonContentBody: point.address,
                        orderId: point.id,
                        orderType: point.order_type.name,
                        orderSubTypes: point.order_type.subtypes,
                        subtypes: point.subtypes,
                        need_performers: point.need_performers,
                        performers: point.performers.length,
                        user: point.user,
                        date: createdAt.toLocaleDateString('ru-RU'),
                        description: point.description
                    }));

                    if (openOrderId && openOrderId === point.id) {
                        openOrderModal(window.placemarks[k]);
                    }
                }
            });

            // Создаем собственный макет с информацией о выбранном геообъекте.
            let customBalloonContentLayout = ymaps.templateLayoutFactory.createClass([
                '<ul class=list>',
                // Выводим в цикле список всех геообъектов.
                '{% for geoObject in properties.geoObjects %}',
                '<li><div class="balloon-head">{{ geoObject.properties.balloonContentHeader|raw }}</div><div class="balloon-content">{{ geoObject.properties.balloonContentBody|raw }}</div></li>',
                '{% endfor %}',
                '</ul>'
            ].join(''));

            window.clusterer = new ymaps.Clusterer({
                preset: 'islands#invertedRedClusterIcons',
                clusterDisableClickZoom: true,
                clusterOpenBalloonOnClick: true,
                // Устанавливаем режим открытия балуна.
                // В данном примере балун никогда не будет открываться в режиме панели.
                clusterBalloonPanelMaxMapArea: 0,
                // По умолчанию опции балуна balloonMaxWidth и balloonMaxHeight не установлены для кластеризатора,
                // так как все стандартные макеты имеют определенные размеры.
                clusterBalloonMaxHeight: 200,
                // Устанавливаем собственный макет контента балуна.
                clusterBalloonContentLayout: customBalloonContentLayout,
            });
            addPointsToMap();
        }
    });
}

let addPointsToMap = () => {
    window.clusterer.add(window.placemarks);
    window.myMap.geoObjects.add(window.clusterer);
    bindOrderClick();
}

let bindOrderClick = () => {
    $(document).on('click', 'a.list-item', function() {
        openOrderModal(window.placemarks[$(this).data().id]);
    });
}

let openOrderModal = (point) => {
    let orderSuBTypeList = orderModal.find('ul'),
        properties = point.properties,
        orderId = properties.get('orderId'),
        user = properties.get('user'),
        description = properties.get('description'),
        orderSubTypes = properties.get('orderSubTypes'),
        currentSubTypes = properties.get('subtypes');

    window.selectedPoint = point;

    orderSuBTypeList.html('');

    if (orderSubTypes && currentSubTypes) {
        for (let i=0; i < orderSubTypes.length; i++) {
            for (let ic=0; ic < currentSubTypes.length; ic++) {
                if (orderSubTypes[i].id === currentSubTypes[ic]) {
                    orderSuBTypeList.append($('<li></li>').html(orderSubTypes[i].name));
                    break;
                }
            }
        }
    }
    $('.order-number').html(orderId);
    $('.order-date').html(properties.get('date'));
    if (user.avatar) $('.avatar.cir').css('background-image','url("/' + user.avatar + '")');
    $('.user-name').html(user.name);
    $('.born-date').html(user.born);
    $('.order-type').html(properties.get('orderType'));
    $('.order-address').html(properties.get('balloonContentBody'));
    $('#need-performers').html(properties.get('need_performers'));
    $('#ready-to-help').html(properties.get('performers'));
    $('#order-description').html(description ? description : absentDescr);

    if (userId === user.id) {
        window.respondButton.attr('disabled','disabled');
        window.subscribeButton.hide();
    } else {
        window.respondButton.removeAttr('disabled');
        window.subscribeButton.show();
    }

    // If user.id exist in subscriptions list
    changeSubscribeButton(window.subscriptions.includes(user.id));

    // If order.id exist in unread list
    let indexOrderId = window.unreadOrders.indexOf(orderId);
    if (indexOrderId !== -1) {
        $.get(
            orderReadOrderUrl,
            {'order_id': orderId}
        ).done(() => {
            window.unreadOrders.splice(indexOrderId, 1);
        });
    }

    window.orderModal.modal('show');
}

let revertSubscribeButton = (revert) => {
    window.subscribeButton.fadeOut(() => {
        changeSubscribeButton(revert);
        window.subscribeButton.fadeIn();
    });
}

let changeSubscribeButton = (revert) => {
    let icon, buttonName, removeClass, addClass;
    if (revert) {
        icon = 'icon-bell-cross';
        buttonName = unsubscribe;
        removeClass = 'btn-primary';
        addClass = 'btn-gray';
    } else {
        icon = 'icon-bell-check';
        buttonName = subscribe;
        removeClass = 'btn-gray';
        addClass = 'btn-primary';
    }
    window.subscribeButton.find('i').prop('className', icon);
    window.subscribeButton.find('span').html(buttonName);
    window.subscribeButton.removeClass(removeClass).addClass(addClass);
}
