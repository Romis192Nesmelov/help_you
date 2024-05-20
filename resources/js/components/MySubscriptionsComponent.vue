<template>
    <OrderRespondModalComponent
        :order_id="respondOrderId"
        :order_name="respondOrderName"
        :order_date="respondOrderDate"
        :order_address="respondOrderAddress"
        :chat_url="chat_url"
    ></OrderRespondModalComponent>

    <ModalComponent id="unsubscribe-confirm-modal" head="Закрытие подписки">
        <h4 class="text-center">Вы действительно хотите отписаться от этого пользователя?</h4>
        <ModalPairButtonsComponent @click-yes="unsubscribing"></ModalPairButtonsComponent>
    </ModalComponent>

    <div id="my-subscriptions" class="col-12 col-lg-8 right-block">
        <div class="rounded-block tall">
            <h2>Мои подписки</h2>
            <TabsComponent
                :tabs="tabs"
                :show_counters=true
                :active="activeTab"
            ></TabsComponent>
            <OrdersTabsComponent
                :user_id="userId"
                :tabs="tabs"
                :active_tab="activeTab"
                :subscription_mode=true
                :chat_mode=false
                :orders_map_url="orders_map_url"
                @change-page="changePage"
                @respond="orderRespond"
                @unsubscribe="unsubscribe"
            ></OrdersTabsComponent>
        </div>
    </div>
</template>

<script>
import OrderRespondModalComponent from "./blocks/OrderRespondModalComponent.vue";
import MyOrdersListComponent from "./MyOrdersListComponent.vue";

export default {
    extends: MyOrdersListComponent,
    name: "MySubscriptionsComponent",
    components: {
        OrderRespondModalComponent,
    },
    created() {
        window.emitter.on('refresh-complete', () => {
            if (!this.tabs.open.orders.length) {
                window.emitter.emit('my-subscriptions', false);
            }
        });

        window.Echo.private('notice_' + this.userId).listen('.notice', res => {
            if (res.notice === 'new_order_in_subscription') this.refreshOrders();
        });
    },
    data() {
        return {
            userId: Number,
            respondOrderId: 0,
            respondOrderName: '',
            respondOrderDate: '',
            respondOrderAddress: '',
            tabs: {
                open: {
                    name: 'Открыты',
                    counter: 0,
                    orders: Array,
                    links: []
                }
            },
            subscription: 0,
        }
    },
    props: {
        'unsubscribe_url': String,
        'order_response_url': String,
        'orders_urls': String,
        'orders_map_url': String,
        'read_order_url': String,
        'chat_url': String,
    },
    methods: {
        unsubscribe(readSubscriptions) {
            let self = this;
            $.each(readSubscriptions, function (k,readSubscription) {
                if (readSubscription.subscription.subscriber_id === self.userId) {
                    self.subscription = readSubscription.subscription;
                    $('#unsubscribe-confirm-modal').modal('show');
                    return false;
                }
            });
        },
        unsubscribing() {
            let self = this;
            axios.post(self.unsubscribe_url, {
                _token: window.tokenField,
                id: self.subscription.id
            }).then(function (response) {
                self.refreshOrders();
            });
        },
        orderRespond(order) {
            let self = this;
            axios.post(this.order_response_url, {
                _token: window.tokenField,
                id: order.id
            })
                .then(function (response) {
                    self.respondOrderId = order.id;
                    self.respondOrderName = order.name;
                    self.respondOrderDate = order.date;
                    self.respondOrderAddress = order.address;
                    $('#order-respond-modal').modal('show');
                })
                .catch(function (error) {
                    console.log(error);
                });

            axios.get(this.read_order_url + '?order_id=' + order.id).then(function (response) {
                window.emitter.emit('read-order', order.id);
            });
        }
    },
}
</script>
