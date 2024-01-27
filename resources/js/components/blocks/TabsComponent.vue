<template>
    <div class="top-submenu row">
        <div v-for="(option, key) in tabs" :class="'col-lg-3 col-md-6 col-sm-12' + (key === activeTab ? ' active' : '')" :key="key">
            <a :id="'top-submenu-' + key" href="#" @click="changeTab(key)">{{ option.name }}</a>
            <sup v-if="show_counters">{{ option.counter }}</sup>
        </div>
    </div>
</template>

<script>
export default {
    name: "TabsComponent",
    props: {
        'tabs': Object,
        'show_counters': Boolean,
    },
    created() {
        this.activeTab = Object.keys(this.tabs)[0];
    },
    data() {
        return {
            activeTab: String,
        }
    },
    methods: {
        changeTab(key) {
            this.activeTab = key;
            this.$emit('changeTab',key);
        }
    }
}
</script>
