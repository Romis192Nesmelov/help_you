<template>
    <ul class="nav navbar-nav">
        <li class="dropdown">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                <i class="icon-bubbles4"></i>
                <span class="visible-xs-inline-block position-right">Сообщения</span>
                <span v-if="notices.length" class="badge bg-warning-400">{{ notices.length }}</span>
            </a>

            <div class="dropdown-menu dropdown-content width-350">
                <div class="dropdown-content-heading">Сообщения</div>

                <ul class="media-list dropdown-content-body">
                    <li class="media" v-for="notice in notices" :key="'notice-' + notice.order.id">
                        <div class="media-left">
                            <AvatarComponent
                                :small="true"
                                :avatar_image="notice.order.user.avatar"
                                :avatar_props="notice.order.user.avatar_props"
                            ></AvatarComponent>
                        </div>
                        <div class="media-body">
                            <span class="text-semibold"><UserNameComponent :user="notice.order.user"></UserNameComponent></span>
                            <span class="media-annotation pull-right">{{ new Date(notice.order.created_at).toLocaleDateString('ru-RU') }}</span>
                            <a :href="orders_url + '?id=' + notice.order.id" class="media-heading">Создал новую заявку «{{ notice.order.name }}»</a>
                        </div>
                    </li>
                </ul>

                <div class="dropdown-content-footer">
                    <a href="#" data-popup="tooltip" title="" data-original-title="All messages"><i class="icon-menu display-block"></i></a>
                </div>
            </div>
        </li>

        <li class="dropdown dropdown-user">
            <a class="dropdown-toggle" data-toggle="dropdown">
                <AvatarComponent
                    :small="true"
                    :avatar_image="user.avatar"
                    :avatar_props="user.avatar_props"
                ></AvatarComponent>
                <UserNameComponent :user="user"></UserNameComponent><i class="caret"></i>
            </a>
            <ul class="dropdown-menu dropdown-menu-right">
                <li><a :href="logout_url"><i class="icon-switch2"></i>Выход</a></li>
            </ul>
        </li>
    </ul>
</template>

<script>
import AvatarComponent from "../../blocks/AvatarComponent.vue";
import UserNameComponent from "../../blocks/UserNameComponent.vue";

export default {
    name: "NoticeComponent",
    components: {
        AvatarComponent,
        UserNameComponent
    },
    props: {
        'incoming_notices': String,
        'incoming_user': String,
        'orders_url': String,
        'logout_url': String
    },
    created() {
        let self = this;
        this.user = JSON.parse(this.incoming_user);
        this.notices = JSON.parse(this.incoming_notices);

        window.Echo.channel('admin_order_event').listen('.admin_order', res => {
            if (res.notice === 'new_item') {
                self.notices.unshift({
                    order: res.model
                });
            } else if ( (res.notice === 'change_item' && res.model.status !== 3) || res.notice === 'del_item') {
                for (let i=0;i<self.notices.length;i++) {
                    if (self.notices[i].order.id === res.model.id) {
                        self.notices.splice(i,1);
                        break;
                    }
                }
            }
        });
    },
    data() {
        return {
            user: Object,
            notices: Object
        }
    }
}
</script>
