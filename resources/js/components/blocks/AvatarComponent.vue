<template>
    <ModalComponent v-if="change_avatar_url" id="tune-avatar-modal" head="Настроить аватар">
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
                :dismiss="true"
                text="Закрыть"
                @click="resetAvatar"
            ></ButtonComponent>
            <ButtonComponent
                class="btn btn-primary w-25 mt-3"
                :dismiss="true"
                text="Сохранить"
                data-bs-dismiss='modal'
                @click="saveAvatar"
            ></ButtonComponent>
        </div>
    </ModalComponent>

    <div
        :class="`avatar cir ${this.small ? 'small' : ''} ${this.avatar_error ? 'error' : ''}`"
        :style="`
            background-image:url(/${this.avatar_image ? (this.avatar_image + '?' + getRandomString()) : 'images/def_avatar.svg'});
            ${this.avatarProps && this.avatarProps['background-position-x'] ? 'background-position-x:'+(this.avatarProps['background-position-x'] * this.avatarCoof)+'px' : ''};
            ${this.avatarProps && this.avatarProps['background-position-y'] ? 'background-position-y:'+(this.avatarProps['background-position-y'] * this.avatarCoof)+'px' : ''};
            ${this.avatarProps && this.avatarProps['background-size'] ? 'background-size:'+(this.avatarProps['background-size']) : ''};
        `"
    >
        <img class="hover-image" v-if="change_avatar_url" :src="'/images/input_image_hover.svg'" />
        <input v-if="change_avatar_url" type="file" name="avatar" @change="avatarTune">
    </div>
</template>
<script>
import ModalComponent from "./ModalComponent.vue";
import ButtonComponent from "./ButtonComponent.vue";

export default {
    name: "AvatarComponent",
    components: {
        ModalComponent,
        ButtonComponent
    },
    props: {
        'small': false,
        'user_id': Number|NaN,
        'avatar_image': String|NaN,
        'avatar_props': Object|String|NaN,
        'avatar_error': String|NaN,
        'change_avatar_url': String|NaN,
    },
    created() {
        this.avatarCoof = this.small ? 0.2 : 0.35;
        this.avatarProps = (typeof this.avatar_props === 'string') ? JSON.parse(this.avatar_props) : this.avatar_props;
    },
    data() {
        return {
            defAvatar: window.defAvatar,
            avatarProps: Object,
            avatarCoof: Number,
            resizingFlag: false,
            avatarImage: Object,
            avatarSize: 0
        }
    },
    methods: {
        getRandomString() {
            let result = '';
            const characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
            const charactersLength = characters.length;
            let counter = 0;
            while (counter < 10) {
                result += characters.charAt(Math.floor(Math.random() * charactersLength));
                counter += 1;
            }
            return result;
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
                    self.resizingFlag = true;
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
        resetAvatar() {
            let self = this;
            window.avatarBlock.removeAttr('style');
            if (this.avatar_image) {
                window.avatarBlock.css('background-image','url(/'+this.avatar_image+')');
                if (this.avatarProps['background-size']) window.avatarBlock.css('background-size',this.avatarProps['background-size']);
                $.each(['background-position-x','background-position-y'],function (k, prop) {
                    if (self.avatarProps[prop]) window.avatarBlock.css(prop,(self.avatarProps[prop] * 0.35));
                });
            } else {
                window.avatarBlock.css('background-image','url('+window.defAvatar+')');
            }
        },
        saveAvatar() {
            this.saveAvatarFlag = true;

            let basePosY = 200 / 2 - this.avatarImage.height() / 2,
                avatarPosX = parseInt(this.avatarImage.css('left')),
                avatarPosY = parseInt(this.avatarImage.css('top')) + basePosY;

            this.avatarSize = this.resizingFlag ? this.avatarSize + 100 : 100;

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
            if (this.user_id) formData.append('id', this.user_id);

            window.addLoader();
            axios.post(this.change_avatar_url, formData)
                .then(function (response) {
                    window.showMessage(response.data.message);
                    window.removeLoader();
                })
                .catch(function (error) {
                    console.log(error);
                    $.each(error.response.data.errors, (name,error) => {
                        self.errors[name] = error[0];
                    });
                    window.removeLoader();
                });
        }
    }
}
</script>
