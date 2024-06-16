<template>
    <AvatarComponent
        :small=true
        :avatar_image="answer.user.avatar"
        :avatar_props="answer.user.avatar_props"
    ></AvatarComponent>
    <div class="message">
        <div class="author">
            <UserNameComponent :user="answer.user"></UserNameComponent>
            <span>{{ getDate(answer.created_at) }}</span>
        </div>
        <div>{{ answer.text }}</div>
    </div>
</template>

<script>
import AvatarComponent from "./AvatarComponent.vue";
import UserNameComponent from "./UserNameComponent.vue";

export default {
    name: "AnswerComponent",
    components: {
        AvatarComponent,
        UserNameComponent
    },
    props: {
        'answer': Object
    },
    methods: {
        getDate(createdAt) {
            return window.convertTime(createdAt);
        },
    }
}
</script>
