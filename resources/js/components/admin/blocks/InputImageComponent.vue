<template>
    <div class="box-image">
        <div class="panel panel-flat">
            <a class="fancybox" :href="image ? '/' + image : placeholder_image">
                <img class="image" :src="image ? '/' + image : placeholder_image" />
            </a>
        </div>
        <input type="file" :name="name" class="file-styled">
        <div class="error" v-if="error">{{ error }}</div>
    </div>
</template>

<script>
export default {
    name: "InputImageComponent",
    props: {
        'name': String,
        'image': String|null,
        'placeholder_image': String,
        'error': String|null
    }
}
</script>
