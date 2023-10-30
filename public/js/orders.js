$(document).ready(function () {
    ymaps.ready(mapInitWithContainer);
    window.selectedPoints = $('#selected-points');
    window.selectedPoint = null;
    window.tokenField = $('input[name=_token]').val();
    window.respondButton = $('#respond-button');
    window.pointsContainer = $('#points-container');
    window.selectedPointsOpened = false;

    $('#apply-button').click((e) => {
        e.preventDefault();
        window.myMap.geoObjects.removeAll();
        getPoints();
    });

    $('#selected-points i.icon-close2, #map').click(() => {
        if (window.selectedPointsOpened) removeSelectedPoints();
    });
});

const mapInitWithContainer = () => {
    mapInit('map');
    getPoints();
}

const getPoints = () => {
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
                let createdAt = new Date(point.created_at);
                window.placemarks.push(getPlaceMark([point.latitude, point.longitude], {
                    placemarkId: k,
                    // balloonContentHeader: '<a data-id="' + k + '" class="list-item">' + point.order_type.name + '</a>',
                    // balloonContentBody: point.address,
                    orderId: point.id,
                    name: point.order_type.name,
                    address: point.address,
                    orderType: point.order_type.name,
                    images: point.images,
                    orderSubTypes: point.order_type.subtypes,
                    subtypes: point.subtypes,
                    need_performers: point.need_performers,
                    performers: point.performers.length,
                    user: point.user,
                    date: createdAt.toLocaleDateString('ru-RU'),
                    description: point.description
                }));

                if (window.openOrderId && window.openOrderId === point.id) {
                    window.openOrderId = null;
                    showOrder(window.placemarks[k]);
                }
            });

            // Создаем собственный макет с информацией о выбранном геообъекте.
            // let customBalloonContentLayout = ymaps.templateLayoutFactory.createClass([
            //     '<ul class=list>',
            //     // Выводим в цикле список всех геообъектов.
            //     '{% for geoObject in properties.geoObjects %}',
            //     '<li><div class="balloon-head">{{ geoObject.properties.balloonContentHeader|raw }}</div><div class="balloon-content">{{ geoObject.properties.balloonContentBody|raw }}</div></li>',
            //     '{% endfor %}',
            //     '</ul>'
            // ].join(''));

            window.clusterer = new ymaps.Clusterer({
                preset: 'islands#darkOrangeClusterIcons',
                clusterDisableClickZoom: true,
                clusterOpenBalloonOnClick: false,
                // Устанавливаем режим открытия балуна.
                // В данном примере балун никогда не будет открываться в режиме панели.
                clusterBalloonPanelMaxMapArea: 0,
                // По умолчанию опции балуна balloonMaxWidth и balloonMaxHeight не установлены для кластеризатора,
                // так как все стандартные макеты имеют определенные размеры.
                clusterBalloonMaxHeight: 200,
                // Устанавливаем собственный макет контента балуна.
                // clusterBalloonContentLayout: customBalloonContentLayout,
            });

            // Click on cluster
            // window.clusterer.events.add('click', function (e) {
            //     $.each(e.get('target').properties._data.geoObjects, function (k, object) {
            //         console.log(object.properties.get('user'));
            //     });
            // });

            window.myMap.geoObjects.events.add('click', function (e) {
                var objects = e.get('target').properties.get('geoObjects');
                if (objects) {
                    if (window.selectedPointsOpened) {
                        removeSelectedPoints(() => { clickedToCluster(objects); });
                    } else clickedToCluster(objects);
                } else {
                    var point = e.get('target');
                    if (window.selectedPointsOpened) {
                        removeSelectedPoints(() => { clickedToPoint(point); });
                    } else clickedToPoint(point);
                }
            });
            addPointsToMap();
        }
    });
}

const addPointsToMap = () => {
    window.clusterer.add(window.placemarks);
    window.myMap.geoObjects.add(window.clusterer);
}

