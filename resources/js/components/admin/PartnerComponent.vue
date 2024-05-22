<template>
    <data-table-component
        :fields=fields
        :get_data_url="get_partners_url"
        :edit_url=edit_partner_url
        :delete_url="delete_partner_url"
        delete_phrase="Вы действительно хотите удалить этого партнера?"
        :arrange="arrange"
        broadcast_on="admin_partner_event"
        broadcast_as="admin_partner"
    ></data-table-component>
</template>

<script>
import DataTableComponent from "./blocks/DataTableComponent.vue";

export default {
    name: "PartnerComponent",
    components: {
        DataTableComponent
    },
    props: {
        'get_partners_url': String,
        'edit_partner_url': String,
        'delete_partner_url': String,
        'arrange': String
    },
    data() {
        return {
            fields: {
                id:'id',
                logo:'Логотип',
                name:'Название',
                about:'О партнере',
                info:'Описание',
            },
            arrangeCol: Object
        }
    },
}
</script>
