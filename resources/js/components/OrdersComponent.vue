<template xmlns="http://www.w3.org/1999/html">
    <OrderRespondModalComponent
        :order_id="respondOrderId"
        :order_name="respondOrderName"
        :order_date="respondOrderDate"
        :order_address="respondOrderAddress"
        :chat_url="chat_url"
    ></OrderRespondModalComponent>

    <ModalComponent id="order-full-description-modal" head="Описание заявки">
        <p class="p-3">{{ orderFullDescription }}</p>
    </ModalComponent>

    <div class="rounded-block mb-2 p-3 h-auto">
        <div class="row d-flex justify-content-center ps-3 pe-3">
            <div class="col-lg-2 col-sm-12 col-12 row d-flex align-items-end m-0 p-0">
                <label class="ms-3">Виды помощи</label>
                <select name="order_type" class="form-select" v-model="filterType">
                    <option value="0">Все</option>
                    <option v-for="(orderType, k) in orderTypes" :key="'order-type' + orderType.id" :value="orderType.id">{{ orderType.name }}</option>
                </select>
            </div>
            <div class="col-lg-4 col-12 row d-flex align-items-end m-0 p-0">
                <label class="ms-3">Кол-во исполнителей</label>
                <div class="col-sm-6 col-12">
                    <select name="performers_from" class="form-select mb-2 mb-sm-0" v-model="filterPerformersFrom">
                        <option v-for="value in 19" :key="'performers-from' + value" :value="value">{{ 'от ' + value }}</option>
                    </select>
                </div>
                <div class="col-sm-6 col-12">
                    <select name="performers_from" class="form-select" v-model="filterPerformersTo">
                        <option v-for="value in 20" :key="'performers-to' + value" :value="value">{{ 'до ' + value }}</option>
                    </select>
                </div>
            </div>
            <div class="col-lg-2 col-sm-12 col-12 d-flex align-items-end m-0">
                <ButtonComponent
                    class="btn btn-primary w-100 mt-lg-0 mt-3"
                    text="Применить"
                    @click=""
                ></ButtonComponent>
            </div>
            <div class="col-lg-4 col-12 mt-lg-0 mt-2 row d-flex align-items-end m-0 p-0">
                <label class="ms-3">Поиск</label>
                <div class="form-group mt-2">
                    <i class="icon-search4"></i>
                    <input
                        type="text"
                        name="search"
                        class="form-control has-icon"
                        v-model="searchingString"
                    >
                </div>
            </div>
        </div>
    </div>

    <div id="map-container" class="rounded-block">
        <div id="map"></div>

        <div id="selected-points">
            <i id="close-selected-points" class="icon-close2"></i>
            <div id="points-container">
                <div class="mb-3 order-block" v-for="point in selectedPoints" :key="point.properties.get('orderId')">
                    <h2 class="text-center mt-4 pt-1">{{ point.properties.get('name') + ' от ' + point.properties.get('date') }}</h2>
                    <div class="w-100 d-flex align-items-center justify-content-between">
                        <UserPropertiesComponent
                            :user="point.properties.get('user')"
                            :small=false
                            :avatar_coof=0.35
                            :use_rating=true
                            :allow_change_rating=false
                        ></UserPropertiesComponent>
                        <i @click="setSubscription($event, point.properties.get('user').id)" :class="'subscribe-icon ' + (getSubscription(point.properties.get('user').id) ? 'icon-bell-cross' : 'icon-bell-check')"></i>
                    </div>
                    <OrderCarouselImagesComponent :images="point.properties.get('images')"></OrderCarouselImagesComponent>
                    <OrderPropertiesComponent
                        :type="point.properties.get('orderType')"
                        :subtype="point.properties.get('subtype')"
                        :address="point.properties.get('address')"
                        :description_short="point.properties.get('description_short')"
                    ></OrderPropertiesComponent>

                    <p class="fw-bold text-left mt-0 mb-2" v-if="point.properties.get('description_full')">
                        <a href="#" @click.prevent="getOrderFullDescription(point.properties.get('description_full'))">Полное описание »</a>
                    </p>

                    <p class="text-left mb-2"><b>Кол-во исполнителей:</b> {{ point.properties.get('performers') + ' из ' + point.properties.get('need_performers') }}</p>

                    <button @click="orderRespond(point)" type="button" class="btn btn-primary w-100">
                        <span>Откликнуться на заявку</span>
                    </button>

                    <button @click="copyLink(point.properties.get('orderId'))" type="button" class="btn btn-primary w-100 mt-3">
                        <span>Скопировать ссылку</span>
                    </button>
                    <hr>
                </div>
            </div>

        </div>
    </div>
</template>

<script>
import ModalComponent from "./blocks/ModalComponent.vue";
import OrderRespondModalComponent from "./blocks/OrderRespondModalComponent.vue";
import UserPropertiesComponent from "./blocks/UserPropertiesComponent.vue";
import OrderCarouselImagesComponent from "./blocks/OrderCarouselImagesComponent.vue";
import OrderPropertiesComponent from "./blocks/OrderPropertiesComponent.vue";
import ButtonComponent from "./blocks/ButtonComponent.vue";

