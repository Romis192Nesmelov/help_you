<template>
    <div class="col-lg-3 col-md-4 col-sm-12">
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
                <TextAreaComponent
                    label="Текст ответа"
                    max="3000"
                    :value="obj.text"
                    v-model:value="obj.text"
                    :error="errors.text"
                    @change="errors.text=null"
                ></TextAreaComponent>
            </div>
        </div>
        <div class="panel panel-flat">
            <div class="panel-body">
                <CheckboxComponent
                    name="read_owner"
                    label="Прочитано пользователем"
                    :checked="obj.read_owner"
                    v-model:checked="obj.read_owner"
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
    name: "AnswerComponent",
    components: {
        UserComponent,
        UserNameComponent,
        InputImageComponent
    },
    created() {
        if (!this.objId) this.obj.user_id = parseInt(this.admin_id);
        if (this.parent_id) this.obj.ticket_id = parseInt(this.parent_id);
    },
    props: {
        'admin_id': String,
        'parent_id': String|NaN,
        'placeholder_image': String
    },
    data() {
        return {
            obj: {
                user_id: 1,
                image: '',
                text: '',
                read_admin: 1,
                read_owner: 0
            },
            disabledButtons: false,
        }
    },
    methods: {
        save() {
            this.obj.read_admin = 1;
            let formData = this.preparingFields();
            formData.append('image', $('input[name=image]')[0].files[0]);
            this.saving(formData);
        }
    }
}
</script>
