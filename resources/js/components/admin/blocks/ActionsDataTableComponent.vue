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
                            @click="setArrange(field,arrangeCol.field === field ? arrangeCol.direction : 'asc')"
                        ></i>
                    </th>
                    <th class="text-center arrange" v-if="edit_url || delete_url"></th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="action in items.data" :key="'dt-row-' + action.id">
                    <td :class="'text-center' + (field === 'id' || field === 'rating' ? ' ' + field : '')" v-for="(desc, field) in fields" :key="'dt-cell-' + field">
                        <img v-if="field === 'image' && object.image" class="dt-image" :src="'/' + object.image" />
                        <span v-else-if="field === 'rating'" :class="'label label-' + ratings[action.rating-1].color">{{ ratings[action.rating-1].text }}</span>
                        <strong v-else-if="field === 'start' || field === 'end'">{{ new Date(action[field]).toLocaleDateString('ru-RU') }}</strong>
                        <span v-else>{{ action[field] }}</span>
                    </td>
                    <td class="tools" v-if="edit_url || delete_url">
                        <a v-if="edit_url" :href="edit_url + (edit_url.indexOf('?') === -1 ? '?' : '&') + 'id=' + action.id">
                            <i title="Редактировать" class="icon-pencil7"></i>
                        </a>
                        <i title="Удалить" class="icon-cancel-circle2 text-danger cursor-pointer" @click="confirmDel(action.id)"></i>
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
    name: "ActionsDataTableComponent",
    components: {
        UserPropertiesComponent,
        PaginatorComponent
    },
    data() {
        return {
            items: Object,
            itemsSubData: 'actions',
            partners: Object,
            ratings: [
                {color:'success','text':'Первый'},
                {color:'primary','text':'Второй'},
            ]
        }
    },
    methods: {
        getData(url) {
            let self = this;
            axios.get(url).then(function (response) {
                self.items = response.data.items;
                self.partners = response.data.partners;
            });
        },
        getAdditionalFilters() {
            let addFilter = '';
            for (let i=0;i<this.partners.length;i++) {
                if (this.partners[i].name.toLowerCase().indexOf(this.filter.toLowerCase()) !== -1) {
                    addFilter += '&partners_ids[]=' + this.partners[i].id;
                }
            }
            return addFilter;
        },
    }
}
</script>
