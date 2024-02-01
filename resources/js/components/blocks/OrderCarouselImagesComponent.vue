<template>
    <div class="images owl-carousel mt-3" v-if="images.length">
        <a v-for="image in images" class="fancybox" :key="image" :href="'/' + image.image">
            <div class="image" :style="`background-image:url(/${image.image});`"></div>
        </a>
    </div>
</template>

<script>
export default {
    name: "OrderCarouselImagesComponent",
    props: {
        'images': Object
    }
}
</script>
