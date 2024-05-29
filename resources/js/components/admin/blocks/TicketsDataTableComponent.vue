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
                            :class="(arrangeCol.field === field ? 'text-info ' : '') + (arrangeCol.field === field && arrangeCol.direction === 'desc' ? 'icon-arrow-up12' : 'icon-arrow-down12')"
                            @click="setArrange((field === 'user' ? field + '_id' : field),arrangeCol.field === field ? arrangeCol.direction : 'asc')"
                        ></i>
                    </th>
                    <th class="text-center arrange"></th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="object in items.data" :key="'dt-row-' + object.id">
                    <td :class="'text-center' + (field === 'id' || field === 'user' || field === 'status' || field === 'read_admin' || field === 'read_owner' ? ' ' + field : '')" v-for="(desc, field) in fields" :key="'dt-cell-' + field">
                        <UserPropertiesComponent
                            v-if="field === 'user'"
                            :user="object.user"
                            :small="true"
                            :use_rating="true"
                            :allow_change_rating="false"
                        ></UserPropertiesComponent>
                        <img v-else-if="field === 'image' && object.image" class="dt-image" :src="'/' + object.image" />
                        <span v-else-if="field === 'status'" :class="'label label-' + statuses[object.status].color">{{ statuses[object.status].text }}</span>
                        <span v-else-if="field === 'read_admin'" :class="'label label-' + readStatuses[object.read_admin].color">{{ readStatuses[object.read_admin].text }}</span>
                        <span v-else-if="field === 'read_owner'" :class="'label label-' + readStatuses[object.read_owner].color">{{ readStatuses[object.read_owner].text }}</span>
                        <span v-else-if="field === 'text'">{{ cutString(object[field],300) }}</span>
                        <span v-else>{{ object[field] }}</span>
                    </td>
                    <td class="tools" v-if="edit_url || delete_url">
                        <a v-if="edit_url" :href="getUrlWithParam(object.id)">
                            <i title="Редактировать" class="icon-pencil7"></i>
                        </a>
                        <i title="Удалить" class="icon-cancel-circle2 text-danger cursor-pointer" @click="confirmDel(object.id)"></i>
                    </td>
                </tr>
            </tbody>
        </table>
        <paginator-component
            v-if="items.links"
            :links="items.links"
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
    name: "TicketsDataTableComponent",
    components: {
        UserPropertiesComponent,
        PaginatorComponent
    },
    data() {
        return {
            users: Object,
            statuses: [
                {color:'danger','text':'Открыт'},
                {color:'primary','text':'Закрыт'},
            ],
            readStatuses: [
                {color:'danger','text':'Не прочитано'},
                {color:'primary','text':'Прочитано'},
            ]
        }
    },
    methods: {
        getData(url) {
            let self = this;
            axios.get(url).then(function (response) {
                self.items = response.data.items;
                self.users = response.data.users;
            });
        },
        getAdditionalFilters() {
            return this.getAdditionalFilterUser();
        }
    }
}
</script>
