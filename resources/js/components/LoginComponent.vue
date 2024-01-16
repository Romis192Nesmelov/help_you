<template>
    <InputComponent
        :label="lphone"
        icon=""
        type="text"
        name="phone"
        placeholder="+_(___)___-__-__"
        value=""
        error=""
    ></InputComponent>
    <InputComponent
        :label="lpassword"
        icon="icon-eye"
        type="password"
        name="password"
        :placeholder="lpassword"
        value=""
        error=""
    ></InputComponent>
    <ButtonComponent
        id="enter"
        class="btn btn-primary"
        :data-bs-toggle=false
        :data-bs-target=false
        :disabled=true
        :data-bs-dismiss=false
        :data-dismiss=false
        icon="icon-enter3"
        :btext="entertext"
        @click="onSubmit"
    ></ButtonComponent>
    <ButtonComponent
        id="register"
        class="btn btn-primary"
        data-bs-toggle="modal"
        data-bs-target="register-modal"
        :disabled=false
        data-bs-dismiss="modal"
        data-dismiss="modal"
        icon="icon-user-plus"
        :btext="registertext"
    ></ButtonComponent>
    <CheckboxComponent
        id="remember_me"
        name="remember"
        :checked=rememberMe
        :label="lcheckbox"
        v-model:checked="rememberMe"
    ></CheckboxComponent>
</template>

<script>
import InputComponent from "./blocks/InputComponent.vue";
import CheckboxComponent from "./blocks/CheckboxComponent.vue";
import ButtonComponent from "./blocks/ButtonComponent.vue";
export default {name: "LoginComponent",
    components: {ButtonComponent, CheckboxComponent, InputComponent},
    props: ['url', 'lphone', 'lpassword', 'lcheckbox', 'lenter', 'lregister','entertext','registertext'],
    data() {
        return {
            rememberMe:false,
        }
    },
    methods: {
        validationPassword(event) {
            this.passwordVal = event.target.value;
            this.disableSubmit = !(this.passwordVal.length && window.window.inputPhone);
        },
        onSubmit(event) {
            console.log(window.inputPhone + ' – ' + window.inputPassword + ' – ' + this.rememberMe);
            // axios.post(this.url, {
            //     _token: window.tokenField,
            //     phone: window.inputPhone,
            //     password: window.inputPassword,
            //     remember: this.rememberMe
            // })
            // .then(function (response) {
            //     console.log(response);
            // })
            // .catch(function (error) {
            //     console.log(error.response.data.errors);
            // });
        }
    }
}
</script>
