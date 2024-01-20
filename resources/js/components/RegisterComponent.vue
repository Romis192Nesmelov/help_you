<template>
    <ModalComponent id="register-modal" head="Зарегистрироваться" v-on:keyup.enter="onSubmit">
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
        <InputComponent
            label="Подтверждение пароля"
            icon="icon-eye"
            type="password"
            name="password_confirmation"
            placeholder="Подтверждение пароля"
            :value="passwordConfirmation"
            :error="errors['password']"
        ></InputComponent>
        <InputComponent
            v-if="getCodeAgainVisible"
            label="Код"
            icon=""
            type="text"
            name="code"
            placeholder="__-__-__"
            :value="code"
            :error="errors['code']"
        ></InputComponent>
        <ButtonComponent
            id="get-register-code"
            class_name="btn btn-primary"
            :disabled=true
            icon="icon-key"
            text="Получить код"
            @click="getCode"
        ></ButtonComponent>
        <div id="get-code-again" class="w-100 text-center mb-2" v-if="getCodeAgainVisible">Получить код повторно можно через <span>{{ getCodeAgainTimer }}</span> секунд</div>
        <ButtonComponent
            id="register"
            class_name="btn btn-primary"
            :disabled=true
            icon="icon-reset"
            text="Зарегистрироваться"
            @click="onSubmit"
        ></ButtonComponent>
        <CheckboxComponent
            id="i_agree"
            name="i_agree"
            :checked=iAgree
            label="Даю согласие на обработку персональных данных"
            v-model:checked="iAgree"
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
    name: "RegisterComponent",
    components: {
        ModalComponent,
        ButtonComponent,
        CheckboxComponent,
        InputComponent,
        ForgotPasswordComponent
    },
    props: {
        'register_url': String,
        'get_code_url': String
    },
    data() {
        return {
            iAgree: true,
            phone: '',
            password: '',
            passwordConfirmation: '',
            code: '',
            getCodeAgainVisible: false,
            getCodeAgainTimer: 0,
            errors: {
                phone: null,
                password: null,
                code: null
            }
        }
    },
    methods: {
        getCode() {
            if (!this.getCodeAgainTimer) {
                this.getCodeAgainVisible = true;
                this.getCodeAgainTimer = 45;

                let getRegisterCodeButton = $('#get-register-code'),
                    countDown = setInterval(() => {
                        if (!this.getCodeAgainTimer) {
                            getRegisterCodeButton.removeAttr('disabled');
                            this.getCodeAgainVisible = false;
                            clearInterval(countDown);
                        } else {
                            this.getCodeAgainTimer--;
                        }
                    }, 1000);

                getRegisterCodeButton.attr('disabled','disabled');

                axios.post(this.get_code_url, {
                    _token: window.tokenField,
                    phone: window.inputRegisterPhone,
                    password: window.inputRegisterPassword,
                    password_confirmation: window.inputRegisterConfirmPassword,
                    i_agree: this.iAgree
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
        },
        onSubmit(event) {
            let self = this;
            this.phone = window.inputRegisterPhone;
            this.password = window.inputRegisterPassword;
            this.passwordConfirmation = window.inputRegisterConfirmPassword;
            this.code = window.inputRegisterCode;
            axios.post(this.register_url, {
                _token: window.tokenField,
                phone: window.inputRegisterPhone,
                password: window.inputRegisterPassword,
                password_confirmation: window.inputRegisterConfirmPassword,
                code: this.code,
                i_agree: this.iAgree
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
