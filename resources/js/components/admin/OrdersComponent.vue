<template>
    <orders-data-table-component
        :fields=fields
        :get_data_url="get_orders_url"
        :edit_url=edit_order_url
        :delete_url="delete_order_url"
        delete_phrase="Вы действительно хотите удалить эту заявку?"
        :arrange="arrange"
        broadcast_on="admin_user_event"
        broadcast_as="admin_user"
    ></orders-data-table-component>
</template>

<script>
import OrdersDataTableComponent from "./blocks/OrdersDataTableComponent.vue";

export default {
    name: "OrdersComponent",
    components: {
        OrdersDataTableComponent
    },
    props: {
        'orders_url': String,
        'get_orders_url': String,
        'edit_order_url': String,
        'delete_order_url': String,
        'arrange': String
    },
    data() {
        return {
            ordersData: Object,
            fields: {
                id:'id',
                user:'Пользователь',
                order_type:'Тип заявки',
                name:'Название',
                description_short:'Короткое описание',
                address:'Адрес',
                status:'Статус',
            },
        }
    },
}
</script>
