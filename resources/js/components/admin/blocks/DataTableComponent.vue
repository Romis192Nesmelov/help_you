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
                            v-if="field !== 'avatar' && field !== 'logo' && field !== 'image' && field !== 'rating'"
                            :class="(arrangeCol.field === field ? 'text-info ' : '') + (arrangeCol.field === field && arrangeCol.direction === 'desc' ? 'icon-arrow-up12' : 'icon-arrow-down12')"
                            @click="setArrange(field,arrangeCol.field === field ? arrangeCol.direction : 'asc')"
                        ></i>
                    </th>
                    <th class="text-center arrange" v-if="edit_url || delete_url"></th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="item in items.data" :key="'dt-row-' + item.id">
                    <td :class="'text-center' + (
                        field === 'id'      ||
                        field === 'avatar'  ||
                        field === 'logo'    ||
                        field === 'image'   ||
                        field === 'user'    ||
                        field === 'status'  ||
                        field === 'active'  ||
                        field === 'admin'
                        ? ' ' + field : '')" v-for="(desc, field) in fields" :key="'dt-cell-' + field">
                        <AvatarComponent
                            v-if="field === 'avatar'"
                            :user_id="item.id"
                            :small=true
                            :avatar_image="item.avatar"
                            :avatar_props="item.avatar_props"
                            :change_avatar_url="change_avatar_url"
                        ></AvatarComponent>
                        <RatingLineComponent
                            :income_rating="getUserRating(item.ratings)"
                            :allow_change_rating="false"
                            v-else-if="field === 'rating'"
                        ></RatingLineComponent>
                        <a v-else-if="field === 'logo' || field === 'image'" class="fancybox" :href="'/' + item[field]"><img :src="'/' + item[field]" /></a>
                        <span v-else-if="field === 'active'" :class="'label label-' + (item['active'] ? 'success' : 'warning')">{{ (item['active'] ? 'активен' : 'не активен') }}</span>
                        <span v-else-if="field === 'admin'" :class="'label label-' + (item['admin'] ? 'info' : 'primary')">{{ (item['admin'] ? 'админ' : 'пользователь') }}</span>
                        <span v-else>{{ item[field] }}</span>
                    </td>
                    <td class="tools" v-if="edit_url || delete_url">
                        <a v-if="edit_url" :href="edit_url + (edit_url.indexOf('?') === -1 ? '?' : '&') + 'id=' + item.id">
                            <i title="Редактировать" class="icon-pencil7"></i>
                        </a>
                        <i title="Удалить" v-if="delete_url" class="icon-cancel-circle2 text-danger cursor-pointer" @click="confirmDel(item.id)"></i>
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
import InputComponent from "../../blocks/InputComponent.vue";
import ButtonComponent from "../../blocks/ButtonComponent.vue";
import DeleteItemModalComponent from "./DeleteItemModalComponent.vue";
import ShowCaseComponent from "./ShowCaseComponent.vue";
import PaginatorComponent from "./PaginatorComponent.vue";
import AvatarComponent from "../../blocks/AvatarComponent.vue";
import RatingLineComponent from "../../blocks/RatingLineComponent.vue";

export default {
    name: "DataTableComponent",
    components: {
        InputComponent,
        ButtonComponent,
        DeleteItemModalComponent,
        ShowCaseComponent,
        PaginatorComponent,
        AvatarComponent,
        RatingLineComponent
    },
    props: {
        'fields': Object,
        'arrange': String,
        'delete_phrase': String|null,
        'get_data_url': String,
        'edit_url': String|null,
        'delete_url': String|null,
        'change_avatar_url': String|NaN,
        'broadcast_on': String|null,
        'broadcast_as': String|null,
    },
    created() {
        let self = this;
        this.arrangeCol = JSON.parse(self.arrange);
        this.getDataUrl = this.get_data_url;
        this.getData(this.get_data_url);

        if (this.broadcast_on) {
            window.Echo.private(this.broadcast_on).listen('.' + this.broadcast_as, res => {
                if (res.notice === 'new_item' || res.notice === 'del_item') {
                    self.getData(self.getUrl());
                } else if (res.notice === 'change_item') {
                    for (let i=0;i<self.items.data.length;i++) {
                        if (items.data[i].id === res.model.id) {
                            items.data[i] = res.model;
                            break;
                        }
                    }
                }
            });
        }
    },
    data() {
        return {
            getDataUrl: String,
            items: Object,
            showBy: 10,
            filter: '',
            deleteId: Number,
            arrangeCol: Object,
            deleteModal: Object|null,
            deletingId: null
        }
    },
    methods: {
        getData(url) {
            let self = this;
            axios.get(url).then(function (response) {
                self.items = response.data;
            });
        },
        paginate(url) {
            this.getData(this.getUrl(url));
        },
        changeShowCase(showBy) {
            this.showBy = showBy;
            this.getData(this.getUrl(this.get_data_url));
        },
        confirmDel(id) {
            this.deleteId = id;
            $('#confirm-delete-modal').modal('show');
        },
        delRow() {
            let self = this,
                formData = new FormData();

            formData.append('_token', window.tokenField);
            formData.append('id', this.deleteId);

            axios.post(this.delete_url, formData)
                .then(function (response) {
                    self.getData(self.getUrl());
                    window.showMessage(response.data.message);
                })
                .catch(function (error) {
                    // console.log(error);
                });
        },
        setArrange(field, currentDirection) {
            let newDirection = this.arrangeCol.field === field && currentDirection === 'asc' ? 'desc' : 'asc';
            this.arrangeCol.field = field;
            this.arrangeCol.direction = newDirection;
            this.getData(this.getUrl());
        },
        filtered() {
            let self = this;
            self.getData(self.getUrl(self.get_data_url));
        },
        getUrl(url) {
            if (url) this.getDataUrl = url;
            let firstDelimiter = this.getDataUrl.indexOf('page') !== -1 ? '&' : '?';
            return this.getDataUrl
                + firstDelimiter + 'field=' + this.arrangeCol.field
                + '&direction=' + this.arrangeCol.direction
                + '&show_by=' + this.showBy
                + (this.filter ? '&filter=' + this.filter : '')
                + this.getAdditionalFilters()
        },
        getAdditionalFilters() {
            return '';
        },
        getUserRating(ratings) {
            return window.userRating(ratings);
        }
    }
}
</script>
