<template>
    <div class="panel panel-flat">
        <a class="fancybox" :href="image">
            <img class="image" :src="image" />
        </a>
    </div>
    <input
        type="file"
        :name="name"
        class="file-styled"
    >
    <div class="error" v-if="error">{{ error }}</div>
</template>

<script>
export default {
    name: "InputImageComponent",
    props: {
        'name': String,
        'image': String,
        'error': String|null
    }
}
</script>
