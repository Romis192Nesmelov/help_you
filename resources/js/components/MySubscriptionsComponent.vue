<template>
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
                @change-page="changePage"
                @unsubscribe="unsubscribe"
            ></OrdersTabsComponent>
        </div>
    </div>
</template>

<script>
import ModalComponent from "./blocks/ModalComponent.vue";
import MyOrdersListComponent from "./MyOrdersListComponent.vue";
import TabsComponent from "./blocks/TabsComponent.vue";
import OrdersTabsComponent from "./blocks/OrdersTabsComponent.vue";
import NoDataComponent from "./blocks/NoDataComponent.vue";

export default {
    extends: MyOrdersListComponent,
    name: "MySubscriptionsComponent",
    components: {
        ModalComponent,
        TabsComponent,
        OrdersTabsComponent,
        NoDataComponent
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
        }
    },
}
</script>
