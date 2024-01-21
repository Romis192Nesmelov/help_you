<template>
    <div id="get-code-again" class="w-100 text-center mb-2">Получить код повторно можно через <span>{{ timer }}</span> секунд</div>
</template>

<script>
export default {
    name: "GetCodeAgainComponent",
    props: {
        'timer': Number,
    },
}
</script>

<style scoped>

</style>
