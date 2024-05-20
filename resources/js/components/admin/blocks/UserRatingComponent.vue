<template>
    <RatingLineComponent
        :income_rating="userRating"
        :allow_change_rating="false"
    ></RatingLineComponent>
</template>

<script>
import RatingLineComponent from "../../blocks/RatingLineComponent.vue";

export default {
    name: "UserRatingComponent",
    created() {
        this.userRating = window.userRating(JSON.parse(this.ratings));
    },
    components: {
        RatingLineComponent
    },
    props: {
        'ratings': String
    },
    data() {
        return {
            userRating: 0
        }
    }
}
</script>
