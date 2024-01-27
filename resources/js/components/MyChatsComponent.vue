<template>

</template>

<script>
import MyOrdersListComponent from "./MyOrdersListComponent.vue";
import TabsComponent from "./blocks/TabsComponent.vue";
import OrdersTabsComponent from "./blocks/OrdersTabsComponent.vue";
import NoDataComponent from "./blocks/NoDataComponent.vue";

export default {
    extends: MyOrdersListComponent,
    name: "MyChatsComponent",
    components: {
        TabsComponent,
        OrdersTabsComponent,
        NoDataComponent
    },
    created() {
        this.userId = parseInt(this.user_id);
        this.activeTab = Object.keys(this.tabs)[0];
        this.ordersUrls = JSON.parse(this.orders_urls);
        this.refreshOrders();

        window.Echo.private('notice_' + this.userId).listen('.notice', res => {
            let self = this,
                orderIndex;

            if (res.notice === 'new_performer' || res.notice === 'remove_performer') {
                orderIndex = self.getOrderIndex('active', res.order.id);
                if (orderIndex !== null) {
                    self.tabs.active.orders[orderIndex].performers = res.performers;
                }
            } else if (res.notice === 'new_order_status') {
                orderIndex = self.getOrderIndex('active', res.order.id);
                if (orderIndex !== null) {
                    this.refreshOrders();
                }
            }
        });
    },
    data() {
        return {
            userId: Number,
            tabs: {
                my_orders: {
                    name: 'Мои заявки',
                    counter: 0,
                    orders: Array,
                    links: []
                },
                im_performer: {
                    name: 'Я исполнитель',
                    counter: 0,
                    orders: Array,
                    links: []
                }
            },
            activeTab: String
        }
    },
    props: {
        'user_id': String,
        'orders_url': String,
    },
}
</script>
