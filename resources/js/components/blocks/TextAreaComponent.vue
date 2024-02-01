<template>
    <div :id="id" :class="'form-group text-area' + (icon ? ' has-icon' : '')">
        <label v-if="label">{{ label }}</label>
        <i :class="icon" v-if="icon" @click="$emit('iconClick')"></i>
        <textarea
            :name="name"
            :class="'form-control' + (error ? ' error' : '' )"
            :placeholder="placeholder"
            :minlength="min"
            :maxlength="max"
            :value="value"
            @input="$emit('update:value', $event.target.value)"
        ></textarea>
        <div class="error" v-if="error">{{ error }}</div>
    </div>
</template>

<script>
export default {
    name: "TextAreaComponent",
    props: ['id', 'label','icon','name','placeholder','value','min','max','error']
}
</script>
