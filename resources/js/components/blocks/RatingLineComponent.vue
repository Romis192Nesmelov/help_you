<template>
    <div class="rating-line">
        <i class="icon-star-full2" v-for="val in 5" :key="'full'+val" v-show="(val <= rating)" @click="setRating(val)"></i>
        <i class="icon-star-empty3" v-for="val in 5" :key="'empty'+val" v-show="(val > rating)" @click="setRating(val)"></i>
    </div>
</template>

<script>
export default {
    name: "RatingLineComponent",
    created() {
        this.rating = this.income_rating;
    },
    props: {
        'income_rating': Number,
        'allow_change_rating': Boolean
    },
    emits: ['changeRating','update:value'],
    data() {
        return {
            rating: 0
        }
    },
    methods: {
        setRating(val) {
            if (this.allow_change_rating) {
                this.rating = val;
                this.$emit('update:value',this.rating);
            }
        }
    }
}
</script>
