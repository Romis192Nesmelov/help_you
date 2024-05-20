<template>
    <data-table-component
        :fields=fields
        :get_data_url="get_users_url"
        :edit_url=edit_user_url
        :arrange="arrange"
        broadcast_on="admin_user_event"
        broadcast_as="admin_user"
    ></data-table-component>
</template>

<script>
import DataTableComponent from "./blocks/DataTableComponent.vue";

export default {
    name: "UsersComponent",
    components: {
        DataTableComponent
    },
    props: {
        'users_url': String,
        'get_users_url': String,
        'edit_user_url': String,
        'arrange': String
    },
    data() {
        return {
            fields: {
                id:'id',
                avatar:'Аватар',
                name:'Имя',
                family:'Фамилия',
                phone:'Телефон',
                email:'E-mail',
                active:'Статус',
                admin:'Админ'
            },
            arrangeCol: Object
        }
    },
}
</script>
