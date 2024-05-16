<template>
    <data-table-component
        :fields=fields
        delete_phrase="Вы действительно хотите удалить этого пользователя?"
        :get_data_url="get_users_url"
        :edit_url=edit_url
        :arrange="arrange"
        broadcast_on="user_event"
        broadcast_as="user"
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
        'edit_url': String,
        // 'delete_url': String,
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
