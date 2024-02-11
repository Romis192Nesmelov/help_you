<template>
    <div v-for="(tab, tabKey) in tabs" :key="tabKey" v-show="tabKey === active_tab">
        <div class="h-100 d-flex flex-column justify-content-between" v-if="tab.orders.length">
            <div class="row">
                <div class="col-lg-6 col-sm-12 mb-1" v-for="order in tab.orders" :key="order.id">
                    <div class="item-in-list text-center p-3">
                        <UserPropertiesComponent
                            :user="order.user"
                            :small=true
                            :avatar_coof=0.2
                            :use_rating=true
                            :allow_change_rating=false
                        ></UserPropertiesComponent>
                        <hr class="mt-1 mb-1">
                        <div class="h6 fw-bold">«{{ cropContent(order.name,30) }}»</div>
                        <div class="h6 text-secondary">{{ order.order_type.name }}</div>
                        <hr class="mt-1 mb-1">
                        <p class="text-secondary fs-6 mb-1"><small>{{ cropContent(order.address,40) }}</small></p>

                        <div class="w-100 text-center mb-1" v-if="subscription_mode">
                            <i class="icon-map orange me-1"></i>
                            <span class="text-secondary">
                                <small><a :href="orders_map_url + '?id=' + order.id" title="Перейти на карту">Посмотреть на карте</a></small>
                            </span>
                        </div>
                        <div class="w-100 text-center mb-1" role="button" @click="$emit('performers',{'performers':order.performers,'orderId':order.id})" v-else>
                            <span class="text-secondary me-1"><small>Исполнителей:</small></span>
                            <i title="Участники" class="orange icon-users4 me-1"></i>
                            <span class="text-secondary"><small>{{ order.performers.length }}</small></span>
                        </div>

                        <div class="w-100 d-flex justify-content-center" v-if="subscription_mode">
                            <ButtonComponent
                                class="btn btn-primary w-50 m-auto me-1"
                                text="Откликнуться"
                                @click="$emit('respond',order)"
                            ></ButtonComponent>
                            <ButtonComponent
                                class="btn btn-primary w-50 m-auto"
                                text="Отписаться"
                                @click="$emit('unsubscribe',order.read_subscriptions)"
                            ></ButtonComponent>
                        </div>

                        <div v-else-if="chat_mode">
                            <a :href="chat_url + '?id=' + order.id">
                                <ButtonComponent
                                    class="btn btn-primary w-50 m-auto"
                                    text="Перейти в чат"
                                ></ButtonComponent>
                            </a>
                        </div>
                        <ButtonComponent
                            class="btn btn-primary w-50 m-auto"
                            text="Завершить"
                            @click="$emit('closingOrder',order.id)"
                            v-if="user_id === order.user_id && order.status === 1 && !chat_mode"
                        ></ButtonComponent>
                        <ButtonComponent
                            class="btn btn-primary w-50 m-auto"
                            text="Возобновить"
                            @click="$emit('resumingOrder',order.id)"
                            v-else-if="user_id === order.user_id && order.status === 0"
                        ></ButtonComponent>
                        <div class="w-100 d-flex justify-content-center mt-1" v-else-if="user_id === order.user_id && (order.status === 2 || order.status === 3)">
                            <ButtonComponent
                                class="btn btn-primary me-1"
                                text="Удалить"
                                @click="$emit('deletingOrder',{'key':tabKey,'id':order.id})"
                            ></ButtonComponent>
                            <a :href="edit_order_url + '?id=' + order.id">
                                <ButtonComponent
                                    class="btn btn-primary"
                                    text="Редактировать"
                                ></ButtonComponent>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <PaginatorComponent
                :links="tab.links"
                :tab_key="tabKey"
                @paginate="paginate"
            ></PaginatorComponent>
        </div>
        <NoDataComponent v-else></NoDataComponent>
    </div>
</template>

<script>
import NoDataComponent from "./NoDataComponent.vue";
import UserPropertiesComponent from "./UserPropertiesComponent.vue";
import ButtonComponent from "./ButtonComponent.vue";
import PaginatorComponent from "./PaginatorComponent.vue";

export default {
    name: "OrdersTabsComponent",
    components: {
        NoDataComponent,
        UserPropertiesComponent,
        ButtonComponent,
        PaginatorComponent
    },
    props: {
        'user_id': Number,
        'tabs': Object,
        'active_tab': String,
        'edit_order_url': String,
        'chat_url': String,
        'orders_map_url': String,
        'subscription_mode': Boolean,
        'chat_mode': Boolean
    },
    emits: ['closingOrder','resumingOrder','deletingOrder','performers','removingPerformer','changePage','respond','unsubscribe'],
    methods: {
        cropContent(string,max) {
            string = string.toString();
            return string.length > max ? string.substr(0,max) + '…' : string;
        },
        paginate(params) {
            this.$emit('changePage',params);
        }
    }
}
</script>
