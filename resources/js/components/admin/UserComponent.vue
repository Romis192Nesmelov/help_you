<template>
    <div class="col-lg-2 col-md-3 col-sm-4 col-xs-12">
        <div class="panel panel-flat">
            <div class="panel-body display-flex">
                <div v-if="objId">
                    <AvatarComponent
                        :user_id="obj.id"
                        :avatar_image="obj.avatar"
                        :avatar_props="obj.avatar_props"
                        :change_avatar_url="change_avatar_url"
                    ></AvatarComponent>
                    <RatingLineComponent
                        :income_rating="rating"
                        :allow_change_rating="false"
                    ></RatingLineComponent>
                </div>
                <div v-else>
                    <div class="avatar cir" :style="'background-image: url(' + def_avatar + ')'">
                        <img :src="input_image_hover" />
                        <input type="file" name="avatar">
                    </div>
                </div>
            </div>
        </div>
        <div class="panel panel-flat">
            <div class="panel-body">
                <CheckboxComponent
                    name="mail_notice"
                    label="Оповещения по E-mail"
                    :checked="obj.mail_notice"
                    v-model:checked="obj.mail_notice"
                ></CheckboxComponent>
                <CheckboxComponent
                    name="admin"
                    label="Пользователь админ"
                    :checked="obj.admin"
                    v-model:checked="obj.admin"
                ></CheckboxComponent>
                <CheckboxComponent
                    name="active"
                    label="Активирован"
                    :checked="obj.active"
                    v-model:checked="obj.active"
                ></CheckboxComponent>
            </div>
        </div>
    </div>
    <div class="col-lg-10 col-md-9 col-sm-8 col-xs-12">
        <div class="panel panel-flat">
            <div class="panel-body">
                <InputComponent
                    label="Имя пользователя"
                    type="text"
                    name="name"
                    min="3"
                    max="255"
                    placeholder="Имя пользователя"
                    :error="errors.name"
                    v-model:value="obj.name"
                    @change="errors.name=null"
                ></InputComponent>
                <InputComponent
                    label="Фамилия пользователя"
                    type="text"
                    name="family"
                    min="3"
                    max="255"
                    placeholder="Фамилия пользователя"
                    :error="errors.family"
                    v-model:value="obj.family"
                    @change="errors.name=null"
                ></InputComponent>
                <InputComponent
                    label="Дата рождения"
                    type="text"
                    name="born"
                    max="10"
                    placeholder="__-__-____"
                    :error="errors.born"
                    v-model:value="obj.born"
                    @change="errors.born=null"
                ></InputComponent>
                <InputComponent
                    label="Телефон"
                    type="text"
                    name="phone"
                    max="16"
                    placeholder="+7(___)___-__-__"
                    :error="errors.phone"
                    v-model:value="obj.phone"
                    @change="errors.phone=null"
                ></InputComponent>
                <InputComponent
                    label="E-mail"
                    type="email"
                    name="email"
                    max="100"
                    placeholder="E-mail"
                    :error="errors.email"
                    v-model:value="obj.email"
                    @change="errors.email=null"
                ></InputComponent>
                <div class="panel panel-flat">
                    <div class="panel-heading" v-if="objId">
                        <h4 class="text-grey-300">Если вы не хотите менять пароль, оставьте эти поля пустыми</h4>
                    </div>
                    <div class="panel-body">
                        <InputComponent
                            label="Пароль"
                            type="password"
                            name="password"
                            max="50"
                            placeholder="Пароль"
                            :error="passwordErr"
                            v-model:value="password"
                            @change="passwordErr=null"
                        ></InputComponent>
                        <InputComponent
                            label="Подтверждение пароля"
                            type="password"
                            name="password_confirmation"
                            max="50"
                            placeholder="Подтверждение пароля"
                            :error="passwordConfirmErr"
                            v-model:value="passwordConfirm"
                            @change="passwordConfirmErr=null"
                        ></InputComponent>
                    </div>
                </div>
                <TextAreaComponent
                    label="Информация о пользователе"
                    name="info_about"
                    max="5000"
                    :value="obj.info_about"
                    v-model:value="obj.info_about"
                    :error="errors.info_about"
                    @change="errors.info_about=null"
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
import InputComponent from "../blocks/InputComponent.vue";
import TextAreaComponent from "../blocks/TextAreaComponent.vue";
import ButtonComponent from "../blocks/ButtonComponent.vue";
import AvatarComponent from "../blocks/AvatarComponent.vue";
import RatingLineComponent from "../blocks/RatingLineComponent.vue";
import CheckboxComponent from "./blocks/CheckboxComponent.vue";

