$(document).ready(function () {
    ymaps.ready(mapInitWithContainer);
    window.selectedPoints = $('#selected-points');
    window.selectedPoint = null;
    window.respondButton = $('#respond-button');
    window.pointsContainer = $('#points-container');
    window.selectedPointsOpened = false;
    window.cickedTarget = null;

    $('#apply-button').click((e) => {
        e.preventDefault();
        window.myMap.geoObjects.removeAll();
        getPoints();
    });

    $('#selected-points i.icon-close2').click(() => {
        if (window.selectedPointsOpened) {
            removeSelectedPoints();
            // window.myMap.balloon.close();
        }
    });
});

const mapInitWithContainer = () => {
    mapInit('map');
    getPoints();
}

const getPoints = () => {
    getUrl($('form'), (getPreviewFlag ? getPreviewUrl : null), (data) => {
        getPreviewFlag = false;
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
                    // balloonContentHeader: point.order_type.name,
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
                    description_short: point.description_short,
                    description_full: point.description_full
                }));

                if (window.openOrderId && window.openOrderId === point.id) {
                    window.openOrderId = null;
                    window.cickedTarget = window.placemarks[k];
                    window.placemarks[k].options.set('iconColor', '#bc202e');
                    showOrder(window.placemarks[k]);
                    setBindsAndOpen();
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
                var target = e.get('target');

                target.options.set('iconColor', '#bc202e');
                if (target.properties.get('geoObjects')) {
                    if (window.selectedPointsOpened) {
                        removeSelectedPoints(target,() => { clickedToCluster(target);});
                    } else clickedToCluster(target);
                } else {
                    if (window.selectedPointsOpened) {
                        removeSelectedPoints(target,() => { clickedToPoint(target);});
                    } else clickedToPoint(target);
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
        descriptionShort = properties.get('description_short'),
        descriptionFull = properties.get('description_full'),
        orderContainer = $('<div></div>').addClass('order-block mb-3').attr('id','order-'+properties.get('placemarkId'));

    // Check subscriptions
    let subscribeBellClass = window.subscriptions.includes(user.id) ? 'icon-bell-cross' : 'icon-bell-check',
        avatarProps = {'background-image':'url('+avatar+')'};

    if (user.avatar_props) {
        $.each(user.avatar_props, function (prop, value) {
            avatarProps[prop] = value;
        });
    }

    $.get(
        getUserAgeUrl,
        {'id': user.id}
    ).done((data) => {
        window.useAge = data.age;
    });

    orderContainer
        .append(
            $('<h6></h6>').addClass('order-number').html(orderNumber + properties.get('orderId') + fromText + properties.get('date'))
        ).append(
            $('<div></div>').addClass('w-100 d-flex align-items-center justify-content-between')
                .append(
                    $('<div></div>').addClass('d-flex align-items-center justify-content-center')
                        .append(
                            $('<div></div>').addClass('avatar cir').css(avatarProps)
                        ).append(
                            $('<div></div>').css('width',215)
                                .append(
                                    $('<div></div>').addClass('user-name').html(user.family+' '+user.name)
                                ).append(
                                    $('<div></div>').addClass('born').html(window.useAge)
                                )
                        )
                )
                .append($('<i></i>').addClass('subscribe-icon ' + subscribeBellClass))
        );

    if (images.length) {
        let imagesContainer = $('<div></div>').addClass('images owl-carousel mt-3');
        $.each(images, function (k, image) {
            imagesContainer.append(
                $('<a></a>').addClass('fancybox').attr('href',image.image).append(
                    $('<div></div>').addClass('image').css('background-image','url(/'+image.image+')')
                )
            );
        });
        orderContainer.append(imagesContainer);
        enablePointImagesCarousel(imagesContainer,images.length > 1);
    }

    orderContainer.append($('<h2></h2>').addClass('order-type text-dark text-left mt-3 mb-4').html(properties.get('orderType')));

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

    orderContainer.append($('<p></p>').addClass('mb-1 text-left').html('<b>' + address +'</b>: ' + properties.get('address')));

    if (descriptionShort) {
        orderContainer
            .append(
                $('<p></p>').addClass('fw-bold text-left mt-2 mb-0').html(descriptionShortText + ':')
            ).append(
                $('<p></p>').addClass('text-left order-description mb-1').html(descriptionShort)
            );
    }

    if (descriptionFull) {
        orderContainer
            .append(
                $('<p></p>').addClass('fw-bold text-left mt-0 mb-2')
                    .append($('<a></a>').addClass('description-full').html(descriptionFullText + ' »'))
                );
    }

    orderContainer
        .append(
            $('<p></p>').addClass('text-left mb-2').html(
                '<b>' + numberOfPerformersText + ':</b> ' + properties.get('performers') + outOfText + properties.get('need_performers')
            )
        );

    if (userId !== user.id) {
        orderContainer.append($('<button></button>').addClass('respond-button btn btn-primary w-100').attr('type','button').append($('<span></span>').html(respondToAnOrder)));
    }

    orderContainer.append($('<button></button>').addClass('cb-copy btn btn-primary w-100 mt-3').attr({
        'type':'button',
        'order_id':properties.get('orderId')
    }).append($('<span></span>').html(copyOrderHrefToClipboard)));

    orderContainer.append($('<hr>'));
    window.pointsContainer.append(orderContainer);
    bindFancybox();
}

const removeSelectedPoints = (target, callBack) => {
    if ( (window.cickedTarget && !target) || (window.cickedTarget && target && window.cickedTarget !== target) ) {
        window.cickedTarget.options.set('iconColor', '#e6761b');

        window.selectedPoints.animate({'margin-left': -1 * (window.selectedPoints.width() + 150)}, 'slow', function () {
            window.selectedPointsOpened = false;
            if (callBack) callBack();
        });
    }
}

const clickedToCluster = (target, objects) => {
    window.cickedTarget = target;
    purgePointsContainer();
    $.each(target.properties.get('geoObjects'), function (k, object) {
        showOrder(object);
    });
    setBindsAndOpen();
}

const clickedToPoint = (point) => {
    window.cickedTarget = point;
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
            orderId = properties.get('orderId'),
            orderRespondModal = $('#order-respond-modal');
        $.post(
            orderResponseUrl,
            {
                '_token': window.tokenField,
                'id': orderId,
            }
        ).done(() => {
            orderRespondModal.find('.order-number').html(orderId);
            orderRespondModal.find('.order-date').html(properties.get('date'));
            orderRespondModal.find('.order-type').html(properties.get('orderType'));
            orderRespondModal.find('.order-address').html(properties.get('address'));
            orderRespondModal.modal('show');
            window.clusterer.remove(point);
            removeSelectedPoints();
        });
    });

    // Bind subscribe button
    $('.subscribe-icon').click(function (e) {
        e.preventDefault();
        let button = $(this),
            point = getPlaceMarkOnMap(button),
            userId = point.properties.get('user').id;

        $.get(
            subscribeUrl,
            {'user_id': userId}
        ).done((data) => {
            button.fadeOut(() => {
                button.toggleClass('icon-bell-cross',data.subscription).toggleClass('icon-bell-check',!data.subscription);
                button.fadeIn();
            });
            window.subscriptions.push(userId);
        });
    });

    // Click to description full
    $('.description-full').click(function (e) {
        e.preventDefault();
        let point = getPlaceMarkOnMap($(this)),
            properties = point.properties,
            fullDescriptionModal = $('#order-full-description-modal');

        fullDescriptionModal.find('h5').html(descriptionFullOfOrderText + properties.get('orderId') + '<br>' + fromText + properties.get('date'));
        fullDescriptionModal.find('.modal-body p').html(properties.get('description_full'));
        fullDescriptionModal.modal('show');
    });

    // Open selected points
    window.selectedPoints.animate({'margin-left': 0}, 'slow', function () {
        window.selectedPointsOpened = true;
    });

    // Copy order href
    $('.cb-copy').click(function () {
        let orderId = $(this).attr('order_id');
        navigator.clipboard.writeText(ordersUrl + '?id=' + orderId).then(() => {
            window.messageModal.find('h4').html(hrefIsCopied);
            window.messageModal.modal('show');
        });
    });

}

const enablePointImagesCarousel = (container, autoplay) => {
    container.owlCarousel(owlSettings(
        10,
        autoplay,
        6000,
        {0: {items: 1}},
        autoplay
    ));
}

const getPlaceMarkOnMap = (obj) => {
    let placemarkId = parseInt((obj).parents('.order-block').attr('id').replace('order-',''));
    return window.placemarks[placemarkId];
}
