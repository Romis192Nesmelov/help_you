<template>
    <div class="col-12 col-lg-4">
        <div class="rounded-block tall left-menu">
            <div id="avatar-block">
                <div class="w-100">
                    <div class="d-flex align-items-center justify-content-start">
                        <AvatarComponent
                            :small=false
                            :avatar_image="userObj.avatar"
                            :avatar_props="userObj.avatar_props"
                            :avatar_error="errors['avatar']"
                            :change_avatar_url="change_avatar_url"
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
        this.leftMenu = JSON.parse(this.left_menu);

        let self = this;
        $.each(this.leftMenu, function (k,menu) {
            window.emitter.on(menu.id, flag => {
                self.newsFlags[menu.id] = flag;
            });
        });
    },
    data() {
        return {
            userObj: {},
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
        'left_menu': String,
        'logout_url': String,
        'change_avatar_url': String|NaN,
        'active_left_menu': String,
    },
}
</script>
