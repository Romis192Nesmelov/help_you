<template>
    <img class="account-icon" :src="icon" />
</template>

<script>
export default {
    name: "AccountIconComponent",
    props: {
        'icon': String
    }
}
</script>
