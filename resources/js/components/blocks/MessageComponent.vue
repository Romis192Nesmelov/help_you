<template>
    <div class="message-block">
        <AvatarComponent
            :small=true
            :avatar_image="message.user.avatar"
            :avatar_props="message.user.avatar_props"
        ></AvatarComponent>
        <div class="message">
            <div class="author">
                <UserNameComponent :user="message.user"></UserNameComponent>
                <span>{{ getMessageTime(message.created_at) }}</span>
            </div>
            <div>{{ message.body }}</div>
        </div>
    </div>
</template>

<script>
import AvatarComponent from "./AvatarComponent.vue";
import UserNameComponent from "./UserNameComponent.vue";

export default {
    name: "MessageComponent",
    components: {
        UserNameComponent,
        AvatarComponent
    },
    props: {
        'message': Object
    },
    methods: {
        getMessageTime(createdAt) {
            let date = new Date(createdAt),
                minutes = date.getMinutes().toString();

            minutes = minutes.length === 1 ? '0' + minutes : minutes;
            return date.getHours() + ':' + minutes
        }
    }
}
</script>
