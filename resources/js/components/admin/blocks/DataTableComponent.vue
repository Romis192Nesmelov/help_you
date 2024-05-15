<template>
    <ModalComponent id="confirm-delete-modal" head="Подтверждение удаления" v-if="delete_url">
        <h2 class="w-100 text-center">{{ delete_phrase }}</h2>
        <div class="modal-footer">
            <button @click="delRow" type="button" class="btn btn-primary" data-bs-dismiss="modal" data-dismiss="modal">
                <i class="icon-checkmark4"></i><span>Да</span>
            </button>

            <button type="button" class="btn btn-primary" data-bs-dismiss="modal" data-dismiss="modal">
                <i class="icon-cancel-circle2"></i><span>Нет</span>
            </button>
        </div>
    </ModalComponent>

    <div class="table-responsive">
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th class="text-center" v-for="(descr, field) in fields">{{ descr }}</th>
                    <th class="tools" v-if="edit_url || delete_url">Инструменты</th>
                </tr>
                <tr>
                    <th class="text-center arrange" v-for="(descr, field) in fields">
                        <i
                            v-if="field !== 'avatar'"
                            :class="(arrangeCol.field === field ? 'text-info ' : '') + (arrangeCol.field === field && arrangeCol.direction === 'desc' ? 'icon-arrow-up12' : 'icon-arrow-down12')"
                            @click="setArrange(field,arrangeCol.field === field ? arrangeCol.direction : 'asc')"
                        ></i>
                    </th>
                    <th class="text-center arrange" v-if="edit_url || delete_url"></th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="item in data.data">
                    <td class="text-center" v-for="(desc, field) in fields">
                        <AvatarComponent
                            v-if="field === 'avatar'"
                            :avatar_image="item.avatar"
                            :avatar_props="item.avatar_props"
                            :avatar_coof=0.35
                            :allow_change_avatar=0
                        ></AvatarComponent>
                        <span v-else-if="field === 'active'" :class="'label label-' + (item['active'] ? 'success' : 'warning')">{{ (item['active'] ? 'активен' : 'не активен') }}</span>
                        <span v-else-if="field === 'admin'" :class="'label label-' + (item['admin'] ? 'info' : 'primary')">{{ (item['admin'] ? 'админ' : 'пользователь') }}</span>
                        <span v-else>{{ item[field] }}</span>
                    </td>
                    <td class="tools" v-if="edit_url || delete_url">
                        <a v-if="edit_url" :href="edit_url + '?id=' + item.id">
                            <i title="Редактировать" class="icon-pencil7"></i>
                        </a>
                        <i title="Удалить" v-if="delete_url" class="icon-cancel-circle2 text-danger cursor-pointer" @click="confirmDel(item.id)"></i>
                    </td>
                </tr>
            </tbody>
        </table>
        <ul class="pagination" v-if="data.links && data.links.length > 3">
            <li :class="link.active ? 'active' : ''" v-for="(link, key) in data.links" :key="'paginate-'+key" @click="paginate(link.url)">
                <span v-if="link.label.indexOf('Пред') === 8">‹</span>
                <span v-else-if="link.label.indexOf('След') === 0">›</span>
                <span v-else>{{ link.label }}</span>
            </li>
        </ul>
    </div>
</template>

<script>
import ModalComponent from "../../blocks/ModalComponent.vue";
import ButtonComponent from "../../blocks/ButtonComponent.vue";
import AvatarComponent from "../../blocks/AvatarComponent.vue";

export default {
    name: "DataTableComponent",
    components: {
        ModalComponent,
        ButtonComponent,
        AvatarComponent,
    },
    props: {
        'fields': Object,
        'arrange': String,
        'delete_phrase': String|null,
        'get_data_url': String,
        'edit_url': String|null,
        'delete_url': String|null
    },
    emits: ['paginate','arrange'],
    created() {
        let self = this;
        this.arrangeCol = JSON.parse(self.arrange);
        this.getDataUrl = this.get_data_url;
        this.getData(this.get_data_url);
    },
    data() {
        return {
            getDataUrl: String,
            data: Object,
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
                self.data = response.data;
            });
        },
        paginate(url) {
            this.getData(this.getUrl(url));
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
                .then(function () {
                    self.getData(self.getUrl());
                })
                .catch(function (error) {
                    console.log(error);
                });
        },
        setArrange(field, currentDirection) {
            let newDirection = this.arrangeCol.field === field && currentDirection === 'asc' ? 'desc' : 'asc';
            this.arrangeCol.field = field;
            this.arrangeCol.direction = newDirection;
            this.getData(this.getUrl());
        },
        getUrl(url) {
            if (url) this.getDataUrl = url;
            let firstDelimiter = this.getDataUrl.indexOf('page') !== -1 ? '&' : '?';
            return this.getDataUrl + firstDelimiter + 'field=' + this.arrangeCol.field + '&direction=' + this.arrangeCol.direction
        }
    }
}
</script>