const showOrder = (point) => {
    let properties = point.properties,
        orderSubTypes = properties.get('orderSubTypes'),
        currentSubTypes = properties.get('subtypes'),
        user = properties.get('user'),
        avatar = user.avatar ? user.avatar : '/images/def_avatar.svg',
        images = properties.get('images'),
        description = properties.get('description'),
        orderContainer = $('<div></div>').addClass('order-block mb-3').attr('id','order-'+properties.get('placemarkId'));

    // Check subscriptions
    if (window.subscriptions.includes(user.id)) {
        var subscribeButtonClass = 'btn-gray',
            subscribeBellClass = 'icon-bell-cross',
            subscribeButtonText = toUnsubscribe;
    } else {
        subscribeButtonClass = 'btn-primary';
        subscribeBellClass = 'icon-bell-check';
        subscribeButtonText = toSubscribe;
    }

    orderContainer
        .append(
            $('<h6></h6>').addClass('text-center').html(orderNumber + properties.get('orderId') + fromText+properties.get('date'))
        ).append(
            $('<div></div>').addClass('w-100 d-flex align-items-center justify-content-between')
                .append(
                    $('<div></div>').addClass('d-flex align-items-center justify-content-center')
                        .append(
                            $('<div></div>').addClass('avatar cir').css('background-image','url('+avatar+')')
                        ).append(
                            $('<div></div>')
                                .append(
                                    $('<div></div>').addClass('user-name').html(user.family+' '+user.name)
                                ).append(
                                    $('<div></div>').addClass('born').html(user.born)
                                )
                        )
                )
                .append(
                    $('<button></button>').addClass('subscribe-button btn small mt-0').addClass(subscribeButtonClass)
                        .append(
                            $('<i></i>').addClass(subscribeBellClass)
                        ).append(
                            $('<br/>')
                        ).append(
                            $('<span></span>').html(subscribeButtonText)
                        )
                )
        );

    if (images.length) {
        let imagesContainer = $('<div></div>').addClass('images owl-carousel mt-3');
        $.each(images, function (k, image) {
            imagesContainer.append(
                $('<div></div>').addClass('image').css('background-image','url(/'+image.image+')')
            );
        });
        orderContainer.append(imagesContainer);
        enablePointImagesCarousel(imagesContainer,images.length > 1);
    }

    orderContainer.append($('<h2></h2>').addClass('order-type text-center mt-3 mb-1').html(properties.get('orderType')));

    if (orderSubTypes && currentSubTypes) {
        let subTypesContainer = $('<ul></ul>').addClass('subtypes');
        for (let i=0; i < orderSubTypes.length; i++) {
            for (let ic=0; ic < currentSubTypes.length; ic++) {
                if (orderSubTypes[i].id === currentSubTypes[ic]) {
                    subTypesContainer.append($('<li></li>').html(orderSubTypes[i].name));
                    break;
                }
            }
        }
        orderContainer.append(subTypesContainer);
    }

    orderContainer.append($('<p></p>').addClass('mb-1 text-center').html(address+': ' + properties.get('address')));

    if (description) {
        orderContainer
            .append(
                $('<p></p>').addClass('small text-center fst-italic mt-2 mb-0').html(descriptionText)
            ).append(
                $('<p></p>').addClass('text-center order-description fst-italic mb-1').html(description)
            );
    }

    orderContainer
        .append(
            $('<p></p>').addClass('small text-center fst-italic mb-2').html(
                needPerformers + ' ' + properties.get('need_performers') + ' ' + readyToHelp + properties.get('performers')
            )
        );

    if (userId !== user.id) {
        orderContainer.append($('<button></button>').addClass('respond-button btn btn-primary w-100').attr('type','button').append($('<span></span>').html(respondToAnOrder)));
    }

    orderContainer.append($('<hr>'));
    window.pointsContainer.append(orderContainer);
}

const removeSelectedPoints = (callBack) => {
    window.selectedPoints.animate({'margin-left': -1 * (window.selectedPoints.width() + 40)}, 'slow', function () {
        window.selectedPointsOpened = false;
        if (callBack) callBack();
    });
}

const clickedToCluster = (objects) => {
    purgePointsContainer();
    $.each(objects, function (k, object) {
        showOrder(object);
    });
    setBindsAndOpen();
}

const clickedToPoint = (point) => {
    purgePointsContainer();
    showOrder(point);
    setBindsAndOpen();
}

const purgePointsContainer = () => {
    window.pointsContainer.removeAttr('class').html('');
}

const setBindsAndOpen = () => {
    // Set custom scroll bar
    window.selectedPoints.mCustomScrollbar({
        axis: 'y',
        theme: 'light-3',
        alwaysShowScrollbar: 0
    });

    // Bind click respond button
    $('.respond-button').click(function (e) {
        e.preventDefault();
        let point = getPlaceMarkOnMap($(this)),
            properties = point.properties,
            orderRespondModal = $('#order-respond-modal');

        $.post(
            orderResponseUrl,
            {
                '_token': window.tokenField,
                'order_id': properties.get('orderId'),
            }
        ).done(() => {
            orderRespondModal.find('.order-type').html(properties.get('orderType'));
            orderRespondModal.find('.order-address').html(properties.get('address'));
            orderRespondModal.modal('show');
            window.clusterer.remove(point);
        });
    });

    // Bind subscribe button
    $('.subscribe-button').click(function (e) {
        e.preventDefault();
        let button = $(this),
            point = getPlaceMarkOnMap(button),
            userId = point.properties.get('user').id;

        $.get(
            subscribeUrl,
            {'user_id': userId}
        ).done((data) => {
            button.fadeOut(() => {
                button.toggleClass('btn-gray',data.subscription).toggleClass('btn-primary',!data.subscription);
                button.find('i').toggleClass('icon-bell-cross',data.subscription).toggleClass('icon-bell-check',!data.subscription);
                button.find('span').html(data.subscription ? toUnsubscribe : toSubscribe);
                button.fadeIn();
            });

            window.subscriptions.push(userId);
        });
    });

    // Open selected points
    window.selectedPoints.animate({'margin-left': 0}, 'slow', function () {
        window.selectedPointsOpened = true;
    });

}

const enablePointImagesCarousel = (container, autoplay) => {
    container.owlCarousel(owlSettings(
        10,
        false,
        6000,
        {0: {items: 1}},
        autoplay
    ));
}

const getPlaceMarkOnMap = (obj) => {
    let placemarkId = parseInt((obj).parents('.order-block').attr('id').replace('order-',''));
    return window.placemarks[placemarkId];
}