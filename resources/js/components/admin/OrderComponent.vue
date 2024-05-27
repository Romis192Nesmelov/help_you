<template>
    <div class="col-lg-3 col-md-4 col-sm-12">
        <div class="panel panel-flat" v-if="!parent_id">
            <div class="panel-heading">
                <h5 class="panel-title">Автор заявки</h5>
            </div>
            <div class="panel-body">
                <select v-model="obj.user_id" class="select form-control">
                    <option v-for="(user, k) in users" :value="user.id" :key="'author' + k">
                        <UserNameComponent :user="user"></UserNameComponent>
                    </option>
                </select>
            </div>
        </div>
        <div class="panel panel-flat">
            <div class="panel-heading">
                <h5 class="panel-title">Тип заявки</h5>
            </div>
            <div class="panel-body">
                <div class="form-group no-margin-bottom" v-for="(type, k) in types">
                    <input :id="'order_type_' + type.id" :value="type.id" type="radio" v-model="obj.order_type_id">
                    <label class="ml-10 form-check-label" :for="'order_type_' + type.id">{{ type.name }}</label>
                    <div :class="'subtypes' + (obj.order_type_id !== type.id ? ' hidden' : '')" v-if="type.subtypes.length">
                        <div class="pl-15" v-for="(subtype, k) in type.subtypes" :key="'order_subtype_' + subtype.id">
                            <div class="form-group no-margin-bottom">
                                <input :id="'order_sub_type_' + subtype.id" :value="subtype.id" type="radio" v-model="obj.subtype_id">
                                <label class="ml-10 form-check-label" :for="'order_sub_type_' + subtype.id"><small>{{ subtype.name }}</small></label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="panel panel-flat">
            <div class="panel-heading">
                <h5 class="panel-title">Статус заявки</h5>
            </div>
            <div class="panel-body">
                <div class="form-group no-margin-bottom" v-for="(value, k) in statusValues" :key="'radio' + k">
                    <input class="order-status" :id="'order_status_' + value.val" :value="value.val" type="radio" v-model="obj.status" @change="changeStatusOrPerformer">
                    <label class="ml-10 form-check-label" :for="'order_status_' + value.val"><small>{{ value.description }}</small></label>
                    <div class="form-control-feedback" v-if="errors.status">
                        <i class="icon-cancel-circle2"></i>
                    </div>
                </div>
                <div class="error" v-if="errors.status">{{ errors.status }}</div>
            </div>
        </div>
        <div class="panel panel-flat">
            <div class="panel-heading">
                <h5 class="panel-title">Исполнитель</h5>
            </div>
            <div class="panel-body">
                <select id="performer" v-model="performer_id" class="select form-control" @change="changeStatusOrPerformer">
                    <option :value="0">Нет</option>
                    <option v-show="user.id !== obj.user_id" v-for="(user, k) in users" :value="user.id" :key="'author' + k">
                        <UserNameComponent :user="user"></UserNameComponent>
                    </option>
                </select>
                <div class="error" v-if="errors.status">{{ errors.performer_id }}</div>
            </div>
        </div>
    </div>
    <div class="col-lg-9 col-md-8 col-sm-12">
        <div class="panel panel-flat">
            <div class="panel-body">
                <div class="col-lg-8 col-md-6 col-sm-12">
                    <InputComponent
                        label="Название заявки"
                        type="text"
                        min="3"
                        max="50"
                        placeholder="Введите название заявки"
                        :error="errors.name"
                        v-model:value="obj.name"
                        @change="errors.name=null"
                    ></InputComponent>
                </div>
                <div class="col-lg-4 col-md-6 col-sm-12">
                    <InputComponent
                        label="Количество исполнителей"
                        type="number"
                        min="1"
                        max="20"
                        placeholder="Количество исполнителей"
                        :error="errors.need_performers"
                        v-model:value="obj.need_performers"
                        @change="errors.need_performers=null"
                    ></InputComponent>
                </div>
                <div class="col-md-12 col-sm-12">
                    <div class="panel panel-flat">
                        <div class="panel-heading">
                            <h5 class="panel-title">Изображения</h5>
                        </div>
                        <div class="panel-body">
                            <OrderImagesComponent
                                :errors="errors"
                                :photo1="photo1"
                                :photo2="photo2"
                                :photo3="photo3"
                                :input_image_hover="input_image_hover"
                            ></OrderImagesComponent>
                        </div>
                    </div>
                    <div class="panel panel-flat">
                        <div class="panel-heading">
                            <h5 class="panel-title">Адрес</h5>
                        </div>
                        <div class="panel-body">
                            <div class="error" v-if="addressError">{{ addressError }}</div>
                            <EditOrderMapComponent
                                :incoming_latitude="obj.latitude"
                                :incoming_longitude="obj.longitude"
                                :incoming_address="obj.address"
                                :yandex_api_key="yandex_api_key"
                                @set-place-mark="setPlaceMark"
                            ></EditOrderMapComponent>
                        </div>
                    </div>
                    <div class="panel panel-flat">
                        <div class="panel-heading">
                            <h5 class="panel-title">Описание</h5>
                        </div>
                        <div class="panel-body">
                            <TextAreaComponent
                                label="Краткое описание (до 200 символов)"
                                max="200"
                                :value="obj.description_short"
                                v-model:value="obj.description_short"
                                :error="errors.description_short"
                                @change="errors.description_short=null"
                            ></TextAreaComponent>
                            <TextAreaComponent
                                label="Полное описание (до 1000 символов)"
                                max="1000"
                                :value="obj.description_full"
                                v-model:value="obj.description_full"
                                :error="errors.description_full"
                                @change="errors.description_full=null"
                            ></TextAreaComponent>
                            <ButtonComponent
                                class_name="btn btn-primary mt-3 pull-right"
                                text="Сохранить"
                                :disabled=disabledSubmit
                                @click="save"
                            ></ButtonComponent>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</template>

