$(document).ready(function () {
    ymaps.ready(mapInitWithContainer);
    let placemarks = [];
    setTimeout(function () {
        $.each(points, function (k,point) {
            placemarks.push(getPlaceMark(point.coordinates, {
                balloonContentHeader: '<a data-id="' + k + '" class="list-item">' + point.type + '</a>',
                balloonContentBody: point.address,
                placemarkId: k
            }));
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

        let clusterer = new ymaps.Clusterer({
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

        clusterer.add(placemarks);
        window.myMap.geoObjects.add(clusterer);
        bindOrderClick(points);
    }, 1000);
    // $('#order-modal').modal('show');
});

let mapInitWithContainer = () => {
    mapInit('map');
}

let bindOrderClick = (points) => {
    $(document).on('click', 'a.list-item', function() {
        let point = points[$(this).data().id],
            orderModal = $('#order-modal'),
            avatarBlock = $('#avatar-block'),
            orderSuBTypeList = orderModal.find('ul');

        orderSuBTypeList.html('');

        if (point.subtypes) {
            let subTypesIds = jQuery.parseJSON(point.subtypes);
            for (let stId=0;stId<subTypesIds.length;stId++) {
                for (let subType=0;subType<subtypes.length;subType++) {
                    if (subTypesIds[stId] === subtypes[subType].id) {
                        orderSuBTypeList.append($('<li></li>').html(subtypes[subType].name));
                        break;
                    }
                }
            }
        }
        $('#order-number').html(point.id);
        $('#order-date').html(point.date);
        if (point.user.avatar) avatarBlock.find('.avatar.cir').css('background-image','url("/' + point.user.avatar + '")');
        avatarBlock.find('.user-name').html(point.user.name);
        avatarBlock.find('.born').html(point.user.born);
        $('#order-type').html(point.type);
        $('#order-address').html(point.address);
        $('#need-performers').html(point.need_performers);
        $('#ready-to-help').html(point.performers);
        $('#order-description').html(point.description ? point.description : absentDescr);
        orderModal.modal('show');
    });
}