export default {
    name: "OrdersComponent",
    components: {
        ModalComponent,
        OrderRespondModalComponent,
        UserPropertiesComponent,
        OrderCarouselImagesComponent,
        OrderPropertiesComponent,
        ButtonComponent
    },
    created() {
        let self = this;

        this.userId = parseInt(this.user_id);
        this.orderTypes = JSON.parse(this.order_types);
        window.previewFlag = parseInt(this.get_preview_flag);
        window.pointsContainer = $('#points-container');
        window.cickedTarget = null;
        window.pointsOpenedFlag = false;

        window.clickedToCluster = (target) => {
            this.selectedPoints = target.properties.get('geoObjects');
            $.each(this.selectedPoints, function(k,point) {
                self.checkUnreadOrders(point.properties.get('orderId'));
            });
            this.showSelectedPointsDie();
        };

        window.clickedToPoint = (point) => {
            this.checkUnreadOrders(point.properties.get('orderId'));
            window.cickedTarget = point;
            this.selectedPoints = [point];
            this.showSelectedPointsDie();
        };

        window.hideSelectedPointsDie = (target, callBack) => {
            if ( (window.cickedTarget && !target) || (window.cickedTarget && target && window.cickedTarget !== target) ) {
                window.cickedTarget.options.set('iconColor', '#e6761b');

                window.selectedPointsDie.animate({'margin-left': -1 * (window.selectedPointsDie.width() + 150)}, 'slow', () => {
                    window.pointsOpenedFlag = false;
                    if (callBack) callBack();
                });
            }
        };

        if (this.order_id) window.openOrderId = parseInt(this.order_id);
        window.emitter.on('map-is-ready', () => {
            this.getOrders();
        });

        window.Echo.channel('order_event').listen('.order', res => {
            if (res.notice === 'remove_order') {
                self.removeOrder(res);
            } else if (res.notice === 'new_order_status') {
                self.newStatusOrder(res);
            }
        });

        $(document).ready(function () {
            $('#close-selected-points').click(() => {
                window.hideSelectedPointsDie();
            });
        });
    },
    props: {
        'user_id': String,
        'get_orders_url': String,
        'order_response_url': String,
        'read_order_url': String,
        'get_preview_url': String,
        'subscription_url': String,
        'chat_url': String,
        'get_preview_flag': String,
        'order_id': String,
        'order_types': String,
    },
    data() {
        return {
            userId: Number,
            orders: [],
            selectedPoints: [],
            respondOrderId: 0,
            respondOrderName: '',
            respondOrderDate: '',
            respondOrderAddress: '',
            openOrderId: 0,
            orderTypes: Array,
            orderFullDescription: '',
            filterType: 0,
            filterPerformersFrom: 1,
            filterPerformersTo: 20,
            searchingString: '',
        }
    },
    methods: {
        getOrders() {
            window.myMap.geoObjects.removeAll();
            window.hideSelectedPointsDie();

            let self = this, fields = {_token: window.tokenField}, url;

            if (this.previewFlag) url = this.get_preview_url;
            else {
                url = this.get_orders_url;

                if (this.filterType) fields.order_type = this.filterType;
                if (this.searchingString) fields.search = this.searchingString;

                fields.performers_from = this.filterPerformersFrom;
                fields.performers_to = this.filterPerformersTo;
            }

            axios.post(url, fields)
                .then(function (response) {
                    window.placemarks = [];
                    window.subscriptions = [];
                    window.unreadOrders = [];

                    if (response.data.subscriptions.length) {
                        $.each(response.data.subscriptions, function (k,subscription) {
                            window.subscriptions.push(subscription.user_id);
                            if (subscription.orders.length) {
                                $.each(subscription.orders, function (k,order) {
                                    window.unreadOrders.push(order.id);
                                });
                            }
                        });
                    }

                    if (response.data.orders.length) {
                        $.each(response.data.orders, function (k,point) {
                            let createdAt = new Date(point.created_at),
                                ImPerformer = false;
                            if (point.performers.length) {
                                for (let p=0;p<point.performers.length;p++) {
                                    if (point.performers[p].id === self.userId) {
                                        ImPerformer = true;
                                        break;
                                    }
                                }
                            }

                            if (!ImPerformer) {
                                window.placemarks.push(getPlaceMark([point.latitude, point.longitude], {
                                    placemarkId: k,
                                    // balloonContentHeader: point.order_type.name,
                                    // balloonContentBody: point.address,
                                    orderId: point.id,
                                    name: point.name,
                                    address: point.address,
                                    orderType: point.order_type.name,
                                    images: point.images,
                                    subtype: point.sub_type ? point.sub_type.name : null,
                                    need_performers: point.need_performers,
                                    performers: point.performers.length,
                                    user: point.user,
                                    date: createdAt.toLocaleDateString('ru-RU'),
                                    description_short: point.description_short,
                                    description_full: point.description_full
                                }));

                                if (window.getPreviewFlag) {
                                    window.getPreviewFlag = false;
                                    self.forceOpenOrder(0);
                                } else if (window.openOrderId && window.openOrderId === point.id) {
                                    self.forceOpenOrder(k);
                                }
                            }
                        });

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
                            let target = e.get('target');

                            target.options.set('iconColor', '#bc202e');
                            if (target.properties.get('geoObjects')) {
                                if (window.pointsOpenedFlag) {
                                    window.hideSelectedPointsDie(target,() => { window.clickedToCluster(target);});
                                } else window.clickedToCluster(target);
                            } else {
                                if (window.pointsOpenedFlag) {
                                    window.hideSelectedPointsDie(target,() => { window.clickedToPoint(target);});
                                } else window.clickedToPoint(target);
                            }
                        });

                        window.clusterer.add(window.placemarks);
                        window.myMap.geoObjects.add(window.clusterer);
                    }
                })
                .catch(function (error) {
                    console.log(error);
                });
        },
        forceOpenOrder(k) {
            window.openOrderId = null;
            window.cickedTarget = window.placemarks[k];
            window.placemarks[k].options.set('iconColor', '#bc202e');
            window.clickedToPoint(window.placemarks[k]);
        },
        showSelectedPointsDie() {
            window.enablePointImagesCarousel();
            window.bindFancybox();
            window.selectedPointsDie.animate({'margin-left': 0}, 'slow', () => {
                window.pointsOpenedFlag = true;
            });
        },
        getSubscription(userId) {
            return window.subscriptions.includes(userId)
        },
        setSubscription(event, userId) {
            let bellIcon = $(event.target);

            bellIcon.fadeOut('fast', () => {
                axios.get(this.subscription_url + '?user_id=' + userId).then(function (response) {
                    if (response.data.subscription) {
                        bellIcon.removeClass('icon-bell-check').addClass('icon-bell-cross');
                        window.subscriptions.push(userId);
                    } else {
                        bellIcon.removeClass('icon-bell-cross').addClass('icon-bell-check');
                        let index = window.subscriptions.indexOf(userId);
                        if (index !== 1) window.subscriptions.splice(index,1);
                    }
                    window.bellRinging(bellIcon);
                    bellIcon.fadeIn('fast');
                });
            });
        },
        checkUnreadOrders(orderId) {
            let index = window.unreadOrders.indexOf(orderId);
            if (index !== -1) {
                axios.get(this.read_order_url + '?order_id=' + orderId).then(function (response) {
                    window.unreadOrders.splice(index,1);
                    window.emitter.emit('read-order', orderId);
                });
            }
        },
        getOrderFullDescription(fullDescription) {
            this.orderFullDescription = fullDescription;
            $('#order-full-description-modal').modal('show');
        },
        orderRespond(point) {
            let self = this;
            axios.post(this.order_response_url, {
                _token: window.tokenField,
                id: point.properties.get('orderId')
            })
                .then(function (response) {
                    self.respondOrderId = point.properties.get('orderId');
                    self.respondOrderName = point.properties.get('name');
                    self.respondOrderDate = point.properties.get('date');
                    self.respondOrderAddress = point.properties.get('address');
                    $('#order-respond-modal').modal('show');
                })
                .catch(function (error) {
                    console.log(error);
                });
        },
        copyLink(orderId) {
            if (navigator.clipboard) {
                window.navigator.clipboard.writeText(this.get_orders_url + '?id=' + orderId).then(() => {
                    window.showMessage('Ссылка скопирована!');
                });
            }
        },
        newStatusOrder(res) {
            if (res.order.status === 2) {
                let markId = window.placemarks.length,
                    createdAt = new Date(res.order.created_at);

                window.placemarks.push(getPlaceMark([res.order.latitude, res.order.longitude], {
                    placemarkId: markId,
                    orderId: res.order.id,
                    name: res.order.name,
                    address: res.order.address,
                    orderType: res.order_type.name,
                    images: res.images,
                    subtype: res.sub_type.name,
                    need_performers: res.order.need_performers,
                    performers: res.performers.length,
                    user: res.user,
                    date: createdAt.toLocaleDateString('ru-RU'),
                    description_short: res.order.description_short,
                    description_full: res.order.description_full
                }));
                window.clusterer.add(window.placemarks);
            } else {
                this.removeOrder(res);
            }
        },
        removeOrder(res) {
            let indexUnread = window.unreadOrders.indexOf(res.order.id);
            if (indexUnread !== -1) window.unreadOrders.splice(indexUnread,1);
            for (let i=0;i<window.placemarks.length;i++) {
                if (window.placemarks[i].properties.get('orderId') === res.order.id) {
                    window.clusterer.remove(window.placemarks[i]);
                    window.placemarks.splice(i,1);
                    break;
                }
            }
            window.hideSelectedPointsDie();
        }
    }
}
</script>
