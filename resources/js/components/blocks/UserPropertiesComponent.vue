<template>
    <div class="d-flex align-items-center justify-content-start">
        <AvatarComponent
            :small=true
            :avatar_image="user.avatar"
            :avatar_props="user.avatar_props"
            :avatar_coof=avatar_coof
            :allow_change_avatar=0
        ></AvatarComponent>
        <div class="fs-lg-6 fs-sm-7 ms-3">
            <small v-if="avatar_coof === 0.2"><UserNameComponent :user="user"></UserNameComponent></small>
            <UserNameComponent :user="user" v-else></UserNameComponent>
            <RatingLineComponent
                :income_rating="userRating"
                :allow_change_rating="allow_change_rating"
            ></RatingLineComponent>
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
        'avatar_coof': Number,
        'allow_change_rating': Boolean
    },
    data() {
        return {
            userRating: 0
        }
    }
}
</script>
