<template>
    <div class="col-lg-3 col-md-4 col-sm-12">
        <div class="panel panel-flat" v-if="!parent_id">
            <div class="panel-heading">
                <h5 class="panel-title">Автор обращения</h5>
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
                <h5 class="panel-title">Скриншот</h5>
            </div>
            <div class="panel-body">
                <InputImageComponent
                    name="image"
                    :placeholder_image = "placeholder_image"
                    :image="obj.image"
                    :error="errors.image"
                ></InputImageComponent>
            </div>
        </div>
    </div>
    <div class="col-lg-9 col-md-8 col-sm-12">
        <div class="panel panel-flat">
            <div class="panel-body">
                <InputComponent
                    label="Тема обращения"
                    type="text"
                    min="3"
                    max="255"
                    placeholder="Укажите тему обращения"
                    :error="errors.subject"
                    v-model:value="obj.subject"
                    @change="errors.subject=null"
                ></InputComponent>
                <TextAreaComponent
                    label="Описание проблемы"
                    max="3000"
                    :value="obj.text"
                    v-model:value="obj.text"
                    :error="errors.text"
                    @change="errors.text=null"
                ></TextAreaComponent>
            </div>
        </div>
        <div class="panel panel-flat">
            <div class="panel-heading">
                <h5 class="panel-title">Статус обращения</h5>
            </div>
            <div class="panel-body">
                <div class="form-group no-margin-bottom" v-for="(value, k) in statusValues" :key="'radio' + k">
                    <input class="order-status" :id="'status_' + value.val" :value="value.val" type="radio" v-model="obj.status">
                    <label class="ml-10 form-check-label" :for="'status_' + value.val"><small>{{ value.description }}</small></label>
                </div>
            </div>
        </div>
        <div class="panel panel-flat">
            <div class="panel-body">
                <CheckboxComponent
                    name="read_admin"
                    label="Прочитано админом"
                    :checked="obj.read_admin"
                    v-model:checked="obj.read_admin"
                ></CheckboxComponent>
                <ButtonComponent
                    class_name="btn btn-primary mt-3 pull-right"
                    text="Сохранить"
                    :disabled=disabledSubmit
                    @click="save"
                ></ButtonComponent>
            </div>
        </div>
    </div>
</template>

<script>
import UserComponent from "./UserComponent.vue";
import UserNameComponent from "../blocks/UserNameComponent.vue";
import InputImageComponent from "./blocks/InputImageComponent.vue";

export default {
    extends: UserComponent,
    name: "TicketComponent",
    components: {
        UserComponent,
        UserNameComponent,
        InputImageComponent
    },
    created() {
        if (this.incoming_users) this.users = JSON.parse(this.incoming_users);
        if (this.parent_id) this.obj.user_id = parseInt(this.parent_id);
    },
    props: {
        'incoming_users': String,
        'parent_id': String|NaN,
        'placeholder_image': String
    },
    data() {
        return {
            obj: {
                user_id: 2,
                image: '',
                subject: '',
                text: '',
                read_admin: 1,
                status: 0,
            },
            users: [],
            disabledButtons: false,
            statusValues: [
                {val: 0, description: 'Открыто'},
                {val: 1, description: 'Закрыто'},
            ]
        }
    },
    methods: {
        save() {
            let formData = this.preparingFields();
            formData.append('image', $('input[name=image]')[0].files[0]);
            this.saving(formData);
        }
    }
}
</script>
