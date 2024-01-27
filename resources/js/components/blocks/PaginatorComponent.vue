<template>
    <div class="w-100 d-flex justify-content-start mt-4 ps-4" v-if="links.length > 3">
        <div :class="'paginate-button ' + (link.active ? 'active' : '')" v-for="(link, key) in links" :key="'paginate-'+key" @click="$emit('paginate',{'key':tab_key,'url':link.url})">
            <i class="icon-arrow-left7" v-if="~link.label.indexOf('Пред')"></i>
            <i class="icon-arrow-right7" v-else-if="~link.label.indexOf('След')"></i>
            <span v-else>{{ link.label }}</span>
        </div>
    </div>
</template>

<script>
export default {
    name: "PaginatorComponent",
    props: {
        'links': Array,
        'tab_key': String
    },
    emits: ['paginate'],
}
</script>
