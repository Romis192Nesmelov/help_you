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

    <div class="dt-top-line">
        <InputComponent
            type="text"
            name="filter"
            placeholder="Фильровать"
            v-model:value="filter"
            @keyup="filtered"
            @change="filtered"
        ></InputComponent>
        <div>
            <span class="hidden-sm">Показывать по:</span>
            <select class="select-show-by" v-model="showBy">
                <option v-for="showCase in showCases" :key="'show-by-' + showCase" :value="showCase">{{ showCase }}</option>
            </select>
        </div>
    </div>
    <div class="table-responsive">
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th class="text-center" v-for="(descr, field) in fields" :key="'dt-head-' + field">{{ descr }}</th>
                    <th class="tools" v-if="edit_url || delete_url">Инструменты</th>
                </tr>
                <tr>
                    <th class="text-center arrange" v-for="(descr, field) in fields" :key="'dt-arrange-by-' + field">
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
                <tr v-for="item in data.data" :key="'dt-row-' + item.id">
                    <td class="text-center" v-for="(desc, field) in fields" :key="'dt-cell-' + field">
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
import InputComponent from "../../blocks/InputComponent.vue";
import ButtonComponent from "../../blocks/ButtonComponent.vue";
import AvatarComponent from "../../blocks/AvatarComponent.vue";

export default {
    name: "DataTableComponent",
    components: {
        ModalComponent,
        InputComponent,
        ButtonComponent,
        AvatarComponent,
    },
    props: {
        'fields': Object,
        'arrange': String,
        'delete_phrase': String|null,
        'get_data_url': String,
        'edit_url': String|null,
        'delete_url': String|null,
        'broadcast_on': String|null,
        'broadcast_as': String|null,
    },
    emits: ['paginate','arrange'],
    created() {
        let self = this;
        this.arrangeCol = JSON.parse(self.arrange);
        this.getDataUrl = this.get_data_url;
        this.getData(this.get_data_url);

        $(document).ready(function () {
            $('.select-show-by').select2({
                minimumResultsForSearch: Infinity,
                width: 80
            }).change(function () {
                self.showBy = $(this).val();
                self.getData(self.getUrl(self.get_data_url));
            });
        });

        if (this.broadcast_on) {
            window.Echo.channel(this.broadcast_on).listen('.' + this.broadcast_as, res => {
                self.getData(self.getUrl());
            });
        }
    },
    data() {
        return {
            getDataUrl: String,
            data: Object,
            showCases: [5,10,20,30,50],
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
        filtered() {
            let self = this;
            // console.log(self.getUrl(self.get_data_url));
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
        }
    }
}
</script>
