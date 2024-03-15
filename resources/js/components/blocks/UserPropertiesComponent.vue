<template>
    <div class="d-flex align-items-center justify-content-start">
        <AvatarComponent
            :small=small
            :avatar_image="user.avatar"
            :avatar_props="user.avatar_props"
            :avatar_coof=avatar_coof
            :allow_change_avatar=0
        ></AvatarComponent>
        <div class="w-100 fs-lg-6 fs-sm-7 ms-3 d-flex flex-column align-items-start">
            <small class="text-start" v-if="small">
                <UserNameComponent :user="user"></UserNameComponent>
            </small>
            <UserNameComponent :user="user" v-else></UserNameComponent>
            <RatingLineComponent
                :income_rating="userRating"
                :allow_change_rating="allow_change_rating"
                v-if="use_rating"
            ></RatingLineComponent>
            <div class="fs-lg-6 fs-sm-7"><small>{{ getUserAge(user.born) }}</small></div>
        </div>
    </div>
</template>

<script>
import AvatarComponent from "./AvatarComponent.vue";
import RatingLineComponent from "./RatingLineComponent.vue";
import UserNameComponent from "./UserNameComponent.vue";

export default {
    name: "UserPropertiesComponent",
    created() {
        this.userRating = window.userRating(this.user.ratings);
    },
    components: {
        AvatarComponent,
        RatingLineComponent,
        UserNameComponent,
    },
    props: {
        'user': Object,
        'small': Boolean,
        'avatar_coof': Number,
        'use_rating': Boolean,
        'allow_change_rating': Boolean
    },
    data() {
        return {
            userRating: 0
        }
    },
    methods: {
        getUserAge(born) {
            return window.getUserAge(born);
        },
    }
}
</script>
