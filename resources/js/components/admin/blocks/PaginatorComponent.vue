<template>
    <ul class="pagination" v-if="links.length > 3">
        <li :class="link.active ? 'active' : ''" v-for="(link, key) in links" :key="'paginate-'+key" @click="$emit('paginate',link.url)">
            <span v-if="link.label.indexOf('Пред') === 8">‹</span>
            <span v-else-if="link.label.indexOf('След') === 0">›</span>
            <span v-else>{{ link.label }}</span>
        </li>
    </ul>
</template>

<script>
export default {
    name: "PaginatorComponent",
    props: {
        'links': Array
    },
    emits: ['paginate'],
}
</script>