<script>
import UserComponent from "./UserComponent.vue";
import UserNameComponent from "../blocks/UserNameComponent.vue";
import OrderImagesComponent from "../blocks/OrderImagesComponent.vue";
import EditOrderMapComponent from "./blocks/EditOrderMapComponent.vue";

export default {
    extends: UserComponent,
    name: "OrderComponent",
    components: {
        UserComponent,
        UserNameComponent,
        OrderImagesComponent,
        EditOrderMapComponent
    },
    created() {
        let self = this;
        this.users = JSON.parse(this.incoming_users);
        this.types = JSON.parse(this.incoming_types);

        if (this.parent_id) this.obj.user_id = parseInt(this.parent_id);

        $(document).ready(function () {
            $('.order-photo .icon-close2').click(function () {
                let pos = parseInt($(this).attr('id').replace('remove-',''));
                if (self.objId) {
                    let formData = new FormData();
                    formData.append('_token', window.tokenField);
                    formData.append('id', self.objId);
                    formData.append('pos', pos);

                    axios.post(self.delete_image, formData)
                        .then(function (response) {
                            window.showMessage(response.data.message);
                        }).catch(function (error) {
                            // console.log(error);
                        });
                }
            });
        });
    },
    props: {
        'delete_image': String,
        'incoming_users': String,
        'incoming_types': String,
        'yandex_api_key': String,
        'parent_id': String|NaN
    },
    data() {
        return {
            obj: {
                user_id: 1,
                order_type_id: 1,
                subtype_id: 1,
                city_id: 1,
                name: '',
                need_performers: 1,
                address: '',
                latitude: 0,
                longitude: 0,
                description_short: '',
                description_full: '',
                status: 3,
            },
            errors: {
                performer_id: null,
                photo1: null,
                photo2: null,
                photo3: null,
            },
            users: [],
            types: {},
            typeId: 1,
            photo1: '',
            photo2: '',
            photo3: '',
            performer_id: 0,
            disabledButtons: false,
            statusValues: [
                {val: 0, description: 'Закрыта'},
                {val: 1, description: 'В работе'},
                {val: 2, description: 'Открыта'},
                {val: 3, description: 'Новая'},
            ]
        }
    },
    computed: {
        addressError() {
            if (this.errors.address || this.errors.latitude || this.errors.longitude) return 'Укажите адрес и проверьте его по api!';
            else return null;
        }
    },
    watch: {
        obj(newObj) {
            if (newObj.performers.length) this.performer_id = newObj.performers[0].id;
            for(let i=1;i<=3;i++) {
                for(let im=0;im<newObj.images.length;im++) {
                    if (i === newObj.images[im].position) {
                        this['photo' + i] = newObj.images[im].image;
                        break;
                    }
                }
            }
        },
    },
    methods: {
        changeStatusOrPerformer()
        {
            this.errors.status=null;
            this.errors.performer_id=null;

            if (!this.performer_id && this.obj.status <= 1) this.obj.status = 3;
            else if (this.obj.status === 3 && this.performer_id) this.performer_id = 0;
        },
        save() {
            let formData = this.preparingFields();
            for (let im=1;im<=3;im++){
                formData.append('photo' + im, $('input[name=photo' + im + ']')[0].files[0]);
            }
            if (this.obj.status === 1) formData.append('performer_id', this.performer_id);

            this.saving(formData);
        },
        setPlaceMark(coordinates) {
            this.obj.address = coordinates.address;
            this.obj.latitude = coordinates.latitude;
            this.obj.latitude = coordinates.longitude;
        }
    }
}
</script>
