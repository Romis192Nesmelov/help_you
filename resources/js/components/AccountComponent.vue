<template>
    <ModalComponent id="change-phone-modal" head="Изменить телефон" v-on:keyup.enter="changePhone">
        <InputComponent
            label="Телефон"
            type="text"
            name="phone"
            placeholder="+_(___)___-__-__"
            :error="errors['phone']"
        ></InputComponent>
        <InputComponent
            label="Код"
            type="text"
            name="code"
            placeholder="__-__-__"
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
        <get-code-again-component
            v-if="getCodeAgainVisible"
            :timer="getCodeAgainTimer"
        ></get-code-again-component>
        <ButtonComponent
            id="change-phone-button"
            class_name="btn btn-primary"
            :disabled=true
            icon="icon-reset"
            text="Изменить телефон"
            @click="changePhone"
        ></ButtonComponent>
    </ModalComponent>

    <ModalComponent id="change-password-modal" head="Изменить пароль" v-on:keyup.enter="changePassword">
        <InputComponent
            label="Старый пароль"
            icon="icon-eye"
            type="password"
            name="old_password"
            placeholder="Пароль"
            :error="errors['old_password']"
            v-model:value="oldPassword"
        ></InputComponent>
        <InputComponent
            label="Новый пароль"
            icon="icon-eye"
            type="password"
            name="password"
            placeholder="Новый пароль"
            :error="errors['password']"
            v-model:value="password"
        ></InputComponent>
        <InputComponent
            label="Подтверждение пароля"
            icon="icon-eye"
            type="password"
            name="password_confirmation"
            placeholder="Подтверждение пароля"
            :error="errors['password']"
            v-model:value="passwordConfirmation"
        ></InputComponent>
        <ButtonComponent
            id="change-password-button"
            class_name="btn btn-primary"
            :disabled=true
            icon="icon-file-locked2"
            text="Изменить пароль"
            @click="changePassword"
        ></ButtonComponent>
    </ModalComponent>

    <div class="col-12 col-lg-8" v-on:keyup.enter="onSubmit">
        <div class="rounded-block tall">
            <div class="row">
                <div id="account-block" class="col-12 col-lg-6">
                    <InputComponent
                        label="Ваше имя"
                        type="text"
                        name="name"
                        placeholder="Введите ваше имя"
                        :value="name"
                        :error="errors['name']"
                        v-model:value="name"
                    ></InputComponent>
                    <InputComponent
                        label="Ваша фамилия"
                        type="text"
                        name="family"
                        placeholder="Введите вашу фамилию"
                        :value="family"
                        :error="errors['family']"
                        v-model:value="family"
                    ></InputComponent>
                    <InputComponent
                        label="Дата рождения"
                        type="text"
                        name="born"
                        placeholder="__-__-____"
                        :value="born"
                        :error="errors['born']"
                    ></InputComponent>
                    <InputComponent
                        label="E-mail"
                        type="text"
                        name="email"
                        placeholder="Введите ваш E-Mail"
                        :error="errors['email']"
                        v-model:value="email"
                    ></InputComponent>
                    <CheckboxComponent
                        name="mail_notice"
                        :checked=mailNotice
                        label="Получать оповещения по E-mail"
                        v-model:checked="mailNotice"
                    ></CheckboxComponent>
                    <p id="phone-number" class="mt-3 mb-0 text-center">Телефон: {{ phone }}</p>
                    <ButtonComponent
                        class_name="btn btn-secondary w-100 mt-3"
                        target="#change-phone-modal"
                        text="Изменить телефон"
                    ></ButtonComponent>
                    <ButtonComponent
                        class_name="btn btn-secondary w-100 mt-2 mb-3"
                        target="#change-password-modal"
                        text="Изменить пароль"
                    ></ButtonComponent>
                </div>
                <div class="col-12 col-lg-6">
                    <TextAreaComponent
                        id="info-about"
                        label="Информация о себе"
                        name="info_about"
                        placeholder=""
                        :value="infoAbout"
                        :error="errors['info_about']"
                    ></TextAreaComponent>
                    <div class="w-100 text-end mt-4">
                        <ButtonComponent
                            id="account-save"
                            class_name="btn btn-primary"
                            text="Сохранить"
                            :disabled="!(name.length && family.length && born.length && email.length)"
                            @click="onSubmit"
                        ></ButtonComponent>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import ModalComponent from "./blocks/ModalComponent.vue";
import InputComponent from "./blocks/InputComponent.vue";
import TextAreaComponent from "./blocks/TextAreaComponent.vue";
import ButtonComponent from "./blocks/ButtonComponent.vue";
import AvatarComponent from "./blocks/AvatarComponent.vue";
import GetCodeAgainComponent from "./blocks/GetCodeAgainComponent.vue";
import RegisterComponent from "./RegisterComponent.vue";

export default {
    extends: RegisterComponent,
    name: "AccountComponent",
    components: {
        AvatarComponent,
        InputComponent,
        TextAreaComponent,
        ModalComponent,
        ButtonComponent,
        GetCodeAgainComponent
    },
    created() {
        let user = JSON.parse(this.user);
        this.name = user.name;
        this.family = user.family;
        this.born = user.born;
        this.phone = user.phone;
        this.email = user.email;
        this.infoAbout = user.info_about;
        this.mailNotice = user.mail_notice;
    },
    data() {
        return {
            name: String,
            family: String,
            born: String,
            phone: String,
            code: '',
            changingPhone: '',
            email: String,
            oldPassword: '',
            password: '',
            passwordConfirmation: '',
            infoAbout: String,
            mailNotice: Boolean,
            errors: {
                name: null,
                family: null,
                born: null,
                phone: null,
                code: null,
                email: null,
                info_about: null,
                old_password: null,
                password: null
            },
        }
    },
    props: {
        'user': String,
        'edit_account_url': String,
        'get_code_url': String,
        'change_phone_url': String,
        'change_password_url': String
    },
    methods: {
        getCode() {
            if (!this.getCodeAgainTimer) {
                let self = this;
                this.runTimer();

                axios.post(this.get_code_url, {
                    _token: window.tokenField,
                    phone: window.inputChangePhone
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
        changePhone() {
            let self = this;
            this.phone = window.inputChangePhone;
            this.code = window.inputChangePhoneCode;
            window.addLoader();

            axios.post(this.get_code_url, {
                _token: window.tokenField,
                phone: window.inputChangePhone,
                code:  window.inputChangePhoneCode
            })
                .then(function (response) {
                    $('#change-phone-modal').modal('hide');
                    window.showMessage(response.data.message);
                })
                .catch(function (error) {
                    $.each(error.response.data.errors, (name,error) => {
                        self.errors[name] = error[0];
                    });
                });

        },
        changePassword() {
            let self = this;

            axios.post(this.change_password_url, {
                _token: window.tokenField,
                old_password: self.oldPassword,
                password:  self.password,
                password_confirmation: self.passwordConfirmation,
            })
                .then(function (response) {
                    $('#change-password-modal').modal('hide');
                    window.showMessage(response.data.message);
                })
                .catch(function (error) {
                    $.each(error.response.data.errors, (name,error) => {
                        self.errors[name] = error[0];
                    });
                });
        },
        onSubmit() {
            let self = this;
            this.born = window.userBorn;
            window.addLoader();

            axios.post(this.edit_account_url, {
                _token: window.tokenField,
                name: self.name,
                family: self.family,
                born: self.born,
                email: self.email,
                info_about: self.info_about,
                mail_notice: self.mail_notice
            })
                .then(function (response) {
                    window.removeLoader();
                    window.showMessage(response.data.message);
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
