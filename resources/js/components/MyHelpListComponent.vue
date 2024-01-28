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
    data() {
        return {
            tabs: {
                active: {
                    name: 'В работе',
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
        }
    },
}
</script>
