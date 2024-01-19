<template>
    <ModalComponent id="login-modal" head="Вход/регистрация" :close_text=false v-on:keyup.enter="onSubmit">
        <InputComponent
            label="Телефон"
            icon=""
            type="text"
            name="phone"
            placeholder="+_(___)___-__-__"
            :value="phone"
            :error="errors['phone']"
        ></InputComponent>
        <InputComponent
            label="Пароль"
            icon="icon-eye"
            type="password"
            name="password"
            placeholder="Пароль"
            :value="password"
            :error="errors['password']"
        ></InputComponent>
        <ButtonComponent
            id="enter"
            class_name="btn btn-primary"
            :disabled=true
            icon="icon-enter3"
            text="Войти"
            @click="onSubmit"
        ></ButtonComponent>
        <ButtonComponent
            id="register"
            class_name="btn btn-secondary"
            target="#register-modal"
            :disabled=false
            :dismiss=true
            icon="icon-user-plus"
            text="Зарегистрироваться"
        ></ButtonComponent>
        <CheckboxComponent
            id="remember_me"
            name="remember"
            :checked=rememberMe
            label="Запомнить меня"
            v-model:checked="rememberMe"
        ></CheckboxComponent>
        <ForgotPasswordComponent></ForgotPasswordComponent>
    </ModalComponent>
</template>

<script>
import ModalComponent from "./blocks/ModalComponent.vue";
import InputComponent from "./blocks/InputComponent.vue";
import CheckboxComponent from "./blocks/CheckboxComponent.vue";
import ButtonComponent from "./blocks/ButtonComponent.vue";
import ForgotPasswordComponent from "./blocks/ForgotPasswordComponent.vue";

export default {
    name: "LoginComponent",
    components: {ModalComponent, ButtonComponent, CheckboxComponent, InputComponent, ForgotPasswordComponent},
    props: {
        'login_url': String,
    },
    data() {
        return {
            rememberMe: false,
            phone: '',
            password: '',
            errors: {
                phone: null,
                password: null
            }
        }
    },
    emits: ['loggedIn'],
    methods: {
        onSubmit(event) {
            let self = this;
            this.phone = window.inputLoginPhone;
            this.password = window.inputLoginPassword;

            axios.post(this.login_url, {
                _token: window.tokenField,
                phone: window.inputLoginPhone,
                password: window.inputLoginPassword,
                remember: this.rememberMe
            })
                .then(function (response) {
                    $('#login-modal').modal('hide');
                    self.$emit('loggedIn');
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
