<template>
    <span>{{ user.name ? user.name + ' ' + user.family : user.phone }}</span>
</template>

<script>
export default {
    name: "UserNameComponent",
    props: {
        'user': Object
    }
}
</script>
