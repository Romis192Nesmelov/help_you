<template>
    <div>
        <span class="hidden-sm">Показывать по:</span>
        <select class="select-show-by" v-model="showBy">
            <option v-for="showCase in showCases" :key="'show-by-' + showCase" :value="showCase">{{ showCase }}</option>
        </select>
    </div>
</template>

<script>
export default {
    name: "ShowCaseComponent",
    created() {
        let self = this;
        $(document).ready(function () {
            $('.select-show-by').select2({
                minimumResultsForSearch: Infinity,
                width: 80
            }).change(function () {
                self.showBy = $(this).val();
                self.$emit('changeShowCase',self.showBy);
            });
        });
    },
    data() {
        return {
            showCases: [5,10,20,30,50],
            showBy: 10,
        }
    },
    emits: ['changeShowCase'],
}
</script>
