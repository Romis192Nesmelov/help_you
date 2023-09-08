$(document).ready(function () {
    setTimeout(function () {
        ymaps.ready(init);
    },1500);
});

function init() {

    // {"type": "Feature", "id": 49, "geometry": {"type": "Point", "coordinates": [55.858585, 37.48498]}, "properties": {"balloonContentHeader": "<font size=3><b><a target='_blank' href='https://yandex.ru'>Здесь может быть ваша ссылка</a></b></font>", "balloonContentBody": "<p>Ваше имя: <input name='login'></p><p><em>Телефон в формате 2xxx-xxx:</em>  <input></p><p><input type='submit' value='Отправить'></p>", "balloonContentFooter": "<font size=1>Информация предоставлена: </font> <strong>этим балуном</strong>", "clusterCaption": "<strong><s>Еще</s> одна</strong> метка", "hintContent": "<strong>Текст  <s>подсказки</s></strong>"}}

    var myMap = new ymaps.Map('map', {
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

    objectManager.objects.options.set('preset', 'islands#orangeDotIcon');
    objectManager.objects.events.add(['mouseenter', 'mouseleave', 'click'], onObjectEvent);
    myMap.geoObjects.add(objectManager);

    $.post(window.getPointsURL, {
        '_token': $('input[name=_token]').val()
    }, function (data) {
        objectManager.add(data);
    });

    function onObjectEvent(e) {
        var objectId = e.get('objectId');
        if (e.get('type') === 'click') {
            let pointModal = $('#point-modal'),
                point = objectManager.objects.getById(objectId);

            pointModal.find('h3').html(point.name);
            pointModal.find('p').html(point.description);
            pointModal.modal('show');
        } else if (e.get('type') === 'mouseenter') {
            // Метод setObjectOptions позволяет задавать опции объекта "на лету".
            objectManager.objects.setObjectOptions(objectId, {
                preset: 'islands#redDotIcon'
            });
        } else {
            objectManager.objects.setObjectOptions(objectId, {
                preset: 'islands#orangeDotIcon'
            });
        }
    }
}
