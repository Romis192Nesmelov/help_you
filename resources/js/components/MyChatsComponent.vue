<template>
    <div id="my-chats" class="col-12 col-lg-8 right-block">
        <div class="rounded-block tall">
            <h2>Мои чаты</h2>
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
                :chat_mode=true
                :chat_url="chat_url"
                @change-page="changePage"
            ></OrdersTabsComponent>
        </div>
    </div>
</template>

<script>
import MyOrdersListComponent from "./MyOrdersListComponent.vue";

export default {
    extends: MyOrdersListComponent,
    name: "MyChatsComponent",
    props: {
        'chat_url': String
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
                performer: {
                    name: 'Я исполнитель',
                    counter: 0,
                    orders: Array,
                    links: []
                }
            },
        }
    },
}
</script>
