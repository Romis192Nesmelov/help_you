<template>
    <div id="my-orders" class="col-12 col-lg-8 right-block">
        <div class="rounded-block tall">
            <h2>Мои запросы</h2>
            <TabsComponent
                :tabs="tabs"
                :counters="counters"
                :active="activeTab"
                @change-tab="changeTab"
            ></TabsComponent>
            <div class="content-block" v-for="(orderList, key) in orders" v-show="key === activeTab">
<!--                <table class="table datatable-basic default" v-if="orderList.length">-->
<!--                    <tr v-for="order in orderList" :key="order.id">-->
<!--                        <td class="id">{{ order.id }}</td>-->

<!--                        <td>{{ order.id }}</td>-->

<!--                        <td class="order-cell-edit icon">-->
<!--                            <a title="Редактировать" :href="edit_order_url + '?id=' + order.id" v-if="order.status === 2"><i class="icon-pencil5"></i></a>-->
<!--                            <div v-else-if="order.status === 1">-->
<!--                                <i title="Участники" class="icon-users4 me-1"></i>-->
<!--                            </div>-->
<!--                        </td>-->

<!--                        <td>{{ order.id }}</td>-->
<!--                        <td>{{ order.id }}</td>-->

<!--                    </tr>-->
<!--                </table>-->
                <h4 class="no-data-block text-uppercase text-secondary" v-else>Нет данных</h4>
            </div>
        </div>
    </div>
</template>

<script>
import TabsComponent from "./blocks/TabsComponent.vue";

export default {
    name: "OrderListComponent",
    components: {
        TabsComponent
    },
    created() {
        let self = this;
        this.orders = JSON.parse(this.order_list);
        this.activeTab = Object.keys(this.tabs)[0];
        $.each(this.orders, function (key,tab) {
            self.counters[key] = tab.length;
        });
        console.log(this.orders);
    },
    data() {
        return {
            tabs: {active:'Активные',approving:'На модерации',archive:'Архив'},
            counters: {},
            orders: Array,
            activeTab: String,

        }
    },
    props: {
        'head': String,
        'order_list': String,
        'edit_order_url': String
    },
    methods: {
        changeTab(key) {
            this.activeTab = key;
        }
    }
}
</script>
