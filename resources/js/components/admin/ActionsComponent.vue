<template>
    <actions-data-table-component
        :fields=fields
        :get_data_url="get_actions_url"
        :edit_url=edit_action_url
        :delete_url="delete_action_url"
        delete_phrase="Вы действительно хотите удалить эту акцию?"
        :arrange="arrange"
        broadcast_on="admin_action_event"
        broadcast_as="admin_action"
    ></actions-data-table-component>
</template>

<script>
import ActionsDataTableComponent from "./blocks/ActionsDataTableComponent.vue";

export default {
    name: "ActionsComponent",
    components: {
        ActionsDataTableComponent
    },
    props: {
        'actions_url': String,
        'get_actions_url': String,
        'edit_action_url': String,
        'delete_action_url': String,
        'arrange': String
    },
    data() {
        return {
            fields: {
                id:'id',
                name:'Название',
                start:'Начало акции',
                end:'Окончание акции',
                rating: 'Рейтинг',
                partner_id:'Партнер',
            },
        }
    },
}
</script>
