<template>
    <ModalComponent id="tune-avatar-modal" head="Настроить аватар" v-if="allowChangeAvatar">
        <div class="w-100 d-flex align-items-center justify-content-center">
            <div class="avatar cir big">
                <div id="avatar-container"></div>
            </div>
        </div>
        <div class="w-100 d-flex justify-content-center">
            <div class="w-75 mt-3 ui-slider-value"></div>
        </div>
        <div class="modal-footer justify-content-center">
            <ButtonComponent
                class="btn btn-primary w-25 mt-3"
                data-bs-dismiss='modal'
                text="Закрыть"
                @click="resetAvatar"
            ></ButtonComponent>
            <ButtonComponent
                class="btn btn-primary w-25 mt-3"
                text="Сохранить"
                data-bs-dismiss='modal'
                @click="saveAvatar"
            ></ButtonComponent>
        </div>
    </ModalComponent>

    <div class="col-12 col-lg-4">
        <div class="rounded-block tall left-menu">
            <div id="avatar-block">
                <div class="w-100">
                    <div class="d-flex align-items-center justify-content-start">
                        <AvatarComponent
                            :small=false
                            :avatar_image="userObj.avatar"
                            :avatar_props="userObj.avatar_props"
                            :avatar_coof=0.35
                            :avatar_error="errors['avatar']"
                            :allow_change_avatar="allowChangeAvatar"
                            :input_image_hover="input_image_hover"
                            @onload-avatar="avatarTune"
                        ></AvatarComponent>
                        <div class="fs-lg-6 fs-sm-7 ms-3">Добро пожаловать,<br><UserNameComponent :user="userObj"></UserNameComponent>!</div>
                        <div class="error" v-if="errors['avatar']">{{ errors['avatar'] }}</div>
                    </div>
                </div>
            </div>

            <ul class="menu">
                <li v-for="menu in leftMenu" :key="menu.id" :class="(menu.key === active_left_menu ? 'active' : '')">
                    <a :href="menu.url"><i :class="menu.icon"></i>{{ menu.name }}</a>
                    <span class="dot" v-if="newsFlags[menu.id]"></span>
                </li>
            </ul>
            <div class="bottom-block">
                <div class="d-flex align-items-center justify-content-left">
                    <a :href="logout_url"><i class="icon-exit3 me-1"></i>Выйти</a>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import AvatarComponent from "./blocks/AvatarComponent.vue";
import UserNameComponent from "./blocks/UserNameComponent.vue";
import ModalComponent from "./blocks/ModalComponent.vue";
import ButtonComponent from "./blocks/ButtonComponent.vue";
export default {
    name: "LeftMenuComponent",
    components: {
        AvatarComponent,
        UserNameComponent,
        ModalComponent,
        ButtonComponent
    },
    created() {
        this.userObj = JSON.parse(this.user);
        this.allowChangeAvatar = parseInt(this.allow_change_avatar);
        this.leftMenu = JSON.parse(this.left_menu);

        let self = this;
        $.each(this.leftMenu, function (k,menu) {
            window.emitter.on(menu.id, flag => {
                self.newsFlags[menu.id] = flag;
            });
        });
        this.avatarSize = 0;
    },
    data() {
        return {
            userObj: {},
            allowChangeAvatar: false,
            avatarImage: Object,
            avatarSize: Number,
            avatarPosX: Number,
            avatarPosY: Number,
            leftMenu: Array,
            saveAvatarFlag: false,
            errors: {
                avatar: null
            },
            newsFlags: {
                'my-messages': false,
                'my-subscriptions': false,
                'my-orders': false,
                'my-help': false,
                'incentives': false
            },
        }
    },
    props: {
        'user': String,
        'allow_change_avatar': String,
        'input_image_hover': String|NaN,
        'left_menu': String,
        'logout_url': String,
        'change_avatar_url': String|NaN,
        'active_left_menu': String,
    },
    methods: {
        resetAvatar() {
            let self = this;
            window.avatarBlock.removeAttr('style');
            if (this.userObj.avatar) {
                window.avatarBlock.css('background-image','url(/'+this.userObj.avatar+')');
                if (self.userObj.avatar_props['background-size']) window.avatarBlock.css('background-size',this.userObj.avatar_props['background-size']);
                $.each(['background-position-x','background-position-y'],function (k, prop) {
                    if (self.userObj.avatar_props[prop]) window.avatarBlock.css(prop,(self.userObj.avatar_props[prop] * 0.35));
                });
            } else {
                window.avatarBlock.css('background-image','url('+window.defAvatar+')');
            }
        },
        avatarTune() {
            const self = this,
                tuneAvatarModal = $('#tune-avatar-modal'),
                avatarCirBig = $('.avatar.cir.big'),
                avatarContainer = $('#avatar-container');
            let avatarRatio = 0;

            $(".ui-slider-value").slider({
                value: 0,
                min: -100,
                max: 100,
                slide: function (event, ui) {
                    self.avatarSize = ui.value;
                    avatarContainer.css({
                        'justify-content': 'start',
                        'align-items': 'start'
                    });

                    if (!avatarRatio) avatarRatio = self.avatarImage.width() / self.avatarImage.height();
                    let avatarWidth = 200 + ui.value * 2,
                        avatarHeight = avatarWidth / avatarRatio;

                    self.avatarImage.css({
                        'width': avatarWidth,
                        'height': avatarHeight
                    });
                    self.avatarImage.css({
                        'top': (200 - self.avatarImage.height()) / 2 + 150,
                        'left': (200 - self.avatarImage.width()) / 2 + 150
                    });
                }
            });

            window.avatarBlock.on('onload_image', function (event, target) {
                $(this).css({
                    'background-size': '100%',
                    'background-position-x': 'center',
                    'background-position-y': 'center',
                });

                self.avatarImage = $('<img />').attr({
                    'id': 'tuning-avatar',
                    'src': target
                }).css('width',avatarCirBig.width());

                avatarContainer.html('');
                avatarContainer.append(self.avatarImage);
                self.avatarImage.draggable({
                    containment: "#avatar-container"
                });
                tuneAvatarModal.modal('show').on('hidden.bs.modal', () => {
                    if (!self.saveAvatarFlag) self.resetAvatar();
                    else self.saveAvatarFlag = false;
                });
            });
        },
        saveAvatar() {
            this.saveAvatarFlag = true;

            let basePosY = 200 / 2 - this.avatarImage.height() / 2,
                avatarPosX = parseInt(this.avatarImage.css('left')),
                avatarPosY = parseInt(this.avatarImage.css('top')) + basePosY;

            this.avatarSize += this.avatarSize !== 100 ? 100 : 0;

            if (this.avatarSize !== 100) {
                avatarPosX -= 150;
                avatarPosY -= 150 + basePosY;
            }

            window.avatarBlock.css({
                'background-position-x': avatarPosX * 0.35,
                'background-position-y': avatarPosY * 0.35,
                'background-size': this.avatarSize + '%'
            });

            let self = this,
                formData = new FormData();

            formData.append('_token', window.tokenField);
            formData.append('avatar', $('input[name=avatar]')[0].files[0]);
            formData.append('avatar_size', this.avatarSize);
            formData.append('avatar_position_x', avatarPosX);
            formData.append('avatar_position_y', avatarPosY);

            window.addLoader();
            axios.post(this.change_avatar_url, formData)
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