export default {
    name: "UserComponent",
    components: {
        InputComponent,
        TextAreaComponent,
        AvatarComponent,
        ButtonComponent,
        RatingLineComponent,
        CheckboxComponent
    },
    created() {
        let self = this;
        if (this.incoming_obj) this.obj = JSON.parse(this.incoming_obj);
        if (this.obj.id) {
            this.objId = this.obj.id;
            this.skipFields.push('avatar');
        }
        if (this.objId) this.rating = window.userRating(this.rating);

        $.each(this.obj, function (field) {
            if (self.skipFields.indexOf(field) === -1) self.errors[field] = null;
        });

        if (this.broadcast_on) {
            window.Echo.private(this.broadcast_on).listen('.' + this.broadcast_as, res => {
                self.broadcasting(res);
            });
        }

        if (this.channel_on) {
            window.Echo.channel(this.channel_on).listen('.' + this.channel_as, res => {
                self.broadcasting(res);
            });
        }
    },
    props: {
        'incoming_obj': String|NaN,
        'edit_url': String,
        'back_url': String,
        'change_avatar_url': String|NaN,
        'def_avatar': String|NaN,
        'input_image_hover': String|NaN,
        'broadcast_on': String|NaN,
        'broadcast_as': String|NaN,
        'channel_on': String|NaN,
        'channel_as': String|NaN,
    },
    data() {
        return {
            objId: null,
            password: null,
            passwordConfirm: null,
            obj: {
                id: 0,
                avatar: null,
                name: '',
                family: '',
                born: '',
                phone: '',
                email: '',
                password: '',
                info_about: '',
                mail_notice: false,
                admin: false,
                active: true
            },
            rating: 0,
            errors: {},
            passwordErr: null,
            passwordConfirmErr: null,
            skipFields: ['id','avatar_props','updated_at','code','created_at'],
            disabledSubmit: false
        }
    },
    methods: {
        broadcasting(res) {
            let self = this;
            if (res.notice === 'del_item' && res.model.id === this.objId) {
                window.location.href = this.back_url;
            } else if (res.notice === 'change_item' && res.model.id === this.objId) {
                this.obj = res.model;

                $.each(['mail_notice','admin','active','status'], function (k, field){
                    let input = $('input[name=' + field + ']');
                    if (input.length) input.prop('checked', self.obj[field]);
                });
                $.uniform.update();

                let timeStartField = $('input[name=start]'),
                    timeEndField = $('input[name=end]');

                if (timeStartField.length && timeEndField.length) {
                    let newStart = window.convertTime(res.model.start),
                        newEnd = window.convertTime(res.model.end);

                    timeStartField.val(newStart);
                    timeEndField.val(newEnd);
                }
            }
        },
        preparingFields() {
            let self = this,
                formData = new FormData();

            this.disabledSubmit = true;

            formData.append('_token', window.tokenField);
            if (this.objId) formData.append('id', this.objId);

            $.each(this.errors, function (field) {
                // console.log(field,self.obj[field]);
                if (
                    (self.obj[field] === false || self.obj[field] === null) &&
                    (['mail_notice','admin','active','status','read_admin','read_owner'].indexOf(field) !== -1)
                ) formData.append(field, 0);
                else if (self.obj[field] === true) formData.append(field, 1);
                else if (self.obj[field] !== null) formData.append(field, self.obj[field]);
            });
            return formData;
        },
        save() {
            let formData = this.preparingFields();

            formData.append('born', $('input[name=born]').val());
            formData.append('phone', $('input[name=phone]').val());

            if (this.password) {
                formData.append('password', this.password);
                formData.append('password_confirmation', this.passwordConfirm);
            }
            this.saving(formData);
        },
        saving(formData) {
            let self = this;
            axios.post(this.edit_url, formData)
                .then(function (response) {
                    // console.log(response);
                    window.showMessage(response.data.message);
                    self.disabledSubmit = false;
                })
                .catch(function (error) {
                    // console.log(error);
                    $.each(error.response.data.errors, (name,error) => {
                        if (name === 'password') self.passwordErr = error[0];
                        else if (name === 'password_confirmation') self.passwordConfirmErr = error[0];
                        else self.errors[name] = error[0];
                        self.disabledSubmit = false;
                    });
                });
        }
    }
}
</script>
