<template>
    <ModalComponent id="restore-password-modal" head="Восстановление пароля" :close_text=false>
        <InputComponent
            label="Телефон"
            icon=""
            type="text"
            name="phone"
            placeholder="+_(___)___-__-__"
            :value="phone"
            :error="errors['phone']"
        ></InputComponent>
        <ButtonComponent
            id="reset-button"
            class_name="btn btn-primary"
            :disabled=true
            icon="icon-user-plus"
            text="Восстановить пароль"
            @click="onSubmit"
        ></ButtonComponent>
        <ButtonComponent
            id="back-to-login"
            class_name="btn btn-secondary"
            toggle="modal"
            target="#login-modal"
            :disabled=false
            :dismiss=true
            icon="icon-enter3"
            text="Войти"
        ></ButtonComponent>
    </ModalComponent>
</template>

<script>
import ModalComponent from "./blocks/ModalComponent.vue";
import ButtonComponent from "./blocks/ButtonComponent.vue";
import InputComponent from "./blocks/InputComponent.vue";

export default {
    name: "RestorePasswordComponent",
    components: {ModalComponent, ButtonComponent, InputComponent},
    props: {
        'reset_pass_url': String,
    },
    data() {
        return {
            phone: '',
            errors: {
                phone: null
            }
        }
    },
    methods: {
        onSubmit(event) {
            let self = this;
            this.phone = window.inputRestorePasswordPhone;
            axios.post(this.reset_pass_url, {
                _token: window.tokenField,
                phone: window.inputRestorePasswordPhone,
            })
                .then(function (response) {
                    window.showMessage(response.data.message);
                })
                .catch(function (error) {
                    $.each(error.response.data.errors, (name,error) => {
                        self.errors[name] = error[0];
                    });
                });
        }
    }
}
</script>
