<template>
    <ModalComponent id="restore-password-modal" head="Восстановление пароля" v-on:keyup.enter="onSubmit">
        <InputComponent
            label="Телефон"
            type="text"
            name="phone"
            placeholder="+_(___)___-__-__"
            :value="phone"
            :error="errors['phone']"
        ></InputComponent>
        <ButtonComponent
            id="reset-button"
            class_name="btn btn-primary"
            :disabled="disabledSubmit"
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
    created() {
        const self = this;
        $.mask.definitions['n'] = "[7-8]";

        $(document).ready(function () {
            $('#restore-password-modal input[name=phone]').mask(window.phoneMask).on('blur keypress keyup change', function () {
                self.phone = $(this).val();
                self.disabledSubmit = self.phone.match(window.phoneRegExp) !== null;
                self.errors['phone'] = null;
            });
        });
    },
    data() {
        return {
            phone: '',
            disabledSubmit: true,
            errors: {
                phone: null
            }
        }
    },
    methods: {
        onSubmit(event) {
            let self = this;
            this.phone = window.inputRestorePasswordPhone;
            this.disabledSubmit = true;
            window.addLoader();

            axios.post(this.reset_pass_url, {
                _token: window.tokenField,
                phone: window.inputRestorePasswordPhone,
            })
                .then(function (response) {
                    window.showMessage(response.data.message);
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
