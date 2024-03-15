<template>
    <ModalComponent id="order-closing-confirm-modal" head="Закрытие заявки">
        <h4 class="text-center">Вы действительно хотите завершить эту заявку?</h4>
        <ModalPairButtonsComponent @click-yes="closingOrder"></ModalPairButtonsComponent>
    </ModalComponent>

    <ModalComponent id="order-resume-confirm-modal" head="Возобновление заявки">
        <h4 class="text-center">Вы действительно хотите возобновить эту заявку?</h4>
        <ModalPairButtonsComponent @click-yes="resumingOrder"></ModalPairButtonsComponent>
    </ModalComponent>

    <ModalComponent id="order-resumed-modal" head="Ваша заявка снова на модерации!">
        <img class="w-100" :src="resume_image" />
    </ModalComponent>

    <ModalComponent id="order-delete-confirm-modal" head="Удаление заявки">
        <h4 class="text-center">Вы действительно хотите удалить эту заявку?</h4>
        <ModalPairButtonsComponent @click-yes="deletingOrder"></ModalPairButtonsComponent>
    </ModalComponent>

    <ModalComponent id="order-performers-modal" head="Исполнители">
        <table class="table table-striped" v-if="performers.length">
            <tr v-for="performer in performers" :key="performer.id">
                <td>
                    <UserPropertiesComponent
                        :user="performer"
                        :small=true
                        :avatar_coof=0.2
                        :use_rating=true
                        :allow_change_rating=false
                    ></UserPropertiesComponent>
                </td>
                <td class="order-cell-delete icon align-middle">
                    <ButtonComponent
                        class="btn btn-primary"
                        text="Отказать"
                        @click="removePerformer(performer.id)"
                    ></ButtonComponent>
                </td>
            </tr>
        </table>
        <NoDataComponent v-else></NoDataComponent>
    </ModalComponent>

    <ModalComponent id="order-removing-performer-confirm-modal" head="Отказ от услуг">
        <h4 class="text-center">Вы действительно хотите отказаться от услуг этого пользователя?</h4>
        <ModalPairButtonsComponent @click-yes="removingPerformer"></ModalPairButtonsComponent>
    </ModalComponent>

    <ModalComponent id="order-closed-modal" head="Заявка успешно закрыта!">
        <div class="text-center">
            <h2 class="text-center mt-3">Выставите рейтинг исполнителя</h2>
            <UserPropertiesComponent
                :user="closingOrderUser"
                :avatar_coof=0.35
                :use_rating=false
                :allow_change_rating=false
                v-if="closingOrderUser"
            ></UserPropertiesComponent>
            <div class="w-100 d-flex justify-content-center">
                <RatingLineComponent
                    :income_rating=0
                    :allow_change_rating=true
                    :center=true
                    v-model:value="rating"
                ></RatingLineComponent>
            </div>
            <ButtonComponent
                class="btn btn-primary w-50 m-auto mt-3"
                text="Отправить"
                data-bs-dismiss='modal'
                @click="setRating"
            ></ButtonComponent>
        </div>
    </ModalComponent>

    <div id="my-orders" class="col-12 col-lg-8 right-block">
        <div class="rounded-block tall">
            <h2>Мои запросы</h2>
            <TabsComponent
                :tabs="tabs"
                :show_counters=true
                :active="activeTab"
                @change-tab="changeTab"
            ></TabsComponent>
            <OrdersTabsComponent
                :user_id="userId"
                :tabs="tabs"
                :active_tab="activeTab"
                :edit_order_url="edit_order_url"
                :subscription_mode=false
                :chat_mode=false
                @closing-order="closeOrder"
                @resuming-order="resumeOrder"
                @deleting-order="deleteOrder"
                @performers="getPerformers"
                @change-page="changePage"
            ></OrdersTabsComponent>
        </div>
    </div>
</template>

<script>
import ModalComponent from "./blocks/ModalComponent.vue";
import TabsComponent from "./blocks/TabsComponent.vue";
import OrdersTabsComponent from "./blocks/OrdersTabsComponent.vue";
import NoDataComponent from "./blocks/NoDataComponent.vue";
import ModalPairButtonsComponent from "./blocks/ModalPairButtonsComponent.vue";
import UserPropertiesComponent from "./blocks/UserPropertiesComponent.vue";
import RatingLineComponent from "./blocks/RatingLineComponent.vue";
import ButtonComponent from "./blocks/ButtonComponent.vue";

