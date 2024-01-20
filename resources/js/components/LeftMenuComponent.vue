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
        <div class="modal-footer">
            <ButtonComponent
                class="btn btn-primary w-50 m-auto mt-3"
                data-bs-dismiss='modal'
                text="Закрыть"
            ></ButtonComponent>
            <ButtonComponent
                class="btn btn-primary w-50 m-auto mt-3"
                text="Сохранить"
                data-bs-dismiss='modal'
                @click="saveAvatar"
            ></ButtonComponent>
        </div>
    </ModalComponent>

    <div class="col-12 col-lg-4">
        <div class="rounded-block tall">
            <div id="avatar-block">
                <div class="w-100">
                    <div class="d-flex align-items-center justify-content-start">
                        <AvatarComponent
                            :avatar_image="userObj.avatar"
                            :avatar_props="userObj.avatar_props"
                            :avatar_coof=0.35
                            :avatar_error="errors['avatar']"
                            :allow_change_avatar="allowChangeAvatar"
                            :input_image_hover="input_image_hover"
                            @onload-avatar="avatarTune"
                        ></AvatarComponent>
                        <div class="fs-lg-6 fs-sm-7 ms-3">Добро пожаловать,<br>{{ userObj.name ? userObj.name + ' ' + userObj.family : userObj.phone }}!</div>
                        <div class="error" v-if="errors['avatar']">{{ errors['avatar'] }}</div>
                    </div>
                </div>
            </div>

            <ul class="menu">
                <li v-for="menu in leftMenu" :key="menu.id" :class="(menu.key === active_left_menu ? 'active' : '')">
                    <a href="{{ menu.url }}"><i class="{{ menu.icon }}"></i>{{ menu.name }}</a>
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
import ModalComponent from "./blocks/ModalComponent.vue";
import ButtonComponent from "./blocks/ButtonComponent.vue";
export default {
    name: "LeftMenuComponent",
    components: {
        AvatarComponent,
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
        // console.log(self.newsFlags);
    },
    data() {
        return {
            userObj: {},
            allowChangeAvatar: false,
            avatarImage: Object,
            avatarSize: Number,
            avatarHeight: Number,
            avatarPosX: Number,
            avatarPosY: Number,
            avatarBlock: Object,
            leftMenu: Array,
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
        'edit_account_url': String|NaN,
        'active_left_menu': String,
    },
    methods: {
        avatarTune() {
            const self = this,
                tuneAvatarModal = $('#tune-avatar-modal'),
                avatarCirBig = $('.avatar.cir.big'),
                avatarContainer = $('#avatar-container');

            this.avatarBlock = $('#avatar-block .avatar.cir');

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

                    if (!self.avatarHeight) self.avatarHeight = self.avatarImage.height();
                    self.avatarImage.css({
                        'width': 200 + ui.value * 2,
                        'height': self.avatarHeight + (self.avatarHeight / 100 * ui.value)
                    });
                    self.avatarImage.css({
                        'top': (200 - self.avatarImage.height()) / 2 + 150,
                        'left': (200 - self.avatarImage.width()) / 2 + 150
                    });
                }
            });

            this.avatarBlock.on('onload_image', function (event, target) {
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
                // window.avatarImage.css('top',(200 - window.avatarImage.height()) / 2 + 150);
                // window.avatarHeight = window.avatarImage.height();
                self.avatarImage.draggable({
                    containment: "#avatar-container"
                });
                tuneAvatarModal.modal('show');
            });
        },
        saveAvatar() {
            let basePosY = 200 / 2 - this.avatarImage.height() / 2,
                avatarPosX = parseInt(this.avatarImage.css('left')),
                avatarPosY = parseInt(this.avatarImage.css('top')) + basePosY;
            this.avatarSize += 100;

            if (this.avatarSize !== 100) {
                avatarPosX -= 150;
                avatarPosY -= 150 + basePosY;
            }

            this.avatarBlock.css({
                'background-position-x': avatarPosX * 0.35,
                'background-position-y': avatarPosY * 0.35,
                'background-size': this.avatarSize + '%'
            });

            window.emitter.emit('changed-avatar', {
                'avatar': $('input[name=avatar]')[0].files[0],
                'avatar_size': this.avatarSize,
                'avatar_position_x': avatarPosX,
                'avatar_position_y': avatarPosY
            });
        }
    }
}
</script>
