<template>
    <TicketsDataTableComponent
        :fields=fields
        :get_data_url="get_answers_url"
        :edit_url=edit_answer_url
        :delete_url="delete_answer_url"
        delete_phrase="Вы действительно хотите удалить эту заявку?"
        :arrange="arrange"
        broadcast_on="admin_answer_event"
        broadcast_as="admin_answer"
    ></TicketsDataTableComponent>
</template>

<script>
import TicketsDataTableComponent from "./blocks/TicketsDataTableComponent.vue";

export default {
    name: "AnswersComponent",
    components: {
        TicketsDataTableComponent
    },
    props: {
        'get_answers_url': String,
        'edit_answer_url': String,
        'delete_answer_url': String,
        'arrange': String
    },
    data() {
        return {
            fields: {
                id:'id',
                user:'Пользователь',
                image:'Скриншот',
                text:'Текст обращения',
                read_admin:'Прочитано админом',
                read_owner:'Прочитано автором',
            },
        }
    },
}
</script>
