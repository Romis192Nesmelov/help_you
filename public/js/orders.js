$(document).ready(function () {
    ymaps.ready(mapInitWithContainer);
    window.orderModal = $('#order-modal');
    window.selectedPoint = null;

    $('#apply-button').click(function (e) {
        e.preventDefault();
        window.myMap.geoObjects.removeAll();
        getPoints();
    });

    $('#respond-button').click(function (e) {
        e.preventDefault();
        window.orderModal.modal('hide');
        $('#order-respond-modal').modal('show');
        let properties = window.selectedPoint.properties;
        $.post(
            orderResponseUrl,
            {
                '_token': $('input[name=_token]').val(),
                'order_id': properties.get('orderId'),
            }
        ).done(() => {
            window.clusterer.remove(window.selectedPoint);
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
        $.each(data, function (k,point) {
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
    });
}

let addPointsToMap = () => {
    window.clusterer.add(window.placemarks);
    window.myMap.geoObjects.add(window.clusterer);
    bindOrderClick();
}

let bindOrderClick = () => {
    $(document).on('click', 'a.list-item', function() {
        let point = window.placemarks[$(this).data().id],
            orderSuBTypeList = orderModal.find('ul'),
            properties = point.properties,
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
        $('.order-number').html(properties.get('orderId'));
        $('.order-date').html(properties.get('date'));
        if (user.avatar) $('.avatar.cir').css('background-image','url("/' + user.avatar + '")');
        $('.user-name').html(user.name);
        $('.born').html(user.born);
        $('.order-type').html(properties.get('orderType'));
        $('.order-address').html(properties.get('balloonContentBody'));
        $('#need-performers').html(properties.get('need_performers'));
        $('#ready-to-help').html(properties.get('performers'));
        $('#order-description').html(description ? description : absentDescr);
        window.orderModal.modal('show');
    });
}
