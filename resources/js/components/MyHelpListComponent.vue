<template>
    <div id="my-help" class="col-12 col-lg-8 right-block">
        <div class="rounded-block tall">
            <h2>Моя помощь</h2>
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
                :subscription_mode=false
                :chat_mode=false
                @change-page="changePage"
            ></OrdersTabsComponent>
        </div>
    </div>
</template>

<script>
import TabsComponent from "./blocks/TabsComponent.vue";
import OrdersTabsComponent from "./blocks/OrdersTabsComponent.vue";
import NoDataComponent from "./blocks/NoDataComponent.vue";
import MyOrdersListComponent from "./MyOrdersListComponent.vue";

export default {
    extends: MyOrdersListComponent,
    name: "MyHelpListComponent",
    components: {
        TabsComponent,
        OrdersTabsComponent,
        NoDataComponent
    },
    created() {
        let self = this,
            orderIndex;

        window.Echo.private('notice_' + this.userId).listen('.notice', res => {
            if (res.notice === 'new_performer' || res.notice === 'remove_performer') {
                orderIndex = self.getOrderIndex('active', res.order.id);
                if (orderIndex !== null) self.tabs.active.orders[orderIndex].performers = res.performers;
                else self.refreshOrders();

                axios.get(self.read_unread_by_performer).then(function (response) {
                    window.emitter.emit('read-unread-by-my-help');
                });
            }
        });
    },
    data() {
        return {
            tabs: {
                active: {
                    name: 'В работе',
                    counter: 0,
                    orders: Array,
                    links: []
                }
            },
        }
    },
    props: {
        'read_unread_by_performer': String
    }
}
</script>
