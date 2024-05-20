<template>
    <delete-item-modal-component
        v-if="delete_url"
        :delete_phrase="delete_phrase"
        @click-yes="delRow"
    ></delete-item-modal-component>

    <div class="dt-top-line">
        <InputComponent
            type="text"
            name="filter"
            placeholder="Фильровать"
            v-model:value="filter"
            @keyup="filtered"
            @change="filtered"
        ></InputComponent>
        <ShowCaseComponent
            @change-show-case="changeShowCase"
        ></ShowCaseComponent>
    </div>
    <div class="table-responsive">
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th class="text-center" v-for="(descr, field) in fields" :key="'dt-head-' + field">{{ descr }}</th>
                    <th class="tools" v-if="edit_url || delete_url"></th>
                </tr>
                <tr>
                    <th class="text-center arrange" v-for="(descr, field) in fields" :key="'dt-arrange-by-' + field">
                        <i
                            v-if="field !== 'avatar' && field !== 'rating'"
                            :class="(arrangeCol.field === field ? 'text-info ' : '') + (arrangeCol.field === field && arrangeCol.direction === 'desc' ? 'icon-arrow-up12' : 'icon-arrow-down12')"
                            @click="setArrange((field === 'user' || field === 'order_type' ? field + '_id' : field),arrangeCol.field === field ? arrangeCol.direction : 'asc')"
                        ></i>
                    </th>
                    <th class="text-center arrange" v-if="edit_url || delete_url"></th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="order in orders.data" :key="'dt-row-' + order.id">
                    <td :class="'text-center' + (field === 'id' || field === 'user' || field === 'status' ? ' ' + field : '')" v-for="(desc, field) in fields" :key="'dt-cell-' + field">
                        <UserPropertiesComponent
                            v-if="field === 'user'"
                            :user="order.user"
                            :small="true"
                            :use_rating="true"
                            :allow_change_rating="false"
                        ></UserPropertiesComponent>
                        <span v-else-if="field === 'status'" :class="'label label-' + statuses[order.status].color">{{ statuses[order.status].text }}</span>
                        <strong v-else-if="field === 'order_type'">{{ order.order_type.name }}</strong>
                        <span v-else>{{ order[field] }}</span>
                    </td>
                    <td class="tools" v-if="edit_url || delete_url">
                        <a v-if="edit_url" :href="edit_url + '?id=' + order.id">
                            <i title="Редактировать" class="icon-pencil7"></i>
                        </a>
                        <i title="Удалить" class="icon-cancel-circle2 text-danger cursor-pointer" @click="confirmDel(order.id)"></i>
                    </td>
                </tr>
            </tbody>
        </table>
        <paginator-component
            v-if="orders.links"
            :links="orders.links"
            @paginate="paginate"
        ></paginator-component>
    </div>
</template>

<script>
import DataTableComponent from "./DataTableComponent.vue";
import UserPropertiesComponent from "../../blocks/UserPropertiesComponent.vue";
import PaginatorComponent from "./PaginatorComponent.vue";

export default {
    extends: DataTableComponent,
    name: "DataTableComponent",
    components: {
        UserPropertiesComponent,
        PaginatorComponent
    },
    data() {
        return {
            orders: Object,
            users: Object,
            types: Object,
            statuses: [
                {color:'success','text':'Закрыта'},
                {color:'primary','text':'В работе'},
                {color:'default','text':'Открыта'},
                {color:'danger','text':'Новая'},
            ]
        }
    },
    methods: {
        getData(url) {
            let self = this;
            axios.get(url).then(function (response) {
                self.orders = response.data.orders;
                self.users = response.data.users;
                self.types = response.data.types;
            });
        },
        getAdditionalFilters() {
            let addFilter = '';
            for (let i=0;i<this.types.length;i++) {
                if (this.types[i].name.toLowerCase().indexOf(this.filter.toLowerCase()) !== -1) {
                    addFilter += '&order_types_ids[]=' + this.types[i].id;
                }
            }
            for (let i=0;i<this.users.length;i++) {
                if (this.users[i].name.toLowerCase().indexOf(this.filter.toLowerCase()) !== -1 || this.users[i].family.toLowerCase().indexOf(this.filter.toLowerCase()) !== -1) {
                    addFilter += '&users_ids[]=' + this.users[i].id;
                }
            }
            return addFilter;
        },
    }
}
</script>
