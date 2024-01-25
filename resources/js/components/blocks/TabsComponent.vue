<template>
    <div class="top-submenu">
        <div v-for="(name, key) in tabs" :class="'tab' + (key === activeTab ? ' active' : '')" :key="key">
            <a :id="'top-submenu-' + key" href="#" @click="changeTab(key)">{{ name }}</a>
            <sup v-if="counters">{{ counters[key] }}</sup>
        </div>
    </div>
</template>

<script>
export default {
    name: "TabsComponent",
    props: {
        'tabs': Object,
        'counters': Object|NaN,
    },
    created() {
        this.activeTab = Object.keys(this.counters)[0];
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
