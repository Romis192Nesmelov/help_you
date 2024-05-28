<template>
    <TicketsDataTableComponent
        :fields=fields
        :get_data_url="get_tickets_url"
        :edit_url=edit_ticket_url
        :delete_url="delete_ticket_url"
        delete_phrase="Вы действительно хотите удалить эту заявку?"
        :arrange="arrange"
        broadcast_on="admin_ticket_event"
        broadcast_as="admin_ticket"
    ></TicketsDataTableComponent>
</template>

<script>
import TicketsDataTableComponent from "./blocks/TicketsDataTableComponent.vue";

export default {
    name: "TicketsComponent",
    components: {
        TicketsDataTableComponent
    },
    props: {
        'get_tickets_url': String,
        'edit_ticket_url': String,
        'delete_ticket_url': String,
        'arrange': String
    },
    data() {
        return {
            fields: {
                id:'id',
                user:'Пользователь',
                image:'Скриншот',
                subject:'Тема обращения',
                text:'Текст обращения',
                read_admin:'Прочитано',
                status:'Статус',
            },
        }
    },
}
</script>
