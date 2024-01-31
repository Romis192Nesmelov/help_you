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
            :disabled=disabledGetCode
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
            :disabled=disabledChangePhone
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
            @change="errors['old_password']=null"
        ></InputComponent>
        <InputComponent
            label="Новый пароль"
            icon="icon-eye"
            type="password"
            name="password"
            placeholder="Новый пароль"
            :error="errors['password']"
            v-model:value="password"
            @change="errors['password']=null"
        ></InputComponent>
        <InputComponent
            label="Подтверждение пароля"
            icon="icon-eye"
            type="password"
            name="password_confirmation"
            placeholder="Подтверждение пароля"
            :error="errors['password']"
            v-model:value="passwordConfirmation"
            @change="errors['password']=null"
        ></InputComponent>
        <ButtonComponent
            id="change-password-button"
            class_name="btn btn-primary"
            :disabled="!(oldPassword.length && password.length && passwordConfirmation.length)"
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
                        @change="checkDisableSubmit"
                    ></InputComponent>
                    <InputComponent
                        label="Ваша фамилия"
                        type="text"
                        name="family"
                        placeholder="Введите вашу фамилию"
                        :value="family"
                        :error="errors['family']"
                        v-model:value="family"
                        @change="checkDisableSubmit"
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
                        @change="checkDisableSubmit"
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
                        v-model:value="infoAbout"
                        :error="errors['info_about']"
                    ></TextAreaComponent>
                    <div class="w-100 text-end mt-4">
                        <ButtonComponent
                            id="account-save"
                            class_name="btn btn-primary"
                            text="Сохранить"
                            :disabled=disabledSubmit
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
import GetCodeAgainComponent from "./blocks/GetCodeAgainComponent.vue";
import RegisterComponent from "./RegisterComponent.vue";

export default {
    extends: RegisterComponent,
    name: "AccountComponent",
    components: {
        InputComponent,
        TextAreaComponent,
        ModalComponent,
        ButtonComponent,
        GetCodeAgainComponent
    },
    created() {
        let self = this,
            user = JSON.parse(this.user);

        this.name = user.name;
        this.family = user.family;
        this.born = user.born;
        this.phone = user.phone;
        this.email = user.email;
        this.infoAbout = user.info_about;
        this.mailNotice = user.mail_notice;
        self.checkDisableSubmit();

        $.mask.definitions['c'] = "[1-2]";
        $(document).ready(function () {
            const changePhoneModal = $('#change-phone-modal'),
                phoneChangePhoneModal = changePhoneModal.find('input[name=phone]'),
                codeChangePhoneModal = changePhoneModal.find('input[name=code]'),
                accountBlock = $('#account-block'),
                userBornInput = accountBlock.find('input[name=born]');

            phoneChangePhoneModal.mask(window.phoneMask).on('blur keypress keyup change', function () {
                self.phone = $(this).val();
                self.disabledGetCode = self.phone.match(window.phoneRegExp) === null;
                self.enableChangePhoneModalButton();
                self.errors['phone'] = null;
            });

            codeChangePhoneModal.mask(window.codeMask).on('blur keypress keyup change', function () {
                self.code = $(this).val();
                self.enableChangePhoneModalButton();
                self.errors['code'] = null;
            });

            userBornInput.mask(window.bornMask).on('blur keypress keyup change', function () {
                self.born = $(this).val();
                self.checkDisableSubmit();
            });
        });
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
            disabledSubmit: true,
            disabledGetCode: true,
            disabledChangePhone: true,
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
        checkDisableSubmit() {
            let self = this;
            $.each(['name','family','born','email'],function (k,field) {
                self.errors[field] = null;
            });
            this.disabledSubmit = !(this.name.length && this.family.length && this.born.match(window.bornRegExp) && this.email.match(window.emailRegExp));
        },
        enableChangePhoneModalButton() {
            this.disabledChangePhone = this.phone.match(window.phoneRegExp) === null || this.code.match(window.codeRegExp) === null;
        },
        getCode() {
            if (!this.getCodeAgainTimer) {
                let self = this;
                this.disabledGetCode = true;

                axios.post(this.get_code_url, {
                    _token: window.tokenField,
                    phone: this.phone
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
        changePhone() {
            let self = this;
            this.disabledChangePhone = true;
            window.addLoader();

            axios.post(this.change_phone_url, {
                _token: window.tokenField,
                phone: this.phone,
                code:  this.code
            })
                .then(function (response) {
                    $('#change-phone-modal').modal('hide');
                    window.showMessage(response.data.message);
                    window.removeLoader();
                })
                .catch(function (error) {
                    $.each(error.response.data.errors, (name,error) => {
                        self.errors[name] = error[0];
                    });
                    window.removeLoader();
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
            this.disabledSubmit = true;
            window.addLoader();

            axios.post(this.edit_account_url, {
                _token: window.tokenField,
                name: self.name,
                family: self.family,
                born: self.born,
                email: self.email,
                info_about: self.infoAbout,
                mail_notice: self.mailNotice
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