export default {
    name: "MyOrdersListComponent",
    components: {
        RatingLineComponent,
        ModalComponent,
        TabsComponent,
        OrdersTabsComponent,
        NoDataComponent,
        ModalPairButtonsComponent,
        UserPropertiesComponent,
        ButtonComponent
    },
    created() {
        let self = this,
            orderIndex;

        this.userId = parseInt(this.user_id);
        this.activeTab = Object.keys(this.tabs)[0];
        this.ordersUrls = JSON.parse(this.orders_urls);
        this.refreshOrders();

        window.Echo.private('notice_' + this.userId).listen('.notice', res => {
            orderIndex = self.getOrderIndex('active', res.order.id);
            if (res.notice === 'new_order_status') {
                self.refreshOrders();
            } else if ( (res.notice === 'new_performer' || res.notice === 'remove_performer') && orderIndex !== null) {
                self.tabs.active.orders[orderIndex].performers = res.performers;
            }

            axios.get(self.read_unread_by_my_orders).then(function (response) {
                window.emitter.emit('read-unread-by-my-orders');
            });
        });

        window.Echo.channel('order_event').listen('.order', res => {
            if (res.notice === 'remove_order' && res.user.id !== self.userId) {
                let tabKey = self.tabs['active'] ? 'active' : 'open';
                orderIndex = self.getOrderIndex(tabKey, res.order.id);
                if (orderIndex !== null) self.refreshOrders();
            }
        });
    },
    data() {
        return {
            userId: Number,
            closingOrderUser: null,
            tabs: {
                active: {
                    name: 'В работе',
                    counter: 0,
                    orders: Array,
                    links: []
                },
                open: {
                    name: 'Открыты',
                    counter: 0,
                    orders: Array,
                    links: []
                },
                approving: {
                    name: 'На модерации',
                    counter: 0,
                    orders: Array,
                    links: []
                },
                archive: {
                    name: 'Архив',
                    counter: 0,
                    orders: Array,
                    links: []
                }
            },
            ordersUrls: [],
            closingOrderId: 0,
            resumingOrderId: 0,
            deletingOrderId: 0,
            deletingOrderTab: '',
            removingPerformerId: 0,
            removingPerformerOrderId: 0,
            performers: [],
            rating: 0,
            activeTab: String,
        }
    },
    props: {
        'user_id': String,
        'head': String,
        'orders_urls': String,
        'read_unread_by_my_orders': String,
        'close_order_url': String,
        'resume_order_url': String,
        'delete_order_url': String,
        'remove_performer_url': String,
        'set_rating_url': String,
        'edit_order_url': String,
        'resume_image': String,
    },
    methods: {
        getOrders(url, key, emitFlag) {
            let self = this;
            axios.get(url).then(function (response) {
                self.tabs[key].counter = response.data.orders.total;
                self.tabs[key].orders = response.data.orders.data;
                self.tabs[key].links = response.data.orders.links;
                if (emitFlag) window.emitter.emit('refresh-complete');
            });
        },
        changePage(params) {
            this.getOrders(params.url, params.key, false);
        },
        changeTab(key) {
            this.activeTab = key;
        },
        closeOrder(order) {
            this.closingOrderId = order.id;
            this.closingOrderUser = order.performers[0];
            $('#order-closing-confirm-modal').modal('show');
        },
        closingOrder() {
            let self = this;
            axios.post(self.close_order_url, {
                _token: window.tokenField,
                id: self.closingOrderId
            }).then(function (response) {
                self.refreshOrders();
                $('#order-closed-modal').modal('show');
            });
        },
        resumeOrder(id) {
            this.resumingOrderId = id;
            $('#order-resume-confirm-modal').modal('show');
        },
        resumingOrder() {
            let self = this;
            axios.post(self.resume_order_url, {
                _token: window.tokenField,
                id: self.resumingOrderId
            }).then(function (response) {
                self.refreshOrders();
                $('#order-resumed-modal').modal('show');
            });
        },
        deleteOrder(params) {
            this.deletingOrderId = params.id;
            this.deletingOrderTab = params.key;
            $('#order-delete-confirm-modal').modal('show');
        },
        deletingOrder() {
            let self = this;
            axios.post(self.delete_order_url, {
                _token: window.tokenField,
                id: self.deletingOrderId
            }).then(function (response) {
                self.refreshOrders();
            });
        },
        getPerformers(params) {
            this.performers = params.performers;
            this.removingPerformerOrderId = params.orderId;
            $('#order-performers-modal').modal('show');
        },
        removePerformer(id) {
            this.removingPerformerId = id;
            $('#order-removing-performer-confirm-modal').modal('show');
        },
        removingPerformer() {
            let self = this;
            axios.post(self.remove_performer_url, {
                _token: window.tokenField,
                order_id: self.removingPerformerOrderId,
                user_id: self.removingPerformerId
            }).then(function (response) {
                let orderIndex = self.getOrderIndex('active', self.removingPerformerOrderId);
                for (let p=0;p<self.tabs.active.orders[orderIndex].performers.length;p++) {
                    if (self.tabs.active.orders[orderIndex].performers[p].id === self.removingPerformerId) {
                        self.tabs.active.orders[orderIndex].performers.splice(p,1);
                        break;
                    }
                }
                $('#order-performers-modal').modal('hide');
                window.showMessage(response.data.message);
            });
        },
        setRating() {
            let self = this;
            axios.post(self.set_rating_url, {
                _token: window.tokenField,
                order_id: self.closingOrderId,
                rating: self.rating
            });
        },
        refreshOrders() {
            let self = this;
            $.each(self.tabs, function (key) {
                self.getOrders(self.ordersUrls[key], key,true);
            });
        },
        getOrderIndex(tabKey, searchedId) {
            let index = null;
            for (let i=0;i<this.tabs[tabKey].orders.length;i++) {
                if (this.tabs[tabKey].orders[i].id === searchedId) {
                    index = i;
                    break;
                }
            }
            return index;
        }
    }
}
</script>
