<template>
    <li :class="'nav-item '+add_class+' '+(is_active ? 'active' : '')">
        <a class="nav-link" :href="url">{{ name }}</a>
    </li>
</template>

<script>
export default {
    name: "TopMenuItemComponent",
    props: {
        'is_active': Boolean,
        'add_class': String,
        'url': String,
        'name': String
    }
}
</script>
