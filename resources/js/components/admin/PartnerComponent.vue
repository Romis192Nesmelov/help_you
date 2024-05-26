<template>
    <div class="col-lg-2 col-md-3 col-sm-4 col-xs-12">
        <InputImageComponent
            name="logo"
            :image="'/' + obj.logo"
            :error="errors.logo"
        ></InputImageComponent>
    </div>
    <div class="col-lg-10 col-md-9 col-sm-8 col-xs-12">
        <div class="panel panel-flat">
            <div class="panel-body">
                <InputComponent
                    label="Название"
                    type="text"
                    name="name"
                    min="3"
                    max="50"
                    placeholder="Название"
                    :error="errors.name"
                    v-model:value="obj.name"
                    @change="errors.name=null"
                ></InputComponent>
                <TextAreaComponent
                    label="Описание"
                    name="about"
                    max="3000"
                    :value="obj.about"
                    v-model:value="obj.about"
                    :error="errors.about"
                    @keyup="errors.about=null"
                ></TextAreaComponent>
                <TextAreaComponent
                    label="Информация о партнере"
                    name="info"
                    max="50000"
                    :value="obj.info"
                    v-model:value="obj.info"
                    :error="errors.info"
                    @keyup="errors.info=null"
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
</template>

<script>
import UserComponent from "./UserComponent.vue";
import InputImageComponent from "./blocks/InputImageComponent.vue";

export default {
    extends: UserComponent,
    name: "PartnerComponent",
    components: {
        InputImageComponent
    },
    props: {
        'placeholder_image': String
    },
    data() {
        return {
            obj: {
                logo: '',
                name: '',
                about: '',
                info: ''
            }
        }
    },
    methods: {
        save() {
            let formData = this.preparingFields();
            this.obj.info = window.editor.getData();
            formData.append('info',this.obj.info);
            formData.append('logo', $('input[name=logo]')[0].files[0]);
            this.saving(formData);
        },
    },
}
</script>
