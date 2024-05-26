<template>
    <div class="col-lg-4 col-md-4 col-sm-12" v-for="count in 3" :key="'order-image-' + count">
        <div :id="'photo' + count" :class="'order-photo' + (errors['photo'+count] ? ' error' : '')" :style="this['photo' + count] ? `background-image:url(/${this['photo' + count]});` : ''">
            <i class="icon-file-plus2" v-show="!this['photo' + count]"></i>
            <img class="hover-image" :src="input_image_hover" />
            <i :id="'remove-' + count" :class="'icon-close2' + (!this['photo' + count] ? ' d-none hidden' : '')"></i>
            <input type="file" :name="'photo' + count">
        </div>
    </div>
</template>

<script>
export default {
    name: "OrderImagesComponent",
    props: {
        errors: Object,
        photo1: String|NaN,
        photo2: String|NaN,
        photo3: String|NaN,
        input_image_hover: String
    }
}
</script>
