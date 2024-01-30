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
            v-model:value="password"
            @keyup="enableRegisterButtons"
        ></InputComponent>
        <InputComponent
            label="Подтверждение пароля"
            icon="icon-eye"
            type="password"
            name="password_confirmation"
            placeholder="Подтверждение пароля"
            :value="passwordConfirmation"
            :error="errors['password']"
            v-model:value="passwordConfirmation"
            @keyup="enableRegisterButtons"
        ></InputComponent>
        <InputComponent
            label="Код"
            type="text"
            name="code"
            placeholder="__-__-__"
            :value="code"
            :error="errors['code']"
            @keyup="enableRegisterButtons"
        ></InputComponent>
        <ButtonComponent
            id="get-register-code"
            class_name="btn btn-primary"
            :disabled=disableGetCode
            icon="icon-key"
            text="Получить код"
            @click="getCode"
        ></ButtonComponent>
        <get-code-again-component
            v-if="getCodeAgainVisible"
            :timer="getCodeAgainTimer"
        ></get-code-again-component>
        <ButtonComponent
            id="register"
            class_name="btn btn-primary"
            :disabled=disabledSubmit
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
            @change="enableRegisterButtons"
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
import GetCodeAgainComponent from "./blocks/GetCodeAgainComponent.vue";

export default {
    name: "RegisterComponent",
    components: {
        ModalComponent,
        ButtonComponent,
        CheckboxComponent,
        InputComponent,
        ForgotPasswordComponent,
        GetCodeAgainComponent
    },
    created() {
        const self = this;
        $.mask.definitions['n'] = "[7-8]";

        $(document).ready(function () {
            const registerModal = $('#register-modal');

            registerModal.find('input[name=phone]').mask(window.phoneMask).on('blur keypress keyup change', function () {
                self.phone = $(this).val();
                self.enableRegisterButtons();
                self.errors['phone'] = null;
                self.errors['password'] = null;
            });

            registerModal.find('input[name=code]').mask(codeMask).on('blur keypress keyup change', function () {
                self.code = $(this).val();
                self.enableRegisterButtons();
                self.errors['code'] = null;
            });
        });
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
            disabledSubmit: true,
            disableGetCode: true,
            errors: {
                phone: null,
                password: null,
                code: null
            }
        }
    },
    methods: {
        runTimer() {
            this.getCodeAgainVisible = true;
            this.getCodeAgainTimer = 45;

            let countDown = setInterval(() => {
                    if (!this.getCodeAgainTimer) {
                        this.disableGetCode = false;
                        this.getCodeAgainVisible = false;
                        clearInterval(countDown);
                    } else {
                        this.getCodeAgainTimer--;
                    }
                }, 1000);
            this.disableGetCode = true;
        },
        enableRegisterButtons() {
            if (this.phone.match(window.phoneRegExp) !== null && this.password.length && this.passwordConfirmation.length && this.iAgree) {
                this.disableGetCode = this.getCodeAgainTimer > 0;
                this.disabledSubmit = !this.code.match(window.codeRegExp) || !this.iAgree;
            } else {
                this.disableGetCode = true;
                this.disabledSubmit = true;
            }
        },
        getCode() {
            if (!this.getCodeAgainTimer) {
                let self = this;
                this.disabledGetCode = true;

                axios.post(this.get_code_url, {
                    _token: window.tokenField,
                    phone: this.phone,
                    password: this.password,
                    password_confirmation: this.passwordConfirmation,
                    i_agree: this.iAgree
                })
                    .then(function (response) {
                        self.runTimer();
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
            this.disabledSubmit = true;
            window.addLoader();

            axios.post(this.register_url, {
                _token: window.tokenField,
                phone: this.phone,
                password: this.password,
                password_confirmation: this.passwordConfirmation,
                code: this.code,
                i_agree: this.iAgree
            })
                .then(function (response) {
                    window.showMessage(response.data.message);
                    $('#register-modal').modal('hide');
                    window.removeLoader();
                })
                .catch(function (error) {
                    $.each(error.response.data.errors, (name,error) => {
                        self.errors[name] = error[0];
                        window.removeLoader();
                    });
                });
        }
    }
}
</script>
