<template>
    <InputComponent
        label="Ваше имя"
        type="text"
        name="name"
        placeholder="Введите ваше имя"
        :value="name"
        :error="errors['name']"
        v-model:value="name"
        @change="checkDisableSubmit"
    ></InputComponent>
    <InputComponent
        label="E-mail"
        type="text"
        name="email"
        placeholder="Введите ваш E-Mail"
        :error="errors['email']"
        v-model:value="email"
        @change="checkDisableSubmit"
    ></InputComponent>
    <TextAreaComponent
        class="mt-2"
        label="Ваше сообщение"
        name="message"
        placeholder="Введите ваше сообщение"
        min="10"
        max="3000"
        :value="text"
        v-model:value="text"
        :error="errors['text']"
        @change="checkDisableSubmit"
    ></TextAreaComponent>
    <CheckboxComponent
        name="i_agree"
        :checked=i_agree
        label="Даю согласие на обработку персональных данных"
        v-model:checked="i_agree"
        @change="checkDisableSubmit"
    ></CheckboxComponent>
    <ButtonComponent
        class_name="btn btn-primary mt-3 pull-right"
        text="Отправить"
        :disabled=disabledSubmit
        @click="feedback"
    ></ButtonComponent>
</template>

<script>
import ModalComponent from "./blocks/ModalComponent.vue";
import InputComponent from "./blocks/InputComponent.vue";
import TextAreaComponent from "./blocks/TextAreaComponent.vue";
import CheckboxComponent from "./blocks/CheckboxComponent.vue";
import ButtonComponent from "./blocks/ButtonComponent.vue";

export default {
    name: "FeedbackComponent",
    components: {
        InputComponent,
        TextAreaComponent,
        ModalComponent,
        CheckboxComponent,
        ButtonComponent,
    },
    data() {
        return {
            name: '',
            email: '',
            text: '',
            i_agree: false,
            disabledSubmit: true,
            errors: {
                name: null,
                email: null,
                text: null
            },
        }
    },
    props: {
        'feedback_url': String
    },
    methods: {
        checkDisableSubmit() {
            let self = this;
            $.each(['name','email','message'],function (k,field) {
                self.errors[field] = null;
            });
            this.disabledSubmit = !this.name.length || !this.email.length || !this.text.length || !this.i_agree;
        },
        feedback() {
            let self = this;
            window.addLoader();

            axios.post(this.feedback_url, {
                _token: window.tokenField,
                name: this.name,
                email:  this.email,
                text: this.text,
                i_agree: this.i_agree
            })
                .then(function (response) {
                    window.showMessage(response.data.message);
                    self.name = '';
                    self.email = '';
                    self.text = '';
                    window.removeLoader();
                })
                .catch(function (error) {
                    $.each(error.response.data.errors, (name,error) => {
                        self.errors[name] = error[0];
                    });
                    window.removeLoader();
                });
        }
    }
}
</script>
<style scoped>
    textarea {
        height: 500px !important;
    }
</style>
