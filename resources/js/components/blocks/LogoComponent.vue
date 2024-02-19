<template>
    <img class="logo" :src="image" />
</template>

<script>
export default {
    name: "LogoComponent",
    props: {
        'image': String
    }
}
</script>
